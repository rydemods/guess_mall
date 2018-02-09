<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"]; 
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
//$onedelete = $_POST['onedelete'];
$no = $_POST['no'];
$type = $_POST['type'];
$estimate_no = $_POST['estimate_no'];
$estimate_price = $_POST['estimate_price'];

if($estimate_no && $estimate_price){
	$query = "update tbl_estimate_sheet  set  estimate_price ='".$estimate_price."'  where 
				no='".$estimate_no."' ";
	pmysql_query($query);
}

if($search_start ){
	$search_start=$search_start?$search_start:$period[0];
	$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
	$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
	$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
}else{
	$search_start = date("Y-m-d",time() );
	$search_end = date("Y-m-d",time() );
}


//exdebug($search_start);
//exdebug($search_end);
$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>31) {
	alert_go('견적서 조회 기간은 1달을 초과할 수 없습니다.');
}

if(ord($search)) {	
	
}

if($type=="delete" ) {	//주문서 삭제	
	$delete="delete from tbl_estimate_sheet where no=".$no ;
	pmysql_query($delete, get_db_conn());
 
}

include("header.php"); 
//$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM {$qry_from} {$qry} ";
// $s_check-타입 $search 검색어
$sql = "select count(*) from tbl_estimate_sheet";
if($search){
	if($s_check == mn ){	// 구매자 id
		$sql .= "  where id LIKE ('%".$search."%')";	
	}
	if($s_check == mi ){	// 상품
		$sql .= "  where productname like ('%".$search."%')";
	}	
	$sql .= " and date >='".$search_start."'" ;
}else{
$sql .= "  where date >= '".$search_start."'" ;
}


//exdebug($s_check);
//exdebug($sql);
// $search_start  $search_end

$paging = new Paging($sql,10,10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//echo $gotopage;
?>

<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
<link rel="stylesheet" href="../css/digiatom.css" />
<script type="text/javascript" src="lib.js.php"></script>
<!--<script language="JavaScript">-->
<script type="text/javascript">
//////////////////////
//function estimate(num,no){
function estimate(no){
	if( no ){	
		var new_win=window.open("about:blank","test_pop","scrollbars=yes,width=800,height=600,resizable=yes");
		document.estimate_sheet_form.target="test_pop";
//		document.estimate_sheet_form.strBasket.value =num;
		document.estimate_sheet_form.strno.value = no;
		document.estimate_sheet_form.action="estimate_sheet_form1.php";
		document.estimate_sheet_form.submit();
	}else{

	}
}
/////////////
function MemberInfo(id) {
	if(id=='notuser'){
		alert("비회원입니다");
	}else{
	window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
	document.form3.target="infopop";
	document.form3.id.value=id;
	document.form3.action="member_infopop.php";
	document.form3.submit();
	}
}
/////////////////
function change_estimate_price(no){
	document.form4.estimate_no.value	= no;
	document.form4.estimate_price.value = document.getElementById(no).value;
	document.form4.submit();
}

function searchForm() {
	//document.form1.action="order_delisearch.php";
	document.form1.submit();
}
/*
function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}
*/
function OnChangePeriod(val) {
	
	var pForm = document.form1;
	var period = new Array(7);
	 period[0] = "<?=$period[0]?>";
	 period[1] = "<?=$period[1]?>";
	 period[2] = "<?=$period[2]?>";
	 period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

var clickno=0;
function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
function onedelete(no){
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.no.value=no;
		document.idxform.submit();
	}
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}


function OrderDelete(ordercode) {
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}


function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}

function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	alert(document.checkexcelform.ordercodes.value);
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel_estimate_sheet.php";
	document.checkexcelform.submit();
}

function OrderSendSMS() {
	document.smsform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.smsform.ordercodes.value+="'"+document.form2.chkordercode[i].value.substring(1)+"',";
		}
	}
	if(document.smsform.ordercodes.value.length==0) {
		alert("SMS를 발송할 주문서를 선택하세요.");
		return;
	}
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.type.value="order";
	document.smsform.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>견적서 조회</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">견적서 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>견적서 조회를 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">견적서 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td>					
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick='OnChangePeriod(0)' />
								<img src=images/btn_day07.gif	border=0 align=absmiddle style="cursor:hand" onclick='OnChangePeriod(1)' />
								<img src=images/btn_day14.gif	border=0 align=absmiddle style="cursor:hand" onclick='OnChangePeriod(2)' />
								<img src=images/btn_day30.gif	border=0 align=absmiddle style="cursor:hand" onclick='OnChangePeriod(3)' />
							</td>
						</TR>

						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
							<select name="s_check" class="select">
								<!--option value="pn" <?php if($s_check=="pn")echo"selected";?>>상품명</option-->
								<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자 ID</option>
								<option value="mi" <?php if($s_check=="mi")echo"selected";?>>상품</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
						
						</TABLE>
						</div>
						</td>
					</tr>
					</table>
					
				</td>
				
			</tr>
			
			
			
			<tr>
				<td style="padding-top:4pt;" align="right">
				<!--
				<a href="javascript:OrderCheckExcel();">
				<img src="images/btn_excel1.gif" border="0" hspace="1">
				</a> // 액셀 미구현-->
				<a href="javascript:searchForm();">
				<img src="images/botteon_search.gif"  border="0"></a>
				
				</td>
			</tr>
			</form>


			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		

		$sql = "SELECT * FROM tbl_estimate_sheet ";
		if($search){
			if($s_check == mn ){	// 구매자 id
				$sql .= "  where id LIKE ('%".$search."%')";	
			}
			if($s_check == mi ){	// 상품
				$sql .= "  where productname like ('%".$search."%')";
			}	
				$sql .= " and date >='".$search_start."'" ;
			}else{
			$sql .= "  where date >= '".$search_start."'" ;
		}
		$sql .= "  order by no desc LIMIT 10 offset ".(($gotopage-1)*10);
		//$sql = $paging->getSql($sql);
		//exdebug($sql);

		$result=pmysql_query($sql,get_db_conn());
		
		$colspan=10;
		//if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :					
					<!-- <A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>신청일자순↑</FONT></B></A> -->
					<A HREF="javascript:void();"><B><FONT class=font_orange>신청일자순↑</FONT></B></A>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col style='auot'></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<!--<input type=hidden name=chkordercode>-->
			
				<TR >
					<!--<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>-->
					
					<th>신청자</th>
					<th>신청일</th>
					<th>이미지</th>
					<th>상품명</th>
					<th>적립금</th>
					<!--<th>달러</th>-->
					<th>판매가</th>
					<th>수량</th>
					<th>합계</th>
					<th>삭제</th>
				</TR>

<?php
		//$colspan=10;
		//if($vendercnt>0) $colspan++;

		//$curdate = date("YmdHi",strtotime('-2 hour'));
		//$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		//$thisordcd="";
		$thiscolor="#FFFFFF";
	//exdebug(pmysql_fetch_array($result));
	while($row=pmysql_fetch_object($result)) {
//exdebug($row);

	$rowspan =count(explode("|",$row->productname));
	$rowspan = $rowspan-1;
	$tinyimage	=	explode("|",$row->tinyimage);
	$productname =	explode("|",$row->productname);
	$reserve	=	explode("|",$row->reserve);
	$sellprice	=	explode("|",$row->sellprice);
	$quantity	=	explode("|",$row->quantity);
	$allprice = 0;
	$reserveprice = 0;
	$cnt1 = count($row);
	$cnt = $cnt+1;
 
 	
			echo "<tr bgcolor={$thiscolor} onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='{$thiscolor}'\">\n";
	 
// <A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}</A>
				//echo "	<td rowspan=\"{$rowspan}\" align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->no}')\">".$row->id."</A></td>\n";
				if( $row->id ){    //exdebug($row);
					//echo "	<td rowspan=\"{$rowspan}\" align=\"center\"><A HREF=\"javascript:MemberInfo('".rtrim($row->id)."')\">".$row->id."</A></td>\n";
					echo "	<td rowspan=\"{$rowspan}\" align=\"center\">
					<A HREF=\"javascript:MemberInfo('".rtrim($row->id)."')\">".$row->id."</A>
					<p style='margin-top:5px;'><a href=javascript:estimate('".$row->no."'); class='estimate_sheet' >
					<img src=../images/btn_estimate.gif></img></a>
						
					</td>\n";
							//exdebug($row->basketidx);
				}else{
					//echo "	<td rowspan=\"{$rowspan}\" align=\"center\"><A HREF=\"javascript:MemberInfo('notuser')\">"."비회원(".$row->tel.")</A></td>\n";
					echo "	<td rowspan=\"{$rowspan}\" align=\"center\">
					<A HREF=\"javascript:MemberInfo('notuser')\">"."비회원(".$row->tel.")</A>
					<p style='margin-top:5px;'><a href=javascript:estimate('".$row->no."');  class='estimate_sheet'  >
					<img src=../images/btn_estimate.gif></img>	</a>
					</td>\n";
						//		<a href="javascript:;"  class="estimate_sheet  btn_B wide">견적서 출력</a>
				}

				echo "	<td align=\"center\">".$row->date."</td>\n";
				
				if( file_exists($Dir.DataDir."shopimages/product/".$tinyimage[0])  ){			
					echo "<td><img width=50; height=50; src='".$Dir.DataDir."shopimages/product/".$tinyimage[0]."'></img></td>\n";
				}else if( file_exists($Dir.$tinyimage[0])  ){
					echo "<td><img width=50; height=50; src=".$Dir.$tinyimage[0]."></img></td>\n";
				}else{
					echo "<td><img width=50; height=50; src=".$Dir."images/no_img.gif></img></td>\n";
				}

				//echo "	<td align=\"center\">".$tinyimage[0]."</td>\n";
				echo "	<td align=\"center\">".$productname[0]."</td>\n";
				echo "	<td align=\"center\">".$reserve[0]." %</td>\n";
				echo "	<td align=\"center\">".number_format($sellprice[0])." 원</td>\n";
				echo "	<td align=\"center\">".$quantity[0]."</td>\n";
				echo "	<td align=\"center\">".number_format($sellprice[0]*$quantity[0])." 원</td>\n";
				echo "<td rowspan=\"{$rowspan}\" align=\"center\">
							<a href=javascript:onedelete(".$row->no.");>
								<img src=images/btn_cate_del.gif  border=0>				
							</a></td></tr>";
					$allprice = 0;
					$reserveprice =0;
					$allprice +=  $sellprice[0]*$quantity[0];
					$reserveprice += intval($reserve[0]*$sellprice[0]*$quantity[0]/100);

				for($i=1 ; $i<$rowspan ; $i++){		
					echo "	<tr>";
					echo "	<td align=\"center\">".$row->date."</td>\n";
					if( file_exists($Dir.DataDir."shopimages/product/".$tinyimage[$i])  ){			
						echo "<td><img width=50; height=50; src='".$Dir.DataDir."shopimages/product/".$tinyimage[$i]."'></img></td>\n";
					}else if( file_exists($Dir.$tinyimage[$i])  ){
						echo "<td><img width=50; height=50; src=".$Dir.$tinyimage[$i]."></img></td>\n";
					}else{
						echo "<td><img width=50; height=50; src=".$Dir."images/no_img.gif></img></td>\n";
					}
					//echo "	<tr><td align=\"center\">".$tinyimage[$i]."</td>\n";
					echo "	<td align=\"center\">".$productname[$i]."</td>\n";
					echo "	<td align=\"center\">".$reserve[$i]." %</td>\n";					
					echo "	<td align=\"center\">".number_format($sellprice[$i])." 원</td>\n";
					echo "	<td align=\"center\">".$quantity[$i]."</td>\n";
					echo "	<td align=\"center\">".number_format($sellprice[$i]*$quantity[$i])." 원</td></tr>\n";

					
					$allprice +=  $sellprice[$i]*$quantity[$i];

					$reserveprice += intval($reserve[$i]*$sellprice[$i]*$quantity[$i]/100);
				}	// for

				echo "	<tr style='background:#f0f0f0' ><td colspan=2 align=\"center\"><strong>"."총 적립금"."</strong></td>
				<td colspan=1 align=\"center\"><strong>".number_format($reserveprice)."  원</strong></td>
				<td colspan=3 align=\"center\"><strong>"."흥정하기"."</strong>
					<input type=text id='".$row->no."'  value='".$row->estimate_price."' />
					<img src=../admin/images/btn_edit.gif  onclick=change_estimate_price(".$row->no.") ><img>
				</td>
				<td colspan=1 align=\"center\"><strong>"."총합"."<strong></td>

				<td colspan=2 align=\"center\"><strong>".number_format($allprice)."  원<strong></td>
				<td colspan=1 align=\"center\"></td>
						
				</tr>";
			//echo "	<td align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}</A></td>\n";
				echo "</tr>\n";

				
				if($cnt1==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
				}	// if
				
	}	//while
	pmysql_free_result($result);
	//exdebug($row);
		
?>
				</TABLE>
				</div>
				</td>
			</tr>
			
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php	/**	페이징... 	**/
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n"; 	
?>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<form name=detailform method="post" action="order_detail_estimate_sheet.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>
			<!--견적서 출력-->
			<form name=estimate_sheet_form  method=post>
				<input type=hidden name=strBasket></input>
				<input type=hidden name=strno></input>
				<input type=hidden name=rowid value=<?=$_ShopInfo->memname?>></input>
			</form>
<!---->
			<form name=form3 method=post>	 <!-- 회원 id 클릭-->
				<input type=hidden name=id>
			</form>
			
			<form name=form4 method=post action="<?=$_SERVER['PHP_SELF']?>">	 <!-- 네고 수정-->
				<input type=hidden name=estimate_price />
				<input type=hidden name=estimate_no />
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type />
			<input type=hidden name=ordercodes value=''/>
			<input type=hidden name=no value=''/>
			<input type=hidden name=block value="<?=$block?>"/>
			<input type=hidden name=gotopage value="<?=$gotopage?>"/>
			<input type=hidden name=orderby value="<?=$orderby?>"/>
			<input type=hidden name=s_check value="<?=$s_check?>"/>
			<input type=hidden name=search value="<?=$search?>"/>
			<input type=hidden name=search_start value="<?=$search_start?>"/>
			<input type=hidden name=search_end value="<?=$search_end?>"/>
			<input type=hidden name=paymethod value="<?=$paymethod?>"/>
			<input type=hidden name=paystate value="<?=$paystate?>"/>
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>"/>
			<input type=hidden name=s_date value="<?=$s_date?>"/>
			</form>
			
			<form name=checkexcelform action="order_excel_estimate_sheet.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
