<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" id="PayPalForm" name="PayPalForm">
 
  <input type="hidden" name="custom" value="<?php echo $custom; ?>" />  
  <div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" onclick="paypal()"
	class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" />
  </div>
</div>

</form>

<script>
  function paypal(){
  
    
	
	document.PayPalForm.submit();
    
	
	
	
	}
   </script>
