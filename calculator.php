<!doctype html>
<html>
    <head>
        <title>PreTally Calculator</title>
        <link rel="stylesheet" type="text/css" media="screen" href="css/calculator.css"/>        		
    </head>
    <body>

        <div class="calculator" data-nightowl='{"drag":false, "subline":"A UroGulf Concern" }'></div>

        <script src="scripts/jquery.js"></script>
        <script type="text/javascript" src="scripts/calculator.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('div.calculator').NightOwl();
            });
        </script>
    </body>
</html>