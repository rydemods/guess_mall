<?
//exit;
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."alimtalk/alimtalk.class.php");
	
	// 주문번호 : 
	/* 
	 * 2017042710551811125A | 2017042711004121549A | 2017042710484676711A (일반 주문 테스트 )
	 * 2017032112004549664A (실 결제정보 있음)
	 * 2016090217591888572A (배송정보)
	 * 2017040618333969835A (배송전 취소 접수)
	 * 2017041016153877235A (반품신청 )
	 * 2017041017460852443A (반품접수)
	 * 2017040618333969835A (환불접수)
	 * 2017041114172403284A (환불완료)
	 */
	
	/* 문구내용 :
	 WEB01 : 주문접수
	 WEB02 : 가상계좌 결제완료
	 WEB03 : 신용카드 결제완료
	 WEB04 : 배송중
	 WEB05 : 배송완료
	 WEB06 : 배송전 취소접수
	 WEB07 : 반품신청
	 WEB08 : 반품접수
	 WEB09 : 환불접수
	 WEB10 : 환불완료
	
	 ##################
	
	 SCC01 (재고 有) - 상품준비 ( 매장확인 )
	 SCC02 (재고 有) - 고객수령
	 SCC03 (재고 無) - RT 완료 ( 발송매장 )
	 SCC04 (재고 無) - RT 도착 ( 픽업매장 )
	 SCC05 (재고 無) - RT 상품수령
	 SCC06 (재고 無) - 낙찰완료
	 SCC07 (재고 無) - 배송중
	 SCC08 (재고 無) - 상품수령
	 SCC09 (재고 無) - 배송중
	 SCC10 (재고 無) - 상품수령
	 SCC11 (재고 無) - 결제완료
	 SCC12 (재고 無) - 고객수령
	 */

	//$ordercode = "2018011010485610209A";		// 주문코드
	//$ordertype = "WEB16";						// 문구코드
	
	$idx = "40870";								// 상품 idx
//	$oc_no = "33017";								// 취소번호
	
	# 배열로 담아서 직접 전송 하는 방법
	# ordercode, template, cellphone
/*
	$code='WEB15';
 	$array = array("template"=>"{$code}", 
 					"cellphone"=>"01028413981",
 					"order_name"=>"테스트", 
 					"order_product"=>"002002003002000056", 
 					"order_price"=>number_format(10000), 
 					"virtual_number"=>"00554157415", 
 					"brand_name"=>"테스트_", 
 					"product_code"=>"테스트_", 
					"bank_name"=>"국민은행", 
 					"order_url"=>"http://http://shinwonmall.com");
 	$json = json_encode($array);
 	$alim = new ALIM_TALK();
 	$alim->jsonData = $json;
 	$alim->makeJsonDecodeData();
 	$alim->makeAlimTalkMsg();
*/
// 	$alim2 = new ALIM_TALK();
// 	$alim2->makeAlimTalkSearchData($ordercode, "WEB16");
	
	//$alim = new ALIM_TALK();
	//$alim->makeAlimTalkSearchData($ordercode, "WEB16");
// 	$alim->makeAlimTalkSearchNewData($ordercode, 'WEB14','','');
	//$alim->makeAlimTalkSearchData($ordercode, $ordertype);						// 주문완료
	// $ordertype = "WEB05";
	// $alim->makeAlimTalkSearchData($ordercode, $ordertype, $idx, $oc_no);		// 주문배송 완료
	
//	$alim->makeAlimTalkSearchData($ordercode, $ordertype, $idx, $oc_no);

	$sub_sql = "
        				SELECT name,owner_ph FROM tblstore WHERE store_code = 'G9038F'
        				";
	$sub_result = pmysql_query( $sub_sql, get_db_conn() );
	while( $sub_row = pmysql_fetch_object( $sub_result ) ){
			
		if($sub_row->owner_ph != '') {
			$t_phone2 = $sub_row->owner_ph;
		}
	}
	
	echo $t_phone2."------".$sub_row->owner_ph;
	
	if($alim->ataDbFlag == 'succ'){
		//echo "데이터 입력 성공";
	}else if($alim->ataDbFlag == 'none'){
		//echo "데이터가 없습니다.";
	}else{
		//echo "데이터 입력 실패";
	}
?>

<html>
	<br>
	<a href="http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L">테스트1 [a 태그 href 방식]</a><br>
	<a onclick="javascript:window.open('https://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L','KCPHelp','width=500,height=380,scrollbars=auto,resizable=yes');" style="cursor:pointer" class="pl-20">테스트2 [onclick 방식]</a><br>
	<a onclick="javascript:window.open('https://pg.nicepay.co.kr/issue/IssueEscrow.jsp?Mid=shoemarkem&CoNo=1058614706','escrowHelp','width=500,height=380,scrollbars=auto,resizable=yes'); " style="cursor:pointer" class="pl-20">테스트3 [onclick 방식 - test2번과 동일함]</a>
</html>
