<?php

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

echo __DIR__; 
require_once "../vendor/autoload.php"; 

// Checks file type and reads file accordingly 
function readFromFile($file, $type) {
    $text = '';

    if ($type == "txt") {
        $text = file_get_contents("docs/$file"); 
    } else {
        $objReader = WordIOFactory::createReader('Word2007');
        $phpWord = $objReader->load("docs/$file"); 

        function getWordText($element) {
            $result = '';
            if ($element instanceof AbstractContainer) {
                foreach ($element->getElements() as $element) {
                    $result .= getWordText($element);
                }
            } elseif ($element instanceof Text) {
                $result .= $element->getText();
            }

            return $result;
        }

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= getWordText($element);
            }
        }
    }

    return $text; 
}
?>