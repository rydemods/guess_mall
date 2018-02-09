<?php include_once('outline/header_m.php'); ?>

<?

include_once($Dir."lib/forum.class.php");

$type = $_REQUEST['type'] ? : "list";
$select[$type] ="on";
$forum = new FORUM('myforum_list');
$forum_list = $forum->forum_list;

#####날짜 셋팅 부분
$s_year=(int)$_REQUEST["s_year"];
$s_month=(int)$_REQUEST["s_month"];
$s_day=(int)$_REQUEST["s_day"];

$e_year=(int)$_REQUEST["e_year"];
$e_month=(int)$_REQUEST["e_month"];
$e_day=(int)$_REQUEST["e_day"];

$day_division = $_REQUEST['day_division'];
if ($day_division == '') $day_division = 'TODAY';

$ord_step = $_REQUEST['ord_step'];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>나의 포럼</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="forum_board">
	<ul class="tabmenu_cancellist clear">
		<li class="change_tab idx-menu <?=$select['list'];?>" data-type='list'>등록한 게시물</li>
		<li class="change_tab idx-menu <?=$select['reply'];?>" data-type='reply'>댓글 단 게시물</li>
	</ul>
	
<?if($type=='list'){?>
	<section class="idx-content on">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="date1" id="" value="<?=$strDate1?>">
		<input type="hidden" name="date2" id="" value="<?=$strDate2?>">
		<div class="select_sorting clear">
			<select name='day_division' class="select_def" onChange = "GoSearch(this.value)" style="width:100%">
			<?foreach($arrSearchDate2 as $kk => $vv){?>
				<?
					$day_chk_selected = "";
					if($day_division != $kk){
						$day_chk_selected = '';
					}else{
						$day_chk_selected = ' selected';
					}
				?>
				<option value='<?=$kk?>'<?=$day_chk_selected?>><?=$vv?></option>
			<?}?>
			</select>
			<!--
			<select name="" class="select_def hide" onchange="">
				<option value="" selected="">전체</option>
				<option value="0">요리/맛집</option>
				<option value="1">취미/교양</option>
				<option value="2">스포츠</option>
			</select>
			-->
		</div><!-- //.select_sorting -->
		</form>

		<div class="forum_bbody">
			<ul class="th_none">
				<!-- 반복 -->
			<?foreach($forum_list['list'] as $val){?>
				<li>
					<p class="subject"><a href="/m/forum_view.php?index=<?=$val->index?>"><strong>[<?=$val->code_name?>]</strong><?=$val->title?><span class="point-color">[<?=$val->re?>]</span></a></p>
					<p class="info">
						<span class="date"><?=$val->w_time?></span>
						<span class="view">조회수<?=$val->view?></span>
						<button class="comp-like" onclick="" title="선택 안됨"><span><strong>좋아요</strong><?=$val->like?></span></button>
					</p>
				</li>
			<?}?>
				<!-- //반복 -->
			</ul><!-- //.th_none -->
		</div><!-- //.forum_bbody -->

		<div class="forum_bfoot">
			<div class="list-paginate hide">
				<span class="border_wrap">
					<a href="javascript:;" class="prev-all"></a>
					<a href="javascript:;" class="prev"></a>
				</span>
				<a class="on">1</a>
				<a href="javascript:;">2</a>
				<a href="javascript:;">3</a>
				<a href="javascript:;">4</a>
				<a href="javascript:;">5</a>
				<span class="border_wrap">
					<a href="javascript:;" class="next"></a>
					<a href="javascript:;" class="next-all"></a>
				</span>
			</div><!-- //.list-paginate -->

			<div class="list-paginate">
				<?echo $forum_list['paging']->a_prev_page.$forum_list['paging']->print_page.$forum_list['paging']->a_next_page;?>
			</div>
		</div><!-- //.forum_bfoot -->
	</section><!-- //등록한 게시물 -->

<?}?>

<?if($type=='reply'){?>
	<section class="idx-conten ont">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="date1" id="" value="<?=$strDate1?>">
		<input type="hidden" name="date2" id="" value="<?=$strDate2?>">
		<div class="select_sorting clear">
			<select name='day_division' class="select_def" onChange = "GoSearch(this.value)" style="width:100%">
			<?foreach($arrSearchDate2 as $kk => $vv){?>
				<?
					$day_chk_selected = "";
					if($day_division != $kk){
						$day_chk_selected = '';
					}else{
						$day_chk_selected = ' selected';
					}
				?>
				<option value='<?=$kk?>'<?=$day_chk_selected?>><?=$vv?></option>
			<?}?>
			</select>
			<!--
			<select name="" class="select_def" onchange="">
				<option value="" selected="">전체</option>
				<option value="0">요리/맛집</option>
				<option value="1">취미/교양</option>
				<option value="2">스포츠</option>
			</select>
			-->
		</div><!-- //.select_sorting -->
		</form>
		<div class="forum_bbody">
			<ul class="th_none">
				<!-- 반복 -->
				<?foreach($forum_list['list'] as $val){?>
				<li>
					<p class="subject"><a href="/m/forum_view.php?index=<?=$val->index?>"><strong>[<?=$val->code_name?>]</strong> <?=$val->title?><span class="point-color">[<?=$val->re?>]</span></a></p>
					<p class="info">
						<span class="writer"><?=$val->id?></span>
						<span class="date"><?=$val->w_time?></span>
						<span class="view">조회수 <?=$val->view?></span>
						<button class="comp-like" onclick="" title="선택 안됨"><span><strong>좋아요</strong><?=$val->like?></span></button>
					</p>
				</li>
				<?}?>
				<!-- //반복 -->
			</ul><!-- //.th_none -->
		</div><!-- //.forum_bbody -->

		<div class="forum_bfoot">
			<div class="list-paginate hide">
				<span class="border_wrap">
					<a href="javascript:;" class="prev-all"></a>
					<a href="javascript:;" class="prev"></a>
				</span>
				<a class="on">1</a>
				<a href="javascript:;">2</a>
				<a href="javascript:;">3</a>
				<a href="javascript:;">4</a>
				<a href="javascript:;">5</a>
				<span class="border_wrap">
					<a href="javascript:;" class="next"></a>
					<a href="javascript:;" class="next-all"></a>
				</span>

			</div><!-- //.list-paginate -->

			<div class="list-paginate">
				<?echo $forum_list['paging']->a_prev_page.$forum_list['paging']->print_page.$forum_list['paging']->a_next_page;?>
			</div>
		</div><!-- //.forum_bfoot -->
	</section><!-- //댓글 단 게시물 -->
<?}?>
</div><!-- //.forum_board -->

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name="type" value="<?=$type?>" id="chk_type">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
</form>

<script>

var NowTime=parseInt(<?=time()?>);

var chk_type = "<?=$type?>";

function GoSearch(gbn) {

	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			s_date.setDate(s_date.getDate()-7);
			break;
		case "2WEEK":
			s_date.setDate(s_date.getDate()-14);
			break;
		case "3WEEK":
			s_date.setDate(s_date.getDate()-21);
			break;
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
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
	document.form1.date1.value=s_date_full;
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
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//

	CheckForm();
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

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}

function CheckForm() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form1.date1.value;
	if(!isNull(document.form1.date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.s_year.value = sdatearr[0];
			document.form2.s_month.value = sdatearr[1];
			document.form2.s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));

	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form1.date2.value;
	if(!isNull(document.form1.date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.e_year.value = edatearr[0];
			document.form2.e_month.value = edatearr[1];
			document.form2.e_day.value = edatearr[2];
		}
	}

	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}

	document.form2.submit();
}



function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function change_tab()
{
	var types = $(this).data('type');
	if(chk_type != types){
		document.form2.type.value=types;
		document.form2.submit();
	}
}

$(document).on("click",".change_tab",change_tab);
</script>

<? include_once('outline/footer_m.php'); ?>