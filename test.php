<?php
echo "PHP is working! Current time: " . date('Y-m-d H:i:s') . "\n";
echo "Directory: " . __DIR__ . "\n";

// Check if Laravel files exist
if (file_exists(__DIR__ . '/artisan')) {
    echo "Laravel artisan file found\n";
} else {
    echo "Laravel artisan file NOT found\n";
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "Composer autoload found\n";
} else {
    echo "Composer autoload NOT found\n";
}

// Check environment
if (file_exists(__DIR__ . '/.env')) {
    echo ".env file found\n";
} else {
    echo ".env file NOT found\n";
}
