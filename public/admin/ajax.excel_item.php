<?php
/********************************************************************* 
// 파 일 명		: ajax.excel_item.php 
// 설     명		: 엑셀항목 저장
// 상세설명	: 엑셀항목을 회원별로 저장, 리스트들 가져온다.
// 작 성 자		: 2016.06.23 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST[mode];

#---------------------------------------------------------------
# DB에 저장한다.
#---------------------------------------------------------------
	if($mode=='ins'){				// 등록시
		$mem_id		= $_ShopInfo->getId();
		$item_name	= $_POST['save_item_name'];
		$item_type		= $_POST['item_type'];
		$item				= implode(",",$_POST['est']);
		$date				= date("YmdHis");

		list($chk_cnt)=pmysql_fetch("select count(*) as cnt from tblexcelinfo WHERE mem_id = '{$mem_id}' and item_name='{$item_name}' and item_type='{$item_type}'");

		if ($chk_cnt > 0 ) {
			echo "<html></head><body onload=\"alert('동일한 항목명이 존재합니다.');\"></body></html>";exit;
		}

		$sql = "INSERT INTO tblexcelinfo(
		mem_id		,
		item_name		,
		item_type		,
		item		,
		regdt) VALUES (
		'{$mem_id}', 
		'{$item_name}', 
		'{$item_type}', 
		'{$item}', 
		'{$date}' )";

		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('항목저장이 완료되었습니다.');parent.document.idxform.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('항목저장중 오류가 발생하였습니다.');\"></body></html>";exit;
		}
	} else if($mode=='del'){				// 삭제시
		$eid	= $_POST['sel_save_item'];
		$sql = "DELETE FROM tblexcelinfo WHERE eid = '{$eid}' ";

		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('저장항목 삭제가 완료되었습니다.');parent.document.idxform.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('저장항목 삭제중 오류가 발생하였습니다.');\"></body></html>";exit;
		}
	
	}
?>