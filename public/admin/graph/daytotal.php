<?php // hspark
$Dir = "../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if (ord($_ShopInfo->getId()) == 0) exit;

$date     = $_REQUEST["date"];

list($year,$mon)=sscanf($date,'%4s%2s');

$lastdays = array("0","31","28","31","30","31","30","31","31","30","31","30","31");
$lastdays[2] = date("t",strtotime("$year-02-01"));

Header("Content-type: image/gif");
$im       = ImagecreateFromGif("img/daytotal_graph.gif");
$im2      = ImagecreateFromGif("img/graph_dot.gif");
$im3      = ImagecreateFromGif("img/graph_dot2.gif");
$im4      = ImagecreateFromGif("img/graph_dot.gif");
$im5      = ImagecreateFromGif("img/graph_dot.gif");
$im6      = ImagecreateFromGif("img/graph_dot.gif");
$white    = ImageColorAllocate($im,255,255,255);
$dot      = ImageColorAllocate($im,255,60,0);
$dot2     = ImageColorAllocate($im,127,127,127);
$poly     = ImageColorAllocate($im,252,212,166);
$black    = ImageColorAllocate($im,0,0,0);
$blue     = ImageColorAllocate($im,0,0,255);
$red      = ImageColorAllocate($im,255,0,0);

$topvalue = 0;
$toporder = 0;
$toppage  = 0;

if ($date >= date("Ym",strtotime('-1 month'))) {
    $sql = "SELECT SUBSTR(date,7,2) as day,SUM(cnt) as cnt,SUM(pagecnt) as pagecnt FROM tblcounter
    WHERE date LIKE '{$date}%' GROUP BY day ";
    $sql2= "SELECT SUM(cnt) as cnt,SUBSTR(date,7,2) as day FROM tblcounterorder
    WHERE date LIKE '{$date}%' GROUP BY day ";
}
else {
    $sql = "SELECT SUBSTR(date,7,2) as day,cnt, pagecnt FROM tblcountermonth
    WHERE date LIKE '{$date}%' ";
    $sql2= "SELECT cnt,SUBSTR(date,7,2) as day FROM tblcounterordermonth
    WHERE date LIKE '{$date}%' ";
}
$result = pmysql_query($sql,get_db_conn());
while ($row = pmysql_fetch_object($result)) {
    $time[$row->day] = $row->cnt;
    $page[$row->day] = $row->pagecnt;
    if ($topvalue < $row->cnt) $topvalue = $row->cnt;
    if ($toppage < $row->pagecnt) $toppage = $row->pagecnt;
}
pmysql_free_result($result);

$result = pmysql_query($sql2,get_db_conn());
while ($row = pmysql_fetch_object($result)) {
    $order[$row->day] = $row->cnt;
    if ($toporder < $row->cnt) $toporder = $row->cnt;
}
pmysql_free_result($result);

if ($topvalue < 10) $max = 10;
elseif ($topvalue < 20) $max = 20;
elseif ($topvalue < 30) $max = 30;
elseif ($topvalue < 40) $max = 40;
elseif ($topvalue < 50) $max = 50;
elseif ($topvalue < 60) $max = 60;
elseif ($topvalue < 70) $max = 70;
elseif ($topvalue < 80) $max = 80;
elseif ($topvalue < 90) $max = 90;
elseif ($topvalue < 100) $max = 100;
else {
    $max = ceil(($topvalue * 1.1) / 10) * 10;
}
$value = ($max / 10);
$top   = $max + $value;

if ($toppage < 10) $maxpage = 10;
elseif ($toppage < 20) $maxpage = 20;
elseif ($toppage < 30) $maxpage = 30;
elseif ($toppage < 40) $maxpage = 40;
elseif ($toppage < 50) $maxpage = 50;
elseif ($toppage < 60) $maxpage = 60;
elseif ($toppage < 70) $maxpage = 70;
elseif ($toppage < 80) $maxpage = 80;
elseif ($toppage < 90) $maxpage = 90;
elseif ($toppage < 100) $maxpage = 100;
else {
    $maxpage = ceil(($toppage * 1.1) / 10) * 10;
}
$pvalue = ($maxpage / 10);
$pagetop= $maxpage + $pvalue;

if ($toporder < 10) $maxorder = 10;
elseif ($toporder < 20) $maxorder = 20;
elseif ($toporder < 30) $maxorder = 30;
elseif ($toporder < 40) $maxorder = 40;
elseif ($toporder < 50) $maxorder = 50;
elseif ($toporder < 60) $maxorder = 60;
elseif ($toporder < 70) $maxorder = 70;
elseif ($toporder < 80) $maxorder = 80;
elseif ($toporder < 90) $maxorder = 90;
elseif ($toporder < 100) $maxorder = 100;
else {
    $maxorder = ceil(($toporder * 1.1) / 10) * 10;
}
$orvalue = ($maxorder / 10);
$topor   = $maxorder + $orvalue;

for ($i = 0;$i < 10;$i++) {
    $num     = $value * (10 - $i);
    $num2    = $pvalue * (10 - $i);
    $num3    = $orvalue * (10 - $i);

    $ynumber = 79 + ((($i / 2) - 1) * 36);
    $ynumber2= 354 + ((($i / 2) - 1) * 36);
    $ynumber3= 622 + ((($i / 2) - 1) * 36);

    $xnumber = 35 - strlen(number_format($num)) * 3;
    $xnumber2= 35 - strlen(number_format($num2)) * 3;
    $xnumber3= 35 - strlen(number_format($num3)) * 3;

    //imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
    //imageString($im,2,$xnumber2,$ynumber2,number_format($num2),$black);
    //imageString($im,2,$xnumber3,$ynumber3,number_format($num3),$black);
    imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
    imagettftext($im,6,0,$xnumber2,$ynumber2,$black,"font/kroeger.ttf",number_format($num2));
    imagettftext($im,6,0,$xnumber3,$ynumber3,$black,"font/kroeger.ttf",number_format($num3));
}

$count = 0;
if ($date == date("Ym")) $endday = date("d");
else $endday = $lastdays[(int)$mon];

for ($i = 1;$i <= $endday;$i++) {
    $curday = sprintf("%02d",$i);
    //$x = 43 + (($i / 2) * 32) + 6;
    //$x2 = 43 + (($i / 2) * 32) + 6;
    //$x3=45+(($i/2)*32)+5;

    $x=69+((($i-1)/2)*38);
    $x2=69+((($i-1)/2)*38);
    $x3=69+((($i-1)/2)*38);

    $y=221-($time[$curday]/$top)*200;
    $y3=495-($page[$curday]/$pagetop)*200;
    $y5=763-($order[$curday]/$topor)*200;

    //if($curday>=$curday){
        if($i<>1){
            imageline($im,$prevx+1,$prevy,$x+1,$y,$dot);
            imageline($im,$prevx+1,$prevy3,$x+1,$y3,$dot);
        }
        ImageCopyResized($im,$im2,$x-1,$y-1,0,0,5,5,5,5);
        ImageCopyResized($im,$im2,$x-1,$y3-1,0,0,5,5,5,5);
    //}
    if($order[$curday]>0){
        $height=ceil(763-$y5);
        if($i<>31) ImageCopyResized($im,$im4,$x3-2,$y5,0,0,7,$height,7,1);
        else ImageCopyResized($im,$im6,$x3-4,$y5,0,0,5,$height,5,1);
    }
    $prevx=$x;$prevy=$y;
    $prevy3=$y3;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
imageDestroy($im3);
imageDestroy($im4);
imageDestroy($im5);
imageDestroy($im6);
