<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$sql = "SELECT a.*, b.group_level, b.group_name, b.group_code, b.group_orderprice_s, b.group_orderprice_e, b.group_ordercnt_s, b.group_ordercnt_e FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}

//exdebug($_POST);

$s_curtime=strtotime("$s_year-$s_month-$s_day");
$s_curdate=date("Ymd",$s_curtime)."000000";
$e_curtime=strtotime("$e_year-$e_month-$e_day 23:59:59");
$e_curdate=date("Ymd",$e_curtime)."235959";

//exdebug($s_curdate);
//exdebug($e_curdate);

//point
$sql = "SELECT  pid, regdt, body, point, use_point, expire_date, tot_point 
        FROM    tblpoint  
        WHERE   mem_id = '".$_ShopInfo->getMemid()."' 
        AND     regdt >= '".$s_curdate."' AND regdt <= '".$e_curdate."' 
        ORDER BY pid DESC
        ";
$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//exdebug($t_count);
//exdebug($sql);

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//exdebug($sql);

//epoint
$e_sql = "SELECT  pid, regdt, body, point, use_point, expire_date, tot_point 
        FROM    tblpoint_act  
        WHERE   mem_id = '".$_ShopInfo->getMemid()."' 
        AND     regdt >= '".$s_curdate."' AND regdt <= '".$e_curdate."' 
        ORDER BY pid DESC
        ";
$e_paging = new New_Templet_paging($e_sql, 10,  10, 'GoPage2', true);
$e_t_count = $e_paging->t_count;
$gotopage2 = $e_paging->gotopage;
//exdebug($t_count);
//exdebug($sql);

$e_sql = $e_paging->getSql($e_sql);
$e_result=pmysql_query($e_sql,get_db_conn());
//exdebug($sql);

//erp통합포인트 가져오기
$erp_reserve=getErpMeberPoint($_mdata->id);
$erp_reserve = $erp_reserve[p_data]?$erp_reserve[p_data]:"0";

?>
<script type="text/javascript">
<!--
$(document).ready(function(){
});


//-->
</script>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>


<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">포인트</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<article class="my-content">
				
				<div class="point-info clear">
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_grade.png" alt="회원등급">회원등급</dt>
						<dd class="fz-16"><?=$_mdata->name?> 님의 회원등급<br> <strong class="fz-20"><?=$_mdata->group_name?></strong></dd>
						<dd class="pt-5">&nbsp;</dd>
						<!--<dd class="pt-5">등급업 필요 포인트 <strong>1,000P</strong><br>※ 온라인 회원등급은 통합포인트 기준</dd>-->
					</dl>
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_point_big.png" alt="통합 포인트">통합 포인트</dt>
						<dd class="fz-20">현재 통합 포인트 <strong class="fz-22 point-color"><?=number_format($erp_reserve) ?>P</strong></dd>
						<dd class="pt-5">통합포인트: 오프라인 매장, 신원몰에서<br>모두 사용이 가능한 통합포인트</dd>
					</dl>
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_epoint_big.png" alt="E통합 포인트">현재 E포인트</dt>
						<dd class="fz-20">현재 E포인트 <strong class="fz-22 point-color"><?=number_format($_mdata->act_point) ?>P</strong></dd>
						<dd class="pt-5">E포인트: 신원몰에서만 사용이 가능한<br>온라인 전용 포인트</dd>
					</dl>
				</div>

				<section class="mt-25" data-ui="TabMenu">
					<div class="tabs"> 
						<button type="button" data-content="menu" data-point_type='point' data-point_count='<?=$t_count?>' class="<?=$tap_point['point']?> point_change"><span>통합포인트</span></button>
						<button type="button" data-content="menu" data-point_type='epoint' data-point_count='<?=$e_t_count?>' class="<?=$tap_point['epoint']?> point_change"><span>E포인트</span></button>
					</div>
					<header class="my-title mt-50">
						<h3 class="fz-0">포인트 내역</h3>
						<div class="count">전체 <strong class="p_count"><?if($point_type=="point"){echo $t_count;}else{echo $e_t_count;}?></strong></div>
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
						<div class="date-sort clear">
							<div class="type month">
								<p class="title">기간별 조회</p>
								<?
									if(!$day_division) $day_division = '1MONTH';

								?>
								<?foreach($arrSearchDate as $kk => $vv){?>
									<?
										$dayClassName = "";
										if($day_division != $kk){
											$dayClassName = '';
										}else{
											$dayClassName = 'on';
										}
									?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
								<?}?>
							</div>
							<div class="type calendar">
								<p class="title">일자별 조회</p>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$strDate1?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
								<span class="dash"></span>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$strDate2?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
							</div>
							<button type="button" class="btn-point" onClick="javascript:CheckForm();"><span>검색</span></button>
						</div>
						</form>
					</header>
					<div data-content="content" class="<?=$tap_point['point']?>">
						<table class="th-top">
							<caption>통합포인트 목록</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:125px">
								<col style="width:125px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">날짜</th>
									<th scope="col">상세내역</th>
									<th scope="col">적립포인트</th>
									<th scope="col">사용포인트</th>
								</tr>
							</thead>
							<tbody>
<?
    $cnt=0;
    if ($t_count > 0) {
        while($row=pmysql_fetch_object($result)) {

            $regdt = substr($row->regdt,0,4).".".substr($row->regdt,4,2).".".substr($row->regdt,6,2);
?>
								<tr>
									<td class="txt-toneB"><?=$regdt?></td>
									<td class="txt-toneA subject"><?=$row->body?></td>
									<td class="txt-toneA"><? if( $row->point <= 0 ) { echo 0; } else { echo number_format( $row->point ); } ?></td>
									<td class="txt-toneB"><? if( $row->point <= 0 ) { echo number_format( $row->point ); } else { echo 0; } ?></td>
								</tr>
<?
		    $cnt++;
		}
	} else {
?>
								<tr>
									<td colspan="4">내역이 없습니다.</td>
								</tr>
<?
	}
?>

							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
						</div>
					</div>
					<div data-content="content" class="<?=$tap_point['epoint']?>">
						<table class="th-top">
							<caption>E포인트 목록</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:125px">
								<col style="width:125px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">날짜</th>
									<th scope="col">상세내역</th>
									<th scope="col">적립포인트</th>
									<th scope="col">사용포인트</th>
								</tr>
							</thead>
							<tbody>
<?

    $cnt=0;
    if ($e_t_count > 0) {
        while($e_row=pmysql_fetch_object($e_result)) {

            $regdt = substr($e_row->regdt,0,4).".".substr($e_row->regdt,4,2).".".substr($e_row->regdt,6,2);
?>
								<tr>
									<td class="txt-toneB"><?=$regdt?></td>
									<td class="txt-toneA subject"><?=$e_row->body?></td>
									<td class="txt-toneA"><? if( $e_row->point <= 0 ) { echo 0; } else { echo number_format( $e_row->point ); } ?></td>
									<td class="txt-toneB"><? if( $e_row->point <= 0 ) { echo number_format( $e_row->point ); } else { echo 0; } ?></td>
								</tr>
<?
		    $cnt++;
		}
	} else {
?>
								<tr>
									<td colspan="4">내역이 없습니다.</td>
								</tr>
<?
	}
?>

							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$e_paging->a_prev_page.$e_paging->print_page.$e_paging->a_next_page?>
						</div>
					</div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<div id="create_openwin" style="display:none"></div>

<? include($Dir."admin/calendar_join.php");?>