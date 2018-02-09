<?php
/*
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";
$search_word = $_POST['search_word'];
$sort = $_POST["sort"] ? $_POST["sort"] : 'latest';
$sql = "SELECT  i.*, li.section, LAG(idx,1,'1') OVER(ORDER BY regdt DESC) AS pre_idx, LEAD(idx,1,'1') OVER(ORDER BY regdt DESC) AS next_idx,
							 	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
							FROM tblinstagram i
							LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar  = li.hott_code
							WHERE i.display = 'Y' ";
if(!empty($search_word)){
	$sql .= "AND ( i.title iLIKE '%{$search_word}%' OR i.content iLIKE '%{$search_word}%' OR i.hash_tags = '%{$search_word}%')  ";
}

//검색 조건
$order = "";
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY i.regdt desc, i.idx desc";
	}else if($sort == "best"){
		$order .= " ORDER BY i.access desc, i.idx desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc, i.idx desc";
	}
}
$sql .=	$order;
$sql .= " LIMIT 16";
// exdebug($sql);
$result = pmysql_query($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$arrInstaList[] = $row;
	$idx = $row['idx'];
}

//데이터가 있는지 체크
$check_sql = "SELECT * FROM tblinstagram WHERE display = 'Y' AND idx < '{$idx}' ";
$chk_result = pmysql_query($check_sql);
$count = pmysql_num_rows( $chk_result );
while ( $chk_row = pmysql_fetch_array($chk_result) ) {
	$chkidx = $chk_row['idx'];
}*/

?>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Instagram.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';

var brows = ''; 
var insta = new Instagram(req);


$(document).ready( function() {

	var rows = insta.getInstagramCategory();
	$('#instagram_tags').html(rows);

	insta.getInstagramListCnt(insta.currpage);
	
});

function setdisplay(val){
	insta.instagram_tags = val;
	insta.brows='';
	insta.getInstagramListCnt(1);
	$('.category_link').removeClass('active');
	$('#category_link_'+val).addClass('active');
	
}


</script>

<div id="contents">
	<div class="style-page">

		<article class="style-wrap">
			<header><h2 class="style-title">INSTAGRAM</h2></header>
			<div class="instagram-tags mb-5" id="instagram_tags">
				
			</div>
			<ul class="style-list ea3 instagram clear" id="list_area">
				
				
				
			</ul>
			<div class="read-more mt-70" id="read_more"><button type="button" onclick="insta.getInstagramListCnt(insta.currpage);"><span>READ MORE</span></button></div>
		</article>

	</div>
</div><!-- //#contents -->


