<?php

namespace App\Service;

use App\Entity\Client;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpKernel\KernelInterface;
use TCPDF;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;




class PdfGeneratorService extends Fpdi
{

    private string $projectDir;

    public $files = array();


    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function getProjectDir(){
        return $this->projectDir;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }
    /*public function modifyPdf($pdfPath, Client $client): string
    {
        // 📌 Chemin du PDF modèle
        $outputPath = $this->getProjectDir().('/public/uploads/contracts/' . $client->getFirstName() . '_' .$client->getLastName() . "_".date('U').rand(1000,9999).".pdf") ;
        mkdir(dirname($outputPath), 0777, true);
        // 📌 Charger le fichier PDF et extraire le texte
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        // 📌 Données dynamiques
        $clientData = [
            '{{nom}}' => $client->getFirstName(),
            '{{prenom}}' => $client->getLastName(),
            '{{email}}' => $client->getEmail(),
            '{{phone}}' => $client->getIndicative().' '.$client->getPhone()
        ];

        // ✅ Remplacement des valeurs dans le texte
        $newText = str_replace(array_keys($clientData), array_values($clientData), $text);

        // ✅ Générer un nouveau PDF avec le texte modifié
        $tcpdf = new TCPDF();
        $tcpdf->AddPage();
        $tcpdf->SetFont('Helvetica', '', 12);
        $tcpdf->MultiCell(0, 10, $newText);

        // 📌 Sauvegarde du nouveau PDF
        $tcpdf->Output($outputPath, 'F');

        return $outputPath;
    }*/

    public function makePdf($pdfPath, Client $client, $contractSimple){
        $path = ('./public/uploads/contracts/' . $client->getFirstName() . '_' .$client->getLastName() . "_".date('U').rand(1000,9999).".pdf") ;

        $pdf = new Fpdi();
        $pdf->setSourceFile($pdfPath); // Load the PDF file

        $pageCount = $pdf->setSourceFile($pdfPath); // Get total pages

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 10, 10, 200);

            // 🖊️ Add text **only on the first page**
            switch ($contractSimple) {
                case '1':
                    $pdf = $this->writeOn($pdf, $pageNo, $client);
                    break;
                
                default:
                    $pdf = $this->writeOn($pdf, $pageNo, $client);
                    break;
            }
            
        }

        $pdf->Output($path, 'F'); // Save the final PDF

        return $path;
    }

    public function concat()
    {
        foreach($this->files AS $file) {
            $pageCount = $this->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $this->ImportPage($pageNo);
                $s = $this->getTemplatesize($pageId);
                $this->AddPage($s['orientation'], $s);
                $this->useImportedPage($pageId);
            }
        }
    }

    public function writeOn($pdf, $pageNo, $client){
        if ($pageNo === 3) {
            $pdf->SetFont('Arial', '', 11);
            $pdf->Text(110, 128, $client->getLastName());
            $pdf->Text(55, 128, $client->getFirstName());
            $pdf->Text(55, 135, $client->getEmail());
            $pdf->Text(110, 135, $client->getPhone());
        }
        return $pdf;
    }
}
