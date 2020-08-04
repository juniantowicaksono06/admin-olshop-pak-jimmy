
// Bagian fungsi

// Ambil url situs
function base_url(uri) {
    const protocol = window.location.protocol;
    const host = window.location.hostname;
    return `${protocol}//${host}/admin-store/${uri}`;
}

// Fungsi untuk cek json
function isJSON(jsonVar) {
    try { 
        JSON.parse(jsonVar); // Coba parsing json jadi objek
        return true; // return true jika JSON berhasil jadi objek
    } catch (error) {
        return false; // return false jika JSON gagal jadi objek
    }
}

function objectLength(obj) {
    if(typeof obj === "object") {
        var count = 0;
        for(let i in obj) {
            if(obj.hasOwnProperty(i)) {
                count += 1;
            }
        }
        return count;
    }
}
// Ambil parameter GET dari URL
function getGETURLParameter(param) {
  let a = new URLSearchParams(window.location.search);
  let tmp_val = a.get(param);
  if(tmp_val == null || tmp_val == undefined) {
    tmp_val = "";
  }
  return tmp_val;
}







// Misc function

// Inisialisasi CKEditor

function initCKEditor(elementID) { // Replace textarea dengan class text-editor dengan CKEDitor
    $(elementID).each(function() {
      if ($(this).hasClass('text-editor')) {
        CKEDITOR.replace($(this).attr('id'));
      }
    });
}
  


function CreateAlert(message, type) {
    var msg = message !== undefined ? `<ul><li>${message}</li></ul>` : "";
    var type = type.toLowerCase().trim();
    type = type == "success" ? type : type == "info" ? type : "danger";
    return {
        appendMessage: function(message) {
            if(message !== undefined) {
                msg = msg.trim() == "" ? `<ul class="notification-ul"><li>${message}</li></ul>` : msg.replace("</ul>", `<li>${message}</li></ul>`);
            }
        },
        setMessage: function(message) {
            if(message !== undefined) {
                msg = `<ul class="notification-ul"><li>${message}</li></ul>`;
            }
        },
        setAlert: function() {
            var header = type == "success" ? "Notifikasi sukses: " : "Notifikasi gagal: ";
            msg = `
                <div class="alert alert-${type} mb-4 rounded-0">
                    <span>${header}</span>
                    ${msg}
                    <div class="bg-danger notification-msg-dismiss">
                        <span>X</span>
                    </div>
                </div>
            `;
        },
        getAlert: function() {
            return msg;
        }
    }
}

// function untuk buat alert sukses pada bootstrap
function CreateSuccessAlert(message) {
    const alert = CreateAlert(message, "success");
    return alert;
}

// function untuk buat alert danger pada bootstrap
function CreateDangerAlert(message) {
    const alert = CreateAlert(message, "danger");
    return alert;
}

function CreateInfoAlert(message) {
    return CreateAlert(message, "info");
}

function updateCKEDitorTextarea() {
    for (instance in CKEDITOR.instances) {
      CKEDITOR.instances[instance].updateElement();
    }
}

function clearCKEditor() {
    for (instance in CKEDITOR.instances){
      CKEDITOR.instances[instance].setData('');
      CKEDITOR.instances[instance].updateElement();
    }
}

// Format uang

function rupiahFormat(value) {
    var tmpValue = value;
    if(tmpValue.includes(".")) {
        tmpValue = tmpValue.split(/\./g).join('');
    }
    tmpValue = tmpValue.replace(/^(\.|\,)/, "");
    tmpValue = tmpValue.replace(/^0/, 1);
    
    tmpValue = tmpValue.split('').reverse().join('');
    var money = tmpValue = tmpValue.match(/\d{1,3}/g);
    money = tmpValue.join('.').split('').reverse().join('');
    return money;
}

// Fungsi untuk cek header file 
// Berfungsi untuk mendeteksi file berupa gambar?

function checkFileHeaderImg(header) {
    let fileType = '';
    switch (header) {
      // PNG Format
      case '89504e47':
        fileType = 'image/png';
        break;
        // Jpg / JPEG format
      case 'ffd8ffe0':
      case "ffd8ffe1":
      case "ffd8ffe2":
      case "ffd8ffe3":
      case "ffd8ffe8":
        fileType = 'image/jpeg';
        break;
        // Webp Format
      case '52494646':
      case '57454250':
        fileType = 'image/webp';
        break;
      default:
        fileType = 'unknown_file';
        break;
    }
    return fileType;
  }

  // Konversi Base64 Ke Blob Type
// Konversi base64 ke blob
function imgbase64ToBlob(dataURL) {
  var BASE64_MARKER = ';base64,';
  if (dataURL.indexOf(BASE64_MARKER) == -1) {
    var parts = dataURL.split(',');
    var contentType = parts[0].split(':')[1];
    var raw = decodeURIComponent(parts[1]);
    return new Blob([raw], {
      type: contentType
    });
  }
  var parts = dataURL.split(BASE64_MARKER); // Pecah base64 menjadi array
  var contentType = parts[0].split(':')[1];
  var raw = window.atob(parts[1]);
  var rawLength = raw.length;

  var uInt8Array = new Uint8Array(rawLength);

  for (var i = 0; i < rawLength; ++i) {
    uInt8Array[i] = raw.charCodeAt(i);
  }

  return new Blob([uInt8Array], {
    type: contentType
  });
}


  //  Fungsi untuk mengubah image ke format Base64
  function imgToBase64(file) {
    let fileReader = new FileReader();
    return new Promise((resolve, reject) => {
      fileReader.onload = function(e) {
        resolve(e.target.result);
      };
      fileReader.onerror = function(e) {
        reject(new DOMException('Problem parsing input file'));
      };
      fileReader.readAsDataURL(file);
    });
  }

  function checkImgMimeType(file) {
    let fileReader = new FileReader();
    let mimeType = '';
  
    return new Promise((resolve, reject) => {
      fileReader.onload = (e) => {
        let arr = (new Uint8Array(e.target.result)).subarray(0, 4);
        let header = "";
        for (let i = 0; i < arr.length; i++) {
          header += arr[i].toString(16);
        }
        mimeType = checkFileHeaderImg(header);
        resolve(mimeType);
      };
      fileReader.onerror = () => {
        fileReader.abort();
        reject(new DOMException("Problem parsing input file."));
      };
      fileReader.readAsArrayBuffer(file);
    });
  }

  // Generate ID Unik

  function randomUniqueID (prefix = ""){
    return `${prefix}${Math.random().toString(36).substr(2, 9)}`;
  }