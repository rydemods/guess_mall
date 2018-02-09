<?php
##### 추천관련상품

#### 대표 카테고리
$sql_catelink = "SELECT c_category FROM tblproductlink ";
$sql_catelink.= "WHERE c_maincate=1 AND c_productcode='{$productcode}' ";
$sql_catelink.= "LIMIT 1 ";
list($prdmaincate) = pmysql_fetch(pmysql_query($sql,get_db_conn()));
#### // 대표 카테고리

$sql = "SELECT collection_list FROM tblcollection ";
$sql.= "WHERE (productcode='".substr($prdmaincate,0,3)."000000000' ";
$sql.= "OR productcode='".substr($prdmaincate,0,6)."000000' OR productcode='".substr($prdmaincate,0,9)."000' ";
$sql.= "OR productcode='".substr($prdmaincate,0,12)."' OR productcode='{$productcode}') ";
$sql.= "ORDER BY productcode DESC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)){
	$coll_arr[] = $row->collection_list;
}
pmysql_free_result($result);

### tblcollection에서 구한 상품코드를 연결함
if($coll_arr){
	
	$coll_str = implode(",",$coll_arr);
	$coll_arr = explode(",",$coll_str);
	foreach($coll_arr as $v){
		$coll_v[$v] = $v;
	}
	foreach($coll_v as $v){
		$coll_res[] = $v;
	}
	$collection_list=implode(",",$coll_res);
	$collection=str_replace(",","','",$collection_list);
	$sql_coll = "SELECT a.productcode,a.productname,a.sellprice,a.maximage,a.tinyimage,a.etctype,a.reserve,a.reservetype,a.consumerprice,a.option_price,a.tag,a.quantity,a.selfcode 
	FROM tblproduct AS a 
	LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode 
	WHERE a.productcode IN ('{$collection}') 
	AND a.display='Y' AND a.productcode!='{$productcode}'
	AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') 
	ORDER BY FIELD(a.productcode,'{$collection}') LIMIT ".$_data->coll_num;
	$res_coll = pmysql_query($sql_coll);
	while($row_coll = pmysql_fetch_array($res_coll)){
		$coll_prd[] = $row_coll;
	}
	
}else{
	$coll_prd = "";
}
	

?>
			<?php if($coll_prd){ ?>
				<div class="goods_right_best_view">
					<h4>Related Item</h4>
					<div class="view_list_wrap">
					<?php 
						foreach($coll_prd as $k=>$v){
							if($k<5){
					?>
						<ul>
							<li><a href="#"><img src="../img/test/test_img120.jpg" alt="" /></a></li>
							<li class="name"><?=$v['productname']?></li>
							<li class="price">135,200원</li>
						</ul>
					<?php
							}
						}
					?>
					</div>
				</div>
			<?php }else{ ?>
				<div class="goods_right_best_view">
					<h4>Related Item</h4>
					<div class="view_list_wrap">
						<ul>
							<li><a href="#">관련 상품이 없습니다.</a></li>
							<li class="name"></li>
							<li class="price"></li>
						</ul>
					</div>
				</div>
			
			<?php } ?>