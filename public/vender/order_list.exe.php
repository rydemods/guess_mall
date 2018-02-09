<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode			= $_POST["mode"];
$oc_no			= explode(",", $_POST['oc_no']);
$ordercode		= explode(",", $_POST['ordercode']);
$idxs				= explode(",", $_POST['idxs']);

$exe_id		= $_VenderInfo->getId()."||vender";	// 실행자 아이디|이름|타입

if ($mode == 'regoods' || $mode == 'rechange') {
	if ($mode == 'regoods') { // 반품처리
		$deli_gbn	= "E";
		$canmess="반품수거 처리가 되었습니다.";
	} else if ($mode == 'rechange') { // 교환처리
		$deli_gbn	= "C";
		$canmess="교환처리 되었습니다.";
	}

	for ($i=0;$i < count($oc_no);$i++) {
		if ($mode == 'regoods') { // 반품처리
			orderCancelPickupFin($exe_id, $ordercode[$i], $idxs[$i], $oc_no[$i] );
		} else if ($mode == 'rechange') { // 교환처리
			orderCancelReorderFin($exe_id, $ordercode[$i], $idxs[$i], $oc_no[$i] );
		}

		$sql = "UPDATE tblorderproduct SET deli_gbn='".$deli_gbn."' ";
		$sql.= "WHERE ordercode='".trim($ordercode[$i])."' ";
		$sql.= "AND idx IN ('".str_replace("|", "','", $idxs[$i])."') ";
		//echo $sql;

		if(pmysql_query($sql,get_db_conn())) {
			// 반품처리시 - 반송완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
			// 교환처리시 - 취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
			list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode[$i])."' AND idx NOT IN ('".str_replace("|", "','", $idxs[$i])."') AND deli_gbn != '".$deli_gbn."' AND op_step != '44'"));
			if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 반송완료(반품처리시)/취소완료(교환처리시)상태일 경우
				$sql = "UPDATE tblorderinfo SET deli_gbn='".$deli_gbn."' WHERE ordercode='".trim($ordercode[$i])."' ";
				//echo $sql;
				pmysql_query($sql,get_db_conn());
			}
		}
	}
} else if ($mode == 'step2change') { // 배송 준비중으로 변경
	
	for ($i=0;$i < count($ordercode);$i++) {
		$op_idx	= explode("|", $idxs[$i]);
		for ($k=0;$k < count($op_idx);$k++) {
			//현재 주문의 상태값을 가져온다.
			list($old_step, $op_deli_gbn)=pmysql_fetch_array(pmysql_query("select op_step, deli_gbn from tblorderproduct WHERE ordercode='".trim($ordercode[$i])."' AND idx='".$op_idx[$k]."' "));
			if ($old_step == '1' && $op_deli_gbn == 'N') {

				//각각의 주문상품을 배송 준비중으로 변경한다.
				$sql = "UPDATE tblorderproduct SET deli_gbn='S' ";
				$sql.= "WHERE ordercode='".$ordercode[$i]."' AND op_step = '1' AND deli_gbn='N' ";
				$sql.= "AND idx='".$op_idx[$k]."' ";
				
				if(pmysql_query($sql,get_db_conn())) {
					// 신규상태 변경 
					orderProductStepUpdate($exe_id, $ordercode[$i], $op_idx[$k], '2'); // 배송 준비중

					//현재 주문의 상태값을 가져온다.
					list($old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".trim($ordercode[$i])."'"));				
					if ($old_step1 == '1' && $old_step2 == '0') {
						//주문을 배송 준비중으로 변경한다.
						$sql2 = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='".$ordercode[$i]."' AND oi_step1 = '1' AND oi_step2 = '0' AND deli_gbn='N' ";
						pmysql_query($sql2,get_db_conn());

						// 신규상태 변경
						orderStepUpdate($exe_id, $ordercode[$i], '2', '0'); // 배송 준비중
					}
				}
			}
		}
	}
	$canmess="배송 준비중으로 처리되었습니다.\\n(처리여부가 결제완료 상태가 아닌 경우에는 제외됩니다.)";
}
	echo "<html></head><body onload=\"alert('".$canmess."');parent.location.reload();\"></body></html>";exit;
?>