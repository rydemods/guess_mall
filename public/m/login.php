<?
$subTitle = "로그인";
include_once('outline/header_m.php');
include_once($Dir."conf/config.sns.php");

# SNS 관련 세션값 초기화
$_ShopInfo->setCheckSns("");
$_ShopInfo->setCheckSnsLogin("");
$_ShopInfo->Save();


$chUrl=trim(urldecode($_REQUEST["chUrl"]));
//$chUrl=trim(urldecode($_SERVER["HTTP_REFERER"]));
// if ($chUrl == '') $chUrl = "/m/";
if(!$chUrl){
	$chUrl=trim(urldecode($_SERVER["HTTP_REFERER"]));
} 

// 2016-06-28 쿠폰 이슈에서 넘어왔을때 처음 요청했던 페이지로 되돌아가기 위해..
if($chUrl && strstr($chUrl, 'couponissue.php')) {
    //exdebug($_SERVER);
    if($_GET['ret_url']) $chUrl = $chUrl."&ret_url=".$_SERVER['HTTP_REFERER'];
    //exdebug($chUrl);
}

if($chUrl && strstr($chUrl, 'order.php')){
	$chUrlArray = explode("?", $chUrl);
	$chUrl = $chUrlArray[0];
	$chUrlItem = $chUrlArray[1];
}

if($chUrl && strstr($chUrl, 'basket.php')){
	$chUrlArray = explode("?", $chUrl);
	$chUrl = $chUrlArray[0];
	$chUrlItem = $chUrlArray[1];
}

if(strlen($_MShopInfo->getMemid())>0) {
	if (strlen($chUrl)>0) {$onload=$chUrl;}
	if(!$chUrl){$onload="main.php"; }
	echo "<script>location.replace('".$onload."');</script>";
	exit;
}


if($chUrlItem){
	$dirLocation = "order.php?".$chUrlItem;
}else{
	$dirLocation = "order.php";
}
?>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>로그인</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="loginpage sub_bdtop">
		<form class="login-form" name="login_form" id="login_form" action="login.process.php" method="get">
		<input type='hidden' name='action_mode' value="LOGIN">
		<input type='hidden' name='returnUrl' value="<?=$chUrl?>">
		<input type='hidden' name='push_os'>
		<input type='hidden' name='push_token'>
		<div class="login_area">
			<input type="text" class="w100-per" title="아이디 입력자리" placeholder="아이디 입력" id="m_id" name="id"  maxlength="100" value="<?=($_COOKIE["save_id"]=="Y"?$smart_id:"")?>">
			<input type="password" class="w100-per" title="비밀번호 입력자리" placeholder="비밀번호 입력 (영문+숫자 8~20자리)" name="passwd" id="password" maxlength="50" title="비밀번호">
			<label for="save_id"><input type="checkbox" class="check_def" id="save_id" name="save_id" value="Y" <?=($_COOKIE["save_id"]=="Y"?"checked='checked'":"")?>> <span>아이디 저장</span></label>
			<a href="javascript:;" class="btn-point w100-per h-input" onClick="javascript:goLogin();">로그인</a>
			<?if(basename($chUrl)=="order.php" || basename($chUrl)=="basket.php") {?><a href="<?=$dirLocation?>" class="btn-line w100-per h-input mt-10">비회원구매</a><?}?>
		</div><!-- //.login_area -->
		</form>

		<div class="join_yet mt-20 pt-10">
			<p class="ment">- 로그인시 SW ehop 기존회원의 비밀번호는 휴대폰번호(-제외)입니다.</p>
		</div><!-- //.join_yet -->
		<div class="join_yet mt-10 pt-10">
		</div><!-- //.join_yet -->

		<div class="login_simple">
			<p class="tit">간편 회원가입</p>
			<div class="btn_area">
				<ul class="ea2">
				<?if($snsNvConfig["use"] == "nv"){?>
					<li><a href="/plugin/sns/sns_access.php?sns=<?=$snsNvConfig["use"]?>&sns_login=1&ac=m&churl=<?=urlencode($chUrl)?>" class="btn-line"><img src="/sinwon/m/static/img/icon/icon_naver.png" alt="네이버"><span class="naver">네이버</span></a></li>
				<?}?>
				<?if($snsKtConfig["use"] == "kt"){?>
					<li><a href="/plugin/sns/sns_access.php?sns=<?=$snsKtConfig["use"]?>&sns_login=1&ac=m&churl=<?=urlencode($chUrl)?>" class="btn-line"><img src="/sinwon/m/static/img/icon/icon_kakao.png" alt="카카오톡"><span class="kakao">카카오톡</span></a></li>
				<?}?>
				<?if($snsFbConfig["use"] == "fb"){?>
					<!-- <li><a href="/plugin/sns/sns_access.php?sns=<?=$snsFbConfig["use"]?>&sns_login=1&ac=m&churl=<?=urlencode($chUrl)?>" class="btn-line"><img src="/sinwon/m/static/img/icon/icon_facebook.png" alt="페이스북"><span class="facebook">페이스북</span></a></li> -->
				<?}?>
				</ul>
			</div>
		</div><!-- //.login_simple -->

		<div class="mem_menu">
			<ul>
				<li><a href="findid.php">아이디/비밀번호 찾기</a></li>
				<li><a href="login_guest.php">비회원 주문조회</a></li>
			</ul>
		</div><!-- //.mem_menu -->

		<div class="join_yet">
			<p class="ment">아직 신원몰 회원이 아니신가요? <!-- <a href="#">신원몰 멤버쉽 안내</a> --></p>
			<a href="member_certi.php" class="btn-point point2 w100-per h-input">회원가입</a>
		</div><!-- //.join_yet -->

		<div class="join_yet mt-30 pt-20">
			<p class="tit">통합몰 회원전환</p>
			<p class="ment">신원 오프라인 매장의 회원이세요?<br>신원통합몰 회원으로 전환시 <strong class="point-color">5,000 E포인트</strong>를 즉시 증정합니다.</p>
			<a href="member_switch.php" class="btn-point w100-per h-input">신원 통합회원 전환하기</a>
		</div><!-- //.join_yet -->

	</section><!-- //.loginpage -->

</main>
<!-- //내용 -->


<script type="text/javascript" src="js/jquery.form.min.js"></script>
<script type="text/javascript">
$(function() {
	<?php
	if ($login_chk_shop === true) {
	?>
	alert("로그인을 하셔야 서비스 이용이 가능합니다.");
	<?php
	}
	?>

	var _form = $('form[name=login_form]'),
		_input_wrap = $('div.input-wrap');

	/*$('input[type=text], input[type=password]', _form).each(function() {
		$(this).data('background', $(this).css('background'));
		if (this.value.length)
			$(this).css('background', 'none');
	}).bind('blur, keydown', function(e) {
		$(this).data('background', $(this).css('background'));
		if (this.value.length) {
			$(this).css('background', 'none');
		} else {
			$(this).css('background', $(this).data().background + ' no-repeat 0 50%');
		}
	});*/

	_form.submit(function() {
		$(this).ajaxSubmit({
			type: 'GET',
			dataType: 'json',
			beforeSubmit: function() {
				var _id = $('input[name=id]', _form),
					_passwd = $('input[name=passwd]', _form);

				if (!_id.val().length) {
					alert('아이디를 입력하세요.');
					_id.focus();
					return false;
				}

				if (!_passwd.val().length) {
					alert('비밀번호를 입력하세요.');
					_passwd.focus();
					return false;
				}
			},
			success: function(response) {
				if (response.success) {
					// 페이지 이동 처리
					window.location.href = '<?=$chUrl?>';
				}else if (response.success == false) {
					var msg = decodeURIComponent(response.msg);
					alert(msg);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//alert(errorThrown);
				alert('일시적인 오류로 인해 프로그램 수행을 중단합니다.\n다시 시도해 보시기 바랍니다.');
			}
		});

		return false;
	});

});

function goLogin(){
	$('#login_form').submit();
}

function chkOrder(num){
	if(document.frmNomember.ordername.value.length==0) {
		alert("주문자 이름을 입력하세요.");
		document.frmNomember.ordername.focus();
		return;
	}
	if(document.frmNomember.ordercode.value.length==0) {
		alert("주문번호 21자리를 입력하세요.");
		document.frmNomember.ordercode.focus();
		return;
	}
	if(num == '1'){
		if(document.frmNomember.ordercode.value.length!=21) {
			alert("주문번호는 21자리입니다.\n\n다시 입력하세요.");
			document.frmNomember.ordercode.focus();
			return;
		}
	}
	if(num != '1'){
		if(document.frmNomember.ordercode.value.length!=21) {
			alert("주문번호는 21자리입니다.\n\n다시 입력하세요.");
			document.frmNomember.ordercode.focus();
			return;
		}
	}
	var param = $("#frmNomember").serialize();
	if(param){
		param = "?"+param;
	}
	$.post("ajax_nomember_order.php"+param,function(data){
		if(data=="-1"){
			alert("주문자명과 주문번호가 일치하는 주문이 없습니다.");
			return false;
		}else if(data=="-2"){
			alert("주문자명과 주문번호를 입력해야 합니다.");
			return false;
		}else if(data!="0"&&data!=null&&data!=""){
			//$("#frmNomember").attr("action","ajax_nomember_order.php");
			$("#frmNomember").submit();
		}else{
			alert("주문자명과 주문번호가 일치하는 주문이 없습니다.");
			return false;
		}
	});
}

/*로그인시 토큰 값 셋팅 함수 시작 ( 안드로이드에서 호출 )*/
function settingPushSerial(regid,os){
	if (os === undefined) os = "Android";
	$("input[name='push_os']").val(os);
	$("input[name='push_token']").val(regid);
	//console.log(os);
	//console.log(regid);
}
/*로그인시 토큰 값 셋팅 함수 종료 ( 안드로이드에서 호출 )*/
</script>

<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

<?
include_once('outline/footer_m.php')
?>

