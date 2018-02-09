<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.php");

$tday=date('Y-m-d');
$timesale_tday = date('Y-m-d H:i:s');
if($_POST['sword']){
	
	if($_POST['skey']=='title'){
		$where=" where title like '%".$_POST['sword']."%' ";
	}
	if($_POST['skey']=='content'){
		$where=" where (content || shortdesc) like '%".$_POST['sword']."%' ";
	}
}
$selected[skey][$_POST['skey']]='selected';


$imagepath = $cfg_img_path['event'];

$qry2="select COUNT(*) from tbl_event_list ".$where." ";

$result2 = pmysql_query($qry2,get_db_conn());
$row2 = pmysql_fetch_row($result2);
$t_count = $row2[0];
pmysql_free_result($result2);

$paging = new newPaging((int)$t_count,10,8);
$gotopage = $paging->gotopage;

$sql = "select *,to_char(edate,'YYYY-MM-DD') as edt, to_char(sdate,'YYYY-MM-DD') as sdt,to_char(regdt,'YYYY-MM-DD') as rdt from tbl_event_list ".$where." order by regdt desc";
$sql = $paging->getSql($sql);

$ev_res = pmysql_query($sql,get_db_conn());
$total = pmysql_num_rows($ev_res);

$evt_list_total=$t_count;


//타임세일
$imagepath_timesale = $cfg_img_path['timesale'];
$time_qry="select * from tbl_timesale_list where view_type='1' and rolling_type='1' and sdate <='".$timesale_tday."' and edate>= '".$timesale_tday."' and (ea-sale_cnt)>0 and rolling_v_img!='' and view_v_img!='' order by regdt desc";
$time_res=pmysql_query($time_qry);
$time_cnt=pmysql_num_rows($time_res);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - <?=$setup[board_name]?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<link type="text/css" rel="stylesheet" href="../css/common.css" />
<link type="text/css" rel="stylesheet" href="../css/sub.css" />
<script type="text/javascript" src="../css/base.js"></script>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<script>

function GoPage(block,gotopage) {
	document.form_paging.block.value=block;
	document.form_paging.gotopage.value=gotopage;
	document.form_paging.submit();
}

function search(){

	document.eventform.submit();
}



</script>



</HEAD>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>



<form name="eventform" method="post">
<input type="hidden" id="timecnt" value="<?=$time_cnt?>"> <!-- 타임세일 노출갯수 -->
<!-- 컨텐츠 -->
<div class="community_container sub_container">


	<div class="community_contents">
		<?if($time_cnt>0){?>
		<!-- 타임세일 -->
		<div class="community_timesale visual_wrap" style="overflow:hidden;height:420px">
			<div class="slides_container" style="overflow:hidden;height:388px">

				<?
				$loop=0;
				while($time_row=pmysql_fetch_array($time_res)){
					//할인률
					$dc_per = intval((($time_row['price']-$time_row['s_price'])/$time_row['price'])*100);
					//종료시간
					$time_row['edate']=str_replace('-','/',$time_row['edate']);
					
					/*
					$ea_qry="select count(*) as cnt from tblorderproduct where productcode='".$time_row['productcode']."' and date between '".$time_row['sdate']."' and '".$time_row['edate']."' ";
					$ea_res=pmysql_query($ea_qry);
					$ea_row=pmysql_fetch_array($ea_res);
					*/


					//판매수량만큼 추가노출
					$time_row['sale_cnt']+=$time_row['add_ea'];
					$time_row['ea']+=$time_row['add_ea'];
					
					//구매율
					$sale_per=intval(($time_row['sale_cnt']/$time_row['ea'])*100);

					
				?>

				<div class="timesale_content" style="width:830px">
					<img class="image" src="<?=$imagepath_timesale?><?=$time_row['rolling_v_img']?>" alt="" style="width:609px;height:388px"/>
					<div class="percent"><span><em><?=$dc_per?></em>%</span></div>
					<div class="timesale_info">
						<h3><?=$time_row['title']?></h3>
						<input type="hidden" id="edate<?=$loop?>" value="<?=$time_row['edate']?>">
						<input type="hidden" id="stock<?=$loop?>" value="<?=($time_row['ea']-$time_row['sale_cnt'])?>">
						<div class="timer timer<?=$loop?>">

						</div>
						<div class="price">
							<p class="sell"><span>판매가</span><span class="right"><del><?=number_format($time_row['price'])?></del>원</span></p>
							<p class="special"><span>특가</span><span class="right"><em><?=number_format($time_row['s_price'])?></em>원</span></p>
						</div>
						<div class="condition">
							<span class="left"><em><?=$time_row['sale_cnt']?>개</em>구매</span><span class="right">잔여<?=number_format($time_row['ea']-$time_row['sale_cnt'])?> / 총<?=number_format($time_row['ea'])?></span>
							<span class="gage"><span class="bar" style="width:<?=$sale_per?>%"></span></span>
						</div>
						<div class="btn_container" id="btn_container<?=$loop?>">
							<a href="../front/productdetail_timesale.php?productcode=<?=$time_row['productcode']?>&timesale=y&timesno=<?=$time_row['sno']?>" target="_self"><img src="<?=$Dir?>image/sub/btn_view_detail.png" alt="상세보기" /></a>
							<a href="<?=$Dir.FrontDir?>basket.php?ordertype=ordernow&productcode=<?=$time_row[productcode]?>" target="_self"><img src="<?=$Dir?>image/sub/btn_nowbuy.gif" alt="바로구매" /></a>
						</div>
						<img class="icon_arrow" src="<?=$Dir?>image/sub/community_timesale_arrow.png" alt="" />
					</div>
				</div>
				<?
					$loop++;
					}
				?>
			</div>

			<!--ul>
				<li class="on"><a class="menu" href="#" target="_self">1</a></li>
				<li><a class="menu" href="#" target="_self">2</a></li>
				<li><a class="menu" href="#" target="_self">3</a></li>
			</ul-->
		</div>
		<!-- 타임세일 -->
		<?}?>
		<!-- 이벤트 -->
		<div class="community_event">
			<h3>이벤트 <span>| 총 <em><?=number_format($evt_list_total)?>개</em>의 글이 등록되었습니다.</span></h3>
			<ul>

				<? 
					$cnt=0;
					while($row=pmysql_fetch_object($ev_res)) { 

					$cnt++;
				?>
				<li>
					<a href="../board/event_view.php?sno=<?=$row->sno?>">
						<img class="image" src="<?=$imagepath?><?=$row->title_img?>" alt="event1" style="width:391px;height:140px" />
						<span class="event_content">
							<em><?=mb_strimwidth($row->title, '0', '50', '', 'euc-kr')?></em>
							<span class="txt"><?=mb_strimwidth($row->shortdesc, '0', '150', '...', 'euc-kr')?></span>
							<span class="date">이벤트 기간 : 
							<?
								if($row->edt<$tday){
									echo "<span style='color:red;display:inline'>종료된 이벤트 입니다</span>";
								}else{
									echo $row->sdt." ~ ".$row->edt;
								}
							?>
							</span>
						</span>
					</a>
					<?if($row->win_member){?>
					<a href="../board/event_view.php?sno=<?=$row->sno?>" target="_self"><img class="button" src="<?=$Dir?>image/sub/btn_winner.png" alt="당첨자발표" /></a>
					
					<?}?>
				</li>
				<?
					}
					pmysql_free_result($ev_res);
					if ($cnt==0) {
									echo "<li align=center>등록된 이벤트가 없습니다.</li>";
					}
				?>
			</ul>
			<? if ($cnt!=0) {?>
			<div class="paginate">
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			</div>
			<?}?>
			<div class="list_search">
				<select class="option" name="skey">
					<option value="title" <?=$selected['skey']['title']?>>이벤트명</option>
					<option value="content" <?=$selected['skey']['content']?>>내용</option>
				</select>
				<input type="text" class="bar" name="sword" value="<?=$_POST['sword']?>"/>
				<input type="button" class="btn" onclick="search();"/>
			</div>
		</div>
		<!-- // 이벤트 -->

	</div>

</div>
</form>
<form name=form_paging method=post action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name="sword" value="<?=$_POST['sword']?>">
	<input type=hidden name="skey" value="<?=$_POST['skey']?>">
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<input type=hidden name="board" value=<?=$board?>>
</form>
<!-- // 컨텐츠 -->
<hr />


</body>
<script>
$(document).ready(function() {
	
	function event_counter(){

		var timelength = $("#timecnt").val();

		for(var i=0;i<timelength;i++){

			var h_html='<div class="hour">';
			var m_html='<div class="min">';
			var s_html='<div class="sec">';

			today = new Date();
			d_day = new Date($("#edate"+i).val());
			daysround=0;

			hours = (d_day - today) / 1000 / 60 / 60;
			hoursround = Math.floor(hours);
			minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysround) - (60 * hoursround);
			minutesround = Math.floor(minutes);
			seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysround) - (60 * 60 * hoursround) -
			(60 * minutesround);
			secondsround = Math.round(seconds);


			hoursround = hoursround.toString();
			minutesround = minutesround.toString();
			secondsround = secondsround.toString();
			
			//시간끝나면 버튼 사라짐
			if(parseInt(hoursround)<0 || parseInt(minutesround)<0 || parseInt(secondsround)<0 ){
				hoursround = "000";
				minutesround = "00";
				secondsround = "00";

				$("#btn_container"+i).html("<img src='../image/sub/sale_comp.png'>");
			}

			//잔여 없으면 버튼 사라짐
			if($("#stock"+i).val()<=0){
				hoursround = "000";
				minutesround = "00";
				secondsround = "00";
				$("#btn_container"+i).html("<img src='../image/sub/sale_comp.png'>");
			}

			//시간
			for(var h=0;h<(3-hoursround.length);h++){
				var h_html = h_html+"<span>0</span>";
			}
			for(var h=0;h<hoursround.length;h++){
				var h_html = h_html+"<span>"+hoursround.charAt(h)+"</span>";
			}
			h_html=h_html+"</div>";
			
			//분
			if(minutesround.length==1){
				var m_html = m_html+"<span>0</span>";
			}
			for(var h=0;h<minutesround.length;h++){
				var m_html = m_html+"<span>"+minutesround.charAt(h)+"</span>";
			}
			m_html=m_html+"</div>";
			
			//초
			if(secondsround.length==1){
				var s_html = s_html+"<span>0</span>";
			}
			for(var h=0;h<secondsround.length;h++){
				var s_html = s_html+"<span>"+secondsround.charAt(h)+"</span>";
			}
			s_html=s_html+"</div>";

			
			$(".timer"+i).html(h_html+m_html+s_html);

		}

		setTimeout(event_counter, 1000);
	}

	event_counter();

	$('div.visual_wrap').slides({
		play: 3000,
		pause: 6000,
		hoverPause: true,
		generateNextPrev: false,
		generatePagination: true,
		next: 'next-right',
		prev: 'prev-left'
	});
	
	
});
</script>

</html>
