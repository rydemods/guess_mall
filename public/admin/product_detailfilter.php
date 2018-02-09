<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$tmp_filter=explode("#",$_shopdata->filter);
$filter = $tmp_filter[0];
$arfilter = explode("=",$filter);
$review_filter = $tmp_filter[1];

$type=$_POST["type"];
$patten=(array)$_POST["patten"];
$replace=(array)$_POST["replace"];

if ($type=="update") {
	$filter="";
	for($i=0;$i<count($patten);$i++){
		if (strpos($patten[$i],"#") || strpos($patten[$i],"|") || strpos($patten[$i],"") || strpos($replace[$i],"#") || strpos($replace[$i],"|") || strpos($replace[$i],"")) {
			alert_go('입력하신 내용이 『|』나 『#』나 『』문자가 포함되어 등록이 불가능합니다.');
		}
		if(ord($patten[$i])) $filter.="={$patten[$i]}=".$replace[$i];
	}
	$detail_filter=substr($filter,3)."#".$review_filter;
	$sql = "UPDATE tblshopinfo SET filter = '{$detail_filter}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('단어 필터링 정보가 적용되었습니다.');}</script>";

	$tmp_filter=explode("#",$detail_filter);
	$filter = $tmp_filter[0];
	$arfilter = explode("=",$filter);
} else if ($type=="delete") {
	$detail_filter="#".$review_filter;
	$sql = "UPDATE tblshopinfo SET filter = '{$detail_filter}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('단어 필터링 목록 전체를 삭제하였습니다.');}</script>";

	$tmp_filter=explode("#",$detail_filter);
	$filter = $tmp_filter[0];
	$arfilter = explode("=",$filter);
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.type.value="update";
	document.form1.submit();
}

function Delete() {
	document.form1.type.value="delete";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt;<span>상품상세내역 단어 필터링</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품상세내역 단어 필터링</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품상세정보란에 상품상세내역을 단어 필터링을 통해 출력해 주는 기능입니다.</span></div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=></col>
				<col width=50></col>
				<col width=></col>
				<TR align=center>
					<th>검색 단어</th>
					<th>&nbsp;</th>
					<th>수정된 단어</th>
				</TR>
<?php
				for($i=0;$i<20;$i++) {
					$str_class="lineleft";
					if ($i==19) $str_class="linebottomleft";
?>
					<tr>
						<TD><input type=text name="patten[]" maxlength=40 value="<?=$arfilter[$i*2]?>" style="WIDTH: 99%" class=input></td>
						<TD><NOBR><p align="center">&nbsp;<img src="images/btn_next1.gif" border="0"></td>
						<TD><input type=text name="replace[]" maxlength=40 value="<?=$arfilter[$i*2+1]?>" style="WIDTH: 100%" class=input></td>
					</tr>
<?php
				}
?>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>																				
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif"  border="0"></a>&nbsp;&nbsp;<a href="javascript:Delete();"><img src="images/btn_totaldel.gif"  border="0" hspace="2"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품상세내역 단어 필터링</span></dt>
						<dd>
						- 필터링 가능 단어의 수는 최대 20개 까지로 제한되어 있습니다..<br>
						- 필터링은 상품상세내역을 수정하여 출력하는것이 아닌 출력시에만 필터링을 통해서 해당 단어를 바꿔서 출력시킵니다.<br>
						- 단어 입력시 특수문자는 입력하지 마시기 바랍니다.
						</dd>
							
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
