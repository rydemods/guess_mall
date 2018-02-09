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

$sql2 = "SELECT join_msg, order_msg, orderend_msg FROM tblshopinfo ";
$result = pmysql_query($sql2,get_db_conn());
$data = pmysql_fetch_object($result);
$tmp_order_msg = $data->order_msg;
$tmp_order_msg2 = explode("=",$tmp_order_msg);
$order_msg = $tmp_order_msg2[0];
$delivery = $tmp_order_msg2[1];
$hname = $tmp_order_msg2[2];
$orderend_msg = $data->orderend_msg;
$join_msg = $data->join_msg;
pmysql_free_result($result);

$type=$_POST["type"];
$up_join_msg=$_POST["up_join_msg"];
$up_order_msg=$_POST["up_order_msg"];
$up_orderend_msg=$_POST["up_orderend_msg"];

if ($type=="up") {
	$up_order_msg.="=".$delivery;
	$up_order_msg.="=".$hname;

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "join_msg		= '{$up_join_msg}', ";
	$sql.= "order_msg		= '{$up_order_msg}', ";
	$sql.= "orderend_msg	= '{$up_orderend_msg}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('정상적으로 등록되었습니다.'); }</script>\n";

	$sql2 = "SELECT join_msg, order_msg, orderend_msg FROM tblshopinfo ";
	$result = pmysql_query($sql2,get_db_conn());
	if ($data = pmysql_fetch_object($result)) {
		$tmp_order_msg = $data->order_msg;
		$tmp_order_msg2 = explode("=",$tmp_order_msg);
		$order_msg = $tmp_order_msg2[0];
		$delivery = $tmp_order_msg2[1];
		$hname = $tmp_order_msg2[2];
		$orderend_msg = $data->orderend_msg;
		$join_msg = $data->join_msg;
	}
	pmysql_free_result($result);
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.up_join_msg.value=sHTML;

	var sHTML2 = oEditors.getById["ir2"].getIR();
	document.form1.up_order_msg.value=sHTML2;

	var sHTML3 = oEditors.getById["ir3"].getIR();
	document.form1.up_orderend_msg.value=sHTML3;

	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>회원가입/주문 안내문구</span></p></div></div>

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
					<div class="title_depth3">회원가입/주문 안내문구</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원가입 및 주문시 쇼핑몰 운영자의 메세지를 등록할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">회원가입 감사 메일 메세지</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1)회원가입 완료 안내 메일 하단에 표기되는 감사 메제시 입니다. (한글 500자 이내) </li>
						<li>2) 메일 내용과 디자인은 템플릿에 따라 다르며 개별디자인도 가능합니다.(매뉴얼 참조) </li>
						<li>3) HTML로만 사용 가능합니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td><textarea name=up_join_msg cols=86 rows=12 wrap=off style="width:100%" class="textarea" id="ir1"><?=$join_msg?></textarea></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문서작성 안내 메세지</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 주문서 화면의 가장 하단에 표기되는 안내문구입니다.(한글 500자 이내)</li>
						<li>2) 주문서화면 디자인은 템플릿에 따라 다르며, 개별디자인은 제공되지 않습니다. </li>
						<li>3) HTML로만 사용 가능합니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td><textarea name=up_order_msg cols=86 rows=12 wrap=off style="width:100%" class="textarea" id="ir2"><?=$order_msg?></textarea></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문완료 감사 메세지</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 주문서 완료 페이지와 완료확인 안내 메일 가장 하단에 표기되는 안내문구입니다.(한글 500자 이내)</li>
						<li>2) 메일 내용과 디자인은 템플릿에 따라 다르며 개별디자인도 가능합니다. 단, 주문완료 페이지는 안됨.(매뉴얼 참조)</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td><textarea name=up_orderend_msg rows=12 wrap=off style="width:100%" class="textarea" id="ir3"><?=$orderend_msg?></textarea></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배너 개별디자인</span></dt>
							<dd>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_sendmail.php');"><span class="font_blue">디자인관리 > 템플릿 - 페이지 본문 > 메일관련 화면 템플릿</span></a> 에서 템플릿을 선택할 수 있습니다.<br>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_eachsendmail.php');"><span class="font_blue">디자인관리 > 개별디자인 - 페이지 본문 > 메일 화면 꾸미기  </span></a> 에서 직접 HTML로 디자인할 수 있습니다.</a><br>
								- <span class="font_orange">주문서화면은 개별디자인이 제공되지 않습니다.</span>
							</dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
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

<script type="text/javascript">
	var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2SkinNoImg.html",	
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
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir2",
		sSkinURI: "../SE2/SmartEditor2SkinNoImg.html",	
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
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir3",
		sSkinURI: "../SE2/SmartEditor2SkinNoImg.html",	
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
