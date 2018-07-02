<?php

/**
 * Repository settings form
 */
function edoweb_repository_configuration_form() {
    
    $access_options = array(
        'public' => t('Public'),
        'private' => t('Private'),
    );
    
    $form = array();
    
    $form['urn_namespace'] = array(
        '#type' => 'textfield',
        '#title' => t('URN Namensraum'),
        '#default_value' => variable_get('urn_namespace'),
    );
    
    $form['access_md_default'] = array(
        '#type' => 'radios',
        '#title' => t('Standardsichtbarkeit von Metadaten'),
        '#options' => $access_options,
        '#default_value' => variable_get('access_md_default', 'public'),
    );
    
    $form['access_data_default'] = array(
        '#type' => 'radios',
        '#title' => t('Standardsichtbarkeit von Dateien'),
        '#options' => $access_options,
        '#default_value' => variable_get('access_data_default', 'private'),
    );
    
    $form['sub_title_config'] = array(
        '#type' => 'fieldset',
        '#title' => t('Untertitel'),
    );
    
    $form['sub_title_config']['sub_title_fields'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Felder'),
        '#options' => array(
            'field_edoweb_title_of_sub_series' => t('Unterreihe'),
            'field_edoweb_creator' => t('Autor'),
            'field_edoweb_contributor' => t('Bearbeiter'),
            'field_edoweb_issued' => t('Erschienen'),
        ),
        '#default_value' => variable_get('sub_title_fields', array('field_edoweb_creator')),
    );
    
    $form['sub_title_config']['sub_title_field_separator'] = array(
        '#type' => 'textfield',
        '#title' => t('Trennzeichen zwischen Feldern'),
        '#default_value' => variable_get('sub_title_field_separator', '.'),
    );
    
    $form['sub_title_config']['sub_title_field_value_separator'] = array(
        '#type' => 'textfield',
        '#title' => t('Trennzeichen zwischen einzelnen Felderwerten'),
        '#default_value' => variable_get('sub_title_field_value_separator', '|'),
    );
    
    $form['available_facets'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Verfügbare Facetten'),
        '#options' => array(
            'subject.@id' => t('Sacherschließung'),
            'rpbSubject.@id' => t('RPB Erchließung'),
            'contentType' => t('Objektart'),
            'rdftype.@id' => t('Typ'),
            'medium.@id' => t('Medium'),
            'issued' => t('Erscheinungsjahr'),
            'creator.@id' => t('Autor'),
            'institution.@id' => t('Institution'),
        ),
        '#default_value' => variable_get('available_facets', array('creator.@id')),
    );
    
    $entity_table_headers = array();
    foreach (_edoweb_entity_table_headers() as $field => $column) {
        $entity_table_headers[$field] = $column['data'];
    }
    
    $form['editor_entity_table_headers'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Dokument-Tabellenheader für Bearbeiter'),
        '#options' => $entity_table_headers,
        '#default_value' => variable_get('editor_entity_table_headers', _edoweb_entity_table_headers_defaults()),
    );
    
    $form['user_entity_table_headers'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Dokument-Tabellenheader für Endnutzer'),
        '#options' => $entity_table_headers,
        '#default_value' => variable_get('user_entity_table_headers',  _edoweb_entity_table_headers_defaults()),
    );
    
    $authority_table_headers = array();
    foreach (_edoweb_authority_table_headers() as $field => $column) {
        $authority_table_headers[$field] = $column['data'];
    }
    
    $form['editor_authority_table_headers'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Normdaten-Tabellenheader für Bearbeiter'),
        '#options' => $authority_table_headers,
        '#default_value' => variable_get('editor_authority_table_headers',  _edoweb_authority_table_headers_defaults()),
    );
    
    $form['user_authority_table_headers'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Normdaten-Tabellenheader für Endnutzer'),
        '#options' => $authority_table_headers,
        '#default_value' => variable_get('user_authority_table_headers', _edoweb_authority_table_headers_defaults()),
    );
    
    return system_settings_form($form);
    
}
