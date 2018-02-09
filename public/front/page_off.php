<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$search_l=$_POST["search_l"];
$search_w=$_POST["search_w"];

$location = array("서울특별시","인천광역시","경기도","강원도","대전광역시","충청도","대구광역시","경상도","부산광역시","울산광역시","광주광역시","전라도","제주도");

$sql="select * FROM tblboard WHERE board='offlinestore'";

if($search_w!=''){
	$sql .= "AND title LIKE '%".$search_w."%'";
}
if($search_l!=''){
	$sql .= "AND name='".$search_l."'";
}
$sql .= "ORDER BY title";

if(!$setup["list_num"]) $setup["list_num"] = '5';
if(!$setup["page_num"]) $setup["page_num"] = '5';

$paging = new Tem001_saveheels_Paging($sql,$setup["page_num"],$setup["list_num"]);	
$sql = $paging->getSql($sql);
$gotopage = $paging->gotopage;

$sql_off = pmysql_query($sql);

while($res=pmysql_fetch_array($sql_off)){
	$res_list[]=$res;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 오프라인 매장 안내</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script src="//code.jquery.com/jquery-1.10.0.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php">
</script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function goSearch(){
	if(document.form1.search_w.value !=''){
		document.form1.search_l.value = '';
	}
	document.form1.submit();
}

 $(document).ready( function(){
	$(".shop_list li:first").addClass("on");
	$(".right_list:first").css("display","block");	
	
	$(".left_list").click(function(e){
		var idx = $(".left_list").index($(this));
		$(".shop_list li").removeClass("on");
		$(".shop_list li").eq(idx).addClass("on");
		
		$(".right_list").hide();
		$(".right_list").eq(idx).show();
	});	
} );
//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<!-- 메인 컨텐츠 -->
<div class="main_wrap">

	<div class="container1100">
		<div class="line_map_r"><a href="#">홈</a> > <a href="#"><em>오프라인 매장</em></a></div>
	
		<div class="offline_wrap">
			<h3>오프라인 매장</h3>
			<div class="left">
				<form method="POST" name="form1" action="<?=$_SERVER['PHP_SELF']?>">
				<div class="shop_find">
					<table width="410">
						<colgroup>
							<col width="50" /><col width="290" /><col width="*px" />
						</colgroup>
						<tr>
							<th>지역</th>
							<td>
								<select name="search_l" style="width:275px" onchange="this.form.submit();">
									<option value="">지역선택</option>
									<? foreach($location as $lc){
										echo "<option value=\"$lc\"";
										if($lc==$search_l){
											echo " selected=\"selected\">$lc</option>";
										}else{
											echo " >$lc</option>";
										}
									}
									?>
								</select>
							</td>
							<td rowspan=2><a href="javascript:goSearch()"><img src="../img/button/btn_shop_find.gif" alt="검색" /></a></td>
						</tr>
						<tr>
							<th>매장명</th>
							<td><input  type="text" name="search_w" id="" style="width:270px" value="<?=$search_w?>"/></td>
						</tr>
					</table>
				</div>
				</form>
				
				<ul class="shop_list">
					<?php
						if($res_list){			
						foreach($res_list as $rl){
					?>
						<li class="left_list"><a href="#<?=$rl[title]?>">
							<table cellpadding=0 cellspacing=0 border=0 width=400 align=center>
								<tr>
									<td class="name"><a href="#"<?=$rl[title]?>><?=$rl[title]?></a></td>
									<td class="tel"><?=$rl[storetel]?></td>
								</tr>
								<tr>
									<td class="address" colspan=2><?=$rl[storeaddress]?></td>
								</tr>
							</table>
						</a></li>
					<?php
						}
					} else { ?>
						<div class="shop_list_none"><tr><td>검색 결과가 없습니다</td></tr></div>
						<li>
						<table cellpadding=0 cellspacing=0 border=0 width=400 align=center>
							
						</table>
					</li>
					<?	
					 }?>															
				</ul>
				<!--PAGING-->
				<div class="page page_margin">
				<ul>
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
				</div>
				<!--// PAGING-->
			</div>
			<div class="right" >
				<?php
					if($res_list){						
						foreach($res_list as $rl){
				?>
				<div class="right_list" style="display:none">
					<h5><span><?=$rl[title]?></span></h5>
					<ul class="off_shop_info">
						<!--<li class="img"><img src="/data/shopimages/board/offlinestore/<?=$rl[vfilename]?>" height="134" width="134" alt="" /></li>-->
						<li class="info">
							<dl>
								<dt><?=$rl[storetel]?></dt>
								<dd><?=$rl[storeaddress]?></dd>
							</dl>
						</li>
					</ul>
					<div class="mt_20">
					<?if($rl[storefilename]){?>
					<a href="<?=$rl[storefilelink]?>" target="_blank"><img src="<?=$rl[storefilename]?>" height="463" width="600" alt="" /></a>
					<?}else{?>
						<img src="/front/image/noimg.jpg" height="463" width="600" alt="" />
					<?}?>
					</div>
				</div>
				<?php }
				} else{ ?>
				<div>
					<ul>
						<li>
						<table cellpadding=0 cellspacing=0 border=0 width=400 align=center>
							결과가 없습니다.
						</table>
						</li>
					</ul>
				</div>
				<?	
				 }?>		
			</div>	
		</div>
	</div>
		 
</div><!-- //메인 컨텐츠 -->

<div id="create_openwin" style="display:none"></div>
<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_w value="<?=$search_w?>">
<input type=hidden name=search_l value="<?=$search_l?>">
</form>
<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
