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

$imagepath=$Dir.DataDir."shopimages/etc/";
$filename="aboutdeliinfo.gif";
$filename_m="aboutdeliinfo_m.gif";

$maxnum=10;
$maxlength=2000;
$maxj=5; //지역차등배송료
$maxi=5; //차등배송 갯수

$sql = "SELECT order_msg ";
$sql.= "FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
if ($data=pmysql_fetch_object($result)) {
	$order_msg = $data->order_msg;
}
pmysql_free_result($result);

$type=$_POST["type"];
$up_deli_area=$_POST["up_deli_area"];
$up_deli_type=$_POST["up_deli_type"];
$up_deli_setperiod=$_POST["up_deli_setperiod"];
$up_delivery=$_POST["up_delivery"];
//$up_deli_basefee=$_POST["up_deli_basefee".$up_delivery];
//$up_deli_miniprice=(int)$_POST["up_deli_miniprice"];
$up_wantdate=$_POST["up_wantdate"];
$up_day1=$_POST["up_day1"];
$up_time1=$_POST["up_time1"];
$up_time2=$_POST["up_time2"];
$up_bankname=$_POST["up_bankname"];

$deliview=$_POST["deliview"];
IF($deliview=="N"){
	$deliinfook="N";
}else{
	$deliinfook="Y";
}

for($i=0; $i<$maxi; $i++) {
	if(ord($_POST["up_deli_limitup"][$i]) || ord($_POST["up_deli_limitdown"][$i]) || ord($_POST["up_deli_limitfee"][$i])) {
		if($_POST["up_deli_limitup"][$i]>=0 && $_POST["up_deli_limitdown"][$i]>=0 && $_POST["up_deli_limitfee"][$i]>=0) {
			$up_deli_limit_imp[] = (int)$_POST["up_deli_limitup"][$i]."{$_POST["up_deli_limitdown"][$i]}".(int)$_POST["up_deli_limitfee"][$i];
		}
	}
}
$up_deli_limit = @implode("=", $up_deli_limit_imp);
for($j=0; $j<$maxj; $j++) {
	if($_POST["up_gradedeliareanum"][$j]=="Y" && ord($_POST["up_gradedeli_area"][$j])) {
		$up_deli_area_limit_imp[$j] = $_POST["up_gradedeli_area"][$j];
		for($i=0; $i<$maxi; $i++) {
			if(ord($_POST["up_gradedeli_limitup"][$j][$i]) || ord($_POST["up_gradedeli_limitdown"][$j][$i]) || ord($_POST["up_gradedeli_limitfee"][$j][$i])) {
				if($_POST["up_gradedeli_limitup"][$j][$i]>=0 && $_POST["up_gradedeli_limitdown"][$j][$i]>=0 && $_POST["up_gradedeli_limitfee"][$j][$i]>=0) {
					$up_deli_area_limit_imp[$j].= "=".(int)$_POST["up_gradedeli_limitup"][$j][$i]."{$_POST["up_gradedeli_limitdown"][$j][$i]}".(int)$_POST["up_gradedeli_limitfee"][$j][$i];
				}
			}
		}
	}
}
$up_deli_area_limit = @implode(":", $up_deli_area_limit_imp);
//exdebug($_POST);
//exit;

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

$up_deli_basefee     = $basefee_select; //배송료
$up_deli_basefeetype = $basefeetype_select; // 배송료 선택
$up_deli_miniprice   = $minprice_select; // 배송료 지불 기준값
$up_deli_select = $deli_select; // 지불방법

if ($type=="up") {
	/*
	$up_deli_basefeetype="Y";
	if($up_delivery=="F") $up_deli_basefee=0;
	else if($up_delivery=="Y") $up_deli_basefee=-9;
	else if($up_delivery=="N" || $up_delivery=="Q") $up_deli_basefeetype="N";
	else if(strlen($up_deli_basefee) < 1) $up_deli_basefee = 0;
	*/
	$tmp_order_msg = $order_msg;
	$tmp_order_msg2 = explode("=",$tmp_order_msg);
	$message = addslashes($tmp_order_msg2[0]);

	if($up_wantdate=="Y") $message.="=".$up_day1.$up_time1.$up_time2;
	else if($up_wantdate=="A") $message.="=".$up_day1;
	else $message.="=";

	$message.="=".$up_bankname;    
	//exdebug($up_deli_basefee);
	//if(!$up_deli_basefee) $up_deli_basefee = 0;
	//exdebug($up_deli_basefee);
	//exit; 
	
	if($deliinfook=="N") {
		if(file_exists($imagepath.$filename)) {
			unlink($imagepath.$filename);
		}
		if(file_exists($imagepath.$filename_m)) {
			unlink($imagepath.$filename_m);
		}
	}
	if($deliview=="IMAGE") {
		//이미지 업로드 처리
		$up_image=$_FILES["deliinfoimage"];
		if ($up_image["size"]>153600) {
			alert_go('이미지 용량은 150KB를 넘을 수 없습니다.');
		}
		$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
		if (ord($up_image['name']) && $up_image["size"]>0 && in_array($ext,array('gif','jpg'))) {
			$up_image['name']=$filename;
			if(file_exists($imagepath.$filename)) {
				unlink($imagepath.$filename);
			}
			move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image['name']);
			chmod($imagepath.$up_image['name'],0606);
		}

		$up_image_m=$_FILES["deliinfoimage_m"];
		if ($up_image_m["size"]>153600) {
			alert_go('이미지 용량은 150KB를 넘을 수 없습니다.');
		}
		$ext_m = strtolower(pathinfo($up_image_m['name'],PATHINFO_EXTENSION));
		if (ord($up_image_m['name']) && $up_image_m["size"]>0 && in_array($ext_m,array('gif','jpg'))) {
			$up_image_m['name']=$filename_m;
			if(file_exists($imagepath.$filename_m)) {
				unlink($imagepath.$filename_m);
			}
			move_uploaded_file($up_image_m['tmp_name'],$imagepath.$up_image_m['name']);
			chmod($imagepath.$up_image_m['name'],0606);
		}

		$deli_info=$deliinfook."=".$deliview;
	} else if($deliview=="HTML") {
		$deliinfohtml = pg_escape_string($_POST["deliinfohtml"]);
		
		$deli_info=$deliinfook."={$deliview}=".$deliinfohtml;
	}else if($deliview=="N") {
		$deli_info=$deliview;
	}

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "deli_type			= '{$up_deli_type}', ";
	$sql.= "deli_basefee		= '{$up_deli_basefee}', ";
	$sql.= "deli_basefeetype	= '{$up_deli_basefeetype}', ";
	$sql.= "deli_miniprice		= '{$up_deli_miniprice}', ";
	$sql.= "deli_setperiod		= '{$up_deli_setperiod}', ";
	$sql.= "deli_limit			= '{$up_deli_limit}', ";
	$sql.= "order_msg			= '{$message}', ";
	$sql.= "deli_area			= '{$up_deli_area}', ";
	$sql.= "deli_area_limit		= '{$up_deli_area_limit}', ";
	$sql.= "deli_select			= '{$up_deli_select}', ";
	$sql.= "deli_info			= '{$deli_info}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"상품 배송관련 설정이 완료되었습니다.\"); }</script>";

	$log_content = "## 배송관련수정 ## - 배송료: $up_deli_basefee(예전 배송료 : $deli_basefee) 주문가격이 $up_deli_miniprice 보다 작으면 청구(0이면 무조건추가). 배송/교환/환불정보 수정 ## - 기본 - 노출여부 : ".$deliinfook." - 노출입력 : ".$deliview;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

$sql = "SELECT deli_type,deli_basefee,deli_basefeetype,deli_miniprice,deli_setperiod,deli_limit, deli_select, ";
$sql.= "order_msg,deli_area,deli_area_limit, deli_info FROM tblshopinfo ";

$result=pmysql_query($sql,get_db_conn());
if ($data=pmysql_fetch_object($result)) {
	$deli_type = $data->deli_type;
	$deli_basefee = $data->deli_basefee;
	$deli_basefeetype = $data->deli_basefeetype;
	$deli_limit = $data->deli_limit;
	$deli_area_limit = $data->deli_area_limit;
	$deli_select = $data->deli_select;


	if(ord($deli_limit)) {
		if($deli_basefeetype == "Y")
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
		if($deli_basefee==-9) $delivery="Y";
		else if($deli_basefee==0) $delivery="F";
		else {
			if($deli_basefeetype == "Y")
				$delivery="M";
			else
				$delivery="N";
		}
	}


	if(ord($deli_area_limit)) {
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
	if($deli_basefee<0) $deli_basefee=0;
	$deli_miniprice = $data->deli_miniprice;
	$deli_setperiod = $data->deli_setperiod;
	$deli_area = $data->deli_area;
	$order_msg = $data->order_msg;

		
	if(ord($data->deli_info)) {
		$tempdeli_info=explode("=",$data->deli_info);
		$deliinfook=$tempdeli_info[0];
		$deliinfotype=$tempdeli_info[1];
		if($deliinfook=="N"){
			$checked[deliview]["N"]="checked";
		}else{
			if($deliinfotype=="HTML") {
				$deliinfohtml=$tempdeli_info[2];
			}
			$checked[deliview][$deliinfotype]="checked";
		}
	} else {
		$checked[deliview]["N"]="checked";
	}
}
pmysql_free_result($result);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
function CheckForm() {
	var sHTML = oEditors.getById["ir1"].getIR();
	form1.deliinfohtml.value=sHTML;

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
		if (form.up_deli_basefeeM.value.length==0) {
			alert("배송료를 입력하세요.");
			form.up_deli_basefeeM.focus();
			return;
		} else if (isNaN(form.up_deli_basefeeM.value)) {
			alert("배송료는 숫자만 입력 가능합니다.");
			form.up_deli_basefeeM.focus();
			return;
		} else if(form.up_deli_basefeeM.value<=0) {
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			form.up_deli_basefeeM.focus();
			return;
		}
	}
	if(form.up_delivery[3].checked){
		if (form.up_deli_basefeeN.value.length==0) {
			alert("배송료를 입력하세요.");
			form.up_deli_basefeeN.focus();
			return;
		} else if (isNaN(form.up_deli_basefeeN.value)) {
			alert("배송료는 숫자만 입력 가능합니다.");
			form.up_deli_basefeeN.focus();
			return;
		} else if(form.up_deli_basefeeN.value<=0) {
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			form.up_deli_basefeeN.focus();
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
				alert('배송료금액은 0 이상의 숫자만 입력 가능합니다.');
				document.getElementById("deli_limitfee"+i).focus();
				return;
			}
		}
	}
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
	*/
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
/*
	if(document.form1.deliview[0].checked) {
		if(confirm("배송/교환/환불정보 노출안함일경우 등록되있던 텍스트및 이미지가 삭제됩니다.")) {
			
		}else{
			return;
		}
	}
*/
	form.type.value="up";
	form.submit();
}

function SetDisplayTime(tf){
	form1.up_time1.disabled=tf;
	form1.up_time2.disabled=tf;
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
			document.form1.up_deli_basefeeM.disabled=false;
			document.form1.up_deli_basefeeM.style.background='#FFFFFF';
			document.form1.up_deli_basefeeN.disabled=true;
			document.form1.up_deli_basefeeN.style.background='#EFEFEF';
		} else {	
			document.form1.up_deli_basefeeM.disabled=true;
			document.form1.up_deli_basefeeM.style.background='#EFEFEF';
			document.form1.up_deli_basefeeN.disabled=false;
			document.form1.up_deli_basefeeN.style.background='#FFFFFF';
		}
		document.form1.up_deli_miniprice.disabled=false;
		document.form1.up_deli_miniprice.style.background='#FFFFFF';
	} else {
		document.form1.up_deli_basefeeM.disabled=true;
		document.form1.up_deli_basefeeM.style.background='#EFEFEF';
		document.form1.up_deli_basefeeN.disabled=true;
		document.form1.up_deli_basefeeN.style.background='#EFEFEF';
		document.form1.up_deli_miniprice.disabled=true;
		document.form1.up_deli_miniprice.style.background='#EFEFEF';
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
			document.getElementById("deli_limitup"+i).style.background='#EFEFEF';
			document.getElementById("deli_limitdown"+i).disabled=true;
			document.getElementById("deli_limitdown"+i).style.background='#EFEFEF';
			document.getElementById("deli_limitfee"+i).disabled=true;
			document.getElementById("deli_limitfee"+i).style.background='#EFEFEF';
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 배송관리 &gt;<span>상품 배송관련 기능설정</span></p></div></div>
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
					<div class="title_depth3">상품 배송관련 기능설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 배송관련 조건을 쇼핑몰 성격에 맞게 설정하실 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송업체 정보 설정</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                        
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>배송업체 정보 등록 및 주문배송조회 정보를 설정 할 수 있씁니다</li>
							<li><input type="button" onclick=
							"window.open('deli_company_config.php','_blank','width=820,height=300,resizable=1,scrollbars=1')" value="설정하기"></li>
                        </ul>
                    </div>
                        
                </td>
			</tr>
<!--
			<tr>
				<td>
					<div class="title_depth3_sub">배송방법 선택</div>
				</td>
			</tr>
-->
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=up_deli_area>
<!--
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 쇼핑몰의 이용안내페이지에서 배송안내부분에 설정한 내용이 표기됩니다.(이용안내를 개별 디자인할 경우는 제외)</li>
                            <li>2) 1일로 설정시 1~4일, 2일로 설정시 2~5일 로 3일이 더해져 표기됩니다.</li>
                        </ul>
                    </div>
                        
                </td>
			</tr>
-->
			<tr style='display:none;'>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>배송 방법 선택</span></th>
					<TD class="td_con1"><?php ${"deli_select_".$deli_type} = "selected"; ?><select name="up_deli_type" class="select">
					<option <?=$deli_select_T?> value="T">택배
					<option <?=$deli_select_P?> value="P">빠른등기
					<option <?=$deli_select_I?> value="I">일반등기
					<option <?=$deli_select_X?> value="X">택배+빠른등기
					<option <?=$deli_select_S?> value="S">택배+일반등기
					<option <?=$deli_select_M?> value="M">직접배송
					</select></TD>
				</TR>
				<TR>
					<th><span>설명</span></th>
					<TD class="td_con1">주문하신날로 부터 받을 수 있는 날은 <input type=text name=up_deli_setperiod size=2 maxlength=2 value="<?=$deli_setperiod?>" class="input">일 ~ +3일 안에 받을 수 있습니다.</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송료 설정</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                        
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 무료, 단일 항목 중 하나만 선택 가능합니다.</li>
                            <li>2) 기본 배송료 무료 선택시 추가로 설정할 항목은 없습니다.</li>
                            <li>3) 단일 유로 배송료 선택시 추가로 필요한 항목을 확인하신 후 입력해 주세요.</li>
                            <li>4) 상품 등록/수정 페이지에서도 개별적으로 배송료를 설정할 수 있습니다.</li>
                            <li>5) 상품 등록/수정 페이지에서 개별배송비를 사용할 경우 해당 상품은 <b>추가로 개별배송비가 청구</b> 됩니다.</li>
                        </ul>
                    </div>
                        
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<!-- 배송료 책정방법 변경 -->
				<TR>
					<TH><span>배송료 선택</span></TH>
					<TD class="td_con1">
						<input type='radio' name='basefeetype_select' id='basefeetype_0' value='0' <? if( $deli_basefeetype == '0' || is_null($deli_basefeetype) ) { echo 'checked'; } ?> >
						<label for='basefeetype_0'>배송료 <font color='#0000FF'><b>무료</b></font></label>
						<input type='radio' name='basefeetype_select' id='basefeetype_1' value='1' <? if( $deli_basefeetype == '1' ) { echo 'checked'; } ?> >
						<label for='basefeetype_1' >배송료 <font color='#FF0000'><b>유료</b></font></label>
					</TD>
				</TR>
				<TR style="display:none">
					<TH><span>지불방법</span></TH>
					<TD>
						<input type='radio' name='deli_select' id='deli_0' value='0' <? if( $deli_select == '0' || is_null($deli_select) ) { echo 'checked'; } ?> >
						<label for='deli_0' >배송료 <font color='#CC3D3D'><b>선불</b></font></label>
						<input type='radio' name='deli_select' id='deli_1' value='1' <? if( $deli_select == '1' ) { echo 'checked'; } ?> >
						<label for='deli_1' >배송료 <font color='#47C83E'><b>착불</b></font></label>
						<input type='radio' name='deli_select' id='deli_2' value='2' <? if( $deli_select == '2' ) { echo 'checked'; } ?> >
						<label for='deli_2' >배송료 <font color='#4374D9'><b>구매자( 선불/착불 ) 선택</b></font></label>
					</TD>
				</TR>
				<TR>
					<TH><span>배송료</span></TH>
					<TD>
						배송료 <input type='text' name='basefee_select' value='<?=$deli_basefee?>' style='text-align: right;'> 원
						<div style='margin-top : 3px; padding : 5px 5px 0px 0px;' >
						<TABLE cellSpacing='0' cellPadding='0' width="100%" border='0' >
							<tr>
								<td align="center" style="border : 3px #57B54A solid; padding : 5px; ">
									구매금액 <input type='text' name='minprice_select' size='10' maxlength='10' value="<?=$deli_miniprice?>" 
										class="input" style="text-align:right;">
									원 미만일 경우 배송비가 청구됩니다.&nbsp;
									<span class=font_orange style="font-size:8pt; letter-spacing:-0.5pt;">
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
					<th><span>무료 배송료</span></th>
					<TD class="td_con1">
				
					<input type=radio class="radio" id="idx_delivery0" name=up_delivery value="F" <?=($delivery=="F"?"checked":"")?> onClick="SetDeliChange('','');"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery0>배송료 <font color='#0000FF'><b>무료</b></font></label><br>
					<input type=radio class="radio" id="idx_delivery1" name=up_delivery value="Y" <?=($delivery=="Y"?"checked":"")?> onClick="SetDeliChange('','');"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery1>배송료 <font color='#38A422'><b>착불</b></font></label>&nbsp;<span class=font_orange style="font-size:8pt; letter-spacing:-0.5pt;">* 착불의 경우는 장바구니에 배송료가 소비자 부담이라는 문구가 출력됩니다.</span>
					</td>
				</TR>
				<tr>
					<th><span>단일 유료 배송료</span></th>
					<TD class="td_con1">
				                        <div class="table_none">
				                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				                        <tr>
				                            <td><input type=radio class="radio" id="idx_delivery2" name=up_delivery value="M" <?=($delivery=="M"?"checked":"")?> onClick="SetDeliChange('A','M')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery2>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액도 <font color='#0000FF'><b>포함</b></font>)</label>: <input type=text name=up_deli_basefeeM size=10 maxlength=6 value="<?=($delivery == "M"?$deli_basefee:"")?>" class="input" style="text-align:right;" disabled style="background-Color:#EFEFEF;">원<br>
				                        
				                            <input type=radio class="radio" id="idx_delivery3" name=up_delivery value="N" <?=($delivery=="N"?"checked":"")?> onClick="SetDeliChange('A','N')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery3>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액은 <font color='#FF0000'><b>제외</b></font>)</label>: <input type=text name=up_deli_basefeeN size=10 maxlength=6 value="<?=($delivery == "N"?$deli_basefee:"")?>" class="input" style="text-align:right;" disabled style="background-Color:#EFEFEF;">원</td>
				                        </tr>
				                        <tr>
				                            <td height="5"></td>
				                        </tr>
				                        <tr>
				                            <td style="padding-left:20px;">
				                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				                            <tr>
				                                <td align="center" style="border:3px #57B54A solid;padding:5px;">구매금액 <input type=text name=up_deli_miniprice size=10 maxlength=10 value="<?=$deli_miniprice?>" class="input" style="text-align:right;">원 미만일 경우 배송비가 청구됩니다.&nbsp;<span class=font_orange style="font-size:8pt; letter-spacing:-0.5pt;">* 구매금액 0 원 입력시 모든 금액에 배송비가 부과됩니다.</span></TD>
				                            </tr>
				                            </table>
				                            </td>
				                        </tr>
				                        </table>
				                        </div>
					</td>
				</tr> -->
				<tr style="display:none;">
					<th><span>차등 유료 배송료</span></th>
					<TD class="td_con1">
                        <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <tr>
                            <td><input type=radio class="radio" id="idx_delivery4" name=up_delivery value="P" <?=($delivery=="P"?"checked":"")?> onClick="SetDeliChange('B','P')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery4>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액도 <font color='#0000FF'><b>포함</b></font>)</label><br>
                        
                            <input type=radio class="radio" id="idx_delivery5" name=up_delivery value="Q" <?=($delivery=="Q"?"checked":"")?> onClick="SetDeliChange('B','Q')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_delivery5>배송료 <font color='#FF0000'><b>유료</b></font>(배송비 산출시 개별배송 상품금액은 <font color='#FF0000'><b>제외</b></font>)</label></td>
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
                                <?php
                                $j=1;
                                for($i=0; $i<$maxi; $i++) { 
                                ?>
                                <tr align="center">
                                    <td style="padding:5px;padding-bottom:0px;padding-left:0px;border-right:2px #FF7100 solid;"><b><?=str_pad($j, 2, "0", STR_PAD_LEFT);?>. </b><input type=text name=up_deli_limitup[] value="<?=($i==0?$i:$deli_limitup[$i])?>" <?=($i==0?" readonly":"")?> size=20 maxlength=10 class="input" style="text-align:right;" id="deli_limitup<?=$i?>"><b>원 이상&nbsp;&nbsp;∼&nbsp;&nbsp;</b><input type=text name=up_deli_limitdown[] value="<?=$deli_limitdown[$i]?>" size=20 maxlength=10 class="input" id="deli_limitdown<?=$i?>" <?=($j==$maxi?"":" onKeyDown=\"SetValueCopy(this.value,'deli_limitup{$j}');\" onKeyUp=\"SetValueCopy(this.value,'deli_limitup{$j}');\"")?> style="text-align:right;"><b>원 미만</b></td>
                                    <td align="center" style="padding:5px;padding-bottom:0px;"><input type=text name=up_deli_limitfee[] value="<?=$deli_limitfee[$i]?>" size=12 maxlength=6 class="input" style="text-align:right;" id="deli_limitfee<?=$i?>"><b>원</b></td>
                                </tr>
                                <?php 
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
                                <td style="padding:2px;"><span class=font_orange>
                                * 미입력시 기본값 : 원 이상 항목은 "0", 원 미만 항목은 무한, 배송료 항목은 "0" 입니다.<br>
                                * 입력시에는 0 이상의 숫자만 입력해 주세요.<br>* 사용하지 않는 라인은 빈란으로 처리해 주세요.<br>* 차등 배송료 범위에 속하지 않는 구매금액은 배송료 무료가 됩니다.<br>* 차등 배송료 범위가 겹치는 경우 우선순위는 01 ~ 05 입니다.
                                </span></td>
                            </tr>
                            </table>
                            </td>
                        </tr>
                        </table>
                    </div>
					</td>
				</tr>
				</TABLE>
				</td>
			</tr>
<?php
/*
			if($delivery=="M" || $delivery=="N") { 
				echo "<script>SetDeliChange('A','{$delivery}');</script>";
			} else if($delivery=="P" || $delivery=="Q") { 
				echo "<script>SetDeliChange('B','{$delivery}');</script>";
			} else { 
				echo "<script>SetDeliChange('','');</script>";
			}
*/
?>
			<!-- <tr>
				<td>
					<div class="title_depth3_sub">지역별 배송료 조건</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
			                    <div class="help_info01_wrap">
			                        <ul>
			                            <li>1) 특수지역 차등 유료 배송비에 해당될 경우 유료배송비는 특수지역 차등배송비로 교체 됩니다.(개별배송료 정상 청구함)</li>
			                            <li>2) 특수지역, 차등 배송료 범위 모두에 해당될때에만 유료배송료와 교체 됩니다.</li>
			                            <li>3) 차등 항목 미입력시 기본값 : 원 이상 항목은 "0", 원 미만 항목은 무한, 배송료 항목은 "0" 입니다.</li>
			                            <li>4) 차등 항목 입력시에는 0 이상의 숫자만 입력해 주세요.</li>
			                            <li>5) 차등 항목 사용하지 않는 라인은 빈란으로 처리해 주세요.</li>
			                            <li>6) 차등 배송료 범위가 겹치는 경우 우선순위는 01 ~ 05 입니다. </li>
			                        </ul>
			                    </div>
			                        
			                </td>
			</tr> -->
			<tr style='display:none;'>
				<td>
                
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td>
                    <div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <col width="40" />
                    <col width="40" />
                    <col width="" />
					<TR>
						<th>사용</th>
                        <th>번호</th>
                        <th>지역명 (도서,산간 등), 차등 배송료</th>
					</TR>
<?php
		for($k=0;$k<$maxj;$k++){
?>
					<TR align="center">
						<TD><input type=checkbox name="up_gradedeliareanum[]" value="Y" id="idx_gradedeliareanum<?=$k?>" onclick="setGradeDeliUse(this.checked,'<?=$k?>');" <?=(ord($gradedeli_area[$k])?"checked":"")?>></td>
						<TD><b><?=($k+1)?></b></td>
						<TD>
                        <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="96%" border=0>
						<tr align="center">
							<td height="25">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width="60"></col>
							<col width=""></col>
							<tr>
								<td><b>지역명 : </b></td>
								<td><input type=text name="up_gradedeli_area[]" value="<?=$gradedeli_area[$k]?>" id="idx_gradedeli_area<?=$k?>" size="78" style="width:100%; <?=(ord($gradedeli_area[$k])?"\"background:#FFFFFF;\"":"background:#EFEFEF;\"")?>" class="input"></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr id="idx_gradedeliarea<?=$k?>" <?=(ord($gradedeli_area[$k])?"style=\"display:;\"":"style=\"display:none;\"")?>>
							<td>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td style="border:3px #FF7100 solid;" bgcolor="#FFF7F0">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width=""></col>
								<col width="120"></col>
								<tr align="center" height="30">
									<td style="border-right:2px #FF7100 solid;"><b>차등<img width="10" height="0">구매금액<img width="10" height="0">설정</b></td>
									<td><b>배<img width="15" height="0">송<img width="15" height="0">료</b></td>
								</tr>
								<tr>
									<td style="padding-left:5px;padding-right:5px;border-right:2px #FF7100 solid;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
									<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
								</tr>
								<?php
								$j=1;
								for($i=0; $i<$maxi; $i++) { 
								?>
								<tr align="center">
									<td style="padding:5px;padding-bottom:0px;padding-left:0px;border-right:2px #FF7100 solid;"><b><?=str_pad($j, 2, "0", STR_PAD_LEFT);?>. </b><input type=text name=up_gradedeli_limitup[<?=$k?>][] value="<?=($i==0?$i:$gradedeli_limitup[$k][$i])?>" <?=($i==0?" readonly":"")?> size=20 maxlength=10 class="input" style="text-align:right;" id="gradedeli<?=$k?>_limitup<?=$i?>"><b>원 이상&nbsp;&nbsp;∼&nbsp;&nbsp;</b><input type=text name=up_gradedeli_limitdown[<?=$k?>][] value="<?=$gradedeli_limitdown[$k][$i]?>" size=20 maxlength=10 class="input" id="gradedeli<?=$k?>_limitdown<?=$i?>" <?=($j==$maxi?"":" onKeyDown=\"SetValueCopy(this.value,'gradedeli{$k}_limitup{$j}');\" onKeyUp=\"SetValueCopy(this.value,'gradedeli{$k}_limitup{$j}');\"")?> style="text-align:right;"><b>원 미만</b></td>
									<td align="center" style="padding:5px;padding-bottom:0px;"><input type=text name=up_gradedeli_limitfee[<?=$k?>][] value="<?=$gradedeli_limitfee[$k][$i]?>" size=12 maxlength=6 class="input" style="text-align:right;" id="deli<?=$k?>_limitfee<?=$i?>"><b>원</b></td>
								</tr>
								<?php 
									$j++;
								} 
								?>

								</table>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
                        </div>
						</td>
					</tr>
<?php
		}
?>
					</TABLE>
                	</div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr style='display:none;'>
            	<td style="padding-top:3pt; padding-bottom:3pt;">
                    <div class="help_info01_wrap">
                        <ul>
                            <li><SPAN class="font_orange"><b>특수배송지역 이름을 정확히 입력하세요.</b></SPAN><SPAN class=font_color1><BR></SPAN><SPAN class="font_orange"><b>잘못 입력시 특수배송지역이 아닌 곳에 배송비가 추가 될수 있습니다.</span></b></li>
                            <li>
                                    <TABLE class=tdbg cellSpacing=1 cellPadding=3 border=0 bgcolor="#D6E0E3">
                                    <TR class=td3 align=middle>
                                        <TD bgcolor="#ECFAFF">특수배송지역</TD>
                                        <TD bgcolor="#ECFAFF">문제발생원인</TD>
                                        <TD bgColor="#ECFAFF">&nbsp;바른입력(O)&nbsp;</TD>
                                        <TD bgcolor="#ECFAFF">틀린입력(X)</TD>
                                    </TR>
                                    <TR class=td3 align=middle>
                                        <TD bgcolor="#F7FDFF">백령 입력시</TD>
                                        <TD bgcolor="#F7FDFF">인천 옹진군 <FONT style="BACKGROUND-COLOR: #40BFEE">백령</FONT>면 / 경기 연천군 백학면 <FONT style="BACKGROUND-COLOR: #40BFEE">백령</FONT>리</TD>
                                        <TD bgColor="#F7FDFF">백령면</TD>
                                        <TD bgcolor="#F7FDFF">백령, 백령도, 백령리</TD>
                                    </TR>
                                    <TR class=td3 align=middle>
                                        <TD bgcolor="#F7FDFF">거제 입력시</TD>
                                        <TD bgcolor="#F7FDFF">경남 <FONT style="BACKGROUND-COLOR: #40BFEE">거제</FONT>시 <FONT style="BACKGROUND-COLOR: #40BFEE">거제</FONT>면 / 부산 연제구 <FONT style="BACKGROUND-COLOR: #40BFEE">거제</FONT>면<BR>전남 진도군 지산면 <FONT style="BACKGROUND-COLOR: #40BFEE">거제</FONT>리</TD>
                                        <TD bgColor="#F7FDFF">거제시</TD>
                                        <TD bgcolor="#F7FDFF">거제, 거제도, 거제면</TD>
                                    </TR>
                                    <TR class=td3 align=middle>
                                        <TD bgcolor="#F7FDFF">제주시 입력시</TD>
                                        <TD bgcolor="#F7FDFF">제주 북제주군 애월읍 제주극동방송<BR>제주 서귀포시 서홍동 남제주군청<BR>제주 <FONT style="BACKGROUND-COLOR: #40BFEE">제주시</FONT> 건입동 제주지방해양수산청</TD>
                                        <TD bgColor="#F7FDFF">제주</TD>
                                        <TD bgcolor="#F7FDFF">제주시, 제주군</TD>
                                    </TR>
                                    </TABLE>
                                   
                            </li>
                        </ul>
                    </div>
                        
                </td>
			</tr>
			<!-- <tr>
				<td>
					<div class="title_depth3_sub">지역별 배송료 조건 <span>유료배송비 + 특수지역 배송비 추가 및 할인을 설정합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td><span class=font_orange>* 추가배송료 할인시(-) 입력하세요.</span></td>
			</tr> -->
			<tr style='display:none;'>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="40" />
                <col width="" />
                <col width="120" />
				<TR>
					<th>번호</th>
					<th>지역명 (도서,산간 등)</th>
					<th>추가배송료 (+,-)</th>
				</TR>
<?php
		$array_deli=explode("|",$deli_area);
		for($i=0;$i<$maxnum;$i++){
?>
						<TR>
							<TD><?=($i+1)?></td>
							<TD><input type=text name=up_deliarea size=78 style="width:99%" value="<?=$array_deli[$i*2]?>" class=input></td>
							<TD><input type=text name=up_deliareaprice size=15 maxlength=10 value="<?=$array_deli[$i*2+1]?>" class=input></td>
						</tr>

<?php
}
?>
				</TABLE>
                </div>
				</td>
			</tr>
			<!-- <tr>
				<td>
					<div class="title_depth3_sub">희망 배송일자 설정 <span>고객이 희망 배송일자를 입력할 수 있습니다.</span></div>
				</td>
			</tr> -->
			<tr style='display:none;'>
				<td>
<?php 
		if (ord($time1)==0) $time1=8;
		if (ord($time2)==0) $time2=21;
		$deliverydate = explode("=",$order_msg);
		if (strlen($deliverydate[1])==6) {
			$wantdate="Y";
			$day1=substr($deliverydate[1],0,2);
			$time1=substr($deliverydate[1],2,2);
			$time2=substr($deliverydate[1],4,2);
		} else if (ord($deliverydate[1])) {
			$wantdate="A";
			$day1=substr($deliverydate[1],0,2);
		} else {
			$wantdate="N";
		}

		$bankname=$deliverydate[2];
		if (ord($bankname)==0) $bankname="N";
?>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>희망 배송일자 타입</span></th>
					<TD class="td_con1"><input type=radio class="radio" id="idx_wantdate1" name=up_wantdate value="Y" <?php if($wantdate=="Y") echo "checked"?> onclick="SetDisplayTime(false)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_wantdate1>00월 00일 00시(<b>시간까지</b>)</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio class="radio" id="idx_wantdate2" name=up_wantdate value="A" <?php if($wantdate=="A") echo "checked"?> onclick="SetDisplayTime(true)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_wantdate2>00월 00일(<b>일자까지</b>)</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio class="radio" id="idx_wantdate3" name=up_wantdate value="N" <?php if($wantdate=="N") echo "checked"?> onclick="SetDisplayTime(true)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_wantdate3>사용안함</label></TD>
				</TR>
				<TR>
					<th><span>배송가능일자</span></th>
					<TD class="td_con1">주문후 <select name=up_day1 class="select">
<?php
			for ($i=0;$i<30;$i++) {
				$temp=sprintf("%02d",$i);
				echo "<option vlaue=".$temp;
				if($day1==$temp) echo " selected";
				echo ">".$temp;
			}
?>
						</select>일 다음부터 배송 가능
					</TD>
				</TR>													
				<TR>
					<th><span>배송가능시간</span></th>
					<TD class="td_con1"><select name=up_time1 class="select">
<?php
			for ($i=0;$i<24;$i++) {
				$temp=sprintf("%02d",$i);
				echo "<option vlaue=$temp";
				if($time1==$temp) echo " selected";
				echo ">$temp";
			}
?>
						</select>시 ~ <select name=up_time2 class="select">
<?php
			for ($i=0;$i<24;$i++) {
				$temp=sprintf("%02d",$i);
				echo "<option vlaue=$temp";
				if($time2==$temp) echo " selected";
				echo ">$temp";
			}
?>
						</select>시까지
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<?php if($wantdate!="Y") echo "<script>SetDisplayTime(true)</script>"; ?>
<!--
			<tr>
				<td>
					<div class="title_depth3_sub">입금자명 설정 <span>주문자와 입금자명이 다를 경우 주문서 입금자명을 받을 수 있습니다.</span></div>
				</td>
			</tr>
-->
			<tr style='display:none;'>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>입금자명 선택</span></th>
					<TD class="td_con1"><p><input type=radio id="idx_bankname1" name=up_bankname value="Y" <?php if($bankname=="Y") echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bankname1>입금자명 받음</label>  &nbsp;&nbsp;<input type=radio id="idx_bankname2" name=up_bankname value="N" <?php if($bankname=="N") echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bankname2>입금자명 받지 않음</label></p></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap mb-5">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>상품 등록/수정 페이지에서도 개별적으로 배송료를 설정할 수 있으며, 개별배송료 사용시 해당 상품은 기본 배송료가 아닌 <b>개별배송료 설정만</b> 따르게 됩니다.</li>
							<li>지역별 배송비를 설정한 경우 <b>기본배송비에 지역별 배송비가 추가로 청구</b>됩니다.</li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송/교환/환불정보 입력</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>배송정보</span></th>
					<td>
						<TABLE cellSpacing='0' cellPadding='0' width="100%" border='0'  style="border:0px;">
							<tr>
								<td style="border:0px;">
									<input type=radio id="idx_deliview_N" name=deliview value="N" <?=$checked[deliview]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliview_N><b>노출안함</b></label>
								</td>
							</tr>
							<tr>
								<td  style="border:0px;">
									<input type=radio id="idx_deliview_T" name=deliview value="HTML" <?=$checked[deliview]["HTML"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliview_T><b>텍스트로 등록</b></label>
									<div style="padding-top:5px;"><textarea name=deliinfohtml id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$deliinfohtml?></textarea></div>
								</td>
							</tr>
							<tr>
								<td  style="border:0px;">
									<input type=radio id="idx_deliview_I" name=deliview value="IMAGE" <?=$checked[deliview]["IMAGE"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliview_I><b>이미지 등록</b> - 150kb 미만, GIF/JPG파일</label>
									<div style="padding-top:5px;">
										PC<span style="padding-left:33px;">:</span> <input type=file name="deliinfoimage" size=50>
	<?php
							if ($deliinfotype=="IMAGE") {
								if(file_exists($imagepath.$filename)) {
									$width=getimagesize($imagepath.$filename);
									if($width[0]>=585) $width=" width=585 ";
								}
								echo "<img width=0 height=10><br><img src=\"".$imagepath.$filename."\" {$width}>\n";
							}
?>
							<br>
									</div>
									<div style="padding-top:5px;">
										MOBILE : <input type=file name="deliinfoimage_m" size=50>
<?php
							if ($deliinfotype=="IMAGE") {
								if(file_exists($imagepath.$filename_m)) {
									$width=getimagesize($imagepath.$filename_m);
									if($width[0]>=585) $width=" width=585 ";
								}
								echo "<img width=0 height=10><br><img src=\"".$imagepath.$filename_m."\" {$width}>\n";
							}
?>
									</div>

								</td>
							</tr>
						</table>
						</div>
						
						

					</td>
				</TR>
				</TABLE>
				</td>
			</tr>

			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>배송/교환/환불정보는 상품 상세페이지에 출력되며, [텍스트 또는 이미지]를 선택하여 등록하실 수 있습니다.</li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
						<!-- <dl>
							<dt><span>유료배송료</span></dt>
							<dd>- 배송료는 [무료,착불,유료] 구분합니다. 무료/착불 선택시 배송료를 설정 하지 않아도 됩니다.<br>
							- 유료배송료 사용시 개별배송비 상품을 제외한 모든 상품에 동일하게 계산됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>개별배송료</span></dt>
							<dd>- 개별배송료는 상품등록/수정에서 개별배송료를 선택할 수 있습니다.<br>
							<b>&nbsp;&nbsp;</b>개별배송료 사용시 해당 상품은 기본 배송료가 아닌 개별배송료 설정만 따르게 됩니다.<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><b>※ 구매수마다 배송료 청구</b>(개별배송비 상품에서만 적용 가능)<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>- 개별배송비 상품 구매수마다 배송료를 청구할 수 있습니다.<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>- 상품등록/수정에서 개별배송료 선택, 배송료 타입 ‘상품수 대비 배송료 증가’ 체크<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>예) 개별배송비 2,000원과 ‘상품수 대비 배송료 증가’ 체크가된 설정된 상품(5,000원) 5개 구매<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><b>[</b>상품가격(5,000원) + 개별배송비(2,000원)<b>]</b> × 수량(5개) = 35,000원<br>
							<b><font color="#0000FF">예) 유료배송료 적용되는 경우</font></b>(구매액 3만원 미만일때 유료배송료 2,500원, 개별배송료 2,000원)<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;기본상품(12,000원) + 개별배송료 상품(15,000원) = 27,000원<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;기본상품 배송료(2,500원) + 개별배송료 상품 배송료(2,000원) = 4,500원<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;총 결제금액 = 31,500원<br>
							<b><font color="#FF0000">예) 유료배송료 적용되지 않을 경우</font></b>(구매액 3만원 미만일때 유료배송료 2,500원, 개별배송료 2,000원)<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;기본상품(20,000원) + 개별배송료 상품(15,000원) = 35,000원<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;기본상품 배송료(0원) + 개별배송료 상품 배송료(2,000원) = 2,000원<br>
							<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;총 결제금액 = 37,000원
							</dd>
						</dl> -->

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

<SCRIPT LANGUAGE="JavaScript">
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
