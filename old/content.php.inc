<?php
// include('database.php.inc');

// Get a database connection
  // actually, the database.php will make its own connection
  $db_conn = db_connect();
 
  // template files
  $community_temp = $DOCUMENT_ROOT . "/templates/community_content.inc";
  $ongoing_temp = $DOCUMENT_ROOT . "/templates/ongoing_comcal.inc";
  $news_template = $DOCUMENT_ROOT . "/templates/news_content_template.inc";
  $nav_template = $DOCUMENT_ROOT . "/templates/nav_bar_generated_template.inc";


function return_content($section,$page) {
  global $news_template,$db_conn;

  if(!$section) {
    $section = 1;
  }

  if(!$page) {
    $page = 1;
  }

  $page--;
  $num_of_items = "$page , 1";
  $article_data['section'] = $section; 

  $sql = "SELECT article_id, title, subtitle, body, hasPhoto, author_id,
                 date_format(date_stamp,'%m/%d/%Y')
            FROM content
           WHERE section_id=$section
             AND isActive=1
        ORDER BY article_id DESC
           LIMIT $num_of_items";

  // $article_data = array();

  $results = db_query($db_conn,$sql); 
  //print_r($results);
  $data = array_shift($results);
  //print_r($data);

  $article_data['article_id'] = $data[0];
  $article_data['title']      = $data[1];
  $article_data['subtitle']   = $data[2];
  $article_data['body']       = $data[3];
  $article_data['hasPhoto']   = $data[4];
  $article_data['author_id']  = $data[5];
  $article_data['date']       = $data[6];

  // print_r($article_data);

  if($article_data['hasPhoto']) {
    $photodata = return_photoinfo($article_data['article_id']);
    $data = array_shift($photodata);
    $article_data['photopath'] = $data[0];
    $article_data['cutline']   = $data[1];
    $article_data['width']     = $data[2];
    $article_data['height']    = $data[3];
    $article_data['photoby']   = $data[4];
  } 

  if($article_data['author_id']) {
    $results = return_authorinfo($article_data['author_id']);
    $data = array_shift($results);
    $article_data['author']      = $data[0];
    $article_data['designation'] = $data[1];
  } 

  if($article_data['photoby']) {
    $results = return_authorinfo($article_data['photoby']);
    $data = array_shift($results);
    $article_data['photoauthor'] = $data[0];
  } 

  get_prevarticle($article_data);
  get_nextarticle($article_data);

  // print "<h2>".$article_data['prev']."</h2><br/>";
  // print "<h2>".$article_data['next']."</h2>";
  return $article_data;
}

function get_prevarticle(&$id) {
  global $db_conn;

  $sql = "SELECT article_id
            FROM content
           WHERE section_id=".$id['section']."
             AND article_id<".$id['article_id']."
             AND isActive=1
        ORDER BY article_id DESC
           LIMIT 1";

  $results = db_query($db_conn,$sql);
  $data = array_shift($results);
  $id['prev'] = $data[0];
}

function get_nextarticle(&$id) {
  global $db_conn;

  $sql = "SELECT article_id
            FROM content
           WHERE section_id=".$id['section']."
             AND article_id>".$id['article_id']."
             AND isActive=1
        ORDER BY article_id ASC
           LIMIT 1";

  $results = db_query($db_conn,$sql);
  $data = array_shift($results);
  $id['next'] = $data[0];
}

function return_authorinfo($id) {
  global $db_conn;
  
  $sql = "SELECT real_name, title
            FROM user
           WHERE user_id = $id 
             AND isActive = 1";

  $authorresults = db_query($db_conn,$sql); 

  return $authorresults;
}

function return_photoinfo($article) {
  global $db_conn;

  $sql = "SELECT path,cutline,width,height,photoby
            FROM webphoto
           WHERE article_id=$article
             AND isActive=1";

  $photoresults = db_query($db_conn,$sql);
  return $photoresults;
}

function return_article($section,$page) {
  global $news_template,$db_conn;

  if(!$section) {
    $section = 1;
  }

  if(!$page) {
    $page = 1;
  }

  $page--;
  $num_of_items = "$page , 1";
 
  $switchsql = "SELECT article_id, hasPhoto
                  FROM content
                 WHERE section_id='$section'
                   AND isActive=1
              ORDER BY date_stamp DESC
                 LIMIT $num_of_items";


  $switchresults = db_query($db_conn,$switchsql); 
  $dataswitch = array_shift($switchresults);
  $article = $dataswitch[0];
  $switch = $dataswitch[1];
  
  if((int) $switch == 1)  {
    $sql = "SELECT a.title, a.subtitle, a.body,
                   b.real_name as byline, b.title as authortitle,
                   c.path, c.cutline, c.width, c.height,
                   d.real_name as photoby
              FROM content as a, user as b, webphoto as c,
                   user as d
             WHERE a.article_id=$article
               AND a.author_id=b.user_id
               AND a.article_id=c.article_id
               AND c.photoby=d.user_id
               AND c.isActive=1";
  } else {
    $sql = "SELECT a.title, a.subtitle, a.body,
                   b.real_name as byline, b.title as authortitle
              FROM content as a, user as b
             WHERE a.article_id=$article
               AND a.author_id=b.user_id";
  }

  $results = db_query($db_conn,$sql); 

  return $results;
}

function retrieve_community() {
  global $community_temp,$db_conn;

  $week = date('z');
  $week /= 7;

  $sql = "SELECT region_id,title,body
            FROM cbb
           WHERE expires >= $week
             AND isActive = 1
        ORDER BY region_id, cbb_id";

  $results = db_query($db_conn,$sql); 
  $region = '';

  while($results) {
    $data = array_shift($results);
    $regionid = $data[0]; 
    $title = $data[1]; 
    $body = $data[2]; 

    if($region == $regionid) {
      $regionname = '';
    } else {
      $regionname = get_regionname($regionid);
      $region = $regionid;
    }
    include($community_temp);
  } 
}

function get_regionname($regionid) {
  global $db_conn;

  $sql = "SELECT region_name
            FROM region
           WHERE region_id = '$regionid'";
  $results = db_query($db_conn,$sql);
  $data = array_shift($results);
  return $data[0];
}

// function retrieve_articles($section,$number) {
//   global $news_template, $db_conn;
// }

function generate_nav() {
  global $nav_template, $db_conn;
  
  $sql = "SELECT distinct(title),
                 section_id,
                 shortdesc,
                 ext_url
            FROM section
           WHERE isactive=1
        ORDER BY section_id";

  $results = db_query($db_conn,$sql); 

  while($results) {
    $data = array_shift($results);
    $section = $data[0]; 
    $sectionid = $data[1];
    $shortdesc = $data[2];
    $ext_url = $data[3];
    include($nav_template);
  } 
}

function generate_new_nav($id) {
  global $db_conn;
  
  $sql = "SELECT distinct(title),
                 section_id,
                 shortdesc,
                 ext_url
            FROM section
           WHERE isactive=1
        ORDER BY section_id";

  $results = db_query($db_conn,$sql); 

  $first = 1;

  // print "<ul id=\"secondarynav\"><br/>\n";
  while($results) {
    $data = array_shift($results);
    $section = $data[0]; 
    $sectionid = $data[1];
    $shortdesc = $data[2];
    $ext_url = $data[3];
    if(!$ext_url) {
      if($first) {
        if($sectionid == $id) {
          print '<li id="secondarytop"><a style="background:white;color:#33f;" href="index.php?section='.$sectionid.'">'.$section.'</a></li>'."\n";
        } else {
          print '<li id="secondarytop"><a href="index.php?section='.$sectionid.'">'.$section.'</a></li>'."\n";
        }
        $first = 0;
      } else {
        if($sectionid == $id) {
          print '<li><a style="background:white;color:#33f;" href="index.php?section='.$sectionid.'">'.$section.'</a></li>'."\n";
        } else {
          print '<li><a href="index.php?section='.$sectionid.'">'.$section.'</a></li>'."\n";
        }
      }
    } else {
      if($first) {
        if($sectionid == $id) {
          print '<li id="secondarytop" class="visited"><a href="'.$ext_url.'">'.$section.'</a></li>'."\n";
        } else {
          print '<li id="secondarytop"><a href="'.$ext_url.'">'.$section.'</a></li>'."\n";
        }
        $first = 0;
      } else {
        if($sectionid == $id) {
          print '<li class="visited"><a href="'.$ext_url.'">'.$section.'</a></li>'."\n";
        } else {
          print '<li><a href="'.$ext_url.'">'.$section.'</a></li>'."\n";
        }
      }
    }
  } 
  // print "</ul><br/>\n";
}

function getClassbyID($id){
 switch($id) {
   case '085':
   $title = "Publisher's Notice";
   break;
   case '090':
   $title = "Index";
   break;
   case '100':
   $title = "Houses for Sale";
   break;
   case '110':
   $title = "Manufactured Homes";
   break;
   case '120':
   $title = "Orchards & Farms";
   break;
   case '130':
   $title = "Acreage";
   break;
   case '135':
   $title = "Property Wanted";
   break;
   case '140':
   $title = "Commercial Property";
   break;
   case '145':
   $title = "Commercial Rentals";
   break;
   case '150':
   $title = "Business Opportunity";
   break;
   case '160':
   $title = "Housing Wanted";
   break;
   case '170':
   $title = "Shared Housing";
   break;
   case '180':
   $title = "For Rent";
   break;
   case '185':
   $title = "Wanted to Rent";
   break;
   case '190':
   $title = "Vacation Property";
   break;
   case '195':
   $title = "Vacation Rental";
   break;
   case '200':
   $title = "Services";
   break;
   case '210':
   $title = "Daycare";
   break;
   case '215':
   $title = "Education";
   break;
   case '218':
   $title = "Vacation Bible School";
   break;
   case '220':
   $title = "Announcements";
   break;
   case '230':
   $title = "Card of Thanks";
   break;
   case '240':
   $title = "Happy Ads";
   break;
   case '250':
   $title = "Personal";
   break;
   case '260':
   $title = "Finance";
   break;
   case '270':
   $title = "Free";
   break;
   case '280':
   $title = "Lost & Found";
   break;
   case '310':
   $title = "Sales & Marketing";
   break;
   case '320':
   $title = "Help Wanted";
   break;
   case '340':
   $title = "Work Wanted";
   break;
   case '345':
   $title = "Free Kids Ads";
   break;
   case '400':
   $title = "Farm Equipment";
   break;
   case '410':
   $title = "Yard & Garden";
   break;
   case '415':
   $title = "Puzzle Solution";
   break;
   case '420':
   $title = "Produce";
   break;
   case '430':
   $title = "Livestock & Poultry";
   break;
   case '450':
   $title = "Feed: Hay & Grain";
   break;
   case '500':
   $title = "Appliances";
   break;
   case '505':
   $title = "Furniture";
   break;
   case '510':
   $title = "Auctions";
   break;
   case '511':
   $title = "Antiques & Collectibles";
   break;
   case '515':
   $title = "Arts & Crafts";
   break;
   case '520':
   $title = "Musical";
   break;
   case '525':
   $title = "Electronic Equipment";
   break;
   case '526':
   $title = "Computers";
   break;
   case '530':
   $title = "Pets";
   break;
   case '531':
   $title = "Pet Boarding";
   break;
   case '535':
   $title = "Apparel";
   break;
   case '540':
   $title = "Garage & Yard Sale";
   break;
   case '545':
   $title = "Moving Sale";
   break;
   case '550':
   $title = "Wanted";
   break;
   case '555':
   $title = "Bargain Bazaar";
   break;
   case '560':
   $title = "General Merchandise";
   break;
   case '565':
   $title = "Firewood";
   break;
   case '570':
   $title = "Sporting Goods";
   break;
   case '572':
   $title = "Hunting";
   break;
   case '575':
   $title = "Business Equipment";
   break;
   case '580':
   $title = "Equipment";
   break;
   case '590':
   $title = "Building Supplies";
   break;
   case '600':
   $title = "Vehicle Parts & Accessories";
   break;
   case '610':
   $title = "Automobiles";
   break;
   case '620':
   $title = "Trucks & Vans";
   break;
   case '630':
   $title = "Motorcycles";
   break;
   case '635':
   $title = "Snowmobiles";
   break;
   case '640':
   $title = "Campers, Trailers & RVs";
   break;
   case '650':
   $title = "Boats & Trailers";
   break;
   case '660':
   $title = "Rental Equipment";
   break;
   case '994':
   case '995':
   $title = "Statewides";
   break;
   case '996':
   $title = "Okanogan County Legals";
   break;
   case '997':
   $title = "Church Directory";
   break;
   case '998':
   $title = "Business & Services";
   break;
   case '999':
   $title = "Public Notices";
   break;
   default:
   $title = "Undefined";
 }
 return $title;
}

// START EVENT SECTION //

// CREATE TABLE calendar (
//   event_id int(11) NOT NULL auto_increment,
//   org_id int(11) default NULL,
//   title varchar(255) default NULL,
//   body mediumtext,
//   event_date date default NULL,
//   event_time_start time default NULL,
//   event_time_end time default NULL,
//   isActive int(1) default NULL,
//   PRIMARY KEY  (event_id)
// ) TYPE=MyISAM;

// This routine looks up events on the community calendar table 
// If there is an event, it returns true, else false
function retrieve_eventbydate($date) {
  global $db_conn;
  // date_format(date_stamp,'%m/%d/%Y') 
  // print "recieved [$date]\n";
  $datefmt = date('m/d/Y',$date);
  // print "datefmt [$datefmt]\n";
  // die();
  $sql = "SELECT event_id
            FROM calendar
           WHERE date_format(event_date,'%m/%d/%Y') = '$datefmt'
             AND isActive=1";
             // AND isCal=1";

  $results = db_query($db_conn,$sql); 
  $data = array_shift($results);
  return $data;
}
// This routine looks up events on the community calendar table 
// If there is an event, it returns true, else false
// Well, The eventbydate routine took way too much time. Each page hits the 
// database 29-31 times just to generate the calendar (and it FEELS like a
//  long time)
// this new routine will return an array by month, populated with days that
// actually have events (making it 1 call, not 29-31
function retrieve_eventsbymonth($month) {
  global $db_conn;
  $sql = "SELECT date_format(event_date,'%d'),event_id
            FROM calendar
           WHERE date_format(event_date,'%M') = '$month'
             AND isActive=1";
             // AND isCal=1";

  $results = db_query($db_conn,$sql); 
  while($results) {
    $data = array_shift($results);
    $datestamp = $data[0];
    $articleid = $data[1];
    $newdata[$datestamp] = $articleid;
  }
  return $newdata;
}

// This function will take a unix timestamp, and return the detailed information
// about events that fall on that day
function retrieve_eventbyday($datestr) {
  global $db_conn;
  $date = date('m/d/Y',$datestr);
  $sql = "SELECT date_format(event_date,'%m/%d/%Y'),event_id,title,body,
                 time_format(event_time_start,'%h:%i %p'),
                 time_format(event_time_end,'%h:%i %p')
            FROM calendar
           WHERE date_format(event_date,'%m/%d/%Y') = '$date'
             AND isActive=1";
             // AND isCal=1";

  $results = db_query($db_conn,$sql); 
  return $results;
}

// Note: not providing a currentdate to this will mean if people browse back
// in the calendar history a few years, the ongoing events will STILL show up.
// Think if thats what you really want.
function retrieve_eventsongoing() {
  global $ongoing_temp,$db_conn;
  $date = date('m/d/Y',$datestr);
  $sql = "SELECT event_id,title,body,
                 time_format(event_time_start,'%h:%i %p'),
                 time_format(event_time_end,'%h:%i %p')
            FROM calendar
           WHERE event_date is NULL
             AND isActive=1";
             // AND isCal=1";

  $results = db_query($db_conn,$sql); 
  while($results) {
    $data = array_shift($results);
    $event_id = $data[0]; 
    $title    = $data[1]; 
    $body     = $data[2]; 
    $stime    = $data[3]; 
    $etime    = $data[4]; 
    include($ongoing_temp);
  }
}

// END EVENT SECTION //
?>

