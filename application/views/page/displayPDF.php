<?php 
    $st = $this->session->userdata('gc_store');
    $st = strtolower(str_replace(" ","",$st));
	//echo base_url().'reports/'.$buname.'eod.pdf';

?>
<div class="row">
<center><iframe id="iframeId" src="<?php echo base_url();?>assets/reports/<?php echo $st;?>bng.pdf" width="580" height="400" type='application/pdf'>
</iframe></center>
</div>
<script>

	// function print(url)
	// {
	// 	alert(url);
	//     var _this = this,
	//         iframeId = 'iframeprint',
	//         $iframe = $('iframe#iframeprint');
	//     $iframe.attr('src', url);

	//     $iframe.load(function() {
	//         _this.callPrint(iframeId);
	//     });
	// }
	function callPrint(iframeId) {
		var PDF = document.getElementById(iframeId);
		PDF.focus();
		PDF.contentWindow.print();
		$('.printbut').focus();
	}

</script>
