<?
$subTitle = "비밀번호 찾기";
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

?>
<script>
	var now_find_type	= ""; 
	var now_cert_type	= ""; 

	function ipin_chk(ipin){	
		var name	= "";
		var id			= "";
		var email	= "";
		if (now_cert_type =='email')
		{
			$('.'+now_find_type+"_"+now_cert_type+'_required').each(function(){
				var input_name	= $(this).attr("name");
				if (input_name == "name") {
					name	= $(this).val();
				} else if (input_name == "id") {
					id			= $(this).val();
				} else if (input_name == "email") {
					email		= $(this).val();
				}
			});

			if (now_find_type == 'pw')
			{
				if(!confirm("입력정보가 일치할 경우 비밀번호는 변경되며 해당 정보는 이메일로 전송됩니다.\n\n검색 하시겠습니까?")) {
					return;
				}
			}
		}

		$.ajax({ 
			type: "POST", 
			url: "<?=$Dir.FrontDir?>find_idpw_indb.php", 
			data: {mode:"find"+now_find_type, cert_type:now_cert_type, name:name, id:id, email:email, access_type:'mobile'},
			dataType:"json", 
			success: function(data) {
				if (now_find_type == 'pw') {
					if (data.code == '1')
					{
						if (now_cert_type =='email') {
							//alert(data.msg+"\n(자세한 메일주소는 보안을 위해 표시하지 않습니다.)\n\n메일 수신이 되지 않을 경우 스팸함을 검색해 주십시요.");
							alert(data.msg+"\n메일이 수신이 되지 않을 경우 스팸함을 확인해주세요.");
							document.location.href="login.php";
						} else {
							document.location.href="findpw_change.php?mode=pw_change&now_find_type="+now_find_type+"&now_cert_type="+now_cert_type+"&uid="+data.msg;
						}
					} else {
						alert(data.msg);
						return;
					}
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
			}
		}); 
	}

	function go_submit(find_type, cert_type){

		chk_return = 0;
		//alert(type+"/"+cert_type);
		//return;
		if ( find_type == 'pw')
		{

			$('.'+find_type+"_"+cert_type+'_required').each(function(){
				if($(this).val()==''){
					if (chk_return == 0)
					{
						alert($(this).attr("title"));
						chk_return = 1;
						$(this).focus();
						return;
					}
				}
			});
		}
		
		if(chk_return==0){

			now_find_type	= find_type; 
			now_cert_type	= cert_type; 
				
			if(cert_type=="email"){
				 ipin_chk('email');
			}else if(cert_type=="mobile"){
//				document.getElementById("ifrmHidden").src='./checkplus/checkplus_main.php';

                document.auth_form.action = "./checkplus/checkplus_main.php";
                $("#au_auth_type").val("find_id");
                $("#au_find_type").val(find_type);
                $("#au_cert_type").val(cert_type);
                document.auth_form.submit();
			}else if(cert_type=="ipin"){
				document.getElementById("ifrmHidden").src='./ipin/ipin_main.php';
			}else{
				alert('인증 방식을 선택해주세요.');
			}
				
		}

	}
</script>
		
		<div class="sub-title">
			<h2>비밀번호 찾기</h2>
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
				<div class="find-ment">
					<h3 class="ment-title">비밀번호를 분실 하셨나요?</h3>
					<p class="ment">본인명의의 휴대폰 번호로<br>가입여부 및 본인여부를 확인합니다.</p><!-- 휴대전화 찾기시 출력 멘트 -->
					<p class="ment hide">사용하고 계신 정확한 이메일을 입력 해주세요</p><!-- 이메일 찾기시 출력 멘트 -->
				</div>
				<div id="tabs-container">
					<ul class="tabs-menu">
						<li class="on"><a href="#tab-1">휴대전화</a></li>
						<li><a href="#tab-2">이메일</a></li>
					</ul>
					<div class="tab-content-wrap">
						<div id="tab-1" class="tab-content">
							<div class="btnwrap">
								<div class="box">
									<a href="javascript:go_submit('pw','mobile');" class="btn-def pd">휴대폰 인증</a></li>
								</div>
							</div>
						</div><!-- //휴재폰인증 -->
						<div id="tab-2" class="tab-content">
							<form class="login-form">
								<fieldset>
									<legend>이메일인증으로 비밀번호를 위한 이름 아이디 이메일 입력</legend>
									<div class="login-input">
										<ul class="pos-input">
											<li class="half">
												<div><input type="text" class="pw_email_required" name="name" id="pw-user-name02" title="이름을 입력해 주세요." placeholder="이름"></div>
												<div><input type="text" class="pw_email_required" name="id" id="pw-user-id02" title="아이디를 입력해 주세요." placeholder="아이디"></div>
											</li>
											<li><input type="email" class="pw_email_required" name="email" id="user-email02" title="이메일을 입력해 주세요." placeholder="E-mail"></li>
										</ul>
										<button type='button' class="btn-def" class="submit" onClick="javascript:go_submit('pw','email');">확인</button>
									</div>
								</fieldset>
							</form>
							
						</div><!-- //비회원주문조회 -->
					</div>
				</div>
				<div class="cs-summary">
					<span class="tel">02-2145-1400</span>
					<span class="time">am 09:30~pm 17:30</span>
					<span class="date">토/일요일/공휴일 휴무</span>
					<span class="mail">cash@cash-stores.com</span>
				</div>

				

			</div><!-- //.inner -->
		</div><!-- //.member-wrap -->

        <form method="GET" id="auth_form" name="auth_form">
            <input type="hidden" id="au_auth_type" name="auth_type" />
            <input type="hidden" id="au_find_type" name="find_type" />
            <input type="hidden" id="au_cert_type" name="cert_type" />
        </form>

<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<? include_once('outline/footer_m.php'); ?>
