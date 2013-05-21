<?php
 $pagetitle = "The Leavenworth Echo Slideshow";
 // include('application.inc.php');
 include("newapp.inc.php");
 include("lib/slideshow.inc.php");

 if($HTTP_GET_VARS) {
   $article_id = $HTTP_GET_VARS['article_id'];
   $offset = $HTTP_GET_VARS['photo'];
 } else {
   unset($article_id);
   unset($offset);
 }
 $count = get_total_photos($article_id);
 $title = get_story_title_by_id($article_id);

 $photo = get_photo_by_article($article_id,$offset);
 $data = $photo->fetchRow();

 /* displayed photo data */
 $path = $data->path;
 $group = $data->photo_group_id;
 $cutline = $data->cutline;
 $photoby = $data->photoby;
 $width = $data->width;
 $height = $data->height;

 // print "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?"..">";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
  <title><?= $pagetitle; ?></title>
  <link rel="stylesheet" href="css/site_wide.css" type="text/css"/>
  <link rel="stylesheet" href="css/two_tone.css" type="text/css" id="color"/>
  <style type="text/css">
body {
  /* background-color:#ccf; */
  background-color:#eee;
  text-align:center;
  min-width:600px;
}
#container {
  position:absolute;
  top:0;
  height:100%;
  /* width:80%; */
  /* min-width:500px;  */
  width:500px; 
  border-left:1px dotted black;
  border-right:1px dotted black;
  background-color:white;
  /* left:10%;
  right:10%; */
  margin:0 auto;
  left:0;
  right:0;
  text-align:left;
}
#container #header {
  /* margin:10px; */
  /* border:1px solid black; */
  background-color:gray;
  width:100%;
  height:25px;
  margin-top:20px;
  margin:0px;
}
#container #header p {
  position:relative;
  top:3px;
  color:white;
  text-align:center;
  font-size:1.2em;
}
#container #photo {
  float:left;
  background-color:lightgrey;
  /* width:55%;
  min-width:275px; */
  width:270px;
  border-right:1px solid black;
  margin:0;
  padding:0;
}
#container #photo img {
  margin-left:10px;
}
#container #text {
  float:right;
  /* width:40%;
  min-width:200px; */
  width:200px;
  margin-left:10px;
  margin-right:10px;
}
#container #text p {
  font-size:0.8em;
}
#container #nav {
  display:block;
  background-color:gray;
  width:100%;
  height:20px;
  clear:both;
  margin:0px;
}
#container #nav p {
  position:relative;
  color:white;
  top:3px;
  text-align:center;
  margin:0;
}
#container #nav p a {
  color:white;
  text-decoration:none;
}
#disclaimer p {
  text-decoration:none;
  text-align:center;
  font-size:0.8em; 
}
  </style>
 </head>
 <body>
  <div id="container">
   <div id="header">
    <p><?= $title; ?></p>
   </div>
   <div id="photo">
    <img src="<?= $path ?>" width="<?= $width ?>" height="<?= $height ?>">
   </div>
   <div id="text">
    <p><?= $cutline ?></p>
    <p><?= $photoby ?></p>
   </div>
   <div id="nav">
    <p>
     <?php
       for($i=0;$i<$count;$i++) {
      ?>
         <a href="?article_id=<?= $article_id ?>&photo=<?=$i?>"><? print ($i+1); ?></a>
     <? } ?>
    </p>
   </div>
   <div id="disclaimer">
    <p>All content copyright &copy;2006 Prairie Media Inc.</p>
   </div>
  </div>
 </body>
</html>
