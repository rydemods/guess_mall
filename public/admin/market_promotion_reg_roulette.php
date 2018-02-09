<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}


 function insert_coupon_log( $sql, $type )
{
	$logText = "=======".date("Y-m-d H:i:s")."-------".PHP_EOL;
	$logText.= $sql.PHP_EOL;
	$log_folder = $Dir."admin/backup/roulette_log_".date("Ym");
	if( !is_dir( $log_folder ) ){
		mkdir( $log_folder, 0700 );
		chmod( $log_folder, 0777 );
	}
	$file = $log_folder."/coupon_".$type."_".date("Ymd").".txt";
	if( !is_file( $file ) ){
		$f = fopen( $file, "a+" );
		fclose( $f );
		chmod( $file, 0777 );
	}
	file_put_contents( $file, $logText, FILE_APPEND );
}


#########################################################
$page_type = $_REQUEST["page_type"];
$page_text = "룰렛이벤트";
if($page_type=="event"){
	$page_text = "이벤트";
}


$pidx=$_REQUEST["pidx"];
$idx=$_REQUEST['idx']; 
$mode=$_REQUEST['mode'];
$itemCount=(int)$_REQUEST["itemCount"];


$start_date = $_REQUEST["start_date"];
$start_date_time = $_REQUEST["start_date_time"].$_REQUEST["start_date_minute"];

$end_date = $_REQUEST["end_date"];
$end_date_time = $_REQUEST["end_date_time"].$_REQUEST["end_date_minute"];

$publication_date = $_REQUEST["publication_date"];
$bridx          = $_REQUEST["s_brand"];
if ( $bridx === null ) {
    $bridx[0] = 0;
    $bridxList = null;
} else {
    $bridxList      = "," . implode(",", $bridx) . ",";
}
$no_coupon      = $_REQUEST["no_coupon"]?$_REQUEST["no_coupon"]:"N";
$imagepath      = $cfg_img_path['timesale'];
$filedata       = new FILE($imagepath);
$image_type     = $_REQUEST['image_type'];
$image_type_m   = $_REQUEST['image_type_m'];
$hidden         = $_REQUEST['hidden'] ? $_REQUEST['hidden'] : 0;
$errmsg = $filedata->chkExt();

if($errmsg==''){
	$up_file = $filedata->upFiles();

}


// ===========================================================
// 브랜드리스트
// ===========================================================
#$sql    = "SELECT * FROM tblproductbrand WHERE display_yn = 1 ORDER BY bridx asc ";
$sql    = "SELECT * FROM tblproductbrand WHERE display_yn = 1 ORDER BY lower(brandname) asc ";
$result = pmysql_query($sql);

$arrBrandList = array();
while ( $row = pmysql_fetch_object($result) ) {
    $arrBrandList[$row->bridx] = $row->brandname;
}
pmysql_free_result($result);

$content = trim($_REQUEST["content"]);
$content = str_replace("'", "''", $content);

$content_m  = trim($_REQUEST["content_m"]);
$content_m  = str_replace("'", "''", $content_m);

if(ord($_REQUEST["mode2"])>0){
	$ppidx_ = $_REQUEST["ppidx"];
	$pidx_ = $_REQUEST["pidx"];
	$sql = "DELETE FROM tblpromotion WHERE idx = '{$ppidx_}' AND promo_idx = '{$pidx_}' ";
	pmysql_query($sql);
	echo "<script>alert('삭제되었습니다.')</script>";
}

$cqry="select count(*) from tblpromotion WHERE promo_idx='{$pidx}'"; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$count=$crow['count'];

$cqry="select count(*) from tblpromo "; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$mcount=$crow['count'];

$event_type = $_POST['event_type'];
$attendance_weekly_reward = $_POST['attendance_weekly_reward'];
$attendance_weekend_reward = $_POST['attendance_weekend_reward'];
$attendance_complete_reward = $_POST['attendance_complete_reward'];
$attendance_weekly_reward_point = $_POST['attendance_weekly_reward_point'] ?: 0;
$attendance_weekly_reward_coupon = implode("^", $_POST['attendance_weekly_reward_coupon']);
$attendance_weekend_reward_point = $_POST['attendance_weekend_reward_point'] ?: 0;
$attendance_weekend_reward_coupon = implode("^", $_POST['attendance_weekend_reward_coupon']);
$attendance_complete_reward_point = $_POST['attendance_complete_reward_point'] ?: 0;
$attendance_complete_reward_coupon = implode("^", $_POST['attendance_complete_reward_coupon']);

//일 경품 수량
$day_orders =$_REQUEST['day_order'];
if ($day_orders == 0) {
	$day_orders =$_REQUEST['day_order_temp'];
}

//포인트 유효기간
$point_expire_date =$_REQUEST['point_expire_date'];

// 쿠폰 설정 관련 정보
$CurrentTime = time();
$date_start=$_POST["date_start"];		// 유효 기간 설정 사용가능 시작날짜
$date_end=$_POST["date_end"];		// 유효 기간 설정 사용가능 종료날짜
$date_start=$date_start?$date_start:date("Y-m-d",$CurrentTime);
$date_end=$date_end?$date_end:date("Y-m-d",$CurrentTime);
$time= $_POST["time"];							// 유효 기간 설정 선택
$peorid							= $_POST["peorid"];						// 유효 기간 사용가능 일
if ($time=="D") {
	$date_start = str_replace("-","",$date_start)."00";
	$date_end = str_replace("-","",$date_end)."23";
} else {
	$date_start = ($peorid>0?"-":"").$peorid;
	$date_end = str_replace("-","",$date_end)."23";
}
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
$sale_max_money			= $_POST["sale_max_money"];		// 할인 상한 금액
$one_issue_limit				= $_POST["one_issue_limit"];			// 1회 발급 수량 (추가필드)
$one_issue_quantity		= $_POST["one_issue_quantity"];		// 1회 발급 설정수량 (추가필드)
$coupon_use_type			= $_POST["coupon_use_type"];		// 쿠폰 사용 방법
$coupon_is_mobile			= $_POST["coupon_is_mobile"];		// 쿠폰 사용 범위
$mini_type						= $_POST["mini_type"];					// 쿠폰사용제한 선택 (추가필드)
$mini_price					= $_POST["mini_price"];					// 쿠폰사용제한 구매 금액
$mini_quantity				= $_POST["mini_quantity"];				// 쿠폰사용제한 상품 수량 (추가필드)


$productcode					= $_POST["productcode"] ? $_POST["productcode"] : 'ALL';				// 카테고리/상품 선택 구분 - ALL : 전체 / CATEGORY : 카테고리 / GOODS : 상품 / BRANDSEASONS : 브랜드시즌
$set_productcode			= $_POST["set_productcode"];			// 카테고리/상품/브랜드시즌 리스트
$issue_type					= $_POST["issue_type"];					// 쿠폰 발급조건 - 회원가입시 자동발급 : M / 자동발급 : A
$detail_auto					= $_POST["detail_auto"];					// 제품 상세 쿠폰 노출 설정 - 노출안함으로 고정
$issue_code					= $_POST["issue_code"];				// 발급구분 - 0 : 일반, 1 : 지정, 2 : 자동

$issue_max_no				= $_POST["issue_max_no"];			// 발행 쿠폰 수량 설정 - 페이퍼 쿠폰 (추가필드)
//$imagepath					= $Dir.DataDir."shopimages/etc/";		// 제품 상세 쿠폰 직접 업로드 이미지 저장 URL
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
$repeat_id				= $_POST["repeat_id"];							// 동일인 재발급 가능여부 - 가능으로 고정
$issue_member_no	= $_POST["issue_member_no"];				// 보유가능 쿠폰 수 - NULL로 고정
$use_point				= $_POST["use_point"];							// 쿠폰과 등급회원 할인/적립 혜택 동시적용 유무 - 동시적용으로 고정
$delivery_type		= $_POST["delivery_type"];						// 쿠폰 사용시 배송비 포함 유무 - 미포함으로 고정
$use_card				= $_POST['use_card'];							// 사용카드에 따른 유무 - NULL로 고정
//--------------------------------------------- 사용 안하는 필드 끝 ---------------------------------------------//


//룰렛
$_roulette_seg_name = $_POST['roulette_seg_name']; // 상품명
$_roulette_seg_cnt = $_POST['roulette_seg_cnt']; //수량
$_roulette_seg_point = $_POST['roulette_seg_point']; //포인트 또는 할인률
$_roulette_seg_type = $_POST['roulette_seg_type']; // 룰렛 경품 type array ( C:쿠폰, P:포인트 ) ssk 2017-11-01
$_roulette_seg_ptype = $_POST['roulette_seg_point_type']; // 금액 또는 할인률 구분 표시 원 , %
$_roulette_coupon_seq = $_POST['roulette_coupon_seq']; // 룰렛 경품 중 쿠폰이 발행되었을 경우 수정하기 위한 SEQ array ssk 2017-11-01
$roulette_coupon_seq = implode(",",$_roulette_coupon_seq);

$keyArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');


$groupA = (int) $_roulette_seg_cnt[0];
$groupB = (int) $_roulette_seg_cnt[1];
$groupC = (int) $_roulette_seg_cnt[2];
$groupD = (int) $_roulette_seg_cnt[3];
$groupE = (int) $_roulette_seg_cnt[4];
$groupF = (int) $_roulette_seg_cnt[5];
$groupG = (int) $_roulette_seg_cnt[6];
$groupH = (int) $_roulette_seg_cnt[7];
$group = $groupA + $groupB  + $groupC  + $groupD  + $groupE  + $groupF  + $groupG  + $groupH;

$rouletArr = array();
for ($i=0; $i < sizeof($keyArr); $i++) {
	$rouletArr[] = $keyArr[$i].$i;
}

//shuffle($rouletArr);
$roulette_ticket_group = implode(",",$rouletArr);

//가져올때
//echo $rouletArr[1];


$roulette_segment = '';

for ($i=-0; $i < 8; $i++) {
	
	$roulette_segment .=$_roulette_seg_name[$i].":".$_roulette_seg_cnt[$i].":".$_roulette_seg_point[$i].":".$_roulette_seg_type[$i].":".$_roulette_seg_ptype[$i].",";

}
$roulette_ticket_group = implode(",",$rouletArr);

$tmpResult = $_POST['winner_list_content'];   // html제거
if ( trim($tmpResult) === "" ) {
    // 제거한 후 빈값이면 빈값으로 입력
    $winner_list_content = "";
} else {
    // 아니면 입력받은 그대로 입력
    $winner_list_content = $_POST['winner_list_content'];
//     $winner_list_content = str_replace("'", "''", $winner_list_content);
}

$couponData = array(); // 쿠폰 생성 및 수정을 위한 Data array


//exit;
$error_check = true;
switch($mode){
	case "del" :$seq=$_REQUEST['seq']; /*삭제할때 삭제할 로우보다 진열 순위가 낮은 로우를 한개씩 위로 올림*/
				$dcsql = "SELECT count(*) FROM tblpromo WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
				$dcres = pmysql_query($dcsql,get_db_conn());
				$dcrow=pmysql_fetch_array($dcres);
				if($dcrow[0]!=0){
					$dusql = "UPDATE tblpromo SET display_seq = display_seq-1 
						WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
					pmysql_query($dusql,get_db_conn());
				}
				/*메인 타이틀 삭제*/
				$dsql = "DELETE FROM tblpromo WHERE idx='{$pidx}'";
				pmysql_query($dsql);	
				
				/*상품 삭제*/
				$ddsql = "SELECT idx FROM tblpromotion WHERE promo_idx='{$pidx}'";
				$ddres = pmysql_query($ddsql);
				$ddrow= pmysql_fetch_object($ddres);
				for($i=0;$i<count($ddrow);$i++){	
					$dsql2 = "DELETE FROM tblspecialpromo WHERE special='".$ddrow->idx."'";
					pmysql_query($dsql2);
				}		
				/*서브 타이틀 삭제*/	 
				$dsql3 = "DELETE FROM tblpromotion WHERE promo_idx='{$pidx}' "; 
				pmysql_query($dsql3);		
				
				echo "<script>alert('삭제되었습니다.');</script>";
				echo "<script>window.parent.frames['topframe'].ClearCache('N');document.location.href='market_promotion_roulette.php';</script>";
				break; 
				
	case "ins" : $count=$count+1; $mcount= $mcount+1; break;	 
				
	case "ins_submit" : $ptitle = pmysql_escape_string($_POST["ptitle"]); $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
//						exdebug($up_file);

						$coupon_noti =''; // 쿠폰 등록시 얼럿창 추가 내용 
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = pmysql_escape_string($_POST["mtitle"]); $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$mcount++;
						
						$mnsql = "select idx from tblpromo order by idx desc";
						$mnres = pmysql_query($mnsql);
						$tempx = 1;
						while($mnrow = pmysql_fetch_object($mnres)){
							if($tempx <= $mnrow->idx){
								$tempx = $mnrow->idx+1;								
							}							
						}
						
						$misql = "insert into tblpromo (idx, title, thumb_img, thumb_img_m, banner_img, display_type, display_seq, rdate, hidden, ";
                        $misql.= "start_date, end_date, start_date_time, end_date_time, ";

                        if ( $publication_date != "" ) {
                            $misql.= "publication_date, ";
                        }

                        $misql.= "no_coupon,image_type, image_type_m, content, content_m, title_banner, banner_img_m, ";
                        $misql.= "event_type, attendance_weekly_reward, attendance_weekend_reward, attendance_complete_reward, ";
                        $misql.= "attendance_weekly_reward_point, attendance_weekly_reward_coupon, attendance_weekend_reward_point, ";
                        $misql.= "attendance_weekend_reward_coupon, attendance_complete_reward_point, attendance_complete_reward_coupon, ";
                        $misql.= "winner_list_content, attendance_weekly_icon, attendance_weekend_icon, bridx, bridx_list, ";
						$misql.= "roulette_ticket_group ,roulette_order_num , roulette_segment, roulette_product_id, day_orders, point_expire_date) ";
						$misql.= "values('".$tempx."', '{$mt}', '{$up_file['thumb_img'][0]['v_file']}', '{$up_file['thumb_img_m'][0]['v_file']}', ";
                        $misql.= "'{$up_file['banner_img'][0]['v_file']}', '{$mdt}', '{$mds}', current_date, {$hidden}, '{$start_date}', '{$end_date}','{$start_date_time}', '{$end_date_time}', ";

                        if ( $publication_date != "" ) {
                            $misql.= "'{$publication_date}', ";
                        }

                        $misql.= "'{$no_coupon}','{$image_type}', '{$image_type_m}', '{$content}', '{$content_m}', ";
                        $misql.= "'{$up_file['title_banner'][0]['v_file']}', '{$up_file['banner_img'][1]['v_file']}', ";
                        $misql.="'{$event_type}', '{$attendance_weekly_reward}', '{$attendance_weekend_reward}', ";
                        $misql.="'{$attendance_complete_reward}', {$attendance_weekly_reward_point}, '{$attendance_weekly_reward_coupon}', ";
                        $misql.="{$attendance_weekend_reward_point}, '{$attendance_weekend_reward_coupon}', ";
                        $misql.="{$attendance_complete_reward_point}, '{$attendance_complete_reward_coupon}', '{$winner_list_content}', ";
                        $misql.="'{$up_file['attendance_weekly_icon'][0]['v_file']}', '{$up_file['attendance_weekend_icon'][0]['v_file']}', {$bridx[0]}, '{$bridxList}' , ";
						$misql.="'{$roulette_ticket_group}', 0, '{$roulette_segment}', '{$roulette_coupon_seq}', '{$day_orders}', '{$point_expire_date}') ";

//						exdebug($misql);
						
                        pmysql_query($misql);
						if(!pmysql_error()){
							$pidx = $tempx;
							$pcode = array();
							for($aa=0;count($pt)>$aa;$aa++){
								$csql = "SELECT count(*) FROM tblpromotion where  promo_idx='{$tempx}'  ";
								$cres = pmysql_query($csql,get_db_conn());
								$crow=pmysql_fetch_array($cres);
								if($crow[0]!=$ps[$aa]+1){ /*새로 등록할때 지정한 진열순위가 맨 뒤가 아니라면 지정한 순위부터 뒤에 로우를 한칸씩 뒤로 민다.*/
									$usql = "UPDATE tblpromotion SET display_seq = display_seq+1
									WHERE idx = ( select * from (select idx where  promo_idx='{$tempx}' AND display_seq >= {$ps[$aa]}) as a)";
									pmysql_query($usql,get_db_conn());
								}
								//exdebug($usql);
								$isql = "INSERT INTO tblpromotion (	idx,
																title,
																info,
																display_seq,
																display_tem,
																rdate,
																promo_idx
																) ";
								$isql.= "values (  {$pidxs[$aa]},
								'{$pt[$aa]}',
								'{$pi[$aa]}',
								{$ps[$aa]},
								{$pte[$aa]},
								current_date,
								'{$tempx}'
								)";
								pmysql_query($isql,get_db_conn());
//								exdebug($isql);
//								exit;
								/*프로모션이 등록되었으면 관련 쿠폰 발행 
								 *처음 등록시만 발행하며 자동 발행된 쿠폰이 있을 경우는 해당 내용을 수정함 
								 *
								*/
								if(!pmysql_error()){
									// 쿠폰이 있는지 조회 없을 경우 생성 : tblcouponinfo
										//$result = createRouletteCoupon($mode, $tempx, $couponData);
									$sql = "select * from tblpromo where idx = '".$tempx."' limit 1";
									$result = pmysql_query($sql,get_db_conn());
									$ii=0;
									while ($row = pmysql_fetch_array($result)) {	
										foreach ($row as $key => $value) {
											$_roulette[$ii]->$key	= $value;
										}
										$ii+=1;
									}

									//made json
									$roulette = json_decode(json_encode($_roulette[0]),true);

									// fields 
									$roulette_title = $roulette['title']; // roulette main title
									$idx = $roulette['idx']; // roulette promotion idx
									$_roulette_segment = explode(',',$roulette['roulette_segment']); //roulette segment array data
									$_roulette_product_id = explode(',',$roulette['roulette_product_id']); //roulette product id
									
									$coupon_noti .= '\n-------------------------------------------------\n';
									$coupon_noti .= '룰렛이벤트 등록시 자동 등록된 일반 쿠폰 리스트 입니다.\n';
									$coupon_noti .= '-------------------------------------------------\n';

									foreach($_roulette_segment as $key => $s){
										$strSegment = explode(':',$s);
										$jj++;
										$seg[$jj] = $strSegment[0]; //name
										$num[$jj] = $strSegment[1]; //수량
										$sum[$jj] = $strSegment[2]; //포인트 및 할인률
										$rid[$jj] = $strSegment[3]; //type
										$ptype[$jj] = $strSegment[4]; //type
										$pcode[$jj] = $_roulette_product_id[$jj]; // 쿠폰 아이디 (포인트일 경우 0으로 셋팅)
										

//										exdebug($strSegment);
//										exdebug($seg);
//										exdebug($rid);
//										exdebug($num);
//										exdebug($sum);
//										exdebug($ptype);
//										exit;

										if ($rid[$jj] == 'C') {
											//생성제한 체크 
											list($coupon_made_limit,$amount_floor)=pmysql_fetch_array(pmysql_query("select made_limit, amount_floor from tblcoupon "));
										
											// 쿠폰 상세 정보
											$coupon_name				= '['.$roulette_title.'] '.$seg[$jj];				// 쿠폰 명
											$description					= '룰렛 이벤트 쿠폰-'.$roulette_title;					// 쿠폰 설명
											$sale2							= $ptype[$jj];							// 금액/할인율 선택 - 원, %
											$sale_money					= $sum[$jj];				// 쿠폰 할인 금액/할인률
											$amount_floor					= $amount_floor;				// 금액 절삭
											$issue_tot_no			= $num[$jj];						// 총 발행 쿠폰 수 - 무제한으로 고정


											//ins_submit (insert), mod_submit (modify), del
											if ($mode=="ins_submit" || $mode=="mod_submit") {
												//exdebug($_POST);
												//exit;
												if ($coupon_made_limit == '1' && $coupon_use_type == '2') echo "<script>alert('장바구니 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";
												if ($coupon_made_limit == '2' && $coupon_use_type == '1') echo "<script>alert('상품 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";

												if ($type!="mod_submit") {
													$coupon_code=substr(ceil(date("sHi").rand(10,99)/10*8)."000",0,8);
													// coupon_code check
													$csql = "select coupon_code from tblcouponinfo where coupon_code = '{$coupon_code}' limit 1";
													$cidxs = pmysql_query($csql);
													if ($cidxrow = pmysql_fetch_object($cidxs)){
														if($coupon_code == $cidxrow->coupon_code){
															$coupon_code = $coupon_code+1;								
														}							
													}

													$pcode[$jj] = $coupon_code; // 쿠폰 아이디 array
												}
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

												
												if(ord($issue_max_no)==0) $issue_max_no=0;

												$display_img_name = "";
												
												if ($use_con_type2 == 'N') { // 제외
													$in_productcode		= "";
													$out_productcode	= $productcode;
												} else if ($use_con_type2 == 'Y') { //포함
													$in_productcode		= $productcode;
													$out_productcode	= "";
												}
												
												if ($mode!="mod_submit") {
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
													'Y',
													'{$time}')";
													//exdebug($sql);
													//exit;
												}
//												exdebug($sql);
//												exit;

												insert_coupon_log( $sql , $mode ); //sql log print 2017-11-06 ssk
												//mode : ins_submit (insert), mod_submit (modify), del
												pmysql_query($sql,get_db_conn());
												if(!pmysql_errno()) {	
													if ($mode=="mod_submit") {
														
														//echo "<script>alert('쿠폰 수정이 완료 되었습니다._{$jj}'); </script>";
													} else {
														$coupon_noti .= '# 등록 : '.$coupon_name.' \n';
														//echo "<script>alert('쿠폰 등록이 완료 되었습니다._{$jj}'); </script>";
													}
													//exit;
												} else {		
													$coupon_noti .= '# 실패 : '.$coupon_name.' (관리자문의필요)\n';
													//echo "<script>alert('오류가 발생하였습니다._{$jj}');</script>";
													//exdebug($sql);
													//exit;
													$error_check = false;
												}
											}
										}
									}
								}
							}
								
							
							/*메인테이블 업데이트*/
							// created coupon code update
							$_roulette_product_id = implode(",",$pcode);
							$musql = "update tblpromo set roulette_product_id = '{$_roulette_product_id}' ";
							$musql .= " where idx='{$pidx}' ";	
							insert_coupon_log( $musql , $mode ); 
							pmysql_query($musql);
							
							echo "<script>alert('룰렛 이벤트가 등록되었습니다.{$coupon_noti}');</script>";
							if (!$error_check) {
								//exdebug($musql);
								//exit;
							}
							echo "<script>window.parent.frames['topframe'].ClearCache('N');document.location.href='market_promotion_roulette.php';</script>";
							break;

						}else{
							echo "<script>alert('오류가 발생하였습니다.');</script>";
						}
						
	case "mod_submit" :  $ptitle = pmysql_escape_string($_POST["ptitle"]); $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = pmysql_escape_string($_POST["mtitle"]); $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$arrPromoSeq = explode(",", $_POST["ppromo_seq"]);
						$promo_code = $_POST["promo_code"];
						$promo_view = $_POST["promo_view"];


						$musql = "SELECT display_seq FROM tblpromo WHERE idx='{$pidx}' ";
						$mures = pmysql_query($musql);	
						$murow = pmysql_fetch_array($mures);
						
						if($murow[0]!=$mds){ /*수정할때 지정한 진열 순위에 따라 나머지 로우들도 진열 순위를 수정함*/
							if($murow[0]<$mds){
								$usql = "UPDATE tblpromo SET display_seq = display_seq-1 
										WHERE idx = ( select * from (select idx where display_seq between {$murow[0]} and {$mds}) as a)";
								pmysql_query($usql,get_db_conn());
							} 
							if($murow[0]>$mds){
								$usql = "UPDATE tblpromo SET display_seq = display_seq+1 
										WHERE idx = ( select * from (select idx where display_seq between {$mds} and {$murow[0]}) as a)";
								pmysql_query($usql,get_db_conn());	
							}
						}
						 /*메인테이블 업데이트*/
						$musql = "update tblpromo set title = '{$mt}', display_type = '{$mdt}', display_seq =  '{$mds}', promo_code =  '{$promo_code}', promo_view =  '{$promo_view}', 
								start_date = '{$start_date}', end_date = '{$end_date}', start_date_time = '{$start_date_time}', end_date_time = '{$end_date_time}', ";

                        if ( $publication_date != "" ) { $musql.= "publication_date = '{$publication_date}', "; }
//                        if ( $bridx[0] !== null ) { $musql.= "bridx = {$bridx[0]}, "; }
//                        if ( $bridxList != "" ) { $musql.= "bridx_list = '{$bridxList}', "; }

						$musql.= "roulette_ticket_group = '{$roulette_ticket_group}', ";
						$musql.= "roulette_order_num = 0, ";
						$musql.= "roulette_segment = '{$roulette_segment}', ";
						$musql.= "point_expire_date = '{$point_expire_date}', ";
                        $musql.= "bridx = {$bridx[0]}, "; 
                        $musql.= "bridx_list = '{$bridxList}', ";

                        $musql.= "no_coupon = '{$no_coupon}', image_type = '{$image_type}', image_type_m = '{$image_type_m}', ";
                        $musql.= "content = '{$content}', content_m = '{$content_m}' ";						
                        $musql.= ", hidden = {$hidden}";

						if($up_file['thumb_img'][0]['v_file']){
							$musql.=", thumb_img = '{$up_file['thumb_img'][0]['v_file']}' ";

							list($temp_banner_img)=pmysql_fetch("select thumb_img from tblpromo where idx='{$pidx}'");
							if($temp_banner_img) @unlink($imagepath.$temp_banner_img);
						}

						if($up_file['thumb_img_m'][0]['v_file']){
							$musql.=", thumb_img_m = '{$up_file['thumb_img_m'][0]['v_file']}' ";

							list($temp_banner_img)=pmysql_fetch("select thumb_img_m from tblpromo where idx='{$pidx}'");
							if($temp_banner_img) @unlink($imagepath.$temp_banner_img);
						}

						if($up_file['banner_img'][0]['v_file']){
							$musql.=", banner_img = '{$up_file['banner_img'][0]['v_file']}' ";

							list($temp_banner_img)=pmysql_fetch("select banner_img from tblpromo where idx='{$pidx}'");
							if($temp_banner_img) @unlink($imagepath.$temp_banner_img);
						}
						if($up_file['banner_img'][1]['v_file']){
							$musql.=", banner_img_m = '{$up_file['banner_img'][1]['v_file']}' ";

							list($temp_banner_m_img)=pmysql_fetch("select banner_img_m from tblpromo where idx='{$pidx}'");
							if($temp_banner_m_img) @unlink($imagepath.$temp_banner_m_img);
						}
						// 핏플랍 모바일 타이틀 베너
						if($up_file['title_banner'][0]['v_file']){
							$musql.=", title_banner = '{$up_file['title_banner'][0]['v_file']}' ";

							list($temp_tbanner_img)=pmysql_fetch("select title_banner from tblpromo where idx='{$pidx}'");
							if($temp_tbanner_img) @unlink($imagepath.$temp_tbanner_img);
						}

                        // 출석체크시 설정값 업데이트 
                        $musql .= ", event_type = '{$event_type}', ";
                        $musql .= "attendance_weekly_reward = '{$attendance_weekly_reward}', ";
                        $musql .= "attendance_weekend_reward = '{$attendance_weekend_reward}', ";
                        $musql .= "attendance_complete_reward = '{$attendance_complete_reward}', ";

                        if ( $attendance_weekly_reward == "0" ) {
                            $musql .= "attendance_weekly_reward_point = {$attendance_weekly_reward_point}, ";
                            $musql .= "attendance_weekly_reward_coupon = '', ";
                        } else {
                            $musql .= "attendance_weekly_reward_point = 0, ";
                            $musql .= "attendance_weekly_reward_coupon = '{$attendance_weekly_reward_coupon}', ";
                        }

                        if ( $attendance_weekend_reward == "0" ) {
                            $musql .= "attendance_weekend_reward_point = {$attendance_weekend_reward_point}, ";
                            $musql .= "attendance_weekend_reward_coupon = '', ";
                        } else {
                            $musql .= "attendance_weekend_reward_point = 0, ";
                            $musql .= "attendance_weekend_reward_coupon = '{$attendance_weekend_reward_coupon}', ";
                        }

                        if ( $attendance_complete_reward == "0" ) {
                            $musql .= "attendance_complete_reward_point = {$attendance_complete_reward_point}, ";
                            $musql .= "attendance_complete_reward_coupon = '' ";
                        } else {
                            $musql .= "attendance_complete_reward_point = 0, ";
                            $musql .= "attendance_complete_reward_coupon = '{$attendance_complete_reward_coupon}' ";
                        }

                        // 당첨자발표 내용
                        $musql .= ", winner_list_content = '{$winner_list_content}' ";

						if($up_file['attendance_weekly_icon'][0]['v_file']){
							$musql.=", attendance_weekly_icon = '{$up_file['attendance_weekly_icon'][0]['v_file']}' ";
                        }

						if($up_file['attendance_weekly_mobile_icon'][0]['v_file']){
							$musql.=", attendance_weekly_mobile_icon = '{$up_file['attendance_weekly_mobile_icon'][0]['v_file']}' ";
                        }

						if($up_file['attendance_weekend_icon'][0]['v_file']){
							$musql.=", attendance_weekend_icon = '{$up_file['attendance_weekend_icon'][0]['v_file']}' ";
                        }

						if($up_file['attendance_weekend_mobile_icon'][0]['v_file']){
							$musql.=", attendance_weekend_mobile_icon = '{$up_file['attendance_weekend_mobile_icon'][0]['v_file']}' ";
                        }

						$musql .= " where idx='{$pidx}' ";						
                        //echo $musql . "<br/>";

						pmysql_query($musql);

						$promotion_sql = "SELECT seq FROM tblpromotion WHERE promo_idx='{$pidx}'";
						$promotion_result = pmysql_query($promotion_sql,get_db_conn());
						$arrTempSeq = array();
						while($promotion_row=pmysql_fetch_object($promotion_result)) {
							$arrTempSeq[] = $promotion_row->seq;
						}
						$arrDeletePromotion = array_diff($arrTempSeq, $arrPromoSeq);
						foreach($arrDeletePromotion as $kk => $vv){
							$mdsql = "DELETE FROM tblpromotion WHERE seq='{$vv}'";
							pmysql_query($mdsql);
							$mdsql = "DELETE FROM tblspecialpromo WHERE special='{$vv}'";
							pmysql_query($mdsql);
						}

						for($aa=0;count($pt)>$aa;$aa++){
							if($arrPromoSeq[$aa] != 'undefined' && $arrPromoSeq[$aa]){	//$arrPromoSeq[$aa] 조건 추가 by PTY - 2014.10.14
								$isql = "UPDATE tblpromotion SET idx = {$pidxs[$aa]}, title = '{$pt[$aa]}', info = '{$pi[$aa]}', display_seq = {$ps[$aa]}, display_tem = {$pte[$aa]}, rdate = current_date, promo_idx = '{$pidx}' WHERE seq = '".$arrPromoSeq[$aa]."'"; 
							}else{
								
								$isql = "INSERT INTO tblpromotion 
											(idx, title, info, display_seq, display_tem, rdate, promo_idx) ";
								$isql.= "values 
											({$pidxs[$aa]}, '{$pt[$aa]}', '{$pi[$aa]}', {$ps[$aa]}, {$pte[$aa]}, current_date, '{$pidx}')"; 
							}
							pmysql_query($isql);
						}
							
						/*프로모션이 등록되었으면 관련 쿠폰 발행 
						 *처음 등록시만 발행하며 자동 발행된 쿠폰이 있을 경우는 해당 내용을 수정함 
						 *
						*/
						if(!pmysql_error()){
							// 쿠폰이 있는지 조회 없을 경우 생성 : tblcouponinfo
								//$result = createRouletteCoupon($mode, $tempx, $couponData);
							$sql = "select * from tblpromo where idx = '".$pidx."' limit 1";
							$result = pmysql_query($sql,get_db_conn());
							$ii=0;
							while ($row = pmysql_fetch_array($result)) {	
								foreach ($row as $key => $value) {
									$_roulette[$ii]->$key	= $value;
								}
								$ii+=1;
							}

							//made json
							$roulette = json_decode(json_encode($_roulette[0]),true);

							// fields 
							$roulette_title = $roulette['title']; // roulette main title
							$idx = $roulette['idx']; // roulette promotion idx
							$_roulette_segment = explode(',',$roulette['roulette_segment']); //roulette segment array data
							$_roulette_product_id = explode(',',$roulette['roulette_product_id']); //roulette product id
							
							$coupon_noti .= '\n-------------------------------------------------\n';
							$coupon_noti .= '룰렛이벤트 등록시 자동 등록된 일반 쿠폰 리스트 입니다.\n';
							$coupon_noti .= '-------------------------------------------------\n';

							foreach($_roulette_segment as $key => $s){
								$strSegment = explode(':',$s);
								$jj++;
								$seg[$jj] = $strSegment[0]; //name
								$num[$jj] = $strSegment[1]; //수량
								$sum[$jj] = $strSegment[2]; //포인트 및 할인률
								$rid[$jj] = $strSegment[3]; //type
								$ptype[$jj] = $strSegment[4]; //type
								$pcode[$jj] = $_roulette_product_id[$jj]; // 쿠폰 아이디 (포인트일 경우 0으로 셋팅)
								

							
								if ($rid[$jj] == 'C') {
									//생성제한 체크 
									list($coupon_made_limit,$amount_floor)=pmysql_fetch_array(pmysql_query("select made_limit, amount_floor from tblcoupon "));
								
									// 쿠폰 상세 정보
									$coupon_name				= $seg[$jj];				// 쿠폰 명
									$description					= '룰렛 이벤트 쿠폰-'.$roulette_title;					// 쿠폰 설명
									$sale2							= $ptype[$jj];							// 금액/할인율 선택 - 원, %
									$sale_money					= $sum[$jj];				// 쿠폰 할인 금액/할인률
									$amount_floor					= $amount_floor;				// 금액 절삭
									$issue_tot_no			= $num[$jj];						// 총 발행 쿠폰 수 - 무제한으로 고정


									//ins_submit (insert), mod_submit (modify), del
									if ($mode=="ins_submit" || $mode=="mod_submit") {
										//exdebug($_POST);
										//exit;
										if ($coupon_made_limit == '1' && $coupon_use_type == '2') echo "<script>alert('장바구니 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";
										if ($coupon_made_limit == '2' && $coupon_use_type == '1') echo "<script>alert('상품 쿠폰만 등록 가능 합니다.');location.href='".$_SERVER['PHP_SELF']."';</script>";

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

										
										if(ord($issue_max_no)==0) $issue_max_no=0;

										$display_img_name = "";
										
										if ($use_con_type2 == 'N') { // 제외
											$in_productcode		= "";
											$out_productcode	= $productcode;
										} else if ($use_con_type2 == 'Y') { //포함
											$in_productcode		= $productcode;
											$out_productcode	= "";
										}
										
										if ($mode=="mod_submit") {
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
											$sql.= "time_type						= '{$time}' WHERE coupon_code='{$pcode[$jj]}'";
											
										}
										//exdebug($sql);
										//exit;
										insert_coupon_log( $sql , $mode ); //sql log print 2017-11-06 ssk
										//mode : ins_submit (insert), mod_submit (modify), del
										pmysql_query($sql,get_db_conn());
										if(!pmysql_errno()) {	
											if ($mode=="mod_submit") {
												
												//echo "<script>alert('쿠폰 수정이 완료 되었습니다._{$jj}'); </script>";
											} else {
												$coupon_noti .= '# 등록 : '.$coupon_name.' \n';
												//echo "<script>alert('쿠폰 등록이 완료 되었습니다._{$jj}'); </script>";
											}
											//exit;
										} else {		
											$coupon_noti .= '# 실패 : '.$coupon_name.' (관리자문의필요)\n';
											//echo "<script>alert('오류가 발생하였습니다._{$jj}');</script>";
											//exdebug($sql);
											//exit;
											$error_check = false;
										}
									}
								}
							}
						}
						
						echo "<script>alert('수정되었습니다.');</script>";
						echo "<script>window.parent.frames['topframe'].ClearCache('N');document.location.href='market_promotion_roulette.php';</script>";
						break;
}


?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
 
<script language="JavaScript">
function tr_remove(){
	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	document.eventform.itemCount.value = itemCount;
	$(".table_style01 [name=promotable]:last").remove();
	
}

function chkfrm()	{
	var checkVal = true;
	var startDate = $("input[name='start_date']").val().trim();
    if ( $("#mtitle").val().trim() === "" ) {
		checkVal = false;
        alert("메인 타이틀을 입력해 주세요.");
        $("#mtitle").val("").focus();
        return false;
    }


    if ( $("input[name='start_date']").val().trim() === "" ) {
		checkVal = false;
        alert("노출 시작일을 입력해 주세요.");
        return false;
    }

    if ( startDate === "" ) {
		checkVal = false;
        alert("노출 마감일을 입력해 주세요.");
        return false;
    }

	if ($("input[name='end_date']").val().trim()> $("input[name='date_end']").val().trim())
	{
			alert("쿠폰 유효기간 종료일을 룰렛이벤트 기간보다 길게 설정해 주세요.");
			return false;
	}

    if ( $("input[name='point_expire_date']").val().trim() === "" ) {
		checkVal = false;
        alert("포인트 유효기간을 입력해 주세요.");
		$("input[name='point_expire_date']").val("").focus();
        return false;
    }
	
	if(checkVal === true) {
		$("input[name='banner_img[]']").each(function(i){ if($(this).val().trim() === "") {checkVal = false;alert(((i+1) == 1 ? "PC용" : "모바일용")+" 이미지 등록이 필요합니다. ");  return false;}
		});
	}
	if(checkVal === true) {
		$("input[name='roulette_seg_name[]']").each(function(i){ if($(this).val().trim() === "") {checkVal = false;alert((i+1)+" 번째 경품명을 입력해주세요."); $(this).val("").focus(); return false;}
		});
	}

	if(checkVal === true) {
		$("input[name='roulette_seg_cnt[]']").each(function(i){ if($(this).val().trim() === "") {checkVal = false;alert((i+1)+" 번째 경품수량을 입력해주세요."); $(this).val("").focus(); return false;}
		});
	}

	if(checkVal === true) {
		$("input[name='roulette_seg_point[]']").each(function(i){ if($(this).val().trim() === "") {checkVal = false;alert((i+1)+" 번째 경품금액을 입력해주세요."); $(this).val("").focus(); return false;}
		});
	}

	// 기 등록된 룰렛 체크
	if(checkVal === true) {
		var param = {
			mode:'date',
			date:startDate,
		}

		$.ajax({
			url: 'market_promotion_roulette_indb.php',
			type:'get',
			data: param,
			dataType: 'text',
			async: false,
			success: function(data) {
				if(data != '0'){
					alert(data);
					checkVal = false;
				}
			}
		});
	}

	if (checkVal)
	{
		var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
		var mode = document.eventform.mode.value;
		if(mode=="ins"){  
			if(confirm("등록하시겠습니까?")){
				document.eventform.mode.value = "ins_submit";
			} else {
				return false;
			}
		}else if(mode=="mod"){
			if(confirm("수정하시겠습니까?")){
				document.eventform.mode.value = "mod_submit";
			} else {
				return false;
			}	
		} 
		//promo_seq
		for(var i=1;i<=itemCount;i++){ 
			for(var ii=0;ii<6;ii++){
				var itemname
				var hiddenname
				switch(ii){
					case 0 : itemname = ".item"+i+" [name=title]";	
							hiddenname = document.eventform.ptitle;						
							break;
					case 1 : itemname = ".item"+i+" [name=info]";	
							hiddenname = document.eventform.pinfo;
							break;
					case 2 : itemname = ".item"+i+" [name=display_seq]";	
							hiddenname = document.eventform.pseq;
							break;
					case 3 : itemname = ".item"+i+" [name=display_tem]";	
							hiddenname = document.eventform.ptem;
							break;
					case 4 : itemname = ".item"+i+" [name=ppidx]";	
							hiddenname = document.eventform.pppidx;
							break;
					case 5 : itemname = ".item"+i+" [name=promo_seq]";	
							hiddenname = document.eventform.ppromo_seq;
							break;
				}						
				if(hiddenname.value==""){
					hiddenname.value =$(itemname).val();
				}else{ 
					hiddenname.value = hiddenname.value+","+$(itemname).val();
				}	
			}
		}

	} else {
		return false;
	}
	
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span><?=$page_text?> 관리</span></p></div></div>
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

	<div class="title_depth3"><?=$page_text?> <?if($mode=="ins" || $mode=="copy"){echo "등록";}else{echo "수정";} ?>
		
			
		<?if($mode=="mod"){?>		
		<a href="/admin/market_promotion_product_new.php?pidx=<?=$pidx?>" target="_self">
			<img align="right" id="add_prod" src="/admin/images/btn_promo_product.gif" alt="상품등록"/></a>&nbsp;
		<a href="/front/promotion_roulette.php?pidx=<?=$pidx?>" target="_blank">
			<img align="right" src="/admin/images/btn_preview.gif" alt="미리보기"/></a>
		<?}?>	
	</div>



<form name="eventform" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="return chkfrm();">
	<input type="hidden" name="ptitle">
	<input type="hidden" name="pinfo">
	<input type="hidden" name="pseq">
	<input type="hidden" name="ptem">
	<input type="hidden" name="pppidx">
	<input type="hidden" name="ppromo_seq">
	<input type="hidden" name="itemCount">
	<input type="hidden" name="mode" value="<?=($mode == 'copy' ? 'ins' : $mode)?>">
	<input type="hidden" name="idx" value="<?=$idx?>">
	<input type="hidden" name="pidx" value="<?=($mode == 'copy' ? '' : $pidx)?>">
	<input type="hidden" name="testw" value="">
	<INPUT type = 'hidden' name='coupon_type' value='16'>
	<INPUT type = 'hidden' name='join_rote' value='A'>
	<INPUT type = 'hidden' name='sel_gubun' value='A'>
	<INPUT type = 'hidden' name='sel_group' value=''>
	<INPUT type = 'hidden' name='one_issue_limit' value='1'>
	<INPUT type = 'hidden' name='one_issue_quantity' value=''>
	<INPUT type = 'hidden' name='coupon_use_type' value='2'>
	<INPUT type = 'hidden' name='coupon_is_mobile' value='A'>
	<INPUT type = 'hidden' name='issue_days_ago' value='0'>
	<INPUT type = 'hidden' name='order_accept_quantity' value='0'>
	<INPUT type = 'hidden' name='order_accept_price' value='0'>
	<INPUT type = 'hidden' name='set_productcode' value=''>
	<INPUT type = 'hidden' name='detail_auto' value='N'>
	<INPUT type = 'hidden' name='display_img_type' value='1'>
	<INPUT type = 'hidden' name='display_img' value=''>
	<INPUT type = 'hidden' name='old_display_img' value=''>
	<INPUT type = 'hidden' name='sel_group' value=''>
	<INPUT type = 'hidden' name='productcode' value="ALL">
	<INPUT type = 'hidden' name='use_point' value = 'Y'>
	<INPUT type = 'hidden' name='sale_type' value = '-'>
	<INPUT type = 'hidden' name='sale_max_money' value = '0'>
	<INPUT type = 'hidden' name='bank_only' value = 'N'>
	<INPUT type = 'hidden' name='delivery_type' value = 'N'>
	<INPUT type = 'hidden' name='use_con_type1' value='N'>
	<INPUT type = 'hidden' name='use_con_type2' value='Y'>
	<INPUT type = 'hidden' name='use_card' value=''>
	<INPUT type = 'hidden' name="repeat_id" value="Y">
	<INPUT type = 'hidden' name="issue_member_no" value="1">
	<INPUT type = 'hidden' name='issue_type' value = 'N'>
	<INPUT type = 'hidden' name='issue_code' value = '0'>
	<INPUT type = 'hidden' name='issue_max_no' value='0'>
	<INPUT type = 'hidden' name="issue_selmembers" value=''>
	<INPUT type = 'hidden' name="issue_excelmembers" value=''> 
	<INPUT type = 'hidden' name="image_type" value='F'>
	<INPUT type = 'hidden' name="image_type_m" value='F'>
		<!-- 테이블스타일01 -->
		<div class="table_style01 pt_20" style="position:relative">
			<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<?php
				$msql = "SELECT * FROM tblpromo WHERE idx = '{$pidx}'";
				$mres = pmysql_query($msql);
				$mrow = pmysql_fetch_array($mres);

				$roulArr = explode(",",$mrow['roulette_segment']); // 룰렛 경품 정보
				$roulProdArr = explode(",",$mrow['roulette_product_id']); //쿠폰ID 포인트면 0 
				$cpArr = array(); // 생성된 쿠폰 리스트
				$cpProdId = '';
				foreach ($roulProdArr as $key => $prod) {
					if ($prod > 0) { // 쿠폰이 생성되었을 경우 생성 coupon id로 정보 검색해서 배열에 담는다.
						$cmsql = "select coupon_code, mini_type, mini_price, mini_quantity, time_type, date_start, date_end from tblcouponinfo WHERE coupon_code = '{$prod}' limit 1";
						$cmres = pmysql_query($cmsql);
						$cmrow = pmysql_fetch_array($cmres);
						if ($prod == $cmrow['coupon_code']) {
							$cpProdId = $cmrow['coupon_code'];
							$cpArr[$prod]['coupon_code'] = $cmrow['coupon_code'];
							$cpArr[$prod]['mini_type'] = $cmrow['mini_type'];
							$cpArr[$prod]['mini_price'] = $cmrow['mini_price'];
							$cpArr[$prod]['mini_quantity'] = $cmrow['mini_quantity'];
							$cpArr[$prod]['time_type'] = $cmrow['time_type'];
							$cpArr[$prod]['date_start'] = $cmrow['date_start'];
							$cpArr[$prod]['date_end'] = $cmrow['date_end'];
						} else {
							$cpArr[$prod]['coupon_code'] = $prod;
							$cpArr[$prod]['mini_type'] = '';
							$cpArr[$prod]['mini_price'] = '';
							$cpArr[$prod]['mini_quantity'] = '';
							$cpArr[$prod]['time_type'] = '';
							$cpArr[$prod]['date_start'] = '';
							$cpArr[$prod]['date_end'] = '';
						}
					}
				}

				//todo 사용제한에 대한 변수 적용 확인 후 수정 
				if ($cpProdId > 0) {
					//사용 제한
					//$cpArr[$cpProdId]['coupon_code'];
					$cp_mini_type = $cpArr[$cpProdId]['mini_type'];
					$checked[mini_type][$cp_mini_type]	= "checked";
					if ($cp_mini_type == 'P') {
						$cp_mini_price		= $cpArr[$cpProdId]['mini_price'];
					} else if ($cp_mini_type == 'Q') {
						$cp_mini_quantity	= $cpArr[$cpProdId]['mini_quantity'];
					}
						
					//유효 기간
					$cp_time	= $cpArr[$cpProdId]['time_type'];
					$selected[time_type][$cp_time]	= "selected";
					if($cpArr[$cpProdId]['date_start']>0) {
						$cp_date_start	=substr($cpArr[$cpProdId]['date_start'],0,4)."-".substr($cpArr[$cpProdId]['date_start'],4,2)."-".substr($cpArr[$cpProdId]['date_start'],6,2);
						$cp_date_end	= substr($cpArr[$cpProdId]['date_end'],0,4)."-".substr($cpArr[$cpProdId]['date_end'],4,2)."-".substr($cpArr[$cpProdId]['date_end'],6,2);
					} else {
						$cp_peorid	= $cpArr[$cpProdId]['date_start'];
						if ( $mode == 'copy' ) {
							$tmp_cp_peorid = $cp_peorid*-1;
							$cp_date_end	= date('Y-m-d', strtotime("+{$tmp_cp_peorid} day"));
						} else {
							$cp_date_end	= substr($cpArr[$cpProdId]['date_end'],0,4)."-".substr($cpArr[$cpProdId]['date_end'],4,2)."-".substr($cpArr[$cpProdId]['date_end'],6,2);
						}
					}
				}
				
                // 출석체크시 쿠폰설정값            
                $arrTmp = array('attendance_weekly_reward_coupon', 'attendance_weekend_reward_coupon', 'attendance_complete_reward_coupon');
                
                $idx = 0;
                foreach ( $arrTmp as $fieldName ) {
                    $coup_temp = explode("^", $mrow[$fieldName]);
                    $subwhere = "and coupon_code in ('".implode("','", $coup_temp)."')";                            
                    $coup_sql = "SELECT coupon_code, coupon_name FROM tblcouponinfo WHERE 1=1 $subwhere";                
                    //echo $coup_sql;                
                    $coup_result = pmysql_query($coup_sql,get_db_conn());
                    while($coup_row = pmysql_fetch_array($coup_result)){
                        $thisCoupon[$idx][] = $coup_row;
                    }

                    $idx++;
                }

			?>
	
				<th colspan="2">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>이벤트 등록 정보</b></li>
					</ul>
				</div>				
				</th>
			</tr>
			<tr> 
				<th><span>메인 타이틀</span></th>
				<td><input type="text" name="mtitle" id="mtitle" style="width:50%" value="<?=$mrow['title']?>" alt="타이틀" />
				<input type="hidden" name="event_type" value="5" />
				</td>
			</tr>
			<!--
			<tr>
				<th><span>이벤트 종류</span></th>
				<td>    
					<input type="radio" name="event_type" value="5" checked />룰렛이벤트
				</td>
			</tr>
			
			<tr>
				<th style="border-top: 1px solid black; border-left: 1px solid black;"><span>썸네일 이미지(PC)<br>&nbsp;&nbsp;&nbsp;( 345 * 117 )</span></th>
				<td style="border-top: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="thumb_img[]" alt="썸네일 이미지" />
				<?
					if($mrow['thumb_img'] && $mode != 'copy'){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['thumb_img']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>-->
			<tr id="img_F">
				<th>
					<span>룰렛 이미지 (PC)<br>&nbsp;&nbsp;&nbsp;( 700 * 700 )</span>
				</th>
				<td>
				<input type="file" name="banner_img[]" class="file-upload" alt="본문 이미지" />
				<?
					if($mrow['banner_img'] && $mode != 'copy'){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['banner_img']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<!--<tr>
				<th style="border-top: 1px solid black; border-left: 1px solid black;"><span>썸네일 이미지(모바일)<br>&nbsp;&nbsp;&nbsp;( 385 * 109 )</span></th>
				<td style="border-top: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="thumb_img_m[]" alt="썸네일 이미지" />
				<?
					if($mrow['thumb_img_m'] && $mode != 'copy'){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['thumb_img_m']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>-->
			<tr id="img_FM">
				<th><span>룰렛 이미지 (모바일)<br>&nbsp;&nbsp;&nbsp;( 700 * 700 )</span></th>
				<td>
				<input type="file" name="banner_img[]" alt="본문 이미지" />
				<?
					if($mrow['banner_img_m'] && $mode != 'copy'){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['banner_img_m']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			
			<tr>
				<th><span>전시 상태</span></th>
				<td>
					<select name="display_type" id="display_type">
						<option value="A" <?if($mrow['display_type']=='A') echo "selected";?>>모두</option>
						<option value="P" <?if($mrow['display_type']=='P') echo "selected";?>>PC만</option>
						<option value="M" <?if($mrow['display_type']=='M') echo "selected";?>>모바일만</option>
						<option value="N" <?if($mrow['display_type']=='N') echo "selected";?>>보류</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><span>노출</span></th>
				<td>
                    <select name="hidden">
						<option value="0" <?if($mrow['hidden']=='0') echo "selected";?>>비노출</option>
                        <option value="1" <?if($mrow['hidden']=='1' && $mode != 'copy') echo "selected";?>>노출</option>
					</select>
				</td>
			</tr>
			<TR>
				<th><span>노출 기간</span></th>
				<TD class="td_con1">
					<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=start_date value="<?=($mode == 'copy' ? date('Y-m-d') : $mrow['start_date'])?>" class="input_bd_st01">
					<select name="start_date_time" class="input_bd_st01">
						<?
						for ($i=0; $i<=23; $i++) { 
							$i = $i<10?"0".$i:$i;	
							if(substr($mrow['start_date_time'],0,2)==$i){
								echo "<option value=\"$i\" selected>$i</option>\n"; 	
							}else{
								echo "<option value=\"$i\">$i</option>\n"; 
							}

							
						} 
						?>
					</select>시
					<select name="start_date_minute" class="input_bd_st01">
						<?
						for ($i=0; $i<=59; $i++) { 
							$i = $i<10?"0".$i:$i;	
							if(substr($mrow['start_date_time'],2,4)==$i){
								echo "<option value=\"$i\" selected>$i</option>\n"; 	
							}else{
								echo "<option value=\"$i\">$i</option>\n"; 
							}
						} 
						?>
					</select>분
					부터  
					<INPUT type=text style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=end_date value="<?=($mode == 'copy' ? date('Y-m-d') : $mrow['end_date'])?>" class="input_bd_st01">
					<select name="end_date_time"class="input_bd_st01">
						<?
						for ($i=0; $i<=23; $i++) { 
							$i = $i<10?"0".$i:$i;	
							if(substr($mrow['end_date_time'],0,2)==$i){
								echo "<option value=\"$i\" selected>$i</option>\n"; 	
							}else{
								echo "<option value=\"$i\">$i</option>\n"; 
							}
						} 
						?>
					</select>시
					<select name="end_date_minute"class="input_bd_st01">
						<?
						for ($i=0; $i<=59; $i++) { 
							$i = $i<10?"0".$i:$i;	
							if(substr($mrow['end_date_time'],2,4)==$i){
								echo "<option value=\"$i\" selected>$i</option>\n"; 	
							}else{
								echo "<option value=\"$i\">$i</option>\n"; 
							}
						} 
						?>
					</select>분
					<span>까지</span>
				</TD>
			</TR>	
			<tr id="fmobile" <?if($mrow['display_type']!='B') echo " style='display: none'";?>>
				<th><span>핏플랍 모바일 타이틀 배너</span></th>
				<td>
					<input type="file" name="title_banner[]" alt="본문 이미지" />
				<?
					if($mrow['title_banner']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['title_banner']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr>
				<th><span>영역 우선순위</span></th>
				<td>
					<select name="mdisplay_seq" id="mdisplay_seq">
					<?if($count==0){$count=1;} for($i=1; $i<=$mcount; $i++){?>
						<option value="<?=$i?>" <?if($mrow['display_seq']== $i) echo "selected";?>><?=$i?></option>
					<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<th><span>일 경품 배당 수량 설정</span></th>
				<td><?=$mrow['title_banner']?>
					<INPUT onkeyup="strnumkeyup(this); if ($.isNumeric(this.value)){ sumPointTotal();}" maxLength=10 size=10 name=day_order value='<?=($mode != 'copy' && $mrow['day_orders']?$mrow['day_orders']:'0')?>' style='PADDING-RIGHT: 3px;TEXT-ALIGN: right'> 개 <span class="font_orange"> * 0원일 경우  (총 경품 수량 / 총 이벤트 기간 으로 자동 저장)</span>
					<INPUT type=hidden name=day_order_temp>
				</td>
			</tr>

			<tr class="roulette_info">
				<th><span>경품별설정</span></th>
				<td>
					<ul>
						<li style="font-weight: bold;padding-bottom: 2px;">
						<!-- keyup set-->
							<!--포인트 : 총 금액 (<span class='roulette-tot-price font_orange' id='tot_point_price'>0</span> 원), 쿠폰 : 총 금액 (<span class='roulette-tot-price font_orange' id='tot_coupon_price'>0</span> 원), 총 할인률 (<span class='roulette-tot-price font_orange' id='tot_coupon_rate'>0</span> %), --> 총 경품 수량 : <span class='roulette-tot-price font_orange' id='tot_coupon_count'>0</span> 
						</li>
					<?

					for($i=0; $i<=count($roulArr); $i++) { 
						$roulArrSub[$i] = explode(":",$roulArr[$i]);
					} 	
						for($i=0; $i<8; $i++) {?>
						<li>
							<input type="hidden" name="roulette_coupon_seq[]" value="<?=($mode != 'copy' && !empty($roulProdArr[$i]) ? $roulProdArr[$i] : '0')?>">
							<input type="hidden" name="roulette_seg_type[]" value="<?=$roulArrSub[$i][3] ? $roulArrSub[$i][3] :'P'?>">
							<input type="hidden" name="roulette_seg_point_type[]" value="<?=$roulArrSub[$i][4] ? $roulArrSub[$i][4]:'원'?>">
							<select name="roulette_seg_type_tmep[]"  onchange="changeStatus(this)" <?=($mode != 'copy' && $roulArrSub[$i][3] ? 'disabled style=\'background:#dddddd;\'':'')?> id="<?=$i?>">
								<option value="P" <?=$roulArrSub[$i][3] == 'P'?'selected':''?>>포인트</option>
								<option value="C" <?=$roulArrSub[$i][3] == 'C'?'selected':''?>>쿠폰</option>
							</select>
							경품명<?=($i+1)?> <input type="text" name="roulette_seg_name[]" value="<?=$roulArrSub[$i][0]?>">, 
							최대 당첨수량 <input onkeyup="strnumkeyup(this); if ($.isNumeric(this.value)){ sumPointTotal();}" type="text" name="roulette_seg_cnt[]" value="<?=($mode != 'copy' && $roulArrSub[$i][1] ? $roulArrSub[$i][1] : '')?>"  >,
							<SELECT style="WIDTH: 70px;padding-top: 1pt;" onchange="changerate(this)" name="roulette_seg_point_type_temp[]" class="select_selected" id="<?=$i?>" <?=($mode != 'copy' && $roulArrSub[$i][3] == 'P' ? 'disabled':(!$roulArrSub[$i][3] ? 'disabled' : ''))?> >
								<OPTION value='원' <?=$roulArrSub[$i][4] == '원'?'selected':''?>>금액</OPTION>
								<OPTION value='%' <?=$roulArrSub[$i][4] == '%'?'selected':''?>>할인율</OPTION>
							</SELECT>
							→
							<INPUT onkeyup="strnumkeyup(this); if ($.isNumeric(this.value)){ sumPointTotal();}" style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=10 size=10 name=roulette_seg_point[]   value='<?=$roulArrSub[$i][2]?$roulArrSub[$i][2]:''?>' maxlength="<?=$roulArrSub[$i][4] == '%'?'2':'10'?>">
							<INPUT class="input_hide1" readOnly size=1 value='<?=$roulArrSub[$i][4]?$roulArrSub[$i][4]:'원'?>' name=roulette_seg_ptype[]>
						</li>
						<?}?>
					</ul>
				</td>
			</tr>
			

		<tr>
				<th colspan="2">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
			            <li><b style='font-size:14px;'>쿠폰 사용 정보</b></li>
					</ul>
				</div>				
				</th>
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
					<input type="hidden" name="time" value="P">
					<!--<select name=time class="select_selected CLS_coupon_time"  style="width:120px;padding-top:1pt;">
						<option value='P' <?=$selected[time_type]['P']?$selected[time_type]['P']:'selected'?>>발급일 기준</option>
						<option value='D' <?=$selected[time_type]['D']?$selected[time_type]['D']:''?>>기간 기준</option>
					</select>-->
					
					<span id = 'ID_coupon_timeD' <?if ($cp_time != 'D') {?>style='display:none;'<?}?>><INPUT onfocus=this.blur(); onclick=Calendar(event) size=11 name=date_start value="<?=$cp_date_start?$cp_date_start:$date_start?>" class="input_selected"> 부터 </span><span id = 'ID_coupon_timeP' <?if ($cp_time != 'P' && $cp_time != '') {?>style='display:none;'<?}?>>발급일 부터 <INPUT onkeyup="strnumkeyup(this);settingDate();" style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid value='<?=$cp_peorid?($cp_peorid*-1):'0'?>'> 일 동안,  </span><INPUT  onfocus=this.blur();  size=11 name=date_end value="<?=$cp_date_end?$cp_date_end:date('Y-m-d')?>" class="input_selected" readonly> 까지<span id='ID_coupon_time_weekend' style='font-size: 12px;height:20px;<?if ($cp_coupon_type != '14') {?>display:none;<?}?>'>, <b class='font_blue2'>해당 주말 동안 </b></span><span id='ID_coupon_time_month' style='font-size: 12px;height:20px;<?if ($cp_coupon_type != '15') {?>display:none;<?}?>'>, <b class='font_blue2'>해당 월 동안 </b></span> 사용가능<span class="font_orange"> (유효기간 마지막일 23시59분59초 까지) <br> 설정된 이벤트 노출 기간을 기준으로 자동 세팅됩니다</span>
					</div>									
				</td>
			</tr>
			<tr>
				<th colspan="2">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>포인트 사용 정보</b></li>
					</ul>
				</div>				
				</th>
			</tr>
			<tr>
				<th><span>유효 기간 설정</span></th>
				<td>
					발급일 부터 <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=point_expire_date value='<?=$mrow['point_expire_date']?$mrow['point_expire_date']:''?>'> 일 동안									
				</td>
			</tr>
			<tr>


						<!--룰렛이벤트들-->
			<?if($mode=="ins"){?>
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
				<tr>
					<th><span><?=$page_text?> 타이틀</span></th>
					<td><input type="text" name="title" id="title" style="width:20%" value="" alt="타이틀" /></td>
				</tr>						
				<tr style='display:none;' >
					<th><span>타이틀 설명</span></th>
					<td><textarea name="info" style="width:500;height:100;"></textarea> </td> 
				</tr>
				<tr>
					<th><span>영역 우선순위</span></th>
					<td>
						<select name="display_seq"class="display_seq">
						<?if($count==0){$count=1;}else{ for($i=1; $i<=$count; $i++){?>
							<option value="<?=$i?>"><?=$i?></option>
						<?}}?>
						</select>
					</td>
				</tr>
				<tr >
					<th><span>상품 리스팅 템플릿</span></th>
					<td><select name="display_tem">
							<option value="1" >4단배열</option>
							<option value="2" >7단배열</option>
							<option value="3" >2단배열</option>
							<option value="4" >1단배열</option>
							<option value="5" >1단배열(슬라이드)</option>
						</select>
					</td>
				</tr>				
				<input type="hidden" name="ppidx" value="1"/>
			</table> 
			<?}else if($mode=="mod"){ 
			$qry="select * from tblpromotion where promo_idx='".$pidx."' ORDER by idx ASC "; 
			$res=pmysql_query($qry);
			$cnt=0;
			while($row=pmysql_fetch_array($res)){ $cnt++;?>
				<!-- img align="left" class="tr_remove" src="../admin/images/del_arrow.gif" align="right" alt="삭제하기" onclick="javascript:del_prmo(<?=$row['idx']?>)" -->
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item<?=$cnt?>">			
					<tr>
						<th><span><?=$page_text?> 타이틀</span></th>
						<td>
							<input type="text" name="title" id="title" style="width:20%" value="<?=$row['title']?>" alt="타이틀" />
						</td>
					</tr>						
					<tr style='display:none;' >
						<th><span>타이틀 설명</span></th>
						<td><textarea name="info" style="width:500;height:100;"><?=$row['info']?></textarea> </td>
					</tr>
					<tr>
						<th><span>영역 우선순위</span></th>
						<td>
							<select name="display_seq" class="display_seq">
							<?if($count==0){$count=1;} for($i=1; $i<=$count; $i++){?>
								<option value="<?=$i?>" <?if($row['display_seq']== $i) echo "selected";?>><?=$i?></option>
							<?}?>
							</select>
						</td>
					</tr> 
					<tr>
						<th><span>상품 리스팅 템플릿</span></th>
						<td><select name="display_tem">
							<option value="1"  <?if($row['display_tem']=='1') echo "selected";?>>4단배열</option>
							<option value="2"  <?if($row['display_tem']=='2') echo "selected";?>>7단배열</option>
							<option value="3"  <?if($row['display_tem']=='3') echo "selected";?>>2단배열</option>
							<option value="4"  <?if($row['display_tem']=='4') echo "selected";?>>1단배열</option>
							<option value="5"  <?if($row['display_tem']=='5') echo "selected";?>>1단배열(슬라이드)</option>
							
							</select>
						</td>
					</tr>
					
					<input type="hidden" name="ppidx" value="<?=$row['idx']?>"> 
					<input type="hidden" name="promo_seq" value="<?=$row['seq']?>"/>
				</table> 
				<!--<table>
				<tr>
					<td colspan="2" align="center">
					<img align="left" class="tr_remove" src="../admin/images/botteon_del.gif" align="right" alt="삭제하기" onclick="javascript:del_prmo(this)">
					</td>
				</tr> 
				</table>-->
			<?  }
			} 
			if($cnt == 0  and $mode != "ins" ){ ?> 
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
					<tr>  
						<th><span><?=$page_text?> 타이틀</span></th>
						<td><input type="text" name="title" id="title" style="width:20%" value="" alt="타이틀" /></td>
					</tr>						
					<tr style='display:none;' >
						<th><span>타이틀 설명</span></th>
						<td><textarea name="info" style="width:500;height:100;"></textarea> </td> 
					</tr>
					<tr>
						<th><span>영역 우선순위</span></th>
						<td>
							<select name="display_seq"class="display_seq">
							<?if($count==0){$count=1;}else{ for($i=1; $i<=$count; $i++){?>
								<option value="<?=$i?>"><?=$i?></option>
							<?}}?>
							</select>
						</td>
					</tr>
					<tr>
						<th><span>상품 리스팅 템플릿</span></th>
						<td><select name="display_tem">
							<option value="1" >4단배열</option>
							<option value="2" >7단배열</option>
							<option value="3" >2단배열</option>
							<option value="4" >1단배열</option>
							<option value="5" >1단배열(슬라이드)</option>
							</select>
						</td>
					</tr>
					<input type="hidden" name="ppidx" value="1"/>
				</table> 
				<?}?>
			<div id="add_div"></div>
		</div>
		<div style="width:100%;text-align:center">
			<input type="image" src="../admin/images/btn_confirm_com.gif">
			<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='market_promotion_roulette.php'">
		</div>


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
<form name="delform" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<input type="hidden" name="ppidx" />
<input type="hidden" name="mode" value="mod" />
<input type="hidden" name="mode2" value="!!!" />
<input type="hidden" name="pidx" value="<?=$pidx?>" />
</form>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript">
var checkF = false;  
$(document).ready(function(){
	sumPointTotal();
	$("#tr_add").click(function(){
		var lastItemNo = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
		document.eventform.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			var newItem = $(".table_style01 [name=promotable]:last").clone();
			newItem.removeClass();
			var xxx = $(".table_style01 [name=promotable]:last [name=ppidx]").val();
			newItem.addClass("item"+(parseInt(lastItemNo)+1));
			newItem.appendTo('.table_style01'); 			
			$(".table_style01 [name=promotable]:last [name=ppidx]").attr('value', parseInt(xxx)+1);	
			$(".table_style01 [name=promotable]:last [name=promo_seq]").val('');		
			
			var optemp = "<option value='"+(parseInt(lastItemNo)+1)+"'>"+(parseInt(lastItemNo)+1)+"</option>";
			$(".table_style01").find(".display_seq").append(optemp);
			
			$(".table_style01 [name=promotable]:last [name=title]").val(""); 
			$(".table_style01 [name=promotable]:last [name=info]").val(""); 
			$(".table_style01 [name=promotable]:last [name=display_seq]:last option:last").attr("selected", "selected"); 
		}else{ 
			alert("20개까지 등록할 수 있습니다.");
			return;   
		}
	}); 
	
	// 달력 클릭시 이벤트 ( 쿠폰 유효 종료일 자동 계산 )
	$(document).on("click",".day", function(event){
		settingDate(event);
	});

	$(".img_view_sizeset").on('mouseover',function(){
		$("#img_view_div").find('img').attr('src',($(this).attr('src')));
		$("#img_view_div").find('img').css('display','block');
	});

	$(".img_view_sizeset").on('mouseout',function(){
		$("#img_view_div").find('img').css('display','none'); 
	});	
	
	
	//핏플랍 모바일 타이틀 배너 display
	$("#display_type").change(function() {
		if($("#display_type option:selected").val()=="B"){
			$("#fmobile").show();
		}else{
			$("#fmobile").hide();
		}
	});

	//유효기간설정 select action
	$(".CLS_coupon_time").on('change', function() {
		$("input[name=peorid]").val('0');
		if($(this).val() == 'D'){
			$("input[name=date_start]").val('');
			$("#ID_coupon_timeD").show();
			$("#ID_coupon_timeP").hide();
		}else{
			$("input[name=peorid]").val(0);
			$("#ID_coupon_timeP").show();
			$("#ID_coupon_timeD").hide();
		}
	});

	//쿠폰사용제한 select action
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

    // 출석체크시 설정 각각의 radio button 설정
    var m_attendance_weekly_reward = "<?=$mrow['attendance_weekly_reward']?>" !== "" ? "<?=$mrow['attendance_weekly_reward']?>" : "0";
    var m_attendance_weekend_reward = "<?=$mrow['attendance_weekend_reward']?>" !== "" ? "<?=$mrow['attendance_weekend_reward']?>" : "0";
    var m_attendance_complete_reward = "<?=$mrow['attendance_complete_reward']?>" !== "" ? "<?=$mrow['attendance_complete_reward']?>" : "0";

    $("input:radio[name='attendance_weekly_reward']:radio[value='" + m_attendance_weekly_reward + "']").attr("checked", true);
    $("input:radio[name='attendance_weekend_reward']:radio[value='" + m_attendance_weekend_reward + "']").attr("checked", true);
    $("input:radio[name='attendance_complete_reward']:radio[value='" + m_attendance_complete_reward + "']").attr("checked", true);

    // 로딩시 현재 이벤트 타입별로 화면 재구성
    changeEventType($("input[name='event_type']:checked"));
});

//쿠폰 종료일 날짜 계산
function settingDate(event) {
	if ((event === undefined && $("input[name='end_date']").val().trim() != '') || eventElement.name == 'end_date')
	{
		var oldDate = $("input[name='end_date']").val().trim();
		var newDate = (event !== undefined ? event.target.title : oldDate);// 이벤트 종료일
		if (oldDate != '' && $("input[name='start_date']").val().trim()!= '' &&  $("input[name='start_date']").val().trim() > newDate)
		{
			
			alert("노출기간 종료일을 다시 선택해 주세요.");
			$("input[name='end_date']").val('');
			return false;
		}
		var eDateArr = newDate.split("-"); 
		var peorid = $("input[name='peorid']").val().trim(); // 쿠폰 유효기간 일
		var eDateObj = new Date(eDateArr[0], Number(eDateArr[1])-1, eDateArr[2]); 
		//일추가
		eDateObj.setDate(eDateObj.getDate() + Number(peorid));

		var sYear = eDateObj.getFullYear();
		var sMonth = eDateObj.getMonth() + 1;
		var sDay = eDateObj.getDate();
		sYear = eDateObj.getFullYear();
		sMonth = (sMonth < 10) ? "0"+sMonth : sMonth;
		sDay = (sDay < 10) ? "0"+sDay : sDay;

		$("input[name='date_end']").val(sYear + "-" + sMonth + "-" + sDay);// 쿠폰 유효기간 종료일
		
	} else {
		var oldDate = $("input[name='start_date']").val().trim();
		var newDate = (event !== undefined ? event.target.title : oldDate);// 이벤트 종료일
		if (oldDate != '' && $("input[name='end_date']").val().trim()!= '' && $("input[name='end_date']").val().trim() < newDate)
		{
			
			alert("노출기간 시작일을 다시 선택해 주세요.");
			$("input[name='start_date']").val('');
			return false;
		}
	}
	sumPointTotal();
}
function del_prmo(t){		
	if(confirm("삭제하시겠습니까?")){
		document.delform.ppidx.value=t;
		document.delform.submit();
	}
}

// 이벤트 종류를 변경시 호출
function changeEventType(obj) {
    if ( $(obj).val() != '1' && $(obj).val() != '0' ) {
        // 이벤트 종류가 룰렛이벤트이 아닌 경우
        $("table[name='promotable']").hide();
        $("#tr_add").hide();
        $("#add_prod").hide();
        $(".tr_remove").hide();
    } else {
        $("table[name='promotable']").show();
        $("#tr_add").show();
        $("#add_prod").show();
        $(".tr_remove").show();
    }

    if ( $(obj).val() != '4' ) {
        // 이벤트 종류가 '출석체크'가 아닌 경우
        $("#attendance_tr").hide();
    } else {
        $("#attendance_tr").show();
    }
}

function toggle_attendance_input(obj) {
    var point_id = $(obj).attr("name") + "_point";

    if ( $(obj).val() === "0" ) {
        // 마일리지
        $("#" + point_id).attr("disabled", false);       
    } else if ( $(obj).val() === "1" ) {
        // 쿠폰
        $("#" + point_id).attr("disabled", true);       
    }

}

function layer_open(el,onMode,coupon_id){
    var checkVal = $("input:radio[name='" + coupon_id + "']:checked").val();
    if ( checkVal == "0" ) {
        // '쿠폰'을 선택하지 않은 경우
        alert("'쿠폰'을 선택해 주세요.");
        return false;
    }

    var temp = $('#' + el);
    var bg = temp.prev().hasClass('bg');    //dimmed 레이어를 감지하기 위한 boolean 변수
    switch(onMode){
        case 'normalCoupon' :
            $('#listMode').val('normalCoupon');
            $('#couponId').val(coupon_id);
            break;
        default :
            $('#listMode').val('');
            break;
    }
    
    if(bg){
        temp.parents('.layer').fadeIn();   //'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
    }else{
        temp.fadeIn();
    }

    layerResize(el);

    temp.find('a.cbtn').click(function(e){
        if(bg){
            $('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
            outLayer();
        }else{
            temp.fadeOut();
            outLayer();
        }
        e.preventDefault();
    });

    $('.layer .bg').click(function(e){  //배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
        $('.layer').fadeOut();
        outLayer();
        e.preventDefault();
    });

}

function layerResize(el){
    var temp = $('#' + el);
    // 화면의 중앙에 레이어를 띄운다.
    if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
    else temp.css('top', '0px');
    if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
    else temp.css('left', '0px');
    
    //console.log(temp.outerHeight());
}

function outLayer(){
    $("#s_keyword").val("");
    $("#couponList").html("");
    $('#listMode').val("");
    //$("#checkProduct").html("");
}

function couponListSearch(){
    var s_keyword = $("#s_keyword").val();
    var listMode = $("#listMode").val();
    var coupon_id = $("#couponId").val();
    $.post(
        "member_groupnew_couponlistPost_v3.php",
        {
            s_keyword:s_keyword,
            listMode:listMode,
            coupon_id:coupon_id
        },
        function(data){
            $("#couponList").html(data);
            layerResize('layer2');
        }
    );
}

function gradeCoupon(prname,prcode,coupon_id){
    var upList = true;
    var appHtml = "";
    /*if($("input[name='relationProduct[]']").length > 4){
        alert('관련상품은 5개까지 등록이 가능합니다.');
        upList = false;
        //return upList;
    }*/

    if ( $("input[name='" + coupon_id + "_coupon[]']").length >= 1 ) {
        alert("쿠폰은 한개만 등록가능합니다.");
        upList = false;
        return upList;
    }

    $("input[name='" + coupon_id + "_coupon[]']").each(function(){
        if($(this).val() == prcode){
            alert('쿠폰이 중복되었습니다.');
            upList = false;
            return upList;
        }else{
        }
    });

    if(upList){
        appHtml= "<tr align=\"center\">\n";
        appHtml+= " <td style='border:0px' align=\"left\">"+prname+"&nbsp;&nbsp;<img src=\"images/icon_del1.gif\" onclick=\"javascript:gradeCouponDel('"+prcode+"', '" + coupon_id + "');\" border=\"0\" style=\"cursor: hand;vertical-align:middle;\" />\n";
        appHtml+= "     <input type='hidden' name='" + coupon_id + "_coupon[]' value='"+prcode+"'>\n";
        appHtml+= " </td>\n";
        appHtml+= "</tr>\n";
        $("#checkProduct_" + coupon_id).append(appHtml);
    }
}

function gradeCouponDel(prcode, coupon_id){
    if(confirm('해당 쿠폰을 삭제 하시겠습니까?')){
        $("input[name='" + coupon_id + "_coupon[]']").each(function(){
            if($(this).val() == prcode){
                $(this).parent().parent().remove();
            }
        });
    }
}

// 금액 할인률 change
function changerate(obj){
	var indx = obj.id;
	$("input[name='roulette_seg_point[]']").eq(indx).val(0);
	console.log(obj.value);
	if (obj.value == '%') {
		$("input[name='roulette_seg_point[]']").eq(indx).attr("maxlength","2");
	} else {
		$("input[name='roulette_seg_point[]']").eq(indx).attr("maxlength","10");
	}
	$("input[name='roulette_seg_point_type[]']").eq(indx).val(obj.value);
	$("input[name='roulette_seg_ptype[]']").eq(indx).val(obj.value);
	sumPointTotal();
}

// 쿠폰 포인트 change
function changeStatus(obj){
	var indx = obj.id;
	var subObj = $("select[name='roulette_seg_type_tmep[]'] option:selected").eq(indx);

	if (subObj.val() == 'P') 
	{
		$("select[name='roulette_seg_point_type_temp[]']").eq(indx).attr("disabled",true);
	}
	else {
		$("select[name='roulette_seg_point_type_temp[]']").eq(indx).attr("disabled",false);
	}
	$("input[name='roulette_seg_type[]']").eq(indx).val(obj.value);
	sumPointTotal();
}

// 총 경품 금액 계산 
function sumPointTotal() {
	var arrTot = new Array(0,0,0,0);
	var arrPoint = 	 $("input[name='roulette_seg_point[]']").map(function(){return ($(this).val() == '' ? 0 : $(this).val()) ; });
	var arrType = $("input[name='roulette_seg_type[]']").map(function(){return $(this).val(); });
	var arrPtype = $("input[name='roulette_seg_point_type[]']").map(function(){return $(this).val(); });
	var arrCount = 	 $("input[name='roulette_seg_cnt[]']").map(function(){return ($(this).val() == '' ? 0 : $(this).val()) ; });
	var start_date = $("input[name='start_date']").val();
	var end_date = $("input[name='end_date']").val();

	$.each(arrPoint, function (index, value) {
		if(arrType[index] == 'P') {
			arrTot[0] = parseInt(arrTot[0]) + parseInt(arrPoint[index]);
		} else if (arrPtype[index] == '%'){
			arrTot[2] = parseInt(arrTot[2]) + parseInt(arrPoint[index]);
		} else {
			arrTot[1] = parseInt(arrTot[1]) + parseInt(arrPoint[index]);
		}
		arrTot[3] = parseInt(arrTot[3]) + parseInt(arrCount[index]);
	});

	if (checkF)
	{
		if ($("input[name='day_order']").val() > arrTot[3]) 
		{
			alert("총 경품 수량보다 큰 수를 넣을 수 없습니다.");
			$("input[name='day_order']").val(arrTot[3]);
		}


	}
	checkF = true;

	//$(".roulette-tot-price").each(function(i,e){return $(this).text(arrTot[i]); }); // 총금액관련부분 주석처리 나중에 적용시 확인 후 수정
	$(".roulette-tot-price").text(arrTot[3]);
	if (start_date != '' && end_date != '' )
	{
		var tmpCnt = Math.round(parseInt(arrTot[3])/parseInt(calDateRange(start_date, end_date)));
		$("input[name='day_order_temp']").val((start_date == end_date ? arrTot[3] : (tmpCnt == 0 ? 1 : tmpCnt)));
//			console.log("day_order_temp"+$("input[name='day_order_temp']").val());
//			console.log("arrTot[3]"+parseInt(arrTot[3]));
//			console.log("calDateRange"+parseInt(calDateRange(start_date, end_date)));
//			console.log("parseInt"+parseInt(arrTot[3])/parseInt(calDateRange(start_date, end_date)));
//			console.log("arrTot"+Math.round(parseInt(arrTot[3])/parseInt(calDateRange(start_date, end_date))));
	}

}

/**
 * 두 날짜의 차이를 일자로 구한다.(조회 종료일 - 조회 시작일)
 *
 * @param val1 - 조회 시작일(날짜 ex.2002-01-01)
 * @param val2 - 조회 종료일(날짜 ex.2002-01-01)
 * @return 기간에 해당하는 일자
 */
function calDateRange(val1, val2)
{
	var FORMAT = "-";

   

	// FORMAT을 포함한 길이 체크
	if (val1.length != 10 || val2.length != 10)
		return null;



	// FORMAT이 있는지 체크
	if (val1.indexOf(FORMAT) < 0 || val2.indexOf(FORMAT) < 0)
		return null;



	// 년도, 월, 일로 분리
	var start_dt = val1.split(FORMAT);
	var end_dt = val2.split(FORMAT);



	// 월 - 1(자바스크립트는 월이 0부터 시작하기 때문에...)
	// Number()를 이용하여 08, 09월을 10진수로 인식하게 함.
	start_dt[1] = (Number(start_dt[1]) - 1) + "";
	end_dt[1] = (Number(end_dt[1]) - 1) + "";



	var from_dt = new Date(start_dt[0], start_dt[1], start_dt[2]);
	var to_dt = new Date(end_dt[0], end_dt[1], end_dt[2]);

	var result = ((to_dt.getTime() - from_dt.getTime()) / 1000 / 60 / 60 / 24)+1;
	return result;
}
/*
function T_GoPage(block,gotopage){
    var s_keyword = $("#s_keyword").val();
    var listMode = $("#listMode").val();
    $.post(
        "member_groupnew_couponlistPost_v3.php",
        {
            listMode:listMode,
            s_keyword:s_keyword,
            block:block,
            gotopage:gotopage
        },
        function(data){
            $("#couponList").html(data);
            layerResize('layer2');
        }
    );
}
*/
</script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
/*
function htmlsetmode(mode,i){
	if(mode==document.eventform.htmlmode.value) {
		return;
	} else {
		i.checked=true;
		editor_setmode('content',mode);
	}
	document.eventform.htmlmode.value=mode;
}
_editor_url = "htmlarea/";
editor_generate('content');
*/
</script>

<style type="text/css">
    .layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100;}
    .layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
    .layer .pop-layer {display:block;}

    .pop-layer {display:none; position: absolute; top: 50%; left: 50%; width: 900px; height:500px;  background-color:#fff; border: 5px solid #3571B5; z-index: 10; overflow-y: scroll;} 
    .pop-layer .pop-container {padding: 20px 25px;}
    .pop-layer p.ctxt {color: #666; line-height: 25px;}
    .pop-layer .btn-r {
            /*width: 100%; margin:10px 0 20px; padding-top: 10px; border-top: 1px solid #DDD; text-align:right;*/
            position: fixed; margin-left: 843px; margin-top: -35;
    }

    a.cbtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #304a8a; background-color:#3f5a9d; font-size:13px; color:#fff; line-height:25px;} 
    a.cbtn:hover {border: 1px solid #091940; background-color:#1f326a; color:#fff;}
    
    /*
    li.prListOn { position:relative; float:left; margin-right:15px; margin-bottom:5px; width:100px; height: 150px;}
    li.prListOn:before {display:block; width:1px; height:100%; content:""; background:#dbdbdb; position:absolute; top:0px; left:105px;}
    */
</style>

<!-- 쿠폰조회 레이어팝업 S -->
<input type="hidden" name="listMode" id="listMode" value=""/>
<input type="hidden" name="couponId" id="couponId" value=""/>
<div class="layer">
    <div class="bg"></div>
    <div id="layer2" class="pop-layer" style='width:1000px'>
        <div class="btn-r" style='margin-left:942px'>
            <a href="#" class="cbtn">Close</a>
        </div>
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <p class="ctxt mb20" style="font-size:15px; font-weight: 700;">쿠폰 선택
                    <div>
                        <input type="text" name="s_keyword" id="s_keyword" value="" style="width: 250px;"/>
                        <a href="javascript:couponListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
                    </div>
                </p>
                <div id="couponList">
                    
                </div>
                <!--// content-->
            </div>
        </div>
    </div>
</div>
<!-- 쿠폰조회 레이어팝업 E -->

<?include("layer_brandListPop.php");?>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php 
include("copyright.php");
