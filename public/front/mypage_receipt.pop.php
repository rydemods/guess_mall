<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	list($shopCode) = pmysql_fetch("SELECT tax_mid FROM tblshopinfo", get_db_conn());
	$orderid = $_GET['orderid'];
	$authno = $_GET['authno'];
	$mode = $_GET['mode'];
	if( $mode == '01' ){
		list($tid) = pmysql_fetch("SELECT mtrsno FROM tbltaxsavelist where ordercode='{$orderid}'", get_db_conn());
		if($shopCode){
			//Header("Location:https://pg.nicepay.co.kr/issue/IssueLoader.jsp?TID=".$tid."&type=1");
			echo "<script>			
			var status = \"toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=414,height=622\";
			window.open(\"https://pg.nicepay.co.kr/issue/IssueLoader.jsp?TID={$tid}&type=1\",\"popupIssue\",status);
			self.close();
			</script>
			";
			exit;
		}else{
			echo "<script>alert('현금영수증 처리가 진행중입니다.');window.close();</script>";
			exit;
		}
	} else if( $mode == '02' ){
		$sql = "SELECT ordercode, trans_code, price FROM tblpcardlog WHERE ordercode ='".$orderid."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_row( $result );
		pmysql_free_result( $result );
		if( $row ){
			Header("Location:https://pg.nicepay.co.kr/issue/IssueLoader.jsp?type=0&TID=".$row[1]."&order_no=".$row[0]."&trade_mony=".$row[2]);
		} else {
			echo "<script>alert('영수증 처리가 진행중입니다.');window.close();</script>";
			exit;
		}
	}
?>