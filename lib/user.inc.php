<?php
/*
 *@param User profile update
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */
function userUpdate($user_id,$password, $email, $first_name, $last_name, $gender ,$address, $city, $state , $zip, $country, $phone, $dob, $incomelevel, $investingstyle, $tradespermonth, $education, $trades){
	global $db;
	$query="
		update 	user 
		set 	password='$password', email='$email',first_name='$first_name', 
				last_name='$last_name', gender='$gender',address='$address', 
				city='$city', state='$state', zip='$zip', country='$country', 
				phone='$phone',dob='$dob', incomelevel='$incomelevel', 
				investingstyle='$investingstyle', tradespermonth = '$tradespermonth',
				highesteducation = '$education', trades = '$trades'
		where 	user_id='$user_id'
	";
	$db->query($query);
	return 1;

}
/*
 *@purpose: To verify unique username(email)
 *@param: user email
 *@return: success (1 - unique)
 *@return: failure (0)
 *@author: katyan
 *@date: 29/11/02
 */
function isUniqueEmail($email) {
	global $db;
	$query = "select * from user where email = '$email'";

	$result = $db->query($query);
	if(dbError($result)) {
		if($result->numRows() > 0) {
			return 0; //failure
		}
		else {
			return 1; //success
		}
	}
}

/*
 *@purpose: To verify unique username
 *@param: user email
 *@return: success (1 - unique)
 *@return: failure (0)
 *@author: katyan
 *@date: 29/11/02
 */
function isUniqueUser($login) {
	global $db;
	$query = "select * from user where login = '$login'";
	$result = $db->query($query);
	if(dbError($result)) {
		if($result->numRows() > 0) {
			return 0; //failure
		}
		else {
			return 1; //success
		}
	}
}

/*
 *@purpose: To register a user
 *@param: user information
 *@return: 
 *@author: dhyan
 *@date:23/12/02
 */
function registerUser($login, $password, $email, $salutation, $first_name, $last_name, $gender, $address, $city, $state, $zip, $country, $phone, $dob, $sponsor, $incomelevel, $investingstyle, $tradespermonth, $education, $trades, $foundus, $searchengine, $foundusother) {
	global $db;
	$sponsor = ($sponsor == 0)?'':$sponsor;

	$query = "insert into user (login, password, email, salutation, first_name, last_name, gender, address, city, state, zip, country, phone, dob, status, date, sponser, incomelevel, investingstyle, tradespermonth, highesteducation, trades, foundus, searchengine, foundusother) values('$login', '$password', '$email', '$salutation', '$first_name', '$last_name', '$gender','$address', '$city', '$state', '$zip','$country', '$phone', '$dob','1', '" . time() . "', '$sponsor', '$incomelevel', '$investingstyle', '$tradespermonth', '$education', '$trades', '$foundus', '$searchengine', '$foundusother')";
	$result=$db->query($query);
	//echo $query;die;
	if(dbError($result)) {
		return $result;
	}
}

/*
 *@purpose: To login a user
 *@param: user information
 *@return: 
 *@author: katyan
 *@date: 29/11/02
 */
function loginUser($login, $password) {
	global $db;
	$query = "select * from user where login = '$login' and password = '$password' and status = '1'";
	$result = $db->query($query);
	if(dbError($result)) {
		$row = $result->fetchRow();
		return $row->user_id;
	}
	return 0;
} 

/*
 *@purpose: To get details of a user
 *@param: user id
 *@return: 
 *@author: katyan
 *@date: 29/11/02
 */
function getUserDetails($user_id, $login="") {
	global $db;
	// if user_id is empty, look up user by their login
	if (empty($user_id))
		$query = "select * from user where login = '$login'";
	else
		$query = "select * from user where user_id = $user_id";
	$result = $db->query($query);
	if(dbError($query)) {
		return $result->fetchRow();
	}
	return;
}

function getUserDetailsLogin($login) {
	global $db;
	$lastupdate = dateOfPortfolioUpdate();

	$query = "
		select 	a.*, b.total_equity, b.total_long, b.total_short 
		from 		user a, portfolio b
		where 	a.login = '$login' 
						AND b.date='$lastupdate'
						and b.login = a.login";
	$result = $db->query($query);
	if(dbError($query)) {
		return $result->fetchRow();
	}
	return;
}

/*
 *@purpose: To display signin status of the user at top right of the page
 *@param: 
 *@return: 
 *@author: katyan
 *@date: 29/11/02
 */
function userSignInOut() {
	global $_SESSION;
	if(isset($_SESSION[user])) {
	echo "<div align='right'><font class='item'>Sign in as: " . $_SESSION[user][login] . "</font> <a href='../common/logout.php' class='link'>Sign out</a></div>";
	}
	return;
} 
/*
 *@purpose: To send an email notifying registration to user
 *@param: user information
 *@return: 
 *@author: dhyan
 *@date:23/12/02
 */

function registrationEmail($name,$email,$pass){
		global $CFG;
		$mail = new phpmailer();
		$mail->From = $CFG->ADMIN_EMAIL;
		$mail->FromName = $CFG->ADMIN_NAME;
		$mail->Host = $CFG->SMTP_SERVER;
		$mail->Mailer = "smtp";
		$mail->Subject=$CFG->REGISTRATION_SUBJECT;

		$mail->Body="Hi ".$name.",\n
Welcome to World Investing Championship, and thank you for being a part of the web's largest independent stock market simulation.\n
We have already activated your account and you're ready to start trading immediately!\n
To get started visit ".$CFG->webserver.", enter your username and password and then begin making deals!\n
It's powered with healthy competition and compelling prizes. And best of all, it's FREE!\n
We welcome you as part of our community of investors of all levels, including MBA's, financial professionals, investment clubs and college students.\n
But before you do, please take note of your important password and user information:\n\nEmail Id:".$email."\nPassword:".$pass."\n\n
If you have any questions, please contact us directly at ".$CFG->ADMIN_EMAIL."\n\n
Thank you once again.  ".$CFG->ADMIN_NAME;
		$mail->AddAddress($email,$name);
		if(!$mail->send()){
			echo $mail->ErrorInfo;
		}
		$mail->ClearAddresses();
}

function sendPassword($login, $inputemail){
	global $CFG;
	global $db;
	$query = "
		select 	*
		from 		user a
		where 	a.login='$login' OR email='$inputemail'";
	$result = $db->query($query);
	$row = $result->fetchRow();
	if ($result->numRows() == 0)
		return -1;

	$mail = new phpmailer();
			$mail->From = $CFG->ADMIN_EMAIL;
			$mail->FromName = $CFG->ADMIN_NAME;
			$mail->Host = $CFG->SMTP_SERVER;
			$mail->Mailer = "smtp";
			$mail->Subject="Your Password";
	
			$mail->Body="Here is the account information you requested to gain access to your
account:

Username:   " . $row->login . "
Password:   " . $row->password . "
    
If you continue to have problems or have not requested this
information, feel free to contact us via:

http://www.worldinvestingchampionship.com/thegame/info/contact_us.php
";
			$mail->AddAddress($row->email,$login);
			if(!$mail->send()){
				echo $mail->ErrorInfo;
			}
			$mail->ClearAddresses();
			return $row->email;
}

/*
 *@purpose: To register user to a team
 *@param: user login, team (country)
 *@return: 
 *@author: katyan
 *@date: 1/1/03
 */
function registerTeamUser($login, $team, $sponser) {
	global $db;
	// check if new pre-registration, or if already had a future team 
	// (will determine which email gets sent)
	$olddetail = getUserDetails('', $login);
	$query = "update user set futureteam = '$team', payment_status = '1', sponser = '$sponser' where login = '$login'";
	$db->query($query);

	// get future competition details
	$futureCompetition = getFutureCompetition();

	// send confirmation emails
	if (!empty($olddetail->futureteam))
		$message = "
Congratulations!  You have successfully changed teams for Competition #" . $futureCompetition[compid] . " in the World Investing Championship series.
 
You are now registered as a member of Team $team.  
You are no longer a member of Team " . $olddetail->futureteam . ". 

Competition #" . $futureCompetition[compid] . " begins " . $futureCompetition[startdate] . " and ends " . $futureCompetition[enddate] . ".  You can see a list of other players on your team by logging on to the website at www.WorldInvestingChampionship.com  and going to your Account Command Center.  The \"Next Competition\" area of the Command Center has all of the links you will need.

If you elect to change teams prior to the start of a competition, you can do so from your Account Command Center.  Once each competition gets under way,  the teams are closed to new players and registered players cannot change teams.

We are really excited that you have joined us for the next competition!  It will be challenging, educational, and fun and you have the opportunity to win great prizes.  As always, if you have questions you can get answers by using the \"Investor Resources\" link on the website.

Have a great competition!

The World Investing Championship Management Team";
	else
		$message = "
Congratulations!  You are now officially pre-registered as a member of Team $team for the next competition in the World Investing Championship series.  This competition begins " . $futureCompetition[startdate] . " and ends " . $futureCompetition[enddate] . ".  You can see a list of other players on your team by logging on to the website at www.WorldInvestingChampionship.com  and going to your Account Command Center.  The \"Next Competition\" area of the Command Center has all of the links you will need.  
 
If you elect to change teams prior to the start of a competition, you can do so from your Account Command Center.  Once each competition gets under way,  the teams are closed to new players and  registered players cannot change teams.
 
We are really excited that you have joined us for the next competition!  It will be challenging, educational, and fun and you have the opportunity to win great prizes.  As always, if you have questions you can get answers by using the \"Investor Resources\" link on the website.
 
Have a great competition!
 
The World Investing Championship Management Team";

	// do not send email if registered for same team they were already on
	if ($olddetail->futureteam != $team)
		emailUser($olddetail->login, $olddetail->email, "WorldInvestingChampionship Pre-Registration", $message);

	return;
}

// withdraw user from future competition
function withdrawFutureComp($userid) {
	global $db;
	
	$query = "
		update 	user 
		set 		futureteam = ''
		where 	user_id = '$userid'";
	$db->query($query);
	return;
}

function emailUser($toname, $toemail, $subject, $message) {
		global $CFG;
		$mail = new phpmailer();
		$mail->From = $CFG->ADMIN_EMAIL;
		$mail->FromName = $CFG->ADMIN_NAME;
		$mail->Host = $CFG->SMTP_SERVER;
		$mail->Mailer = "smtp";
		$mail->Subject=$subject;

		$mail->Body=$message;
		$mail->AddAddress($toemail,$toname);
		if(!$mail->send()){
			echo $mail->ErrorInfo;
		}
		$mail->ClearAddresses();
		
		return;
}

function getSponsors($name = '') {
	global $db;
	// search for sponsor
	if (!empty($name)) {
		$extrafilter =  " and (first_name like '%$name%' OR last_name like '%$name%'
			or concat(first_name, ' ', last_name) like '%$name%')";
	}
	$query = "
		SELECT	DISTINCT first_name, last_name, min(user_id) as user_id
		FROM	user 
		WHERE 	status=1
				$extrafilter
		GROUP BY first_name, last_name
		ORDER BY first_name, last_name";
	$result = $db->query($query);
	if(dbError($result)) {
		return $result;
	}
	return;
}
?>
