<?php
 include("application.inc.php");
 $npage = "7";
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
  <h2 style="font:x-large/1.5em Georgia,Verdana,Arial,Sans-serif;text-transform:uppercase;margin:2px;"><?= $headline ?></h2>
  <h3 style="font:Georgia,Verdana,Arial,Sans-serif;margin:0px;"><i><?= $subhead ?></i></h3>
  <p style="color:gray;font:x-small;">
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

</td>
<!-- End Content -->

  <td valign=top class="rightCol">
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
