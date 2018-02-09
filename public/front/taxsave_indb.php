<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


function getParseTax($temp) {
	$val = array();
	$list = explode("<br>\n",$temp);
	for ($i=0;$i<count($list); $i++) {
		$data = explode("=",$list[$i]);
		$val[$data[0]] = $data[1];
	}
	return $val;
}

$ordercode=$_POST["ordercode"];
$productname=urldecode($_POST["productname"]);

if(ord($ordercode)==0) {
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}

$tax_cnum="";
$sql = "SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid ";
$sql.= "FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

$tax_cnum=$row->tax_cnum;
$taxsavetype=$row->tax_type;
$tax_rate=$row->tax_rate;

$tax_no=$row->tax_cnum;
$kcp_mid=$row->tax_mid;
$kcp_tid=$row->tax_tid;

$tax_cnum1=substr($tax_cnum,0,3);
$tax_cnum2=substr($tax_cnum,3,2);
$tax_cnum3=substr($tax_cnum,5,5);

if(ord($tax_cnum)==0 || $taxsavetype=="N") {
	alert_go("본 쇼핑몰에서는 현금영수증 발급 기능을 지원하지 않습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",'c');
}


$sql = "SELECT ordercode,tsdtime,tax_no,type,authno,id_info,mtrsno FROM tbltaxsavelist WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$date = date("Ymd",strtotime('-1 day'));
	if($row->type=="Y" && $date."020000">=$row->tsdtime) {
		$query ="cashtype=QURY";
		$query.="&midbykcp=".$kcp_mid;
		$query.="&termid=".$kcp_tid;
		$query.="&cashipaddress1=203.238.36.160";
		$query.="&cashportno1=9981";
		$query.="&cashipaddress2=203.238.36.161";
		$query.="&cashportno2=9981";
		$query.="&tax_no=".$tax_no;

		$id_info=$row->id_info;
		$authno=$row->authno;

		$query.="&tsdtime=".substr($row->tsdtime,2);
		$query.="&id_info=".$row->id_info;
		$query.="&authno=".$row->authno;
		$query.="&mtrsno=".$row->mtrsno;

		//cgi 호출
		$host_url=$_SERVER['HTTP_HOST'];
		$host_cgi="/".RootPath.CashcgiDir."bin/cgiway.cgi";

		$resdata=SendSocketPost($host_url,$host_cgi,$query);
		$_taxdata=getParseTax($resdata);

		if(count($_taxdata)>0 && ord($_taxdata["mrspc"])) {
			if($_taxdata["mrspc"]!="00") {
				$msg="현금영수증 조회가 실패하였습니다.\\n\\n--------------------실패사유--------------------\\n\\n".$_taxdata["resp_msg"];
				echo "<script>alert('{$msg}');window.close();</script>";
				exit;
			}
		} else {
			echo "<script>alert('현금영수증 서버 연결이 실패하였습니다.');window.close();</script>";
			exit;
		}

		include ($Dir."lib/taxsaveview.inc.php");

		exit;
	} else if($row->type=="Y") {
		$msg="이미 현금영수증을 발급하셨습니다.\\n\\n발급된 현금영수증은 발급 후 1일 후에 국체청 홈페이지에서 확인이 가능합니다.";
	} else {
		$msg="이미 현금영수증을 발급요청하셨습니다.";
	}
	if(ord($msg)) {
		alert_go($msg,'mypage_orderlist_view.php?ordercode='.$ordercode);
	}
	exit;
}
pmysql_free_result($result);

$mode=$_POST["mode"];

if($mode=="update") {
	$up_tr_code=$_GET["up_tr_code"];
	$up_id_info=$_GET["up_id_info"];

	$sql = "SELECT price,paymethod,bank_date,sender_name,sender_email,sender_tel,del_gbn FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$up_name=$row->sender_name;
		$up_email=$row->sender_email;
		$up_tel=$row->sender_tel;
		$up_productname=$productname;

		if(strstr("BOQ", $row->paymethod[0]) && $row->deli_gbn!="C") {
			if($tax_rate==10) {
				$up_amt1=$row->price;
				$up_amt4=floor(($up_amt1/1.1)*0.1);
				$up_amt2=$up_amt1-$up_amt4;
				$up_amt3=0;
			} else {
				$up_amt1=$row->price;
				$up_amt2=0;
				$up_amt3=0;
				$up_amt4=0;
			}

			if($up_amt1<1) {
				alert_go('구매금액이 1원 이상 부터 현금영수증 발급이 가능합니다.','c');
			}

			

			$tsdtime=date("YmdHis");
			$sql = "INSERT INTO tbltaxsavelist(
			ordercode		,
			tsdtime			,
			tr_code			,
			tax_no			,
			id_info			,
			name			,
			tel				,
			email			,
			productname		,
			amt1			,
			amt2			,
			amt3			,
			amt4			,
			type			) VALUES (
			'{$ordercode}', 
			'{$tsdtime}', 
			'{$up_tr_code}', 
			'{$tax_cnum}', 
			'{$up_id_info}', 
			'{$up_name}', 
			'{$up_tel}', 
			'{$up_email}', 
			'{$up_productname}', 
			{$up_amt1}, 
			{$up_amt2}, 
			{$up_amt3}, 
			{$up_amt4}, 
			'N')";

			pmysql_query($sql,get_db_conn());

			if(pmysql_error()) {
				alert_go('현금영수증 발급요청이 실패하였습니다.','mypage_orderlist_view.php?ordercode='.$ordercode);
			} else {
				//자동발급
				if($taxsavetype=="Y" && strlen($row->bank_date)==14 && $row->deli_gbn!="C") {
					$flag="Y";
					include($Dir."lib/taxsave.inc.php");
				}
				if(ord($msg)) {
					alert_go($msg,'mypage_orderlist_view.php?ordercode='.$ordercode);
				} else {
					alert_go("현금영수증 개별발급 요청이 완료되었습니다.\\n\\n관리자가 확인 후 발급해드립니다.\\n\\n발급된 현금영수증은 발급 1일 후에 확인이 가능합니다.",'mypage_orderlist_view.php?ordercode='.$ordercode);
				}
			}
			
		}
	}
	pmysql_free_result($result);
	exit;
}
?>