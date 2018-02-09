<?php
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT  a.*, b.group_level, b.group_name, b.group_code, b.group_orderprice_s, b.group_orderprice_e, b.group_ordercnt_s, b.group_ordercnt_e FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);

#####날짜 셋팅 부분
$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

$day_division = $_POST['day_division'];

$limitpage = $_POST['limitpage'];
$point_type=$_POST['point_type']?$_POST['point_type']:"point";

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);

$tap_point[$point_type]="active";

$s_curtime=strtotime("$s_year-$s_month-$s_day");
$s_curdate=date("Ymd",$s_curtime)."000000";
$e_curtime=strtotime("$e_year-$e_month-$e_day 23:59:59");
$e_curdate=date("Ymd",$e_curtime)."235959";

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



?>

<script type="text/javascript">
<!--

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	//document.form2.block2.value="";
	//document.form2.gotopage2.value=0;
	document.form2.submit();
}
function GoPage2(block,gotopage) {
	//document.form2.block.value="";
	//document.form2.gotopage.value=0;
	document.form2.block2.value=block;
	document.form2.gotopage2.value=gotopage;
	document.form2.submit();
}

var NowTime=parseInt(<?=time()?>);
function GoSearch(gbn, obj) {

	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(s_date.getMonth()-9);
			break;
		case "12MONTH":
			s_date.setFullYear(s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form2.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form2.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//

    document.form2.submit();
}

function str_pad_right(num){
	
	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;
}
//-->
</script>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
<input type=hidden name=block2 value="<?=$block2?>">
<input type=hidden name=gotopage2 value="<?=$gotopage2?>">
<input type=hidden name=point_type value="<?=$point_type?>">
<input type=hidden name="date1" id="" value="<?=$strDate1?>">
<input type=hidden name="date2" id="" value="<?=$strDate2?>">
</form>

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>포인트</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_point sub_bdtop">

		<div class="mypoint">
			<div class="lv_point">
				<p class="mylv"><strong><?=$_mdata->name?></strong>님의 회원등급 <strong class="level"><?=$_mdata->group_name?></strong></p>
				<!--<p class="msg">등급업 필요 포인트 <strong>1,000P</strong></p>-->
			</div>
			<div class="point_now mt-15">
				<ul class="clear">
					<li>
						<span class="icon">P</span>
						<p class="mt-5">현재 통합 포인트</p>
						<p class="point-color"><strong><?=number_format($erp_reserve)?>P</strong></p>
					</li>
					<li>
						<span class="icon">E</span>
						<p class="mt-5">현재 E포인트</p>
						<p class="point-color"><strong><?=number_format($_mdata->act_point) ?>P</strong></p>
					</li>
				</ul>
			</div>
			<div class="point_info">
				<p class="notice">※ 온라인 회원등급은 통합포인트 기준</p>
				<ul>
					<li>통합포인트: 오프라인 매장, 신원몰에서 모두 사용이 가능한 통합포인트</li>
					<li>E포인트: 신원몰에서만 사용이 가능한 온라인 전용 포인트</li>
				</ul>
			</div>
		</div><!-- //.mypoint -->
		
		<div class="point_tab tab_type1" data-ui="TabMenu">
			<div class="tab-menu clear">
				<a data-content="menu" data-point_type='point' class="<?=$tap_point['point']?> point_change" title="선택됨">통합포인트</a>
				<a data-content="menu" data-point_type='epoint' class="<?=$tap_point['epoint']?> point_change">E포인트</a>
			</div>

			<!-- 통합포인트 -->
			<div class="tab-content <?=$tap_point['point']?>" data-content="content">
				
				<div class="check_period">
					<ul>
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
							<li class="<?=$dayClassName?>"><a href="javascript:;" onClick = "GoSearch('<?=$kk?>', this)"><?=$vv?></a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
						<?}?>
					</ul>
				</div><!-- //.check_period -->
				
				<div class="list_point"><!-- [D] 5개 페이징 -->
					<ul>
<?
    $cnt=0;
    if ($t_count > 0) {
        while($row=pmysql_fetch_object($result)) {

            $regdt = substr($row->regdt,0,4).".".substr($row->regdt,4,2).".".substr($row->regdt,6,2);
?>
						<li>
							<p class="point_name"><?=$row->body?><span class="date"><?=$regdt?></span></p>
							<p class="">적립 포인트 <span class="blk"><? if( $row->point <= 0 ) { echo 0; } else { echo number_format( $row->point ); } ?>P</span> <span class="bar">|</span> 사용 포인트 <span class="blk"><? if( $row->point <= 0 ) { echo number_format( $row->point ); } else { echo 0; } ?>P</span></p>
						</li>
<?
		    $cnt++;
		}
	} else {
?>
						<li>
							<p class="point_name">내역이 없습니다.</span></p>
						</li>		
<?
	}
?>

					</ul>
				</div><!-- //.list_point -->
				
				<div class="list-paginate mt-15">
					<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
				</div><!-- //.list-paginate -->
			</div>
			<!-- //통합포인트 -->

			<!-- E포인트 -->
			<div class="tab-content <?=$tap_point['epoint']?>" data-content="content"><!-- [D] 통합포인트와 구성 동일 -->
				<div class="check_period">
					<ul>
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
							<li class="<?=$dayClassName?>"><a href="javascript:;" onClick = "GoSearch('<?=$kk?>', this)"><?=$vv?></a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
						<?}?>
					</ul>
				</div><!-- //.check_period -->

				<div class="list_point"><!-- [D] 5개 페이징 -->
					<ul>
<?

    $cnt=0;
    if ($e_t_count > 0) {
        while($e_row=pmysql_fetch_object($e_result)) {

            $regdt = substr($e_row->regdt,0,4).".".substr($e_row->regdt,4,2).".".substr($e_row->regdt,6,2);
?>
						<li>
							<p class="point_name"><?=$e_row->body?><span class="date"><?=$regdt?></span></p>
							<p class="">적립 포인트 <span class="blk"><? if( $e_row->point <= 0 ) { echo 0; } else { echo number_format( $e_row->point ); } ?>P</span> <span class="bar">|</span> 사용 포인트 <span class="blk"><? if( $e_row->point <= 0 ) { echo number_format( $e_row->point ); } else { echo 0; } ?>P</span></p>
						</li>
<?
		    $cnt++;
		}
	} else {
?>
						<li>
							<p class="point_name">내역이 없습니다.</span></p>
						</li>		
<?
	}
?>
					
					</ul>
				</div><!-- //.list_point -->
				
				<div class="list-paginate mt-15">
					<?=$e_paging->a_prev_page.$e_paging->print_page.$e_paging->a_next_page?>
				</div><!-- //.list-paginate -->
			</div>
			<!-- //E포인트 -->
		</div><!-- //.point_tab -->

	</section><!-- //.mypage_point -->

</main>
<!-- //내용 -->


<SCRIPT>
$(document).ready(function(){

	$(".point_change").click(function(){
		$("input[name=point_type]").val($(this).data('point_type'));
	})
});
</SCRIPT>

<? include_once('outline/footer_m.php'); ?>