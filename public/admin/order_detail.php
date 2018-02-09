<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

$ordercode=$_REQUEST["ordercode"];
$idx				= $_REQUEST["idx"];
$type=$_POST["type"];

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
//exdebug($_ord);
pmysql_free_result($result);
if(!$_ord) {
	alert_go("해당 주문내역이 존재하지 않습니다.",'c');
}

#메모 정보 쿼리
list($oc_no)=pmysql_fetch("SELECT oc_no FROM tblorderproduct WHERE ordercode='{$ordercode}'");
$memo_sql="select * from tblcscentermemo where receipt_no='".$oc_no."' and route_type='csadmin' order by regdt";
//echo $memo_sql;
$memo_result=pmysql_query($memo_sql);
while($memo_data=pmysql_fetch_array($memo_result)){
	$memo_while[$memo_data["no"]]=$memo_data;

	$file_sql="select * from tblcscenterfile where receipt_no='".$oc_no."' and memo_no='".$memo_data["no"]."' and route_type='csadmin' order by no";
	$file_result=pmysql_query($file_sql);
	while($file_data=pmysql_fetch_array($file_result)){
		$memo_while[$memo_data["no"]]["filename"][$file_data["no"]]=$file_data["filename"];
	}
}
pmysql_free_result($memo_result);

$tax_type=$_shopdata->tax_type;


$pgid_info="";
$pg_type="";
switch ($_ord->paymethod[0]) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_shopdata->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_shopdata->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_shopdata->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_shopdata->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);


//무통장입금확인
if($type=="bank" && ord($ordercode)) {
	if($_ord->paymethod=="B" && $tax_type=="Y") {
		$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='N' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row->cnt>0) {
			$flag="Y";
			include($Dir."lib/taxsave.inc.php");
		}
	}

	pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$ordercode}' ",get_db_conn());

	//주문수량 차감
	order_quantity( $ordercode );

	// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
	orderStepUpdate($exe_id, $ordercode, '1'); // 결제완료

	$isupdate=true;

	//입금 확인 메일과 SMS을 보낸다. - 테스트 중이라 일단 막음 꼭 풀어야함 - 김재수
	if(ord($_ord->sender_email)) {
		SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $ordercode);
	}

	# SMS ( 입금 확인 안내 )
	$mem_return_msg = sms_autosend( 'mem_bankok', $ordercode, '', '' );
	$admin_return_msg = sms_autosend( 'admin_bankok', $ordercode, '', '' );
	
	echo "<script>";
	echo "	alert('입금 완료로 처리되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//배송 준비
} elseif($type=="readydeli" && ord($ordercode)) {
	
	$readydeli_sql	= "select count(*) as cnt,sum(case when delivery_type='0' then 1 else 0 end) delivery_cnt  from tblorderproduct where ordercode='{$ordercode}'"; //2016-10-11 ssuya O2O주문이 아니더라고 store_code가 들어감으로 변경

	list($bcnt,$delivery_cnt)=pmysql_fetch_array(pmysql_query($readydeli_sql));

	if ($_ord->deli_gbn == "N" && $bcnt == $delivery_cnt) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
		if (pmysql_query($sql, get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql .= "AND op_step < 40 ";
			//$sql .= "AND deli_gbn='N' and length(store_code)=0 ";	//2016-10-07 libe90 O2O주문은 배송처리 안되게 작업
			$sql .= "AND deli_gbn='N' and delivery_type='0' ";	//2016-10-11 ssuya O2O주문이 아니더라고 store_code가 들어감으로 변경
			pmysql_query($sql, get_db_conn());
		}

		// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
		orderStepUpdate($exe_id, $ordercode, '2'); // 배송준비
	}

	echo "<script>";
	echo "	alert('배송 준비로 처리되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;
} elseif($type=="deliinfoup" && ord($ordercode) && ord($_POST["idxs"])) {

	$delimailok=$_POST["delimailtype"];	//배송중에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$idxs=$_POST["idxs"];
	$deli_com=$_POST["deli_com"];
	$deli_num=$_POST["deli_num"];

	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deliinfoup_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$tmp_arr_deli = array();
	$arr_deli_idxs = array();
//	$deliQry = "";
	if ($deli_com != '' && $deli_num !='') {
		
		/********
		에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
		********/
		//배송한 상품의 수를 체크한다.
		list($op_deli_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND deli_gbn = 'Y' "));
		list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' "));

		if ($op_deli_cnt==0) { // 처음 배송된 상품일 경우
			if(ord($deli_name)==0) {
				$deli_name="자가배송";
				$deli_num="0000";
			}
			if(strstr("QP", $_ord->paymethod[0])) {

				if($pg_type=="A") {	//KCP
					$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

					$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

					$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
					if (substr($delivery_data,0,2)!="OK") {
						$tempdata=explode("|",$delivery_data);
						$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
						if(ord($tempdata[1])) $errmsg=$tempdata[1];
						//alert_go($errmsg,-1);
						echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
					} else {
						$tempdata=explode("|",$delivery_data);
						if(ord($tempdata[1])) $errmsg=$tempdata[1];
						if(ord($errmsg)) {
							echo "<script> alert('{$errmsg}');</script>";
						}
					}
				} else if($pg_type=="G") {	//NICE
					$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

					$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

					$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
					if (substr($delivery_data,0,2)!="OK") {
						$tempdata=explode("|",$delivery_data);
						$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
						if(ord($tempdata[1])) $errmsg=$tempdata[1];
						//alert_go($errmsg,-1);
						echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
					} else {
						$tempdata=explode("|",$delivery_data);
						if(ord($tempdata[1])) $errmsg=$tempdata[1];
						if(ord($errmsg)) {
							echo "<script> alert('{$errmsg}');</script>";
						}
					}
				}
			}
		}
		//$deliQry = ", deli_com = '".$deli_com."', deli_num = '".$deli_num."' ";
		$sql = "UPDATE tblorderproduct SET deli_com='{$deli_com}', deli_num='{$deli_num}', deli_gbn = 'Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idxs}' ";
		//$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		$sql.= "AND op_step < 40 ";
		//echo $sql;
		pmysql_query($sql,get_db_conn());
		if( !pmysql_error() ){

			// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
			orderProductStepUpdate($exe_id, $ordercode, $idxs, '3'); // 배송중

			#배송정보 변경 LOG
			$outText.= " 주문 번호     : ".$ordercode."\n";
			$outText.= " 상품 IDX     : ".$idxs."\n";
			$outText.= " 송장 번호     : ".$deli_num."\n";
			$outText.= " 배송회사 코드 : ".$deli_com."\n";
			$outText.= " 배송상태 변경 : 발송중( deli_gbn = Y )\n";
/*
			if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
				$delimailtype="N";
				$tmp_arr_deli_idx = array_search( $deliQry, $tmp_arr_deli );
				if( $tmp_arr_deli_idx === false && $deliQry != '' ) {
					$tmp_arr_deli[] = $deliQry;
					$arr_deli_idxs[] = array( 'ordercode'=>$ordercode, 'idxs'=>$idxs, 'deli_com'=>$deli_com, 'deli_num'=>$deli_num );
				} else if( $deliQry != '' ) {
					$arr_deli_idxs[$tmp_arr_deli_idx]['idxs'] = $arr_deli_idxs[$tmp_arr_deli_idx]['idxs'].','.$idxs;
				}
			}*/
		} else {
			$outText.= " 주문 번호     : ".$ordercode."\n";
			$outText.= " 상품 IDX     : ".$idxs."\n";
			$outText.= " 송장 번호     : ".$deli_num."\n";
			$outText.= " 배송회사 코드 : ".$deli_com."\n";
			$outText.= " 배송상태 변경 : ERR \n";
			$qryErr++;
		}
	} else {
		$outText.= " 주문 번호     : ".$ordercode."\n";
		$outText.= " 상품 IDX     : ".$idxs."\n";
		$outText.= " 송장 번호     : ".$deli_num."\n";
		$outText.= " 배송회사 코드 : ".$deli_com."\n";
		$outText.= " 배송상태 변경 : ERR \n";
		$qryErr++;
	}
		
	if( $qryErr == 0 ){
		$sql = "UPDATE tblorderinfo SET deli_gbn = 'Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		//echo $sql;
		pmysql_query($sql,get_db_conn());

		// 신규상태 변경 추가
		orderStepUpdate($exe_id, $ordercode, '3', '0' ); // 배송중

		if($delimailok=="Y") {	//배송중 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, 'N', $idxs);

			if(ord($_ord->sender_tel)) {
				$mem_return_msg = sms_autosend( 'mem_delinum', $ordercode, $idxs, '' );
				$admin_return_msg = sms_autosend( 'admin_delinum', $ordercode, $idxs, '' );
			}
		}
/*
		// 배송 mail 및 문자발송
		if( count( $arr_deli_idxs ) > 0 ){
			foreach( $arr_deli_idxs as $k=>$v ){
				SendDeliMail( $_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $v['ordercode'], $v['deli_com'], $v['deli_num'], 'N', $v['idxs'] );
				$op_cnt_sql = "SELECT COUNT( * ) AS cnt FROM tblorderproduct WHERE ordercode ='".$v['ordercode']."'";
				$op_cnt_res = pmysql_query( $op_cnt_sql, get_db_conn() );
				$op_cnt_row = pmysql_fetch_object( $op_cnt_res );
				pmysql_free_result( $op_cnt_res );
				$op_idx_cnt = count( explode( ',', $v['idxs'] ) );
				if( $op_cnt_row->cnt == 1 || $op_idx_cnt == $op_cnt_row->cnt ){
					$mem_return_msg = sms_autosend( 'mem_delivery', $v['ordercode'], $v['idxs'], '' );
					$admin_return_msg = sms_autosend( 'admin_delivery', $v['ordercode'], $v['idxs'], '' );
				} else if( $op_cnt_row->cnt > 1 ) {
					$mem_return_msg = sms_autosend( 'mem_delinum', $v['ordercode'], $v['idxs'], '' );
					$admin_return_msg = sms_autosend( 'admin_delinum', $v['ordercode'], $v['idxs'], '' );
				}
			}
		}*/
	}

	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'deliinfoup_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deliinfoup_".date("Ymd").".txt",0777);

	echo "<script>";
	//echo "	alert('발송처리 완료 되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;
	

//송장정보 업데이트
} elseif($type=="deliupdate" && ord($ordercode)) {
	$delimailok=$_POST["delimailtype"];	//배송중에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$deli_com=$_POST["deli_com"];
	$deli_num=$_POST["deli_num"];
	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deliupdate_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$patterns = array(" ","_","-");
	$replace = array("","","");
	$deli_num = str_replace($patterns,$replace,$deli_num);

	/********
	에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
	********/

	list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' "));

	if(ord($deli_name)==0) {
		$deli_name="자가배송";
		$deli_num="0000";
	}
	if(strstr("QP", $_ord->paymethod[0])) {

		if($pg_type=="A") {	//KCP
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

			$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

			$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
			if (substr($delivery_data,0,2)!="OK") {
				$tempdata=explode("|",$delivery_data);
				$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				//alert_go($errmsg,-1);
				echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
			} else {
				$tempdata=explode("|",$delivery_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}
		} else if($pg_type=="G") {	//NICE
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

			$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

			$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
			if (substr($delivery_data,0,2)!="OK") {
				$tempdata=explode("|",$delivery_data);
				$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				//alert_go($errmsg,-1);
				echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
			} else {
				$tempdata=explode("|",$delivery_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}
		}
	}

	$sql = "UPDATE tblorderproduct SET deli_num='{$deli_num}', deli_com='{$deli_com}', deli_gbn ='Y', deli_date='".date("YmdHis")."'  ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	//$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	$sql.= "AND op_step < 40 AND (op_step='2' OR op_step='3' OR op_step='4')";
	pmysql_query($sql,get_db_conn());

	if( !pmysql_error() ){
		$sql = "UPDATE tblorderinfo SET deli_gbn ='Y', deli_date='".date("YmdHis")."'  ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());

		// 신규상태 변경 추가
		orderStepUpdate($exe_id, $ordercode, '3' ); // 배송중

		#배송정보 변경 LOG
		$outText.= " 주문 번호     : ".$ordercode."\n";
		$outText.= " 송장 번호     : ".$deli_num."\n";
		$outText.= " 배송회사 코드 : ".$deli_com."\n";
		$outText.= " 배송상태 변경 : 발송중( deli_gbn = Y )\n";
	} else {

		#배송정보 변경 LOG
		$outText.= " 주문 번호     : ".$ordercode."\n";
		$outText.= " 송장 번호     : ".$deli_num."\n";
		$outText.= " 배송회사 코드 : ".$deli_com."\n";
		$outText.= " 배송상태 변경 : ERR \n";
		$qryErr++;
	}
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$outText.= "\n";
	$upQrt_f = fopen($textDir.'deliupdate_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deliupdate_".date("Ymd").".txt",0777);

	if( $qryErr == 0 ){
		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

			if(ord($_ord->sender_tel)) {
				# SMS ( 완전배송 안내메세지 )
				$mem_return_msg = sms_autosend( 'mem_delivery', $ordercode, '', '' );
				$admin_return_msg = sms_autosend( 'admin_delivery', $ordercode, '', '' );
				
			}
		}
	}
	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

}


# 전체 매장을 불러온다.
//$sql="SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$sql="SELECT * FROM tblstore WHERE 1=1 ORDER BY sort asc, sno desc ";
$result=pmysql_query($sql,get_db_conn());
$storelist=array();
while($row=pmysql_fetch_object($result)) {
	$storelist[$row->store_code]=$row;
}
pmysql_free_result($result);

#배송업체
$delicomlist=array();
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$delicomlist[]=$row;
	$delicomlist_code[trim($row->code)]=$row;
	
}

pmysql_free_result($result);

list($prod_total)=pmysql_fetch_array(pmysql_query("select count(*) as prod_total from tblorderproduct WHERE ordercode='".$_ord->ordercode."' "));

$groupOpSql = "SELECT a.idx, a.productcode, a.vender, a.productname, a.addcode, a.selfcode, a.deli_com, a.deli_num, a.price, ";
$groupOpSql.= "( a.price * a.option_quantity ) AS sum_def_price, ";
$groupOpSql.= "( a.option_price * a.option_quantity ) AS sum_opt_price,  ";
$groupOpSql.= "( ( a.price + a.option_price ) * a.option_quantity ) AS sum_price, ";
$groupOpSql.= "a.quantity, a.opt1_name, a.opt2_name, a.option_quantity, a.option_price, a.option_price_text, ";
$groupOpSql.= "a.coupon_price, (a.reserve * a.option_quantity) AS sum_reserve, a.use_point, a.use_epoint, a.receive_ok, ";
$groupOpSql.= "a.redelivery_type, a.redelivery_date, a.redelivery_reason, ";
$groupOpSql.= "a.deli_gbn,a.deli_price, a.receive_ok, a.op_step, a.oc_no, a.opt1_change, a.opt2_change, a.text_opt_subject, ";
$groupOpSql.= "a.text_opt_content, a.option_price_text, a.text_opt_subject_change, a.text_opt_content_change, ";
$groupOpSql.= "option_price_text_change, b.oi_step1, b.oi_step2, a.order_conf, a.delivery_type, a.store_code, a.reservation_date, a.deli_closed ";
$groupOpSql.= "FROM tblorderproduct a ";
$groupOpSql.= "join tblorderinfo b on a.ordercode = b.ordercode ";
$groupOpSql.= "WHERE a.ordercode ='".$_ord->ordercode."' ";
$groupOpSql.= "order by a.vender, a.idx";
//echo "sql = ".$groupOpSql."<br>";
	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){

	}
$erp_result=pmysql_query($groupOpSql,get_db_conn());
while($erp_row=pmysql_fetch_object($erp_result)) {
	$oc_prod_idx	= $oc_prod_idx?$oc_prod_idx."|".$erp_row->idx:$erp_row->idx;
	$oc_prod_name	= $oc_prod_name?$oc_prod_name."|".$erp_row->productname:$erp_row->productname;
}

$groupOpRes = pmysql_query( $groupOpSql, get_db_conn() );
$total		= pmysql_num_rows($groupOpRes);

$message=explode("[MEMO]",$_ord->order_msg);
$message[0]=str_replace("\"","&quot;",$message[0]);
$message[0]=str_replace("\"","",$message[0]);

$message[0]=str_replace("\r\n","<br>\n&nbsp;&nbsp;",$message[0]);


$pc_type	= ($total==$prod_total)?"ALL":"PART";

#주소
$place_name=$_ord->receiver_name;
$place_tel=	$_ord->receiver_tel2;
$address = str_replace("\n"," ",trim($_ord->receiver_addr));
$address = str_replace("\r"," ",$address);
$pos=strpos($address,"주소");
if ($pos>0) {
	$post = trim(substr($address,0,$pos));
	$address = substr($address,$pos+9);
}
$post = str_replace("우편번호 : ","",$post);
$arpost = explode("-",$post);
$zonecode	= $post;

#주문자
if(substr($_ord->ordercode,20)=="X") {	//비회원
	//$stridM = $_ord->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>) / 주문번호: ".substr($_ord->id,1,6);
	$stridM = $_ord->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>)";
} else {	//회원
	$stridM = "<FONT COLOR=\"blue\">{$_ord->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>({$_ord->id})</FONT>";
}

#주문일
$order_date=substr($_ord->ordercode,'0','4').'-'.substr($_ord->ordercode,'4','2').'-'.substr($_ord->ordercode,'6','2').' '.substr($_ord->ordercode,'8','2').':'.substr($_ord->ordercode,'10','2').':'.substr($_ord->ordercode,'12','2');

#주문채널
$chk_mb["0"]="PC";
$chk_mb["1"]="MO";
$chk_mb["2"]="AP";

$totalprice="0";
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>주문서</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="styleSheet" href="/css/common.css" type="text/css">
<link rel="stylesheet" href="/admin/static/css/crm.css" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
</head>

<script language="javascript">
	window.resizeTo(1000,1000); // 지정한 크기로 변한다.(가로,세로)

function DeliSearch(deli_url){
	//window.open(deli_url,"배송추적",'_blank ',"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
	//새탭으로 변경
	opener.window.open(deli_url,"배송추적");
}

function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('receiver_zipcode').value = data.zonecode;
			document.getElementById('receiver_address').value = data.address;
			document.getElementById('receiver_address').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;
		}
	}).open();
}

function order_admin_memo(mode, ordercode, om_no) {
		window.open("about:blank","ordermemo_pop","width=200,height=200,scrollbars=yes");
		document.form_memo.target="ordermemo_pop";
		document.form_memo.mode.value=mode;
		document.form_memo.ordercode.value=ordercode;
		document.form_memo.om_no.value=om_no;
		document.form_memo.action="order_memo_reg.php";
		document.form_memo.submit();
}

function order_submit(){
	if(confirm("저장 하시겠습니까?")){
<?if($oc_no != 0){?>
		var fd = new FormData();
		var sHTML = oEditors.getById["ir1"].getIR();
		$("textarea[name=cs_memo]").val(sHTML);
		var cs_memo			= $('textarea[name=cs_memo]').val();
		fd.append('cs_memo',cs_memo);
<?}?>
		$("#order_form").submit();
	}
}

function ajaxValue(type, idx){
	
	if(!confirm("상품의 배송정보를 수정하시겠습니까?")) {
		return;
	}
	if(confirm("상품의 배송정보 변경내역을 메일/SMS로 발송하시겠습니까?")){
		$("#delimailtype").val("Y");
	}else{
		$("#delimailtype").val("N");
	}
	
	var ordercode=$("#ordercode").val();

	if(type=="deliinfoup"){
		var chkdeli_com=$("select[name='chkdeli_com["+idx+"]']").val();
		var deli_num=$("input[name='deli_num_array["+idx+"]']").val();
	}else if(type=="deliupdate"){
		var chkdeli_com=$("select[name='deli_com']").val();
		var deli_num=$("input[name='deli_num']").val();
	}
	$("#deli_com").val(chkdeli_com);
	$("#deli_num").val(deli_num);
	$("#idxs").val(idx);
	$("#type").val(type);
	$("#form_deil").submit();
	
	/*
	var allData = { "idx" : idx, "mode" : mode, "ordercode" : ordercode, "chkdeli_com" : chkdeli_com, "deli_num" : deli_num };

	$.ajax({ 
	type: "POST", 
	url: "./order_detail_indb.php", 
	data: allData
	}).done(function(msg) {
		alert(msg);
	});
*/	
}


function change_deli(mode, ordercode, idx , status){
	if (mode == 'open_deli') {
		$("#view_deli_div_"+idx).hide();
		$("#modi_deli_div_"+idx).show();
	} else if (mode == 'close_deli') {
		$("#modi_deli_div_"+idx).hide();
		$("#view_deli_div_"+idx).show();
	} else if (mode == 'ajax_deli') {
		if(!confirm("상품의 배송정보를 수정하시겠습니까?")) {
			return;
		}

		/*if(status != '30'){
			alert('배송중인 정보만 수정가능 합니다');
			return;
		}*/
		
		var chkdeli_com_text	= $("select[name='chkdeli_com["+idx+"]'] option:selected").text();
		var chkdeli_com	= $("select[name='chkdeli_com["+idx+"]']").val();
		var deli_num		= $("input[name='deli_num_array["+idx+"]']").val();
		
		var allData = { "idx" : idx, "mode" : mode, "ordercode" : ordercode, "chkdeli_com" : chkdeli_com, "deli_num" : deli_num, "chkdeli_com" : chkdeli_com,"chkdeli_com_text" : chkdeli_com_text};

// 		console.log(allData);
// // 		//return;

		$.ajax({ 
		type: "POST", 
		url: "./order_detail_indb.php", 
		data: allData
		}).done(function(msg) {
			alert(msg);
			window.location.reload();
			window.opener.location.reload();
		});
	}
}
var countdeli=countdelinum=countdecan=countbank=countbacan=countvican=counttrcan=countokcan=countokhold=0;

//무통장입금 완료처리
function banksend(){
	if(!countbank){
		if(!confirm("입금확인을 셋팅하시겠습니까?")) return;
		countbank++;
		document.form2.type.value="bank";
		document.form2.submit();
	}
}

//발송준비, 배송완료 처리
function delisend(temp){
	if(!countdeli){
		if(temp=="S" && !confirm("발송준비 지시를 하시겠습니까?")) return;
		if(temp=="S") document.form2.type.value="readydeli";
		countdeli++;
		document.form2.submit();
	}
}
function status_view(osl_no) {
	$(".CLS_status_det").removeClass("hide");
	$(".CLS_status_det").addClass("hide");
	$(".csd_"+osl_no).removeClass("hide");
}

function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
function PageResize() {
	var oWidth = 1020;
	var oHeight = 700;

	window.resizeTo(oWidth,oHeight);
}

function oc_ajax_proc(mode, no){
	var receipt_no	= $('input[name=oc_no]').val();
	var fd = new FormData();

	var confirm_txt	= '저장 하시겠습니까?';

	if(mode=="memo_del"){ // 메모 삭제

		fd.append('mode', mode);
		fd.append('receipt_no', receipt_no);
		fd.append('memo_no', no);

		var confirm_txt	= '메모를 삭제 하시겠습니까?';

	}else if(mode=="memo_img_del"){ // 메모 이미지 삭제

		fd.append('mode', mode);
		fd.append('receipt_no', receipt_no);
		fd.append('img_no', no);

		var confirm_txt	= '메모 이미지를 삭제 하시겠습니까?';

	}

	if(confirm(confirm_txt)){
		$.ajax({
			url:"cscenter_order_cancel_indb.php",
			type:'POST',
			data:fd,
			dataType: "json",
			async:false,
			cache:false,
			contentType:false,
			processData:false,
			success: function(data){
				alert(data.msg);
				if(data.type == 1){
					if(mode=="memo_del" || mode=="memo_img_del"){
						$(".txt-box").html(data.html);
					} else if (mode=="receipt_save") {
						$(".receipt_info").html(data.html);
						zip_change();
					}
				}
			}
		});
	}

}

</script>
<!-- <body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"> -->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize();">


<div class="pop_top_title"><p>주문서</p></div>
<form name='order_form' id='order_form' action="./order_detail_indb.php" method='post'>
<input type="hidden" name="mode" id="mode" value="update">
<input type="hidden" name="ordercode" id="ordercode" value="<?=$_ord->ordercode?>">

<section class="online-as">
	<!--배송상태 변경 임시 비노출 처리 2016-10-13-->
<!--
	<div class="title">
		<h3>
	<?
	if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C") {
		if(strstr("B",$_ord->paymethod[0]) && strlen($_ord->bank_date)!=14) {
	?>
			<a href="javascript:banksend()"><img src="images/ordtl_btnbankok.gif" align=absmiddle border=0></a>

	<?
		} elseif(!strstr("OQ",$_ord->paymethod[0]) || strlen($_ord->bank_date)>=12) {	//가상계좌, 가상계좌(매매보호) 입금건이 아닌건에 대해서 입금이 된 경우
			if($_ord->deli_gbn!="S"){
	?>
				<a href="javascript:delisend('S')"><img src="images/ordtl_btndeliready.gif" align=absmiddle border=0></a> 
	<?		}
		}
	}	
		?>
		</h3>
	</div>-->
	<div class="title">
		<h3><span class="point-txt"><?=$ordercode?> </span>주문정보</h3>
		<p><a href="javascript:window.print()" class="btn-type">주문서 출력</a></p>
	</div>
	<div class="clear">
		<div class="content-l">
			<table class="table-th-left">
				<caption></caption>
				<colgroup>
					<col style="width:120px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">주문채널</th>
						<td><?=$chk_mb[$_ord->is_mobile]?></td>
					</tr>
					<tr>
						<th scope="row">대표 주문번호</th>
						<td><?=$_ord->ordercode?></td>
					</tr>
					<tr>
						<th scope="row">PG사 주문번호</th>
						<td><?=$_ord->pg_ordercode?></td>
					</tr>
					<tr>
						<th scope="row">주문일</th>
						<td><?=$order_date?></td>
					</tr>
					<tr>
						<th scope="row">주문자</th>
						<td><?=$stridM?></td>
					</tr>
					<tr>
						<th scope="row">결제</th>
						<td><?=$arpm[$_ord->paymethod[0]]?><?=$oc_coupon_price>0?" + 쿠폰":""?><?=$oc_use_point>0?" + 포인트":""?><?=$oc_use_epoint>0?" + E포인트":""?><?=$_ord->paymethod[0]=="O"?"(".$_ord->pay_data.")":"" ?></td>
					</tr>
					
				</tbody>
			</table>
		</div>
		<div class="content-r">
			<div class="cont-box">
				<table class="table-th-top">
				<caption></caption>
				<thead>
					<tr class="bg">
						<th scope="col"><strong>처리이력</strong></th>

					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<div>
								<strong>주문상태</strong>
								<div class="product-l">
									<span>상품별 보기</span>
									<select name="vi_prod_idx" class="select" onchange="javascript:status_view(this.value);">
										<option value="0">전체</option>
									<?
									$oc_prod_idx_arr		= explode("|", $oc_prod_idx);
									$oc_prod_name_arr	= explode("|", $oc_prod_name);
									for($k=0;$k<count($oc_prod_idx_arr);$k++) {
									?>
										<option value="<?=$oc_prod_idx_arr[$k]?>"><?=$oc_prod_name_arr[$k]?></option>
									<?}?>
									</select>
								</div>
							</div>
							<div class="scroll">
<?
	#주문처리내역
	$sts_sql = "SELECT * FROM
					tblorder_status_log
				WHERE
					ordercode='".$ordercode."'
					AND idx IN ('0','".str_replace("|","','",$oc_prod_idx)."')
					AND (proc_type='CS' OR (proc_type='AS' AND step_code NOT IN ('40','41','47','42')))
				ORDER BY idx, osl_no ";

	$sts_bf_idx						= "";
	$sts_bf_status_code		= "";
	$sts_bf_step_code			= "";

		$sts_result=pmysql_query($sts_sql,get_db_conn());
		while($sts_row=pmysql_fetch_object($sts_result)) {
			$regdate			= $sts_row->moddt!=''?$sts_row->moddt:$sts_row->regdt;
			$regdate_YMD	= substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
			$regdate_H			= substr($regdate,8,2);
			$regdate_I			= substr($regdate,10,2);
			$regdate_S			= substr($regdate,12,2);

			$sts_class_add	= $sts_row->idx>0?" hide":"";

			$sts_name_txt	= ($sts_row->proc_type=='AS'?"AS ":"").$sts_row->memo;
			if ($sts_row->reg_name) {
				$sts_name_txt	.= " [실행자 : ".$sts_row->reg_name."(".($sts_row->reg_id?$sts_row->reg_id:"비회원").")"."]";
			}
?>
								<div class='CLS_status_det csd_<?=$sts_row->idx?><?=$sts_class_add?>' osl_no="<?=$sts_row->osl_no?>" regdt="<?=$regdate?>"> <!-- [D] 리스트 반복 -->
									<p class="name"><?=$sts_name_txt?></p>
									<div class="date-sort clear">
										<div class="type calendar">
											<div class="box">
												<input type="text"name="regdt_ymd" title="일자별 시작날짜" style="width:120px;" value="<?=$regdate_YMD?>" OnClick="Calendar(event)" readonly>
												<!--<button type="button" OnClick="Calendar(event)">달력 열기</button>-->
											</div>
											<select name="regdt_h" class="ml_5 select">
											<?
											for($j=0;$j<=23;$j++) {
												$regdt_h_val	= $j<10?'0'.$j:$j;
												$regdt_h_sel	= $regdate_H==$regdt_h_val?" selected":"";
											?>
												<option value="<?=$regdt_h_val?>"<?=$regdt_h_sel?>><?=$regdt_h_val?></option>
											<?}?>
											</select>
											<span>시</span>
											<select name="regdt_i" class="select">
											<?
											for($j=0;$j<=59;$j++) {
												$regdt_i_val	= $j<10?'0'.$j:$j;
												$regdt_i_sel	= $regdate_I==$regdt_i_val?" selected":"";
											?>
												<option value="<?=$regdt_i_val?>"<?=$regdt_i_sel?>><?=$regdt_i_val?></option>
											<?}?>
											</select>
											<span>분</span>
											<select name="regdt_s" class="select">
											<?
											for($j=0;$j<=59;$j++) {
												$regdt_s_val	= $j<10?'0'.$j:$j;
												$regdt_s_sel	= $regdate_S==$regdt_s_val?" selected":"";
											?>
												<option value="<?=$regdt_s_val?>"<?=$regdt_s_sel?>><?=$regdt_s_val?></option>
											<?}?>
											</select>
											<span>초</span>
										</div>
									</div>
								</div><!-- // [D] 리스트 반복 -->
<?

			$sts_bf_idx						= $sts_row->idx;
			$sts_bf_status_code		= $sts_row->status_code;
			$sts_bf_step_code			= $sts_row->step_code;
		}
		pmysql_free_result($sts_result);
?>
							</div>
							<div class="btn-bottom"><a href="#" class="btn-type c1">처리이력 저장</a></div>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mt_40">
		<h3>주문상품</h3>
		<table class="table-th-top02">
			<caption>주문상품</caption>
			<colgroup>
				<col style="width:8%">
				<col style="width:15%">
				<col style="width:8%">
				<col style="width:10%">
				<col style="width:10%">				
				<col style="width:8%">
				<col style="width:10%">
				<col style="width:15%">				
				<col style="width:12%">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">주문번호</th>
					<th scope="col">상품명</th>
					<th scope="col">옵션</th>
					<th scope="col">정상가</th>
					<th scope="col">혜택가</th>
					<th scope="col">수량</th>
					<th scope="col">총금액</th>
					<th scope="col">주문상태</th>
					<th scope="col">출고매장</th>
					<th scope="col">배송정보</th>
				</tr>
			</thead>
			<tbody>
				<?
					$op_reorderidx				=  "";
					$t_op_price						=  0;
					$t_op_dc_coupon_price	=  0;
					$t_op_dc_use_point			=  0;
					$t_op_dc_use_epoint			=  0;
					$t_op_dc_price				=  0;
					$t_op_deli_price				=  0;
					$t_op_total_price				=  0;
					$t_op_total_quantity			=  0;
					while( $groupOpRow = pmysql_fetch_object( $groupOpRes ) ){

					//배송비로 인한 보여지는 가격 재조정
					$can_deli_price	= 0;
					$can_total_price	= (($groupOpRow->price + $groupOpRow->option_price) * $groupOpRow->option_quantity) - ($groupOpRow->coupon_price + $groupOpRow->use_point + $groupOpRow->use_epoint) + $groupOpRow->deli_price;

					list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$groupOpRow->productcode."%'"));
					//echo $od_deli_price;
					if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
						// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
						list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$groupOpRow->idx."' and op_step < 40 limit 1"));
						if ($op_idx) { // 상품이 있으면
							if ($groupOpRow->deli_price > 0) $can_total_price	= $can_total_price - $od_deli_price;
						} else {
							$can_deli_price	= $od_deli_price;
						}
					}
					
					#구매금액
					
					$t_op_price += $groupOpRow->sum_price;		
					
					$t_op_dc_coupon_price	+=  $groupOpRow->coupon_price;
					$t_op_dc_use_point			+=  $groupOpRow->use_point;
					$t_op_dc_use_epoint			+=  $groupOpRow->use_epoint;
					$t_op_dc_price	+=  $groupOpRow->coupon_price + $groupOpRow->use_point + $groupOpRow->use_epoint;
					if ($pc_type == 'ALL') {
						$t_op_deli_price	+=  $groupOpRow->deli_price;
						$t_op_total_price	+=  (($groupOpRow->price + $groupOpRow->option_price) * $groupOpRow->option_quantity) - ($groupOpRow->coupon_price + $groupOpRow->use_point + $groupOpRow->use_epoint) + $groupOpRow->deli_price;
					} else if ($pc_type == 'PART') {
						$t_op_deli_price	+=  $can_deli_price;
						$t_op_total_price	+=  $can_total_price;
					}

					$t_op_total_quantity += $groupOpRow->option_quantity;

					if($_ord->oldordno) {

						$reorderidx_sql	= "select op.idx as reorderidx 
							from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
							WHERE oi.ordercode='".$_ord->oldordno."' 
							and op.productcode='".$groupOpRow->productcode."' 
							AND op.op_step='44' 
							AND op.redelivery_type='G' ";
						if ($groupOpRow->opt1_name) $reorderidx_sql	.= "AND op.opt1_change='".$groupOpRow->opt1_name."' ";
						if ($groupOpRow->opt2_name) $reorderidx_sql	.= "AND op.opt2_change='".$groupOpRow->opt2_name."' ";
						if ($groupOpRow->text_opt_subject) $reorderidx_sql	.= "AND op.text_opt_subject_change='".$groupOpRow->text_opt_subject."' ";
						if ($groupOpRow->text_opt_content) $reorderidx_sql	.= "AND op.text_opt_content_change='".$groupOpRow->text_opt_content."' ";
						if ($groupOpRow->option_price_text) $reorderidx_sql	.= "AND op.option_price_text_change='".$groupOpRow->option_price_text."' ";

						//echo $reorderidx_sql;

						list($reorderidx)=pmysql_fetch_array(pmysql_query($reorderidx_sql));
						$op_reorderidx	= $reorderidx;
					}


					#상품정보
					$product_sql="select p.*, pb.brandname from tblproduct p left join tblproductbrand pb on (p.brand=pb.bridx) where p.productcode='".$groupOpRow->productcode."'";
					$product_result=pmysql_query($product_sql);
					$product_data=pmysql_fetch_object($product_result);

					#상품이미지
					$product_img = getProductImage($Dir.DataDir.'shopimages/product/', $product_data->tinyimage);

					if ($groupOpRow->oc_no) {
						list($oc_reg_type, $proc_type)=pmysql_fetch_array(pmysql_query("SELECT reg_type, proc_type FROM tblorder_cancel WHERE oc_no='".$groupOpRow->oc_no."' "));
						$oc_reg_type_txt="";
						if ($oc_reg_type =='admin') {
							$oc_reg_type_txt="(".$proc_type.")";
						} else if ($oc_reg_type =='user') {
							$oc_reg_type_txt="(고객)";
						} else if ($oc_reg_type =='api') {
							$oc_reg_type_txt="(API)";
						}
					}
					
					$erp_pc_code	= "[".$product_data->prodcode."-".$product_data->colorcode."]";
					
					?>
				<tr>
					<td><?=$_ord->ordercode?></td>
					<td class="ta_l">
						<div class="product-info clear">
							<a href="javascript:ProductDetail('<?=$product_data->productcode?>')">
							<img src="<?=$product_img?>" alt="">
							</a>
							<div class="pro-title">
								<a href="javascript:ProductDetail('<?=$product_data->productcode?>')">
								<strong><?=$product_data->brandname?></strong>
								<p><?=$groupOpRow->productname?></p>
								<p><?=$erp_pc_code?></p>
								</a>
							</div>
						</div>
					</td>
					<td><?=$groupOpRow->opt2_name?></td>
					<td><?=number_format($product_data->consumerprice)?>원</td>
					<td><?=number_format($groupOpRow->price)?>원</td>
					<td><?=$groupOpRow->option_quantity?></td>
					<td><?=number_format(($groupOpRow->price + $groupOpRow->option_price) * $groupOpRow->option_quantity)?>원</td>
					<td>
						<?if($groupOpRow->op_step < "40"){
							if($groupOpRow->deli_closed && $groupOpRow->op_step=="3"){
								echo "CJ배송완료";
							}else{
								echo GetStatusOrder("p", $groupOpRow->oi_step1, $groupOpRow->oi_step2, $groupOpRow->op_step, $groupOpRow->redelivery_type, $groupOpRow->order_conf);
							}
							if($oc_reg_type_txt){
								echo "<br>".$oc_reg_type_txt;
							}
						}else{
							$sql="SELECT * FROM tblorder_cancel WHERE oc_no='{$groupOpRow->oc_no}'";
							$result=pmysql_query($sql,get_db_conn());
							$_oci=pmysql_fetch_object($result);
							echo orderCancelStatusStep($groupOpRow->redelivery_type, $_oci->oc_step, $_oci->hold_oc_step);
							if($oc_reg_type_txt){
								echo "<br>".$oc_reg_type_txt;
							}
						}
						?>
					</td>
					<td><?=($storelist[$groupOpRow->store_code]->name !='')?$storelist[$groupOpRow->store_code]->name:'-'?></td>
					<td>
						<!--배송상태 변경 임시 비노출 처리 2016-10-13-->
						<!--
						<strong>
							<select name="chkdeli_com[<?=$groupOpRow->idx?>]" style="width:100;  font-size:9pt">
									<option value="">없음</option>
								<?php
									$deli_url="";
									$trans_num="";
									$company_name="";
									for($yy=0;$yy<count($delicomlist);$yy++) {
										if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
											if(ord($delicomlist[$yy]->dacom_code)) {
												echo "		<option value=\"{$delicomlist[$yy]->code}\"";
												if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
													echo " selected";
													$deli_url=$delicomlist[$yy]->deli_url;
													$trans_num=$delicomlist[$yy]->trans_num;
													$company_name=$delicomlist[$yy]->company_name;
												}
												echo ">{$delicomlist[$yy]->company_name}</option>\n";
											}
										} else {
											echo "		<option value=\"{$delicomlist[$yy]->code}\"";
											if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
												echo " selected";
												$deli_url=$delicomlist[$yy]->deli_url;
												$trans_num=$delicomlist[$yy]->trans_num;
												$company_name=$delicomlist[$yy]->company_name;
											}
											echo ">{$delicomlist[$yy]->company_name}</option>\n";
										}
									}
								?>
							</select>
						</strong>
						<p style="padding-top:5px;"><input type="text" name="deli_num_array[<?=$groupOpRow->idx?>]" value="<?=$groupOpRow->deli_num?>"></p>
						<p style="padding-top:5px;">
							<?if ($_ord->oi_step1 > 1 && $_ord->oi_step1 < 4){?>
							<input type='button' value='수정' onclick="javascript:ajaxValue('deliinfoup', '<?=$groupOpRow->idx?>');" style='padding:2px 5px 1px'>&nbsp;
							<?}?>
							<?
							if(ord($groupOpRow->deli_num) && ord($deli_url)) {
								if(ord($trans_num)) {
									$arrtransnum=explode(",",$trans_num);
									$pattern=array("[1]","[2]","[3]","[4]");
									$replace=array(substr($groupOpRow->deli_num,0,$arrtransnum[0]),substr($groupOpRow->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
									$deli_url=str_replace($pattern,$replace,$deli_url);
								} else {
									$deli_url.=$groupOpRow->deli_num;
								}
								echo "<input type='button' value='배송추적' onclick=\"DeliSearch('".$deli_url."')\" style='padding:2px 5px 1px'>";
							
							} else {
								echo "<input type=button value='배송추적' class='btn_blue' style=\"padding:2px 5px 1px\">";
							}	
							?>
							
						</p>
						-->

						<?
//exdebug("1 = ".$deli_url);

						if($groupOpRow->deli_num){
							echo "<div id='view_deli_div_".$groupOpRow->idx."'>";
							echo "<a href=\"javascript:change_deli('open_deli','".$ordercode."','".$groupOpRow->idx."')\"><strong id='view_deli_com_".$groupOpRow->idx."'>".$delicomlist_code[trim($groupOpRow->deli_com)]->company_name."</strong><p id='view_deli_num_".$groupOpRow->idx."'>".$groupOpRow->deli_num."</p></a>";
						?>	
							<p style="padding-top:5px;">
							<?
							if(ord($groupOpRow->deli_num) && ord($deli_url)) {
								if(ord($trans_num)) {
									$arrtransnum=explode(",",$trans_num);
									$pattern=array("[1]","[2]","[3]","[4]");
									$replace=array(substr($groupOpRow->deli_num,0,$arrtransnum[0]),substr($groupOpRow->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
									$deli_url=str_replace($pattern,$replace,$deli_url);
								} else {
									//$deli_url.=$groupOpRow->deli_num;
								}
								echo "<input type='button' value='배송추적' onclick=\"DeliSearch('".$deli_url."')\" style='padding:2px 5px 1px'>";
							
							} else {
								echo "<input type=button value='배송추적' class='btn_blue' style=\"padding:2px 5px 1px\">";
							}	
							?>
							
						</p>
						</div>
						<div id='modi_deli_div_<?=$groupOpRow->idx?>' style='display:none;'>
						<p style="padding-top:5px;">
							<select name="chkdeli_com[<?=$groupOpRow->idx?>]" style="width:100;  font-size:9pt">
									<option value="">없음</option>
								<?php
									$deli_url="";
									$trans_num="";
									$company_name="";
									for($yy=0;$yy<count($delicomlist);$yy++) {
										if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
											if(ord($delicomlist[$yy]->dacom_code)) {
												echo "		<option value=\"{$delicomlist[$yy]->code}\"";
												if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
													echo " selected";
													$deli_url=$delicomlist[$yy]->deli_url;
													$trans_num=$delicomlist[$yy]->trans_num;
													$company_name=$delicomlist[$yy]->company_name;
												}
												echo ">{$delicomlist[$yy]->company_name}</option>\n";
											}
										} else {
											echo "		<option value=\"{$delicomlist[$yy]->code}\"";
											if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
												echo " selected";
												$deli_url=$delicomlist[$yy]->deli_url;
												$trans_num=$delicomlist[$yy]->trans_num;
												$company_name=$delicomlist[$yy]->company_name;
											}
											echo ">{$delicomlist[$yy]->company_name}</option>\n";
										}
									}
								?>
							</select>
						</p>
						<p style="padding-top:5px;"><input type="text" name="deli_num_array[<?=$groupOpRow->idx?>]" style="width:100;  font-size:9pt" value="<?=$groupOpRow->deli_num?>"></p>
						<p style="padding-top:5px;"><input type='button' value='수정' onclick="javascript:change_deli('ajax_deli', '<?=$ordercode?>', '<?=$groupOpRow->idx?>','<?=$groupOpRow->op_step.''.$groupOpRow->deli_closed?>');" style='padding:2px 5px 1px'>&nbsp;<input type='button' value='취소' onclick="javascript:change_deli('close_deli', '<?=$ordercode?>', '<?=$groupOpRow->idx?>');" style='padding:2px 5px 1px'></p>
						</div>
						<?
						}else{	
							echo "-";
						}
						?>
					</td>
					


				</tr>
				<?}?>
				<tr>
					<td colspan="7">
					<!--배송상태 변경 임시 비노출 처리 2016-10-13-->
					<!--
					<?
						if ($_ord->oi_step1 > 1 && $_ord->oi_step1 < 4) { // 배송준비부터 노출
						
							echo "		배송정보 일괄등록 : \n";
							echo "		<select name=deli_com style=\"width:90;font-size:9pt\">\n";
							echo "		<option value=\"\">없음</option>\n";

							for($yy=0;$yy<count($delicomlist);$yy++) {
								if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
									if(ord($delicomlist[$yy]->dacom_code)) {
										echo "		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
									}
								} else {
									echo "		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
								}
							}
							echo "		</select>\n";
							echo "		<input type=text name=deli_num value=\"\" size=10 maxlength=20 style=\"font-size:9pt\" >\n"; // onkeyup=\"strnumkeyup(this)\"
							echo "<input type=button value='등록' onclick=\"ajaxValue('deliupdate')\" class='btn_blue' style=\"padding:2px 5px 1px\">\n";
							echo "		\n";
							
						}else{
							echo " ";
						}
					?>
					-->
					&nbsp;
					</td>
					<td colspan="3" style="text-align:left; padding-left:20px;"><b>총수량 : <?=$t_op_total_quantity?>개 <br>총 구매금액 : <?=number_format($t_op_price);?>원</b></td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="order-info">
		<h3>주문자</h3>
		<table class="table-th-left">
			<caption>주문자</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">주문자이름</th>
					<td><input type="text" name="" style="width:125px" class="input" value="<?=$_ord->sender_name?>" title="주문자 이름" readonly></td>
				</tr>
				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" name="" style="width:125px" class="input" value="<?=$_ord->sender_tel?>" title="휴대전화" readonly></td>
				</tr>
				<tr>
					<th scope="row">이메일</th>
					<td><input type="text" name="" style="width:200px" class="input" value="<?=$_ord->sender_email?>" title="주소" readonly></td>
				</tr>
				<!--
				<tr>
					<th scope="row">주소</th>
					<td>
						<div>
							<input type="text" name="" id="" title="우편번호 앞자리" value="111" style="width:40px;">
							<span class="dash">-</span>
							<input type="text" name="" id="" title="우편번호 뒷자리" value="111" style="width:40px;">
						</div>
						<div class="input-wrap">
							<input type="text" name="" id="" title="주소" value="서울시 강남구 논현동" style="width:350px;">
							<input type="text" name="" id="" title="상세주소"  value="123-45번지" style="width:350px;">
						</div>
					</td>
				</tr>-->
			</tbody>
		</table>
	</div>

	<div class="order-info">
		<h3>수취인</h3>
		<table class="table-th-left">
			<caption>수취인</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">수취인이름</th>
					<td><input type="text" name="receiver_name" style="width:125px" class="input" value="<?=$place_name?>" title="주문자 이름"></td>
				</tr>
				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" name="receiver_tel2" style="width:125px" class="input" value="<?=$place_tel?>" title="휴대전화"></td>
				</tr>
				<tr>
					<th scope="row">주소</th>
					<td>
						<div>
							<input type="text" name="receiver_zipcode" id="receiver_zipcode" title="우편번호 앞자리" value="<?=$zonecode?>" style="width:60px;text-align:center; padding-left:0px;" readonly>  <span class="btn-small"><a href="javascript:openDaumPostcode();" class="btn-type c2">우편번호 찾기</a></span>
							
						</div>
						<div class="input-wrap">
							<input type="text" name="receiver_address" id="receiver_address" title="주소" value="<?=$address?>" style="width:600px;">
							
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">배송메모</th>
					<td><textarea style="width:100%; height:50px;" readonly><?=$_ord->order_msg2?></textarea></td>
				</tr>
			</tbody>
		</table>
	</div>

<?php
		if($_ord->oldordno) {
				$reordercode	= $_ord->oldordno;
				if (ord($op_reorderidx)) $coupon_add_qry	= " AND op.idx='".$op_reorderidx."' ";
		} else {
				$reordercode	= $ordercode;
				if (ord($idx)) $coupon_add_qry	= " AND op.idx='".$idx."' ";
		}
		
		# 쿠폰
		$couponSql = "SELECT op.productname, op.opt1_name, op.opt2_name, op.text_opt_subject, op.text_opt_content, ci.coupon_name, co.dc_price, ci.coupon_type FROM tblcouponinfo ci ";
		$couponSql.= "JOIN tblcoupon_order co ON co.coupon_code = ci.coupon_code ";
		$couponSql.= "left join tblorderproduct op ON op.idx = co.op_idx ";
		$couponSql.= "WHERE co.ordercode = '".$reordercode."' {$coupon_add_qry} order by op.vender, op.idx";
		//echo $couponSql;
		$coupon_use_html	= "";

		$couponRes =  pmysql_query( $couponSql, get_db_conn() );
		$couponTotal	= pmysql_num_rows($couponRes);
		if ($couponTotal > 0) {
			while( $couponRow = pmysql_fetch_object( $couponRes ) ) {
			# 상품 옵션 정보 저장 및 출력
			

				$cp_opt_name	= "";
				if( strlen( trim( $couponRow->opt1_name ) ) > 0 ) {
					$cp_opt1_name_arr	= explode("@#", $couponRow->opt1_name);
					$cp_opt2_name_arr	= explode(chr(30), $couponRow->opt2_name);
					$s_cnt	= 0;
					for($s=0;$s < sizeof($cp_opt1_name_arr);$s++) {
						if ($cp_opt2_name_arr[$s]) {
							if ($s_cnt > 0) $cp_opt_name	.= " / ";
							$cp_opt_name	.= $cp_opt1_name_arr[$s].' : '.$cp_opt2_name_arr[$s];
							$s_cnt++;
						}
					}
				}
															
				if( strlen( trim( $couponRow->text_opt_subject ) ) > 0 ) {
					$cp_text_opt_subject_arr	= explode("@#", $couponRow->text_opt_subject);
					$cp_text_opt_content_arr	= explode("@#", $couponRow->text_opt_content);

					for($s=0;$s < sizeof($cp_text_opt_subject_arr);$s++) {
						if ($cp_text_opt_content_arr[$s]) {
							if ($cp_opt_name != '') $cp_opt_name	.= " / ";
							$cp_opt_name	.= $cp_text_opt_subject_arr[$s].' : '.$cp_text_opt_content_arr[$s];
						}
					}
				}	
				$coupon_use_html	.= "
					<tr>
						<td>".$couponRow->coupon_name."</td>
						<td>".$couponRow->productname."</td>
						<td>".$cp_opt_name."</td>
						<td>".($couponRow->coupon_type == '9'?"-":number_format( $couponRow->dc_price )."원")."</td>";
				if($_ord->oldordno) {
					$coupon_use_html	.= "
							<td>불가 (재주문건)</td>
						</tr>";
				} else {
					$coupon_use_html	.= "
							<td>가능</td>
						</tr>";
				}
			}
			pmysql_free_result( $couponRes );
		}
?>
	<div class="mt_40">
		<h3>결제정보</h3>
		<table class="table-th-left">
			<caption>결제정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">결제수단</th>
					<td><?=$arpm[$_ord->paymethod[0]]?><?=$t_op_dc_coupon_price>0?" + 쿠폰":""?><?=$t_op_dc_use_point>0?" + 포인트":""?><?=$t_op_dc_use_epoint>0?" + E포인트":""?><?=$_ord->paymethod[0]=="O"?"(".$_ord->pay_data.")":"" ?></td>
				</tr>
				<tr>
					<th scope="row">실결제금액<br>/총주문금액</th>
					<td>실결제 금액 : <strong class="point-txt"><?=number_format($t_op_total_price)?>원</strong> <span class="point-txt"><?if($t_op_dc_price>0 || $t_op_deli_price>0) {?> (<?=$t_op_dc_price>0?number_format($t_op_dc_price)."원 할인":""?><?=$t_op_dc_price>0&&$t_op_deli_price>0?" / ":""?><?=$t_op_deli_price>0?number_format($t_op_deli_price)."원 배송비":""?>)<?}?></span> · 총구매 금액 : <strong class="point-txt2"><?=number_format($t_op_price)?>원</strong></td>
				</tr>
				<!--
				<tr>
					<th scope="row">환불금액</th>
					<td><strong class="point-txt"><?=number_format($t_op_total_price)?>원</strong></td>
				</tr>-->
				<tr>
					<th scope="row">쿠폰할인내역</th>
					<td>
			<?
					if ($coupon_use_html) {
			?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table-th-top02 border-t">
						<tbody>
							<tr>
								<th scope="col">쿠폰명</th>
								<th scope="col">상품</th>
								<th scope="col">옵션</th>
								<th scope="col">할인액</th>
								<th scope="col">복원</th>
							</tr>
							<?=$coupon_use_html?>
						</tbody>
					</table>
			<?
					} else {
						echo "-";
					}
			?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="order-info">
		<h3>관리자 메모</h3>
		<table class="table-th-left">
			<caption>관리자 메모</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				
				<tr>
					<th scope="row">관리자 메모</th>
					<td><textarea name=memo0 cols=110 rows=3 style="font-size:9pt;vertical-align: middle;"><?=$message[0]?></textarea></td>
				</tr>
			</tbody>
		</table>
	</div>
<?
if($oc_no != 0){
?>
	<div class="mt_40">
		<h3>기타</h3>
		<table class="table-th-left">
			<caption>기타</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">CS 메모</th>
					<td>
						<textarea wrap=off  id="ir1" id="cs_memo" name="cs_memo" label="문의내용" style="width:100%; height:300px"></textarea>
<!--
						<div class="add-file-cover">
							<div id="filename0"></div> <!-- 파일 업로드시 파일 주소 출력 -->
<!--							<input type="file" id="add_file" name="file[]" onchange="filenamein(this,'0')">
						</div>
						<div class="btn-wrap1"><span><a href="javascript:add()" class="btn-type1">이미지추가</a></span></div>

						<div id="add_file_div"></div> <!-- 이미지 추가 -->

						<?if($memo_while){?>
						<div class="txt-box">
							<?
								foreach($memo_while as $mw=>$mwv){
									#접수일
									$memo_date=substr($mwv['regdt'],'0','4').'-'.substr($mwv['regdt'],'4','2').'-'.substr($mwv['regdt'],'6','2').' '.substr($mwv['regdt'],'8','2').':'.substr($mwv['regdt'],'10','2').':'.substr($mwv['regdt'],'12','2');

								?>
							<h4><strong><?=$mwv["admin_name"]?>(<?=$mwv["admin_id"]?>)</strong> <?=$memo_date?> <?if($mwv["admin_id"]==$_ShopInfo->id){?>&nbsp;<div class="btn-wrap1"><span><a href="javascript:oc_ajax_proc('memo_del', '<?=$mw?>')" class="btn-type1" style="width:50px;">삭제</a></span></div><?}?></h4>
							<div class="cont">
								<?if($mwv["filename"]){
									foreach($mwv["filename"] as $mwf=>$mwfv){
									?>
									<img src="<?=$filepath.$mwfv?>" style='width:700px'>
									<?if($mwv["admin_id"]==$_ShopInfo->id){?>&nbsp;<div class="btn-wrap1"><span><a href="javascript:oc_ajax_proc('memo_img_del', '<?=$mwf?>')" class="btn-type1" style="width:50px;">삭제</a></span></div><?}?></span><br><br>
								<?
									}
								}?>
								<?=$mwv["cs_memo"]?>
							</div>
								<?
								}
								?>
						</div>
						<?}?>
					
					
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	}
?>
	<div class="mt_40 btn-set">
		<a href="javascript:order_submit()" class="btn-type c1">저장</a>
		<a href="javascript:window.close();" class="btn-type c2">닫기</a>
	</div>

</section> <!-- // .online-as -->
<input type='hidden' name='oc_no' value="<?=$oc_no?>">
</form>
<form name='form_memo' action="<?=$_SERVER['PHP_SELF']?>" method='post'>
<input type='hidden' name='mode'>
<input type='hidden' name='ordercode'>
<input type='hidden' name='om_no'>
</form>
<form name='form2' action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
</form>
<form name='form_deil' id='form_deil' action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type="hidden" name="type" id="type" value="deliinfoup">
<input type="hidden" name="ordercode" id="ordercode" value="<?=$_ord->ordercode?>">
<input type="hidden" name=delimailtype id=delimailtype value="N">
<input type="hidden" name=deli_com id=deli_com>
<input type="hidden" name=deli_num id=deli_num>
<input type="hidden" name=idxs id=idxs>
</form>
<script type="text/javascript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		},
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});


	function filenamein(obj, num){
		$("#filename"+num).html(obj.value);
	}

	var count=1;
	function add(){
		if (count == 5) {
			alert("5개까지만 첨부가 가능합니다.");
		} else {
			var html = "<div>";
			html += "<div class='add-file-cover'>";
			html += "<div id='filename"+count+"'></div>";
			html += "<input type='file' id='add_file' name='file[]' onchange='filenamein(this,"+count+")'>";
			html += "</div>";
			html += "</div>";
			count++;
			$("#add_file_div").append(html);
		}
	}

</script>

<?=$onload?>
</body>
</html>