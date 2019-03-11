var siteurl ="";
$(document).ready(function() {	
    siteurl = $('input[name="baseurl"]').val();
    checksession();
    var url = document.URL;
    var to = url.split('/');
    if(to[to.length-2]=='beamandgoreport')
    {
        // display report dialog`1b 
        var trnum = to[to.length-1];
        BootstrapDialog.show({
            title: 'Beam and Go to GC Accountability Report',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
                return $message;
            },
            data: {
                'pageToLoad': siteurl+'Report/displayPDF',
            },
            cssClass: 'pdfshowModal',           
                onshown: function(dialogRef){                   
            },
            onhidden: function(dialogRef){ 
                window.location.replace(siteurl+'report/beamandgoreport'); 

            },
            buttons: [{
                icon: 'glyphicon glyphicon-print',
                label: ' Print',
                cssClass: 'btn-default printbut',
                action: function(dialogItself){
                    callPrint('iframeId');
                }
            },{
                icon: 'glyphicon glyphicon-remove-sign',
                label: ' Close',
                cssClass: 'btn-default',
                action: function(dialogItself){                    
                window.location.replace(siteurl+'report/beamandgoreport'); 

                dialogItself.close();
                  // window.location = '../cashiering';
                }   
            }]
        });
        //alert(trnum);
    }
    
    // Begin Uploading Testing //
    $('body').on('change', 'input.uploaex', function (e) {

        uploadfiles = e.target.files;

        $('body').find(".upload__x").trigger('submit');

    });

    $('body').on('submit', '.upload__x', function (e) {
        e.preventDefault();
        //alert('fsdfdsf');

        formData = new FormData();

        $.each( uploadfiles, function(i, file) {
            formData.append('xcelfile[]', file);
        });

        jqstream(siteurl+'transaction/uploading', formData);

        //stream(formData);
        //clear_input(this);
    });


    // End Uploading Testing //

    $("body").on('click', "table#_verifiedgc tbody tr td a i#revalidation", function(){
        var id = $(this).closest('div.action-barcode').attr('data-id');
        BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> GC Barcode # '+id+' transaction.',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': siteurl+'transaction/validationInfoDialog/revalidation/'+id,
            },            
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

        return false;
    });  

    $("body").on('click', "table#_verifiedgc tbody tr td a i#reverified", function(){
        var id = $(this).closest('div.action-barcode').attr('data-id');
        BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> GC Barcode # '+id+' transaction.',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': siteurl+'transaction/transactionDialog/'+id,
            },
            size: BootstrapDialog.SIZE_WIDE,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });  

    $("body").on('click', "table#_verifiedgc tbody tr td a i#transactions", function(){
        var id = $(this).closest('div.action-barcode').attr('data-id');
        BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> GC Barcode # '+id+' transaction.',
            cssClass: 'modlarge',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': siteurl+'transaction/transactionDialog/'+id,
            },
            size: BootstrapDialog.SIZE_WIDE,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });  


    $('#datepicker').datepicker({
      autoclose: true
    });

	$('input[id^=dennum]').inputmask("integer", { allowMinus: false,autoGroup: true, groupSeparator: ",", groupSize: 3 });

	$('input#denomination,input#gcbarcodever, input#paymentreceived').inputmask();  

    $('#list,#revalidationtable').dataTable( {
        "pagingType": "full_numbers",
        "ordering": false,
        "processing": true
    });

    $('input#gcbarcodever').on('focus',function(){
        $(this).select();
    });

    $('#btnReval').click(function(){
    	$('.response').html('');
    	$('.response-scan').html('');
    	$('#btnReval').prop('disabled',true);	

    	var cnt = $('#gcscancou').val();
		cnt = parseInt(cnt.replace(/,/g , ""));			
		cnt = isNaN(cnt) ? 0 : cnt;

		if(cnt == 0)
		{
			$('.response').html('<div class="form-group"><div class="alert alert-danger">Please scan GC Barcode # to revalidate.</div></div>');
			$('#barcode').select();
			$('#btnReval').prop('disabled',false);
			return false;
		}

		var revalpayment = $('#totalrevalpayment').val();
		revalpayment = parseFloat(revalpayment.replace(/,/g , ""));			
		revalpayment = isNaN(revalpayment) ? 0 : revalpayment;

		var paymentreceived = $('#paymentreceived').val();
		paymentreceived = parseFloat(paymentreceived.replace(/,/g , ""));		

		paymentreceived = isNaN(paymentreceived) ? 0 : paymentreceived;

		var change = 0;
		change = parseFloat(paymentreceived - revalpayment);
		if(change < 0)
		{
			$('.response').html('<div class="form-group"><div class="alert alert-danger">Total Revalidation is greater than Payment Received.</div></div>');
			$('#barcode').select();
			$('#btnReval').prop('disabled',false);
			return false;
		}

        BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Revalidate GC?',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden:function(dialog){
            	$('#btnReval').prop('disabled',false);
            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-primary',
                hotkey: 13,
                action:function(dialogItself){
                    var $button1 = this;
                    $button1.disable();
                    $.ajax({
						url:siteurl+'transaction/revalidationpayment',
						data:{paymentreceived:paymentreceived},
                        type:'POST',
                        success:function(data)
                        {
                            console.log(data);
                            var data = JSON.parse(data);
                            if(data['st'])
                            {          
                            	dialogItself.close();                      
                                var dialog = new BootstrapDialog({
                                message: function(dialogRef){
                                var $message = $('<div><h4>Revalidation Successfull</h4><table class="table revaldialog"><tbody><tr><td>Amount Due</td><td class="dlg-r">'+revalpayment+'</td></tr><tr><td>Payment Received</td><td class="dlg-r">'+paymentreceived+'</td></tr><tr><td>Change</td><td class="dlg-r dlg-rchange">'+change+'</td></tr></tbody></div>');    
				                var $button = $('<button class="btn btn-primary btn-lg btn-block">Close the dialog</button>');
				                $button.on('click', {dialogRef: dialogRef}, function(event){
				                    event.data.dialogRef.close();
				                });
				                $message.append($button);             
                                    return $message;
                                },
                                onhidden:function(dialog){
									window.location = siteurl;
                                },
                                closable: false
                                });
                                dialog.realize();
                                dialog.getModalFooter().hide();
                                dialog.getModalBody().css('background-color', '#0088cc');
                                dialog.getModalBody().css('color', '#fff');
                                dialog.open();
                                
                                // setTimeout(function(){
                                // 	$('#gcbarcodever').focus();
                                //     dialog.close(); 
                                // }, 1500);
                            }
                            else 
                            {
                                $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">'+data['msg']+'</div>');
                                $('form#_addcustomer input#fname').focus();
                                dialogItself.close();
                                $button.enable();   
                                return false;
                            }
                        }
                    });                                                    
                }
            },{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                    $button.enable();  
                }
            }]
        }); 


    });

	$('.form-container').on('submit','form#_scanrevalgc',function(event){
		event.preventDefault()
		$('.response-scan').html('');
		$('.response').html('');

		$('#btnscanreval').prop('disabled',true);	

		var formURL = $('form#_scanrevalgc').attr('action'), formData = $('form#_scanrevalgc').serialize();

		var barcode = $('#barcode').val();
		if(barcode.trim()=='')
		{
			$('.response-scan').html('<div class="form-group"><div class="alert alert-danger">Please input GC Barcode #.</div></div>');
			$('#barcode').focus();
			$('#btnscanreval').prop('disabled',false);
			return false;
		}

		$.ajax({
			url:formURL,
			type:'POST',
			data:formData,
			beforeSend:function(){

			},
			success:function(data){
				console.log(data);
				var data = JSON.parse(data);

				if(data['st'])
				{

					$('#gcscancou').val(data['count']);
					$('#totalrevalpayment').val(data['total']);

					var t = $('#revalidationtable').DataTable();

	    			var counter = 1;
			        t.row.add( [	
			        	data['barcode'],	        	
			            data['denomination'],
			            data['reval'].toFixed(2),
			            '<input type="hidden" value="'+data['key']+'" class="denoms"><i class="fa fa-times removed" aria-hidden="true"></i>'
			        ] ).draw( false );
			 		
			        counter++;

					$('.response-scan').html('<div class="form-group"><div class="alert alert-info info-success">'+data['msg']+'</div></div>');
					$('#barcode').select();
					$('#btnscanreval').prop('disabled',false);
				}
				else 
				{
					$('.response-scan').html('<div class="form-group"><div class="alert alert-danger">'+data['msg']+'</div></div>');
					$('#barcode').select();
					$('#btnscanreval').prop('disabled',false);
				}
			}
		});
	});

	$('table#revalidationtable').on('click','.removed',function(){		
		var key = $(this).parents('tr').find('input.denoms').val();
		var r = confirm("Remove Barcode?");
		if (r == true) {			
			$.ajax({
				//url:'../ajax.php?action=deleteAssignByKey',
				url:siteurl+'transaction/removeByKeyRevalidation',
				data:{key:key},
				type:'POST',
				success:function(data)
				{
					console.log(data);
					var data = JSON.parse(data);
					if(data['st'])
					{
						var total = data['total'];
						total = addCommas(parseFloat(total).toFixed(2));
						$('#denocr').val(total);
					}
				}
			});

			var table = $('#revalidationtable').DataTable();
			table
			.row( $(this).parents('tr') )
			.remove()
			.draw();
		}
		
		// $('input[name=lastname]').focus();
	});

    $('table#_verifiedgc').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
	    "url": siteurl+'transaction/verifiedgclist',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "vs_barcode" },
	        { "data": "vs_tf_denomination" },
	        { "data": "gctype" },
	        { "data": "cusname" },
	        { "data": "dateverified" },
	        { "data": "verby"},
	        { "data": "action"}     
	    ]
    });

    $('table#_bgnscangclist').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "autoWidth": false,
        "ajax":{
	    "url": siteurl+'transaction/bgnscangclist',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "date" },
	        { "data": "ref" },
	        { "data": "serial" },
	        { "data": "barcode" },
	        { "data": "amount" },
	        { "data": "beneficiary"}   
	    ]
    });

    $('table#_revalidatedgc').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
	    "url": siteurl+'transaction/revalidatedgclist',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "barcode" },
	        { "data": "denomination" },
	        { "data": "gctype" },
	        { "data": "customer" },
	        { "data": "daterevalidated" },
	        { "data": "revalidatedby"},
	        { "data": "payment"}     
	    ]
    });    

    $('table#_gcforeod').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
	    "url": siteurl+'transaction/gclistforeod',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "barcode" },
	        { "data": "denomination" },
	        { "data": "gctype" },
	        { "data": "customer" },
	        { "data": "dateverre" },
	        { "data": "reverby"}    
	    ]
    }); 

    $('table#_gcformigration').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
	    "url": siteurl+'transaction/gcformigration',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "barcode" },
	        { "data": "denomination" },
	        { "data": "gctype" },
	        { "data": "cusname" },
	        { "data": "dateverified" },
	        { "data": "verby"},
	        { "data": "eoddate"}     
	    ]
    });    

    $('table#_importedgc').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax":{
	    "url": siteurl+'transaction/importedgc',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "barcode" },
	        { "data": "denomination" },
	        { "data": "gctype" },
	        { "data": "cusname" },
	        { "data": "dateverified" },
	        { "data": "verby"},
            { "data": "eoddate"},
            { "data": "datemigrated"}
	    ]
    });     
    
	$('#_logout').click(function(){

        BootstrapDialog.show({
        	title: 'Logout',
            message: 'Are you sure you want to log out?',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onshown:function(dialog){
                restrictback=0;
            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Yes, Please',
                cssClass: 'btn-primary',
                hotkey: 13,
                action:function(dialogItself){
                    dialogItself.enableButtons(false);
                    dialogItself.setClosable(false);
					$.ajax({
						url:siteurl+'User/logoutuser',			
						beforeSend:function(){									
						},
						success:function(data){	
							// alert(response);
							console.log(data);
							var  data = JSON.parse(data);
							if(data['st'])
							{
								window.location.href = siteurl;
							} 

						}
					});
                	window.location.href = siteurl+'User/logoutuser';
                }
            }, {
            	icon: 'glyphicon glyphicon-remove-sign',
                label: 'No Thanks',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
	});

	$("input[id^=dennum]").keyup(function(){
		var sum = 0;
		var qty = 0;

		$('input[id^=dennum]').each(function(){
			// var a = $(this).parent('.col-sm-3').find('input.denval').val();
			var dnid = $(this).attr('id').slice(6);
			var a = $("#dennum"+dnid).val();
			a = parseInt(a.replace(/,/g , ""));			
			a = isNaN(a) ? 0 : a;
			qty +=a;
			var den = parseInt($("#m"+dnid).val());
			var sub = a * den;
			sum+=sub;
		});	
		$('span#internaltot').text(addCommas(parseFloat(sum).toFixed(2)));
		$('span#totgcreqqty').text(addCommas(parseInt(qty)));
	});

	$('#denomination').keyup(function(){
        $('#amtinwords').val($(this).AmountInWords());	
	});

	$( "#_vercussearch" ).bind({
		keyup: function() {
			var cust = $(this).val().trim();
			if(cust.length > 2)
			{
				$('span.xcus').show();
				$('span.xcus').html("<img src='"+siteurl+"assets/img/ring-alt.svg' class='loadver'> Processing Please Wait..");
				$.ajax({
		    		url:siteurl+'transaction/searchCustomerVerification',
		    		data:{cust:cust},
		    		type:'POST',
					beforeSend:function(){
						
					},
					success:function(data){
						console.log(data);
						var data = JSON.parse(data);
						if(data['st'])
						{
							$('span.xcus').html(data['msg']);	
						}
						else 
						{
							$('span.xcus').html(data['msg']);
						}
					}
				});
			}
			else 
			{
				$('span.xcus').hide();
			}
        },
        blur:function(){
            setTimeout(function(){
                $('span.xcus').hide();
            },200) 
        }
	});

	$('div.col-sm-12').on('click','span.xcus ul li.vernames',function(){
		var name = $(this).text();
		var id = $(this).attr('data-id');
		var fname = $(this).attr('data-fname');
		var mname = $(this).attr('data-mname');
		var lname = $(this).attr('data-lname');
		var next = $(this).attr('data-namext');
		$('span.xcus').hide();
		$('span.xcus').html('');
		$('#_vercussearch').val(name);
		$('#cid').val(id);
		$('#fname').val(fname);
		$('#lname').val(lname);
		$('#mname').val(mname);
		$('#next').val(next);
		$('#gcbarcodever').focus();
    });

    $('.form-container').on('submit','form#_reverifygc',function(event){
        var formURL = $(this).attr('action'), formData = $(this).serialize();
		event.preventDefault();
        $('.response').html('');
        $('#denomination').val("");
        $('#amtinwords').val("");        
        $('#gctype').val("");
        $('#fname').val("");
        $('#lname').val("");        
        $('#mname').val("");
        $('#next').val(""); 

        $('button.reverifybtn').prop("disabled",true);	
        
        var payto = $('#payto').val();
        var barcode = $('#gcbarcodever').val();

        setTimeout(function(){
            if(payto.trim() == '' || barcode.trim() == '')
            {
                $('.response').html('<div class="alert alert-danger" id="danger-x">Pay to and GC Barcode Number required.</div>');
                $('button.reverifybtn').prop("disabled",false);	
                return false;
            }

            $.ajax({
                url:formURL,
                type:'POST',
                data:formData,
                beforeSend:function(){
                    $('#processing-modal').modal('show');
                },
                success:function(data){
                    console.log(data);
                    var data = JSON.parse(data);

                    if(data['st'])
                    {
                        $('#processing-modal').modal('hide');
                        $('.response').html('<div class="alert alert-success">'+data['msg']+'</div>');
                        if(data['reval'])
                        {
                            $('#print-receipt-verify').html(data['barcode']+' '+data['customer']+' '+data['date']+' '+data['time']+' '+data['storename']).css('left','230px');
                        }
                        else 
                        {
                            $('#print-receipt-verify').html(data['barcode']+' '+data['customer']+' '+data['date']+' '+data['time']+' '+data['storename']);
                        }
                        jQuery('#print-receipt-verify').print();
                        var dialog = new BootstrapDialog({
                        message: function(dialogRef){
                        var $message = $('<div>'+data['flashmsg']+'</div>');			        
                            return $message;
                        },
                        closable: false
                        });
                        dialog.realize();
                        dialog.getModalHeader().hide();
                        dialog.getModalFooter().hide();
                        dialog.getModalBody().css('background-color', '#0088cc');
                        dialog.getModalBody().css('color', '#fff');
                        dialog.open();
                        setTimeout(function(){
                            dialog.close();
                        }, 1500);
                        setTimeout(function(){
                            $('#gcbarcodever').select();
                            $('#print-receipt').html('');
                        }, 1700);
                        $('button.reverifybtn').prop("disabled",false);	   

                        $('#denomination').val(data['denom']);
                        $('#amtinwords').val($('#denomination').AmountInWords());        
                        $('#gctype').val(data['gctype']);
                        $('#fname').val(data['fname']);
                        $('#lname').val(data['lname']);        
                        $('#mname').val(data['mname']);
                        $('#next').val(data['namext']);
                    }
                    else 
                    {
                        $('#processing-modal').modal('hide');
                        if(data['isverified'])
                        {
                            $('#denomination').val(data['denom']);
                            $('#amtinwords').val($('#denomination').AmountInWords());        
                            $('#gctype').val(data['gctype']);
                            $('#fname').val(data['fname']);
                            $('#lname').val(data['lname']);        
                            $('#mname').val(data['mname']);
                            $('#next').val(data['namext']);
                        }
                        $('.response').html('<div class="alert alert-danger">'+data['msg']+'</div>');
                        $('#gcbarcodever').select();	
                        $('button.reverifybtn').prop("disabled",false);					
                    }
                    $('span.verifyreprint').html('');
                    $('#isreprint').val(0);
                }
            });
        
        },200)
        


        return false;
    });

	$('.form-container').on('submit','form#_verifygc2',function(event){
		var formURL = $(this).attr('action'), formData = $(this).serialize();
		event.preventDefault()
		$('.response').html('');

		$('button.verifybtn').prop("disabled",true);
        setTimeout(function(){
            if($('#cid').val().trim()=='')
            {
                $('.response').html('<div class="alert alert-danger" id="danger-x">Please input customer details.</div>');
                $('button.verifybtn').prop("disabled",false);       
                return false;
            }

            if($('#gcbarcodever').val().trim()=='')
            {
                $('.response').html('<div class="alert alert-danger" id="danger-x">Please input GC Barcode number.</div>'); 
                $('button.verifybtn').prop("disabled",false);               
                return false;
            }

            var denom = $('#denomination').val().trim();
            denom = denom.replace(/,/g , "");
            denom = isNaN(denom) ? 0 : denom;
            denom  = denom * 1;

            if(denom=='' || denom=='0' || denom=='0.00')
            {
                $('.response').html('<div class="alert alert-danger" id="danger-x">Please input valid denomination.</div>');    
                $('button.verifybtn').prop("disabled",false);               
                return false;
            }

            $.ajax({
                url:formURL,
                type:'POST',
                data:formData,
                beforeSend:function(){
                    $('#processing-modal').modal('show');
                },
                success:function(data){
                    console.log(data);
                    var data = JSON.parse(data);

                    if(data['st'])
                    {
                        $('#processing-modal').modal('hide');
                        $('.response').html('<div class="alert alert-success">'+data['msg']+'</div>');
                        if(data['reval'])
                        {
                            $('#print-receipt-verify').html(data['barcode']+' '+data['customer']+' '+data['date']+' '+data['time']+' '+data['storename']).css('left','230px');
                        }
                        else 
                        {
                            $('#print-receipt-verify').html(data['barcode']+' '+data['customer']+' '+data['date']+' '+data['time']+' '+data['storename']);
                        }
                        jQuery('#print-receipt-verify').print();
                        var dialog = new BootstrapDialog({
                        message: function(dialogRef){
                        var $message = $('<div>'+data['flashmsg']+'</div>');                    
                            return $message;
                        },
                        closable: false
                        });
                        dialog.realize();
                        dialog.getModalHeader().hide();
                        dialog.getModalFooter().hide();
                        dialog.getModalBody().css('background-color', '#0088cc');
                        dialog.getModalBody().css('color', '#fff');
                        dialog.open();
                        setTimeout(function(){
                            dialog.close();
                        }, 1500);
                        setTimeout(function(){
                            $('#gcbarcodever').select();
                            $('#print-receipt').html('');
                            $('button.verifybtn').prop("disabled",false);    
                        }, 1700);

                    }
                    else 
                    {
                        $('#processing-modal').modal('hide');
                        $('.response').html('<div class="alert alert-danger">'+data['msg']+'</div>');
                        $('#gcbarcodever').select();      
                        $('button.verifybtn').prop("disabled",false);                 
                    }
                    $('span.verifyreprint').html('');
                    $('#isreprint').val(0);
                }
            });


            
        },100);	

		return false;
	});

    $('table#posts').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
	    "url": siteurl+'transaction/datatablesample',
	    "dataType": "json",
	    "type": "POST"
	    },
    	"columns": [
	        { "data": "id" },
	        { "data": "title" },
	        { "data": "body" },
	        { "data": "created_at" },
	    ]

    });

	// $('span.xcus ul li').click(function(){
	// 	alert('xxx');
	// 	var name = $(this).text();
	// 	$('span.xcus').hide();
	// 	$('#_vercussearch').val(name);
    // });
    
    $('#_bgnscangc').DataTable( {
        "bSort": false

    } ); 

    $('.form-container').on('submit','#_bngTransaction',function(event){
        event.preventDefault();
        $('.response').html('');       
        
        var formURL = $(this).attr('action'), formData = $(this).serialize();  
        $('#btnBNGSub').prop('disabled',true);

        if($('#totamt').val()==0 || $('#gcscanned').val()==0)
        {
            $('.response').html('<div class="alert alert-danger alert-danger-dialog">Please upload file / Scan GC.</div>');     
            $('#btnBNGSub').prop('disabled',false);
            return false;
        }

        BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Save Data?',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
                $('button#btnBNGSub').prop('disabled',true);
            },
            onhide: function(dialog) {
                $('button#btnBNGSub').prop('disabled',false);
            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Yes',
                cssClass: 'btn-primary',
                hotkey: 13,
                action:function(dialogItself){    
                    $button = this;   
                    $button.disable();  
                    dialogItself.close();
                    $.ajax({
                        url:formURL,
                        beforeSend:function(){
                            $('#processing-modal').modal('show');
                        },
                        success:function(data){
                            console.log(data);
                            var data  = JSON.parse(data);

                            if(data['st'])
                            {
                                $('#processing-modal').modal('hide');
                                var dialog = new BootstrapDialog({
                                message: function(dialogRef){
                                var $message = $('<div>Beam and Go Transaction successfully saved.</div>');                    
                                    return $message;
                                },
                                closable: false
                                });
                                dialog.realize();
                                dialog.getModalHeader().hide();
                                dialog.getModalFooter().hide();
                                dialog.getModalBody().css('background-color', '#0088cc');
                                dialog.getModalBody().css('color', '#fff');
                                dialog.open();
                                setTimeout(function(){
                                    location.reload();
                                }, 1500);
                            }
                            else 
                            {
                                $('.response').html('<div class="alert alert-danger alert-danger-dialog">'+data['msg']+'</div>');
                            }

                        }
                    });
                    
                    $button.enable();   
                }
            }, {
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'No',
                action: function(dialogItself){
                    dialogItself.close();
                    $('#btnBNGSub').prop('disabled',false);
                }
            }]
        });

        return false;
    });

    $('input#upload').change(function(e) {
        e.preventDefault(); 
        $('#processing-modal').modal('show');
        var t = $('#_bgnscangc').DataTable();
        var counter = 1;

        $('#totamt').val(0);   
        $('#totamt').val(0);   
        $('#gcscanned').val(0);

        t.clear().draw();

        $('.response').html('');

        var formData = new FormData();
        formData.append('file', $('#upload')[0].files[0]);

        var value = $(this).val();
        if(value.length == 0 )
        {
            $('#processing-modal').modal('hide');
            return false;
        }
        var allowedExtensions = ["xlsx"];
        file = value.toLowerCase(),
        extension = file.substring(file.lastIndexOf('.') + 1);
        if ($.inArray(extension, allowedExtensions) == -1) 
        {
            $('.response').html('<div class="alert alert-danger">Invalid File.</div>');
            return false;
        } 

        $.ajax({
            url : siteurl+'transaction/getbngexceldata',
            type : 'POST',
            data : formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success : function(data) {
                console.log(data);
                var data = JSON.parse(data);
                if(data['st'])
                {
                    $('#totamt').val(data['totamt']);                
                    var l = data['data'].length;               
                    var cnt = 0;     
                    for (var i = 0; i < data['data'].length; i++) {

                        var counter = 1;
                        t.row.add( [                    
                            data['data'][i]['sernum'],
                            data['data'][i]['value'],
                            '',
                            '<input type="hidden" value="'+data['data'][i]['sernum']+'" class="serial"><i class="fa fa-times remove-node" aria-hidden="true"></i>'
                        ] ).draw( false );
                        
                        counter++;
                        cnt++;
                        //alert(data['data'][i]['refnum']);
                    };   
                    console.log(l);
                    if(l==cnt)
                    {
                        $('#processing-modal').modal('hide');
                    }
                    
                }
                else 
                {
                    $('#processing-modal').modal('hide');
                    $('.response').html('<div class="alert alert-danger alert-danger-dialog">'+data['msg']+'</div>');
                    $('#totamt').val(0);   
                } 
            }
        });

    });

    $('table#_bgnscangc').on('click','.remove-node',function(){
               
        var serial = $(this).parents('tr').find('input.serial').val();
        var r = confirm("Remove Item?");
        if (r == true) {
            $('#processing-modal').modal('show');
            $.ajax({
                //url:'../ajax.php?action=deleteAssignByKey',
                //url:'../ajax.php?action=removeBySerialNumber',
                url:siteurl+'Transaction/removeBySerialNumber',
                data:{serial:serial},
                type:'POST',
                success:function(data)
                {
                    console.log(data);
                    var data = JSON.parse(data);
                    if(data['st'])
                    {
                        $('#totamt').val(data['total']);
                        $('#gcscanned').val(data['count']);
                        $('#processing-modal').modal('hide');
                    }
                    else 
                    {
                        $('#processing-modal').modal('hide');
                        $('.response').html('<div class="alert alert-danger alert-danger-dialog">Item not found.</div>');
                    }

                }
            });

            var table = $('#_bgnscangc').DataTable();
            table
            .row( $(this).parents('tr') )
            .remove()
            .draw();                  

        }
        
        //$('input[name=lastname]').focus();
    });

    $('#_scangcbng').click(function(){
        $('.response').html('');
        $('#_scangcbng').prop("disabled",true);
        $('#_scangcbng').html('<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> Processing...');
        $('#upload').prop("disabled",true);
        $('#btnBNGSub').prop("disabled",true);        
        
        var t = $('#_bgnscangc').DataTable();
        var counter = 1;
        $('#processing-modal').modal('show');
        $.ajax({
            url : siteurl+'transaction/checkGCToSCanBNG',
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success : function(data) {
                console.log(data);
                var data = JSON.parse(data);

                if(parseInt(data['gctoscan'])> 0)
                {                    
                    BootstrapDialog.show({
                        title: 'Scan GC',
                        cssClass: 'meddlg',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        message: function(dialog) {
                            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                            var pageToLoad = dialog.getData('pageToLoad');
                            setTimeout(function(){
                            $message.load(pageToLoad);
                            },1000);
                            return $message;
                        },
                        data: {
                            'pageToLoad': siteurl+'transaction/scanGCForCustomerBNG/'+data['gctoscan']
                        },
                        onshow: function(dialogRef){
                            $('#processing-modal').modal('hide');
                        },
                        onhidden:function(dialogRef){    
                            $('#processing-modal').modal('show');
                    
                            // var h = $('#_bgnscangcdiv').height();
                            // var w = $('#_bgnscangcdiv').width();
                            // w = Math.round(w);
                            // h = Math.round(h);
                            // h = h/2;
                            // w = w/2;
                            // h = h - 10;
                            // $('#_loadingtable').css({
                            //     'top' : h,
                            //     'left':w,
                            //     'display':'block'
                            //  });

                            t.clear()
                                .draw();
                                $.ajax({
                                    url:siteurl+'transaction/getBNGScanBarcode',
                                    success:function(data){
                                        //$('#gctoscan').val(0);
                                        console.log(data);
                                        var data = JSON.parse(data);                                            
                                        for (var i = 0; i < data['data'].length; i++) {

                                            var counter = 1;               
                                            t.row.add( [                    
                                                data['data'][i]['sernum'],
                                                data['data'][i]['value'],
                                                data['data'][i]['barcode'],
                                                '<input type="hidden" value="'+data['data'][i]['sernum']+'" class="serial"><i class="fa fa-times remove-node" aria-hidden="true"></i>'
                                            ] ).draw( false );
                                            
                                            counter++;
                                            //alert(data['data'][i]['refnum']);
                                        }; 
                                        // $('#_loadingtable').css({                                            
                                        //     'display':'none'
                                        //  });
                                        $('#processing-modal').modal('hide');
                                        $('#upload').prop("disabled",false);
                                        $('#btnBNGSub').prop("disabled",false);  
                                        $('#_scangcbng').prop("disabled",false);
                                        $('#_scangcbng').html('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Scan GC');                                        
                                    }
                                });     

                           

                        },        
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Submit',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                $('#processing-modal').modal('show');
                                dialogItself.enableButtons(false);
                                dialogItself.setClosable(false);
                                $('.response-validate').html('');
                                var t = $('#bnggc').DataTable();
                                var counter = 1;
                                var barcode = $('#gcbarcode').val(), formUrl = $('form#scanGCForBNGCustomer').attr('action');
                                var denom = $('#denomination').val().trim();
                                denom = denom.replace(/,/g , "");
                                denom = isNaN(denom) ? 0 : denom;
                                denom  = denom * 1;
                                if(barcode==undefined)
                                {
                                    $('#processing-modal').modal('hide');
                                    dialogItself.enableButtons(true);
                                    dialogItself.setClosable(true);
                                    return false;
                                }

                                if(barcode.trim()=='')
                                {
                                    $('#processing-modal').modal('hide');
                                    $('.response-validate').html('<div class="alert alert-danger alert-dismissable">Please input GC barcode number.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');      
                                    $('#gcbarcode').select();
                                    dialogItself.enableButtons(true);
                                    dialogItself.setClosable(true);
                                    return false;
                                }

                                if(denom=="" || denom=="0.00" || denom=="0")
                                {
                                    $('#processing-modal').modal('hide');
                                    $('.response-validate').html('<div class="alert alert-danger alert-dismissable">Please input valid denomination.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');      
                                    $('#gcbarcode').select();
                                    dialogItself.enableButtons(true);
                                    dialogItself.setClosable(true);
                                    return false;
                                }

                                $.ajax({
                                    url:formUrl,
                                    data:{barcode:barcode,denom:denom},
                                    type:'POST',
                                    success:function(data){
                                        //$('#gctoscan').val(0);
                                        console.log(data);
                                        var data = JSON.parse(data);
                                        if(data['st'])
                                        {
                                            $('#processing-modal').modal('hide');
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                            $('#gctoscan').val(data['nobarcode']);

                                            $('.response-validate').html('<div class="alert alert-success alert-dismissable">'+data['msg']+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); 
                                            $('#gcbarcode').select();


                                            $('#gcscanned').val(data['gcscan']);
                                            // $('#totden2').val(data['total']);

                                        }
                                        else 
                                        {
                                            $('#processing-modal').modal('hide');
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                            $('.response-validate').html('<div class="alert alert-danger alert-dismissable">'+data['msg']+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');  
                                            $('#gcbarcode').select();                               
                                        }
                                    }
                                });         
                            }
                        }, {
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Close',
                            action: function(dialogItself){
                                dialogItself.close();
                            }
                        }]
                    });
                }
                else 
                {
                    $('#processing-modal').modal('hide');
                    $('#upload').prop("disabled",false);
                    $('#btnBNGSub').prop("disabled",false);  
                    $('#_scangcbng').html('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Scan GC');
                    $('#_scangcbng').prop("disabled",false);
                    alert('Please upload file.');
                }
            }
        });
        

    });   

    $('#btn-bngreport').click(function(e){

        e.preventDefault();
        //window.location = siteurl+'report/beamandgoreport/1';  
        $('.response').html('');

        $('#btn-bngreport').prop("disabled",true);
        $('#processing-modal').modal('show');

        var formURL = $('#_querybngdata').attr('action');
        var trdate = $('#datepicker').val();
        $.ajax({
            url : formURL,
            type : 'POST',
            data : {trdate:trdate},
            success : function(data) {
                console.log(data);
                var data = JSON.parse(data);

                if(!data.st)
                {
                    $('#processing-modal').modal('hide');
                    $('#btn-bngreport').prop("disabled",false);
                    $('.response').html('<div class="alert alert-danger">'+data.msg+'</div>');
                }
                else 
                {
                    window.location = siteurl+'report/beamandgoreport/1';  
                }
            }
        });

        return false;

    });

}); // document end

//Begin Uploading Function

function changePassword()
{
    BootstrapDialog.show({
        title: '<i class="fa fa-user"></i></i> Change Password',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        message: function(dialog) {
            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
            var pageToLoad = dialog.getData('pageToLoad');
            setTimeout(function(){
                $message.load(pageToLoad); 
            },1000);
          return $message;
        },
        data: {
            'pageToLoad': siteurl+'user/eodConfirmationDialog',
        },
        cssClass: 'changeaccountpass',
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
        },
        onhidden: function(dialogRef){
            $('#numOnly').focus();
            flag=0;
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Submit',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
                $('.responsepass').html('');
                dialogItself.enableButtons(false);
                dialogItself.setClosable(false);
                if($('input[name=password]').val()!=undefined)
                {
                    var formData = $('form#eodconfirm').serialize(), formURL = $('form#eodconfirm').attr('action');
                    if($('input[name=password]').val()!='')
                    {      

                    }
                    else 
                    {
                        $('.responsepass').html('<div class="alert alert-danger">Please input password.</div>');
                    }
                }
                dialogItself.enableButtons(true);
                dialogItself.setClosable(true);
                $('input[name=username]').focus();                                          
            }
        },{
            icon: 'glyphicon glyphicon-remove-sign',
            label: 'Close',
            cssClass: 'btn-default',
            action:function(dialogItself){
                dialogItself.close();
            }
        }]
    }); 
}

function changeUsername()
{
    BootstrapDialog.show({
        title: '<i class="fa fa-user"></i></i> Change Username',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        message: function(dialog) {
            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
            var pageToLoad = dialog.getData('pageToLoad');
            setTimeout(function(){
                $message.load(pageToLoad); 
            },1000);
          return $message;
        },
        data: {
            'pageToLoad': siteurl+'user/eodConfirmationDialog',
        },
        cssClass: 'changeaccountpass',
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
        },
        onhidden: function(dialogRef){
            $('#numOnly').focus();
            flag=0;
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Submit',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
                $('.responsepass').html('');
                dialogItself.enableButtons(false);
                dialogItself.setClosable(false);
                if($('input[name=password]').val()!=undefined)
                {
                    var formData = $('form#eodconfirm').serialize(), formURL = $('form#eodconfirm').attr('action');
                    if($('input[name=password]').val()!='')
                    {      

                    }
                    else 
                    {
                        $('.responsepass').html('<div class="alert alert-danger">Please input password.</div>');
                    }
                }
                dialogItself.enableButtons(true);
                dialogItself.setClosable(true);
                $('input[name=username]').focus();                                          
            }
        },{
            icon: 'glyphicon glyphicon-remove-sign',
            label: 'Close',
            cssClass: 'btn-default',
            action:function(dialogItself){
                dialogItself.close();
            }
        }]
    }); 
}

function stream(url, data) {

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                //dialogItself.close();
                $('#processing-modal').modal('show');
                //console.log(output);
                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                                 
                            
                            //var result2 = JSON.parse( new_response );
                            console.log(result);

                            if(result.status=='checking')
                            {
                                $('h4.loading').html(result.message);
                            }
                            
                            if(result.status=='error')
                            {
                                
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "EOD Failed",
                                    type: "warning",
                                    text: result.message,
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "OK",
                                    closeOnConfirm: true
                                })
                            }     

                            if(result.status=='complete')
                            {
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "Complete",
                                    type: "success",
                                    text: result.message,
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "OK",
                                    closeOnConfirm: true
                                },function(){
                                    location.reload();
                                })
                            }
                            
                            if(result.status=='looping')
                            {
                                $('h4.loading').html(result.progress);
                            }


                            

                            output.previous_text = output.responseText;

                            //console.log(new_response);
                        }
                    }catch(e){
                        console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}

//End Uploading Function

function checktextfiles()
{    
    $.ajax({        
        url: siteurl+'transaction/textfileChecker',
        type: 'POST',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                $('#processing-modal').modal('show');

                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                                
                            
                            //var result2 = JSON.parse( new_response );
                            console.log(result);        

                            if(result.status=='checking')
                            {
                                $('h4.loading').html(result.message);
                            }
                            
                            if(result.status=='error')
                            {
                                
                                $('#processing-modal').modal('hide');
                                swal({
                                    html:true,
                                    title: "EOD Failed",
                                    type: "warning",
                                    text: result.message,
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "OK",
                                    closeOnConfirm: true
                                })
                            }     

                            if(result.status=='complete')
                            {
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "Complete",
                                    type: "success",
                                    text: result.message,
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "OK",
                                    closeOnConfirm: true
                                })
                            }
                            
                            if(result.status=='looping')
                            {
                                $('h4.loading').html(result.progress);
                            }                           

                            output.previous_text = output.responseText;

                            //console.log(new_response);
                        }
                    }catch(e){
                        console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}

function eodstorestextfiles()
{
    BootstrapDialog.show({
        title: '<i class="fa fa-user"></i></i> EOD Confirmation Re-enter your password',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        message: function(dialog) {
            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
            var pageToLoad = dialog.getData('pageToLoad');
            setTimeout(function(){
                $message.load(pageToLoad); 
            },1000);
          return $message;
        },
        data: {
            'pageToLoad': siteurl+'user/eodConfirmationDialog',
        },
        cssClass: 'changeaccountpass',
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
        },
        onhidden: function(dialogRef){
            $('#numOnly').focus();
            flag=0;
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Submit',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
                $('.responsepass').html('');
                dialogItself.enableButtons(false);
                dialogItself.setClosable(false);
                if($('input[name=password]').val()!=undefined)
                {
                    var formData = $('form#eodconfirm').serialize(), formURL = $('form#eodconfirm').attr('action');
                    if($('input[name=password]').val()!='')
                    {        

                        $.ajax({
                            url:formURL,
                            data:formData,
                            type:'POST',
                            success:function(data)
                            {
                                //console.log(data);
                                var data = JSON.parse(data);
                                if(data['st'])
                                {                                        
                                    stream(siteurl+'transaction/processeodtextfile',data);
                                    dialogItself.enableButtons(true);
                                    dialogItself.setClosable(true);
                                    dialogItself.close();   
                                }
                                else 
                                {
                                    $('.responsepass').html('<div class="alert alert-danger">Incorrect Password</div>');
                                    dialogItself.enableButtons(true);
                                    dialogItself.setClosable(true);
                                }
                            }
                        });
                    }
                    else 
                    {
                        dialogItself.enableButtons(true);
                        dialogItself.setClosable(true);
                        $('.responsepass').html('<div class="alert alert-danger">Please input password.</div>');
                    }
                }
                else 
                {
                    dialogItself.enableButtons(true);
                    dialogItself.setClosable(true);
                }
                $('input[name=username]').focus();                                          
            }
        },{
            icon: 'glyphicon glyphicon-remove-sign',
            label: 'Close',
            cssClass: 'btn-default',
            action:function(dialogItself){
                dialogItself.close();
            }
        }]
    }); 
}

function updateReleasedGC()
{
    BootstrapDialog.show({
    	title: 'Confirmation',
        message: 'Update Released GC?',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
            $('#updateReleasedGC').prop("disabled",true);
        },
        onhidden:function(dialog){
        	$('#updateReleasedGC').prop("disabled",false);
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Yes',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
            	
            	$buttons = this;
            	$buttons.disable();                	
            	dialogItself.close();
				$.ajax({
		    		url:siteurl+'transaction/updateReleasedGC',
					beforeSend:function(){
						$('#processing-modal').modal('show');
					},
					success:function(data){
						console.log(data);
						var data = JSON.parse(data);
						$('#processing-modal').modal('hide');
						if(data['st'])
						{
							dialogItself.close();
							var dialog = new BootstrapDialog({
				            message: function(dialogRef){
				            var $message = $('<div>Released GC Updated.</div>');			        
				                return $message;
				            },
				            closable: false
					        });
					        dialog.realize();
					        dialog.getModalHeader().hide();
					        dialog.getModalFooter().hide();
					        dialog.getModalBody().css('background-color', '#0088cc');
					        dialog.getModalBody().css('color', '#fff');
					        dialog.open();
		               		setTimeout(function(){
		                    	window.location = siteurl;
		               		}, 1700);											
						}
						else 
						{
							alert(data['msg']);
							$buttons.enable();
						}
					}
				});
            }
        }, {
        	icon: 'glyphicon glyphicon-remove-sign',
            label: 'No',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    });
}

function updateUserList()
{
    BootstrapDialog.show({
    	title: 'Confirmation',
        message: 'Update User List?',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
            $('#updateUserList').prop("disabled",true);
        },
        onhidden:function(dialog){
        	$('#updateUserList').prop("disabled",false);
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Yes',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
            	
            	$buttons = this;
            	$buttons.disable();                	
            	dialogItself.close();
				$.ajax({
		    		url:siteurl+'transaction/updateUserListServerToStore',
					beforeSend:function(){
						$('#processing-modal').modal('show');
					},
					success:function(data){
						console.log(data);
						var data = JSON.parse(data);
						$('#processing-modal').modal('hide');
						if(data['st'])
						{
							dialogItself.close();
							var dialog = new BootstrapDialog({
				            message: function(dialogRef){
				            var $message = $('<div>User List Successfully Updated.</div>');			        
				                return $message;
				            },
				            closable: false
					        });
					        dialog.realize();
					        dialog.getModalHeader().hide();
					        dialog.getModalFooter().hide();
					        dialog.getModalBody().css('background-color', '#0088cc');
					        dialog.getModalBody().css('color', '#fff');
					        dialog.open();
		               		setTimeout(function(){
		                    	window.location = siteurl;
		               		}, 1700);											
						}
						else 
						{
							alert(data['msg']);
							$buttons.enable();
						}
					}
				});

            }
        }, {
        	icon: 'glyphicon glyphicon-remove-sign',
            label: 'No',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    });
}

function updateServerPendingGCRequest()
{
    BootstrapDialog.show({
    	title: 'Confirmation',
        message: 'Update Server?',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
            $('#updateServerBut').prop("disabled",true);
        },
        onhidden:function(dialog){
        	$('#updateServerBut').prop("disabled",false);
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Yes',
            cssClass: 'btn-primary',
            hotkey: 13,
            action:function(dialogItself){
            	
            	$buttons = this;
            	$buttons.disable();                	
            	dialogItself.close();
					$.ajax({
			    		url:siteurl+'transaction/updateGCRequestMainServer',
						beforeSend:function(){
							$('#processing-modal').modal('show');
						},
						success:function(data){
							console.log(data);
							var data = JSON.parse(data);
							$('#processing-modal').modal('hide');
							if(data['st'])
							{
								dialogItself.close();
								var dialog = new BootstrapDialog({
					            message: function(dialogRef){
					            var $message = $('<div>Main Server Updated.</div>');			        
					                return $message;
					            },
					            closable: false
						        });
						        dialog.realize();
						        dialog.getModalHeader().hide();
						        dialog.getModalFooter().hide();
						        dialog.getModalBody().css('background-color', '#0088cc');
						        dialog.getModalBody().css('color', '#fff');
						        dialog.open();
			               		setTimeout(function(){
			                    	window.location = siteurl;
			               		}, 1700);											
							}
							else 
							{
								alert(data['msg']);
								$buttons.enable();
							}
						}
					});
            }
        }, {
        	icon: 'glyphicon glyphicon-remove-sign',
            label: 'No',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    });
}

function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function checksession()
{
    setInterval(function() {
        $.ajax({
            url: siteurl+'User/checksession',
            success:function(data)
            {
                var data = JSON.parse(data);
                if(!data['st'])
                {
                    BootstrapDialog.closeAll();
                    var dialog = new BootstrapDialog({
                    message: function(dialogRef){
                    var $message = $('<div>Session already expired, Logging out...</div>');                 
                        return $message;
                    },
                    closable: false
                    });
                    dialog.realize();
                    dialog.getModalHeader().hide();
                    dialog.getModalFooter().hide();
                    dialog.getModalBody().css('background-color', '#0088cc');
                    dialog.getModalBody().css('color', '#fff');
                    dialog.open();
                    setTimeout(function(){
                        window.location.href =siteurl+'home';
                    }, 1500);    
                    //$('.responsechangepass').html('<div class="alert alert-danger alert-no-bot alertpad8">'+data['msg']+'</div>');
                }
            }
        });
        console.log('yeaahh');
    },40000); // 60000 milliseconds = one minute
    
}

function addNewCustomer()
{
    BootstrapDialog.show({
        title: 'Add New Customer',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        message: function(dialog) {
            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
            var pageToLoad = dialog.getData('pageToLoad');
            setTimeout(function(){
                $message.load(pageToLoad); 
            },1000);
          return $message;
        },
        data: {
            'pageToLoad': siteurl+'customer/addNewCustomerDialog',
        },
        cssClass: 'changeaccountpass',
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
        },
        onhidden: function(dialogRef){
            $('#numOnly').focus();
            flag=0;
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Add',
            cssClass: 'btn-success',
            hotkey: 13,
            action:function(dialogItself){   
                $('.response-dialog').html('');
                var $button = this;
                $button.disable();
                var postData = $('#_addcustomer').serialize(), formURL = $('#_addcustomer').attr("action");
                var fname = $('form#_addcustomer input#fnamedialog').val(), lname = $('form#_addcustomer input#lnamedialog').val();

                if(fname.trim()=='' || lname.trim()=='')
                {
                	$('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields.</div>');
                	$button.enable();
                	return false;
                }

                BootstrapDialog.show({
                    title: 'Confirmation',
                    message: 'Add New Customer?',
                    closable: true,
                    closeByBackdrop: false,
                    closeByKeyboard: true,
                    onshow: function(dialog) {
                        // dialog.getButton('button-c').disable();
                    },
                    buttons: [{
                        icon: 'glyphicon glyphicon-ok-sign',
                        label: 'Ok',
                        cssClass: 'btn-primary',
                        hotkey: 13,
                        action:function(dialogItself){
                            var $button1 = this;
                            $button1.disable();
                            $.ajax({
                                url:formURL,
                                data:postData,
                                type:'POST',
                                beforeSend:function(){
                                    $('#processing-modal').modal('show');
                                },
                                success:function(data)
                                {
                                    $('#processing-modal').modal('hide');
                                    console.log(data);
                                    var data = JSON.parse(data);
                                    if(data['st'])
                                    {
                                        BootstrapDialog.closeAll();
                                        var dialog = new BootstrapDialog({
                                        message: function(dialogRef){
                                        var $message = $('<div>Customer Successfully Added.</div>');                 
                                            return $message;
                                        },
                                        closable: false
                                        });
                                        dialog.realize();
                                        dialog.getModalHeader().hide();
                                        dialog.getModalFooter().hide();
                                        dialog.getModalBody().css('background-color', '#0088cc');
                                        dialog.getModalBody().css('color', '#fff');
                                        dialog.open();

                                        $('#cid').val(data['cusid']);

                                        $('#fname').val(data['fname']);
                                        $('#lname').val(data['lname']);
                                        $('#mname').val(data['mname']);
                                        $('#next').val(data['next']);

                                        $('#_vercussearch').val(data['fullname']);

                                        setTimeout(function(){                                            
                                        	$('#gcbarcodever').focus();
                                            dialog.close(); 
                                        }, 1500);
                                    }
                                    else 
                                    {
                                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">'+data['msg']+'</div>');
                                        $('form#_addcustomer input#fname').focus();
                                        $('#processing-modal').modal('hide');
                                        dialogItself.close();
                                        $button.enable();   
                                        return false;
                                    }
                                }
                            });                                                    
                        }
                    },{
                        icon: 'glyphicon glyphicon-remove-sign',
                        label: 'Cancel',
                        action: function(dialogItself){
                            dialogItself.close();
                            $button.enable();  
                        }
                    }]
                }); 

                return false;

            }
        }, {
          icon: 'glyphicon glyphicon-remove-sign',
            label: 'Cancel',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    }); 
}

