<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempFile;
use Illuminate\Support\Facades\Storage;

class TempFileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'filepond' => 'required',
        ]);

        if ($request->hasFile('filepond')) {
            $file = $request->file('filepond');
            $fileName = $file->getClientOriginalName();
            $folder = uniqid();
            $file->storeAs('TempFiles/' . $folder, $fileName, 'public');

            TempFile::create([
                'name' => $fileName,
                'folder' => $folder,
                'path' => "TempFiles/$folder/$fileName",
                'type' => $file->getMimeType(),
            ]);

            return $folder;
        }

        return '';
    }

    public function revert(Request $request, $fileFolder)
    {
        $tempFile = TempFile::where('folder', $fileFolder)->first();
        if ($tempFile) {
            Storage::disk('public')->deleteDirectory('TempFiles/' . $tempFile->folder);
            $tempFile->delete();
        }

        return '';
    }
}
