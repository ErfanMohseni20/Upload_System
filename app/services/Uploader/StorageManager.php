<?php

namespace App\Services\Uploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class  StorageManager
{
    public function putFileAsPrivate(string $name, UploadedFile $file, string $type)
    {
        return Storage::disk('private')->putFileAs($this->folderLocation($type), $file, $name);
    }
    public function putFileAsPublic(string $name, UploadedFile $file, string $type)
    {
        return Storage::disk('public')->putFileAs($this->folderLocation($type), $file, $name);
    }
    public function getAbsolutePathOf(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->path($this->fullRelativePath($this->folderLocation($type), $name));
    }
    public function isFileExists(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->exists($this->fullRelativePath($this->folderLocation($type), $name));
    }
    public function deleteFile(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->delete($this->fullRelativePath($this->folderLocation($type), $name));
    }
    public function getFile(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->download($this->fullRelativePath($this->folderLocation($type), $name));
    }
    private function disk(bool $isPrivate)
    {
        return $isPrivate ? Storage::disk('private') : Storage::disk('public');
    }

    public function getRelativePathOf(string $name, string $type): string
    {
        return $this->fullRelativePath($this->folderLocation($type), $name);
    }

    private function folderLocation(string $type)
    {
        $month = now()->format('m');
        $year = now()->format('Y');
        $folderAddress = "{$type}/{$year}/{$month}";
        return $folderAddress;
    }
    private function fullRelativePath(string $name, string $type): string
    {
        return $this->folderLocation($type) . DIRECTORY_SEPARATOR . $name;
    }
}
