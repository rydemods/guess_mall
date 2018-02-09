<?php // hspark

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");


####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

if($_POST['chk_detail'] && $_POST['serial_value']){
	$arrSerialValue = explode("&", $_POST['serial_value']);
	foreach($arrSerialValue as $serialVal){
		$serialValue = explode("=", $serialVal);
		$_REQUEST[$serialValue[0]] = str_replace('+', ' ', $serialValue[1]);
	}
}
#########################################################

$mode = $_POST["mode"];
if($mode == "insert"){
	$won = $_POST["won"];
	$reg_date = date("YmdHis");
	$sql = "
		INSERT INTO tblexchangerate
			(
				won
				,reg_date
				,id
				,ip
			)VALUES(
			
				{$won}
				,'{$reg_date}'
				,'{$_ShopInfo->id}'
				,'{$_SERVER[REMOTE_ADDR]}'
			)
	";
	pmysql_query($sql,get_db_conn());
	if(pmysql_error()){
		alert_go("입력이 실패했습니다. 다시입력해 주세요",-1);
	}
}

$sql = "SELECT won FROM tblexchangerate ORDER BY reg_date DESC OFFSET 0 LIMIT 1";
$res = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($res);
pmysql_free_result($res);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
function exchageUp(){
	if(confirm("환율을 입력하시겠습니까?")){
		if($("#won").val().length==0){
			alert("금액을 입력해 주세요");
			return;
		}
		if($("#won").val()<=0){
			alert("금액이 0보다 작습니다.");
			return;
		}
		$("#mode").val("insert");
		$("#frm1").submit();
	}
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>환율 등록</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<form name=form1 id="frm1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" name="mode" id="mode"/>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<div class="title_depth3">환율등록</div>

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>환율등록(USD 기준)</span></th>
							<td>
								<input type="text" name="won" id="won" value="<?=$row->won?>"/> 원
							</td>
						</tr>
					</table>
				</div>
				<p class="ta_c"><a href="javascript:exchageUp();"><img src="img/btn/btn_input02.gif" alt="입력" /></a></p>
				</td>
			</tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?php
include("copyright.php");
?>
<?=$onload?>