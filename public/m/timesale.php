<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
?>
<nav class="maintop">
	<!--
		(D) ���õ� li �� class="on" title="���õ�" �� �߰��մϴ�.
		a �� href �� "#link_banner + ����" �������� ���ʷ� �־��ݴϴ�.
		������ ���� �������� ������ ������ a �� href �� ������ ��θ� �־��ְ�, ������ �ڵ��Ǿ� �ִ� �������� ����Ͻø� �˴ϴ�.
	-->
	<ul>
		<li class="on" title="���õ�"><a href="#link_content1"><span>�̺�Ʈ</span></a></li>
		<li><a href="#link_content2"><span>�ƿ﷿</span></a></li>
		<li><a href="#link_content3"><span>������ ��õ</span></a></li>
		<li><a href="#link_content4"><span>�Ż�ǰ</span></a></li>
		<li><a href="#link_content5"><span>�ֱ� �� ��ǰ</span></a></li>
	</ul>
</nav>

<!--<div class="btn_area"><a class="btn_more" onclick="timesale();">Ÿ�Ӽ���</a></div>
<div class="btn_area"><a class="btn_more" onclick="eventlist();">�̺�Ʈ</a></div>-->

<main id="content" class="mainpage rolling">
	<div class="loadwrap">
		<article class="index_s_banner">
		<?echo $ts;?>
		<!--���� �߰��� ��� ���� and ���� �̹��� ����, �̹����� ������ index �����̵��� �ȵȴ�. �ݵ�� �̹��� �ϳ��� ��µǰ� ����� �κ�-->
			<ul class="s_banner">
				<li><a href="#"><img src="images/dummy.png" alt="" /></a></li>
			</ul>
		</article>
	<?header("Content-Type:text/html;charset=EUC-KR")?>
	<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<script>
function timesale(){ //document.ready�� Ÿ�Ӽ��� �κ� �ҷ��ͼ� �ѷ���. timesale�� ajax�� �ҷ���
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

		<div id="timesale"> <!--���⼭���� ����-->
		<!--ajax�� �ҷ��� Ÿ�Ӽ��� ���� ����-->

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
				<? if(file_exists($imgpath.$row->vstorefilename) && ord($row->vstorefilename)) { ?> <!-- �̹��� ���� Ȯ�� -->
				<?php
					if($row->link_url){
			$link_str = str_replace("/front","/m",$row->link_url);
	?>
				<a href="<?=$link_str?>" target="_self">
					<?}else{?>
				<a href="javascript:goView('<?=$row->num?>')" target="_self">
					<?} ?>
					<img src="<?=$imgpath.$row->vstorefilename?>" alt="" /></a></div>
					<span class="<?if($row->category=="������"){echo "on";}else{echo "off";}?>"><?=$row->category?></span>
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



