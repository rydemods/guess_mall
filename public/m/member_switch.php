<?
$subTitle = "신원 통합회원 전환";
include_once('outline/header_m.php');
?>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>신원 통합회원 전환</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="joinpage mem_switch sub_bdtop">
		<p class="info_msg">기존 신원 브랜드의 오프라인 매장 회원님은<br> 손쉽게 신원 통합회원으로 전환이 가능합니다.</p>

		<div class="agree_form mt-20">
			<h3 class="tit">개인정보 제공 동의 약관</h3>
			<textarea>제공하는 개인정보 항목
이름, 생년월일, 성별, 이메일, 전화번호, 휴대폰번호, 주소, 연계정보(CI), 중복가입 방지 정보(DI), 아이디, 내외국인 여부, 개인정보 제3자 제공동의서
상품안내 전화 수신 동의서
이메일 수신동의 여부
SMS 수신동의여부
보유 및 이용기간
신원몰 (㈜신원) 서비스 이용약관 철회 또는 신원몰 (㈜신원) 회원탈퇴시까지</textarea>
			<label for="switch_agree"><input type="checkbox" class="check_def" id="switch_agree"> <span>약관에 동의합니다.</span></label>
		</div>

		<form name='swithForm'>
		<div class="login_area">
			<input type="text" class="w100-per" name='u_name' id='u_name' title="이름을 입력하세요." placeholder="이름 입력" maxlength='10'>
			<input type="text" class="w100-per" name='u_mobile' id='u_mobile' title="휴대폰 번호를 입력하세요" placeholder="휴대폰 번호 입력('-'제외)" maxlength='11'>
			<a href="javascript:;" class="btn-point w100-per h-input" onClick="javascript:CheckFormSubmit();">신원 오프라인 매장 회원정보 확인</a>
		</div>
		</form>

		<hr class="line_basic mt-25">

		<div class="agree_form mt-35">
			<h3 class="tit">해당 브랜드</h3>
			<ul class="integrated_brand clear mt-5">
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_besti.png" alt="BESTI BELLI"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_viki.png" alt="VIKI"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_si.png" alt="SI"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_isabey.png" alt="ISABEY"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_sieg.png" alt="SIEG"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_siegf.png" alt="SIEG FAHRENHEIT"></span></li>
				<li><span><img src="/sinwon/m/static/img/common/logo_standard_vanhart.png" alt="VanHart di Albazar"></span></li>
			</ul>
		</div>
		
		<div class="agree_form mt-25">
			<h3 class="tit">신원 통합회원 전환시 받을 수 있는 혜택</h3>
			<ul class="switch_benefit clear mt-5">
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_epoint.png" alt="포인트 아이콘"></div>
						<div class="txt">
							<p>20,000 E포인트</p>
						</div>
					</div>
				</li>
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_coupon.png" alt="쿠폰 아이콘"></div>
						<div class="txt">
							<p>10% 할인 쿠폰</p>
						</div>
					</div>
				</li>
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_epoint.png" alt="포인트 아이콘"></div>
						<div class="txt">
							<p>상품 구매시<br> 포인트 적립</p>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</section><!-- //.joinpage -->
	<form name="erp_form" id="erp_form" action="member_certi.php" method="post">
		<input type="hidden" name="erp_member_yn" >
		<input type="hidden" name="erp_cust_name" >
		<input type="hidden" name="erp_cell_phone_no" >
	</form>

</main>
<!-- //내용 -->


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
		$("form[name=erp_form]").find("input[name=erp_cust_name]").val("");
		$("form[name=erp_form]").find("input[name=erp_cell_phone_no]").val("");

		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&mobile=" + u_mobile_val + "&mode=erp_mem_chk",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {

					$("form[name=erp_form]").find("input[name=erp_cust_name]").val(data.msg.cust_name);
					$("form[name=erp_form]").find("input[name=erp_cell_phone_no]").val(data.msg.cell_phone_no1+'-'+data.msg.cell_phone_no2+'-'+data.msg.cell_phone_no3);

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

<?
include_once('outline/footer_m.php')
?>

