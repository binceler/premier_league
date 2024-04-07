@extends('layouts.default')
@section('content')
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <div style="margin: 10px auto">
        <table class="table table-hover">
            <tr>
                <th>League Table</th>
                <th>Macth Results</th>
                <th>Champion Probabilities</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table>
                        <thead>
                        <tr>
                            <th>Teams</th>
                            <th>PTS</th>
                            <th>P</th>
                            <th>W</th>
                            <th>D</th>
                            <th>L</th>
                            <th>GD</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (!empty($league))
                            @foreach ($league as $lg)
                                <tr>
                                    <td> {{$lg->name}}</td>
                                    <td>@if(isset($lg->points)) {{$lg->points}} @else 0 @endif</td>
                                    <td>@if(isset($lg->played)) {{$lg->played}} @else 0 @endif</td>
                                    <td>@if(isset($lg->won)) {{$lg->won}} @else 0 @endif</td>
                                    <td>@if(isset($lg->draw)) {{$lg->draw}} @else 0 @endif</td>
                                    <td>@if(isset($lg->lose)) {{$lg->lose}} @else 0 @endif</td>
                                    <td>@if(isset($lg->goal_drawn)) {{$lg->goal_drawn}} @else 0 @endif</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table>
                        <thead>
                        <tr>
                            <th colspan="3">{{ $currentWeek }} st Week Matches</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (!empty($matches))
                            @foreach ($matches[$currentWeek] as $results)
                                <tr>
                                    <td>{{$results['home_team']}}</td>
                                    <td>
                                        <div style="float:left" id="home-goal" data-match-id="{{$results['id']}}">{{$results['home_goal']}}</div>
                                        <div style="float:left" id="t">-</div>
                                        <div style="float:left" id="away-goal" data-match-id="{{$results['id']}}">{{$results['away_goal']}} </div>
                                    </td>
                                    <td>{{$results['away_team']}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table border='0'>
                        <thead>
                        <tr>
                            <th>{{ $currentWeek }}st Week Predictions of Championship</th>
                        </tr>
                        <tr>
                            <th>Team</th>
                            <th>Win Probability (%)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($championProbabilities as $teamId => $probability)
                            <tr>
                                <td>{{ $teams->find($teamId)->name }}</td>
                                <td>{{ $probability }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <table style="width:100%">
                        <tr>
                            <td style="text-align:center;">
                                <button class="btn btn-success pull-right" id="play-weekly" onclick="playWeekly({{ $currentWeek }})">Play Weekly</button>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn btn-success" id="play-all" onclick="playAll()">Play all</button>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn btn-danger" id="reset" onclick="resetFixture()">Reset Fixture</button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection
<script type="text/javascript">
    function playWeekly(week) {

        if (week == 7) {
            alert('oynanacak başka maç kalmadı');
        }

        $.ajax({
            url: "/play-weekly/" + week,
            type: 'GET',
            success: function(response) {
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function playAll() {
        $.ajax({
            url: '/play-all',
            type: 'GET',
            success: function(response) {
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function resetFixture() {
        $.ajax({
            url: '/reset',
            type: 'GET',
            success: function(response) {
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function resetTable() {

        var tableDiv = $('#table-body');
        tableDiv.empty();
        location.reload();
    }

    function changeStrength(id){
        console.log($(this).find('option'));
    }
</script>
