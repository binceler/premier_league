@extends('layouts.default')
@section('content')
<style>
    #home-goal,#away-goal,#t{
        width:40px;
    }
    #home-goal:after,#away-goal:after{
        font-family: FontAwesome;
        content: "\f040";
        padding: 5px;
    }
</style>
    <div style="margin: 10px auto">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td colspan="7">League Table</td>
                    </tr>
                    <tr>
                        <td>Teams</td>
                        <td>PTS</td>
                        <td>P</td>
                        <td>W</td>
                        <td>D</td>
                        <td>L</td>
                        <td>GD</td>
                    </tr>
                    </thead>
                    <tbody id="leauge-table-body">
                    @if (!empty($league))
                        @foreach ($league as $lg)

                            <tr>
                                <td><img width="50" height="50" src="{{ asset('images/'.$lg->logo) }}"/> {{$lg->name}}
                                </td>
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
            </div>
            <div class="col-md-6">
                <table class="table table-hover" id="weekly" data-week-id="2" real=1>
                    <thead>
                    <tr>
                        <td colspan="4">Matches</td>
                    </tr>
                    </thead>
                    <tbody id="weekly-matches">

                    @if (!empty($matches) && $currentWeek != 0)
                        @foreach ($matches[$currentWeek] as $results)
                            <tr>
                                <td colspan="3">{{ $currentWeek }} st Week Matches</td>
                            </tr>
                            <tr>
                                <td><img width="30" height="30" src="{{ asset('images/'.$results['home_logo']) }}"/> {{$results['home_team']}}</td>
                                <td>
                                    <div  style="float:left" id="home-goal" data-match-id="{{$results['id']}}">{{$results['home_goal']}}</div>
                                    <div style="float:left" id="t">-</div>
                                    <div style="float:left" id="away-goal" data-match-id="{{$results['id']}}">{{$results['away_goal']}} </div>
                                </td>
                                <td><img width="30" height="30" src="{{ asset('images/'.$results['away_logo']) }}"/> {{$results['away_team']}}</td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                    <tr>
                        <td>
                            <button class="btn btn-success pull-right" id="play-weekly" onclick="playWeekly({{ $currentWeek }})" >Play Weekly</button>
                        </td>
                    </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            <table class="table">
                <tr>
                    <td>
                        <button class="btn btn-success" id="play-all" onclick="playAll()">Play all</button>
                    </td>
                    <td>
                        <button class="btn btn-danger" id="reset" onclick="resetFixture()">Reset Fixture</button>
                    </td>

                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td colspan="3">Fixture</td>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    @if (!empty($weeks))
                        @foreach($weeks as $week)
                            <tr>
                                <td colspan="3">{{$week->name}} Matches</td>
                            </tr>
                            @if (!empty($fixture) && isset($fixture[$week->id]))
                                @foreach ($fixture[$week->id] as $results)
                                    <tr>
                                        <td><img width="30" height="30" src="{{ asset('images/'.$results['home_logo']) }}"/> {{$results['home_team']}}</td>
                                        <td>{{$results['home_goal']}} - {{$results['away_goal']}}</td>
                                        <td><img width="30" height="30" src="{{ asset('images/'.$results['away_logo']) }}"/> {{$results['away_team']}}</td>
                                    </tr>

                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <div id="chart">
                </div>
            </div>
        </div>
    </div>
@endsection
<script type="text/javascript">
    // Haftalık maçları oynatma fonksiyonu
    function playWeekly(week) {

        if (week == 7) {
            alert('oynanacak başka maç kalmadı');
        }

        $.ajax({
            url: "/play-weekly/" + week,
            type: 'GET',
            success: function(response) {
                // AJAX isteği başarılı olduğunda yapılacak işlemler burada
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Tüm maçları oynatma fonksiyonu
    function playAll() {
        $.ajax({
            url: '/play-all',
            type: 'GET',
            success: function(response) {
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                // AJAX isteği başarısız olduğunda yapılacak işlemler burada
                console.error(xhr.responseText);
            }
        });
    }

    // Fixture'ı sıfırlama fonksiyonu
    function resetFixture() {
        $.ajax({
            url: '/reset',
            type: 'GET',
            success: function(response) {
                // AJAX isteği başarılı olduğunda yapılacak işlemler burada
                console.log(response);
                resetTable();
            },
            error: function(xhr, status, error) {
                // AJAX isteği başarısız olduğunda yapılacak işlemler burada
                console.error(xhr.responseText);
            }
        });
    }

    function resetTable() {
        // Tabloyu içeren div elementini seç
        var tableDiv = $('#table-body');
        // Tablo içeriğini boşalt
        tableDiv.empty();
        // Yeniden veri almak için sayfayı yenileme
        location.reload();
    }

    function changeStrength(id){
        console.log($(this).find('option'));
    }
</script>
