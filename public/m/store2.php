<?
include_once('outline/header_m.php');
include ("header.inc.php");
$subTitle = "오프라인 매장";
include ("sub_header.inc.php");

$location = array("서울특별시","인천광역시","경기도","강원도","대전광역시","충청도","대구광역시","경상도","부산광역시","울산광역시","광주광역시","전라도","제주도");
$search_l=$_POST["search_l"];
$search_w=$_POST["search_w"];

$gotopage=$_POST["gotopage"];
if($gotopage==""){
	$gotopage = 0;
}

$qry = " ";
if($search_w!=''){
	$qry = "AND title LIKE '%".$search_w."%'";
}
if($search_l!=''){
	$qry = "AND name LIKE '%".$search_l."%'";
}


$tsql="select num FROM tblboard WHERE board='offlinestore' ".$qry;
$cnt_res = pmysql_query($tsql);
$tcnt=pmysql_num_rows($cnt_res);
$tcnt = round($tcnt/5,0);

$sql="select * FROM tblboard WHERE board='offlinestore' ".$qry;
$sql .= " ORDER BY title LIMIT 5 OFFSET {$gotopage} ";
$sql_off = pmysql_query($sql);
while($res=pmysql_fetch_array($sql_off)){
	$res_list[]=$res;
}
?>
<script>
<!--
function GoPage(gotopage) {
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
function goSearch(){
	if(document.form1.search_w.value !=''){
		document.form1.search_l.value = '';
	}
	document.form1.submit();
}

function subToggle(eq){
	$("#open_store"+eq).toggle(100);
	$("#store_list"+eq).toggleClass('open');
}

//-->
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">
<article>
	<section class="store_search">
		<form method="POST" name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<div>
			<!--<p></p>-->
		<select name="search_l">
			<option value="">지역선택</option>
			<? foreach($location as $lc){
				echo "<option value=\"$lc\"";
				if($lc==$search_l){
					echo " selected=\"selected\">$lc</option>";
				}else{
					echo " >$lc</option>";
				}
			}
			?>
		</select>
		</div>
		매장명<input type="text" name="search_w" id=""  value="<?=$search_w?>" /><input type="button" value="검색" onclick="javascript:goSearch()" />
		</form>
	</section>

	<section class="store_sorting">
		<ul class="store_list">
			<?
			if($res_list){
				$temp = 0;
				foreach($res_list as $rl){
			?>
			<li id="store_list<?=$temp?>">
				<a href="javascript:subToggle(<?=$temp?>)"><div>
				<p class="store_name">
				<span class="title"><?=$rl[title]?></span>
				<span class="tel"><?=$rl[storetel]?></span>
				</p>
				<!--<p class="star3">별점 5개</p>-->
				<p class="addr"><?=$rl[storeaddress]?></p>
				</div></a>
				<div class="open_store" id="open_store<?=$temp?>" style="display:none;">
				<?if($rl[vfilename]){?>
				<img src="<?=$rl[storefilename]?>" width="100%">
				<?}else{?>
				<img src="../front/image/noimg.jpg" width="100%">
				<?}?>
				</div>
			</li>
			<?	$temp++;
				}
			} else{ ?>
			<li class="">
				<p class="store_name" align="center">결과가 없습니다.</p>
			</li>
			<?}?>
		</ul>

	<div class="paginate">
		<?if($gotopage!=0){ ?>
			<a href="javascript:GoPage(<?=$gotopage-1?>)" class="pre">이전</a>
		<?}?>
		<?=$gotopage+1?><?if($tcnt>1){ echo " / ".$tcnt;}?>
		<?if($gotopage+1<$tcnt and $tcnt>1){ ?>
			<a href="javascript:GoPage(<?=$gotopage+1?>)" class="next">다음</a>
		<?}?>
	</div>

	</section>
 </article>
<? include ("footer.inc.php"); ?>

<!--<li>
	<a href=""><div>
		<p class="store_name">
		<span class="title">현대백화점 일산킨텍스점</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">별점 5개</p>
	<p class="addr">경기도 고양시 일산서구 대화동 2602 현대 백화점 킨텐스점 3층</p>
	</div></a>
	</li>
	<li class="open"><a href=""><div>
		<p class="store_name">
		<span class="title">현대백화점 본점</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star3">별점 5개</p>
	<p class="addr">경기도 고양시 일산서구 대화동 2602 현대 백화점 킨텐스점 3층</p>
	</div></a>

	<div class="open_store">
       <img src="img/store.jpg" alt="" width="100%" />
	<ul class="sns">
	<li class="sns_t"><a href="">트위터</a></li>
	<li class="sns_f"><a href="">페이스북</a></li>
	<li class="sns_m"><a href="">미투데이</a></li>
	<li class="sns_k"><a href="">카카오톡</a></li>
	<li class="sns_c"><a href="">싸이월드</a></li>
 	</ul>

		 <div class="goods_review store_rev">
		 <h3>매장 한줄 평가하기</h3>
		<form method="post" action="" class="form_wrap">
			<div class="radio_star">
			<p><input type="radio" id="star1" /> <label for="star1"  class="no01">별점5</label><input type="radio" id="star2" /> <label for="star2"  class="no02">별점4</label>
			<input type="radio" id="star3" /> <label for="star3"  class="no03">별점3</label></p>
			<p><input type="radio" id="star4" /> <label for="star4"  class="no04">별점2</label><input type="radio" id="star5" /> <label for="star5"  class="no05">별점1</label></p>
		</div>
		<div class="form">
		<textarea id="" rows="" cols="" class="contents">내용을 입력해주세요.</textarea>
		<input type="button" value="한줄 평가하기" onclick="" />
		</div>
		</form>
		<table class="store_tb">
			<tr>
				<td><p class="star4">별점 5개</p>  <span class="date">2013.10.25</span>
				<span class="name">박용하님</span>
				매니저님 서비스가 너무 좋아요. 매장에 자주 갈게요.
				</td>
			</tr>
			<tr>
				<td><p class="star2">별점 5개</p>  <span class="date">2013.10.25</span>
				<span class="name">박용하님</span>
				매니저님 서비스가 너무 좋아요. 매장에 자주 갈게요.
				</td>
			</tr>
			<tr>
				<td><p class="star2">별점 5개</p>  <span class="date">2013.10.25</span>
				<span class="name">박용하님</span>
				매니저님 서비스가 너무 좋아요. 매장에 자주 갈게요.
				</td>
			</tr>
		</table>
		</div>
<a href="" class="attention">매장 이벤트 쿠폰 발급받기&nbsp;&nbsp;(본 서비스는 로그인 후 이용가능)</a>
	</div>
	</li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">현대백화점 일산킨텍스점</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">별점 5개</p>
	<p class="addr">경기도 고양시 일산서구 대화동 2602 현대 백화점 킨텐스점 3층</p>
	</div></a></li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">현대백화점 일산킨텍스점</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">별점 5개</p>
	<p class="addr">경기도 고양시 일산서구 대화동 2602 현대 백화점 킨텐스점 3층</p>
	</div></a></li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">현대백화점 일산킨텍스점</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">별점 5개</p>
	<p class="addr">경기도 고양시 일산서구 대화동 2602 현대 백화점 킨텐스점 3층</p>
	</div></a></li>-->