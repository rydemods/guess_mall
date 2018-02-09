<?
//print_r($_GET);
//print_r($_POST);

$sql = "SELECT  count(a.*) 
        From    tblwishlist a 
        JOIN 	tblproduct b on a.productcode = b.productcode 
        WHERE	a.id = '".$mem_id."' 
        And	    b.display = 'Y' 
        ";
//print_r($sql);
$paging = new newPaging($sql,10,5,'GoPageWish');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;


########### 위시리스트 구하기
$sql = "SELECT  a.productcode, a.wish_idx, a.date, b.productname, b.sellprice, b.tinyimage 
        From    tblwishlist a 
        JOIN 	tblproduct b on a.productcode = b.productcode 
        WHERE	a.id = '".$mem_id."' 
        And	    b.display = 'Y' 
        Order by a.date desc
        ";
//print_r($sql);
$sql = $paging->getSql($sql);
$ret_board = pmysql_query($sql, get_db_conn());
$cnt_board = pmysql_num_rows($ret_board);
?>
<script type="text/javascript">
<!--

function GoPageWish(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

//-->
</script>

			<div class="contentsBody">

				<h3 class="table-title">관심상품 조회</h3>

				<table class="th-top">
					<caption>관심상품 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:auto"><col style="width:120px"><col style="width:120px">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">상품정보</th>
							<th scope="row">상품금액</th>
							<th scope="row">담은날짜</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_board > 0) {
                    $cnt=0;
                    while($row_board = pmysql_fetch_object($ret_board)) {

                        $number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

                        $date = substr($row_board->date,0,4)."-".substr($row_board->date,4,2)."-".substr($row_board->date,6,2);
                        $productname = $row_board->productname?strcutMbDot($row_board->productname, 35):"-";
                        $tinyimage = getProductImage($Dir.DataDir.'shopimages/product/',$row_board->tinyimage);
                        //echo "img = ".$tinyimage;
?>
						<tr>
							<td><?=$number?></td>
							<td class="ta-l">
                                <A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row_board->productcode?>" target="_blank"><img src="<?=$tinyimage?>" style='max-width:80px' border=0>&nbsp;<?=$productname?></a>
                            </td>
							<td class="ta-r"><?=number_format($row_board->sellprice)?></a></td>
							<td><?=$date?></td>
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
							<td colspan="7" class="ta-c">관심상품정보가 없습니다.</td>
						</tr>
<?
                }
pmysql_free_result($ret_board);
?>
					</tbody>
				</table>

                <form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="wish">
                <input type=hidden name=type>
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                </form>

				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<!-- <dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd> -->
				</dl>


			</div><!-- //.contentsBody -->