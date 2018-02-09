<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// $sql="SELECT agreement FROM tbldesign ";
// $result=pmysql_query($sql,get_db_conn());
// if($row=pmysql_fetch_object($result)) {
// 	$agreement=stripslashes($row->agreement);
// }
// pmysql_free_result($result);

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

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="sub-page">

		<article class="sub-wrap">
			<header class="subPage-title"><h2>이용약관</h2></header>
			<section class="agreement-pack">
				<h3 class="v-hidden">이용약관페이지</h3>
				<div class="tabs three"> 
					<a href="etc_agreement.php" class="active"><span>이용약관</span></a>
					<a href="etc_privacy.php"><span>개인정보취급방침</span></a>
					<a href="etc_email.php"><span>이메일 무단 수집거부</span></a>
				</div>
				<div class="frm-box">
					<?=$agreement?>
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
