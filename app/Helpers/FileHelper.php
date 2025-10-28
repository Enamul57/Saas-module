<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('storeFile')) {
    function storeFile($file, $folderName)
    {
        if (!$file) {
            return null;
        }
        if (is_string($file)) {
            return $file;
        }
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return null;
        }

        $fileName = Str::uuid() . "." . $file->extension();
        $folder = trim($folderName, '/');
        $filePath = $file->storeAs("uploads/${folder}", $fileName, 'public');
        //return path without public
        return asset("storage/{$filePath}");
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($filePath)
    {
        if (!$filePath) return false;

        // Remove full URL if stored as URL
        $filePath = str_replace(url('/') . '/storage/', '', $filePath);

        // Delete from public disk
        return Storage::disk('public')->delete($filePath);
    }
}

if (!function_exists('uploadAttachments')) {
    /**
     * Upload multiple attachments for a model.
     *
     * @param Model $model       The Eloquent model to attach files to.
     * @param array $files       Array of UploadedFile instances.
     * @param string $folderName Folder name inside storage/app/public.
     * @return void
     */
    function uploadAttachments(Model $model, array $files, string $folderName)
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $folder = trim($folderName, '/');
            $fileName = Str::uuid() . "." . $file->getClientOriginalExtension();
            $filePath = $file->storeAs("uploads/${folder}", $fileName, 'public');

            $model->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => asset("storage/${filePath}"),
                'file_type' => $file->getClientMimeType(),
            ]);
        }
    }
}

if (!function_exists('deleteAttachments')) {
    function deleteAttachments(Model $model, array $attachments = null)
    {
        $deleteAttachments = $attachments ?? $model->attachments;
        foreach ($deleteAttachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }
    }
}
