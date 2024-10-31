<?php 
	
	header('Access-Control-Allow-Origin: *'); 

	if(!isset($_GET['code'])){

		die();

	}

	$code = $_GET['code'];

?>
<!DOCTYPE html>
<html>
<head>

	<meta name="robots" content="noindex,nofollow">

	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel='stylesheet' id='s3bubble-hosted-cdn-css'  href='<?php echo S3BUBBLE_OEMBED_PLUGIN_URL . '/dist/css/s3bubble.min.css?ver=' . S3BUBBLE_OEMBED_PLUGIN_VERSION; ?>' type='text/css' media='all' /> 

	<style type="text/css">
		html, body {
			overflow: hidden;
			margin: 0px;
    		background: black;
		}
	</style>

	<script type="text/javascript">

		/*if(window.location.href === window.top.location.href) {

    		window.location.href = window.location.protocol + '//' + window.location.host;

		}*/

	</script>

</head>
<body>

	<div class="s3bubble" data-code="<?php echo $code; ?>"></div>
	
	<?php s3bubble_oembed_amp_iframe_scripts(); ?>

	<script type='text/javascript' src='<?php echo S3BUBBLE_OEMBED_PLUGIN_URL . '/dist/js/s3bubble.min.js?ver=' . S3BUBBLE_OEMBED_PLUGIN_VERSION; ?>' id='s3bubble-hosted-cdn-js'></script>

</body>
</html>