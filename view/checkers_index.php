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

    <br><br>
            <!-- TODO ovaj link cemo kasnije morati maknuti, trenutno je samo za testiranje ovdje -->
            <li><a href="<?php echo __SITE_URL; ?>/index.php?rt=checkers/game">Game view</a></li>

<div id="div-online"></div>

<br><br>
<p id="logout"><a href="<?php echo __SITE_URL; ?>/index.php?rt=login/logout">Logout</a></p>

<script>
var izazov = setInterval(jesamLiIzazvan, 4000);
$("body").on("click", "td", function () {
	var name = $(this).children(0).html();
	if (confirm('Challenge ' + name + ' to a game?')) {
    	pozoviIgraca(name);
	} else {
	    odbijPoziv(name);
	}
});
$( document ).ready( function() {
	ucitajOnlineIgrace(0);
});
ucitajOnlineIgrace = function(vrijemeZadnjegPristupa)
{
    var url = "Temporary/ucitajOnlineIgrace.php";
	$.ajax(
	{
		url: url,
		dataType: "json",
		data:
		{
			vrijemeZadnjegPristupa: vrijemeZadnjegPristupa,
            username:  <?php echo '"'.$_SESSION['username'].'"'; ?>
		},
		success: function(data)
		{
            console.log("Success");
			if( typeof( data.error ) === "undefined" )
			{
				crtajOnlineIgrace(data);
				ucitajOnlineIgrace(data.vrijemeZadnjegPristupa);
			} else {
                console.log("Undefined");
            }
		},
		error: function( xhr, status )
		{
			if( status === "timeout" )
				ucitajOnlineIgrace(vrijemeZadnjegPristupa);
		}
	} );
}

crtajOnlineIgrace = function(data)
{
    console.log(<?php echo '"'.$_SESSION['username'].'"';?>);
	var tbl = $( "<table></table>" ).attr("id", "online");
    var th = $( "<th></th>").html("Online:");
    tbl.append(th);
	for( var i = 0; i < data.usernames.length; ++i )
	{
		var tr = $( "<tr></tr>" );
		var td = $( "<td></td>" );
        var but_user = $("<button></button>").html( data.usernames[i].username);
        td.append(but_user);
		tr.append(td);
		tbl.append(tr);
	}
	$("#div-online").html(tbl);
}

function jesamLiIzazvan() {
        $.ajax({
            type: "post",
            url: "../Checkers/index.php?rt=invite/obradiPoziv",
            dataType: "json",
            data: {username: <?php echo '"'.$_SESSION['username'].'"'; ?>},
            success: function (data) {
                console.log("Success jesamLiIzazvan");
                if (data.poziv !== 'nema_poziva') {
                    if(confirm("Želite li igrati s " + data.username_bijelog + "?")) {
                        prihvatiPoziv(data.username_bijelog);
                    } else {
                        odbijPoziv(data.username_bijelog);
                    }
                }
                else {console.log("Nema poziva!");}

            },
            error: function (status) {
                console.log("ajax error: jesamLiIzazvan" + JSON.stringify(status) );
            }
        });
    }

function prihvatiPoziv(username_bijelog) {
    $.ajax({
        type: "post",
        url: "../Checkers/index.php?rt=invite/obradiPoziv",
        dataType: "json",
        data: {prihvati_poziv_usera: username_bijelog, moj_username:<?php echo '"'.$_SESSION['username'].'"'; ?>, },
        success: function (data) {
            console.log("Success prihvatiPoziv");
        },
        error: function (status) {
            console.log("ajax error: prihvatiPoziv" + JSON.stringify(status));
        }
    });
}

function odbijPoziv(username_bijelog) {
    $.ajax({
        type: "post",
        url: "../Checkers/index.php?rt=invite/obradiPoziv",
        dataType: "json",
        data: {odbijen_user: username_bijelog, moj_username:<?php echo '"'.$_SESSION['username'].'"'; ?>, },
        success: function (data) {
            console.log("Success odbijPoziv");
        },
        error: function (status) {
            console.log("ajax error: odbijPoziv" + JSON.stringify(status));
        }
    });
}

function pozoviIgraca(playerNick) {
        $.ajax({
            type: "post",
            url: "../Checkers/index.php?rt=invite/pozoviNaIgru",
            dataType: "json",
            data: {username_igraca_kojeg_zovemo: playerNick, nas_username: <?php echo '"'.$_SESSION['username'].'"'; ?>},
            success: function (data) {
                console.log("Success pozoviIgraca");
                $("#gameview").click();
            },
            error: function (status) {
                console.log("ajax error: pozoviIgraca" + JSON.stringify(status));
            }
        });
    }
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
