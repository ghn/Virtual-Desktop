<?php
	if (isset($_POST['vd_auth_login']) && isset($_POST['vd_auth_password'])) {
		
		$parameters = array(
			'login'		=> $_POST['vd_auth_login'],
			'password'	=> $_POST['vd_auth_password']);
		
		require_once ('lib/CDrive.php');
		require_once ('lib/CAuth.php');
		$auth = new CAuth($parameters);
	}
?>

<html>
	<head>
		<title>Authentication page, please log in</title>
		<style type="text/css">
			body {
				margin: 0;
				padding: 0;
			}
			div#page {
			}
				div#header {
					border-bottom: 3px solid #ffda76;
					padding: 20px 50px;
				}
					div#header h1 {
						margin: 0;
						padding: 0;
						text-align: left;
						font-family: lucida, georgia, verdana;
						color: #4e4632;
					}
				div#about {
					margin-right: 345px;
					padding: 20px;
					padding-left: 50px;
					min-height: 200px;
				}
					div#about h2 {
						font-family: lucida, georgia, verdana;
						color: #4e4632;
					}
				div#auth {
					position: absolute;
					top: 100px;
					right: 15px;
					width: 300px;
					padding: 10px;
					padding-bottom: 50px;
					
					background: #ffeec1;
					border: 1px solid #ffda76;
					border-width: 0 1px 1px 0;
				}
					div#auth h2 {
						font-family: lucida, georgia, verdana;
						color: #4e4632;
					}
					div#auth p.field {
						width: 220px;
						clear: both;
						padding-bottom: 10px;
						disply: inline;
					}
						div#auth p.field label {
							float: left;
						}
						div#auth p.field input {
							float: right;
						}
				div#footer {
					border-top: 1px solid #ffda76;
					padding: 10px 50px;
					text-align: right;
				}
		</style>
		
		<script type="text/javascript">
			window.onload = function() {
				document.getElementById("vd_auth_login").focus();
			}
		</script>
	</head>
	
	<body>
		<div id="page">
			<div id="header">
				<h1>Virtual Desktop</h1>
			</div>
			
			<div id="about">
				<h2>Welcome on board!</h2>
			</div>
					
			<div id="auth">
				<h2>Login here please</h2>
				<form name="vd_auth_form" method="post">
					<p class="field">
						<label for="vd_auth_login">Login</label>
						<input type="text" id="vd_auth_login" name="vd_auth_login" />
					</p>
					<p class="field">
						<label for="vd_auth_password">Password</label>
						<input type="password" id="vd_auth_password" name="vd_auth_password" />
					</p>
					<p class="field">
						<input type="submit" value="Log in" />
					</p>
				</form>
			</div>
			
			<div id="footer">
				<p class="copyright">&copy; 2007-<?php print date('Y', time()); ?> germain.cn | <a href="thanks.html" rel="lightbox[external 640 360]" title="Credits">Credits</a></p>
			</div>
		</div>
	</body>
</html>