<?php
/********************************************************************* 
// 파 일 명		: member_join.php 
// 설     명		: 회원가입 정보등록
// 상세설명	: 회원가입시 정보를 등록
// 작 성 자		: 2016.01.07 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	include("calendar.php");

####################### 페이지 접근권한 check ###############
	$PageCode	= "cs-1";
	$MenuCode	= "cscenter";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#############################################################

# 배송업체를 불러온다.
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);

#---------------------------------------------------------------
# 변수를 정리한다.
#---------------------------------------------------------------

	//exdebug($_POST);
	//exdebug($_GET);

	$CurrentTime	= time();
	$period[0]		= date("Y-m-d",$CurrentTime);
	$period[1]		= date("Y-m-d",$CurrentTime-(60*60*24*7));
	$period[2]		= date("Y-m-d",$CurrentTime-(60*60*24*14));
	$period[3]		= date("Y-m-d",strtotime('-1 month'));
	$period[4]		= substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);

	$orderby		= $_GET["orderby"];
	if(ord($orderby)==0) $orderby = "DESC";

	$s_check		= $_GET["s_check"];
	$search			= trim($_GET["search"]);
	$s_date			= $_GET["s_date"];
	if(ord($s_date)==0) $s_date = "ordercode";
	$prog_type=$_GET["prog_type"];
	
	if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
		$s_date = "ordercode";
	}
	
	$search_start		= $_GET["search_start"];
	$search_end		= $_GET["search_end"];
	$s_prod				= $_GET["s_prod"];
	$search_prod		= $_GET["search_prod"];
	$staff_order		= $_GET["staff_order"]?$_GET["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
	$dvcode				= $_GET["dvcode"];
	$oistep1				= $_GET["oistep1"];
	$oi_type				= $_GET["oi_type"];
	$paystate			= $_GET["paystate"]?$_GET["paystate"]:"A";
	$paymethod		= $_GET["paymethod"];
	$ord_flag			= $_GET["ord_flag"]; // 유입경로
	$mem_type		= $_GET["mem_type"]?$_GET["mem_type"]:"A"; // 회원구분

	if ($oistep1[0] == '' && $oi_type[0] == '') {
		$oistep1_def=array("0","1","2","3","4");
		foreach($oistep1_def as $k => $v) $oistep1[$k] = $v;
		$oi_type_def=array("44","65","70","71","68","63","64","67","61","62");
		foreach($oi_type_def as $k => $v) $oi_type[$k] = $v;
	}

	if ($paymethod[0] == '') {
		foreach(array_keys($arpm) as $k => $v) {
			$paymethod[$k] = $v;	
		}
	}

	if ($ord_flag[0] == '') {
		$ord_flag_def=array("PC","MO","AP");
		foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;	
	}

	if(is_array($oistep1)) $oistep1 = implode(",",$oistep1);
	if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
	if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
	if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

	$oistep1_arr		= explode(",",$oistep1);
	$oi_type_arr		= explode(",",$oi_type);
	$paymethod_arr	= explode("','",$paymethod);
	$ord_flag_arr		= explode("','",$ord_flag);

	$sel_vender		= $_GET["sel_vender"];  // 벤더 선택값으로 검색
	$brandname		= $_GET["brandname"];  // 벤더이름 검색

	$selected[s_check][$s_check]		= 'selected';
	$selected[s_date][$s_date]				= 'selected';
	$selected[s_prod][$s_prod]			= 'selected';
	$selected[staff_order][$staff_order]	= 'checked'; //스테프관련 추가 (2016.05.11 - 김재수)
	$selected[dvcode][$dvcode]			= 'selected';
	$selected[paystate][$paystate]		= 'checked';
	$selected[mem_type][$mem_type]		= 'checked';

	$search_start	= $search_start?$search_start:$period[1];
	$search_end	= $search_end?$search_end:date("Y-m-d",$CurrentTime);
	$search_s		= $search_start?str_replace("-","",$search_start."000000"):"";
	$search_e		= $search_end?str_replace("-","",$search_end."235959"):"";

#---------------------------------------------------------------
# 검색조건을 정리한다.
#---------------------------------------------------------------
	// 기본 검색 조건
	$qry_from = "tblorderinfo a ";
	$qry_from.= " join tblorderproduct b on a.ordercode = b.ordercode ";
	$qry.= "WHERE 1=1 ";

	// 기간선택 조건
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
		}
	}

	// 검색어
	if(ord($search)) {
// 		if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
// 		else if($s_check=="dv") $qry.= "AND b.deli_num = '{$search}' ";
// 		else if($s_check=="on") $qry.= "AND a.sender_name = '{$search}' ";
// 		else if($s_check=="oi") $qry.= "AND a.id = '{$search}' ";
// 		else if($s_check=="oh") $qry.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
// 		else if($s_check=="op") $qry.= "AND a.ip = '{$search}' ";
// 		else if($s_check=="sn") $qry.= "AND a.bank_sender = '{$search}' ";
// 		else if($s_check=="rn") $qry.= "AND a.receiver_name = '{$search}' ";
// 		else if($s_check=="rh") $qry.= "AND replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
// 		else if($s_check=="ra") $qry.= "AND a.receiver_addr like '%{$search}%' ";
// 		else if($s_check=="nm") $qry.= "AND (a.sender_name = '{$search}' OR a.bank_sender = '{$search}' OR a.receiver_name = '{$search}') ";
// 		else if($s_check=="al") {
// 			$or_qry[] = " a.ordercode like '%{$search}%' ";
// 			$or_qry[] = " b.deli_num = '{$search}' ";
// 			$or_qry[] = " a.sender_name = '{$search}' ";
// 			$or_qry[] = " a.id = '{$search}' ";
// 			$or_qry[] = " replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
// 			$or_qry[] = " a.ip = '{$search}' ";
// 			$or_qry[] = " a.bank_sender = '{$search}' ";
// 			$or_qry[] = " a.receiver_name = '{$search}' ";
// 			$or_qry[] = " replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
// 			$or_qry[] = " a.receiver_addr like '%{$search}%' ";
// 			$qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
// 		}

		$search = trim($search);
		$temp_search = explode("\r\n", $search);
		$cnt = count($temp_search);
		
		$search_arr = array();
		for($i = 0 ; $i < $cnt ; $i++){
			array_push($search_arr, "'%".$temp_search[$i]."%'");
		}
		
// 		$qry.= "AND a.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
		
		if($s_check=="oc") $qry.= "AND a.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
		else if($s_check=="dv") $qry.= "AND b.deli_num LIKE any ( array[".implode(",", $search_arr)."] )  ";
		else if($s_check=="on") $qry.= "AND a.sender_name LIKE any ( array[".implode(",", $search_arr)."] )  ";
		else if($s_check=="oi") $qry.= "AND a.id LIKE any ( array[".implode(",", $search_arr)."] )  ";
		else if($s_check=="oh") $qry.= "AND replace(a.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] )";
		else if($s_check=="op") $qry.= "AND a.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
		else if($s_check=="sn") $qry.= "AND a.bank_sender LIKE any ( array[".implode(",", $search_arr)."] )  ";
		else if($s_check=="rn") $qry.= "AND a.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
		else if($s_check=="rh") $qry.= "AND replace(a.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
		else if($s_check=="ra") $qry.= "AND a.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] )  ";
		else if($s_check=="nm") $qry.= "AND (a.sender_name LIKE any ( array[".implode(",", $search_arr)."] )  OR a.bank_sender LIKE any ( array[".implode(",", $search_arr)."] )  OR a.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
		else if($s_check=="al") {
			$or_qry[] = " a.ordercode LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " b.deli_num LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " a.sender_name LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " a.id LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " replace(a.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
			$or_qry[] = " a.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
			$or_qry[] = " a.bank_sender LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " a.receiver_name LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$or_qry[] = " replace(a.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
			$or_qry[] = " a.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] )  ";
			$qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
		}
		
	}

	// 상품 조건
	if(ord($search_prod)) {
		if($s_prod=="pn") $qry.= "AND upper(b.productname) like upper('%{$search_prod}%') ";
		else if($s_prod=="pc") $qry.= "AND upper(b.productcode) like upper('%{$search_prod}%') ";
		else if($s_prod=="sc") $qry.= "AND upper(b.selfcode) like upper('%{$search_prod}%') ";
	}

	// 주문구분 조건 (2016.05.11 - 김재수)
	if(ord($staff_order))	{
		if($staff_order != "A") $qry.= "AND a.staff_order = '{$staff_order}' ";
	}

	// 배송업체 조건
	if(ord($dvcode))	$qry.= "AND a.deli_com = '{$dvcode}' ";

	// 결제상태 조건
	if(ord($paystate)) {
		if($paystate == "N") $qry.="AND a.oi_step1 < 1";
		else if($paystate == "Y") $qry.="AND a.oi_step1 > 0";
	}


	// 결제타입 조건
	if(ord($paymethod))	$qry.= "AND SUBSTRING(a.paymethod,1,1) in ('".$paymethod."') ";

	// 유입경로 조건
	if(ord($ord_flag)) {
		$chk_mb = array();
		if(count($ord_flag_arr)) {
			foreach($ord_flag_arr as $k => $v) {
				switch($v) {
					case "PC" : $chk_mb[]	= "0"; break;
					case "MO" : $chk_mb[]	= "1"; break;
					case "AP" : $chk_mb[]	= "2"; break;
				}
			}
		}
		if(count($subWhere)) {
			 $qry.= "AND a.is_mobile in ('".implode("','",$chk_mb)."') ";
		}
	}

	// 회원구분 조건
	if(ord($mem_type)) {
		if($mem_type == "M") $qry.=" AND SUBSTRING(a.ordercode,21,1) = '' ";
		else if($mem_type == "X") $qry.=" AND SUBSTRING(a.ordercode,21,1) = 'X' ";
	}

	// 브랜드 조건
	if($sel_vender || $brandname) {
		if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
		else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

		//$qry_from.= " join tblvenderinfo v on b.vender = v.vender ".$subqry."";
		$qry_from.= " join tblproductbrand v on b.vender = v.vender ".$subqry."";
	} else {
		//$qry_from.= " join tblvenderinfo v on b.vender = v.vender ";
		$qry_from.= " join tblproductbrand v on b.vender = v.vender ";
	}

	// 주문상태별 조건
	if( $oistep1_arr[0] == '' ) $oistep1_arr = array();
	if( count($oistep1_arr) || count($oi_type_arr) ) {
		$subWhere = array();

		if(count($oistep1_arr)) {
			$subWhere[] = " (a.oi_step1 in (".$oistep1.") And a.oi_step2 = 0) ";
		}

		if(count($oi_type_arr)) {
			foreach($oi_type_arr as $k => $v) {
				switch($v) {
					case 44 : $subWhere[] = " (a.oi_step1 = 0 And a.oi_step2 = 44) "; break;    //입금전취소완료
					case 70 : $subWhere[] = " (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 41) "; break;   //취소접수
					case 71 : $subWhere[] = " (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 44) "; break;   //취소완료

					case 65 : $subWhere[] = " (b.store_stock_yn = 'N')"; break;  // 재고부족

					case 68 : $subWhere[] = " (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 40) "; break;    //반품신청
					case 63 : $subWhere[] = " (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step in (41,42)) "; break;    //반품접수
					case 64 : $subWhere[] = " (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And a.oi_step2 = 44) "; break;   //반품완료

					case 67 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 40) "; break;   //교환신청
					case 61 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 41) "; break;   //교환접수
					case 62 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 44) "; break;   //교환완료

				}
			}
		}

		//exdebug($subWhere);
		if(count($subWhere)) {
			$sub = " (".implode(" OR ", $subWhere)." ) ";
		}
	}
	//exdebug($sub);
	if($sub) $qry.= " AND ".$sub;
	
	//전체
	list($count_progress_all)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} GROUP BY a.ordercode) a "));
	list($count_progress_0)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 in ('0') And a.oi_step2 = 0) GROUP BY a.ordercode) a "));
	//결제완료
	list($count_progress_1)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 in ('1') And a.oi_step2 = 0) GROUP BY a.ordercode) a "));
	//배송준비중
	list($count_progress_2)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 in ('2') And a.oi_step2 = 0) GROUP BY a.ordercode) a "));
	//배송중
	list($count_progress_3)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 in ('3') And a.oi_step2 = 0) GROUP BY a.ordercode) a "));
	//배송완료&구매확정
	list($count_progress_4)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 in ('4') And a.oi_step2 = 0) GROUP BY a.ordercode) a "));
	// 입금전취소완료
	list($count_progress_44)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (a.oi_step1 = 0 And a.oi_step2 = 44) GROUP BY a.ordercode) a "));
	//취소접수
	list($count_progress_70)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 41) GROUP BY a.ordercode) a "));
	//취소완료
	list($count_progress_71)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 44) GROUP BY a.ordercode) a "));
	//재고부족
	list($count_progress_65)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.store_stock_yn = 'N') GROUP BY a.ordercode) a "));
	//반품신청
	list($count_progress_68)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 40) GROUP BY a.ordercode) a "));
	//반품접수
	list($count_progress_63)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step in (41,42)) GROUP BY a.ordercode) a "));
	//반품완료
	list($count_progress_64)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And a.oi_step2 = 44) GROUP BY a.ordercode) a "));
	//교환신청
	list($count_progress_67)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'G' And b.op_step = 40) GROUP BY a.ordercode) a "));
	//교환접수
	list($count_progress_61)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'G' And b.op_step = 41) GROUP BY a.ordercode) a "));
	//교환완료
	list($count_progress_62)=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} AND (b.redelivery_type = 'G' And b.op_step = 44) GROUP BY a.ordercode) a "));

	if($prog_type!='') {
		if($prog_type=='0' || $prog_type=='1' || $prog_type=='2' || $prog_type=='3' || $prog_type=='4') {
			$qry.= " AND (a.oi_step1 in (".$prog_type.") And a.oi_step2 = 0) ";
		}
		
		if($prog_type=='44') $qry.= " AND (a.oi_step1 = 0 And a.oi_step2 = 44) ";    //입금전취소완료

		if($prog_type=='65') $qry.= " AND (b.store_stock_yn = 'N')";  // 재고부족

		if($prog_type=='70') $qry.= " AND (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 41) ";   //취소접수
		if($prog_type=='71') $qry.= " AND (b.redelivery_type = 'N' And a.oi_step1 !='0' And b.op_step = 44) ";   //취소완료

		if($prog_type=='68') $qry.= " AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 40) ";    //반품신청
		if($prog_type=='63') $qry.= " AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step in (41,42)) ";    //반품접수
		if($prog_type=='64') $qry.= " AND (b.redelivery_type = 'Y' And a.oi_step1 in (2,3,4) And a.oi_step2 = 44) ";   //반품완료

		if($prog_type=='67') $qry.= " AND (b.redelivery_type = 'G' And b.op_step = 40) ";   //교환신청
		if($prog_type=='61') $qry.= " AND (b.redelivery_type = 'G' And b.op_step = 41) ";   //교환접수
		if($prog_type=='62') $qry.= " AND (b.redelivery_type = 'G' And b.op_step = 44) ";   //교환완료
	}



	if($type=="delete" && ord($ordercodes)) {	//주문서 삭제
		$ordercode=str_replace(",","','",$ordercodes);
		pmysql_query("INSERT INTO tblorderinfotemp SELECT * FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
		pmysql_query("INSERT INTO tblorderproducttemp SELECT * FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
		pmysql_query("INSERT INTO tblorderoptiontemp SELECT * FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

		pmysql_query("DELETE FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
		pmysql_query("DELETE FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
		pmysql_query("DELETE FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

		$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 삭제하였습니다.'); }</script>";
	}

	$t_price=0;

	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	pmysql_free_result($result);

	if($vendercnt>0){
		$venderlist=array();
		//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
		$sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
				FROM    tblvenderinfo a 
				JOIN    tblproductbrand b on a.vender = b.vender 
				ORDER BY lower(b.brandname) ASC
				";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$venderlist[$row->vender]=$row;
		}
		pmysql_free_result($result);
	}

	include("header.php"); 

	$sql = "SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} GROUP BY a.ordercode) a ";
	//$paging = new Paging($sql,10,20);
	//exdebug($sql);
	$paging = new newPaging($sql,10,20,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$excel_sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, 
							min(a.reserve) as reserve, min(a.point) as point, min(a.paymethod) as paymethod, min(a.pay_flag) as pay_flag, 
							min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, 
							min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type,
							min(productname) as productname, 
							(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt, 
							(select count(*) from tblorderproduct op where op.ordercode = a.ordercode AND op_step NOT IN ('0','1','2','3','4')) can_prod_cnt, 
							min(is_mobile) as is_mobile, 
							min(a.sender_tel) as sender_tel, min(a.sender_email) as sender_email 
					FROM {$qry_from} {$qry} ";
	$excel_sql_orderby = "
					GROUP BY a.ordercode 
					ORDER BY a.ordercode {$orderby} 
					";
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn	= $(this).attr('chk');
		 if($(this).prop("checked")){
			$("."+chk_cn).attr("checked", true);
		 } else {
			$("."+chk_cn).attr("checked", false);
		 }
	});
	$(".detail_area_tr").show();
});

// 온라인as
$(document).on("click", ".online_as", function(e) {
	var ordercode	= $(this).attr('ordercode');
	var idx	= $(this).attr('idx');
	
	var popup_url	= "cscenter_online_as_write.php?ordercode="+ordercode+"&idx="+idx;
	window.open(popup_url, "online_as", "scrollbars=yes,width=1000,height=700,resizable=yes");
});

function searchForm() {
	document.form1.action="cscenter_order_list_all_order.php";
    document.form1.method="GET";
    document.form1.prog_type.value="";
	document.form1.submit();
}

function progress_chage(prog_type) {
	document.form1.action="cscenter_order_list_all_order.php";
    document.form1.method="GET";
    document.form1.prog_type.value=prog_type;
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	
    //if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    //}else{
	//    pForm.search_start.value = '';
	//    pForm.search_end.value = '';
    //}
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function CrmView(id) {
	document.crmview.id.value = id;
	window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
	document.crmview.submit();
}

function OrderExcel() {
	document.downexcelform.ordercodes.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.ordercodes.value!='') document.downexcelform.ordercodes.value +=",";
			document.downexcelform.ordercodes.value+=document.form2.chkordercode[i].value;
		}
	}
	if(document.downexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function detail_open_all(chk) {
	$(".detail_open_sel").val(chk);
	if (chk == 'Y') {
		$(".detail_area_tr").show();
	} else if (chk == 'N') {
		$(".detail_area_tr").hide();
	}
}

function detail_open(obj, num, ordercode) {
	var chk	= obj.value;
	if (chk == 'Y') {
		/*$.ajax({
			type: "POST",
			url: "ajax_cs_orderproduct_list.php",
			data: "ordercode="+ordercode,
			dataType:"html",
			success: function(data){
				if (data)
				{
					$("#ord_prod_"+ordercode).html(data);*/
					$(".detail_area_"+num).show();
				/*}
			},
			complete: function(data){
			},
			error:function(xhr, status , error){
				alert("에러발생");
			}
		});*/
	} else if (chk == 'N') {
		$(".detail_area_"+num).hide();
	}
}

// 주문취소
$(document).on("click", ".ord_cancel", function(e) {
	var can_type	= $(this).attr('can_type');
	var ordercode	= $(this).attr('ordercode');
	var pc_type	= $(this).attr('pc_type');
	
	// 취소 - 주문접수상태일 경우
	if (can_type == 'cancel') {
		var idxs				= $("#ord_prod_"+ordercode).find("input[name=pr_idxs]").val();
		var paymethod	= $(this).attr("paymethod");

		/*alert(
			"mode : receive_cancel\n"+
			"ordercode : "+ordercode+"\n"+
			"idxs : "+idxs+"\n"+
			"paymethod : "+paymethod+"\n");
		return;*/

		if(confirm('취소를 하시겠습니까?')){
			$.post("cscenter_order_cancel_indb.php",{mode:"receive_cancel",ordercode:ordercode,idxs:idxs,paymethod:paymethod},function(data){
				alert(data.msg);
				if(data.type == 1){ 
					window.location.reload();
				}javascript:menu_over('0')
			},"json");
		}
	} else {
		var popup_name	= "cscenter_order_cancel_request_view_"+can_type+"_"+ordercode;
		if (pc_type == 'PROC') { // 처리
			var oc_no	= $(this).attr('oc_no');
			var popup_url	= "cscenter_order_cancel_detail.php?type="+can_type+"&oc_no="+oc_no;
			window.open(popup_url, popup_name, "scrollbars=yes,width=1000,height=700,resizable=yes");
		} else {
			var popup_url	= "cscenter_order_cancel_request.php?type="+can_type+"&ordercode="+ordercode;

			if (pc_type == 'PART') {
				var idx	= $(this).attr('idx');
				popup_url += "&idx="+idx;
				popup_name += "_"+idx;
			}
			window.open(popup_url, popup_name, "scrollbars=yes,width=1000,height=700,resizable=yes");
		}
	}
});
function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : CS 관리  &gt; CS 관리 &gt;<span>CS 통합 리스트</span></p></div></div>

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
			<?php include("menu_cscenter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">CS 통합 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=prog_type value="<?=$prog_type?>">
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
                                <select name="s_check" class="select" style="width:80px;height:32px;vertical-align:middle;">
                                    <option value="al" <?=$selected[s_check]["al"]?>>전체</option>
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <!-- <option value="dv" <?=$selected[s_check]["dv"]?>>송장번호</option> -->
                                    <option value="">----------------------</option>
                                    <option value="on" <?=$selected[s_check]["on"]?>>주문자명</option>
                                    <option value="oi" <?=$selected[s_check]["oi"]?>>주문자ID</option>
                                    <option value="oh" <?=$selected[s_check]["oh"]?>>주문자HP</option>
                                    <option value="op" <?=$selected[s_check]["op"]?>>주문자IP</option>
                                    <option value="">----------------------</option>
                                    <option value="sn" <?=$selected[s_check]["sn"]?>>입금자명</option>
                                    <option value="rn" <?=$selected[s_check]["rn"]?>>수령자명</option>
                                    <option value="rh" <?=$selected[s_check]["rh"]?>>수령자HP</option>
                                    <option value="ra" <?=$selected[s_check]["ra"]?>>배송지주소</option>
                                    <option value="">----------------------</option>
                                    <option value="nm" <?=$selected[s_check]["nm"]?>>주문자명,입금자명,수령자명</option>
                                </select>
                                <!-- 
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                                 -->
                                <textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea> 
                            </TD>
						</tr>

						<TR>
							<th><span>기간선택</span></th>
							<td>
                                <select name="s_date" class="select">
                                    <option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
                                    <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option>
                                    <option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option>
                                </select>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</TR>

						<tr>
							<th><span>상품</span></th>
							<TD class="td_con1">
                                <select name="s_prod" class="select">
                                    <option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
                                    <option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
                                    <option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
                                </select>
							    <input type=text name=search_prod value="<?=$search_prod?>" style="width:197" class="input">
                            </TD>
						</tr>

                        <TR style='display:none;'>
							<th><span>주문구분</span></th>
							<TD class="td_con1">
                                <input type="radio" name="staff_order" value="A" <?=$selected[staff_order]["A"]?>>전체</input>
                                <input type="radio" name="staff_order" value="N" <?=$selected[staff_order]["N"]?>>일반</input>
                                <input type="radio" name="staff_order" value="Y" <?=$selected[staff_order]["Y"]?>>임직원</input>
                            </TD>
						</TR>

                        <TR>
							<th><span>주문상태</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_oistep' name="oistep_all" value="<?=$k?>" <?if(count($oistep1_arr) == 5 && count($oi_type_arr) == 10) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<? 
                            foreach ($oi_step1 as $k=>$v){ 
?>
	                            <input type="checkbox" class='chk_oistep' name="oistep1[]" value="<?=$k?>" <?=( in_array($k, $oistep1_arr )?'checked':'')?>><?=$v?></input>
<?
                            } 
?>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="44" <?=(in_array(44,$oi_type_arr)?'checked':'')?>>입금전취소완료</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="65" <?=(in_array(65,$oi_type_arr)?'checked':'')?>>재고부족</input>

                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="70" <?=(in_array(70,$oi_type_arr)?'checked':'')?>>취소접수</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="71" <?=(in_array(71,$oi_type_arr)?'checked':'')?>>취소완료</input>

                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="67" <?=(in_array(67,$oi_type_arr)?'checked':'')?>>교환신청</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="61" <?=(in_array(61,$oi_type_arr)?'checked':'')?>>교환접수</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="62" <?=(in_array(62,$oi_type_arr)?'checked':'')?>>교환완료</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="68" <?=(in_array(68,$oi_type_arr)?'checked':'')?>>반품신청</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="63" <?=(in_array(63,$oi_type_arr)?'checked':'')?>>반품접수</input>
                                <input type="checkbox" class='chk_oistep' name="oi_type[]" value="64" <?=(in_array(64,$oi_type_arr)?'checked':'')?>>반품완료</input>
							</TD>
						</TR>

                        <TR>
							<th><span>결제상태</span></th>
							<TD class="td_con1">
                                <input type="radio" name="paystate" value="A" <?=$selected[paystate]["A"]?>>전체</input>
                                <input type="radio" name="paystate" value="N" <?=$selected[paystate]["N"]?>>입금전</input>
                                <input type="radio" name="paystate" value="Y" <?=$selected[paystate]["Y"]?>>입금완료(결제완료)</input>
                            </TD>
						</TR>

                        <TR>
							<th><span>결제타입</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="all" <?if(count($paymethod_arr) == count($arpm)) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?php
							foreach($arpm as $k => $v) {
								$selPaymethod='';
								if(in_array($k,$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" class='chk_paymethod' name="paymethod[]" value="<?=$k?>" <?=$selPaymethod?>><?=$v?>
<?
							}
?>
							</TD>
						</TR>

                        <TR>
							<th><span>유입경로</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="all" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="PC" <?=(in_array('PC',$ord_flag_arr)?'checked':'')?>>PC</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="MO" <?=(in_array('MO',$ord_flag_arr)?'checked':'')?>>MOBILE</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="AP" <?=(in_array('AP',$ord_flag_arr)?'checked':'')?>>APP</input>
                            </TD>
						</TR>

                        <TR>
							<th><span>회원구분</span></th>
							<TD class="td_con1">
                                <input type="radio" name="mem_type" value="A" <?=$selected[mem_type]["A"]?>>전체</input>
                                <input type="radio" name="mem_type" value="M" <?=$selected[mem_type]["M"]?>>회원</input>
                                <input type="radio" name="mem_type" value="X" <?=$selected[mem_type]["X"]?>>비회원</input>
                            </TD>
						</TR>

<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>브랜드</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                            foreach($venderlist as $key => $val) {
                                echo "<option value=\"{$val->vender}\"";
                                if($sel_vender==$val->vender) echo " selected";
                                echo ">{$val->brandname}</option>\n";
                            }
?>
                                </select> 
                                <input type=text name=brandname value="<?=$brandname?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
<?php
		$sql = "SELECT  a.ordercode, min(a.pg_ordercode) as pg_ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price,
                        min(a.reserve) as reserve, min(a.point) as point, min(a.paymethod) as paymethod, min(a.pay_flag) as pay_flag, 
                        min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, 
                        min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type,
                        min(productname) as productname, min(b.oc_no) as oc_no, min(a.deli_closed) as deli_closed,
                        (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt, 
						(select count(*) from tblorderproduct op where op.ordercode = a.ordercode AND op_step NOT IN ('0','1','2','3','4')) can_prod_cnt,  
						(select count(*) from (select op.oc_no from tblorderproduct op where op.ordercode = a.ordercode group by op.oc_no) gon) can_prod_chk_cnt, 
						(select count(*) from tblorderproduct op where op.ordercode = a.ordercode AND store_stock_yn ='N') op_store_stock_yn, 
                        min(is_mobile) as is_mobile 
                FROM {$qry_from} {$qry} 
                GROUP BY a.ordercode 
		        ORDER BY a.ordercode {$orderby} 
                ";

		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
//         echo "sql = ".$sql."<br>";
//         exdebug($sql);

		$colspan=13;
		if($vendercnt>0) $colspan++;
?>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">				
				<div class="table_style01">
				<table cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="20%"></col>
				<col width="16%"></col>
				<col width="16%"></col>
				<col width="16%"></col>
				<col width="16%"></col>
				<col width="16%"></col>
				<tr>
					<td><a href="javascript:progress_chage('');">전체 (<?=number_format($count_progress_all)?>)</a></td>
					<td><a href="javascript:progress_chage('0');">주문접수 (<?=number_format($count_progress_0)?>)</a></td>
					<td><a href="javascript:progress_chage('1');">결제완료 (<?=number_format($count_progress_1)?>)</a></td>
					<td><a href="javascript:progress_chage('2');">배송준비중 (<?=number_format($count_progress_2)?>)</a></td>
					<td><a href="javascript:progress_chage('3');">배송중 (<?=number_format($count_progress_3)?>)</a></td>
					<td><a href="javascript:progress_chage('4');">배송완료 (<?=number_format($count_progress_4)?>)</a></td>
				</tr>
				<tr>
					<td><a href="javascript:progress_chage('44');">입금전취소완료 (<?=number_format($count_progress_44)?>)</a></td>
					<td><a href="javascript:progress_chage('65');">재고부족 (<?=number_format($count_progress_65)?>)</a></td>
					<td><a href="javascript:progress_chage('70');">취소접수 (<?=number_format($count_progress_70)?>)</a></td>
					<td><a href="javascript:progress_chage('71');">취소완료 (<?=number_format($count_progress_71)?>)</a></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><a href="javascript:progress_chage('67');">교환신청 (<?=number_format($count_progress_67)?>)</a></td>
					<td><a href="javascript:progress_chage('61');">교환접수 (<?=number_format($count_progress_61)?>)</a></td>
					<td><a href="javascript:progress_chage('62');">교환완료 (<?=number_format($count_progress_62)?>)</a></td>
					<td><a href="javascript:progress_chage('68');">반품신청 (<?=number_format($count_progress_68)?>)</a></td>
					<td><a href="javascript:progress_chage('63');">반품접수 (<?=number_format($count_progress_63)?>)</a></td>
					<td><a href="javascript:progress_chage('64');">반품완료 (<?=number_format($count_progress_64)?>)</a></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">			
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right">
					<img src="images/icon_8a.gif" border="0">전체 상세 
					<select name='sel_detail_open_all' onChange="javascript:detail_open_all(this.value);">
					<option value="Y">열기</option>
					<option value="N">닫기</option>
					</select>&nbsp;&nbsp;
					<img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=40></col>
				<col width=130></col>
				<col width=80></col>
				<col width=200></col>
				<col width=200></col>
				<col width=></col>
                <col width=80></col>
                <col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=100></col>
				<input type=hidden name=chkordercode>
			
				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>번호</th>
					<th>주문일자</th>
					<th>유입경로</th>
					<th>주문번호</th>
					<th>주문자 정보</th>
					<th>상품</th>
					<th>상세</th>
					<th>금액</th>
					<th>실결제금액</th>
                    <th>결제방법</th>
					<th>처리단계</th>
					<th>CS처리</th>
				</TR>

<?php
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				//$stridM = $row->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>) / 주문번호: ".substr($row->id,1,6);
				$stridM = "<FONT COLOR=\"blue\">{$row->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>(비회원)</FONT>";
			} else {	//회원
				$stridM = "<a href=\"javascript:CrmView('$row->id');\"><FONT COLOR=\"blue\">{$row->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>({$row->id})</FONT></a>";
			}
			/*if($thisordcd!=$row->ordercode) {
				$thisordcd=$row->ordercode;
				if($thiscolor=="#FFFFFF") {
					$thiscolor="#FEF8ED";
                    //$thiscolor="#FFEEFF";
				} else {
					$thiscolor="#FFFFFF";
				}*/

				$thiscolor="#FFFFFF";
				$thiscolor2="#FFFFFF";
			//}

            /*if($row->prod_cnt > 1) $productname = strcutMbDot(strip_tags($row->productname), 35)." 외 ".($row->prod_cnt-1)."건";
            else $productname = strcutMbDot(strip_tags($row->productname), 35);*/
			$productname = "상품 ".$row->prod_cnt."종";

            $ord_status = "";   // 결제실패 정보
            if(strstr("V", $row->paymethod[0])) {
                if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[결제실패]</font>";
            }

            if(strstr("M", $row->paymethod[0])) {	//핸드폰
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[결제실패]</font>";
            }

            if(strstr("O", $row->paymethod[0])) {	//가상계좌
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[주문실패]</font>";
            }

            if(strstr("C", $row->paymethod[0])) {	//신용카드
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[카드실패]</font>";
            }
?>
			    <tr bgcolor=<?=$thiscolor?>>
			        <td align="center">
                        <input type=checkbox name=chkordercode value="<?=$row->ordercode?>"><br>
                    </td>
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$date?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=$arr_mobile2[$row->is_mobile]?></td>
                    <td style='text-align:center'><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?><br><FONT class=font_orange><?=$row->pg_ordercode?></font></A></td>
			        <td style='text-align:center'><?=$stridM?></td>
                    <td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')"><?=$productname?>&nbsp;<img src="images/newwindow.gif" border=0 align=absmiddle></a></td>
                    <td align="center">
					<select name='detail_open_<?=$number?>' class='detail_open_sel' onChange="detail_open(this, '<?=$number?>', '<?=$row->ordercode?>');">
					<option value="Y">열기</option>
					<option value="N">닫기</option>
					</select>
					</td>
                    <td style="text-align:right;font-size:8pt;padding:3"><?=number_format($row->price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align:right;font-size:8pt;padding:3"><?=number_format($row->price-$row->dc_price-$row->reserve-$row->point+$row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?><?=$ord_status?></td>
                    <td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2?><br>
					<?if($row->oi_step2 < '40'){

						if($row->oi_step1=="3" && $row->oi_step2=="0" && $row->deli_closed){
							echo "CJ배송완료";
						}else{
							echo $o_step[$row->oi_step1][$row->oi_step2];
						}
					}else{
						echo GetStatusOrder('o',$row->oi_step1,$row->oi_step2,'',$row->redelivery_type,$row->order_conf);
					}
					?>
					
					<?=$row->op_store_stock_yn>0?"<br><font color='red'>(재고부족)</font>":""?></td>
                    <td align=center style="padding:3">
<?
					if ($row->oi_step1 < 3) {
						if ($row->can_prod_cnt == 0) {
							if ($row->oi_step1 == '0') {
								$add_can_type	= "cancel";
							} else {
								$add_can_type	= "refund";
							}
?>
					<input type='button' value='취소' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" pc_type="ALL" can_type="<?=$add_can_type?>" paymethod="<?=$row->paymethod[0]?>">
<?	
						} else {
							if ($row->can_prod_chk_cnt == 1) {
								if ($row->oi_step1 != '0') {
									$add_can_type	= "refund";
?>
					<input type='button' value='취소처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" pc_type="PROC" can_type="<?=$add_can_type?>" oc_no="<?=$row->oc_no?>">
<?
								} else {
?>
					<input type='button' value='취소처리' class='btn_blue' style='padding:2px 5px 1px' onClick="javascript:OrderDetailView('<?=$row->ordercode?>');">

<?
								}
							} else {
								echo "-";
							}
						}
					} else {
						echo "-";
					}
?>
					</td>
				</tr>
				<tr bgcolor=<?=$thiscolor2?> class='detail_area_tr detail_area_<?=$number?>' style='display:none;'><td colspan=<?=$colspan?> align=center height=40 style='padding:0 0;'>
				<div id='ord_prod_<?=$row->ordercode?>'>				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=100></col>
				<col width=200></col>
				<col width=80></col>
				<col width=></col>
				<col width=150></col>
				<col width=90></col>
				<col width=40></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=150></col>
				<col width=170></col>			
				<tr bgcolor="#EFEFEF">
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>접수번호</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>주문번호</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>상품</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>옵션</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>판매가</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>수량</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>총금액</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>주문상태</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>O2O정보</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>배송정보</td>
					<td style='border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>CS처리</td>
				</tr>
			<?
				#주문상품
				$prod_sql = "SELECT 
								a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
								a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
								a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
								b.minimage, a.option_type, a.option_price, a.option_quantity, 
								a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
								a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
								a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, a.use_epoint, b.option1_tf, option2_tf, option2_maxlen, 
								a.delivery_type, a.store_code, a.reservation_date, a.oc_no, a.store_stock_yn, b.prodcode, b.colorcode, a.deli_closed
							FROM 
								tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
							WHERE 
								a.ordercode='".$row->ordercode."' 
							ORDER BY a.vender, a.idx ";

				$prod_result	= pmysql_query($prod_sql,get_db_conn());
				$pr_idxs		= "";

				while($prod_row=pmysql_fetch_object($prod_result)) {
					if ($pr_idxs == '') {
						$pr_idxs		.= $prod_row->idx;
					} else {
						$pr_idxs		.= "|".$prod_row->idx;
					}

					$file = getProductImage($Dir.DataDir.'shopimages/product/', $prod_row->tinyimage);

					$optStr	= "";
					$option1	 = $prod_row->opt1_name;
					$option2	 = $prod_row->opt2_name;

					if( strlen( trim( $prod_row->opt1_name ) ) > 0 ) {
						$opt1_name_arr	= explode("@#", $prod_row->opt1_name);
						$opt2_name_arr	= explode(chr(30), $prod_row->opt2_name);
						for($g=0;$g < sizeof($opt1_name_arr);$g++) {
							if ($g > 0) $optStr	.= " / ";
							$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
						}
					}

					if( strlen( trim( $prod_row->text_opt_subject ) ) > 0 ) {
						$text_opt_subject_arr	= explode("@#", $prod_row->text_opt_subject);
						$text_opt_content_arr	= explode("@#", $prod_row->text_opt_content);

						for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
							if ($text_opt_content_arr[$s]) {
								if ($optStr != '') $optStr	.= " / ";
								$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
							}
						}
					}
					$oc_reg_type_txt="";

					if ($prod_row->oc_no) {
						list($oc_reg_type, $proc_type)=pmysql_fetch_array(pmysql_query("SELECT reg_type, proc_type FROM tblorder_cancel WHERE oc_no='".$prod_row->oc_no."' "));
						$oc_reg_type_txt="-";
						if ($oc_reg_type =='admin') {
							$oc_reg_type_txt=$proc_type;
						} else if ($oc_reg_type =='user') {
							$oc_reg_type_txt="고객";
						} else if ($oc_reg_type =='api') {
							$oc_reg_type_txt="API";
						}
					}

					$erp_pc_code	= "&nbsp;&nbsp;[".$prod_row->prodcode."-".$prod_row->colorcode."]";
					
					$storeData = getStoreData($prod_row->store_code);
					
			?>
				<tr>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$prod_row->oc_no?$prod_row->oc_no:'-'?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$row->ordercode?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><a href="javascript:ProductDetail('<?=$prod_row->productcode?>')"><img src="<?=$file?>" style="width:70px" border="1" alt="<?=$prod_row->productname?>"></a></td>
					<td style='padding:5px;border-bottom:1px solid #cbcbcb;text-align:left'><a href="javascript:ProductDetail('<?=$prod_row->productcode?>')"><strong>[<?=$prod_row->brandname?>]</strong><br><?=$prod_row->productname?><?=$erp_pc_code?></a></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:left'><?=$optStr?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($prod_row->price)?>원</td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($prod_row->option_quantity)?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($prod_row->price * $prod_row->option_quantity)?>원</td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
						<?if($prod_row->deli_closed && $prod_row->op_step=="3"){
						echo "CJ배송완료";
						}else{?>
						<?=GetStatusOrder("p", $row->oi_step1, $row->oi_step2, $prod_row->op_step, $prod_row->redelivery_type, $prod_row->order_conf)?>
					<?}?>
						<?=$oc_reg_type_txt?"<br>(".$oc_reg_type_txt.")":""?><?=$prod_row->store_stock_yn=='N'?"<br><font color='red'>(재고부족)</font>":""?>
					</td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
					<?
						echo '<strong>'.$arrChainCode[$prod_row->delivery_type].'</strong>';
						if( $prod_row->reservation_date ){
							echo '<br>'.substr($prod_row->reservation_date, 0, 4).".".substr($prod_row->reservation_date, 5, 2).".".substr($prod_row->reservation_date, 8, 2);
						}
						if($prod_row->store_code){
							echo '<br>'.$storeData["name"];
							echo '<br>'.$prod_row->store_code;
						}
					?>
					</td><!-- O2O 배송 -->
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$prod_row->deli_num?$delicomlist[$prod_row->deli_com]->company_name."<br><font color='blue'>".$prod_row->deli_num."</font>":"-"?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
					
				<? if ($prod_row->op_step < 40) { //주문취소 신청및 완료상태가 아닌경우
						if( $prod_row->op_step == 1 || $prod_row->op_step == 2 ){ // 입금완료, 배송 준비중일 경우
				?>
					<input type='button' value='취소' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" idx = "<?=$prod_row->idx?>" pc_type="PART" can_type="refund">
				<?
						} else if( $prod_row->op_step == 3 || $prod_row->op_step == 4){ // 배송중일 경우
				?>
					<input type='button' value='반품' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" idx = "<?=$prod_row->idx?>" pc_type="PART" can_type="regoods">&nbsp;<input type='button' value='교환' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" idx = "<?=$prod_row->idx?>" pc_type="PART" can_type="rechange">
				<?
						} else {
						echo "-";
						}
					} else {
						if ($prod_row->op_step >= 40 && $prod_row->op_step <= 44 && $proc_type != 'AS') { // 취소 진행중인 상태일 경우
							if( $prod_row->redelivery_type == 'N'){ // 입금완료, 배송 준비중일 경우
								if( $row->oi_step1 != '0' && $row->can_prod_chk_cnt > 1){ // 주문접수가 아니고 전체취소가 아닐경우
				?>
						<input type='button' value='취소처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" oc_no = "<?=$prod_row->oc_no?>" pc_type="PROC" can_type="refund">
				<?
								}
							} else if( $prod_row->redelivery_type == 'Y'){ // 반품일 경우
				?>
						<input type='button' value='반품처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" oc_no = "<?=$prod_row->oc_no?>" pc_type="PROC" can_type="regoods">
				<?
							} else if( $prod_row->redelivery_type == 'G'){ // 교환일 경우
				?>
						<input type='button' value='교환처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" oc_no = "<?=$prod_row->oc_no?>" pc_type="PROC" can_type="rechange">
				<?
							} 
						}else {
						echo "-";
						}
						
					}
				
				?>
					<!--<input type='button' value='온라인AS' class='btn_blue online_as' style='padding:2px 5px 1px' ordercode = "<?=$row->ordercode?>" idx = "<?=$prod_row->idx?>" pc_type="PART" can_type="onlineas">-->
					</td>
				</tr>
			<?
				}
			?>
				</table>
				<input type=hidden name=pr_idxs value="<?=$pr_idxs?>">
				</div>
				<div style="font=align:center;padding:10px; line-height:140%;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;">
                <strong><?=number_format($row->price)?>원 + 배송비 <?=number_format($row->deli_price)?>원 = <?=number_format($row->price + $row->deli_price)?>원</strong>
                <br>
                쿠폰 <?=number_format($row->dc_price)?>원 / 포인트 <?=number_format($row->reserve)?>원 / E포인트 <?=number_format($row->point)?>원 
				<br>
                <br>
				<strong style="color:#FF0000">최종결제금액 <?=number_format($row->price-$row->dc_price-$row->reserve-$row->point+$row->deli_price)?>원 </strong>
				</div>
				</td></tr>
<?


			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:20px">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=130></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left' valign=middle><a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
				</tr>
				<tr>
					
					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
					
				<tr>
				</table>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">

			</form>

			<!-- <form name=detailform method="post" action="order_detail_v2.php" target="orderdetail"> -->
            <form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=dvcode value="<?=$dvcode?>">
            <input type=hidden name=oistep1 value="<?=$oistep1?>">
            <input type=hidden name=oi_type value="<?=$oi_type?>">
            <input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<!-- <input type=hidden name=deli_gbn value="<?=$deli_gbn?>"> -->
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
			<!-- <input type=hidden name=redelivery_type value="<?=$redelivery_type?>"> -->
            <input type=hidden name=ord_flag value="<?=$ord_flag?>">
            <input type=hidden name=mem_type value="<?=$mem_type?>">
            <input type=hidden name=prog_type value="<?=$prog_type?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="cs_order_all">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="ordercodes">
			</form>

			<!-- <form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form> -->

			<!-- <form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form> -->

			<!-- <form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			</form> -->

            <form name=stepform action="order_state_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idx>
			<input type=hidden name=ordercodes>
			</form>
            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

            <form name=crmview method="post" action="crm_view.php">
			<input type=hidden name=id>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배송/입금일별 주문조회</span></dt>
							<dd>
								- 입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 부가기능</span></dt>
							<dd>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 주의사항</span></dt>
							<dd>- 배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
?>