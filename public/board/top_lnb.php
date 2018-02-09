					<?php 
						$page_code=($page_code)?$page_code:"notice";
						$class_on[$page_code]=' class="on"';
					?>
					<div class="left_lnb">
						<div class="lnb_area">
						<h2><a href="../front/cscenter.php">Community</a></h2>
						<!-- (D) 선택된 li 에 class="on" 을 추가합니다. -->
						<ul>
							<li<?=$class_on["notice"]?>><a href="/board/board.php?board=notice" target="_self">공지/이벤트</a></li>
							<li<?=$class_on["event"]?>><a href="/board/board.php?board=event" target="_self">EVENT</a></li>
							<li<?=$class_on["review"]?>><a href="/front/reviewall.php" target="_self">제품후기</a></li>
							<li<?=$class_on["report"]?>><a href="/board/board.php?board=report" target="_self">보도자료</a></li>
							<li<?=$class_on["faq"]?>><a href="/front/csfaq.php" target="_self">FAQ</a></li>
							<li<?=$class_on["default"]?>><a href="/front/default.php" target="_self">default</a></li>

							<!-- <li<?=$class_on["1n1"]?>><a href="/front/mypage_personal.php" target="_self">1:1 문의</a></li>
							<li<?=$class_on["qnabbs"]?>><a href="/board/board.php?board=qnabbs" target="_self">Q&A</a></li>
							<li<?=$class_on["contact"]?>><a href="/front/cscenter_contact.php" target="_self">CONTACT US</a></li> -->
						</ul>
						</div>
					</div>