<?php
include_once('outline/header_m.php')
?>
<!-- ���� ��� -->
	<nav class="maintop">
		<!--
			(D) ���õ� li �� class="on" title="���õ�" �� �߰��մϴ�.
			a �� href �� "#link_banner + ����" �������� ���ʷ� �־��ݴϴ�.
			������ ���� �������� ������ ������ a �� href �� ������ ��θ� �־��ְ�, ������ �ڵ��Ǿ� �ִ� �������� ����Ͻø� �˴ϴ�.
		-->
		<ul>
			<li><a href="#link_content1"><span>�̺�Ʈ</span></a></li>
			<li><a href="#link_content2"><span>�ƿ﷿</span></a></li>
			<li><a href="#link_content3"><span>������ ��õ</span></a></li>
			<li class="on" title="���õ�"><a href="#link_content4"><span>�Ż�ǰ</span></a></li>
			<li><a href="#link_content5"><span>�ֱ� �� ��ǰ</span></a></li>
		</ul>
	</nav>
	<!-- // ���� ��� -->
<!-- ���� -->
<main id="content" class="mainpage rolling">



	<div class="loadwrap todaywrap">
		<article class="index_s_banner">

		<!--���� �߰��� ��� ���� and ���� �̹��� ����, �̹����� ������ index �����̵��� �ȵȴ�. �ݵ�� �̹��� �ϳ��� ��µǰ� ����� �κ�-->
			<ul class="s_banner">
				<li><a href="#"><img src="images/dummy.png" alt="" /></a></li>
			</ul>
		</article>

		<div class="productwrap thumb" id="listUL3" >
				<ul >
					<?for($i=0; $i < 4; $i++){?>
					<li>
						<div class="goods_wrap" >
						<?if(is_file($Dir.DataDir."shopimages/product/".$newgoods[$i][maximage])){?>
						<a href="productdetail.php?pridx=<?=$newgoods[$i][pridx]?>">
						<img src="<?=$Dir.DataDir."shopimages/product/".$newgoods[$i][maximage]?>" alt="" />
						<div class="infobox">
							<span class="name"><?=$newgoods[$i][productname];?></span>
							<div class="pricebox">
								<strong>
									<del><?=number_format($newgoods[$i][consumerprice])?></del>
									<?=number_format($todaygoods[$i][sellprice])?>
								</strong>
							</div>
						</div>
						</a>
						<?}?>
						</div>
					</li><?}?>
				</ul>

			</div>
			<div class="btn_area"><a class="btn_more" onclick="morePrd3();" id="bmore2">������</a></div>
			<script type="text/javascript">
				var display2 = 4;	//����Ǵ� ���μ�
				var offset2 = 4;		//���� �������� �ִ� ���μ�
				var catmobile2=2; //today.php�� ajax_moblie.php���� �ϱ� ������ ���� ������

				function morePrd3(){
					$.post('ajax_mobile.php',{displayLine:display2, offsetLine:offset2,catmobile:catmobile2},function(data){
						if(data!=0)
						{
							$("#listUL3").append(data);
							offset2+=display2;
							// ���� ������
							$(".container").height($(".container").children("ul").children("li").eq(4).outerHeight());
						}
						else
						{	$("#bmore2").hide();
							alert("�� �̻� ��ǰ�� �����ϴ�");
						}
					});
				}
			</script>

		</div>
	</div><!--class="loadwrap todaywrap" end"--> <!--������ʺ��� �Ѹ��� ���Ե�-->

</main>
<!-- // ���� -->

<?php
include_once('outline/footer_m.php')
?>