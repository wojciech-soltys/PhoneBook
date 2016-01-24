<?php
include ('login.php'); // Includes Login Script
if (isset($_SESSION ['userID'])) {
	header ( 'Location: members.php' );
}
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<link rel="Shortcut icon" href="http://aegee-gliwice.org/strona/wp-content/uploads/2013/12/favicon.png" />
</head>
<body>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="#"><img src="logo.png"></a>	
			</div>
		</div>
	</div>
	<div class="wrapper" id="login_box">
	<form class="" action="" method="post" enctype="multipart/form-data">
		<p>
			<input id="username" name="username" value="Nazwa użytkownika" onfocusout="if(this.value == ''){this.value = 'Nazwa użytkownika';}" onfocus="if(this.value == 'Nazwa użytkownika'){this.value = '';}" autofocus type="text" autocomplete="off">
			<input id="password" name="password" value="Hasło" onfocusout="if(this.value == ''){this.value = 'Hasło';}" onfocus="if(this.value == 'Hasło'){this.value = '';}" type="password" autocomplete="off">
		</p>
		<p>
			<span><?php echo $error; ?></span>
		</p>
        <p>
           	<input name="submit" value="Zaloguj" class="blueButton" type="submit">
       	</p>
    </form>
	</div>
	</div>
</body>
</html>