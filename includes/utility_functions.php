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

if (!function_exists('showAlert')) {
    function showAlert($type, $title, $message)
    {
        $icon = '';
        switch ($type) {
            case 'success':
                $icon = 'check-circle';
                break;
            case 'error':
                $icon = 'exclamation-circle';
                $type = 'danger';
                break;
            case 'warning':
                $icon = 'exclamation-triangle';
                break;
            case 'info':
                $icon = 'info-circle';
                break;
        }

        return "
        <div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-{$icon} me-2'></i>{$title}</strong>
            " . ($message ? "<br>{$message}" : "") . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}
