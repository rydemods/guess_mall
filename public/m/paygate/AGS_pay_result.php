<?php
/**********************************************************************************************
*
* ������Ʈ : AGSMobile V1.0
* (�� �� ������Ʈ�� ������ �� �ȵ���̵忡�� �̿��Ͻ� �� ������ �Ϲ� �������������� ������ �Ұ��մϴ�.)
*
* ���ϸ� : AGS_pay_result.php
* �ۼ����� : 2010/10/6
*
* ���ϰ�������� ó���մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

//������
$AuthTy 		= trim( $_POST["AuthTy"] );				//��������
$SubTy 			= trim( $_POST["SubTy"] );				//�����������
$rStoreId 		= trim( $_POST["rStoreId"] );			//��üID
$rAmt 			= trim( $_POST["rAmt"] );				//�ŷ��ݾ�
$rOrdNo 		= trim( $_POST["rOrdNo"] );				//�ֹ���ȣ
$rProdNm 		= trim( $_POST["rProdNm"] );			//��ǰ��
$rOrdNm			= trim( $_POST["rOrdNm"] );				//�ֹ��ڸ�

//������Ű���(�ſ�ī��,�ڵ���,�Ϲݰ������)�� ���
$rSuccYn 		= trim( $_POST["rSuccYn"] );			//��������
$rResMsg 		= trim( $_POST["rResMsg"] );			//���л���
$rApprTm 		= trim( $_POST["rApprTm"] );			//���νð�

//�ſ�ī�����
$rBusiCd 		= trim( $_POST["rBusiCd"] );			//�����ڵ�
$rApprNo 		= trim( $_POST["rApprNo"] );			//���ι�ȣ
$rCardCd 		= trim( $_POST["rCardCd"] );			//ī����ڵ�
$rDealNo 		= trim( $_POST["rDealNo"] );			//�ŷ�������ȣ

//�ſ�ī��(�Ƚ�,�Ϲ�)
$rCardNm 		= trim( $_POST["rCardNm"] );			//ī����
$rMembNo 		= trim( $_POST["rMembNo"] );			//��������ȣ
$rAquiCd 		= trim( $_POST["rAquiCd"] );			//���Ի��ڵ�
$rAquiNm 		= trim( $_POST["rAquiNm"] );			//���Ի��

//�ڵ���
$rHP_TID 		= trim( $_POST["rHP_TID"] );			//�ڵ�������TID
$rHP_DATE 		= trim( $_POST["rHP_DATE"] );			//�ڵ���������¥
$rHP_HANDPHONE 	= trim( $_POST["rHP_HANDPHONE"] );		//�ڵ��������ڵ�����ȣ
$rHP_COMPANY 	= trim( $_POST["rHP_COMPANY"] );		//�ڵ���������Ż��(SKT,KTF,LGT)

//�������
$rVirNo 		= trim( $_POST["rVirNo"] );				//������¹�ȣ ��������߰�
$VIRTUAL_CENTERCD = trim( $_POST["VIRTUAL_CENTERCD"] );	//������� �Ա������ڵ�

//����������ũ��
$ES_SENDNO	= trim( $_POST["ES_SENDNO"] );				//����������ũ��(������ȣ)

/*
$ERRMSG = "";

if( empty( $rStoreId ) || $rStoreId == "" )
{
	$ERRMSG .= "�������̵� �Է¿��� Ȯ�ο�� <br>";		//�������̵�
}

if( empty( $rOrdNo ) || $rOrdNo == "" )
{
	$ERRMSG .= "�ֹ���ȣ �Է¿��� Ȯ�ο�� <br>";		//�ֹ���ȣ
}

if( empty( $rProdNm ) || $rProdNm == "" )
{
	$ERRMSG .= "��ǰ�� �Է¿��� Ȯ�ο�� <br>";			//��ǰ��
}

if( empty( $rAmt ) || $rAmt == "" )
{
	$ERRMSG .= "�ݾ� �Է¿��� Ȯ�ο�� <br>";			//�ݾ�
}

/*
if( empty( $DeviId ) || $DeviId == "" )
{
	$ERRMSG .= "�ܸ�����̵� �Է¿��� Ȯ�ο�� <br>";	//�ܸ�����̵�
}

if( empty( $rSuccYn ) || $rSuccYn == "" )
{
	$ERRMSG .= "�������� �Է¿��� Ȯ�ο�� <br>";		//��������
}

if( strlen($ERRMSG) == 0 )
{
	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}

	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
	$return_resurl=$shopurl."m/payresult.php?ordercode=".$rOrdNo;
	
	
}

echo "<script>";
echo "opener.location.href=\"http://".$return_resurl."\";\n";
echo "window.close();";
echo "</script>";
exit;
*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META content="user-scalable=no, initial-scale = 1.0, maximum-scale=1.0, minimum-scale=1.0" name=viewport>
<META content=telephone=no name=format-detection>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR">
<title>�ô�����Ʈ</title>
<style type="text/css">
body { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
</style>
<script language=javascript> // "����ó����" �˾�â �ݱ�
var _ua = window.navigator.userAgent.toLowerCase();

var browser = {
	model: _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/) ? _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/)[0] : "",
	skt : /msie/.test( _ua ) && /nate/.test( _ua ),
	lgt : /msie/.test( _ua ) && /([010|011|016|017|018|019]{3}\d{3,4}\d{4}$)/.test( _ua ),
	opera : (/opera/.test( _ua ) && /(ppc|skt)/.test(_ua)) || /opera mobi/.test( _ua ),
	ipod : /webkit/.test( _ua ) && /\(ipod/.test( _ua ) ,
	iphone : /webkit/.test( _ua ) && /\(iphone/.test( _ua ),
	lgtwv : /wv/.test( _ua ) && /lgtelecom/.test( _ua )
};

if(browser.opera) {
	document.write("<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75\" \/>");
} else if (browser.ipod || browser.iphone) {
	setTimeout(function() { if(window.pageYOffset == 0){ window.scrollTo(0, 1);} }, 100);
}
</script>
<script language=javascript>
/***********************************************************************************
* �� ������ ����� ���� �ڹٽ�ũ��Ʈ
*		
*	������ ����� [ī�����]�ÿ��� ����Ͻ� �� �ֽ��ϴ�.
*  
*   �ش��� �����ǿ� ���ؼ� ������ ����� �����մϴ�.
*     ���� ���Ŀ��� �Ʒ��� �ּҸ� �˾�(630X510)���� ��� ���� ��ȸ �� ����Ͻñ� �ٶ��ϴ�.
*	  �� �˾��� ����������ȸ ������ �ּ� : 
*	     	 http://www.allthegate.com/support/card_search.html
*		�� (�ݵ�� ��ũ�ѹٸ� 'yes' ���·� �Ͽ� �˾��� ���ñ� �ٶ��ϴ�.) ��
*
***********************************************************************************/
function show_receipt()
{
	if("<?=$rSuccYn?>"== "y" && "<?=$AuthTy?>"=="card")
	{
		var send_dt = appr_tm.value;
		
		url="http://www.allthegate.com/customer/receiptLast3.jsp"
		url=url+"?sRetailer_id="+sRetailer_id.value;
		url=url+"&approve="+approve.value;
		url=url+"&send_no="+send_no.value;
		url=url+"&send_dt="+send_dt.substring(0,8);
		
		location.href = url;
	}
	else
	{
		alert("�ش��ϴ� ���������� �����ϴ�");
	}
}
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=320 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>���� ���</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=320 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright width=110>�������� : </td>
						<td class=clsleft width=220>
							<?php

							if($AuthTy == "card")
							{
								if($SubTy == "isp")
								{
									echo "�ſ�ī�����-��������(ISP)";
								}	
								else if($SubTy == "visa3d")
								{
									echo "�ſ�ī�����-�Ƚ�Ŭ��";
								}
								else if($SubTy == "normal")
								{
									echo "�ſ�ī�����-�Ϲݰ���";
								}
								
							}
							else if($AuthTy == "hp")
							{
								echo "�ڵ�������";
							}
							else if($AuthTy == "virtual")
							{
								echo "������°���";
							}
							?>
						</td>
					</tr>
					<tr>
						<td class=clsright>�������̵� : </td>
						<td class=clsleft><?=$rStoreId?></td>
					</tr>
					<tr>
						<td class=clsright>�ֹ���ȣ : </td>
						<td class=clsleft><?=$rOrdNo?></td>
					</tr>
					<tr>
						<td class=clsright>�ֹ��ڸ� : </td>
						<td class=clsleft><?=$rOrdNm?></td>
					</tr>
					<tr>
						<td class=clsright>��ǰ�� : </td>
						<td class=clsleft><?=$rProdNm?></td>
					</tr>
					<tr>
						<td class=clsright>�����ݾ� : </td>
						<td class=clsleft><?=$rAmt?></td>
					</tr>
					<tr>
						<td class=clsright>�������� : </td>
						<td class=clsleft><?=$rSuccYn?></td>
					</tr>
					<tr>
						<td class=clsright>ó���޼��� : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
<?				if($AuthTy == "card" || $AuthTy == "virtual") { ?>
					<tr>
						<td class=clsright>���νð� : </td>
						<td class=clsleft><?=$rApprTm?></td>
					</tr>
<?				}
				if($AuthTy == "card" && $rSuccYn == "y") {?>
					<tr>
						<td class=clsright>�����ڵ� : </td>
						<td class=clsleft><?=$rBusiCd?></td>
					</tr>
					<tr>
						<td class=clsright>���ι�ȣ : </td>
						<td class=clsleft><?=$rApprNo?></td>
					</tr>
					<tr>
						<td class=clsright>ī����ڵ� : </td>
						<td class=clsleft><?=$rCardCd?></td>
					</tr>
					<tr>
						<td class=clsright>�ŷ���ȣ : </td>
						<td class=clsleft><?=$rDealNo?></td>
					</tr>
<?				}
				if($AuthTy == "card" && ($SubTy == "visa3d" || $SubTy == "normal") && $rSuccYn == "y") {?>
					<tr>
						<td class=clsright>ī���� : </td>
						<td class=clsleft><?=$rCardNm?></td>
					</tr>
					<tr>
						<td class=clsright>���Ի��ڵ� : </td>
						<td class=clsleft><?=$rAquiCd?></td>
					</tr>
					<tr>
						<td class=clsright>���Ի�� : </td>
						<td class=clsleft><?=$rAquiNm?></td>
					</tr>
					<tr>
						<td class=clsright>��������ȣ : </td>
						<td class=clsleft><?=$rMembNo?></td>
					</tr>					
<?				}
				if($AuthTy == "hp" ) {?>
					<tr>
						<td class=clsright>�ڵ�������TID : </td>
						<td class=clsleft><?=$rHP_TID?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ���������¥ : </td>
						<td class=clsleft><?=$rHP_DATE?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ��������ڵ�����ȣ : </td>
						<td class=clsleft><?=$rHP_HANDPHONE?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ���������Ż�� : </td>
						<td class=clsleft><?=$rHP_COMPANY?></td>
					</tr>
<?				}
				if($AuthTy == "virtual" ) {?>
					<tr>
						<td class=clsright>�Աݰ��¹�ȣ : </td>
						<td class=clsleft><?=$rVirNo?></td>
					</tr>
                    <tr><!-- �����ڵ�(20) : �츮���� -->
						<td class=clsright>�Ա����� : </td>
						<td class=clsleft><?=getCenter_cd($VIRTUAL_CENTERCD)?></td>
					</tr>
                    <tr>
						<td class=clsright>�����ָ� : </td>
						<td class=clsleft>(��)������ȿ��</td>
					</tr>
					<tr>
						<td class=clsright>����������ũ��(SEND_NO) : </td>
						<td class=clsleft><?=$ES_SENDNO?></td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>������ :</td>
						<!--��������������ؼ������ִ°�-------------------->
						<input type=hidden name=sRetailer_id value="<?=$rStoreId?>"><!--�������̵�-->
						<input type=hidden name=approve value="<?=$rApprNo?>"><!---���ι�ȣ-->
						<input type=hidden name=send_no value="<?=$rDealNo?>"><!--�ŷ�������ȣ-->
						<input type=hidden name=appr_tm value="<?=$rApprTm?>"><!--���νð�-->
						<!--��������������ؼ������ִ°�-------------------->
						<td class=clsleft><input type="button" value="������" onclick="javascript:show_receipt();"></td>
					</tr>
					<tr>
						<td colspan=2>&nbsp;</td>
					</tr>
					<tr>
						<td align=center colspan=2>ī�� �̿������ ����ó�� <font color=red>������ ������������(��)</font>�� ǥ��˴ϴ�.</td>
					</tr>
<?				}	?>
					
				</table>
				</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.</td> 
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?
	function getCenter_cd($VIRTUAL_CENTERCD){
		if($VIRTUAL_CENTERCD == "39"){
			echo "�泲����";
		}else if($VIRTUAL_CENTERCD == "34"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "04"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "11"){
			echo "�����߾�ȸ";
		}else if($VIRTUAL_CENTERCD == "31"){
			echo "�뱸����";
		}else if($VIRTUAL_CENTERCD == "32"){
			echo "�λ�����";
		}else if($VIRTUAL_CENTERCD == "02"){
			echo "�������";
		}else if($VIRTUAL_CENTERCD == "45"){
			echo "�������ݰ�";
		}else if($VIRTUAL_CENTERCD == "07"){
			echo "�����߾�ȸ";
		}else if($VIRTUAL_CENTERCD == "48"){
			echo "�ſ���������";
		}else if($VIRTUAL_CENTERCD == "26"){
			echo "(��)��������";
		}else if($VIRTUAL_CENTERCD == "05"){
			echo "��ȯ����";
		}else if($VIRTUAL_CENTERCD == "20"){
			echo "�츮����";
		}else if($VIRTUAL_CENTERCD == "71"){
			echo "��ü��";
		}else if($VIRTUAL_CENTERCD == "37"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "23"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "35"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "21"){
			echo "(��)��������";
		}else if($VIRTUAL_CENTERCD == "03"){
			echo "�߼ұ������";
		}else if($VIRTUAL_CENTERCD == "81"){
			echo "�ϳ�����";
		}else if($VIRTUAL_CENTERCD == "88"){
			echo "��������";
		}else if($VIRTUAL_CENTERCD == "27"){
			echo "�ѹ�����";
		}
				}
?>
