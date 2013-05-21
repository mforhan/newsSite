<?php
 include('newapp.inc.php');
/* [Wed Jun 14 15:06:58 2006] [error] PHP Warning:  opendir(/home/httpd/vhosts/leavenworthecho.com/httpdocs/content/classG): failed to open dir: No such file or directory in /home/httpd/vhosts/leavenworthecho.com/httpdocs/classifieds.php on line 98
*/
 unset($item);

 if( $HTTP_POST_VARS || $HTTP_GET_VARS ) {
   // List possible items that could be passed
   // and assign them for variables for full processing
   if( $_SERVER['_POST'] ) { 
     // possible passed vars
     $item = $HTTP_POST_VARS['class'];
   } else {
     $item = $HTTP_GET_VARS['class'];
   }
 } 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
  <title>The Leavenworth Echo Classifieds</title>
  <link rel="stylesheet" href="/css/wide_style.css" type="text/css" />
<style type="text/css" id="overrideStyle">
#content p {
  color:black;
}
#content p.break {
  height:0px;
  margin:0px;
  margin-top:10px;
}
#content a {
  font:10pt Verdana;
  color:#007;
}
</style>
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
   <img src="images/logo3.jpg" alt="The Leavenworth Echo Webite Logo"/>
  </div>
  <div id="nav">
   <? include("horzNav.php"); ?>
  </div>
  <div id="content">
  <!-- Start Advertising Box -->
    <div id="adverts">

      <div class="ad">
       <h1>Sonnenschein auf Leavenworth</h1>
       <p>Your source for information on Tourist Activities, Festivals and More in North Central Washington</p>
      </div>

    </div>
  <!-- End Advertising Box -->
  <p class="break">&nbsp;</p>
<? if($item) {
    // include($DOCUMENT_ROOT."/".$item);
    // $CFG->classdir
    // include($CFG->dirroot."/".$item);
    include($CFG->dirroot."/content/class/".$item.".html"); ?>
<? } else { ?>
   <h1>Classifieds</h1>
   <p>
   Click the Back Button on your browser to return to this page<br/>
   Classifieds are updated every Thursday no later than 5pm<br/>
   <a href="http://www.lakechelanmirror.com/classified.php">Click here</a> to see Lake Chelan area classifieds<br/>
   <a href="http://www.gazette-tribune.com/classified.php">Click here</a> to see the Okanogan Valley classifieds<br/>
   </p>
   <hr>
 <?php
   // We need to read the content/class folder and generate links for the
   // Classifieds in that section
   $classifieds = $CFG->classdir; 
   // $classifieds = "content/class";
   if( $dir = opendir($classifieds) ){
     while($file = readdir($dir)) {
      // $fullname = $classifieds.'/'.$file;
      $fullname = $CFG->classlink.'/'.$file;
      if($file == '.'  || 
         $file == '..' ||
         $file == 'index.html') {
       continue;
      }
      if(is_dir($CFG->classdir.'/'.$file)) {
       continue;
      }
      // ereg('[0-9]+',$file,$section);
      $section = substr($file,0,3);
      // $string = $section[0];
      $name = getclassbyID($section);
      if($item) {
        print "<a style=\"font-size:9pt;\" href=\"classified.php?class=$section\">$section $name</a><br/>";
      } else {
        print "<a href=\"classified.php?class=$section\">$section $name</a><br/>";
      }
      
     }
     closedir($dir);
   }
 } ?>


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
 </body>
</html>
