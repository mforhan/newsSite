<?php
 include("newapp.inc.php");
 $site="4";
 $section="Front Page";
 // $section="Sports & Outdoors";
 // $section = "Community";
?>
<html>
 <head>
  <title><?= $THEME->site_title ?></title>
  <LINK REL="stylesheet" type="text/css" href="page_test.css"/>
  <style type="text/css" media="screen">
  <? include($THEME->screen_style); ?>
  </style>
<script type="text/javascript">
<!--//
function init() {
  if(window.Event) {
    document.captureEvents(Event.MOUSEMOVE);
  }
  document.onmousemove = getXY;
}
function getXY(e) {
  xCoor = (window.Event) ? e.pageX : event.clientX;
  yCoor = (window.Event) ? e.pageY : event.clientY;
}
function showToolTip() {
  valX = yCoor;//-120;//+30;
  valY = xCoor+25;//+30;//-30;
  document.all.tooltip.style.top = valX+"px";
  document.all.tooltip.style.left = valY+"px";
  document.all.tooltip.style.visibility='visible';
}
function hideToolTip() {
  document.all.tooltip.style.visibility='hidden';
}
window.onload = init;
-->
</script>
 </head>
 <body>
 <table border="0" width="800">
  <tr height="100" class="topCol">
   <td align="center" colspan="3">
    <img src="<?= $THEME->logo ?>">
</td>
  </tr>
  <tr height="25" class="topCol">
   <td><p style="font:10pt/12pt Verdana,Arial,Sans-serif;padding-left:10px;">
   <?= date("F j, Y"); ?>
    </p></td>
   <td colspan="2" align="right"><span style="font:10pt/10pt Verdana,Arial,Sans-serif;padding-right:10px;">Volume 103 &bull; Issue <?= date("W"); ?></span></td>
  </tr>
  <tr>
<!-- Start Nav Bar -->
   <td valign="top" width="150" class="leftCol">
   <? include('newnav.html'); ?>
   </td>
<!-- End Nav Bar -->
<!-- Start Content -->
   <td>
<?php
 $content = retrieve_content($site,$section);
 unset($data);
 while($data = $content->fetchRow()) {
    $article_id = $data->article_id;
    $headline = $data->headline;
    $subhead = $data->subhead;
    $story = $data->body;
    $author_name = $data->author_name;
    $author_title = $data->author_title;
    $hasPhoto = $data->hasPhoto;
 }
 if($hasPhoto) {
    $photo = get_photos($article_id);

    // while($data = $photo->fetchRow()) {
    $data = $photo->fetchRow();
       $path = $data->path;
       $group_id = $data->photo_group_id;
       $cutline = $data->cutline;
       $photoby = $data->photoby;
       $width = $data->width;
       $height = $data->height;
    // }
    if($group_id) {
      $icon = "<a id=\"mPhotos\" target=\"_blank\" onClick='javascript:window.open(\"viewPhoto.php?article_id=$article_id\",\"\",\"scrollbars=yes,toolbars=no,width=320px,height=450px\");'><img src=\"images/morePhotos.gif\" width=\"20\" height=\"20\" style=\"margin-left:5px;margin-top:5px;cursor:pointer;\" alt=\"click here for more photos\" onMouseOver=\"showToolTip();\" onMouseout=\"hideToolTip();\"></a>";
      $tooltip = "<div id=\"tooltip\"><p>click to see more photos</p></div>";
    } else {
      $icon = NULL;
      $tooltip = NULL;
    }
 }
?>
<!-- Start story -->
<div id="content">
<h1><?= $headline ?></h1>
<h4><?= $subhead ?></h4>
<? if($hasPhoto && $article_id) { ?>
<!-- Start Photo -->
<div id="photo">
<img src="<?= $path ?>" width="<?= $width ?>" height="<?= $height ?>">
<?= $icon ?><?= $tooltip ?><p class="byline"><?= $photoby ?></p>
<p><?= $cutline ?></p>
</div>
<!-- End Photo -->
<? } ?>
<p class="byline"><?= $author_name ?></p>
<p class="byline"><?= $author_title ?></p>
<p><?= $story ?></p>
</div>
<!-- End story -->
   </td>
<!-- End Content -->
<!-- Start Right Bar -->
   <td valign="top" width="150" class="rightCol">
     &nbsp;    
   </td>
<!-- End Right Bar -->
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
