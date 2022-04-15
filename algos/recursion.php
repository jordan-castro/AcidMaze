<?php

/**
 * Using multiple algorithms make one master maze connecting them all.
 */
function makeRecursion(Algorithm &$alg, $algorithms = []) {
    $roomCount = count($algorithms);
    if ($roomCount == 0) {
        // Umm there is no maze then?
        return;
    }

    // Check that roomCount is odd
    if ($roomCount / 2 != 0) {
        // Make even
        $roomCount++;
    }

    // Shuffle the algs
    shuffle($algorithms);
    
    // Hold the grids out here dog
    $grids = [];
    for ($i = 0; $i < $roomCount; $i++) {
        $algorithm = null;
        if ($i >= count($algorithms)-1) {
            // Choose a random algorithm
            $algorithm = array_rand($algorithms);
        } else {
            // Go in order
            $algorithm = $algorithms[$i];
        }

        // Generate the maze
        $size = [$alg->size[0] / $roomCount, $alg->size[1] / $roomCount];
        $algor = new Algorithm($algorithm, $size, $alg->debug);
        $algor->generate();

        // Add the maze to the grid
        $grids[] = $algor->grid;
    }

    // Now to connect all the grids together
    $alg->grid = connectGrids($grids);

    debug($alg);
}

/**
 * Connect the grids together.
 */
function connectGrids($grids) {
    // Get the size of the grid
    $size = count($grids[0]) * count($grids);

    // Create a new grid
    $grid = createGrid($size, $size);

    // Loop through the grids
    for ($g = 0; $g < count($grids); $g++) {
        $start = findCell($grids[$g], START);
        $end = findCell($grids[$g], END);
        // Get rid of start and end on almost all of them
        if ($g > 0) {
            $grid[$start[0]][$start[1]] = WALL;
        } elseif ($g < count($grids) - 1) {
           $grid[$end[0]][$end[1]] = WALL;
        }
    }

    // Now connect the grids together
    for ($i = 0; $i < count($grids); $i++) {
        $currentGrid = $grids[$i];
        $size = $currentGrid[0];

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $grid[$y * $i][$x * $i] = $currentGrid[$y][$x];                
            }
        }
    }

    return $grid;
}