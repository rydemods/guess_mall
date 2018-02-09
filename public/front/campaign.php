<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sql="SELECT agreement FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$agreement=stripslashes($row->agreement);
}
pmysql_free_result($result);

if(ord($agreement)==0) {
	$agreement=file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement="<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td  style=\"padding:10\">{$agreement}</td></tr></table>";
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);

//$brand_code = $_REQUEST['brand_code'];
$brand_code='3';

function campaignList($brand_code){//campaign리스트 및 페이징 함수
	$campaign = array();
	$array = array();
	$sql = "select * from tblbrand_board where board_code={$brand_code} ";
	$sql.= "ORDER BY date DESC ";
	$paging = new Tem001_saveheels_Paging($sql,10,10,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$campaign[] = $row;
	}
	$array[0] = $campaign;
	$array[1] = $paging;
	$array[2] = $t_count;
	$array[3] = $gotopage;
	return $array;
}

function Item($board_num){//관련 상품 가져오는 함수
	$item = array();
	$sql = "select * from tblbrand_boarditem a join tblproduct b on a.productcode=b.productcode where a.board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$item[] = $row;
	}
	return $item;
}

//페이징 및 campging리스트 가져오기
$array = campaignList($brand_code);
$gotopage = $array[3];
$t_coiunt = $array[2];
$paging = $array[1];
$campaign = $array[0];
//exdebug($array);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - CAMPAIGN</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		CAMPAIGN
		<p class="line_map"><a>홈</a> &gt; <a>BRAND</a> &gt; <span>CAMPAIGN</span></p>
	</h3>

	<div class="campaing-wrap">
		<!-- list S -->
		<div class="campaing_list">
		<?if($campaign){?>
			<?for($i=0; $i<count($campaign); $i++){?>
				<a href="#campaing_layer<?=$i?>"><img src="../data/shopimages/brandboard/<?=$campaign[$i]->thumbnail_image?>"></a>
			<?}?>
		<?}?>
			<!--
			<a href="#campaing_layer01"><img src="../front/images/campaing_img01.jpg" alt="" /></a>
			<a href="#campaing_layer02"><img src="../front/images/campaing_img02.jpg" alt="" /></a>
			<a href="#campaing_layer03"><img src="../front/images/campaing_img03.jpg" alt="" /></a>
			<a href="#campaing_layer04"><img src="../front/images/campaing_img04.jpg" alt="" /></a>
			<a href="#campaing_layer05"><img src="../front/images/campaing_img05.jpg" alt="" /></a>
			<a href="#campaing_layer06"><img src="../front/images/campaing_img06.jpg" alt="" /></a>
			<a href="#campaing_layer07"><img src="../front/images/campaing_img07.jpg" alt="" /></a>
			<a href="#campaing_layer08"><img src="../front/images/campaing_img08.jpg" alt="" /></a>
			<a href="#campaing_layer09"><img src="../front/images/campaing_img09.jpg" alt="" /></a>
			<a href="#campaing_layer10"><img src="../front/images/campaing_img10.jpg" alt="" /></a>
			<a href="#campaing_layer11"><img src="../front/images/campaing_img11.jpg" alt="" /></a>
			<a href="#campaing_layer12"><img src="../front/images/campaing_img12.jpg" alt="" /></a>
			-->
		</div>
		<!-- //list E -->

		<!-- layer S -->
	<?if($campaign){?>
		<?for($i=0; $i<count($campaign); $i++){?>
		<div class="campaing_layer <?=$i?>" id=<?=$i?>>
			<div class="layer_con">
				<div class="control">
					<?if($i !=0){?>
					<a href="#" class="prev"><img src="../img/btn/btn_layer_prev.png" alt="이전" /></a>
					<?}?>
					<?if($i != count($campaign)-1){?>
					<a href="#" class="next"><img src="../img/btn/btn_layer_next.png" alt="다음" /></a>
					<?}?>
				</div>			

				<!-- -->
				
				<div class="con_inner" id="campaing_layer<?=$i?>" value=<?=$i?>>
					<div class="campaing_top">
						<ul class="left">
							<li><a href="#"><img src="../img/btn/btn_facebook.gif" alt="facebook" /></a></li>
							<li><a href="#"><img src="../img/btn/btn_kakao.gif" alt="카카오톡" /></a></li>
							<li><a href="#"><img src="../img/btn/btn_instar.gif" alt="인스타그램" /></a></li>
						</ul>
						<span class="right"><img src="../img/btn/btn_shop.gif" alt="Shop" /></span>
					</div>	
					<div class="campaing_view">
						<div class="campaing_big"><img src="../data/shopimages/brandboard/<?=$campaign[$i]->big_image?>" alt="" /></div>
						
						<div class="campaing_product">
						<?$item = Item($campaign[$i]->board_num)?>
						<?foreach($item as $val){?>
							<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$val->productcode?>"><img src="../data/shopimages/product/<?=$val->minimage?>" width="133" height="130" alt="" /></a>
						<?}?>
							
					
							<!--
							<a href="#"><img src="../data/shopimages/product/product_17.jpg" width="133" height="130" alt="" /></a>
							<a href="#"><img src="../data/shopimages/product/product_18.jpg" width="133" height="130" alt="" /></a>
							<a href="#"><img src="../data/shopimages/product/product_19.jpg" width="133" height="130" alt="" /></a>
							-->
						</div>					
					</div>
				</div>
				<!-- //-->

				<a href="#" class="close"><img src="../img/btn/btn_layer_close.png" alt="닫기" /></a>
			</div>
		</div>
		<?}?>
	<?}?>
		<!-- //layer E -->
	</div>

	<form id="paging" name="paging" method=POST action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
        <input type="hidden" name="block" value="<?=$block?>"/>
		<input type="hidden" name="brand_code" value="<?=$brand_code?>"/>
	</form>
	<div class="paging">
			<!--
			<a class='on'>1</a>
			<a href="javascript:GoPage(0,2);" onMouseOver="window.status='페이지 : 2';return true">2</a>
			<a href="javascript:GoPage(0,3);" onMouseOver="window.status='페이지 : 3';return true">3</a>	
			-->
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
	</div>
	

	<script type="text/javascript">

	function GoPage(block,gotopage) {//페이징 하는 녀석 
        document.paging.block.value = block;
        document.paging.gotopage.value = gotopage;
		document.paging.submit();
    }

	$(function(){
		/* layer */
		$(".campaing_list a").on('click', function(ev) {
			ev.preventDefault();
			currentPosition = $('.campaing_list a').index($(this));
		
			var layer_obj = $(this).attr('href');
			$('.campaing_layer .con_inner').hide();
			//alert($(layer_obj).parent().parent().attr('class'));
	
			$(layer_obj).parent().parent().css({height:$('window').height()}).fadeIn('fast');	
			$(layer_obj).parent().css({marginTop:- + $(layer_obj).outerHeight()/2 , marginLeft:- + $(layer_obj).outerWidth()/2});
			$(layer_obj).show();
		});		

		$('.close').on('click', function(ev){
			ev.preventDefault();

			$(this).parent().parent().fadeOut('fast');	
			//alert($(this).parent().parent().attr('id'));
		});

		/* rolling */
		var currentPosition = 0;
		var slideWidth = $('.campaing_layer .con_inner').width(); 
	    var slides = $('.campaing_layer .con_inner');
		var numberOfSlides = slides.length;
		var layer;
		$('.campaing_layer .control a').on('click', function(ev){    
			ev.preventDefault();

			//$('.campaing_layer .con_inner').hide();
			
			if ($(this).hasClass('next')) { 
				//if(currentPosition<numberOfSlides-1) currentPosition++;
				//$(".campaing_layer" +'.'+currentPosition).css('display','none');
				$("#"+currentPosition).fadeOut('fast');
				currentPosition++;
			} else {
				//if(currentPosition>0)currentPosition--;
				$("#"+currentPosition).fadeOut('fast');
				currentPosition--;
			}
			//$(".campaing_layer" +'.'+currentPosition).css('display','block');
			//slides[currentPosition].style.display = 'block';
			var layer_obj = "#campaing_layer"+currentPosition;
			$(layer_obj).parent().parent().css({height:$('window').height()}).fadeIn('fast');	
			$(layer_obj).parent().css({marginTop:- + $(layer_obj).outerHeight()/2 , marginLeft:- + $(layer_obj).outerWidth()/2});
			$(layer_obj).show();
		});
	});	
	</script>

</div>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
