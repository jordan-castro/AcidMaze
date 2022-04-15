<?php

/**
 * Draw the maze to a image.
 */
function mazeToImage($grid, $name, $debug) {
    // Details
    $height = count($grid);
    $width = count($grid[0]);

    $image = imagecreate($width, $height);

    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 0, 0, 255);
    $red = imagecolorallocate($image, 255, 0, 0);

    // Now loop through the pixels and set the grid values
    for ($y = 0; $y < count($grid); $y++) {
        for ($x = 0; $x < count($grid[0]); $x++) {
            switch ($grid[$y][$x]) {
                case PATH:
                    // Set the pixel to white
                    imagesetpixel($image, $x, $y, $white);
                    break;
                case WALL:
                    // Set the pixel to black
                    imagesetpixel($image, $x, $y, $black);
                    break;
                case START:
                    $color = null;
                    if ($debug) {
                        $color = $blue;
                    } else {
                        $color = $white;
                    }
                    imagesetpixel($image, $x, $y, $color);
                    break;
                case END:
                    $color = null;
                    if ($debug) {
                        $color = $red;
                    } else {
                        $color = $white;
                    }
                    imagesetpixel($image, $x, $y, $color);
                    break;
                default:
                    break;
            }
        }
    }

    // Save the image and destroy the object
    imagepng($image, $name);
}

/**
 * Function to write a maze to a txt file.
 */
function writeMaze($grid, $fileName) {
    $contents = "";

    for ($i = 0; $i < count($grid); $i++) {
        for ($j = 0; $j < count($grid[$i]); $j++) {
            $contents .= $grid[$i][$j];
        }
        $contents .= "\n";
    }

    // Open and write
    $file = fopen($fileName, "w");
    fwrite($file, $contents);
    fclose($file);
}