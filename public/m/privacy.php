<?php
include_once('./outline/header_m.php');

// 개인정보취급방침
$privercy = "";
if(file_exists($Dir.AdminDir."privercy.txt")) {
    $privercy = file_get_contents($Dir.AdminDir."privercy.txt");
    
    $pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]");
    $replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
    $privercy = str_replace($pattern,$replace,$privercy);
}

?>
			
			<!-- <div class="sub-title">
				<h2>개인정보 취급방침</h2>
				<a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div> -->
			<section class="top_title_wrap">
				<h2 class="page_local">
					<a href="javascript:history.back();" class="prev"></a>
					<span>개인정보취급방침</span>
					<a href="/m/shop.php" class="home"></a>
				</h2>
			</section>
			
			<div class="agree-content-box">
				<div class="inner">
                    <?=$privercy?>
				</div>
			</div>

<?php
include_once('./outline/footer_m.php');
?>
