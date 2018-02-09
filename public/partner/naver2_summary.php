<?php
set_time_limit(0);
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

include("../dbconn.php");
include("../lib/lib.func.php");

$tmp = date("Y-m-d 00:00:00");
mysql_query("delete from ".GD_GOODS_UPDATE_NAVER." where utime < '$tmp'");
$query = "select * from ".GD_GOODS_UPDATE_NAVER." order by no asc";
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
{
	$mapid = $row['mapid'];
	$class = $row['class'];
	$utime = $row['utime'];

	unset($row['no']);
	unset($row['mapid']);
	unset($row['class']);
	unset($row['utime']);

	echo "<<<begin>>>\n";
	echo '<<<mapid>>>'.$mapid."\n";
	foreach($row as $key=>$value)
	{
		if(!is_null($value)) echo '<<<'.$key.'>>>'.$value."\n";
	}
	echo '<<<class>>>'.$class."\n";
	echo '<<<utime>>>'.$utime."\n";
	echo "<<<ftend>>>\n";
}


?>