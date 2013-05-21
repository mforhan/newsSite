<?
/*
 **** Application include file ****
 *@param  To setup all database connections to start
 *@param  a session. CFG contains all of the initial
 *@param  information to connect to a database.
 *@param  Designed to function with PDO to MySQL
 *@global $db as global variable
 *@class  $CFG contains the initial database & server variables
 *@access public
 *@author: Michael Forhan
 *@date: 05/19/2013
 */

class object {};
$CFG = new object;

  // Database Config
$CFG->dbhost = "localhost";
$CFG->dbport = "";
$CFG->dbname = "prairie_media";
$CFG->dbuser = "pmedia";
$CFG->dbpass = "nc139b6";

  // Path Variables for links
$CFG->webserver = "";
$CFG->webport = "80";
$CFG->dirroot = $_SERVER["DOCUMENT_ROOT"];
$CFG->dirrootlink = "$CFG->webserver";
$CFG->libdir = "$CFG->dirroot/lib";
$CFG->liblink = "$CFG->dirrootlink/lib";
$CFG->imagelink = "$CFG->dirrootlink/images";
$CFG->imagedir = "$CFG->dirroot/images";
$CFG->csslink = "$CFG->dirrootlink/css";
$CFG->cssdir = "$CFG->dirroot/css";

//starting session
session_start();

//required library files 
// require_once($CFG->libdir."/DB.php");
//require_once("DependencyDB.php");
require_once("DependencyDB.php");
// require_once("lib/DB.php");
// require_once($CFG->libdir."/competition.inc.php");
// require_once($CFG->libdir."/user.inc.php");
// require_once($CFG->libdir."/admin.inc.php");
// require_once($CFG->libdir."/class.phpmailer.php");
// require_once($CFG->libdir."/class.smtp.php");

//theme configuration
// echo "$CFG->libdir /theme.inc.php";
// require_once($CFG->libdir."/theme.inc.php");
require_once("lib/theme.inc.php");
require_once("lib/newcontent.inc.php");

//configure variables
$CFG->title = "Cashmere Valley Record";
$CFG->ADMIN_EMAIL = "pmedia@gazette-tribune.com";
$CFG->ADMIN_NAME = "Prairie Media Webmaster";
$CFG->SMTP_SERVER ="localhost";

// Module Config
 // classified files
$CFG->classlink = "$CFG->dirrootlink/content/class";
$CFG->classdir = "$CFG->dirroot/content/class";

//$CFG->FORGET_PASSWORD_SUBJECT= "";
//$CFG->FORGET_PASSWORD_BODY="";
//$CFG->REGISTRATION_SUBJECT="Prairie Media Registration Confirmation Letter";
//$CFG->REGISTRATION_BODY="";

//site administrator password
$CFG->ADMIN_LOGIN = "admin";
$CFG->ADMIN_PASSWORD = "admin";

//whether log or not
$CFG->ERROR_LOG = 1;

//Database Connection
try {
  $db=new PDO("mysql:host=$CFG->dbhost;dbname=$CFG->dbname",$CFG->dbuser,$CFG->dbpass);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo 'ERROR: ' . $e->getMessage();
}

// To set the fetchmode for fetchrow()
// $db->setFetchMode(DB_FETCHMODE_OBJECT);

//to verify between a error or a valid connection.

//array for all the states of US
$state=array("Alabama","Alaska","Arizone","Arkansas","California","Colorado","Connecticut","Delaware","District of Columbia","Florida","Georgia",
             "Hawaii","Idaho","Illinois","Indiana","Iowa","Kanas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi",
             "Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon",
             "Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming");
$province = array("Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland", "Northwest Territories", "Nova Scotia", "Nunavut", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan", "Yukon Territory");

?>
