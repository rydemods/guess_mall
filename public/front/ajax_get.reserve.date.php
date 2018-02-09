<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/paging.php");

	$delivery_type = $_REQUEST['delivery_type'];
	$mode = $_REQUEST['mode'];
	$sel_date = $_REQUEST['sel_date'];
	if($mode == "date"){
		if($delivery_type == "1"){

			$dayPlus = 0;
			if( 22 < date('H') ){
				#23시가 넘어가면 다음날로 넘어감
				$dayPlus = 1;
			}
			for( $i = 0; $i < 6; $i++ ){
				$sellDate[] = date('Y-m-d', strtotime( '+'.( $i + $dayPlus ).' day' ) );
			}

			echo "<option value=''>- 방문일 선택 -</option>";
			foreach($sellDate as $v){
?>
				<option value="<?=$v?>"><?=$v?></option>
<?
			}
		}else{
			$dayPlus = 0;
			if( 14 < date('H') ){
				#15시가 넘어가면 주문 불가
				echo "0";
			}else{
				echo "1";
			}
		}
	}else if($mode == "dateFlag"){
		if($delivery_type == "1"){
			# 23시가 넘어가면 다음날로 넘어감
			# 오늘이 아니면 시간에 상관없이 가능
			if($sel_date == date("Y-m-d")){
				if( 22 < date('H') ){
					echo "0";
				}else{
					echo "1";
				}
			}else{
				echo "1";
			}
		}else{
			#15시가 넘어가면 주문 불가
			if( 14 < date('H') ){
				//echo "0";
				echo "1"; //<-- 임시
			}else{
				echo "1";
				
			}
		}
		

	}else if($mode == "stockChk"){
		//data : { mode : 'stockChk', quantity : stockQuantity, prodcode : stockProdcd, colorcode : stockColorcd, size : stockSize },
		$quantity = $_REQUEST['quantity'];
		$prodcode = $_REQUEST['prodcode'];
		$colorcode = $_REQUEST['colorcode'];
		$size = $_REQUEST['size'];
		$store_code = $_REQUEST['storecode'];
		$bool = $_REQUEST['flag'];

		$arrReturn = array();
		if($store_code=='delivery_chk') {	//2016-10-06 libe90 매장발송건일 경우 수량 가장 많은 매장의 재고로 가능여부 체크
			$shopRealtimeStock = getErpProdShopStock_Type($prodcode, $colorcode, $size, 'delivery');
			#$arrReturn['asdadasd'] = $shopRealtimeStock[sumqty];

			if($quantity > $shopRealtimeStock['availqty']){
				$arrReturn['flag'] = false;
				$arrReturn['str'] = "재고가 부족합니다.";
				if($bool == "+"){
					$arrReturn['quan'] = $quantity - 1;
				}else if($bool == "-"){
					$arrReturn['quan'] = $quantity + 1;
				}
			}else{
				$arrReturn['flag'] = true;
				$arrReturn['quan'] = $quantity;
			}
		}else{
			$shopRealtimeStock = getErpPriceNStock($prodcode, $colorcode, $size, $store_code);
			#$arrReturn['asdadasd'] = $shopRealtimeStock[sumqty];

			if($quantity > $shopRealtimeStock['sumqty']){
				$arrReturn['flag'] = false;
				$arrReturn['str'] = "해당 매당의 재고가 부족합니다.";
				if($bool == "+"){
					$arrReturn['quan'] = $quantity - 1;
				}else if($bool == "-"){
					$arrReturn['quan'] = $quantity + 1;
				}
			}else{
				$arrReturn['flag'] = true;
				$arrReturn['quan'] = $quantity;
			}
		}
		echo json_encode($arrReturn);
	}
?>