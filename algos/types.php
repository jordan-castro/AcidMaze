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