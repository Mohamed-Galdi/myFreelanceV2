<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TempFile;

class FileService
{
    public static function moveTempFile(string $folder, string $destinationPath, string $title): ?string
    {
        $tempFile = TempFile::where('folder', $folder)->first();

        if ($tempFile) {
            $slug = Str::slug($title);
            $uniqueName = "{$slug}-" . time() . "-" . Str::random(8) . "." . pathinfo($tempFile->name, PATHINFO_EXTENSION);
            $filePath = "{$destinationPath}/{$uniqueName}";

            Storage::disk('public')->move($tempFile->path, $filePath);

            Storage::disk('public')->deleteDirectory("TempFiles/{$tempFile->folder}");
            $tempFile->delete();

            $finalFilePath = 'storage/' . $filePath;

            return $finalFilePath;
        }

        return null;
    }
}
