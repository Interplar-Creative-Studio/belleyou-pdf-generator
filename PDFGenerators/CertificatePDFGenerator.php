<?php

namespace PDFGenerators;

use PDFGenerators\PDFGenerator;

class CertificatePDFGenerator extends PDFGenerator
{
    private $args;

    public function __construct($args = [])
    {
        $this->args = [
            'save_path' => null,
            'main_image' => null,
            'logo' => null,
            'amount' => '',
            'title' => '',
            'subtitle' => '',
            'order_number' => '',
            'order_number_text' => '',
            'barcode' => null,
            'barcode_text' => '',
        ];
        $args = self::defaultArgs($this->args, $args);
        $this->args = $args;
    }

    public function generatePDF()
    {
        $pdf_width = 2480 * PX_TO_PT;
        $pdf_height = 3508 * PX_TO_PT;
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
        list($logo_w, $logo_h) = getimagesize($this->args['logo']);
        $k = $logo_w / $logo_h;
        $logo_w = $frame_width * 0.31;
        $logo_h = $logo_w / $k;
        $pdf->Image($this->args['logo'], ($frame_width - $logo_w) / 2, ($header_height - $logo_h) / 2, $logo_w, $logo_h);

        $currentX = $header_height;

        // Сумма купона
        $currentX += 70 * PX_TO_PT;
        $pdf->SetXY(0, $currentX);
        $pdf->SetFont('Montserrat SemiBold','',185 * PX_TO_PT);
        $amount_height = 219 * PX_TO_PT;
        $pdf->Cell($frame_width, $amount_height, $this->args['amount'],0,0,'C',false);
        $currentX += $amount_height;

        $padding = 130 * PX_TO_PT;

        // основной текст
        $currentX += 30 * PX_TO_PT;
        $pdf->SetXY($padding, $currentX);
        $pdf->SetFont('Montserrat Medium','',57 * PX_TO_PT);
        $title_height = 70 * PX_TO_PT;
        $pdf->MultiCell($frame_width - $padding * 2, $title_height, $this->args['title'],0,'C',false);
        $currentX += $title_height * 3;

        // текст
        $currentX += 50 * PX_TO_PT;
        $pdf->SetXY($padding, $currentX);
        $pdf->SetFont('Montserrat Regular','',36 * PX_TO_PT);
        $subtitle_height = 43 * PX_TO_PT;
        $pdf->MultiCell($frame_width - $padding * 2, $subtitle_height, $this->args['subtitle'],0,'C',false);
        $currentX += $subtitle_height * 5;

        // номер заказа
        $currentX += 100 * PX_TO_PT;
        $order_number_x = $padding + 55 * PX_TO_PT;
        $pdf->SetXY($order_number_x, $currentX);
        $pdf->SetFont('Montserrat SemiBold','',120 * PX_TO_PT);
        $order_number_height = 145 * PX_TO_PT;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(4 * PX_TO_PT);
        $pdf->Cell(702 * PX_TO_PT, $order_number_height, '',1, 0,'C',false);
        $pdf->SetXY($order_number_x, $currentX + 10 * PX_TO_PT);
        $pdf->Cell(702 * PX_TO_PT, $order_number_height, $this->args['order_number'],0, 0,'C',false);
        $currentX1 = $currentX + $order_number_height;

        // номер заказа (подпись)
        $currentX1 += 14 * PX_TO_PT;
        $pdf->SetXY($padding, $currentX1);
        $pdf->SetFont('Montserrat Regular','',32 * PX_TO_PT);
        $order_number_text_height = 39 * PX_TO_PT;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->MultiCell(812 * PX_TO_PT, $order_number_text_height, $this->args['order_number_text'],0,'C',false);

        // штрихкод
        $currentX += 25 * PX_TO_PT;
        list($barcode_w, $barcode_h) = getimagesize($this->args['barcode']);
        $k = $barcode_w / $barcode_h;
        $barcode_w = 620 * PX_TO_PT;
        $barcode_h = $barcode_w / $k;
        $barcode_x = $frame_width - $barcode_w - $padding - 70 * PX_TO_PT;
        $pdf->Image($this->args['barcode'], $barcode_x, $currentX, $barcode_w, $barcode_h);
        $currentX2 = $currentX + $barcode_h;

        // штрихкод (подпись)
        $currentX2 += 11 * PX_TO_PT;
        $barcode_padding = 65 * PX_TO_PT;
        $barcode_text_width = $barcode_w - $barcode_padding * 2;
        $pdf->SetXY($barcode_x + $barcode_padding, $currentX2);
        $pdf->SetFont('Montserrat Regular','',32 * PX_TO_PT);
        $order_number_text_height = 39 * PX_TO_PT;
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->MultiCell($barcode_text_width, $order_number_text_height, $this->args['barcode_text'],0,'C',false);

        // сохранение
        $pdf->Output($this->args['save_path'], 'F', true);
    }
}