<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('storeFile')) {
    function storeFile($file, $folderName)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $fileName = Str::uuid() . "." . $file->extension();
        $folder = trim($folderName, '/');
        $filePath = $file->storeAs("uploads/${folder}", $fileName, 'public');
        //return path without public
        return str_replace('public/', 'storage/', $filePath);
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($filePath)
    {
        if (!$filePath) {
            return false;
        }
        $storagePath = str_replace('storage/', 'public/', $filePath);
        Storage::disk('public')->delete($storagePath);
        return true;
    }
}
