<?
/*
 **** Theme include file ****
 *@param  Listing of all files used in a theme so that the webpage
 *@param  can be customized by changing this file.
 *@global $db as global variable
 *@class  $THEME contains the initial variables
 *@access public
 *@author: Xaeridus
 *@date: 10/01/04
 */

$THEME = new object;

// Sample Variable
// $THEME->varname = "value";
$THEME->screen_style = "$CFG->cssdir/style.css";
$THEME->print_style  = "$CFG->cssdir/print.css";

// Flag (Header)
$THEME->site_title = "The Leavenworth Echo";
$THEME->logo = "$CFG->imagelink/logo3.jpg";
$THEME->site_url = "http://www.leavenworthecho.com/";

// Left Column Items


// Right Column Items


// Content Area Items

// CSS Style Colors

$THEME->story_color = 'black';
$THEME->photoby_color = 'gray';
$THEME->cutline_color = 'black';
$THEME->flag_bcolor = '#fff';
$THEME->flag_color = 'black';
$THEME->content_area_bcolor = '#ddd';
$THEME->nav_bcolor = '#cdf';
$THEME->nav_linkcolor = '#000';
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;
// $THEME->BLANK;

?>
