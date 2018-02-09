<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$instaimgpath = $Dir.DataDir."shopimages/instagram/";

$id = $_POST['id'];
$type = $_POST['type'];
$search_word = $_POST['search_word'];
$sort = $_POST['sort'];
$limit = $type == "mobile"?'5':'16';
$page	= $_POST["page"]?$_POST["page"]:1;

$offset	= ($page - 1) * $limit;

$sql = "SELECT  i.*, li.section,
							 	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
							FROM tblinstagram i
							LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
							WHERE i.display = 'Y' ";
if(!empty($id)) $sql .= "AND i.idx < '{$id}' ";
if(!empty($search_word)){
	$sql .= "AND ( i.title iLIKE '%{$search_word}%' OR i.content iLIKE '%{$search_word}%' OR i.hash_tags = '%{$search_word}%')  ";
}



//검색 조건
$order = "";
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY i.regdt desc";
	}else if($sort == "best"){
		$order .= " ORDER BY i.access desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc";
	}
}

$sql .=	$order;
if($type == "mobile"){
	$sql .=	" LIMIT ".($limit+1)." OFFSET {$offset}";
} else {
	$sql .=	" LIMIT ".$limit;
}
// exdebug($sql);
$result = pmysql_query($sql);

if($type == "mobile"){
	$limit_cnt	= 0;
	$chkidx = "";
	while ( $row = pmysql_fetch_array($result) ) {
		$limit_cnt++;
		if ($limit_cnt == 6) {
			$chkidx = $row['idx'];
		} else {
			$arrInstaList[] = $row;
		}
	}

	$instaHtml = "";
	$last_index = "";
	foreach( $arrInstaList as $key=>$val ){
		$arrTag = explode(",",$val['hash_tags']);
		$last_index = $val['idx'];

		$instaHtml.= '<li>
								<div class="name">
									<span></span>';
		if($val['section']){
			$instaHtml .='<button class="comp-like btn-like like_i'.$val['idx'].' on" onclick="detailSaveLike(\''.$val['idx'].'\',\'on\',\'instagram\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\' )" id="like_'.$val['idx'].'" title="선택됨"><span  class="like_icount_'.$val['idx'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
		}else{
			$instaHtml .='<button class="comp-like btn-like like_i'.$val['idx'].'" onclick="detailSaveLike(\''.$val['idx'].'\',\'off\',\'instagram\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\' )" id="like_'.$val['idx'].'" title="선택 안됨"><span  class="like_icount_'.$val['idx'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
		}
		$instaHtml .= '	</div>
								<div class="cont-img"><img src="'.$instaimgpath.$val['img_m_file'].'" alt=""></div>
								<div class="title">
									<p>'.strcutMbDot(strip_tags($val['content']),35).' </p>
									<p class="tag">';
									foreach($arrTag as $tag){
										$instaHtml .='#'.trim($tag);
									}
		$instaHtml .= '		</p>
								</div>
								<div class="btnwrap mb-10">
									<ul class="ea1">
										<li class="hide"></li>
										<li><a href="instagram_view.php?ino='.$val['idx'].'" class="btn-def">상세보기</a></li>
									</ul>
								</div>
							</li>';

	}

}else{
	while ( $row = pmysql_fetch_array($result) ) {
		$arrInstaList[] = $row;
		$idx = $row['idx'];
	}

	$instaHtml = "";
	$last_index = "";
	foreach( $arrInstaList as $key=>$val ){
		$arrTag = explode(",",$val['hash_tags']);
		$last_index = $val['idx'];
		$instaHtml.= '<li>
								<figure>
									<a href="javascript:detailView(\''.$val['idx'].'\');" idx="'.$val['idx'].'" class="btn-view-detail"><img src="'.$instaimgpath.$val['img_file'].'" alt=""></a>
									<figcaption>
									<a href="javascript:detailView(\''.$val['idx'].'\');" idx="'.$val['idx'].'">
										<p class="id"></p>
										<p class="cont">'.strip_tags($val['content']).' </p>
										<p class="tag">';
		foreach($arrTag as $tag){
			$instaHtml .='#'.trim($tag);
		}
		$instaHtml .= '      	</p>
									</a>';
		if($val['section']){
			$instaHtml .='<button class="comp-like btn-like like_i'.$val['idx'].' on" onclick="detailSaveLike(\''.$val['idx'].'\',\'on\',\'instagram\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\' )" id="likedetail_'.$val['idx'].'" title="선택됨"><span  class="like_icount_'.$val['idx'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
		}else{
			$instaHtml .='<button class="comp-like btn-like like_i'.$val['idx'].'" onclick="detailSaveLike(\''.$val['idx'].'\',\'off\',\'instagram\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\' )" id="likedetail_'.$val['idx'].'" title="선택 안됨"><span  class="like_icount_'.$val['idx'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>';
		}
		$instaHtml.='</figcaption>
						</figure>
					</li>	';

	}

	//데이터가 있는지 체크
	$check_sql = "SELECT * FROM tblinstagram WHERE display = 'Y' AND idx < '{$last_index}' ";
	// if(!empty($search_word)){
	// 	$check_sql .= "AND ( title iLIKE '%{$search_word}%' OR content iLIKE '%{$search_word}%' OR hash_tags = '%{$search_word}%')  ";
	// }
	$chk_result = pmysql_query($check_sql);
	$count = pmysql_num_rows( $chk_result );
	while ( $chk_row = pmysql_fetch_array($chk_result) ) {
		$chkidx = $chk_row['idx'];
	}

}

$instaHtml .= "|||" .$chkidx;
echo $instaHtml;


?>
