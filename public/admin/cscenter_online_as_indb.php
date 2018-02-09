<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."conf/cscenter_ascode.php");
include_once($Dir."lib/file.class.php");
include("access.php");

$mode=$_POST["mode"];
$place_type=$_POST["place_type"]; //수령지 변경 유무
$place_name=$_POST["place_name"];
$place_mobile=$_POST["place_mobile"];
$place_zipcode=$_POST["place_zipcode"];
$place_addr=$_POST["place_addr"];
$receipt_store=$_POST["receipt_store"];
$receipt_type=$_POST["receipt_type"];
$depreciation_type=$_POST["depreciation_type"];
$repairs_type=$_POST["repairs_type"];
$cash_type=$_POST["cash_type"];
$cash_detail_type=$_POST["cash_detail_type"];
$cash_detail_tel1=$_POST["cash_detail_tel1"];
$cash_detail_tel2=$_POST["cash_detail_tel2"];
$cash_detail_tel3=$_POST["cash_detail_tel3"];
$cash_detail_num1=$_POST["cash_detail_num1"];
$cash_detail_num2=$_POST["cash_detail_num2"];
$cash_detail_num3=$_POST["cash_detail_num3"];
$requests_text=$_POST["requests_text"];
$delivery_cost=$_POST["delivery_cost"];
$delivery_receipt=$_POST["delivery_receipt"];
$cs_memo=str_replace("'", "''", $_POST['cs_memo']);
if ($cs_memo =='<br>') $cs_memo					="";
$nowdate=date("YmdHis");




//경로
$filepath = $Dir.DataDir."shopimages/cscenter/";
//파일
$asfile = new FILE($filepath);

$up_asfile = $asfile->upFiles();




if($mode=="insert"){
	$ordercode=$_POST["ordercode"];
	$productcode=$_POST["productcode"];
	$productidx=$_POST["productidx"];
	$as_type=$_POST["as_type"];
	
	$insname="";
	$insvalue="";

	//수령지 변경시 변경될 주소를 입력받는다.
	if($place_type){
		$insname[]="place_zipcode";
		$insname[]="place_addr";
		$insname[]="place_name";
		$insname[]="place_mobile";

		$insvalue[]=$place_zipcode;
		$insvalue[]=$place_addr;
		$insvalue[]=$place_name;
		$insvalue[]=$place_mobile;
	}

	//접수유형이 유상수선일경우 유상수선비, 현금영수증 발급 정보를 등록해준다.
	if($receipt_type=="1"){
		$insname[]="repairs_type";
		$insname[]="cash_type";
		
		$insvalue[]=$repairs_type;
		$insvalue[]=$cash_type;

		if($cash_type=="Y"){
			if($cash_detail_type=="1") $cash_detail_num=$cash_detail_tel1."-".$cash_detail_tel2."-".$cash_detail_tel3;
			else $cash_detail_num=$cash_detail_num1."-".$cash_detail_num2."-".$cash_detail_num3;

			$insname[]="cash_detail_type";
			$insname[]="cash_detail_num";
			
			$insvalue[]=$cash_detail_type;
			$insvalue[]=$cash_detail_num;
		}

	}

	//AS코드 생성
	list($codenum)=pmysql_fetch("select as_code from tblcsasreceiptinfo order by no desc limit 1");

	if($codenum){
		$as_code=$codenum;
		$as_code=substr($as_code,"2");
		$as_code=$as_code+1;
		$as_code="AS".str_pad($as_code, 8, "0", STR_PAD_LEFT);
	}else {
		$as_code="AS00000001";
	}

	// as정보 등록
	$ins_sql="insert into tblcsasreceiptinfo 
	(as_code, as_ordercode, as_productcode, as_idx, as_type, place_type, receipt_store, receipt_type, depreciation_type, requests_text, delivery_cost, delivery_receipt, step_code, modregdt, regdt";
	if($insname){
		$ins_sql.=", ".implode(", ", $insname);
	}
	$ins_sql.=" ) values ('".$as_code."', '".$ordercode."', '".$productcode."', '".$productidx."', '".$as_type."', '".$place_type."', '".$receipt_store."', '".$receipt_type."', '".$depreciation_type."', '".$requests_text."', '".$delivery_cost."', '".$delivery_receipt."', 'progress_a01', '".$nowdate."', '".$nowdate."'";
	
	if($insvalue){
		$ins_sql.=", '".implode("', '", $insvalue)."'";
	}
	$ins_sql.=" )RETURNING no ";

	$ins_idx = pmysql_fetch_object(pmysql_query( $ins_sql, get_db_conn() ));

	if($ins_idx){
		//진행상태로고등록

		$log_sql="insert into tblcsaslog (receipt_no, step_code, step_name, admin_id, admin_name, regdt) values ('".$ins_idx->no."', 'progress_a01', '".$as_progress['progress_a01']."', '".$_ShopInfo->id."', '".$_ShopInfo->name."', '".$nowdate."')";
		
		pmysql_query($log_sql);

		//cs메모
		if($up_asfile["file"][0]["v_file"] || $cs_memo){
			$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$ins_idx->no."', '".$cs_memo."', 'progress_a01', '".$as_progress['progress_a01']."', '".$_ShopInfo->id."', '".$_ShopInfo->name."', '".$nowdate."', 'onlineas') RETURNING no";
			$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
			
			//파일첨부
			
			if($up_asfile["file"][0]["v_file"]){
				foreach($up_asfile as $fa=>$fav){
					foreach($fav as $na=>$nav){
						$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$ins_idx->no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->id."', '".$nowdate."', 'onlineas')";
						pmysql_query($file_sql);
					}
				}
			}
		}		
	}

	alert_go("등록되었습니다.","cscenter_online_as_pop.php?no={$ins_idx->no}");


}else if($mode=="request"){
	$receipt_no=$_POST["receipt_no"]; 
	$step_code=$_POST["step_code"]; //진행상태
	$c_return=$_POST["c_return"]; //회송 접수유형
	$as_process=$_POST["as_process"]; //수선 
	$as_process_cost=$_POST["as_process_cost"]; //수선비용
	$as_process_title=$_POST["as_process_title"]; //수선 기타
	$as_process_text=$_POST["as_process_text"]; //수선 기타 가격
	$as_returngoods=$_POST["as_returngoods"]; //as반품 처리내용
	$as_return_type=$_POST["as_return_type"]; //as 반품 타입
	$as_return_text=$_POST["as_return_text"]; //as 반품 처리 텍스트
	$c_reviewreturn=$_POST["c_reviewreturn"]; //심의회송 처리내용
	$as_outreviewgoods_1=$_POST["as_outreviewgoods_1"]; //외부심의반품, 반품처리, 반품등록, 로케이션이동 구분
	$as_outreviewgoods_2=$_POST["as_outreviewgoods_2"]; //외부심의반품, 반품처리, 반품등록, 로케이션이동 상세
	$as_outreviewreturn=$_POST["as_outreviewreturn"]; //외부심의 회송 처리내용
	$complete_cost=$_POST["complete_cost"]; //수선비
	$complete_type=$_POST["complete_type"]; //as처리정보 처리내용
	$complete_detail=$_POST["complete_detail"]; //as처리정보 기타상세처리
	$complete_store=$_POST["complete_store"]; //업체명(밴더사)
	$complete_delicode=$_POST["complete_delicode"]; //발송운송장 (택배사)
	$complete_delinumber=$_POST["complete_delinumber"]; //발송운송장 (번호)

	$end_proc_yn			= "Y";
	list($oa_end_step)=pmysql_fetch_array(pmysql_query("select end_step from tblcsasreceiptinfo WHERE no='".$receipt_no."' "));

	if ($oa_end_step == '2' || $oa_end_step == '3') $end_proc_yn = "N";

	$end_step				= $_POST["end_step"];
	$now_end_step		= $_POST["now_end_step"];

	if ($end_step == $now_end_step) {
		if ($end_step == '2' || $end_step == '3') $end_proc_yn = "N";
	}

	$end_bankcode		= "";
	$end_bankaccount	= "";
	$end_bankuser		= "";

	$end_option1			= "";
	$end_option2			= "";
	$end_text_opt_s		= "";
	$end_op_text_opt_c	= "";

	if ($end_step == '1' || $end_step == '2') {

		$end_bankcode		= $_POST["bankcode"];
		$end_bankaccount	= $_POST["bankaccount"];
		$end_bankuser		= $_POST["bankuser"];

		$end_rechange_type		= '0';
		$end_return_store_code	= $_POST["sel_return_store_code1"];

	} else if ($end_step == '3') {
		
		$end_rechange_type		= $_POST["rechange_type"];
		$end_return_store_code	= $_POST["sel_return_store_code2"];

		$end_option1			= $_POST["option1"];
		$end_option2			= $_POST["option2"];
		$end_text_opt_s		= $_POST["text_opt_s"];
		$end_op_text_opt_c	= $_POST["op_text_opt_c"];

	} else {

		$end_rechange_type		= '0';
		$end_return_store_code	= "";
	}

	$updarray="";

	//수령지 변경유무
	$updarray[]="place_type='".$place_type."'";
	//접수매장
	$updarray[]="receipt_store='".$receipt_store."'";
	//접수유형
	$updarray[]="receipt_type='".$receipt_type."'";
	//감가적용
	$updarray[]="depreciation_type='".$depreciation_type."'";
	//요청사항
	$updarray[]="requests_text='".$requests_text."'";
	//고객부담 택배비
	$updarray[]="delivery_cost='".$delivery_cost."'";
	//택배비 수령
	$updarray[]="delivery_receipt='".$delivery_receipt."'";
	//진행상태
	$updarray[]="step_code='".$step_code."'";
	//as처리정보(처리내용)
	$updarray[]="complete_type='".$complete_type."'";
	//as처리정보(처리상세)
	$updarray[]="complete_detail='".$complete_detail."'";
	//as처리정보(수선비)
	$updarray[]="complete_cost='".$complete_cost."'";
	//as처리정보(업체명)
	$updarray[]="complete_store='".$complete_store."'";
	//as처리정보(발송택배사)
	$updarray[]="complete_delicode='".$complete_delicode."'";
	//as처리정보(송장번호)
	$updarray[]="complete_delinumber='".$complete_delinumber."'";
	//업데이트 날짜
	$updarray[]="modregdt='".$nowdate."'";
	
	

	//수령지 변경시 변경될 주소를 입력받는다.
	if($place_type){
		$updarray[]="place_zipcode='".$place_zipcode."'";
		$updarray[]="place_addr='".$place_addr."'";
		$updarray[]="place_name='".$place_name."'";
		$updarray[]="place_mobile='".$place_mobile."'";
	}

	//진행상태별 등록 조건 변경
	if($as_progress_class[$step_code]){
		if($as_progress_class[$step_code]=="return"){
	
			$updarray[]="c_return='".$c_return."'";
	
		}else if($as_progress_class[$step_code]=="repair"){
			//기존에 수선상태 삭제후 재등록
			$repair_del="delete from tblcsasreceiptdetail where receipt_no='".$receipt_no."' and process_name='".$as_progress_class[$step_code]."'";
			pmysql_query($repair_del);
			if($as_process){
				foreach($as_process as $dap=>$dapv){
					$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', '".$dap."','' ,'".str_replace(",","",$as_process_cost[$dap])."', '".$nowdate."', '".$as_progress_class[$step_code]."')";
					pmysql_query($dtail_ins);
				}
			}
			//기타 작성시 등록
			if($as_process_title){
				$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', 'process_text','".$as_process_title."' ,'".str_replace(",","",$as_process_text)."', '".$nowdate."', '".$as_progress_class[$step_code]."')";
				pmysql_query($dtail_ins);
			}

		}else if($as_progress_class[$step_code]=="returngoods"){
			//기존에 반품처리 삭제후 재등록
			$repair_del="delete from tblcsasreceiptdetail where receipt_no='".$receipt_no."' and process_name='".$as_progress_class[$step_code]."'";
			pmysql_query($repair_del);

			if($as_returngoods){
				foreach($as_returngoods as $dar=>$darv){
					$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', '".$darv."','' ,'', '".$nowdate."', '".$as_progress_class[$step_code]."')";
					pmysql_query($dtail_ins);
				}
			}
			
			//as 반품처리 등록
			if($as_return_type){
				$updarray[]="as_return_type='".$as_return_type."'";
				$updarray[]="as_return_text='".$as_return_text[$as_return_type]."'";
			}
			
		
		}else if($as_progress_class[$step_code]=="reviewreturn"){

			$updarray[]="c_reviewreturn='".$c_reviewreturn."'";
		
		}else if($as_progress_class[$step_code]=="outreviewgoods"){

			//기존에 외부심의반품 삭제후 재등록
			$repair_del="delete from tblcsasreceiptdetail where receipt_no='".$receipt_no."' and process_name='".$as_progress_class[$step_code]."'";
			pmysql_query($repair_del);

			if($as_outreviewgoods_1){
				foreach($as_outreviewgoods_1 as $dao1=>$daov1){
					$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', '".$daov1."','' ,'', '".$nowdate."', '".$as_progress_class[$step_code]."')";
					pmysql_query($dtail_ins);
				}
			}
			if($as_outreviewgoods_2){
				foreach($as_outreviewgoods_2 as $dao2=>$daov2){
					$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', '".$daov2."','' ,'', '".$nowdate."', '".$as_progress_class[$step_code]."')";
					pmysql_query($dtail_ins);
				}
			}

		}else if($as_progress_class[$step_code]=="outreviewreturn"){
			//기존에 외부심의회송처리내용 삭제후 재등록
			$repair_del="delete from tblcsasreceiptdetail where receipt_no='".$receipt_no."' and process_name='".$as_progress_class[$step_code]."'";
			pmysql_query($repair_del);

			if($as_outreviewreturn){
				foreach($as_outreviewreturn as $daor=>$daorv){
					$dtail_ins="insert into tblcsasreceiptdetail (receipt_no, as_code, process_title, process_price, regdt, process_name) values ('".$receipt_no."', '".$daorv."','' ,'', '".$nowdate."', '".$as_progress_class[$step_code]."')";
					pmysql_query($dtail_ins);
				}
			}

		}
	}

	//접수유형이 유상수선일경우 유상수선비, 현금영수증 발급 정보를 등록해준다.
	if($receipt_type=="1"){
		$updarray[]="repairs_type='".$repairs_type."'";
		$updarray[]="cash_type='".$cash_type."'";
		
		if($cash_type=="Y"){
			if($cash_detail_type=="1") $cash_detail_num=$cash_detail_tel1."-".$cash_detail_tel2."-".$cash_detail_tel3;
			else $cash_detail_num=$cash_detail_num1."-".$cash_detail_num2."-".$cash_detail_num3;

			$updarray[]="cash_detail_type='".$cash_detail_type."'";
			$updarray[]="cash_detail_num='".$cash_detail_num."'";
		}else{
			$updarray[]="cash_detail_type='1'";
			$updarray[]="cash_detail_num=''";
		}

	}else{
		$updarray[]="repairs_type='F'";
		$updarray[]="cash_type='N'";
		$updarray[]="cash_detail_type='1'";
		$updarray[]="cash_detail_num=''";
	}
	if ($end_proc_yn	== 'Y') { 
		//처리결과
		$updarray[]="end_step='".$end_step."'";
		$updarray[]="end_bankcode='".$end_bankcode."'";
		$updarray[]="end_bankaccount='".$end_bankaccount."'";
		$updarray[]="end_bankuser='".$end_bankuser."'";
		$updarray[]="end_rechange_type='".$end_rechange_type."'";
		$updarray[]="end_return_store_code='".$end_return_store_code."'";
		$updarray[]="end_option1='".$end_option1."'";
		$updarray[]="end_option2='".$end_option2."'";
		$updarray[]="end_text_opt_s='".$end_text_opt_s."'";
		$updarray[]="end_op_text_opt_c='".$end_op_text_opt_c."'";
	}

	//업데이트
	$upd_qry="update tblcsasreceiptinfo set ".implode(", ",$updarray)." where no='".$receipt_no."'";
	//echo $upd_qry;
	//exit;
	if($upd_check=pmysql_query($upd_qry)){
		
		list($step_count)=pmysql_fetch("select count(*) from tblcsaslog where step_code='".$step_code."' and receipt_no='".$receipt_no."'");

		if(!$step_count){
			//로그등록
			$log_sql="insert into tblcsaslog (receipt_no, step_code, step_name, admin_id, admin_name, regdt) values ('".$receipt_no."', '".$step_code."', '".$as_progress[$step_code]."', '".$_ShopInfo->id."', '".$_ShopInfo->name."', '".$nowdate."')";
		}
		
		pmysql_query($log_sql);

		//cs메모
		if($up_asfile["file"][0]["v_file"] || $cs_memo){
			$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$receipt_no."', '".$cs_memo."', '".$step_code."', '".$as_progress[$step_code]."', '".$_ShopInfo->id."', '".$_ShopInfo->name."', '".$nowdate."', 'onlineas') RETURNING no";
			$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
			
			//파일첨부
			
			if($up_asfile["file"][0]["v_file"]){
				foreach($up_asfile as $fa=>$fav){
					foreach($fav as $na=>$nav){
						$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$receipt_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->id."', '".$nowdate."', 'onlineas')";
						pmysql_query($file_sql);
					}
				}
			}
		}
	}

	alert_go("처리되었습니다..","cscenter_online_as_pop.php?no={$receipt_no}");

}else if($mode=="fin_proc"){
	$c_re_type					= $_POST['re_type'];
	$c_ordercode				= $_POST['ordercode'];
	$c_idxs						= $_POST['idxs'];
	$c_sel_code				= $_POST['sel_code'];
	$c_sel_sub_code		= $_POST['sel_sub_code'];
	$c_paymethod			= $_POST['paymethod'];
	$c_rechange_type		= $_POST['rechange_type'];
	$c_return_store_code	= $_POST['return_store_code'];
	$c_bankcode				= $_POST['bankcode'];
	$c_bankaccount			= $_POST['bankaccount'];
	$c_bankuser				= $_POST['bankuser'];
	$c_opt1_changes		= $_POST['opt1_changes'];
	$c_opt2_changes		= $_POST['opt2_changes'];
	$c_opt2_pt_changes		= $_POST['opt2_pt_changes'];
	$c_opt_text_s_changes	= $_POST['opt_text_s_changes'];
	$c_opt_text_c_changes	= $_POST['opt_text_c_changes'];

	$c_receipt_name		= $_POST["receipt_name"];
	$c_receipt_tel			= $_POST["receipt_tel"];
	$c_receipt_mobile	= $_POST["receipt_mobile"];
	$c_receipt_addr		= $_POST["receipt_addr"];
	$c_receipt_post5		= $_POST["receipt_post5"];

	$c_pgcancel_type			= $_POST['pgcancel_type'];
	$c_pgcancel_res_code	= $_POST['pgcancel_res_code'];
	$c_pgcancel_res_msg		= $_POST['pgcancel_res_msg'];

	$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

	$proc_type	= "AS";

	$in_date		= date("YmdHis");

	$msg = "";
	$msgType = "0";

	//상품을 가져온다.
	$deliveryCheck_sql = "SELECT a.ordercode, a.oi_step1, a.oi_step2, b.idx, b.redelivery_type, b.op_step, b.oc_no FROM tblorderinfo a, tblorderproduct b WHERE a.ordercode=b.ordercode ";
	$deliveryCheck_sql.= "AND a.ordercode='".trim($c_ordercode)."' ";
	$deliveryCheck_sql.= "AND b.idx = '".trim($c_idxs)."' ";
	//echo $deliveryCheck_sql;
	//exit;
	$deliveryCheck_res		= pmysql_query($deliveryCheck_sql,get_db_conn());
	$deliveryCheck			= pmysql_fetch_array($deliveryCheck_res);
	if ($deliveryCheck) {
		if ($deliveryCheck['op_step'] != '3' && $deliveryCheck['op_step'] != '4') { // 배송중, 배송완료가 아닌 경우			
			if ($deliveryCheck['op_step'] == '0' || $deliveryCheck['op_step'] == '1' || $deliveryCheck['op_step'] == '2') { // 배송중, 배송완료일 경우
				$msg = "배송전 상품으로 반품 또는 교환이 불가능한 주문입니다.";
			} else if ($deliveryCheck['op_step'] == '40' || $deliveryCheck['op_step'] == '41' || $deliveryCheck['op_step'] == '42' || $deliveryCheck['op_step'] == '44') { // 환불/교환/반품 CS진행이 있을 경우
				if ($deliveryCheck['op_step'] == '44') {
					if($deliveryCheck['redelivery_type'] == "N"){
						$msg = "취소완료가 되어있는 주문입니다.";
					}else if($deliveryCheck['redelivery_type'] == "Y"){
						$msg = "반품완료가 되어있는 주문입니다.";
					}else if($deliveryCheck['redelivery_type'] == "G"){
						$msg = "교환완료가 되어있는 주문입니다.";
					}
				} else {
					if($deliveryCheck['redelivery_type'] == "N"){
						$msg = "배송전 상품으로 반품 또는 교환이 불가능한 주문입니다.";
					} else {
						orderCancelRestore($exe_id, $c_ordercode, $deliveryCheck['oc_no'], $proc_type );
					}
				}
			}else{
				$msg = "반품 또는 교환이 불가능한 주문입니다.";
			}
		}

		if ($msg == "") {
			if ($c_re_type == 'B') {			// 반품
				$redelivery_type	= "Y";
			} else if ($c_re_type == 'C') {	// 교환
				$redelivery_type	= "G";
			}

			//상품을 가져온다.
			list($c_oi_step1, $c_oi_step2, $c_op_step)=pmysql_fetch_array(pmysql_query("SELECT a.oi_step1, a.oi_step2, b.op_step FROM tblorderinfo a, tblorderproduct b WHERE a.ordercode=b.ordercode AND a.ordercode='".trim($c_ordercode)."' AND b.idx = '".trim($c_idxs)."' "));

			// 기존에 사용하던 반품신청 필드를 반품또는 교환으로 업데이트 한다.
			$redliveryUpdate_sql = "UPDATE tblorderproduct SET ";
			$redliveryUpdate_sql.= "redelivery_type='{$redelivery_type}' , redelivery_date='".date("YmdHis")."' ";
			$redliveryUpdate_sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
			$redliveryUpdate_sql.= "AND idx='".trim($c_idxs)."' ";

			pmysql_query($redliveryUpdate_sql,get_db_conn());
			if(pmysql_error()){
				$msg = "접수 실패. 관리자에게 문의해주세요.";
			}else{
								
				// 반품및 교환 신청을 한다.
				$c_oc_no	= orderCancel($exe_id, $c_ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_sel_code, '', $c_bankcode, $c_bankaccount, $c_bankuser, '', $c_re_type, $c_opt1_changes, $c_opt2_changes, $c_opt2_pt_changes, $c_opt_text_s_changes, $c_opt_text_c_changes, '', '', '', $c_sel_sub_code, '', $c_receipt_name, $c_receipt_tel, $c_receipt_mobile, $c_receipt_addr, $c_receipt_post5, $c_rechange_type, $c_return_store_code, '0', '', '', '', $proc_type );

				//exdebug($c_oc_no);
				//exit;

				// 상품별 반품또는 교환신청이 아닌 상태가 있는지 체크한다.
				list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND redelivery_type != '{$redelivery_type}'"));
				if ($op_rt_cnt == 0) { // 전체가 반품또는 교환신청일 경우
					$sql = "UPDATE tblorderinfo SET redelivery_type='{$redelivery_type}', redelivery_date='{$in_date}' WHERE ordercode='".trim($c_ordercode)."' ";
					pmysql_query($sql,get_db_conn());
				}
				
				if ($c_re_type == 'B') {			// 반품
					orderCancelAccept($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );			// 접수
					orderCancelGetPickup($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );		// 제품도착
					orderCancelPickupFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );		// 승인

					$sql = "UPDATE tblorderproduct SET deli_gbn='E' ";
					$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
					$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
					//echo $sql;

					if(pmysql_query($sql,get_db_conn())) {
						// 반송완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'E' AND op_step != '44'"));
						if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 반송상태일 경우
							$sql = "UPDATE tblorderinfo SET deli_gbn='E' WHERE ordercode='".trim($c_ordercode)."' ";
							//echo $sql;
							pmysql_query($sql,get_db_conn());
						}
					}

					
					if (((strstr("CV", $c_paymethod) && $c_pgcancel_type == '1') || strstr("G", $c_paymethod))) { // 카드, 계좌이체 또는 임직원 포인트일 경우 

						// PG상태값 업데이트 (카드, 계좌이체) 및 취소처리
						$rfee						= "";
							
						//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND oc_no != '".$c_oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));			
						if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
							//PG취소가 완료된 수수료를 가져온다.
							list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
							if ($op_rfee_amt == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
								$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
								$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
								pmysql_query($sql,get_db_conn());
								//exdebug($sql);
							}
						}

						//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
						list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$c_ordercode}' and oc_no='{$c_oc_no}' group by ordercode"));

						// 최종 환불금액을 계산한다. (실결제금액 - 환불수수료)
						if ($rfee > 0) {
							$rprice	= $sum_price - $rfee;
						} else {
							$rprice	= $sum_price;
						}

						// 취소테이블의 취소상태를 완료로 변경한다.
						$sql   = " UPDATE tblorder_cancel SET pgcancel='Y', rprice = '{$rprice}' ";
						if ($rfee) $sql  .= " , rfee='{$rfee}' ";
						$sql.= "WHERE oc_no='".$c_oc_no."' ";
						pmysql_query($sql,get_db_conn());
						//exdebug($sql);

						orderCancelFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, '', '', '', '', $rfee, '', $proc_type );
			
						//ERP로 환불완료데이터를 보낸다.
						sendErporderReturn($c_ordercode, $c_oc_no, $c_idxs);
						
						$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
						$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
						$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
						//exdebug($sql);

						if(pmysql_query($sql,get_db_conn())) {
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
							if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								//PG취소가 완료된 수수료를 가져온다.
								list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
								$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
								if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
									$sql .= ", pay_admin_proc='C' ";
								}
								$sql .= "WHERE ordercode='".trim($c_ordercode)."' ";
								//exdebug($sql);
								pmysql_query($sql,get_db_conn());
							}
						}

						//sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
						//sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );
						
					} else {

						orderCancelFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, '', $c_bankcode, $c_bankaccount, $c_bankuser, '', '', $proc_type );

						// 반품 환불
						sendErporderReturn($c_ordercode, $c_oc_no, $c_idxs);

						$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
						$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
						$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
						//echo $sql;

						if(pmysql_query($sql,get_db_conn())) {
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
							if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
								$sql .= ", pay_admin_proc='C' ";
								$sql .= "WHERE ordercode='".trim($c_ordercode)."' ";
								//echo $sql;
								pmysql_query($sql,get_db_conn());
							}
						}
						//sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
						//sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );			
					}

					$msg	="처리 되었습니다.";
					$msgType	= '1';

				} else if ($c_re_type == 'C') {	// 교환
					orderCancelAccept($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );					// 접수
					orderCancelGetPickup($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );				// 제품도착
					orderCancelReorderPickupFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type );		// 승인
					orderCancelReorderFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, $proc_type);				// 완료

					$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
					$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
					$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
					//echo $sql;

					if(pmysql_query($sql,get_db_conn())) {
						//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
						if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
							$sql = "UPDATE tblorderinfo SET deli_gbn='C' WHERE ordercode='".trim($c_ordercode)."' ";
							//echo $sql;
							pmysql_query($sql,get_db_conn());
						}
					}
					//sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
					//sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );		

					$msg	="처리 되었습니다.";
					$msgType	= '1';
				}			
			}
		}
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
	echo $msg;
}

?>