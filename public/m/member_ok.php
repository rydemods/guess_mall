<?
$subTitle = "ȸ������";
include_once('outline/header_m.php');

$mode = $_REQUEST['mode'];


if(!$mode){
	header("location:member_jointype.php");
}



include ("header.inc.php");
$subTitle = "ȸ������";
include ("sub_header.inc.php");

?>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">


<main id="content" class="subpage">
<article class="join_step_tap">
	<h1></h1>
	<section>
		<ul>
			<li><a href="#">ȸ������</a></li>
			<li><a href="#">�������</a></li>
			<li><a href="#">�����Է�</a></li>
			<li><a class="on" href="#">���ԿϷ�</a></li>
		</ul>
	</section>
</article>
<article class="join_step03">
	<h3>ȸ�������� �Ϸ�Ǿ����ϴ�.</h3>
	<p class="name">ȸ���� �ǽŰ��� ȯ���մϴ�.</p>
   <div class="join_btn_area">
	<center><a href="index.php"><input type="button" value="��������" class="join" style="width:100%;"/></a><a href="mypage.php"><input type="button" value="����������" class="cancle" style="width:100%;"/></a></center>
   </div>
</article>
</main>

<? include_once('outline/footer_m.php'); ?>