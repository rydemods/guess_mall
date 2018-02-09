<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "store_import";
$class_on['store_import'] = " class='active'";
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">입점문의</h2>

		<div class="inner-align page-frm clear">
		
			<?php 
				$lnb_flag = 5;
				include ($Dir.MainDir."lnb.php");
			?>
	
			<article class="cs-content layer-contact-us">
				<h2 class="v-hidden">입점문의하기</h2>
				<section>
					<header class="my-title">
						<h3 class="v-hidden">입점문의</h3>
					</header>
					<form id="storeLocWriteForm2" name="storeLocWriteForm2" method="post" action="/board/board.php">
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
						<table class="th-left">
							<caption>입점문의</caption>
							<colgroup>
								<col style="width:144px">
								<col style="width:auto">
							</colgroup>
							<tbody>
								<tr>
									<th scope="row"><label for="contact_name" class="essential">작성자</label></th>
									<td><div class="input-cover"><input type="text" style="width:175px" title="작성자 입력" id="up_name" name="up_name"></div></td>
								</tr>
								<tr>
									<th scope="row"><label class="essential">휴대폰 번호</label></th>
									<td>
										<div class="input-cover">
											<div class="select">
												<select style="width:110px" id="phone_num1">
													<option value="">선택</option>
													<option value="010">010</option>
													<option value="011">011</option>
													<option value="016">016</option>
													<option value="017">017</option>
													<option value="018">018</option>
													<option value="019">019</option>
												</select>
											</div>
											<span class="txt">-</span>
											<input type="text" title="휴대폰 가운데 번호 입력" style="width:110px" id="phone_num2">
											<span class="txt">-</span>
											<input type="text" title="휴대폰 마지막 번호 입력" style="width:110px" id="phone_num3">
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="contact_email" class="essential">이메일</label></th>
									<td>
										<div class="input-cover">
											<input type="text"  style="width:175px" title="이메일 입력" id="contact_email" name="contact_email">
											<span class="txt">@</span>
											<div class="select">
												<select style="width:175px" id="email_select" onchange="EmailChange()">
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
											<input type="text" title="도메인 직접 입력" class="ml-10" style="width:175px" id="email2" readonly> <!-- [D] 직접입력시 인풋박스 출력 -->
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="contact_title" class="essential">제목</label></th>
									<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="up_subject" name="up_subject"></div></td>
								</tr>
								<tr>
									<th scope="row"><label for="contact_textarea" class="essential">문의내용</label></th>
									<td><textarea id="up_memo" class="w100-per" style="height:272px" name="up_memo"></textarea></td>
								</tr>
							</tbody>
						</table>
						<div class="ta-c mt-40"><button class="btn-point h-large w200" type="button" onclick="javascript:SubmitContact(this.form); return false;">문의하기</button></div>
					</form>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<SCRIPT LANGUAGE="JavaScript">
<!--

function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(num) {
	location.href="customer_notice_view.php?num="+num;
}
function SubmitContact(form){

	var email = '';
	var up_email = '';
	var up_storetel = '';

    form.mode.value = "up_result";
    //reWriteName(form);
	
	if (form.up_name.value.trim()==""){
		alert("작성자 이름을 입력하세요");
		form.up_name.focus();  
		return;  
	}

	if (form.phone_num1.value.trim()==""){
		alert("전화번호 첫번재 번호를 선택하세요");
		return;  
	} else if (form.phone_num1.value.trim()!=""){
		up_storetel += form.phone_num1.value.trim();
	}
	if (form.phone_num2.value.trim()==""){
		alert("전화번호 중간 번호를 입력하세요");
		form.phone_num2.focus();  
		return;  
	} else if (form.phone_num2.value.trim()!=""){
		up_storetel += form.phone_num2.value.trim();
	}
	if (form.phone_num3.value.trim()==""){
		alert("전화번호 마지막 번호를 입력하세요");
		form.phone_num3.focus();  
		return;  
	} else if (form.phone_num3.value.trim()!=""){
		up_storetel += form.phone_num3.value.trim();
	}
	
	if (form.contact_email.value.trim()==""){
		alert("이메일을 입력하세요");
		form.contact_email.focus();  
		return;  
	} else if(form.contact_email.value.trim()!="") {
		up_email += form.contact_email.value;
	}
	if (form.email_select.value.trim()==""){
		email = document.getElementById("email2").value;
		if(email.trim() ==""){
			alert("이메일마지막 자리를 입력하세요");
			form.email2.focus();  
			return;  
		} else if(email.trim() !="") {
			up_email += email;
		}
	} else if (form.email_select.value.trim()!="") {
		up_email += form.email_select.value;
	}
		
	if (form.up_subject.value.trim()==""){
		alert("제목을 입력하세요");
		form.up_subject.focus();  
		return;  
	}
	if (form.up_memo.value.trim()==""){
		alert("내용을 입력하세요");
		form.up_memo.focus();  
		return;  
	}

	form.up_storetel.value = up_storetel;
	form.up_email.value = up_email;
	
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
        location.href="../../index.htm";
    }).fail(function() {
        alert('등록이 실패했습니다.');
    });
	
	//form.submit();
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

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>




