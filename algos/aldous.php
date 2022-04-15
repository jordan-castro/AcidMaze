<?php

/**
 * Make Aldous Broder maze using.
 * 1. Choose a random cell.
 * 2. Choose a random neighbor of the current cell and visit it. If the neighbor has not yet been visited, add the traveled edge to the spanning tree.
 * 3. Repeat step 2 until all cells have been visited.
 */
function makeAldous(Algorithm &$alg) {
    // Get a random cell to start at
    $cc = $alg->randomCell();

    // Mark cc as visited
    $alg->visit($cc);

    // A infinite loop (until there are no more valid cells)
    while (1) {
        // Get neighbors of currentCell
        $neighbors = aldousGetValidNeighbors($alg, $cc);

        // Check if we have no more
        if (count($neighbors) == 0) {
            // Get a new starter 
            $allPaths = getAllPaths($alg->grid);
            $gottem = false;
            foreach ($allPaths as $path) {
                $validNeighbors = aldousGetValidNeighbors($alg, $path);
                // If we have valid neighbors, we can start at this path
                if (count($validNeighbors) > 0) {
                    $cc = $path;
                    $gottem = true;
                    break;
                }
            }

            // Are we done or nah?
            if ($gottem) continue;
            else break;
        }

        // Choose a random neighbor
        $neighbor = $neighbors[rand(0, count($neighbors) - 1)];
        // Add to grid and update currentCell
        $alg->visit($neighbor);
        $cc = $neighbor;
    }

    // Run prim adjustments
    primMakeAdjustments($alg);
    debug($alg);
}

/**
 * Get valid neigbors for the aldous broder.
 * 
 * Extension on: utils.algo_utils.fiterNeighbors()
 */
function aldousGetValidNeighbors(Algorithm $alg, $cell, $usePrim = true) {
    $valid = [];

    $neighbors = $alg->findNeighbors($cell);
    // Filter the $neighbors so that we only get walls
    $neighbors = filterNeighbors($alg->grid, $neighbors, WALL);

    foreach ($neighbors as $n) {
        // Sometimes we dont want to use prim. F.E. If we are using backtracking
        if ($usePrim) {
            // Prim should define it as valid
            if (!isValidPrim($alg, $n)) {
                continue;
            }
        }
        $isValid = true;
    
        // Check that the neighbor is not connected to any other paths
        // Get neighbor's neighbors
        $nN = $alg->findNeighbors($n);

        // The neighbors can only be connected to the new neighbors at most.
        foreach ($nN as $nn) {
            if (!isWall($alg, $nn) && $nn != $cell) {
                $isValid = false;
                break;
            }
        }
        
        if ($isValid) {
            $valid[] = $n;
        }
    }

    return $valid;
}