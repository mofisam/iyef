<?php
require_once 'config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Get all settings
 * 
 * @return array Settings data
 */
function getSettings() {
    return fetchSingle("SELECT * FROM settings LIMIT 1");
}

/**
 * Update settings
 * 
 * @param array $settingsData Settings data
 * @return bool True on success, false on failure
 */
function updateSettings($settingsData) {
    // Get current settings to determine if we need to insert or update
    $currentSettings = getSettings();
    
    if ($currentSettings) {
        // Update existing settings
        $result = updateRecord('settings', $settingsData, 'id = ?', [$currentSettings['id']]);
    } else {
        // Insert new settings
        $result = insertRecord('settings', $settingsData);
    }
    
    return $result !== false;
}