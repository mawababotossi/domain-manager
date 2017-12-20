<?php 

add_shortcode( 'attopup', 'at_topup_shortcode' );

function at_topup_display_widget(){
$site_url = site_url();
$AT_BASE = plugins_url( '' , __FILE__ );

ob_start();
	?>
<div class="row">
    <div class="col-xs-3 visible-desktop">&nbsp;<img src="<?php echo $AT_BASE; ?>/../images/signup3_XS.png"></div>
    
    <div class="col-xs-8" style="margin-top:40px">
        <form id="topup-form" action="" class="form-horizontal">
            <fieldset id="payement">
                <div class="row form-group">
                    <label class="col-xs-12" for="payement"><?php _e('Mode de payement', 'active-texto'); ?></label>
                    <div class="_input-group col-xs-12">
			<span class="ui-select">
                           <select name="payement-mode">
		              <option value="Flooz">Flooz</option>
		           </select>
			</span>
                    </div>
                </div>
            </fieldset>
            
            <div class="well" id="payement_well"> Pour recharger votre compte en utilisant ce moyen de payement,
vous devez effecteuer un transfert flooz du montant correspondant à votre recharge au numéro (+228)97640297. </br>
Si vous souhaitez payer par un autre moyen ou pour toute autre question, vous pouvez nous <a href="../help">contacter</a> </div>
          
        </form>
    </div>
</div>
<script type="text/javascript">
    
/*jQuery('');*/

</script>
<?php
	$topup_form = ob_get_contents();
	ob_end_clean();
	
	return $topup_form;
    
}

function at_topup_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
	), $atts ) );

	return at_topup_display_widget();
}
