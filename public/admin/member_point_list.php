<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-3";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

#exdebug($_POST);
#exdebug($_GET);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$s_date     = $_GET["s_date"];

$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];


$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';

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
$qry_from = "tblpoint a ";
$qry_from.= "JOIN 	tblmember b on a.mem_id = b.id ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND a.regdt >= '{$search_s}' AND a.regdt <= '{$search_e}' ";
}

// 검색어
if(ord($search)) {
	if($s_check=="id") $qry.= "AND a.mem_id = '{$search}' ";
    else if($s_check=="name") $qry.= "AND b.name like '%{$search}%' ";
}


include("header.php"); 

$listnum    = $_GET["listnum"] ?: "20";

$sql = "SELECT COUNT(a.mem_id) as t_count FROM {$qry_from} {$qry} ";
$paging = new newPaging($sql,10,$listnum,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	//document.form1.action = "<?=$_SERVER['PHP_SELF']?>";
    document.form1.listnum.value = document.form2.listnum.value;
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


function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function DownloadExcel() {
    //alert("excel");
	document.form1.action="member_point_list_excel.php";
    document.form1.method="POST";
    //document.form1.target="_blank";
	document.form1.submit();
	document.form1.action="";
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원관리 부가기능 &gt;<span>통합포인트 지급 리스트</span></p></div></div>

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
			<?php include("menu_member.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">통합포인트 지급 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>통합포인트 지급 내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">통합포인트 현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=mode>
            <input type=hidden name=ordercodes>
            <input type=hidden name=listnum>

			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
                                <select name="s_check" class="select">
                                    <option value="id" <?=$selected[s_check]["id"]?>>회원ID</option>
                                    <option value="name" <?=$selected[s_check]["name"]?>>회원명</option>
                                </select>
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                            </TD>
						</tr>

						<TR>
							<th><span>기간선택</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</TR>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right">
                    <a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:DownloadExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a>
                </td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = "SELECT  a.pid, a.mem_id, b.name, a.regdt, a.body, a.point, a.expire_date, a.tot_point 
                FROM {$qry_from} {$qry} 
		        ORDER BY a.pid DESC 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=10;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right">
                        <img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지
                        <select name="listnum" onchange="javascript:searchForm();">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                            <option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
                        </select>
                    </td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<!-- <col width=40></col> -->
				<col width=80></col>
				<col width=150></col>
				<col width=80></col>
				<col width=100></col>
				<col width=></col>
                <col width=90></col>
				<col width=90></col>
				<!-- <input type=hidden name=chkordercode> -->
			
				<TR >
					<!-- <th><input type=checkbox name=allcheck onclick="CheckAll()"></th> -->
					<th>번호</th>
					<th>처리일자</th>
					<th>회원ID</th>
					<th>회원명</th>
					<th>상세내역</th>
                    <th>적립포인트</th>
					<th>사용포인트</th>
				</TR>

<?php
		$colspan=10;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$regdt = substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";
			$expiredt = substr($row->expire_date,0,4)."/".substr($row->expire_date,4,2)."/".substr($row->expire_date,6,2);

            $reserve_point = 0;
            $use_point = 0;
            $tot_point = $row->tot_point;
            if($row->point < 0) $use_point = $row->point;
            else $reserve_point = $row->point;
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <!-- <td align="center">
                        <input type=checkbox name=chkordercode value="<?=$row->pid?>"><br>
                    </td> -->
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$regdt?></td>
                    <td align="center"><A HREF="javascript:CrmView('<?=$row->mem_id?>');"><font color="blue"><?=$row->mem_id?></font></A></td>
			        <td align="center"><?=$row->name?></td>
                    <td align="center"><?=$row->body?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($reserve_point)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($use_point)?>&nbsp;&nbsp;&nbsp;</td>
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
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
            <input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=s_date value="<?=$s_date?>">
            <input type=hidden name=listnum value="<?=$listnum?>">
			</form>

            <form name=crmview method="post" action="crm_view.php">
            <input type=hidden name=id>
            </form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>통합포인트 지급 리스트</span></dt>
							<dd>
							</dd>
						</dl>
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
?>
