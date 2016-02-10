<?php
	session_start();
	include "db.php";// Connect with database

if(isset($_GET["q"]) && isset($_GET["id"])){

	$code = test_input($_GET["q"]);
	$id = test_input($_GET["id"]);
	$query="SELECT email, password, firstname, lastname FROM professionals WHERE id='$id'";
	$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$pass = $row["password"];
		$email = $row["email"];
		$fname = $row["firstname"];
		$lname = $row["lastname"];
		$name = $fname ." ".$lname;
		$encrypt = md5(13*31+$pass); // encript your data (some addition, multiplication etc for security improvement)
		if($encrypt==$code){
?>
<br /><br /><h1>Reset your password </h1><br /><br />
<form id="login" role="form" class="clearfix" action="saveReset.php" method="post" onsubmit="return validForm();">
	<table cellspacing="0" cellpadding="4" border="0">
		<tr>
			<td>New Password:</td>
			<td><input type="password" name="password" required="required" class="form-control" id="pass" placeholder="New Password" /></td>
		</tr>
		<tr>
			<td>Repeat New Password:</td>
			<td><input type="hidden" name="mail" value="<?php echo $email; ?>"/>
				<input type="hidden" name="code" value="<?php echo $encrypt; ?>"/>
				<input type="password" name="rpassword" required="required" class="form-control" id="rePass" placeholder="Repeat New Password" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Reset Password" /></td>
		</tr>
	</table>
	<br />
</form>
                            
<?php
		}else{
			echo '<script type="text/javascript">window.alert("Go back to login page and try again later.");</script>';
			header("Location:login.php");
		}
	}
}else{
	echo '<script type="text/javascript">window.alert("Go back to login page and try again later.");</script>';
	header("Location:login.php");
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = strip_tags($data);
	$data = htmlspecialchars($data);
	return $data;
}
?>

<script type="text/javascript">
function validForm(){
		var pass = document.getElementById('pass').value;
		var repass = document.getElementById('rePass').value;
		if(pass != repass){
			alert("Password does not matched ");
			return false;
		}else{
			return true;
		}	
	}
</script>
