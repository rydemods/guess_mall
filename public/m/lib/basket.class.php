<?php

/********************************************************************* 
// 파 일 명 : basket.class.php 
// 설    명 : 장바구니 등록 / 수정 / 삭제 / 출력 
// 상세설명 : 장바구니에 상품을 등록/수정/삭제/출력 및 판매가능여부 체크
// 작 성 자 : 유동혁 2016.01.11
// 수 정 자 : 
// 
// 
*********************************************************************/ 

CLASS Basket {

	public $basket; // 장바구니 상품 정보
	protected $success = true; //장바구니 INSERT / UPDATE 성공가능
	public $return_code = array(
		'S01'=>'장바구니에 등록되었습니다.', //성공
		'S02'=>'장바구니에 등록되었습니다.', //기존 장바구니를 변경
		'S03'=>'장바구니에 상품을 삭제했습니다.',

		'E01'=>'장바구니에 등록이 실패되었습니다.', //실패
		'E02'=>'상품이 존재하지 않습니다.', //상품이 존재하지 않음
		'E03'=>'옵션이 존재하지 않습니다.', //옵션이 존재하지 않음
		'E04'=>'등록이 실패하였습니다.', //쿼리 오류
		'E05'=>'상품이 존재하지 않습니다.', //상품 카테고리가 존재하지 않음
		'E06'=>'상품이 존재하지 않습니다.', //카테고리 상품 숨김
		'E07'=>'상품이 존재하지 않습니다.', //특정회원만 이용가능한 카테고리
		'E08'=>'상품이 존재하지 않습니다.', //특정 그룹만 이용가능한 카테고리
		'E09'=>'삭제가 실패하였습니다.',
		'E10'=>'중복된 상품이 존재합니다.',

		'F01'=>'상품 수량이 부족합니다.', //수량부족
		'F02'=>'상품 수량이 부족합니다.', //최소 수량부족
		'F03'=>'상품 수량이 많습니다.', //최대수량부족
		'F04'=>'품절된 상품입니다.', //상품 품절
		'F05'=>'상품 수량이 부족합니다.', //상품 수량부족
		'F06'=>'품절된 옵션입니다.', //옵션 품절
		'F07'=>'상품 수량이 부족합니다.' //옵션 수량부족
	);
	public $this_code = '00';
	public $return_idx = '';

	function Basket()
	{
		global $_ShopInfo;

		//장바구니 인증키 확인
		if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
			$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
		}

		if(strlen($_ShopInfo->getTempkeySelectItem())==0 || $_ShopInfo->getTempkeySelectItem()=="deleted") {
			$_ShopInfo->setTempkeySelectItem($_data->ETCTYPE["BASKETTIME"]);
		}
		#주문이 시도되었던 상품을 다시 불러와줌
		//$this->revert_item();

		$this->get_item();
	}
	# 인증키가 다를경우가 발생 - 아이디가 같을경우 인증키 업데이트한다.(2015.11.18 - 김재수)
	public function revert_id_item()
	{
		global $_ShopInfo;
		if( $_ShopInfo->getMemid() ){
			$cok_sql		= "SELECT tempkey FROM tblbasket WHERE id = '".$_ShopInfo->getMemid()."' ORDER BY date DESC LIMIT 1";
			$cok_result		= pmysql_query($cok_sql,get_db_conn());
			$countOldkey = pmysql_fetch_object($cok_result);
			if($countOldkey->tempkey != $_ShopInfo->getTempkey()){
				$upNewQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE id = '".$_ShopInfo->getMemid()."' ";
				pmysql_query( $upNewQuery, get_db_conn() );
			}
		}
	}

	#주문이 시도되었던 상품을 다시 불러와줌
	public function revert_item()
	{
        /*
		global $_ShopInfo;
		$sql = "UPDATE tblbasket SET tempkey='".$_ShopInfo->getTempkey()."' WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'";
		pmysql_query( $sql, get_db_conn() );
		$this->get_item();
        */
	}

	#장바구니 정보를 불러옴
	public function get_item($basketidxs = null)
	{
		global $_ShopInfo;
		unset( $this->basket );

        $arrBasketIdxs = array();
        if ( $basketidxs != null ) {
            $arrBasketIdxs = explode("|", $basketidxs);
        }

        $whereQueryId = "";
        $whereQuery = "";

		# 매장 픽업 / 당일 수령 장바구니 상품 삭제
		delDeliveryTypeData();

		if ( strlen($_ShopInfo->getMemid()) > 0 ) {
			// 로그인
			$whereQueryId = "bsk.id = '" . $_ShopInfo->getMemid() . "' ";
		} else {
			// 비로그인
			$whereQueryId = "bsk.tempkey='".$_ShopInfo->getTempkey()."' AND bsk.id = '' ";
		}

        if ( count($arrBasketIdxs) == 0 ) { // 전체 선택
            $whereQuery = $whereQueryId;
        } else {// 개별 선택
            $whereQuery = $whereQueryId."AND bsk.basketidx in ( " . implode(",", $arrBasketIdxs) . " ) ";
        }
		/*
		$sql = "SELECT tempkey, productcode, quantity, basketidx, id, optionarr, quantityarr, pricearr, opt1_idx, opt2_idx, op_type, ";
		$sql.= "text_opt_subject, text_opt_content ";
		$sql.= "FROM tblbasket WHERE {$whereQuery} ORDER BY date DESC, basketidx ASC ";
*/
		# 2016-09-21 핫딜용 상품 노출 안시키기위한 장바구니 수정
		# 장바구니에 들어가있는 힛딜 상품들제외
		$sql.= "SELECT 
						bsk.tempkey, bsk.productcode, bsk.quantity, bsk.basketidx, bsk.id, bsk.optionarr, bsk.quantityarr, 
						bsk.pricearr, bsk.opt1_idx, bsk.opt2_idx, bsk.op_type, bsk.text_opt_subject, bsk.text_opt_content, bsk.delivery_price, 
						bsk.delivery_type, bsk.reservation_date, bsk.store_code, bsk.post_code, bsk.address1, bsk.address2, tpt.prodcode, tpt.colorcode
						,case when delivery_type='0' then '0' else '1' end as reserv_gb
					FROM 
						tblbasket bsk 
						JOIN tblproduct tpt ON bsk.productcode = tpt.productcode 
					WHERE 
						bsk.basketidx not in (
							SELECT 
								a.basketidx 
							FROM 
								tblbasket a 
								LEFT JOIN tblproduct b on(a.productcode=b.productcode) 
							WHERE 
								b.hotdealyn='Y' and {$whereQuery} group by a.basketidx
						) 
						AND {$whereQuery} ORDER BY reserv_gb, bsk.date DESC, bsk.basketidx ASC";
						
		//echo $sql;

		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			// 같은상품이 있는지 체크한다.
			$whereQueryId = str_replace("bsk.", "", $whereQueryId);
			$bk_same_sql = "DELETE FROM tblbasket WHERE {$whereQueryId} 
				AND productcode='".$row->productcode."' 
				AND optionarr='".$row->optionarr."' 
				AND opt1_idx='".$row->opt1_idx."' 
				AND opt2_idx='".$row->opt2_idx."' 
				AND text_opt_subject='".$row->text_opt_subject."' 
				AND text_opt_content='".$row->text_opt_content."' 
				AND reservation_date='".$row->reservation_date."' 
				AND store_code='".$row->store_code."' 
				AND delivery_type='".$row->delivery_type."' 
				AND post_code='".$row->post_code."' 
				AND address1='".$row->address1."' 
				AND address2='".$row->address2."' 
				AND basketidx !='".$row->basketidx."' ";
			pmysql_query( $bk_same_sql, get_db_conn() );
			pmysql_free_result( $bk_same_sql );

			$this->basket[] = $row;
		}
        pmysql_free_result;
		
	}

	#장바구니에 상품을 넣음
	public function set_item( $productcode, $quantity, $option_code = '' , $option_quantity = 0, $option_type = 0, $text_opt_subject = '', $text_opt_content = '', $basketArray = array() )
	{
		global $_ShopInfo;
		$addQueryCol = ''; // 추가 컬럼
		$addQueryVal = ''; // 추가 내용
		$basketidx = ''; //중복장바구니 idx
		$vdate = date("YmdHis");

		#상품 그룹을 체크함
		$this->check_product_group( $productcode );
		
		#해당 상품정보를 가져옴
		$select_product = $this->select_product( $productcode );
		if( !$select_product ) {
			$this->success = false; //실제 상품이 존재하지 않음
			$this->this_code = 'E02';
		} else {
			#상품 조건부 수량을 체크함
			$this->check_product( $select_product, $quantity );
			#해당 상품 수량 체크
			$this->check_product_quantity( $select_product, $quantity );
		}
		
		#회원일 경우
		if( strlen( $_ShopInfo->getMemid() ) > 0 ){
			$addQueryCol .= ' ,id ';
			$addQueryVal .= " ,'".$_ShopInfo->getMemid()."' ";
		}

		#옵션이 존재할 경우
		if( $option_code != '' ){
			
			#해당 옵션정보를 가져옴
			if( $option_type == 1 ){  //독립형 옵션일 경우
				$tmp_option_code = explode( '@#', $option_code );
				$tmp_select_option = array();
				$tmp_option_price = 0;
				foreach( $tmp_option_code as $tmpOptionCode ){
					if( $tmpOptionCode != '' ){
						$tmp_select_option = $this->select_options( $productcode, $tmpOptionCode, $option_type );
						if( !$tmp_select_option && $this->success ){
							$this->success = false; //실제 옵션이 존재하지 않음
							$this->this_code = 'E03';
							break;
						} else {
							$tmp_option_price += $tmp_select_option[0]->option_price;
							if( $select_product->quantity < 999999999 ){
								$this->check_option_quantity( $tmp_select_option[0], $option_quantity );
							}
						}
					}
				}
				if( $this->success ){
					$opt1_idx = $select_product->option1;
					$opt2_idx = $option_code;

					//추가 컬럼 add되는 내용이기 , 를 앞에찍음
					$addQueryCol .= ' ,optionarr ';
					$addQueryCol .= ' ,quantityarr ';
					$addQueryCol .= ' ,pricearr ';
					$addQueryCol .= ' ,opt1_idx ';
					$addQueryCol .= ' ,opt2_idx ';
					$addQueryCol .= ' ,op_type ';
					//추가 내용도 add되는 내용이기 , 를 앞에찍음 앞에 컬럼과 순서가 같아야함
					$addQueryVal .= " ,'".$option_code."' ";
					$addQueryVal .= " ,'".$option_quantity."' ";
					$addQueryVal .= " ,'".$tmp_option_price."' ";
					$addQueryVal .= " ,'".$opt1_idx."' ";
					$addQueryVal .= " ,'".$opt2_idx."' ";
					$addQueryVal .= " ,'".$option_type."' ";
				}
			} else { // 조합형 옵션일 경우
				$select_option = $this->select_options( $productcode, $option_code, $option_type );
				
				if( !$select_option ) {
					$this->success = false; //실제 옵션이 존재하지 않음
					$this->this_code = 'E03';
				} else {
					#해당 옵션 수량 체크
					if( $select_product->quantity < 999999999 ){
						$this->check_option_quantity( $select_option[0], $option_quantity );
					}
					//옵션 1, 옵션2를 나누어줌
					/*
					$tmp_option = explode( chr(30), $option_code );
					$opt1_idx = '';
					$opt2_idx = '';
					if( $tmp_option[0] ) $opt1_idx = $tmp_option[0];
					if( $tmp_option[1] ) $opt2_idx = $tmp_option[1];
					*/
					 $opt1_idx = $select_product->option1;
					 $opt2_idx = $option_code;

					//추가 컬럼 add되는 내용이기 , 를 앞에찍음
					$addQueryCol .= ' ,optionarr ';
					$addQueryCol .= ' ,quantityarr ';
					$addQueryCol .= ' ,pricearr ';
					$addQueryCol .= ' ,opt1_idx ';
					$addQueryCol .= ' ,opt2_idx ';
					$addQueryCol .= ' ,op_type ';
					//추가 내용도 add되는 내용이기 , 를 앞에찍음 앞에 컬럼과 순서가 같아야함
					$addQueryVal .= " ,'".$option_code."' ";
					$addQueryVal .= " ,'".$option_quantity."' ";
					$addQueryVal .= " ,'".$select_option[0]->option_price."' ";
					$addQueryVal .= " ,'".$opt1_idx."' ";
					$addQueryVal .= " ,'".$opt2_idx."' ";
					$addQueryVal .= " ,'".$option_type."' ";
				}
			}

            if ( strlen($_ShopInfo->getMemid()) > 0 ) {
                // 로그인
                $whereQuery = "id = '" . $_ShopInfo->getMemid() . "' ";
            } else {
                // 비로그인
                $whereQuery = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
            }

			$checkSql = "SELECT basketidx FROM tblbasket WHERE {$whereQuery} ";
			$checkSql.= "AND productcode = '".$productcode."' AND opt1_idx = '".$opt1_idx."' AND opt2_idx = '".$opt2_idx."' ";
			$checkSql.= "AND text_opt_subject = '".$text_opt_subject."' AND text_opt_content = '".$text_opt_content."' ";
			$checkSql.= "AND delivery_type = '".$basketArray['delivery_type']."' AND store_code = '".$basketArray['store_code']."' ";
			$checkSql.= "AND reservation_date = '".$basketArray['reservation_date']."' AND post_code = '".$basketArray['post_code']."' ";
			$checkSql.= "AND address1 = '".$basketArray['address1']."' AND address2 = '".$basketArray['address2']."' ";
			$checkRes = pmysql_query( $checkSql, get_db_conn() );
			$checkRow = pmysql_fetch_object( $checkRes );
			if( $checkRow->basketidx ){
				$upSql = "UPDATE tblbasket SET quantity = '".$quantity."' WHERE basketidx = '".$checkRow->basketidx."' ";
				pmysql_query( $upSql );
				$this->this_code = 'S01';
				$this->return_idx = $checkRow->basketidx;
				return $this->this_code;
			}
			pmysql_free_result( $checkRes );
		}

		#insert함
		if( $this->success ){

			$basketidx = $this->check_basket( $productcode, $opt1_idx, $opt2_idx, $basketArray ); //장바구니 중복 확인
			if( $basketidx != '' ){
				$this->basket_quantity_update( $basketidx, $quantity ); //중복된 장바구니가 있을경우 수량변경
				return $this->this_code;
			} else { //중복된 장바구니가 없을경우 입력
				$sql = 'INSERT INTO tblbasket( tempkey, productcode, quantity, date, text_opt_subject, text_opt_content, delivery_type, store_code, reservation_date, post_code, address1, address2 '.$addQueryCol;
				$sql.= ' ) VALUES ( ';
				$sql.= "'".$_ShopInfo->getTempkey()."', '".$productcode."', '".$quantity."', '".$vdate."', '".$text_opt_subject."', '".$text_opt_content."', '".$basketArray['delivery_type']."', '".$basketArray['store_code']."', '".$basketArray['reservation_date']."', '".$basketArray['post_code']."', '".$basketArray['address1']."', '".$basketArray['address2']."' ".$addQueryVal;
				$sql.= ' ) RETURNING basketidx ';
				$result = pmysql_query( $sql, get_db_conn() );
				if( pmysql_errno() > 0 ){
					$this->this_code = 'E04';
				} else {
					$this->this_code = 'S01';
					$row = pmysql_fetch_object( $result );
					$this->return_idx = $row->basketidx;
					pmysql_free_result( $result );
				}
			}
		} else {
			//$this->this_code = 'E01';
		}

		return $this->this_code;

	}

	#장바구니의 상품을 수정함
	public function modify_item( $basketidx, $quantity, $option_code = '', $option_quantity = 0, $option_type = 0, $text_opt_content = '' )
	{
		global $_ShopInfo;
		$this->select_item( $basketidx );
		$addQueryCol = ''; // 추가 컬럼
		$vdate = date("YmdHis");

		$productcode = $this->basket[0]->productcode;

		#상품 그룹을 체크함
		$this->check_product_group( $productcode );
		
		#해당 상품정보를 가져옴
		$select_product = $this->select_product( $productcode );
		if( !$select_product ) {
			$this->success = false; //실제 상품이 존재하지 않음
			$this->this_code = 'E02';
		} else {
			#상품 조건부 수량을 체크함
			$this->check_product( $select_product, $quantity );		
			#해당 상품 수량 체크
			$this->check_product_quantity( $select_product, $quantity );
		}

		#옵션이 존재할 경우
		if( $option_code != '' ){
			if( $option_type == 1 ){  //독립형 옵션일 경우
				$tmp_option_code = explode( '@#', $option_code );
				$tmp_select_option = array();
				$tmp_option_price = 0;
				foreach( $tmp_option_code as $tmpOptionCode ){
					if( $tmpOptionCode != '' ){
						$tmp_select_option = $this->select_options( $productcode, $tmpOptionCode, $option_type );
						if( !$tmp_select_option ){
							$this->success = false; //실제 옵션이 존재하지 않음
							$this->this_code = 'E03';
							break;
						} else {
							$tmp_option_price += $tmp_select_option[0]->option_price;
							if( $select_product->quantity < 999999999 ){
								$this->check_option_quantity( $tmp_select_option[0], $option_quantity );
							}
						}
					}
				}
				if( $this->success ){
					$opt1_idx = $select_product->option1;
					$opt2_idx = $option_code;

					//추가되는 내용이기 , 를 앞에찍음
					$addQueryCol .= " ,optionarr = '".$option_code."' ";
					$addQueryCol .= " ,quantityarr = '".$option_quantity."' ";
					$addQueryCol .= " ,pricearr = '".$tmp_option_price." '";
					$addQueryCol .= " ,opt1_idx = '".$opt1_idx."' ";
					$addQueryCol .= " ,opt2_idx = '".$opt2_idx."' ";

				}
			} else { // 조합형 옵션일 경우
				#해당 옵션정보를 가져옴
				$select_option = $this->select_options( $productcode, $option_code, $option_type );
				if( !$select_option ) {
					$this->success = false; //실제 옵션이 존재하지 않음 또는 사용 중지된 옵션
					$this->this_code = 'E03';
				} else {
					#해당 옵션 수량 체크
					if( $select_product->quantity < 999999999 ){
						$this->check_option_quantity( $select_option[0], $option_quantity );
					}
					//옵션 1, 옵션2를 나누어줌
					/*
					$tmp_option = explode( chr(30), $option_code );
					$opt1_idx = '';
					$opt2_idx = '';
					if( $tmp_option[0] ) $opt1_idx = $tmp_option[0];
					if( $tmp_option[1] ) $opt2_idx = $tmp_option[1];
					*/
					 $opt1_idx = $select_product->option1;
					 $opt2_idx = $option_code;

					//추가되는 내용이기 , 를 앞에찍음
					$addQueryCol .= " ,optionarr = '".$option_code."' ";
					$addQueryCol .= " ,quantityarr = '".$option_quantity."' ";
					$addQueryCol .= " ,pricearr = '".$select_option[0]->option_price." '";
					$addQueryCol .= " ,opt1_idx = '".$opt1_idx."' ";
					$addQueryCol .= " ,opt2_idx = '".$opt2_idx."' ";

				}
			}

            if ( strlen($_ShopInfo->getMemid()) > 0 ) {
                // 로그인
                $whereQuery = "id = '" . $_ShopInfo->getMemid() . "' ";
            } else {
                // 비로그인
                $whereQuery = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
            }

			$checkSql = "SELECT COUNT( * ) AS cnt FROM tblbasket WHERE {$whereQuery} ";
			$checkSql.= "AND productcode = '".$productcode."' AND opt1_idx = '".$opt1_idx."' AND opt2_idx = '".$opt2_idx."' ";
            $checkSql.= "AND text_opt_subject = '".$text_opt_subject."' AND text_opt_content = '".$text_opt_content."' ";
            //$checkSql.= "AND quantity = '".$quantity."' ";
            $checkSql.= " AND basketidx != '".$basketidx."' ";

			$checkRes = pmysql_query( $checkSql, get_db_conn() );
			$checkRow = pmysql_fetch_object( $checkRes );
			if( $checkRow->cnt > 0 ){
				$this->success = false; //실제 옵션이 존재하지 않음 또는 사용 중지된 옵션
				$this->this_code = 'E10';

				return $this->this_code;
			}
			pmysql_free_result( $checkRes );
		}

		$text_opt_subject = $select_product->option2;

		#update함
		if( $this->success ){
			$sql = 'UPDATE tblbasket SET ';
			$sql.= " date = '".$vdate."' ";
			if ($quantity) $sql.= " ,quantity = '".$quantity."' ";
			$sql.= " ,text_opt_subject = '".$text_opt_subject."' ";
			$sql.= " ,text_opt_content = '".$text_opt_content."' ";
			$sql.= $addQueryCol;
			$sql.= "WHERE basketidx = '".$basketidx."' ";
			pmysql_query( $sql, get_db_conn() );
			if( pmysql_errno() > 0 ){
				$this->this_code = 'E04';
			} else {
				$this->this_code = 'S01';
			}
		} else {
			$this->this_code = 'E01';
		}

		$this->get_item();
		return $this->this_code;


	}

	#장바구니의 상품을 지움
	public function del_item( $basketidxs )
	{
		global $_ShopInfo;
		unset( $this->basket );
		#basketidxs를 IN에 맞게 변경
		$strWhere = $this->split_basketidxs( $basketidxs );

//		$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND basketidx IN ('".$strWhere."') ";
		$sql = "DELETE FROM tblbasket WHERE basketidx IN ('".$strWhere."') ";
		pmysql_query( $sql, get_db_conn() );
		if( pmysql_errno() > 0 ){
			$this->this_code = 'E09';
		} else {
			$this->this_code = 'S03';
		}
		$this->get_item();
		return $this->this_code;
	}

	#선택한 장바구니를 가져옴
	public function select_item( $basketidxs )
	{
		global $_ShopInfo;
		unset( $this->basket );
		#basketidxs를 IN에 맞게 변경
		$strWhere = $this->split_basketidxs( $basketidxs );
		
		$sql = "SELECT tempkey, productcode, quantity, basketidx, id, optionarr, quantityarr, pricearr, opt1_idx, opt2_idx, op_type ";
//		$sql.= "FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND basketidx IN ('".$strWhere."') ";
		$sql.= "FROM tblbasket WHERE basketidx IN ('".$strWhere."') ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$this->basket[] = $row;
			
		}
	}

	#상품의 옵션정보를 가져옴
	public function select_options( $productcode, $option_code = '', $option_type = 0 )
	{
		$sql = "SELECT option_num, option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, ";
		$sql.= "self_goods_code ";
		$sql.= "FROM tblproduct_option WHERE productcode = '".$productcode."' AND option_type = '".$option_type."' AND option_use = 1 ";
		if( strlen( $option_code ) > 0 ) $sql.= "AND option_code = '".$option_code."' ";
		$sql.= "ORDER BY option_num ASC ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$select_options[] = $row;
		}
		pmysql_free_result( $result );
		
		return $select_options;
	}

	#장바구니에 넣을 상품정보를 가져옴
	public function select_product( $productcode )
	{
		$sql = "SELECT pridx, productcode, productname, sellprice, consumerprice, ";
		$sql.= "buyprice, reserve, reservetype, quantity, option1, option2, addcode, ";
		$sql.= "maximage, minimage, tinyimage, deli, deli_price, display, selfcode, ";
		$sql.= "vender, brand, min_quantity, max_quantity, setquota, supply_subject, deli_qty, ";
		$sql.= "deli_select, rate, self_goods_code, timesale_code "; // self_goods_code 추가 (20160610_김재수 추가)
		//$sql.= "detail_deli, deli_min_price, deli_package ";
		$sql.= "FROM tblproduct WHERE productcode = '".$productcode."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_object( $result );
		$select_product = $row;
		pmysql_free_result( $result );

		return $select_product;
	}

	#옵션 수량을 체크함
	public function check_option_quantity( $option, $option_quantity )
	{
		if( $option->option_quantity <= 0 ) {
			$this->success = false;
			$this->this_code = 'E06';
		} else if(  $option->option_quantity < $option_quantity ) {
			$this->success = false;
			$this->this_code = 'E07';
		}
		//else $this->success = true;
	}

	#상품 수량을 체크함
	public function check_product_quantity( $product, $quantity )
	{
		if( $product->quantity <= 0 ) {
			$this->success = false;
			$this->this_code = 'F04';
		} else if(  $product->quantity < $quantity ) {
			$this->success = false;
			$this->this_code = 'F05';
		}
		//else $this->success = true;
	}

	#상품 그룹을 체크함
	public function check_product_group( $productcode )
	{
		global $_ShopInfo;

		//상품의 해당 카테고리를 가져옴
		$cate_sql = "SELECT c_category FROM tblproductlink WHERE c_productcode = '".$productcode."' AND c_maincate = 1 ";
		$cate_res = pmysql_query( $cate_sql, get_db_conn() );
		$cate_row = pmysql_fetch_object( $cate_res );
		pmysql_free_result( $cate_res );

		if( strlen( $cate_row->c_category ) == 0 ) {
			$this->success = false;
			$this->this_code = 'E05';
		} else {
			$sql = "SELECT group_code FROM tblproductcode WHERE code_a||code_b||code_c||code_d = '".$cate_row->c_category."' ";
			$result = pmysql_query( $sql, get_db_conn() );
			$row = pmysql_fetch_object( $result );
			if($row->group_code=="NO") {	//숨김 분류
				$this->success = false;
				$this->this_code = 'E06';
			} elseif($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
				$this->success = false;
				$this->this_code = 'E07';
			} elseif(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
				$this->success = false;
				$this->this_code = 'E08';
			}
			pmysql_free_result( $result );
		}
	}

	#상품 조건부 수량을 체크함
	public function check_product( $product, $quantity )
	{
		if( $product->min_quantity != 0 && $product->min_quantity > 1 && $quantity < $product->min_quantity ){ //최소 구매수량
			$this->success = false;
			$this->this_code = 'F02';
		}
		if( $product->max_quantity > 0 && $quantity > $product->max_quantity ){ //최대 구매수량
			$this->success = false;
			$this->this_code = 'F03';
		}

	}

	#장바구니의 중복되는 상품이 있는지 체크함
	public function check_basket( $productcode, $opt1, $opt2, $basketArray = array() ){
		global $_ShopInfo;
		$basketidx = '';

        if ( strlen($_ShopInfo->getMemid()) > 0 ) {
            // 로그인
            $whereQuery = "id = '" . $_ShopInfo->getMemid() . "' ";
        } else {
            // 비로그인
            $whereQuery = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
        }

		$sql = "SELECT basketidx FROM tblbasket WHERE {$whereQuery} AND productcode ='".$productcode."' ";
		$sql.= "AND opt1_idx = '".$opt1."' AND opt2_idx = '".$opt2."' ";
		$sql.= "AND delivery_type = '".$basketArray['delivery_type']."' AND store_code = '".$basketArray['store_code']."' ";
		$sql.= "AND reservation_date = '".$basketArray['reservation_date']."' AND post_code = '".$basketArray['post_code']."' ";
		$sql.= "AND address1 = '".$basketArray['address1']."' AND address2 = '".$basketArray['address2']."' ";
		//$sql.= "AND quantityarr = '".$option_code."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_object( $result );
		$basketidx = $row->basketidx;
		pmysql_free_result( $result );

		return $basketidx;
	}

	#특정 장바구니의 수량을 교체해줌
	public function basket_quantity_update( $basketidx, $quantity ){
		$sql = "UPDATE tblbasket SET quantity = '".$quantity."' WHERE basketidx = '".$basketidx."' ";
		pmysql_query( $sql, get_db_conn() );
		if( pmysql_errno() ){
			$this->this_code = 'E04';
			$this->success = false;
		} else {
			$this->this_code = 'S02';
			$this->return_idx = $basketidx;
		}
	}

	#특정 장바구니의 옵션수량을 교체해줌
	public function basket_quantityarr_update( $basketidx, $quantity ){
		$sql = "UPDATE tblbasket SET quantityarr = '".$quantity."' WHERE basketidx = '".$basketidx."' ";
		pmysql_query( $sql, get_db_conn() );
		if( pmysql_errno() ){
			$this->this_code = 'E04';
			$this->success = false;
		} else {
			$this->this_code = 'S02';
			$this->return_idx = $basketidx;
		}
	}

	#장바구니 idx를 잘라줌
	public function split_basketidxs ( $basketidxs )
	{
		$exp_basketidx = explode("|", $basketidxs );

		foreach( $exp_basketidx as $k => $v){
			if($v) $arr_basketidx[] = $v;
		}
		
		$strWhere = implode("', '", $arr_basketidx );
		
		return $strWhere;
	}
	
	public function get_success()
	{
		return $this->success;
	}

}
?>
