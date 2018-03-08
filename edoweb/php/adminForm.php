<?php

/**
 * Provides a form to administrate entities.
 */
function edoweb_basic_admin($form, &$form_state, $entity) {
    
    $form['basic_entity'] = array(
        '#type' => 'value',
        '#value' => $entity,
    );
    
    $form['actions'] = array(
        '#type' => 'fieldset',
        '#title' => t('Actions'),
        '#weight' => 5,
    );
    
    $form['actions']['reload'] = array(
        '#type' => 'submit',
        '#value' => t('Reload'),
        '#submit' => array('edoweb_basic_admin_reload'),
        '#weight' => 100,
    );
    
    //  if (_is_edoweb_entity($entity)) {
    $api = new EdowebAPIClient();
    $has_urn = field_get_items('edoweb_basic', $entity, 'field_edoweb_urn') ? TRUE : FALSE;
    if (!$has_urn) {
        $form['actions']['urn'] = array(
            '#type' => 'submit',
            '#value' => t('Add URN'),
            '#submit' => array('edoweb_basic_admin_add_urn'),
            '#weight' => 50,
        );
    }
    $has_doi = field_get_items('edoweb_basic', $entity, 'field_edoweb_doi') ? TRUE : FALSE;
    //    if (!$has_doi) {
    $form['actions']['doi'] = array(
        '#type' => 'submit',
        '#value' => t('Add DOI'),
        '#submit' => array('edoweb_basic_admin_add_doi'),
        '#weight' => 50,
    );
    //  }
    $form['actions']['index'] = array(
        '#type' => 'submit',
        '#value' => t('Index'),
        '#submit' => array('edoweb_basic_admin_index'),
        '#weight' => 50,
    );
    $form['actions']['delete'] = array(
        '#type' => 'submit',
        '#value' => t('Delete'),
        '#submit' => array('edoweb_basic_admin_delete'),
        '#weight' => 200,
    );
    $form['transformers'] = array(
        '#type' => 'fieldset',
        '#title' => t('Transformers'),
        '#weight' => 5,
    );
    $transformers = $api->getTransformers($entity);
    $form['transformers']['transformers'] = array(
        '#type' => 'checkboxes',
        '#attributes' => array('disabled' => 'disabled'),
        '#options' => array(
            'epicur' => t('Register Urn'),
            'aleph' => t('Copy to Catalog'),
            'oaidc' => t('Support OAI-PMH'),
            'mets' => t('OAI-PMH METS'),
            'rdf' => t('OAI-PMH RDF'),
        ),
        '#default_value' => $transformers,
    );
    
    foreach ($transformers as $transformer) {
        $label = $form['transformers']['transformers']['#options'][$transformer];
        $edoweb_api_host = variable_get('edoweb_api_host');
        $api_link = l(
            $label,
            "{$edoweb_api_host}/resource/{$entity->remote_id}.$transformer",
            array('attributes' => array('target'=>'_blank'))
            );
        $form['transformers']['transformers']['#options'][$transformer] = $api_link;
    }
    
    //}
    
    _edoweb_build_breadcrumb($entity);
    return $form;
}

/**
 * Form index handler.
 *
 */
function edoweb_basic_admin_index( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $api = new EdowebAPIClient();
    $api->index($entity);
    $form_state['redirect'] = 'resource/' . $entity->remote_id;
}

/**
 * Form reload handler.
 *
 */
function edoweb_basic_admin_reload( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    entity_get_controller('edoweb_basic')->clearCache($entity->remote_id);
    $form_state['redirect'] = 'resource/' . $entity->remote_id;
}

/**
 * Form deletion handler.
 *
 */
function edoweb_basic_admin_delete( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    edoweb_basic_delete($entity);
    $parents = field_get_items('edoweb_basic', $entity, 'field_edoweb_struct_parent');
    $parent_id = '';
    if (FALSE !== $parents) {
        foreach($parents as $parent) {
            $parent_id = $parent['value'];
        }
    }
    $form_state['redirect'] = "resource/$parent_id";
}

/**
 * Form transformers handler.
 *
 */
function edoweb_basic_admin_apply_transformers( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $transformers = array_keys(array_filter($form_state['values']['transformers']));
    $api = new EdowebAPIClient();
    $api->saveResource($entity, $transformers);
}

/**
 * Form add URN handler.
 *
 */
function edoweb_basic_admin_add_urn( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $api = new EdowebAPIClient();
    $api->addURN($entity);
}

/**
 * Form add DOI handler.
 *
 */
function edoweb_basic_admin_add_doi( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $api = new EdowebAPIClient();
    $api->addDOI($entity);
}
