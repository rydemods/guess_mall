<?php

//include_once('basket.class.php');

/*********************************************************************
// 파 일 명 : order.class.php
// 설    명 : 상품 주문
// 상세설명 : 장바구니에 상품의 판매가능여부 체크 및 불러와 주문에 넣는다
// 작 성 자 : 유동혁 2016.01.13
// 수 정 자 :
//
//
*********************************************************************/

class Order extends Basket {

	protected $order = array(); // 주문정보
	protected $order_object; // 주문 상품정보
	//전체 가격 및 할인정보
	protected $total_price_set = array(
		'price'=>0, //상품 가격
		'deli_price'=>0, // 배송비
		'dc_price'=>0, //쿠폰 할인가
		'reserve'=>0, // 적립금 할인가
		'point'=>0, // 포인트 할인가
		'staff_price'=>0, // 임직원 할인가
		'cooper_price'=>0, // 협력사 할인가
		'timesale_price'=>0 // 타임세일 할인가
	);
	protected $last_price = 0; //최종 가격
	protected $product_coupon; //상품쿠폰정보
	protected $basket_coupon; //장바구니쿠폰정보
	protected $vender_deli; // 벤더별 배송비
	protected $product_deli; // 상품별 배송비
	protected $vender_info; //벤더별 배송비 정책 ( 선착불 상태를 확인하기 위하여 필요 )
	protected $deli_free = false; // 배송비 무료 상태 true - 무료 / false - 일반
	protected $deli_ori_price = 0; // 착불 / 무료 배송료
	protected $staff_order = 'N'; // 임직원 주문
	protected $cooper_order = 'N'; // 협력사 주문
	protected $sync_bon_code='A1801B'; //본사 코드
	protected $order_reserve = array(
		/*
		array(
			'op_price'=>0,
			'op_reserve'=>0,
			'op_rate'=>0,
			'deli_price'=>0,
			'deli_reserve'=>0,
			'deli_rate'=>0
		)
		*/
	);


	public function Order()
	{
		global $_data;

		$this->get_item();
	}

	#주문을 위하여 tempkey변경
	protected function basket_tempkey_change( $basketidxs )
	{
        /*
		global $_ShopInfo;
		unset( $this->basket );
		$sql_r = "UPDATE tblbasket SET tempkey='".$_ShopInfo->getTempkey()."' WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'";
		pmysql_query( $sql_r, get_db_conn() );
		$strWhere = $this->split_basketidxs( $basketidxs );

		$sql = "UPDATE tblbasket SET tempkey='".$_ShopInfo->getTempkeySelectItem()."' ";
		$sql.= "WHERE basketidx NOT IN ('".$strWhere."') AND tempkey = '".$_ShopInfo->getTempkey()."' ";

		pmysql_query( $sql, get_db_conn() );
		$this->get_item();
        */

	}

	# 주문할 장바구니의 상품을 가져옴
	public function basket_select_item( $basketidxs )
	{
		global $_ShopInfo;
		unset( $this->basket );

		$whereQuery = "";
		if ( strlen($_ShopInfo->getMemid()) > 0 ) {
			// 로그인
			$whereQuery = "a.id = '" . $_ShopInfo->getMemid() . "' ";
		} else {
			// 비로그인
			$whereQuery = "a.tempkey='".$_ShopInfo->getTempkey()."' AND a.id = '' ";
		}

		$strWhere = '';
		$basketWhere = '';
		if( strlen( trim( $basketidxs ) ) > 0 ){
			$strWhere = $this->split_basketidxs( $basketidxs );
			$basketWhere = " AND a.basketidx IN ('".$strWhere."')  ";
		}


		$sql = "SELECT a.tempkey, a.productcode, a.quantity, a.basketidx, a.id, a.optionarr, a.quantityarr, a.pricearr, a.opt1_idx, a.opt2_idx, a.op_type, a.delivery_price, ";
		$sql.= "a.text_opt_subject, a.text_opt_content, a.delivery_type, a.reservation_date, a.store_code, a.post_code, a.address1, a.address2, b.colorcode, b.prodcode, a.gps_x, a.gps_y ";
		$sql.= "FROM tblbasket a JOIN tblproduct b ON a.productcode = b.productcode WHERE {$whereQuery} ".$basketWhere;
		$sql.= "ORDER BY a.date DESC, a.basketidx ASC ";

		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$this->basket[] = $row;

			//ERP 상품을 쇼핑몰에 업데이트한다.
			if ($row->opt1_idx == 'SIZE') {
				getUpErpProductUpdate($row->productcode, $row->opt2_idx);
			}
		}

		pmysql_free_result;

	}

	#장바구니에 구매 불가능한 상품이 있는지 체크함
	public function basket_check()
	{
		global $_ShopInfo;

		foreach ( $this->basket as $basketKey=>$basketVal ) {
			$basket_item = '';
			$basket_option = '';

			#상품 그룹을 체크함
			$this->check_product_group( $basketVal->productcode );

			//ERP 상품을 쇼핑몰에 업데이트한다.
			getUpErpProductUpdate($basketVal->productcode);

			#상품을 체크함
			$basket_item = $this->select_product( $basketVal->productcode );

			if( !$basket_item ) {
				$this->success = false; //실제 상품이 존재하지 않음
			} else {
				#상품 조건부 수량을 체크함
				$this->check_product( $basket_item, $basketVal->quantity );
				#해당 상품 수량 체크
				$this->check_product_quantity( $basket_item, $basketVal->quantity );
			}

			#옵션이 존재할 경우
			if( $basketVal->optionarr != '' ){

				#해당 옵션정보를 가져옴
				if( $basketVal->op_type == 1 ){ // 독립형 옵션일 경우
					$tmp_option = explode( '@#', $basketVal->opt2_idx );
					foreach( $tmp_option as $optionKey=>$optionVal ){
						if( $optionVal != '' ){
							$basket_option = $this->select_options( $basketVal->productcode, $optionVal, $basketVal->op_type );
							if( !$basket_option ){
								$this->success = false; //실제 옵션이 존재하지 않음 또는 사용 중지된 옵션
							} else {
								if( $basket_item->quantity < 999999999 ){
									$this->check_option_quantity( $basket_option[0], $basketVal->quantityarr );
								}
							}
						}
					}
				} else { // 조합형 옵션일 경우
					$basket_option = $this->select_options( $basketVal->productcode, $basketVal->optionarr, $basketVal->op_type );
					if( !$basket_option ) {
						$this->success = false; //실제 옵션이 존재하지 않음 또는 사용 중지된 옵션
					} else {
						#해당 옵션 수량 체크
						if( $basket_item->quantity < 999999999 ){
							$this->check_option_quantity( $basket_option[0], $basketVal->quantityarr );
						}
					}
				}
			}


		}

		return $this->success;
	}

	#장바구니별 상품세팅
	protected function obejct_setting( $basket )
	{
		$basket_object = '';
		$reserve = 0;
		$option_price = 0; // 옵션총합가격

		/*
		$opt1 = '';
		$opt2 = '';
		$option_code = '';
		$option_quantity = 0;
		$option_type = 0;
		*/
		$option = array(); //옵션정보

		$select_product = $this->select_product( $basket->productcode );
		#상품별 적립금 세팅 '$reserveshow' 위치확인 필요
		//$reserve = getReserveConversion( $select_product->reserve, $select_product->reservetype, $select_product->sellprice, "N" );

		$ori_sellprice	= $select_product->sellprice;
		$timesale_detail="";

		//임직원 주문일경우 가격, 적립금을 다시 세팅한다.
		if( $this->staff_order == 'Y' ){
			$ori_sellprice	= $select_product->consumerprice;
			list($staff_rate) = pmysql_fetch("SELECT staff_dc_rate FROM tblproduct where productcode= '".$basket->productcode."' ");
			$select_product->sellprice	= round( ( (100 - $staff_rate) / 100 ) * $select_product->consumerprice );
			$select_product->reserve	= 0;
		}else if( $this->cooper_order == 'Y' ){
				list($sale_num) = pmysql_fetch("SELECT b.group_productcode FROM tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$basket->id."' ");
				$c_productcode = $basket->productcode;
				list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");

				$t_product_price = $select_product->consumerprice;
				$c_product_price = $company_price;

				if($c_product_price >= $t_product_price || $c_product_price == 0){
					$select_product->sellprice = $t_product_price;
				}else if($c_product_price > $ori_sellprice){
					$select_product->sellprice = $ori_sellprice;
				}else{
					$select_product->sellprice = $c_product_price;
				}

//			if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){}
/*		
				$ori_sellprice	= $select_product->consumerprice;
				list($cooper_rate) = pmysql_fetch("SELECT cooper_dc_rate FROM tblproduct where productcode= '".$basket->productcode."' ");
				$select_product->sellprice	= round( ( (100 - $cooper_rate) / 100 ) * $select_product->consumerprice );
*/		
			
			//브랜드별 적립금 퍼센트 처리
			$brand_rate_point=$this->brand_rate_reserve($basket->productcode);
			
			$select_product->reserve	= $brand_rate_point;
		}else{
			//기간할인이 있을경우
			if($select_product->timesale_code){
				$select_product->sellprice=timesale_price($select_product->productcode);
				$time_d_sql="select * from tblproduct_timesale where timesale_sno='".$select_product->timesale_code."'";
				$time_d_result=pmysql_query($time_d_sql);
				$time_d_data=pmysql_fetch_object($time_d_result);
				$timesale_detail=$time_d_data->timesale_sno."@#".$time_d_data->timesale_type."@#".$time_d_data->title."@#".$time_d_data->price_rate."@#".$time_d_data->rate_type."@#".$time_d_data->sdate."@#".$time_d_data->edate."@#".$time_d_data->order_time."@#".$time_d_data->week."@#".$time_d_data->newday."@#".$time_d_data->state;
				
			}
			//브랜드별 적립금 퍼센트 처리
			$brand_rate_point=$this->brand_rate_reserve($basket->productcode);
			
			$select_product->reserve	= $brand_rate_point;
		}

		#옵션정보 세팅
		if( $basket->optionarr != '' ){

			if( $basket->op_type == 1 ){ // 독립형 옵션
				$tmp_option_subject = explode( '@#', $basket->opt1_idx );
				$tmp_option_content = explode( '@#', $basket->opt2_idx );
				foreach( $tmp_option_content as $contentKey=>$contentVal ){
					if( $contentVal != '' ){
						$opt2_val = $this->select_options( $basket->productcode, $contentVal, $basket->op_type );

						$ori_option_price1	= $opt2_val[0]->option_price;

						//임직원 주문일경우 옵션가격을 다시 세팅한다.
						if( $this->staff_order == 'Y' ){
							$opt2_val[0]->option_price	= round( ( (100 - $staff_rate) / 100 ) * $opt2_val[0]->option_price );
						}

						$option[$contentKey] = array(
							'option_code'          =>$opt2_val[0]->option_code,
							'option_price'         =>$opt2_val[0]->option_price,
							'ori_option_price'    =>$ori_option_price1,
							'option_quantity'      =>$opt2_val[0]->option_quantity,
							'option_quantity_noti' =>$opt2_val[0]->option_quantity_noti,
							'option_type'          =>$opt2_val[0]->option_type,
							'self_goods_code'      =>$opt2_val[0]->self_goods_code
						);
						$option_price += $opt2_val[0]->option_price;
					} else {
						$option[$contentKey] = array(
							'option_code'          =>'',
							'option_price'         =>0,
							'ori_option_price'    =>0,
							'option_quantity'      =>0,
							'option_quantity_noti' =>0,
							'option_type'          =>1,
							'self_goods_code'      =>''
						);
					}
				}

			} else { // 조합형 옵션
				$select_option = $this->select_options( $basket->productcode, $basket->optionarr, $basket->op_type );

				$ori_option_price2	= $select_option[0]->option_price;

				//임직원 주문일경우 옵션가격을 다시 세팅한다.
				if( $this->staff_order == 'Y' ){
					$select_option[0]->option_price	= round( ( (100 - $staff_rate) / 100 ) * $select_option[0]->option_price );
				}

				$option[] = array(
					'option_code'          =>$select_option[0]->option_code,
					'option_price'         =>$select_option[0]->option_price,
					'ori_option_price'    =>$ori_option_price2,
					'option_quantity'      =>$select_option[0]->option_quantity,
					'option_quantity_noti' =>$select_option[0]->option_quantity_noti,
					'option_type'          =>$select_option[0]->option_type,
					'self_goods_code'      =>$select_option[0]->self_goods_code
				);

				$option_price += $select_option[0]->option_price;
			}

			$option_quantity = $basket->quantity;

		} else {
			$option_quantity = $basket->quantity;
		}


		#기초정보 세팅
		$basket_object = array(
			'basketidx'        =>$basket->basketidx,
			/*상품 기본정책으로 배송비 측정2017-02-27
			'vender'           =>$select_product->vender,*/
			'vender'           =>0,
			'brand'            =>$select_product->brand,
			'productcode'      =>$select_product->productcode,
			'productname'      =>$select_product->productname,
			'price'            =>$select_product->sellprice,
			'ori_price'            =>$ori_sellprice,
			'quantity'         =>$basket->quantity,
			'reserve'          =>$select_product->reserve,
			'reservetype'      =>$select_product->reservetype,
			'selfcode'         =>$select_product->selfcode,
			'addcode'          =>$select_product->addcode,
			'tinyimage'        =>$select_product->tinyimage,
			'deli'             =>$select_product->deli,
			'deli_price'       =>$select_product->deli_price,
			'deli_qty'         =>$select_product->deli_qty,
			'deli_select'      =>$select_product->deli_select,
			'ci_no'            =>'',
			'coupon_code'      =>'',
			'dc_price'         =>0,
			'use_reserve'      =>0,
			'use_point'      =>0,
			//'detail_deli'    =>$select_product->detail_deli,
			//'deli_min_price' =>$select_product->deli_min_price,
			//'deli_package'   =>$select_product->deli_package
			'option_subject'   =>$basket->opt1_idx,
			'option_type'      =>$basket->op_type,
			//'option_price'     =>$option_price,
			'option_quantity'  =>$option_quantity,
			'text_opt_subject' =>$basket->text_opt_subject,
			'text_opt_content' =>$basket->text_opt_content,
            'rate'             =>$select_product->rate,
            'self_goods_code' =>$select_product->self_goods_code,
			'option'           =>$option,
			'delivery_type'=>$basket->delivery_type,
			'reservation_date'=>$basket->reservation_date,
			'store_code'=>$basket->store_code,
			'post_code'=>$basket->post_code,
			'address1'=>$basket->address1,
			'address2'=>$basket->address2,
			'colorcode'=>$basket->colorcode,
			'prodcode'=>$basket->prodcode,
			'basket_deli_price'        =>$basket->delivery_price,
			'timesale_detail'			=>$timesale_detail,
			'gps_x'            =>$basket->gps_x,
			'gps_y'            =>$basket->gps_y
		);

		// basket_object 에 self_goods_code 추가 (20160610_김재수 추가)

		#주문정보 세팅
		$this->order_object[] = $basket_object;

		#전체금액 세팅
		$this->total_price_set['price'] += ( $select_product->sellprice + $option_price ) * $option_quantity;

		return $basket_object;
	}

	#유일키 발급
	protected function set_orderkey()
	{
		global $_ShopInfo, $_data;
		$ordercode = '';
		$id = '';

		if( ord( $_ShopInfo->getMemid() ) > 0 ) {
			$sql = "SELECT id FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
			$result = pmysql_query( $sql, get_db_conn() );
			if( $row = pmysql_fetch_object( $result ) ) {
				//$this->order['ordercode'] = unique_id();
				//$this->order['id'] =  $row->id;
				$ordercode = unique_id();
				$id = $row->id;
				pmysql_free_result( $result );
			} else {
				$_ShopInfo->SetMemNULL();
				//guest
				//$this->order['ordercode'] = unique_id()."X";
				//$this->order['id'] = "X".date("iHs");
				$ordercode = unique_id()."X";
				$id = "X".date("iHs").$this->order['sender_name'];
			}
		} else {
			//guest
			//$this->order['ordercode'] = unique_id()."X";
			//$this->order['id'] = "X".date("iHs");
			$ordercode = unique_id()."X";
			$id = "X".date("iHs").$this->order['sender_name'];
		}
		$arr = array( 'ordercode'=>$ordercode, 'id'=>$id );
		$this->push_info( $arr );

	}

	#pg_data setting
	protected function set_pgkey()
	{
		global $_ShopInfo, $_data;

		#### PG 데이타 세팅 ####
		$_ShopInfo->getPgdata();

		$pg_type="";
		$paymethod = $this->order['paymethod'];
		switch ($paymethod) {
			case "B":
				break;
			case "V":
				$pgid_info=GetEscrowType($_data->trans_id);
				$pg_type=$pgid_info["PG"];
				break;
			case "O":
				$pgid_info=GetEscrowType($_data->virtual_id);
				$pg_type=$pgid_info["PG"];
				break;
			case "Q":
				$pgid_info=GetEscrowType($_data->escrow_id);
				$pg_type=$pgid_info["PG"];
				break;
			case "C":
				$pgid_info=GetEscrowType($_data->card_id);
				$pg_type=$pgid_info["PG"];
				break;
			case "P":
				$pgid_info=GetEscrowType($_data->card_id);
				$pg_type=$pgid_info["PG"];
				break;
			case "M":
                // =============================================
                // KCP 휴대폰 결제
                // =============================================
				$pgid_info=GetEscrowType($_data->mobile_id);
				$pg_type=$pgid_info["PG"];

                // =============================================
                // 다날 휴대폰 결제
                // =============================================
				//$pg_type="E";
				break;
			case "Y":
                // =============================================
                // PAYCO 결제
                // =============================================
				$pg_type="F";
				break;
		}
		$pg_type=trim($pg_type);

		$pmethod=$paymethod.$pg_type;

		//$this->order['pmethod'] = $pmethod;
		$arr = array( 'pmethod'=>$pmethod, 'pg_type'=>$pg_type, 'pgid_info'=>$pgid_info );
		$this->push_info( $arr );

	}

	#상품쿠폰 insert
	protected function insert_prcoupon_order( $op_idx, $ordercode, $couponArr )
	{
		$err = 0;

		$oc_sql = "INSERT INTO tblcoupon_order ( ordercode, coupon_code, productcode, date, ci_no, op_idx, dc_price ";
		$oc_sql.= ") VALUES ( '".$ordercode."', '".$couponArr['coupon_code']."', '".$couponArr['productcode']."', ";
		$oc_sql.= "'".date('YmdHis')."', '".$couponArr['ci_no']."', '".$op_idx."', '".$couponArr['dc']."' )";
        backup_save_sql( $sql );
		pmysql_query( $oc_sql, get_db_conn() );
		if( pmysql_errno() ){
			$err++;
		}
		return $err;
	}
	#장바구니쿠폰 insert
	protected function insert_bcoupon_order( $op_idx, $ordercode, $basketidx )
	{
		$err = 0;

		foreach( $this->basket_coupon as $bcKey=>$bcVal ){
			foreach( $bcVal as $cKey=>$cVal ){
				if( $cVal['basketidx'] == $basketidx ){
					$oc_sql = "INSERT INTO tblcoupon_order ( ordercode, coupon_code, productcode, date, ci_no, op_idx, dc_price ";
					$oc_sql.= ") VALUES ( '".$ordercode."', '".$cVal['coupon_code']."', '".$cVal['productcode']."', ";
					$oc_sql.= "'".date('YmdHis')."', '".$cVal['ci_no']."', '".$op_idx."', '".$cVal['dc']."' )";
                    backup_save_sql( $sql );
					pmysql_query( $oc_sql, get_db_conn() );
					if( pmysql_errno() ){
						$err++;
					}
				}
			}
		}

		return $err;

	}

	#개별배송비 insert
	protected function insert_product_delivery( $vender, $productcode, $deli_type='' )
	{
		//매장픽업은 배송비가 추가되지않으므로 튕겨낸다.
		if($deli_type=="1") return 0;

		$ordercode = $this->order['ordercode'];
		$product_deli = $this->product_deli[$vender][$productcode];
        unset( $this->product_deli[$vender][$productcode] );
		$product_deliprice = $product_deli['deli_price'];
		if( $product_deliprice > 0 ){
			//개별 배송비를 배송비 테이블에 넣는다
			$sql = "INSERT INTO tblorder_delivery ( vender, ordercode, product, deli_price, date ) ";
			$sql.= "VALUES ( '".$vender."', '".$ordercode."', '".$productcode."', '".$product_deliprice."', '".date("YmdHis")."' ) ";
            backup_save_sql( $sql );
			pmysql_query( $sql, get_db_conn() );
		} else {
			$product_deliprice = 0;
		}
		return $product_deliprice;
	}

	#벤더별 배송비 insert
	protected function insert_vender_delivery( $vender, $areap='0' )
	{
		$ordercode = $this->order['ordercode'];
		$vender_deli = $this->vender_deli[$vender];
		unset( $this->vender_deli[$vender] );
		$vender_price = $vender_deli['deli_price']+$areap;
		$vender_product = implode( ',', $vender_deli['productcode'] );
		if( $vender_price > 0 && strlen( $vender_product ) > 0 ){
			//벤더별 배송비 테이블에 넣는다
			$sql = "INSERT INTO tblorder_delivery ( vender, ordercode, product, deli_price, date ) ";
			$sql.= "VALUES ( '".$vender."', '".$ordercode."', '".$vender_product."', '".$vender_price."', '".date("YmdHis")."' ) ";
            backup_save_sql( $sql );
			pmysql_query( $sql, get_db_conn() );
		}

		return $vender_price;
	}

	#주문상품 temptable insert
	protected function insert_orderproducttemp()
	{
		global $_ShopInfo;

		//o2o상품을 제외하고 재고 체크하여 상품이 본사에있는 갯수만큼만 본사코드를 넣어주고 나머지는 null
		//if(!$order_product[$i]['delivery_type']){
		$this->market_stock_check($this->sync_bon_code);
		//}


		$order_product = $this->order_object;
		$order = $this->order;
		$count = count( $this->order_object );
		$error = 0;
		$oprDcPrice = 0;
		$deli_price = 0;
		$vender_price = 0;
		$reserve = 0;
		$true_price = 0;
		$tmp_option_price = 0;
	
		

		BeginTrans();
		for( $i=0; $i < $count; $i++ ){

            $deli_ori_price = 0;
			$deli_price     = 0;
			$deli_area		= 0;
            $deli_select = $this->vender_info[$order_product[$i]['vender']]['deli_select'];

			//상품 개별배송비를 넣어준다
			$deli_price = $this->insert_product_delivery( $order_product[$i]['vender'], $order_product[$i]['productcode'], $order_product[$i]['delivery_type'] );
			//벤더별 배송비를 넣어준다
			if($i==0){
				$deli_area=$this->total_price_set['deli_price_area'];
			}

            if( $deli_price == 0 ) { // 개별 배송비가 없으면
                $vender_price = $this->insert_vender_delivery( $order_product[$i]['vender'], $deli_area );
            }
			//지역별배송비는 1회 추가된다. 
			/*
			if($i==0){
				$deli_area=$this->total_price_set['deli_price_area'];
			}
			$deli_price = $deli_price + $vender_price + $deli_area;
			*/
			$deli_price = $deli_price + $vender_price;

            #배송비가 착불일 경우
            if( $deli_select == '1' && $this->deli_free === false ){
                $deli_ori_price = $deli_price;
                $deli_price = 0;
            }
            # 배송비가 무료일 경우
            if( $this->deli_free === true ) {
                $deli_ori_price = $deli_price;
                $deli_price = 0;
                $deli_select = 2;
            }

			$true_price =  ( ( $order_product[$i]['price'] + $order_product[$i]['option_price'] ) * $order_product[$i]['quantity'] ) - $order_product[$i]['dc_price'] - $order_product[$i]['use_reserve'] - $order_product[$i]['use_point'];
            // 임직원 구매 적립금 분기처리 Y 일 경우 0원으로 처리
//            if( $this->staff_order == 'N' && $this->cooper_order == 'N' ){ 20170901
            if( $this->staff_order == 'N'){
				//브랜드별 적립금 퍼센트 처리
				$brand_rate_point=$this->brand_rate_reserve($order_product[$i]['productcode']);
				
    			//$reserve = getReserveConversion( $order_product[$i]['reserve'], $order_product[$i]['reservetype'], $true_price, "N" ); //$order_product[$i]['reserve']
				$reserve = getReserveConversion( $brand_rate_point, "Y", $true_price, "N" ); //$order_product[$i]['reserve']
            } else {
                $reserve = 0;
            }
			$tmp_option_price = 0;
			if( count( $order_product[$i]['option'] ) > 0 ) {
                if( $order_product[$i]['option_type'] == 0 ){ // 조합형일경우의 옵션
                    $temp_option = $order_product[$i]['option'][0];
                    if( $temp_option ){
                        $addCol = ',option_price ,opt2_name ,option_quantity, option_price_text, self_goods_code, ori_option_price ';
                        $addVal = ",'".$temp_option['option_price']."' ,'".$temp_option['option_code']."' , '".$order_product[$i]['quantity']."' ";
                        $addVal.= ",'".$temp_option['option_price']."', '".$temp_option['self_goods_code']."', '".$temp_option['ori_option_price']."' ";
                    } else {
                        $addCol = ',option_quantity ';
                        $addVal = ", '".$order_product[$i]['quantity']."' ";
                    }
					$order_product[$i]['size']=$temp_option['option_code'];	//2016-10-07 libe90 사이즈변수할당
					$tmp_option_price = $temp_option['option_price'];
                } else { // 독립형일경우의 옵션
                    $temp_option = $order_product[$i]['option'];
                    $temp_opt_val = '';
                    $temp_opt_price = 0;
					$temp_ori_opt_price = 0;
                    $temp_opt_price_text = '';
                    $temp_cnt = count( $temp_option );
					$temp_self_code = '';
                    $temp_opt_code = '';
                    if( $temp_cnt > 0 ){
                        foreach( $temp_option as $tempKey=>$tempVal ) { // 독립형일경우 옵션을 여러개 가지고 있다.
                            $temp_opt = '';
                            if( $tempVal['option_code'] != '' ) $temp_opt = explode( chr(30), $tempVal['option_code'] );
                            if( $temp_cnt -1 == $tempKey ) $temp_opt_code.= $temp_opt[1];
                            else $temp_opt_code.= $temp_opt[1].chr(30);

                            if( $tempVal['option_price'] == '' ) $tempVal['option_price'] = 0;
                            $temp_opt_price += $tempVal['option_price'];

                            if( $temp_opt_price_text == '' ) $temp_opt_price_text.= $tempVal['option_price'];
                            else $temp_opt_price_text.= '||'.$tempVal['option_price'];

							if( $temp_self_code == '' ) $temp_self_code.= $tempVal['self_goods_code'];
                            else $temp_self_code.= '@#'.$tempVal['self_goods_code'];

							if( $tempVal['ori_option_price'] == '' ) $tempVal['ori_option_price'] = 0;
                            $temp_ori_option_price += $tempVal['ori_option_price'];

                        }
                        $addCol = ',option_price ,opt2_name ,option_quantity ,option_price_text, self_goods_code, ori_option_price ';
                        $addVal = ",'".$temp_opt_price."' ,'".$temp_opt_code."' , '".$order_product[$i]['quantity']."' ";
                        $addVal.= ",'".$temp_opt_price_text."', '".$temp_self_code."', '".$temp_ori_option_price."' ";
                    } else {
                        $addCol = ', option_quantity ';
                        $addVal = ", '".$order_product[$i]['quantity']."' ";
                    }
					$tmp_option_price = $temp_opt_price;
                }
            } else {
                $addCol = ', option_quantity, self_goods_code '; // self_goods_code 추가 (20160610_김재수 추가)
                $addVal = ", '".$order_product[$i]['quantity']."', '".$order_product[$i]['self_goods_code']."' "; // self_goods_code 추가 (20160610_김재수 추가)
            }
			
			//임직원 및 협력사 할인금액 설정
			$staff_price="0";
			$cooper_price="0";
			$timesale_price="0";
			if( $this->staff_order == 'Y' ){
				$staff_price=($order_product[$i]['ori_price']-$order_product[$i]['price'])*$order_product[$i]['quantity'];
    			$addCol.= ', staff_price ';
                $addVal.= ", '".$staff_price."' ";
				$this->total_price_set['staff_price']+=$staff_price;
            } else if($this->cooper_order == 'Y') {
					$c_productcode = $order_product[$i]['productcode'];
					list($consumerprice) = pmysql_fetch("select consumerprice from tblproduct where productcode= '".$c_productcode."' ");
					$cooper_price=($consumerprice-$order_product[$i]['price'])*$order_product[$i]['quantity'];
					$addCol.= ', cooper_price ';
					$addVal.= ", '".$cooper_price."' ";
					$this->total_price_set['cooper_price']+=$cooper_price;
/*
					if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){}
					$cooper_price=($order_product[$i]['ori_price']-$order_product[$i]['price'])*$order_product[$i]['quantity'];
					$addCol.= ', cooper_price ';
					$addVal.= ", '".$cooper_price."' ";
					$this->total_price_set['cooper_price']+=$cooper_price;
*/
            } else if($order_product[$i]['ori_price']!=$order_product[$i]['price']) {
				$timesale_price=($order_product[$i]['ori_price']-$order_product[$i]['price'])*$order_product[$i]['quantity'];
				$addCol.= ', timesale_price ';
                $addVal.= ", '".$timesale_price."' ";
				$addCol.= ', timesale_detail ';
                $addVal.= ", '".$order_product[$i]['timesale_detail']."' ";
				$this->total_price_set['timesale_price']+=$timesale_price;
			}

			$o2otype="O";
			if(!$order_product[$i]['delivery_type']){
				$order_product[$i]['delivery_type'] = "0";
				$o2otype="";

				if(strlen($order['ordercode'])=="20"){$o2otype="A";}
				else {$o2otype="";}

			}

			if($order_product[$i]['store_code']=="undefined"){
				$store_code="";
			}else{
				$store_code=$order_product[$i]['store_code'];
			}

			//택배발송시 본사로 보내짐. 2017-03-21
/*
			if ($order_product[$i]['prodcode'] && $order_product[$i]['colorcode'] && $order_product[$i]['delivery_type']=='0') {	//2016-10-07 libe90 매장발송일경우 재고체크해서 재고 가장 많은 매장으로 매장정보표시
				$shopRealtimeStock = getErpProdShopStock_Type($order_product[$i]['prodcode'], $order_product[$i]['colorcode'], $order_product[$i]['size'], 'delivery');
				$order_product[$i]['store_code'] = $shopRealtimeStock['shopcd'];
			}
			*/
			$o2num=sprintf("%02d", $i+1);;
			$pr_code=$order['ordercode'].$o2otype.'_'.$o2num;
			list($vender_no)=pmysql_fetch("select vender from tblproduct where productcode='".$order_product[$i]['productcode']."'");

			$sql = "INSERT INTO tblorderproducttemp ( ";
			$sql.= "vender, ordercode, tempkey, productcode, ";
			$sql.= "productname, opt1_name, ";
			$sql.= "addcode, quantity, price, ";
			$sql.= "reserve, date, selfcode, ";
			$sql.= "order_prmsg, ";
			$sql.= "option_type, coupon_price, deli_price, ";
			$sql.= "use_point, basketidx, rate, ";
			$sql.= "text_opt_subject, text_opt_content, deli_ori_price, deli_select, ";
			$sql.= "staff_order, delivery_type, reservation_date, store_code, ori_price, use_epoint, pr_code, cooper_order, gps_x, gps_y ";
			$sql.= $addCol;
			$sql.= " ) VALUES ( ";
			$sql.= " '".$vender_no."', '".$order['ordercode']."', '".$_ShopInfo->getTempkey()."', '".$order_product[$i]['productcode']."', ";
			$sql.= " '".pmysql_escape_string( $order_product[$i]['productname'] )."', '".$order_product[$i]['option_subject']."', ";
			$sql.= " '".$order_product[$i]['addcode']."', '".$order_product[$i]['quantity']."', '".$order_product[$i]['price']."', ";
			$sql.= " '".$reserve."', '".date('Ymd')."', '".$order_product[$i]['selfcode']."', "; // $order_product[$i]['reserve'] -> $reserve
			$sql.= " '".$order['order_msg2']."', ";
			$sql.= " '".$order_product[$i]['option_type']."', '".$order_product[$i]['dc_price']."', '".$deli_price."', "; //".$order_product[$i]['deli_price']."
			$sql.= " '".$order_product[$i]['use_reserve']."', '".$order_product[$i]['basketidx']."', '".$order_product[$i]['rate']."', ";
			$sql.= " '".$order_product[$i]['text_opt_subject']."', '".$order_product[$i]['text_opt_content']."', '".$deli_ori_price."', '".$deli_select."', ";
			$sql.= " '".$this->staff_order."', '".$order_product[$i]['delivery_type']."', '".$order_product[$i]['reservation_date']."', '".$store_code."', '".$order_product[$i]['ori_price']."', '".$order_product[$i]['use_point']."', '".$pr_code."', '".$this->cooper_order."', '".$order_product[$i]['gps_x']."', '".$order_product[$i]['gps_y']."' ";
			$sql.= $addVal;
			$sql.= " ) RETURNING idx ";
			backup_save_sql( $sql );
			$result = pmysql_query( $sql, get_db_conn() );
			//exdebug($sql);
			if( $row = pmysql_fetch_object( $result ) ){
				//쿠폰이 있을시 쿠폰정보를 넣어준다
				if( $this->product_coupon[$order_product[$i]['basketidx']] ){
					$error += $this->insert_prcoupon_order( $row->idx, $order['ordercode'], $this->product_coupon[$order_product[$i]['basketidx']] );
				}
				//장바구니 쿠폰이 있을경우
				if( $this->basket_coupon ){
					$error += $this->insert_bcoupon_order( $row->idx, $order['ordercode'], $order_product[$i]['basketidx'] );
				}

				// 배송비가 걸려있는 상품의 정보를 넣는다
				// 적립금으로 배송비를 사용할수없음으로 필요없어짐 주석처리 2017-03-09
				//$error += $this->insert_order_reserve( $order['ordercode'], $row->idx, $i );

			} else {
				$error++;
			}


		}

		if( $error > 0 ){
			RollbackTrans();
			$this->success = false;
		} else {
			 CommitTrans();
		}

	}

	#주문 temptbale insert
	protected function insert_orderinfotemp()
	{
		global $_ShopInfo;

		$order = $this->order;
		$prise_set = $this->total_price_set;
		$ip = $_SERVER['REMOTE_ADDR'];
		$addQry = '';
		$addVal = '';
		$error = 0;
        $is_mobile = 0;
        $deli_ori_price = $this->deli_ori_price;
        $deli_select = 0;
        $deli_price = 0;

		#적립금으로 구매시
		if( $this->last_price <= 0 ) {
			$order['pay_data']  = "총 구매금액 ".number_format( $prise_set['price'] + $prise_set['deli_price'] )."원을 포인트/쿠폰 으로 구매";
            $order['pmethod']   = 'B';
            $this->order['paymethod'] = 'B';

			//$addQry .= " ,bank_date ";
			//$addVal .=  " ,'".date("YmdHis")."' ";
            /*
			if(strstr("OQ", $paymethod)) {
				$addQry .= " ,pay_flag ";	//가상계좌만,,,
				$addVal .=  " ,'0000' ";
			}
            */

		}

        $deli_price = $prise_set['deli_price'];
        //$deli_select = $this->vender_info[$order_product[$i]['vender']]['deli_select'];
        #배송비가 착불일 경우 상품별 확인은 가능하지만 전체는 확인 불가능함
        /*
        if( $deli_select == '1' && $this->deli_free === false ){
            $deli_ori_price = $deli_price;
            $deli_price = 0;
        }
        */
        # 배송비가 무료일 경우
        if( $this->deli_free === true ) {
            $deli_ori_price += $deli_price;
            $deli_price = 0;
            $deli_select = 2;
        }

		if( $prise_set['reserve'] == '' ) $prise_set['reserve'] = 0;

        #구매 경로 체크
        $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
        // 모바일인지 pc인지 체크
        if( preg_match( $mobileBrower, $_SERVER['HTTP_USER_AGENT'] ) ) {
            if( get_session( 'ACCESS' ) == 'app' ) $is_mobile = 2;
            else $is_mobile = 1;
        }

		$sql = "INSERT INTO tblorderinfotemp ( ";
		$sql.= "ordercode, tempkey, id, price, deli_price, ";
		$sql.= "dc_price, paymethod, pay_data, sender_name, sender_email, ";
		$sql.= "sender_tel, sender_tel2, receiver_name, receiver_tel1, receiver_tel2, ";
		$sql.= "receiver_addr, order_msg, ip, del_gbn, reserve, ";
		$sql.= "partner_id, loc, bank_sender, receipt_yn, ";
		$sql.= "order_msg2, overseas_code, post5, is_mobile,  ";
        $sql.= "deli_ori_price, deli_select, staff_order, point, pg_ordercode, cooper_order, staff_price, cooper_price, timesale_price ";
		$sql.= $addQry;
		$sql.= " ) VALUES ( ";
		$sql.= "'".$order['ordercode']."', '".$_ShopInfo->getTempkey()."', '".$order['id']."', '".$prise_set['price']."', '".$prise_set['deli_price']."', ";
		$sql.= "'".$prise_set['dc_price']."', '".$order['pmethod']."', '".$order['pay_data']."', '".$order['sender_name']."', '".$order['sender_email']."', ";
		$sql.= "'".$order['sender_tel']."', '".$order['sender_tel2']."', '".$order['receiver_name']."', '".$order['receiver_tel1']."', '".$order['receiver_tel2']."', ";
		$sql.= "'".$order['receiver_addr']."', '".$order['order_msg']."', '".$ip."', 'N', '".$prise_set['reserve']."', ";
		$sql.= "'".$_ShopInfo->getRefurl()."', '".$order['loc']."', '".$order['bank_sender']."', '".$order['receipt_yn']."', ";
		$sql.= "'".$order['order_msg2']."', '".$order['overseas_code']."', '".$order['post5']."', '".$is_mobile."', ";
        $sql.= "'".$deli_ori_price."', '".$deli_select."', '".$this->staff_order."', '".$prise_set['point']."', '".$order['ordercode']."', '".$this->cooper_order."', '".$prise_set['staff_price']."', '".$prise_set['cooper_price']."', '".$prise_set['timesale_price']."' ";
		$sql.= $addVal;
		$sql.= " ) ";
		BeginTrans();
		pmysql_query( $sql, get_db_conn() );
		backup_save_sql( $sql );
		if ( pmysql_errno() ) {
			RollbackTrans();
			$this->success = false;
		} else {
			CommitTrans();
		}

	}

	#주문자 정보등록
	protected function push_info( $arr )
	{
		$this->order = array_merge( $this->order, $arr );
	}

	#주문정보 세팅
	public function set_orderinfo( $arr )
	{
		$this->push_info( $arr );
		$this->set_orderkey();
		$this->set_pgkey();
	}

	#주문상품 세팅
	public function order_setting( $basketidxs = '' )
	{
		// 선택상품일 경우 키값 변경
		if( strlen( $basketidxs ) > 0 ){
			$this->basket_select_item( $basketidxs );
		}

		if( $this->basket_check() ){ // 상품 유효성 체크
			foreach( $this->basket as $basketKey=>$basketVal ){
				$this->obejct_setting( $basketVal ); // 상품정보 및 가격 세팅
			}
		}
		return $this->success;
	}

	#주문 종료후 임시키 변경
	protected function set_oldkey()
	{
		global $_ShopInfo;
		/*
		$oldtempkey=$_ShopInfo->getTempkey();
		$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
		$_ShopInfo->setGifttempkey($oldtempkey);
		$_ShopInfo->setOldtempkey($oldtempkey);
		$_ShopInfo->setOkpayment("");
		$_ShopInfo->Save();
		*/
		$_ShopInfo->setOkpayment("");
		$_ShopInfo->Save();
	}

	#주문할 상품정보를 받아온다.
	public function get_order_object()
	{
		return $this->order_object;
	}

	#주문자 정보를 받아온다.
	public function get_order()
	{
		return $this->order;
	}

	#전체 가격을 받아온다
	public function get_total_price()
	{
		return $this->total_price_set;
	}

	#최종 가격을 받아온다
	public function get_last_price()
	{
		return $this->last_price;
	}

	#상품쿠폰정보
	public function get_product_coupon()
	{
		return $this->product_coupon;
	}

	#장바구니쿠폰 정보
	public function get_basket_coupon()
	{
		return $this->basket_coupon;
	}

    # 벤더정책을 세팅한다
    public function set_vender_info( $vender_info )
    {
        $this->vender_info = $vender_info;
    }


	#최종 가격을 세팅한다
	public function set_last_price()
	{
		
			$price = $this->total_price_set['price'];
			$deli_price = $this->total_price_set['deli_price'];
			$dc_price = $this->total_price_set['dc_price'];
			$reserve = $this->total_price_set['reserve'];
			$point = $this->total_price_set['point'];
			$this->last_price = $price + $deli_price - $dc_price - $reserve - $point;
		
	}

	#주문 입력
	public function order_send()
	{
		$this->set_last_price();
		$this->set_oldkey();
		//이전에 넘어온 금액과 최종 금액이 맞는지 확인한다.
		if( $this->order['total_sum'] != $this->last_price ) $this->success = false;
		if( $this->success ) $this->insert_orderproducttemp();
		if( $this->success ) $this->insert_orderinfotemp();
		return $this->success;
	}

	#벤더별 배송료 정보를 받아 세팅한다
	public function vender_delivery_set ( $vender_deli_array )
	{
        $this->deli_free = false;
		$this->vender_deli = $vender_deli_array;
		if( count( $vender_deli_array ) > 0 ){
			foreach( $vender_deli_array as $key=>$val ){
                if( $this->vender_info[$key]['deli_select'] == '0' ){
    				$this->total_price_set['deli_price'] += $val['deli_price'];
                } else {
                    $this->deli_ori_price += $val['deli_price'];
                }
			}
		}
		$this->set_last_price();
	}
	#상품별 배송료 정보를 받아 세팅한다
	public function product_delivery_set ( $product_deli_array )
	{
        $this->deli_free = false;
		$this->product_deli = $product_deli_array;
		if( count( $product_deli_array ) > 0 ){
			foreach( $product_deli_array as $vender=>$productVal ){
				foreach( $productVal as $product=>$val ){
                    if( $this->vender_info[$vender]['deli_select'] == '0' ){
    					$this->total_price_set['deli_price'] += $val['deli_price'];
                    } else {
                        $this->deli_ori_price += $val['deli_price'];
                    }
				}
			}
		}
		$this->set_last_price();
	}

    #배송비 무료세팅
    public function set_free_deli()
    {
        $this->deli_ori_price += $this->total_price_set['deli_price'];
        $this->total_price_set['deli_price'] = 0;
        $this->deli_free = true;
        $this->set_last_price();
    }

	#상품쿠폰 정보를 받아 세팅한다
	public function product_coupon_set( $couponData )
	{
		$couponArr = array();
		$price = 0;

		foreach( $this->order_object as $orderKey=>$orderVal ){
			$opt_cnt   = count( $orderVal['option'] );
			$opt_price = 0;
			if( $opt_cnt > 0 ){
				for( $opt_i = 0; $opt_i < $opt_cnt; $opt_i++ ){
					$opt_price += $orderVal['option'][$opt_i]['option_price'];
				}
			}
			if( $orderVal['basketidx'] == $couponData['basketidx'] ){
				$productPrice = ( $orderVal['price'] + $opt_price ) * $orderVal['option_quantity'];
				$couponPrice = CouponDiscount( $productPrice, $couponData['ci_no'] );

				$this->product_coupon[$couponData['basketidx']] = array(
					'basketidx'   => $couponData['basketidx'],
					'productcode' => $orderVal['productcode'],
					'ci_no'       => $couponPrice['ci_no'],
					'coupon_code' => $couponPrice['coupon_code'],
					'price'       => $couponPrice['sellprice'],
					'dc'          => $couponPrice['dc'],
					'type'        => $couponPrice['type'],
					'coupon_type' => $couponPrice['coupon_type']
				);

				$this->order_object[$orderKey]['ci_no']       = $couponPrice['ci_no'];
				$this->order_object[$orderKey]['coupon_code'] = $couponPrice['coupon_code'];
				$this->order_object[$orderKey]['dc_price']    = $couponPrice['dc'];

				$price += $couponPrice['dc'];
			}
		}

		$this->total_price_set['dc_price'] += $price;

		$this->set_last_price();

	}
	#장바구니 쿠폰을 받아 세팅한다
	public function basket_coupon_set( $ci_no )
	{
		$priceArr = array();
		$couponArr = array();
		$total_priec = 0;
		foreach( $this->order_object as $orderKey=>$orderVal ){
			$opt_cnt   = count( $orderVal['option'] );
			$opt_price = 0;
			if( $opt_cnt > 0 ){
				for( $opt_i = 0; $opt_i < $opt_cnt; $opt_i++ ){
					$opt_price += $orderVal['option'][$opt_i]['option_price'];
				}
			}
			$selectPrice = ( ( $orderVal['price'] + $opt_price ) * $orderVal['option_quantity'] ) - $orderVal['dc_price'];
			$couponArr[$orderKey]['basketidx']   = $orderVal['basketidx'];
			$couponArr[$orderKey]['price']       = $selectPrice;
			$couponArr[$orderKey]['ci_no']       = $ci_no;
			$couponArr[$orderKey]['productcode'] = $orderVal['productcode'];
			$priceArr[$orderKey]                 = $selectPrice;
			$total_priec                        += $selectPrice;
		}

		$couponPrice = CouponDiscount( $total_priec, $ci_no );

		$bk_dc = allot( $couponPrice['dc'], $priceArr );
		foreach( $this->order_object as $orderKey=>$orderVal ){
			$this->order_object[$orderKey]['dc_price'] += $bk_dc[$orderKey];
			$couponArr[$orderKey]['dc']                 = $bk_dc[$orderKey];
			$couponArr[$orderKey]['type']               = $couponPrice['type'];
			$couponArr[$orderKey]['coupon_code']        = $couponPrice['coupon_code'];
			$couponArr[$orderKey]['coupon_type']        = $couponPrice['coupon_type'];
		}

		$this->basket_coupon[] = $couponArr;

		$this->total_price_set['dc_price'] += $couponPrice['dc'];

		$this->set_last_price();
	}


	#적립금을 받아 세팅한다
	public function reserve_set( $reserve, $point="0", $deli_area="0" )
	{
		$this->total_price_set['reserve'] = $reserve;
		$this->total_price_set['point'] = $point;
		$in_vender = array();
		$in_product = array();
		$priceArr  = array();
		foreach( $this->order_object as $orderKey=>$orderVal ){
			$opt_cnt   = count( $orderVal['option'] );
			$opt_price = 0;
			if( $opt_cnt > 0 ){
				for( $opt_i = 0; $opt_i < $opt_cnt; $opt_i++ ){
					$opt_price += $orderVal['option'][$opt_i]['option_price'];
				}
			}

			$deli_price = 0;
			if( $this->product_deli[$orderVal['vender']][$orderVal['productcode']] && array_search( $orderVal['vender'], $in_product ) === false ){
				$in_product[] = $orderVal['productcode'];
				$deli_price = $this->product_deli[$orderVal['vender']][$orderVal['productcode']]['deli_price'];
			} else if( $this->vender_deli[$orderVal['vender']] && array_search( $orderVal['vender'], $in_vender ) === false ){
				$in_vender[] = $orderVal['vender'];
				$deli_price = $this->vender_deli[$orderVal['vender']]['deli_price']+$deli_area;
			}

			//$priceArr[$orderKey]  = ( ( $orderVal['price'] + $opt_price ) * $orderVal['option_quantity']) + $deli_price - $orderVal['dc_price'];
			$priceArr[$orderKey] = ( ( $orderVal['price'] + $opt_price ) * $orderVal['option_quantity']) - $orderVal['dc_price'];

			//배송비 적립금 사용불가로 배송비 0처리.
			//$deliArr[$orderKey]   = $deli_price;
			$deliArr[$orderKey]   = 0;

		}

		//$pr_reserve      = reserve_allot( $reserve, $priceArr );
		$rate_reserve    = rate_allot( $reserve, $priceArr, $deliArr, $point );
		foreach( $this->order_object as $orderKey=>$orderVal ){
			//$this->order_object[$orderKey]['use_reserve'] = $pr_reserve[$orderKey];
			$this->order_object[$orderKey]['use_reserve']   = $rate_reserve['op_point'][$orderKey];
			$this->order_object[$orderKey]['use_point']   = $rate_reserve['op_epoint'][$orderKey];
			/*
			$this->order_reserve[$orderKey] = array(
				'op_price'      => $rate_reserve['op_price'][$orderKey],
				'op_reserve'    => $rate_reserve['op_reserve'][$orderKey],
				'op_rate'       => $rate_reserve['op_rate'][$orderKey],
				'deli_price'    => $rate_reserve['deli_price'][$orderKey],
				'deli_reserve'  => $rate_reserve['deli_reserve'][$orderKey],
				'deli_rate'     => $rate_reserve['deli_rate'][$orderKey]
			);
			*/
		}

		$this->set_last_price();
	}

	#상품 외 X건의 이름을 가져온다
	public function get_goodname()
	{
		$goodname = '';
		$cnt = 0;
		foreach( $this->order_object as $key=>$val ){
			if( $goodname == '' ) $goodname = $val['productname'];
			else $cnt++;
		}
		if( $cnt > 0 ) $goodname .= ' 외 '.$cnt.' 건';
		return $goodname;
	}

	#상품코드기준으로 장바구니 정보를 가져옴
	public function convert_productcode ( $productcodes )
	{
		//상품코드를 가져오는 기준은 장바구니와 동일
		$strWhere = $this->split_basketidxs( $productcodes );
		$basketidxs = '';

        if ( strlen($_ShopInfo->getMemid()) > 0 ) {
            // 로그인
            $whereQuery = "id = '" . $_ShopInfo->getMemid() . "' ";
        } else {
            // 비로그인
            $whereQuery = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
        }

		$sql = "SELECT basketidx FROM tblbasket WHERE {$whereQuery} AND productcode IN ('".$strWhere."') ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$basketidxs .= $row->basketidx.'|';
		}

		$this->basket_tempkey_change( substr( $basketidxs, 0, -1 ) );

	}

	# 임직원 구매 type 세팅
	public function set_staff_order ( $type )
	{
		$this->staff_order = $type;
	}

	# 협력사 구매 type 세팅
	public function set_cooper_order ( $type )
	{
		$this->cooper_order = $type;
	}


	# 주문시 사용된 마일리지 정보
	public function insert_order_reserve( $ordercode, $op_idx, $key )
	{
		$err = 0;
		if( count( $this->order_reserve[$key] ) == 0 ) return 1;
		$order_reserve = $this->order_reserve[$key];
		$op_price      = $order_reserve['op_price'];
		$op_reserve    = $order_reserve['op_reserve'];
		$op_rate       = $order_reserve['op_rate'];
		$deli_price    = $order_reserve['deli_price'];
		$deli_reserve  = $order_reserve['deli_reserve'];
		$deli_rate     = $order_reserve['deli_rate'];

		$sql = "INSERT INTO tblorder_reserve ( ordercode, op_idx, op_price, op_reserve, op_rate, deli_price, deli_reserve, deli_rate, date ) ";
		$sql.= "VALUES ( '".$ordercode."', ".$op_idx.", ".$op_price.", ".$op_reserve.", ".$op_rate.", ";
		$sql.= "'".$deli_price."', ".$deli_reserve.", ".$deli_rate.", '".date("YmdHis")."' ) ";
		$sql.= "RETURNING idx ";
		$result = pmysql_query( $sql, get_db_conn() );
		backup_save_sql( $sql );
		if( $row = pmysql_fetch_object( $result ) ){
			if( $deli_price > 0 ) {
				$sql2 = "INSERT INTO tblorder_reserve_log ( idx, ordercode, op_idx, op_price, op_reserve, op_rate, deli_price, deli_reserve, deli_rate, date ) ";
				$sql2.= "VALUES ( ".$row->idx.", '".$ordercode."', ".$op_idx.", ".$op_price.", ".$op_reserve.", ".$op_rate.", ";
				$sql2.= "'".$deli_price."', ".$deli_reserve.", ".$deli_rate.", '".date("YmdHis")."' ) ";
				pmysql_query( $sql2, get_db_conn() );
				if( pmysql_error() ) $err = pmysql_errno();
			}
		} else {
			$err++;
		}


		return  $err;
	}


	# 주문시 사용된 마일리지 정보
	public function delivery_area_set( $deli_area )
	{
		$this->total_price_set['deli_price'] += $deli_area;
		$this->total_price_set['deli_price_area'] += $deli_area;
		$this->set_last_price();
	}

	//재고체크
	public function market_stock_check($maket_code)
	{
		foreach( $this->order_object as $key=>$val ){
			$maket_stock=getErpProdShopStock($val[prodcode], $val[colorcode], $val[option][0][option_code], $this->sync_bon_code);
			$pr_stock[$val[productcode]][$val[option][0][option_code]]=$maket_stock[sumqty];
					
		}
	

		foreach( $this->order_object as $key=>$val ){
			if ($val['prodcode'] && $val['colorcode'] && $val['delivery_type']=='0') {
				if($pr_stock[$val[productcode]][$val[option][0][option_code]]>0){
					$this->order_object[$key]["store_code"]=$maket_code;
				}
				$pr_stock[$val[productcode]][$val[option][0][option_code]]--;
			}
			
		}
	}

	//브랜드별 적립예정마일리지 계산
	public function brand_rate_reserve($productcode)
	{
		list($sellprice_rate, $brand) = pmysql_fetch("SELECT sellprice_dc_rate, brand FROM tblproduct where productcode= '".$productcode."' ");
		if(!$sellprice_rate) $sellprice_rate ="0";
		list($brand_rate_point)=pmysql_fetch("select ins_per from tblproductbrand_point where bridx='".$brand."' and st_per <= '".$sellprice_rate."' and en_per >= '".$sellprice_rate."' order by point_date desc limit 1");

		return $brand_rate_point;
	}

}

?>
