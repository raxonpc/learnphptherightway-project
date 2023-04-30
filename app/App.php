<?php

declare(strict_types=1);

// Your Code
function get_transactions(string $file_path): array|null
{
    if (!is_file($file_path)) {
        return null;
    }

    $file = fopen($file_path, "r");
    if ($file === false) {
        return null;
    }

    $rows = [];
    $row_count = 0;
    while (($row = fgetcsv($file)) !== false) {
        $row_count = array_push($rows, $row);
    }
    return $row_count === 0 ? null : $rows;
}

echo "<pre>\n";
print_r(get_transactions(FILES_PATH . "sample_1.csv"));
echo "</pre>\n";