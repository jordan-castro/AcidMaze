<?php

/**
 * The algorthim object
 */
class Algorithm {
    public $alg;
    // The public size
    public $Size;
    // The actual size
    public $size;
    // TODO: add difficulty
    // Grid
    public $grid;
    // What was the last cell the alghorithm was on.
    public $lastCell;
    // Whether or not we are debugging
    public $debug;

    function __construct($algType, $size, $debug) {
        $this->alg = $algType;
        $this->debug = $debug;

        // Set the size
        $this->setSize($size);        

        // Set up the grid
        $this->setUpGrid();
    }

    /**
     * Setup the grid.
     */
    private function setUpGrid() {
        $this->grid = createGrid($this->size[0], $this->size[1]);
    }

    /**
     * Set the size
     */
    function setSize($size) {
        $this->Size[0] = (int) $size[0];
        $this->Size[1] = (int) $size[1];

        $rows = (2 * $this->Size[0]) + 1;
        $cols = (2 * $this->Size[1]) + 1;

        $this->size = [$rows, $cols];
    }

    /**
     * Function to find the neighbors of a cell.
     */
    function findNeighbors($cell, $value = null) {
        return find_neighbors($this->grid, $cell, $value);
    }

    /** 
     * Generate a maze
     */
    function generate($addStartEnd = true) {
        // Set a random seed
        srand(time());
            
        // Switch the type of algorithm
        switch ($this->alg) {
            case 0:
                makeAldous($this);
                break;
            case 1:
                makeBacktracing($this);
                break;
            case 2:
                makeDungeonRooms($this);
                break;
            case 3:
                makePrim($this);
                break;
            case 4:
                $algs = getRandomAlgorithms();
                makeRecursion($this, $algs);
                break;
        }

        if ($addStartEnd) {
            // Set start end
            addStartAndEnd($this);
        }
        // Debug
        debug($this);
    }

    /**
     * Get a random cell that is within the bounds.
     */
    function randomCell() {
        while (1) {
            $cell = [rand(1, $this->size[0] - 2), rand(1, $this->size[1] - 2)];

            // THE LAST CHECK.
            if (isWall($this, $cell)) {
                // Woohoo we found a valid cell. Now lets return it.
                return $cell;
            }
        }
    }

    /**
     * Visit a cell
     */
    function visit($cell) {
        // Set the cell to PATH
        setCell($this, $cell, PATH);
    }

    /**
     * Block a cell
     */
    function block($cell) {
        setCell($this, $cell, WALL);
    }

    /**
     * Check that a cell is on the grid.
     */
    function isOnGrid($cell) {
        if ($cell[0] < 0 || $cell[0] >= $this->size[0] || $cell[1] < 0 || $cell[1] >= $this->size[1]) {
            return false;
        }

        return true;
    }
}

/**
 * Get random alg types
 */
function getRandomAlgorithms() {
    // Black list
    $blackList = [AlgorithmType::DungeonRooms, AlgorithmType::Recursion];
    // White liest
    $whiteList = [AlgorithmType::AldousBroder, AlgorithmType::Backtracing, AlgorithmType::Prim];

    // Get a random number
    $rand = rand(0, count($whiteList) - 1);
    shuffle($whiteList);

    if ($rand == 0) {
        return [$whiteList[$rand]];
    }

    $algs = [];
    for ($i = 0; $i < $rand; $i++) {
        $algs[] = $whiteList[$i];
    }

    return $algs;
}