<?php
/********************************************************************* 
// 파 일 명		: member_join.php 
// 설     명		: 로그인,로그아웃,회원탈퇴 총괄
// 상세설명	: 로그인,로그아웃,회원탈퇴 부분을 처리
// 작 성 자		: hspark
// 수 정 자		: 2015.10.29 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
//로그인,로그아웃,회원탈퇴 총괄
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

# SNS 관련 세션값 초기화
$_ShopInfo->setCheckSns("");
$_ShopInfo->setCheckSnsLogin("");
$_ShopInfo->Save();

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

$type=$_REQUEST["type"];	//login,logout,exit
$id=$_POST["id"];
$vid=$_GET["vid"];
if($vid) $id=$vid;
$passwd=$_POST["passwd"];

$sns_id=$_POST["sns_id"];
$sns_type=$_POST["sns_type"];
$sns_login=$_POST["sns_login"];				// SNS 로그인인지 체크값
$sns_login_id=$sns_type."||".$sns_id;		// SNS결과와 DB 비교값

$_SESSION[sns][sns_id]		= $sns_id;
$_SESSION[sns][sns_type]	= $sns_type;
$_SESSION[sns][sns_login]	= $sns_login;
$_SESSION[sns][sns_login_id]= $sns_login_id;


$ssltype=$_POST["ssltype"];
$sessid=$_POST["sessid"];
$nexturl=$_POST["nexturl"];
$signagetype=$_POST["signagetype"];

$chUrl=trim(urldecode($_REQUEST["chUrl"]));
$history="-1";
$ssllogintype="";
if($ssltype=="ssl" && strlen($id)>0 && strlen($sessid)==32) {
	$ssllogintype="ssl";
	$history="-2";
}

$memoutinfo	= $_REQUEST["memoutinfo"]; // 회원 탈퇴시 소멸내역정보(2016.08.18 - 김재수 추가)

//로그인을 안한상태에서 로그아웃 또는 회원탈퇴 시도시에....
if(strlen($_ShopInfo->getMemid())==0 && ($type=="logout" || $type=="exit")) {
	echo "<html><head><title></title></head><body onload=\"location.href='".$Dir.FrontDir."main.php'\"></body></html>";exit;
}


if($type=="exit") {
	if($_data->memberout_type=="N") {
		alert_go("회원탈퇴를 하실 수 없습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.",-1);
	}
	$sql = "SELECT name,email,mobile FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		if($row->member_out=="Y") {
			alert_go('회원 아이디가 존재하지 않습니다.',-1);
		}
		$exitname=$row->name;
		$exitemail=$row->email;
		$exitmobile=$row->mobile;
		
		//로그 저장 텍스트를 만든다.
		$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";
		//if ($row=pmysql_fetch_object($result)) {
			foreach($row as $key=>$val){
				$savetemp.= $key." : ".$val."\n";
			}
		//}
		$savetemp.= "\n";
	} else {
		alert_go('회원 아이디가 존재하지 않습니다.',-1);
	}
	pmysql_free_result($result);
}

if ($type=="logout" || $type=="exit") { 
	if($type=="exit") {
		$state="N";
		if($_data->memberout_type=="O") {
			$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
			$result= pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			/*if($row->cnt==0) {
				$sql ="DELETE FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
				$state="Y";
			} else {*/
				$sql = "UPDATE tblmember SET ";
				$sql.= "passwd			= '', ";
				$sql.= "resno			= '', ";
				$sql.= "email			= '', ";
				$sql.= "mobile			= '', ";
				$sql.= "news_yn			= 'N', ";
				$sql.= "age				= '', ";
				$sql.= "gender			= '', ";
				$sql.= "job				= '', ";
				$sql.= "birth			= '', ";
				$sql.= "home_post		= '', ";
				$sql.= "home_addr		= '', ";
				$sql.= "home_tel		= '', ";
				$sql.= "office_post		= '', ";
				$sql.= "office_addr		= '', ";
				$sql.= "office_tel		= '', ";
				$sql.= "memo			= '', ";
				$sql.= "reserve			= 0, ";
				$sql.= "joinip			= '', ";
				$sql.= "ip				= '', ";
				$sql.= "authidkey		= '', ";
				$sql.= "group_code		= '', ";
				$sql.= "member_out		= 'Y', ";
				$sql.= "dupinfo		= '', ";
				$sql.= "sns_type		= '', ";
				$sql.= "act_point		= 0, ";
				$sql.= "etcdata			= '' ";
				$sql.= "WHERE id = '".$_ShopInfo->getMemid()."'";
				$state="V";
			//}
			pmysql_free_result($result);
			pmysql_query($sql,get_db_conn());

			//탈퇴회원정보를 파일로 저장한다.
			$file = "../data/backup/tblmember_out_".date("Y")."_".date("m")."_".date("d").".txt";
			if(!is_file($file)){
				$f = fopen($file,"a+");
				fclose($f);
				chmod($file,0777);
			}
			file_put_contents($file,$savetemp,FILE_APPEND);

			//$sql = "DELETE FROM tblpoint WHERE mem_id='".$_ShopInfo->getMemid()."'";
			//pmysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."'";
			pmysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblmemo WHERE id='".$_ShopInfo->getMemid()."'";
			pmysql_query($sql,get_db_conn());
			//$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='".$_ShopInfo->getMemid()."'";
			//pmysql_query($sql,get_db_conn());
			//$sql = "DELETE FROM tblrecomendlist WHERE id='".$_ShopInfo->getMemid()."'";
			//pmysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."'";
			pmysql_query($sql,get_db_conn());

			//$text = "alert('해당 ID를 탈퇴처리해 드렸습니다.');";
		} else {
			$text = "alert('쇼핑몰에서 확인후 처리해 드립니다.');";
		}

		//list($out_reason, $out_reason_content) = pmysql_fetch("SELECT out_reason, out_reason_content FROM tblmemberout_temp WHERE id = '".$_ShopInfo->getMemid()."'");

		$sql = "INSERT INTO tblmemberout ( 
		id, name, email, tel, ip, 
		state, date, out_reason, out_reason_content) VALUES (
		'".$_ShopInfo->getMemid()."', '".$exitname."', '".$exitemail."', '".$exitmobile."', '".$_SERVER['REMOTE_ADDR']."', 
		'".$state."', '".date("YmdHis")."', '0', '".$out_reason_content."') ";
		pmysql_query($sql,get_db_conn());

		pmysql_query("DELETE FROM tblmemberout_temp WHERE id='".$_ShopInfo->getMemid()."'", get_db_conn());
		
		if ($out_access_type =='') $out_access_type = "web";
		//---------------------------------------------------- 탈퇴시 로그를 등록한다. ----------------------------------------------------//
		$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$_ShopInfo->getMemid()."','out','".$out_access_type."','".date("YmdHis")."')";
		pmysql_query($memLogSql,get_db_conn());
		//---------------------------------------------------------------------------------------------------------------------------------//

        // ERP로 회원정보 전송..2016-12-19
        sendErpMemberInfo($_ShopInfo->getMemid(),"out");

		//SMS 발송
		sms_autosend( 'mem_out', $_ShopInfo->getMemid(), '', '' );
		//SMS 관리자 발송
		sms_autosend( 'admin_out', $_ShopInfo->getMemid(), '', '' );
	}

	if($type=="logout") {
		$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$_ShopInfo->getMemid()."' ";
		pmysql_query($sql,get_db_conn());
	}

	$_ShopInfo->SetMemNULL();
	$_ShopInfo->Save();

	if(file_exists($Dir.DataDir."design/intro.htm")) {
		$url=$Dir."index.php";
	} else {
		if ($out_access_type == 'mobile') {
			$url=$Dir.'m/logout.php';
		} else {
			$url=$Dir;
		}
	}
	if($type=="exit") {
		//echo $text;
		if ($memoutinfo) $url_add_pram	= "?memoutinfo={$memoutinfo}";

		//탈퇴메일 발송 처리
		if(ord($exitemail)) {
			SendOutMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->out_msg, $_data->info_email, $exitemail, $exitname);
		}
		if( $basename == "mypage_memberout.php" ){ // 모바일 결제시 m으로 가게 변경
			/*
			$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
			if(preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && !$_GET[pc]) {
				//광고파라미터 "index.php?".$_SERVER["QUERY_STRING"] 로 가야하는것이 맞는지 확인
				//$mainurl="m/?".$_SERVER["QUERY_STRING"];
				$url=$Dir.'m/';
				exdebug( $url );
			}
			*/
			if ($out_access_type == 'mobile') {
				$url=$Dir.'m/logout.php'.$url_add_pram;
			} else {
				//$url="/";
				$url=$Dir.'front/mypage_memberout.php'.$url_add_pram;
			}
		}
	}

	if($_data->frame_type=="Y") {
		echo "<script>{$text}location.href='".$url."';</script>";
	} else {
		echo "<script>{$text}parent.location.href='".$url."';</script>";
	}
	exit;
}

if($ssllogintype!="ssl") {
	$passwd_type='';
	$alertMsg='';
	if($sns_login){
		$sql = "SELECT passwd, id FROM tblmember WHERE sns_type='".$sns_login_id."' ";
		//$alertMsg='SNS로 간편가입을 하지 않았습니다.';
		//$alertMsg='SNS 간편회원가입을 먼저 진행해 주십시오.';
	}else{
		$sql = "SELECT passwd, id FROM tblmember WHERE id='".$id."' ";
		$alertMsg='아이디 또는 비밀번호가 틀립니다.';
	}
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {

		// 회원등급 변경 상위 이동(2016.10.26 - 김재수 추가)
		//ChangeGrade($row->id);

		if(substr($row->passwd,0,3)=="$1$") {
			$passwd_type="hash";
			$hashdata=$row->passwd;
		} else if(strlen($row->passwd)==16) {
			$passwd_type="password";
			$chksql = "SELECT PASSWORD('1') AS passwordlen ";
			$chkresult=pmysql_query($chksql,get_db_conn());
			if($chkrow=pmysql_fetch_object($chkresult)) {
				if(strlen($chkrow->passwordlen)==41 && $chkrow->passwordlen[0]=="*") {
					$passwd_type="old_password";
				}
			}
			pmysql_free_result($chkresult);
		} else if(substr($row->passwd,0,1) == "*" && strlen($row->passwd) == 41){
			// mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
			$passwd_type = "sha1";
			$shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
		} else {
			$passwd_type="md5";
		}
	} else {
		if($sns_login){
			//alert_go($alertMsg, "/plugin/sns/sns_access.php?sns=".$sns_type);
			//alert_go($alertMsg, (int)$history);
			alert_go($alertMsg, "member_certi.php");
		}else{
			alert_go($alertMsg, (int)$history);
		}
	}
	pmysql_free_result($result);
}

if($sns_login){
	$sql = "SELECT a.*, b.group_level,b.group_wsmember FROM tblmember a  left join tblmembergroup b on a.group_code=b.group_code WHERE a.sns_type='".$sns_login_id."' ";
}else{
	$sql = "SELECT a.*, b.group_level,b.group_wsmember 
	FROM tblmember a  
	left join tblmembergroup b on a.group_code=b.group_code 
	WHERE a.id='".$id."' ";
	$alt_text	= "아이디";
	if($ssllogintype=="ssl") {
		$sql.= "AND a.authidkey='".$sessid."' ";
	} elseif($vid=='') {
		if($passwd_type=="hash") {
			$sql.= "AND a.passwd='".crypt($passwd, $hashdata)."' ";
		} elseif($passwd_type=="password") {
			$sql.= "AND a.passwd=password('".$passwd."')";
		} elseif($passwd_type=="old_password") {
			$sql.= "AND a.passwd=old_password('".$passwd."')";
		} elseif($passwd_type=="md5") {
			$sql.= "AND a.passwd=md5('".$passwd."')";
		} elseif($passwd_type=="sha1") {
			$sql.= "AND a.passwd = '".$shadata."'";
		}
	}
}
//exdebug($sql);exit;

//기본 쇼핑몰 타입을 위한 값 셋팅 (2015.12.07 김재수) / 나중에 지워줘야 함
$affiliatetype = 1;

$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$memid=$row->id;
	$memname=$row->name;
	$memnickname=$row->nickname;
	$mememail=$row->email;
	$memgroup=$row->group_code;
	$memreserve=$row->reserve;
	$memlevel=$row->group_level;
	$wsmember=$row->group_wsmember;
	$memgender=$row->gender;
	$memage=$row->age;
	$staff_type=$row->staff_type;
	$staff_yn=$row->staff_yn;
	$cooper_yn=$row->cooper_yn;
	$staffcardno=$row->staffcardno;

	if(!$memage) { // 회원나이가 없을경우
		if ($row->birth) { //생일이 있을 경우
			$mem_birthday = str_replace("-","",$row->birth); 
			//만나이
			$mem_birthday1 = date("Ymd", strtotime( $mem_birthday )); //생년월일
			$mem_nowday1 =  date('Ymd'); //현재날짜
			$memage			= floor(($mem_nowday1 - $mem_birthday1) / 10000); //만나이
		}
	}

	if($row->member_out=="Y") {	//탈퇴한 회원
		alert_go($alt_text.' 또는 비밀번호가 틀리거나 탈퇴한 회원입니다.',(int)$history);
	}
	if ($_data->member_baro=="Y" && $row->confirm_yn=="N" && $row->mem_type == "1") { //관리자인증기능여부 및 회원인증 검사
		alert_go("쇼핑몰 운영자 인증 후 로그인이 가능합니다.\\n\\n전화로 문의바랍니다.\\n\\n".$_data->info_tel,(int)$history);
	}
	if($_data->coupon_ok=="Y" && strlen( $memid ) > 0 ) {
		include_once($Dir."lib/coupon.class.php");
		$ci = new CouponInfo();
		# 즉시
		$ci->set_coupon( '1' );
		$ci->search_coupon( '', $memid ); // 쿠폰 확인
		$ci->set_couponissue( $memid ); // 등록 테이블
		$ci->insert_couponissue(); // 발급
		# 무료배송
		$ci->set_coupon( '9' );
		$ci->search_coupon( '', $memid ); // 쿠폰 확인
		$ci->set_couponissue( $memid ); // 등록 테이블
		$ci->insert_couponissue(); // 발급
		# 생일
		$ci->set_coupon( '10' );
		$ci->search_coupon( '', $memid ); // 쿠폰 확인
		$ci->set_couponissue( $memid ); // 등록 테이블
		$ci->insert_couponissue(); // 발급
		# 회원 등급
		$ci->set_coupon( '15' );
		$ci->search_coupon( '', $memid ); // 쿠폰 확인
		$ci->set_couponissue( $memid ); // 등록 테이블
		$ci->insert_couponissue(); // 발급
		/*
		$date = date("YmdHis");
		$sql2 = "SELECT coupon_code FROM tblcouponissue WHERE id='".$memid."'";
		$result2 = pmysql_query($sql2,get_db_conn());
        $coupon_code = '';
		while($row2=pmysql_fetch_object($result2)){
		  $coupon_code.="'".$row2->coupon_code."',";
		}
        $coupon_code.= "'13171812',"; // [PRE-OPEN 기념] 기존회원을 위한 특별한 20% 할인쿠폰
		$coupon_code = substr($coupon_code,0,-1);
		pmysql_free_result($result2);

		$sql2 = "SELECT coupon_code, date_start, date_end, vender FROM tblcouponinfo ";
		$sql2.= "WHERE display='Y' AND issue_type='N' ";
		$sql2.= "AND (coupon_is_mobile='A' OR coupon_is_mobile='P' OR coupon_is_mobile='B' OR coupon_is_mobile='C') ";
		$sql2.= "AND (member='ALL' OR (member='".$memgroup."' AND member!='')) ";
		$sql2.= "AND (date_end>'".substr($date,0,10)."' OR date_end='') ";
		if(strlen($coupon_code)>0) $sql2.="AND coupon_code NOT IN (".$coupon_code.") ";

		$sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
		$couponcnt ="";
		$count=0;
		$result2 = pmysql_query($sql2,get_db_conn());
		while($row2=pmysql_fetch_object($result2)) {
			if($row2->date_start>0) {
				$date_start=$row2->date_start;
				$date_end=$row2->date_end;
			} else {
				$date_start = substr($date,0,10);
				$date_end = date("Ymd23",strtotime("+".abs($row2->date_start)." day"));
			}
			$sql.=" ('".$row2->coupon_code."','".$memid."','".$date_start."','".$date_end."','".$date."'),";
			$couponcnt.="'".$row2->coupon_code."',";
			$count++;
		}
		pmysql_free_result($result2);
		if($count>0) {
			$sql = substr($sql,0,-1);
			pmysql_query($sql,get_db_conn());
			if(!pmysql_errno()) {
				$couponcnt = substr($couponcnt,0,-1);
				$sql = "UPDATE tblcouponinfo SET issue_no=issue_no+1 ";
				$sql.= "WHERE coupon_code IN (".$couponcnt.")";
				pmysql_query($sql,get_db_conn());
				$msg = "회원 쿠폰이 발급되었습니다.";
			}
		}
		*/
	}
} else {
	if($ssllogintype!="ssl") {
		alert_go('비밀번호가 틀립니다.',(int)$history);
	} else {
		echo "<html><head><title></title></head><body onload=\"history.go(".$history.")\"></body></html>";exit;
	}
}
pmysql_free_result($result);

$authidkey = md5(uniqid(""));
$_ShopInfo->setMemid($memid);
$_ShopInfo->setNickName($memnickname);
$_ShopInfo->setAuthidkey($authidkey);
$_ShopInfo->setMemgroup($memgroup);
$_ShopInfo->setMemname($memname);
$_ShopInfo->setMemreserve($memreserve);
$_ShopInfo->setMememail($mememail);
$_ShopInfo->setMemlevel($memlevel);
$_ShopInfo->setWsmember($wsmember);
$_ShopInfo->setStaffType($staff_type);
$_ShopInfo->setStaffYn($staff_yn);
$_ShopInfo->setCooperYn($cooper_yn);
$_ShopInfo->setStaffCardNo($staffcardno);
$_ShopInfo->Save();

$sql = "UPDATE tblmember SET ";
$sql.= "authidkey		= '".$authidkey."', ";
if(!$sns_login){
	if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
		$sql.= "passwd		= '".md5($passwd)."', ";
	} else if($passwd_type=="sha1") {
		$sql.= "passwd		= '*".strtoupper(SHA1(unhex(SHA1($passwd))))."', ";
	}
}
$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
$sql.= "logindate		= '".date("YmdHis")."', ";

## 로그인시 토큰 추가 시작
$push_os = $_POST["push_os"];
$push_token=$_POST["push_token"];
if($push_token){
	if($push_os == "Android"){
		$sql.= "push_token		= '".$push_token."', ";
	}else{
		$sql.= "push_token_ios		= '".$push_token."', ";
	}
}
## 로그인시 토큰 추가 종료

$sql.= "logincnt		= logincnt+1 ";
$sql.= "WHERE id='".$_ShopInfo->getMemid()."'";
//echo $sql;exit;
pmysql_query($sql,get_db_conn());

//--------------------------------- 로그인시 장바구니를 로그인 아이디로 한다. (2016.06.22 - 김재수 추가)-------------------------------//
/*
$upDelQuery = "DELETE FROM tblbasket where basketidx NOT IN (select MAX(basketidx) basketidx 
from tblbasket 
where 
(tempkey = '".$_ShopInfo->getTempkey()."' AND id='') 
or id = '".$_ShopInfo->getMemid()."' 
group by tempkey, productcode, opt1_idx, opt2_idx, optidxs, package_idx, assemble_idx)
AND ((tempkey = '".$_ShopInfo->getTempkey()."' AND id='') 
or id = '".$_ShopInfo->getMemid()."') ";
pmysql_query( $upDelQuery, get_db_conn() );
*/
$upNewQuery = "UPDATE tblbasket SET id = '".$_ShopInfo->getMemid()."' WHERE tempkey = '".$_ShopInfo->getTempkey()."' AND id='' ";
pmysql_query( $upNewQuery, get_db_conn() );

//---------------------------------------------------------------------------------------------------------------------------------//

//---------------------------------------------------- 로그인시 로그를 등록한다. ----------------------------------------------------//
$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$_ShopInfo->getMemid()."','login','web','".date("YmdHis")."')";
pmysql_query($memLogSql,get_db_conn());
//---------------------------------------------------------------------------------------------------------------------------------//


//로그인시 포인트 소멸체크 및 업데이트(2015.11.25 - 김재수 추가)
/*if($_data->reserve_maxuse >= 0) { 
    $sum_point = get_point_sum($_ShopInfo->getMemid()); 
    $sql= " update tblmember set reserve = '{$sum_point}' where id = '".$_ShopInfo->getMemid()."' "; 
    pmysql_query($sql); 
    //echo "sql1 = ".$sql."<br>"; 
}*/

//로그인시 포인트 소멸체크 및 업데이트(2015.11.25 - 김재수 추가)
if($_data->e_reserve_maxuse >= 0) { 
    $sum_act_point = get_point_act_sum($_ShopInfo->getMemid()); 
    $sql= " update tblmember set act_point = '{$sum_act_point}' where id = '".$_ShopInfo->getMemid()."' "; 
    pmysql_query($sql); 
    //echo "sql1 = ".$sql."<br>"; 
}

//로그인시 이전주문체크 정보 삭제(2016.09.06 - 김재수 추가)
$sql= " delete from tblorder_check where id = '".$_ShopInfo->getMemid()."' "; 
pmysql_query($sql); 

$loginday = date("Ymd");

//로그인시 포인트 지급(2015.02.28 - 김재수 추가)
include_once($Dir."conf/config.ap_point.php");
$tp_login_cnt		= $pointSet['login']['count']; 
$tp_login_point	= $pointSet['login']['point']; 

// 오늘 로그인시 적립받은 갯수를 체크한다.
list($lp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) lp_cnt from tblpoint_act WHERE rel_flag='@login_point' and regdt >= '{$loginday}000000' AND regdt <= '{$loginday}999999' AND mem_id = '".$_ShopInfo->getMemid()."' "));
if ($lp_cnt < $tp_login_cnt) { // 로그인시 적립받은 갯수가 설정수보다 작으면
	insert_point_act($_ShopInfo->getMemid(), $tp_login_point, '로그인 포인트', '@login_point', '', date("YmdHis"), 0);
}

// 회원등급 변경(2016.09.30 - 김재수 추가)
//ChangeGrade($_ShopInfo->getMemid());

// 회원 온오프라인통합포인트내역 가져오기(2017.04.13 - 김재수 추가)
getErpOnOffPoint($_ShopInfo->getMemid());
getErpMeberPoint($_ShopInfo->getMemid());


$sql = "SELECT id_list FROM tblshopcountday ";
$sql.= "WHERE date='".$loginday."'";
$result = pmysql_query($sql,get_db_conn());
if($row3 = pmysql_fetch_object($result)){
	if(!strpos(" ".$row3->id_list,"".$_ShopInfo->getMemid()."")){
		$id_list=$row3->id_list.$_ShopInfo->getMemid()."";
		$sql = "UPDATE tblshopcountday SET id_list='".$id_list."',login_cnt=login_cnt+1 ";
		$sql.= "WHERE date='".$loginday."'";
		pmysql_query($sql,get_db_conn());
	}
} else {
	$id_list="".$_ShopInfo->getMemid()."";
	$sql = "INSERT INTO tblshopcountday (date,count,login_cnt,id_list) VALUES ('".$loginday."',1,1,'".$id_list."')";
	pmysql_query($sql,get_db_conn());
}

?>
<?
	if($biz[bizNumber]){
?>
	<script type="text/javascript">var _TRK_LID="<?=$biz[bizNumber]?>";var _L_TD="ssl.logger.co.kr";</script>
	<script type="text/javascript">var _CDN_DOMAIN = location.protocol == "https:" ? "https://fs.bizspring.net" : "http://fs.bizspring.net";document.write(unescape("%3Cscript src='" + _CDN_DOMAIN +"/fs4/bstrk.1.js' type='text/javascript'%3E%3C/script%3E"));</script>
	<noscript><img alt="Logger Script" width="1" height="1" src="http://ssl.logger.co.kr/tracker.tsp?u=<?=$biz[bizNumber]?>&amp;js=N" /></noscript>

	<script type="text/javascript">
		_TRK_PI = "LIR"; 
		_TRK_SX = "{성별코드}";          // M:남자, F:여자, U:기타(기업, 법인등)
		_TRK_AG = "{연령코드}";         // A~Z  A:10대이하, B:10대, C:20대, D:30대, E:40대, F:50대, G:60대  
	</script>

	<script type="text/javascript">
		var _ag = "<?=$memage?>";
		var _id = "<?=$id?>";
		var _mr = "<?=$married?>";
		var _gd = "<?=$memgender=='1'?'man':'woman'?>";
	</script>
<?
	}

/*
if($signagetype=="1"){
	echo "<script>parent.location.href='".$Dir.MainDir."winning.php';</script>";
	exit;
}
*/


if($ssllogintype!="ssl") {
	if ($_data->frame_type!="N") {
		
		/*if(strlen($chUrl)>0){
			Header("Location:".$Dir.FrontDir."mypage_pw.php?chUrl=".$chUrl);
		}

		Header("Location:".$Dir.FrontDir."mypage_pw.php?chUrl=".$chUrl);		*/

        /**
        * 로그인 완료페이지는 http로 처리한다
        * 작성자 : 유동혁
        * 날짜   : 2016-12-08
        */
        /*
        $rUrl = '';
        if(strlen($chUrl)>0){
            $rUrl = "/" . FrontDir. "mypage_pw.php?chUrl=". urlencode($chUrl);
        } else {
            $rUrl = "/" . MainDir. "main.php";
        }
        $redirect_url = "http://" . $_SERVER['HTTP_HOST'] . $rUrl; //$_SERVER['REQUEST_URI'];

        echo "<script>";
        echo "  parent.location.href = '" . $redirect_url . "'; ";
        echo "</script>";
        */
		if(strlen($chUrl)>0){
			echo "<script>parent.location.href='mypage_pw.php?chUrl=".urlencode($chUrl)."'; </script>";
		} else {
			echo "<script>parent.location.href='".$Dir.MainDir."main.php';</script>";
		}
		exit;
	} else {
		if(strlen($chUrl)>0){
			echo "<script>location.href='mypage_pw.php?chUrl=".urlencode($chUrl)."'; </script>";
		} else {
			echo "<script>location.href='".$Dir.MainDir."main.php'; parent.topmenu.history.go(0);</script>";
		}
		exit;
	}
} else {
	if ($_data->frame_type!="N") {
		if(strlen($nexturl)>0) {
			echo "<script>parent.location.href='".$nexturl."'</script>";
		} else {
			echo "<script>parent.location.href='".$Dir.MainDir."main.php'</script>";
		}
		exit;
	} else {
		if(strlen($nexturl)>0) {
			echo "<script>parent.location.href='".$nexturl."'; parent.parent.topmenu.history.go(0);</script>";
		} else {
			echo "<script>parent.location.href='".$Dir.MainDir."main.php'; parent.parent.topmenu.history.go(0);</script>";
		}
		exit;
	}
}
?>
