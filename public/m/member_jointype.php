<?
$subTitle = "ȸ������";

include_once('outline/header_m.php');

if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:index.php");
	exit;
}

$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));
if($CertificationData->realname_check || $CertificationData->ipin_check){
	$certiSrc = "member_certi.php";
}else{
	$certiSrc = "member_agree.php";
}

/*if(!$_data->shop_mem_type){
	header("Location:".$certiSrc."?jointype=0");
	exit;
}*/






?>
<script type="text/javascript">
	function join_next_tem01(links,val){
		$("#mem_type").val(val);
		$("#frmInfo").attr("action",links).submit();
	}
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<form id="frmInfo" name="frmInfo" method="POST">
	<input type="hidden" id="mem_type" name="mem_type" value=""/>
</form>

<main id="content" class="subpage">
<article class="join_step_tap">
	<h1><!-- ��ܹ��� �ڸ� --></h1>
	<section>
		<ul>
			<li><a class="on" href="#">ȸ������</a></li>
			<li><a >�������</a></li>
			<li><a >�����Է�</a></li>
			<li><a >���ԿϷ�</a></li>
		</ul>
	</section>
</article>

<section class="ipin">
	<!--input type="button" value="������ ����" onclick="checkSubmit();" /-->
	<div class="btn_center mpb_50">
	<a href="javascript:join_next_tem01('<?=$certiSrc?>','0');" class="btn_gray_p1" style="background-color:#E8380D;color:#fff">�Ϲ�ȸ�� ����</a>
	<!--<a href="javascript:join_next_tem01('<?=$certiSrc?>','1');" class="btn_gray_p1" style="background-color:#f7f7f7;">���ȸ�� ����</a>-->
	</div>
	<p><!--�˸��� ��ġ--></p>
</section>
</main>
<? include_once('outline/footer_m.php'); ?>