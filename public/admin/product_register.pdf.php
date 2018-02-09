<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_REQUEST); 
$mode = $_REQUEST["mode"];
$code = $_REQUEST["code"];
$productcode = $_REQUEST["productcode"];
$pdfPATH = $Dir.DataDir."pdf/";

if($mode == "del" ){
	unlink($pdfPATH.$code.".pdf");
	$onload = "<script>alert('삭제되었습니다.');self.close();</script>";
}elseif($mode == "download"){
	$Path = $pdfPATH.$productcode.".pdf";
	if (is_file($Path)) { 
		$len = filesize($Path); 
		$filename = basename($Path);
		
		header("Pragma: public"); 
		header("Expires: 0"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/pdf; charset=utf-8"); 
		$pdf_name = pmysql_fetch(pmysql_query("SELECT pdf_oriname FROM tblproduct WHERE productcode = '{$productcode}'"));
		header("Content-Disposition: attachment; filename=".$pdf_name[0] ); 
		header("Content-Length: ".$len); 
		@readfile($Path); 
} else { 
		echo "<script>alert('해당 파일이나 경로가 존재하지 않습니다.'); history.back();</script>";
	}     
}

?>

