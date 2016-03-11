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

function login(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$username = $request->username;
	@$pass = $request->password;
	@$passw = md5($pass);
	$sql = "SELECT u.id, u.username, u.lastLogin, m.type FROM users u JOIN members m ON u.memberId = m.id WHERE username = '$username' AND u.password = '$passw'";
	$result = $this->mysqli->query($sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);			
		$datetime = date('Y-m-d H:i:s');
		$ses = $row["username"].$datetime;
		$sql = "UPDATE users SET lastLogin = '$datetime', sessionId='".md5($ses)."' WHERE id = " .$row["id"];			
		$result = $this->mysqli->query($sql);
		if ($result) {	
			$this->response($this->json(array('role' => $row['type'], 'session' => md5($ses), 'url' => 'index.html')), 200);
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
	$sql = "SELECT id FROM users WHERE username = '$username' AND sessionId='$session_id'";
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
	$sql = "SELECT u.id, m.firstName, m.lastName, u.lastLogin FROM users u JOIN members m ON u.memberId = m.id WHERE u.username = '$username' AND u.sessionId='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if (time() - strtotime($row['lastLogin']) < 1800) {
		$this->response($this->json(array('firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'url' => 'index.html', 'isLoggedIn' => true)), 200);
		} else {
			$this->response('', 401);
		}
	} else {
		$this->response('', 401);
	}
}

private function isLoggedAsAdmin($request) {
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT u.id, u.lastLogin
		FROM users u JOIN members m ON u.memberId = m.id 
		WHERE u.username = '$username' AND u.sessionId='$session_id' AND m.type='Z'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if (time() - strtotime($row['lastLogin']) < 1800) {
			return true;
		} else {
			$this->response('', 401);
			return false;
		}
	} else {
		$this->response('', 401);
		return false;
	}
}

private function isLogged($request) {
	@$session_id = $request->session_id;
	@$username = $request->username;
	$sql = "SELECT id, lastLogin FROM users WHERE username = '$username' AND sessionId='$session_id'";
	$result = $this->mysqli->query($sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if (time() - strtotime($row['lastLogin']) < 1800) {
			return true;
		} else {
			$this->response('', 401);
			return false;
		}
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
		@$old = $request->old;
		$sql = "SELECT m.id, m.firstName, m.lastName, m.accessionDate, m.phone, m.privateEmail, m.aegeeEmail, m.birthDate, m.cardNumber, m.declaration, m.connectedToList, m.type, (SELECT expirationDate
								FROM payments p
								WHERE p.memberId = m.id
								ORDER BY p.expirationDate DESC
								LIMIT 1
								) as 'expirationDate'
			FROM members m 
			WHERE m.old = $old AND m.id > 0
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
					'expirationDate' => $row["expirationDate"],
					'type' => $row["type"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function getMembersShortList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
		@$old = $request->old;
		$sql = "SELECT m.id, m.firstName, m.lastName
			FROM members m LEFT JOIN users u ON m.id = u.memberId
			WHERE m.old = 0 AND m.id > 0 and u.memberID IS NULL
			ORDER BY m.lastName ASC";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"],
					'lastName' => $row["lastName"],
					'firstName' => $row["firstName"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function setDeclaration() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$id = $request->member_id;
		@$declaration = $request->declaration;
		if (!isset($declaration)) {
			$declaration = 0;
		}
		if($id != null) {
			$sql = "UPDATE members SET declaration = $declaration WHERE id = $id";
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
}

function setAegeeEmail() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$id = $request->member_id;
		@$aegeeEmail = $request->aegeeEmail;
		if (!isset($aegeeEmail)) {
			$aegeeEmail = 0;
		}
		if($id != null) {
			$sql = "UPDATE members SET aegeeEmail = $aegeeEmail WHERE id = $id";
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
}

function setConnectedToList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$id = $request->member_id;
		@$connectedToList = $request->connectedToList;
		if (!isset($connectedToList)) {
			$connectedToList = 0;
		}
		if($id != null) {
			$sql = "UPDATE members SET connectedToList = $connectedToList WHERE id = $id";
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
	if ($this->isLoggedAsAdmin($request)) {
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
					$mentorId, '$type', 0)";
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

function changeMember() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$id = $request->id;
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
		if($id != null && 
			$firstName != null && strlen($firstName) < 255 && 
			$lastName != null && strlen($lastName) < 255 &&
			$accessionDate != null &&
			$phone != null && preg_match($phoneRegex, $phone) &&
			$privateEmail != null && strlen($privateEmail) < 255 && filter_var($privateEmail, FILTER_VALIDATE_EMAIL) &&
			$birthDate != null &&
			$cardNumber != null && strlen($cardNumber) < 20 && preg_match($cardNumberRegex, $cardNumber) &&
			$mentorId != null) {
			$sql = "UPDATE members SET 
						firstName = '$firstName',
						lastName = '$lastName',
						accessionDate = '".$accessionDate->format('Y-m-d')."',
						phone = '$phone',
						privateEmail = '$privateEmail',
						aegeeEmail = $aegeeEmail,
						birthDate = '".$birthDate->format('Y-m-d')."',
						cardNumber = '$cardNumber',
						declaration = $declaration,
						connectedToList = $connectedToList,
						mentorId = $mentorId,
						type = '$type'
					WHERE id = $id";
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

function getUsersList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
		@$old = $request->old;
		$sql = "SELECT u.id, m.firstName, m.lastName, m.privateEmail, u.username, u.memberId, u.lastLogin
			FROM members m JOIN users u ON m.id = u.memberId
			ORDER BY m.lastName ASC";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"],
					'lastName' => $row["lastName"],
					'firstName' => $row["firstName"], 
					'privateEmail' => $row["privateEmail"],
					'username' => $row["username"],
					'lastLogin' => $row["lastLogin"],
					'memberId' => $row["memberId"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function getUserProfile() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$session_id = $request->session_id;
		@$username = $request->username;
		$sql = "SELECT u.id, u.username, m.firstName, m.lastName, m.privateEmail 
				FROM users u JOIN members m ON u.memberId = m.id 
				WHERE username = '$username' AND sessionId = '$session_id'";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$this->response($this->json(array('id' => $row["id"], 'username' => $row["username"], 'firstName' => $row["firstName"], 'lastName' => $row["lastName"], 'privateEmail' => $row["privateEmail"])), 200);
		} else {
			$this->response('', 401);
		}
	}
}

function setUserProfile() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$session_id = $request->session_id;
		@$username = $request->username;
		$valid = true;
		@$currentPassword = $request->currentPassword;
		if (!isset($currentPassword) || strlen($currentPassword) < 5) {
			$this->response($this->json(array('code' => 'currentPassword')), 306);
			$valid = false;
		} else {
			$sql = "SELECT password FROM users WHERE username = '$username'";
			$result = $this->mysqli->query($sql);
			if (mysqli_num_rows($result) > 0) {
				$currentPassword = md5($currentPassword);
				$row = mysqli_fetch_assoc($result);
				if ($row['password'] != $currentPassword) {
					$this->response($this->json(array('code' => 'currentPassword')), 306);
					$valid = false;
				}
			}
		}
		
		@$password = $request->password;
		if (!isset($password) || strlen($password) < 5) {
			$this->response($this->json(array('code' => 'password')), 306);
			$valid = false;
		} else {
			$password = md5($password);
		}

		if ($valid) {
			$sql = "UPDATE users SET password='$password' WHERE username = '$username' AND sessionId = '$session_id' AND password = '$currentPassword'";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('', 200);
			} else {
				$this->response('', 306);
			}
		}
	}
}

function setNewUser() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		$valid = true;	

		@$username = $request->_username;
		if (!isset($username) || strlen($username) < 5) {
			$this->response($this->json(array('code' => 'username')), 306);
			$valid = false;
		}
		
		@$password = $request->password;
		if (!isset($password) || strlen($password) < 5) {
			$this->response($this->json(array('code' => 'password')), 306);
			$valid = false;
		} else {
			$password = md5($password);
		}

		@$memberId = $request->memberId;
		if (!isset($memberId)) {
			$this->response($this->json(array('code' => 'memberId')), 306);
			$valid = false;
		}

		if ($valid) {
			$sql = "INSERT INTO users (username, password, memberId) 
				VALUES ('$username', '$password', $memberId)";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('', 200);
			} else {
				$this->response('', 306);
			}
		}
	}
}

function setNewPassword() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		$valid = true;	

		@$memberId = $request->memberId;
		if (!isset($memberId)) {
			$this->response($this->json(array('code' => 'memberId')), 306);
			$valid = false;
		}
		
		@$password = $request->password;
		if (!isset($password) || strlen($password) < 5) {
			$this->response($this->json(array('code' => 'password')), 306);
			$valid = false;
		} else {
			$password = md5($password);
		}

		if ($valid) {
			$sql = "UPDATE users SET password = '$password' WHERE memberId = $memberId";
			$result = $this->mysqli->query($sql);
			if ($result) {
				$this->response('', 200);
			} else {
				$this->response('', 306);
			}
		}
	}
}

function getMemberDetails() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$member_id = $request->member_id;
		$sql = "SELECT m.id, m.firstName, m.lastName, m.accessionDate, m.phone, m.privateEmail, m.aegeeEmail, m.birthDate, m.cardNumber, m.declaration, m.connectedToList, m.mentorId, m.type, m.old, m2.firstName AS mentorFirstName, m2.lastName AS mentorLastName
				FROM members m LEFT JOIN members m2 ON m.mentorId = m2.id
				WHERE m.id = '$member_id'";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$this->response($this->json(array('id' => $row["id"], 'firstName' => $row["firstName"], 'lastName' => $row["lastName"], 'accessionDate' => $row["accessionDate"], 'phone' => $row["phone"], 'privateEmail' => $row["privateEmail"], 'aegeeEmail' => $row["aegeeEmail"], 'birthDate' => $row["birthDate"], 'cardNumber' => $row["cardNumber"], 'declaration' => $row["declaration"], 'connectedToList' => $row["connectedToList"], 'mentorId' => $row['mentorId'], 'type' => $row["type"], 'old' => $row["old"], 'mentorFirstName' => $row["mentorFirstName"], 'mentorLastName' => $row["mentorLastName"])), 200);
		} else {
			$this->response('', 401);
		}
	}
}

function getPaymentsForMember() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$memberId = $request->memberId;
		$toReturn = array();
		$sql = "SELECT id, amount, paymentDate, expirationDate, type
				FROM payments
				WHERE memberId = '$memberId'
				ORDER BY paymentDate DESC";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('id' => $row["id"], 'amount' => $row["amount"], 'paymentDate' => $row["paymentDate"], 'expirationDate' => $row["expirationDate"], 'type' => $row["type"]);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
		}
	}
}

function removeUser(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$id = $request->id;
		@$username = $request->_username;
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

function setNewPayment() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		$valid = true;	

		@$memberId = $request->memberId;
		if (!isset($memberId)) {
			$this->response($this->json(array('code' => 'memberId')), 306);
			$valid = false;
		}
		
		@$paymentDate = $request->paymentDate;
		if (!isset($paymentDate)) {
			$this->response($this->json(array('code' => 'paymentDate')), 306);
			$valid = false;
		} else {
			$paymentDate = new DateTime(substr($request->paymentDate, 0, 23), new DateTimeZone('Poland'));
		}

		@$type = $request->type;
		if (!isset($type)) {
			$this->response($this->json(array('code' => 'type')), 306);
			$valid = false;
		}

		@$expirationDate = $request->expirationDate;
		if (!isset($expirationDate)) {
			$this->response($this->json(array('code' => 'expirationDate')), 306);
			$valid = false;
		} else {
			$expirationDate = new DateTime(substr($request->expirationDate, 0, 23), new DateTimeZone('Poland'));
		}

		@$amount = $request->amount;
		if (!isset($amount)) {
			$this->response($this->json(array('code' => 'amount')), 306);
			$valid = false;
		} else {
			$amount = number_format($amount, 2);
		}

		@$session_id = $request->session_id;
		@$username = $request->username;
		$sql = "SELECT m.id FROM users u JOIN members m ON u.memberId = m.id 
			WHERE u.username = '$username' AND u.sessionId='$session_id'";
		$result = $this->mysqli->query($sql);
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			if ($valid) {
				$auditCD = date('Y-m-d H:i:s');
				$auditCU = $row['id'];
				$sql = "INSERT INTO payments (memberId, amount, paymentDate, expirationDate, type, auditCD, auditCU) 
				VALUES ($memberId, $amount, '" . $paymentDate->format('Y-m-d') . "', '" . $expirationDate->format('Y-m-d') . "', $type, '$auditCD', $auditCU)";
				$result = $this->mysqli->query($sql);
				if ($result) {
					$this->response('', 200);
				} else {
					$this->response($this->json(array('code' => $sql)), 306);
				}
			}
		} else {
			$this->response($this->json(array('code' => 'username')), 306);
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