<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
header("Content-type: text/html; charset=utf-8");
	
$ordercode = $_POST['ordercode']; 
$tbltaxcalclistIdx = $_POST['t_idx'];  
//$ordercode = "2015030323102408083A";  
//$tbltaxcalclistIdx = '246'; 

$shopResult = pmysql_fetch_object(pmysql_query("select * from tblshopinfo limit 1"));
$linkhub_id				= trim($shopResult->linkhub_id);// duometis
$linkhub_pwd			= trim($shopResult->linkhub_pwd);//  duometis0413        
$linkhub_linkid			= trim($shopResult->linkhub_linkid);//  DUO                 
$linkhub_corpnum		= trim($shopResult->linkhub_corpnum);//  2068610624
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

//exdebug(trim($shopResult);
$taxResult = pmysql_fetch_object(pmysql_query("select * from tbltaxcalclist where ordercode='{$ordercode}' and no={$tbltaxcalclistIdx}"));
//$taxResult = pmysql_fetch_object(pmysql_query("select * from tbltaxcalclist where ordercode='2015030323102408083A' and no=246"));
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

?>
<style type="text/css" >
#etax_area_form table { table-layout:fixed; border-collapse:collapse; word-break:break-all; border-spacing:0;  }

/* etax_table */
#etax_area_form { position:relative; padding:1px; z-index:999; width:780px; height:auto; }
#etax_area_form .etax_table { width:100%; border-spacing:0px 0px; }
#etax_area_form .etax_table th { font-weight:normal; padding-top:2px; padding-bottom:2px; line-height:15px; 	height:20px; }
#etax_area_form .etax_table th, x:-moz-any-link, x:default { height:25px;*height:20px; }
#etax_area_form .etax_table td { color:#333333; padding-top:2px; padding-bottom:2px; height:20px; }
#etax_area_form .etax_table td, x:-moz-any-link, x:default { height:25px;*height:20px; }
#etax_area_form .etax_table tbody td.splitline { height:0px; padding-top:1px; padding-bottom:0px; line-height:0px; }

/* table_border_red */
#etax_area_form .table_border_red { border:1px solid #E66464; }
#etax_area_form .table_border_red th { color:#E66464; }
#etax_area_form .table_border_red tbody th { border-top:1px solid #E66464; border-left:1px solid #E66464; }
#etax_area_form .table_border_red tbody td { border-top:1px solid #E66464; border-left:1px solid #E66464; }
#etax_area_form .table_border_red .underline { border-bottom:1px solid #E66464; }

/* table_border_blue */
#etax_area_form .table_border_blue { border:1px solid #666699; }
#etax_area_form .table_border_blue th { color:#666699; }
#etax_area_form .table_border_blue tbody th { border-top:1px solid #666699; border-left:1px solid #666699; }
#etax_area_form .table_border_blue tbody td { border-top:1px solid #666699; border-left:1px solid #666699; }
#etax_area_form .table_border_blue .underline { border-bottom:1px solid #666699; }

/* layout */
#etax_area_form .etax_table .gray_bg { 	background-color:#F5F5F5; }
#etax_area_form .etax_table span.way { display:inline-block; width:100px; margin-left:10px; }
#etax_area_form .etax_table span.sm { width:70px; }
#etax_area_form .etax_table div.PT1 { float:left; 	width:80px; text-align:right; margin-top:10px; }
#etax_area_form .etax_table div.PT2 { float:left; width:90px; }
#etax_area_form .etax_table div.PT3 { float:left; width:25px; 	text-align:left; margin-top:10px; }
#etax_area_form .etax_table span.PT { display:block; margin-left:0px; width:50px; height:20px; }
#etax_area_form .etax_table #TrusterInfo tr th { height:30px; }

/* area_info */
#area_info { margin-top:30px; }

/* Border Color */
.border_red { border:1px solid #E66464; }
.border_blue { border:1px solid #666699; }
.border_black { border:1px solid #777777; }

#etax_area_form .in_detail {
padding-top: 8px;
padding-bottom: 7px;
}
/* input text width */ 
.iw_189 { width:189px; }
.iw_190 { width:190px; }
.iw_279 { width:279px; }
.iw_283 { width:283px; }
/* Text */
.al_l { text-align:left !important; }
.al_c { text-align:center !important; }
.al_r { text-align:right !important; }
.height_20 { height:20px !important;}
.height_32 { height:32px !important;}
.height_35 { height:35px !important;}
.height_42 { height:42px !important; }
.height_46 { height:46px !important; }
.height_51 { height:51px !important; }
.height_75 { height:75px !important; }
label.for {
padding-left: 3px;
cursor: pointer;
}
/* Text */
.in_txt {
	height:25px;
	margin:0px;
	border:1px solid #cacaca;
	background-color:#FFFFFF;
	color:#333333;
	padding:2px;
	vertical-align:middle;
	font-size:12px;
	font-family:dotum;
}

/* Textarea */
.txt_ar {
	height:48px;
	margin:0px;
	border:1px solid #cacaca;
	background-color:#FFFFFF;
	color:#333333;
	padding:2px;	
	font-size:9pt;
	font-family:dotum;
}

/* Font */
.dotum { font-family:Dotum, dotum, tahoma, sans-serif; }
.batang { font-family:Batang, batang, tahoma, sans-serif; }
.bold { font-weight:bold; }
.normal { font-weight:normal; }
.letspc { letter-spacing:-1px; }
.letspc_0 { letter-spacing:0px; }
.underline { text-decoration:underline; }

.ft_1 { font-size:1px; line-height:1px; }
.ft_9 { font-size:9px; line-height:9px; }
.ft_10 { font-size:10px; line-height:10px; }
.ft_11 { font-size:11px; line-height:11px; }
.ft_12 { font-size:12px; line-height:12px; }
.ft_13 { font-size:13px; line-height:13px; }
.ft_14 { font-size:14px; line-height:14px; }
.ft_15 { font-size:15px; line-height:15px; }
.ft_16 { font-size:16px; line-height:16px; }
.ft_20 { font-size:20px; line-height:20px; }
.ft_24 { font-size:24px; line-height:24px; }

.ft_24 {
font-size: 24px;
line-height: 24px;
}
.lh_16 { line-height:16px !important; }
.lh_30 { line-height:30px !important; }

#area_form textArea {
overflow: hidden;
}
</style>

<body>

<script language="JavaScript">
$(function(){
	$( "#issuedate" ).datepicker({
		inline: true,
		dateFormat: "yy-mm-dd"
	});
	$( "#purchasedt" ).datepicker({
		inline: true,
		dateFormat: "yy-mm-dd"
	});
	
});
$(document).ready(function(){
	$("input:radio[name='PurposeType']:radio[value='<?=$purposetype?>']").attr("checked","true");
});
</script>
<form id="tax_reg_frm" name="tax_reg_frm" method="post" action="linkhub_tax_register_proc.php" >
<input type="hidden" name="t_idx" id="t_idx" value="<?=$_POST['t_idx']?>">
	<div id="etax_area_form" class="border_red">
		<table summary="세금계산서" class="etax_table table_border_red">
			<colgroup>
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
				<col width="2.94117647058824%">
			</colgroup>
			<thead>
				<tr>
					<th class="al_c" colspan="16" rowspan="2">

						<div>
							<span class="ft_24 bold" id="taxName">세금계산서</span>
						</div>
					</th>
					<th class="al_c" colspan="4" rowspan="2"><span>공 급 자</span><br>(보
						관 용)</th>
					<td colspan="2" rowspan="2">&nbsp;</td>
					<th class="al_r" colspan="4" style="padding-right: 3px !important;"></th>
					<td class="al_c" colspan="3"></td>
					<td class="al_c" colspan="1"></td>
					<td class="al_c" colspan="3"></td>
					<td class="al_c" colspan="1"></td>
				</tr>
				<tr>
					<th class="al_r" colspan="4" style="padding-right: 3px !important;">
					</th>
					<td colspan="8"></td>
				</tr>
			</thead>
			<tbody id="InvoiceList">
				<tr>
					<th class="al_c" colspan="1" rowspan="6"><span
						class="bold lh_30">공<br>급<br>자
					</span></th>
					<th class="al_c" colspan="3">등록번호</th>
					<td class="al_c" colspan="8"><input
						class="in_txt readonly al_c" maxlength="14" readonly="readonly"
						style="width: 169px;" tabindex="4" type="text"
						id="linkhub_corpnum" name="linkhub_corpnum" readonly="true"
						value="<?=substr($linkhub_corpnum,0,3)."-".substr($linkhub_corpnum,3,2)."-".substr($linkhub_corpnum,5)?>"></td>


					<th class="al_c" colspan="3">종사업장</th>
					<td class="al_c" colspan="2"></td>
					<th class="al_c" colspan="1" rowspan="6"><span
						class="bold lh_16">공<br>급<br>받<br>는<br>자
					</span></th>
					<th class="al_c" colspan="3">등록번호</th>
					<td class="al_c" colspan="8"><input class="in_txt al_c"
						maxlength="12" style="width: 169px;" tabindex="14" type="text"
						id="busino" name="busino" value="<?=substr($busino,0,3)."-".substr($busino,3,2)."-".substr($busino,5)?>"></td>

					<th class="al_c" colspan="3">종사업장</th>
					<td class="al_c" colspan="2"></td>
				</tr>
				<tr>
					<th class="al_c" colspan="3">상호</th>
					<td class="al_c" colspan="8"><textarea class="txt_ar kr"
							maxlength="70" style="width: 169px; height: 25px;" tabindex="6"
							id="linkhub_corpname" name="linkhub_corpname" readonly="true"><?=$linkhub_corpname?></textarea></td>
					<th class="al_c" colspan="1">성<br>명
					</th>
					<td class="al_c" colspan="4"><textarea class="txt_ar kr"
							maxlength="30" style="width: 79px; height: 25px;" tabindex="7"
							id="linkhub_ceoname" name="linkhub_ceoname" readonly="true"><?=$linkhub_ceoname?></textarea></td>
					<th class="al_c" colspan="3">상호</th>
					<td class="al_c" colspan="8"><textarea class="txt_ar kr"
							maxlength="70" style="width: 169px; height: 25px;" tabindex="16"
							id="company" name="company"><?=$company?></textarea></td>
					<th class="al_c" colspan="1">성<br>명
					</th>
					<td class="al_c" colspan="4"><textarea class="txt_ar kr"
							maxlength="30" style="width: 78px; height: 25px;" tabindex="17"
							id="name" name="name"><?=$name?></textarea></td>
				</tr>
				<tr>
					<th class="al_c" colspan="3">사업장<br>주소
					</th>
					<td class="al_c" colspan="13"><textarea class="txt_ar kr"
							maxlength="150" style="width: 283px; height: 36px;" tabindex="8"
							id="linkhub_addr" name="linkhub_addr" readonly="true"><?=$linkhub_addr?></textarea></td>
					<th class="al_c" colspan="3">사업장<br>주소
					</th>
					<td class="al_c" colspan="13"><textarea class="txt_ar kr"
							maxlength="150" style="width: 283px; height: 36px;" tabindex="18"
							id="address" name="address"><?=$address?></textarea></td>
				</tr>
				<tr>
					<th class="al_c" colspan="3">업태</th>
					<td class="al_c" colspan="6"><textarea class="txt_ar kr"
							maxlength="40" style="width: 123px; height: 36px;" tabindex="9"
							id="linkhub_biztype" name="linkhub_biztype" readonly="true"><?=$linkhub_biztype?></textarea></td>
					<th class="al_c" colspan="2">종목</th>
					<td class="al_c" colspan="5"><textarea class="txt_ar kr"
							maxlength="40" style="width: 101px; height: 36px;" tabindex="10"
							id="linkhub_bizclass" name="linkhub_bizclass" readonly="true"><?=$linkhub_bizclass?></textarea></td>
					<th class="al_c" colspan="3">업태</th>
					<td class="al_c" colspan="6"><textarea class="txt_ar kr"
							maxlength="40" style="width: 124px; height: 36px;" tabindex="19"
							id="service" name="service"><?=$service?></textarea></td>
					<th class="al_c" colspan="2">종목</th>
					<td class="al_c" colspan="5"><textarea class="txt_ar kr"
							maxlength="40" style="width: 100px; height: 36px;" tabindex="20"
							id="item" name="item"><?=$item?></textarea></td>
				</tr>
				<tr>
					<th class="al_c" colspan="3">담당자</th>
					<td class="al_c" colspan="6"><input class="in_txt kr"
						maxlength="30" style="width: 123px;" tabindex="11" type="text"
						id="linkhub_contactname" name="linkhub_contactname" value="<?=$linkhub_contactname?>" readonly="true"></td>
					<th class="al_c" colspan="2">연락처</th>
					<td class="al_c" colspan="5"><input class="in_txt"
						maxlength="20" style="width: 100px;" tabindex="12" type="text"
						id="linkhub_contacttel" name="linkhub_contacttel" value="<?=$linkhub_contacttel?>" readonly="true"></td>
					<th class="al_c" colspan="3">담당자</th>
					<td class="al_c" colspan="6"><input class="in_txt kr"
						maxlength="30" style="width: 124px;" tabindex="21" type="text"
						id="InvoiceeContactName1" name="InvoiceeContactName1" value="<?=$invoiceecontactname1?>"></td>
					<th class="al_c" colspan="2">연락처</th>
					<td class="al_c" colspan="5"><input class="in_txt"
						maxlength="20" style="width: 100px;" tabindex="22" type="text"
						id="InvoiceeTEL1" name="InvoiceeTEL1" value=""></td>
				</tr>
				<tr>
					<th class="al_c" colspan="3">이메일</th>
					<td class="al_c" colspan="13"><input maxlength="40"
						tabindex="13" type="text" class="in_txt en iw_283"
						id="linkhub_contactemail" name="linkhub_contactemail"
						value="<?=$linkhub_contactemail?>" readonly="true"></td>
					<th class="al_c" colspan="3">이메일</th>
					<td class="al_c" colspan="13"><input maxlength="40"
						tabindex="23" type="text" class="in_txt en iw_283"
						id="invoiceeemail1" name="invoiceeemail1" value="<?=$invoiceeemail1?>"></td>
				</tr>
				<tr>
					<td class="al_c splitline" colspan="34">&nbsp;</td>
				</tr>
			</tbody>



			<tbody id="TotalList">
				<tr>
					<th class="al_c" colspan="4"><span class="bold">작성일자</span>&nbsp;</th>
					<th class="al_c pet0" colspan="16"><span class="bold">공급가액</span></th>
					<th class="al_c pet1" colspan="14" style="display:;"><span
						class="bold">세액</span></th>
				</tr>
				<tr>
					<td class="al_c" colspan="4"><input
						class="in_txt al_c dtpicker dp-applied" maxlength="10"
						style="width: 79px;" tabindex="25" type="text" id="issuedate"
						name="issuedate" value="<?=$issuedate?>"></td>
					<td class="al_c pet0" colspan="16"><input
						class="in_txt readonly al_r" maxlength="18" readonly="readonly"
						style="width: 351px;" type="text" id="supply"
						name="supply" value="<?=$supply?>"></td>
					<td class="al_c pet1" colspan="14" style="display:;"><input
						class="in_txt readonly al_r" maxlength="18" readonly="readonly"
						style="width: 306px;" type="text" id="surtax" name="surtax"
						value="<?=$surtax?>"></td>
				</tr>
				<tr>
					<td class="al_c splitline" colspan="34">&nbsp;</td>
				</tr>
			</tbody>
			<tbody id="WriteTypeList">
				<tr>
					<th class="gray_bg al_c" colspan="4"><span class="bold">작성방법</span></th>
					<td class="gray_bg" colspan="30"><span class="pdl_10"><input
							checked="checked" class="rad" id="WriteType1" name="WriteType"
							tabindex="29" type="radio" value="직접입력"><label
							class="for" for="WriteType1">직접입력</label></span> </span></td>
				</tr>
				<tr>
					<td class="al_c splitline" colspan="34">&nbsp;</td>
				</tr>
			</tbody>
			<tbody id="taxList">
				<tr>
					<th class="al_c" colspan="4"><span class="bold">월-일</span></th>
					<th class="al_c" colspan="6"><span class="bold">품목</span></th>
					<th class="al_c" colspan="5"><span class="bold">규격</span></th>
					<th class="al_c pet2" colspan="3"><span class="bold">수량</span></th>
					<th class="al_c pet3" colspan="3"><span class="bold">단가</span></th>
					<th class="al_c pet4" colspan="4"><span class="bold">공급가액</span></th>
					<th class="al_c pet5" colspan="4" style="display:;"><span
						class="bold">세액</span></th>
					<th class="al_c" colspan="5"><span class="bold">비고</span></th>
				</tr>
				<tr id="item_0">
					<td class="al_c" colspan="4">
					<input type="hidden" name="serialnum" id="serialnum" value="1" />
					<input class="in_txt al_c in_detail" maxlength="2" style="width: 79px;" tabindex="50" type="text"
						id="purchasedt" name="purchasedt" value="<?=$purchasedt?>" ></td>
					<td class="al_c" colspan="6"><textarea class="txt_ar"
							maxlength="100" style="width: 130px; height: 36px;" tabindex="50"
							id="productname" name="productname" readonly="true"><?=$productname?></textarea>

					</td>
					<td class="al_c" colspan="5"><textarea class="txt_ar"
							maxlength="60" style="width: 100px; height: 25px;" tabindex="50"
							id="spec" name="spec" readonly="true"><?=$spec?></textarea></td>
					<td class="al_c pet2" colspan="3"><input
						class="in_txt al_r in_detail" maxlength="12"
						style="width: 55px;" tabindex="50" type="text"
						id="qty" name="qty" value="<?=$qty?>" readonly="true"></td>
					<td class="al_c pet3" colspan="3"><input
						class="in_txt al_r in_detail" maxlength="18"
						style="width: 55px;" tabindex="50" type="text"
						id="unitcost" name="unitcost" value="<?=$unitcost?>" readonly="true"></td>
					<td class="al_c pet4" colspan="4"><input
						class="in_txt al_r in_detail" maxlength="18"
						style="width: 78px;" tabindex="50" type="text"
						id="supplycost" name="supplycost"
						value="<?=$supplycost?>" readonly="true"></td>
					<td class="al_c pet5" colspan="4" style="display:;"><input
						class="in_txt al_r in_detail" maxlength="18"
						style="width: 78px;" tabindex="50" type="text"
						id="tax" name="tax" value="<?=$tax?>" readonly="true"></td>
					<td class="al_c" colspan="4"><textarea class="txt_ar"
							maxlength="100" style="width: 78px; height: 25px;" tabindex="50"
							id="remark" name="remark"><?=$remark?></textarea></td>
					<td class="al_c" colspan="1"></td>
				</tr>
				<!--
	<tr id="item_1">		        	
	  <td class="al_c" colspan="1">
		<input type="hidden" id="detailList1.SerialNum" name="detailList[1].SerialNum" value="2">
		<input type="hidden" id="detailList1.PurchaseDT" name="detailList[1].PurchaseDT" value="">
		<input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList1.PurchaseDT1" name="detailList[1].PurchaseDT" value="">
	  </td> 
	  <td class="al_c" colspan="1"><input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList1.PurchaseDT2" name="detailList[1].PurchaseDT" value=""></td> 
	  <td class="al_c" colspan="8">
		<textarea class="txt_ar" maxlength="100" style="width:144px; height:25px;" tabindex="50" id="detailList1.ItemName" name="detailList[1].ItemName"></textarea>
		
	  </td>
	  <td class="al_c" colspan="5"><textarea class="txt_ar" maxlength="60" style="width:100px; height:25px;" tabindex="50" id="detailList1.Spec" name="detailList[1].Spec"></textarea></td>
	  <td class="al_c pet2" colspan="3"><input class="in_txt al_r in_detail" maxlength="12" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList1.Qty" name="detailList[1].Qty" value=""></td> 
	  <td class="al_c pet3" colspan="3"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList1.UnitCost" name="detailList[1].UnitCost" value=""></td> 
	  <td class="al_c pet4" colspan="4"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountSupplyToTax(this);" style="width: 78px;" tabindex="50" type="text" id="detailList1.SupplyCost" name="detailList[1].SupplyCost" value=""></td> 
	  <td class="al_c pet5" colspan="4" style="display:;"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountListTax(this);" style="width:78px;" tabindex="50" type="text" id="detailList1.Tax" name="detailList[1].Tax" value=""></td> 
	  <td class="al_c" colspan="4"><textarea class="txt_ar" maxlength="100" style="width:78px; height:25px;" tabindex="50" id="detailList1.Remark" name="detailList[1].Remark"></textarea></td>
	  <td class="al_c" colspan="1"></td>
	</tr>
	<tr id="item_2">		        	
	  <td class="al_c" colspan="1">
		<input type="hidden" id="detailList2.SerialNum" name="detailList[2].SerialNum" value="3">
		<input type="hidden" id="detailList2.PurchaseDT" name="detailList[2].PurchaseDT" value="">
		<input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList2.PurchaseDT1" name="detailList[2].PurchaseDT" value="">
	  </td> 
	  <td class="al_c" colspan="1"><input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList2.PurchaseDT2" name="detailList[2].PurchaseDT" value=""></td> 
	  <td class="al_c" colspan="8">
		<textarea class="txt_ar" maxlength="100" style="width:144px; height:25px;" tabindex="50" id="detailList2.ItemName" name="detailList[2].ItemName"></textarea>
		
	  </td>
	  <td class="al_c" colspan="5"><textarea class="txt_ar" maxlength="60" style="width:100px; height:25px;" tabindex="50" id="detailList2.Spec" name="detailList[2].Spec"></textarea></td>
	  <td class="al_c pet2" colspan="3"><input class="in_txt al_r in_detail" maxlength="12" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList2.Qty" name="detailList[2].Qty" value=""></td> 
	  <td class="al_c pet3" colspan="3"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList2.UnitCost" name="detailList[2].UnitCost" value=""></td> 
	  <td class="al_c pet4" colspan="4"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountSupplyToTax(this);" style="width: 78px;" tabindex="50" type="text" id="detailList2.SupplyCost" name="detailList[2].SupplyCost" value=""></td> 
	  <td class="al_c pet5" colspan="4" style="display:;"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountListTax(this);" style="width:78px;" tabindex="50" type="text" id="detailList2.Tax" name="detailList[2].Tax" value=""></td> 
	  <td class="al_c" colspan="4"><textarea class="txt_ar" maxlength="100" style="width:78px; height:25px;" tabindex="50" id="detailList2.Remark" name="detailList[2].Remark"></textarea></td>
	  <td class="al_c" colspan="1"></td>
	</tr>
	<tr id="item_3">		        	
	  <td class="al_c" colspan="1">
		<input type="hidden" id="detailList3.SerialNum" name="detailList[3].SerialNum" value="4">
		<input type="hidden" id="detailList3.PurchaseDT" name="detailList[3].PurchaseDT" value="">
		<input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList3.PurchaseDT1" name="detailList[3].PurchaseDT" value="">
	  </td> 
	  <td class="al_c" colspan="1"><input class="in_txt al_c in_detail" maxlength="2" style="width:13px;" tabindex="50" type="text" id="detailList3.PurchaseDT2" name="detailList[3].PurchaseDT" value=""></td> 
	  <td class="al_c" colspan="8">
		<textarea class="txt_ar" maxlength="100" style="width:144px; height:25px;" tabindex="50" id="detailList3.ItemName" name="detailList[3].ItemName"></textarea>
		
	  </td>
	  <td class="al_c" colspan="5"><textarea class="txt_ar" maxlength="60" style="width:100px; height:25px;" tabindex="50" id="detailList3.Spec" name="detailList[3].Spec"></textarea></td>
	  <td class="al_c pet2" colspan="3"><input class="in_txt al_r in_detail" maxlength="12" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList3.Qty" name="detailList[3].Qty" value=""></td> 
	  <td class="al_c pet3" colspan="3"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountQtyList(this);" style="width: 55px;" tabindex="50" type="text" id="detailList3.UnitCost" name="detailList[3].UnitCost" value=""></td> 
	  <td class="al_c pet4" colspan="4"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountSupplyToTax(this);" style="width: 78px;" tabindex="50" type="text" id="detailList3.SupplyCost" name="detailList[3].SupplyCost" value=""></td> 
	  <td class="al_c pet5" colspan="4" style="display:;"><input class="in_txt al_r in_detail" maxlength="18" onkeyup="number_to_money(this, 1); amountListTax(this);" style="width:78px;" tabindex="50" type="text" id="detailList3.Tax" name="detailList[3].Tax" value=""></td> 
	  <td class="al_c" colspan="4"><textarea class="txt_ar" maxlength="100" style="width:78px; height:25px;" tabindex="50" id="detailList3.Remark" name="detailList[3].Remark"></textarea></td>
	  <td class="al_c" colspan="1"></td>
	</tr>
	-->
			</tbody>
			<tbody id="taxTotalList">
				<tr>
					<td class="al_c splitline" colspan="34">&nbsp;</td>
				</tr>
				<tr>
					<th class="al_c" colspan="9"><span class="bold">합계금액</span></th>
					<th class="al_c" colspan="4">현금</th>
					<th class="al_c" colspan="4">수표</th>
					<th class="al_c" colspan="4">어음</th>
					<th class="al_c" colspan="4">외상미수금</th>
					<td class="al_c" colspan="9" rowspan="2">
						<div class="PT1">이 금액을</div>
						<div align="center" class="PT2">
							<span class="PT"><input class="rad" id="PurposeType1"
								tabindex="55" type="radio" value="영수" name="PurposeType"><label
								class="for" for="PurposeType1">영수</label></span> <span class="PT"><input
								checked="checked" class="rad" id="PurposeType2" tabindex="55"
								type="radio" value="청구" name="PurposeType"><label
								class="for" for="PurposeType2">청구</label></span>
						</div>
						<div class="PT3">함</div>
					</td>
				</tr>
				<tr>
					<td class="al_c" colspan="9"><input
						class="in_txt readonly al_r" maxlength="18" readonly="readonly"
						style="width: 194px;" type="text" id="price"
						name="price" value="<?=$price?>" ></td>
					<td class="al_c" colspan="4"><input class="in_txt al_r"
						maxlength="18" 
						style="width: 78px;" tabindex="51" type="text" id="Cash"
						name="Cash" value="" readonly="true"></td>
					<td class="al_c" colspan="4"><input class="in_txt al_r"
						maxlength="18" 
						style="width: 78px;" tabindex="52" type="text" id="ChkBill"
						name="ChkBill" value="" readonly="true"></td>
					<td class="al_c" colspan="4"><input class="in_txt al_r"
						maxlength="18" 
						style="width: 78px;" tabindex="53" type="text" id="Note"
						name="Note" value="" readonly="true"></td>
					<td class="al_c" colspan="4"><input class="in_txt al_r"
						maxlength="18" 
						style="width: 78px;" tabindex="54" type="text" id="Credit"
						name="Credit" value="" readonly="true"></td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
</body>