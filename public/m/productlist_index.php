<?php
header("Content-Type:text/html;charset=EUC-KR");
$code=$_REQUEST["code"];
$poption=$_REQUEST["poption"];
$list_type = $_REQUEST["list_type"];

include_once('outline/header_m.php')
?>
<?$ts=$_GET['ts'];?>

<?
if($codeA=='003'){//accī�װ� ���ƴ޶�� ��û. �ӽ÷� �޾Ƴ���
	echo "<script>alert('���� �غ����Դϴ�');history.go(-1);</script>";
}
?>

<?if($codeA=='006'){
	include_once('brand.php');
}else{
?>


<!-- �극��ũ�� - ��ǰ����Ʈ������ ������ �˴ϴ�. -->
<script src="js2/breadcrumb.js"></script>

<!-- ���� ��� -->
	<nav class="maintop" id="mainTop">
		<!--s
			(D) ���õ� li �� class="on" title="���õ�" �� �߰��մϴ�.
			a �� href �� "#link_banner + ����" �������� ���ʷ� �־��ݴϴ�.
			������ ���� �������� ������ ������ a �� href �� ������ ��θ� �־��ְ�, ������ �ڵ��Ǿ� �ִ� �������� ����Ͻø� �˴ϴ�.
		-->
		<!--<div>-->
			<ul>
			<?if($mcate){?>
				<?for($i=0; $i<count($mcate); $i++){?>
				<li <?if($i==0){echo "class='on'";}?>style="display: block;"><a href="#link_content<?=$i+1?>" idx = "<?=$i?>"><span><?=$mcate[$i]->code_name?></span></a></li>
				<?}//$c2 ����κ� ����?>
			<?}else{?>
				<li class="on" style="display:block;"><a href="#link_content1" idx = "0"><span><?=$cateListA_row->code_name?></span></a></li>
			<?}?>
			</ul>
		<!--</div>-->
	</nav>
	<!-- // ���� ��� -->

<!-- ���� -->
<main id="content" class="mainpage rolling">

	<div class="loadwrap todaywrap">
		<div class="container">
			<!--
				(D) data-url ����� ������ ���������� �ε��Ͽ� �ش� li �ȿ� �ٿ��ֽ��ϴ�.
				li �� href �� ����ǵ��� id �� ���ʷ� �־��ݴϴ�.
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

		<h2 class="blind">�����ε���</h2>

	</div>

</main>
<!-- // ���� -->
<?}?>
<?php
include_once('outline/footer_m.php')
?>