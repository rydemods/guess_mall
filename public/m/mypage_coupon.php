<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}

if($_data->coupon_ok!="Y") {
	alert_go('본 쇼핑몰에서는 쿠폰 기능을 지원하지 않습니다.',"./mypage.php");
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

#####날짜 셋팅 부분
$s_year=(int)$_GET["s_year"];
$s_month=(int)$_GET["s_month"];
$s_day=(int)$_GET["s_day"];

$e_year=(int)$_GET["e_year"];
$e_month=(int)$_GET["e_month"];
$e_day=(int)$_GET["e_day"];

$day_division = $_GET['day_division'];
if ($day_division == '') $day_division = '1MONTH';

$limitpage = $_GET['limitpage'];
$coupon_type = $_GET['coupon_type'];
if ($coupon_type == '') $coupon_type = 'online';

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);

$sql = "SELECT  issue.coupon_code, issue.id, issue.date_start, issue.date_end, 
				issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, 
				info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, 
				info.productcode, info.not_productcode, info.use_con_Type1, info.use_con_type2, info.description, 
				info.use_point, info.vender, info.delivery_type, info.coupon_use_type, 
				info.coupon_type, info.sale_max_money, info.coupon_is_mobile 
		FROM    tblcouponissue issue 
		JOIN    tblcouponinfo info ON info.coupon_code = issue.coupon_code 
		WHERE   issue.id = '".$_MShopInfo->getMemid()."' 
		AND     (issue.date_end >= '".date("YmdH")."' and issue.used = 'N') 
		AND     ( (issue.date_start <= '".str_replace( '-', '', $strDate1)."00' and issue.date_end >= '".str_replace( '-', '', $strDate1)."00') or (issue.date_start <= '".str_replace( '-', '', $strDate2)."23' and issue.date_end >= '".str_replace( '-', '', $strDate2)."23') or (issue.date_start >= '".str_replace( '-', '', $strDate1)."00' and issue.date_end <= '".str_replace( '-', '', $strDate2)."23')  ) 
		ORDER BY issue.date_end DESC, issue.ci_no desc
		";
$paging = new New_Templet_paging($sql, 5,  3, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//exdebug($sql);
?>
<script type="text/javascript">
<!--
function submitPaper() {
    var coupon_code	= document.coupon_code_form.coupon_code.value;
    if (coupon_code == '' || coupon_code.length < 16) {
        alert("쿠폰번호를 입력해 주시기 바랍니다.");
        document.coupon_code_form.coupon_code.focus();
        return;
    }
    var coupon_code1	= coupon_code.substr(0,4);
    var coupon_code2	= coupon_code.substr(4,4);
    var coupon_code3	= coupon_code.substr(8,4);
    var coupon_code4	= coupon_code.substr(12,4);

    var papercode	= coupon_code1+"-"+coupon_code2+"-"+coupon_code3+"-"+coupon_code4;
    //alert(papercode);return;

    $.post("<?=$Dir.FrontDir?>mypage_paper.ajax.php",{mode:"paper",papercode:papercode},function(data){
        if(data == '1'){
            alert("쿠폰이 발급 되었습니다.");
            location.href="mypage_coupon.php";
        }else if(data == '2'){
            alert("이미 사용한 쿠폰 번호입니다.");
        }else if(data == '3'){
            alert("해당하는 쿠폰이 없습니다.");
        }else if(data == '4'){
            alert("이미 발급된 쿠폰 입니다.");
        }else if(data == '5'){
            alert("같은 쿠폰의 사용하지 않은 쿠폰이 존재 합니다.");
        }else if(data == '0'){
			alert("쿠폰발행 시 에러발생 하였습니다 관리자님한테 연락하세요.");
		}
    });
}

function GoPage(block,gotopage) {
    document.form2.block.value=block;
    document.form2.gotopage.value=gotopage;
    document.form2.submit();
}

var NowTime=parseInt(<?=time()?>);
function GoSearch(gbn, obj) {

	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(s_date.getMonth()-9);
			break;
		case "12MONTH":
			s_date.setFullYear(s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form2.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form2.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//

    document.form2.submit();
}

function str_pad_right(num){
	
	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;
}
//-->
</script>

<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
<input type=hidden name=coupon_type value="<?=$coupon_type?>">
<input type="hidden" name="date1" id="" value="<?=$strDate1?>">
<input type="hidden" name="date2" id="" value="<?=$strDate2?>">
</form>


<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>쿠폰</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_coupon">
		<form name="coupon_code_form" action="<?=$_SERVER['PHP_SELF']?>">
		<div class="coupon_regist">
			<p>발급 받으신 쿠폰을 등록해주세요.</p>
			<div class="input_addr mt-10">
				<input type="text" name="coupon_code" placeholder="16자리 숫자만 입력">
				<div class="btn_addr"><a href="javascript:;" class="btn-point h-input" onClick="javascript:submitPaper();">쿠폰등록</a></div>
			</div>
		</div><!-- //.coupon_regist -->
		</form>

		<div class="check_period mt-15">
			<ul>
				<?
					if(!$day_division) $day_division = '1MONTH';

				?>
				<?foreach($arrSearchDate as $kk => $vv){?>
					<?
						$dayClassName = "";
						if($day_division != $kk){
							$dayClassName = '';
						}else{
							$dayClassName = 'on';
						}
					?>
					<li class="<?=$dayClassName?>"><a href="javascript:;" onClick = "GoSearch('<?=$kk?>', this)"><?=$vv?></a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
				<?}?>
			</ul>
		</div><!-- //.check_period -->
		<div class="list_coupon"><!-- [D] 5개 페이징 -->
			<ul>
<?
	$cnt=0;
	if ($t_count > 0) {
		while($row=pmysql_fetch_object($result)) {

			$code_a=substr($row->productcode,0,3);
			$code_b=substr($row->productcode,3,3);
			$code_c=substr($row->productcode,6,3);
			$code_d=substr($row->productcode,9,3);

			$prleng=strlen($row->productcode);
			$coupondate=date("YmdH");
			
			$couponcheck="";
			if($row->date_start>$coupondate || $row->date_end<$coupondate || $row->date_end==''){
				if($row->used=="Y"){
					$couponcheck="사용";
				}else{
					$couponcheck="사용불가";
				}
			}else if($row->used=="Y"){
				$couponcheck="사용";
			}else{
				$couponcheck="사용가능";
			}
			$likecode=$code_a;
			if($code_b!="000") $likecode.=$code_b;
			if($code_c!="000") $likecode.=$code_c;
			if($code_d!="000") $likecode.=$code_d;

			if($prleng==18) $productcode[$cnt]=$row->productcode;
			else $productcode[$cnt]=$likecode;

			if($row->sale_type<=2) {
				$dan="%";
			} else {
				$dan="원";
			}
			if($row->sale_type%2==0) {
				$sale = "할인";
			} else {
				$sale = "적립";
			}
			
			$product = "";
			if( $row->productcode=="ALL" ) {
				$product="전체상품";
			} else if( $row->productcode=="GOODS" ) {
				$product = "상품 ";
				$prSql = "SELECT cp.coupon_code, pr.productname, pr.brand FROM tblcouponproduct cp ";
				$prSql.= "JOIN tblproduct pr ON pr.productcode = cp.productcode WHERE cp.coupon_code = '".$row->coupon_code."' ";
				$prRes = pmysql_query( $prSql, get_db_conn() );
				$prCnt = 0;
				$prProd = array();
				$prBrand = "";
				while( $prRow = pmysql_fetch_object( $prRes ) ){
					if( $prCnt == 0 ) $product .= " [ ".$prRow->productname." ] ";

					list($prBrand) = pmysql_fetch("select brandname from tblproductbrand where bridx = ".$prRow->brand."");
					$prProd[] = "<em>[".$prBrand."]</em> ".$prRow->productname;
					$prCnt++;
				}
				if( $prCnt > 1 ) {
					$product .= '외 '.( $prCnt - 1 )."건";
					//$product = '<span class="line">'.$product.'</span>';
				}

			} else if( $row->not_productcode=="GOODS" ) {
				$product = "상품 ";
				$prSql = "SELECT cp.coupon_code, pr.productname, pr.brand FROM tblcouponproduct cp ";
				$prSql.= "JOIN tblproduct pr ON pr.productcode = cp.productcode WHERE cp.coupon_code = '".$row->coupon_code."' ";
				$prRes = pmysql_query( $prSql, get_db_conn() );
				$prCnt = 0;
				$prProd = array();
				$prBrand = "";
				while( $prRow = pmysql_fetch_object( $prRes ) ){
					if( $prCnt == 0 ) $product .= " [ ".$prRow->productname." ] ";

					list($prBrand) = pmysql_fetch("select brandname from tblproductbrand where bridx = ".$prRow->brand."");
					$prProd[] = "<em>[".$prBrand."]</em> ".$prRow->productname;
					$prCnt++;
				}
				if( $prCnt > 1 ) {
					$product .= '외 '.( $prCnt - 1 )."건 제외";
					//$product = '<span class="line">'.$product.'</span>';
				}

			} else if( $row->productcode=="CATEGORY" ){
				$product = "카테고리 ";
				$prSql = "SELECT pc.code_a, pc.code_b, pc.code_c, pc.code_d, pc.code_name, cc.categorycode  ";
				$prSql.= "FROM tblcouponcategory cc ";
				$prSql.= "JOIN tblproductcode pc ON ";
				$prSql.= " ( CASE ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 3 THEN ( pc.code_a = cc.categorycode AND pc.code_b = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 6 THEN ( pc.code_a||pc.code_b = cc.categorycode AND pc.code_c = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 9 THEN ( pc.code_a||pc.code_b||pc.code_c = cc.categorycode AND pc.code_d = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 12 THEN ( pc.code_a||pc.code_b||pc.code_c||pc.code_d = cc.categorycode ) ";
				$prSql.= " END ) ";
				$prSql.= "WHERE cc.coupon_code = '".$row->coupon_code."' ";
				$prSql.= "ORDER BY code_a, code_b, code_c, code_d , sort ";
				$prRes = pmysql_query( $prSql, get_db_conn() );
				$prCnt = 0;
				$prProd = array();
				while( $prRow = pmysql_fetch_object( $prRes ) ){
					if( $prCnt == 0 ) $product .= " [ ".$prRow->code_name." ] ";

					$_cate = implode(getCodeLoc3($prRow->categorycode)," > ");
					$prProd[] = $_cate;
					$prCnt++;
				}
				if( $prCnt > 1 ) {
					$product .= '외 '.( $prCnt - 1 )."건";
					//$product = '<span class="line">'.$product.'</span>';
				}
			} else if( $row->not_productcode=="CATEGORY" ){
				$product = "카테고리 ";
				$prSql = "SELECT pc.code_a, pc.code_b, pc.code_c, pc.code_d, pc.code_name, cc.categorycode ";
				$prSql.= "FROM tblcouponcategory cc ";
				$prSql.= "JOIN tblproductcode pc ON ";
				$prSql.= " ( CASE ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 3 THEN ( pc.code_a = cc.categorycode AND pc.code_b = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 6 THEN ( pc.code_a||pc.code_b = cc.categorycode AND pc.code_c = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 9 THEN ( pc.code_a||pc.code_b||pc.code_c = cc.categorycode AND pc.code_d = '000' ) ";
				$prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 12 THEN ( pc.code_a||pc.code_b||pc.code_c||pc.code_d = cc.categorycode ) ";
				$prSql.= " END ) ";
				$prSql.= "WHERE cc.coupon_code = '".$row->coupon_code."' ";
				$prSql.= "ORDER BY code_a, code_b, code_c, code_d , sort ";
				$prRes = pmysql_query( $prSql, get_db_conn() );
				$prCnt = 0;
				$prProd = array();
				while( $prRow = pmysql_fetch_object( $prRes ) ){
					if( $prCnt == 0 ) $product .= " [ ".$prRow->code_name." ] ";

					$_cate = implode(getCodeLoc3($prRow->categorycode)," > ");
					//exdebug($_cate);
					$prProd[] = $_cate;
					$prCnt++;
				}
				if( $prCnt > 1 ) {
					$product .= '외 '.( $prCnt - 1 )."건 제외";
					//$product = '<span class="line">'.$product.'</span>';
				}
			}

			$t = sscanf($row->date_start,'%4s%2s%2s%2s%2s%2s');
			$s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
			$t = sscanf($row->date_end,'%4s%2s%2s%2s%2s%2s');
			$e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");

			$date=date("Y.m.d H",$s_time)."시 ~ ".date("Y.m.d H",$e_time)."시";
?>
			<li>
				<div class="coupon_info">
					<div class="coupon_num">
						<span class="tit">쿠폰번호</span>
						<span><?=$row->coupon_code?></span>
						<span class="status point-color"><?=$couponcheck?></span>
					</div>
					<p class="period"><?=$date?></p>
					<p class="name"><?=$row->coupon_name?></p>
					<button type="button" class="target">적용대상: <?=$product?></button>
				</div>
				<?
				if(count($prProd) > 1) {
				?>
				<div class="target_more">
					<?
					for($i=0; $i<count($prProd);$i++) {
					?>
					<p><?=$prProd[$i]?></p>
					<?}?>
				</div>
				<?}?>
			</li>
			<?
		$cnt++;
		}
	} else {
?>
				<li>
					<div class="coupon_info">
					내역이 없습니다.
					</div>
				</li>
<?	}	?>
			</ul>
		</div><!-- //.list_coupon -->

		<div class="list-paginate mt-10">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div><!-- //.list-paginate -->

	</section><!-- //.mypage_coupon -->

</main>
<!-- //내용 -->



<? include_once('outline/footer_m.php'); ?>