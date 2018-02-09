<?php 

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>
<script type="text/javascript">
function changeSite(){
	var chageURL = "";
	switch($("#change_site").val()){
		case '0' :
			chageURL = "#";
			break;
		case '1' :
			chageURL = "http://www.xngolf.com";
			break;
		default :
			chageURL = "#";
			break;
	}
	window.location.href = chageURL;
	
}
</script>

		<!-- //footer_wrap -->
		<div class="footer_wrap">
			
			<div class="util_line_wrap">
				<div class="util_menu">
					<ul class="list">
						<li><a href="privacy.php">개인정보 처리방침</a></li>
						<li><a href="agreement.php">이용약관</a></li>
					</ul>
					<p class="cs_num">고객센터 070-7621-6556</p>
				</div>
			</div>
			<div class="copy_wrap">
				<div class="copy">
					<div class="foot_logo"><a href=""><img src="../img/common/footer_logo.gif" alt="풋터로고" /></a></div>
					<div class="address">
						<ul>
							<li>(주)엑스넬스 코리아 경기도 하남시 창우동 250-5번지 2층</li>
							<li>Copyright ⓒ 2014 by xnells All rights reserved.</li>
						</ul>
					</div>
					<div class="family_site">
						<select name="" id="change_site" onchange="changeSite();">
							<option value="0">Family site</option>
							<option value="1">Xnells moll</option>
						</select>
					</div>
				</div>
			</div>

		</div><!-- //footer_wrap -->