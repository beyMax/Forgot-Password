<?php
include "db.php"; //connect with your database using a php file
if(isset($_POST["submit"])){
	$email = test_input($_POST["mail"]);
	$code = test_input($_POST["code"]);
	$query = "SELECT email, password, firstname, lastname FROM professionals WHERE email='$email'"; // professionals is the table name
	$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
	$check = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$pass = $row["password"];
		$email = $row["email"];
		$fname = $row["firstname"];
		$lname = $row["lastname"];
		$name = $fname ." ".$lname;
		$encrypt = md5(28624*13+$pass);
		$check = 1;
		
		if($encrypt==$code){
			$password = test_input($_POST["password"]);
			$update = mysql_query("UPDATE professionals SET password='$password' WHERE email='$email'") or die(mysql_error());
			
			$to = $email;
			$subject="Password successfully changed";
			$from = 'support@abcd.com';
			$body = 'Hi '.$name.', <br/> <br/>
			This email is to confirm that you recently changed your password for the CXLIST professional account.<br /><br />
			If you did not request this change, please contact CXLIST support immediately at <span style="color:#139FE4;">info@cxlist.com</span><br /><br />

			Thanks,<br />
			The CXLIST Team';
		
			$headers = "From: " . strip_tags($from) . "\r\n";
			$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($to,$subject,$body,$headers);
			echo '<script type="text/javascript">window.alert("Password successfully changed");</script>';
			echo '<script type="text/javascript">window.location="https://www.abcd.com/login.php";</script>';
		}else{
			echo '<script type="text/javascript">window.alert("Go back to login page and try again later.");</script>';
			echo '<script type="text/javascript">window.location="https://www.abcd.com/login.php";</script>';
		}
	}
	if( $check == 0 ){
		echo '<script type="text/javascript">window.alert("Account does not found.");</script>';
		header("Location:login.php");
	}
}else{
	echo '<script type="text/javascript">window.alert("Go back to login page and try again later.");</script>';
	echo '<script type="text/javascript">window.location="https://www.abcd.com/login.php";</script>';
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = strip_tags($data);
	$data = htmlspecialchars($data);
	return $data;
}
?>
