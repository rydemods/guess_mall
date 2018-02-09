<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	/*if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}*/

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<div id="contents" >
	<div class="containerBody sub-page" >
		
		<div class="breadcrumb">
			<ul>
				<li><a href="/">HOME</a></li>
				<li class="on"><a>C.A.S.H</a></li>
			</ul>
		</div>

		<div class="store-info-wrap">
			<img src="../static/img/common/cash_intro.jpg" alt="C.A.S.H 소개">
		</div>

	</div>
</div>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
