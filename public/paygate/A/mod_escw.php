<?
    /* ============================================================================== */
    /* =   PAGE : ����ũ�� ���� ���� PAGE                                           = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �Ʒ��� �� ���� �� �κ��� �� �����Ͻþ� ������ �����Ͻñ� �ٶ��ϴ�.       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>*** KCP [AX-HUB Version] ***</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <link href="css/style.css" rel="stylesheet" type="text/css"/>

    <script language="javascript">
    function  jsf__go_mod( form )
    {
        if(form.mod_type.value=="mod_type_not_sel")
        {
          alert( "���º��� ������ �����Ͻʽÿ�.");
        }
        else if ( form.tno.value.length < 14 )
        {
            alert( "KCP �ŷ� ��ȣ�� �Է��ϼ���." );
            form.tno.focus();
            form.tno.select();
        }

        else if(form.mod_type.selectedIndex == 1 && form.deli_numb.value=="")
        {
            alert( "����� ��ȣ�� �Է��ϼ���." );
            form.deli_numb.focus();
            form.deli_numb.select();
        }
        else if(form.mod_type.selectedIndex == 1 && form.deli_corp.value=="")
        {
            alert( "�ù� ��ü���� �Է��ϼ���." );
            form.deli_corp.focus();
            form.deli_corp.select();
        }
        else if((form.mod_type.selectedIndex == 2 || form.mod_type.selectedIndex == 4) && form.vcnt_use_yn.checked && form.mod_account.value=="")
        {
            alert( "ȯ�� ���� ���¹�ȣ�� �Է��ϼ���." );
            form.mod_account.focus();
            form.mod_account.select();
        }
        else if((form.mod_type.selectedIndex == 2 || form.mod_type.selectedIndex == 4) && form.vcnt_use_yn.checked && form.mod_depositor.value=="")
        {
            alert( "ȯ�� ���� �����ָ��� �Է��ϼ���." );
            form.mod_depositor.focus();
            form.mod_depositor.select();
        }
        else if((form.mod_type.selectedIndex == 2 || form.mod_type.selectedIndex == 4) && form.vcnt_use_yn.checked && form.mod_bankcode.value=="mod_bankcode_not_sel")
        {
            alert( "ȯ�� ���� �����ڵ带 ������ �ּ���." );
        }
        else
        {
            return true ;
        }
        return false;
    }
    function typeChk( form )
    {
        if (form.mod_type.selectedIndex == 1)
        {
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE1.style.display = "block";
            type_STE9.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "none";
        }
        else if (form.mod_type.selectedIndex == 2 || form.mod_type.selectedIndex == 4)
        {
            type_STE1.style.display = "none";
            type_STE5.style.display = "none";
            type_STE2N4.style.display = "block";
            type_STE9.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "none";
        }
        else if (form.mod_type.selectedIndex == 5)
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "block";
            type_STE9.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "none";
        }
        else if (form.mod_type.selectedIndex == 6 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "none";
            type_STE9.style.display = "block";
        }
        else if (form.mod_type.selectedIndex == 7 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "block";
            type_STE9_2.style.display = "block";
            type_STE9_3.style.display = "block";
            type_STE9_4.style.display = "none";
            type_STE9.style.display = "block";
        }
        else if (form.mod_type.selectedIndex == 8 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "none";
            type_STE9.style.display = "block";
        }
        else if (form.mod_type.selectedIndex == 9 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "block";
            type_STE9_2.style.display = "block";
            type_STE9_3.style.display = "block";
            type_STE9_4.style.display = "none";
            type_STE9.style.display 	= "block";
        }
        else if (form.mod_type.selectedIndex == 10 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "block";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "block";
            type_STE9.style.display = "block";
        }
        else if (form.mod_type.selectedIndex == 11 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "none";
            type_STE9_2.style.display = "none";
            type_STE9_3.style.display = "none";
            type_STE9_4.style.display = "block";
            type_STE9.style.display = "block";
        }
        else if (form.mod_type.selectedIndex == 12 )
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
            type_STE9_1.style.display = "block";
            type_STE9_2.style.display = "block";
            type_STE9_3.style.display = "block";
            type_STE9_4.style.display = "block";
            type_STE9.style.display = "block";
        }
        else
        {
            type_STE1.style.display = "none";
            type_STE2N4.style.display = "none";
            type_STE5.style.display = "none";
        }
    }

    function selfDeliChk( form )
    {
        if (form.self_deli_yn.checked)
        {
            form.deli_numb.value = "0000";
            form.deli_corp.value = "�ڰ����";
        }
        else
        {
            form.deli_numb.value = "";
            form.deli_corp.value = "";
        }
    }

    function vcntUseChk( form )
    {
        if (form.vcnt_use_yn.checked)
        {
            type_RFND.style.display = "block";
            form.vcnt_yn.value = "Y";
        }
        else
        {
            type_RFND.style.display = "none";
            form.vcnt_yn.value = "N";
        }
    }
    </script>
</head>

<body>

<div id="sample_wrap">
<?
    /* ============================================================================== */
    /* =    ���º��� ��û �Է� ��(mod_escrow_form)                                  = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ���º��� ��û�� �ʿ��� ������ �����մϴ�.                                = */
    /* = -------------------------------------------------------------------------- = */
?>
    <form name="mod_escrow_form" method="post" action="global.lib.php">

                 <!-- Ÿ��Ʋ Start-->
                    <h1>[�����û] <span>�� �������� ����ũ�� ���º����� ��û�ϴ� ����(����) �������Դϴ�.</span></h1>
                 <!-- Ÿ��Ʋ End -->

                 <!-- ��� ���̺� Start -->
                    <div class="sample">
                    <p>
                    �ҽ� ������ �ҽ� �ȿ� <span>�� ���� ��</span>ǥ�ð� ���Ե� ������ �������� ��Ȳ�� �°� ������ ����</br>
                    �����Ͻñ� �ٶ��ϴ�.</br>
                    <span>�� �������� ����ũ�η� ������ �ǿ� ���� ���º����� ��û�ϴ� ������ �Դϴ�.</span></br>
                    ������ ���εǸ� ��������� KCP �ŷ���ȣ(tno)���� ���� �� �ֽ��ϴ�.<br/>
                    ������������ �� KCP �ŷ���ȣ(tno)������ ����ũ�� ���º��� ��û �� �� �ֽ��ϴ�.
                    </p>
                  <!-- ��� ���̺� End -->
                <!-- ��� ��û ���� �Է� ���̺� Start -->
                    <h2>&sdot; ����ũ�� ���º��� ��û</h2>
                    <table class="tbl" cellpadding="0" cellspacing="0">
                <!-- ��û ���� : ����ũ�� ���º��� ��û -->
                <!-- ��û ���� : ��۽���, ������, ���꺸��, ���, �߱ް������� -->
                    <tr>
                        <th>���º��� ����</th>
                        <td>
                          <select name="mod_type" onChange="javascript:typeChk(this.form);">
                            <option value="mod_type_not_sel" selected>�����Ͻʽÿ�</option>
                            <option value="STE1">��۽���</option>
                            <option value="STE2">������</option>
                            <option value="STE3">���꺸��</option>
                            <option value="STE4">���</option>
                            <option value="STE5">�߱ް�������</option>
                            <option value="STE9_C">����Ȯ�������ī��</option>
                            <option value="STE9_CP">����Ȯ���ĺκ����ī��</option>
                            <option value="STE9_A">����Ȯ������Ұ���</option>
                            <option value="STE9_AP">����Ȯ���ĺκ���Ұ���</option>
                            <option value="STE9_AR">����Ȯ����ȯ�� ����</option>
                            <option value="STE9_V">����Ȯ����ȯ�� ����</option>
                            <option value="STE9_VP">����Ȯ���ĺκ�ȯ�� ����</option>
                          </select>
                        </td>
                    </tr>
                    <!-- KCP �ŷ���ȣ(tno) -->
                    <tr>
                        <th>KCP �ŷ���ȣ</th>
                        <td><input type="text" name="tno" value=""  class="frminput" size="20" maxlength="14"/></td>
                    </tr>
              </table>
              <span id="type_STE1" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>�ڰ���ۿ���</th>
                      <td>&nbsp;&nbsp;&nbsp;�ڰ������ ��� üũ&nbsp;<input type='checkbox' name='self_deli_yn' onClick='selfDeliChk(this.form)'></td>
                  </tr>
                  <tr>
                      <th>������ȣ</th>
                      <td><input type='text' name='deli_numb' value='' class="frminput" size='20' maxlength='25'></td>
                  </tr>
                  <tr>
                      <th>�ù� ��ü��</th>
                      <td><input type='text' name='deli_corp' value='' class="frminput" size='20' maxlength='25'></td>
                  </tr>
              </table>
              </span>
              <span id="type_STE2N4" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>������� �ŷ�</th>
                      <td>
                          &nbsp;&nbsp;&nbsp;������� ���&nbsp;<input type='checkbox' name='vcnt_use_yn' onClick='vcntUseChk(this.form)'>
                      </td>
                  </tr>
              </table>
              <div id="type_RFND" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>ȯ�Ҽ�����¹�ȣ</th>
                      <td>
                          <input type='text' name='mod_account' class="frminput" value='' size='23' maxlength='50'>
                      </td>
                  </tr>
                  <tr>
                      <th>ȯ�Ҽ�������ָ�</th>
                      <td>
                          <input type='text' name='mod_depositor' value='' class="frminput" size='23' maxlength='50'>
                      </td>
                  </tr>
                  <tr>
                      <th>ȯ�Ҽ��������ڵ�</th><!-- ��Ÿ �պ��� �����̳� ���ǻ�� �Ŵ����� �����Ͻñ� �ٶ��ϴ� -->
                      <td>
                          <select name='mod_bankcode'>
                              <option value="mod_bankcode_not_sel" selected>����</option>
                              <option value="39">�泲����</option>
                              <option value="34">��������</option>
                              <option value="04">��������</option>
                              <option value="03">�������</option>
                              <option value="11">����</option>
                              <option value="31">�뱸����</option>
                              <option value="32">�λ�����</option>
                              <option value="45">�������ݰ�</option>
                              <option value="07">����</option>
                              <option value="88">��������</option>
                              <option value="48">����</option>
                              <option value="05">��ȯ����</option>
                              <option value="20">�츮����</option>
                              <option value="71">��ü��</option>
                              <option value="35">��������</option>
                              <option value="81">�ϳ�����</option>
                              <option value="27">�ѱ���Ƽ����</option>
                              <option value="54">HSBC</option>
                              <option value="23">SC��������</option>
                              <option value="02">�������</option>
                              <option value="37">��������</option>
                          </select>
                      </td>
                  </tr>
              </table>
              </div>
              </span>
              <span id="type_STE5" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <td><center>�߱ް������� ��û�� ������� ������ ���ؼ��� �̿��Ͻñ� �ٶ��ϴ�.</center></td>
                  </tr>
              </table>
              </span>
              <span id="type_STE9_1" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>�����ִ±ݾ�</th>
                          <td>
                              <input type='text' name='rem_mny' value='' size='20' maxlength='20'>��
                          </td>
                  </tr>
              </table>
              </span>
              <span id="type_STE9_2" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>��ҿ�û�ݾ�</th>
                          <td>
                              <input type='text' name='mod_mny' value='' size='20' maxlength='20'>��
                          </td>
                  </tr>
              </table>
              </span>

              <span id="type_STE9_3" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>���� ��� ��û�ݾ�</th>
                          <td>
                              <input type='text' name='tax_mny' value='' size='20' maxlength='20'>��
                          </td>
                  </tr>
                  <tr>
                      <th>����� ��� ��û�ݾ�</th>
                          <td>
                              <input type='text' name='free_mod_mny' value='' size='20' maxlength='20'>��
                          </td>
                  </tr>
                  <tr>
                      <th>�ΰ��� ��� ��û�ݾ�</th>
                          <td>
                              <input type='text' name='add_tax_mny' value='' size='20' maxlength='20'>��
                          </td>
                  </tr>
              </table>
              </span>

              <span id="type_STE9_4" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>ȯ�Ұ��¹�ȣ</th>
                          <td>
                              <input type='text' name='a_refund_account' value='' size='23' maxlength='50'>
                          </td>
                  </tr>
                  <tr>
                      <th>ȯ�Ұ����ָ�</th>
                          <td>
                              <input type='text' name='a_refund_nm' value='' size='23' maxlength='50'>
                          </td>
                  </tr>
                  <tr>
                      <th>ȯ�������ڵ�</th>
                          <td>
                              <select name='a_bank_code'>
                              <option value="bank_code_not_sel" selected>����</option>
                              <option value="39">�泲����</option>
                              <option value="34">��������</option>
                              <option value="04">��������</option>
                              <option value="03">�������</option>
                              <option value="11">����</option>
                              <option value="31">�뱸����</option>
                              <option value="32">�λ�����</option>
                              <option value="45">�������ݰ�</option>
                              <option value="07">����</option>
                              <option value="88">��������</option>
                              <option value="48">����</option>
                              <option value="05">��ȯ����</option>
                              <option value="20">�츮����</option>
                              <option value="71">��ü��</option>
                              <option value="35">��������</option>
                              <option value="81">�ϳ�����</option>
                              <option value="27">�ѱ���Ƽ����</option>
                              <option value="54">HSBC</option>
                              <option value="23">SC��������</option>
                              <option value="02">�������</option>
                              <option value="37">��������</option>
                              </select>
                          </td>
                  </tr>
              </table>
              </span>
              <span id="type_STE9" style="display:none">
              <table class="tbl" cellpadding="0" cellspacing="0">
                  <tr>
                      <th>������һ���</th>
                          <td>
                              <select name='mod_desc_cd'>
                              <option value="" selected>����</option>
                              <option value="CA06">��Ÿ</option>
                              </select>
                          </td>
                  </tr>
                  <tr>
                      <th>��һ���</th>
                          <td>
                              <input type='text' name='mod_desc' value='' size='40' maxlength='40'>
                          </td>
                  </tr>
              </table>
              </span>
                <!-- ����ũ�� ���º��� ��û/ó������ -->
                    <!-- ���� ��ư ���̺� Start -->
                    <div class="btnset">
                    <input name="" type="submit" class="submit" value="�����û" onclick="return jsf__go_mod(this.form);" alt="����ũ�� ����Ȯ���� ��û�մϴ�"/>
					<a href="../index.html" class="home">ó������</a>
                    </div>
                    <!-- ���� ��ư ���̺� End -->
                </div>
            <div class="footer">
                Copyright (c) KCP INC. All Rights reserved.
            </div>
        </table>
<?
    /* ============================================================================== */
    /* =   1-1. ��� ��û �ʼ� ���� ����                                            = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ� - �ݵ�� �ʿ��� �����Դϴ�.                                      = */
    /* = ---------------------------------------------------------------------------= */
?>
        <input type="hidden" name="req_tx"   value="mod_escrow" />
        <input type="hidden" name="vcnt_yn"  value="N" />
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   1. ��� ��û ���� END                                                    = */
    /* ============================================================================== */
?>
    </form>
</div>
</body>
</html>

