<?php
if (isset($_GET['delete'])) {

    $files = glob(dirname(__FILE__) . '/*'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file))
            unlink($file); // delete file
    }
}