<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");

$id;
$deliprice;
$title_all_price =0;
$temp_tinyimage;
$temp_sellprice;
$temp_quantity;
$temp_reserve;
$temp_productname;
$temp_productcode;
$strno =$_POST['strno'];


$cnt_arr = array();


$query ="select * from tblmember where id =
(select id from tbl_estimate_sheet where  no ='".$strno."')";		// 견적서 신청자의 정보를 가져오는 query
$query_start=pmysql_query($query,get_db_conn());
$start=pmysql_fetch_object($query_start);
$memgroup = $start->group_code;
$id = $start->id;
	
$query1 =	"select * from tbl_estimate_sheet where no ='".$strno."'";
$query_start1=pmysql_query($query1,get_db_conn());
$main=pmysql_fetch_object($query_start1);
$tel = $main->tel;
$email = $main->email;

$select ="select * from tblshopinfo";
$select=pmysql_query($select,get_db_conn());
$select=pmysql_fetch_object($select);
$deli_basefee	= $select->deli_basefee;
$deli_miniprice = $select->deli_miniprice;


$sqlVcount = "select * from tbl_estimate_sheet where no ='".$strno."'";
//	where a.id='".$_ShopInfo->memid."' and a.basketidx in(".$temp.") order by a.date desc";	
$resultVcount=pmysql_query($sqlVcount,get_db_conn());

$productcode = array();

$product_cnt = 0;

$temp_quantity=  explode("|",$main->quantity);	// 수량
$temp_price=  explode("|",$main->sellprice)	;	// 가격
$temp_productname=  explode("|",$main->productname)	;	// 상품명

$loop =(count($temp_quantity) -1);

for($i = 0; $i < $loop ; $i++){
	$product_cnt += $temp_quantity[$i];			// 제품 수
}




$title_all_price = 0;
for($i = 0; $i < $loop ; $i++){
	$title_all_price += $temp_price[$i]*$temp_quantity[$i];			// 총 가격
	$total_all_price += $temp_price[$i]*$temp_quantity[$i];			// 총 가격
	//exdebug($temp_price[$i]);exdebug($temp_quantity[$i]);
}
if( $main->estimate_price){
	$title_all_price=$main->estimate_price;
}


?>


<html>
<head>

<title></title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<link rel="stylesheet" href="../css/digiatom.css" />
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
resizeTo(750,850);
//-->
</SCRIPT>
</head>

<style>
body,table,td,th,p,span,em,strong {padding:0px; margin:0px; font-family:dotum; font-size:12px;}
table {border-collapse:collapse;}
div.estimate_wrap {padding:0px}
div.estimate_wrap h2 {font-family:dotum; font-size:20px; font-weight:bold; color:#000; text-align:center; padding:0px 0px 10px;}
div.estimate_wrap div.company_info {border-top:1px dotted #333; padding:15px 0px; border-bottom:1px dotted #333;}
table.company th {text-align:left; font-weight:lighter; font-size:12px; border-bottom:1px dotted #999;}
table.company td {padding:5px 0px; border-bottom:1px dotted #999;}
table.company td.price span {color:#ff6000; font-weight:bold;}
table.company td.subject {font-weight:bold; color:#000; vertical-align:top;}
table.company td input{border:none;}

table.info {border-collapse:collapse; border-top:2px solid #c6c6c6; border-left:2px solid #c6c6c6;}
table.info th {background-color:#efefef; padding:3px 0px; font-weight:lighter; text-align:left; text-indent:3px; border-bottom:2px solid #c6c6c6; border-right:2px solid #c6c6c6;}
table.info td {padding:3px 0px; text-indent:3px; border-bottom:2px solid #c6c6c6; border-right:2px solid #c6c6c6;}
.ment01 {font-size:12px; text-align:center; padding:5px 0px;}
table.info th.ta_c {text-align:center;}
table.info td.add_price {color:#ff6000; font-weight:bold; text-align:right; padding-right:5px;}
table.info td.ta_c {text-align:center;}
table.info td.last_total div.total1 {padding:10px 20px 10px;font-size:16px; color:#000; margin:3px; border:1px solid #dfdfdf; background-color:#f7f7f7; text-align:right; font-weight:bold;}
table.info td.last_total div.total1 span {font-size:16px; font-weight:bold; color:#ff6000;}

@media print { .notprint {display: none;} } /* 인쇄시 불필요한 부분 비활성화 */
</style>


<div class="estimate_wrap">
	<h2>온라인견적서</h2>
	<div class="company_info">
	<div style="margin:auto; width:700px;">
	<table align="center" width="700px">
		<colgroup><col style="width:39%" /><col style="width:2%" /><col style="width:59%" /></colgroup>
		<tr valign=top>
			<td>
			
				<table width="100%" class="company">
					<colgroup>
						<col style="width:25%" /><col style="width:5%" /><col style="width:70%" />
					</colgroup>
			<? if( $id){?>
					<tr>
						<th>> 아이디</th>
						<td>: </td>
						<td><span><?=$id?><span></td>
					</tr
					<tr>
						<th>> 회사명</th>
						<td>: </td>
						<td><input type="text" name="" id="company_name"/></td>
					</tr>
					<tr>
						<th>> 담당자</th>
						<td>: </td>
						<td><input type="text" name="" id="person"/></td>
					</tr>
				<? }else{?>
					<tr>
						<th>> 전화번호</th>
						<td>: </td>
						<td><? echo $tel?></td>
					</tr>
					<tr>
						<th>> 이메일</th>
						<td>: </td>
						<td><? echo $email?></td>
					</tr>
				<?} ?>
					<tr>
						<th>> 일자</th>
						<td>:</td>
						<td><?php echo date("Y")."년 ".date("m")."월 ".date("d")."일 ".date("H")."시 ".date("i")."분"?></td>
					</tr>
					<tr>
						<th>> 금액</th>
						<td>:</td>
						<!-- <td class="price" ><span><?=number_format($title_all_price)?></span> 원</td> -->
						<td class="price" >
							<span>
								<input type="text" id="ajax_price" value="<?=number_format($title_all_price)?>"></input>
							</span> 원
						</td>
					</tr>
					<tr>
						<th>> 제품 수</th>
						<td>: </td>
						<td class="price"><span><?=intval($product_cnt)?></span> 개</td>
						<!--<td class="subject"><?echo intval(count($temp)/8)." 개";?></td>-->
					</tr>
				</table>
			
			</td>
			<td></td>
			<td>
				
				<table class="info" width="100%">
					<colgroup><col style="width:20%"/><col style="width:40%"/><col style="width:10%"/><col style="width:30%"/></colgroup>
					<tr>
						<th colspan="4"><b>공급자</b></th>
					</tr>
					<tr>
						<th>사업자번호</th>
						<td colspan="3"><?=$_data->companynum?></td>
					</tr>
					<tr>
						<th>상호명</th>
						<td><?=$_data->companyname?></td>
						<th>대표</th>
						<td><?=$_data->companyowner?></td>
					</tr>
					<tr>
						<th>주소</th>
						<td colspan="3"><?=$_data->companyaddr?></td>
					</tr>
					<tr>
						<th>업태</th>
						<td><?=$_data->companybiz?></td>
						<th>종목</th>
						<td><?=$_data->companyitem?></td>
					</tr>
					<tr>
						<th>대표전화</th>
						<td><?=$_data->info_tel?></td>
						<th>FAX</th>
						<td>02-714-0287</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
	</div>
	</div>
	<p class="ment01">아래와 같이 견적합니다.</p>






<div style="margin:auto; width:700px;">
	<table class="info" align="center" width="700px">
	<colgroup><col style="width:auto"/><col style="width:20%"/><col style="width:15%"/><col style="width:15%"/></colgroup>
		<tr>
			<th class="ta_c">상품명</th>
			<th class="ta_c">판매가</th>
			<!--<th class="ta_c">수량</th>-->
			<th class="ta_c">총 수량</th>
			<th class="ta_c">총합</th>
		</tr>
<? 
for($i=0 ; $i< $loop ; $i++){
?> 

		<tr>
			<td style="text-align:center;"><?=$temp_productname[$i]?> </td>
			<td style="text-align:center;"><?=number_format($temp_price[$i])?> </td>
			<td style="text-align:center;"><?=$temp_quantity[$i]?> </td>
			<td style="text-align:center;"><?=number_format($temp_price[$i]*$temp_quantity[$i])?>			<!-- 총합 -->
			</td>
		</tr>	

		<?if($opt1_idx != 0  ){
			}	// if
} // for


?>
<!-- --><!-- -->
<!-- -->
<tr>
			<td class="last_total" colspan="4"> 
				<!--<div class="total1">총 적립금 : <span><?=number_format($reserverprice)?></span>원</div>-->
				<?
				if( $total_all_price > $deli_miniprice){	// 10만원 이상시					
					$deli_basefee = 0;		
				}else{
					$total_all_price +=$deli_basefee;			
				}
?>
				<!--div class="total1">배송비 : <span><?=number_format($deli_basefee)?></span>원</div>-->
				<div class="total1">   총 합계 : <span><?=number_format($total_all_price)?></span>원</div>
			</td>
		</tr>
	</table>
</div>


<div style="margin:auto; width:700px;">
	<table align="center" width="700px">
		<tr height="30">
			<td>* 상기 금액은 부가세 포함 견적입니다.</td>
			<td align=right><b><?=date("Y")?>년 <?=date("m")?>월 <?=date("d")?>일</b></td>
		</tr>
		<tr height="50">
			<td align=center colspan="2">
			<? if( $id){ ?>
				<a href="javascript:go_print(0)" class=btn_D> 인쇄하기 </a>
				<a href="javascript:nego(0)" class=btn_D> 수정하기 </a> 
			<? }else{ // 비회원?>
				<a href="javascript:go_print(1)" class=btn_D> 인쇄하기 </a>
				<a href="javascript:nego(1)" class=btn_D> 수정하기 </a> 
			<? } ?>
				<a href="javascript:window.close();" class=btn_D>화면닫기</a>
			</td>
		</tr>
	</table>
</div>

<input type=hidden id="userid"			 value='<?=$id?>' ></input>
<input type=hidden id="tel"			 value='<?=$tel?>' ></input>
<input type=hidden id="email"			 value='<?=$email?>' ></input>
<input type=hidden id="temp_tinyimage"	 value='<?=$temp_tinyimage?>' ></input>
<input type=hidden id="temp_sellprice"	 value='<?=$temp_sellprice?>' ></input>
<input type=hidden id="temp_quantity"	 value='<?=$temp_quantity?>' ></input>
<input type=hidden id="temp_reserve"	 value='<?=$temp_reserve?>' ></input>
<input type=hidden id="temp_productname" value='<?=$temp_productname?>' ></input>
<input type=hidden id="temp_productcode" value='<?=$temp_productcode?>' ></input>
<input type=hidden id="temp_basketidx" value='<?=$temp?>' ></input>
<input type=hidden id="temp_no" value='<?=$strno?>' ></input>

<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
<script>
///////////
	function nego(num){
	
	var price = document.getElementById("ajax_price").value;

	var userid = document.getElementById("userid").value;
	var tinyimage = document.getElementById("temp_tinyimage").value;
	var sellprice = document.getElementById("temp_sellprice").value;
	var quantity = document.getElementById("temp_quantity").value;
	var reserve = document.getElementById("temp_reserve").value;
	var productname = document.getElementById("temp_productname").value;
	var productcode = document.getElementById("temp_productcode").value;
	var basketidx = document.getElementById("temp_basketidx").value;
	var no = document.getElementById("temp_no").value;
	var ajax_price=	document.getElementById("ajax_price");
	
//	var 
	if(num==0){
			if(company_name.value==""||company_name.value==null){
				alert("회사명을 입력해주세요.\n개인이면 개인이라고 입력해주세요");
				company_name.focus();
			}else
			if(person.value==""||person.value==null){
				alert("담당자를 입력해주세요.");
				person.focus();
			}else{
				$.post("ajax_nego.php",
					{ price:price , 
					id:userid,
					tinyimage:tinyimage,
					sellprice:sellprice,
					quantity:quantity,
					reserve:reserve,
					productname:productname,
					productcode:productcode,
					basketidx:basketidx,
					no:no
					},
					function(data){
						if(data){
							if(data == 1){
								alert("수정 되었습니다");
								window.close();
							}else{
								alert(data);
								ajax_price.value ="";
								ajax_price.focus();
							}						
						}
				});
			};	
	}	
	if(num==1){		

	var email = document.getElementById("email").value;
	var tel = document.getElementById("tel").value; 
	var telnum = document.getElementById("tel"); 

		if( telnum.value=="" || telnum.value==null ){
			alert("전화번호를 입력해주세요.");
			telnum.focus();
		}else{
			$.post("ajax_nego.php",
				{ price:price , 
				id:userid,
				tinyimage:tinyimage,
				sellprice:sellprice,
				quantity:quantity,
				reserve:reserve,
				productname:productname,
				productcode:productcode ,
				email:email,
				tel:tel,
				basketidx:basketidx,
				no:no
				},
				function(data){
					if(data){
						if(data == 1){
							alert("수정 되었습니다");
							window.close();
						}else{
							alert(data);
							ajax_price.value ="";
							ajax_price.focus();
						}						
					}
				});
		};
	}
	
}; // nego function end

function go_print(num){

		var company_name=document.getElementById("company_name");
		var person=document.getElementById("person");
		var tel=document.getElementById("tel");
			
		if( num == 0){			
			if(company_name.value==""||company_name.value==null){
				alert("회사명을 입력해주세요.\n개인이면 개인이라고 입력해주세요");
				company_name.focus();
			}else
			if(person.value==""||person.value==null){
				alert("담당자를 입력해주세요.");
				person.focus();
			}else{
				print();
			}
		}


		if( num ==1 ){
			if( num == 1 && (tel.value==""||tel.value==null) ){
				alert("전화번호를 입력해주세요.");
				tel.focus();
			}else{
				print();
			}
		}					

	};
</script>	

</html>