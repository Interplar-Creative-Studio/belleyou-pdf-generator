<?php

function dump(...$args){
    foreach ($args as $arg){
        print_r($arg);
        echo PHP_EOL . PHP_EOL;
    }
}

//require('./lib/FPDF/makefont/makefont.php');
//MakeFont(__DIR__ . '/files/fonts/Montserrat-Medium.ttf','cp1251');
//MakeFont(__DIR__ . '/files/fonts/Montserrat-Regular.ttf','cp1251');
//MakeFont(__DIR__ . '/files/fonts/Montserrat-SemiBold.ttf','cp1251');

//include_once './lib/FPDF/fpdf.php';
include_once './lib/tfpdf/tfpdf.php';
include_once './PDFGenerators/Autoloader.php';

$pdfgen = new \PDFGenerators\CertificatePDFGenerator([
    'main_image' => __DIR__ . '/files/images/background.jpg',
    'logo' => __DIR__ . '/files/images/logo.png',
    'amount' => "10 000 \u{20BD}",
    'title' => 'Подарочный сертификат дает право на приобретение товаров из ассортимента интернет-магазина и розничных магазинов Belle YOU на сумму указанного номинала.',
    'subtitle' => 'Если цена товара ниже номинала сертификата, остаток средств на сертификате сохраняется и может быть использован при следующей покупке. Если сумма заказа выше номинала сертификата, то недостающая сумма подлежит доплате.'
    ."\n\n". 'Сертификат не действует в Универмаге Цветной, Универмаге Стокманн и в корнерах магазинов Золотое яблоко.',
    'order_number' => 'А1FFFD',
    'order_number_text' => 'Ввести в поле «Подарочные сертификаты» при оформлении заказа в интернет-магазине',
    'barcode' => __DIR__ . '/files/images/81003597.jpg',
    'barcode_text' => 'Для предъявления на кассе в розничном магазине',
    'save_path' => __DIR__ . '\output\cert1.pdf',
]);
$pdfgen->generatePDF();
