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
$("body").on("click", "td", function () {
	var name = $(this).html();
	if (confirm('Challenge ' + name + ' to a game?')) {
    	return;
	} else {
	    return;
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
		},
		success: function(data)
		{
            console.log("Success");
			if( typeof( data.error ) === "undefined" )
			{
				crtajOnlineIgrace(data);
				ucitajOnlineIgrace(data.vrijemeZadnjegPristupa);
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
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
