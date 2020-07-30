<div class="container-fluid mt-5 py-3">
    <div class="row mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-8 col-lg-8 col-md-10 offset-xl-2 offset-lg-2 offset-md-1">
                    <div class="card w-100 rounded-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $site_title ?></h5>
                            <div id="notificationMsg">
                            </div>
                            <div class="w-100 mt-4">
                            <div class="py-2">
                                <a href="<?= base_url("kategori_produk/tambah_kategori_produk"); ?>" class="btn btn-primary rounded-0"><i class="fa fa-fw fa-plus"></i> Tambah Kategori Produk</a>
                            </div>
                            <div class="row category-list-container">
                                <?= $pagination_content; ?>
                            </div>
                            <div class="mt-3 pagination-container">
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