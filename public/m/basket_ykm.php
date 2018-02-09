<?
//$Dir="../";
include_once('outline/header_m.php');
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");
//$basketidxs = $_POST['basketidxs'];

// ��ٱ��Ͽ� ���ִ� ���� ��ǰ���� �����Ѵ�.
if ( strlen($_ShopInfo->getMemid()) > 0 ) {
	// �α���
	$directQuery = "a.id = '" . $_ShopInfo->getMemid() . "' ";
} else {
	// ��α���
	$directQuery = "a.tempkey='".$_ShopInfo->getTempkey()."' AND a.id = '' ";
}
$query="delete from tblbasket where basketidx in (select max(a.basketidx) from tblbasket a 
		left join tblproduct b on(a.productcode=b.productcode) 
		where b.hotdealyn='Y' and ".$directQuery." group by a.basketidx)";
pmysql_query($query);
////////////////////////////////////////////////////////////////

# ���� �Ⱦ� / ���� ���� ��ٱ��� ��ǰ ����
delDeliveryTypeData();





basket_restore(); // lib�� ��������� ��
$Basket = new Basket(); //��ٱ��� �ʱ�ȭ�� ���� �ҷ��´�
$Basket->revert_item(); // �ֹ������� ��ǰ�� �ǵ�����.
$Delivery = new Delivery();

//��ǰ �̹��� ���
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

	//ERP ��ǰ�� ���θ��� ������Ʈ�Ѵ�.
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

	#��ǰ�� ������ ���� '$reserveshow' ��ġȮ�� �ʿ�
	$reserve = getReserveConversion( $select_product->reserve, $select_product->reservetype, ( $select_product->sellprice + $basket->pricearr ) * $basket->quantity , "N" );

	$opt1 = $basket->opt1_idx;
	$opt2 = $basket->opt2_idx;
	$option_price = $basket->pricearr;
	$option_type = $basket->op_type;
	$option_quantity = $basket->quantity;
	$text_opt_subject = $basket->text_opt_subject;
	$text_opt_content = $basket->text_opt_content;

	#�������� ����
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

	# ��ǰ����
	$bkProduct = $Basket->select_product( $bkVal->productcode );
	$option = array();
	# �ɼ�����
	if( $basket->optionarr != '' ){
		if( $bkVal->op_type == 1 ){ // ������ �ɼ�
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

		} else { // ������ �ɼ�
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





# ��ǰ�� ��� üũ�� ���� ��ǰ ������ ���� �ɼ��� ��ǰ�� ������ ���� �� �� �ϱ� ���� �迭 ����
# �ɼ��� �������� ���� �Ѵٰ� �Ͽ� �������� ���� ���븸 �۾�
$stockArrayCheck = array();
foreach( $brandArr as $brand=>$brandObj ){
	foreach( $brandObj as $product ) {
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
			if( strlen( $product['opt1_name'] ) > 0 ){
				if( $product['option_type'] == 0 ){ //������ �ɼ�
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
						# ���� �ڵ尡 �������� ���� �ڵ� ���� ������ ���� ���´�. ���� �ڵ���� ��ǰ�� ���� �ɼ��� ��ü ��� ���ؾ� �ϱ� ����
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
		# ��ǰ�� ��� üũ
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
			<span>��ٱ���</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
	<div class="cart-order-wrap">
		<ul class="process_order clear">
			<li class="on">��ٱ���</li>
			<li>�ֹ��ϱ�</li>
			<li>�����Ϸ�</li>
		</ul>

<?php
$sumprice = 0;
$deli_price = 0;
$reserve = 0;
foreach( $brandArr as $brand=>$brandObj ){ // ������
	$brand_name = get_brand_name( $brand );
	$vender	=$brandVenderArr[$brand];
?>
		<!-- ��ǰ�� ���� �ݺ� -->
		<h3 class="pro_title"><?=$brand_name?></h3>
		<section class="cart-list-wrap">
			<div class="total-select">
				<input type="checkbox" name="all-select" class="checkbox_custom">
				<label for="all-select">��ü���� / ����</label>
			</div>
			<ul class="list vender_product_list">
<?php
	foreach( $brandObj as $product ) { // ��ǰ��
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
				<!-- ��ǰ ����Ʈ �ݺ�-->
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
										<p style = 'color:blue;'>������ : <?=$product['reservation_date']?></p>
									<?}else if($product['delivery_type'] == '2'){?>
										<p style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></p>
										<p style = 'color:blue;'>�ּ� : [<?=$product['post_code']?>] <?=$product['address1']?> <?=$product['address2']?></p>
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
			if( $option_price > 0 ) $tmp_opt_content_html	 .= '&nbsp;( + '.number_format( $option_price ).' ��)';

			if ($tmp_opt_content_html !='') $tmp_opt_content_html	 .= ' / ';
			$tmp_opt_content_html	 .= '���� : '.number_format( $product['quantity'] ).'��';

			echo $tmp_opt_content_html;
?>
                                </p>
<?php
        } // opt1_name len if
?>
									<!-- p class="shipping">
									<?
										$product_deli_price	= $product_deli[$vender][$product['productcode']]['deli_price'];
										$product_deli_price = $product_deli_price > 0?number_format( $product_deli_price )."��":"����";
									?>
									<?="��ۺ� ".$product_deli_price?>
									</p -->
									<p class="price">
										<span style = 'color:red;'>
											<?if(in_array(($product['productcode'].$sizeString.$product['store_code']), $stockSoldoutArray, true)){?>
												[<?=$sizeString?>] ������
											<?}?>
										</span>
									</p>
									<p class="price"><span class="point-color"><?=number_format( ( $product['price'] * $product['quantity'] ) + $option_price )?>��</span></p>
								</figcaption>
							</figure>
                        </div><!-- //.box_cart -->

						<div class="btnwrap">
							<ul class="ea<?=$staff_yn == 'Y'?'3':'2'?>">
<?php
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
?>
								<?if($product['delivery_type'] == '0'){?>
									<li><a href="javascript:;" class="btn-def line btn-opt-change" name='option_change'>�ɼ�/���� ����</a></li>
								<?}?>
<?php
        } // option if
?>
								<li><a href="javascript:;" class="btn-def line" name="select_order" staff_yn='N'>�ٷα���</a></li>
								<?if( $staff_yn == 'Y' ) {?>
								<li><a href="javascript:;" class="btn-def line" name="select_order" staff_yn='Y'>����������</a></li>
								<?}?>
							</ul>
						</div>
						<!-- �ɼ� ���� ���̾� -->
						<div class="opt-change-box">
							<ul class="clear">
<?php
		if( strlen( $product['opt1_name'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){ // �ɼ����� Ȯ��
            if( strlen( $product['opt1_name'] ) > 0 ){
                $opt1_subject = option_slice( $product['opt1_name'], '1' );
                $opt1_content = option_slice( $product['opt2_name'], $product['option_type'] );
                $opt_tf       = option_slice( $product['option1_tf'], '1' );
                $select_option_code = array();
                $option_depth = count( $opt1_subject ); // �ɼ� ����
                foreach( $opt1_subject as $subjectKey=>$subjectVal ){
                    $opt_code = ''; // �˻��� ���� �ɼ��ڵ�
                    if( $product['option_type'] == '0' ) { //������ �ɼ�
                        $select_option_code[] = $opt1_content[$subjectKey]; // ���õ� �ɼ��ڵ�
                        $tmp_option_code = array();
                        if( $subjectKey > 0 ) {
                            for( $i = 0; $i < count( $select_option_code ) - 1; $i++ ){
                                $tmp_option_code[] = $select_option_code[$i]; // ���� ���õ� �ɼ��ڵ带 ���� �˻���� �־��ش�
                            }
                            $opt_code = implode( chr( 30 ), $tmp_option_code ); // �ɼ��ڵ� + ������ + ���� �ɼ��ڵ�...
                        }
                        $get_option = get_option( $product['productcode'], $opt_code, $subjectKey ); //������ �ɼ�����
                    } else if( $product['option_type'] == '1' ) { // ������ �ɼ��� ���
                        $opt_code = $opt1_content[$subjectKey]; // �������� ��� => �ɼǸ� + ������ + �ɼ��ڵ�
                        $get_option = mobile_get_alone_option( $product['productcode'], $subjectVal ); // ������ �ɼ�����
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
                    foreach( $get_option as $contentKey=>$contentVal ) { //�ɼǳ���
                        $option_qty = $contentVal['qty']; // ����
                        $option_text = ''; // ǰ�� text
                        $priceText = ''; // ����
                        $option_desabled = false;
                        $alone_opt = array();
                        
                        if( $product['option_type'] == '0' && $subjectKey == 0 ) {
                            $select_code = $contentVal['code']; //������ �ɼ� �ڵ����� + 1depth �϶�
                        } else if( $product['option_type'] == '0' && $subjectKey > 0 ) {
                            $select_code = $opt_code.chr(30).$contentVal['code']; //������ �ɼ� �ڵ�����
                        } else if( $product['option_type'] == '1' ) {
                            $select_code = $contentVal['option_code']; // ������ �ɼ��϶�
                            $alone_opt = explode( chr( 30 ), $opt1_content[$subjectKey] );
                        }

                        //��ǰ���� text ó�� ( �������� ��� ������ depth�� �ɼǸ� ����, �������ϰ�� ���δ� ���� )
                        if( 
                            ( 
                              ( $product['option_type'] == '0' && $subjectKey + 1 == $option_depth ) || 
                              ( $product['option_type'] == '1' )
                            ) && $contentVal['price'] > 0 
                        ) {
                            $priceText = ' ( + '.number_format($contentVal['price']).' �� )';
                        } else if(
                            ( 
                              ( $product['option_type'] == '0' && $subjectKey + 1 == $option_depth ) || 
                              ( $product['option_type'] == '1' )
                            ) && $contentVal['price'] < 0 
                        ) {
                            $priceText = ' ( - '.number_format($contentVal['price']).' �� )';
                        } // ��ǰ���� if

                        //ǰ�� text ó��
                        if( 
                            ( $option_qty !== null && $option_qty <= 0 ) && 
                            $product['option_type'] == '0' && 
                            $product['quantity'] < 999999999
                        ){
                            $option_text = '[ǰ��]&nbsp;';
                            $option_desabled = true;
                        } //ǰ�� id
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

            if( strlen( $product['text_opt_subject'] ) > 0 ){ // �ؽ�Ʈ �ɼ�
                $text_opt_subject = option_slice( $product['text_opt_subject'], '1' );
                $text_opt_content = option_slice( $product['text_opt_content'], '1' );
                $text_opt_tf      = option_slice( $product['text_opt_tf'], '1' ); 
                $test_opt_maxln   = option_slice( $product['text_opt_maxlen'], '1' );
                foreach( $text_opt_subject as $textOptKey=>$textOptVal ){
                    $text_opt_tf_msg = '';
                    if( $text_opt_tf[$textOptKey] == 'T' ) $text_opt_tf_msg = '(�ʼ�)';

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
										<button class="plus" type="button">����</button>
										<button class="minus" type="button">����</button>
									</div>
								</li>
								<li><button class="btn-def line" type="button" name='basket_modify'>�ɼǺ���</button></li>
								<li><button class="btn-def line opt-change-hide" type="button" name="close">�������</button></li>
							</ul>
						</div>
						<!-- // �ɼ� ���� ���̾� -->
					</div>
                </li>
				<!-- // ��ǰ ����Ʈ �ݺ�-->
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
					[<?=$brand_name?>] ��ۺ� <strong><?=number_format( $vender_deli[$vender]['deli_price'] )?></strong>��

<?php
            if( $vender_info[$vender]['deli_price_min'] != 0 ){
?>
									&nbsp;(<?=number_format( $vender_info[$vender]['deli_price_min'] )?>�� �̻� ���� �� ����)
<?php
            }
?>
<?php
			$deli_price += $vender_deli[$vender]['deli_price'];
		} else {
?>
									[<?=$brand_name?>] ��ۺ� ����
<?php
		}
		if( $product_deli[$vender] ){
?>
<?php
			$prDeliCnt	= 0;
			foreach( $product_deli[$vender] as $prDeliKey => $prDeliVal ){
?>
									/ <?=$prDeliVal['productname']?> ��ۺ� <strong><?=number_format( $prDeliVal['deli_price'] )?></strong>
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
		<!-- // ��ǰ�� ���� �ݺ� -->
<?php
} // brandArr foreach

if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // �α����� ������ ���
	$reserve	= 0;
}
?>
		<div class="btnwrap">
			<ul class="ea2">
				<li><a href="javascript:select_delete();" class="btn-def">���û���</a></li>
				<li><a href="javascript:basket_clear();" class="btn-def">��ü����</a></li>
				<!--li><a href="#" class="btn-def">��ü����</a></li-->
			</ul>
		</div>

		<div class="total_order">
			<ul class="clear">
				<li>��ǰ �հ�<strong><?=number_format( $sumprice )?>��</strong></li>
				<li>��ۺ�<strong><?=number_format(  $deli_price )?>��</strong></li>
			</ul>
			<div class="total_price">
				<label>������ �ݾ�</label>
				<span class="point-color">�� <?=number_format( $sumprice + $deli_price )?>��</span>
			</div>
		</div><!-- //.total_order -->

		<div class="btnwrap btn_order">
			<ul class="ea2">
				<li><a href="javascript:select_order('N');" class="btn-def">���û�ǰ �ֹ��ϱ�</a></li>
				<li><a href="javascript:order('N');" class="btn-point">��ü��ǰ �ֹ��ϱ�</a></li>
			</ul>
			<?if( $staff_yn == 'Y' ) {?>
			<ul class="ea2 mt-5">
				<li><a href="javascript:select_order('Y');" class="btn-def">���û�ǰ �������ֹ�</a></li>
				<li><a href="javascript:order('Y');" class="btn-point">��ü��ǰ �������ֹ�</a></li>
			</ul>
			<?}?>
		</div>
	</div> <!-- .cart-order-wrap -->

    <form name='orderfrm' id='orderfrm' method='POST' action='<?=$Dir.MDir?>order.php' >
        <input type='hidden' name='basketidxs' id='basketidxs' value='' >
        <input type='hidden' name='staff_order' id='staff_order' value='N' >
    </form>

    <script>
        //�ɼ� ����â on, off
        /*$(document).on( 'click', 'a[name="option_change"]', function() {
            var changeTag = $(this).parent().parent().parent().next();
            if( $(changeTag).hasClass('hide') ){
                $(changeTag).removeClass('hide');
            } else {
                $(changeTag).addClass('hide');
            }

        });
        //�ɼ� ����â close
        $(document).on( 'click', 'a[name="close"]', function(){
            $(this).parent().parent().addClass('hide');
        });*/
        //��ü���� true / false
        $(document).on( 'click', 'input[name="all-select"]', function() {
            var select_state    = $(this).prop( 'checked' );
            var select_idx      = $('input[name="all-select"]').index( $(this) );
            var target_checkbox = $('.vender_product_list').eq( select_idx ).find('input[name="basket_idx"]');

            $.each( target_checkbox, function(){
                $(this).prop( 'checked', select_state );
            });
        });
        // ��ü����/���� on off
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
        //�ɼǺ���
        $(document).on( 'change', 'select[name="opt_value"]', function( event ){
            var product_area = $(this).parent().parent().parent().parent();
            var list_index = $('.product_area').index( product_area );
            var productcode = $(this).data('prcode');
            var product_qty = $(this).data('qty');
            var option_type = $(this).data('type');
            var option_code = '';
            var idx = $(this).data('depth');
            var next_select_box = $( product_area ).find('select[name="opt_value"]').eq( idx );
            // ������ �ɼ��� ��쿡�� �۵��� ���Ѵ� ( ���� �̹� �� �ҷ��Ա� ���� )
            if( option_type == '1' ) return;
            // ���õ� �ɼ��ڵ带 �����´�
            $(this).find('option').each( function(){
                if( $(this).prop( 'selected' ) ){
                    option_code = $(this).val();
                }
            });
            // ���õ� �ɼ� ���Ŀ� �͵��� �ʱ�ȭ
            $( product_area ).find('select[name="opt_value"]').each( function( i, obj ){
                if( i >= idx) {
                    $(this).html( '<option value="" > ���� </option>' );
                    $(this).attr( 'disabled', 'true' );
                }
            });
            // �ɼ� �ڵ尡 ������ ���� �ɼ��� ���� ���Ѵ�
            if( option_code == '' ) return;
            // ���� �ɼǰ��� �����´�
            $.ajax({
                type : "POST",
                url : "../front/ajax_option_select.php",
                data : { productcode : productcode, option_code : option_code, idx : idx },
                dataType : "json"
            }).done( function( data ){
                var html = '<option value="" > ���� </option>';
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
                        // �ɼ� �߰� ���� text
                        if( idx == $( product_area ).find('select[name="opt_value"]').length - 1 ){
                            if( obj.price != '' && obj.price > 0 ){
                                price_text = ' ( + ' + comma( obj.price ) + ' �� )';
                            } else if( obj.price != '' && obj.price < 0 ) {
                                price_text = ' ( - ' + comma( obj.price ) + ' �� )';
                            }
                        }
                        // ����
                        if( obj.soldout == "1" && product_qty < 999999999 ) {
                            soldout = '[ǰ��]&nbsp;';
                            disabled_text = 'disabled';
                        }

                        html += '<option value="' + data_code.join( chr( 30 ) ) + '" data-qty="' + obj.qty + '" ' + disabled_text + ' >' + soldout + obj.code + price_text +'</option>';
                    });
                    next_select_box.removeAttr( 'disabled' );
                    next_select_box.html( html );
                }
            });

        });
        // �ؽ�Ʈ �ɼ� ���ڿ� ����
        $(document).on( 'keyup', 'input[name="text_opt_value"]', function( event ) {
            var event_target = $(this).next().find('strong');
            event_target.html( $(this).val().length );
        });
        //�������� +
        $(document).on( 'click', '.quantity > .plus', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this).prev();
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( qty < parseInt( $(input_target).val() ) + 1 ){
                alert('��� �����մϴ�.');
                $(input_target).val( qty );
                return;
            } else {
                $(input_target).val( parseInt( $(input_target).val() ) + 1 );
            }

        });
        //�������� -
        $(document).on( 'click', '.quantity > .minus', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this).prev().prev();
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( parseInt( $(input_target).val() ) - 1 < 1 ) {
                alert('��ǰ������ 1�� �̻� �����ϼž� �մϴ�.');
                $(input_target).val( 1 );
                return;
            } else {
                $(input_target).val( parseInt( $(input_target).val() ) - 1 );
            }
        });
        //�������� �����Է�
        $(document).on( 'keyup', 'input[name="basket_qty"]', function( event ){
            var product_area  = $(this).parent().parent().parent().parent().parent();
            var list_index    = $('.product_area').index( product_area );
            var input_target  = $(this);
            var option_type   = $(input_target).data('optype');
            var qty           = 0;

            if( check_option( list_index, option_type ) === false ) return;
            qty = chk_quantity( list_index, option_type );

            if( qty < 1 ) {
                alert('��ǰ������ 1�� �̻� �����ϼž� �մϴ�.');
                $(input_target).val( 1 );
                return;
            } else if( qty < parseInt( $(input_target).val() ) ){
                alert('��� �����մϴ�.');
                $(input_target).val( qty );
                return;
            }
        });
        //����Ű �̿��� ���� ����
        $(document).on( 'keydown', 'input[name="number"]', function( event ) {
            if( !isNumKey( event ) ) event.preventDefault();
        });
        //��ٱ��� ����
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
                alert('��ǰ������ 1�� �̻� �����ϼž� �մϴ�.');
                $(input_target).val( 1 );
                return;
            } else if( qty < parseInt( $(input_target).val() ) ){
                alert('��� �����մϴ�.');
                $(input_target).val( qty );
                return;
            }
            //
            quantity = $(input_target).val();
            //�ش� �ɼ������� ������
            opt_obj = select_opt( list_index, option_type );
            opt_code = opt_obj.op_code;
            txt_op_code = opt_obj.txt_op_code;

            if( !confirm('�ش� �ɼ�/������ �����Ͻðڽ��ϱ�?') ){
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
        //��ٱ��� ����
       /* $(document).on( 'click', 'a[name="select_delete"]', function( event ){
            var product_area  = $(this).parent().parent().parent().parent();
            var target_basket = $( product_area ).find('input[name="basket_idx"]');
            $('input[name="basket_idx"]').each( function(){
                $(this).prop( 'checked', false );
            });
            $( target_basket ).prop( 'checked', true );
            //����
            select_delete();
        });*/
        //���� �ٷα���
         $(document).on( 'click', 'a[name="select_order"]', function( event ){
            var staff_yn  = $(this).attr('staff_yn');
            var product_area  = $(this).parent().parent().parent().parent();
            var target_basket = $( product_area ).find('input[name="basket_idx"]');
            $('input[name="basket_idx"]').each( function(){
                $(this).prop( 'checked', false );
            });
            $( target_basket ).prop( 'checked', true );
            //����
            select_order(staff_yn);
        })
        // �ɼ� üũ
        function check_option( list_index, op_type ) {
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var txt_opt_target = $(product_area).find('input[name="text_opt_value"]');
            var err_type = true;

            if( $( txt_opt_target ).length > 0 ){ // text �ɼ��� ������ ���
                $( txt_opt_target ).each( function(){
                    if( $(this).data('tf') == 'T' && $(this).val() == '' ){
                        alert( '�ʼ� �ɼ��� �����մϴ�.' );
                        $(this).focus();
                        err_type = false;
                        return false;
                    }
                });
            }

            if( err_type === false ) return err_type;

            if( $( opt_target ).length > 0 ){ // �ɼ��� ������ ���
                if( op_type == '0' ){ // ������ �ɼ�
                    if(  $(opt_target).last().val() == '' ){
                        alert( '�ɼ��� �����ϼž� �մϴ�.' );
                        err_type = false;
                        return err_type;
                    }
                } else { // ������ �ɼ�
                    $(opt_target).each( function(){
                        if( $(this).data('tf') == 'T' && $(this).val() == '' ){
                            alert( '�ɼ��� �����ϼž� �մϴ�.' );
                            err_type = false;
                            return false;
                        }
                    });
                }
            }

            return err_type;

        }
        //���� üũ
        function chk_quantity( list_index, op_type ){
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var product_qty    = $(product_area).find('input[name="basket_qty"]').data('qty');
            var qty            = 0;
            var option_qty     = 0;

            if( $( opt_target ).length > 0 ){
                if( op_type == '0' ){ // ������ �ɼ�
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
        //�ɼ��ڵ�
        function select_opt( list_index, op_type ){
            var product_area   = $('.product_area').eq( list_index );
            var opt_target     = $(product_area).find('select[name="opt_value"]');
            var txt_opt_target = $(product_area).find('input[name="text_opt_value"]');
            var tmp_op_code = [];
            var op_code = '';
            var tmp_txt_op_code = [];
            var txt_op_code = '';
            var obj = {};

            if( $( txt_opt_target ).length > 0 ){ // text �ɼ��� ������ ���
                $( txt_opt_target ).each( function(){
                    tmp_txt_op_code.push( $(this).val() );
                });
                txt_op_code = tmp_txt_op_code.join('@#');
            }

            if( $( opt_target ).length > 0 ){ // �ɼ��� ������ ���
                if( op_type == '0' ){ // ������ �ɼ�
                    op_code = $(opt_target).last().val();
                } else { // ������ �ɼ�
                    $(opt_target).each( function(){
                        tmp_op_code.push( $(this).val() );
                    });
                    op_code = tmp_op_code.join('@#');
                }
            }

            obj = { "op_code" : op_code, "txt_op_code" : txt_op_code };

            return obj;

        }
        //��ٱ��� ����
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
                alert('�ϳ� �̻��� ��ǰ�� �����ϼž� �մϴ�.');
                return false;
            } else {
                basketidxs = basketidxs.substr( 0, basketidxs.length - 1 );
            }

            return basketidxs;
        }

        //��ٱ��� ��ü ����
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
                    alert('��ٱ��� ������ ���еǾ����ϴ�.');
                }
            });

        }
        //��������
        function select_delete(){
            var basketidxs = '';
            basketidxs = basket_select();
            if( basketidxs === false ) return;

            if( !confirm('�ش� ��ǰ�� �����Ͻðڽ��ϱ�?') ){
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
                    alert('��ٱ��� ������ ���еǾ����ϴ�.');
                }
            });

        }
        // ���� �ֹ�
        function select_order( staff_yn ){

			// ���� ���� ��ǰ ���� üũ
			var delivery_Type_check = 0;
			$("input[name='basket_idx']:checked").each(function(){
				if($(this).data('delivery_type') == '2'){
					delivery_Type_check++;
				}
			});

			if(delivery_Type_check > 1){
				alert("���ϼ��� ��ǰ�� �� �ֹ����� �ϳ��� �ֹ��� �����մϴ�.");
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
        // ��ü �ֹ�
        function order( staff_yn ){

            var basketidxs = '';

            $("input[name='basket_idx']").each( function( idx, obj ) {
                $(this).prop( 'checked', true );
            });


			// ���� ���� ��ǰ ���� üũ
			var delivery_Type_check = 0;
			$("input[name='checkBasket']:checked").each(function(){
				if($(this).data('delivery_type') == '2'){
					delivery_Type_check++;
				}
			});

			if(delivery_Type_check > 1){
				alert("���ϼ��� ��ǰ�� �� �ֹ����� �ϳ��� �ֹ��� �����մϴ�.");
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

        // php chr() ����
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
