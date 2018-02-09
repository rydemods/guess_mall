<?php 
$Dir="../";
include_once($Dir."lib/init.php");

if (strpos($_SERVER['HTTP_REFERER'],"productdetail.php")==false) exit;

$productcode=$_REQUEST["productcode"];
$size=$_REQUEST["size"];
$thumbtype=$_REQUEST["thumbtype"];

if(ord($size)==0) $size=320;
else $size+=20;

$size2=70;
if($thumbtype==2) $size2=130;
?>
<html>
<head>
<title>상품확대보기</title>
</head>
<frameset rows="<?=($size+40)?>px,<?=$size2?>px" border=0>
<frame src="<?=$Dir.FrontDir?>primage_multiframemain.php?productcode=<?=$productcode?>&size=<?=$size-20?>" name=main noresize scrolling=no marginwidth=0 marginheight=3>
<frame src="<?=$Dir.FrontDir?>primage_multiframethumb.php?productcode=<?=$productcode?>&maxsize=<?=$size-20?>" name=top noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</html>
