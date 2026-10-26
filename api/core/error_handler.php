<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error: [$errno] $errstr in $errfile on line $errline");
    if (ENVIRONMENT === 'development') {
        echo "<div class='error'>Error: $errstr</div>";
    }
});

set_exception_handler(function($e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code(500);
    if (ENVIRONMENT === 'development') {
        echo "<div class='error'>Exception: " . $e->getMessage() . "</div>";
    } else {
        echo "<div class='error'>Something went wrong. Please try again later.</div>";
    }
});
