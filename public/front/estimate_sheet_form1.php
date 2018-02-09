<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");

$id;
$temp_tinyimage;
$temp_sellprice;
$temp_quantity;
$temp_reserve;
$temp_productname;
$temp_productcode;

$temp =	substr($_POST['strBasket'],0,strlen($_POST['strBasket'])-1 );  
$temp =	str_replace("|",",",$temp);
//exdebug($_POST['strBasket']);																									//	exdebug($temp);
//$temp  =  substr($temp,0,strlen($temp)-1 );  
		//$v  = str_replace(",","",$v);
	$temp_v = explode(",",$temp);
$temp ='';
	foreach($temp_v as $kk => $vv){
		$temp.="'".$vv."',";
	}
$temp  =  substr($temp,0,strlen($temp)-1 );  
																									//	exdebug($temp);

$cnt_arr = array();

$deliprice;


$title_all_price =0;

$select ="select * from tblshopinfo";
$select=pmysql_query($select,get_db_conn());
$select=pmysql_fetch_object($select);
$deli_basefee	= $select->deli_basefee;
$deli_miniprice = $select->deli_miniprice;

//exdebug($_ShopInfo->memid);
//exdebug($_ShopInfo);
if( $_ShopInfo->memid){
	$sqlVcount = "select distinct(a.*),c.option1, c.option_consumer , c.option_price  from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$temp.") order by a.date desc";	
//	where a.id='".$_ShopInfo->memid."' and a.basketidx in(".$temp.") order by a.date desc";	
	$resultVcount=pmysql_query($sqlVcount,get_db_conn());

}else{	// 비회원
	$sqlVcount = "select distinct(a.*),c.option1, c.option_consumer , c.option_price  from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$temp.") order by a.date desc";	

	$resultVcount=pmysql_query($sqlVcount,get_db_conn());
}																						//	exdebug($sqlVcount);
$productcode = array();

$product_cnt = 0;
while($row=pmysql_fetch_object($resultVcount)){		// 정보 가져오는 오는데.. productcode 비교
	$productcode[$row->productcode] .= $row->basketidx.",";
	$tempkey = $row->tempkey;	// 하단 query에서 필요한 변수
	$product_cnt += $row->quantity;
}


if( $_ShopInfo->memid){
$total ="select distinct(a.*),c.option1 option1,c.productname productname, c.option_consumer option_consumer ,c.sellprice sellprice , c.option_price option_price 
from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$temp.") order by a.date desc";	
//	where a.id='".$_ShopInfo->memid."'   order by a.date desc";
	//		where a.id='".$_ShopInfo->memid."'   order by a.date desc";
}else{	// 비회원
	/*
$total ="select distinct(a.*),c.option1 option1,c.productname productname, c.option_consumer option_consumer ,c.sellprice sellprice , c.option_price option_price 
from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.tempkey='".$tempkey."'   order by a.date desc";
	*/
	$total = "select distinct(a.*) ,c.option1 option1,c.productname productname, c.option_consumer option_consumer ,c.sellprice sellprice , c.option_price option_price 
	from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$temp.") order by a.date desc";	
//	where  a.basketidx in(".$temp.") order by a.date desc";	
}
// exdebug($total);
//exdebug($tempkey);

$total=pmysql_query($total,get_db_conn());


 
while($row_title=pmysql_fetch_object($total)){

		if( $_ShopInfo->memgroup != ""){
			$query= "SELECT a.* ,c.sellprice as group_sellprice,c.consumerprice as group_consumerprice 
							FROM tblproduct a
							LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode 
							LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price WHERE group_code = '".$_ShopInfo->memgroup."' ) c 
							ON a.productcode = c.productcode
							WHERE a.display = 'Y' and a.productcode ='".$row_title->productcode."'  
							order by a.vcnt desc ";

							//exdebug($query);
			$query=pmysql_query($query,get_db_conn());
			$query=pmysql_fetch_object($query);
			//exdebug($query->group_sellprice);
			if( $query->group_sellprice != ""){
				$row_title->sellprice=$query->group_sellprice;
			}
		}else{	// 비회원
		
		}
		//exdebug($row_title->productcode);
		$option = explode(",",$row_title->option_price);	
		$row_title->quantity;								// 옵션수량	
		
		$option_price =  $option[$row_title->opt1_idx-1] ;	// 옵션 가격		// $row_title->sellprice 가 그룹일대인걸 어떻게 알지
		//exdebug( $option_price );
		
		$title_all_price+= $row_title->quantity * ($option_price+ exchageRate($row_title->sellprice) );
			
		
		//exdebug($title_all_price);
		//exdebug($row_title->sellprice);

}
//exdebug($title_all_price);
/////////


pmysql_free_result($select);
?>


<html>
<head>

<title></title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
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

a.btn_A {display:inline-block; color:#fff; background-color:#ee4035; height:39px; width:119px; border-bottom:1px solid #d66000; border-right:1px solid #d66000; text-align:center; font:bold 14px/40px dotum; vertical-align:top}
a.btn_A.small { height:24px; width:89px; line-height:25px}
a.btn_A.login { height:70px; width:99px; line-height:70px}
a.btn_A.wide {width:138px}
a.btn_A:hover {background-color:#d3291f}

a.btn_A {display:inline-block; color:#fff; background-color:#ee4035; height:39px; width:119px; border-bottom:1px solid #d66000; border-right:1px solid #d66000; text-align:center; font:bold 14px/40px dotum; vertical-align:top}
a.btn_A:hover {background-color:#d3291f}

a.btn_B {display:inline-block; color:#666; background-color:#fff; height:38px; width:118px; border-bottom:1px solid #b4b4b4; border-right:1px solid #b4b4b4; border-top:1px solid #ddd; border-left:1px solid #ddd; text-align:center; font:bold 14px/40px dotum; vertical-align:top}
a.btn_B:hover {background-color:#b4b4b4; border-bottom:1px solid #ddd; border-top:1px solid #ddd}

a.btn_C {display:inline-block; color:#ccc; height:24px; width:76px; border:1px solid #999; text-align:center; border-radius:2px; margin-right:8px; font:11px/26px dotum}
a.btn_C:hover {border-color:#ffa93a; color:#ffa93a}

a.btn_D {display:inline-block; color:#fff; height:40px; padding:0px 20px; text-align:center; border-radius:2px; font:bold 12px/40px dotum; background-color:#44474c}
a.btn_D.on {background-color:#ee4035; color:#fff}
a.btn_D:hover {background-color:#323335}
a.btn_D.on:hover {background-color:#d3291f}

a.btn_E {display:inline-block; color:#999; height:16px; padding:0px 18px 0 8px; border:1px solid #dfdfdf; text-align:center; border-radius:1px; font:11px/18px dotum; background-color:#FFF; vertical-align:middle; letter-spacing:-1px;position:relative}
a.btn_E:before {display:block; position:absolute; top:4px; right:10px; background:url(../img/icon/icon.png) -290px 0px no-repeat; width:5px; height:9px; content:""}
a.btn_E:hover {border-color:#ddd; background-color:#f0f0f0; color:#999}


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
			<? if( $_ShopInfo->memid != ""){?>
					<tr>
						<th>> 아이디</th>
						<td>: </td>
						<td><span><?=$_ShopInfo->memid?></span></td>
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
						<td><input type="text" name="" id="tel"/></td>
					</tr>
					<tr>
						<th>> 이메일</th>
						<td>: </td>
						<td><input type="text" name="" id="email"/></td>
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
			
foreach($productcode as $k => $v){
$v  =  substr($v,0,strlen($v)-1 );  
		//$v  = str_replace(",","",$v);
	$temp_v = explode(",",$v);
$v ='';
	foreach($temp_v as $kk => $vv){
		$v .="'".$vv."',";
	}
$v  =  substr($v,0,strlen($v)-1 );  
//exdebug($v);		

	if( $_ShopInfo->memid){

		$sqlVcount = "select distinct(a.*),c.option1 option1,c.productname productname, c.option_consumer option_consumer , c.option_price option_price  from tblbasket a
		LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
		LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
		where  a.basketidx in(".$v .") order by a.date desc";
//		where a.id='".$_ShopInfo->memid."' and a.basketidx in(".$v.") and a.productcode ='".$k."' order by a.date desc , a.quantity desc";

		$resultVcount=pmysql_query($sqlVcount,get_db_conn());

	}else{	// 비회원

		$sqlVcount = "select distinct(a.*),c.option1 option1,c.productname productname, c.option_consumer option_consumer , c.option_price option_price  from tblbasket a
		LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
		LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
		where   a.basketidx in(".$v.") and a.productcode ='".$k."' order by a.date desc , a.quantity desc";
		//exdebug($sqlVcount);
		$resultVcount=pmysql_query($sqlVcount,get_db_conn());
	}    //exdebug($sqlVcount);
?>
<!--
	<colgroup><col style="width:auto"/><col style="width:20%"/><col style="width:15%"/><col style="width:15%"/></colgroup>
		<tr>
			<th class="ta_c">상품명</th>
			<th class="ta_c">판매가</th>
-->
			<!--<th class="ta_c">수량</th>-->
<!--			<th class="ta_c">총 수량</th>
			<th class="ta_c">총합</th>
		</tr>
-->
<?
if( $_ShopInfo->memid){
	$title_query = "select distinct(a.*),c.option1 option1,c,tinyimage as tinyimage, c.productname productname, c.productcode as productcode ,c.reserve as reserve, c.option_consumer option_consumer ,c.sellprice sellprice , c.option_price option_price , c.quantity quan
			from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$v .") order by a.date desc";
//	where a.id='".$_ShopInfo->memid."' and a.basketidx in(".$v.") and a.productcode ='".$k."'  order by a.date desc , a.quantity desc";
	
}else{	// 비회원
	$title_query = "select distinct(a.*),c.option1 option1,c,tinyimage as tinyimage, c.productname productname, c.productcode as productcode ,c.reserve as reserve, c.option_consumer option_consumer ,c.sellprice sellprice , c.option_price option_price , c.quantity quan
			from tblbasket a
	LEFT OUTER JOIN tblbasket b on  a.productcode = b.productcode
	LEFT OUTER JOIN tblproduct c on  a.productcode = c.productcode
	where  a.basketidx in(".$v.") and a.productcode ='".$k."'  order by a.date desc  , a.quantity desc";
} //exdebug($title_query);
	$resultVcountt_title = pmysql_query($title_query,get_db_conn());

	$productname ='';
	$p_cnt =0;
	$price =0;
	$sellprice =0;
	$option_price =0;	
	$c_cnt =0;
	$allprice =0;
	$all_cnt=0;

	$opt1_idx=0;
	while($row_title = pmysql_fetch_object($resultVcountt_title)){
		//exdebug($row_title->quantity);
			if( $_ShopInfo->memgroup != ""){
					$query= "SELECT a.* ,c.sellprice as group_sellprice,c.consumerprice as group_consumerprice 
									FROM tblproduct a
									LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode 
									LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price WHERE group_code = '".$_ShopInfo->memgroup."' ) c 
									ON a.productcode = c.productcode
									WHERE a.display = 'Y' and a.productcode ='".$row_title->productcode."'  
									order by a.vcnt desc ";

									//exdebug($query);
					$query=pmysql_query($query,get_db_conn());
					$query=pmysql_fetch_object($query);
					//exdebug($query->group_sellprice);
					if( $query->group_sellprice != ""){
						$row_title->sellprice=$query->group_sellprice;
					}
			}

		$productname = $row_title->productname;				// 상품명
		$sellprice =  $row_title->sellprice;				// 판매가 달러
		$option = explode(",",$row_title->option_price);	
		$p_cnt =	$row_title->quantity;					// 옵션수량		
		$option_price =  $option[$row_title->opt1_idx-1] ;	// 옵션 가격
		$all_cnt +=$p_cnt;

		$opt1_idx += $row_title->opt1_idx;		
		$allprice += $option_price*$p_cnt;	// 옵션 추가 가격 더함

				
		$temp_tinyimage .= $row_title->tinyimage."|";
		$temp_sellprice .= (exchageRate($row_title->sellprice)+ $option_price)."|";
		$temp_quantity .= $row_title->quantity."|";
		$temp_reserve	.= $row_title->reserve."|";
		$temp_productname .= $row_title->productname."|";
		$temp_productcode .= $row_title->productcode."|";
		$prcode .= "'".$row_title->productcode."',";
		/**
			echo $row_title->tinyimage."<br>";
			echo $row_title->sellprice."<br>";
			echo $p_cnt."<br>";
			echo $row_title->reserve."<br>";	
			echo $row_title->productname."<br>";
			echo $row_title->productcode."<br>";
		**/
	}		// while
		$allprice += exchageRate($sellprice*$all_cnt);	// 옵선 가격 + 상품 기본가격*수량
		//exdebug($allprice);
	$total_all_price +=$allprice;    
?> 

		<tr>
			<td style="text-align:center;"><?=$productname?> </td>
			<td style="text-align:center;"><?=number_format(exchageRate($sellprice))?> </td>
			<td style="text-align:center;"><?=$all_cnt?> </td>

			<td style="text-align:center;"><?=number_format($allprice)?>			<!-- 총합 -->
				
			</td>
			<!--<td style="text-align:center;"><?=number_format(exchageRate($price))?> </td>-->
		</tr>	
		<!-- 옵션이 있는지의 여부 확인-->


		<?if($opt1_idx != 0  ){?>
		<!--	<tr>			
			<th colspan="2" class="ta_c">옵션</th>
			<th class="ta_c">옵션 가격</th>
			<th class="ta_c">수량</th>
			</tr>
		-->

		<?	// 옵션 부분
			//while($row=pmysql_fetch_object($resultVcount)){		
				//exdebug($row);
				
		?>
					<!--<td style="text-align:center;"><?=$row->productname?> </td>-->							
<!--			<tr>

					<td colspan="1" class="add_price" style="text-align:center;">
						<?if($row->option1){  
							$option = explode(",",$row->option1);
							echo $option[$row->opt1_idx];
							} 
						?>
					</td>   

					<td class="ta_c">
						<?if($row->option1){  
							$option = explode(",",$row->option_price); //exdebug($row->opt1_idx);
								if($row->opt1_idx >= 1){
									echo  number_format($option[$row->opt1_idx-1]);
								}else{
									echo  "0";
								}
							} 
						?>
					</td>
					<td style="text-align:center;"><?=$row->quantity?></td>
				</tr>	-->		



<?

			//}	// while
			}	// if
} // foreach


	if(   $_ShopInfo->memid !=""  ){	
		$sql  = "INSERT INTO tbl_estimate_sheet(id,tinyimage,sellprice,quantity,reserve,productname,productcode,date,basketidx)    VALUES ('". $_ShopInfo->memid."','".$temp_tinyimage."','".$temp_sellprice."','".$temp_quantity."','".$temp_reserve."','".$temp_productname."','".$temp_productcode."',now(),'".$_POST['strBasket']."'  )";

		pmysql_query($sql,get_db_conn());
	}	
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
			<? if( $_ShopInfo->memid){?>
				<!--<a href="javascript:go_print(0)"><img src="../images/common/estimate/icon_print.gif" border="0"></a>
				<a href="javascript:nego('<?=$_ShopInfo->memid?>','<?=$temp_productcode?>','<?=$temp_quantity?>')"> 네고하기 </a>// 수정 -->
				<a href="javascript:go_print(0)" class=btn_A> 인쇄하기 </a>
				<!--<a href="javascript:nego(0)" class=btn_D> 수정하기 </a> -->
			<? }else{ // 비회원?>
				<!--<a href="javascript:go_print(1)"><img src="../images/common/estimate/icon_print.gif" border="0"></a>	
				<a href="javascript:nego('<?=$_ShopInfo->memid?>','<?=$temp_productcode?>','<?=$temp_quantity?>')"> 네고하기 </a>-->
				<a href="javascript:go_print(1)" class=btn_A> 인쇄하기 </a>
				<!--<a href="javascript:nego(1)" class=btn_D> 수정하기 </a> -->
			<? } ?>
				<!--<a href="javascript:window.close();"><img src="../images/common/estimate/icon_close.gif" border="0"></a>-->
				<a href="javascript:window.close();" class=btn_B>화면닫기</a>
			</td>
		</tr>
	</table>
</div>

<input type=hidden id="userid"			 value='<?=$_ShopInfo->memid?>' ></input>
<input type=hidden id="temp_tinyimage"	 value='<?=$temp_tinyimage?>' ></input>
<input type=hidden id="temp_sellprice"	 value='<?=$temp_sellprice?>' ></input>
<input type=hidden id="temp_quantity"	 value='<?=$temp_quantity?>' ></input>
<input type=hidden id="temp_reserve"	 value='<?=$temp_reserve?>' ></input>
<input type=hidden id="temp_productname" value='<?=$temp_productname?>' ></input>
<input type=hidden id="temp_productcode" value='<?=$temp_productcode?>' ></input>
<input type=hidden id="temp_basketidx" value='<?=$temp?>' ></input>

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
	var ajax_price=	document.getElementById("ajax_price");	
	if(num==0){
		if(company_name.value==""||company_name.value==null){
				alert("회사명을 입력해주세요.\n개인이면 개인이라고 입력해주세요");
				company_name.focus();
			}else
			if(person.value==""||person.value==null){
				alert("담당자를 입력해주세요.");
				person.focus();
			}else{
				$.post("ajax_memo.php",
					{ price:price , 
					id:userid,
					tinyimage:tinyimage,
					sellprice:sellprice,
					quantity:quantity,
					reserve:reserve,
					productname:productname,
					productcode:productcode,
					basketidx:basketidx
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
	var telnum=	document.getElementById("tel");
	var ajax_price=	document.getElementById("ajax_price");

		if( telnum.value=="" || telnum.value==null ){
			alert("전화번호를 입력해주세요.");
			telnum.focus();
		}else{
			$.post("ajax_memo.php",
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
				basketidx:basketidx
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
		
} // nego function end

	function go_print(num){
		/*var company_name=document.getElementById("company_name");
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
			};	
		}


		if( num ==1 ){
			if( num == 1 && (tel.value==""||tel.value==null) ){
				alert("전화번호를 입력해주세요.");
				tel.focus();
			}else{
				print();
			};
		}*/
		
		print();
					
	};
</script>	

</html>