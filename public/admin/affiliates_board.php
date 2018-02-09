<?php
/********************************************************************* 
// 파 일 명		: affiliates.php 
// 설     명		: 제휴 학교/회사 등록,수정,삭제, 리스트
// 상세설명	: 관리자 제휴 학교/회사 등록,수정,삭제 및 리스트를 보여줌
// 작 성 자		: 2015.10.26 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "me-4";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
$search=array(
	'af_use'=>(string)$_POST['af_use'], 
	'af_type'=>(string)$_POST['af_type'], 
	'af_area'=>(string)$_POST['af_area'], 
	'af_name'=>(string)$_POST['af_name'], 
);
	$af_use		= $_POST[af_use];
	$af_type	= $_POST[af_type];
	$af_area	= $_POST[af_area];
	$af_name	= $_POST[af_name];
	$checked[af_use][$af_use]="checked";
	$checked[af_type][$af_type]="checked";
	$selected[$af_area]="selected";
	if($af_use != '') $where[]="use='".$af_use."'";
	if($af_type != '') $where[]="type='".$af_type."'";
	if($af_area != '') $where[]="area='".$af_area."'";
	if($af_name != '') $where[]="name like '%".$af_name."%'";

#---------------------------------------------------------------
# 제휴 학교/회사 리스트를 불러온다.
#---------------------------------------------------------------
	$sql0 = "select COUNT(*) as t_count from tblaffiliatesinfo ";
	if(count($where))$sql0.=" where ".implode(" and ",$where);
	$paging = new newPaging($sql0,10,15,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$query="select * from tblaffiliatesinfo ";
	if(count($where))$query.=" where ".implode(" and ",$where);
	$query.=" order by idx DESC";
	$query = $paging->getSql($query);
	$result=pmysql_query($query);
    //echo $query;

#---------------------------------------------------------------
# 이미지 저장을 위한 기본정보를 설정한다.
#---------------------------------------------------------------
	$imagepath=$Dir.DataDir."shopimages/affiliates_logo/";

include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function affiliates_del(no){
	if(confirm("해당 게시물을 삭제 하시겠습니까?")){
		document.form2.no.value=no;
		document.form2.mode.value="affiliates_del";
		document.form2.submit();	
	}

}

function affiliates_mod(no){
	location.href="affiliates_register.php?mode=affiliates_mod&no="+no;
}

function go_search(sort) {
	document.form1.mode.value = "";
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function affiliates_use_output(){
	document.form1.mode.value="affiliates_use_output";
	document.form1.action="affiliates_indb.php";
	document.form1.submit();
}

function excel_download() {
	if(confirm("검색된 모든 정보를 다운로드 하시겠습니까?")) {
		document.excelform.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 제휴 학교/회사 관리 &gt;<span>제휴 학교/회사 관리</span></p></div></div>
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
					<div class="title_depth3">제휴 학교/회사 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>제휴 학교/회사 리스트를 변경 및 삭제처리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" name="listMode" id="listMode" value=""/>
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">		
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">제휴 학교/회사 검색</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col> 
				<tr>
					<th><span>사용</span></th>
					<td>
						<input type="radio" name="af_use" value="" <?=$checked[af_use]['']?>>전체	
						<input type="radio" name="af_use" value="1" <?=$checked[af_use]['1']?>>사용
						<input type="radio" name="af_use" value="0" <?=$checked[af_use]['0']?>>사용 안함
					</td>		
				</tr>
				<tr>
					<th><span>구분</span></th>
					<td>
						<input type="radio" name="af_type" value="" <?=$checked[af_type]['']?>>전체	
						<input type="radio" name="af_type" value="1" <?=$checked[af_type]['1']?>>학교
						<input type="radio" name="af_type" value="2" <?=$checked[af_type]['2']?>>기업
					</td>		
				</tr>
				<tr>
					<th><span>지역</span></th>
					<td>
					<select id="af_area" name="af_area">
						<option value="" <?=$selected['']?>>전체</option>
						<option value="서울/경기" <?=$selected['서울/경기']?>>서울/경기</option>
						<option value="경북/경남" <?=$selected['경북/경남']?>>경북/경남</option>
						<option value="전북/전남" <?=$selected['전북/전남']?>>전북/전남</option>
						<option value="충북/충남" <?=$selected['충북/충남']?>>충북/충남</option>
						<option value="강원도" <?=$selected['강원도']?>>강원도</option>
						<option value="제주" <?=$selected['제주']?>>제주</option>
						<option value="기타" <?=$selected['기타']?>>기타</option>
					</select>
					</td>
				</tr>
				<tr>
					<th><span>학교/회사명</span></th>
					<TD><INPUT maxLength=200 size=50 name=af_name class="input_selected" value="<?=$af_name?>"></TD>
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
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=50></col>
				<col width=50></col>
				<col width=70></col>
				<col width=180></col>
				<col width=></col>
				<col width=70></col>
				<col width=160></col>
				<col width=70></col>
				<col width=180></col>
				<col width=50></col>
				<col width=50></col>
				<col width=180></col>
				<col width=100></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>번호</th>
					<th>구분</th>
					<th>지역</th>
					<th>로고</th>
					<th>학교/기업명</th>
					<th>rfcode<br>(배너)</th>
					<th>배너 접속경로</th>
					<th>rfcode<br>(이메일)</th>
					<th>이메일 접속경로</th>
					<th>사용</th>
					<th>출력</th>
					<th>쿠폰</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
					
				</TR>
<?php
				$cnt=0;
				while($data=pmysql_fetch_object($result)){
					if ($data->type == '1')	$type	= "학교";
					if ($data->type == '2')	$type	= "기업";
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$regdt = substr($data->regdate,0,4)."-".substr($data->regdate,4,2)."-".substr($data->regdate,6,2);
					($data->use > 0) ? $checked[$data->idx]="checked":$checked[$data->idx]="";
					($data->output > 0) ? $checked2[$data->idx]="checked":$checked2[$data->idx]="";
					echo "<tr>";
					echo "<input type='hidden' name='num[]' value='{$data->idx}'>";
					echo "<td>{$number}</td>";
					echo "<td>{$type}</td>";
					echo "<td>{$data->area}</td>";
					if ( ord($data->logoimage) && file_exists($imagepath.$data->logoimage) ){
						echo "<td><img src='".$imagepath.$data->logoimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/affiliates_logo/{$data->logoimage}' style=\"height:15px\"></td>";
					} else {
						echo "<td>&nbsp;</td>";
					}
					echo "<td style='text-align:left;'>{$data->name}</td>";
					echo "<td>B{$data->idx}</td>";
					echo "<td style='text-align:left;'>{$data->referrer_url}</td>";
					echo "<td>E{$data->idx}</td>";
					echo "<td style='text-align:left;'>{$data->referrer_email_url}</td>";
					echo "<td><input type=checkbox name='use[]' value='{$data->idx}' {$checked[$data->idx]}></td>";
					echo "<td><input type=checkbox name='output[]' value='{$data->idx}' {$checked2[$data->idx]}></td>";
					echo "<td style='text-align:left;'>{$data->coupon}</td>";
					echo "<td>{$regdt}</td>";
					echo "<td><a href=\"javascript:affiliates_mod('{$data->idx}')\"><img src=\"images/btn_edit.gif\"></a></td>";
					echo "<td><a href=\"javascript:affiliates_del('{$data->idx}')\"><img src=\"images/btn_del.gif\"></a></td>";
					echo "</tr>";
					
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan=\"12\" align=center>등록된 리스트가 존재하지 않습니다.</TD></TR>";
				}
?>				

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
			<td>
			<?
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
			?>
			</td>
			</tr>
			<tr>
				<td colspan=9 align=right>
                    <a href="javascript:affiliates_use_output();"><img src="images/botteon_save.gif"></a>
                    <a href="javascript:excel_download()"><img src="images/btn_excel1.gif" border="0"></a>
                </td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>제휴 학교/회사 관리</span></dt>
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
			<form name=form2 method=post action="affiliates_indb.php">
			<input type=hidden name=no>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=title>

			</form>

            <form name=excelform action="aff_excel.php" method=post>
			<!-- <input type=hidden name=af_use value="<?=$af_use?>">
			<input type=hidden name=af_type value="<?=$af_type?>">
			<input type=hidden name=af_area value="<?=$af_area?>">
			<input type=hidden name=af_name value="<?=$af_name?>"> -->
            <input type="hidden" name="search" value="<?php echo htmlspecialchars(serialize($search));?>"/>
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
?>