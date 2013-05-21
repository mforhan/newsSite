<?php
 include("application.inc.php");
 header("Content-Type: text/xml");
 echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
 $npage = array(1,3);
 $myurl = "";
 $counter = 0;
?>
<rss version="2.0">
 <channel>
  <title>The Leavenworth Echo</title>
  <copyright>
   Copyright (c) 2005 Prairie Media Inc. All rights reserved.
  </copyright>
  <link>http://www.leavenworthecho.com/</link>
  <description>Weekly News</description>
  <language>en-us</language>
  <lastBuildDate><?= $fulldate ?></lastBuildDate>
  <ttl>288</ttl>
  <image>
   <title>The Leavenworth Echo</title>
   <width>150</width>
   <height>50</height>
   <link>http://www.leavenworthecho.com</link>
   <url>
     http://www.leavenworthecho.com/images/rss_head.jpg
   </url>
  </image>
<?
 while($npage) {
   $page = array_shift($npage);
   $content = retrieve_content($page,1,'FULL');
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
      ereg('(.[^\.]+\.\ *[0-9]*.[^\.]*\.){1}',$story01,$sentence); 
      $count = 0;
      $story = $sentence[0];
      $story = eregi_replace('<p>','',$story);
      $story = eregi_replace('</p>','',$story);
    }
    switch($page) {
      case '1':
        $myurl = "index.php";
        break;
      case '3':
        $myurl = "sports.php";
        break;
    } 
?>
  <item>
   <title><?= $headline ?></title>
   <link>http://www.leavenworthecho.com/<?= $myurl ?></link>
   <guid isPermaLink="false"><?= $myurl ?></guid>
   <pubDate><?= $fulldate ?></pubDate>
   <description>
    <?= $story ?>
   </description>
  </item>
<?
   $counter++;
   if($counter > 10) {
    die("Big loop!");
   }
  }
?>
 </channel>
</rss>
