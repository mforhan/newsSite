<?php
// NOTE: 250px is 88.19mm
include("application.inc.php");

// A new proposed method for pdf generation is as follows:
// 1 - break story into paragraphs, subheads in the story 
//     are then available as separate elements
// 2 - split the pdf into a 3 column page
// 3 - Begin by taking any photo/cutline and marking up
//     space in the pdf. This space is off limits for any
//     other use. 
// 4 - determine size of headline and subhead, place headline
//     over entire page, subhead only in bold at top of column 1
// 5 - flow the page with remaining paragraphs into 1st and 2nd
//     columns.
// 6 - on arrival on 3rd column, move down the page past the photo
//     cutline space. Finish paragraph. 
// 7 - If onto second page, flow all columns.

 // Layout variables 
 $num_columns = '';
 $column_width = '';
 $gutter = '';
 $LR_margins = '';
 $TB_margins = '';
 $story_font = '';
 $story_font_size = '';
 $headline_font = '';
 $headline_font_size = '';
 $subhead_font = '';
 $subhead_font_size = '';
 
 
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

function px2mm($px){
	return $px*25.4/72;
}

class PDF extends FPDF {

// Page header
function Header() {
  $this->SetFont('Times','',8);
  $this->Cell(0,10,'Story from The Okanogan Valley Gazette-Tribune Website (http://www.gazette-tribune.com/)','B',0,'C');
  $this->Ln(20);
}
// Page Footer
function Footer() {
  $this->SetY(-15);
  $this->SetFont('Times','',8);
  $this->Cell(0,10,'All content copyright 2005 Prairie Media Inc. Use without prior permission is prohibited. Page '.$this->PageNo().'/{nb} ','T',0,'C');
}
function Photospot($image,$photoby,$cutline) {
    // Get Photo Info
    $img = ImageCreateFromJPEG($image);
    $imgX = ImageSx($img);
    $imgY = ImageSy($img);
    ImageDestroy($img);

    //Column widths
    // $w=88;
    $w = px2mm($imgX);
    $h = px2mm($imgY);
    $x=117; // This assumes the width of the photo @ 250px (88mm)
    $y=70; // starting top row of photo (in mm)

    //Photo
    $this->Image("$image",$x,$y,$w,$h);
    $this->Ln();

    // photoby
    $newY = $y+$h+5;
    $this->SetXY($x,$newY);
    $this->SetTextColor(100,100,100);
    $this->Cell($w,6,$photoby,0,0,'R');
    $this->Ln();

    // cutline
    $this->SetX($x);
    $this->SetTextColor(0,0,0);
    $this->MultiCell($w,3,"$cutline");
    $this->Ln();
    $newX = $this->GetX();

    //Closure line
    $this->SetX($x);
    $this->Cell($w,0,'','T');
    return $this->GetY(); // Y is the position down the page.
    
}
function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0,$maxline=0)
    {
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

function cellSize($string) {
   $x1 = $this->GetX();
   $y1 = $this->GetY();
   // $story=$pdf->MultiCell(100,5,$story,0,'J',0,35);
   // $this-><MultiCell(w,h,$string,border,align,maxlines);
   // $null = $this->MultiCell(100,5,$string,0,'J',0); 
   $this->Cell(100,5,$string,0,1,'J');
   $x2 = $this->GetX();
   $y2 = $this->GetY();
   $string2 = "x1 [$x1] y1 [$y1] :: x2 [$x2] y2 [$y2]";
   return $string2;
  }

function stringWidth($string) { 
  // $text = "big elephant jumps over the tree";
  $this->Cell(100, 5, $string, 0, 1, 'R'); // cell aligning in 100px block
  $width = $this->GetStringWidth($string);

   // $this->SetX($pdf->GetX() + 101 - $width); // manual align - block minus width
   // $this->Write(5, $string); 
  }
}

if($article_id) {
  $content = retrieve_contentbyID($article_id,'FULL');
} else {
  $content = retrieve_content($section_id,1,'FULL');
}

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

$pdf=new PDF('P','mm','Letter');
$pdf->SetCreator("MySQL2PDF by Michael Forhan");
$pdf->SetTitle("Gazette-Tribune Article for $fulldate");
// $pdf->SetAuthor("Story by [$byline]: Program by [Michael Forhan]");
$pdf->SetAuthor("Michael Forhan");
$pdf->AliasNbPages();
$pdf->AddPage();

if($image) {
  $image = $image;
  $image_end = $pdf->Photospot($image,$photoby,$cutline);
}

//$pdf->SetXY(10,15);
//$pdf->SetFont('Arial','',8);
//$pdf->Cell(0,6,$fulldate);
$pdf->SetXY(10,25);
$pdf->SetFont('Arial','B',20);
$pdf->MultiCell(0,10,"$headline");
$pdf->Ln();
if($subhead) {
  $pdf->SetFont('Arial','BI',16);
  $pdf->MultiCell(0,10,"$subhead");
  $pdf->Ln();
}
$pdf->SetFont('Arial','I',10);
$pdf->SetTextColor(100,100,100);
$pdf->Cell(0,5,"$byline");
$pdf->Ln();
$pdf->Cell(0,5,"$title");
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);

$story01 = stripslashes($story01);

$story = eregi_replace('<p>','',$story01);
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
$story = eregi_replace('<p class="cbbhead">','',$story);
$story = eregi_replace('<p class="headsub">','',$story);

// This forces the story to wrap the text around
// the image space.
// Good Test articles (46,47,42)
if($image) {
 if($subhead) {
   $lines = round($image_end/8);  // I have no idea why these numbers are
 } else {                         // what they are. They seem to work mostly.
   $lines = round($image_end/6.75); 
 } 
 $lines+=1; // I find if I don't add 1, frequently the last line has probs
 $story=$pdf->MultiCell(100,5,$story,0,'J',0,$lines);
 $story=$pdf->MultiCell(0,5,$story,0,'J',0,0);
} else {
 $pdf->MultiCell(0,5,$story);
}

$pdf->Ln();
$pdf->Output();
?>
