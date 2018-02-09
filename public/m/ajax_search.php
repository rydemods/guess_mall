<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");


	$offset = $_POST['offsetLine']*2;
	$display = $_POST['display']*2;
	$search = urldecode($_POST['search_str']);
	$sort_val = $_REQUEST["sort_val"];
	$brand = $_REQUEST["brand"];

	$qry = "WHERE a.display='Y' AND (  a.mall_type = 0 OR a.mall_type = '".$_MShopInfo->getAffiliateType()."' ) "; // 해당 몰관련 상품만 보여줌 (2015.11.10 - 김재수)
	
	//검색어
	if($search){
	$brand = "";
	$qry.="AND ((UPPER(a.productname) LIKE '%{$search}%' OR UPPER(a.keyword) LIKE '%{$search}%') 
	OR a.productcode LIKE '{$search}%' 
	OR UPPER(a.production) LIKE '%{$search}%' 
	OR UPPER(a.model) LIKE '%{$search}%' 
	OR UPPER(a.selfcode) LIKE '%{$search}%' 
	OR UPPER(a.mdcomment) LIKE '%{$search}%' 
	OR UPPER(a.content) LIKE '%{$search}%') ";
	}

	//브랜드
	if($brand){
		$qry .= " AND a.brand = '{$brand}' ";
	}
	
	//번호, 사진, 상품명, 제조사, 가격
	$tmp_sort=explode("_",$sort_val);
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}
	
	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, ";
	if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
	$sql.= "a.maximage, a.minimage,a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
	$sql.= $addsortsql;
	
	$sql.= "FROM (select *, case when (buyprice - sellprice) <= 0 then 0 else (buyprice - sellprice) end as saleprice from tblproduct) AS a  ";
	
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";

	$sql.= $qry." ";

	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="rcnt") $sql.= "ORDER BY a.review_cnt ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="saleprice") $sql.= "ORDER BY a.saleprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
    // 등록일순으로 변경함. 만약 수정일순으로 변경한다면 modifydate desc 로 변경..2015-11-30 jhjeong
    elseif($tmp_sort[0]=="opendate") $sql.= "ORDER BY a.regdate DESC, pridx ASC ";
	elseif($tmp_sort[0]=="dcprice") $sql.= "ORDER BY case when consumerprice>0 then  100 - cast((cast(sellprice as float)/cast(consumerprice as float))*100 as integer) else 0 end desc ";	
	elseif($tmp_sort[0]=="best"){

		$sql.= "ORDER BY a.start_no desc ";
	}else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
			} else {
				$sql.= "ORDER BY opendate DESC ";
			}
		} elseif($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.start_no desc,a.productname ";
		} elseif($_cdata->sort=="production") {
			$sql.= "ORDER BY a.start_no desc,a.production ";
		} elseif($_cdata->sort=="price") {
			$sql.= "ORDER BY a.start_no desc,a.sellprice ";
		}
	}

	$res = pmysql_query($sql);
	$t_count = pmysql_num_rows($res);
	pmysql_free_result($res);

	$lineCnt = 0;
		//$sql.= " LIMIT {$display} OFFSET {$offset} ";
		$sql.= " LIMIT ".$display." OFFSET ".$offset;
		//echo $sql;
		$result=pmysql_query($sql,get_mdb_conn());
		$list_cnt = pmysql_num_rows($result);
?>
<?php if ($list_cnt) : ?>
<ul>
<?

		while($row=pmysql_fetch_object($result)){

			$sellprice = number_format($row->sellprice);
			$consumerprice = number_format($row->consumerprice);
			$temp = $row->option1;
			$tok = explode(",",$temp);
			$goods_count=count($tok);

			$check_optea='0';
			if($goods_count>"1"){
				$check_optea="1";
			}

			$optioncnt = explode(",",ltrim($row->option_quantity,','));
			$check_optout=array();
			$check_optin=array();
			for($gi=1;$gi<$goods_count;$gi++) {

				if(strlen($row->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
				else{  $check_optin[]='1';}
			}

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
			// 이미지 tinyimage => minimage로 변경 2015 11 09 유동혁
			if (strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
				// 이미지 오류로 변경 2015 11 09 유동혁
				$imgsrc = $Dir.DataDir."shopimages/product/".$row->minimage;
			}else{
				$imgsrc = $Dir."images/common/noimage.gif";
			}

			#장바구니를 위한 것들
			//장바구니를 위한 옵션

			if($row->option1){
				$opt1arr = explode(",",$row->option1);
				$opt1 = $opt1arr[1];
			}
			if($row->option2){
				$opt2arr = explode(",",$row->option2);
				$opt2 = $opt2arr[1];
			}

			## 품절 안된옵션중에 하나 가져와서 담아야 하기때문에 품절 안된것 조회 하여 옵션인덱스 셋팅 2014-08-25 12:12
			$calOpt1 = $calOpt2 = $resultOption1Val = $resultOption2Val = $resultOptionKey = 0;
			foreach($opt1arr as $opt1Key => $opt1Val){
				if($resultOption1Val) continue;
				if($opt1Key == 0) continue;
				$calOpt1 = $opt1Key-1;
				foreach($opt2arr as $opt2Key =>  $opt2Val){
					if($opt2Key == 0) continue;
					$calOpt2 = ($opt2Key-1)*10;
				}
				$resultOptionKey = $calOpt2 + $calOpt1;
				if($optioncnt[$resultOptionKey] > 0){
					$resultOption1Val = $opt1Key;
					$resultOption2Val = $opt2Key;
				}
			}

			//상품의 카테고리 코드
			$prd_cate_code = substr($row->productcode,0,12);

			$buy_price = ($row->buyprice)?$row->buyprice:"0";
			$sell_price = ($row->sellprice)?$row->sellprice:"0";
			$consumerprice = ($row->consumerprice)?$row->consumerprice:"0";
			$option_reserve = ($row->option_reserve)?$row->option_reserve:"0";

			if ($row->keyword) {
				$tags_tmp	=explode(",",$row->keyword);
				for($t=0;$t < sizeof($tags_tmp);$t++) {
					if ($tags_tmp[$t]) $tags[]	= $tags_tmp[$t];
				}
			}

		?>
			<li>
				<div class="goods_ico">
				<?=viewicon($row->etctype)?>
				</div>
				<div class="goods_wrap">
					<a href="productdetail.php?productcode=<?=$row->productcode?>">
						<img src="<?=$imgsrc?>" alt="">
						<div class="infobox">
							<span class="name"><?=$row->productname?></span>
							<div>
								<span class="pricebox" id="cupon_price">
									<p><label>정상가</label><del><?=$buy_price?>원</del></p>
									<p><label>최저가</label><del class="color"><?=$consumerprice?>원</del></p>
									<p class="last-price">
										<label>교육할인가</label>
										<strong class="member-only"><?if(strlen($_MShopInfo->getMemid())>0) {?><?=$sell_price?>원<?} else {?><img src="../images/common/ico_memberonly_sub.gif" alt="members only"><?}?></strong>
									</p>
								</span>
							</div>
						</div>
					</a>
				</div>
			</li>
		<?}?>
</ul>
<?php endif; ?>