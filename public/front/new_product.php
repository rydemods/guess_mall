<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<div id="contents">
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">NEW</li>
		</ul>
	</div>
	<main class="new-product">
        <div class="goods-list">
            <!-- 상품리스트 - 상품 -->
            <section class="goods-list-item">
				<div class="comp-select sorting">
					<select title="상품정렬순" id="" onchange="">
						<option value="" selected="">브랜드 전체</option>
						<option value="">NIKE</option>
					</select>
				</div>
                <!--
                    (D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
                    좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
                    페이지 변경할 때 페이지 리로드가 아닌 ajax로 연동하거나,
                    더보기 등으로 리스트 하단에 상품이 추가될 경우,
                    컬러 썸네일 스크립트 적용을 위해 내용 변경 후 color_slider_control() 함수를 호출해주세요.
                -->
                <ul class="comp-goods">
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/PUMA/36146601-yellow/36146601-Bright Gold-Puma Black_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
					<li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/NIKE/819719-white/819719-100_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/NIKE/596728-black/596728-026_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/REEBOK/BD1436-gray/BD1436-ASTEROID DUSTWHITE_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/REEBOK/BD1436-gray/BD1436-ASTEROID DUSTWHITE_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/PUMA/36146602-black/36146602-Puma Black-Electric Blue Lemonade_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/ADIDAS/BB4977-white/BB4977-FTWWHTFTWWHTBLUE_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/NIKE/615957-white/615957-100_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                    <li>
                        <figure>
                            <a href="javascript:void(0);"><img src="../data/shopimages/product/NIKE/824463-rainbow/824463-063_01_m.jpg" alt=""></a>
                            <div class="color-thumb">
                                <ul>
                                    <li><a href="javascript:void(0);"><img src="../static/img/test/@test_list_item_thumb1.jpg" alt="white"></a></li>
                                </ul>
                            </div>
                            <figcaption>
                                <a href="javascript:void(0);">
                                    <strong class="brand">ADIDAS</strong>
                                    <p class="title">Adidas Gazelle 아디다스 가젤</p>
                                    <span class="price"><strong>100,000원</strong></span>
                                </a>
								 <div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(55)
									<button class="comp-like btn-like" title="선택안함"><span><strong>좋아요</strong>159</span></button>
								 </div>
								 <div class="tagset">
									<span class="img"><img src="../images/common/icon01.gif" border="0" align="absmiddle"></span>
								 </div>
                            </figcaption>
                        </figure>
                    </li>
                </ul>
                <div class="list-paginate">
					<span class="border_wrap">
						<a href="javascript:;" class="prev-all"></a>
						<a href="javascript:;" class="prev"></a>
					</span>
					<a class="on">1</a>
					<a href="javascript:GoPage(0,2);">2</a>
					<a href="javascript:GoPage(0,3);">3</a>
					<a href="javascript:GoPage(0,4);">4</a>
					<a href="javascript:GoPage(0,5);">5</a>
					<span class="border_wrap">
						<a href="javascript:GoPage(1,2);" class="next"></a>
						<a href="javascript:GoPage(0,5);" class="next-all"></a>
					</span>
				</div>
            </section>
            <!-- 상품리스트 - 상품 -->
        </div>
    </main>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->



<?php
include ($Dir."lib/bottom.php")
?>
