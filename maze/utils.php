<?php
/**
 * Check that a maze is valid.
 * 1. Has a start and finish.
 * 2. Can reach the finish from the start.
 */
function isValidMaze($grid):bool {
    $start = findCell($grid, START);
    $end = findCell($grid, END);
    $continue = true;
    $track = [$start];
    $currentCell = $start;

    // Loop until we either can't continue or we have reached the end.
    while($track[count($track) - 1] != $end) {
        $currentCell = $track[count($track) - 1];
        // Neighbors of cell
        $neighbors = find_neighbors($grid, $cell, PATH);

        // Check that we need to go back a few steps.
        if (count($neighbors) <= 1) {
        }
        foreach ($neighbors as $neighbor) {
            // Check that the neigbors has more than one path neighbor.
            $pathNeighbors = find_neighbors($grid, $neighbor, PATH);
            if (count($pathNeighbors) <= 1) {
                continue;
            }
        
            // Add only if not in neighbor list.
            if (!in_array($neighbor, $track)) {
                $track[] = $neighbor;
            }
        }
    }


    return true;
}