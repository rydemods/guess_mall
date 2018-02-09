<?php
    $Dir="../../";
    include_once($Dir."lib/init.php");
    include_once($Dir."lib/lib.php");

	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );

	/******************************************************************************** 
	 *
	 * �ٳ� �޴��� ����
	 *
	 * - ���� ��û ������ 
	 *	�ݾ� Ȯ�� �� ���� ��û
	 *
	 * ���� �ý��� ������ ���� ���ǻ����� �����ø� ���񽺰��������� ���� �ֽʽÿ�.
	 * DANAL Commerce Division Technique supporting Team 
	 * EMail : tech@danal.co.kr 
	 *
	 ********************************************************************************/


?>
<html>
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
</head>
<?php
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
	 * ConfirmOption�� 1�̸� CPID, AMOUNT �ʼ� ����
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

	if( $Res["Result"] == "0" && $Res2["Result"] == "0" )
	{
		/**************************************************************************
		 *
		 * ���� �Ϸῡ ���� �۾� 
		 * - AMOUNT, ORDERID �� ���� �ŷ����뿡 ���� ������ �ݵ�� �Ͻñ� �ٶ��ϴ�.
		 *
		 **************************************************************************/
        if ( $_POST['_ordercode'] == $Res2['ORDERID'] && $_POST['_price'] == $Res2["ItemAmt"] ) {

            $ordr_idxx      = $_POST['_ordercode'];
            $good_name      = iconv("EUC-KR", "UTF-8", $_POST['_goodname']);
            $good_mny       = $Res2["ItemAmt"];
            $PAY_FLAG       = "0000";
            $DELI_GBN       = "N";
            $ok             = "Y";
            $PAY_AUTH_NO    = $Res2["TID"];
            $MSG1           = iconv("EUC-KR", "UTF-8", "������� - ���ι�ȣ : ") . $PAY_AUTH_NO;
            $pay_data       = iconv("EUC-KR", "UTF-8", "���ι�ȣ : ") . $PAY_AUTH_NO;
            $paymethod      = "M";

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

            if(strlen(RootPath)>0) {
                $hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
                $pathnum=@strpos($hostscript,RootPath);
                $shopurl=substr($hostscript,0,$pathnum).RootPath;
            } else {
                $shopurl=$_SERVER['HTTP_HOST']."/";
            }

            $return_host=$_SERVER['HTTP_HOST'];
            $return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
            $return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx;

            $return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
            $return_data2 = iconv("EUC-KR", "UTF-8", str_replace("'","",$return_data));
            $sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
            pmysql_query($sql,get_db_conn());
            //backup_save_sql($sql);

            $temp = SendSocketPost($return_host,$return_script,$return_data);
            if($temp!="ok") {
                //error (���� �߼�)
                if(strlen(AdminMail)>0) {
                    @mail(AdminMail,"[Danal] ".$ordr_idxx." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
                }
            } else {
                pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
            }

        }

/*
        $sql  = "UPDATE tblorderinfotemp SET pay_auth_no = '" . $PAY_AUTH_NO . "', pay_data = '" . $pay_data . "', pay_flag = '" . $PAY_FLAG . "' ";
        $sql .= "WHERE ordercode='{$ordr_idxx}' ";
        pmysql_query($sql, get_db_conn());
*/
?>
<body>
<form name="Success" action="./Success.php" method="post">
<?php
	MakeFormInput($_POST);
	MakeFormInput($Res,array("Result","ErrMsg"));
	MakeFormInput($Res2,array("Result","ErrMsg"));
?>
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
		 * ���� ���п� ���� �۾� 
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

		include( "./Error.php" );
	}
?>
