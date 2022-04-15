<?php

function makeMaze($rows, $column, $alg, $debug) {
    $algorithm = new Algorithm($alg, [$rows, $column], $debug);
    $algorithm->generate();

    return $algorithm->grid;
}