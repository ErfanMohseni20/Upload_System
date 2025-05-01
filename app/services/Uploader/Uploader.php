<?php

namespace App\Services\Uploader;

use App\Exceptions\FileHasExistsException;
use App\Models\File as ModelsFile;
use File;
use Illuminate\Http\Request;
use League\CommonMark\Extension\Footnote\Event\FixOrphanedFootnotesAndRefsListener;
use PHPUnit\Event\TestData\DataFromDataProvider;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;

class Uploader {
        /**
     * @var Request
     */
    private $request;

    /**
     * @var StorageManager
     */
    private $storageManager;

    private $file;

    /**
     * @var FFMpegService
     */
    private $ffmpeg;
    public function __construct(Request $request , StorageManager $storageManager , FFMpegService $ffmpeg) {
        $this->request = $request ;
        $this->storageManager = $storageManager;
        $this->file = $request->file('file'); 
        $this->ffmpeg = $ffmpeg;
    }
    public function upload() {
        if ($this->isFileExists()) throw new FileHasExistsException('File has already uploaded');
        $this->putFileIntoStorage();
        return $this->saveFileIntoDatabase();

    }
    
    private function putFileIntoStorage()
    {
        $method = $this->isPrivate() ? 'putFileAsPrivate' : 'putFileAsPublic';

        $this->storageManager->$method($this->file->getClientOriginalName(), $this->file,$this->getType());

    }
    private function saveFileIntoDatabase(){
        $file = new \App\Models\File([
            'name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
            'type' => $this->getType(),
            'is_private' => $this->isPrivate()
        ]);
        $file->time = $this->getTime($file);
        $file->save();
    }

    private function isPrivate()
    {
        return $this->request->has('is-private');
    }

    private function getType()
    {
        $types = [
            'image/jpeg' => 'image',
            'video/mp4' => 'video',
            'application/zip' => 'archive'
        ];
        $mimeType = $this->file->getClientMimeType();
        return $types[$mimeType] ?? 'unknown';
    }
    private function getTime(\App\Models\File $file) {
        if(!$file->isMedia()) {
            return null;
        }
        return $this->ffmpeg->durationOf($file->absolutePath());
    }
    private function isFileExists()
    {
       return $this->storageManager->isFileExists($this->file->getClientOriginalName(), $this->getType(), $this->isPrivate());
    }
}