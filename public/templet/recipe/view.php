<?
$recipe->getMyRecipe($_REQUEST[no]);
$recipe_no = $_REQUEST[no];
$sql = "SELECT * FROM tblboardadmin WHERE board='studyb' ";
$result=pmysql_query($sql,get_db_conn());
$qnasetup=pmysql_fetch_object($result);
pmysql_free_result($result);
if($qnasetup->use_hidden=="Y") $qnasetup=null;

?>
<!-- start container -->
<div id="container">
<? include "side.php" ; ?> 
	<!-- start contents -->
	<div class="recipeView_contents_side">
		<h2><img src="/image/recipe/recipe_title.png"  alt="신선한 레시피" /></h2>
		<div class="recipe_detailWrap">
		<p><img src="/image/recipe/bg_recipe_detailWrap_01.gif" /></p>
			<div class="recipe_detail">
				<ul>
				<li><img src="/image/recipe/bg_recipe_detail_01.gif" /></li>
					<li>
						<ul class="recipe_tit_map">
							<li class="recipe_ti"><a href="javascript:goViewpage('<?=$other[prev][no]?>')"><img src="/image/recipe/bt_back.gif" alt="이전 레시피보기" /></a></li>
							<li class="recipe_t2"><?=$data[subject]?></li>
							<li class="recipe_t3"><a href="javascript:goViewpage('<?=$other[next][no]?>')"><img src="/image/recipe/bt_next.gif" alt="다음 레시피보기" /></a></li>
						</ul>
					</li>
					<li class="r5"><?=$data[name]?> / Posted at <?=$data[regdt]?></li>
					<li class="r6"><?=$data[contents_tag]?></li>
					<li><img src="/image/recipe/bg_recipe_detail_03.gif" /></li>
				</ul>
			</div><!--recipe_detail 끝  -->
			<div class="recipe_item">
			<ul>
				<li class="recipe_item01"><a href = "javascript:popPrint('<?=$_REQUEST[no]?>','all');"><img src="/image/recipe/bt_recipeprint.gif" alt="레시피인쇄하기"/></a></li>
				<li class="recipe_item02"><img src="/image/recipe/explain_recipeitem.gif" alt="레시피에 사용된 제품" /></li>
				<li class="recipe_item03">
					<table width="183" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<table width="183" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top">
											<table width="150" border="0" cellspacing="0" cellpadding="0" align="center">
											<form name="recipeproduct" action="basket.exe.php" target="indb" method="post">
											<input type="hidden" name="module" value="proc">
											<input type="hidden" name="frame" value="parent">
											<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
											<?
											$i=0;
											if(is_array($product_list)){foreach($product_list as $data){
												$i++;
												$recipe_total_price += $data[price];
//												echo $data[opt1_stock];
//												echo $data[opt1_idx];
												if(!$data[stock])$status="품절";
												else $status="";
											?>
											<tr align="center">
												<td>
													<input type="hidden" name="productcode[<?=$i?>]" value="<?=$data[productcode]?>">
													<input type="hidden" name="option1[<?=$i?>]" value="<?=$data[opt1_idx]?>">
													<input type="hidden" name="option2[<?=$i?>]" value="<?=$data[opt2_idx]?>">
													<input type="hidden" name="quantity[<?=$i?>]" value="1">
													<input type="hidden" name="assemble_idx[<?=$i?>]" value="0">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														 <tr align="left">			
															<td rowspan = '2'>
															<input type = 'checkbox' name = 'idx[]' style = 'border:0px;' value = '<?=$i?>' <?=!$data[stock]?"disabled":"class='do_basket_check'"?>>
															</td>
															<td class="img_item" rowspan=2>
															<a href="/front/productdetail.php?productcode=<?=$data[productcode]?>" target="_top" valign="bottom"><img src="<?=$data[img_src]?>" width="60" align=absmiddle alt="" border="0"></a>
															</td>
															<td class="td1" colspan=3>
															<a href="#" target="_top" valign="bottom"><?=$data[productname]?> - <?=$data[option1]?></a>	
															</td>	
														 </tr>
														<? if(!$data[stock]){?>
														<tr align="left">			
															<td class="recipe_price"><span style="color:red; font-weight:bold;">품절</span></td>
															<td></td>
														</tr>
														<?}else{?>
														<tr align="left">			
															<td class="recipe_price"><?=number_format($data[price])?>원</td>
															<td><img src="/image/recipe/icon_cart.gif" border="0" style='vertical-align:middle; cursor:pointer;' class="do_basket"></td>
														</tr>
														<?}?>
													</table>
												</td>
											</tr>
											<tr><td colspan=1 class="td2"></td></tr>
											<?}}?>
												</table>
												</td>
												</tr>
												<tr><td colspan=1 class="td2"></td></tr>
											</form>
											</table>
											<div class="recipe_item_total">합계 금액 : <?=number_format($recipe_total_price)?>원</div><br>
										</td>
									</tr>
								</table>
				</li><!-- item03 li 끝 -->
			</ul>	
				<div class="recipe_bottom_item">
					<ul>
						<li><a href="javascript:basketItem();"><img src="/image/recipe/bt_selec_cartin.gif" /></a></li>
						<li><a href="#;" id="do_basket_all"><img src="/image/recipe/bt_all_cartin.gif"  /></a></li>
					</ul>
				</div>
				<div class="compare_check" >
				좌측의 레시피 내용과 비교하여<br>
				빠진게 있는지 확인해 주세요 !
				</div>
			</div><!--recipe_item 끝  -->
		   <div style="clear:both"><img src="/image/recipe/bg_recipe_detailWrap_03.gif" /></div>
   	</div><!--recipe_detailWrap 끝  -->
	<p class="attention_explain"><img src="/image/recipe/copyright.gif" width="840" height="201" border="0" alt="레시피 주의사항과 저작권 주의사항"></p>
	<div class="recipe_board">
			<a name="review">
			<table class="recipe_board_table">
				<tr>
					<th width="100" height="40" align="center" style="background:#c0c0c0"><a href="#review">레시피후기</a></th>
					<th width="100" height="40" align="center"><a href="#qna">레시피문의</a></th>
					<td width="" align="center">&nbsp;</td>
				</tr>
				<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>
			</table>			

			<table class="recipe_board_table">
			<!--
			<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>
			<tr>
				<th width="70" align="center">작성자</th>
				<th width="500" align="center">내용</th>
				<th width="70" align="center">작성일</th>
			</tr>
			<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>
			-->
			<?
			$recipe->list_size=10;
			$comment_list = $recipe->getRecipeCommentList($no);
			if(count($comment_list)){
			foreach($comment_list as $data){?>
				<tr>
					<td width="70"><?=$data[name]?></td>
					<td width="500" style="padding:10px;">
					<?=$data[comment_tag]?>
					<!--<?=getStringCut($data[comment_tag],80)?>--> &nbsp; <a class="comment_reply_btn" num="<?=$data[num]?>" onclick="return false" style="cursor:pointer">[답글]</a>
					<!--div class="comment_contents_warp" style="display:none"><?=$data[comment_tag]?></div-->
					</td>
					<td width="70" align="center"><?=date("Y-m-d",$data[writetime])?> <?if($_ShopInfo->memid && $_ShopInfo->memid==$data[c_mem_id]){?><a href="javascript:delComment(<?=$data[num]?>)">x</a><?}?></td>
				</tr>
				<tr class="comment_reply_form" style="display:none">
					<td colspan="3" >
						<table>
						<form name="comment_reply" action="/admin/recipe_indb.php" method="post" onsubmit="return addCommentReply(this)">
						<input type="hidden" name="module" value="recipe_contents">
						<input type="hidden" name="mode" value="add_comment_reply">
						<input type="hidden" name="num" value="<?=$data[parent_comment]?>">
						<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
							<tr>
								<td rowspan=2>
								<textarea name="comment" style="width:700px;min-height:89px" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
								</td>
								<td><img src="/image/recipe/icon_name.gif" alt="이름" /></td>
								<td class="bold">
								<input type="hidden" name="recipe_no" value="<?=$_REQUEST[no]?>">					
								<input type="hidden" name="memid" value="<?=$_ShopInfo->memid?>">					
								<input type="hidden" name="memname" value="<?=$_ShopInfo->memname?>">					
								<?=$_ShopInfo->memname?>
								</td>
								</tr>
								<tr>
								<td colspan=2>
								<input type=image src="/image/recipe/bt_comment.gif">
								</td>
							</tr>
						</form>
						</table>				
					</td>
				</tr>
				<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>
			<?}}else{?>
				<tr><td colspan="3" height="50" align="center"> 등록된 후기가 없습니다.</td></tr>	
				<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>

			<?}?>
				<tr><td colspan="3"><?$recipe->getPageNavi()?></td></tr>
			</table>
			<script>
			$(window).ready(function(){
				$(".comment_reply_btn").click(function(){
					var idx = $(this).index(".comment_reply_btn");
//					var frm = $("#comment_reply").val();
					var displaystate = $(".comment_reply_form:eq("+idx+")").css("display");
					$(".comment_reply_form").hide();
					if(displaystate=="none") $(".comment_reply_form:eq("+idx+")").show();
//					$(".comment_reply_form:eq("+idx+")").find("#num").val($(this).attr("num"));
				});
			});
			</script>
			<table>
			<form name="comment" action="/admin/recipe_indb.php" method="post" onsubmit="return addComment(this)">
			<input type="hidden" name="module" value="recipe_contents">
			<input type="hidden" name="mode" value="add_comment">
			<input type="hidden" name="num" value="">
			<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
				<tr>
					<td rowspan=2>
					<textarea name="comment" style="width:730px;min-height:89px" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
					</td>
					<td><img src="/image/recipe/icon_name.gif" alt="이름" /></td>
					<td class="bold">
					<input type="hidden" name="recipe_no" value="<?=$_REQUEST[no]?>">					
					<input type="hidden" name="memid" value="<?=$_ShopInfo->memid?>">					
					<input type="hidden" name="memname" value="<?=$_ShopInfo->memname?>">					
					<?=$_ShopInfo->memname?>
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<input type=image src="/image/recipe/bt_comment.gif">
					</td>
				</tr>
			</form>
			</table>

			<div class="description_qna_list" style="margin-top:80px;">
				<a name="qna">
				<table class="recipe_board_table" style="margin-top:0px;">
					<tr>
						<th width="100" height="40" align="center"><a href="#review">레시피후기</a></th>
						<th width="100" height="40" align="center" style="background:#c0c0c0"><a href="#qna">레시피문의</a></th>
						<td width="" align="center">&nbsp;</td>
					</tr>
					<tr><td colspan="3" bgcolor="#efefef" height="1"></td></tr>
				</table>	
				<?php include($Dir.FrontDir."recipe_tem001.php"); ?>
			</div>

		</div><!-- recipe_text_comment 끝 -->

		<div class="btn_recipe_footer">
			<ul>
				<li><a href="/front/myrecipe.php"><img src="/image/recipe/bt_go_recipelist.gif"  alt="레시피보관함가기" /></a></li>
				<li><a href="<?=$_REQUEST[listUrl]?>"><img src="/image/recipe/bt_list.gif"  alt="목록보기" /></a></li>
				<li><a href="javascript:loginChk('/admin/recipe_indb.php?module=recipe_contents&mode=set_my_recipe&recipe_no=<?=$_REQUEST[no]?>&returnUrl=<?=urlencode($_SERVER[REQUEST_URI])?>')"><img src="/image/recipe/bt_recipe_in.gif"  alt="레시피담기" /></a></li>
			</ul>
		</div>
		<div class="recipe_link_list">
			<ul>
				<li>
				<ul class="recipe_list_up">
					<li><a href="javascript:goViewpage('<?=$other[prev][no]?>')"><img src="/image/recipe/bt_list_up.gif" alt="이전글" /></a></li>
					<li class="bold">이전글</li>
					<li><a href="javascript:goViewpage('<?=$other[prev][no]?>')"><?=$other[prev][subject]?></a></li>
				</ul>
				</li>
				<li>
				<ul class="recipe_list_down">
					<li><a href="javascript:goViewpage('<?=$other[next][no]?>')"><img src="/image/recipe/bt_list_down.gif" alt="다음글" /></a></li>
					<li class="bold">다음글</li>
					<li><a href="javascript:goViewpage('<?=$other[next][no]?>')"><?=$other[next][subject]?></a></li>
				</ul>
				</li>
			</ul>
		</div><!-- recipe_board 끝 -->
</div><!-- //end contents_side -->

<div class="clearboth"></div>
</div><!-- //end container -->
<!--map name="print_map">
	<area shape="rect" coords="15,53,45,64" href="#" onclick="popPrint('<?=$_REQUEST[no]?>','recipe')"/>
	<area shape="rect" coords="57,53,83,64" href="#" onclick="popPrint('<?=$_REQUEST[no]?>','all')"/>
</map-->
<script>
function popPrint(no, mode){
	if(mode=="recipe"){
		if(!$("#startDiv").length){
			alert('레시피 인쇄영역이 지정되지 않았습니다.');
			return false;
		}
	}
	window.open('recipe_print.php?print='+mode+'&no=<?=$_REQUEST[no]?>','recipe_print','width=588, height=800, scrollbars=yes');
}
function basketItem(){
	if(!$(".do_basket_check:checked").length){
		alert("선택된 상품이 없습니다.");
	}else{
		alert("좌측의 레시피 내용과 비교하여 \n 빠진게 있는지 확인해 주세요 !");
		document.recipeproduct.submit();
	}
}

function loginChk(url){
	var mem_id = '<?=$_ShopInfo->memid?>';
	if(!mem_id){
		if(confirm('회원전용 입니다. \n 로그인 페이지로 이동하시겠습니까?')){
			document.location.href='/front/login.php?returnUrl=<?=$_SERVER[REQUEST_URI]?>';
		}
	}else{
		document.indb.location.href=url;
	}
}

$(window).ready(function(){
	$("#do_basket_all").click(function(){
		$(".do_basket_check").prop("checked",true);
		basketItem();
	});
	$(".do_basket").click(function(){
		var idx = $(this).index(".do_basket");
		$(".do_basket_check:eq("+idx+")").prop("checked",true);
		basketItem();
	});
});

function goViewpage(no){
	document.list.action="recipe_view.php";
	document.list.no.value=no;
	document.list.submit();
}
function addComment(frm){
	if(!Validate(frm)){
		return false;
	}
	document.comment.mode.value='add_comment';
	return true;
}

function addCommentReply(frm){
	if(!Validate(frm)){
		return false;
	}
	frm.mode.value='add_comment_reply';
	return true;
}

function delComment(num){
	if(confirm("정말 삭제하시겠습니까?")){
		document.comment.mode.value='del_comment';
		document.comment.num.value=num;
		document.comment.submit();
	}
}
</script>
<iframe name="indb" style="display:none;" src=""></iframe>