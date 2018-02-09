<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<?php
include ($Dir.MainDir.$_data->menu_type.".php");
$page_code = "benefit";
/* lnb 호출 */
/*
$page_code = "default";
$lnb_flag = 1;
include ($Dir.MainDir."lnb.php"); 
*/
?>
<div id="contents" >
	<div class="containerBody sub-page" >
		
		<div class="breadcrumb">
			<ul>
				<li><a href="/">HOME</a></li>
				<li><a href="mypage.php">MY PAGE</a></li>
				<li class="on"><a>회원탈퇴</a></li>
			</ul>
		</div>
		
		<!-- LNB -->
		<div class="left_lnb">
			<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
		</div><!-- //LNB -->

		<div class="right_section mypage-content-wrap">

			<form>
				<fieldset>
					<legend>회원탈퇴를 신청하기전 정보보호를 위해 비밀번호 재확인 입력</legend>
					<div class="memberout-login">
						<div class="inner">
							<p class="ment">회원님의 소중한 정보보호를 위해<br>비밀번호를 재확인하고 있습니다.</p>
							<ul>
								<li><label>아이디</label><input type="text" readonly value="hong1334"></li>
								<li><label for="pwd">비밀번호</label><input type="password" id="pwd" class="input-def"></li>
							</ul>
						</div>
						<div class="btn-place">
							<button class="btn-dib-function" type="submit"><span>확인</span></button>
							<a href="#" class="btn-dib-function line" ><span>취소</span></a>
						</div>
					</div>
				</fieldset>
			</form>

		</div><!-- //.right_section -->

	</div>
</div>

<?php
include ($Dir."lib/bottom.php") 
?>

</BODY>
</HTML>
