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
                $uploadOk = $this->checkImg($file, $fileType, $uploadOk);  
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
    function checkImg($file, $fileType, $uploadOk) {
        if ($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png" && $fileType != "gif") {
            echo "<p class='header-notif'>Cannot accept file type $fileType.</p>";
            $uploadOk = 0; 
        }

        // Resize img for better performance
        $imgSpecs = getimagesize($file["tmp_name"]); 

        function resize($imgTemp, $imgSpecs) {
            $ratio = round($imgSpecs[0] / $imgSpecs[1], 2); 
            $imgResized = null; 

            // If height > width, set height = 1200 instead of width
            if ($imgSpecs[1] > $imgSpecs[0]) {
                $imgResized = imagescale($imgTemp, 1200 * $ratio, 1200); 
            } else {
                $imgResized = imagescale($imgTemp, 1200); 
            }

            return $imgResized; 
        }

        // Checking if image's width || height > 1200
        if ($imgSpecs[0] > 1200 || $imgSpecs[1] > 1200) {
            switch($fileType) {
                case "jpg": 
                case "jpeg": 
                    $imgTemp = imagecreatefromjpeg($file["tmp_name"]); 
                    $imgResized = resize($imgTemp, $imgSpecs); 
    
                    imagejpeg($imgResized, $file["tmp_name"]); 
    
                    break;
                case "png": 
                    $imgTemp = imagecreatefrompng($file["tmp_name"]); 
                    $imgResized = resize($imgTemp, $imgSpecs);
    
                    imagepng($imgResized, $file["tmp_name"]); 
    
                    break;
                case "gif": 
                    $imgTemp = imagecreatefromgif($file["tmp_name"]); 
                    $imgResized = resize($imgTemp, $imgSpecs);
    
                    imagegif($imgResized, $file["tmp_name"]); 
    
                    break;
                default: 
                    $uploadOk = 0; 
    
                    break;
            }
        }

        return $uploadOk; 
    }

}

$fileSystem = new FileSystem(); 

?>