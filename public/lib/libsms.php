<?

function getDuoKey(){
	$result = "smspduometissms";
	return $result;
}

function getDuoEmployEsntlKey(){
	$query = "select authkey from tblsmsinfo limit 1";
	$result = pmysql_fetch_array(pmysql_query($query));
	return $result[authkey];
}

function getReturnTel(){
	$query = "select return_tel from tblsmsinfo limit 1";
	$result = pmysql_fetch_array(pmysql_query($query));
	return $result[return_tel];
}

function duo_smsAuthCheck(){
	// cmd_mode => 작업 구분 값
	$cmd_mode = '_useAuthorityCheck';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();
	$data = array(
		"key" => $key,
		"esntl_key" => $esntl_key,
		"cmd_mode" => $cmd_mode, 
		"hashdata" => md5($cmd_mode.$key)
	);
	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch)); 
	curl_close($ch);
	$resultArr = Array(
		result => $result->result,
		employ_sms_ea => $result->resultList[0]->employ_sms_ea,
		msg => $result->msg,
		sms_cut_count => $result->sms_cut_count,
		lms_cut_count => $result->lms_cut_count,
		mms_cut_count => $result->mms_cut_count
	);
	//$remain_sms_count = $result->resultList[0]->employ_sms_ea ? $result->resultList[0]->employ_sms_ea : '0';

	return $resultArr;
}

function duo_smsEmpolyIdCheck($id=''){
	$cmd_mode = '_empolyIdCheck';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();

	$data = array(
		"key" => $key,
		"cmd_mode" => $cmd_mode, 
		"hashdata" => md5($cmd_mode.$key),
		"employ_id" => $id
	);
	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch)); 
	curl_close($ch);
	$resultArr = Array(
		'result' => $result->result,
		'msg' => urldecode($result->msg)
	);
	return $resultArr;
}

function duo_smsEmpolyAdd($data){
	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch)); 
	curl_close($ch);
	$resultArr = Array(
		'result' => $result->result,
		'esntl_key' => $result->resultList->esntl_key,
		'msg' => urldecode($result->msg)
	);
	return $resultArr;
}

function duo_setPayInfo($data){
	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch)); 
	curl_close($ch);
	//$resultArr = Array('result'=>$result->result,'msg'=>urldecode($result->msg));

	return $result;
}

function duo_sms_send($data){
	//for dev
	//if(isdev()) $data['esntl_key'] = "WFLpCfg7RbeXfwcKT2j8j9fQ6RisV391I";
	if(!array_key_exists('ip', $data)) $data['ip'] = $_SERVER['REMOTE_ADDR'];

	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch)); 
	curl_close($ch);
	$resultArr = Array(
		'result' => $result->result,
		'send_object_count' => $result->send_object_count,
		'msg' => mb_convert_encoding(urldecode($result->msg),'utf-8','euc-kr'),
        //'msg' => $result->msg,
		'goods_img_url' => $result->goods_img_url
	);
	return $resultArr;
}

function duo_smsSendList($data){
	if(!array_key_exists('ip', $data)) $data['ip'] = $_SERVER['REMOTE_ADDR'];

	$url = "http://smsp.duometis.co.kr/interx/req_info.php";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch));
	//exdebug($result);
	curl_close($ch);
	$resultArr = Array(
		'result' => $result->result,
		'msg' => urldecode($result->msg),
		'total_count' => $result->total_count,
		'resultList' => urldecode_data($result->resultList),
		'commcode' => urldecode_data($result->commcode)
	);
	return $resultArr;
}

function urldecode_data($arg)
{
	if(is_object($arg)){
		foreach($arg as $k => $v){
			$arg->$k = urldecode_data($v);
		}
	}elseif(is_array($arg)){
		foreach($arg as $k => $v){
			$arg[$k] = urldecode_data($v);
		}
	}else{
		if(gettype($arg) == "string")
			$arg = urldecode($arg);
	}
	return $arg;
}

/**
 * SMS 타입별 발송 처리
 * type		: 전송타입 (예 : mem_join - 회원가입 / mem_orderok - 주문완료)
 * pkey		: 기본이 되는 값 (예 : id - 회원가입 / ordercode - 주문완료)
 * skey		: 서브가 되는 값 (예 : oc_no - 취소/반품/교환 / num - 문의(상품문의))
 * ekey		: 기타 값 (예 : '비밀번호' - 정보변경일 경우 변경된 값)
 * smstype  : SMS / LMS / MMS 전송 type
**/
function sms_autosend($type, $pkey, $skey='', $ekey='', $smstype = 'MMS', $subject = 'Shinwon' ) {
	$sql = "SELECT id, authkey, {$type},msg_{$type}, return_tel, admin_tel, subadmin1_tel, subadmin2_tel, subadmin3_tel, ";
	$sql.= "sleep_time1, sleep_time2 ";
	$sql.= "FROM tblsmsinfo /*WHERE ".$type."='Y'*/  ";

	$result	= pmysql_query($sql,get_db_conn());
	if($row	= pmysql_fetch_array($result)) {
		$_sms	= $row;
	}
	pmysql_free_result($result);	
	if ($_sms) {
		$sms_id				= $_sms[0];	// sms_id
		$sms_authkey		= $_sms[1];	// sms_authkey
		$sms_chk			= $_sms[2];	// 전송타입
		$sms_msg			= $_sms[3];	// 전송메시지
		$sms_from			= $_sms[4];	// 회신 전화번호
		$sms_admin			= $_sms[5];	// 관리자 전화번호
		$sms_date			= 0;				// 전송시간

		// 전송시간 설정 ( SMS 임시중단 )
		if( $_sms['sleep_time1'] != $_sms['sleep_time2'] ) {
			$time = date("Hi");
			if( $_sms['sleep_time2'] < 12 && $time <= sprintf( "%02d59", $_sms['sleep_time2'] ) ) $time += 2400;
			if( $_sms['sleep_time2'] < 12 && $_sms['sleep_time1'] > $_sms['sleep_time2'] ) $_sms['sleep_time2'] += 24;

			if( $time < sprintf( "%02d00", $_sms['sleep_time1'] ) || $time >= sprintf( "%02d59", $_sms['sleep_time2'] ) ){
				if( $time < sprintf( "%02d00", $_sms['sleep_time1'] ) ) $day = 0;
				else $day = 1;
				
				$sms_date = date( "Y-m-d", strtotime("+{$day} day") ).sprintf( " %02d:00:00", $_sms['sleep_time1'] );
			}
		}
		
		// 타입별로 정보를 불러온다.
		if (
				$type == 'mem_join' || $type == 'admin_join' || $type == 'mem_passwd' || $type == 'admin_passwd' ||
				$type == 'mem_out' || $type == 'admin_out' || $type == 'mem_qna' || $type == 'admin_qna' ||
				$type == 'mem_personal' || $type == 'admin_personal' || $type == 'mem_birth' || $type == 'admin_birth' ||
				$type == 'mem_modify' || $type == 'admin_modify' 
			) { // 회원정보를 불러온다.
			$id				=	$pkey; // 회원아이디
			if ($type == 'mem_out') { // 탈퇴인경우 탈퇴정보에서 가져온다.
				list($name, $mobile, $date)=pmysql_fetch_array(pmysql_query("select  name, tel, date  from tblmemberout WHERE id='".trim($id)."'"));	// 이름과 핸드폰번호, 탈퇴일자를 불러온다.
				$date	= substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
			} else {// 기본 회원 정보에서 가져온다.				
				list($name, $mobile)=pmysql_fetch_array(pmysql_query("select  name, mobile  from tblmember WHERE id='".trim($id)."'"));	// 이름과 핸드폰번호를 불러온다.
			}
			if ($type == 'mem_passwd' || $type == 'admin_passwd' ) $pw	= $ekey;		// 비밀번호
			if ($type == 'mem_modify' || $type == 'admin_modify' ) $part	= $ekey;		// 변경된정보
		} else { // 주문정보를 불러온다.
			$ordercode		=	$pkey; // 주문번호
			//list($pay_data, $paymethod, $name, $mobile, $price)=pmysql_fetch_array(pmysql_query("select  oi.pay_data, oi.paymethod, m.name, oi.sender_tel, (oi.price::integer + oi.deli_price::integer - oi.dc_price::integer - oi.reserve::integer) as tot_price from tblorderinfo as oi left join tblmember as m on oi.id=m.id WHERE oi.ordercode='".trim($ordercode)."'"));	// 결제정보, 결제타입, 이름과 핸드폰번호, 주문금액을 불러온다.
            list($pay_data, $paymethod, $name, $mobile, $price)=pmysql_fetch_array(pmysql_query("select  oi.pay_data, oi.paymethod, oi.sender_name as name, oi.sender_tel, (oi.price::integer + oi.deli_price::integer - oi.dc_price::integer - oi.reserve::integer) as tot_price from tblorderinfo as oi WHERE oi.ordercode='".trim($ordercode)."'"));	// 결제정보, 결제타입, 이름과 핸드폰번호, 주문금액을 불러온다.

			$pd_arr		= explode(" (예금주:", $pay_data);
			$pd_arr[0]	= str_replace(" ", ":", $pd_arr[0]);
			$pd_arr2		= explode(":", $pd_arr[0]);
			$depositor	= substr($pd_arr[1],0,-1);

			$bank		= trim($pd_arr2[0]); // 은행명
			$account	= trim($pd_arr2[1]); // 은행계좌
			$depositor	= trim($depositor); // 예금주
			$price		= number_format($price); // 주문금액

			if ( $skey != '' && ( $type == 'mem_delinum' || $type == 'admin_delinum' ) ) {
				list($delicom, $delinum)=pmysql_fetch_array(pmysql_query("select dc.company_name, op.deli_num from tblorderproduct as op left join tbldelicompany dc on op.deli_com=dc.code WHERE op.ordercode='".trim($ordercode)."' and op.idx IN ('".str_replace(",","','",trim($skey))."') and op.deli_num != '' and op.deli_com != '' limit 1"));
			} else if( $type == 'mem_delivery' || $type == 'admin_delivery' ) {
				list($delicom, $delinum)=pmysql_fetch_array( pmysql_query( "select dc.company_name, op.deli_num from tblorderproduct as op left join tbldelicompany dc on op.deli_com=dc.code WHERE op.ordercode='".trim($ordercode)."' limit 1" ) );
			}
		}

		$pattern			= array(
									"[NAME]",
									"[ORDERID]",
									"[BANK]",
									"[ACCOUNT]",
									"[DEPOSITOR]",
									"[PRICE]",
									"[DELICOM]",
									"[DELINUM]",
									"[PW]",
									"[DATE]",
									"[PART]"
								);
		$replace		= array(
									$name,
									$ordercode,
									$bank,
									$account,
									$depositor,
									$price,
									$delicom,
									$delinum,
									$pw,
									$date,
									$part
								);
		$sms_msg		= str_replace($pattern, $replace, $sms_msg);
		$sms_msg		= AddSlashes($sms_msg);
		
		if( // 관리자 발송인 경우
			$type == 'admin_join' || $type == 'admin_orderok' || $type == 'admin_order' || 
			$type == 'admin_bank' || $type == 'admin_bankok' || $type == 'admin_delivery' ||
			$type == 'admin_delinum' || $type == 'admin_cancel' || $type == 'admin_refund' ||
			$type == 'admin_passwd' || $type == 'admin_out' || $type == 'admin_qna' ||
			$type == 'admin_personal' || $type == 'admin_birth' || $type == 'admin_modify' ||
			$type == 'admin_auth'
		){
			$mobile			= str_replace( "-", "", str_replace( " ", "", $sms_admin ) );
		} else { // 회원 발송인 경우
			if ($type == 'mem_personal' || $type == 'admin_personal' ) { // qna는 전화번호를 따로 남긴다.
				$mobile = $ekey;
			}
			$mobile			= str_replace(" ","",$mobile);
			$mobile			= str_replace("-","",$mobile);
		}
		$sms_etcmsg	 = $type;
		$sms_etcmsg	.= " / ".$pkey;
		if ($skey) $sms_etcmsg	.= " / ".$skey;
		if ($ekey) $sms_etcmsg	.= " / ".$ekey;

		//echo "$type - SendSMS($sms_id, $sms_authkey, $mobile, '', $sms_from, $sms_date, $sms_msg, $sms_etcmsg)<br>";
		$temp = '';
		
		if( strlen( $type ) > 0 && strlen( $pkey ) > 0 && $sms_chk == 'Y' ) { // SMS를 보낸다.
			if( $smstype == 'SMS' ) {
				$temp = SendSMS($sms_id, $sms_authkey, $mobile, '', $sms_from, $sms_date, $sms_msg, $sms_etcmsg);
			} else {
				$temp = SendMMS($sms_id, $sms_authkey, $mobile, '', $sms_from, $sms_date, $sms_msg, $sms_etcmsg, $_FILES, $subject );
			}
		}
		return $temp;
		/*
		$sms_etcmsg="회원가입 축하메세지(관리자)";
		if($admin_join=="Y") {
			$adminphone	= $row->admin_tel;
			if(strlen($row->subadmin1_tel)>8) $adminphone.=",".$row->subadmin1_tel;
			if(strlen($row->subadmin2_tel)>8) $adminphone.=",".$row->subadmin2_tel;
			if(strlen($row->subadmin3_tel)>8) $adminphone.=",".$row->subadmin3_tel;
			$adminphone=str_replace(" ","",$adminphone);
			$adminphone=str_replace("-","",$adminphone);
			$temp=SendSMS($sms_id, $sms_authkey, $adminphone, "", $fromtel, $date, $sms_msg, $sms_etcmsg);
		}*/
	}
}
?>