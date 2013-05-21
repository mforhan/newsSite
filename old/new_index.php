<?php
 include("application.inc.php");
 $npage = "1";

function retrieve_new_content($section,$number,$style) {
  global $db;

  if(!$section) {
    $section = 1;
  }

  if(!$number) {
    $number = 1;
  }

  // FULL Style is the full story, COMPACT is only the first 200 words
  if(!$style && ($number == 1)) {
    $style = 'FULL';
  } else if (!$style) {
    $style = 'COMPACT';
  }

  $num_of_items = $number;
 
  $switchsql = "SELECT article_id, hasPhoto
                  FROM content
                 WHERE section_id='$section'
                   AND isActive=1
              ORDER BY date_stamp DESC
                 LIMIT $num_of_items";

  $switchresults = $db->query($switchsql); 
  while($dataswitch = $switchresults->fetchRow()) {
    $article = $dataswitch->article_id;
    $switch = $dataswitch->hasPhoto;

  
    if((int) $switch == 1)  {
      $sql = "SELECT a.article_id as article_id, a.section_id as section_id, 
                     a.title as headline, a.subtitle as subhead, a.body as story,
                     date_format(a.date_stamp,'%M %D, %Y') as date,
                     b.real_name as byline, b.title as authortitle,
                     c.path as path, c.cutline as cutline, c.width as width, c.height as height,
                     d.real_name as photoby
                FROM content as a, user as b, webphoto as c,
                     user as d
               WHERE a.author_id=b.user_id
                 AND a.article_id=c.article_id
                 AND a.article_id=$article
                 AND c.photoby=d.user_id
                 AND a.section_id='$section'
                 AND a.isActive=1
                 AND c.isActive=1
            ORDER BY a.date_stamp
               LIMIT $num_of_items";
    } else {
      $sql = "SELECT a.article_id as article_id, a.section_id as section_id,
                     a.title as headline, a.subtitle as subhead, a.body as story,
                     date_format(a.date_stamp,'%M %D, %Y') as date,
                     b.real_name as byline, b.title as authortitle
                FROM content as a, user as b
               WHERE a.author_id=b.user_id
                 AND a.article_id=$article
                 AND a.section_id='$section'
                 AND a.isActive=1
            ORDER BY a.date_stamp
               LIMIT $num_of_items";
    }
    // echo $sql;
    $results = $db->query($sql); 
    $final[] = $results;
  }
    return $final;
}

?>
<html>
 <head>
  <title><?= $THEME->site_title ?></title>
  <style type="text/css" media="screen">
  <? include($THEME->screen_style); ?>
  </style>
 </head>
 <body bgcolor="#123">
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
  <div id=vertnav style="margin-bottom:15px;">
  <ul>
   <? generate_nav(); ?>
  </ul>
  </div>
  </td>
<!-- End Nav Bar -->
<!-- Start Content -->
 <td valign=top id=content class="midCol"> 
<? $content = retrieve_new_content($npage,5,'COMPACT');
while($sql_res = array_shift($content)) {
    while($data = $sql_res->fetchRow()) {
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
    $article_id = $data->article_id;
    $story01 = stripslashes($story01);
    // This regular expression reduces the story to
    // a couple of sentences, defined by {x}
    ereg('(.[^\.]+\.\ *[0-9]*.[^\.]*\.){2}',$story01,$sentence); 
    $story01 = $sentence[0];
?>
  <h4 style="font:xx-small;text-transform:uppercase"><?= $fulldate ?></h4>
  <h2 style="font:x-large/1.5em Georgia,Verdana,Arial,Sans-serif;text-transform:uppercase;margin:2px;"><?= $headline ?></h2>
  <h3 style="font:Georgia,Verdana,Arial,Sans-serif;margin:0px;"><i><?= $subhead ?></i></h3>
  <p style="color:gray;font:x-small;">
  <?= $byline ?><br/>
  <?= $title ?>
  </p>

<? if($image) { ?>
  <table cols=1 rows=2 style="width:250px;float:right;">
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
  <hr style="clear:both;"/>
   <a href="index.php?article_id=<?= $article_id ?>">Read More</a>
  <hr/>
<? }} ?>
</td>
<!-- End Content -->
  <td valign=top class="rightCol">
    <?= $sidebar_data ?>
  </td>
 </tr>
</table>
 </body>
</html>
