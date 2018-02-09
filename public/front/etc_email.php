<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="sub-page">

		<article class="sub-wrap">
			<header class="subPage-title"><h2>이메일 무단 수집거부</h2></header>
			<section class="agreement-pack">
				<h3 class="v-hidden">이메일 무단 수집거부 페이지</h3>
				<div class="tabs three"> 
					<a href="etc_agreement.php"><span>이용약관</span></a>
					<a href="etc_privacy.php"><span>개인정보취급방침</span></a>
					<a href="etc_email.php" class="active"><span>이메일 무단 수집거부</span></a>
				</div>
				<div class="frm-box">
					<p>본 웹사이트에 게시된 이메일 주소가 전자우편 수집 프로그램이나 그 밖의 기술적 장치를 이용하여 무단으로 수집되는 것을 거부하며, 이를 위반시 정보통신망법의해 형사처벌됨을 유념하시기 바랍니다. 이메일을 기술적 장치를 사용하여 무단으로 수집, 판매·유통하거나 이를 이용한 자는 [정보통신망이용 촉진및정보보호등에관한법률] 제50조의2 규정에 의하여 1천만원 이하의 벌금형에 처해집니다. </p>
					<dl>
						<dt>정보통신망법 제50조의2 (전자우편주소의 무단 수집행위 등 금지)</dt>
						<dd>- 누구든지 전자우편주소의 수집을 거부하는 의사가 명시된 인터넷 홈페이지에서 자동으로 전자 우편주소를 수집하는 프로그램 그 밖의 기술적 장치를 이용하여 전자우편주소를 수집하여서는 아니 된다. </dd>
						<dd>- 누구든지 제1항의 규정을 위반하여 수집된 전자우편주소를 판매·유통하여서는 아니된다.</dd>
						<dd>- 누구든지 제1항 및 제2항의 규정에 의하여 수집·판매 및 유통이 금지된 전자우편주소임을 알고 이를 정보전송에 이용하여서는 아니 된다.</dd>
					</dl>
				</div>
			</section>
		</article>

	</div>
</div><!-- //#contents -->

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>