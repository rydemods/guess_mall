<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$likecode = $_POST["code"];
$search_word = $_POST["search_word"];
$search_select = $_POST["search_select"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - default</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function select_code(code1) {
	var code2 = code1
	document.form1.code.value="00400"+code2;
	if(code2==null){
		document.form1.code.value="004";
	}
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<?include ($Dir.MainDir.$_data->menu_type.".php");
$page_code = "about";
/* lnb 호출 */
$lnb_flag = 3;
include ($Dir.MainDir."lnb.php");?>

	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<!-- default -->
	
		<div class="right_section">
			
			<div class="sub_title">
				<h3 class="def"><span class="kr">떼르벨 소개</span></h3>
			</div>

			<div class="terrebell_introduce">
				<h4>건강한 삶과 자연을 생각하는 친환경 홈패브릭 브랜드 자연을 담은 브랜드</h4>
				<pre>프랑스어로 아름다운 땅이라는 의미를 담고 있습니다.</pre>
<pre>
떼르벨은 100% 친환경 웰빙 침구 전문브랜드입니다.
면, 마, 실크, 모달과 같은 순수 천연소재와, 자연에서 채취하고 국제적으로 인증된(GOTS) 천연염료만으로 염색하여
인체에 해로운 화학성분이 전혀 없는 친환경 웰빙 침구 및 홈패브릭 제품을 생산합니다.
</pre>

<pre>
떼르벨은 세계최초로 천연염색 제품의 패션화, 대중화를 추구합니다.
자체 천연염색연구소에서 다년간 연구해왔던 노하우와 현대화된 자체 천연염색공장 운영으로, 
기존 가내 수공업단계 천연염색의 한계를 벗어나서 다양한 색감, 트렌디한 디자인과 혁신적인 가격으로 
누구나가 천연염색 침구 및 홈패브릭 제품을 이용할 수 있습니다.
</pre>

<pre>
떼르벨은 생활속에서 자연을 느끼게 하고 심신의 건강을 생각합니다.
자연(숲)에서 느낄 수 있는 향기, 촉감, 전경, 맛(과일)등을 모티브로 하여 생활 속에서 자연을 느낄 수 있도록 하였습니다
또한 건강에 유익한 도움을 주는 쪽, 석류, 꼭두서니, 오배자, 대황, 아선약 등에서 
추출된 염료로 천연염색을 하여 건강증진에 도움을 주고자 합니다.
</pre>

<pre>
떼르벨은 지구 환경을 생각하는 브랜드입니다.
지구환경에 해로운 화학성분을 사용하지 않고 자연소재와 천연염료만을 사용하여
환경공해를 최소화하고, 제품 또한 사용 후 자연속으로 손쉽게 돌아가서 지구환경 보존에 기여 합니다.
</pre>
			</div>

		</div>
	
	
	
	
	<!-- default -->
		<input type=hidden name=code value="<?=$likecode?>">
	</form>

<div class="page"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div>
	
<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=code value="<?=$likecode?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>
<script>
$(function() {
	var brandcode = "logo0"+document.form1.code.value.substr(5,1);
	document.getElementById(brandcode).className = document.getElementById(brandcode).className+" on";
});
</script>
</BODY>
</HTML>
