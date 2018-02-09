<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
// 장바구니의 주문선택
$type = $_GET['type'];
if( is_null($type) ){
	$type = '0';
}
# 주문 상품 
# 2015 11 10 유동혁
function BasketData ( $tempkey ) {
	$bData = array();
	$pData = array();
	$vData = array();
	$BasketData = array();
	# 장바구니 상품정보 가져오기
	$bkSql = "WITH thisBasket AS ( ";
	$bkSql.= " SELECT productcode, tempkey FROM tblbasket WHERE tempkey = '".$tempkey."'  GROUP BY productcode, tempkey ";
	$bkSql.= ") ";
	$bkSql.= "SELECT pr.productcode, pr.productname, pr.sellprice, pr.consumerprice, ";
	$bkSql.= "pr.buyprice, pr.reserve, pr.reservetype, pr.tinyimage, pr.minimage, ";
	$bkSql.= "pr.quantity, pr.vender, pr.option1, pr.option2, ";
	$bkSql.= "pr.etctype, pr.deli_price, pr.deli, pr.selfcode, ";
	$bkSql.= "pr.vip_product, pr.membergrpdc, pr.bankonly, pr.addcode, ";
	$bkSql.= "pr.setquota, pr.overseas_type, pr.deli, pr.deli_price, pr.overseas_type ";
	$bkSql.= "FROM tblproduct pr ";
	$bkSql.= "JOIN thisBasket bk ON bk.productcode = pr.productcode ";
	$bkSql.= "ORDER BY vender ASC, productcode ASC ";
	$bkRes = pmysql_query( $bkSql, get_db_conn() );
	while( $bkRow = pmysql_fetch_object( $bkRes ) ){
		
		#장바구니 상품정보
		$bData[$bkRow->productcode] = $bkRow;
		
		#상품 벤더
		if( $bkRow->vender>0) { // 벤더 세팅
			$sql = "SELECT deli_price,deli_pricetype,deli_mini,deli_area,deli_limit,deli_area_limit FROM tblvenderinfo WHERE vender='".$bkRow->vender."' ";
			$res2=pmysql_query($sql,get_db_conn());
			if($_vender=pmysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;

			}
			$vData[$bkRow->productcode] = $_vender;
			pmysql_free_result($res2);
		}

		# 장바구니 옵션 정보 
		$sql = "SELECT bk.basketidx, bk.productcode, bk.opt1_idx, bk.opt2_idx, bk.optidxs, ";
		$sql.= "bk.quantity, bk.optionarr, bk.quantityarr, bk.pricearr, bk.op_type, ";
		$sql.= "bk.assemble_list, bk.assemble_idx, bk.package_idx, ";
		$sql.= "op.option_num, op.option_code, op.option_price, op.option_quantity ";
		$sql.= "FROM tblbasket bk ";
		$sql.= "LEFT JOIN tblproduct_option op ON ( bk.optionarr = op.option_code AND bk.productcode = op.productcode ) ";
		$sql.= "WHERE bk.tempkey='".$tempkey."' ";
		$sql.= "AND bk.productcode = '".$bkRow->productcode."' ";
		$sql.= "ORDER BY bk.tempkey ASC, bk.productcode ASC, bk.basketidx ASC ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ) {
			$pData[$bkRow->productcode][] = $row;
		}
		pmysql_free_result( $result );
	}
	pmysql_free_result( $bkRes );

	$BasketData['basket'] = $bData;
	$BasketData['vender'] = $vData;
	$BasketData['product'] = $pData;

	return $BasketData;
}

if( $_REQUEST[selectItem] ){
	$tempkey = $_ShopInfo->getTempkeySelectItem();
	$basket = BasketData( $tempkey );
} else if ( $_REQUEST[allcheck] ) {
	$tempkey = $_ShopInfo->getTempkey();
	$basket = BasketData( $tempkey );
} else {
	$basket = '';
}

?>
<?include ($Dir.MainDir.$_data->menu_type.".php");?>
<!-- 메인 컨텐츠 -->
<div class="main_wrap">
		<div class="cart_wrap">
		<div class="cart_complete_wrap">
			<h3 class="title mt_20">
				<p class="line_map"><a>홈</a> &gt; <a>주문/결제</a> &gt; <a class="on">주문실패</a></p>
			</h3>
<?php
	if (strstr("B", $_ord->paymethod[0]) || (strstr("VOQCPM", $_ord->paymethod[0]) && strcmp($_ord->pay_flag,"0000")==0)){
?>
			<div class="message_wrap m0">
				<h5><strong>주문</strong>이 정상적으로 <strong>완료</strong>되었습니다.</h5>
				<div class="ng_14 ta_c mt_10"><?=$_data->shopname?>를 이용해 주셔서 감사합니다.</div>
				<div class="mt_30 ta_c">주문번호 : <?=$ordercode?></div>
			</div>
<?php
	}else{
?>
			<div class="message_wrap m0">
				<h5><strong>결제</strong>가 <strong>실패</strong>되었습니다.</h5>
				<!--<div class="ng_14 ta_c mt_10">디지아톰을 이용해 주셔서 감사합니다.</div>-->
			</div>
<?	} ?>
			<!-- 주문 상품 -->
			<table class="list_table" summary="담은 상품의 정보, 판매가, 수량, 할인금액, 결제 예정가, 적립금을 확인할 수 있습니다.">
				<caption>주문 상품</caption>
				<colgroup>
					<col style="width:auto" />
					<col style="width:95px" />
					<col style="width:85px" />
					<col style="width:85px" />
					<!--<col style="width:95px" />-->
				</colgroup>
				<thead>
					<tr>
						<th scope="col">상품정보</th>
						<th scope="col">결제가</th>
						<th scope="col">수량</th>
						<th scope="col">총 결제가</th>
						<!--<th scope="col">적립금</th>-->
					</tr>
				</thead>
				<tbody>
<?php
	foreach( $basket['basket'] as $bKey=>$bVal ){ // 상품
		$tmpQuantity = 0;
		$product_price = $bVal->sellprice;
		$tmpPrice = 0;
?>
					<tr>
						<td class="info">
							<a href="productdetail.php?productcode=<?=$bKey?>" target="_self">
<?php
		if(strlen($bVal->minimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$bVal->minimage)){
			$file_size=getImageSize($Dir.DataDir."shopimages/product/".$bVal->minimage);
?>
									<img src="<?=$Dir.DataDir?>shopimages/product/<?=$bVal->minimage?>" <?if($file_size[0]>=$file_size[1]){ echo " width='126'"; }else{ echo "height='126'"; }?>>
<?php
		} else {
?>
									<img src="<?=$Dir?>images/no_img.gif" width="126">
<?php
		} //viewselfcode($prVal->productname,$prVal->selfcode)
?>
								<span class="name"><?=$bVal->productname?><br />
									<span class="option">
<?php
		foreach( $basket['product'][$bKey] as $optVal ){ //옵션
			// 상품 + 필수옵션
			if( $optVal->op_type == '0' && strlen( $optVal->opt1_idx ) > 0 ){
				$tempOpt1Arr = explode( ',', $bVal->option1);
				$tempOpt2Arr = explode( ',', $bVal->option2);
				echo '옵션 : '.$tempOpt1Arr[0].' '.$optVal->opt1_idx;
				if(strlen( $optVal->opt2_idx ) > 0 ){
					echo ' / '.$tempOpt2Arr[0].' '.$optVal->opt2_idx;
				}
				echo "&nbsp;( ".$optVal->quantityarr." 개 ".number_format( $optVal->option_price * $optVal->quantityarr )." 원 )";
				echo "<br>";
				$tmpQuantity += $optVal->quantityarr;
			// 추가옵션
			} else if( $optVal->op_type == '1' && strlen( $optVal->opt1_idx ) > 0 ){
				echo '옵션 : '.$optVal->opt1_idx;
				echo "&nbsp;( ".$optVal->quantityarr." 개 ".number_format( $optVal->option_price * $optVal->quantityarr )." 원 )";
				echo "<br>";
			// 상품
			} else {
				$tmpQuantity += $optVal->quantityarr;
			}	
			$tmpPrice += ( ( $bVal->sellprice + $optVal->option_price ) * $optVal->quantityarr );
		}
?>
									</span>
								</span>
							</a>
						</td>
						<td><strong><?=number_format( $product_price )?></strong></td>
						<td><?=$tmpQuantity?>개</td>
						<td><strong><?=number_format( $tmpPrice )?></strong></td>
					<!--	<td class="point"><img src="../img/icon/cart_point_icon.gif" alt="적립금" /><?=number_format($row->reserve*$row->quantity)?></td> -->
					</tr>
<?php
	}				
?>
				</tbody>
			</table>
			<!-- // 주문 상품 -->

			<div class="ta_c mt_50 pb_50">
				<a href="javascript:;" class = 'CLS_GoToMain btn_D on'>메인으로 이동</a>
			</div>


		</div>

</div><!-- //메인 컨텐츠 -->

<?php  include ($Dir."lib/bottom.php") ?>