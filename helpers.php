<?php
// Calculates the weighted sustainability score
/*
  @param int $pkg Packaging score (0-100)
 @param int $src Sourcing score (0-100)
 @param int $lng Longevity score (0-100)
 @return float Total weighted score
*/
function calculateSustainabilityScore($pkg, $src, $lng) {
    // Define weights as constants for easier updates
    $w_packaging = 0.30;
    $w_sourcing = 0.40;
    $w_longevity = 0.30;

    $total = ($pkg * $w_packaging) + ($src * $w_sourcing) + ($lng * $w_longevity);
    
    // Rounding to 2 decimal places
    return round($total, 2);
}

// Returns the text label based on score
?>