<?php
	include_once('outline/header_m.php');
	
	$filePATH	= $Dir.DataDir."shopimages/overseadelivery/";
	$vimage	= "";
	if (file_exists($filePATH."overseadelivery1.gif")) $vimage ="overseadelivery1.gif";
	if (file_exists($filePATH."overseadelivery1.jpg")) $vimage ="overseadelivery1.jpg";
	if (file_exists($filePATH."overseadelivery1.png")) $vimage="overseadelivery1.png";

	if (file_exists($filePATH."overseadelivery1_m.gif")) $vimage ="overseadelivery1_m.gif";
	if (file_exists($filePATH."overseadelivery1_m.jpg")) $vimage ="overseadelivery1_m.jpg";
	if (file_exists($filePATH."overseadelivery1_m.png")) $vimage="overseadelivery1_m.png";
?>

<main id="content" class="subpage base">
<?if($vimage) {?>
	<center><a <?if(strlen($_MShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="http://jetdream.kr/" target="_blank"<?}?>><img src='<?=$filePATH.$vimage?>' border="0"></a></center>
<?}?>
</main>

<? include_once('outline/footer_m.php'); ?>