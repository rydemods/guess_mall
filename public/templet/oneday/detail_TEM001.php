<?
include_once dirname(__FILE__)."/../../lib/product.class.php";
$product = new PRODUCT();
$dc_data = $product->getProductDcRate($productcode);
?>

<script type="text/javascript">

	$(document).ready(function(){
		var year="<?=date('Y')?>";
		var month="<?=date('m')?>";
		goCalendar(year,month);
		
	});
	
	setInterval(function(){nowtime()},1000) ;
	
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




		/*
			추천상품 롤링 Start
		*/
		$(".jCarouselLite_recommendation").jCarouselLite({
			visible: 4,
			btnNext: ".recommend_product_right",
			btnPrev: ".recommend_product_left",
			auto: 6000,
			speed: 1000
		});
		/*
			추천상품 롤링 End
		*/





		/*
			상품 옵션 선택 Start
		*/
		var option1TempValue = $(".selectOption1").prev().html();
		var clickOption1 = false;
		$(".selectOption1 li").click(function(){
			if($(this).children().attr('opt')){
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option1").val($(this).children().attr('opt'));
				$(this).parent().parent().removeClass('open');
				option1TempValue = $(this).children().html();
				clickOption1 = true;
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



		/*
			미리 계산기 Start
			ID_use_point : 포인트 사용 텍스트박스
			ID_use_all_point : 전체 사용 버튼
			ID_member_point : 전체 포인트 히든밸류
			ID_coupon_search : 쿠폰조회
			ID_view_basket_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [장바구니]
			ID_view_etc_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [무적]
			ID_view_goods_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [상품]
			ID_coupon_dc_won : 할인액
			ID_coupon_dc_per : 할인 %
			ID_original_price : 상품 가격
			ID_last_price : 최종 할인 가격
			ID_basket_coupon_value : 장바구니 쿠폰 할인 저장 구역
			ID_etc_coupon_value : 무적 쿠폰 할인 저장 구역
			ID_goods_coupon_value : 상품 쿠폰 할인 저장 구역
			CLS_tot_dc_prices : 총 할인액
		*/
		$("#ID_coupon_search").click(function(){
			$.ajax({
				type: "GET", 
				url: "../front/prcoupon_list_proc.php", 
				data: "productcode="+$("input[name='productcode']").val()+"&price="+$("#ID_goodsprice").val(),
				dataType: "json",
			}).done(function ( data ) {
				$("#ID_coupon_search").html("일반쿠폰조회 ( <span class='coupon_count'>"+data.tot+"</span> 장 )");
				$("#ID_view_basket_coupon_layer").html(data.layer1);
				$("#ID_view_etc_coupon_layer").html(data.layer3);
				$("#ID_view_goods_coupon_layer").html(data.layer2);
			});
		})
		$(document).on("click", ".CLS_btn_coupon_basket, .CLS_btn_coupon_etc, .CLS_btn_coupon_goods", function(){
			if($(this).attr('class') == 'CLS_btn_coupon_basket'){
				$(".CLS_btn_coupon_basket").show();
				$("#ID_basket_coupon_value").val($(this).attr('price'));
			}else if($(this).attr('class') == 'CLS_btn_coupon_etc'){
				$(".CLS_btn_coupon_etc").show();
				$("#ID_etc_coupon_value").val($(this).attr('price'));
			}else if($(this).attr('class') == 'CLS_btn_coupon_goods'){
				$(".CLS_btn_coupon_goods").show();
				$("#ID_goods_coupon_value").val($(this).attr('price'));
			}
			$(this).hide();
			sumDcTotal();
		});
		$(document).on("keyup", "#ID_use_point", function(){
			sumDcTotal();
		});
		
		/* 달력보이기 */
		$('div.time_sail_calender_wrap div.title a.close').click(function(){
			$('div.time_sail_calender_wrap').css('display' , 'none');
		});
		$('.btn_calender').click(function(){
			$('div.time_sail_calender_wrap').css('display' , 'block');
		});
		/* 달력보이기 End */


		$("#ID_use_all_point").click(function(){
			$("#ID_use_point").val($("#ID_member_point").val());
			sumDcTotal();
		})
		$('#ID_original_price, #ID_last_price').html(comma($("#ID_goodsprice").val()));
		



		function sumDcTotal(){
			var totalDcPrice = 0;
			$(".CLS_tot_dc_prices").each(function(){
				totalDcPrice += parseInt($(this).val());
			})
			var totalResultPrice = $("#ID_goodsprice").val()-totalDcPrice;
			var totalResultPricePer = Math.floor((totalDcPrice / $("#ID_goodsprice").val()) * 100);
			$("#ID_coupon_dc_won").html(comma(totalDcPrice)+"원<br /><span id = 'ID_coupon_dc_per'>(약 "+totalResultPricePer+"%할인)</span>");

			if(totalResultPrice < 0){
				$("#ID_last_price").html(0);
			}else{
				$("#ID_last_price").html(comma(totalResultPrice));
			}
		}
		/*
			미리 계산기 End
		*/
	
		
	});
	
	function nowtime(){
		var today = new Date();
		var year = today.getFullYear();
		var month = today.getMonth();
		var date = today.getDate();
		var enddate = new Date(year,month,date+1,0,0,0);
		var gaptime = (enddate.getTime()-today.getTime())/1000;
		
		var gapS = Math.floor(gaptime%60);
		var gapM = Math.floor((gaptime/60)%60);
		var gapH = Math.floor((gaptime/3600)%24);
		
		if(gapH<10){
			gapH = "0"+gapH;
		}
		if(gapM<10){
			gapM = "0"+gapM;
		}
		$("div.sale_time p.hour").html(gapH);
		$("div.sale_time p.min").html(gapM);
	}
	
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

	function goCalendar(year,month){
		$.post("onedaydetail_ajax.php",
			{
				toyear:year,
				tomonth:month
			},
			function(data){
				$("div.time_sail_calender_wrap div.celender_layer").html(data);
			}
		)	
	}
	
	function goReserve(param,mode){
		$.post("oneday_indb.php",
			{
				param:param,
				mode:mode
			},
			function(data){
				var jdata = eval("("+data+")");
				if(jdata.msg2=="RF1"){
					jdata.msg1 = jdata.msg1+"\n로그인 하시겠습니까?";
					if(confirm(jdata.msg1)){
						location.href="login.php";
					}
				}else{
					alert(jdata.msg1);
				}
			}
		)
	}
	
</script>



<div class="main_wrap">
	<div class="container1100 pos_r">
	
		<!-- 라인맵 -->
		<div class="line_map_r">
			<a href="#">홈</a> > <a href="#"><em>오늘의 특가</em></a>
		</div><!-- //라인맵 -->

		<!-- 타임세일 달력 레이어 -->
		<div class="time_sail_calender_wrap">
			<div class="title"><a href="#" class="close"></a></div>
			<div class="celender_layer">
				<!--달력부분-->
				<?php /*ajax로 가져옴 */?>
				<!--//달력부분-->
			</div>
		</div>			
	
	
	
		<!-- 상세페이지 시작 -->
		<div class="today_sale_wrap">
			<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
				<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice'>
				
				<!-- 상품이미지 -->
				<div class="left">
				<?php
					if(strlen($_pdata->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->minimage)){
				?>
					<a href="#"><img src="<?=$Dir.ImageDir."product/".$_pdata->minimage?>" alt="" /></a>
				<?php
					}
				?>
					<div class="goods_social_icon">
						소문내기 
						<a href="#"><img src="../img/icon/icon_social_kakao.gif" alt="카카오톡" /></a>
						<a href="#"><img src="../img/icon/icon_social_facebook.gif" alt="페이스북" /></a>
						<a href="#"><img src="../img/icon/icon_social_url.gif" alt="URL복사" /></a>
					</div>
				</div>
				<!-- //상품이미지 -->
				
				<!-- 상품이미지 오른쪽 영역 -->
				<div class="right">
					
					
					<?php
						
						/* $SellpriceValue를 위해 남겨놓은 로직*/
						
						$SellpriceValue=0;
						if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0){						
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
								$pricetok[$tmp]=number_format(getProductSalePrice($pricetok[$tmp], $dc_data[price]));
								$consumertok[$tmp]=number_format($consumertok[$tmp]);
								$reservetok[$tmp]=number_format($reservetok[$tmp]);
							}	
							$SellpriceValue=str_replace(",","",$pricetok[0]);
						} else if(strlen($optcode)>0) {
							$SellpriceValue=$_pdata->sellprice;
						} else if(strlen($_pdata->option_price)==0) {
							if($_pdata->assembleuse=="Y") {
								$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
							} else {
								$SellpriceValue=$_pdata->sellprice;
							}
							$priceindex=0;
						}
						/* 로직 End  */
					?>
					<!-- 판매가/특가 -->
					<div class="today_sale_table">
						<div class="title">
							<?=$_pdata->productname?>
							<?php if($_pdata->mdcomment){ ?>
							<span>
								<?=$_pdata->mdcomment?>
							</span>
							<?php } ?>
						</div>
						<div class="pos_r">
						<div class="price_down">
						<?php if($_odata['ratio']){ ?>
							<p><?=$_odata['ratio']?><span>%</span></p>
						<?php } ?>
						</div>
						<table class="price_info" cellpadding=0 cellspacing=0 border=0 width="500">
							<colgroup>
								<col width="60" /><col width="200" /><col width="240"/>
							</colgroup>
							<tr>
								<th>판매가</th>
								<td class="price"><span><?=number_format($_odata['sellprice'])?></span> 원</td>
								<td rowspan=2></td>
							</tr>
							<tr>
								<th>특가</th>
								<td class="dc"><span><?=number_format($_odata['dcprice'])?></span> 원</td>
							</tr>
						</table>
						</div>
					</div>
					<!-- //판매가/특가 -->

					<!-- 남은시간 -->
					<div class="sale_time">
						<p class="ea"><?=$_odata['sale_cnt']+$_odata['add_ea']?></p>
						<p class="hour">15</p>
						<p class="min">42</p>
					</div><!-- //남은시간 -->

					<table class="price_spec" cellpadding=0 cellspacing=0 border=0 width="100%">
						<colgroup>
							<col width="110" /><col width="*px" />
						</colgroup>
						<tr>
							<th>원산지</th>
							<td><?=$_pdata->madein?$_pdata->madein:"-";?></td>
						</tr>
						<tr>
							<th>제조사</th>
							<td><?=$_pdata->production?$_pdata->production:"-";?></td>
						</tr>

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
						<tr>
							<th><?=$tok[0]?></th>
							<td>
								<div class="select_type" style="width:180px;z-index:10;">
									<span class="ctrl"><span class="arrow"></span></span>
									<button type="button" class="myValue">=====옵션 선택=====</button>
									<ul class="aList selectOption1">
										<?for($i=1;$i<$count;$i++) {?>
											<li>
											<?if(strlen($tok[$i]) > 0) {?>
												<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>	
													<a href="javascript:;" opt = ''><strike><?=$tok[$i]." [품절]"?></strike></a>
												<?}else{?>
													<a href="javascript:;" opt = '<?=$i?>'><?=$tok[$i]?></a><?//." (".number_format($optprice[$i-1])."원) (".$optcode[$i-1].")"?>
												<?}?>
											<?}?>
											</li>
										<?}?>
									</ul>
								</div>
								<input type = 'hidden' name = 'option1' id = 'ID_option1'>
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
							<th>옵션선택2</th>
							<td>
								<div class="select_type" style="width:180px;z-index:9;">
									<span class="ctrl"><span class="arrow"></span></span>
									<button type="button" class="myValue"><?=$tok[0]?></button>
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
							if(strlen($optcode)>0) {
								$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
								$result = pmysql_query($sql,get_db_conn());
								if($row = pmysql_fetch_object($result)) {
									$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
									$opti=0;
									$option_choice = $row->option_choice;
									$exoption_choice = explode("",$option_choice);
						?>
						<tr>
							<th>상품옵션</th>
							<td>
								<?
									while(strlen($optionadd[$opti])>0) {
										$opval = str_replace('"','',explode("",$optionadd[$opti]));
										$opcnt=count($opval);
								?>	
										<select id="mulopt" onchange="chopprice('<?=$opti?>)" name="mulopt">
											<option value="">=<?=$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")?>=</option>

								<?
										for($j=1;$j<$opcnt;$j++) {
											$onchange_opt3="";
											$exop = str_replace('"','',explode(",",$opval[$j]));
											if($exop[1]>0) $onchange_opt3.=$exop[0]."(+".$exop[1]."원)";
											else if($exop[1]==0) $onchange_opt3.=$exop[0];
											else $onchange_opt3.=$exop[0]."(".$exop[1]."원)";

								?>	
											<option value="<?=$opval[$j]?>"><?=$onchange_opt3?>

								<?	
										}
								?>
								</select>
								<input type=hidden name="opttype" value="0"><input type=hidden name="optselect" value="<?=$exoption_choice[$opti]?>"><br>
								<?			
										$opti++;
									}
								?>
								<input type=hidden name="mulopt"><input type=hidden name="opttype"><input type=hidden name="optselect">
							</td>
						</tr>
						<?	
								}
								pmysql_free_result($result);
							}
						?>
						
						<?
							if(strlen($onchange_opt1)==0 && strlen($onchange_opt2)==0 && strlen($onchange_opt3)==0) {
						?>
						<tr>
							<td colspan = '2'><input type="hidden" name="option1" id = 'ID_option1_etc'><input type="hidden" name="option2" id = 'ID_option2_etc'></td>
						</tr>
						<?
							}
						?>
						
						<tr>
							<th>수량</th>
							<td>
								<div class="ea_select" style="z-index:50x">
									<input type=text name="quantity" value="<?=($miniq>1?$miniq:"1")?>" <?if($_pdata->assembleuse=="Y"){echo " readonly";}else{ echo "onkeyup='strnumkeyup(this)'";}?> class="amount" size = '2'>
									<a href="javascript:change_quantity('up')" class="up"></a>
									<a href="javascript:change_quantity('dn')" class="down"></a>
								</div>
							</td>
						</tr>
					</table>

					<div class="mt_30">
						<a href="javascript:CheckForm('ordernow','<?=$opti?>')"><img src="../img/button/btn_today_sale_buy.gif" alt="오늘의 특가 바로구매" /></a>
						<a class="btn_calender" href="javascript:;"><img src="../img/button/btn_today_sale_celender.gif" alt="오늘의 특가 Calender 보기" /></a>
					</div>

				</div>			
				<!--// 상품이미지 오른쪽 영역 -->	
					
				

				
			
				<input type=hidden name=code value="<?=$code?>">
				<input type=hidden name=productcode value="<?=$productcode?>">
				<input type=hidden name=ordertype>
				<input type=hidden name=opts>
			</form>
		</div>
		
		<div class="goods_view_wrap">
		
			<!-- 왼쪽영역 -->
			<div class="left_area">
				
				<?if($mainbanner['productdetail_banner']['1']['banner_img']){?>
				<div class="goods_view_midd_banner">
					<a href="<?=$mainbanner['productdetail_banner']['1']['banner_link']?$mainbanner['productdetail_banner']['1']['banner_link']:"javascript:;";?>">
						<img src="<?=$mainbanner['productdetail_banner']['1']['banner_img']?>" alt="" />
					</a>
				</div>
				<?}?>



				<!-- 탭:상세정보-->
				<div class="goods_view_tap">
					<a name="1">&nbsp;</a>
					<ul class="view_tap">
						<li class="on"><a href="#1">상세정보</a></li>
						<li><a href="#2">상품리뷰 <span class="num reviewTotalMenuBar">(0)</span></a></li>
						<li><a href="#3">상품Q&A</a></li>
						<li><a href="#4">배송/교환/환불</a></li>
					</ul>
					<div class="product_detail_view">
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
					</div>
				</div>
				<!-- //탭:상세정보-->



				<!-- 탭:상세리뷰-->
				<div class="goods_view_tap">
					<a name="2">&nbsp;</a>
					<ul class="view_tap">
						<li><a href="#1">상세정보</a></li>
						<li class="on"><a href="#2">상품리뷰 <span class="num reviewTotalMenuBar">(0)</span></a></li>
						<li><a href="#3">상품Q&A</a></li>
						<li><a href="#4">배송/교환/환불</a></li>
					</ul>
					<div class="goods_view_review_write">
						<?if($_data->review_type!="N") {?>
							<?php include($Dir.FrontDir."prreview_tem001.php"); ?>
						<?}?>
					</div>
				</div>
				<!-- //탭:상세리뷰-->




				<!-- 탭:상세Q&A-->
				<div class="goods_view_tap">
					<a name="3">&nbsp;</a>
					<ul class="view_tap">
						<li><a href="#1">상세정보</a></li>
						<li><a href="#2">상품리뷰 <span class="num reviewTotalMenuBar">(0)</span></a></li>
						<li class="on"><a href="#3">상품Q&A</a></li>
						<li><a href="#4">배송/교환/환불</a></li>
					</ul>
					<div class="goods_viw_qna">
						<?php include($Dir.FrontDir."prqna_tem001.php"); ?>
					</div>
				</div>
				<!-- //탭:상세Q&A-->




				<!-- 탭:배송/교환/환불-->
				<div class="goods_view_tap">
					<a name="4">&nbsp;</a>
					<ul class="view_tap">
						<li><a href="#1">상세정보</a></li>
						<li><a href="#2">상품리뷰 <span class="num reviewTotalMenuBar">(0)</span></a></li>
						<li><a href="#3">상품Q&A</a></li>
						<li class="on"><a href="#4">배송/교환/환불</a></li>
					</ul>
					<div class="goods_refund_wrap">
						<?=$deli_info?>
					</div>
				</div>
				<!-- //탭:배송/교환/환불-->

			</div>
			<!-- //왼쪽영역 -->
			
			<!-- 오른쪽영역 -->
			<div class="right_area">
				<div class="goods_right_best_view">
					<h4>가장 많이 본 상품</h4>
					<div class="view_list_wrap">
						<ul>
							<li><a href="#">Related Item 이 없습니다.</a></li>
							<li class="name"></li>
							<li class="price"></li>
						</ul>
					</div>
				<?php //include "related_item.inc.php"; ?>
			</div>
			<!-- //오른쪽영역 -->		
		
		
		
		</div>
	</div>
</div>






<!-- 쇼핑태그 -->
<?if($_data->ETCTYPE["TAGTYPE"]!="N") {?>
<div class="tag_list_wrap">
	<div class="tag_list">
		<div class="reg">
			<p>본상품의 태그를 달아주세요(한번에 하나의 태그만) </p>
			<input type="text" name="searchtagname" id="searchtagname" /> &nbsp; <a href="#a" class="btn_black" onclick="tagCheck('<?=$productcode?>')">TAG달기</a>
		</div>
		<div class="list">
		<span id="prtaglist" style="word-break:break-all">
		<?
			$arrtaglist=explode(",",$_pdata->tag);
			$jj=0;
			for($i=0;$i<count($arrtaglist);$i++) {
				$arrtaglist[$i]=preg_replace("/<|>/","",$arrtaglist[$i]);
				if(strlen($arrtaglist[$i])>0) {
					if($jj>0) echo ",&nbsp;&nbsp;";
					echo "<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$i])."\" onmouseover=\"window.status='".$arrtaglist[$i]."';return true;\" onmouseout=\"window.status='';return true;\">".$arrtaglist[$i]."</a>";
					$jj++;
				}
			}
		?>

		</span>
			<!--p><a href="javascript:tagView();" class="btn_small">더보기</a></p-->
		</div>
	</div>
</div>
<?}?>
<!-- #쇼핑태그 -->









<?=$count2?>
<?php $priceindex=0?>


<script language="JavaScript">
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


</SCRIPT>
