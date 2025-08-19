<?php
class SimplePDF {
    private $width;
    private $height;
    private $pages = [];
    private $current = -1;

    public function __construct($width = 595, $height = 842) {
        // A4 en puntos por defecto (72 dpi): 595 x 842
        $this->width  = $width;
        $this->height = $height;
    }

    public function addPage() {
        $this->current++;
        $this->pages[$this->current] = '';
    }

    /**
     * Escapa paréntesis y backslashes para el contenido de texto PDF.
     * Recibe ya bytes en WinAnsi (NO UTF-8).
     */
    private function esc($s) {
        return str_replace(
            ["\\", "(", ")"],
            ["\\\\", "\\(", "\\)"],
            $s
        );
    }

    /**
     * Convierte desde UTF-8 a Windows-1252 (WinAnsi).
     * Usa mb_convert_encoding si está disponible; si no, iconv.
     * Si algún carácter no existe en WinAnsi, se intenta transliterar.
     */
    private function toWinAnsi($s) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        }
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);
        return $converted !== false ? $converted : $s;
    }

    /**
     * Dibuja texto. $x,$y en puntos desde la esquina inferior izquierda.
     * $size en puntos.
     */
    public function text($x, $y, $txt, $size = 12) {
        if ($this->current < 0) $this->addPage();
        // Convertir coordenada Y al sistema PDF (origen en esquina inferior)
        $y = $this->height - $y;

        // Convertimos a WinAnsi antes de escapar
        $bytes = $this->toWinAnsi($txt);
        $this->pages[$this->current] .=
            "BT /F1 {$size} Tf {$x} {$y} Td (" . $this->esc($bytes) . ") Tj ET\n";
    }

    /**
     * Dibuja rectángulo. Si $fill=true, rellena; si no, sólo traza.
     * $x,$y desde la esquina inferior izquierda (coordenadas de usuario).
     */
    public function rect($x, $y, $w, $h, $fill = true) {
        if ($this->current < 0) $this->addPage();
        // Ajustar Y al sistema PDF (origen abajo) y altura del rect
        $y = $this->height - $y - $h;
        $op = $fill ? 'f' : 'S';
        $this->pages[$this->current] .= "{$x} {$y} {$w} {$h} re {$op}\n";
    }

    /**
     * Devuelve el PDF completo como string.
     */
    public function output() {
        $n = count($this->pages);
        if ($n === 0) $this->addPage();

        $n = count($this->pages);
        $fontObj = 3 + 2 * $n; // número de objeto de la fuente

        $pdf = "%PDF-1.4\n";
        $xref = [];

        // Helper para registrar offsets
        $offset = strlen($pdf);
        $put = function($s) use (&$pdf, &$offset) {
            $pdf .= $s;
            $offset = strlen($pdf);
        };

        // 1) Catálogo
        $xref[1] = $offset;
        $put("1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n");

        // 2) Árbol de páginas
        $kids = '';
        for ($i = 0; $i < $n; $i++) {
            $kids .= (3 + $i) . " 0 R ";
        }
        $xref[2] = $offset;
        $put("2 0 obj\n<< /Type /Pages /Kids [$kids] /Count $n >>\nendobj\n");

        // 3..(2+n) Páginas
        for ($i = 0; $i < $n; $i++) {
            $pageNum = 3 + $i;
            $contNum = 3 + $n + $i;
            $xref[$pageNum] = $offset;
            $put("$pageNum 0 obj\n".
                "<< /Type /Page /Parent 2 0 R ".
                "/MediaBox [0 0 {$this->width} {$this->height}] ".
                "/Resources << /Font << /F1 {$fontObj} 0 R >> >> ".
                "/Contents {$contNum} 0 R >>\nendobj\n");
        }

        // Contenidos de cada página
        for ($i = 0; $i < $n; $i++) {
            $contNum = 3 + $n + $i;
            $content = $this->pages[$i];
            $len = strlen($content);
            $xref[$contNum] = $offset;
            $put("$contNum 0 obj\n<< /Length $len >>\nstream\n");
            $pdf .= $content;
            $offset = strlen($pdf);
            $put("endstream\nendobj\n");
        }

        // Objeto de fuente Type1 Helvetica con codificación WinAnsi
        $xref[$fontObj] = $offset;
        $put("$fontObj 0 obj\n".
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\n".
            "endobj\n");

        // xref
        $startxref = $offset;
        $size = $fontObj + 1;

        $pdf .= "xref\n0 $size\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $fontObj; $i++) {
            $pos = isset($xref[$i]) ? $xref[$i] : 0;
            $pdf .= sprintf("%010d 00000 n \n", $pos);
        }

        // trailer
        $pdf .= "trailer << /Size $size /Root 1 0 R >>\n";
        $pdf .= "startxref\n$startxref\n%%EOF";

        return $pdf;
    }
}

// ======= Ejemplo de uso =======
// $pdf = new SimplePDF();
// $pdf->addPage();
// $pdf->text(72, 100, "Evaluación: test final", 16);
// $pdf->rect(70, 120, 200, 30, false);
// header('Content-Type: application/pdf');
// echo $pdf->output();
