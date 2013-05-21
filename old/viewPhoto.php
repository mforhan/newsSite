<?php
 // include('application.inc.php');
 include("newapp.inc.php");

 if($HTTP_GET_VARS) {
   $article_id = $HTTP_GET_VARS['article_id'];
 } else {
   unset($article_id);
 }
?>
<html>
 <head>
  <!--<title><?= $THEME->site_title ?></title>-->
  <title>Image Gallery</title>
  <!--<link href="css/menu_bar_admin.css" rel="stylesheet" type="text/css">-->
  <LINK REL="stylesheet" type="text/css" href="page_test.css"/>
  <style type="text/css">
body {
background-color:#004;
margin:10px;
}
#head {
font:16pt Georgia Black;
background-color:#dddddd;
margin:-10px;
text-align:center;
}
#photo {
width:250px;
padding:10px;
margin:10px;
border:1px solid black;
background-color:#ddd;
clear:both;
margin-bottom:0px;
}
#photo p {
font:8pt georgia;
text-align:justify;
margin:0px;
margin-top:2px;
color:black;
}
#photo p.byline {
/* font-weight:bold; */
font-style:italic;
text-align:right;
color:gray;
}
a:link {
  color: black;
  text-decoration:none;
}
a:visited { 
  color: black; 
  text-decoration:none;
}
a:hover { 
  color: gray;
}
a:active { 
  color: black; 
}
a img {
border:none;
}
  </style>
 </head>
 <body>
<!-- Start Content -->
<div id="head">
<p>Image Gallery</p>
</div>
<?php
  $photo = get_photos($article_id);

  while($data = $photo->fetchRow()) {
     $path = $data->path;
     $group = $data->photo_group_id;
     $cutline = $data->cutline;
     $photoby = $data->photoby;
     $width = $data->width;
     $height = $data->height;

?>
<!-- Start Photo -->
<div id="photo">
<img src="<?= $path ?>" width="<?= $width ?>" height="<?= $height ?>">
<p class="byline"><?= $photoby ?></p>
<p><?= $cutline ?></p>
</div>
<!-- End Photo -->
<? } ?>
<!-- End story -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-339325-1";
urchinTracker();
</script>
 </body>
</html>
