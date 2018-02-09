<?
//print_r($_GET);
//print_r($_POST);
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";


// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND to_char(regdt, 'YYYYMMDDHH24MISS') >= '{$search_s}' AND to_char(regdt, 'YYYYMMDDHH24MISS') <= '{$search_e}' ";
}

$sql = "Select  count(*) 
        FROM    tblmember_question 
        WHERE   counsel_type = 'p'  
        ".$qry."
        AND     id = '".$mem_id."' 
        ";
//print_r($sql);
$paging = new newPaging($sql,10,5,'GoPageBoard');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;


########### 메모 구하기
$sql = "SELECT  sno, id, counsel_id, contents, regdt, counsel_type 
        FROM    tblmember_question 
        WHERE   counsel_type = 'p'  
        ".$qry."
        AND     id = '".$mem_id."' 
        ORDER BY sno DESC 
        ";
//print_r($sql);
$sql = $paging->getSql($sql);
$ret_memo = pmysql_query($sql, get_db_conn());
$cnt_memo = pmysql_num_rows($ret_memo);
?>
<script type="text/javascript">
<!--
function searchForm() {
	//document.form1.action="order_list_all_order.php";
	document.memo_frm.submit();
}

function OnChangePeriod(val) {
    //alert(val);
	var pForm = document.memo_frm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	
    if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    }else{
	    pForm.search_start.value = '';
	    pForm.search_end.value = '';
    }
}

function GoPageBoard(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function viewBoard(counsel_type, idx) {
    //alert(board + " / " + idx);
    window.open("about:blank","call_pop","width=600,height=570,scrollbars=yes");
	document.view_frm.idx.value=idx;
    document.view_frm.counsel_type.value=counsel_type;
    document.view_frm.action = "crm_view_call_memo_pop.php";
	document.view_frm.submit();
}
//-->
</script>

			<div class="contentsBody">

                <form name="memo_frm" action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="call_memo">
				<h3 class="table-title">전화상담 메모 조회&nbsp;&nbsp;&nbsp;<button onClick="javascript:viewBoard('p', '', '<?=$mem_id?>')" class="btn-function on" type="button"><span>메모작성</span></button></h3>
				<table class="th-left">
					<caption>전화상담 메모 조회</caption>
					<colgroup>
						<col style="width:18%"><col style="width:82%">
					</colgroup>
					<tbody>

						<TR>
							<th scope="row">상담일</th>
							<td>
                                <div class="date-choice">
                                    <input class="w100" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="w100" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
                                    <button OnClick="javascript:OnChangePeriod(0);" class="btn-line" type="button"><span>오늘</span></button>
                                    <button OnClick="OnChangePeriod(1)" class="btn-line" type="button"><span>7일</span></button>
                                    <button OnClick="OnChangePeriod(2)" class="btn-line" type="button"><span>14일</span></button>
                                    <button OnClick="OnChangePeriod(3)" class="btn-line" type="button"><span>한달</span></button>
                                    <button OnClick="OnChangePeriod(4)" class="btn-line" type="button"><span>전체</span></button>
                                </div>
							</td>
						</TR>

					</tbody>
				</table>
				<div class="btn-place">
					<a href="javascript:searchForm();" class="btn-dib on">검색</a>
				</div>
                </form>


				<table class="th-top">
					<caption>전화상담 메모 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">상담일</th>
							<th scope="row">처리자</th>
							<th scope="row">내용</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_memo > 0) {
                    $cnt = 0;
                    while($row_memo = pmysql_fetch_object($ret_memo)) {

                        $number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
                        $memo = strcutMbDot($row_memo->contents, 50);
?>
						<tr>
							<td><?=$number?></td>
							<td><a href="javascript:viewBoard('p', '<?=$row_memo->sno?>');"><?=$row_memo->regdt?></a></td>
							<td><?=$row_memo->counsel_id?></td>
							<td class="ta-l"><a href="javascript:viewBoard('p', '<?=$row_memo->sno?>');"><?=$memo?></a></td>
						</tr>
<?
                        $cnt_memo--;
                        $cnt++;
                    }
?>
                        <tr>
                            <td colspan="4" class="ta-c">
                                <div id="page_navi01" style="height:'40px'">
                                    <div class="page_navi">
                                        <ul>
                                            <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
<?
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

                <form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="mem_memo">
                <input type=hidden name=type>
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                <input type=hidden name=search_start value="<?=$search_start?>">
                <input type=hidden name=search_end value="<?=$search_end?>">
                </form>

                <form name=view_frm  method=post target="call_pop">
                <input type=hidden name=idx>
                <input type=hidden name=counsel_type>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                </form>

				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<!-- <dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd> -->
				</dl>


			</div><!-- //.contentsBody -->