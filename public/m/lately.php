<?php
include_once('outline/header_m.php');
include_once ("../lib/lib.php");

$dispLine = 1;				//���� ��ǰ�� ���� ���μ�
$limit = $dispLine*2;		//���μ��� ���� ��ǰ��
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
			<li><a href="#link_content4"><span>�Ż�ǰ</span></a></li>
			<li class="on" title="���õ�"><a href="#link_content5"><span>�ֱ� �� ��ǰ</span></a></li>
		</ul>
	</nav>
	<!-- // ���� ��� -->
<!-- ���� -->
<main id="content" class="mainpage rolling">
	<div class="loadwrap">


		<!--���� �߰��� ��� ���� and ���� �̹��� ����, �̹����� ������ index �����̵��� �ȵȴ�. �ݵ�� �̹��� �ϳ��� ��µǰ� ����� �κ�-->

				<li><img src="images/dummy.png" alt="" style="height:1px;widht:1px"/></li>



			<div class="productwrap thumb" id="lately">
				<ul>
<?php
	####### �ֱ� �� ��ǰ ����Ʈ #######
	//exdebug($_COOKIE);
	$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,��ǰ�ڵ�1,��ǰ�ڵ�2,��ǰ�ڵ�3,) ��������
	$prdt_list=explode(",",$_prdt_list);
	$prdt_no=count($prdt_list);
	if(ord($prdt_no)==0||!$_prdt_list) {
		$prdt_no=0;
	}
	//debug($prdt_no);

	$tmp_product="";
	for($i=0;/*$i<$prdt_no;*/$i<6;$i++){ //pc���� �ֱٺ� ��ǰ5�� ������. ����ϵ� 5���� ���̰� ����
		$tmp_product.="'{$prdt_list[$i]}',";
	}

	$productall = array();
	$tmp_product=rtrim($tmp_product,',');
	$sql = "SELECT productcode,productname,maximage,tinyimage,quantity,consumerprice,sellprice,pridx FROM tblproduct ";
	$sql.= "WHERE productcode IN ({$tmp_product}) ";
	$sql.= "ORDER BY FIELD(productcode,{$tmp_product})";
	$sql.= "LIMIT ".$limit." OFFSET 0";
	$result=pmysql_query($sql,get_db_conn());
	if($prdt_no>0) :
		while($row=pmysql_fetch_object($result)) :
		//exdebug($row);
		//$row->quantity;

	##### ������ ���� ���� ����
	$cou_data = couponDisPrice($row->productcode);
	if($cou_data['coumoney']){
		$nomalprice=$row->sellprice;
		$row->sellprice = $row->sellprice-$cou_data['coumoney'];
	}
	##### ������ Ư��, Ÿ�Ӽ��Ͽ� ���� ����
	$spesell = getSpeDcPrice($row->productcode);
	if($spesell){
		$nomalprice=$row->sellprice;
		$row->sellprice = $spesell;
	}
	##### //������ Ư��, Ÿ�Ӽ��Ͽ� ���� ����
?>
						<li>
							<div class="goods_wrap">
								<a href="productdetail.php?pridx=<?=$row->pridx?>">
								<img src="../data/shopimages/product/<?=$row->maximage;?>" onerror="this.src='<?=$Dir?>images/acimage.gif'" />
								<div class="infobox">
									<span class="name"><?=$row->productname?></span>
									<div class="pricebox">
									<strong>
										<del><?=number_format($row->consumerprice)?></del>
										<?=number_format($row->sellprice)?>
									</strong>
									</div>
								</div>
								</a>
							</div>
						</li>


<?php
		endwhile;
	else :
?>
						<li style="height:200px;">
							<center>�ֱ� �� ��ǰ�� �����ϴ�.</center>
						</li>
<?php
	endif;
?>
				</ul>
			</div>



	<div class="btn_area"><a class="btn_more" onclick="morelately();" id="latelybtn">������</a></div>
	<script type="text/javascript">
		var displayLine = <?=$dispLine?>;	//����Ǵ� ���μ�
		var offsetLine = displayLine;		//���� �������� �ִ� ���μ�
		function morelately(){
			//alert("ok");
			$.post('ajax_lately.php',{display:displayLine,offsetLine:offsetLine},function(p){
				if(offsetLine<3){
					//alert(p);
					$("#lately").append(p);
					offsetLine+=displayLine;
					// ���� ������
					$(".container").height($(".container").children("ul").children("li").eq(5).outerHeight());
				}
				else{
					alert("�ֱ� �� ��ǰ�� 6������ �������ϴ�");
					$("#latelybtn").hide();
				}
			});
		}
	</script>
	</div> <!--�� div�����ȿ� ����־�� ����� �Ѹ��ȴ�-->

</main>
<!-- // ���� -->

<?php
include_once('outline/footer_m.php')
?>

