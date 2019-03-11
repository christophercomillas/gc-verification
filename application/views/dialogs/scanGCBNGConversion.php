<div class="row">
    <div class="col-xs-12 form-horizontal">
        <form method="post" action="<?php echo base_url(); ?>transaction/scanGCForBNGCustomer" id="scanGCForBNGCustomer">
            <div class="form-group bot16">
                <label class="col-xs-5 control-label">Remaining GC to scan:</label>
                <div class="col-xs-7">
                    <input type="text" class="form input-sm inptxt form-control bld" readonly="readonly" value="<?php echo $gctoscan; ?>" id="gctoscan">
                </div>
            </div><!-- end of form-group --> 
            <div class="form-group bot16">
                <label class="col-xs-5 control-label">Denomination:</label>
                <div class="col-xs-7">
                    <input type="text" class="form input-sm inptxt form-control bld" name="denombng" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" name="data" autocomplete="off" value="0.00" id="denomination">
                </div>
            </div><!-- end of form-group -->  
            <div class="labels-mdl">GC Barcode #</div>
            <div class="form-group inputGcbarcode">
                <div class="col-xs-12">
                    <input data-inputmask="'alias': 'numeric','digits': 0, 'digitsOptional': false, 'prefix': '', 'placeholder': ''" class="form-control input-lg input-validation" id="gcbarcode" name="gcbarcode" autocomplete="off" maxlength="13" />
                </div>
            </div>
        </form>
        <div class="response-validate">
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#gcbarcode,#denomination').inputmask();
    $('#denomination').select();
</script>