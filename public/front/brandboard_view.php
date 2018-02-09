<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$board_code = $_GET['board_code'];
$board_num = $_GET['board_num'];
$gotopage = $_GET['gotopage'];
$block = $_GET['block'];

function boardView($board_num){
	//보드 불러오기
	$sql = "select * from tblbrand_board where board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	$boardView = pmysql_fetch_object($result);
	pmysql_free_result($result);
	//보드 카운트
	$cntSql = "UPDATE tblbrand_board SET count = count+1 WHERE board_num={$board_num}";
	pmysql_query($cntSql,get_db_conn());
	
	return $boardView;
}

function boardItem($board_num){
	$sql = "select * from tblbrand_boarditem a join tblproduct b on a.productcode=b.productcode where a.board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$boardItem[] = $row;
	}
	pmysql_free_result($result);
	return $boardItem;
}

function boardNext($board_num,$board_code){
	$linkText = "";
	if($board_code){
		$sql = "SELECT * FROM tblbrand_board WHERE board_code = {$board_code} AND board_num < {$board_num} ORDER BY date DESC LIMIT 1";
	}else{
		$sql = "SELECT * FROM tblbrand_board WHERE board_num < {$board_num} ORDER BY date DESC LIMIT 1";
	}
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_array($result) ) {
		$linkText[link] = "?board_code=".$row[board_code]."&board_num=".$row[board_num];
		$linkText[date] = substr($row[date],0,4)."-".substr($row[date],4,2)."-".substr($row[date],6,2);
		$linkText[board_title] = $row[board_title];
	}
	pmysql_free_result($result);
	return $linkText;
}

function boardPrev($board_num,$board_code){
	$linkText = "";
	if($board_code){
		$sql = "SELECT * FROM tblbrand_board WHERE board_code = {$board_code} AND board_num > {$board_num} ORDER BY date DESC LIMIT 1";
	}else{
		$sql = "SELECT * FROM tblbrand_board WHERE board_num > {$board_num} ORDER BY date DESC LIMIT 1";
	}
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_array($result) ) {
		$linkText[link] = "?board_code=".$row[board_code]."&board_num=".$row[board_num];
		$linkText[date] = substr($row[date],0,4)."-".substr($row[date],4,2)."-".substr($row[date],6,2);
		$linkText[board_title] = $row[board_title];
	}
	pmysql_free_result($result);
	return $linkText;
}
?>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include($Dir.TempletDir."/brandboard/boardview_TEM001.php") ?>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
