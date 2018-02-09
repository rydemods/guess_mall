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

//exdebug($_POST);
//exdebug($_GET);

include("header.php"); 
?>
<style type="text/css">
/* ==================================================
	탭
================================================== */

.tabs-menu {}
	.tabs-menu:after {display:block; clear:both; content:"";}
	.tabs-menu li {float:left; position:relative; width:20%; height: 31px;line-height: 31px;float: left;background-color: #f0f0f0; box-sizing:border-box; border:1px solid #d3d3d3; border-bottom:1px solid #4b4b4b;}
	.tabs-menu li.on {position: relative;background-color: #fff; z-index: 5; border:1px solid #4b4b4b; border-bottom:1px solid #fff; }
	.tabs-menu li.on:after {display:block; position:absolute; top:0; right:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:last-child::after {display:none;}
	.tabs-menu li.on:before {display:block; position:absolute; top:0; left:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:first-child::before {display:none;}
	.tabs-menu li a {display:block; font-size:0.8rem; font-weight:bold; color:#aaa; text-align:center;}
	.tabs-menu .on a {color: #4b4b4b;}

.tab-content-wrap {background-color: #fff; }
	.tab-content {display: none;}
	.tab-content-wrap > div:first-child { display: block;}
</style>
<script type="text/javascript">
<!--
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("on");
        $(this).parent().siblings().removeClass("on");
        var tab = $(this).attr("href");
        var loc = $(this).attr("alt");
		$(tab).find('iframe').attr('src',loc);
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});
//-->
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>단계별 주문 조회(주문별)</span></p></div></div>

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
					<div class="title_depth3">단계별 주문 조회 (주문별)</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
            <tr>
                <td>
                    <div id="tabs-container">
                        <ul class="tabs-menu">
                            <li class="on"><a href="#tab-1" alt="./order_list_all_order_tab_view.php?oistep1=0">주문접수</a></li>
                            <li><a href="#tab-2" alt="./order_list_all_order_tab_view.php?oistep1=1">결제완료</a></li>
                            <li><a href="#tab-3" alt="./order_list_all_order_tab_view.php?oistep1=2">배송준비중</a></li>
                            <li><a href="#tab-4" alt="./order_list_all_order_tab_view.php?oistep1=3">배송중</a></li>
                            <li><a href="#tab-5" alt="./order_list_all_order_tab_view.php?oistep1=4">배송완료</a></li>
                        </ul>
                        <div class="tab-content-wrap">
                            <div id="tab-1" class="tab-content"><iframe src="./order_list_all_order_tab_view.php?oistep1=0" width="100%" height="800" frameborder=0 scrolling="auto"></iframe></div>
                            <div id="tab-2" class="tab-content"><iframe src="./order_list_all_order_tab_view.php?oistep1=1" width="100%" height="800" frameborder=0 scrolling="auto"></iframe></div>
                            <div id="tab-3" class="tab-content"><iframe src="./order_list_all_order_tab_view.php?oistep1=2" width="100%" height="800" frameborder=0 scrolling="auto"></iframe></div>
                            <div id="tab-4" class="tab-content"><iframe src="./order_list_all_order_tab_view.php?oistep1=3" width="100%" height="800" frameborder=0 scrolling="auto"></iframe></div>
                            <div id="tab-5" class="tab-content"><iframe src="./order_list_all_order_tab_view.php?oistep1=4" width="100%" height="800" frameborder=0 scrolling="auto"></iframe></div>
                        </div>
                    </div>
                </td>
            </tr>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<!-- <div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배송/입금일별 주문조회</span></dt>
							<dd>
								- 입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 부가기능</span></dt>
							<dd>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 주의사항</span></dt>
							<dd>- 배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.</dd>
						</dl>
					</div> -->
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