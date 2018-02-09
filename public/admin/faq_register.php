<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$no=$_REQUEST[no];
$mode=$_REQUEST[mode]?$_REQUEST[mode]:"faq_add";

//수정
$query="select * from tblfaq where no='{$no}'";
$result=pmysql_query($query);
$data=pmysql_fetch_array($result);

$checked[$data[faq_type]]="selected";


##카테고리 쿼리
$cate_qry="select * from tblfaqcategory order by sort_num";
$cate_result=pmysql_query($cate_qry);

?>

<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<link rel="stylesheet" href="../css/community.css">
<script>LH.add("parent_resizeIframe('AddFrame')");</script>

<SCRIPT LANGUAGE="JavaScript">

function checkform(){
	
	
	form=document.form1;
	
	if(!form.faq_type.value){
		alert("분류를 선택하여 주십시요.");		
		form.faq_type.focus();
		return;
	}	

	if(!form.faq_title.value){
		alert("질문을 입력하여주십시요.");		
		form.faq_title.focus();
		return;
	}

	var sHTML = oEditors.getById["faq_content"].getIR();
	form.faq_content.value=sHTML;

	if(!form.faq_content.value){
		alert("답변을 입력하여주십시요.");		
		form.faq_content.focus();
		return;
	}
	
	form.submit();

}


	
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt; <span>FAQ등록</span></p></div></div>

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
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	
		<form name=form1 action="faq_indb.php" method=post enctype="multipart/form-data">
		<input type="hidden" name="mode" value="<?=$mode?>">
		<input type="hidden" name="no" value="<?=$no?>">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<!-- 페이지 타이틀 -->
							<div class="title_depth3">FAQ 질문</div>
						</td>
					</tr>			
					<tr><td height="20"></td></tr>
					<tr>
						<td>
							<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
									<tr>
										<th><span>분류 설정</span></th>
										<td colspan="3">
											<select name="faq_type" id="category_no">
												<option value="">〓〓 FAQ분류를 선택하세요. 〓〓</option>	
<?while($cate_row=pmysql_fetch_object($cate_result)){?>
							
												<option value="<?=$cate_row->num?>" <?=$data[faq_type]==$cate_row->num?"selected":""?>><?=$cate_row->faq_category_name?></option>
							
<?}?>
																						
											</select>
										</td>
									</tr>
									<tr>
										<th><span>질문</span></th>
										<td colspan="3">
											<input type="text" size="100" name="faq_title" id="faq_title" value="<?=$data[faq_title]?>">
										</td>
									</tr>
									<tr>
										<th><span>답변</span></th>
										<td colspan="3">
											<textarea name="faq_content" id="faq_content" rows="20" cols="100" style="width:766px; height:412px;"><?=$data[faq_content]?></textarea>
											
										</td>
									</tr>
									
								</table>
							</div>
						</td>
					</tr>
					<tr><td height=20></td></tr>
					</table>
				</td>
			</tr>
			<tr>
				
				<td align=center>
				<a href="javascript:checkform();">
				<?if($mode=="faq_mod"){?>
					<img src="<?=$Dir."/admin/images/btn_modify_com.gif"?>">
				<?}else{?>
					<img src="<?=$Dir."/admin/images/btn_confirm_com.gif"?>">
				<?}?>
				</a>
				<a href="faq.php"><img src="<?=$Dir."/admin/images/btn_list_com.gif"?>"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			
			<tr>
				<td>
					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>메뉴얼</p></div>
						<dl>
							<dt><span>제목제목제목</span></dt>
							<dd>
								  - 내용내용내용  <br />
								  - 내용내용내용 <br />
								  - 내용내용내용
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			
			<tr><td height=20></td></tr>
			<tr><td height="50"></td></tr>
			<tr><td height=20 colspan=2></td></tr>
			
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

    <SCRIPT LANGUAGE="JavaScript">
		var oEditors = [];

		nhn.husky.EZCreator.createInIFrame({
			oAppRef: oEditors,
			elPlaceHolder: "faq_content",
			sSkinURI: "../SE2/SmartEditor2Skin.html",
			htParams : {
				bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
				bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
				bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
				//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
				fOnBeforeUnload : function(){
				}
			},
			fOnAppLoad : function(){
			},
			fCreator: "createSEditor2"
		});

	</script>

<?php
include("copyright.php");

?>

</body>
</html>
