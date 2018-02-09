<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
?>

<?php include("header.php");?>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품 결제관련 기능설정</span></p></div></div>
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
			<?php include("menu_signage.php"); ?>
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
					<div class="title_depth3">네이버 지도 세팅</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>.....</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">인증 KEY 세팅</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center">Client_ID</TD>
					<TD class="table_cell" align="center">Client_secret</TD>
				</TR>
				<TR><TD class="td_con1\" colspan=2 align=center>등록된 정보가 없습니다.</td></tr>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<TD width="100%"><div class="point_title">...</div></TD>
                    </tr>
                    <tr>
						<td width="100%">
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>Client_ID</span></th>
							<TD class="td_con1"><input type=text name=up_newbank_account1 size=27 maxlength=50 class="input"></TD>
						</TR>
						<TR>
							<th><span>Client_Secret</span></th>
							<TD class="td_con1"><input type=text name=up_newbank_account1 size=27 maxlength=50 class="input"></TD>
						</TR>
						</TABLE>
                        </div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>



			
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
	
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>네이버 지도 관련 세팅</span></dt>
							<dd>- 메뉴얼
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
