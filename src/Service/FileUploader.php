<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    private string $targetDirectory;
    public function __construct(private SluggerInterface $slugger ) {}

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function upload(UploadedFile $file, String $path): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory().'/', $fileName);
        } catch (FileException $e) {
            return $e->getMessage();
        }

        return $fileName;
    }

    
}