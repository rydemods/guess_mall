<?php
$bridx          = $_REQUEST['bridx'];

if ( $bridx === "" ) {
    echo "<script type='text/javascript'>alert('해당 브랜드가 존재하지 않습니다.'); history.go(-1);</script>";
    exit;
}

$sort           = $_REQUEST['sort']?:"new";
$color_name = $_REQUEST['color'];
$size = $_REQUEST['size'];
//$sort           = $_REQUEST['sort'];
$soldout = $_REQUEST['soldout'];
$selected[soldout][$soldout]  = 'checked';

if ( $isMobile ) {
    $listnum        = $_REQUEST['listnum']?:10;
} else {
    $listnum        = $_REQUEST['listnum']?:20;
}

$search_word    = $_REQUEST['search_word']?:"";
$sel_cate_code      = $_REQUEST['sel_cate_code'];
$cate_code_a    = substr($cate_code, 0, 3);
$cate_code_b    = substr($cate_code, 3, 3);
$cate_code_c    = substr($cate_code, 6, 3);
$cate_code_d    = substr($cate_code, 9, 3);

// ======================================================================================
// 브랜드 정보 조회
// ======================================================================================

$sql  = "SELECT * FROM tblproductbrand WHERE bridx = {$bridx} ";
$row  = pmysql_fetch_object(pmysql_query($sql));

$brand_name = $row->brandname;
$brand_cate = $row->productcode_a;
$venderIdx  = $row->vender;

/*
if ( empty($cate_code) ) {
    $cate_code = $brand_cate;
}
*/

$sql  = "SELECT * ";
$sql .= "FROM tblvenderinfo_add ";
$sql .= "WHERE vender = {$venderIdx} ";
$row  = pmysql_fetch_object(pmysql_query($sql));

$brand_desc = $row->description;

// 롤링할 이미지
$arrRollingBannerImg = array();
for ( $i = 1; $i <= 10; $i++ ) {
    $varName = "b_img" . $i;

    if ( !empty($row->$varName) ) {
        array_push($arrRollingBannerImg, $row->$varName);
    }
}

if ( $isMobile ) {
    $rolling_html = '';
    if ( count($arrRollingBannerImg) >= 1 ) {
        $rolling_html = '
                <div class="js-brand-visual">
                    <div class="js-brand-visual-list">
                        <ul>';

        $bannerCount = 0;
        foreach ( $arrRollingBannerImg as $key => $val ) {
            if ( !empty($val) ) {
                $rolling_html .= '<li class="js-brand-visual-content"><a href="javascript:;"><img src="/data/shopimages/vender/' . $val . '" alt=""></a></li>';
                $bannerCount++;
            }
        }

        $rolling_html .= '
                        </ul>
                    </div>';

        if ( $bannerCount >= 2 ) {
            $rolling_html .= '
                        <button class="js-brand-visual-arrow" data-direction="prev" type="button"><img src="./static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
                        <button class="js-brand-visual-arrow" data-direction="next" type="button"><img src="./static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>';
        }

        $rolling_html .= '
                </div>';
    }
}


// ======================================================================================
// 브랜드 관련 상품 리스트
// ======================================================================================

$tmp_sort=explode("_",$sort);

$prod_sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.soldout, a.quantity, a.brand, a.maximage, a.minimage, a.tinyimage, a.over_minimage, ";
$prod_sql .= "a.mdcomment, a.review_cnt, a.icon, a.relation_tag , a.prodcode, a.colorcode, a.sizecd, a.brandcd, a.brandcdnm, ";
$prod_sql .= "COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$prod_sql .= "(a.consumerprice - a.sellprice) as diffprice ";
$prod_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";

if ( !empty($sel_cate_code) ) {
    $prod_sql .= "LEFT JOIN tblproductlink c ON a.productcode = c.c_productcode ";
}
$prod_sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
							count(productcode) as marks_total_cnt
				FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$prod_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";

$prod_sql .= "WHERE a.display = 'Y' AND a.hotdealyn = 'N' AND a.brand  = {$bridx} ";

if ( !empty($search_word) ) {
    $prod_sql .= "AND a.productname like '%{$search_word}%' ";
}

if ( !empty($sel_cate_code) ) {
    // 뒤에 '0'을 모두 제거
    $prod_sql .= "AND ( c.c_maincate = 1 AND c.c_category like '" . rtrim($sel_cate_code, "0") . "%' ) ";
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
	//$prod_sql .= " AND a.color_code = '".$color_name."'";
}

//사이즈 검색
$arrSize = explode(",", $size);
//exdebug($brand);
if($size){
	foreach($arrSize as $i => $v){
			if($i == 0){
			$prod_sql.= " AND (a.sizecd LIKE '%".$v."%'";
		}else{
			$prod_sql.= " OR a.sizecd LIKE '%".$v."%'";
		}
	}
	$prod_sql.=")";
}

// 품절상품제외 2016-10-10
if($soldout == "1") {
    $prod_sql.= " AND a.quantity > 0 ";
}

if ( $tmp_sort[0] == "rcnt" ) {
    // REVIEW
    $prod_sql .= " ORDER BY a.review_cnt ".$tmp_sort[1];
} else if ( $tmp_sort[0]=="price" ) {
    // PRICE
    $prod_sql .= " ORDER BY a.sellprice ".$tmp_sort[1];
} else if ( $tmp_sort[0]=="best" ) {
    // BEST
    $prod_sql .= " ORDER BY a.vcnt desc, a.pridx desc ";
} else if ( $tmp_sort[0]=="sale" ) {
    // SALE (정가 - 판매가 값이 큰순으로 정렬)
    $prod_sql .= " ORDER BY diffprice desc";
}else if($tmp_sort[0]=="marks"){
	$prod_sql .= " ORDER BY COALESCE(re.marks, 0) desc, a.pridx desc";
}else if($tmp_sort[0]=="like"){
	$prod_sql .= " ORDER BY hott_cnt desc, a.pridx desc ";
} else {
	if($sort=="price"){
		$prod_sql .= " ORDER BY a.start_no desc,a.sellprice ";
	}else if($sort=="price_desc"){
		$prod_sql .= " ORDER BY a.start_no desc,a.sellprice desc ";
	}else{
		// NEW
		$prod_sql .= " ORDER BY a.modifydate desc, a.date desc, a.pridx desc ";
	}
}
$prod_sql .= ", a.regdate desc, a.modifydate desc";

if ( $isMobile ) {
    $paging = new New_Templet_mobile_paging($prod_sql, 5, $listnum, 'GoPage', true);
} else {
    $paging = new New_Templet_paging($prod_sql,10,$listnum,'GoPage',true);
}
$t_count    = $paging->t_count;
$gotopage   = $paging->gotopage;

$prod_sql   = $paging->getSql($prod_sql);
$total_cnt  = $paging->t_count;

// exdebug($prod_sql);

if ( $isMobile ) {
    $arrProd = productlist_print($prod_sql, "W_015", null, $listnum);
} else {
    $arrProd = productlist_print($prod_sql, "W_010", null, $listnum);
}

$thisCate = getDecoCodeLoc( $sel_cate_code );

if ( $isMobile ) {
//     include($Dir.TempletDir."brand/mobile/brand_detail_TEM001.php");
} else {
?>
<main id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li>BRAND</li>
			<li class="on"><?=$brand_name?></li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="goods-list">
		<!-- 상품리스트 - 사이드바 -->

		<!-- LNB -->
		<?php include($Dir.TempletDir."product/product_category_TEM001.php");?>
		<!-- //LNB -->

		<!-- // 상품리스트 - 사이드바 -->



		<?php
			// 브랜드 제목이 없고 롤링이미지가 없는 경우
			$hideClass = "";
			if ( empty($brand_name) && count($arrRollingBannerImg) == 0 ) {
				$hideClass = "hide";
			}
		?>
		<!-- 브랜드 비쥬얼 영역 -->
		<div class="brand_visual with-btn-rolling mb-20 <?=$hideClass?>">
			<?if(count($arrRollingBannerImg) > 0){ ?>
			<ul>
				<?php for ( $i = 0; $i < count($arrRollingBannerImg); $i++ ) { ?>
				<li><a href="javascript:;"><img src="/data/shopimages/vender/<?=$arrRollingBannerImg[$i]?>" alt=""></a></li>
				<?php } ?>
			</ul>
			<?} ?>
		</div>
		<?
		$thisCateName = '';
		$thisCateCnt = count( $thisCate );
		if( $thisCateCnt == 0 ){ // 1차 카테고리
			$thisCateName = "ALL";
		} else if( $thisCateCnt == 1 ){ // 1차 카테고리
			$thisCateName = $thisCate[0]->code_name;
		} else if($thisCateCnt == 2){
			$thisCateName = $thisCate[1]->code_name;
		} else if( $thisCateCnt == 3 ){
			$thisCateName = $thisCate[2]->code_name;
		} else if( $thisCateCnt == 4 ){
			$thisCateName = $thisCate[3]->code_name;
		}
		?>
		<!-- 상품리스트 - 상품 -->
		<section class="goods-list-item">
			<h3>
                <?=$thisCateName?><!--  <span class="num">(<?=number_format( $total_cnt )?>)</span>-->
                <label><input type=checkbox class="CLS_brandchk" id=chksoldout onchange="ChangeList()" <?=$selected[soldout]["1"]?>><span>품절 상품 제외</span></label>
            </h3>
			<div class="comp-select sorting">
				<select title="상품정렬순"  id="sortlist" onchange="ChangeSort(this.value)">
					<option value="new"<?=$sort=="new"?"selected":""?>>신상품</option>
					<option value="best"<?=$sort=="best"?"selected":""?>>인기순</option>
					<option value="marks"<?=$sort=="marks"?"selected":""?>>상품평순</option>
					<option value="like"<?=$sort=="like"?"selected":""?>>좋아요순</option>
					<option value="price" <?=$sort=="price"?"selected":""?>>낮은가격순</option>
					<option value="price_desc" <?=$sort=="price_desc"?"selected":""?>>높은가격순</option>
				</select>

			</div>
			<div class="comp-select prodview">
				<select title="상품 갯수"  id="prodivew" onchange="ChangeProdView(this.value)">
					<?foreach ($prod_view_code as $key => $val){ ?>
					<option value="<?=$key ?>"<?=$listnum==$key?"selected":""?>><?=$val ?></option>
					<?} ?>
				</select>
			</div>
			<!--
				(D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
				좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
				페이지 변경할 때 페이지 리로드가 아닌 ajax로 연동하거나,
				더보기 등으로 리스트 하단에 상품이 추가될 경우,
				컬러 썸네일 스크립트 적용을 위해 내용 변경 후 color_slider_control() 함수를 호출해주세요.
			-->
			<ul class="comp-goods item-list">
                <?=$arrProd[0]?>
			</ul>
			<div class="list-paginate mt-20">
			<?php
				if( $paging->pagecount > 1 ){
					echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
				}
			?>
			</div>
		</section>
		<!-- 상품리스트 - 상품 -->
	</div>
</main>


<?php
}
?>

<script type="text/javascript">
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeProdView(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}


function ChangeList() {
    var soldout = "";
    if($("#chksoldout").prop('checked')) soldout = "1";
    else soldout = "0";
    document.form2.soldout.value = soldout;
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.submit();
}
</script>

<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=sort value="<?=$sort?>">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
    <input type=hidden name=bridx value="<?=$bridx?>">
    <input type=hidden name=vender value="<?=$venderIdx?>">
    <input type=hidden name=search_word value="<?=$search_word?>">
    <input type=hidden name=sel_cate_code value="<?=$sel_cate_code?>">
    <input type="hidden" name="color" id="color" value="<?=$color_name?>">
    <input type="hidden" name="size" id="size" value="<?=$size ?>">
    <input type="hidden" name="soldout" id="soldout" value="<?=$soldout?>">
</form>

<script>

$(document).ready(function(){
	var sizeCode = [];
	var colorCode = [];
	var strSize = $("#size").val().split(",");
	var strColor = $("#color").val().split(",");

	$.each( strSize, function( index, val ){
		  $("#size_"+val).attr("checked", true);
	});
	$.each( strColor, function( index, val ){
		  $("#smart_search_color_"+val).attr("checked", true);
			if(val == "white" || val == "transparent"){
				$("#smart_search_color_"+val).parent().parent().addClass("light")
			}
	});
	

	$(".btn-submit").click(function() {
		if(sizeCode != ""){
			//배열에 code가 있는 경우 삭제
			var codeSize = sizeCode.length;
			sizeCode.splice(0,codeSize);
		}

		if(typeof $("input[name=sizechk]:checked").val() == "undefined"){
			$("input[name=size]").val("");
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

		document.form2.block.value="";
		document.form2.gotopage.value="";
		document.form2.sort.value="";
		document.form2.submit();
	});

	//투명, 흰색 등 색이 밝아 체크 색상이 검은색인 것은 li에 class="light" 을 추가
	$("input[name=smart_search_color]").click(function() {
		var color_name = $(this).val();
		if(color_name == "white" || color_name == "transparent"){
			var idx = $(this).attr("idx");
			$("#color_"+idx).addClass("light");
		}
	});

});

//카테고리 관련 javascript end

</script>
