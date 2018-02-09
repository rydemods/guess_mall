<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$no     = $_GET['no'];  // 코멘트 no
$p_no   = $_GET['p_no'];  // 기획전 no

$flagResult = "SUCCESS";

BeginTrans();
try {
    $sql  = "DELETE FROM tblboardcomment_promo ";
    $sql .= "WHERE parent = {$p_no} AND num = {$no} ";

    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Delete Fail');
    }
} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();
?>

<script type="text/javascript">
<?php if ( $flagResult == "SUCCESS" ) { ?>
    alert("삭제가 성공했습니다.");
<?php } else { ?>
    alert("삭제가 실패했습니다.");
<?php } ?>

    location.href = "/admin/popup.event_comment.php?no=<?=$p_no?>";
</script>


