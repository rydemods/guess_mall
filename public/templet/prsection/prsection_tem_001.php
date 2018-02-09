<!-- 상세페이지 -->
<div class="main_wrap">
	<div class="container">
		<!-- 카테고리별 리스트 -->
		<div class="main_product_list">
			<h2><?=$category_name?></h2>
<?
//	<!-- 상품목록 시작 -->

?>
			<h1 class="sub_name">
				<br>
				<p><a href="#">총 <?=$t_count?>개의 상품이 있습니다.</a></p>
			</h1>
			<div class="list">
				<ul>
<?

		$tag_0_count = 2; //전체상품 태그 출력 갯수
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.maximage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql = $paging->getSql($sql);


		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			//타임세일 가격변경
			$timesale_data=$timesale->getPdtData($row->productcode);
			$time_sale_now='';
			if($timesale_data['s_price']>0){
				$time_sale_now='Y';
				$row->sellprice = $timesale_data['s_price'];
			}
			//타임세일 가격변경
			
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
			if (strlen($row->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->maximage)) { ?>
				<li>
					<div class="img">
					<A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort=".$sort?>">
					<img src="<?=$Dir.DataDir."shopimages/product/".urlencode($row->maximage)?>" width=240 height=240 ></A>
				
					<div class="quick_menu">
					<img src="<?=$Dir?>images/common/icon_RPLout01.gif" onclick="PrdtQuickCls.quickView('<?=$row->productcode?>');">
					<img src="<?=$Dir?>images/common/icon_RPLout02.gif" onclick="PrdtQuickCls.quickFun('<?=$row->productcode?>','1');">
					<img src="<?=$Dir?>images/common/icon_RPLout03.gif" onclick="PrdtQuickCls.quickFun('<?=$row->productcode?>','2');">
					<img src="<?=$Dir?>images/common/icon_RPLout04.gif" onclick="PrdtQuickCls.quickFun('<?=$row->productcode?>','3');">	
					</div>
					</div>
					<p><A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query?>"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></A>
					<br />
					<?if($row->consumerprice!=0){?>
							<strike><?=number_format($row->consumerprice)?></strike>
					<?}
					echo number_format($row->sellprice)." won";
					?>
					
				</p>
<?php		} else { ?>
				<li></li><img src="<?=$Dir?>images/no_img.gif" width=240 height=240></li>
<?php		}

		}?>
				
			</div>
		</div>
		<!-- # 카테고리별 리스트 -->

		<!-- 페이징 -->
		<div class="page_wrap">
			<div class="page">
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			
		</div>
		<!-- #페이징 -->

	</div>
</div>




