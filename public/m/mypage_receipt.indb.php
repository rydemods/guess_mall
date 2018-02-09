<?
//header("Content-Type: text/html; charset=UTF-8");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/product.class.php");
	
	$mode=$_POST["mode"];

	$taxData = pmysql_fetch("SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid FROM tblshopinfo", get_db_conn());

	$tax_rate=$taxData['tax_rate'];
	$tax_cnum=$taxData['tax_cnum'];

	if($mode=="receipt_cash") {		
		$ordercode = $_POST['ordercode'];
		if($ordercode){
			$dataOrder = pmysql_fetch("select * from tblorderinfo where ordercode='".$ordercode."'");

			$up_name = $dataOrder["sender_name"];
			$up_email = $dataOrder["sender_email"];
			$up_tel = $dataOrder["sender_tel"];
			$resultItem_sql = '';
			$resultItem_sql = "SELECT productname FROM tblorderproduct  ";
			$resultItem_sql.= "WHERE productcode NOT LIKE 'COU%' AND ordercode = '".$ordercode."' ";
			$resultItem_sql.= "GROUP BY ordercode, productcode, productname ";
			//$resultItem=pmysql_query("select * from tblorderproduct where productcode not like 'COU%' AND ordercode='".$ordercode."'");
			$resultItem=pmysql_query( $resultItem_sql, get_db_conn() );
			$itemCount = 0;
			$up_productname = "";
			while($rowItem=pmysql_fetch_object($resultItem)){
				if(!$up_productname) $up_productname = strcutDot($rowItem->productname, 22);
				$itemCount++;
			}

			if($itemCount > 1){
				$up_productname .= " 외 ".($itemCount-1)."건";
			}
			# 2015 12 08 유동혁 
			# price에 더이상 가격이합산되어 나오지 않아 수정함 
			$up_amt = (int)$dataOrder["price"] + (int)$dataOrder["deli_price"] - (int)$dataOrder["dc_price"] - (int)$dataOrder["reserve"];

			$up_tr_code=$_POST["up_tr_code"];

			/*$up_mobile1=$_POST["up_mobile1"];
			$up_mobile2=$_POST["up_mobile2"];
			$up_mobile3=$_POST["up_mobile3"];*/

			$up_mobile=$_POST["up_mobile"];

			$up_comnum1=$_POST["up_comnum1"];
			$up_comnum2=$_POST["up_comnum2"];
			$up_comnum3=$_POST["up_comnum3"];

			if($tax_rate==10) {
				$up_amt1=$up_amt;
				$up_amt4=floor(($up_amt1/1.1)*0.1);
				$up_amt2=$up_amt1-$up_amt4;
				$up_amt3=0;
			} else {
				$up_amt1=$up_amt;
				$up_amt2=0;
				$up_amt3=0;
				$up_amt4=0;
			}

			if($up_tr_code=="0") {
				#개인
				//$up_id_info=$up_mobile1.$up_mobile2.$up_mobile3;	
				$up_id_info=$up_mobile;	
			} else {
				#사업자
				$up_id_info=$up_comnum1.$up_comnum2.$up_comnum3;
			}

			#$tsdtime=substr($ordercode,0,14);
			$tsdtime=date('YmdHmi');

			$sql = "INSERT INTO tbltaxsavelist(
			ordercode	,
			tsdtime		,
			tr_code		,
			tax_no		,
			id_info		,
			name		,
			tel				,
			email		,
			productname	,
			amt1		,
			amt2		,
			amt3		,
			amt4		,
			type) VALUES (
			'{$ordercode}', 
			'{$tsdtime}', 
			'{$up_tr_code}', 
			'{$tax_cnum}', 
			'{$up_id_info}', 
			'{$up_name}', 
			'{$up_tel}', 
			'{$up_email}', 
			'{$up_productname}', 
			{$up_amt1}, 
			{$up_amt2}, 
			{$up_amt3}, 
			{$up_amt4}, 
			'N')";
			if(pmysql_query($sql,get_db_conn())) {
				$flagResult = "SUCCESS";
				$msg = "현금영수증 개별발급 요청이 완료되었습니다.\\n\\n현금영수증 발급/조회에서 최종적으로 발급하시면 국세청에 신고됩니다.";
			} else {
				$flagResult = "FAIL";
				$msg = "현금영수증 발급요청이 실패하였습니다.";
			}
		}else{
			$flagResult = "FAIL";
			$msg = "현금영수증 발급요청이 실패하였습니다.";
		}
		echo $flagResult."||".$msg;
		exit;
	}else if($mode=="receipt_tax"){		
		$ordercode = $_POST['ordercode'];
		if($ordercode){
			$dataOrder = pmysql_fetch("select * from tblorderinfo where ordercode='".$ordercode."'");

			$up_mem_id=$dataOrder["id"];

			$resultItem_sql = '';
			$resultItem_sql = "SELECT productname FROM tblorderproduct  ";
			$resultItem_sql.= "WHERE productcode NOT LIKE 'COU%' AND ordercode = '".$ordercode."' ";
			$resultItem_sql.= "GROUP BY ordercode, productcode, productname ";
			//$resultItem=pmysql_query("select * from tblorderproduct where productcode not like 'COU%' AND ordercode='".$ordercode."'");
			$resultItem=pmysql_query( $resultItem_sql, get_db_conn() );
			$itemCount = 0;
			$up_productname = "";
			while($rowItem=pmysql_fetch_object($resultItem)){
				if(!$up_productname) $up_productname = strcutDot($rowItem->productname, 22);
				$itemCount++;
			}

			if($itemCount > 1){
				$up_productname .= " 외 ".($itemCount-1)."건";
			}
			# 2015 12 08 유동혁 
			# price에 더이상 가격이합산되어 나오지 않아 수정함 
			$up_amt = (int)$dataOrder["price"] + (int)$dataOrder["deli_price"] - (int)$dataOrder["dc_price"] - (int)$dataOrder["reserve"];
			//$up_amt=(int)$dataOrder["price"];

			$up_company=$_POST["up_company"];

			$up_name=$_POST['up_name'];

			$up_service=$_POST['up_service'];

			$up_item=$_POST['up_item'];

			$up_address=$_POST['up_address'];

			$up_comnum1=$_POST["up_comnum1"];
			$up_comnum2=$_POST["up_comnum2"];
			$up_comnum3=$_POST["up_comnum3"];

			if($tax_rate==10) {
				$up_amt1=$up_amt;
				$up_amt4=floor(($up_amt1/1.1)*0.1);
				$up_amt2=$up_amt1-$up_amt4;
				$up_amt3=0;
			} else {
				$up_amt1=$up_amt;
				$up_amt2=0;
				$up_amt3=0;
				$up_amt4=0;
			}

			
			$up_id_info=$up_comnum1.$up_comnum2.$up_comnum3;
		


			$sql = "
						INSERT INTO tbltaxcalclist(
							ordercode, 
							mem_id, 
							name, 
							company, 
							service, 
							item, 
							busino, 
							address, 
							productname, 
							price, 
							supply, 
							surtax, 
							ip, 
							type, 
							issuedate, 
							date
						) VALUES (
							'".$ordercode."', 
							'".$up_mem_id."', 
							'".$up_name."', 
							'".$up_company."', 
							'".$up_service."', 
							'".$up_item."', 
							'".$up_id_info."', 
							'".$up_address."', 
							'".$up_productname."', 
							".$up_amt1.", 
							".$up_amt2.", 
							".$up_amt4.", 
							'".$_SERVER['REMOTE_ADDR']."', 
							0, 
							'".date('Y-m-d')."', 
							'".date('YmdHmi')."'
						)";
			if(pmysql_query($sql,get_db_conn())) {
				$flagResult = "SUCCESS";
				$msg = "세금계산서 개별발급 요청이 완료되었습니다.";
			} else {
				$flagResult = "FAIL";
				$msg = "세금계산서 발급요청이 실패하였습니다.";
			}
		}else{
			$flagResult = "FAIL";
			$msg = "세금계산서 발급요청이 실패하였습니다.";
		}
		echo $flagResult."||".$msg;
		exit;
	}
?>