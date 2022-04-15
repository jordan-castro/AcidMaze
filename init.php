<?php

include_once __DIR__ . "/algos/index.php";
include_once __DIR__ . "/maze/index.php";
include_once __DIR__ . "/utils/index.php";

/**
 * Generate a Maze
 */
function generateMaze(int $size, string $file, int $alg, bool $debug = false, $imageify = true):int {
    if ($size < 3) {
        return 1; // Too small
    }
    if ($size > 100) {
        return 2; // Too big
    }

    $maze = makeMaze(
        $size,
        $size,
        $alg,
        $debug
    );

    if ($imageify) {
        mazeToImage($maze, $file, $debug);
    }
    return 0; // A-ok!
}

/**
 * Generate a Maze outside of the Acid_Maze "namespace".
 */
function genMaze(int $size, int $alg) {
    // The $maze is a 2D array of cells.
    $maze = makeMaze(
        $size,
        $size,
        $alg,
        false
    );

    // Check that it is a valid maze. Has start and finish.
    // TODO: This ^^^

    // Save maze to a file.
    $folder = __DIR__ . "/mazes/";
    // Get todays date
    $file = nameFile($size, $alg);

    $path = $folder . $file;

    // Write the maze
    writeMaze($maze, $path);
}