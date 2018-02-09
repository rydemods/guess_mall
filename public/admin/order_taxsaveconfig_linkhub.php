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
$linkhub_ID			= $_shopdata->linkhub_id; // 가입자 아이디
$linkhub_PWD			= $_shopdata->linkhub_pwd;
$linkhub_CorpNum1	= substr($_shopdata->linkhub_corpnum,0,3); 
$linkhub_CorpNum2	= substr($_shopdata->linkhub_corpnum,3,2);
$linkhub_CorpNum3	= substr($_shopdata->linkhub_corpnum,5,5);
$linkhub_CorpNum		= $linkhub_CorpNum1.$linkhub_CorpNum2.$linkhub_CorpNum3; // 가입자 사업자 번호
$linkhub_CEOName		= $_shopdata->linkhub_ceoname; // 가입자 대표자명
$linkhub_CorpName	= $_shopdata->linkhub_corpname; // 가입자 회사명
$linkhub_Addr		= $_shopdata->linkhub_addr; // 가입자 주소
$linkhub_ZipCode_ex	= explode("-",$_shopdata->linkhub_zipcode);
$linkhub_ZipCode1	= $linkhub_ZipCode_ex[0];
$linkhub_ZipCode2	= $linkhub_ZipCode_ex[1];
$linkhub_ZipCode		= $linkhub_ZipCode1."-".$linkhub_ZipCode2;
$linkhub_BizType		= $_shopdata->linkhub_biztype;
$linkhub_BizClass	= $_shopdata->linkhub_bizclass;
$linkhub_ContactName = $_shopdata->linkhub_contactname;
$linkhub_ContactEmail_ex= explode("@",$_shopdata->linkhub_contactemail);
$linkhub_ContactEmail1= $linkhub_ContactEmail_ex[0];
$linkhub_ContactEmail2= $linkhub_ContactEmail_ex[1];
$linkhub_ContactEmail = $linkhub_ContactEmail1."@".$linkhub_ContactEmail2;
$linkhub_ContactTEL_ex	= explode("-",$_shopdata->linkhub_contacttel);
$linkhub_ContactTEL1	= $linkhub_ContactTEL_ex[0];
$linkhub_ContactTEL2	= $linkhub_ContactTEL_ex[1];
$linkhub_ContactTEL3	= $linkhub_ContactTEL_ex[2];
$linkhub_ContactTEL	= $linkhub_ContactTEL1."-".$linkhub_ContactTEL2."-".$linkhub_ContactTEL3;
$linkhub_ContactHP_ex	= explode("-",$_shopdata->linkhub_contacthp);
$linkhub_ContactHP1	= $linkhub_ContactHP_ex[0];
$linkhub_ContactHP2	= $linkhub_ContactHP_ex[1];
$linkhub_ContactHP3	= $linkhub_ContactHP_ex[2];
$linkhub_ContactHP	= $linkhub_ContactHP1."-".$linkhub_ContactHP2."-".$linkhub_ContactHP3;
$linkhub_ContactFAX_ex	= explode("-",$_shopdata->linkhub_contactfax);
$linkhub_ContactFAX1	= $linkhub_ContactFAX_ex[0];
$linkhub_ContactFAX2	= $linkhub_ContactFAX_ex[1];
$linkhub_ContactFAX3	= $linkhub_ContactFAX_ex[2];
$linkhub_ContactFAX	= $linkhub_ContactFAX1."-".$linkhub_ContactFAX2."-".$linkhub_ContactFAX3;


include "../linkhub/TaxinvoiceExample/common.php";


// 가입 여부 확인
try	{
	$result = $TaxinvoiceService->CheckIsMember($linkhub_CorpNum,$LinkID); 
	$code = mb_convert_encoding($result->code,"euc-kr","utf-8");
	$message = mb_convert_encoding($result->message,"euc-kr","utf-8");
} 
catch(PopbillException $pe) {
	$code = mb_convert_encoding($pe->getCode(),"euc-kr","utf-8");
	$message = mb_convert_encoding($pe->getMessage(),"euc-kr","utf-8");
}

############################################
// 발행 단가
try{
	$unitCost = $TaxinvoiceService->GetUnitCost($linkhub_CorpNum);
}catch(PopbillException $pe){
	$code = mb_convert_encoding($pe->getCode(),"euc-kr","utf-8");
	$message = mb_convert_encoding($pe->getMessage(),"euc-kr","utf-8");
}
// 공인인증서 만료일
try{
	$certificateExpireDate = $TaxinvoiceService->GetCertificateExpireDate($linkhub_CorpNum);
}catch(PopbillException $pe){
	$certificateExpireDate_code = mb_convert_encoding($pe->getCode(),"euc-kr","utf-8");
	$certificateExpireDate_message = mb_convert_encoding($pe->getMessage(),"euc-kr","utf-8");
}
// 연동회원 잔여 포인트 확인
try{
	$partnerBalance = $TaxinvoiceService->GetBalance($linkhub_CorpNum);
}catch(PopbillException $pe){
	$partnerBalance_code = mb_convert_encoding($pe->getCode(),"euc-kr","utf-8");
	$partnerBalance_message = mb_convert_encoding($pe->getMessage(),"euc-kr","utf-8");
}

###############################################

$popbill_login_url = "";
$popbill_point_url = "";
$popbill_publiclicense_url = "";
$popbill_tempdocu_url = "";
$popbill_salesdocu_url = "";
$popbill_makesales_url = "";
if ($code == "1") {
	
} else {
	$linkhub_ID			= ""; 
	$linkhub_PWD		= "";
	$linkhub_CorpNum1	= ""; 
	$linkhub_CorpNum2	= "";
	$linkhub_CorpNum3	= "";
	$linkhub_CorpNum		= $linkhub_CorpNum1.$linkhub_CorpNum2.$linkhub_CorpNum3;
	$linkhub_CEOName		= "";
	$linkhub_CorpName	= "";
	$linkhub_Addr		= "";
	$linkhub_ZipCode_ex	= "";
	$linkhub_ZipCode1	= "";
	$linkhub_ZipCode2	= "";
	$linkhub_ZipCode		= "";
	$linkhub_BizType		= "";
	$linkhub_BizClass	= "";
	$linkhub_ContactName = "";
	$linkhub_ContactEmail_ex = "";
	$linkhub_ContactEmail1= "";
	$linkhub_ContactEmail2= "";
	$linkhub_ContactEmail = "";
	$linkhub_ContactTEL_ex	= "";
	$linkhub_ContactTEL1	= "";
	$linkhub_ContactTEL2	= "";
	$linkhub_ContactTEL3	= "";
	$linkhub_ContactTEL	= "";
	$linkhub_ContactHP_ex	= "";
	$linkhub_ContactHP1	= "";
	$linkhub_ContactHP2	= "";
	$linkhub_ContactHP3	= "";
	$linkhub_ContactHP	= "";
	$linkhub_ContactFAX_ex	= "";
	$linkhub_ContactFAX1	= "";
	$linkhub_ContactFAX2	= "";
	$linkhub_ContactFAX3	= "";
	$linkhub_ContactFAX	= "";
}
?>

<?php include("header.php"); ?>
<style>
#loading {
  width: 100%;
  height: 100%;
  top: 0px;
  left: 0px;
  position: fixed;
  display: block;
  opacity: 0.7;
  background-color: #fff;
  z-index: 10000;
  text-align: center;
}

#loading-image {
  position: absolute;
  top: 100px;
  left: 240px;
  z-index: 100;
}
#lhub_corpnum_check_btn {
	display: inline-block;border-radius: 3px;border: 1px solid #0056D1;	padding: 4px 7px;
	color: #fff; font: 11px ngeb; cursor:pointer;
	text-align: center;
	border: 1px solid #0066FF;
	background: #BBE3FF;
	background: -moz-linear-gradient(top, #BBE3FF 0%, #f36363 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#BBE3FF), color-stop(100%,#0056D1));
	background: -webkit-linear-gradient(top, #BBE3FF 0%,#0056D1 100%);
	background: -o-linear-gradient(top, #BBE3FF 0%,#0056D1 100%);
	background: -ms-linear-gradient(top, #BBE3FF 0%,#0056D1 100%);
	background: linear-gradient(to bottom, #BBE3FF 0%,#0056D1 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#BBE3FF', endColorstr='#0056D1',GradientType=0 );
}

.popbill_btn {
	display: inline-block;border-radius: 3px;border: 1px solid #0A65B2;	padding: 7px 14px;
	color: #fff; font: 11px ngeb; cursor:pointer;
	text-align: center;
	border: 1px solid #52A3E7;
	background: #BBE3FF;
	background: -moz-linear-gradient(top, #BBE3FF 0%, #0A65B2 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#BBE3FF), color-stop(100%,#0A65B2));
	background: -webkit-linear-gradient(top, #BBE3FF 0%,#0A65B2 100%);
	background: -o-linear-gradient(top, #BBE3FF 0%,#0A65B2 100%);
	background: -ms-linear-gradient(top, #BBE3FF 0%,#0A65B2 100%);
	background: linear-gradient(to bottom, #BBE3FF 0%,#0A65B2 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#BBE3FF', endColorstr='#0A65B2',GradientType=0 );
}
.popbill_btn a{
	text-decoration: inherit;
	color: #fff;
}
</style>
<div id="loading" style="display:none;">
	<div id='loading-image'><img src="/images/ajax-loader.gif" width="70px"></div>                                                                                                                  
</div>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	<?
		if ($code == "1"){
	?>
		$("input[type='text']").prop("readonly",true);
		$("#lhub_corpnum_check_btn").hide();
		$("#email_domains").hide();
	<?	
		}	
	?>
    $(document).ajaxStart(function() {
		$('#loading').show();
		$("#loading-image").show().css({
            top:  ($(window).height()-20 )/2 + 'px',
            left: ($(window).width() )/2.2 + 'px'
        });
		
    }).ajaxStop(function() {
        $('#loading').hide();
		$("#loading-image").hide();
    });

});
function CheckForm() {
	//serialize()
	//세금계산서 ID
	if ($("#up_linkhub_ID").val() == ""){
		alert("세금계산서 ID 을 입력 하세요.");
		$("#up_linkhub_ID").focus();
		return;
	}
	if ($("#up_linkhub_ID").val().length < 6){
		alert("세금계산서 ID 는 6자 이상 20자 미만 어야 합니다.");
		$("#up_linkhub_ID").focus();
		return;
	}
	//세금계산서 비밀번호
	if ($("#up_linkhub_PWD").val() == ""){
		alert("세금계산서 비밀번호 을 입력 하세요.");
		$("#up_linkhub_PWD").focus();
		return;
	}
	if ($("#up_linkhub_PWD").val().length < 6){
		alert("세금계산서 비밀번호 는 6자 이상 20자 미만 어야 합니다.");
		$("#up_linkhub_PWD").focus();
		return;
	}
	//사업자등록번호
	if ($("#up_linkhub_Corp_check").val() != "y"){
		alert("사업자등록번호 을 확인 하세요.");
		$("#lhub_corpnum_check_btn").focus();
		return;
	}
	//대표자 성명
	if ($("#up_linkhub_CEOName").val() == ""){
		alert("대표자 성명 을 입력 하세요.");
		$("#up_linkhub_CEOName").focus();
		return;
	}
	//상호
	if ($("#up_linkhub_CorpName").val() == ""){
		alert("상호 을 입력 하세요.");
		$("#up_linkhub_CorpName").focus();
		return;
	}
	//사업장 우편주소
	if ($("#up_linkhub_ZipCode1").val() == ""){
		alert("사업장 우편주소 을 입력 하세요.");
		$("#up_linkhub_ZipCode1").focus();
		return;
	}
	if ($("#up_linkhub_ZipCode2").val() == ""){
		alert("사업장 우편주소 을 입력 하세요.");
		$("#up_linkhub_ZipCode2").focus();
		return;
	}
	//사업장 주소
	if ($("#up_linkhub_Addr").val() == ""){
		alert("사업장 주소 을 입력 하세요.");
		$("#up_linkhub_Addr").focus();
		return;
	}
	//업태
	if ($("#up_linkhub_BizType").val() == ""){
		alert("업태 을 입력 하세요.");
		$("#up_linkhub_BizType").focus();
		return;
	}
	//업종
	if ($("#up_linkhub_BizClass").val() == ""){
		alert("업종 을 입력 하세요.");
		$("#up_linkhub_BizClass").focus();
		return;
	}
	//담당자명
	if ($("#up_linkhub_ContactName").val() == ""){
		alert("담당자명 을 입력 하세요.");
		$("#up_linkhub_ContactName").focus();
		return;
	}
	//담당자 이메일
	if ($("#up_linkhub_ContactEmail1").val() == ""){
		alert("담당자 이메일 을 입력 하세요.");
		$("#up_linkhub_ContactEmail1").focus();
		return;
	}
	if ($("#up_linkhub_ContactEmail2").val() == ""){
		alert("담당자 이메일 을 입력 하세요.");
		$("#email_domains").focus();
		return;
	}
	//담당자 전화번호
	if ($("#up_linkhub_ContactTEL1").val() == ""){
		alert("담당자 전화번호 을 입력 하세요.");
		$("#up_linkhub_ContactTEL1").focus();
		return;
	}
	if ($("#up_linkhub_ContactTEL2").val() == ""){
		alert("담당자 전화번호 을 입력 하세요.");
		$("#up_linkhub_ContactTEL2").focus();
		return;
	}
	if ($("#up_linkhub_ContactTEL3").val() == ""){
		alert("담당자 전화번호 을 입력 하세요.");
		$("#up_linkhub_ContactTEL3").focus();
		return;
	}
	//document.form1.action = "/linkhub/TaxinvoiceExample/JoinMember_ajax.php";
	//document.form1.submit();
	//return;
	$.ajax({
		type: "POST",
		url: "/linkhub/TaxinvoiceExample/JoinMember_ajax.php",
		data: $("#form1").serialize(),
		dataType:"JSON",
		beforeSend: function () {
			//$(".meno_list").html('');
		}
	}).done(function(data){
		if(data.code=="1") {
			alert(data.message);
			window.location.reload();
		} else {
			alert(data.message);
		}
		
	});
}
$(function(){
	$("#email_domains").change(function(){
		var _val = $(this).val();
		if (_val == ""){
			return;
		} else if (_val == "text"){
			$("#up_linkhub_ContactEmail2").prop("readonly",false);
			$("#up_linkhub_ContactEmail2").focus();
		} else {
			$("#up_linkhub_ContactEmail2").val('');
			$("#up_linkhub_ContactEmail2").val(_val);
			$("#up_linkhub_ContactEmail2").prop("readonly",true);
		}
	});
	
	$("#lhub_corpnum_check_btn").click(function(){
		if ($(this).text() == "확인"){
			if ($("#up_linkhub_CorpNum1").val() == "" || $("#up_linkhub_CorpNum2").val() == "" || $("#up_linkhub_CorpNum3").val() == ""){
				alert("사업자 등록번호를 입력 하세요.");
				$("#up_linkhub_Corp_check").val("n");
				$("#up_linkhub_CorpNum1").focus();
				return;
			}
			var _corpnum = $("#up_linkhub_CorpNum1").val()+$("#up_linkhub_CorpNum2").val()+$("#up_linkhub_CorpNum3").val();
			
			$.ajax({
				type: "POST",
				url: "/linkhub/TaxinvoiceExample/CheckIsMember_ajax.php",
				data: "CorpNum="+_corpnum,
				dataType:"JSON",
				beforeSend: function () {
					//$(".meno_list").html('');
				}
			}).done(function(data){
				if (data.code=="0"){
					alert("미가입 사업자 번호 입니다.");
					$("#up_linkhub_CorpNum1").prop("readonly",true);
					$("#up_linkhub_CorpNum2").prop("readonly",true);
					$("#up_linkhub_CorpNum3").prop("readonly",true);
					$("#up_linkhub_Corp_check").val("y");
					$("#lhub_corpnum_check_btn").text("다시입력");
				} else if(data.code=="1") {
					alert("이미 등록된 사업자 번호 입니다.\r관리자에게 문의하세요.");
					$("#up_linkhub_CorpNum1").prop("readonly",false);
					$("#up_linkhub_CorpNum2").prop("readonly",false);
					$("#up_linkhub_CorpNum3").prop("readonly",false);
					$("#up_linkhub_CorpNum1").val("").focus();
					$("#up_linkhub_CorpNum2").val("");
					$("#up_linkhub_CorpNum3").val("");
					$("#up_linkhub_Corp_check").val("n");
				} else {
					alert(data.message);
					$("#up_linkhub_CorpNum1").prop("readonly",false);
					$("#up_linkhub_CorpNum2").prop("readonly",false);
					$("#up_linkhub_CorpNum3").prop("readonly",false);
					$("#up_linkhub_CorpNum1").val("").focus();
					$("#up_linkhub_CorpNum2").val("");
					$("#up_linkhub_CorpNum3").val("");
					$("#up_linkhub_Corp_check").val("n");
				}
				
			});
				
		} else if ($(this).text() == "다시입력"){
			$("#up_linkhub_CorpNum1").prop("readonly",false);
			$("#up_linkhub_CorpNum2").prop("readonly",false);
			$("#up_linkhub_CorpNum3").prop("readonly",false);
			$("#up_linkhub_CorpNum1").val("").focus();
			$("#up_linkhub_CorpNum2").val("");
			$("#up_linkhub_CorpNum3").val("");
			$("#up_linkhub_Corp_check").val("n");
			$(this).text("확인");
		}
	});
	$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
	$(document).on("keyup", "input:text[engOnly]", function() {$(this).val( $(this).val().replace(/[0-9]|[^\!-z]/gi,"") );});
});
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 세금계산서 관리 &gt; <span>세금계산서 환경설정</span></p></div></div>

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
					<div class="title_depth3">세금계산서 환경설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>세금계산서 가입을 위한 사업자정보를 관리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<style>
				.input_warnning_info{
					color:red;
				}
			</style>
			<form name=form1 id=form1 action="" onsubmit="return false;" method=post>
			<input type=hidden name=mode>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">세금계산서 환경설정<br><span style="color:red;font-size: 12px;">세금계산서 발행용 정보입니다. <b>사업자 등록증</b>에 등록 되어있는 정보를 입력 해주세요.</span></div>
				</td>
			</tr>
			<?if($code=="1"){?>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>발행 단가</span></th>
					<TD class="td_con1">
					<input type=text name="" id="" value="<?=$unitCost?>" size=20 maxlength=20 class="input_selected" readonly />
					</TD>
				</TR>
				<TR>
					<th><span>공인인증서 만료일</span></th>
					<TD class="td_con1">
					<input type=text name="" id="" value="<?=substr($certificateExpireDate,0,4)."-".substr($certificateExpireDate,4,2)."-".substr($certificateExpireDate,6,2)?>" size=20 maxlength=20 class="input_selected" readonly />
					</TD>
				</TR><TR>
					<th><span>잔여 포인트</span></th>
					<TD class="td_con1">
					<input type=text name="" id="" value="<?=$partnerBalance?>" size=20 maxlength=20 class="input_selected" readonly />
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<?}?>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>세금계산서 ID</span></th>
					<TD class="td_con1">
					<input type=text name="up_linkhub_ID" id="up_linkhub_ID" value="<?=$linkhub_ID?>" size=20 maxlength=20 class="input_selected">
					<span class="input_warnning_info">(이상 20자 미만)</span>
					</TD>
				</TR>
				<TR>
					<th><span>세금계산서 비밀번호</span></th>
					<TD class="td_con1">
					<input type=text name="up_linkhub_PWD" id="up_linkhub_PWD" value="<?=$linkhub_PWD?>" size=20 maxlength=20 class="input_selected">
					<span class="input_warnning_info">(6자 이상 20자 미만)</span>
					</TD>
				</TR>
				<TR>
					<th><span>사업자등록번호</span></th>
					<TD class="td_con1">
					<input type=text numberOnly="true" name="up_linkhub_CorpNum1" id="up_linkhub_CorpNum1" value="<?=$linkhub_CorpNum1?>" size=3 maxlength=3 class="input_selected"> - 
					<input type=text numberOnly="true" name="up_linkhub_CorpNum2" id="up_linkhub_CorpNum2" value="<?=$linkhub_CorpNum2?>" size=2 maxlength=2 class="input_selected"> - 
					<input type=text numberOnly="true" name="up_linkhub_CorpNum3" id="up_linkhub_CorpNum3" value="<?=$linkhub_CorpNum3?>" size=5 maxlength=5 class="input_selected">
					<span id="lhub_corpnum_check_btn">확인</span>
					<input type="hidden" name="up_linkhub_Corp_check" id="up_linkhub_Corp_check" value="n" / >
					</TD>
				</TR>
				<TR>
					<th><span>대표자 성명</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_CEOName" id="up_linkhub_CEOName" value="<?=$linkhub_CEOName?>" size=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>상호</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_CorpName" id="up_linkhub_CorpName" value="<?=$linkhub_CorpName?>" size=50 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>사업장 우편주소</span></th>
					<TD class="td_con1">
					<input type=text numberOnly="true" name="up_linkhub_ZipCode1" id="up_linkhub_ZipCode1" value="<?=$linkhub_ZipCode1?>" size=3 maxlength=3 class="input_selected" > - 
					<input type=text numberOnly="true" name="up_linkhub_ZipCode2" id="up_linkhub_ZipCode2" value="<?=$linkhub_ZipCode2?>" size=3 maxlength=3 class="input_selected" >
					</TD>
				</TR>
				<TR>
					<th><span>사업장 주소</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_Addr" id="up_linkhub_Addr" value="<?=$linkhub_Addr?>" size=70 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>업태</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_BizType" id="up_linkhub_BizType" value="<?=$linkhub_BizType?>" size=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>업종</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_BizClass" id="up_linkhub_BizClass" value="<?=$linkhub_BizClass?>" size=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>담당자명</span></th>
					<TD class="td_con1"><input type=text name="up_linkhub_ContactName" id="up_linkhub_ContactName" value="<?=$linkhub_ContactName?>" size=20 class="input_selected"></TD>
				</TR>
				<TR>
					<th><span>담당자 이메일</span></th>
					<TD class="td_con1">
						<input type=text name="up_linkhub_ContactEmail1" id="up_linkhub_ContactEmail1" value="<?=$linkhub_ContactEmail1?>" size=20 class="input_selected">&nbsp;@
						<input type=text name="up_linkhub_ContactEmail2" id="up_linkhub_ContactEmail2" value="<?=$linkhub_ContactEmail2?>" size=20 class="input_selected" readonly="true">
						<select name="email_domains" id="email_domains" >
							<option value="">선택</option>
							<option value="text">직접입력</option>
							<option value="naver.com">naver.com</option>
							<option value="hanmail.net">hanmail.net</option>
							<option value="nate.com">nate.com</option>
							<option value="gmail.com">gmail.com</option>
							<option value="lycos.co.kr">lycos.co.kr</option>
							<option value="yahoo.co.kr">yahoo.co.kr</option>
							<option value="yahoo.com">yahoo.com</option>
							<option value="empal.com">empal.com</option>
							<option value="dreamwiz.com">dreamwiz.com</option>
							<option value="paran.com">paran.com</option>
							<option value="korea.com">korea.com</option>
							<option value="chol.com">chol.com</option>
							<option value="hanmir.com">hanmir.com</option>
							<option value="hanafos.com">hanafos.com</option>
							<option value="freechal.com">freechal.com</option>
							<option value="hotmail.com">hotmail.com</option>
							<option value="netian.com">netian.com</option>
						</select>
						<span class="input_warnning_info"></span>
					</TD>
				</TR>
				<TR>
					<th><span>담당자 전화번호</span></th>
					<TD class="td_con1">
					<input type=text numberOnly="true" name="up_linkhub_ContactTEL1" id="up_linkhub_ContactTEL1" value="<?=$linkhub_ContactTEL1?>" size=3 class="input_selected"> - 
					<input type=text numberOnly="true" name="up_linkhub_ContactTEL2" id="up_linkhub_ContactTEL2" value="<?=$linkhub_ContactTEL2?>" size=4 class="input_selected"> - 
					<input type=text numberOnly="true" name="up_linkhub_ContactTEL3" id="up_linkhub_ContactTEL3" value="<?=$linkhub_ContactTEL3?>" size=4 class="input_selected">
					</TD>
				</TR>
				<TR>
					<th><span>담당자 휴대폰 번호</span></th>
					<TD class="td_con1">
					<input type=text numberOnly="true" name="up_linkhub_ContactHP1" id="up_linkhub_ContactHP1" value="<?=$linkhub_ContactHP1?>" size=3 maxlength=3 class="input_selected" > - 
					<input type=text numberOnly="true" name="up_linkhub_ContactHP2" id="up_linkhub_ContactHP2" value="<?=$linkhub_ContactHP2?>" size=4 maxlength=4 class="input_selected" > - 
					<input type=text numberOnly="true" name="up_linkhub_ContactHP3" id="up_linkhub_ContactHP3" value="<?=$linkhub_ContactHP3?>" size=4 maxlength=4 class="input_selected" >
					</TD>
				</TR>
				<TR>
					<th><span>담당자 팩스 번호</span></th>
					<TD class="td_con1">
					<input type=text numberOnly="true" name="up_linkhub_ContactFAX1" id="up_linkhub_ContactFAX1" value="<?=$linkhub_ContactFAX1?>" size=3 maxlength=3 class="input_selected" > - 
					<input type=text numberOnly="true" name="up_linkhub_ContactFAX2" id="up_linkhub_ContactFAX2" value="<?=$linkhub_ContactFAX2?>" size=4 maxlength=4 class="input_selected" > - 
					<input type=text numberOnly="true" name="up_linkhub_ContactFAX3" id="up_linkhub_ContactFAX3" value="<?=$linkhub_ContactFAX3?>" size=4 maxlength=4 class="input_selected" >
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<script> 
				$(function(){
					$(".popbill_btn").click(function(){
						var _constantVal = $(this).attr("id");
						switch(_constantVal){
							case "popbill_login_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetPopbillURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=LOGIN",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_point_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetPopbillURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=CHRG",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_publiclicense_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetPopbillURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=CERT",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_tempdocu_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=TBOX",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_salesdocu_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=SBOX",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_buydocu_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=PBOX",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
							case "popbill_makesales_btn" : 
								$.ajax({
									type: "POST",
									url: "/linkhub/TaxinvoiceExample/GetURL_ajax.php",
									data: "CorpNum=<?=$linkhub_CorpNum?>&UserID=<?=$linkhub_ID?>&CHRG=WRITE",
									dataType:"JSON",
									beforeSend: function () {
									}
								}).done(function(data){
									openBlankPop(data.url);
								});
								break;
						}
					});
				});

				function openBlankPop(val){
					var openUrl = val;
					if (openUrl != ""){
						window.open(
						  openUrl,
						  '_blank' 
						);
					}
				}
			</script>
			<tr>
				<td align="center" id="btn_area">
				<?
					if ($code == "1"){
				?>
						<p>
						<span class="popbill_btn" id="popbill_login_btn" alt="팝빌 로그인">팝빌 로그인</span>
						<span class="popbill_btn" id="popbill_point_btn" alt="포인트충전">포인트충전</span>
						<span class="popbill_btn" id="popbill_publiclicense_btn" alt="공인인증서 등록">공인인증서 등록</span>
						</p>
						<p>
						<span class="popbill_btn" id="popbill_tempdocu_btn" alt="임시 문서함">임시 문서함</span>
						<span class="popbill_btn" id="popbill_salesdocu_btn" alt="매출 문서함">매출 문서함</span>
						<span class="popbill_btn" id="popbill_buydocu_btn" alt="매입 문서함">매입 문서함</span>
						<span class="popbill_btn" id="popbill_makesales_btn" alt="매출작성">매출작성</span>
						</p>
				<?	
					} else {
				?>
						<p>
						<a href="javascript:CheckForm();"><img src="images/botteon_save.gif" /></a>
						</p>
				<?
					}
				?>
				
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>세금계산서 환경설정</p></div>
					<dl>
						<dt><span>title</span></dt>
						<dd>
							
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
<?=$onload?>
<?php 
include("copyright.php");
