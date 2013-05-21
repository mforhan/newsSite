<?php
 include("application.inc.php");

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
 // $item = urlencode($item);
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
<? if($item) {
    // include($DOCUMENT_ROOT."/".$item);
    // $CFG->classdir
    // include($CFG->dirroot."/".$item);
    include($CFG->dirroot."/content/class/".$item.".html"); ?>
   </td>
   <td valign=top class="rightCol">
<? } else { ?>
   <h2>Classifieds</h2>
   <p style="text-align:left;text-indent:none;">
   Click the Back Button on your browser to return to this page<br/>
   Classifieds are updated every Thursday no later than 5pm<br/>
   <a style="color:#009;" href="http://www.gazette-tribune.com/classified.php">Click here</a> to see the Okanogan Valley classifieds<br/>
   <a style="color:#009;" href="http://www.lakechelanmirror.com/classified.php">Click here</a> to see Lake Chelan area classifieds<br/>
   </p>
   <hr>
<? } ?>
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
 ?>

</td>
<!-- End Content -->

<? if(!$item) { ?>
   <td valign=top class="rightCol">
    <?= $sidebar_data ?>
   </td>
<? } ?>
 </tr>
</table>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-339325-1";
urchinTracker();
</script>
 </body>
</html>
