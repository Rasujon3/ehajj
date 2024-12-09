<?php
set_time_limit(-1);
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Dhaka');
}

$year_basis_folder_path = "uploads/" . date("Y");
if (!file_exists($year_basis_folder_path)) {
    mkdir($year_basis_folder_path, 0777, true);
}

$ym = date("Y") . "/" . date("m") . "/";
$month_basis_folder_path = "uploads/" . date("Y") . "/" . date("m");
if (!file_exists($month_basis_folder_path)) {
    mkdir($month_basis_folder_path, 0777, true);
}

$path = "uploads/";

$selected_file = $_POST["selected_file"];
$isRequired = $_POST["isRequired"];
$req = "";
if ($isRequired == "1")
    $req = "required";

$validateFieldName = $_POST["validateFieldName"];
$max_size = $_POST["max_size"];
//$valid_formats = array("jpg", "jpeg", "png", "pdf"); //"jpg", "png", "gif", "bmp", "txt", "doc", "docx",
$valid_formats = array("pdf"); //"jpg", "png", "gif", "bmp", "txt", "doc", "docx",
$validFileFlag = "";
if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_FILES[$selected_file]['name'];
    $size = $_FILES[$selected_file]['size'];

    if (strlen($name)) {
        //list($txt, $ext) = explode(".", $name);
        $i = strripos($name, '.');
        if ($i > 0 && $i + 2 < strlen($name)) {
            $ext = strtolower(substr($name, $i + 1));
        } else {
            $ext = 'xxxxx';
        }
        if (in_array($ext, $valid_formats)) {
            if ($size < 1024 * $max_size) { // maximum file size 3 MB
                $actual_image_name = uniqid(config('app.APP_NAME') .'_', true) . "." . $ext;
                $tmp = $_FILES[$selected_file]['tmp_name'];
                if (move_uploaded_file($tmp, $path . $ym . $actual_image_name)) {
//           For Image if you want to display
//          echo "<img src='ajaximage/uploads/".$ym.$actual_image_name."' class='preview' style='width:150px; height=150px;' >";
                    $validFileFlag = $ym . $actual_image_name;
                    $flag = 'span_' . $validateFieldName;
                    echo "<font class=$flag color='#000'>-Uploaded file size is " . (($size > 1024 * 1024) ? round($size/ (1024 * 1024), 2) ." MB" : number_format($size/1024) . " KB") . "</font>";
                } else
                    echo "-failed";
            } else
                echo "-File size max " .(($max_size > 1023 ) ? round($max_size/1024, 2) ." MB" : $max_size. " KB");
        } else
            echo "-Invalid file format..";
    } else
        echo "-Please select file..!";
}
?>
<input type="hidden" <?php echo $req == "" ? "" : 'class="required"'; ?> value="<?php echo $validFileFlag; ?>"
       id="<?php echo $validateFieldName; ?>" name="<?php echo $validateFieldName; ?>"/>