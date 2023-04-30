<?php

declare(strict_types=1);

// Your Code
function parse_csv(string $file_path): array|bool
{
    if (!is_file($file_path)) {
        return false;
    }

    $file = fopen($file_path, "r");
    if ($file === false) {
        return false;
    }

    $rows = [];
    $row_count = 0;
    while (($row = fgetcsv($file)) !== false) {
        $row_count = array_push($rows, $row);
    }
    return $row_count === 0 ? false : $rows;
}

function push_transaction(array &$transactions, array $transaction): void
{
    // if the array is empty, then include the first row(signature)
    if ($transactions === []) {
        $transactions = array_merge($transactions, $transaction);
        return;
    }

    // otherwise, check if the signatures match:
    if ($transactions[0] !== $transaction[0]) {
        return;
    }

    // and push the rows, excluding the signature
    $transactions = array_merge($transactions, array_slice($transaction, 1));
}

function read_files(array &$transactions, array $files): void
{
    foreach ($files as $file) {
        if (!is_file($file)) {
            continue;
        }

        $parsed = parse_csv($file);
        if ($parsed === false) {
            continue;
        }
        push_transaction($transactions, $parsed);
    }
}

function get_transactions(string $directory): array|bool
{
    if (!is_dir($directory)) {
        return false;
    }
    $files = scandir($directory);
    if ($files === false) {
        return false;
    }

    // append the directory at the front
    $files = array_map(fn($filename) => $directory . $filename, $files);

    $transactions = [];
    read_files($transactions, $files);

    return ($transactions === []) ? false : $transactions;
}

function amount_to_float(string $amount): float|bool
{
    if (!str_starts_with($amount, '$') && !str_starts_with($amount, '-')) {
        return false;
    }

    $value = str_replace(["$", ","], "", $amount);
    return floatval($value);
}

// use the first element of the array as keys for the new array
function translate_array(array|bool $transactions): array|bool
{
    if ($transactions === false) {
        return false;
    }

    $keys = $transactions[0];
    $output = [];
    foreach (array_slice($transactions, 1) as $transaction) {
        array_push($output, array_combine($keys, $transaction));
    }

    $func = function (array $data): array {
        $data["Amount"] = amount_to_float($data["Amount"]);
        return $data;
    };

    $output = array_map($func, $output);

    return $output;
}

function format_dollar_amount(float $amount): string
{
    if ($amount < 0) {
        $prefix = "-$";
    } else {
        $prefix = "$";
    }

    return $prefix . abs($amount);
}

function format_date(string $date): string
{
    return date('M j, Y', strtotime($date));
}