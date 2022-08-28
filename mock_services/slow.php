<?php

declare(strict_types=1);

// Emulate processing 0 - 2 seconds
usleep(rand(0, 2000) * 1000);

echo json_encode([
    "coefficient" => rand(1, 100) / 10, // random 0.1 - 10
    "date"        => date("Y-m-d", time() + rand(86400, 86400 * 5)), // random 1 - 5 days
    "error"       => rand(0, 100) == 0 ? "some error" : null, // error emulation
]);
