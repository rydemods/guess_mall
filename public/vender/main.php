<?php
/********************************************************************* 
// 파 일 명		: vender_main.php 
// 설     명		: 입점업체 관리자모드 메인
// 상세설명	: 입점업체 관리자모드의 메인화면
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
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
	include_once($Dir."lib/venderlib.php");
	include("access.php");
#---------------------------------------------------------------
# 기본 날짜를 설정한다.
#---------------------------------------------------------------
$curdate = date("Ymd");
$curdate_1 = date("Ymd",strtotime('-1 day'));

#---------------------------------------------------------------
# 메인에 보여질 쿼리들이만 다시 정리해야함
# 주문접수를 제외한 쿼리로 변경 START (2016.05.18 - 김재수)
#---------------------------------------------------------------
	
	$sql = "SELECT ";
	//오늘 주문건수 및 주문금액
	/* 입금일이 오늘이고 주문상품의 상태가 결제완료, 배송준비, 배송중, 배송완료인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".$curdate."%') AND (b.op_step IN ('1','2','3','4')) THEN a.ordercode ELSE NULL END)) as totordcnt, ";
	/* 입금일이 오늘이고 주문상품의 상태가 결제완료, 배송준비, 배송중, 배송완료인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".$curdate."%') AND (b.op_step IN ('1','2','3','4')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE 0 END) as totordprice, ";

	//오늘 미배송 건수 및 미배송건 금액 (배송중, 배송완료 제외)
	/* 입금일이 오늘이고 주문상품의 상태가 결제완료, 배송준비인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".$curdate."%') AND (b.op_step IN ('1','2')) THEN a.ordercode ELSE NULL END)) as totdelaycnt, ";
	/* 입금일이 오늘이고 주문상품의 상태가 결제완료, 배송준비인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".$curdate."%') AND (b.op_step IN ('1','2')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE 0 END) as totdelayprice, ";

	//1일전 주문건수 및 주문금액
	/* 입금일이 어제이고 주문상품의 상태가 결제완료, 배송준비인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".$curdate_1."%') AND (b.op_step IN ('1','2','3','4')) THEN a.ordercode ELSE NULL END)) as totordcnt1, ";
	/* 입금일이 어제이고 주문상품의 상태가 결제완료, 배송준비인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".$curdate_1."%') AND (b.op_step IN ('1','2','3','4')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE NULL END) as totordprice1, ";

	//1일전 미배송 건수 및 미배송건 금액 (배송중, 배송완료 제외)
	/* 입금일이 어제이고 주문상품의 상태가 결제완료, 배송준비인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".$curdate_1."%') AND (b.op_step IN ('1','2')) THEN a.ordercode ELSE NULL END)) as totdelaycnt1, ";
	/* 입금일이 어제이고 주문상품의 상태가 결제완료, 배송준비인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".$curdate_1."%') AND (b.op_step IN ('1','2')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE 0 END) as totdelayprice1, ";

	//이달 주문건수 및 매출
	/* 입금일이 이번달이고 주문상품의 상태가 결제완료, 배송준비, 배송중, 배송완료인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".substr($curdate,0,6)."%') AND (b.op_step IN ('1','2','3','4')) THEN a.ordercode ELSE NULL END)) as totmonordcnt, ";
	/* 입금일이 이번달이고 주문상품의 상태가 배송중, 배송완료인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".substr($curdate,0,6)."%') AND (b.op_step IN ('3','4')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE 0 END) as totmonordprice, ";

	//이달 미배송 건수 및 미배송건 금액
	/* 입금일이 이번달이고 주문상품의 상태가 결제완료, 배송준비인 주문건수 */
	$sql.= "COUNT(DISTINCT(CASE WHEN (a.bank_date LIKE '".substr($curdate,0,6)."%') AND (b.op_step IN ('1','2')) THEN a.ordercode ELSE NULL END)) as totdelaycnt2, ";
	/* 입금일이 이번달이고 주문상품의 상태가 결제완료, 배송준비인 주문금액 */
	$sql.= "SUM(CASE WHEN (a.bank_date LIKE '".substr($curdate,0,6)."%') AND (b.op_step IN ('1','2')) THEN ((b.price+b.option_price)*b.option_quantity)+b.deli_price ELSE 0 END) as totdelayprice2 ";

	$sql.= "FROM tblorderinfo a, tblorderproduct b WHERE b.vender='".$_VenderInfo->getVidx()."' AND a.ordercode=b.ordercode ";
	if(substr($curdate,0,6)!=substr($curdate_1,0,6)) {
		$sql.="AND (a.bank_date LIKE '".substr($curdate,0,6)."%' OR a.bank_date LIKE '".$curdate_1."%') ";
	} else {
		$sql.="AND a.bank_date LIKE '".substr($curdate,0,6)."%' ";
	}
	$sql.= "AND a.oi_step1 !='0' AND a.oi_step2 < 40 AND b.op_step IN ('1','2','3','4') ";
#---------------------------------------------------------------
# 주문접수를 제외한 쿼리로 변경 END (2016.05.18 - 김재수)
#---------------------------------------------------------------

	$filename=$_VenderInfo->getVidx().".admin.order.cache";

	//echo $sql;

	get_db_cache($sql, $resval, $filename, 30);
	$row=$resval[0];

	$totordcnt=(int)$row->totordcnt;			//오늘 주문건수
	$totordprice=(int)$row->totordprice;		//오늘 주문금액
	$totdelaycnt=(int)$row->totdelaycnt;		//오늘 미배송건수
	$totdelayprice=(int)$row->totdelayprice;	//오늘 미배송금액

	$totordcnt1=(int)$row->totordcnt1;			//1일전 주문건수
	$totordprice1=(int)$row->totordprice1;		//1일전 주문금액
	$totdelaycnt1=(int)$row->totdelaycnt1;		//1일전 미배송건수
	$totdelayprice1=(int)$row->totdelayprice1;	//1일전 미배송금액

	$totmonordcnt=(int)$row->totmonordcnt;		//이달의 주문건수
	$totmonordprice=(int)$row->totmonordprice;	//이달의 매출
	$totdelaycnt2=(int)$row->totdelaycnt2;		//이달의 미배송건수
	$totdelayprice2=(int)$row->totdelayprice2;	//이달의 미배송금액

	//****************************************************************************
	// 상품 QNA, 상품 리뷰 현황 시작 (2016.06.23 - 김재수 추가)
	//****************************************************************************
	//상품QNA 게시판 존재여부 확인 및 설정정보 확인
	$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
	if(strlen($prqnaboard)>0) {
		$qnaset_sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
		$qnaset_result=pmysql_query($qnaset_sql,get_db_conn());
		$qnasetup=pmysql_fetch_object($qnaset_result);
		pmysql_free_result($qnaset_result);

		$qnasetup->btype=$qnasetup->board_skin[0];
		$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
		if($qnasetup->use_hidden=="Y") $qnasetup=NULL;
	}
	
	//날짜 설정
	$curtime_s		= strtotime(date("Y-m-d")." 00:00:00");
	$curtime_e		= strtotime(date("Y-m-d")." 23:59:59");
	$curtime1_s	= strtotime(date("Y-m-d",strtotime('-1 day'))." 00:00:00");
	$curtime1_e	= strtotime(date("Y-m-d",strtotime('-1 day'))." 23:59:59");
	$curtime2_s	= strtotime(date("Y-m")."-01 00:00:00");
	$curtime2_e	= strtotime(date("Y-m")."-".date("t")." 23:59:59");
	
	// QNA 카운트를 가져온다
	$qna_sql	= "SELECT SUM(yesterday_cnt) AS yesterday_cnt, SUM(today_cnt) AS today_cnt, SUM(month_cnt) AS month_cnt FROM ( ";
	$qna_sql	.= "SELECT COUNT(*) as yesterday_cnt , 0 as today_cnt, 0 as month_cnt FROM tblboard a, (select a.*, c.c_category FROM tblproduct a left join tblproductlink c on a.productcode=c.c_productcode and c.c_category='1') b WHERE a.board='".$qnasetup->board."' AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' AND a.writetime>='{$curtime1_s}' AND a.writetime <='{$curtime1_e}' ";
	$qna_sql	.= "union SELECT 0 as yesterday_cnt , COUNT(*) as today_cnt, 0 as month_cnt FROM tblboard a, (select a.*, c.c_category FROM tblproduct a left join tblproductlink c on a.productcode=c.c_productcode and c.c_category='1') b WHERE a.board='".$qnasetup->board."' AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' AND a.writetime>='{$curtime_s}' AND a.writetime <='{$curtime_e}' ";
	$qna_sql	.= "union SELECT 0 as yesterday_cnt , 0 as today_cnt, COUNT(*) as month_cnt FROM tblboard a, (select a.*, c.c_category FROM tblproduct a left join tblproductlink c on a.productcode=c.c_productcode and c.c_category='1') b WHERE a.board='".$qnasetup->board."' AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' AND a.writetime>='{$curtime2_s}' AND a.writetime <='{$curtime2_e}' ";
	$qna_sql	.= " ) AS a";
	//exdebug($qna_sql);
	$qna_result=pmysql_query($qna_sql,get_db_conn());
	$qna_row=pmysql_fetch_object($qna_result);
	pmysql_free_result($qna_result);
	$qna_yesterday_cnt	= (int)$qna_row->yesterday_cnt;	// 어제 QNA 건수
	$qna_today_cnt			= (int)$qna_row->today_cnt;	// 오늘 QNA 건수
	$qna_month_cnt			= (int)$qna_row->month_cnt;	// 이번달 QNA 건수
	
	//날짜 설정
	$curdate_s		= date("Ymd")."000000";
	$curdate_e		= date("Ymd")."235959";
	$curdate1_s	= date("Ymd",strtotime('-1 day'))."000000";
	$curdate1_e	= date("Ymd",strtotime('-1 day'))."235959";
	$curdate2_s	= date("Ym")."01000000";
	$curdate2_e	= date("Ym").date("t")."235959";
	
	// 리뷰 카운트를 가져온다
	$review_sql	= "SELECT SUM(yesterday_cnt) AS yesterday_cnt, SUM(today_cnt) AS today_cnt, SUM(month_cnt) AS month_cnt FROM ( ";
	$review_sql	.= "SELECT COUNT(*) as yesterday_cnt , 0 as today_cnt, 0 as month_cnt FROM tblproductreview a, tblproduct b, (select * from tblproductlink where c_maincate = '1') c WHERE a.productcode=b.productcode AND b.productcode=c.c_productcode AND b.vender='".$_VenderInfo->getVidx()."' AND a.date>='{$curdate1_s}' AND a.date <='{$curdate1_e}' AND b.display='Y' ";
	$review_sql	.= "union SELECT 0 as yesterday_cnt ,  COUNT(*) as today_cnt, 0 as month_cnt FROM tblproductreview a, tblproduct b, (select * from tblproductlink where c_maincate = '1') c WHERE a.productcode=b.productcode AND b.productcode=c.c_productcode AND b.vender='".$_VenderInfo->getVidx()."' AND a.date>='{$curdate_s}' AND a.date <='{$curdate_e}' AND b.display='Y' ";
	$review_sql	.= "union SELECT 0 as yesterday_cnt , 0 as today_cnt, COUNT(*) as month_cnt FROM tblproductreview a, tblproduct b, (select * from tblproductlink where c_maincate = '1') c WHERE a.productcode=b.productcode AND b.productcode=c.c_productcode AND b.vender='".$_VenderInfo->getVidx()."' AND a.date>='{$curdate2_s}' AND a.date <='{$curdate2_e}' AND b.display='Y' ";
	$review_sql	.= " ) AS a";
	//exdebug($review_sql);
	$review_result=pmysql_query($review_sql,get_db_conn());
	$review_row=pmysql_fetch_object($review_result);
	pmysql_free_result($review_result);
	$review_yesterday_cnt	= (int)$review_row->yesterday_cnt;	// 어제 리뷰 건수
	$review_today_cnt			= (int)$review_row->today_cnt;	// 오늘 리뷰 건수
	$review_month_cnt			= (int)$review_row->month_cnt;	// 이번달 리뷰 건수

	//****************************************************************************
	// 상품 QNA, 상품 리뷰 현황 끝 (2016.06.23 - 김재수 추가)
	//****************************************************************************

	include("header.php"); // 상단부분을 불러온다. 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoNoticeView(artid) {
	url="shop_notice.php?type=view&artid="+artid;
	document.location.href=url;
}
function GoCounselView(artid) {
	url="shop_counsel.php?type=view&artid="+artid;
	document.location.href=url;
}
</script>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); // 해당 메뉴부분을 불러온다. ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0" style="table-layout:fixed">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<col width=></col>
			<col width=10></col>
			<col width=220></col>
			<tr>
				<td valign=top>
				<!-- 중앙 내용 시작 -->
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;border:1px solid #EEEEEE" bgcolor="#ffffff">
					<tr>
						<td valign=top style="padding:12,10">
						<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style="table-layout:fixed">
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<tr>
							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#FCF7FD style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> 오늘 현황 <img src=images/icon_today.gif border=0 align=absmiddle></td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=66 valign=top style="padding:5;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=60></col>
								<col width=></col>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">주문수</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totordcnt?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">주문액</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totordprice)?></font>원</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">미배송</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totdelaycnt?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_qna.php">Q&A</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($qna_today_cnt)?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_review.php">리뷰</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($review_today_cnt)?></font>건</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>

							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#FCF7FD style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> 어제 현황</td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=66 valign=top style="padding:5;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=60></col>
								<col width=></col>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">주문수</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totordcnt1?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">주문액</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totordprice1)?></font>원</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">미배송</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totdelaycnt1?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_qna.php">Q&A</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($qna_yesterday_cnt)?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_review.php">리뷰</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($review_yesterday_cnt)?></font>건</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>

							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#FCF7FD style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> 이달 현황</td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=66 valign=top style="padding:5;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=60></col>
								<col width=></col>
								<tr>
									<td style="padding-left:5"><A HREF="sellstat_list_v2.php">주문수</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totmonordcnt?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="sellstat_list_v2.php">매출액</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totmonordprice)?></font>원</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_list.php">미배송</A></td>
									<td><font class=verdana style="font-size:8pt"><?=$totdelaycnt2?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_qna.php">Q&A</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($qna_month_cnt)?></font>건</td>
								</tr>
								<tr>
									<td style="padding-left:5"><A HREF="order_review.php">리뷰</A></td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($review_month_cnt)?></font>건</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
				<td height=20></td>
				<td valign=top>
<?
					$sql = "SELECT * FROM tblvenderstorecount WHERE vender='".$_VenderInfo->getVidx()."' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					pmysql_free_result($result);
					$prdt_allcnt=$row->prdt_allcnt;
					$prdt_cnt=$row->prdt_cnt;
					$cust_cnt=$row->cust_cnt;
					$count_total=$row->count_total;
					$count_today=0;

					$period_0 = date("Ymd");
					$period_1 = date("Ymd",time()-(60*60*24*1));
					$period_2 = date("Ymd",time()-(60*60*24*2));
					$period_3 = date("Ymd",time()-(60*60*24*3));
					$period_4 = date("Ymd",time()-(60*60*24*4));
					$period_5 = date("Ymd",time()-(60*60*24*5));
					$period_6 = date("Ymd",time()-(60*60*24*6));
					$period_7 = date("Ymd",time()-(60*60*24*7));
					$visit[$period_1]=0;
					$visit[$period_2]=0;
					$visit[$period_3]=0;
					$visit[$period_4]=0;
					$visit[$period_5]=0;
					$visit[$period_6]=0;
					$visit[$period_7]=0;
					$sql = "SELECT date,cnt FROM tblvenderstorevisit ";
					$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND date<='".$period_0."' AND date >='".$period_7."' ";
					$result=pmysql_query($sql,get_db_conn());
					$sumvisit=0;
					while($row=pmysql_fetch_object($result)) {
						if($row->date==$period_0) {
							$count_today=$row->cnt;
						} else {
							$sumvisit=$sumvisit+$row->cnt;
							$visit[$row->date]=$row->cnt;
						}
					}
					pmysql_free_result($result);
?>
				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;border:1px solid #EEEEEE" bgcolor="#ffffff">
					<tr>
						<td valign=top style="padding:12,10">
						<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style="table-layout:fixed">
						<col width=></col>
						<tr>
							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#FCF7FD style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> 내 판매상품 현황</td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=66 valign=top style="padding:5;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=120></col>
								<col width=></col>
								<tr>
									<td style="padding-left:5">상품등록 제한</td>
									<td><?=($_venderdata->product_max>0?"<font class=verdana style=\"font-size:8pt\"><B>".$_venderdata->product_max."</B></font> 개":"<B>무제한</B>")?></td>
								</tr>
								<tr>
									<td style="padding-left:5">등록 상품(판매중)</td>
									<td><font class=verdana style="font-size:8pt"><B><?=$prdt_allcnt?></B></font> 개</td>
								</tr>
								<tr>
									<td style="padding-left:5"><font color=#737373>진열중/진열안함</font></td>
									<td><font class=verdana style="font-size:8pt"><B><?=$prdt_cnt?></B>개/<font class=verdana style="font-size:8pt"><B><?=$prdt_allcnt?></B>개</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>						
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>				
				</td>
			</tr>
			<tr>
				<td height=20 colspan=3></td>
			</tr>
			<tr>			
				<td valign=top colspan=3>	
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;border:1px solid #EEEEEE" bgcolor="#ffffff">
					<tr>
						<td valign=top style="padding:12,10">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td valign=top bgcolor=#FEFCDA style="padding:7">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr><td height=7></td></tr>
							<tr>
								<td><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> 주요기능 바로가기</td>
							</tr>
							<tr><td height=5></td></tr>
							<tr>
								<td bgcolor=#FFFFFF style="padding:10,10;border:1px solid #FFCC00">
								<table border=0 cellpadding=10 cellspacing=0 width=100%>
								<tr>
									<td class="font_size">
									<A HREF="delivery_info.php">배송관련기능설정</A><img src=images/main_center_quick_sel.gif>
									<A HREF="product_deliinfo.php">배송/교환/환불정보 노출</A><img src=images/main_center_quick_sel.gif>
									<A HREF="product_register.php">상품 신규등록</A><img src=images/main_center_quick_sel.gif>
									<A HREF="product_myprd.php">내 상품 관리</A><img src=images/main_center_quick_sel.gif>
									<A HREF="order_list.php">주문조회/배송</A><img src=images/main_center_quick_sel.gif>
									<A HREF="sellstat_list_v2.php">판매상품 정산조회</A>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
						</tr>	
						</table>
						</td>
					</tr>	
					</table>
					</td>
				</tr>	
				</table>
				</td>
			</tr>
			<tr>
				<td height=10 colspan=3></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<?=$onload?>
<?php include("copyright.php"); // 하단부분을 불러온다. ?>
