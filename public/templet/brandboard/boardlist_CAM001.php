<?php
/*
$boardArray = barandBoardList($board_code,$boardSearch);
$gotopage = $boardArray[3];
$t_coiunt = $boardArray[2];
$paging = $boardArray[1];
$campaign = $boardArray[0];

//페이징 변환
$pageItem = explode("</a>",$paging->print_page);
if ( $pageItem ) {
	foreach( $pageItem as $pageKey=>$pageVal ) {
		if( $posNum = strpos( $pageVal,"class=" ) ){
			$pageNum[$pageKey]['class'] = substr( $pageVal, $posNum + 7, 2 );
			$pageNum[$pageKey]['href']  = "javascript:;";
		}else if( $posNum = strpos( $pageVal,"href=" ) ) {
			$pageNum[$pageKey]['class'] = "";
			$pageNum[$pageKey]['href']  = substr( $pageVal, $posNum + 6 ,  strpos( $pageVal,";" ) - ($posNum + 6) );
		}
	}
}
*/
$sortBoardCate = sortBoardCate( $board_code, $page_code );
if( !$page_code ) {
	$page_code = $sortBoardCate['on']['page_code'];
}
$sortBoardList = sortBoardList($page_code,$board_code,$boardSearch);

?>
<style>
	div.campaing_list a {
		/*max-width: 225px; max-height: 317px; */
		/*width: 225px; height: 317px; overflow:hidden; display: inline-block; vertical-align: top;*/
		
	}
	div.campaing_list a img {
		/*overflow:hidden;*/
		/*max-width: none; max-height: 100%;*/
		max-width: 225px; max-height: 317px; 
	}
</style>
<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		<?=$boardCate->board_name?>
		<p class="line_map"><a>홈</a> &gt; <a>BRAND</a> &gt; <span><?=$boardCate->board_name?></span></p>
		<? if($brandBoardCate){ ?>
		<div style="position:absolute; right: 0; top: 25px;">
			<form name="cateChage" id="cateChage" method="GET" style="position:absolute; right: 200px; top: 25px;" action="<?=$_SERVER['PHP_SELF']?>">
				<select name="board_code" onchange="this.form.submit()">
				<? foreach($brandBoardCate as $boardKey=>$boardVal){?>
					<option value="<?=$boardVal->board_code?>" <? if($boardVal->board_code == $board_code) echo "SELECTED"; ?> ><?=$boardVal->board_name?></option>
				<? } ?>
				</select>
			</form>
			<input type="text" id="searchText" value="<?=$boardSearch?>" style="position:absolute; right: 25px; top: 25px;" />
			<a href="javascript:searchBoard();" style="position:absolute; right: 0; top: 25px;"><img src="<?=$Dir."img/Search_Button.png"?>" style="width: 20px;"/></a>
		</div>
		<? } ?>
	</h3>

	<div class="campaing-wrap">
	
		<!-- 2015.08.11 수정 S -->
		<div class="campaing-wrap_top">
			<div class="inner">
				<a href="#" class="link-box"><?=$sortBoardCate['on']['page_name']?><img src="./img/bg_movie_arr.gif" alt=""></a>
				<ul class="campaing-wrap_view">
				<?	if ( $sortBoardCate ) {
						foreach ( $sortBoardCate as $sortKey=>$sortVal ) { 
							if( $sortKey !== "on" ) {
				?>
					<li><a class="" href="<?=$Dir.FrontDir."brandboard_list.php?board_code=".$sortVal[board_code]."&page_code=".$sortVal[page_code]?>"><?=$sortVal[page_name]?></a></li>
				<? 		
							} else if ( $boardSearch != "" ) {
				?>
					<li><a class="" href="<?=$Dir.FrontDir."brandboard_list.php?board_code=".$sortVal[board_code]."&page_code=".$sortVal[page_code]?>"><?=$sortVal[page_name]?></a></li>
				<?
							}
						}
					} 
				?>
				</ul>
			</div>
		</div>
		<!-- 2015.08.11 수정 E -->
	
		<!-- list S -->
		<div class="campaing_list">
		<?if($sortBoardList){?>
			<?for($i=0; $i<count($sortBoardList); $i++){?>
				<a href="#campaing_layer<?=$i?>" boardNum="<?=$sortBoardList[$i][board_num]?>" >
					<img src="../data/shopimages/brandboard/<?=$sortBoardList[$i][thumbnail_image]?>" >
				</a>
			<?}?>
		<?}?>
		</div>
		<!-- //list E -->

		<!-- layer S -->
	<?if($sortBoardList){?>
		<?for($i=0; $i<count($sortBoardList); $i++){?>
		<div class="campaing_layer <?=$i?>" id=<?=$i?>>
			<div class="layer_con">
				<div class="control">
					<?if($i !=0){?>
					<a href="#" class="prev"><img src="../img/btn/btn_layer_prev.png" alt="이전" /></a>
					<?}?>
					<?if($i != count($sortBoardList)-1){?>
					<a href="#" class="next"><img src="../img/btn/btn_layer_next.png" alt="다음" /></a>
					<?}?>
				</div>			

				<!-- -->
				
				<div class="con_inner" id="campaing_layer<?=$i?>" boardNum="<?=$sortBoardList[$i][board_num]?>" value=<?=$i?>>
					<div class="campaing_top">
						<ul class="left">
							<li><a href="#"><img src="../img/btn/btn_facebook.gif" alt="facebook" /></a></li>
							<li><a href="#"><img src="../img/btn/btn_kakao.gif" alt="카카오톡" /></a></li>
							<li><a href="#"><img src="../img/btn/btn_instar.gif" alt="인스타그램" /></a></li>
						</ul>
						<span class="right"><img src="../img/btn/btn_shop.gif" alt="Shop" /></span>
					</div>	
					<div class="campaing_view">
						<div class="campaing_big"><img src="../data/shopimages/brandboard/<?=$sortBoardList[$i][big_image]?>" alt="" /></div>
						
						<div class="campaing_product">
						<?$item = Item($sortBoardList[$i][board_num])?>
						<?foreach($item as $val){?>
							<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$val->productcode?>"><img src="../data/shopimages/product/<?=$val->minimage?>" width="133" height="130" alt="" /></a>
						<?}?>
						</div>
					</div>
				</div>
				<!-- //-->

				<a href="#" class="close"><img src="../img/btn/btn_layer_close.png" alt="닫기" /></a>
			</div>
		</div>
		<?}?>
	<?}?>
		<!-- //layer E -->
	</div>

	<!--<div class="paging">
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
	</div>-->
	
	<form id="paging" name="paging" method=GET action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
        <input type="hidden" name="block" value="<?=$block?>"/>
		<input type="hidden" name="brand_code" value="<?=$brand_code?>"/>
	</form>
	
	<script type="text/javascript">

	function GoPage(block,gotopage) {
	    document.searchForm.block.value = block;
	    document.searchForm.gotopage.value = gotopage;
		document.searchForm.submit();
	}

	$(function(){
		/* layer */
		$(".campaing_list a").on('click', function(ev) {
			ev.preventDefault();
			currentPosition = $('.campaing_list a').index($(this));
		
			var layer_obj = $(this).attr('href');
			$('.campaing_layer .con_inner').hide();
			//alert($(layer_obj).parent().parent().attr('class'));
			boardCnt($(this).attr('boardnum'));
			$(layer_obj).parent().parent().css({height:$('window').height()}).fadeIn('fast');	
			$(layer_obj).parent().css({marginTop:- + $(layer_obj).outerHeight()/2 , marginLeft:- + $(layer_obj).outerWidth()/2});
			$(layer_obj).show();
		});		

		$('.close').on('click', function(ev){
			ev.preventDefault();

			$(this).parent().parent().fadeOut('fast');	
			//alert($(this).parent().parent().attr('id'));
		});

		/* rolling */
		var currentPosition = 0;
		var slideWidth = $('.campaing_layer .con_inner').width(); 
	    var slides = $('.campaing_layer .con_inner');
		var numberOfSlides = slides.length;
		var layer;
		$('.campaing_layer .control a').on('click', function(ev){    
			ev.preventDefault();

			//$('.campaing_layer .con_inner').hide();
			
			if ($(this).hasClass('next')) { 
				//if(currentPosition<numberOfSlides-1) currentPosition++;
				//$(".campaing_layer" +'.'+currentPosition).css('display','none');
				$("#"+currentPosition).fadeOut('fast');
				currentPosition++;
			} else {
				//if(currentPosition>0)currentPosition--;
				$("#"+currentPosition).fadeOut('fast');
				currentPosition--;
			}
			//$(".campaing_layer" +'.'+currentPosition).css('display','block');
			//slides[currentPosition].style.display = 'block';
			var layer_obj = "#campaing_layer"+currentPosition;
			boardCnt($(layer_obj).attr("boardnum"));
			$(layer_obj).parent().parent().css({height:$('window').height()}).fadeIn('fast');	
			$(layer_obj).parent().css({marginTop:- + $(layer_obj).outerHeight()/2 , marginLeft:- + $(layer_obj).outerWidth()/2});
			$(layer_obj).show();
		});
		
		$("#searchText").keypress(function(e){
			if(e.keyCode === 13){
				e.preventDefault();
				searchBoard();
			}
		});
	});	
	
	function boardCnt(boardNum){
		$.post("ajax_brandboard_count.php",{board_num:boardNum});
	}
	
	
	function searchBoard(){
		$("#searchBoardCode").val(document.cateChage.board_code.value);
		$("#boardSearch").val($("#searchText").val());
		$("#searchForm").submit();
	}
	</script>

</div>