<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_title ?></title>
    <link rel="stylesheet" href="<?= "{$css_path}vendor/bootstrap/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= "{$css_path}style/common-style.css" ?>">
</head>
<body>
    <div class="bg-primary h-100 w-100 position-fixed"></div>

    <div class="h-100 d-flex justify-content-center position-relative">
        <div class="main-content">
            <div id="mainSiteLogo" class="mt-3 mb-1 d-flex justify-content-center">
                <img src="<?= "{$img_path}logo/logo-jm.png" ?>" width="128">
            </div>
            <div class="panel panel-transparent mt-2 mb-4">
                <h3 class="text-center">Login Page</h3>
                <div id="notificationMsg">
                </div>
                <div class="w-100 my-4">
                    <form action="<?= base_url("auth/check_login") ?>" method="POST" id="loginForm" data-refresh="no-refresh">
                        <div class="my-3">
                            <div class="input-group">
                                <div class="input-group-prepend btn btn-primary rounded-0">
                                    <span><i class="fa fa-fw fa-user"></i></span>
                                </div>
                                <input type="text" name="usernameInput" class="form-control rounded-0" placeholder="Username..." id="usernameInput">
                            </div>
                            <div class="input-error">
                                <span id="usernameInputError"></span>
                            </div>
                        </div>

                        <div class="my-3">
                            <div class="input-group">
                                <div class="input-group-prepend btn btn-danger rounded-0">
                                    <span><i class="fa fa-fw fa-lock"></i></span>
                                </div>
                                <input type="password" name="passwordInput" class="form-control rounded-0" placeholder="Password..." id="passwordInput">
                                <abbr title="Tampilkan Password">
                                    <div class="input-group-append btn btn-info rounded-0 show-password" data-target-password="passwordInput">
                                        <span><i class="fa fa-fw fa-eye"></i></span>
                                    </div>
                                </abbr>
                            </div>

                            <div class="input-error">
                                <span id="passwordInputError"></span>
                            </div>
                        </div>

                        <div class="form-group position-relative">
                            <label class="container-chkbox w-75">
                                <small class="position-relative" style="top: -7px">Ingat saya (3 hari)</small>
                                <input type="checkbox" name="rememberMeInput" value="<?= $remember_me_value; ?>">
                                <span class="checkmark"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <input type="submit" name="btnSubmit" class="btn btn-success rounded-0" value="Login">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="<?= "{$js_path}vendor/jquery/jquery.min.js" ?>"></script>
    <script src="<?= "{$js_path}vendor/ckeditor/ckeditor.js" ?>"></script>
    <script src="<?= "{$js_path}vendor/bootstrap/bootstrap.bundle.min.js" ?>"></script>
    <script src="<?= "{$js_path}script/global-function.js" ?>"></script>
    <script src="<?= "{$js_path}/script/notification-function.js" ?>"></script>
    <script src="<?= "{$js_path}script/main.bundle.js" ?>"></script>
</body>
</html>