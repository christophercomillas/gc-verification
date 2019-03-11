<div class="form-container">  
	<form method="POST" action="<?php echo base_url(); ?>customer/addnewcustomerValidation" id="_addcustomer">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>First Name</label>
					<input type="text" class="form form-control inpmedx inptxt-l" id="fnamedialog" name="fname" autocomplete="off" autofocus>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Last Name</label>
					<input type="text" class="form form-control inpmedx inptxt-l" id="lnamedialog" name="lname" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Middle Name</label>
					<input type="text" class="form form-control inpmedx inptxt-l" id="mnamedialog" name="mname" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog">Name Ext. (ex. jr, sr, III)</label>
					<input type="text" class="form form-control inpmedx inptxt-l" id="extnamedialog" name="extname" autocomplete="off">
				</div>
				<div class="response-dialog">										
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#fnamedialog').select();
</script>
