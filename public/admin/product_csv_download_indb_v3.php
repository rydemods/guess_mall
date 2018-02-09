<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	@set_time_limit(1000);

	$mode			= $_POST["mode"];
	$excel_sql		= $_POST["excel_sql"];
	$est				= $_POST["est"];   
	$connect_ip	= $_SERVER['REMOTE_ADDR'];
	$curdate		= date("YmdHis");

	if($mode=="download") {
		$csv_filename	= "product_".$curdate.".csv";
		$log_content		= "## 상품 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
		$fields_type		= 'PD';
	}else if($mode=="download_opt"){
		$csv_filename	= "product_opt_".$curdate.".csv";
		$log_content		= "## 상품 옵션 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
		$fields_type		= 'OPT';
	}

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$brandname[$row->bridx] = $row->brandname;
	}
	pmysql_free_result($result);

	$patten = array ("\r\n");
	$replace = array ("");

	$result = pmysql_query($excel_sql,get_db_conn());

	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	Header("Content-Disposition: attachment; filename={$csv_filename}");
	header('Content-Type: application/csv');
	header("Content-Description: PHP4 Generated Data" );

	$fields = parse_ini_file("./product_csv_download_conf.ini", true);

	$fp = fopen('php://temp', 'w+');

	
	$arrField = $arrColumn = array();
	foreach ( $est as $key => $val ) {
		if ( $fields[$val] !='' && $fields[$val]['down'] == 'Y' && $fields[$val]['type'] == $fields_type ) {
			$arrField[]		= iconv( 'utf-8', 'euc-kr', $fields[$val]['text']);
			if($mode=="download")
				$val	= $val=='op_self_goods_code'?'self_goods_code':$val;
			if($mode=="download_opt")
				$val	= $val=='productcode_opt'?'productcode':$val;
				$val	= $val=='prodcode_opt'?'prodcode':$val;
				$val	= $val=='colorcode_opt'?'colorcode':$val;
			$arrColumn[] = iconv( 'utf-8', 'euc-kr', $val);
		}
	}

	fputcsv($fp, $arrField);
	fputcsv($fp, $arrColumn);

	while ($data=pmysql_fetch($result)) {
		if($mode=="download") {
			$arrData = $codeArr = $dbDataDuplArray = array();
			$productcode=$data[productcode];

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
			/*foreach ( $est as $key => $val ) {
				if ( $fields[$val]['down'] == 'Y' && $fields[$val]['type'] == $fields_type ) {
					exdebug($data[ $val ]);
				}
			}*/


			foreach ( $est as $key => $val ) {
				if ( $fields[$val]['down'] == 'Y' && $fields[$val]['type'] == $fields_type ) {
					$data[ $val ] = iconv( 'utf-8', 'euc-kr', $data[ $val ]);
					if ( $val == 'content' ) {
						$data[ $val ] = str_replace($patten, $replace, $data[ $val ]);
					}else if($val == 'option1' || $val == 'option2' || $val == 'option1_tf' || $val == 'option2_tf' || $val == 'option2_maxlen'){
						$data[ $val ] = str_replace("@#", "||", $data[ $val ]);
					}else if($val == 'productname'){
						$data[ $val ] = str_replace("<BR>"," ",str_replace("<br>"," ",$data[ $val ]));
					}else if($val == 'productcode'){
						$data[ $val ] = "'".$data[ $val ];						
					}else if($val == 'category'){
						$data[ $val ] = "'".$codeArr[ $val ];
					}else if($val == 'vender'){
						list($in_vender)=pmysql_fetch("SELECT id FROM tblvenderinfo WHERE vender = '".$data[ $val ]."'");
						$data[ $val ] = $in_vender;
					}else if($val == 'primg01' || $val == 'primg02' || $val == 'primg03' || $val == 'primg04' || $val == 'primg05' || $val == 'primg06' || $val == 'primg07' || $val == 'primg08' || $val == 'primg09' || $val == 'primg10'){
						$dataMul=pmysql_fetch("SELECT * FROM tblmultiimages WHERE productcode='".$productcode."'");
						$data[ $val ] = $dataMul[ $val ];
					}else if($val == 'checkquantity'){
						if($data[ 'quantity' ]=="999999999"){
							$data[ $val ] = "F";
						}else if($data[ 'quantity' ]=="0"){
							$data[ $val ] = "E";
						}else if($data[ 'quantity' ]=="-9999"){
							$data[ $val ] = "A";
						}else{
							$data[ $val ] = "C";
						}
					}else if($val == 'op_self_goods_code'){
						$val	= 'self_goods_code';
						$data[ $val ] = $data[ $val ]?"'".$data[ $val ]:$data[ $val ];
					}else if($val == 'option_type'){
						$data[ $val ] = trim($data[ $val ])!=''?$data[ $val ]:'2';
					}else if($val == 'prodcode'){
						$data[ $val ] = "'".$data[ $val ];						
					}else if($val == 'colorcode'){
						$data[ $val ] = "'".$data[ $val ];						
					}




					$arrData[] = $data[ $val ];
					//exdebug($arrData);
				}
			}
			fputcsv($fp, $arrData);
		}else if($mode=="download_opt"){
			$sql_o = "SELECT * FROM tblproduct_option where productcode = '".$data['productcode']."' ";
			$result_o = pmysql_query($sql_o, get_db_conn());
			while ($data_o=pmysql_fetch($result_o)) {
				$arrData = $codeArr = array();

				foreach ( $est as $key => $val ) {

					if ( $fields[$val]['down'] == 'Y' && $fields[$val]['type'] == 'OPT' ) {
						$val	= $val=='productcode_opt'?'productcode':$val;
						$val	= $val=='prodcode_opt'?'prodcode':$val;
						$val	= $val=='colorcode_opt'?'colorcode':$val;
						$data[ $val ] = iconv( 'utf-8', 'euc-kr', $data[ $val ]);
						$data_o[ $val ] = iconv( 'utf-8', 'euc-kr', $data_o[ $val ]);

						 if($val == 'productcode') $data_o[ $val ] = "'".$data_o[ $val ];

						if($val == "option_code") $data_o[ $val ] = str_replace(chr(30), "||", $data_o[ $val ]);

						 if($val == 'prodcode') $data_o[ $val ] = "'".$data[ $val ];

						 if($val == 'colorcode') $data_o[ $val ] = "'".$data[ $val ];

						$arrData[] = $data_o[ $val ];
					}
				}
				fputcsv($fp, $arrData);
			}
		}
	}

	rewind($fp);
	$csv_contents = stream_get_contents($fp);
	fclose($fp);

	echo $csv_contents;



	flush();
	exit;
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
