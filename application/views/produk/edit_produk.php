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
                <form action="<?= base_url("produk/input_tambah_produk/edit") ?>" id="formInputSubmit">
                  <input type="hidden" readonly name="idProdukInput" value="<?= $id_product ?>">
                  <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                      <div class="mb-2">
                        <span>Nama Produk</span>
                      </div>
                      <input type="text" class="form-control rounded-0" name="namaProdukInput" id="namaProdukInput" placeholder="Nama Produk" value="<?= $product_list->namaProduk; ?>">
                      <div>
                        <span class="input-error text-danger" id="namaProdukInputError"></span>
                      </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                      <div class="mb-2">
                        <span>Harga Produk</span>
                      </div>
                      <div class="input-group">
                        <div class="input-prepend">
                          <span class="btn btn-dark rounded-0">Rp.</span>
                        </div>

                        <input type="text" class="form-control rounded-0 money-rupiah" id="hargaProdukInput" name="hargaProdukInput" value="<?= number_format($product_list->hargaProduk, 0, ".", "."); ?>" placeholder="Harga Produk">
                      </div>
                      <div>
                        <span class="input-error text-danger" id="hargaProdukInputError"></span>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="mb-2">
                        <span>Kategori Produk</span>
                      </div>
                      <select class="selectpicker w-100" id="kategoriProdukInput" data-live-search="true" name="kategoriProdukInput" title="Silahkan Pilih Kategori">
                        <?= $category_list; ?>
                      </select>
                      <div>
                        <span class="input-error text-danger" id="kategoriProdukInputError"></span>
                      </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                      <div class="mb-2">
                        <span>Stok Produk</span>
                      </div>
                      <input type="text" id="stokProdukInput" name="stokProdukInput" class="form-control rounded-0" value="<?= $product_list->stok; ?>">
                      <div>
                        <span class="input-error text-danger" id="stokProdukInputError"></span>
                      </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                      <div class="mb-2">
                        <span>Berat Produk</span>
                      </div>
                      <input type="text" id="beratProdukInput" name="beratProdukInput" class="form-control rounded-0" value="<?= $product_list->berat ?>">
                      <div>
                        <span class="input-error text-danger" id="beratProdukInputError"></span>
                      </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                      <div class="mb-2">
                        <span>Diskon Produk</span>
                      </div>
                      <div class="input-group">
                        <input type="text" id="diskonProdukInput" name="diskonProdukInput" class="form-control number-colon-only rounded-0" value="<?= $product_list->diskon ?>">
                        <div class="input-append">
                          <span class="btn btn-dark rounded-0">%</span>
                        </div>

                      </div>
                      <div>
                        <span class="input-error text-danger" id="diskonProdukInputError"></span>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="mb-2">
                        <span>Deskripsi Produk</span>
                      </div>
                      <textarea name="deskripsiProdukInput" id="deskripsiProdukInput" cols="30" rows="10" class="form-control rounded-0 ckeditor"><?= $product_list->deskripsiProduk ?></textarea>
                      <div>
                        <span class="input-error text-danger" id="deskripsiProdukInputError"></span>
                      </div>
                    </div>
                    <?= $image_list ?>

                    <!-- <div class="col-xl-12 col-lg-12">
                      <div class="row">
                        <div class="col-md-4 col-sm-12 col-12 px-4 mb-4">
                          <img src="<?= base_url("uploads/gambar_produk/7scs27yrh781s44hr8fkqtekqh6m6wsborsvbo3g2.jpg") ?>" class="w-100">
                        </div>
                        <div class="col-md-4 col-sm-12 col-12 px-4 mb-4">
                          <img src="<?= base_url("uploads/gambar_produk/7scs27yrh781s44hr8fkqtekqh6m6wsborsvbo3g2.jpg") ?>" class="w-100">
                        </div>
                        <div class="col-md-4 col-sm-12 col-12 mb-4">
                          <div class="card">
                            <div class="card-body">
                              <div class="px-2 mb-4">
                                <img src="<?= base_url("uploads/gambar_produk/7scs27yrh781s44hr8fkqtekqh6m6wsborsvbo3g2.jpg") ?>" class="w-100">
                              </div>
                              <div class="mb-2">
                                <a href="#" class="btn btn-danger btn-sm btn-block rounded-0"><i class="fa fa-fw fa-trash"></i> Hapus Gambar</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> -->

                    <div class="col-lg-12 mt-2 mb-2">
                      <div class="mb-2">
                        <h4>Upload Gambar Produk</h4>
                      </div>
                      <div class="file-upload-style position-relative">
                        <input type="file" multiple class="rounded-0 w-100 h-100 opacity-0 position-absolute" name="produkImgInput" id="produkImgInput" accept="image/jpg, image/png, image/jpeg">
                        <div class="d-flex h-100 justify-content-center">
                          <div class="align-self-center text-center">
                            <span style="font-size: 3.8rem"><i class="fa fa-fw fa-upload"></i></span>
                            <div>
                              <h3>Silahkan Klik atau Tarik Gambar Ke Sini</h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <button type="submit" name="btnSubmitInput" class="btn btn-success rounded-0" id="btnSubmitInput">
                        <i class="fa fa-fw fa-paper-plane"></i> Input
                      </button>
                      <button type="reset" class="btn btn-danger rounded-0">
                        <i class="fa fa-fw fa-undo"></i> Reset
                      </button>
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
</div>