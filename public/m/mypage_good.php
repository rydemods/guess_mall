<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);

$sel_good_type	= $_GET['sel_good_type']?$_GET['sel_good_type']:'';
$section				= $sel_good_type;
?>
<script type="text/javascript">
<!--
$(document).ready(function() {
	$('.comp-posting').masonry({ 
		itemSelector: '.grid-item', 
		columnWidth: '.grid-sizer', 
		percentPosition: false 
	}); 
});

function goodChange(type) {
	location.href= "<?=$_SERVER['PHP_SELF']?>?sel_good_type="+type;
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
function gotoreload() {
	var type	= document.form2.sel_good_type.value;
	location.href= "<?=$_SERVER['PHP_SELF']?>?sel_good_type="+type;
}
//-->
</script>
<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sel_good_type value="<?=$sel_good_type?>">
</form>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>mypage.php" class="prev"></a>
			<span>좋아요</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
<?

if($section == "1") $section = "pdt";
elseif($section == "2") $section = "mgz";
elseif($section == "3") $section = "lbk";
elseif($section == "4") $section = "ins";
elseif($section == "5") $section = "frm";
elseif($section == "6") $section = "sts";
else $section = "all";

// exdebug($section);

$pdt_sql = "select  a.hno, a.section, a.regdt, b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand,
                    b.maximage, b.tinyimage, c.brandname, '' title, '' img_file, '' as content, a.section,
           			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND b.productcode = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblproduct b on a.hott_code = b.productcode
            join    tblproductbrand c on b.brand = c.bridx
            where   1=1
            and     a.section = 'product'
			and     b.display = 'Y'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
            ";

$ins_sql = "select  a.hno, a.section, a.regdt, b.idx::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND b.idx::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblinstagram b on a.hott_code = b.idx::varchar
            where   1=1
            and     a.section = 'instagram' 
            and     b.display = 'Y'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
            ";
            

$sts_sql = "select  a.hno, a.section, a.regdt, b.sno::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.filename as img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND b.sno::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblstorestory b on a.hott_code = b.sno::varchar
            where   1=1
            and     a.section = 'storestory'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
            ";

$mgz_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblmagazine b on a.hott_code = b.no::varchar
            where   1=1
            and     a.section = 'magazine'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
            ";

$frm_sql = "select  a.hno, a.section, a.regdt, b.index::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img as img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND b.index::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tblforumlist b on a.hott_code = b.index::varchar
            where   1=1
            and     a.section = 'forum_list'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
            ";
$lbk_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                    '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
            		COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
			from    tblhott_like a
            join    tbllookbook b on a.hott_code = b.no::varchar
            where   1=1
            and     a.section = 'lookbook'
            and     a.like_id = '".$_MShopInfo->getMemid()."'
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
        ";
//exdebug($sql);
$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql,get_db_conn());
?>
	<div class="mypage_good">
		<div class="wrap_select">
			<select class="select_def" name='sel_good_type' onChange="goodChange(this.value)">
				<option value=''<?if($sel_good_type==''){?> selected<?}?>>전체</option>
				<option value='1'<?if($sel_good_type=='1'){?> selected<?}?>>상품</option>
				<option value='2'<?if($sel_good_type=='2'){?> selected<?}?>>매거진</option>
				<option value='3'<?if($sel_good_type=='3'){?> selected<?}?>>룩북</option>
				<option value='4'<?if($sel_good_type=='4'){?> selected<?}?>>인스타그램</option>
				<option value='5'<?if($sel_good_type=='5'){?> selected<?}?>>포럼</option>
				<option value='6'<?if($sel_good_type=='6'){?> selected<?}?>>스토어 스토리</option>
			</select>
		</div>
<?
if ($t_count > 0) {
?>
		<div class="main-community-content on">
			<ul class="comp-posting">
				<li class="grid-sizer"></li>
<?
	while( $row = pmysql_fetch_object($result) ) {
		if($row->section == "product") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->maximage);
?>
				<li class="grid-item">
					<figure>
						<a href="<?=$Dir.MDir.'productdetail.php?productcode='.$row->productcode?>"><img src="<?=$p_img?>" alt=""></a>
						<figcaption>
							<a href="<?=$Dir.MDir.'productdetail.php?productcode='.$row->productcode?>">
								<span class="category"><?=$row->brandname?></span>
								<p class='title'><?=$row->productname?></p>
								<p class="desc"><?=number_format($row->sellprice)?>원</p>
							</a>
							<button class="comp-like btn-like like_p<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','<?=$row->brand ?>')" id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_pcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
						</figcaption>
					</figure>
				</li>
<?
		} else if($row->section == "instagram") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/instagram/',$row->img_file);
?>
				<li class="grid-item">
					<figure>
						<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$row->productcode ?>"><img src='<?=$p_img?>' alt=''></a>
						<figcaption>
							<a href="<?=$Dir.MDir?>instagram_view.php?ino=<?=$row->productcode ?>">
								<span class="category"><?=substr($row->regdt, 0, 8)?> / 인스타그램</span>
								<p class="title"><?=$row->title?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($row->content), 35)?></p>
							</a>
							<button class="comp-like btn-like like_i<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_icount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
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
				<li class="grid-item">
					<figure>
						<a href="<?=$Dir.MDir?>store_story_view.php?sno=<?=$row->productcode?>"><img src='<?=$p_img?>' alt=''></a>
						<figcaption>
							<a href="<?=$Dir.MDir?>store_story_view.php?sno=<?=$row->productcode?>">
								<span class="category"><?=substr($row->regdt, 0, 8)?> / STORE STORY</span>
								<p class="title"><?=$row->title?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($storyRow_content),35)?></p>
							</a>
							<button class="comp-like btn-like like_s<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_scount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
						</figcaption>
					</figure>
				</li>
<?
		} else if($row->section == "magazine") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/magazine/',$row->img_file);
?>
				<li class="grid-item">
					<figure>
						<a href="../m/magazine_detail.php?no=<?=$row->productcode ?>"><img src='<?=$p_img?>' alt=''></a>
						<figcaption>
							<a href="../m/magazine_detail.php?no=<?=$row->productcode ?>">
								<span class="category"><?=substr($row->regdt, 0, 8)?> / MAGAZINE</span>
								<p class="title"><?=$row->title?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($row->content), 35)?></p>
							</a>
							<button class="comp-like btn-like like_m<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_mcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
						</figcaption>
					</figure>
				</li>
<?
		} else if($row->section == "forum_list") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/forum/',$row->img_file);
?>
				<li class="grid-item">
					<figure>
						<a href="../m/forum_view.php?index=<?=$row->productcode ?>;"><img src='<?=$p_img?>' alt=''></a>
						<figcaption>
							<a href="../m/forum_view.php?index=<?=$row->productcode ?>;">
								<span class="category"><?=substr($row->regdt, 0, 8)?> / FORUM</span>
								<p class="title"><?=$row->title?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($row->content), 35)?></p>
							</a>
							<button class="comp-like btn-like like_f<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','forum_list_mypage','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_fcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
						</figcaption>
					</figure>
				</li>
<?
		} else if($row->section == "lookbook") {
			$p_img = getProductImage($Dir.DataDir.'shopimages/lookbook/',$row->img_file);
?>
				<li class="grid-item">
					<figure>
						<a href="../m/lookbook_detail.php?no=<?=$row->productcode ?>;"><img src='<?=$p_img?>' alt=''></a>
						<figcaption>
							<a href="../m/lookbook_detail.php?no=<?=$row->productcode ?>;">
								<span class="category"><?=substr($row->regdt, 0, 8)?> / LOOKBOOK</span>
								<p class="title"><?=$row->title?></p>
								<p class="desc"><?=strcutMbDot2(strip_tags($row->content), 35)?></p>
							</a>
							<button class="comp-like btn-like like_l<?=$row->productcode ?> on" onclick="detailSaveLike('<?=$row->productcode ?>','on','<?=$row->section ?>','<?=$_ShopInfo->getMemid()?>','')"  id="like_<?=$row->productcode ?>"title="선택됨"><span class="like_lcount_<?=$row->productcode ?>"><strong>좋아요</strong><?=$row->hott_cnt?></span></button>
						</figcaption>
					</figure>
				</li>
<?
		}
	}
?>
			</ul>
			<div class="list-paginate">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
			</div>
		</div>
<?
} else {
?>
		<!-- 내역 없는경우 -->
		<div class="none-ment margin">
			<p>좋아요 내역이 없습니다.</p>
		</div><!-- //내역 없는경우 -->


<?
}
?>

	</div><!-- //.mypage_good -->

<? include_once('outline/footer_m.php'); ?>