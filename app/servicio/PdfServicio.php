<?php

use Fpdf\Fpdf;

class PdfServicio extends Fpdf{
    function Header()
    {
        $this->SetFont('Arial','B',10);
        // Calculate width of title and position
        $w = $this->GetStringWidth("Ventas Cryptos")+6;
        $this->SetX((210-$w)/2);
        // Colors of frame, background and text
        $this->SetDrawColor(0,80,180);
        $this->SetFillColor(230,230,0);
        $this->SetTextColor(220,50,50);
        // Thickness of frame (1 mm)
        $this->SetLineWidth(1);
        // Title
        $this->Cell($w,9,"Ventas Cryptos",1,1,'C',true);
        // Line break
    }

    function Footer(){
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',10);
        // Text color in gray
        $this->SetTextColor(128);
        // Page number
        $this->Cell(0,10,'Vicente Montilla',0,0,'C');
    }
}

