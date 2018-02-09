<?php
/*********************************************************************
// 파 일 명		: member_certi.php
// 설     명		: 회원가입 인증 또는 확인
// 상세설명	: 회원가입시 약관 및 간편회원 추가입력폼
// 작 성 자		: 2016.07.28 - 김재수
// 수 정 자		:
//
//
*********************************************************************/
?>
<?php
	session_start();

#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."conf/config.sns.php");

	$mem_type = $_POST[mem_type];
	if (!$mem_type) $mem_type = 0;
	$join_type = $_POST[join_type];

	if(strlen($_ShopInfo->getMemid())>0) {
		$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
		if ($mem_auth_type != 'sns') {
			header("Location:../index.php");
			exit;
		}
	}
	$_SESSION[ipin][name]	="";
	$_SESSION[ipin][dupinfo]	="";
	$_SESSION[ipin][gender]	="";
	$_SESSION[ipin][birthdate]	="";
	$_SESSION[ipin][mobileno]	="";
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(t,type) {

	
		
			$('#auth_type').val(type);
			if(type=='ipin'){
				//document.getElementById("ifrmHidden").src="./ipin/ipin_main.php";
				document.getElementById("ifrmHidden").src="./ipin_new/ipin_main.php";
			}else{
				document.getElementById("ifrmHidden").src="./checkplus/checkplus_main.php";
				//document.getElementById("ifrmHidden").src="./checkplus/checkplus_main_test.php"; // 테스트용
			}		
		


}

function ipin_chk(ipin, uname){
	var erp_member_yn	= $("form[name=form_agree]").find("input[name=erp_member_yn]").val();
	var erp_cust_name		= $("form[name=form_agree]").find("input[name=erp_cust_name]").val();
	if (erp_member_yn == 'Y' && erp_cust_name != uname) {
		alert("오프라인 매장 회원정보와 인증된 정보가 다릅니다. 같은 정보로 인증하시기 바랍니다.");
	} else {
		document.getElementById("ifrmHidden").src="./member_chkid.php";
	}
	return;
}


function certi_return(rt_yn, rt_name, rt_id, full_id){
	if(rt_yn=='1'){
		$("form[name=form_agree]").submit();
	}else{
		//alert(rt_name+" 고객님께서는 "+rt_id+"로 이미 가입하셨습니다.");
		alert(rt_name+" 고객님은 "+rt_id+"로 이미 가입한 통합 회원이십니다. 신원 통합몰로 로그인으로 이동합니다.");
		location.href="login.php";
		return;
	}
}

//-->
</SCRIPT>

<div id="contents">
	<div class="member-page">
		<form name="form_agree" action="member_agree.php" method=post>
		<input type="hidden" name="auth_type" id="auth_type" >
		<input type="hidden" name="mem_type" id="mem_type" value="0">
		<input type="hidden" name="join_type" id="join_type" value="1">
		<input type="hidden" name="staff_join" value="<?=$_REQUEST['staff_join']?>">
		<input type="hidden" name="cooper_join" value="<?=$_REQUEST['cooper_join']?>">

		<article class="memberJoin-wrap">
			<header class="join-title">
				<h2>회원가입</h2>
				<ul class="flow clear">
					<li class="active"><div><i></i><span>STEP 1</span>본인인증</div></li>
					<li><div><i></i><span>STEP 2</span>약관동의</div></li>
					<li><div><i></i><span>STEP 3</span>정보입력</div></li>
					<li><div><i></i><span>STEP 4</span>가입완료</div></li>
				</ul>
			</header>
			<section class="align-inner join-certification">
				<header class="sub-title">
					<h3>실명인증</h3>
					<p class="att">고객님의 개인정보 보호를 위해 본인인증을 해주세요. <br>휴대폰 인증 및 아이핀 인증이 가능합니다.</p>
				</header>
				<div class="frm-box mt-40 clear">
					<div class="inner">
						<img src="/sinwon/web/static/img/common/certification_phone.png" alt="휴대폰 인증">
						<div class="comment mt-25"><span>본인명의의 휴대폰 번호로 인증하여 회원가입을 진행합니다.<br>타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</span></div>
						<button class="btn-point h-large" type="button" onclick="CheckForm('1','mobile');"><span>휴대폰 인증</span></button>
					</div>
					<div class="inner">
						<img src="/sinwon/web/static/img/common/certification_ipin.png" alt="아이핀 인증">
						<div class="comment mt-25"><span>아이핀으로 인증하여 회원가입을 진행합니다.</span></div>
						<button class="btn-point h-large" type="button" onclick="CheckForm('1','ipin');"><span>아이핀 인증</span></button>
					</div>
				</div><!-- //.frm-box -->
			</section>
			
		</article>
		<input type="hidden" name="erp_member_yn" value="<?=$_POST['erp_member_yn']?>">
		<input type="hidden" name="erp_member_id" value="<?=$_POST['erp_member_id']?>">
		<input type="hidden" name="erp_cust_name" value="<?=$_POST['erp_cust_name']?>">
		<input type="hidden" name="erp_birthday" value="<?=$_POST['erp_birthday']?>">
		<input type="hidden" name="erp_birth_gb" value="<?=$_POST['erp_birth_gb']?>">
		<input type="hidden" name="erp_cell_phone_no1" value="<?=$_POST['erp_cell_phone_no1']?>">
		<input type="hidden" name="erp_cell_phone_no2" value="<?=$_POST['erp_cell_phone_no2']?>">
		<input type="hidden" name="erp_cell_phone_no3" value="<?=$_POST['erp_cell_phone_no3']?>">
		<input type="hidden" name="erp_sex_gb" value="<?=$_POST['erp_sex_gb']?>">
		<input type="hidden" name="erp_job_cd" value="<?=$_POST['erp_job_cd']?>">
		<input type="hidden" name="erp_home_zip_old_new" value="<?=$_POST['erp_home_zip_old_new']?>">
		<input type="hidden" name="erp_home_zip_no" value="<?=$_POST['erp_home_zip_no']?>">
		<input type="hidden" name="erp_home_addr1" value="<?=$_POST['erp_home_addr1']?>">
		<input type="hidden" name="erp_home_addr2" value="<?=$_POST['erp_home_addr2']?>">
		<input type="hidden" name="erp_sms_yn" value="<?=$_POST['erp_sms_yn']?>">
		<input type="hidden" name="erp_kakao_yn" value="<?=$_POST['erp_kakao_yn']?>">
		<input type="hidden" name="erp_email1" value="<?=$_POST['erp_email1']?>">
		<input type="hidden" name="erp_email2" value="<?=$_POST['erp_email2']?>">
		<input type="hidden" name="erp_home_tel_no1" value="<?=$_POST['erp_home_tel_no1']?>">
		<input type="hidden" name="erp_home_tel_no2" value="<?=$_POST['erp_home_tel_no2']?>">
		<input type="hidden" name="erp_home_tel_no3" value="<?=$_POST['erp_home_tel_no3']?>">
		</form>
	</div>
</div><!-- //#contents -->


<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
