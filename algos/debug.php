<?php

/**
 * Draws the maze to a image.
 */
function debug(Algorithm &$alg) {
    if ($alg->debug) {
        // Sleep for milliseconds
        usleep(50000);
        mazeToImage($alg->grid, "debug.png", true);
    }
}