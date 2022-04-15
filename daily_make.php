<?php

include_once __DIR__ . "/init.php";

// Each day we will be making a new maze.

$algorithms = [
    AlgorithmType::DungeonRooms,
    AlgorithmType::AldousBroder,
    AlgorithmType::Prim,
    // AlgorithmType::Backtracing
];

$algorithm = $algorithms[array_rand($algorithms)];

$size = rand(10, 50);

genMaze(10, $algorithm);