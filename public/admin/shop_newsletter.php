<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql2 = "SELECT * FROM tblnewsletter order by date DESC , no DESC";
$result = pmysql_query($sql2,get_db_conn());
$data = pmysql_fetch_object($result);
$tmp_order_msg = $data->html;	// html내용 가져오길..	
$join_msg =  $tmp_order_msg;	//넣을곳 = $join_msg

$type=$_POST["type"];
$up_join_msg=$_POST["up_join_msg"];

if ($type=="up") {
	$sql = "insert into tblnewsletter( html) values('{$up_join_msg}')";

	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('정상적으로 등록되었습니다.');
	}</script>\n";

}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.up_join_msg.value=sHTML;
	document.form1.type.value="up";
	//alert(sHTML);
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>뉴스레터 관리</span></p></div></div>

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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">뉴스레터 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>뉴스레터의 내용을 등록할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">뉴스레터 메일 메세지</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 뉴스레터 내용과 디자인은 템플릿에 따라 다르며 개별디자인도 가능합니다.(매뉴얼 참조) </li>
						<li>2) HTML로만 사용 가능합니다.</li>
					</ul>
				</div>				
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td><textarea name=up_join_msg cols=86 rows=12 wrap=off style="width:100%" class="textarea" id="ir1"><?=$join_msg?></textarea></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>


			<tr>
				<td height="20">&nbsp;</td>
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

<script type="text/javascript">
	var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2onlyhtml.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true ,
			//bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
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
