<?php // hspark
$Dir = "../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if (ord($_ShopInfo->getId()) == 0) exit;

$date     = date("YmdH");
$prevdate = date("YmdH",strtotime("-9 hour"));

Header("Content-type: image/gif");
$im       = ImagecreateFromGif("img/main_graph2.gif");
$im2      = ImagecreateFromGif("img/graph_dot.gif");
$im3      = ImagecreateFromGif("img/graph_dot.gif");
$im4      = ImagecreateFromGif("img/graph_dot.gif");
$white    = ImageColorAllocate($im,255,255,255);
$dot      = ImageColorAllocate($im,255,60,0);
$poly     = ImageColorAllocate($im,252,212,166);
$black    = ImageColorAllocate($im,0,0,0);
$blue     = ImageColorAllocate($im,0,0,255);
$red      = ImageColorAllocate($im,255,0,0);

$topvalue = 0;

$sql      = "SELECT cnt,SUBSTR(date,9,2) as hour FROM tblcounterorder
WHERE (date>='{$prevdate}' AND date<='{$date}') ";
$result   = pmysql_query($sql,get_db_conn());
while ($row = pmysql_fetch_object($result)) {
    $time[$row->hour] = $row->cnt;
    if ($topvalue < $row->cnt) $topvalue = $row->cnt;
}
pmysql_free_result($result);

if ($topvalue < 4)        $max = 4;
elseif ($topvalue < 8)    $max = 8;
elseif ($topvalue < 20)   $max = 20;
elseif ($topvalue < 40)   $max = 40;
elseif ($topvalue < 80)   $max = 80;
elseif ($topvalue < 100)  $max = 100;
elseif ($topvalue < 200)  $max = 200;
elseif ($topvalue < 400)  $max = 400;
elseif ($topvalue < 800)  $max = 800;
elseif ($topvalue < 1000) $max = 1000;
elseif ($topvalue < 2000) $max = 2000;
elseif ($topvalue < 4000) $max = 4000;
else                    $max   = 8000;

$value = ($max / 4);
$top   = $max + $value;

for ($i = 0;$i <= 3;$i++) {
    $num     = $value * (4 - $i);
    $ynumber = 66 + ((($i / 2) - 1) * 36);
    $xnumber = 35 - strlen(number_format($num)) * 3;
    //imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
    imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
}


$date = $prevdate;
$count= 0;
for ($i = 0;$i < 10;$i++) {
    $curdate = sprintf("%02d",date("H",strtotime("-9 hour")) + $i);

    $xnumber = 40 + (($i / 2) * 52);
    if ($i % 2 == 1) {
        //imageString($im,2,$xnumber,100,$curdate,$black);
        imagettftext($im,6,0,$xnumber,108,$black,"font/kroeger.ttf",$curdate);
    }
    $x = $xnumber + 6;
    //$y = 97 - ($time[$curdate] / $top) * 82;
    $y = 97 - ceil(($time[$curdate] / $top) * 86);
    if (strcmp($topvalue,$time[$curdate]) == 0) {
        //imageString($im,2,$x - 5,$y - 15,$topvalue,$red);
        imagettftext($im,6,0,$x - 5,$y - 5,$red,"font/kroeger.ttf",$topvalue);
    }
    if ($time[$curdate] > 0) {
        $height = 97 - ceil($y);
        if ($i == 0) ImageCopyResized($im,$im3,$x + 2,$y,0,0,5,$height,5,1);
        elseif ($i <> 9) ImageCopyResized($im,$im2,$x - 2,$y,0,0,7,$height,7,1);
        else ImageCopyResized($im,$im4,$x - 4,$y,0,0,5,$height,5,1);
    }
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
imageDestroy($im3);
imageDestroy($im4);
