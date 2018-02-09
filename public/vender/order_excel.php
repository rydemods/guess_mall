<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$ordercodes=rtrim($_POST["ordercodes"],',');
$paystate=$_POST["paystate"];
$oi_type=$_POST["oi_type"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];

$orderby=$_POST["orderby"];
if(strlen($orderby)==0) $orderby="DESC";

$CurrentTime = time();

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>31) {
	echo "<script>alert('주문서 EXCEL 다운로드 기간은 1달을 초과할 수 없습니다.');</script>";
	exit;
}

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=order_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

if(strlen($ordercodes)>0) $ordercodes="'".str_replace(",","','",$ordercodes)."'";

$sql = "SELECT * FROM tblorderoption ";
if(strlen($ordercodes)>0) 
	$sql.= "WHERE ordercode IN (".$ordercodes.") ";
else
	$sql.= "WHERE ordercode >= '".$search_s."' AND ordercode <= '".$search_e."' ";
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)) {
	$optionkey=$row->ordercode.$row->productcode.$row->opt_idx;
	$addoption[$optionkey]=$row->opt_name;
}
pmysql_free_result($result);


// 기본 검색 조건
$qry_from = "tblorderinfo a ";
$qry_from.= " join tblorderproduct b on a.ordercode = b.ordercode ";
$qry.= "WHERE 1=1 ";
$qry.= "AND b.vender='".$_VenderInfo->getVidx()."' ";


// 기간선택 조건
if(strlen($ordercodes)>0) {
	$qry.= "AND a.ordercode IN (".$ordercodes.") ";
} else {
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND a.ordercode >='{$search_s}' AND a.ordercode <='{$search_e}' ";
		}
	}
}

// 결제상태 조건
if(ord($paystate)) {
    if($paystate == "N") $qry.="AND a.oi_step1 < 1 ";
    else if($paystate == "Y") $qry.="AND a.oi_step1 > 0 ";
}

// 주문상태별 조건
if(ord($oi_type)) {
	if ($oi_type == 44) {
		$qry .= "AND (a.oi_step1 = 0 And a.oi_step2 = 44) ";    //입금전취소완료
	} else if ($oi_type == 61) {
		$qry .= "AND (b.redelivery_type = 'G' And b.op_step = 41) ";   //교환접수
	} else if ($oi_type == 62) {
		$qry .= "AND (b.redelivery_type = 'G' And b.op_step = 44) ";   //교환완료
	} else if ($oi_type == 63) {
		$qry .= "AND (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 41) ";    //반품접수
	} else if ($oi_type == 64) {
		$qry .= "AND (a.oi_step1 in (3,4) And a.oi_step2 = 42) ";   //반품완료(배송중 이상이면서 환불접수단계)
	} else if ($oi_type == 65) {
		$qry .= "AND (a.bank_date is not null And ((a.oi_step1 in (1,2) and b.op_step = 41) OR b.op_step = 42) And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '')))";  //환불접수
	} else if ($oi_type == 66) {
		$qry .= "AND (a.oi_step1 > 0 And b.op_step = 44 And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = ''))) ";  //환불완료
	} else {
		 $qry .= "AND (a.oi_step1 in (".$oi_type.") And a.oi_step2 = 0) ";
	}
}

// 검색어
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="pn") $qry.= "AND b.productname LIKE '".$search."%' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id='".$search."X' ";
}

$sql = "SELECT a.ordercode,a.id,a.paymethod, ";
$sql.= "a.pay_data,a.bank_date,a.pay_flag,a.pay_admin_proc, ";
$sql.= "a.sender_name,a.sender_email,a.sender_tel,a.receiver_name,a.receiver_tel1,a.receiver_tel2, ";
$sql.= "a.receiver_addr,a.order_msg,a.del_gbn, b.deli_gbn as prdt_deli_gbn,b.deli_date as prdt_deli_date, ";
$sql.= "b.deli_num,b.productcode,b.productname,b.opt1_name,b.opt2_name, ";
$sql.= "b.addcode,b.quantity,b.price,b.option_price,b.option_quantity,b.reserve,b.deli_price,b.coupon_price,b.use_point,b.date,b.order_prmsg, b.op_step ";
$sql.= "FROM {$qry_from} {$qry} ";
$sql.= "ORDER BY a.ordercode {$orderby}, b.vender, b.idx ";
$result = pmysql_query($sql,get_db_conn());
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
		<th>일자</th>
		<th>주문자</th>
		<th>주문자 전화(XXX-XXXX-XXXX)</th>
		<th>이메일</th>
		<th>주문ID/주문번호</th>
		<th>결제상태</th>
		<th>받는사람</th>
		<th>전화번호(XX-XXXX-XXXX)</th>
		<th>휴대전화(XXX-XXXX-XXXX)</th>
		<th>우편번호(XXX-XXX)</th>
		<th>주소</th>
		<th>전달사항</th>
		<th>상품명</th>
		<th>옵션(특징포함)</th>
		<th>갯수</th>
		<th>판매금액</th>
		<th>입금일</th>
		<th>주문관련메모(관리자)</th>
		<th>고객알리미</th>
		<th>상품별 송장번호</th>
		<th>거래번호</th>
		<th>상품코드</th>
		<th>옵션</th>
		<th>특징</th>
		<th>상품별 처리여부</th>
		<th>상품별 주문메세지</th>
		<th>상품별 배송일</th>
    </tr>
<?

while ($row=pmysql_fetch_object($result)) {
	$ordercode=$row->ordercode;
	$temp=$row->ordercode;
	$date = substr($row->ordercode, 0, 12);
	$date = substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2);   //날짜 형식 수정  
	$orderdate = str_replace("/","-",$date)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2).")";
	$sender_name=$row->sender_name;
	$pay_data=$row->pay_data;
	$sender_email=$row->sender_email;

	if($row->ordercode[20]=="X") {	//비회원
		$idnum = substr($row->id,1,6);
	} else {	//회원
		$idnum = $row->id;
	}

	if(strstr("B", $row->paymethod[0])) {	//무통장
		if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") $pay="환불";
		else if (strlen($row->bank_date)>0) $pay="입금완료";
		else $pay="미입금";
	} else if(strstr("V", $row->paymethod[0])) {	//계좌이체
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		else if ($row->pay_flag=="0000") $pay="결제완료";
	} else if(strstr("M", $row->paymethod[0])) {	//핸드폰
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
		else if ($row->pay_flag=="0000") $pay="결제완료";
	} else if(strstr("OQ", $row->paymethod[0])) {	//가상계좌
		if (strcmp($row->pay_flag,"0000")!=0) $pay="주문실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		else if ($row->pay_flag=="0000" && strlen($row->bank_date)==0) $pay="미입금";
		else if ($row->pay_flag=="0000" && strlen($row->bank_date)>0) $pay="입금완료";
	} else {
		if (strcmp($row->pay_flag,"0000")!=0) $pay="카드실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") $pay="카드승인";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") $pay="결제완료";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
	}

	/*switch($row->prdt_deli_gbn) {
		case 'S': $prdt_deli_gbn="발송준비";  break;
		case 'X': $prdt_deli_gbn="배송요청";  break;
		case 'Y': $prdt_deli_gbn="배송";  break;
		case 'D': $prdt_deli_gbn="취소요청";  break;
		case 'N': $prdt_deli_gbn="미처리";  break;
		case 'E': $prdt_deli_gbn="환불대기";  break;
		case 'C': $prdt_deli_gbn="주문취소";  break;
		case 'R': $prdt_deli_gbn="반송";  break;
		case 'H': $prdt_deli_gbn="배송(정산보류)";  break;
	}*/
	$prdt_deli_gbn=$op_step[$row->op_step];
	$sender_telnum=check_num($row->sender_tel);
	$receiver_tel1num=check_num($row->receiver_tel1);
	$receiver_tel2num=check_num($row->receiver_tel2);
	$receiver_tel = $receiver_tel1num." ".$receiver_tel2num;
	$sender_tel = replace_tel($sender_telnum);
	$receiver_tel1 = replace_tel($receiver_tel1num);
	$receiver_tel2 = replace_tel($receiver_tel2num);
	$sender_telnum="=\"".$sender_telnum."\""; 
	$receiver_tel1num="=\"".$receiver_tel1num."\""; 
	$receiver_tel2num="=\"".$receiver_tel2num."\""; 
	$receiver_name=$row->receiver_name;
	$bank_date="=\"".($row->paymethod=="B"?$row->bank_date:substr($row->ordercode,0,14))."\"";

	$row->receiver_addr=str_replace("\r\n","",$row->receiver_addr);
	$row->receiver_addr=str_replace("\n","",$row->receiver_addr);
	$row->receiver_addr = str_replace("↑=↑"," ",$row->receiver_addr);
	$row->receiver_addr = str_replace("우편번호 : "," ",$row->receiver_addr);
	$receiver_addr=explode("주소 : ",$row->receiver_addr);
	$post2 = $receiver_addr[0];
	$addr = $receiver_addr[1]; 

	$mess=explode("[MEMO]",$row->order_msg);
	$message=str_replace($pattern,$replacement,strip_tags($mess[0]));
	$messnotag=str_replace($pattern,$replacement,$mess[0]);
	$adminmemo=str_replace($pattern,$replacement,$mess[1]);
	$usermemo=str_replace($pattern,$replacement,$mess[2]);
	$quantity=$row->option_quantity;
	$product1=str_replace(",","",$row->productname);
	$product=strip_tags($product1);
	//$productcode="=\"".$row->productcode."\"";
    $productcode=$row->productcode;
	$price=(($row->price+$row->option_price)*$row->option_quantity)+$row->deli_price;
	$reserve=$row->reserve;
	$option=$option2=$addcode="";
	if (strlen($row->addcode)>0) $addcode=$row->addcode;	

	# 상품 옵션 정보 저장 및 출력

	$opt_name	= "";
	if( strlen( trim( $row->opt1_name ) ) > 0 ) {
		$opt1_name_arr	= explode("@#", $row->opt1_name);
		$opt2_name_arr	= explode(chr(30), $row->opt2_name);
		$s_cnt	= 0;
		for($s=0;$s < sizeof($opt1_name_arr);$s++) {
			if ($opt2_name_arr[$s]) {
				if ($s_cnt > 0) $opt_name	.= " / ";
				$opt_name	.= $opt1_name_arr[$s].' : '.$opt2_name_arr[$s];
				$s_cnt++;
			}
		}
		//echo "<br>".$opt_name;
	}
	
	if( strlen( trim( $row->text_opt_subject ) ) > 0 ) {
		$text_opt_subject_arr	= explode("@#", $row->text_opt_subject);
		$text_opt_content_arr	= explode("@#", $row->text_opt_content);

		for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
			if ($text_opt_content_arr[$s]) {
				if ($opt_name != '') $opt_name	.= " / ";
				$opt_name	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
			}
		}
	}
	
	if (($row->option_price * $row->option_quantity) > 0 ) $opt_name .= " (+".$row->option_price * $row->option_quantity."원)";

	$option = $opt_name;

	$option2=$addcode.$option;
	$productname=$product."-".$quantity.(strlen($option2)==0?"":"-".$option2);
	$productname2=$productname;
	$deli_num=$row->deli_num;
	$prdt_message=str_replace($pattern,$replacement,strip_tags($row->order_prmsg));
	$prdt_deli_date="=\"".$row->prdt_deli_date."\"";
?>
    <tr>	
		<td align="center"><?=$date?></td>
		<td><?=$sender_name?></td>
		<td><?=$sender_tel?></td>
		<td><?=$sender_email?></td>
		<td><?=$idnum?></td>
		<td><?=$pay?></td>
		<td><?=$receiver_name?></td>
		<td><?=$receiver_tel1?></td>
		<td><?=$receiver_tel2?></td>
		<td style="mso-number-format:\@"><?=$post2?></td>
		<td><?=$addr?></td>
		<td><?=$message?></td>
		<td><?=$product?></td>
		<td><?=$option2?></td>
		<td><?=$quantity?></td>
		<td><?=$price?></td>
		<td><?=$bank_date?></td>
		<td><?=$adminmemo?></td>
		<td><?=$usermemo?></td>
		<td><?=$deli_num?></td>
		<td><?=$ordercode?></td>
		<td style="mso-number-format:\@"><?=$productcode?></td>
		<td><?=$option?></td>
		<td><?=$addcode?></td>
		<td><?=$prdt_deli_gbn?></td>
		<td><?=$prdt_message?></td>
		<td><?=$prdt_deli_date?></td>
    </tr>
<?
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);

function doubleQuote($str) {
	return str_replace('"', '""', $str);
}
?>