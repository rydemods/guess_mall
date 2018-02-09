
<?php if ( $isMobile ) { ?>

    <?php include ($Dir.TempletDir."studio/mobile/navi_TEM001.php"); ?>
    
    <!-- SNS 리스트 -->
    <div class="studio-sns-list">
        <ul>
        </ul>
        <button class="btn-list-more" type="button"><img src="./static/img/btn/btn_list_more.png" alt="더 보기"></button>
    </div>
    <!-- // SNS 리스트 -->

<?php } else { ?>

<div id="contents">

        <div class="containerBody sub-page">

            <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>

            <div class="sns_container studio-sns mainPage">
                <div class="insta_wrap">
                    <h3>INSTAGRM C.A.S.H STORE<br>
                    <span>instagrm id : cashstores</span></h3>
                    <ul>
                    </ul>
                </div><!-- //.insta_wrap -->
               <div class="btn-more-wrap"><button class="btn-more">더 보기</button></div>
            </div><!-- //.sns_container -->

        </div><!-- //공통 container -->
    
</div><!-- //#contents -->

<?php } ?>

<script type="text/javascript">

    $(document).ready(function() {
        // ajax로 인스타그램 리스트를 구한다.
            getSnsList(1);
    });

    function getSnsList(page) {
        $.ajax({
            type: "get",
            url: "/front/ajax_get_sns_list.php",
            data: 'gotopage=' + page + '&im=<?=$isMobile?>'
        }).success(function ( result ) {
            var arrTmp = result.split("||");

            <?php if ($isMobile) { ?>

                if ( arrTmp[0] == "END" ) {
                    // 마지막 페이지인 경우 더보기 숨김
                    $(".studio-sns-list .btn-list-more").hide();
                } else {
                    // 더보기 링크를 다음페이지로 셋팅
                    $(".studio-sns-list .btn-list-more").unbind("click").bind("click", function() {
                        getSnsList(page + 1);
                    });
                }

                /*
                바뀐 소스(FADE IN 효과 )
                AJAX로 호출 하는 소스 수정 내용 ( ex. deco@182.162.154.102:/public/front/ajax_get_brand_list.php )
                    1. li에 showLayerFadein클래스 추가. 
                    2. li 마지막에 구분자 ▒▒ 추가.
                */
                if ( arrTmp[1] != "" ) {
                    // 추가 내용이 있으면 기존꺼에 추가
                    if(page == 1){
                        var appendData = arrTmp[1].replace(/\▒▒/g, '');
                        $(".studio-sns-list ul").append( appendData );
                    }else{
                        $(".studio-sns-list ul li").removeClass('showLayerFadein');

                        var appendData = arrTmp[1].split("▒▒");
                        var modCount = 1;
                        var modHtml = "";
                        for(var i = 0; i <= appendData.length; i++){
                            if(appendData[i]){
                                $(".studio-sns-list ul").append( appendData[i] );
                                $(".studio-sns-list ul li:last").hide();
                            }
                        }

                        $(".showLayerFadein").each(function(i, element) {
                            $(this).delay( 50 * i ).fadeIn(800).removeClass('showLayerFadein');
                        })
                    }
                }

            <?php } else { ?>

                if ( arrTmp[0] == "END" ) {
                    // 마지막 페이지인 경우 더보기 숨김
                    $(".sns_container .btn-more-wrap").hide();
                } else {
                    // 더보기 링크를 다음페이지로 셋팅
                    $(".sns_container .btn-more").unbind("click").bind("click", function() {
                        getSnsList(page + 1);
                    });
                }

                /*
                바뀐 소스(FADE IN 효과 )
                AJAX로 호출 하는 소스 수정 내용 ( ex. deco@182.162.154.102:/public/front/ajax_get_brand_list.php )
                    1. li에 showLayerFadein클래스 추가. 
                    2. li 마지막에 구분자 ▒▒ 추가.
                */
                if ( arrTmp[1] != "" ) {
                    // 추가 내용이 있으면 기존꺼에 추가
                    if(page == 1){
                        var appendData = arrTmp[1].replace(/\▒▒/g, '');
                        $(".sns_container ul").append( appendData );
                    }else{
                        $(".sns_container ul li").removeClass('showLayerFadein');

                        var appendData = arrTmp[1].split("▒▒");
                        var modCount = 1;
                        var modHtml = "";
                        for(var i = 0; i <= appendData.length; i++){
                            if(appendData[i]){
                                $(".sns_container ul").append( appendData[i] );
                                $(".sns_container ul li:last").hide();
                            }
                        }

                        $(".showLayerFadein").each(function(i, element) {
                            $(this).delay( 50 * i ).fadeIn(800).removeClass('showLayerFadein');
                        })
                    }
                }

            <?php } ?>
        });
    }


</script>
