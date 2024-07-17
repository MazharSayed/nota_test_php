<?php

$directory = __DIR__ . '/datafiles';
$fileNames = [];

if (is_dir($directory)) {
    if ($handle = opendir($directory)) {
        while (false !== ($entry = readdir($handle))) {
            if (preg_match('/^[a-zA-Z0-9]+\.ixt$/', $entry)) {
                $fileNames[] = $entry;
            }
        }
        closedir($handle);
    } else {
        echo "Failed to open the directory.";
    }
} else {
    echo "Directory does not exist.";
}

sort($fileNames);

foreach ($fileNames as $fileName) {
    echo $fileName . PHP_EOL;
}
?>
