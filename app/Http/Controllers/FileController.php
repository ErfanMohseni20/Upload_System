<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\File;
use App\Services\Uploader\Uploader;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $uploader ;

    public function __construct(Uploader $uploader) {
        $this->uploader = $uploader;
    }
    public function Home(){
        $files =  File::all();

        return view('files.index' , compact('files'));
    }
    public function create(){
        return view('files.create');
    }
    public function UploadFile(FileUploadRequest $request) {
        try {
            // dd($request->all());
            $request->validated();
            $this->uploader->upload();
            return redirect()->back()->withSuccess('File has uploaded successfully');
        } catch(\Exception $e){

            return redirect()->back()->withError($e->getMessage());
        }
    }
    public function showFile(File $file) {
        return $file->download();
    }
    public function deleteFile(File $file) {
         $file->DeleteFile();
         return redirect()->back()->withSuccess('File has deleted successfully');    
        }
}
