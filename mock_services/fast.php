<?php

declare(strict_types=1);

// Emulate processing 0 - 2 seconds
usleep(rand(0, 2000) * 1000);

echo json_encode([
    "price"     => rand(1000, 10000) / 10, // random 100 - 1000
    "period"    => rand(1, 5), // random 1 - 5 days
    "error"     => rand(0, 100) == 0 ? "some error" : null, // error emulation
]);
