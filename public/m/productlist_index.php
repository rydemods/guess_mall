<?php
header("Content-Type:text/html;charset=EUC-KR");
$code=$_REQUEST["code"];
$poption=$_REQUEST["poption"];
$list_type = $_REQUEST["list_type"];

include_once('outline/header_m.php')
?>
<?$ts=$_GET['ts'];?>

<?
if($codeA=='003'){//acc카테고리 막아달라고 요청. 임시로 달아놓음
	echo "<script>alert('현재 준비중입니다');history.go(-1);</script>";
}
?>

<?if($codeA=='006'){
	include_once('brand.php');
}else{
?>


<!-- 브레드크럼 - 상품리스트에서만 나오면 됩니다. -->
<script src="js2/breadcrumb.js"></script>

<!-- 메인 상단 -->
	<nav class="maintop" id="mainTop">
		<!--s
			(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
			a 의 href 는 "#link_banner + 숫자" 조합으로 차례로 넣어줍니다.
			각각의 별도 페이지로 구성할 때에는 a 의 href 에 페이지 경로를 넣어주고, 별도로 코딩되어 있는 페이지를 사용하시면 됩니다.
		-->
		<!--<div>-->
			<ul>
			<?if($mcate){?>
				<?for($i=0; $i<count($mcate); $i++){?>
				<li <?if($i==0){echo "class='on'";}?>style="display: block;"><a href="#link_content<?=$i+1?>" idx = "<?=$i?>"><span><?=$mcate[$i]->code_name?></span></a></li>
				<?}//$c2 헤더부분 참조?>
			<?}else{?>
				<li class="on" style="display:block;"><a href="#link_content1" idx = "0"><span><?=$cateListA_row->code_name?></span></a></li>
			<?}?>
			</ul>
		<!--</div>-->
	</nav>
	<!-- // 메인 상단 -->

<!-- 내용 -->
<main id="content" class="mainpage rolling">

	<div class="loadwrap todaywrap">
		<div class="container">
			<!--
				(D) data-url 경로의 파일을 순차적으로 로드하여 해당 li 안에 붙여넣습니다.
				li 에 href 와 연결되도록 id 를 차례로 넣어줍니다.
			-->
			<ul>
			<?if($mcate){?>
				<?for($i=0; $i<count($mcate); $i++){?>
					<li id="link_content<?=$i+1?>" data-url="productlist.php?code=<?=$mcate[$i]->code_a.$mcate[$i]->code_b.'&poption='.$poption.'&list_type='.$list_type?>"></li>
				<?}?>
			<?}else{?>
				<li id="link_content1" data-url="productlist.php?code=<?=$codeA.'&poption='.$poption.'&list_type='.$list_type?>"></li>
			<?}?>
			</ul>
			<script src="js2/main.js"></script>
		</div>

		<h2 class="blind">메인인덱스</h2>

	</div>

</main>
<!-- // 내용 -->
<?}?>
<?php
include_once('outline/footer_m.php')
?>