<?
//$Dir="../";
include_once('outline/header_m.php');
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");
//$basketidxs = $_POST['basketidxs'];

// 장바구니에 들어가있는 힛딜 상품들을 삭제한다.
if ( strlen($_ShopInfo->getMemid()) > 0 ) {
	// 로그인
	$directQuery = "a.id = '" . $_ShopInfo->getMemid() . "' ";
} else {
	// 비로그인
	$directQuery = "a.tempkey='".$_ShopInfo->getTempkey()."' AND a.id = '' ";
}
$query="delete from tblbasket where basketidx in (select max(a.basketidx) from tblbasket a 
		left join tblproduct b on(a.productcode=b.productcode) 
		where b.hotdealyn='Y' and ".$directQuery." group by a.basketidx)";
pmysql_query($query);
////////////////////////////////////////////////////////////////

# 매장 픽업 / 당일 수령 장바구니 상품 삭제
delDeliveryTypeData();





basket_restore(); // lib에 지정해줘야 함
$Basket = new Basket(); //장바구니 초기화를 위해 불러온다
$Basket->revert_item(); // 주문실패한 상품을 되돌린다.
$Delivery = new Delivery();

//상품 이미지 경로
function obejct_setting( $basket )
{
	$basket_object = '';
	$opt1 = '';
	$opt2 = '';
	$reserve = 0;
	$option_code = '';
	$option_price = 0;
	$option_quantity = 0;
	$option_type = 0;

	//ERP 상품을 쇼핑몰에 업데이트한다.
	if ($basket->opt1_idx == 'SIZE') {
		getUpErpProductUpdate($basket->productcode, $basket->opt2_idx);
	}

	$pr_sql = "SELECT pridx, productcode, productname, sellprice, consumerprice, ";
	$pr_sql.= "buyprice, reserve, reservetype, quantity, option1, option2, addcode, ";
	$pr_sql.= "maximage, minimage, tinyimage, deli, deli_price, display, selfcode, ";
	$pr_sql.= "vender, brand, min_quantity, max_quantity, setquota, supply_subject, deli_qty, ";
	$pr_sql.= "quantity, option2_tf, option1_tf, option2_maxlen ";
	//$sql.= "detail_deli, deli_min_price, deli_package ";
	$pr_sql.= "FROM tblproduct WHERE productcode = '".$basket->productcode."' ";
	$pr_result = pmysql_query( $pr_sql, get_db_conn() );
	$pr_row = pmysql_fetch_object( $pr_result );
	$select_product = $pr_row;
	pmysql_free_result( $pr_result );

	#상품별 적립금 세팅 '$reserveshow' 위치확인 필요
	$reserve = getReserveConversion( $select_product->reserve, $select_product->reservetype, ( $select_product->sellprice + $basket->pricearr ) * $basket->quantity , "N" );

	$opt1 = $basket->opt1_idx;
	$opt2 = $basket->opt2_idx;
	$option_price = $basket->pricearr;
	$option_type = $basket->op_type;
	$option_quantity = $basket->quantity;
	$text_opt_subject = $basket->text_opt_subject;
	$text_opt_content = $basket->text_opt_content;

	#기초정보 세팅
	$basket_object = array(
		'basketidx'=>$basket->basketidx,
		'vender'=>$select_product->vender,
		'brand'=>$select_product->brand,
		'productcode'=>$select_product->productcode,
		'productname'=>$select_product->productname,
		'pr_quantity'=>$select_product->quantity,
		'price'=>$select_product->sellprice,
        'consumerprice'=>$select_product->consumerprice,
		'quantity'=>$basket->quantity,
		'reserve'=>$reserve,
		'selfcode'=>$select_product->selfcode,
		'addcode'=>$select_product->addcode,
		'tinyimage'=>$select_product->tinyimage,
		'opt1_name'=>$opt1,
		'opt2_name'=>$opt2,
        'opt_tf'=>$select_product->option1_tf,
		'text_opt_subject'=>$text_opt_subject,
		'text_opt_content'=>$text_opt_content,
		'text_opt_tf'=>$select_product->option2_tf,
        'text_opt_maxlen'=>$select_product->option2_maxlen,
		'option_price'=>$option_price,
		'option_quantity'=>$option_quantity,
		'option_type'=>$option_type,
		'deli'=>$select_product->deli,
		'deli_price'=>$select_product->deli_price,
		'deli_qty'=>$select_product->deli_qty, 
		'delivery_type'=>$basket->delivery_type,
		'reservation_date'=>$basket->reservation_date,
		'store_code'=>$basket->store_code,
		'post_code'=>$basket->post_code,
		'store_code'=>$basket->store_code,
		'address1'=>$basket->address1,
		'address2'=>$basket->address2,
		'prodcode'=>$basket->prodcode,
		'colorcode'=>$basket->colorcode
		//'detail_deli'=>$select_product->detail_deli,
		//'deli_min_price'=>$select_product->deli_min_price,
		//'deli_package'=>$select_product->deli_package
	);

	return $basket_object;
}

function basket_option( $productcode , $option_code = '', $option_type = 0 ){

	$option_sql = "SELECT option_num, option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use  ";
	$option_sql.= "FROM tblproduct_option WHERE productcode = '".$productcode."' AND option_type = '".$option_type."' AND option_use = 1 ";
	if( strlen( $option_code ) > 0 ) $option_sql.= "AND option_code = '".$option_code."' ";
	$option_sql.= "ORDER BY option_num ASC ";
	$option_result = pmysql_query( $option_sql, get_db_conn() );
	while( $option_row = pmysql_fetch_object( $option_result ) ){
		$select_option[] = $option_row;
	}

	pmysql_free_result( $option_result );

	return $select_option;
}
$basket_cnt = 0;
foreach( $Basket->basket as $bkVal ){
	$basket[] = obejct_setting( $bkVal );

	# 상품정보
	$bkProduct = $Basket->select_product( $bkVal->productcode );
	$option = array();
	# 옵션정보
	if( $basket->optionarr != '' ){
		if( $bkVal->op_type == 1 ){ // 독립형 옵션
			$tmp_option_subject = explode( '@#', $bkVal->opt1_idx );
			$tmp_option_content = explode( '@#', $bkVal->opt2_idx );
			foreach( $tmp_option_content as $contentKey=>$contentVal ){
				if( $contentVal != '' ){
					$opt2_val = $Basket->select_options( $bkVal->productcode, $contentVal, $bkVal->op_type );
					$option[$contentKey] = array(
						'option_code'          =>$opt2_val[0]->option_code,
						'option_price'         =>$opt2_val[0]->option_price,
						'option_quantity'      =>$opt2_val[0]->option_quantity,
						'option_quantity_noti' =>$opt2_val[0]->option_quantity_noti,
						'option_type'          =>$opt2_val[0]->option_type
					);
					$option_price += $opt2_val[0]->option_price;
				} else {
					$option[$contentKey] = array(
						'option_code'          =>'',
						'option_price'         =>0,
						'option_quantity'      =>0,
						'option_quantity_noti' =>0,
						'option_type'          =>1
					);
				}
			}

		} else { // 조합형 옵션
			$select_option = $Basket->select_options( $bkVal->productcode, $bkVal->optionarr, $bkVal->op_type );

			$option[] = array(
				'option_code'          =>$select_option[0]->option_code,
				'option_price'         =>$select_option[0]->option_price,
				'option_quantity'      =>$select_option[0]->option_quantity,
				'option_quantity_noti' =>$select_option[0]->option_quantity_noti,
				'option_type'          =>$select_option[0]->option_type
			);

			$option_price += $select_option[0]->option_price;
		}
	}
	$deli_obj[] = array(
		'vender'		=>$bkProduct->vender,
		'brand'		=>$bkProduct->brand,
		'productcode'	=>$bkProduct->productcode,
		'productname'	=>$bkProduct->productname,
		'quantity'		=>$bkVal->quantity,
		'deli'			=>$bkProduct->deli,
		'deli_price'	=>$bkProduct->deli_price,
		'deli_qty'		=>$bkProduct->deli_qty,
		'deli_select'	=>$bkProduct->deli_select,
		'price'			=>$bkProduct->sellprice,
		'delivery_type'=>$bkVal->delivery_type,
		'option'		=> $option
	);
	
	$brandVenderArr[$bkProduct->brand]	=  $bkProduct->vender;
    $basket_cnt++;

}

$Delivery->get_product( $deli_obj );
$Delivery->set_deli_item();
$vender_info    = $Delivery->get_vender();
$vender_deli    = $Delivery->get_vender_deli();
$free_deli      = $Delivery->get_free_deli();
$product_deli   = $Delivery->get_product_deli();

$brandArr = ProductToBrand_Sort( $basket );

//exdebug($brandArr);


$productImgPath = $Dir.DataDir."shopimages/product/";

$staff_yn       = $_ShopInfo->staff_yn;
if( $staff_yn == '' ) $staff_yn = 'N';





# 상품별 재고 체크를 위해 상품 재정렬 같은 옵션의 상품의 수량을 더한 후 비교 하기 위해 배열 셋팅
# 옵션은 조합형만 존재 한다고 하여 조합형에 대한 내용만 작업
$stockArrayCheck = array();
foreach( $brandArr as $brand=>$brandObj ){
	foreach( $brandObj as $product ) {
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
			if( strlen( $product['opt1_name'] ) > 0 ){
				if( $product['option_type'] == 0 ){ //조합형 옵션
					$tmpOptName = explode( '@#', $product['opt1_name'] );
					$tmpOptVal = explode( chr(30), $product['opt2_name'] );
					$tmpOptCnt	= 0;
					foreach( $tmpOptName as $tmpKey=>$tmpVal ){
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['productcode'] = $product['productcode'];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['productname'] = $product['productname'];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['prodcode'] = $product['prodcode'];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['colorcode'] = $product['colorcode'];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['size'] = $tmpOptVal[$tmpKey];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['store_code'] = $product['store_code'];
						$stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey].$product['store_code']]['quantity'] += $product['quantity'];
						# 매장 코드가 있을때만 매장 코드 없이 수량을 더해 놓는다. 매장 코드없는 상품은 같은 옵션의 전체 재고를 비교해야 하기 때문
						if($product['store_code']) $stockArrayCheck[$product['prodcode'].$tmpOptVal[$tmpKey]]['quantity'] += $product['quantity'];
					}
				}
			}
		}
	}
}

$stockSoldoutArray = array();
if(count($stockArrayCheck) > 0){
	foreach($stockArrayCheck as $k => $v){
		# 상품별 재고 체크
		if($v['prodcode'] && $v['colorcode']){
			$shopRealtimeStock = getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $v['store_code']);
			if($v['quantity'] > $shopRealtimeStock['sumqty']){
				$stockSoldoutArray[] = $v['productcode'].$v['size'].$v['store_code'];
			}
		}
	}
}
?>
	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>장바구니</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
	<div class="cart-order-wrap">
		<ul class="process_order clear">
			<li class="on">장바구니</li>
			<li>주문하기</li>
			<li>결제완료</li>
		</ul>

<?php
$sumprice = 0;
$deli_price = 0;
$reserve = 0;
foreach( $brandArr as $brand=>$brandObj ){ // 벤더별
	$brand_name = get_brand_name( $brand );
	$vender	=$brandVenderArr[$brand];
?>
		<!-- 상품별 섹션 반복 -->
		<h3 class="pro_title"><?=$brand_name?></h3>
		<section class="cart-list-wrap">
			<div class="total-select">
				<input type="checkbox" name="all-select" class="checkbox_custom">
				<label for="all-select">전체선택 / 해제</label>
			</div>
			<ul class="list vender_product_list">
<?php
	foreach( $brandObj as $product ) { // 상품별
		$sizeString = "";
		$storeData = getStoreData($product['store_code']);
        $product_price = ( $product['price'] + $product['option_price'] ) * $product['option_quantity'];
        $sumprice += $product_price;
        $option_price = $product['option_price'] * $product['option_quantity'];
		$reserve += $product['reserve'];

        if($product['soldout'] == "Y") {
            $disabled = "disabled";
            $soldout = "<br><span><img src=\"{$Dir}images/common/icon_soldout.gif\" border=0 align=absmiddle></span>";
        } else {
            $disabled = "";
            $soldout = "";
        }
?>
				<!-- 상품 리스트 반복-->
				<li class="vender_area">
                   <div class="product_area">
						<div class="box_cart">
                           <input type="checkbox" name="basket_idx" value="<?=$product['basketidx']?>" class="checkbox_custom" data-delivery_type = "<?=$product['delivery_type']?>" <?=$disabled?>>
							<figure class="mypage_goods">
								<div class="img">
									<a href="<?=$Dir.MDir?>productdetail.php?productcode=<?=$product['productcode']?>">
										<img src="<?= getProductImage( $productImgPath, $product['tinyimage'] )?>" alt="">
									</a>
								</div>
								<figcaption>
									<p class="brand">[<?=$brand_name?>]</p>
									<p class="name"><?=$product['productname']?></p>
									<?if($product['delivery_type'] == '1'){?>
										<p style = 'color:blue;'>[<?=$arrDeliveryType['1']?>] <?=$storeData['name']?></p>
										<p style = 'color:blue;'>예약일 : <?=$product['reservation_date']?></p>
									<?}else if($product['delivery_type'] == '2'){?>
										<p style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></p>
										<p style = 'color:blue;'>주소 : [<?=$product['post_code']?>] <?=$product['address1']?> <?=$product['address2']?></p>
									<?}?>


<?php
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
?>
									<p class="shipping">
<?php
			$tmp_opt1_subject = option_slice( $product['opt1_name'], '1' );
            $tmp_opt_content = option_slice( $product['opt2_name'], $product['option_type'] );
			$tmpOptCnt	= 0;
			$tmp_opt_content_html	= '';
			//exdebug($tmp_opt1_subject);
            foreach( $tmp_opt_content as $contentKey=>$contentVal ){
                if( $product['option_type'] == '1' ) {
                    $tmpVal = explode( chr( 30 ), $contentVal );
                    $optVal = $tmpVal[1];
                } else {
                    $optVal = $contentVal;
                }
				if ($tmp_opt_content_html !='') $tmp_opt_content_html	 .= ' / ';
                $tmp_opt_content_html	 .= $tmp_opt1_subject[$contentKey].' : '.$optVal;
				$sizeString = $optVal;
            } // opt_subject foreach
            
            if( strlen( $product['text_opt_subject'] ) > 0 ){
                $tmp_text_opt_content = option_slice( $product['text_opt_content'], '1' );
                foreach( $tmp_text_opt_content as $contentKey=>$contentVal ){
				if ($tmp_opt_content_html !='') $tmp_opt_content_html	 .= ' / ';
                    $tmp_opt_content_html	 .= $contentVal;
                } // opt_subject foreach
            }
			if( $option_price > 0 ) $tmp_opt_content_html	 .= '&nbsp;( + '.number_format( $option_price ).' 원)';

			if ($tmp_opt_content_html !='') $tmp_opt_content_html	 .= ' / ';
			$tmp_opt_content_html	 .= '수량 : '.number_format( $product['quantity'] ).'개';

			echo $tmp_opt_content_html;
?>
                                </p>
<?php
        } // opt1_name len if
?>
									<!-- p class="shipping">
									<?
										$product_deli_price	= $product_deli[$vender][$product['productcode']]['deli_price'];
										$product_deli_price = $product_deli_price > 0?number_format( $product_deli_price )."원":"무료";
									?>
									<?="배송비 ".$product_deli_price?>
									</p -->
									<p class="price">
										<span style = 'color:red;'>
											<?if(in_array(($product['productcode'].$sizeString.$product['store_code']), $stockSoldoutArray, true)){?>
												[<?=$sizeString?>] 재고부족
											<?}?>
										</span>
									</p>
									<p class="price"><span class="point-color"><?=number_format( ( $product['price'] * $product['quantity'] ) + $option_price )?>원</span></p>
								</figcaption>
							</figure>
                        </div><!-- //.box_cart -->

						<div class="btnwrap">
							<ul class="ea<?=$staff_yn == 'Y'?'3':'2'?>">
<?php
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
?>
								<?if($product['delivery_type'] == '0'){?>
									<li><a href="javascript:;" class="btn-def line btn-opt-change" name='option_change'>옵션/수량 변경</a></li>
								<?}?>
<?php
        } // option if
?>
								<li><a href="javascript:;" class="btn-def line" name="select_order" staff_yn='N'>바로구매</a></li>
								<?if( $staff_yn == 'Y' ) {?>
								<li><a href="javascript:;" class="btn-def line" name="select_order" staff_yn='Y'>임직원구매</a></li>
								<?}?>
							</ul>
						</div>
						<!-- 옵션 선택 레이어 -->
						<div class="opt-change-box">
							<ul class="clear">
<?php
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){ // 옵션정보 확인
            if( strlen( $product['opt1_name'] ) > 0 ){
                $opt1_subject = option_slice( $product['opt1_name'], '1' );
                $opt1_content = option_slice( $product['opt2_name'], $product['option_type'] );
                $opt_tf       = option_slice( $product['option1_tf'], '1' );
                $select_option_code = array();
                $option_depth = count( $opt1_subject ); // 옵션 길이
                foreach( $opt1_subject as $subjectKey=>$subjectVal ){
                    $opt_code = ''; // 검색에 사용될 옵션코드
                    if( $product['option_type'] == '0' ) { //조합형 옵션
                        $select_option_code[] = $opt1_content[$subjectKey]; // 선택된 옵션코드
                        $tmp_option_code = array();
                        if( $subjectKey > 0 ) {
                            for( $i = 0; $i < count( $select_option_code ) - 1; $i++ ){
                                $tmp_option_code[] = $select_option_code[$i]; // 현제 선택된 옵션코드를 빼고 검색대상에 넣어준다
                            }
                            $opt_code = implode( chr( 30 ), $tmp_option_code ); // 옵션코드 + 구분자 + 이후 옵션코드...
                        }
                        $get_option = get_option( $product['productcode'], $opt_code, $subjectKey ); //조합형 옵션정보
                    } else if( $product['option_type'] == '1' ) { // 독립형 옵션일 경우
                        $opt_code = $opt1_content[$subjectKey]; // 독립형인 경우 => 옵션명 + 구분자 + 옵션코드
                        $get_option = mobile_get_alone_option( $product['productcode'], $subjectVal ); // 독립형 옵션정보
                    }
                    //exdebug( $get_option );
?>
								<li name='opt' >
									<select class="select_def" name='opt_value' 
                                        data-type='<?=$product['option_type']?>' 
                                        data-prcode='<?=$product['productcode']?>'  
                                        data-depth='<?=($subjectKey + 1)?>' 
                                        data-qty='<?=$product['pr_quantity']?>'
                                        data-tf='<?=$opt_tf[$subjectKey]?>'>
										<option value='' ><?=$subjectVal?></option>
<?php
                    foreach( $get_option as $contentKey=>$contentVal ) { //옵션내용
                        $option_qty = $contentVal['qty']; // 수량
                        $option_text = ''; // 품절 text
                        $priceText = ''; // 가격
                        $option_desabled = false;
                        $alone_opt = array();
                        
                        if( $product['option_type'] == '0' && $subjectKey == 0 ) {
                            $select_code = $contentVal['code']; //조합형 옵션 코드형태 + 1depth 일때
                        } else if( $product['option_type'] == '0' && $subjectKey > 0 ) {
                            $select_code = $opt_code.chr(30).$contentVal['code']; //조합형 옵션 코드형태
                        } else if( $product['option_type'] == '1' ) {
                            $select_code = $contentVal['option_code']; // 독립형 옵션일때
                            $alone_opt = explode( chr( 30 ), $opt1_content[$subjectKey] );
                        }

                        //상품가격 text 처리 ( 조합형일 경우 마지막 depth의 옵션만 적용, 독립형일경우 전부다 적용 )
                        if( 
                            ( 
                              ( $product['option_type'] == '0' && $subjectKey + 1 == $option_depth ) || 
                              ( $product['option_type'] == '1' )
                            ) && $contentVal['price'] > 0 
                        ) {
                            $priceText = ' ( + '.number_format($contentVal['price']).' 원 )';
                        } else if(
                            ( 
                              ( $product['option_type'] == '0' && $subjectKey + 1 == $option_depth ) || 
                              ( $product['option_type'] == '1' )
                            ) && $contentVal['price'] < 0 
                        ) {
                            $priceText = ' ( - '.number_format($contentVal['price']).' 원 )';
                        } // 상품가격 if

                        //품절 text 처리
                        if( 
                            ( $option_qty !== null && $option_qty <= 0 ) && 
                            $product['option_type'] == '0' && 
                            $product['quantity'] < 999999999
                        ){
                            $option_text = '[품절]&nbsp;';
                            $option_desabled = true;
                        } //품절 id
?>
                                        <option value="<?=$select_code?>" 
                                            <? if( $contentVal['code'] == $opt1_content[$subjectKey] && $product['option_type'] == '0' ){ echo ' selected '; } ?> 
                                            <? if( $contentVal['code'] == $alone_opt[1] && $product['option_type'] == '1' ){ echo ' selected '; } ?> 
                                            <? if( $option_desabled ) { echo ' disabled '; } ?>
                                            <? if( $product['option_type'] == '0' && $subjectKey + 1 == $option_depth ) { echo 'data-qty="'.$option_qty.'"'; } ?>
                                        >
                                            <?=$option_text.$contentVal['code'].$priceText?>
                                        </option>
<?php
                    } // get_option if
?>
									</select>
								</li>

<?php
                } // opt_subject foreach
            } // opt1_name if

            if( strlen( $product['text_opt_subject'] ) > 0 ){ // 텍스트 옵션
                $text_opt_subject = option_slice( $product['text_opt_subject'], '1' );
                $text_opt_content = option_slice( $product['text_opt_content'], '1' );
                $text_opt_tf      = option_slice( $product['text_opt_tf'], '1' ); 
                $test_opt_maxln   = option_slice( $product['text_opt_maxlen'], '1' );
                foreach( $text_opt_subject as $textOptKey=>$textOptVal ){
                    $text_opt_tf_msg = '';
                    if( $text_opt_tf[$textOptKey] == 'T' ) $text_opt_tf_msg = '(필수)';

?>
								<li name='text-opt'>
									<input type='text' name='text_opt_value' value='<?=$text_opt_content[$textOptKey]?>' maxlength='<?=$test_opt_maxln[$textOptKey]?>' data-tf="<?=$text_opt_tf[$textOptKey]?>"  placeholder="<?=$textOptVal.' '.$text_opt_tf_msg?>">
									<span class="byte">(<strong><?=strlen($text_opt_content[$textOptKey])?></strong>/<?=$test_opt_maxln[$textOptKey]?>)</span>
								</li>
<?php
                } // text_opt_subject foreach
            } // text_opt_subject if
?>
<?php
        }// option if
?>
								<li name='qunatity'>
									<div class="quantity">
                                        <input type="number" name='basket_qty' value="<?=$product['quantity']?>" data-qty='<?=$product['pr_quantity']?>' data-optype='<?=$product['option_type']?>' >
										<button class="plus" type="button">증가</button>
										<button class="minus" type="button">감소</button>
									</div>
								</li>
								<li><button class="btn-def line" type="button" name='basket_modify'>옵션변경</button></li>
								<li><button class="btn-def line opt-change-hide" type="button" name="close">변경취소</button></li>
							</ul>
						</div>
						<!-- // 옵션 선택 레이어 -->
					</div>
                </li>
				<!-- // 상품 리스트 반복-->
<?php
	} // brandObj foreach
?>

			</ul>
			<div class="pay-price">
				<section>
					<h4>
<?php
	if( $vender_info[$vender] ){
		if( $vender_info[$vender]['deli_type'] == '1' && $vender_deli[$vender]['deli_price'] > 0 ){
?>
					[<?=$brand_name?>] 배송비 <strong><?=number_format( $vender_deli[$vender]['deli_price'] )?></strong>원

<?php
            if( $vender_info[$vender]['deli_price_min'] != 0 ){
?>
									&nbsp;(<?=number_format( $vender_info[$vender]['deli_price_min'] )?>원 이상 구매 시 무료)
<?php
            }
?>
<?php
			$deli_price += $vender_deli[$vender]['deli_price'];
		} else {
?>
									[<?=$brand_name?>] 배송비 무료
<?php
		}
		if( $product_deli[$vender] ){
?>
<?php
			$prDeliCnt	= 0;
			foreach( $product_deli[$vender] as $prDeliKey => $prDeliVal ){
?>
									/ <?=$prDeliVal['productname']?> 배송비 <strong><?=number_format( $prDeliVal['deli_price'] )?></strong>
<?php
				$deli_price += $prDeliVal['deli_price'];
			}
?>
<?php
		}
	} else {
?>
<?php
	}
?>
					</h4>
				</section>
			</div>
		</section>
		<!-- // 상품별 섹션 반복 -->
<?php
} // brandArr foreach

if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // 로그인을 안했을 경우
	$reserve	= 0;
}
?>
		<div class="btnwrap">
			<ul class="ea2">
				<li><a href="javascript:select_delete();" class="btn-def">선택삭제</a></li>
				<li><a href="javascript:basket_clear();" class="btn-def">전체삭제</a></li>
				<!--li><a href="#" class="btn-def">전체선택</a></li-->
			</ul>
		</div>

		<div class="total_order">
			<ul class="clear">
				<li>상품 합계<strong><?=number_format( $sumprice )?>원</strong></li>
				<li>배송비<strong><?=number_format(  $deli_price )?>원</strong></li>
			</ul>
			<div class="total_price">
				<label>결제할 금액</label>
				<span class="point-color">￦ <?=number_format( $sumprice + $deli_price )?>원</span>
			</div>
		</div><!-- //.total_order -->

		<div class="btnwrap btn_order">
			<ul class="ea2">
				<li><a href="javascript:select_order('N');" class="btn-def">선택상품 주문하기</a></li>
				<li><a href="javascript:order('N');" class="btn-point">전체상품 주문하기</a></li>
			</ul>
			<?if( $staff_yn == 'Y' ) {?>
			<ul class="ea2 mt-5">
				<li><a href="javascript:select_order('Y');" class="btn-def">선택상품 임직원주문</a></li>
				<li><a href="javascript:order('Y');" class="btn-point">전체상품 임직원주문</a></li>
			</ul>
			<?}?>
		</div>
	</div> <!-- .cart-order-wrap -->

    <form name='orderfrm' id='orderfrm' method='POST' action='<?=$Dir.MDir?>order.php' >
        <input type='hidden' name='basketidxs' id='basketidxs' value='' >
        <input type='hidden' name='staff_order' id='staff_order' value='N' >
    </form>

    <script>
        //옵션 변경창 on, off
        /*$(document).on( 'click', 'a[name="option_change"]', function() {
            var changeTag = $(this).parent().parent().parent().next();
            if( $(changeTag).hasClass('hide') ){
                $(changeTag).removeClass('hide');
            } else {
                $(changeTag).addClass('hide');
            }

        });
        //옵션 변경창 close
        $(document).on( 'click', 'a[name="close"]', function(){
            $(this).parent().parent().addClass('hide');
        });*/
        //전체선택 true / false
        $(document).on( 'click', 'input[name="all-select"]', function() {
            var select_state    = $(this).prop( 'checked' );
            var select_idx      = $('input[name="all-select"]').index( $(this) );
            var target_checkbox = $('.vender_product_list').eq( select_idx ).find('input[name="basket_idx"]');

            $.each( target_checkbox, function(){
                $(this).prop( 'checked', select_state );
            });
        });
        // 전체선택/해제 on off
        $(document).on( 'click', 'input[name="basket_idx"]', function() {
            var vender_area     = $(this).parents('.cart-list-wrap');
            var check_state     = true;
            var all_checkbox    = $('input[name="all-select"]').eq( $('.cart-list-wrap').index( vender_area ) );
            var target_checkbox = $( vender_area ).find('input[name="basket_idx"]');

            $(target_checkbox).prop( 'checked', function( i, val ) {
                if( val === false ) check_state = false;
            });

            $( all_checkbox ).prop( 'checked', check_state );
        });
        //옵션변경
        $(document).on( 'change', 'select[name="opt_value"]', function( event ){
            var product_area = $(this).parent().parent().parent().parent();
            var list_index = $('.product_area').index( product_area );
            var productcode = $(this).data('prcode');
            var product_qty = $(this).data('qty');
            var option_type = $(this).data('type');
            var option_code = '';
            var idx = $(this).data('depth');
            var next_select_box = $( product_area ).find('select[name="opt_value"]').eq( idx );
            // 독립형 옵션일 경우에는 작동을 안한다 ( 값을 이미 다 불러왔기 때문 )
            if( option_type == '1' ) return;
            // 선택된 옵션코드를 가져온다
            $(this).find('option').each( function(){
                if( $(this).prop( 'selected' ) ){
                    option_code = $(this).val();
                }
            });
            // 선택된 옵션 이후에 것들을 초기화
            $( product_area ).find('select[name="opt_value"]').each( function( i, obj ){
                if( i >= idx) {
                    $(this).html( '<option value="" > 선택 </option>' );
                    $(this).attr( 'disabled', 'true' );
                }
            });
            // 옵션 코드가 없으면 다음 옵션을 지정 못한다
            if( option_code == '' ) return;
            // 다음 옵션값을 가져온다
            $.ajax({
                type : "POST",
                url : "../front/ajax_option_select.php",
                data : { productcode : productcode, option_code : option_code, idx : idx },
                dataType : "json"
            }).done( function( data ){
                var html = '<option value="" > 선택 </option>';
                if( !jQuery.isEmptyObject( data ) ){
                    $.each( data , function( i, obj ){
                        var price_text = '';
                        var soldout = '';
                        var disabled_text = '';
                        var data_code = [];
                        var tmp_option_code = obj.option_code.split( chr( 30 ) );
                        for( var i=0; i < idx + 1; i++ ){
                            data_code.push( tmp_option_code[i] );
                        }
                        // 옵션 추가 가격 text
                        if( idx == $( product_area ).find('select[name="opt_value"]').length - 1 ){
                            if( obj.price != '' && obj.price > 0 ){
                                price_text = ' ( + ' + comma( obj.price ) + ' 원 )';
                            } else if( obj.price != '' && obj.price < 0 ) {
                                price_text = ' ( - ' + comma( obj.price ) + ' 원 )';
                            }
                        }
                        // 수량
                        if( obj.soldout == "1" && product_qty < 999999999 ) {
                            soldout = '[품절]&nbsp;';
                            disabled_text = 'disabled';
                        }

                        html += '<option value="' + data_code.join( chr( 30 ) ) + '" data-qty="' + obj.qty + '" ' + disabled_text + ' >' + soldout + obj.code + price_text +'</option>';
                    });
                    next_select_box.removeAttr( 'disabled' );
                    next_select_box.html( html );
                }
            });

        });
        // 텍스트 옵션 문자열 증가
        $(document).on( 'keyup', 'input[name="text_opt_value"]', function( event ) {
            var event_target = $(this).next().find('strong');
            event_target.html( $(this).val().length );
        });
        //수량변경 +
        $(document).on( 'click', '.quantity > .plus', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this).prev();
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( qty < parseInt( $(input_target).val() ) + 1 ){
                alert('재고가 부족합니다.');
                $(input_target).val( qty );
                return;
            } else {
                $(input_target).val( parseInt( $(input_target).val() ) + 1 );
            }

        });
        //수량변경 -
        $(document).on( 'click', '.quantity > .minus', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this).prev().prev();
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( parseInt( $(input_target).val() ) - 1 < 1 ) {
                alert('상품수량을 1개 이상 선택하셔야 합니다.');
                $(input_target).val( 1 );
                return;
            } else {
                $(input_target).val( parseInt( $(input_target).val() ) - 1 );
            }
        });
        //수량변경 직접입력
        $(document).on( 'keyup', 'input[name="basket_qty"]', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this);
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( qty < 1 ) {
                alert('상품수량을 1개 이상 선택하셔야 합니다.');
                $(input_target).val( 1 );
                return;
            } else if( qty < parseInt( $(input_target).val() ) ){
                alert('재고가 부족합니다.');
                $(input_target).val( qty );
                return;
            }
        });
        //숫자키 이외의 것을 막음
        $(document).on( 'keydown', 'input[name="number"]', function( event ) {
            if( !isNumKey( event ) ) event.preventDefault();
        });
        //장바구니 변경
        $(document).on( 'click', 'button[name="basket_modify"]', function( event ) {
            var product_area  = $(this).parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $( product_area ).find('input[name="basket_qty"]');
            var option_type   = $(input_target).data('optype');
            var basket_idx    = $( product_area ).find('input[name="basket_idx"]').val();
            var quantity      = 0;
            var qty           = 0;
            var opt_obj       = {};
            var opt_code      = '';
            var txt_op_code   = '';

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( qty < 1 ) {
                alert('상품수량을 1개 이상 선택하셔야 합니다.');
                $(input_target).val( 1 );
                return;
            } else if( qty < parseInt( $(input_target).val() ) ){
                alert('재고가 부족합니다.');
                $(input_target).val( qty );
                return;
            }
            //
            quantity = $(input_target).val();
            //해당 옵션정보를 가져옴
            opt_obj = select_opt( list_index, option_type );
            opt_code = opt_obj.op_code;
            txt_op_code = opt_obj.txt_op_code;

            if( !confirm('해당 옵션/수량을 수정하시겠습니까?') ){
                return;
            }

            $.ajax({
                method : 'POST',
                url : '../front/confirm_basket_proc.php',
                data : {
                    mode : 'modify', basketidx : basket_idx, quantity : quantity,
                    option_code : opt_code, text_content : txt_op_code, option_type : option_type
                },
                dataType : 'json'
            }).done( function( data ) {
                //console.log( data );
                alert( data.msg );
                location.href = 'basket.php';
            });
        });
        //장바구니 삭제
       /* $(document).on( 'click', 'a[name="select_delete"]', function( event ){
            var product_area  = $(this).parent().parent().parent().parent();
            var target_basket = $( product_area ).find('input[name="basket_idx"]');
            $('input[name="basket_idx"]').each( function(){
                $(this).prop( 'checked', false );
            });
            $( target_basket ).prop( 'checked', true );
            //삭제
            select_delete();
        });*/
        //선택 바로구매
         $(document).on( 'click', 'a[name="select_order"]', function( event ){
            var staff_yn  = $(this).attr('staff_yn');
            var product_area  = $(this).parent().parent().parent().parent();
            var target_basket = $( product_area ).find('input[name="basket_idx"]');
            $('input[name="basket_idx"]').each( function(){
                $(this).prop( 'checked', false );
            });
            $( target_basket ).prop( 'checked', true );
            //구매
            select_order(staff_yn);
        })
        // 옵션 체크
        function check_option( list_index, op_type ) {
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var txt_opt_target = $(product_area).find('input[name="text_opt_value"]');
            var err_type = true;

            if( $( txt_opt_target ).length > 0 ){ // text 옵션이 존재할 경우
                $( txt_opt_target ).each( function(){
                    if( $(this).data('tf') == 'T' && $(this).val() == '' ){
                        alert( '필수 옵션이 존재합니다.' );
                        $(this).focus();
                        err_type = false;
                        return false;
                    }
                });
            }

            if( err_type === false ) return err_type;

            if( $( opt_target ).length > 0 ){ // 옵션이 존재할 경우
                if( op_type == '0' ){ // 조합형 옵션
                    if(  $(opt_target).last().val() == '' ){
                        alert( '옵션을 선택하셔야 합니다.' );
                        err_type = false;
                        return err_type;
                    }
                } else { // 독립형 옵션
                    $(opt_target).each( function(){
                        if( $(this).data('tf') == 'T' && $(this).val() == '' ){
                            alert( '옵션을 선택하셔야 합니다.' );
                            err_type = false;
                            return false;
                        }
                    });
                }
            }

            return err_type;

        }
        //수량 체크
        function chk_quantity( list_index, op_type ){
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var product_qty    = $(product_area).find('input[name="basket_qty"]').data('qty');
            var qty            = 0;
            var option_qty     = 0;

            if( $( opt_target ).length > 0 ){
                if( op_type == '0' ){ // 조합형 옵션
                    var last_option = $(opt_target).last();
                    $( last_option ).find('option').each( function(){
                        if( $(this).prop( 'selected' ) ) {
                            option_qty = $(this).data('qty');
                        }
                    });
                    qty = option_qty;
                } else {
                    qty = product_qty;
                }
            } else {
                qty = product_qty;
            }

            return qty;

        }
        //옵션코드
        function select_opt( list_index, op_type ){
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var txt_opt_target = $(product_area).find('input[name="text_opt_value"]');
            var tmp_op_code = [];
            var op_code = '';
            var tmp_txt_op_code = [];
            var txt_op_code = '';
            var obj = {};

            if( $( txt_opt_target ).length > 0 ){ // text 옵션이 존재할 경우
                $( txt_opt_target ).each( function(){
                    tmp_txt_op_code.push( $(this).val() );
                });
                txt_op_code = tmp_txt_op_code.join('@#');
            }

            if( $( opt_target ).length > 0 ){ // 옵션이 존재할 경우
                if( op_type == '0' ){ // 조합형 옵션
                    op_code = $(opt_target).last().val();
                } else { // 독립형 옵션
                    $(opt_target).each( function(){
                        tmp_op_code.push( $(this).val() );
                    });
                    op_code = tmp_op_code.join('@#');
                }
            }

            obj = { "op_code" : op_code, "txt_op_code" : txt_op_code };

            return obj;

        }
        //장바구니 선택
        function basket_select(){
            var basketidxs = '';
            var cnt = 0;
            $("input[name='basket_idx']").each( function( idx, obj ) {
                if( $(this).prop( 'checked' ) ) {
                    basketidxs += $(this).val() + '|';
                    cnt++;
                }
            });
            if( cnt == 0 ) {
                alert('하나 이상의 상품을 선택하셔야 합니다.');
                return false;
            } else {
                basketidxs = basketidxs.substr( 0, basketidxs.length - 1 );
            }

            return basketidxs;
        }

        //장바구니 전체 삭제
        function basket_clear(){

            var basketidxs = '';
            var cnt = 0;
            $("input[name='basket_idx']").each( function( idx, obj ) {
                basketidxs += $(this).val() + '|';
                cnt++;
            });
            if( cnt == 0 ) {
                return;
            } else {
                basketidxs = basketidxs.substr( 0, basketidxs.length - 1 );
            }

            $.ajax({
                method : 'POST',
                url : '../front/confirm_basket_proc.php',
                data: { basketidxs : basketidxs, mode : 'delete' },
                dataType : 'json'
            }).done( function( data ) {
                if( data ){
                    alert( data.msg );
                    location.href="basket.php";
                } else {
                    alert('장바구니 삭제가 실패되었습니다.');
                }
            });

        }
        //개별삭제
        function select_delete(){
            var basketidxs = '';
            basketidxs = basket_select();
            if( basketidxs === false ) return;

            if( !confirm('해당 상품을 삭제하시겠습니까?') ){
                return;
            }

            $.ajax({
                method : 'POST',
                url : '../front/confirm_basket_proc.php',
                data: { basketidxs : basketidxs, mode : 'delete' },
                dataType : 'json'
            }).done( function( data ) {
                if( data ){
                    alert( data.msg );
                    location.href="basket.php";
                } else {
                    alert('장바구니 삭제가 실패되었습니다.');
                }
            });

        }
        // 개별 주문
        function select_order( staff_yn ){

			// 당일 수령 상품 수량 체크
			var delivery_Type_check = 0;
			$("input[name='basket_idx']:checked").each(function(){
				if($(this).data('delivery_type') == '2'){
					delivery_Type_check++;
				}
			});

			if(delivery_Type_check > 1){
				alert("당일수령 상품은 한 주문서에 하나만 주문이 가능합니다.");
			}else{
				var basketidxs = '';
				basketidxs = basket_select();
				if( basketidxs === false ) return;

				$("#basketidxs").val( basketidxs );
				<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
					$('#orderfrm').attr( 'action', 'login.php?chUrl=order.php?basketidxs=' + basketidxs );
				<?php } ?>
				if( staff_yn == 'Y' ) $('#staff_order').val('Y');
				$("#orderfrm").submit();
			}



        }
        // 전체 주문
        function order( staff_yn ){

            var basketidxs = '';

            $("input[name='basket_idx']").each( function( idx, obj ) {
                $(this).prop( 'checked', true );
            });


			// 당일 수령 상품 수량 체크
			var delivery_Type_check = 0;
			$("input[name='checkBasket']:checked").each(function(){
				if($(this).data('delivery_type') == '2'){
					delivery_Type_check++;
				}
			});

			if(delivery_Type_check > 1){
				alert("당일수령 상품은 한 주문서에 하나만 주문이 가능합니다.");
			}else{
				basketidxs = basket_select();
				if( basketidxs == '' ) {
					return;
				}
				$("#basketidxs").val( basketidxs );
				<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
					$('#orderfrm').attr( 'action', 'login.php?chUrl=order.php?basketidxs=' + basketidxs );
				<?php } ?>
				if( staff_yn == 'Y' ) {
					$('#staff_order').val('Y');
				} else {
					$('#staff_order').val('N');
				}
				$("#orderfrm").submit();
			}
        }

        // php chr() 대응
        function chr(code)
        {
            return String.fromCharCode(code);
        }

    </script>

<?php
$sql = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
pmysql_query($sql,get_db_conn());
?>
<?include_once('outline/footer_m.php');?>
