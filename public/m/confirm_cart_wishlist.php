<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}
$idx=$_POST['wishidx'];

$wish_idx=implode("','",$idx);

$qry="select * from tblwishlist where wish_idx in('".$wish_idx."')";

$res=pmysql_query($qry);


while($row=pmysql_fetch_array($res)){

	$productcode=$row["productcode"];
	$opts=$row["optidxs"];
	$option1=$row["opt1_idx"];
	$option2=$row["opt2_idx"];


	if (empty($opts))  $opts="0";
	if (empty($option1))  $option1=0;
	if (empty($option2))  $option2=0;


	if(strlen($productcode)==18) {
		list($code_a,$code_b,$code_c,$code_d) = sscanf($productcode,'%3s%3s%3s%3s');

		$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
		$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->group_code=="NO") {
				//���� �з�
				alert_go('�ǸŰ� ����� ��ǰ�Դϴ�.','c');
			}
			else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {
				//�׷�ȸ���� ����
				alert_go('�ش� �з��� ���� ������ �����ϴ�.','c');
			}
		}
		else {
			alert_go('�ش� �з��� �������� �ʽ��ϴ�.','c');
		}
		pmysql_free_result($result);

		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
		$sql.= "WHERE productcode='{$productcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->display!="Y") {
				$errmsg="�ش� ��ǰ�� �ǸŰ� ���� �ʴ� ��ǰ�Դϴ�.\\n";
			}
			if($row->group_check!="N") {
				if(strlen($_ShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='{$productcode}' ";
					$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
					$resultgc=pmysql_query($sqlgc,get_db_conn());
					if($rowgc=@pmysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							$errmsg="�ش� ��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.\\n";
						}
						@pmysql_free_result($resultgc);
					}
					else {
						$errmsg="�ش� ��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.\\n";
					}
				}
				else {
					$errmsg="�ش� ��ǰ�� ȸ�� ���� ��ǰ�Դϴ�.\\n";
				}
			}
			if(ord($errmsg)==0) {
				if(strlen(dickerview($row->etctype,0,1))>0) {
					$errmsg="�ش� ��ǰ�� �ǸŰ� ���� �ʽ��ϴ�.\\n";
				}
			}
			if(empty($option1) && ord($row->option1))  $option1=1;
			if(empty($option2) && ord($row->option2))  $option2=1;
		}
		else {
			$errmsg="�ش� ��ǰ�� �������� �ʽ��ϴ�.\\n";
		}
		pmysql_free_result($result);

		if(ord($errmsg)) {
			msg($errmsg,-1);
			exit;
		}

		$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND productcode='{$productcode}' ";
		$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
		//$sql.= "AND assemble_idx = '{$assemble_idx}' ";
		//$sql.= "AND package_idx = '{$package_idx}' ";

		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);

		if ($row) {
			msg('�̹� ��ٱ��Ͽ� ��ǰ�� ����ֽ��ϴ�.',-1);
			exit;

		}
		else {
			$vdate = date("YmdHis");
			$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);

			if($row->cnt>=200) {
				echo "<html></head><body onload=\"alert('��ٱ��Ͽ��� �� 200�������� ������ �ֽ��ϴ�.');\"></body></html>";
				exit;
			}
			else {
				$sql = "INSERT INTO tblbasket(
				tempkey			,
				productcode		,
				opt1_idx		,
				opt2_idx		,
				optidxs			,
				quantity		,
				package_idx		,
				assemble_idx	,
				assemble_list	,
				date,id) VALUES (
				'".$_ShopInfo->getTempkey()."',
				'{$productcode}',
				'{$option1}',
				'{$option2}',
				'{$opts}',
				'1',
				'0',
				'0',
				'',
				'{$vdate}','".$_ShopInfo->getMemid()."')";

				pmysql_query($sql,get_db_conn());

			}
		}
	}
}


msg('�����Ͻ� ��ǰ�� ��ٱ��Ͽ� ��ҽ��ϴ�.','basket.php');
?>
