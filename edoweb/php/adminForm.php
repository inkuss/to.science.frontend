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
    $form['delete'] = array(
        '#type' => 'fieldset',
        '#title' => t('Delete'),
        '#weight' => 200,
    );
    $form['delete']['keepWebarchives'] = array(
        '#type' => 'checkbox',
        '#title' => t('behalte Webarchive'),
        '#name' => 'keepWebarchives',
        '#default_value' => TRUE,
    );
    $parents = field_get_items('edoweb_basic', $entity, 'field_edoweb_struct_parent');
    $parent_id = '';
    if (FALSE !== $parents) {
        foreach($parents as $parent) {
            $parent_id = $parent['value'];
        }
    }
    if (! $conf = $api->getCrawlerConfiguration($entity)) {
    	if (! $conf = $api->getCrawlerConfigurationById($parent_id)) {
        	/* falls keine Conf vorhanden noch versuchen, die Conf des Parent zu lesen (für kaputte Webschnitte) */
        	$form['delete']['keepWebarchives']['#attributes'] = array('disabled' => 'disabled');
        }
    }
   $form['delete']['purge'] = array(
     	'#type' => 'checkbox',
       	'#title' => t('endgültig löschen'),
       	'#name' => 'purge',
       	'#default_value' => FALSE,
    	);
    if( $entity->bundle() != 'version') {
        $form['delete']['purge']['#attributes'] = array('disabled' => 'disabled');
    }
    $form['delete']['doDelete'] = array(
        '#type' => 'submit',
        '#value' => t('Delete'),
        '#submit' => array('edoweb_basic_admin_delete'),
    );
    $form['delete']['reactivate'] = array(
        '#type' => 'submit',
        '#value' => t('Reaktivieren'),
        '#submit' => array('edoweb_basic_admin_reactivate'),
    );

    $toscience_import_server_name = variable_get('toscience_import_server_name');
    if ($toscience_import_server_name != '' && $conf) {
    	$form['importWS'] = array(
        	'#type' => 'fieldset',
        	'#title' => t('Import Webschnitt'),
        	'#weight' => 300,
    	);
    	$form['importWS']['quellserver'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver'),
        	'#name' => 'quellserver',
        	'#attributes' => array('disabled' => 'disabled'),
        	'#default_value' => $toscience_import_server_name
    	);
    	$form['importWS']['quellwebpage'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver Website PID'),
        	'#name' => 'quellwebpage',
        	'#default_value' => @$conf['quellserverWebpagePid'] == null ? 'edoweb:NNNN' : @$conf['quellserverWebpagePid'],
    	);
   	if( $entity->bundle() == 'version') {
        	$form['importWS']['quellwebpage']['#attributes'] = array('readonly' => 'readonly');
    	}
    	$form['importWS']['quellwebschnitt'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Quellserver Webschnitt PID'),
        	'#name' => 'quellwebschnitt',
        	'#default_value' => @$conf['quellserverWebschnittPid'] == null ? 'edoweb:NNNN' : @$conf['quellserverWebschnittPid'],
        	'#required' => TRUE,
    	);
   	if( $entity->bundle() == 'version') {
        	$form['importWS']['quellwebschnitt']['#attributes'] = array('readonly' => 'readonly');
    	}
	$form['importWS']['deleteQuellserverWebschnitt'] = array(
        	'#type' => 'checkbox',
        	'#title' => t('Lösche Webschnitt auf Quellserver'),
        	'#name' => 'deleteQuellserverWebschnitt',
        	'#default_value' => @$conf['deleteQuellserverWebschnitt'] == false ? 0 : 1,
    	);
    	if( $entity->bundle() == 'version') {
        	$form['importWS']['deleteQuellserverWebschnitt']['#attributes'] = array('disabled' => 'disabled');
    	}   
   	if( $entity->bundle() != 'version') {
    		$form['importWS']['doImportWS'] = array(
        		'#type' => 'submit',
        		'#value' => t('Importiere Webschnitt'),
        		'#submit' => array('edoweb_basic_admin_importws'),
    		);
    	}
    }
    if ($conf) {
    	$form['postVersion'] = array(
        	'#type' => 'fieldset',
        	'#title' => t('Erzeuge Webschnitt für Zeitstempel'),
        	'#weight' => 400,
    	);
    	$form['postVersion']['crawler'] = array(
        	'#type' => 'textfield',
        	'#title' => t('Crawler'),
        	'#name' => 'crawler',
        	'#default_value' => @$conf['crawlerSelection'],
    	);
	$title_html = $api->getTitle($entity->remote_id); // aus File Label oder Titel holen
	$title_html = preg_replace('/\n/','', $title_html); // remove line breaks
        $title_text = preg_replace('/^.*<span class="titleMain">(.*?)<\/span>.*$/','$1', $title_html); // convert html to text
        $default_timestamp = preg_replace('/[^0-9]/', '', $title_text); //  convert title to timestamp
    	$form['postVersion']['zeitstempel'] = array(
        	'#type' => 'textfield',
		'#title' => t('Zeitstempel im Format yyyyMMddHHmmss. <br/> Achtung! Es muss ein Webarchiv mit diesem Zeitstempel existieren!'),
        	'#name' => 'zeitstempel',
                '#default_value' => $default_timestamp,
    	);
    	$form['postVersion']['doPostVersion'] = array(
        	'#type' => 'submit',
        	'#value' => t('Erzeuge Webschnitt'),
        	'#submit' => array('edoweb_basic_admin_post_version'),
    	);
    }

    $form['transformers'] = array(
        '#type' => 'fieldset',
        '#title' => t('Transformers'),
        '#weight' => 500,
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
    $keepWebarchives = $form_state['values']['keepWebarchives'] ? $form_state['values']['keepWebarchives'] : 0;
    $purge = $form_state['values']['purge'];
    edoweb_basic_delete($entity, $keepWebarchives, $purge);
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
 * Form reactivation handler.
 *
 */
function edoweb_basic_admin_reactivate( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $api = new EdowebAPIClient();
    $api->activate($entity);
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

/**
 * Form Export Webschnitt
 *
 */
function edoweb_basic_admin_post_version( $form , &$form_state ) {
    $entity = $form_state['values']['basic_entity'];
    $parents = field_get_items('edoweb_basic', $entity, 'field_edoweb_struct_parent');
    $parent_id = '';
    if (FALSE !== $parents) {
        foreach($parents as $parent) {
            $parent_id = $parent['value'];
        }
    }
    $api = new EdowebAPIClient();
    if (! $conf = $api->getCrawlerConfiguration($entity)) {
       	/* falls keine Conf vorhanden noch versuchen, die Conf des Parent zu lesen (für kaputte Webschnitte) */
    	$conf = $api->getCrawlerConfigurationById($parent_id);
    }
    $webpage_pid = $conf['name'];
    $version_pid = '';
    $crawler = $form_state['values']['crawler'];
    $zeitstempel = $form_state['values']['zeitstempel'];
    $filename = '';
    $api->postVersion($entity, $webpage_pid, $version_pid, $crawler, $zeitstempel, $filename);
}
