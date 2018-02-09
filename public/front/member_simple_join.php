<?php

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");
	include_once($Dir."conf/config.sns.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

	$type	= $_POST['type'];

	if ($type == "insert") {// 회원가입 등록	

		if($_data->group_code){
			$group_code=$_data->group_code;
		}else{
			$group_code="";	
		}

		$id					= trim($_POST["id"]);
		$passwd1			= $_POST["passwd1"];
		$name				= trim($_POST["name"]);
		$email				= $_POST["email"]? trim($_POST["email"]):$id;
		$mobile				= $_POST['mobile1']."-".$_POST['mobile2']."-".$_POST['mobile3'];
		$news_sms_yn	= $_POST["news_sms_yn"];
		$news_mail_yn	= $_POST["news_mail_yn"];
		$sns_type			= $_POST["sns_type"];

		$onload="";

		$sql = "SELECT email FROM tblmember WHERE email='{$email}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			echo "<html><head></head><body onload=\"alert('아이디가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.');parent.location.href='member_agree.php';\"></body></html>";exit;
		}
		pmysql_free_result($result);

		//insert					

		if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
			$news_yn="Y";
		} else if($news_mail_yn=="Y") {
			$news_yn="M";
		} else if($news_sms_yn=="Y") {
			$news_yn="S";
		} else {
			$news_yn="N";
		}

		$confirm_yn	= "Y";
		$ip				= $_SERVER['REMOTE_ADDR'];
		$date				= date("YmdHis");

		 $shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd1))));	
		
		BeginTrans();
		
		$sql = "INSERT INTO tblmember(id) VALUES('{$id}')";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblmember SET ";
		$sql.= "id			= '{$id}', ";
		$sql.= "passwd		= '".$shadata."', ";
		$sql.= "name		= '{$name}', ";
		$sql.= "email		= '{$email}', ";
		$sql.= "mobile		= '{$mobile}', ";
		$sql.= "news_yn		= '{$news_yn}', ";
		$sql.= "joinip		= '{$ip}', ";
		$sql.= "ip			= '{$ip}', ";
		$sql.= "date		= '{$date}', ";
		$sql.= "sns_type		= '{$sns_type}', ";

		if(ord($group_code)) {
			$sql.= "group_code='{$group_code}', ";
		}

		$sql.= "confirm_yn	= '{$confirm_yn}' WHERE id='{$id}'";

		//echo $sql;
		//exit;
		$insert=pmysql_query($sql,get_db_conn());

		if (pmysql_errno()==0) {
			CommitTrans();

			//---------------------------------------------------- 가입시 로그를 등록한다. ----------------------------------------------------//
			$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$id."','join','web','".date("YmdHis")."')";
			pmysql_query($memLogSql,get_db_conn());
			//---------------------------------------------------------------------------------------------------------------------------------//

			//가입메일 발송 처리
			if(ord($email)) {
				SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id);
			}

			$mem_return_msg = sms_autosend( 'mem_join', $id, '', '' );
			$admin_return_msg = sms_autosend( 'admin_join', $id, '', '' );

			echo "<html><head></head><body onload=\"alert('회원가입이 완료되었습니다.\\n\\n감사합니다.');parent.location.href='member_joinend.php?name={$name}&id={$id}';\"></body></html>";exit;
		} else {
			RollbackTrans();
			echo "<html><head></head><body onload=\"alert('회원등록 중 오류가 발생하였습니다.\\n\\n관리자에게 문의하시기 바랍니다.');parent.location.href='member_agree.php';\"></body></html>";exit;
		}
	}

	$_ShopInfo->setCheckSns("");
	$_ShopInfo->setCheckSnsLogin("");
	$_ShopInfo->setCheckSnsAccess("");
	$_ShopInfo->setCheckSnsChurl("");
	$_ShopInfo->Save();

	if ($_POST["sns_email"]) $add_where	= " AND id='".$_POST["sns_email"]."' ";

	$snsCertiData = pmysql_fetch_object(pmysql_query("select id, name from tblmember where sns_type = '".$_POST["sns_type"]."||".$_POST['sns_id']."' {$add_where} "));

	#####실명인증 결과에 따른 분기
	if($snsCertiData->id){
		echo "<script>alert('".$snsCertiData->name." 고객님께서는 [".$snsCertiData->id."]로 이미 가입하셨습니다.');location.href='login.php';</script>";
		exit;
	}
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function(){
		$("#agreeAll").click(function(){
			$("#term01").prop('checked', $(this).prop('checked'));
			$("#term02").prop('checked', $(this).prop('checked'));
			$("#term03").prop('checked', $(this).prop('checked'));
		})
	})

	function ValidFormId(type) { //아이디 유효성 체크		

		var email_id		= $("input[name=email_id]").val();
		var email_addr	= $("input[name=email_addr]").val();

		$("input[name=email_id]").removeClass("alert-line");
		$("input[name=email_addr]").removeClass("alert-line");

		if(email_id.length==0){
			alert("이메일 아이디를 입력하세요.");
			$("input[name=email_id]").focus();
			$("input[name=email_id]").addClass("alert-line");
			return;
		}

		if(email_addr.length==0){
			alert("이메일 주소를 입력하세요.");
			$("input[name=email_addr]").focus();
			$("input[name=email_addr]").addClass("alert-line");
			return;
		}	

		$("input[name=id]").val(email_id+"@"+email_addr);

		var val	= $("input[name=id]").val();
		
		$.ajax({ 
			type: "GET", 
			url: "<?=$Dir.FrontDir?>iddup.proc.php", 
			data: "id=" + val + "&mode=id",
			dataType:"json", 
			success: function(data) {
				$("input[name=email_id]").parent().find(".join-att-ment").html(data.msg);
				$("#id_checked").val(data.code);
				if (data.code == 0) {
					$("input[name=email_id]").addClass("alert-line");
					$("input[name=email_addr]").addClass("alert-line");
				} else {
					if(type=='') {
						ValidFormPassword();
					}
				}
			},
			error: function(result) {
				$("input[name=email_id]").parent().find(".join-att-ment").html("에러가 발생하였습니다."); 
				$("input[name=email_id]").addClass("alert-line");
				$("input[name=email_addr]").addClass("alert-line");
			}
		}); 
	}

	function ValidFormPassword(){//비밀번호 유효성 체크
		var val	= $("input[name=passwd1]").val();
		$("input[name=passwd1]").removeClass("alert-line");
		if (val == '') {
			$("input[name=passwd1]").parent().find(".join-att-ment").html($("input[name=passwd1]").attr("title"));
			$("input[name=passwd1]").addClass("alert-line");
		} else {
			if (!(new RegExp(/^.*(?=.{4,20})(?=.*[a-zA-Z])(?=.*[0-9]).*$/)).test(val)) {
				$("input[name=passwd1]").parent().find(".join-att-ment").html("4~20자 이내 영문, 숫자 2가지 조합으로 이루어져야 합니다.");
				$("input[name=passwd1]").addClass("alert-line");
			} else {
				$("input[name=passwd1]").parent().find(".join-att-ment").html("");
				$("#passwd1_checked").val("1");
				ValidFormPasswordRe();
			}
		}
	}


	function ValidFormPasswordRe(){
		var val			= $("input[name=passwd2]").val();
		var pw1_val	= $("input[name=passwd1]").val();

		$("input[name=passwd2]").removeClass("alert-line");
		if (val == '') {
			$("input[name=passwd2]").parent().find(".join-att-ment").html($("input[name=passwd2]").attr("title"));
			$("input[name=passwd2]").addClass("alert-line");
		} else {
			if (val != pw1_val) {
				$("input[name=passwd2]").parent().find(".join-att-ment").html("비밀번호를 다시 확인해 주세요.");
				$("input[name=passwd2]").addClass("alert-line");
			} else {
				$("input[name=passwd2]").parent().find(".join-att-ment").html("비밀번호가 일치합니다.");
				$("#passwd2_checked").val("1");
				ValidFormName();
			}
		}
	}


	function ValidFormName(){
		var val			= $("input[name='name']").val();

		$("input[name='name']").removeClass("alert-line");
		if (val == '') {
			$("input[name='name']").parent().find(".join-att-ment").html($("input[name='name']").attr("title"));
			$("input[name='name']").addClass("alert-line");
		} else {
			$("#name_checked").val("1");
			ValidFormMobile();
		}
	}


	function ValidFormMobile(){
		var val1			= $("input[name=mobile1]").val();
		var val2			= $("input[name=mobile2]").val();
		var val3			= $("input[name=mobile3]").val();

		$("input[name=mobile1]").removeClass("alert-line");
		$("input[name=mobile2]").removeClass("alert-line");
		$("input[name=mobile3]").removeClass("alert-line");

		if (val1 == '' || val2 == '' || val3 == '') {
			$("input[name=mobile1]").parent().find(".join-att-ment").html($("input[name=mobile1]").attr("title"));
			if (val1 == '') $("input[name=mobile1]").addClass("alert-line");
			if (val2 == '') $("input[name=mobile2]").addClass("alert-line");
			if (val3 == '') $("input[name=mobile3]").addClass("alert-line");
		} else {
			$("#mobile_checked").val("1");
			CheckFormSubmit();
		}
	}


	function CheckForm(memtype) {
		$("input[name=id_checked]").val('0');
		$("input[name=passwd1_checked]").val('0');
		$("input[name=passwd2_checked]").val('0');
		$("input[name=name_checked]").val('0');
		$("input[name=mobile_checked]").val('0');
		ValidFormId('');
	}

	function CheckFormSubmit() {
		form=document.form1;

		var id_checked = $("input[name=id_checked]").val();
		var passwd1_checked = $("input[name=passwd1_checked]").val();
		var passwd2_checked = $("input[name=passwd2_checked]").val();
		var name_checked		= $("input[name=name_checked]").val();
		var mobile_checked	= $("input[name=mobile_checked]").val();

		if(!$("#term01").prop('checked')){
			alert("이용약관에 동의 하지 않으셨습니다");
			$("#term01").focus();
			return;
		}
		if(!$("#term02").prop('checked')){
			alert("개인정보 수집 및 이용안내에 동의 하지 않으셨습니다");
			$("#term02").focus();
			return;
		}
		if(!$("#term03").prop('checked')){
			alert("개인정보 취급위탁 동의에 동의 하지 않으셨습니다");
			$("#term03").focus();
			return;
		}


		if (id_checked == '1' && passwd1_checked == '1' && passwd2_checked == '1' && name_checked == '1' && mobile_checked == '1')
		{
			form.type.value="insert";
			form.target	= "HiddenFrame";
			<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
				form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
			<?php }?>
			if(confirm("회원가입을 하겠습니까?"))
				form.submit();
			else
				return;
		} else {
			return;
		}
	}
</SCRIPT>
<style>
	.join-att-ment{
		padding-left:10px;
	}
</style>
<div id="contents">
	<main class="join-wrap">
				
		<header>
			<h1>the hook 회원가입</h1>
			<ul class="page-flow clear">
				<li><i><img src="../static/img/icon/icon_join_flow01.gif" alt="인증선택"></i>01 인증선택</li>
				<li class="on"><i><img src="../static/img/icon/icon_join_flow02.gif" alt="회원가입 및 약관 동의"></i>02 회원가입 및 약관 동의</li>
				<li><i><img src="../static/img/icon/icon_join_flow03.gif" alt="가입완료"></i>03 가입완료</li>
			</ul>
		</header>
<?
	$sns_email	= explode("@",$_POST["sns_email"]);
?>
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<input type="hidden" name="type" value="">
			<input type="hidden" name="sns_type" value="<?=$_POST["sns_type"]."||".$_POST['sns_id']?>">
			<input type="hidden" name="email" value="<?=$_POST["sns_email"]?>">
			<input type="hidden" name="id_checked" id="id_checked" value="0">
			<input type="hidden" name="passwd1_checked" id="passwd1_checked" value="0">
			<input type="hidden" name="passwd2_checked" id="passwd2_checked" value="0">
			<input type="hidden" name="name_checked" id="name_checked" value="0">
			<input type="hidden" name="mobile_checked" id="mobile_checked" value="0">
			<input type="hidden" name="id">
			<article>
				<section>
					<div class="title-box">
						<h2>SNS 간편가입 정보입력</h2>
						<p class="ment"><span class="required">*</span> 표시는 필수 항목이므로 반드시 기재하셔야 합니다</p>
					</div>					

					<table class="member-th-left">
						<caption></caption>
						<colgroup>
							<col style="width:160px">
							<col style="width:auto">
						</colgroup>
						<tr>
							<th><span class="required">아이디</span></th>
							<td>
							<?if ($_POST["sns_email"]) {?>
								<span><?=$_POST["sns_email"]?></span>
								<input type="hidden" name="email_id" id="ID_email_id" title="이메일 아이디" value='<?=$sns_email[0]?>'>
								<input type="hidden" name="email_addr" id="ID_email_addr" title="이메일 도메인" value='<?=$sns_email[1]?>'>
							<?} else {?>
								<input type="text" name="email_id" id="ID_email_id" title="이메일 아이디" style="width:105px;" value='<?=$sns_email[0]?>'>
								<span class="dash">&#64;</span>
								<input type="text" name="email_addr" id="ID_email_addr" title="이메일 도메인" style="width:105px;" value='<?=$sns_email[1]?>'>
								<select name="email_select" id="ID_email_select" title="이메일 도메인 선택" onchange="javascript:$('#ID_email_addr').val(this.value);">
									<option value="">직접입력</option>
									<option value="naver.com"<?=$sns_email[1]=='naver.com'?' selected':''?>>naver.com</option>
									<option value="gmail.com"<?=$sns_email[1]=='gmail.com'?' selected':''?>>gmail.com</option>
									<option value="hanmail.net"<?=$sns_email[1]=='hanmail.net'?' selected':''?>>hanmail.net</option>
								</select>
								<a href="javascript:ValidFormId('chk');" target="_blank" class="btn-basic">중복확인</a><span class="join-att-ment"></span>
							<?}?>
							</td>
						</tr>
						<tr>
							<th><span class="required">비밀번호 입력</span></th>
							<td>
								<input type="password" name="passwd1" id="ID_passwd1" class="fullwidth" title="비밀번호를 입력하세요.">
								<span class="join-att-ment"></span>
							</td>
						</tr>
						<tr>
							<th><span class="required">비밀번호 확인</span></th>
							<td>
								<input type="password" name="passwd2" id="ID_passwd2" class="fullwidth" title="비밀번호를 한 번 더 입력하세요.">
								<span class="join-att-ment"></span>
							</td>
						</tr>
						<tr>
							<th><span class="required">이름</span></th>
							<td>
							<?if ($_POST["sns_name"]) {?>
								<span><?=$_POST["sns_name"]?></span>
								<input type="hidden" name="name" id="ID_name" title="이름을 입력하세요." value="<?=$_POST["sns_name"]?>">
							<?} else {?>
								<input type="text" name="name" id="ID_name" class="fullwidth" title="이름을 입력하세요." value="<?=$_POST["sns_name"]?>">
								<span class="join-att-ment"></span>
							<?}?>
							</td>
						</tr>
						<tr>
							<th><span class="required">휴대폰 번호</span></th>
							<td>
								<input type="text" name="mobile1" id="ID_mobile1" maxlength="3" title="휴대폰 번호를 입력하세요." style="width:50px;">
								<span class="dash">-</span>
								<input type="text" name="mobile2" id="ID_mobile2" maxlength="4" title="휴대폰 번호를 입력하세요." style="width:50px;">
								<span class="dash">-</span>
								<input type="text" name="mobile3" id="ID_mobile3" maxlength="4" title="휴대폰 번호를 입력하세요." style="width:50px;">
								<span class="join-att-ment"></span>
							</td>
						</tr>
						<tr>
							<th>수신동의</th>
							<td>
								<span class="input-checkbox-wrap">
									<input type="checkbox" name="news_sms_yn" id="ID_news_sms_yn" value="Y"><label for="ID_news_sms_yn">SMS</label>
								</span>
								<span class="input-checkbox-wrap">
									<input type="checkbox" name="news_mail_yn" id="ID_news_mail_yn" value="Y"><label for="ID_news_mail_yn">이메일</label>
								</span>
							</td>
						</tr>
					</table>
				</section>

				<section>
					<header>
						<h2>이용약관</h2>
						<p>이용약관과 개인정보 취급방침은 서비스 사이트이용 및 상품 매매 규정사항입니다.<br>
						가입 전에 반드시 읽어보시고, 동의하셔야 회원가입이 완료됩니다.</p>
					</header>

					<div class="wrap-terms">
						<ul class="clear">
							<li class="term_01">
								<div><input type="checkbox" id="term01"> <label for="term01">이용약관</label></div>
								<a href="javascript:;" class="term_view">내용보기</a>
							</li>
							<li class="term_02">
								<div><input type="checkbox" id="term02"> <label for="term02">개인정보 수집 및 이용안내</label></div>
								<a href="javascript:;" class="term_view">내용보기</a>
							</li>
							<li class="term_03">
								<div><input type="checkbox" id="term03"> <label for="term03">개인정보 취급위탁 동의</label></div>
								<a href="javascript:;" class="term_view">내용보기</a>
							</li>
						</ul>
						<p>※ 이용약관, 개인정보 수집 및 이용안내 및 개인정보 취급위탁 동의를 확인 하였으며, 이에 동의합니다.</p>
					</div>
				</section>

				<div class="join-all-agree">
					<input type="checkbox" id="agreeAll"> <label for="agreeAll">사이트 이용을 위한 이용약관에 모두 동의 합니다.</label>
					<button type="button" class="btn-point" onClick="javascript:CheckForm('<?=$mem_type?>');">가입완료</button>
				</div>
			</article>
		</form>

	</main>
</div><!-- //#contents -->
<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
<?php  include ($Dir."lib/bottom.php") ?>
