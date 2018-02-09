<?php
$Dir="../";
/*
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");
*/
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."lib/coupon.class.php");
include_once($Dir."conf/config.point.new.php");


$vdate = date("Ymd");

$gubun = $_REQUEST[gubun];
/* 댓글수정 */
if($gubun=="comment_update"){

	$usql = "update tblboardcomment_promo set comment ='".$_REQUEST[comment]."' where c_mem_id='".$_ShopInfo->getMemid()."' and num ='".$_REQUEST[comment_num]."' ";
	//echo $usql;
	pmysql_query($usql);
	
}
/* 댓글삭제 */
if($gubun=="comment_delete"){

	$usql = "delete from tblboardcomment_promo where c_mem_id='".$_ShopInfo->getMemid()."' and num ='".$_REQUEST[comment_num]."' ";
	
	//echo $usql;
	pmysql_query($usql);
	
}

/* 포토수정 */
if($gubun=="photo_update"){

	$addQury = "";

	if($_REQUEST[user_img1]!=""){
		$addQury .= ",vfilename = '".$_REQUEST[user_img1]."' ";
	}else if($_REQUEST[delchk1]=="Y"){
		$addQury .= ",vfilename = '' ";
	}

	if($_REQUEST[user_img2]!=""){
		$addQury .= ",vfilename2 = '".$_REQUEST[user_img2]."' ";
	}else if($_REQUEST[delchk2]=="Y"){
		$addQury .= ",vfilename2 = '' ";
	}

	if($_REQUEST[user_img3]!=""){
		$addQury .= ",vfilename3 = '".$_REQUEST[user_img3]."' ";
	}else if($_REQUEST[delchk3]=="Y"){
		$addQury .= ",vfilename3 = '' ";
	}

	if($_REQUEST[user_img4]!=""){
		$addQury .= ",vfilename4 = '".$_REQUEST[user_img4]."' ";
	}else if($_REQUEST[delchk4]=="Y"){
		$addQury .= ",vfilename4 = '' ";
	}


	$usql = "update tblboard_promo set 	content ='".$_REQUEST[content]."' ,title = '".$_REQUEST[title]."'  ".$addQury."
	where mem_id='".$_ShopInfo->getMemid()."' and promo_idx ='".$_REQUEST[parent]."' and num= '".$_REQUEST[board_num]."' ";
	//echo $usql;
	pmysql_query($usql);
	
}

/* 포토글삭제 */
if($gubun=="photo_delete"){

	$usql = "delete from tblboard_promo where mem_id='".$_ShopInfo->getMemid()."' and num ='".$_REQUEST[comment_num]."' ";
	
	//echo $usql;
	pmysql_query($usql);
	
}

/* 포토삭제 */
if($gubun=="photonly_delete"){

	$usql = "update tblboard_promo set ".$_REQUEST[imgqry]." where mem_id='".$_ShopInfo->getMemid()."' and num ='".$_REQUEST[comment_num]."' ";
	
	//echo $usql;
	pmysql_query($usql);
	
}

/* 리뷰등록 */
if($gubun=="review_insert"){
	
	if($_REQUEST[cm]==""){
		$_REQUEST[cm] = 0;
	}
	if($_REQUEST[kg]==""){
		$_REQUEST[kg] = 0;
	}

	$file_exists_count	= 0;
	if ($_REQUEST[user_img1]) 
		$file_exists_count	= $file_exists_count + 1;
	if ($_REQUEST[user_img2]) 
		$file_exists_count	= $file_exists_count + 1;
	if ($_REQUEST[user_img3]) 
		$file_exists_count	= $file_exists_count + 1;
	if ($_REQUEST[user_img4]) 
		$file_exists_count	= $file_exists_count + 1;


	// 업로드한 파일이 하나 이상이면 '포토리뷰'
	$review_type	= "0";
	if ( $file_exists_count >= 1 ) {
		$review_type    = "1";
	}

	$usql = "insert into tblproductreview 
			(productcode, id, name, date, content, 
			subject, ordercode,  productorder_idx, size, color, 
			quality, deli, cm, kg, type,  
			upfile, upfile2, upfile3, upfile4
			)values(
			'".$_REQUEST[productcode]."', '".$_ShopInfo->getMemid()."', '".$_ShopInfo->getMemname()."', to_char(current_timestamp, 'yyyymmddHH24MISS'), '".$_REQUEST[content]."', 
			'".$_REQUEST[subject]."', '".$_REQUEST[ordercode]."',  '".$_REQUEST[productorder_idx]."', '".$_REQUEST[size]."', '".$_REQUEST[color]."', 
			'".$_REQUEST[quality]."', '".$_REQUEST[deli]."', '".$_REQUEST[cm]."', '".$_REQUEST[kg]."', '".$review_type."',
			'".$_REQUEST[user_img1]."', '".$_REQUEST[user_img2]."', '".$_REQUEST[user_img3]."', '".$_REQUEST[user_img4]."'
			
			)";
	
	//echo $usql;
	pmysql_query($usql);

	$productcode			= $_REQUEST[productcode];
	$ordercode				= $_REQUEST[ordercode];
	$productorder_idx	= $_REQUEST[productorder_idx];
	$review_content		= $_REQUEST[content];

	$sql  = "UPDATE tblproduct ";
	$sql .= "SET review_cnt = review_cnt + 1 ";
	$sql .= "WHERE productcode ='".$productcode."'";
	$result = pmysql_query($sql, get_db_conn());
	//================================================================================================
	// 포인트 지급은 실제로 해당 상품을 구입한 경우에만 지급
	// ================================================================================================
	if ( !empty($ordercode) && !empty($productorder_idx) ) {
		// 현재 상품에 대한 리뷰가 한개인 경우에만 포인트 지급
		/*
		$sql  = "SELECT num FROM tblproductreview ";
		$sql .= "WHERE productcode = '{$productcode}' AND id = '" . $_ShopInfo->getMemid() . "' ";
		*/

		// 현재 상품을 구입하고 리뷰를 처음 작성한 경우에만 포인트 지급
		$sql  = "SELECT tpr.num ";
		$sql .= "FROM ";
		$sql .= "   tblproductreview tpr LEFT JOIN tblorderproduct top ON tpr.productcode = top.productcode AND tpr.productorder_idx = top.idx ";
		$sql .= "WHERE ";
		$sql .= "   tpr.productcode = '{$productcode}' AND tpr.id = '" . $_ShopInfo->getMemid() . "' AND top.idx='{$productorder_idx}' ";

		$row_count = pmysql_num_rows(pmysql_query($sql));
		list($review_num) = pmysql_fetch($sql);

		if ( $row_count == 1 ) {
			if ( $review_type == "1" ) {
				// 포토리뷰
				$title = "포토리뷰 작성보상";
				$point = $pointSet_new['poto_point'];
			} else {
				// 텍스트리뷰
				$title = "텍스트리뷰 작성보상";
				if(strlen($review_content)<"100") $point = $pointSet_new['protext_down_point'];
				else  $point = $pointSet_new['protext_up_point'];
			}

			$result = insert_point_act($_ShopInfo->getMemid(), $point, $title, "@review", "admin_".date("YmdHis"), $review_num);
		}

		//리뷰 작성시 3회까지는 추가적립을 해준다.
		$th_qry="select * from tblproductreview where productcode = '{$productcode}'";
		$th_result=pmysql_query($th_qry);
		$review_count=pmysql_num_rows($th_result);
		
		if($review_count<="3") insert_point_act($_ShopInfo->getMemid(), $pointSet_new['proreview_point'], "리뷰 작성보상(3번째 이내 상품평 작성)", "@review", "admin_".date("YmdHis"), $review_num."_proreview_point");
	}
	
}

/* QNA등록 */
if($gubun=="qna_insert"){
		
	$usql = "insert into tblboard (
				board, pridx, name , email, is_secret, title, 
				writetime, ip, total_comment, content , mem_id, hp
			)values(
				'qna', '".$_REQUEST[pridx]."','".$_ShopInfo->getMemname()."','".$_REQUEST[email]."','".$_REQUEST[open_yn]."','".$_REQUEST[subject]."',
				cast(extract(epoch from current_timestamp) as integer),'".$_SERVER[REMOTE_ADDR]."', 0,'".$_REQUEST[content]."', '".$_ShopInfo->getMemid()."','".$_REQUEST[hp]."'				
			)";
			
	
	//echo $usql;
	pmysql_query($usql);
	
}


/* 룰렛이벤트 */
if($gubun=="roulette"){
	$current_date = date('Y-m-d');
	//응모여부조회
	$sql = "select count(*) cnt from tblpromo_roulett where roulette_id='".$_REQUEST[idx]."' and member_id='".$_ShopInfo->getMemid()."' and success_yn = 'Y' limit 1 ";

	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)) {
		$chkcnt = $row->cnt;
	}
	
	if($chkcnt==0){
		$roulette = array();
		$keyArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
		$index = $_REQUEST[index];
		$sql = "select * from tblpromo where event_type='5' and idx='".$_REQUEST[idx]."' ";
		$result = pmysql_query($sql,get_db_conn());
		$ii=0;
		while ($row = pmysql_fetch_object($result)) {	
			foreach ($row as $key => $value) {
				$roulette[$ii]->$key	= $value;
			}
			$ii+=1;
		}	
		pmysql_free_result($result);
		
		
		$ticket_ = $roulette[0]->roulette_ticket_group;
		$ticketArr = explode(",", $ticket_);
		
		//$ticket = $ticketArr[1];//발행티켓
		//$range = substr($ticket, 0,1);
		$ticket = $keyArr[$index-1].($index-1);//발행티켓
		$range = $keyArr[$index-1];
		
		if($range=="A")	$range_num = 1;	
		if($range=="B")	$range_num = 2;
		if($range=="C")	$range_num = 3;
		if($range=="D")	$range_num = 4;
		if($range=="E")	$range_num = 5;
		if($range=="F")	$range_num = 6;
		if($range=="G")	$range_num = 7;
		if($range=="H")	$range_num = 8;
		
		$range_sum = 45 * $range_num;
		
		$stopAngle = mt_rand(($range_sum-44), $range_sum);
			
	
		
		
		$segment = $roulette[0]->roulette_segment;
		
		
		$segmentArr = explode(",", $segment);
		
		for($i=1; $i<=8; $i++){
			
			if($range_num==$i){
				$segmentArrSub = explode(":", $segmentArr[$i-1]);
				
				//룰렛당첨테이블insert
				$usql = "insert into tblpromo_roulett (roulette_id, member_id, ticket, ticket_comment, regdate) values ('".$_REQUEST[idx]."','".$_ShopInfo->getMemid()."', '".$ticket."', '".$segmentArrSub[0].",".$segmentArrSub[2]."' , current_timestamp)";
				pmysql_query($usql);
				
				//룰렛티켓발송갯수 업데이트
				$usql = "update tblpromo set roulette_order_num = roulette_order_num+1 where  event_type='5' and idx = '".$_REQUEST[idx]."'";
				pmysql_query($usql);
				
				//포인트자동지급
//				if($segmentArrSub[2]!=""){ 
//					insert_point_act($_ShopInfo->getMemid(), $segmentArrSub[2], '룰렛 참여 포인트', '@roulette', $_ShopInfo->getMemid(), date("Ymd"), 0);	
//				}
				
			}	
		}	
		
		echo $stopAngle; //룰렛각도리턴
		
		
	}else{
		echo "repeat";
	}
	
		
}

/* 기간 할인 가격 가져오기 */
if($gubun=="timesale_price"){
	echo timesale_price($_REQUEST[productcode]);
	
	
}


/* 상품정보가져오기 */
if($gubun=="product_coupon"){
	$product_data = getProductCouponInfo($_REQUEST[productcode]);

	echo json_encode($product_data);

}


?>