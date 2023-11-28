<?php

class FileSystem {

    // Confirming the deletion of an object
    function confirm() {
        header("Location: confirm.php"); 
        die(); 
    }

    // Deleting a file
    function delete($file) {
        $file = htmlspecialchars_decode($file); 

        if (unlink($file)) {
            echo "<p class='header-notif'>$file successfully deleted.</p>";

            return true; 
        } else {
            echo "<p class='header-notif'>$file was unable to be deleted.</p>";

            return null; 
        }
    }

    // Uploading a file
    function upload($file, $type) {
        if ($type != "docs" && $type != "images") {
            return null; 
        }

        $uploadOk = 1;
        $fileType = strtolower(pathinfo(basename($file["name"]), PATHINFO_EXTENSION));
        $uniqName = uniqid(); 
        $newFile = "$type/$uniqName.$fileType"; 

        // Regenerating uniqid if already exists as file name
        while (file_exists($newFile)) {
            $uniqName = uniqid(); 
            $newFile = "$type/$uniqName.$fileType"; 
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
                echo "<p class='header-notif'>$file[name] successfully uploaded.</p>";

                return $newFile; 
            } else {
                echo "<p class='header-notif'>$file[name] was unable to upload.</p>";
            }
        }

        return null; 
    }

    // Checking if doc fits accepted file types
    function checkDoc($fileType, $uploadOk) {
        if ($fileType != "doc" && $fileType != "docx" && $fileType != "txt" && $fileType != "jpg" && $fileType != "jpeg" && $fileType != "png" && $fileType != "gif") {
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