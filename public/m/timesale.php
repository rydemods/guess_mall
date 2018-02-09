<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
?>
<nav class="maintop">
	<!--
		(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
		a 의 href 는 "#link_banner + 숫자" 조합으로 차례로 넣어줍니다.
		각각의 별도 페이지로 구성할 때에는 a 의 href 에 페이지 경로를 넣어주고, 별도로 코딩되어 있는 페이지를 사용하시면 됩니다.
	-->
	<ul>
		<li class="on" title="선택됨"><a href="#link_content1"><span>이벤트</span></a></li>
		<li><a href="#link_content2"><span>아울렛</span></a></li>
		<li><a href="#link_content3"><span>오늘의 추천</span></a></li>
		<li><a href="#link_content4"><span>신상품</span></a></li>
		<li><a href="#link_content5"><span>최근 본 상품</span></a></li>
	</ul>
</nav>

<!--<div class="btn_area"><a class="btn_more" onclick="timesale();">타임세일</a></div>
<div class="btn_area"><a class="btn_more" onclick="eventlist();">이벤트</a></div>-->

<main id="content" class="mainpage rolling">
	<div class="loadwrap">
		<article class="index_s_banner">
		<?echo $ts;?>
		<!--차후 추가될 배너 영역 and 더미 이미지 영역, 이미지가 없으면 index 슬라이딩이 안된다. 반드시 이미지 하나는 출력되게 만드는 부분-->
			<ul class="s_banner">
				<li><a href="#"><img src="images/dummy.png" alt="" /></a></li>
			</ul>
		</article>
	<?header("Content-Type:text/html;charset=EUC-KR")?>
	<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<script>
function timesale(){ //document.ready로 타임세일 부분 불러와서 뿌려줌. timesale은 ajax로 불러옴
			//alert("ok1");
			$.post('ajax_timesale.php',function(data){
			//alert(data);
			$("#timesale").append(data);

		});
}


/*function eventlist(){
			alert("ok2");
			$.post('event.php',function(data){
			alert(data);
			$("#event").append(data);

			});
		}*/
$(document).ready(timesale());
</script>

		<div id="timesale"> <!--여기서부터 내용-->
		<!--ajax로 불러온 타임세일 들어가는 영역-->

		</div>

		<div class="event" id="event">
<?
$imgpath = $Dir.DataDir."shopimages/board/event/";

$sql = "SELECT * FROM tblboard WHERE board='event' ";
$result=pmysql_query($sql,get_db_conn());
if(pmysql_num_rows($result) > 0) {
	while($row=pmysql_fetch_object($result)) {
?>


			<article class="event_item" style="margin-bottom:15px;">
				<div class="pic">
				<? if(file_exists($imgpath.$row->vstorefilename) && ord($row->vstorefilename)) { ?> <!-- 이미지 파일 확인 -->
				<?php
					if($row->link_url){
			$link_str = str_replace("/front","/m",$row->link_url);
	?>
				<a href="<?=$link_str?>" target="_self">
					<?}else{?>
				<a href="javascript:goView('<?=$row->num?>')" target="_self">
					<?} ?>
					<img src="<?=$imgpath.$row->vstorefilename?>" alt="" /></a></div>
					<span class="<?if($row->category=="진행중"){echo "on";}else{echo "off";}?>"><?=$row->category?></span>
				<? } ?>
			</article>

<?
	}//while
}

?>


<script>
function goView(num){
	location.href="board_view.php?board=event&boardnum="+num;
}
</script>


		</div>
	</div>
</main>



