			<div class="contentsBody">

				<h3 class="table-title">기본정보 <a href="javascript:go_menu('mem_list');" class="btn-line">더보기</a></h3>
				<table class="th-left">
					<caption>회원 기본정보 출력</caption>
					<colgroup>
						<col style="width:18%"><col style="width:32%"><col style="width:18%"><col style="width:32%">
					</colgroup>
					<tbody>
						<tr>
							<th scope="col">아이디</th>
							<td><?=$mem_id?></td>
							<th scope="col">회원등급</th>
							<td><?=$mem_group_name?></td>
						</tr>
						<tr>
							<th scope="col">이름</th>
							<td><?=$mem_name?></td>
							<th scope="col">휴대전화</th>
							<td><?=$mem_hp?></td>
						</tr>
						<tr>
							<th scope="col">회원구분</th>
							<td><?=$mem_staff_yn?></td>
							<th scope="col">인증수단</th>
							<td><?=$mem_auth_type?></td>
						</tr>
						<tr>
							<th scope="col">통합포인트</th>
							<td><?=number_format($mem_reserve)?> P</td>
							<th scope="col">E포인트</th>
							<td><?=number_format($mem_act_point)?> P</td>
						</tr>
						<tr class="hide">
							<th scope="col">총임직원적립금</th>
							<td><?=number_format($staff_reserve)?></td>
							<th scope="col">총적립금</th>
							<td><?=number_format($mem_reserve)?></td>
						</tr>
						<tr>
							<th scope="col">이메일</th>
							<td><?=$mem_email?></td>
							<th scope="col">맴버아이디</th>
							<td><?=$erp_shopid?></td>
						</tr>
						<tr>
							<th scope="col">주소</th>
							<td colspan="3"><?=$mem_home_addr?></td>
						</tr>
					</tbody>
				</table>

<?
########### 주문정보 구하기
//$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

$sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
                min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
                min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
        FROM    tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender 
        WHERE   1=1 
        AND     a.id = '".$mem_id."' 
        GROUP BY a.ordercode 
        ORDER BY a.ordercode DESC 
        LIMIT 2 OFFSET 0
        ";
//        AND     b.option_type = 0 
$ret_order = pmysql_query($sql, get_db_conn());
$cnt_ord = pmysql_num_rows($ret_order);
?>
				<h3 class="table-title mt-50">주문정보 <a href="javascript:go_menu('order');" class="btn-line">자세히</a><!-- <a href="#" class="icon-question"></a> --></h3>
				<table class="th-top">
					<caption>주문정보 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto"><col style="width:75px"><col style="width:75px"><col style="width:75px">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">주문일</th>
							<th scope="row">주문번호</th>
							<th scope="row">상품명</th>
							<th scope="row">실결제금액</th>
							<th scope="row">결제수단</th>
							<th scope="row">처리단계</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_ord > 0) {
                    while($row_ord = pmysql_fetch_object($ret_order)) {

                        $date = substr($row_ord->ordercode,0,4)."/".substr($row_ord->ordercode,4,2)."/".substr($row_ord->ordercode,6,2)." (".substr($row_ord->ordercode,8,2).":".substr($row_ord->ordercode,10,2).")";
                        if($row_ord->prod_cnt > 1) $productname = strcutMbDot($row_ord->productname, 35)." 외 ".($row_ord->prod_cnt-1)."건";
                        else $productname = strcutMbDot($row_ord->productname, 35);
?>
						<tr>
							<td><?=$cnt_ord?></td>
							<td><?=$date?></td>
							<td><A HREF="javascript:OrderDetailView('<?=$row_ord->ordercode?>')"><?=$row_ord->ordercode?></A></td>
							<td><?=$productname?></td>
							<td><?=number_format($row_ord->price-$row_ord->dc_price-$row_ord->reserve+$row_ord->deli_price)?></td>
							<td><?=$arpm[$row_ord->paymethod[0]]?></td>
							<td><?=$o_step[$row_ord->oi_step1][$row_ord->oi_step2]?></td>
						</tr>
<?
                        $cnt_ord--;
                    }
                } else {
?>
						<tr>
							<td colspan="7" class="ta-c">주문 내역이 없습니다.</td>
						</tr>
<?
                }
pmysql_free_result($ret_order);
?>
					</tbody>
				</table>

<?
########### 메모 구하기
$sql = "SELECT  id, date, memo, writer 
        FROM    tblmemo 
        WHERE   1=1 
        AND     id = '".$mem_id."' 
        ORDER BY date DESC 
        LIMIT 2 OFFSET 0
        ";
$ret_memo = pmysql_query($sql, get_db_conn());
$cnt_memo = pmysql_num_rows($ret_memo);
?>
				<h3 class="table-title mt-50">회원메모 <a href="javascript:go_menu('mem_memo');" class="btn-line">더보기</a></h3>
				<table class="th-top">
					<caption>회원메모 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto">
					</colgroup>
					<thead>
						<tr>
							<th scope="row" colspan="4" class="ta-r"><button onClick="javascript:MemberMemo2('<?=$mem_id?>')" class="btn-function on" type="button"><span>메모작성</span></button></th>
						</tr>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">작성일</th>
							<th scope="row">작성자</th>
							<th scope="row">내용</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_memo > 0) {
                    while($row_memo = pmysql_fetch_object($ret_memo)) {

                        $date = substr($row_memo->date,0,4)."/".substr($row_memo->date,4,2)."/".substr($row_memo->date,6,2)." (".substr($row_memo->date,8,2).":".substr($row_memo->date,10,2).")";
                        $memo = strcutMbDot($row_memo->memo, 35);
?>
						<tr>
							<td><?=$cnt_memo?></td>
							<td><?=$date?></td>
							<td><?=$row_memo->writer?></td>
							<td class="ta-l"><?=$memo?></td>
						</tr>
<?
                        $cnt_memo--;
                    }
                } else {
?>
						<tr>
							<td colspan="4" class="ta-c">회원메모 내역이 없습니다.</td>
						</tr>
<?
                }
pmysql_free_result($ret_memo);
?>
					</tbody>
				</table>

<?
########### 최근게시글 구하기
$sql = "select a.*, b.productname 
        from 
        (
            select  bd.board, bd.title, bd.mem_id, to_char(to_timestamp(bd.writetime), 'YYYYMMDDHH24MISS') as regdt, 0 as marks, p.productcode  
            from    tblboard bd 
            join    tblproduct p on bd.pridx = p.pridx 
            where   bd.board = 'qna' and bd.mem_id = '".$mem_id."' 
            union all
            select  '1:1' as board, subject as title, id as mem_id, date as regdt, 0 as marks, productcode from tblpersonal where id = '".$mem_id."' 
            union all
            select  'review' as board, subject as title, id as mem_id, date as regdt, marks, productcode from tblproductreview where id = '".$mem_id."' 
        ) a 
        left join tblproduct b on a.productcode = b.productcode 
        order by a.regdt desc 
        limit 2
        ";
$ret_board = pmysql_query($sql, get_db_conn());
$cnt_board = pmysql_num_rows($ret_board);
?>
				<h3 class="table-title mt-50">최근게시글 <a href="javascript:go_menu('board');" class="btn-line">더보기</a></h3>
				<table class="th-top">
					<caption>최근게시글 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:auto"><col style="width:auto"><col style="width:120px"><col style="width:60px">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">게시판</th>
							<th scope="row">상품정보</th>
							<th scope="row">제목</th>
							<!-- <th scope="row">작성자</th> -->
							<th scope="row">작성일</th>
							<th scope="row">평점</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_board > 0) {
                    while($row_board = pmysql_fetch_object($ret_board)) {

                        $date = substr($row_board->regdt,0,4)."/".substr($row_board->regdt,4,2)."/".substr($row_board->regdt,6,2)." (".substr($row_board->regdt,8,2).":".substr($row_board->regdt,10,2).")";
                        $title = strcutMbDot($row_board->title, 35);
?>
						<tr>
							<td><?=$cnt_board?></td>
							<td><?=$row_board->board?></td>
							<td class="ta-l"><?=$row_board->productname?$row_board->productname:"-"?></td>
							<td class="ta-l"><?=$title?></td>
							<!-- <td><?=$row_board->mem_id?></td> -->
							<td><?=$date?></td>
							<td><?=$row_board->marks?></td>
						</tr>
<?
                        $cnt_board--;
                    }
                } else {
?>
						<tr>
							<td colspan="7" class="ta-c">최근게시글이 없습니다.</td>
						</tr>
<?
                }
pmysql_free_result($ret_board);
?>
					</tbody>
				</table>


				<!-- <dl class="help-attention mt-50">
					<dt>도움말</dt>
					<dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd>
				</dl> -->


			</div><!-- //.contentsBody -->