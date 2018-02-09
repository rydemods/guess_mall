<?php // hspark
$Dir = "../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if (ord($_ShopInfo->getId()) == 0) exit;

$prevdate = $_REQUEST["search_s"];
$nextdate = $_REQUEST["search_e"];
$type = "w";

//list($year,$mon,$day) = sscanf($nextdate,'%4s%2s%2s');

Header("Content-type: image/gif");
$im       = ImagecreateFromGif("img/timevisit_graph.gif");
$im2      = ImagecreateFromGif("img/graph_dot.gif");
$im3      = ImagecreateFromGif("img/graph_dot2.gif");
$white    = ImageColorAllocate($im,255,255,255);
$dot      = ImageColorAllocate($im,255,60,0);
$dot2     = ImageColorAllocate($im,127,127,127);
$poly     = ImageColorAllocate($im,252,212,166);
$black    = ImageColorAllocate($im,0,0,0);
$blue     = ImageColorAllocate($im,0,0,255);
$red      = ImageColorAllocate($im,255,0,0);

$topvalue = 0;

$sql ="SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounter ";
$sql.="WHERE (date >= '{$prevdate}' AND date <= '{$nextdate}') GROUP BY hour ";
$sql.="Order by hour ";
$result = pmysql_query($sql,get_db_conn());
//exdebug($sql);
while ($row = pmysql_fetch_object($result)) {
    $time[$row->hour] = $row->cnt;
    if ($topvalue < $row->cnt) $topvalue = $row->cnt;
}
pmysql_free_result($result);
//exdebug($time);

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

//Y좌표 숫자 만들기
for ($i = 0;$i < 10;$i++) {
    $num     = $value * (10 - $i);
    $ynumber = 60 + ((($i / 2) - 1) * 36);
    $xnumber = 35 - strlen(number_format($num)) * 3;
    //imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
    imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
}

$date    = $prevdate;
$count   = 0;
$curhour = date("dH");
//if ($day == date("d")) $end = "YES";
for ($i = 0;$i < 24;$i++) {

    $x = 45 + (($i / 2) * 52) + 5;    //오늘 X좌표
    $x2= 45 + (($i / 2) * 52) + 5;    //전날 X좌표
    if ($type == "d") {
        $y = 201 - ($time[$curdate] / $top) * 200;    //오늘 Y좌표
        $y2= 201 - ($time[$predate] / $top) * 200;    //전날 Y좌표
    }else {
        $curdate = sprintf("%02d",$i);
        $y       = 201 - ($time[$curdate] / $top) * 200;
    }
    if ($i <> 0) {
        imageline($im,$prevx + 1,$prevy,$x + 1,$y,$dot);
    }
    if ($i <> 0 && $i <> 23) {
        ImageCopyResized($im,$im2,$x - 1,$y - 1,0,0,5,5,5,5);
    }
    $prevx = $x;$prevy = $y;
    $prevx2= $x2;$prevy2= $y2;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
imageDestroy($im3);
