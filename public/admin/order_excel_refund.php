<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

//print_r($_POST);

$oc_no   = $_POST["oc_no"];

$orderby    = $_POST["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$paymethod	= $_POST["paymethod"];
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
Header("Content-Disposition: attachment; filename=order_excel_refund_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$excel_info = "49,0,53,54,35,52,1,5,57,6,21,25,23,56,50,28,27,51,58,59,60,61,62,63,64";
//$excel_info = trim(str_replace("32,","",$_shopdata->excel_info),',');
$excel_ok  = $_shopdata->excel_ok;
//print_r($excel_info);

$excelval=array(
	array("주문일자"									,&$date),				#0
	array("주문자"									,&$sender_name),		#1
	array("주문자 전화(XXXXXXXX)"					,&$sender_telnum),		#2
	array("주문자 전화(XX-XXXX-XXXX)"				,&$sender_tel),			#3
	array("이메일"									,&$sender_email),		#4
	array("주문자ID"							,&$idnum),				#5
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
	array("수량"									,&$quantity),			#23
	array("옵션1 - 옵션2"	                        ,&$productname),		#24
	array("판매가격"								,&$price),				#25
	array("상품 적립금"								,&$reserve),			#26
	array("개별배송비"									,&$deli_price),			#27
	array("적립금"								,&$usereserve),			#28
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
	array("쿠폰"									,&$coupon_price),				#50
	array("상품결제가"									,&$totprice),				#51
	array("브랜드"									,&$brand),				#52
	array("요청일"							,&$rg_date),				#53
	array("사유"							,&$occode),				#54
	array("주문자ID"						,&$sender_id)	,	#55
	array("옵션"						,&$option_price),		#56
	array("취소수량/주문수량"						,&$can_ord_quantity),		#57
	array("환불예정금액(상품결제단가)"						,&$ref_tot_price),		#58
	array("환불은행"						,&$ref_bankcode),		#59
	array("환불계좌"						,&$ref_bankaccount),		#60
	array("예금주"						,&$ref_bankuser),		#61
	array("환불 수수료"						,&$ref_rfee),		#62
	array("최종 환불금액 (실결제금액 - 환불수수료)"						,&$ref_rprice),		#63
	array("취소처리"						,&$pg_cancel)		#64
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

if(ord($oc_no)) $ordercodes="'".str_replace(",","','",$oc_no)."'";
if($oc_no) $ordercodes= str_replace(",''","",$oc_no);

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

	//$qry = "WHERE 1=1 AND toc.pickup_state in ('N','Y') AND toc.restore ='N' AND toc.cfindt ='' ";
	$qry = "WHERE 1=1 AND toc.pickup_state in ('N','Y') AND toc.restore ='N' AND (toc.cfindt ='' OR (LENGTH(toc.bankaccount) < 9 AND toc.pgcancel = 'N' AND a.paymethod IN ('CA'))) ";
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND toc.regdt LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND toc.regdt>='{$search_s}' AND toc.regdt <='{$search_e}' ";
		}
	}

	if(ord($oc_no)) $qry.= " AND b.oc_no IN (".$oc_no.") ";

	if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

	$paymethod_arr = explode("','",$paymethod);

	if(ord($paymethod))	$qry.= "AND a.paymethod in('".$paymethod."')";

	if(ord($search)) {
		if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
		else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	}

	$qry_from  = " tblorder_cancel toc ";
	$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";

	if($vendercnt>0) {
		if($sel_vender || $com_name) {
			if($com_name) $qry .= " and b.brandname like '%".strtoupper($com_name)."%' ";
			else if($sel_vender) $qry .= " and b.vender = ".$sel_vender." ";
		}

		$qry_from .= ", (Select p.vender, p.ordercode, p.productcode, p.productname, p.opt1_name, p.opt2_name, p.quantity, p.price, p.option_price,
					p.deli_com, p.deli_num, p.deli_date, p.deli_price, 
					p.coupon_price, p.use_point, p.op_step, p.opt1_change, p.opt2_change, p.oc_no, p.date, p.idx, p.option_price_text_change, p.option_quantity FROM tblorderproduct p left join tblvenderinfo v on p.vender = v.vender left join tblproductbrand pb on p.vender=pb.vender where p.oc_no > 0  and p.redelivery_type != 'G' and p.op_step in ('41','42')) b ";
		$qry.= "AND toc.oc_no=b.oc_no {$subqry} ";
	} else {
		$qry_from .= ", (Select oc_no from tblorderproduct p where oc_no > 0 and redelivery_type != 'G' and op_step in ('41','42') group by oc_no) b ";
		$qry.= "AND toc.oc_no=b.oc_no ";
	}

	$sql = "SELECT  b.vender, b.ordercode, b.productcode, b.productname, b.opt1_name, b.opt2_name, b.quantity, b.price, b.option_price,
					b.deli_com, b.deli_num, b.deli_date, b.deli_price, 
					b.coupon_price, b.use_point, b.op_step, b.opt1_change, b.opt2_change, b.oc_no, b.date, b.idx, b.option_price_text_change, b.option_quantity, toc.regdt, toc.code, toc.bankcode, toc.bankaccount, toc.bankuser, toc.rfee, toc.pgcancel,
					a.id, a.sender_name, a.paymethod, a.oi_step1, a.oi_step2 
			FROM {$qry_from} {$qry} 
			ORDER BY  toc.oc_no {$orderby} 
		";

	$result = pmysql_query($sql,get_db_conn());
echo $sql;
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
		$totprice=(($row->price + $row->option_price)*$row->option_quantity)-$row->coupon_price-$row->use_point+$row->deli_price;
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
		$rg_date=substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";
		$occode=$oc_code[$row->code];
		$option_price = $row->option_price*$row->option_quantity;
				
		list($oc_count)=pmysql_fetch("SELECT count(*) as oc_count from tblorderproduct WHERE oc_no='{$row->oc_no}' ");			
		list($tot_count)=pmysql_fetch("SELECT count(*) as tot_count from tblorderproduct WHERE ordercode = '{$row->ordercode}' ");	
		$can_ord_quantity = $oc_count."/".$tot_count;
		list($main_order_pridx)=pmysql_fetch("SELECT idx from tblorderproduct WHERE oc_no='{$row->oc_no}' GROUP BY idx, productcode, vender, productname");	
		list($main_sum_totprice)=pmysql_fetch("SELECT SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_totprice from tblorderproduct WHERE oc_no='{$row->oc_no}' GROUP BY oc_no");	
		$rowspan = "";
		if ($oc_count > 1) {
			if ($row->idx == $main_order_pridx) {
				$rowspan= $oc_count;
			} else {
				$rowspan= 0;
			}
		}
		if ($rowspan > 1 || $rowspan == "") {
			$ref_tot_price = $main_sum_totprice;
			$ref_bankcode = $oc_bankcode[$row->bankcode];
			$ref_bankaccount = $row->bankaccount;
			$ref_bankuser = $row->bankuser;
			$ref_rfee = $row->rfee;
			if ($row->rprice > 0) {
				$ref_rprice= $row->rprice;
			} else {
				$ref_rprice= $main_sum_totprice;
			}

			if ($row->paymethod[0] == 'C') {		// 카드결제일 경우
				if($oc_count == $tot_count) {			// 전체취소시
					if ($row->pgcancel == 'N') {

						$pg_cancel	= "-";
								
					} else if ($row->pgcancel == 'Y') {
						$pg_cancel	= "전체취소 완료";
					}
				} else {										// 부분취소시
					if ($row->pgcancel == 'N') {

						$pg_cancel	= "-";

					} else if ($row->pgcancel == 'Y') {
						$pg_cancel	= "부분취소 완료";
					}
				}
			} else { // 그외
				$pg_cancel	= "카드결제건이 아닙니다.";
			}
		}

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
			if ($arr_excel[$i] =='58' || $arr_excel[$i] =='59' || $arr_excel[$i] =='60' || $arr_excel[$i] =='61' || $arr_excel[$i] =='62' || $arr_excel[$i] =='63' || $arr_excel[$i] =='64') {
				if ($rowspan > 0 || $rowspan == "") {
					echo "<td rowspan='".$rowspan."'>";
					echo doubleQuote($excelval[$arr_excel[$i]][1]);
					echo "</td>";
				}
			} else {
                echo "<td>";
                echo doubleQuote($excelval[$arr_excel[$i]][1]);
                echo "</td>";
			}
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
