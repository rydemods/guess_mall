<?php // hspark
// 페이퍼 쿠폰 생성
function create_coupon_number($sno, $num){
	$key = rand(11,55).sprintf('%010d',$sno).rand(55,99).sprintf('%010d',$num);
	$key = str_split($key,12);
	$key = strtoupper(base_convert($key[0], 10, 34).base_convert($key[1], 10, 34));
	$key = str_split($key,4);
	return $key;
}

$mode_text		= $mode=='modify'?"수정":"등록"; 
if($mode == 'modify'|| $mode == 'copy_insert') {
	$submit_type	= $mode;
} else {
	$submit_type	= 'insert';
}
$coupon_code	= $_REQUEST['coupon_code'];
$CurrentTime = time();
$date_start=$_POST["date_start"];		// 유효 기간 설정 사용가능 시작날짜
$date_end=$_POST["date_end"];		// 유효 기간 설정 사용가능 종료날짜
$date_start=$date_start?$date_start:date("Y-m-d",$CurrentTime);
$date_end=$date_end?$date_end:date("Y-m-d",$CurrentTime);

//생성제한 체크
list($coupon_made_limit)=pmysql_fetch_array(pmysql_query("select made_limit from tblcoupon "));

$type=$_POST["type"];
$coupon_type					= $_POST["coupon_type"];				// 쿠폰 발급 구분
$join_rote						= $_POST["join_rote"];						// 경로 (추가필드)

$sel_gubun					= $_POST["sel_gubun"];					// 쿠폰 발급 대상 구분 (추가필드)
$sel_group						= $_POST["sel_group"];					// 쿠폰 발급 대상 등급 (추가필드)
$issue_selmembers		= $_POST["issue_selmembers"];		// 쿠폰 발급 대상 - 선택 회원
$issue_excelmembers	= $_POST["issue_excelmembers"];	// 쿠폰 발급 대상 - 업로드 회원

$issue_days_ago			= $_POST["issue_days_ago"];			// 발급 시점 설정 - 시작 (추가필드)
$order_accept_quantity	= $_POST["order_accept_quantity"];	// 발급 충족 구매 수량 (추가필드)
$order_accept_price		= $_POST["order_accept_price"];		// 발급 충족 구매 금액 (추가필드)
$coupon_name				= $_POST["coupon_name"];				// 쿠폰 명
$description					= $_POST["description"];					// 쿠폰 설명
$sale2							= $_POST["sale2"];							// 금액/할인율 선택 - 원, %
$sale_money					= $_POST["sale_money"];				// 쿠폰 할인 금액/할인률
$sale_max_money			= $_POST["sale_max_money"];		// 할인 상한 금액
$amount_floor					= $_POST["amount_floor"];				// 금액 절삭
$one_issue_limit				= $_POST["one_issue_limit"];			// 1회 발급 수량 (추가필드)
$one_issue_quantity		= $_POST["one_issue_quantity"];		// 1회 발급 설정수량 (추가필드)
$coupon_use_type			= $_POST["coupon_use_type"];		// 쿠폰 사용 방법
$coupon_is_mobile			= $_POST["coupon_is_mobile"];		// 쿠폰 사용 범위
$mini_type						= $_POST["mini_type"];					// 쿠폰사용제한 선택 (추가필드)
$mini_price					= $_POST["mini_price"];					// 쿠폰사용제한 구매 금액
$mini_quantity				= $_POST["mini_quantity"];				// 쿠폰사용제한 상품 수량 (추가필드)
$time								= $_POST["time"];							// 유효 기간 설정 선택
$peorid							= $_POST["peorid"];						// 유효 기간 사용가능 일
$productcode					= $_POST["productcode"];				// 카테고리/상품 선택 구분 - ALL : 전체 / CATEGORY : 카테고리 / GOODS : 상품 / BRANDSEASONS : 브랜드시즌
$set_productcode			= $_POST["set_productcode"];			// 카테고리/상품/브랜드시즌 리스트
$issue_type					= $_POST["issue_type"];					// 쿠폰 발급조건 - 회원가입시 자동발급 : M / 자동발급 : A
$detail_auto					= $_POST["detail_auto"];					// 제품 상세 쿠폰 노출 설정 - 노출안함으로 고정
$issue_code					= $_POST["issue_code"];				// 발급구분 - 0 : 일반, 1 : 지정, 2 : 자동

$issue_max_no				= $_POST["issue_max_no"];			// 발행 쿠폰 수량 설정 - 페이퍼 쿠폰 (추가필드)

$imagepath					= $Dir.DataDir."shopimages/etc/";		// 제품 상세 쿠폰 직접 업로드 이미지 저장 URL
$display_img_type			= $_POST["display_img_type"];		// 제품 상세 쿠폰 노출 이미지 사용여부 (추가필드)
$display_img					= $_FILES["display_img"];				// 제품 상세 쿠폰 직접 업로드 이미지 (추가필드)
$old_display_img			= $_POST["old_display_img"];			// 제품 상세 쿠폰 직접 업로드 이전 이미지 (추가)


//--------------------------------------------- 고정값 필드 시작 ---------------------------------------------//
$sale_type						= $_POST["sale_type"];					// 쿠폰 종류 - 할인쿠폰으로 고정
$use_con_type2				= $_POST["use_con_type2"];			// 카테고리/상품 포함/제외 여부 - 제외로 고정
//--------------------------------------------- 고정값 필드 끝 ---------------------------------------------//

//--------------------------------------------- 사용 안하는 필드 시작 ---------------------------------------------//
$bank_only				= $_POST["bank_only"];							// 쿠폰사용가능 결제방법 - 제한 없음으로 고정
$use_con_type1		= $_POST["use_con_type1"];					// 다른쿠폰과 같이사용 유무 - 같이 사용가능으로 고정
$issue_tot_no			= $_POST["issue_tot_no"];						// 총 발행 쿠폰 수 - 무제한으로 고정
$repeat_id				= $_POST["repeat_id"];							// 동일인 재발급 가능여부 - 가능으로 고정
$issue_member_no	= $_POST["issue_member_no"];				// 보유가능 쿠폰 수 - NULL로 고정
$use_point				= $_POST["use_point"];							// 쿠폰과 등급회원 할인/적립 혜택 동시적용 유무 - 동시적용으로 고정
$delivery_type		= $_POST["delivery_type"];						// 쿠폰 사용시 배송비 포함 유무 - 미포함으로 고정
$use_card				= $_POST['use_card'];							// 사용카드에 따른 유무 - NULL로 고정
//--------------------------------------------- 사용 안하는 필드 끝 ---------------------------------------------//


if ($type=="insert" || $type=="modify" || $type=="copy_insert") {
	//exdebug($_POST);
	//exit;
	if ($coupon_made_limit == '1' && $coupon_use_type == '2') echo "<script>alert('장바구니 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";
	if ($coupon_made_limit == '2' && $coupon_use_type == '1') echo "<script>alert('상품 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";

	if ($type!="modify") $coupon_code=substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
	if(ord($issue_days_ago)==0) $issue_days_ago=0;
	if(ord($order_accept_quantity)==0) $order_accept_quantity=0;
	if(ord($order_accept_price)==0) $order_accept_price=0;
	if($sale_type=="+" && $sale2=="%") $realsale=1;				// % 적립 (사용안함)
	else if($sale_type=="-" && $sale2=="%") $realsale=2;		// % 할인
	else if($sale_type=="+" && $sale2=="원") $realsale=3;		// 원 적립 (사용안함)
	else if($sale_type=="-" && $sale2=="원") $realsale=4;		// 원 할인
	if(ord($sale_money)==0) $sale_money=0;
	if(ord($sale_max_money)==0) $sale_max_money=0;
	if(ord($one_issue_limit)==0) $one_issue_limit=0;
	if(ord($one_issue_quantity)==0) $one_issue_quantity=0;
	if(ord($mini_price)==0) $mini_price=0;
	if(ord($mini_quantity)==0) $mini_quantity=0;
	if(ord($use_con_type1)==0 || $productcode=="ALL") $use_con_type1="N";
	if(ord($use_con_type2)==0 || $productcode=="ALL") $use_con_type2="Y";
	if(ord($repeat_id)==0) $repeat_id="N";
	if(ord($issue_tot_no)==0) $issue_tot_no=0;
	if(ord($issue_member_no)==0) $issue_member_no=1;
	if ($time=="D") {
		$date_start = str_replace("-","",$date_start)."00";
		$date_end = str_replace("-","",$date_end)."23";
	} else {
		$date_start = ($peorid>0?"-":"").$peorid;
		$date_end = str_replace("-","",$date_end)."23";
	}
	
	if(ord($issue_max_no)==0) $issue_max_no=0;

	if ($display_img_type == '2') { // 직접 업로드시
		if ($type=="copy_insert") {
			if(ord($old_display_img) && file_exists($imagepath.$old_display_img)) {
				$odi_arr	= explode(".",$old_display_img);
				$copy_display_img	= "COUPON_{$coupon_code}.".$odi_arr[1];
				copy($imagepath.$old_display_img, $imagepath.$copy_display_img);
				$old_display_img	= $copy_display_img;
			}
		}
		if($display_img['size'] < 1048576) {
			if (ord($display_img['name']) && file_exists($display_img['tmp_name'])) {
				$ext = strtolower(pathinfo($display_img['name'],PATHINFO_EXTENSION));
				if (in_array($ext, array('png','jpg','gif'))) {
					if ($type=="modify") {
						if(ord($old_display_img) && file_exists($imagepath.$old_display_img)) {
							unlink($imagepath.$old_display_img);
						}
					}

					$display_img_name = "COUPON_{$coupon_code}.".$ext;
					move_uploaded_file($display_img['tmp_name'],$imagepath.$display_img_name);
					chmod($imagepath.$display_img_name,0666);
				} else {
					echo "<script>alert('쿠폰 이미지 파일(확장자가 png, jpg, gif)만 등록 가능합니다.');</script>";
					exit;
				}
			} else {
				if ($type=="modify" || $type=="copy_insert") {
					$display_img_name = $old_display_img;
				} else {
					$display_img_name = "";
				}
			}
		} else {
			echo "<script>alert('쿠폰 이미지 파일 용량이 초과되었습니다.\\n\\n쿠폰 이미지 파일 1MB 이하로 올려주시기 바랍니다.');</script>";
			exit;
		}
		if ($type=="copy_insert") {
			if(ord($old_display_img) && file_exists($imagepath.$old_display_img)) {
				$display_img_name	= $copy_display_img;
			}
		}
	} else {
		if ($type=="modify") {
			if(ord($old_display_img) && file_exists($imagepath.$old_display_img)) {
				unlink($imagepath.$old_display_img);
			}
		}
		$display_img_name = "";
	}

	
	if ($use_con_type2 == 'N') { // 제외
		$in_productcode		= "";
		$out_productcode	= $productcode;
		$set_type				= "0";
	} else if ($use_con_type2 == 'Y') { //포함
		$in_productcode		= $productcode;
		$out_productcode	= "";
		$set_type				= "1";
	}
	
	if ($type!="modify") {
		$sql = "INSERT INTO tblcouponinfo(
		coupon_code	,
		coupon_name	,
		coupon_use_type ,
		coupon_type ,
		date_start	,
		date_end	,
		sale_type	,
		sale_money	,
		amount_floor	,
		mini_price	,
		sale_max_money,
		bank_only	,
		productcode	,
		not_productcode	,
		use_con_type1	,
		use_con_type2	,
		issue_type	,
		detail_auto	,
		issue_tot_no	,
		issue_member_no,
		repeat_id	,
		description	,
		use_point	,
		member		,
		display		,
		date,
		delivery_type,
		use_card,
		coupon_is_mobile,
		issue_code,
		join_rote,
		issue_days_ago,
		order_accept_quantity,
		order_accept_price,
		one_issue_limit,
		one_issue_quantity,
		mini_type,
		mini_quantity,
		sel_gubun,
		sel_group,
		issue_max_no,
		display_img_type,
		display_img,
		issue_status,
		time_type) VALUES (
		'{$coupon_code}',
		'{$coupon_name}',
		'{$coupon_use_type}',
		'{$coupon_type}',
		'{$date_start}',
		'{$date_end}',
		'{$realsale}',
		'{$sale_money}',
		'{$amount_floor}',
		{$mini_price},
		'{$sale_max_money}',
		'{$bank_only}',
		'{$in_productcode}',
		'{$out_productcode}',
		'{$use_con_type1}',
		'{$use_con_type2}',
		'{$issue_type}',
		'{$detail_auto}',
		{$issue_tot_no},
		{$issue_member_no},
		'{$repeat_id}',
		'{$description}',
		'{$use_point}',
		'".($issue_type!="N"?"ALL":"")."',
		'".($issue_type!="N"?"Y":"N")."',
		'".date("YmdHis")."',
		'{$delivery_type}',
		'{$use_card}',
		'{$coupon_is_mobile}',
		'{$issue_code}',
		'{$join_rote}',
		'{$issue_days_ago}',
		'{$order_accept_quantity}',
		'{$order_accept_price}',
		'{$one_issue_limit}',
		'{$one_issue_quantity}',
		'{$mini_type}',
		'{$mini_quantity}',
		'{$sel_gubun}',
		'{$sel_group}',
		'{$issue_max_no}',
		'{$display_img_type}',
		'{$display_img_name}',
		'R',
		'{$time}')";
	} else {
		$sql = "UPDATE tblcouponinfo SET ";
		$sql.= "coupon_name				= '{$coupon_name}' ,";
		$sql.= "coupon_use_type			= '{$coupon_use_type}' ,";
		$sql.= "coupon_type				= '{$coupon_type}' ,";
		$sql.= "date_start						= '{$date_start}' ,";
		$sql.= "date_end						= '{$date_end}' ,";
		$sql.= "sale_type						= '{$realsale}' ,";
		$sql.= "sale_money					= '{$sale_money}' ,";
		$sql.= "amount_floor				= '{$amount_floor}' ,";
		$sql.= "mini_price					= {$mini_price} ,";
		$sql.= "sale_max_money			= '{$sale_max_money}' ,";
		$sql.= "bank_only					= '{$bank_only}' ,";
		$sql.= "productcode					= '{$in_productcode}' ,";
		$sql.= "not_productcode			= '{$out_productcode}' ,";
		$sql.= "use_con_type1				= '{$use_con_type1}' ,";
		$sql.= "use_con_type2				= '{$use_con_type2}' ,";
		$sql.= "issue_type					= '{$issue_type}' ,";
		$sql.= "detail_auto					= '{$detail_auto}' ,";
		$sql.= "issue_tot_no					= {$issue_tot_no} ,";
		$sql.= "issue_member_no		= {$issue_member_no} ,";
		$sql.= "repeat_id						= '{$repeat_id}' ,";
		$sql.= "description					= '{$description}' ,";
		$sql.= "use_point						= '{$use_point}' ,";
		$sql.= "member						= '".($issue_type!="N"?"ALL":"")."' ,";
		$sql.= "display						= '".($issue_type!="N"?"Y":"N")."' ,";
		$sql.= "delivery_type				= '{$delivery_type}' ,";
		$sql.= "use_card						= '{$use_card}' ,";
		$sql.= "coupon_is_mobile			= '{$coupon_is_mobile}' ,";
		$sql.= "issue_code					= '{$issue_code}' ,";
		$sql.= "join_rote						= '{$join_rote}' ,";
		$sql.= "issue_days_ago			= '{$issue_days_ago}' ,";
		$sql.= "order_accept_quantity	= '{$order_accept_quantity}' ,";
		$sql.= "order_accept_price		= '{$order_accept_price}' ,";
		$sql.= "one_issue_limit			= '{$one_issue_limit}' ,";
		$sql.= "one_issue_quantity		= '{$one_issue_quantity}' ,";
		$sql.= "mini_type						= '{$mini_type}' ,";
		$sql.= "mini_quantity				= '{$mini_quantity}' ,";
		$sql.= "sel_gubun					= '{$sel_gubun}' ,";
		$sql.= "sel_group					= '{$sel_group}' ,";
		$sql.= "issue_max_no				= '{$issue_max_no}' ,";
		$sql.= "display_img_type			= '{$display_img_type}' ,";
		$sql.= "display_img					= '{$display_img_name}' ,";
		$sql.= "time_type						= '{$time}' WHERE coupon_code='{$coupon_code}'";
		
	}

	pmysql_query($sql,get_db_conn());

	if ($type=="modify") {
		pmysql_query("DELETE FROM tblcouponproduct WHERE coupon_code = '{$coupon_code}'", get_db_conn());
		pmysql_query("DELETE FROM tblcouponcategory WHERE coupon_code = '{$coupon_code}'", get_db_conn());
		pmysql_query("DELETE FROM tblcouponbrandseason WHERE coupon_code = '{$coupon_code}'", get_db_conn());
		pmysql_query("DELETE FROM tblcouponissue_standby WHERE coupon_code = '{$coupon_code}' ", get_db_conn());
		pmysql_query("DELETE FROM tblcouponpaper WHERE coupon_code = '{$coupon_code}' ", get_db_conn());
	}

	// 쿠폰 발급 대상 회원 등록
	if ($sel_gubun == 'M' || $sel_gubun == 'E') {
		if ($sel_gubun == 'M') {
			$issue_members	= explode(",", $issue_selmembers);
		} else  if ($sel_gubun == 'E') {
			$issue_members	= explode(",", $issue_excelmembers);
		}

		foreach($issue_members as $v){
			pmysql_query("INSERT INTO tblcouponissue_standby (coupon_code, id) VALUES ('{$coupon_code}', '{$v}')", get_db_conn());
			//echo "INSERT INTO tblcouponissue_standby (coupon_code, id) VALUES ('{$coupon_code}', '{$v}')<br>";
		}
	}

	// 카테고리/상품 등록
	if(is_array($set_productcode)) $set_productcode = array_unique($set_productcode);

	if($productcode == 'CATEGORY'){
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponcategory (coupon_code, categorycode, type) VALUES ('{$coupon_code}', '{$v}','{$set_type}')", get_db_conn());
		}
	}else if($productcode == 'GOODS'){
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponproduct (coupon_code, productcode, type) VALUES ('{$coupon_code}', '{$v}','{$set_type}')", get_db_conn());
		}
	}else if($productcode == 'BRANDSEASONS'){
		foreach($set_productcode as $v){
			$v_arr	= explode("|", $v);

			$v_bridx				= $v_arr[0];
			$v_vender			= $v_arr[1];
			$v_season_year	= $v_arr[2];
			$v_season			= $v_arr[3];
			pmysql_query("INSERT INTO tblcouponbrandseason (coupon_code, bridx, vender, season_year, season, type) VALUES ('{$coupon_code}', '{$v_bridx}', '{$v_vender}', '{$v_season_year}', '{$v_season}','{$set_type}')", get_db_conn());
		}
	}

	if($coupon_type == '7'){ // 페이퍼 쿠폰 생성
		for($i=1;$i<=$_POST['issue_max_no'];$i++){
			$paperNum = create_coupon_number($coupon_code, $i.rand(0,1000));
			$paperNum = implode('-',$paperNum);
			pmysql_query("INSERT INTO tblcouponpaper (coupon_code, papercode) VALUES ('{$coupon_code}', '{$paperNum}')", get_db_conn());
		}
	}

	//echo "<body onload=\"location.href='market_couponlist_v3.php';\"></body>";
	//exit;	

	if(!pmysql_errno()) {	
		if ($type=="modify") {
			echo "<script>alert('쿠폰 {$mode_text}이 완료 되었습니다.'); parent.document.listform.submit();</script>";
		} else {
			if ($coupon_type_check	== 'auto') $par_loc	= "market_couponautolist_v3.php";
			if ($coupon_type_check	== 'normal') $par_loc	= "market_couponnewlist_v3.php";
			echo "<script>alert('쿠폰 {$mode_text}이 완료 되었습니다.'); parent.location.href='{$par_loc}';</script>";
		}
		exit;
	} else {		
		echo "<script>alert('쿠폰 {$mode_text}중 오류가 발생하였습니다.');</script>";
		exit;
	}
}
//금액 절삭
list($amount_floor)=pmysql_fetch_array(pmysql_query("select amount_floor from tblcoupon "));

if (ord($mode) && ord($coupon_code)) {
	$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
	echo $sql;
	$result = pmysql_query($sql,get_db_conn());
	if(!$cp_row=pmysql_fetch_object($result)) {
		alert_go("해당 쿠폰 정보가 존재하지 않습니다.",-1);
	}
	pmysql_free_result($result);

	$cp_coupon_type	= $cp_row->coupon_type;
	$selected[coupon_type][$cp_coupon_type]	= "selected";
	$cp_productcode	= $cp_row->productcode;
	$cp_issue_type		= $cp_row->issue_type;
	$cp_issue_code		= $cp_row->issue_code;

	// 구매 수량 충족
	if($cp_row->coupon_type == '12') $cp_order_accept_quantity	= $cp_row->order_accept_quantity;

	// 구매 금액 충족
	if($cp_row->coupon_type == '13') $cp_order_accept_price	= $cp_row->order_accept_price;

	// 쿠폰 발급 대상
	if($cp_row->coupon_type == '1' || $cp_row->coupon_type == '9' || $cp_row->coupon_type == '15'){
		$cp_sel_gubun	= $cp_row->sel_gubun;
		$selected[sel_gubun][$cp_sel_gubun]	= "selected";
		if ($cp_row->sel_gubun == 'A') $cp_issue_members_html = "전체회원";
		if ($cp_row->sel_gubun == 'G') {
			$cp_sel_group	= $cp_row->sel_group;
			$selected[sel_group][$cp_sel_group]	= "selected";
			list($cp_issue_members_html)=pmysql_fetch_array(pmysql_query("SELECT group_name FROM tblmembergroup where group_code='{$cp_sel_group}'"));
		}
		if($cp_row->sel_gubun == 'M' || $cp_row->sel_gubun == 'E'){			
			$im_sql = "SELECT id FROM tblcouponissue_standby where coupon_code='{$coupon_code}' order by cis_no ";
			$im_result = pmysql_query($im_sql,get_db_conn());
			$im_count=0;
			$_im=array();
			$cp_issue_members	= "";
			while($im_row=pmysql_fetch_object($im_result)) {
				if ($im_count > 0) $cp_issue_members .= ",";
				$cp_issue_members	.= $im_row->id;
				if ($im_count > 0) $cim_br	="<br>";
				$_im[] = $cim_br." <img src='img/icon/table_bull.gif'> ".$im_row->id;
				$im_count++;
			}
			$cp_issue_members_html = implode("",$_im);
			pmysql_free_result($im_result);
			if ($cp_row->sel_gubun == 'M') $cp_issue_selmembers	= $cp_issue_members;
			if ($cp_row->sel_gubun == 'E') $cp_issue_excelmembers	= $cp_issue_members;
		}
	}
	if($cp_row->coupon_type == '3' || $cp_row->coupon_type == '10'){
		// 발급 시점
		$cp_issue_days_ago	= $cp_row->issue_days_ago;
	} else {
		// 경로
		$cp_join_rote	= $cp_row->join_rote;
		$checked[join_rote][$cp_join_rote]	= "checked";
	}

	//쿠폰 명
	$cp_coupon_name	= $cp_row->coupon_name;

	//쿠폰 설명
	$cp_description	= $cp_row->description;

	// 혜택
	if($cp_row->sale_type<=2) $cp_sale2="%";
	else $cp_sale2="원";
	$selected[sale2][$cp_sale2]	= "selected";

	$cp_sale_money			= $cp_row->sale_money;
	$cp_sale_max_money	= $cp_row->sale_max_money;
	
	//1회 발급 수량
	if($cp_row->coupon_type != '7') {
		$cp_one_issue_limit		= $cp_row->one_issue_limit;
		$checked[one_issue_limit][$cp_one_issue_limit]	= "checked";
		$cp_one_issue_quantity	= $cp_row->one_issue_quantity;
	}

	// 쿠폰 사용 방법
	$cp_coupon_use_type	= $cp_row->coupon_use_type;
	$checked[coupon_use_type][$cp_coupon_use_type]	= "checked";

	// 쿠폰 사용 범위
	$cp_coupon_is_mobile	= $cp_row->coupon_is_mobile;
	$checked[coupon_is_mobile][$cp_coupon_is_mobile]	= "checked";

	//사용 제한
	$cp_mini_type		= $cp_row->mini_type;
	$checked[mini_type][$cp_mini_type]	= "checked";
	if ($cp_row->mini_type == 'P') {
		$cp_mini_price		= $cp_row->mini_price;
	} else if ($cp_row->mini_type == 'Q') {
		$cp_mini_quantity	= $cp_row->mini_quantity;
	}
		
	//유효 기간
	$cp_time	= $cp_row->time_type;
	$selected[time_type][$cp_time]	= "selected";
	if($cp_row->date_start>0) {
		$cp_date_start	=substr($cp_row->date_start,0,4)."-".substr($cp_row->date_start,4,2)."-".substr($cp_row->date_start,6,2);
		$cp_date_end	= substr($cp_row->date_end,0,4)."-".substr($cp_row->date_end,4,2)."-".substr($cp_row->date_end,6,2);
	} else {
		$cp_peorid	= $cp_row->date_start;
		$cp_date_end	= substr($cp_row->date_end,0,4)."-".substr($cp_row->date_end,4,2)."-".substr($cp_row->date_end,6,2);
	}

	//제외/포함 카테고리 또는 상품
	$cp_use_con_type2	= $cp_row->use_con_type2;

	if($cp_row->use_con_type2=="Y") {
		$cp_productcode	= $cp_row->productcode;
	} else if($cp_row->use_con_type2=="N") {					
		$cp_productcode	= $cp_row->not_productcode;
	}
	
	$checked[couponaccept][$cp_productcode]	= "checked";

	$prleng=strlen($cp_productcode);
	if($cp_productcode=="ALL") {
		$cp_product="없음";
	} else if($cp_productcode == "CATEGORY") {
		$sqlCate = "SELECT categorycode FROM tblcouponcategory WHERE coupon_code = '{$coupon_code}'";
		$resultCate = pmysql_query($sqlCate,get_db_conn());
		$__=array();
		while($rowCate = pmysql_fetch_object($resultCate)) {
			$sql2 = "SELECT code_name as product FROM tblproductcode WHERE code_a='".substr($rowCate->categorycode,0,3)."' ";
			if(substr($rowCate->categorycode,3,3)!="000") {
				$sql2.= "AND (code_b='".substr($rowCate->categorycode,3,3)."' OR code_b='000') ";
				if(substr($rowCate->categorycode,6,3)!="000") {
					$sql2.= "AND (code_c='".substr($rowCate->categorycode,6,3)."' OR code_c='000') ";
					if(substr($rowCate->categorycode,9,3)!="000") {
						$sql2.= "AND (code_d='".substr($rowCate->categorycode,9,3)."' OR code_d='000') ";
					} else {
						$sql2.= "AND code_d='000' ";
					}
				} else {
					$sql2.= "AND code_c='000' ";
				}
			} else {
				$sql2.= "AND code_b='000' AND code_c='000' ";
			}
			$sql2.= "AND type IN ('L', 'LX', 'LM', 'LMX') 
			ORDER BY code_a,code_b,code_c,code_d ASC ";
			$result2 = pmysql_query($sql2,get_db_conn());
			$_=array();
			while($row2=pmysql_fetch_object($result2)) {
				$_[] = $row2->product;
			}
			$__[] = "<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;".implode(" > ",$_)."<input type = 'hidden' name ='set_productcode[]' value = '".$rowCate->categorycode."'></div>";
			pmysql_free_result($result2);
		}
		$cp_product = implode("",$__);
		pmysql_free_result($resultCate);
	} else if($cp_productcode=="GOODS") {
		$sql2 = "SELECT productname as product, a.productcode FROM tblproduct a JOIN tblcouponproduct b on a.productcode = b.productcode WHERE coupon_code = '{$coupon_code}'";
		$result2 = pmysql_query($sql2,get_db_conn());
		$count = 1;
		while($row2 = pmysql_fetch_object($result2)) {
			$_[] = "<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;".$row2->product."<input type = 'hidden' name ='set_productcode[]' value = '".$row2->productcode."'></div>";
			$count++;
		}
		$cp_product = implode("",$_);
		pmysql_free_result($result2);
	} else if($cp_productcode=="BRANDSEASONS") {
		$sql2 = "SELECT brandname, a.bridx, b.vender, season_year, season FROM tblproductbrand a JOIN tblcouponbrandseason b on a.bridx = b.bridx WHERE coupon_code = '{$coupon_code}' order by no";
		$result2 = pmysql_query($sql2,get_db_conn());
		$count = 1;
		while($row2 = pmysql_fetch_object($result2)) {
			if ($row2->season_year) {
				list($_season)=pmysql_fetch("SELECT season_kor_name FROM tblproductseason where season_year='{$row2->season_year}' AND season='{$row2->season}' ");
			} else {
				$_season	= "전체";
			}
			$_bs_name	= $row2->brandname." > ".$_season;
			$_bs_val		= $row2->bridx."|".$row2->vender."|".$row2->season_year."|".$row2->season;
			$_[] = "<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;".$_bs_name."<input type = 'hidden' name ='set_productcode[]' value = '".$_bs_val."'></div>";
			$count++;
		}
		$cp_product = implode("",$_);
		pmysql_free_result($result2);
	}

	// 제품 상세 쿠폰 노출 설정
	if($cp_row->coupon_type == '6') {
		$cp_detail_auto	= $cp_row->detail_auto;
		$selected[detail_auto][$cp_detail_auto]	= "selected";
		if($cp_row->detail_auto == 'Y'){
			$cp_display_img_type	= $cp_row->display_img_type;
			$checked[display_img_type][$cp_display_img_type]	= "checked";
			if ($cp_row->display_img_type == '2') {
				if(file_exists($imagepath.$cp_row->display_img)) {
					$cp_display_img	= $cp_row->display_img;
					$cp_display_img_html = "<img src='{$imagepath}{$cp_row->display_img}' style='max-width:352px'>";
				}
			}
		}
	}

	// 발행 쿠폰 수량
	if($cp_row->coupon_type == '7') {
		$cp_issue_max_no	= $cp_row->issue_max_no; 
	}
}

//회원 등급
$sql = "SELECT group_code, group_name FROM tblmembergroup ";
$result = pmysql_query($sql,get_db_conn());
$count=0;
$group_option	= '';
while ($row=pmysql_fetch_object($result)) {
	if($count==0) $group_option	.= "<option value=\"\">등급선택</option>\n";
	$group_option	.="<option value=\"{$row->group_code}\" ".$selected[sel_group][$row->group_code].">{$row->group_name}</option>\n";
	$count++;
}
pmysql_free_result($result);
if($count==0) $group_option	.= "<option value=\"\">회원등급 없음.</option>\n";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".CLS_coupon_type").on('change', function() {
		$("#ID_join_rote").hide();
		$("#ID_issue_date").hide();
		$("#ID_issue_date_ago").hide();
		$("#ID_issue_date_weekend").hide();
		$("#ID_issue_date_month").hide();
		$("#ID_order_acceptQ").hide();
		$("#ID_order_acceptP").hide();
		$("input[name=issue_selmembers]").val("");
		$("input[name=issue_excelmembers]").val("");
		$("#ID_sel_members").hide();
		$("#ID_membersLayer").html('');
		$("#ID_sel_group").hide();
		$("select[name=sel_gubun]").val("A");
		$("select[name=sel_group]").val("");
		$("#ID_btn_selmembers").hide();
		$("#ID_btn_excelmembers").hide();
		$("#ID_one_issue_limit").show();
		$("#ID_sale2").show();
		$("input[name=peorid]").val('0');
		$("select[name=time]").val("P");
		$("#ID_coupon_time_weekend").hide();
		$("#ID_coupon_time_month").hide();
		$("#ID_coupon_timeP").show();
		$("#ID_coupon_timeD").hide();
		$("select[name=time] option[value='P']").removeAttr('disabled');
		$("#ID_detail_auto").hide();
		$("select[name=detail_auto]").val("1");
		$("input[name=display_img_type]").eq(0).attr('checked',true);
		$("#ID_display_img_type").hide();
		$("input[name=display_img]").val("");
		$("#ID_display_def_img").hide();
		$("#ID_display_up_img").hide();
		$("#ID_issue_max").hide();
		$("#ID_sel_gubun").hide();
		$("input[name=coupon_use_type]").eq(1).removeAttr('disabled');
		$("input[name=couponaccept]").eq(2).removeAttr('disabled');
		$("input[name=issue_max_no]").val('0');
		if($(this).val() == '1'){ // 즉시발급
			$("input[name=issue_type]").val('N');
			$("input[name=issue_code]").val('1');
			$("#ID_sel_gubun").show();
			$("#ID_sel_members").show();
			$("#ID_membersLayer").html('전체회원');
		} else if($(this).val() == '2'){ // 회원가입
			$("input[name=issue_type]").val('M');
			$("input[name=issue_code]").val('2');
		} else if($(this).val() == '6'){ // 다운로드
			$("input[name=issue_type]").val('Y');
			$("input[name=issue_code]").val('0');
			$("#ID_detail_auto").show();
		} else if($(this).val() == '7'){ // 페이퍼
			$("input[name=issue_type]").val('P');
			$("input[name=issue_code]").val('0');
			$("input[name=one_issue_limit]").eq(0).attr('checked',true);
			$("input[name=one_issue_quantity]").val('');
			$("input[name=one_issue_quantity]").attr("disabled", "disabled");
			$("input[name=one_issue_quantity]").css('background','#F0F0F0');
			$("#ID_one_issue_limit").hide();
			$("input[name=issue_max_no]").val('100');
			$("#ID_issue_max").show();
		} else if($(this).val() == '9'){ // 무료배송
			$("input[name=issue_type]").val('N');
			$("input[name=issue_code]").val('1');
			$("select[name=sale2]").val("원");
			$("input[name='sale_money']").val(0);
			$("input[name='sale_max_money']").val(0);
			document.form1.rate.value="원";
			$('#ID_maxPrice').hide();
			$("#ID_sale2").hide();
			$("#ID_sel_gubun").show();
			$("#ID_sel_members").show();
			$("#ID_membersLayer").html('전체회원');
			if($("input[name=coupon_use_type]:checked").val() != '1') {
				$("input[name=coupon_use_type]").eq(0).attr('checked',true);
				$('#ID_coupon1').removeAttr('disabled');
				$('#ID_coupon2').attr("disabled", "disabled");
				$('#ID_coupon3').removeAttr('disabled');
				$('#ID_coupon1').trigger('click');
			}
			$("input[name=coupon_use_type]").eq(1).attr("disabled", "disabled");
			$("input[name=couponaccept]").eq(1).attr("disabled", "disabled");
		} else if($(this).val() == '15'){ // 회원 등급별
			$("input[name=issue_type]").val('N');
			$("input[name=issue_code]").val('2');
			$("#ID_sel_gubun").hide();
			$("#ID_sel_members").show();
			$("#ID_membersLayer").html('전체회원');
			$("select[name=sel_gubun]").val('G').change(); 
		} else if($(this).val() == '16'){ // 일반
			$("input[name=issue_type]").val('N');
			$("input[name=issue_code]").val('0');
		} else {// 그외
			$("input[name=issue_type]").val('A');
		}
		if($(this).val() == '12'){
			$("#ID_order_acceptQ").show();
		}else if($(this).val() == '13'){
			$("#ID_order_acceptP").show();
		}else if($(this).val() == '14' || $(this).val() == '15'){
			$("input[name=peorid]").val('0');
			$("select[name=time]").val("D");
			$("#ID_coupon_timeP").hide();
			$("#ID_coupon_timeD").show();
			$("select[name=time] option[value='P']").prop("disabled","disabled"); 
		}

		if($(this).val() == '3' || $(this).val() == '10' || $(this).val() == '14' || $(this).val() == '15'){
			$("input[name=join_rote]").eq(0).attr('checked',true);
			$("#ID_issue_date").show();
			if($(this).val() == '3' || $(this).val() == '10') $("#ID_issue_date_ago").show();
			if($(this).val() == '14') {
				$("#ID_issue_date_weekend").show();
				$("#ID_coupon_time_weekend").show();
			}
			if($(this).val() == '15') {
				$("#ID_issue_date_month").show();
				$("#ID_coupon_time_month").show();
			}
		} else {
			$("#ID_join_rote").show();
		}
	});
	$(".CLS_coupon").click(function(){
		$("#ID_use_con_type2").hide();
		if($(this).val() == '1'){
			$("select[name=use_con_type2]").val("N");
			$("#ID_coupon_all").show();
			$("#ID_coupon_goods").hide();
			$("input[name='productcode']").val("ALL");
			$("#ID_productLayer").html('');
		}else{
			$("#ID_use_con_type2").show();
			$("#ID_coupon_all").hide();
			$("#ID_coupon_goods").show();
			$("input[name='productcode']").val("");
			$("#ID_productLayer").html('');
		}
	});
	$(".CLS_sel_gubun").on('change', function() {
		$("input[name=issue_selmembers]").val("");
		$("input[name=issue_excelmembers]").val("");
		$("#ID_membersLayer").html('');
		$("#ID_sel_group").hide();
		$("select[name=sel_group]").val("");
		$("#ID_btn_selmembers").hide();
		$("#ID_btn_excelmembers").hide();

		if($(this).val() == 'A') {
			$("#ID_membersLayer").html('전체회원');
		} else if($(this).val() == 'G') {
			$("#ID_sel_group").show();
			$("#ID_membersLayer").html('등급을 선택하세요.');
		} else if($(this).val() == 'M') {
			$("#ID_btn_selmembers").show();
			$("#ID_membersLayer").html('회원을 선택하세요.');
		} else if($(this).val() == 'E') {
			$("#ID_btn_excelmembers").show();
			$("#ID_membersLayer").html('직접 업로드를 하세요.');
		}
	});
	$(".CLS_sel_group").on('change', function() {
		$("#ID_membersLayer").html($("option:selected",this).text());
	});
	$(".CLS_coupon_use_type").click(function(){
		$("select[name=mini_type] option[value='Q']").removeAttr('disabled'); 
		if($(this).val() == '2'){
			$("input[name=mini_quantity]").val('0');
			$("select[name=mini_type]").val('P');
			$("select[name=mini_type] option[value='Q']").prop("disabled","disabled"); 
			$("#ID_mini_typeP").show();
			$("#ID_mini_typeQ").hide();
			$('#ID_coupon1').attr("disabled", "disabled");
			$('#ID_coupon2').removeAttr('disabled');
			$('#ID_coupon3').removeAttr('disabled');
			$('#ID_coupon2').trigger('click');
		}else{
			$('#ID_coupon1').removeAttr('disabled');
			$('#ID_coupon2').attr("disabled", "disabled");
			$('#ID_coupon3').removeAttr('disabled');
			$('#ID_coupon1').trigger('click');
		}
	});
	$(".CLS_coupon_time").on('change', function() {
		$("input[name=peorid]").val('0');
		if($(this).val() == 'D'){
			$("#ID_coupon_timeD").show();
			$("#ID_coupon_timeP").hide();
		}else{
			$("#ID_coupon_timeP").show();
			$("#ID_coupon_timeD").hide();
		}
	});
	$(".CLS_mini_type").on('change', function() {
		if($(this).val() == 'P'){
			$("input[name=mini_quantity]").val('0');
			$("#ID_mini_typeP").show();
			$("#ID_mini_typeQ").hide();
		}else{
			$("input[name=mini_price]").val('0');
			$("#ID_mini_typeQ").show();
			$("#ID_mini_typeP").hide();
		}
	});
	$(".CLS_one_issue_limit").click(function(){
		if($(this).val() == '1'){
			$("input[name=one_issue_quantity]").val('');
			$("input[name=one_issue_quantity]").attr("disabled", "disabled");
			$("input[name=one_issue_quantity]").css('background','#F0F0F0');
		}else{
			$("input[name=one_issue_quantity]").val('0');
			$("input[name=one_issue_quantity]").removeAttr('disabled');
			$("input[name=one_issue_quantity]").css('background','#FFFFFF');
		}
	});
	$(".CLS_detail_auto").on('change', function() {
		$("input[name=display_img_type]").eq(0).attr('checked',true);
		$("input[name=display_img]").val("");
		$("#ID_display_def_img").hide();
		$("#ID_display_up_img").hide();
		if($(this).val() == 'N'){
			$("#ID_display_img_type").hide();
		}else{
			$("#ID_display_img_type").show();
			$("#ID_display_def_img").show();
		}
	});

	$(".CLS_display_img_type").click(function(){
		$("#ID_display_def_img").hide();
		$("#ID_display_up_img").hide();
		if($(this).val() == '1'){
			$("input[name=display_img]").val("");
			$("#ID_display_def_img").show();
		} else {
			$("#ID_display_up_img").show();
		}
	});
});
function ChoiceProduct(){
	if($(".CLS_coupon:checked").val() == '2'){
		window.open("about:blank","coupon_product","width=600,height=150,scrollbars=no");
		document.form2.action = "coupon_productchoice2.php";
	}else if($(".CLS_coupon:checked").val() == '3'){
		window.open("about:blank","coupon_product","width=700,height=800,scrollbars=yes");
		document.form2.action = "coupon_productchoice3.php";
	}else if($(".CLS_coupon:checked").val() == '4'){
		window.open("about:blank","coupon_product","width=600,height=150,scrollbars=yes");
		document.form2.action = "coupon_productchoice4.php";
	}
	document.form2.submit();
}
function changerate(rate){
	if($("input[name='sale_money']").val() =='') $("input[name='sale_money']").val(0);
	$("input[name='sale_max_money']").val(0);
	document.form1.rate.value=rate;
	if(rate=="%") {
		if($("input[name='sale_type']").val() == '-') $('#ID_maxPrice').show();
	} else {
		if($("input[name='sale_type']").val() == '-') $('#ID_maxPrice').hide();
	}
}
function CheckForm(submit_type) {
	var form = document.form1;
	var content ="아래의 사항을 확인하시고, <?=$mode_text?>하시면 됩니다.\n\n"
			 +"<쿠폰 발급 정보>--------------------------------------------\n\n"
			 +"* 쿠폰 발급 구분 : "+form.coupon_type.options[form.coupon_type.selectedIndex].text+"\n\n";
	if(form.coupon_type.value == '12') {
		if(form.order_accept_quantity.value == '') {
			alert("구매 수량을 입력하세요.");
			form.order_accept_quantity.focus();
			return;	
		} else {
			content+="* 구매 수량 : "+form.order_accept_quantity.value+"개 이상\n\n";
		}
	}
	if(form.coupon_type.value == '13') {
		if(form.order_accept_price.value == '') {
			alert("구매 금액을 입력하세요.");
			form.order_accept_price.focus();
			return;	
		} else {
			content+="* 구매 금액 : "+form.order_accept_price.value+"원 이상\n\n";
		}
	}
	if(form.coupon_type.value == '1' || form.coupon_type.value == '9' || form.coupon_type.value == '15'){
		content+="* 쿠폰 발급 대상 : ";
		if(form.sel_gubun.value == 'A') {
			content+=form.sel_gubun.options[form.sel_gubun.selectedIndex].text;
		} else if(form.sel_gubun.value == 'G') {
			if(form.sel_group.value == '') {
				alert('등급을 선택하세요.');
				return;
			} else {
				content+=form.sel_group.options[form.sel_group.selectedIndex].text + " 등급";
			}
		} else if(form.sel_gubun.value == 'M') {
			if(form.issue_selmembers.value == '') {
				alert('회원을 선택하세요.');
				return;
			} else {
				var issue_selmembers_val	= form.issue_selmembers.value;
				var issue_selmembers_arr	= issue_selmembers_val.split(",");
				content+= issue_selmembers_arr[0];
				if(issue_selmembers_arr.length > 1) content+= " 외 " + (issue_selmembers_arr.length - 1)+"명";
			}
		} else if(form.sel_gubun.value == 'E') {
			if(form.issue_excelmembers.value == '') {
				alert('직접 업로드를 하세요.');
				return;
			} else {
				var issue_excelmembers_val	= form.issue_excelmembers.value;
				var issue_excelmembers_arr	= issue_excelmembers_val.split(",");
				content+= issue_excelmembers_arr[0];
				if(issue_excelmembers_arr.length > 1) content+= " 외 " + (issue_excelmembers_arr.length - 1)+"명";
			}
		}
		content+="\n\n";

	}
	if(form.coupon_type.value == '3' || form.coupon_type.value == '10'){
		if(form.issue_days_ago.value=='') {
			alert("발급 시작일을 입력하세요.");
			form.issue_days_ago.focus();
			return;		
		}
		content+="* 발급 시점 설정 : 해당일 기준 "+form.issue_days_ago.value+" 일 전\n\n";
	} else if(form.coupon_type.value == '14') {
		content+="* 발급 시점 설정 : 주단위 주말\n\n";
	} else if(form.coupon_type.value == '15') {
		content+="* 발급 시점 설정 : 월단위\n\n";
	} else {
		var join_rote_text	= "";
		if (form.join_rote.value == 'A') join_rote_text = "전체";
		if (form.join_rote.value == 'P') join_rote_text = "PC";
		if (form.join_rote.value == 'M') join_rote_text = "모바일 웹";
		if (form.join_rote.value == 'T') join_rote_text = "모바일 APP";
		content+="* 경로 : "+join_rote_text+"\n\n";
	}
	if(form.coupon_name.value.length==0) {
		alert("쿠폰 명을 입력하세요.");
		form.coupon_name.focus();
		return;
	}
	if(CheckLength(form.coupon_name)>40) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 20자 이내 혹은 영문/숫자/기호 40자 이내로 입력이 가능합니다.");
		form.coupon_name.focus();
		return;
	}
	content+="* 쿠폰 명 : "+form.coupon_name.value+"\n\n";
	content+="* 쿠폰 설명 : "+form.description.value+"\n\n";

	if(form.coupon_type.value != '9') {	
		if (form.sale_money.value.length==0 || form.sale_money.value<1) {
			alert("쿠폰 할인 금액/할인률을 입력하세요.");
			form.sale_money.focus();
			return;
		} else if (!IsNumeric(form.sale_money.value)) {
			alert("쿠폰 할인 금액/할인률은 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
			form.sale_money.focus();
			return;
		}
		if(form.sale2.selectedIndex==1 && (form.sale_money.value>=100 || form.sale_money.value<1)){
			alert("쿠폰 할인률은 1 ~ 100 이여야 합니다.");
			form.sale_money.focus();
			return;
		}
		content+="* 쿠폰 금액/할인률 : "+form.sale_money.value+form.sale2.options[form.sale2.selectedIndex].value+"\n\n";
		if(form.sale_max_money.value==''){
			alert("할인 상한 금액을 입력하세요.");
			form.sale_max_money.focus();
			return;
		}
		if(form.sale2.selectedIndex==1) {
			var sale2_text	= form.sale_max_money.value+"원";
			if(form.sale_max_money.value=='0') sale2_text = "제한 없음";
			content+="* 할인 상한 금액 : "+sale2_text+"\n\n";
		}
	}

	if(form.coupon_type.value != '7') {
		if(form.one_issue_limit.value==1) {
			content+="* 1회 발급 수량 : 1장 발급\n\n";
		} else {
			if(form.one_issue_limit.value==2 && form.one_issue_quantity.value==''){
				alert("1회 발급 수량을 입력해 주세요.");
				form.one_issue_quantity.focus();
				return;
			}
			if(form.one_issue_limit.value==2 && form.one_issue_quantity.value<1){
				alert("1회 발급 수량은 최소 1개 이상 이여야 합니다.");
				form.one_issue_quantity.focus();
				return;
			}
			content+="* 1회 발급 수량 : "+form.one_issue_quantity.value+"장 발급\n\n";
		}
	}
	var coupon_use_type_text	= "";
	if(form.coupon_use_type.value=='1') coupon_use_type_text = "장바구니 쿠폰";
	if(form.coupon_use_type.value=='2') coupon_use_type_text = "상품별 쿠폰";
	content +="<쿠폰 사용 정보>--------------------------------------------\n\n";
	content +="* 쿠폰 사용 방법 : "+coupon_use_type_text+"\n\n";
	var coupon_is_mobile_text	= "";
	if (form.coupon_is_mobile.value == 'A') coupon_is_mobile_text = "전체";
	if (form.coupon_is_mobile.value == 'P') coupon_is_mobile_text = "PC";
	if (form.coupon_is_mobile.value == 'M') coupon_is_mobile_text = "모바일 웹";
	if (form.coupon_is_mobile.value == 'T') coupon_is_mobile_text = "모바일 APP";
	if (form.coupon_is_mobile.value == 'B') coupon_is_mobile_text = "PC + 모바일 웹";
	if (form.coupon_is_mobile.value == 'C') coupon_is_mobile_text = "PC + 모바일 APP";
	if (form.coupon_is_mobile.value == 'D') coupon_is_mobile_text = "모바일 웹 + 모바일 APP";
	content+="* 쿠폰 사용 범위 : "+coupon_is_mobile_text+"\n\n";
	if(form.mini_type.selectedIndex==0){
		if(form.mini_price.value==''){
			alert("쿠폰 사용 제한 주문 금액을 입력하세요.");
			document.form1.mini_price.focus();
			return;
		}else if(!IsNumeric(form.mini_price.value)){
			alert("쿠폰 사용 제한 주문 금액은 숫자만 입력 가능합니다.");
			form.mini_price.focus();
			return;
		}
		if(form.mini_price.value=='0'){
			content+="* 쿠폰 사용 제한 : "+form.mini_type.options[form.mini_type.selectedIndex].text+" 제한없음\n\n";
		} else {
			content+="* 쿠폰 사용 제한 : "+form.mini_type.options[form.mini_type.selectedIndex].text+" "+form.mini_price.value+"원 이상\n\n";
		}
	} else if(form.mini_type.selectedIndex==1){
		if(form.mini_quantity.value==''){
			alert("쿠폰 사용 제한 상품 수량을 입력하세요.");
			form.mini_quantity.focus();
			return;
		}else if(!IsNumeric(form.mini_quantity.value)){
			alert("쿠폰 사용 제한 상품 수량은 숫자만 입력 가능합니다.");
			form.mini_quantity.focus();
			return;
		}
		if(form.mini_quantity.value=='0'){
			content+="* 쿠폰 사용 제한 : "+form.mini_type.options[form.mini_type.selectedIndex].text+" 제한없음\n\n";
		} else {
			content+="* 쿠폰 사용 제한 : "+form.mini_type.options[form.mini_type.selectedIndex].text+" "+form.mini_quantity.value+"개 이상\n\n";
		}
	}

	if(form.time.selectedIndex==0){
		if(form.peorid.value==''){
			alert("유효 기간 설정 발급일 일수를 입력하세요.");
			form.peorid.focus();
			return;
		}else if(!IsNumeric(form.peorid.value)){
			alert("유효 기간 설정 발급일 일수는 숫자만 입력 가능합니다.");
			form.peorid.focus();
			return;
		}
		content+="* 유효 기간 설정 : "+form.time.options[form.time.selectedIndex].text+" "+form.peorid.value+"일 동안, "+form.date_end.value+" 까지 사용가능\n\n";
	} else {
		date = "<?=date("Y-m-d");?>";
		if (form.date_start.value<date || form.date_end.value<date || form.date_start.value>form.date_end.value) {
			alert("쿠폰 유효기간 설정이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			form.date_start.focus();
			return;
		}

		content+="* 유효 기간 설정 : "+form.time.options[form.time.selectedIndex].text+" "+form.date_start.value+"부터 "+form.date_end.value+" 까지";
		if(form.coupon_type.value == '14') content+=", 해당 주말 동안";
		if(form.coupon_type.value == '15') content+=", 해당 월 동안";
		content+=" 사용가능\n\n";
	}
	
	content +="<쿠폰 부가 정보>--------------------------------------------\n\n";
	if(form.couponaccept.value=='1') {
		content+="* 카테고리/상품 선택 : 사용안함";
	} else if(form.couponaccept.value=='2') {
		content+="* 카테고리/상품 선택 : 카테고리 " + form.use_con_type2.options[form.use_con_type2.selectedIndex].text;
	} else if(form.couponaccept.value=='3') {
		content+="* 카테고리/상품 선택 : 상품 " + form.use_con_type2.options[form.use_con_type2.selectedIndex].text;
	} else if(form.couponaccept.value=='4') {
		content+="* 카테고리/상품 선택 : 브랜드시즌 " + form.use_con_type2.options[form.use_con_type2.selectedIndex].text;
	}
	var couponaccept_pro	= $("#ID_productLayer").html();
	couponaccept_pro = couponaccept_pro.replace(/(<([^>]+)>)/gi, "");
	couponaccept_pro = couponaccept_pro.replace(/&nbsp;&nbsp;/gi, "\n - ");
	couponaccept_pro = couponaccept_pro.replace(/&gt;/gi, ">");
	if((form.couponaccept.value=='2' || form.couponaccept.value=='3' || form.couponaccept.value=='4') && couponaccept_pro =='') {
		alert("상품군을 선택하세요.");
		return;
	}
	content+=couponaccept_pro+"\n\n";

	if(form.coupon_type.value=='6') {
		content+="* 제품 상세 쿠폰 노출 설정 : " + form.detail_auto.options[form.detail_auto.selectedIndex].text;
		if(form.detail_auto.value=='Y'){
			if(form.display_img_type.value =="1") content+=" (기본 이미지 사용)";
			if(form.display_img_type.value =="2") {
				if(form.display_img.value =="" && form.old_display_img.value=="") {
					alert("제품 상세 쿠폰 이미지를 업로드하세요.");
					return;
				}
				content+=" (직접 업로드)";
			}
		}
		content+="\n\n";
	}

	if(form.coupon_type.value=='7') {
		if(form.issue_max_no.value =="" || form.issue_max_no.value == '0') {
			alert("발행 쿠폰 수량을 입력하세요.");
			form.issue_max_no.focus();
			return;
		}
		content+="* 발행 쿠폰 수량 설정 : " + form.issue_max_no.value + "장\n\n";
	}

	if(confirm(content)){
		form.type.value=submit_type;
		form.target="hiddenframe";
		form.submit();
	}
}

function add_members(type) {
	 window.open("about:blank","findpopup","width=250,height=150,scrollbars=yes");
	 if (type =='sel')
	 {
		 document.mform.action='market_coupon_selmembers_v3.php';
	 } else if (type == 'excel')
	 {
		 document.mform.action='market_coupon_excelmembers_v3.php';
	 }
	 document.mform.submit();
}
</script>
<style>
.btn_blue {cursor:pointer;color:#FFFFFF;background-color:#52A3E7;font-size:9pt;border:1px solid rgb(0,102,255);padding:2px 5px 2px;margin-top:2px;font-family: '돋움';}
.btn_blue:hover {background-color:#368FDA;}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쿠폰발행 서비스 설정 &gt;<span><?=$menu_title_name?> <?=$mode_text?></span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<INPUT type = 'hidden' name='type'>
			<INPUT type = 'hidden' name='coupon_code' value='<?=$mode=='modify'?$coupon_code:''?>'>
			<INPUT type = 'hidden' name='mode' value='<?=$mode?>'>
			<INPUT type = 'hidden' name='productcode' value="<?=$cp_productcode?$cp_productcode:'ALL'?>">
			<INPUT type = 'hidden' name='use_point' value = 'Y'>
			<INPUT type = 'hidden' name='sale_type' value = '-'>
			<INPUT type = 'hidden' name='bank_only' value = 'N'>
			<INPUT type = 'hidden' name='delivery_type' value = 'N'>
			<INPUT type = 'hidden' name='use_con_type1' value='Y'>
			<INPUT type = 'hidden' name='issue_tot_no'>
			<INPUT type = 'hidden' name="repeat_id" value="Y">
			<INPUT type = 'hidden' name="issue_member_no">
			<INPUT type = 'hidden' name="amount_floor" value="<?=$amount_floor?>">
			<?if ($coupon_type_check == 'normal') {?>
			<INPUT type = 'hidden' name='issue_type' value = '<?=$cp_issue_type?$cp_issue_type:"N"?>'>
			<INPUT type = 'hidden' name='issue_code' value = '<?=$cp_issue_code?$cp_issue_code:"0"?>'>
			<?} else if ($coupon_type_check == 'auto') {?>
			<INPUT type = 'hidden' name='issue_type' value = '<?=$cp_issue_type?$cp_issue_type:"M"?>'>
			<INPUT type = 'hidden' name='issue_code' value = '2'>
			<?}?>
			<INPUT type = 'hidden' name="issue_selmembers" value='<?=$cp_issue_selmembers?>'>
			<INPUT type = 'hidden' name="issue_excelmembers" value='<?=$cp_issue_excelmembers?>'>
			<INPUT type = 'hidden' name="old_display_img" value='<?=$cp_display_img?>'>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$menu_title_name?> <?=$mode_text?></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원에 대한 <?=$menu_title_name?>을 <?=$mode_text?> 할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">정보 <?=$mode_text?></div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>쿠폰 발급 정보</b></li>
						<li style='margin-top:8px'>- <?=$menu_title_name?> 의 발급정보를 <?=$mode_text?> 할 수 있습니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                <tr>
                	<td>
                    	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
							<th height='33'><span>쿠폰 발급 구분</span></th>
								<td>
									<select name=coupon_type class="select_selected CLS_coupon_type"  style="width:120px;padding-top:1pt;">
									<?if ($coupon_type_check == 'auto') {?>
										<option value=2 <?=$selected[coupon_type]['2']?>>회원가입</option>
										<option value=3 <?=$selected[coupon_type]['3']?>>기념일</option>
										<option value=10 <?=$selected[coupon_type]['10']?>>생일</option>
										<option value=4 <?=$selected[coupon_type]['4']?>>첫구매</option>
										<option value=11 <?=$selected[coupon_type]['11']?>>상품구매 후기</option>
										<option value=12 <?=$selected[coupon_type]['12']?>>구매 수량 충족</option>
										<option value=13 <?=$selected[coupon_type]['13']?>>구매 금액 충족</option>
										<option value=14 <?=$selected[coupon_type]['14']?>>주말 출석</option>
										<option value=15 <?=$selected[coupon_type]['15']?>>회원 등급별</option>
									<?} else if ($coupon_type_check == 'normal') {?>
										<option value=16 <?=$selected[coupon_type]['16']?>>일반발급</option>
										<option value=6 <?=$selected[coupon_type]['6']?>>다운로드</option>
										<option value=1 <?=$selected[coupon_type]['1']?>>즉시발급</option>
										<option value=7 <?=$selected[coupon_type]['7']?>>페이퍼</option>
										<option value=9 <?=$selected[coupon_type]['9']?>>무료배송</option>
									<?}?>
									</select><span id = 'ID_order_acceptQ' <?if($cp_coupon_type != '12'){?>style='display:none;'<?}?>>&nbsp;<INPUT onkeyup=strnumkeyup(this); maxLength=6 size=7 name=order_accept_quantity value=0 style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 개 이상</span><span id = 'ID_order_acceptP' <?if($cp_coupon_type != '13'){?>style='display:none;'<?}?>>&nbsp;<INPUT onkeyup=strnumkeyup(this); maxLength=6 size=7 name=order_accept_price value=0 style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 원 이상</span>
									<span id = 'ID_sel_gubun' <?if($cp_coupon_type != '1' && $cp_coupon_type != '9'){?>style='display:none;'<?}?>>
									<select name=sel_gubun class="select_selected CLS_sel_gubun"  style="width:120px;padding-top:1pt;">
									<option value='A' <?=$selected[sel_gubun]['A']?>>전체회원</option>
									<option value='G' <?=$selected[sel_gubun]['G']?>>회원등급선택</option>
									<option value='M' <?=$selected[sel_gubun]['M']?>>회원선택</option>
									<option value='E' <?=$selected[sel_gubun]['E']?>>직접업로드</option>
									</select>
									</span>
									<span id = 'ID_sel_group' <?if($cp_sel_gubun != 'G'){?>style='display:none;'<?}?>>
									<select name=sel_group class="select_selected CLS_sel_group"  style="width:120px;padding-top:1pt;">
									<?=$group_option?>
									</select>
									</span>
									<span id = 'ID_btn_selmembers' <?if($cp_sel_gubun != 'M'){?>style='display:none;'<?}?>><input type="button" value="회원선택" class="btn_blue" onclick="javascript:add_members('sel');"></span>
									<span id = 'ID_btn_excelmembers' <?if($cp_sel_gubun != 'E'){?>style='display:none;'<?}?>><input type="button" value="파일 업로드" class="btn_blue" onclick="javascript:add_members('excel');"></span>
								</td>
							</tr>
							<tr id='ID_sel_members' <?if($cp_coupon_type != '1' && $cp_coupon_type != '9' && $cp_coupon_type != '15'){?>style='display:none;'<?}?>>
								<th><span>쿠폰 발급 대상</span></th>
								<td id = 'ID_membersLayer' style='line-height:20px'><?=$cp_issue_members_html?></td>
							</tr>
							<tr id='ID_join_rote' <?if($cp_coupon_type == '3' || $cp_coupon_type == '10' || $cp_coupon_type == '14' || $cp_coupon_type == '15'){?>style='display:none;'<?}?>>
								<th><span>경로</span></th>
								<td>
									<input type=radio id=ID_join_rote1 name=join_rote value="A" <?=$checked[join_rote]['A']?$checked[join_rote]['A']:"checked"?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_join_rote1>전체</label>&nbsp;
									<input type=radio id=ID_join_rote2 name=join_rote value="P" <?=$checked[join_rote]['P']?$checked[join_rote]['P']:""?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_join_rote2>PC</label>&nbsp;
									<input type=radio id=ID_join_rote3 name=join_rote value="M" <?=$checked[join_rote]['M']?$checked[join_rote]['M']:""?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_join_rote3>모바일 웹</label>&nbsp;
									<input type=radio id=ID_join_rote4 name=join_rote value="T" <?=$checked[join_rote]['T']?$checked[join_rote]['T']:""?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_join_rote4>모바일 APP</label>
								</td>
							</tr>
							<tr id='ID_issue_date' <?if($cp_coupon_type != '3' && $cp_coupon_type != '10' && $cp_coupon_type != '14' && $cp_coupon_type != '15'){?>style='display:none;'<?}?>>
								<th height=31><span>발급 시점 설정</span></th>
								<td>
									<div id='ID_issue_date_ago' <?if($cp_coupon_type != '3' && $cp_coupon_type != '10'){?>style='display:none;'<?}?>>해당일 기준 <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px;TEXT-ALIGN: right" maxLength=3 size=4 name=issue_days_ago value='<?=$cp_issue_days_ago?$cp_issue_days_ago:'0'?>'> 일 전</div>
									<div id='ID_issue_date_weekend' <?if($cp_coupon_type != '14'){?>style='display:none;'<?}?>>주단위 주말</div>
									<div id='ID_issue_date_month' <?if($cp_coupon_type != '15'){?>style='display:none;'<?}?>>월단위</div>
								</td>
							</tr>
							<tr>
								<th><span>쿠폰 명</span></th>
								<td><INPUT maxLength=40 size=70 name=coupon_name value='<?=$cp_coupon_name?>'> <span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~[한글 기호 숫자 합처서 40바이트 넘어가면 안됩니다.]</b></span></td>
							</tr>
							<tr>
								<th><span>쿠폰 설명</span></th>
								<td><INPUT maxLength=200 size=91 name=description style=width:99% value='<?=$cp_description?>'></td>
							</tr>
							<tr id='ID_sale2' <?if($cp_coupon_type == '9'){?>style='display:none;'<?}?>>
								<th><span>금액/할인율 선택</span></th>
								<td>
								<SELECT style="WIDTH: 130px;padding-top: 1pt;" onchange="changerate(this.value)" name=sale2 class="select_selected">
									<OPTION value='원' <?=$selected[sale2]['원']?$selected[sale2]['원']:'selected'?>>금액</OPTION>
									<OPTION value='%' <?=$selected[sale2]['%']?$selected[sale2]['%']:''?>>할인율</OPTION>
								</SELECT>
								→
								<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money value='<?=$cp_sale_money?$cp_sale_money:'0'?>'>
								<INPUT class="input_hide1" readOnly size=1 value='<?=$cp_sale2?$cp_sale2:'원'?>' name=rate>
								</td>
							</tr>
							<tr id = 'ID_maxPrice' <?if($cp_sale2 != '%'){?>style='display:none;'<?}?>>
								<th><span>할인 상한 금액</span></th>
								<td>
									<INPUT maxLength='10' size='10' name='sale_max_money' value = '<?=$cp_sale_max_money?$cp_sale_max_money:'0'?>' onkeyup='strnumkeyup(this);' style="PADDING-RIGHT: 5px; TEXT-ALIGN: right"> 원
									 <span class="font_orange"> * %할인의 경우 최대로 할인 받을수 있는 금액(0원일 경우 제한 없음).</span>
								</td>
							</tr>
							<tr id = 'ID_one_issue_limit' <?if($cp_coupon_type == '7') {?>style='display:none;'<?}?>>
								<th><span>1회 발급 수량</span></th>
								<td class="td_con1">
									<input type = 'radio' name = 'one_issue_limit' id = 'ID_one_issue_limit1' class = 'CLS_one_issue_limit' value = '1' <?=$checked[one_issue_limit]['1']?$checked[one_issue_limit]['1']:'checked'?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_one_issue_limit1>1장 발급</label>&nbsp;
									<input type = 'radio' name = 'one_issue_limit' id = 'ID_one_issue_limit2' class = 'CLS_one_issue_limit' value = '2' <?=$checked[one_issue_limit]['2']?$checked[one_issue_limit]['2']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_one_issue_limit2>설정된 수량만큼 발급</label> ( <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px;TEXT-ALIGN: right; <?=$cp_one_issue_limit == '2'?'background: rgb(255, 255, 255)':''?>" maxLength=6 size=7 name=one_issue_quantity class="input_disabled" <?if($cp_one_issue_limit != '2') {?>disabled<?}?>> 장 )&nbsp;
								</td>
							</tr>
                            </table>
                        </div>
					</td>
                </tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>쿠폰 사용 정보</b></li>
						<li style='margin-top:8px'>- <?=$menu_title_name?>의 사용 방법, 범위, 제한, 기간설정 정보를 <?=$mode_text?> 할 수 있습니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                <tr>
                	<td>
                    	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>쿠폰 사용 방법</span></th>
								<td>
									<?if ($coupon_made_limit == '1' || $coupon_made_limit == '3') {?><INPUT type='radio' value='1' id='coupon_use_type1'  name='coupon_use_type' class='CLS_coupon_use_type' <?=$checked[coupon_use_type]['1']?$checked[coupon_use_type]['1']:'checked'?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_use_type1>장바구니 쿠폰</label>&nbsp;<?}?>
									<?if ($coupon_made_limit == '2' || $coupon_made_limit == '3') {?><INPUT type='radio' value='2' id='coupon_use_type2' name='coupon_use_type' class='CLS_coupon_use_type'<?if ($coupon_made_limit == '2' || $cp_coupon_use_type == '2' ) {?> checked<?}?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_use_type2>상품별 쿠폰</label>&nbsp;<?}?>
								</td>
							</tr>
							<tr>
								<th><span>쿠폰 사용 범위</span></th>
								<td>
									<INPUT type='radio' value='A' id='coupon_is_mobile1' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['A']?$checked[coupon_is_mobile]['A']:'checked'?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile1>전체</label>&nbsp;
									<INPUT type='radio' value='P' id='coupon_is_mobile2' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['P']?$checked[coupon_is_mobile]['P']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile2>PC</label>&nbsp;
									<INPUT type='radio' value='M' id='coupon_is_mobile3' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['M']?$checked[coupon_is_mobile]['M']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile3>모바일 웹</label>&nbsp;
									<INPUT type='radio' value='T' id='coupon_is_mobile4' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['T']?$checked[coupon_is_mobile]['T']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile4>모바일 APP</label>&nbsp;
									<INPUT type='radio' value='B' id='coupon_is_mobile5' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['B']?$checked[coupon_is_mobile]['B']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile5>PC + 모바일 웹</label>&nbsp;
									<INPUT type='radio' value='C' id='coupon_is_mobile6' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['C']?$checked[coupon_is_mobile]['C']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile6>PC + 모바일 APP</label>&nbsp;
									<INPUT type='radio' value='D' id='coupon_is_mobile7' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' <?=$checked[coupon_is_mobile]['D']?$checked[coupon_is_mobile]['D']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=coupon_is_mobile7>모바일 웹 + 모바일 APP</label>&nbsp;<?=$cp_mini_type?>
								</td>
							</tr>
							<tr>
								<th><span>쿠폰 사용 제한</span></th>
								<td>
								<select name=mini_type class="select_selected CLS_mini_type"  style="width:120px;padding-top:1pt;">
									<option value='P' <?=$checked[mini_type]['P']?$checked[mini_type]['P']:'selected'?>>구매 금액 기준</option>
									<option value='Q' <?=$checked[mini_type]['Q']?$checked[mini_type]['Q']:''?>>상품 수량 기준</option>
								</select><span id = 'ID_mini_typeP' <?if ($cp_mini_type != 'P' && $cp_mini_type != '') {?>style='display:none;'<?}?>>&nbsp;<INPUT onkeyup=strnumkeyup(this); maxLength=10 size=10 name=mini_price value='<?=$cp_mini_price?$cp_mini_price:'0'?>' style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 원 이상 <span class="font_orange"> * 0원일 경우 제한 없음.</span></span><span id = 'ID_mini_typeQ' <?if ($cp_mini_type != 'Q') {?>style='display:none;'<?}?>>&nbsp;<INPUT onkeyup=strnumkeyup(this); maxLength=10 size=10 name=mini_quantity value='<?=$cp_mini_quantity?$cp_mini_quantity:'0'?>' style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 개 이상 <span class="font_orange"> * 0개일 경우 제한 없음.</span></span>
								</td>
							</tr>
							<tr>
								<th><span>유효 기간 설정</span></th>
								<td>
									<div id='ID_coupon_time_normal'>
									<select name=time class="select_selected CLS_coupon_time"  style="width:120px;padding-top:1pt;">
										<option value='P' <?=$selected[time_type]['P']?$selected[time_type]['P']:'selected'?> <?if ($cp_coupon_type == '14' || $cp_coupon_type == '15') {?>disabled<?}?>>발급일 기준</option>
										<option value='D' <?=$selected[time_type]['D']?$selected[time_type]['D']:''?>>기간 기준</option>
									</select>
									<span id = 'ID_coupon_timeD' <?if ($cp_time != 'D') {?>style='display:none;'<?}?>><INPUT onfocus=this.blur(); onclick=Calendar(event) size=11 name=date_start value="<?=$cp_date_start?$cp_date_start:$date_start?>" class="input_selected"> 부터 </span><span id = 'ID_coupon_timeP' <?if ($cp_time != 'P' && $cp_time != '') {?>style='display:none;'<?}?>>발급일 부터 <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid value='<?=$cp_peorid?($cp_peorid*-1):'0'?>'> 일 동안,  </span><INPUT  onfocus=this.blur(); onclick=Calendar(event) size=11 name=date_end value="<?=$cp_date_end?$cp_date_end:$date_end?>" class="input_selected"> 까지<span id='ID_coupon_time_weekend' style='font-size: 12px;height:20px;<?if ($cp_coupon_type != '14') {?>display:none;<?}?>'>, <b class='font_blue2'>해당 주말 동안 </b></span><span id='ID_coupon_time_month' style='font-size: 12px;height:20px;<?if ($cp_coupon_type != '15') {?>display:none;<?}?>'>, <b class='font_blue2'>해당 월 동안 </b></span> 사용가능<span class="font_orange"> (유효기간 마지막일 23시59분59초 까지)</span>
									</div>									
								</td>
							</tr>
							</table>
                        </div>
					</td>
                </tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>쿠폰 부가 정보</b></li>
						<li style='margin-top:8px'>- <?=$menu_title_name?> 의 적용대상 카테고리 및 상품, 제품상세 노출 여부등 부가정보를 <?=$mode_text?> 할 수 있습니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                <tr>
                	<td>
                    	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th rowspan=2><span>카테고리/상품 선택</span></th>
								<td class="td_con1">
									<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon1' value = '1' <?if ($coupon_made_limit == '2' || $cp_coupon_use_type == '2' ) {?>disabled<?} else {?><?=$checked[couponaccept]['ALL']?$checked[couponaccept]['ALL']:'checked'?><?}?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_coupon1>사용안함</label>&nbsp;
									<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon2' value = '2' <?=$checked[couponaccept]['CATEGORY']?$checked[couponaccept]['CATEGORY']:''?> <?if ($coupon_made_limit == '1' || $coupon_made_limit == '3' || $cp_coupon_use_type == '1' ) {?>disabled<?}?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_coupon2>카테고리</label>&nbsp;
									<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon3' value = '3' <?=$checked[couponaccept]['GOODS']?$checked[couponaccept]['GOODS']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_coupon3>상품</label>&nbsp;
									<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon4' value = '4' <?=$checked[couponaccept]['BRANDSEASONS']?$checked[couponaccept]['BRANDSEASONS']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_coupon4>브랜드시즌</label>&nbsp;<span id = 'ID_use_con_type2' <?if ($cp_productcode == 'ALL' || $cp_productcode == '') {?>style = 'display:none;'<?}?>>
									<div style="display:none;">
									<select name=use_con_type2 class="select_selected CLS_use_con_type2"  style="width:120px;padding-top:1pt;">
										<option value='Y' selected>포함</option>
										<option value='N'>제외</option>
									</select>
									</div>
									</span>
								</td>
							</tr>
							<tr>
								<td class="td_con1">
									<div class="table_none" id = 'ID_coupon_all' <?if ($cp_productcode != 'ALL' && $cp_productcode != '' ) {?>style = 'display:none;'<?}?>>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="80" height=28>상품군  :  </td>
												<td>전체
													<INPUT type = 'hidden' name='productname' value='전체상품'>
												</td>
											</tr>
										</table>
									</div>
									<div class="table_none" id = 'ID_coupon_goods' <?if ($cp_productcode == 'ALL' || $cp_productcode == '') {?>style = 'display:none;'<?}?>>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="100"><a href="javascript:ChoiceProduct();"><img src="images/btn_select2.gif" border="0" hspace="2"></a></td>
												<td width="80">상품군  :  </td>
												<td>
													<INPUT type = 'hidden' name='productname' value='-'>
													<div id = 'ID_productLayer'><?=$cp_product?></div>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr id='ID_detail_auto'<?if ($coupon_type_check == 'auto' || $cp_coupon_type != '6') {?> style='display:none;'<?}?>>
								<th height=33><span>제품 상세 쿠폰 노출 설정</span></th>
								<td class="td_con1">
									<select name=detail_auto class="select_selected CLS_detail_auto"  style="width:120px;padding-top:1pt;">
										<option value='N' <?=$selected[detail_auto]['N']?$selected[detail_auto]['N']:'selected'?>>노출 안함</option>
										<option value='Y' <?=$selected[detail_auto]['Y']?$selected[detail_auto]['Y']:''?>>노출 함</option>
									</select>&nbsp;
									<span id = 'ID_display_img_type' <?if ($cp_detail_auto == 'N' || $cp_detail_auto == '') {?>style='display:none;'<?}?>><input type = 'radio' name = 'display_img_type' class = 'CLS_display_img_type' id = 'ID_display_img_type1' value='1' <?=$checked[display_img_type]['1']?$checked[display_img_type]['1']:'checked'?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_display_img_type1>기본 이미지 사용</label>&nbsp;
									<input type = 'radio' name = 'display_img_type' class = 'CLS_display_img_type' id = 'ID_display_img_type2' value='2' <?=$checked[display_img_type]['2']?$checked[display_img_type]['2']:''?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=ID_display_img_type2>직접 업로드</label>&nbsp;&nbsp;<INPUT type=file size=40 name=display_img></span><div id='ID_display_def_img' style='padding-top:5px;<?if($cp_display_img_type !='1') {?>display:none;<?}?>'><IMG src="images/sample/market_couponsampleimg.gif"></div><div id='ID_display_up_img' style='padding-top:5px;<?if($cp_display_img_type !='2') {?>display:none;<?}?>'><?=$cp_display_img_html?></div></td>
							</tr>
							<tr id='ID_issue_max'<?if ($cp_coupon_type != '7') {?> style='display:none;'<?}?>>
								<th><span>발행 쿠폰 수량 설정</span></th>
								<td class="td_con1">
									<input onkeyup=strnumkeyup(this); maxLength=5 size=5 name=issue_max_no value = "<?=$cp_issue_max_no?$cp_issue_max_no:'0'?>" style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 장</td>
							</tr>
							</table>
                        </div>
					</td>
                </tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('<?=$submit_type?>');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span><?=$menu_title_name?> <?=$mode_text?> 안내</span></dt>
							<dd>- 
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			<form name=form2 action="coupon_productchoice2.php" method=post target=coupon_product>
			</form>
			<form name=mform action="market_coupon_selmembers_v3.php" method=post target=findpopup>
			<input type=hidden name=formname value="form1">
			</form>
			<form name=listform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=block value="<?=$_GET['block']?>">
			<input type=hidden name=gotopage value="<?=$_GET['gotopage']?>">
			<input type=hidden name=s_coupon_type value="<?=$_GET['s_coupon_type']?>">
			<input type=hidden name=search value="<?=$_GET['search']?>">
			<input type=hidden name=s_date value="<?=$_GET['s_date']?>">
			<input type=hidden name=e_date value="<?=$_GET['e_date']?>">
			<input type=hidden name=search_start value="<?=$_GET['search_start']?>">
			<input type=hidden name=search_end value="<?=$_GET['search_end']?>">
			<input type=hidden name=s_sale_type value="<?=$_GET['s_sale_type']?>">
			<input type=hidden name=s_use_type value="<?=$_GET['s_use_type']?>">
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<IFRAME name="hiddenframe" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
<?=$onload?>
<?php 
include("copyright.php");
