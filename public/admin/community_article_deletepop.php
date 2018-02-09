<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

include($Dir.BoardDir."file.inc.php");

$board=$_REQUEST["board"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$cart=$_REQUEST["cart"];

if (count($cart)<=0) {
	echo "<script>window.close();</script>";
	exit;
}

$qry = "WHERE 1=1 ";
if(ord($board)) $qry.= "AND board='{$board}' ";

for($y=count($cart)-1;$y>=0;$y--) {
	$tmp_num = explode("",$cart[$y]);
	$num=$tmp_num[0];

	$sql="SELECT * FROM tblboard {$qry} AND num='{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row = pmysql_fetch_object($result)) {
		if ($row->pos<>0) {
			// 게시물을 삭제하자
			$sql = "DELETE FROM tblboard WHERE board='{$row->board}' AND num={$num} ";
			$isUpdate = true;
		} else {
			$sql2 = "SELECT COUNT(*) FROM tblboard WHERE board='{$row->board}' ";
			$sql2.= "AND thread={$row->thread} ";
			$result2 = pmysql_query($sql2,get_db_conn());
			$deleteTotal = pmysql_result($result2,0,0);
			pmysql_free_result($result2);

			if ($deleteTotal == 1) {
				$sql = "DELETE FROM tblboard WHERE board='{$row->board}' AND num = {$num} ";
				$isUpdate = true;
			} else {
				$delMsg = "관리자 또는 작성자에 의해 삭제되었습니다.";
				$sql  = "UPDATE tblboard SET 
				prev_no = 0, 
				next_no = 0, 
				use_html = '0', 
				title = '{$delMsg}', 
				filename = '', 
				total_comment = 0, 
				content = '{$delMsg}', 
				notice = '0', 
				deleted = '1' 
				WHERE board='{$row->board}' AND num={$num} ";
			}
		}
		$delete = pmysql_query($sql,get_db_conn());

		if($delete) {
			if($row->prev_no) pmysql_query("UPDATE tblboard SET next_no='{$row->next_no}' WHERE board='{$row->board}' AND next_no='{$row->num}'",get_db_conn());
			if($row->next_no) pmysql_query("UPDATE tblboard SET prev_no='{$row->prev_no}' WHERE board='{$row->board}' AND prev_no='{$row->num}'",get_db_conn());

			// ===== 관리테이블의 게시글수 update =====
			$in_max_qry='';
			$in_total_qry='';
			if ($row->pos == 0) {
				if ($row->prev_no == 0) {
					$in_max_qry = "max_num = '{$row->next_no}' ";
				}
			}
			if ($isUpdate) {
				$in_total_qry = "total_article = total_article - 1 ";
			}

			$sql3 = "UPDATE tblboardadmin SET ";
			if ($in_max_qry) $sql3.= $in_max_qry;
			if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
			else if (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
			$sql3.= "WHERE board='{$row->board}' ";

			if ($in_max_qry || $in_total_qry) $update = pmysql_query($sql3,get_db_conn());

			if ($row->total_comment > 0) {
				@pmysql_query("DELETE FROM tblboardcomment WHERE board='{$row->board}' AND parent = '{$row->num}'",get_db_conn());
			}

			if($row->filename) {
				$filedel=ProcessBoardFileDel($row->board,$row->filename);
			}
		}
	}
	pmysql_free_result($result);
}
echo "
	<script>
		opener.location.reload();
		window.close();
	</script>
";
exit;
