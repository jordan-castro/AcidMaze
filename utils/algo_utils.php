<?php

/**
 * Get all walls of a grid.
 */
function getAllWalls($grid) {
    return getCells($grid, WALL);
}

/**
 * Get all paths of a grid.
 */
function getAllPaths($grid) {
    return getCells($grid, PATH);
}

/**
 * Get all cells of a certain value.
 */
function getCells($grid, $value) {
    $cells = [];
    
    for ($y = 0; $y < count($grid); $y++) {
        for ($x = 0; $x < count($grid[0]); $x++) {
            if ($grid[$y][$x] == $value) {
                $cells[] = [$y, $x];
            }
        }
    }

    return $cells;
}

/**
 * Filter the neighbors of a cell.
 */
function filterNeighbors($grid, $n, $s) {
    $cells = [];

    foreach ($n as $cell) {
        if ($grid[$cell[0]][$cell[1]] == $s) {
            $cells[] = $cell;
        }
    }

    return $cells;
}