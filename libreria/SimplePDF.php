<?php
class SimplePDF {
    private $width;
    private $height;
    private $pages = [];
    private $current = -1;

    public function __construct($width=595, $height=842) {
        $this->width = $width;
        $this->height = $height;
    }

    public function addPage() {
        $this->current++;
        $this->pages[$this->current] = '';
    }

    private function esc($s) { return str_replace(["\\","(",")"], ["\\\\","\\(","\\)"], $s); }

    public function text($x, $y, $txt, $size=12) {
        if ($this->current < 0) $this->addPage();
        $y = $this->height - $y;
        $this->pages[$this->current] .= "BT /F1 {$size} Tf {$x} {$y} Td (".$this->esc($txt).") Tj ET\n";
    }

    public function rect($x,$y,$w,$h,$fill=true) {
        if ($this->current < 0) $this->addPage();
        $y = $this->height - $y - $h;
        $op = $fill ? 'f' : 'S';
        $this->pages[$this->current] .= "{$x} {$y} {$w} {$h} re {$op}\n";
    }

    public function output() {
        $n = count($this->pages);
        $fontObj = 3 + 2*$n; // object number of font
        $pdf = "%PDF-1.4\n";
        $offset = strlen($pdf);
        $xref = [];
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $xref[1] = $offset;
        $offset = strlen($pdf);
        $kids = '';
        for ($i=0; $i<$n; $i++) {
            $kids .= (3+$i).' 0 R ';
        }
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [$kids] /Count $n >>\nendobj\n";
        $xref[2] = $offset;
        $offset = strlen($pdf);
        // page and content objects
        for ($i=0; $i<$n; $i++) {
            $pageNum = 3+$i;
            $contNum = 3+$n+$i;
            $pdf .= "$pageNum 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->width} {$this->height}] /Resources << /Font << /F1 {$fontObj} 0 R >> >> /Contents {$contNum} 0 R >>\nendobj\n";
            $xref[$pageNum] = $offset;
            $offset = strlen($pdf);
        }
        for ($i=0; $i<$n; $i++) {
            $contNum = 3+$n+$i;
            $content = $this->pages[$i];
            $len = strlen($content);
            $pdf .= "$contNum 0 obj\n<< /Length $len >>\nstream\n$content\nendstream\nendobj\n";
            $xref[$contNum] = $offset;
            $offset = strlen($pdf);
        }
        $pdf .= "$fontObj 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $xref[$fontObj] = $offset;
        $offset = strlen($pdf);
        $startxref = $offset;
        $pdf .= "xref\n0 ".($fontObj+1)."\n0000000000 65535 f \n";
        for ($i=1; $i<=$fontObj; $i++) {
            $pos = $xref[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $pos);
        }
        $pdf .= "trailer << /Size ".($fontObj+1)." /Root 1 0 R >>\nstartxref\n$startxref\n%%EOF";
        return $pdf;
    }
}
?>
