<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$eventbody="";
$sql_design="SELECT * FROM tbldesignnewpage WHERE type='leftevent'";
$result_design=pmysql_query($sql_design,get_db_conn());
if($row_design=pmysql_fetch_object($result_design)){
	$designtype=$row_design->code;
	$eventbody = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($designtype==1) {
		$eventbody.= "<tr>\n";
		$eventbody.= "	<td align=center><img src=\"".$Dir.DataDir."shopimages/etc/".$row_design->filename."\"></td>\n";
		$eventbody.= "</tr>";
	}else if($designtype==2){
		$eventbody.= "<tr>\n";
		$eventbody.= "	<td>".$row_design->body."</td>\n";
		$eventbody.= "</tr>";
	}
	$eventbody.= "</table>";
}

