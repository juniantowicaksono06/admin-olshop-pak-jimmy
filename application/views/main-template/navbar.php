<div class="container-fluid">
    <div class="row">
        <!--  Navbar -->
        <nav class="navbar fixed-top w-100" style="background-color: #099cec; z-index:99;">
            <a class="navbar-brand" href="<?= base_url("dashboard"); ?>" style="margin: 0; padding:0">
                <img src="<?= "{$img_path}/logo/logo-jm.png"; ?>" style="width: 5rem">
            </a>
            <div class="d-inline-block position-absolute text-white cursor-pointer" style="left: 110px" id="btnToggleSidebar" data-target="sidebarContainer">
                <span class="i fa fa-fw fa-bars" style="font-size: 32px"></span>
            </div>
            <!-- Pengaturan Pengguna -->
            <div class="float-right positon-relative">
                <div class="d-inline text-white no-select cursor-pointer" id="toggleUserMenuPanel" data-target="dropdown-panel">
                <?php 
                    if(empty($auth_user_photo_profile)) {
                ?>
                    <span><i class="fa fa-fw fa-user" style="font-size: 1.5rem;" ></i></span>
                <?php
                    }
                    else {
                ?>  
                    <img src="<?= "{$img_path}/icons/REM.jpg" ?>" width="48" class="rounded-circle mr-1">
                    
                <?php
                    }
                ?>
                    <span><?= $auth_username ?></span>
                    <div class="dropdown-panel dropdown-panel-absolute" data-show="show">
                        <a href="<?= base_url('auth/logout'); ?>" class="text-black no-underline"><i class="fa fa-fw fa-sign-out"></i> Logout</a>
                    </div>

                </div>
                <!-- Akhir Pengaturan Pengguna -->
            </div>
        </nav>
        <!-- Akhir Navbar -->
    </div>
</div>