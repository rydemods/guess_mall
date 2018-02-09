<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$productcode = $_POST["productcode"];
$brand = $_POST["brand"];
$view_type = $_POST["view_type"]?$_POST["view_type"]:'';
$productname = getProductName($productcode);


if($view_type == 'm') { // 모바일일 경우

	$page	= $_POST["page"]?$_POST["page"]:1;
	$limit		= 2;
	$offset	= ($page - 1) * $limit;
	$returnArr		= array();
	$postingHtml	= "";

	$postingSql = "SELECT 'instagram' as type, i.idx::varchar, i.title ,i.content, i.img_m_file, i.regdt, i.link_url, i.link_m_url, i.access, li.section,
								COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
							FROM tblinstagram i
							LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
							WHERE i.display = 'Y' AND (i.hash_tags LIKE $$%".$productname."%$$ OR i.hash_tags LIKE '%".$brand."%') ";
	$postingSql .= " UNION ";
	$postingSql .= "SELECT 'magazine' as type, m.no::varchar, m.title ,m.content, m.img_m_file, m.regdt, m.link_url, m.link_m_url, m.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblmagazine m
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code
	WHERE m.display = 'Y' AND (m.tag LIKE $$%".$productname."%$$ OR m.tag LIKE '%".$brand."%')";
	$postingSql .= " UNION ";
	$postingSql .= "SELECT 'lookbook' as type, l.no::varchar, l.title ,l.content, l.img_m_file , l.regdate as regdt,'' as link_url,'' as link_m_url, l.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tbllookbook l
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code
	WHERE l.display = 'Y' AND (l.tag LIKE $$%".$productname."%$$ OR l.tag LIKE '%".$brand."%') ";
	$postingSql .= " UNION ";
	$postingSql .= "SELECT 'forum_list' as type, f.index::varchar as idx, f.title ,f.content, f.img as img_m_file, f.writetime::varchar, '' as link_url,'' as link_m_url, f.view as access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblforumlist f
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code
	WHERE (f.tag LIKE $$%".$productname."%$$ OR f.tag LIKE '%".$brand."%')";
	$postingSql .= " ORDER BY regdt DESC LIMIT {$limit} OFFSET {$offset}";
	$postingResult	= pmysql_query($postingSql,get_db_conn());
	$postingCnt	= pmysql_num_rows($postingResult);
	while ( $postingRow = pmysql_fetch_array($postingResult) ) {
		if($postingRow['type'] == "instagram"){
			$imagepath = $Dir.DataDir."shopimages/instagram/";
			$posting_img = getProductImage($imagepath,$postingRow['img_m_file']);
			$reg_date = substr($postingRow['regdt'], 0,8);
			$postingHtml .= '
						<li class="grid-item">
							<figure>
								<!-- <a href="'.$postingRow['link_m_url'].'"><img src="'.$posting_img.'" alt=""></a> -->
								<a href="'.$Dir.MDir.'instagram_view.php?ino='.$postingRow['idx'].'"><img src="'.$posting_img.'" alt=""></a>
								<figcaption>
									<!-- <a href='.$postingRow['link_m_url'].'"> -->
									<a href="'.$Dir.MDir.'instagram_view.php?ino='.$postingRow['idx'].'">
										<span class="category">'.$reg_date.' / INSTAGRAM</span>
										<p class="title">'.$postingRow['title'].'</p>
										<p class="desc">'.strcutMbDot(strip_tags($postingRow['content']),35).'</p>
									</a>
									<button class="comp-like btn-like like_i'.$postingRow['idx'].' '.($postingRow['section']?' on':'').'" onclick="detailSaveLike(\''.$postingRow['idx'].'\',\''.($postingRow['section']?' on':'off').'\',\'instagram\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\')" id="like_'.$postingRow['idx'].'" title="'.($postingRow['section']?'선택됨':'선택 안됨').'"><span  class="like_icount_'.$postingRow['idx'].'"><strong>좋아요</strong>'.$postingRow['hott_cnt'].'</span></button>
								</figcaption>
							</figure>
						</li>';
		}else if($postingRow['type'] == "magazine"){
			$imagepath = $Dir.DataDir."shopimages/magazine/";
			$posting_img = getProductImage($imagepath,$postingRow['img_m_file']);
			$reg_date = substr($postingRow['regdt'], 0,8);
			$postingHtml .= '
						<li class="grid-item">
							<figure>
								<a href="'.$Dir.MDir.'magazine_detail.php?no='.$postingRow['idx'].'"><img src="'.$posting_img.'" alt=""></a>
								<figcaption>
									<a href='.$Dir.MDir.'magazine_detail.php?no='.$postingRow['idx'].'">
										<span class="category">'.$reg_date.' / MAGAZINE</span>
										<p class="title">'.$postingRow['title'].'</p>
										<p class="desc">'.strcutMbDot(strip_tags($postingRow['content']),35).'</p>
									</a>
									<button class="comp-like btn-like like_m'.$postingRow['idx'].' '.($postingRow['section']?' on':'').'" onclick="detailSaveLike(\''.$postingRow['idx'].'\',\''.($postingRow['section']?' on':'off').'\',\'magazine\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\')" id="like_'.$postingRow['idx'].'" title="'.($postingRow['section']?'선택됨':'선택 안됨').'"><span  class="like_mcount_'.$postingRow['idx'].'"><strong>좋아요</strong>'.$postingRow['hott_cnt'].'</span></button>
								</figcaption>
							</figure>
						</li>';
		}else if($postingRow['type'] == "lookbook"){
			$imagepath = $Dir.DataDir."shopimages/lookbook/";
			$posting_img = getProductImage($imagepath,$postingRow['img_m_file']);
			$reg_date = substr($postingRow['regdt'], 0,8);
			
			$postingHtml .= '
						<li class="grid-item">
							<figure>
								<a href="'.$Dir.MDir.'lookbook_detail.php?no='.$postingRow['idx'].'"><img src="'.$posting_img.'" alt=""></a>
								<figcaption>
									<a href='.$Dir.MDir.'lookbook_detail.php?no='.$postingRow['idx'].'">
										<span class="category">'.$reg_date.' / LOOKBOOK</span>
										<p class="title">'.$postingRow['title'].'</p>
										<p class="desc">'.strcutMbDot(strip_tags($postingRow['content']),35).'</p>
									</a>
									<button class="comp-like btn-like like_l'.$postingRow['idx'].' '.($postingRow['section']?' on':'').'" onclick="detailSaveLike(\''.$postingRow['idx'].'\',\''.($postingRow['section']?' on':'off').'\',\'lookbook\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\')" id="like_'.$postingRow['idx'].'" title="'.($postingRow['section']?'선택됨':'선택 안됨').'"><span  class="like_lcount_'.$postingRow['idx'].'"><strong>좋아요</strong>'.$postingRow['hott_cnt'].'</span></button>
								</figcaption>
							</figure>
						</li>';
			
		}else if($postingRow['type'] == "forum_list"){
			$imagepath = $Dir.DataDir."shopimages/forum/";
			$posting_img = getProductImage($imagepath,$postingRow['img_file']);
			$content = $postingRow['content'];
			$content = preg_replace('/\r\n|\r|\n/','',$content); //상품명 개행문자 제거
			$content = preg_replace("(\<(/?[^\>]+)\>)", "", $content); //상품명 태그 제거
			
			$postingHtml .= '
						<li class="grid-item">
							<figure>
								<a href="'.$Dir.MDir.'forum_view.php?index='.$postingRow['idx'].'"><img src="'.$posting_img.'" alt=""></a>
								<figcaption>
									<a href='.$Dir.MDir.'forum_view.php?index='.$postingRow['idx'].'">
										<span class="category">'.$reg_date.' / FORUM</span>
										<p class="title">'.$postingRow['title'].'</p>
										<p class="desc">'.strcutMbDot(strip_tags($content),35).'</p>
									</a>
									<button class="comp-like btn-like like_f'.$postingRow['idx'].' '.($postingRow['section']?' on':'').'" onclick="detailSaveLike(\''.$postingRow['idx'].'\',\''.($postingRow['section']?' on':'off').'\',\'forum_list\',\''.$_ShopInfo->getMemid().'\',\''.$brand.'\')" id="like_'.$postingRow['idx'].'" title="'.($postingRow['section']?'선택됨':'선택 안됨').'"><span  class="like_fcount_'.$postingRow['idx'].'"><strong>좋아요</strong>'.$postingRow['hott_cnt'].'</span></button>
								</figcaption>
							</figure>
						</li>';
			
		}
	}
	$returnArr['posting_next_page']	= ($postingCount[0] > 0 && $postingCount[0] > ($offset+$postingCnt))?($page+1):'E';
	$returnArr['posting_html']	= $postingHtml;
	$returnArr['posting_total'] = $postingCnt;
	pmysql_free_result( $postingResult );

	echo json_encode( $returnArr );

} else {
?>
<?
	$posting_sql = "SELECT 'instagram' as type, i.idx::varchar, i.title ,i.content, i.img_file, i.regdt, i.link_url, i.link_m_url, i.access, li.section,
								COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
							FROM tblinstagram i
							LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
							WHERE i.display = 'Y' AND (i.hash_tags LIKE $$%".$productname."%$$ OR i.hash_tags LIKE '%".$brand."%') ";
	$posting_sql .= " UNION ";
	$posting_sql .= "SELECT 'magazine' as type, m.no::varchar, m.title ,m.content, m.img_file, m.regdt, m.link_url, m.link_m_url, m.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblmagazine m
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code
	WHERE m.display = 'Y' AND (m.tag LIKE $$%".$productname."%$$ OR m.tag LIKE '%".$brand."%')";
	$posting_sql .= " UNION ";
	$posting_sql .= "SELECT 'lookbook' as type, l.no::varchar, l.title ,l.content, l.img_file, l.regdate as regdt,'' as link_url,'' as link_m_url, l.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tbllookbook l
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code
	WHERE l.display = 'Y' AND (l.tag LIKE $$%".$productname."%$$ OR l.tag LIKE '%".$brand."%') ";
	$posting_sql .= " UNION ";
	$posting_sql .= "SELECT 'forum_list' as type, f.index::varchar as idx, f.title ,f.content, f.img as img_file, f.writetime::varchar, '' as link_url,'' as link_m_url, f.view as access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblforumlist f
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code
	WHERE (f.tag LIKE $$%".$productname."%$$ OR f.tag LIKE '%".$brand."%')";
	$posting_sql .= "ORDER BY regdt LIMIT 8";
    //exdebug($posting_sql);
	$result = pmysql_query($posting_sql);
	while ( $row = pmysql_fetch_array($result) ) {
		$arrPosting[] = $row;
	}

	if(count($arrPosting) > 0){?>
		<ul class="comp-posting">
		<input type="hidden" id="posting_count" value="<?=count($arrPosting)?>" />
		<?foreach( $arrPosting as $key=>$val ){
				if($val['type'] == "instagram"){
					$imagepath = $Dir.DataDir."shopimages/instagram/";
					$posting_img = getProductImage($imagepath,$val['img_file']);
					$reg_date = substr($val['regdt'], 0,8);
			?>
				<li>
					<figure>
						<a href="javascript:detailView('<?=$val['idx'] ?>');"  idx="<?=$val['idx'] ?>" class="btn-view-detail"><img src="<?=$posting_img?>" alt=""></a>
							<figcaption>
								<a href="javascript:detailView('<?=$val['idx'] ?>');" idx="<?=$val['idx'] ?>" class="btn-view-detail">
									<span class="category"><?=$reg_date?> / INSTAGRAM</span>
									<p class="title"><?=$val['title']?></p>
									<p class="desc"><?=strcutMbDot(strip_tags($val['content']),35)?></p>
								</a>
								<?if($val['section']){ ?>
								<button class="comp-like btn-like like_i<?=$val['idx']?> on" onclick="detailSaveLike('<?=$val['idx']?>','on','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$val['idx']?>" title="선택됨"><span  class="like_icount_<?=$val['idx']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
								<?}else{ ?>
								<button class="comp-like btn-like like_i<?=$val['idx']?>" onclick="detailSaveLike('<?=$val['idx']?>','off','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$val['idx']?>" title="선택 안됨"><span class="like_icount_<?=$val['idx']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
								<?} ?>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "magazine") {
					$imagepath = $Dir.DataDir."shopimages/magazine/";
					$posting_img = getProductImage($imagepath,$val['img_file']);
					$reg_date = substr($val['regdt'], 0,8);
			?>
				<li>
					<figure>
						<a href="javascript:detail_magazine('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
						<figcaption>
							<a href="javascript:detail_magazine('<?=$val['idx']?>');">
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / MAGAZINE</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
							</a>
						<button class="like_m<?=$val['idx'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['idx'] ?>', '<?=$val['section']?'on':'off' ?>', 'magazine', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_mcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "lookbook") { 
					$imagepath = $Dir.DataDir."shopimages/lookbook/";
					$posting_img = getProductImage($imagepath,$val['img_file']);
					$reg_date = substr($val['regdate'], 0,8);
			?>
				<li>
					<figure>
						<a href="javascript:detail_lookbook('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
						<figcaption>
							<a href="javascript:detail_lookbook('<?=$val['idx']?>');">
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / LOOKBOOK</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
							</a>
						<button class="like_l<?=$val['idx'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['idx'] ?>', '<?=$val['section']?'on':'off' ?>', 'lookbook', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_lcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "forum_list") {
					$imagepath = $Dir.DataDir."shopimages/forum/";
					$posting_img = getProductImage($imagepath,$val['img_file']);
					$content = $val['content'];
					$content = preg_replace('/\r\n|\r|\n/','',$content); //상품명 개행문자 제거
					$content = preg_replace("(\<(/?[^\>]+)\>)", "", $content); //상품명 태그 제거
			?>
				<li>
					<figure>
						<a href="<?=$Dir.FrontDir?>forum_view.php?index=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a> 
						<figcaption>
							<a href="<?=$Dir.FrontDir?>forum_view.php?index=<?=$val['idx']?>">
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / FORUM</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($content), 35)?></p>
							</a>
						<button class="comp-like btn-like like_f<?=$val['idx'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['idx'] ?>','on','forum_list_mypage','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['idx'] ?>"title="선택됨"><span class="like_fcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}?>
		<?}?>
		</ul>
	<?}else{?>
		<li class="ta-c none">관련 포스팅이 없습니다.</li>
	<?}?>
<?
}
?>