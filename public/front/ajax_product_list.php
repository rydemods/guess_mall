<?php
/*********************************************************************
// 파 일 명		: ajax_product_list.php
// 설     명		: ajax 카테고리 상품 리스트
// 상세설명	: 1,2,3차 카테고리 상품을 ajax 리스트로 출력
// 작 성 자		: 2016-08-08 - daeyeob(김대엽)
//
*********************************************************************/
?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$cate_code = $_POST["code"];
$brand_code = $_POST["brandcode"];
$cate_type = $_POST["catetype"];
$sort = $_POST["sort"];
$strBrandCode = explode(",", $brand_code);
$imagepath=$Dir.DataDir."shopimages/product/";

// ===================================================================
// catetype - 1차 카테고리 : main, 2,3차 카테고리 : list
// ===================================================================
if($cate_type == "main"){
	$cateNameSql= "select * from tblproductcode where code_a = '{$cate_code}' and code_b = '000' limit 1";
	$result = pmysql_query($cateNameSql);
	$row = pmysql_fetch_object($result);
	$cate_name = $row->code_name;

	$orderby	= "";

	if($sort == "marks"){
		#상품평
		$orderby.= " ORDER BY re.marks ASC";
	}elseif($sort == "like"){
		#좋아요
		$orderby.= " ORDER BY hott_cnt DESC";
	}elseif($sort == "best"){
		#인기
		$orderby .= " ORDER BY qty DESC";
	}

	$sql = "SELECT p.pridx, p.productcode, p.productname,p.consumerprice ,p.sellprice, p.assembleuse, p.consumerprice, p.reserve, p.reservetype, p.production,
	p.madein, p.model, p.brand, p.selfcode, p.quantity, p.option1, p.keyword, p.maximage, p.minimage, p.tinyimage, p.date, p.vender,
	p.content, p.over_minimage, p.rate, p.prodcode, p.colorcode, re.marks_total_cnt,
	TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt, li.section, COALESCE( li.hott_cnt, 0 ) AS hott_cnt,
	COALESCE( (select sum(op.option_quantity) as qty from tblorderproduct op join tblproductlink pl on op.productcode = pl.c_productcode
		and pl.c_category LIKE '%' where op.ordercode >= '20160710000000' and op.ordercode <= '20160810235959' and op.productcode = p.productcode group by op.productcode
		order by op.productcode), 0) qty
	FROM tblproduct p
	LEFT JOIN (SELECT productcode,
			sum(marks) as marks,
			count(productcode) as marks_total_cnt
			FROM tblproductreview group by productcode) re on p.productcode = re.productcode
	LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code";

	$sql .= " WHERE p.productcode LIKE '".$cate_code."%' AND p.display = 'Y'";

	if($brand_code){
		foreach($strBrandCode as $i => $v){
			if($i == 0){
				$sql.= " AND (p.brand = '".$v."'";
			}else{
				$sql.= " OR p.brand = '".$v."'";
			}
		}
		$sql.=")";
	}

	$sql.= $orderby;

}elseif ($cate_type == "list"){
	$category = $_POST['category'];
	$firstCateCode = substr($cate_code,0,3);
	$secondCateCode = substr($cate_code,0,-6);
	$thirdCateCode = substr($cate_code,0, -3);

	$selectCateSql  = "select * from tblproductcode";
	if($category == "second"){
		$strSecondCateCode = substr($secondCateCode,3);
		$selectCateSql .= " where code_b = '{$strSecondCateCode}'  limit 1";
	}else if($category == "third"){
		$strSecondCateCode = substr($secondCateCode,3);
		$strthirdCateCode = substr($thirdCateCode,6);
		$selectCateSql .= " where code_b = '{$strSecondCateCode}' and code_c = '{$strthirdCateCode}' and code_d = '000' limit 1";
	}
	$result = pmysql_query($selectCateSql);
	$row = pmysql_fetch_object($result);
	$cate_name = $row->code_name;

	$orderby	= "";

	if($sort == "marks"){
		#상품평
		$orderby= " ORDER BY re.marks ASC";
	}elseif($sort == "like"){
		#좋아요
		$orderby= " ORDER BY hott_cnt DESC";
	}elseif($sort == "best"){
		#인기
		$orderby	= " ORDER BY qty DESC";
	}

	$sql = "SELECT p.pridx, p.productcode, p.productname, p.consumerprice, p.sellprice, p.assembleuse, p.consumerprice, p.reserve, p.reservetype, p.production,
	p.madein, p.model, p.brand, p.selfcode, p.quantity, p.option1, p.keyword, p.maximage, p.minimage, p.tinyimage, p.date, p.vender,
	p.content, p.over_minimage, p.rate, p.prodcode, p.colorcode, re.marks_total_cnt,
	TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt, li.section,
	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt,
	COALESCE( (select sum(op.option_quantity) as qty from tblorderproduct op join tblproductlink pl on op.productcode = pl.c_productcode
		and pl.c_category LIKE '%' where op.ordercode >= '20160710000000' and op.ordercode <= '20160810235959' and op.productcode = p.productcode group by op.productcode
		order by op.productcode), 0) qty
	FROM tblproduct p
	LEFT JOIN (SELECT productcode,
			sum(marks) as marks,
			count(productcode) as marks_total_cnt
			FROM tblproductreview group by productcode) re on p.productcode = re.productcode
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code {$add_join} ";
	if($category == "second"){
		$sql .= " WHERE p.productcode LIKE '".$secondCateCode."%' AND p.display = 'Y'";
	}else if($category == "third"){
		$sql .= " WHERE p.productcode LIKE '".$thirdCateCode."%' AND p.display = 'Y'";
	}
	if($brand_code){
		foreach($strBrandCode as $i => $v){
			if($i == 0){
				$sql.= " AND (p.brand = '".$v."'";
			}else{
				$sql.= " OR p.brand = '".$v."'";
			}
		}
		$sql.=")";
	}

	$sql.= $orderby;

}

# 페이징
$paging = new New_Templet_paging($list_sql, 18,  18, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql);
$htmlResult = '';
while ( $row = pmysql_fetch_array($result) ) {
	$arrFirstProduct[] = $row;
}
?>

					<h3><?=$cate_name?><span class="num">(<?=count($arrFirstProduct) ?>)</span></h3>
					<div class="comp-select sorting">
						<select title="상품정렬순" id="sortlist" onchange="sortList(this.value)">
							<option value="best" <?=$sort  == 'best' ? ' selected' : '';?>>인기순</option>
							<option value="marks" <?=$sort  == 'marks' ? ' selected' : '';?>>상품평순</option>
							<option value="like" <?=$sort  == 'like' ? ' selected' : '';?>>좋아요순</option>
						</select>
					</div>
                <!--
                    (D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
                    좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
                -->
                <ul class="comp-goods item-list">
                	<?
                	if(count($arrFirstProduct) > 0){
						foreach( $arrFirstProduct as $key=>$val ){
                			$marks = substr($val['marks_ever_cnt'],0,1)  * 20;
                			#같은 상품 색상 옵션
                			$prod_sql = "SELECT pridx, productcode, prodcode, colorcode, minimage FROM tblproduct WHERE prodcode = '".$val['prodcode']."' AND display = 'Y' AND productcode <> '".$val['productcode']."' limit 3";
                			$result = pmysql_query($prod_sql);
                			$arrColorProd = "";
                			while ( $row = pmysql_fetch_array($result) ) {
                				$arrColorProd[] = $row;
                			}
                		?>
             		<li>
                        <figure>
                            <a href="javascript:prod_detail('<?=$val['productcode'] ?>');" id="prod_detail"><img src="<?=$imagepath.$val['maximage']."?v".date("His")?>" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                	<?foreach( $arrColorProd as $v ){ ?>
                                    <li><a href="javascript:prod_detail('<?=$v['productcode'] ?>');"><img src="<?=$imagepath.$v['minimage']."?v".date("His") ?>" alt="white"></a></li>
                                	<?} ?>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand"><?=brand_name($val['brand']) ?></strong>
                                    <p class="title"><?=$val['productname'] ?></p>
                                    <?if($val['consumerprice'] != $val['sellprice']){ ?>
                                    <span class="price"><del><?=number_format($val['consumerprice']) ?></del><strong><?=number_format($val['sellprice']) ?></strong></span>
                                    <?}else{ ?>
                                    <span class="price"><strong><?=number_format($val['consumerprice']) ?></strong></span>
                                    <?} ?>
                                    <div class="star"><span class="comp-star star-score"><strong style="width:<?=$marks?>%;"></strong></span>(<?=$val['marks_total_cnt']?$val['marks_total_cnt']:'0'?>)</div>
                                </a>
								<?if($val['section']){ ?>
                                <button class="comp-like btn-like on" id="like_<?=$val['productcode'] ?>" type="on" ids="<?=$val['productcode'] ?>" title="선택됨"><span id="like_count_<?=$val['productcode'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
                            	<?}else{ ?>
                            	<button class="comp-like btn-like" id="like_<?=$val['productcode'] ?>" type="off" ids="<?=$val['productcode'] ?>" title="선택 안됨"><span id="like_count_<?=$val['productcode'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
                            	<?} ?>
                            </figcaption>
                        </figure>
                    </li>
                    <?} ?>
                <?}else{?>
                	<p style="text-align: center">해당 상품이 없습니다.</p>
                <?} ?>
                </ul>
                <div class="list-paginate mt-20">
					<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
				</div>

