<?
//메인 상단 배너
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
// $productpath = $Dir.DataDir."shopimages/product/";
// $banner_img_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 113 AND banner_type = 0";
// $banner_img_sql .= " ORDER BY banner_sort asc, no desc limit 7";
// $baner_img_result = pmysql_query($banner_img_sql);
// while ( $row = pmysql_fetch_array($baner_img_result) ) {
// 	$arrMainImgBanner[] = $row;
// }
// //메인 상단 배너(동영상)
// $clip_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 113 AND banner_type = 1";
// $clip_result = pmysql_query($clip_sql);
// if($row=pmysql_fetch_object($clip_result)) {
// 	$mainClipBanner = $row;
// }

// //메인 중단 배너
// $banner_img2_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 114 AND banner_type = 0";
// $banner_img2_sql .= " ORDER BY banner_sort asc, no desc limit 1";
// $baner_img_result = pmysql_query($banner_img2_sql);
// if($row=pmysql_fetch_object($baner_img_result)) {
// 	$mainImgBanner2 = $row;
// }
// preg_match_all('/src=\"(.[^"]+)"/i', $mainImgBanner2->banner_img_m, $src);

// //베스트 포토 리뷰
// $sql = "SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.subject,a.content,a.date,a.productcode,
// 			b.productname,b.tinyimage,b.selfcode,b.assembleuse, a.upfile, a.best_type, a.marks, a.quality, a.type, a.num
// 			FROM tblproductreview a, tblproduct b, (SELECT c_productcode,c_category FROM tblproductlink WHERE c_maincate = 1 ) c
// 			WHERE a.productcode = b.productcode AND a.productcode = c.c_productcode AND a.type = '1' AND b.display = 'Y' ORDER BY a.best_type desc, a.date DESC";
// $result = pmysql_query($sql);
// while ( $row = pmysql_fetch_array($result) ) {
// 	$photoReviewList[] = $row;
// }


?>

<!-- 내용 -->
<main id="content">

	<section class="main_visual with-btn-rolling">
		<?if(count($arrMainImgBanner) > 0){?>
			<ul class="slide">
				<?foreach( $arrMainImgBanner as $key=>$val ){
					if($val['banner_img_m']){
						$bannerImg	= getProductImage($imagepath, $val['banner_img_m']);
				?>
				<li><a href="<?=$val['banner_mlink']?>"><img src="<?=$bannerImg?>" alt="<?=$val['banner_title']?>"></a></li>
				<?php }
				}?>
			</ul>
			<?php }?>
		</ul>
	</section><!-- //.main_visual -->

	<section class="new_arrivals">
		<h2 class="main_title"><?=$top_product_row['banner_title']?></h2>
		<div class="with-btn-rolling">
			
				<?
					//상품리스트
					//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
					foreach( $top_product_list_mo_array as $mainTopKey=>$mainTopVal ){
						echo $mainTopVal;
					}
				?>
			
		</div>
	</section><!-- //.new_arrivals -->

	<section class="md_banner">
	<?
		foreach($MiddleTopBannerImg as $middletop_banner){
			$bannerImg	= getProductImage($banner_imagepath, $middletop_banner['banner_img_m']);
		?>
			<a href="<?=$middletop_banner['banner_mlink']?>"><img src="<?=$bannerImg?>" alt=""></a>
		<?}?>
	</section><!-- //.md_banner -->

	<section class="best_seller">
		<h2 class="main_title"><?=$brand_banner_list[0]['banner_title']?></h2>
		<div data-ui="TabMenu">
			<div class="wrap_longtab">
				<div class="tab-menu clear">
					<?
					$productbrand_cnt = 0;
					foreach($brand_banner_list as $brand_banner){
						if($productbrand_cnt == 0)	$isActive = "active";
					?>
						<a data-content="menu" class="<?=$isActive?>"><?=$brand_banner['banner_up_title']?></a>
					<?
						$productbrand_cnt++;
						$isActive = "";
					}?>
				</div>
			</div>

			<?
			$brand_banner_cnt = 0;
			foreach($brand_banner_list as $brand_banner){
				$brandBannerImg = getProductImage($banner_imagepath, $brand_banner['banner_img_m']);
				$brand_banner_no = $brand_banner['no'];
				$brand_banner_product_sql = fnGetBannerProduct($brand_banner_no,"3");
				$brand_banner_product_mo_array = productlist_print( $brand_banner_product_sql, $type = 'MO_002', array(), null, null, $code );

				if($brand_banner_cnt == 0)	$isActive = "active";
			?>
			<!-- BESTIBELLI -->
			<div class="tab-content <?=$isActive?>" data-content="content">
				<div class="brand">
					<div class="bg_img"><img src="<?=$brandBannerImg?>" alt="브랜드 배경 이미지"></div>
					<div class="best_slider">
						<?
							//상품리스트
							//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
							foreach( $brand_banner_product_mo_array as $mainMiddleKey=>$mainMiddleVal ){
								echo $mainMiddleVal;
							}
						?>
					</div>
				</div>
			</div>
			<?php
				$brand_banner_cnt++;
				$isActive = "";
			}?>
		</div>
	</section><!-- //.best_seller -->

	<section class="outlet_banner">
		<a href="<?=$middle_banner_img_row['banner_mlink']?>"><img src="<?=$middleBannerImg_m?>" alt=""></a>
	</section><!-- //.outlet_banner -->

	<section class="ranking">
		<h2 class="main_title"><span><?=$look_banner_row['banner_title']?></span></h2>
		<p class="ment"><?=$look_banner_row['banner_name']?></p><!-- [D] 두줄 이상 넘어가면 말줄임 -->
		<div class="ranking_img"><img src="<?=getProductImage($banner_imagepath, $look_banner_row['banner_img_m'])?>" alt="크리스마스를 위한 특별한 룩"></div>
		<div class="ranking_list">
			<?
				//상품리스트
				//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
				foreach( $look_banner_product_mo_array as $mainBottomKey=>$mainBottomVal ){
					echo str_replace('/front/','/m/',$mainBottomVal);
				}
			?>
		</div>
	</section><!-- //.ranking -->

	<section class="btm_banner with-btn-rolling">
		<ul class="slide">
			<?php 
			while($bottom_banner_img_row = pmysql_fetch_array($bottom_banner_img_result)){
				$bottomBannerImg = getProductImage($banner_imagepath, $bottom_banner_img_row['banner_img_m']);
			?>
			<li><a href="<?=$bottom_banner_img_row['banner_mlink']?>"><img src="<?=$bottomBannerImg?>" alt="하단 슬라이드 이미지"></a></li>
			<?php } ?>
		</ul>
	</section><!-- //.bt_banner -->


<!-- //내용 -->


<script type="text/javascript">
$(document).ready( function() {
    $("iframe").width(320);
    $("iframe").height(315);
// 	postingList();
// 	mainItemList('md');
// 	mainBottomItemList();
});

//베스트 리뷰
function reviewList(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=review&type=mobile",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-review").html(html);
		if($("#review_chknum").val() == "" || typeof $("#review_chknum").val() == "undefined"){
			$(".more_btn").hide();
		}else{
			$(".more_btn").show();
		}
		$(".main-community-content").trigger("COMMUNITY_RESET");
		masonryAlign(".hot-review");
	});
}

//실시간 베스트
function postingList(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=posting&type=mobile",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-posting").html(html);
		if($("#posting_chknum").val() == "" || typeof $("#posting_chknum").val() == "undefined"){
			$(".more_btn").hide();
		}else{
			$(".more_btn").show();
		}
		$(".main-community-content").trigger("COMMUNITY_RESET");
		masonryAlign(".hot-posting");
	});
}

//좋아요
function likeList(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=like&type=mobile",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-like").html(html);
		if($("#like_chknum").val() == "" || typeof $("#like_chknum").val() == "undefined"){
			$(".more_btn").hide();
		}else{
			$(".more_btn").show();
		}
		$(".main-community-content").trigger("COMMUNITY_RESET");
		masonryAlign(".hot-like");
	});
}

function masonryAlign($tg){
	var listLen = 0;

	for(var i=0;i<$($tg+'>li>figure>a>img').length;i++)
	{
		$($tg+'>li>figure>a>img').eq(i).attr("src", $($tg+'>li>figure>a>img').eq(i).attr("src"));
	}

	$($tg+'>li>figure>a>img').on('load', function(){
		listLen++;
		if(listLen == $($tg+'>li').length)
		{
			$($tg).masonry();
			$($tg).masonry('reloadItems');
			$($tg).masonry('layout');
		}
		$($tg).masonry();
		$($tg).masonry('layout');
	});
}


//메인 중단 상품
var product_slide;
function mainItemList(category_type){
	$.ajax({
		type: "POST",
		url: "../main/ajax_product_mainitem.php",
		data: "category_type="+category_type+"&type=mobile",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(window).off('resize');
		$(".mdpick-list").html(html);

		//$(".main-list-rec").trigger('resize');

		var len = $(".mdpick-list").find('img').length;
		var count = 0;
		$(".mdpick-list").find('img').on('load', function(){
			count++;
			if(count == len)
			{
				var mdslides = Math.floor($('.product_area').width()/148);
				var mdoption = {
						infiniteLoop: false,
						hideControlOnEnd: true,
						slideWidth:148,
						slideMargin:8,
						minSlides:1,
						maxSlides:mdslides,
						controls:false
					};

				if(product_slide)
				{
					product_slide.reloadSlider(mdoption);
				}else{
					product_slide = $('.product_area').children('ul').bxSlider(mdoption);
				}

				$(window).on('resize',function(){
					var mdslides = Math.floor($('.product_area').width()/148);
					var mdoption = {
						infiniteLoop: false,
						hideControlOnEnd: true,
						slideWidth:148,
						slideMargin:8,
						minSlides:1,
						maxSlides:mdslides,
						controls:false
					};
					product_slide.reloadSlider(mdoption);
				});


			}
		});

		/*if(category_type == "best"){
			$(".best-list").html(html);
			$(".main-list-rec").trigger('resize');
		}else if(category_type == "md"){
			$(".mdpick-list").html(html);
			$(".main-list-rec").trigger('resize');
		}else if(category_type == "new"){
			$(".new-list").html(html);
			$(".main-list-rec").trigger('resize');
		}*/


	});
}

//메인 하단 상품
function mainBottomItemList(){
	$.ajax({
		type: "POST",
		data: "type=mobile",
		url: "../main/ajax_product_mainitem_bottom.php",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".bottom-item-list").html(html);
		$(".main-list-item").trigger('resize');
	});
}


//더 보기
function moreView(tab){
	var rownum = "";

	if(tab == "posting"){
	    rownum = $("#posting_rownum").val();
	}else if(tab == "review"){
	    rownum = $("#review_rownum").val();
	}else if(tab == "like"){
		rownum = $("#like_rownum").val();
	}

    if(rownum){
        $.ajax({
            type: "POST",
            url: "../m/ajax_moreview.php",
            data: "rownum="+ rownum+"&tab="+tab,
            contentType : "application/x-www-form-urlencoded; charset=UTF-8",
            error:function(request,status,error){
                //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        }).done(function(data){
        	$(".main-community-content").trigger("COMMUNITY_RESET");
        	/*if(tab == "posting"){
        		masonryAlign(".hot-posting");
        	}else if(tab == "review"){
        		masonryAlign(".hot-review");
        	}else if(tab == "like"){
        		masonryAlign(".hot-like");
        	}*/
        	 var arrData = data.split("|||");
//         	 console.log(arrData[1]);
			var $items;
            if(tab == "posting"){
                //$(".hot-posting").append(arrData[0]);
				$items = $(arrData[0]);
				$('.hot-posting').append( $items ).masonry( 'appended', $items );
                if(arrData[1] == ""){
                	$(".more_btn").hide();
                }else{
					$("#posting_rownum").val(arrData[1]-1);
                }
				masonryAlign(".hot-posting");
            }else if(tab == "review"){
                //$(".hot-review").append(arrData[0]);
				$items = $(arrData[0]);
				$('.hot-review').append( $items ).masonry( 'appended', $items );
                if(arrData[1] == ""){
                	$(".more_btn").hide();
                }else{
					$("#review_rownum").val(arrData[1]-1);
                }
				masonryAlign(".hot-review");
            }else if(tab == "like"){
                //$(".hot-like").append(arrData[0]);
				$items = $(arrData[0]);
				$('.hot-like').append( $items ).masonry( 'appended', $items );
                if(arrData[1] == ""){
                	$(".more_btn").hide();
                }else{
					$("#like_rownum").val(arrData[1]-1);
                }
				masonryAlign(".hot-like");
            }

        	$('.btn-related').click(function(){
        		$('.pop-related').fadeIn();
        	});

        	$(".btn-related").on("click",function(){
        		 var code = $(this).attr("idx");
        		 relatedView(code);
        	});

        });
    }else{
        $(".btn_list_more .mt-50").html('The End');// no results
    }
}


</script>
