<?php 
//if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) exit;

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");

$ordercode=$_POST["ordercode"];
$pay_flag=$_POST["pay_flag"];
$pay_auth_no=$_POST["pay_auth_no"];
$pay_data=$_POST["pay_data"];
$real_price=$_POST["real_price"];
$message=$_POST["message"];
$deli_gbn=$_POST["deli_gbn"];
if (ord($deli_gbn)==0) $deli_gbn="N";
$paycode = $_POST['paycode'];
$basketidxs = $_POST['basketidxs'];

# 파일 로그 추가 2016-03-15 유동혁
$logText = "## ".date("Y-m-d H:i:s")." [ ORDERCODE : ".$ordercode." ] ## \n";
$logText.= " paycode    : ".$paycode." \n";
$logText.= " basketidxs : ".$basketidxs." \n";
// 상태값 check용
$err_type = true;
// 주문 상태 확인
/*$ex_basketidxs = explode( '|', $basketidxs );
$orderChk_res = pmysql_query( " SELECT COUNT( * ) AS cnt FROM tblorder_check WHERE paycode = '".$paycode."' AND basketidx IN ( ".implode( ',', $ex_basketidxs )." ) ", get_db_conn() );
$orderChk_row = pmysql_fetch_object( $orderChk_res );
pmysql_free_result( $orderChk_res );
if( $orderChk_row->cnt != count( $ex_basketidxs ) ){
    $err_type = false;
    $logText.= " order_check : false \n";
}*/
// 쿠폰 상태 확인
$couponChk_sql = "SELECT ci_no FROM tblcoupon_order WHERE ordercode ='".$ordercode."' GROUP BY ci_no";
$couponChk_res = pmysql_query( $couponChk_sql, get_db_conn() );
while( $couponChk = pmysql_fetch_object( $couponChk_res ) ){
    $issue_sql = "SELECT ci_no, used FROM tblcouponissue WHERE ci_no = ".$couponChk->ci_no;
    $issue_res = pmysql_query( $issue_sql, get_db_conn() );
    $issue_num = pmysql_num_rows( $issue_res );
    $issue_row = pmysql_fetch_object( $issue_res );
    pmysql_free_result( $issue_res );
    if( $issue_num <= 0 ){
        $err_type = false;
        $logText.= " coupon_check : false \n";
    } else if( $issue_row->used == 'Y' ) {
        $err_type = false;
        $logText.= " coupon_check : false \n";
    }
}
pmysql_free_result( $couponChk_res );
// 마일리지 확인
$pointChk_sql = "
    SELECT
      mem.reserve AS mem_point, temp.reserve AS use_point, temp.point as use_epoint, ( mem.reserve - temp.reserve ) AS is_point, ( mem.act_point - temp.point ) AS is_epoint,
      mem.staff_reserve, ( mem.staff_reserve - temp.reserve ) AS is_staff_point, temp.staff_order
    FROM
        ( SELECT id, reserve, staff_order, point FROM tblorderinfotemp WHERE  ordercode = '".$ordercode."' AND id != '' ) AS temp
    JOIN 
      ( SELECT id, reserve, staff_reserve, act_point FROM tblmember ) AS mem ON ( temp.id = mem.id )
";
$pointChk_res = pmysql_query( $pointChk_sql, get_db_conn() );
$pointChk_row = pmysql_fetch_object( $pointChk_res );
pmysql_free_result( $pointChk_res );
$use_now_point="0";
if($_ShopInfo->getMemid()){
	//통합포인트를 erp에서 가져온다.
	$user_erp_reserve=getErpMeberPoint($_ShopInfo->getMemid());
	$user_reserve = $user_erp_reserve[p_data]?$user_erp_reserve[p_data]:"0";
	$use_now_point=$user_reserve-$pointChk_row->use_point;
}
if( $pointChk_row->use_point > 0 && $use_now_point < 0 && $pointChk_row->staff_order == 'N' ){
    $err_type = false;
    $logText.= " point_check : false \n";
}else if( $pointChk_row->use_epoint > 0 && $pointChk_row->is_epoint < 0 && $pointChk_row->staff_order == 'N' ){
    $err_type = false;
    $logText.= " epoint_check : false \n";
}/* else if( $pointChk_row->use_point > 0 && $pointChk_row->is_staff_point < 0 && $pointChk_row->staff_order == 'Y' ){
    $err_type = false;
    $logText.= " point_check : false \n";
}*/
// $orInsertArr, $prInsertArr => lib에 존재함
if( ord( $ordercode ) && $err_type ) {
	# select table의 컬럼을 지정해준다
	$orStr = implode( ',', $orInsertArr );
	$prStr = implode( ',', $prInsertArr );
	//pmysql_query("INSERT INTO tblorderinfo SELECT * FROM tblorderinfotemp WHERE ordercode='{$ordercode}'",get_db_conn());
	pmysql_query("INSERT INTO tblorderinfo ( ".$orStr." ) ( SELECT ".$orStr." FROM tblorderinfotemp WHERE ordercode='{$ordercode}' ) ",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();

	//pmysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='{$ordercode}'",get_db_conn());
	pmysql_query("INSERT INTO tblorderproduct ( ".$prStr." ) ( SELECT ".$prStr." FROM tblorderproducttemp WHERE ordercode='{$ordercode}' ) ",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();

//    setSyncInfo($ordercode, $opidx, 'I');

	pmysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='{$ordercode}'",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	if ($pmysql_errno!=0) { 
        $logText .= " DB_ERROR \n";
        $logText .= "\n";
        $log_folder = DirPath.DataDir."backup/payparocess_log_".date("Ym");
        if( !is_dir( $log_folder ) ){
            mkdir( $log_folder, 0700 );
            chmod( $log_folder, 0777 );
        }
        $file = $log_folder."/payparocess_".date("Ymd").".txt";
        if(!is_file($file)){
            $f = fopen($file,"a+");
            fclose($f);
            chmod($file,0777);
        }
        file_put_contents($file,$logText,FILE_APPEND);
        echo "no"; 
        exit; 
    }
} else {
    $logText .= "\n";
    $log_folder = DirPath.DataDir."backup/payparocess_log_".date("Ym");
    if( !is_dir( $log_folder ) ){
        mkdir( $log_folder, 0700 );
        chmod( $log_folder, 0777 );
    }
    $file = $log_folder."/payparocess_".date("Ymd").".txt";
    if(!is_file($file)){
        $f = fopen($file,"a+");
        fclose($f);
        chmod($file,0777);
    }
    file_put_contents($file,$logText,FILE_APPEND);
    // ordercode가 없거나 주문이 불가능할때
    echo "no";
    exit;
}

//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
list($reg_id, $reg_name)=pmysql_fetch_array(pmysql_query("select id, sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
if(substr($ordercode,20)=="X") {	//비회원
	$reg_id	= "";
} else {
	list($reg_name)=pmysql_fetch_array(pmysql_query("select name from tblmember WHERE id='{$reg_id}' "));
}

$exe_id		= $reg_id."|".$reg_name."|user";	// 실행자 아이디|이름|타입

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$tempkey=$row->tempkey;
	$id=$row->id;
	$pay_flag_check = $row->pay_flag;
	$paymethod=$row->paymethod[0];
	$pg_type=$row->paymethod[1];
	$last_price = $row->price + $row->deli_price - $row->dc_price - $row->reserve - $row->point;
	$user_reserve=$row->reserve;
	$user_point=$row->point;
	$delflag=$row->del_gbn;
	$staff_order = $row->staff_order;
	$cooper_order = $row->cooper_order;

    $logText.= "tempkey        = ".$tempkey."\n";
    $logText.= "id             = ".$id."\n";
    $logText.= "pay_flag_check = ".$pay_flag_check."\n";
    $logText.= "paymethod      = ".$paymethod."\n";
    $logText.= "pg_type        = ".$pg_type."\n";
    $logText.= "last_price     = ".$last_price."\n";
    $logText.= "user_reserve   = ".$user_reserve."\n";
	$logText.= "user_point   = ".$user_point."\n";
    $logText.= "delflag        = ".$delflag."\n";

}
pmysql_free_result($result);

if (!strstr("VOQCPMY", $paymethod)) { echo "no";exit; }

if (strstr("VOQCPMY", $paymethod)) {
	//주문 상태 변경 (주문접수)
	orderStepUpdate($exe_id,  $ordercode, '0', '', '', '', '', 'Y' );
}

list($staff_price)=pmysql_fetch("select sum(staff_price) from tblorderproduct where ordercode='".$ordercode."'");
list($cooper_price)=pmysql_fetch("select sum(cooper_price) from tblorderproduct where ordercode='".$ordercode."'");

//############ 카드 승인/실패 업데이트 ############
if (strcmp($pay_flag_check,"0000")!=0 && ord($pay_flag)) {
	#추후 여러 PG사 이용할 경우 PG사에 따라 결과코드를 구분하기 위하여
	if (strcmp($pay_flag,"0000")==0) {
		$auto_pay_admin_proc="N";
		if(strstr("ABCD", $pg_type)) {	//KCP/DACOM/ALLTHEGATE/INIPAY 경우 자동매입이기 때문에 아래 세팅
			$auto_pay_admin_proc="Y";
		}
	} else {

	}
	if ($pay_flag=="0000" && $last_price!=$real_price) {
        if(strlen(AdminMail)>0) {
		    sendmail(AdminMail,"[결제] 승인금액이 맞지않음 ({$paymethod})","주문금액 : $last_price\n승인금액 : $real_price\n\nordercode=$ordercode\npay_data=$pay_data", getMailHeader( '[PG]'.$ordercode, AdminMail ) );
        }

	}

	if(strstr("CP", $paymethod)) {	//신용카드
		$sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', pay_auth_no='{$pay_auth_no}', ";
		$sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
		if($auto_pay_admin_proc=="Y") $sql.= ", pay_admin_proc='Y' ";
	} else if(strstr("OQ", $paymethod)) {	//가상계좌
		$sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', ";
		$sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
	} else if(strstr("M", $paymethod)) {	//휴대폰
        if ( $pg_type == "E" ) {
            // =========================================================================================
            // 다날 휴대폰 결제인 경우, pay_auth_no 업데이트
            // =========================================================================================
            $sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', pay_auth_no='{$pay_auth_no}', ";
            $sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
        } else {
            $sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', ";
            $sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
        }
	} else if(strstr("Y", $paymethod)) {	//페이코
        if ( $pg_type == "F" ) {
            // =========================================================================================
            // 페이코 결제인 경우, pay_auth_no 업데이트
            // =========================================================================================
            $sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', pay_auth_no='{$pay_auth_no}', ";
            $sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
        } else {
            $sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', ";
            $sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
        }
	} else if(strstr("V", $paymethod)) {	//계좌이체
		$sql = "UPDATE tblorderinfo SET pay_flag='{$pay_flag}', ";
		$sql.= "pay_data='{$pay_data}', deli_gbn='{$deli_gbn}' ";
	}
	if($deli_gbn=="C") $sql.= ",del_gbn='R' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
    backup_save_sql( $sql );
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='{$deli_gbn}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		//$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());
	}
	$pmysql_errno+=pmysql_errno();

    if( $deli_gbn == "C" ) {
        # 주문 상태 => 결제실패
        $oi2_sql = "UPDATE tblorderinfo SET oi_step2 = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $oi2_sql, get_db_conn() );
         # 주문상품 상태 => 결제실패
        $op_sql = "UPDATE tblorderproduct SET op_step = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $op_sql, get_db_conn() );
    }else{
		setSyncInfo($ordercode, $opidx, 'I');
	}

} else if (strcmp($pay_flag_check,"0000")!=0 && ord($ordercode) && $deli_gbn=="C") {
	$sql = "UPDATE tblorderinfo SET ";
	$sql.= "deli_gbn	= '{$deli_gbn}', ";
	$sql.= "pay_data		= '결제정보 작성 중 주문취소' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='{$deli_gbn}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		//$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());
	}
	$pmysql_errno += pmysql_errno();

	$opSql = "SELECT idx FROM tblorderproduct WHERE ordercode = '".$ordercode."'";
	$opRes = pmysql_query( $opSql, get_db_conn() );
	while( $opRow = pmysql_fetch_object( $opRes ) ){
		orderProductStepUpdate($exe_id,  $ordercode, $opRow->idx, 44 );
	}
	pmysql_free_result( $opRes );
	
}
$logText.= " === 주문 상태 진입 === \n";
if (strstr("VOQCPMY", $paymethod) && strcmp($pay_flag,"0000")!=0 && $delflag=='N') {	//주문실패
    $logText.= " // 주문 실패 // \n";
	/*
	if( strstr("CPM", $paymethod) ){
		# 수량복구
		order_recovery_quantity( $ordercode );
	}
	
	# 쿠폰 복구
	$sql = "SELECT coupon_code FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$coupon_code = $row->coupon_code;
		pmysql_query("UPDATE tblcouponissue SET used='N' WHERE id='{$id}' AND coupon_code='{$coupon_code}'",get_db_conn());
	}
	pmysql_free_result($result);
	*/
} else if((strstr("VOQCPMY", $paymethod) && strcmp($pay_flag,"0000")==0 && ord($ordercode)) && ($delflag=='N' || $delflag=="R")) {	//주문성공
	$logText.= " // 주문 성공 // \n";
    $logText.= " ID : ".$id." \n";
	#적립금 사용
    if( strlen( $id ) > 0 && ( $user_reserve > 0 || $user_point > 0 || $staff_order=="Y" || $cooper_order=="Y" )){
		$point_sql="select * from tblorderproduct where ordercode='".$ordercode."'";
		$point_result=pmysql_query($point_sql);
		while($point_data=pmysql_fetch_object($point_result)){
			if($staff_order=="Y"){
				$point_change=$point_data->staff_price;
			}else if($cooper_order=="Y"){
				$point_change=$point_data->cooper_price;
			}else{
				$point_change=$point_data->use_point;
			}

			//insert_point( $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') ); 
			$logText.= " 적립금 사용 : ".$point_change." \n";
			$logText.= " E포인트 사용 : ".$user_point." \n";
			insert_order_point( $ordercode, $id, (-1) * $point_change, "주문번호 {$ordercode} 결제(1건)에 대한 사용", '@order', $id, $id.'-'.uniqid('').'|'.$ordercode.'_'.$point_data->idx, '', '', (-1) * $point_data->use_epoint );
	//		"주문번호 {$ordercode} 결제(1건)에 대한 사용"
		}
    }
    #쿠폰사용
    $sql = "SELECT ci_no FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
    $result=pmysql_query($sql,get_db_conn());
    while($row=pmysql_fetch_object($result)){
        $ci_no = $row->ci_no;
        $logText.= " 쿠폰 사용 : ".$ci_no." \n";
        pmysql_query("UPDATE tblcouponissue SET used='Y' WHERE id='".$id."' AND ci_no='{$ci_no}'",get_db_conn());
    }

	# 주문수량 차감
	if( strstr("VCPMY", $paymethod) ) {
        
		//주문수량 차감
		order_quantity( $ordercode );
		//주문 상태 변경
		orderStepUpdate($exe_id,  $ordercode, 1 );
		$sql="UPDATE tblorderinfo SET bank_date = '".date('YmdHis')."' WHERE ordercode = '".$ordercode."'";
		pmysql_query($sql,get_db_conn());

		if( strstr("VCPMY", $paymethod) ){ // 계좌이체, 신용카드, 매매보호(신용카드) , 휴대폰
				$Sync = new Sync();
				$arrayDatax=array('ordercode'=>$ordercode);

				$srtn=$Sync->OrderInsert($arrayDatax);

			//if ($staff_order!= "Y") {
				//주문완료시 본사발송이 아닐경우 입찰로 돌림
				//15ss 자동으로 싱크커머스로 이동안되게수정
				//$pr_qry="select * from tblorderproduct where ordercode='".$ordercode."' AND (delivery_type = '0') AND op_step in ('1') and store_code!='".$sync_bon_code."'";
				//$pr_qry="select * from tblorderproduct op left join tblproduct p on (op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1') and op.store_code!='".$sync_bon_code."' and concat(p.season_year,p.season)!='2015K'";
				//2017년 12월19일 신원고효서 주임 요청으로 2015년 상품 o2o 제외
				$pr_qry="select * from tblorderproduct op left join tblproduct p on (op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1') and op.store_code!='".$sync_bon_code."' and p.season_year!='2015'";
				$logText.= " 싱크커머스 매장발송  : ".$pr_qry." \n";
				$pr_result=pmysql_query($pr_qry);
				while($pr_data=pmysql_fetch_object($pr_result)){
					$sync_idx = $pr_data->idx;
					$logText.= " 싱크커머스 idx  : ".$sync_idx." \n";
					$logText.= " 싱크커머스 매장  : ".$pr_data->store_code." \n";
					sendErpChangeShop($ordercode, $sync_idx, '');
					$arrayDatax_p=array('ordercode'=>$ordercode,'sync_idx'=>'AND idx='.$sync_idx);
					$deli_type_sql = "UPDATE tblorderproduct SET store_code='',delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
					pmysql_query($deli_type_sql, get_db_conn());

					$srtn=$Sync->OrderInsert($arrayDatax_p);

					#싱크커머스 API호출
					if($srtn != 'fail') {
						#주문정보 update
						$in_deli_sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";

						pmysql_query($in_deli_sql, get_db_conn());
						#배송준비중으로 변경
						$exe_id		= $reg_id."|".$reg_name."|user";	// 실행자 아이디|이름|타입
						orderProductStepUpdate($exe_id, $ordercode, $pr_data->idx, '2');

						//현재 주문의 상태값을 가져온다.
						list($old_step1, $old_step2) = pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='" . trim($ordercode) . "'"));
						if (($old_step1 == '1' || $old_step1 == '2') && $old_step2 == '0') {
							//주문을 배송 준비중으로 변경한다.
							$sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='" . $ordercode . "'";
							pmysql_query($sql2, get_db_conn());
						}

						$template = 'WEB14';
						$alim = new ALIM_TALK();
						$alim->makeAlimTalkSearchNewData($ordercode, $template , $pr_data->idx ,'');						

					}else{
						$in_deli_sql = "UPDATE tblorderproduct SET  store_code='',delivery_type='0' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
						pmysql_query($in_deli_sql, get_db_conn());
						
					}
				}
			//}
		}
	}

	if(  $delflag=="R" ){
		$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='{$ordercode}'";
		pmysql_query($sql,get_db_conn());
	}
}
$logText .= "\n";
$log_folder = DirPath.DataDir."backup/payparocess_log_".date("Ym");
if( !is_dir( $log_folder ) ){
    mkdir( $log_folder, 0700 );
    chmod( $log_folder, 0777 );
}
$file = $log_folder."/payparocess_".date("Ymd").".txt";
if(!is_file($file)){
    $f = fopen($file,"a+");
    fclose($f);
    chmod($file,0777);
}
file_put_contents($file,$logText,FILE_APPEND);

# 에러처리
if ($pmysql_errno!=0) { 
	echo $pmysql_errno;
	exit; 
}
?>
ok
