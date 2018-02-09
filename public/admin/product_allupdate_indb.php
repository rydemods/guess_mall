<?
// =========================================================================================
// FileName         : dvcode_all_indb.php
// Desc             : csv파일 업로드해서 일괄 송장정보 업데이트
// By               : moondding2
// Last Updated     : 2016.03.13
// =========================================================================================

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$uploaddir = $Dir . "uploads/";
$uploadfile = $uploaddir . basename($_FILES['csv_file']['name']);

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadfile)) {


    // 필드 0 : 상품코드
    // 필드 1 : 브랜드
	// 필드 3 : 상품코드
    // 필드 4 : 상품명
    // 필드 5 : 품번
    // 필드 6 : 컬러
    // 필드 7 : 정가
    // 필드 8 : 판매가
    // 필드 9 : 변경될 판매가

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
            $productcode            = trim($data[0]);
			$consumerprice		= trim($data[6]);
			$sellprice		= trim($data[7]);
            $new_sellprice		= trim($data[8]);
			//exdebug($data);
			if($productcode && $new_sellprice){

				$sellprice_dc_rate = floor((($consumerprice-$new_sellprice)/$consumerprice)*100);
				
				$flagResult = true;
				
				BeginTrans();
				try {
					
					// 판매가 및 할인율 업데이트
					$sql  = "UPDATE tblproduct ";
					$sql .= "SET sellprice = '{$new_sellprice}', sellprice_dc_rate = '{$sellprice_dc_rate}', modifydate = now() ";
					$sql .= "WHERE productcode = '${productcode}'";
					$result = pmysql_query($sql, get_db_conn());
					//echo "sql = ".$sql."<br>";
					if ( empty($result) ) {
						throw new Exception('Insert Fail');
					}
					

					// 로그 남기기
					$sql  = "INSERT INTO tblproduct_sellpricechange_log ";
					$sql .= "( productcode, sellprice, consumerprice, new_sellprice, sell_dc_rate, regdt, change_id ) VALUES ";
					$sql .= "( '{$productcode}', '{$sellprice}', '{$consumerprice}', '{$new_sellprice}', '{$sellprice_dc_rate}', '" . date("YmdHis") . "', '" . $_ShopInfo->id . "' )";
					$result = pmysql_query($sql, get_db_conn());
					//echo "sql = ".$sql."<br>";
					if ( empty($result) ) {
						throw new Exception('Insert Fail');
					}
				} catch (Exception $e) {
					$flagResult = false;
					$arrData[$productcode]="실패";
					RollbackTrans();
				}
				CommitTrans();
				if(!$arrData[$productcode])$arrData[$productcode]="성공";


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
?>
