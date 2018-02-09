<?php
$Dir="../";
include_once($Dir."lib/init.php");

$url=$_REQUEST["url"];
$file_name=$_REQUEST["file_name"];

$attachfileurl=$url.$file_name;

 if(strstr($file_name, 'conf') || strstr($file_name, '.php')){ 
		exit; 
}

if(file_exists($attachfileurl)) {

		$file = $attachfileurl;

	//	if(strpos(" ".$file,"..")!==FALSE) exit;

		Header("Content-Disposition: attachment; filename=$file_name");
		Header("Content-Type: application/octet-stream;");
		Header("Pragma: no-cache");
		Header("Expires: 0");
		Header("Content-type: application/octet-stream");

		readfile($file);

}
