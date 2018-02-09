<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");

$board_num = $_REQUEST['num'];

if(ord($board_num)==0) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}

include ($Dir.MainDir.$_data->menu_type.".php");


#####게시물
$sql = "SELECT * FROM tblboard where num='{$board_num}'";

$res_event = pmysql_query($sql,get_db_conn());
$data = pmysql_fetch_array($res_event);

$reg_date = date("Y-m-d",$data['writetime']);//게시물 날짜
pmysql_free_result($res_event);
#####이전 게시물
if($data['prev_no']){
	$sql_prev = "SELECT title,num,writetime FROM tblboard where num='{$data['prev_no']}'";
	$res_prev = pmysql_query($sql_prev,get_db_conn());
	
	$data_prev = pmysql_fetch_array($res_prev);
	$reg_date_prev = date("Y-m-d",$data_prev['writetime']);//게시물 날짜
}

#####다음 게시물
if($data['next_no']){
	$sql_next = "SELECT title,num,writetime FROM tblboard where num='{$data['next_no']}'";
	$res_next = pmysql_query($sql_next,get_db_conn());
	
	$data_next = pmysql_fetch_array($res_next);
	$reg_date_next = date("Y-m-d",$data_next['writetime']);//게시물 날짜
}

//exdebug($data_next);

#####좌측 메뉴의 class="on"을 위한 페이지 코드
$page_code = "event";

?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function goView(num){
	document.form3.num.value=num;
	document.form3.submit();
}
//-->
</SCRIPT>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
<?php 

include($Dir.TempletDir."event/event_view_tem001.php");
	
?>
	</td>
</tr>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=category_code value="<?=$category_code?>">
<input type=hidden name=searchtxt value="<?=$searchtxt?>">
<input type=hidden name=tab value="<?=$tab?>">
</form>

<form name=form3 method="POST" action="event_view.php">
<input type=hidden name=num value="">
</form>
</table>



<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>