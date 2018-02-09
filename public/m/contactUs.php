<?php
include_once('outline/header_m.php');
?>

<!-- 내용 -->
<main id="content" class="subpage fullh">

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>입점문의</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="sub_bdtop">
		<form id="storeLocWriteForm2" name="storeLocWriteForm2" method="post" >
		<input type=hidden name=mode value=''>
        <input type=hidden name=pagetype value='write'>
        <input type=hidden name=exec value='write'>
        <input type=hidden name=num value=>
        <input type=hidden name=board value='storeloc'>
        <input type=hidden name=s_check value=>
        <input type=hidden name=search value=>
        <input type=hidden name=block value=>
        <input type=hidden name=gotopage value=>
        <input type=hidden name=pridx value=>
        <input type=hidden name=recipe_no value=>
        <input type=hidden name=pos value="">
        <input type=hidden name=up_is_secret value="">
        <input type=hidden name=up_passwd value="1234">
        <input type=hidden id="up_storetel" name=up_storetel value="">
        <input type=hidden id="up_email" name=up_email value="">
        <!-- 해당 전달값 없을시 write.php 에서 에러발생함 [lib.js.php 분석결과] (reWriteName(form))   -->
        <input type=hidden id="" name=ins4e[mode] value="up_result">
        <input type=hidden id="" name=ins4e[up_subject] value="tempfilename">
		<div class="board_type_write pb-35">
			<dl>
				<dt><span class="required" for="contact_name">작성자</span></dt>
				<dd><input type="text" class="w100-per" placeholder="작성자 입력(필수)" id="up_name" name="up_name"></dd>
			</dl>
			<dl>
				<dt><span class="required">휴대폰 번호</span></dt>
				<dd>
					<div class="input_tel">
						<select class="select_line" id="phone_num1">
							<option value="">선택</option>
							<option value="010">010</option>
							<option value="011">011</option>
							<option value="016">016</option>
							<option value="017">017</option>
							<option value="018">018</option>
							<option value="019">019</option>
						</select>
						<span class="dash"></span>
						<input type="tel" maxlength="4" id="phone_num2">
						<span class="dash"></span>
						<input type="tel" maxlength="4" id="phone_num3">
					</div>
				</dd>
			</dl>
			<dl>
				<dt><span class="required" for="contact_email">이메일</span></dt>
				<dd>
					<div class="input_mail">
						<input type="text" class="" id="contact_email" name="contact_email">
						<span class="at">@</span>
						<select class="select_line" id="email_select" onchange="EmailChange()">
							<option value="@naver.com">naver.com</option>
							<option value="@daum.net">daum.net</option>
							<option value="@gmail.com">gmail.com</option>
							<option value="@nate.com">nate.com</option>
							<option value="@yahoo.co.kr">yahoo.co.kr</option>
							<option value="@lycos.co.kr">lycos.co.kr</option>
							<option value="@empas.com">empas.com</option>
							<option value="@hotmail.com">hotmail.com</option>
							<option value="@msn.com">msn.com</option>
							<option value="@hanmir.com">hanmir.com</option>
							<option value="@chol.net">chol.net</option>
							<option value="@korea.com">korea.com</option>
							<option value="@netsgo.com">netsgo.com</option>
							<option value="@dreamwiz.com">dreamwiz.com</option>
							<option value="@hanafos.com">hanafos.com</option>
							<option value="@freechal.com">freechal.com</option>
							<option value="@hitel.net">hitel.net</option>
							<option value="">직접입력</option>
						</select>
					</div>
					<input type="text" class="w100-per mt-5" placeholder="직접입력" id="email2" readonly>
				</dd>
			</dl>
			<dl>
				<dt><span class="required">제목</span></dt>
				<dd><input type="text" class="w100-per" placeholder="제목 입력(필수)" id="up_subject" name="up_subject"></dd>
			</dl>
			<dl>
				<dt><span class="required">내용</span></dt>
				<dd><textarea class="w100-per" rows="5" placeholder="내용 입력(필수)" id="up_memo" name="up_memo"></textarea></dd>
			</dl>

			<div class="btn_area mt-35">
				<ul>
					<li><a href="javascript:SubmitContact(this.form);" class="btn-point h-input">문의하기</a></li>
				</ul>
			</div>
		</div><!-- //.board_type_write -->
		</form>
	</section><!-- //.my_deli_site -->

</main>
<!-- //내용 -->

<SCRIPT LANGUAGE="JavaScript">
<!--
function SubmitContact(){

	var email = '';
	var up_email = '';
	var up_storetel = '';

	document.storeLocWriteForm2.mode.value = "up_result";
    //reWriteName(form);
	
	if (document.storeLocWriteForm2.up_name.value.trim()==""){
		alert("작성자 이름을 입력하세요");
		document.storeLocWriteForm2.up_name.focus();  
		return;  
	}

	if (document.storeLocWriteForm2.phone_num1.value.trim()==""){
		alert("전화번호 첫번재 번호를 선택하세요");
		return;  
	} else if (document.storeLocWriteForm2.phone_num1.value.trim()!=""){
		up_storetel += document.storeLocWriteForm2.phone_num1.value.trim();
	}
	if (document.storeLocWriteForm2.phone_num2.value.trim()==""){
		alert("전화번호 중간 번호를 입력하세요");
		document.storeLocWriteForm2.phone_num2.focus();  
		return;  
	} else if (document.storeLocWriteForm2.phone_num2.value.trim()!=""){
		up_storetel += document.storeLocWriteForm2.phone_num2.value.trim();
	}
	if (document.storeLocWriteForm2.phone_num3.value.trim()==""){
		alert("전화번호 마지막 번호를 입력하세요");
		document.storeLocWriteForm2.phone_num3.focus();  
		return;  
	} else if (document.storeLocWriteForm2.phone_num3.value.trim()!=""){
		up_storetel += document.storeLocWriteForm2.phone_num3.value.trim();
	}
	
	if (document.storeLocWriteForm2.contact_email.value.trim()==""){
		alert("이메일을 입력하세요");
		document.storeLocWriteForm2.contact_email.focus();  
		return;  
	} else if(document.storeLocWriteForm2.contact_email.value.trim()!="") {
		up_email += document.storeLocWriteForm2.contact_email.value;
	}
	if (document.storeLocWriteForm2.email_select.value.trim()==""){
		email = document.getElementById("email2").value;
		if(email.trim() ==""){
			alert("이메일마지막 자리를 입력하세요");
			document.storeLocWriteForm2.email2.focus();  
			return;  
		} else if(email.trim() !="") {
			up_email += email;
		}
	} else if (document.storeLocWriteForm2.email_select.value.trim()!="") {
		up_email += document.storeLocWriteForm2.email_select.value;
	}
		
	if (document.storeLocWriteForm2.up_subject.value.trim()==""){
		alert("제목을 입력하세요");
		document.storeLocWriteForm2.up_subject.focus();  
		return;  
	}
	if (document.storeLocWriteForm2.up_memo.value.trim()==""){
		alert("내용을 입력하세요");
		document.storeLocWriteForm2.up_memo.focus();  
		return;  
	}

	document.storeLocWriteForm2.up_storetel.value = up_storetel;
	document.storeLocWriteForm2.up_email.value = up_email;
	
	var fd = new FormData($("#storeLocWriteForm2")[0]);
	
    $.ajax({
        type: "POST",
        url: "../board/board.php",
        data: fd,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
    }).success( function( data ) {
        alert('등록이 성공했습니다.');
        location.href="../m/index.htm";
    }).fail(function() {
        alert('등록이 실패했습니다.');
    });
	
	//document.storeLocWriteForm2.submit();
}
// 벨리데이션 체크 [직접입력] 시 입력가능
function EmailChange (){
	var email_select = document.getElementById("email_select").value;
	if(email_select == ''){
		document.getElementById("email2").readOnly = false;
	} else {
		document.getElementById("email2").readOnly = true;
	}
}

//-->
</SCRIPT>

<?php
include_once('outline/footer_m.php');
?>