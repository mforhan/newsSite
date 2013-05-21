<?
//this function auto submit
//SendToHost('www.google.com','get','/search','q=php_imlib');
//SendToHost('www.example.com','post','/some_script.cgi','param=First+Param&second=Second+param');


$PROXY[Enable] = 1;
$PROXY[Host] = "192.168.0.1";
$PROXY[Port] = 8080;
$PROXY[User] = "";
$PROXY[Pass] = "";

$LOG_POST = 0;
$DEBUG_POST = 1;

function LogPost($str) {
	global $CFG;
	$fp = fopen($CFG->dirroot . "post.log", "a");
	if($fp)
		echo "File opened successfully: " . "post.log<br>";
	else 
		echo "File couldnt be opened<br>";
	$date = date("Y-m-d H:i:s");
	echo fwrite($fp, "\n-------------------------------------------\n");
	echo fwrite($fp, "Date: $date\n");
	fwrite($fp, $str);
	fwrite($fp, "\n-------------------------------------------\n");
	fflush($fp);
	fclose($fp);
	return;
}

function SendToHost($Host, $Method, $Path, $Data, $Referer=0) {
	global $PROXY, $DEBUG_POST, $LOG_POST;
	
	if($LOG_POST) 
		LogPost("URL: [$Host]\nMethod: [$Method]\nPath: [$Path]\nData: [$Data]\nReferer: [$Referer]\n");
	if($DEBUG_POST)
		//echo "<br><br><b>Data being posted</b><br>URL: -$Host-<br>  Method: -$Method-<br>  Path: -$Path-<br> Data: -$Data-<br> Referer: -$Referer-<br><br><br>";
	
	if($PROXY[Enable] == 1) {
		$Proxy = 1;
		$Realm = base64_encode($PROXY[User] . ":" . $PROXY[Pass]);
	}

	if($Proxy)
		$fp = fsockopen($PROXY[Host], $PROXY[Port], $errno, $errstr, 10);
	else
		$fp = fsockopen($Host, 80, $errno, $errstr, 1);

	if(!$fp) {
		echo "\n\nCouldnt connect\n";
		echo "$errno:$errstr\n\n";
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
	
	//returning output
	if($DEBUG_POST) {
		//echo "\n\nServer Response\n\n";
		while (!feof($fp)) {
			$buf .= fgets($fp,128);
			}
		fclose($fp);
		return $buf;
	}
	else {
		fclose($fp);
		return;
	}
}

function getQuote($symbol) {
	$rows = SendToHost("finance.yahoo.com", "GET", "/d/quotes.csv", "s=" . $symbol . "&f=sl1d1t1c1ohgv&e=.csv");
	$rows = explode("\r\n\r\n", $rows);
	$rows = explode("\r\n", $rows[1]);
	$elements = explode(",", $rows[0]);

	$quote = array("symbol" => substr(trim($elements[0]), 1, strlen(trim($elements[0])) - 2), "last" => $elements[1], "date" => substr(trim($elements[2]), 1, strlen(trim($elements[2])) - 2), "time" => substr(trim($elements[3]), 1, strlen(trim($elements[3])) - 2), "change" => $elements[4], "open" => $elements[5], "day_high" => $elements[6], "day_low" => $elements[7], "volume" => $elements[8]);
	
	return $quote;
}

$rows = SendToHost("finance.yahoo.com", "GET", "/d/quotes.csv", "s=" . $_GET[s] . "&f=sl1d1t1c1ohgv&e=.csv");

$rows = explode("\r\n\r\n", $rows);

$rows = explode("\r\n", $rows[1]);

print_r(getQuote("MSFT"));

echo "<table cellspacing=0 cellpadding=3 border=1>";
echo "<tr><td>Symbol</td><td>Last Trade</td><td>Date</td><td>Time</td><td>Change</td><td>Open</td><td>Day High</td><td>Day Low</td><td>Volume</td></tr>";
for($i = 0; $i < count($rows); $i++) {
	//echo $rows[$i] . "<br>";
	$row = explode(",", $rows[$i]);
	echo "<tr>";
	for($j = 0; $j < count($row); $j++) {
		echo "<td>" . $row[$j] . "</td>";
	}
	echo "</tr>";
}
echo "</table>";
?>
