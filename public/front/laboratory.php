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
$page_code = "laboratory";
/* lnb 호출 */
$lnb_flag = 3;
include ($Dir.MainDir."lnb.php");?>

	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<!-- default -->
	
		<div class="right_section">
			
			<div class="sub_title">
				<h3 class="def"><span class="kr">천연염색연구소</span></h3>
			</div>

			<div class="terrebell_introduce">
				<h4>천연염색사업</h4>

				<dl>
					<dt>왜 천연염색인가?</dt>
					<dd>
	속도와 효율을 중시하는 현대의 실용주의는 우리의 생활을 압도하고 있습니다. 
	의식주를 막론하고 빠르고 쉽고 편리한 것이 추구됩니다. 
	어느 분야에서든 우리는 항상 바쁘게 살아갑니다. 게다가 ‘돈이 되면 무엇이든지 할 수 있다’는 
	배금주의는 어느덧 우리를 포위하고 있는 형국입니다. 어쩌면 우리는 실용과 속도와 효율의 
	노예가 되고 말았는지도 모르겠습니다. 
	그런 가운데 우리가 잃고 있는 것은 무엇일까요? 
	푸른텍스타일이 천연염색을 선택한 이유는 우리가 지금껏 잃어 왔던 것들을 되찾고자 하는 것입니다. 그것은 삶의 여유와 건강, 우리 전통문화, 그리고 우리가 계속 소모해 오기만 했던 지구 환경입니다. 
					</dd>
				</dl>

				<dl>
					<dt>천연염색의 대중화 선언</dt>
					<dd>
	수만 년 동안 인류의 의생활을 담당해왔던 천연염색이 합성염색에게 자리를 내준 것은 불과 백여 년에 불과합니다만 합성염색은 우리가 천연염색을 거의 기억하지 못하게 할 만큼 강력하였습니다. 그런 와중에도 전통의 기억을 찾는 뜻있는 분들의 정성과 노력으로 천연염색은 계승 발전되어왔습니다. 푸른텍스타일이 천연염색 사업에 도전할 수 있었던 것은 오롯이 그 분들의 덕입니다. 천연염색에 대한 장인정신으로 화덕 앞에서 진한 땀을 흘리고 계실 분들을 우리는 존중합니다.  
	그분들의 노력으로 문화재로서의 가치가 되살려졌고 많은 사람들의 취미가 되었고 또 소규모 산업으로 발전할 수도 있었습니다. 
	그래도 여전히 일반 소비자가 부담 없이 접하기에는 생산량이 턱없이 부족하기에 푸른텍스타일은 천연염색의 대중화를 위하여 산업화 생산에 뛰어 들었습니다. 이는 지난 5년간 천연염색에 특화된 생산 설비에 대해 각종 특허를 획득하는 등 연구 개발에 노력해 온 결과이기도 합니다.
	아직도 부족한 점이 많습니다만 조심스러우나마 감히 천연염색의 대중화를 선언합니다.
					</dd>
				</dl>

				<dl>
					<dt>전통의 계승.</dt>
					<dd>
	산업화 생산을 하더라도 푸른텍스타일의 천연염색은 방법에 있어서 전통을 계승합니다. 약초보감, 임원경제지, 산림경제, 천공개물 등 천연염색과 관련된 고서의 문헌을 참조하는 방법을 엄격히 따릅니다. 
					</dd>
				</dl>

				<dl>
					<dt>천연염색의 새로운 기준</dt>
					<dd>
	천연염색에 대한 많은 편견이 있습니다. 색이 잘 빠지고 원하는 색상을 얻을 수 없다는 등 패션소재로 이용하기에는 한계가 매우 많다는 것입니다. 그런 시각이 근거가 없는 것은 아닙니다만 푸른텍스타일은 이런 모든 어려움을 극복하기 위해 수년간 장인정신으로 무장한 연구진들이 패션 소재로 손색이 없을 견뢰도와 재현성을 확보하기 위한 연구를 해 왔습니다. 그 땀의 결실이 이제 동두천 천연염색 공장에서 익어가고 있습니다.  동두천 공장에서는 전통의 천연염색 재료와 방법으로 패션 소비자의 요구에 부응하는 원단과 의복을 공급하기 위한 체계를 갖추었습니다. 
					</dd>
				</dl>

				<dl>
					<dt>환경을 우선합니다.</dt>
					<dd>
	하나뿐인 지구는 우리 모두의 자산입니다. 
	지속 가능한 환경을 지키기 위한 노력은 아무리 해도 지나치지 않을 것입니다.  
	천연염색 사업을 구상하면서부터 지금껏 우리는 에너지와 물의 사용을 줄이고 배출 오염을 최소화하기 
	위한 노력을 지속하고 있습니다.

					</dd>
				</dl>

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
