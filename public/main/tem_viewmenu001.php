<?php

if(strlen($Dir)==0) $Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/recipe.class.php");

$recipe=new RECIPE();
?>
<div id="cate_map">
		<div class="mapline navi1">
         <h5> SHOPPING MENU</h5>
		 <ul>
<?


$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and (code_a='003' or code_a='004' or code_a='006' or code_a='002' or code_a='034' or code_a='009' or code_a='010' or code_a='007' or code_a='001') and code_b='000'ORDER BY cate_sort ";
$result = pmysql_query($sql);
while($cate_data=pmysql_fetch_object($result)){
	

?>
			
		<li>
			<dl>
				<dt><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dt>
				<?
				
				$sql_2 = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
				$sql_2.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a='".$cate_data->code_a."' and code_b!='000' and code_c='000'ORDER BY cate_sort ";
				$result_2 = pmysql_query($sql_2);
				while($cate_data_2=pmysql_fetch_object($result_2)){
				?>
				<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data_2->code_a.$cate_data_2->code_b.$cate_data_2->code_c.$cate_data_2->code_d?>"><?=$cate_data_2->code_name?></a></dd>
				<?}?>
				
			</dl>
		</li>
<?}?>

<!--
				<li>				
        <dl>
			<dt><a href="#">아로마오일,워터</a></dt>
			<dd><a href="#">아로마오일</a></dd>
			<dd><a href="#">하이드로졸</a></dd>
			<dd><a href="#">베이스워터</a></dd>
			<dd><a href="#">유기농</a></dd>
			<dd><a href="#">깔리떼아로마</a></dd>
			<dd><a href="#">쏠아로마</a></dd>
		</dl>								
				</li>
				<li>				
		<dl>
			<dt><a href="#">화장품원료</a></dt>
			<dd><a href="#">기능성원료</a></dd>
			<dd><a href="#">유화,점증</a></dd>
			<dd><a href="#">추출물</a></dd>
			<dd><a href="#">썬케어,컬러</a></dd>
			<dd><a href="#">향료</a></dd>
			<dd><a href="#">화장품원재료</a></dd>
			<dd><a href="#">유기농</a></dd>
		</dl>					
				</li>
				<li>
		<dl>
			<dt><a href="#">비누원료</a></dt>
			<dd><a href="#">비누베이스</a></dd>
			<dd><a href="#">물비누베이스</a></dd>
			<dd><a href="#">원재료</a></dd>
			<dd><a href="#">치약,바스붐</a></dd>
			<dd><a href="#">비누용색소</a></dd>
		</dl>					
				</li>
				<li>			
		<dl>
			<dt><a href="#">분말,허브</a></dt>
			<dd><a href="#">천연분말</a></dd>
			<dd><a href="#">허브</a></dd>
			<dd><a href="#">유기농</a></dd>
		</dl>					
				</li>
				<li>				
		<dl>
			<dt><a href="#">용기</a></dt>
			<dd><a href="#">펌프</a></dd>
			<dd><a href="#">스프레이</a></dd>
			<dd><a href="#">디스펜서</a></dd>
			<dd><a href="#">크림</a></dd>
			<dd><a href="#">립</a></dd>
			<dd><a href="#">기타</a></dd>
		</dl>	
				</li>
				<li>
		<dl>
			<dt><a href="#">예쁜포장</a></dt>
			<dd><a href="#">스티커</a></dd>
			<dd><a href="#">상자</a></dd>
			<dd><a href="#">쇼핑백</a></dd>
			<dd><a href="#">비닐 / 종이</a></dd>
			<dd><a href="#">리본</a></dd>
			<dd><a href="#">쵸핑</a></dd>
			<dd><a href="#">택,타이</a></dd>
		</dl>	
				</li>
				<li>
		<dl>
			<dt><a href="#">만들기도구</a></dt>
			<dd><a href="#">만들기도구</a></dd>
			<dd><a href="#">아로마용품</a></dd>
			<dd><a href="#">스탬프</a></dd>
			<dd><a href="#">향초</a></dd>
			<dd><a href="#">도구세트</a></dd>
			<dd><a href="#">몰드</a></dd>
		</dl>	
				</li>
				<li>
		<dl>
			<dt><a href="#">키트세트</a></dt>
			<dd><a href="#">만들기키트</a></dd>
			<dd><a href="#">세트상품</a></dd>
		</dl>	
				</li>
				-->
			</ul>
		</div><!-- //end navi1 -->

<div class="space"></div>

		<div class="mapline navi2">
         <h5>RECIPE MENU</h5>

			<ul>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001001000000">화장품</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001001000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001002000000">비누</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001002000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001003000000">생활용품</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001003000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001004000000">헤어케어</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001004000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001005000000">베이비</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001005000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=001006000000">기타</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("001006000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=002000000000">기능별</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("002000000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
				<li>
					<dl>
						<dt><a href="/front/recipe.php?code=003000000000">피부타입별</a></dt>
						<?$cate3 = $recipe->getRecipeCategoryList("003000000000");?>
						<?foreach($cate3 as $v3){?>
						<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
						<?}?>
					</dl>
		        </li>
			</ul>
		</div><!-- //end navi2 -->

<div class="space"></div>
		<div class="mapline navi3">
         <h5>COMMUNITY MENU</h5>

			<ul>
				<li>
		<dl>
			<dt>아나운서</dt>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=notice">공지사항</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=event">쏘쿨이벤트</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=commongsoon">품절입고예정</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=banking">입금자찾기</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt>문의게시판</dt>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=qana">고객문의</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=studyb">레시피문의</a></dd>
			<dd><a href="<?=$Dir.FrontDir?>cscenter.php">자주하는 질문</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt>커뮤니티</dt>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=free">일상다반사</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=myphoto">내작품전시</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=ImMD">나는 MD다</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=open">오픈지식</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=wish">불만제로</a></dd>
			<dd><a href="http://blog.naver.com/suejwang">블로그</a></dd>
			<dd><a href="http://blog.naver.com/suejwang">트위터</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt>연구실</dt>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=soapschool">솝스쿨 실험실</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=choice">소울팩토리</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=story">어느별에서 왔니</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=tip">DIY아카데미</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt>후기</dt>
			<dd><a href="<?=$Dir.FrontDir?>reviewall.php">상품후기</a></dd>
			<dd><a href="<?=$Dir.FrontDir?>recipe_review.php">레시피후기</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=review">나눔샘플링</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=recipesix">강좌후기</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="<?=$Dir.FrontDir?>mypage.php">마이페이지</a></dt>
			<dd><a href="<?=$Dir.FrontDir?>mypage_orderlist.php">주문,배송정보</a></dd>
			<dd><a href="<?=$Dir.FrontDir?>mypage_myWrite.php">내가쓴글</a></dd>
			<dd><a href="<?=$Dir.FrontDir?>mypage_myReply.php">내가쓴 댓글</a></dd>
			<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=018">개인결제창</a></dd>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="<?=$Dir.FrontDir?>cscenter.php">고객센터</a></dt>
			<dd><a href="<?=$Dir.BoardDir?>board.php?pagetype=view&num=93279&board=notice&block=&gotopage=1&search=&s_check=">세금계산서&현금영수증</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?pagetype=view&num=93315&board=notice&block=&gotopage=1&search=&s_check=">학교 및 비영리단체</a></dd>
			<dd><a href="<?=$Dir.BoardDir?>board.php?board=bigorder">도매멤버쉽</a></dd>
			<!--<dd><a href="<?=$Dir.BoardDir?>board.php?board=banking">이용안내맵</a></dd>-->
		</dl>
		        </li>



			</ul>
		</div><!-- //end navi3 -->



</div>


