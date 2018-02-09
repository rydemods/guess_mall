<?
	include "../../lib/db.class.php";
	include "../../lib/lib.func.php";
	include "../../conf/config.linker.php";
	
	$db = new db("../../conf/db.conf.php");

	$saleStatusArray = array(
											"i01" => array("0101"=>"material", "0102"=>"color", "0103"=>"size", "0104"=>"maker", "0105"=>"origin", "0106"=>"wash", "0107"=>"maker_dt", "0108"=>"quality", "0109"=>"as"), 
											"i02" => array("0201"=>"material", "0202"=>"color", "0203"=>"size", "0204"=>"maker", "0205"=>"origin", "0206"=>"wash", "0207"=>"quality", "0208"=>"as"), 
											"i03" => array("0301"=>"kind", "0302"=>"material", "0303"=>"color", "0304"=>"size", "0305"=>"maker", "0306"=>"origin", "0307"=>"wash", "0308"=>"quality", "0309"=>"as"), 
											"i04" => array("0401"=>"kind", "0402"=>"material", "0403"=>"size", "0404"=>"maker", "0405"=>"origin", "0406"=>"wash", "0407"=>"quality", "0408"=>"as")
								);

	$goodscd = $_GET['goodscd'];
	$goodsinfo = $_GET['goods_info_reg'];
	$resGoods = $db->query("SELECT * FROM ".GD_GOODS." WHERE GOODSCD = '".$goodscd."' AND ROWNUM = 1");
	while($dataGoods=$db->fetch($resGoods)){		
		$goodsCode = $dataGoods['goodscd'];

		$dataInfoAry = array();
		$resOptions = $db->query("SELECT * FROM ".GD_GOODS_OPTION." WHERE GOODSNO = '".$dataGoods[goodsno]."' ORDER BY SNO ASC");
		while($dataOptions=$db->fetch($resOptions)){			
			if($dataOptions['opt1']) $tmpOptionArray1[$dataOptions['opt1']] = $dataOptions['opt1'];
			if($dataOptions['opt2']) $tmpOptionArray2[$dataOptions['opt2']] = $dataOptions['opt2'];
			$tmpOptionValueArray[] = $dataOptions;
		}




		$resGoodsColor = $db->query("SELECT team_color FROM ".GD_GOODS." WHERE GOODSCD = '".$goodscd."'");
		while($dataGoodsColor=$db->fetch($resGoodsColor)){
			$colorLoop[] = $dataGoodsColor['team_color'];
		}
		
		if(count($colorLoop)>0){
			$infoColor = implode(", ", $colorLoop);
		}


		# 팀컬러가 있으면 OPT1이 사이즈가 되고 없으면 OPT2가 사이즈가 됨
		if($dataGoods[team_color]){
			$infoSize = implode(", ", $tmpOptionArray1);
		}else{
			$infoSize = implode(", ", $tmpOptionArray2);
		}
		if($goodsinfo == "i02"){
			$infoSize = $infoSize."##굽높이";
		}else if($goodsinfo == "i03"){
			$infoSize = "가로##세로##높이";
			$dataInfoAry['kind'] = "가방";
		}else if($goodsinfo == "i04"){
			$dataInfoAry['kind'] = "패션잡화(모자/벨트/액세러리)";
		}
		
		
		$queryMaterial = "SELECT 
										c_gubun,c_material,c_mixrate 
									FROM 
										".GD_GOODS." a, TEMP_MATERIAL b 
									WHERE 
										b.c_erp_code = a.goodscd 
										AND a.GOODSCD = '".$goodscd."' 
									GROUP BY c_sid, c_gubun,c_material,c_mixrate 
									ORDER BY 
										c_sid";
		$resMaterial = $db->query($queryMaterial);
		while($dataMaterial = $db->fetch($resMaterial)) {
			$dataGoods[material][]= $dataMaterial[c_gubun].':'.$dataMaterial[c_material].' '.$dataMaterial[c_mixrate].'%';
		}
		$dataInfoAry['color'] = $infoColor;
		$dataInfoAry['size'] = $infoSize;
		$dataInfoAry['material'] = $dataGoods[material]?implode(", ", $dataGoods[material]):"-";
		$dataInfoAry['maker'] = $dataGoods[maker]?$dataGoods[maker]:"-";
		$dataInfoAry['origin'] = $dataGoods[origin]?$dataGoods[origin]:"-";
		$dataInfoAry['maker_dt'] = $dataGoods[launchdt]?$dataGoods[launchdt]:"-";
		$dataInfoAry['wash'] = "세탁방법 및 취급시 주의사항";
		$dataInfoAry['quality'] = "품질보증기준 : 구매일로부터 1년간";
		$dataInfoAry['as'] = "02-520-0923 / 02-520-0115";


		echo "<?xml version='1.0' encoding='utf-8'?>\n";
		echo "<openMarket>\n";
		echo "	<goodsinfo>\n";
		echo "		<customer_id>".$linkerData['customer_id']."</customer_id>\n";
		echo "		<product_id></product_id>\n";
		echo "		<partner_product_id>".$goodsCode."</partner_product_id>\n";
		echo "		<lclass_id>".$goodsinfo."</lclass_id>\n";

			foreach($saleStatusArray[$goodsinfo] as $kk => $vv){
				echo "<item>\n";
				echo "	<item_seq>".$kk."</item_seq>\n";
				echo "	<item_info><![CDATA[".$dataInfoAry[$vv]."]]></item_info>\n";
				echo "</item>\n";
			}
		echo "	</goodsinfo>\n";
		echo "</openMarket>";
		
		/*
		<?xml version="1.0" encoding="euc-kr"?>
			<openMarket>
				<goodsinfo>
					<customer_id>고객사코드</customer_id>
					<product_id>샵링커 상품코드</product_id>
					<partner_product_id>자체 상품코드</partner_product_id>
					<lclass_id>i01</lclass_id>
					<item>
						<item_seq>0101</item_seq>
						<item_info><![CDATA[제품소재]]></item_info>
					</item>
					<item>
						<item_seq>0102</item_seq>
						<item_info><![CDATA[색상]]></item_info>
					</item>
					<item>
						<item_seq>0103</item_seq>
						<item_info><![CDATA[치수]]></item_info>
					</item>
					<item>
						<item_seq>0104</item_seq>
						<item_info><![CDATA[제조자,수입품의 경우 수입자를 함께 표기]]></item_info>
					</item>
					<item>
						<item_seq>0105</item_seq>
						<item_info><![CDATA[제조국]]></item_info>
					</item>
					<item>
						<item_seq>0106</item_seq>
						<item_info><![CDATA[세탁방법 및 취급시 주의사항]]></item_info>
					</item>
					<item>
						<item_seq>0107</item_seq>
						<item_info><![CDATA[제조년월]]></item_info>
					</item>
					<item>
						<item_seq>0108</item_seq>
						<item_info><![CDATA[품질보증기준]]></item_info>
					</item>
					<item>
						<item_seq>0109</item_seq>
						<item_info><![CDATA[AS책임자와 전화번호]]></item_info>
					</item>		
				</goodsinfo>
			</openMarket>
		
		*/
		/*
		$authNumber = $dataGoods['goodsno'].date("His");
		echo "<?xml version='1.0' encoding='utf-8'?>\n";
		echo "<openMarket>\n";
		echo "	<MessageHeader>\n";
		echo "		<sendID>1</sendID>\n";
		echo "		<senddate>".date("Ymd")."</senddate>\n";
		echo "	</MessageHeader>\n";
		echo "	<productInfo>\n";
		echo "		<product>\n";
		echo "			<customer_id>".$linkerData['customer_id']."</customer_id>\n";
		echo "			<partner_product_id>".$goodsCode."</partner_product_id>\n";
		echo "			<product_name><![CDATA[".$goodsName."]]></product_name>\n";
		echo "			<sale_status>".$saleStatus."</sale_status>\n";
		echo "			<category_l><![CDATA[".$categoryShopLinker_l."]]></category_l>\n";
		echo "			<category_m><![CDATA[".$categoryShopLinker_m."]]></category_m>\n";
		echo "			<category_s><![CDATA[".$categoryShopLinker_s."]]></category_s>\n";
		echo "			<category_d><![CDATA[".$categoryShopLinker_d."]]></category_d>\n";
		echo "			<ccategory_l><![CDATA[".$categoryCustomer_l."]]></ccategory_l>\n";
		echo "			<ccategory_m><![CDATA[".$categoryCustomer_m."]]></ccategory_m>\n";
		echo "			<ccategory_s><![CDATA[".$categoryCustomer_s."]]></ccategory_s>\n";
		echo "			<ccategory_d><![CDATA[".$categoryCustomer_d."]]></ccategory_d>\n";
		echo "			<maker><![CDATA[".$makerName."]]></maker>\n";
		echo "			<maker_dt>".$makeDate."</maker_dt>\n";
		echo "			<origin><![CDATA[".$originName."]]></origin>\n";
		echo "			<image_url num='1'><![CDATA[".$imageUrl1."]]></image_url>\n";
		echo "			<start_price>".$priceStart."</start_price>\n";
		echo "			<market_price>".$priceMarket."</market_price>\n";
		echo "			<sale_price>".$priceSale."</sale_price>\n";
		echo "			<supply_price>".$priceSupply."</supply_price>\n";
		echo "			<market_price_p>".$priceMarketP."</market_price_p>\n";
		echo "			<sale_price_p>".$priceSaleP."</sale_price_p>\n";
		echo "			<supply_price_p>".$priceSupplyP."</supply_price_p>\n";
		echo "			<delivery_charge_type><![CDATA[".$deliveryChargeType."]]></delivery_charge_type>\n";
		echo "			<delivery_charge>".$deliveryCharge."</delivery_charge>\n";
		echo "			<tax_yn>".$taxYn."</tax_yn>\n";
		echo "			<detail_desc><![CDATA[".$detailDesc."]]></detail_desc>\n";
		echo "			<quantity>".$goodsQuantity."</quantity>\n";
		echo "			<salearea><![CDATA[".$saleArea."]]></salearea>\n";
		echo "			<partner_id_tmp><![CDATA[".$partnerId."]]></partner_id_tmp>\n";
		echo "			<sex>".$goodsSex."</sex>\n";
		echo "			<keyword><![CDATA[".$goodsKeyword."]]></keyword>\n";
		echo "			<model><![CDATA[".$modelName."]]></model>\n";
		echo "			<model_no><![CDATA[".$modelGoodsno."]]></model_no>\n";
		echo "			<option_kind>".$optionKind."</option_kind>\n";
		echo "			<option_name num='1'><![CDATA[".$optionName1."]]></option_name>\n";
		echo "			<option_value num='1'><![CDATA[".$optionValue1."]]></option_value>\n";
		echo "			<option_name num='2'><![CDATA[".$optionName2."]]></option_name>\n";
		echo "			<option_value num='2'><![CDATA[".$optionValue2."]]></option_value>\n";
		echo "			<opt_info><![CDATA[".$optInfo."]]></opt_info>\n";
		echo "			<brand><![CDATA[".$brandName."]]></brand>\n";
		echo "			<auth_no> </auth_no>\n";
		echo "		</product>\n";
		echo "	</productInfo>\n";
		echo "</openMarket>";
		*/
	}
?>