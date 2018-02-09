<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
header("Content-type: appliction/json; charset=utf-8");

$tbltaxcalclistIdx = $_POST['t_idx'];
$_POST = euckrToUtf8($_POST);
$name					= $_POST['name'];//대표자명
$company				= $_POST['company'];//테스트회사
$service				= $_POST['service'];//업태
$item					= $_POST['item'];//종목
$busino					= str_replace("-","",$_POST['busino']);//1298657701
$address				= $_POST['address'];//서울시 삼십분
$productname			= $_POST['productname'];//테스트 상품 외 1건
$price					= $_POST['price'];//3570
$supply					= $_POST['supply'];//3246
$surtax					= $_POST['surtax'];//324
$issuedate				= $_POST['issuedate'];//2015-03-05
$invoicermgtkey			= date("Ymd")."-".$_POST['t_idx'];	
$issuetype				= $_POST['issuetype'] == "" ? "정발행" : $_POST['issuetype'];//
$purposetype			= $_POST['PurposeType'];// 
$taxtype				= $_POST['taxtype'] == "" ? "과세":$_POST['taxtype'];//
$issuetiming			= $_POST['issuetiming']== "" ? "직접발행":$_POST['issuetiming'];//
$invoiceetype			= $_POST['invoiceetype']== "" ? "사업자":$_POST['invoiceetype'];//
$invoiceecontactname1	= $_POST['InvoiceeContactName1'];//담당자명                          
$invoiceeemail1			= $_POST['invoiceeemail1'];//doraemon01@naver.com                                        
$useat_linkhub			= $_POST['useat_linkhub'];//y
$serialnum				= $_POST['serialnum'];// 일련번호
$purchasedt				= $_POST['purchasedt'];// 거래일자
$spec					= $_POST['spec'];	//규격
$qty					= $_POST['qty'];	//수량
$unitcost				= $_POST['unitcost'];	//단가
$supplycost				= $_POST['supplycost'];	//공급가액
$tax					= $_POST['tax'];	//세액
$remark					= $_POST['remark'];	//비고
$chargedirection		= "정과금";

if ($tbltaxcalclistIdx != ""){
	$upQuery = "
		UPDATE tbltaxcalclist
		   SET name='{$name}', 
			   company='{$company}', 
			   service='{$service}', 
			   item='{$item}', 
			   busino='{$busino}', 
			   address='{$address}', 
			   productname='{$productname}', 
			   price='{$price}', 
			   supply='{$supply}', 
			   surtax='{$surtax}', 
			   issuedate='{$issuedate}', 
			   purposetype='{$purposetype}',
			   issuetype='{$issuetype}',
			   chargedirection='{$chargedirection}',
			   issuetiming='{$issuetiming}', 
			   invoiceetype='{$invoiceetype}', 
			   invoiceecontactname1='{$invoiceecontactname1}', 
			   invoiceeemail1='{$invoiceeemail1}', 
			   serialnum='{$serialnum}', 
			   purchasedt='{$purchasedt}', 
			   spec='{$spec}', 
			   qty='{$spec}', 
			   unitcost='{$unitcost}', 
			   supplycost='{$supplycost}', 
			   tax='{$tax}', 
			   remark='{$remark}',
			   invoicermgtkey='{$invoicermgtkey}',
			   taxtype='{$taxtype}'
		 WHERE no = {$tbltaxcalclistIdx};
	";
	//exdebug($upQuery);
	if (pmysql_query($upQuery)){
		//echo "1";
		include "../linkhub/TaxinvoiceExample/common.php";
		$writeSpecification = false;				# 거래명세서 동시작성 여부
		$shopResult = pmysql_fetch_object(pmysql_query("select * from tblshopinfo limit 1"));
		$testUserID				= trim($shopResult->linkhub_id);// duometis
		$linkhub_pwd			= trim($shopResult->linkhub_pwd);//  duometis0413        
		$linkhub_linkid			= trim($shopResult->linkhub_linkid);//  DUO                 
		$testCorpNum			= trim($shopResult->linkhub_corpnum);//  2068610624
		$linkhub_ceoname		= trim($shopResult->linkhub_ceoname);//  정욱                            
		$linkhub_corpname		= trim($shopResult->linkhub_corpname);//  듀오테스터                         
		$linkhub_addr			= trim($shopResult->linkhub_addr);//  안드로메다                                                                                         
		$linkhub_zipcode		= trim($shopResult->linkhub_zipcode);//  112-112   
		$linkhub_biztype		= trim($shopResult->linkhub_biztype);//  업태                                      
		$linkhub_bizclass		= trim($shopResult->linkhub_bizclass);//  업종                                      
		$linkhub_contactname	= trim($shopResult->linkhub_contactname);//  길동이                           
		$linkhub_contactemail	= trim($shopResult->linkhub_contactemail);//  doraemon01@naver.com                                                  
		$linkhub_contacttel		= trim($shopResult->linkhub_contacttel);//  02-0202-0202        
		$linkhub_contacthp		= trim($shopResult->linkhub_contacthp);//  010-2222-3333       
		$linkhub_contactfax		= trim($shopResult->linkhub_contactfax);//  070-0707-0707   
		$taxResult = pmysql_fetch_object(pmysql_query("select * from tbltaxcalclist where no={$tbltaxcalclistIdx}"));
		$no						= trim($taxResult->no);//246
		$ordercode				= trim($taxResult->ordercode);//2015030323102408083A
		$mem_id					= trim($taxResult->mem_id);//tigersoft
		$name					= trim($taxResult->name);//대표자명
		$company				= trim($taxResult->company);//테스트회사
		$service				= trim($taxResult->service);//업태
		$item					= trim($taxResult->item);//종목
		$busino					= trim($taxResult->busino);//1298657701
		$address				= trim($taxResult->address);//서울시 삼십분
		$productname			= trim($taxResult->productname);//테스트 상품 외 1건
		$price					= trim($taxResult->price);//3570
		$supply					= trim($taxResult->supply);//3246
		$surtax					= trim($taxResult->surtax);//324
		$type					= trim($taxResult->type);//0
		$issuedate				= trim($taxResult->issuedate);//2015-03-05
		$date					= trim($taxResult->date);//20150305110317
		$invoicermgtkey			= trim($taxResult->invoicermgtkey);// 
		$issuetype				= trim($taxResult->issuetype);//
		$chargedirection		= trim($taxResult->chargedirection);//
		$purposetype			= trim($taxResult->purposetype) == "" ? "청구" : trim($taxResult->purposetype);// 
		$taxtype				= trim($taxResult->taxtype);//
		$issuetiming			= trim($taxResult->issuetiming);//
		$invoiceetype			= trim($taxResult->invoiceetype);//
		$invoiceecontactname1	= trim($taxResult->invoiceecontactname1);//담당자명                          
		$invoiceeemail1			= trim($taxResult->invoiceeemail1);//doraemon01@naver.com                                        
		$useat_linkhub			= trim($taxResult->useat_linkhub);//y
		$serialnum				= trim($taxResult->serialnum);// 일련번호
		$purchasedt				= trim($taxResult->purchasedt);// 거래일자
		$spec					= trim($taxResult->spec);	//규격
		$qty					= trim($taxResult->qty);	//수량
		$unitcost				= trim($taxResult->unitcost);	//단가
		$supplycost				= trim($taxResult->supplycost);	//공급가액
		$tax					= trim($taxResult->tax);	//세액
		$remark					= trim($taxResult->remark);	//비고
		$invoicerMgtKey			= trim($taxResult->invoicermgtkey);
		try	{
			$result = $TaxinvoiceService->CheckIsMember($testCorpNum,$LinkID); 
			$code = mb_convert_encoding($result->code,"euc-kr","utf-8");
			$message = mb_convert_encoding($result->message,"euc-kr","utf-8");
		} 
		catch(PopbillException $pe) {
			$code = mb_convert_encoding($pe->getCode(),"euc-kr","utf-8");
			$message = mb_convert_encoding($pe->getMessage(),"euc-kr","utf-8");
		}
		if ($code == "1") { // 세금계산서 임시 저장 시작
			$testCorpNum = $shopResult->linkhub_corpnum;				# 회원 사업자번호, '-' 제외 10자리
			$invoicerMgtKey = $taxResult->invoicermgtkey;			# 문서관리번호
			$testUserID = $shopResult->linkhub_id;					# 회원 아이디
			$writeSpecification = false;				# 거래명세서 동시작성 여부
			$Taxinvoice = new Taxinvoice();
			
			$Taxinvoice->writeDate = str_replace("-","",$taxResult->issuedate);		# [필수] 작성일자, 형식(yyyyMMdd) 예)20150101
			$Taxinvoice->issueType = trim(mb_convert_encoding($taxResult->issuetype,"utf-8","euc-kr"));			# [필수] 발행형태, '정발행', '역발행', '위수탁' 중 기재
			$Taxinvoice->chargeDirection = trim(mb_convert_encoding($taxResult->chargedirection,"utf-8","euc-kr"));	# [필수] 과금방향, '정과금'(공급자 과금), '역과금'(공급받는자 과금) 중 기재, 역과금은 역발행시에만 가능.
			$Taxinvoice->purposeType = trim(mb_convert_encoding($taxResult->purposetype,"utf-8","euc-kr"));			# [필수] '영수', '청구' 중 기재
			$Taxinvoice->taxType = trim(mb_convert_encoding($taxResult->taxtype,"utf-8","euc-kr"));				# [필수] '과세', '영세', '면세' 중 기재
			$Taxinvoice->issueTiming = trim(mb_convert_encoding($taxResult->issuetiming,"utf-8","euc-kr"));		# [필수] 발행시점, '직접발행', '승인시자동발행' 중 기재

			$Taxinvoice->invoicerCorpNum = $testCorpNum;				# [필수] 공급자 사업자번호 
			$Taxinvoice->invoicerCorpName = trim(mb_convert_encoding($shopResult->linkhub_corpname,"utf-8","euc-kr"));				# [필수] 공급자 상호
			$Taxinvoice->invoicerMgtKey = $invoicerMgtKey;				# [필수] 공급자 연동관리번호
			$Taxinvoice->invoicerCEOName = trim(mb_convert_encoding($shopResult->linkhub_ceoname,"utf-8","euc-kr"));			# [필수]
			$Taxinvoice->invoicerAddr = $shopResult->linkhub_zipcode." ".trim(mb_convert_encoding($shopResult->linkhub_addr,"utf-8","euc-kr"));					
			$Taxinvoice->invoicerContactName = trim(mb_convert_encoding($shopResult->linkhub_contactname,"utf-8","euc-kr"));			# [필수]
			$Taxinvoice->invoicerEmail = $shopResult->linkhub_contactemail;				
			$Taxinvoice->invoicerTEL = $shopResult->linkhub_contacttel;					
			$Taxinvoice->invoicerHP = $shopResult->linkhub_contacthp;
			$Taxinvoice->invoicerBizClass = trim(mb_convert_encoding($shopResult->linkhub_bizclass,"utf-8","euc-kr"));					
			$Taxinvoice->invoicerBizType = trim(mb_convert_encoding($shopResult->linkhub_biztype,"utf-8","euc-kr"));

			$Taxinvoice->invoicerSMSSendYN = false;						# 발행시 문자전송 여부

			$Taxinvoice->invoiceeType = trim(mb_convert_encoding($taxResult->invoiceetype,"utf-8","euc-kr"));						# [필수] 공급받는자 구분, '사업자', '개인', '외국인' 중 기재
			$Taxinvoice->invoiceeCorpNum = $busino;				# [필수] 공급받는자 구분에 따라 기재
																		# '사업자':사업자번호 10자리('-'제외), '개인':주민등록번호-13자리('-'제외), '외국인' - '9999999999999' (13자리) 기재
			$Taxinvoice->invoiceeCorpName = trim(mb_convert_encoding($taxResult->company,"utf-8","euc-kr"));				# [필수]
			$Taxinvoice->invoiceeMgtKey = '';							# 공급받는자 연동관리번호, [역발행]시 필수 
			$Taxinvoice->invoiceeCEOName = trim(mb_convert_encoding($taxResult->name,"utf-8","euc-kr"));			# [필수]
			$Taxinvoice->invoiceeAddr = trim(mb_convert_encoding($taxResult->address,"utf-8","euc-kr"));	
			$Taxinvoice->invoiceeContactName1 = trim(mb_convert_encoding($taxResult->invoiceecontactname1,"utf-8","euc-kr"));		# [필수]
			$Taxinvoice->invoiceeEmail1 = $taxResult->invoiceeemail1;
			$Taxinvoice->invoiceeTEL1 = '';
			$Taxinvoice->invoiceeHP1 = '';
			$Taxinvoice->invoiceeBizClass = trim(mb_convert_encoding($taxResult->item,"utf-8","euc-kr"));					
			$Taxinvoice->invoiceeBizType = trim(mb_convert_encoding($taxResult->service,"utf-8","euc-kr"));
			$Taxinvoice->invoiceeSMSSendYN = false;						# 발행시 문자전송 여부

			$Taxinvoice->supplyCostTotal = $taxResult->supply;		# [필수] 공급가액 합계
			$Taxinvoice->taxTotal = $taxResult->surtax;				# [필수] 세액 합계
			$Taxinvoice->totalAmount = $taxResult->price;			# [필수] 합계금액, 숫자만가능하며 (-) 기재도 가능

			$Taxinvoice->detailList = array();				

			$Taxinvoice->detailList[] = new TaxinvoiceDetail();
			$Taxinvoice->detailList[0]->serialNum = 1;				# [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재, 
			$Taxinvoice->detailList[0]->purchaseDT = str_replace("-","",$taxResult->purchasedt);  //# 거래일자
			$Taxinvoice->detailList[0]->itemName = trim(mb_convert_encoding($taxResult->productname,"utf-8","euc-kr"));		# 품명
			$Taxinvoice->detailList[0]->spec = $taxResult->spec;				# 규격
			$Taxinvoice->detailList[0]->qty = $taxResult->qty;					# 수량
			$Taxinvoice->detailList[0]->unitCost = $taxResult->unitcost;		# 단가
			$Taxinvoice->detailList[0]->supplyCost = $taxResult->supplycost;		# 공급가액
			$Taxinvoice->detailList[0]->tax = $taxResult->tax;				# 세액
			$Taxinvoice->detailList[0]->remark = $taxResult->remark;		# 비고
			
			try {
				#Register(사업자번호, 세금계산서객체, 회원아이디, 거래명세서 동시작성여부)
				$tax_result = $TaxinvoiceService->Register($testCorpNum, $Taxinvoice, $testUserID, $writeSpecification);
				$tax_code = trim(mb_convert_encoding($tax_result->code,"euc-kr","utf-8"));
				$tax_message = trim(mb_convert_encoding($tax_result->message,"euc-kr","utf-8"));
			}
			catch(PopbillException $tax_pe) {
				//$tax_code = $tax_pe->getCode();
				//$tax_message = $tax_pe->getMessage();
				$tax_code = trim(mb_convert_encoding($tax_pe->getCode(),"euc-kr","utf-8"));
				$tax_message = trim(mb_convert_encoding($tax_pe->getMessage(),"euc-kr","utf-8"));
			}
			//exdebug($tax_code);
			//exdebug($tax_message);
			
			if ($tax_code == "1"){
				$qry = "
					UPDATE tbltaxcalclist
					   SET type = 2
					 WHERE no = {$tbltaxcalclistIdx}
				";
				pmysql_query($qry);
				$arr = array('code' => $tax_code, "message" => mb_convert_encoding(urldecode($tax_message),'utf-8','euc-kr'));
				echo json_encode($arr);
			} else {
				$arr = array('code' => $tax_code, "message" => mb_convert_encoding(urldecode($tax_message),'utf-8','euc-kr'));
				echo json_encode($arr);
			}
		} else { // 미가입 상태
			//exdebug($code);
			//exdebug($message);	
			$arr = array('code' => $code,"message"=>$message);
			echo json_encode($arr);
		}
	} else {
		$arr = array('code' => "0", 'message' => mb_convert_encoding(urldecode("오류"),'utf-8','euc-kr'));
		echo json_encode($arr);
	}
} else {
	$arr = array('code' => "0", 'message' => mb_convert_encoding(urldecode("오류"),'utf-8','euc-kr'));
	echo json_encode($arr);
}
?>