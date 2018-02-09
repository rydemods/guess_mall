<?php
    // COLLECTION 등록 데이터 
    $sql  = "SELECT * FROM tblimgcollectionmain LIMIT 1";
    $result = pmysql_query($sql);
    $rowData = pmysql_fetch_object( $result );
    
    // STAGE별 사진 리스트
    $sql  = "SELECT a.*, b.id as member_id ";
    $sql .= "FROM tblimgcollectionlist a left join tblbrandwishlist b on a.bridx = b.bridx and b.id = '" . $_ShopInfo->getMemid() . "' ";
    $sql .= "WHERE a.stage in ('2', '3', '4', '5') and a.hidden = 1 ";
    $sql .= "ORDER BY a.stage, sort ";
    $result = pmysql_query($sql);

    $arrImgList = array();
	$arrLinkList = array();
	$arrTargetList = array();

    $arrBrandList = array();
    while ( $row = pmysql_fetch_array( $result ) ) {
        if ( !isset($arrImgList[$row['stage']]) ) {
            $arrImgList[$row['stage']] = array();
			$arrLinkList[$row['stage']] = array();
			$arrTargetList[$row['stage']] = array();
        }

        if ( $row['stage'] == '4' ) {
            // STAGE4는 카테고리 별로 리스트가 따로 있어야 함.
            if ( !isset($arrImgList[$row['stage']][$row['cate']]) ) {
                $arrImgList[$row['stage']][$row['cate']] = array();
				$arrLinkList[$row['stage']][$row['cate']] = array();
				$arrTargetList[$row['stage']][$row['cate']] = array();
            }

            array_push( $arrImgList[$row['stage']][$row['cate']], "/data/shopimages/collection/" . $row['img'] );
			array_push( $arrLinkList[$row['stage']][$row['cate']], $row['link'] );
			array_push( $arrTargetList[$row['stage']][$row['cate']], $row['target'] );
        } else {
            array_push( $arrImgList[$row['stage']], "/data/shopimages/collection/" . $row['img'] );
			array_push( $arrLinkList[$row['stage']], $row['link'] );
			array_push( $arrTargetList[$row['stage']], $row['target'] );

            if ( $row['stage'] == '5' ) {
                // STAGE5는 브랜드명과 브랜드idx가 필요(for wishlist)
                if ( !isset($arrBrandList['brand_name']) ) { $arrBrandList['brand_name'] = array(); }           
                if ( !isset($arrBrandList['brand_idx']) ) { $arrBrandList['brand_idx'] = array(); }           
                if ( !isset($arrBrandList['brand_wish_id']) ) { $arrBrandList['brand_wish_id'] = array(); }           

                array_push($arrBrandList['brand_name'], $row['brname']);
                array_push($arrBrandList['brand_idx'], $row['bridx']);
                array_push($arrBrandList['brand_wish_id'], $row['member_id']);  
            }
        }
    }

    // STAGE4에 노출되는 카테고리 정보
    $sql  = "SELECT * FROM tblimgcollectioncate ORDER BY no asc ";
    $result = pmysql_query($sql);

    $arrCategoryName = array();
    while ( $row = pmysql_fetch_array( $result ) ) {
        $arrCategoryName[$row['no']] = $row['name'];
    }
?>
	
	<script type="text/javascript">
	//클릭시 스크롤 이동 처리
		function moveToScroll(idx, cont_id){
		//	alert($("#"+cont_id+"0"+idx).height());
			var scroll_top = 0;
			scroll_top = $("#"+cont_id+"0"+idx).offset().top;
			$('html, body').animate({scrollTop:scroll_top},500);
		}
		$('.paginate ul li').click(function(){
			$('.paginate ul li').removeClass('strong');
			$(this).addClass('strong');
		})
		//스크롤 변화로 인한 네비게이션 이동
			$(window).scroll(function() {
				if($('#wrap02').offset().top > $(window).scrollTop() + 72){
					$('.paginate ul li').removeClass('strong');
					$('.paginate ul').children(':eq(0)').addClass('strong');
				}else if($('#wrap03').offset().top > $(window).scrollTop() + 72){
					$('.paginate ul li').removeClass('strong');
					$('.paginate ul').children(':eq(1)').addClass('strong');
				}else if($('#wrap04').offset().top > $(window).scrollTop() + 72){
					$('.paginate ul li').removeClass('strong');
					$('.paginate ul').children(':eq(2)').addClass('strong');
				}else if($('#wrap05').offset().top > $(window).scrollTop() + 72){
					$('.paginate ul li').removeClass('strong');
					$('.paginate ul').children(':eq(3)').addClass('strong');
				}else if($('#wrap05').offset().top < $(window).scrollTop() + 72){
					$('.paginate ul li').removeClass('strong');
					$('.paginate ul').children(':eq(4)').addClass('strong');
				}
			});
	</script>
<style type="text/css">
#header {}
.containerBody {padding-top:0px;}

.sector01 {background:none !important; height:auto !important;}
.sector01 img {width:100%;}
</style>
	<div id="contents">
		<div class="breadcrumb">
			<ul>
				<li><a href="/">HOME</a></li>
				<li class="on"><a href="<?=$_SERVER["REQUEST_URI"]?>">COLLECTION</a></li>
			</ul>
		</div><!-- //.breadcrumb -->

		<div class="collection-wrap">

			<div class="paginate">
				<!-- 활성화 되었을 때 li에 class="strong" 추가 -->
				<ul>
					<li class="strong"><a onclick="moveToScroll(1,'wrap');"></a></li>
					<li><a onclick="moveToScroll(2,'wrap');"></a></li>
					<li><a onclick="moveToScroll(3,'wrap');"></a></li>
					<li><a onclick="moveToScroll(4,'wrap');"></a></li>
					<li><a onclick="moveToScroll(5,'wrap');"></a></li>
				</ul>
			</div>
			
			<a name="sector01"></a>
			<!-- <div class="sector01 scroll-local" id="wrap01" style="background:url('/data/shopimages/collection/<?=$rowData->stage1_img?>') center no-repeat;">
			</div> -->
			<div class="sector01 scroll-local" id="wrap01">
				<img src="/data/shopimages/collection/<?=$rowData->stage1_img?>" alt="">
			</div>
			
			<a name="sector02"></a>
			<div class="sector02 scroll-local" id="wrap02" style="background:url('/data/shopimages/collection/<?=$rowData->stage2_bg?>') center no-repeat;">
				<div class="inner-box">
					
					<div class="collection-rolling-wrap with-btn-rolling right"><!-- 우측으로 정렬시에 right 클래스 추가 -->
						<div class="rollgin-title"><?=$rowData->stage2_text?><span><?=$rowData->stage2_subtext?></span></div>
						<div class="inner">
							<ul class="rolling-screen" id="rolling-screen_1">
								<?php 
									$sectionNum = 2;
									$loopCnt = count($arrImgList[$sectionNum]);

									for ( $i = 0; $i < $loopCnt; $i++ ) {
										$imgUrl = $arrImgList[$sectionNum][$i];
										$linkUrl = $arrLinkList[$sectionNum][$i];
										$target = $arrTargetList[$sectionNum][$i];

										echo "<li>";
										if ( !empty($linkUrl) ) {
											echo "<a href='{$linkUrl}' target='{$target}'>";
										}
										echo "<img src=\"{$imgUrl}\" alt=\"\">";
										if ( !empty($linkUrl) ) {
											echo "</a>";
										} 
										echo "</li>";
									}
								?>
							</ul>
						</div>
					</div>

				</div>
			</div>

			<a name="sector03"></a>
			<div class="sector03 scroll-local" id="wrap03" style="background:url('/data/shopimages/collection/<?=$rowData->stage3_bg?>') center no-repeat;">
				<div class="inner-box">

					<div class="collection-rolling-wrap with-btn-rolling "><!-- 우측으로 정렬시에 right 클래스 추가 -->
						<div class="rollgin-title"><?=$rowData->stage3_text?><span><?=$rowData->stage3_subtext?></span></div>
						<div class="inner">
							<ul class="rolling-screen" id="rolling-screen_2">

								<?php 
									$sectionNum = 3;
									$loopCnt = count($arrImgList[$sectionNum]);

									for ( $i = 0; $i < $loopCnt; $i++ ) {
										$imgUrl = $arrImgList[$sectionNum][$i];
										$linkUrl = $arrLinkList[$sectionNum][$i];
										$target = $arrTargetList[$sectionNum][$i];

										echo "<li>";
										if ( !empty($linkUrl) ) {
											echo "<a href='{$linkUrl}' target='{$target}'>";
										}
										echo "<img src=\"{$imgUrl}\" alt=\"\">";
										if ( !empty($linkUrl) ) {
											echo "</a>";
										} 
										echo "</li>";
									}
								?>
							</ul>
						</div>
					</div>

				</div>
			</div>

			<a name="sector04"></a>
			<div class="sector04 scroll-local" id="wrap04" style="background:url('/data/shopimages/collection/<?=$rowData->stage4_bg?>') center no-repeat;">
				<div class="inner-box">
					
					<div class="screen-title"><?=$rowData->stage4_text?><span><?=$rowData->stage4_subtext?></span></div>

                    <?php 
                        $stage = 4;
                        foreach ( $arrCategoryName as $cate_idx => $cate_name ) { 
                            if ( isset($arrImgList[$stage][$cate_idx]) ) {
                    ?>

					<div class="collection-rolling-mini-wrap with-btn-rolling ">
						<div class="inner">
							<ul class="rolling-screen-mini" id="rolling_screen_mini_<?=$cate_idx?>">
                                <?php 					
								$loopCnt =  count($arrImgList[$stage][$cate_idx]);

								for ( $i = 0; $i < $loopCnt; $i++ ) {
										$imgUrl = $arrImgList[$stage][$cate_idx][$i];
										$linkUrl = $arrLinkList[$stage][$cate_idx][$i];
										$target = $arrTargetList[$stage][$cate_idx][$i];

										echo "<li>";
										if ( !empty($linkUrl) ) {
											echo "<a href=\"{$linkUrl}\" target=\"{$target}\">";
										}
										echo "<img src=\"{$imgUrl}\" alt=\"\">";
										if ( !empty($linkUrl) ) {
											echo "</a>";
										}
										echo "</li>";
								}
								?>
							</ul>
						</div>
						<div class="rollgin-title"><?=$cate_name?></div>
					</div>
     
                        <?php 
                            // 롤링이 필요할때만 되게 처리
                            if ( count($arrImgList[$stage][$cate_idx]) >= 2 ) { 
                        ?>   
                        <script type="text/javascript">
                            $("#rolling_screen_mini_<?=$cate_idx?>").bxSlider({
                                slideWidth: 250,
                                minSlides: 1,
                                slideMargin: 0,
                                pager:false
                            });
                        </script>
                        <?php } ?>

                    <?php 
                            } 
                        }
                    ?>
				</div>
			</div>
			

			<a name="sector05"></a>

            <div class="sector06 scroll-local" id="wrap05">
                <div class="brand-floor1">
                    <div class="inner">
                        <ul class="three">
                        <?php 
                            $stage = 5;
                            $arrDivClass = array("left", "", "right", "left", "", "", "right");
                            for ( $i = 0; $i < 3; $i++ ) { 
                                // 이미 위시리스트에 있으면 on 클래스 추가
                                $classOn = "";
                                if ( !empty($arrBrandList['brand_wish_id'][$i]) ) {
                                    $classOn = "on";
                                }

                                echo '
                                    <li>
                                        <div class="brand-show"><!-- 중앙 1개 -->
                                            <img src="' . $arrImgList[$stage][$i] . '" alt="">
                                            <p class="brand-nm">' . $arrBrandList['brand_name'][$i] . '</p>
                                            <div class="brand-more">
                                                <div class="align">
												<a href="/front/brand_detail.php?bridx=' . $arrBrandList['brand_idx'][$i] . '" class="view">BRAND VIEW</a>
                                                <button class="wish-star ' . $classOn . '" type="button" onclick="javascript:setBrandWishList(this, \'' . $arrBrandList['brand_idx'][$i] . '\', \'/' . getUrl() . '\')">위리시스트 추가</button><!-- 위시리스트 추가 될시 on 클래스 추가 -->
												</div>
                                            </div>
                                        </div>
                                    </li>
                                ';
                        } ?>
                        </ul>
					</div><!-- //.inner -->

				</div><!-- //.brand-floor1 -->
				<div class="brand-floor2">
					
					<div class="inner">
						<ul class="four">

                        <?php 
                            for ( $i = 3; $i < 7; $i++ ) { 
                                // 이미 위시리스트에 있으면 on 클래스 추가
                                $classOn = "";
                                if ( !empty($arrBrandList['brand_wish_id'][$i]) ) {
                                    $classOn = "on";
                                }

                                echo '
                                    <li>
                                        <div class="brand-show"><!-- 중앙 1개 -->
                                            <img src="' . $arrImgList[$stage][$i] . '" alt="">
                                            <p class="brand-nm">' . $arrBrandList['brand_name'][$i] . '</p>
                                            <div class="brand-more">
                                                <div class="align">
												<a href="/front/brand_detail.php?bridx=' . $arrBrandList['brand_idx'][$i] . '" class="view">BRAND VIEW</a>
                                                <button class="wish-star ' . $classOn . '" type="button" onclick="javascript:setBrandWishList(this, \'' . $arrBrandList['brand_idx'][$i] . '\', \'' . getUrl() . '\')">위리시스트 추가</button><!-- 위시리스트 추가 될시 on 클래스 추가 -->
												</div>
                                            </div>
                                        </div>
                                    </li>
                                ';
                        } ?>
                        </ul>
					</div><!-- //.inner -->

				</div><!-- //.brand-floor2 -->
				
			</div>

		<button class="section-up" type="button"></button>

		</div>

	</div>

