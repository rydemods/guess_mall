<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

//exdebug($_POST);

$ordercodes = "";
$err_ordercodes = "";
//exdebug($idxs);

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

if($_POST["idx"]) {
    $idxs = rtrim($_POST["idx"],',');
    //$sql = "Select ordercode From tblorderproduct Where idx in (".$idxs.") Group by ordercode";
	$sql = "Select ordercode, idx From tblorderproduct Where idx in (".$idxs.")";

} elseif($_POST["ordercodes"]) {
    $ordercodes = str_replace(",", "','", rtrim($_POST["ordercodes"],','));
    $sql = "select ordercode from tblorderinfo where ordercode in ('".$ordercodes."')";
}
$ret = pmysql_query($sql,get_db_conn());
#exdebug($sql);
#exit;

// 해당 주문건 입금처리
if($_POST[mode] == "1") {

    $tax_type = $_shopdata->tax_type;

    while($roword = pmysql_fetch_object($ret)) {

        $sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$roword->ordercode}'";
        $result = pmysql_query($sql,get_db_conn());
        //exdebug($sql);
        $_ord = pmysql_fetch_object($result);
        pmysql_free_result($result);

        if($_ord->paymethod=="B" && $tax_type=="Y") {

            $sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$_ord->ordercode}' AND type='N' ";
            $result = pmysql_query($sql,get_db_conn());
            //exdebug($sql);
            $row = pmysql_fetch_object($result);
            pmysql_free_result($result);
            if($row->cnt > 0) {
                $flag="Y";
                include($Dir."lib/taxsave.inc.php");
            }
        }

        pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$_ord->ordercode}' ",get_db_conn());
        //exdebug("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$_ord->ordercode}' ");
        // 상태변경 호출
        orderStepUpdate($exe_id, $_ord->ordercode, 1);
        // 재고처리 호출(입금완료(결제완료) 단계에서 재고 차감)
        order_quantity($_ord->ordercode);

        $isupdate=true;

        if(ord($_ord->sender_email)) {
            //exdebug($_shopdata->shopname);
            //exdebug($shopurl);
            //exdebug($_shopdata->design_mail);
            //exdebug($_shopdata->info_email);
            //exdebug($_ord->sender_email);
            //exdebug($_ord->ordercode);
            SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $_ord->ordercode);
        }

        $sql = "SELECT * FROM tblsmsinfo WHERE mem_bankok='Y' ";
        $result = pmysql_query($sql,get_db_conn());
        //exdebug($sql);
        if($rowsms = pmysql_fetch_object($result)) {
            $sms_id = $rowsms->id;
            $sms_authkey = $rowsms->authkey;

            $bankprice = $_ord->price - $_ord->dc_price - $_ord->reserve + $_ord->deli_price;
            $bankname = $_ord->sender_name;
            $msg_mem_bankok = $rowsms->msg_mem_bankok;
            if(ord($msg_mem_bankok)==0) $msg_mem_bankok = "[".strip_tags($_shopdata->shopname)."] [DATE]의 주문이 입금확인 되었습니다. 빨리 발송해 드리겠습니다.";
            $patten = array("[DATE]","[NAME]","[PRICE]");
            $replace = array(substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2),$bankname,$bankprice);

            $msg_mem_bankok = str_replace($patten,$replace,$msg_mem_bankok);
            $msg_mem_bankok = addslashes($msg_mem_bankok);

            $fromtel = $rowsms->return_tel;
            $date=0;
            $etcmsg="입금확인메세지(회원)";
            $temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
        }

        pmysql_free_result($result);

        $ordercodes .=  $_ord->ordercode.",";

        if( $_ord->ordercode ){ //2016-10-06 libe90 싱크커머스 주문전송
            $ordercode = $_ord->ordercode;

            $Sync = new Sync();
            $arrayDatax=array('ordercode'=>$ordercode);

            $srtn=$Sync->OrderInsert($arrayDatax);

            sendErporder($ordercode);
        }
    }
    pmysql_free_result($ret);

	$log_content = "## 주문내역 입금처리 ## - 주문번호 : ".$ordercodes;
    //exdebug($log_content);
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	echo "<script>alert('선택하신 주문내역을 입금처리하였습니다.'); parent.location.reload();</script>";
	exit;

}else if($_POST[mode] == "2") {
// 해당 주문건 배송준비중 처리

    while($roword = pmysql_fetch_object($ret)) {

        $sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$roword->ordercode}'";
        $result = pmysql_query($sql,get_db_conn());
        //exdebug($sql);
        $_ord = pmysql_fetch_object($result);
        pmysql_free_result($result);

        //if($_ord->deli_gbn=="N") {

            // 입금확인을 거치지 않고, 바로 배송준비중으로 넘어올 경우 bank_date 값이 없다.2016-03-21 jhjeong
            if($_ord->bank_date == "") {
                pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$_ord->ordercode}' ",get_db_conn());
            }

            $sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='{$_ord->ordercode}' AND deli_gbn='N' ";
            if(pmysql_query($sql,get_db_conn())) {
                $sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='{$_ord->ordercode}' ";
                //$sql.= "AND idx = {$idx} ";
				$sql.= "AND idx in ($roword->idx) ";
                $sql.= "AND deli_gbn='N' ";
                pmysql_query($sql,get_db_conn());
                //exdebug($sql);

                // 상태변경 호출
                 orderProductStepUpdate($exe_id, $_ord->ordercode, $roword->idx, 2);
            }
            $ordercodes .=  $_ord->ordercode.",";
        //}
    }
    pmysql_free_result($ret);

	$log_content = "## 주문내역 배송준비중 처리 ## - 주문번호 : ".$ordercodes;
    //exdebug($log_content);
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	echo "<script>alert('선택하신 주문내역을 배송준비중 처리하였습니다.'); parent.location.reload();</script>";
	exit;

}else if($_POST[mode] == "3") {
// 해당 주문건의 상품 배송중 처리

    while($roword = pmysql_fetch_object($ret)) {

        $sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$roword->ordercode}'";
        $result = pmysql_query($sql,get_db_conn());
        $_ord = pmysql_fetch_object($result);
        pmysql_free_result($result);

        if(strstr("NXS",$_ord->deli_gbn)) {

            $sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
            $sql.= "WHERE ordercode='{$_ord->ordercode}' ";
            if(pmysql_query($sql,get_db_conn())) {
                $sql = "UPDATE  tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
                $sql.= "WHERE   ordercode='{$_ord->ordercode}' ";
                $sql.= "AND     idx in ($idxs) ";
                $sql.= "AND deli_gbn!='Y' ";
                pmysql_query($sql,get_db_conn());
                //exdebug($sql);

                // 상태변경 호출
                orderProductStepUpdate($exe_id, $_ord->ordercode, $idxs, 3);
            }
            $ordercodes .=  $_ord->ordercode.",";

        } elseif(!strstr("NXS",$_ord->deli_gbn)) {

            $err_ordercodes .=  $_ord->ordercode.",";
        }
    }
    pmysql_free_result($ret);

    $log_content = "## 주문내역 배송중 처리 ## - 주문번호 : ".$ordercodes." / ".$idxs;
    //exdebug($log_content);
    ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

    if($err_ordercodes != "") {
        $log_content = "## 주문내역 배송중 처리중 오류건들 ## - 주문번호 : ".$err_ordercodes." / ".$idxs;
        //exdebug($log_content);
        ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
        echo "<script>alert('$err_ordercodes 이미 취소되거나 발송된 물품이 포함되어 있습니다.');  parent.location.reload();</script>";
    } else {
        echo "<script>alert('선택하신 주문내역을 배송중 처리하였습니다.'); parent.location.reload(); </script>";
    }
    exit;

}


/*
select * from tblorderinfo a where a.ordercode = '2016010122390394059A' ;
select * from tblorderinfo a where a.ordercode = '2016020114463992315A' ;

update tblorderinfo set oi_step1 = 0, bank_date = '' where ordercode = '2016020114463992315A';
update tblorderinfo set oi_step1 = 0, bank_date = '' where ordercode = '2016020411250549022A';

update tblorderinfo set oi_step1 = 0, bank_date = '' where ordercode = '2016012817592858501A';

update tblorderinfo set deli_gbn='S', sender_email = 'ikazeus@naver.com', sender_tel = '010-4121-2734', oi_step1 = 2 where ordercode = '2016010608514419250A';
update tblorderproduct set deli_gbn = 'N', deli_com = '', deli_num = '', deli_date = '', op_step = 1 where ordercode = '2016020411250549022A';

select * from tblorderinfo a where a.ordercode = '2016012817592858501A' ;
select * from tblorderproduct where ordercode = '2016020411250549022A';

update tblorderproduct set op_step = 0 where ordercode = '2016020114463992315A';

Select ordercode From tblorderproduct Where idx in (1176,1165,1164) Group by ordercode


select * from tblorderproduct where ordercode = '2016010120185008470A'

update tblorderinfo set redelivery_type = 'G', redelivery_reason = '교환테스트' where ordercode = '2016010120185008470A';
update tblorderproduct set redelivery_type = 'G', redelivery_reason = '교환테스트' where ordercode = '2016010120185008470A';


update tblorderproduct set deli_gbn = 'N', op_step = 1 where ordercode = '2016012817592858501A' and idx in (1176,1165,1164);

update tblorderinfo set deli_gbn = 'N', deli_date = '' where ordercode = '2016020411250549022A';
update tblorderinfo set deli_gbn = 'N', deli_date = '' where ordercode = '2016020114463992315A';

select * from tblproduct where productcode = '1447637447';

SELECT a.vender, v.com_name, a.ordercode, a.productcode, a.productname, a.selfcode, a.opt1_name, a.opt2_name, a.quantity, a.price, a.deli_com, a.deli_num, a.deli_date,
	a.deli_price, a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2
FROM tblorderproduct a
join tblorderinfo b on a.ordercode = b.ordercode
join tblvenderinfo v on a.vender = v.vender
WHERE 1=1 AND a.option_type = 0
AND upper(a.selfcode) like upper('%br%')
ORDER BY a.ordercode DESC, a.vender DESC LIMIT 20 OFFSET 0



SELECT a.vender, v.com_name, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, a.deli_com, a.deli_num, a.deli_date, a.deli_price, a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2 FROM tblorderproduct a join tblorderinfo b on a.ordercode = b.ordercode join tblvenderinfo v on a.vender = v.vender
WHERE 1=1 AND a.option_type = 0
--AND ( a.redelivery_type = 'G' And a.op_step = 41 )
AND ( (a.redelivery_type = 'G' And a.op_step = 44) )
ORDER BY a.ordercode DESC, a.vender DESC LIMIT 20 OFFSET 0
*/
?>
