<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
	include_once($Dir."lib/shopdata.php");
	include("access.php");

	$curdate		= date("YmdHis");
	$connect_ip	= $_SERVER['REMOTE_ADDR'];
	$mode		= $_POST['mode'];
	$idxs			= $_POST["idxs"];
	$arr_idxs	= explode(",", $idxs);

	$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입



	$orderproductinfo	= "";

	if($idxs) {

		if($mode == "1") {	// 해당 주문건 배송준비중 => 결제완료
			$sql = "SELECT * FROM tblorderproduct WHERE idx IN ('".str_replace(",", "','", rtrim($idxs,','))."') AND op_step='2'";
			$ret = pmysql_query($sql,get_db_conn());
			while($roword = pmysql_fetch_object($ret)) {		
				$sql0 = "UPDATE tblorderproduct SET deli_gbn='N' WHERE ordercode = '{$roword->ordercode}' AND idx = '{$roword->idx}' AND deli_gbn = 'S' AND op_step = '2'";
				if(pmysql_query($sql0,get_db_conn())) {
					// 상태변경 호출
					orderProductStepUpdate($exe_id, $roword->ordercode, $roword->idx, '1'); // 주문코드, 상품 idx, 주문상태
					if ($orderproductinfo) $orderproductinfo	.= ",";
					$orderproductinfo	.= $roword->ordercode."-".$roword->idx;
					list($cntprcodes)=pmysql_fetch("SELECT count(*) from tblorderproduct WHERE ordercode = '{$roword->ordercode}' AND deli_gbn != 'N' AND op_step != '1' AND op_step < '40' ");
					if ($cntprcodes == 0) {
						$sql1 = "UPDATE tblorderinfo SET deli_gbn='N' WHERE ordercode='{$roword->ordercode}' AND deli_gbn = 'S' ";
						pmysql_query($sql1,get_db_conn());
					}         
				}
			}  

			$log_content = "## 주문건 배송준비중 => 결제완료 처리 ## - 주문건 : ".$orderproductinfo." - 시간 : ".$curdate;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			echo "<script>alert('결제완료로 처리하였습니다.'); parent.location.reload();</script>";
			exit;

		} else if($mode == "2") {	// 해당 주문건 배송중 => 배송준비중
			
			$sql = "SELECT * FROM tblorderproduct WHERE idx IN ('".str_replace(",", "','", rtrim($idxs,','))."') AND op_step='3'";
			$ret = pmysql_query($sql,get_db_conn());
			while($roword = pmysql_fetch_object($ret)) {		
				$sql0 = "UPDATE tblorderproduct SET deli_com=NULL, deli_num=NULL, deli_gbn = 'S', deli_date=NULL ";
				$sql0.= "WHERE ordercode='{$roword->ordercode}' AND idx='{$roword->idx}' AND deli_gbn = 'Y' AND op_step = '3' ";

				if(pmysql_query($sql0,get_db_conn())) {
					// 상태변경 호출
					orderProductStepUpdate($exe_id, $roword->ordercode, $roword->idx, '2'); // 주문코드, 상품 idx, 주문상태
					if ($orderproductinfo) $orderproductinfo	.= ",";
					$orderproductinfo	.= $roword->ordercode."-".$roword->idx;
					list($cntprcodes)=pmysql_fetch("SELECT count(*) from tblorderproduct WHERE ordercode = '{$roword->ordercode}' AND deli_gbn != 'S' AND op_step != '2' AND op_step < '40' ");
					if ($cntprcodes == 0) {
						$sql1 = "UPDATE tblorderinfo SET deli_gbn = 'S', deli_date=NULL WHERE ordercode='{$roword->ordercode}' AND deli_gbn = 'Y' ";
						pmysql_query($sql1,get_db_conn());
					}         
				}
			}
			
			$log_content = "## 주문건 배송중 => 배송준비중 처리 ## - 주문건 : ".$orderproductinfo." - 시간 : ".$curdate;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			echo "<script>alert('배송준비중으로 처리하였습니다.'); parent.location.reload();</script>";
			exit;
		}
	}
?>