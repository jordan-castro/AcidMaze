<?php

include_once __DIR__ . "/init.php";

$type = chooseRandomMazeType();

echo "Chosen type: " . typeName($type);

generateMaze(30, "maze.png", $type);
// genMaze(30, AlgorithmType::DungeonRooms);