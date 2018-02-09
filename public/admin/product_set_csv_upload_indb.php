<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	//setlocale(LC_CTYPE, 'ko_KR.UTF-8');
	//setlocale(LC_CTYPE, 'ko_KR.eucKR'); 

    $CurrentTime = time();
    header("Content-type: application/vnd.ms-excel");
    Header("Content-Disposition: attachment; filename=upload_result_".date("YmdHis").".xls"); 
    Header("Pragma: no-cache"); 
    Header("Expires: 0");
    Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    Header("Content-Description: PHP4 Generated Data");

	echo '
		<html>
		<head>
		<title>list</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
		</head>
		<body>
		<table border="1">
		';
	print '<tr>';
	print '<td>No.</td>';
	print '<td>상품명</td>';
	print '<td>상품코드</td>';
	print '<td>등록여부</td>';
	print '</tr>';


	####################### 페이지 접근권한 check ###############
	$PageCode = "pr-2";
	$MenuCode = "product";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}


	if($_FILES['upfile_pd'][tmp_name]){
		$fp = fopen( $_FILES['upfile_pd'][tmp_name], 'r' );
		$fields = fgetcsv( $fp, 135000, ',' );
		$fields = fgetcsv( $fp, 135000, ',' );
		$fieldLen = count( $fields );
		$FieldNm = Array();

		for ( $i = 0; $i < $fieldLen; $i++ ){
			if ( $fields[$i] <> '' ){
				$FieldNm[$fields[$i]] = $i;
			}
		}

		$MSG_RESULT = "";
		$MSG_SPLIT = "||||||";
		$arrOptType = $arrCheckQuantity = $arrInsertResultExcel = array();
		while ( $data = fgetcsv( $fp, 135000, ',' ) ){
			# 변수 생성 및 한글 처리
			foreach($FieldNm as $column => $idx){
				${$column} = mb_convert_encoding($data[$FieldNm[$column]], "UTF-8", "EUC-KR");
			}

            // category, prodcode, colorcode는 앞에 0일 올수도 있어서 ' 붙이기로 했음. 제거작업 추가하자.
			$code = "001004002005";
			$productname = str_replace("'", "", trim($productname));
			$join_prodcode = str_replace("'", "", trim($join_prodcode));
            list($in_productcode) = pmysql_fetch("Select productcode From tblproduct Where prodcode = '".$join_prodcode."' ");
			if(!$in_productcode) {				

				if($in_productcode == "") {
					$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct WHERE productcode LIKE '001004002005%' LIMIT 1";
					//exdebug($sql);
					$result = pmysql_query($sql,get_db_conn());
					if ($rows = pmysql_fetch_object($result)) {
						if (strlen($rows->maxproductcode)==18) {
							$in_productcode = ((int)$rows->maxproductcode)+1;
							$in_productcode = str_pad($in_productcode, 18, '0', STR_PAD_LEFT);
						} else if($rows->maxproductcode==NULL){
							$in_productcode = $code."000001";
						} 
						pmysql_free_result($result);
					} else {
						$in_productcode = $code."000001";
					}
				}
            }

			$arrInsertResultExcel[$join_prodcode]['productcode'] = $join_prodcode;
			$arrInsertResultExcel[$join_prodcode]['productname'] = $productname;
			$arrInsertResultExcel[$join_prodcode]['code'] = $code;
			$arrInsertResultExcel[$join_prodcode]['color_code'] = $color_code;
			$arrInsertResultExcel[$join_prodcode]['flag'] = "F";

			$arr_join_prodcode = explode("_", $join_prodcode);
			$jp_product_productname	= "";
			$jp_product_cnt	= 0;
			$jp_product_qty	= 9999999;
			$jp_product_sellprice	= 0;
			$jp_product_consumerprice	= 0;
			$jp_product_productcode		= "";
			$jp_product_colorcode		= "";
			$jp_product_tag_style_no		= "";
			$jp_product_season_year	= "";
			$jp_product_season		= "";

			//exdebug($arr_join_prodcode);
			foreach($arr_join_prodcode as $ajp_k => $ajp_v){    
				$jp_sql	= "
					Select 
					productcode, 
					production, 
					model, 
					brand, 
					vender, 
					brandcd, 
					brandcdnm, 
					sellprice, 
					consumerprice, 
					quantity, 
					productname, 
					prodcode, 
					colorcode,  
					tag_style_no,  
					season_year,  
					season  
					From tblproduct Where join_yn != 'Y' AND tag_style_no LIKE '".$ajp_v."%' ORDER BY pridx DESC LIMIT 1";
				list(
					$jp_productcode, 
					$jp_production, 
					$jp_model, 
					$jp_brand, 
					$jp_vender, 
					$jp_brandcd, 
					$jp_brandcdnm, 
					$jp_sellprice, 
					$jp_consumerprice, 
					$jp_quantity, 
					$jp_productname, 
					$jp_prodcode, 
					$jp_colorcode,  
					$jp_tag_style_no,  
					$jp_season_year,  
					$jp_season
				) = pmysql_fetch($jp_sql);
				//exdebug($jp_sql);
				//exit;

				if ($jp_productname == '-') {
					list($jp_productname) = pmysql_fetch("Select productname From tblproduct Where join_yn != 'Y' AND tag_style_no LIKE '".$ajp_v."%' ORDER BY pridx ASC LIMIT 1 ");
				}

				if($jp_quantity < $jp_product_qty) $jp_product_qty	= $jp_quantity;

				if ($jp_production) {
					$jp_product_productname	.= $jp_product_productname?"|".$jp_productname:$jp_productname;
					//exdebug($jp_product_productname);
					$jp_product_sellprice	+= $jp_sellprice;
					$jp_product_consumerprice	+= $jp_consumerprice;


					$jp_product_productcode	.=  $jp_product_productcode?"|".$jp_productcode:$jp_productcode;
					$jp_product_colorcode	= $jp_colorcode;
					$jp_product_tag_style_no	.= $jp_product_tag_style_no?"_".$jp_tag_style_no:$jp_tag_style_no;
					$jp_product_season_year	= $jp_season_year;
					$jp_product_season	= $jp_season;
					$jp_product_cnt++;
				}
			}

			list($jp_product_color_code) = pmysql_fetch("Select maincolor_name From tblproduct_color_erp Where colorcode = '".$jp_product_colorcode."'");
			//echo $jp_product_cnt;
            
			if($jp_product_cnt > 1) {				
				
				$sql = "
						WITH upsert as (
							update  tblproduct 
							set 	
									productname = '$productname', 
									sellprice = '$jp_product_sellprice', 
									consumerprice = '$jp_product_consumerprice', 
									color_code = '$jp_product_color_code', 
									prodcode = '$join_prodcode', 
									colorcode = '$jp_product_colorcode', 
									tag_style_no = '$jp_product_tag_style_no', 
									season_year = '$jp_product_season_year', 
									season = '$jp_product_season', 
									quantity = '$jp_product_qty', 
									join_productcode = '".$jp_product_productcode."^".$jp_product_productname."', 
									modifydate = now()
							where	productcode = '".$in_productcode."' 
							RETURNING * 
						)
						insert into tblproduct 
						(
						productcode, 
						brand, 
						vender, 
						productname, 
						sellprice, 
						consumerprice, 
						production, 
						model, 
						color_code, 
						quantity, 
						display, 
						date, 
						regdate, 
						modifydate, 
						option1_tf, 
						self_goods_code, 
						prodcode, 
						colorcode, 
						sizecd, 
						brandcd, 
						brandcdnm, 
						tag_style_no, 
						season_year, 
						season, 
						sellprice_dc_rate,
						mixrate,
						join_yn,
						join_productcode
						)
						Select  
						'$in_productcode', 
						'$jp_brand', 
						'$jp_vender', 
						'$productname', 
						'$jp_product_sellprice', 
						'$jp_product_consumerprice', 
						'$jp_production', 
						'$jp_model', 
						'$jp_product_color_code', 
						'$jp_product_qty', 
						'R', 
						'".date("YmdHis")."', 
						now(), 
						now(), 
						'', 
						'', 
						'$join_prodcode', 
						'$jp_product_colorcode', 
						'', 
						'$jp_brandcd', 
						'$jp_production', 
						'$jp_product_tag_style_no', 
						'$jp_product_season_year', 
						'$jp_product_season', 
						'0',
						'',
						'Y',
						'".$jp_product_productcode."^".$jp_product_productname."'
						WHERE NOT EXISTS ( select * from upsert ) 
						";
				//exdebug($sql);
				//exit;
				
				if($insert = pmysql_query($sql,get_db_conn())) {
					$arrInsertResultExcel[$join_prodcode]['flag'] = "T";

					$date1=date("Ym");
					$date=date("dHis");				

					## || 로 구분해서 여러개 입력
					$arrCode = explode("||", $code);
					foreach($arrCode as $vIndex => $vCode){

						list($code_cnt) = pmysql_fetch("Select count(*) as cnt From tblproductlink where c_productcode = '".$in_productcode."' and c_category = '".$vCode."'");

						$mainCate = 0;
						if($vIndex == 0) $mainCate = 1;				
						$query = "
								WITH upsert_cate as (
									update  tblproductlink 
									set 	
											c_category = '".$vCode."',
											c_date = '".$date1.$date."', 
											c_date_1 = '".$date1.$date."', 
											c_date_2 = '".$date1.$date."', 
											c_date_3 = '".$date1.$date."', 
											c_date_4 = '".$date1.$date."'
									where	c_productcode = '".$in_productcode."'
									and c_maincate ='".$mainCate."' 
									RETURNING * 
								)	
								INSERT INTO tblproductlink
								(c_productcode, c_category, c_maincate, c_date, c_date_1, c_date_2, c_date_3, c_date_4 ) 
								Select  '".$in_productcode."', '".$vCode."', '".$mainCate."', '".$date1.$date."', '".$date1.$date."', '".$date1.$date."', '".$date1.$date."', '".$date1.$date."'
								WHERE NOT EXISTS ( select * from upsert_cate ) ";

						pmysql_query($query);
						//exdebug($query);
					}
				}else{
					$arrInsertResultExcel[$join_prodcode]['flag'] = "F";
				}
				
			} else {
				$arrInsertResultExcel[$join_prodcode]['productcode'] = "";
			}
		}

		fclose($fp);
	}





	if($arrInsertResultExcel){
		$excelCount = 1;
		foreach($arrInsertResultExcel as $join_prodcode => $value){
			if($join_prodcode){
				print '<tr>';
				print '<td>'.$excelCount.'</td>';
				print '<td>\''.$value['productname'].'</td>';
				print '<td>\''.$value['productcode'].'</td>';

				if($value['flag'] == "T"){
					print '<td>성공</td>';
				}else if($value['flag'] == "F"){
					print "<td>실패</td>";
				}else{
					print '<td></td>';
				}

				print '</tr>';

				$excelCount++;
			}
		}
	}else{
		print '<tr>';
		print '<td colspan = "6">데이터가 없습니다.</td>';
		print '</tr>';
	}

	echo '
		</table>
		</body>
		</html>
		';
	exit;
?>