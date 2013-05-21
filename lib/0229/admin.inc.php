<?php
/* --------------------------------------------------------------------	*/
/* Retrieve any notices/alerts that are stored in session				*/
/* E.g. success/failure messages.  Return as a html formatted string	*/
/* --------------------------------------------------------------------	*/
function getAlerts() {
	// check if alert exists.
	if (!empty($_SESSION[alert])) {
		$alert = '<div class="redmd" style="width:500">';
		$alert .= $_SESSION[alert];
		$alert .= '</div><br>';
		
		// Reset Session variable now that it has been displayed 
		// (so that it's not repeated on other pages)
		$_SESSION[alert] = "";
	}
	return $alert;
}

/*
 *@param to get all the team deatails 
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */
function adminTeamDetails() {
	global $db;
	$query = "select * from team order by country DESC";
	$result = $db->query($query);
	if(dbError($result)) {
		return $result;
	}
	return;
}

// retrieve any customizable server settings
function getSettings() {
	global $db;
	$query = "
		SELECT	*
		FROM		settings
	";
	$result = $db->query($query);
	return $result->fetchRow();
}

function updateSettings($messageimg) {
	global $db;
	$query = "
		UPDATE	settings
		SET			messageimg = '$messageimg'
	";
	$result = $db->query($query);
	return;
}

/*
 *@param to create a team
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */

function adminTeamCreate($country, $continent, $file_name, $animated_file) {
	global $db;
	$query = "insert into team (country, continent, file_name, animatedflag) values ('$country', '$continent', '$file_name', '$animated_file')";
	$result = $db->query($query);
	if(dbError($result)) {
		return $result;
	}
	return;
}

function adminTeamDelete($teamid) {
	global $db;
	$query = "delete from team where id = $teamid";
	$result = $db->query($query);
	if(dbError($result)) {
		return $result;
	}
	return;
}

function adminTeamUpdate($id, $country, $continent, $file, $animatedfile) {
	global $db;
	
	if($file != '')
		$files = ", file_name = '$file'";
	if($animatedfile != '')
		$files .= ", animatedflag = '$animatedfile'";
	$query = "
		update 	team 
		set 		country = '$country', continent = '$continent'
						$files
		where 	id = $id";
	$db->query($query);
	return;
}

function getGivenTeamDetails($id) {
	global $db;
	
	$query = "select * from team where id = $id";
	$result = $db->query($query);
	return $result->fetchRow();
}

/*
 *@param Admin user profile update
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */
function adminUpdateUser($user_id,$login, $password, $email, $salutation, $first_name, $last_name, $gender ,$address, $city,$state , $zip, $country, $phone, $dob, $incomelevel, $investingstyle, $tradespermonth, $education, $trades){
	global $db;
	$query="
		update 	user 
		set 	login='$login', password='$password', email='$email', 
				salutation='$salutation', first_name='$first_name', 
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
 *@param Admin user status update
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */
function adminUserStatus($user_id,$status){
	global $db;
	$query="update user set status='$status' where user_id='$user_id'";
	$db->query($query);
	return 1;
}

/*
 *@param to get all the user's deatails 
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */

function adminUserDetails() {
	global $db;
	$query = "select * from user order by user_id DESC";
	$result = $db->query($query);
	if(dbError($result)) {
		return $result;
	}
	return;
}
/*
 *@param to delete an user and also his projects with attachmets and his portfolio deleted
 *@return 
 *@author: dhyan
 *@date: 24/12/02
 */

function adminUserDelete($user_id){
	global $db;
		
	// Finally delete the user
	$query="delete from user where user_id='$user_id'";
	$db->query($query);
	
	return 1;
}

/*
 *@param database query result
 *@return 
 *@author: katyan
 *@date: 17/10/02
 */
function dbError($result) {
	global $db;
	if(DB::isError($result)) {
		echo "<b>" . $result->getMessage() . "</b><br>";
		print_r($result);
		die;
	}
	else {
		return 1;
	}
}
/*
 *@purpose	to add project attachment
 *@param	project id and filename
 *@return	
 *@author	katyan
 *@date		26/10/2002
 */
function addProjectAttachment($project_id, $file_name) {
	global $db;
	$query = "insert into project_attachment (project_id, file_name, creation_date) values(" . $project_id . ", '" . $file_name . "', '" . time() . "')";	
	$db->query($query);
	return;
}	

//admin message - get all teams
function getAllTeamsMessage() {
	global $db;
	
	$query = "select country as team from team order by country asc";
	return $db->query($query);
}
//admin message - get all players
function getAllPlayersMessage($team = 0) {
	global $db;
	if($team) {
		$query = "select login, email from user where team = '$team' order by login asc";
	}
	else {
		$query = "select login, email from user order by login asc";
	}	
	return $db->query($query);
}

/*
 *Send email
 */
function sendEmail($to, $from, $subject, $message) {
	global $CFG;
	$name = "World Investing Championship";
	$mail = new phpmailer();
	$mail->From = $from;
	$mail->FromName = $name;
	$mail->Host = $CFG->SMTP_SERVER;
	$mail->Mailer = "smtp";
	$mail->Subject = $subject;
	$mail->Body = $message;
	$mail->AddAddress($to, $toname);

	if(!$mail->Send())
		echo $mail->ErrorInfo;
}

function getStaticPages($category='', $extrasort='') {
	global $db;
	$categoryfilter = '';
	if (!empty($category))
		$categoryfilter = "WHERE	category = '$category'";

	$query = "
		select	*, case when pagename='Home' then 1 
					when category='Newsletter' then 2 else pageid end as sortid
		from	staticpages
		$categoryfilter
		ORDER BY $extrasort sortid
	";
	return $db->query($query);
}

function getStaticPage($pageid) {
	global $db;
	$query = "
		select 	*
		from	staticpages
		where	pageid='$pageid'
	";
	$result = $db->query($query);
	return $result->fetchRow();
}
function editStaticPage($pageid, $content) {
	global $db;
	$query = "
		UPDATE	staticpages
		SET			pagecontent = '$content'
		WHERE		pageid='$pageid'
	";
	$result = $db->query($query);
	return;
}

function addStaticPage($pagename, $category, $parentpage, $site) {
	global $db;
	if (empty($pagename))
		return;
	$query = "
		INSERT INTO staticpages
		SET			pagename = '$pagename',
					category = '$category',
					parentpage = '$parentpage',
					site = '$site'
	";
	$result = $db->query($query);
	return;
}

function deleteStaticPage($pageid) {
	global $db;
	$query = "
		DELETE FROM staticpages
		WHERE		pageid='$pageid'
	";
	$result = $db->query($query);
	return;
}

// for admin referral page (drop down to select older registration stats)
function getUniqueRegDates() {
	global $db;
	$query = "
		SELECT 	DISTINCT month(from_unixtime(date)) as month, year(from_unixtime(date)) as year
		FROM	user
		WHERE	status=1
				AND sponser > 0
	";
	$result = $db->query($query);
	return $result;
}

// get user + number of referrals for particalar year/month
function getRegStats($year, $month, $userid='') {
	global $db;
	$query = "
		SELECT 	b.login, b.first_name, b.last_name, count(b.login) as referrals, max(from_unixtime(a.date)) as latestdate, b.user_id
		FROM	user a, user b
		WHERE	a.sponser = b.user_id
				and a.status=1 
				and a.sponser > 0
				and month(from_unixtime(a.date)) = $month
				and year(from_unixtime(a.date)) = $year
		GROUP BY b.login, b.first_name, b.last_name, b.user_id
		ORDER BY referrals DESC
	";
	$result = $db->query($query);
	return $result;
}

// get detail about a user's referrals
function getUserReferrals($userid, $year='', $month='') {
	global $db;
	// limit to a period of time if requested
	if (!empty($year))
		$extrafilter = " and year(from_unixtime(a.date)) = $year";
	if (!empty($month))
		$extrafilter = " and month(from_unixtime(a.date)) = $month";

	$query = "
		SELECT 	concat(a.first_name, ' ', a.last_name) as referredby, a.login, from_unixtime(a.date) as signupdate
		FROM	user a, user b
		WHERE	a.sponser = b.user_id
				and b.user_id = '$userid'
				and a.status=1 
				and a.sponser > 0
				$extrafilter
		ORDER BY signupdate DESC
	";
	$result = $db->query($query);
	echo mysql_error();
	return $result;
}
?>