<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

//print_r($_POST);

$ordercodes   = $_POST["ordercodes"];

$orderby    = $_POST["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$s_date     = $_POST["s_date"];
if(ord($s_date)==0) $s_date = "ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date = "ordercode";
}
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$s_prod         = $_POST["s_prod"];
$search_prod    = $_POST["search_prod"];
$staff_order    = $_POST["staff_order"]?$_POST["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$dvcode         = $_POST["dvcode"];
$oistep1        = $_POST["oistep1"];
$oi_type        = $_POST["oi_type"];
$paystate       = $_POST["paystate"]?$_POST["paystate"]:"A";
$paymethod      = $_POST["paymethod"];
$ord_flag       = $_POST["ord_flag"]; // 유입경로

if ($oistep1[0] == '' && $oi_type[0] == '') {
	$oistep1_def=array("0","1","2","3","4");
	foreach($oistep1_def as $k => $v) $oistep1[$k] = $v;
	$oi_type_def=array("44","67","61","62","68","63","64","65","66");
	foreach($oi_type_def as $k => $v) $oi_type[$k] = $v;
}

if ($paymethod[0] == '') {
	$paymethod_def=array("B","CA","VA","OA","ME","QA","YF");
	foreach($paymethod_def as $k => $v) $paymethod[$k] = $v;	
}

if ($ord_flag[0] == '') {
	$ord_flag_def=array("PC","MO","AP");
	foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;	
}

if(is_array($oistep1)) $oistep1 = implode(",",$oistep1);
if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

$oistep1_arr  = explode(",",$oistep1);
$oi_type_arr  = explode(",",$oi_type);
$paymethod_arr  = explode("','",$paymethod);
$ord_flag_arr  = explode("','",$ord_flag);

$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

$CurrentTime = time();

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

/*$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('주문서 EXCEL 다운로드 기간은 1년을 초과할 수 없습니다.');
}*/

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=order_excel_all_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//$excel_info = "49,0,35,1,52,21,6,25,23,50,28,27,51,10,53";
$excel_info = "49,0,35,1,5,3,4,52,21,6,25,23,50,28,27,51,10,53";
//$excel_info = trim(str_replace("32,","",$_shopdata->excel_info),',');
$excel_ok  = $_shopdata->excel_ok;
//print_r($excel_info);

$excelval=array(
	array("일자"									,&$date),				#0
	array("주문자"									,&$sender_name),		#1
	array("주문자 전화(XXXXXXXX)"					,&$sender_telnum),		#2
	array("주문자 전화"				                ,&$sender_tel),			#3
	array("이메일"									,&$sender_email),		#4
	array("주문ID"							        ,&$idnum),				#5
	array("결제방법"								,&$paymethod),			#6
	array("결제상태"								,&$pay),				#7
	array("결제방법(상태)"							,&$pay2),				#8
	array("주문금액(배송비제외)"					,&$sumprice),			#9
	array("처리단계"								,&$deli_gbn),			#10
	array("받는사람"								,&$receiver_name),		#11
	array("전화번호 비상전화"						,&$receiver_tel),		#12
	array("전화번호(XXXXXXXX)"						,&$receiver_tel1num),	#13
	array("비상전화(XXXXXXXX)"						,&$receiver_tel2num),	#14
	array("전화번호(XX-XXXX-XXXX)"					,&$receiver_tel1),		#15
	array("비상전화(XX-XXXX-XXXX)"					,&$receiver_tel2),		#16
	array("우편번호(XXXXXX)"						,&$post1),				#17
	array("우편번호(XXX-XXX)"						,&$post2),				#18
	array("주소"									,&$addr),				#19
	array("전달사항"								,&$message),			#20
	array("상품명"									,&$product),			#21
	array("옵션(특징포함)"							,&$option2),			#22
	array("수량"									,&$quantity),			#23
	array("옵션1 - 옵션2"	                        ,&$productname),		#24
	array("금액"								    ,&$price),				#25
	array("상품 적립금"								,&$reserve),			#26
	array("배송비"									,&$deli_price),			#27
	array("사용포인트"								,&$usereserve),			#28
	array("입금일"									,&$bank_date),			#29
	array("배송일"									,&$deli_date),			#30
	array("주문관련메모(관리자)"					,&$adminmemo),			#31
	array("고객알리미"								,&$usermemo),			#32
	array("상품명1-갯수-옵션^상품명2-갯수-옵션"		,&$productname2),		#33
	array("상품별 송장번호"							,&$deli_num),			#34
	array("주문번호"								,&$ordercode),			#35
	array("상품코드"								,&$productcode),		#36
	array("은행계좌(카드내역)"						,&$pay_data),			#37
	array("옵션"									,&$option),				#38
	array("특징"									,&$addcode),			#39
	array("상품명(태그제거안함)"					,&$product1),			#40
	array("전달사항(태그제거안함)"					,&$messnotag),			#41
	array("일자(시분초 표시)"						,&$orderdate),			#42

	array("상품별 처리여부"							,&$prdt_deli_gbn),		#43
	array("주문메세지"						        ,&$prdt_message),		#44
	array("상품별 배송일"							,&$prdt_deli_date),		#45
	array("진열코드"								,&$prdt_selfcode),		#46
	array("거래처정보"								,&$prdt_business),		#47
	array("사은품"									,&$gift_name),			#48
	array("번호"									,&$vnum),				#49
	array("쿠폰할인"								,&$coupon_price),		#50
	array("실결제금액"								,&$totprice),			#51
	array("브랜드"									,&$brand),				#52
	array("유입경로"								,&$is_mobile)			#53
);

$isproductall="N";
if(preg_match("/24|33/",$excel_info)){	//상품명1-갯수-옵션 ^ 상품명2-갯수-옵션 일 경우
	$isproductall="Y";
}
$isproduct ="N";
if(preg_match("/2([12356]{1})|34|38|39|40|43|44|45|46|47/",$excel_info)){
	$isproduct="Y";
}
$arr_excel = explode(",",$excel_info);
$cnt = count($arr_excel);

if(ord($ordercodes)) $ordercodes="'".str_replace(",","','",$ordercodes)."'";
if($ordercodes) $ordercodes= str_replace(",''","",$ordercodes);


	if(($s_check=="bank_date" || $s_check=="deli_date") && ord($ordercodes)==0) {
		$tablecode=$s_check;
		$sql = "SELECT ordercode FROM tblorderinfo WHERE {$tablecode} >= '{$search_s}' AND {$tablecode} <= '{$search_e}' ";
		$result = pmysql_query($sql,get_db_conn());
		$_ = array();
		while($row=pmysql_fetch_object($result)) {
			$_[] = "'{$row->ordercode}'";
		}
		pmysql_free_result($result);
		$tempordercode=implode(',',$_);
	} else {
		$tablecode="ordercode";
	}

	if($termday<=92) {
		$sql = "SELECT * FROM tblorderoption ";
		if(ord($tempordercode)) 
			$sql.= "WHERE ordercode IN ({$tempordercode}) ";
		elseif(ord($ordercodes)) 
			$sql.= "WHERE ordercode IN ({$ordercodes}) ";
		else
			$sql.= "WHERE ordercode >= '{$search_s}' AND ordercode <= '{$search_e}' ";
		$result = pmysql_query($sql,get_db_conn());
		while($row = pmysql_fetch_object($result)) {
			$optionkey=$row->ordercode.$row->productcode.$row->opt_idx;
			$addoption[$optionkey]=$row->opt_name;
		}
		pmysql_free_result($result);
	}

	//벤더, 브랜드 리스트 검색
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

	// 기본 검색 조건
	$qry_from = "tblorderproduct a ";
	$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
	$qry.= "WHERE 1=1 ";

	// 기간선택 조건
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND b.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND b.{$s_date}>='{$search_s}' AND b.{$s_date} <='{$search_e}' ";
		}
	}

	if(ord($ordercodes)) $qry.= " AND a.idx IN (".$ordercodes.") ";

	// 기본옵션만 검색 (2016-03-08 김재수 막음 - 추가옵션도 있어서..)
	//$qry.= "AND b.option_type = 0 ";

	// 검색어
	if(ord($search)) {
		if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
		else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
		else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
		else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
		else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
		else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
		else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
		else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
		else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
		else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
		else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
	}

	// 상품 조건
	if(ord($search_prod)) {
		if($s_prod=="pn") $qry.= "AND upper(a.productname) like upper('%{$search_prod}%') ";
		else if($s_prod=="pc") $qry.= "AND upper(a.productcode) like upper('%{$search_prod}%') ";
		else if($s_prod=="sc") $qry.= "AND upper(a.selfcode) like upper('%{$search_prod}%') ";
	}

	// 주문구분 조건 (2016.05.11 - 김재수)
	if(ord($staff_order))	{
		if($staff_order != "A") $qry.= "AND b.staff_order = '{$staff_order}' ";
	}

	// 배송업체 조건
	if(ord($dvcode))	$qry.= "AND a.deli_com = '{$dvcode}' ";

	// 결제상태 조건
	if(ord($paystate)) {
		if($paystate == "N") $qry.="AND b.oi_step1 < 1";
		else if($paystate == "Y") $qry.="AND b.oi_step1 > 0";
	}

	// 주문상태별 조건
	if( $oistep1_arr[0] == '' ) $oistep1_arr = array();
	//exdebug(count($oistep1_arr));
	//exdebug($oistep1);
	//exdebug(count($oi_type));
	if( count($oistep1_arr) || count($oi_type_arr) ) {
		$subWhere = array();

		if(count($oistep1_arr)) {
			//$subWhere[] = " (b.oi_step1 in (".implode(", ", $oistep1_arr).") And b.oi_step2 = 0) ";
			//$subWhere[] = " (b.oi_step1 in (".$oistep1.") And b.oi_step2 = 0) ";
			$subWhere[] = " a.op_step in (".$oistep1.") ";
		}

		if(count($oi_type_arr)) {
			foreach($oi_type_arr as $k => $v) {
				switch($v) {
					case 44 : $subWhere[] = " (b.oi_step1 = 0 And b.oi_step2 = 44) "; break;    //입금전취소완료
					//case 61 : $subWhere[] = " (b.oi_step1 in (3,4) And (coalesce(a.opt1_change, '') != '' OR coalesce(a.opt2_change, '') != '') And a.op_step = 41) "; break;   //교환접수
					//case 62 : $subWhere[] = " (b.oi_step1 in (3,4) And (coalesce(a.opt1_change, '') != '' OR coalesce(a.opt2_change, '') != '') And a.op_step = 44) "; break;   //교환완료
					// 2016-02-12 jhjeong redelivery_type = 'G' 추가..옵션없는 상품의 교환일 경우 구분할수 있는 값이 없어서 추가함.
					case 67 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 40) "; break;   //교환신청
					case 61 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) "; break;   //교환접수
					case 62 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) "; break;   //교환완료
					case 68 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 40) "; break;    //반품신청
					case 63 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) "; break;    //반품접수
					case 64 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
					case 65 : $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))"; break;  //환불접수
					case 66 : $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) "; break;  //환불완료

					/*case 61 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) "; break;   //교환접수
					case 62 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) "; break;   //교환완료
					case 63 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) "; break;    //반품접수
					case 64 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
					case 65 : $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))"; break;  //환불접수
					case 66 : $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) "; break;  //환불완료*/
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


	// 결제타입 조건
	if(ord($paymethod))	$qry.= "AND b.paymethod in ('".$paymethod."')";

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
			 $qry.= "AND b.is_mobile in ('".implode("','",$chk_mb)."')";
		}
	}

	// 브랜드 조건
	if($sel_vender || $brandname) {
		if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
		else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

		//$qry_from.= " join tblvenderinfo v on b.vender = v.vender ".$subqry."";
		$qry_from.= " join tblproductbrand v on a.vender = v.vender ".$subqry."";
	} else {
		//$qry_from.= " join tblvenderinfo v on b.vender = v.vender ";
		$qry_from.= " join tblproductbrand v on a.vender = v.vender ";
	}




	/*$sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, 
					min(a.reserve) as reserve, min(a.paymethod) as paymethod, min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, 
					min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type,
					min(productname) as productname, 
					(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
			FROM {$qry_from} {$qry} 
			GROUP BY a.ordercode 
			ORDER BY a.ordercode {$orderby} 
			";*/

	$sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, 
                    (a.price+a.option_price) as price, a.option_quantity, 
                    a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
					a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
					b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, b.is_mobile, 
                    b.sender_tel, b.sender_email 
			FROM {$qry_from} {$qry} 
			ORDER BY a.ordercode {$orderby}, a.vender DESC
			";

$result = pmysql_query($sql,get_db_conn());
//echo $sql;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<?
//print_r($_POST);
//echo $sql;
echo "<table border=1><tr>";
if($title!="NO") {
	for($i=0;$i<$cnt;$i++) {
		//if($i!=0){ 
            echo "<td>";
            echo $excelval[$arr_excel[$i]][0];
            echo "</td>";
		//}
	}
	echo "</tr>";
}

$pattern = array("\r\n","\"",",",";");
$replacement = array(" ","",".","");

$temp = "";
$chkordercode='';
//$cnt=pmysql_num_rows($result);
$vnum=0;
while ($row=pmysql_fetch_object($result)) {
	$vnum++;

	if($chkordercode!=$row->ordercode){
		$chkordercode=$row->ordercode;

		//사은품 쿼리
		$gift_qry="select productname from tblorderproduct where ordercode='".$chkordercode."' and productcode like '%GIFT' ";
		$gift_res=pmysql_query($gift_qry);
		$gift_row=pmysql_fetch_array($gift_res);
		pmysql_free_result($gift_res); 
		$gift_name=$gift_row['productname'];	
	}else{
		$gift_name="";	
	}
	
	//주문메세지NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') 
	list($row->order_prmsg)=pmysql_fetch("select order_prmsg from tblorderproduct where ordercode='".$row->ordercode."' AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%')  limit 1");
	//if ($temp!=$row->ordercode) {
		if($isproductall=="Y" && strlen($temp)!=0) {
			echo "<tr>";
			for($i=0;$i<$cnt;$i++) {
				//if($i!=0){
	
                    echo "<td>";
                    echo doubleQuote($excelval[$arr_excel[$i]][1]);
                    echo "</td>";
				//}
			}
			echo "</tr>";
		}
		$ordercode=$row->ordercode;
		$temp=$row->ordercode;
		$date = substr($row->ordercode, 0, 12);
		$date = substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";   //날짜 형식 수정  
		$orderdate = str_replace("/","-",$date)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2).")";
		$sender_name=$row->sender_name;
		$pay_data=$row->pay_data;
		$sender_email=$row->sender_email;

		if(substr($row->ordercode,20)=="X") {	//비회원
			$idnum = "(비회원)".substr($row->id,1,6);
		} else {	//회원
			$idnum = $row->id;
		}

		if(strstr("Y", $row->paymethod[0])) {	//PAYCO
			$paymethod="PAYCO";
        } elseif(strstr("B", $row->paymethod[0])) {	//무통장
			$paymethod="무통장";
			if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") $pay="환불";
			elseif (ord($row->bank_date)) $pay="입금완료";
			else $pay="미입금";
		} elseif(strstr("V", $row->paymethod[0])) {	//계좌이체
			$paymethod="실시간계좌이체";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
			elseif ($row->pay_flag=="0000") $pay="결제완료";
		} elseif(strstr("M", $row->paymethod[0])) {	//핸드폰
			$paymethod="핸드폰결제";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
			elseif ($row->pay_flag=="0000") $pay="결제완료";
		} elseif(strstr("OQ", $row->paymethod[0])) {	//가상계좌
			if(strstr("O", $row->paymethod[0])) $paymethod="가상계좌";
			elseif(strstr("Q", $row->paymethod[0])) $paymethod="가상계좌(매매보호)";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="주문실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
			elseif ($row->pay_flag=="0000" && ord($row->bank_date)==0) $pay="미입금";
			elseif ($row->pay_flag=="0000" && ord($row->bank_date)) $pay="입금완료";
		} else {
			if(strstr("C", $row->paymethod[0])) $paymethod="신용카드";
			elseif(strstr("P", $row->paymethod[0])) $paymethod="신용카드(매매보호)";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="카드실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") $pay="카드승인";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") $pay="결제완료";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
		}

		$pay2 = $paymethod."[{$pay}]";
		$sumprice=$row->sumprice - $row->coupon_price - $row->use_point;
		$totprice=($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point+$row->deli_price;
		$deli_price=$row->deli_price;
		$coupon_price=$row->coupon_price;
		$usereserve=$row->use_point;
		/*switch($row->prdt_deli_gbn) {
			case 'S': $deli_gbn="배송준비";  break;
			case 'X': $deli_gbn="배송요청";  break;
			case 'Y': $deli_gbn="배송중";  break;
			case 'D': $deli_gbn="취소요청";  break;
			case 'N': $deli_gbn="주문접수";  break;
			case 'E': $deli_gbn="환불대기";  break;
			case 'C': $deli_gbn="주문취소";  break;
			case 'R': $deli_gbn="반송";  break;
			case 'H': $deli_gbn="배송(정산보류)";  break;
		}*/
		$deli_gbn=$op_step[$row->op_step];
		$sender_telnum=check_num($row->sender_tel);
		$receiver_tel1num=check_num($row->receiver_tel1);
		$receiver_tel2num=check_num($row->receiver_tel2);
		$receiver_tel = $receiver_tel1num." ".$receiver_tel2num;
		$sender_tel = replace_tel($sender_telnum);
		$receiver_tel1 = replace_tel($receiver_tel1num);
		$receiver_tel2 = replace_tel($receiver_tel2num);
//		$sender_telnum="=\"{$sender_telnum}\""; 
//		$receiver_tel1num="=\"{$receiver_tel1num}\""; 
//		$receiver_tel2num="=\"{$receiver_tel2num}\""; 
		$sender_telnum="{$sender_telnum}"; 
		$receiver_tel1num="{$receiver_tel1num}"; 
		$receiver_tel2num="{$receiver_tel2num}"; 
		$receiver_name=$row->receiver_name;
		$bank_date="=\"".($row->paymethod=="B"?$row->bank_date:substr($row->ordercode,0,14))."\"";
		$deli_date="=\"{$row->deli_date}\"";
		$row->receiver_addr=str_replace("\r\n","",$row->receiver_addr);
		$row->receiver_addr=str_replace("\n","",$row->receiver_addr);
		$row->receiver_addr=str_replace("↑=↑"," ",$row->receiver_addr);
		$receiver_addr=explode("주소 : ",$row->receiver_addr);
		$post1 = substr($receiver_addr[0],11,3).substr($receiver_addr[0],15,3);
		$post2 = str_replace("우편번호 : ","",$receiver_addr[0]);
		$addr = $receiver_addr[1]; 
		$mess=explode("[MEMO]",$row->order_msg);
		$message=str_replace($pattern,$replacement,strip_tags($mess[0]));
		$messnotag=str_replace($pattern,$replacement,$mess[0]);
		$adminmemo=str_replace($pattern,$replacement,$mess[1]);
		$usermemo=str_replace($pattern,$replacement,$mess[2]);
		$quantity=$row->quantity;

		$brand	= $venderlist[$row->vender]->brandname;

		$product1=str_replace(",","",$row->productname);

		$product = $row->productname;

		//$product=strip_tags($product1);
		$productcode="=\"{$row->productcode}\"";
		$price=$row->price;
		$reserve=$row->reserve;
		$option=$option2=$addcode="";
		if (ord($row->addcode)) $addcode=$row->addcode;
		if (ord($row->opt1_name)) {
			if(strpos($row->opt1_name,"[OPTG")===0) {
				$key=$row->ordercode.$row->productcode.$row->opt1_name;
				$option.=$addoption[$key];
			} else {
				$option.=(ord($option)==0?"":"-").$row->opt1_name;
			}
		}
		if (ord($row->opt2_name)) $option.=(ord($option)==0?"":" - ").$row->opt2_name;
		$option2=$addcode.$option;
		//$productname=$product."-".$quantity.(ord($option2)==0?"":"-".$option2);
        $productname=(ord($option2)==0?"":$option2);
		$productname2=$productname;
		$deli_num=$row->deli_num;
		$prdt_message=str_replace($pattern,$replacement,strip_tags($row->order_prmsg));
		//$prdt_deli_gbn=$row->prdt_deli_gbn;
        $prdt_deli_gbn=$deli_gbn;
		$prdt_deli_date=$row->prdt_deli_date;
		$prdt_selfcode=$row->selfcode;
		$prdt_business=$row->productbisiness;
        $is_mobile = $arr_mobile[$row->is_mobile];
	//}
    /*
    elseif($isproductall=="Y") {
		$quantity=$row->quantity;
		$productcode="=\"{$row->productcode}\"";
		$product1=str_replace(",","",$row->productname);
		$product=strip_tags($product1);
		$price=$row->price;
		$reserve=$row->reserve;
		$option=$option2=$addcode="";
		if(ord($row->addcode)) $addcode=$row->addcode;
		if(ord($row->opt1_name)) {
			if(strpos($row->opt1_name,"[OPTG")===0) {
				$key=$row->ordercode.$row->productcode.$row->opt1_name;
				$option.=$addoption[$key];
			} else {
				$option.=(ord($option)==0?"":"-").$row->opt1_name;
			}
		}
		if (ord($row->opt2_name)) $option.=(ord($option)==0?"":"-").$row->opt2_name;
		$option2=$addcode.$option;
		//$productname.=" ^ {$product}-".$quantity.(ord($option2)==0?"":"-".$option2);
        $productname.= " ^ ".(ord($option2)==0?"":$option2);
		//$productname2.="^{$product}-".$quantity.(ord($option2)==0?"":"-".$option2);
        $productname2.="^".(ord($option2)==0?"":$option2);
		$prdt_message=str_replace($pattern,$replacement,strip_tags($row->order_prmsg));
		$prdt_deli_gbn=$row->prdt_deli_gbn;
		$prdt_deli_date=$row->prdt_deli_date;
		$prdt_selfcode=$row->selfcode;
		$prdt_business=$row->productbisiness;
	} else { //같은 주문일경우
		if($excel_ok=="N") {
			$date=$sender_name=$pay_data=$sender_telnum=$sender_tel=$sender_email=$idnum=$paymethod="";
			$pay=$pay2=$sumprice=$deli_gbn=$receiver_name=$receiver_tel=$receiver_tel1num="";
			$receiver_tel2num=$receiver_tel1=$receiver_tel2=$post1=$post2=$addr=$message=$messnotag="";
			$deli_price=$usereserve=$deli_date=$bank_date=$adminmemo=$usermemo=$ordercode="";
		}
		$quantity=$row->quantity;
		$productcode="=\"{$row->productcode}\"";
		$product1=str_replace(",","",$row->productname);
		$product=strip_tags($product1);
		$price=$row->price;
		$reserve=$row->reserve;
		$option=$option2=$addcode="";
		if(ord($row->addcode)) $addcode=$row->addcode;
		if(ord($row->opt1_name)) {
			if(strpos($row->opt1_name,"[OPTG")===0) {
				$key=$row->ordercode.$row->productcode.$row->opt1_name;
				$option.=$addoption[$key];
			} else {
				$option.=(ord($option)==0?"":"-").$row->opt1_name;
			}
		}
		if (ord($row->opt2_name)) $option.=(ord($option)==0?"":"-").$row->opt2_name;
		$option2=$addcode.$option;
		//$productname=$product."-".$quantity.(ord($option2)==0?"":"-".$option2);
        $productname=(ord($option2)==0?"":$option2);
		$productname2=$productname;
		$deli_num=$row->deli_num;
		$prdt_message=str_replace($pattern,$replacement,strip_tags($row->order_prmsg));
		$prdt_deli_gbn=$row->prdt_deli_gbn;
		$prdt_deli_date=$row->prdt_deli_date;
		$prdt_selfcode=$row->selfcode;
		$prdt_business=$row->productbisiness;
	}*/
	if($isproductall=="N") {
		echo "<tr>";
		for($i=0;$i<$cnt;$i++) {
			//if($i!=0){
                echo "<td>";
                echo doubleQuote($excelval[$arr_excel[$i]][1]);
                echo "</td>";
			//}
		}
		echo "</tr>";
	}
}
pmysql_free_result($result);

if($isproductall=="Y"){	
	echo "<tr>";
	for($i=0;$i<$cnt;$i++){
		//if($i!=0){
            echo "<td>";
            echo doubleQuote($excelval[$arr_excel[$i]][1]);
            echo "</td>";
		//}
	}
	echo "</tr>";
}
echo "</table>";
?>
</body>
</html>
<?
function doubleQuote($str) {
	return str_replace('"', '""', $str);
}
