<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Owlet</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-language" content="en" />
		
		<!-- in production we compress and load with php -->
		<link href="/templates/css/reset.css" type="text/css" rel="stylesheet" />
		<link href="/templates/css/template.css" type="text/css" rel="stylesheet" />

		<script type="text/javascript" src="/templates/js/jquery-2.0.3.min.js"></script>
		<script type="text/javascript" src="/templates/js/components.js"></script>
	</head>

	<body>
		<div id="wrapper">
			<div id="container">
				<div class="headerElement">
					<h1>Owlets Are Friends With Short URLs</h1>
				</div>

				<?=$form?>

				<div id="responseElement">
				</div><!-- responseElement -->


				<?=$navigation?>
			</div><!-- end of container -->
		</div><!-- end of wrapper -->
	</body>

</html>