<div class="container-fluid mt-5 py-3">
    <div class="row mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 col-lg-6 col-xl-6 offset-md-2 offset-lg-3 offset-xl-3">
                    <div class="card w-100 rounded-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $site_title ?></h5>
                            <div id="notificationMsg">
                            </div>
                            <form action="<?= base_url("kategori_produk/input_edit_subkategori_produk") ?>" id="formInputSubmit" data-refresh="no-refresh">
                                <div class="w-100 mt-4">
                                    <input type="hidden" name="idKategoriInput" value="<?= $idKategori; ?>" readonly>
                                    <input type="hidden" name="idSubKategoriInput" value="<?= $idSubKategori; ?>" readonly>
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control rounded-0" name="namaSubKategoriInput" placeholder="Nama Kategori" value="<?= $namaSubKategori; ?>">
                                        <span class="input-error text-danger" id="namaSubKategoriInputError"></span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control rounded-0" name="subKategoriLinkInput" placeholder="Kategori Link (Opsional)">
                                        <span class="input-error text-danger" id="subKategoriLinkInputError"></span>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-success rounded-0"><i class="fa fa-fw fa-paper-plane"></i> Submit</button>
                                        <button type="button" class="btn btn-danger rounded-0"><i class="fa fa-fw fa-undo"></i> Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
