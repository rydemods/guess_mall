<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

include ("header.inc.php");
?>
<div id="titleNaviWrap">
	<h2 class="blind">Ÿ��Ʋ �׺���̼ǹ� - ����ȭ�� ������ ��ġ���</h2>
	<p class="ticon"><script type="text/javascript">document.write(fn_GoBackurl("javascript:history.back();"));</script></p>
	<h2><img src="style/001/images/title_batang.gif" /></h2>
</div><!-- Ÿ��Ʋ -->

<hr />

<div id="bodyWrap" class="batang">
	<dl>
		<dt class="blind">����ȭ�� ������ ��ġ���</dt>
		<dd>
			����ȭ�鿡 <?=$_data->shopname?> ����� �� �ٷΰ��� �������� �߰��ϼż� �� �� ���ϰ� <?=$_data->shopname?>�� ������ ������.
		</dd>
		<dd>
			����ȭ�鿡�� ��ġ�Ͻñ� �ٶ��ϴ�.
		</dd>
	</dl>
	<ul>
		<li><img src="./style/001/images/img_batang_step1.gif" width="278" height="172" alt="1. ������ �ϴ��� �÷��� ��ư�� ��������." /></li>
		<li><img src="./style/001/images/img_batang_step2.gif" width="278" height="227" alt="2. �޴����� Ȩ ȭ�鿡 �߰��� �����ϼ���." /></li>
		<li><img src="./style/001/images/img_batang_step3.gif" width="278" height="227" alt="3. �̸��� ���θ� �̸��� �Է��ϰ�, ������ �߰���ư�� ��������." /></li>
		<li><img src="./style/001/images/img_batang_step4.gif" width="278" height="383" alt="4. ������ ����ȭ�鿡 ������ ���θ� �������� Ȯ���ϼ���." /></li>
	</ul>
</div>

<hr />

<? include ("footer.inc.php"); ?>