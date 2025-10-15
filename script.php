<?php
// hai sa facem un script php
//care sa imi afiseze cate fisiere sunt in fiecare directory
//exemplu
//root(5 files)
//- folder1 (3 files)
//-- subFolder2 (4 files)
//- folder3 (4 files)
//- folder (60 files)

function countFilesInDirectory($dir, $depth = 0) {
    if (!is_dir($dir)) {
        return 0;
    }

    $files = scandir($dir);
    $fileCount = 0;
    $totalFiles = 0;
    $directories = [];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_file($fullPath)) {
            $fileCount++;
            $totalFiles++;
        } elseif (is_dir($fullPath)) {
            $directories[] = $fullPath;
        }
    }

    $prefix = str_repeat('-', $depth);
    if ($depth > 0) {
        $prefix .= ' ';
    }

    $dirName = basename($dir);
    if ($depth === 0) {
        $dirName = 'root';
    }

    echo $prefix . $dirName . " ({$fileCount} files)\n";

    foreach ($directories as $subDir) {
        $totalFiles += countFilesInDirectory($subDir, $depth + 1);
    }

    return $totalFiles;
}

$currentDir = __DIR__;
$totalFiles = countFilesInDirectory($currentDir);
echo "\nTotal files: {$totalFiles}\n";
