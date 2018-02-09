<?php
/********************************************************************* 
// 파 일 명		: product_like_proc.php
// 설     명		: 상품 좋아요 proc
// 상세설명	: 해당 상품의 좋아요 등록, 해제 처리
// 작 성 자		: 2016-08-11 - daeyeob(김대엽)
// 
*********************************************************************/ 
?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.ap_point.php");

// 상품 코드 조회
$pram_prod_code = $_POST["code"];
$prod_code 		= getGroupProductcode($pram_prod_code);
$prodcode 		= getProdcode($pram_prod_code);

$like_type 	= $_POST["liketype"];
$section 	= $_POST["section"];
$section2 	= $_POST["section2"];
$page 		= $_POST["page"];
$brand 		= $_POST["brand"];
$date 		= date("YmdHis");
$like_point = $pointSet['like']['point'];

#포럼 마이페이지 좋아요에서 좋아요 클릭시 분기처리
$forum_type="list";
if($section=="forum_list_mypage"){
	$section="forum_list";
	$forum_type="mypage";
}

$arr_like = array(); // 좋아요 정보
$section_arr = array("instagram", "product", "storestory", "magazine", "lookbook", "forum_list_mypage", "forum_list");  // section 유형정의 2016-10-25
if(in_array($section, $section_arr)) $chk_section = 1;
else $chk_section = 0;

if($like_type == "off"){
    list($like_cnt) = pmysql_fetch("Select count(*) From tblhott_like Where like_id = '".$_ShopInfo->getMemid()."' And section = '".$section."' And hott_code = '".$prodcode."'");
    if($like_cnt == 0 && $chk_section) {
        $sql = "INSERT INTO tblhott_like (
                    like_id,
                    section,
                    hott_code,
                    regdt
                    )values(
                    '{$_ShopInfo->getMemid()}',
                    '{$section}',
                    '{$prodcode}',
                    '{$date}'
                )";
        pmysql_query($sql,get_db_conn());
        //insert_point_act($_ShopInfo->getMemid(), $like_point, "좋아요 포인트", "like_plus_point", $date, 0);
        //insert_point_act($_ShopInfo->getMemid(), $like_point, "좋아요 포인트", "like_plus_point_".$section, $date, $prod_code);

		$dddd="Select count(*) From tblhott_like Where like_id = '".$_ShopInfo->getMemid()."' And section = '".$section."' And hott_code = '".$prodcode."'";
    }
}else{
	$sql = "DELETE FROM tblhott_like WHERE like_id = '".$_ShopInfo->getMemid()."' AND hott_code = '".$prodcode."'";
	$dddd=$sql;
	pmysql_query($sql, get_db_conn());
	//insert_point_act($_ShopInfo->getMemid(), -$like_point, "좋아요 취소 포인트", "like_minus_point", $date, 0);
//    insert_point_act($_ShopInfo->getMemid(), -$like_point, "좋아요 취소 포인트", "like_minus_point_".$section, $date, $prod_code);
}


if($page =="mypage"){
	$section2    = "all";
	$pdt_sql = "select  a.hno, a.section, a.regdt, b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand,
                    b.tinyimage, c.brandname, '' title, '' img_file, '' as content, a.section,
           			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND b.productcode = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblproduct b on a.hott_code = b.productcode
            join    tblproductbrand c on b.brand = c.bridx
            where   1=1
            and     a.section = 'product'
            and     a.like_id = '".$_ShopInfo->getMemid()."'
            ";
	
	$ins_sql = "select  a.hno, a.section, a.regdt, b.idx::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND b.idx::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblinstagram b on a.hott_code = b.idx::varchar
            where   1=1
            and     a.section = 'instagram'
            and     a.like_id = '".$_ShopInfo->getMemid()."'
            ";
	
	$union_sql = "";
	if($section2 == "all") {
		$union_sql = "
                 ".$pdt_sql."
                 Union All
                 ".$ins_sql."
                 ";
	} elseif($section2 == "pdt") {
		$union_sql = "".$pdt_sql."";
	} elseif($section2 == "ins") {
		$union_sql = "".$ins_sql."";
	}
	
	$sql = "Select  z.*
        From
        (
            ".$union_sql."
        ) z
        Order by z.regdt desc
        Limit 8
        ";
	$result = pmysql_query($sql,get_db_conn());
	
	$html = "";
	$html .= "<ul class='comp-posting'>";
	while( $row = pmysql_fetch_object($result) ) {
		if($row->section == "product") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);
			$html .= "<li>
								<figure>
									<a href='javascript:void(0);'><img src='".$p_img."' alt=''></a>
									<figcaption>
										<a href='javascript:void(0);'>
											<span class='category'>".$row->brandname."</span>				
											<p class='title'>".$row->productname."</p>
											<p class='desc price'><strong>".number_format($row->sellprice)."원</strong></p>
										</a>
										<button class='comp-like btn-like on' onclick='saveLike(\"".$row->productcode."\",\"on\",\"".$section."\",\"".$section2."\",\"".$_ShopInfo->getMemid()."\",\"mypage\")' id='like_".$row->productcode." title='선택됨'><span><span><strong>좋아요</strong>".$row->hott_cnt."</span></button>	
									</figcaption>
								</figure>	
							</li>	 ";
		}else if($row->section == "instagram") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/instagram/',$row->img_file);
			$html .= "<li>
								<figure>
									<a href='javascript:void(0);'><img src='".$p_img."' alt=''></a>
									<figcaption>
										<a href='javascript:void(0);'>
											<span class='category'>".substr($row->regdt, 0, 8)."</span>
											<p class='title'>".$row->title."</p>
											<p class='desc'>".strcutMbDot(strip_tags($row->content), 60)."</p>
										</a>
										<button class='comp-like btn-like on' onclick='saveLike(\"".$row->productcode."\",\"on\",\"".$section."\",\"".$section2."\",\"".$_ShopInfo->getMemid()."\",\"mypage\")' id='like_".$row->productcode." title='선택됨'><span><span><strong>좋아요</strong>".$row->hott_cnt."</span></button>
									</figcaption>
								</figure>
							</li>	 ";
		}
	}
	$html .= "</ul>";
	echo $html;
}else if($page =="mypage_like"){
	$block      = $_GET['block'];
	$gotopage   = $_GET['gotopage'];
	$list_num   = 8;
	
	$pdt_sql = "select  a.hno, a.section, a.regdt, b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand,
                    b.tinyimage, c.brandname, '' title, '' img_file, '' as content, a.section,
           			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND b.productcode = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblproduct b on a.hott_code = b.productcode
            join    tblproductbrand c on b.brand = c.bridx
            where   1=1
            and     a.section = 'product'
            and     a.like_id = '".$_ShopInfo->getMemid()."'
            ";
	
	$ins_sql = "select  a.hno, a.section, a.regdt, b.idx::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND b.idx::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblinstagram b on a.hott_code = b.idx::varchar
            where   1=1
            and     a.section = 'instagram'
            and     a.like_id = '".$_ShopInfo->getMemid()."'
            ";
	
	$union_sql = "";
	if($section2 == "all") {
		$union_sql = "
                 ".$pdt_sql."
                 Union All
                 ".$ins_sql."
                 ";
	} elseif($section2 == "pdt") {
		$union_sql = "".$pdt_sql."";
	} elseif($section2 == "ins") {
		$union_sql = "".$ins_sql."";
	}
	
	$sql = "Select  z.*
        From
        (
            ".$union_sql."
        ) z
        Order by z.regdt desc
        ";
	
	$paging = new New_Templet_paging($sql,10,$list_num);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	
	$html = "";
	$html .= "<ul class='item-list comp-posting'>";
	while( $row = pmysql_fetch_object($result) ) {
		if($row->section == "product") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);
			$html .= "<li>
								<figure>
									<a href='javascript:void(0);'><img src='".$p_img."' alt=''></a>
									<figcaption>
										<a href='javascript:void(0);'>
											<span class='category'>".$row->brandname."</span>
											<p class='title'>".$row->productname."</p>
											<p class='desc price'><strong>".number_format($row->sellprice)."원</strong></p>
										</a>
										<button class='comp-like btn-like on' onclick='saveLike(\"".$row->productcode."\",\"on\",\"".$section."\",\"".$section2."\",\"".$_ShopInfo->getMemid()."\",\"mypage_like\")' id='like_".$row->productcode." title='선택됨'><span><span><strong>좋아요</strong>".$row->hott_cnt."</span></button>
									</figcaption>
								</figure>
							</li>	 ";
		}else if($row->section == "instagram") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/instagram/',$row->img_file);
			$html .= "<li>
								<figure>
									<a href='javascript:void(0);'><img src='".$p_img."' alt=''></a>
									<figcaption>
										<a href='javascript:void(0);'>
											<span class='category'>".substr($row->regdt, 0, 8)."</span>
											<p class='title'>".$row->title."</p>
											<p class='desc'>".strcutMbDot(strip_tags($row->content), 60)."</p>
										</a>
										<button class='comp-like btn-like on' onclick='saveLike(\"".$row->productcode."\",\"on\",\"".$section."\",\"".$section2."\",\"".$_ShopInfo->getMemid()."\",\"mypage_like\")' id='like_".$row->productcode." title='선택됨'><span><span><strong>좋아요</strong>".$row->hott_cnt."</span></button>
									</figcaption>
								</figure>
							</li>	 ";
		}
	}
	$html .= "</ul>";
	echo $html;
	
}else{
	
	// 좋아요 처리 후 결과 조회 위민트 170205
	if($section == "product"){
		$sql = "SELECT p.productcode, li.section,
							COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.prodcode = tl.hott_code),0) AS hott_cnt
				FROM tblproduct p
				LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' and hott_code != '' GROUP BY hott_code, section ) li on p.prodcode = li.hott_code";
		$sql .= " WHERE p.prodcode = '".$prodcode."' AND p.display = 'Y' limit 1";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$arr_like[] = array(
					'productcode'      => $row->productcode,
					'section'      => $row->section,
					'hott_cnt'      => $row->hott_cnt
			);
		}
		
	}else if($section == "instagram"){
		$sql = "SELECT i.*,li.section,
				COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar  = tl.hott_code),0) AS hott_cnt
				FROM tblinstagram i
				LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar  = li.hott_code
				WHERE i.idx  = '".$prod_code."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$count = pmysql_num_rows( $result );
		if($count > 0){
			while($row = pmysql_fetch_object( $result )){
				$arr_like[] = array(
						'idx'      => $row->idx,
						'section'      => $row->section,
						'hott_cnt'      => $row->hott_cnt
				);
			}
		}else{
			$arr_like[] = array(
					'idx'      => 0,
					'section'      => null,
					'hott_cnt'      => 0
			);
		}
	}else if($section == "storestory"){
		$sql = "SELECT i.*,li.section,
				COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND i.sno::varchar  = tl.hott_code),0) AS hott_cnt
				FROM tblstorestory i
				LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'storestory' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.sno::varchar  = li.hott_code
				WHERE i.sno  = '".$prod_code."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$count = pmysql_num_rows( $result );
		if($count > 0){
			while($row = pmysql_fetch_object( $result )){
				$arr_like[] = array(
						'idx'      => $row->idx,
						'section'      => $row->section,
						'hott_cnt'      => $row->hott_cnt
				);
			}
		}else{
			$arr_like[] = array(
					'idx'      => 0,
					'section'      => null,
					'hott_cnt'      => 0
			);
		}
	}else if($section == "forum_list"){//포럼 글 좋아요 추가 09 21 원재 ㅠㅠ

		#포럼 좋아요 클릭위치가 마이페이지 좋아요면 이쪽으로
		if($forum_type=="mypage"){
			$sql = "SELECT m.*, li.section,
				COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND m.index::varchar = tl.hott_code),0) AS hott_cnt
				FROM tblforumlist m ";
			$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'forum_list' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.index::varchar = li.hott_code ";
			$sql .= "WHERE m.index = '".$prod_code."' ";

			$result = pmysql_query( $sql, get_db_conn() );
			$count = pmysql_num_rows( $result );
			if($count > 0){
				while($row = pmysql_fetch_object( $result )){
					$arr_like[] = array(
							'idx'      => $row->index,
							'section'      => $row->section,
							'hott_cnt'      => $row->hott_cnt
					);
				}
			}else{
				$arr_like[] = array(
						'idx'      => 0,
						'section'      => null,
						'hott_cnt'      => 0
				);
			}
		}else{
			//좋아요 등록 또는 해제 후 본인이 해당 글 좋아요 체크 여부 및, 해당 글에 좋아요 개수 리턴합니다.
			$sql = "
				select count(*) as count ,
				( select like_id from tblhott_like where like_id = '{$_ShopInfo->memid}' AND section = 'forum_list' AND hott_code = '{$prod_code}' ) as chk_like 
				from tblhott_like li
				where li.section = 'forum_list' AND hott_code = '{$prod_code}'
			";
			$return_data = pmysql_fetch_object( pmysql_query($sql) );
			$arr_like[] = array(
				'chk_like'      => $return_data->chk_like,
				'like_count'      => $return_data->count
			);
		
		}
	}else if($section == "forum_list_request"){//포럼 글 좋아요 추가 09 21 원재 ㅠㅠ
		//좋아요 등록 또는 해제 후 본인이 해당 글 좋아요 체크 여부 및, 해당 글에 좋아요 개수 리턴합니다.
		$sql = "
			select count(*) as count ,
			( select like_id from tblhott_like where like_id = '{$_ShopInfo->memid}' AND section = 'forum_list_request' AND hott_code = '{$prod_code}' ) as chk_like 
			from tblhott_like li
			where li.section = 'forum_list_request' AND hott_code = '{$prod_code}'
		";
		$return_data = pmysql_fetch_object( pmysql_query($sql) );
		$arr_like[] = array(
			'chk_like'      => $return_data->chk_like,
			'like_count'      => $return_data->count
		);
	}else if($section == "magazine"){
		$sql = "SELECT m.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND m.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tblmagazine m ";
		$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'magazine' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on m.no::varchar = li.hott_code ";
		$sql .= "WHERE m.no = '".$prod_code."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$count = pmysql_num_rows( $result );
		if($count > 0){
			while($row = pmysql_fetch_object( $result )){
				$arr_like[] = array(
						'idx'      => $row->no,
						'section'      => $row->section,
						'hott_cnt'      => $row->hott_cnt
				);
			}
		}else{
			$arr_like[] = array(
					'idx'      => 0,
					'section'      => null,
					'hott_cnt'      => 0
			);
		}
	}else if($section == "lookbook"){
		$sql = "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
		$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
		$sql .= "WHERE l.no = '".$prod_code."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$count = pmysql_num_rows( $result );
		if($count > 0){
			while($row = pmysql_fetch_object( $result )){
				$arr_like[] = array(
						'idx'      => $row->no,
						'section'      => $row->section,
						'hott_cnt'      => $row->hott_cnt
				);
			}
		}else{
			$arr_like[] = array(
					'idx'      => 0,
					'section'      => null,
					'hott_cnt'      => 0
			);
		}
	}
	echo json_encode( $arr_like );
}
?>