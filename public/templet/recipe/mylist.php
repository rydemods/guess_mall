<div id="container">
	<?include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	<div class="contents_side">
		<? include $Dir.FrontDir."mypage_menu.php";?>
		<div class="my_wish_detail">

			 <div class="my_wishlist_title">
			 <span class="my_wishlist_titleimg"><img src="../image/mypage/wish_wish_title.gif" alt="상품보관함"></span>
			 </div>

             <div class="my_wishlist_warp">

             <div class="my_wishlist_bar">
			 <ul>
			 <li class="cell10"><A HREF="javascript:CheckBoxAll()"  class="btn_white_check"><img src="../image/mypage/wish_menu01.gif"  alt="선택"></a></li>
			 <li class="cell10"><img src="../image/mypage/wish_menu01_02.gif"  alt="제품이미지"></li>
			 <li class="cell65"><img src="../image/mypage/wish_menu02.gif"  alt="상품정보"></li>
			 <li class="cell15"><img src="../image/mypage/wish_menu05.gif" alt="보관날짜"></li>
			 </ul>
			 </div>

			 <div class="my_wishlist_list">
			 <form name="recipe_form" action="/admin/recipe_indb.php" method="post">
			 <input type="hidden" name="module" value="recipe_contents">
			 <input type="hidden" name="mode" value="del_my_recipe">
			 <input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
			 <?if(is_array($list)){foreach($list as $data){
				 $link = "../front/recipe_view.php?no=".$data[recipe_no];
			 ?>
			 <ul>
			  <li class="cell10"><input type=checkbox name=myrecipe_no[] value="<?=$data[no]?>" style="BORDER:none;" class="recipe_no"></li>
			  <li class="cell10">
				<A HREF="<?=$link?>" onmouseover="window.status='레시피 상세페이지';return true;" onmouseout="window.status='';return true;">
				<img src="<?=$data[timg_src]?>" width="40" border=0>				
				</a>
			  </li>
			  <li class="cell65 textAlignIs">
				<A HREF="<?=$link?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><?=$data[subject]?></a>
			 </li>
			 <li class="cell15"><?=$data[regdt]?></li>
			 </ul>
			 <?}}?>
			
			<?$recipe->getPageNavi()?>

			<div style="width:100%; text-align:center; margin:30px auto;">
			<a href="#" onclick="selected_delete(recipe_form)"><img src="../image/community/bt_del.gif" style="display:inline"></a>
			<!--<input type="button" value="선택 삭제" onclick="selected_delete(this.form)">-->
			</div>
			</form>
			 </div><!-- my_wishlist_list 끝 -->
			
			 </div><!-- my_wishlist_warp 끝 -->

		</div>
	</div>
</div>
<script>
	function selected_delete(frm){
		if($(".recipe_no:checked").length==0){
			alert("선택된 레시피가 없습니다.");
		}else{
			if(confirm("정말 삭제하시겠습니까?")){
				frm.submit();
			}
		}
	}
</script>
