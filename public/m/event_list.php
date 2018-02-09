<?
	include_once('outline/header_m.php');
?>
<main id="content" class="subpage">

<?
$imgpath = $Dir.DataDir."shopimages/board/event/";

$sql = "SELECT * FROM tblboard WHERE board='event' ORDER BY category DESC, writetime DESC ";
$result=pmysql_query($sql,get_db_conn());
if(pmysql_num_rows($result) > 0) {
	while($row=pmysql_fetch_object($result)) {
?>


<article class="event_item">
	<div class="pic">
	<? //if(file_exists($imgpath.$row->vstorefilename) && ord($row->vstorefilename)) { ?> <!-- 이미지 파일 확인 -->
	<?php
		if($row->link_url){
			$link_str = str_replace("/front","/m",$row->link_url);
	?>
		<a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="<?=$link_str?>" target="_self"<?}?>>
	<?}else{?>
		<a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="javascript:goView('<?=$row->num?>')" target="_self"<?}?>>
	<?} ?>
		<img src="<?=$imgpath.$row->vstorefilename?>" <?if(file_exists($imgpath.$row->vstorefilename) && ord($row->vstorefilename)) {?>style='height:auto;'<?} else {?>style='height:150px;'<?}?> alt="<?=$row->title?>" /></a></div>
		<span class="<?if($row->category=="진행중"){echo "on";}else{echo "off";}?>"><?=$row->category?></span> 
		<div class="event_info">
			<ul>
			<li class="subject">
			<?php
				if($row->link_url){
					$link_str = str_replace("/front","/m",$row->link_url);
			?>
				<a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="<?=$link_str?>" target="_self"<?}?>><?}else{?><a <?if(strlen($_ShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="javascript:goView('<?=$row->num?>')" target="_self"<?}?>><?} ?><?=$row->title?></a></li>
			</ul>
		</div> 
	<? //} ?>
</article>

<?
	}
}

?>
</main>

<script>
function goView(num){
	location.href="event_view.php?board=event&boardnum="+num;
}
</script>

<?
include_once('outline/footer_m.php')
?>

