<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

	if( $mode == "write" ){
		$Query = "
							INSERT INTO
							tblmember_question
								(
									id, counsel_id, contents, regdt, counsel_Type
								)
							VALUES
								(
									'$_POST[id]', '$_POST[counsel_id]', '$_POST[contents]', '$_POST[regdt]', '$_POST[counsel_Type]'
								)
		";
		if($insert = pmysql_query($Query)){
			echo "<script>alert('등록 되었습니다.')</script>";
			echo "<script type='text/javascript' src='../js/jquery.js'></script>";
			echo "<script>$('#dataInnerHtml', parent.document).load('./member_question_list.php?id=".$_POST[id]."'); $('#dialog-overlay, #dialog-box', parent.document).hide();  </script>";
			//
		}else{
			echo "<script>alert('등록이 실패 되었습니다.')</script>";
		}
	}

	if( $mode == "update" ){
		$Query = "
							UPDATE
								tblmember_question
							SET
								id = '$_POST[id]', 
								counsel_id = '$_POST[counsel_id]', 
								contents = '$_POST[contents]', 
								regdt = '$_POST[regdt]', 
								counsel_Type = '$_POST[counsel_Type]'
							WHERE
								sno = '".$_POST[sno]."'
		";
		if($insert = pmysql_query($Query)){
			echo "<script>alert('수정 되었습니다.')</script>";
			echo "<script type='text/javascript' src='../js/jquery.js'></script>";
			echo "<script>$('#dataInnerHtml', parent.document).load('./member_question_list.php?id=".$_POST[id]."'); $('#dialog-overlay, #dialog-box', parent.document).hide();  </script>";
			//
		}else{
			echo "<script>alert('등록이 실패 되었습니다.')</script>";
		}
	}
?>