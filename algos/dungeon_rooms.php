<?php

/**
 * Create a maze with rooms in it.
 * 
 * 1. Place a number of randomly sized and positioned rooms. If a room
 * overlaps an existing room, it is discarded. Any remaining rooms are
 * carved out.
 * 
 * 2. Any remaining solid areas are filled in with mazes. The maze generator
 * will grow and fill in even odd-shaped areas, but will not touch any
 * rooms.
 * 
 * 3. The result of the previous two steps is a series of unconnected rooms
 * and mazes. We walk the stage and find every tile that can be a
 * "connector". This is a solid tile that is adjacent to two unconnected
 * regions.
 * 
 * 4. We randomly choose connectors and open them or place a door there until
 * all of the unconnected regions have been joined. There is also a slight
 * chance to carve a connector between two already-joined regions, so that
 * the dungeon isn't single connected.
 */
function makeDungeonRooms(Algorithm &$alg) {
    $dungeon = new Dungeon($alg);
    $amount = rand(5, 30);
    $size = rand(10, $alg->size[1] / 4);

    $dungeon->carveRooms($amount, $size);
    $dungeon->addMaze();
    $dungeon->removeDeadEnds();
}

/**
 * Because this code is really special it gets it's own class lol.
 */
class Dungeon {
    /**
     * Array that holds every room in the Dungeon, holds 4 cells.
     * [
     *  topLeft,
     *  bottomRight
     * ]
     */
    public $rooms;

    /**
     * The father of the Dungeon
     */
    public Algorithm $alg;

    public function __construct(Algorithm $alg) {
        $this->alg = $alg;
        // Instantiate empty array
        $this->rooms = [];
    }

    /**
     * Carve out the rooms.
     */
    function carveRooms($roomCount, $roomSize) {
        // Rooms are carved like so:
        // A room is valid if it is not overlapping another room and if has walls around it.
        for ($i = 0; $i < $roomCount; $i++) {
            // Choose random room size
            $roomHeight = rand(4, $roomSize);
            $roomWidth = rand(4, $roomSize);

            // Choose random coordinates
            $topLeft = [
                rand(1, (count($this->alg->grid) - 1) - $roomHeight),
                rand(1, (count($this->alg->grid[0]) - 1) - $roomWidth)
            ];

            // Get bottomRight
            $bottomRight = [
                $topLeft[0] + $roomHeight,
                $topLeft[1] + $roomWidth
            ];

            $room = [
                $topLeft,
                $bottomRight
            ];
            // Check if room is valid
            if ($this->isValidRoom($room)) {
                // Add to rooms
                $this->addRoom($room);
            }

        }
        // echo "Room count: " . count($this->rooms) . "\n";
    }

    /**
     * This function adds the maze to the dungeon.
     */
    function addMaze() {
        $methods = [AlgorithmType::Prim, AlgorithmType::AldousBroder];
        // Get a random algorithm method
        $method = $methods[array_rand($methods)];
        // Setup and create the maze
        $algorithm = new Algorithm(
            $method, 
            $this->alg->Size, 
            $this->alg->debug
        );
        $algorithm->grid = $this->alg->grid;
        $algorithm->generate(false);

        // Add the maze to the dungeon
        $this->alg->grid = $algorithm->grid;

        // Loop through the rooms and hollow out each wall.
        // Also add a door to each room.
        foreach ($this->rooms as $room) {
            // $this->hollowOutBorder($room);
            $this->addDoor($room);
        }
    }

    /**
     * Remove dead ends.
     */
    function removeDeadEnds() {
        // Get all paths
        $paths = getAllPaths($this->alg->grid);

        foreach ($paths as $path) {
            $neighbors = $this->alg->findNeighbors($path, WALL);

            if (count($neighbors) == 3) {
                // We got a dead end
                $this->alg->block($path);
            }
        }
    }

    /**
     * Hollow out the borders of a room just in case.
     * This is more of a hack than a proper solution.
     */
    private function hollowOutBorder($room) {
        // Get border
        $roomBorder = $this->getRoomBorder($room, false);
        // Loop through border
        foreach ($roomBorder as $border) {
            $this->alg->block($border);
        }
    }

    /**
     * Add a door to a room. This is done by looping through the border and finding,
     * the walls that have a path neighbor, then just choose one of those.
     */
    private function addDoor($room) {
        // Get border
        $roomBorder = $this->getRoomBorder($room);
        $choices = [];

        // Loop through border and find a valid cell
        foreach ($roomBorder as $border) {
            // Get neighbors
            $neighbors = $this->alg->findNeighbors($border, PATH);
            
            if (count($neighbors) == 2) {
                // Add to choices
                $choices[] = $border;
            }
        }

        // Choose a random cell
        $door = $choices[array_rand($choices)];
        $this->alg->visit($door);
    }

    /**
     * Checking for valid room.
     * 
     * To be valid it has to:
     * 1. No overlap any other rooms.
     * 2. Must have a border of walls surrounding it. 
     */
    private function isValidRoom($room):bool {
        // Loop through all rooms
        foreach ($this->rooms as $otherRoom) {
            // Check if room overlaps another room
            if ($this->overlaps($room, $otherRoom)) {
                return false;
            }
        }
        // Check that room has a border
        $roomSize = $this->getRoomSize($room);
        $border = $this->getRoomBorder($room);

        $sizeToBe = (($roomSize[0] * 2) + ($roomSize[1] * 2) - 4);
        if (count($border) != $sizeToBe) {
            return false;
        }

        return true;
    }

    /**
     * Check if two rooms overlap.
     */
    private function overlaps($room, $otherRoom):bool {
        $roomCells = $this->getRoomCells($room);
        $otherRoomCells = $this->getRoomCells($otherRoom);

        // Check if there are any repeated cells
        foreach ($roomCells as $cell) {
            if (in_array($cell, $otherRoomCells)) {
                return true;
            }
        }

        // A-Ok none overlap
        return false;
    }

    /**
     * Get the border of a room.
     */
    private function getRoomBorder($room, $onlyWallCells = true) {
        $topLeft = $room[0];
        $bottomRight = $room[1];

        // Get height and width
        $size = $this->getRoomSize($room);
        $height = $size[0];
        $width = $size[1];

        // Get the border
        $border = [];
        for ($y = $topLeft[0]; $y < $bottomRight[0]; $y++) {
            for ($x = $topLeft[1]; $x < $bottomRight[1]; $x++) {
                if ($y == $topLeft[0]) {
                    $border[] = [$y - 1, $x];
                } else if ($y == $bottomRight[0] - 1) {
                    $border[] = [$y + 1, $x];
                } else if ($x == $topLeft[1]) {
                    $border[] = [$y, $x - 1];
                } else if ($x == $bottomRight[1] - 1) {
                    $border[] = [$y, $x + 1];
                }
            }
        }
        // No repeats
        $border = array_unique($border, SORT_REGULAR);
        $border = array_values($border);

        // Now loop through $border and check if wall and not off grid.
        foreach ($border as $index => $cell) {
            // Obv if it is off the grid than we want to take it off
            if (!$this->alg->isOnGrid($cell)) {
                unset($border[$index]);
                continue;
            } elseif ($onlyWallCells) {
                // If we only want wall cells:
                if (!isWall($this->alg, $cell)) {
                    unset($border[$index]);
                    continue;
                }
            }
        }
        // Reorder just incase.
        $border = array_values($border);
    
        return $border;
    }

    /**
     * Get height and width of a room.
     */
    private function getRoomSize($room) {
        $topLeft = $room[0];
        $bottomRight = $room[1];

        // Get difference of topLeft and bottomRight
        $height = $bottomRight[0] - $topLeft[0];
        $width = $bottomRight[1] - $topLeft[1];
        
        return [$height, $width];
    }

    /**
     * Add the room to the list and edit the grid.
     */
    private function addRoom($room) {
        $this->rooms[] = $room;

        // Get height and width
        $size = $this->getRoomSize($room);
        $height = $size[0];
        $width = $size[1];

        // Get the topLeft and bottomRight
        $topLeft = $room[0];

        // Loop through the room and edit the grid
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                // The cell is based on the topLeft
                $cell = [$topLeft[0] + $y, $topLeft[1] + $x];
                // Visit the cell
                $this->alg->visit($cell);
            }
        }

        // Now reorder the $rooms array just in case
        $this->rooms = array_values($this->rooms);
    }

    /**
     * Get all the cells of a room.
     */
    private function getRoomCells($room) {
        $size = $this->getRoomSize($room);
        $topLeft = $room[0];

        // Get the cells
        $cells = [];
        for ($y = 0; $y < $size[0]; $y++) {
            for ($x = 0; $x < $size[1]; $x++) {
                $cell = [$topLeft[0] + $y, $topLeft[1] + $x];
                $cells[] = $cell;
            }
        }

        return $cells;
    }
}