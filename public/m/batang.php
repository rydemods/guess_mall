<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

include ("header.inc.php");
?>
<div id="titleNaviWrap">
	<h2 class="blind">타이틀 네비게이션바 - 바탕화면 아이콘 설치방법</h2>
	<p class="ticon"><script type="text/javascript">document.write(fn_GoBackurl("javascript:history.back();"));</script></p>
	<h2><img src="style/001/images/title_batang.gif" /></h2>
</div><!-- 타이틀 -->

<hr />

<div id="bodyWrap" class="batang">
	<dl>
		<dt class="blind">바탕화면 아이콘 설치방법</dt>
		<dd>
			바탕화면에 <?=$_data->shopname?> 모바일 웹 바로가기 아이콘을 추가하셔서 좀 더 편리하게 <?=$_data->shopname?>에 접속해 보세요.
		</dd>
		<dd>
			메인화면에서 설치하시기 바랍니다.
		</dd>
	</dl>
	<ul>
		<li><img src="./style/001/images/img_batang_step1.gif" width="278" height="172" alt="1. 브라우저 하단의 플러스 버튼을 누르세요." /></li>
		<li><img src="./style/001/images/img_batang_step2.gif" width="278" height="227" alt="2. 메뉴에서 홈 화면에 추가를 선택하세요." /></li>
		<li><img src="./style/001/images/img_batang_step3.gif" width="278" height="227" alt="3. 이름에 쇼핑몰 이름을 입력하고, 우측의 추가버튼을 누르세요." /></li>
		<li><img src="./style/001/images/img_batang_step4.gif" width="278" height="383" alt="4. 아이폰 바탕화면에 생성된 쇼핑몰 아이콘을 확인하세요." /></li>
	</ul>
</div>

<hr />

<? include ("footer.inc.php"); ?>