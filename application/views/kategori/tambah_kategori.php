<div class="container-fluid mt-5 py-3">
    <div class="row mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 col-lg-6 col-xl-6 offset-md-2 offset-lg-3 offset-xl-3">
                    <div class="card w-100 rounded-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $site_title ?></h5>
                            <div id="notificationMsg">
                            </div>
                            <div class="w-100 mt-4">
                                <form action="<?= base_url("kategori_produk/input_kategori_produk/add_category_input") ?>" id="formInputSubmit" data-refresh="refresh">
                                    <div id="inputSection">
                                        <div class="form-group mb-2">
                                            <div class="mb-2">
                                                <span>Nama Kategori</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Nama Kategori" name="kategoriInput">
                                            <span id="kategoriInputError" class="input-error text-danger"></span>
                                        </div>
                                        <div class="form-group mb-2">
                                            <div class="mb-2">
                                                <span>Kategori Link</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Kategori Link (Opsional)" name="kategoriLinkInput">
                                            <span id="kategoriLinkInputError" class="input-error text-danger"></span>
                                        </div>

                                        <!-- Default Sub Kateogri Input -->
                                        <div class="form-group mb-2">
                                            <div class="mb-2">
                                                <span>Nama Sub Kategori</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0 subkategori-input" placeholder="Nama Sub Kategori" data-subkategori-input-index="1" name="subKategoriInput1">
                                            <span id="subKategoriInput1Error" class="input-error text-danger"></span>
                                        </div>

                                        <div class="form-group mb-2">
                                            <div class="mb-2">
                                                <span>Sub Kategori Link</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0 subkategori-link-input" placeholder="Sub Kategori Link (Opsional)" name="subKategoriLinkInput1">
                                            <span id="subKategoriLinkInput1Error" class="input-link-error input-error  text-danger"></span>
                                        </div>

                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary rounded-0" id="btnTambahInputSubKategori" data-target="subkategori-input" data-target-link="subkategori-link-input">
                                                <i class="fa fa-fw fa-plus"></i> <span>Tambah Input Sub Kategori</span>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Tombol Submit -->
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-success rounded-0"><i class="fa fa-fw fa-paper-plane"></i> Submit</button>
                                        <button type="button" class="btn btn-danger rounded-0"><i class="fa fa-fw fa-undo"></i> Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>