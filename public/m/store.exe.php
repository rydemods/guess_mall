<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

	$search_w		= $_POST["search_w"];
	$list_num		= $_POST["list_num"];
	$page_num	= $_POST["page_num"];

	if(!$list_num) $list_num = '5';
	if(!$page_num) $page_num = '1';
	//$page_num = '1';
	$list_num = '6';
	$where	= "";

	if($search_w != ''){
		$where .= "AND title LIKE '%".iconv('utf-8','euc-kr',$search_w)."%' ";
	}

	$sql_t="select count(*) as cnt FROM tblboard WHERE board='offlinestore' AND deleted = '0' ".$where;
	$result_t = pmysql_query($sql_t,get_db_conn());
	$row_t = pmysql_fetch_object($result_t);
	$total = $row_t->cnt;

	$total_page	= ceil($total/ $list_num);
	$JSON	= "";
	if ($page_num > $total_page) {
		$JSON .= "noRecord";
	} else {

		$offset	= $list_num * ($page_num - 1);	 
		$sql  = "select * FROM tblboard WHERE board='offlinestore' AND deleted = '0' ".$where;
		$where .= "ORDER BY title";
		$sql .= " LIMIT $list_num OFFSET $offset";

		$sql_off = pmysql_query($sql);
		$i = ($total - $offset);
		$s	=1;
		$JSON .= "[ ";
		while($res=pmysql_fetch_array($sql_off)){
			$ii	=$i--;
			$JSON .= "{";
			$JSON .= "\"number\": \"".$ii."\", " ;
			$JSON .= "\"storeName\": \"".iconv('euc-kr','utf-8',$res['title'])."\", " ;
			$JSON .= "\"storeAddress\": \"".iconv('euc-kr','utf-8',$res['storeaddress'])."\", " ;
			$JSON .= "\"storeTel\": \"".$res['storetel']."\", " ;
			$JSON .= "\"storeXY\": \"".$res['etc']."\" " ;
			$JSON .= "}";


			if(($ii > 1) && ($s < $list_num )) 
			{
				$JSON .= ", \n";
			}
			
			$s++;
			//echo "no:".$i--."/$total>".$res['title']."<br>";
		}
		$JSON .= "]\n";		
	}

	// 결과가 없을때를 처리한다.
	if(!$JSON || $JSON == '') {
		echo ("noRecord");
		exit;
	}
	Header("Cache-Control:no-cache");
	Header("Pragma: no-cache");
	header('Content-Type: application/json; charset=utf-8');
	echo($JSON);
	exit;
?>