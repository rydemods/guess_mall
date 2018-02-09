<?
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=utf-8");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	if($_POST['mode'] == 'writeReviewContents'){

		$sql = "
						INSERT INTO TBLPRODUCTREVIEW_COMMENT
						(
							pnum, 
							id ,
							name ,
							regdt ,
							content
						) VALUES (
							'".$_POST['num']."', 
							'".$_ShopInfo->getMemid()."', 
							'".$_ShopInfo->memname."', 
							'".date("YmdHis")."', 
							'".mb_convert_encoding($_POST['contents'], 'euc-kr', 'utf-8')."'
						)";
		pmysql_query($sql,get_db_conn());

	}else if($_POST['mode'] == 'deleteReview'){
		pmysql_query("DELETE FROM tblproductreview WHERE num = '".$_POST['num']."' AND id = '".$_ShopInfo->getMemid()."'", get_db_conn());
		pmysql_query("DELETE FROM tblproductreview_comment WHERE pnum = '".$_POST['num']."'", get_db_conn());

		$sql = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode = '".$_POST['productcode']."'";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$t_count_review = (int)$row->t_count;
		#echo number_format($t_count_review);
		echo '1';
	}else if($_POST['mode'] == 'deleteReviewContents'){

		pmysql_query("DELETE FROM tblproductreview_comment WHERE no = '".$_POST['no']."'", get_db_conn());

	}
	
	if($_POST['mode'] == 'writeReviewContents' || $_POST['mode'] == 'deleteReviewContents'){

		$sqlReply = "SELECT * FROM tblproductreview_comment WHERE pnum = '".$_POST['num']."' ORDER BY no DESC ";
		$resultReply=pmysql_query($sqlReply,get_db_conn());
		$commentBody = "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
		$commentBody .= "	<colgroup><col width='70' /><col width='' /><col width='40' /></colgroup>";
		while($rowReply=pmysql_fetch_object($resultReply)) {
			$deleteStr = "";
			$valueStr = "";
			if($rowReply->id == $_ShopInfo->getMemid()){
				$deleteStr = "<a href='javascript:;' class = 'reviewCommentDeleteAjax'>x</a>";
			}
			$valueStr = "<input type='hidden' value = '".$_POST['num']."'/><input type='hidden' value = '".$rowReply->no."'/> ";

			$commentBody .= "		<tr><th>".$rowReply->name."</th><td>".$rowReply->content."</td><td>".$valueStr.$deleteStr."</td></tr>";
		}
		$commentBody .= "</table>";
		echo $commentBody;

	}
?>