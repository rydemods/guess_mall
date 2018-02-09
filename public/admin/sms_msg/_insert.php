<?php
$Dir = '../';
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 등록</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="<?=$Dir?>static/js/jquery-1.12.0.min.js"></script>
<script LANGUAGE="JavaScript">

    function append_msg(){
        $('#msg_mode').val( 'msg_append' );
        $('#msg_frm').submit();
    }

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

    $(document).ready( function () {
        //cal_pre2('massage_1',false);
        $('textarea[name^="mem_msg"]').each( function ( msg_idx, msg_obj ){
            $('input[name^="len_mem_msg"]').eq( msg_idx ).val( cal_byte2( $(this).val() ) );
        });
        console.log(1);
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
<div class="title_depth3">SMS 등록</div>
<div>
<form id='msg_frm' name='msg_frm' method='POST' action='smssend_msg_indb.php' >
<input type='hidden' name='msg_mode' id='msg_mode' value='' >
<table border='1' bordercolor="#e6e6e6" cellpadding="5" style="width: 98%; border-collapse: collapse; margin-left: 5px; margin-top: 5px;" >
    <colgroup>
        <col class="cellC">
        <col class="cellL">
    </colgroup>
    <tr>
        <th>제목</th>
        <td>
            <input type='text' name='mem_title[]' value='<?=$msg_obj->msg[$msg_i]->title?>' >
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
                                    <textarea class="textarea_hide" name='mem_msg[]' rows='5' cols="26" ><?=$msg_obj->msg[$msg_i]->content?></textarea>
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
    <a href='javascript:append_msg();' >
        <img src="images/btn_confirm_com.gif" border="0">
    </a>
</div>
</body>
</html>