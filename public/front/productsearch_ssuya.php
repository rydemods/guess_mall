<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once ($Dir."lib/product.class.php");
$product = new PRODUCT();

// 원래 POST 였으나, 통계때문에 GET 으로 변경 후, POST에 값 넣어줌.
foreach ($_GET as $key => $val) {
    $_POST[$key] = $val;
}
//테그
$tag = $_GET['tag'];

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
$s_sort = $_POST[s_sort]?$_POST[s_sort]:"";

#sleep('4');
$searchCategory = $_POST['s_search_category'];
$searchBrand = $_POST['s_search_brand'];
//$searchMinPrice = $_POST['s_search_min_price']?$_POST['s_search_min_price']:1;
//$searchMaxPrice = $_POST['s_search_max_price']?$_POST['s_search_max_price']:500000;
$searchColor = $_POST['s_search_color'];

$searchtype = array();
$addQuery = array();
$addQuery[] = "a.display = 'Y' ";
$addQuery[] = "(  a.mall_type = 0 OR a.mall_type = '".$_ShopInfo->getAffiliateType()."' ) "; // 해당 몰관련 상품만 보여줌 (2015.11.10 - 김재수)

if($_POST['reSearch']){
	$checked['reSearch'] = "checked";
	$addQuery[] = "(".str_replace('WHERE ','',str_replace('\\','',$_POST['addwhere'])).")";
}

if($search){
	$brand = "";
	$search = strtoupper($search);
	//20150401 상품명으로만 검색이 가능하게 해달라고 요청
	$sword_search = "
	((UPPER(a.productname) LIKE '%{$search}%' OR UPPER(a.keyword) LIKE '%{$search}%') 
	OR a.productcode LIKE '{$search}%' 
	OR UPPER(a.production) LIKE '%{$search}%' 
	OR UPPER(a.model) LIKE '%{$search}%' 
	OR UPPER(a.selfcode) LIKE '%{$search}%' 
	OR UPPER(a.mdcomment) LIKE '%{$search}%' 
	OR UPPER(a.content) LIKE '%{$search}%') ";
	
	//$sword_search = "(UPPER(a.productname) LIKE '%{$search}%')";
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

if( $tag ){
	$addQuery[] = "UPPER( a.keyword ) LIKE '%".$tag."%' ";
	$search = $tag;
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
	$tmp_sort=explode("_",$s_sort);
	
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}

	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.maximage, a.minimage,a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
	
	$sql.= $addsortsql;
	
	$sql.= "FROM (select *, case when (buyprice - sellprice) <= 0 then 0 else (buyprice - sellprice) end as saleprice from tblproduct) AS a  ";
	
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";

	$sql.= $strAddQuery." ";	

	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="rcnt") $sql.= "ORDER BY a.review_cnt ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="saleprice") $sql.= "ORDER BY a.saleprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
    // 등록일순으로 변경함. 만약 수정일순으로 변경한다면 modifydate desc 로 변경..2015-11-30 jhjeong
    elseif($tmp_sort[0]=="opendate") $sql.= "ORDER BY a.regdate DESC, pridx ASC ";
	elseif($tmp_sort[0]=="dcprice") $sql.= "ORDER BY case when consumerprice>0 then  100 - cast((cast(sellprice as float)/cast(consumerprice as float))*100 as integer) else 0 end desc ";
	else if($tmp_sort[0]=="best"){

		$sql.= "ORDER BY a.start_no desc ";
	}else {
		$sql.= "ORDER BY a.start_no asc, modifydate desc";
	}
	$paging = new Tem001_saveheels_Paging($sql,10,16,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = $paging->getSql($sql);
	echo $sql;
	$result_T=pmysql_query($sql,get_db_conn());

}else{
	$t_count = 0;
}
?>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

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
	<?
	$subTop_flag = 3;
	//include ($Dir.MainDir."sub_top.php");
	?>

	<div class="containerBody sub_skin">
<?if ($brand) {?>
	<h3 class="title mt_20 ">
		<?if($brand){echo $strBrand[brandname];}?>
		<p class="line_map"><a>홈</a> &gt; <a class="on"><?if($brand){echo $strBrand[brandname];}?></a></p>
	</h3>
<?} else {?>
	<h3 class="title mt_20 ">
		상품검색
		<p class="line_map"><a>홈</a> &gt; <a class="on">상품검색</a></p>
	</h3>
<?}?>
<form name="frmSearch" method="GET" action="productsearch_ssuya.php">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_sort value="<?=$s_sort?>">
<input type=hidden name=addwhere value = "<?=$strAddQuery?>">
<input type=hidden name=brand value = "<?=$brand?>">

			<div class="search_input_area">
				<div class="inner">
				<table cellpadding="0" cellspacing="0" border="0" width="685">
					<colgroup>
						<col style="width:155px" /><col style="width:430px" /><col style="width:100px" />
					</colgroup>
					<tr>
						<th style="line-height:1.5">search</th>
						<td class="input_t"><input type="text" name="search" id="search" value="<?=$search?>" onkeydown="javascript: if (event.keyCode == 13) {GoSearch();}" /><a href="javascript:GoSearch();" class="btn_D on va_t">상품검색</a></td>
						<td class="re"><input type="checkbox" name="reSearch" id="reSearch" value="1" <?=$checked['reSearch']?> /> 결과 내 재검색</td>
					</tr>
				</table>
				</div>
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
						<li><a href="javascript:ChangeSort('best')" <?if($s_sort == 'best'){?> class="on" <?}?> >베스트순</a></li>
						<li><a href="javascript:ChangeSort('opendate')" <?if($s_sort == 'opendate'){?> class="on" <?}?> >신상품순</a></li>
						<li><a href="javascript:ChangeSort('rcnt_desc')" <?if($s_sort == 'rcnt_desc'){?> class="on" <?}?> >리뷰순</a></li>
						<li><a href="javascript:ChangeSort('saleprice_desc')" <?if($s_sort == 'saleprice_desc'){?> class="on" <?}?> >할인률순</a></li>
						<li><a href="javascript:ChangeSort('price_desc')" <?if($s_sort == 'price_desc'){?> class="on" <?}?> >높은가격순</a></li>
						<li><a href="javascript:ChangeSort('price')" <?if($s_sort == 'price'){?> class="on" <?}?> >낮은가격순</a></li>
					</ul>
				</div>
			</div>
            <div class="goods_list">
                <div class="block">
                    <ul class="product_list">
<?	
	$p_cnt	= 0;
while($row=pmysql_fetch_object($result_T)) { 

	if ($p_cnt > 0 && ($p_cnt+1) <= count((array)$row)) {
		if ($p_cnt%4 == 0) {
			echo "</ul><ul class='product_list'>";
		}
	}
	$p_cnt++;

	##### 쿠폰에 의한 가격 할인
	$cou_data = couponDisPrice($row->productcode);
	if($cou_data['coumoney']){
		$row->sellprice = $row->sellprice-$cou_data['coumoney'];
		$row->dc_type = $cou_data["goods_sale_money"];
	}

	/*$groupPriceList = $product->getProductGroupPrice($row->productcode);

	if ($groupPriceList) { // 일반 및 도매회원 금액 세팅시 로그인 되어잇는 user 등급에 따라 판매 금액 적용
		$row->sellprice = $groupPriceList[sellprice];
		$row->consumerprice = $groupPriceList[consumerprice];
	}*/

	##### 쿠폰에 의한 가격 할인
	
	##### 오늘의 특가, 타임세일에 의한 가격
	$spesell = getSpeDcPrice($row->productcode);
	if($spesell){
		$row->sellprice = $spesell;
	}
	##### //오늘의 특가, 타임세일에 의한 가격
	$dc_rate = getDcRate($row->consumerprice,$row->sellprice);

	// 이미지 tinyimage => minimage로 변경 2015 11 09 유동혁
	if (strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
		// 이미지 오류로 변경 2015 11 09 유동혁
		//$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($row->tinyimage);
		$imgsrc = $Dir.DataDir."shopimages/product/".$row->minimage;
	}else{
		$imgsrc = $Dir."images/common/noimage.gif";
	}
	
	/*if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
		$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($row->tinyimage);
	}else if(strlen($row->tinyimage)>0 && file_exists($Dir.$row->tinyimage)){
		$imgsrc = $Dir.$row->tinyimage;
	}else if(strlen($row->maximage)>0 && file_exists($Dir.$row->maximage)){
		$imgsrc = $Dir.$row->maximage;
	}else{
		$imgsrc = $Dir."images/no_img.gif";
	}*/
?>
                        <li>
							<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>"><img src="<?=$imgsrc?>" alt="" class='img-size-list' >
							<h4 class="tit_goods"><?=$row->productname?></h4>
							<p class="stit_goods">
<?php
			if( $row->overseas_type == '1' ){
?>
								<img src="../images/main/ico_outdely.png" alt="해외배송" > 
<?php
			}
			if( strlen( trim( $row->mdcomment ) ) > 0 ) { 
				echo $row->mdcomment; 
			}
?>
							</p>
							<table >
							<caption>가격정보</caption>
							<tr>
								<th scope="row">정상가</th>
								<td class="p1"><?=number_format( $row->buyprice )?>원</td>
							</tr>
							<tr>
								<th scope="row">최저가</th>
								<td class="p2"><?=number_format( $row->consumerprice )?>원</td>
							</tr>
							<tr>
								<th scope="row" class="mem_price">교육할인가</th>
<?php
			if( strlen( $_ShopInfo->getMemid() ) > 0 ) {
?>
								<td class="mem_price"><?=number_format( $row->sellprice )?>원</td>
<?php
			} else {
?>
								<td class="mem_price">	<img src="../images/common/ico_memberonly_sub.gif" alt="members only" ></td>
<?php
			}
?>
							</tr>
							</table>
	                        </a>
                        </li>
<?}?>
					</ul>
				</div>
			</div><!-- //검색 리스트 -->
			
		<div class="paging mt_30">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div>
	</div>
<!-- 미리보기 팝업 -->
<div id="divDetail" style="position: fixed; top:1px; left:50%; margin-left:-452px; width: 902px;height: 555px;z-index: 30; background-color: #ffffff;border: 1px solid;display:none">		
</div>
<div id="overDiv" style="position:absolute;top:0px;left:0px;z-index:100;display:none;" class="alpha_b60" ></div>
<div class="popup_preview_warp" style="margin-left: 50%;left: -459px;display:none;" ></div>

<script>

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
	
	//if(gbn!="wishlist") {
	if(gbn!="wishlist" || gbn=='') {		
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
/**
	if(gbn=="ordernow") {
		document.form1.ordertype.value="ordernow";
	}
**/
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
//alert(document.form1.optionArr.value +"----"+document.form1.quantityArr.value+"----"+document.form1.priceArr.value);
	if(gbn=="ordernow") {
		//alert("바로주문");
		document.search_basket.productcode.value	= document.form1.productcode.value;

		if(document.form1.optionArr.value==''){
			document.search_basket.optionArr.value		= '';
		}else{
			document.search_basket.optionArr.value		= document.form1.optionArr.value;
		}

		if(document.form1.quantityArr.value==''){
			
			document.search_basket.quantity.value		= document.form1.quantity.value;	// 들어가는데..?
		}else{
			document.search_basket.quantityArr.value		= document.form1.quantityArr.value;
		}
		
		if(document.form1.priceArr.value==''){
			document.search_basket.priceArr.value		= '';
		}else{
			document.search_basket.priceArr.value		= document.form1.priceArr.value;
		}	

		document.search_basket.ordertype.value= "ordernow";
		document.search_basket.action="../front/basket.php";
		document.search_basket.submit();
		return;

	}

	//if( gbn==''  || temp2 ==''){
	if( gbn==''){
		
		document.search_basket.productcode.value	= document.form1.productcode.value;

		if(document.form1.optionArr.value==''){
			document.search_basket.optionArr.value		= '';
		}else{
			document.search_basket.optionArr.value		= document.form1.optionArr.value;
		}

		if(document.form1.quantityArr.value==''){
			
			document.search_basket.quantity.value		= document.form1.quantity.value;	// 들어가는데..?
		}else{
			document.search_basket.quantityArr.value		= document.form1.quantityArr.value;
		}
		
		if(document.form1.priceArr.value==''){
			document.search_basket.priceArr.value		= '';
		}else{
			document.search_basket.priceArr.value		= document.form1.priceArr.value;
		}	
		//alert("장바구니");
		document.search_basket.action="../front/confirm_basket.php";
		document.search_basket.target="confirmbasketlist_t";
		window.open("about:blank","confirmbasketlist_t","width=401,height=309,scrollbars=no,resizable=no, status=no,"); 
		document.search_basket.submit();
		return;
		
	}
/**
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
**/
	

	

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
		//document.wishform.submit();
	}
	
}


</script>

<div id="create_openwin" style="display:none"></div>
<!-- -->
<form name="search_basket" method="post">
	<input type="hidden" name="productcode" />
	<input type="hidden" name="optionArr" />
	<input type="hidden" name="quantityArr" />
	<input type="hidden" name="priceArr" />
	<input type="hidden" name="quantity" />
	<input type="hidden" name="ordertype" />
</form>

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
<?php
include ($Dir."lib/bottom.php") 
?>
</BODY>
</HTML>
