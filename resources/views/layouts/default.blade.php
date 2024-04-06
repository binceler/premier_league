<!doctype html>
<html>
    <head>

    </head>
    <body class="">
        <div class="jumbotron text-center" style="max-width:100%;" >
            <div style="width: 100px; position: absolute; left: 25%; top:2%">
                <img vspace="20" width="100" class="jumbotronwidth" alt=" " src="{{asset('images/logo.png')}}">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

            </div>
            <div>
            <h1 style="color: #38003c;">Premier League Predictor</h1>
            </div>


        </div>
        <div id="main" class="row" style="margin:0px;">
            @yield('content')
        </div>

    </body>
</html>
