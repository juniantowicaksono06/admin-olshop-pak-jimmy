<div class="container-fluid mt-5 py-3">
    <div class="row mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card w-100 rounded-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $site_title ?></h5>
                            <div id="notificationMsg">
                            </div>
                            <div class="product-option mb-3">
                                <div class="my-2">
                                    <abbr title="Tambah Produk">
                                        <a href="<?= base_url("produk/tambah_produk") ?>" class="btn btn-primary rounded-0"><i class="fa fa-fw fa-plus"></i> <span class="d-none d-sm-inline" style="font-size: 14px;">Tambah Produk</span></a>
                                    </abbr>
                                    <abbr title="Edit Produk">
                                        <a href="<?= base_url("produk/edit_produk?idProduk={$id_product}") ?>" class="btn btn-info rounded-0"><i class="fa fa-fw fa-edit"></i> <span class="d-none d-sm-inline" style="font-size: 14px;">Edit Produk</span></a>
                                    </abbr>
                                    <abbr title="Hapus Produk">
                                        <a href="<?= base_url("produk/input_hapus_produk?idProduk={$id_product}") ?>" class="btn btn-danger rounded-0" id="btnHapusProductFromDetail"><i class="fa fa-fw fa-trash"></i> <span class="d-none d-sm-inline" style="font-size: 14px;">Hapus Produk</span></a>
                                    </abbr>
                                </div>
                            </div>
                            <div class="product-detail-mb-3">
                                <?= $product_detail; ?>

                                <!-- <div class="container-fluid">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12 mb-3">
                                            <div class="py-1 px-1 mb-2">
                                                <img src="<?= base_url("uploads/gambar_produk/sr1djkebgltcgxjro5d87uvx607dyo5knm2y94t8f.jpg") ?>" class="w-100" id="imgPreviewMain">
                                            </div>
                                            <div id="carouselExampleSlidesOnly" class="carousel slide d-flex" data-interval="false">
                                                <a href="#carouselExampleSlidesOnly" role="button" data-slide="prev" class="align-self-center">
                                                    <i class="fa fa-fw fa-chevron-left text-primary"></i>
                                                </a>
                                                <div class="carousel-inner">
                                                    <div class="carousel-item active">
                                                        <div class="row justify-content-center" style="border: 1px">
                                                            <div class="col-3 img-list-box img-list-box-active py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/ayjam5ayus1osuob5rgeihoa77dky06tvalykl134.jpg") ?>" alt="First slide">
                                                            </div>
                                                            <div class="col-3 img-list-box py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/0kw7mryjd53zsbn13hx4w4x2zz8m21r3lcwghsgf4.jpg") ?>" alt="First slide">
                                                            </div>
                                                            <div class="col-3 img-list-box py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/0kw7mryjd53zsbn13hx4w4x2zz8m21r3lcwghsgf4.jpg") ?>" alt="First slide">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="carousel-item">
                                                        <div class="row justify-content-center" style="border: 1px">
                                                            <div class="col-3 img-list-box py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/ayjam5ayus1osuob5rgeihoa77dky06tvalykl134.jpg") ?>" alt="First slide">
                                                            </div>
                                                            <div class="col-3 img-list-box py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/0kw7mryjd53zsbn13hx4w4x2zz8m21r3lcwghsgf4.jpg") ?>" alt="First slide">
                                                            </div>
                                                            <div class="col-3 img-list-box py-1 px-1 mx-1 cursor-pointer"  data-image-preview-target="imgPreviewMain">
                                                                <img class="d-block w-100" src="<?= base_url("uploads/gambar_produk/0kw7mryjd53zsbn13hx4w4x2zz8m21r3lcwghsgf4.jpg") ?>" alt="First slide">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="#carouselExampleSlidesOnly" role="button" data-slide="next" class="align-self-center">
                                                    <i class="fa fa-fw fa-chevron-right text-primary"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-xl-7 col-lg-7 col-md-5 col-sm-12 col-12 px-2">
                                            <div class="product-title">
                                                <h2><strong>TeS1234</strong></h2>
                                            </div>
                                            <div class="product-price">
                                                <h5>Rp. <span class="text-rupiah">600000</span></h5>
                                            </div>
                                            <div class="product-description">
                                                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Sequi laboriosam ex vero reiciendis, nostrum ipsum qui optio quas magni delectus itaque. Nesciunt deleniti voluptas, enim nam quaerat mollitia. Vero, quasi.
                                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Id explicabo ullam unde blanditiis culpa enim quia nulla at accusantium aut? Voluptatibus aut optio error nostrum rem porro temporibus sed eius.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
