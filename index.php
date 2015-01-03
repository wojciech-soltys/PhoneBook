<?php
include ('login.php'); // Includes Login Script
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
<body home page page-id-131 page-template-default rf_wrapper>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="#"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p class="login_title">Portal członków</p>
					<form class="" action="" method="post" enctype="multipart/form-data">
						<p>
							<input id="username" name="username" value="Nazwa użytkownika" onfocus="if(this.value == 'Nazwa użytkownika'){this.value = '';}" 
							type="text" autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDiEFu6xIcAAAAXhJREFUOMvNk8FLVFEUxn/ffRdmIAla1CbBFDGCpoiQWYlBLty7UHAvEq2HYLhveDMws2/TIly6E9SdIEj+AVYgRaTgXhe2C968x2nhTOjow8pNZ/ede/ide893Lvx3UavVhkMIk30dQqiGECpF9e68CCG8LpfL3yStAAIk6Z2kT3Ect68C+AGdSroFVEII82aWSXoGYGYHVwE0qOM43pU0BXw3s1zSI2AnSZKXhYB6vT7inLvd7XZ/eu8fOOe2JEW9zjkwZ2bHkoayLDtpt9ufLzzBe/8GWC6VSpc7nIE2pLPLeu/fA0uDQ3T/6pp6039uZnfN7Ieke1EUrQOu3/VawPloNBrbwIyZ7TvnLvg/+mKOJ3xk88NR4R4sADM92fp9MDRMdXaRxenHVMbuFy8SMAFkZval2Wyu9ZN3Hk4zWx0nAtKsWwxotVrNNE2f5nn+CrB+/nRvlSR5y2EK0TWbSKfT+fo3Lribfr4bA/yfl56y2kkuZX8BjXVyqMs8oFcAAAAASUVORK5CYII=); 
							background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<input id="password" name="password" value="Hasło" onfocus="if(this.value == 'Hasło'){this.value = '';}" 
							type="password" autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDiEFu6xIcAAAAXhJREFUOMvNk8FLVFEUxn/ffRdmIAla1CbBFDGCpoiQWYlBLty7UHAvEq2HYLhveDMws2/TIly6E9SdIEj+AVYgRaTgXhe2C968x2nhTOjow8pNZ/ede/ide893Lvx3UavVhkMIk30dQqiGECpF9e68CCG8LpfL3yStAAIk6Z2kT3Ect68C+AGdSroFVEII82aWSXoGYGYHVwE0qOM43pU0BXw3s1zSI2AnSZKXhYB6vT7inLvd7XZ/eu8fOOe2JEW9zjkwZ2bHkoayLDtpt9ufLzzBe/8GWC6VSpc7nIE2pLPLeu/fA0uDQ3T/6pp6039uZnfN7Ieke1EUrQOu3/VawPloNBrbwIyZ7TvnLvg/+mKOJ3xk88NR4R4sADM92fp9MDRMdXaRxenHVMbuFy8SMAFkZval2Wyu9ZN3Hk4zWx0nAtKsWwxotVrNNE2f5nn+CrB+/nRvlSR5y2EK0TWbSKfT+fo3Lribfr4bA/yfl56y2kkuZX8BjXVyqMs8oFcAAAAASUVORK5CYII=); 
							background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
						</p>
						<p>
							<span><?php echo $error; ?></span>
						</p>
            			<p>
           					<input name="submit" value="Zaloguj" class="redButton" type="submit">
       					</p>
           			</form>
				</div>	
			</div>
		</div>
	</div>
	</div>
</body>
</html>