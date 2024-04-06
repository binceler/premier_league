<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
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

    /**
     * HomeController constructor.
     * @param LeagueRepository $leagueRepository
     * @param PlayRepository $playRepository
     */
    public function __construct(LeagueRepository $leagueRepository, PlayRepository $playRepository)
    {
        $this->leagueRepository = $leagueRepository;
        $this->playRepository = $playRepository;
        $this->leagueRepository->createLeague();
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

        return view(
            'pages/home',
            ['league' => $this->league,
                'matches' => $grouped->toArray(),
                'fixture' => $grouped->toArray(),
                'weeks' => $this->weeks,
                'strength' => $strength,
                'currentWeek' => $currentWeek["week_id"] ?? 0,
                'types' => array('weak', 'average', 'strong')
            ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshFixture()
    {
        $this->weeks = $this->playRepository->getWeeks();
        $this->fixture = $this->playRepository->getFixture();
        $collection = collect($this->fixture);
        $grouped = $collection->groupBy('week_id');
        return response()->json(array('weeks' => $this->weeks, 'items' => $grouped->toArray()));
    }

    /**
     *
     */
    public function play()
    {
        $matches = $this->playRepository->getAllMatches();
        $this->playGame($matches);
    }


    /**
     * @param $week
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param $matches
     */
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
