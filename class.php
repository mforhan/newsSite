<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
  <title>Test Classifieds</title>
  <!--<link rel="stylesheet" href="css/site_wide.css" type="text/css"/>-->
  <!--<link rel="stylesheet" href="css/two_tone.css" type="text/css" id="color"/>-->
  <style type="text/css">
body {
  background-color:#dcb;
}
#classes {
  display:block;
  width:450px;
  padding:10px;
  background-color:white;
  margin-left:auto;
  margin-right:auto;
}
.head {
  display:block;
  width:450px;
  height:1em;
  padding-top:0.1em;
  padding-bottom:0.1em;
  text-align:center;
  background-color:black;
  color:white;
}
p {
  display:block;
  width:450px;
  text-align:justify;
  border-bottom:1px solid black;
  padding-bottom:10px;
  font:10pt Helvetica;
  /* font-family:Helvetica;
  font-size:10pt; */
}
  </style>
 </head>
 <body>
<?php
 $filename = "PIPETEXT.TXT";
 if($HTTP_GET_VARS['class']) {
   $class = $HTTP_GET_VARS['class'];
 } else {
   unset($class);
 }
 if(is_file($filename)) {
   print "<div id=\"classes\">\n";
   $fHandle = fopen($filename,"r");
   while($line = fgets($fHandle)) {
     $line = trim($line);
     $array = explode("|",$line);
     // print_r($array);
     // print "<p>$line</p>\n";
     if(getClassbyID($array[0]) == $head) {
       next;
     } else {
       $head = getClassbyID($array[0]);
       if($class == $array[0] || !isset($class)) {
         print "<div class=\"head\" onClick=\"location='?class=".$array[0]."';\">".$array[0]." - ".getClassbyID($array[0])."</div>\n"; 
       }
     }
     if($class == $array[0] || !isset($class)) {
       $text = $array[7];
       $text = preg_replace("/\@/","(at)",$text); // For Email Addresses 
       $text = preg_replace("/\Õ/","'",$text); // Replace "Smart" apostrophe
       $text = preg_replace("/\Ò/","\"",$text); // Replace "Smart" open quote
       $text = preg_replace("/\Ó/","\"",$text); // Replace "Smart" close quote
       $text = preg_replace("/\¥/","&#8901;",$text); // Replace Word's dot
       if($text == "") {
         next;
       } else {
         print "<p>$text</p>\n";
       }
     }
   }
   fclose($fHandle);
   print "</div>";
 } else {
   die("Could not open file '$filename'\n");
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
 </body>
</html>
