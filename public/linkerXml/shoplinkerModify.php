<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/product.class.php");
	include_once($Dir."conf/config.linker.php");
	
	$saleStatusArray = array("1" => "001", "3" => "003", "6" => "006");
	$deliStatusArray = array("1" => "001", "2" => "002", "3" => "003", "4" => "004");

	$productcode = $_GET['pd_code'];

	$resGoods = pmysql_query("SELECT * FROM tblproduct WHERE productcode = '".$productcode."'", get_db_conn());
	while($dataGoods=pmysql_fetch_array($resGoods)) {
		$option_name1 = $option_name2 = "";
		$ary_option_name1 = $ary_option_name2 = array();
		$option_name_result = "";
		$goodsQuantity = 0;
		$status = 1;
		if($dataGoods['brand']) list($brandnm)=pmysql_fetch("SELECT brandname FROM tblproductbrand WHERE bridx='".$dataGoods['brand']."'");
		/*
		list($womanCategoryCount)=pmysql_fetch("SELECT count(c_category) FROM tblproductlink WHERE c_productcode='".$dataGoods['productcode']."' AND c_category like '001%'");
		list($manCategoryCount)=pmysql_fetch("SELECT count(c_category) FROM tblproductlink WHERE c_productcode='".$dataGoods['productcode']."' AND c_category like '002%'");
		*/
		list($mainCategory)=pmysql_fetch("SELECT c_category FROM tblproductlink WHERE c_productcode = '".$dataGoods['productcode']."' AND c_maincate = '1'");
		$mainCategoryToSend = $mainCategoryArray = array();
		if($mainCategory){
			list($mainCategoryArray[0], $mainCategoryArray[1], $mainCategoryArray[2], $mainCategoryArray[3]) = sscanf($mainCategory,'%3s%3s%3s%3s');
			foreach($mainCategoryArray as $ck => $cv){
				if($cv == '000' || !$cv){
					continue;
				}else{
					$mainCategoryToSend[$ck] = $cv;
				}
			}
		}
		$sexStatus = "";
		/*
		if($womanCategoryCount > 0 && $manCategoryCount > 0){
			$sexStatus = "003";
		}else if($womanCategoryCount > 0 && $manCategoryCount == 0){
			$sexStatus = "002";
		}else if($womanCategoryCount == 0 && $manCategoryCount > 0){
			$sexStatus = "001";
		}else{
			$sexStatus = "003";
		}
		*/

		#1.무료 2.착불 3.선결제 4.착불/선결제
		$deliType = "";
		/*
		if($dataGoods['deli'] == 'H'){
			$deliType = "기본";
		}else if($dataGoods['deli'] == 'F'){
			$deliType = "1";
		}else if($dataGoods['deli'] == 'G'){
			$deliType = "2";
		}else if($dataGoods['deli'] == 'N'){
			$deliType = "3";
		}else if($dataGoods['deli'] == 'Y'){
			$deliType = "3";
		}else{
			$deliType = "2";
		}
		*/

		if(preg_match("/^\[OPTG\d{4}\]$/", $dataGoods['option1'])){
			$optcode = substr($dataGoods['option1'], 5, 4);
			$dataGoods['option1'] = "";
			$dataGoods['option_price'] = "";
		}

		$product = new PRODUCT();
		$dc_data = $product->getProductDcRate($dataGoods['productcode']);
		$check_optout = $check_optin = $optionValue1 = $optionValue2 = $optionValueResult = $pricetok = $tok = $tok2 = array();
		$priceindex = $check_optea = $goods_count = 0;
		$option_price = "";
		
		$optionArray = explode(",",ltrim($dataGoods['option_quantity'],','));


		if(strlen($dicker=dickerview($dataGoods['etctype'], number_format($dataGoods['sellprice']),1))>0){



			$SellpriceValue = $dicker;
			if($optionArray['0']){
				$goodsQuantity = $optionArray['0'];
			}else{
				$goodsQuantity = $dataGoods['quantity'];
			}




		}else if(strlen($optcode)==0 && strlen($dataGoods['option_price'])>0) {





			$option_price = $dataGoods['option_price'];
			$optioncnt = explode(",",ltrim($dataGoods['option_quantity'],','));
			$pricetok = explode(",", $option_price);
			$priceindex = count($pricetok);

			$tok = explode(",", $dataGoods['option1']);
			$tok2 = explode(",", $dataGoods['option2']);
			$goods_count=count($tok);
			if($goods_count>"1"){
				$check_optea="1";	
			}

			$option_quantity_array=explode(",",$dataGoods['option_quantity']);
			for($i=0;$i<5;$i++){
				for($j=0;$j<10;$j++){
					$option_quantity_array[$j*10+$i+1] = $option_quantity_array[$j*10+$i+1];
				}
			}

			for($tmp=0;$tmp<50;$tmp++) {
				$pricetok[$tmp]=number_format(getProductSalePrice($pricetok[$tmp], $dc_data[price]));
				# 2차 옵션 번호
				$option2Num = floor($tmp/10)+1;
				if($tok[($tmp%10)+1] && $tok2[$option2Num]){
					# 무제한일 경우 재고 900개로 셋팅
					if(!$optioncnt[$tmp]) $optioncnt[$tmp] = 900;
					if($tok2[$option2Num]){
						$optionValue1[] = $tok[($tmp%10)+1]."/".$tok2[$option2Num]."^^".$optioncnt[$tmp]."<**>".(str_replace(",","",$pricetok[($tmp%10)])-str_replace(",","",$pricetok[0]));
					}else{
						$optionValue1[] = $tok[($tmp%10)+1]."^^".$optioncnt[$tmp]."<**>".(str_replace(",","",$pricetok[($tmp%10)])-str_replace(",","",$pricetok[0]));
					}
				}else{
					$optionValue1[] = "";
				}

				if(strlen($dataGoods['option2']) == 0 && $optioncnt[$tmp]=="0"){ $check_optout[]='1';}
				else{  $check_optin[]='1';}
				$goodsQuantity += $optioncnt[$tmp];
			}
			$SellpriceValue = str_replace(",","",$pricetok[0]);

		}else{



			$SellpriceValue = $dataGoods['sellprice'];
			if($optionArray['0']){
				$goodsQuantity = $optionArray['0'];
			}else{
				$goodsQuantity = $dataGoods['quantity'];
			}




		}

		
		if($goodsQuantity == '0'){
			$status = 3;
			$goodsQuantity = 0;
		}

		if($goodsQuantity != '0' && !$goodsQuantity ){
			$status = 1;
			$goodsQuantity = 900;
		}

		if(strlen($optionArray['0']) > 0 && ($optionArray['0'] <= "0" || (count($check_optin)=='0' && $check_optea))){
			$status = 3;
		}



		$deli_init = false;
		$deliType = "1";
		$deli_productprice = $pd_deliprice = $deli_price = $pd_sumprice = $pd_delisumprice = 0;
		if (($dataGoods['deli'] == "Y" || $dataGoods['deli'] == "N") && $dataGoods['deli_price'] > 0) {
			$deliType = "3";
			if($dataGoods['deli'] == "Y") {
				$deli_productprice += $dataGoods['deli_price'];
			} else {
				$deli_productprice += $dataGoods['deli_price'];
			}
			$pd_delisumprice = $SellpriceValue;
		} else if($dataGoods['deli'] == "F" || $dataGoods['deli'] == "G") {
			$deli_productprice += 0;
			if($dataGoods['deli'] == "F") {
				$deliType = "1";
			} else {
				$deliType = "2";
			}
			$pd_delisumprice = $SellpriceValue;
		} else {
			$deli_init = true;
			$deliType = '4';
			$pd_delisumprice = $SellpriceValue;
		}

		$pd_deliprice = $deli_productprice;
		$pd_sumprice = $pd_delisumprice;
		if($_data->deli_basefee > 0) {
			if($_data->deli_basefeetype == "Y") {
				$pd_delisumprice = $pd_sumprice;
			}

			if ($pd_delisumprice < $_data->deli_miniprice && $deli_init) {
				$pd_deliprice += $_data->deli_basefee;
			}
		} else if(strlen($_data->deli_limit) > 0) {
			if($_data->deli_basefeetype == "Y") {
				$pd_delisumprice = $pd_sumprice;
			}

			if($deli_init) {
				$delilmitprice = setDeliLimit($pd_delisumprice, $_data->deli_limit);
				$pd_deliprice += $delilmitprice;
			}
		}
		$deli_price += $pd_deliprice;
		if($deli_price == 0) $deliType = '1';


		if (ord($dataGoods['option1'])){
			if(!$option_name1){
				$option_name1 = htmlspecialchars($tok[0]);
				$ary_option_name1 = array_slice($tok, 1);
			}
		}
		if (ord($dataGoods['option2'])){
			if(!$option_name2){
				$option_name2 = htmlspecialchars($tok2[0]);
				$ary_option_name2 = array_slice($tok2, 1);
			}
			if(!count($optionValue2)) $optionValue2 = array_slice($tok2, 1);
		}		

		if (ord($dataGoods['maximage'])) {
			$imageUrls = "http://".$_ShopInfo->getShopurl()."data/shopimages/product/".$dataGoods['maximage'];
		}

		$contents = stripslashes($dataGoods['content']);
		$contents = str_replace("/SE2/upload/", "http://".$_ShopInfo->getShopurl()."SE2/upload/", $contents);

		# 정규식으로 [~~~] 안에 문자는 지우고 보냄
		$strProductName = preg_replace("/\[.*?\]/", "", $dataGoods['productname']);





		$aryOptionInfo = array();
		foreach($optionValue1 as $optVal1){
			if($optVal1) $aryOptionInfo[] = $optVal1;
		}

		if($_GET['solodout']){
			$status = '3';
		}

		echo "<?xml version='1.0' encoding='euc-kr'?>\n";
		echo "<openMarket>\n";
		echo "	<MessageHeader>\n";
		echo "		<sendID>1</sendID>\n";
		echo "		<senddate>".date("Ymd")."</senddate>\n";
		echo "	</MessageHeader>\n";
		echo "	<productInfo>\n";
		echo "		<product>\n";
		echo "			<customer_id>".$linkerData['customer_id']."</customer_id>\n";
		echo "			<partner_product_id><![CDATA[".$dataGoods['productcode']."]]></partner_product_id>\n";
		echo "			<mall_product_id><![CDATA[".$dataGoods['mall_product_id']."]]></mall_product_id>\n";
		echo "			<sale_status>".$saleStatusArray[$status]."</sale_status>\n";

		if(!$dataGoods['buyprice']) $dataGoods['buyprice'] = $SellpriceValue;
		if(!$dataGoods['consumerprice']) $dataGoods['consumerprice'] = $SellpriceValue;

		echo "			<market_price>".$dataGoods['consumerprice']."</market_price>\n";
		echo "			<sale_price>".$SellpriceValue."</sale_price>\n";
		echo "			<supply_price>".$dataGoods['buyprice']."</supply_price>\n";
		echo "			<market_price_p>".$dataGoods['consumerprice']."</market_price_p>\n";
		echo "			<sale_price_p>".$SellpriceValue."</sale_price_p>\n";
		echo "			<supply_price_p>".$dataGoods['buyprice']."</supply_price_p>\n";

		echo "			<quantity>".$goodsQuantity."</quantity>\n";


		$optionTitle = "";
		if($option_name1 && !$option_name2){
			$optionTitle = $option_name1."||";
		}else if($option_name1 && $option_name2){
			$optionTitle = $option_name1."/".$option_name2."||";
		}else{
			$optionTitle = "";
		}
		if(count($aryOptionInfo) > 0){
			echo "			<opt_info><![CDATA[".$optionTitle.implode(",", $aryOptionInfo)."]]></opt_info>\n";
		}else{
			echo "			<opt_info><![CDATA[]]></opt_info>\n";
		}
		#echo "			<maker><![CDATA[".$makerName."]]></maker>\n";
		#echo "			<brand_nm><![CDATA[".$brandName."]]></brand_nm>\n";
		#echo "			<model_nm><![CDATA[".$modelName."]]></model_nm>\n";
		echo "			<product_name><![CDATA[".$dataGoods[productname]."]]></product_name>\n";
		#echo "			<keyword><![CDATA[".$goodsKeyword."]]></keyword>\n";
		#echo "			<origin><![CDATA[".$originName."]]></origin>\n";
		echo "			<auth_no> </auth_no>\n";
		echo "			<detail_desc><![CDATA[".$contents."]]></detail_desc>\n";
		echo "			<new_desc_top><![CDATA[".$contents."]]></new_desc_top>\n";
		echo "		</product>\n";
		echo "	</productInfo>\n";
		echo "</openMarket>";
	}
?>