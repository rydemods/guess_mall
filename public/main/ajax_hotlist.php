<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$tab = $_POST["tab"];
$type = $_POST["type"];
$pr_link = $Dir.'m/productdetail.php?productcode=';

//포스팅 Tab 
if($tab == "posting"){

	$posting_sql = "SELECT * FROM (";
	$posting_sql.= "SELECT ROW_NUMBER() OVER(order by regdt desc) AS ROWNUM,* FROM( ";
	$posting_sql .= "SELECT 'instagram' as type, i.idx::varchar, i.title ,i.content, i.img_file, i.regdt, i.link_url, i.link_m_url ,i.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblinstagram i
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code WHERE i.display = 'Y'";
	$posting_sql .= " UNION ";
/*	$posting_sql .= "SELECT 'forum_list' as type, f.index::varchar as idx, f.title ,f.content, f.img as img_file, f.writetime::varchar, '' as link_url, f.view as access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblforumlist f
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code";
	$posting_sql .= " UNION ";*/
	$posting_sql .= "SELECT 'magazine' as type, m.no::varchar, m.title ,m.content, m.img_file, m.regdt, m.link_url, m.link_m_url ,m.access, li.section,
	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblmagazine m
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code WHERE m.display = 'Y' AND m.type != 2 ";
	$posting_sql .= " UNION ";
	$posting_sql .= "SELECT 'lookbook' as type, l.no::varchar, l.title ,l.content, l.img_file, l.regdate as regdt,'' as link_url, '' as link_m_url  ,l.access, li.section,
	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tbllookbook l
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code WHERE l.display = 'Y' ";
	$posting_sql .= " ORDER BY regdt DESC";
	$posting_sql .= " ) a";
	$posting_sql .= " ) INFO ORDER BY ROWNUM  LIMIT 20";
	
// 	exdebug($posting_sql);
	$result = pmysql_query($posting_sql);
	while ( $row = pmysql_fetch_array($result) ) {
		$arrPosting[] = $row;
		$rownum = $row['rownum'];
	}	
	if($type == "mobile"){
		//데이터가 있는지 체크
		$check_sql = "SELECT * FROM (";
		$check_sql.= "SELECT ROW_NUMBER() OVER(order by access desc) AS ROWNUM,* FROM( ";
		$check_sql .= "SELECT 'instagram' as type, i.idx::varchar, i.title ,i.content, i.img_file, i.regdt, i.link_url, i.link_m_url ,i.access, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblinstagram i
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code WHERE i.display = 'Y'";
		$check_sql .= " UNION ";
/*		$check_sql .= "SELECT 'forum_list' as type, f.index::varchar as idx, f.title ,f.content, f.img as img_file, f.writetime::varchar, '' as link_url, f.view as access, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblforumlist f
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code";
		$check_sql .= " UNION ";*/
		$check_sql .= "SELECT 'magazine' as type, m.no::varchar, m.title ,m.content, m.img_file, m.regdt, m.link_url, m.link_m_url ,m.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblmagazine m
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code WHERE m.display = 'Y' AND m.type != 2 ";
		$check_sql .= " UNION ";
		$check_sql .= "SELECT 'lookbook' as type, l.no::varchar, l.title ,l.content, l.img_file, l.regdate as regdt,'' as link_url, '' as link_m_url ,l.access, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
		FROM tbllookbook l
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code WHERE l.display = 'Y' ";
		$check_sql .= " ORDER BY  access DESC";
		$check_sql .= " ) a";
		$check_sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM  LIMIT 20";
		$chk_result = pmysql_query($check_sql);
		while ( $chk_row = pmysql_fetch_array($chk_result) ) {
			$chk_rownum = $chk_row['rownum'];
		}
	}
	if(count($arrPosting) > 0){
		foreach( $arrPosting as $key=>$val ){
			if($val['type'] == "instagram"){
				$imagepath = $Dir.DataDir."shopimages/instagram/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdt'], 0,8);
	?>
				<li class="grid-item">
					<figure>
						<?if($type == "mobile"){?>
						<!-- <a href="<?=$val['link_m_url'] ?>"><img src="<?=$posting_img?>" alt=""></a> -->
						<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$val['idx'] ?>"><img src="<?=$posting_img?>" alt=""></a>
						<?}else{ ?>
						<a href="javascript:detailView('<?=$val['idx'] ?>');"  idx="<?=$val['idx'] ?>" class="btn-view-detail"><img src="<?=$posting_img?>" alt=""></a>
						<?} ?>
						<figcaption>
							<?if($type == "mobile"){?>
							<!-- <a href="<?=$val['link_m_url'] ?>"> -->
							<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$val['idx'] ?>">
							<?}else{ ?>
							<a href="javascript:detailView('<?=$val['idx'] ?>');" idx="<?=$val['idx'] ?>" class="btn-view-detail">
							<?} ?>
								<span class="category"><?=$reg_date?> / INSTAGRAM</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35) ?></p>
							</a>
							<?if($val['section']){ ?>
							<button class="comp-like btn-like like_i<?=$val['idx']?> on" onclick="detailSaveLike('<?=$val['idx']?>','on','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$val['idx']?>" title="선택됨"><span  class="like_icount_<?=$val['idx']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?}else{ ?>
							<button class="comp-like btn-like like_i<?=$val['idx']?>" onclick="detailSaveLike('<?=$val['idx']?>','off','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$val['idx']?>" title="선택 안됨"><span class="like_icount_<?=$val['idx']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?} ?>
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
				<li class="grid-item">
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>forum_view.php?index=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<a href="<?=$Dir.FrontDir?>forum_view.php?index=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a> 
						<?}?>
						<figcaption>
							<?if($type == "mobile"){?>
							<a href="<?=$Dir.MDir?>forum_view.php?index=<?=$val['idx']?>">
							<?}else{ ?>
							<a href="<?=$Dir.FrontDir?>forum_view.php?index=<?=$val['idx']?>">
							<?} ?>
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / FORUM</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($content), 35)?></p>
							</a>
						<button class="comp-like btn-like like_f<?=$val['idx'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['idx'] ?>','on','forum_list_mypage','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['idx'] ?>"title="선택됨"><span class="like_fcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
		<?}else if($val['type'] == "magazine"){ 
				$imagepath = $Dir.DataDir."shopimages/magazine/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdt'], 0,8);
		?>
				<li class="grid-item">
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>magazine_detail.php?no=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<a href="javascript:detail_magazine('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
						<?}?>
						<figcaption>
							<?if($type == "mobile"){?>
							<a href="<?=$Dir.MDir?>magazine_detail.php?no=<?=$val['idx']?>">
							<?}else{ ?>
							<a href="javascript:detail_magazine('<?=$val['idx']?>');">
							<?} ?>
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
			<li class="grid-item">
				<figure>
					<?if($type == "mobile"){?>
					<a href="<?=$Dir.MDir?>lookbook_detail.php?no=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
					<?} else {?>
					<a href="javascript:detail_lookbook('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
					<?}?>
					<figcaption>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>lookbook_detail.php?no=<?=$val['idx']?>">
						<?}else{ ?>
						<a href="javascript:detail_lookbook('<?=$val['idx']?>');">
						<?} ?>
							<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / LOOKBOOK</span>
							<p class="title"><?=$val['title']?></p>
							<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
						</a>
					<button class="like_l<?=$val['idx'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['idx'] ?>', '<?=$val['section']?'on':'off' ?>', 'lookbook', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_lcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
					</figcaption>
				</figure>
			</li>
		<?} ?>
		
	<?} ?>	
		<input type="hidden" id="posting_rownum" value="<?=$rownum ?>" />
		<input type="hidden" id="posting_chknum" value="<?=$chk_rownum ?>" />
	<?}else{ ?>
		<li class="ta-c none"></li>
	<?} ?>
<?} //포스팅 Tab  ?>
<?
//리뷰 Tab
if($tab == "review"){
	$imagepath=$Dir.DataDir."shopimages/product/";
	
	$review_sql = "SELECT * FROM (";
	$review_sql .= "select ROW_NUMBER() OVER(order by review_cnt desc) AS ROWNUM, * from (SELECT p.productcode, p.productname, p.sellprice, p.consumerprice, p.brand, p.minimage, p.display, p.hotdealyn ,li.section,
		 (select count(r.*) from tblproductreview r where r.productcode = p.productcode) as review_cnt,
		(select subject from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by subject) as subject,
		(select content from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by content) as content,
		COALESCE((select best_type from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by best_type),0) as best_review,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt
	 from tblproduct p
	 LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code ) a
	 WHERE review_cnt != 0 AND display = 'Y' AND hotdealyn = 'N' AND best_review = 1
	ORDER BY review_cnt DESC";
	$review_sql .= ") INFO ORDER BY ROWNUM  LIMIT 20";
// 	exdebug($review_sql);
	$result = pmysql_query($review_sql);
	while ( $row = pmysql_fetch_array($result) ) {
		$arrReview[] = $row;
		$rownum = $row['rownum'];
	}
	
	if($type == "mobile"){
		//데이터가 있는지 체크
		$check_sql = "SELECT * FROM (";
		$check_sql .= "select ROW_NUMBER() OVER(order by review_cnt desc) AS ROWNUM, * from (SELECT p.productcode, p.display, p.hotdealyn,
			 (select count(r.*) from tblproductreview r where r.productcode = p.productcode) as review_cnt,
			(select subject from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by subject) as subject,
			(select content from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by content) as content,
			COALESCE((select best_type from tblproductreview r where r.productcode = p.productcode and best_type = 1 group by best_type),0) as best_review,
		 from tblproduct p ) a
		 WHERE review_cnt != 0 AND display = 'Y' AND hotdealyn = 'N' AND best_review = 1
		ORDER BY review_cnt DESC";
		$check_sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM  LIMIT 20";
		$chk_result = pmysql_query($check_sql);
		while ( $chk_row = pmysql_fetch_array($chk_result) ) {
			$chk_rownum = $chk_row['rownum'];
		}
	}
	
	if(count($arrReview) > 0){
		foreach( $arrReview as $key=>$val ){
	?>
				<li class="grid-item">
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$pr_link.$val['productcode'] ?>"><img src="<?=$imagepath.$val['minimage']?>" alt=""></a>
						<figcaption>
							<a href="<?=$pr_link.$val['productcode'] ?>">
<!-- 								<span class="category">20160801 / LOOKBOOK</span> -->
						<?}else{ ?>
						<a href="javascript:prod_detail('<?=$val['productcode'] ?>');"><img src="<?=$imagepath.$val['minimage']?>" alt=""></a>
						<figcaption>
							<a href="javascript:prod_detail('<?=$val['productcode'] ?>');">
						<?} ?>
								<p class="title"><?=$val['subject']?></p>
	                             <p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 20)?></p>
							</a>
							<?if($val['section']){ ?>
							<button class="comp-like btn-like like_p<?=$val['productcode']?> on" onclick="detailSaveLike('<?=$val['productcode']?>','on','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="like_<?=$val['productcode']?>" title="선택됨"><span  class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?}else{ ?>
							<button class="comp-like btn-like like_p<?=$val['productcode']?>" onclick="detailSaveLike('<?=$val['productcode']?>','off','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="like_<?=$val['productcode']?>" title="선택 안됨"><span class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
							<?} ?>
						</figcaption>
					</figure>
				</li>
		<?} ?>
		<input type="hidden" id="review_rownum" value="<?=$rownum ?>" />
		<input type="hidden" id="review_chknum" value="<?=$chk_rownum ?>" />
	<?}else{ ?>
	<li class="ta-c none"></li>
	<?} ?>
<?} //리뷰 Tab  ?>
<?
if($tab == "like"){
	$sql = "SELECT * FROM (";
	$sql.= "SELECT ROW_NUMBER() OVER(order by hott_cnt desc) AS ROWNUM,* FROM( ";
	$sql .= "SELECT 'product' as type,'' as idx ,p.productcode, p.productname, '' as title,'' as content, p.sellprice, p.consumerprice, p.brand, p.minimage, '' as img_file ,'' as regdt, '' as link_url, '' as link_m_url  ,li.section, 
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt
	FROM tblproduct p
	LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code WHERE p.display = 'Y' AND hotdealyn = 'N'  ";
	$sql .= " UNION ";
	$sql .= "SELECT 'instagram' as type, i.idx::varchar, '' as productcode, '' as productname, i.title ,i.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, i.img_file, i.regdt, i.link_url, i.link_m_url ,li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblinstagram i
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code WHERE i.display = 'Y'";
	$sql .= " UNION ";
	$sql .= "SELECT 'storestory' as type, s.sno::varchar as idx, '' as productcode, '' as productname, s.title ,s.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, s.filename as img_file, s.regdt, '' as link_url, '' as link_m_url ,li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND s.sno::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblstorestory s
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'storestory' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on s.sno::varchar = li.hott_code";
	$sql .= " UNION ";
/*	$sql .= "SELECT 'forum_list' as type, f.index::varchar as idx, '' as productcode, '' as productname, f.title ,f.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, f.img, f.writetime::varchar, '' as link_url, li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblforumlist f
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code";
	$sql .= " UNION ";*/
	$sql .= "SELECT 'magazine' as type, m.no::varchar, '' as productcode, '' as productname, m.title ,m.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, m.img_file, m.regdt, m.link_url, m.link_m_url ,li.section,
	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tblmagazine m
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code WHERE m.display = 'Y' AND m.type != 2 ";
	$sql .= " UNION ";
	$sql .= "SELECT 'lookbook' as type, l.no::varchar, '' as productcode, '' as productname, l.title ,l.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, l.img_file, l.regdate as regdt,'' as link_url, '' as link_m_ur ,li.section,
	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
	FROM tbllookbook l
	LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code WHERE l.display = 'Y' ";
	$sql .= " ORDER BY  hott_cnt DESC";
	$sql .= " ) a";
	$sql .= " ) INFO ORDER BY ROWNUM  LIMIT 20";
// 	exdebug($sql);
	$result = pmysql_query($sql);
	while ( $row = pmysql_fetch_array($result) ) {
		$arrLike[] = $row;
		$rownum = $row['rownum'];
	}
	if($type == "mobile"){
		//데이터가 있는지 체크
		$check_sql = "SELECT * FROM (";
		$check_sql.= "SELECT ROW_NUMBER() OVER(order by hott_cnt desc) AS ROWNUM,* FROM( ";
		$check_sql .= "SELECT 'product' as type,'' as idx ,p.productcode, p.productname, '' as title,'' as content, p.sellprice, p.consumerprice, p.brand, p.minimage, '' as img_file ,'' as regdt, '' as link_url,  '' as link_m_url ,li.section, 
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt
		FROM tblproduct p
		LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code WHERE p.display = 'Y' AND hotdealyn = 'N'  ";
		$check_sql .= " UNION ";
		$check_sql .= "SELECT 'instagram' as type, i.idx::varchar, '' as productcode, '' as productname, i.title ,i.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, i.img_file, i.regdt, i.link_url,  i.link_m_url ,li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblinstagram i
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code WHERE i.display = 'Y'";
		$check_sql .= " UNION ";
		$check_sql .= "SELECT 'storestory' as type, s.sno::varchar as idx, '' as productcode, '' as productname, s.title ,s.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, s.filename as img_file, s.regdt, '' as link_url,  '' as link_m_url ,li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND s.sno::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblstorestory s
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'storestory' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on s.sno::varchar = li.hott_code";
		$check_sql .= " UNION ";
/*		$check_sql .= "SELECT 'forum_list' as type, f.index::varchar as idx, '' as productcode, '' as productname, f.title ,f.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, f.img, f.writetime::varchar, '' as link_url, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND f.index::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblforumlist f
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on f.index::varchar = li.hott_code";
		$check_sql .= " UNION ";*/
		$check_sql .= "SELECT 'magazine' as type, m.no::varchar, '' as productcode, '' as productname, m.title ,m.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, m.img_file, m.regdt, m.link_url,  m.link_m_url ,li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
		FROM tblmagazine m
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code WHERE m.display = 'Y' AND m.type != 2 ";
		$check_sql .= " UNION ";
		$check_sql .= "SELECT 'lookbook' as type, l.no::varchar, '' as productcode, '' as productname, l.title ,l.content, 0 as sellprice, 0 as consumerprice, 0 as brand, '' as minimage, l.img_file, l.regdate as regdt,'' as link_url,  '' as link_m_url ,li.section,
		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
		FROM tbllookbook l
		LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code WHERE l.display = 'Y' ";
		$check_sql .= " ORDER BY  hott_cnt DESC";
		$check_sql .= " ) a";
		$check_sql .= " ) INFO WHERE ROWNUM > ".$rownum." ORDER BY ROWNUM  LIMIT 20";
	}
// 	exdebug($check_sql);
	$chk_result = pmysql_query($check_sql);
	while ( $chk_row = pmysql_fetch_array($chk_result) ) {
		$chk_rownum = $chk_row['rownum'];
	}
	
	if(count($arrLike) > 0){
		foreach( $arrLike as $key=>$val ){
			if($val['type'] == "product"){
				$imagepath=$Dir.DataDir."shopimages/product/";
?>
				<li class="grid-item">
					<figure>
					<?if($val['minimage']){ ?>
						<?if($type == "mobile"){?>
							<a href="<?=$pr_link.$val['productcode'] ?>"><img src="<?=$imagepath.$val['minimage']?>" alt=""></a>
						<?}else{ ?>
							<a href="javascript:prod_detail('<?=$val['productcode'] ?>');"><img src="<?=$imagepath.$val['minimage']?>" alt=""></a>
						<?} ?>		
					<?} ?>	
						<figcaption>
						<?if($type == "mobile"){?>
							<a href="<?=$pr_link.$val['productcode'] ?>">
						<?}else{ ?>
							<a href="javascript:prod_detail('<?=$val['productcode'] ?>');">
						<?} ?>
								<p class="title"><?=brand_name($val['brand'])."\n".$val['productname']?></p>
								 <?if($val['consumerprice'] != $val['sellprice']){ ?>
	                             <span class="price"><del><?=number_format($val['consumerprice']) ?></del><strong><?=number_format($val['sellprice']) ?></strong></span>
	                             <?}else{ ?>
	                             <span class="price"><strong><?=number_format($val['consumerprice']) ?></strong></span>
	                             <?} ?>
							</a>
							
						<button class="comp-like btn-like like_p<?=$val['productcode'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['productcode'] ?>','on','<?=$val['type']?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['productcode'] ?>"title="선택됨"><span class="like_pcount_<?=$val['productcode'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "instagram"){ 
				$imagepath = $Dir.DataDir."shopimages/instagram/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdt'], 0,8);
			?>
			
				<li>
					<figure>
						<?if($type == "mobile"){?>
						<!-- <a href="<?=$val['link_m_url'] ?>"><img src="<?=$posting_img?>" alt=""></a> -->
						<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$val['idx'] ?>"><img src="<?=$posting_img?>" alt=""></a>
						<?}else{ ?>
						<a href="javascript:detailView('<?=$val['idx'] ?>');" idx="<?=$val['idx'] ?>" class="btn-view-detail"><img src="<?=$posting_img?>" alt=""></a>
						<?} ?>
						<figcaption>
							<?if($type == "mobile"){?>
							<!-- <a href="<?=$val['link_m_url'] ?>"> -->
							<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$val['idx'] ?>">
							<?}else{ ?>
							<a href="javascript:detailView('<?=$val['idx'] ?>');" idx="<?=$val['idx'] ?>" class="btn-view-detail">
							<?} ?>
								<span class="category"><?=$reg_date?> / INSTAGRAM</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
							</a>
							
						<button class="comp-like btn-like like_i<?=$val['idx'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['idx'] ?>','on','<?=$val['type']?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['idx'] ?>"title="선택됨"><span class="like_icount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "storestory"){ 
				$imagepath = $Dir.DataDir."shopimages/store_story/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdt'], 0,8);

				$storyRow_content = stripslashes($val['content']);

				// <br>태그 제거
				$arrList = array("/<br\/>/", "/<br>/");
				$storyRow_content_tmp = trim(preg_replace($arrList, "", $storyRow_content));

				if ( !empty($storyRow_content_tmp) ) {
						$storyRow_content	= nl2br($storyRow_content);
						$storyRow_content	= str_replace("<p>","<div>",$storyRow_content);
						$storyRow_content	= str_replace("</p>","</div>",$storyRow_content);
				}
			?>
			
				<li>
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>store_story_view.php?sno=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<a href="javascript:stsDetailView('<?=$val['idx']?>','open_only');"><img src="<?=$posting_img?>" alt=""></a>
						<?}?>
						<figcaption>
							<a href="javascript:;">
								<span class="category"><?=$reg_date?> / STORE STORY</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($storyRow_content), 35)?></p>
							</a>
							
						<button class="comp-like btn-like like_s<?=$val['idx'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['idx'] ?>','on','<?=$val['type']?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['idx'] ?>"title="선택됨"><span class="like_scount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
			<?} else if($val['type'] == "forum_list"){ 
				$imagepath = $Dir.DataDir."shopimages/forum/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$content = $val['content'];
				$content = preg_replace('/\r\n|\r|\n/','',$content); //상품명 개행문자 제거
				$content = preg_replace("(\<(/?[^\>]+)\>)", "", $content); //상품명 태그 제거
			?>
				<li>
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>forum_view.php?index=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<!-- <a href="javascript:forumDetailView('<?=$val['idx']?>','open_only');"><img src="<?=$posting_img?>" alt=""></a> -->
						<a href="<?=$Dir.FrontDir?>forum_view.php?index=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a> 
						<?}?>
						<figcaption>
							<a href="javascript:;">
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / FORUM</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($content), 35)?></p>
							</a>
							
						<button class="comp-like btn-like like_f<?=$val['idx'] ?><?=$val['section']?' on':''?>" onclick="detailSaveLike('<?=$val['idx'] ?>','on','forum_list_mypage','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$val['idx'] ?>"title="선택됨"><span class="like_fcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt']?></span></button>
						</figcaption>
					</figure>
				</li>
			<?}else if($val['type'] == "magazine"){
				$imagepath = $Dir.DataDir."shopimages/magazine/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdt'], 0,8);
			?>	
				<li>
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>magazine_detail.php?no=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<a href="javascript:detail_magazine('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
						<?}?>
						<figcaption>
							<?if($type == "mobile"){?>
							<a href="<?=$Dir.MDir?>magazine_detail.php?no=<?=$val['idx']?>">
							<?}else{ ?>
							<a href="javascript:detail_magazine('<?=$val['idx']?>');">
							<?} ?>
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / MAGAZINE</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
							</a>
						<button class="like_m<?=$val['idx'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['idx'] ?>', '<?=$val['section']?'on':'off' ?>', 'magazine', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_mcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						</figcaption>
					</figure>
				</li>
			
			<?}else if($val['type'] == "lookbook"){
				$imagepath = $Dir.DataDir."shopimages/lookbook/";
				$posting_img = getProductImage($imagepath,$val['img_file']);
				$reg_date = substr($val['regdate'], 0,8);
			?>
				<li>
					<figure>
						<?if($type == "mobile"){?>
						<a href="<?=$Dir.MDir?>lookbook_detail.php?no=<?=$val['idx']?>"><img src="<?=$posting_img?>" alt=""></a>
						<?} else {?>
						<a href="javascript:detail_lookbook('<?=$val['idx']?>');"><img src="<?=$posting_img?>" alt=""></a> 
						<?}?>
						<figcaption>
							<?if($type == "mobile"){?>
							<a href="<?=$Dir.MDir?>lookbook_detail.php?no=<?=$val['idx']?>">
							<?}else{ ?>
							<a href="javascript:detail_lookbook('<?=$val['idx']?>');">
							<?} ?>
								<span class="category"><?=date("Ymd",strtotime($val['regdt']))?> / LOOKBOOK</span>
								<p class="title"><?=$val['title']?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($val['content']), 35)?></p>
							</a>
						<button class="like_l<?=$val['idx'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['idx'] ?>', '<?=$val['section']?'on':'off' ?>', 'lookbook', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_lcount_<?=$val['idx'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						</figcaption>
					</figure>
				</li>
			<?} ?>
			
		<?} ?>
		<input type="hidden" id="like_rownum" value="<?=$rownum ?>" />
		<input type="hidden" id="like_chknum" value="<?=$chk_rownum ?>" />
	<?}else{ ?>
	
	<?} ?>	
<?} //좋아요 Tab  ?>
