<?php
include_once('outline/header_m.php');


if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

//소멸예정 마일리지
//$expireSql = "SELECT point, body, to_char( expire_date::date, 'YYYY-MM-DD' ) as expire_date FROM tblpoint WHERE mem_id = '".$_MShopInfo->getMemid()."' AND expire_chk = 0 ORDER BY expire_date ASC LIMIT 1";
$expireSql =	"select sum(point - use_point) as expire_point
						from tblpoint  
						where mem_id = '".$_MShopInfo->getMemid()."' 
						and expire_chk = 0  
						and expire_date = '".date('Ym').date("t", time())."' 
					";
$expireRes = pmysql_query( $expireSql, get_db_conn() );
$expireRow = pmysql_fetch_row( $expireRes );
pmysql_free_result( $expireRes );

$sql = "SELECT  pid, regdt, body, point, use_point, expire_date, tot_point 
                FROM    tblpoint 
                WHERE   mem_id = '".$_MShopInfo->getMemid()."' 
                ORDER BY pid DESC
            ";

//echo $sql;

$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
?>
<script>
<!--
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
-->
</script>

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>포인트</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_point sub_bdtop">

		<div class="mypoint">
			<div class="lv_point">
				<p class="mylv"><strong>권영은</strong>님의 회원등급 <strong class="level">BRONZE</strong></p>
				<p class="msg">등급업 필요 포인트 <strong>1,000P</strong></p>
			</div>
			<div class="point_now mt-15">
				<ul class="clear">
					<li>
						<span class="icon">P</span>
						<p class="mt-5">현재 통합 포인트</p>
						<p class="point-color"><strong>2,000P</strong></p>
					</li>
					<li>
						<span class="icon">E</span>
						<p class="mt-5">현재 E포인트</p>
						<p class="point-color"><strong>2,000P</strong></p>
					</li>
				</ul>
			</div>
			<div class="point_info">
				<p class="notice">※ 온라인 회원등급은 통합포인트 기준</p>
				<ul>
					<li>통합포인트: 오프라인 매장, 신원몰에서 모두 사용이 가능한 통합포인트</li>
					<li>E포인트: 신원몰에서만 사용이 가능한 온라인 전용 포인트</li>
				</ul>
			</div>
		</div><!-- //.mypoint -->
		
		<div class="point_tab tab_type1" data-ui="TabMenu">
			<div class="tab-menu clear">
				<a data-content="menu" class="active" title="선택됨">통합포인트</a>
				<a data-content="menu">E포인트</a>
			</div>

			<!-- 통합포인트 -->
			<div class="tab-content active" data-content="content">
				<div class="check_period">
					<ul>
						<li class="on"><a href="javascript:;">1개월</a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
						<li><a href="javascript:;">3개월</a></li>
						<li><a href="javascript:;">6개월</a></li>
						<li><a href="javascript:;">12개월</a></li>
					</ul>
				</div><!-- //.check_period -->

				<div class="list_point"><!-- [D] 5개 페이징 -->
					<ul>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
					</ul>
				</div><!-- //.list_point -->
				
				<div class="list-paginate mt-15">
					<a href="#" class="prev-all disabled">처음</a><!-- [D] 버튼 비활성인 경우 .disabled 클래스 추가 -->
					<a href="#" class="prev disabled">이전</a>
					<a href="#" class="on">1</a>
					<a href="#">2</a>
					<a href="#">3</a>
					<a href="#">4</a>
					<a href="#">5</a>
					<a href="#">6</a>
					<a href="#" class="next">다음</a>
					<a href="#" class="next-all">끝</a>
				</div><!-- //.list-paginate -->
			</div>
			<!-- //통합포인트 -->

			<!-- E포인트 -->
			<div class="tab-content" data-content="content"><!-- [D] 통합포인트와 구성 동일 -->
				<div class="check_period">
					<ul>
						<li class="on"><a href="javascript:;">1개월</a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
						<li><a href="javascript:;">3개월</a></li>
						<li><a href="javascript:;">6개월</a></li>
						<li><a href="javascript:;">12개월</a></li>
					</ul>
				</div><!-- //.check_period -->

				<div class="list_point"><!-- [D] 5개 페이징 -->
					<ul>
						<li>
							<p class="point_name">구매 적립 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
						<li>
							<p class="point_name">로그인 포인트 <span class="date">2017.01.14</span></p>
							<p class="">적립 포인트 <span class="blk">10P</span> <span class="bar">|</span> 사용 포인트 <span class="blk">10P</span></p>
						</li>
					</ul>
				</div><!-- //.list_point -->
				
				<div class="list-paginate mt-15">
					<a href="#" class="prev-all disabled">처음</a><!-- [D] 버튼 비활성인 경우 .disabled 클래스 추가 -->
					<a href="#" class="prev disabled">이전</a>
					<a href="#" class="on">1</a>
					<a href="#">2</a>
					<a href="#">3</a>
					<a href="#">4</a>
					<a href="#">5</a>
					<a href="#">6</a>
					<a href="#" class="next">다음</a>
					<a href="#" class="next-all">끝</a>
				</div><!-- //.list-paginate -->
			</div>
			<!-- //E포인트 -->
		</div><!-- //.point_tab -->

	</section><!-- //.mypage_point -->

</main>
<!-- //내용 -->

	<div class="sub-title">
		<h2>마일리지</h2>
		<a class="btn-prev" href="mypage.php"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>

	<div class="mileage-list-wrap">
		<div class="my-mileage-info">
			<div class="user">
				<strong class="name"><?=$_mdata->name?><span>(<?=$_mdata->id?>)</span> 님</strong>
				<a href="/m/mypage_benefit.php" class="btn-benefit">등급별 혜택</a>
				<div class="date">마일리지 산정기간<br><?=date('Y.m.d')?> ~ <?=date( 'Y.m.d', mktime( 0, 0, 0, date('m'), date('d'), date('Y') + 1 ) )?></div>
			</div>
			<div class="mileage-info">
				<div>사용가능한 마일리지<strong><?=number_format($_mdata->reserve)?> <span>M</span></strong></div>
				<div>소멸예정 마일리지<br>(소멸예정일 : <?=date('Y.m').".".date("t", time())?>)<strong><?=number_format( $expireRow[0] )?> <span>M</span></strong></div>
			</div>
		</div>
		<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<ul class="mileage-list">
<?
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$regdt = substr($row->regdt,0,4).".".substr($row->regdt,4,2).".".substr($row->regdt,6,2);
            $expire_date = substr($row->expire_date,0,4).".".substr($row->expire_date,4,2).".".substr($row->expire_date,6,2);
?>
			<li>
				<p class="date"><?=$regdt?></p>
				<p class="type"><?=$row->body?></p>
				<p class="mileage"><?=number_format( $row->point )?></p>
			</li>
<?
		
			$i++;
		}
		pmysql_free_result($result);
?>			
		</ul>
		</form>

		<div class="paginate">
			<div class="box">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
			</div>
		</div>
		
		<dl class="attention margin"><!-- 기본 안내사항 -->
			<dt>유의사항</dt>
			<dd>쇼핑몰에서 발행한 종이쿠폰/시리얼쿠폰/모바일쿠폰 등의 인증번호를 등록하시면 온라인쿠폰으로 발급되어 사용이 가능합니다.</dd>
			<dd>쿠폰은 주문 시 1회에 한해 적용되며, 1회 사용시 재 사용이 불가능합니다.</dd>
			<dd>쿠폰은 적용 가능한 상품이 따로 적용되어 있는 경우 상품 구매 시에만 사용이 가능합니다.</dd>
			<dd>특정한 종이쿠폰/시리얼쿠폰/모바일쿠폰의 경우 단 1회만 사용이 가능할 수 있습니다.</dd>
		</dl>

	</div><!-- //.mileage-list -->

<? include_once('outline/footer_m.php'); ?>
