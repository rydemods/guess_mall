<?php
include_once('./outline/header_m.php');

$sql = "SELECT agreement FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$agreement = ($row->agreement=="<P>&nbsp;</P>"?"":$row->agreement);
	$agreement = str_replace('\\','',$agreement);
}

if(ord($agreement)==0) {
	$agreement=file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement="<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td  style=\"padding:10\">{$agreement}</td></tr></table>";
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);

?>
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>이용약관</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
				<li>
					<a href="javascript:;">이용약관</a>
					<ul class="depth3">
						<li><a href="etc_agreement.php">이용약관</a></li>
						<li><a href="etc_privacy.php">개인정보취급방침</a></li>
						<li><a href="etc_email.php">이메일 무단 수집거부</a></li>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="wrap_privacy">
		<?=$agreement?>
	</section><!-- //.wrap_privacy -->

</main>
<!-- //내용 -->

<?php
include_once('./outline/footer_m.php');
?>