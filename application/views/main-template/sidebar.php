<div id="sidebarContainer" class="text-white py-4 px-2" data-collapse="show">
        <div class="sidebar-link pb-4">
            <a href="<?= base_url("dashboard"); ?>"><i class="fa fa-fw fa-home mr-2"></i><span class="link-title">Dashboard</span></a>
        </div>

        <div class="sidebar-link pb-4" >
            <div class="sidebar-dropdown"  data-target="sidebar-dropdown-content">
                <div class="sidebar-dropdown-title"><i class="fa fa-fw fa-tag mr-2"></i><span class="dropdown-title">Kategori</span> <span class="dropdown-caret float-right"><i class="fa fa-fw fa-caret-right"></i></span></div>
            </div>

            <div class="sidebar-link mt-3 sidebar-dropdown-content" data-collapse="show">
                <small>MENU</small>
                <hr>
                <div class="sidebar-dropdown-link py-2">
                    <a href="<?= base_url("kategori_produk/daftar_kategori_produk") ?>">Daftar Kategori Produk</a>
                </div>
                <div class="sidebar-dropdown-link py-2">
                    <a href="<?= base_url("kategori_produk/tambah_kategori_produk") ?>">Tambah Kategori Produk</a>
                </div>
            </div>
        </div>

        <div class="sidebar-link pb-4">
            <div class="sidebar-dropdown" data-target="sidebar-dropdown-content">
                <div class="sidebar-dropdown-title"><i class="fa fa-fw fa-cubes mr-2"></i><span class="dropdown-title">Produk</span> <span class="dropdown-caret float-right"><i class="fa fa-fw fa-caret-right"></i></span></div>
            </div>
            <div class="sidebar-link mt-3 sidebar-dropdown-content" data-collapse="show">
                <small>MENU</small>
                <hr>
                <div class="sidebar-dropdown-link py-2">
                    <a href="<?= base_url("produk/daftar_produk") ?>">Daftar Produk</a>
                </div>
                <div class="sidebar-dropdown-link py-2">
                    <a href="<?= base_url("produk/tambah_produk") ?>">Tambah Produk</a>
                </div>

            </div>
        </div>
</div>