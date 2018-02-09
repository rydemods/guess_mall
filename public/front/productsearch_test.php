<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once ($Dir."lib/product.class.php");
$product = new PRODUCT();


#상단 브랜드 링크로 넘어오는 브랜드 인덱스
$brand = $_REQUEST["brand"];

#검색어 설정 (헤더 검색필드는 GET // 검색페이지 폼은 POST)
$search = $_POST?$_POST['search']:$_GET['search'];
$brandChk = $brandChk == "" ? "" : $_REQUEST['brandChk'];
foreach($_POST as $k => $v){
	if(is_array($v)){
		$_POST[$k] = array_filter($v);
	}
}

#브랜드 체크박스 체크설정
if($_POST[s_search_brand]){
	for($b = 0 ; $b < count($_POST[s_search_brand]) ; $b++){
		$checked[brand][$_POST[s_search_brand][$b]] = "checked";
	}
}

#sort 정리
$s_sort = $_POST[s_sort]?$_POST[s_sort]:"date_desc";
$sort = str_replace('_',' ',$s_sort);

#sleep('4');
$searchCategory = $_POST['s_search_category'];
$searchBrand = $_POST['s_search_brand'];
//$searchMinPrice = $_POST['s_search_min_price']?$_POST['s_search_min_price']:1;
//$searchMaxPrice = $_POST['s_search_max_price']?$_POST['s_search_max_price']:500000;
$searchColor = $_POST['s_search_color'];

$searchtype = array();
$addQuery = array();
$addQuery[] = "a.display = 'Y' ";

if($_POST['reSearch']){
	$checked['reSearch'] = "checked";
	$addQuery[] = "(".str_replace('WHERE ','',str_replace('\\','',$_POST['addwhere'])).")";
}

if($search){
	$brand = "";
	$search = strtoupper($search);
	$sword_search = "
	((UPPER(a.productname) LIKE '%{$search}%' OR UPPER(a.keyword) LIKE '%{$search}%') 
	OR a.productcode LIKE '{$search}%' 
	OR UPPER(a.production) LIKE '%{$search}%' 
	OR UPPER(a.model) LIKE '%{$search}%' 
	OR UPPER(a.selfcode) LIKE '%{$search}%' 
	OR UPPER(a.content) LIKE '%{$search}%') ";
	$addQuery[] = $sword_search;
}

if(count($searchCategory) > 0){
	$brand = "";
	$searchtype[] = "Category";
	$addQuery[] = "b.c_category LIKE '".max($searchCategory)."%'";
}
if(count($searchBrand) > 0){
	$brand = "";
	$searchtype[] = "Brand";
	$addQuery[] = "a.brand IN ('".implode("', '", $searchBrand)."')";
}
/*if($searchMinPrice && $searchMaxPrice){
	$searchtype[] = "Price";
	$addQuery[] = "a.sellprice between ".$searchMinPrice." and  ".$searchMaxPrice." ";
}*/

if($searchColor){
	$brand = "";
	$searchtype[] = "Color";
	$arrSearchColor = explode(",", $searchColor);
	$arrSearchColorParts = array();
	foreach($arrSearchColor as $v){
		if(!strlen($v)) continue;
		$selectedColor[$v] = " class = 'select'";
		$arrSearchColorParts[] = "a.color_code like '%".$v."%'";
	}
	$addQuery[] = "(".implode(" or ", $arrSearchColorParts).")";
}


#검색 리스트 
if(count($addQuery) > 0){
	$loopSearchData = array();
	$strAddQuery = "WHERE ".implode(" AND ", $addQuery);
	if($brand){
		$strAddQuery .= " AND a.brand = '{$brand}' ";
		$strBrand = pmysql_fetch(pmysql_query("SELECT brandname FROM tblproductbrand WHERE bridx = {$brand} "));
	}
	$sql = "
				SELECT 
					productcode, 
					productname, 
					consumerprice, 
					sellprice, 
					reserve, 
					minimage, 
					maximage,
					etctype, 
					mdcomment, 
					option1, 
					max(date) date, 
					max(vcnt) vcnt
				FROM 
					tblproduct a 
					LEFT JOIN tblproductlink b on a.productcode = b.c_productcode 
				".$strAddQuery." AND display = 'Y' 
				GROUP BY 
					productcode, productname, consumerprice, sellprice, reserve, minimage, maximage, etctype, mdcomment, option1
				ORDER BY 
					".$sort."
				";

	$paging = new Tem001_saveheels_Paging($sql,10,10,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = $paging->getSql($sql);
//exdebug($sql);
	$result=pmysql_query($sql,get_db_conn());

}else{
	$t_count = 0;
}

#추천상품
	$sqlVcount = "SELECT 
					productcode, 
					productname, 
					consumerprice, 
					sellprice, 
					reserve, 
					minimage
					FROM tblproduct order by vcnt desc limit 10 offset 0;";
	$resultVcount=pmysql_query($sqlVcount,get_db_conn());

#키워드
	$keywordquery = "select search from tblcounterkeyword order by cnt desc limit 4 offset 0 ";
	$keywordresult=pmysql_query($keywordquery,get_db_conn());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE><?=$_data->shoptitle." [상품검색]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="../js/jquery.sudoSlider.js" ></script>
<script type="text/javascript" src="../js/jcarousellite_1.0.js" ></script>
<script type="text/javascript" src="../js/slides.jquery.js" ></script>
<script type="text/javascript" src="../js/custom.js" ></script>
<script type="text/javascript" src="../css/select_type01.js" ></script>

<!--스마트 서치용 jQuery-->
<script type="text/javascript" src="../js/jquery.nstSlider.js" ></script>


<?php include($Dir."lib/style.php")?>
</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<script type="text/javascript">
	
function GoPage(block,gotopage) {
	document.frmSearch.block.value=block;
	document.frmSearch.gotopage.value=gotopage;
	document.frmSearch.submit();
}

function ChangeSort(sort){
	var frm = document.frmSearch;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.s_sort.value = sort;
	frm.submit();
}

function keywordsearch(keyword){
	var frm = document.frmSearch;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.search.value=keyword;
	frm.submit();
}

function GoSearch(){
	var frm = document.frmSearch;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.submit();
}

function basket(productcode){
	var frm = document.frmBasket;
	frm.code.value = productcode.substr( 0, 12 );
	frm.productcode.value = productcode;

	$.ajax({
		type: "POST", 
		url: "../front/confirm_basket_proc.php", 
		data: $('#ID_frmBasket').serialize(), 
		async: false,
		beforeSend: function () {
		//전송전
		}
	}).done(function ( msg ) {
		if(msg){
			alert(msg);
			procBuy = false;
			return false;
		}else{
			procBuy = true;
		}
	});

		if(procBuy){
			document.frmBasket.action="/front/confirm_basket.php";
			document.frmBasket.target="confirmbasketlist";
			window.open("about:blank","confirmbasketlist","width=500,height=250,scrollbars=no");
			document.frmBasket.submit();
		}

}

</script>
<form name=frmBasket method=post id='ID_frmBasket'>
	<input type=hidden name=option1 value='1'>
	<input type=hidden name=quantity value='1'>
	<input type=hidden name=code value="">
	<input type=hidden name=productcode value="">
	<input type=hidden name=ordertype>
	<input type=hidden name=opts>
</form>
<!-- 메인 컨텐츠 -->
<div class="main_wrap">
	<?
	$subTop_flag = 3;
	include ($Dir.MainDir."sub_top.php");
	?>

	<div class="container1100">

	<h3 class="title mt_20 ">
		상품검색
		<p class="line_map"><a>홈</a> &gt; <a class="on">상품검색</a></p>
	</h3>

<form name="frmSearch" method="POST" action="productsearch.php">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_sort value="<?=$s_sort?>">
<input type=hidden name=addwhere value = "<?=$strAddQuery?>">
<input type=hidden name=brand value = "<?=$brand?>">
			<div class="search_input_area">
				<table cellpadding="0" cellspacing="0" border="0" width="685">
					<colgroup>
						<col style="width:155px" /><col style="width:430px" /><col style="width:100px" />
					</colgroup>
					<tr>
						<th style="line-height:1.5">search</th>
						<td class="input_t"><input type="text" name="search" id="search" value="<?=$search?>" /><a href="javascript:GoSearch();" class="btn_D on va_t">상품검색</a></td>
						<td class="re"><input type="checkbox" name="reSearch" id="reSearch" value="1" <?=$checked['reSearch']?> /> 결과 내 재검색</td>
					</tr>
					<!--
					<tr>
						<td></td>
						<td colspan="2" class="word"><span>인기검색어</span> 
						<?$k = 0;
						while($keyworddata=pmysql_fetch_object($keywordresult)) {$k++;?>
						<a href="javascript:keywordsearch('<?=$keyworddata->search?>')"><?=$keyworddata->search?></a>
						<?
						if($k <4) echo "|";
						}?>
						</td>
					</tr>
					-->
				</table>
			</div>

			<!-- 검색 옵션 -->
			<div class="search_opt_wrap hide">
				<table class="search_sort" cellpadding=0 border=0 cellspacing=0>
					<colgroup>
						<col width="100" /><col width="*px" />
					</colgroup>
					<tr>
						<th>category</th>
						<td>
							<select name="s_search_category[]" id="c_category1" class="c_category" categoryStep = '2'>
								<option value="">〓〓 1차 카테고리 〓〓</option>
								<?foreach($smartSearchCategory as $smart_ckey => $smart_cval){?>
									<option value="<?=$smart_cval['category']?>" <?=$_POST[s_search_category][0] == $smart_cval['category']?"selected":""?> ><?=$smart_cval['code_name']?></option>
								<?}?>
							</select>
							<select name="s_search_category[]" id="c_category2" class="c_category" categoryStep = '3'>
								<option value="">〓〓 2차 카테고리 〓〓</option>
							</select>
							<select name="s_search_category[]" id="c_category3" class="c_category" categoryStep = '4'>
								<option value="">〓〓 3차 카테고리 〓〓</option>
							</select>
							<select name="s_search_category[]" id="c_category4">
								<option value="">〓〓 4차 카테고리 〓〓</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>brand</th>
						<td>
							<ul class="search_brand">
								<?foreach($smartSearchBrand as $smart_bkey => $smart_bval){?>
									<li><input type="checkbox" name="s_search_brand[]" value = '<?=$smart_bval['bridx']?>' <?=$checked[brand][$smart_bval['bridx']]?> class = 's_search_brand'/> <?=$smart_bval['brandname']?></li>
								<?}?>
							</ul>
						</td>
					</tr>
					<tr>
						<th>price</th>
						<td>
							<ul class="price_bar_wrap">
								<li class="s_nstSlider" data-range_min="1" data-range_max="500000" data-cur_min="<?=$searchMinPrice?>"  data-cur_max="<?=$searchMaxPrice?>">
									<div class="price_bar">
										<div class="bar"></div>
										<a href="javascript:;" class="price_bar_btn_left leftGrip"></a>
										<a href="javascript:;" class="price_bar_btn_right rightGrip"></a>
									</div>
								</li>
								<li class="ml_15">
									<div class="search_price">
										<input type="text" name="s_search_min_price" id="s_search_min_price"/> 원 ~
										<input type="text" name="s_search_max_price" id="s_search_max_price" value="<?=$_POST[s_search_max_price]?>"/> 원
									</div>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th>color</th>
						<td>
							<ul class="s_search_color">
								<?foreach($arrayColorCode as $ck => $cv){?>
									<li <?=$selectedColor[$ck]?>><a href="javascript:;" class="color_<?=$cv?>" alt = '<?=$ck?>'></a></li>
								<?}?>
							</ul>
							<input type = 'hidden' name = 's_search_color' id = 's_search_color'>
						</td>
					</tr>
					<tr>
						<th>size</th>
						<td>
							<ul class="price_bar_wrap">
								<li class="s_nstSliderSize" data-range_min="100" data-range_max="350" data-cur_min="100"  data-cur_max="350">
									<div class="price_bar">
										<div class="barSize"></div>
										<a href="javascript:;" class="price_bar_btn_left leftGripSize"></a>
										<a href="javascript:;" class="price_bar_btn_right rightGripSize"></a>
									</div>
								</li>
								<li class="ml_15">
									<div class="search_price">
										<input type="text" name="s_search_min_size" id="s_search_min_size" /> mm ~
										<input type="text" name="s_search_max_size" id="s_search_max_size" /> mm
									</div>
								</li>
							</ul>
						</td>
					</tr>
				</table>
			</div><!-- //검색 옵션 -->
</form>

			<div class="message_wrap ptb_40">
				<h4>
				<?if($t_count){?>
				<span class="dahong">"<?if($search){echo $search;}elseif($brand){echo $strBrand[brandname];}?>"</span> 총 <span class="dahong"><?=$t_count?></span> 건의 검색결과가 있습니다
				<?}else{?>
				"<?if($brand){echo $strBrand[brandname];}?>" 검색결과가 없습니다.
				<?}?>
				</h4>
			</div>
			
			<!-- 검색 리스트 -->
			<div class="table_wrap mt_20 ">
				<h3><span class="total ngb_14">Total <strong><?=$t_count?></strong></span></h3>
				<div class="right_info" style="right:3px; top:-8px;">
					<ul class="sort">
						<li><a href="javascript:ChangeSort('date_desc')" <?if($s_sort == 'date_desc'){?> class="on" <?}?> >신규등록순</a></li>
						<li><a href="javascript:ChangeSort('vcnt_desc')" <?if($s_sort == 'vcnt_desc'){?> class="on" <?}?> >인기판매순</a></li>
						<li><a href="javascript:ChangeSort('sellprice_desc')" <?if($s_sort == 'sellprice_desc'){?> class="on" <?}?> >높은가격순</a></li>
						<li><a href="javascript:ChangeSort('sellprice')" <?if($s_sort == 'sellprice'){?> class="on" <?}?> >낮은가격순</a></li>
					</ul>
				</div>
				<ul class="goods_list_type_a"> 

<?		$result=pmysql_query($sql,get_db_conn());	// 임시 
while($row=pmysql_fetch_object($result)) {  

	//$t=pmysql_query($sql,get_db_conn()); $aa=pmysql_fetch_object($t);  exdebug($aa);
	##### 쿠폰에 의한 가격 할인
	$cou_data = couponDisPrice($row->productcode);
	if($cou_data['coumoney']){
		$row->sellprice = $row->sellprice-$cou_data['coumoney'];
		$row->dc_type = $cou_data["goods_sale_money"];
	}

	$groupPriceList = $product->getProductGroupPrice($row->productcode);
	if ($groupPriceList) { // 일반 및 도매회원 금액 세팅시 로그인 되어잇는 user 등급에 따라 판매 금액 적용
		$row->sellprice = $groupPriceList[sellprice];
		$row->consumerprice = $groupPriceList[consumerprice];
	}

	##### 쿠폰에 의한 가격 할인
	
	##### 오늘의 특가, 타임세일에 의한 가격
	$spesell = getSpeDcPrice($row->productcode);
	if($spesell){
		$row->sellprice = $spesell;
	}
	##### //오늘의 특가, 타임세일에 의한 가격
	$dc_rate = getDcRate($row->consumerprice,$row->sellprice);
	
	if (strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
		$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($row->minimage);
	}else if(strlen($row->minimage)>0 && file_exists($Dir.$row->minimage)){
		$imgsrc = $Dir.$row->minimage;
	}else if(strlen($row->maximage)>0 && file_exists($Dir.$row->maximage)){
		$imgsrc = $Dir.$row->maximage;
	}else{
		$imgsrc = $Dir."images/no_img.gif";
	}
?>
					<li>	
						<ul class="goods_quick_icon02">
							<li><a href="javascript:;" alt="<?=$row->productcode?>" class="detail">상품미리보기</a></li>
							<!--<li><a href="javascript:basket('<?=$row->productcode?>');" class="cart">장바구니 담기</a></li>-->
						</ul>
						<a href="productdetail.php?productcode=<?=$row->productcode?>">
							<img src="<?=$imgsrc?>" style="width:178px; height:178px;" alt="" />
						</a>
						<div class="goods_price_info">
							<a href="productdetail.php?productcode=<?=$row->productcode?>">
							<span class="title">
							<?=mb_substr($row->productname,0,30,"euc-kr")?>...</span><br />
							</a>
							<!-- <span class="ment"><?=$row->mdcomment?></span> -->
							<!--<span class="icon"><?=viewicon($row->etctype)?></span>	 아이콘 -->
							<span class="price">
								<?php// if($row->consumerprice != $row->sellprice) { ?>
								<em><?=Number_format($row->consumerprice)?> 원 </em>
								<?// } ?>
								<?=Number_format($row->sellprice)?> 원
							</span>							
						</div>
					</li>
<?}?>
				</ul>
			</div><!-- //검색 리스트 -->
			
		<div class="paging mt_30">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div>

			<!-- 추천상품 -->
			<div class="recommend_product hide">
				<h3>추천상품 <span>YOU MAY ALSO LOVE</span></h3>
				<div class="product_list" style="width:1098px !important">
					<ul class="recommend_list">
<?	while($rowVcount=pmysql_fetch_object($resultVcount)) {
	if (strlen($rowVcount->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$rowVcount->minimage)) {
		$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($rowVcount->minimage);
	}else{
		$imgsrc = $Dir."images/no_img.gif";
	}
?>
						<li>
							<a href="productdetail.php?productcode=<?=$rowVcount->productcode?>"><img src="<?=$imgsrc?>" style="width:142px; height:142px;" alt="" /></a>
							<div class="goods_info w140">
								<?=$rowVcount->productname?><br />
								<span class="original"><?=Number_format($rowVcount->consumerprice)?></span><span class="off"><?=Number_format($rowVcount->sellprice)?>원</span>
								<p><img src="../img/icon/icon_p.gif" alt="" /><?=Number_format($rowVcount->reserve)?></p>
							</div>
						</li>
<?}?>
					</ul>
				</div>
				<a href="#" class="recommend_product_left" style="z-index:50"></a>
				<a href="#" class="recommend_product_right" style="z-index:50"></a>
			</div><!-- //추천상품 -->

	</div>
</div><!-- //메인 컨텐츠 -->
<!-- 미리보기 팝업 -->
<div id="divDetail" style="position: fixed; top:1px; left:50%; margin-left:-452px; width: 902px;height: 555px;z-index: 30; background-color: #ffffff;border: 1px solid;display:none">		
</div>
<div id="overDiv" style="position:absolute;top:0px;left:0px;z-index:100;display:none;" class="alpha_b60" ></div>
<div class="popup_preview_warp" style="margin-left: 50%;left: -459px;display:none;" ></div>

<script>


$(function(){
	$('ul.goods_list_type_a li').mouseenter(function(){
	$(this).find('ul.goods_quick_icon02').css('display','block');
	});
	$('ul.goods_list_type_a li').mouseleave(function(){
	$(this).find('ul.goods_quick_icon02').css('display','none');
	});

	$('ul.goods_quick_icon02 li a.detail').click(function(){			
		$('div.popup_preview_warp').html("<img src='../images/common/loading_img.gif'>");
		$('#overDiv').css({'width':$(document).width(),'height':$(document).height()})
		$('#overDiv').show();	
		
		var prcode = $(this).attr("alt");
		$.post("ajax_preview_for_list.php",{productcode:prcode},function(data){
			if(data){
			$('div.popup_preview_warp').html(data);
			}
		});
		$('div.popup_preview_warp').show();
		$('div.popup_preview_warp').css({'top':$(window).scrollTop()+100,'z-index':'210'});
		
	});

});



function showDetail(code){ //code에 productcode


	$('div.popup_preview_warp').html("<img src='../images/common/loading_img.gif'>");
	$('#overDiv').css({'width':$(document).width(),'height':$(document).height()})
	$('#overDiv').show();	
	


	$.post("ajax_preview_for_list.php",{productcode:code},function(data){
		if(data){
		$('div.popup_preview_warp').html(data);
		}
	});
	$('div.popup_preview_warp').show();
	$('div.popup_preview_warp').css({'top':$(window).scrollTop()+100,'z-index':'210'});

}

function closeDetail(){
	$("#divDetail").hide();
}


function CheckForm(gbn,temp2) {
	var itemCount = 0;
	if(gbn!="wishlist") {
		if (typeof($("#quantity").val()) == "undefined" || $("#quantity").val() == null && typeof($("#option2").val())){
			$(".opt_list li").each(function(){
			var id = $(this).attr("id");
			var ex_id = id.split("-");
			document.form1.optionArr.value = document.form1.optionArr.value == "" ? ex_id[1] : document.form1.optionArr.value+"||"+ex_id[1];
			document.form1.quantityArr.value = document.form1.quantityArr.value == "" ? $("#quantityea-"+ex_id[1]).val(): document.form1.quantityArr.value+"||"+$("#quantityea-"+ex_id[1]).val();
			document.form1.priceArr.value = document.form1.priceArr.value == "" ? $("#itemPrice-"+ex_id[1]).attr("alt"): document.form1.priceArr.value+"||"+$("#itemPrice-"+ex_id[1]).attr("alt");
			itemCount++;
			});
			
			if (itemCount < 1){
				document.form1.optionArr.value = "";
				document.form1.quantityArr.value = "";
				document.form1.priceArr.value = "";
				alert('주문을 추가 하셔야 합니다.');
				$("#option1").focus();
				return;
			}
		} else {
			if ($("#quantity").val() < 1){
				alert('주문 수량을 확인하세요.');
			}
		}		
	
	}

	if(gbn=="ordernow") {
		document.form1.ordertype.value="ordernow";
	}
	
	if(temp2!="") {
		document.form1.opts.value="";
		try {
			for(i=0;i<temp2;i++) {
				if(document.form1.optselect[i].value==1 && document.form1.mulopt[i].selectedIndex==0) {
					alert('필수선택 항목입니다. 옵션을 반드시 선택하세요');
					document.form1.mulopt[i].focus();
					return;
				}
				document.form1.opts.value+=document.form1.mulopt[i].selectedIndex+",";
			}
		} catch (e) {}
	}

	if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex<2  && itemCount<1) {
		if (document.form1.option1.value == ""){
			alert('해당 상품의 옵션을 선택하세요.');
			document.form1.option1.focus();
			return;
		}
		
	}

	if(typeof(document.form1.option2)!="undefined" && document.form1.option2.selectedIndex<2  && itemCount<1) {
		alert('해당 상품의 옵션을 선택하세요..');
		document.form1.option2.focus();
		return;
	}

	if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex>=2 && itemCount<1) {
		temp2=document.form1.option1.selectedIndex-1;
		if(typeof(document.form1.option2)=="undefined") temp3=1;
		else temp3=document.form1.option2.selectedIndex-1;
		if(num[(temp3-1)*10+(temp2-1)]==0) {
			alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
			document.form1.option1.focus();
			return;
		}
	}

	if(typeof(document.form1.package_type)!="undefined" && typeof(document.form1.packagenum)!="undefined" && document.form1.package_type.value=="Y" && document.form1.packagenum.selectedIndex<2) {
		alert('해당 상품의 패키지를 선택하세요.');
		document.form1.packagenum.focus();
		return;
	}

	if(gbn!="wishlist") {
		<?php  if($_pdata->assembleuse=="Y") { ?> // 무시해도 됨
		if(typeof(document.form1.assemble_type)=="undefined") {
			alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
			return;
		} else {
			if(document.form1.assemble_type.value.length>0) {
				arracassembletype = document.form1.assemble_type.value.split("|");
				document.form1.assemble_list.value="";

				for(var i=1; i<=arracassembletype.length; i++) {
					if(arracassembletype[i]=="Y") {
						if(document.getElementById("acassemble"+i).options.length<2) {
							alert('필수 구성상품의 상품이 없어서 구매가 불가능합니다.');
							document.getElementById("acassemble"+i).focus();
							return;
						} else if(document.getElementById("acassemble"+i).value.length==0) {
							alert('필수 구성상품을 선택해 주세요.');
							document.getElementById("acassemble"+i).focus();
							return;
						}
					}

					if(document.getElementById("acassemble"+i)) {
						if(document.getElementById("acassemble"+i).value.length>0) {
							arracassemblelist = document.getElementById("acassemble"+i).value.split("|");
							document.form1.assemble_list.value += "|"+arracassemblelist[0];
						} else {
							document.form1.assemble_list.value += "|";
						}
					}
				}
			} else {
				alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
				return;
			}
		}
		<?php  } ?>
		document.form1.submit();
	} else {
		document.wishform.opts.value=document.form1.opts.value;
		if(typeof(document.form1.option1)!="undefined") document.wishform.option1.value=document.form1.option1.value;
		if(typeof(document.form1.option2)!="undefined") document.wishform.option2.value=document.form1.option2.value;

		window.open("about:blank","confirmwishlist","width=500,height=250,scrollbars=no");
		document.wishform.submit();
	}
	
}


</script>

<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

<form name="test">
<input type="hidden" name="s_category1" value="<?=$_POST[s_search_category][0]?>"/>
<input type="hidden" name="s_category2" value="<?=$_POST[s_search_category][1]?>"/>
<input type="hidden" name="s_category3" value="<?=$_POST[s_search_category][2]?>"/>
<input type="hidden" name="s_category4" value="<?=$_POST[s_search_category][3]?>"/>
</form>

<script>

	var s_cate1 = document.test.s_category1.value;
	var s_cate2 = document.test.s_category2.value;
	var s_cate3 = document.test.s_category3.value;
	var s_cate4 = document.test.s_category4.value;
	if(s_cate1){
		$.ajax({
			type: "GET",
			url: "../lib/proc.category.php",
			data: "category="+s_cate1+"&step=2"
		}).done(function ( data ) {
			$('#c_category2').html(data);
			if(s_cate2){
				$("#c_category2 option[value="+s_cate2+"]").attr("selected", "selected");
				$.ajax({
					type: "GET",
					url: "../lib/proc.category.php",
					data: "category="+s_cate2+"&step=3"
				}).done(function ( data ) {
					$('#c_category3').html(data);
					if(s_cate3){
						$("#c_category3 option[value="+s_cate3+"]").attr("selected", "selected");
						$.ajax({
							type: "GET",
							url: "../lib/proc.category.php",
							data: "category="+s_cate3+"&step=4"
						}).done(function ( data ) {
							$('#c_category4').html(data);
							if(s_cate4){
								$("#c_category4 option[value="+s_cate4+"]").attr("selected", "selected");
							}
						});
					}
				});
			}
		});
	}




</script>

</div>
</BODY>
</HTML>
