<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\MatchGame;
use App\Models\TeamStrength;
use App\Models\Week;

class PlayRepository
{
    protected $team;
    protected $match;
    protected $week;
    protected $teamStrength;
    public $result;

    /**
     * PlayRepository constructor.
     * @param Team $team
     * @param MatchGame $match
     * @param Week $week
     * @param TeamStrength $teamStrength
     */
    public function __construct(Team $team, MatchGame $match, Week $week, TeamStrength $teamStrength)
    {
        $this->team = $team;
        $this->match = $match;
        $this->week = $week;
        $this->teamStrength = $teamStrength;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->team->pluck('id');
    }

    /**
     * @return mixed
     */
    public function getWeeksId()
    {
        return $this->week->pluck('id');
    }

    /**
     * @return mixed
     */
    public function getWeeks()
    {
        return $this->week->get();
    }


    /**
     *
     */
    public function createFixture()
    {
        foreach ($this->getWeeksId() as $week) {
            foreach ($this->iterateTeams($this->getTeamId()) as $value) {
                if (0 == $this->checkMatch($week, $value)) {
                    $this->match->create(['home' => $value[0], 'away' => $value[1], 'week_id' => $week]);
                }

            }

        }
    }

    /**
     * @param $teams
     * @return array
     */
    private function iterateTeams($teams)
    {
        $collection = collect($teams);
        $matrix = $collection->crossJoin($teams);
        $data = $matrix->reject(function($items){

            if($items[0] == $items[1]){
                return $items;
            }
        })->shuffle();
        return $data->all();
    }

    /**
     * @param $week_id
     * @param $teams
     * @return mixed
     */
    public function checkMatch($week_id, $teams)
    {
        return $this->match->where('week_id', '=', $week_id)
            ->whereRaw('(home IN(' . implode(',', $teams) . ') OR away IN(' . implode(',', $teams) . '))')
            ->count();
    }


    /**
     * @return mixed
     */
    public function getFixture()
    {

        return $this->match->select(
            'match_games.id',
            'match_games.played',
            'match_games.week_id',
            'match_games.home_goal',
            'match_games.away_goal',
            'week_id',
            'home.name as home_team',
            'home.logo as home_logo',
            'away.logo as away_logo',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=','match_games.week_id')
            ->join('teams as home', 'home.id', '=','match_games.home')
            ->join('teams as away', 'away.id', '=','match_games.away')
            ->orderBy('week_id','ASC')
            ->get();
    }

    /**
     * @param $week_id
     * @return mixed
     */
    public function getFixtureByWeekId($week_id)
    {

        return $this->match->select(
            'match_games.id',
            'match_games.played',
            'match_games.week_id',
            'match_games.home_goal',
            'match_games.away_goal',
            'week_id',
            'weeks.name',
            'home.logo as home_logo',
            'away.logo as away_logo',
            'home.name as home_team',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=','match_games.week_id')
            ->join('teams as home', 'home.id', '=','match_games.home')
            ->join('teams as away', 'away.id', '=','match_games.away')
            ->where('match_games.week_id','=',$week_id)
            ->orderBy('match_games.id','ASC')
            ->get();
    }

    /**
     * @param $team_id
     * @param $is_home
     * @return mixed
     */
    public function getTeamStrenght($team_id, $is_home)
    {
        if (!empty($this->teamStrength)) return $this->teamStrength->where([['team_id','=',$team_id],['is_home','=',$is_home]])->get();
    }

    /**
     * @param $team_id
     * @param $is_home
     * @return int
     */
    public function createStrenght($team_id, $is_home)
    {
        $this->result = rand(0,1);
        foreach ($this->getTeamStrenght($team_id,$is_home) as $value){
            switch ($value->strength){
                case 'strong':
                        $this->result = rand(4,5);
                    break;
                case 'average':
                        $this->result = rand(2,3);
                    break;
                case 'weak' :
                        $this->result = rand(0,2);
                    break;
                default :
                        $this->result = rand(0,1);
                    break;
            }
        }

        return $this->result;
    }

    /**
     * @param $week
     * @return mixed
     */
    public function getMatchesFromWeek($week)
    {
        return $this->match->where([['week_id','=',$week],['played','=',0]])->get();
    }

    /**
     * @param int $played
     * @return mixed
     */
    public function getAllMatches($played = 0)
    {
        return $this->match->where('played','=',$played)->get();
    }

    /**
     * @param $homeScore
     * @param $awayScore
     * @param $home
     * @param $away
     */
    public function calculateScore($homeScore, $awayScore, $home, $away)
    {
        if($homeScore > $awayScore){
            $home->won += 1;
            $home->points += 3;
            $home->goal_drawn += ($homeScore - $awayScore);
            $away->lose += 1;
            $away->goal_drawn  += ($awayScore - $homeScore);
        }elseif($awayScore > $homeScore){
            $away->won += 1;
            $away->points += 3;
            $away->goal_drawn += ($awayScore - $homeScore);
            $home->lose += 1;
            $home->goal_drawn += ($homeScore - $awayScore);
        }else{
            $home->draw += 1;
            $away->draw += 1;
            $home->points += 1;
            $away->points += 1;
        }

        $home->played  += 1;
        $away->played += 1;
        $home->save();
        $away->save();
    }



    /**
     *
     */
    public function truncateMatches()
    {
        $this->match->truncate();
    }

    public function getAllStrenght(){
        return $this->teamStrength->select('team_strengths.id','teams.name','teams.logo','team_strengths.is_home','team_strengths.strength')->join('teams','teams.id','=','team_strengths.team_id')->orderBy('teams.id')->get();
    }

}
