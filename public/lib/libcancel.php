<?
// 주문 전체 상태 배열로 설정(log를 위해)
$o_status_step		= array(
								'order'	=> array(
										0	=> "주문접수",
										1	=> "결제완료",
										2	=> "배송준비중",
										3	=> "배송중",
										4	=> "배송완료",
										45	=> "재고부족",
										50	=> "결제시도",
										51	=> "PG확인요망",
										54	=> "결제실패",
										),
								'cancel'	=> array(
										41	=> "취소접수",
										44	=> "취소완료",
										),
								'cancel_and_refund'	=> array(
										41	=> "취소접수",
										44	=> "취소완료",
										),
								'regoods_and_rechange'	=> array(
										40	=> "반품/교환신청",
										41	=> "반품/교환접수",
										42	=> "반품/교환승인",
										44	=> "반품/교환완료",
										46	=> "반품/교환요청",
										47	=> "제품도착",
										48	=> "반품/교환보류",
										49	=> "반품/교환철회",
										),
								);
// 주문 전체 상태 배열로 설정(log를 위해)
$op_status_step		= array(
								'order'	=> array(
										0	=> "주문접수",
										1	=> "결제완료",
										2	=> "배송준비중",
										3	=> "배송중",
										4	=> "배송완료",
										45	=> "재고부족",
										50	=> "결제시도",
										51	=> "PG확인요망",
										54	=> "결제실패",
										),
								'cancel'	=> array(
										41	=> "취소접수",
										44	=> "취소완료",
										),
								'regoods'	=> array(
										40	=> "반품신청",
										41	=> "반품접수",
										42	=> "반품승인",
										44	=> "반품완료",
										46	=> "교환요청",
										47	=> "제품도착",
										48	=> "반품보류",
										49	=> "반품철회",
										),
								'rechange'	=> array(
										40	=> "교환신청",
										41	=> "교환접수",
										42	=> "교환승인",
										44	=> "교환완료",
										46	=> "반품요청",
										47	=> "제품도착",
										48	=> "교환보류",
										49	=> "교환철회",
										),
								);

/**
 * 주문/주문상품 전체 상태변경내역 처리
 * exe_id				: 실행자 정보(아이디|이름|타입)
 * type					: 로그타입(주문: o, 주문상품 : p)
 * ordercode			: 주문코드
 * idx						: 상품 idx
 * step1					: 주문 상태
 * step2					: 주문취소 상태
 * redelivery_type	: 취소구분(N : 취소,Y : 반품, G : 교환)
 * proc_type			: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
 * regdate				: 등록일
**/
function orderStepChangeLog($exe_id, $type, $ordercode, $idx='', $step1='', $step2='', $redelivery_type='', $proc_type='', $regdate='') {
	global $o_status_step, $op_status_step;
	
	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];	
	
	if ($idx =='') $idx	= 0;
	if ($step2 =='') $step2	= 0;
			
	$status_code	= "order";
	if ($step2 > 0) {
		if ($type=='o') {
			if ($step1 =='0') $status_code = "cancel";
			if ($step1 =='1' || $step1 =='2') $status_code = "cancel_and_refund";
			if ($step1 =='3' || $step1 =='4') $status_code = "regoods_and_rechange";
		} else if ($type=='p') {
			if ($step2 >= 40 && $step2 < 50) {
				if ($redelivery_type == 'N') $status_code = "cancel";
				if ($redelivery_type == 'Y') $status_code = "regoods";
				if ($redelivery_type == 'G') $status_code = "rechange";
			}
		}
	}
	
	if ($type=='o') {
		$status_step_code		= $step2=='0'?$step1:$step2;
		$status_memo			= $o_status_step[$status_code][$status_step_code];
	} else if ($type=='p') {
		$status_step_code		= $step2;
		$status_memo			= $op_status_step[$status_code][$status_step_code];
	}

	$date=date("YmdHis");
	if ($regdate) $date = $regdate;
	$status_log_sql = "INSERT INTO tblorder_status_log(
	ordercode	,
	idx	,
	status_code	,
	step_code,
	memo,
	reg_id,
	reg_name,
	reg_type,
	regdt) VALUES (
	'{$ordercode}',
	'{$idx}',
	'{$status_code}',
	'{$status_step_code}',
	'{$status_memo}',
	'{$reg_id}',
	'{$reg_name}',
	'{$reg_type}',
	'{$date}') RETURNING osl_no";

	$row2 = pmysql_fetch_array(pmysql_query($status_log_sql,get_db_conn()));
	$osl_no = $row2[0];

	// 처리 구분이 있을경우 업데이트 해준다.
	if($proc_type)	{
		pmysql_query(" UPDATE tblorder_status_log SET proc_type = '{$proc_type}' WHERE osl_no='".trim($osl_no)."'",get_db_conn());
	}
}

/**
 * 주문정보 상태변경
 * exe_id				: 실행자 정보(아이디|이름|타입)
 * ordercode			: 주문코드
 * step1					: 주문 상태
 * step2					: 주문취소 상태
 * oc_change_yn	: 반품/교환 전환시 (Y : 전환)
 * oc_status_step	: 반품/교환 전환시 상태값
 * proc_type			: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
 * req_type				: 주문신청 구분(Y : 주문신청건)
**/
function orderStepUpdate($exe_id, $ordercode, $step1, $step2="", $oc_change_yn='', $oc_status_step='', $proc_type='', $req_type='' ) {
	global $o_step;
	$op_step_array		= array(
					0	=> "주문접수",
					1	=> "결제완료",
					2	=> "배송준비중",
					3	=> "배송중",
					4	=> "배송완료",
					40	=> "취소신청",
					41	=> "취소접수",
					42	=> "취소진행",
					44	=> "취소완료",
					50	=> "결제시도",
					51	=> "PG확인요망",
					54	=> "결제실패",
					);
	
	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//현재 주문의 상태값을 가져온다.
	list($order_mem_id, $old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select id, oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".trim($ordercode)."'"));

    if($old_step2 == '0' && $step2 == '0') $step_pass = "Y";
    else $step_pass = "N";

	if (($old_step1 != $step1 && ($old_step2 != $step2 || $step_pass == "Y")) || $req_type == 'Y') { // 상태변경이 있을 경우에만

		$qry_where	= "";

		if ($step1 != '') $qry_where[] = "oi_step1 = '{$step1}' ";
		if ($step2 != '') $qry_where[] = "oi_step2 = '{$step2}' ";

		// 주문의 상태값을 변경한다.
		$osu_sql = " UPDATE tblorderinfo SET ";
		$osu_sql.= implode(", ",$qry_where);
		$osu_sql.= " WHERE ordercode='".trim($ordercode)."'";
		//echo $osu_sql;
		pmysql_query($osu_sql,get_db_conn());

        // step2가 없으면 주문기준으로 값이 변경되어지므로, 상품도 주문상태인 것들은 전부 같이 변경되어짐.
        if($step2 =='') {

			// 주문 상품정보를 가져온다.
			$op_sql		= "SELECT * FROM tblorderproduct Where ordercode='".trim($ordercode)."' And op_step < 40";
			$op_result	= pmysql_query($op_sql,get_db_conn());
			$op_total	= pmysql_num_rows($op_result);
			while($op_row=pmysql_fetch_object($op_result)) {
				//메모에 등록된 내용을 설정한다.
				$osu_memo	= $op_step_array[$op_row->op_step]."=>". $op_step_array[$step1];

				$date=date("YmdHis");
				$log_sql = "INSERT INTO tblorderproduct_log(
				ordercode	,
				idx	,
				step_prev	,
				step_next,
				memo,
				reg_id,
				reg_name,
				reg_type,
				regdt) VALUES (
				'{$ordercode}',
				'{$op_row->idx}',
				'{$op_row->op_step}',
				'{$step1}',
				'{$osu_memo}',
				'{$reg_id}',
				'{$reg_name}',
				'{$reg_type}',
				'{$date}')";
				@pmysql_query($log_sql,get_db_conn());

				// 주문/주문상품 전체 상태변경내역 처리
				$op_step2	= $step1;
				/*if ($op_row->op_step =='0' && $op_step2 == '1') {
					$regdate	= substr($ordercode, 0, 14);
					orderStepChangeLog($exe_id, 'p', $ordercode, $op_row->idx, '', $op_row->op_step, $op_row->redelivery_type, $proc_type, $regdate);
				}*/
				if ($op_row->op_step >= 40 && $op_row->op_step > $op_step2 && $oc_change_yn =='' ) $op_step2	= '49';
				if($oc_status_step) $op_step2	= $oc_status_step;
				orderStepChangeLog($exe_id, 'p', $ordercode, $op_row->idx, '', $op_step2, $op_row->redelivery_type, $proc_type);
			}
			pmysql_free_result($op_result);

            $sql = "Update tblorderproduct Set op_step = ".$step1." Where ordercode='".trim($ordercode)."' And op_step < 40";
            pmysql_query($sql,get_db_conn());
        }

		if( !pmysql_error() ){
			
			// 메일 알림 설정
			global $_ShopInfo;
			$_data=new ShopData($_ShopInfo);
			$_data=$_data->shopdata;
				
			// 도메인 정보
			$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
			$row        = pmysql_fetch_object(pmysql_query($sql));
			$shopurl    = str_replace("http://", "", $row->shopurl)."/";
			
			//알림톡삽입 2017-05-04
			$alim = new ALIM_TALK();
			if( $step1 == '1' ){
				# 주문 결제완료
				if( strstr( "BOQ", $order_paymethod[0] ) ){
					# 가상계좌 / 무통장 / 에스크로
					$alim->makeAlimTalkSearchData($ordercode, 'WEB02');
					
					// 매장픽업시 업주 알림톡 delivery_type = 1 [WEB16]
					$alim_owner = new ALIM_TALK();
					$alim_owner->makeAlimTalkSearchData($ordercode, 'WEB16');
					
					SendBankMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, '' ,$ordercode);	// 입금확인
				} else if( strstr( "CVPM", $order_paymethod[0] ) ){
					# 신용카드 / 계좌이체 / 휴대폰 /
					$alim->makeAlimTalkSearchData($ordercode, 'WEB03');
					
					// 매장픽업시 업주 알림톡 delivery_type = 1 [WEB16]
					$alim_owner = new ALIM_TALK();
					$alim_owner->makeAlimTalkSearchData($ordercode, 'WEB16');

					SendBankMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, '' ,$ordercode);	// 입금확인
				}
			}else if( $step1 == '3' ){
				# 배송중 일괄 업데이트
                // 템플릿 검수 문제로 되돌림 2017-03-10 유동혁
				//$alim->makeAlimTalkSearchData($ordercode, 'WEB04');
				//SendDeliMail( $shopname, $shopurl, $mail_type, $info_email, $ordercode, $deli_com, $deli_num, $delimailtype, $idx='' );	// 배송중
			}

			// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
			$date=date("YmdHis");

			if(!$step1) $step1 = $old_step1; // 스탭이 없으면 기존 스탭을 유지한다.
            if( $step2 == '' ) $step2 = '0'; // 2016-03-13 db 입력 오류 null이 허용 안됨 -> 0으로 변경

			//메모에 등록된 내용을 설정한다.
			$osu_memo	= $o_step[$old_step1][$old_step2]."=>". $o_step[$step1][$step2];
			//exdebug($osu_memo);
			//exit;
			$log_sql = "INSERT INTO tblorder_log(
			ordercode	,
			step1_prev	,
			step2_prev	,
			step1_next,
			step2_next,
			memo,
			reg_id,
			reg_name,
			reg_type,
			regdt) VALUES (
			'{$ordercode}',
			'{$old_step1}',
			'{$old_step2}',
			'{$step1}',
			'{$step2}',
			'{$osu_memo}',
			'{$reg_id}',
			'{$reg_name}',
			'{$reg_type}',
			'{$date}')";
			@pmysql_query($log_sql,get_db_conn());

			// 주문/주문상품 전체 상태변경내역 처리
			$o_step2	= $step2;
			/*if ($old_step1 =='0' && $old_step2=='0' && $step1 == '1' && $step2=='0') {
				$regdate	= substr($ordercode, 0, 14);
				orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step1, $o_step2,'', $proc_type, $regdate);
			}*/
			
			if ($old_step2 > $step2 && $oc_change_yn =='') $o_step2	= '49';
			if($oc_status_step) $o_step2	= $oc_status_step;
			orderStepChangeLog($exe_id, 'o', $ordercode, '', $step1, $o_step2,'', $proc_type);
			
			/* 
			 * 20170504 기존 알림톡 사용안함 기능별로 분리됨 [libcancel.php & payresult.php]
			// 알림톡 
			$Dir="../";
			include_once($Dir."alimtalk/alimtalk.class.php");
			$ordertype = "";
			switch ($step1){
				case 0:
					$ordertype = "WEB01";
					break;
				case 1:
					$ordertype = "WEB03";
					break;
				case 3:
					$ordertype = "WEB04";
					break;
				case 4:
					$ordertype = "WEB05";
					break;
				case 40:
					$ordertype = "WEB07";
					break;
				case 41:
					$ordertype = "WEB08";
					break;
				case 44:
					$ordertype = "WEB10";
					break;
			}
			$temp_ordercode = $ordercode;
			$alim = new ALIM_TALK();
			$alim->makeAlimTalkSearchData($temp_ordercode, $ordertype);
			 */
		}
	}

	return true;

}

/**
 * 주문 상품정보 상태변경
 * exe_id						: 실행자 정보(아이디|이름|타입)
 * ordercode					: 주문코드
 * idx								: 상품 idx
 * step							: 주문상태
 * oc_no						: 주문취소 번호
 * opt1_change				: 교환옵션1
 * opt2_change				: 교환옵션2
 * opt2_pt_change			: 교환 옵션별 가격 구분자 ||
 * opt_text_s_change		: 교환 텍스트 옵션 옵션명
 * opt_text_c_change		: 교환 텍스트 옵션 옵션값
 * sync_type					: 싱크커머스 체크용 값
 * oc_change_yn			: 반품/교환 전환시 (Y : 전환)
 * oc_status_step			: 반품/교환 전환시 상태값
 * proc_type					: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderProductStepUpdate($exe_id, $ordercode, $idx, $step, $oc_no='', $opt1_change='', $opt2_change='', $opt2_pt_change='', $opt_text_s_change='', $opt_text_c_change=''  , $sync_type='', $oc_change_yn='', $oc_status_step='', $proc_type='') {	//2016-10-06 libe90 싱크커머스 체크용 값 추가
    //global $ci;
	$op_step_array		= array(
					0	=> "주문접수",
					1	=> "결제완료",
					2	=> "배송준비중",
					3	=> "배송중",
					4	=> "배송완료",
					40	=> "취소신청",
					41	=> "취소접수",
					42	=> "취소진행",
					44	=> "취소완료",
					50	=> "결제시도",
					51	=> "PG확인요망",
					54	=> "결제실패",
					);

	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	$idx_arr	= explode("|", $idx);		// 상품배열을 분리한다.

	for($j=0;$j < count($idx_arr);$j++) {
		// 주문 상품정보 상태를 가져온다.
		list($old_op_step,$delivery_type,$redelivery_type)=pmysql_fetch_array(pmysql_query("select op_step, delivery_type, redelivery_type from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx='".trim($idx_arr[$j])."' "));	//2016-10-06 libe90 싱크커머스 전송 체크용 발송구분호출

		//메모에 등록된 내용을 설정한다.
		$osu_memo	= $op_step_array[$old_op_step]."=>". $op_step_array[$step];

		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorderproduct_log(
		ordercode	,
		idx	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$ordercode}',
		'{$idx_arr[$j]}',
		'{$old_op_step}',
		'{$step}',
		'{$osu_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());

		// 주문/주문상품 전체 상태변경내역 처리
		$op_step2	= $step;
		if ($old_op_step > $op_step2 && $oc_change_yn =='' ) $op_step2	= '49';
		if ($oc_status_step) $op_step2	= $oc_status_step;
		if ($oc_status_step=='46') $redelivery_type	= $redelivery_type=='Y'?'G':'Y';
		orderStepChangeLog($exe_id, 'p', $ordercode, $idx_arr[$j], '', $op_step2, $redelivery_type, $proc_type);


        # 2016-11-28 싱크커머스로 배송준비중 전송 2016-11-28 유동혁
        if ($step == '2' &&  $sync_type == 'M') {
             $Sync = new Sync();
            $arrayDatax = array(
                'ordercode'     => $ordercode,
                'sync_status'   => 'S',
                'sync_idx'      => $idx_arr[$j]
            );
            $srtn = $Sync->StatusChange($arrayDatax);
        }

        # 2016-11-28 싱크커머스로 배송중 전송 2016-11-28 유동혁
        if ($step == '3' &&  $sync_type == 'M') {

            # 배송업체
            $delicomlist=array();
            $delComSql="SELECT * FROM tbldelicompany ORDER BY company_name ";
            $delComRes =pmysql_query( $delComSql, get_db_conn() );
            while( $delComRow =pmysql_fetch_object( $delComRes ) ) {
                $delicomlist[] = $delComRow;
                $delicomlist_code[trim($delComRow->code)] = $delComRow;
                
            }
            pmysql_free_result( $delComRow );

            # 배송정보
            $deliSql = "
                SELECT deli_com, deli_num, deli_date
                FROM tblorderproduct
                WHERE ordercode='".$ordercode."' AND idx='".$idx_arr[$j]."'
            ";
            $deliRes = pmysql_query( $deliSql, get_db_conn() );
            $deliRow = pmysql_fetch_object( $deliRes );
            pmysql_free_result( $deliRes );

            $tmpCompanyname = $delicomlist_code[trim($deliRow->deli_com)]->company_name;
            $tmpDeliDate = substr( $deliRow->deli_date, 0, 4 ).'-'.substr( $deliRow->deli_date, 4, 2 ).'-'.substr( $deliRow->deli_date, 6, 2 ); // 년-월-일
            $tmpDeliDate.= ' '.substr( $deliRow->deli_date, 8, 2 ).':'.substr( $deliRow->deli_date, 10, 2 ).':'.substr( $deliRow->deli_date, 12, 2 ); // 시:분:초

            // sync 정보값
            $arrayDatax = array(
                'ordercode'          => $ordercode,                                     // 주문코드
                'delivery_num'       => $deliRow->deli_num,                             // 송장번호
                'delivery_com'       => trim($deliRow->deli_com),                       // 배송회사코드
                'delivery_name'      => $tmpCompanyname,                                // 배송회사 이름
                'delivery_send_date' => $tmpDeliDate,                                   // 입력일
                'sync_status'        => 'Y',                                            // 주문상태
                'sync_idx'           => $idx_arr[$j]                                    // 주문상세 번호
            );
            $Sync = new Sync();
            $srtn = $Sync->StatusChange($arrayDatax);
        }

		
		//배송완료시 erp로 전송 -> 구매확정시 변경되도록 위치 변경 mypage_orderlist.ajax.php 2017-04-18
		/*
		if ($step == '4'){
			sendErpOrderEndInfo($ordercode, $idx_arr[$j]);
		}*/
		if ($step == '4' && $delivery_type != '0' &&  $sync_type == '') {	//2016-10-06 libe90 싱크커머스로 배송완료 전송
            // $delivery_type != '0' && 슈마커는 전체가 싱크커머스로 넘어감 2016-10-12 유동혁
			$Sync = new Sync();
			$arrayDatax = array(
				'ordercode' => $ordercode,
				'delivery_num' => '',
				'sync_status' => 'F',
				'sync_idx' => $idx_arr[$j]
			);
			$srtn = $Sync->StatusChange($arrayDatax);
		}

		if( $step=='44' && $delivery_type != '0'){	//2016-10-06 libe90 싱크커머스로 취소완료 전송
            // $delivery_type != '0' && 슈마커는 전체가 싱크커머스로 넘어감 2016-10-12 유동혁
			//$redelivery_type >> 'N' >> 취소, 'G'  >>  교환,  'Y' >> 반품
			$sync_idx=$idx_arr[$j];
			// 반품
			if( $redelivery_type == 'Y' ){
				$sync_status='K';
			// 교환
			} elseif( $redelivery_type == 'G' ) {
				$sync_status='G';
			// 취소
			} else {
				$sync_status='C';
			}
			$Sync = new Sync();
			$arrayDatax=array(
				'ordercode'=>$ordercode,
				'delivery_num'=>'',
				'sync_status'=>$sync_status,
				'sync_idx'=>$sync_idx
			);
			$Sync->StatusChange($arrayDatax);
		}
	}

	$idx	= str_replace("|", ",", $idx);

	// 주문의 상태값을 변경한다.
	$sql	 = " UPDATE tblorderproduct SET op_step = '{$step}' ";
	if ($oc_no) $sql	.= ", oc_no='{$oc_no}' ";
	if ($opt1_change) $sql	.= ", opt1_change='{$opt1_change}' ";
	if ($opt2_change) $sql	.= ", opt2_change='{$opt2_change}' ";

	if ($opt1_change) { // 교환상품이 있을경우 자체코드를 불러온다
		list($self_goods_code_change)=pmysql_fetch_array(pmysql_query("SELECT b.self_goods_code FROM tblorderproduct a LEFT JOIN tblproduct_option b ON a.productcode=b.productcode AND b.option_code='{$opt2_change}' where  a.ordercode='".trim($ordercode)."' AND a.idx='{$idx}' "));
		$sql	.= ", self_goods_code_change='{$self_goods_code_change}' ";
	} else {
		$sql	.= ", self_goods_code_change=self_goods_code ";
	}

	if ($opt2_pt_change) $sql	.= ", option_price_text_change='{$opt2_pt_change}' ";
	if ($opt_text_s_change) $sql	.= ", text_opt_subject_change='{$opt_text_s_change}' ";
	if ($opt_text_c_change) $sql	.= ", text_opt_content_change='{$opt_text_c_change}' ";
	//$sql	.= "WHERE ordercode='".trim($ordercode)."' AND idx='".trim($idx)."' ";
    $sql	.= "WHERE ordercode='".trim($ordercode)."' AND idx in (".trim($idx).") "; // 2016-02-11 jhjeong 같은 주문의 다른 상품일 경우도 해결하기 위해...
	pmysql_query($sql,get_db_conn());
    //echo $sql;

	if( !pmysql_error() ){
		// 메일 알림 설정
		global $_ShopInfo;
		$_data=new ShopData($_ShopInfo);
		$_data=$_data->shopdata;
			
		// 도메인 정보
		$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
		$row        = pmysql_fetch_object(pmysql_query($sql));
		$shopurl    = str_replace("http://", "", $row->shopurl)."/";
		
		$alim = new ALIM_TALK();
        // 알림톡 삽입 2017-05-04
        // $alim->makeAlimTalkSearchData($ordercode, $idx, $oc_no);
		if( $step == '3' ){
			# 주문 배송중
			$alim->makeAlimTalkSearchData($ordercode, 'WEB04', $idx, $oc_no);
		} else if( $step == '4' ){
			# 주문 배송완료
			$alim->makeAlimTalkSearchData($ordercode, 'WEB05', $idx, $oc_no);
		} else if( $step == '40' ){
			# 주문 취소접수
// 			$alim->makeAlimTalkSearchData($ordercode, 'WEB07', $idx, $oc_no);
// 			$alim->makeAlimTalkSearchData($ordercode, 'WEB06', $idx, $oc_no);
			if( $redelivery_type == 'N' ){
				$alim->makeAlimTalkSearchData($ordercode, 'WEB11', $idx, $oc_no);	// WEB11 신규 주문취소
			}
			// $redelivery_type >> 'N' >> 취소, 'G'  >>  교환,  'Y' >> 반품
			// SendCancelMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $ordercode, 'Y', 'Y', '', 0);	// 취소신청
		} else if( $step == '41' ){
// 			if($step < 2){
// 				# 배송 전 주문 반품접수
// // 				$alim->makeAlimTalkSearchData($ordercode, 'WEB06', $idx, $oc_no);
// 				if( $redelivery_type == 'N' ){
// 					$alim->makeAlimTalkSearchData($ordercode, 'WEB11', $idx, $oc_no);
// 					error_log("temp-error".$step.",| ".$redelivery_type,3,"/tmp/error_log_sinwon.log"); 
// 				} 
// 				// $redelivery_type >> 'N' >> 취소, 'G'  >>  교환,  'Y' >> 반품
// 			}else{
// 				# 배송 후 주문 반품접수
// 				$alim->makeAlimTalkSearchData($ordercode, 'WEB08', $idx, $oc_no);
// 			}

// 			if( $redelivery_type == 'N' ){
// 				$alim->makeAlimTalkSearchData($ordercode, 'WEB11', $idx, $oc_no);	// 고객취소
// 			}
		} else if( $step == '42' ){
			# 주문 환불접수
			$alim->makeAlimTalkSearchData($ordercode, 'WEB09', $idx, $oc_no);
		} else if( $step == '44' ){
			# 주문 취소 / 환불환료
// 			$alim->makeAlimTalkSearchData($ordercode, 'WEB06', $idx, $oc_no);
			if( $redelivery_type == 'N' ){
				$alim->makeAlimTalkSearchData($ordercode, 'WEB11', $idx, $oc_no);
			}
			$alim->makeAlimTalkSearchData($ordercode, 'WEB10', "", $oc_no);

		}
// echo $step;
// exit();

		//현재 주문의 취소상태가 아닌값을 가져온다.
		list($op_step_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_step_cnt from (select op_step from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step < 40 group by op_step) as foo"));


		if ($op_step_cnt ==0) {		//모두 취소상태인 경우
			//현재 주문의 취소상태중 취소완료상태가 아닌 카운트를 가져온다
			list($op_cancel_44_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_cancel_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step != 44"));
			if ($op_cancel_44_cnt ==0) {		//모두 취소완료상태인 경우
				$oi_step2	= 44;
				orderStepUpdate($exe_id, $ordercode, '', $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
			} else {
				//현재 주문의 취소상태중 취소진행상태가 아닌 카운트를 가져온다
				list($op_cancel_42_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_cancel_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step != 42"));
				if ($op_cancel_42_cnt ==0) {		//모두 취소진행상태인 경우
					$oi_step2	= 42;
					orderStepUpdate($exe_id, $ordercode, '', $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
				} else {
					//현재 주문의 취소상태중 취소접수상태가 아닌 카운트를 가져온다
					list($op_cancel_41_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_cancel_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step != 41"));
					if ($op_cancel_41_cnt ==0) {		//모두 취소접수상태인 경우
						$oi_step2	= 41;
						orderStepUpdate($exe_id, $ordercode, '', $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
					} else {
						//현재 주문의 취소상태중 취소신청상태가 아닌 카운트를 가져온다
						list($op_cancel_40_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_cancel_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step != 40"));
						if ($op_cancel_40_cnt ==0) {		//모두 취소신청상태인 경우
							$oi_step2	= 40;
							orderStepUpdate($exe_id, $ordercode, '', $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
						}
					}
				}
			}
		} else {							//주문상태인 경우
            // 2016-11-08 아래에서 위로 이동..주문상태가 update 안되어있어서 아래 쿠폰 지급 부분에서 쿠폰 발급이 안된다고 함.
			if ($op_step_cnt ==1) { // 주문상태가 모두 동일할경우
				list($op_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step < 40 limit 1"));
				$oi_step2	= '0';
				orderStepUpdate($exe_id, $ordercode, $op_step, $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
			}

			if ($step == '4') { // 구매확정(배송완료)일 경우
				include_once("coupon.class.php");
				$ci = new CouponInfo();
				# 첫구매
				$ci->set_coupon( '4' );
				$ci->search_coupon( '', $ordercode ); // 쿠폰 확인
				$ci->set_couponissue(); // 등록 테이블
				$ci->insert_couponissue(); // 발급
				# 주문 수량 충족
				$ci->set_coupon( '12' );
				$ci->search_coupon( '', $ordercode ); // 쿠폰 확인
				$ci->set_couponissue(); // 등록 테이블
				$ci->insert_couponissue(); // 발급
				# 주문 금액 충족
				$ci->set_coupon( '13' );
				$ci->search_coupon( '', $ordercode ); // 쿠폰 확인
				$ci->set_couponissue(); // 등록 테이블
				$ci->insert_couponissue(); // 발급
				#상품구매 후기
				if( strlen( $idx ) > 0 ){
					$ci->set_coupon( '11' );
					$ci->search_coupon( '', $idx ); // 쿠폰 확인
					$ci->set_couponissue(); // 등록 테이블
					$ci->insert_couponissue(); // 발급
				}
			}
            /* 2016-11-08 위로 이동.
			if ($op_step_cnt ==1) { // 주문상태가 모두 동일할경우
				list($op_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step < 40 limit 1"));
				$oi_step2	= '0';
				orderStepUpdate($exe_id, $ordercode, $op_step, $oi_step2, $oc_change_yn, $oc_status_step, $proc_type );
			}
            */
		}
	}
}

/**
 * 주문 반품/교환 요청, 취소 요청/완료
 * exe_id					: 실행자 정보(아이디|이름|타입)
 * ordercode				: 주문코드
 * idxs						: 상품 idx들 예) 1|2|3
 * oi_step1					: 주문 상태코드
 * oi_step2					: 주문취소 상태코드
 * op_step					: 주문상품 상태코드
 * paymethod				: 결제방식
 * code						: 사유 코드(lib.php 의 $oc_code)
 * memo					: 상세 사유
 * bankcode				: 환불 은행 코드(lib.php 의 $oc_bankcode)
 * bankaccount			: 환불 은행 계좌번호
 * bankuser				: 환불 은행 예금주
 * bankusertel				: 환불 연락처
 * re_type					: 타입 (NULL: 환불, C:교환, B: 반품)
 * opt1_changes			: 교환옵션1
 * opt2_changes			: 교환옵션2
 * opt2_pt_changes		: 교환 옵션별 가격 구분자 ||
 * opt_text_s_changes	: 교환 텍스트 옵션 옵션명
 * opt_text_c_changes	: 교환 텍스트 옵션 옵션값
 * pgcancel_type		: pg 결제 취소 타입
 * pgcancel_res_code	: pg 결제 취소 결과코드
 * pgcancel_res_msg	: pg 결제 취소 결과메시지
 * sub_code				: 사유 상세코드(lib.php 의 $oc_reason_code) - 구분자 |
 * admin_memo			: 관리자 사유메모
 * receipt_name			: 반품/교환시 수령대상자 이름
 * receipt_tel				: 반품/교환시 수령대상자 전화번호
 * receipt_mobile		: 반품/교환시 수령대상자 휴대전화번호
 * receipt_addr			: 반품/교환시 수령대상자 주소
 * receipt_post5			: 반품/교환시 수령대상자 신주소 5자리
 * rechange_type		: 교환시 교환방법(0 : 없음, 1 : 동일상품교환, 2 : 다른 사이즈로 교환)
 * return_store_code	: 반품/교환시 회송매장코드

 * return_deli_price		: 반품/교환시 고객부탐 택배비
 * return_deli_receipt	: 반품/교환시 택배비 수령
 * return_deli_type		: 반품/교환시 택배비부담 방법
 * return_deli_memo	: 반품/교환시 택배비 메모
 * proc_type				: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancel($exe_id, $ordercode, $idxs, $oi_step1, $oi_step2, $op_step='', $paymethod='', $code='', $memo='', $bankcode='', $bankaccount='', $bankuser='', $bankusertel='', $re_type='', $opt1_changes='', $opt2_changes='', $opt2_pt_changes='', $opt_text_s_changes='', $opt_text_c_changes='', $pgcancel_type='', $pgcancel_res_code='', $pgcancel_res_msg='', $sub_code='', $admin_memo='', $receipt_name='', $receipt_tel='', $receipt_mobile='', $receipt_addr='', $receipt_post5='', $rechange_type='', $return_store_code='', $return_deli_price='', $return_deli_receipt='', $return_deli_type='', $return_deli_memo='', $proc_type='' ) {

	//넘어온 파라미터를 정리한다.
	if(!$code)	$code= 0;
	if(!$bankcode)	$bankcode= 0;

	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_type		= $exe_id_arr[2];

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	if ($reg_type == 'batch') { // 쉘파일일 경우
		$textDir = $_SERVER[HOME].'/public/data/backup/cancel_logs_'.date("Ym").'/';
	} else {
		$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';
	}

	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$outText.= " ordercode     : ".$ordercode."\n";
	$outText.= " idxs     : ".$idxs."\n";
	$outText.= " oi_step1     : ".$oi_step1."\n";
	$outText.= " oi_step2     : ".$oi_step2."\n";
	$outText.= " op_step     : ".$op_step."\n";
	$outText.= " paymethod     : ".$paymethod."\n";
	$outText.= " code     : ".$code."\n";
	$outText.= " sub_code     : ".$sub_code."\n";
	$outText.= " memo     : ".$memo."\n";
	$outText.= " admin_memo     : ".$admin_memo."\n";
	$outText.= " bankcode     : ".$bankcode."\n";
	$outText.= " bankaccount     : ".$bankaccount."\n";
	$outText.= " bankuser     : ".$bankuser."\n";
	$outText.= " bankusertel     : ".$bankusertel."\n";
	$outText.= " re_type     : ".$re_type."\n";
	$outText.= " opt1_changes     : ".$opt1_changes."\n";
	$outText.= " opt2_changes     : ".$opt2_changes."\n";
	$outText.= " opt2_pt_changes     : ".$opt2_pt_changes."\n";
	$outText.= " opt_text_s_changes     : ".$opt_text_s_changes."\n";
	$outText.= " opt_text_c_changes     : ".$opt_text_c_changes."\n";
	$outText.= " pgcancel_type     : ".$pgcancel_type."\n";
	$outText.= " pgcancel_res_code     : ".$pgcancel_res_code."\n";
	$outText.= " pgcancel_res_msg     : ".$pgcancel_res_msg."\n";

	$outText.= " receipt_name     : ".$receipt_name."\n";
	$outText.= " receipt_tel     : ".$receipt_tel."\n";
	$outText.= " receipt_mobile     : ".$receipt_mobile."\n";
	$outText.= " receipt_addr     : ".$receipt_addr."\n";
	$outText.= " return_store_code     : ".$return_store_code."\n";

	$outText.= " rechange_type     : ".$rechange_type."\n";
	$outText.= " receipt_post5     : ".$receipt_post5."\n";

	$outText.= " return_deli_price     : ".$return_deli_price."\n";
	$outText.= " return_deli_receipt     : ".$return_deli_receipt."\n";
	$outText.= " return_deli_type     : ".$return_deli_type."\n";
	$outText.= " return_deli_memo     : ".$return_deli_memo."\n";
	$outText.= " proc_type     : ".$proc_type."\n";
	
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//

	/*if($oi_step1 == '0' && $oi_step2 == '0') { // 주문접수일 경우
		//취소완료로 변경한다.
		$step	= 44;
		orderProductStepUpdate($exe_id, $ordercode, $idxs, $step ); // 주문코드, 상품 idx, 주문상태

	}*/

	//if ($oi_step1 != '0') {


		$idxs_arr	= explode("|", $idxs);		// 상품배열을 분리한다.

		//전체 취소인지 부분취소인지 체크한다.
		list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idxs)."') "));
		if ($op_idx_cnt == 0) { //전체 취소일 경우
			$oc_type = "ALL";
		} else {
			$oc_type = "PART";
		}

		// 옵션이 있을경우 분할한다.
		if($opt1_changes) {
			$opt1_change	= explode("|", $opt1_changes);		// 교환옵션1 배열을 분리한다.
			$opt2_change	= explode("|", $opt2_changes);		// 교환옵션2 배열을 분리한다.

			$opt2_pt_change			= explode("|!@#|", $opt2_pt_changes);			// 교환 옵션별 가격을 분리한다.
			$opt_text_s_change		= explode("|!@#|", $opt_text_s_changes);		// 교환 텍스트 옵션 옵션명을 분리한다.
			$opt_text_c_change		= explode("|!@#|", $opt_text_c_changes);		// 교환 텍스트 옵션 옵션값을 분리한다.
		}

		$opt_idx = array();
		for($j=0;$j < count($idxs_arr);$j++) {

			//2016-10-06 libe90 싱크커머스로 주문취소신청 전송
			list($delivery_type)=pmysql_fetch_array(pmysql_query("SELECT b.delivery_type 
				FROM tblorderinfo as a,tblorderproduct b 
				WHERE a.ordercode=b.ordercode 
				and  a.ordercode='{$ordercode}' 
				and b.idx={$idxs_arr[$j]}"));

			if( $memo!='Synccommerce' && $oi_step1 != '0' && $delivery_type != '0'){ // $delivery_type != '0' && 슈마커는 택배도 O2O다
                //입금대기일 경우 취소요청은 없다
				$sync_status = 'D'; // 취소요청
				// 전체취소이고 주문접수, 결제완료, 배송준비중일 경우
				if ($oc_type == 'ALL' && ($oi_step1 == '0' || $oi_step1 == '1' || $oi_step1 == '2') && $oi_step2 == '0') {
					$sync_status = 'D'; // 취소요청
				} else {
					// 주문접수, 결제완료, 배송준비중일 경우
					if(($oi_step1 == '0' ||$oi_step1 == '1' || $oi_step1 == '2' || ($oi_step1 == '3' && $op_step == '2')) && $oi_step2 == '0') {
						$sync_status = 'D'; // 취소요청
					}
					// 배송중, 배송완료일 경우 이거나 상품의 상태가 배송준비가 아닐경우
					if( ( $oi_step1 == '3' || $oi_step1 == '4' ) && $oi_step2 == '0' && $op_step != '2') {
						if ( $re_type == 'B' ) { // 반송요청이 있을경우
							$sync_status = 'J'; // 반품요청
						} else if ( $re_type == 'C' )  { // 교환요청이 있을경우
							$sync_status = 'E'; // 교환요청
						}
					}
				}
				$sync_idx=$idxs_arr[$j];
				$Sync = new Sync();
				$arrayDatax=array(
					'ordercode'=>$ordercode,
					'sync_status'=>$sync_status,
					'sync_idx'=>$sync_idx
				);
				$Sync->StatusChange($arrayDatax);
			}
			$outText.= '속도체크1========================'.date("Y-m-d H:i:s")."=============================\n";
			if($opt1_changes !='' || $opt_text_s_changes != '') {
				$opt_idx[$idxs_arr[$j]]['opt1_change']	= $opt1_change[$j];
				$opt_idx[$idxs_arr[$j]]['opt2_change']	= $opt2_change[$j];
				$opt_idx[$idxs_arr[$j]]['opt2_pt_change']	= $opt2_pt_change[$j];
				$opt_idx[$idxs_arr[$j]]['opt_text_s_change']	= $opt_text_s_change[$j];
				$opt_idx[$idxs_arr[$j]]['opt_text_c_change']	= $opt_text_c_change[$j];
			} else {
				$opt_idx[$idxs_arr[$j]]['opt1_change']	= "";
				$opt_idx[$idxs_arr[$j]]['opt2_change']	= "";
				$opt_idx[$idxs_arr[$j]]['opt2_pt_change']	= "";
				$opt_idx[$idxs_arr[$j]]['opt_text_s_change']	= "";
				$opt_idx[$idxs_arr[$j]]['opt_text_c_change']	= "";
			}
		}
		//exdebug($opt_idx);
		//exit;

		// 벤더별로 분할한다.
		$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$ordercode}' AND idx IN ('".str_replace("|", "','", $idxs)."') order by vender";
		$result=pmysql_query($sql,get_db_conn());
		//echo $ordercode."<br>".$idxs."<br>".$oi_step1."<br>".$oi_step2."<br>".$paymethod."<br>".$code."<br>".$memo."<br>".$bankcode."<br>".$bankaccount."<br>".$bankuser."<br>".$bankusertel."<br>".$re_type."<br>".$opt1_changes."<br>".$opt2_changes;
		//exit;

		$vd_idx = array();
		while ($row=pmysql_fetch_object($result)) {
			if ($vd_idx[$row->vender]) {
				$vd_idx[$row->vender]['idx']	.= "|".$row->idx;
				$vd_idx[$row->vender]['opt1_changes']	.= "|".$opt_idx[$row->idx]['opt1_change'];
				$vd_idx[$row->vender]['opt2_changes']	.= "|".$opt_idx[$row->idx]['opt2_change'];

				$vd_idx[$row->vender]['opt2_pt_changes']		.= "|!@#|".$opt_idx[$row->idx]['opt2_pt_change'];
				$vd_idx[$row->vender]['opt_text_s_changes']	.= "|!@#|".$opt_idx[$row->idx]['opt_text_s_change'];
				$vd_idx[$row->vender]['opt_text_c_changes']	.= "|!@#|".$opt_idx[$row->idx]['opt_text_c_change'];

			} else {
				$vd_idx[$row->vender]['idx']	.= $row->idx;
				$vd_idx[$row->vender]['opt1_changes']	.= $opt_idx[$row->idx]['opt1_change'];
				$vd_idx[$row->vender]['opt2_changes']	.= $opt_idx[$row->idx]['opt2_change'];

				$vd_idx[$row->vender]['opt2_pt_changes']		.= $opt_idx[$row->idx]['opt2_pt_change'];
				$vd_idx[$row->vender]['opt_text_s_changes']	.= $opt_idx[$row->idx]['opt_text_s_change'];
				$vd_idx[$row->vender]['opt_text_c_changes']	.= $opt_idx[$row->idx]['opt_text_c_change'];

			}
		}
		$outText.= '속도체크2========================'.date("Y-m-d H:i:s")."=============================\n";
		pmysql_free_result($result);
		//var_dump($vd_idx);
		//foreach($vd_idx as $key => $val) {
			//echo "$key => $val <br>";
			//echo $ordercode."<br>".$val['idx']."<br>".$oi_step1."<br>".$oi_step2."<br>".$paymethod."<br>".$code."<br>".$memo."<br>".$bankcode."<br>".$bankaccount."<br>".$bankuser."<br>".$bankusertel."<br>".$re_type."<br>".$val['opt1_changes']."<br>".$val['opt2_changes'];
		//}
		//exit;
		if ($oc_type == 'ALL' && ($oi_step1 == '0' || $oi_step1 == '1' || $oi_step1 == '2') && $oi_step2 == '0') { // 전체취소이고 주문접수, 결제완료, 배송준비중일 경우
			//주문취소 또는 환불요청
			$oc_no = orderCancelRequest($exe_id, $ordercode, $idxs, $paymethod, $code, $memo, $bankcode, $bankaccount, $bankuser, $bankusertel, $re_type, '', '', '', '', '', $pgcancel_type, $pgcancel_res_code, $pgcancel_res_msg, $sub_code, $admin_memo, $receipt_name, $receipt_tel, $receipt_mobile, $receipt_addr, $receipt_post5, $rechange_type, $return_store_code, $return_deli_price, $return_deli_receipt, $return_deli_type, $return_deli_memo, $proc_type );
			$outText.= '속도체크3========================'.date("Y-m-d H:i:s")."=============================\n";
			#주문취소메일
			order_cancel_mail("SendCancelMail", $ordercode, $oc_no);
			$outText.= '속도체크4========================'.date("Y-m-d H:i:s")."=============================\n";
			if ($oi_step1 == '0'){ // 주문접수일 경우
				#주문 취소 완료
				orderCancelFin($exe_id, $ordercode, $idxs, $oc_no, '', '', '', '', '', '', $proc_type );
			}
			$outText.= '속도체크5========================'.date("Y-m-d H:i:s")."=============================\n";
		}  else {
			foreach($vd_idx as $key => $val) {
				if(($oi_step1 == '0' ||$oi_step1 == '1' || $oi_step1 == '2' || ($oi_step1 == '3' && $op_step == '2')) && $oi_step2 == '0') { // 주문접수, 결제완료, 배송준비중일 경우
					$outText.= '속도체크3========================'.date("Y-m-d H:i:s")."=============================\n";
					//주문취소 또는 환불요청
					$oc_no = orderCancelRequest($exe_id, $ordercode, $val['idx'], $paymethod, $code, $memo, $bankcode, $bankaccount, $bankuser, $bankusertel, $re_type, '', '', '', '', '', $pgcancel_type, $pgcancel_res_code, $pgcancel_res_msg, $sub_code, $admin_memo, $receipt_name, $receipt_tel, $receipt_mobile, $receipt_addr, $receipt_post5, $rechange_type, $return_store_code, $return_deli_price, $return_deli_receipt, $return_deli_type, $return_deli_memo, $proc_type );
					$outText.= '속도체크4========================'.date("Y-m-d H:i:s")."=============================\n";
					#주문취소메일
					order_cancel_mail("SendCancelMail", $ordercode, $oc_no);
					$outText.= '속도체크5========================'.date("Y-m-d H:i:s")."=============================\n";

					if ($oi_step1 == '0'){ // 주문접수일 경우
						#주문 취소 완료
						orderCancelFin($exe_id, $ordercode, $val['idx'], $oc_no, '', '', '', '', '', '', $proc_type );
					}
					$outText.= '속도체크6========================'.date("Y-m-d H:i:s")."=============================\n";

					//if ($oi_step1 == '0' || (($oi_step1 == '1' || $oi_step1 == '2' || ($oi_step1 == '3' && $op_step == '2')) && $paymethod == 'C')){ // 주문접수이거나 또는 결제완료, 배송준비중이며 신용카드일 경우
						#주문 취소 완료
					//	if ($paymethod == 'C') {
					//		$pgcancel	="Y";
					//	} else {
					//		$pgcancel	="";
					//	}
					//	orderCancelFin($exe_id, $ordercode, $val['idx'], $oc_no, $pgcancel );
					//}
				}

				if(($oi_step1 == '3' || $oi_step1 == '4') && $oi_step2 == '0' && $op_step != '2') { // 배송중, 배송완료일 경우 이거나 상품의 상태가 배송준비가 아닐경우
					$outText.= '속도체크3-1========================'.date("Y-m-d H:i:s")."=============================\n";
					$val_idx_arr	= explode("|", $val['idx']);
					for($i=0;$i < count($val_idx_arr);$i++) {
						$outText.= '속도체크4-1========================'.date("Y-m-d H:i:s")."=============================\n";
						$oc_no = orderCancelRequest($exe_id, $ordercode, $val_idx_arr[$i], $paymethod, $code, $memo, $bankcode, $bankaccount, $bankuser, $bankusertel, $re_type, $val['opt1_changes'], $val['opt2_changes'], $val['opt2_pt_changes'], $val['opt_text_s_changes'], $val['opt_text_c_changes'], '', '', '', $sub_code, $admin_memo, $receipt_name, $receipt_tel, $receipt_mobile, $receipt_addr, $receipt_post5, $rechange_type, $return_store_code, $return_deli_price, $return_deli_receipt, $return_deli_type, $return_deli_memo, $proc_type );
						$outText.= '속도체크5-1========================'.date("Y-m-d H:i:s")."=============================\n";
						if ($re_type == 'B') { // 반송요청이 있을경우
							#반품요청메일
							if ($proc_type != 'AS') order_cancel_mail("SendReturnMail", $ordercode, $oc_no);
						} else if ($re_type == 'C')  { // 교환요청이 있을경우
							#교환접수메일
							if ($proc_type != 'AS') order_cancel_mail("SendRequestMail", $ordercode, $oc_no);
						}
						$outText.= '속도체크6-1========================'.date("Y-m-d H:i:s")."=============================\n";
					}
				}
				$outText.= '속도체크7========================'.date("Y-m-d H:i:s")."=============================\n";
			}
		}
	//}

	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'cancel_ordercancel_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."cancel_ordercancel_".date("Ymd").".txt",0777);


	return $oc_no;
}


/**
 * 주문 반품/교환 접수
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelAccept($exe_id, $ordercode, $idxs, $oc_no, $proc_type='') {
	global $op_step;

	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//주문 반품/교환 접수로 업데이트한다.
	$date=date("YmdHis");
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', accept_date = '{$date}', oc_step = '1', hold_oc_step='0', hold_date ='' WHERE oc_no='".trim($oc_no)."' ";
	pmysql_query($sql,get_db_conn());

	$step	= 41;	// 접수
	if($oc_no>0) {
		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE oc_no='".trim($oc_no)."' order by oc_no desc limit 1"));

		for($p=0;$p < count($idx);$p++) {
			//주문 상품정보 상태를 변경한다.
			orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, '', '', '', '', ''  , '', '', '', $proc_type );
		}

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$old_step]."=>". $op_step[$step];
		//exdebug($oc_memo);

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$old_step}',
		'{$step}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());
	}

	return true;
}

/**
 * 주문 반품->교환, 교환->반품 으로 전환
 * exe_id					: 실행자 정보(아이디|이름|타입)
 * ordercode				: 주문코드
 * idxs						: 상품 idx들 예) 1|2|3
 * oc_no					: 취소 번호
 * cha_redelivery_type	: 전환구분(Y : 반품, G : 교환)
**/
function orderCancelChange($exe_id, $ordercode, $idxs, $oc_no, $cha_redelivery_type) {

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//주문 보류상태로 업데이트한다.
	$date=date("YmdHis");

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	// 반품/교환 상태를 가져온다.
	list($oc_step, $hold_oc_step)=pmysql_fetch_array(pmysql_query("select oc_step, hold_oc_step from tblorder_cancel WHERE ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' "));	

	$deli_gbn			='';
	if ($cha_redelivery_type == 'Y') {					// 교환 -> 반품
		$rechange_type	='0';
		$redelivery_type	='G';
		if ($oc_step == '3') $deli_gbn			='E';
		if ($oc_step == '0') $step	=40;
		if ($oc_step == '1') $step	=41;
		if ($oc_step == '2') $step	=41;
		if ($oc_step == '3') $step	=42;

	} else if ($cha_redelivery_type == 'G') {			// 반품 -> 교환
		$rechange_type	='1';
		$redelivery_type	='Y';
		if ($oc_step == '3') $deli_gbn			='Y';
		$step	=41;
		if ($oc_step == '0') $step	=40;
	}

	//echo $cha_redelivery_type."/".$oc_code;
	//exit;

	if ($oc_step == '0') $oc_status_step	= 40; // 신청
	if ($oc_step == '1') $oc_status_step	= 41; // 접수
	if ($oc_step == '2') $oc_status_step	= 47; // 제품도착
	if ($oc_step == '3') $oc_status_step	= 42; // 승인
	if ($oc_step == '4') $oc_status_step	= 44; // 완료
	if ($oc_step == '5') $oc_status_step	= 48; // 보류

	/*for($p=0;$p < count($idx);$p++) {
		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select oi_step1 from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));

		// 주문상품 전체 상태변경내역 처리 - 반품/교환 요청
		orderStepChangeLog($exe_id, 'p', $ordercode, $idx[$p], '', '49', $cha_redelivery_type);
	}

	//현재 주문의 취소상태중 취소접수상태가 아닌 카운트를 가져온다
	list($op_cancel_41_tr_cnt)=pmysql_fetch_array(pmysql_query("select count(*) as op_cancel_cnt from (select a.*, case when b.oc_step IS NULL THEN 9999 ELSE b.oc_step END AS oc_step from tblorderproduct a left join tblorder_cancel b on a.oc_no=b.oc_no) c WHERE ordercode='".trim($ordercode)."' AND (redelivery_type != '{$cha_redelivery_type}' OR oc_step != '{$oc_step}') AND idx NOT IN ('".str_replace("|", "','", $idxs)."') "));
	if ($op_cancel_41_tr_cnt ==0) {		//모두 동일한 상태일 경우
		// 주문 전체 상태변경내역 처리 - 반품/교환 요청
		orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step, '49', '');
	}*/

	$sql = "UPDATE tblorderproduct SET redelivery_type='{$cha_redelivery_type}' ";
	if ($deli_gbn) $sql.= ", deli_gbn='{$deli_gbn}' ";
	$sql.= "WHERE ordercode='".trim($ordercode)."' ";
	$sql.= "AND idx IN ('".str_replace("|", "','", $idxs)."') ";

	pmysql_query($sql,get_db_conn());

	for($p=0;$p < count($idx);$p++) {
		$opt1_change				= "";
		$opt2_change				= "";
		$opt2_pt_change			= "";
		$opt_text_s_change		= "";
		$opt_text_c_change		= "";
		if ($cha_redelivery_type == 'G') {			// 반품 -> 교환
			list($opt1_change, $opt2_change, $opt2_pt_change, $opt_text_s_change, $opt_text_c_change)=pmysql_fetch_array(pmysql_query("select opt1_name, opt2_name, option_price_text, text_opt_subject, text_opt_content from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx = '".trim($idx[$p])."' "));
		}

		//스탭을 변경한다.
		orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, $opt1_change, $opt2_change, $opt2_pt_change, $opt_text_s_change, $opt_text_c_change, '', 'Y', '46');
		
		if ($cha_redelivery_type == 'Y') {					// 교환 -> 반품
			$sql = "UPDATE tblorderproduct SET ";
			$sql	.= "self_goods_code_change=NULL ";
			$sql	.= ", opt1_change=NULL ";
			$sql	.= ", opt2_change=NULL ";
			$sql	.= ", text_opt_subject_change=NULL ";
			$sql	.= ", text_opt_content_change=NULL ";
			$sql	.= ", option_price_text_change=NULL ";
			$sql.= "WHERE ordercode='".trim($ordercode)."' ";
			$sql.= "AND idx = '".trim($idx[$p])."' ";
			 //echo $sql;
			pmysql_query($sql,get_db_conn());
		}
	}

	// 반송 또는 발송완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
	list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $idxs)."') AND (deli_gbn != '{$deli_gbn}' OR op_step != '44')"));
	if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 반송 또는 발송완료상태일 경우
		$sql = "UPDATE tblorderinfo SET deli_gbn='{$deli_gbn}' WHERE ordercode='".trim($ordercode)."' ";
		//echo $sql;
		pmysql_query($sql,get_db_conn());
	}

	$sql = "UPDATE tblorder_cancel SET rechange_type='{$rechange_type}' ";
	$sql.= "WHERE ordercode='".trim($ordercode)."' ";
	$sql.= "AND oc_no='".trim($oc_no)."' ";

	pmysql_query($sql,get_db_conn());
}

/**
 * 주문 반품승인 완료
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelPickupFin($exe_id, $ordercode, $idxs, $oc_no, $proc_type='') {
	global $op_step;

	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	$date=date("YmdHis");

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	//주문 반품수거 완료가 안된경우 완료로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET pickup_get_state = 'Y', pickup_get_date = '{$date}', oc_step = '2' WHERE oc_no='".trim($oc_no)."' AND pickup_get_state ='N' ";
	pmysql_query($sql,get_db_conn());

	//주문 반품승인 완료로 업데이트한다.
	$date=date("YmdHis");
	$sql   = " UPDATE tblorder_cancel SET pickup_state = 'Y', pickup_date = '{$date}', oc_step = '3' WHERE oc_no='".trim($oc_no)."' ";
	pmysql_query($sql,get_db_conn());

	// 주문취소(반품시) 완료되었을 경우 로그에 넣는다.
	$step	= 42;	// 환불
	if($oc_no>0) {
		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE oc_no='".trim($oc_no)."' order by oc_no desc limit 1"));

		for($p=0;$p < count($idx);$p++) {
			//주문 상품정보 상태를 변경한다.
			orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, '', '', '', '', ''  , '', '', '', $proc_type );
			//주문 수량을 복구한다.
			//order_recovery_quantity($ordercode, $idx[$p]); - 해당사항은 함수변경후 적용예정
		}

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$old_step]."=>반품승인=>". $op_step[$step];
		//exdebug($oc_memo);

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$old_step}',
		'{$step}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());

		#반품완료메일
		// order_cancel_mail("SendReturnokMail", $ordercode, $oc_no);
	}

	return true;
}

/**
 * 주문 반품/교환 제품도착 완료
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelGetPickup($exe_id, $ordercode, $idxs, $oc_no, $proc_type='') {

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	//주문 반품수거 완료로 업데이트한다.
	$date=date("YmdHis");
	$sql   = " UPDATE tblorder_cancel SET pickup_get_state = 'Y', pickup_get_date = '{$date}', oc_step = '2' WHERE oc_no='".trim($oc_no)."' AND pickup_get_state !='Y' ";
	pmysql_query($sql,get_db_conn());

	for($p=0;$p < count($idx);$p++) {
		// 주문 상품정보 상태를 가져온다.
		list($redelivery_type)=pmysql_fetch_array(pmysql_query("select redelivery_type from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx='".trim($idx[$p])."' "));	
		// 주문/주문상품 전체 상태변경내역 처리
		orderStepChangeLog($exe_id, 'p', $ordercode, $idx[$p], '', '47', $redelivery_type, $proc_type );

		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select oi_step1 from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));

		//현재 주문의 취소상태중 취소접수상태가 아닌 카운트를 가져온다
		list($op_cancel_41_cnt)=pmysql_fetch_array(pmysql_query("select count(*) as op_cancel_cnt from (select a.*, case when b.oc_step IS NULL THEN 9999 ELSE b.oc_step END AS oc_step from tblorderproduct a left join tblorder_cancel b on a.oc_no=b.oc_no) c WHERE ordercode='".trim($ordercode)."' AND (op_step != 41 OR oc_step != '2' ) "));
		if ($op_cancel_41_cnt ==0) {		//모두 취소접수상태인 경우
			orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step, '47', '', $proc_type );
		}
	}
}

/**
 * 주문 교환(반품승인) 완료
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelReorderPickupFin($exe_id, $ordercode, $idxs, $oc_no, $proc_type='') {

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	$date=date("YmdHis");

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	//주문 반품수거 완료로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET pickup_state = 'Y', pickup_date = '{$date}', oc_step = '3' WHERE oc_no='".trim($oc_no)."' ";
	pmysql_query($sql,get_db_conn());

	for($p=0;$p < count($idx);$p++) {
		// 주문 상품정보 상태를 가져온다.
		list($redelivery_type)=pmysql_fetch_array(pmysql_query("select redelivery_type from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx='".trim($idx[$p])."' "));	
		// 주문/주문상품 전체 상태변경내역 처리
		orderStepChangeLog($exe_id, 'p', $ordercode, $idx[$p], '', '42', $redelivery_type, $proc_type );

		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select oi_step1 from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));

		//현재 주문의 취소상태중 취소접수상태가 아닌 카운트를 가져온다
		list($op_cancel_41_cnt)=pmysql_fetch_array(pmysql_query("select count(*) as op_cancel_cnt from (select a.*, case when b.oc_step IS NULL THEN 9999 ELSE b.oc_step END AS oc_step from tblorderproduct a left join tblorder_cancel b on a.oc_no=b.oc_no) c WHERE ordercode='".trim($ordercode)."' AND (op_step != 41 OR oc_step != '3' ) "));
		if ($op_cancel_41_cnt ==0) {		//모두 취소접수상태인 경우
			orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step, '42', '', $proc_type );
		}
	}
}

/**
 * 주문 교환(반품수거/승인) 완료 후 재주문 넣기
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelReorderFin($exe_id, $ordercode, $idxs, $oc_no, $proc_type='') {
	global $op_step, $sync_bon_code;

	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	$date=date("YmdHis");
	//주문 반품수거 완료가 안된경우 완료로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET pickup_get_state = 'Y', pickup_get_date = '{$date}', oc_step = '2' WHERE oc_no='".trim($oc_no)."' AND pickup_get_state ='N' ";
	pmysql_query($sql,get_db_conn());

	//주문 반품승인 완료로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET pickup_state = 'Y', pickup_date = '{$date}', oc_step = '3' WHERE oc_no='".trim($oc_no)."' AND pickup_state !='Y' ";
	pmysql_query($sql,get_db_conn());

	#반품완료메일
	#order_cancel_mail("SendReturnokMail", $ordercode, $oc_no);

	// 주문취소(교환시) 완료되었을 경우 로그에 넣는다.
	$step	= 44;
	if($oc_no>0) {
		//주문취소(교환시) 완료로 업데이트한다.
		$date=date("YmdHis");
		$sql   = " UPDATE tblorder_cancel SET cfindt = '{$date}', oc_step = '4' WHERE oc_no='".trim($oc_no)."' ";
		 //echo $sql;
		pmysql_query($sql,get_db_conn());

		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE oc_no='".trim($oc_no)."' order by oc_no desc limit 1"));

		// 수령자 정보를 가져온다.
		list($receipt_name, $receipt_tel, $receipt_mobile, $receipt_addr, $receipt_post5)=pmysql_fetch_array(pmysql_query("select receipt_name, receipt_tel, receipt_mobile, receipt_addr, receipt_post5 from tblorder_cancel WHERE oc_no='".trim($oc_no)."' "));

		for($p=0;$p < count($idx);$p++) {
			//주문 상품정보 상태를 변경한다.
			orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, '', '', '', '', ''  , '', '', '', $proc_type );
			//주문 수량을 복구한다.
			order_recovery_quantity($ordercode, $idx[$p]);

			list($opt1_change, $opt2_change)=pmysql_fetch_array(pmysql_query("select opt1_change, opt2_change from tblorderproduct where  ordercode='".trim($ordercode)."' AND idx='".trim($idx[$p])."' "));

			// 주문의 상태값을 변경한다.
			$sql	 = " UPDATE tblorderproduct SET ";

			if ($opt1_change) { // 교환상품이 있을경우 자체코드를 불러온다
				list($self_goods_code_change)=pmysql_fetch_array(pmysql_query("SELECT b.self_goods_code FROM tblorderproduct a LEFT JOIN tblproduct_option b ON a.productcode=b.productcode AND b.option_code='{$opt2_change}' where  a.ordercode='".trim($ordercode)."' AND a.idx='".trim($idx[$p])."' "));
				$sql	.= " self_goods_code_change='{$self_goods_code_change}' ";
			} else {
				$sql	.= " self_goods_code_change=self_goods_code ";
			}
			$sql	.= "WHERE ordercode='".trim($ordercode)."' AND idx in (".trim($idx[$p]).") "; // 2016-02-11 jhjeong 같은 주문의 다른 상품일 경우도 해결하기 위해...
			pmysql_query($sql,get_db_conn());
		}

		//새로운 주문코드 생성
		//$n_ordercode = unique_id();
        if(substr(trim($ordercode), -1) == "X") {
            $n_ordercode = unique_id()."X";
        } else {
            $n_ordercode = unique_id();
        }
		$oi_price				= 0;
		$oi_reserve			= 0;
		$oi_point			= 0;
		$oi_dc_price			= 0;
		$oi_deli_price			= 0;
		$oi_staff_price			= 0;
		$oi_cooper_price			= 0;
		$oi_timesale_price			= 0;

		// 재주문을 한다. - 상품(배송 준비중으로)
		$op_sql		= "select * from tblorderproduct  WHERE ordercode='{$ordercode}' and idx IN ('".str_replace("|", "','", $idxs)."') and oc_no='{$oc_no}' ";
		$op_result	= pmysql_query($op_sql,get_db_conn());
		$i=0;
		while($op_row = pmysql_fetch_object($op_result)) {
			$option_price	= 0;
			$option_price_arr		= explode("||", $op_row->option_price_text_change);
			for($p=0;$p<count($option_price_arr);$p++) {
				if ($option_price_arr[$p] != '') {
					$option_price	= $option_price + $option_price_arr[$p];
				}
			}
			$oi_price				= $op_price + (($op_row->price + $option_price) * $op_row->option_quantity); // 상품 가격 총합
			$oi_reserve			= $oi_reserve + $op_row->use_point; // 사용한 적립금 총합
			$oi_point			= $oi_point + $op_row->use_epoint; // 사용한 E포인트 총합
			$oi_dc_price			= $oi_dc_price + $op_row->coupon_price; // 쿠폰가 총합
			$oi_staff_price			= $oi_staff_price + $op_row->staff_price; // 임직원 총합
			$oi_cooper_price			= $oi_cooper_price + $op_row->cooper_price; // 협력사 총합
			$oi_timesale_price			= $oi_timesale_price + $op_row->timesale_price; // 할인금액 총합
			$oi_idx						= $op_row->pg_idx;

			$pg_idx=$oi_idx?$oi_idx:$op_row->idx;

			//배송비가 있으면 배송료 상세정보에 있는지 체크한다.
			/*if ($op_row->deli_price == 0) {
				list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$op_row->productcode."%'"));
				if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
					// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
					list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $op_row->productcode)."') and idx != '".$op_row->idx."' limit 1"));

					if ($op_idx) { // 상품이 있으면 주문상품의 배송료를 0 으로 변경하고 가져온주문에 배송비를 업데이트 한다.
						$old_up_sql	 = " UPDATE tblorderproduct SET deli_price = '0' WHERE idx = '".$op_row->idx."'";
						pmysql_query($old_up_sql,get_db_conn());
						$new_up_sql	 = " UPDATE tblorderproduct SET deli_price = '{$od_deli_price}' WHERE idx != '".$op_idx."'";
						pmysql_query($new_up_sql,get_db_conn());
					}
					$op_deli_price = $od_deli_price;// 배송료
				} else {
					$op_deli_price = $op_row->deli_price;// 배송료
				}
			} else {*/
				$op_deli_price = $op_row->deli_price;// 배송료
			//}

			$oi_deli_price			= $oi_deli_price + $op_deli_price;// 배송료 총합

			# hott erp shopcode를 구한다 2016-10-13 유동혁
			/*
            $codeSql = "SELECT prodcode, colorcode FROM tblproduct WHERE productcode = '".$op_row->productcode."'";
            $codeRes = pmysql_query( $codeSql, get_db_conn() );
            $codeRow = pmysql_fetch_object( $codeRes );
            pmysql_free_result( $codeRes );
            $resShop = getErpProdShopStock_Type( $codeRow->prodcode, $codeRow->colorcode, $op_row->opt2_change );
            $shopcd = $resShop['shopcd'];
*/
			#본사 재고체크후 없으면 null값으로보낸다.2017-04-10
			 $codeSql = "SELECT prodcode, colorcode FROM tblproduct WHERE productcode = '".$op_row->productcode."'";
            $codeRes = pmysql_query( $codeSql, get_db_conn() );
            $codeRow = pmysql_fetch_object( $codeRes );
            pmysql_free_result( $codeRes );
			$resShop=getErpPriceNStock($codeRow->prodcode, $codeRow->colorcode, $op_row->opt2_change, $sync_bon_code);
			
			$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';
			$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
			$outText.= " prodcode     : ".$codeRow->prodcode."\n";
			$outText.= " colorcode     : ".$codeRow->colorcode."\n";
			$outText.= " opt2_change     : ".$op_row->opt2_change."\n";
			$outText.= " sync_bon_code     : ".$sync_bon_code."\n";
			$outText.= " 본사재고     : ".$resShop['sumqty']."\n";
			$outText = "---------------------------------------------\n";
			

			if($resShop['sumqty']>0) $shopcd = $sync_bon_code;
			else $shopcd = "";
			
			

			// idx를 얻기 위해 temp 에 저장한다.
			$op_tem_sql	= "INSERT INTO tblorderproducttemp (vender, ordercode, tempkey, productcode) VALUES ('".$op_row->vender."', '".$n_ordercode."', '".$op_row->tempkey."', '".$op_row->productcode."')  RETURNING idx";
			$op_tem_res	= pmysql_query($op_tem_sql,get_db_conn());
			$op_tem_row	= pmysql_fetch_array($op_tem_res);
			$op_idx			= $op_tem_row[0];
			pmysql_free_result($op_tem_res);

            $op_row->productname = pg_escape_string($op_row->productname);
			
			/*품목주문코드 생성*/
			$o2otype="O";
			if(!$op_row->delivery_type){
				if(strlen($n_ordercode)=="20"){$o2otype="A";}
				else {$o2otype="";}
			}

			$o2num=sprintf("%02d", $i+1);;
			$pr_code=$n_ordercode.$o2otype.'_'.$o2num;
			/**/

			// 주문상품에 저장한다.
			$op_in_sql = "INSERT INTO tblorderproduct ( vender, ordercode, tempkey, productcode, productname, opt1_name, opt2_name, ";
			$op_in_sql.= " addcode, quantity, price, reserve, date, selfcode, order_prmsg, ";
			$op_in_sql.= " option_price, option_quantity, option_type, coupon_price, deli_price, op_step, use_point, use_epoint, idx, ";
			$op_in_sql.= " text_opt_subject, text_opt_content, option_price_text, ";
			$op_in_sql.= " deli_ori_price, deli_select, deli_expdate, rate, staff_order, self_goods_code, ";
            $op_in_sql.= " store_code, ori_price, ori_option_price, pr_code, cooper_order, staff_price, cooper_price, timesale_price, timesale_detail, pg_idx ";
			$op_in_sql.= " ) VALUES ( ";
			$op_in_sql.= " '".$op_row->vender."', '".$n_ordercode."', '".$op_row->tempkey."', '".$op_row->productcode."', '".$op_row->productname."', '".$op_row->opt1_change."', '".$op_row->opt2_change."', ";
			$op_in_sql.= " '".$op_row->addcode."', '".$op_row->quantity."', '".$op_row->price."', '".$op_row->reserve."', '".date('Ymd')."', '".$op_row->selfcode."', '".$op_row->order_prmsg."', ";
			$op_in_sql.= " '".$option_price."', '".$op_row->option_quantity."', '".$op_row->option_type."', '".$op_row->coupon_price."', '".$op_deli_price."', '2', '".$op_row->use_point."', '".$op_row->use_epoint."', '".$op_idx."', ";
			$op_in_sql.= " '".$op_row->text_opt_subject_change."', '".$op_row->text_opt_content_change."', '".$op_row->option_price_text_change."', ";
			$op_in_sql.= " '".$op_row->deli_ori_price."', '".$op_row->deli_select."', '".$op_row->deli_expdate."', '".$op_row->rate."', '".$op_row->staff_order."', '".$op_row->self_goods_code_change."', ";
            $op_in_sql.= " '".$shopcd."', '".$op_row->ori_price."', '".$op_row->ori_option_price."', '".$pr_code."', '".$op_row->cooper_order."', '".$op_row->staff_price."', '".$op_row->cooper_price."', '".$op_row->timesale_price."', '".$op_row->timesale_detail."', '".$pg_idx."' ";
			$op_in_sql.= " )";
			pmysql_query($op_in_sql,get_db_conn());

			$outText.= " re_qry     : ".$op_in_sql."\n";

			$op_step_array		= array(
					0	=> "주문접수",
					1	=> "결제완료",
					2	=> "배송준비중",
					3	=> "배송중",
					4	=> "배송완료",
					40	=> "취소신청",
					41	=> "취소접수",
					42	=> "취소진행",
					44	=> "취소완료",
					50	=> "결제시도",
					51	=> "PG확인요망",
					54	=> "결제실패",
					);
			//메모에 등록된 내용을 설정한다.
			$osu_memo	= $op_step_array['2']."(재주문)";

			$date=date("YmdHis");
			$log_sql = "INSERT INTO tblorderproduct_log(
			ordercode	,
			idx	,
			step_prev	,
			step_next,
			memo,
			reg_id,
			reg_name,
			reg_type,
			regdt) VALUES (
			'{$n_ordercode}',
			'{$op_idx}',
			'0',
			'2',
			'{$osu_memo}',
			'{$reg_id}',
			'{$reg_name}',
			'{$reg_type}',
			'{$date}')";
			@pmysql_query($log_sql,get_db_conn());

			//temp의 정보를 삭제한다.
			$op_tem_del = "DELETE FROM tblorderproducttemp WHERE idx = '".$op_idx."' ";
			pmysql_query($op_tem_del,get_db_conn());

			// 주문수량을 처리한다.
			order_quantity($n_ordercode, $idx[$p]);

			// 주문/주문상품 전체 상태변경내역 처리
			orderStepChangeLog($exe_id, 'p', $n_ordercode, $op_idx, '', '2', '', $proc_type );
			$i++;

			

			#교환시 재주문에 대한 이전 주문번호와  idx를 저장한다. (김재수 추가)
			$tcr_sql = "INSERT INTO tblorder_cancel_reorder(
			oc_no	,
			ordercode	,
			idx	,
			pr_code	,
			old_ordercode	,
			old_idx	,
			old_pr_code) VALUES (
			'{$oc_no}',
			'{$n_ordercode}',
			'{$op_idx}',
			'{$pr_code}',
			'{$ordercode}',
			'{$op_row->idx}',
			'{$op_row->pr_code}')";
			pmysql_query($tcr_sql,get_db_conn());
		}
		pmysql_free_result($pro_result);

		if ($receipt_name != '') {
			$oi_row->receiver_name	= $receipt_name;
			$oi_row->receiver_tel1		= $receipt_tel;
			$oi_row->receiver_tel2		= $receipt_mobile;
			$oi_row->receiver_addr	= "우편번호 : {$receipt_post5}
	주소 : {$receipt_addr}";
			$oi_row->post5				= $receipt_post5;
		}

		// 재주문을 한다. - 주문(배송준비중으로)
		$date=date("YmdHis");
		$inf_sql		= "select * from tblorderinfo  WHERE ordercode='{$ordercode}' ";
		$inf_result	= pmysql_query($inf_sql,get_db_conn());
		$oi_row		= pmysql_fetch_object($inf_result);

		$oi_sql = "INSERT INTO tblorderinfo ( ";
		$oi_sql.= "ordercode, tempkey, id, price, deli_price, ";
		$oi_sql.= "dc_price, reserve, point, paymethod, bank_date, pay_flag, pay_auth_no, pay_admin_proc, pay_data, ";
		$oi_sql.= "escrow_result, deli_gbn, sender_name, sender_email, sender_tel, receiver_name, receiver_tel1, receiver_tel2, ";
		$oi_sql.= "receiver_addr, order_msg, ip, del_gbn, loc, bank_sender, receipt_yn, ";
		$oi_sql.= "order_msg2, oi_step1, oi_step2, oldordno, regdt, ";
		$oi_sql.= "post5, deli_select, deli_ori_price, sender_tel2, staff_order, pg_ordercode, cooper_order, staff_price, cooper_price, timesale_price ";

		$oi_sql.= " ) VALUES ( ";
		$oi_sql.= "'".$n_ordercode."', '".$oi_row->tempkey."', '".$oi_row->id."', '".$oi_price."', '".$oi_deli_price."', ";
		$oi_sql.= "'".$oi_dc_price."', '".$oi_reserve."', '".$oi_point."', '".$oi_row->paymethod."', '".$date."', '".$oi_row->pay_flag."', '".$oi_row->pay_auth_no."', '".$oi_row->pay_admin_proc."', '".$oi_row->pay_data."', ";
		$oi_sql.= "'".$oi_row->escrow_result."', 'S', '".$oi_row->sender_name."', '".$oi_row->sender_email."', '".$oi_row->sender_tel."', '".$oi_row->receiver_name."', '".$oi_row->receiver_tel1."', '".$oi_row->receiver_tel2."', ";
		$oi_sql.= "'".$oi_row->receiver_addr."', '".$oi_row->order_msg."', '".$oi_row->ip."', 'N', '".$oi_row->loc."', '".$oi_row->bank_sender."', '".$oi_row->receipt_yn."', ";
		$oi_sql.= "'".$oi_row->order_msg2."', '2', '0', '".$ordercode."', '".$date."', ";
		$oi_sql.= "'".$oi_row->post5."', '".$oi_row->deli_select."', '".$oi_row->deli_ori_price."', '".$oi_row->sender_tel2."', '".$oi_row->staff_order."', '".$oi_row->pg_ordercode."', '".$oi_row->cooper_order."', '".$oi_staff_price."', '".$oi_cooper_price."', '".$oi_timesale_price."' ";

		$oi_sql.= " ) ";
		pmysql_query($oi_sql,get_db_conn());

		pmysql_free_result($inf_result);

		orderStepChangeLog($exe_id, 'o', $n_ordercode, '', '2', '0', '', $proc_type );

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$old_step]."=>반품승인=>".$op_step[$step]."(재주문)";
		//exdebug($oc_memo);

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$old_step}',
		'{$step}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());


        // 주문 체크 추가
        setSyncInfo($n_ordercode, $opidx, 'I');

		//ERP로 교환완료데이터를 보낸다.
		sendErporderChange($n_ordercode);

		// 싱크커머스에 재주문 상품을 넣어준다
		$Sync = new Sync();
		$arrayDatax=array( 'ordercode'=>$n_ordercode, 'redelivery_type' => 'G' );
		$srtn=$Sync->OrderInsert($arrayDatax);
		$outText.= " shopcd     : ".$shopcd."\n";
		if ($shopcd == '') {
			$arrayDatax_p=array('ordercode'=>$n_ordercode,'sync_idx'=>'AND idx='.$op_idx);
			$deli_type_sql = "UPDATE tblorderproduct SET store_code='',delivery_type='2' WHERE ordercode='{$n_ordercode}' and idx={$op_idx} ";
			pmysql_query($deli_type_sql, get_db_conn());

			$sql = "INSERT INTO tblorderproduct_store_change(ordercode, idx, regdt) VALUES ('{$n_ordercode}','{$op_idx}','".date('YmdHis')."')";
			pmysql_query($sql, get_db_conn());
			$srtn=$Sync->OrderInsert($arrayDatax_p);

			#싱크커머스 API호출
			if($srtn != 'fail') {

				//변경전 erp로 전송
				sendErpChangeShop($n_ordercode, $op_idx, '', '2');

				#주문정보 update
				$in_deli_sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$n_ordercode}' and idx={$op_idx} ";
				pmysql_query($in_deli_sql, get_db_conn());
			}else{
				$in_deli_sql = "UPDATE tblorderproduct SET  store_code='',delivery_type='0' WHERE ordercode='{$n_ordercode}' and idx={$op_idx} ";
				pmysql_query($in_deli_sql, get_db_conn());
			}
		}

		$upQrt_f = fopen($textDir.'cancel_reorder_'.date("Ymd").'.txt','a');
		fwrite($upQrt_f, $outText );
		fclose($upQrt_f);
		chmod($textDir."cancel_reorder_".date("Ymd").".txt",0777);

		#교환완료메일
		// order_cancel_mail("SendRequestokMail", $ordercode, $oc_no);

	}

	return true;

}

/**
 * 주문 취소/환불 완료
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
 * pgcancel		: pg 결제 취소 여부(신용카드일경우)
 * bankcode		: 환불 은행 코드(lib.php 의 $oc_bankcode)
 * bankaccount	: 환불 은행 계좌번호
 * bankuser		: 환불 은행 예금주
 * rfee				: 환불 수수료
 * coupon_status	: 쿠폰복원 유무 (복원 안할시 N)
 * proc_type			: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelFin($exe_id, $ordercode, $idxs, $oc_no, $pgcancel='', $bankcode='', $bankaccount='', $bankuser='', $rfee='', $coupon_status='', $proc_type='') {
	global $op_step;
	
	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//넘어온 파라미터를 정리한다.
	if(!$bankcode)	$bankcode= 0;
	if(!$rfee)	$rfee= 0;
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//보류상태를 진행상태로 업데이트한다.
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'Y', hold_date ='', hold_oc_step='0', oc_step=hold_oc_step WHERE oc_no='".trim($oc_no)."' AND accept_status='D' ";
	pmysql_query($sql,get_db_conn());

	//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
	list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point - use_epoint + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$ordercode}' and idx IN ('".str_replace("|", "','", $idxs)."') and oc_no='{$oc_no}' group by ordercode"));

	// 최종 환불금액을 계산한다. (실결제금액 - 환불수수료)
	if ($rfee > 0) {
		$rprice	= $sum_price - $rfee;
	} else {
		$rprice	= $sum_price;
	}

	//주문취소(환불) 완료로 업데이트한다.
	$date=date("YmdHis");
	$sql   = " UPDATE tblorder_cancel SET rprice = '{$rprice}', rfindt = '{$date}' ";
	if ($pgcancel) $sql  .= " , pgcancel='{$pgcancel}' ";
	if ($bankaccount) $sql  .= " , bankcode='{$bankcode}', bankaccount='{$bankaccount}', bankuser='{$bankuser}', rfee='{$rfee}' ";
	$sql  .= " , cfindt = '{$date}', oc_step = '4' WHERE oc_no='".trim($oc_no)."' ";
	 //echo $sql;
	pmysql_query($sql,get_db_conn());

	//주문자 ID를 구한다.
	list($ord_id)=pmysql_fetch_array(pmysql_query("select id from tblorderinfo WHERE ordercode='{$ordercode}'"));

	//주문접수시 입력된 로그의 정보를 가져온다.
	list($step_prev, $step_next)=pmysql_fetch_array(pmysql_query("select step_prev, step_next from tblorder_cancel_log where ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' order by ocl_no asc limit 1"));

	// 주문취소 완료되었을 경우 로그에 넣는다.
	$step	= 44;
	if($oc_no>0) {

		$t_use_point	= 0; // 사용 적립금 총합
		$t_use_epoint	= 0; // 사용 적립금 총합
		$t_reserve		= 0; // 적립 예정 적립금 총합
		$t_staff_price		= 0; // 임직원 포인트
		$t_cooper_price		= 0; // 협력사 포인트 
		$t_timesale_price		= 0; // 타임세일 할인금액 포인트 

		for($p=0;$p < count($idx);$p++) {

			// 주문 상품정보 상태, 사용 적립금, 적립 예정 적립금, 배송비 를 가져온다.
			list($old_step, $use_point, $reserve, $deli_price, $productcode, $use_epoint, $staff_price, $cooper_price, $timesale_price)=pmysql_fetch_array(pmysql_query("select op_step, use_point, reserve, deli_price, productcode, use_epoint, staff_price, cooper_price, timesale_price from tblorderproduct WHERE oc_no='".trim($oc_no)."' and idx='".$idx[$p]."'"));

			//배송비가 있으면 배송료 상세정보에 있는지 체크한다.
			/*if ($deli_price > 0) {
				list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$productcode."%'"));
				if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
					// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
					list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $productcode)."') and idx != '".$idx[$p]."' limit 1"));

					if ($op_idx) { // 상품이 있으면 주문상품의 배송료를 0 으로 변경하고 가져온주문에 배송비를 업데이트 한다.
						$old_up_sql	 = " UPDATE tblorderproduct SET deli_price = '0' WHERE idx = '".$idx[$p]."'";
						pmysql_query($old_up_sql,get_db_conn());
						$new_up_sql	 = " UPDATE tblorderproduct SET deli_price = '{$od_deli_price}' WHERE idx = '".$op_idx."'";
						pmysql_query($new_up_sql,get_db_conn());
					}
				}
			}*/

			//주문 상품정보 상태를 변경한다.
			orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, '', '', '', '', ''  , '', '', '', $proc_type );

			// 상품 수량을 돌려준다.
			if ($step_prev > 0) { // 주문접수가 아닌경우
				//수량복구 함수 추가해야 함
				order_recovery_quantity($ordercode, $idx[$p]);
				if ($step_prev == '4') { // 배송완료일 경우 적립한 포인트를 돌려 받는다.
					$t_reserve = $t_reserve + $reserve;
				}
			}

			if (substr($ordercode,-1)!="X") { // 비회원 주문이 아닐경우
				if ($coupon_status != 'N') {
					// 사용 쿠폰 복구(재발급)한다.
					order_recovery_coupon($ordercode, $idx[$p], $ord_id);
				}
				//사용 적립금을 돌려준다.
				if ($use_point != 0 || $use_epoint != 0 || $staff_price!=0 || $cooper_price!=0 ){
					$point_change="0";
					if($staff_price!=0){
						$point_change=$staff_price;
					}else if($cooper_price!=0){
						$point_change=$cooper_price;
					}else{
						$point_change=$use_point;
					}

					insert_order_point($ordercode, $ord_id, $point_change, "주문 ".$ordercode." 취소(1건)에 대한 사용 포인트 환원", '@order_cancel','', date('YmdHis').'-'.uniqid('').'|'.$ordercode.'_'.$idx[$p], $return_point_term, '', $use_epoint);
				}
			}
			$t_use_point = $t_use_point + $use_point;
			$t_use_epoint = $t_use_epoint + $use_epoint;
			$t_staff_price = $t_staff_price + $staff_price;
			$t_cooper_price = $t_cooper_price + $cooper_price;
		}

		if (substr($ordercode,-1)!="X") { // 비회원 주문이 아닐경우
			//적립 예정 적립금을 돌려받는다.
			if ($t_reserve != 0) insert_order_point($ordercode, $ord_id, $t_reserve * -1, "주문 ".$ordercode." 취소(".count($idx)."건)에 대한 포인트 지급취소", '@order_end_cancel','', date('YmdHis').'-'.uniqid(''), $return_point_term);
		}

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$old_step]."=>". $op_step[$step];
		//exdebug($oc_memo);

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$old_step}',
		'{$step}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());

		if ($step_prev > 0) { // 주문접수가 아닌경우
			#환불안내메일
			order_cancel_mail("SendRefundMail", $ordercode, $oc_no);
		}

	}

	return true;
}


/**
 * 주문취소(반품/교환/환불) 요청
 * exe_id					: 실행자 정보(아이디|이름|타입)
 * ordercode				: 주문코드
 * idxs						: 상품 idx들 예) 1|2|3
 * paymethod				: 결제 방식
 * code						: 사유 코드(lib.php 의 $oc_code)
 * memo					: 상세 사유
 * bankcode				: 환불 은행 코드(lib.php 의 $oc_bankcode)
 * bankaccount			: 환불 은행 계좌번호
 * bankuser				: 환불 은행 예금주
 * bankusertel				: 환불 연락처
 * re_type					: 타입 (NULL: 환불, C:교환, B: 반품)
 * opt2_pt_changes		: 교환 옵션별 가격 구분자 ||
 * opt_text_s_changes	: 교환 텍스트 옵션 옵션명
 * opt_text_c_changes	: 교환 텍스트 옵션 옵션값
 * pgcancel_type		: pg 결제 취소 타입
 * pgcancel_res_code	: pg 결제 취소 결과코드
 * pgcancel_res_msg	: pg 결제 취소 결과메시지
 * sub_code				: 사유 상세코드(lib.php 의 $oc_reason_code) - 구분자 |
 * admin_memo			: 관리자 사유메모
 * receipt_name			: 반품/교환시 수령대상자 이름
 * receipt_tel				: 반품/교환시 수령대상자 전화번호
 * receipt_mobile		: 반품/교환시 수령대상자 휴대전화번호
 * receipt_addr			: 반품/교환시 수령대상자 주소
 * receipt_post5			: 반품/교환시 수령대상자 신주소 5자리
 * rechange_type		: 교환시 교환방법(0 : 없음, 1 : 동일상품교환, 2 : 다른 사이즈로 교환)
 * return_store_code	: 반품/교환시 회송매장코드

 * return_deli_price		: 반품/교환시 고객부탐 택배비
 * return_deli_receipt	: 반품/교환시 택배비 수령
 * return_deli_type		: 반품/교환시 택배비부담 방법
 * return_deli_memo	: 반품/교환시 택배비 메모
 * proc_type				: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelRequest($exe_id, $ordercode, $idxs, $paymethod, $code='', $memo='', $bankcode='', $bankaccount='', $bankuser='', $bankusertel='', $re_type='', $opt1_changes='', $opt2_changes='', $opt2_pt_changes='', $opt_text_s_changes='', $opt_text_c_changes='', $pgcancel_type='', $pgcancel_res_code='', $pgcancel_res_msg='', $sub_code='', $admin_memo='', $receipt_name='', $receipt_tel='', $receipt_mobile='', $receipt_addr='', $receipt_post5='', $rechange_type='', $return_store_code='', $return_deli_price='', $return_deli_receipt='', $return_deli_type='', $return_deli_memo='', $proc_type='' ) {
	global $op_step;
	
	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	//넘어온 파라미터를 정리한다.
	if(!$code)	$code= 0;
	if(!$sub_code)	$sub_code= 0;
	if(!$rechange_type)	$rechange_type= 0;
	if(!$bankcode)	$bankcode= 0;
	if(!$return_deli_price)	$return_deli_price= 0;
	if(!$return_deli_type)	$return_deli_type= 0;

	$idx	= explode("|", $idxs);		// 상품배열을 분리한다.

	if ($bankusertel && !strstr("-",$bankusertel)) $bankusertel	= addMobile($bankusertel);

	$opt1_change	= explode("|", $opt1_changes);		// 교환옵션1 배열을 분리한다.
	$opt2_change	= explode("|", $opt2_changes);		// 교환옵션2 배열을 분리한다.

	$opt2_pt_change			= explode("|!@#|", $opt2_pt_changes);			// 교환 옵션별 가격을 분리한다.
	$opt_text_s_change		= explode("|!@#|", $opt_text_s_changes);		// 교환 텍스트 옵션 옵션명을 분리한다.
	$opt_text_c_change		= explode("|!@#|", $opt_text_c_changes);		// 교환 텍스트 옵션 옵션값을 분리한다.

	$date=date("YmdHis");

	// 주문취소 요청한 내역을 입력한다.
	$sql = "INSERT INTO tblorder_cancel(
		ordercode,
		code,
		sub_code,
		memo,
		admin_memo,
		bankcode,
		bankaccount,
		bankuser,
		bankusertel,
		pgcancel_res_code,
		pgcancel_res_msg,
		receipt_name,
		receipt_tel,
		receipt_mobile,
		receipt_addr,
		receipt_post5,
		rechange_type,
		return_store_code,
		return_deli_price,
		return_deli_receipt,
		return_deli_type,
		return_deli_memo,
		reg_id,
		reg_name,
		reg_type,
		consult_can_name,
		consult_can_tel,
		consult_can_mobile,
		regdt) VALUES (
		'{$ordercode}',
		'{$code}',
		'{$sub_code}',
		'{$memo}',
		'{$admin_memo}',
		'{$bankcode}',
		'{$bankaccount}',
		'{$bankuser}',
		'".trim($bankusertel)."',
		'{$pgcancel_res_code}',
		'{$pgcancel_res_msg}',
		'{$receipt_name}',
		'{$receipt_tel}',
		'{$receipt_mobile}',
		'{$receipt_addr}',
		'{$receipt_post5}',
		'{$rechange_type}',
		'{$return_store_code}',
		'{$return_deli_price}',
		'{$return_deli_receipt}',
		'{$return_deli_type}',
		'{$return_deli_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$receipt_name}',
		'{$receipt_tel}',
		'{$receipt_mobile}',
		'{$date}') RETURNING oc_no";
	$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$oc_no = $row2[0];
	//$oc_no = '1';

	// 처리 구분이 있을경우 업데이트 해준다.
	if($proc_type)	{
		pmysql_query(" UPDATE tblorder_cancel SET proc_type = '{$proc_type}' WHERE oc_no='".trim($oc_no)."'",get_db_conn());
	}

	// 주문취소 입력이 완료되었을 경우 로그에 넣는다.

	if($oc_no>0) {
		if ($re_type == 'C' || $re_type == 'B') { // 반송상품이 있을경우 (교환 또는 반품일 경우)
			// 반송 대기상태이고 주문접수대기상태로 변경한다.
			pmysql_query(" UPDATE tblorder_cancel SET pickup_state = 'R', accept_status = 'N' WHERE oc_no='".trim($oc_no)."'",get_db_conn());
		} else {
			// 주문접수완료상태로 변경한다.
			pmysql_query(" UPDATE tblorder_cancel SET accept_status = 'Y', accept_date ='{$date}', oc_step = '1' WHERE oc_no='".trim($oc_no)."'",get_db_conn());
		}
		$step	= 41; // 접수
		if ($re_type == 'C' || $re_type == 'B') $step	= 40; // 신청
		for($p=0;$p < count($idx);$p++) {
			if ($p == 0) {
				// 이전 주문 상품정보 상태를 가져온다.
				list($old_step)=pmysql_fetch_array(pmysql_query("select op_step from tblorderproduct WHERE ordercode='".trim($ordercode)."' and idx='".trim($idx[$p])."'"));
			}

			// 주문 상품정보 상태, 배송비,상품코드 를 가져온다.
			list($deli_price, $productcode)=pmysql_fetch_array(pmysql_query("select deli_price, productcode from tblorderproduct WHERE ordercode='".trim($ordercode)."' and idx='".$idx[$p]."'"));

			//배송비가 있으면 배송료 상세정보에 있는지 체크한다.
			if ($deli_price > 0) {
				list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$productcode."%'"));

				if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
					// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
					list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$idx[$p]."' and op_step < 40 order by idx limit 1"));

					if ($op_idx) { // 상품이 있으면 주문상품의 배송료를 0 으로 변경하고 가져온주문에 배송비를 업데이트 한다.
						$old_up_sql	 = " UPDATE tblorderproduct SET deli_price = '0' WHERE idx = '".$idx[$p]."'";
						pmysql_query($old_up_sql,get_db_conn());
						$new_up_sql	 = " UPDATE tblorderproduct SET deli_price = '{$od_deli_price}' WHERE idx = '".$op_idx."'";
						pmysql_query($new_up_sql,get_db_conn());
						
						/*
						// tblorder_reserve 2016-05-17 유동혁
						$old_or_select_sql = "SELECT deli_price, deli_reserve, deli_rate FROM tblorder_reserve WHERE op_idx ='".$idx[$p]."' ";
						$old_or_result     = pmysql_query( $old_or_select_sql, get_db_conn() );
						$old_or_row        = pmysql_fetch_object( $old_or_result );
						pmysql_free_result( $old_or_result );
						// 기존 배송비 reserve를 0으로 변경
						$old_or_update_sql = "UPDATE tblorder_reserve SET deli_price = 0, deli_reserve = 0, deli_rate = 0 WHERE op_idx ='".$idx[$p]."' ";
						pmysql_query( $old_or_update_sql, get_db_conn() );
						// 배송비 reserve를 변경해준다
						$new_or_update_sql = "UPDATE tblorder_reserve SET deli_price = ".$old_or_row->deli_price.", deli_reserve = ".$old_or_row->deli_reserve.", ";
						$new_or_update_sql.= "deli_rate = ".$old_or_row->deli_rate." WHERE op_idx = '".$op_idx."' ";
						$new_or_update_sql.= "RETURNING idx, ordercode, op_idx, op_price, op_reserve, op_rate, deli_price, deli_reserve, deli_rate ";
						$new_or_result     = pmysql_query( $new_or_update_sql, get_db_conn() );
						$new_or_row        = pmysql_fetch_object( $new_or_result );
						pmysql_free_result( $new_or_result );
						// 로그 테이블에 insert
						$insert_new_or_sql = "INSERT INTO tblorder_reserve_log ( idx, ordercode, op_idx, op_price, ";
						$insert_new_or_sql.= "op_reserve, op_rate, deli_price, deli_reserve, ";
						$insert_new_or_sql.= "deli_rate, date ) ";
						$insert_new_or_sql.= "VALUES ( ".$new_or_row->idx.", '".$new_or_row->ordercode."', ".$new_or_row->op_idx.", ".$new_or_row->op_price.", ";
						$insert_new_or_sql.= $new_or_row->op_reserve.", ".$new_or_row->op_rate.", ".$new_or_row->deli_price.", ".$new_or_row->deli_reserve.", ";
						$insert_new_or_sql.= $new_or_row->deli_rate.", ".date("YmdHis")." ) ";
						pmysql_query( $insert_new_or_sql, get_db_conn() );
						//배송비에 사용된 적립금을 옮겨준다
						$old_reserve_sql = "UPDATE tblorderproduct SET use_point = use_point - ".$new_or_row->deli_reserve." WHERE idx ='".$idx[$p]."'";
						pmysql_query( $old_reserve_sql, get_db_conn() );
						$new_reserve_sql = "UPDATE tblorderproduct SET use_point = use_point + ".$new_or_row->deli_reserve." WHERE idx ='".$op_idx."'";
						pmysql_query( $new_reserve_sql, get_db_conn() );
						*/
					}
				}
			}
			//주문 상품정보 상태를 변경한다.
			orderProductStepUpdate($exe_id, $ordercode, $idx[$p], $step, $oc_no, $opt1_change[$p], $opt2_change[$p], $opt2_pt_change[$p], $opt_text_s_change[$p], $opt_text_c_change[$p]  , '', '', '', $proc_type );
		}

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$old_step]."=>".$op_step[$step];
		//exdebug($osu_memo);
		//exit;

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$old_step}',
		'{$step}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());

	}

	// 주문취소번호를 리턴한다.
	return $oc_no;

}

/**
 * 주문 교환/반품 보류
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * idxs				: 상품 idx들 예) 1|2|3
 * oc_no			: 취소 번호
**/
function orderCancelHold($exe_id, $ordercode, $idxs, $oc_no) {

	//넘어온 파라미터를 정리한다.
	$idx = explode("|", $idxs);		// 상품배열을 분리한다.

	//주문 보류상태로 업데이트한다.
	$date=date("YmdHis");
	$sql   = " UPDATE tblorder_cancel SET accept_status = 'D', hold_date = '{$date}', hold_oc_step = oc_step, oc_step = '5' WHERE oc_no='".trim($oc_no)."' ";
	pmysql_query($sql,get_db_conn());

	for($p=0;$p < count($idx);$p++) {
		// 주문 상품정보 상태를 가져온다.
		list($redelivery_type)=pmysql_fetch_array(pmysql_query("select redelivery_type from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx='".trim($idx[$p])."' "));	
		// 주문/주문상품 전체 상태변경내역 처리
		orderStepChangeLog($exe_id, 'p', $ordercode, $idx[$p], '', '48', $redelivery_type);

		// 주문 상품정보 상태를 가져온다.
		list($old_step)=pmysql_fetch_array(pmysql_query("select oi_step1 from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));

		//현재 주문의 취소상태중 취소접수상태가 아닌 카운트를 가져온다
		list($op_cancel_41_cnt)=pmysql_fetch_array(pmysql_query("select count(*) as op_cancel_cnt from (select a.*, case when b.oc_step IS NULL THEN 9999 ELSE b.oc_step END AS oc_step from tblorderproduct a left join tblorder_cancel b on a.oc_no=b.oc_no) c WHERE ordercode='".trim($ordercode)."' AND (op_step NOT IN ('40','41','42') OR oc_step != '5' ) "));
		if ($op_cancel_41_cnt ==0) {		//모두 취소접수의 보류상태인 경우
			orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step, '48', '');
		}
	}
}

/**
 * 주문취소 복원(철회)
 * exe_id			: 실행자 정보(아이디|이름|타입)
 * ordercode		: 주문코드
 * oc_no			: 주문취소코드
 * proc_type		: 처리 구분(CS : CS에서 처리, AS : AS에서 처리)
**/
function orderCancelRestore($exe_id, $ordercode, $oc_no, $proc_type='') {
	global $op_step, $o_step;
	
	// 실행자 정보 추가 (2016.10.07 - 김재수 추가)
	$exe_id_arr	= explode("|", $exe_id);
	$reg_id			= $exe_id_arr[0];
	$reg_name		= $exe_id_arr[1];
	$reg_type		= $exe_id_arr[2];

	$productinfo	= "";
	// 주문취소 상품정보를 가져온다.
	$op_sql		= "SELECT * FROM tblorderproduct where ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."'";
	$op_result	= pmysql_query($op_sql,get_db_conn());
	$op_total	= pmysql_num_rows($op_result);
	$cnt = 0;
	while($op_row=pmysql_fetch_object($op_result)) {
		if ($cnt > 0) $productinfo	.= "|!@#|";
		$productinfo	.= $op_row->idx."!@#";
		$productinfo	.= $op_row->productname."!@#";
		$productinfo	.= $op_row->option_quantity."!@#";
		$productinfo	.= $op_row->opt1_name."!@#";
		$productinfo	.= $op_row->opt2_name."!@#";
		$productinfo	.= $op_row->option_price_text."!@#";
		$productinfo	.= $op_row->text_opt_subject."!@#";
		$productinfo	.= $op_row->text_opt_content."!@#";
		$productinfo	.= $op_row->opt1_change."!@#";
		$productinfo	.= $op_row->opt2_change."!@#";
		$productinfo	.= $op_row->option_price_text_change."!@#";
		$productinfo	.= $op_row->text_opt_subject_change."!@#";
		$productinfo	.= $op_row->text_opt_content_change."!@#";
		$productinfo	.= $op_row->redelivery_type;
		$cnt++;
	}
	pmysql_free_result($op_result);

	//주문접수시 입력된 로그의 정보를 가져온다.
	list($step_prev, $step_next)=pmysql_fetch_array(pmysql_query("select step_prev, step_next from tblorder_cancel_log where ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' order by ocl_no asc limit 1"));
	//현재 주문상태를 가져온다.
	list($old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo where ordercode='".trim($ordercode)."'"));

	$op_step_array		= array(
					0	=> "주문접수",
					1	=> "결제완료",
					2	=> "배송준비중",
					3	=> "배송중",
					4	=> "배송완료",
					40	=> "취소신청",
					41	=> "취소접수",
					42	=> "취소진행",
					44	=> "취소완료",
					50	=> "결제시도",
					51	=> "PG확인요망",
					54	=> "결제실패",
					);
	
	// 주문 상품정보를 가져온다.
	$op_sql		= "SELECT * FROM tblorderproduct Where ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' ";
	$op_result	= pmysql_query($op_sql,get_db_conn());
	$op_total	= pmysql_num_rows($op_result);

    $Sync = new Sync();

	while($op_row=pmysql_fetch_object($op_result)) {
		//메모에 등록된 내용을 설정한다.
		$osu_memo	= $op_step_array[$op_row->op_step]."=>". $op_step_array[$step_prev];

		$date=date("YmdHis");
		$log_sql = "INSERT INTO tblorderproduct_log(
		ordercode	,
		idx	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$ordercode}',
		'{$op_row->idx}',
		'{$op_row->op_step}',
		'{$step_prev}',
		'{$osu_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		@pmysql_query($log_sql,get_db_conn());
		// 주문/주문상품 전체 상태변경내역 처리
		orderStepChangeLog($exe_id, 'p', $ordercode, $op_row->idx, '', '49', $op_row->redelivery_type, $proc_type);

        //if($op_row->redelivery_type == "G") $sync_status = "H"; //교환철회
        //else $sync_status = "L";                                //반품철회
        if($step_prev == "3") $sync_status = "Y";
        else $sync_status = "F";
        // 싱크커머스에 철회정보 전송
        $arrayDatax = array(
            'ordercode' => $ordercode,
            'delivery_num' => '',
            'sync_status' => $sync_status,
            'sync_idx' => $op_row->idx
        );
        $srtn = $Sync->StatusChange($arrayDatax);

	}
	pmysql_free_result($op_result);

	if ($step_prev == 0) $qry	= ", deli_gbn = 'N' ";
	if ($step_prev == 1) $qry	= ", deli_gbn = 'N' ";
	if ($step_prev == 2) $qry	= ", deli_gbn = 'S' ";
	if ($step_prev == 3) $qry	= ", deli_gbn = 'Y' ";
	if ($step_prev == 4) $qry	= ", deli_gbn = 'F' ";

	// 주문상품의 상태값을 변경한다.
	$sql	 = " UPDATE tblorderproduct SET op_step = '{$step_prev}', oc_no = '0' ".$qry;
	if ($old_step1 > 2 || ($step_prev == 3 || $step_prev == 4)) { // 배송중, 배송완료일 경우 복원시
		$sql.= " ,redelivery_type='N', redelivery_date=NULL, opt1_change=NULL, opt2_change=NULL, self_goods_code_change=NULL, option_price_text_change=NULL, text_opt_subject_change=NULL, text_opt_content_change=NULL ";
	}
    $sql	.= "WHERE ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' ";
	//echo $sql."<br>";
	pmysql_query($sql,get_db_conn());

	// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
	$date=date("YmdHis");

	if( !pmysql_error() ){

		if($old_step2 > 0 || $step_prev == 3) {

			if ($old_step1 == 0) $qry	= ", deli_gbn = 'N' ";
			if ($old_step1 == 1) $qry	= ", deli_gbn = 'N' ";
			if ($old_step1 == 2) $qry	= ", deli_gbn = 'S' ";
			if ($old_step1 == 3 || $step_prev == 3) $qry	= ", deli_gbn = 'Y' ";
			if ($old_step1 == 4) $qry	= ", deli_gbn = 'F' ";

			// 주문의 상태값을 변경한다.
			if ($step_prev == 3) $qry	= ", oi_step1 = '{$step_prev}' ";
			$osu_sql = " UPDATE tblorderinfo SET oi_step2 = '0', pay_admin_proc = 'Y' ".$qry;
			if ($old_step1 > 2  || $step_prev == 3) { // 배송중, 배송완료일 경우 복원시
				$osu_sql.= " ,redelivery_type='N', redelivery_date=NULL ";
			}
			$osu_sql.= " WHERE ordercode='".trim($ordercode)."' ";
			//if ($step_prev == 3) $osu_sql	.= "AND oi_step1 != '{$step_prev}' ";
			//echo $osu_sql."<br>";
			pmysql_query($osu_sql,get_db_conn());

			//메모에 등록된 내용을 설정한다.
			$osu_memo	= $o_step[$old_step1][$old_step2]."=>". $o_step[$old_step1][0];
			//exdebug($osu_memo);
			//exit;

			$log_sql = "INSERT INTO tblorder_log(
			ordercode	,
			step1_prev	,
			step2_prev	,
			step1_next,
			step2_next,
			memo,
			reg_id,
			reg_name,
			reg_type,
			regdt) VALUES (
			'{$ordercode}',
			'{$old_step1}',
			'{$old_step2}',
			'{$old_step1}',
			'0',
			'{$osu_memo}',
			'{$reg_id}',
			'{$reg_name}',
			'{$reg_type}',
			'{$date}')";
			//echo $log_sql."<br>";
			@pmysql_query($log_sql,get_db_conn());

			orderStepChangeLog($exe_id, 'o', $ordercode, '', $old_step1, '49', '', $proc_type);
		}

		// 주문취소 복원 테이블에 저장한다.
		$rs_sql		= "INSERT INTO tblorder_cancel_restore(oc_no, productinfo, regdt) VALUES ('{$oc_no}', '{$productinfo}', '{$date}') RETURNING rs_no";
		//echo $rs_sql."<br>";
		$rs_res		= pmysql_query($rs_sql,get_db_conn());
		$rs_row		= pmysql_fetch_array($rs_res);
		$rs_no		= $rs_row[0];
		pmysql_free_result($rs_res);


		// 주문취소 테이블에 업데이트한다.
		$oc_sql	 = " UPDATE tblorder_cancel SET restore = 'Y', rs_no = '{$rs_no}', oc_step = '6' ";
		$oc_sql	.= "WHERE oc_no='".trim($oc_no)."' ";
		//echo $oc_sql."<br>";
		pmysql_query($oc_sql,get_db_conn());

		//메모에 등록된 내용을 설정한다.
		$oc_memo	= $op_step[$step_next]."=>". $op_step[$step_prev];

		// 상태변경이 정상적으로 완료되었을 경우 로그에 넣는다.
		$oc_log_sql = "INSERT INTO tblorder_cancel_log(
		oc_no	,
		ordercode	,
		step_prev	,
		step_next,
		memo,
		reg_id,
		reg_name,
		reg_type,
		regdt) VALUES (
		'{$oc_no}',
		'{$ordercode}',
		'{$step_next}',
		'{$step_prev}',
		'{$oc_memo}',
		'{$reg_id}',
		'{$reg_name}',
		'{$reg_type}',
		'{$date}')";
		//echo $oc_log_sql."<br>";
		@pmysql_query($oc_log_sql,get_db_conn());
	}
	//exit;

}

/**
 * 쿠폰 복구(재발급)
 * ordercode		: 주문코드
 * idx					: 상품 idx
 * m_id				: 주문자 ID
**/
function order_recovery_coupon($ordercode, $idx='', $m_id) {
	$sql = "SELECT coupon_code, op_idx, ci_no FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
	if ( $idx ) $sql.= "AND op_idx='{$idx}' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$coupon_code = $row->coupon_code;
		$op_idx = $row->op_idx;
		$ci_no = $row->ci_no;
		/* 쿠폰을 복원하는 소스 막음
		// 주문건중 취소가 안된 같은 쿠폰코드가 있는지 체크한다.
		list($coupon_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(op.*) coupon_cnt from tblorderproduct op LEFT JOIN tblcoupon_order co ON op.idx=co.op_idx WHERE op.ordercode='{$ordercode}' and op.idx != '{$op_idx}' AND co.coupon_code='{$coupon_code}' AND op.op_step !='44'"));
		if ($coupon_cnt == 0) { // 주문건중 취소가 안된 같은 쿠폰코드가 없다면
			pmysql_query("UPDATE tblcouponissue SET used='N' WHERE id='{$m_id}' AND coupon_code='{$coupon_code}'",get_db_conn());
		}*/

		// 주문건중 취소가 안된 같은 쿠폰코드가 있는지 체크한다.
		list($coupon_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(op.*) coupon_cnt from tblorderproduct op LEFT JOIN tblcoupon_order co ON op.idx=co.op_idx WHERE op.ordercode='{$ordercode}' and op.idx != '{$op_idx}' AND co.coupon_code='{$coupon_code}' AND co.ci_no='{$ci_no}' AND op.op_step !='44'"));
		if ($coupon_cnt == 0) { // 주문건중 취소가 안된 같은 쿠폰코드가 없다면
			$date = date("YmdHis");
			$date_be = date("YmdHis",strtotime("-15 seconds"));
			// 15초전에서 지금까지 중복발급이 있는지 체크
			list($coupon_same_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) coupon_same_cnt from tblcouponissue WHERE coupon_code='{$coupon_code}' AND id='{$m_id}' AND used='N' AND date BETWEEN '{$date_be}' AND '{$date}' "));
			if ($coupon_cnt == 0) { // 중복발급이 아니면
				//쿠폰 정보를 가져온다.
				$cou_sql		= "SELECT date_start,date_end FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}'  ";
				$cou_result	= pmysql_query($cou_sql,get_db_conn());

				if($cou_row = pmysql_fetch_object($cou_result)){
					if($cou_row->date_start>0) { // 기간이 정해져 있으면
						$date_start=$cou_row->date_start;
						$date_end=$cou_row->date_end;
					} else { //사용만료일이 있으면
						$date_start = substr($date,0,10);
						$date_end = date("Ymd23",strtotime("+".abs($cou_row->date_start)." day"));
						if ($date_end > $cou_row->date_end) { // 사용만료일이 쿠폰의 사용만료일보다 크면
							$date_end=$cou_row->date_end;
						}
					}

					// 쿠폰을 재발급 해준다.
					$recou_sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ('{$coupon_code}','{$m_id}','{$date_start}','{$date_end}','{$date}')";
					pmysql_query($recou_sql,get_db_conn());

					if(!pmysql_errno()) {
						//사용쿠폰수를 증가해 준다.
						$ucou_sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 WHERE coupon_code = '{$coupon_code}'";
						pmysql_query($ucou_sql,get_db_conn());
					}
				}
			}
		}
	}
	pmysql_free_result($result);
}

/**
 * 주문관련 메일
 * type_code		: 메일타입코드
 * ordercode		: 주문코드
 * oc_no			: 주문취소코드
**/
function order_cancel_mail($type_code, $ordercode, $oc_no) {
	global $_ShopInfo;
	$_data=new ShopData($_ShopInfo);
	$_data=$_data->shopdata;

    // 도메인 정보
    $sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
    $row        = pmysql_fetch_object(pmysql_query($sql));
    $shopurl    = str_replace("http://", "", $row->shopurl)."/";

	//return $_data->shopname."/".$shopurl."/".$_data->design_mail."/".$_data->info_email;
	if ($type_code == 'SendCancelMail') {
		#주문취소메일
		SendCancelMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	} else if ($type_code == 'SendReturnMail') {
		#반품요청메일
		SendReturnMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	} else if ($type_code == 'SendReturnokMail') {
		#반품완료메일
		SendReturnokMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	} else if ($type_code == 'SendRefundMail') {
		#환불안내메일
		SendRefundMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	} else if ($type_code == 'SendRequestMail') {
		#교환접수메일
		SendRequestMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	} else if ($type_code == 'SendRequestokMail') {
		#교환완료메일
		SendRequestokMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email,  $ordercode, 'N', 'Y', '', $oc_no);
	}
}

/**
 * 주문관련 SMS 발송(벤더)
 * ordercode		: 주문코드
 * oc_no			: 주문취소코드
**/
function order_cancel_sms_vender($ordercode, $oc_no) {
	global $_ShopInfo;
	$_data=new ShopData($_ShopInfo);
	$_data=$_data->shopdata;

	$sql="SELECT * FROM tblsmsinfo WHERE 1=1 LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	if($rowsms=pmysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		list($userid)=pmysql_fetch_array(pmysql_query("select id from tblorderinfo where ordercode='".trim($ordercode)."'"));

		list($vender)=pmysql_fetch_array(pmysql_query("select vender from tblorderproduct where ordercode='".trim($ordercode)."' AND oc_no='".trim($oc_no)."' limit 1"));
		list($p_mobile)=pmysql_fetch_array(pmysql_query("select p_mobile from tblvenderinfo where vender='".trim($vender)."'"));

		$msg_vender_cancel_push="[".strip_tags($_data->shopname)."] ID : [USERID]님이 [DATE]에 취소/반품/교환을 요청하였습니다.";
		$patten=array("[DATE]","[USERID]");
		$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$userid);

		$msg_vender_cancel_push=str_replace($patten,$replace,$msg_vender_cancel_push);
		$msg_vender_cancel_push=addslashes($msg_vender_cancel_push);

		$fromtel=$rowsms->return_tel;
		$date=0;
		$etcmsg="반품/환불요청메세지(벤더)";
		$temp=SendSMS($sms_id, $sms_authkey, $p_mobile, "", $fromtel, $date, $msg_vender_cancel_push, $etcmsg);
	}
	pmysql_free_result($result);
}

function orderCancelStatusStep($redelivery_type, $oc_step, $hold_oc_step='') {
	$status_txt	= "";
	if ($redelivery_type == 'N') $status_def = "취소";
	if ($redelivery_type == 'Y') $status_def = "반품";
	if ($redelivery_type == 'G') $status_def = "교환";

	if ($oc_step == '0') $status_txt = $status_def."신청";
	if ($oc_step == '1') $status_txt = $status_def."접수";
	if ($oc_step == '2') $status_txt = "제품도착";
	if ($oc_step == '3') $status_txt = $status_def."승인";
	if ($oc_step == '4') $status_txt = $status_def."완료";
	if ($oc_step == '5') {
		$status_txt = $status_def."보류";
		if ($hold_oc_step == '0') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."신청)</span>";
		if ($hold_oc_step == '1') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."접수)</span>";
		if ($hold_oc_step == '2') $status_txt .= "<br><span style='font-size:11px'>(제품도착)</span>";
		if ($hold_oc_step == '3') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."승인)</span>";
	}
	if ($oc_step == '6') $status_txt = $status_def."철회";

	return $status_txt;
}
?>
