<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-4";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$tax_cnum=$_shopdata->tax_cnum;
$tax_cname=$_shopdata->tax_cname;
$tax_cowner=$_shopdata->tax_cowner;
$tax_caddr=$_shopdata->tax_caddr;
$tax_ctel=$_shopdata->tax_ctel;
$tax_type=$_shopdata->tax_type;
$tax_rate=$_shopdata->tax_rate;
//$tax_mid=$_shopdata->tax_mid;
//$tax_tid=$_shopdata->tax_tid;


$mode=$_POST["mode"];
if($mode=="update") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$up_tax_cnum1=$_POST["up_tax_cnum1"];
	$up_tax_cnum2=$_POST["up_tax_cnum2"];
	$up_tax_cnum3=$_POST["up_tax_cnum3"];
	$up_tax_cname=$_POST["up_tax_cname"];
	$up_tax_cowner=$_POST["up_tax_cowner"];
	$up_tax_caddr=$_POST["up_tax_caddr"];
	$up_tax_ctel1=$_POST["up_tax_ctel1"];
	$up_tax_ctel2=$_POST["up_tax_ctel2"];
	$up_tax_ctel3=$_POST["up_tax_ctel3"];
	$up_tax_type=$_POST["up_tax_type"];
	$up_tax_rate=$_POST["up_tax_rate"];
	//$up_tax_mid=$_POST["up_tax_mid"];
	//$tax_tid=$_POST["tax_tid"];

	$up_tax_cnum="";
	$up_tax_ctel="";
	if(strlen($up_tax_cnum1)==3 && strlen($up_tax_cnum2)==2 && strlen($up_tax_cnum3)==5) {
		$up_tax_cnum=$up_tax_cnum1.$up_tax_cnum2.$up_tax_cnum3;
	}
	if(ord($up_tax_ctel1) && ord($up_tax_ctel2) && ord($up_tax_ctel3)) {
		$up_tax_ctel=$up_tax_ctel1."-{$up_tax_ctel2}-".$up_tax_ctel3;
	}

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "tax_cnum	= '{$up_tax_cnum}', ";
	$sql.= "tax_cname	= '{$up_tax_cname}', ";
	$sql.= "tax_cowner	= '{$up_tax_cowner}', ";
	$sql.= "tax_caddr	= '{$up_tax_caddr}', ";
	$sql.= "tax_ctel	= '{$up_tax_ctel}', ";
	$sql.= "tax_type	= '{$up_tax_type}', ";
	$sql.= "tax_rate	= '{$up_tax_rate}' ";
	# KCP는 mid, tid를 사용안한다
	//$sql.= "tax_mid		= '{$up_tax_mid}', ";
	//$sql.= "tax_tid		= '{$up_tax_tid}' ";

	
	if(pmysql_query($sql,get_db_conn())) {
		$tax_cnum=$up_tax_cnum;
		$tax_cname=$up_tax_cname;
		$tax_cowner=$up_tax_cowner;
		$tax_caddr=$up_tax_caddr;
		$tax_ctel=$up_tax_ctel;
		$tax_type=$up_tax_type;
		$tax_rate=$up_tax_rate;
		//$tax_mid=$up_tax_mid;
		//$tax_tid=$up_tax_tid;

		$onload="<script>window.onload=function(){ alert('현금영수증 환경설정이 완료되었습니다.'); }</script>";
		DeleteCache("tblshopinfo.cache");
	}
}

$tax_cnum1=substr($tax_cnum,0,3);
$tax_cnum2=substr($tax_cnum,3,2);
$tax_cnum3=substr($tax_cnum,5,5);

$arr_ctel=explode("-",$tax_ctel);
$tax_ctel1=$arr_ctel[0];
$tax_ctel2=$arr_ctel[1];
$tax_ctel3=$arr_ctel[2 ];

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
/*
	if(document.form1.up_tax_mid.value.length==0) {
		alert("올더게이트 에서 발급받은 가맹점 ID를 입력하세요.");
		document.form1.up_tax_mid.focus();
		return;
	}
*/
//	if(document.form1.up_tax_tid.value.length==0) {
//		alert("올더게이트에서 발급받은 TID를 입력하세요.");
//		document.form1.up_tax_tid.focus();
//		return;
//	}
	if(document.form1.up_tax_cnum1.value.length!=3 || document.form1.up_tax_cnum2.value.length!=2 || document.form1.up_tax_cnum3.value.length!=5) {
		alert("사업자등록번호가 잘못되었습니다.");
		document.form1.up_tax_cnum1.focus();
		return;
	}
	if(!chkBizNo(document.form1.up_tax_cnum1.value+""+document.form1.up_tax_cnum2.value+""+document.form1.up_tax_cnum3.value)) {
		alert("사업자등록번호가 잘못되었습니다.");
		return;
	}
	if(document.form1.up_tax_cname.value.length==0) {
		alert("가맹점 상호명을 정확히 입력하세요.");
		document.form1.up_tax_cname.focus();
		return;
	}
	if(document.form1.up_tax_cowner.value.length==0) {
		alert("대표자명을 정확히 입력하세요.");
		document.form1.up_tax_cowner.focus();
		return;
	}
	if(document.form1.up_tax_caddr.value.length==0) {
		alert("사업장 주소를 정확히 입력하세요. (우편번호 제외)");
		document.form1.up_tax_caddr.focus();
		return;
	}
	if(document.form1.up_tax_ctel1.value.length==0 || document.form1.up_tax_ctel2.value.length==0 || document.form1.up_tax_ctel3.value.length==0) {
		alert("사업자 전화번호를 정확히 입력하세요.");
		document.form1.up_tax_ctel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_tax_ctel1.value) || !IsNumeric(document.form1.up_tax_ctel2.value) || !IsNumeric(document.form1.up_tax_ctel3.value)) {
		alert("사업자 전화번호를 정확히 입력하세요.");
		document.form1.up_tax_ctel1.focus();
		return;
	}
	if(document.form1.up_tax_type[0].checked!=true && document.form1.up_tax_type[1].checked!=true && document.form1.up_tax_type[2].checked!=true) {
		alert("현금영수증 발급방법을 선택하세요.");
		document.form1.up_tax_type[2].focus();
		return;
	}
	if(document.form1.up_tax_rate[0].checked!=true && document.form1.up_tax_rate[1].checked!=true) {
		alert("사업자 형태를 선택하세요. (하단 메뉴얼 참조)");
		document.form1.up_tax_rate[0].focus();
		return;
	}
	if(confirm("현금영수증 환경설정 정보를 수정하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 현금영수증 관리 &gt; <span>현금영수증 환경설정</span></p></div></div>

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
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">현금영수증 환경설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>현금영수증 발급을 위한 사업자정보를 관리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">현금영수증 환경설정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<!--
				<TR>
					<th><span>MID</span></th>
					<TD class="td_con1"><input type=text name=up_tax_mid value="<?=$tax_mid?>" size=20 maxlength=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>TID</span></th>
					<TD class="td_con1"><input type=text name=up_tax_tid value="<?=$tax_tid?>" size=6 maxlength=6 class="input_selected"></TD>
				</TR>
				-->
				<TR>
					<th><span>사업자등록번호</span></th>
					<TD class="td_con1"><input type=text name=up_tax_cnum1 value="<?=$tax_cnum1?>" size=3 class="input_selected"> - <input type=text name=up_tax_cnum2 value="<?=$tax_cnum2?>" size=2 class="input_selected"> - <input type=text name=up_tax_cnum3 value="<?=$tax_cnum3?>" size=5 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>가맹점 상호</span></th>
					<TD class="td_con1"><input type=text name=up_tax_cname value="<?=$tax_cname?>" size=50 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>대표자명</span></th>
					<TD class="td_con1"><input type=text name=up_tax_cowner value="<?=$tax_cowner?>" size=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>사업자 주소</span></th>
					<TD class="td_con1"><input type=text name=up_tax_caddr value="<?=$tax_caddr?>" size=70 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>사업자 전화번호</span></th>
					<TD class="td_con1"><input type=text name=up_tax_ctel1 value="<?=$tax_ctel1?>" size=3 maxlength=3 class="input_selected" onkeyup="strnumkeyup(this)"> - <input type=text name=up_tax_ctel2 value="<?=$tax_ctel2?>" size=4 maxlength=4 class="input_selected" onkeyup="strnumkeyup(this)"> - <input type=text name=up_tax_ctel3 value="<?=$tax_ctel3?>" size=4 maxlength=4 class="input_selected" onkeyup="strnumkeyup(this)"></TD>
				</TR>
				<TR>
					<th><span>발급방법</span></th>
					<TD class="td_con1"><input type=radio id="idx_tax_type0" name=up_tax_type value="Y"<?php if($tax_type=="Y")echo " checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tax_type0>자동발급</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tax_type1" name=up_tax_type value="A"<?php if($tax_type=="A")echo " checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tax_type1>수동발급</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tax_type2" name=up_tax_type value="N"<?php if($tax_type=="N")echo " checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tax_type2>사용안함</label></TD>
				</TR>
				<TR>
					<th><span>사업자형태</span></th>
					<TD class="td_con1"><input type=radio id="idx_tax_rate0" name=up_tax_rate value="10"<?php if($tax_rate=="10")echo " checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tax_rate0>일반과세사업자</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tax_rate1" name=up_tax_rate value="0"<?php if($tax_rate=="0")echo " checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tax_rate1>일반면세/간이사업자</label></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><p><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></p></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>현금영수증 환경설정</p></div>
					<!--<dl>
						<dt><span>하단 표기 내용</span></dt>
						<dd>
							- 아래 절차로 신청해 주시면 현금영수증 서비스를 셋팅해 드립니다.<br />
							현금영수증 가맹점 신청서 작성(<a href="http://www.allthegate.com/ags/add/add_08.jsp" target="_blank">http://www.allthegate.com/ags/add/add_08.jsp</a>)<br />
							ㆍ올더게이트 전자결제 서비스 미이용 가맹점만 해당됩니다.<br />
							ㆍ기존 올더게이트 전자결제 서비스 이용업체는 신청서를 따로 작성하실 필요가 없습니다.<br />
							- 일반 과세사업자의 경우는 구매금액중 10%를 부가세로 신고합니다.
						</dd>
					</dl>-->
					<dl>
						<dt><span>발급방법</span></dt>
						<dd>
							- <span class="point_c1">자동발급</span> : 쇼핑몰 운영자의 입금확인/주문취소 단계에서 자동으로 현금영수증을 신청하신 고객에게 현금영수증이 발급/취소됩니다.<br />
							<span style="padding:0px 35px">&nbsp;</span>쇼핑몰 운영자의 입금완료 확인 후 익일 반영 및 조회 됩니다.<br />
							- <span class="point_c1">수동발급</span> : 상점에서 현금영수증 신청건별로 [발급] 버튼을 눌러야만 익일 국세청으로 전송됩니다.(발급취소시에도 수동으로 적용)
						</dd>
					</dl>
					<!--
					<dl>
						<dt><span>사업자형태</span></dt>
						<dd>
							- 일반과세사업자 : 사업자등록증상 일반과세사업자 / 과세물품 판매<br />
							- 일반면세/간이사업자 : 사업자등록증상 면세/간이 사업자<br />
							- 법인사업자 : 판매물품이 과세이면 일반과세사업자, 면세이면 일반면세/간이사업자
						</dd>
					</dl>
					-->
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
<?=$onload?>
<?php 
include("copyright.php");
