<?php
$Dir="../";
include_once($Dir."lib/init.php");

$file_name=$_REQUEST["file_name"];
$file_name_ori=$_REQUEST["file_name_ori"];

$attachfileurl=$_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."shopimages/cscenter/".$file_name;
if(file_exists($attachfileurl)) {
	$file = $attachfileurl;

	if(strpos(" ".$file,"..")!==FALSE) exit;

	Header("Content-Disposition: attachment; filename=$file_name_ori");
	Header("Content-Type: application/octet-stream;");
	Header("Pragma: no-cache");
	Header("Expires: 0");
	Header("Content-type: application/octet-stream");

	readfile($file);
}
