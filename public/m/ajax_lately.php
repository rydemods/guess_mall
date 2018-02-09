<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$offset = $_POST['offsetLine']*2;
$display = $_POST['display']*2;
//exdebug($offset);
//exdebug($display);
//echo $dispaly;

?>


<ul>
<?php
	####### 최근 본 상품 리스트 #######
	//exdebug($_COOKIE);
	$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로
	$prdt_list=explode(",",$_prdt_list);
	$prdt_no=count($prdt_list);
	if(ord($prdt_no)==0||!$_prdt_list) {
		$prdt_no=0;
	}
	//debug($prdt_no);

	$tmp_product="";
	for($i=0;/*$i<$prdt_no;*/$i<6;$i++){ //pc에서 최근본 상품5개 보여줌. 모바일도 5개만 보이게 수정
		$tmp_product.="'{$prdt_list[$i]}',";
	}
	//exdebug($tmp_product);
	$productall = array();
	$tmp_product=rtrim($tmp_product,',');
	$sql = "SELECT productcode,productname,maximage,tinyimage,quantity,consumerprice,sellprice,pridx FROM tblproduct ";
	$sql.= "WHERE productcode IN ({$tmp_product}) ";
	$sql.= "ORDER BY FIELD(productcode,{$tmp_product})";
	$sql.= "LIMIT ".$display." OFFSET ".$offset;
	$result=pmysql_query($sql,get_db_conn());
	if($prdt_no>0) :
		while($row=pmysql_fetch_object($result)) :
		//exdebug($row);
		//$row->quantity;
			##### 쿠폰에 의한 가격 할인
			$cou_data = couponDisPrice($row->productcode);
			if($cou_data['coumoney']){
				$nomalprice=$row->sellprice;
				$row->sellprice = $row->sellprice-$cou_data['coumoney'];
			}
			##### 오늘의 특가, 타임세일에 의한 가격
			$spesell = getSpeDcPrice($row->productcode);
			if($spesell){
				$nomalprice=$row->sellprice;
				$row->sellprice = $spesell;
			}
			##### //오늘의 특가, 타임세일에 의한 가격

?>
						<li>

							<div class="goods_wrap">
								<a href="productdetail.php?pridx=<?=$row->pridx?>">
									<img src="../data/shopimages/product/<?=$row->maximage;?>" onerror="this.src='<?=$Dir?>images/acimage.gif'" style="width:100%;height:100%;"/>
								<div class="infobox">
									<span class="name"><?=$row->productname?></span>
									<div class="pricebox">
										<strong>
											<del><?=number_format($row->consumerprice)?></del>
											<?=number_format($row->sellprice)?>
										</strong>
									</div>
								</div>
								</a>
							</div>
						</li>


<?php
		endwhile;

?>


<?php
	endif;
?>
</ul>


