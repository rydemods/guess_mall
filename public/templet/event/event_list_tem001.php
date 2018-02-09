<!--이벤트 배너를 가지고 오기(임시로 메인상단 롤링 배너를 가져옴)-->

			<!-- 메인 컨텐츠 -->
<div class="main_wrap">
				<!-- 이벤트 -->
	<div class="event_list_wrap">
		<div class="title_area" >
			<h2><img src="<?=$Dir?>img/sub/event_title.png" alt="EVENT" /></h2>
			<div class="line_map" style="height:0px;">홈 &gt; <strong>이벤트</strong></div>
		</div>
		<!-- 이벤트 상단 -->
		<div class="top_area" style="margin: 5px 0px 5px 0px; ">
		<div class="trend_timesale_wrap">
<?		##### 타임세일 정보
			$today_date = date("Y-m-d H:i:s");
			$sql_timesale = "SELECT *,to_char(sdate,'YYYY-MM-DD-HH24-MI-SS') as s_date, to_char(edate,'YYYY/MM/DD HH24:MI:SS') as e_date FROM tbl_timesale_list ";
			$sql_timesale.= "WHERE 1=1 ";
			$sql_timesale.= "AND sdate<='{$today_date}' AND edate>='{$today_date}'";
			$res_timesale = pmysql_query($sql_timesale);
			$_odata = pmysql_fetch_array($res_timesale);
?>
			<input type="hidden" id="edate" value="<?=$_odata['e_date']?>">
			<input type="hidden" id="ddate" value="<?=$ddate?>">
			<div class="time_wrap ">
			<?	if($_odata && $_odata['view_type']=='1') {	?>
				<div>
				<h3><img src="../img/common/h3_title_timesale.gif" alt="" /></h3>
				<p class="goods"><a href="../front/timesale.php"><img src="../data/shopimages/timesale/<?=$_odata['rolling_v_img']?>" alt="" /></a></p>
				<div class="title">
					<?=$_odata['title']?><br />
					<span class="original"><?=number_format($_odata['price'])?></span>
					<span class="price"><?=number_format($_odata['s_price'])?><em>원</em></span>
					<div class="time_bg">
						<ul>
							<li class="timerH">00</li>
							<li class="timerM">00</li>
							<li class="timerS">00</li>
							<li class="btn"><a href="../front/timesale.php"><img src="../img/btn/btn_buy01.gif" alt="구매하기" /></a></li>
						</ul>
					</div>
				</div>
				</div>
			<?	}	?>
				</div>
			</div><!-- //div.trend_timesale_wrap -->
		</div>
		<!-- // 이벤트 상단 -->
		<!--<div class="title_area" style="margin-top: 5px;"></div>-->
		<!-- 새로운 이벤트리스트 -->
		<div class="new_event_list" style="margin-top: 5px;">
			<div class="list_sort">
				<ul>
					<li id="event1" class="<?=$class_on_tab[0]?>"><a href="#" class="sort" onclick="goTab2('0');return false;">전체 이벤트</a></li>
					<li id="event2" class="<?=$class_on_tab[1]?>"><a href="#" class="sort" onclick="goTab2('1');return false;">진행중 이벤트</a></li>
					<li id="event3" class="<?=$class_on_tab[2]?>"><a href="#" class="sort" onclick="goTab2('2');return false;">종료된 이벤트</a></li>
				</ul>
				<div class="right_search">
					<input type="text" name="searchtxt" value = '<?=$searchtxt?>' />
					<a href="javascript:goSearch();" class="btn_search">검색</a>
				</div>
			</div>

			<ul class="layout" id="event_all" style="display:block;">
			<?php
				switch($tab){
					case '0':
						$where = "";
					break;
					case '1':
						$where = " AND category = '진행중' ";
					break;
					case '2':
						$where = " AND category = '마감' ";
					break;
				}

				if($searchtxt){
					$where .= " AND title like '%{$searchtxt}%' ";
				}

				$sql = "SELECT * FROM tblboard where board='event' {$where} ";
				$sql .= "ORDER BY category DESC, writetime DESC";
				$paging = new Tem001_saveheels_Paging($sql, 10, 8, 'GoPage', true);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				$sql = $paging->getSql($sql);
				//exdebug($sql);
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) { //exdebug($row);
			?>

				<li>
					<div class="event_item">
					<p>
					<?if($row->link_url){?>
						<a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="<?=$row->link_url?>" target="_self"<?}?>>
					<?}else{?>
						<!--<a href="javascript:goView('<?=$row->num?>')" target="_self">-->
						<a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="../board/board.php?pagetype=view&num=<?=$row->num?>&board=event" target="_self"<?}?>>
					<?}?>
							<img src="<?=$Dir.DataDir."shopimages/board/event/".$row->vstorefilename?>" alt="<?=$row->title?>" /></a></p>
						<dl>
							<dt><?=$row->title?></dt>
							<dd class="date"></dd>
							<?if($row->category=='진행중'){?>
							<dd class="prossess"><span>진행중</span></dd>
							<?}else{?>
							<dd class="prossess"><span class="end">종료</span></dd>
							<?}?>
						</dl>
					</div>
				</li>
			<?php
				}
			?>
			</ul>
			<div class="paging goods_list">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			</div>
		</div>
		<!-- //새로운 이벤트리스트 -->
	</div>
	<!-- // 이벤트 -->
</div>
<!-- //메인 컨텐츠 -->
<script>
<?if($_odata ){?>
$(document).ready(function(){

	function event_counter(){

			var h_html='';
			var m_html='';
			var s_html='';


			$.post( "../front/ajax_for_today_date.php", function(data){
				$("#ddate").val(data);
			});

			today = new Date($("#ddate").val());
			d_day = new Date($("#edate").val());
			daysround=0;

			hours = (d_day - today) / 1000 / 60 / 60;
			hoursround = Math.floor(hours);
			minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysround) - (60 * hoursround);
			minutesround = Math.floor(minutes);
			seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysround) - (60 * 60 * hoursround) -
			(60 * minutesround);
			secondsround = Math.round(seconds);
			if(hoursround>99){
				hoursround=99;
			}


			hoursround = hoursround.toString();
			minutesround = minutesround.toString();
			secondsround = secondsround.toString();

			//시간끝나면 버튼 사라짐
			if(parseInt(hoursround)<0 || parseInt(minutesround)<0 || parseInt(secondsround)<0 ){
				hoursround = "00";
				minutesround = "00";
				secondsround = "00";

				$(".btn_container_time").html("<img src='../img/common/sale_comp.png'>");
			}

			//잔여 없으면 버튼 사라짐

			if($("#stock").val()<=0){
				hoursround = "00";
				minutesround = "00";
				secondsround = "00";
				$(".btn_container_time").html("<img src='../img/common/sale_comp.png'>");
			}

			//시간
			for(var h=0;h<(2-hoursround.length);h++){
				var h_html = h_html+"0";
			}
			for(var h=0;h<hoursround.length;h++){
				var h_html = h_html+hoursround.charAt(h);
			}
			//h_html=h_html+"</div>";

			//분
			if(minutesround.length==1){
				var m_html = m_html+"0";
			}
			for(var h=0;h<minutesround.length;h++){
				var m_html = m_html+minutesround.charAt(h);
			}
			//m_html=m_html+"</div>";

			//초
			if(secondsround.length==1){
				var s_html = s_html+"0";
			}
			for(var h=0;h<secondsround.length;h++){
				var s_html = s_html+secondsround.charAt(h);
			}
			//s_html=s_html+"</div>";
			$(".timerH").html(h_html);
			$(".timerM").html(m_html);
			$(".timerS").html(s_html);
			//$(".timer").html(h_html+m_html+s_html);

			setTimeout(event_counter, 1000);
	}

	event_counter();

});
<?}?>
</script>