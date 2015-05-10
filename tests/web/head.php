<?php

if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    header("HTTP/1.1 200");
} else {
    header("HTTP/1.1 400");
}
