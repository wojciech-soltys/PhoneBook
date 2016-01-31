<?php
include ('session.php');

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
if (isset ( $_POST ['submit'] )) {
	$fn = "membersList.txt";
	$file = fopen($fn, "w");
	$line = "";
	if (isset ($_POST ['lp'])) {
		$line .= "Lp;";
		$index = 1;
	}
	if (isset ($_POST ['imie'])) {
		$line .= "Imię;";
	}
	if (isset ($_POST ['nazwisko'])) {
		$line .= "Nazwisko;";
	}
	if (isset ($_POST ['dataWst'])) {
		$line .= "Data wstąpienia;";
	}
	if (isset ($_POST ['telefon'])) {
		$line .= "Numer telefonu;";
	}
	if (isset ($_POST ['email'])) {
		$line .= "Adres e-mail;";
	}
	if (isset ($_POST ['nrKarty'])) {
		$line .= "Numer karty";
	}
	fwrite($file, $line);
	$query = "SELECT * FROM `Members` WHERE id > 0 AND old = '0' ORDER BY lastName";
	$result = mysql_query($query);
	$fee = 0;
	while ( $row = mysql_fetch_array($result) ) {
		if (isset ($_POST ['oplacona'])) {
			$currentDate = date("Y-m-d");
			$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id 
					AND expiration_date >= STR_TO_DATE('". ($currentDate) ."' ,'%Y-%m-%d')";
			$fee = @mysql_num_rows(mysql_query($query_fee));
		}
		if (!(isset ($_POST ['oplacona'])) || (isset ($_POST ['oplacona']) && $fee > 0)) {
			$line = "\r\n";
			if (isset ($_POST ['lp'])) {
				$line .= $index++ . ";";
			}
			if (isset ($_POST ['imie'])) {
				$line .= $row["firstName"] . ";";
			}
			if (isset ($_POST ['nazwisko'])) {
				$line .= $row["lastName"] . ";";
			}
			if (isset ($_POST ['dataWst'])) {
				$line .= $row["accessionDate"] . ";";
			}
			if (isset ($_POST ['telefon'])) {
				$line .= $row["phone"] . ";";
			}
			if (isset ($_POST ['email'])) {
				$line .= $row["privateEmail"] . ";";
			}
			if (isset ($_POST ['nrKarty'])) {
				$line .= $row["cardNumber"];
			}
			fwrite($file, $line);
			if (isset ($_POST ['skladki'])) {
				$queryFee = "SELECT date, type, year, amount FROM `Payments` WHERE member_id=" . $row["id"] . " ORDER BY date DESC";
				$resultFee = mysql_query($queryFee);
				$indexFee = 1;
				$line = "\r\n-----";
				while ( $rowFee = mysql_fetch_array($resultFee) ) {
					$line .= $indexFee++ . ";";
					$line .= $rowFee["date"] . ";";
					if ($rowFee["type"] == 1) {
						$line .= "Semestr 1;";
					} else if ($rowFee["type"] == 2) {
						$line .= "Semestr 2;";
					} else if ($rowFee["type"] == 3) {
						$line .= "Rok;";
					}
					$line .= $rowFee["year"] . ";";
					$line .= number_format((float)$rowFee["amount"], 2, '.', '') . " zł;";
					fwrite($file, $line);
					$line = "\r\n-----";
				}
			}
		}
	}
	fclose($file);
	$fsize = filesize($fn);
	$file = fopen($fn, "r");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: filename=\"raport.txt\"");
	header("Content-length: $fsize");
	header("Cache-control: private"); //use this to open files directly
	while(!feof($file)) {
        $buffer = fread($file, 2048);
        echo $buffer;
    };
    fclose($file);
}
mysql_close($connection);
?>