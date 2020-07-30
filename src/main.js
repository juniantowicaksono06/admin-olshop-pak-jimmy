import * as appModule from './wrapper-module.js';



$(document).ready(function() {
    sessionStorage.clear();
    const ajaxModule = appModule.app_ajaxModule;

    const appCommand = appModule.app_commandModule;

    const Event = appCommand.Event;

    const ConfirmationDialog = appModule.app_confirmationDialog;

    initCKEditor('ckeditor');

    Event.addDOMEvent(appCommand.closeNotification, '.notification-msg-dismiss'); // Tambahkan event notification close ketika tombol di click
    // Submit Get Form bersihkan nilai kosong saat submit form dengan get method

    $('.submit-get-form').on('submit', function(e) {
        Event.execute(new appCommand.SubmitGetFormCommand(this));
    });

    $('.text-rupiah').each(function() {
        $(this).text(rupiahFormat($(this).text()));
    });




    // Form Submit Event
    $('#loginForm, #formInputSubmit').on('submit', function(e){
        e.preventDefault();
        let me = this;
        let data = new FormData();
        let url = $(me).attr('action');
        updateCKEDitorTextarea(); // Update value dari textarea dengan inputan dari CKEditor
        $('input, select, textarea').each(function(index, elem) {
            let name = $(elem).attr('name');
            let value = $(elem).val();
            if($(elem).attr('type') == 'checkbox') {
                if(!$(elem).prop('checked')) {
                    value = "";
                }
            }
            data.append(name, value);
        });

        // Tambahkan gambar ke FormData

        if(sessionStorage.getItem('files') != null || sessionStorage.getItem('files') != undefined) {
            let files = JSON.parse(sessionStorage.getItem('files'));
            for(let file in files) {
                data.append("files[]", imgbase64ToBlob(files[file]), file);
            }
        }

        let ajaxConfig = new ajaxModule.SetAjaxConfig();
        ajaxConfig.setConfig({
            'url' : url,
            'data': data,
            'type': "POST",
            'dataType': "JSON",
            'processData': false,
            'contentType': false,
            'cache': false,
            'runEventSuccessOnce': $(me).attr('id') != 'loginForm' ? false : true,
            'formRefresh': $(me).attr('data-refresh'),
            'elementFadeOutDelete': '.subkategori-box, .img-uploaded-list-container',
            'fileUploadDeleteSessionStorage': true,
            'targetEvent': {
                'target': me,
                'event': 'submit'
            }
        });
        ajaxConfig.setBeforeSend(ajaxInputBefore); // Set BeforeSend
        ajaxConfig.setComplete(ajaxInputComplete); // Set Complete
        ajaxConfig.setSuccess(ajaxInputSuccess, true); // Atur fungsi Sukses
        ajaxConfig.setError(ajaxInputError); // Atur fungsi error
        ajaxModule.AjaxQuery.sendAjax(ajaxConfig.getConfig()); // Kirim ajax
    });



    // Tampilkan / Sembunyikan password saat tombol dengan kelas show password ditekan
    $('.show-password').on('click', function(e) {
        e.preventDefault();
        Event.execute(new appCommand.ShowPasswordCommand(this));
    });


    $('.money-rupiah').on('keypress', function(e) {
        var key = e.which || e.keycode;
        if (key != 188 && key != 8 && key != 17 && key != 86 && key != 67 && (key < 48 || key > 57)
		) {
        	e.preventDefault();
            return;
        }
    });
    // Batasi input dengan kelas number-colon-only untuk angka dan titik
    $('.number-colon-only').on('keypress', function(e) {
        var key = e.which || e.keycode;
        if(key != 188 && key != 8 && key != 46 && key != 86 && key != 67 && (key < 48 || key > 57)) {
            e.preventDefault();
            return;
        }
    });

    $('.money-rupiah').on('input', function(e) {
        Event.execute(new appCommand.MoneyFormatRupiahInputCommand(e));
    });

    $('#produkImgInput').on('change', function(e) {
        Event.execute(new appCommand.AddImgUploadListQueueCommand(e));
    });

    // Tambah input subkategori

    $('#btnTambahInputSubKategori').on('click', function(e) {
        e.preventDefault();
        Event.execute(new appCommand.AddSubCategoryInputCommand(this));
    });



    // Hapus kategori

    $(document).on('click', '.btn-hapus-kategori', function(e) {
        e.preventDefault();

        let config = {
            'confirmationDialog': ConfirmationDialog,
            'ajaxConfig': new ajaxModule.SetAjaxConfig(),
            'ajaxModule': ajaxModule
        }
        Event.execute(new appCommand.RemoveCategoryCommand($(this), config));
    });

    $(document).on('click', '.btn-hapus-subkategori', function(e) {
        e.preventDefault();

        let config = {
            'confirmationDialog': ConfirmationDialog,
            'ajaxConfig': new ajaxModule.SetAjaxConfig(),
            'ajaxModule': ajaxModule
        };
        Event.execute(new appCommand.RemoveSubCategoryCommand($(this), config));
    });

    // Hapus modal saat modal ditutup
    $(document).on('hidden.bs.modal', function() {
        ConfirmationDialog.deleteModal();
    });

    $(document).on('click', '.btn-hapus-subkategori-input', function(e) {
        e.preventDefault();
        const elem = $(this).last(); // Gak tahu kenapa dengan this malah gak jalan :(
        Event.execute(new appCommand.RemoveSubCategoryInputCommand(elem));
    });

    // Tutup sidebar dan menu userpanel
    $('body').on('click', function(e) {
        appCommand.OffCollapseElementCommand({
            'eventTarget': e.target,
            'targetList': [
                {
                    'target': `.${$('#toggleUserMenuPanel').attr('data-target')}`,
                    'command': appCommand.ToggleUserMenuPanelCommand,
                    'collapse': true
                },
                {
                    'target': '.sidebar-dropdown',
                    'command': appCommand.ToggleSidebarDropdownCommand,
                    'collapse': true
                },
                {
                    'target': '#sidebarContainer',
                    'command': appCommand.ToggleSideBarAnimCommand,
                    'collapse': true
                }
            ],
            'excludeTarget': [
                '.dropdown-panel',
                '.sidebar-dropdown-content', 
                '.sidebar-link', 
                '.sidebar-dropdown-link', 
                '#sidebarContainer'
            ]
        })
    });


    $('#toggleUserMenuPanel').on('click', function(e) {
        e.stopPropagation();
        Event.execute(new appCommand.ToggleUserMenuPanelCommand(`.${$(this).attr('data-target')}`))
    });

    $("#btnToggleSidebar").on('click', function(e) {
        e.stopPropagation();
        Event.execute(new appCommand.ToggleSideBarAnimCommand(`#${$(this).attr('data-target')}`));
    });

    $('.sidebar-dropdown').on('click', function(e) {
        e.stopPropagation();
        Event.execute(new appCommand.ToggleSidebarDropdownCommand(this));
    });

    // Hapus content dari dom saat element dengan class remove-element di klik
    $(document).on('click', '.remove-element', function(e) {
        e.preventDefault();
        Event.execute(new appCommand.RemoveContentFromDOMCommand(this));
        Event.execute(new appCommand.RemoveFileFromSessionStorageCommand(this));
    });

});