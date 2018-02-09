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
                    <?php   if ($basename == "index.php" || $basename == "index.htm") { ?>
                                <div class="js-main-list-content" data-url="./mainBlank.html"><?php include_once("./mainShop_index.php"); ?></div>
                                <div class="js-main-list-content" data-url="./mainPromotion.html"></div>
                                <div class="js-main-list-content" data-url="./mainStudio.html"></div>
                    <?php   } else { 
                                if ( $basename == "promotion.php") {             
                                    echo '<div class="js-main-list-content" data-url="./mainPromotion.html?view_type=' . $view_type . '"></div>';
                                } elseif ( $basename == "studio.php" ) { 
                                    echo '<div class="js-main-list-content" data-url="./mainStudio.html"></div>';
                                }
                                echo '<div class="js-main-list-content" data-url="./mainBlank.html"></div>';
                                echo '<div class="js-main-list-content" data-url="./mainBlank.html"></div>';
                            }
                    ?>
				</div>
			</div>

