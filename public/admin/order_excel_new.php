<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

//print_r($_POST);

$ordercodes=rtrim($_POST["ordercodes"],',');
$paymethod=$_POST["paymethod"];
$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];	//배송/입금일별 주문조회(처리기준)
$search=$_POST["search"];
$sel_vender = $_POST["sel_vender"];
$com_name = $_POST["com_name"];  // 벤더이름 검색
$referer1 = $_POST["referer1"];

$CurrentTime = time();

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('주문서 EXCEL 다운로드 기간은 1년을 초과할 수 없습니다.');
}

if(($s_check=="bank_date" || $s_check=="deli_date") && ord($ordercodes)==0) {
	if ($termday>31) {
		alert_go('배송/입금별 주문서 EXCEL 다운로드 기간은 1달을 초과할 수 없습니다.');
	}
}

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=order_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//$excel_info = "35,1,4,21,24,11,19,15,16,27,43,44";
$excel_info = trim(str_replace("32,","",$_shopdata->excel_info),',');
$excel_ok  = $_shopdata->excel_ok;
//print_r($excel_info);

$excelval=array(
	array("일자"									,&$date),				#0
	array("주문자"									,&$sender_name),		#1
	array("주문자 전화(XXXXXXXX)"					,&$sender_telnum),		#2
	array("주문자 전화(XX-XXXX-XXXX)"				,&$sender_tel),			#3
	array("이메일"									,&$sender_email),		#4
	array("주문ID/주문번호"							,&$idnum),				#5
	array("결제방법"								,&$paymethod),			#6
	array("결제상태"								,&$pay),				#7
	array("결제방법(상태)"							,&$pay2),				#8
	array("주문금액(배송비제외)"								,&$sumprice),			#9
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
	array("갯수"									,&$quantity),			#23
	array("옵션1 - 옵션2"	                        ,&$productname),		#24
	array("상품가격"								,&$price),				#25
	array("상품 적립금"								,&$reserve),			#26
	array("배송료"									,&$deli_price),			#27
	array("사용적립금"								,&$usereserve),			#28
	array("입금일"									,&$bank_date),			#29
	array("배송일"									,&$deli_date),			#30
	array("주문관련메모(관리자)"					,&$adminmemo),			#31
	array("고객알리미"								,&$usermemo),			#32
	array("상품명1-갯수-옵션^상품명2-갯수-옵션"		,&$productname2),		#33
	array("상품별 송장번호"							,&$deli_num),			#34
	array("거래번호"								,&$ordercode),			#35
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
	array("번호"									,&$vnum)				#49
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

if($isproduct=="Y" || $isproductall=="Y") {
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

	$sql = "SELECT a.ordercode,a.id,a.price as sumprice,a.reserve as usereserve,a.deli_price, a.dc_price,a.paymethod, ";
	$sql.= "a.pay_data,a.bank_date,a.pay_flag,a.pay_admin_proc,a.deli_gbn,a.deli_date, ";
	$sql.= "a.sender_name,a.sender_email,a.sender_tel,a.receiver_name,a.receiver_tel1,a.receiver_tel2, ";
	$sql.= "a.receiver_addr,a.order_msg,a.del_gbn";
	$sql.= ", b.deli_gbn as prdt_deli_gbn,b.deli_date as prdt_deli_date, ";
	$sql.= "b.deli_num,b.productcode,b.productname,b.opt1_name,b.opt2_name, ";
	$sql.= "b.addcode,b.quantity,b.price,b.reserve,b.date,b.order_prmsg,b.selfcode,b.productbisiness ";
	$sql.= "FROM tblorderinfo a ";
	$sql.= "JOIN tblorderproduct b ON b.ordercode=a.ordercode ";
    $sql.= "JOIN tblmember c on a.id = c.id ";
    $sql.= "JOIN tblaffiliatesinfo d on c.mb_referrer2 = d.idx::varchar ";

    if($sel_vender || $com_name) {
        if($com_name) $subqry = " and v.com_name like '%".strtoupper($com_name)."%' ";
        else if($sel_vender) $subqry = " and v.vender = ".$sel_vender." ";

        $sql.= " join tblvenderinfo v on b.vender = v.vender ".$subqry." ";
    }

    if(ord($ordercodes))
		$sql.= "WHERE a.{$tablecode} IN ({$ordercodes}) ";
	else
		$sql.= "WHERE a.{$tablecode} >= '{$search_s}' AND a.{$tablecode} <= '{$search_e}' ";

	if (ord($deli_gbn)) $sql.= "AND a.deli_gbn = '{$deli_gbn}' ";

	if($paystate=="Y") {		//입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(a.bank_date)=14 ";	//무통장/가상계좌/실시간
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc!='C' AND a.pay_flag='0000' ";	//신용카드/핸드폰
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
	} elseif($paystate=="B") {	//미입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND (a.bank_date IS NULL OR a.bank_date='') ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc='C' AND a.pay_flag!='0000' ";
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
	} elseif($paystate=="C") {	//환불
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(a.bank_date)=9 ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc='C' AND a.pay_flag='0000' ";
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
	}

	if (ord($paymethod)) $sql.= "AND a.paymethod LIKE '{$paymethod}%' ";

	if(ord($search)) {
		if($s_check=="cd") $sql.= "AND a.ordercode like '{$search}%' ";
		else if($s_check=="mn") $sql.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $sql.= "AND a.id='{$search}' ";
		else if($s_check=="cn") $sql.= "AND a.id LIKE 'X{$search}%' ";
        else if($s_check=="st") $sql.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        else if($s_check=="rn") $sql.= "AND a.receiver_name = '{$search}' ";
	}

    if($referer1) {
        $sql.= " AND d.idx = {$referer1} ";
    }

	//$sql.= "AND NOT (b.productcode LIKE '999%' OR b.productcode LIKE 'COU%') ";
	$sql.= "ORDER BY a.ordercode desc, b.productcode DESC ";
} else {
	if(ord($ordercodes)) $tablecode="ordercode";
	if(ord($tablecode)==0) $tablecode="ordercode";
	$sql = "SELECT *,price as sumprice,reserve as usereserve FROM tblorderinfo ";

	if(ord($ordercodes))
		$sql.= "WHERE {$tablecode} IN ({$ordercodes}) ";
	else 
		$sql.= "WHERE {$tablecode} >= '{$search_s}' AND {$tablecode} <= '{$search_e}' ";

	if (ord($deli_gbn)) $sql.= "AND deli_gbn = '{$deli_gbn}' ";

	if($paystate=="Y") {		//입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(bank_date)=14 ";	//무통장/가상계좌/실시간
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc!='C' AND pay_flag='0000' ";	//신용카드/핸드폰
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_admin_proc!='C' AND pay_flag='0000')) ";
	} elseif($paystate=="B") {	//미입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND (bank_date IS NULL OR bank_date='') ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc='C' AND pay_flag!='0000' ";
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag!='0000' AND pay_admin_proc='C')) ";
	} elseif($paystate=="C") {	//환불
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(bank_date)=9 ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc='C' AND pay_flag='0000' ";
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=9) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag='0000' AND pay_admin_proc='C')) ";
	}

	if (ord($paymethod)) $sql.= "AND paymethod LIKE '{$paymethod}%' ";

	if(ord($search)) {
		if($s_check=="cd") $sql.= "AND a.ordercode like '{$search}%' ";
		else if($s_check=="mn") $sql.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $sql.= "AND a.id='{$search}' ";
		else if($s_check=="cn") $sql.= "AND a.id LIKE 'X{$search}%' ";
        else if($s_check=="st") $sql.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        else if($s_check=="rn") $sql.= "AND a.receiver_name = '{$search}' ";
	}

	$sql.= "ORDER BY ordercode DESC";
}

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
		$date = substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2);   //날짜 형식 수정  
		$orderdate = str_replace("/","-",$date)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2).")";
		$sender_name=$row->sender_name;
		$pay_data=$row->pay_data;
		$sender_email=$row->sender_email;

		if(substr($row->ordercode,20)=="X") {	//비회원
			$idnum = substr($row->id,1,6);
		} else {	//회원
			$idnum = $row->id;
		}

		if(strstr("B", $row->paymethod[0])) {	//무통장
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
		$sumprice=$row->sumprice - $row->dc_price - $row->usereserve;
		$deli_price=$row->deli_price;
		$usereserve=$row->usereserve;
		switch($row->prdt_deli_gbn) {
			case 'S': $deli_gbn="배송준비";  break;
			case 'X': $deli_gbn="배송요청";  break;
			case 'Y': $deli_gbn="배송중";  break;
			case 'D': $deli_gbn="취소요청";  break;
			case 'N': $deli_gbn="주문접수";  break;
			case 'E': $deli_gbn="환불대기";  break;
			case 'C': $deli_gbn="주문취소";  break;
			case 'R': $deli_gbn="반송";  break;
			case 'H': $deli_gbn="배송(정산보류)";  break;
		}
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
		$product1=str_replace(",","",$row->productname);
		$product=strip_tags($product1);
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
