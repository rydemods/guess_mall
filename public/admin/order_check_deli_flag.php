<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");

$suc_cnt = 0;
$err_cnt = 0;
if(count($_POST['query_idx']) > 0){
	$arrIdx = implode("','", array_filter(explode(",", $_POST['query_idx'])));
	# 선택한 아이템중 배송주체 변경이 가능한 상품만 조회해서 처리
	$selItemRes = pmysql_query("SELECT idx, ordercode, store_code FROM tblorderproduct WHERE idx in ('".$arrIdx."') AND (delivery_type = '0' OR delivery_type = '2') AND op_step in ('1','2')");
	while ($selItemRow=pmysql_fetch_object($selItemRes)) {
		$ordercode = $selItemRow->ordercode;
		$idxs = $selItemRow->idx;
		$null_storecode="";

		list($store_code)=pmysql_fetch("select store_code from tblorderproduct where ordercode='".$ordercode."' and idx='".$idxs."'");

		if($store_code==$sync_bon_code) $null_storecode="store_code='', ";
		$Sync = new Sync();
		$sync_idx = $idxs;
		$arrayDatax=array('ordercode'=>$ordercode,'sync_idx'=>'AND idx='.$sync_idx);
		$sql = "UPDATE tblorderproduct SET ".$null_storecode."delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$idxs} ";
		pmysql_query($sql, get_db_conn());

		$sql = "INSERT INTO tblorderproduct_store_change(ordercode, idx, regdt) VALUES ('{$ordercode}','{$idxs}','".date('YmdHis')."')";
		pmysql_query($sql, get_db_conn());

		$srtn=$Sync->OrderInsert($arrayDatax);
		#싱크커머스 API호출
		if($srtn != 'fail') {

			//변경전 erp로 전송
			sendErpChangeShop($ordercode, $idxs, '', '2');

			#주문정보 update
			$sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$idxs} ";

			pmysql_query($sql, get_db_conn());
			#배송준비중으로 변경
			$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입
			orderProductStepUpdate($exe_id, $ordercode, $idxs, '2');

			//현재 주문의 상태값을 가져온다.
			list($old_step1, $old_step2) = pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='" . trim($ordercode) . "'"));
			if (($old_step1 == '1' || $old_step1 == '2') && $old_step2 == '0') {
				//주문을 배송 준비중으로 변경한다.
				$sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='" . $ordercode . "'";
				pmysql_query($sql2, get_db_conn());
			}
			$suc_cnt++;
		}else{
			$sql = "UPDATE tblorderproduct SET store_code='".$selItemRow->store_code."', delivery_type='0' WHERE ordercode='{$ordercode}' and idx={$idxs} ";
			pmysql_query($sql, get_db_conn());
			$err_cnt++;
		}
		$template = 'WEB13';
		$alim = new ALIM_TALK();
		$alim->makeAlimTalkSearchNewData($ordercode, $template , $idxs ,'');
	}
}

$msg = "배송주체 일괄 수정 : 성공 {$suc_cnt}건, 실패 {$err_cnt}건";
alert_go($msg, "/admin/order_list_all.php?".$_POST['query_string']);

?>
