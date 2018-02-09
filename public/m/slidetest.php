<?php
header("Content-Type:text/html;charset=EUC-KR");
include_once('outline/header_m.php')
?>


<link type="text/css" rel="stylesheet" href="<?=$Dir?>m/css/lightslider.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="<?=$Dir?>m/js/lightslider.js"></script>



<script type="text/javascript">
  $(document).ready(function() {
    $("#lightSlider").lightSlider();
  });
</script>


<main class="subpage">

<ul id="lightSlider">
  <li>
    <img src="http://nasign.ajashop.co.kr/data/shopimages/product/001001001000000003.jpg">
  </li>
  <li>
    <img src="http://nasign.ajashop.co.kr/data/shopimages/product/001001001000000003.jpg">
  </li>
   <li>
    <img src="http://nasign.ajashop.co.kr/data/shopimages/product/001001001000000003.jpg">
  </li>
   <li>
    <img src="http://nasign.ajashop.co.kr/data/shopimages/product/001001001000000003.jpg">
  </li>
   <li>
    <img src="http://nasign.ajashop.co.kr/data/shopimages/product/001001001000000003.jpg">
  </li>
</ul>
</main>
