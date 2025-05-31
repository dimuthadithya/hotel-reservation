<?php

/**
 * Utility functions for the hotel reservation system
 */

if (!function_exists('getRatingText')) {
    function getRatingText($rating)
    {
        if ($rating >= 9) return 'Excellent';
        if ($rating >= 8) return 'Very Good';
        if ($rating >= 7) return 'Good';
        if ($rating >= 6) return 'Fair';
        return 'Basic';
    }
}
