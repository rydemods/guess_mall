<?
	$subTitle = "카테고리";
	include_once('outline/header_m.php');
	include_once('sub_header.inc.php');
?>
<main id="content" class="subpage">
<div class="category-all-wrap">
	<ul class="cate_list">
<?php
			// 상단 메뉴 정보를 가져온다.

			$nav_num	= 1;
			if ($_ShopInfo->getAffiliateType() == '') {
				$nav_menu_type	= 1;
			} else {
				$nav_menu_type	= $_ShopInfo->getAffiliateType();
			}

			$nav_sql	= "select * from tblmainmenu where 1=1 and menu_type ='".$nav_menu_type."' and menu_display = '0' order by menu_sort asc";			
			$nav_res = pmysql_query($nav_sql,get_db_conn());
			while($nav_row = pmysql_fetch_object($nav_res)){
				$nav_title	= $nav_row->menu_title;
				$nav_img	= $nav_row->menu_img;

				if(strpos($nav_row->menu_url, "javascript") !== false) {
					$nav_url		= $nav_row->menu_url;
				} else {
					$nav_url		= $Dir.$nav_row->menu_url;
				}

				$nav_url		= str_replace("../","", $nav_url);
				$nav_url		= str_replace("front/","", $nav_url);

				if (strstr($nav_url,'code=')) {
					$nav_url_arr		= explode('=', $nav_url); 
					$nav_cate_code	= $nav_url_arr[1];
					
					if ($nav_cate_code == '001' || $nav_cate_code == '002') { // 001 (디지털), 002 (패션/잡화) 만 2차 3차 카테고리 적용 (2015.11.03 - 김재수)
						$nav_toggle	="on";
					} else {
						$nav_toggle	="off";
					}
				} else {
					$nav_toggle	="off";
				}

				
?>
		<li>

			<a href="<?=$nav_url?>" <?if ($nav_toggle == 'on') {?>class='on'<?} else {?>class='none'<?}?>><span class="cate-icon"><img src="img/icon/category_icon0<?=$nav_num?>.gif" alt="<?=$nav_title?>"></span><?=$nav_title?></a>
<?php
			if (strstr($nav_url,'code=')) {
				if ($nav_cate_code == '001' || $nav_cate_code == '002') { // 001 (디지털), 002 (패션/잡화) 만 2차 3차 카테고리 적용 (2015.11.03 - 김재수)
?>

			<ul id="subCate<?=$nav_num?>">

<?php


					//코드에 해당하는 상품 카테고리를 가져와서 뿌려준다.
					$cateListA_sql = "
					SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
					FROM tblproductcode
					WHERE code_b = '000' and code_a ='{$nav_cate_code}'
					AND group_code !='NO' AND display_list is NULL
					ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";
					$cateListA_res = pmysql_query($cateListA_sql,get_db_conn());

					
					### 상단 메뉴 2/3차 카테고리 가져오기
					$cate_sql = "
					SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
					FROM tblproductcode
					WHERE code_b!='000' and code_a ='{$nav_cate_code}'
					AND group_code !='NO' AND display_list is NULL 
					ORDER BY cate_sort ASC";
					$cate_res = pmysql_query($cate_sql,get_db_conn());
					while($cate_row = pmysql_fetch_array($cate_res)){
						if($cate_row['code_c']=='000'){
							$cateListB[$cate_row['code_a']][$cate_row['code_b']] = $cate_row;
						}
						else{
							$cateListC[$cate_row['code_a'].$cate_row['code_b']][] = $cate_row;
						}
					}
					pmysql_free_result($cate_res);

					### 상단 메뉴 2/3차 카테고리 가져오기 끝

					while($cateListA_row = pmysql_fetch_object($cateListA_res)){
?>
						
				<?if($cateListB[$cateListA_row->code_a]){
				?>

					<?foreach($cateListB[$cateListA_row->code_a] as $bListKey=>$bListVal){?>
						<li>
							<a href="javascript:;"	 rel="external" title="<?=$bListVal['code_name']?>">
							<?=$bListVal['code_name']?>
							</a>
							<div class="depth-third">
					<?if($cateListC[$cateListA_row->code_a.$bListVal['code_b']]){?>
						<?foreach($cateListC[$cateListA_row->code_a.$bListVal['code_b']] as $cVal){?>
								<span><a href="<?=$Dir.MDir."productlist.php?code=".$cVal['cate_code']?>"><?=$cVal['code_name']?></a></span>
						<?}?>
					<?}?>					
							</div>
                        </li>
					<?}?>
				<?}?>
<?php
					
					}
					pmysql_free_result($cateListA_res);
?>
				</ul>
<?php		}
			}
?>
		</li>
<?php
				$nav_num++;
			}
			pmysql_free_result($nav_res);
?>



	</ul>
</div><!-- //.category-all-wrap -->
</main>

<?
include_once('outline/footer_m.php')
?>

