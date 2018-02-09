<?
    $pathDir = "..";
    if ( $_SERVER['PHP_SELF'] == "/m/index.php" ) {
        $pathDir = ".";
    }
?>


<!-- 푸터 -->
        <footer id="footer">
            <nav class="menu">
                <ul>
                    <li><a href="#">회원가입</a></li>
                    <li><a href="../member/login.php">로그인</a></li>
                    <li><a href="../cs/csFaq.php">CS CENTER</a></li>
                    <li><a href="#">PC버전</a></li>
                </ul>
            </nav>
            <div class="js-brand">
                <div class="js-brand-list">
                    <ul>
                        <li class="js-brand-content"><a href="#"><img src="../static/img/test/@footer_brand_ninesix.png" alt="NINESIX NY"></a></li>
                        <li class="js-brand-content"><a href="#"><img src="../static/img/test/@footer_brand_anacapri.png" alt="ANACAPRI"></a></li>
                    </ul>
                </div>
                <button class="js-brand-arrow" data-direction="prev" type="button"><span class="ir-blind">이전</span></button>
                <button class="js-brand-arrow" data-direction="next" type="button"><span class="ir-blind">다음</span></button>
                <ul class="js-brand-sns">
                    <li class="js-brand-sns-content on">
                        <a href="#"><img src="../static/img/btn/btn_footer_brand_sns_instagram.png" alt="NINESIX NY 인스타그램"></a>
                        <a href="#"><img src="../static/img/btn/btn_footer_brand_sns_facebook.png" alt="NINESIX NY 페이스북"></a>
                    </li>
                    <li class="js-brand-sns-content">
                        <a href="#"><img src="../static/img/btn/btn_footer_brand_sns_instagram.png" alt="ANACAPRI 인스타그램"></a>
                        <a href="#"><img src="../static/img/btn/btn_footer_brand_sns_facebook.png" alt="ANACAPRI 페이스북"></a>
                    </li>
                </ul>
            </div>
            <div class="footer-content">
                <address>
                    고객센터 주소 : (138-130) 서울시 송파구 위례성대로22길 21 데코앤이<br>
                    고객센터 전화 : <a class="btn-tel" href="tel:02-2145-1400">02-2145-1400</a><br>
                    이메일 : <a href="mailto:cash@cash-stores.com">cash@cash-stores.com</a><br>
                    사업자 등록번호 : 230-81-45177<br>
                    통신판매업 신고번호 : 제 2015-서울송파-0881호<br>
                    대표 : (주)데코앤컴퍼니 정인견<br>
                </address>
                <br>
                <ul class="terms">
                    <li><a href="#">이용약관</a></li>
                    <li><a class="btn-privacy" href="#">개인정보 취급방침</a></li>
                </ul>
                <br>
                <span class="copyright">&copy; 2015 C.A.S.H CO.,LTD. ALL RIGHTS RESERVED</span>
            </div>
            <!-- (D) 폰트 사이즈는 최소사이즈 data-min(10 변경 불가), 최대사이즈 data-max를 변경하면 됩니다. -->
            <div class="js-font" data-min="10" data-max="15">
                <button class="js-btn-small" type="button"><img src="../static/img/btn/btn_font_small.png" alt="폰트사이즈 줄임"></button>
                <button class="js-btn-big" type="button"><img src="../static/img/btn/btn_font_big.png" alt="폰트사이즈 키움"></button>
            </div>
        </footer>
        <!-- // 푸터 -->
    </div>
    
    <!-- 위젯 -->
    <div class="js-widget">
        <button class="js-widget-toggle" type="button"><img src="../static/img/btn/btn_widget.png" alt="위젯메뉴 보기/숨기기"><span class="js-cross"></span></button>
        <div class="js-widget-content">
            <ul>
                <li><a href="#">주문/배송조회</a></li>
                <li><a href="#">최근 본 상품</a></li>
                <li><a href="#">위시리스트</a></li>
                <li><a href="#">CS CENTER</a></li>
            </ul>
        </div>
    </div>
    <a class="js-btn-top" href="#header" onclick="scroll_anchor($(this).attr('href'));return false;">TOP</a>
    <!-- // 위젯 -->
    
    <!-- 툴바 -->
    <aside id="toolbar">
        <ul class="menu">
            <li><a href="#"><img src="../static/img/icon/ico_toolbar_home.png" alt=""><span>HOME</span></a></li>
            <li><a href="#"><img src="../static/img/icon/ico_toolbar_stores.png" alt=""><span>STORES</span></a></li>
            <li><a href="#"><img src="../static/img/icon/ico_toolbar_search.png" alt=""><span>SEARCH</span></a></li>
            <li><a href="#"><img src="../static/img/icon/ico_toolbar_bag.png" alt=""><span>BAG</span></a></li>
            <li><a href="#"><img src="../static/img/icon/ico_toolbar_mypage.png" alt=""><span>MY PAGE</span></a></li>
        </ul>
    </aside>
    <!-- // 툴바 -->
    
</body>

</html>
