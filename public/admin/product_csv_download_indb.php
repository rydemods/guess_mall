<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "pr-4";
	$MenuCode = "product";

	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
	#########################################################

	$mode=$_POST["mode"];
	$vender=$_POST["vender"];
	$code=$_POST["code"];

	function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"', $pos = false) {
		$str = '';
		$escape_char = '\\';
		$count = 0;
		//exdebug($pos);
		foreach ($fields as $value) {
			
			if (strpos($value, $delimiter) !== false ||
			strpos($value, $enclosure) !== false ||
			strpos($value, "\n") !== false ||
			strpos($value, "\r") !== false ||
			strpos($value, "\t") !== false ||
			strpos($value, ' ') !== false ) {
				$str2 = $enclosure;
				$escaped = 0;
				$len = strlen($value);
				for ($i=0;$i<$len;$i++) {
					if ($value[$i] == $escape_char) {
						$escaped = 1;
					} else if (!$escaped && $value[$i] == $enclosure) {
						$str2 .= $enclosure;
					} else {
						$escaped = 0;
					}
					$str2 .= $value[$i];
				}
				$str2 .= $enclosure;
				$str .= $str2.$delimiter;
			} else {
				$str .= $value.$delimiter;
			}
			/*if ($count == 21){
				exdebug($count);
				exdebug($str);
			}*/
			$count++;
		}
		$str = rtrim($str,$delimiter);
		$str .= "\n";
		return $str;
	}

	@set_time_limit(1000);

    if($vender) $qry .= "AND brand = '{$vender}' ";

	if($code>0) {
		$code_aBCD = str_pad($code, 12, "0", STR_PAD_RIGHT);

		//분류 확인
		$sql = "SELECT type FROM tblproductcode ";
		$sql.= "WHERE code_a='".substr($code_aBCD,0,3)."' AND code_b='".substr($code_aBCD,3,3)."' ";
		$sql.= "AND code_c='".substr($code_aBCD,6,3)."' AND code_d='".substr($code_aBCD,9,3)."' ";
		$result=pmysql_query($sql,get_db_conn());
		if(@pmysql_num_rows($result)!=1) {
			alert('상품을 다운로드할 분류 선택이 잘못되었습니다.');
			exit;
		}
		pmysql_free_result($result);
		$qry .= "AND b.c_category LIKE '{$code}%' ";
	}

	if(ord($qry)) {
		$qry = "WHERE".substr($qry,3);
	}

	$connect_ip = $_SERVER['REMOTE_ADDR'];
	$curdate = date("YmdHis");

	$sql = "SELECT COUNT(*) as cnt FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
	$sql.= $qry;
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	if ($row->cnt>=5000) {
		$temp = "삼일";
		$okdate = date("Ymd",strtotime('+3 day'));
	} else {
		$temp = "하루";
		$okdate=date("Ymd");
	}
	pmysql_free_result($result);

	$log_content = "## 상품 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$sql = "SELECT code_a||code_b||code_c||code_d as code, type,code_name FROM tblproductcode ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$code_name[$row->code] = $row->code_name;
	}
	pmysql_free_result($result);

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$brandname[$row->bridx] = $row->brandname;
	}
	pmysql_free_result($result);

	$patten = array ("\r\n");
	$replace = array ("");

	$sql = "SELECT 
	*,
	a.maximage as maximageurl,
	a.minimage as minimageurl,
	a.tinyimage as tinyimageurl
	FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
	$sql.= $qry;
	$sql.= " and b.c_maincate = 1";
	$sql.= " ORDER BY productcode "; 
	$result = pmysql_query($sql,get_db_conn());


/*
	debug($sql);
	while ($data=pmysql_fetch($result)) {
		$dbDataDuplArray[$data[productcode]][category][] = $data[c_category];
		
		$arrData = $codeArr = array();
		$codeArr['category1'] = substr($data[c_category],0,3);
		$codeArr['category2'] = substr($data[c_category],3,3);
		$codeArr['category3'] = substr($data[c_category],6,3);
		$codeArr['category4'] = substr($data[c_category],9,3);

		foreach ( $fields as $key => $arr ) {
			if ( $arr['down'] == 'Y' && $arr['type'] == 'PD' ) {
				$data[ $key ] = iconv( 'utf-8', 'euc-kr', $data[ $key ]);
				//$data[ $key ] = $data[ $key ];
				if ( $key == 'content' ) {
					//$data[ $key ] = str_replace($patten, $replace, $data[ $key ]);
					$data[ $key ] = str_replace($patten, $replace, $data[ $key ]);
					//debug($data[ $key ]);
				}else if($key == 'option1' || $key == 'option2' || $key == 'option1_tf' || $key == 'option2_tf' || $key == 'option2_maxlen'){
					$data[ $key ] = str_replace("@#", "||", $data[ $key ]);
				}else if($key == 'category1' || $key == 'category2' || $key == 'category3' || $key == 'category4'){
					$data[ $key ] = "'".$codeArr[ $key ];
				}else if($key == 'vender'){
					list($in_vender)=pmysql_fetch("SELECT id FROM tblvenderinfo WHERE vender = '".$data[ $key ]."'");
					$data[ $key ] = $in_vender;
				}

				$arrData[] = $data[ $key ];
			}
		}
		debug($arrData);
		//fputcsv($fp, $arrData);
		//echo getcsvdata($arrData,',','"',true);
	}
	debug(implode("||", $dbDataDuplArray[$data[productcode]][category]));

		//debug("--------------------");
		//debug($dbDataDuplArray);
	exit;
*/


	if($mode=="download") {
		Header("Content-Disposition: attachment; filename=product_".date("Ymd").".csv");
		header('Content-Type: application/csv');
		header("Content-Description: PHP4 Generated Data" );

		$fields = parse_ini_file("./product_csv_download_conf.ini", true);

		$fp = fopen('php://temp', 'w+');

		$arrField = $arrColumn = array();
		foreach ( $fields as $key => $arr ){
			if ( $arr['down'] == 'Y' && $arr['type'] == 'PD' ) $arrField[] = iconv( 'utf-8', 'euc-kr', $arr['text']);
		}

		foreach ( $fields as $key => $arr ){
			//echo $key."<br>";
			$key	= $key=='op_self_goods_code'?'self_goods_code':$key;
			if ( $arr['down'] == 'Y' && $arr['type'] == 'PD')  $arrColumn[] = iconv( 'utf-8', 'euc-kr', $key);
		}

		fputcsv($fp, $arrField);
		fputcsv($fp, $arrColumn);

		//echo getcsvdata($arrField);
		//echo getcsvdata($arrColumn);

		//exit;

		
		while ($data=pmysql_fetch($result)) {
			$arrData = $codeArr = $dbDataDuplArray = array();
			$checkquantity = "";
			$checkquantityKeyForeach = $checkquantityKey = 0;

			/*
			$codeArr['category1'] = substr($data[c_category],0,3);
			$codeArr['category2'] = substr($data[c_category],3,3);
			$codeArr['category3'] = substr($data[c_category],6,3);
			$codeArr['category4'] = substr($data[c_category],9,3);
			*/

			$resultCate = pmysql_query("SELECT * FROM tblproductlink WHERE c_productcode = '".$data[productcode]."'",get_db_conn());
			while ($dataCate=pmysql_fetch($resultCate)) {
				$dbDataDuplArray[category][] = $dataCate[c_category];
			}
			$moreCategory = implode("||", $dbDataDuplArray[category]);
			if($moreCategory){
				$codeArr['category'] = $moreCategory;
			}else{
				$codeArr['category'] = $data[c_category];
			}


			foreach ( $fields as $key => $arr ) {
				if ( $arr['down'] == 'Y' && $arr['type'] == 'PD' ) {
					$data[ $key ] = iconv( 'utf-8', 'euc-kr', $data[ $key ]);
					//$data[ $key ] = $data[ $key ];
					if ( $key == 'content' ) {
						//$data[ $key ] = str_replace($patten, $replace, $data[ $key ]);
						$data[ $key ] = str_replace($patten, $replace, $data[ $key ]);
						//debug($data[ $key ]);
					}else if($key == 'option1' || $key == 'option2' || $key == 'option1_tf' || $key == 'option2_tf' || $key == 'option2_maxlen'){
						$data[ $key ] = str_replace("@#", "||", $data[ $key ]);
					//}else if($key == 'category1' || $key == 'category2' || $key == 'category3' || $key == 'category4'){
					}else if($key == 'productcode'){
						$data[ $key ] = "'".$data[ $key ];
					}else if($key == 'category'){
						$data[ $key ] = "'".$codeArr[ $key ];
					}else if($key == 'vender'){
						list($in_vender)=pmysql_fetch("SELECT id FROM tblvenderinfo WHERE vender = '".$data[ $key ]."'");
						$data[ $key ] = $in_vender;
					}else if($key == 'primg01' || $key == 'primg02' || $key == 'primg03' || $key == 'primg04' || $key == 'primg05' || $key == 'primg06' || $key == 'primg07' || $key == 'primg08' || $key == 'primg09' || $key == 'primg10'){
						$dataMul=pmysql_fetch("SELECT * FROM tblmultiimages WHERE productcode='".$data[productcode]."'");
						$data[ $key ] = $dataMul[ $key ];
					}else if($key == 'quantity'){
						if($data[ $key ]=="999999999"){
							$checkquantity="F";
						}else if($data[ $key ]=="0"){
							$checkquantity="E";
						}else if($data[ $key ]=="-9999"){
							$checkquantity="A";
						}else{
							$checkquantity="C";
						}
					}else if($key == 'checkquantity'){
						$checkquantityKey = $checkquantityKeyForeach;
					}else if($key == 'op_self_goods_code'){
						$key	= 'self_goods_code';
						$data[ $key ] = $data[ $key ]?"'".$data[ $key ]:$data[ $key ];
					}else if($key == 'option_type'){
						$data[ $key ] = trim($data[ $key ])!=''?$data[ $key ]:'2';
					}else if($key == 'prodcode'){
						$data[ $key ] = "'".$data[ $key ];
					}else if($key == 'colorcode'){
						$data[ $key ] = "'".$data[ $key ];
					}
					$checkquantityKeyForeach++;




					$arrData[] = $data[ $key ];
				}
			}
			$arrData[$checkquantityKey] = $checkquantity;

			fputcsv($fp, $arrData);
		}

		rewind($fp);
		$csv_contents = stream_get_contents($fp);
		fclose($fp);

		echo $csv_contents;



		flush();
		exit;
	}else if($mode=="download_opt"){
		$curdate = date("YmdHis");
		$connect_ip = $_SERVER['REMOTE_ADDR'];
		$log_content = "## 상품 옵션 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);


		Header("Content-Disposition: attachment; filename=product_opt_".date("Ymd").".csv");
		header('Content-Type: application/csv');
		header("Content-Description: PHP4 Generated Data" );


		$fields = parse_ini_file("./product_csv_download_conf.ini", true);

		$fp = fopen('php://temp', 'w+');

		$arrField = $arrColumn = array();
		foreach ( $fields as $key => $arr ){
			if ( $arr['down'] == 'Y' && $arr['type'] == 'OPT' ) $arrField[] = iconv( 'utf-8', 'euc-kr', $arr['text']);
		}

		foreach ( $fields as $key => $arr ){
			if($key == "productcode_opt") $key = "productcode";
			if($key == "prodcode_opt") $key = "prodcode";
			if($key == "colorcode_opt") $key = "colorcode";
			if ( $arr['down'] == 'Y' && $arr['type'] == 'OPT')  $arrColumn[] = iconv( 'utf-8', 'euc-kr', $key);
		}

		fputcsv($fp, $arrField);
		fputcsv($fp, $arrColumn);
		
		while ($data=pmysql_fetch($result)) {


			$sql_o = "SELECT * FROM tblproduct_option where productcode = '".$data['productcode']."' ";
			$result_o = pmysql_query($sql_o, get_db_conn());
			while ($data_o=pmysql_fetch($result_o)) {
				$arrData = $codeArr = array();

				foreach ( $fields as $key => $arr ) {
					if($key == "productcode_opt") $key = "productcode";
					if($key == "prodcode_opt") $key = "prodcode";
					if($key == "colorcode_opt") $key = "colorcode";

					if ( $arr['down'] == 'Y' && $arr['type'] == 'OPT' ) {
						$data[ $key ] = iconv( 'utf-8', 'euc-kr', $data[ $key ]);
						$data_o[ $key ] = iconv( 'utf-8', 'euc-kr', $data_o[ $key ]);

                         if($key == 'productcode') $data_o[ $key ] = "'".$data_o[ $key ];

						if($key == "option_code") $data_o[ $key ] = str_replace(chr(30), "||", $data_o[ $key ]);

						 if($key == 'prodcode') $data_o[ $key ] = "'".$data[ $key ];

						 if($key == 'colorcode') $data_o[ $key ] = "'".$data[ $key ];

						$arrData[] = $data_o[ $key ];
					}
				}
/*
				if($data['option_type'] == '0'){
					debug("A");
					debug($arrData);
				}else if($data['option_type'] == '1'){
					debug("B");
					debug($arrData);
				}*/
				fputcsv($fp, $arrData);
			}
		}

		rewind($fp);
		$csv_contents = stream_get_contents($fp);
		fclose($fp);

		//debug($arrData);

		echo $csv_contents;

		flush();
		exit;


	}
?>



<!--
<html>
	<head>
	<title>list</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>.text{mso-number-format:"\@";}</style>
	</head>

	<body>
		<table border="1">
			<tr>
				<?
					foreach ( $fields as $key => $arr ){
						if ( $arr['down'] == 'Y' && $arr['type'] == 'PD' ) echo '<td>' . $arr['text'] . '</td>';
					}
			?>
			</tr>

			<tr>
				<?
					foreach ( $fields as $key => $arr ){
						if ( $arr['down'] == 'Y' && $arr['type'] == 'PD') echo '<td>' . $key . '</td>';
					}
				?>
			</tr>
			
			<?
				while ($data=pmysql_fetch($result)) {
					echo '<tr>';
					foreach ( $fields as $key => $arr ) {
						if ( $key == 'content' ) {
							//$data[ $key ] = str_replace($patten, $replace, $data[ $key ]);
							//$data[ $key ] = htmlspecialchars( $data[ $key ] );
							//$data[ $key ] = strlen($data[ $key ]);
							$data[ $key ] = htmlspecialchars( $data[ $key ] );
						}else if($key == 'option1' || $key == 'option2' || $key == 'option1_tf' || $key == 'option2_tf' || $key == 'option2_maxlen'){
							$data[ $key ] = str_replace("@#", "||", $data[ $key ]);
						}

						if ( in_array( $key, array( 'content222') ) ) echo '<td>' . $data[ $key ] . '</td>';
						else echo '<td class="text">' . $data[ $key ] . '</td>';
					}
					echo '</tr>';
				}
			?>
		</table>
	</body>
</html>
-->
