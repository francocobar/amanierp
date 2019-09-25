<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        @yield('blade-style')
        <style>
            body {
                padding-top: 5rem;
            }
            .cashier-template {
                /* padding: 3rem 1.5rem; */
                text-align: center;
            }

            .bg-amanie-dark {
                background-color: #a33b7d;
            }

            .btn-amanie-opt.btn-primary {
                background-color: #966b3a;
                border-color: #966b3a;
            }

            .btn-amanie-opt.btn-primary:not(:disabled):not(.disabled):active, .btn-amanie-opt.btn-primary:hover {
                background-color: #edb874;
                border-color: #edb874;
            }
            .btn-amanie-opt.btn-primary.focus, .btn-amanie-opt.btn-primary:focus {
                box-shadow: 0 0 0 0.2rem #d39e0029;
            }
            .btn-amanie-opt.btn-primary:not(:disabled):not(.disabled):active:focus {
                box-shadow: 0 0 0 0.2rem #d39e0029;
            }
            .ui-menu .ui-menu-item a {
                font-size: 12px;
            }
            .ui-autocomplete {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1510 !important;
                float: left;
                display: none;
                min-width: 160px;
                /* width: 160px; */
                padding: 4px;
                margin: 2px 0 0 0;
                list-style: none;
                background-color: #ffffff;
                border-color: #ccc;
                border-color: rgba(0, 0, 0, 0.2);
                border-style: solid;
                border-width: 1px;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
                border-radius: 2px;
                -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                -webkit-background-clip: padding-box;
                -moz-background-clip: padding;
                background-clip: padding-box;
                *border-right-width: 2px;
                *border-bottom-width: 2px;
            }
            .ui-menu-item > a.ui-corner-all {
                display: block;
                padding: 3px 15px;
                clear: both;
                font-weight: normal;
                line-height: 18px;
                color: #555555;
                white-space: nowrap;
                text-decoration: none;
            }
            .ui-state-hover, .ui-state-active {
                color: #ffffff;
                text-decoration: none;
                background-color: #966b3a;
                border-radius: 0px;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                background-image: none;
            }

            .ui-helper-hidden-accessible {
                display: none;
            }

            .blink-me {
                animation: blinker 1s linear infinite;
            }

            @keyframes blinker {
                50% { opacity: 0; }
            }
        </style>
        <title>{{isset($title) ? $title.' - ' : ''}}Amanie</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-amanie-dark fixed-top">
            <a class="navbar-brand" href="#">Amanie</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php /*
            <div class="collapse navbar-collapse" id="navbars">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#">Disabled</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
            </div>
            */?>
        </nav>

        <main role="main" class="container">
            <div class="cashier-template">
                <h1>{!!isset($headline) ? $headline : 'POS Module'!!}</h1>
                @yield('content')
                <?php /*
                <p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>
                */ ?>
            </div>

        </main>

        @yield('blade-hidden')
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="/js/jquery.js" type="text/javascript"></script>
        <script src="/js/jquery-ui.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

        <script src="{{ URL::asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.maskMoney.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/general.js') }}" type="text/javascript"></script>

        @yield('blade-script')
    </body>
</html>
