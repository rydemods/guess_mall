<?php
include_once('outline/header_m.php');
$page_cate = 'Q&amp;A';

$bridx = $_GET['bridx'];
$temp_sql = "SELECT * FROM tblproductbrand WHERE bridx = ".$bridx;
$temp_result = pmysql_query($temp_sql,get_db_conn());
?>
<link rel="stylesheet" type="text/css" href="./fromsw/css/brand_qna.css?ver=4.8">
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
	<?php 
		while($temp_row = pmysql_fetch_object($temp_result)) {
			echo "<span>".$temp_row->brandname."</span>";
		}
	?>
		</h2>
		<div class="breadcrumb">
			<?php include_once('brand_menu.php'); ?>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_wrap">
		<div class='qna-wrap'>
			<div class='qna-desc'>공식 온라인 쇼핑몰 [신원몰]과 관련된 문의는 신원몰 <a href="./mypage_personal.php" class='qna-desc-strong'>마이페이지 1:1문의</a>를 이용해주시길 바랍니다.</div>
			<iframe src="http://www.sw.co.kr/QnA/QnA_mall.php?brand=<?php echo $bridx;?>" id="qna-frame" frameborder="0" scrolling="no" ></iframe>
		</div>
	</section>

</main>
<!-- //내용 -->
<?php
include_once('outline/footer_m.php');
?>

