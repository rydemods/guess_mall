<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$filePATH	= $Dir.DataDir."shopimages/overseadelivery/";
	$vimage1	= "";
	if (file_exists($filePATH."overseadelivery1.gif")) $vimage1 ="overseadelivery1.gif";
	if (file_exists($filePATH."overseadelivery1.jpg")) $vimage1 ="overseadelivery1.jpg";
	if (file_exists($filePATH."overseadelivery1.png")) $vimage1 ="overseadelivery1.png";
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<!-- start contents -->
<div class="containerBody sub_skin">
<?if($vimage1) {?>
	<center><img src='<?=$filePATH.$vimage1?>' usemap="#Map" border="0"></center>
	<map name="Map">
	  <area shape="rect" coords="261,540,593,601" <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="http://jetdream.kr/" target="_blank"<?}?>>
	</map>
<?}?>
</div>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
