<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."conf/cscenter_ascode.php");
include_once($Dir."lib/file.class.php");
include("access.php");

$mode=$_POST["mode"];
$ordercode=$_POST["ordercode"]; //수령지 변경 유무

$nowdate=date("YmdHis");



if($mode=="update"){
	$receiver_name=$_POST["receiver_name"];
	$receiver_tel2=$_POST["receiver_tel2"];
	$receiver_zipcode=$_POST["receiver_zipcode"];
	$receiver_address=$_POST["receiver_address"];
	$memo[0]=$_POST["memo0"];
	$c_oc_no=$_POST["oc_no"];
	$c_cs_memo					= str_replace("'", "''", $_POST['cs_memo']);
	if ($c_cs_memo =='<br>') $c_cs_memo					="";
	//print_r($_POST);
	//exdebug($c_cs_memo);
	//exit;

	//경로
	$filepath = $Dir.DataDir."shopimages/cscenter/";
	//파일
	$asfile = new FILE($filepath);
	$up_asfile = "";
	if ($c_cs_memo !='') {
		$up_asfile = $asfile->upFiles();
	}

	$order_msg=implode("[MEMO]",$memo);
	$receiver_addr="우편번호 : {$receiver_zipcode}\n주소 : ".$receiver_address;

	pmysql_query("UPDATE tblorderinfo SET order_msg='{$order_msg}', receiver_name='{$receiver_name}', receiver_tel2='{$receiver_tel2}', receiver_addr='{$receiver_addr}' WHERE ordercode='{$ordercode}'",get_db_conn());
	
	if ($c_cs_memo !='') {
		$req_step_code =  'main_0';
		$req_step_name = '주문서';
		//cs메모
		$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$c_oc_no."', '".$c_cs_memo."', '".$req_step_code."', '".$req_step_name."', '".$_ShopInfo->getId()."', '".$_ShopInfo->getName()."', '".date("YmdHis")."', 'csadmin') RETURNING no";
		$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
		//exdebug($memo);
		//exdebug($memo_sql);
		//exit
		//파일첨부
		if($up_asfile["file"][0]["v_file"]){
			foreach($up_asfile as $fa=>$fav){
				foreach($fav as $na=>$nav){
					$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$c_oc_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->getId()."', '".date("YmdHis")."', 'csadmin')";
					pmysql_query($file_sql);
				}
			}
		}
	}

	alert_go("수정되었습니다.","order_detail.php?ordercode={$ordercode}");



}else if($mode=="ajax_deli"){
	$idx=$_POST["idx"];
	$chkdeli_com=$_POST["chkdeli_com"];
	$deli_num=$_POST["deli_num"];
	
	$ordercode=$_POST["ordercode"];
	$chkdeli_com_text=$_POST["chkdeli_com_text"];

	$pr_qry="select * from tblorderproduct where idx='".$idx."'";
	$pr_result=pmysql_query($pr_qry);
	$pr_data=pmysql_fetch_object($pr_result);

	if($pr_data->delivery_type=="0"){
		pmysql_query("UPDATE tblorderproduct SET deli_com='{$chkdeli_com}', deli_num='{$deli_num}' WHERE ordercode='{$ordercode}' and idx='{$idx}'",get_db_conn());
		echo "수정되었습니다.";
	}else{
		$Sync = new Sync();
		$arrayData = array(
			'ordercode'    => $ordercode,
			'delivery_num' => $deli_num,
			'delivery_com' => $chkdeli_com, //char(3)
			'delivery_name'=> $chkdeli_com_text,
			'sync_status'  => "Y",   //Y:배송중,반송신청
	//		'sync_idx'     => $orderidxs[$i]
			'sync_idx'     => $idx
		);
		$rtn = $Sync->StatusChange($arrayData);
		if( $rtn == "fail" ) {
			batchlog("[error] SyncCommerce API(StatusChange) failed ".json_encode_kr($arrayData));
		} else {
			pmysql_query("UPDATE tblorderproduct SET deli_com='{$chkdeli_com}', deli_num='{$deli_num}' WHERE ordercode='{$ordercode}' and idx='{$idx}'",get_db_conn());
			echo "수정되었습니다.";
		}
	}

}
?>