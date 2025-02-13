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




class ConcatService extends Fpdi
{

    public $files = array();

    //public function __construct(){}

    public function setFiles($files)
    {
        $this->files = $files;
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
}
