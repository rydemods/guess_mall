<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//exdebug($_POST);
//exdebug($_GET);

$imagepath=$Dir.DataDir."shopimages/product/";
$pr_link = $Dir.'m/productdetail.php?productcode=';
$code=$_POST["code"];
$color_name = $_POST['color'];
$sort=$_POST["sort"]?$_POST["sort"]:"recent";
$brand = $_POST['brand'];
$size = $_POST['size'];
$search         = $_POST['sm_search'] ?: $_POST['search'];
$search         = trim($search);    // 앞뒤 빈공간 제거
$search         = str_replace("'", "''", $search);  // for query
$listnum = $_POST['listnum'] ?: "10";
$soldout = $_POST['soldout'];

list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');

$code=$code_a.$code_b.$code_c.$code_d;

$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;
$thisCate = getDecoCodeLoc( $code );

//1차 카테고리 조회
$thisCateName = '';
$thisCateCnt = count( $thisCate );
if( $thisCateCnt == 1 ){ // 1차 카테고리
	$thisCateName = $thisCate[0]->code_name;
}else if($thisCateCnt == 2){
	$thisCateName = $thisCate[1]->code_name;
}else if( $thisCateCnt == 3 ){
	$thisCateName = $thisCate[2]->code_name;
}

//조건
$qry = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N'  ";

if($search){
	$searchWord = strtolower($search);
	$searchWord = str_replace("'", "''", $searchWord);
	
	// 브랜드 검색
	$subsql = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$searchWord}%' OR lower(brandname2) like '%{$searchWord}%' OR lower(brandtag) like '%{$searchWord}%' ";
	$subresult = pmysql_query($subsql);
	
	$arrSearchBrand = array();
	while ( $subrow = pmysql_fetch_object($subresult) ) {
		if ( $subrow->bridx ) {
			array_push($arrSearchBrand, $subrow->bridx);
		}
	}
	pmysql_free_result($subresult);
	
	$sword_search = "(
	lower(a.productname) LIKE '%{$searchWord}%'
	OR lower(a.keyword) LIKE '%{$searchWord}%'
	OR lower(a.mdcomment) LIKE '%{$searchWord}%'
	OR lower(a.sizecd) LIKE '%{$searchWord}%'
	OR lower(a.productcode) LIKE '{$searchWord}%'";
	
	if ( count($arrSearchBrand) > 0 ) {
		$sword_search .= " OR a.brand in ( " . implode(",", $arrSearchBrand) . " ) ";
	}
	
	$sword_search .= ")";
	
	//$sword_search = "(UPPER(a.productname) LIKE '%{$search}%')";
	$addQuery[] = $sword_search;
	//조건
	$strAddQuery = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N' ";
	
	$strAddQuery .= "AND ".implode(" AND ", $addQuery);
	
	//색상별 검색
	if(!empty($color_name)){
		
		foreach($color_name as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.color_code = '".$v."'";
			}else{
				$strAddQuery.= " OR a.color_code = '".$v."'";
			}
		}
		$strAddQuery.=")";
	}
	
	//브랜드별 검색
	// exdebug($brand);
	// $arrBrand = explode(",", $brand);
	if(!empty($brand)){
		foreach($brand as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.brand = '".$v."'";
			}else{
				$strAddQuery.= " OR a.brand = '".$v."'";
			}
		}
		$strAddQuery.=")";
	}
	//사이즈 검색
	if(!empty($size)){
		foreach($size as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.sizecd LIKE '%".$v."%'";
			}else{
				$strAddQuery.= " OR a.sizecd LIKE '%".$v."%'";
			}
		}
		$strAddQuery.=")";
	}
}else {
	$strAddQuery = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N' ";
	
	//색상별 검색
	if(!empty($color_name)){
		foreach($color_name as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.color_code = '".$v."'";
			}else{
				$strAddQuery.= " OR a.color_code = '".$v."'";
			}
		}
		$strAddQuery.=")";
	}
	
	//브랜드별 검색
	// exdebug($brand);
	// $arrBrand = explode(",", $brand);
	if(!empty($brand)){
		foreach($brand as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.brand = '".$v."'";
			}else{
				$strAddQuery.= " OR a.brand = '".$v."'";
			}
		}
		$strAddQuery.=")";
	}
	//사이즈 검색
	if($size[0] != ""){
		foreach($size as $i => $v){
			if($i == 0){
				$strAddQuery.= " AND (a.sizecd LIKE '%".$v."%'";
			}else{
				$strAddQuery.= " OR a.sizecd LIKE '%".$v."%'";
			}
		}
		$strAddQuery.=")";
	}
}
// 품절상품제외 2016-10-10
if($soldout == "1") {
    $strAddQuery.= " AND a.quantity > 0 ";
}

//상품리스트
$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.color_code, ";
$sql.= "a.maximage, a.minimage,a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, a.brand, a.icon, a.soldout, a.prodcode, a.colorcode, a.sizecd, COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$sql.= "TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt ";
$sql.= "FROM (select *, case when (consumerprice - sellprice) <= 0 then 0 else (consumerprice - sellprice) end as saleprice from tblproduct) AS a  ";
$sql.= "JOIN ( SELECT c_productcode FROM tblproductlink WHERE c_category LIKE '".$likecode."%' GROUP BY c_productcode ) AS link ";
$sql.= "ON( a.productcode=link.c_productcode ) ";
$sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
								count(productcode) as marks_total_cnt
					FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$sql.= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";
if($sort=="best"){
	$sql.= "LEFT JOIN
                    (
                        select op.productcode, sum(op.option_quantity) as qty
                        from tblorderproduct op
                        join	tblproductlink pl on op.productcode = pl.c_productcode and pl.c_category LIKE '".$likecode."%'
                        where op.ordercode >= '".date("Ymd",strtotime('-1 month'))."000000' and op.ordercode <= '".date("Ymd")."235959'
                        group by op.productcode
                        order by op.productcode
                    ) bt on a.productcode = bt.productcode
                ";
}


$sql.= $strAddQuery." ";
if($sort=="recent"){
	$sql.= " ORDER BY a.start_no desc, a.pridx desc ";
}else if($sort=="best"){
	$sql.= " ORDER BY COALESCE(bt.qty, 0) desc, a.pridx desc ";
}else if($sort=="marks"){
	$sql.= " ORDER BY COALESCE(re.marks, 0) desc, a.pridx desc  ";
}else if($sort=="like"){
	$sql.= " ORDER BY hott_cnt desc, a.pridx desc  ";
}else{
	if($sort=="price"){
		$sql .= " ORDER BY a.start_no desc,a.sellprice ";
	}else if($sort=="price_desc"){
		$sql .= " ORDER BY a.start_no desc,a.sellprice desc ";
	}
}
$paging = new New_Templet_mobile_paging($sql,5,$listnum,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
// exdebug($sql);
//exdebug($t_count);
$result = pmysql_query($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$arrProductList[] = $row;
}

?>

	<div class="goods-list">
		<div class="goods-list-item product-list">
			<!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
			<ul>
				<li class="grid-sizer"></li>
`				<?
				foreach( $arrProductList as $key=>$val ){
					$marks = substr($val['marks_ever_cnt'],0,1)  * 20;
				?>
				<li class="grid-item">
                	<a href="<?=$pr_link.$val['productcode'] ?>">
                    <figure>
                        <img src="<?=$imagepath.$val['minimage']?>" alt="">
                        <figcaption>
                            <span class="brand"><?=brand_name($val['brand']) ?></span>
                            <p class="title"><?=$val['productname'] ?></p>
								 <?if($val['consumerprice'] != $val['sellprice']){ ?>
	                             <span class="price"><del><?=number_format($val['consumerprice']) ?></del><strong><?=number_format($val['sellprice']) ?></strong></span>
	                             <?}else{ ?>
	                             <span class="price"><strong><?=number_format($val['consumerprice']) ?></strong></span>
	                             <?} ?>
							<div class="star"><span class="comp-star star-score"><strong style="width:<?=$marks ?>%;"></strong></span>([REVIEW_CNT])</div>
                        	<?if($val['section']){ ?>
							<button class="comp-like btn-like on" onclick="detailSaveLike('<?=$val['productcode']?>','on','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택됨"><span  id="likehott_count_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?}else{ ?>
							<button class="comp-like btn-like" onclick="detailSaveLike('<?=$val['productcode']?>','off','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택 안됨"><span id="likehott_count_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?} ?>
						</figcaption>
                    </figure>
                	</a>
            	</li>
				<?} ?>
			</ul>
		</div>
	</div>
	<input type="hidden" id="t_count" value="<?=$t_count ?>" />
			
	<!-- 페이징 -->
	<div class="list-paginate mt-10 mb-30">
		<?echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;?>
	</div>
	<!-- // 페이징 -->