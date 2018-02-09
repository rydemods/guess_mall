		</main>
		<!-- // 내용 -->

<?
    $isApp = get_session("ACCESS");
	if(strstr($_SERVER['HTTP_USER_AGENT'], 'RUNNING_IN_APP')){
		$isApp = "app";
	}
	$companynum=str_replace($arcompa,$arcomre,$_data->companynum);
	if(strlen($companynum)==13) {
		$companynum=substr($companynum,0,6)."-*******";
	} else {
		$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
	}
?>

<!-- 푸터 -->
<footer id="footer" <?if($isApp == "app") {?> class='app'<?}?>>
	<div class="sns">
		<span class="">SHARE YOUR STYLE</span>
		<a href="https://www.facebook.com/HOTTofficial" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_facebook.png" alt="facebook"></a>
		<a href="https://www.instagram.com/hott_official/" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_instagram.png" alt="instagram"></a>
		<a href="javascript:void(0);" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_youtube.png" alt="youtube"></a>
	</div>

	<div class="footer-info">
		<div class="terms">
			<a href="company.php">회사소개</a>
			<a href="<?=$Dir.MDir?>store.php">매장안내</a>
			<a href="#">개인정보취급방침</a>
			<a href="#">이용약관</a>
			<a href="#">고객센터</a>
		</div>
		
		<address><?=$_data->companyname?>  대표자(성명) : <?=$_data->companyowner?><br>
		사업장 소재지 : <?=$_data->info_addr?><br>
		대표번호: <?=$_data->info_tel?>  사업자 등록번호 안내 : <?=$companynum?><br>
		개인정보관리책임자 : <?=$_data->privercyname?>  통신판매업 신고 : <?=$_data->reportnum?><br>
		<span class="copyright">COPYRIGHT&copy;2017 <?=$_data->shopname?>. ALL RIGHTS RESERVED.</span></address>

		<div class="service_check">
			<a href="javascript:window.open('https://pg.nicepay.co.kr/issue/IssueEscrow.jsp?Mid=&CoNo=<?=$_data->companynum?>','escrowHelp','width=500,height=380,scrollbars=auto,resizable=yes');" target="_blank">에스크로 서비스 가입 확인</a>
			<a href="javascript:openBizInfo('[ORIBIZNUM]');" class="ml-5" target="_blank">사업자정보확인</a>
		</div>
	
		<div class="app-down">
			<a href="javascript:void(0);" target="_blank"><img src="/sinwon/m/static/img/btn/btn_appstore.gif" alt="App Store"></a>
			<a href="javascript:void(0);" class="ml-5" target="_blank"><img src="/sinwon/m/static/img/btn/btn_googleplay.gif" alt="Google Play"></a>
		</div>

	</div>
</footer>
<?php include_once("footer_script.php") ?>
<?php //include_once($Dir.LibDir."bottom_marketing_script_ace_counter.php") ?>
</body>

</html>
