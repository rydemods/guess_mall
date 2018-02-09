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
	$auth_type = $_POST[auth_type];

	if(strlen($_ShopInfo->getMemid())>0) {
		$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
		if ($mem_auth_type != 'sns') {
			header("Location:../index.php");
			exit;
		}
	}
	if ($_POST['erp_member_yn'] != 'Y' && $auth_type =='mobile') {
		$name		= trim($_SESSION[ipin][name]);
		$mobile		= trim($_SESSION[ipin][mobileno]);
		$send_name	= iconv('utf-8','euc-kr',$name);
		if (!$send_name) $send_name	= $name;
		$meberinfo	= getErpMeberinfo($send_name, $mobile);
		$code		= $meberinfo['p_err_code'];
		$p_data		= $meberinfo['p_data'];
		if ($code == '0') {
			if ($_POST['staff_join'] != 'Y' && $_POST['cooper_join'] != 'Y') {
				if ($p_data['eshop_id']=='') {
					echo("<script>alert('회원님은 신원의 오프라인 매장 회원이십니다. 신원 통합몰 회원 전환으로 이동합니다.');location.href='member_switch.php';</script>");
				} else {
					echo("<script>alert(회원님은 통합 회원이십니다. 신원 통합몰 로그인으로 이동합니다.');location.href='login.php';</script>");
				}
				exit;
			} else {
				$_POST['erp_member_yn'] = "Y";
				$_POST['erp_member_id'] = $p_data['member_id'];
				$_POST['erp_cust_name'] = $p_data['cust_name'];
				$_POST['erp_birthday'] = $p_data['birthday'];
				$_POST['erp_birth_gb'] = $p_data['birth_gb'];
				$_POST['erp_cell_phone_no1'] = $p_data['cell_phone_no1'];
				$_POST['erp_cell_phone_no2'] = $p_data['cell_phone_no2'];
				$_POST['erp_cell_phone_no3'] = $p_data['cell_phone_no3'];
				$_POST['erp_sex_gb'] = $p_data['sex_gb'];
				$_POST['erp_job_cd'] = $p_data['job_cd'];
				$_POST['erp_home_zip_old_new'] = $p_data['home_zip_old_new'];
				$_POST['erp_home_zip_no'] = $p_data['home_zip_no'];
				$_POST['erp_home_addr1'] = $p_data['home_addr1'];
				$_POST['erp_home_addr2'] = $p_data['home_addr2'];
				$_POST['erp_sms_yn'] = $p_data['sms_yn'];
				$_POST['erp_kakao_yn'] = $p_data['kakao_yn'];
				$_POST['erp_email1'] = $p_data['email1'];
				$_POST['erp_email2'] = $p_data['email2'];
				$_POST['erp_home_tel_no1'] = $p_data['home_tel_no1'];
				$_POST['erp_home_tel_no2'] = $p_data['home_tel_no2'];
				$_POST['erp_home_tel_no3'] = $p_data['home_tel_no3'];
			}
		}
	}
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
$(function(){
	$("#checkall").click(function(){
		//만약 전체 선택 체크박스가 체크된상태일경우
		if($("#checkall").prop("checked")) {
			//해당화면에 전체 checkbox들을 체크해준다
			$(".chk_agree").prop("checked",true);
		// 전체선택 체크박스가 해제된 경우
		} else {
			//해당화면에 모든 checkbox들의 체크를해제시킨다.
			$(".chk_agree").prop("checked",false);
		}
	});
});



function CheckForm(mt, jt, sns_url, sns_type) {
	
	if($("input[name=agree_check01]").prop("checked")) {
		if($("input[name=agree_check02]").prop("checked")) {
			ResetForm();
		} else {
			alert("[개인정보취급방침]에 동의하셔야 회원가입이 가능합니다.");
			if(document.form_agree.agree_check02) {
				document.getElementById("agree_check02").focus();
			}
		}
	} else {
		alert("[서비스 이용약관]에 동의하셔야 회원가입이 가능합니다.");
		if(document.form_agree.agree_check01) {
			document.getElementById("agree_check01").focus();
		}
	}
	
}


function ResetForm() {	
	document.form_agree.submit();	
}

function submitCancel(){
	if(confirm('취소하시겠습니까?')){
		var add_par	= "";
		if ($("input[name=staff_join]").val() == 'Y') {
			add_par	= "?staff_join=Y";
		} else  if ($("input[name=cooper_join]").val() == 'Y') {
			add_par	= "?cooper_join=Y";
		}
		location.href="member_certi.php"+add_par;
	}
}


//-->
</SCRIPT>

<?php
$sql="SELECT agreement,privercy,etc_agreement1,etc_agreement2,etc_agreement3 FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$agreement=$row->agreement;
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];

$etc_agreement1=$row->etc_agreement1;
$etc_agreement2=$row->etc_agreement2;
$etc_agreement3=$row->etc_agreement3;

pmysql_free_result($result);

if(ord($agreement)==0) {
	$buffer = file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement=$buffer;
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);
$agreement = preg_replace('/[\\\\\\\]/',"",$agreement);

if(ord($privercy)==0) {
	$buffer = file_get_contents($Dir.AdminDir."privercy2.txt");
	$privercy=$buffer;
}

$pattern=array("[SHOP]","[COMPANY]","[NAME]","[EMAIL]","[TEL]");
$replace=array($_data->shopname, $_data->companyname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
$privercy = str_replace($pattern,$replace,$privercy);

$etc_agreement1 = str_replace($pattern,$replace,$etc_agreement1);
$etc_agreement1 = preg_replace('/[\\\\\\\]/',"",$etc_agreement1);

$etc_agreement2 = str_replace($pattern,$replace,$etc_agreement2);
$etc_agreement2 = preg_replace('/[\\\\\\\]/',"",$etc_agreement2);

$etc_agreement3 = str_replace($pattern,$replace,$etc_agreement3);
$etc_agreement3 = preg_replace('/[\\\\\\\]/',"",$etc_agreement3);
?>

<div id="contents">
	<div class="member-page">
	<form name="form_agree" action="member_join.php" method=post>
	<input type="hidden" name="auth_type" id="auth_type" value="<?=$auth_type?>">
	<input type="hidden" name="mem_type" id="mem_type" value="<?=$mem_type?>">
	<input type="hidden" name="join_type" id="join_type" value="<?=$join_type?>">
	<input type="hidden" name="staff_join" value="<?=$_POST['staff_join']?>">
	<input type="hidden" name="cooper_join" value="<?=$_POST['cooper_join']?>">
	
		<article class="memberJoin-wrap">
			<header class="join-title">
				<h2>회원가입</h2>
				<ul class="flow clear">
					<li><div><i></i><span>STEP 1</span>본인인증</div></li>
					<li class="active"><div><i></i><span>STEP 2</span>약관동의</div></li>
					<li><div><i></i><span>STEP 3</span>정보입력</div></li>
					<li><div><i></i><span>STEP 4</span>가입완료</div></li>
				</ul>
			</header>
			<section class="inner-align join-agreement">
				<header class="sub-title">
					<h3>신원몰 서비스 약관동의</h3>
				</header>
				<div class="wrap">
					<h4 class="title">서비스 이용약관(필수)</h4>
					<div class="terms-box"><?=$agreement?></div>
					<div class="checkbox mt-15">
						<input id="agree_check01" name="agree_check01" type="checkbox" class="chk_agree checkbox-def" value="1"><label for="agree_check01">약관에 동의합니다.</label>
					</div>
				</div>
				<div class="wrap">
					<h4 class="title">개인정보취급방침(필수)</h4>
					<div class="terms-box"><?=$privercy?></div>
					<div class="checkbox mt-15">
						<input id="agree_check02" name="agree_check02" type="checkbox" class="chk_agree checkbox-def" value="1"><label for="agree_check02">개인정보취급방침에 동의합니다.</label>
					</div>
				</div>
				<div class="all-agree mt-15">
					<div class="checkbox">
						<input id="checkall" type="checkbox" class="chk_allAgree checkbox-def">
						<label for="checkall">사이트 이용을 위한 모든 약관에 동의합니다.</label>
					</div>
				</div>
				<div class="btnPlace mt-35">
					<a href="javascript:;" onclick="submitCancel();" class="btn-line h-large">취소</a>
					<a href="javascript:CheckForm('0','1','','');" class="btn-point h-large">다음</a>
				</div>
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
		<input type="hidden" name="erp_home_addr2	" value="<?=$_POST['erp_home_addr2']?>">
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
