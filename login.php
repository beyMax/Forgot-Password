<style>
#resetPassword{display:none;}
.cancel{margin-left:50px;}
input.btn.btn-primary.pull-left {
    height: 30px;
    width: auto;
    padding: 5px 25px;
}
</style>
<?php
	session_start();
	
	include "db.php";
	
	if(isset($_GET['logout'])){
		unset($_SESSION['proid']);
	}
	if(isset($_POST['submit'])){
		$email = trim(strip_tags($_POST['email']));
		$password = trim(strip_tags($_POST['password']));
		if($email=='' || $password==''){
			$msg = "<p class='err'>Blank email or password. Try again.</p>\n";
		}
		else{
			$query = "select id, account_id, disabled from professionals where email='$email' and password='$password' limit 1";
			$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
			$id = 0;
			while ($r = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$id = $r['id'];
				$account_id = $r['account_id'];
				$disabled = $r['disabled'];
			}
			$suspended = 0;
			if($id!=0){
				// see if account is suspended by admin
				$query = "select admin_suspend from accounts where id='$account_id' limit 1";
				$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
				while ($r = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$suspended = $r['admin_suspend'];
				}
			}
			
			if($id==0){
				$msg = "<p class='err'>Invalid email or password. Try again or contact customer support for more information.</p>\n";
			}
			elseif($disabled==1){
				$msg = "<p class='err'>Professional account is disabled. Contact customer support for more information.</p>\n";
			}
			elseif($suspended==1){
				$msg = "<p class='err'>This account is currently suspended. Contact customer support for more information.</p>\n";
			}
			else{
				$query = "update professionals set last_login='".time()."' where id='$id' limit 1";
				$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
				unset($_SESSION['clientid']); // unset client session
				unset($_SESSION['staffid']); // unset staff session
				$_SESSION['proid'] = $id;
				
				// Now we get the PayPal billing profile information, if they have started a subscription
				$query = "select paypal_monthly_profile_id from accounts where id='$account_id' limit 1";
				$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
				$PROFILEID = '';
				while ($r = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$PROFILEID = $r['paypal_monthly_profile_id'];
				}
				
				// they have a paypal profile, let's make sure it is still active
				if($PROFILEID!=''){
					$expiry = '';
					include "paypalvars.php";
				
					// GetRecurringPaymentsProfileDetails API
					//		https://www.x.com/developers/paypal/documentation-tools/api/getrecurringpaymentsprofiledetails-api
					$nvpStr = '';
					$nvpStr .= "&PROFILEID=" . urlencode($PROFILEID);
							
					// Execute the API operation; see the PPHttpPost function in paypalvars.php
					$httpParsedResponseAr = PPHttpPost('GetRecurringPaymentsProfileDetails', $nvpStr);
					
					if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {						
						// stuff we need to know:
						$STATUS = urldecode($httpParsedResponseAr['STATUS']);  // ActiveProfile, PendingProfile, CancelledProfile, SuspendedProfile, ExpiredProfile						
						$FINALPAYMENTDUEDATE = strtotime(urldecode($httpParsedResponseAr['FINALPAYMENTDUEDATE'])); // final scheduled payment due date before the profile expires
						$NEXTBILLINGDATE = strtotime(urldecode($httpParsedResponseAr['NEXTBILLINGDATE'])); // next billing date (YYYY-MM-DD)
						$OUTSTANDINGBALANCE = urldecode($httpParsedResponseAr['OUTSTANDINGBALANCE']); 	// past due
						$LASTPAYMENTDATE = strtotime(urldecode($httpParsedResponseAr['LASTPAYMENTDATE'])); // last successful payment (YYYY-MM-DD)
						$LASTPAYMENTAMT = urldecode($httpParsedResponseAr['LASTPAYMENTAMT']); // last successful payment amount
						
						// default expiry is two months from now
						// (that way they can't get too behind in their billing)
						$expiry = strtotime("+2 months");
						
						// if account is not active or pending, set expiry date to now
						if($STATUS!='Active' && $STATUS!='Pending'){
							$expiry = time();
						}
						// account going to expire soon, set expiry date to $FINALPAYMENTDUEDATE
						if($FINALPAYMENTDUEDATE!=0) {
							$expiry = $FINALPAYMENTDUEDATE;
						}
						
						// if we're changing the expiry date, do it now
						if($expiry!=''){
							$query = "update accounts set expiry='$expiry' where id='$account_id' limit 1";
							$result = mysql_query($query) or die("Query failed.<br /><br />$query<br /><br />" . mysql_error());
						}
					}
				}
				header("Location:prohome.php");
			}
		}
	}
	
	include "pagetop.php";
?>
<h2>Professional Login</h2>
<?php 
	if(isset($msg)) echo $msg;
?>
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
<p>Not signed up as a professional yet? <a href='prosignup.php'>Set up an account now!</a></p>
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
<?php
	include "pagebottom.php";
?>