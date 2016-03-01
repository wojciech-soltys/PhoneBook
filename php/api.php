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

function isUserLogged() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT u.id, m.firstName as 'firstName', m.lastName as 'lastName' FROM users u JOIN members m ON u.member_id = m.id WHERE u.username = '$username' AND u.session_id='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$this->response($this->json(array('firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'url' => 'index.html', 'isLoggedIn' => true)), 200);
	} else {
		$this->response('', 401);
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

function getMembersList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
		@$edition = $request->edition;
		$sql = "SELECT m.id, m.firstName, m.lastName, m.accessionDate, m.phone, m.privateEmail, m.aegeeEmail, m.birthDate, m.cardNumber, m.declaration, m.connectedToList, (SELECT expirationDate
								FROM payments p
								WHERE p.member_id = m.id
								ORDER BY p.expirationDate DESC
								LIMIT 1
								) as 'expirationDate'
			FROM members m 
			WHERE m.old = 0 AND m.id > 0
			ORDER BY m.lastName ASC";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"],
					'lastName' => $row["lastName"],
					'firstName' => $row["firstName"], 
					'phone' => $row["phone"], 
					'privateEmail' => $row["privateEmail"], 
					'birthDate' => $row["birthDate"], 
					'cardNumber' => $row["cardNumber"], 
					'declaration' => $row["declaration"], 
					'aegeeEmail' => $row["aegeeEmail"], 
					'connectedToList' => $row["connectedToList"],
					'expirationDate' => $row["expirationDate"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function getMentors() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
		$sql = "SELECT m.id, CONCAT(m.firstName, ' ', m.lastName) AS 'name'
			FROM members m 
			WHERE m.old = 0 AND m.mentorId = 0
			ORDER BY m.lastName ASC";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"],
					'name' => $row["name"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function saveMember() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$firstName = $request->firstName;
		@$lastName = $request->lastName;
		$accessionDate = new DateTime(substr($request->accessionDate, 0, 23), new DateTimeZone('Poland'));
		@$phone = $request->phone;
		$phoneRegex = "/[0-9]{9}/";
		@$privateEmail = $request->privateEmail;
		@$aegeeEmail = $request->aegeeEmail;
		if (!isset($aegeeEmail)) {
			$aegeeEmail = 0;
		}
		$birthDate = new DateTime(substr($request->birthDate, 0, 23), new DateTimeZone('Poland'));
		@$cardNumber = $request->cardNumber;
		$cardNumberRegex = "/[a-zA-Z0-9]{6}-[a-zA-Z0-9]{6}/";
		@$declaration = $request->declaration;
		if (!isset($declaration)) {
			$declaration = 0;
		}
		@$connectedToList = $request->connectedToList;
		if (!isset($connectedToList)) {
			$connectedToList = 0;
		}
		@$mentorId = $request->mentorId;
		@$type = $request->type;
		if (!isset($type)) {
			$type = 0;
		}
		if($firstName != null && strlen($firstName) < 255 && 
			$lastName != null && strlen($lastName) < 255 &&
			$accessionDate != null &&
			$phone != null && preg_match($phoneRegex, $phone) &&
			$privateEmail != null && strlen($privateEmail) < 255 && filter_var($privateEmail, FILTER_VALIDATE_EMAIL) &&
			$birthDate != null &&
			$cardNumber != null && strlen($cardNumber) < 20 && preg_match($cardNumberRegex, $cardNumber) &&
			$mentorId != null) {
			$sql = "INSERT INTO members (firstName, lastName, accessionDate,
				phone, privateEmail, aegeeEmail, birthDate, cardNumber, 
				declaration, connectedToList, mentorId, type, old) 
				VALUES ('$firstName', '$lastName', '"
					.$accessionDate->format('Y-m-d').
					"', '$phone', '$privateEmail', $aegeeEmail, '"
					.$birthDate->format('Y-m-d').
					"', '$cardNumber', $declaration, $connectedToList, 
					$mentorId, $type, 0)";
				$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('', 200);
			} else {
				$this->response($this->json(array('message'=>'Błąd zapisu danych')), 400);
			}
		} else {
			$this->response($this->json(array('message'=>'Nie wszystkie pola zostały wypełnione')), 400);
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