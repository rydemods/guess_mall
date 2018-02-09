<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_estimate_ok=$_POST["up_estimate_ok"];
$up_estimate_window=$_POST["up_estimate_window"];
$up_estimate_msg=$_POST["up_estimate_msg"];

if ($type=="up") {
	if($up_estimate_window=="O" && $up_estimate_ok=="Y") $up_estimate_ok="O";
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "estimate_ok		= '{$up_estimate_ok}', ";
	$sql.= "estimate_msg	= '{$up_estimate_msg}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('상품 견적서 기능 설정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT estimate_ok,estimate_msg FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$estimate_ok=$row->estimate_ok;
	$estimate_msg=$row->estimate_msg;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="JavaScript">
function CheckForm() {
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.up_estimate_msg.value=sHTML;

	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품 견적서 기능설정</span></p></div></div>

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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 견적서 기능설정</div>
				</td>
			</tr>
            <tr>
				<td height="20"></td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 쇼핑몰의 상품견적서 기능을 설정할 수 있습니다.</li>
                            <li>2) <a href="javascript:parent.topframe.GoMenu(4,'product_estimate.php');"><font class="font_blue">상품관리 > 사은품/견적/기타관리 > 견적서 상품 등록/관리</font></a> 에서 견적서에 상품을 등록 할 수 있습니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<th><span>견적서 사용 설정 여부</span></th>
					<TD class="td_con1"><input type=radio id="idx_estimate_ok1" name=up_estimate_ok value="Y" <?php if($estimate_ok == "Y" || $estimate_ok=="O" ) echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_estimate_ok1>견적서 기능 사용</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=radio id="idx_estimate_ok2" name=up_estimate_ok value="N" <?php if($estimate_ok == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_estimate_ok2>견적서 기능 미사용</label></TD>
				</TR>
                <tr>
                	<th><span>디스플레이 방식 선택</span></th>
                    <td class="td_con1"><input type=radio id="idx_estimate_window2" name=up_estimate_window value="O" <?php if($estimate_ok == "O") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_estimate_window2>페이지 본문에 출력(권장)</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_estimate_window1" name=up_estimate_window value="Y" <?php if($estimate_ok == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_estimate_window1>팝업으로 출력</label></td>
                </tr>
				</TABLE>
				</div>
                </td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">견적서 비고 입력</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 견적서의 비고란에 출력할 문구를 입력하세요.</li>
                            <li>2) HTML이나 이미지 삽입이 가능합니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			<tr>
				<td><textarea name=up_estimate_msg rows=15 style="width:100%" wrap=off class="textarea" id="ir1"><?=$estimate_msg?></textarea></td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>디자인 내용 입력 안내</span></dt>
							<dd>Text &nbsp;/ 부분 html을 사용 할 경우 &nbsp;줄바꿈(br)은 엔터(Enter]키를 이용하시면 됩니다.<br>&nbsp;&nbsp;&nbsp;부분 html 예)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=000.jpg&gt; ↘ (Enter)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[MAINIMG] ↘ (Enter)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;쇼핑몰을 방문해주셔서 감사합니다.</dd>
							
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
						</dl>												
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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

<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	}, 
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>

<?=$onload?>
<?php 
include("copyright.php");
