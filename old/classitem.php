<?php
 include("application.inc.php");
 
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
<html>
 <head>
  <title>Online Classifieds</title>
 <style type="text/css">
body {
  background-color:#123;
}
p {
  color:black;
  font-size: 12px;
  line-height: 16px;
  text-align:justify;
  font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
  margin-top:0px;
}
.photoby {
  text-align:right;
  color:gray;
  font-size:9px;
  line-height:9px;
  margin-right:0;
  font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
  text-transform:capitalize;
  margin-bottom:5px;
}
.cutline {
  color: black;
  font-size: 10px;
  line-height: 11px;
  text-transform:capitalize;
  /* text-transform:uppercase; */
  margin-bottom:5px;
  padding-bottom:10px;
  /* border-bottom: 1px solid; */
  font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
}
#flag {
  width:695px;
  background:#fff;
  margin-bottom:10px;
  text-align:right;
}
#poll {
  background-color:#888;
  width:100%;
  padding-top:5px;
  padding-bottom:5px;
  border-top:1px dotted;
  border-bottom:1px dotted;
}
#content {
  width:500px;
  padding:5px;
  padding-top:10px;
  background-color:#ddd;
}
#vertnav {
  background:#cdf;
  padding:5px;
  border-top:1px dotted;
  border-bottom:1px dotted;
}
#vertnav ul {
  width:125px;
  padding-left:10px;
  list-style:none;
  /* list-style-image:url(images/newspaper.gif); */
  text-align:left;
  font:10pt/15pt Verdana,Arial,Sans-serif;
}
#vertnav a {
  width:100px;
  color:#000;
  text-decoration:none;
}
 </style>
 </head>
 <body bgcolor="#123">
 <table style="width:800px;" cols=3 cellpadding=0>
<!-- Start Logo Bar -->
  <tr>
   <td align=center colspan=3 style="background-color:#fff;">
    <!--<img src="<?= $THEME->logo ?>">-->
     <h1 style="margin-top:25px;margin-bottom:25px;" valign=center><i>Okanogan Valley Gazette-Tribune</i></h1>
   </td>
  </tr>
<!-- End Logo Bar -->
<!-- Start Nav Bar -->
  <tr>
   <td valign=top style="width:150px;padding-top:10px;background-color:#aaa;">
  <div id=vertnav>
  <ul>
   <? generate_nav(); ?>
  </ul>
  </div>
  </td>
<!-- End Nav Bar -->
<!-- Start Content -->
 <td valign=top id=content> 
   <? include($item); ?>
</td>
<!-- End Content -->

  <td valign=top style="width:150px;padding-top:10px;background-color:#aaa;">
    <?= $sidebar_data ?>
  </td>
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
