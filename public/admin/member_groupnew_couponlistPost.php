<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

//print_r($_POST);

$coupon_type = array("1"=>"상품쿠폰", "2"=>"신규가입쿠폰", "3"=>"기념일 쿠폰", "4"=>"첫구매 쿠폰", "5"=>"등급업 쿠폰", "6"=>"기타 쿠폰", "7"=>"페이퍼 쿠폰");

$block = $_POST['block'];
$gotopage = $_POST['gotopage'];
$listMode = $_POST['listMode'];
$s_keyword = $_POST['s_keyword'];
$coupon_id = $_POST['coupon_id'];   // 한 페이지에서 여러 곳에 쓰기 위해 추가(20160127 by moondding2)

if($listMode == 'gradeCoupon'){

	if ($s_keyword) $where = "AND lower(coupon_name) LIKE lower('%{$s_keyword}%') ";
	
	## jhjeong 2015-06-11
	$sql0 = "SELECT COUNT(*) as t_count FROM tblcouponinfo WHERE 1=1 $where ";
	if(!$listnum){
		$listnum = 20;
	}
	$paging = new newPaging($sql0,10,$listnum,$link='T_GoPage',$block,$gotopage);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	$sql = "SELECT * FROM tblcouponinfo WHERE 1=1 $where ORDER BY date DESC";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
    //echo $sql."<br>";
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$repProduct[] = $row;
	}
	pmysql_free_result($result);
}
?>
<div class="table_style02">
	<table width=100% cellpadding=0 cellspacing=0>
		<colgroup>
			<col width="50" />
			<col width="80" />
			<col width="" />
			<col width="120" />
			<col width="200" />
			<col width="200" />
			<col width="80" />
		</colgroup>
		<tr>
			<th>No</th>
			<th>쿠폰코드</th>
			<th>쿠폰타입</th>
			<th>쿠폰명</th>
			<th>할인</th>
			<th>유효기간</th>
			<th>등록일</th>
		</tr>
<?
	$page_numberic_type=1;
	foreach($repProduct as $repPrKey=>$row){
		$number = ($t_count-(20 * ($gotopage-1))-$cnt);

        if($row->sale_type<=2) $dan="%";
        else $dan="원";

        if($row->sale_type%2==0) $sale = "할인";
        else $sale = "적립";

        if($row->date_start>0) {
            $date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
        } else {
            $date = abs($row->date_start)."일동안";
        }

        $regdt = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
?>
		<tr>
			<td><?=$number?></td>
            <td><?=$row->coupon_code?></td>
            <td><?=$coupon_type[$row->coupon_type]?></td>
			<td height="50">
					<a href="javascript:gradeCoupon('<?=$row->coupon_name?>','<?=$row->coupon_code?>', '<?=$coupon_id?>');">
						<?=$row->coupon_name?>
					</a>
			</td>
			<td><?=number_format($row->sale_money).$dan?></td>
			<td><?=$date?></td>
			<td><?=$regdt?></td>
		</tr>
<?
		$cnt++;
	}
	if ($cnt==0) {
		$colspan='9';
		$page_numberic_type="";
		echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 쿠폰이 존재하지 않습니다.</td></tr>";
	}
?>
	</table>
</div>
<div id="page_navi01" style="height:'40px'">
	<div class="page_navi">
	<?if($page_numberic_type){?>
		<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
	<?}?>
	</div>
</div>
