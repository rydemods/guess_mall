<?php
	header("Content-Type: text/html; charset=UTF-8");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");

	//print_r($_POST);

	$block = $_POST['block'];
	$gotopage = $_POST['gotopage'];
	$listMode = $_POST['listMode'];
	$s_keyword = $_POST['s_keyword'];
	$coupon_id = $_POST['coupon_id'];   // 한 페이지에서 여러 곳에 쓰기 위해 추가(20160127 by moondding2)

	if($listMode == 'gradeCoupon'){				// 등급별 쿠폰
		$s_coupon_type	= '15';
	} else if ($listMode == 'normalCoupon') {	// 일반쿠폰
		$s_coupon_type	= '16';
	}

	if ($s_keyword) $where = "AND lower(coupon_name) LIKE lower('%{$s_keyword}%') ";

	## jhjeong 2015-06-11
	$sql0 = "SELECT COUNT(*) as t_count FROM tblcouponinfo WHERE coupon_type='{$s_coupon_type}' AND issue_status='Y' $where ";
	if(!$listnum){
		$listnum = 20;
	}
	$paging = new newPaging($sql0,10,$listnum,$link='T_GoPage',$block,$gotopage);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "SELECT * FROM tblcouponinfo WHERE coupon_type='{$s_coupon_type}' AND issue_status='Y' $where ORDER BY date DESC";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	//echo $sql."<br>";
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$repProduct[] = $row;
	}
	pmysql_free_result($result);
?>
<div class="table_style02">
	<table width=100% cellpadding=0 cellspacing=0>
		<colgroup>
			<col width="50" />
			<col width="75" />
			<col width="" />
			<col width="75" />
			<col width="130" />
			<col width="150" />
			<col width="75" />
		</colgroup>
		<tr>
			<th>No</th>
			<th>쿠폰코드</th>
			<th>쿠폰명</th>
			<th>쿠폰종류</th>
			<th>할인</th>
			<th>유효기간</th>
			<th>등록일</th>
		</tr>
<?
	$page_numberic_type=1;
	foreach($repProduct as $repPrKey=>$row){
		$number = ($t_count-(20 * ($gotopage-1))-$cnt);

		if($row->coupon_use_type == "1") $coupon_use_type = "장바구니";
		else $coupon_use_type = "상품쿠폰";

		if($row->sale_type == '1' || $row->sale_type == '2') $sale2_text = "할인율 쿠폰&nbsp;&nbsp;&nbsp;";
		if($row->sale_type == '3' || $row->sale_type == '4') $sale2_text = "금액 쿠폰&nbsp;&nbsp;&nbsp;";

		if($row->sale_type<=2) $dan="%&nbsp;&nbsp;&nbsp;";
		else $dan="원&nbsp;&nbsp;&nbsp;";

		$maxPrice = $row->sale_max_money?"(최대 ".number_format($row->sale_max_money)."원)&nbsp;&nbsp;&nbsp;":'';
		
		if($row->date_start>0) {
			$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."<br>~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
		} else {
			$date = "발급일 부터 ".abs($row->date_start)."일동안,<br>~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
		}

        $regdate = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);
?>
		<tr>
			<td height="50"><?=$number?></td>
            <td><B><a href="javascript:gradeCoupon('<?=$row->coupon_name?>','<?=$row->coupon_code?>', '<?=$coupon_id?>');" style='text-decoration:underline;'><span class="font_blue"><?=$row->coupon_code?></span></a></B></td>
			<td><b><a href="javascript:gradeCoupon('<?=$row->coupon_name?>','<?=$row->coupon_code?>', '<?=$coupon_id?>');" style='text-decoration:underline;'><?=$row->coupon_name?></a></b></td>
            <td><?=$coupon_use_type?></td>
			<TD style='text-align:right;'><?=$sale2_text?><br><span class="font_orange"><?=number_format($row->sale_money).$dan?><br><?=$maxPrice?></span></TD>
			<td><?=$date?></td>
			<td><?=$regdate?></td>
		</tr>
<?
		$cnt++;
	}
	if ($cnt==0) {
		$colspan='7';
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
