<?php
/*********************************************************************
// 파 일 명		: mypage_mbpasscheck_TEM_001.php
// 설     명		: 비밀번호 확인
// 상세설명	: 회원정보 수정 또는 탈퇴시 비밀번호 확인을 위한 페이지
// 작 성 자		: 2016.02.27 - 김재수
// 수 정 자		:
//
//
*********************************************************************/
?>

<?if ($menu_title_val == "modify") {?>
<SCRIPT LANGUAGE="JavaScript">
<!--


function CheckForm() {

	form=document.form1;

	//기존 비밀번호 유효성 체크
	var val	= $("input[name=oldpasswd]").val();
	if (val == '') {
		alert($("input[name=oldpasswd]").attr("title"));
	} else {
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "passwd=" + val + "&mode=passwd",
			dataType:"json",
			success: function(data) {
				$("#oldpasswd_checked").val(data.code);
				if (data.code == 0) {
					alert(data.msg);
					return;
				}

				var oldpasswd_checked	= $("input[name=oldpasswd_checked]").val();

				if (oldpasswd_checked == '1')
				{
					form.my_passwd_check.value="Y";
					form.submit();
				} else {
					return;
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
			}
		});
	}
}
//-->
</SCRIPT>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title"><?=$menu_title_text?></h2>

		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<?php
			 include ($Dir.FrontDir."mypage_TEM01_left.php");
			?>
			<!-- //LNB -->
			<article class="my-content">
				
				<div class="gray-box">
					<div class="inner-vm">
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="CheckForm();return false;" method="post">
						<input type=hidden name=oldpasswd_checked id=oldpasswd_checked value="0">
						<input type=hidden name=my_passwd_check id=my_passwd_check value="N">
						<fieldset class="pw-checkForm">
							<legend>본인확인을 위한 비밀번호 입력</legend>
							<p class="att">본인 확인을 위해 비밀번호를 입력해주세요.</p>
							<input type="password" id="pwd" name="oldpasswd" placeholder="비밀번호 입력" title="비밀번호를 입력해 주시기 바랍니다.">
							<button class="btn-point w100-per h-large" type="button" onClick="javascript:CheckForm();"><span>확인</span></button>
						</fieldset>
						</form>
					</div>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->
<?} else {?>
<SCRIPT LANGUAGE="JavaScript">
<!--


function CheckForm() {

	form=document.form1;

	//기존 비밀번호 유효성 체크
	var val	= $("input[name=oldpasswd]").val();
	var val2	= $("input[name=oldpasswdre]").val();
	if (val == '' || val2 =='') {
		if (val == '') {
			alert($("input[name=oldpasswd]").attr("title"));
			return;
		} else if (val2 == '') {
			alert($("input[name=oldpasswdre]").attr("title"));
			return;
		}
	} else {
		if (val != val2) {
			alert("비밀번호가 일치하지 않습니다.");	
			return;
		} else {
			$.ajax({
				type: "GET",
				url: "<?=$Dir.FrontDir?>iddup.proc.php",
				data: "passwd=" + val + "&mode=passwd",
				dataType:"json",
				success: function(data) {
					if (data.code == 0) {
						alert(data.msg);
						return;
					} else if (data.code == '1') {
						if (document.form1.t_order_cnt.value == 0)
						{	
							if(confirm("회원탈퇴를 신청하시겠습니까?")) {
								document.form1.type.value="exit";
								document.form1.submit();
							}
							return;
						} else {
							alert("진행중인 주문이 완료 되어야 탈퇴처리 가능하십니다.");
							return;
						}
					} else {
						return;
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
				}
			});
		}
	}
}
//-->
</SCRIPT>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title"><?=$menu_title_text?></h2>

		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<?php
			 include ($Dir.FrontDir."mypage_TEM01_left.php");
			?>
			<!-- //LNB -->
			<article class="my-content">
				
				<div class="gray-box">
					<div class="member-out">
						<dl>
							<dt>신원몰 회원탈퇴 유의사항</dt>
							<dd>- 적립된 포인트 및 쿠폰정보는 모두 소멸됩니다.</dd>
							<dd>- 동일 아이디로 재가입이 불가능합니다.</dd>
						</dl>
					</div>
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="CheckForm();return false;" method="post">
						<input type=hidden name=type>
						<input type=hidden name=t_order_cnt value="<?=$t_order_cnt?>">
						<input type=hidden name=mem_id value="<?=$_ShopInfo->getMemid()?>">
						<input type=hidden name=memoutinfo value="<?=encrypt_md5(($t_product_sale_cnt+$t_coupon_sale_cnt)."|".$_mdata->act_point."|".$_mdata->reserve."|".$_mdata->name."|".$_ShopInfo->getMemid())?>">
						<fieldset class="pw-checkForm">
							<legend>회원탈퇴를 위한 비밀번호 확인</legend>
							<p class="att">회원탈퇴를 위해 비밀번호를 입력해주세요.</p>
							<input type="password" id="pwd" name="oldpasswd" placeholder="비밀번호 입력" title="비밀번호를 입력해 주시기 바랍니다.">
							<input type="password" id="pwdre" name="oldpasswdre" title="비밀번호를 재 입력해 주시기 바랍니다." placeholder="비밀번호 재입력">
							<button class="btn-point w100-per h-large" type="button" onClick="javascript:CheckForm();"><span>확인</span></button>
						</fieldset>
						</form>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->
<?}?>