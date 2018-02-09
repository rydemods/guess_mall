<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath = $Dir.DataDir."shopimages/lookbook/";
$search = $_POST["search"];
$sort = $_POST["sort"] ? $_POST["sort"] : 'latest';
$year = $_POST["year"];
$kind = $_POST['kind'];

//검색 조건
$where = "";
$order = "";
if(!empty($search)){
	$where .= " AND ( l.title iLIKE '%{$search}%' OR l.content iLIKE '%{$search}%' OR l.tag = '%{$search}%')  ";
}
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY regdate desc, no desc";
	}else if($sort == "best"){
		$order .= " ORDER BY access desc, no desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc, no desc";
	}
}
if(!empty($year)){
	$where .= " AND regdate >= '".$year."0101000000' AND regdate <= '".$year."1231235959' " ;
}

//룩북 리스트
$sql = "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$sql .= "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$sql .= "WHERE l.display = 'Y' ";
$sql .= $where;
$sql .= $order;
$sql .= ") INFO ORDER BY ROWNUM ";
if($kind == "mobile"){
	$sql .= "LIMIT 10";
}else{
	$sql .= "LIMIT 12";
}
// exdebug($sql);
$result = pmysql_query($sql);
while ($row = pmysql_fetch_array($result)) {
	$arrLookbookList[] = $row;
	$rownum = $row['rownum'];
}

//데이터가 있는지 체크
$check_sql = "SELECT * FROM (";
$check_sql .= "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$check_sql .= "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$check_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$check_sql .= "WHERE l.display = 'Y' ";
$check_sql .= $where;
$check_sql .= $order;
$check_sql .= " ) a";
if($rownum){
	$check_sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM LIMIT 1";
}else{
	$check_sql .= ") INFO ORDER BY ROWNUM LIMIT 1";
}
$chk_result = pmysql_query($check_sql);
while ( $chk_row = pmysql_fetch_array($chk_result) ) {
	$chk_rownum = $chk_row['rownum'];
}

$arr= array();
if(count($arrLookbookList) > 0){
	foreach( $arrLookbookList as $key=>$val ){
		$reg_date	= substr($val['regdate'],0,4).".".substr($val['regdate'],4,2).".".substr($val['regdate'],6,2);
		$html = '
							<li>
								<a href="javascript:detail(\''.$val['no'].'\');">
									<figure>
										<img src="'.$imagepath.$val['img_file'].'" alt="룩북이미지">
										<figcaption>
											<div class="inner">
												<p>'.$val['title'].'</p>
												<p>'.$reg_date.'</p>
											</div>			
										</figcaption>
									</figure>
								</a>
								<div class="btn-posting">
									<button class="comp-like btn-like like_l'.$val['no'].' '.($val['section']?' on':'').'" onclick="detailSaveLike(\''.$val['no'].'\',\''.($val['section']?' on':'off').'\',\'lookbook\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\')" id="like_'.$val['no'].'" title="'.($val['section']?'선택됨':'선택 안됨').'"><span  class="like_lcount_'.$val['no'].'"><strong>좋아요</strong>'.$val['hott_cnt'].'</span></button>
							   </div>
							</li>';
		
		array_push($arr,$html);
	}
	$hiddenhtml .= '<input type="hidden" id="chk_rownum" value="'.$chk_rownum.'" />';
	array_push($arr,$hiddenhtml);
}
echo json_encode( $arr );

?>


