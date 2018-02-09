<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath=$Dir.DataDir."shopimages/product/";
$pr_link = $Dir.'m/productdetail.php?productcode=';
$bridx=$_POST["bridx"];
$code=$_POST["code"];
$color_name = $_POST['color'];
$sort=$_POST["sort"]?$_POST["sort"]:"new";
$size = $_POST['size'];
$search_word    = $_REQUEST['search_word']?:"";
$listnum = $_POST['listnum'] ?: "5";

list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');

$code=$code_a.$code_b.$code_c.$code_d;


// ======================================================================================
// 브랜드 정보 조회
// ======================================================================================

$sql  = "SELECT * FROM tblproductbrand WHERE bridx = {$bridx} ";
$row  = pmysql_fetch_object(pmysql_query($sql));

$brand_name = $row->brandname;
$brand_cate = $row->productcode_a;
$venderIdx  = $row->vender;


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

if ( !empty($code) ) {
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

if ( !empty($code) ) {
	// 뒤에 '0'을 모두 제거
	$prod_sql .= "AND ( c.c_maincate = 1 AND c.c_category like '" . rtrim($code, "0") . "%' ) ";
}

//색상별 검색
if(!empty($color_name)){
	foreach($color_name as $i => $v){
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
if(!empty($size)){
	foreach($size as $i => $v){
		if($i == 0){
			$prod_sql.= " AND (a.sizecd LIKE '%".$v."%'";
		}else{
			$prod_sql.= " OR a.sizecd LIKE '%".$v."%'";
		}
	}
	$prod_sql.=")";
}
if ( $tmp_sort[0]=="new" ) {
	// NEW
	$prod_sql .= " ORDER BY a.modifydate desc, a.date desc, a.pridx desc ";
}else if ( $tmp_sort[0]=="best" ) {
	// BEST
	$prod_sql .= " ORDER BY a.vcnt desc, a.pridx desc ";
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
// exdebug($prod_sql);

$paging = new New_Templet_mobile_paging($prod_sql, 5, $listnum, 'GoPage', true);

$t_count    = $paging->t_count;
$gotopage   = $paging->gotopage;
$prod_sql   = $paging->getSql($prod_sql);

$result = pmysql_query($prod_sql);
while ( $row = pmysql_fetch_array($result) ) {
	$arrBrandList[] = $row;
}

?>

	<div class="goods-list">
		<div class="goods-list-item">
			<!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
			<ul>
				<li class="grid-sizer"></li>
`				<?
				foreach( $arrBrandList as $key=>$val ){
					$marks = substr($val['marks_ever_cnt'],0,1)  * 20;
				?>
				<li class="grid-item">
                	<a href="<?=$pr_link.$val['productcode'] ?>">
                    <figure>
                        <img src="<?=$imagepath.$val['minimage']?>" alt="">
                        <figcaption>
                            <span class="brand"><?=$brand_name?></span>
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