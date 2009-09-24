<?php
include_once ("../lib/admin.defines.php");
include_once ("../lib/admin.module.access.php");
include_once ("../lib/admin.smarty.php");


if (!$ACXACCESS) {
	Header ("HTTP/1.0 401 Unauthorized");
	Header ("Location: PP_error.php?c=accessdenied");	   
	die();	   
}

$smarty->display('main.tpl');


?>
<br/><br/>
<center>
<table align="center" width="90%" bgcolor="white" cellpadding="15" cellspacing="15" style="border: solid 1px">
	<tr>
		<td width="340" align="center">
			<img src="<?php echo Images_Path;?>/a2b-logo-450.png">
			<br><br>
			
		</td>
		<?php if (SHOW_DONATION) { ?>
		<td align="left">
		For information and documentation on A2Billing, <br> please visit <a href="http://www.a2billing.org" target="_blank">http://www.a2billing.org</a><br><br>
		
		For Commercial Installations, Hosted Systems, Customisation and Commercial support, please visit <a href="http://www.star2billing.com" target="_blank">http://www.star2billing.com</a><br><br>
		
		
		For VoIP termination, please visit <a href="http://www.call-labs.com" target="_blank">http://www.call-labs.com</a>
		<center>
		<?php echo '<a href="http://www.call-labs.com/" target="_blank"><img src="'.Images_Path.'/call-labs.com.png" alt="call-labs"/></a>'; ?>
		</center>
		</td>
		<?php } ?>
	</tr>
	
	<tr>
		<td colspan="2">
		<center>
			<b><i>A2Billing is licensed under <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html" 	target="_blank">AGPL 3</a>.</i></b>
			<br><a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html" target="_blank"><img src="images/agplv3-88x31.png"></a>
			</center>
			
		<div class="scroll">
<pre>
<?php echo (file_get_contents("../lib/COPYING")); ?>
</pre>
</div> 
		
		</td>
	</tr>
	
</table>


<br>
	
<table align=center width="90%" bgcolor="white" cellpadding="5" cellspacing="5" style="border: solid 1px">
	<tr>
		<td align="center"> 
			<?php if (SHOW_DONATION) { ?>
			<center>
				<?php echo gettext("If you find A2Billing useful, please donate to the A2Billing project by clicking the Donate button :");?>  
				
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="lc" value="US">
					<input type="hidden" name="country" value="USA">
					<input type="hidden" name="hosted_button_id" value="3769548">
					<input type="image" src="https://www.paypal.com/en_US/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Make Donation with PayPal">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>	
			</center>
			<br>
			<?php } ?>
		</td>
	</tr>
</table>

</center>

<?php

$smarty->display('footer.tpl');


