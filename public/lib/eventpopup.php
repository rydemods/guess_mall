<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$curdate=date("Ymd");
$_layerdata=array();
$sql = "SELECT * FROM tbleventpopup WHERE start_date<='".$curdate."' AND end_date>='".$curdate."' AND is_mobile='N' ";
$sql .= " order by num asc " ; //팝업 겹칠때 최근꺼 위로 올라오도록 요청함 
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	if($row->frame_type!="2" && $row->frame_type!="3") {	//팝업창일 경우에만 (레이어 타입 제외)
		$cookiename="eventpopup_".$row->num;
		if($row->end_date!=$_COOKIE[$cookiename]) {
			if($row->scroll_yn=="Y") $scroll="yes";
			else $scroll="no";
			$onload.=" if (\"".$row->end_date."\"!=getCookie(\"".$cookiename."\")) window.open('".$Dir.FrontDir."event.php?num=".$row->num."','event_".$row->num."','left=".$row->x_to.",top=".$row->y_to.",width=".$row->x_size.",height=".$row->y_size.",scrollbars=".$scroll."');\n";
		}
	} else {
		$_layerdata[]=$row;
	}
}
pmysql_free_result($result);
