<?
$subTitle = "아이디 찾기";
include_once('outline/header_m.php');
//include_once('sub_header.inc.php');

if(strlen($_MShopInfo->getMemid())!=0) {
	echo ("<script>location.replace('/m/');</script>");
	exit;
}

$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

if(!$CertificationData->ipin_check  && !$CertificationData->realname_check){
	$mail_chk="checked";
}

$now_find_type	= $_GET['now_find_type'];
if ($now_find_type=='') $now_find_type = "id";
$now_cert_type	= $_GET['now_cert_type'];
$mode				= $_GET['mode'];
$name				= $_GET['name'];
$uid					= $_GET['uid'];
?>
<script>
	var now_find_type	= "";
	var now_cert_type	= "";
	var mode = "<?=$mode?>";
	$(document).ready(function(){
		if(mode == "result"){
			now_find_type = "id";
			now_cert_type = "ipin";
			ipin_chk('ipin');
		}else if(mode == "pw_change"){
			now_find_type = "pw";
			now_cert_type = "ipin";
			ipin_chk('ipin');
		}		
	});	

	function ipin_chk(ipin, uname){
		var name	= "<?=$name?>";
		var id			= "";
		var email	= "";

		if(name != ""){
			$.ajax({
				type: "POST",
				url: "<?=$Dir.FrontDir?>find_idpw_indb.php",
				data: {mode:"find"+now_find_type, cert_type:now_cert_type,access_type:"m_store" ,name:name, id:id, email:email},
				dataType:"json",
				success: function(data) {
					if (data.code == '1') {
						if (data.now_find_type == 'findid') {
							$('#find_id').text(data.msg);
						} else if (data.now_find_type == 'findpw') {
							$("input[name=u_id]").val(data.msg);
							$('span.find_pw_memid').html(data.changeid);
							//$('.findpw_change').fadeIn();
						}
					} else {
						alert(data.msg);
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
				}
			});
		}
	}

	function go_submit(find_type, cert_type){

		now_find_type	= find_type;
		now_cert_type	= cert_type;

		if(cert_type=="mobile"){
            document.auth_form.action = "./checkplus/checkplus_main.php";
			//document.auth_form.action = "./checkplus/checkplus_main_test.php"; // 테스트용
            $("#au_auth_type").val("find_id");
            $("#au_find_type").val(find_type);
            $("#au_cert_type").val(cert_type);
            document.auth_form.submit();
		}else{
			document.auth_form.action = "./ipin_m/IPINMain.php";
            $("#au_auth_type").val("find_"+find_type);
            $("#au_find_type").val(find_type);
            $("#au_cert_type").val(cert_type);
			document.auth_form.submit();
		}	
}

	function pwd_change() {

		now_find_type	= "<?=$now_find_type?>"; 
		now_cert_type	= "<?=$now_cert_type?>"; 

		var u_id			= $("input[name=u_id]").val();
		var pw1_val	= $("input[name=ch_pw1]").val();
		var pw2_val	= $("input[name=ch_pw2]").val();

		if (pw1_val == '') {
			alert($("input[name=ch_pw1]").attr("title"));
			$("input[name=ch_pw1]").focus();
			return;
		}	

		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9]).*$/)).test(pw1_val)) {
			alert("8~20자 이내 영문, 숫자 2가지 조합으로 이루어져야 합니다.");
			$("input[name=ch_pw1]").focus();
			return;
		}

		if (pw2_val == '') {
			alert($("input[name=ch_pw2]").attr("title"));
			$("input[name=ch_pw2]").focus();
			return;
		}

		if (pw1_val != pw2_val) {
			alert("비밀번호를 다시 확인해 주세요.");
			$("input[name=ch_pw2]").focus();
			return;
		} else {
			$.ajax({ 
				type: "POST", 
				url: "<?=$Dir.FrontDir?>find_idpw_indb.php", 
				data: {mode:"find"+now_find_type+"_change", cert_type:now_cert_type, id:u_id, ch_pwd:pw1_val},
				dataType:"json", 
				success: function(data) {
					if (data.code == '1')
					{
						document.location.href="<?=$Dir.MDir?>findid.php?now_find_type="+now_find_type+"&now_cert_type="+now_cert_type+"&mode=pw_change_end";
					} else {
						alert(data.msg);
						return;
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
					return;
				}
			}); 
		}
	}
</script>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>아이디/비밀번호 찾기</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="loginpage sub_bdtop">
		
		<div class="idpw_tab tab_type1" data-ui="TabMenu">
			<div class="tab-menu clear">
				<a data-content="menu"<?=$now_find_type == 'id'?' class="active" title="선택됨"':''?>>아이디 찾기</a>
				<a data-content="menu"<?=$now_find_type == 'pw'?' class="active" title="선택됨"':''?>>비밀번호 찾기</a>
			</div>

			<!-- 아이디 찾기 -->
			<div class="tab-content<?=$now_find_type == 'id'?' active':''?>" data-content="content">
				<?if ($mode != 'result') {?>
				<!-- 찾기 -->
				<div class="certification">
					<div class="icon certi_phone"><img src="/sinwon/m/static/img/icon/icon_certi_phone.png" alt="휴대폰 인증"></div>
					<div class="info">
						<p>본인명의의 휴대폰 번호로 가입여부 및 본인여부를 확인합니다. 타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</p>
						<a href="javascript:go_submit('id','mobile');" class="btn-point">휴대폰 인증</a>
					</div>
				</div>
				<div class="certification">
					<div class="icon certi_ipin"><img src="/sinwon/m/static/img/icon/icon_certi_ipin.png" alt="아이핀 인증"></div>
					<div class="info">
						<p>회원가입시 아이핀으로 가입한 경우 본인여부 확인이 가능합니다.</p>
						<a href="javascript:go_submit('id','ipin');" class="btn-point">아이핀 인증</a>
					</div>
				</div>
				<!-- //찾기 -->
				<?} else {?>
				<!-- 결과 -->
				<div class="result result_id">
					<p class="msg">회원님의 아이디는 <strong class="point-color"><?=$uid?></strong>입니다.</p>
					<a href="login.php" class="btn-point w100-per h-input">로그인</a>
				</div>
				<!-- //결과 -->
				<?}?>
			</div>
			<!-- //아이디 찾기 -->

			<!-- 비밀번호 찾기 -->
			<div class="tab-content<?=$now_find_type == 'pw'?' active':''?>" data-content="content">
			<?if ($mode != 'pw_change' && $mode != 'pw_change_end') {?>
				<!-- 찾기 -->
				<div class="certification">
					<div class="icon certi_phone"><img src="/sinwon/m/static/img/icon/icon_certi_phone.png" alt="휴대폰 인증"></div>
					<div class="info">
						<p>본인명의의 휴대폰 번호로 가입여부 및 본인여부를 확인합니다. 타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</p>
						<a href="javascript:go_submit('pw','mobile');" class="btn-point">휴대폰 인증</a>
					</div>
				</div>
				<div class="certification">
					<div class="icon certi_ipin"><img src="/sinwon/m/static/img/icon/icon_certi_ipin.png" alt="아이핀 인증"></div>
					<div class="info">
						<p>회원가입시 아이핀으로 가입한 경우 본인여부 확인이 가능합니다.</p>
						<a href="javascript:go_submit('pw','ipin');" class="btn-point">아이핀 인증</a>
					</div>
				</div>
				<!-- //찾기 -->
				<?} else {?>
				<!-- 결과 -->
					<?if ($mode == 'pw_change') {?>
				<div class="result result_pw">
					<input type="hidden" name="u_id" value="<?=$_GET['uid']?>">
					<div class="login_area mt-15">
						<input type="password" class="w100-per" id="ch-pwd1" name="ch_pw1"  placeholder="신규 비밀번호 (영문, 숫자 포함 8~20자리)" title="신규 비밀번호를 입력하세요.">
						<input type="password" class="w100-per" id="ch-pwd2" name="ch_pw2" placeholder="비밀번호 재입력" title="신규 비밀번호를 한 번 더 입력해 주세요.">
						<a href="javascript:;" class="btn-point w100-per h-input" onClick="javascript:pwd_change('<?=$uid?>');">비밀번호 변경</a>
					</div>
				</div>
					<?} else if ($mode == 'pw_change_end') {?>
				<div class="result result_id">
					<p class="msg">회원님의 비밀번호가 변경 되었습니다.</p>
					<a href="login.php" class="btn-point w100-per h-input">로그인</a>
				</div>
					<?}?>
				<!-- //결과 -->
				<?}?>
			</div>
			<!-- //비밀번호 찾기 -->
		</div>

	</section><!-- //.loginpage -->

</main>
<!-- //내용 -->

<form method="GET" id="auth_form" name="auth_form">
	<input type="hidden" id="au_auth_type" name="auth_type" />
	<input type="hidden" id="au_find_type" name="find_type" />
	<input type="hidden" id="au_cert_type" name="cert_type" />
</form>

<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<? include_once('outline/footer_m.php'); ?>
