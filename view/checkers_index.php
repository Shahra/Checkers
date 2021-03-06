<?php
    if (!LoginService::loggedIn()) {
        header('Location: ' . __SITE_URL . '/index.php?rt=login/index');
        exit();
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkers</title>
    <link rel="stylesheet" type="text/css" href="<?php echo __SITE_URL;?>/css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
</head>
<body>
	<h1><?php echo $title; ?></h1>

    <div id="div-online"></div>
    <span class="upperRightCorner"><?php echo $_SESSION['username'] ?></span>
    <p id="logout">
        <a href="<?php echo __SITE_URL; ?>/index.php?rt=login/logout">Logout</a>
    </p>

    <script>
        $(document).ready(function() {
            //We want to refresh on 0 seconds and then on every 2 seconds.
            //That's why we have setInterval(refresh) right after refresh.
            refresh(); setInterval(refresh, 2000);
            $("body").on("click", "#div-online td", function () {
                var name = $(this).children(0).html();
                if(confirm('Challenge ' + name + ' to a game?')) {
                    challengePlayer(name);
                }
            });
        });

        function refresh() {
            insideGame();
            checkIfChallenged();
            getOnlinePlayers();
        }

        function getOnlinePlayers() {
            $.ajax({
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/getOnlinePlayers",
                dataType: "json",
                data: {},
                success: function(data) {
                    updateOnlinePlayers(data);
                },
                error: function(status) {
                    console.log("Greska dohvat podataka: " + status.responseText);
                }
            });
        }

        function updateOnlinePlayers(onlinePlayers) {
            var tbl = $('<table id="online"></table>');
            var th = $('<th>Online:</th>');
            tbl.append(th);
            for(var i = 0; i < onlinePlayers.length; ++i) {
                var tr = $("<tr></tr>");
                var td = $("<td></td>");
                var onlinePlayer = $("<button>" + onlinePlayers[i] +"</button>");
                td.append(onlinePlayer);
                tr.append(td);
                tbl.append(tr);
            }
            $("#div-online").html(tbl);
        }

        function challengePlayer(name) {
            $.ajax({
                type: "post",
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/challengePlayer",
                dataType: "json",
                data: {
                    challengedPlayer: name
                },
                success: function(data) {
                    console.log("LOG:: player challenged");
                },
                error: function(status) {
                    console.log("Ajax error: challengePlayer" + JSON.stringify(status));
                }
            });
        }

        function checkIfChallenged() {
            $.ajax({
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/checkIfChallenged",
                dataType: "json",
                data: {},
                success: function(name) {
                    if(name !== false) {
                        if(confirm('Do you want to play against ' + name + '? ')) {
                            acceptChallenge(name);
                        }
                        else {
                            declineChallenge(name);
                        }
                    }
                },
                error: function(status) {
                    console.log("Ajax error: checkIfChallenged" + JSON.stringify(status));
                }
            });
        }

        function acceptChallenge(name) {
            $.ajax({
                type: "post",
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/acceptChallenge",
                dataType: "json",
                data: {
                    name: name
                },
                success: function(data) {
                    console.log("LOG::accepted challenge");
                },
                error: function(status) {
                    console.log("Ajax error: acceptChallenge" + JSON.stringify(status));
                }
            });
        }

        function declineChallenge(name) {
            $.ajax({
                type: "post",
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/declineChallenge",
                dataType: "json",
                data: {
                    name: name
                },
                success: function(data) {
                    console.log("LOG::decline successful");
                },
                error: function(status) {
                    console.log("Ajax error: declineChallenge" + JSON.stringify(status));
                }
            });
        }

        function insideGame() {
            $.ajax({
                url: "<?php echo __SITE_URL; ?>/index.php?rt=checkers/insideGame",
                dataType: "json",
                data: {},
                success: function(data) {
                    console.log("LOG::insideGame::" + data);
                    if(data === true) {
                        window.location.replace("<?php echo __SITE_URL; ?>/index.php?rt=checkers/game");
                    }
                },
                error: function(status) {
                    console.log("Ajax error: insideGame" + JSON.stringify(status));
                }
            });
        }

    </script>
<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
