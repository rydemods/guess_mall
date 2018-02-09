<?php
/*********************************************************************
// 파 일 명		: member_switch.php
// 설     명		: 신원 통합몰 회원 전환
// 상세설명	: 회신원 통합몰 오프라인 회원 유무 체크
// 작 성 자		: 2017.03.22 - 김재수
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
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--

function CheckFormSubmit() {
	
	if($("#switch_agree").prop("checked")) {

		var u_name_val	= $("input[name=u_name]").val();
		var u_mobile_val	= $("input[name=u_mobile]").val();
		if (u_name_val == ''){
			alert($("input[name=u_name]").attr('title'));
			$("input[name=u_name]").focus();
			return;
		}

		if (u_mobile_val == '' || u_mobile_val.length < 10){
			alert($("input[name=u_mobile]").attr('title'));
			$("input[name=u_mobile]").focus();
			return;
		}

		$("form[name=erp_form]").find("input[name=erp_member_yn]").val("");
		$("form[name=erp_form]").find("input[name=erp_member_id]").val("");
		$("form[name=erp_form]").find("input[name=erp_cust_name]").val("");
		$("form[name=erp_form]").find("input[name=erp_birthday]").val("");
		$("form[name=erp_form]").find("input[name=erp_birth_gb]").val("");
		$("form[name=erp_form]").find("input[name=erp_cell_phone_no1]").val("");
		$("form[name=erp_form]").find("input[name=erp_cell_phone_no2]").val("");
		$("form[name=erp_form]").find("input[name=erp_cell_phone_no3]").val("");
		$("form[name=erp_form]").find("input[name=erp_sex_gb]").val("");
		$("form[name=erp_form]").find("input[name=erp_job_cd]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_zip_old_new]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_zip_no]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_addr1]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_addr2]").val("");
		$("form[name=erp_form]").find("input[name=erp_sms_yn]").val("");
		$("form[name=erp_form]").find("input[name=erp_kakao_yn]").val("");
		$("form[name=erp_form]").find("input[name=erp_email1]").val("");
		$("form[name=erp_form]").find("input[name=erp_email2]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_tel_no1]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_tel_no2]").val("");
		$("form[name=erp_form]").find("input[name=erp_home_tel_no3]").val("");
		console.log(u_name_val);
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&mobile=" + u_mobile_val + "&mode=erp_mem_chk",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {

					$("form[name=erp_form]").find("input[name=erp_member_id]").val(data.msg.member_id);
					$("form[name=erp_form]").find("input[name=erp_cust_name]").val(data.msg.cust_name);
					$("form[name=erp_form]").find("input[name=erp_birthday]").val(data.msg.birthday);
					$("form[name=erp_form]").find("input[name=erp_birth_gb]").val(data.msg.birth_gb);
					$("form[name=erp_form]").find("input[name=erp_cell_phone_no1]").val(data.msg.cell_phone_no1);
					$("form[name=erp_form]").find("input[name=erp_cell_phone_no2]").val(data.msg.cell_phone_no2);
					$("form[name=erp_form]").find("input[name=erp_cell_phone_no3]").val(data.msg.cell_phone_no3);
					$("form[name=erp_form]").find("input[name=erp_sex_gb]").val(data.msg.sex_gb);
					$("form[name=erp_form]").find("input[name=erp_job_cd]").val(data.msg.job_cd);
					$("form[name=erp_form]").find("input[name=erp_home_zip_old_new]").val(data.msg.home_zip_old_new);
					$("form[name=erp_form]").find("input[name=erp_home_zip_no]").val(data.msg.home_zip_no);
					$("form[name=erp_form]").find("input[name=erp_home_addr1]").val(data.msg.home_addr1);
					$("form[name=erp_form]").find("input[name=erp_home_addr2]").val(data.msg.home_addr2);
					$("form[name=erp_form]").find("input[name=erp_sms_yn]").val(data.msg.sms_yn);
					$("form[name=erp_form]").find("input[name=erp_kakao_yn]").val(data.msg.kakao_yn);
					$("form[name=erp_form]").find("input[name=erp_email1]").val(data.msg.email1);
					$("form[name=erp_form]").find("input[name=erp_email2]").val(data.msg.email2);
					$("form[name=erp_form]").find("input[name=erp_home_tel_no1]").val(data.msg.home_tel_no1);
					$("form[name=erp_form]").find("input[name=erp_home_tel_no2]").val(data.msg.home_tel_no2);
					$("form[name=erp_form]").find("input[name=erp_home_tel_no3]").val(data.msg.home_tel_no3);

					//alert("eshop_id : "+data.msg.eshop_id);
					//return;
					if (data.msg.eshop_id =='') {
						if (confirm("회원님은 신원의 오프라인 매장 회원이십니다. 신원 통합몰회원으로 가입하시겠습니까?")) {
							$("form[name=erp_form]").find("input[name=erp_member_yn]").val("Y");
							$("form[name=erp_form]").submit();
							return;
						} else {
							location.href="login.php";
							return;
						}
					} else {					
						alert("회원님은 통합 회원이십니다. 신원 통합몰 로그인으로 이동합니다.");
						location.href="login.php";
						return;
					}
				} else if (data.code == 99) {
					alert("회원님은 통합 회원이십니다. 신원 통합몰 로그인으로 이동합니다.");
					location.href="login.php";
					return;
				} else {				
					if (confirm("회원님은 신원의 오프라인 회원이 아닙니다. 간단한 정보 입력후 통합회원으로 가입하시겠습니까")) {
						$("form[name=erp_form]").find("input[name=erp_member_yn]").val("N");
						$("form[name=erp_form]").submit();
						return;
					} else {
						location.href="login.php";
						return;
					}
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				$("input[name=u_mobile]").focus();
				return;
			}
		});
	} else {
		alert("회원님의 고객정보를 신원 통합몰 (㈜신원)에 제공함을 동의하셔야 회원정보 확인이 가능합니다.");
		$("#switch_agree").focus();
	}

}

//-->
</SCRIPT>

<div id="contents">
	<div class="member-page">

		<article class="memberLogin-wrap">
			<header class="login-title"><h2>신원 통합회원 전환</h2></header>
			<div class="frm-box mt-50 ">
				
				<div class="member-switch">
					<h3>기존 신원 브랜드의 오프라인 매장 회원님은 손쉽게 신원 통합회원으로 전환이 가능합니다.</h3>
					<div class="clear mt-30">
						<div class="agree">
							<div class="checkbox">
								<input type="checkbox" id="switch_agree">
								<label for="switch_agree">회원님의 고객정보를 신원몰 (㈜신원)에 제공함을 동의 합니다.</label>
							</div>
							<div class="agree-box">
								<dl>
									<dt>제공하는 개인정보 항목</dt>
									<dd>이름, 생년월일, 성별, 이메일, 전화번호, 휴대폰번호, 주소, 연계정보(CI), 중복가입 방지 정보(DI), 아이디, 내외국인 여부, 개인정보 제3자 제공동의서</dd>
									<dd>상품안내 전화 수신 동의서</dd>
									<dd>이메일 수신동의 여부</dd>
									<dd>SMS 수신동의여부</dd>
								</dl>
								<hr>
								<h4>보유 및 이용기간</h4>
								<p>신원몰 (㈜신원) 서비스 이용약관 철회 또는 신원몰 (㈜신원) 회원탈퇴시까지</p>
							</div>
						</div>
						<div class="input-box">
							<form name='swithForm'>
								<fieldset>
									<legend>정보입력</legend>
									<p class="title">정보입력</p>
									<input type="text" name='u_name' id='u_name' title="이름을 입력하세요." placeholder="이름 입력" class="w100-per" maxlength='10'>
									<input type="text" name='u_mobile' id='u_mobile' title="휴대폰 번호를 입력하세요" placeholder="휴대폰 번호 입력('-'제외)" class="mt-10 w100-per" maxlength='11'>
									<button class="btn-point w100-per h-large mt-20" type="button" onClick="javascript:CheckFormSubmit();"><span>신원 오프라인 매장 회원정보 확인</span></button>
								</fieldset>
							</form>
						</div>
					</div>
					<hr class="mt-40">
					<h4 class="mt-40 fz-15 fw-bold txt-toneA">해당 브랜드</h4>
					<ul class="brand clear mt-20">
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_bb.png" alt="BESTI BELLI"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_viki2.png" alt="VIKI"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_si.png" alt="SI"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_isabey.png" alt="ISABEY"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_sieg.png" alt="SIEG"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_siegf.png" alt="SIEG FAHRENHEIT"></span></li>
						<li><span><img src="../sinwon/web/static/img/common/brand_logo_vda.png" alt="VanHart di Albazar"></span></li>
					</ul>
					<h4 class="mt-40 fz-15 fw-bold txt-toneA">신원 통합회원 전환시 받을 수 있는 혜택</h4>
					<ul class="benefit mt-20 clear">
						<li><i class="icon-point"></i>20,000 E포인트</li>
						<li class="pt-5"><i class="icon-coupon"></i>10% 할인 쿠폰</li>
						<li><i class="icon-point"></i>상품 구매시 포인트 적립</li>
					</ul>
				</div><!-- //.member-switch -->

			</div>
		</article>
	</div>
</div><!-- //#contents -->
<form name="erp_form" id="erp_form" action="member_certi.php" method="post">
	<input type="hidden" name="erp_member_yn" >
	<input type="hidden" name="erp_member_id" >
	<input type="hidden" name="erp_cust_name" >
	<input type="hidden" name="erp_birthday" >
	<input type="hidden" name="erp_birth_gb" >
	<input type="hidden" name="erp_cell_phone_no1" >
	<input type="hidden" name="erp_cell_phone_no2" >
	<input type="hidden" name="erp_cell_phone_no3" >
	<input type="hidden" name="erp_sex_gb" >
	<input type="hidden" name="erp_job_cd" >
	<input type="hidden" name="erp_home_zip_old_new" >
	<input type="hidden" name="erp_home_zip_no" >
	<input type="hidden" name="erp_home_addr1" >
	<input type="hidden" name="erp_home_addr2" >
	<input type="hidden" name="erp_sms_yn" >
	<input type="hidden" name="erp_kakao_yn" >
	<input type="hidden" name="erp_email1" >
	<input type="hidden" name="erp_email2" >
	<input type="hidden" name="erp_home_tel_no1" >
	<input type="hidden" name="erp_home_tel_no2" >
	<input type="hidden" name="erp_home_tel_no3" >
</form>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
