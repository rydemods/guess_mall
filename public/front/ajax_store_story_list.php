<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath = $Dir.DataDir."shopimages/store_story/";

$store_code = $_POST["store_code"];
$search = $_POST["search"];
$start_sno = $_POST["start_sno"];
$view_type = $_POST["view_type"]?$_POST["view_type"]:'';

$page	= $_POST["page"]?$_POST["page"]:1;
if ($view_type == 'm') {
	$limit		= 6;
} else {
	$limit		= 5;
}
$offset	= ($page - 1) * $limit;
$returnArr		= array();
$storyHtml	= "";

$storyAddSql.= "FROM tblstorestory s LEFT JOIN tblstore st ON s.store_code=st.store_code LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'storestory' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) h ON s.sno::varchar = h.hott_code ";
$storyAddSql.= "WHERE 1=1 ";
if ($start_sno) $storyAddSql.= "AND s.sno <= {$start_sno} ";
if ($store_code) $storyAddSql.= "AND s.store_code = '{$store_code}' ";
if (trim($search)) $storyAddSql.= "AND (st.name LIKE '%{$search}%' OR s.content LIKE '%{$search}%' ) ";

$storyCountSql = 'SELECT COUNT( * ) AS storycnt ';
$storyCountSql.= $storyAddSql;

$storyCountRes = pmysql_query( $storyCountSql, get_db_conn() );
$storyCount = pmysql_fetch_row( $storyCountRes );
$returnArr['story_total']	= $storyCount[0];
pmysql_free_result( $storyCountRes );

$storySql = "SELECT s.*, st.name as store_name, h.section,
								COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND s.sno::varchar = tl.hott_code),0) AS hott_cnt,
								COALESCE((select COUNT( sc.cno )AS sc_cnt from tblstorestory_comment sc WHERE sc.sno = s.sno),0) AS sc_cnt ";
$storySql .= $storyAddSql;
$storySql .= "order by s.sno desc LIMIT {$limit} OFFSET {$offset}";

$storyResult	= pmysql_query($storySql,get_db_conn());
$storyCnt	= pmysql_num_rows($storyResult);
$cnt	= 0;
$returnArr['story_total_sno']	= "";
while ( $storyRow = pmysql_fetch_array($storyResult) ) {
	if ($page == 1 && $cnt == 0) $returnArr['story_start_sno']	= $storyRow['sno'];
	if ($returnArr['story_total_sno']) $returnArr['story_total_sno'] .= ",";
	$returnArr['story_total_sno']	.= $storyRow['sno'];
	$story_img = getProductImage($imagepath,$storyRow['filename']);
	$reg_date = substr($val['regdt'], 0,8);
	if ($view_type == 'm') {
		$storyHtml .= '
						<li class="grid-item">';
	} else {
		$storyHtml .= '
						<li class="grid-item">';
	}
		$storyHtml .= '
							<figure>
								<a href="javascript:stsDetailView(\''.$storyRow['sno'].'\',\'open\');" class="btn-view-detail"><img src="'.$story_img.'" alt=""></a>
								<figcaption>
									<a href="javascript:;">
										<p class="id"><span>@</span>'.$storyRow['store_name'].'</p>
									</a>
									<p class="subject">'.$storyRow['title'].'</p>
									<p class="name">'.setEmailEncryp($storyRow['mem_id']).'</p>
									<div class="btn-posting">
										<button class="like_s'.$storyRow['sno'].' comp-like btn-like'.($storyRow['section']?' on':'').'" onclick="detailSaveLike(\''.$storyRow['sno'].'\',\''.($storyRow['section']?' on':'off').'\',\'storestory\',\''.$_ShopInfo->getMemid().'\',\'\')" title="'.($storyRow['section']?'선택됨':'선택 안됨').'"><span  class="like_scount_'.$storyRow['sno'].'"><strong>좋아요</strong>'.number_format($storyRow['hott_cnt']).'</span></button>
										<span class="comment"><strong>댓글</strong>'.number_format($storyRow['sc_cnt']).'</span>
									</div>
								</figcaption>
							</figure>
						</li>';
	$cnt++;
}
if ($page > 1) $returnArr['story_start_sno']	= $start_sno;
$returnArr['story_next_page']	= ($storyCount[0] > 0 && $storyCount[0] > ($offset+$storyCnt))?($page+1):'E';
$returnArr['story_html']	= $storyHtml;
$returnArr['storySql']	= $storySql;
pmysql_free_result( $storyResult );

echo json_encode( $returnArr );