<?php
/*********************************************************************
// 파 일 명		: login_TEM_001.php
// 설     명		: 로그인 템플릿
// 상세설명	: 회원 로그인 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2015.01.07 - 김재수
//
//
*********************************************************************/
?>
<!-- 메인 컨텐츠 -->
<?php
	//$sel_hide[$mode] = 'style="display:none"';	//선택하지 않은 레이어는 숨기기 위해
	//$sel_on[$mode] =' class=on';	// 선택한 탭을 on 시키기 위해

	if(!$chUrl){
		$chUrl=trim(urldecode($_SERVER["HTTP_REFERER"]));
	}

	$page_code = "login";
?>


<!--로그인시 토큰 값 셋팅 함수 시작 ( 안드로이드에서 호출 )-->
<script type="text/javascript">
	function settingPushSerial(regid,os){
		if (os === undefined) os = "Android";
		$("input[name='push_os']").val(os);
		$("input[name='push_token']").val(regid);
	}
</script>
<!--로그인시 토큰 값 셋팅 함수 종료 ( 안드로이드에서 호출 )-->
<div id="contents">
	<div class="member-page">

		<article class="memberLogin-wrap">
			<header class="login-title"><h2>로그인</h2></header>
			<div class="frm-box mt-50 with-inner">
				<div class="inner pl-80 pr-80">
					<section>
						<header><h3 class="title">로그인</h3></header>
						<form class="login-reg mt-20" action="[FORM_ACTION]" method="post" name="form1">
							
							<fieldset>
								<legend>회원 로그인폼</legend>
								<input type="text" class="w100-per" title="아이디 입력자리" name="id" id="user-id" maxlength="100" placeholder="아이디 입력"  >
								<input type="password" class="w100-per mt-10" title="비밀번호 입력자리" name="passwd" id="user-pw" placeholder="비밀번호 입력 (영문+숫자 8~20자리)">
								<div class="mt-10">
									<div class="checkbox">
										<input type="checkbox" id="save_id">
										<label for="save_id">아이디 저장</label>
									</div>
								</div>
								<div class="mt-10"><button class="btn-point w100-per h-large" onclick="JavaScript:CheckForm();return false;"><span>로그인</span></button></div>
							</fieldset>
						</form>
					</section>
					<?if(basename($chUrl)=="order.php" || basename($chUrl)=="basket.php") {?><div class="mt-10"><button class="btn-line w100-per h-large" onclick="javascript:location.href='[NOLOGIN]'"><span>비회원 구매하기</span></button></div><?}?>
					<section class="join-benefit mt-15">
						<dl class="clear">
							<dd class='w100-per'>- 로그인시 SW ehop 기존회원의 비밀번호는 휴대폰번호(-제외)입니다.</dd>
						</dl>
					</section>
					<section class="mt-15">
						<header><h3 class="title">간편 회원가입</h3></header>
						<ul class="easy-login mt-15 clear">								
						<?if($snsNvConfig["use"] == "nv"){?>
							<li>
								<a href="javascript:;" onclick="javascript:sns_open('/plugin/sns/sns_access.php?sns=<?=$snsNvConfig["use"]?>&sns_login=1&ac=front', '<?=$snsNvConfig["use"]?>','1');">
									<i class="icon-snsLogin-naver"></i>네이버</a></li>
								</a>
							</li>
						<?}?>


						<?if($snsKtConfig["use"] == "kt"){?>
							<li>
								<a href="javascript:;" onclick="javascript:sns_open('/plugin/sns/sns_access.php?sns=<?=$snsKtConfig["use"]?>&sns_login=1&ac=front', '<?=$snsKtConfig["use"]?>','1');">
									<i class="icon-snsLogin-katalk"></i>카카오톡</a></li>
								</a>
							</li>
						<?}?>


						<?if($snsFbConfig["use"] == "fb"){?>
							<!-- <li>
								<a href="javascript:;" onclick="javascript:sns_open('/plugin/sns/sns_access.php?sns=<?=$snsFbConfig["use"]?>&sns_login=1&ac=front', '<?=$snsFbConfig["use"]?>','1');">
									<i class="icon-snsLogin-facebook"></i>페이스북</a></li>
								</a>
							</li> -->
						<?}?>
						</ul>
						<div class="login-link mt-20">
							<a href="findid.php">아이디/비밀번호 찾기</a>
							<a href="login_guest.php">비회원 주문조회</a>
						</div>
					</section>
				</div>
				<div class="inner pl-80 pr-80">
					<section class="join-benefit total">
						<h4 class="v-hidden">통합몰 회원전환 안내</h4>
						<dl>
							<dt class="title">통합몰 회원전환</dt>
							<dd>신원 오프라인 매장의 회원이세요?</dd>
							<dd>신원통합몰 회원으로 전환시 <strong class="point-color">5,000 E포인트</strong>를 즉시 증정합니다.</dd>
						</dl>
						<a href="member_switch.php" class="btn-basic w100-per h-large">신원 통합회원 전환하기</a>
					</section>
					<section class="join-benefit mt-25">
						<h4 class="v-hidden">신원몰 회원시 헤택안내</h4>
						<dl class="clear">
							<dt class="title">신원몰 회원이 누릴 수 있는 혜택!</dt>
							<dd>- 회원 대상 상시 이벤트 진행</dd>
							<dd>- 등급별 멤버십 운영</dd>
							<dd>- 상품 구매 시 포인트 적립 및 사용 가능</dd>
							<dd>- 회원 가입시 5,000 E포인트 증정</dd>
							<dd>- 다양한 쿠폰 증정</dd>
							<dd>- 회원 가입시 10%할인 쿠폰 증정</dd>
						</dl>
						<a href="member_certi.php" class="btn-point w100-per h-large mt-15">회원가입</a>
					</section>
				</div>
			</div>
			<!-- SNS 간편 가입 테스트 -->
	<form action="/front/member_certi.php" method="POST" name="frmSns">
		<input type="hidden" name="chUrl" value="<?=$chUrlLoc?>">
		<input type='hidden' name='sns_login' value="">
		<input type='hidden' name='sns_id' value="">
		<input type='hidden' name='sns_email' value="">
		<input type='hidden' name='sns_name' value="">
		<input type='hidden' name='sns_type' value="">
		<input type='hidden' name='sns_token' value="">
	</form>
		</article>

	</div>
</div><!-- //#contents -->

