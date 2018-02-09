<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	/*
	//같은 게시판 ID의 중복 데이터 삭제
	$sqlaa = "SELECT a.* from (select count(groupid) aaa, groupid, max(num) num from tblboard where trim(groupid) != '' group by board, groupid) a WHERE a.aaa > 1 order by a.groupid desc";
	$resultaa=pmysql_query($sqlaa);
	while($rowaa=pmysql_fetch($resultaa)) {
		$sql="delete from tblboard WHERE num = '".$rowaa[num]."'";
		pmysql_query($sql,get_db_conn());
	}	
	$sqlaa = "SELECT a.* from (select count(board_uni) aaa, board_uni, max(num) num from tblboard group by board_uni) a WHERE a.aaa > 1 order by a.board_uni desc";
	$resultaa=pmysql_query($sqlaa);
	while($rowaa=pmysql_fetch($resultaa)) {
		$sql="delete from tblboard WHERE num = '".$rowaa[num]."'";
		pmysql_query($sql,get_db_conn());
	}
	*/

	/*
	//쓰레드 처리
	$sql = "SELECT * FROM tblboard ORDER BY num asc ";
	$result=pmysql_query($sql,get_db_conn());
	$cnt=0;
	$thread = 999999999;
	while($row=pmysql_fetch($result)) {
		pmysql_query("UPDATE tblboard SET thread = '".$thread."' WHERE num = '".$row[num]."'");
		$thread--;
	}
	*/


	/*
	//아이디 처리
	$sql = "SELECT b.id, a.num, a.m_no FROM tblboard a LEFT JOIN tblmember b on a.m_no = b.m_no WHERE a.mem_id = '' ORDER BY a.num asc ";
	$result=pmysql_query($sql,get_db_conn());
	$arrayMember = array("1"=>"suejwang", "15267"=>"엽스", "15269"=>"수제이", "17572"=>"실버", "22750"=>"지영", "22806"=>"라임", "51433"=>"민트");
	while($row=pmysql_fetch($result)) {
		if($row[id]){
			pmysql_query("UPDATE tblboard SET mem_id = '".$row[id]."' WHERE num = '".$row[num]."'");
		}else{
			if($arrayMember[$row[m_no]]){
				debug("UPDATE tblboard SET mem_id = '".$arrayMember[$row[m_no]]."' WHERE num = '".$row[num]."'"."////////////".$row[id]);
				pmysql_query("UPDATE tblboard SET mem_id = '".$arrayMember[$row[m_no]]."' WHERE num = '".$row[num]."'");
			}else{
				//pmysql_query("UPDATE tblboard SET mem_id = 'suejwang', name = '관리자' WHERE num = '".$row[num]."'");
			}
		}
	}
	*/


	/*
	//이전글 다음글 처리
	$arrayBoardId = array("notice", 	"review", "qana", "soapnew", "studya", "studyb", "tip", "movie", "myphoto", "free", "diary", "faq", "packing", "story", "choice", "bigorder", "recipesix", "MS", "Paper", "banking", "kit", "soapschool", "commongsoon", "open", "hairzc", "testb", "testa", "wish", "BestBloger", "emoney", "mtbook", "bloger", "event", "dayz", "ImMD", "aromatip", "companywholesale", "soaptip", "cosmetictip", "lifetip", "usetip", "personalwholesale", "FifthEvent", "Aprilevent", "therapywholesale", "workshop", "octEvent", "QuizEvent", "octquiz");
	foreach($arrayBoardId as $v){
		$sql = "SELECT * FROM tblboard WHERE notice = '0' AND board = '".$v."' ORDER  BY num";
		$result=pmysql_query($sql);
		$tmpPrevNum = 0;
		$tmpNextNum = 0;
		$sqlTotal = pmysql_query("SELECT count(board) total FROM tblboard WHERE notice = '0' AND board = '".$v."'");
		$totalCount = pmysql_fetch($sqlTotal);
		$boardCount = 0;
		while($row=pmysql_fetch($result)) {
			$boardCount++;
			$tmpPrevNum = $row[num]+1;
			if($boardCount == $totalCount[total]){
				$tmpPrevNum = 0;
			}
			pmysql_query("UPDATE tblboard SET prev_no = '".$tmpPrevNum."', next_no = '".$tmpNextNum."' WHERE num = '".$row[num]."'");
			//debug($tmpPrevNum."||".$tmpNextNum);
			//pmysql_query("UPDATE tblboard SET thread = '".$thread."' WHERE num = '".$row[num]."'");
			$tmpNextNum = $row[num];
		}
	}
	*/


	/*
	//코멘트 아이디 처리
	$sql = "SELECT b.id, a.num, a.m_no FROM tblboardcomment a LEFT JOIN tblmember b on a.m_no = b.m_no WHERE a.c_mem_id = '' ORDER BY a.num asc ";
	$result=pmysql_query($sql,get_db_conn());
	$arrayMember = array("1"=>"suejwang", "15267"=>"엽스", "15269"=>"수제이", "17572"=>"실버", "22750"=>"지영", "22806"=>"라임", "51433"=>"민트");
	while($row=pmysql_fetch($result)) {
		if($row[id]){
			pmysql_query("UPDATE tblboardcomment SET c_mem_id = '".$row[id]."' WHERE num = '".$row[num]."'");
		}else{
			if($arrayMember[$row[m_no]]){
				debug("UPDATE tblboardcomment SET c_mem_id = '".$arrayMember[$row[m_no]]."' WHERE num = '".$row[num]."'"."////////////".$row[id]);
				pmysql_query("UPDATE tblboardcomment SET c_mem_id = '".$arrayMember[$row[m_no]]."' WHERE num = '".$row[num]."'");
			}else{
				//pmysql_query("UPDATE tblboardcomment SET c_mem_id = 'suejwang', name = '관리자' WHERE num = '".$row[num]."'");
			}
		}
	}
	//코멘트 원글 번호 처리
	$sql = "SELECT b.num, a.num as anum FROM tblboardcomment a LEFT JOIN tblboard b on a.board_uni = b.board_uni ORDER BY num asc ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {
		if(!$row[num]){
			//pmysql_query("DELETE FROM tblboardcomment WHERE num = '".$row[anum]."'");
		}else{
			pmysql_query("UPDATE tblboardcomment SET parent = '".$row[num]."' WHERE num = '".$row[anum]."'");
		}
	}
	*/

	/*
	//코멘트 갯수 수정 처리
	$sql = "SELECT a.board_uni, a.num, b.total FROM tblboard a LEFT JOIN (select count(board_uni) total, board_uni from tblboardcomment group by board_uni) b on a.board_uni = b.board_uni WHERE a.total_comment != b.total";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {
		pmysql_query("UPDATE tblboard SET total_comment = '".$row[total]."' WHERE num = '".$row[num]."'");
	}
	*/
	/*
	$sql = "SELECT * from (select count(idx_main) asd, idx_main, board from tblboard group by idx_main, board) a where a.asd > 1 order by board";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {

		$result_total=pmysql_query("SELECT count(*) aaa, max(thread) thread from tblboard where board = '".$row[board]."' AND idx_main = '".$row[idx_main]."' group by idx_main, board");
		$cnt_total=pmysql_fetch($result_total);

		$sql_sub = "SELECT num from tblboard where board = '".$row[board]."' AND idx_main = '".$row[idx_main]."' order by thread";
		$result_sub=pmysql_query($sql_sub);
		$count = 1;
		$cnt_total_temp = $cnt_total[aaa]-1;
		while($row_sub=pmysql_fetch($result_sub)) {
			if($count < $cnt_total[aaa]){
				//debug("         UPDATE tblboard set thread = '".$cnt_total[thread]."', pos = '".$cnt_total_temp."', depth = '1' WHERE num = '".$row_sub[num]."' AND board = '".$row[board]."' AND idx_main = '".$row[idx_main]."'");
				pmysql_query("UPDATE tblboard set thread = '".$cnt_total[thread]."', pos = '".$cnt_total_temp."', depth = '1' WHERE num = '".$row_sub[num]."' AND board = '".$row[board]."' AND idx_main = '".$row[idx_main]."'");
			}
			$count++;
			$cnt_total_temp--;
		}
	}
	*/
?>