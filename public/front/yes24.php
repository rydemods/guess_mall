<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<!-- start contents -->
<div class="yes24-point">
	<div class="inner"><a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="http://www.yes24.com/?PID=196126" target="_blank"<?}?> class="go-yes24">예스24 바로가기</a></div>
</div>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
