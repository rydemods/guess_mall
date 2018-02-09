<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "asinfo";
$class_on['asinfo'] = " class='active'";
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">AS 안내</h2>

		<div class="inner-align page-frm clear">

			<?php 
				$lnb_flag = 5;
				include ($Dir.MainDir."lnb.php");
			?>
			<article class="cs-content">
				<h2 class="v-hidden">AS 안내</h2>
				<div class="as-flow clear">
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as01.png" alt="AS접수"><span>AS 접수</span></div>
						<div class="comment" style="font-size: 11px;">
							<dl>
								<!-- <dt>AS 접수</dt> -->
								<dd>- 온라인 AS 게시판 접수</dd>
								<dd>- 온라인 콜센터 접수</dd>
							</dl>
							<dl>
								<!-- <dt>신원몰 구매 고객</dt> -->
								<!-- <dd>- 온라인 AS 게시판 접수</dd>
								<dd>- 온라인 콜센터 접수</dd> -->
							</dl>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as02.png" alt="고객만족실"><span>고객상담실(1661-2585)</span></div>
						<div class="comment" style="font-size: 11px;">
							<p>- 상품 인수 수선 또는 외부 심의 의뢰<br>
							- 고객에게 유선 통화</p>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as03.png" alt="수선진행"><span>수선진행</span></div>
						<div class="comment" style="font-size: 11px;">
						<!--  
							<p>- 자체 수선실 및 협력업체에서<br><span style="padding-left:7px"></span>수선 지행</p>
						-->
							<p >고객과실<br>
							- 수선가능 : 수선 (고객 수선비 부담)<br>
							- 수선불가 : 회송</p>
							<p>제품하자<br>
							- 수선가능 : 수선 (신원몰 수선비 부담)<br>
							- 수선불가 : 교환 / 환불 진행</p>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as04.png" alt="수선완료 후 발송"><span>수선완료 후 발송</span></div>
						<div class="comment" style="font-size: 11px;">
							<p>- 수선 완료후 발송 혹은 환불</p>
						</div>
					</div><!-- //.inner -->
				</div>

				<ul class="as-attention mt-20">
				<!--  
					<li>1. 신원몰 마이페이지 AS 접수 메뉴 또는 <strong>CS(1661-2585)</strong>를 통해 직접 상담/접수해 주시기 바랍니다.
						<p style="padding:12px 0 0 12px"><strong>주소 : 서울 중랑구 신내로1길 20 신원몰CS CJ대한통운택배중랑대리점 </strong></p>
					</li>
					<li>
						2. AS 상품과 함께 간단한 수선의뢰 내용 및 고객의 연락처를 메모하여 함께 보내주시면 고객만족실에서 상품인수 후 담당자가 고객께 연락을 드리고
						<br><span style="padding-left:12px"></span>있습니다.
					</li>
					<li>
						3. 고객만족실 자체 수선실이나 협력업체를 통하여 정성껏 수선하여 고객이 원하시는 매장이나 댁으로 발송(우편/택배)하여 드리고 있습니다. 
					</li>
					<li>
						4. 상품상의 하자로 인한 수선건은 무상으로 처리해 드리고 있으나, 고객 취급과실로 인한 원단 파손이나 상품의 과다 착용으로 인한 자재 교체 수선건은
						<br><span style="padding-left:12px"></span>유료 수선을 시행됩니다.
					</li>
					<li>
						5. 수선의뢰 상품 중 의류상품(소재 및 디자인의 특성) 한계로 인하여 수선 후 외관상 어색하거나 착용 시 불편할 수 있는 수선 사항에 대해서는 수선이
						<br><span style="padding-left:12px"></span>불가하여 작업하여 드리지 못할 수도 있으므로 이 점 양해하여 주시기 바랍니다.
					</li>
				-->
					<li><strong>신원몰 A/S 업무</strong></li>
					<li> - 상품상의 하자로 인한 수선에 대해서는 무상으로 처리해 드리고 있으나, 고객 취급상의 과실로 인한 파손 또는 과다 착용으로 인한  수선은 수선가능과 <br><span style="padding-left:12px"></span>불가능 및 유상수선과 무상수선으로 나뉘어 진행하고 있습니다.</li>
					<li><strong>신원몰 A/S 의뢰방법 - 택배접수</strong></li>
					<li> - A/S 제품과 수선의뢰 내용 및 고객 성명,연락처를 메모하여 함께 동봉하여 보내주시면 제품 수령후 담당자가 고객님께 연락을 드립니다.</li>
					<li>* 주소지 : 서울시 중랑구 신내동 495-42 CJ택배 서울 상봉대리점 ㈜신원</li>
					<li><strong>신원몰 A/S 수선기준</strong></li>
					<li> - 구입일로 부터 1년이내 수선가능 (수선가능과 불가능 및 유상/무상 수선으로 나뉘어 집니다.)</li>
					<li>   *수선불가 항목</li>
					<li>    소매기장 / 총장 / 밑단 수선등<br>
					    디자인 변경불가 (리폼불가)</li>
				</ul>
				<p class="mt-15 fz-13">※ 신원몰 회원은 온라인 AS 접수가 가능합니다.</p>
				<div class="ta-c mt-20"><a href="../front/mypage_as.php" class="btn-point h-large w250">온라인 AS 접수하기</a></div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->


<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
