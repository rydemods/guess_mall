<?php
include_once('outline/header_m.php')
?>

<?
$imgurl="http://nasign.ajashop.co.kr/data/shopimages/mainbanner/";
//���� ��� �Ѹ� ��� �̹��� �޿��̱�
$mainbanner_sql="
select * from tblmainbannerimg where banner_name='maintop_rolling' and banner_hidden='1' ORDER BY banner_sort;";

$mainbanner_res = pmysql_query($mainbanner_sql, get_db_conn());
while($mainbanner_row = pmysql_fetch_array($mainbanner_res)){
	$mainbanner[]=$mainbanner_row;}
//exdebug($mainbanner);
?>

<? //���� �ҹ�� �̹��� �޾ƿ���
$middlebannerimg_sql="select * from tblmainbannerimg where banner_name='mainmiddle_rolling' ORDER BY banner_sort;
";

$middlebannerimg_res = pmysql_query($middlebannerimg_sql, get_db_conn());
while($middlebannerimg_row = pmysql_fetch_array($middlebannerimg_res)){
	$middlebannerimg[]=$middlebannerimg_row;}
	//exdebug($middlebannerimg);
?>

<? //new arrivals ����
$goodslist=main_disp_goods();
$new_arrivals=$goodslist[1];
$max=count($goodslist);
?>

<? //besttime�޾ƿ���
$bestitem_sql="SELECT a.category_idx,a.sort,b.pridx,b.productcode,b.productname,b.sellprice,b.maximage,b.minimage,c.code_a
FROM tblrecommendlist a
JOIN tblproduct b ON a.pridx=b.pridx
JOIN tblproductcode c ON a.category_idx=c.idx
WHERE b.display ='Y'
AND code_b = '000'
AND group_code != 'NO'
ORDER BY a.category_idx,a.sort ASC;";

$bestitem_res = pmysql_query($bestitem_sql, get_db_conn());
while($bestitem_row = pmysql_fetch_array($bestitem_res)){
	$bestitem[$bestitem_row['category_idx']]=$bestitem_row;}
?>

<!-- ���� ��� -->
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
	<!-- // ���� ��� -->
<!-- ���� -->
<main id="content" class="mainpage rolling">

	<div class="loadwrap">
		<h2 class="blind">�̺�Ʈ</h2>

		<!-- ��� �Ѹ� -->
		<div class="rollingwrap">
			<div class="containerB">
				<!-- (D) li �� href �� ����ǵ��� id �� ���ʷ� �־��ݴϴ�. -->
				<ul>
				<?for($i=0 ; $i < count($mainbanner) ; $i++){?>
					<li><img src="<?=$imgurl.$mainbanner[$i][banner_img];?>" alt="" /></li>
				<? } ?>
				</ul>
			</div>
			<nav>
				<!--
					(D) ���õ� li �� class="on" title="���õ�" �� �߰��մϴ�.
					a �� href �� "#link_banner + ����" �������� ���ʷ� �־��ݴϴ�.
				-->
				<ul>
					<li class="on" title="���õ�"><a href="#link_banner1">1</a></li>
					<li><a href="#link_banner2">2</a></li>
					<li><a href="#link_banner3">3</a></li>
				</ul>
			</nav>
		</div>
		<!-- // ��� �Ѹ� -->

		<!-- ���� �ҹ�� -->
		<article class="index_s_banner">
			<ul class="s_banner">
			<? for($i=0 ; $i<2 ; $i++){?>
				<li><a href="<?echo $middlebannerimg[$i][banner_link];?>"><img src="<?echo $imgurl.$middlebannerimg[$i][banner_img];?>" alt="" /></a></li>
			<?}?>
			</ul>
		</article>
		<!-- //���� �ҹ�� -->

		<!-- ��ǰ ����Ʈ -->
		<article>
			<ul class="index_goods_tap">
				<li id="na" class="on"><a href="#" id="menu1" onclick="displayswitch('n_arrivals');return false;">NEW ARRIVALS</a></li>

				<li id="bi"><a href="#" id="menu2" onclick="displayswitch('bestitem');return false;">BEST ITEM</a></li>
			</ul>
			<div class="productwrap thumb" id="n_arrivals" style="display:block;">
				<ul>
					<? for ($i=0; $i<4; $i++ ) { ?>
					<li>
						<a href="nesign_goods_view.php?pridx=<?=$new_arrivals[$i][pridx]?>">
							<img class="item" src="<?=$Dir.DataDir."shopimages/product/".$new_arrivals[$i][minimage]?>" alt="">
							<div class="infobox">
								<span class="name"><?=$new_arrivals[$i][productname];?></span>
								<div class="pricebox"><strong><?=number_format($new_arrivals[$i][sellprice])."��";?></strong></div>
							</div>
						</a>
					</li>
					<?}?>
				</ul>
			</div>

			<div class="productwrap thumb" id="bestitem" style="display:none;">
				<ul>
				<!-- BEST ITEM ī�װ� ���� �� �����ؾ� �� -->
				<? for ($i=0; $i<4; $i++ ) {;?>
					<li>
						<a href="nesign_goods_view.php?pridx=<?=$bestitem['107'+$i][pridx]?>">
							<img class="item" src="<?=$Dir.DataDir."shopimages/product/".$bestitem['107'+$i][minimage]?>" alt="">
							<div class="infobox">
								<span class="name"><?=$bestitem['107'+$i][productname];?></span>
								<div class="pricebox"><strong><?=number_format($bestitem['107'+$i][sellprice])."��";?></strong></div>
							</div>
						</a>
					</li>
				<?}?>
				</ul>
			</div>
		</article>
		<!-- // ��ǰ ����Ʈ -->
		<script src="js2/mainEvent.js"></script>
		<script LANGUAGE="JavaScript">//new arrivals �����̵�
			function displayswitch(id){ //new arrivals and bestitem �޴� ��ȯ
			    var objDiv = document.getElementById(id);
				if(id==('n_arrivals')){
					objDiv.style.display="block";
					bestitem.style.display="none";
					document.getElementById("na").className="on";
					document.getElementById("bi").className="";
				}
				if(id==('bestitem')){
					objDiv.style.display = "block";
					n_arrivals.style.display="none";
					document.getElementById("bi").className="on";
					document.getElementById("na").className="";
				}
			};
		</script>
	</div>
<script>
</script>
</main>
<!-- // ���� -->

<?php
include_once('outline/footer_m.php')
?>