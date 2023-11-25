<?php

class FileSystem {

    // Confirming the deletion of an object
    function confirm($type, $name) {
        $input = null; 

        echo "<script>"; 

        echo "var msg = null;"; 

        switch ($type) {
            case "issue": 
                echo "msg = 'Deleting $name will also remove all works within this issue. Do you want to continue? (Y/N)';"; 
                break; 
            case "media": 
                echo "msg = 'Deleting $name will also remove all works of this media type. Do you want to continue? (Y/N)';"; 
                break; 
            case "work": 
                echo "var msg = '$name will be deleted. Do you want to continue? (Y/N)';"; 
                break; 
        }

        echo "var input = prompt(msg);"; 
        echo "</script>"; 

        $input = "<script>document.write(input);</script>"; 

        return $input; 
    }

    // Deleting a file
    function delete($file, $type) {
        if (unlink("$type/" . $file)) {
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

        $newFile = "$type/". basename($file["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($newFile, PATHINFO_EXTENSION));

        // Checking the file type
        switch($type) {
            case ("docs"): 
                $uploadOk = $this->checkDoc($fileType, $uploadOk); 
                break; 
            case ("images"):
                $uploadOk = $this->checkImg($fileType, $uploadOk);  
                break; 
        }

        // Checking if file already exists
        // NOTE: if exists, make so that it uses existing file?
        if (file_exists($newFile)) {
            // if ($type == "images") {
            //     // $database->insertValues
            // } else {
            //     echo "<p class='header-notif'>A file with this name and type already exists.</p>";
            //     $uploadOk = 0;
            // }

            echo "<p class='header-notif'>A file with this name and type already exists.</p>";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "<p class='header-notif'>Your file was not able to upload.</p>";
        } else {
            if (move_uploaded_file($file["tmp_name"], $newFile)) {
                echo "<p class='header-notif'>$file[name] successfully uploaded.</p>";

                return true; 
            } else {
                echo "<p class='header-notif'>$file[name] was unable to upload.</p>";
            }
        }

        return null; 
    }

    // Checking if doc fits accepted file types
    function checkDoc($fileType, $uploadOk) {
        if ($fileType != "doc" && $fileType != "docx" && $fileType != "rtf" && $fileType != "txt") {
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