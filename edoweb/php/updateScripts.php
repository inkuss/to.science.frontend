<?php

function _update_edoweb_installed_fields($updated_fields = null) {
    
    module_load_install('edoweb');
    $installed_fields = _edoweb_installed_fields();
    
    // Update all fields by default
    if (is_null($updated_fields)) {
        $updated_fields = array_keys($installed_fields);
    }
    
    foreach ($updated_fields as $field_name) {
        $field_definition = $installed_fields[$field_name];
        $field_definition['field_name'] = $field_name;
        $installed_field = field_info_field($field_name);
        if (is_null($installed_field)) {
            $field_definition['storage'] = array(
                'type' => 'edoweb_storage',
            );
            field_create_field($field_definition);
            drupal_set_message("Created field $field_name.");
        } else if($installed_field['type'] != $field_definition['type']) {
            field_delete_field($field_name);
            field_purge_batch(10);
            $field_definition['storage'] = array(
                'type' => 'edoweb_storage',
            );
            field_create_field($field_definition);
            drupal_set_message("Recreated field $field_name.");
        } else {
            field_update_field($field_definition);
            drupal_set_message("Updated field $field_name.");
        }
    }
    
}

/*
 * Reload instance definitions from edoweb.install
 * @param $updated_instances
 *   array(
 *     'updated_bundle' => array(
 *       field_name_1, field_name_2, ...
 *     )
 *   )
 */
function _update_edoweb_installed_instances($updated_instances = null) {
    
    module_load_install('edoweb');
    $installed_instances = _edoweb_installed_instances();
    
    // Update all instances by default
    if (is_null($updated_instances)) {
        $updated_instances = array();
        foreach ($installed_instances as $bundle_type => $installed_fields) {
            foreach (array_keys($installed_fields) as $field_name) {
                $updated_instances[$bundle_type][] = $field_name;
            }
        }
    }
    
    foreach ($updated_instances as $bundle_type => $field_names) {
        foreach ($field_names as $field_name) {
            $field_instance = $installed_instances[$bundle_type][$field_name];
            $field_instance['field_name'] = $field_name;
            $field_instance['entity_type'] = 'edoweb_basic';
            $field_instance['bundle'] = $bundle_type;
            $defaults = _edoweb_field_instance_defaults($field_name);
            $field_instance += $defaults;
            $widget_weight = array_search(
                $field_name, array_keys($installed_instances[$bundle_type])
                );
            //
            if (FALSE !== $widget_weight) {
                $field_instance['widget']['weight'] = $widget_weight;
                unset($field_instance['display']);
                foreach (_edoweb_viewmodes_for($field_name) as $view_mode) {
                    $field_instance['display'][$view_mode]['weight'] = $widget_weight;
                    drupal_set_message("Setting widget weight for $field_name in bundle $bundle_type to $widget_weight for $view_mode");
                }
            }
            $installed_instance = field_info_instance(
                EDOWEB_ENTITY_TYPE, $field_name, $bundle_type
                );
            if (is_null($installed_instance)) {
                field_create_instance($field_instance);
                drupal_set_message(
                    "Created instance for field $field_name in bundle $bundle_type."
                    );
            } else {
                field_update_instance($field_instance);
                drupal_set_message(
                    "Updated instance for field $field_name in bundle $bundle_type."
                    );
            }
        }
    }
    
}

function _update_edoweb_js_permissions() {
    
    module_load_install('edoweb');
    
    $entity_info = entity_get_info(EDOWEB_ENTITY_TYPE);
    $permissions = array();
    foreach (array_keys($entity_info['bundles']) as $bundle_type) {
        $permissions[] = EDOWEB_ENTITY_TYPE . " entity js read $bundle_type";
    }
    
    $installed_permissions = _edoweb_installed_permissions();
    $role_ids = array(DRUPAL_ANONYMOUS_RID, DRUPAL_AUTHENTICATED_RID);
    foreach (array_keys($installed_permissions) as $role_name) {
        $role = user_role_load_by_name($role_name);
        $role_ids[] = $role->rid;
    }
    
    foreach ($role_ids as $role_id) {
        user_role_grant_permissions($role_id, $permissions);
    }
    
}

function _update_rdf_mapping($updated_mappings = null) {
    
    module_load_install('edoweb');
    $installed_fields = _edoweb_installed_fields();
    
    // Update mappings for all fields by default
    if (is_null($updated_mappings)) {
        $updated_mappings = array_keys($installed_fields);
    }
    
    // Add RDF type
    $updated_mappings[] = 'type';
    
    $rdf_mappings = edoweb_rdf_mapping();
    foreach ($rdf_mappings as $rdf_mapping) {
        foreach (array_keys($rdf_mapping['mapping']) as $field_name) {
            if (!in_array($field_name, $updated_mappings)) {
                unset($rdf_mapping['mapping'][$field_name]);
            }
        }
        rdf_mapping_save($rdf_mapping);
        drupal_set_message(
            'Updated RDF mappings for '
            . implode(', ', array_keys($rdf_mapping['mapping']))
            );
    }
    
}





/**
 * Add edoweb_backend_subscriber and edoweb_backend_remote roles and permissions
 */
function edoweb_update_7149() {
    module_load_install('edoweb');
    $permissions = _edoweb_installed_permissions();
    $subscriber_role = new stdClass();
    $subscriber_role->name = 'edoweb_backend_subscriber';
    user_role_save($subscriber_role);
    user_role_grant_permissions($subscriber_role->rid, $permissions[$subscriber_role->name]);
    $remote_role = new stdClass();
    $remote_role->name = 'edoweb_backend_remote';
    user_role_save($remote_role);
    user_role_grant_permissions($remote_role->rid, $permissions[$remote_role->name]);
}

/**
 * DELETE superfluous fields
 */
function edoweb_update_7179(){
    field_delete_field('field_thesis_information');
    field_delete_field('field_congress_title');
    field_delete_field('field_congress_location');
    field_delete_field('field_congress_duration');
    field_delete_field('field_year_of_copyright');
    field_delete_field('field_embargo_time');
    field_delete_field('field_license');
    field_delete_field('field_professional_group');
    field_delete_field('field_related_resource');
    field_purge_batch(10);
}

/**
 * Update all
 */
function edoweb_update_7213(){
    _update_edoweb_installed_fields();
    _update_edoweb_installed_instances();
    _update_rdf_mapping();
}
