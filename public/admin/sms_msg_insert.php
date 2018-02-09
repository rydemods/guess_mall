<?php
$Dir = '../';
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$idx = $_POST['modify_idx'];
$modify_mode = $_POST['modify_mode'];
$xml_arr = array();
if( $modify_mode == 'modify' ){
    $folder    = './sms_msg';
    $file_path = $folder.'/';
    $file      = 'mem_msg.xml';
    $xml = simplexml_load_file( $file_path.$file );

    $xml_arr = array( 'title'=>$xml->msg[(int)$idx]->title, 'content'=>$xml->msg[(int)$idx]->content, 'idx'=>(int)$idx );
}


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 메세지 저장</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script>
<script LANGUAGE="JavaScript">

    function cal_byte2(aquery) {
        var tmpStr;
        var temp = 0;
        var onechar;
        var tcount = 0;
        var reserve = 0;

        tmpStr = new String(aquery);
        temp = tmpStr.length;

        for(k=0; k<temp; k++) {
            onechar = tmpStr.charAt(k);
            if(escape(onechar).length > 4) {
                tcount += 2;
            } else {
                tcount ++;
            }
        }
        return tcount;
    }

    function append_msg(){
        $('#msg_mode').val( 'msg_append' );
        $('#msg_frm').submit();
    }

    function modify_msg(){
        $('#msg_mode').val( 'msg_modify' );
        $('#msg_frm').submit();
    }

    $(document).ready( function () {
        //cal_pre2('massage_1',false);
        $('textarea[name^="mem_msg"]').each( function ( msg_idx, msg_obj ){
            $('input[name^="len_mem_msg"]').eq( msg_idx ).val( cal_byte2( $(this).val() ) );
        });
    });

    $(document).on( 'keyup', 'textarea[name^="mem_msg"]', function( event ) {
        var msg_idx = $('textarea[name^="mem_msg"]').index( $(this) );
        $('input[name^="len_mem_msg"]').eq( msg_idx ).val( cal_byte2( $(this).val() ) );
    });

</script>

<style type="text/css">
    .title_depth3 {
        margin-top: 20px;
        background: url('../admin/img/common/title_depth3_bg3.gif') no-repeat;
        height: 20px;
        color: #000;
        font-size: 16px;
        font-weight: bold;
        padding-left: 15px;
        letter-spacing: -0.02em;
        position: relative;
    }

    table th { font: 9pt tahoma; color: #000000; background-color: #f8f8f8; }

    div.insert-button { margin-top : 15px; text-align: center; }

</style>

</head>
<body>
<div class="title_depth3">SMS 메세지 저장</div>
<div>
<form id='msg_frm' name='msg_frm' method='POST' action='smssend_msg_indb.php' >
<input type='hidden' name='msg_mode' id='msg_mode' value='' >
<input type='hidden' name='msg_idx' id='msg_idx' value='<?=$idx?>' >
<table border='1' bordercolor="#e6e6e6" cellpadding="5" style="width: 98%; border-collapse: collapse; margin-left: 5px; margin-top: 5px;" >
    <colgroup>
        <col class="cellC">
        <col class="cellL">
    </colgroup>
    <tr>
        <th>제목</th>
        <td>
            <input type='text' name='mem_title[]' value='<?=$xml_arr['title']?>' >
        </td>
    </tr>
    <tr>
        <th>내용</th>
        <td>
            <div>
                <table >
                    <tr>
                        <td height="">
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <TABLE WIDTH='200' BORDER='0' CELLPADDING='0' CELLSPACING='0' align="center">
                            <TR>
                                <TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
                            </TR>
                            <TR>
                                <TD align='center' height="90" background="images/sms_bg.gif" valign="top">
                                    <TEXTAREA class="textarea_hide" name='mem_msg[]' rows='5' cols="26" ><?=$xml_arr['content']?></TEXTAREA>
                                </TD>
                            </TR>
                            <TR>
                                <TD align='center' height="26" background="images/sms_down_01.gif">
                                    <INPUT style="PADDING-RIGHT:5px; WIDTH:40px; TEXT-ALIGN:right" onfocus='this.blur();' value='0' name='len_mem_msg[]' size="3" class="input_hide"> bytes (최대2000 bytes)
                                </TD>
                            </TR>
                        </TABLE>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
</form>
</div>
<div class='insert-button' >
<?php
if( $modify_mode == 'modify' ){
?>
    <a href='javascript:modify_msg();' >
        <img src="images/btn_confirm_com.gif" border="0">
    </a>
<?php
} else {
?>
    <a href='javascript:append_msg();' >
        <img src="images/btn_confirm_com.gif" border="0">
    </a>
<?php
}
?>
</div>
</body>
</html>