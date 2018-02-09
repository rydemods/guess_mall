<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");

#####이벤트 공지사항 탭 구분하기
$tab = ($_REQUEST['tab'])?$_REQUEST['tab']:"0";
$class_on_tab[$tab]=' class="on"';

##### 진행중인 이벤트&마감된 이벤트
if($_GET['category_code']){
	$category="마감";
	$category_code = 1;
}else{
	$category="진행중";
	$category_code=0;
}
$class_on_cate[$category_code]=' class="on"';
$searchtxt = $_REQUEST['searchtxt'];

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
function goEventCate(cate){
	if(cate==''||cate==null){
		cate=0;
	}
	document.form2.block.value='';
	document.form2.gotopage.value='';
	document.form2.category_code.value=cate;
	document.form2.submit();
	
}

function goTab(tab){
	if(tab=="0"){
		$("#event_notice").removeClass("on");
		$("#event_result").addClass("on");
	}else{
		$("#event_result").removeClass("on");
		$("#event_notice").addClass("on");
	}
}
		
function goTab2(tab){
	document.form2.tab.value=tab;
	document.form2.submit();
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
$page_code = "terrebell_event";
/* lnb 호출 */
$lnb_flag = 1;
include ($Dir.MainDir."lnb.php");?>

	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=get>
	<!-- default -->
	
		<div class="right_section">
			
			<div class="sub_title">
				<h3 class="def"><span class="kr">EVENT</span></h3>
			</div>

			<div class="sub_top_img"><img src="../img/test/test_sub_top.jpg"></div>

			<ul class="event_tap_wrap">
				<li<?=$class_on_cate[0]?>><a href="javascript:goEventCate('0');" target="_self">현재 진행중인 이벤트</a></li>
				<li<?=$class_on_cate[1]?>><a href="javascript:goEventCate('1');" target="_self">종료된 이벤트</a></li>
				
			</ul>
			<ul class="event_list">
			<?php
				$sql = "SELECT count(*) as t_count FROM tblboard where board='event' ";
				$sql.= "";
				
				$paging = new Tem001_saveheels_Paging($sql,10,9,'GoPage',true);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT * FROM tblboard where board='event' ";
				$sql.= "AND category='{$category}' ";
				if($searchtxt){
					$sql.="AND title like '%{$searchtxt}%' ";
				}
				$sql.= "ORDER BY writetime DESC";
				
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
			?>
				<li>
					<?if($row->link_url){?>
						<a href="<?=$row->link_url?>" target="_self">
					<?}else{?>
						<a href="javascript:goView('<?=$row->num?>')" target="_self">
					<?}?><img src="<?=$Dir.DataDir."shopimages/board/event/".$row->vstorefilename?>" alt="Terrebell BE MY FRIEND" /></a>
				</li>
			<?php
				}
			?>
			</ul>
			<div class="page">
				<ul>
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				</ul>
			</div>

		</div>
	
	
	
	
	<!-- default -->
	</form>

	
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=category_code value="<?=$category_code?>">
<input type=hidden name=searchtxt value="<?=$searchtxt?>">
<input type=hidden name=tab value="<?=$tab?>">
</form>

<form name=form3 method="POST" action="event_view.php">
<input type=hidden name=num value="">
</form>

<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</BODY>
</HTML>
