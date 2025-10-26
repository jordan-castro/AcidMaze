<?php

include_once __DIR__ . "/init.php";

$type = chooseRandomMazeType();
echo "Type chosen = " . typeName($type);

generateMaze(20, "debug.png", $type, true);