<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

include ("header.inc.php");
$subTitle = "REVIEW";
include ("sub_header.inc.php");
?>

<form name="frmInfo" id="frmInfo" method="POST">
	<input type="hidden" id="page" name="page" value="<?=$page?>" />
</form>
<?
$sql = "SELECT * FROM tblproductreview WHERE best_type = 1 ORDER BY date DESC LIMIT 3";
$result2 = pmysql_query($sql);
while($brow2 = pmysql_fetch_object($result2)){
$date=substr($brow2->date,0,4)."-".substr($brow2->date,4,2)."-".substr($brow2->date,6,2);
?>		
<div id="review_popup<?=$brow2->num?>" class="review_popup" style="display:none; z-index: 30;position: absolute;">
	<div class="title">
		<h3><?=$brow2->subject?></h3>
		<a href="javascript:closePop(<?=$brow2->num?>)">¡¿</a>
		</div>
		<div class="content">
		<span class="name"><?=$brow2->id?>´Ô<em>(<?=$date?>)</em></span>
		<span class="con">
			<?=$brow2->content?>
		</span>
		<span class="img"><img src="<?="../data/shopimages/board/reviewbbs/".$brow2->upfile?>" alt="" /></span>
    </div>
</div>
<?}?>

<article class="best_review_top">
	<h2><img src="img/title_review.jpg" alt="BEST REVIW" /></h2>
	<div class="reviw_sorting">
		<ul>
			<?			
			$result = pmysql_query($sql);
			$cnt = 1;
			while($brow = pmysql_fetch_object($result)){
			?>
			<li><a href="javascript:showPop(<?=$brow->num?>)"><p class="best_icon">BEST<span>0<?=$cnt?></span></p><div class="thumb">
				<img src="<?="../data/shopimages/board/reviewbbs/".$brow->upfile?>" alt="" />
				<p class="review_data">
				<?
				$qry1 = "SELECT pridx, productname FROM tblproduct WHERE productcode = '{$brow->productcode}'";
				$res1 = pmysql_query($qry1);
				$row1 = pmysql_fetch_object($res1);
				?>
				<span class="prodct"><?=$row1->productname?></span>
				<span class="star">
					<?for($i=0;$i<$brow->marks;$i++) echo "¡Ú";
						for($i=$brow->marks;$i<5;$i++) {
							echo "<em>¡Ú</em>";
						}
					?>
				</span>
				<span class="name"><?=$brow->id?>´Ô</span></p>
				<p class="review_data2"><?=strcutDot($brow->content,100)?></p>
			</div></a></li>			
			<? $cnt++; }?>
		</ul>
		
	</div>
</article>

<article class="mypage">
	<section class="mypage_tb2">
	<h3 class="mypage_tit"">»óÇ°Æò</h3>
   	</section>

	<article class="review">

		<div class="best_review">

			<article class="brand_best_review">
				<? 
					$qry = "SELECT * FROM tblproductreview ORDER BY date DESC LIMIT 4";
					$res = pmysql_query($qry);
					while($row = pmysql_fetch_object($res)){
					$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
				?>
				<ul class="review">
					<li>
						<p class="title">
							<a href="javascript:divToggle(<?=$row->num?>)"><?=$row->subject?>
							<?if($row->upfile != ""){?>
								<img src="img/icon_photo.png" alt="" />
							<?}?></a>
							<span class="star">
								<?for($i=0;$i<$row->marks;$i++) echo "¡Ú";
									for($i=$row->marks;$i<5;$i++) {
										echo "<em>¡Ú</em>";
									}
								?>
							</span>
						</p>
						<div class="best_review_content" id="best_review_content<?=$row->num?>" style="display:none;">
							<div class="subject">
								<?
									$qry2 = "SELECT pridx, productname FROM tblproduct WHERE productcode = '{$row->productcode}'";
									$res2 = pmysql_query($qry2);
									$row2 = pmysql_fetch_object($res2);
								?>
								
								<p><?=$row2->productname?></p>
								<a href="<?="productdetail.php?pridx=".$row2->pridx?>" class="view">Á¦Ç°º¸±â ></a>
							</div>
							<div class="content">
								<span class="name"><?=$row->id?>´Ô<em>(<?=$date?>)</em></span>
								<span class="con">
									<?=nl2br($row->content)?>
								</span>
								<span class="img"><img src="<?="../data/shopimages/board/reviewbbs/".$row->upfile?>" alt="" /></span>
							</div>
						</div>
					</li>
				</ul>		
				<?	}?>
				
				
			</article>

		</div>
	</article>
	
</article>

<script type="text/javascript">

function divToggle(idx){
	$("#best_review_content"+idx).toggle(100);
}

function showPop(idx){
	$("#review_popup"+idx).show();
}

function closePop(idx){
	$("#review_popup"+idx).hide();
}
</script>

<? include ("footer.inc.php"); ?>