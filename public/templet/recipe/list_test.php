
<!-- start container -->

<script language="JavaScript">
	theDate= new Date();
	months = new Array();
	for(var i=1;i<=12;i++){
		months[i] ="image/recipe/monthly_title"+i+".jpg";
	}
	

	function printDate() {
		document.write('<img src="../' + months[theDate.getMonth()+1] + '">'); 

	}

	$(document).ready(function(){	
		var sudoSlider = $(".recipe_banner").sudoSlider({
			effect: "slide",
			continuous:true,
			slideCount:1,
			prevNext:false,
			updateBefore: true,
			moveCount:1,
			customLink:'a.recipenavibt',
			auto:true
	   });
	});
</script>


<div id="container">
<? include dirname(__FILE__)."/side.php" ; ?> 
	<!-- start contents -->
	<div class="contents_side">
	<form name="list" action="<?=$_SERVER[PHP_SELF]?>">
	<input type="hidden" name="page_no" value="">
	<input type="hidden" name="no" value="">
	<input type="hidden" name="listUrl" value="">
	<input type="hidden" name="code" value="<?=$_REQUEST[code]?>">

	<div class="recipe_visual"> 
		<h2><img src="../image/recipe/recipe_title.png"  alt="신선한 레시피"></h2>

		<div class="recipe_header"> 
			<div class="recipe_header_title"><script language="JavaScript">printDate();</script></div>
			<div class="recipe_header_banner">

			<ul class="recipe_banner">
				<?
					
					for($i=0;$i<count($banner_arr['img']);$i++){
				?>
					<li><a href="<?=$banner_arr['link'][$i]?>"><img src="../data/shopimages/mainbanner/<?=$banner_arr['img'][$i]?>"></a></li>
				<?}?>
			</ul>		

			<span class="recipenavi">
				<a href="#" class="recipenavibt" rel="1">1</a>/
				<a href="#" class="recipenavibt" rel="2">2</a>/
				<a href="#" class="recipenavibt" rel="3">3</a>
			</span>

			</div>
			<div class="recipe_header_right"><img src="../data/shopimages/mainbanner/<?=$banner_arr2['img'][0]?>"></div>
		</div>


		<p class="new_recipe_tag"><img src="../image/recipe/tag_new_recipe.png" alt="뉴레시피 태그"></p>
	</div>
	<div class="recipe_list">
		<div class="search_recipe">
			<ul>
				<li><input type=checkbox name="search_field[]" value="all" id = 'searchAll' <?if(in_array("all",$_REQUEST[search_field]) || $_REQUEST[search_field]==''){ echo "checked";}?>>&nbsp;통합검색</li>
				<li><input type=checkbox name="search_field[]" value="subject" <?=in_array("subject",$_REQUEST[search_field])?"checked":""?>>&nbsp;제목</li>
				<li><input type=checkbox name="search_field[]" value="name" <?=in_array("name",$_REQUEST[search_field])?"checked":""?>>&nbsp;이름</li>
				<li><input type=checkbox name="search_field[]" value="contents" <?=in_array("contents",$_REQUEST[search_field])?"checked":""?>>&nbsp;내용</li>
				<li><input type="text" name="search_word" value="<?=$_REQUEST[search_word]?>" style="border:1px solid #cccccc; background-color:transparent; height:22px; color: #565454; font-size: 9pt; font-family:verdana" ></li>
				<li><input type = 'image' src="/image/recipe/bt_search_recipe.gif"></li>
			</ul>
		</div>
	<div class="recipe_gallery_list">
		<?
		$i=0;
		if(is_array($list)){
			foreach($list as $data){
				$i++;
				$detaillink = "recipe_view.php?no=".$data[no]."&listUrl=".urlencode($_SERVER[REQUEST_URI]);
				$detaillink = "javascript:goViewpage('".$data[no]."','".urlencode($_SERVER[REQUEST_URI])."')";
				
				$time=date("Y-m-d",time());
				$time2=date("Y-m-d",strtotime("-2 days"));
		?>
		<?if(($i%5)==1){?>
		<ul class="gallery_lineWarp">
		<?}?>
			<li class="gallery_cell">
					  <dl>
					  <dt><a href="<?=$detaillink?>"><img src="<?=$data[timg_src]?>" width="150" height="150"></a></dt>
					  <dd><?IF($time2<=$data[regdt] && $time>=$data[regdt]){?><img src="../images/common/icon03.gif"><?}?><a href="<?=$detaillink?>"><?=$data[subject]?></a></dd>
					  <dd class="gallery_cell_date"><?=$data[regdt]?></dd>
					  </dl>
					 
			</li>
		<?if(($i%5)==0){?>
		</ul>
		<?}?>
		<?}?>
		<?
		if(($i%5)!=0){?>
		</ul>
		<?}?>
		<?}else{?>
		<?}?>
	</div> <!-- recipe_gallery_list 끝 -->
	<div class="paging">
		<?$recipe->getPageNavi()?>
	</div><!-- paging 끝 -->
</div> <!-- recipe_list 끝 -->
</form>
</div><!-- //end contents_side -->

<div class="clearboth"></div>

</div><!-- //end container -->
<script>
function goPage2(no){
	document.list.action="recipe.php";
	document.list.page_no=no;
	alert();
//	document.list.submit();
}

function goViewpage(no, listurl){
	document.list.action="recipe_view.php";
	document.list.no.value=no;
	document.list.listUrl.value=listurl;
	document.list.submit();
}
</script>