<?php

/********************************************************************* 
// 파 일 명 : delivery.class.php 
// 설    명 : 배송비 책정 
// 상세설명 : 상품의 배송비를 책정한다 
// 작 성 자 : 유동혁 2016.01.14
// 수 정 자 : 유동혁 2016-03-09
// 
// 
*********************************************************************/ 

CLASS Delivery {
    # 상점별 배송비 정책 array
    public $vender = array(
        /*
        'vender'=>array(
            'deli_type'      => 0,
            'deli_price'     => 0,
            'deli_price_min' => 0,
            'deli_select'    => 0
        )
        */
    );
    # 상품 정보
   public $product = array(
       /*
        $vender      = '벤더 번호',
        $productcode = '상품코드',
        $deli        = '상품 배송료 정책 ( deli )',
        $deli_price  = '배송비 ( deli_price )',
        $deli_select = '배송비 유형 ( deli_select )',
        $deli_qty    = '수량별 배송 기준 ( deli_qty )',
        $price       = '실 결제 가격'
        */
   );
    # 벤더별 배송 상품
    public $vender_deli = array();
    # 무료 배송 상품
    public $free_deli = array();
    # 개별배송 상품
    public $product_deli = array();

    # 선언 / 기본 정책 설정
    public function Delivery ()
    {
        global $_ShopInfo, $_data;
        //기본 배송비 정책을 세팅해줌
        $this->vender[0] = array(
            'deli_type'      => $_data->deli_basefeetype, // 배송료 유료/무료
            'deli_price'     => $_data->deli_basefee,     // 배송료
            'deli_price_min' => $_data->deli_miniprice,   // 최소 구매금액
            'deli_select'    => $_data->deli_select       // 선/착불 
        );

    }

    public function get_product ( $product_obj )
    {
        $cnt = count( $product_obj );

        for( $i = 0; $i < $cnt ; $i++ ){

            if( $product_obj[$i]['vender'] != 0 ){
                $this->set_vender_deli( $product_obj[$i]['vender'] ); // 벤더별 배송비 설정
            }
            
            $vender      = $product_obj[$i]['vender'];      // 벤더 번호
            $brand      = $product_obj[$i]['brand'];      // 브랜드 번호
            $productcode = $product_obj[$i]['productcode']; // 상품코드
			$productname = $product_obj[$i]['productname']; // 상품명
            $quantity    = $product_obj[$i]['quantity'];    // 구매상품 수량

			# 배송 타입이 택배배송일때만 배송비 책정
			if($product_obj[$i]['delivery_type'] == '0'){

				$deli        = $product_obj[$i]['deli'];        // 상품 배송료 정책 ( deli )
				$deli_price  = $product_obj[$i]['deli_price'];  // 배송비 ( deli_price )
				$deli_qty    = $product_obj[$i]['deli_qty'];    // 수량별 배송 기준 ( deli_qty )
				$deli_select = $product_obj[$i]['deli_select']; // 배송비 유형 ( deli_select )
				$price       = $product_obj[$i]['price'];       // 실 결제 가격

			}else if($product_obj[$i]['delivery_type'] == '1'){

				$deli        = 1;        // 상품 배송료 정책 ( deli )
				$deli_price  = 0;  // 배송비 ( deli_price )
				$deli_qty    = 0;    // 수량별 배송 기준 ( deli_qty )
				$deli_select = 0; // 배송비 유형 ( deli_select )
				$price       = $product_obj[$i]['price'];       // 실 결제 가격

			}else if($product_obj[$i]['delivery_type'] == '3'){

				$deli        = 2;        // 상품 배송료 정책 ( deli )
				$deli_price  = $product_obj[$i]['basket_deli_price'];  // 배송비 ( deli_price )
				$deli_qty    = 0;    // 수량별 배송 기준 ( deli_qty )
				$deli_select = 1; // 배송비 유형 ( deli_select )
				$price       = $product_obj[$i]['price'];       // 실 결제 가격

			}


            $options     = $product_obj[$i]['option'];
            $option_cnt  = count( $options );
            if( $option_cnt > 0 ){
                for( $j = 0; $j < $option_cnt; $j++ ){
                    $price += $options[$j]['option_price']; // 옵션가격 추가
                }
            }
            $price				= $price * $quantity;              // 가격 * 수량
            $price				-= $product_obj[$i]['dc_price'];    // 쿠폰할인
            $price				-= $product_obj[$i]['use_reserve']; // 마일리지 할인
			$delivery_type	= $product_obj[$i]['delivery_type']; // 배송 타입
            $product = array(
                'vender'       => $vender,
                'brand'       => $brand,
                'productcode'  => $productcode,
                'productname'  => $productname,
                'deli'         => $deli,
                'deli_price'   => $deli_price,
                'deli_select'  => $deli_select,
                'deli_qty'     => $deli_qty,
                'price'        => $price,
				'delivery_type'	=> $delivery_type, 
                'product_qty'  => $quantity
            );

            $this->product[] = $product;
        }
/*
        foreach( $this->product as $pKey=>$pVal ){
            $this->set_delivery( $pVal );
        }
*/
    }

    public function set_deli_item()
    {
        foreach( $this->product as $prKey=>$prVal ){
            $this->set_delivery( $prVal );
        }
    }

    # 배송방식 설정 변경 0 - 선불,  1 - 착불
    public function set_deli_select( $item )
    {
        foreach( $item as $key=>$val ){
            $this->vender[$key] = array_merge( $this->vender[$key], array( 'deli_select'=>$val ) );
        }
    }

    # 벤더별 배송정책 설정
    public function set_vender_deli( $vender )
    {
        $sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit, deli_area_limit, deli_select FROM tblvenderinfo WHERE vender = '".$vender."' ";
        $result = pmysql_query( $sql, get_db_conn() );
        $row = pmysql_fetch_object( $result );
        $this->vender[$vender] = array (
            'deli_type'      => $row->deli_pricetype,   //배송료 무료
            'deli_price'     => $row->deli_price,       //배송료
            'deli_price_min' => $row->deli_mini,        //최소 구매금액
            'deli_select'    => $row->deli_select       //배송방식 선택
        );
        pmysql_free_result( $result );
    }

    # 배송비 설정
    public function set_delivery( $product )
    {
        // deli => 0 - 기본배송비, 1 - 무료, 2 - 유료
        switch( $product['deli'] ){
            case 0 : // 0 - 기본배송비
                # 벤더별 배송 상품
                $this->set_vender_delivery( $product );
                break;
            case 1 : // 1 - 배송비 무료
                # 무료 배송 상품
                $this->free_deli[$product['productcode']]['vender']      = $product['vender'];
                $this->free_deli[$product['productcode']]['productcode'] = $product['productcode'];
                break;
            case 2 : // 2 - 배송비 유료
                # 개별배송 상품
                //$this->product_deli[] = $product;
                $this->set_product_delivery( $product );
                break;
            default :
                break;
        }
    }

    # 상품별 배송비 책정
    public function set_product_delivery( $product )
    {
		GLOBAL $arrDeliveryType;
        // deli_select => 0 - 고정배송비, 1 - 수량별 배송비 , 2 - 수량별 비례 배송비
        switch( $product['deli_select'] ){
            case 0 : // 고정배송비
                $this->product_deli[$product['vender']][$product['productcode']]['vender']      = $product['vender'];
                $this->product_deli[$product['vender']][$product['productcode']]['productcode'] = $product['productcode'];
				$this->product_deli[$product['vender']][$product['productcode']]['productname'] = $product['productname'];
                $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] = $product['product_qty'];
                $this->product_deli[$product['vender']][$product['productcode']]['deli_price']  = $product['deli_price'];
                break;
            case 1 : // 수량별 배송비 증가
				if($product['delivery_type'] != '0'){
					$product['productname'] = "[".$arrDeliveryType[$product['delivery_type']]."]".$product['productname'];
				}

                if( count( $this->product_deli[$product['vender']][$product['productcode']] ) > 0 ) { // 해당 상품이 존재하면
                    $tmp_product = $this->product_deli[$product['vender']][$product['productcode']];
                    $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] = ( $tmp_product['product_qty'] + $product['product_qty'] );
                    $this->product_deli[$product['vender']][$product['productcode']]['deli_price']  = $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] * $product['deli_price'];
                } else {
                    $this->product_deli[$product['vender']][$product['productcode']]['vender']      = $product['vender'];
                    $this->product_deli[$product['vender']][$product['productcode']]['productcode'] = $product['productcode'];
					$this->product_deli[$product['vender']][$product['productcode']]['productname'] = $product['productname'];
                    $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] = $product['product_qty'];
                    $this->product_deli[$product['vender']][$product['productcode']]['deli_price']  = ( $product['deli_price'] * $product['product_qty'] );
                }
                break;
            case 2 : // 수량별 비례 배송비
				if($product['delivery_type'] != '0'){
					$product['productname'] = "[".$arrDeliveryType[$product['delivery_type']]."]".$product['productname'];
				}
                if( count( $this->product_deli[$product['vender']][$product['productcode']] ) > 0 ) { // 해당 상품이 존재하면
                    $tmp_product = $this->product_deli[$product['vender']][$product['productcode']];
                    $tmp_product['product_qty']  = ( $tmp_product['product_qty'] + $product['product_qty'] );
                    $tempDeilQty_1 = $tmp_product['product_qty'] % $product['deli_qty'];
                    $tempDeliQty_2 = floor( $tmp_product['product_qty'] / $product['deli_qty'] );
                    $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] = $tmp_product['product_qty'];
                    $this->product_deli[$product['vender']][$product['productcode']]['deli_price'] = $product['deli_price'] * ( $tempDeilQty_1 + $tempDeliQty_2 );
                } else {
                    $tempDeilQty_1 = $product['product_qty'] % $product['deli_qty'];
                    $tempDeliQty_2 = floor( $product['product_qty'] / $product['deli_qty'] );
                    $this->product_deli[$product['vender']][$product['productcode']]['vender']      = $product['vender'];
                    $this->product_deli[$product['vender']][$product['productcode']]['productcode'] = $product['productcode'];
					$this->product_deli[$product['vender']][$product['productcode']]['productname'] = $product['productname'];
                    $this->product_deli[$product['vender']][$product['productcode']]['product_qty'] = $product['product_qty'];
                    $this->product_deli[$product['vender']][$product['productcode']]['deli_price']  = ( $product['deli_price'] * ( $tempDeilQty_1 + $tempDeliQty_2 ) );
                }
                break;
            default :
                break;
        }

    }
    # 벤더별 배송비 책정
    public function set_vender_delivery( $product )
    {
        /*
            deli_type => 배송정책
                0 - 무료, 1 - 유료 // deli_pricetype, deli_basefeetype
            deli_price => 배송비 
                배송비 입력 // deli_price, deli_basefee
            deli_price_min => 배송비 부과 기준
                입력금액 미만일 경우 배송비 부과 // deli_mini, deli_miniprice
            deli_select => 배송방식 선택
                0 - 선불, 1 - 착불, 2 - 선/착불 선택 // deli_select, deli_select
        */
        
        if( $this->vender[$product['vender']]['deli_type'] == '1' ){
            if( count( $this->vender_deli[$product['vender']] ) > 0 ){
                $this->vender_deli[$product['vender']]['productcode'][] = $product['productcode'];
                $this->vender_deli[$product['vender']]['price']        += $product['price'];
                if ( $this->vender[$product['vender']]['deli_price_min'] <=  $this->vender_deli[$product['vender']]['price'] ) {
                    if( $this->vender[$product['vender']]['deli_price_min'] == 0 ){
                        $this->vender_deli[$product['vender']]['deli_price'] = $this->vender[$product['vender']]['deli_price'];
                    } else {
                        $this->vender_deli[$product['vender']]['deli_price'] = 0;
                    }
                }
            } else {
                $this->vender_deli[$product['vender']]['vender']        = $product['vender'];
                $this->vender_deli[$product['vender']]['productcode'][] = $product['productcode'];
                $this->vender_deli[$product['vender']]['price']         = $product['price'];
                $this->vender_deli[$product['vender']]['deli_select']   = $this->vender[$product['vender']]['deli_select'];

                if ( $this->vender[$product['vender']]['deli_price_min'] >  $this->vender_deli[$product['vender']]['price'] ) {
                    $this->vender_deli[$product['vender']]['deli_price'] = $this->vender[$product['vender']]['deli_price'];
                } else {
                    if( $this->vender[$product['vender']]['deli_price_min'] == 0 ){
                        $this->vender_deli[$product['vender']]['deli_price'] = $this->vender[$product['vender']]['deli_price'];
                    } else {
                        $this->vender_deli[$product['vender']]['deli_price'] = 0;
                    }
                }
                
            }

        } else {
            $this->free_deli[$product['vender']]['vender']        = $product['vender'];
            $this->free_deli[$product['vender']]['productcode'] = $product['productcode'];
        }
    }

    # 벤더별 배송 상품
    public function get_vender_deli()
    {
        return $this->vender_deli;
    }
     #  무료 배송 상품
    public function get_free_deli()
    {
        return $this->free_deli;
    }
     # 개별배송 상품
    public function get_product_deli()
    {
        return $this->product_deli;
    }

	# 벤더별 배송 정보
	public function get_vender()
	{
		return $this->vender;
	}
}



?>