<script type="text/javascript" src="/js/sidemenu.js"></script>

	<div id="side">
	<div class="sideWrap">
	<h3><img src="/image/main/side_recipe_tit.gif" alt="recipe book" /></h3>

<div class="side_menu_warp">
			<!-- slide start -->

						<div style="position:relative;">
						<div class="sidemenutabs">
							<a href="#" class="sidemenu tab1" rel="1"></a>
							<a href="#" class="sidemenu tab2" rel="2"></a>
							<a href="#" class="sidemenu tab3" rel="3"></a>
						</div>
					   <div id="sidemenu_contents" class="sidemenu_contents">
							<ul class="sidemenu_contents_ul">				
							<li class="sidemenu_contents_li">

<div id="dhtmlgoodies_menu">
	<ul>
		<?$cate2 = $recipe->getRecipeCategoryList("001000000000");?>
		<?foreach($cate2 as $v2){?>
			<li><a href="/front/recipe.php?code=<?=$v2[code]?>"><?=$v2[code_name]?></a>
		<?$cate3 = $recipe->getRecipeCategoryList($v2[code]);?>
		<?if($cate3) echo "<ul>";?>
		<?foreach($cate3 as $v3){?>
			<li><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></li>
		<?}if($cate3) echo "</ul>";?>
		</li>
		<?}?>
	</ul>
</div>

</li>
<li class="sidemenu_contents_li">
							
<div id="dhtmlgoodies_menu2">							
			<ul>
				<li class="type2">
				  <dl>
					<dt class="blind">기능별</dt> 
					<?$cate2 = $recipe->getRecipeCategoryList("002000000000");?>
					<?foreach($cate2 as $v2){?>
						<dd><a href="/front/recipe.php?code=<?=$v2[code]?>"><?=$v2[code_name]?></a></dd>
					<?}?>
				  </dl>
				</li>
			</ul>						
</div>							
							
							</li>
							<li class="sidemenu_contents_li">
							
	<div id="dhtmlgoodies_menu2">							
							
	<ul>
				<li class="type1">
				  <dl>
					<dt class="blind">타입별</dt> 
					<?$cate2 = $recipe->getRecipeCategoryList("003000000000");?>
					<?foreach($cate2 as $v2){?>
						<dd><a href="/front/recipe.php?code=<?=$v2[code]?>"><?=$v2[code_name]?></a></dd>
					<?}?>
				  </dl>
				</li>
				</ul>						
							
	</div>					
							</li>
							</ul>
						</div>
						</div>


</div>


<script type="text/javascript">
$(document).ready(function() {
  var $search = $('#search').addClass('overlabel');
  var $searchInput = $search.find('input');
  var $searchLabel = $search.find('label');
  
  if ($searchInput.val()) {
    $searchLabel.hide();
  }

  $searchInput
  .focus(function() {
    $searchLabel.hide();
  })
  .blur(function() {
    if (this.value == '') {
      $searchLabel.show();
    }
  });
  
  $searchLabel.click(function() {
    $searchInput.trigger('focus');
  });
});

</script>
<style type="text/css">
.s_search form label{ display:block;color:#eb5350;}
.s_search .overlabel{ position: relative;}
.s_search .overlabel label{ position:absolute; top:1px; left:3px; cursor:text;}

</style>


   <div class="s_search" >
     <form method="get" action="/front/recipe.php" id="search">
	 <input type="hidden" name="search_field[]" value="all">
	 <label for="keyword1" style="margin-top:10px;">레시피를 검색해보세요</label>
				<fieldset>
					  <legend>검색</legend>					
   					  <ul>
					  	<li style="float:left"><input id="keyword1" name="search_word" class="inputTypeText1" style="margin-top:5px;" value="<?=$_REQUEST[search_word]?>" type="text" /></li>
					  	<li><input type="image" src="/image/main/side_search_btn.gif" alt="검색" /></li>
					  </ul>

				</fieldset>
	</form>
	</div>
	<div class="s_banner">
	<a href="../board/board.php?board=tip"><img src="/image/main/side_banner.gif" alt="D.I.Y academy" /></a>
	</div>
	
	

   </div><!-- //end sideWrap -->
    <?
	if($_SERVER[PHP_SELF]!="/main/main.php" && $_SERVER[PHP_SELF]!="/index.php"){
	$rcomment = new RECIPE();
	$rcomment->list_size=3;
	$rcomment->page_no=1;
	$rlist = $rcomment->getRecipeCommentList();

	?>
	<div class="recipe_side_board">
	<ul>
		<li><a href="/front/recipe_calcu.php"><img src="/image/recipe/soap_calculator.jpg" alt="비누화값 계산기" /></a></li>
		<li>
		<dl>
		  <dt><img src="/image/recipe/recipe_review_tit.gif" alt="레시피리뷰" /></dt>
			<?
			foreach($rlist as $rdata){
			$link = "/front/recipe_view.php?no=".$rdata[no]."&listUrl=".urlencode("/front/recipe.php");
			?>

			<dd>
			<table summary="">
			<caption>레시피후기</caption>
				<tr>
				   <td rowspan=2><a href="<?=$link?>"><img src="<?=$rdata[timg_src]?>" alt="image" width="50" height="50" border="0" alt="레시피이미지"/></a></td>
				   <td class="bold"><a href="<?=$link?>"><?=$rdata[subject]?></a></td>
				 </tr>
				 <tr>
				   <td><a href="<?=$link?>"><?=getStringCut($rdata[comment],50)?></a></td>
				</tr>
			</table>
			</dd>
			<?}?>
		</dl>
		<p><a href="/front/recipe_review.php"><img src="/image/recipe/recipe_review_bt_more.gif" alt="더보기" /></a></p>
		</li>
	</ul>
	</div>
	<?}?>
   </div><!-- //end side -->