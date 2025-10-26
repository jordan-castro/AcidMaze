<?php

// Enum values for the algorithm types
abstract class AlgorithmType {
    const AldousBroder = 0;
    const Backtracing = 1;
    const DungeonRooms = 2;
    const Prim = 3;
    const Recursion = 4;
}

// Wall, Path, Start, End
define("PATH", 0);
define("WALL", 1);
define("START", 2);
define("END", 3);

function typeName(int $type) {
    switch ($type) {
        case 0: 
            return "AldousBroder";
        case 1:
            return "Backtracing";
        case 2:
            return "DungeonRooms";
        case 3:
            return "Prim";
        case 4:
            return "Recursion";
        default:
            return "Unknown";
    }
}