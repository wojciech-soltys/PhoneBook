<?php
include ('session.php'); // Includes Login Script

$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
if ( !mysql_connect($databaseAddress, $databaseUser, $databasePassword) ) {
	echo '<script language="javascript">';
	echo 'alert("Błąd połączenia z baza danych");';
	echo '</script>';
	exit (0);
}
mysql_query("SET NAMES utf8");
if (!mysql_select_db($databaseName)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}
$query = "SELECT * FROM `Members`";
$result = mysql_query($query);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<script src="jquery-2.1.3.js"></script>
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body home page page-id-131 page-template-default rf_wrapper>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="/"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p class="login_title">Portal członków</p>
					<form class="" action="" method="post" enctype="multipart/form-data">
						<p>
							Zalogowany: <?php echo $login_session; ?>
						</p>
            			<p>
           					<a class="redButton" href="logout.php">Wyloguj</a>
       					</p>
           			</form>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Lista członków</h3>
		<table class="hovered">
			<col width="1%">
			<col width="11%">
			<col width="11%">
			<col width="8%">
			<col width="15%">
			<col width="7%">
			<col width="13%">
			<col width="5%">
			<col width="5%">
			<col width="22%">
			<tr>
				<th class='center'>Lp.</th>
				<th class='center'>Imię</th>
				<th class='center'>Nazwisko</th>
				<th class='center'>Numer telefonu</th>
				<th class='center'>Prywatny adres email</th>
				<th class='center'>@aegee-gliwice.org</th>
				<th class='center'>Numer karty członkowskiej</th>
				<th class='center'>Deklaracja</th>
				<th class='center'>Podłączenie do listy</th>
				<th class='center'>Mentor</th>
			</tr>
			<?php 
			$index = 1;
			while ( $row = mysql_fetch_array($result) ) {
				echo "<tr onclick='details(".$row["id"].")'>";
				echo "<td>" . $index++ . "</td>";
				echo "<td>" . $row["firstName"] . "</td>";
				echo "<td>" . $row["lastName"] . "</td>";
				echo "<td>" . $row["phone"]  . "</td>";
				echo "<td>" . $row["privateEmail"]  . "</td>";
				if ($row["aegeeEmail"] == 1) {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/></td>";
				}
				echo "<td>" . $row["cardNumber"]  . "</td>";
				if ($row["declaration"] == 1) {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled/></td>";
				}
				if ($row["connectedToList"] == 1) {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled/></td>";
				}
				if ($row["mentorID"] != 0 && $row["mentorID"] != -1) {
					$query = "SELECT firstName, lastName FROM `Members` WHERE id = ".$row["mentorID"];
					$mentorResult = mysql_query($query);
					$mentor = mysql_fetch_array($mentorResult);
					echo "<td>" . $mentor["firstName"] . " " . $mentor["lastName"] . "</td>";
				} else if ($row["mentorID"] == 0){
					echo "<td>" . "<b>Mentor</b>" . "</td>";
				} else if ($row["mentorID"] == -1){
					echo "<td>" . "-" . "</td>";
				}
				echo "</tr>";
			}
			?>
		</table>
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
</body>
</html>