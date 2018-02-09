<?php
include_once('outline/header_m.php');
$page_cate = 'OPEN GUIDE';

$bridx = $_GET['bridx'];

$temp_sql = "SELECT * FROM tblproductbrand WHERE bridx = ".$bridx;
$temp_result = pmysql_query($temp_sql,get_db_conn());

/*BESTIBELLI*/
$openguide["301"]["position"] = "전국 주요 상권 및 인구 5만 이상 도시";
$openguide["301"]["deal"] = "100% 위탁 판매";
$openguide["301"]["area"] = "전면 4m 이상 15평 ~ 25평";
$openguide["301"]["margin"] = "협의 가능";
$openguide["301"]["deposit"] = "현금 2천만원 / 부동산 담보 5천만원 (월2회 결제) or <br/>현금 2천만원 / 부동산 담보 1억원(월 1회 결제)";
$openguide["301"]["payment"] = "1회결제 or 월 2회 결제";
$openguide["301"]["interior"] = "3.3㎡ 당 2백만원";
$openguide["301"]["person"] = '<span class="o_txt o_txt_t_m"><span>조용득 과장</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(H.P 010-9194-3062 / TEL. 02-3274-6016 / E-mail. ydcho@sw.co.kr)</span>';


/*VIKI*/
$openguide["302"]["position"] = "전국 주요상권";
$openguide["302"]["deal"] = "100% 위탁 판매";
$openguide["302"]["area"] = "전면 4M 이상 / 15평~25평";
$openguide["302"]["margin"] = "유선 협의";
$openguide["302"]["deposit"] = "현금보증금 2천만원 / 부동산 담보 8천만원";
$openguide["302"]["payment"] = "월 1회 현금 결제 (월 마감후 익월 20일 결제)";
$openguide["302"]["interior"] = "평당 2백만원";
$openguide["302"]["person"] = '<span class="o_txt o_txt_t_m"><span>이상호 과장</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(H.P 010-8519-0010 / TEL. 02-3274-6045 / E-mail. bible930@sw.co.kr)</span>';


/*SI*/
$openguide["303"]["position"] = "전국 주요 상권 및 인구 7만 이상 도시";
$openguide["303"]["deal"] = "100% 위탁 판매";
$openguide["303"]["area"] = "전면 4M 이상 / 15평~25평";
$openguide["303"]["margin"] = "유선 협의";
$openguide["303"]["deposit"] = "현금보증금 2천만원 / 부동산 담보 1억원";
$openguide["303"]["payment"] = "월 1회 현금 결제 (월 마감후 익월 20일 결제)";
$openguide["303"]["interior"] = "3.3㎡ 당 약 2백만원";
$openguide["303"]["person"] = '<span class="o_txt o_txt_t_m"><span>운준필 과장</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(TEL. 02-3274-5869 / E-mail. junfeel33@sw.co.kr)</span>';


/*ISABEY*/
$openguide["304"]["position"] = "전국 주요 상권 및 인구 5만 이상 도시";
$openguide["304"]["deal"] = "100% 위탁 판매";
$openguide["304"]["area"] = "전면 5m 이상 20평 이상";
$openguide["304"]["margin"] = "협의 가능";
$openguide["304"]["deposit"] = "현금보증금 2천만원 / 부동산 담보 8천만원 (월2회 결제)";
$openguide["304"]["payment"] = "당월 25일 / 익월 10일(2회)";
$openguide["304"]["interior"] = "3.3㎡ 당 2백만원";
$openguide["304"]["person"] = '<span class="o_txt o_txt_t_m"><span>이민규 차장</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(H.P 010-2636-4112 / TEL. 02-3274-5372 / E-mail. mklee1@sw.co.kr)</span><br/>
							<span class="o_txt"><span>정현철 과장</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(H.P 010-9450-6991 / TEL. 02-3274-6854 / E-mail. bongku1@sw.co.kr)</span><br/>
							<span class="o_txt"><span> 김희승 대리</span><a href="./brand_qna.php?bridx='.$bridx.'" class="goQna">문의하기</a><br>
							<span class="o_txt">(H.P 010-7301-0311 / TEL. 02-3274-6011 / E-mail. hskim6@sw.co.kr)</span>';

?>
<link rel="stylesheet" type="text/css" href="./fromsw/css/openguide.css?ver=3.6">
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
	<?php 
		while($temp_row = pmysql_fetch_object($temp_result)) {
			echo "<span>".$temp_row->brandname."</span>";
		}
	?>
		</h2>
		<div class="breadcrumb">
			<?php include_once('brand_menu.php'); ?>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_wrap">
		<div class="open_icon">
			<ul>
				<li>
					<div class="o_icon o_icon1">
						<div class="o_text">
							<span class="o_tit">입지조건</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["position"]?></span>
						</div>
					</div>
					
				</li>
				<li>
					<div class="o_icon o_icon2">
						<div class="o_text">
							<span class="o_tit">거래형태</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["deal"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon3">
						<div class="o_text">
							<span class="o_tit">면적</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["area"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon4">
						<div class="o_text">
							<span class="o_tit">마진</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["margin"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon5">
						<div class="o_text">
							<span class="o_tit">보증금</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["deposit"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon6">
						<div class="o_text">
							<span class="o_tit">결제조건</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["payment"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon7">
						<div class="o_text">
							<span class="o_tit">인테리어</span> <br> 
							<span class="o_txt"><?=$openguide[$bridx]["interior"]?></span>
						</div>
					</div>						
				</li>
				<li>
					<div class="o_icon o_icon8">
						<div class="o_text">
							<span class="o_tit">담당자 연락처</span> <br> 
							<?=$openguide[$bridx]["person"]?>
						</div>
					</div>						
				</li>
			</ul>
		</div>
	</section>

</main>
<!-- //내용 -->

<?php
include_once('outline/footer_m.php');
?>