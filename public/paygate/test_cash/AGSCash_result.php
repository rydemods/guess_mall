<?php

/*******************************************************************************
* AGSCash_ing.php ���κ��� �Ѱܹ��� ������ 
********************************************************************************/

$Retailer_id = trim($_POST["Retailer_id"]);		//�������̵�

$Ord_No = trim($_POST["Ord_No"]);				//�ֹ���ȣ

$Amtcash = trim($_POST["Amtcash"]);				//�ŷ��ݾ�

$deal_won = trim($_POST["deal_won"]);			//���ް���

$Dealno = trim( $_POST["Dealno"] );			    //�ŷ�������ȣ

$Cust_no = trim($_POST["Cust_no"]);				//ȸ�����̵�

$Cat_id = trim($_POST["Cat_id"]);				//�ܸ����ȣ

$Alert_msg1 = trim($_POST["Alert_msg1"]);		//�˸��޼���1

$Alert_msg2 = trim($_POST["Alert_msg2"]);		//�˸��޼���2

$rResMsg = trim($_POST["rResMsg"]);		        //�����޽���

$Adm_no = trim($_POST["Adm_no"]);				//���ι�ȣ

$Amttex = trim($_POST["Amttex"]);				//�ΰ���ġ��

$Amtadd = trim($_POST["Amtadd"]);				//�����

$prod_nm = trim($_POST["prod_nm"]);				//��ǰ��

$prod_set = trim($_POST["prod_set"]);			//��ǰ����

$deal_won = trim($_POST["deal_won"]);	        //�ŷ��ݾ�

$Gubun_cd = trim($_POST["Gubun_cd"]);			//�ŷ��ڱ���    01.�ҵ������ 02.����������

$Confirm_no = trim($_POST["Confirm_no"]);		//�ź�Ȯ�ι�ȣ

$Pay_kind = trim($_POST["Pay_kind"]);			//��������

$Success = trim($_POST["Success"]);				//�������� y,n ���� ǥ��

$Pay_type = trim($_POST["Pay_type"]);	        //������� 1.�������ӱ� 2.������ü

$Org_adm_no = trim($_POST["Org_adm_no"]);	    //��ҽ� ���ι�ȣ

$Email = trim($_POST["Email"]);					//�̸����ּ�

/***************************************************************************************************
* ��ǰ�� ������(��ǰ��, ��ǰ����, �ֹ��ڸ��)�� �������� ó���� �ؾ���
****************************************************************************************************/
?>
<html>
<head>
<title>�ô�����Ʈ</title>
<style type="text/css">
<!--
body { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
-->
</style>
<script language=javascript> // �������� y�ϰ�쿣 �������˾� ���
<!--
function show_receipt() // ������ ��� 
	{
		if("<?=$Success?>"== "y"){
	     	
   		   document.cash_pay.submit();
			}
		else
		{
			alert("�ش��ϴ� ���������� �����ϴ�");
		}
	}
	//������ ��³�
-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0 >
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=400 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>���ݿ�����ó�����</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=400 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright>�������� : </td>
						<td class=clsleft>
<?php
	if($Pay_kind == "cash-appr")
	{
		echo "���ݿ����������û";
	}	
	else if($Pay_kind == "cash-cncl")
	{
		echo "���ݿ�������ҿ�û";
	}
	else if($Pay_kind == "cash-appr-temp")
	{
		echo "���ݿ������ӽ�����(����)��û";
	}
	else if($Pay_kind == "cash-cncl-temp")
	{
		echo "���ݿ������ӽ�����(���)��û";
	}
	else if($Pay_kind == "non-cash-appr")
	{
		echo "�̵�ϻ������ݿ����������û";
	}
	else if($Pay_kind == "non-cash-cncl")
	{
		echo "�̵�ϻ������ݿ�������ҿ�û";
	}
?></td>
					<tr>
						<td class=clsright>�ŷ��ڱ��� : </td>
						<td class=clsleft>
<?php
	if($Gubun_cd == "01")
	{
		echo "�ҵ������";
	}	
	else if($Gubun_cd == "02")
	{
		echo "���������������";
	}
?></td>
					</tr>
					<tr>
						<td class=clsright>������� : </td>
						<td class=clsleft>
<?php
	if($Pay_type == "1")
	{
		echo "�������Ա�";
	}	
	else if($Pay_type == "2")
	{
		echo "������ü";
	}
?></td>
					<tr>
						<td class=clsright>ȸ�����̵� : </td>
						<td class=clsleft><?=$Cust_no?></td>
					</tr>
					<tr>
						<td class=clsright>���ֹ���ȣ : </td>
						<td class=clsleft><?=$Ord_No?></td>
					</tr>
					<tr>
						<td class=clsright>�ֹ���ȣ : </td>
						<td class=clsleft><?=$Dealno?></td>
					</tr>
					<tr>
						<td class=clsright>��ǰ�� : </td>
						<td class=clsleft><?=$prod_nm?></td>
					</tr>
					<tr>
						<td class=clsright>��ǰ���� : </td>
						<td class=clsleft><?=$prod_set?></td>
					</tr>
					</tr>
						<tr>
						<td class=clsright>�����ݾ� : </td>
						<td class=clsleft><?=$Amtcash?></td>
					</tr>
					<tr>
						<td class=clsright>���ް��� : </td>
						<td class=clsleft><?=$deal_won?></td>
					</tr>
					<tr>
						<td class=clsright>�ΰ��� : </td>
						<td class=clsleft><?=$Amttex?></td>
					</tr>
					<tr>
						<td class=clsright>����� : </td>
						<td class=clsleft><?=$Amtadd?></td>
					</tr>
					<tr>
						<td class=clsright>�̸��� : </td>
						<td class=clsleft><?=$Email?></td>
					</tr>
					<tr>
						<td class=clsright>�ź�Ȯ�ι�ȣ : </td>
						<td class=clsleft><?=$Confirm_no?></td>
					</tr>
					<tr>
						<td class=clsright>���ι�ȣ : </td>
						<td class=clsleft><?=$Adm_no?></td>
					</tr>
					<tr>
						<td class=clsright>�����ι�ȣ : </td>
						<td class=clsleft><?=$Org_adm_no?></td>
					
					<tr>
						<td class=clsright>�������� : </td>
						<td class=clsleft><?=$Success?></td>
					</tr>
					<tr>
						<td class=clsright>����޽��� : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
					<tr>
						<td class=clsright>�˸��޽���1 : </td>
						<td class=clsleft><?=$Alert_msg1?></td>
					</tr>
					<tr>
						<td class=clsright>�˸��޽���2 : </td>
						<td class=clsleft><?=$Alert_msg2?></td>
					</tr>
					    <tr>
						<td class=clsright>������ :</td>
						<td class=clsleft><? if ($Pay_kind == "cash-appr" || $Pay_kind == "cash-cncl" || $Pay_kind == "non-cash-appr" || $Pay_kind == "non-cash-cncl")	{ ?>	<input type="button" value="������" onclick="javascript:show_receipt();"> <?}?></td>
					</tr>
				    <tr>
						<td colspan=2>&nbsp;</td>
					</tr>
							
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
<!---------------------������ ����� ���� �Ѱ��� ������------------------------->
<form name=cash_pay method=post action=AGSCash_receipt.php 	target="_blank">
<input type=hidden name=Retailer_id value="<?=$Retailer_id?>">
<input type=hidden name=Ord_No value="<?=$Ord_No?>">
<input type=hidden name=Cust_no value="<?=$Cust_no?>">
<input type=hidden name=Adm_no value="<?=$Adm_no?>">
<input type=hidden name=Success value="<?=$Success?>">
<input type=hidden name=Resp_msq value="<?=$rResMsg?>">
<input type=hidden name=Alert_msg1 value="<?=$Alert_msg1?>">
<input type=hidden name=Alert_msg2 value="<?=$Alert_msg2?>">
<input type=hidden name=deal_won value="<?=$deal_won?>">
<input type=hidden name=Amttex value="<?=$Amttex?>">
<input type=hidden name=Amtadd value="<?=$Amtadd?>">
<input type=hidden name=Amtcash value="<?=$Amtcash?>">
<input type=hidden name=prod_nm value="<?=$prod_nm?>">
<input type=hidden name=prod_set value="<?=$prod_set?>">
<input type=hidden name=Gubun_cd value="<?=$Gubun_cd?>">
<input type=hidden name=Pay_kind value="<?=$Pay_kind?>">
<input type=hidden name=Confirm_no value="<?=$Confirm_no?>">
<input type=hidden name=Org_adm_no value="<?=$Org_adm_no?>">
</body>
</html>
