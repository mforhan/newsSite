<?php
function getContinentPlayers($continent) {
	global $db;
	
	$query = "select count(a.login) as total from user a, team b where a.team = b.country and b.continent = '$continent' and a.IC_user=0";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->total;
}

function getTeams($continent) {
	global $db;
	//getting all teams for which there exist at least one player
	$query = "SELECT a.country, avg(c.total_equity) as total FROM team a, user b, portfolio c where a.continent = '$continent' and b.team = a.country and c.login = b.login group by b.team and b.IC_user=0 order by total desc";
	$result = $db->query($query);
	$teams_ranked = array();
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		$teams_ranked[$i] = $row->country;
	}
	//getting list of all teams for which no player is registered
	// temporarily set Antarctica teams w/ single digit #'s to double digit for proper sorting order
	$query = "
		select 		(case when substring(country, 1, 4) = 'Zone' and length(substring(country, 6, 7)) = 1 then
								Concat('Zone 0', substring(country, 6, 7)) 
							else 
								country end) as country1,
							ltrim(rtrim(country)) as country
		from 			team 
		where 		continent = '$continent' 
		order by 	country1
	";
	$result = $db->query($query);
	$teams_total = array();
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		$teams_total[$i] = $row->country;		
	}
	$teams_diff = array_diff($teams_total, $teams_ranked);
	$teams = array_merge($teams_ranked, $teams_diff);

	return $teams;
}

// Get Points for All Players
function getPlayerPoints() {
	global $db;

	$lastupdate = dateOfPortfolioUpdate();

	$totalcontinents = 7;
	$totalteams = getTotalTeamNumber();
	$totalplayers = getTotalPlayersNumber();

	$query = "select * 
		FROM 		team a, user b, portfolio c 
		where 	c.date='$lastupdate' and b.team = a.country and c.login = b.login 
				and b.IC_user=0
	";
	$result = $db->query($query);
	$player = array();
	for ($i=0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		$teamrank = getTeamRanking($row->team);
		if ($row->continent)
			$continentrank = getContinentRanking($row->continent);
	
		$playerpoints = ($totalplayers+1)-getUserRanking($row->login);
		$countrypoints = ($totalteams+1)-$teamrank;;
		$continentpoints = (($totalcontinents+1)-$continentrank)*10;
		$player[$row->login][totalpoints] = $playerpoints + $countrypoints + $continentpoints;
		$player[$row->login][name] = $row->login;
		$player[$row->login][TE] = $row->total_equity;
		$player[$row->login][playerpts] = $playerpoints;
		$player[$row->login][countrypts] = $countrypoints;
		$player[$row->login][continentpts] = $continentpoints;
		// stats for competiton history
		$player[$row->login][team] = $row->team;
		$player[$row->login][total_cash] = $row->total_cash;
		$player[$row->login][total_short] = $row->total_short;
		$player[$row->login][total_long] = $row->total_long;
		$player[$row->login][interest_earned] = $row->interest_earned;
		$player[$row->login][starting_cash] = $row->starting_cash;
	}
//	$players_sorted = array_csort($player, 'totalpoints', SORT_DESC);
	$players_sorted = array_csort($player, 'TE', SORT_DESC);
	$rank = 0;
	foreach($players_sorted as $key=>$value) {
//		if ($players_sorted[$key][totalpoints] != $lastPlayersPoints)
		if ($players_sorted[$key][TE] != $lastPlayersPoints)
			$rank++;
		// keep track of points to compare with next player
//		$lastPlayersPoints = $player[$key][totalpoints];
		$lastPlayersPoints = $player[$key][TE];
		$players_sorted[$players_sorted[$key][name]][rank] = $rank;
	}
	return $players_sorted;
}

function array_csort() {  //coded by Ichier2003
  $args = func_get_args();
   $marray = array_shift($args);

  $msortline = "return(@array_multisort(";
   foreach ($args as $arg) {
       $i++;
       if (is_string($arg)) {
          foreach ($marray as $row) {
               $sortarr[$i][] = $row[$arg];
           }
       } else {
          $sortarr[$i] = $arg;
       }
       $msortline .= "\$sortarr[".$i."],";
   }
   $msortline .= "\$marray));";

   eval($msortline);
   return $marray;
}

function getContinentPoints() {
	global $db;

	$lastupdate = dateOfPortfolioUpdate();
	
	$continent_name = array("Asia", "Africa", "Europe", "North America", "South America", "Australia Oceania", "Antarctica");

	$continent = array();
	// set defaults
	foreach ($continent_name as $key => $value) {
		$continent[$value][value] = 0;
		$continent[$value][rank] = 0;
	}
	$query = "
		SELECT 	a.continent, avg(c.total_equity) as total 
		FROM 		team a, user b, portfolio c 
		where 	c.date='$lastupdate' and b.team = a.country and c.login = b.login 
				and b.IC_user=0
		group by continent 
		order by total desc";
		$result = $db->query($query);
		$rank = 0;
		$lastresult = 0;
		for($i = 0; $i < $result->numRows(); $i++) {
			$row = $result->fetchRow();
			// in case of tie, don't increment
			if ($row->total != $lastresult)
				$rank++;
			if (in_array($row->continent, $continent_name)) {
				$continent[$row->continent][value] = $row->total;
				$continent[$row->continent][rank] = $rank;
			}
			$lastresult = $row->total;
		}	

	arsort($continent);

	return $continent;
}

function getTeamPoints($thisteam='all') {
	global $db;

	$lastupdate = dateOfPortfolioUpdate();
	
	// allow gathering of 1 team's data if set
	if ($thisteam != 'all')
		$filter = " and b.team='$thisteam'";
	$team = array();
	$query = "
		SELECT 	b.team, avg(c.total_equity) as total 
		FROM 	team a, user b, portfolio c 
		where 	c.date='$lastupdate' and b.team = a.country and c.login = b.login 
				and b.IC_user=0
				$filter
		group by b.team
		order by total desc";
		$result = $db->query($query);
		$rank = 0;
		$lastresult = 0;
		for($i = 0; $i < $result->numRows(); $i++) {
			$row = $result->fetchRow();
			$row->team = strtoupper($row->team);
			// in case of tie, don't increment
			if ($row->total != $lastresult)
				$rank++;
			$team[$row->team][value] = $row->total;
			$team[$row->team][rank] = $rank;
			$team[$row->team][name] = $row->team;
			$lastresult = $row->total;
		}	
	return $team;
}

function getTotalPlayersNumber() {
	global $db;
	
	$query = "
		select 	count(*) as total 
		from		user a, team b
		where 	a.team=b.country
				and a.team != '' and a.IC_user=0
	";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->total;
}

/*
 *@purpose: To retrieve country flag from its name
 *@param: team (country)
 *@return: 
 *@author: dhyan
 *@date: 04/1/03
 */ 
function getFlag($team) {
	global $db;
	$query = "select * from team where country = '$team'";
	$result = $db->query($query);
	return $result->fetchRow();
} 

/*
 *@purpose: To get no of members of a team
 *@param: team (country)
 *@return: 
 *@author: katyan
 *@date: 1/1/03
 */
function getMembersCount($team) {
	global $db;
	
	$query = "select count(*) as num from user where team = '$team' and IC_user=0 group by team";
	$result = $db->query($query);
	$row = $result->fetchRow();
	if($row->num > 0) {
		return $row->num;
	}
	else {
		return 0;
	}	
}

/*
 *@purpose: To members' login of a team
 *@param: team (country)
 *@return: 
 *@author: katyan
 *@date: 1/1/03
 */ 
function getMembers($team) {
	global $db;
	$lastupdate = dateOfPortfolioUpdate();
	$query = "
		SELECT	a.*, b.total_equity 
		FROM		user a, portfolio b
		WHERE		b.date='$lastupdate' and b.login = a.login 
					AND team = '$team' and a.IC_user=0
		ORDER BY b.total_equity DESC";
	$result = $db->query($query);
	return $result;
}

function errorLog($str) {
	global $CFG;
	
	//if logging is on
	if(!$CFG->ERROR_LOG) {
		return;
	}
	
	$fp = fopen($CFG->dirroot . "/trash/error.log", "a+");
	if($fp) {
		//echo "File opened successfully: " . "post.log<br>";
	}	
	else {
		echo "File couldnt be opened<br>";
	}	
	$date = date("Y-m-d H:i:s");
	fwrite($fp, "\n-------------------------------------------\n");
	fwrite($fp, "Date: $date\n");
	fwrite($fp, $str);
	fwrite($fp, "\n-------------------------------------------\n");
	fflush($fp);
	fclose($fp);
	return;
}

function SendToHost($Host, $Method, $Path, $Data, $Referer=0) {
	global $PROXY, $DEBUG_POST, $LOG_POST;
	
	if($LOG_POST) {
		errorLog("URL: [$Host]\nMethod: [$Method]\nPath: [$Path]\nData: [$Data]\nReferer: [$Referer]\n");
	}	
	if($DEBUG_POST) {
		echo "<br><br><b>Data being posted</b><br>URL: -$Host-<br>  Method: -$Method-<br>  Path: -$Path-<br> Data: -$Data-<br> Referer: -$Referer-<br><br><br>";
	}
	if($PROXY[Enable] == 1) {
		$Proxy = 1;
		$Realm = base64_encode($PROXY[User] . ":" . $PROXY[Pass]);
	}
	if($Proxy) {
		$fp = fsockopen($PROXY[Host], $PROXY[Port], $errno, $errstr, 10);
	}	
	else {
		$fp = fsockopen($Host, 80, $errno, $errstr, 1);
	}	

	if(!$fp) {
		echo "\n\nCouldnt connect\n";
		echo "$errno:$errstr\n\n";
		echo "\nExiting ...\n";
		return;
	}
	
	if($Method == "GET") {
		$Path .= "?" . $Data;
	}
	
	if($Proxy) {
		//when connecting through proxy
                fputs($fp, "$Method http://$Host$Path HTTP/1.1\r\nHost:http://$Host\r\n\r\n");
	}
	else {
		//when connecting directly
		fputs($fp, "$Method $Path HTTP/1.1\n");
		fputs($fp, "Host: $Host\n");
	}

	fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
	fputs($fp, "Content-length: " . strlen($Data) . "\n");

	if($Proxy) {
		fputs($fp, "Proxy-Connection: Keep-Alive\r\n"); 
		fputs($fp, "Pragma: no-cache\r\n"); 
		fputs($fp, "Proxy-authorization: Basic $Realm\r\n"); 
	}

	fputs($fp, "User-Agent: PHP ".phpversion()."\n"); 
	if($Referer)
		fputs($fp, "Referer: $Referer\n");
	fputs($fp, "Connection: close\n\n");

	if ($Method == "POST")
		fputs($fp, $Data);
	
	while (!feof($fp)) {
		$buf .= fgets($fp,128);
	}
	fclose($fp);
	return $buf;	
}

// new function to get quotes. Above function is not working
function getFromHost($Host, $Method, $Path, $Data, $Referer=0) {
	$open = fopen('http://' . $Host . $Path . '?' . $Data, "r"); 
  $buf = trim(fread($open, 2000));
  fclose($open); 
	return $buf;
}

//to fetch stock price quote from yahoo's server  
function getQuote($symbol) {
	$rows = getFromHost("finance.yahoo.com", "GET", "/d/quotes.csv", "s=" . $symbol . "&f=sl1d1t1c1ohgv&e=.csv");

	$elements = explode(",", $rows);

	$quote = array("symbol" => substr(trim($elements[0]), 1, strlen(trim($elements[0])) - 2), "last" => $elements[1], "date" => substr(trim($elements[2]), 1, strlen(trim($elements[2])) - 2), "time" => substr(trim($elements[3]), 1, strlen(trim($elements[3])) - 2), "change" => $elements[4], "open" => $elements[5], "day_high" => $elements[6], "day_low" => $elements[7], "volume" => $elements[8]);
	
	return $quote;
}

//get the total cash user is having
function getTotalCash($login) {
	global $db, $CFG;
	
	$query = "select total_cash, temp_cash from user where login = '$login'";
	$result = $db->query($query);
	
	$row = $result->fetchRow();

	// for afterhours pending verification....more realistic view of available cash
	return $row->temp_cash;
}

//verify if user is having certain quantity of given share
function shareOwe($login, $symbol, $volume, $type) {
	global $db;

	/* Prohibiting partial buy to cover orders - because of volume chekck
	 * $query = "select volume from basket where login = '$login' and symbol = '$symbol' and volume = $volume and type = '$type'";
	 */
 	$query = "select sum(volume) as volume from basket where login = '$login' and symbol = '$symbol' and type = '$type'";
	errorlog($query);
	$result = $db->query($query);
	$row = $result->fetchRow();

	if($row->volume >= $volume) {
		return 1;
	} 
	else {
		return 0;
	}
}

//to do auto adjust in case cash is insuficient to buy the given amount of shares
function autoAdjust($login, $volume, $price) {
	
	errorLog("Autoadjusting");
	
	$cost = $volume * $price;
	$cash = getTotalCash($login);
	
	if($cost > $cash) {
		return round($cash/$price);
	}
	else {
		return $volume;
	}
}

//validate transaction
function validatetransaction($login, $symbol, $volume, $price, $transaction, $marketprice) {
	global $db;

	errorLog("Validating transaction for: $login, $symbol, $volume, $price, $transaction");
	
	if($transaction == "Sell Short") {
		$cost = $marketprice * $volume;
		//checking if user is having enough cash		
		if($cost < getTotalCash($login)) {
			errorLog("Valid transaction");
			return 1;
		}
		else {
			errorLog("Invalid transaction: Insufficient cash in hand");
			return "Insufficient cash in hand";
		}
	}
	else if($transaction == "Buy") {
		$cost = $marketprice * $volume;
		//checking if user is having enough cash
		if($cost < getTotalCash($login)) {
			errorLog("Valid transaction");
			return 1;
		}
		else {
			errorLog("Invalid transaction: Insufficient cash in hand");
			return "Insufficient cash in hand";
		}
	}
	else if($transaction == "Sell") {
		if(shareOwe($login, $symbol, $volume, "Long")) {
			errorLog("Valid transaction");
			return 1;
		}
		else {
			errorLog("Invalid transaction: You dont own sufficient quantity of given share");
			return "You dont own sufficient quantity of given share";
		}
	}
	else if($transaction == "Buy to Cover") {
		if(shareOwe($login, $symbol, $volume, "Short")) {
			errorLog("Valid transaction");
			return 1;
		}
		else {
			errorLog("Invalid transaction: You must have Sell Short $volume shares of $symbol for Buy to Cover");
			return "You must have Sell Short $volume shares of $symbol for Buy to Cover";
		}
	}
}

//execute the transaction, executed only after validating transaction
function dotransaction($transaction, $symbol, $volume, $price_type, $price_value, $marketprice, $term, $commission, $adjust, $login, $IC_user=0) {
	global $db, $DOCUMENT_ROOT;
	
	$query = "insert into transaction (transaction, symbol, volume, price_type, order_price, marketprice, term, commission, auto_adjust, login, order_date, IC_user) values('$transaction', '$symbol', '$volume', '$price_type', '$price_value', '$marketprice', '$term', '$commission', '$adjust', '$login', '" . time() . "', '$IC_user')";
	errorLog("Executing transaction: $transaction, $symbol, $volume, $price_type, $price_value, $term, $commission, $adjust, $login");
	$db->query($query);

	$total_cash = 0;
	if($transaction == "Buy") {
		$total_cash = -1 * $volume * $marketprice;
	}
	else if($transaction == "Sell") {
		$total_cash = $volume * $marketprice;
	}
	else if($transaction == "Buy to Cover") {
		$sell_shorted_price = getSellShortedPrice($login, $symbol);
		$total_cash = $volume * $sell_shorted_price + $volume * ($sell_shorted_price - $marketprice);
	}
	else if($transaction == "Sell Short") {
		$total_cash = -1 * $volume * $marketprice;
	}
	$total_cash = $total_cash - $commission;
	
	$query = "update user set temp_cash = temp_cash + $total_cash where login='$login'";
	$db->query($query);
	
	exec($DOCUMENT_ROOT . '/thegame/script/server.sh');
	return;
}

//to get sell shorted order price
function getSellShortedPrice($login, $symbol) {
	global $db;
	
	$query = "select price from basket where login = '$login' and symbol = '$symbol' and type = 'Short'";
	errorLog($query);
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->price;
}


//get all transactions of given user
function getUsertransactions($login, $pending=0, $id='') {
	global $db;
	// if set, get only pending transactions
	if ($pending == 1)
		$extrafilter = " and status = 0";
	if (!empty($id))
		$extrafilter .= " and id = '$id'";

	$query = "
		SELECT	*, ucase(symbol) as symbol
		FROM		transaction
		WHERE		login = '$login'
						$extrafilter
		ORDER BY id DESC";
	return $db->query($query);
}

function getUserCommission($login) {
	global $db;
	$query = "
		SELECT	sum(commission) as commission
		FROM	transaction
		WHERE	login = '$login'
				and status=1
	";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->commission;
}

//get user's portfolio at previous market close
function getPreviousPortfolio($login) {
	global $db;
	$query = "
		select 	*
		from 	portfolio
		where 	login = '$login'
				and date < '" . mktime(0,0,0,date("m"),date("d"),date("Y")) . "'
		order by date desc 
		limit 	0,1";
	return $db->query($query);
}

//get user's portfolio for previous week
function getPreviousWeekPortfolio($login) {
	global $db;
	
	$query = "";
	return $db->query($query);
}

//get all shares that user owns - that user bought or sold short
function getUsersShares($login) {
	global $db;
	$query = "
		select 	ucase(symbol) as symbol, sum(volume*price)/sum(volume) as price, sum(volume) as volume, type, count(symbol) * 10 as commission
		from 	basket 
		where 	login = '$login'
		group by symbol, type
	";
	return $db->query($query);
}

// get date of last portfolio update
function dateOfPortfolioUpdate() {
	global $db;
	$query = "
		SELECT	max(date) as lastdate
		FROM		portfolio";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->lastdate;
}

//get user's ranking
function getUserRanking($login) {
	global $db;
	$lastupdate = dateOfPortfolioUpdate();
	$query = "
		SELECT	a.login, total_equity
		FROM		portfolio a, user b
		WHERE		a.login=b.login
					AND a.date='$lastupdate' and b.IC_user=0
		ORDER BY	total_equity desc";
	$result = $db->query($query);
	
	$rank = 0;
	$lastresult = 0;
	for($i = 0; $i < $result->numRows() ; $i++) {
		$row = $result->fetchRow();
		// in case of tie, don't increment
		if ($row->total_equity != $lastresult)
			$rank++;
		if(stristr($row->login, $login))  {
			return $rank;
		}
		$lastresult = $row->total_equity;
	}
	return $rank;
}

//get team's ranking as a whole
function getTeamRanking($team) {
	global $db;

	$lastupdate = dateOfPortfolioUpdate();
	
	$query = "
		SELECT 	avg(a.total_equity) as point, b.country as team 
		FROM 		portfolio a, team b, user c 
		WHERE		a.date='$lastupdate'
					and a.login = c.login and c.team = b.country and c.IC_user=0
		GROUP BY b.country 
		ORDER BY point DESC";
	$result = $db->query($query);
	$rank = 0;
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		// in case of tie, don't increment
		if ($row->point != $lastresult) {
			$rank++;
		}
		if(stristr($row->team, $team)) {
			return $rank;
		}
		$lastresult = $row->point;
	}
	return 0;
}

//get continent's ranking as a whole
function getContinentRanking($continent) {
	global $db;

	$lastupdate = dateOfPortfolioUpdate();
	
	$query = "
		SELECT 	avg(a.total_equity) as point, b.continent
		FROM 		portfolio a, team b, user c 
		WHERE		a.date='$lastupdate'
					and a.login = c.login and c.team = b.country and c.IC_user=0
		GROUP BY b.continent
		ORDER BY point DESC";
	$result = $db->query($query);
	$rank = 0;
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		// in case of tie, don't increment
		if ($row->point != $lastresult) {
			$rank++;
		}
		if(stristr($row->continent, $continent)) {
			return $rank;
		}
		$lastresult = $row->point;
	}
	return 0;
}

//get total number of teams.  
// If active is requested, only show total # teams with members in them
function getTotalTeamNumber($active=1) {
	global $db;
	$lastupdate = dateOfPortfolioUpdate();

	if ($active)
		$query = "select count(distinct team) as num from user where length(team) > 0 and IC_user=0";
//			$query = "SELECT count(distinct b.country) as num FROM portfolio a, team b, user c WHERE a.date='$lastupdate' and a.login = c.login and c.team = b.country";
	else
		$query = "select count(*) as num from user where IC_user=0";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->num;
}

//get user's ranking within team
function getUserTeamRanking($login, $team) {
	global $db;
	$lastupdate = dateOfPortfolioUpdate();
	
	$query = "
		select 	total_equity, a.login 
		from 	user a, portfolio b
		where	a.login=b.login and team = '$team'
				and b.date = '$lastupdate' and a.IC_user=0
		order by total_equity desc";
	$result = $db->query($query);
	
	$rank = 0;
	$query .= 'login ' . $login;
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		// in case of tie, don't increment
		if ($row->total_equity != $lastresult) {
			$rank++;
		}
		$query .= 'thisone ' . $row-login . "\r\n";
		if(strtolower($row->login) == strtolower($login)) {
			return $rank;
		}
		$lastresult = $row->total_equity;
	}
}

function deleteUsertransaction($transaction_id, $login) {
	global $db;

	$detail = getUsertransactions($login, 1, $transaction_id);
	$detail = $detail->fetchRow();

	$total_cash = 0;

	if($detail->transaction == "Buy") {
		$total_cash = -1 * $detail->volume * $detail->marketprice;
	}
	else if($detail->transaction == "Sell") {
		$total_cash = $detail->volume * $detail->marketprice;
	}
	else if($detail->transaction == "Buy to Cover") {
		$sell_shorted_price = getSellShortedPrice($login, $detail->symbol);
		$total_cash = $detail->volume * $sell_shorted_price + $detail->volume * ($sell_shorted_price - $detail->marketprice);
	}
	else if($detail->transaction == "Sell Short") {
		$total_cash = -1 * $detail->volume * $detail->marketprice;
	}
	$total_cash = $total_cash - $detail->commission;

	$query = "update user set temp_cash = temp_cash - $total_cash where lower(login)='" . strtolower($login) . "'";
	$db->query($query);

	$query = "delete from transaction where id = $transaction_id";
	$db->query($query);
	return;
}

function createSortableLink($colname, $displayname, $currcol, $currdirection, $querystr, $class) {
	echo $PHP_SELF;
	if ($colname==$currcol && $currdirection=="desc") {
		$newdirection = "asc";
		$sortimg = "darrow.gif";
	}
	else {
		$newdirection = "desc";
		if ($colname == $currcol)
			$sortimg = "uarrow.gif";
	}
	if (isset($sortimg))
		$link = '<img src="../images/' . $sortimg . '" width="9" height="9">';
	else
		$link = '&nbsp;&nbsp;';
	$link .= '<a href="' . $PHP_SELF . "?$querystr&sort=$colname&direction=$newdirection" . '" class="' . $class . '">' . $displayname . "</a>";
	return $link;
}

function getFutureTeams() {
	global $db;
	
	$continent_name = array("Asia", "Africa", "Europe", "North America", "South America", "Australia Oceania", "Antarctica");

	$continent = array();

	$query = "
		SELECT 	a.continent, trim(lcase(b.futureteam)) as futureteam, count(futureteam) as players
		FROM 	team a, user b
		where 	b.futureteam = a.country and b.IC_user=0
		group by continent, futureteam
	";
	$result = $db->query($query);

	foreach ($continent_name as $cname) {
		$query = "
			select distinct 
				(case when substring(country, 1, 4) = 'Zone' and length(substring(country, 6, 7)) = 1 then
						Concat('Zone 0', substring(country, 6, 7)) 
				else 
					trim(lcase(country)) end) as country1,
			trim(lcase(country)) as country
			from 			team
			where 		continent = '$cname' 
			order by 	country1
		";
		$result2 = $db->query($query);
		for($i = 0; $i < $result2->numRows(); $i++) {
			$row = $result2->fetchRow();
			$continent[$cname][$row->country] = '0';
		}
	}

	// insert player counts into team array
	for($i = 0; $i < $result->numRows(); $i++) {
		$row = $result->fetchRow();
		$continent[$row->continent][$row->futureteam] = $row->players;
	}

	return $continent;
}

function getFutureMembers($team) {
	global $db;

	$query = "
		SELECT	*
		FROM		user a
		WHERE		futureteam = '$team' and IC_user=0
		ORDER BY login";
	$result = $db->query($query);
	return $result;
}

function getCurrentCompetition() {
	global $db;

	$query = "
		SELECT	*
		FROM		competition
		ORDER BY compid desc
		LIMIT 1";
	$result = $db->query($query);
	return $result->fetchRow();
}

// get information for next competition.
// auto increment id, so can use current id and add 1 to get next id
// start & end date based on monthly competitions.  So next competition will begin the 1st
// day of next month, and will end on the last day of next month.
// return array of information
function getFutureCompetition($jumpahead=1) {
	// get future competition id
	$currentCompetition = getCurrentCompetition();
	$result['compid'] = $currentCompetition->compid + $jumpahead;
	
	// get start/end date
	$startdateTS = mktime (0,0,0,date("m")+$jumpahead,1,date("Y"));
	$result['startdate'] = date("M d, Y", $startdateTS);
	$nextmonthTS = mktime (0,0,0,date("m", $startdateTS)+1,1,date("Y"));
	$result['enddate'] = date("M d, Y", mktime (0,0,0,date("m", $nextmonthTS),date("d", $nextmonthTS)-1,date("Y"))); 

	return $result;
}

function getMarketStatus() {
	global $db;

	$query = "
		SELECT	*
		FROM	marketopen
	";
	$result = $db->query($query);
	$row = $result->fetchRow();
	return $row->status;
}
?>
