<?php

namespace App\Models;

use App\Services\Uploader\StorageManager;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['name' , 'size' , 'time' , 'type' , 'is_private'];

    public function isMedia(){
        return $this->type == 'video';
    }
    public function absolutePath(){
        return resolve(name: StorageManager::class)->getAbsolutePathOf($this->name , $this->type , $this->is_private);
    }
    public function download() {
        return resolve(name: StorageManager::class)->getFile($this->name , $this->type , $this->is_private);

    }
    public function DeleteFile() {
        $deleted = resolve(StorageManager::class)->deleteFile($this->name, $this->type, $this->is_private);
        if ($deleted) {
            return parent::delete();
        }
        throw new \Exception('Failed to delete file from storage.');
    } 
}
