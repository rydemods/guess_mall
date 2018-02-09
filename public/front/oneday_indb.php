<?php 

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/member.class.php");

##### 회원정보
$member_info= new  MEMBER();
$member_info = $member_info->getMemberInfo(); //회원정보

$mode = $_POST['mode'];				//mode
$param = $_POST['param'];		//적용 날짜(알림예약)
$productcode = $_POST['productcode'];		
$id = $member_info['id'];
$userip = $_SERVER['REMOTE_ADDR'];

##### 오늘의 특가 정보
$sql_oneday = "SELECT * FROM tblproductoneday ";
$sql_oneday.= "WHERE applydate = '{$param}' ";
$sql_oneday.= "ORDER BY modifydate ";
$sql_oneday.= "LIMIT 1 ";
$res_oneday = pmysql_query($sql_oneday);
$_odata = pmysql_fetch_array($res_oneday);

##### 리턴값
#### RS : 알림예약 성공 
#### RF1 : ID 없음
#### RF2 : 이미 예약이 등록되어 있음
#### RF3 : 등록실패


#### ES : 앵콜요청 성공
#### EF1 : productcode없음
#### EF2 : 이미 앵콜 요청을 한 ip임
#### EF3 : 요청 실패

$rtn_msg = "";
switch($mode){
	case "reserve":
		if($id){
			list($mobile) = pmysql_fetch(pmysql_query("SELECT mobile FROM tblmember where id='{$id}'"));
			
			$sql_reserve = "SELECT * from tblproductonedaydetail ";
			$sql_reserve.= "WHERE id='{$id}' AND applydate='{$param}' ";
			$res_reserve = pmysql_query($sql_reserve);
			$rownum_reserve = pmysql_num_rows($res_reserve);
			if($rownum_reserve){
				$rtn_msg = "RF2";
			}else{
				$sql_reserve_insert = "INSERT INTO tblproductonedaydetail(
										id, 
										mobile, 
										type, 
										applydate, 
										productcode,
										ip
										)VALUES (
										'{$id}', 
										'{$mobile}', 
										'R', 
										'{$param}', 
										'".$_odata['productcode']."',
										'{$userip}'
										)RETURNING idx";
				if($row = pmysql_fetch(pmysql_query($sql_reserve_insert))){
					$rtn_msg = "RS";
				}else{
					$rtn_msg = "RF3";
				}
			}
			
		}else{
			$rtn_msg = "RF1";
		}
		break;
	
	case "encore":

		if($param){
			$sql_encore = "SELECT * from tblproductonedaydetail ";
			$sql_encore.= "WHERE ip='{$userip}' AND productcode='".$_odata['productcode']."' ";
			$res_encore = pmysql_query($sql_encore);
			$rownum_encore = pmysql_num_rows($res_encore);
			if($rownum_encore){
				$rtn_msg = "EF2";
			}else{
				$sql_encore_insert = "INSERT INTO tblproductonedaydetail(
										id, 
										mobile, 
										type, 
										applydate, 
										productcode,
										ip
										)VALUES (
										'{$id}', 
										'{$mobile}', 
										'E', 
										'{$param}',
										'".$_odata['productcode']."', 
										'{$userip}'
										)RETURNING idx";
				if($row = pmysql_fetch(pmysql_query($sql_encore_insert))){
					$rtn_msg = "ES";
				}else{
					$rtn_msg = "EF3";
				}
			}
		}else{
			$rtn_msg = "EF1";
		}
		break;
	
	default:
		break;
}

switch($rtn_msg){

	case "RS" :
		$rtn_msg_str = "알림예약을 등록하였습니다.";
		break;
	case "RF1" :
		$rtn_msg_str = "알림예약은 로그인을 해야 합니다.";
		break;
	case "RF2" :
		$rtn_msg_str = "이미 예약이 등록되어 있습니다.";
		break;
	case "RF3" :
		$rtn_msg_str = "알림예약 등록중 에러가 발생하였습니다.\n(관리자에게 문의 바랍니다.)";
		break;
	case "ES" :
		$rtn_msg_str = "앵콜요청을 하였습니다.";
		break;
	case "EF1" :
		$rtn_msg_str = "상품코드가 없습니다.";
		break;
	case "EF2" :
		$rtn_msg_str = "이미 앵콜요청이 되어 있습니다.";
		break;
	case "EF3" :
		$rtn_msg_str = "앵콜요청중 에러가 발생하였습니다.\n(관리자에게 문의 바랍니다.)";
		break;
}

?>
{"msg1":"<?=$rtn_msg_str?>","msg2":"<?=$rtn_msg?>"}