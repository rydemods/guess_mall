<?php
	session_start();

#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."conf/config.sns.php");
		
	$mem_type = $_POST[mem_type];
	if (!$mem_type) $mem_type = 0;
	$join_type = $_POST[join_type];

	if(strlen($_MShopInfo->getMemid())>0) {
		$mem_auth_type	= getAuthType($_MShopInfo->getMemid());
		if ($mem_auth_type != 'sns') {
			header("Location:".$Dir.MDir."index.php");
			exit;
		}		
	}
	$_SESSION[ipin][name]	="";
	$_SESSION[ipin][dupinfo]	="";
	$_SESSION[ipin][gender]	="";
	$_SESSION[ipin][birthdate]	="";
	$_SESSION[ipin][mobileno]	="";

	include_once('outline/header_m.php');
	$subTitle = "회원가입";

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(cert_type) {
	$('#auth_type').val(cert_type);
	$("#au_auth_type").val(cert_type);
	if(cert_type == "mobile"){
		document.form_agree.action = "./checkplus/checkplus_main.php";
	}else{
		document.form_agree.action = "./ipin_m_new/ipin_main.php";
//		document.form_agree.action = "./ipin_m/IPINMain.php";
	}
			
	//document.auth_form.action = "./checkplus/checkplus_main_test.php"; // 테스트용
	document.form_agree.submit();
}
//-->
</SCRIPT>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>회원가입</span>
		</h2>
		<div class="page_step join_step">
			<ul class="ea4 clear">
				<li class="on"><span class="icon_join_step01"></span>본인인증</li>
				<li><span class="icon_join_step02"></span>약관동의</li>
				<li><span class="icon_join_step03"></span>정보입력</li>
				<li><span class="icon_join_step04"></span>가입완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->

	<section class="joinpage">
	<form name="form_agree" action="member_join.php" method=post>
	<input type="hidden" name="auth_type" id="auth_type" >
	<input type="hidden" name="mem_type" id="mem_type" value="0">
	<input type="hidden" name="join_type" id="join_type" value="1">
	<input type="hidden" name="staff_join" value="<?=$_REQUEST['staff_join']?>">
	<input type="hidden" name="cooper_join" value="<?=$_REQUEST['cooper_join']?>">
		<div class="certi_notice">
			고객님의 개인정보 보호를 위해 본인인증을 해주세요.<br>휴대폰 인증 및 아이핀 인증이 가능합니다.
		</div>
		
		<div class="mt-25 pb-5">
			<div class="certification">
				<div class="icon certi_phone"><img src="/sinwon/m/static/img/icon/icon_certi_phone.png" alt="휴대폰 인증"></div>
				<div class="info">
					<p>본인명의의 휴대폰 번호로 인증하여 회원가입을 진행합니다. 타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</p>
					<a href="javascript:;" class="btn-point" onClick="javascript:CheckForm('mobile');">휴대폰 인증</a>
				</div>
			</div>
			<div class="certification">
				<div class="icon certi_ipin"><img src="/sinwon/m/static/img/icon/icon_certi_ipin.png" alt="아이핀 인증"></div>
				<div class="info">
					<p>아이핀으로 인증하여 회원가입을 진행합니다.</p>
					<a href="javascript:;" class="btn-point" onClick="javascript:CheckForm('ipin');">아이핀 인증</a>
				</div>
			</div>
		</div>
	<input type="hidden" name="erp_member_data" value="<?=$_POST['erp_member_yn']=='Y'?$_POST['erp_member_yn']."|".$_POST['erp_cust_name']."|".$_POST['erp_cell_phone_no']:''?>">
	</form>
	</section><!-- //.joinpage -->

</main>
<!-- //내용 -->
	
<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>

<?include_once('outline/footer_m.php'); ?>