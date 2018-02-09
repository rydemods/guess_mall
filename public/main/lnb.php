<?php
/*********************************************************************
// 파 일 명		: lnb.php
// 설     명		: 왼쪽메뉴 통합
// 상세설명	: 왼쪽메뉴 통합
// 작 성 자		: hspark
// 수 정 자		: 2015.11.02 - 김재수
//
//
*********************************************************************/
?>
<?php
/*	lnb type
	1 : Community
	2 : main lnb
	3 :
	4 : Member
	5 : CS
 */

switch($lnb_flag){
	case 1 :

	$page_code=($page_code)?$page_code:"notice";
	echo $page_code;
	$class_on[$page_code]=' class="active"';
?>
<div class="left_lnb">
	<div class="lnb_def">
	<h2><a href="../front/cscenter.php">Community</a></h2>
		<div class="lnb-inner">
			<ul>
				<li<?=$class_on["notice"]?>><a href="/board/board.php?board=notice" target="_self">공지</a></li>
				<li<?=$class_on["terrebell_event"]?>><a href="/front/terrebell_event.php" target="_self">EVENT</a></li>
				<li<?=$class_on["review"]?>><a href="/front/reviewall.php" target="_self">제품후기</a></li>
				<li<?=$class_on["report"]?>><a href="/board/board.php?board=report" target="_self">보도자료</a></li>
				<!--<li<?=$class_on["qnabbs"]?>><a href="/board/board.php?board=qnabbs" target="_self">Q&A</a></li>20141013-->
			</ul>
		</div>
	</div>
</div>

<? break;

	case 2 :

	$sql = "SELECT * FROM tblproductcode WHERE group_code != 'NO' AND type = 'L' ";
    //exdebug($sql);
	if ($_GET['code']) {
		$cate_code	=substr($_GET['code'], 0, 3);
		$sql .= "and code_a='{$cate_code}' ";
	}
	$res = pmysql_query($sql);
	while($row = pmysql_fetch_object($res)){
		if ($cate_code == $row->code_a) $cate_title	= $row->code_name;
?>
				<h2 class="tit_lnb"><?=$row->code_name?></h2>
<?
				//$sql2 = "SELECT * FROM tblproductcode WHERE group_code != 'NO' AND type != 'L' AND code_a = '{$row->code_a}' ORDER BY code_a,code_b,code_c";
                // 1차 고정이므로 order by 수정하여 cate_sort 적용.
                $sql2 = "SELECT * FROM tblproductcode WHERE group_code != 'NO' AND type != 'L' AND code_a = '{$row->code_a}' ORDER BY code_a,code_b, type asc, cate_sort asc";
				$res2 = pmysql_query($sql2);
                //exdebug($sql2);
				$c	= 0;
				while($row2 = pmysql_fetch_object($res2)){
					$code_lnb = $row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d;
					if($row2->code_c == '000'){
						if ($c != 0) {
?>
                </ul>
<?
						}
                        if (substr($code_lnb, 0, 6) == substr($page_code, 0, 6)) $cate_title	.= " > ".$row2->code_name;
?>
					<h3 class="stit_lnb"><?=$row2->code_name?></h3>
					<ul class="lnb_nav">
				<?
					} else {
					    if ($code_lnb == $page_code) $cate_title	.= " > ".$row2->code_name;
				?>
					<li><a href="../front/productlist.php?code=<?=$code_lnb?>" <?if($code_lnb==$page_code)echo "class='on' "; ?>><?=$row2->code_name?></a></li>
				<?
					}
				$c++;
				}?>
<?	} ?>
                </ul>
<?	break;

	case 3 :


	$page_code=($page_code)?$page_code:"about";
	$class_on[$page_code]=' class="active"';
?>

<? break;

case 4 :

	$page_code=($page_code)?$page_code:"login";
	$class_on[$page_code]=' class="active"';
?>
<div class="lnb_def">
	<h2><a href="../front/cscenter.php">Member</a></h2>
	<div class="lnb-inner">
		<ul>
			<li<?=$class_on["login"]?>><a href="/front/login.php" target="_self">로그인</a></li>
			<li<?=$class_on["member_join"]?>><a href="/front/member_agree.php" target="_self">회원가입</a></li>
			<li<?=$class_on["find_id_pw"]?>><a href="/front/findid.php" target="_self">아이디/비밀번호 찾기</a></li>
		</ul>
	</div>
</div>

<? break;

	case 5 :

	//$page_code=$board;
	$class_on[$board]= ' class="active"';
?>
<!-- 기존소스
<aside class="lnb_mypage">
	<a href="#"><h2>고객센터</h2></a>
	<nav>
		<ul class="menu_list">
			<li>
				<ul class="s_menu">
					<li><a <?=$class_on['notice']?> href="../front/customer_notice.php">공지사항</a></li>
				</ul>
			</li>
			<li>
				<ul class="s_menu">
					<li><a <?=$class_on['csfaq']?> href="../front/customer_faq.php">FAQ</a></li>
				</ul>
			</li>
			<li>
				<ul class="s_menu">
					<li><a href="../front/mypage_personal.php">1:1문의</a></li>
				</ul>
			</li>
			<li class="border-none">
				<ul class="s_menu">
					<li><a <?=$class_on['membership']?> href="../front/customer_grade.php">핫티 멤버쉽 안내</a></li>
				</ul>
			</li>
		</ul>
		<div class="customer-center">
			<p>고객센터 1544-9556</p>
			<ul>
				<li>월-금 10:00~17:00 </li>
				<li>점심  12:00~13:00</li>
				<li>주말.공휴일 휴무 </li>
			</ul>
		</div>
	</nav>
</aside>
 -->
 
<div class="cs-lnb">
	<h3 class="lnb-title">고객센터</h3>
	<ul>
		<li><a <?=$class_on['notice']?> href="/front/customer_notice.php">공지사항</a></li> <!-- [D] 해당 페이지에서 a태그에 active 클래스추가 -->
		<li><a <?=$class_on['csfaq']?> href="../front/customer_faq.php">FAQ</a></li>
		<li><a href="../front/mypage_personal.php">1:1문의</a></li>
		<li><a <?=$class_on['store']?>href="../front/storeList.php">매장안내</a></li>
		<li><a <?=$class_on['store_import']?>href="../front/contactUs.php">입점문의</a></li>
		<li><a <?=$class_on['membership']?> href="../front/customer_grade.php">멤버쉽안내</a></li>
		<li><a <?=$class_on['asinfo']?>href="../front/customer_cs.php">A/S안내</a></li>
	</ul>
</div>
<?	break;
} ?>



