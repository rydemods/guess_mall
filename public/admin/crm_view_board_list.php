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
$s_board        = $_GET["s_board"];
$s_check        = $_GET["s_check"];
$search         = trim($_GET["search"]);
$reply_yn       = $_GET["reply_yn"]?$_GET["reply_yn"]:"A";
$file_yn        = $_GET["file_yn"]?$_GET["file_yn"]:"A";



$selected[s_board][$s_board]    = 'selected';
$selected[s_check][$s_check]    = 'selected';
$selected[reply_yn][$reply_yn]  = 'checked';
$selected[file_yn][$file_yn]    = 'checked';


$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";


// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND a.regdt >= '{$search_s}' AND a.regdt <= '{$search_e}' ";
}

// 게시글찾기
if($search != "") {
    if($s_check == "subject") $qry.= "AND a.title like '%".$search."%' ";
    if($s_check == "body") $qry.= "AND a.body like '%".$search."%' ";
    if($s_check == "pname") $qry.= "AND b.productname like '%".$search."%' ";
}

// 답변여부
if($reply_yn == "N") $qry.= "AND a.reply_cnt = 0 ";
if($reply_yn == "Y") $qry.= "AND a.reply_cnt > 0 ";

// 첨부파일 여부
if($file_yn == "N") $qry.= "AND a.filename == '' ";
if($file_yn == "Y") $qry.= "AND a.filename != '' ";

$union = array();
if($s_board == "personal" || $s_board == "") {
    $union[] = "select 'personal' as board, idx, subject as title, content as body, id as mem_id, date as regdt, 0 as marks, productcode, up_filename as filename, (case when re_date != '' then 1 else 0 end) as reply_cnt from tblpersonal where id = '".$mem_id."' ";
}

if($s_board == "qna" || $s_board == "") {
    $union[] = "select bd.board, num as idx, bd.title, bd.content as body, bd.mem_id, to_char(to_timestamp(bd.writetime), 'YYYYMMDDHH24MISS') as regdt, 0 as marks, p.productcode, '' as filename, total_comment as reply_cnt from tblboard bd join tblproduct p on bd.pridx = p.pridx where bd.board = 'qna' and bd.mem_id = '".$mem_id."' ";
}

if($s_board == "review" || $s_board == "") {
    $union[] = "select 'review' as board, num as idx, subject as title, content as body, id as mem_id, date as regdt, marks, productcode, upfile as filename, 0 as reply_cnt from tblproductreview where id = '".$mem_id."' ";
}

$unionall = implode(" UNION ALL ", $union);
//print_r($unionall);

$sql = "Select count(a.*) From ( 
        ".$unionall." 
        ) a 
        Left Join tblproduct b on a.productcode = b.productcode 
        Where   1=1 
        ".$qry." 
        ";
//print_r($sql);
$paging = new newPaging($sql,10,5,'GoPageBoard');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;


########### 게시글 구하기
$sql = "select a.*, b.productname 
        from 
        (
        ".$unionall." 
        ) a 
        Left Join tblproduct b on a.productcode = b.productcode 
        Where   1=1 
        ".$qry." 
        order by a.regdt desc 
        ";
//print_r($sql);
$sql = $paging->getSql($sql);
$ret_board = pmysql_query($sql, get_db_conn());
$cnt_board = pmysql_num_rows($ret_board);
?>
<script type="text/javascript">
<!--
function searchForm() {
	//document.form1.action="order_list_all_order.php";
	document.board_frm.submit();
}

function OnChangePeriod(val) {
    //alert(val);
	var pForm = document.board_frm;
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

function viewBoard(board, idx) {
    //alert(board + " / " + idx);
    window.open("about:blank","board_pop","width=600,height=570,scrollbars=yes");
	document.view_frm.idx.value=idx;

    if(board == "personal") {
        document.view_frm.action = "community_personal_pop.php"
    } else if(board == "review") {
        document.view_frm.action = "crm_view_board_review_pop.php";
    } else if(board == "qna") {
        document.view_frm.action = "crm_view_board_qna_pop.php";
    }
	document.view_frm.submit();
}
//-->
</script>

			<div class="contentsBody">

                <form name="board_frm" action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="board">
				<h3 class="table-title">게시글 조회</h3>
				<table class="th-left">
					<caption>게시글 조회</caption>
					<colgroup>
						<col style="width:18%"><col style="width:82%">
					</colgroup>
					<tbody>

						<TR>
							<th scope="row">작성일</th>
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

						<tr>
							<th scope="row"><label for="keyword01">게시판 선택</label></th>
							<td>
                                <div>
                                    <select name="s_board">
                                        <option value="">----------------------</option>
                                        <option value="personal" <?=$selected[s_board]["personal"]?>>1:1게시판</option>
                                        <option value="qna" <?=$selected[s_board]["qna"]?>>상품Q&A</option>
                                        <option value="review" <?=$selected[s_board]["review"]?>>상품REVIEW</option>
                                    </select>
                                </div>
                            </TD>
						</tr>


						<tr>
							<th scope="row"><label for="keyword01">게시글찾기</label></th>
							<td>
                                <div>
                                    <select name="s_check">
                                        <option value="subject" <?=$selected[s_check]["subject"]?>>제목</option>
                                        <option value="body" <?=$selected[s_check]["body"]?>>내용</option>
                                        <option value="pname" <?=$selected[s_check]["pname"]?>>상품명</option>
                                    </select>
                                    <input type=text name=search value="<?=$search?>" class="w400">
                                </div>
                            </TD>
						</tr>

                        <TR>
							<th scope="row">답변여부</th>
							<TD class="td_con1">
                                <input type="radio" name="reply_yn" value="A" <?=$selected[reply_yn]["A"]?>><label for="order-type01">&nbsp;전체</label></input>
                                <input type="radio" name="reply_yn" value="N" <?=$selected[reply_yn]["N"]?>><label for="order-type01">&nbsp;답변대기</label></input>
                                <input type="radio" name="reply_yn" value="Y" <?=$selected[reply_yn]["Y"]?>><label for="order-type01">&nbsp;답변완료</label></input>
                            </TD>
						</TR>

                        <TR>
							<th scope="row">첨부파일 여부</th>
							<TD class="td_con1">
                                <input type="radio" name="file_yn" value="A" <?=$selected[file_yn]["A"]?>><label for="order-type01">&nbsp;전체</label></input>
                                <input type="radio" name="file_yn" value="N" <?=$selected[file_yn]["N"]?>><label for="order-type01">&nbsp;없음</label></input>
                                <input type="radio" name="file_yn" value="Y" <?=$selected[file_yn]["Y"]?>><label for="order-type01">&nbsp;있음</label></input>
                            </TD>
						</TR>


					</tbody>
				</table>
				<div class="btn-place">
					<a href="javascript:searchForm();" class="btn-dib on">검색</a>
				</div>
                </form>


				<table class="th-top">
					<caption>최근게시글 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto"><col style="width:120px"><col style="width:60px">
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
                    $cnt=0;
                    while($row_board = pmysql_fetch_object($ret_board)) {

                        $number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

                        if($row_board->board == "personal") $board_name = "1:1 문의";
                        else if($row_board->board == "qna") $board_name = "상품 Q&A";
                        else if($row_board->board == "review") $board_name = "상품 리뷰";
                        $date = substr($row_board->regdt,0,4)."/".substr($row_board->regdt,4,2)."/".substr($row_board->regdt,6,2)." (".substr($row_board->regdt,8,2).":".substr($row_board->regdt,10,2).")";
                        $title = strcutMbDot($row_board->title, 50);
                        $productname = $row_board->productname?strcutMbDot($row_board->productname, 35):"-";
?>
						<tr>
							<td><?=$number?></td>
							<td><?=$board_name?></td>
							<td class="ta-l"><?=$productname?></td>
							<td class="ta-l"><a href="javascript:viewBoard('<?=$row_board->board?>', '<?=$row_board->idx?>');"><?=$title?></a></td>
							<!-- <td><?=$row_board->mem_id?></td> -->
							<td><?=$date?></td>
							<td><?=$row_board->marks?></td>
						</tr>
<?
                        $cnt_board--;
                        $cnt++;
                    }
?>
                        <tr>
                            <td colspan="7" class="ta-c">
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
                <input type="hidden" name="menu" value="board">
                <input type=hidden name=type>
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                <input type=hidden name=search_start value="<?=$search_start?>">
                <input type=hidden name=search_end value="<?=$search_end?>">
                <input type=hidden name=s_board value="<?=$s_board?>">
                <input type=hidden name=s_check value="<?=$s_check?>">
                <input type=hidden name=search value="<?=$search?>">
                <input type=hidden name=reply_yn value="<?=$reply_yn?>">
                <input type=hidden name=file_yn value="<?=$file_yn?>">
                </form>

                <form name=view_frm  method=post target="board_pop">
                <input type=hidden name=idx>
                </form>


				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<!-- <dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd> -->
				</dl>


			</div><!-- //.contentsBody -->