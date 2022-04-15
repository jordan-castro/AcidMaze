<?php

$password = $_SERVER['HTTP_PASSWORD'] ?? null;

if ($password !== "AcidMaze") {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? null;
if ($method === null) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

include_once __DIR__ . "/init.php";

// API starts here!
if ($method == "GET") {
    // Do some getting!
    $query = $_GET['query'] ?? null;

    if ($query === null) {
        header("HTTP/1.1 400 Bad Request");
        exit;
    }

    if ($query == "maze") {
        $files = get_files(date("Y-m-d"));
        // Choose a random file
        $file = $files[array_rand($files)];        

        $data = cut_file_name($file);
        $data = json_decode(json_encode($data), true);

        $data["maze"] = file_get_contents($file);

        echo json_encode($data);
        exit;
    }
}