<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("calendar.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//exdebug($_GET);

// 선택된 상품 삭제
if($_GET['type'] == "delete") {
    $sql = "Delete From tblbatchapplyrate_log Where rno = ".$_GET['rno']."";
    pmysql_query($sql,get_db_conn());
    
    msg("선택 상품이 삭제되었습니다.");
}

if($_GET['type'] == "all_delete") {
    if($_GET["selectChk"]){
		$selectChk = substr($_GET["selectChk"],0,strlen($_GET["selectChk"])-1);
	}
	//exdebug($selectChk);

    $sql = "Delete From tblbatchapplyrate_log Where rno in (".$selectChk.") ";
    pmysql_query($sql,get_db_conn());
    
    msg("선택 상품이 삭제되었습니다.");
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색

$selected[s_prod][$s_prod]      = 'selected';

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

// 기본 검색 조건
$qry_from = "tblbatchapplyrate_log a ";
$qry_from.= " left join tblproduct b on a.productcode = b.productcode ";
$qry.= "WHERE 1=1 ";
$qry.= "AND a.ridx > 0 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND a.date >='{$search_s}' AND a.date <='{$search_e}' ";
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(b.productname) like upper('%{$search_prod}%') ";
    else if($s_prod=="pc") $qry.= "AND upper(a.productcode) like upper('%{$search_prod}%') ";
    //else if($s_prod=="sc") $qry.= "AND upper(b.selfcode) like upper('%{$search_prod}%') ";
}

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " left join tblproductbrand v on b.vender = v.vender ".$subqry."";
} else {
    $qry_from.= " left join tblproductbrand v on b.vender = v.vender ";
}

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
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

$sql = "SELECT COUNT(*) as t_count FROM (SELECT a.rno FROM {$qry_from} {$qry} ) a ";
//exdebug($sql);
//echo "sql = ".$sql."<br>";
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="/js/jquery.js"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn	= $(this).attr('chk');
		 if($(this).prop("checked")){
			$("."+chk_cn).attr("checked", true);
		 } else {
			$("."+chk_cn).attr("checked", false);
		 }
	});

});

function searchForm() {
	//document.form1.action="order_list_all_order.php";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	
    if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    }else{
	    pForm.search_start.value = '';
	    pForm.search_end.value = '';
    }
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function ProductDel(rno) {
    alert(rno);
	if(confirm("해당 상품을 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.rno.value=rno;
		document.idxform.submit();
	}
}

function allDelete(){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		var selectCheck = "";
		if($("input[name=chkordercode]:checked").length<1){
			alert("삭제하실 상품을 선택해 주세요.");
            return;
		}
		$("input[name=chkordercode]:checked").each(function(){
			selectCheck += $(this).val()+",";
		});
		$("#selectChk").val(selectCheck);
		document.idxform.type.value="all_delete";
		document.idxform.submit();
	}
}
</script>

			<table cellpadding="5" cellspacing="0" width="100%">		
			<tr>
				<td>
					<div class="title_depth3_sub">기간설정 현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>등록일</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</TR>

						<tr>
							<th><span>상품</span></th>
							<TD class="td_con1">
                                <select name="s_prod" class="select">
                                    <option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
                                    <option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
                                    <!-- <option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option> -->
                                </select>
							    <input type=text name=search_prod value="<?=$search_prod?>" style="width:197" class="input">
                            </TD>
						</tr>
<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>브랜드</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                            foreach($venderlist as $key => $val) {
                                echo "<option value=\"{$val->vender}\"";
                                if($sel_vender==$val->vender) echo " selected";
                                echo ">{$val->brandname}</option>\n";
                            }
?>
                                </select> 
                                <input type=text name=brandname value="<?=$brandname?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="10"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = "SELECT  a.*, b.productname, v.brandname 
                FROM {$qry_from} {$qry} 
		        ORDER BY a.ridx desc, a.rno desc 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=10;
		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"></td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<!-- <col width=40></col>
				<col width=40></col>
				<col width=80></col>
				<col width=80></col>
				<col width=150></col>
				<col width=></col>
                <col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=100></col>
				<col width=70></col> -->
				<input type=hidden name=chkordercode>
			
				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>번호</th>
					<th>브랜드</th>
					<th>상품코드</th>
					<th>상품명</th>
					<th>이전 마진율</th>
                    <th>적용 마진율</th>
					<th>등록일</th>
					<th>기간설정</th>
					<th>비고</th>
				</TR>

<?php
		$colspan=12;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			if($thisordcd!=$row->ridx) {
				$thisordcd=$row->ridx;
				if($thiscolor=="#FFFFFF") {
					//$thiscolor="#FEF8ED";
                    $thiscolor="#ffeeff";
				} else {
					$thiscolor="#FFFFFF";
				}
			}
            $productname = strcutMbDot(strip_tags($row->productname), 25);
            $date = substr($row->date, 0, 4)."-".substr($row->date, 4, 2)."-".substr($row->date, 6, 2);
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <td align="center">
                        <input type=checkbox name=chkordercode value="<?=$row->rno?>"><br>
                    </td>
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$row->brandname?></td>
                    <td align="center"><?=$row->productcode?></td>
			        <td style='text-align:left'><?=$productname?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->old_rate)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->new_rate)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align="center"><?=$date?></td>
                    <td align="center"><?=$row->start_date." - ".$row->end_date?></td>
                    <td align="center">
<?
            if($row->start_date > date("Ymd")) {
?>
                        <a href="javascript:ProductDel('<?=$row->rno?>')"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a>
<?
            } else {
?>
                        - 
<?
            }
?>
                    </td>
<?


			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><a href="javascript:allDelete();"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			<tr>
				<td>
                    <div id="page_navi01" style="height:'40px'">
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">

			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=rno>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_prod value="<?=$s_prod?>"> 
			<input type=hidden name=search_prod value="<?=$search_prod?>"> 
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
            <input type="hidden" name="selectChk" id="selectChk"/>
			</form>

            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

			</table>
<?=$onload?>
</body>
</html>