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
$tax_mid=$_shopdata->tax_mid;
$tax_tid=$_shopdata->tax_tid;

$tax_cnum1=substr($tax_cnum,0,3);
$tax_cnum2=substr($tax_cnum,3,2);
$tax_cnum3=substr($tax_cnum,5,5);

if(ord($tax_cnum)==0) {
	alert_go('현금영수증 환경설정 후 이용하시기 바랍니다.','order_taxsaveabout.php');
}

$mode=$_POST["mode"];
if($mode=="insert") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$up_name=$_POST["up_name"];
	$up_email=$_POST["up_email"];
	$up_productname=$_POST["up_productname"];
	$up_amt=(int)$_POST["up_amt"];
	$up_tr_code=$_POST["up_tr_code"];
	$up_gbn=$_POST["up_gbn"];

	$up_resno1=$_POST["up_resno1"];
	$up_resno2=$_POST["up_resno2"];

	$up_mobile1=$_POST["up_mobile1"];
	$up_mobile2=$_POST["up_mobile2"];
	$up_mobile3=$_POST["up_mobile3"];

	$up_comnum1=$_POST["up_comnum1"];
	$up_comnum2=$_POST["up_comnum2"];
	$up_comnum3=$_POST["up_comnum3"];

	if($tax_rate==10) {
		$up_amt1=$up_amt;
		$up_amt4=floor(($up_amt1/1.1)*0.1);
		$up_amt2=$up_amt1-$up_amt4;
		$up_amt3=0;
	} else {
		$up_amt1=$up_amt;
		$up_amt2=0;
		$up_amt3=0;
		$up_amt4=0;
	}

	if($up_tr_code=="0") {	//개인
		if($up_gbn=="0") {
			$up_id_info=$up_resno1.$up_resno2;	//주민번호
		} else {
			$up_id_info=$up_mobile1.$up_mobile2.$up_mobile3;	//핸드폰번호
		}
	} else {	//사업자
		$up_id_info=$up_comnum1.$up_comnum2.$up_comnum3;	//사업자번호
	}

	$ordercode=unique_id();
	$tsdtime=substr($ordercode,0,14);
	$sql = "INSERT INTO tbltaxsavelist(
	ordercode	,
	tsdtime		,
	tr_code		,
	tax_no		,
	id_info		,
	name		,
	email		,
	productname	,
	amt1		,
	amt2		,
	amt3		,
	amt4		,
	type) VALUES (
	'{$ordercode}', 
	'{$tsdtime}', 
	'{$up_tr_code}', 
	'{$tax_cnum}', 
	'{$up_id_info}', 
	'{$up_name}', 
	'{$up_email}', 
	'{$up_productname}', 
	{$up_amt1}, 
	{$up_amt2}, 
	{$up_amt3}, 
	{$up_amt4}, 
	'N')";
	if(pmysql_query($sql,get_db_conn())) {
		$onload="<script>window.onload=function(){ alert('현금영수증 개별발급 요청이 완료되었습니다.\\n\\n현금영수증 발급/조회에서 최종적으로 발급하시면 국세청에 신고됩니다.'); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert('현금영수증 발급요청이 실패하였습니다.'); }</script>";
	}
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.up_name.value.length==0) {
		alert("주문자명을 입력하세요.");
		document.form1.up_name.focus();
		return;
	}
	if(document.form1.up_email.value.length==0) {
		alert("주문자 이메일을 입력하세요.");
		document.form1.up_email.focus();
		return;
	}
	if(!IsMailCheck(document.form1.up_email.value)) {
		alert("이메일 입력이 잘못되었습니다.");
		document.form1.up_email.focus();
		return;
	}
	if(document.form1.up_productname.value.length==0) {
		alert("주문 상품명을 입력하세요.");
		document.form1.up_productname.focus();
		return;
	}
	if(document.form1.up_amt.value.length==0) {
		alert("주문 상품가격을 입력하세요.");
		document.form1.up_amt.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_amt.value)) {
		alert("주문 상품가격은 숫자만 입력하세요.");
		document.form1.up_amt.focus();
		return;
	}
	if(document.form1.up_amt.value<1) {
		alert("주문 상품가격은 1원 이상 등록이 가능합니다.");
		document.form1.up_amt.focus();
		return;
	}

	if(document.form1.up_tr_code[0].checked) {
		if(document.form1.up_gbn[0].checked) {
			if(document.form1.up_resno1.value.length==0 || document.form1.up_resno2.value.length==0 || document.form1.up_resno1.value.length!=6 || document.form1.up_resno2.value.length!=7) {
				alert("주민번호를 정확히 입력하세요.");
				document.form1.up_resno1.focus();
				return;
			}
			if(!chkResNo(document.form1.up_resno1.value+"-"+document.form1.up_resno2.value)) {
				alert("주민번호 입력이 잘못되었습니다.");
				document.form1.up_resno1.focus();
				return;
			}
		} else {
			mobile1=document.form1.up_mobile1;
			mobile2=document.form1.up_mobile2;
			mobile3=document.form1.up_mobile3;
			if(mobile1.value.length==0 || mobile2.value.length==0 || mobile3.value.length==0) {
				alert("핸드폰번호를 정확히 입력하세요.");
				mobile1.focus();
				return;
			}
			if(!IsNumeric(mobile1.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile1.focus();
				return;
			}
			if(!IsNumeric(mobile2.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile2.focus();
				return;
			}
			if(!IsNumeric(mobile3.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile3.focus();
				return;
			}
			if(mobile1.value=="010" || mobile1.value=="011" || mobile1.value=="016" || mobile1.value=="017" || mobile1.value=="018" || mobile1.value=="019") {
				if(mobile2.value.length<3 && mobile3.value.length<4) {
					alert("핸드폰번호를 정확히 입력하세요.");
					mobile2.focus();
					return;
				}
			} else {
				alert("핸드폰번호를 정확히 입력하세요.");
				mobile1.focus();
				return;
			}
		}
	} else {
		//사업자번호 체크
		biz1=document.form1.up_comnum1.value;
		biz2=document.form1.up_comnum2.value;
		biz3=document.form1.up_comnum3.value;
		if(!chkBizNo(biz1+""+biz2+""+biz3)) {
			alert("사업자번호 입력이 잘못되었습니다.");
			document.form1.up_comnum1.focus();
			return;
		}
	}

	document.form1.mode.value="insert";
	document.form1.submit();
}

function ViewLayer(layer) {
	if(layer=="layer2") {
		document.all["layer1"].style.display="none";
		document.all["layer2"].style.display="";
		document.form1.up_gbn[2].checked=true;

		document.form1.up_comnum1.disabled=false;
		document.form1.up_comnum2.disabled=false;
		document.form1.up_comnum3.disabled=false;

		document.form1.up_resno1.disabled=true;
		document.form1.up_resno2.disabled=true;
		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;

		document.form1.up_resno1.value="";
		document.form1.up_resno2.value="";
		document.form1.up_mobile1.value="";
		document.form1.up_mobile2.value="";
		document.form1.up_mobile3.value="";

	} else {
		document.all["layer2"].style.display="none";
		document.all["layer1"].style.display="";
		document.form1.up_gbn[0].checked=true;

		document.form1.up_comnum1.disabled=true;
		document.form1.up_comnum2.disabled=true;
		document.form1.up_comnum3.disabled=true;

		document.form1.up_comnum1.value="";
		document.form1.up_comnum2.value="";
		document.form1.up_comnum3.value="";

		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;

		document.form1.up_resno1.disabled=false;
		document.form1.up_resno2.disabled=false;
	}
}
function change_gbn(gbn) {
	if(gbn==0) {
		document.form1.up_resno1.disabled=false;
		document.form1.up_resno2.disabled=false;
		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;
		document.form1.up_mobile1.value="";
		document.form1.up_mobile2.value="";
		document.form1.up_mobile3.value="";
	} else if(gbn==1) {
		document.form1.up_mobile1.disabled=false;
		document.form1.up_mobile2.disabled=false;
		document.form1.up_mobile3.disabled=false;
		document.form1.up_resno1.disabled=true;
		document.form1.up_resno2.disabled=true;
		document.form1.up_resno1.value="";
		document.form1.up_resno2.value="";
	}
}

</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 현금영수증 관리 &gt; <span>현금영수증 개별발급</span></p></div></div>

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
				<div class="title_depth3">현금영수증 개별발급</div>
				<!-- 소제목 -->
				<div class="title_depth3_sub"><span>현금영수증을 개별적으로 발급요청이 가능합니다.</span></div>
            </td>
        </tr>
        <tr>
        	<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub">현금영수증 개별발급</div>
			</td>
		</tr>
		<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type=hidden name=mode>
		<tr>
			<td>
			<div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<th><span>주문자명</span></th>
				<TD class="td_con1"><input type=text name=up_name size=30 maxlength=20 class="input"></TD>
			</TR>
			<TR>
				<th><span>이메일</span></th>
				<TD class="td_con1"><input type=text name=up_email size=30 maxlength=30 class="input"></TD>
			</TR>
			<TR>
				<th><span>상품명</span></th>
				<TD class="td_con1"><input type=text name=up_productname size=30 maxlength=30 class="input"></TD>
			</TR>
			<TR>
				<th><span>상품가격</span></th>
				<TD class="td_con1"><input type=text name=up_amt size=12 maxlength=12 style="text-align:right" onkeyup="strnumkeyup(this)" class="input">원 &nbsp;<span class="font_orange">* 총 상품가격(일반과세업자의 경우는 부가세 10%를 계산하여 신고됩니다.)</span></TD>
			</TR>
			<TR>
				<th rowspan=2><span>발급형태</span></th>
				<TD class="td_con1" style="border-bottom-width:1pt; border-bottom-color:rgb(237,237,237); border-bottom-style:solid;"><input type=radio id="idx_tr_code0" name=up_tr_code value="0" checked onclick="ViewLayer('layer1')"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tr_code0>개인</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tr_code1" name=up_tr_code value="1" onclick="ViewLayer('layer2')"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tr_code1>사업자</label></TD>
			</TR>
			<TR>
				<TD class="td_con1">
				<div class="table_none">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
					<DIV id=layer1 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: block; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
					<input type=radio id="idx_gbn0" name=up_gbn value="0" checked onclick="change_gbn(0)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gbn0>주민번호</label>&nbsp;&nbsp;
					<input type=text name=up_resno1 size=6 maxlength=6 onkeyup="strnumkeyup(this)" class="input"> - <input type=text name=up_resno2 size=7 maxlength=7 onkeyup="strnumkeyup(this)" class="input">
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_gbn1" name=up_gbn value="1" onclick="change_gbn(1)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gbn1>핸드폰</label>&nbsp;&nbsp;
					<input type=text name=up_mobile1 size=3 maxlength=3 disabled onkeyup="strnumkeyup(this)" class="input"> - <input type=text name=up_mobile2 size=4 maxlength=4 disabled onkeyup="strnumkeyup(this)" class="input"> - <input type=text name=up_mobile3 size=4 maxlength=4 disabled onkeyup="strnumkeyup(this)" class="input">
					</DIV>
					<div id=layer2 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: none; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
					<input type=radio id="idx_gbn2" name=up_gbn value="2" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gbn2>사업자번호</label>&nbsp;&nbsp;
					<input type=text name=up_comnum1 size=3 maxlength=3 disabled onkeyup="strnumkeyup(this)" class="input"> - <input type=text name=up_comnum2 size=2 maxlength=2 disabled onkeyup="strnumkeyup(this)" class="input"> - <input type=text name=up_comnum3 size=5 maxlength=5 disabled onkeyup="strnumkeyup(this)" class="input">
					</div>
					</td>
				</tr>
				</table>
				</div>
				</TD>
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
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>하단 표기 내용</span></dt>
						<dd>
							- 주문서를 통한 무통장 입금이 아닌 경우나 기타 오프라인 금액에 대해서도 현금영수증 발급이 가능합니다.<br>
							- 현금영수증 발급시 국세청에 통보되기 때문에 정확한 자료를 입력해야 합니다. <br />
							- 개별발급을 하면 발급요청으로 처리됨으로 발급 후 <a href="javascript:parent.topframe.GoMenu(5,'order_taxsavelist.php');">주문/매출 > 현금영수증 관리 > 현금영수증 발급/조회</a> 에서 실제 발급을 하셔야 합니다.
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
<?=$onload?>
<?php 
include("copyright.php");
