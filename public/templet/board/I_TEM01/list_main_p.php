<?php
//exdebug($row);
?>

<?if($_data->icon_type == 'tem_001'){?>
	<?
		if ($i==0){
//			echo "<ul class=\"photo-list\">";
		}
	?>

        <li>
            <a href="<?=$row['link_url']?>">
                <figure>
                    <?=$photo_img?>
                    <figcaption>
                        <p class="subject"><?=$row['title']?></p>
                        <!--p class="date">2016.01.15 ~ 2016.01.30</p-->
                        <p class="reg-name"><?=$row['name']?></p>
                    </figcaption>
                </figure>
            </a>
        </li>

        <?php if(false) { ?>
            
		<li class="gallery_cell">
			<dl>
				<dt><a href="gallery_view.html"><?=$mini_file1?></a></dt>
				<dd>
					<?=$secret_img?> <?=$subject?> <?if($comment_tot){echo "<font style=\"font-size:8pt;font-family:Tahoma;font-weight:normal\">(<font color=\"red\">{$comment_tot}</font>)</font>";}?>
				</dd>
				<?=$hide_date_start?>
				<dd class="gallery_cell_date"><?=str_replace("/", "-", substr($reg_date, 0, 10))?></dd>
				<?=$hide_date_end?>
			</dl>					 
		</li>

        <?php } ?>

	<?	
        /*
		if ($i!=0 && ($i+1)%5==0 && ($i+1)==$total) {
			echo "</ul>";
		} elseif ($i!=0 && ($i+1)%5==0) {
			echo "</ul><ul class='gallery_lineWarp'>";
        }
        */
//        echo "</ul>";
	?>
<?}else{?>
	<?
		if($i==0) {
			echo "<tr>\n";
			echo "	<td valign=\"top\" colspan=\"".$table_colcnt."\">\n";
			echo "	<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"0\">\n";
			echo "	<TR>\n";
		}

		if ($i!=0 && $i%5==0) {
			echo "	</tr><tr>\n";
		}
	?>
		<!-- 목록 부분 시작 -->
		<TD width="20%" align="center" valign="top" style="padding:5px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr valign="top">
			<td style="padding-top:5px;">
		
		
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td align="center" valign="top"><?=$mini_file1?></td>
			</tr>
			<tr>
				<td align="center" style="padding-top:10px;" nowrap><nobr><B><div style="white-space:nowrap;width:116px;overflow:hidden;text-overflow:ellipsis;font-size:8pt;"><?=$secret_img?> <?=$subject?></div></b></td>
			</tr>
			<tr>
				<td align="center" nowrap><nobr><B><font color="#A48B00" style="font-size:8pt;"><?=$str_name?></font></b></td>
			</tr>
			<?=$hide_date_start?>
			<tr>
				<td align="center" nowrap><nobr><font color="#82705C" style="font-size:8pt;"><?=$reg_date?></font></td>
			</tr>
			<?=$hide_date_end?>
			</table>

			</td>
		</tr>
		</table>
		</TD>
		<!-- 목록 부분 끝 -->

	<?
		if(($total-1) == $i) {
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
	?>
<?}?>
