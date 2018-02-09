<?
$subTitle = "주문 상세정보";
include_once('outline/header_m.php');
include_once('sub_header.inc.php');

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

$ordercode=$_POST["ordercode"];	//로그인한 회원이 조회시
$ordername=$_POST["ordername"]; //비회원 조회시 주문자명
//$ordercodeid=$_POST["ordercode"];	//비회원 조회시 주문번호 6자리
$print=$_POST["print"];	//OK일 경우 프린트
$ordgbn=$_POST['ordgbn'];	// 주민배송 페이지와 취소환불 페이지의 구분

/*if(strlen($_MShopInfo->getMemid())==0&&!($ordercode)) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}
$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);
*/
function getDeligbn($strdeli,$true=true) {
	global $_MShopInfo, $ordercode, $arrdeli;
	if(!is_array($arrdeli)) {
		$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='{$ordercode}' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		$sql.= "GROUP BY deli_gbn ";
		$result=pmysql_query($sql,get_db_conn());
		$arrdeli=array();
		while($row=pmysql_fetch_object($result)) {
			$arrdeli[]=$row->deli_gbn;
		}
		pmysql_free_result($result);
	}

	$res=true;
	for($i=0;$i<count($arrdeli);$i++) {
		if($true) {
			if(!preg_match("/^({$strdeli})$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		} else {
			if(preg_match("/^({$strdeli})$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		}
	}
	return $res;
}

if(ord($ordercode) && strlen($ordercode)!=21) {
	if(strlen($ordercode)!=20){
		alert_go('주문번호를 정확히 입력하시기 바랍니다.','c');
	}
}

$gift_type=explode("|",$_data->gift_type);

$type=$_POST["type"];
$tempkey=$_POST["tempkey"];
$rescode=$_POST["rescode"];

####### 에스크로 구매결정 #######
if ($type=="okescrow" && ord($ordercode) && $rescode=="Y") {
	$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$sql.= "AND (SUBSTR(paymethod,1,1)='Q' OR SUBSTR(paymethod,1,1)='P') ";
	$sql.= "AND deli_gbn='Y' ";
	$result = pmysql_query($sql,get_db_conn());

	echo "<script>alert('구매결정 되었습니다.');self.close();</script>";
	exit;
}

####### 주문취소 (에스크로 포함) #######
if ($type=="cancel" || ($type=="okescrow" && $rescode=="C" && ord($ordercode))) { //매매보호 주문거절시
	$sql = "SELECT price,deli_gbn,reserve,sender_name,paymethod,bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	if($type=="cancel") $sql.= "AND tempkey='{$tempkey}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if (
		(strstr("QP", $row->paymethod[0]) && !strstr("CDEH", $row->deli_gbn) && getDeligbn("C|D|E|H",false))
		|| ($_data->ordercancel==0 && ($row->deli_gbn=="S" || $row->deli_gbn=="N") && getDeligbn("N|S",true)) //tblorderproduct에 deli_gbn이 "S|N"만 있는지 확인한다.
		|| ($_data->ordercancel==2 && $row->deli_gbn=="N" && getDeligbn("N",true)) //tblorderproduct에 deli_gbn이 "N"만 있는지 확인한다.
		|| ($_data->ordercancel=="1" && $row->paymethod=="B" && strlen($row->bank_date)<12 && $row->deli_gbn=="N" && getDeligbn("N",true))
		) {  // 배송기준일 경우 아직 배달을 안했을경우에만 주문 취소, 결제 기준일경우 입금안된건만

			if(strstr("QP", $row->paymethod[0])) $deliok="D";
			else $deliok="C";

			$sql = "UPDATE tblorderinfo SET deli_gbn='{$deliok}' WHERE ordercode='{$ordercode}' ";
			if($type=="cancel") $sql.= "AND tempkey='{$tempkey}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='{$deliok}' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
				pmysql_query($sql,get_db_conn());

				if(empty($ordercode) && strlen($_MShopInfo->getMemid())>0 && $row->reserve>0) {
					$sql = "UPDATE tblmember SET reserve=reserve+{$row->reserve} ";
					$sql.= "WHERE id='".$_MShopInfo->getMemid()."' ";
					pmysql_query($sql,get_db_conn());

					$sql = "INSERT INTO tblreserve(
					id			,
					reserve		,
					reserve_yn	,
					content		,
					orderdata	,
					date		) VALUES (
					'".$_MShopInfo->getMemid()."',
					{$row->reserve},
					'Y',
					'주문 취소건에 대한 적립금 환원',
					'{$ordercode}={$row->price}',
					'".date("YmdHis")."')";
					pmysql_query($sql,get_db_conn());
				}

				/////////////// 주문취소시 관리자에게 메일을 발송
				$maildata=$row->sender_name."고객님이 <font color=blue>".date("Y")."년 ".date("m")."월 ".date("d")."일</font>에 아래와 같이 주문을 취소하셨습니다.<br><br>";
				$maildata.="<li> 취소된 주문의 번호 : $ordercode<br><br>";
				$maildata.="취소된 주문은 관리자메뉴의 주문조회에서 확인하실 수 있습니다.";

				if (ord($_data->shopname)) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($_data->shopname)."?=";
				$header=getMailHeader($mailshopname,$_data->info_email);
				if(ismail($_data->info_email)) {
					sendmail($_data->info_email, $_data->shopname." 주문취소 확인 메일입니다.", $maildata, $header);
				}

				if(ord($_data->okcancel_msg)==0)  $_data->okcancel_msg="정상적으로 주문이 취소되었습니다!";
				if (strstr("Q", $row->paymethod[0]) && strlen($row->bank_date)>=12) $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 환불처리됩니다.";
				if (strstr("P", $row->paymethod[0]) && $row->pay_flag=="0000") $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 카드취소처리됩니다.";

				$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_cancel='Y' ";
				$resultsms= pmysql_query($sqlsms,get_db_conn());
				if($rowsms=pmysql_fetch_object($resultsms)) {
					if(ord($ordercode)) {
						$sms_id=$rowsms->id;
						$sms_authkey=$rowsms->authkey;

						$totellist=$rowsms->admin_tel;
						if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
						if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
						if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
						$fromtel=$rowsms->return_tel;

						$smsmsg=$row->sender_name."님께서 ".substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2)."에 주문하신 주문을 취소하셨습니다.";
						$etcmsg="주문취소 메세지(관리자)";
						if($rowsms->sleep_time1!=$rowsms->sleep_time2) {
							$date="0";
							$time = date("Hi");
							if($rowsms->sleep_time2<"12" && $time<=sprintf("%02d59",$rowsms->sleep_time2)) $time+=2400;
							if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

							if($time<sprintf("%02d00",$rowsms->sleep_time1) || $time>=sprintf("%02d59",$rowsms->sleep_time2)){
								if($time<sprintf("%02d00",$rowsms->sleep_time1)) $day = 0;
								else $day=1;

								$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$rowsms->sleep_time1);
							}
						}
						$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
					}
				}
				pmysql_free_result($resultsms);
				$onload="<script>alert('{$_data->okcancel_msg}');</script>";
				$onload_msg = $_data->okcancel_msg;
			} else {
				$onload="<script>alert('요청하신 작업중 오류가 발생하였습니다.');</script>";
				$onload_msg = "요청하신 작업중 오류가 발생하였습니다.";
			}
		} else if (strstr("QP", $row->paymethod[0]) && strstr("D", $row->deli_gbn)) {
			$onload="<script>alert('최종적으로 상점에서 취소 후 환불처리됩니다.');</script>";
			$onload_msg = "최종적으로 상점에서 취소 후 환불처리됩니다.";
		} else if($_data->ordercancel==0) {
			if(ord($_data->nocancel_msg)==0){
				$onload="<script>alert(\"이미 배송된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
				$onload_msg = "이미 배송된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.";
			}else{
				$onload="<script>alert('$_data->nocancel_msg');</script>";
				$onload_msg = $_data->nocancel_msg;
			}
		} else if($_data->ordercancel==2) {
			if(ord($_data->nocancel_msg)==0){
				$onload="<script>alert(\"발송준비가 완료되어 택배회사에 전달된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
				$onload_msg = "발송준비가 완료되어 택배회사에 전달된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.";
			}else {
				$onload="<script>alert('$_data->nocancel_msg');</script>";
				$onload_msg = $_data->nocancel_msg;
			}
		} else {
			if(ord($_data->nocancel_msg)==0){
				$onload="<script>alert(\"결제대금의 환불/취소는 쇼핑몰로 연락주시기 바랍니다.\");</script>";
				$onload_msg = "결제대금의 환불/취소는 쇼핑몰로 연락주시기 바랍니다.";
			}else{
				$onload="<script>alert('$_data->nocancel_msg');</script>";
				$onload_msg = $_data->nocancel_msg;
			}
		}
	}
}

####### 주문서 삭제 #######
if($type=="delete" && ord($ordercode) && ord($tempkey)) {
	$sql = "SELECT del_gbn FROM tblorderinfo WHERE ordercode='{$ordercode}' AND tempkey='{$tempkey}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$del_gbn = $row->del_gbn;
	if($del_gbn=="N" || $del_gbn==NULL) $okdel="Y";
	else if($del_gbn=="A") $okdel="R";
	else {
		echo "<html><head><title></title></head><body onload=\"alert('해당 주문서는 이미 삭제처리가 되었습니다.');window.close();opener.location.reload();\"></body></html>";exit;
	}

	$sql = "UPDATE tblorderinfo SET del_gbn='{$okdel}' WHERE ordercode='{$ordercode}' AND tempkey='{$tempkey}' ";
	pmysql_query($sql,get_db_conn());
	echo "<html><head><title></title></head><body onload=\"alert('해당 주문서를 삭제처리 하였습니다.');window.close();opener.location.reload();\"></body></html>";exit;
}

##### 주문 여부 확인 #####
$row_count = 0;
if (ord($ordercode) && ord($ordername)) {	//비회원 주문조회
	$curdate = date("Ymd00000",strtotime('-90 day'));
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode ='{$ordercode}' ";
	$sql.= "AND sender_name='{$ordername}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_ord=$row;
		$ordercode=$row->ordercode;
		$gift_price=$row->price-$row->deli_price;

	} else {
		##### 비회원 주문이 없을 경우 #####
	}
	$row_count = pmysql_num_rows($result);
	pmysql_free_result($result);
} else {	//회원 주문조회
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_ord=$row;
		$gift_price=$row->price-$row->deli_price;
	} else {
		##### 회원 주문이 없을 경우 #####
	}
	$row_count = pmysql_num_rows($result);
	pmysql_free_result($result);
}

?>


<script>
<?php	if($onload_msg)	:	?>
	alert('<?=$onload_msg?>');
<?php	endif;	?>
	function order_cancel(tempkey,ordercode) {	//주문취소
		if (confirm("주문을 취소하시겠습니까?")) {
			$("#tempkey").val(tempkey);
			$("#ordercode").val(ordercode);
			$("#type").val("cancel");
			$("#frmInfo").submit();
		}
	}
	function goList(ordgbn){

	}
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">


<form name="frmInfo" id="frmInfo" method="POST">
	<input type="hidden" name="tempkey" id="tempkey">
	<input type="hidden" name="ordercode" id="ordercode">
	<input type="hidden" name="type" id="type">
	<input type="hidden" name="ordercode" id="ordercode" value="<?=$ordercode?>">
	<input type="hidden" name="ordername" id="ordername" value="<?=$ordername?>">
</form>


<main id="content" class="subpage">


<article class="mypage">
<?php
	if(!$ordercode){
		$myp_no = "2";
		if($ordgbn=="C")$myp_no = "3";
		include_once("myp_sub_header.php");
	}
?>

<!-- 주문 상품 정보 -->
<section class="cart_list mypage_tb_list">
    <h3>주문상품</h3>
  <table>
 <caption class="hide">주문상품 리스트 목록</caption>
<colgroup>
<col width="25%" />
<col width="75%" />
</colgroup>
<?php
	if($row_count)	:	//주문내역이 있을 때만
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=pmysql_query($sql,get_db_conn());
		$delicomlist=array();
		while($row=pmysql_fetch_object($result)) {
			$delicomlist[$row->code]=$row;
		}
		pmysql_free_result($result);


		##### 주문 상품 정보 #####
		$sql = "SELECT productcode,productname,opt1_name,opt2_name,tempkey,addcode,quantity,price,reserve, ";
		$sql.= "quantity*price as sumprice, deli_gbn, deli_com, deli_num, deli_date, order_prmsg, package_idx, assemble_idx, assemble_info, ";
		$sql.= "(select pridx from tblproduct where productcode = a.productcode) ";
		$sql.= "FROM tblorderproduct a WHERE ordercode='{$ordercode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		$gift_check="N";
		$taxsaveprname="";
		$etcdata=array();
		$in_reserve=0;
		while($row=pmysql_fetch_object($result)) :
			if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {
				if ($gift_check=="N" && strpos($row->productcode,"GIFT")!==false) $gift_check="Y";
				$etcdata[]=$row;
				continue;
			}
			$gift_tempkey=$row->tempkey;
			$taxsaveprname.=$row->productname.",";

			$optvalue="";
			##### 옵션 정보 #####
			if(preg_match("/^\[OPTG\d{3}\]$/",$row->opt1_name)) {
				$optioncode=$row->opt1_name;
				$row->opt1_name="";
				$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='{$ordercode}' AND productcode='{$row->productcode}' ";
				$sql.= "AND opt_idx='{$optioncode}' ";
				$result2=pmysql_query($sql,get_db_conn());
				if($row2=pmysql_fetch_object($result2)) {
					$optvalue=$row2->opt_name;
				}
				pmysql_free_result($result2);
			}

			$in_reserve+=$row->quantity*$row->reserve;

			$image = getMaxImageForXn($row->productcode);
			$packagestr = "";
			$packageliststr = "";
			//exdebug($row->pridx);
?>
	<tr>
		<td class="thumb"><a href="productdetail.php?pridx=<?=$row->pridx?>"><img src="<?=$image?>" /></a></td>
		<td class="left">
			<span class="name"><a href="productdetail.php?pridx=<?=$row->pridx?>"><?=$row->productname?></a></span>
<!--			<span class="discount">원</span>-->
			<div class="order_price"><span  class="price"><em><?=number_format($row->sumprice)?></em>원</span> <span class="quantity">수량 : <?=$row->quantity?>개</span></div>
		</td>
	</tr>
<?php
			#####	패키지 상품 시작 #####
			if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) 	:
				$assemble_infoall_exp = explode("=",$row->assemble_info);
				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0)	:
?>
	<tr>
		<td colspan="2">
			<section class="mypage_tb_list">
				<h3>패키지 상품 정보</h3>
					<table class="mypage_tb">
						<caption class="hide">쿠폰내역 리스트 목록</caption>
						<colgroup>
							<col width="*" />
							<col width="50%" />
						</colgroup>
						<tr>
							<th scope="col">상품명</th>
							<th scope="col">제한사항</th>
						</tr>
<?php
					$rowspanstr++;
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					$package_name = $package_info_exp[3];	//패키지 명
					$package_price = number_format($package_info_exp[2]);	//패키지 추가금액
					$productname_package_list_exp = explode("",$package_info_exp[1]);
					if(count($productname_package_list_exp)>0 && ord($productname_package_list_exp[0]))	:
						for($k=0; $k<count($productname_package_list_exp); $k++) 	:	//패키지 상품들 반복 시작
?>

						<tr>
							<td><?=$productname_package_list_exp[$k]?></td>
							<td>본 상품당 1개당 수량 1개</td>
						</tr>
<?php
						endfor;
					endif;
?>
					</table>
			</section>
		</td>
	</tr>

<?php

				endif;
			endif;	//패키지 상품 if
		endwhile;
	else	:	//주문 내역이 없을 경우
?>
	<tr>
		<td colspan="2"> 주문 내역이 없습니다.</td>
	</tr>
<?php
	endif;
?>
   </table>
</section>
<!-- //주문 상품 정보 -->



<?php	if($row_count)	:	//주문내역이 있을때만	?>
<div class="btn_wrap">
	<?if($_ord->deli_gbn=="N" &&((strstr("BOQ", $_ord->paymethod[0]) && !$_ord->bank_date) || (strstr("CPMV", $_ord->paymethod[0]) && $_ord->pay_flag!= '0000' && $_ord->pay_admin_proc== 'C'))){?>
	<input type="button" value="주문 전체취소" onclick="order_cancel('<?=$_ord->tempkey?>','<?=$_ord->ordercode?>');" />
	<?}?>
</div>
<!-- 주문 정보 -->
<?php
			if ($_ord->deli_gbn=="C") $ord_state = "주문취소";
			else if ($_ord->deli_gbn=="D") $ord_state = "취소요청";
			else if ($_ord->deli_gbn=="E") $ord_state = "환불대기";
			else if ($_ord->deli_gbn=="X") $ord_state = "발송준비";
			else if ($_ord->deli_gbn=="Y") $ord_state = "발송완료";
			else if ($_ord->deli_gbn=="N") {
				if (strlen($_ord->bank_date)<12 && strstr("BOQ", $_ord->paymethod[0])) $ord_state = "입금확인중";
				else if ($_ord->pay_admin_proc=="C" && $_ord->pay_flag=="0000") $ord_state = "결제취소";
				else if (strlen($_ord->bank_date)>=12 || $_ord->pay_flag=="0000") $ord_state = "발송준비";
				else $ord_state = "결제확인중";
			} else if ($_ord->deli_gbn=="S") {
				$ord_state = "발송준비";
			} else if ($_ord->deli_gbn=="R") {
				$ord_state = "반송처리";
			} else if ($_ord->deli_gbn=="H") {
				$ord_state = "발송완료 [정산보류]";
			}
?>
<section class="mypage_tb_list">
	<h3>주문 정보</h3>
	<table class="my_01">
	<colgroup>
		<col width="20%" />
		<col width="80%" />
	</colgroup>
		<tr>
			<th scope="row">처리 상태</th>
			<td><?=$ord_state?></td>
		</tr>
	</table>
</section>
<!-- //주문 정보 -->


<!-- 주문자 정보 -->
<section class="mypage_tb_list">
	<h3>주문자 정보</h3>
	<table class="my_01">
	<colgroup>
		<col width="20%" />
		<col width="80%" />
	</colgroup>
		<tr>
			<th scope="row">주문하시는 분</th>
			<td><?=$_ord->sender_name?></td>
		</tr>
		<tr>
			<th scope="row">전화번호</th>
			<td><?=$_ord->sender_tel?></td>
		</tr>
		<!--	<tr>
			<th scope="row">핸드폰번호</th>
			<td></td>
		</tr>-->
		<tr>
			<th scope="row">이메일</th>
			<td><?=$_ord->sender_email?></td>
		</tr>
	</table>
</section>
<!-- //주문자 정보 -->


	<form name="frmOrder" method="post" action="indb.php" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="modReceiver">
	<input type="hidden" name="ordno" value="{ordno}">
	<input type="hidden" name="sno_mem" id="sno_mem" value="">

<!-- 배송지 정보 -->
 <section class="mypage_tb_list">
    <h3>배송 정보</h3>
	<table class="my_01">
<colgroup>
<col width="20%" />
<col width="80%" />
</colgroup>
	<tr>
		<th scope="row">받으시는 분</th>
		<td><?=$_ord->receiver_name?></td>
	</tr>
	<tr>
		<th scope="row">주소</th>
		<td><?=str_replace("주소 :","<br>주소 :",$_ord->receiver_addr)?></td>
	</tr>
	<tr>
		<th scope="row">전화번호</th>
		<td><?=$_ord->receiver_tel1?></td>
	</tr>
	<tr>
		<th scope="row">핸드폰번호</th>
		<td><?=$_ord->receiver_tel2?></td>
	</tr>
<?php
		$order_msg=explode("[MEMO]",$_ord->order_msg);
		if(ord($order_msg[0])) 	:
?>
	<tr>
		<th scope="row">고객메모</th>
		<td><?=nl2br($order_msg[0])?></td>
	</tr>
<?php
		endif;
		if(ord($order_msg[2]))	:
?>
	<tr>
		<th scope="row">상점메모</th>
		<td><?=nl2br($order_msg[2])?></td>
	</tr>
<?php
		endif;
		if($_ord->deli_num)	:
?>
	<tr>
		<th scope="row">송장번호</th>
		<td><?=$_ord->deli_num?></td>
	</tr>
<?php	endif;	?>
</table>
  </section>
<!-- //배송지 정보 -->


<input type="hidden" name="settlekind" value="">
<input type="hidden" name="escrowyn" value="">
<!-- 결제정보 -->
<section class="mypage_tb_list">
    <h3>결제 정보</h3>
	<table class="my_01">
	<colgroup>
	<col width="20%" />
	<col width="80%" />
	</colgroup>
	<tr>
		<th scope="row">총주문금액</th>
		<td><span id="paper_goodsprice"><?=number_format($_ord->price)?></span>원</td>
	</tr>
<?php
		for($i=0;$i<count($etcdata);$i++)	:
			$in_reserve+=$etcdata[$j]->reserve;

			##### 쿠폰 #####
			if(preg_match("/^COU\d{8}X$/",$etcdata[$i]->productcode))	:
?>

	<tr>
		<th scope="row">쿠폰할인</th>
			<td><span id="paper_coupon"><?=number_format($etcdata[$i]->price)?></span>원</td>
	</tr>

<?php
			##### 쿠폰 아닌 것들 #####
			elseif(preg_match("/^9999999999\dX$/",$etcdata[$i]->productcode))	:
				if($etcdata[$i]->productcode=="99999999999X")	:
?>
	<tr>
		<th scope="row">결제할인</th>
			<td><span id="paper_memberdc"><?=number_format($etcdata[$i]->price)?></span>원</td>
	</tr>

<?php
				elseif($etcdata[$i]->productcode=="99999999998X")	:
?>
	<tr>
		<th scope="row">결제 수수료</th>
			<td><span id="paper_memberdc"><?=number_format($etcdata[$i]->price)?></span>원</td>
	</tr>
<?php
				elseif($etcdata[$i]->productcode=="99999999990X")	:
?>
	<tr>
		<th scope="row">배송비</th>
		<td>
			<div id="paper_delivery_msg1" ><span id="paper_delivery"><?=number_format($etcdata[$i]->price)?></span>원</div>
			<div id="paper_delivery_msg2" style="float:left;margin:0;" ><?=$etcdata[$i]->productname?></div>
		</td>
	</tr>

<?php
				elseif($etcdata[$i]->productcode=="99999999997X")	:
?>
	<tr>
		<th scope="row">부가세</th>
			<td><span id="paper_memberdc"><?=number_format($etcdata[$i]->price)?></span>원</td>
	</tr>
<?php

				endif;
			endif;
		endfor;
		$dc_price=(int)$_ord->dc_price;
				$salemoney=0;
				$salereserve=0;
				if($dc_price<>0)	:
					if($dc_price>0) $salereserve=$dc_price;
					else $salemoney=-$dc_price;
					if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
						$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
						$sql.= "WHERE a.id='{$_ord->id}' AND b.group_code=a.group_code AND SUBSTR(b.group_code,1,1)!='M' ";
						$result=pmysql_query($sql,get_db_conn());
						if($row=pmysql_fetch_object($result)) {
							$group_name=$row->group_name;
						}
						pmysql_free_result($result);
					}
					if($salemoney){
						$grpmoney = "-".number_format($salemoney);
					}elseif($salereserve){
						$grpmoney = "+".number_format($salereserve);
					}else{
						$grpmoney = "0";
					}
?>
	<tr>
		<th scope="row">그룹적립/할인</th>
			<td><span id="paper_memberdc"></span><?=$grpmoney?>원</td>
	</tr>
<?php
				endif;
				if($_ord->reserve>0)	:
?>
	<tr>
		<th scope="row">적립금 사용</th>
		<td><span id="paper_emoney"><?=$_ord->reserve?></span>원</td>
	</tr>
<?php
				endif;
?>
<!--	<tr>
		<th scope="row">보증보험 수수료</th>
		<td><span id="paper_eggfee"></span>원</td>
	</tr>-->
	<tr>
		<th scope="row">결제금액</th>
		<td><b><span id="paper_settlement"><?=number_format($_ord->price)?></span>원</b></td>
	</tr>
<?php
	if (strstr("BOQ",$_ord->paymethod[0]))	:	//무통장, 가상계좌, 가상계좌 에스크로
		if($_ord->paymethod=="B"){
			$pay_type = "무통장 입금";
		}elseif($_ord->paymethod=="O"){
			$pay_type = "가상 계좌";
		}else{
			$pay_type = "매매 보호 - 가상 계좌";
		}
?>
	<tr>
		<th scope="row">결제방법</th>
		<td><?=$pay_type?></td>
	</tr>
	<tr>
		<th scope="row">입금계좌</th>
		<td><?=$_ord->pay_data?></td>
	</tr>
<!--
	<tr>
		<th scope="row">입금은행</th>
		<td></td>
	</tr>
	<tr>
		<th scope="row">예금주명</th>
		<td></td>
	</tr>
	<tr>
		<th scope="row">입금자명</th>
		<td></td>
	</tr>
-->
<?php
		if (strlen($_ord->bank_date)>=12)	:
?>
	<tr>
		<th scope="row">입금확인</th>
		<td><?=substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")"?></td>
	</tr>
<?php
		elseif(strlen($_ord->bank_date)==9)	:
?>
	<tr>
		<th scope="row">결제방법</th>
		<td>환불</td>
	</tr>

<?php
		endif;
	elseif($_ord->paymethod[0]=="M")	:
		$pay_type = "핸드폰 결제";
		if ($_ord->pay_flag=="0000"){
			if($_ord->pay_admin_proc=="C") $pay_sub_msg = "결제취소 완료";
			else $pay_sub_msg = "결제가 성공적으로 이루어졌습니다.";
		}
		else $pay_sub_msg = "결제가 실패되었습니다.";
	elseif($_ord->paymethod[0]=="P")	:
		$pay_type = "매매보호 - 신용카드";
		if($_ord->pay_flag=="0000") {
			if($_ord->pay_admin_proc=="C") $pay_sub_msg = "카드결제 취소완료";
			else if($_ord->pay_admin_proc=="Y") $pay_sub_msg = "카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no}";
		}
		else $pay_sub_msg = $_ord->pay_data;
	elseif($_ord->paymethod[0]=="C")	:
		$pay_type = "신용카드";
		if($_ord->pay_flag=="0000") {
			if($_ord->pay_admin_proc=="C") $pay_sub_msg = "카드결제 취소완료";
			else if($_ord->pay_admin_proc=="Y") $pay_sub_msg = "카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no}";
		}
		else $pay_sub_msg = "{$_ord->pay_data}";
	elseif($_ord->paymethod[0]=="V")	:
		$pay_type = "실시간 계좌 이체";
		if ($_ord->pay_flag=="0000") {
			if($_ord->pay_admin_proc=="C") $pay_sub_msg = "[환불]";
			else $pay_sub_msg = $_ord->pay_data;
		}
		else $pay_sub_msg = "결제가 실패되었습니다.";
	endif;
	if($pay_type)	:
?>
	<tr>
		<th scope="row">결제방법</th>
		<td><?=$pay_type?></td>
	</tr>
<?php
	endif;
	if($pay_sub_msg)	:
?>
	<tr>
		<th scope="row">승인결과</th>
		<td><?=$pay_sub_msg?></td>
	</tr>

<?php
	endif;
?>
</table>
  </section>
<!-- //결제정보 -->

</form>
<?php
		endif;
		if($ordercode)	:
?>
<div class="btn_wrap"></div>
<?php
	else	:
		if($ordgbn=="S"){
			$ordPage = 'mypage_orderlist.php';
		}else{
			$ordPage = 'mypage_cancellist.php';
		}
?>
<div class="btn_wrap">
	<input type="button" value="목록보기" onclick="location.href='<?=$ordPage?>'" />
</div>
	<?php if(!$ordercode) include("myp_sub_footer.php");?>
<?		endif;	?>
</article>
</main>

<? include_once('outline/footer_m.php'); ?>