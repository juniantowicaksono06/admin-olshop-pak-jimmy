<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= "{$css_path}/vendor/bootstrap/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= "{$css_path}style/common-style.css" ?>">
    <title><?= $site_title; ?></title>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .no-select {
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            -moz-user-select: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid w-100 h-100">
        <div class="row w-100 h-100">
            <div class="d-flex w-100 justify-content-center">        
                <div class="w-100 align-self-center text-center">
                    <img src="<?= "{$img_path}logo/logo-jm.png" ?>" width="128">
                    <div>
                        <h4 class="no-select mt-2"><?= $detail_err400; ?></h4>
                        <a href="<?= base_url() ?>" class="btn btn-primary rounded-0 mt-2">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{$js_path}/vendor/jquery/jquery.min.js"></script>
    <script src="{$js_path}/vendor/bootstrap/bootstrap.min.js"></script>
</body>
</html>