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
	@$username = mysql_escape_string($request->username);
	@$pass = mysql_escape_string($request->password);
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
			if ($row['type'] == 'Z') {
				$role = 'ADM';
			} else {
				$role = null;
			}
			$this->response($this->json(array('role' => $role, 'session' => md5($ses), 'url' => 'index.html')), 200);
		} else {
			$this->response('', 400);
		}
	} else {
		$this->response($this->json(array('role' => $username, 'passw' => $passw, 'pass' => $pass)),400);
	}
}

function logout(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	@$username = mysql_escape_string($request->username);
	@$session_id = mysql_escape_string($request->session_id);
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
	@$session_id = mysql_escape_string($request->session_id);
	@$username = mysql_escape_string($request->username);
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
	@$session_id = mysql_escape_string($request->session_id);
	@$username = mysql_escape_string($request->username);
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
	@$session_id = mysql_escape_string($request->session_id);
	@$username = mysql_escape_string($request->username);
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
		@$old = mysql_escape_string($request->old);
		if (is_numeric($old)) {
			$sql = "SELECT m.id, m.firstName, m.lastName, m.accessionDate, m.phone, m.privateEmail, m.aegeeEmail, m.birthDate, m.cardNumber, m.declaration, m.connectedToList, m.type, 
					(SELECT expirationDate
					FROM payments p
					WHERE p.memberId = m.id
					ORDER BY p.expirationDate DESC
					LIMIT 1
					) AS 'expirationDate'
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
		} else {
			$this->response('', 306);
		}
	}
}

function getMembersShortList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
		@$old = mysql_escape_string($request->old);
		if (is_numeric($old)) {
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
		} else {
			$this->response('', 204);
		}
	}
}

function setDeclaration() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$memberId = mysql_escape_string($request->memberId);
		@$declaration = mysql_escape_string($request->declaration);
		if (!isset($declaration)) {
			$declaration = 0;
		}
		if($memberId != null && is_numeric($memberId) && is_numeric($declaration)) {
			$sql = "UPDATE members SET declaration = $declaration WHERE id = $memberId";
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
		@$memberId = mysql_escape_string($request->memberId);
		@$aegeeEmail = mysql_escape_string($request->aegeeEmail);
		if (!isset($aegeeEmail)) {
			$aegeeEmail = 0;
		}
		if($memberId != null && is_numeric($memberId) && is_numeric($aegeeEmail)) {
			$sql = "UPDATE members SET aegeeEmail = $aegeeEmail WHERE id = $memberId";
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
		@$memberId = mysql_escape_string($request->memberId);
		@$connectedToList = mysql_escape_string($request->connectedToList);
		if (!isset($connectedToList)) {
			$connectedToList = 0;
		}
		if($memberId != null && is_numeric($memberId) && is_numeric($connectedToList)) {
			$sql = "UPDATE members SET connectedToList = $connectedToList WHERE id = $memberId";
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
		$sql = "SELECT id, CONCAT(firstName, ' ', lastName) AS 'name'
		FROM members 
		WHERE old = 0 AND mentorId = 0
		ORDER BY lastName ASC";
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
		@$firstName = mysql_escape_string($request->firstName);
		@$lastName = mysql_escape_string($request->lastName);
		$accessionDate = new DateTime(substr($request->accessionDate, 0, 23), new DateTimeZone('Poland'));
		@$phone = mysql_escape_string($request->phone);
		$phoneRegex = "/[0-9]{9}/";
		@$privateEmail = mysql_escape_string($request->privateEmail);
		@$aegeeEmail = mysql_escape_string($request->aegeeEmail);
		if (!isset($aegeeEmail)) {
			$aegeeEmail = 0;
		}
		$birthDate = new DateTime(substr($request->birthDate, 0, 23), new DateTimeZone('Poland'));
		@$cardNumber = mysql_escape_string($request->cardNumber);
		$cardNumberRegex = "/[a-zA-Z0-9]{6}-[a-zA-Z0-9]{6}/";
		@$declaration = mysql_escape_string($request->declaration);
		if (!isset($declaration)) {
			$declaration = 0;
		}
		@$connectedToList = mysql_escape_string($request->connectedToList);
		if (!isset($connectedToList)) {
			$connectedToList = 0;
		}
		@$mentorId = mysql_escape_string($request->mentorId);
		@$type = mysql_escape_string($request->type);
		if (!isset($type)) {
			$type = 'C';
		}
		if($firstName != null && strlen($firstName) < 255 && 
			$lastName != null && strlen($lastName) < 255 &&
			$accessionDate != null &&
			$phone != null && preg_match($phoneRegex, $phone) && is_numeric($phone) &&
			$privateEmail != null && strlen($privateEmail) < 255 && filter_var($privateEmail, FILTER_VALIDATE_EMAIL) &&
			is_numeric($aegeeEmail) &&
			$birthDate != null &&
			$cardNumber != null && strlen($cardNumber) < 20 && preg_match($cardNumberRegex, $cardNumber) &&
			is_numeric($declaration) &&
			is_numeric($connectedToList) &&
			$mentorId != null && is_numeric($mentorId) &&
			$type != null) {
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
			$this->response($this->json(array('message'=>'Błąd zapisu danych')), 400);
		}
	} else {
		$this->response($this->json(array('message'=>'Nie wszystkie pola zostały wypełnione')), 400);
	}
}

function changeMember() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$id = mysql_escape_string($request->id);
		@$firstName = mysql_escape_string($request->firstName);
		@$lastName = mysql_escape_string($request->lastName);
		$accessionDate = new DateTime(substr($request->accessionDate, 0, 23), new DateTimeZone('Poland'));
		@$phone = mysql_escape_string($request->phone);
		$phoneRegex = "/[0-9]{9}/";
		@$privateEmail = mysql_escape_string($request->privateEmail);
		@$aegeeEmail = mysql_escape_string($request->aegeeEmail);
		if (!isset($aegeeEmail)) {
			$aegeeEmail = 0;
		}
		$birthDate = new DateTime(substr($request->birthDate, 0, 23), new DateTimeZone('Poland'));
		@$cardNumber = mysql_escape_string($request->cardNumber);
		$cardNumberRegex = "/[a-zA-Z0-9]{6}-[a-zA-Z0-9]{6}/";
		@$declaration = mysql_escape_string($request->declaration);
		if (!isset($declaration)) {
			$declaration = 0;
		}
		@$connectedToList = mysql_escape_string($request->connectedToList);
		if (!isset($connectedToList)) {
			$connectedToList = 0;
		}
		@$mentorId = mysql_escape_string($request->mentorId);
		@$type = mysql_escape_string($request->type);
		if (!isset($type)) {
			$type = 'C';
		}
		if($id != null && is_numeric($id) &&
			$firstName != null && strlen($firstName) < 255 && 
			$lastName != null && strlen($lastName) < 255 &&
			$accessionDate != null &&
			$phone != null && preg_match($phoneRegex, $phone) && is_numeric($phone) &&
			$privateEmail != null && strlen($privateEmail) < 255 && filter_var($privateEmail, FILTER_VALIDATE_EMAIL) &&
			is_numeric($aegeeEmail) &&
			$birthDate != null &&
			$cardNumber != null && strlen($cardNumber) < 20 && preg_match($cardNumberRegex, $cardNumber) &&
			is_numeric($declaration) &&
			is_numeric($connectedToList) &&
			$mentorId != null && is_numeric($mentorId) &&
			$type != null) {
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
			$this->response($this->json(array('message'=>'Błąd zapisu danych')), 400);
		}
	} else {
		$this->response($this->json(array('message'=>'Nie wszystkie pola zostały wypełnione')), 400);
	}
}

function getUsersList() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$toReturn = array();
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
		@$session_id = mysql_escape_string($request->session_id);
		@$username = mysql_escape_string($request->username);
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
		@$session_id = mysql_escape_string($request->session_id);
		@$username = mysql_escape_string($request->username);
		$valid = true;
		@$currentPassword = mysql_escape_string($request->currentPassword);
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
		
		@$password = mysql_escape_string($request->password);
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

		@$username = mysql_escape_string($request->_username);
		if (!isset($username) || strlen($username) < 5) {
			$this->response($this->json(array('code' => 'username')), 306);
			$valid = false;
		}
		
		@$password = mysql_escape_string($request->password);
		if (!isset($password) || strlen($password) < 5) {
			$this->response($this->json(array('code' => 'password')), 306);
			$valid = false;
		} else {
			$password = md5($password);
		}

		@$memberId = mysql_escape_string($request->memberId);
		if (!isset($memberId) || !is_numeric($memberId)) {
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

		@$memberId = mysql_escape_string($request->memberId);
		if (!isset($memberId) || !is_numeric($memberId)) {
			$this->response($this->json(array('code' => 'memberId')), 306);
			$valid = false;
		}
		
		@$password = mysql_escape_string($request->password);
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
		@$memberId = mysql_escape_string($request->memberId);
		if (is_numeric($memberId)) {
			$sql = "SELECT m.id, m.firstName, m.lastName, m.accessionDate, m.phone, m.privateEmail, m.aegeeEmail, m.birthDate, m.cardNumber, m.declaration, m.connectedToList, m.mentorId, m.type, m.old, m2.firstName AS mentorFirstName, m2.lastName AS mentorLastName
					FROM members m LEFT JOIN members m2 ON m.mentorId = m2.id
					WHERE m.id = '$memberId'";
			$result = $this->mysqli->query($sql);
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$this->response($this->json(array('id' => $row["id"], 'firstName' => $row["firstName"], 'lastName' => $row["lastName"], 'accessionDate' => $row["accessionDate"], 'phone' => $row["phone"], 'privateEmail' => $row["privateEmail"], 'aegeeEmail' => $row["aegeeEmail"], 'birthDate' => $row["birthDate"], 'cardNumber' => $row["cardNumber"], 'declaration' => $row["declaration"], 'connectedToList' => $row["connectedToList"], 'mentorId' => $row['mentorId'], 'type' => $row["type"], 'old' => $row["old"], 'mentorFirstName' => $row["mentorFirstName"], 'mentorLastName' => $row["mentorLastName"])), 200);
			} else {
				$this->response('', 401);
			}
		} else {
			$this->response('', 306);
		}
	}
}

function getPaymentsForMember() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$memberId = mysql_escape_string($request->memberId);
		if (is_numeric($memberId)) {
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
		} else {
			$this->response('', 306);
		}	
	}
}

function removeUser(){
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$id = mysql_escape_string($request->id);
		if (is_numeric($id)) {
			@$username = mysql_escape_string($request->_username);
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
		} else {
			$this->response('', 306);
		}	
	}
}

function setNewPayment() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		$valid = true;	

		@$memberId = mysql_escape_string($request->memberId);
		if (!isset($memberId) || !is_numeric($memberId)) {
			$this->response($this->json(array('code' => 'memberId')), 306);
			$valid = false;
		}
		
		@$paymentDate = mysql_escape_string($request->paymentDate);
		if (!isset($paymentDate)) {
			$this->response($this->json(array('code' => 'paymentDate')), 306);
			$valid = false;
		} else {
			$paymentDate = new DateTime(substr($request->paymentDate, 0, 23), new DateTimeZone('Poland'));
		}

		@$type = mysql_escape_string($request->type);
		if (!isset($type) || !is_numeric($type)) {
			$this->response($this->json(array('code' => 'type')), 306);
			$valid = false;
		}

		@$expirationDate = mysql_escape_string($request->expirationDate);
		if (!isset($expirationDate)) {
			$this->response($this->json(array('code' => 'expirationDate')), 306);
			$valid = false;
		} else {
			$expirationDate = new DateTime(substr($request->expirationDate, 0, 23), new DateTimeZone('Poland'));
		}

		@$amount = mysql_escape_string($request->amount);
		if (!isset($amount) || !is_numeric($amount)) {
			$this->response($this->json(array('code' => 'amount')), 306);
			$valid = false;
		} else {
			$amount = number_format($amount, 2);
		}

		@$sessionId = mysql_escape_string($request->session_id);
		@$username = mysql_escape_string($request->username);
		$sql = "SELECT m.id FROM users u JOIN members m ON u.memberId = m.id 
			WHERE u.username = '$username' AND u.sessionId='$sessionId'";
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

function getStatistics() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		$sql = "SELECT count(id) AS 'membersCount' FROM `members` WHERE id > 0 AND old = 0";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$membersCount = $row['membersCount'];
		}
		date_default_timezone_set('UTC');
		$currentDate = date("Y-m-d");

		$sql = "SELECT count(id) AS 'membersCountWithPayment' FROM `members` WHERE id > 0 AND old = '0' AND id IN (
				SELECT memberId
				FROM `payments`
				WHERE expirationDate >= STR_TO_DATE('$currentDate', '%Y-%m-%d'))";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$membersCountWithPayment = $row['membersCountWithPayment'];
		}

		$sql = "SELECT count(id) AS 'membersAegeeMail' FROM `members` WHERE id > 0 AND old = 0 AND aegeeEmail = 1";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$membersAegeeMail = $row['membersAegeeMail'];
		}

		$sql = "SELECT count(id) AS 'membersDeclaration' FROM `members` WHERE id > 0 AND old = 0 AND declaration = 1";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$membersDeclaration = $row['membersDeclaration'];
		}

		$sql = "SELECT count(id) AS 'membersConnectedToList' FROM `members` WHERE id > 0 AND old = 0 AND connectedToList = 1";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$membersConnectedToList = $row['membersConnectedToList'];
		}

		$sql = "SELECT count(id) AS 'mentorsCount' FROM `members` WHERE id > 0 AND old = '0' AND mentorId = 0";
		$result = $this->mysqli->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$mentorsCount = $row['mentorsCount'];
		}

		if ($membersCount != null && $membersCountWithPayment != null && $membersAegeeMail != null && $membersDeclaration!= null && $membersConnectedToList != null && $mentorsCount != null) {
			$this->response($this->json(array('membersCount' => $membersCount, 'membersCountWithPayment' => $membersCountWithPayment, 'membersAegeeMail' => $membersAegeeMail, 'membersDeclaration' => $membersDeclaration, 'membersConnectedToList' => $membersConnectedToList, 'mentorsCount' => $mentorsCount)), 200);
		} else {
			$this->response('', 401);
		}
	}
}

function moveToOld() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$memberId = mysql_escape_string($request->memberId);
		if($memberId != null && is_numeric($memberId)) {
			$sql = "UPDATE members SET old = 1 WHERE id = $memberId";
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

function moveToCurrent() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLoggedAsAdmin($request)) {
		@$memberId = mysql_escape_string($request->memberId);
		if($memberId != null && is_numeric($memberId)) {
			$sql = "UPDATE members SET old = 0 WHERE id = $memberId";
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

function getReportData() {
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if ($this->isLogged($request)) {
		@$onlyWithPaidContribution = mysql_escape_string($request->onlyWithPaidContribution);
		
		if (strcmp($onlyWithPaidContribution, "1") === 0) {
			date_default_timezone_set('UTC');
			$currentDate = date("Y-m-d");

			$sql = "SELECT firstName, lastName, accessionDate, phone, privateEmail, birthDate, cardNumber, declaration
					FROM members
					WHERE id > 0 AND old = 0 AND id IN (
					SELECT memberId
					FROM payments
					WHERE expirationDate >= STR_TO_DATE('$currentDate', '%Y-%m-%d'))";
			$result = $this->mysqli->query($sql);
		} else {
			$sql = "SELECT firstName, lastName, accessionDate, phone, privateEmail, birthDate, cardNumber, declaration
				FROM members
				WHERE id > 0 AND old = 0";
			$result = $this->mysqli->query($sql);
		}
		
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {					
				$toReturn[] = array('firstName' => $row["firstName"],
					'lastName' => $row["lastName"],
					'accessionDate' => $row["accessionDate"],
					'phone' => $row["phone"],
					'privateEmail' => $row["privateEmail"],
					'birthDate' => $row["birthDate"],
					'cardNumber' => $row["cardNumber"],
					'declaration' => $row["declaration"]
					);
			}
			$this->response($this->json($toReturn), 200);
		} else {
			$this->response('', 204);
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