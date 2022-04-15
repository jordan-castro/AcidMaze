<?php

/**
 * Glob the files of a certain DATE.
 */
function get_files(string $date):array {
    // Get all the files in the folder regardless of extension or name.
    $files = glob(__DIR__ . "/../mazes/" . $date . "*.txt");

    return $files;
}

/**
 * Cut file name into named JSON.
 */
function cut_file_name(string $fileName) {
    $slash = "/";
    if (strpos($fileName, $slash) === false) {
        $slash = "\\";
    }
    // Remove the slash from the name.
    $fileName = explode($slash, $fileName);
    $fileName = $fileName[count($fileName) - 1];

    // Remove the extension from the name.
    $fileName = explode(".", $fileName);
    $fileName = $fileName[0];

    // Now explode again to get the info
    $fileName = explode("_", $fileName);
    
    // Date is in Y-m-d format
    $date = strtotime($fileName[0]);
    // The size of the grid, rowsXcols
    $size = $fileName[1];
    // The algorithm used to create the maze
    $alg = $fileName[2];
    // (This is for internal use.) The number of the file (How many files have been made so far?)
    $number = (int) $fileName[3] ?? 0;

    $object = [
        "date" => $date,
        "size" => $size,
        "alg" => $alg,
        "number" => $number
    ];

    $object = json_encode($object);

    return json_decode($object);
}

/**
 * Name the file dog!
 */
function nameFile($size, $alg) {
    // Todays date
    $date = date("Y-m-d");
    // The initial file name
    $file = $date . "_" . $size . "_" . $alg;
    // Glob files
    $files = get_files($date);

    // Get the oldestFile (Technichally the youngest file) LOL.
    $oldestFile = null;
    foreach ($files as $f) {
        $data = cut_file_name($f);
        
        if ($oldestFile === null)
            $oldestFile = $data;
        else
            if ($data->number > $oldestFile->number) 
                $oldestFile = $data;
    }

    // Number is used for internal use. See [cut_file_name]
    if ($oldestFile !== null) {
        $number = $oldestFile->number;
        $number += 1;
    } else {
        $number = 0;
    }
    $file .= "_" . $number . ".txt";

    return $file;
}