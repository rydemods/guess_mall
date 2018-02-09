<?php // hspark
$Dir="../";
include("header.php");
//테스트 할때 폼 출력하려면 값이 없어야 함
@include_once($Dir."data/BankdaConfig.php");

//http://도메인주소/bankda/sync.php를 크론에서 항상 돌도록 서버 셋팅
?>
<!-- <script type="text/javascript" src="lib.js.php"></script> -->
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
	function openIDcheck() {
		var idval = document.getElementById('join_frm').user_id.value;
		window.open("https://ssl.bankda.com/partnership/user/id_check.php?Memid=" + idval + "&service_type=standard","idCheck","width=360,height=200,top="+window.event.y+",left="+window.event.x+",toolbar=no") 
	}	
	
	function dosubmit() {
		var frm = document.getElementById('join_frm');
		
		if(frm.user_name.value == "") {
			alert("이용자이름을 입력하세요.");
			frm.user_name.focus();
			return false;
		}
		if(frm.user_id.value == "") {
			alert("이용자ID를 입력하세요.");
			frm.user_id.focus();
			return false;
		}
		if(frm.user_pw.value == "") {
			alert("이용자PW를 입력하세요.");
			frm.user_pw.focus();
			return false;
		}
		if(frm.user_pw.value != frm.user_pw2.value) {
			alert("이용자PW를 확인하세요.");
			frm.user_pw2.focus();
			return false;
		}
		if(frm.user_tel.value == "") {
			alert("전화번호를 입력하세요.");
			frm.user_tel.focus();
			return false;
		}
		if(frm.user_email.value == "") {
			alert("email정보를 입력하세요.");
			frm.user_email.focus();
			return false;
		}
		if(frm.id_ck.value == "0") {
			alert("ID중복체크를 실행하세요.");
			frm.user_id.focus();
			return false;
		}
	}

	$(document).ready(function(){
		$('.onSubmitBtn222').click(function(){
			alert(document.getElementsByName('ifrmHidden')[0].contentWindow.document.body );
		})


		$('.hackBankda').click(function(){
			if(confirm("뱅크다 결제 서비스를 해지 하시겠습니까?")){
				$.ajax({
					type: "POST",
					url: "bankda_hack_proc.php",
					data: $('#join_frm').serialize(),
					beforeSend: function () {}
				}).done(function ( data ) {
					if(data == 'Err'){
						alert("서비스 해지에 실패 하셨습니다.");
					}else if(data == 'Ok'){	
						alert("서비스를 정상적으로 해지 하셨습니다.");
						document.location.reload();
					}else{	
						alert(data);
					}
					return false;
				});
			}
		})

		$('.onSubmitBtn').click(function(){
			dosubmit();

			$.ajax({
				type: "POST",
				url: "bankda_proc.php",
				data: $('#join_frm').serialize(),
				beforeSend: function () {}
			}).done(function ( data ) {
				if(data == 'Err'){
					alert("서비스 등록에 실패 하셨습니다.");
				}else if(data == 'Ok'){	
					alert("서비스를 정상적으로 등록 하셨습니다.");
					document.location.reload();
				}else{	
					alert(data);
				}
				return false;
			});
		})
	})
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 자동입금 확인 &gt;<span>뱅크다 결제 서비스</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">뱅크다 결제 서비스</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub">
						<?if($cfgBankda[user_id]){?>
							<script language="JavaScript">
								$(document).ready(function(){
									$(".bankdaOpen").click(function(){
										var user_id = $("#ID_user_id").val();
										var user_pw = $("#ID_user_pw").val();
										window.open("https://ssl.bankda.com/partnership/user/user_login.php?job_type=service&partner_id=duometis&user_id="+user_id+"&user_pw="+user_pw+"&service_type=standard","bankda");
									})
									//$(".bankdaOpen").trigger( "click" );
								})
							</script>
							<input type="hidden" id = 'ID_user_id' value="<?=$cfgBankda[user_id]?>">
							<input type="hidden" id = 'ID_user_pw' value="<?=$cfgBankda[user_pw]?>">
							
							
							<span>팝업창에서 뱅크다 결제가 가능합니다.  <a href="javascript:;" class = "bankdaOpen"><strong>[<?=$cfgBankda[user_id]?>님의 뱅크다 결제페이지 열기]</strong></a></span><br><br>
							<span><a href = "javascript:;" class = "hackBankda"><strong>[뱅크다 서비스 해지 하기]</strong></a></span>
							
							
							<form name="join_frm" id="join_frm" method="post" target = 'ifrmHidden'>
								<input type="hidden" name="directAccess" value="y">
								<input type="hidden" name="service_type" value="standard">
								<input type="hidden" name="partner_id" value="duometis">
								<input type="hidden" name="user_id" value="<?=$cfgBankda[user_id]?>">
								<input type="hidden" name="user_pw" value="<?=$cfgBankda[user_pw]?>">
								<input type="hidden" name="command" value="excute">
							</form>
						<?}else{?>
							<div class="table_style01">
								<!--form name="join_frm" id="join_frm" method="post" action=""-->
								<form name="join_frm" id="join_frm" method="post" target = 'ifrmHidden'>
									<input type="hidden" name="directAccess" value="y">
									<input type="hidden" name="service_type" value="standard">
									<input type="hidden" name="partner_name" id="partner_name" class="form" value="duometis">
									<input type="hidden" name="partner_id" id="partner_id" class="form" value="duometis">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<th><span>이용자이름</span></th>
											<td class="td_con1">
												<input type = "text" name="user_name" id = "user_name" size="20" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input">
											</td>
										</tr>
										<tr>
											<th><span>이용자자ID</span></th>
											<td class="td_con1">
												<input type = "text" name="user_id" id = "user_id" size="20" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input">
												&nbsp;
												<!--input type="button" value="ID체크" onclick="openIDcheck();"--><input type="hidden" name="id_ck" value="1">
											</td>
										</tr>
										<tr>
											<th><span>이용자PW</span></th>
											<td class="td_con1">
												<input type = "password" name="user_pw" id = "user_pw" size="20" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input">
											</td>
										</tr>
										<tr>
											<th><span>이용자PW 확인</span></th>
											<td class="td_con1">
												<input type = "password" name="user_pw2" id = "user_pw2" size="20" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input">
											</td>
										</tr>
										<tr>
											<th><span>전화번호</span></th>
											<td class="td_con1">
												<input type = "text" name="user_tel" id = "user_tel" size="20" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input">
											</td>
										</tr>
										<tr>
											<th><span>email</span></th>
											<td class="td_con1">
												<input type = "text" name="user_email" id = "user_email" size="50" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input">
											</td>
										</tr>
									</table>
									<br>
									<input type="button" value="등록" style="width:100px;height:30px;" class = 'onSubmitBtn'>
								</form>
							</div>
						<?}?>
					</div>
				</td>
			</tr>
			
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<iframe name="ifrmHidden" width=10 height=10 style = 'display:none;'></iframe>
	<div style="height:200px"></div>
<?=$onload?>
<?php
include("copyright.php");
