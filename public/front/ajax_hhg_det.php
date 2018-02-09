<?
header("Content-Type: text/html;charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']		= $_ShopInfo->getMemid();
$member['name']	= $_ShopInfo->getMemname();

$mode	= $_REQUEST['mode'];
if ($mode == 'loginCheck'){
	echo $member['id']."|".$member['name'];
} else if ($mode == 'list'){
	$type					= $_REQUEST['type'];
	$track_num		= $_REQUEST['track_num'];

	$sql = "SELECT * FROM tblhhgcomment where 1=1";
	if ($type == 'more') $sql.= " and num < {$track_num} ";
	$sql.= " ORDER BY writetime DESC  LIMIT 6";
	
	$result = pmysql_query($sql,get_db_conn());

	$resultStart = '{"items":'; // 데이터의 기본값을 items로 설정.
	$resultData = '';            // mysql로 넘겨 받는 문자열 변수
	$resultEnd = '}';         // 마무리 문자열 변수

	$resultStart .= '[ ';
	$resultEnd = ' ]}';

	while($rows = pmysql_fetch_object($result)) {
		$resultData .= '{ ';
		foreach($rows as $key => $value) {	$resultData .= '"'.$key.'":"'.str_replace("\n","<br>",$value).'",'; }
		 // 컬럼 값 마지막 부분에 콤마 제거
		$resultData = substr($resultData,0, -1);
   		$resultData .= '},';
	}

	// 데이터 값 마지막 부분에 콤마 제거 
	$resultData = substr($resultData,0, -1);
	echo $resultStart.$resultData.$resultEnd;  // 결과값 전달
	pmysql_free_result($result);

} else {
	if ($member['id'] =='') {
		echo "noConnect";
		exit;	
	}

	$num					= $_REQUEST['num'];
	$uid					= $_REQUEST['uid'];
	$up_name			= iconv("UTF-8","EUC-KR", $_REQUEST['up_name']);
	$up_passwd		= $_REQUEST['up_passwd'];
	$up_comment		= iconv("UTF-8","EUC-KR", $_REQUEST['up_comment']);

	if ($mode == 'ins'){
		$query = "insert into tblhhgcomment(id,name,passwd,ip, comment, writetime) 
			values ('".$uid."','".$up_name."','".$up_passwd."','".$_SERVER['REMOTE_ADDR']."','".$up_comment."','".date('Y-m-d H:i:s',time())."')";

		pmysql_query($query);

		echo "Y";
	}else if ($mode == 'mod'){
		$sql = "SELECT id FROM tblhhgcomment where num='{$num}'";	
		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);

		if ($row->id == $uid) {
			$sql = "UPDATE tblhhgcomment SET ";
			$sql.= "comment		= '{$up_comment}' ";
			$sql.= "WHERE  num = '{$num}' ";
			pmysql_query($sql,get_db_conn());
		} else {
			echo "noPass";
			exit;
		}

		echo "Y";
	}else if ($mode == 'del'){
		$sql = "SELECT id FROM tblhhgcomment where num='{$num}'";	
		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);

		if ($row->id == $uid) {

			$sql = "DELETE  FROM tblhhgcomment ";
			$sql.= "WHERE  num = '{$num}' ";
			pmysql_query($sql,get_db_conn());
		} else {
			echo "noPass";
			exit;
		}
		echo "Y";
	}
}
exit;
/*
	}else if ($mode == 'mod'){
		$sql = "SELECT id, passwd FROM tblhhgcomment where num='{$num}'";	
		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);

		if ($row->id == $uid && $row->passwd == $up_passwd) {
			$sql = "UPDATE tblhhgcomment SET ";
			$sql.= "comment		= '{$up_comment}' ";
			$sql.= "WHERE  num = '{$num}' ";
			pmysql_query($sql,get_db_conn());
		} else {
			echo "noPass";
			exit;
		}

		echo "Y";
	}else if ($mode == 'del'){
		$sql = "SELECT id, passwd FROM tblhhgcomment where num='{$num}'";	
		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);

		if ($row->id == $uid && $row->passwd == $up_passwd) {

			$sql = "DELETE  FROM tblhhgcomment ";
			$sql.= "WHERE  num = '{$num}' ";
			pmysql_query($sql,get_db_conn());
		} else {
			echo "noPass";
			exit;
		}
		echo "Y";
	}
}
exit;*/
?>