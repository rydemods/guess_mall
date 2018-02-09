<?
$subTitle = "비회원 주문조회";
include_once('outline/header_m.php');
?>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>비회원 주문조회</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="loginpage sub_bdtop">
		<form class="login-form" id="frmNomember" name="frmNomember" method=post action="mypage_orderlist_view.php">
		<input type=hidden name=mode value="guestMobile">
		<div class="login_area">
			<input type="text" class="w100-per" title="이름 입력자리" placeholder="이름 입력" id="ordername" name="ordername">
			<input type="text" class="w100-per" title="주문번호 입력자리" placeholder="주문번호 입력" id="ordercode" name="ordercode">
			<a href="javascript:;" class="btn-point w100-per h-input" onClick="javascript:chkOrder('1')">비회원 주문조회</a>
		</div><!-- //.login_area -->
		</form>

		<div class="mem_menu">
			<ul>
				<li><a href="findid.php">아이디/비밀번호 찾기</a></li>
				<li><a href="login.php">로그인</a></li>
			</ul>
		</div>

		<div class="join_yet">
			<p class="ment">아직 신원몰 회원이 아니신가요? <!-- <a href="#">신원몰 멤버쉽 안내</a> --></p>
			<a href="member_certi.php" class="btn-point point2 w100-per h-input">회원가입</a>
		</div><!-- //.join_yet -->

		<div class="join_yet mt-30 pt-20">
			<p class="tit">통합회원 전환</p>
			<p class="ment">신원 오프라인 매장의 회원이세요?<br>신원 통합회원으로 전환시 <strong class="point-color">5,000 E포인트</strong>를 즉시 증정합니다.</p>
			<a href="member_switch.php" class="btn-point w100-per h-input">신원 통합회원 전환하기</a>
		</div><!-- //.join_yet -->

	</section><!-- //.loginpage -->

</main>
<!-- //내용 -->


<script type="text/javascript" src="js/jquery.form.min.js"></script>
<script type="text/javascript">

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
			$("#frmNomember").submit();
		}else{
			alert("주문자명과 주문번호가 일치하는 주문이 없습니다.");
			return false;
		}
	});
}
</script>

<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

<?
include_once('outline/footer_m.php')
?>

