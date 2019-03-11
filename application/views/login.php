<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GC Login</title>
<link rel="shortcut icon" href="<?php echo base_url().'assets/img/favicon.ico'?>" type="image/icon">

<!--STYLESHEETS-->
<link href="<?php echo base_url().'assets/css/login.css'?>" rel="stylesheet" type="text/css" />

<!--SCRIPTS-->
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-1.10.2.js'?>"></script>
<!--Slider-in icons-->
<script type="text/javascript">
$(document).ready(function() {
	$(".username").focus(function() {
		$(".user-icon").css("left","-48px");
	});
	$(".username").blur(function() {
		$(".user-icon").css("left","0px");
	});
	
	$(".password").focus(function() {
		$(".pass-icon").css("left","-48px");
	});
	$(".password").blur(function() {
		$(".pass-icon").css("left","0px");
	});
});
</script>

</head>
<body>

<!--WRAPPER-->
<div id="wrapper">

	<!--SLIDE-IN ICONS-->
    <div class="user-icon"></div>
    <div class="pass-icon"></div>
    <!--END SLIDE-IN ICONS-->

	<!--LOGIN FORM-->
	<form name="login-form" class="login-form" action="<?php echo base_url(); ?>User/loginUser" method="post" id="login-form">

		<!--HEADER-->
	    <div class="header">
		<!-- 		<h2>Gift Check</h2>
				<p>Monitoring System</p> -->
			<div class="header-cont">				
				<img src="<?php echo base_url().'assets/img/gcicon.png'?>">
			</div>
	    </div>
	    <!--END HEADER-->
		<h4 style="text-align:center; font: 40px Helvetica, Sans-Serif; letter-spacing: -5px; color: rgba(0,0,255,0.5);">Store Server</h4>
		<!--CONTENT-->
	    <div class="content">
		<!--USERNAME--><input name="username" type="text" autofocus="on" placeholder="Username" class="input username" autocomplete="off" /><!--END USERNAME-->
	    <!--PASSWORD--><input name="password" type="password" placeholder="Password" class="input password" autocomplete="off" /><!--END PASSWORD-->
	    </div>
	    <!--END CONTENT-->
	    
	    <!--FOOTER-->
	    <div class="footer">
	    <!--LOGIN BUTTON--><input type="submit" name="submit" value="Login" class="button" id="btn-login" /><!--END LOGIN BUTTON-->    
	    </div>
	    <!--END FOOTER-->

	</form>
	<!--END LOGIN FORM-->
</div>
<!--END WRAPPER-->

<!--GRADIENT--><div class="gradient"></div><!--END GRADIENT-->

<script type="text/javascript">
	$(document).ready(function(){
		$("#login-form").submit(function(){
			$('input[type=submit]').val('Logging in...').attr('disabled','disabled');
			var postData = $(this).serializeArray();
			var formURL = $(this).attr("action");
			$.ajax({
				url:formURL,					
				type:'POST',
				data:postData,
				beforeSend:function(){									
				},
				success:function(data){	
					// alert(response);
					console.log(data);
					var  data = JSON.parse(data);
					if(data['st'])
					{						
						location.href="<?php echo base_url();?>Home/dashboard";
					} 
					else 
					{						
						$('.footer').html('<div class="login-response">'+data['msg']+'</div>');
						$('.username').setCaret(0);
						setTimeout(function(){
							$('.footer').fadeIn('slow').html('<input type="submit" name="submit" value="Login" class="button" id="btn-login" />');
						} , 1500);
						$('.logout-message').hide();
					}
				}
			});
			return false;
		});

		setTimeout(function(){
           	$('.logout-message').fadeOut(500).hide(600);
        }, 1700);

		jQuery.fn.setCaret = function (pos) {
		    var input = this[0];
		    if (input.setSelectionRange) {
		        input.focus();
		        input.setSelectionRange(pos, pos);
		    } else if (input.createTextRange) {
		        var range = input.createTextRange();
		        range.collapse(true);
		        range.moveEnd('character', pos);
		        range.moveStart('character', pos);
		        range.select();
		    } else if(input.selectionStart){
		        input.focus();
		        input.selectionStart = pos;
		        input.selectionEnd = pos;
		    }
		};
	});

</script>
</body>
</html>