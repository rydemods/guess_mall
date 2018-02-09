<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(ord($_ShopInfo->getId())==0){
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}


$mode =     $_POST["mode"];
$area_name =     $_POST["area_name"];
$st_zipcode =     $_POST["st_zipcode"];
$en_zipcode =     $_POST["en_zipcode"];
$upfile_pd =     $_POST["upfile_pd"];
$deli_price =     $_POST["deli_price"];

if ($mode=="detail_ins") {
	$sql="insert into tbldeliarea(area_name, st_zipcode, en_zipcode, deli_price, area_date) values ('".$area_name."','".$st_zipcode."','".$en_zipcode."','".$deli_price."','".date("YmdHis")."')";
	//echo $sql;
	pmysql_query($sql);
	echo "<script>alert('지역별 배송비가 등록되었습니다.');opener.location.reload();window.close();</script>";
	exit;
} else if ($mode=="excel_ins") {
	if($_FILES['upfile_pd'][tmp_name]){
		$fp = fopen( $_FILES['upfile_pd'][tmp_name], 'r' );
		$nn=0;
		while ( $data = fgetcsv( $fp, 135000, ',' ) ){
			if($nn++==0) continue;
			foreach( $data as $fKey=>$fVal ){
				$data[$fKey] = iconv( 'euc-kr', 'utf-8', $fVal );
			}
			
			$sql="insert into tbldeliarea(area_name, st_zipcode, en_zipcode, deli_price, area_date) values ('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."','".date("YmdHis")."')";
			pmysql_query($sql);
		}
	}
	echo "<script>alert('지역별 배송비가 등록되었습니다.');opener.location.reload();window.close();</script>";
	exit;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>지역별 배송비 설정</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.1.min.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 150;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if(!$("#area_name").val() && $("#mode").val()=="detail_ins"){
		alert("특수지역명을 입력해주세요.");
		$("#area_name").focus();
		return;
	}else if(!$("#st_zipcode").val() && $("#mode").val()=="detail_ins"){
		alert("시작 우편번호를 입력해주세요.");
		$("#st_zipcode").focus();
		return;
	}else if(!$("#en_zipcode").val() && $("#mode").val()=="detail_ins"){
		alert("끝 우편번호를 입력해주세요.");
		$("#en_zipcode").focus();
		return;
	}else if(($("#st_zipcode").val() > $("#en_zipcode").val()) && $("#mode").val()=="detail_ins"){
		alert("시작 우편번호는 끝 우편번호보다 클수 없습니다.");
		$("#en_zipcode").focus();
		return;
	}else if(!$("#upfile_pd").val() && $("#mode").val()=="excel_ins"){
		alert("엑셀파일을 등록해주세요.");
		return;
	}else if(!$("#deli_price").val() && $("#mode").val()=="detail_ins"){
		alert("지역 배송비를 입력해주세요.");
		$("#deli_price").focus();
		return;
	}else{
		if (confirm("배송비 지역이 겹칠경우 더 높은 배송비가 부과됩니다.")) {
			document.form1.submit();
		}
	}
}
function ins_type(num){
	if(num=="1"){
		$(".detail_search").show();
		$(".excel_search").hide();
		$("#mode").val("detail_ins");
	}else if(num=="2"){
		$(".excel_search").show();
		$(".detail_search").hide();
		$("#mode").val("excel_ins");
	}
}

function openDaumPostcode(wi) {
	new daum.Postcode({
		oncomplete: function(data) {
			if(wi=="st"){
				document.getElementById('st_zipcode').value = data.zonecode;
			}else if(wi=="en"){
				document.getElementById('en_zipcode').value = data.zonecode;
			}
		}
	}).open();
}
//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p>지역별 배송비 설정</p></div>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<!-- <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" > -->
<TABLE WIDTH="650" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post  enctype="multipart/form-data">
<input type=hidden name=mode id=mode value="detail_ins">

<TR>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR class="detail_search">
			<th><span>특수지역명*</span></th>
			<TD class="td_con1"><input type=text name=area_name id=area_name style="width:300px;"class="input"></TD>
		</TR>
		<TR>
			<th><span>추가방법*</span></th>
			<TD class="td_con1">
				<input type="radio" name="area_type" checked onclick="javascript:ins_type(1)">상세 우편번호 입력 <input type="radio" name="area_type" onclick="javascript:ins_type(2)">엑셀파일등록
			</TD>
		</TR>
		
        <TR class="detail_search">
			<th><span>지역검색*</span></th>
			<TD class="td_con1">
				<input type=text name=st_zipcode id=st_zipcode style="width:80;text-align:right"class="input"> <a href="javascript:openDaumPostcode('st')"><img src="images/order_no_uimg.gif" border="0" align="absmiddle"></a>부터
				<input type=text name=en_zipcode id=en_zipcode style="width:80;text-align:right"class="input"> <a href="javascript:openDaumPostcode('en')"><img src="images/order_no_uimg.gif" border="0" align="absmiddle"></a>까지
			</TD>
		</TR>
		
		<TR class="excel_search" style="display:none;">
			<th><span>엑셀파일 등록*</span><br>&nbsp;<a href="./sample/지역별배송비-엑셀샘플.csv"><input type="button" value="샘플파일 다운로드" style="padding:2px 5px 1px"></a></th>
			<TD class="td_con1">
				<input type='file' name='upfile_pd' id='upfile_pd' ><br />
				<span class="font_orange" style="font-size:9px; padding-left:15px;">- CSV파일만 등록가능하며, 샘플파일과 동일한 형식으로 입력해주시기 바랍니다.</span>
			</TD>
		</TR>
		<TR class="detail_search">
			<th><span>추가 배송비*</span></th>
			<TD class="td_con1"><input type=text name=deli_price id=deli_price style="width:80;text-align:right"class="input">원</TD>
		</TR>
		</TABLE>
        </div>
		</td>
	</tr>
	
	<tr>
		<td class="font_blue"><hr size="1" noshade color="#F3F3F3"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_input.gif" border="0" vspace="0" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
