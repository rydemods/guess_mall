<?php
/********************************************************************* 
// �� �� ��		: json.php
// ��     ��		: APP ���� JSON ����
// �󼼼���	: APP ���� ���ǰ��� JSON���� �����Ѵ�.
// �� �� ��		: 2016.03.30 - �����
// �� �� ��		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# �⺻���� ���������� �����´�.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	header('Content-Type: application/json');

	$type	= $_REQUEST['type']; // version : ��������, intro : ��Ʈ�� �̹���, end : ���� �˾�

	$returnData	= array();

	if ($type == "version") { // ��������
		
		if($f=@file(DirPath.AuthkeyDir."app")) {
			for($i=0;$i<count($f);$i++) {
				$f[$i]=trim($f[$i]);
				if (strpos($f[$i],"ios:::")===0) $returnData['ios']=substr($f[$i],6);
				elseif (strpos($f[$i],"aos:::")===0) $returnData['aos']=substr($f[$i],6);
			}
		}

	} else if ($type == "intro") { // ��Ʈ��

		$sql = "SELECT * FROM tblappbannerimg WHERE type='F' AND hidden = '1' ORDER BY no desc limit 1";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$returnData['1136'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/".$row->img1;
			$returnData['1334'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/".$row->img2;
			$returnData['2208'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/".$row->img3;
			$returnData['1280'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/".$row->img4;
			$returnData['2560'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/".$row->img5;
		}

	} else if ($type == "end") { // �����˾�

		$sql = "SELECT * FROM tblappbannerimg WHERE type='E' AND hidden = '1' ORDER BY no desc limit 1 ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$returnData[0]['type'] = "1280";
			$returnData[0]['img'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/endpopup/".$row->img1;
			$returnData[0]['link'] = $row->link;
			$returnData[0]['target'] = $row->target;
			$returnData[1]['type'] = "2560";
			$returnData[1]['img'] = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/endpopup/".$row->img1;
			$returnData[1]['link'] = $row->link;
			$returnData[1]['target'] = $row->target;
		}
	}

	if ($returnData) echo json_encode( $returnData );
?>