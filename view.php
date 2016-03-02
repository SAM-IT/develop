<?php

$links = [
    'MailCatcher' => "http://" . strtr($_SERVER['HTTP_HOST'], ['console' => 'mailcatcher']),
    'Logs' => "/pimp-my-log",
    'PHPMyAdmin' => "/phpmyadmin",
    'Beanstalk console' => "/beanstalk_console/public/?server=beanstalk://localhost:11300"
];
?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<style>
    div.tab-content {
        position:absolute;
        top: 50px;
        left: 0;
        right: 0;
        bottom: 0;
    }

    div.tab-content > div {
        position: absolute;
        left: 0px;
        right: 0px;
        top: 0px;
        bottom:0px;
    }
</style>
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid" id="tabs">
        <ul class="nav navbar-nav nav-tabs" role="tablist">
    <?php
    foreach ($links as $title => $url) {
        $id = md5($url);
        $active = reset($links) === $url ? 'active' : '';
        echo "<li class='$active' role='tab' data-toggle='tab'><a href='#$id'>$title</a></li>";
    }
    ?>
        </ul>
    </div>
    <script>
        $(document).ready(function() {
            $('#tabs a').on('click', function (e) {
                e.preventDefault()
                $(this).tab('show')
            });
        });
    </script>
</nav>
<div class="tab-content">
<?php

foreach ($links as $title => $url) {
    $id = md5($url);
    $active = reset($links) === $url ? 'active' : '';
    echo "<div role='tab-panel' class='tab-pane $active' id='$id'>";
    echo "<iframe width='100%' height='100%' src='$url'></iframe>";

    echo "</div>";
}
?>
</div>

<!--<div id="iframe">-->
<!--<iframe name="iframe" src="" width="100%" height="100%" frameborder="0"></iframe>-->
<!--</div>-->
<script>
//    $('body').on('click', 'a', function() {
//        $('li').removeClass('active');
//
//        $(this).parent().addClass('active');
//
//    });
</script>
</body>
</html>