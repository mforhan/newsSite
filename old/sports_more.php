<?php
 include("application.inc.php");
 $npage = "3";

 unset($rss);

 if( $HTTP_POST_VARS || $HTTP_GET_VARS ) {
   // List possible items that could be passed
   // and assign them for variables for full processing
 
   if( $_SERVER['_POST'] ) { 
     // possible passed vars
     $rss = $HTTP_POST_VARS['rss'];
   } else {
     $rss = $HTTP_GET_VARS['rss'];
   }
 } 
?>
<html>
 <head>
  <title><?= $THEME->site_title ?></title>
  <style type="text/css" media="screen">
  <? include($THEME->screen_style); ?>
  </style>
 </head>
 <body>
 <table class="masterArea" cols=3 cellpadding=0>
<!-- Start Logo Bar -->
  <tr>
   <td align=center colspan=3 class="topCol">
    <img src="<?= $THEME->logo ?>">
   </td>
  </tr>
<!-- End Logo Bar -->
<!-- Start Nav Bar -->
  <tr>
   <td valign=top class="leftCol">
  <div id=vertnav>
  <ul>
   <? generate_nav(); ?>
  </ul>
  </div>
  </td>
<!-- End Nav Bar -->
<!-- Start Content -->
 <td valign=top id=content class="midCol"> 
<? $content = retrieve_content($npage,1,'FULL');
unset($data);
while($data = $content->fetchRow()) {
    $headline = $data->headline;
    $subhead = $data->subhead;
    $story01 = $data->story;
    $fulldate = $data->date;
    $byline = $data->byline;
    $title = $data->authortitle;
    $image = $data->path;
    $cutline = $data->cutline;
    $width = $data->width;
    $height = $data->height;
    $photoby = $data->photoby;
    $section_id = $data->section_id;
  }
?>
  <h4 style="font:xx-small;text-transform:uppercase"><?= $fulldate ?></h4>
  <h2 style="font:x-large/1.5em Georgia,Verdana,Arial,Sans-serif;text-transform:uppercase;margin:2px;"><?= $headline ?></h2>
  <h3 style="font:Georgia,Verdana,Arial,Sans-serif;margin:0px;"><i><?= $subhead ?></i></h3>
  <p style="color:gray;font:x-small;">
  <?= $byline ?><br/>
  <?= $title ?>
  </p>

<? if($image) { ?>
  <table cols=1 rows=2 style="width:250px;float:right;border-bottom:1px solid;">
   <tr><td><img src="<?= $image ?>" width="<?= $width ?>" height="<?= $height ?>"></td></tr>
   <tr><td>
    <p class="photoby">
    <!--<p style="font-size:0.75em;color:#aaa;text-align:right;margin-bottom:5px;">--><?= $photoby ?></p>
   <!--<p style="font-size:0.5em;text-transform:uppercase;margin:5px;">-->
   <p class="cutline">
   <?= $cutline ?>
   </p>
    </td></tr>
  </table>
<? } ?>

  <?= $story01 ?>

<? if($image02) { ?>
  <table cols=1 rows=2 style="width:250px;float:left;border-bottom:1px solid;">
   <tr><td><?= $image_02 ?></td></tr>
   <tr><td>
    <p style="font-size:0.75em;color:#aaa;text-align:right;margin-bottom:5px;"><?= $photoby_02 ?></p>
   <p style="font:xx-small;text-transform:uppercase;margin:5px;">
    <?= $cutline_02 ?>
   </p>
    </td></tr>
  </table>
  <p/>

 <?= $story02 ?>
<? } ?>

 <hr>
 <p style="text-align:right">
 [Print] [Email] <a href="pdfstory.php?section=<?= $section_id ?>">[Save as PDF]</a>
 </p>
</td>
<!-- End Content -->

  <td valign=top class="rightCol">
    <?= $sidebar_data ?>
<!-- Start RSS -->
<?php
// Code from SitePoint's article on Parsing RSS 1.0
// http://www.sitepoint.com/article/php-xml-parsing-rss-1-0

// globals
$insideitem = false;
$insideimg = true;
$tag = "";
$title = "";
$description = "";
$link   = "";
$width  = "";
$height = "";
$url    = "";
$counter = 0;
$maxcnt  = 10;


// Create an XML parser
$xml_parser = xml_parser_create();

// Set the functions to handle opening and closing tags
xml_set_element_handler($xml_parser,"startElement","endElement");

// Set the function to handle blocks of character data
xml_set_character_data_handler($xml_parser,"characterData");

// Open the XML file for reading
// $fp = fopen("http://www.sitepoint.com/rss.php","r") or die("Error reading RSS data.");
// $fp = fopen("http://rss.news.yahoo.com/rss/us","r") or die("Error reading RSS data.");
if($rss) {
$fp = fopen($rss,"r") or die("Error reading RSS data.");
} else {
 die("No RSS Feed specified");
}

// Read the XML file 4KB at a time
while($data = fread($fp,4096)) {
  // Parse each 4KB chunk with the XML parser created above
  xml_parse($xml_parser,$data,feof($fp))
    // Handle errors in parsing
    or die(sprintf("XML error: %s at line %d",
       xml_error_string(xml_get_error_code($xml_parser)),
       xml_get_current_line_number($xml_parser())));
}

// Close the XML file
fclose($fp);

// Free up memory used by the XML parser
xml_parser_free($xml_parser);

function startElement($parser,$tagName,$attrs) {
  global $insideimg, $insideitem, $tag;
  if($insideitem || $insideimg) {
    $tag = $tagName;
  } else {
    switch($tagName) {
      case "ITEM":
        $insideitem = true;
        $insideimg  = false;
        break;
      case "IMAGE":
        $insideitem = false;
        $insideimg  = true;
        break;
    }
  }
}

function characterData($parser,$data) {
  global $insideimg, $insideitem, $tag, $title, $description, $link, $width, $height, $url;


  if($insideitem) {
    switch ($tag) {
      case "TITLE":
        $title .= $data;
        break;
      case "DESCRIPTION":
        $description .= $data;
        break;
      case "LINK":
        $link .= $data;
        break;
    }
  } else if($insideimg) {
    switch ($tag) {
      case "WIDTH":
        $width .= $data;
        break;
      case "HEIGHT":
        $height .= $data;
        break;
      case "URL":
        $url .= $data;
        break;
    }
  }
}

function endElement($parser,$tagName) {
  global $insideimg, $insideitem,$tag,$title,$description,$link,$width,$height,$url,$counter,$maxcnt;
  if($counter <= $maxcnt) {
    if($tagName == "IMAGE") {
      if($url) {
        printf("<p><img src='%s' width='%s' height='%s'></p>",trim($url),trim($width),trim($height));
      }
      $url = "";
      $width = "";
      $height = "";
      $insideimg = false;
      $counter++;
    }
    if($tagName == "ITEM") {
      printf("<p style=\"font-size:12px;font-align:left;\"><b><a href='%s'>%s</a></b></p>",trim($link),htmlspecialchars(trim($title)));
      printf("<p style=\"font-size:10px;font-align:left;\">%s</p>",htmlspecialchars(trim($description)));
      $title = "";
      $description = "";
      $link = "";
      $insideitem = false;
      $counter++;
    }
  }
}

?>
<!-- End RSS -->
  </td>
 </tr>
</table>
 </body>
</html>
