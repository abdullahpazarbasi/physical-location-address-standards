<?php

$rootDirectory = __DIR__ . DIRECTORY_SEPARATOR . "../resource/countries";

function extractFilePaths($rootDirectory, &$paths): void
{
    $filePaths = scandir($rootDirectory);
    foreach ($filePaths as $currentPath) {
        if (in_array($currentPath, [".", ".."])) {
            continue;
        }
        $p = realpath($rootDirectory . DIRECTORY_SEPARATOR . $currentPath);
        if (is_dir($p)) {
            extractFilePaths($p, $paths);
        } else {
            $paths[] = $p;
        }
    }
}

$paths = [];
extractFilePaths($rootDirectory, $paths);

foreach ($paths as $path) {
    if (preg_match('/\.json$/', $path) !== false) {
        $currentFileContent = file_get_contents($path);
        $currentModel = json_decode($currentFileContent, true);
        $currentCountryCode = $currentModel["code"];
        //
        //
        $currentFileContent = json_encode($currentModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $currentFileContent);
    }
}
