<?
$subTitle = "회원가입";
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())!=0) {
	echo ("<script>location.replace('/m/');</script>");
	exit;
}

$mem_type = $_GET[mem_type];
if (!$mem_type) $mem_type = 0;
?>

<SCRIPT LANGUAGE="JavaScript">
<!--

	$(function(){
		$("#all-agree").click(function(){
			//만약 전체 선택 체크박스가 체크된상태일경우
			if($("#all-agree").prop("checked")) {
				//해당화면에 전체 checkbox들을 체크해준다
				$(".chk_agree").prop("checked",true);
			// 전체선택 체크박스가 해제된 경우
			} else {
				//해당화면에 모든 checkbox들의 체크를해제시킨다.
				$(".chk_agree").prop("checked",false);
			}
		});
	});

	function CheckForm()
	{

		if($("#agree01").is(":checked")==false){
			alert('[이용약관]을 동의하셔야 회원가입이 가능합니다.');
			$("#agree01").focus();
			return false;
		}

		if($("#agree02").is(":checked")==false){
			alert('[개인정보 보호를 위한 이용자 동의사항]을 동의하셔야 회원가입이 가능합니다.');
			$("#agree02").focus();
			return false;
		}	

		type	= $("input[name=ceti-type]:checked").val();
	
		$('#auth_type').val(type);

		if(type=='ipin'){
			//window.open('', 'popupIPIN2', 'width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no'); 
			//document.getElementById("ifrmHidden").src="./ipin_m/IPINMain.php?callType=joinmember";

            document.auth_form.action = "./ipin_m/IPINMain.php?callType=joinmember";
            $("#au_auth_type").val(type);
            document.auth_form.submit();

		}else if(type=='real'){
			//window.open('', 'popupChk', 'width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no'); 
//			document.getElementById("ifrmHidden").src="./checkplus/checkplus_main.php";

            document.auth_form.action = "./checkplus/checkplus_main.php";
            $("#au_auth_type").val(type);
            document.auth_form.submit();

		} else {
			$('#mem_type').val('<?=$mem_type?>');
			document.form_agree.submit();
		}

		return true;
	}

	function ipin_chk(){
		document.getElementById("ifrmHidden").src="../front/member_chkid.php";
	}


	function ipin_chk2(yn){
		if(yn=='1'){
			$('#mem_type').val('<?=$mem_type?>');
			document.form_agree.submit();
		}else{
			var u_name	= $("#popup-join-overlap").find("#namespan").html();
			var u_id			= $("#popup-join-overlap").find("#idspan").html();
			//alert(u_name+" 고객님께서는 "+u_id+"로 이미 가입하셨습니다.");
			popup_open('#popup-join-overlap');
		}
	}

//-->
</SCRIPT>
		
		<div class="sub-title">
			<h2>회원가입</h2>
			<a class="btn-prev" href="/m/"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			<div class="js-sub-menu">
				<button class="js-btn-toggle" title="펼쳐보기"><img src="./static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
				<div class="js-menu-content">
					<ul>
						<li><a href="login.php">로그인</a></li>
						<li><a href="findid.php">아이디 찾기</a></li>
						<li><a href="findpw.php">비밀번호 찾기</a></li>
						<li><a href="member_agree.php">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="member-wrap">
			<div class="inner">
				<div class="join-flow">
					<img src="./static/img/common/join_flow01.gif" alt="01.약관동의 / 실명인증">
					<h3 class="title">환영합니다.</h3>
					<p><?=$_data->shoptitle?> 이용약관을 확인 후<br>동의하셔야 회원가입이 완료 됩니다.</p>
				</div>
			</div><!-- //.inner -->
	
			<form name=form_agree action="member_join_iphone.php" method=post>
			<input type="hidden" name="auth_type" id="auth_type" >
			<input type="hidden" name="mem_type" id="mem_type" >		
			<div class="line-title"><input type="checkbox" id="all-agree"><label for="all-agree"><?=$_data->shoptitle?> 약관 전체동의</label></div>
			<div class="use-agree">
				<ul class="inner">
					<li>
						<input type="checkbox"  id="agree01" class="chk_agree">
						<label for="agree01">이용약관</label>
						<a href="agreement.php" class="btn-function" target="_blank">내용보기</a>
					</li>
					<li>
						<input type="checkbox"  id="agree02" class="chk_agree">
						<label for="agree02">개인정보 보호를 위한 이용자 동의사항</label>
						<a href="privacy.php" class="btn-function" target="_blank">내용보기</a>
					</li>
				</ul>
			</div>

			<!-- <div class="line-title margin">실명인증</div>
			<div class="ceti-type">
				<input type="radio" name="ceti-type" id="ceti-type-a" value='real' checked>
				<label for="ceti-type-a">모바일 인증</label>
				<input type="radio" name="ceti-type" id="ceti-type-b" value='ipin'>
				<label for="ceti-type-b">아이핀(I-PIN)</label>
			</div> -->

			<div class="btnwrap page-end">
				<div class="box">
					<a class="btn-def" href="javascript:CheckForm();">확인</a>
				</div>
			</div>
			</form>		

            <form method="GET" id="auth_form" name="auth_form">
                <input type="hidden" id="au_auth_type" name="auth_type" />
                <input type="hidden" id="au_mem_type" name="mem_type" value="<?=$mem_type?>"/>
            </form>

		</div><!-- //.member-wrap -->

<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<? include_once('outline/footer_m.php'); ?>
