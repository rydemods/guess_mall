<?php

//*******************************************************************************
// FILE NAME : INIpayResult.php
// DATE : 2006.05
// �̴Ͻý� ������� �Աݳ��� ó��demon���� �Ѿ���� �Ķ���͸� control �ϴ� �κ� �Դϴ�.
//*******************************************************************************

$TEMP_IP = $_SERVER['REMOTE_ADDR'];
$PG_IP  = substr($TEMP_IP,0, 10);

if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138")  //PG���� ���´��� IP�� üũ
{
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	Header("Pragma: no-cache");

	@extract($_GET);
	@extract($_POST);
	@extract($_SERVER);

	//**********************************************************************************
	//  �̺κп� �α����� ��θ� �������ּ���.	
	$INIpayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D";      // �̴����� Ȩ���͸�
	//**********************************************************************************

	$msg_id = $msg_id;	     //�޼��� Ÿ��
	$tno = $no_tid;	     //�ŷ���ȣ
	$order_no = $no_oid;	     //���� �ֹ���ȣ
	$id_merchant = $id_merchant;   //���� ���̵�
	$cd_bank = $cd_bank;	   //�ŷ� �߻� ��� �ڵ�
	$cd_deal = $cd_deal;	   //��� ��� �ڵ�
	$dt_trans = $dt_trans;	 //�ŷ� ����
	$tm_trans = $tm_trans;	 //�ŷ� �ð�
	$no_msgseq = $no_msgseq;       //���� �Ϸ� ��ȣ
	$cd_joinorg = $cd_joinorg;     //���� ��� �ڵ�

	$dt_transbase = $dt_transbase; //�ŷ� ���� ����
	$noti_id = $no_transeq;     //�ŷ� �Ϸ� ��ȣ
	$type_msg = $type_msg;	 //�ŷ� ���� �ڵ�
	$cl_close = $cl_close;	 //���� �����ڵ�
	$cl_kor = $cl_kor;	     //�ѱ� ���� �ڵ�
	$no_msgmanage = $no_msgmanage; //���� ���� ��ȣ
	$no_vacct = $no_vacct;	 //������¹�ȣ
	$amt_input = $amt_input;       //�Աݱݾ�
	$amt_check = $amt_check;       //�̰��� Ÿ���� �ݾ�
	$nm_inputbank = $nm_inputbank; //�Ա� ���������
	$nm_input = $nm_input;	 //�Ա� �Ƿ���
	$dt_inputstd = $dt_inputstd;   //�Ա� ���� ����
	$dt_calculstd = $dt_calculstd; //���� ���� ����
	$flg_close = $flg_close;       //���� ��ȭ

	$logfile = fopen( $INIpayHome . "/log/result.log", "a+" );
	
	fwrite( $logfile,"************************************************");
	fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
	fwrite( $logfile,"NO_TID : ".$no_tid."\r\n");
	fwrite( $logfile,"NO_OID : ".$no_oid."\r\n");
	fwrite( $logfile,"NO_VACCT : ".$no_vacct."\r\n");
	fwrite( $logfile,"AMT_INPUT : ".$amt_input."\r\n");
	fwrite( $logfile,"NM_INPUTBANK : ".$nm_inputbank."\r\n");
	fwrite( $logfile,"NM_INPUT : ".$nm_input."\r\n");
	fwrite( $logfile,"************************************************");
	fwrite( $logfile,"��ü �����"."\r\n");
	fwrite( $logfile, $msg_id."\r\n");
	fwrite( $logfile, $cd_bank."\r\n");
	fwrite( $logfile, $dt_trans."\r\n");
	fwrite( $logfile, $tm_trans."\r\n");
	fwrite( $logfile, $no_msgseq."\r\n");
	fwrite( $logfile, $type_msg."\r\n");
	fwrite( $logfile, $cl_close."\r\n");
	fwrite( $logfile, $cl_kor."\r\n");
	fwrite( $logfile, $no_msgmanage."\r\n");
	fwrite( $logfile, $amt_check."\r\n");
	fwrite( $logfile, $dt_inputstd."\r\n");
	fwrite( $logfile, $dt_calculstd."\r\n");
	fwrite( $logfile, $flg_close."\r\n");
	fwrite( $logfile, "\r\n");
	fclose( $logfile );

	$rescode="";
	$date=date("YmdHis");
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$order_no."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
	} else {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$order_no." �ֹ���ȣ �������� ����","$sql");
		}
	}
	pmysql_free_result($result);

	if(strstr("OQ", $paymethod)) {
		if(strlen(RootPath)>0) {
			$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
			$pathnum=@strpos($hostscript,RootPath);
			$shopurl=substr($hostscript,0,$pathnum).RootPath;
		} else {
			$shopurl=$_SERVER['HTTP_HOST']."/";
		}

		if(strlen($type_msg)==0 && $paymethod=="Q") {
			$check_host=$_SERVER['HTTP_HOST'];
			$check_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl)."paygate/D/escrow/INIescrow.php";
			$check_query="hanatid=".$tno."&EscrowType=dr&invno=check&transtype=S0";			
			$check_data=SendSocketPost($check_host, $check_script, $check_query);
			$check_data_exp = explode("|",$check_data);
			
			if($check_data_exp[0]!="4913") {
				$type_msg="0200";
			}
		}

		$tblname="";
		if(strstr("P", $paymethod)) {
			$tblname="tblpcardlog";
		} else if(strstr("OQ", $paymethod)) {
			$tblname="tblpvirtuallog";
		}

		$return_host=$_SERVER['HTTP_HOST'];
		$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/inicis.php";
		$query="ordercode=".$order_no."&type_msg=".$type_msg;

		####################### ok�� "M|Y", status�� "N"�� ��쿡�� ����ó�� ########################
		$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
		$sql.= "WHERE ordercode='".$order_no."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$query.="&price=".$amt_input."&ok=";
			if($type_msg=="0400") $query.="C";
			else $query.="Y";

			if($type_msg=="0400") {
				//if($row->noti_id==$noti_id) {
					if($row->ok=="Y" && $row->status=="N") {
						$send_data=SendSocketPost($return_host, $return_script, $query);
						$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
						if (substr($send_data,0,2)=="OK") {
							$sql = "UPDATE ".$tblname." SET ";
							$sql.= "ok			= 'M', ";
							$sql.= "bank_price	= NULL, ";
							$sql.= "remitter	= '', ";
							$sql.= "bank_code	= '', ";
							$sql.= "bank_date	= '', ";
							$sql.= "receive_date= '' ";
							$sql.= "WHERE ordercode='".$order_no."' ";
							pmysql_query($sql,get_db_conn());
							if(!pmysql_error()) {
								$rescode="OK";
							} else {
								if(strlen(AdminMail)>0) {
									@mail(AdminMail,"[PG] ".$order_no." ������� �Ա��뺸��� ������Ʈ ����","$sql");
								}
							}
						}
					}
				//}
			} else {
				if($row->ok=="M" && $row->status=="N") {
					$send_data=SendSocketPost($return_host, $return_script, $query);
					$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
					if (substr($send_data,0,2)=="OK") {
						$sql = "UPDATE ".$tblname." SET ";
						$sql.= "ok			= 'Y', ";
						$sql.= "bank_price	= '".(int)$amt_input."', ";
						$sql.= "remitter	= '".$nm_input."', ";
						$sql.= "bank_code	= '".substr($cd_bank,-2)."', ";
						$sql.= "bank_date	= '".$dt_trans.$tm_trans."', ";
						//$sql.= "noti_id		= '".$noti_id."', ";
						$sql.= "receive_date= '".$date."' ";
						$sql.= "WHERE ordercode='".$order_no."' ";
						pmysql_query($sql,get_db_conn());
						if(!pmysql_error()) {
							$rescode="OK";
						} else {
							if(strlen(AdminMail)>0) {
								@mail(AdminMail,"[PG] ".$order_no." ������� �Ա��뺸 ������Ʈ ����","$sql");
							}
						}
					}
				}
			}
		}
	}
}
echo $rescode;
