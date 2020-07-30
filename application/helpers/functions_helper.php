<?php 

function randomString($prefix = "", $length = 5, $type = "all") {
    $char_list = "";
    $type = strtolower($type);
    if($type == "all") {
        $char_list = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_+/";
    }
    else if($type == "char_low_and_num") {
        $char_list = "abcdefghijklmnopqrstuvwxyz0123456789";
    }  
    else if($type == "char_upp_and_num") {
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    else if($type == "number") {
        $char_list = "0123456789";
    }
    else {
        if($type == "letter-upper") {
            $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        else {
            $char_list = "abcdefghijklmnopqrstuvwxyz";
        }
    }
    $str = "";
    for($x = 0; $x <= $length; $x++) {
        $random_number = rand(0, strlen($char_list) - 1);
        $str .= $char_list[$random_number];
    }
    return $str;
}

function strToLink($str) {
    $str = preg_split('/(\s|\&)/', $str);
    return strtolower(join('_', $str));
}


// Validasi gambar yang diupload

// Ambil tipe mime suatu file
function getMimeType($file_data)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Ambil mime ekstensinya
    $file_mime_type = finfo_file($finfo, $file_data); // Buka filenya
    finfo_close($finfo); // Tutup filenya
    return $file_mime_type; // Kembalikan filenya
}

function getClosestNumberInArray($number, $arr) {
   $closest = null;
   foreach ($arr as $item) {
      if ($closest === null || abs($number - $closest) > abs($item - $number)) {
         $closest = $item;
      }
   }
   return $closest;
}

function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
      // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
      return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    else {
      return round($size);
    }
  }


  function convertByteToMegaByte($size) {
      return round($size / pow(1024, 2));
  }

// Cukup Kirim $_FILES saja fungsi dibawah ini sudah bisa jalan
function validateMultipleImage($files, $max_size = 2048)
{
    $valid_extension = ["image/png", "image/jpeg", "image/jpg"];
    $error_list = [];
    $CI =& get_instance();
    $file_size_maximum = parse_size($CI->config->item('max_upload_file_size'));
    if (count($files) > 0) {
        $error_status = false;
        for ($x = 0; $x < count($files['files']['error']); $x++) {
            if($files['files']['error'][$x] == 0) {
                $mime_type = getMimeType($files['files']['tmp_name'][$x]); // Ambil ekstensi filenya
                $file_size = filesize($files['files']['tmp_name'][$x]); // Ambil ukuran filenya
                $KB_size = round($file_size / 2048); // Bulatkan ukurannya ke KiloByte
                if (!in_array($mime_type, $valid_extension)) {
                    $error_status = true;
                    $error_list["{$files['files']['name'][$x]}"]['invalid_format_error'] = "Format file harus jpg, atau png!";
                }
        }
        else if($files['files']['error'][$x] == 1) {
            $error_status = true;
            $error_list["{$files['files']['name'][$x]}"]['max_size_error'] = "Ukuran file terlalu besar silahkan unggah gambar yang lebih kecil dari " . convertByteToMegaByte($file_size_maximum) . "MB!";
        }
        else if($files['files']['error'][$x] == 3) {
            $error_status = true;
            $error_list["{$files['files']['name'][$x]}"]['file_upload_corrupt'] = "File yang di unggah korup silahkan coba lagi!";
        }
        else if($files['files']['error'][$x] == 6) {
            $error_status = true;
            $error_list["{$files['files']['name'][$x]}"]['file_upload_no_tmp_dir'] = "Tidak ditemukan direktori \"sementara\" silahkan hubungi administrator website!";
        }
        else if($files['files']['error'][$x] == 7) {
            $error_status = true;
            $error_list["{$files['files']['name'][$x]}"]['file_upload_no_tmp_dir'] = "Tidak dapat mengunggah file ke server silahkan hubungi administrator website!";
        }
        else {
            $error_status = true;
            $error_list["{$files['files']['name'][$x]}"]['file_upload_no_tmp_dir'] = "Tidak dapat mengunggah file ke server silahkan hubungi administrator website!";  
        }
        }
        if ($error_status) {
            return $error_list;
        } else {
            return true;
        }
    }
}