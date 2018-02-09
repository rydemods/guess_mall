<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-3";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$prcode=$_POST["prcode"];
if(strlen($prcode)==18) {
	$code=substr($prcode,0,12);
	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="";
var prcode="";

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
		document.form2.action="order_eachsale.list.php";
		document.form2.submit();
	}
	prcode="";
}

</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 224;}
</STYLE>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 장바구니 및 매출 분석 &gt;<span>개별상품 매출분석</span></p></div></div>

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
			<?php include("menu_order.php"); ?>
			</td>
			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">개별상품 매출분석</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>개별상품의 매출정보를 확인하실 수 있습니다.</span></div>
                    <div class="title_depth3_sub"><span>개별상품의 매출정보는 개별상품가 기준으로 매출이 표시됩니다.(한 주문에 여러상품 구매시 사용포인트 정보등이 분리되지 않습니다.)</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품리스트</div>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD><div class="point_title02">상품카테고리 목록</div></TD>
					<TD>&nbsp;</TD>
					<TD><div class="point_title03">등록된 상품목록</span></div></TD>
				</TR>
				<TR>
					<td width="222" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px; border:1px solid #b8b8b8;">
					<DIV class=MsgrScroller id=contentDiv style="width:99%;height:394px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
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
					</TD>
					<TD width="50" align="center"><img src="images/btn_next.gif" border="0" vspace="2"></TD>
					<TD width="458" align="center" valign="top" height="100%" style="padding:5px;" bgcolor="#f8f8f8"><IFRAME name="ListFrame" src="order_eachsale.list.php" width=100% height=390 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td><IFRAME name="AddFrame" id="AddFrame" src="order_eachsale.result.php" width=100% height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>

			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>개별상품 매출분석</span></dt>
							<dd>
								- 주문리스트에 등록되어 있는 주문건을 기준으로 산출되며 배송/반송/미처리로 구분되어 출력됩니다.<br />
								- 카테고리 또는 상품을 선택후 기간별/연령별/지역별/성별/회원별/결제방법별 검색이 가능합니다.<br />
								&nbsp;&nbsp;지역별 검색의 경우 배송정보의 지역을 기준으로 합니다.<br />
								&nbsp;&nbsp;연령별 검색에서 전체연령 검색시 0을 입력하시면 됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>카테고리 및 상품 지정 검색방법</span></dt>
							<dd>
								① 특정 카테고리에 포함된 모든 상품의 매출을 분석시 해당 카테고리를 선택후 조회하기 버튼을 누르시면 됩니다.<br />
								② 특정 상품의 매출을 분석시 해당 상품을 선택후 조회하기 버튼을 누르시면 됩니다.
							</dd>
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
<?php
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY code_a, code_b, code_c, code_d DESC ";
include("codeinit.php");
include("copyright.php");
