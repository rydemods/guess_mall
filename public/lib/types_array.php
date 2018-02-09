<?php
//상품노출HTML
$list_products = array(
    'A' => '
			<li>
				<figure>
					<a href="javascript:prod_detail(\'[CODE]\');" id="prod_detail"><img src="[MINIMAGE]" alt=""></a>
					<div class="color-thumb">
						<ul>
							[COLOR_PROD]
						</ul>
					</div>
					<figcaption>
						<a href="javascript:;">
							<strong class="brand">[BAND]</strong>
							<p class="title">[NAME]</p>
							<span class="price"><del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>   <strong>[SELLPRICE]</strong></span>
						</a>
						<div class="star"><span class="comp-star star-score"><strong style="width:[REVIEW_MARK]%;"></strong></span>([REVIEW_CNT]) [LIKE]</div>
						<div class="tagset">[ICON]</div>
	               </figcaption>
				</figure>
			</li>',
    'B' => '<a href="[PRODUCTLINK]">
				<span class="img">
					<img src="[MINIMAGE]" alt="">
				</span>
				<div class="info_con">
					<span class="cate">[BAND]</span>
					<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
					<span class="name">[NAME]</span>
					<span class="price">
						<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
						<strong>[SELLPRICE]</strong>
					</span>
				</div>
				<div class="label">
					<!-- 아이콘 영역 -->
					[ICON]
				</div>
			</a>
			<div class="overinfo">
				<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
				<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
				<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
			</div>',
);

//치환된 값
$list_key = array(
	'[CODE]',               //상품코드
	'[NAME]',               //상품명
	'[SELLPRICE]',          //할인가
	'[CONSUMERPRICE]',      //판매가
	'[CONSUMER_CLASS]',     //판매가 CLASS
	'[BAND]',               //브랜드명
	'[MAXIMAGE]',           //큰이미지
	'[MINIMAGE]',           //중간이미지
	'[TINYIMAGE]',          //작은이미지
	'[COMMENT]',            //코맨트
	'[REVIEW]',             //리뷰수
	'[PRODUCTLINK]',        //상품링크
	'[ICON]',               //아이콘
	'[WISH]',               //위시리스트 CLASS
	'[PRODUCTLINK_REVIEW]', //상품리뷰링크
	'[COMMENT_COLOR]',      //MD코멘트 색깔
    '[OVERIMAGE]',          //롤오버 이미지
    '[ROLL_OVER_IMG]',     //이미지 롤오버
	'[REVIEW_MARK]',       //리뷰 별점
	'[REVIEW_CNT]',       //리뷰수
	'[LIKE]',                     //좋아요
	'[COLOR_PROD]',                     //좋아요
	'[C_CODE]',               //카테고리 코드
	'[SIZE_HTML]',             //상품사이즈
	'[HOT_CNT]',               //좋아요수
	'[PRODUCT_CNT]',           //상품카운트
	'[PRCODE_COLOR]',          //상품컬러코드
	'[SOLDOUT_CLASS]',         //솔드아웃 CLASS
	'[PRODUCT_OUTNAME]',        //메인상품명 전용 

);
//상품 리스트
$list_types = array(
	//W_001
	//메인 : MD PICK, ONLY C.A.S.H
	//대카테고리 : NEW ARRIVAL, MD PICK, ONLY CASH ....
	'W_001'=>array(
		'content_length'=>10,
		'tag'=>'<ul class="goods-list">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=> $list_products['A']
	),
	//W_002
	//메인 : ONLY C.A.S.H 바로 밑에 부분(좌측에 큰 배너이미지있고, 우측에 상품 4개)
	'W_002'=>array(
		'content_length'=>4,
		'tag'=>'<ul class="goods_list">[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li class="[CONTENT_CLASS]" >
				<a href="[PRODUCTLINK]">
					<span class="img">
						<img src="[MINIMAGE]" alt="" width="190" height="190">
					</span>
					<div class="info_con">
						<span class="cate">[BAND]</span>
						<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
						<span class="goods-nm">[NAME]</span>
						<span class="price">
							<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
							<strong>[SELLPRICE]</strong>
						</span>
					</div>
					<div class="label">
						<!-- 아이콘 영역 -->
						[ICON]
					</div>
				</a>
				<div class="overinfo">
					<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
					<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
					<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
				</div>
            </li>'
	),
	//W_003
	//메인 : LOOKBOOK 바로 위 (좌측은 제외하고 나머지 상품 7개)
	'W_003'=>array(
		'content_length'=>7,
		'tag'=>'[CONTENT]',
		'content_class'=>array( 'big', '', '', 'small', 'small', '', '' ),
		'img_width'=>array( '365', '270', '270', '180', '180', '270', '270' ),
		'img_height'=>array( '365', '270', '270', '180', '180', '270', '270' ),
		'content'=>'
			<li class="[CONTENT_CLASS]" >
				<a href="[PRODUCTLINK]">
					<span class="img">
						<img src="[MINIMAGE]" alt="" width="[IMG_WIDTH]" height="[IMG_HEIGHT]">
					</span>
					<div class="info_con">
						<span class="cate">[BAND]</span>
						<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
						<span class="name">[NAME]</span>
						<span class="price">
							<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
							<strong>[SELLPRICE]</strong>
						</span>
					</div>
					<div class="label">
						<!-- 아이콘 영역 -->
						[ICON]
					</div>
				</a>
				<div class="overinfo">
					<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
					<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
					<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
				</div>
            </li>'
	),
	//W_004
	//메인 : LOOKBOOK 상품 리스트
	'W_004'=>array(
		'content_length'=>2,
		'tag'=>'<ul class="goods_list">[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li class="[CONTENT_CLASS]" >
				<a href="[PRODUCTLINK]">
					<span class="img">
						<img src="[MINIMAGE]" alt="" width="185" height="184">
					</span>
					<div class="info_con">
						<span class="cate">[BAND]</span>
						<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
						<span class="name">[NAME]</span>
						<span class="price">
							<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
							<strong>[SELLPRICE]</strong>
						</span>
					</div>
					<div class="label">
						<!-- 아이콘 영역 -->
						[ICON]
					</div>
				</a>
				<!-- <div class="overinfo">
					<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
					<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
					<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
				</div> -->
            </li>'
	),
	//W_005
	//대카테고리 TODAY PICK
	'W_005'=>array(
		'content_length'=>1,
		'tag'=>'<div class="goods_list">[CONTENT]</div>',
		'content_class'=>array(),
        'content' => $list_products['B']
	),
	//W_006
	//대카테고리 WEEKLY BEST 7
	'W_006'=>array(
		'content_length'=>7,
		'tag'=>'<ol class="goods_list">[CONTENT]</ol>',
		'content_class'=>array( 'num_big', 'num_small', 'num_small', 'num_small', 'num_small', 'num_small', 'num_small' ),
		'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<span class="img">
						<img src="[MINIMAGE]" alt="">
					</span>
					<div class="info_con">
						<span class="cate">[BAND]</span>
						<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
						<span class="name">[NAME]</span>
						<span class="price">
							<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
							<strong>[SELLPRICE]</strong>
						</span>
					</div>
					<div class="label">
						<!-- 아이콘 영역 -->
						[ICON]
					</div>
				</a>
				<div class="overinfo">
					<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
					<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
					<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
				</div>
				<div class="[CONTENT_CLASS]"><img src="[WEEKLY_BEST_ICON]" alt=""></div>
				<!--1번 외엔 .num_small <div class="num_small"><img src="../static/img/icon/best_num02.png" alt=""></div>-->
			</li>'
	),
	//W_007
	//대카테고리 BEST REVIEW 내 상품정보
	'W_007'=>array(
		'content_length'=>1,
		'tag'=>'<div class="goods_list">[CONTENT]</div>',
		'content_class'=>array(),
		'content'=>'
			<a href="[PRODUCTLINK]">
				<span class="img">
					<img src="[MINIMAGE]" alt="">
				</span>
				<div class="info_con">
					<span class="cate">[BAND]</span>
					<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
					<span class="name">[NAME]</span>
					<span class="price">
						<del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>
						<strong>[SELLPRICE]</strong>
					</span>
				</div>
				<div class="label">
					<!-- 아이콘 영역 -->
					[ICON]
				</div>
			</a>
			<div class="overinfo">
				<button type="button" class="star-wish [WISH]" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');">위시리스트</button>
				<button type="button" class="rview" onClick="location.href=\'[PRODUCTLINK_REVIEW]\';">[REVIEW]</button>
				<button type="button" class="pview" onClick="javascript:setProductPopup(\'[CODE]\');">미리보기</button>
			</div>'
	),
	//W_008
	//대카테고리 BRAND - NEW BRAND, BEST BRAND
	'W_008'=>array(
		'content_length'=>3,
		'tag'=>'<ul class="goods_list">[CONTENT]</ul>',
		'content_class'=>array(),
        'content' => $list_products['A']
	),
	//W_009
	//상품상세 : RELATED PRODUCT
	'W_009'=>array(
		'content_length'=>7,
		'tag'=>'<ul class="related-list">[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<img src="[MINIMAGE]" alt="[NAME]">
						<figcaption>
							# [BAND]<br>
							[NAME]
						</figcaption>
					</figure>
				</a>
			</li>'
	),
	//W_010
	//브랜드 상품 메인 : 하단 상품 리스트(페이징 있음)
	'W_010'=>array(
		'content_length'=>80,
		'tag'=>'<ul class="comp-goods item-list" >[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li>
				<figure>
					<a href="javascript:prod_detail(\'[CODE]\');" id="prod_detail"><img src="[MINIMAGE]" alt=""></a>
					<div class="color-thumb">
						<ul>
							[COLOR_PROD]
						</ul>
					</div>
					<figcaption>
						<a href="javascript:prod_detail(\'[CODE]\');">
							 <strong class="brand">[BAND]</strong>
							<p class="title">[NAME]</p>
							<span class="price"><del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>   <strong>[SELLPRICE]</strong></span>
						</a>
						<div class="star"><span class="comp-star star-score"><strong style="width:[REVIEW_MARK]%;"></strong></span>([REVIEW_CNT]) [LIKE]</div>
						<div class="tagset">
							[ICON]
						</div>
					</figcaption>
				</figure>
			</li>'
	),
	//W_011
	//프로모션 상품형 페이지
	'W_011'=>array(
		'content_length'=>100,
		'tag'=>'<ul class="comp-goods item-list" >[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>$list_products['A']
	),

	//W_012
	//프로모션 > PRESS : 상품
	'W_012'=>array(
		'content_length'=>3,
		'tag'=>'<ul class="thumb-width-rolling-goods goods-over-ea3">[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li>
				<img src="[TINYIMAGE]" alt="">
			</li>
			'
	),
	//W_013
	//프로모션 > 스타가 되고 싶니 : 상품
	'W_013'=>array(
		'content_length'=>10,
		'tag'=>'<ul class="thumb-width-rolling-goods goods-over-ea3">[CONTENT]</ul>',
		'content_class'=>array(),
		'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<img src="[TINYIMAGE]" alt="">
					<div class="price-info-box">
						<p class="brand-nm">[BAND]</p>
						<p class="goods-nm">[NAME]</p>
						<p class="price"><del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>[SELLPRICE]</p>
					</div>
				</a>
			</li>
			'
	),
    //W_014
    //브랜드 위시리스트 브랜드별 상품 3개
	'W_014'=>array(
		'content_length'=>3,
		'tag'=>'<ul class="goods-list">[CONTENT]</ul>',
		'content_class'=>array(),
        'content' => $list_products['A']
	),
    //W_015
    //모바일 버젼 상품 리스트
	'W_015'=>array(
		'content_length'=>10,
		'tag'=>'<ul class=""></ul>',
		'content_class'=>array(),
        'content'=>'
            <li>
				<figure>
					<a href="[PRODUCTLINK]"><img src="[MINIMAGE]" alt=""></a>
					<figcaption>
						<a href="[PRODUCTLINK]">
							<p class="title"><strong class="brand">[[BAND]]</strong><span class="name">[NAME]</span></p>
							<span class="price"><del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>   <strong>[SELLPRICE]원</strong></span>
							<div class="star"><span class="comp-star star-score"><strong style="width:[REVIEW_MARK]%;"></strong></span>([REVIEW_CNT])</div>
							<div class="tagset">
								[ICON]
							</div>
						</a>
						[LIKE]
					</figcaption>
					 <input type="hidden" name="s_search_category2" id="s_search_category2" value = [C_CODE]>
				</figure>
			</li>'
	),
    //W_016
    //모바일 버젼 관련 상품 리스트
	'W_016'=>array(
		'content_length'=>12,
		'tag'=>'<ul>[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
				<li><a href="[PRODUCTLINK]"><img src="[MINIMAGE]" alt=""></a></li>'
	),
    //W_017
    //모바일 메인페이지 하단 "NEW BRAND" 배너 양옆 부분
	'W_017'=>array(
		'content_length'=>10,
		'tag'=>'<ul class="js-goods-list">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
            <li>
                <a href="[PRODUCTLINK]">
                    <figure>
                        <div class="img"><img src="[MINIMAGE]" alt=""></div>
                        <figcaption>
                            <ul class="tag-list">
                                [ICON]
                            </ul>
                            <span class="brand">[BAND]</span>
							<span class="comment" style="color:#[COMMENT_COLOR]" >[COMMENT]</span>
                            <span class="name">[NAME]</span>
                            <span class="price"><strong>[SELLPRICE]</strong></span>
                        </figcaption>
                    </figure>
                </a>
                <button class="btn-wishlist [WISH]" type="button" onClick="javascript:setProductWishList(this, \'[CODE]\', \'' . $_SERVER['REQUEST_URI'] . '\');" title="담겨짐"><span class="ir-blind">위시리스트 담기/버리기</span></button>
            </li>'
	),
    //W_018
    //모바일 버젼 프로모션 상품 리스트
	'W_018'=>array(
		'content_length'=>10,
		'tag'=>'<ul>[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<div class="tagset">
							[ICON]
						</div>
						<img src="[MINIMAGE]" alt="">
						<figcaption>
							<strong class="brand">[BAND]</strong>
							<p class="title">[NAME]</p>
							<span class="price"><del class="[CONSUMER_CLASS]">[CONSUMERPRICE]</del>   <strong>[SELLPRICE]</strong></span>
						</figcaption>
					</figure>
				</a>
			</li>'
	),
	 //신원전용 메인
	'M_001'=>array(
		'content_length'=>100,
		'tag'=>'<ul id="new-arrivals" class="clear">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<div class="goods-item">
					<div class="thumb-img">
						<a href="[PRODUCTLINK]">
							<img src="[TINYIMAGE]" alt="">
						</a>
						<div class="layer">
							<div class="btn">
								<button type="button" class="btn-preview" onclick="productLayer(\'[CODE]\');"><span><i class="icon-cart">장바구니</i></span>
								</button>
								[LIKE]
							</div>
							[SIZE_HTML]
						</div>
					</div>
					<!-- //.thumb-img -->
					<div class="price-box">
						<div class="brand-nm">[BAND]</div>
						<div class="goods-nm">[PRODUCT_OUTNAME]</div>
						<div class="price"><del class=[CONSUMER_CLASS]>\[CONSUMERPRICE]</del><span class=[SOLDOUT_CLASS]>\</span>[SELLPRICE]</div>
						<div class="color-chip">
							[PRCODE_COLOR]
						</div>
						<div class="goods-icon">
							[ICON]
						</div>
					</div>
				</div> <!-- //.goods-item -->
			</li>'
	),
	'M_002'=>array(
		'content_length'=>100,
		'tag'=>'[CONTENT]',
		'content_class'=>array(),
        'content'=>'
			<div class="goods-box[PRODUCT_CNT]">
				<div class="rank">BEST<strong>[PRODUCT_CNT]</strong></div>
				<div class="goods-item">
					<div class="thumb-img">
						<a href="[PRODUCTLINK]">
							<img src="[TINYIMAGE]" alt="">
						</a>
					</div><!-- //.thumb-img -->
					<div class="price-box">
						<div class="brand-nm">[BAND]</div>
						<div class="goods-nm">[PRODUCT_OUTNAME]</div>
						<div class="price"><del class=[CONSUMER_CLASS]>\[CONSUMERPRICE]</del><span class=[SOLDOUT_CLASS]>\</span>[SELLPRICE]</div>
						<!--<div class="color-chip">
							[PRCODE_COLOR]
						</div>
						<div class="goods-icon">
							[ICON]
						</div>-->
					</div>
				</div><!-- //.goods-item -->
			</div>'
	),
	'M_003'=>array(
		'content_length'=>100,
		'tag'=>'[CONTENT]',
		'content_class'=>array(),
        'content'=>'
			<div class="goods-box[PRODUCT_CNT]">
				<div class="rank">RANKING<strong>[PRODUCT_CNT]</strong></div>
				<div class="goods-item">
					<div class="thumb-img">
						<a href="[PRODUCTLINK]">
							<img src="[TINYIMAGE]" alt="">
						</a>
					</div><!-- //.thumb-img -->
					<div class="price-box">
						<div class="brand-nm">[BAND]</div>
						<div class="goods-nm">[PRODUCT_OUTNAME]</div>
						<div class="price"><del class=[CONSUMER_CLASS]>\[CONSUMERPRICE]</del><span class=[SOLDOUT_CLASS]>\</span>[SELLPRICE]</div>
						<!--<div class="color-chip">
							[PRCODE_COLOR]
						</div>
						<div class="goods-icon">
							[ICON]
						</div>-->
					</div>
				</div><!-- //.goods-item -->
			</div>'
	),
	
	 //신원전용 리스트
	'S_001'=>array(
		'content_length'=>10,
		'tag'=>'<ul>[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<div class="goods-item">
					<div class="thumb-img">
						<a href="[PRODUCTLINK]"><img src="[MINIMAGE]" alt="상품 썸네일"></a>
						<div class="layer">
							<div class="btn">
								<button type="button"  onclick="productLayer(\'[CODE]\');" class="btn-preview"><span><i class="icon-cart">장바구니</i></span></button>
								[LIKE]
							</div>
							[SIZE_HTML]
						</div>
					</div><!-- //.thumb-img -->
					<div class="price-box">
						<div class="brand-nm">[BAND]</div>
						<div class="goods-nm">[NAME]</div>
						<div class="price"><del class=[CONSUMER_CLASS]>\[CONSUMERPRICE]</del><span class=[SOLDOUT_CLASS]>\</span>[SELLPRICE]</div>
						<div class="color-chip">
							[PRCODE_COLOR]
						</div>
						<div class="goods-icon">
							[ICON]
						</div>
					</div>
				</div>
			</li>'
	),

	//신원전용 모바일 메인
	'MO_001'=>array(
		'content_length'=>100,
		'tag'=>'<ul class="goodslist">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<div class="img"><img src="[TINYIMAGE]" alt="상품 이미지"></div>
						<figcaption>
							<p class="brand">[BAND]</p>
							<p class="name">[NAME]</p>
							<p class="price"><span class=[SOLDOUT_CLASS]>￦ </span>[SELLPRICE]</p>
							<div class="tagset">
								[ICON]
							</div>
						</figcaption>
					</figure>
				</a>
			</li>
		'
	),

	'MO_002'=>array(
		'content_length'=>100,
		'tag'=>'<ul class="goodslist">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<div class="img"><img src="[TINYIMAGE]" alt="상품 이미지"></div>
						<figcaption class="vmd">
							<div>
								<p class="name">[NAME]</p>
								<p class="price"><span class=[SOLDOUT_CLASS]>￦ </span>[SELLPRICE]</p>
							</div>
						</figcaption>
						<span class="tag_best">BEST<strong>[PRODUCT_CNT]</strong></span>
					</figure>
				</a>
			</li>
			<img src="/sinwon/m/static/img/btn/best_slide_larr.png" class="left">
			<img src="/sinwon/m/static/img/btn/best_slide_rarr.png" class="right">
		'
	),

	'MO_003'=>array(
		'content_length'=>100,
//		'tag'=>'<ul class="goodslist col3">[CONTENT]</ul>',			// col1,col2,col3 각각 이미지노출 갯수
		'tag'=>'<ul class="goodslist col2">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<div class="img"><img src="[TINYIMAGE]" alt="상품 이미지"></div>
						<figcaption>
							<p class="name">[NAME]</p>
							<p class="price"><span class=[SOLDOUT_CLASS]>￦ </span>[SELLPRICE]</p>
						</figcaption>
					</figure>
				</a>
				<span class="ranking_tag">Ranking <strong>[PRODUCT_CNT]</strong></span>
			</li>

		'
	),

	'MO_004'=>array(
			'content_length'=>100,
			'tag'=>'<ul class="goodslist col3">[CONTENT]</ul>',
			'content_class'=>array(),
			'content'=>'
		<li>
			<a href="[PRODUCTLINK]">
				<figure>
					<div class="img"><img src="[TINYIMAGE]" alt="상품 이미지"></div>
					<figcaption>
						<p class="name">[NAME]</p>
						<p class="price"><span class=[SOLDOUT_CLASS]>￦ </span>[SELLPRICE]</p>
					</figcaption>
				</figure>
			</a>
			<span class="ranking_tag">Ranking <strong>[PRODUCT_CNT]</strong></span>
		</li>
	
	'
	),
		
	//신원전용 모바일 리스트
	'SMO_001'=>array(
		'content_length'=>10,
		'tag'=>'<ul class="goodslist">[CONTENT]</ul>',
		'content_class'=>array(),
        'content'=>'
			<li>
				<a href="[PRODUCTLINK]">
					<figure>
						<div class="img"><img src="[TINYIMAGE]" alt="상품 이미지"></div>
						<figcaption>
							<p class="brand">[BAND]</p>
							<p class="name">[NAME]</p>
							<p class="price"><del class="vertical-align: top; [CONSUMER_CLASS]">￦[CONSUMERPRICE]</del><span class=[SOLDOUT_CLASS]>￦ </span>[SELLPRICE]</p>
							<div class="color">
								[PRCODE_COLOR]
							</div>
							<div class="tagset">
								[ICON]
							</div>
						</figcaption>
					</figure>
				</a>
			</li>
		'
	),
);

?>
