<?php
 include("newapp.inc.php");
 $site="4";
 // $section="Front Page";
 // $section="Sports & Outdoors";
 $section = "Community";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
  <title><?= $THEME->site_title; ?></title>
  <link rel="stylesheet" href="/css/wide_style.css" type="text/css" />
<script type="text/javascript">
<!--//

function init() {
  resize = setInterval('fixHeights()',250);
}
function fixHeights() {
  contSize = document.getElementById('content');
  if(!contSize) {
    return false;
  }
  advert = document.getElementById('adverts');

  var prevHeight;
  var count = 0;
  heightVal = contSize.offsetHeight;

  while(prevHeight != heightVal) {
    heightVal = contSize.offsetHeight;
    advert.style.height = heightVal+"px";
    prevHeight = heightVal;
    heightVal = contSize.offsetHeight;
    count++;
    if(count == 20) {
      break;
    }
  }
  clearInterval(resize);
  return true;
}
window.onLoad = init();
-->
</script>
 </head>
 <body>
  <div id="topCol">
   <img src="images/logo3.jpg" alt="The Leavenworth Echo Website Logo"/>
  </div>
  <div id="nav">
   <? include("horzNav.php"); ?>
  </div>
  <div id="content">
  <!-- Start Advertising Box -->
    <div id="adverts">
      <? include('ads.php'); ?>
    </div>
  <!-- End Advertising Box -->
  <? include("content.php"); ?>
  </div>
  <!-- End Content -->
  <!-- Start Footer -->
  <div id="footer">
   <p>All content copyright &copy; 2006 Prairie Media Inc. All rights reserved.</p>
  </div>
  <!-- End Footer -->
  <!-- Start Final Page Items -->
  <!--<div id="valid">
    <p><a href="http://validator.w3.org/check?uri=referer"><img
        style="border:0;width:88px;height:31px"
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" /></a>
      <a href="http://jigsaw.w3.org/css-validator/"><img 
       style="border:0;width:88px;height:31px"
       src="http://jigsaw.w3.org/css-validator/images/vcss" 
       alt="Valid CSS!" /></a>
  </p>
  </div> -->
  <!-- End Final Page Items -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-339325-1";
urchinTracker();
</script>
 </body>
</html>
