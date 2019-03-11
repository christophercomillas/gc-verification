	<div class="row rownobot">
		<div class="col-xs-12">
			<form class="form-horizontal" action="<?php echo base_url(); ?>user/passwordconfirmation" id="eodconfirm">
				<div class="form-group">
					<label class="col-xs-4 control-label">Password:</label>
					<div class="col-xs-6">
						<input type="password" class="form-control input-xs reqfieldmk fontsizexs" name="password" required autocomplete="off">
						<input type="password" style="display:none" class="form-control input-xs reqfieldmk fontsizexs" name="password1" required autocomplete="off">	
					</div>
				</div>
				<div class="responsepass">
				</div>
			</form>
		</div>
	</div>
	<script>
		$('input[name=password]').focus();
	</script>