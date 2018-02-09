<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$maxnum=10;
$maxlength=2000;
$maxj=5; //지역차등배송료
$maxi=5; //차등배송 갯수

$mode=$_POST["mode"];
if($mode=="update") {
	$up_deli_pricetype="Y";
	$up_deli_area=$_POST["up_deli_area"];
	$up_delivery=$_POST["up_delivery"];
	$up_deli_price=$_POST["up_deli_price".$up_delivery];
	$up_deli_mini=$_POST["up_deli_mini"];

	for($i=0; $i<$maxi; $i++) {
		if(strlen($_POST["up_deli_limitup"][$i])>0 || strlen($_POST["up_deli_limitdown"][$i])>0 || strlen($_POST["up_deli_limitfee"][$i])>0) {
			if($_POST["up_deli_limitup"][$i]>=0 && $_POST["up_deli_limitdown"][$i]>=0 && $_POST["up_deli_limitfee"][$i]>=0) {
				$up_deli_limit_imp[] = (int)$_POST["up_deli_limitup"][$i]."".$_POST["up_deli_limitdown"][$i]."".(int)$_POST["up_deli_limitfee"][$i];
			}
		}
	}
	$up_deli_limit = @implode("=", $up_deli_limit_imp);
	for($j=0; $j<$maxj; $j++) {
		if($_POST["up_gradedeliareanum"][$j]=="Y" && strlen($_POST["up_gradedeli_area"][$j])>0) {
			$up_deli_area_limit_imp[$j] = $_POST["up_gradedeli_area"][$j];
			for($i=0; $i<$maxi; $i++) {
				if(strlen($_POST["up_gradedeli_limitup"][$j][$i])>0 || strlen($_POST["up_gradedeli_limitdown"][$j][$i])>0 || strlen($_POST["up_gradedeli_limitfee"][$j][$i])>0) {
					if($_POST["up_gradedeli_limitup"][$j][$i]>=0 && $_POST["up_gradedeli_limitdown"][$j][$i]>=0 && $_POST["up_gradedeli_limitfee"][$j][$i]>=0) {
						$up_deli_area_limit_imp[$j].= "=".(int)$_POST["up_gradedeli_limitup"][$j][$i]."".$_POST["up_gradedeli_limitdown"][$j][$i]."".(int)$_POST["up_gradedeli_limitfee"][$j][$i];
					}
				}
			}
		}
	}
	$up_deli_area_limit = @implode(":", $up_deli_area_limit_imp);
	/*
	if($up_delivery=="F") $up_deli_price=0;
	else if($up_delivery=="Y") $up_deli_price=-9;
	else if($up_delivery=="N" || $up_delivery=="Q") $up_deli_pricetype="N";
	else if(strlen($up_deli_price) < 1) $up_deli_price = 0;
	*/
	#배송비 선정방법 변경 2016-02-16 유동혁
	$basefeetype_select = $_POST['basefeetype_select']; // 배송료 선택 0 - 무료 / 1 - 유료
	if( $basefeetype_select == '1' ){
		$deli_select = $_POST['deli_select']; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
		$basefee_select = $_POST['basefee_select']; // 배송료
		$minprice_select = $_POST['minprice_select']; // 배송료 지불 기준값 ( 미만 )
	} else {
		$deli_select = 0; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
		$basefee_select = 0; // 배송료
		$minprice_select = 0; // 배송료 지불 기준값 ( 미만 )
	}

	$up_deli_price     = $basefee_select; //배송료
	$up_deli_pricetype = $basefeetype_select; // 배송료 선택
	$up_deli_mini      = $minprice_select; // 배송료 지불 기준값
	$up_deli_select    = $deli_select; // 지불방법

	$sql = "UPDATE tblvenderinfo SET ";
	$sql.= "deli_price		= '".$up_deli_price."', ";
	$sql.= "deli_pricetype	= '".$up_deli_pricetype."', ";
	$sql.= "deli_mini		= '".$up_deli_mini."', ";
	$sql.= "deli_select		= '".$up_deli_select."', ";
	$sql.= "deli_area		= '".$up_deli_area."', ";
	$sql.= "deli_limit		= '".$up_deli_limit."', ";
	$sql.= "deli_area_limit	= '".$up_deli_area_limit."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";

	if(pmysql_query($sql,get_db_conn())) {
			
		$log_content = "## 입점업체 배송료 수정 ## - 배송료 선택 : ".$up_deli_pricetype." - 지불방법 : ".$up_deli_select." - 배송료 : ".$basefee_select." - 배송료 지불 기준값 : ".$up_deli_mini;
		$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content);
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}
/*
$deli_price = $_venderdata->deli_price;
$deli_pricetype = $_venderdata->deli_pricetype;
*/
$deli_basefee     = $_venderdata->deli_price; //배송료
$deli_basefeetype = $_venderdata->deli_pricetype;; //배송료 선택
$deli_miniprice   = $_venderdata->deli_mini; // 배송료 지불 기준값
$deli_select      = $_venderdata->deli_select; // 지불방법

$deli_limit = $_venderdata->deli_limit;
$deli_area_limit = $_venderdata->deli_area_limit;
if(strlen($deli_limit)>0) {
	if($deli_pricetype == "Y")
		$delivery="P";
	else
		$delivery="Q";
	$deli_limit_exp = explode("=",$deli_limit);
	$deli_limitup=array();
	$deli_limitdown=array();
	$deli_limitfee=array();
	for($i=0; $i<count($deli_limit_exp); $i++) {
		$deli_limit_exp2=explode("",$deli_limit_exp[$i]);
		$deli_limitup[] = $deli_limit_exp2[0];
		$deli_limitdown[] = $deli_limit_exp2[1];
		$deli_limitfee[] = $deli_limit_exp2[2];
	}
} else {
	if($deli_price==-9) $delivery="Y";
	else if($deli_price==0) $delivery="F";
	else {
		if($deli_pricetype == "Y")
			$delivery="M";
		else
			$delivery="N";
	}
}
if(strlen($deli_area_limit)>0) {
	$deli_area_limit_exp = explode(":",$deli_area_limit);

	unset($gradedeli_area[$i]);
	for($i=0; $i<count($deli_area_limit_exp); $i++) {
		$deli_area_limit_exp1=explode("=",$deli_area_limit_exp[$i]);
		$gradedeli_area[] = $deli_area_limit_exp1[0];

		unset($gradedeli_limitup[$i]);
		unset($gradedeli_limitdown[$i]);
		unset($gradedeli_limitfee[$i]);
		for($j=1; $j<count($deli_area_limit_exp1); $j++) {
			$deli_area_limit_exp2=explode("",$deli_area_limit_exp1[$j]);
			$gradedeli_limitup[$i][] = $deli_area_limit_exp2[0];
			$gradedeli_limitdown[$i][] = $deli_area_limit_exp2[1];
			$gradedeli_limitfee[$i][] = $deli_area_limit_exp2[2];
		}
	}
}
if($deli_price<0) $deli_price=0;
//$deli_mini = $_venderdata->deli_mini;
$deli_area = $_venderdata->deli_area;

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.11.1.min.js"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;

	//배송료 책정방법 변경 2016-02-16 유동혁
	if( $('input[name="basefeetype_select"]:checked').val() == '1' ){

		if( $('input[name="basefee_select"]').val().length == 0 ){
			alert("배송료를 입력하세요.");
			$('input[name="basefee_select"]').focus();
			return;
		} else if( isNaN( $('input[name="basefee_select"]').val() ) ){
			alert("배송료는 숫자만 입력 가능합니다.");
			$('input[name="basefee_select"]').focus();
			return;
		} else if( parseInt( $('input[name="basefee_select"]').val() ) <= 0 ){
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			$('input[name="basefee_select"]').focus();
			return;
		}

		if( isNaN( $('input[name="minprice_select"]').val() ) ){
			$('input[name="minprice_select"]').val( 0 );
		} else if( parseInt( $('input[name="minprice_select"]').val() ) < 0 ){
			$('input[name="minprice_select"]').val( 0 );
		}

	}
/*
	if(form.up_delivery[2].checked){
		if (form.up_deli_priceM.value.length==0) {
			alert("배송료를 입력하세요.");
			form.up_deli_priceM.focus();
			return;
		} else if (isNaN(form.up_deli_priceM.value)) {
			alert("배송료는 숫자만 입력 가능합니다.");
			form.up_deli_priceM.focus();
			return;
		} else if(form.up_deli_priceM.value<=0) {
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			form.up_deli_priceM.focus();
			return;
		}
	}
	if(form.up_delivery[3].checked){
		if (form.up_deli_priceN.value.length==0) {
			alert("배송료를 입력하세요.");
			form.up_deli_priceN.focus();
			return;
		} else if (isNaN(form.up_deli_priceN.value)) {
			alert("배송료는 숫자만 입력 가능합니다.");
			form.up_deli_priceN.focus();
			return;
		} else if(form.up_deli_priceN.value<=0) {
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			form.up_deli_priceN.focus();
			return;
		}
	}

	if(form.up_delivery[4].checked || form.up_delivery[5].checked){
		for(var i=0; i<<?=$maxi?>; i++) {
			if(document.getElementById("deli_limitup"+i).value.length>0 && (isNaN(document.getElementById("deli_limitup"+i).value) || document.getElementById("deli_limitup"+i).value<0)) {
				alert('차등구매금액은 0 이상의 숫자만 입력 가능합니다.');
				document.getElementById("deli_limitup"+i).focus();
				return;
			} else if(document.getElementById("deli_limitdown"+i).value.length>0 && (isNaN(document.getElementById("deli_limitdown"+i).value) || document.getElementById("deli_limitdown"+i).value<0)) {
				alert('차등구매금액은 0 이상의 숫자만 입력 가능합니다.');
				document.getElementById("deli_limitdown"+i).focus();
				return;
			} else if(document.getElementById("deli_limitfee"+i).value.length>0 && (isNaN(document.getElementById("deli_limitfee"+i).value) || document.getElementById("deli_limitfee"+i).value<0)) {
				alert('배송료는금액은 0 이상의 숫자만 입력 가능합니다.');
				document.getElementById("deli_limitfee"+i).focus();
				return;
			}
		}
	}
*/
	var k=1;
	for(i=0;i<<?=$maxj?>;i++){
		if(document.getElementById("idx_gradedeliareanum"+i).checked) {
			if(document.getElementById("idx_gradedeli_area"+i).value.length==0) {
				alert(k+"번째 특수지역명을 입력해 주세요.");
				document.getElementById("idx_gradedeli_area"+i).focus();
				return;
			}
			for(var j=0; j<<?=$maxi?>; j++) {
				if(document.getElementById("gradedeli"+i+"_limitup"+j).value.length>0 && (isNaN(document.getElementById("gradedeli"+i+"_limitup"+j).value) || document.getElementById("gradedeli"+i+"_limitup"+j).value<0)) {
					alert(k+"번째 차등구매금액은 0 이상의 숫자만 입력 가능합니다.");
					document.getElementById("gradedeli"+i+"_limitup"+j).focus();
					return;
				} else if(document.getElementById("gradedeli"+i+"_limitdown"+j).value.length>0 && (isNaN(document.getElementById("gradedeli"+i+"_limitdown"+j).value) || document.getElementById("gradedeli"+i+"_limitdown"+j).value<0)) {
					alert(k+"번째 차등구매금액은 0 이상의 숫자만 입력 가능합니다.");
					document.getElementById("gradedeli"+i+"_limitdown"+j).focus();
					return;
				} else if(document.getElementById("deli"+i+"_limitfee"+j).value.length>0 && (isNaN(document.getElementById("deli"+i+"_limitfee"+j).value) || document.getElementById("deli"+i+"_limitfee"+j).value<0)) {
					alert(k+"번째 배송료금액은 0 이상의 숫자만 입력 가능합니다.");
					document.getElementById("deli"+i+"_limitfee"+j).focus();
					return;
				}
			}
		}
		k++;
	}
	form.up_deli_area.value="";
	for(i=0;i<<?=$maxnum?>;i++){
		if((form.up_deliarea[i].value.length==0 && form.up_deliareaprice[i].value.length>0) 
		|| (form.up_deliarea[i].value.length>0 && form.up_deliareaprice[i].value.length==0)) {
			alert("특수 지역명과 추가배송료를 둘다 입력하셔야 합니다");
			form.up_deliarea[i].focus();
			return;
		}
		if (isNaN(form.up_deliareaprice[i].value)) {
			alert("추가배송료는 숫자만 입력 가능합니다.");
			form.up_deliareaprice[i].focus();
			return;
		}
		if (form.up_deliareaprice[i].value.length>0 && Math.abs(form.up_deliareaprice[i].value)==0) {
			alert("추가배송료는 0원 이상 입력하셔야 합니다.");
			form.up_deliareaprice[i].focus();
			return;
		}
		if(form.up_deliarea[i].value.length>0 && form.up_deliareaprice[i].value.length>0){
			form.up_deli_area.value+=form.up_deliarea[i].value+"|"+form.up_deliareaprice[i].value+"|";
		}
	}
	messlength = CheckLength(form.up_deli_area);
	if(messlength > <?=$maxlength?>){
		alert('총 입력가능한 길이가 한글 <?=($maxlength/2)?>자까지입니다. 다시한번 확인하시기 바랍니다.');
		form.up_deliarea[0].focus();
		return;
	}
	if(confirm("변경하신 내용을 저장하시겠습니까?")) {
		form.mode.value="update";
		form.target="processFrame";
		form.submit();
	}
}

function SetDeliChange(changetype,chnagevalue) {
	if((changetype=="A" || changetype=="B") && chnagevalue.length>0) {
		if(changetype=="A") {
			SetDeliChangeA(chnagevalue);
			SetDeliChangeB('');
		} else {
			SetDeliChangeB(chnagevalue);
			SetDeliChangeA('');
		}
	} else {
		SetDeliChangeA('');
		SetDeliChangeB('');
	}
}
function SetDeliChangeA(chnagevalue) {
	if(chnagevalue=="M" || chnagevalue=="N") {
		if(chnagevalue=="M") {
			document.form1.up_deli_priceM.disabled=false;
			document.form1.up_deli_priceM.style.background='#FFFFFF';
			document.form1.up_deli_priceN.disabled=true;
			document.form1.up_deli_priceN.style.background='#C0C0C0';
		} else {	
			document.form1.up_deli_priceM.disabled=true;
			document.form1.up_deli_priceM.style.background='#C0C0C0';
			document.form1.up_deli_priceN.disabled=false;
			document.form1.up_deli_priceN.style.background='#FFFFFF';
		}
		document.form1.up_deli_mini.disabled=false;
		document.form1.up_deli_mini.style.background='#FFFFFF';
	} else {
		document.form1.up_deli_priceM.disabled=true;
		document.form1.up_deli_priceM.style.background='#C0C0C0';
		document.form1.up_deli_priceN.disabled=true;
		document.form1.up_deli_priceN.style.background='#C0C0C0';
		document.form1.up_deli_mini.disabled=true;
		document.form1.up_deli_mini.style.background='#C0C0C0';
	}
}

function SetDeliChangeB(chnagevalue) {
	if(chnagevalue=="P" || chnagevalue=="Q") {
		for(var i=0; i<<?=$maxi?>; i++) {
			document.getElementById("deli_limitup"+i).disabled=false;
			document.getElementById("deli_limitup"+i).style.background='#FFFFFF';
			document.getElementById("deli_limitdown"+i).disabled=false;
			document.getElementById("deli_limitdown"+i).style.background='#FFFFFF';
			document.getElementById("deli_limitfee"+i).disabled=false;
			document.getElementById("deli_limitfee"+i).style.background='#FFFFFF';
		}
	} else {
		for(var i=0; i<<?=$maxi?>; i++) {
			document.getElementById("deli_limitup"+i).disabled=true;
			document.getElementById("deli_limitup"+i).style.background='#C0C0C0';
			document.getElementById("deli_limitdown"+i).disabled=true;
			document.getElementById("deli_limitdown"+i).style.background='#C0C0C0';
			document.getElementById("deli_limitfee"+i).disabled=true;
			document.getElementById("deli_limitfee"+i).style.background='#C0C0C0';
		}
	}
}

function SetValueCopy(insertValue,insertObject) {
	if(document.getElementById(insertObject)) {
		document.getElementById(insertObject).value=insertValue;
	}
}

function setGradeDeliUse(checkValue,textValue) {
	if(document.getElementById("idx_gradedeliarea"+textValue)) {
		if(checkValue) { 
			document.getElementById("idx_gradedeliarea"+textValue).style.display="";
			document.getElementById("idx_gradedeli_area"+textValue).disabled=false;
			document.getElementById("idx_gradedeli_area"+textValue).style.background='#FFFFFF';
		} else {
			document.getElementById("idx_gradedeliarea"+textValue).style.display="none";
			document.getElementById("idx_gradedeli_area"+textValue).disabled=true;
			document.getElementById("idx_gradedeli_area"+textValue).style.background='#EFEFEF';
		}
	}
}
</script>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>배송관련 기능설정</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>배송관련 기능설정</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 공급업체별 배송료 조건을 설정하실 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> Vender 관리자가 등록한 상품배송비는 본사 사이트에 입력된 배송비 설정값에 적용되지 않습니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=10></td></tr>
			<tr>
				<td style="padding:15">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=mode>
				<input type=hidden name=up_deli_area>

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 배송료 설정</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>
				
				<table border=0 cellpadding=7 cellspacing=1 bgcolor=#dddddd width=100%>
				<col width=120></col>
				<col width=></col>
				<!-- 배송료 책정방법 변경 -->
				<TR>
					<TD bgcolor='#f0f0f0' style="padding-left:10"><b>배송료 선택</b></TD>
					<TD bgcolor='#ffffff' style="padding-left:10" >
						<input type='radio' name='basefeetype_select' id='basefeetype_0' value='0' <? if( $deli_basefeetype == '0' || is_null($deli_basefeetype) ) { echo 'checked'; } ?> >
						<label for='basefeetype_0'>배송료 <font color='#0000FF'><b>무료</b></font></label>
						<input type='radio' name='basefeetype_select' id='basefeetype_1' value='1' <? if( $deli_basefeetype == '1' ) { echo 'checked'; } ?> >
						<label for='basefeetype_1' >배송료 <font color='#FF0000'><b>유료</b></font></label>
					</TD>
				</TR>
				<TR>
					<TD bgcolor='#f0f0f0' style="padding-left:10"><b>지불방법</b></TD>
					<TD bgcolor='#ffffff' style="padding-left:10" >
						<input type='radio' name='deli_select' id='deli_0' value='0' <? if( $deli_select == '0' || is_null($deli_select) ) { echo 'checked'; } ?> >
						<label for='deli_0' >배송료 <font color='#CC3D3D'><b>선불</b></font></label>
						<input type='radio' name='deli_select' id='deli_1' value='1' <? if( $deli_select == '1' ) { echo 'checked'; } ?> >
						<label for='deli_1' >배송료 <font color='#47C83E'><b>착불</b></font></label>
						<input type='radio' name='deli_select' id='deli_2' value='2' <? if( $deli_select == '2' ) { echo 'checked'; } ?> >
						<label for='deli_2' >배송료 <font color='#4374D9'><b>구매자( 선불/착불 ) 선택</b></font></label>
					</TD>
				</TR>
				<TR>
					<TD bgcolor='#f0f0f0' style="padding-left:10"><b>배송료</b></TD>
					<TD bgcolor='#ffffff' style="padding-left:10" >
						배송료 <input type='text' name='basefee_select' value='<?=$deli_basefee?>' style='text-align: right;'> 원
						<div style='margin-top : 3px; padding : 5px 5px 0px 0px;' >
						<TABLE cellSpacing='0' cellPadding='0' width="100%" border='0' >
							<tr>
								<td align="center" style="border : 3px #57B54A solid; padding : 5px; ">
									구매금액 <input type='text' name='minprice_select' size='10' maxlength='10' value="<?=$deli_miniprice?>" 
										class="input" style="text-align:right;">
									원 미만일 경우 배송비가 청구됩니다.<br>
									<span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">
										* 구매금액 0 원 입력시 모든 금액에 배송비가 부과됩니다.
									</span>
								</td>
							</tr>
						</table>
						</div>
					</TD>
				</TR>
				<script>
					//배송료 변환 script
					$(document).ready( function(){
						var basefeetype = $('input[name="basefeetype_select"]:checked');
						if( basefeetype.val() == '0'){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});

					$(document).on( 'click', 'input[name="basefeetype_select"]', function( event ){
						if( $(this).val() == '0' ){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});
				</script>
				<!-- //배송료 책정방법 변경 -->

				<!-- <TR>
					<TD bgcolor=#f0f0f0 style="padding-left:10"><b>무료 배송료</b></TD>
					<TD bgcolor=#ffffff style="padding-left:10">
				
					<input type=radio id="idx_delivery0" name=up_delivery value="F" <?=($delivery=="F"?"checked":"")?> onClick="SetDeliChange('','');"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery0>배송료 <font color='#0000FF'><b>무료</b></font></label><br>
					<input type=radio id="idx_delivery1" name=up_delivery value="Y" <?=($delivery=="Y"?"checked":"")?> onClick="SetDeliChange('','');"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery1>배송료 <font color='#38A422'><b>착불</b></font></label>&nbsp;<span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">* 착불의 경우는 장바구니에 배송료가 소비자 부담이라는 문구가 출력됩니다.</span>
					</td>
				</TR>
				<tr>
					<TD bgcolor=#f0f0f0 style="padding-left:10"><b>단일 유료 배송료</b></TD>
					<TD bgcolor=#ffffff style="padding-left:10">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><input type=radio id="idx_delivery2" name=up_delivery value="M" <?=($delivery=="M"?"checked":"")?> onClick="SetDeliChange('A','M')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery2>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액도 <font color='#0000FF'><b>포함</b></font>)</label>: <input type=text name=up_deli_priceM size=10 maxlength=6 value="<?=($delivery == "M"?$deli_price:"")?>" style="text-align:right;" disabled style="background-Color:#C0C0C0;">원<br>
					
						<input type=radio id="idx_delivery3" name=up_delivery value="N" <?=($delivery=="N"?"checked":"")?> onClick="SetDeliChange('A','N')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery3>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액은 <font color='#FF0000'><b>제외</b></font>)</label>: <input type=text name=up_deli_priceN size=10 maxlength=6 value="<?=($delivery == "N"?$deli_price:"")?>" style="text-align:right;" disabled style="background-Color:#C0C0C0;">원</td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td style="padding-left:20px;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td align="center" style="border:3px #57B54A solid;padding:5px;">구매금액 <input type=text name=up_deli_mini size=10 maxlength=10 value="<?=$deli_mini?>" style="text-align:right;">원 미만일 경우 배송비가 청구됩니다.<br><span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">* 구매금액 0 원 입력시 모든 금액에 배송비가 부과됩니다.</span></TD>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr> -->

<!-- 차등 배송료 감춤 -->
				<tr style='display:none;'>
					<TD bgcolor=#f0f0f0 style="padding-left:10"><b>차등 유료 배송료</b></TD>
					<TD bgcolor=#ffffff style="padding-left:10">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><input type=radio id="idx_delivery4" name=up_delivery value="P" <?=($delivery=="P"?"checked":"")?> onClick="SetDeliChange('B','P')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery4>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액도 <font color='#0000FF'><b>포함</b></font>)</label><br>
					
						<input type=radio id="idx_delivery5" name=up_delivery value="Q" <?=($delivery=="Q"?"checked":"")?> onClick="SetDeliChange('B','Q')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery5>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액은 <font color='#FF0000'><b>제외</b></font>)</label></td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td style="padding-left:20px;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td style="border:3px #FF7100 solid;" bgcolor="#FFF7F0">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width=""></col>
							<col width="120"></col>
							<tr>
								<td style="border-right:2px #FF7100 solid;" height="7"><img width="0" height="0"></td>
								<td></td>
							</tr>
							<tr align="center" height="30">
								<td style="border-right:2px #FF7100 solid;"><b>차등<img width="10" height="0">구매금액<img width="10" height="0">설정</b></td>
								<td><b>배<img width="15" height="0">송<img width="15" height="0">료</b></td>
							</tr>
							<tr>
								<td style="border-right:2px #FF7100 solid;" height="3"><img width="0" height="0"></td>
								<td></td>
							</tr>
							<tr>
								<td style="padding-left:5px;padding-right:5px;border-right:2px #FF7100 solid;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
								<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
							</tr>
							<tr>
								<td style="border-right:2px #FF7100 solid;" height="5"><img width="0" height="0"></td>
								<td></td>
							</tr>
							<?
							$j=1;
							for($i=0; $i<$maxi; $i++) { 
							?>
							<tr align="center">
								<td style="padding:5px;padding-bottom:0px;padding-left:0px;border-right:2px #FF7100 solid;"><b><?=str_pad($j, 2, "0", STR_PAD_LEFT);?>. </b><input type=text name=up_deli_limitup[] value="<?=($i==0?$i:$deli_limitup[$i])?>" <?=($i==0?" readonly":"")?> size=14 maxlength=10 style="text-align:right;" id="deli_limitup<?=$i?>"><b>원 이상&nbsp;&nbsp;∼&nbsp;&nbsp;</b><input type=text name=up_deli_limitdown[] value="<?=$deli_limitdown[$i]?>" size=14 maxlength=10 id="deli_limitdown<?=$i?>" <?=($j==$maxi?"":" onKeyDown=\"SetValueCopy(this.value,'deli_limitup".$j."');\" onKeyUp=\"SetValueCopy(this.value,'deli_limitup".$j."');\"")?> style="text-align:right;"><b>원 미만</b></td>
								<td align="center" style="padding:5px;padding-bottom:0px;"><input type=text name=up_deli_limitfee[] value="<?=$deli_limitfee[$i]?>" size=12 maxlength=6 style="text-align:right;" id="deli_limitfee<?=$i?>"><b>원</b></td>
							</tr>
							<? 
								$j++;
							} 
							?>
							
							<tr>
								<td style="border-right:2px #FF7100 solid;" height="7"><img width="0" height="0"></td>
								<td></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td style="padding:2px;"><span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">* 미입력시 기본값 : 원 이상 항목은 "0", 원 미만 항목은 무한, 배송료 항목은 "0" 입니다.<br>
							* 입력시에는 0 이상의 숫자만 입력해 주세요.<br>* 사용하지 않는 라인은 빈란으로 처리해 주세요.<br>* 차등 배송료 범위에 속하지 않는 구매금액은 배송료 무료가 됩니다.<br>* 차등 배송료 범위가 겹치는 경우 우선순위는 01 ~ 05 입니다.
							</span></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
<!--// 차등 배송료 감춤 -->
<?
/*
			if($delivery=="M" || $delivery=="N") { 
				echo "<script>SetDeliChange('A','".$delivery."');</script>";
			} else if($delivery=="P" || $delivery=="Q") { 
				echo "<script>SetDeliChange('B','".$delivery."');</script>";
			} else { 
				echo "<script>SetDeliChange('','');</script>";
			}
*/
?>

				</table>
				<!-- <table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td style="padding:5,0,0,5;font-size:8pt;color:#2A97A7;line-height:11pt">
					- 무료, 단일 항목 중 하나만 선택 가능합니다.
					<br>
					- 기본배송료 무료 선택시 추가로 설정할 항목은 없습니다.
					<br>
					- 단일 유로 배송료 선택시 추가로 필요한 항목을 확인하신 후 입력해 주세요.
					<br>
					- 상품 등록/수정 페이지에서도 개별적으로 배송료를 설정할 수 있습니다.
					<br>
					- 상품 등록/수정 페이지에서 개별배송비를 사용할 경우 해당 상품은 <b>추가로 개별배송비가 청구</b> 됩니다.
					</td>
				</tr>
				</table> -->
<!-- 지역별 배송료 사용안함 2015 11 25 유동혁 -->
<div style='display:none;' >
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 지역별 배송료 조건</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 bgcolor=#dddddd width=100%>
				<tr>
					<td bgcolor=#ffffff>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width="50"></col>
						<col width="50"></col>
						<col width=""></col>
						<TR height="36" align="center">
							<TD bgcolor=#f0f0f0><b>사용</b></TD>
							<TD bgcolor=#f0f0f0 style="border-left:#DDDDDD 1px solid;border-right:#DDDDDD 1px solid;"><b>번호</b></TD>
							<TD bgcolor=#f0f0f0><b>지역명 (도서,산간 등), 차등 배송료</b></TD>
						</TR>
						<TR>
							<TD colspan="3" bgcolor="#DDDDDD" height="1"></TD>
						</TR>
<?
		for($k=0;$k<$maxj;$k++){
?>
						<TR bgColor="#FFFFFF" align="center">
							<TD valign="top" style="padding-top:4px;"><input type=checkbox name="up_gradedeliareanum[]" value="Y" id="idx_gradedeliareanum<?=$k?>" onclick="setGradeDeliUse(this.checked,'<?=$k?>');" <?=(strlen($gradedeli_area[$k])>0?"checked":"")?>></td>
							<TD valign="top" style="border-left:#DDDDDD 1px solid;border-right:#DDDDDD 1px solid;padding-top:4px;"><b><?=($k+1)?></b></td>
							<TD>
							<TABLE cellSpacing=0 cellPadding=0 width="96%" border=0>
							<tr align="center">
								<td height="25">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width="60"></col>
								<col width=""></col>
								<tr>
									<td><b>지역명 : </b></td>
									<td><input type=text name="up_gradedeli_area[]" value="<?=$gradedeli_area[$k]?>" id="idx_gradedeli_area<?=$k?>" size="78" style="width:100%; <?=(strlen($gradedeli_area[$k])>0?"\"background:#FFFFFF;\"":"background:#EFEFEF;\"")?>" class="input"></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr id="idx_gradedeliarea<?=$k?>" <?=(strlen($gradedeli_area[$k])>0?"style=\"display:;\"":"style=\"display:none;\"")?>>
								<td>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
									<td style="border:3px #FF7100 solid;" bgcolor="#FFF7F0">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<col width=""></col>
									<col width="120"></col>
									<tr>
										<td style="border-right:2px #FF7100 solid;" height="7"><img width="0" height="0"></td>
										<td></td>
									</tr>
									<tr align="center" height="30">
										<td style="border-right:2px #FF7100 solid;"><b>차등<img width="10" height="0">구매금액<img width="10" height="0">설정</b></td>
										<td><b>배<img width="15" height="0">송<img width="15" height="0">료</b></td>
									</tr>
									<tr>
										<td style="border-right:2px #FF7100 solid;" height="3"><img width="0" height="0"></td>
										<td></td>
									</tr>
									<tr>
										<td style="padding-left:5px;padding-right:5px;border-right:2px #FF7100 solid;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
										<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
									</tr>
									<tr>
										<td style="border-right:2px #FF7100 solid;" height="5"><img width="0" height="0"></td>
										<td></td>
									</tr>
									<?
									$j=1;
									for($i=0; $i<$maxi; $i++) { 
									?>
									<tr align="center">
										<td style="padding:5px;padding-bottom:0px;padding-left:0px;border-right:2px #FF7100 solid;"><b><?=str_pad($j, 2, "0", STR_PAD_LEFT);?>. </b><input type=text name=up_gradedeli_limitup[<?=$k?>][] value="<?=($i==0?$i:$gradedeli_limitup[$k][$i])?>" <?=($i==0?" readonly":"")?> size=18 maxlength=10 class="input" style="text-align:right;" id="gradedeli<?=$k?>_limitup<?=$i?>"><b>원 이상&nbsp;&nbsp;∼&nbsp;&nbsp;</b><input type=text name=up_gradedeli_limitdown[<?=$k?>][] value="<?=$gradedeli_limitdown[$k][$i]?>" size=18 maxlength=10 class="input" id="gradedeli<?=$k?>_limitdown<?=$i?>" <?=($j==$maxi?"":" onKeyDown=\"SetValueCopy(this.value,'gradedeli".$k."_limitup".$j."');\" onKeyUp=\"SetValueCopy(this.value,'gradedeli".$k."_limitup".$j."');\"")?> style="text-align:right;"><b>원 미만</b></td>
										<td align="center" style="padding:5px;padding-bottom:0px;"><input type=text name=up_gradedeli_limitfee[<?=$k?>][] value="<?=$gradedeli_limitfee[$k][$i]?>" size=12 maxlength=6 class="input" style="text-align:right;" id="deli<?=$k?>_limitfee<?=$i?>"><b>원</b></td>
									</tr>
									<? 
										$j++;
									} 
									?>
									
									<tr>
										<td style="border-right:2px #FF7100 solid;" height="7"><img width="0" height="0"></td>
										<td></td>
									</tr>
									</table>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td height="2"></td>
							</tr>
							</table>
							</td>
						</tr>
						<TR>
							<TD colspan="3" bgcolor="#DDDDDD" height="1"></TD>
						</TR>
<?
		}
?>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 지역별 배송료 조건</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=4 cellspacing=1 bgcolor=#dddddd width=100%>
				<col width=50></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td bgcolor="#F0F0F0" align=center nowrap>번호</td>
					<td bgcolor="#F0F0F0" align=center>지역명 (도서,산간 등)</td>
					<td bgcolor="#F0F0F0" align=center nowrap>추가배송료 (+,-)</td>
				</tr>
<?
				$array_deli=explode("|",$deli_area);
				for($i=0;$i<$maxnum;$i++){
?>                 
				<tr bgcolor=#FFFFFF>
					<td align=center><?=($i+1)?></td>
					<td align=center><input type=text name=up_deliarea size=78 style="width:95%" value="<?=$array_deli[$i*2]?>"></td>
					<td align=center><input type=text name=up_deliareaprice size=15 maxlength=10 value="<?=$array_deli[$i*2+1]?>"></td>
				</tr>
<?
				}
?>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td style="padding:5,0,0,5;font-size:8pt;color:#2A97A7;line-height:11pt">
					- 원하시는 특수지역을 콤마(",")로 구분하여 입력하세요. (예 : 제주,거제시)
					<br>
					- 등록된 특수지역에서 주문을 할 경우 기본 배송료 외에 추가 배송료가 부가됩니다.
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<!-- <tr>
					<td align=center>
					<A HREF="javascript:CheckForm()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr> -->

				</form>

				</table>
</div>
<!-- 지역별 배송료 사용안함 2015 11 25 유동혁 -->
				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>

			<tr>
				<td align=center>
				<A HREF="javascript:CheckForm()"><img src="images/btn_save01.gif" border=0></A>
				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->


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
