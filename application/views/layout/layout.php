<!DOCTYPE html><html>	<head>		<!-- Meta data -->		<meta charset="utf-8" />		<title><?php echo $title; ?></title>                <link rel="stylesheet" href="/css/screen.css">	</head>	<body>		<div id="wrapper" class="other-info">            <div id="oi-machine">                <a id="puncher-button" href="/" title="Accueil">                    <span>&#xF011;</span> Puncher !                </a>            </div>            <div id="oi-content">                            <?php if (validation_errors() != "" || count($errors) > 0) { ?>                    <div class="wrong">                        <?php echo validation_errors(); ?>                        <?php                             foreach ($errors as $key => $value) {                                echo $value;                            }                        ?>                    </div>                <?php } ?>                                <?php echo $content; ?>            </div>        </div>	</body></html>