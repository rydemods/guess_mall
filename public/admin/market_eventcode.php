<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(selcode.length==12 && selcode!="000000000000") {
		document.form2.mode.value="";
		document.form2.code.value=selcode;
		document.form2.target="ListFrame";
		document.form2.action="market_eventcode.add.php";
		document.form2.submit();
	} else {
		document.form2.mode.value="";
		document.form2.code.value="";
		document.form2.target="ListFrame";
		document.form2.action="market_eventcode.add.php";
		document.form2.submit();
	}
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>카테고리별 이벤트 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">카테고리별 이벤트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>각 카테고리별 페이지 상단에 이미지 또는 Html 편집을 통해 이벤트를 관리 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="242" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top" >
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">전체 카테고리</div>
							</td>
						</tr>

						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;" class="bd_editer">
								<DIV class=MsgrScroller id=contentDiv style="width=99%;height:394px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
									<DIV id=bodyList>
										<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
										<tr>
											<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
										</tr>
										<tr>
											<!-- 상품카테고리 목록 -->
											<td id="code_list" nowrap valign=top></td>
											<!-- 상품카테고리 목록 끝 -->
										</tr>
										</table>
									</DIV>
								</DIV>
							</td>
						</tr>
						</table>
						</td>
					</tr>

					</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" border="0" hspace="5"><br></td>
					<td width="100%" valign="top" height="100%"><IFRAME name="ListFrame" id="ListFrame" src="market_eventcode.add.php" width=100% height=300 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
				</tr>
				</table>
				</td>
			</tr>

			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>카테고리별 이벤트 관리</span></dt>
							<dd>
								- 이미지 또는 Html 편집을 하시면 각 카테고리 상단을 다양하게 꾸미실 수 있습니다.<br>
								- 카테고리별 이벤트는 "상품 카테고리 템플릿" 사용시에만 출력됩니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_plist.php');"><span class="font_blue">디자인 관리 > 템플릿-메인 및 카테고리 > 상품 카테고리 템플릿</span></a><br>
								- 개별 디자인 사용시 "상품 카테고리 꾸미기"에서 해당 매크로를 이용하시면 출력이 가능합니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_eachplist.php');"><span class="font_blue">디자인 관리 > 개별디자인-페이지 본문 > 상품 카테고리 꾸미기</span></a>
							</dd>
							
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>

			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			</form>
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
<?php
$sql = "SELECT * FROM tblproductcode ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php");
