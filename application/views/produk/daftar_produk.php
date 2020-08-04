<div class="container-fluid mt-5 py-3">
    <div class="row mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 col-lg-6 col-xl-6 offset-md-2 offset-lg-3 offset-xl-3">
                    <div class="card w-100 rounded-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $site_title ?></h5>
                            <div id="notificationMsg">
                                <?= $this->session->flashdata("notifyMsgSuccess"); ?>
                                <?= $this->session->flashdata("notifyMsgFailed"); ?>
                            </div>
                            <div class="product-option mb-3">
                                <div class="my-2">
                                    <a href="<?= base_url("produk/tambah_produk") ?>" class="btn btn-primary rounded-0"><i class="fa fa-fw fa-plus"></i> Tambah Produk</a>
                                </div>
                                <form action="<?= base_url('produk/daftar_produk') ?>" method="get" class="submit-get-form">
                                    <div class="row">
                                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12 mb-2">
                                            <div>
                                                <span style="font-size: 14px">Pilih Kategori</span>
                                            </div>
                                            <select name="id_subkategori_input" class="selectpicker w-100" id="" title="Silahkan Pilih Kategori" data-live-search="true">
                                                <?= $category_list ?>
                                            </select>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                                            <div>
                                                <span style="font-size: 14px">Per Halaman</span>
                                            </div>
                                            <select name="per_page" class="selectpicker w-100" id="">
                                                <?= $pagination_per_page; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-12 mb-2">
                                            <div>
                                                <span style="font-size: 14px">Cari Produk</span>
                                            </div>
                                            <div class="input-group">
                                                <input name="search_product_name" type="text" class="form-control rounded-0" value="<?= $search_product; ?>" placeholder="Cari Produk...">
                                                <div class="input-append">
                                                    <abbr title="Cari Produk">
                                                        <button type="submit" class="btn btn-primary rounded-0"><i class="fa fa-fw fa-search"></i></button>
                                                    </abbr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(!empty($data_filter_found)): ?>
                                        <div class="row mx-1 mb-2">
                                            <span class="text-success total-rows-found"><?= $data_filter_found; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <!-- <div class="d-inline-flex w-100">
                                            <select name="per_page" class="selectpicker mr-2" id="">
                                                <?= $pagination_per_page; ?>
                                            </select>
                                            <select name="id_subkategori_input" class="selectpicker mr-2" id="" title="Silahkan Pilih Kategori" data-live-search="true">
                                                <?= $category_list ?>
                                            </select>
                                            <div class="position-relative ml-auto" style="right: 0">
                                                <div class="input-group">
                                                    <input name="search_product_name" type="text" class="form-control rounded-0" value="<?= $search_product; ?>" placeholder="Cari Produk...">
                                                    <div class="input-append">
                                                        <abbr title="Cari Produk">
                                                            <button type="submit" class="btn btn-primary rounded-0"><i class="fa fa-fw fa-search"></i></button>
                                                        </abbr>
                                                    </div>
                                                </div>
                                            </div>
                                    </div> -->
                                </form>
                            </div>
                            <div class="container-fluid">
                                <div class="row" id="productContainer">
                                <?= $product_list; ?>
                                    <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12  mb-2">
                                        <div class="card w-100">
                                            <div class="card-body">
                                                <img src="<?= base_url('uploads/gambar_produk/0remz7srs3fb3il8p0m8x4911mt08cxint42ehahy.jpg') ?>" class="w-100" alt="">
                                                <div class="mt-3">
                                                    <div class="product-title card-title">
                                                        <h5 class="text-justify">Tes</h5>
                                                    </div>
                                                    <div class="product-price mb-2">
                                                        <span>Rp. 2.500.255</span>
                                                    </div>
                                                    <div class="product-action">
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-primary btn-sm rounded-0"><i class="fa fa-fw fa-edit"></i></button>
                                                        </abbr>
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-danger btn-sm rounded-0"><i class="fa fa-fw fa-trash"></i></button>
                                                        </abbr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12  mb-2">
                                        <div class="card w-100">
                                            <div class="card-body">
                                                <img src="<?= base_url('uploads/gambar_produk/0remz7srs3fb3il8p0m8x4911mt08cxint42ehahy.jpg') ?>" class="w-100" alt="">
                                                <div class="mt-3">
                                                    <div class="product-title card-title">
                                                        <h5 class="text-justify">Tes</h5>
                                                    </div>
                                                    <div class="product-price mb-2">
                                                        <span>Rp. 2.500.255</span>
                                                    </div>
                                                    <div class="product-action">
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-primary btn-sm rounded-0"><i class="fa fa-fw fa-edit"></i></button>
                                                        </abbr>
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-danger btn-sm rounded-0"><i class="fa fa-fw fa-trash"></i></button>
                                                        </abbr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12  mb-2">
                                        <div class="card w-100">
                                            <div class="card-body">
                                                <img src="<?= base_url('uploads/gambar_produk/0remz7srs3fb3il8p0m8x4911mt08cxint42ehahy.jpg') ?>" class="w-100" alt="">
                                                <div class="mt-3">
                                                    <div class="product-title card-title">
                                                        <h5 class="text-justify">Tes</h5>
                                                    </div>
                                                    <div class="product-price mb-2">
                                                        <span>Rp. 2.500.255</span>
                                                    </div>
                                                    <div class="product-action">
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-primary btn-sm rounded-0"><i class="fa fa-fw fa-edit"></i></button>
                                                        </abbr>
                                                        <abbr title="Edit Produk">
                                                            <button class="btn btn-danger btn-sm rounded-0"><i class="fa fa-fw fa-trash"></i></button>
                                                        </abbr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="mt-3" id="paginationContainer">
                                    <?= $pagination; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>