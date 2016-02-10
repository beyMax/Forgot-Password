<?php
include "db.php"; // connect database (your database connection code)
$message = 'Invalid email. Try again or contact customer support for more information';
if(isset($_POST["q"])){
	$email = test_input($_POST["q"]);
	$query="SELECT id, password, firstname, lastname FROM professionals WHERE email='$email'"; //professionals is the table name
	$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$pass = $row["password"];
		$id = $row["id"];
		$fname = $row["firstname"];
		$lname = $row["lastname"];
		$name = $fname ." ".$lname;
		$encrypt = md5(28624*13+$pass);
		$to = $email;
		$subject="Password reset for CXLIST professional account";
		$from = 'support@abcd.com';
		$body = '
		<div style="padding:0;width:100%!important;margin:0" marginheight="0" marginwidth="0">
<center>
	<table cellpadding="8" cellspacing="0" style="padding:0;width:100%!important;background:#ffffff;margin:0;background-color:#ffffff" border="0">
	<tbody>
		<tr><td valign="top">
			<table cellpadding="0" cellspacing="0" style="border-radius:4px;border:1px #dceaf5 solid" border="0" align="center">
			<tbody>
			<tr>
			<td colspan="3" height="6"></td></tr><tr style="line-height:0px"><td width="100%" style="font-size:0px" align="center" height="1"><h1 style="#139FE4">CXLIST.COM</h1></td></tr><tr><td><table cellpadding="0" cellspacing="0" style="line-height:25px" border="0" align="center"><tbody><tr><td colspan="3" height="30"></td></tr><tr><td width="36"></td>
<td width="454" align="left" style="color:#444444;border-collapse:collapse;font-size:11pt;font-family:proxima_nova,\'Open Sans\',\'Lucida Grande\',\'Segoe UI\',Arial,Verdana,\'Lucida Sans Unicode\',Tahoma,\'Sans Serif\';max-width:454px" valign="top">

Hi '.$name.',<br><br>Someone recently requested a password change for your CXLIST Professional account. If this was you, you can set a new password <a href="https://cxlist.com/resetPassword.php?q='.$encrypt.'&id='.$id.'" target="_blank">here</a>:<br><br><center><a style="border-radius:3px;color:white;font-size:15px;padding:14px 7px 14px 7px;max-width:210px;font-family:proxima_nova,\'Open Sans\',\'lucida grande\',\'Segoe UI\',arial,verdana,\'lucida sans unicode\',tahoma,sans-serif;border:1px #1373b5 solid;text-align:center;text-decoration:none;width:210px;margin:6px auto;display:block;background-color:#007ee6" href="https://cxlist.com/resetPassword.php?q='.$encrypt.'&id='.$id.'" target="_blank">Reset password</a></center>
<br>If you can\'t open the link, you can copy and paste the following link in your web browser:<br />
		https://abcd.com/resetPassword.php?q='.$encrypt.'&id='.$id.'
<br>If you don\'t want to change your password or didn\'t request this, just ignore and delete this message.<br><br>To keep your account secure, please don\'t forward this email to anyone.<br><br>Thanks!<br>The CXLIST Team</td>
<td width="36"></td>
</tr><tr><td colspan="3" height="36"></td></tr></tbody></table></td></tr></tbody></table><table cellpadding="0" cellspacing="0" align="center" border="0"><tbody><tr><td height="10"></td></tr><tr><td style="padding:0;border-collapse:collapse"><table cellpadding="0" cellspacing="0" align="center" border="0"><tbody><tr style="color:#a8b9c6;font-size:11px;font-family:proxima_nova,\'Open Sans\',\'Lucida Grande\',\'Segoe UI\',Arial,Verdana,\'Lucida Sans Unicode\',Tahoma,\'Sans Serif\'"><td width="400" align="left"></td>
<td width="128" align="right">© 2016 <span class="il">ABCD.COM</span></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></center></div>';	
		
		$headers = "From: " . strip_tags($from) . "\r\n";
		$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		mail($to,$subject,$body,$headers);
		$message = "Your password reset link send to your e-mail address.";
	}	
}else{
	echo '<script type="text/javascript">window.alert("Please submit proper email address.");</script>';
	echo '<script type="text/javascript">window.location="https://abcd.com/prologin.php";</script>';
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = strip_tags($data);
	$data = htmlspecialchars($data);
	return $data;
}
	echo '<script type="text/javascript">window.alert("'.$message.'");</script>';
	echo '<script type="text/javascript">window.location="https://abcd.com";</script>';
?>
