<?php 
	session_start();

	include_once 'dbconnect.php';

	if(isset($_REQUEST["login"])){
		login();
	}
	else if(isset($_REQUEST["register"])){
		register();
	}
	else if(isset($_REQUEST["add_event"])){
		addEvent();
	}
	else if(isset($_REQUEST["getevents"])){
		getEvents();
	}
	else if(isset($_REQUEST["delete_id"])){
		deleteEvent();
	}
	else if(isset($_REQUEST["getdelegates"])){
		getDelegates();
	}

	function login(){
		$username = $_REQUEST["username"];
		$password = $_REQUEST["password"];
		//$e_password = md5($password);
		//echo "Got password and username";

		$db = new DbConnect();

		$query = "SELECT staff_id, name, username, password, phone FROM staff WHERE username='$username' AND password='$password'";
		$result = mysql_query($query) or die(mysql_error());
		$num_rows = mysql_num_rows($result);
		$info = mysql_fetch_assoc($result);


		//echo "Got result";
		if($result){
			if($num_rows > 0){
				//echo "session_stuuf";
				session_start();
				$_SESSION['login'] = "1";
				$_SESSION['id'] = $info["staff_id"];
				$_SESSION['name'] = $info["name"];
				$_SESSION['username'] = $info["username"];
				$_SESSION['phone'] = $info["phone"];

				
				echo "true";
			}
			else{
				echo "false";
			}
		}
	}

	function register(){
		$full_name = $_REQUEST["full_name"];
		$email = $_REQUEST["email"];
		$phone = $_REQUEST["phone"];
		$organization = $_REQUEST["organization"];
		$event_id = intval($_REQUEST["event_id"]);
		$barcode = mt_rand(10000, 999999);

		$tel = substr($phone, 1);
		$db = new DbConnect();
		$query = "INSERT INTO delegates(name, email, phone, organization, event_id, barcode) VALUES('$full_name', '$email', '$phone', '$organization', '$event_id', '$barcode')";
		$result = mysql_query($query) or die(mysql_error());
		if($result == 1){
			$url = "http://api.smsgh.com/v3/message/send?"
			. "From=IWuzHierSom"
			. "&To=%2B233$tel"
			. "&Content=Your%20registration%20was%20successful.%20Registration%20code%20is%20{$barcode}"
			. "&ClientId=odfbifrp"
			. "&ClientSecret=rktegnml"
			. "&RegisteredDelivery=true";
			//Fire the request and wait for the response
			$response = file_get_contents($url);
			var_dump($response);

			echo '{"result" : 1, "error" : "false"}';
		}
	}

	function addEvent(){
		$event_name = $_REQUEST["event_name"];
		$organizer = $_REQUEST["organizer"];
		$venue = $_REQUEST["venue"];
		$date = $_REQUEST["date"];
		$time = $_REQUEST["time"];
		$staff = 1;
		$staff_phone = "+233268722849";

		$db = new DbConnect();
		$query = "INSERT INTO events(name, organizer, venue, date, time, staff_id, staff_phone) VALUES('$event_name', '$organizer', '$venue', '$date', '$time', '$staff', '$staff_phone')";
		$result = mysql_query($query) or die(mysql_error());

		if($result == 1){
			echo '{"result" : 1, "error" : "false"}';
		}
	}

	function getEvents(){
		$db = new Dbconnect();

		$response = array();
		$response["events"] = array();

		$result = mysql_query("SELECT * FROM events");

		while($row = mysql_fetch_array($result)){
			//temporary array to create single category
			$tmp = array();
			$tmp["event_id"] = $row["event_id"];
			$tmp["name"] = $row["name"];
			$tmp["organizer"] = $row["organizer"];
			$tmp["venue"] = $row["venue"];
			$tmp["date"] = $row["date"];
			$tmp["time"] = $row["time"];
			$tmp["staff_phone"] = $row["staff_phone"];
			//push category to final json array
			array_push($response["events"], $tmp);
		}
		//keeping response header to json
		header('Content-Type: application/json');

		//echoing json result
		echo json_encode($response);
	}

	function getDelegates(){
		$dv = new Dbconnect();
		$response = array();
		$response["delegates"] = array();
		$result = mysql_query("SELECT * FROM delegates");

		while ($row = mysql_fetch_array($result)) {
			$tmp = array();
			$tmp["delegate_id"] = $row["delegate_id"];
			$tmp["name"] = $row["name"];
			$tmp["email"] = $row["email"];
			$tmp["phone"] = $row["phone"];
			$tmp["organization"] = $row["organization"];
			$tmp["event_id"] = $row["event_id"];

			array_push($response["delegates"], $tmp);
		}
		header('Content-Type: application/json');

		echo json_encode($response);

	}

	function deleteEvent(){
		$db = new Dbconnect();

		$event_id = $_REQUEST["delete_id"];
		$query = "DELETE FROM events WHERE event_id = $event_id";
		$result = mysql_query($query) or die(mysql_error());

		if($result == 1){
			header("Location: home.php");
		}
	}
 ?>