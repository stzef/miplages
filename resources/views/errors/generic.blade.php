<!DOCTYPE html>
<html>
    <head>
        <title>{{ $title }} Algo Hay Ocurrido</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('layout/css/bootstrap.min.css') }}" >

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 30px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Opps Hay Ocurrido Algo</div>
                <div>
                    <table class="table">
                        <tr>
                            <td>Mensaje</td>
                            <td><p>{{ $message }}</p></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
