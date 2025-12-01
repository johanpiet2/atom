<?php

/**
 * Museum Metadata helper functions.
 *
 * These functions are available in templates when the museum metadata plugin is enabled.
 */

/**
 * Get museum metadata adapter instance.
 *
 * @return \sfMuseumPlugin\Adapters\LaravelMuseumAdapter
 */
function get_museum_adapter()
{
    static $adapter = null;

    if (null === $adapter) {
        require_once sfConfig::get('sf_plugins_dir').'/sfMuseumPlugin/lib/Adapters/LaravelMuseumAdapter.php';
        $adapter = new \sfMuseumPlugin\Adapters\LaravelMuseumAdapter();
    }

    return $adapter;
}

/**
 * Check if information object has museum metadata.
 *
 * @param QubitInformationObject $object
 *
 * @return bool
 */
function has_museum_metadata($object)
{
    if (!$object || !$object->id) {
        return false;
    }

    $adapter = get_museum_adapter();

    return $adapter->hasMuseumMetadata($object->id);
}

/**
 * Get museum metadata for information object.
 *
 * @param QubitInformationObject $object
 *
 * @return null|array
 */
function get_museum_metadata($object)
{
    if (!$object || !$object->id) {
        return null;
    }

    $adapter = get_museum_adapter();
    $museumObject = $adapter->getMuseumMetadata($object->id);

    return $museumObject ? $museumObject->toArray() : null;
}

/**
 * Format museum measurements for display.
 *
 * @param array $measurements
 *
 * @return string
 */
function format_museum_measurements($measurements)
{
    if (empty($measurements)) {
        return '';
    }

    $adapter = get_museum_adapter();

    return $adapter->formatMeasurements($measurements);
}

/**
 * Get extent statement from measurements.
 *
 * @param array $measurements
 *
 * @return string
 */
function get_extent_statement($measurements)
{
    if (empty($measurements)) {
        return '';
    }

    $adapter = get_museum_adapter();

    return $adapter->getExtentStatement($measurements);
}

/**
 * Get work types for select dropdown.
 *
 * @return array
 */
function get_work_types()
{
    $adapter = get_museum_adapter();
    $workTypes = $adapter->getWorkTypes();
    $options = [];

    foreach ($workTypes as $type) {
        $config = $adapter->getWorkTypeConfig($type);
        $options[$type] = $config['label'] ?? ucwords(str_replace('_', ' ', $type));
    }

    return $options;
}

/**
 * Get materials for autocomplete.
 *
 * @return array
 */
function get_materials()
{
    $adapter = get_museum_adapter();

    return $adapter->getMaterials();
}

/**
 * Get techniques for autocomplete.
 *
 * @return array
 */
function get_techniques()
{
    $adapter = get_museum_adapter();

    return $adapter->getTechniques();
}

/**
 * Format material list for display.
 *
 * @param array $materials
 *
 * @return string
 */
function format_materials($materials)
{
    if (empty($materials)) {
        return '';
    }

    return implode(', ', $materials);
}

/**
 * Format technique list for display.
 *
 * @param array $techniques
 *
 * @return string
 */
function format_techniques($techniques)
{
    if (empty($techniques)) {
        return '';
    }

    return implode(', ', $techniques);
}

/**
 * Get work type label.
 *
 * @param string $workType
 *
 * @return string
 */
function get_work_type_label($workType)
{
    $adapter = get_museum_adapter();
    $config = $adapter->getWorkTypeConfig($workType);

    return $config['label'] ?? ucwords(str_replace('_', ' ', $workType));
}

/**
 * Format date range for display.
 *
 * @param null|string $earliest
 * @param null|string $latest
 *
 * @return string
 */
function format_date_range($earliest, $latest)
{
    if (empty($earliest) && empty($latest)) {
        return '';
    }

    if ($earliest && $latest && $earliest === $latest) {
        return $earliest;
    }

    if ($earliest && $latest) {
        return "{$earliest} - {$latest}";
    }

    if ($earliest) {
        return "From {$earliest}";
    }

    return "Until {$latest}";
}

/**
 * Check if museum metadata is enabled.
 *
 * @return bool
 */
function is_museum_metadata_enabled()
{
    return sfConfig::get('app_museum_metadata_enabled', true);
}
