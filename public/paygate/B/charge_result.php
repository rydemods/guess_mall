<?php
$Dir="../../";
include_once($Dir."lib/init.php");

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$oid;

echo "<script>";
echo "location.href=\"http://".$return_resurl."\";\n";
echo "window.close();";
echo "</script>";
exit;
