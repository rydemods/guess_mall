<?php 
$view_type = $_GET['view_type'];
?>

			<!--
				(D) 메인페이지에서만 body와 header에 class가 추가되는 부분이 있습니다.
				작업 시 (D)로 검색하여 페이지 내 주석 참고해주시기 바랍니다.
			-->
			<div class="js-main-list">
				<div class="main-list-inner">
					<!-- (D) 연결할 페이지를 data-url에 넣어줍니다. -->
                    <?php   if ($basename == "index.php") { ?>
                                <div class="js-main-list-content" data-url="./mainShop.php"><?php include_once("./mainShop_index.php"); ?></div>
                                <div class="js-main-list-content" data-url="./mainPromotion.html"></div>
                                <div class="js-main-list-content" data-url="./mainStudio.html"></div>
                    <?php   } else { 
                                if ( $basename == "promotion.php") {             
                                    echo '<div class="js-main-list-content" data-url="./mainPromotion.html?view_type=' . $view_type . '"></div>';
                                } elseif ( $basename == "studio_dev.php" ) { 
                                    echo '<div class="js-main-list-content" data-url="./mainStudio_dev.html"></div>';
                                }
                                echo '<div class="js-main-list-content" data-url="./mainBlank.html"></div>';
                                echo '<div class="js-main-list-content" data-url="./mainBlank.html"></div>';
                            }
                    ?>
				</div>
			</div>

<script type="text/javascript">
    $(document).ready(function() {
        ui_init();
    });

    function showMainLayer(idx) {
        // 현재 탭이 'STUDIO'가 아닌 경우, 상단 레이어 닫기
        if ( idx != 2 ) {
            $(".js-btn-toggle").removeClass("on");
            $(".js-menu-content").removeClass("on");
        }

        var $content = $(".js-main-list-content");

        var obj = $content[idx];
        if (!$(obj).attr("data-url") || $(obj).attr("data-url") === "./mainShop.php") return;

        var url = $(obj).data("url");
        var index = idx;

        $(obj).removeAttr("data-url").load(url + " #content > *", complete_handler);
        function complete_handler() {
            ui_init(); // 컨텐츠 스크립트를 메인에서는 로드 이후 적용
        }
    }
</script>

