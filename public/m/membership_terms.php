<?php
include_once('outline/header_m.php');

$sql = "SELECT etc_agreement1, etc_agreement2, etc_agreement3 FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {

// 	$etc_agreement1 = ($row->etc_agreement1=="<P>&nbsp;</P>"?"":$row->etc_agreement1);
// 	$etc_agreement1 = str_replace('\\','',$etc_agreement1);

// 	$etc_agreement2 = ($row->etc_agreement2=="<P>&nbsp;</P>"?"":$row->etc_agreement2);
// 	$etc_agreement2 = str_replace('\\','',$etc_agreement2);

	$etc_agreement3 = ($row->etc_agreement3=="<P>&nbsp;</P>"?"":$row->etc_agreement3);
	$etc_agreement3 = str_replace('\\','',$etc_agreement3);
}
pmysql_free_result($result);

?>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>멤버쉽 약관</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="wrap_privacy sub_bdtop">
		<p><?=$etc_agreement3 ?></p>
	</section><!-- //.wrap_privacy -->

</main>
<!-- //내용 -->

<?php
include_once('outline/footer_m.php');
?>