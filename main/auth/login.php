<?php 
function printContent() 
{
	global $action;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Jyotirmoy Saha">
    <title><?=WEBSITE_TITLE?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo CDN_URL; ?>css/bootstrap.min.css?v=<?php echo ASSETS_VERSION; ?>">
    <link href="<?php echo CDN_URL; ?>fa/css/all.min.css?v=<?php echo ASSETS_VERSION; ?>" rel="stylesheet">
    <link href="<?php echo CDN_URL; ?>css/admin/login.css?v=<?php echo ASSETS_VERSION; ?>" rel="stylesheet">
	<!-- toastr css -->
	<link href="<?php echo CDN_URL; ?>css/admin/toastr.min.css?v=<?php echo ASSETS_VERSION; ?>" rel="stylesheet">
	<link href="<?=CDN_URL; ?>css/admin/sweetalert.css?v=<?=ASSETS_VERSION; ?>" rel="stylesheet">
    <!-- title x-iccon -->
    <link rel="shortcut icon" href="<?php echo COMPANY_TITLE_LOGO_PATH; ?>" type="image/x-icon">
	<style type="text/css" rel="stylesheet">
			html, body {
				max-width: 100% !important;
				overflow-x: hidden !important;
			}
		<?php if(is_mobile() || is_tablet()): ?>
			#container
			{
				width: 93% !important;
			}
			#container .sign-up-container {
				display: none;
			}
			#container .sign-in-container {
				/* left: 47px !important; */
				width: 100% !important;
			}
			.overlay-container {
				display: none;
			}
			#user_id
			{
				width: 70%;
			}
		<?php endif; ?>
	</style>
</head>
<body>

<div class="row" style="margin-top: 20px;">
	<div class="col-sm-12 col-md-12 col-lg-12 text-center">
		<img class="img-fluid" src="<?=COMPANY_LOGO_PATH?>" style="width: 13%; padding-top: 5px;">
	</div>
	<div class="col-sm-12 col-md-12 col-lg-12 text-center mt-4">
		<h3><?=COMPANY_BUSINESS_HEADING?></h3>
		<h5><?=COMPANY_BUSINESS_SUB_HEADING?></h5>
	</div>
</div>
<!-- <small>Please Sign in to continue in Member's Area</small> -->
<!-- <button id="backToHome" class="<?= TOOLTIP_CLASS; ?>" title="Back To Home Page" style="margin-top:10px;">Back To Website</button> -->
<div class="container" id="container" style="margin-top: 10px; margin-bottom:20px;">
	<div class="form-container sign-up-container">
		<form action="#" <?php if(is_mobile()): ?> style="padding:0 !important;" <?php endif;?>>
			<h1>Create Account</h1>
			<div class="social-container d-none">
				<a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
				<a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
				<a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
			</div>
			<span>or use your email for registration</span>
			<input type="text" placeholder="Name" />
			<input type="email" placeholder="Email" />
			<input type="password" placeholder="Password" />
			<button>Sign Up</button>
		</form>
	</div>
	<div class="form-container sign-in-container">
		<form action="#" <?php if(is_mobile() || is_tablet()): ?> style="padding: 0px !important;" <?php endif; ?>>
			<h1>Sign in</h1>
			<div class="social-container d-none">
				<a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
				<a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
				<a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
			</div>
			<!-- <span>or use your account</span> -->
			<!-- <label>Email</label> -->
			<input type="text" placeholder="Email / Mobile" name="user_id" id="user_id" autocomplete="off" autofocus required onkeypress="checkRtype($(this)); enterEventListner($(this), $('#user_sign_in'));"/>
			<div class="wrapper">
				<input type="password" placeholder="Password" id="user_pass" autocomplete="off" onkeypress="enterEventListner($(this), $('#user_sign_in'));"/>
				<span style="cursor: pointer;">
					<i class="fa fa-eye" id="eye" onclick="toggle()">
					</i>
				</span>
			</div>
			<span style="color: red; font-weight:bold;display:none;" id="err_sp"></span>
			<a href="javascript:void:(0);" onclick="frgt_pass();">Forgot your password?</a>
			<button type="button" id="user_sign_in">Sign In</button>
			<div class="progress" style="width: 100%; margin-top:10px; display:none;" id="signin_progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
			</div>
		</form>
	</div>
	<div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-left">
				<h1>Welcome Back!</h1>
				<p>To keep connected with us please login with your personal info</p>
				<button class="ghost" id="signIn" <?php if(is_mobile()): ?> style="padding: 12px 19px; !important;" <?php endif;?>>Sign In</button>
			</div>
			<div class="overlay-panel overlay-right">
				<h1>Hello, Team !</h1>
				<p>Sign In To Explore Member's Area</p>
				<!-- <button class="ghost" id="signUp" <?php if(is_mobile()): ?> style="padding: 12px 19px; !important;" <?php endif;?>>Sign Up</button> -->
			</div>
		</div>
	</div>
</div>
<div>
	<p class="text-center" style="font-weight: 400 !important;">Made with love <span class="text-danger">&#10084;</span> by Adzguru (PNG) Limited, Papua New Guinea</p>
</div>
	<script>
      const DEF_AJAX_URL 		 = '<?php echo AJAX_URL; ?>';
      const DEBUG_APP 			 = <?php echo DEBUG_APP ? 'true' : 'false'; ?>,
	  AJAX_REQUEST               = '<?=AJAX_REQUEST; ?>';
      const IS_CONTENT_TYPE_JSON = <?php echo IS_CONTENT_TYPE_JSON ? 'true' : 'false'; ?>;
      const HOST_URL 			 = '<?php echo HOST_URL; ?>';
      const IS_MOBILE 			 = <?php if (is_mobile()) : ?> true <?php else : ?> false <?php endif; ?>;
      const IS_TABLET 			 = <?php if (is_tablet()) : ?> true <?php else : ?> false <?php endif; ?>;
      const IS_IOS_TABLET 		 = <?php if (is_ios_tablet()) : ?> true <?php else : ?> false <?php endif; ?>;        
      const CUR_DATE      		 = '<?php echo getToday(false); ?>';
      const CUR_DATE_TIME 		 = '<?php echo getToday(true); ?>',
			ISUSERLOGGEDIN       = <?php if(isUserLoggedIn()): ?>1<?php else: ?>0<?php endif; ?>,
  			CUSTOM_SPINNER_ID    = "<?=CUSTOM_SPINNER_ID?>",
  			TIME_ZONE    		 = "<?=TIME_ZONE?>",
			PAGE_ACTION          = '<?=$action; ?>';
	  var login_rtype			 = 'e';
    </script>
	<script src="<?=CDN_URL;?>js/jquery-3.6.0.min.js?v=<?=ASSETS_VERSION;?>"></script>
	<!-- Popper.JS -->
	<script src="<?php echo CDN_URL; ?>js/popper.min.js?v=<?php echo ASSETS_VERSION; ?>"></script>
	<!-- Bootstrap JS -->
	<script src="<?php echo CDN_URL; ?>js/bootstrap.min.js?v=<?php echo ASSETS_VERSION; ?>"></script>
	<script src="<?=CDN_URL;?>fa/js/all.min.js?v=<?=ASSETS_VERSION;?>"></script>
	<script src="<?=CDN_URL;?>js/login.js?v=<?=ASSETS_VERSION;?>"></script>
	<!-- toastr js -->
	<script src="<?php echo CDN_URL; ?>js/toastr.min.js?v=<?php echo ASSETS_VERSION; ?>"></script>
<script src="<?=CDN_URL; ?>js/sweetalert.min.js?v=<?=ASSETS_VERSION;?>"></script>
	<script src="<?=CDN_URL;?>js/common.js?v=<?php echo ASSETS_VERSION; ?>"></script>
	<script src="<?=CDN_URL;?>js/jquery_value_validation.js?v=<?=ASSETS_VERSION;?>"></script>
	<script src="<?=CDN_URL;?>js/main.js?v=<?=ASSETS_VERSION;?>"></script>
	<script>
		// const frgt_pass = () => {
		// 	alert("Please Contact Your Administrator !");
		// };
		$(document).ready(function () {
			<?php if((isset($_SESSION[ISSSEMSG])) && ($_SESSION[SEMSG])): ?>
				// clog("Session message Active");
				toastAlert("<?=$_SESSION[SEMSG]?>", "<?=$_SESSION[SEMSG_COLOR]?>");
			<?php 
				unset($_SESSION[ISSSEMSG]);
				unset($_SESSION[SEMSG]);
				unset($_SESSION[SEMSG_COLOR]);
				endif;
			?>
		});
		const enterEventListner = (onEle, triggerEle) => {
			onEle.on('keypress', function(e) {
				if (e.which == 13) {
					triggerEle.click();
				}
			});
		}
		var state= false;
		function toggle(){
			if(state){
				document.getElementById("user_pass").setAttribute("type","password");
				document.getElementById("eye").style.color='#7a797e';
				state = false;
			}
			else{
				document.getElementById("user_pass").setAttribute("type","text");
				document.getElementById("eye").style.color='#5887ef';
				state = true;
			}
		}
		$("#backToHome").on('click', () => {
			window.location.href = "#";
			return false;
		});
		$('.<?php echo TOOLTIP_CLASS; ?>').tooltip();
	</script>
</body>
</html>
<?php } ?>