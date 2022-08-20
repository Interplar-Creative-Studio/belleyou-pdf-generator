<?php

namespace PDFGenerators;

if(!defined('PX_TO_PT')) {
    define('PX_TO_PT', 3 / 4);
}
abstract class PDFGenerator {
    abstract public function generatePDF();

    /*
     * @param $def array
     * @param $args array|null
     */
    public static function defaultArgs($def, $args = []){
        if(!is_array($args)) $args = [];
        $new = [];
        foreach ($def as $key => $value){
            $new[$key] = isset($args[$key]) ? $args[$key] : $value;
        }
        return $new;
    }
}
