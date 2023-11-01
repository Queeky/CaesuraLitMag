<?php

class FileSystem {

    // Uploading a file
    function upload($file, $type) {
        if ($type != "docs" && $type != "images") {
            return null; 
        }

        $newFile = "$type/". basename($file["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($newFile, PATHINFO_EXTENSION));

        // Checking if file already exists
        if (file_exists($newFile)) {
            echo "<p class='header-notif'>A file with this name and type already exists.</p>";
            $uploadOk = 0;
        }

        // Checking the file type
        switch($type) {
            case ("docs"): 
                $uploadOk = $this->checkDoc($fileType, $uploadOk); 
                break; 
            case ("images"):
                $uploadOk = $this->checkImg($fileType, $uploadOk);  
                break; 
        }

        if ($uploadOk == 0) {
            echo "<p class='header-notif'>Your file was not able to upload.</p>";
        } else {
            if (move_uploaded_file($file["tmp_name"], $newFile)) {
                echo "<p class='header-notif'>Your file successfully uploaded.</p>";

                return true; 
            } else {
                echo "<p class='header-notif'>There was an error with the upload.</p>";
            }
        }

        return null; 
    }

    // Checking if doc fits accepted file types
    function checkDoc($fileType, $uploadOk) {
        if ($fileType != "doc" && $fileType != "docx" && $fileType != "txt") {
            echo "<p class='header-notif'>Cannot accept file type $fileType.</p>";
            $uploadOk = 0; 
        }

        return $uploadOk; 
    }

    // Checking if img fits accepted file types
    function checkImg($fileType, $uploadOk) {
        if ($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png" && $fileType != "gif") {
            echo "<p class='header-notif'>Cannot accept file type $fileType.</p>";
            $uploadOk = 0; 
        }

        return $uploadOk; 
    }

}

$fileSystem = new FileSystem(); 

?>