<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>push 발송타겟</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

$( document ).ready(function() {
	$(".btn_center_ing").hide();
	$(".btn_center_ing").css("text-align", "center");

	var searchStart = $("input[name='search_start']", opener.document).val();
	var searchEnd = $("input[name='search_end']", opener.document).val();

	var pushSendType = $(".pushSendTypeClass:checked", opener.document).val();
	var loadMemberCount = $(".loadMemberCount", opener.document).html();
	var pushSendDay = $("input[name='push_send_day']", opener.document).val();
	var pushSendHour = $("select[name='push_send_hour']", opener.document).val();
	var buyType = $("input[name='buyType']", opener.document).val();
	var buyTypeArray = new Array(7);
	buyTypeArray[0] = "전체";
	buyTypeArray[1] = "오늘";
	buyTypeArray[2] = "7일";
	buyTypeArray[3] = "14일";
	buyTypeArray[4] = "한달";

	var url = "";


	// 검색 된 회원 수
	$(".popMemberCount").html(loadMemberCount);
	$(".popBuyType").html(buyTypeArray[buyType]);

	// 즉시 발송은 날짜 노출 X
	if(pushSendType == "n"){
		$(".date").hide();
	}else if(pushSendType == "r"){
		$(".date").show();
		$(".popSendDay").html(pushSendDay);
		$(".popSendHour").html(pushSendHour);
	}

	var memSearchType = $("input[name='mem_search_type']:checked", opener.document).val();
	if(memSearchType == 'a'){
		$(".memSearchTypeClass").html("일반 회원");
	}else{
		$(".memSearchTypeClass").html("구매이력 회원");
	}

/*
	$('.frmTab1Class', opener.document).on('submit',(function(e) {
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			type:'POST',
			url: $(this).attr('action'),
			data:formData,
			cache:false,
			contentType: false,
			processData: false,
			success:function(data){
				var arrReturn = data.split("::::");
				if(arrReturn[1] == "0"){
					alert(arrReturn[0]);
					window.close();
				}else{
					alert(arrReturn[0]);
					$.ajax({
						type: "POST",
						url: "./market_app_push_indb.php",
						data: "mode=sendAjaxPush&no=" + arrReturn[1],
						beforeSend: function () {
							// 구글과 통신 하여 PUSH 발송.
							$(".btn_center").hide();
							$(".btn_center_ing").show();
						}
					}).done(function ( succCnt ) {
						if(succCnt > 0){
							alert("전송이 완료되었습니다.");
							opener.location.reload();
							window.close();
						}else if(succCnt == 'n'){
							opener.location.reload();
							window.close();
						}else{
							alert("전송이 실패 했습니다.");
							$(".btn_center").show();
							$(".btn_center_ing").hide();
						}
					});
				}
			},
			error: function(data){
				alert("등록에 실패했습니다.");
			}
		});
	}));
*/



	$(".popSubmit").bind('click', function() {
		$(".onSubmitHidden", opener.document).click();
	});

	/*
	$(".popSubmit").bind('click', function() {
		$.ajax({
			type: "POST",
			url: "./market_app_push_indb.php",
			data: $(".frmTab1Class", opener.document).serialize(),
			beforeSend: function () {
			}
		}).done(function ( data ) {
			var arrReturn = data.split("::::");
			if(arrReturn[1] == "0"){
				alert(arrReturn[0]);
			}else{
				$.ajax({
					type: "POST",
					url: "./market_app_push_indb.php",
					data: "mode=sendAjaxPush&no=" + arrReturn[1],
					beforeSend: function () {
					}
				}).done(function ( succCnt ) {
					alert(succCnt);
				});
			}
		});

		//$(".frmTab1Class", opener.document).submit();
		//window.close();
	});
	*/

	$(".popCancel").bind('click', function() {
		window.close();
	});
});
//-->
</SCRIPT>

</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>push 발송타겟</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>push 발송타겟</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">

	<div class="push_pop">
		<ul>
			<!-- <li>회원선택 : <span>사용자 전체</span></li>   -->
			<li><span class = "memSearchTypeClass">구매이력 회원</span> : <span class = "popBuyType">오늘</span></li>
		</ul>
		<div class="txt">
			<span class="blue bold popMemberCount">0</span>건<br />
			<p class="date"><span class = 'popSendDay'>YYYY-MM-DD</span> /<span class = 'popSendHour'>00:00</span></p>
			Push를 발송하겠습니까?
		</div>
		<div class="btn_center">
			<a href = "javascript:;" class = "popSubmit"><img src="images/btn_send.gif" alt="발송"/></a>
			<a href = "javascript:;" class = "popCancel"><img src="images/btn_cancel2.gif" alt="취소"/></a>
		</div>
		<div class="btn_center_ing">
			전송중입니다.<br>다소 시간이 소요될수 있습니다.
		</div>
	</div>
	<!-- 높이 210px -->
</body>
