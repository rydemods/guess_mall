<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$section    = "all";
$like_id    = $_ShopInfo->getMemid();
$list_num   = 8;

if($section == "1") $section = "pdt";
elseif($section == "2") $section = "mgz";
elseif($section == "3") $section = "lbk";
elseif($section == "4") $section = "ins";
elseif($section == "5") $section = "frm";
elseif($section == "6") $section = "sts";
else $section = "all";

//exdebug($section);

$pdt_sql = "select  a.hno, a.section, a.regdt, b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand,
                    b.maximage, b.tinyimage, c.brandname, '' title, '' img_file, '' as content, a.section,
					COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND b.productcode = tl.hott_code),0) AS hott_cnt
            from    tblhott_like a
            join    tblproduct b on a.hott_code = b.productcode
            join    tblproductbrand c on b.brand = c.bridx
            where   1=1
            and     a.section = 'product'
			and     b.display = 'Y'
            and     a.like_id = '".$like_id."'
            ";

$ins_sql = "select  a.hno, a.section, a.regdt, b.idx::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND b.idx::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblinstagram b on a.hott_code = b.idx::varchar
            where   1=1
            and     a.section = 'instagram'
			and     b.display = 'Y'
            and     a.like_id = '".$like_id."'
            ";

$sts_sql = "select  a.hno, a.section, a.regdt, b.sno::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.filename as img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND b.sno::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblstorestory b on a.hott_code = b.sno::varchar
            where   1=1
            and     a.section = 'storestory'
            and     a.like_id = '".$like_id."'
            ";

$mgz_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblmagazine b on a.hott_code = b.no::varchar
            where   1=1
            and     a.section = 'magazine'
			and     b.display = 'Y'
            and     a.like_id = '".$like_id."'
            ";

$frm_sql = "select  a.hno, a.section, a.regdt, b.index::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img as img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND b.index::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblforumlist b on a.hott_code = b.index::varchar
            where   1=1
            and     a.section = 'forum_list'
            and     a.like_id = '".$like_id."'
            ";
$lbk_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tbllookbook b on a.hott_code = b.no::varchar
            where   1=1
            and     a.section = 'lookbook'
			and     b.display = 'Y'
            and     a.like_id = '".$like_id."'
            ";


$union_sql = "";
if($section == "all") {
    $union_sql = "
                 ".$pdt_sql."
                 Union All
                 ".$ins_sql."
                 Union All
                 ".$sts_sql."
                 Union All
                 ".$mgz_sql."
				 Union All
                 ".$frm_sql."
				 Union All
                 ".$lbk_sql."
                 ";
} elseif($section == "pdt") {
    $union_sql = "".$pdt_sql."";
} elseif($section == "ins") {
    $union_sql = "".$ins_sql."";
} elseif($section == "sts") {
    $union_sql = "".$sts_sql."";
} elseif($section == "mgz") {
    $union_sql = "".$mgz_sql."";
} elseif($section == "frm") {
    $union_sql = "".$frm_sql."";
} elseif($section == "lbk") {
    $union_sql = "".$lbk_sql."";
}

$sql = "Select  z.*
        From
        (
            ".$union_sql."
        ) z
        Order by z.regdt desc
        Limit 8
        ";
// exdebug($sql);
$result = pmysql_query($sql,get_db_conn());
?>
							<ul class="comp-posting">
<?
while( $row = pmysql_fetch_object($result) ) {

    if($row->section == "product") {
        $p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->maximage);
?>
								<li>
									<figure>
										<a href="javascript:prod_detail('<?=$row->productcode?>');"><img src="<?=$p_img?>" alt=""></a>
										<figcaption>
											<a href="javascript:prod_detail('<?=$row->productcode?>');">
												<span class="category"><?=$row->brandname?></span>
												<p class="title"><?=$row->productname?></p>
												<p class="desc price"><strong><?=number_format($row->sellprice)?>원</strong></p>
											</a>
											<button class="comp-like btn-like like_p<?=$row->productcode ?><?=$row->section?' on':''?>" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','<?=$row->brand ?>')" id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_pcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
										</figcaption>
									</figure>
								</li>

<?
    } else if($row->section == "instagram") {
        $p_img = getProductImage($Dir.DataDir.'shopimages/instagram/',$row->img_file);
?>
								<li>
									<figure>
										<a href="javascript:detailView('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="btn-view-detail"><img src="<?=$p_img?>" alt=""></a>
										<figcaption>
											<a href="javascript:detailView('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="btn-view-detail">
												 <span class="category"><?=substr($row->regdt, 0, 8)?> /  INSTARGRAM</span>
												<p class="title"><?=$row->title?></p>
												<p class="desc"><?=strcutMbDot(strip_tags($row->content), 35)?></p>
											</a>
											<button class="comp-like btn-like like_i<?=$row->productcode ?><?=$row->section?' on':''?>" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_icount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
										</figcaption>
									</figure>
								</li>
<?
    } else if($row->section == "storestory") {
        $p_img = getProductImage($Dir.DataDir.'shopimages/store_story/',$row->img_file);

		$storyRow_content = stripslashes($row->content);

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
										<a href="javascript:stsDetailView('<?=$row->productcode?>','open_only');"><img src="<?=$p_img?>" alt=""></a>
										<figcaption>
											<a href="javascript:void(0);">
												 <span class="category"><?=substr($row->regdt, 0, 8)?> / STORE STORY</span>
												<p class="title"><?=$row->title?></p>
												<p class="desc"><?=strcutMbDot2(strip_tags($row->content), 35)?></p>
											</a>
											<button class="comp-like btn-like like_si<?=$row->productcode ?><?=$row->section?' on':''?>" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_scount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
										</figcaption>
									</figure>
								</li>
<?
    }else if($row->section == "magazine") {
    	$p_img = getProductImage($Dir.DataDir.'shopimages/magazine/',$row->img_file);
?>
							<li>
								<figure>
									<a href="javascript:detail_magazine('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="CLS_magazine"><img src='<?=$p_img?>' alt=''></a>
									<figcaption>
									<a href="javascript:detailView('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="btn-view-detail">
										<strong class='brand'><?=substr($row->regdt, 0, 8)?> / MAGAZINE</strong>
										<p class='title'><?=$row->title?></p>
										<span class='price'><strong><?=strcutMbDot2(strip_tags($row->content),35)?></strong></span>
									</a>
									<button class="comp-like btn-like like_m<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_mcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
									</figcaption>
								</figure>
							</li>
<?
    }else if($row->section == "forum_list") {
    	$p_img = getProductImage($Dir.DataDir.'shopimages/forum/',$row->img_file);
?>	
							<li>
								<figure>
									<a href="javascript:detail_forum('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="CLS_forum"><img src='<?=$p_img?>' alt=''></a>
									<figcaption>
									<a href="javascript:detailView('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="btn-view-detail">
										<strong class='brand'><?=substr($row->regdt, 0, 8)?> / FORUM</strong>
										<p class='title'><?=$row->title?></p>
										<span class='price'><strong><?=strcutMbDot2(strip_tags($row->content),35)?></strong></span>
									</a>
									
									<button class="comp-like btn-like like_f<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','forum_list_mypage','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_fcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
									
									
									</figcaption>
								</figure>
							</li>
<?
    }else if($row->section == "lookbook") {
    	$p_img = getProductImage($Dir.DataDir.'shopimages/lookbook/',$row->img_file);
?>
							<li>
								<figure>
									<a href="javascript:detail_lookbook('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="CLS_lookbook"><img src='<?=$p_img?>' alt=''></a>
									<figcaption>
									<a href="javascript:detailView('<?=$row->productcode ?>');" idx="<?=$row->productcode ?>" class="btn-view-detail">
										<strong class='brand'><?=substr($row->regdt, 0, 8)?> / LOOKBOOK</strong>
										<p class='title'><?=$row->title?></p>
										<span class='price'><strong><?=strcutMbDot2(strip_tags($row->content),35)?></strong></span>
									</a>
									
									<button class="comp-like btn-like like_l<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_lcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
									
									
									</figcaption>
								</figure>
							</li>
<?}
}
?>
							</ul>