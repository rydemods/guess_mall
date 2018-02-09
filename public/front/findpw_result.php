<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?
$page_code = "find_id_pw";
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

		<div class="login_wrap">
			<div class="idpw_find_result mt_20">
				<dl>
					<dt>이메일을 확인해 주세요</dt>
					<dd>- 가입하신 이메일로 임시 비밀번호가 발송되었습니다.</dd>
					<dd>- 발급된 비밀번호는 임시 비밀번호이므로 로그인 후 반드시 변경하시기 바랍니다. </dd>
				</dl>
			</div>
			
			<div class="ta_c mt_20"><a href="login.php" class="btn_D on">로그인 하기</a> <a href="/" class="btn_D">메인으로 이동</a></div>
		</div>

	</div>

</div><!-- //container -->



<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
