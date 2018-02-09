<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

	$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

	$mail_chk='';
	if(!$CertificationData->ipin_check  && !$CertificationData->realname_check){
		$mail_chk="checked";
	}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<script>

	function ipin_chk(ipin){
		document.dinfo.value=ipin;
		document.findform.submit();
	}

	function go_submit(){
		chk_return = 0;
		type = $("input[name='confirm_type']:checked").val();

		if(!type){
			alert("인증 방식을 선택해주세요.");
			return;
		}

		$('.required').each(function(){

			if($(this).val()==''){
				alert("["+$(this).attr("alt")+"] 필수사항 입니다.");
				chk_return = 1;
				$(this).focus();
				return false;
			}
		});

		if(chk_return==0){
			if(type==1){
				document.findform.submit();
			}else if(type==2){
				alert('chkeck');
				document.getElementById("ifrmHidden").src='./checkplus/checkplus_main.php';
			}else if(type==3){
				alert('ipin');
				document.getElementById("ifrmHidden").src='./ipin/ipin_main.php';
			}else{
				alert('인증 방식을 선택해주세요.');
			}
		}

	}
</script>
<?
$page_code = "find_id_pw";
?>
<!-- header 끝-->
<!-- 상세페이지 -->
<form name="findform" action="find_idpw_indb.php" method="post">
<input type="hidden" name="mode" value="findpw">
<input type="hidden" name="dinfo">

<?
$subTop_flag = 3;
//include ($Dir.MainDir."sub_top.php");
?>
<div class="containerBody sub_skin">
	<div class="left_lnb">
		<?
		/* lnb 호출 */
		$lnb_flag = 4;
		include ($Dir.MainDir."lnb.php");
		?>
	</div>

	<div class="right_section">

		<h3 class="title">
			비밀번호 찾기
			<p class="line_map"><a>Member</a> &gt; <a class="on">비밀번호 찾기</a></p>
		</h3>

		<!-- 로그인영역 -->
		<div class="login_area">
			<ul class="login_tab hide">
				<li<?=$sel_on["member"]?>><a href="javascript:loginTab(0);">회원 로그인</a></li>
				<li<?=$sel_on["nonmember"]?>><a href="javascript:loginTab(1);">비회원 조회하기</a></li>
			</ul>

			<div class="table_form">
			<div class="table_style">
			<table width=100% cellpadding=0 cellspacing=0 border=0 class="login_form">
				<colgroup>
					<col width="20%" /><col width="" />
				</colgroup>
				<caption>비밀번호를 잊으셨나요?</caption>
				<tr>
					<th>인증수단</th>
					<td>
						<input type="radio" name="confirm_type" id="confirm_type3" value="1" <?=$mail_chk?>/>가입한 이메일로 인증<br />

						<?if($CertificationData->ipin_check){?>
						<input type="radio" name="confirm_type" id="confirm_type2" value="3" />
						<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="confirm_type2">아이핀 인증</LABEL><br />
						<?}?>
						<?if($CertificationData->realname_check){?>
						<input type="radio" name="confirm_type" id="confirm_type1" value="2" />
						<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="confirm_type1">휴대폰 인증</LABEL>
						<?}?>
					</td>
				</tr>
				<tr>
					<th>이름</th>
					<td><input type="text" name="name" id="name" style="width:188px" class="txt required" alt="이름"/></td>
				</tr>
				<tr>
					<th>아이디</th>
					<td><input type="text" name="id" id="id" style="width:188px" class="txt required input_id" alt="아이디"/></td>
				</tr>
				<tr>
					<th>휴대전화</th>
					<td>
						<select name="mobile[]" id="mobile1" class="required" alt="휴대전화">
						<option value="010" selected="selected">010&nbsp;&nbsp;&nbsp;</option>
						<option value="011">011</option>
						<option value="016">016</option>
						<option value="017">017</option>
						<option value="018">018</option>
						<option value="019">019</option>
						</select>
						- <input type="text" name="mobile[]" id="mobile2" maxlength="4" size="4" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="txt input numberOnly required"  alt="휴대전화"/>
						- <input type="text" name="mobile[]" id="mobile3" maxlength="4" size="4" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="txt input numberOnly required"  alt="휴대전화"/>
					</td>
				</tr>
				<tr>
					<th>이메일</th>
					<td><input type="text" name="email" id="email" style="width:188px" class="txt required" alt="이메일"/></td>
				</tr>
			</table>
			</div>
			</div>

			<div class="ta_c mt_20">
				<a href="javascript:go_submit();" class="btn_D on">비밀번호 찾기</a>
				<a href="findid.php" class="btn_D">아이디 찾기</a>
				<a href="login.php" class="btn_D">로그인 하기</a>
			</div>
		</div><!-- //로그인영역 -->

		</div>

</div><!-- //container -->

</form>


<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
