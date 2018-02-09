<?php
header("Content-Type: text/html; charset=UTF-8");
//if($_SERVER['REMOTE_ADDR']!="203.238.36.58" && $_SERVER['REMOTE_ADDR']!="203.238.36.160" && $_SERVER['REMOTE_ADDR']!="203.238.36.161" && $_SERVER['REMOTE_ADDR']!="203.238.36.173" && $_SERVER['REMOTE_ADDR']!="203.238.36.178") exit;
//$ip_arr = array('203.238.36.173','203.238.36.178','210.122.73.58');
//if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //아이피 인증
Header("Pragma: no-cache");

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


	@extract($_GET);
	@extract($_POST);
	@extract($_SERVER);

	$PayMethod = $PayMethod;				//지불수단
	$M_ID = $MID;										//상점ID
	$MallUserID = $MallUserID;			//회원사 ID
	$Amt = $Amt;										//금액
	$name = $name	;								//구매자명
	$GoodsName = $GoodsName;				//상품명
	$TID = 	$TID;									//거래번호
	$MOID = $MOID;									//주문번호
	$AuthDate = $AuthDate;					//입금일시 (yyMMddHHmmss)	
	$ResultCode = $ResultCode;			//결과코드 ('4110' 경우 입금통보)
	$ResultMsg  = $ResultMsg;			//결과메시지	
	$VbankNum = $VbankNum;					//가상계좌번호
	$FnCd = $FnCd;									//가상계좌 은행코드
	$VbankName = $VbankName;				//가상계좌 은행명
	$VbankInputName = $VbankInputName;	//입금자 명
	
	//가상계좌채번시 현금영수증 자동발급신청이 되었을경우 전달되며 
	//RcptTID 에 값이 있는경우만 발급처리 됨	
	$RcptTID = $RcptTID;							//현금영수증 거래번호
	$RcptType = $RcptType;						//현금 영수증 구분(0:미발행, 1:소득공제용, 2:지출증빙용)
	$RcptAuthCode = $RcptAuthCode;		//현금영수증 승인번호

	//**********************************************************************************
	//이부분에 로그파일 경로를 수정해주세요.	
	 $logfile = fopen("./log/nice_common_return_".date("Ymd").".txt","a+");
	//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
	//**********************************************************************************
	 
  fwrite( $logfile,"************************************************\r\n");
  fwrite( $logfile,"PayMethod : ".$PayMethod."\r\n");
  fwrite( $logfile,"MID : ".$MID."\r\n");
  fwrite( $logfile,"MallUserID : ".$MallUserID."\r\n");
  fwrite( $logfile,"Amt : ".$Amt."\r\n");
  fwrite( $logfile,"name : ".mb_convert_encoding($name,'UTF-8','EUC-KR')."\r\n");
  fwrite( $logfile,"GoodsName : ".mb_convert_encoding($GoodsName,'UTF-8','EUC-KR')."\r\n");
  fwrite( $logfile,"TID : ".$TID."\r\n");
  fwrite( $logfile,"MOID : ".$MOID."\r\n");
  fwrite( $logfile,"AuthDate : ".$AuthDate."\r\n");
  fwrite( $logfile,"ResultCode : ".$ResultCode."\r\n");
  fwrite( $logfile,"ResultMsg : ".mb_convert_encoding($ResultMsg,'UTF-8','EUC-KR')."\r\n");
  fwrite( $logfile,"VbankNum : ".$VbankNum."\r\n");
  fwrite( $logfile,"FnCd : ".$FnCd."\r\n");
  fwrite( $logfile,"VbankName : ".mb_convert_encoding($VbankName,'UTF-8','EUC-KR')."\r\n");
  fwrite( $logfile,"VbankInputName : ".mb_convert_encoding($VbankInputName,'UTF-8','EUC-KR')."\r\n");
  fwrite( $logfile,"RcptTID : ".$RcptTID."\r\n");
  fwrite( $logfile,"RcptType : ".$RcptType."\r\n");
  fwrite( $logfile,"RcptAuthCode : ".$RcptAuthCode."\r\n");
  fwrite( $logfile,"************************************************\r\n");

  fclose( $logfile );
	
$date=date("YmdHis");
$order_no = $MOID;
$tno = $TID;
$ipgm_mnyx = $Amt;
$remitter = mb_convert_encoding($VbankInputName,'UTF-8','EUC-KR');
$bank_code = $FnCd;
$tx_tm = $AuthDate;
$noti_id = $MallUserID;

if(preg_match("/^(VBANK)$/", $PayMethod)) {
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$order_no."' ";
	$result=pmysql_query($sql,get_db_conn());
    //echo "sql = ".$sql."<br>";
	if($row=pmysql_fetch_object($result)) {
		$paymethod_shop = $row->paymethod;
	} else {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$order_no." 주문번호 존재하지 않음","$sql");
		}
	}
	pmysql_free_result($result);
}

$tblname="";
if(strstr("P", $paymethod_shop)) {
	$tblname="tblpcardlog";
} else if(strstr("OQ", $paymethod_shop)) {
	$tblname="tblpvirtuallog";
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/nice.php";
$query="ordercode=".$order_no."&tx_cd=".$ResultCode;
//echo "return_host = ".$return_host."<br>";
//echo "return_script = ".$return_script."<br>";

$rescode = "FAIL";
if ( $ResultCode == "4110" ) {
	if(strstr("OQ", $paymethod_shop)) {
		####################### ok가 "M|Y", status가 "N"인 경우에만 정상처리 ########################
		$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
		$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
        //echo "sql = ".$sql."<br>";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$query.="&price=".$ipgm_mnyx."&ok=Y";

			if($row->ok=="M" && $row->status=="N") {
                //echo "query = ".$query."<br>";
				$send_data=SendSocketPost($return_host, $return_script, $query);
                //echo "send_data = ".$send_data."<br>";
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok			= 'Y', ";
					$sql.= "bank_price	= '".$ipgm_mnyx."', ";
					$sql.= "remitter	= '".$remitter."', ";
					$sql.= "bank_code	= '".$bank_code."', ";
					$sql.= "bank_date	= '".$tx_tm."', ";
					$sql.= "receive_date= '".$date."', ";
					$sql.= "noti_id		= '".$noti_id."' ";
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
                    //echo "sql = ".$sql."<br>";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="OK";
						//ERP로 결제완료데이터를 보낸다.
						sendErporder($order_no);
                        // 싱크커머스로 결제완료 데이터를 보낸다
                        $Sync = new Sync();
                        $arrayDatax=array('ordercode'=>$order_no);
                        $srtn=$Sync->OrderInsert($arrayDatax);
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보 업데이트 오류","$sql");
						}
					}
				}
			}
		}
	}
}
echo $rescode;
?>