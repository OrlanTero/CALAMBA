<?php

include_once "./../../includes/Response.php";
function UploadFileFromFile($FILE, $TOPATH, $FILENAME = false)
{

    $extension = pathinfo($FILE["name"], PATHINFO_EXTENSION);
    $fileTmpPath = $FILE['tmp_name'];
    $fileName = $FILENAME && $FILENAME != 'false' ? $FILENAME . '.' . $extension : $FILE['name'];
    $dest_path = $TOPATH . $fileName;
    $i = 1;

    if (!file_exists($TOPATH)) {
        mkdir($TOPATH, 0777, true);
    }

    while (file_exists($dest_path)) {
        $fn = pathinfo($fileName, PATHINFO_FILENAME);
        $nfn = $fn . '(' . $i . ').' . $extension;
        $dest_path = $TOPATH . $nfn;
        $i++;
    }

    $upload = move_uploaded_file($fileTmpPath, $dest_path);
    $message = 'File is successfully uploaded.';
    $message1 = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';

    return new Response($upload ? 200 : 204, $upload ? $message : $message1, ["path" => $fileName, "basename" => pathinfo($dest_path, PATHINFO_BASENAME)]);
}

echo json_encode(UploadFileFromFile($_FILES['file'], $_POST['destination'], $_POST['filename']), JSON_THROW_ON_ERROR);
