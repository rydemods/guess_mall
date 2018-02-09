<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$date_start=$_POST["date_start"];
$date_end=$_POST["date_end"];
$date_start=$date_start?$date_start:date("Y-m-d",$CurrentTime);
$date_end=$date_end?$date_end:date("Y-m-d",$CurrentTime);

$type=$_POST["type"];
$productcode=$_POST["productcode"];
$coupon_name=$_POST["coupon_name"];
$time=$_POST["time"];
$peorid=$_POST["peorid"];
$sale_type=$_POST["sale_type"];
$sale2=$_POST["sale2"];
$sale_money=$_POST["sale_money"];
$amount_floor=$_POST["amount_floor"];
$mini_price=$_POST["mini_price"];
$bank_only=$_POST["bank_only"];
$use_con_type1=$_POST["use_con_type1"];
$issue_type=$_POST["issue_type"];
$detail_auto=$_POST["detail_auto"];
$issue_tot_no=$_POST["issue_tot_no"];
$repeat_id=$_POST["repeat_id"];
$description=$_POST["description"];
$use_point=$_POST["use_point"];
$couponimg=$_FILES["couponimg"];

$imagepath=$Dir.DataDir."shopimages/etc/";
if ($type=="insert") {
	$coupon_code=substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
	if($couponimg['size'] < 153600) {
		if (ord($couponimg['name']) && file_exists($couponimg['tmp_name'])) {
			$ext = strtolower(pathinfo($couponimg['name'],PATHINFO_EXTENSION));
			if ($ext=="gif") {
				$imagename = "COUPON{$coupon_code}.gif";
				move_uploaded_file($couponimg['tmp_name'],$imagepath.$imagename);
				chmod($imagepath.$imagename,0666);
			} else {
				alert_go('쿠폰 이미지 파일은 GIF 파일만 등록 가능합니다.',-1);
			}
		}
	} else {
		alert_go("쿠폰 이미지 파일 용량이 초과되었습니다.\\n\\nGIF 파일 150KB 이하로 올려주시기 바랍니다.",-1);
	}

	if(ord($mini_price)==0) $mini_price=0; 
	if(ord($use_con_type1)==0 || $productcode=="ALL") $use_con_type1="N"; 
	if(ord($use_con_type2)==0 || $productcode=="ALL") $use_con_type2="Y"; 
	if(ord($repeat_id)==0) $repeat_id="N";
	if(ord($issue_tot_no)==0) $issue_tot_no=0; 
	if(ord($sale_money)==0) $sale_money=0; 
	if($sale_type=="+" && $sale2=="%") $realsale=1;
	else if($sale_type=="-" && $sale2=="%") $realsale=2;
	else if($sale_type=="+" && $sale2=="원") $realsale=3;
	else if($sale_type=="-" && $sale2=="원") $realsale=4;
	if ($time=="D") {
		$date_start = str_replace("-","",$date_start)."00";
		$date_end = str_replace("-","",$date_end)."23";
	} else {
		$date_start = "-".$peorid;
		$date_end = "";
	}

	$sql = "INSERT INTO tblcouponinfo(
	coupon_code	,
	coupon_name	,
	date_start	,
	date_end	,
	sale_type	,
	sale_money	,
	amount_floor	,
	mini_price	,
	bank_only	,
	productcode	,
	use_con_type1	,
	use_con_type2	,
	issue_type	,
	detail_auto	,
	issue_tot_no	,
	repeat_id	,
	description	,
	use_point	,
	member		,
	display		,
	date) VALUES (
	'{$coupon_code}', 
	'{$coupon_name}', 
	'{$date_start}', 
	'{$date_end}', 
	'{$realsale}', 
	'{$sale_money}', 
	'{$amount_floor}', 
	{$mini_price}, 
	'{$bank_only}', 
	'{$productcode}', 
	'{$use_con_type1}', 
	'{$use_con_type2}', 
	'{$issue_type}', 
	'{$detail_auto}', 
	{$issue_tot_no}, 
	'{$repeat_id}', 
	'{$description}', 
	'{$use_point}', 
	'".($issue_type!="N"?"ALL":"")."', 
	'".($issue_type!="N"?"Y":"N")."', 
	'".date("YmdHis")."')";
	pmysql_query($sql,get_db_conn());

	if($issue_type!="N") $url = "market_couponlist.php";
	else $url = "market_couponsupply.php";

	echo "<body onload=\"location.href='$url';\"></body>";
	exit;
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.coupon_name.value.length==0) {
		alert("쿠폰 이름을 입력하세요.");
		form.coupon_name.focus();
		return;
	}
	if(CheckLength(form.coupon_name)>100) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 50자 이내 혹은 영문/숫자/기호 100자 이내로 입력이 가능합니다.");
		form.coupon_name.focus();
		return;
	}
	content ="아래의 사항을 확인하시고, 등록하시면 됩니다.\n\n"
			 +"--------------------------------------------\n\n"
			 +"* 쿠폰 이름 : "+form.coupon_name.value+"\n\n";
	
	if (form.time[0].checked) {
		date = "<?=date("Y-m-d");?>";
		if (form.date_start.value<date || form.date_end.value<date || form.date_start.value>form.date_end.value) {
			alert("쿠폰 유효기간 설정이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			form.date_start.focus();
			return;
		}
		content+="* 쿠폰 유효기간 : "+form.date_start.value+" ~ "+form.date_end.value+" 까지\n\n";
	} else {
		if (form.peorid.value.length==0) {
			alert("쿠폰 사용기간을 입력하세요.");
			form.peorid.focus();
			return;
		} else if (!IsNumeric(document.form1.peorid.value)) {
			alert("쿠폰 사용기간은 숫자만 입력 가능합니다.");
			form.peorid.focus();
			return;
		}
		content+="* 쿠폰 사용기간 : "+form.peorid.value+"일 동안\n\n";
	}
	if (form.sale_money.value.length==0) {
		alert("쿠폰 할인 금액/할인률을 입력하세요.");
		form.sale_money.focus();
		return;
	} else if (!IsNumeric(form.sale_money.value)) {
		alert("쿠폰 할인 금액/할인률은 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
		form.sale_money.focus();
		return;
	}
	if(form.sale2.selectedIndex==1 && form.sale_money.value>=100){
		alert("쿠폰 할인률은 100보다 작아야 합니다.");
		form.sale_money.focus();
		return;
	}
	content+="* 쿠폰종류 : "+form.sale_type.options[form.sale_type.selectedIndex].text+"\n\n";
	content+="* 쿠폰 금액/할인률 : "+form.sale_money.value+form.sale2.options[form.sale2.selectedIndex].value+"\n\n";
	if(form.bank_only[0].checked) content+="* 쿠폰 사용가능 결제방법 : 제한 없음\n\n";
	else content+="* 쿠폰 사용가능 결제방법 : 현금 결제만 가능(실시간 계좌이체 포함)\n\n";

	if(form.productcode.value.length==18 && form.checksale[1].checked && form.use_con_type2.checked!=true) {
		alert("쿠폰이 한상품에 적용될경우 구매금액에 제한이 없습니다.");
		nomoney(1);
	}
	if(form.checksale[1].checked){
		if(form.mini_price.value.length==0){
			alert("쿠폰 결제 금액을 입력하세요.");
			document.form1.mini_price.focus();
			return;
		}else if(!IsNumeric(form.mini_price.value)){
			alert("쿠폰 결제 금액은 숫자만 입력 가능합니다.");
			form.mini_price.focus();
			return;
		}
		content+="* 쿠폰 결제 금액 : "+form.mini_price.value+"원 이상 구매시\n\n";
	} else {
		content+="* 쿠폰 결제 금액 : 제한없음\n\n";
	}
	content+="* 적용상품군 : "+form.productname.value+"\n\n";

	if(form.detail_auto[0].checked && form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출함\n\n";
	} else if(form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출안함\n\n";
	}

	if(form.description.value.length==0) {
		alert("쿠폰 설명을 입력하세요.");
		form.description.focus();
		return;
	}
	if(CheckLength(form.description)>100) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 50자 이내 혹은 영문/숫자/기호 100자 이내로 입력이 가능합니다.");
		form.description.focus();
		return;
	}
	if((form.issue_type[0].checked || form.issue_type[2].checked) && form.checknum[1].checked){
		alert("즉시 발급시,회원 가입시 쿠폰 발행의 경우 발행 쿠폰수에 제한이 없습니다.");
		nonum(1);
	}
	if(form.checknum[1].checked){
		if(form.issue_tot_no.value.length==0){
			alert("쿠폰 발행수를 입력하세요.");
			form.issue_tot_no.focus();
			return;
		}else if(!IsNumeric(form.issue_tot_no.value)){
			alert("쿠폰 발행수는 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
			form.issue_tot_no.focus();
			return;
		}else if(form.issue_tot_no.value<=0) {
			alert("쿠폰 발행 매수를 입력하세요.");
			form.issue_tot_no.focus();
			return;
		}
		content+="* 발행 쿠폰수 : "+form.issue_tot_no.value+"개\n\n";
	} else {
		content+="* 발행 쿠폰수 : 무제한\n\n";
	}

	if(form.issue_type[0].checked) tempmsg ="즉시 발급용 쿠폰";
	else if(form.issue_type[1].checked) tempmsg ="쿠폰 클릭시 발급";
	else if(form.issue_type[2].checked) tempmsg ="회원 가입시 발급";
	content+="* 발급조건 : "+tempmsg+"\n\n";
	content+="* 제한사항 : 등급할인혜택과 동시사용 "+form.use_point.options[form.use_point.selectedIndex].text+"\n\n";
	if(form.useimg[0].checked){
		form.couponimg.value="";
		content+="* 쿠폰이미지 : 기본이미지\n\n";
	} else if(form.useimg[1].checked && form.couponimg.value.length==0){
		alert("쿠폰 이미지를 등록하세요.");
		form.couponimg.focus();
		return;
	} else {
		content+="* 쿠폰이미지 : 선택 이미지 등록\n\n";
	}
	content+="--------------------------------------------";
	if(confirm(content)){
		form.type.value="insert";
		form.submit();
	}
}
function changerate(rate){  
	document.form1.rate.value=rate;
	if(rate=="%") {
		document.form1.amount_floor.disabled=false;
	} else {
		document.form1.amount_floor.disabled=true;
	}
}
function nomoney(temp){  
	if(temp==1){
		document.form1.mini_price.value="";
		document.form1.mini_price.disabled=true;
		document.form1.mini_price.style.background='#F0F0F0';
		document.form1.checksale[0].checked=true;
	} else {
		document.form1.mini_price.value="0";
		document.form1.mini_price.disabled=false;
		document.form1.mini_price.style.background='white';
		document.form1.checksale[1].checked=true;
	}
}
function nonum(temp){  
	if(temp==1){
		document.form1.issue_tot_no.value="";
		document.form1.issue_tot_no.disabled=true;
		document.form1.issue_tot_no.style.background='#F0F0F0';
		document.form1.checknum[0].checked=true;
	} else {
		document.form1.issue_tot_no.value="0";
		document.form1.issue_tot_no.disabled=false;
		document.form1.issue_tot_no.style.background='white';
		document.form1.checknum[1].checked=true;
	}
}
function ViewLayer(layer,display){
	if(document.all){
		document.all[layer].style.display=display;
	} else if(document.getElementById){
		document.getElementById(layer).style.display=display;
	} else if(document.layers){
		document.layers[layer].display=display;
	}
}
function ChoiceProduct(){
	window.open("about:blank","coupon_product","width=245,height=140,scrollbars=no");
	document.form2.submit();
}

</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt; <span class="2depth_select">새로운 쿠폰 생성하기</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=productcode value="ALL">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponnew_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">회원들에게 자유롭게 쿠폰발행 서비스를 진행할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponnew_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif"></TD>
					<TD width="100%" class="notice_blue">쿠폰 사용은 한 주문건에 대해서 한개의 쿠폰만 사용이 가능합니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=160></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 이름</TD>
					<TD class="td_con1"><INPUT maxLength=100 size=70 name=coupon_name class="input"><br><span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~</b></span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">유효기간</TD>
					<TD class="td_con1">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD ><INPUT type=radio value=D name=time>기간설정 : <INPUT onfocus=this.blur(); onclick=Calendar(this) size=10 name=date_start value="<?=$date_start?>" class="input_selected"> 부터 <INPUT  onfocus=this.blur(); onclick=Calendar(this) size=10 name=date_end value="<?=$date_end?>" class="input_selected"> 까지 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </TD>
					</TR>
					<TR>
						<TD ><INPUT type=radio CHECKED value=P name=time>발행 후 <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid class="input">일 동안 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰종류 선택</TD>
					<TD class="td_con1">
					<SELECT style="WIDTH: 100px" name=sale_type class="select">
					<OPTION value=- selected>할인 쿠폰</OPTION> 
					<OPTION value=+>적립 쿠폰</OPTION>
					</SELECT>
					<span class="font_orange"> * 할인쿠폰은 구매시 즉시 할인되며, 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액/할인율 선택</TD>
					<TD class="td_con1">
					<SELECT style="WIDTH: 100px" onchange=changerate(options.value); name=sale2 class="select">
					<OPTION value=원 selected>금액</OPTION> <OPTION value=%>할인(적립)율</OPTION>
					</SELECT>
					→ 
					<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money class="input"> <INPUT class="input_hide1" readOnly size=1 value=원 name=rate>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액절사</TD>
					<TD class="td_con1">
					<SELECT disabled name=amount_floor class="select">
<?php 
					$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
					$arcnt = count($arfloor);
					for($i=1;$i<$arcnt;$i++){
						echo "<option value=\"{$i}\"";
						if($amount_floor==$i) echo " selected";
						echo ">{$arfloor[$i]}</option>";
					}
?>
					</SELECT>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 결제금액 사용제한</TD>
					<TD class="td_con1">
					<INPUT onclick=nomoney(1) type=radio CHECKED name=checksale>제한 없음  &nbsp;
					<INPUT onclick=nomoney(0) type=radio name=checksale><INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=mini_price class="input_disabled">원 이상 주문시 가능
					<SCRIPT>nomoney(1);</SCRIPT>
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰사용가능 결제방법</TD>
					<TD class="td_con1">
					<INPUT type=radio CHECKED value=N name=bank_only>제한 없음  &nbsp;
					<INPUT type=radio value=Y name=bank_only><B>현금 결제</B>만 가능(실시간 계좌이체 포함)
					</TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponnew_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=160></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 적용 상품군 선택</TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100">현재 적용 상품군:  </td>
						<td width="100"><INPUT style="width:400px" onclick="alert('현재 적용 상품군의 변경은 [선택하기]버튼을 클릭하시면 됩니다.')" readOnly size="64" value=전체상품 name=productname class="input"></td>
						<td><a href="javascript:ChoiceProduct();"><img src="images/btn_select2.gif" width="76" height="28" border="0" hspace="2"></a></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<TR>
					<TD colspan="2">
					<div id=layer1 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<col width=160></col>
					<col width=></col>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 사용조건</TD>
						<TD class="td_con1">
						<INPUT type=checkbox CHECKED value=Y name=use_con_type1>다른 상품과 함께 구매시에도, 해당 쿠폰을 사용합니다.<BR>
						<INPUT type=checkbox value=N name=use_con_type2>선택된 카테고리(상품)을 제외하고 적용합니다.
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					</TABLE>
					</div>
					</TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 발급조건</TD>
					<TD class="td_con1">
					<INPUT onclick="ViewLayer('layer2','none')" type=radio CHECKED value=N name=issue_type>쿠폰 발급용 쿠폰&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* [생성된 쿠폰 즉시 발급] 에서 발급 가능합니다.</span><BR>
					<INPUT onclick="ViewLayer('layer2','block')" type=radio value=Y name=issue_type>쿠폰 클릭시 발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원이 쿠폰 클릭시 자동 발급됩니다. </span><BR>
					<INPUT onclick="ViewLayer('layer2','none')" type=radio value=M name=issue_type>회원 가입시 발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원가입하면 자동 발급됩니다.</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2">
					<div id=layer2 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<col width=160></col>
					<col width=></col>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 자동노출 여부</TD>
						<TD class="td_con1">
						상품 상세페이지 상세설명 상단에 쿠폰을 자동  
						<SELECT name=detail_auto class="select">
						<OPTION value=Y selected>노출함</OPTION> 
						<OPTION value=N>노출안함</OPTION>
						</SELECT>
						<IMG height=5 width=0><BR><span class="font_orange"> * 회원이 직접 쿠폰을 클릭함으로서 발급받을 수 있는 서비스입니다.</span>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">발행 쿠폰 수</TD>
						<TD class="td_con1">
						<INPUT onclick=nonum(1) type=radio CHECKED name=checknum>무제한 &nbsp;
						<INPUT onclick=nonum(0) type=radio name=checknum><INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=issue_tot_no class="input">매 한정
						<SCRIPT>nonum(1);</SCRIPT>
						</TD>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">동일인 재발급 가능여부</TD>
						<TD class="td_con1">
						<INPUT type=radio value=Y name=repeat_id checked>가능  &nbsp;
						<INPUT type=radio value=N name=repeat_id>불가능
						</TD>
						</div>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					</TABLE>
					</div>
					</TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 설명</TD>
					<TD class="td_con1"><INPUT maxLength=200 size=91 name=description style=width:99% class="input"> <span class="font_orange"> * 입력한 쿠폰설명은 쿠폰이미지 상단에 출력됩니다.</span></TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 적용 제한&nbsp;사항</TD>
					<TD class="td_con1">
					쿠폰과 등급회원 할인/적립 혜택 동시  
					<SELECT name=use_point class="select">
					<OPTION value=Y selected>적용함</OPTION> 
					<OPTION value=A>적용안함</OPTION>
					</SELECT>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 이미지 설정</TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><INPUT type=radio CHECKED name=useimg>기본 이미지 사용<br></td>
						</tr>
						<tr>
							<td><IMG src="images/sample/market_couponsampleimg.gif" width="352" height="122"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><IMG height=3 width=0><INPUT type=radio name=useimg>자유제작 이미지 등록<span class="font_orange">(*GIF 파일 150KB 이하로 올려주시고, 권장 사이즈는 350*150 입니다.)</span></td>
						</tr>
						<tr>
							<td><INPUT type=file size=65 name=couponimg class="input"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm(document.form1);"><img src="images/btn_cupon.gif" width="139" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>쿠폰 사용은 한번의 주문건에서만 사용할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>쿠폰사용 선택 : ① 할인쿠폰은 구매시 즉시 할인됩니다.</td>
					</tr>
					<tr>
						<td align="right" valign="top">&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;② 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>쿠폰상품 선택 :모든상품,일부카테고리,일부상품 으로 구분 됩니다.</td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>발생한 쿠폰은 로그인 후 마이페이지 정보에서 확인 할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

			<form name=form2 action="coupon_productchoice.php" method=post target=coupon_product>
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
<?=$onload?>
<?php include("copyright.php"); ?>
