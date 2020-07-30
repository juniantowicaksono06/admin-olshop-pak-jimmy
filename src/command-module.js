// Command Module
const Command = function(cmd, value = "", config = {}) {
    this.cmd = cmd;
    this.cmdValue = value;
    this.config = config;
    this.execute = function() {
        if(this.cmdValue == "" || this.cmdValue.length == 0 || (objectLength(this.cmdValue) == 0 && !this.cmdValue instanceof Element)) {
            this.cmd();
        }
        else {
            if(objectLength(this.config) > 0) {
                this.cmd.call({}, this.cmdValue, this.config);
            }
            else {
                this.cmd.call({}, this.cmdValue);
            }
        }
    };
}

export const ShowPasswordCommand = function(elem) {
    return new Command(showPassword, elem);
}

// Perintah untuk menutup element seperti user panel ketika user klik diluar konten tersebut
export const OffCollapseElementCommand = function(data) {
    if(data instanceof Object) {
        const eventTarget = data.eventTarget;
        const targetList = data.targetList;
        const excludeTarget = data.excludeTarget;
        var match = false;
        for(let i = 0; i < excludeTarget.length; i++) {
            if($(excludeTarget[i]).find($(eventTarget)).length >= 1 || $(excludeTarget[i]).is($(eventTarget))) {
                match = true;
            }
            $(excludeTarget[i]).children().each(function(index, elem) {
                if($(elem).find(eventTarget).length >= 1) {
                    match = true;
                    return;
                }
            })
        }
        

        if(!match) {
            for(let a = 0; a < targetList.length; a++) {
                Event.execute(new targetList[a].command({
                    'target': targetList[a].target,
                    'collapse': targetList[a].collapse
                }))
            }
        }
    }
}


// Tambahkan gambar dari input type file ke daftar gambar yang akan diupload
export const AddImgUploadListQueueCommand = function(elem) {
    return new Command(addImgUploadListQueue, elem);
}

export const ToggleUserMenuPanelCommand = function(elem) {
    return new Command(toggleUserMenuPanel, elem);
}
// Tambah input sub kategori input command
export const AddSubCategoryInputCommand = function(elem) {
    return new Command(addSubCategoryInput, elem);
}
// Hapus kategori command
export const RemoveCategoryCommand = function(elem, config) {
    return new Command(removeCategory, elem, config);
}

export const RemoveSubCategoryInputCommand = function(elem) {
    return new Command(removeSubCategoryInput, elem);
}
// Hapus sub kategori command
export const RemoveSubCategoryCommand = function(elem, config) {
    return new Command(removeSubCategory, elem, config);
}

// Input uang format rupiah command

export const MoneyFormatRupiahInputCommand = function(value) {
    return new Command(moneyFormatRupiahInput, value);
}
// Command untuk menghapus content saat diklik
export const RemoveContentFromDOMCommand = function(value) {
    const t = value;
    return new Command(removeContentFromDOM, t);
}

// Command untuk menghapus file dari sessionStorage
export const RemoveFileFromSessionStorageCommand = function(value) {
    return new Command(removeFileFromSessionStorage, value);
}

// Hapus file dari sessionStorage
function removeFileFromSessionStorage(element) {
    let fileName = $(element).attr('data-file-name');
    if((fileName != undefined || fileName != null) && (sessionStorage.getItem('files') != null || sessionStorage.getItem('files') != undefined)) {
        let files = JSON.parse(sessionStorage.getItem('files'));
        delete files[`${fileName}`];
        if(objectLength(files) == 0) {
            sessionStorage.removeItem('files');
        }
        else {
            sessionStorage.setItem('files', JSON.stringify(files));
        }
    }
}

function removeContentFromDOM(element) {
    let target = $(element).attr('data-target');
    let parentContainer = $(element).attr('data-parent-container-target');
    let remove = "";
    if($(element).closest(`.${target}`).length > 0) {
        $(element).closest(`.${target}`).fadeOut('slow', function() {
            $(this).remove();
        });
    }
    else if($(element).children().find(`.${target}`).length > 0) {
        $(element).children().find(`.${target}`).fadeOut('slow', function() {
            $(this).remove();
        });
    }
};

function addImgUploadListQueue(elem) {
    const files = $(elem.target).prop('files');
    const file_type = [];

    let file_store = sessionStorage.getItem('files') == undefined ? Object.create(null) : JSON.parse(sessionStorage.getItem('files'));

    for(let x = 0; x < files.length; x++) {
        (async function(x) {
            await checkImgMimeType(files[x]).then(async function (fullfill) {
                let msg = [];
                let notification = "";
                let error = false;
                let randomName = randomUniqueID();
                if(fullfill == 'unknown_file') {
                    error = true;
                    msg.push("Format file harus berupa .jpg, dan .png");
                }

                if(files[x].size > 2097152) {
                    error = true 
                    msg.push("Ukuran file maksimal 2MB");
                }

                if(!error) {
                    file_store[`${randomName}`] = await imgToBase64(files[x]);
                    sessionStorage.setItem('files', JSON.stringify(file_store));
                }

                if(msg.length > 0 && error) {
                    let tmp = "";
                    for(let p = 0; p < msg.length; p++) {
                        tmp += `
                            <div><span>${msg[p]}</span></div>
                        `;
                    }
                    notification = `
                    <div class="card-body rounded-0 alert alert-danger pb-0 mb-0">
                        <span>Error Terjadi</span>
                        ${tmp}
                    </div>
                    `;
                }
    
                let element = `
                    <div class="col-lg-12 mb-2 px-0 list-container">
                        <div class="card rounded-0 ${randomName}">
                            <div class="list-header font-md py-3 px-3">
                                <div class="position-relative w-100">
                                    <span class="link-title">${files[x].name}</span>
                                    <span class="position-absolute close-list remove-element cursor-pointer hover-red" data-target="list-container" data-parent-container-target="img-uploaded-list-container" data-file-name="${randomName}" style="right: 2px; top: 50%; transform: translateY(-50%);"><i class="fa fa-fw fa-times"></i></span>
                                </div>
                            </div>
                            ${notification}
                        </div>
                    </div>
                `;
                let parent = $('.file-upload-style').parent();
                if($(parent).find('.img-uploaded-list-container').length == 0) {
                    $(parent).append(`<div class="mt-2 mb-2 w-100 img-uploaded-list-container"></div>`);
                }
                $('.img-uploaded-list-container').append(element);
            });
        })(x);
    }
}

// Konversi input angka ke format uang rupiah

function moneyFormatRupiahInput(value) {
    var target = value.target;
    var targetPos = target.selectionStart;
    
    var targetVal = $(target).val();
    targetVal = targetVal.replace(/[a-zA-Z\!@#$%^&\*\(\)\_\+\-\=\{\}\[\]\|\\\:\;\"\'\<\>\?\/]/g, "").trim();
    var rupiah;
    var rupiahLength = 0;
    if(targetVal != "") {
        rupiah = rupiahFormat(targetVal)
        rupiahLength = rupiah.length;
        $(target).val(rupiah);
    }
    if(targetPos != rupiahLength) {
        if(rupiahLength > targetVal.length) {
            targetPos += 1;
        }
        else if(rupiahLength < targetVal.length) {
            if(targetPos - 1 > 0) {
                targetPos -= 1;
            }
        }
    }
    target.selectionEnd = targetPos;
}

// Hapus subkategori
export const removeSubCategory = function(elem, config) {
    const ConfirmationDialog = config.confirmationDialog;
    const ajaxConfig = config.ajaxConfig;
    const ajaxModule = config.ajaxModule;
    const target = $(elem).attr('data-target');
    const targetContainer = $(elem).attr('data-target2');
    const idSubKategori = $(elem).attr('data-id-subkategori');
    ConfirmationDialog.prependModal();
    ConfirmationDialog.setModalMessage("Apakah anda ingin menghapus data?");
    ConfirmationDialog.setDangerMessage("Peringatan menghapus data sub kategori juga akan menghapus data produk!");
    ConfirmationDialog.setModalFooterButton("Iya", 'primary', true);
    ConfirmationDialog.setModalFooterButton("Tidak", 'danger', true);
    ConfirmationDialog.showModal();
    // Hapus modal dari DOM setelah diklik
    $(document).on('click', '.btn-confirmation', function(p) {
        if($(this).attr('data-answer') === 'yes') {
            // Hapus element subkateogri dari DOM!
            $(elem).parents(`.${target}`).fadeOut('slow', function() {
                $(this).remove();
            });
            // Hapus element kategori dari DOM jika subkategori sudah tidak ada lagi!
            if($(elem).parents('.list-content').children(`.${target}`).length - 1 <= 0) {
                $(elem).parents(`.${targetContainer}`).fadeOut('slow', function() {
                    $(this).remove();
                });
            }
            
            let pageNumber = getGETURLParameter('page=') != "" ? getGETURLParameter('page=') : 1;
            pageNumber = !isNaN(pageNumber) ? parseInt(pageNumber) : 1;
            let data = new FormData();
            data.append('idSubKategori', idSubKategori);
            data.append('page', pageNumber);
            data.append('kategoriElementTotal', $('.list-container').length);
            let url = base_url("kategori_produk/hapus_subkategori_produk?page=" + pageNumber); // URL target
                
            ajaxConfig.setConfig({
                'url' : url,
                'data': data,
                'type': "POST",
                'dataType': "JSON",
                'processData': false,
                'contentType': false,
                'cache': false,
            });
            ajaxConfig.setBeforeSend(ajaxDeleteBefore); // Set BeforeSend
            ajaxConfig.setComplete(ajaxDeleteComplete); // Set Complete
            ajaxConfig.setSuccess(ajaxDeleteSuccess, true); // Atur fungsi Sukses
            ajaxConfig.setError(ajaxDeleteError); // Atur fungsi error
            ajaxModule.AjaxQuery.sendAjax(ajaxConfig.getConfig()); // Kirim ajax
        }
        $(document).off('click', '.btn-confirmation'); // Hapus event click pada tombol konfirmasi modal
    });
}

// Hapus kategori
export const removeCategory = function(elem, config) {
    const ConfirmationDialog = config.confirmationDialog;
    const ajaxConfig = config.ajaxConfig;
    const ajaxModule = config.ajaxModule;
    const target = $(elem).attr('data-target');
    const btn = $(elem);
    const idKategori = $(btn).parents('.list-container').attr("data-idkategori");
    ConfirmationDialog.prependModal(); // Tambahkan modal pada child element pertama pada body
    ConfirmationDialog.setModalMessage("Apakah anda ingin menghapus data?");
    ConfirmationDialog.setDangerMessage("Peringatan menghapus data kategori juga akan menghapus data subkategori, dan produk!");
    ConfirmationDialog.setModalFooterButton("Iya", 'primary', true);
    ConfirmationDialog.setModalFooterButton("Tidak", 'danger', true);
    ConfirmationDialog.showModal();

    // Hapus modal dari DOM saat modal ditutup
    $(document).on('click', '.btn-confirmation', function(p) {
        if($(this).attr('data-answer') === 'yes') {
            $(btn).parents(`.${target}`).fadeOut('slow', function() {
                $(this).remove();
                let pageNumber = getGETURLParameter('page=') != "" ? getGETURLParameter('page=') : 1;
                pageNumber = !isNaN(pageNumber) ? parseInt(pageNumber) : 1;
                let data = new FormData();
                data.append('idKategori', idKategori);
                data.append('page', pageNumber);
                data.append('kategoriElementTotal', $('.list-container').length);
                let url = base_url("kategori_produk/hapus_kategori_produk?page=" + pageNumber); // URL target
                
                ajaxConfig.setConfig({
                    'url' : url,
                    'data': data,
                    'type': "POST",
                    'dataType': "JSON",
                    'processData': false,
                    'contentType': false,
                    'cache': false,
                });
                ajaxConfig.setBeforeSend(ajaxDeleteBefore); // Set BeforeSend
                ajaxConfig.setComplete(ajaxDeleteComplete); // Set Complete
                ajaxConfig.setSuccess(ajaxDeleteSuccess, true); // Atur fungsi Sukses
                ajaxConfig.setError(ajaxDeleteError); // Atur fungsi error
                ajaxModule.AjaxQuery.sendAjax(ajaxConfig.getConfig()); // Kirim ajax
            });
        }
        $(document).off('click', '.btn-confirmation'); // Hapus event click pada tombol konfirmasi modal
    });
}

// Toggle sidebar dengan animasi command
export const ToggleSideBarAnimCommand = function(elem) {
    return new Command(toggleSidebarAnim, elem);
}

// Toggle sidebar dropdown command
export const ToggleSidebarDropdownCommand = function(elem) {
    return new Command(toggleSidebarDropdown, elem);
}

// Submit Get Form dan bersihkan value kosong saat submit

export const SubmitGetFormCommand = function(elem) {
    return new Command(submitGetForm, elem);
}

function submitGetForm(elem) {
    $(elem).find('input, select').each(function() {
        if($(this).val() == "") {
            $(this).attr('disabled', true);
        }
    });
}

// Tambah input sub kategori


function addSubCategoryInput(elem) {
    const target = `.${$(elem).attr('data-target')}`;
    const targetLink = `.${$(elem).attr('data-target-link')}`;
    const index = isNaN(parseInt($(target).last().attr('data-subkategori-input-index'))) == false ?  parseInt($(target).last().attr('data-subkategori-input-index')) + 1 : 1;
    if($(target).length <= 9) {
        const subKategoriBox = `
        <div class="mt-2 mb-2 subkategori-box">
            <div class="card rounded-0">
                <div class="card-body">
                    <button type='button' class="btn-hapus-subkategori-input rounded-0 btn btn-danger"><i class="fa fa-fw fa-times"></i> Hapus Input Sub Kategori</button>
                    <div class="mt-4 appended-input">
                        <h5 class="appended-input-title">Sub Kategori Input ${index}</h5>
                        <div class="mb-2">
                            <span>Nama Sub Kategori</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        // const targetLastIndex = $(target).attr('data-subkategori-input-index');
        $(subKategoriBox).hide().appendTo('#inputSection'); // Sembunyikan kotaknya
        

        $('.appended-input:last').append('<div class="input-box mb-2"></div><div class="mb-2"><span>Link Sub Kategori</span></div><div class="input-box"></div>');
        $('.appended-input:last').append('');
        $(target).last().clone().appendTo('.appended-input:last .input-box:first'); // Tambah input dengan copy dari input subkategori terakhir
        
        $('.appended-input:last .input-box').first().append(`<span id="subKategoriInput${index}Error" class="input-error text-danger"></span>`);
        $(target).last().val('');
        $(targetLink).last().clone().appendTo('.input-box:last'); // Tambah input link dengan copy dari input subkategori link terakhir
        $(targetLink).last().val('');
        $('.input-box').last().append(`<span id="subKategoriLinkInput${index}Error" class="input-link-error input-error text-danger"></span>`);
        $('.subkategori-input:last').attr('data-subkategori-input-index', index); // Tambah attribute data-subkategori-input-index
        // $('.subkategori-input').last().val('');
        $('.subkategori-input:last').attr('name', `dynamic_subKategoriInput[]`);
        $('.input-error:last').attr('id', `subKategoriInput${index}Error`);
        $('.subkategori-link-input').last().attr('name', `dynamic_subKategoriLinkInput[]`);
        $('.input-link-error:last').attr('id', `subKategoriLinkInput${index}Error`);
        $('.subkategori-box').last().fadeIn('slow'); // Tampilkan kontennya
    }
}

function removeSubCategoryInput(elem) {
    const inputElement = $(elem).next().find('.subkategori-input').last();
    let index = !isNaN(parseInt($(inputElement).attr('data-subkategori-input-index'))) ? parseInt($(inputElement).attr('data-subkategori-input-index')) : 2 ;
    // console.log($(elem).parents('.subkategori-box').nextAll());
    const nextSubKategoriBox = $(elem).parents('.subkategori-box').nextAll();
    $(elem).parents('.subkategori-box').fadeOut('fast', function() {
        $(this).remove();
        if(nextSubKategoriBox.length > 0) {
            $(nextSubKategoriBox).each(function(elementIndex, element) {
                const currentIndex = $(element).find('.subkategori-input').attr('data-subkategori-input-index') - 1;
                $(element).find('.subkategori-input').attr('data-subkategori-input-index', currentIndex);
                $(element).find('.appended-input-title').text(`Sub Kategori Input ${currentIndex}`);
                $(element).find('.input-error').attr('id', "subKategoriInput" + currentIndex + "Error");
            });
        }
    });
}


// Toggle sidebar dropdown item

function toggleSidebarDropdown(elem) {
    var collapse = false;
    if(elem instanceof Element == false && typeof elem !== 'string') {
        collapse = elem.collapse;
        elem = elem.target;
    }
    const parentElem = $(elem).parent(); // Ambil parent elemennya
    const target = $(parentElem).find(`.${$(elem).attr('data-target')}`).attr('data-collapse');
    if(target != 'show' || collapse) {
        $(parentElem).find(`.${$(elem).attr('data-target')}`).fadeOut();
        $(parentElem).find('.fa-caret-right').css({
            'transform': 'rotate(0deg)',
            '-moz-transform': 'rotate(0deg)',
            '-webkit-transform': 'rotate(0deg)'
        });
        $(parentElem).find(`.${$(elem).attr('data-target')}`).attr('data-collapse', 'show');
    }
    else {
        $(parentElem).find(`.${$(elem).attr('data-target')}`).fadeIn();
        $(parentElem).find(`.${$(elem).attr('data-target')}`).attr('data-collapse', 'collapse');
        $(parentElem).find('.fa-caret-right').css({
            'transform': 'rotate(90deg)',
            '-moz-transform': 'rotate(90deg)',
            '-webkit-transform': 'rotate(90deg)'
        });
    }
}

// Toggle sidebar
function toggleSidebarAnim(elem) {
    var collapse = false;
    if(elem instanceof Element == false && typeof elem !== 'string') {
        collapse = elem.collapse;
        elem = elem.target;
    }
    if($(elem).length > 0) {
        var dataCollapse = $(elem).attr('data-collapse');
        if(dataCollapse.toLowerCase() == "collapse" || collapse) {
            // Sembunyikan sidebar
                    $(elem).attr('data-collapse', "show");
                $(elem).animate({
                    'left': '-599px'
                }, 800);
        }
        else {
            // Tampilkan sidebar
                $(elem).attr('data-collapse', "collapse");
            $(elem).animate({
                'left': '0',
            }, 800);
        }
    }
}


function toggleUserMenuPanel(data) {
    var target = data;
    var collapse = false;
    if(target instanceof Object) {
        target = target.target;
        collapse = data.collapse;
    }
    if($(target).length > 0) {
        if($(target).attr("data-show").toLowerCase() == "hide" || collapse) {
            $(target).animate({
                opacity: 0
            }, 200, function() {
                $(this).hide();
                $(this).attr("data-show", "show");
            });
        }
        else {
            $(target).show(function() {
                $(this).animate({
                    opacity: 1
                }, 200);
                $(this).attr("data-show", "hide");
            });
        }
    }
}

// Tutup bootstrap notification alert
export function closeNotification(element) {
    $(document).on('click', element, function(e) {
        e.preventDefault();
        const elem = $(this).parent();
        $(elem).fadeOut('slow', function() {
            $(this).remove();
        });
    });
}



function showPassword(elem)
{
    const icon = $(elem).children().find('i');
    const target = $(`#${$(elem).attr('data-target-password')}`);
    if($(icon).hasClass('fa-eye'))
    {
        $(icon).removeClass('fa-eye');
        $(icon).addClass('fa-eye-slash');
        $(target).attr('type', 'text');
    }
    else {
        $(icon).removeClass('fa-eye-slash');
        $(icon).addClass('fa-eye');
        $(target).attr('type', 'password');
    }
}


export const Event = new function() {
    return {
        addDOMEvent: function(event, selector) {
            event.call({}, selector);
        },
        execute: function(command)
        {
            command.execute();
        }
    }
}