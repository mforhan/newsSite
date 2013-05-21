<?php

/* Slideshow Include */
// The purpose of this is to enable slideshows for stories. The fastest method
// to generate a slideshow is from the article_id. Using the article_id, we
// can get a list of photos from the database.

function get_photo_by_article($article_id,$offset) {
  global $db;
  
  if(!isset($offset)) {
    $offset = 0;
  } 
  if(!$article_id) {
    return -1;
  }

  $SQL = "SELECT path,photo_group_id,cutline,photoby,
                 width,height
            FROM webphoto
           WHERE article_id=$article_id
             AND isActive = 1
           LIMIT $offset,1";

  $results = $db->query($SQL);
  return($results);
}
function get_total_photos($article_id) {
  global $db;

  $SQL = "SELECT count(*) as total
            FROM webphoto
           WHERE article_id = $article_id
             AND isActive = 1";           

  $results = $db->query($SQL);
  $data = $results->fetchRow();
  return $data->total;
}
function get_story_title_by_id($article) {
  global $db;

  $SQL = "SELECT headline
            FROM content
           WHERE article_id=$article
             AND status=6
             AND isActive=1
           LIMIT 1";
  $results = $db->query($SQL);
  $data = $results->fetchRow();
  return $data->headline;
}
?>
