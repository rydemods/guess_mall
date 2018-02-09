<?php
    $Dir="../../";
    include_once($Dir."lib/init.php");
    include_once($Dir."lib/lib.php");

	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );

	/******************************************************************************** 
	 *
	 * 다날 휴대폰 결제
	 *
	 * - 결제 요청 페이지 
	 *	금액 확인 및 결제 요청
	 *
	 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	 * DANAL Commerce Division Technique supporting Team 
	 * EMail : tech@danal.co.kr 
	 *
	 ********************************************************************************/

    if(strlen(RootPath)>0) {
        $hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $pathnum=@strpos($hostscript,RootPath);
        $shopurl=substr($hostscript,0,$pathnum).RootPath;
    } else {
        $shopurl=$_SERVER['HTTP_HOST']."/";
    }

	$BillErr = false;
	$TransR = array();

	/*
	 * Get ServerInfo
	 */
	$ServerInfo = $_POST["ServerInfo"];  

	/*
	 * NCONFIRM
	 */
	$nConfirmOption = 1; 
	$TransR["Command"] = "NCONFIRM";
	$TransR["OUTPUTOPTION"] = "DEFAULT";
	$TransR["ServerInfo"] = $ServerInfo;
	$TransR["IFVERSION"] = "V1.1.2";
	$TransR["ConfirmOption"] = $nConfirmOption;

	/*
	 * ConfirmOption이 1이면 CPID, AMOUNT 필수 전달
	 */
	if( $nConfirmOption == 1 )
	{
		$TransR["CPID"] = $ID;
//		$TransR["AMOUNT"] = $AMOUNT;
		$TransR["AMOUNT"] = $_POST['_price'];
	}

	$Res = CallTeledit( $TransR,false );

	if( $Res["Result"] == "0" )
	{
		/*
		 * NBILL
		 */
		$TransR = array();

		$nBillOption = 0;
		$TransR["Command"] = "NBILL";
		$TransR["OUTPUTOPTION"] = "DEFAULT";
		$TransR["ServerInfo"] = $ServerInfo;
		$TransR["IFVERSION"] = "V1.1.2";
		$TransR["BillOption"] = $nBillOption;

		$Res2 = CallTeledit( $TransR,false );

		if( $Res2["Result"] != "0" )
		{
			$BillErr = true;
		}
	}

    $ordr_idxx      = $_POST['_ordercode'];
    $good_name      = iconv("EUC-KR", "UTF-8", $_POST['_goodname']);
    $good_mny       = $Res["AMOUNT"];
    $PAY_AUTH_NO    = $Res["TID"];
    $paymethod      = "M";
    // 결제 체크를 위한 값
    $paycode        = $_POST['_paycode'];  //결제코드
    $basketidxs     = $_POST['_basketidxs']; // 장바구니 idx

	if( $Res["Result"] == "0" && $Res2["Result"] == "0" )
	{
		/**************************************************************************
		 *
		 * 결제 완료에 대한 작업 
		 * - AMOUNT, ORDERID 등 결제 거래내용에 대한 검증을 반드시 하시기 바랍니다.
		 *
		 **************************************************************************/
        $return_resurl="http://".$shopurl.FrontDir."payresult.php?ordercode=".$_POST['_ordercode'];

        if ( $_POST['_ordercode'] == $Res2['ORDERID'] && $_POST['_price'] == $Res["AMOUNT"] ) {
            $PAY_FLAG       = "0000";
            $DELI_GBN       = "N";
            $ok             = "Y";
            $MSG1           = iconv("EUC-KR", "UTF-8", "정상승인 - 승인번호 : ") . $PAY_AUTH_NO;
            $pay_data       = iconv("EUC-KR", "UTF-8", "승인번호 : ") . $PAY_AUTH_NO;
        } else {
            $PAY_FLAG       = "9999";
            $DELI_GBN       = "C";
            $ok             = "N";
            $MSG1           = $Res2["ErrMsg"];
            $pay_data       = $Res2["ErrMsg"];
        }

        $sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
        pmysql_query($sql,get_db_conn());

        $sql = "
            INSERT INTO tblpmobilelog  
            (
                ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg
            )
            VALUES
            (
                '".$ordr_idxx."', '".$PAY_AUTH_NO."', '".$pay_data."', 'E', '".$ok."', '".date("YmdHis")."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
            )
        ";
        pmysql_query($sql,get_db_conn());

        $return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
        $return_data .= "&paycode=".$paycode."&basketidxs=".$basketidxs;
        $return_data2 = str_replace("'","",$return_data);
        $sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
        pmysql_query($sql,get_db_conn());
        //backup_save_sql($sql);

        $return_host=$_SERVER['HTTP_HOST'];
        $return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
        $temp = SendSocketPost($return_host,$return_script,$return_data);
        if( trim( $temp ) != "ok" ) {
            //error (메일 발송)
            if(strlen(AdminMail)>0) {
                @mail(AdminMail,"[Danal] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
            }
            # 주문 check 테이블 비우기
            if( strlen( $paycode ) > 0 ){
                pmysql_query( "DELETE FROM tblorder_check WHERE paycode = '".$paycode."' ", get_db_conn() );
            }
            // 결제 취소
            $ReturnData = '';
            $CancelData = array(
                'ordercode' => $ordr_idxx,
            );
            $CancelCURL = curl_init();
            $CancelUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace($_SERVER['HTTP_HOST'],"",$shopurl).'paygate/E/cancel.ajax.php';
            curl_setopt( $CancelCURL, CURLOPT_URL, $CancelUrl );
            curl_setopt( $CancelCURL, CURLOPT_POST, 1 );
            curl_setopt( $CancelCURL, CURLOPT_POSTFIELDS, $CancelData );
            curl_setopt( $CancelCURL, CURLOPT_RETURNTRANSFER, TRUE );
            $ReturnData = curl_exec( $CancelCURL );
            curl_close( $CancelCURL );
            $ArrayReturnData = json_decode( $ReturnData, true );
            if( $ArrayReturnData['type'] == '1' ){
                $sql_tmp = "UPDATE tblorderinfotemp SET deli_gbn = 'C' WHERE ordercode = '".$ordr_idxx."' ";
                pmysql_query( $sql_tmp, get_db_conn() );
                $sql_ptmp = "UPDATE tblorderproducttemp SET deli_gbn = 'C' WHERE ordercode = '".$ordr_idxx."' ";
            } else {
                //backup_save_sql( $ArrayReturnData['msg'] );
            }
            $Res["ErrMsg"] = "결제중 중복결제로 인한 ".$ArrayReturnData['msg'];
            $_POST["BackURL"]  = "javascript:location.href='".$return_resurl."';";
            $ok = "N";
        } else {
            //pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
        }

        if ( $ok == "N" ) {
            $Result		= $Res["Result"];
            $ErrMsg		= $Res["ErrMsg"];
            $AbleBack	= false;
            $BackURL	= $_POST["BackURL"];
            $IsUseCI	= $_POST["IsUseCI"];
            $CIURL		= $_POST["CIURL"];
            $BgColor	= $_POST["BgColor"];

		    include( "./Error.php" );
            exit;
        }
?>
<html>
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
</head>
<body>
<form name="Success" action="./Success.php" method="post">
<?php
	MakeFormInput($_POST);
	MakeFormInput($Res,array("Result","ErrMsg"));
	MakeFormInput($Res2,array("Result","ErrMsg"));
?>
    <input type="hidden" name="return_resurl" value="<?=urlencode($return_resurl)?>">
</form>
<script>
	document.Success.submit();
</script>
</body>
</html>
<?php
	} else {
		/**************************************************************************
		 *
		 * 결제 실패에 대한 작업 
		 *
		 **************************************************************************/

		if( $BillErr ) $Res = $Res2;

		$Result		= $Res["Result"];
		$ErrMsg		= $Res["ErrMsg"];
		$AbleBack	= false;
		$BackURL	= $_POST["BackURL"];
		$IsUseCI	= $_POST["IsUseCI"];
		$CIURL		= $_POST["CIURL"];
		$BgColor	= $_POST["BgColor"];

        $PAY_FLAG       = "9999";
        $DELI_GBN       = "C";
        $ok             = "N";
        $MSG1           = $Res2["ErrMsg"];
        $pay_data       = $Res2["ErrMsg"];

        $sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
        pmysql_query($sql,get_db_conn());

        $sql = "
            INSERT INTO tblpmobilelog  
            (
                ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg
            )
            VALUES
            (
                '".$ordr_idxx."', '".$PAY_AUTH_NO."', '".$pay_data."', 'E', '".$ok."', '".date("YmdHis")."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
            )
        ";
        pmysql_query($sql,get_db_conn());

        $return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
        $return_data2 = str_replace("'","",$return_data);
        $sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
        pmysql_query($sql,get_db_conn());
        //backup_save_sql($sql);

        $return_host=$_SERVER['HTTP_HOST'];
        $return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
        $temp = SendSocketPost($return_host,$return_script,$return_data);
        if($temp!="ok") {
            //error (메일 발송)
            if(strlen(AdminMail)>0) {
                @mail(AdminMail,"[Danal] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
            }
        } else {
            pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
        }

		include( "./Error.php" );
	}
?>
