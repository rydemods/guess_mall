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
$staff_order         = $_POST["staff_order"]?$_POST["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$sel_op_step        = 2;
$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

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

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=order_excel_delivery_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//$excel_info = "49,0,35,1,11,52,21,6,23,51,10";
$excel_info = "35,29,21,36,38,53,6,25,23,11,18,19,15,16,54,1,55,57,58,56,3,59,34";
//$excel_info = trim(str_replace("32,","",$_shopdata->excel_info),',');
$excel_ok  = $_shopdata->excel_ok;
//print_r($excel_info);

$excelval=array(
	array("일자"									,&$date),				#0
	array("주문자"									,&$sender_name),		#1
	array("주문자 전화(XXXXXXXX)"					,&$sender_telnum),		#2
	array("주문자핸드폰"            				,&$sender_tel),			#3
	array("이메일"									,&$sender_email),		#4
	array("주문ID/주문번호"							,&$idnum),				#5
	array("결제수단"								,&$paymethod),			#6
	array("결제상태"								,&$pay),				#7
	array("결제방법(상태)"							,&$pay2),				#8
	array("주문금액(배송비제외)"					,&$sumprice),			#9
	array("처리단계"								,&$deli_gbn),			#10
	array("수령자"								    ,&$receiver_name),		#11
	array("전화번호 비상전화"						,&$receiver_tel),		#12
	array("전화번호(XXXXXXXX)"						,&$receiver_tel1num),	#13
	array("비상전화(XXXXXXXX)"						,&$receiver_tel2num),	#14
	array("전화번호"					            ,&$receiver_tel1),		#15
	array("비상전화"				            	,&$receiver_tel2),		#16
	array("우편번호(XXXXXX)"						,&$post1),				#17
	array("우편번호"        						,&$post2),				#18
	array("주소"									,&$addr),				#19
	array("전달사항"								,&$message),			#20
	array("상품명"									,&$product),			#21
	array("옵션(특징포함)"							,&$option2),			#22
	array("수량"									,&$quantity),			#23
	array("옵션1 - 옵션2"	                        ,&$productname),		#24
	array("판매가"								    ,&$price),				#25
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
	array("자체품목코드"							,&$opt_code),			#53
    array("비고"							        ,&$ord_msg2),			#54
    array("주문자ID"							    ,&$id),			        #55
    array("주문자전화번호"          				,&$sender_tel2),		#56
    array("주문자우편번호"          				,&$sender_post),		#57
    array("주문자주소"          	    			,&$sender_addr),		#58
    array("배송업체명"              				,&$deli_company)		#59
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
			$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
		}
	}

	if(ord($ordercodes)) $qry.= " AND a.idx IN (".$ordercodes.") ";

	// 기본옵션만 검색 (2016-03-08 김재수 막음 - 추가옵션도 있어서..)
	//$qry.= "AND a.option_type = 0 ";

	// 검색어
	if(ord($search)) {
		if($s_check=="oc") $qry.= "AND a.ordercode = '{$search}' ";
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

	// 주문구분 조건 (2016.05.11 - 김재수)
	if(ord($staff_order))	{
		if($staff_order != "A") $qry.= "AND b.staff_order = '{$staff_order}' ";
	}

	// 주문상태별 조건
	$qry.= "AND a.op_step = ".$sel_op_step ;

	// 브랜드 조건
	if($sel_vender || $com_name) {
		if($com_name) $subqry = " and v.com_name like '%".strtoupper($com_name)."%'";
		else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

		$qry_from.= " join tblvenderinfo v on a.vender = v.vender ".$subqry."";
	} else {
		$qry_from.= " join tblvenderinfo v on a.vender = v.vender ";
	}

    $qry_from.= " left join tblmember m on b.id = m.id ";

	$sql = "SELECT  a.vender, v.com_name, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, 
                    a.text_opt_subject, a.text_opt_content, 
                    a.quantity, a.price, a.option_price, a.option_quantity, 
                    a.deli_com, a.deli_num, a.deli_date, 
                    a.deli_price, a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, b.id, 
                    b.order_msg2, b.bank_date, 
                    b.sender_name, b.sender_tel, b.sender_tel2, m.home_post, m.home_addr, 
                    b.receiver_name, b.receiver_tel1, b.receiver_tel2, b.receiver_addr, 
                    b.paymethod, b.oi_step1, b.oi_step2 
			FROM {$qry_from} {$qry} 
			ORDER BY a.ordercode {$orderby}, a.vender DESC 
			";

$result = pmysql_query($sql,get_db_conn());
//echo $sql;

function option_slice2( $content, $option_type = '0' ){
    $tmp_content = '';
    if( $option_type == '0' ) {
        $tmp_content = explode( chr(30), $content );
    } else {
        $tmp_content = explode( '@#', $content );
    }
    
    return $tmp_content;
}
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
		$sumprice=$row->sumprice - $row->coupon_price - $row->use_point;
		$totprice=(($row->price+$row->option_price)*$row->option_quantity)-$row->coupon_price-$row->use_point+$row->deli_price;
		$deli_price=$row->deli_price;
		$coupon_price=$row->coupon_price;
		$usereserve=$row->use_point;

		$deli_gbn=$op_step[$row->op_step];
		$sender_telnum=check_num($row->sender_tel);
		$receiver_tel1num=check_num($row->receiver_tel1);
		$receiver_tel2num=check_num($row->receiver_tel2);
		$receiver_tel = $receiver_tel1num." ".$receiver_tel2num;
		$sender_tel = replace_tel($sender_telnum);
        $sender_tel2 = $row->sender_tel2;
        $sender_post = "'".$row->home_post."'";
        $sender_addr = str_replace("↑=↑"," ",$row->home_addr);
		$receiver_tel1 = $row->receiver_tel1;
		$receiver_tel2 = $row->receiver_tel2;
        //$sender_telnum="=\"{$sender_telnum}\""; 
        //$receiver_tel1num="=\"{$receiver_tel1num}\""; 
        //$receiver_tel2num="=\"{$receiver_tel2num}\""; 
		$sender_telnum="{$sender_telnum}"; 
		$receiver_tel1num="{$receiver_tel1num}"; 
		$receiver_tel2num="{$receiver_tel2num}"; 
		$receiver_name=$row->receiver_name;
		//$bank_date="=\"".($row->paymethod=="B"?$row->bank_date:substr($row->ordercode,0,14))."\"";
        $bank_date = substr($row->bank_date, 0, 8);
		$deli_date="=\"{$row->deli_date}\"";
		$row->receiver_addr=str_replace("\r\n","",$row->receiver_addr);
		$row->receiver_addr=str_replace("\n","",$row->receiver_addr);
		$row->receiver_addr=str_replace("↑=↑"," ",$row->receiver_addr);
		$receiver_addr=explode("주소 : ",$row->receiver_addr);
		$post1 = substr($receiver_addr[0],11,3).substr($receiver_addr[0],15,3);
		$post2 = "'".str_replace("우편번호 : ","",$receiver_addr[0])."'";
		$addr = $receiver_addr[1]; 
        $ord_msg2 = $row->order_msg2;
		$mess=explode("[MEMO]",$row->order_msg);
		$message=str_replace($pattern,$replacement,strip_tags($mess[0]));
		$messnotag=str_replace($pattern,$replacement,$mess[0]);
		$adminmemo=str_replace($pattern,$replacement,$mess[1]);
		$usermemo=str_replace($pattern,$replacement,$mess[2]);
		$quantity=$row->quantity;

		$brand	= $venderlist[$row->vender]->brandname;

		$product1=str_replace(",","",$row->productname);

		$product = "'".$row->productname."'";

		//$product=strip_tags($product1);
		$productcode = "'".$row->productcode."'";
		$price=$row->price;
		$reserve=$row->reserve;
		$option=$option2=$addcode="";
        /*
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
        */
        $text_opt_subject = $row->text_opt_subject;
        $text_opt_content = $row->text_opt_content;
        $idx = $row->idx;
        $tmp_opt1 = explode("@#", $row->opt1_name);
        $tmp_opt2 = option_slice2( $row->opt2_name, '0' );

        $options = array();
        if($row->opt1_name) {
            for($i=0; $i < count($tmp_opt1); $i++) {
                $options[$idx] .= $tmp_opt1[$i]." : ".$tmp_opt2[$i]." / ";
            }
        } else {
            $options[$idx] = "-";
        }
        //print_r($options);
        $add_opt = '';
        if($text_opt_content) {
            $tmp_subject = option_slice2(  $text_opt_subject, '1' );
            $tmp_content = option_slice2(  $text_opt_content, '1' );
            for($i=0; $i < count($tmp_subject); $i++) {
                $add_opt .= $tmp_subject[$i]." : ".$tmp_content[$i]." / ";
            }
        }
        $option = $options[$idx]." ".$add_opt;
        $id = $row->id;

		$productname2=$productname;
		$deli_num=$row->deli_num;
		$prdt_message=str_replace($pattern,$replacement,strip_tags($row->order_prmsg));
		//$prdt_deli_gbn=$row->prdt_deli_gbn;
        $prdt_deli_gbn=$deli_gbn;
		$prdt_deli_date=$row->prdt_deli_date;
		$prdt_selfcode=$row->selfcode;
		$prdt_business=$row->productbisiness;

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
