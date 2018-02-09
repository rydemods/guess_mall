<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();
//var_dump($member);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 이용약관</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>
<!--php끝-->

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<div class="line_map hhg-head">
	<div class="container">
		<div><em>&gt;</em><a>HAPPY HUNTING GROUND</a><em>&gt;</em><span><a>SPECIAL INTEREST</a></span></div>
		<h3 class="hhg-title">HAPPY HUNTING GROUND</h3>
		<p class="hhg-subtitle">원하는 모든걸 얻을 수 있는 오야니 행복사냥터</p>
		<ul class="hhg-menu">
			<li><a href="javascript:storyBegins();">story begins</a></li>
			<li><a class="on">special interest</a></li>
			<li><a href="<?$Dir.FrontDir?>color_we_love.php">color we love</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram.php">play</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram_tags.php">#tags</a></li>
			<li><a href="<?$Dir.FrontDir?>logo_art.php">logo art project</a></li>
		</ul>
	</div>
</div>

<!-- start contents -->
<div class="containerBody sub_skin">
	
	<div id="hhw-wrapper">

		<p class="comming-soon"><img src="../img/common/comming_soon.gif" alt="현재 페이지는 서비스 준비중 입니다."></p>

	</div>

</div>

<script>

function storyBegins(){
	storyBeginsURL = "<?=$Dir.FrontDir?>"+"storybegins/";
	window.open(storyBeginsURL,"stPop",'height=' + screen.height + ',width=' + screen.width + "fullscreen=yes,scrollbars=no,resizable=no");
}

</script>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
