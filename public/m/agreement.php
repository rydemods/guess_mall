<?php
include_once('./outline/header_m.php');

// 이용약관
$agreement = "";
if(file_exists($Dir.AdminDir."agreement.txt")) {
    $agreement = file_get_contents($Dir.AdminDir."agreement.txt");
    
    $pattern=array("[SHOP]","[COMPANY]");
    $replace=array($_data->shopname, $_data->companyname);
    $agreement = str_replace($pattern,$replace,$agreement);
    $agreement = preg_replace('/[\\\\\\\]/',"",$agreement);
}

?>
			
			<!-- <div class="sub-title">
				<h2>이용약관</h2>
				<a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div> -->
			<section class="top_title_wrap">
				<h2 class="page_local">
					<a href="javascript:history.back();" class="prev"></a>
					<span>이용약관</span>
					<a href="/m/shop.php" class="home"></a>
				</h2>
			</section>
			
			<div class="agree-content-box">
                <div class="inner">
                <?=$agreement?>
                </div>
			</div>

<?php
include_once('./outline/footer_m.php');
?>
