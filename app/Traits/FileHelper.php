<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

trait FileHelper
{
    public static function uploadFile($file, $repository, $accessFromProject = false): string
    {
        $fileName = self::fileName($file);

        $realPath = $repository . $fileName;

        Storage::disk('public')->put($realPath, file_get_contents($file));

        $filePath   = 'public' . $realPath;

        return $filePath;


        // for shared hosting

        // $fileName = self::fileName($file);

        // $realPath = $repository . $fileName;

        // $file->move(public_path($repository), $fileName);

        // $filePath = $realPath;

        // if ($accessFromProject == true) {
        //     $filePath   = getcwd() . $realPath;
        // }

        // return $filePath;
    }

    public static function fileName($file): string
    {
        return  Carbon::now()->format('Y_m_d_u') . '_' . $file->getClientOriginalName();
    }

    public static function deleteFile($fileName): bool
    {
        if (file_exists(public_path($fileName))) {
            unlink(public_path($fileName));
            return true;
        }
        return false;
    }

    public static function getFileExtension($FilePath): string
    {
        $infoPath = pathinfo(public_path($FilePath));

        return $infoPath['extension'];
    }

    public static function UploadMultipleFile($files, $repository, $accessFromProject = false): string
    {
        $files = [];
        foreach ($files as $key => $file) {
            $file_name = time() . rand(1, 99) . '.' . $file->extension();
            $file->move(public_path('uploads'), $file_name);
            $files[]['name'] = $file_name;
        }

        foreach ($files as $key => $file) {
            File::create($file);
        }
        return true;
    }

    public static function generateExcelFile($collection, $repository, $fileName): string
    {
        $repository = 'storage'. $repository; //comment this line for 000webhost
        // if (!File::exists($repository)) {
        //     File::makeDirectory($repository, 0777, true);
        // }

        $filePath = $repository . Carbon::now()->format('Y_m_d_u') . '_' . $fileName;
        // solve excel exporting problem by zena
        // (new FastExcel($collection))->export(public_path($filePath));
        (new FastExcel($collection))->export(($filePath));

        return $filePath;
    }

}
