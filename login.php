<style>
#resetPassword{display:none;}
.cancel{margin-left:50px;}
input.btn.btn-primary.pull-left {
    height: 30px;
    width: auto;
    padding: 5px 25px;
}
</style>
<h1>Professional Login</h1>
<form id="login" name="form1" method="post" action="prologin.php">
	<table cellspacing="0" cellpadding="4" border="0">
		<tr>
			<td>Email address:</td>
			<td><input type="text" name="email" size="25" maxlength="255" /></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password" size="25" maxlength="25" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="login" /></td>
		</tr>
	</table>
	<br />
	Forgot your password? <a href="#" onclick="editDisplay('login', 'resetPassword')">Click here</a>
</form>

<form id="resetPassword" class="form-horizontal" role="form" method="post" onsubmit="return validEmail();" action="forgotPassword.php">
	<table cellspacing="0" cellpadding="4" border="0">
		<tr>
			<td>Email address:</td>
			<td><input type="email" required="required" id="remail" name="q" class="form-control" placeholder="Enter email" autocomplete="on" autofocus="autofocus" /></td>
		</tr>
	</table><br />
	<input type="submit" class="btn btn-primary pull-left" value="Request to reset password" />
	<a href="#" class="cancel" onclick="editDisplay('resetPassword', 'login')">  Cancel</a>
</form>
<br />
<p>Not signed up as a professional yet? <a href='signup.php'>Set up an account now!</a></p>
<script type="text/javascript">
		function editDisplay(hideID, displayID ){
			document.getElementById(hideID).style.display = "none";
			document.getElementById(displayID).style.display = "block";
		}
		
		function validremail(){
			var email = document.getElementById('remail').value;
			apos= email.indexOf("@");
			dotpos= email.lastIndexOf(".");
			if (apos<1 || dotpos-apos<2 || dotpos+2>=email.length){
				alert("Not a valid email address.");
				return false;
			}else{
				return true;
			}	
		}
</script>
<script type="text/javascript">
	function validEmail(){
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
