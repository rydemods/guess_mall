<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");


####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


$faq_title=$_POST[faq_title];
$faq_type=$_POST[faq_type];
$faq_best=$_POST[faq_best];
$listnum    = $_POST["listnum"] ?: "20";


$selected[$faq_type]="selected";
$checked[faq_best][$faq_best]="checked";

if($faq_title) {
	$search = trim($faq_title);
	$temp_search = explode("\r\n", $search);
	$cnt = count($temp_search);
	
	$search_arr = array();
	for($i = 0 ; $i < $cnt ; $i++){
		array_push($search_arr, "'%".$temp_search[$i]."%'");
	}
	
	$where[]="faq_title LIKE any ( array[".implode(",", $search_arr)."] )";
}
if($faq_type!='') $where[]="faq_type='".$faq_type."'";
if($faq_best) $where[]="faq_best='".$faq_best."'";
//운영자 레피시 불러오기
$sql0 = "select COUNT(*) as t_count from tblfaq ";
if(count($where))$sql0.=" where ".implode(" and ",$where);
$paging = new newPaging($sql0,10,$listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$query="select * from tblfaq ";
if(count($where))$query.=" where ".implode(" and ",$where);
$query.=" order by sort ASC, no ASC";
$query = $paging->getSql($query);
$result=pmysql_query($query);


##카테고리 쿼리
$cate_qry="select * from tblfaqcategory order by sort_num";
$cate_result=pmysql_query($cate_qry);

include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function faq_del(no){
	if(confirm("해당 질문을 삭제 하시겠습니까?")){
		document.form2.no.value=no;
		document.form2.mode.value="faq_del";
		document.form2.submit();	
	}

}

function faq_mod(no){
	document.form3.no.value=no;
	document.form3.mode.value="faq_mod";
	document.form3.submit();
}

function go_search(sort) {
	document.form1.mode.value = "";
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function faq_apply() {
	document.form1.mode.value = "faq_apply";
	document.form1.action = "faq_indb.php";
	document.form1.submit();
}

function listnumSet(listnum){
	document.form1.listnum.value=listnum.value;
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>FAQ 리스트 관리</span></p></div></div>
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
			<?php include("menu_cscenter.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">FAQ 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>운영자가 등록한 FAQ를 변경 및 삭제처리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">			
			<input type=hidden name=listnum value="<?=$listnum?>">
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">FAQ 검색</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=500></col>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>질문 검색</span></th>
					<TD>
						<!--  
						<INPUT maxLength=200 size=50 name=faq_title class="input_selected" value="<?=$faq_title?>">
						-->
						<textarea rows="2" cols="10" class="w200" name="faq_title" id="search" style="resize:none;vertical-align:middle;"><?=$faq_title?></textarea>
					</TD>
					
					<th><span>카테고리 검색</span></th>
					<td>
						<select name="faq_type" style="width:200px;height:32px;vertical-align:middle;">
							<option value="" <?=$selected['']?>>카테고리를 선택하여주십시요.</option>
<?while($cate_row=pmysql_fetch_object($cate_result)){
	$faq_type[$cate_row->num]=$cate_row->faq_category_name;
	?>
							
							<option value="<?=$cate_row->num?>" <?=$selected[$cate_row->num]?>><?=$cate_row->faq_category_name?></option>
							
<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<th><span>BEST 질문</span></th>
					<td>
						<input type="radio" name="faq_best" value="" <?=$checked[faq_best]['']?>>전체	
						<input type="radio" name="faq_best" value="Y" <?=$checked[faq_best][Y]?>>BEST
						<input type="radio" name="faq_best" value="N" <?=$checked[faq_best][N]?>>일반
					</td>		
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center><a href="javascript:go_search();"><img src="images/btn_search01.gif"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
                    <div class="btn_right">
                        <select name="listnum_select" onchange="javascript:listnumSet(this)">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                        </select>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=30></col>
				<col width=50></col>
				<col width=100></col>
				<col width=></col>
				<col width=100></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>No</th>
					<th>Best</th>
					<th>순서</th>
					<th>질문유형</th>
					<th>질문</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
					
				</TR>
<?php
				$cnt=0;
				while($data=pmysql_fetch_object($result)){
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					//$catename=pmysql_fetch_array(pmysql_query("select * from tbl_recipe_category where no={$data->category_no}"));
					$regdt = substr($data->date,0,4)."-".substr($data->date,4,2)."-".substr($data->date,6,2);
					
					
					$checked="";
					if($data->faq_best=="Y")$checked="checked";
					echo "<input type=\"hidden\" name=\"n_best[]\" value=\"{$data->no}\">";
					echo "<tr>";
					echo "<td>{$number}</td>";
					echo "<td><input type=\"checkbox\" value=\"{$data->no}\" name=\"best_check[]\" {$checked}></td>";
					echo "<td><input type='text' name='n_sort[]' size=5 value='{$data->sort}' style='text-align:center;'></td>";
					echo "<td>".$faq_type[$data->faq_type]."</td>";
					echo "<td>{$data->faq_title}</td>";					
					echo "<td>{$regdt}</td>";
					echo "<td><a href=\"javascript:faq_mod('{$data->no}')\"><img src=\"images/btn_edit.gif\"></a></td>";
					echo "<td><a href=\"javascript:faq_del('{$data->no}')\"><img src=\"images/btn_del.gif\"></a></td>";
					echo "</tr>";
					
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan=\"8\" align=center>등록된 게시판이 존재하지 않습니다.</TD></TR>";
				}
?>				

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=170></col>
				<col width=></col>
				<col width=170></col>
				<tr>
					<td align='left'><a href="javascript:faq_apply();"><img src="images/btn_modify_com.gif"></a></td>
					<td align='center'>
			<?
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
			?></td>
					<td align='right'><a href="faq_register.php"><img src="/admin/images/btn_confirm_com.gif"  border="0"></a></td>
				<tr>
				</table>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>FAQ 리스트 관리</span></dt>
							<dd>- <br>
							- <br>
							- 
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name=form2 method=post action="faq_indb.php">
			<input type=hidden name=no>
			<input type=hidden name=mode>

			</form>
			<form name=form3 method=post action="faq_register.php">
			<input type=hidden name=no>
			<input type=hidden name=mode>

			</form>
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
