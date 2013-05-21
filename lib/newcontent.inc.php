<?php
/*
 **** SQL Library file ****
 *@param  This library contains all the SQL for the site
 *@param  and prepared in PDO to be ready for page retrieval
 *@global $db is global established in newapp.inc.php
 *@class  SQL object
 *@access public
 *@author: Michael Forhan
 *@date: 05/19/2013
 */


// $content = retrieve_content($npage,1,$preview,'FULL');
function retrieve_ids($site_id) {
  return -1;
  global $db;
  if(!$site_id) {
    return -1;
  }
 
  $SQL = "SELECT content_id
            FROM site_content
           WHERE site_id = :site
             AND isActive = 1
        ORDER BY publish_date DESC";
           //LIMIT 2";

  $results = $db->prepare($SQL);
  $results->execute(array('site' => $site_id));

  return $results; // $row = $results->fetch(); <- wrap in a while for all rows
}
function lookup_story_by_id($article) {
  global $db;
    return -1;
  // $article = $content_id; 
 
  $SQL = "SELECT article_id, headline, subhead, body, author_name, author_title,
                 date_format(date_stamp,'%M %D, %Y') as date, hasPhoto
            FROM content
           WHERE article_id= :article
             AND status=6
             AND isActive=1";

  $results = $db->prepare($SQL);
  $results->execute(array('article' => $article));

  return($results);
}
function retrieve_content($site_id,$section_name) {
  global $db;
  if(!$site_id || !$section_name) {
    return -1;
  }
 
  $SQL = "SELECT content_id
            FROM site_content
           WHERE site_id= :site
             AND section_name= :section
             AND isActive = :active
        ORDER BY publish_date DESC
           LIMIT 1";

  // PDO Option
  // $result = $db->query($SQL);
  // $result->setFetchMode(PDO::FETCH_CLASS, 'User');
  // while($user = $result->fetch()) {
  //   $user->full_name();
  // }

  $results = $db->prepare($SQL);
  $results->execute(array(':site' => $site_id, ':section' => $section_name, ':active' => 1));
  $results->setFetchMode(PDO::FETCH_CLASS, 'User');

  $data = $results->fetch();
  echo "data contains: [" . $data . "]\n";
  print_r($data);

  $article = $data->content_id; 
 
  $SQL = "SELECT article_id, headline, subhead, body, author_name, author_title,
                 date_format(date_stamp,'%M %D, %Y') as date, hasPhoto
            FROM content
           WHERE article_id = .article
             AND status=6
             AND isActive=1";

  $results = $db->prepare($SQL);
  $results->execute(array('article' => $article));
  return($results);
  
}

function get_photos($article_id) {
  global $db;
  if(!$article_id) {
    return -1;
  }
  return -1;
  $SQL = "SELECT path,photo_group_id,cutline,photoby,width,height
            FROM webphoto
           WHERE article_id=$article_id
             AND isActive = 1
        ORDER BY photo_id ASC";

  $results = $db->query($SQL);
  return($results);
}

function _retrieve_content($section,$number,$preview,$style) {
  global $db;

  return -1;
  if(!$section) {
    $section = 1;
  }

  if(!$number) {
    $number = 1;
  }

  if($preview || $preview == 1) {
    $isActive = 0; 
  } else {
    $isActive = 1;
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
                   AND isActive=$isActive
              ORDER BY date_stamp DESC
                 LIMIT $num_of_items";

  $switchresults = $db->query($switchsql); 
  $dataswitch = $switchresults->fetchRow();
  $article = $dataswitch->article_id;
  $switch = $dataswitch->hasPhoto;

  
  if((int) $switch == 1)  {
    $sql = "SELECT a.section_id as section_id, a.title as headline, 
                   a.subtitle as subhead, a.body as story,
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
               AND a.isActive=$isActive
               AND c.isActive=1
          ORDER BY a.date_stamp
             LIMIT $num_of_items";
  } else {
    $sql = "SELECT a.section_id as section_id, a.title as headline, 
                   a.subtitle as subhead, a.body as story,
                   date_format(a.date_stamp,'%M %D, %Y') as date,
                   b.real_name as byline, b.title as authortitle
              FROM content as a, user as b
             WHERE a.author_id=b.user_id
               AND a.article_id=$article
               AND a.section_id='$section'
               AND a.isActive=$isActive
          ORDER BY a.date_stamp
             LIMIT $num_of_items";
  }
  // echo $sql;
  $results = $db->query($sql); 

  return $results;
  //while($data = $results->fetchRow()) {
  //  $headline = $data->headline;
  //  $subhead = $data->subhead;
  //  $body = $data->story;
  //  $author = $data->byline;
  //  $authortitle = $data->authortitle;
  //  $path = $data->path;
  //  $cutline = $data->cutline;
  //  $width = $data->width;
  //  $height = $data->height;
  //  $photoby = $data->photoby;
  //} 
}


function generate_nav() {
  global $db;
  
  return -1;
  $sql = "SELECT distinct(title),
                 section_id,
                 ext_url,
                 shortdesc,
                 roworder
            FROM section
           WHERE isactive=1
        ORDER BY roworder";

  $results = $db->query($sql);

  while($data = $results->fetchRow()) {
    $section   = $data->title;
    $sectionid = $data->section_id;
    $url   = $data->ext_url;
    $shortdesc = $data->shortdesc;

    print "<li style=\"border-bottom:1px dotted;font:10pt/14pt Georgia;\"><a href=\"$url\" alt=\"$shortdesc\">$section</a></li><!-- $count -->\n";
    // In order to use separators you'll need to write a section
    // that reads every record out of the db, publishes the active ones
    // and puts in placeholders where the numbers skip.
  } 

}

function samplecode() {
        global $db, $CFG, $DOCUMENT_ROOT;

        // SELECT
        $query = " SELECT  max(compid) as compid
                     FROM  competition ";
        $result = $db->query($query);
        $row = $result->fetchRow();
        $compid = $row->compid;
        
        errorLog2("Ending competition # $compid on " . date("d-m-Y"));
        
        $today = time();

        // UPDATE
        $query = " UPDATE  competition
                      SET  enddate = '$today'
                    WHERE  compid = '$compid' ";
        $result = $db->query($query);
 
        // INSERT
        $query = " INSERT INTO  comp_history
                           SET  compid = '$compid',
                                login  = '" . $player[$key][name] . "',
                                team   = '" . $player[$key][team] . "'
                  ";
                $result = $db->query($query);
}

function getClassbyID($id){
  return -1;
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
   case '574':
   $title = "Recreation";
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
?>
