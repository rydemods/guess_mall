<?php // hspark
$Dir = "../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if (ord($_ShopInfo->getId()) == 0) exit;

$type = $_REQUEST["type"];
$date = $_REQUEST["date"];

list($year,$mon,$day) = sscanf($date,'%4s%2s%2s');

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

if ($type == "d") {
    $prevdate = date("Ymd",strtotime("$year-$mon-$day -1 day")).date("H");
    if ($date == date("Ymd")) $date = date("Ymd",strtotime("$year-$mon-$day")).date("H");
    else $date = $date."99";
    $sql  = "SELECT cnt,SUBSTR(date,7,4) as hour FROM tblcounter 
    WHERE (date>='{$prevdate}' AND date<='{$date}') ORDER BY date ";
}elseif ($type == "w") {
    $prevdate = date("Ymd00",strtotime("-7 day"));
    $nextdate = date("Ymd99");
    $sql      = "SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounter 
    WHERE (date<='{$nextdate}' AND date>='{$prevdate}') GROUP BY hour ";
}elseif ($type == "m") {
    $date = date("Ym");
    $sql  = "SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounter 
    WHERE date LIKE '{$date}%' GROUP BY hour ";
}

$result = pmysql_query($sql,get_db_conn());
while ($row = pmysql_fetch_object($result)) {
    $time[$row->hour] = $row->cnt;
    if ($topvalue < $row->cnt) $topvalue = $row->cnt;
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
if ($day == date("d")) $end = "YES";
for ($i = 0;$i < 24;$i++) {
    if ($type == "d") {
		$predate = date("d",strtotime("$year-$mon-$day -1 day")).sprintf("%02d",$i);
		$curdate = date("d",strtotime("$year-$mon-$day")).sprintf("%02d",$i);
    }
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
        if ($type == "d") imageline($im,$prevx2 + 1,$prevy2,$x2 + 1,$y2,$dot2);
        if ($type <> "d" || ($type == "d" && ($curhour >= $curdate || $end != "YES"))) imageline($im,$prevx + 1,$prevy,$x + 1,$y,$dot);
    }
    if ($i <> 0 && $i <> 23) {
        if ($type == "d") ImageCopyResized($im,$im3,$x2 - 1,$y2 - 1,0,0,5,5,5,5);
        if ($type <> "d" || ($type == "d" && ($curhour >= $curdate || $end != "YES"))) ImageCopyResized($im,$im2,$x - 1,$y - 1,0,0,5,5,5,5);
    }
    $prevx = $x;$prevy = $y;
    $prevx2= $x2;$prevy2= $y2;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
imageDestroy($im3);
