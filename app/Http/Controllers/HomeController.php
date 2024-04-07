<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\MatchGame;
use App\Models\Team;
use App\Models\TeamStrength;
use App\Repositories\LeagueRepository;
use App\Repositories\PlayRepository;

class HomeController extends Controller
{
    public $league;
    public $teams;
    public $weeks;
    public $leagueRepository;
    public $playRepository;
    public $fixture;
    public $result = array();

    public function __construct(LeagueRepository $leagueRepository, PlayRepository $playRepository)
    {
        $this->leagueRepository = $leagueRepository;
        $this->playRepository = $playRepository;
        $this->leagueRepository->createLeague();
    }

    public function getLeague()
    {
        $this->weeks = $this->playRepository->getWeeks();
        $this->league = $this->leagueRepository->getAll();
        $this->playRepository->createFixture();
        $this->fixture = $this->playRepository->getFixture();
        $collection = collect($this->fixture);
        $grouped = $collection->groupBy('week_id');
        $currentWeek = MatchGame::where('played', 0)->first();

        $strength = $this->playRepository->getAllStrenght();

        $teams = Team::all();
        $championProbabilities = $this->calculateChampionProbabilities($teams);

        return view(
            'pages/home',
            ['league' => $this->league,
                'matches' => $grouped->toArray(),
                'fixture' => $grouped->toArray(),
                'weeks' => $this->weeks,
                'strength' => $strength,
                'currentWeek' => $currentWeek["week_id"] ?? 6,
                'types' => array('weak', 'average', 'strong'),
                'championProbabilities' => $championProbabilities,
                'teams' => $teams
            ]);
    }

    private function calculateChampionProbabilities($teams)
    {
        $teams = League::all();
        $matchGames = MatchGame::all();
        $teamStrengths = TeamStrength::all();

        $championProbabilities = [];
        $totalProbability = 0;
        foreach ($teams as $team) {
            $points = $team->points;
            $strength = $this->getStrengthValue($teamStrengths->where('team_id', $team->id)->first()->strength);

            $championProbability = $this->calculateWinProbability($points, $strength);
            $championProbabilities[$team->id] = $championProbability;
            $totalProbability += $championProbability;
        }

        $totalRoundedProbability = 0;
        foreach ($championProbabilities as $teamId => $probability) {
            $championProbabilities[$teamId] = ($totalProbability != 0) ? round(($probability / $totalProbability * 100)) : 0;
            $totalRoundedProbability += $championProbabilities[$teamId];
        }

        if ($totalRoundedProbability !== 100 && $totalRoundedProbability !== 0) {
            $maxProbabilityTeamId = array_keys($championProbabilities, max($championProbabilities))[0];
            $championProbabilities[$maxProbabilityTeamId] += 100 - $totalRoundedProbability;
        }

        return $championProbabilities;

    }

    private function getStrengthValue($strength)
    {
        switch ($strength) {
            case 'weak':
                return 1;
            case 'average':
                return 2;
            case 'strong':
                return 3;
            default:
                return 1; // Varsayılan olarak "weak" kabul et
        }
    }

    private function calculateWinProbability($points, $strength)
    {
        $multiplier = 0.1;

        $probability = (($points * $strength) * $multiplier) * 100;

        return $probability;
    }
    public function refreshFixture()
    {
        $this->weeks = $this->playRepository->getWeeks();
        $this->fixture = $this->playRepository->getFixture();
        $collection = collect($this->fixture);
        $grouped = $collection->groupBy('week_id');
        return response()->json(array('weeks' => $this->weeks, 'items' => $grouped->toArray()));
    }

    public function play()
    {

        $matches = $this->playRepository->getAllMatches();
        $this->playGame($matches);
    }

    public function refreshLeauge()
    {
        $this->league = $this->leagueRepository->getAll();
        return response()->json($this->league);
    }

    public function playWeekly($week)
    {
        $matches = $this->playRepository->getMatchesFromWeek($week);
        $this->playGame($matches);
        $result = $this->playRepository->getFixtureByWeekId($week);

        return $this->getLeague();
    }

    public function reset()
    {
        $this->playRepository->truncateMatches();
        $this->leagueRepository->truncateLeauge();
        $this->playRepository->createFixture();
    }

    private function playGame($matches)
    {
        foreach ($matches as $match) {
            $homeScore = $this->playRepository->createStrenght($match->home, 1);
            $awayScore = $this->playRepository->createStrenght($match->away, 0);
            $home = $this->leagueRepository->getLeaugeByTeamId($match->home);
            $away = $this->leagueRepository->getLeaugeByTeamId($match->away);
            $this->playRepository->calculateScore($homeScore, $awayScore, $home, $away);
            $match->home_goal = $homeScore;
            $match->away_goal = $awayScore;
            $match->played = 1;
            $match->save();
        }

    }
}
