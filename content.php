<!-- Start Content -->
<?php
 $content = retrieve_content($site,$section);
 unset($data);
 while($data = $content->fetch(PDO::FETCH_OBJ)) {
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

    $data = $photo->fetch(PDO::FETCH_OBJ);
       $path = $data->path;
       $group_id = $data->photo_group_id;
       $cutline = $data->cutline;
       $photoby = $data->photoby;
       $width = $data->width;
       $height = $data->height;
    if($group_id) {
      $icon = "<a id=\"mPhotos\" target=\"_blank\" onClick='javascript:window.open(\"viewPhoto.php?article_id=$article_id\",\"\",\"scrollbars=yes,toolbars=no,width=320px,height=450px\");'><img src=\"images/morePhotos.gif\" width=\"20\" height=\"20\" style=\"margin-left:5px;margin-top:5px;cursor:pointer;\" alt=\"click here for more photos\" onMouseOver=\"showToolTip();\" onMouseout=\"hideToolTip();\"></a>";
      $tooltip = "<div id=\"tooltip\"><p>click to see more photos</p></div>";
    } else {
      $icon = NULL;
      $tooltip = NULL;
    }
 }
 /* $procStory = $story;
 $procStory = eregi_replace('<p>','',$procStory);
 $procStory = eregi_replace('</p>','',$procStory);
 $procStory = eregi_replace('&ldquo;','"',$procStory);
 $procStory = eregi_replace('&rdquo;','"',$procStory);
 $procStory = eregi_replace('&rsquo;','\'',$procStory);
 $procStory = ereg_replace('\."','. "',$procStory);
 $Sentences=preg_split("/((?<=[a-z0-9)][.?!])|(?<=[a-z0-9][.?!]\"))(\s|\r\n)(?=\"?[A-Z])/",$procStory); */
?>
<!-- Start story -->
<h1><?= $headline ?></h1>
<? if($subhead) { ?>
<h4><?= $subhead ?></h4>
<? } ?>
<? if($hasPhoto && $article_id) { ?>
<!-- Start Photo -->
<div id="photo">
  <img src="<?= $path ?>" width="<?= $width ?>" height="<?= $height ?>" alt="story photo"/>
  <?= $icon ?><?= $tooltip ?>
  <p class="byline"><?= $photoby ?></p>
  <p><?= $cutline ?></p>
</div>
<!-- End Photo -->
<? } ?>
<p class="byline"><?= $author_name ?></p>
<p class="byline"><?= $author_title ?></p>
<?= $story ?>
<!--<p><? //print_r($Sentences); ?></p>-->
<!-- End story -->
<!-- End Content -->
