<?php
$Dir = "../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);

$mb_qry="select * from tblmainbannerimg order by banner_sort";

if (stripos($shop_intro,"<table")!==false || strlen($mainflash)>0)
	$main_banner=$shop_intro;
else
	$main_banner=nl2br($shop_intro);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="떼르벨" />
	<meta name="keywords" content="" />

	<title>떼르벨</title>

	<link rel="stylesheet" href="../css/terrebell.css" />
	<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
</head>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
	
	<div class="main_wrap">
	<?include ($Dir.MainDir.$_data->menu_type.".php");?>
		<div class="container1100">	
			<!-- webzine_step01 -->
			<div class="webzine_step01">
				<p class="dyeing_absolute"></p>
				<div class="goods_wrap">
				<!-- 진열관리 db(관리자 페이지에서 정한 상품에 대한 정보) -->
				<?
				$sql = "SELECT special_list FROM tblspecialmain WHERE special='2' ";
				$result = pmysql_query($sql,get_db_conn());
						if($row = pmysql_fetch_object($result)){
							$cnt_prcode=$row->special_list;
							$sp_prcode=str_replace(',','\',\'',$cnt_prcode);
						}
				pmysql_free_result($result);
				if(ord($sp_prcode)) {
					$sql = "SELECT productcode,productname,sellprice,consumerprice,minimage,etctype ";
					$sql.= "FROM tblproduct ";
					$sql.= "WHERE productcode IN ('{$sp_prcode}')";
					
					$result = pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					$productcode = $row->productcode;
					}
				?>
					<a href="productdetail.php?productcode=<?=$productcode?>"><!-- 상품 상세페이지 -->
				<?	if(ord($row->minimage) && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)){
						echo "<img src=\"".$Dir.DataDir."shopimages/product/{$row->minimage}\" border=0 >";
					} else {
						echo "<img src=\"{$Dir}images/no_img.gif\" border=0 width=$imgwidth>";
					} ?></a>
					<div class="price">
						<span class="icon"><?=viewicon($row->etctype)?></span><!-- 아이콘 -->
						<span class="title"><?=$row->productname?></span>
						<span class="price"><em><?=number_format($row->consumerprice)?> 원</em>
						<?=number_format($row->sellprice)?> 원</span>
					</div>
					<!-- 리뷰 db (진열된 상품의 베스트 리뷰) -->
				<?
				pmysql_free_result($result);
				$rsql = "SELECT id, content FROM tblproductreview ";
				$rsql.= "WHERE productcode= ('{$productcode}') ";
				$rsql.= "AND best_type = 1 ";
				$rsql.= "ORDER BY date DESC LIMIT 1";
				$result_review = pmysql_query($rsql);
				$review_row=pmysql_fetch_object($result_review);
				?>
					<dl class="review">
						<p class="id"><?=substr($review_row->id,0,3)?>***</p>
						<dt>Review</dt>
						<dd>
							<?=strcutDot($review_row->content,100)?>
						</dd>
					</dl>
				</div>
				<!-- webzine_step01 우측 배너 -->
				<div class="banner_area">
					<img src="<?=$banner_url.$mainBanner[webzine_step01][1][banner_img]?>" alt="" />
				</div>
			</div><!-- //webzine_step01 -->

			<!-- webzine_step02 -->
			<!-- webzine_step02 좌측 배너 -->
			<div class="webzine_step02">
				<div class="left_area">
					<h3 class="def"><em>BABY</em> ZONE</h3>
					<div class="step02_banner"><a href="<?=$banner_url.$mainBanner[webzine_step02l][1][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step02l][1][banner_img]?>" alt="" /></a></div>
				</div>
				<!-- webzine_step02 우측 배너 1,2 -->
				<div class="right_area">
					<h3 class="def"><em>NATURALDYEING</em> ZONE</h3>
					<p class="banner01"><a href="<?=$banner_url.$mainBanner[webzine_step02r][1][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step02r][1][banner_img]?>" alt="" /></a></p>
					<p class="banner02"><a href="<?=$banner_url.$mainBanner[webzine_step02r][2][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step02r][2][banner_img]?>" alt="" /></a></p>
				</div>
			</div><!-- //webzine_step02 -->

			<!-- webzine_step03 -->
			<div class="webzine_step03">
				<div class="qna">
					<div class="webzine_qna_wrap">
					<h3 class="def"><em>TERREBELL</em> Q&A</h3>
					<!-- Q&A 게시판 db (1:1 문의 db ) -->
					<?    /* Q&A 게시판  */
						$board = "tblboard";
						$sql_qna = "SELECT num, name, writetime, content FROM tblboard ";
						$sql_qna.= "WHERE board='qnabbs' ";
						$sql_qna.= "ORDER BY writetime DESC ";
						$sql_qna.= "LIMIT 4";
						$qna_result=pmysql_query($sql_qna,get_db_conn());
						while($qna_row=pmysql_fetch_object($qna_result)){
							$qna_date=date("Y-m-d H:i:s", $qna_row->writetime);
					?>
						<p class="more"><a href="<?=$Dir.BoardDir?>board.php?board=qnabbs">more+</a></p>
						<ul class="list">
							<li>
								<a href="<?=$Dir.BoardDir?>board.php?pagetype=view&num=<?=$qna_row->num?>&board=qnabbs">
								<span class="name"><?=substr($qna_row->name,0,2)?>***</span>
								<span class="date"><?=$qna_date?></span><br />
								<span class="subject"><?=strcutDot($qna_row->content,55)?>...</span>
								</a>
							</li>
						<?	}	?>
						</ul>
					</div>
				</div>
				<!-- webzine_step03 우측 배너 1,2-->
				<p class="banner01"><a href="<?=$banner_url.$mainBanner[webzine_step03r][1][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step03r][1][banner_img]?>" alt="" /></a></p>
				<p class="banner02"><a href="<?=$banner_url.$mainBanner[webzine_step03r][2][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step03r][2][banner_img]?>" alt="" /></a></p>
			</div><!-- //webzine_step03 -->

			<!-- webzine_step04 -->
			<div class="webzine_step04">
			<!-- webzine_step04 좌측 배너 -->
				<div class="left_area">
					<h3 class="def"><em>BANNER</em> AREA</h3>
					<div class="banner">
						<a href="<?=$banner_url.$mainBanner[webzine_step04][1][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_step04][1][banner_img]?>" alt="" /></a>
					</div>
				</div>
				<div class="right_area">
					<h3 class="def"><em>BEST</em> REVIEW</h3>
					<div class="list_wrap">
					<!-- BEST REVIEW -->
					<?
						$bqry = "WHERE a.productcode = b.productcode AND a.productcode = c.c_productcode ";
						$bsql = "SELECT a.productcode, a.num, a.id, a.content, a.subject, a.upfile, b.tinyimage ";
						$bsql.= "FROM tblproductreview a, tblproduct b, tblproductlink c ";
						$bsql.= $bqry;
						$bsql.= "AND a.best_type=1 ";
						$bsql.= "ORDER BY a.date DESC ";
						$bsql.= "LIMIT 3 ";
						
						$bresult=pmysql_query($bsql,get_db_conn());
						while($brow=pmysql_fetch_object($bresult)){
					?>
						<div class="webzine_review_list">
							<a href="review_view.php?num=<?=$brow->num?>">
							<div class="img100">
							<? if(ord($brow->upfile) && file_exists($Dir.DataDir."shopimages/board/reviewbbs/".$brow->upfile)) { ?>
									<img src="<?=$Dir.DataDir?>shopimages/board/reviewbbs/<?=$brow->upfile?>" border=0 >
							<?	}
								else {
									if(ord($brow->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$brow->tinyimage)) { ?>
										<img src="<?=$Dir.DataDir?>shopimages/product/<?=$brow->tinyimage?>" border=0 >
							<?		}
									else {	?>
										<img src="<?=$Dir?>images/no_img.gif" border=0 width=$imgwidth>
							<?			}
								} ?>
							</div>
							<div class="review031">
								<span class="title"><?=$brow->subject?> <em><?=substr($brow->id,0,2)?>***</em></span>
								<span class="content"><?=strcutDot($brow->content,100)?></span>
							</div>
							</a>
						</div>
					<?	}	?>
					</div>
				</div>
			</div><!-- webzine_step04 -->

			<div class="webzine_event_area">
				<h3 class="def"><em>EVENT</em> AREA</h3>
				<ul>
					<li><a href="<?=$banner_url.$mainBanner[webzine_event_area][1][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_event_area][1][banner_img]?>" alt="" /></a></li>
					<li><a href="<?=$banner_url.$mainBanner[webzine_event_area][2][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_event_area][2][banner_img]?>" alt="" /></a></li>
					<li><a href="<?=$banner_url.$mainBanner[webzine_event_area][3][banner_link]?>"><img src="<?=$banner_url.$mainBanner[webzine_event_area][3][banner_img]?>" alt="" /></a></li>
				</ul>
			</div>			
			<!-- 바텀 -->


		</div>
		<?	include ($Dir."lib/bottom.php");?>
	</div>

</body>

</html>