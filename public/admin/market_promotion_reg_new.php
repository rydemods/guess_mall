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
#########################################################
$page_type = $_REQUEST["page_type"];
$page_text = "기획전";
$return_page_link = "market_promotion_new.php";
if($page_type=="event"){
	$page_text = "이벤트";
	$return_page_link = "market_promotion_new_sw.php";
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
$hidden         = $_REQUEST['hidden'];
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

$tmpResult = $_POST['winner_list_content'];   // html제거
if ( trim($tmpResult) === "" ) {
    // 제거한 후 빈값이면 빈값으로 입력
    $winner_list_content = "";
} else {
    // 아니면 입력받은 그대로 입력
    $winner_list_content = $_POST['winner_list_content'];
//     $winner_list_content = str_replace("'", "''", $winner_list_content);
}

switch($mode){
	case "del" : 	$seq=$_REQUEST['seq']; /*삭제할때 삭제할 로우보다 진열 순위가 낮은 로우를 한개씩 위로 올림*/
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

				/*관련 댓글 삭제*/	 
				$dsql4 = "DELETE FROM tblboardcomment_promo WHERE parent='{$pidx}' "; 
				pmysql_query($dsql4);	

				echo "<script>alert('삭제되었습니다.');</script>";
// 				echo "<script>document.location.href='market_promotion_new.php';</script>";
				echo "<script>document.location.href='".$return_page_link."';</script>";
				break; 
				
	case "ins" : $count=$count+1; $mcount= $mcount+1; break;	 
				
	case "ins_submit" : $ptitle = pmysql_escape_string($_POST["ptitle"]); $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
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
                        $misql.= "winner_list_content, attendance_weekly_icon, attendance_weekend_icon, bridx, bridx_list ) ";
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
                        $misql.="'{$up_file['attendance_weekly_icon'][0]['v_file']}', '{$up_file['attendance_weekend_icon'][0]['v_file']}', {$bridx[0]}, '{$bridxList}' ) ";

                        pmysql_query($misql);
						if(!pmysql_error()){
							for($aa=0;count($pt)>$aa;$aa++){
								$csql = "SELECT count(*) FROM tblpromotion where  promo_idx='{$tempx}'  ";
								$cres = pmysql_query($csql,get_db_conn());
								$crow=pmysql_fetch_array($cres);
								if($crow[0]!=$ps[$aa]+1){ /*새로 등록할때 지정한 진열순위가 맨 뒤가 아니라면 지정한 순위부터 뒤에 로우를 한칸씩 뒤로 민다.*/
									$usql = "UPDATE tblpromotion SET display_seq = display_seq+1
									WHERE idx = ( select * from (select idx where  promo_idx='{$tempx}' AND display_seq >= {$ps[$aa]}) as a)";
									pmysql_query($usql,get_db_conn());
								}
							
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
							}
							echo "<script>alert('등록되었습니다.');</script>";
							//echo "<script>document.location.href='market_promotion_new.php';</script>";
							echo "<script>document.location.href='".$return_page_link."';</script>";
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
						
// 						if($murow[0]!=$mds){ /*수정할때 지정한 진열 순위에 따라 나머지 로우들도 진열 순위를 수정함*/
// 							if($murow[0]<$mds){
// 								$usql = "UPDATE tblpromo SET display_seq = display_seq-1 
// 										WHERE idx = ( select * from (select idx where display_seq between {$murow[0]} and {$mds}) as a)";
// 								pmysql_query($usql,get_db_conn());
// 							} 
// 							if($murow[0]>$mds){
// 								$usql = "UPDATE tblpromo SET display_seq = display_seq+1 
// 										WHERE idx = ( select * from (select idx where display_seq between {$mds} and {$murow[0]}) as a)";
// 								pmysql_query($usql,get_db_conn());	
// 							}
// 						}
						 /*메인테이블 업데이트*/
						$musql = "update tblpromo set title = '{$mt}', display_type = '{$mdt}', display_seq =  '{$mds}', promo_code =  '{$promo_code}', promo_view =  '{$promo_view}', 
								start_date = '{$start_date}', end_date = '{$end_date}', start_date_time = '{$start_date_time}', end_date_time = '{$end_date_time}', ";

                        if ( $publication_date != "" ) { $musql.= "publication_date = '{$publication_date}', "; }
//                        if ( $bridx[0] !== null ) { $musql.= "bridx = {$bridx[0]}, "; }
//                        if ( $bridxList != "" ) { $musql.= "bridx_list = '{$bridxList}', "; }

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
						
						echo "<script>alert('수정되었습니다.');</script>";
						echo "<script>document.location.href='".$return_page_link."';</script>";
						break;
}
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
 
<script language="JavaScript">
function tr_remove(obj){
	if(typeof(obj) == 'undefined' || obj == ''){
	 	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	 	document.eventform.itemCount.value = itemCount;
	 	$(".table_style01 [name=promotable]:last").remove();
	} else {
		$('.'+obj).remove();
	}
	
}

function all_remove (){
	if ( confirm("전체타이틀 을 삭제 하시겠습니까?") ) {
		var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
		for(var i=1;i<=itemCount;i++){
			if(i > 1){
				$('.item'+i).remove();
			}
		}
	}
}

function chkfrm()	{

    if ( $("#mtitle").val().trim() === "" ) {
        alert("메인 타이틀을 입력해 주세요.");
        $("#mtitle").val("").focus();
        return false;
    }

/*
    if ( $("#s_brand").val().trim() === "" ) {
        alert("브랜드를 선택해 주세요.");
        $("#s_brand").focus();
        return false;
    }
*/

    if ( $("input[name='start_date']").val().trim() === "" ) {
        alert("노출 시작일을 입력해 주세요.");
        return false;
    }

    if ( $("input[name='end_date']").val().trim() === "" ) {
        alert("노출 마감일을 입력해 주세요.");
        return false;
    }

/*
    if ( $("select[name='display_type']").val() != "N" && $("input[name='publication_date']").val().trim() === "" ) {
        alert("발표일을 입력해 주세요.");
        return false;
    }
*/

    if ( $("input[name='event_type']:checked").val() === "4" ) {

        // 출석체크인 경우 '설정'값들을 제대로 입력했는지 체크
        var arrNames = [ "attendance_weekly_reward", "attendance_weekend_reward", "attendance_complete_reward" ];
        var arrTitles = [ "주중", "주말", "완료시 보상" ];  
        
        var flagSuccess = true;
        for ( var i in arrNames ) {
            var chkVal = $("input:radio[name='" + arrNames[i] + "']:checked").val();    // radio button 선택값

            if ( chkVal === "0" ) {
                // 마일리지
                $mileageVal = $("input[name='" + arrNames[i] + "_point']").val().trim();

                if ( $mileageVal === "" || $mileageVal === "0" ) {
                    alert( "'" + arrTitles[i] + "' 마일리지를 입력해 주세요.");
                    flagSuccess = false;
                    break;
                }
            } else if ( chkVal === "1" ) {
                // 쿠폰
                if ( $("input[name='" + arrNames[i] + "_coupon[]']").length == 0 ) {
                    alert( "'" + arrTitles[i] + "' 쿠폰정보를 입력해 주세요.");
                    flagSuccess = false;
                    break;
                }
            }
        }

        if ( !flagSuccess ) {
            return false;
        }
    }

	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	var mode = document.eventform.mode.value;
	if(mode=="ins"){  
		if(confirm("등록하시겠습니까?")){
			document.eventform.mode.value = "ins_submit";
		}	
	}else if(mode=="mod"){
		if(confirm("수정하시겠습니까?")){
			document.eventform.mode.value = "mod_submit";
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

    if ( oEditors.getById["ir1_m"] ) {
        var sHTML = oEditors.getById["ir1_m"].getIR();
        document.eventform.content_m.value=sHTML;
    }

    if ( oEditors.getById["ir2"] ) {
        var sHTML = oEditors.getById["ir2"].getIR();
        document.eventform.winner_list_content.value=sHTML;
    } 

    if ( oEditors.getById["ir1"] ) { 
        var sHTML = oEditors.getById["ir1"].getIR();
        document.eventform.content.value=sHTML;
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

	<div class="title_depth3"><?=$page_text?> <?if($mode=="ins"){echo "등록";}else{echo "수정";} ?>
		<a href="#">
			<img align="right" class="tr_remove" src="../admin/images/botteon_del.gif" align="right" alt="삭제하기" onclick="javascript:all_remove()"></a>
		<a href="#">
			<img align="right" id="tr_add" src="../admin/images/btn_badd2.gif" alt="추가하기"></a>
		<?if($mode=="mod"){?>		
		<a href="/admin/market_promotion_product_new.php?pidx=<?=$pidx?>" target="_self">
			<img align="right" id="add_prod" src="/admin/images/btn_promo_product.gif" alt="상품등록"/></a>&nbsp;
		<a href="/front/promotion.php?pidx=<?=$pidx?>" target="_blank">
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
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="idx" value="<?=$idx?>">
	<input type="hidden" name="pidx" value="<?=$pidx?>">
	<input type="hidden" name="page_type" value="<?=$page_type?>">
	
		<!-- 테이블스타일01 -->
		<div class="table_style01 pt_20" style="position:relative">
			<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<?php
                $msql = "SELECT * FROM tblpromo WHERE idx = '{$pidx}'";
				$mres = pmysql_query($msql);
				$mrow = pmysql_fetch_array($mres);

                // 신규일 경우, 이벤트 종류를 '기획전'으로 세팅
                if ( $mrow['event_type'] == "" ) { $mrow['event_type'] = "1"; } 

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
			<tr> 
				<th><span>메인 타이틀</span></th>
				<td><input type="text" name="mtitle" id="mtitle" style="width:50%" value="<?=$mrow['title']?>" alt="타이틀" /></td>
			</tr>
			<tr> 
				<th><span>브랜드 선택</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_brand_sel','');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
				<td>
                    <?php if ( false ) { ?>
                    <select name="s_brand" id="s_brand">
                        <? 
                            $selected = "";
                            if ( $mrow['bridx'] == "0" ) {
                                $selected = "selected";
                            }
                            echo "<option value='0' {$selected}>ALL</option>";

                            foreach ( $arrBrandList as $key => $value ) {
                                $selected = "";
                                if ( $mrow['bridx'] == $key ) { $selected = "selected"; }

                                echo "<option value='" . $key . "' {$selected}> " . $value . " </option>";
                            } 
                        ?>
                    </select>
                    <?php } else { ?>

                    <div style="margin-top:0px; margin-bottom: 0px;">                           
                        <table border="0" cellpadding="0" cellspacing="0" style="border:0px" name="prList" id="check_relationProduct">  
                            <colgroup>
                                <col width="50">
                                <col width="">
                            </colgroup>
                            <tbody id="sel_brand_list">
<?php
                            if ( !empty($mrow['bridx_list']) ) {
                                $brand_list = trim($mrow['bridx_list'], ",");
                                $sub_sql  = "SELECT *, tvia.s_img ";
                                $sub_sql .= "FROM tblproductbrand tpb LEFT JOIN tblvenderinfo_add tvia ON tpb.vender = tvia.vender ";
                                $sub_sql .= "WHERE tpb.display_yn = 1 AND tpb.bridx in ( $brand_list ) ";
                                $sub_sql .= "ORDER BY POSITION(tpb.bridx::text in ('$brand_list') ) ";
                            } elseif ( !empty($mrow['bridx']) ) {
                                $sub_sql  = "SELECT *, tvia.s_img ";
                                $sub_sql .= "FROM tblproductbrand tpb LEFT JOIN tblvenderinfo_add tvia ON tpb.vender = tvia.vender ";
                                $sub_sql .= "WHERE tpb.display_yn = 1 AND tpb.bridx = " . $mrow['bridx'];
                            }

                            $sub_result = pmysql_query($sub_sql);

                            while ( $sub_row = pmysql_fetch_object($sub_result) ) {
                                $s_img  = getProductImage($Dir.DataDir.'shopimages/vender/', $sub_row->s_img);
?>

                                    <tr align="center" id="tr_brand_<?=$sub_row->bridx?>">
                                    <td style="border:0px">
                                    <img style="width: 40px; height:40px;" src="<?=$s_img?>" border="1">
                                    <input type="hidden" name="s_brand[]" value="<?=$sub_row->bridx?>">
                                    </td>
                                    <td style="border:0px" align="left">
                                    <?=$sub_row->brandname?>&nbsp;&nbsp;<img src="images/icon_del1.gif" border="0" style="cursor: hand;vertical-align:middle;" onClick="javascript:T_delBrandList('<?=$sub_row->bridx?>');">
                                    </td>
                                    </tr>
<?php
                            }
?>

                            </tbody>
                        </table>
                    </div>


                    <?php } ?>
                </td>
			</tr>
			<tr>
				<th><span>이벤트 종류</span></th>
				<td>    
					<?if($page_type=="event"){?>
					<input type="radio" name="event_type" value="2" <?if($mrow['event_type']=='2') echo "checked";?> onChange="javascript:changeEventType(this);" />댓글
                    <input type="radio" name="event_type" value="3" <?if($mrow['event_type']=='3') echo "checked";?> onChange="javascript:changeEventType(this);" />포토
					<?}else{?>
					<input type="radio" name="event_type" value="1" <?if($mrow['event_type']=='1') echo "checked";?> onChange="javascript:changeEventType(this);" />일반
					<input type="radio" name="event_type" value="0" <?if($mrow['event_type']=='0') echo "checked";?> onChange="javascript:changeEventType(this);" />타임세일
					<?}?>
                    
                    <!-- <input type="radio" name="event_type" value="4" <?if($mrow['event_type']=='4') echo "checked";?> onChange="javascript:changeEventType(this);" />출석체크 -->
				</td>
			</tr>
			<tr style="display:none;"> 
				<th><span>메인 카테고리</span></th>
				<td>
					<select name = 'promo_code'>
						<option value = ''>--카테고리 선택--</option>
						<?
							$selected['promo_code'][$mrow['promo_code']] = 'selected';
							$checked['promo_view']['Y'] = 'checked';
							# 1차 카테고리만 출력
							$first_cate_sql = "
										SELECT 
											* 
										FROM 
											tblproductcode 
										WHERE 
											group_code!='NO' 
											AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') 
											AND code_b = '000' 
											AND code_c = '000' 
											AND code_d = '000' 
										ORDER BY 
											sequence 
										DESC";
							$first_cate_result = pmysql_query($first_cate_sql,get_db_conn());
							while($first_cate_row=pmysql_fetch_object($first_cate_result)) {
						?>
						<option value = '<?=$first_cate_row->code_a?>' <?=$selected['promo_code'][$first_cate_row->code_a]?>><?=$first_cate_row->code_name?></option>
						<?
							}
						?>
					</select>
					<input type = 'checkbox' name = 'promo_view' value = 'Y' <?=$checked['promo_view'][$mrow['promo_view']]?>> 메인 노출
				</td>
			</tr>
			<tr>
				<th style="border-top: 1px solid black; border-left: 1px solid black;"><span>썸네일 이미지(PC)<br>&nbsp;&nbsp;&nbsp;( 345 * 117 )</span></th>
				<td style="border-top: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="thumb_img[]" alt="썸네일 이미지" />
				<?
					if($mrow['thumb_img']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['thumb_img']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr>
				<th style="border-left: 1px solid black;"><span>메인 이미지 타입 선택(PC)</span></th>
				<td style="border-right: 1px solid black;">
					<input type="radio" name="image_type" value="F" <?if($mrow['image_type']=="F" || $mrow['image_type']=="") echo "checked";?> />파일 업로드 &nbsp;
					<input type="radio" name="image_type" value="E" <?if($mrow['image_type']=="E") echo "checked";?> />에디터 사용
				</td>
			</tr>
			<tr id="img_E" style="display:none;">
				<th style="border-bottom: 1px solid black; border-left: 1px solid black;"><span>메인 이미지 에디터(PC)</span></th>
				<td style="border-bottom: 1px solid black; border-right: 1px solid black;"><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name=content><?=stripslashes($mrow['content'])?></textarea></td>
			</tr>
			<tr id="img_F">
				<th style="border-bottom: 1px solid black; border-left: 1px solid black;"><span>메인 이미지 (PC)<br>&nbsp;&nbsp;&nbsp;( 1100 * 580 )</span></th>
				<td style="border-bottom: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="banner_img[]" alt="본문 이미지" />
				<?
					if($mrow['banner_img']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['banner_img']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>

			<tr>
				<th style="border-top: 1px solid black; border-left: 1px solid black;"><span>썸네일 이미지(모바일)<br>&nbsp;&nbsp;&nbsp;( 385 * 109 )</span></th>
				<td style="border-top: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="thumb_img_m[]" alt="썸네일 이미지" />
				<?
					if($mrow['thumb_img_m']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['thumb_img_m']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr>
				<th style="border-left: 1px solid black;"><span>메인 이미지 타입 선택(모바일)</span></th>
				<td style="border-right: 1px solid black;">
					<input type="radio" name="image_type_m" value="F" <?if($mrow['image_type_m']=="F" || $mrow['image_type_m']=="") echo "checked";?> />파일 업로드 &nbsp;
					<input type="radio" name="image_type_m" value="E" <?if($mrow['image_type_m']=="E") echo "checked";?> />에디터 사용
				</td>
			</tr>
			<tr id="img_FM">
				<th style="border-bottom: 1px solid black; border-left: 1px solid black;"><span>메인 이미지 (모바일)<br>&nbsp;&nbsp;&nbsp;( 640 *  )</span></th>
				<td style="border-bottom: 1px solid black; border-right: 1px solid black;">
				<input type="file" name="banner_img[]" alt="본문 이미지" />
				<?
					if($mrow['banner_img_m']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['banner_img_m']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr id="img_EM" style="display:none;">
				<th style="border-bottom: 1px solid black; border-left: 1px solid black;"><span>메인 이미지 에디터(모바일)</span></th>
				<td style="border-bottom: 1px solid black; border-right: 1px solid black;"><textarea wrap=off  id="ir1_m" style="WIDTH: 100%; HEIGHT: 300px" name=content_m><?=stripslashes($mrow['content_m'])?></textarea></td>
			</tr>
			<tr>
				<th><span>전시 상태</span></th>
				<td><select name="display_type" id="display_type">
					<option value="A" <?if($mrow['display_type']=='A') echo "selected";?>>모두</option>
					<option value="P" <?if($mrow['display_type']=='P') echo "selected";?>>PC만</option>
					<option value="M" <?if($mrow['display_type']=='M') echo "selected";?>>모바일만</option>
					<option value="N" <?if($mrow['display_type']=='N') echo "selected";?>>보류</option>
					<!-- <option value="S" <?if($mrow['display_type']=='S') echo "selected";?>>PC 비전시</option>
					<option value="D" <?if($mrow['display_type']=='D') echo "selected";?>>모바일 비전시</option>
					<option value="B" <?if($mrow['display_type']=='B') echo "selected";?>>fitflop 모바일만</option>
					<option value="C" <?if($mrow['display_type']=='C') echo "selected";?>>fitflop 모바일 비전시</option> -->
					</select>
				</td>
			</tr>
			<tr>
				<th><span>노출</span></th>
				<td>
                    <select name="hidden" >
                        <option value="1" <?if($mrow['hidden']=='1') echo "selected";?>>노출</option>
                        <option value="0" <?if($mrow['hidden']=='0') echo "selected";?>>비노출</option>
					</select>
				</td>
			</tr>
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
			<tr style="display:none;">
				<th><span>쿠폰, 적립금 사용금지</span></th>
				<td>
					<input type="checkbox" name="no_coupon" value="Y" <?if($mrow['no_coupon'] == 'Y') echo checked;?> />
				</td>
			</tr>
			<TR>
				<th><span>노출 기간</span></th>
				<TD class="td_con1">
					<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=start_date value="<?=$mrow['start_date']?>" class="input_bd_st01">
					<?=substr($mrow['start_date_time'],0,2); ?>
										<?=substr($mrow['start_date_time'],2,4); ?>
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

					<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=end_date value="<?=$mrow['end_date']?>" class="input_bd_st01">
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
			<TR>
				<th><span>발표일</span></th>
				<TD class="td_con1"><INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=publication_date value="<?=$mrow['publication_date']?>" class="input_bd_st01"></TD>
			</TR>			

			<TR id="attendance_tr">
				<th><span>출석체크시 설정</span></th>
				<TD class="td_con1">
                    <table border="0" width="100%">
                    <colgroup>
                        <col width="10%" />
                        <col width="auto" />
                    </colgroup>
                    <tr>
                        <td>주중</td>
                        <td>
                            <input type="radio" name="attendance_weekly_reward" value="0" onClick="javascript:toggle_attendance_input(this);"/> 마일리지 : <input type="text" name="attendance_weekly_reward_point" id="attendance_weekly_reward_point" value="<?=$mrow['attendance_weekly_reward_point']?>"/> <br/><br/>
                            <input type="radio" name="attendance_weekly_reward" value="1" onClick="javascript:toggle_attendance_input(this);"/> 쿠폰 : <a href="javascript:layer_open('layer2','normalCoupon','attendance_weekly_reward');"><img src="./images/btn_search2.gif" style="vertical-align:middle;padding-bottom:4px;"></a>

                            <input type="hidden" name="group_couponcode" class="input" value="">

                            <div style="margin-top:0px; margin-bottom: 0px;">
                                <table border=1 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="checkProduct_attendance_weekly_reward">

                            <?foreach($thisCoupon[0] as $k=>$v){?>	
                                <tr align="center">
                                    <td style='border:0px' align="left">
                                        <?=$v[coupon_name]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:gradeCouponDel('<?=$v[coupon_code]?>', 'attendance_weekly_reward');" border="0" style="cursor: hand;vertical-align:middle;" />
                                        <input type='hidden' name='attendance_weekly_reward_coupon[]' value='<?=$v[coupon_code]?>'>
                                    </td>
                                </tr>
                            <?}?>
</table>
                            </div>

                            <br/>
                            (PC) 아이콘 이미지 업로드 : <input type="file" name="attendance_weekly_icon[]"/>
                            <?
                                if($mrow['attendance_weekly_icon']){
                            ?>
                                <br><br><img src="<?=$imagepath?><?=$mrow['attendance_weekly_icon']?>" style="height:30px;" class="img_view_sizeset">
                            <?
                                }
                            ?>
                            
                            <br/>
                            (MOBILE) 아이콘 이미지 업로드 : <input type="file" name="attendance_weekly_mobile_icon[]"/>
                            <?
                                if($mrow['attendance_weekly_mobile_icon']){
                            ?>
                                <br><br><img src="<?=$imagepath?><?=$mrow['attendance_weekly_mobile_icon']?>" style="height:30px;" class="img_view_sizeset">
                            <?
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>주말</td>
                        <td>
                            <input type="radio" name="attendance_weekend_reward" value="0" onClick="javascript:toggle_attendance_input(this);"/> 마일리지 : <input type="text" name="attendance_weekend_reward_point" id="attendance_weekend_reward_point" value="<?=$mrow[attendance_weekend_reward_point]?>"/> <br/><br/>
                            <input type="radio" name="attendance_weekend_reward" value="1" onClick="javascript:toggle_attendance_input(this);"/> 쿠폰 : <a href="javascript:layer_open('layer2','normalCoupon','attendance_weekend_reward');"><img src="./images/btn_search2.gif" style="vertical-align:middle;padding-bottom:4px;"></a>

                            <input type="hidden" name="group_couponcode" class="input" value="">

                            <div style="margin-top:0px; margin-bottom: 0px;">
                                <table border=1 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="checkProduct_attendance_weekend_reward">

                            <?foreach($thisCoupon[1] as $k=>$v){?>	
                                <tr align="center">
                                    <td style='border:0px' align="left">
                                        <?=$v[coupon_name]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:gradeCouponDel('<?=$v[coupon_code]?>', 'attendance_weekend_reward');" border="0" style="cursor: hand;vertical-align:middle;" />
                                        <input type='hidden' name='attendance_weekend_reward_coupon[]' value='<?=$v[coupon_code]?>'>
                                    </td>
                                </tr>
                            <?}?>

</table>
                            </div>

                            <br/>
                            (PC) 아이콘 이미지 업로드 : <input type="file" name="attendance_weekend_icon[]"/>
                            <?
                                if($mrow['attendance_weekend_icon']){
                            ?>
                                <br><br><img src="<?=$imagepath?><?=$mrow['attendance_weekend_icon']?>" style="height:30px;" class="img_view_sizeset">
                            <?
                                }
                            ?>
                            <br/>
                            (MOBILE) 아이콘 이미지 업로드 : <input type="file" name="attendance_weekend_mobile_icon[]"/>
                            <?
                                if($mrow['attendance_weekend_mobile_icon']){
                            ?>
                                <br><br><img src="<?=$imagepath?><?=$mrow['attendance_weekend_mobile_icon']?>" style="height:30px;" class="img_view_sizeset">
                            <?
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>완료시 보상</td>
                        <td>
                            <input type="radio" name="attendance_complete_reward" value="0" onClick="javascript:toggle_attendance_input(this);"/> 마일리지 : <input type="text" name="attendance_complete_reward_point" id="attendance_complete_reward_point" value="<?=$mrow[attendance_complete_reward_point]?>"/> <br/><br/>
                            <input type="radio" name="attendance_complete_reward" value="1" onClick="javascript:toggle_attendance_input(this);"/> 쿠폰 : <a href="javascript:layer_open('layer2','normalCoupon','attendance_complete_reward');"><img src="./images/btn_search2.gif" style="vertical-align:middle;padding-bottom:4px;"></a>
                            <input type="hidden" name="group_couponcode" class="input" value="">

                            <div style="margin-top:0px; margin-bottom: 0px;">
                                <table border=1 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="checkProduct_attendance_complete_reward">
                            <?foreach($thisCoupon[2] as $k=>$v){?>	
                                <tr align="center">
                                    <td style='border:0px' align="left">
                                        <?=$v[coupon_name]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:gradeCouponDel('<?=$v[coupon_code]?>', 'attendance_complete_reward');" border="0" style="cursor: hand;vertical-align:middle;" />
                                        <input type='hidden' name='attendance_complete_reward_coupon[]' value='<?=$v[coupon_code]?>'>
                                    </td>
                                </tr>
                            <?}?>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </table>
                </TD>
			</TR>			
			<tr id="winner_list_E">
				<th><span>당첨자 발표</span></th>
				<td><textarea wrap=off  id="ir2" style="WIDTH: 100%; HEIGHT: 300px" name="winner_list_content"><?=stripslashes($mrow['winner_list_content'])?></textarea></td>
			</tr>
			</table>
			&nbsp;
			
			<!--기획전들-->
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
							<option value="1" >기본형(웹4단/모바일2단)</option>
							<option value="2" >복합형(웹7단/모바일3단)</option>
							<option value="3" >강조형(웹2단/모바일1단)</option>
							<option value="4" >세로형</option>
							<option value="5" >슬라이드형</option>
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
							<input type="text" name="title" id="title" style="width:20%" value="<?=$row['title']?>" alt="타이틀" />&nbsp;<a href="javascript:tr_remove('item<?=$cnt?>');">삭제하기</a>
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
					

							<option value="1"  <?if($row['display_tem']=='1') echo "selected";?>>기본형(웹4단/모바일2단)</option>
							<option value="2"  <?if($row['display_tem']=='2') echo "selected";?>>복합형(웹7단/모바일3단)</option>
							<option value="3"  <?if($row['display_tem']=='3') echo "selected";?>>강조형(웹2단/모바일1단)</option>
							<option value="4"  <?if($row['display_tem']=='4') echo "selected";?>>세로형</option>
							<option value="5"  <?if($row['display_tem']=='5') echo "selected";?>>슬라이드형</option>
							
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
							<option value="1" >기본형(웹4단/모바일2단)</option>
							<option value="2" >복합형(웹7단/모바일3단)</option>
							<option value="3" >강조형(웹2단/모바일1단)</option>
							<option value="4" >세로형</option>
							<option value="5" >슬라이드형</option>
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
			<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='<?=$return_page_link ?>'">
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
$(document).ready(function(){
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
	 
	$(".img_view_sizeset").on('mouseover',function(){
		$("#img_view_div").find('img').attr('src',($(this).attr('src')));
		$("#img_view_div").find('img').css('display','block');
	});

	$(".img_view_sizeset").on('mouseout',function(){
		$("#img_view_div").find('img').css('display','none'); 
	});	
	
	$('input[name=image_type]:checked').trigger('click');
	$('input[name=image_type_m]:checked').trigger('click');
	
	
	//핏플랍 모바일 타이틀 배너 display
	$("#display_type").change(function() {
		if($("#display_type option:selected").val()=="B"){
			$("#fmobile").show();
		}else{
			$("#fmobile").hide();
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

function del_prmo(t){		
	if(confirm("삭제하시겠습니까?")){
		document.delform.ppidx.value=t;
		document.delform.submit();
	}
}

var oEditors = [];
var flagShowEditor = false;
var flagShowEditor_m = false;

$('input[name=image_type]').click(function(){
	var type = $(this).val();
	if(type == "E"){
		$('#img_E').show();

        // 에디터를 보여줘야 하는 경우
        if ( flagShowEditor == false ) {

            nhn.husky.EZCreator.createInIFrame({
                oAppRef: oEditors,
                elPlaceHolder: "ir1",
                sSkinURI: "../SE2/SmartEditor2Skin.html",
                htParams : {
                    bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                    //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                    fOnBeforeUnload : function(){
                    }
                },
                fOnAppLoad : function(){
                },
                fCreator: "createSEditor2"
            });

            flagShowEditor = true;
        }

		$('#img_F').hide();
	}else if(type == "F"){
		$('#img_E').hide();
		$('#img_F').show();		
	}
});

$('input[name=image_type_m]').click(function(){
	var type = $(this).val();
	if(type == "E"){
		$('#img_EM').show();

        // 에디터를 보여줘야 하는 경우
        if ( flagShowEditor_m == false ) {

            nhn.husky.EZCreator.createInIFrame({
                oAppRef: oEditors,
                elPlaceHolder: "ir1_m",
                sSkinURI: "../SE2/SmartEditor2Skin.html",
                htParams : {
                    bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                    //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                    fOnBeforeUnload : function(){
                    }
                },
                fOnAppLoad : function(){
                },
                fCreator: "createSEditor2"
            });

            flagShowEditor_m = true;
        }

		$('#img_FM').hide();
	}else if(type == "F"){
		$('#img_EM').hide();
		$('#img_FM').show();		
	}
});

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir2",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

// 이벤트 종류를 변경시 호출
function changeEventType(obj) {
    if ( $(obj).val() != '1' && $(obj).val() != '0' ) {
        // 이벤트 종류가 기획전이 아닌 경우
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
