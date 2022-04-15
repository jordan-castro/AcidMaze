<?php

abstract class Directions {
    const North = [-2, 0];
    const South = [2, 0];
    const East  = [0, 2];
    const West  = [0, -2];
}

/** The Algorithm
 *
 * Randomly choose a starting cell.
 * Randomly choose a wall at the current cell and open a passage through it to any random, unvisited, adjacent cell. This is now the current cell.
 * If all adjacent cells have been visited, back up to the previous and repeat step 2.
 * Stop when the algorithm has backed all the way up to the starting cell.
 *
 * Results
 * perfect
 *
 * Notes
 * This is perhaps the most common maze-generation algorithm because it is easy to understand and implement. And it produces high-quality mazes.
*/
function makeBacktracing(Algorithm &$alg) {
    // Contain the Track
    $track = []; 

    $possibleStarts = [
        [0, 0],
        [0, $alg->size[1] - 1],
        [$alg->size[0] - 1, 0],
        [$alg->size[0] - 1, $alg->size[1] - 1]
    ];
    // Choose a random start and add to track
    $start = $possibleStarts[rand(0, count($possibleStarts) - 1)];
    $track[] = $start;

    drawBacktracking($alg, $track);

    // Add a border to the grid.
    // This is a bit of a hack because we need to have a start and finish
    $alg->grid = addBorder($alg->grid);
    // Update size
    $size = [
        $alg->Size[0] + 1,
        $alg->Size[1] + 1
    ];
    $alg->setSize($size);

    // Debug after border
    debug($alg);
}

/**
 * What handles the drawing.
 * Calls go() to draw the track.
 */
function drawBacktracking(Algorithm &$alg, &$track) {
    // Get the current cell based on the track[-1]
    $cell = $track[count($track) - 1];
    $neighbors = aldousGetValidNeighbors($alg, $cell, false);

    // If there are no neighbors, back up
    if (count($neighbors) == 0) {
        $track = array_slice($track, 0, -1);
        return;
    } 

    // The directions the tracking can go in
    $directions = [
        Directions::North,
        Directions::South,
        Directions::East,
        Directions::West
    ];
    // Shuffle the directions
    shuffle($directions);    

    foreach ($directions as $direction) {
        go($alg, $cell, $direction, $track);
    }
}

// Try to do the backtracking for a cell.
function go(Algorithm &$alg, $cell, $direction, &$track) {
    $y = $cell[0];
    $x = $cell[1];

    // To be added to the track. Maybe
    $currentCell = [];

    // Shall we call the draw function again?
    $canGo = false;

    switch ($direction) {
        case Directions::South:
            if ($y + 1 < $alg->size[0] && isWall($alg, [$y + 2, $x])) {
                $canGo = true;
                for ($i = 0; $i < 3; $i++) {
                    if ($alg->isOnGrid([$y + $i, $x])) {
                        $alg->visit([$y+$i, $x]);
                    }
                }
                $currentCell = [$y + 2, $x];
            }
            break;
        case Directions::North:
            if ($y > 1 && isWall($alg, [$y - 2, $x])) {
                $canGo = true;
                for ($i = 0; $i < 3; $i++) {
                    $alg->visit([$y-2+$i, $x]);
                }
                $currentCell = [$y - 2, $x];
            }
            break;
        case Directions::East:
            if ($x + 1 < $alg->size[1] && isWall($alg, [$y, $x + 2])) {
                $canGo = true;
                for ($i = 0; $i < 3; $i++) {
                    if ($alg->isOnGrid([$y, $x+$i])) {
                        $alg->visit([$y, $x+$i]);
                    }
                }
                // Set new current
                $currentCell = [$y, $x + 2];
            }
            break;
        case Directions::West:
            if ($x > 1 && isWall($alg, [$y, $x - 2])) {
                $canGo = true;
                for ($i = 0; $i < 3; $i++) {
                    $alg->visit([$y, $x-2+$i]);
                }
                // Set new current
                $currentCell = [$y, $x - 2];
            }
            break;
    }

    // Check canGo
    if ($canGo) {
        // Recurse
        $track[] = $currentCell;
        drawBacktracking($alg, $track);
    }
}