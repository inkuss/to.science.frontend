<?php

/**
 * Provides a form to configure access for entities.
 */
function edoweb_basic_access_form($form, &$form_state, $entity) {
    
    $form['basic_entity'] = array(
        '#type' => 'value',
        '#value' => $entity,
    );
    
    $form['access_md'] = array(
        '#type' => 'radios',
        '#title' => t('Metadata'),
        '#default_value' => isset($entity->access_md) ? $entity->access_md : variable_get('access_md_default'),
        '#options' => array(
            'public' => t('Public'),
            'private' => t('Private'),
        ),
    );
    
    $form['access_data'] = array(
        '#type' => 'radios',
        '#title' => t('Data'),
        '#default_value' => isset($entity->access_data) ? $entity->access_data : variable_get('access_data_default'),
        '#options' => array(
            'public' => t('Public'),
            'private' => t('Private'),
            'restricted' => t('Restricted'),
            'remote' => t('Remote'),
            'single' => t('Single'),
        ),
    );
    
    $children = field_get_items('edoweb_basic', $entity, 'field_edoweb_struct_child');
    if (FALSE !== $children) {
        $form['subtree_apply'] = array(
            '#type' => 'checkbox',
            '#title' => t('Auch auf untergeordnete Objekte anwenden'),
        );
    }
    
    // Submit button
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Apply'),
    );
    
    _edoweb_build_breadcrumb($entity);
    return $form;
}

function edoweb_basic_access_form_submit($form, &$form_state) {
    $entity = $form_state['values']['basic_entity'];
    
    $access_md = isset($form_state['values']['access_md'])
    ? $form_state['values']['access_md'] : FALSE;
    
    $access_data = isset($form_state['values']['access_data'])
    ? $form_state['values']['access_data'] : FALSE;
    
    $subtree_apply = isset($form_state['values']['subtree_apply'])
    ? $form_state['values']['subtree_apply'] : FALSE;
    
    if ($access_md) $entity->access_md = $access_md;
    if ($access_data) $entity->access_data = $access_data;
    
    $api = new EdowebAPIClient();
    $api->setAccessRights($entity, $subtree_apply);
}