<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php if (isset($ptitle)) echo $ptitle . " - " ?>e-Accounting &copy;</title>
        <link rel="stylesheet" href="<?= base_url() ?>css/default/login2.css" type="text/css" />
        <!--[if lt IE 7]>
        <![endif]-->
        <!--[if IE ]>
        <link rel="stylesheet" href="<?= base_url() ?>css/default/login.ie.css" type="text/css" />
        <![endif]-->
        <link rel="shortcut icon" href="<?= base_url() ?>favicon.ico" />
        <link rel="icon" href="<?= base_url() ?>favicon.ico" type="image/x-icon" />
        <script type="text/javascript">
            //prepare appearance
            var root = '<?= base_url() ?>';
            var mod = '<?php if (isset($mod)) echo $mod; else if (isset($current_tab)) echo $current_tab; ?>';
        </script>
    </head>
    <body>
		<div class="container">
			<section id="content">
				<?= form_open('mod_user/user_auth'); ?>
					<h1>e-Accounting</h1>
						<div class="parent">
							<?php
								$message = $this->session->flashdata('messages');
								if (!empty($message)) {
									echo $message;
								}
								?>
							
							<?php echo ( ! empty($error) ? $error : '' ); ?>
							<div class="left" align="left">
								<img src="<?= base_url() ?>css/default/images/abipraya.png" width="90%" height="90%">
							</div>
							<div class="right">
								<div>
									<input type="text" placeholder="Username" required="" id="username" name="username"/>
								</div>
								<div>
									<input type="password" placeholder="Password" required="" id="password" name="password" />
								</div>
								<div>
									<input type="submit" value="Log in" />
									<!--<button type="submit"><span><i>Login</i></span></button>
									 <a href="#">Lost your password?</a> 
									<a href="#">Register</a>-->
								</div>
							</div>
							<div style="clear:both;"></div>
						</div>
								
					
				<?= form_close(); ?><!-- form -->
				<!--<div class="button">
					<a href="#">Download source file</a>
				</div> button -->
			</section><!-- content -->
		</div><!-- container -->
       <!--  <div class="outer">
            <div class="middle">
                <div class="inner mbox <?= 'mbox' . mt_rand(0, 2) ?>">
                    <div class="logbox">
                        <div class="outer">
                            <div class="middle">
                                <div class="inner">
                                    <div class="fpane">
                                        <?= form_open('mod_user/user_auth'); ?>
                                        <div class="fields">
                                            <h1>Login Portal</h1>
                                            <p>Masukkan username dan password Anda di bawah ini untuk login.</p>
                                            <p>
                                                <?php
                                                $message = $this->session->flashdata('messages');
                                                if (!empty($message)) {
                                                    echo $message;
                                                }
                                                ?>
                                                
                                                <?php echo ( ! empty($error) ? $error : '' ); ?>
                                            </p>
                                            <div class="frow">
                                                <div class="flabel"><label for="username"><u>L</u>ogin</label></div>
                                                <div class="field">
                                                    <input type="text" title="User Name" accesskey="l" size="20" id="username" name="username" class="tb rtb" value="<?= set_value('username'); ?>" />
                                                </div>
                                            </div>
                                            <div class="frow">
                                                <div class="flabel"><label for="password">Password</label></div>
                                                <div class="field">
                                                    <input type="password" title="Password" size="20" id="password" name="password" class="tb rtb" />
                                                </div>
                                            </div>
                                            <div class="frow">
                                                <div class="flabel">&nbsp;</div>
                                                <div class="field">
                                                    <input type="checkbox" name="remember_me" value="1"  /><label for="remember">Remember Me</label>
                                                </div>
                                            </div>
                                            <?php
                                            $counter = $this->session->userdata('counter');
                                            if ($counter > 5) {
                                                ?>
                                                <div class="frow">
                                                    <div class="flabel"><label for="security_code">Security Code</label></div>
                                                    <div class="field">
                                                        <img src="<?= base_url(); ?>captcha/normal" />
                                                    </div>
                                                </div>
                                                <div class="frow">
                                                    <div class="flabel"><label for="security_code"></label>&nbsp;</div>
                                                    <div class="field">
                                                        <input type="text" title="Security Code" size="20" id="security_code" name="security_code" class="tb rtb" />
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="frow">
                                                <div class="flabel">&nbsp;</div>
                                                <div class="field">
                                                    <button type="submit"><span><i>Login</i></span></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="response"></div>
                                        <?= form_close(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </body>
</html>
