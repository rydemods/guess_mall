<?
// =========================================================================================
// FileName         : product_allupdate_company_indb.php
// Desc             : csv파일 업로드해서 일괄 제휴사 가격 업데이트
// By               : 
// Last Updated     : 2017.08.27
// =========================================================================================

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$file_name =  $_FILES['csv_file']['name'];

if(strstr($file_name, '.csv')){

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//echo "csv";
		//exit;
	}

	$uploaddir = $Dir . "uploads/";
	$uploadfile = $uploaddir . basename($_FILES['csv_file']['name']);

	$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

	if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadfile)) {


		// 필드 0 : 상품코드
		// 필드 1 : 브랜드
		// 필드 2 : 상품코드
		// 필드 3 : 상품명
		// 필드 4 : 품번
		// 필드 5 : 컬러
		// 필드 6 : 정가
		// 필드 7 : 판매가
		// 필드 8 : 변경될 판매가
		// 필드 9 : 변경될 제휴사 판매가1
		// 필드 10 : 변경될 제휴사 판매가2
		// 필드 11 : 변경될 제휴사 판매가3
		// 필드 12 : 변경될 제휴사 판매가4
		// 필드 13 : 변경될 제휴사 판매가5
		// 필드 14 : 변경될 제휴사 판매가6
		// 필드 15 : 변경될 제휴사 판매가7	
		// 필드 16 : 변경될 제휴사 판매가8
		// 필드 17 : 변경될 제휴사 판매가9
		// 필드 18 : 변경될 제휴사 판매가10

		$handle = fopen($uploadfile,  "r"); 

		$fieldCount = 24;

		$arrResultMsg = array();
		$tmp_arr_deli = array();
		$arr_deli_idxs = array();
		$rowCount = 0; 
		while (($data = fgetcsv($handle, 135000, ",")) !== FALSE) {
			if ( $rowCount == 0 ) {
				// 첫번째 라인은 pass

				$rowCount++;
				continue;
			}

			//echo "cnt = ".count($data)."<br>";
	 
			if ( $data && count($data) == $fieldCount || true ) {
				$productcode				=	trim($data[0]);
				$self_goods_code		=	trim($data[2]);
				$prodcode					=	trim($data[4]);
				$colorcode					=	trim($data[5]);
				$new_sellprice				=	trim($data[8]);
				$t_sql =  "modifydate = now()";
				$sale_num = 1;
				for($j=9;$j<=18;$j++) {
					if(trim($data[$j])){
						$temp = "sale_price".$sale_num;
						$sale_price	= trim($data[$j]);
						$t_sql .= " ,  ".$temp." =  '{$sale_price}' ";
					}
					$sale_num++;
				}
				//exdebug($data);
				if($productcode && $self_goods_code){

					$sellprice_dc_rate = floor((($consumerprice-$new_sellprice)/$consumerprice)*100);
					
					$flagResult = true;

					BeginTrans();
					try {
						
						// 판매가 및 할인율 업데이트
						$sql  = "UPDATE tblproduct ";
						$sql .= "SET ";
						$sql .= $t_sql;
						$sql .= "WHERE self_goods_code = '${self_goods_code}' and prodcode= '${prodcode}' and colorcode='${colorcode}'";
						$result = pmysql_query($sql, get_db_conn());
	//					echo "sql = ".$sql."<br>";
	//					echo $sql."<br>";
	//					exit;
						if ( empty($result) ) {
							throw new Exception('Insert Fail');
						}

						// 로그 남기기
						$sql  = "INSERT INTO tblproduct_sellpricechange_log ";
						$sql .= "( productcode, sellprice, consumerprice, new_sellprice, sell_dc_rate, regdt, change_id ) VALUES ";
						$sql .= "( '{$productcode}', '{$sellprice}', '{$consumerprice}', '{$self_goods_code}', '{$sellprice_dc_rate}', '" . date("YmdHis") . "', '" . $_ShopInfo->id . "' )";
						//$result = pmysql_query($sql, get_db_conn());
						//echo "sql = ".$sql."<br>";
						if ( empty($result) ) {
							throw new Exception('Insert Fail');
						}
					} catch (Exception $e) {
						$flagResult = false;
						$arrData[$self_goods_code]="실패";
						RollbackTrans();
					}

					CommitTrans();
					if(!$arrData[$self_goods_code])$arrData[$self_goods_code]="성공";


				}
			}
		}

		if ( count($arrData) > 0 ) {
			
			Header("Content-type: application/vnd.ms-excel");
			Header("Content-Disposition: attachment; filename=product_allupdate_out_".date("Ymd").".xls");
			Header("Pragma: no-cache");
			Header("Expires: 0");
			Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			Header("Content-Description: PHP4 Generated Data");

			
	?>

	<html>
	<head>  
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	</head> 
	<body>  

	<table border="1">
		<tr align="center">
			<th>상품번호</th>
			<th>결과</th>
		</tr>
	<?php 
		foreach ( $arrData as $data=>$datav ) {
			echo "<tr>";
			echo "<td>".iconv('EUC-KR', 'UTF-8', "'".$data."'")."</td>";
			echo "<td>".$datav."</td>";
			//echo "<td>{$data}</td>";
			echo "</tr>";
		}
	?>
	</table>
	</body>
	</html>

<?php
		}
	}
}else{
?>
	<html>
	<head>  
	<script>
	alert('파일 형식을(csv) 확인해 주세요');
	</script>
	<body onLoad="window.location.href='http://shinwonmall.com/admin/product_allupdate_company.php'";>
	</body>
	</html>
<?
//	$next_url = "http://shinwonmall.com/admin/product_allupdate_company.php";
//	header("LOCATION: $next_url");
}
?>
