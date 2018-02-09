<?php

$boardArray = barandBoardList($board_code,$boardSearch);
$gotopage = $boardArray[3];
$t_coiunt = $boardArray[2];
$paging = $boardArray[1];
$boardList = $boardArray[0];

?>
<style>
div.new_goods16ea ul.list.brand li {
    margin: 0 12px 13px 0;
     padding: 0 0px 0 0; 
    width: 264px;
     height: 264px; 
    border: 0 !important;
}
div.layer_goods_icon {
	border:0 none;
	width:264px !important;
	height:264px !important;
}
div.layer_goods_icon span.bg {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: #000;
    opacity: .8;
    filter: alpha(opacity=80);
}
div.layer_goods_icon span.conts {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    color: #fff !important;
    font-family: '맑은 고딕', 'Malgun Gothic', '돋움', 'dotum';
}
div.layer_goods_icon span {
	display:block;
	color:#fff;
}
div.layer_goods_icon span.conts .ttl {
	padding-top:95px;
	font-size:20px;
	line-height:30px;
}
div.layer_goods_icon span.conts .date {
	margin-top:25px;
	font-size:14px;
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

	<div class="new_goods16ea">
		<ul class="list brand">
		<?if($boardList){?>
			<?for($i=0; $i<count($boardList); $i++){?>
			<li class="in_icon">
				<div class="goods_A">
					<a href="#">
						<p class=""><img src="../data/shopimages/brandboard/<?=$boardList[$i]->thumbnail_image?>" style="max-width: 264px;" /></p>
					</a>
				</div>
				<?if($boardList[$i]->option1) $option_chk=3; else $option_chk=1;?>
				<div class="layer_goods_icon" link_url="<?=$Dir.FrontDir.'brandboard_view.php?board_num='.$boardList[$i]->board_num.'&board_code='.$board_code?>">
					<span class="bg"></span>
					<span class="conts">
						<span class="ttl">
							<?=$boardList[$i]->board_title?>
						</span>
						<span class="date">
							<?=substr($boardList[$i]->date,0,4)."-".substr($boardList[$i]->date,4,2)."-".substr($boardList[$i]->date,6,2)?>
						</span>
					</span>
				</div>
			</li>
			<?}?>
		<?}?>
		</ul>
	</div>
	<div class="paging">
		<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
	</div>

<script type="text/javascript">

function GoPage(block,gotopage) {
    document.searchForm.block.value = block;
    document.searchForm.gotopage.value = gotopage;
	document.searchForm.submit();
}
    
$(document).ready(function() {
	//Default Action
	var defaultType = 0;
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li").each(function(){
		if($(this).attr("class")=="active"){
			defaultType = 1;
			var tabId = $(this).find("a").attr("href");
			$(tabId).show();
		}
	});
	if(defaultType == 0){
		$("ul.tabs li:first").addClass("active").show(); //Activate first tab
		$(".tab_content:first").show(); //Show first tab content
	}

	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});

	$('.new_goods4ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.new_goods16ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.in_icon').mouseleave(function(){
	$('.layer_goods_icon').hide();
	});
	
	$(".layer_goods_icon").on("click",function(e){
    	var target = e.target
    	if($(target).attr("class") == "cart" || $(target).attr("class") == "view" ) return; 
    	location.href = $(this).attr("link_url");
    });
    
    $(".view").on("click",function(){
    	location.href = $(this).attr("link_url");
    });
    
	$("#searchText").keypress(function(e){
		if(e.keyCode === 13){
			e.preventDefault();
			searchBoard();
		}
	});

});
function searchBoard(){
	$("#searchBoardCode").val(document.cateChage.board_code.value);
	$("#boardSearch").val($("#searchText").val());
	$("#searchForm").submit();
}
</script>

</div>