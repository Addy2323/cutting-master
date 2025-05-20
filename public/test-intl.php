<?php
if (extension_loaded('intl')) {
    echo "The intl extension is loaded successfully!";
    
    // Test NumberFormatter
    $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    echo "\nTest currency formatting: " . $fmt->format(1234.56);
} else {
    echo "The intl extension is NOT loaded.";
} 