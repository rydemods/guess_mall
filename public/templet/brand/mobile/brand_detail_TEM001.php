<?php
$bridx  = $_REQUEST['bridx'] ? $_REQUEST['bridx'] : $_POST['bridx'];

if ( $bridx === "" ) {
	echo "<script type='text/javascript'>alert('해당 브랜드가 존재하지 않습니다.'); history.go(-1);</script>";
	exit;
}
$sort           = $_REQUEST['sort']?:"new";
$color_name = $_REQUEST['color'];
$size = $_REQUEST['size'];
$soldout = $_REQUEST ['soldout'];
$listnum        = $_REQUEST['listnum']?:10;

$search_word    = $_REQUEST['search_word']?:"";
$searchCategory = $_POST['s_search_category'];

$likeSearchForCategory = "";
$code=$searchCategory[0];
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');

if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";

$code=$code_a.$code_b.$code_c.$code_d;

if ( $code_a != "000" ) $likeSearchForCategory .= $code_a;
if ( $code_b != "000" ) $likeSearchForCategory .= $code_b;
if ( $code_c != "000" ) $likeSearchForCategory .= $code_c;
if ( $code_d != "000" ) $likeSearchForCategory .= $code_d;

$categoryHtml = makeCategorySelectHtml($code, "ss");
$subcode = substr($code,0,3);
$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
 					WHERE code_a = '".$subcode."' AND code_b != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";

$sub_result = pmysql_query($sub_sql);
$arrSecondDepthCate = array();  // 2차 카테고리
while ( $sub_row = pmysql_fetch_array($sub_result) ) {
	if ( $sub_row['code_c'] == "000" ) {
		// 2차 카테고리
		array_push($arrSecondDepthCate, array($sub_row['cate_sort'], $sub_row['code_a'], $sub_row['code_b'], $sub_row['code_c'], $sub_row['code_d'], $sub_row['code_name']));
	}
}

pmysql_free_result($sub_result);
sort($arrSecondDepthCate);
$secondCateHtml = "<option value=''>카테고리 All</option>";
foreach ( $arrSecondDepthCate as $arrCateInfo ) {
	$firstCateCode = $arrCateInfo[1];
	$secondCateCode = $arrCateInfo[2];
	$secondCateHtml .= "<option value='".$firstCateCode.$secondCateCode."000000'>".$arrCateInfo[5]."</option>";
}

// ===============================================================================
// 1차 카테고리 리스트
// ===============================================================================
$firstCateSql = " SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort
    					  FROM tblproductcode
 						  WHERE code_b = '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 						  ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";
$first_result = pmysql_query($firstCateSql);
$firstCateHtml = "<option value=''>성별 All</option>";
while ( $row = pmysql_fetch_array($first_result) ) {
	$arrFirstCategory[] = $row;
}
foreach ($arrFirstCategory as $key=>$val){
	$firstCateHtml .= "<option value='".$val['code_a']."'>".$val['code_name']."</option>";
}

$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
 					WHERE code_a = '".$code."' AND code_b != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";

// ======================================================================================
// 브랜드 정보 조회
// ======================================================================================

$sql  = "SELECT * FROM tblproductbrand WHERE bridx = {$bridx} ";
$row  = pmysql_fetch_object(pmysql_query($sql));

$brand_name = $row->brandname;
$brand_cate = $row->productcode_a;
$venderIdx  = $row->vender;


$sql  = "SELECT * ";
$sql .= "FROM tblvenderinfo_add ";
$sql .= "WHERE vender = {$venderIdx} ";
$row  = pmysql_fetch_object(pmysql_query($sql));

$brand_desc = $row->description;

// 롤링할 이미지
$arrRollingBannerImg = array();
for ( $i = 1; $i <= 10; $i++ ) {
	$varName = "b_img" . $i."_m";
	if ( !empty($row->$varName) ) {
		array_push($arrRollingBannerImg, $row->$varName);
	}
}

// ======================================================================================
// 브랜드 관련 상품 리스트
// ======================================================================================

$tmp_sort=explode("_",$sort);

$prod_sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.soldout, a.quantity, a.brand, a.maximage, a.minimage, a.tinyimage, a.over_minimage, ";
$prod_sql .= "a.mdcomment, a.review_cnt, a.icon, ";
$prod_sql .= "COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$prod_sql .= "(a.consumerprice - a.sellprice) as diffprice ";
$prod_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";

if ($code != "000000000000") {
	$prod_sql .= "LEFT JOIN tblproductlink c ON a.productcode = c.c_productcode ";
}
$prod_sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
							count(productcode) as marks_total_cnt
				FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$prod_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";

$prod_sql .= "WHERE a.display = 'Y' AND hotdealyn = 'N' ";
$prod_sql .= "AND a.brand = {$bridx} ";

if ( !empty($search_word) ) {
	$prod_sql .= "AND a.productname like '%{$search_word}%' ";
}
if ($code != "000000000000") {
	// 뒤에 '0'을 모두 제거
	$prod_sql .= "AND ( c.c_maincate = 1 AND c.c_category like '" . rtrim($code, "0") . "%' ) ";
}

//색상별 검색
$arrColor = explode(",", $color_name);
if($color_name){
	foreach($arrColor as $i => $v){
		if($i == 0){
			$prod_sql.= " AND (a.color_code = '".$v."'";
		}else{
			$prod_sql.= " OR a.color_code = '".$v."'";
		}
	}
	$prod_sql.=")";
// 	$prod_sql .= " AND a.color_code = '".$color_name."'";
}

//사이즈 검색
if($size){
	foreach($size as $i => $v){
		if($i == 0){
			$prod_sql.= " AND (a.sizecd LIKE '%".$v."%'";
		}else{
			$prod_sql.= " OR a.sizecd LIKE '%".$v."%'";
		}
	}
	$prod_sql.=")";
}

// 품절상품제외 2016-10-11
if($soldout == "1") {
	$prod_sql.= " AND a.quantity > 0 ";
}

if ( $tmp_sort[0]=="new" ) {
	// NEW
	$prod_sql .= "ORDER BY a.modifydate desc, a.date desc, a.pridx desc ";
}else if ( $tmp_sort[0]=="best" ) {
	// BEST
	$prod_sql .= "ORDER BY a.vcnt desc, a.pridx desc ";
}else if($tmp_sort[0]=="marks"){
	$prod_sql .= " ORDER BY COALESCE(re.marks, 0) desc, a.pridx desc ";
}else if($tmp_sort[0]=="like"){
	$prod_sql .= " ORDER BY hott_cnt desc, a.pridx desc ";
}else{
	if($sort=="price"){
		$prod_sql .= " ORDER BY a.start_no desc,a.sellprice ";
	}else if($sort=="price_desc"){
		$prod_sql .= " ORDER BY a.start_no desc,a.sellprice desc ";
	}	
}
$prod_sql .= ", a.regdate desc, a.modifydate desc";


$paging = new New_Templet_mobile_paging($prod_sql, 5, $listnum, 'GoPage', true);
$t_count    = $paging->t_count;
$gotopage   = $paging->gotopage;

$prod_sql   = $paging->getSql($prod_sql);
$total_cnt  = $paging->t_count;

// exdebug($prod_sql);

$arrProd = productlist_print($prod_sql, "W_015", null, $listnum);

$thisCate = getDecoCodeLoc( $sel_cate_code );

// 브랜드 설명에서 태그 제거(단 br태그는 살림)
$brand_desc = str_replace(array("<br>", "<br/>", "</br>"), chr(10), $brand_desc);
$brand_desc = strip_tags($brand_desc);
$brand_desc = str_replace(chr(10), "<br/>", $brand_desc);

?>

<!-- [D] 20160823 브랜드 상세 퍼블 -->

<main id="content" class="brand_wrap">

<section>
	<h2 class="page_local">
		<a href="<?=$Dir.MDir ?>" class="prev"></a>
		<span><?=brand_name($bridx)?></span>
	</h2>
</section>
<!-- 브랜드 비쥬얼 영역 -->
<div class="brand_visual_wrap">
	<ul>
		<? for ( $i = 0; $i < count($arrRollingBannerImg); $i++ ) { ?>
		<li><a href="javascript:;"><img src="/data/shopimages/vender/<?=$arrRollingBannerImg[$i]?>" alt=""></a></li>
		<?} ?>
	</ul>
</div>

<section class="goods_list_wrap">
	<div class="inner">
		<!-- 정렬 -->
		<div class="goods-range">
			<div class="select-def">
				<select class="SEARCH_SELECT first_category" onchange="ChangeCategory(this.value)">
					<?=$firstCateHtml ?>
				</select>
			</div>
			<div class="box">
				<div class="select-def">
					<select class="SEARCH_SELECT second_category" onchange="ChangeCategory2(this.value)">
					<?=$secondCateHtml ?>
					</select>
				</div>
			</div>
		</div>
		<!-- // 정렬 -->

		<!-- 상품 갯수 -->
		<div class="select-def mb-10">
			<select title="상품 갯수" id="prodivew" onchange="ChangeProdView(this.value)">
				<?foreach ($prod_view_mcode as $key => $val){ ?>
				<option value="<?=$key ?>"<?=$listnum==$key?"selected":""?>><?=$val ?></option>
				<?} ?>
			</select>
		</div>
		<!-- //상품 갯수 -->

		<!-- 상품검색정렬 -->
		<div class="list_sort">
			<ul>
				<li>
					<div><input type="checkbox" id="sold-out" name="sold-out" class="chk_agree checkbox_custom" value="" > <label for="sold-out">품절상품제외</label></div>
				</li>
				<li><a href="" class="btn-search-pop">상세검색</a></li>
				<li><a href="" class="btn-sorting-search">신상품</a></li>
			</ul>
		</div>
		<!-- // 상품검색정렬 -->
	</div> <!-- // .inner -->

	<!-- 상품 리스트 영역 -->
	<div class="product-list">
		<div class="goods-list">
			<div class="goods-list-item">
				<!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
				<ul>
					<li class="grid-sizer"></li>
					<?=$arrProd[0]?>
				</ul>
			</div>
		</div>
		<!-- // 상품 리스트 영역 -->

		<!-- 페이징 -->
		<div class="list-paginate mt-10 mb-30">
		<?php
			if( $paging->pagecount > 1 ){
				echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
			}
		?>
		</div>
		<!-- // 페이징 -->
	</div>

	<!-- 상세검색 팝업 -->
	<div class="layer-dimm-wrap pop-detail-search">
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">상세검색</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content">
				<div class="sorting">
					<section class="sorting-size on">
						<h6>사이즈</h6>
						<ul>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_220" name="sizechk" ids="220"><span>220</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_225" name="sizechk" ids="225"><span>225</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_230" name="sizechk" ids="230"><span>230</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_235" name="sizechk" ids="235"><span>235</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_240" name="sizechk" ids="240"><span>240</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_245" name="sizechk" ids="245"><span>245</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_250" name="sizechk" ids="250"><span>250</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_255" name="sizechk" ids="255"><span>255</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_260" name="sizechk" ids="260"><span>260</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_265" name="sizechk" ids="265"><span>265</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_270" name="sizechk" ids="270"><span>270</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_275" name="sizechk" ids="275"><span>275</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_280" name="sizechk" ids="280"><span>280</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_285" name="sizechk" ids="285"><span>285</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_290" name="sizechk" ids="290"><span>290</span></label></li>
							<li><label><input type="checkbox" class="CLS_sizechk" id="size_300" name="sizechk" ids="300"><span>300</span></label></li>
						</ul>
						<a class="btn-toggle" href="javascript:void(0);" title="접어놓기"><span>SIZE 정렬</span></a>
					</section>
					<section class="sorting-color on">
						<h6>색상</h6>
						<!-- (D) 투명, 흰색 등 색이 밝아 체크 색상이 검은색인 것은 li에 class="light" 을 추가합니다. -->
						<ul>
						<?
						$checked['color'][$color_name] = "checked";
						$arrDbColorCode = dataColor();
						?>
						<?foreach($arrDbColorCode as $ck => $cv){?>
							<li id="color_<?=$cv->cno?>" ><label><input type="checkbox" name="smart_search_color" id="smart_search_color" idx="<?=$cv->cno?>" value="<?=$cv->color_name?>" <?=$checked[color][$cv->color_name]?> hidden>
								<span><img src="../static/img/test/<?=$cv->color_img?>" alt="<?=$cv->color_name?>" title="<?=$cv->color_name?>"></span></label>
							</li>
						<?} ?>
						</ul>
						<a class="btn-toggle" href="javascript:void(0);" title="접어놓기"><span>COLOR 정렬</span></a>
					</section>
					<div class="btn-wrap"><button class="btn-submit" type="submit"><span>적용하기</span></button></div>
				</div>
			</div>
		</div>
	</div>
	<!-- // 상세검색 팝업 -->

	<!-- 정렬방식 팝업 -->
	<div class="layer-dimm-wrap pop-sorting-search">
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">정렬방식</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content">
				<div class="sorting">
					<section class="sorting-wrap">
						<ul>
							<li>
								<div>
									<label for="sorting_check01">신상품</label>
									<input type="radio" id="sorting_check01" name="sorting_check" value="new">
								</div>
							</li>
							<li>
								<div>
									<label for="sorting_check02">인기순</label>
									<input type="radio" id="sorting_check02" name="sorting_check" value="best">
								</div>
							</li>
							<li>
								<div>
									<label for="sorting_check03">상품평순</label>
									<input type="radio" id="sorting_check03" name="sorting_check" value="marks">
								</div>
							</li>
							<li>
								<div>
									<label for="sorting_check04">좋아요순</label>
									<input type="radio" id="sorting_check04" name="sorting_check" value="like">
								</div>
							</li>
							<li>
								<div>
									<label for="sorting_check04">낮은가격순</label>
									<input type="radio" id="sorting_check05" name="sorting_check" value="price">
								</div>
							</li>
							<li>
								<div>
									<label for="sorting_check04">높은가격순</label>
									<input type="radio" id="sorting_check06" name="sorting_check" value="price_desc">
								</div>
							</li>
						</ul>
					</section>

					<div class="btn-wrap"><button class="btn-submit" type="submit"><span>적용하기</span></button></div>
				</div>
			</div>
		</div>
	</div>
	<!-- // 정렬방식 팝업 -->
</section>
</main>

<!-- // [D] 20160823 브랜드 상세 퍼블 -->

<form name="formSearch" id="formSearch" method="GET" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return submitForm(this);">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=bridx value="<?=$bridx ?>">
    <input type=hidden name=sort id="sort" value="<?=$sort?>">
    <input type="hidden" name="s_search_category[]" id="s_search_category" value = "<?=$searchCategory[0]?>">
    <input type="hidden" name="color"/>
    <input type="hidden" name="size" id="size" value="<?=$size ?>">
    <input type="hidden" name="soldout" id="soldout" value="<?=$soldout ?>" />
</form>


<!-- 이전 소스 백업 -->
<div class="sub-title hide">
    <h2><?=$brand_name?></h2>
    <a class="btn-prev" href="<?=$Dir.MDir?>><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
</div>

<!-- 브랜드 정보 -->
<div class="js-brand-info">
    <div class="brand-info-btn">
        <ul>
            <li><button class="js-btn-toggle" type="button"><span class="ir-blind">브랜드 설명 펼쳐보기/접어놓기</span></button></li>
            <li>
                <!-- (D) 위시브랜드 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
                <button class="btn-wishlist <?=$onBrandWishClass?>" type="button" onClick="javascript:setBrandWishList(this, '<?=$bridx?>', '<?=urldecode(getUrl())?>')"><span class="ir-blind">위시브랜드 담기/버리기</span></button>
            </li>
        </ul>
    </div>
    <p class="brand-info-text">
        <?=$brand_desc?>
    </p>
</div>
<!-- // 브랜드 정보 -->

<!-- 브랜드 이미지 -->
<?=$rolling_html?>
<!-- // 브랜드 이미지 -->

<!-- 정렬 -->
<div class="goods-range hide">
    <?=makeCategorySelectHtml($cate_code, $brand_cate);?>
    <div class="container ">
		<div class="select-def">
			<select onchange="javascript:ChangeSort2(this);">
				<option value="order" <?php if ( $sort == "order" ) echo "selected"; ?>>NEW</option>
				<option value="best" <?php if ( $sort == "best" ) echo "selected"; ?>>BEST</option>
				<option value="sale" <?php if ( $sort == "sale" ) echo "selected"; ?>>SALE</option>
				<option value="rcnt_desc" <?php if ( $sort == "rcnt_desc" ) echo "selected"; ?>>REVIEW</option>
				<option value="price" <?php if ( $sort == "price" ) echo "selected"; ?>>LOWPRICE</option>
				<option value="price_desc" <?php if ( $sort == "price_desc" ) echo "selected"; ?>>HIGHPRICE</option>
			</select>
		</div>
    </div>
</div>
<!-- // 정렬 -->

<!-- 상품 리스트 -->
<div class="goods-list hide">
    <div class="container">
        <p class="note">총 <?=number_format($t_count)?>개의 상품이 진열되어 있습니다.</p>
        <div class="list-type">
            <button class="js-goods-type on" data-type="double"><img src="./static/img/btn/btn_goods_list_type_double.png" alt="2열로 보기"></button>
            <button class="js-goods-type" data-type="single"><img src="./static/img/btn/btn_goods_list_type_single.png" alt="1열로 보기"></button>
        </div>
    </div>
    <?=$arrProd[0]?>

    <div class="list-paginate mt-10 mb-30">
        <?php
            if( $paging->pagecount > 1 ){
                echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
            }
        ?>
    </div>
</div>
<!-- // 상품 리스트 -->
<!-- // 이전 소스 백업 -->

<script type="text/javascript">
var bridx = "<?=$bridx ?>";
var soldout = "<?=$soldout ?>";
var str = "<?=$searchCategory[0]?>";
var firstCategory = str.substr(0,3);
var secondCategory = str.substr(3,6);
var color_name;
var sizeCode = [];
var colorCode = [];

$(document).ready(function() {

	//정렬 Default
	$("#sorting_check01").prop("checked", true);

	//카테고리 선택
	$(".first_category").val(firstCategory).prop("selected", true);
	if(secondCategory == ""){
		$(".second_category").val("").prop("selected", true);
	}else{
		$(".second_category").val("<?=$searchCategory[0]?>").prop("selected", true);
	}


	//품절상품 제외 선택
	if(soldout == "1"){
		$("#sold-out").prop("checked", true);
	}

	//투명, 흰색 등 색이 밝아 체크 색상이 검은색인 것은 li에 class="light" 을 추가
	$("input[name=smart_search_color]").click(function() {
		color_name = $(this).val();
		if(color_name == "white" || color_name == "transparent"){
			var idx = $(this).attr("idx");
			$("#color_"+idx).addClass("light");
		}
	});

	//조건 적용
	$(".btn-submit").click(function() {
		//정렬 value
		var sort = $(":radio[name=sorting_check]:checked").val();
		$("input[name=sort]").val(sort);

		//색상 value
		if(typeof color_name == "undefined"){
			color_name = "";
		}else{
			$("input[name=color]").val(color_name);
		}

		//사이즈 value
		if(sizeCode != ""){
			//배열에 code가 있는 경우 삭제
			var codeSize = sizeCode.length;
			sizeCode.splice(0,codeSize);
		}

		if(typeof $("input[name=sizechk]:checked").val() == "undefined"){
			sizeCode.push("");
		}else{
			$("input[name=sizechk]:checked").each(function() {
				sizeCode.push($(this).attr("ids"));
				$("input[name=size]").val(sizeCode);
			});
		}

		if(typeof $("input[name=smart_search_color]:checked").val() == "undefined"){
			$("input[name=color]").val("");
		}else{
			$("input[name=smart_search_color]:checked").each(function() {
				colorCode.push($(this).val());
				$("input[name=color]").val(colorCode);
			});			
		}

        //2016-09-25 페이징시 정렬 방식 유지를 위해 추가
		$("#sort").val(sort);
        if(sort=="new"){
			sortname='신상품';
		}else if(sort=="best"){
			sortname='인기순';
		}else if(sort=="marks"){
			sortname='상품평순';
		}else if(sort=="like"){
			sortname='좋아요순';
		}else if(sort=="price"){
			sortname='낮은가격순';
		}else if(sort=="price_desc"){
			sortname='높은가격순';
		}		
		$(".btn-sorting-search").html(sortname);

		var param = {"bridx":bridx, "color":colorCode, "sort":sort, "size":sizeCode};
		$.ajax({
			type: "POST",
			url: "../m/ajax_brand_search.php",
			data: param,
			dataType:"HTML",
		    error:function(request,status,error){
		       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		    }
		}).done(function(html){
			$(".product-list").html(html);
			$(".btn-close").trigger("click");
		});

	});

	//좋아요
	$(".btn-like").click(function() {
		var productCode = $(this).attr("ids");
		var likeType = $(this).attr("type");
		var memId = "<?=$_ShopInfo->getMemid()?>";

		if(memId != ""){
			$.ajax({
				type: "POST",
				url: "../front/product_like_proc.php",
				data: "code="+productCode+"&liketype="+likeType+"&section=product",
				dataType:"JSON"
			}).done(function(data){
				$("#like_count_"+productCode).html("<strong>좋아요</strong>"+data[0]['hott_cnt']);
				if(data[0]['section'] == "product"){
					$("#like_"+productCode).attr("class","comp-like btn-like on");
					$("#like_"+productCode).attr("type","on");
				}else{
					$("#like_"+productCode).attr("class","comp-like btn-like");
					$("#like_"+productCode).attr("type","off");
				}
			});
		}else{
			//로그인 상태가 아닐때 로그인 페이지로 이동
			var url = "../m/login.php?chUrl=/";
			$(location).attr('href',url);
		}
	});

	//품정상품 제외
	$("#sold-out").change(function() {
	    var soldout = "";
	    if($("#sold-out").prop('checked')) soldout = "1";
	    else soldout = "0";
	    document.formSearch.soldout.value = soldout;
		document.formSearch.block.value="";
		document.formSearch.gotopage.value="";
		document.formSearch.submit();

	});

});

function ChangeCategory(val){
	var sort = $(":radio[name=sorting_check]:checked").val();
	var param = {"bridx":bridx, "color":color_name, "sort":sort, "size":sizeCode, "code":val};

	$.ajax({
		type: "POST",
		url: "../m/ajax_category_list.php",
		data: "code="+val+"&category_type=second",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".second_category").html(html);
		$("#s_search_category").val(val);
	});

	$.ajax({
		type: "POST",
		url: "../m/ajax_brand_search.php",
		data: param,
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".product-list").html(html);
	});
}

function ChangeCategory2(val){
	var code = val;
	if(code == ""){
		code = $(".second_category option:selected").val();
	}
	var sort = $(":radio[name=sorting_check]:checked").val();
	var param = {"bridx":bridx, "color":color_name, "sort":sort, "size":sizeCode, "code":val};


	$.ajax({
		type: "POST",
		url: "../m/ajax_brand_search.php",
		data: param,
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".product-list").html(html);
		if(val != ""){
			$("#s_search_category").val(val);
		}else{
			$("#s_search_category").val(firstCategory);
		}
	});
}

function ChangeProdView(val) {
	document.formSearch.block.value="";
	document.formSearch.gotopage.value="";
	document.formSearch.listnum.value=val;
	document.formSearch.submit();
}

function GoPage(block,gotopage) {
	document.formSearch.block.value=block;
	document.formSearch.gotopage.value=gotopage;
	document.formSearch.submit();
}
</script>

