<?php
include ('session.php'); // Includes Login Script

$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
$connection =  mysql_connect($databaseAddress, $databaseUser, $databasePassword);
if (!$connection) {
	echo '<script language="javascript">';
	echo 'alert("Błąd połączenia z baza danych");';
	echo '</script>';
	exit (0);
}
mysql_query("SET NAMES utf8");
if (!mysql_select_db($databaseName,$connection)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}
$ses_sql = mysql_query("SELECT member_id FROM Members, Login WHERE username='$user_check'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
$ses_sql = mysql_query("SELECT type from Members WHERE id='" . $ses_row["member_id"] . "'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<script src="jquery-2.1.3.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<link href="metro-bootstrap.css" rel="stylesheet" type="text/css">
<link href="metro-bootstrap-responsive.css" rel="stylesheet" type="text/css">
<link rel="Shortcut icon" href="http://aegee-gliwice.org/strona/wp-content/uploads/2013/12/favicon.png" />
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body class="metro">
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="javascript:window.location.href='members.php';"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p>
						Zalogowany: <?php echo $login_session;?>
					</p>          			
				</div>	
			</div>
		</div>
			<hr>
	<header class="bg-light">
	<div class="navigation-bar light">
		<div class="navigation-bar-content container">
			<ul class="element-menu">
				<li>
					<a href="javascript:window.location.href='members.php';">Lista członków</a>
				</li>
				<?php  if ($ses_row["type"] == 'Z') {?>
				<li>
					<a href="javascript:window.location.href='createMember.php';">Dodaj członka</a>
				</li>
				<?php }?>
				<li class="active">
					<a href="#">Byli członkowie</a>
				</li>
				<li>
					<a href="javascript:window.location.href='myData.php';">Moje dane</a>
				</li>
				<li>
					<a href="javascript:window.location.href='generateFile.php';">Raport</a>
				</li>
				<li>
					<a id="logout_button" href="#">Wyloguj</a>
				</li>
			</ul>
		</div>
	</div>
	<form id="logout" action="logout.php" method="post" enctype="multipart/form-data">
	</form>
	<script>
	$("#logout_button").click(function() {
		document.getElementById('logout').submit();
    });
	</script>
</header>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Lista byłych członków</h3>
		<table class="hovered">
			<col width="1%">
			<col width="18%">
			<col width="18%">
			<col width="12%">
			<col width="25%">
			<col width="16%">
			<col width="5%">
			<col width="5%">
			<tr>
				<th class=''>Lp.</th>
				<th class=''>Imię</th>
				<th class=''>Nazwisko</th>
				<th class=''>Numer telefonu</th>
				<th class=''>Prywatny adres e-mail</th>
				<th class=''>Numer karty członkowskiej</th>
				<th class=''>@aegee-gliwice.org</th>
				<th class=''>Podłączenie do listy</th>
			</tr>
			<?php 
			$index = 1;
			$indexFile = 1;
			$query = "SELECT * FROM `Members` WHERE id > 0 AND old = '1' ORDER BY lastName";
			$result = mysql_query($query);
			while ( $row = mysql_fetch_array($result) ) {
				echo "<tr onclick='details(".$row["id"].")'>";
				echo "<td>" . $index++ . "</td>";
				echo "<td>" . $row["firstName"] . "</td>";
				echo "<td>" . $row["lastName"] . "</td>";
				echo "<td><a class=\"mobilesOnly\" href=\"tel:" . $row["phone"] . "\">" . $row["phone"]  . "</a></td>";
				echo "<td><a href=\"mailto:" . $row["privateEmail"]  . "\">" . $row["privateEmail"]  . "</a></td>";
				echo "<td>" . $row["cardNumber"]  . "</td>";
				if ($row["connectedToList"] == 1) {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled/></td>";
				}
				if ($row["aegeeEmail"] == 1) {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/></td>";
				}
				echo "</tr>";
			}
			?>
		</table>
		<br>
		<script>
			function details(id) {
				$("#selectedId").val(id);
				$("#details").submit();
			}
		</script>
		<form id="details" method="post" action="selectedMember.php">
    		<input type="hidden" id="selectedId" name="selectedId" value="0">
		</form>
	</div>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>