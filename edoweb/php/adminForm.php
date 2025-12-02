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
    if ($conf = $api->getCrawlerConfiguration($entity)) {
        $form['actions']['delete_keep_webarchives'] = array(
            '#type' => 'submit',
            '#value' => t('Löschen, behalte Webarchive'),
            '#submit' => array('edoweb_basic_admin_delete_keep_webarchives'),
            '#weight' => 180,
        );
    }

    $toscience_import_server_name = variable_get('toscience_import_server_name');
    if ($toscience_import_server_name != '' && $conf ) {
    	$form['actions']['importWS'] = array(
        	'#type' => 'fieldset',
        	'#title' => t('Import Webschnitt'),
        	'#weight' => 300,
    	);
    	$form['actions']['importWS']['quellserver'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver'),
        	'#name' => 'quellserver',
        	'#attributes' => array('disabled' => 'disabled'),
        	'#default_value' => $toscience_import_server_name
    	);
    	$form['actions']['importWS']['quellwebpage'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver Website PID'),
        	'#name' => 'quellwebpage',
        	'#default_value' => @$conf['quellserverWebpagePid'] == null ? 'edoweb:NNNN' : @$conf['quellserverWebpagePid'],
    	);
   	if( $entity->bundle() == 'version') {
        	$form['actions']['importWS']['quellwebpage']['#attributes'] = array('readonly' => 'readonly');
    	}
    	$form['actions']['importWS']['quellwebschnitt'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver Webschnitt PID'),
        	'#name' => 'quellwebschnitt',
        	'#default_value' => @$conf['quellserverWebschnittPid'] == null ? 'edoweb:NNNN' : @$conf['quellserverWebschnittPid'],
        	'#required' => TRUE,
    	);
   	if( $entity->bundle() == 'version') {
        	$form['actions']['importWS']['quellwebschnitt']['#attributes'] = array('readonly' => 'readonly');
    	}
	$form['actions']['importWS']['deleteQuellserverWebschnitt'] = array(
        	'#type' => 'checkbox',
        	'#title' => t('Lösche Webschnitt auf Quellserver'),
        	'#name' => 'deleteQuellserverWebschnitt',
        	'#default_value' => @$conf['deleteQuellserverWebschnitt'] == false ? 0 : 1,
    	);
    	if( $entity->bundle() == 'version') {
        	$form['actions']['importWS']['deleteQuellserverWebschnitt']['#attributes'] = array('disabled' => 'disabled');
    	}   
   	if( $entity->bundle() != 'version') {
    		$form['actions']['importWS']['doImportWS'] = array(
        		'#type' => 'submit',
        		'#value' => t('Importiere Webschnitt'),
        		'#submit' => array('edoweb_basic_admin_importws'),
    		);
    	}
    }

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
            'aleph' => t('Copy to Aleph-Katalog'),
            'alma' => t('Copy to Alma-Katalog'),
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
    $purge = "false";
    edoweb_basic_delete($entity, $purge, TRUE);
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
 * Form deletion handler.
 *
 */
function edoweb_basic_admin_delete_keep_webarchives( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $purge = "false";
    edoweb_basic_delete($entity, $purge, FALSE);
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

/**
 * Form Export Webschnitt
 *
 */
function edoweb_basic_admin_importws( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $quellwebpage = $form_state['values']['quellwebpage'];
    $quellwebschnitt = $form_state['values']['quellwebschnitt'];
    $deleteQuellserverWebschnitt = $form_state['values']['deleteQuellserverWebschnitt'];
    $api = new EdowebAPIClient();
    $api->importWS($entity, $quellwebpage, $quellwebschnitt, $deleteQuellserverWebschnitt);
}
