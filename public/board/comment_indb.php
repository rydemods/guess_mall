<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include("head.php");

$up_name=$_POST["up_name_two"];
$up_passwd=md5($_POST["up_passwd_two"]);
$up_passwd=substr($up_passwd, 0, 16);
$up_comment=$_POST["up_comment_two"];
$num_two=$_POST["c_num"];
# 비밀뎃글 추가
# 2015 11 23 유동혁
$up_is_secret = $_POST['up_is_secret_two'];
if( is_null( $up_is_secret )  ){
	$up_is_secret == '0';
}

if ($setup['use_comment'] != "Y") {
	$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
	alert_go($errmsg,-1);
}

if ($member['grant_comment']!="Y") {
	$errmsg="댓글쓰기 권한이 없습니다.";
	alert_go($errmsg,-1);
}

if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	$errmsg="잘못된 경로로 접근하셨습니다.";
	alert_go($errmsg,-1);
}


if(isNull($up_comment)) {
	$errmsg="내용을 입력하셔야 합니다.";
	alert_go($errmsg,-1);
}

if(strlen($member['name'])==0) {
	if(isNull($up_name)) {
		$errmsg="이름을 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}
	if(isNull($up_passwd)) {
		$errmsg="비밀번호를 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}
} else {
	$up_name = $member['name'];
}


$up_name = addslashes($up_name);
$up_comment = autoLink($up_comment);
$up_comment = addslashes($up_comment);

if ($setup['use_filter'] == "1") {
	if (isFilter($setup['filter'],$up_comment,$findFilter)) {
		$errmsg="사용 제한된 불량단어를 사용하셨습니다.\\n\\n다시 확인 하십시오.";
		alert_go($errmsg,-1);
	}
}

$check = pmysql_fetch_array(pmysql_query("SELECT num FROM tblboardcomment WHERE board='$board' AND num = '$num_two'",get_db_conn()));
if(!$check[0]) {
	$errmsg="원본 글이 존재하지 않습니다.";
	alert_go($errmsg,-1);
}


$sql  = "INSERT INTO tblboardcomment_re (board,parent,name,passwd,ip,writetime,comment, c_mem_id, is_secret ) VALUES ";
$sql .= "('{$board}','{$num_two}','{$up_name}','{$up_passwd}','".$_SERVER['REMOTE_ADDR']."','".time()."','{$up_comment}', '{$_ShopInfo->memid}', '{$up_is_secret}')";
$insert = pmysql_query($sql,get_db_conn());

// 코멘트 갯수를 구해서 정리

pmysql_query("UPDATE tblboard SET total_comment=total_comment+1 WHERE board='{$board}' AND num='{$num}'",get_db_conn());





if($setup['btype']=="B") {
	if($_POST["frametype"]=="Y") {
		if($mypageid){
			header("Location:board.php?pagetype=comment_frame&board=$board&num=$num&mypageid=$mypageid");	
		}else{
			header("Location:board.php?pagetype=comment_frame&board=$board&num=$num");
		}
		
	} else {
		if($mypageid){
			header("Location:board.php?pagetype=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&mypageid=$mypageid");	
		}else{
			header("Location:board.php?pagetype=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");	
		}
		
	}
} else {
	
	if($mypageid){
		header("Location:board.php?pagetype=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&mypageid=$mypageid");		
	}else{
		header("Location:board.php?pagetype=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");	
	}
	
}


?>