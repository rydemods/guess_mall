<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");
/**
* 회원등급 table
* 
* @return $groupArray;
*/
function groupCheck() {
	$sql = "SELECT group_code, group_name, group_usemoney, group_level, group_no FROM tblmembergroup ORDER BY group_level ASC ";
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_array( $result ) ){
		$groupArray[$row[group_code]] = $row;
	}
	
	return $groupArray;
}
/**
* 등급업 쿠폰발급
* @param String $coupon_code
* @param String $memberId
* 
* @return
*/
function giveCouponGradeUp($coupon_code, $memberId){
	$thisYear = date('Y');
	$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code='{$coupon_code}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$giveCouponDate = date("YmdHis");
		if($row->date_start>0) {
			$date_start=$row->date_start;
			$date_end=$row->date_end;
		} else {
			$date_start = substr($giveCouponDate,0,10);
			$date_end = date("Ymd23",strtotime("+".abs($row->date_start)." day"));
		}
		$sql = "INSERT INTO tblcouponissue (coupon_code, id, date_start, date_end, date) VALUES ('{$coupon_code}', '".$memberId."', '{$date_start}', '{$date_end}', '{$giveCouponDate}')";
		//exdebug($sql);
		pmysql_query($sql,get_db_conn());
		
		$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 WHERE coupon_code = '{$coupon_code}'";
		//exdebug($sql);
		pmysql_query($sql,get_db_conn());	
	}
	pmysql_free_result($result);
}
/**
* 실버등급이상 조건에 포함된 회원
* 
* @return Array $memberArray
*/
function groupUpMember(){
	$sql = "WITH memberSearch AS ( ";
	$sql.= " SELECT id, COUNT(id) as ordercnt, sum(price) as sumprice  FROM tblorderinfo ";
	$sql.= " WHERE deli_gbn = 'Y' ";
	$sql.= " GROUP BY id ) ";
	$sql.= "SELECT a.*, b.group_code ";
	$sql.= "FROM memberSearch a LEFT JOIN tblmember b ON a.id=b.id ";
	$sql.= "WHERE a.sumprice >= 300000 ";
	$sql.= "AND a.ordercnt >= 1 ";
	$sql.= "ORDER BY a.ordercnt DESC, a.sumprice DESC ";
	
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_array( $result ) ){
		$memberArray[] = $row;
	}
	pmysql_free_result($result);
	return $memberArray;
}

/**
* 등급조건 확인
* @param Array $member
* @param &Array $groupArray
* 
* @return String $groupCode
*/
function checkLevel( $member, &$groupArray ){
	$groupCode = "";
	// 등급별 필요구매 횟수
	$groupOrderCnt = array("0004"=>"3","0003"=>"2","0002"=>"1");
	foreach ( $groupArray as $groupVal ) {
		if ( ( (int) $groupVal[group_code] <= (int) $member[group_code] ) && ord($member[group_code]) ) {
			$groupCode = "";
		} else if ( ( $member[sumprice] >= $groupVal[group_usemoney] ) && ( $member[ordercnt] >= $groupOrderCnt[$groupVal[group_code]] ) ){
			$groupCode = $groupVal[group_code];
		}
	}
	return $groupCode;
}

/**
* 등급별 적립금
* @param String $group_code
* @param String $memberId
* 
* @return Int reserveData
*/
function groupUpReserve( $group_code, $memberId ){
	$reserveData = 0;
	// 적립금
	$upReserve = array("0004"=>10000,"0003"=>7000,"0002"=>5000);
	foreach ( $upReserve as $groupKey=>$reserveVal ) {
		if( $group_code == $groupKey ){
			$sql = "UPDATE tblmember SET reserve = ( reserve + ".$reserveVal." ) WHERE id='".$memberId."' ";
			//exdebug( $sql );
			pmysql_query( $sql, get_db_conn() );
			$logSql = "INSERT INTO tblreserve ( id, reserve, reserve_yn, content, date ) ";
			$logSql.= "VALUES ( '".$memberId."', ".$reserveVal.", 'Y', '등급 업그레이드 축하 적립금 입니다. 감사합니다.', '".date("YmdHis")."' ) ";
			//exdebug( $logSql );
			pmysql_query( $logSql, get_db_conn() );
			$reserveData = $reserveVal;
		}
	}
	
	return $reserveData;
}

//등급별 쿠폰 종류 다이아몬드, 골드, 실버
$groupUpCouponArray = array("0004"=>"25150314","0003"=>"34145714","0002"=>"31454144");
$groupArray = groupCheck();
$memberArray = groupUpMember();
$upMemberArray = array();
$successType = true;

if( count($memberArray) > 0 ){
	BeginTrans();
	for( $i=0; $i < count($memberArray); $i++ ){
		$reserveData = 0;
		$couponData = "";
		//exdebug($i." -------------------------------------------------------------------------------------------------------------------------------");
		$checkLevel = checkLevel( $memberArray[$i], $groupArray);
		if( $checkLevel != ""  ){
			$memberUpdateGroupSql = "UPDATE tblmember SET group_code = '".$checkLevel."' WHERE id = '".$memberArray[$i]["id"]."' ";
			//exdebug( $memberUpdateGroupSql );
			pmysql_query( $memberUpdateGroupSql, get_db_conn() );
			$reserveData = groupUpReserve( $checkLevel, $memberArray[$i]["id"] );
			foreach($groupUpCouponArray as $couponKey=>$couponVal){
				if( $checkLevel == $couponKey ) {
					giveCouponGradeUp( $couponVal , $memberArray[$i]["id"] );
					$couponData = $couponVal;
				}
			}
			//등급을 올리는 대상
			$upMemberArray[$i]["id"] = $memberArray[$i]["id"];
			$upMemberArray[$i]["group_code"] = $checkLevel;
			$upMemberArray[$i]["group_name"] = $groupArray[$checkLevel]["group_name"];
			$upMemberArray[$i]["reserve"] = $reserveData;
			$upMemberArray[$i]["coupon"] = $couponData;
		}
		//exdebug($i." //-------------------------------------------------------------------------------------------------------------------------------");
	}
	CommitTrans();
	if(pmysql_error()){
		$successType = false;
	}
}
// 성공했을때 로그 남기기
if( $successType && $upMemberArray ) {
	$f = fopen(DirPath."batch/oryanyGroupLog_".date("Ymd").".txt","a+");
	foreach( $upMemberArray as $successVal ) {
		exdebug($successVal);
		fwrite($f, "\r\n".$successVal['id']." 회원 : ".date("Y-m-d H:i:s")."에 등급 [".$successVal['group_name']."]로 변경,\r\n 쿠폰 ( 코드 [[".$successVal['coupon']."]] ) 발급 완료 \r\n 적립금 ".$successVal['reserve']." 포인트 발급 완료 \r\n\r\n");
		fwrite($f, "-----------------------------------------------------------------------------------------\r\n");
	}
	fclose($f);
	chmod("oryanyGroupLog_".date("Ymd").".txt",0777);
	exit;
	//exdebug($upMemberArray);
}
?>