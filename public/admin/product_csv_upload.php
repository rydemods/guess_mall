<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "pr-4";
	$MenuCode = "product";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}

	include("header.php"); 
?>
	<script type="text/javascript" src="lib.js.php"></script>
	<script language="JavaScript">
		function CheckForm() {
			document.form1.mode.value="upload";
			document.form1.submit();
		}
		

		$( document ).ready(function(e) {
			$("input[name='upfile_pd']").on("change", function(){
				var filename = $(this).val();
				var extension = filename.replace(/^.*\./, '');
				if (extension == filename) {
					extension = '';
				} else {
					extension = extension.toLowerCase();
				}
				
				//이미지 파일은 JPG, PNG, GIF 확장자만 가능
				if( (extension != 'csv') ) {
					var control = $(this);
					control.replaceWith( control = control.clone( true ) );
					
					alert("상품 업로드는 CSV 확장자만 가능합니다.");
				}
			});
		});
	</script>
	<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>상품 엑셀 업로드</span></p></div></div>
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
							<?php include("menu_product.php"); ?>
							</td>

							<td></td>

							<td valign="top">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height="8"></td>
							</tr>
							<tr>
								<td>
									<!-- 페이지 타이틀 -->
									<div class="title_depth3">상품정보 일괄 등록</div>
								</td>
							</tr>
							<tr>
								<td>
									<!-- 소제목 -->
									<div class="title_depth3_sub"><span>다수 상품정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</span></div>
								</td>
							</tr>
							<tr>
								<td>
									<!-- 소제목 -->
									<div class="title_depth3_sub">카테고리별 상품 일괄 등록 처리 <a href="./sample/product_upload_sample.zip">[샘플 다운로드]</a></div>
								</td>
							</tr>
							<tr>
								<td height=3></td>
							</tr>

							<form name=form1 action="./product_csv_upload_indb.php" method=post enctype="multipart/form-data">
							<input type='hidden' name='mode' value = 'upload'>
							<tr>
								<td>
									<div class="table_style01">
										<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
											<TR>
												<th><span>상품 기본정보 CSV 등록</span></th>
												<TD class="td_con1">
													<input type='file' name='upfile_pd' style="width:30%"><br />
													<span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span>
												</TD>
											</TR>
										</TABLE>
									</div>
								</td>
							</tr>
							<tr>
								<td align="center" height=10></td>
							</tr>
							<tr>
								<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
							</tr>
							</form>
							<tr>
								<td height=20></td>
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