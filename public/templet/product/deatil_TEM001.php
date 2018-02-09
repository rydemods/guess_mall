<?
include_once dirname(__FILE__)."/../../lib/product.class.php";
$product = new PRODUCT();
$dc_data = $product->getProductDcRate($productcode);
?>

<script type="text/javascript">
	var gBlock = 0;
	var gGotopage = 1;
	var gqBlock = 0;
	var gqGotopage = 1;
	$(function(){
		/*
		$('div.top_line_banner a.close').click(function(){
			$('div.top_line_banner').slideUp(500);
		});
		$('a.btn_quick_close').click(function(){
			$('div.right_quick_menu_wrap').css('display' , 'none');
		});
		$('a.btn_quick_open').click(function(){
			$('div.right_quick_menu_wrap').css('display' , 'block');
		});
		$('a.btn_quick_close').click(function(){
			$('a.btn_quick_close , a.btn_quick_open').css('right' , '0px');
		});
		$('a.btn_quick_open').click(function(){
			$('a.btn_quick_close , a.btn_quick_open').css('right' , '105px');
		});*/






		/*
			상품 Review Start
		*/
		$(".reviewStars li").click(function(){
			$(this).parent().prev().html($(this).children().html());
			$(this).parent().parent().removeClass('open');
			$('#rmarks').val($(this).children().attr('star'));
		})
		/*
		$(document).on("click", ".reviewCommentShowAjax", function(){
			$(this).parent().next().children().slideDown(400);
		});
		*/
		$(document).on("click", ".reviewCommentReportAjax", function(){
		});
		$(document).on("click", ".reviewContentsDeleteAjax", function(){
			if(confirm('해당 리뷰를 삭제하시겠습니까?')){
				$.ajax({
					type: "POST",
					url: "../front/prreview_tem001_comment_proc.php",
					data: "num="+$(this).prev().val()+"&productcode="+$("input[name='productcode']").val()+"&mode=deleteReview"
				}).done(function ( data ) {
					$("#reviewTotalCount").html(data);
					$(".reviewTotalMenuBar").html("("+$("#reviewTotalCount").html()+")");
					$(".goods_right_review_list").load("../front/prreview_tem001_right.php?productcode="+$("input[name='productcode']").val());
					GoPageAjax(gBlock, gGotopage);
				});
			}
		});
		$(document).on("click", ".reviewCommentDeleteAjax", function(){
			if(confirm('해당 리플을 삭제하시겠습니까?')){
				var objComment = $(this).parent().parent().parent().parent();
				$.ajax({
					type: "POST",
					url: "../front/prreview_tem001_comment_proc.php",
					data: "no="+$(this).prev().val()+"&num="+$(this).prev().prev().val()+"&mode=deleteReviewContents"
				}).done(function ( data ) {
					$(objComment).html(data);
				});
			}
		});
		$(document).on("click", ".reviewCommentAjax", function(){
			var objText = $(this).prev();
			var objComment = $(this).parent().next();
			$.ajax({
				type: "POST",
				url: "../front/prreview_tem001_comment_proc.php",
				data: "num="+$(this).prev().prev().val()+"&contents="+$(this).prev().val()+"&mode=write"
			}).done(function ( data ) {
				$(objComment).html(data);
				$(objText).val('');
			});
		});
		$(".reviewTotalMenuBar").html("("+$("#reviewTotalCount").html()+")");


		$(".goods_right_review_list").load("../front/prreview_tem001_right.php?productcode="+$("input[name='productcode']").val());
		$(".view_list_wrap").load("../front/prvcount_tem001_right.php");
		/*
			상품 Review End
		*/





		/*
			상품 QNA Start
		*/
		$(document).on("click", ".chkQnaPasswd", function(){
			var obj = $(this);
			$.ajax({
				type: "POST",
				url: "../front/prqna_tem001_pass_proc.php",
				data: "passwd="+$(this).prev().val()+"&id_num="+$(this).attr('idx')
			}).done(function ( data ) {
				if(data == '1'){
					$(obj).parent().hide();
					$(obj).parent().next().show();
				}else{
					alert("비밀번호가 틀렸습니다.");
					$(obj).prev().val('');
					$(obj).prev().focus();
					$(obj).parent().show();
					$(obj).parent().next().hide();
				}
			});
		})

		$(document).on("click", ".qnaViewPanel", function(){
			if($(this).next().css('display') == 'none'){
				$(this).next().show();
			}else{
				$(this).next().hide();
			}


		})
		/*
			상품 QNA End
		*/



		/*
			상품 할인율 Start
		*/
		if($("#ID_priceDcPercent").val() > 0){
			$("#ID_priceDcPercentLayer").html("단독 "+$("#ID_priceDcPercent").val()+"% 할인");
		}
		/*
			상품 할인율 End
		*/



		/*
			URL복사 Start
		*/
		$(".CLS_urlcopy").click(function(){
			var trb = $("#ID_faceboolMallUrl").val();
			var IE=(document.all)?true:false;
			if (IE) {
				if(confirm("이 글의 트랙백 주소를 클립보드에 복사하시겠습니까?"))
				window.clipboardData.setData("Text", trb);
			} else {
				temp = prompt("이 글의 트랙백 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", trb);
			}
		})
		/*
			URL복사 End
		*/







	});
	function GoPageAjax(block,gotopage) {
		gBlock = block;
		gGotopage = gotopage;
		$.ajax({
			type: "GET",
			url: "../front/prreview_tem001_proc.php",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			data: "productcode="+$("input[name='productcode']").val()+"&block="+block+"&gotopage="+gotopage+"&setting_reviewlist="+$("#setting_reviewlist").val()
		}).done(function ( data ) {
			$("#boardCommentAjax").html(data);
		});
	}

	function GoPageAjaxQna(qblock, qgotopage) {
		gqBlock = qblock;
		gqGotopage = qgotopage;
		$.ajax({
			type: "GET",
			url: "../front/prqna_tem001_proc.php",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			data: "pridx="+$("#qnaPridx").val()+"&block="+qblock+"&gotopage="+qgotopage
		}).done(function ( data ) {
			$("#boardQnaAjax").html(data);
		});
	}



</script>
<script src="../js/jquery.elevatezoom.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/jcarousellite_1.0.js" ></script>
<div class="container960">
	<!-- 배너 -->
	<?
		$imagepath=$Dir.DataDir."shopimages/mainbanner/";
		
		$sql = "select * from tblmainbannerimg where banner_no='63' and banner_hidden = 1 ";
		$result = pmysql_query($sql,get_db_conn());
	?>
	<? if ($row=pmysql_fetch_object($result)) { ?>
		<p class="view_top_banner mt_20">
			<a href="<?=$row->banner_link?>" target="_blank">
			<img src="<?=$imagepath.$row->banner_img?>" alt="<?=$row->banner_title?>" border=0 align=absmiddle>
			</a>
		</p>
	<? } ?>

	<!--
	<p class="view_top_banner mt_20">
		<a href="">
			<img src="../img/test/test_view_top.jpg" alt="" />
		</a>
	</p>
	-->
	<!-- 배너 -->
	<div class="goods_view_detail_wrap">
		<!-- 좌측정보 -->
		<div class="left_thumb">
			<img src="<?=$Dir.DataDir."shopimages/product/".$_pdata->maximage?>" alt="" />
			<div class="share_icon">
				<span>공유하기</span>
				<a href="<?=$facebookurl?>"><img src="../img/icon/icon_share_f.gif" alt="페이스북" class="facebook" /></a>
				<a href=""><img src="../img/icon/icon_share_t.gif" alt="트위터" /></a>
				<a href=""><img src="../img/icon/icon_share_k.gif" alt="카카오톡" /></a>
			</div>
			※실제이미지는 차이가 있을 수 있습니다
		</div><!-- //div.left_thumb -->
		<div class="center_info_wrap">
		<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
			<p class="sub_txt"><?=$_pdata->mdcomment?></p>
			<table class="goods_info">
				<caption><?=$_pdata->productname?></caption>
				<colgroup><col style="width:110px"/><col style="width:auto"/></colgroup>
				<?
					$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
					$SellpriceValue=0;
					if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0){
				?>
				<tr>
					<th>판매가</th>
					<td class="price"><span></span>원
					</td>
				</tr>
				<?
						$prdollarprice="";
						$priceindex=0;
					} else if(strlen($optcode)==0 && strlen($_pdata->option_price)>0) {
						$option_price = $_pdata->option_price;
						$option_consumer = $_pdata->option_consumer;
						$option_reserve = $_pdata->option_reserve;
						$pricetok=explode(",",$option_price);
						$consumertok=explode(",",$option_consumer);
						$reservetok=explode(",",$option_reserve);
						$priceindex = count($pricetok);
						for($tmp=0;$tmp<=$priceindex;$tmp++) {
							$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
							$spricetok[$tmp]=number_format($pricetok[$tmp]);
							$pricetok[$tmp]=number_format(getProductSalePrice($pricetok[$tmp], 0));
							if(!$consumertok[$tmp]){
								$consumertok[$tmp] = 0;
							}else{
								$consumertok[$tmp]=number_format($consumertok[$tmp]);
							}
							$reservetok[$tmp]=number_format($reservetok[$tmp]);
						}
				?>
				<tr>
					<th>판매가</th>
					<td class="price"><?=number_format(str_replace(",","",$pricetok[0]))?>원
					</td>
				</tr>
				<?
						$SellpriceValue=str_replace(",","",$pricetok[0]);
					} else if(strlen($optcode)>0) {
				?>
				<tr>
					<th>판매가</th>
					<td class="price"><?=number_format($_pdata->sellprice)?>원
						<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
						<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					</td>
				</tr>
				<?
						$SellpriceValue=$_pdata->sellprice;
					} else if(strlen($_pdata->option_price)==0) {
						if($_pdata->assembleuse=="Y") {
				?>
				<tr>
					<th>판매가</th>
					<td class="price">
						<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>원
						<input type=hidden name=price value="<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format(($miniq>1?$miniq*$_pdata->consumerprice:$_pdata->consumerprice))?>">
						<input type=hidden name=o_reserve value="<?=number_format(($miniq>1?$miniq*$_pdata->option_reserve:$_pdata->option_reserve))?>">
					</td>
				</tr>
				<?
					$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
					} else {
				?>
				<tr>
					<th>판매가</th>
					<td class="price" >
						<?=number_format($_pdata->sellprice)?>원
						<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=ID_sellprice id="ID_sellprice" value="<?=$_pdata->sellprice?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
						<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					</td>
				</tr>

				<?
							$SellpriceValue=$_pdata->sellprice;
						}
						$priceindex=0;
					}
				?>
				<?
					if($couponDownLoadFlag){
						if($goods_sale_type <= 2){
							$couponDcPrice = ($SellpriceValue*$goods_sale_money)*0.01;
							$couponDcPrice = ($couponDcPrice / pow(10, $goods_amount_floor)) * pow(10, $goods_amount_floor);
							$goods_dc_coupong = number_format($goods_sale_money)."%";
						}else{
							$couponDcPrice = $goods_sale_money;
							$goods_dc_coupong = number_format($goods_sale_money)."원";
						}
						if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
							$couponDcPrice = $goods_sale_max_money;
						}
						$coumoney = $couponDcPrice;
					}
				?>


				<?
					if(strlen($_pdata->option1)>0) {
						$temp = $_pdata->option1;
						$tok = explode(",",$temp);
						$optprice = explode(",", $_pdata->option_price);
						$optcode = "";
						if($_pdata->optcode){
							$optcode = explode(",", $_pdata->optcode);
						}

						$count=count($tok);

						if ($priceindex!=0) {
							$onchange_opt1="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
							if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
							else $onchange_opt1.="''";
							$onchange_opt1.=")";
							$onchange_opt1.="\"";
						} else {
							$onchange_opt1="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
							if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
							else $onchange_opt1.="''";
							$onchange_opt1.=")";
							$onchange_opt1.="\"";
						}
						$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
				?>
				<?if($_pdata->reserve>0){
					$getReserveConversion = getReserveConversion($_pdata->reserve, $_pdata->reservetype, $_pdata->sellprice,'Y');
				?>
				<tr>
					<th>적립금</th>
					<td><?=number_format($getReserveConversion)?>원</td>
				</tr>
				<?}?>
				<tr>
					<td colspan="3" class="line_1px" ><em></em></td>
				</tr>
				<tr>
					<th><?=$tok[0]?></th>
					<td>
						<div class="select_type" style="width:180px;z-index:10;">
							<span class="ctrl"><span class="arrow"></span></span>
							<button type="button" class="myValue">옵션을 선택해주세요</button>
							<ul class="aList selectOption1">
								<?for($i=1;$i<$count;$i++) {?>
									<li>
									<?if(strlen($tok[$i]) > 0) {?>
										<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
											<a href="javascript:;" opt = ''><strike><?=$tok[$i]." [품절]"?></strike></a>
										<?}else{?>
											<a href="javascript:;" opt = '<?=$i?>' opt1='<?=$optprice[$i-1]?>'><?=$tok[$i]?></a><?//." (".number_format($optprice[$i-1])."원) (".$optcode[$i-1].")"?>
										<?}?>
									<?}?>
									</li>
								<?}?>
							</ul>
						</div>

						<input type = 'text' name = 'option1' id = 'ID_option1'>
					</td>
				</tr>
				<?
					}
				?>

				<?
					$onchange_opt2="";
					if(strlen($_pdata->option2)>0) {
						$temp = $_pdata->option2;
						$tok = explode(",",$temp);
						$count2=count($tok);
						$onchange_opt2.="onchange=\"change_price(0,";
						if(strlen($_pdata->option1)>0) $onchange_opt2.="document.form1.option1.selectedIndex-1";
						else $onchange_opt2.="''";
						$onchange_opt2.=",document.form1.option2.selectedIndex-1)\"";
				?>
				<tr>
					<th><?=$tok[0]?></th>
					<td>
						<div class="select_type" style="width:180px;z-index:9;">
							<span class="ctrl"><span class="arrow"></span></span>
							<button type="button" class="myValue">옵션을 선택해주세요</button>
							<ul class="aList selectOption2">
								<?
									for($i=1;$i<$count2;$i++) {
								?>
									<li>
									<?if(strlen($tok[$i]) > 0) {?>
										<a  href="javascript:;" opt2 = '<?=$i?>'><?=$tok[$i]?></a>
									<?}?>
									<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
										<a href="javascript:;" opt2 = ''><strike><?=$tok[$i]." (품절)"?></strike></a>
									<?}?>
									</li>
								<?
									}
								?>
							</ul>
						</div>
						<input type = 'hidden' name = 'option2' id = 'ID_option2'>
					</td>
				</tr>
				<?
					}
				?>
			<?
				/*할인율 계산*/
				if($SellpriceValue != $_pdata->consumerprice && $_pdata->consumerprice > 0){
					$priceDcPercent = floor(100 - ($SellpriceValue / $_pdata->consumerprice * 100));
				}else{
					$priceDcPercent = 0;
				}
			?>
			<input type = 'hidden' value = '<?=$priceDcPercent?>' id = 'ID_priceDcPercent'>
				<tr>
					<th>옵션(필수)</th>
					<td>
						<select name="" id="">
							<option value="">선택하세요</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>추가구성상품</th>
					<td>
						<select name="" id="">
							<option value="">선택하세요</option>
						</select>
					</td>
				</tr>
				<tr><td colspan="2" class="line"><div class="line"></div></td></tr>
				<tr>
					<th>배송비</th>
					<td>
						<select name="" id="">
							<option value="">선불</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>수량11111111</th>
					<td>
						<div class="ea_select">
								
								<input type=text name="quantity" value="<?=($miniq>1?$miniq:"1")?>" onkeyup='quantityKeyUp(this)' class="amount" size = '2'>
								<a href="javascript:change_quantity('up')" class="btn_plus"></a>
								<a href="javascript:change_quantity('dn')" class="btn_minus"></a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<ul class="opt_list">
							<li>-화이트5호 <p>20,000원 <a href=""><img src="../img/btn/btn_close03.gif" alt="삭제" /></a></p></li>
							<li>-화이트5호 <p>20,000원 <a href=""><img src="../img/btn/btn_close03.gif" alt="삭제" /></a></p></li>
						</ul>
					</td>
				</tr>
				<tr>
					<th class="total_price">구매예정금액</th>
					<td class="total_price" id="total_price" ><span><?=number_format($SellpriceValue)?></span> 원</td>
					<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
					<input type = 'hidden' name = 'option1price' id = 'ID_option1price'>
				</tr>
				
				<tr>
					<td colspan="2">
			<?
				if($_pdata->assembleuse!="Y"){
					if(strlen($dicker)==0) {
						$temp = $_pdata->option1;
						$tok = explode(",",$temp);
						$goods_count=count($tok);

						$check_optea='0';
						if($goods_count>"1"){
							$check_optea="1";
						}

						$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
						$check_optout=array();
						$check_optin=array();
						for($gi=1;$gi<$goods_count;$gi++) {

							if(strlen($_pdata->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
							else{  $check_optin[]='1';}
						}


						if(strlen($_pdata->quantity)>0 && ($_pdata->quantity<="0" || (count($check_optin)=='0' && $check_optea))){
			?>
							<FONT style="color:#F02800;"><b>품 절</b></FONT>
			<?
						}else {
			?>
							<?if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {?>
								<a href="javascript:SA_PRODUCT(document.getElementById('quantity').value);CheckForm('ordernow','<?=$opti?>');" class="first"><img src="../img/btn/btn_buy_now.gif" alt="바로구매" /></a><a href="javascript:SA_PRODUCT(document.getElementById('quantity').value);CheckForm('','<?=$opti?>');" class="btn_cart"><img src="../img/btn/btn_cart.gif" alt="장바구니" /></a><a href="javascript:CheckForm('wishlist','<?=$opti?>')" class="btn_wishlist"><img src="../img/btn/btn_wishlist.gif" alt="관심상품" /></a>
							<?} else {?>
								<a href="javascript:SA_PRODUCT(document.getElementById('quantity').value);CheckForm('ordernow','<?=$opti?>');" class="first"><img src="../img/btn/btn_buy_now.gif" alt="바로구매" /></a><a href="javascript:SA_PRODUCT(document.getElementById('quantity').value);CheckForm('','<?=$opti?>');" class="btn_cart"><img src="../img/btn/btn_cart.gif" alt="장바구니" /></a><a href="javascript:check_login();" class="btn_wishlist"><img src="../img/btn/btn_wishlist.gif" alt="관심상품" /></a>
							<?}?>
			<?
						}
					}
				}
			?>
						<input type=hidden name=code value="<?=$code?>">
						<input type=hidden name=productcode value="<?=$productcode?>">
						<input type=hidden name=ordertype>
						<input type=hidden name=opts>
						<input type=hidden name=vip_type value="<?=$vrow->vip_type?>">
						<input type=hidden name=staff_type value="<?=$vrow->staff_type?>">
						<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
					</td>
				</tr>
			</table>
			</form>
		</div><!-- //div.center_info_wrap -->
		<!-- 우측정보 -->
		<div class="right_info_wrap">
			<div class="info">
				<table class="info" width="166">
					<colgroup><col style="width:84px" /><col style="width:84px" /></colgroup>
					<tr>
						<th>무이자할부</th>
						<td>해당카드사 확인</td>
					</tr>
					<tr>
						<th>원산지</th>
						<td><?=$_pdata->madein?></td>
					</tr>
					<tr>
						<th>브랜드</th>
						<td><?=$_pdata->brand?></td>
					</tr>
					<tr>
						<th>상품코드</th>
						<td><?=$_pdata->productcode?></td>
					</tr>
				</table>
			</div>
			<div class="mini_review">
				<p class="title">
					<span>3</span>건의 리뷰가 있습니다.
					<a href="" class="btn_more"></a>
				</p>
				<ul class="list">
					<li><a href="">-확실하게 잡아주는 느낌이 좋아요</a></li>
					<li><a href="">-확실하게 잡아주는 느낌이 좋아요</a></li>
					<li><a href="">-확실하게 잡아주는 느낌이 좋아요</a></li>
				</ul>
			</div>
			<script>
				$(function(){
					$("#category_dep_01").change(function(){
						$.post("../front/category_proc.php", {c_lev:"b",c_code:$(this).val()}, function(data) {
							if (data == "0"){
							} else {
								data = "<option value=''>선택</option>"+data;
								$(".ta_c").show();
								$("#category_dep_02").show().html(data);
							}
						});
					});
					$("#category_dep_02").change(function(){
						location.href="../front/productlist.php?code="+$(this).val();
					});
				});
				$(document).ready(function(){
					$.post("../front/category_proc.php", {c_lev:"a",c_code:$(this).val()}, function(data) {
						$("#category_dep_01").html(data);
					});
					$("#category_dep_01 > option[value=<?=substr($code,0,3).'000000000'?>]").attr("selected", "true");
					$.post("../front/category_proc.php", {c_lev:"b",c_code:'<?=substr($code,0,3)?>'}, function(data) {
						if (data == "0"){
						} else {
							data = "<option value=''>선택</option>"+data;
							$(".ta_c").show();
							$("#category_dep_02").show().html(data);
						}
					});
				});
			</script>
			<div class="category">
				<ul id="category_ul">
					<li><img src="../img/common/title_category.gif" alt="CATEGORY" /></li>
					<li>
						<select name="category_dep_01" id="category_dep_01">
							
						</select>
					</li>
					<li class="ta_c" style="display:none;"><img src="../img/icon/bottom_arrow.gif" alt="" /></li>
					<li>
						<select name="category_dep_02" id="category_dep_02" style="display:none;">
							
						</select>
					</li>
				</ul>
				
			</div>
		</div><!-- //div.right_info_wrap -->
	</div><!-- //div.goods_view_info -->
	<!-- 관련상품 -->
	<div class="related_goods_wrap">
		<div class="title">
			<img src="../img/common/title_related.gif" alt="관련상품" />
			<p class="btn"><a href="" class="left"></a><a href="" class="right"></a></p>
		</div>
		<div class="related_goods">
			<ul class="list">
				<?foreach($main_disp_goods[3] as $mv){?>
				<li>
					<p class="img">
						<a href="productdetail.php?productcode=<?=$mv["productcode"]?>"><img src="<?=$Dir.DataDir."shopimages/product/".$mv["maximage"]?>" alt="" /></a>
					</p>
					<div class="info">
						<p class="title"><a href="">[미국정품] 스마트한 스윙연습기! 스윙윙윙윙윙ㅇㅇㅇ</a></p>
						<p class="price">35,000원</p>
					</div>
				</li>
				<?}?>
			</ul>
		</div>
	</div><!-- //div.related_goods_wrap -->

	<div class="delivery_info_wrap">
		<div class="goods_view_tap">
			<ul class="tap_list">
				<li class="on"><a class="on" href="">제품상세정보<em class="on"></em></a></li>
				<li><a href="">배송/반품/교환<em></em></a></li>
				<li><a href="">상품후기<em></em></a></li>
				<li><a href="">상품Q&A<em></em></a></li>
			</ul>
		</div><!-- //div.goods_view_tap -->
		<!-- 구매사은품 -->
		<div class="gift_wrap">
			<h3><img src="../img/common/h3_title_gift.gif" alt="구매금액별 사은품 안내" /></h3>
			<ul class="store_list">
				<li><a href="">10만원 이상</a></li>
				<li><a href="">20만원 이상</a></li>
				<li class="on"><a href="">50만원 이상</a></li>
				<li><a href="">풀세트 구매시</a></li>
				<li><a href="">아이언 구매시</a></li>
			</ul>
			<div class="gift_list">
				<p><img src="../img/test/gift_list.jpg" alt="" /></p>
			</div>
		</div>

		<!-- 정보고시 -->
		<div class="goods_notice">
			<table class="goods_notice" width="100%">
				<caption><img src="../img/common/h3_title_goods_notice.gif" alt="전자성거래 상품정보제공 고시" /></caption>
				<colgroup>
					<col style="width:130px" /><col style="width:350px" /><col style="width:130px" /><col style="width:350px" />
				</colgroup>
				<tr>
					<th>품명 및 모델명</th>
					<td>도론 마이크로 레더</td>
					<th>판매원</th>
					<td>(주)엑스넬스 코리아</td>
				</tr>
				<tr>
					<th>출시년도</th>
					<td>2013년 7월</td>
					<th>공급원</th>
					<td>(주)엑스넬스 코리아</td>
				</tr>
				<tr>
					<th>소재/재질</th>
					<td>마이크로 극세사 + 합성피혁</td>
					<th>원산지</th>
					<td>대한민국</td>
				</tr>
				<tr>
					<th>색상</th>
					<td>레드/블루/그레이/옐로우</td>
					<th>세탁방법 및 취급사항</th>
					<td>상품택 참조</td>
				</tr>
				<tr>
					<th>사이즈</th>
					<td>22호~26호</td>
					<th>품질보증기준</th>
					<td>상품택 참조</td>
				</tr>
				<tr>
					<th>A/S</th>
					<td colspan="3">(주)엑스넬스 코리아 / 070-7621-6556 (내선 0번)</td>
				</tr>
			</table>
		</div>

		<!-- 상품상세설명 -->
		<div class="goods_editor_area">
		<?
			$_pdata_content = stripslashes($_pdata->content);
			if(strlen($detail_filter)>0) {
				$_pdata_content = preg_replace($filterpattern,$filterreplace,$_pdata_content);
			}
			if (strpos($_pdata_content,"table>")!=false || strpos($_pdata_content,"TABLE>")!=false)
				echo "<pre>".$_pdata_content."</pre>";
			else if(strpos($_pdata_content,"</")!=false)
				echo nl2br($_pdata_content);
			else if(strpos($_pdata_content,"img")!=false || strpos($_pdata_content,"IMG")!=false)
				echo nl2br($_pdata_content);
			else
			echo str_replace(" ","&nbsp;",nl2br($_pdata_content));
		?>
		</div><!-- //상품상세설명 -->
		
		<div class="delivery_info_wrap">
				
			<div class="goods_view_tap">
				<ul class="tap_list">
					<li><a href="">제품상세정보<em></em></a></li>
					<li class="on"><a class="on" href="">배송/반품/교환<em class="on"></em></a></li>
					<li><a href="">상품후기<em></em></a></li>
					<li><a href="">상품Q&A<em></em></a></li>
				</ul>
			</div><!-- //div.goods_view_tap -->

			<div class="delivery_editor_area">
				<img src="../img/common/delivery_info.jpg" alt="배송/반품/교환 안내" />
			</div><!-- //div.delivery_editor_area -->

		</div><!-- //div.delivery_info_wrap -->

		<!-- 배송정보 -->
		<
		<div class="view_page_reivew_wrap">
			<div class="goods_view_tap">
				<ul class="tap_list">
					<li><a href="">제품상세정보<em></em></a></li>
					<li><a href="">배송/반품/교환<em></em></a></li>
					<li class="on"><a class="on" href="">상품후기<em class="on"></em></a></li>
					<li><a href="">상품Q&A<em></em></a></li>
				</ul>
			</div><!-- //div.goods_view_tap -->

			<!-- 상품 리뷰 -->
			<?php include($Dir.FrontDir."prreview_tem001.php"); ?>
			<!-- //상품 리뷰 -->
		</div>
		<div class="view_page_qna_wrap">
			<div class="goods_view_tap">
				<ul class="tap_list">
					<li><a href="">제품상세정보<em></em></a></li>
					<li><a href="">배송/반품/교환<em></em></a></li>
					<li><a href="">상품후기<em></em></a></li>
					<li class="on"><a class="on" href="">상품Q&A<em class="on"></em></a></li>
				</ul>
			</div><!-- //div.goods_view_tap -->

			<!-- 상품 문의-->
			<?php include($Dir.FrontDir."prqna_tem001.php"); ?>
			<!-- //상품 문의 -->
		</div>

	</div>
</div>

<div>&nbsp;</div>
<script language="JavaScript">

	$(".jCarouselLite").jCarouselLite({
		btnNext: ".coodi_pick_wrap .right",
		btnPrev: ".coodi_pick_wrap .left",
		visible: 5,
	    auto: 2000,
	    speed: 1000
	});

var miniq=<?=($miniq>1?$miniq:1)?>;
var ardollar=new Array(3);
ardollar[0]="<?=$ardollar[0]?>";
ardollar[1]="<?=$ardollar[1]?>";
ardollar[2]="<?=$ardollar[2]?>";
<?
if(strlen($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		echo "num = new Array(";
		for($i=0;$i<$maxnum;$i++) {
			if ($i!=0) echo ",";
			if(strlen($optioncnt[$i])==0) echo "100000";
			else echo $optioncnt[$i];
		}
		echo ");\n";
	}
?>

function change_price(temp,temp2,temp3) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	if(temp3=="") temp3=1;
	price = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetok[$i]."'"; } ?>);

	sprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$spricetok[$i]."'"; } ?>);


	consumer = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->consumerprice)."','".number_format($_pdata->consumerprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$consumertok[$i]."'"; } ?>);
	o_reserve = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->option_reserve)."','".number_format($_pdata->option_reserve)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$reservetok[$i]."'"; } ?>);
	doprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetokdo[$i]."'"; } ?>);
	if(temp==1) {
		if (document.form1.option1.selectedIndex><? echo $priceindex+2 ?>)
			temp = <?=$priceindex?>;
		else temp = document.form1.option1.selectedIndex;
		document.form1.price.value = price[temp];

		document.all["idx_price"].innerHTML = document.form1.price.value+"원";


		if(sprice[temp]!='0'){
		document.form1.sprice.value = sprice[temp];
		document.all["idx_sprice"].innerHTML = document.form1.sprice.value+"원";
		}else{
			if(sprice[0]!='0'){
			document.form1.sprice.value = sprice[0];
			document.all["idx_sprice"].innerHTML = document.form1.sprice.value+"원";
			}
		}


		if(consumer[temp]!='0'){
		document.form1.consumer.value = consumer[temp];
		document.all["idx_consumer"].innerHTML = document.form1.consumer.value+"원";
		}else{
			if(consumer[0]!='0'){
			document.form1.consumer.value = consumer[0];
			document.all["idx_consumer"].innerHTML = document.form1.consumer.value+"원";
			}
		}
		if(o_reserve[temp]!='0'){
		document.form1.o_reserve.value = o_reserve[temp];
		document.all["idx_reserve"].innerHTML = document.form1.o_reserve.value+"원";
		}else{
			if(o_reserve[0]!='0'){
			document.form1.o_reserve.value = o_reserve[0];
			document.all["idx_reserve"].innerHTML = document.form1.o_reserve.value+"원";
			}
		}

<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
<? } ?>
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value = doprice[temp];
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
	}
	packagecal(); //패키지 상품 적용
	if(temp2>0 && temp3>0) {
		if(num[(temp3-1)*10+(temp2-1)]==0){
			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');
			if(document.form1.option1.type!="hidden") document.form1.option1.focus();
			return;
		}
	} else {
		if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
		else document.form1.option2.focus();
		return;
	}
}

<? } else if(strlen($optcode)>0) { ?>

function chopprice(temp){
<?=(strlen($dicker)>0)?"return;\n":"";?>
	ind = document.form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.form1.price.value.replace(/,/g, "");
	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	document.form1.opttype[temp].value=optprice;
	var num_str = document.form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.form1.price.value = result;
	document.all["idx_price"].innerHTML=document.form1.price.value+"원";
	packagecal(); //패키지 상품 적용
}

<?}?>
<? if($_pdata->assembleuse=="Y") { ?>
function setTotalPrice(tmp) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	var i=true;
	var j=1;
	var totalprice=0;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[2].length) {
					totalprice += arracassemble[2]*1;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	totalprice = totalprice*tmp;
	var num_str = totalprice.toString();
	var result = '';
	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num_str.charAt(tmp) + result;
	}
	if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
	if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
	if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
	<? } ?>
}
<? } ?>

function packagecal() {
<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
	pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
	var result = "";
	var intgetValue = document.form1.price.value.replace(/,/g, "");
	var temppricevalue = "0";
	for(var j=1; j<pakageprice.length; j++) {
		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
			result="";
			for(var i=0; i<temppricevalue.length; i++) {
				var tmp = temppricevalue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = temppricevalue.charAt(tmp) + result;
			}
			document.getElementById("idx_price"+j).innerHTML=result+"원";
		}
	}

	if(typeof(document.form1.package_idx)=="object") {
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);

		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) {
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
	}
}
</script>

<SCRIPT LANGUAGE="JavaScript">

var imagepath="<?=$imagepath?>";
var setcnt=0;

function primg_preview(img,width,height) {

	if($("img[name='primg']")!=null) {
		setcnt=0;
		$("img[name='primg']").attr("src",imagepath+img);

		if(width>0){
			$("img[name='primg']").css("width",width+"px");
		}
		if(height>0){
			$("img[name='primg']").css("height",height+"px");
		}
		//alert($("img[name='primg']").css("width"));
	} else {
		if(setcnt<=10) {
			setcnt++;
			setTimeout("primg_preview('"+img+"','"+width+"','"+height+"')",500);
		}
	}
}

function primg_preview3(img,width,height) {

	if($("img[name='primg']")!=null) {
		$("img[name='primg']").attr("src",imagepath+img);

		if(width>0){
			$("img[name='primg']").css("width",width+"px");
		}
		if(height>0){
			$("img[name='primg']").css("height",height+"px");
		}
	}
}

function primg_preview2(img,width,height) {
	obj = event.srcElement;
	clearTimeout(obj._tid);
	obj._tid=setTimeout("primg_preview3('"+img+"','"+width+"','"+height+"')",500);
}

primg_preview('<?=$yesimage[0]?>','<?=$xsize[0]?>','<?=$ysize[0]?>');


		/*
			상품 옵션 선택 Start
		*/
		var option1TempValue = $(".selectOption1").prev().html();
		var clickOption1 = false;
		$(".selectOption1 li").click(function(){
			if($(this).children().attr('opt')){
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option1").val($(this).children().attr('opt'));
				$("#ID_option1price").val($(this).children().attr('opt1'));
				$(this).parent().parent().removeClass('open');
				option1TempValue = $(this).children().html();
				clickOption1 = true;
				change_total_price();
			}else{
				alert("품절된 상품 입니다.");
				if(!clickOption1){
					$(this).parent().prev().removeClass('selected');
				}
				$(this).parent().prev().html(option1TempValue);
				$(this).parent().parent().removeClass('open');
			}
		})
		var option2TempValue = $(".selectOption2").prev().html();
		var clickOption2 = false;
		$(".selectOption2 li").click(function(){
			if($(this).children().attr('opt2')){
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option2").val($(this).children().attr('opt2'));
				$(this).parent().parent().removeClass('open');
				option2TempValue = $(this).children().html();
				clickOption2 = true;
				change_total_price();
			}else{
				alert("품절된 상품 입니다.");
				if(!clickOption2){
					$(this).parent().prev().removeClass('selected');
				}
				$(this).parent().prev().html(option2TempValue);
				$(this).parent().parent().removeClass('open');
			}
		})
		/*
			상품 옵션 선택 End
		*/

</SCRIPT>

