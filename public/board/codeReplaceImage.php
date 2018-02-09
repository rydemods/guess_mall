<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id = $_GET['id'];
$div = $_GET['div'] + 0;

$data = pmysql_fetch("select * from tblboard where board = '".$id."' AND num='".$_GET['no']."'");

$old_file	= explode("|",$data['filename']);
$new_file	= explode("|",$data['vfilename']);

$file_name = $old_file[$div];

$dir = (!$_GET['thumbnail']) ? "/data/shopimages/board/$id" : "/data/shopimages/board/$id/t/";

Header("Content-type: ".filetype("$dir/$new_file[$div]"));

if ($fp = fopen($_SERVER[DOCUMENT_ROOT].$dir."/".$new_file[$div], "rb")){
	while (!feof($fp)) {
		$buf = fread($fp, 8196);
		$read = strlen($buf);
		print($buf);
	}
} 
fclose($fp); 

exit();
?>