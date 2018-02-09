<section class="mypage_list">
<h3>CATEGORY</h3>
	<ul class="cate_list">
	   <li><a href="mypage_receipt.php">영수증/계산서 신청</a></li>
	   <li><a href="wishlist.php">위시리스트</a></li>
		<li><a href="mypage_coupon.php">쿠폰</a></li>
		<li><a href="mypage_reserve.php">적립금</a></li>
<?php if($_data->personal_ok=="Y")	:	?>
		<li><a href="mypage_personal.php">1:1 문의</a></li>
<?php endif;	?>
<?php if( $_data->review_memtype=="Y" )	:	?>
		<li><a href="mypage_review.php">상품평</a></li>
<?php endif;	?>
	   <li><a href="mypage_qna.php">질문과 답변</a></li>
	   <li><a href="mypage_memberout.php">회원탈퇴 신청</a></li>
	</ul>
</section>