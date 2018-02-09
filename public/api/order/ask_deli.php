<?php
/**
* CJ대한통운 택배 접수 / 취소
* 
* 접수 : TB_RCPT_SEJUNG010
* 추척 : TB_TRACE_SEJUNG020
*
* 2016.12.05 접수 → 취소 → 재요청 가능하도록 수정. (reqdate)
* 2016.10.24 합포장키에서 날짜 제거 (date("Ymd")."_".)
*/
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr    = array();

$code    = 0;
$message = "success";

$cancel_yn = $_POST["cancel_yn"]; // cancel=Y : 접수취소
$ordercode = $_POST["ordercode"];
$storecode = $_POST["storecode"];
$reqdate   = $_POST["reqdate"];   //요청일(날짜+시분초) date hour min sec (ex)051914
$orderidx  = array_unique((array)$_POST["op_idx"]);
sort($orderidx);

try {
	if ( empty($ordercode) )
		throw new Exception("주문번호가 없습니다.", 1);
	if ( empty($storecode) )
		throw new Exception("상점코드가 없습니다.", 1);
	if ( empty($orderidx) )
		throw new Exception("주문상세인덱스(op_idx)가 없습니다.", 1);

	$cust_id     = "30250545";
	$reg_id      = "SHINWON";
	$cust_use_no = "S_".$ordercode."_".$orderidx[0]; // S{주문번호}_{idx중 최소값} S는 싱크커머스[매장발송] 구분값
	if( $reqdate ) $cust_use_no .= "_".$reqdate;     // 2016-12-05 재요청 가능하도록 날짜+시분초 추가
	$mpck_key    = $cust_id."_".$cust_use_no;        // 2016-10-24 합포장키에서 날짜 제거 (date("Ymd")."_".)
	$req_dv_cd   = "01";                             // 01:요청, 02:취소

	// CUST_USE_NO : S_ 붙이기 이전 주문
	if( $ordercode == "2016101620341030685A" && $orderidx[0] == "3192" ){
		$cust_use_no = $ordercode."_".$orderidx[0];
	}

	if( $cj_dbconn = cj_dbconnect() ){
		if( $cancel_yn=="Y"){
			### [요청 취소시]
			$req_dv_cd = "02";
			# 운송장 생성여부 확인
			$query = "SELECT count(*) as CNT, max(INVC_NO) as INVC_NO FROM TB_TRACE_SHINWON020 WHERE CUST_USE_NO = :cust_use_no ";
			$stid = oci_parse($cj_dbconn, $query);
			oci_bind_by_name($stid, ":cust_use_no", $cust_use_no);
			oci_execute($stid);
			if($row = oci_fetch_array($stid, OCI_ASSOC)){
				if( $row['CNT'] > 0 ){
					$resultArr = array(
						'delivery_num' => $row['INVC_NO'],
						'delivery_com' => "01  ", //char(3)
						'delivery_name'=> "CJ대한통운택배"
					);
					throw new Exception("운송장 번호가 생성되어 취소할 수 없습니다. 지점으로 문의바랍니다.", 1);
				}
			}
			oci_free_statement($stid);
			# 취소대상 존재 확인 / 합포장키(MPCK_KEY) 구하기
			$query = "SELECT count(*) as CNT, min(MPCK_KEY) as MPCK FROM TB_RCPT_SHINWON010 WHERE CUST_USE_NO = :cust_use_no AND REQ_DV_CD = '01'";
			$stid = oci_parse($cj_dbconn, $query);
			oci_bind_by_name($stid, ":cust_use_no", $cust_use_no);
			oci_execute($stid);
			if($row = oci_fetch_array($stid, OCI_ASSOC)){
				if( $row['CNT'] == 0 )
					throw new Exception("취소대상이 없습니다.", 1);
				else
					$mpck_key = $row['MPCK'];
			}
			oci_free_statement($stid);
			# 이미 취소요청 되었는지 확인
			$query = "SELECT count(*) as CNT FROM TB_RCPT_SHINWON010 WHERE CUST_USE_NO = :cust_use_no AND REQ_DV_CD = '02'";
			$stid = oci_parse($cj_dbconn, $query);
			oci_bind_by_name($stid, ":cust_use_no", $cust_use_no);
			oci_execute($stid);
			if($row = oci_fetch_array($stid, OCI_ASSOC)){
				if( $row['CNT'] > 0 )
					throw new Exception("이미 취소요청되었습니다.", 1);
			}
			oci_free_statement($stid);
		}else{
			### [접수요청시] # 이미 접수/취소 요청되었는지 확인
			$query = "SELECT count(*) as CNT, max(REQ_DV_CD) as REQ_DV_CD FROM TB_RCPT_SHINWON010 WHERE CUST_USE_NO = :cust_use_no";
			$stid = oci_parse($cj_dbconn, $query);
			oci_bind_by_name($stid, ":cust_use_no", $cust_use_no);
			oci_execute($stid);
			if($row = oci_fetch_array($stid, OCI_ASSOC)){
				if( $row['CNT'] > 0 && $row['REQ_DV_CD'] == "01" )
					throw new Exception("이미 접수요청되었습니다.", 1);
				elseif( $row['CNT'] > 0 && $row['REQ_DV_CD'] == "02" )
					throw new Exception("이미 취소요청되었습니다.", 1);
			}
			oci_free_statement($stid);
		}
		oci_close($cj_dbconn);
	}else{
		throw new Exception("CJ DB Connect Error.", 1);
	}

	# tblorderinfo
	$sql = "select * from tblorderinfo where ordercode='{$ordercode}'";
	$res = pmysql_query($sql,get_db_conn());
	$ord = pmysql_fetch_object($res);
	pmysql_free_result($res);
	$send_tel = get_phone_num($ord->sender_tel2);
	$send_cp  = get_phone_num($ord->sender_tel);
	$recv_tel = get_phone_num($ord->receiver_tel1);
	$recv_cp  = get_phone_num($ord->receiver_tel2);
	if( !$recv_tel[1] ) $recv_tel = $recv_cp;
	list($recv_zip,$recv_addr) = explode("\n", $ord->receiver_addr);
	$recv_zip  = str_replace("우편번호 : ", "", $recv_zip);
	$recv_addr = str_replace("주소 : ", "", $recv_addr);
	if( trim(recv_zip)=="" )
		throw new Exception("수화인 우편번호가 없습니다.", 1);
	else if( trim($recv_addr)=="" )
		throw new Exception("수화인 주소가 없습니다.", 1);
	list($rcvr_addr1,$rcvr_addr2) = get_addr_div($recv_addr);

	# tblorderproduct.
	$idx = implode(",",$orderidx);
	$sql_add = ( $idx ) ? " and idx in ({$idx})" : "";
	$sql = "select * from tblorderproduct where ordercode='{$ordercode}'{$sql_add}";
	$res = pmysql_query($sql,get_db_conn());
	while( $row = pmysql_fetch_object($res) ){
		$prdname[] = $row->productname;
		$store[$row->store_code] = true;
		//$storecode = $row->store_code;
	}
	pmysql_free_result($res);
	if( count($store) != 1 ){
		$message = count($store)==0 ? "주문정보에 매장코드가 없습니다." : "주문정보에 매장코드가 한 개 이상입니다.";
		throw new Exception($message, 1);
	}
	$goodscnt = count($prdname)==1 ? "" : "외 ".count($prdname)."건";
	$goodsname = $prdname[0].$goodscnt;

	# on_store_data
	//$sql = "select * from on_store_data where store_code='{$storecode}'";
	
	$sql = "select * from tblstore where store_code='{$storecode}'";
	$res = pmysql_query($sql,get_db_conn());
	$sto = pmysql_fetch_object($res);
	pmysql_free_result($res);
	//list($sendr_addr1,$sendr_addr2) = get_addr_div($sto->address1,$sto->address2);
	
	//우편번호 짜르기
	$addr_sido		= explode(" ",$sto->address);
	$addr_one="";
	$addr_two="";
	foreach($addr_sido as $as=>$asv){
		if($asv!=''){
			if($as=="0" || $as=="1" || $as=="2"){
				$addr_one[].=$asv;
			}else{
				$addr_two[].=$asv;
			}
		}
	}

	$phone_num=explode("-",$sto->phone);

	if($sto->brandcd=="Q"){
		$brandname="아이코닉";
	}else{
		list($brandname)=pmysql_fetch("select brandname2 from tblproductbrand where brandcd='".$sto->brandcd."'");
	}
	$a_one=implode(" ",$addr_one);
	$b_one=implode(" ",$addr_two)." (".$brandname.")";

	//전화번호 짜르기
	$tel_cut		= explode(" ",$sto->phone);

	if( !$sto->cj_deli_code )
		throw new Exception("발송고객코드(CUST_MGMT_DLCM_CD)가 없습니다.", 1);

	$rcpt = array(
		CUST_ID           => $cust_id,            //[PK]고객ID
		RCPT_YMD          => date("Ymd"),         //[PK]접수일자
		CUST_USE_NO       => $cust_use_no,        //[PK]고객사용번호. 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호
		RCPT_DV           => "01",                //[PK]접수구분     (01: 일반, 02: 반품)
		WORK_DV_CD        => "01",                //[PK]작업구분코드 (01: 일반, 02: 교환, 03: A/S)
		REQ_DV_CD         => $req_dv_cd,          //[PK]요청구분코드 (01: 요청, 02: 취소)
		MPCK_KEY          => $mpck_key,           //[PK]합포장키. 다수데이터를 한 송장에 출력할 경우 처리(합포없는 경우 YYYYMMDD_고객ID_고객사용번호 or YYYYMMDD_고객ID_운송장번호)
		MPCK_SEQ          => 1,                   //[PK]합포장순번. 합포장 처리건수가 다수일경우 SEQ처리를 수행한다.( 합포없는경우 무조건 1 )
		CAL_DV_CD         => "01",                //[NOT NULL]정산구분코드 (01: 계약 운임, 02: 자료 운임 (계약운임인지 업체에서 넣어주는 운임으로할지..))
		FRT_DV_CD         => "03",                //[NOT NULL]운임구분코드 (01: 선불, 02: 착불, 03: 신용)
		CNTR_ITEM_CD      => "01",                //[NOT NULL]계약품목코드 (01: 일반 품목)
		BOX_TYPE_CD       => "02",                //[NOT NULL]박스타입코드 (01: 극소, 02: 소, 03: 중, 04: 대, 05: 특대)
		BOX_QTY           => 1,                   //[NOT NULL]박스수량. 택배 박스 수량
		#FRT              => "",                  //[운임. 운임적용구분이 자료 운임일 경우 등록 처리
		CUST_MGMT_DLCM_CD => $sto->cj_deli_code,    //[NOT NULL]고객관리거래처코드. 주관사 관리 협력업체 코드 혹은 택배사 관리 업체코드
		SENDR_NM          => "(주)신원 ".$sto->name,  //[NOT NULL]송화인명
		SENDR_TEL_NO1     => "02", //[NOT NULL]송화인전화번호1
		SENDR_TEL_NO2     => "1661", //[NOT NULL]송화인전화번호2
		SENDR_TEL_NO3     => "2585", //[NOT NULL]송화인전화번호3 (암호화 구간)
		SENDR_CELL_NO1    => $phone_num[0],                  //송화인휴대폰번호1
		SENDR_CELL_NO2    => $phone_num[1],                  //송화인휴대폰번호2
		SENDR_CELL_NO3    => $phone_num[2],                  //송화인휴대폰번호3 (암호화 구간)
		#SENDR_SAFE_NO1   => "",                  //송화인안심번호1
		#SENDR_SAFE_NO2   => "",                  //송화인안심번호2
		#SENDR_SAFE_NO3   => "",                  //송화인안심번호3
		SENDR_ZIP_NO      => $sto->post,   //[NOT NULL]송화인우편번호
		SENDR_ADDR        => $a_one,        //[NOT NULL]송화인주소
		//SENDR_DETAIL_ADDR => $sendr_addr2,        //[NOT NULL]송화인상세주소
		SENDR_DETAIL_ADDR => $b_one,        //[NOT NULL]송화인상세주소
		RCVR_NM           => $ord->receiver_name, //[NOT NULL]수화인명
		RCVR_TEL_NO1      => $recv_tel[0],        //[NOT NULL]수화인전화번호1
		RCVR_TEL_NO2      => $recv_tel[1],        //[NOT NULL]수화인전화번호2
		RCVR_TEL_NO3      => $recv_tel[2],        //[NOT NULL]수화인전화번호3 (암호화 구간)
		RCVR_CELL_NO1     => $recv_cp[0],         //수화인휴대폰번호1
		RCVR_CELL_NO2     => $recv_cp[1],         //수화인휴대폰번호2
		RCVR_CELL_NO3     => $recv_cp[2],         //수화인휴대폰번호3 (암호화 구간)
		#RCVR_SAFE_NO1    => "",                  //수화인안심번호1
		#RCVR_SAFE_NO2    => "",                  //수화인안심번호2
		#RCVR_SAFE_NO3    => "",                  //수화인안심번호3
		RCVR_ZIP_NO       => $recv_zip,           //[NOT NULL]수화인우편번호
		RCVR_ADDR         => $rcvr_addr1,         //[NOT NULL]수화인주소
		RCVR_DETAIL_ADDR  => $rcvr_addr2,         //[NOT NULL]수화인상세주소 (암호화 구간)
		ORDRR_NM          => $ord->sender_name,   //주문자명
		ORDRR_TEL_NO1     => $send_tel[0],        //주문자전화번호1
		ORDRR_TEL_NO2     => $send_tel[1],        //주문자전화번호2
		ORDRR_TEL_NO3     => $send_tel[2],        //주문자전화번호3 (암호화 구간)
		ORDRR_CELL_NO1    => $send_cp[0],         //주문자휴대폰번호1
		ORDRR_CELL_NO2    => $send_cp[1],         //주문자휴대폰번호2
		ORDRR_CELL_NO3    => $send_cp[2],         //주문자휴대폰번호3 (암호화 구간)
		#ORDRR_SAFE_NO1   => "",                  //주문자안심번호1
		#ORDRR_SAFE_NO2   => "",                  //주문자안심번호2
		#ORDRR_SAFE_NO3   => "",                  //주문자안심번호3
		ORDRR_ZIP_NO      => "",                  //주문자우편번호
		ORDRR_ADDR        => "",                  //주문자주소 (전체 주소 관리 고객사는 여기에 모두 입력)
		ORDRR_DETAIL_ADDR => "",                  //주문자상세주소 (암호화 구간)
		#INVC_NO          => "",                  //운송장번호
		#ORI_INVC_NO      => "",                  //원운송장번호
		#ORI_ORD_NO       => "",                  //원주문번호
		COLCT_EXPCT_YMD  => date( 'Ymd', strtotime( '+1 day', time() ) ),                  //집화예정일자
		#COLCT_EXPCT_HOUR => "",                  //집화예정시간
		#SHIP_EXPCT_YMD   => "",                  //배송예정일자
		#SHIP_EXPCT_HOUR  => "",                  //배송예정시간
		PRT_ST            => "01",                //[NOT NULL]출력상태 (01: 미출력, 02: 선출력, 03: 선발번 (반품은 선발번이 없음))
		#ARTICLE_AMT      => "",                  //물품가액
		REMARK_1          => $ord->order_msg2,    //비고1 배송메세지1(비고)
		#REMARK_2         => "",                  //비고2 배송메세지2(송화인비고)
		#REMARK_3         => "",                  //비고3 배송메세지3(수화인비고)
		#COD_YN           => "",                  //COD여부. 대면결제 서비스 업체의 경우 대면결제 발생시 Y로 셋팅
		GDS_CD            => "",                  //상품코드
		GDS_NM            => $goodsname,          //[NOT NULL]상품명
		#GDS_QTY          => "",                  //상품수량 내품 수량
		#UNIT_CD          => "",                  //단품코드
		#UNIT_NM          => "",                  //단품명
		#GDS_AMT          => "",                  //상품가액
		#ETC_1            => "",                  //기타1
		#ETC_2            => "",                  //기타2
		#ETC_3            => "",                  //기타3
		#ETC_4            => "",                  //기타4
		#ETC_5            => "",                  //기타5
		DLV_DV            => "01",                //[NOT NULL]택배구분 (택배 : '01', 중량물(설치물류) : '02', 중량물(비설치물류) : '03')
		#RCPT_ERR_YN      => "",                  //접수에러여부. DEFAULT : 'N'
		#RCPT_ERR_MSG     => "",                  //접수에러메세지
		EAI_PRGS_ST       => "01",                //[NOT NULL]EAI전송상태. DEFAULT : '01'
		#EAI_ERR_MSG      => "",                  //에러메세지
		REG_EMP_ID        => $reg_id,             //[NOT NULL]등록사원ID
		REG_DTIME         => "SYSDATE",           //[NOT NULL]등록일시
		MODI_EMP_ID       => $reg_id,             //[NOT NULL]수정사원ID
		MODI_DTIME        => "SYSDATE"            //[NOT NULL]수정일시
	);

	foreach ( $rcpt as $fieldname => $value ){
		$fieldnames[] = $fieldname;
		#$fieldvalue[] = ($value=="SYSDATE") ? $value : ":".strtolower($fieldname);
		$values[] = ($value=="SYSDATE") ? $value : "'".str_replace("'","''",$value)."'";
	}
	$fld_sql = implode(",",$fieldnames);
	#$val_sql = implode(",",$fieldvalue);
	$val_sql = implode(",",$values);

	if( $cj_dbconn = cj_dbconnect() ){
		$query = "INSERT INTO TB_RCPT_SHINWON010 ({$fld_sql}) VALUES ({$val_sql})";
		#echo "<li>$query</li>";
		$stid = oci_parse($cj_dbconn, $query);
		$return = oci_execute($stid);
		if( !$return ){
			$error = oci_error($stid);
			api_log($error['message']);
			if( strpos($error['message'],"ORA-00001:")===0 )
				throw new Exception("이미 요청되었습니다.", 1);
			else
				throw new Exception("CJ DB Query Error : ".$error['message'], 1);
		}
		oci_free_statement($stid);
		oci_close($cj_dbconn);
	}else{
		throw new Exception("CJ DB Connect Error.", 1);
	}
} catch(Exception $e) {
	$code    = $e->getCode();
	$message = $e->getMessage();
}
$resultTotArr["result"]  = $resultArr;
$resultTotArr["code"]    = $code;
$resultTotArr["message"] = $message;

if( $code ) api_log( "[error] ".json_encode_kr($resultTotArr) );
echo json_encode($resultTotArr);

//(CJ요청) 주소 : 3번째 공백에서 분할 (RCVR_ADDR, RCVR_DETAIL_ADDR등)
function get_addr_div($addr1, $addr2=""){
	if( trim($addr2) == "" ){
		$tmp = explode(" ",$addr1);
		if( count($tmp) > 3 ) {
			$rtn_addr1 = $tmp[0]." ".$tmp[1]." ".$tmp[2];
			$rtn_addr2 = "";
			for($i=3; $i<count($tmp); $i++)
				$rtn_addr2.= $tmp[$i]." ";
			$rtn_addr2 = trim($rtn_addr2);
		} else {
			$rtn_addr1 = $addr1;
			$rtn_addr2 = "-";
		}
		return array($rtn_addr1,$rtn_addr2);
	}else
		return array($addr1,$addr2);
}

# 2016.12.22 예외처리:전화번호가 길면 $rtn_val[2]가 길어져서 리턴되도록 수정.
function get_phone_num($str,$idx=""){
	$rtn_val = array();
	$str = str_replace("－","-",$str);
	$num = preg_replace('/\D/','',$str);
	//if(strlen($num)<8) return false;
	if(substr_count($str,"-")==2)
		$rtn_val = explode("-",$str);
	else{
		if(strpos($num,"01")===0){ //cp
			$rtn_val[0] = substr($num,0,3);
			if(strlen($num)==10){
				$rtn_val[1] = substr($num,3,3);
				$rtn_val[2] = substr($num,6,4);
			}elseif(strlen($num)>=11){
				$rtn_val[1] = substr($num,3,4);
				$rtn_val[2] = substr($num,7);
			}
		}elseif(strpos($num,"02")===0){ //Seoul
			$rtn_val[0] = "02";
			if(strlen($num)==9){
				$rtn_val[1] = substr($num,2,3);
				$rtn_val[2] = substr($num,5,4);
			}elseif(strlen($num)>=10){
				$rtn_val[1] = substr($num,2,4);
				$rtn_val[2] = substr($num,6);
			}
		}elseif(strpos($num,"0")===0){ //etc
			$rtn_val[0] = substr($num,0,3);
			if(strlen($num)==10){
				$rtn_val[1] = substr($num,3,3);
				$rtn_val[2] = substr($num,6,4);
			}elseif(strlen($num)>=11){
				$rtn_val[1] = substr($num,3,4);
				$rtn_val[2] = substr($num,7);
			}
		}else
			$rtn_val[0] = $num;
	}
	if( !$rtn_val[0] ) $rtn_val[0] = "";
	if( !$rtn_val[1] ) $rtn_val[1] = "";
	if( !$rtn_val[2] ) $rtn_val[2] = "";
	if(in_array($idx,array('0','1','2')))
		return $rtn_val[$idx];
	else
		return $rtn_val;
}
?>