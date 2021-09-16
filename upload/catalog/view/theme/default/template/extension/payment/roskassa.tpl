<form method="GET" action="<?php echo $action?>">
	<input type="hidden" name="shop_id" value="<?php echo $roskassa_login?>"/>
	<input type="hidden" name="amount" value="<?php echo $out_summ?>"/>
	<input type="hidden" name="order_id" value="<?php echo $order_id?>"/>
	<input type="hidden" name="currency" value="<?php echo $out_summ_currency?>"/>
	<?php foreach ($products as $key => $value) { ?>
        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
    <?php } ?>
    <?php if ($roskassa_test) { ?>
		<input type="hidden" name="test" value="1" />
	<?php } ?>
	<input type="hidden" name="sign" value="<?php echo $sign?>"/>
	<input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
</form>