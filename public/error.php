<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
        header("HTTP/1.0 404 Not Found");
        exit;
}
?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="<?=$Dir?>lib/style.css">
</head>
<body bgcolor="333333" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
        <td height="385" align="center" valign="middle">
        <table width="414" height="245" border="0" cellpadding="0" cellspacing="0">
        <tr>
                <td height="38" valign="top"><img src="<?=$Dir?>images/install_message_tit.gif" width="414" height="38"></td>
        </tr>
        <tr>
                <td valign="top" background="<?=$Dir?>images/install_message_bg.gif">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                        <td height="96" align="center" style="color:#B1B1B1"><?=$msg?></td>
                </tr>
                <tr>
                        <td align="center" valign="top" style="padding-top:15;">

                        <?if(!$url || $url=="-1" || $url=="-2") {?>

                        <img src="<?=$Dir?>images/install_move_back.gif" style="cursor:hand" onclick="history.go(<?=$url?>)">

                        <?}else{?>

                        <img src="<?=$Dir?>images/install_move_page.gif" style="cursor:hand" onclick=location.href="<?=$url?>">

                        <?}?>
                        </td>
                </tr>
                </table>
                </td>
        </tr>
        <tr> 
                <td></td>
        </tr>
        </table>
        </td>
</tr>
</table>
</body>
</html>
