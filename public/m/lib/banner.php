<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$sql = "SELECT * FROM tblbanner ORDER BY date DESC ";
$result=pmysql_query($sql,get_db_conn());
$bannerbody="";
while($row=pmysql_fetch_object($result)) {
	$bannerbody.= "<tr><td align=center><A HREF=\"http".($row->url_type=="S"?"s":"")."://".$row->url."\" target=".$row->target."><img src=\"".$Dir.DataDir."shopimages/banner/".$row->image."\" border=\"".$row->border."\" style = 'max-width:180px;'></A></td></tr>\n";
	//$bannerbody.= "<tr><td height=2></td></tr>\n";
}
pmysql_free_result($result);
if(strlen($bannerbody)>0) {
	$bannerbody = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n".$bannerbody;
	$bannerbody.= "</table>\n";
}
