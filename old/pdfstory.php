<?php
// NOTE: 250px is 88.19mm
include("application.inc.php");
 
 unset($section_id);
 unset($article_id);

 if( $HTTP_POST_VARS || $HTTP_GET_VARS ) {
 
   if( $_SERVER['_POST'] ) { 
     $section_id = $HTTP_POST_VARS['section'];
     $article_id = $HTTP_POST_VARS['article'];
   } else {
     $section_id = $HTTP_GET_VARS['section'];
     $article_id = $HTTP_GET_VARS['article'];
   }
 } 
 
 if(!$section_id && !$article_id) { 
  $section_id = 1;
 }


define('FPDF_FONTPATH','font/');
require('fpdf.php');

// Global Variables
// Usable Space (255m x 203mm) - Not including header/footer
$marginT = 12; // mm
$marginB = 12; // mm
$marginL = 12; // mm
$marginR = 12; // mm

// Fonts & Sizes
$contentFont     = 'Arial';
$contentFontSize = 10;
$photoFont       = 'Arial';
$photoFontSize   = 8;

/* These fonts are automatically resized to fit the given
   space for headline & cutline */
$headFont        = 'Times';  
$headFontSize    = 20;
$subheadFont     = 'Helvetica';
$subheadFontSize = 16;

// usable space
$mmWidth  = 215;
$mmHeight = 278; 
$useWidth  = $mmWidth-($marginL+$marginR);
$useHeight = $mmHeight-($marginT+$marginB+$noteSize); // oddly enought, notesize isn't defined.

// Advertising
$ad = 0;
$adHeight = 1; // in IN

$adHeight = 25*$adHeight; // Convert to MM

/*
if($ad) {
  $useHeight = $useHeight - mm2line($adHeight,$contentFontSize);
} */

// Header & Footer into
$noteSize = 8; // height of footer and header
$noteFont = 'Times';
$noteFontSize = 6;

$numCol  = 3;
$gutter  = 3;
$colW    = $mmWidth-($marginR+$marginL);
$colW    = ceil(($colW - (($numCol-1)*$gutter))/$numCol);

// $colW    = //65;  // 3col*65+gutter*2 = 195+8 = 203

// Program functions
$zero = 0;

function px2mm($px){
	return $px*25.4/72;
}
function pt2mm($pt){
	$pixels = ($pt*72)/72;
        return ceil(px2mm($pixels));
}
function mm2line($mm,$fontsize) {
	// to calculate how many lines fit in a given MM	
       $sizeofLine = pt2mm($fontsize);
       return ceil($mm/$sizeofLine);
}

class PDF extends FPDF {

// Page header
function Header() {
  global $marginL,$marginT,$noteSize,$noteFont,$noteFontSize,$THEME;

  $this->SetXY($marginL,$marginT);
  $this->SetFont($noteFont,'',$noteFontSize);
  $this->Cell($zero,$noteSize,'Story from '.$THEME->site_title .' Website ('.$THEME->site_url .')','B',0,'C');
}

// Page Footer
function Footer() {
  global $marginB,$noteSize,$noteFont,$noteFontSize;
  $this->SetY(-($marginB+$noteSize)); // Negative comes from the bottom up
  $this->SetFont($noteFont,'',$noteFontSize);
  $this->Cell($zero,$noteSize,'All content copyright 2005, 2006 Prairie Media Inc. Use without prior permission is prohibited. Page '.$this->PageNo().'/{nb} ','TB',0,'C');
}

// This function adds maxline
// I'm attempting to rewrite this to deal with a mm size.
/* I'm pretty sure the line calculation works like this:
   1) Take a copy of the input string - remove all newlines
   2) Determine the length of the string (GetStringWidth)
   3) Divide the length by the width of the space
      a) This gives us the number of lines
   4) if the number of lines is greater than maxline, we need
      to trim the line down.
   5) At the given font size, find out how many letters fit on
      a line. 
   6) substring that many characters out
   7) return the remainder of the string and print the multicell
   
   - The only problem with this is it doesn't take newlines into effect
     (adding to the # of lines), so it can't be correct.
   
   Method Two:
   1) Break the content into paragraph chunks (in our case, </p><p> tags)
   2) calculate the number of lines the content will take up
      a) Length of string / size of Font -> convert to MM
   3) if the lines exceeds the limit, the paragraph need to be broken. 
      a) count the number of sentences
      b) determine the percentage the paragraph should be
         i) I have 5 lines available, the content is 10 lines
         ii) We have 6 sentences
         iii) The content needs to be reduced by 50%
         iv) calculate size of 50% of the sentences (3)
   4) Repeat #3 until we have a fit, send all paragraphs and
            paragraph fragment back to the user.
    
*/
function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0,$maxline=0) {
  //Output text with automatic or explicit line breaks, maximum of $maxlines
  $cw=&$this->CurrentFont['cw'];
  if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
  $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
  $s=str_replace("\r",'',$txt);
  $nb=strlen($s);
  if($nb>0 and $s[$nb-1]=="\n")
      $nb--;
  $b=0;
  if($border)
  {
      if($border==1)
      {
          $border='LTRB';
          $b='LRT';
          $b2='LR';
      }
      else
      {
          $b2='';
          if(is_int(strpos($border,'L')))
              $b2.='L';
          if(is_int(strpos($border,'R')))
              $b2.='R';
          $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
      }
  }
  $sep=-1;
  $i=0;
  $j=0;
  $l=0;
  $ns=0;
  $nl=1;
  while($i<$nb)
  {
      //Get next character
      $c=$s[$i];
      if($c=="\n")
      {
          //Explicit line break
          if($this->ws>0)
          {
              $this->ws=0;
              $this->_out('0 Tw');
          }
          $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
          $i++;
          $sep=-1;
          $j=$i;
          $l=0;
          $ns=0;
          $nl++;
          if($border and $nl==2)
              $b=$b2;
          if ( $maxline  && $nl > $maxline ) 
              return substr($s,$i);
          continue;
      }
      if($c==' ')
      {
          $sep=$i;
          $ls=$l;
          $ns++;
      }
      $l+=$cw[$c];
      if($l>$wmax)
      {
          //Automatic line break
          if($sep==-1)
          {
              if($i==$j)
                  $i++;
              if($this->ws>0)
              {
                  $this->ws=0;
                  $this->_out('0 Tw');
              }
              $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
          }
          else
          {
              if($align=='J')
              {
                  $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                  $this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
              }
              $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
              $i=$sep+1;
          }
          $sep=-1;
          $j=$i;
          $l=0;
          $ns=0;
          $nl++;
          if($border and $nl==2)
              $b=$b2;
          if ( $maxline  && $nl > $maxline ) 
              return substr($s,$i);
      }
      else
          $i++;
  }
  //Last chunk
  if($this->ws>0)
  {
      $this->ws=0;
      $this->_out('0 Tw');
  }
  if($border and is_int(strpos($border,'B')))
      $b.='B';
  $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
  $this->x=$this->lMargin;
  return '';
 }
}

function photoBox($image,$photoby,$cutline,$imageStart) {
    global $pdf,$colW,$gutter,$marginL,$photoFont,$photoFontSize;
    // Get Photo Info
    $img = ImageCreateFromJPEG($image);
    $imgX = ImageSx($img);
    $imgY = ImageSy($img);
    ImageDestroy($img);

    //Column widths
    // $w=88;
    $w = px2mm($imgX);
    $h = px2mm($imgY);

    // Since a 'column' is 65mm, we need to calculate a modifier
    // to be able to consistently change the image.
    $modW = $colW/$w; // 65/88 in most cases
    $w = $colW;
    $h = ceil($h*$modW);

    // $x=117; // This assumes the width of the photo @ 250px (88mm)
    // $y=40; // starting top row of photo (in mm)
    $x = $marginL+($colW*2)+($gutter*2);
    if($imageStart > 0) {
      $y = $imageStart;
    }

    $pdf->SetFont($photoFont,'',$photoFontSize);

    $sW = $pdf->GetStringWidth($cutline);
    $roundSW = ceil($sW/$w);
    $textH = pt2mm($photoFontSize); // *$roundSW;

    $bylineH = pt2mm($photoFontSize);

    // Cellheight = Photo Heigh + Height of Cutline + margin
    $cellheight = $h + ($textH*$roundSW) + $bylineH + 8; // ($roundSW*8) + 2; // + 8;

    // size of photo & cutline box
    // $pdf->Rect($x,$y,$w,$cellheight,"D");
    
    // $pdf->Image("$image",$x,$y,$w,$h);
    $pdf->Image("$image",$x,$y,$w,$h);

    $newY = $y+$h;
    $pdf->SetXY($x,$newY);
    $pdf->SetTextColor(100,100,100);
    $pdf->Cell($w,6,$photoby,0,0,'R');

    // cutline
    $pdf->SetXY($x,$newY+6);
    $pdf->SetTextColor(0,0,0);
    $pdf->MultiCell($w,$textH,"$cutline");

    //Closure line
    $pdf->SetXY($x,$cellheight+$y);
    $pdf->Cell($w,0,'','T');
    
    return $cellheight+$imageStart; // $cellheight; // $pdf->GetY(); // Y is the position down the page.
}

function fitText($limitsArray,$text) {
  if(!is_array($limitsArray)) {
    trigger_error ("Input not Array", E_USER_ERROR);
  }
  /* 
  $limitArray['fontSize'];
  $limitArray['fontType'];
  $limitArray['fontWeight'];
  $limitArray['rowOne']['size'];
  $limitArray['rowTwo']['size'];
  */

  $textWidth = ceil($pdf->GetStringWidth($text));
 
  if($textWidth == $limitArray['rowOne']['size']) {
    return 1;
  } else if( $textWidth == $limitArray['rowTwo']['size']) {
    return 2;
  }

  if($textWidth < $limitArray['rowOne']['size']) {
    while($textWidth < $limitArray['rowOne']['size']) {
      $limitArray['fontSize'] += 2;
      $pdf->SetFont($limitArray['fontType'],$limitArray['fontWeight'],$limitArray['fontSize']);
      $textWidth = $pdf->GetStringWidth($text);
    }
  } else if($textWidth > $limitArray['rowOne']['size']) {
  }
}

// $content =retrieve_content(6,1,0,'FULL'); // Community Bulletin Board
if($article_id) {
  $content = retrieve_contentbyID($article_id,'FULL');
} else {
  $content = retrieve_content($section_id,1,'FULL');
}
// $content = retrieve_content(3,1,0,'FULL');

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
  }
/* Added to remove HTML codes for quotes, etc that are added by FCKeditor */
$headline = stripslashes($headline);
$headline = eregi_replace('&nbsp;',' ',$headline);
$headline = eregi_replace('&lsquo;',"'",$headline);
$headline = eregi_replace('&rsquo;',"'",$headline);
$headline = eregi_replace('&ldquo;','"',$headline);
$headline = eregi_replace('&rdquo;','"',$headline);

$subhead = eregi_replace('&nbsp;',' ',$subhead);
$subhead = eregi_replace('&lsquo;',"'",$subhead);
$subhead = eregi_replace('&rsquo;',"'",$subhead);
$subhead = eregi_replace('&ldquo;','"',$subhead);
$subhead = eregi_replace('&rdquo;','"',$subhead);

$cutline = eregi_replace('&nbsp;',' ',$cutline);
$cutline = eregi_replace('&lsquo;',"'",$cutline);
$cutline = eregi_replace('&rsquo;',"'",$cutline);
$cutline = eregi_replace('&ldquo;','"',$cutline);
$cutline = eregi_replace('&rdquo;','"',$cutline);
/* End HTML Code Removal */

$pdf=new PDF('P','mm','Letter');
$pdf->SetCreator("MySQL2PDF by Michael Forhan");
$pdf->SetTitle("Gazette-Tribune Article for $fulldate");
// $pdf->SetAuthor("Story by [$byline]: Program by [Michael Forhan]");
$pdf->SetAuthor("Michael Forhan");
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(0,$marginB+$noteSize);

/* Drawing a Headline box */
  $safety = 40;
  $count = 0;

  $pdf->SetFont($headFont,'B',$headFontSize);
  $headW = $pdf->GetStringWidth($headline);

  if($headW <= $useWidth) {
    while($headW <= $useWidth) {
      $headFontSize += 2;
      $pdf->SetFont($headFont,'B',$headFontSize);
      $headW = $pdf->GetStringWidth($headline);
      $count++;
      if($count > $safety) {
        die("Infinite Loop");
      }
    }   
  }

  $safety = 80;
  $count = 0;
  if($headW > $useWidth && $headW <= 2*$useWidth) {
    if($headW < ($useWidth*1.25)) {
    } else {
    }
    while($headW < $useWidth) {
      $headFontSize += 2;
      $pdf->SetFont($headFont,'B',$headFontSize);
      $headW = $pdf->GetStringWidth($headline);
      $count++;
      if($count > $safety) {
        die("Infinite Loop");
      }
    }   
  }

  $headFontSize -= 2;
  $pdf->SetFont($headFont,'B',$headFontSize);

  $headBoxH = pt2mm($headFontSize);

  $pdf->SetXY($marginL,$marginT+$noteSize);
  $pdf->Cell($useWidth,$headBoxH,"$headline",0,1);

  // $imageStart = $marginT+$noteSize+$headBoxH+5;
  $imageStart = $marginT+$noteSize+$headBoxH+5;
  $pdf->ln(0.75);
/* End Headline Box */ 

if($image) {
  // $image = $image;
  $image_end = photoBox($image,$photoby,$cutline,$imageStart);
} else {
  $image_end = $imageStart;
}


if($image) {
  $subheadBoxW = ($colW*2)+$gutter; // Width of 2 columns (3rd being the photo)
} else {
  $subheadBoxW = $useWidth; // Width of page
}

/* Drawing a subhead box */
if($subhead) {
  $safety = 40;
  $count = 0;

  $pdf->SetFont($subheadFont,'I',$subheadFontSize);
  $subheadW = $pdf->GetStringWidth($subhead);

  if($subheadW <= $subheadBoxW) {
    while($subheadW <= $subheadBoxW) {
      $subheadFontSize += 2;
      $pdf->SetFont($subheadFont,'I',$subheadFontSize);
      $subheadW = $pdf->GetStringWidth($subhead);
      $count++;
      if($count > $safety) {
        die("Infinite Loop");
      }
    }   
  }

  $safety = 40;
  $count = 0;
  if($subheadW > $subheadBoxW && $subheadW <= (2*$subheadBoxW)) {
    if($subheadW < ($subheadBoxW + 0.25*$subheadBoxW)) {
    } else {
    }
    while($subheadW < $subheadBoxW) {
      $subheadFontSize += 2;
      $pdf->SetFont($subheadFont,'I',$subheadFontSize);
      $subheadW = $pdf->GetStringWidth($subhead);
      $count++;
      if($count > $safety) {
        die("Infinite Loop");
      }
    }   
  }

  $subheadFontSize -= 2;
  $pdf->SetFont($subheadFont,'I',$subheadFontSize);

  $subheadBoxH = pt2mm($subheadFontSize);

  $pdf->SetXY($marginL,$imageStart);
  $pdf->Cell($subheadBoxW,pt2mm($subheadFontSize),"$subhead",0,1);

  // WE DON'T WANT THE IMAGE TO START UNDER THE SUBHEAD
  // THE SUBHEAD SHOULD BE FLUSH WITH THE TOP OF THE IMAGE
  //$imageStart += $subheadBoxH + 5;
  $imageStart += $subheadBoxH; // + 5;
  $image_end += $subheadBoxH; // + 5;
}
/* End Subhead Box */ 

/* Story Byline */
// if($section != '6') {
if('6' != $section_id) {
  $pdf->SetXY($marginL,$imageStart);
  $pdf->SetFont($contentFont,'I',$contentFontSize);
  $pdf->SetTextColor(100,100,100);
  $pdf->Cell($colW,pt2mm($contentFontSize),"$byline");
  $pdf->Ln();
  $pdf->SetX($marginL);
  $pdf->Cell($colW,pt2mm($contentFontSize),"$title");

  $creditEnd = $imageStart+2*(pt2mm($contentFontSize));
} else {
  $creditEnd = $imageStart;
}
/* End Story Byline */

$story = stripslashes($story01);

if($section_id = 6) {
  $storyARR = explode("</p><p>",$story);
  foreach($storyARR as $key => $value) {
    $storyARR[$key] = ltrim($storyARR[$key]);
  } 
  $story = join("\n",$storyARR);
}
$story = eregi_replace("\n\n","  ",$story);

/*
$pdf->SetTextColor(10,200,10);
$sizeofLine = pt2mm($contentFontSize);
$length = $pdf->GetStringWidth($story);
	// $maxline = floor($storyBoxHeight/$sizeofLine);
$lines = ceil($length/$colW);
$totalCols = ceil($useHeight/($lines*$sizeofLine));
$pdf->Write($contentFontSize,"[$lines]");
$pdf->Write($contentFontSize," [$totalCols]");
$pdf->Write($contentFontSize,$pdf->GetStringWidth($story)); */

$story = eregi_replace('<p>','',$story);
$story = eregi_replace('</p>','',$story);

$story = eregi_replace('<h3>','',$story);
$story = eregi_replace('</h3>','',$story);
$story = eregi_replace('<h4>','',$story);
$story = eregi_replace('</h4>','',$story);
$story = eregi_replace('<h5>','',$story);
$story = eregi_replace('</h5>','',$story);
$story = eregi_replace('<ul>','',$story);
$story = eregi_replace('</ul>','',$story);
$story = eregi_replace('<ol>','',$story);
$story = eregi_replace('</ol>','',$story);
$story = eregi_replace('<li>','',$story);
$story = eregi_replace('</li>','',$story);
$story = eregi_replace('<b>','',$story);
$story = eregi_replace('</b>','',$story);
$story = eregi_replace('<br/>','',$story);
$story = eregi_replace('<BR>','',$story);
$story = eregi_replace('<p class="cbbhead">','',$story);
$story = eregi_replace('<p class="headsub">','',$story);
$story = eregi_replace('<P class=cbbhead>','',$story);
$story = eregi_replace('<P class=byline>','',$story);
$story = eregi_replace('<EM>','',$story);
$story = eregi_replace('</EM>','',$story);

$story = eregi_replace('&nbsp;',' ',$story);
$story = eregi_replace('&lsquo;',"'",$story);
$story = eregi_replace('&rsquo;',"'",$story);
$story = eregi_replace('&ldquo;','"',$story);
$story = eregi_replace('&rdquo;','"',$story);
$story = eregi_replace('&hellip;','-',$story);

/* We are going to build a 3 column paragraph system 
   This system will use a for($col = 0;$col < $numCols;$col++) {
   to make sure the text is set. Each loop will add the column 
   Width (colW) and the gutter to get to the next column. On the
   last column, we start posY under the photo/cutline box */

/*  $lines+=1; // I find if I don't add 1, frequently the last line has probs
 $story=$pdf->MultiCell(100,5,$story,0,'J',0,$lines);
 $story=$pdf->MultiCell(0,5,$story,0,'J',0,0); */

/* Debug */
/*
$pdf->Ln();
$pdf->Output();
die('done');
*/

/* Start Actual Article */
$pdf->SetFont($contentFont,'',$contentFontSize);
$pdf->SetTextColor(0,0,0);

$safety   = 0;
$page     = 1;
$colLines = 0;
$limit    = 0;

while($story) {
// Each piece of the story needs the following:
  // column position
  // storyBoxHeight (useHeight - margin for top, headline, subline or image)
  // which page we are on.
  // NOTE: we should check the maxline v. the number of required lines
  //       before we start putting the stories onto the page. This calc
  //       will allow us to "balance" the end of the page, ensuring
  //       that all columns are the same length.
  // number of lines in the story / number of lines available on page
  // (removing space for byline, subhead and image space). 
  // if the number of story lines is greater than a page, no balance on 
  // the first page is required. If it is shorter, we run a calculation
  // that will give us the number of max lines for each column to ensure
  // an even bottom. If the bottom suffers a large white space because of
  // the length of the story, then we will put in a 'house ad' on the page.
  // NOTE: The number of lines when there is a photo has to exceed the line
  // height of the photo * 3, or only the first 2 columns will be filled.

  for($curCol = 0;$curCol < $numCol;$curCol++) {
        $storyBoxHeight = 0;
	$sizeofLine = pt2mm($contentFontSize);

        switch ($curCol) {
          case 0:
            $left = $marginL;
            $top = $creditEnd;
            $topMargin = $creditEnd;
            break;
          case 1:
            $left = $marginL+$colW+$gutter;
            $top = $imageStart;
            $topMargin = $imageStart;
            break;
          case 2;
            if($image) {
              $image_end += 2;
            }
            $left = $marginL+2*$colW+2*$gutter;
            $top = $image_end;
            $topMargin = $image_end;
            break;
        }

        if($page > 1) {
          $top       = $marginT+$noteSize+$gutter; // From top of page
          $topMargin = 2*$noteSize+2*$gutter; // Margin *inside* the useHeight
        }

        $pdf->SetXY($left,$top);
	$storyBoxHeight = $useHeight - $topMargin;
	$maxline = floor($storyBoxHeight/$sizeofLine);
	if($ad) {
	  $maxline = $maxline - mm2line($adHeight,$contentFontSize);
	}
        if($limit) {
          $maxline = $colLines;
        }

	$story = $pdf->MultiCell($colW,pt2mm($contentFontSize),$story,0,'J',0,$maxline); 

        /* 
	$str = substr($storyRem,0,1); // Take off first char
	// if the starting char is a newline or return, we want to chop it
	if($str == '\n' || $str == '\r' ) {
	  $storyRem = substr($storyRem,1,strlen($storyRem)-1); // remove the first char
	  $storyRem = "[extra newline] ".$storyRem; // DEBUG
	}
	$storyRem = $pdf->MultiCell($colW,pt2mm($contentFontSize),$storyRem,0,'J',0,$maxline); // Second Column
        */
  }
  if($story) {
    $pdf->AddPage();
    $page++;

    $top       = $marginT+$noteSize+$gutter; // From top of page
    $topMargin = 2*$noteSize+2*$gutter; // Margin *inside* the useHeight
    $storyBoxHeight = $useHeight - $topMargin;
    $maxline = floor($storyBoxHeight/$sizeofLine);

    $lines = $pdf->GetStringWidth($story);
  
    if($lines < $maxline*3) {
      $colLines = ceil($lines/3); 
      $limit = 1;
    } 
    // $story = NULL;
  }
  $safety++;
  if($safety > 5) {
   die("infinite loop");
  }
}

if($ad) {
  $startHeight = $mmHeight - ($noteSize+$marginB+$adHeight+$gutter);

  $pdf->SetFillColor(110,110,110);
  // $pdf->SetXY($marginL,$startHeight); 
  $pdf->Rect($marginL,$startHeight,$useWidth,$adHeight,'DF');
}

$pdf->Ln();
$pdf->Output();
?>
