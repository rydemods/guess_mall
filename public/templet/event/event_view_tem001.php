			<!-- 메인 컨텐츠 -->
			<div class="main_wrap">
				메인랩?
				<div class="customer_wrap">
					
					<!-- 고객센터 LNB -->
					<?php include ("{$Dir}board/top_lnb.php");?>
					<!-- // 고객센터 LNB -->

					<div class="content_area">
						<div class="line_map">홈 &gt; 고객센터 &gt; <strong>EVENT</strong></div>

						<div class="customer_notice_wrap">
							<!--<h3 class="notice_title">NEWS &#38; NOTICE<span>세이브힐즈의 새로운 소식을 알려드립니다</span></h3>-->

							<h4 class="view_title">
								<strong class="title"><?=$data['title']?></strong>
								<span class="right_box">
									<span class="name"><?=$data['name']?></span>
									<span class="date"><?=$reg_date?></span>
								</span>
							</h4>

							<div class="view_content">
								<?=stripslashes($data['content'])?>
							</div>

							<ul class="view_direction">
							<?php
								if($data_prev){
							?>
								<li>
									<span class="direction"><img class="prev" src="<?=$Dir?>img/icon/customer_notice_view_arrow_up.gif" alt="" />이전글</span>
									<a href="javascript:goView('<?=$data_prev['num']?>')" target="_self"><?=$data_prev['title']?></a>
									<span class="date"><?=$reg_date_prev?></span>
								</li>
							<?php
								}
								if($data_next){
							?>
								<li>
									<span class="direction"><img class="next" src="<?=$Dir?>img/icon/customer_notice_view_arrow_down.gif" alt="" />다음글</span>
									<a href="javascript:goView('<?=$data_next['num']?>')" target="_self"><?=$data_next['title']?></a>
									<span class="date"><?=$reg_date_next?></span>
								</li>
							<?php
								}
							?>
							</ul>

							<div class="button_area">
								<a href="<?=$Dir?>front/event_list.php" target="_self"><img src="<?=$Dir?>img/button/customer_notice_view_list_btn.gif" alt="목록으로 가기" /></a>
							</div>
						</div>
					</div>

				</div>

			</div>
			<!-- //메인 컨텐츠 -->