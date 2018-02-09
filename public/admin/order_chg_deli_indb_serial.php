<?php
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


$ordcode=$_GET['ordcode'];
$ordcode=substr($ordcode,0,-1);
$ordcode_chk=str_replace(",","','",$ordcode);
$ordcode_arr=explode(",",$ordcode);

$cKey = $_POST['ordercode'];
$vKey = $_POST['productcode'];

		$sql = "UPDATE tblorderproduct SET ";

			if( $_POST[product_serial][$cKey][$vKey] ){	// 배열여부 확인
					$sql.= " product_serial ='";
					foreach( $_POST[product_serial][$cKey][$vKey] as $vv=>$kk){
					
						if( count($_POST[product_serial][$cKey][$vKey]) > 1){				// 배열 index 여러개
							$sql.= $kk."|"; 
						}else if (count($_POST[product_serial][$cKey][$vKey]) == 1){		// 배열에 index 1개
							$sql.= $kk ;
						}						
					}
					$sql.= "' ";
					$sql.="WHERE ordercode='".$cKey."' AND productcode='".$vKey."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			}					
				//exdebug($sql);  exit;
				pmysql_query($sql,get_db_conn());
				//echo "<script> alert('수정되었습니다');top.window.close('about:blank','test_pop'); </script>";
				echo "<script> alert('수정되었습니다'); window.close(); </script>";


?>
