<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$imagepath = $Dir.DataDir."shopimages/magazine/";
$rownum = $_POST['rownum'];
$category_nm = $_POST["category_nm"];
$sort = $_POST["sort"] ? $_POST["sort"] : 'latest';
$kind = $_POST['kind'];

if(!empty($category_nm)){
	$where .= " AND category_nm = '{$category_nm}'";
}
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY regdt desc, no desc";
	}else if($sort == "best"){
		$order .= " ORDER BY access desc, no desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc, no desc";
	}
}
$sql = "SELECT * FROM (";
$sql .= "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$sql .= "SELECT m.*, li.section,
			COALESCE((select COUNT( mc.no )AS comment_cnt from tblmagazine_comment mc WHERE  m.no = mc.mnum),0) AS comment_cnt,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tblmagazine m ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code ";
$sql .= "WHERE display = 'Y' AND (type = 0 OR type = 1)";
$sql .= $where;
$sql .= $order;
$sql .= " ) a";
$sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM ";
if($kind == "mobile"){
	$sql .= "LIMIT 10";
}else{
	$sql .= "LIMIT 16";
}
$result = pmysql_query($sql);
// exdebug($sql);

if($kind == "mobile"){
	$html = "";
	while ( $row = pmysql_fetch_array($result) ) {
		$arrMagazine[] = $row;
		$rownum = $row['rownum'];
	}
	$last_index = "";
	foreach( $arrMagazine as $key=>$val ){
		$last_index = $val['rownum'];
		$html .= '<li  class="'.($val['type']=="1" ? 'video': '').' ">
							<span>
								<a href="javascript:detail(\''.$val['no'].'\');"> <img src="'.$imagepath.$val['img_file'].'" alt=""></a>
									<div class="btn-posting">
										<button class="like_m'.$val['no'].' comp-like btn-like '.($val['section']?' on':'').'" onclick="detailSaveLike(\''.$val['no'].'\',\''.($val['section']?' on':'off').'\',\'magazine\',\''.$_ShopInfo->getMemid().'\',\'\')"  title="'.($val['section']?'선택됨':'선택 안됨').'"><span class="like_mcount_'.$val['no'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>
										<span class="comment"><strong>댓글</strong>'.$val['comment_cnt'].'</span>
									</div>
							</span>					
						</li>	';
	}
}else{
	$html = "";
	while ( $row = pmysql_fetch_array($result) ) {
		$arrMagazine[] = $row;
		$rownum = $row['rownum'];
	}
	$last_index = "";
	foreach( $arrMagazine as $key=>$val ){
		$reg_date	= substr($val['regdt'],0,4).".".substr($val['regdt'],4,2).".".substr($val['regdt'],6,2);
		$last_index = $val['rownum'];
		$html .= '<li>
							<a href="javascript:detail(\''.$val['no'].'\');">
								<figure class="'.($val['type']=="1" ? 'video': '').' ">
									<img src="'.$imagepath.$val['img_file'].'" alt="">
									<figcaption>
										<div class="btn-posting">
											<button class="like_m'.$val['no'].' comp-like btn-like '.($val['section']?' on':'').'" onclick="detailSaveLike(\''.$val['no'].'\',\''.($val['section']?' on':'off').'\',\'magazine\',\''.$_ShopInfo->getMemid().'\',\'\')"  title="'.($val['section']?'선택됨':'선택 안됨').'"><span class="like_mcount_'.$val['no'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>
											<span class="comment"><strong>댓글</strong>'.$val['comment_cnt'].'</span>
										</div>	
										<p class="title">'.$val['title'].'</p>
										<p>'.$reg_date.'</p>
									</figcaption>
								</figure>				
							</a>				
						</li>	';
	}
	
}

//데이터가 있는지 체크
$check_sql = "SELECT * FROM (";
$check_sql .= "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$check_sql .= "SELECT  m.*,
			COALESCE((select COUNT( mc.no )AS comment_cnt from tblmagazine_comment mc WHERE  m.no = mc.mnum),0) AS comment_cnt,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tblmagazine m ";
$check_sql .= "WHERE display = 'Y' AND (type = 0 OR type = 1)";
$check_sql .= $where;
$check_sql .= $order;
$check_sql .= " ) a";
$check_sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM  ";
if($kind == "mobile"){
	$check_sql .= "LIMIT 10";
}else{
	$check_sql .= "LIMIT 16";
}
$chk_result = pmysql_query($check_sql);
while ( $chk_row = pmysql_fetch_array($chk_result) ) {
	$chk_rownum = $chk_row['rownum'];
}

$html .= "|||" .$chk_rownum;
echo $html;

?>