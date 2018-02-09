<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");

$JSON_USE=true;
$AJAX_USE=true;

include_once("shopdata.inc.php");

$action_mode=$_REQUEST["action_mode"];

switch($action_mode) {
    case "LOGIN":


        $auto_login		= $_REQUEST["auto_login"];
        $save_id		= $_REQUEST["save_id"];
        $id				= data_convert($_REQUEST["id"]);
        $passwd		= $_REQUEST["passwd"];
        $push_os		= $_REQUEST["push_os"];
        $push_token		= $_REQUEST["push_token"];

        if(strlen($id) == 0 || strlen($passwd) == 0 ) {
            $result_msg = "�α��� ������ �����ϴ�.";
        }
        if ($save_id=="Y") {
			setcookie("smart_id", $id, time()+(86400*60), "/".RootPath."m/", getCookieDomain());
			setcookie("save_id", $save_id, time()+(86400*60), "/".RootPath."m/", getCookieDomain());
        } else {
			setcookie("smart_id", "", 0, "/".RootPath."m/", getCookieDomain());
			setcookie("save_id", "", 0, "/".RootPath."m/", getCookieDomain());
        }

		$resdata=loginprocess($id, $passwd, $push_token, $push_os);
		//response(false, $resdata);
		if($resdata=="NO") {
			response(false, "�α��� �����Դϴ�.");
		} else if($resdata=="OUT") {
			response(false, "���̵� �Ǵ� ��й�ȣ�� Ʋ���ų� Ż���� ȸ���Դϴ�.");
		} else if($resdata=="CONFIRM") {
			response(false, "���θ� ��� ���� �� �α����� �����մϴ�. ��ȭ�� ���ǹٶ��ϴ�.".$_data->info_tel);
		} else if($resdata=="OK") {
			if ($auto_login == "OK") {
			// �ڵ��α��� ������
				setcookie("auto_login", "OK", time()+(86400*60), "/".RootPath."m/", getCookieDomain());
				setcookie("smart_pw", encrypt_md5($passwd), time()+(86400*60), "/".RootPath."m/", getCookieDomain());
			}
			response(true, "�α��� ����");			
		}
        break;
    case "SNS_LOGIN":

		$chUrl=$_ShopInfo->getCheckSnsChurl();
		$sns_id=$_REQUEST["sns_id"];
		$sns_type=$_REQUEST["sns_type"];
		$sns_login=$_REQUEST["sns_login"];				// SNS �α������� üũ��
		$sns_login_id=$sns_type."||".$sns_id;		// SNS����� DB �񱳰�

		$resdata=loginprocess('', '', '', '', $sns_id, $sns_type, $sns_login, $sns_login_id);
		//response(false, $resdata);
		if($resdata=="NO") {
			$alertMsg	= "�α��� �����Դϴ�.";
		} else if($resdata=="SNS_NO") {
			//$alertMsg	= "SNS�� �������� ���� �ʾҽ��ϴ�.";
		} else if($resdata=="OUT") {
			$alertMsg	= "���̵� �Ǵ� ��й�ȣ�� Ʋ���ų� Ż���� ȸ���Դϴ�.";
		} else if($resdata=="CONFIRM") {
			$alertMsg	= "���θ� ��� ���� �� �α����� �����մϴ�. ��ȭ�� ���ǹٶ��ϴ�.".$_data->info_tel;
		} else if($resdata=="OK") {
			$alertMsg	= "";
		}
		$alertMsg	= iconv('euc-kr', 'utf-8', $alertMsg);
		echo "<html><head></head><body onload=\"";
		if($alertMsg) echo "alert('{$alertMsg}');";
		if($resdata=="OK") {
			echo "parent.location.href='".($chUrl?urldecode($chUrl):'/m/')."';";
		} else if($resdata=="SNS_NO") {
			echo "parent.location.href='/m/member_certi.php';";
		} else {
			//echo "history.go(-1);";
			echo "parent.location.href='/m/login.php?chUrl=".urldecode($chUrl)."';";
		}
		echo "\"></body></html>";
		exit;
        break;
    case "LOGOUT":
        $id=$_REQUEST["id"];

		$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$id."' ";
		pmysql_query($sql,get_mdb_conn());


        $md5_id = md5($id);
        setcookie($md5_id."_key", "", 0, "/".RootPath."m/", getCookieDomain());
        setcookie("smart_id", "", 0, "/".RootPath."m/", getCookieDomain());
        break;
}
?>
