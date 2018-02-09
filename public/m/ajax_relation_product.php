<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$productimgpath = $Dir.DataDir."shopimages/product/";

$code = $_POST["code"];
$arrCode = explode(",",$code);
$type = $_POST["type"];
$relationHtml = "";

$prod_sql = "SELECT productcode, productname, brand, sellprice, consumerprice, minimage, li.section,  
					COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt 
					FROM tblproduct a ";
$prod_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '" . $_ShopInfo->getMemid () . "' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";
$prod_sql .= "WHERE display = 'Y' "; 
foreach($arrCode as $i => $v){
	if($i == 0){
		$prod_sql.= " AND (a.productcode = '".$v."'";
	}else{
		$prod_sql.= " OR a.productcode = '".$v."'";
	}
}
$prod_sql .= ")";
// exdebug($prod_sql);
$prod_result = pmysql_query( $prod_sql, get_db_conn() );
while ( $row = pmysql_fetch_array($prod_result) ) {
	$arrRelation[] = $row;
}

if($type == "mobile"){
	$pr_link = $Dir.'m/productdetail.php?productcode=';
	foreach ($arrRelation as $key => $val){
		$relationHtml .= '<li class="grid-item">   
									<a href="'.$pr_link.$val['productcode'].'">
										<figure>
											<img src="'.$productimgpath.$val['minimage'].'" alt="">
											<figcaption>
												<p class="title"><strong class="brand">['.brand_name($val['brand']).']</strong>
												<span class="name">'.$val['productname'].'</span></p>	';
												if($val['consumerprice'] != $val['sellprice']){
													$relationHtml .= '<span class="price"><del>'.$val['consumerprice'].'</del>  <strong>'.$val['sellprice'].'</strong></span>';
												}else{
													$relationHtml .= '<span class="price"> <strong>'.$val['consumerprice'].'</strong></span>';
												}
		$relationHtml .= '			</figcaption>
										</figure>					
									</a>';
									if($val['section']){
										$relationHtml .='<button class="comp-like btn-like like_p'.$val['productcode'].' on" onclick="detailSaveLike(\''.$val['productcode'].'\',\'on\',\'product\',\''.$_ShopInfo->getMemid().'\',\''.$val['brand'].'\' )" id="like_'.$val['productcode'].'" title="선택됨"><span  class="like_pcount_'.$val['productcode'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
									}else{
										$relationHtml .='<button class="comp-like btn-like like_p'.$val['productcode'].'" onclick="detailSaveLike(\''.$val['productcode'].'\',\'off\',\'product\',\''.$_ShopInfo->getMemid().'\',\''.$val['brand'].'\' )" id="like_'.$val['productcode'].'" title="선택 안됨"><span  class="like_pcount_'.$val['productcode'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
									}
		$relationHtml .= '		</li>	';
		
	}
	
}

echo $relationHtml;


?>