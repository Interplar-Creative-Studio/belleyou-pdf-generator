<?php

namespace PDFGenerators;

use PDFGenerators\PDFGenerator;

class CertificatePDFGenerator extends PDFGenerator
{
    private $defaultScales;

    private $scale = PX_TO_PT;
    
    private $args;

    public function __construct($args = [])
    {
        $this->args = [
            'save_path' => null,
            'main_image' => null,
            'logo' => null, // not required
            'amount' => '',
            'title' => '',
            'subtitle' => '', // not required
            'order_number' => '',
            'order_number_text' => '',
            'barcode' => null,
            'barcode_text' => '',
            'scale' => 1,
        ];
        $args = self::defaultArgs($this->args, $args);
        $this->args = $args;

        $this->defaultScales = [
            'a4' => 0.34,
            'full' => 1,
        ];
        if($args['scale']){
            $scale = $args['scale'];
            if(is_string(($args['scale'])) and isset($this->defaultScales[$scale])){
                $scale = $this->defaultScales[$scale];
            }
            $this->scale = $scale * PX_TO_PT;
        }
    }

    public function generatePDF()
    {
        $pdf_width = 2480 * $this->scale;
        $pdf_height = 3508 * $this->scale;
        $pdf = new \tFPDF('P', 'pt', [$pdf_width, $pdf_height]);

        $pdf->AddFont('Montserrat SemiBold','','Montserrat-SemiBold.ttf', true);
        $pdf->AddFont('Montserrat Medium','','Montserrat-Medium.ttf', true);
        $pdf->AddFont('Montserrat Regular','','Montserrat-Regular.ttf', true);

        $pdf->SetFont('Montserrat SemiBold','',20);
        $pdf->SetTextColor(255, 255, 255);

        //$pdf->AddPage('P');
        $pdf->SetDisplayMode('real','default');
        $frame_width = $pdf->GetPageWidth();
        $frame_height = $pdf->GetPageHeight();

        // цвет страницы
        $pdf->SetXY(0, $frame_width);
        $pdf->SetFillColor(139, 154, 147);
        $pdf->Cell($frame_width, $frame_height, '', 0, 0, '', true);
        //$pdf->SetFillColor(0);

        // Шапка (фон + лого)
        list($bgw, $bgh) = getimagesize($this->args['main_image']);
        $header_height = $bgh * $frame_width / $bgw;
        $pdf->Image($this->args['main_image'], 0, 0, $frame_width, $header_height);
        try{
            list($logo_w, $logo_h) = getimagesize($this->args['logo']);
            $k = $logo_w / $logo_h;
            $logo_w = $frame_width * 0.31;
            $logo_h = $logo_w / $k;
            $pdf->Image($this->args['logo'], ($frame_width - $logo_w) / 2, ($header_height - $logo_h) / 2, $logo_w, $logo_h);
        }catch (\Throwable $exception){}

        $pdf->SetAutoPageBreak(false);

        $currentX = $header_height;
        dump($bgw, $bgh);
        dump($frame_width);
        dump($header_height);

        // Сумма купона
        $currentX += 70 * $this->scale;
        $pdf->SetXY(0, $currentX);
        $pdf->SetFont('Montserrat SemiBold','',185 * $this->scale);
        $amount_height = 219 * $this->scale;
        $pdf->Cell($frame_width, $amount_height, $this->args['amount'],0,0,'C',false);
        $currentX += $amount_height;

        $padding = 130 * $this->scale;

        // основной текст
        $currentX += 30 * $this->scale;
        $pdf->SetXY($padding, $currentX);
        $pdf->SetFont('Montserrat Medium','',57 * $this->scale);
        $title_height = 70 * $this->scale;
        $pdf->MultiCell($frame_width - $padding * 2, $title_height, $this->args['title'],0,'C',false);
        $currentX += $title_height * 3;

        // текст
        $currentX += 50 * $this->scale;
        $pdf->SetXY($padding, $currentX);
        $pdf->SetFont('Montserrat Regular','',36 * $this->scale);
        $subtitle_height = 43 * $this->scale;
        if($this->args['subtitle']){
            $pdf->MultiCell($frame_width - $padding * 2, $subtitle_height, $this->args['subtitle'],0,'C',false);
        }
        $currentX += $subtitle_height * 5;

        // номер заказа
        $currentX += 100 * $this->scale;
        $order_number_x = $padding + 55 * $this->scale;
        $pdf->SetXY($order_number_x, $currentX);
        $pdf->SetFont('Montserrat SemiBold','',120 * $this->scale);
        $order_number_height = 145 * $this->scale;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(4 * $this->scale);
        $pdf->Cell(702 * $this->scale, $order_number_height, '',1, 0,'C',false);
        $pdf->SetXY($order_number_x, $currentX + 10 * $this->scale);
        $pdf->Cell(702 * $this->scale, $order_number_height, $this->args['order_number'],0, 0,'C',false);
        $currentX1 = $currentX + $order_number_height;

        // номер заказа (подпись)
        $currentX1 += 14 * $this->scale;
        $pdf->SetXY($padding, $currentX1);
        $pdf->SetFont('Montserrat Regular','',32 * $this->scale);
        $order_number_text_height = 39 * $this->scale;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->MultiCell(812 * $this->scale, $order_number_text_height, $this->args['order_number_text'],0,'C',false);

        // штрихкод
        $currentX += 25 * $this->scale;
        list($barcode_w, $barcode_h) = getimagesize($this->args['barcode']);
        $k = $barcode_w / $barcode_h;
        $barcode_w = 620 * $this->scale;
        $barcode_h = $barcode_w / $k;
        $barcode_x = $frame_width - $barcode_w - $padding - 70 * $this->scale;
        $pdf->Image($this->args['barcode'], $barcode_x, $currentX, $barcode_w, $barcode_h);
        $currentX2 = $currentX + $barcode_h;

        // штрихкод (подпись)
        $currentX2 += 11 * $this->scale;
        $barcode_padding = 65 * $this->scale;
        $barcode_text_width = $barcode_w - $barcode_padding * 2;
        $pdf->SetXY($barcode_x + $barcode_padding, $currentX2);
        $pdf->SetFont('Montserrat Regular','',32 * $this->scale);
        $order_number_text_height = 39 * $this->scale;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->MultiCell($barcode_text_width, $order_number_text_height, $this->args['barcode_text'],0,'C',false);

        // сохранение
        $pdf->Output($this->args['save_path'], 'F', true);
    }
}