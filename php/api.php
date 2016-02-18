<?php
require_once("rest.inc.php");

include "db.php";

class API extends REST {

	public $data = "";	
	
	private $db = NULL;
	private $mysqli = NULL;
	public function __construct(){
		parent::__construct(); // Init parent contructor
		$this->dbConnect(); // Initiate Database connection
	}

/*
* Connect to Database
*/
private function dbConnect(){
	$this->mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB);
	$this->mysqli->set_charset("utf8");
}

/*
* Dynmically call the method based on the query string
*/
public function processApi(){
	$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
	if((int)method_exists($this,$func) > 0)
		$this->$func();
	else
		$this->response('',404); // If the method not exist with in this class "Page not found".
}

private function checkAndSetNewPath($folders) {
	if ($folders != null) {
		$destination = '../';
		foreach($folders as $x => $folder) {
	    	$destination .= $folder;
			if(!is_dir($destination)) {
				mkdir($destination, 0775);
			}
			$destination .= '/';
		}
		return true;
	}
	return false;
}


function login(){

	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$username = $request->username;
	@$pass = $request->password;
	@$passw = md5($pass);		
	$sql = "SELECT id, username, last_login FROM users WHERE username = '$username' AND password = '$passw'";
	$result = $this->mysqli->query($sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);			
		$datetime = date('Y-m-d H:i:s');
		$ses = $row["username"].$datetime;
		$sql = "UPDATE users SET last_login = '$datetime', session_id='".md5($ses)."' WHERE id = " .$row["id"];			
		$result = $this->mysqli->query($sql);
		if ($result) {	
			$this->response($this->json(array('session' => md5($ses), 'url' => 'index.html')), 200);
		} else {
			$this->response('', 400);
		}
	} else {
		$this->response('',400);
	}
}

function logout(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$username = $request->username;
	@$session_id = $request->session_id;
	$sql = "SELECT id FROM users WHERE username = '$username' AND session_id='$session_id'";
	$result = $this->mysqli->query($sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);			
		$sql = "UPDATE users SET session_id=-1 WHERE id = " .$row["id"];		
		$result = $this->mysqli->query($sql);
		if ($result) {	
			$this->response('', 200);
		} else {
			$this->response('', 400);
		}
	} else {
		$this->response('', 400);
	}

}

private function isLogged($request) {
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT id FROM users WHERE username = '$username' AND session_id='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		return true;
	} else {
		$this->response('', 401);
		return false;
	}
}

private function isLoggedAsAdmin($request) {
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT id FROM users WHERE username = '$username' AND session_id='$session_id' AND role='a'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		return true;
	} else {
		$this->response('', 401);
		return false;
	}
}

function isUserLogged() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT id FROM users WHERE username = '$username' AND session_id='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$this->response($this->json(array('url' => 'index.html', 'isLoggedIn' => true)), 200);
	} else {
		$this->response('', 401);
	}
}

function getUserDetails() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT id, username, first_name, last_name, email, last_login, role FROM users WHERE username = '$username' AND session_id='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$this->response($this->json(array('id' => $row["id"], 'username' => $row["username"], 'first_name' => $row["first_name"], 'last_name' => $row["last_name"], 'email' => $row["email"])), 200);
	} else {
		$this->response('', 401);
	}
}

function addNewUser() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$username = $request->n_username;
		@$first_name = $request->first_name;
		@$last_name = $request->last_name;
		@$email = $request->email;
		@$role = $request->role;
		@$password = md5($request->password);
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = $this->mysqli->query($sql);
		if(mysqli_num_rows($result) > 0) {
			$this->response('', 302);
		} else if($username != null &&
			$first_name != null &&
			$last_name!= null &&
			$email != null &&
			$role != null &&
			$password != null ) {

			$sql = "INSERT INTO users (
				username,
				first_name,
				last_name,
				email, 
				password, 
				role) 
values('$username',
	'$first_name',
	'$last_name',
	'$email',
	'$password',
	'$role')";

$result = $this->mysqli->query($sql);
if ($result) {
	$this->response('', 201);
} else {
	$this->response('', 409);
}
} else {
	$this->response('', 400);
}
}
}

function removeUser(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$id = $request->r_id;
		@$username = $request->r_username;
		$sql = "SELECT * FROM users WHERE username = '$username' AND id = '$id'";
		$result=$this->mysqli->query($sql);
		if(mysqli_num_rows($result) == 0) {
			$this->response('', 404);
		} else {
			$sql = "DELETE FROM users WHERE username = '$username' AND id = '$id'";
			$result=$this->mysqli->query($sql);
			if($result) {
				$this->response('',200);
			} else {
				$this->response('', 400);
			}
		}
	}
}

function changeUser() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->n_username;
	@$first_name= $request->first_name;
	@$role= $request->role;
	$valid = true;
	if (!isset($first_name) || strlen($first_name) < 2) {
		$this->response($this->json(array('message' => 'Bad first_name', 'code' => 'first_name')), 306);
		$valid = false;
	}
	@$last_name= $request->last_name;
	if (!isset($last_name) || strlen($last_name) < 2) {
		$this->response($this->json(array('message' => 'Bad last_name', 'code' => 'last_name')), 306);
		$valid = false;
	}
	@$email=$request->email;
	if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$this->response($this->json(array('message' => 'Bad email', 'code' => 'email')), 306);
		$valid = false;
	}

	if ($valid) {
		$sql = "SELECT id FROM users WHERE username = '$username'";
		$result = $this->mysqli->query($sql);
		if ($this->isLoggedAsAdmin($request)) {
			$sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', role='$role' WHERE username = '$username' ";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('',200);
			} else {
				$this->response($this->json(array('message' => 'Bad data')), 306);
			}
		}
	}
}

function changeUserAndPassword() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->n_username;
	@$first_name= $request->first_name;
	@$role= $request->role;
	$valid = true;
	$password = $request->password;
	if (!isset($password) || strlen($password) < 5) {
		$this->response($this->json(array('message' => 'Bad password', 'code' => 'oldpassword')), 306);
		$valid = false;
	}
	$password = md5($password);
	
	if (!isset($first_name) || strlen($first_name) < 2) {
		$this->response($this->json(array('message' => 'Bad first_name', 'code' => 'first_name')), 306);
		$valid = false;
	}
	@$last_name= $request->last_name;
	if (!isset($last_name) || strlen($last_name) < 2) {
		$this->response($this->json(array('message' => 'Bad last_name', 'code' => 'last_name')), 306);
		$valid = false;
	}
	@$email=$request->email;
	if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$this->response($this->json(array('message' => 'Bad email', 'code' => 'email')), 306);
		$valid = false;
	}

	if ($valid) {
		$sql = "SELECT id FROM users WHERE username = '$username'";
		$result = $this->mysqli->query($sql);
		if ($this->isLoggedAsAdmin($request)) {
			$sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', role='$role', password='$password' WHERE username = '$username' ";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('',200);
			} else {
				$this->response($this->json(array('message' => 'Bad data')), 306);
			}
		}
	}
}

function setUserDetails() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->username;
	@$first_name= $request->first_name;
	$valid = true;
	if (!isset($first_name) || strlen($first_name) < 2) {
		$this->response($this->json(array('message' => 'Bad first_name', 'code' => 'first_name')), 306);
		$valid = false;
	}
	@$last_name= $request->last_name;
	if (!isset($last_name) || strlen($last_name) < 2) {
		$this->response($this->json(array('message' => 'Bad last_name', 'code' => 'last_name')), 306);
		$valid = false;
	}
	@$email=$request->email;
	if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$this->response($this->json(array('message' => 'Bad email', 'code' => 'email')), 306);
		$valid = false;
	}

	if ($valid) {
		$sql = "SELECT id FROM users WHERE username = '$username' AND session_id='$session_id'";
		$result = $this->mysqli->query($sql);
		if ($this->isLogged($request)) {
			$sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username = '$username' AND session_id='$session_id'";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->getUserDetails();
			} else {
				$this->response($this->json(array('message' => 'Bad data')), 306);
			}
		}
	}
}

function getUsersList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);

	if ($this->isLogged($request)) {
		$toReturn = array();
		$sql = "SELECT * FROM users";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"], 'username' => $row["username"], 'first_name' => $row["first_name"], 'last_name' => $row["last_name"], 'email' => $row["email"], 'last_login' => $row["last_login"], 'role' => $row["role"]);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function setPassword() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->username;
	$valid = true;
	@$oldpassword = $request->old_password;
	if (!isset($oldpassword) || strlen($oldpassword) < 5) {
		$this->response($this->json(array('message' => 'Bad oldpassword', 'code' => 'oldpassword')), 306);
		$valid = false;
	}
	@$newpassword = $request->new_password;
	if (!isset($newpassword) || strlen($newpassword) < 5) {
		$this->response($this->json(array('message' => 'Bad newpassword', 'code' => 'newpassword')), 306);
		$valid = false;
	}
	$oldpassword = md5($oldpassword);
	$newpassword = md5($newpassword);

	if ($valid) {
		$sql = "SELECT id FROM users where username = '$username' AND session_id='$session_id' AND password='$oldpassword'";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$sql="UPDATE users SET password='$newpassword' WHERE username = '$username' AND session_id='$session_id' AND password='$oldpassword'";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('', 200);
			} else {
				$this->response($this->json(array('message' => 'Bad data')), 306);
			}
		} else {
			$this->response('', 401);
		}
	}
}

/*
* Encode array into JSON
*/
public function json($data){
	if(is_array($data)){
		return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}
}

// Initiiate Library

$api = new API;
$api->processApi();
?>