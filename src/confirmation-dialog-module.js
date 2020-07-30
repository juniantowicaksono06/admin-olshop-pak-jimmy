export const ConfirmationDialog = (function() {
    const modal = `
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body modal-body-confirmation">
          </div>
          <div class="modal-footer modal-footer-confirmation">
          </div>
        </div>
      </div>
    </div>
    `;
    const hideModal = function(d) {
        $('#confirmationModal').modal('hide');
    }

    const showModal = function() {
        $('#confirmationModal').modal('show');
    }

    return {
        'setModalMessage': function(msg = "") {
            let modalMessage = `
                <div class="py-2 px-2">
                    <span>${msg}</span>
                </div>
            `;

            $('.modal-body-confirmation').html(modalMessage); // Ganti semua message pada modal body
        },
        'setDangerMessage': function(msg) {
            let modalMessage = `
                <div class="py-2 px-2">
                    <span class="text-danger">${msg}</span>
                </div>
            `
            $('.modal-body-confirmation').append(modalMessage);
        },
        // Method atur tombol pada modal footer
        'setModalFooterButton': function(btnMsg, type = 'primary', append = false) {
            let answer = type == 'danger' ? "no" : "yes";
            let icon = type == 'danger' ? "ban" : "check";
            let button = `<button class="btn btn-${type.toLowerCase()} btn-confirmation" data-answer="${answer}" data-dismiss="modal" aria-label="Close"><i class="fa fa-fw fa-${icon}"></i> ${btnMsg}</button>`;
            // Cek apakah mau diappend atau replace?
            if(append) {
                $('.modal-footer-confirmation').append(button);
            }
            else {
                $('.modal-footer-confirmation').html(button);
            }
        },
        // Method tampilkan modal
        'showModal': function() {
            showModal();
        },
        // Method sembunyikan modal
        'hideModal': function(d) {
            hideModal(d);
        },
        'appendModal': function() {
            $('body').append(modal); // Tambahkan modal saat instansiasi objek
        },
        'prependModal': function(){
            $('body').prepend(modal);
        },
        // Method hapus modal dari DOM
        'deleteModal': function() {
            $('#confirmationModal').remove();
        }
    }
})();