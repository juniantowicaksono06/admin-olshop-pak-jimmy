

    // Fungsi untuk membuat alert bootstrap
    function notify(type = "success", notify) {
        type = type.toLowerCase().trim();
        const alert = type == "success" ? CreateSuccessAlert() : type == "info" ? CreateInfoAlert() : CreateDangerAlert();
        var notifyMsg = notify;
            if(isJSON(notify)) {
                notifyMsg = JSON.parse(notify);
                if(Array.isArray(notifyMsg)) {
                    for(let i of notifyMsg) {
                        alert.appendMessage(notifyMsg[i]); // Tambahkan pesan
                    }
                }
            }
            else {
                alert.setMessage(notifyMsg); // Atur pesan
            }
            alert.setAlert();
            $(alert.getAlert()).appendTo("#notificationMsg").hide().fadeIn('900'); // Tambahkan Elemen sukses ke Elemen notificationMsg
    }

    // Loading notifikasi
    const Loading = (function() {
        var loadingElement = `
        <div class="panel panel-white px-3" id="panelLoading" style="z-index: 9999">
            <span><i class="fa fa-fw fa-spinner fa-spin text-primary" style="font-size: 32px;"></i></span>
            <h5>Loading...</h5>
        </div>
        `;
        return {
            // Tampilkan loading
            "showLoading" : async function() {
                if($("#panelLoading").length == 0) {
                    $("body").prepend(loadingElement); // Tambahkan loading ke body
                }
                $("#panelLoading").animate({
                    top: 20
                }, 300);
            },
            // Sembunyikan loading
            "hideLoading": function() {
                $("#panelLoading").animate({
                    top: -150
                }, 300);
            },
            // Hapus loading
            "deleteLoading" : function() {
                $("#panelLoading").animate({
                    top: -150
                }, 900, function() {
                    $('#panelLoading').remove();
                });
            }
        }
    })();

// Ajax hasil input sukses
async function ajaxInputSuccess(data) {
    console.log(data);

    if(typeof data === 'object') {
        if(!data.notifyStatusError) {
            // Notifikasi sukses
            if(data.hasOwnProperty('notifySuccess')) {
                notify("success", data.notifySuccess);
                let redirect = "";
                let timeout = 0;

                // Cek property running once
                if(this.hasOwnProperty("runEventSuccessOnce") && this.hasOwnProperty("targetEvent")) {
                    if(this.runEventSuccessOnce) {
                        $(this.targetEvent.target).off(this.targetEvent.event);
                    }
                }
                // Refresh form setelah input
                if(this.hasOwnProperty('formRefresh')) {
                    if(this.formRefresh == 'refresh' && $(this.targetEvent.target).is('form')) {
                        $(this.targetEvent.target)[0].reset();
                    }
                }

                // Cek apakah ada property elementFadeOutDelete?
                // Kalo ada hapus element tersebut

                if(this.hasOwnProperty('elementFadeOutDelete')) {
                    // Sembunyikan Elemennya
                    $(this.elementFadeOutDelete).fadeOut('slow', function() {
                        $(this).remove(); // Hapus elemennya dari DOM
                    });
                }
                // Hapus daftar file dari sessionStorage
                if(this.hasOwnProperty('fileUploadDeleteSessionStorage')) {
                    if(this.fileUploadDeleteSessionStorage) {
                        if(sessionStorage.getItem('files') != null || sessionStorage.getItem('files') != undefined) {
                            sessionStorage.removeItem('files');
                        }
                    }
                }
                clearCKEditor(); // Bersihkan inputan textarea CKEditor
                // Redirect ke halaman ?
                if(this.redirect.trim() != "") { // Cek apakah redirect ada isi?
                    redirect = this.redirect; // Atur redirect URL
                    timeout = 5000; // Atur timeout untuk sleep sebelum di redirect
                }
                // Ambil data redirect dari server
                else if(data.hasOwnProperty("notifyRedirect") && data.hasOwnProperty("notifyRedirectTimeout")) {
                    redirect = data.notifyRedirect; // Atur redirect URL
                    timeout = data.notifyRedirectTimeout; // Atur timeout untuk sleep sebelum di redirect
                }

                if(redirect != "") {
                    await new Promise(r => setTimeout(r, timeout)); // Sleep selama timeout berlangsung
                    location.href = redirect;
                }

            }
            if(data.hasOwnProperty("notifyFailed")) {
                notify("danger", data.notifyFailed);
            }
        }
        else {
            if(data.hasOwnProperty("notifyFailed")) {
                notify("danger", data.notifyFailed);
            }
            else if(data.hasOwnProperty('notifyInfo')) {
                notify('info', data.notifyInfo);
            }
            if(data.hasOwnProperty('notifyInputError')) {
                var notifyInputError = data.notifyInputError; // Ambil pesan input error dari server

                for(let i in notifyInputError) {
                    $(`#${i}Error`).html(`${notifyInputError[i]}`); // Tampilkan kesalahan input!
                }
            }

            // Cek error file upload



                if(data.hasOwnProperty('notifyFileErrorStatus')) {
                    if(data.notifyFileErrorStatus) {
                        let listFileError = data.notifyFileListError;
                        for(let p in listFileError) {
                            $(`.${p}`).find('.card-body').remove();
                            $(`.${p}`).append(listFileError[p]);
                        }
                    }
                }
        }
    }
}

// Ajax hasil input error
function ajaxInputError(error) {
    console.log(error);
    const alert = CreateDangerAlert();
    alert.appendMessage("Error terjadi silahkan kontak administrator anda!");
    alert.setAlert();
    // $("#notificationMsg").append(alert.getAlert());
    $(alert.getAlert()).appendTo("#notificationMsg").hide().fadeIn('900');
}

async function ajaxInputBefore() {
    $('.input-error').text('');
    $('.input-error').find('span').text('');
    Loading.showLoading();
    $("#notificationMsg").find('div.alert').fadeOut(700, function() {
       $(this).remove();
    });
}

async function ajaxInputComplete() {
    Loading.deleteLoading();
}


async function ajaxDeleteSuccess(data) {
    console.log(data);
    if(typeof data === 'object') {
        if(!data.notifyStatusError) {
            if(data.hasOwnProperty('notifySuccess')) {
                notify('success', data.notifySuccess);
                if(data.hasOwnProperty('notifyContent') && data.hasOwnProperty('notifyPagination')) {
                    $('.pagination-container').html(data.notifyPagination);
                    $('.category-list-container').append(data.notifyContent);
                }

                if(data.hasOwnProperty('notifyRedirect') && data.hasOwnProperty('notifyRedirectTimeout')) {
                    if(data.notifyRedirect != "") {
                        await new Promise(r => setTimeout(r, data.notifyRedirectTimeout));
                        location.href = data.notifyRedirect;
                    }
                }
            }
        }
        else {
            if(data.hasOwnProperty('notifyFailed')) {
                notify('failed', data.notifyFailed);
            }
        }
    }
}

async function ajaxDeleteError(error) {
    console.log(error);
    notify('danger', "Gagal!");
}

async function ajaxDeleteBefore() {
    Loading.showLoading();
    $("#notificationMsg").find('div.alert').fadeOut(700, function() {
       $(this).remove();
    });
}

async function ajaxDeleteComplete() {
    Loading.deleteLoading();
}