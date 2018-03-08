<?php 

/**
 * Implements hook_menu().
 */
function edoweb_menu() {
    
    // Configuration section for Edoweb modules
    $items['edoweb/config'] = array(
        'title' => 'Configuration',
        'description' => 'Configuration options for the Edoweb modules',
        'position' => 'left',
        'weight' => -100,
        'page callback' => 'system_admin_menu_block_page',
        'access arguments' => array('administer edoweb repository'),
        'file' => 'system.admin.inc',
        'file path' => drupal_get_path('module', 'system'),
    );
    
    // Repository settings configuration
    $items['edoweb/config/settings'] = array(
        'title' => 'Einstellungen',
        'description' => 'Configuration for the repository.',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('edoweb_repository_configuration_form'),
        'access arguments' => array('administer edoweb repository'),
        'type' => MENU_NORMAL_ITEM,
    );
    
    // Paged search result listing for AJAX retrieval
    $items['edoweb/search'] = array(
        'page callback' => '_edoweb_search',
        'access arguments' => array('view any edoweb_basic entity'),
        'type' => MENU_CALLBACK
    );
    
    // List of templates
    $items['edoweb/templates'] = array(
        'page callback' => '_edoweb_templates',
        'access arguments' => array('view any edoweb_basic entity'),
        'type' => MENU_CALLBACK
    );
    
    // Returns last modified entity in tree
    $items['edoweb/lastmodified/%'] = array(
        'page callback' => '_edoweb_lastmodified',
        'page arguments' => array(2),
        'access arguments' => array('view any edoweb_basic entity'),
        'type' => MENU_CALLBACK
    );
    
    // Add new resources
    $items['resource/add/%'] = array(
        'title callback' => 'edoweb_basic_bundle_name',
        'title arguments' => array(2),
        'page callback' => 'edoweb_basic_add',
        'page arguments' => array(2),
        'access arguments' => array('create edoweb_basic entities'),
        'type' => MENU_CALLBACK,
    );
    
    // Resource landing page, list entities
    $items['resource'] = array(
        'title' => 'Edoweb',
        'page callback' => 'edoweb_info_page',
        'page arguments' => array('0'),
        'access arguments' => array('view any edoweb_basic entity'),
    );
    
    // The page to view our entities - needs to follow what
    // is defined in basic_uri and will use load_basic to retrieve
    // the necessary entity info.
    $items['resource/%edoweb_basic'] = array(
        'title callback' => 'entity_label',
        'title arguments' => array(EDOWEB_ENTITY_TYPE, 1),
        'page callback' => 'edoweb_basic_view',
        'page arguments' => array(1),
        // Access control handled by API
        'access callback' => TRUE,
    );
    
    // 'View' tab for an individual entity page.
    $items['resource/%edoweb_basic/view'] = array(
        'title' => 'View',
        'type' => MENU_DEFAULT_LOCAL_TASK,
        'weight' => -10,
    );
    
    // 'Edit' tab for an individual entity page.
    $items['resource/%edoweb_basic/edit'] = array(
        'title' => 'Edit',
        'page callback' => 'edoweb_basic_edit',
        'page arguments' => array(1),
        'access callback' => '_edoweb_is_editable_entity',
        'access arguments' => array(1, 'edit any edoweb_basic entity'),
        'type' => MENU_LOCAL_TASK,
    );
    
    // 'Status' tab.
    $items['resource/%edoweb_basic/status'] = array(
        'title' => 'Status',
        'page callback' => 'edoweb_basic_status',
        'page arguments' => array(1),
        'access callback' => '_edoweb_is_editable_entity',
        'access arguments' => array(1, 'edit any edoweb_basic entity'),
        'type' => MENU_LOCAL_TASK,
    );
    
    // 'Crawler settings' tab for an individual webpage.
    $items['resource/%edoweb_basic/crawler'] = array(
        'title' => 'Crawler settings',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('edoweb_basic_crawler_form', 1),
        'access callback' => '_edoweb_is_webpage_entity',
        'access arguments' => array(1, 'edit any edoweb_basic entity'),
        'type' => MENU_LOCAL_TASK,
    );
    
    // 'Admin' tab for an individual entity page.
    $items['resource/%edoweb_basic/admin'] = array(
        'title' => 'Admin',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('edoweb_basic_admin', 1),
        'access arguments' => array('edit any edoweb_basic entity'),
        'type' => MENU_LOCAL_TASK,
    );
    
    // 'Access' tab for an individual entity page.
    $items['resource/%edoweb_basic/access'] = array(
        'title' => 'Access',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('edoweb_basic_access_form', 1),
        'access callback' => '_edoweb_is_editable_entity',
        'access arguments' => array(1, 'edit any edoweb_basic entity'),
        'type' => MENU_LOCAL_TASK,
    );
    
    // 'Data' callback for entities' datastreams
    $items['resource/%edoweb_basic/data'] = array(
        'page callback' => 'edoweb_basic_data',
        'page arguments' => array(1),
        // Access control handled by API
        'access callback' => TRUE,
        'type' => MENU_CALLBACK,
    );
    
    // 'Structure' callback for entities.
    $items['resource/%edoweb_basic/structure'] = array(
        'page callback' => 'edoweb_basic_structure',
        'page arguments' => array(1),
        'access arguments' => array('view any edoweb_basic entity'),
        'type' => MENU_CALLBACK,
    );
    
    // 'Add Child' for entities that can have children.
    $items['resource/%edoweb_basic/children/add/%'] = array(
        'title callback' => 'edoweb_basic_bundle_name',
        'title arguments' => array(4),
        'page callback' => 'edoweb_basic_add',
        'page arguments' => array(4, 1),
        'access callback' => '_edoweb_field_access',
        'access arguments' => array(1, array('field_edoweb_struct_child'), 'create edoweb_basic entities'),
        'type' => MENU_CALLBACK,
    );
    
    // GND Autocompletion
    $items['edoweb/autocomplete'] = array(
        'page callback' => '_edoweb_autocomplete',
        'access arguments' => array('edit any edoweb_basic entity'),
        'type' => MENU_CALLBACK
    );
    
    // Facet browsing
    $items['browse/%'] = array(
        'page callback' => 'edoweb_basic_browse',
        'page arguments' => array(1),
        'access arguments' => array('view any edoweb_basic entity'),
        'type' => MENU_CALLBACK,
    );
    
    return $items;
}