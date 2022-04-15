<?php

/**
 * Create a new grid.
 */
function createGrid(int $rows, int $columns) {
    assert($rows == $columns, "Rows must be equal to columns.");
    $grid = [];

    // Lopp through the rows
    for ($row = 0; $row < $rows; $row++) {
        // Make a array to hold the columns
        $grid[$row] = [];

        for ($column = 0; $column < $columns; $column++) {
            // Default is a wall
            $grid[$row][$column] = WALL;
        }
    }

    return $grid;
}

/**
 * Update a cells value on the grid.
 */
function setCell(Algorithm &$alg, $cell, $value) {
    // Set the last cell for possible undo
    $alg->lastCell = [
        "cell" => $cell,
        "val" => readCell($alg, $cell)
    ];

    // Update grid
    $alg->grid[$cell[0]][$cell[1]] = $value;

    // Debug
    debug($alg);
}

/**
 * Get the value of a cell
 */
function readCell(Algorithm $alg, $cell) {
    if (!$alg->isOnGrid($cell)) {
        $exception = "Cell: ";
        $exception .= $cell[0] . "," . $cell[1];
        $exception .= " is not on the grid, Count: ";
        $exception .= "(" . count($alg->grid) . "," . count($alg->grid[0]) . ")";
        $exception .= "\n Using algorithm: " . $alg->alg;;

        throw new Exception($exception);
    }

    return $alg->grid[$cell[0]][$cell[1]];
}

/**
 * Add a start and end to a grid.
 */
function addStartAndEnd(Algorithm &$alg) {
    // Get height and witdh of the grid
    $height = count($alg->grid);
    $width = count($alg->grid[0]);

    $startCells = [];
    $endCells = [];

    for ($i = 0; $i < $width; $i++) {
        $start = [0, $i];
        $end = [$height - 1, $i];

        // Check validity
        $sN = $alg->findNeighbors($start, PATH);
        $eN = $alg->findNeighbors($end, PATH);

        if (count($sN) > 0) {
            $startCells[] = $start;
        }
        if (count($eN) > 0) {
            $endCells[] = $end;
        }
    }

    for ($i = 0; $i < $height; $i++) {
        $start = [$i, 0];
        $end = [$i, $width - 1];
        // Check validity
        $sN = $alg->findNeighbors($start, PATH);
        $eN = $alg->findNeighbors($end, PATH);
        
        if (count($sN) > 0) {
            $startCells[] = $start;
        }
        if (count($eN) > 0) {
            $endCells[] = $end;
        }
    }

    // Remove any repeats
    $startCells = array_unique($startCells, SORT_REGULAR);
    $endCells = array_unique($endCells, SORT_REGULAR);

    // Now re-order just incase
    $startCells = array_values($startCells);
    $endCells = array_values($endCells);

    // Random start and end
    $start = $startCells[array_rand($startCells)];
    $end = $endCells[array_rand($endCells)];

    // Update grid
    setCell($alg, $start, START);
    setCell($alg, $end, END);
}

/**
 * Chek if a cell is a wall.
 */
function isWall(Algorithm $alg, $cell) {
    return readCell($alg, $cell) == WALL;
}

/**
 * Add a border to a grid.
 */
function addBorder($grid) {
    // Make a new grid larger than the last by 2 each
    $newGrid = createGrid(count($grid) + 2, count($grid[0]) + 2);

    // Looop through new grid
    for ($y = 0; $y < count($newGrid); $y++) {
        for ($x = 0; $x < count($newGrid[0]); $x++) {
            if ($y == 0 || $x == 0) {
                // Set new border
                $newGrid[$y][$x] = WALL;
            } elseif ($x == count($newGrid)-1 || $y == count($newGrid[0])-1) {
                // Set new border
                $newGrid[$y][$x] = WALL;
            } else {
                // Set old grid
                $newGrid[$y][$x] = $grid[$y-1][$x-1];
            }
        }
    }

    return $newGrid;
}

/**
 * Find a cell in a grid.
 */
function findCell($grid, $value) {
    for ($y = 0; $y < count($grid); $y++) {
        for ($x = 0; $x < count($grid[0]); $x++) {
            if ($grid[$y][$x] == $value) {
                return [$y, $x];
            }
        }
    }
}

/**
 * Find neighbors of a cell.
 */
function find_neighbors($grid, $cell, $value = null) {
    $n = [];
    $row = $cell[0];
    $col = $cell[1];

    // If the row is greater than 1 then add the cell above
    if ($row > 1) {
        $n[] = [$row - 1, $col];
    }
    // If the row is less than the row length - 2 
    if ($row < count($grid) - 2) {
        $n[] = [$row + 1, $col];
    }
    // Same logic for column
    if ($col > 1) {
        $n[] = [$row, $col - 1];
    }
    if ($col < count($grid[1]) - 2) {
        $n[] = [$row, $col + 1];
    }

    // Filter by $value
    if ($value !== null) {
        $n = filterNeighbors($grid, $n, $value);
    }

    return $n;
}