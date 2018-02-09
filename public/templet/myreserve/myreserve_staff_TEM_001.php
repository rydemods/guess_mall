
	
	<div class="containerBody sub-page">
		
		<div class="breadcrumb">
			<ul>
				<li><a href="/">HOME</a></li>
				<li><a href="mypage.php">MY PAGE</a></li>
				<li class="on"><a>STAFF MILEAGE</a></li>
			</ul>
		</div>

		<!-- LNB -->
		<div class="left_lnb">
			<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
			<!---->
		</div><!-- //LNB -->
		
		<!-- 내용 -->
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<div class="right_section mypage-content-wrap">

<?
	//적립예정 마일리지
	//list($save_point)=pmysql_fetch_array(pmysql_query("SELECT sum(op.reserve) as save_point FROM tblorderproduct op LEFT JOIN tblorderinfo oi ON op.ordercode=oi.ordercode WHERE id = '".$_ShopInfo->getMemid()."' AND op.op_step in(1,2,3) AND op.reserve > 0 GROUP BY oi.id"));
?>			
			<h4 class="mypage-title align-top">임직원 마일리지 현황</h4>
			<table class="th-top util">
				<colgroup>
					<col style="width:auto"><!-- <col style="width:180px"><col style="width:180px"> --><!-- <col style="width:190px"> -->
				</colgroup>
				<thead>
					<tr>
						<th scope="col">사용가능 마일리지</th>
						<!-- <th scope="col">적립예정 마일리지</th>
						<th scope="col">소멸예정 마일리지</th> -->
						<!-- <th scope="col">산정기간</th> -->
					</tr>
				</thead>
				<tbody>
					<tr class="my-mileage">
						<td>현재 마일리지는 <?=number_format( $reserve_staff )?> 입니다.</td>
						<!-- <td><strong><?=number_format( $save_point )?> M</strong></td>
						<td>
<?php
	//소멸예정 마일리지
	//$expireSql = "SELECT point, body, to_char( expire_date::date, 'YYYY-MM-DD' ) as expire_date FROM tblpoint WHERE mem_id = '".$_ShopInfo->getMemid()."' AND expire_chk = 0 ORDER BY expire_date ASC LIMIT 1";
	$expireSql =	"select sum(point - use_point) as expire_point
							from tblpoint  
							where mem_id = '".$_ShopInfo->getMemid()."' 
							and expire_chk = 0  
							and expire_date = '".date('Ym').date("t", time())."' 
						";
	$expireRes = pmysql_query( $expireSql, get_db_conn() );
	$expireRow = pmysql_fetch_row( $expireRes );
	pmysql_free_result( $expireRes );
?>
							<strong><?=number_format( $expireRow[0] )?> M</strong><span class="del-date">(소멸 예정일:<?=date('Y-m')."-".date("t", time())?>)</span>
						</td> -->
						<!-- <td class="computation-date"><?=date('Y-m-d')?> ~ <?=date( 'Y-m-d', mktime( 0, 0, 0, date('m'), date('d'), date('Y') + 1 ) )?></td> -->
					</tr>
				</tbody>
			</table>
			
			<h4 class="mypage-title coupon-list3">임직원 마일리지 내역</h4>
			<!-- 날짜 설정 -->
			<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
			<div class="date_find_wrap">
				<ul class="date_setting">
					<li class="title">기간별 조회</li>
					<li class="date">
						<?
							if(!$day_division) $day_division = '1MONTH';

						?>
						<?foreach($arrSearchDate as $kk => $vv){?>
							<?
								$dayClassName = "";
								if($day_division != $kk){
									$dayClassName = 'btn_white_s';
								}else{
									$dayClassName = 'btn_black_s';
								}
							?>
							<a href="Javascript:;" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><?=$vv?></a>
						<?}?>
						
					</li>
					<li class="title">일자별 조회</li>
					<li class="date">
						<div class="input_bg"><input type="text" name="date1" id="" value="<?=$strDate1?>" readonly ></div><a href="javascript:;" class="btn_calen CLS_cal_btn"></a> ~ 
						<div class="input_bg"><input type="text" name="date2" id="" value="<?=$strDate2?>" readonly ></div><a href="javascript:;" class="btn_calen CLS_cal_btn"></a> &nbsp;&nbsp;
						<a href="javascript:CheckForm();" class="btn-dib-function"><span>SEARCH</span></a>
					</li>
					
				</ul>
			</div>
			</form><!-- //날짜 설정 -->
			<!-- 적립금 내역 -->
			<div class="table_wrap">
				
				<table class="th-top util top-line-none">
					<colgroup>
						<col style="width:180px"><col style="width:auto"><col style="width:145px"><col style="width:145px"><col style="width:145px">
					</colgroup>
					<thead>
					<tr>
						<!-- <th scope="col">번호</th> -->
						<th scope="col">날짜</th>
						<th scope="col">상세내역</th>
						<th scope="col">적립 마일리지</th>
						<th scope="col">사용 마일리지</th>
						<th scope="col">잔여 마일리지</th>
					</tr>
					</thead>
<?
		/*
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		*/

		$sql = "SELECT  pid, regdt, body, point, use_point, expire_date, tot_point 
                FROM    tblpoint_staff 
                WHERE   mem_id = '".$_ShopInfo->getMemid()."' 
                AND     regdt >= '".$s_curdate."000000' AND regdt <= '".$e_curdate."235959' 
                ORDER BY pid DESC
            ";

		$paging = new New_Templet_paging($sql,10,10,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //exdebug($sql);
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$regdt = substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2);
            $expire_date = substr($row->expire_date,0,4)."-".substr($row->expire_date,4,2)."-".substr($row->expire_date,6,2);
?>
			 <tr class="coupon-list-padding">
			 <!-- <td><?=$number?></td> -->
			 <td><?=$regdt?></td>
			 <td><?=$row->body?></td>
			 <!-- <td class="price-align"><?=number_format($row->point)?></td>
			 <td><?=$expire_date?></td> -->
			 <td><? if( $row->point <= 0 ) { echo 0; } else { echo number_format( $row->point ); } ?></td>
			 <td><? if( $row->point <= 0 ) { echo number_format( $row->point ); } else { echo 0; } ?></td>
			 <td class="price-align"><?=number_format($row->tot_point)?></td>
			 </tr>
<?
		
			$i++;
		}
		pmysql_free_result($result);
		if ($i==0) {
			echo " <tr><td colspan='5'>해당내역이 없습니다.</td></tr>";
		}
?>
		
				</table>
				<div class="paging mt_30"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div>
			</div>

			<!-- <dl class="attention mt-70">
				<dt>유의사항</dt>
				<dd>마일리지는 구매금액 제한 없이 현금처럼 사용하실 수 있습니다.</dd>
				<dd>마일리지는 부여된 날로부터 2년 후 자동소멸 되며, 소멸되는 마일리지는 1개월 단위로 메일로 안내드립니다.</dd>
				<dd>회원탈퇴 시 보유마일리지는 소멸되며 추후 복구는 불가합니다.</dd>
				<dd>마일리지 적립은 구입액 기준이 아니라 할인된 결제금액으로 적용됩니다.</dd>
				<dd>마일리지로만 구매한 주문건에는 마일리지 적립이 되지 않습니다.</dd>
			</dl> -->

			

		</div><!-- 내용 -->
		</form>
	</div>


<div id="create_openwin" style="display:none"></div>

</div>
<? include($Dir."admin/calendar_join.php");?>
</BODY>
</HTML>






		
