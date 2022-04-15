<?php

/**
 * 1. Start with a grid full of walls.
 * 2. Pick a cell, mark it as part of the maze. Add the walls of the cell to the wall list.
 * 3. While there are walls in the list:
 *      1. Pick a random wall from the list. If only one of the cells that the wall divides is visited, then:
 *          1. Make the wall a passage and mark the unvisited cell as part of the maze.
 *          2. Add the neighboring walls of the cell to the wall list.
 *      2. Remove the wall from the list.
 */
function makePrim(Algorithm &$alg) {
    $currentCell = $alg->randomCell();
    $walls = [];

    // Mark cc as visited
    $alg->visit($currentCell);

    // Get the cells walls
    $wallsOfCell = $alg->findNeighbors($currentCell, WALL);
    // Add the walls
    foreach ($wallsOfCell as $wall) {
        $walls[] = $wall;
    }

    // 3. While there are walls in the list:
    while (count($walls) > 0) {
        // Shuffle to remove the undefined offsett error
        shuffle($walls);
        $index = rand(0, count($walls) - 1);
        // Pick a random wall from the list
        $ranWall = $walls[$index];

        // Check valid
        if (!isValidPrim($alg, $ranWall)) {
            // Remove wall
            unset($walls[$index]);
            continue;
        }

        // If the ranWall is only connected to one cell
        $ne = $alg->findNeighbors($ranWall, PATH);
        if (count($ne) == 1) {
            // A-Ok!
            $alg->visit($ranWall);
            // Add the walls of the $ranWall
            $wallsOfWall = $alg->findNeighbors($ranWall, WALL);
            foreach ($wallsOfWall as $wall) {
                // Append to walls
                $walls[] = $wall;
            }
        } 
        // Remove $ranWall from walls
        unset($walls[$index]);
    }

    primMakeAdjustments($alg);
    debug($alg);
}

/**
 * In order to be valid, the wall must be adjacent to a visited cell.
 */
function isValidPrim(Algorithm &$alg, $cell):bool {
    $amount = 0;
    
    // Get the edge cells
    $edges = [
        // Top left
        [-1, -1],
        // Top right
        [-1, 1],
        // Bottom left
        [1, -1],
        // Bottom right
        [1, 1]
    ];

    foreach ($edges as $edge) {
        $diff = [
            $edge[0] + $cell[0],
            $edge[1] + $cell[1]
        ];

        // If $diff is -1
        if ($diff[0] <= -1 || $diff[1] <= -1) {
            continue;
        } 

        // Check that the edge is a wall
        if (!isWall($alg, $diff)) {
            $amount++;
        }
    }

    return $amount < 2;
}

/**
 * Go through the grid and make some adjustments.
 * 
 * Block a cell if 
 * 1. it has 3 walls.
 * 2. It has one PATH.
 * 3. The paths of the path are greater than 1
 */
function primMakeAdjustments(Algorithm &$alg) {
    // Get all path cells
    $paths = getAllPaths($alg->grid);

    foreach ($paths as $path) {
        // Get Path and wall neighbors
        $pathNeighbors = $alg->findNeighbors($path, PATH);
        $wallNeighbors = $alg->findNeighbors($path, WALL);

        // If the $path has 3 wall neighbors
        if (count($wallNeighbors) == 3) {
            // Check if the $pathNeighbors is 1
            if (count($pathNeighbors) == 1) {
                // Now check the neighbor of the $pathNeighbors only has more 
                // than one path neighbor
                $nn = $alg->findNeighbors($pathNeighbors[0], PATH);
                if (count($nn) > 1) {
                    // Block path
                    $alg->block($path);
                }
            }
        }
    }
}