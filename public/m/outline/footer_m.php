<?php
/*********************************************************************
// 파 일 명		: bottom.php
// 설     명		: 하단 템플릿
// 상세설명	: 하단 ( INFOMATION, CONTACT INFO, HELP DESK) 템플릿
// 작 성 자		: 2016.01.14 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
// 수 정 자		: 2017.01.20 - 위민트
//
*********************************************************************/
?>
<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$companynum="";

/*$bottom_body = "
	<address>
		<p>
			상호 [COMPANYNAME]([NAME]) &nbsp; 전화 : [TEL] &nbsp; 팩스 : [FAX] &nbsp; <a href=\"mailto:[INFOMAIL]\">이메일 : [INFOMAIL]</a> &nbsp; 사업자등록번호 : [BIZNUM] <a href=\"javascript:openBizInfo('[ORIBIZNUM]');\">[사업자정보확인]</a><br>
			통신판매업신고 : [SALENUM] &nbsp; 대표이사 [OWNER], &nbsp; 개인정보책임자 [PRIVERCY] &nbsp; 주소 : [ADDRESS]
		</p>
		<p>COPYRIGHT(C)[NAME]. ALL RIGHTS RESERVED.</p>
	</address>";*/
//기획팀 요청으로 인해 상점명 제거(2016-10-11)
$bottom_body = "
	<address>[COMPANYNAME] 대표자(성명) : [OWNER] <br>
		사업장 소재지 : [ADDRESS] <br>
		대표번호 : [TEL] | 사업자 등록번호 안내 : [BIZNUM] <br>
		개인정보관리책임자 : [PRIVERCY] | 통신판매업 신고 : [SALENUM] <br>
		<span class='copyright'>COPYRIGHT&copy;2017 [NAME]. ALL RIGHTS RESERVED.</span>
	</address>";

$arcompa=array("-"," ",".","_",",");
$arcomre=array("", "", "", "", "");
$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

if(strlen($companynum)==13) {
	$companynum=substr($companynum,0,6)."-*******";
} else {
	$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
}
$bottom_body=str_replace("[DIR]",$Dir,$bottom_body);

$pattern=array("[SHOPTITLE]","[URL]","[NAME]","[TEL]","[FAX]","[INFOMAIL]","[COMPANYNAME]","[ORIBIZNUM]","[BIZNUM]","[SALENUM]","[OWNER]","[PRIVERCY]","[ADDRESS]","[HOME]","[USEINFO]","[BASKET]","[COMPANY]","[ESTIMATE]","[BOARD]","[AUCTION]","[GONGGU]","[EMAIL]","[RESERVEVIEW]","[LOGIN]","[LOGOUT]","[PRIVERCYVIEW]","[CONTRACT]","[MEMBER]","[MYPAGE]","[ORDER]","[RSS]","[PRODUCTNEW]","[PRODUCTBEST]","[PRODUCTHOT]","[PRODUCTSPECIAL]","[REGDATE]");
$replacelogin=array($_data->shoptitle,"http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,str_replace(","," / ", $_data->info_fax),$_data->info_email,$_data->companyname,$_data->companynum,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php","\"JavaScript:alert('로그인중입니다.');\"",$Dir.MainDir."top.php?type=logout","\"/front/privacy.php\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php", substr($_data->regdate,0,4));
$replacelogout=array($_data->shoptitle,"http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,str_replace(","," / ", $_data->info_fax),$_data->info_email,$_data->companyname,$_data->companynum,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."login.php?chUrl=".(isset($_REQUEST["chUrl"])?$_REQUEST["chUrl"]:""),"\"JavaScript:alert('먼저 로그인하세요.');\"","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php", substr($_data->regdate,0,4));

if (strlen($_ShopInfo->getMemid())>0) {
	$bottom_body = str_replace($pattern,$replacelogin,$bottom_body);
} else {
	$bottom_body = str_replace($pattern,$replacelogout,$bottom_body);
}
// lib -> bottom.php 참조
?>

<!-- 공통 적용 스크립트 , 모든 페이지에 노출되도록 설치. 단 전환페이지 설정값보다 항상 하단에 위치해야함 --> 
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"> </script> 
<script type="text/javascript"> 
if (!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_4c8895fe304e";
if (!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>

<script type="text/javascript">
function openBizInfo(bizNo){
	// 1058111908
//	var url="http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no="+bizNo;
	var url="http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=1058111908";
	window.open(url,"communicationViewPopup","width=750, height=700;");
}

function new_open (url){
// 	window.open(url);
	location.href = url;
}

</script>

<script type="text/javascript">
<!--
	function mobRf(){
  		var rf = new EN();
		rf.setSSL(true);
  		rf.sendRf();
	}
  //-->
</script>
<script async="true" src="https://cdn.megadata.co.kr/js/enliple_min2.js" onload="mobRf()"></script>

<div class="quick_btn_wrap">
	<a href="javascript:;" class="top_btn"><img src="/sinwon/m/static/img/btn/btn_top.gif" alt="페이지 상단 바로가기"></a>
	<a href="javascript:history.back();" class="prev_btn"><img src="/sinwon/m/static/img/btn/btn_prev.gif" alt="이전 페이지로 가기"></a>
</div>

<!-- 푸터 -->
		<footer id="footer">
			<div class="sns">
				<span>SHARE YOUR STYLE</span>
				<!-- 
				<a onclick="new_open('https://www.facebook.com/mall.shinwon.9');" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_facebook.png" alt="facebook"></a>
				 -->
				<a onclick="new_open('https://www.instagram.com/shinwonmall/');" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_instagram.png" alt="instagram"></a>
				<a onclick="new_open('https://www.youtube.com/channel/UCITkKbSvb3hjm8rTeLW1Jcw/feed');" target="_blank"><img src="/sinwon/m/static/img/icon/icon_ft_youtube.png" alt="youtube"></a>
			</div>

			<div class="footer-info">
				<div class="ft_menu">
					<a onclick="new_open('http://www.sw.co.kr');">회사소개</a>
					<a href="<?=$Dir.MDir?>storeList.php">매장안내</a>
					<a href="<?=$Dir.MDir?>customer_grade.php">멤버쉽 안내</a>
					<a href="<?=$Dir.MDir?>customer_notice.php">고객센터</a>
					<!-- <a href="/index.htm?">PC버전</a> -->
					<a href="../main/main.php">PC버전</a>
				</div>
				
				<!--  
				<address>(주) 신원  대표자(성명) : 박정주<br>
				사업장 소재지 : 서울특별시 마포구 독막로 328(도화동)<br>
				대표번호:1661-2585  사업자 등록번호 안내 : 105-81-11908<br>
				개인정보관리책임자 : 황우승  통신판매업 신고 : 제 2016-서울마포-0401호<br>
				<span class="copyright">COPYRIGHT&copy;2017 SHINWON. ALL RIGHTS RESERVED.</span></address>
				-->
				<?=$bottom_body ?>

				<div class="terms">
					<ul>
						<li><a href="<?=$Dir.MDir?>etc_privacy.php">개인정보취급방침</a></li>
						<li><a href="<?=$Dir.MDir?>etc_agreement.php">이용약관</a></li>
						<li><a href="<?=$Dir.MDir?>etc_email.php">이메일무단수집거부</a></li>
					</ul>
				</div>

				<div class="service_check">
				<!--  
					<a onclick="javascript:window.open('https://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L','KCPHelp','width=500,height=450,scrollbars=auto,resizable=yes');" target="_blank">에스크로 서비스 가입 확인</a>
					<a onclick="javascript:openBizInfo();" class="ml-5" target="_blank">사업자정보확인</a>
				-->
					<a onclick="javascript:new_open('https://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L');" target="_blank">에스크로 서비스 가입 확인</a>
					<a onclick="javascript:new_open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=1058111908');" class="ml-5" target="_blank">사업자정보확인</a>
				</div>
			<!--  
				<div class="app-down">
					<a href="#" target="_blank"><img src="static/img/btn/btn_appstore.gif" alt="App Store"></a>
					<a href="#" class="ml-5" target="_blank"><img src="static/img/btn/btn_googleplay.gif" alt="Google Play"></a>
				</div>
			-->

			</div>
		</footer>
		<!-- //푸터 -->

	</div><!-- //#page -->

<!-- WIDERPLANET  SCRIPT START 2017.9.18 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	/*Cross device targeting을 원하는 광고주는 로그인한 사용자의 Unique ID (ex. 로그인 ID, 고객넘버 등)를 암호화하여 대입.
				 *주의: 로그인 하지 않은 사용자는 어떠한 값도 대입하지 않습니다.*/
		ti:"37370",	/*광고주 코드*/
		ty:"Home",	/*트래킹태그 타입*/
		device:"mobile"	/*디바이스 종류 (web 또는 mobile)*/
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2017.9.18 -->
<!-- A Square|Site Analyst WebLog for Mobile WebSite Emission Script v1.1 -->
<script type="text/javascript">
	var _nSA=(function(_g,_c,_s,_u,_p,_d,_i,_h){if(_i.mgc!=_g){_i.mgc=_g;_i.mud=_u;_i.mrd=(new Date().getTime());
	var _sc=_d.createElement('script');_sc.src=_p+'//sas.nsm-corp.com/'+_s+'?gc='+_g+'&dn='+escape(_h)+'&rd='+_i.mrd;
	var _sm=(_d.getElementsByTagName('script')[0]).parentNode.insertBefore(_sc,_sm);_i.mgd=_c;}return _i;
})('CM4B41796218757','mgc.nsm-corp.com','sa-m.js','www.shinwonmall.com,shinwonmall.com',location.protocol,document,window._nSA||{},location.hostname);
</script>
<noscript><img src="//mgc.nsm-corp.com/mwg/?mid=CM4B41796218757&tp=noscript&ce=0&" border=0 width=0 height=0></noscript>

</body>
</html>