<?php

function retrieve_content($section,$number,$style,$article_date=FALSE) {
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
  
// Added 02-08-2006 to allow direct story access
  if($article_date) { 
  $switchsql = "SELECT article_id, hasPhoto
                  FROM content
                 WHERE section_id='$section'
                   AND date_stamp='$article_date'
                   AND isActive=1
              ORDER BY date_stamp DESC
                 LIMIT $num_of_items";
  } else {
  $switchsql = "SELECT article_id, hasPhoto
                  FROM content
                 WHERE section_id='$section'
                   AND isActive=1
              ORDER BY date_stamp DESC
                 LIMIT $num_of_items";
  }
 
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
               AND a.isActive=1
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
               AND a.isActive=1
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


function retrieve_contentbyID($article,$style) {
  global $db;

  if(!$article) {
    die('no article specified.');
  }

  // FULL Style is the full story, COMPACT is only the first 200 words
  if(!$style && ($number == 1)) {
    $style = 'FULL';
  } else if (!$style) {
    $style = 'COMPACT';
  }

  $switchsql = "SELECT article_id, hasPhoto
                  FROM content
                 WHERE article_id='$article'";

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
               AND a.isActive=1
               AND c.isActive=1";
  } else {
    $sql = "SELECT a.section_id as section_id, a.title as headline, 
                   a.subtitle as subhead, a.body as story,
                   date_format(a.date_stamp,'%M %D, %Y') as date,
                   b.real_name as byline, b.title as authortitle
              FROM content as a, user as b
             WHERE a.author_id=b.user_id
               AND a.article_id=$article
               AND a.isActive=1
          ORDER BY a.date_stamp";
  }
  // echo $sql;
  $results = $db->query($sql); 

  return $results;
}


function generate_nav() {
  global $db;
  
  $sql = "SELECT distinct(title),
                 section_id,
                 ext_url,
                 shortdesc
            FROM section
           WHERE isactive=1";

  $results = $db->query($sql);

  while($data = $results->fetchRow()) {
    $section   = $data->title;
    $sectionid = $data->section_id;
    $url   = $data->ext_url;
    $shortdesc = $data->shortdesc;
    print "<li style=\"border-bottom:1px dotted;\"><a href=\"$url\" alt=\"$shortdesc\">$section</a></li>";

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
