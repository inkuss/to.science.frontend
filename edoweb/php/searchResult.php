<?php


/*
 * Returns table header
 */
function edoweb_basic_table_header($bundle_type = 'generic', $init_sort = false) {
    
    $columns = array();
    if (user_access('edit any edoweb_basic entity')) {
        $entity_table_headers = variable_get('editor_entity_table_headers', _edoweb_entity_table_headers_defaults());
        $authority_table_headers = variable_get('editor_authority_table_headers', _edoweb_authority_table_headers_defaults());
    } else {
        $entity_table_headers = variable_get('user_entity_table_headers', _edoweb_entity_table_headers_defaults());
        $authority_table_headers = variable_get('user_authority_table_headers', _edoweb_authority_table_headers_defaults());
    }
    
    switch ($bundle_type) {
        case 'monograph':
        case 'journal':
        case 'volume':
        case 'issue':
        case 'article':
        case 'file':
        case 'collection':
        case 'webpage':
        case 'version':
        case 'generic':
        case 'proceeding':
        case 'researchData':
        case 'part':
            foreach (_edoweb_entity_table_headers($init_sort) as $field => $column) {
                if (isset($entity_table_headers[$field]) && $entity_table_headers[$field]) {
                    $columns[] = $column;
                }
            }
            break;
        case 'person':
        case 'subject':
        case 'corporate_body':
        case 'event':
        case 'work':
        case 'place':
        case 'authority_resource':
            foreach (_edoweb_authority_table_headers() as $field => $column) {
                if ($authority_table_headers[$field]) {
                    $columns[] = $column;
                }
            }
            break;
        default:
            $columns = array(
            array(
            'data' => 'Label',
            'type' => 'field',
            'specifier' => array(
            'field' => 'field_edoweb_label',
            'column' => 'value',
            ),
            ),
            );
            
    }
    
    // Add fixed columns, independant of bundle type
    array_push($columns, t('Operations'));
    
    return $columns;
    
}

/*
 * Returns a list of entities themed as a table.
 */
function edoweb_basic_entity_table($header, $entities, $operations = array(), $total = null, $view_mode = 'default') {
    
    $content = array(
        '#type' => 'fieldset',
        '#title' => t('Your search returned @total results', array('@total' => $total)),
        '#attributes' => array(
            'class' => array('edoweb-entity-list')
        ),
    );
    
    $rows = array();
    $columns = $header;
    
    foreach ($entities as $i => $entity) {
        
        // Render embedded operations form
        $operation_elements = '';
        foreach ($operations as $operation) {
            $operation_form = drupal_get_form(
                "{$operation}_{$i}", $entity->remote_id
            );
            $operation_elements .= drupal_render($operation_form);
        }
        
        $wrapper = entity_metadata_wrapper('edoweb_basic', $entity);
        $curie = _edoweb_compact_uri($wrapper->remote_id->value());
        $row = array(
            'data-curie' => $curie,
            'data-updated' => $entity->objectTimestamp
        );
        
        foreach ($columns as $column) {
            $property = null;
            $value = null;
            $is_ref = false;
            $list_items = array();
            if (!isset($column['type'])) {
                $property = null;
            } else if ('field' == $column['type']) {
                $property = $column['specifier']['field'];
                $field_info = field_info_field($property);
                $is_ref = ($field_info['type'] == 'edoweb_ld_reference');
            } else if ('property' == $column['type']) {
                $property = $column['specifier'];
            }
            if (null === $property) {
                if (isset($column['format']) && function_exists($column['format'])
                    && $list_item = $column['format']($entity)) {
                        $list_items[] = $list_item;
                    }
            } else if (!(($property == 'access_md' || $property == 'access_data')
                && ! _is_edoweb_entity($entity))) {
                    try {
                        $values = $wrapper->$property->value();
                        if ($values and !is_array($values)) $values = array($values);
                        if ($values) foreach ($values as $value) {
                            if ('field' == $column['type']) {
                                // Fixme: workaround for Drupal bug
                                // https://www.drupal.org/node/1824820
                                if ($is_ref) {
                                    $field_value = field_view_value(
                                        EDOWEB_ENTITY_TYPE, $entity, $property, array('value' => $value['value'])
                                        );
                                } else {
                                    $field_value = field_view_value(
                                        EDOWEB_ENTITY_TYPE, $entity, $property, array('value' => $value)
                                        );
                                }
                                if (isset($column['format'])) {
                                    $list_items[] = $column['format'](drupal_render($field_value), $entity);
                                } else {
                                    $list_items[] = drupal_render($field_value);
                                }
                            } else if (isset($column['format'])) {
                                $list_items[] = $column['format']($value, $entity);
                            } else {
                                $list_items[] = $value;
                            }
                        }
                    } catch (EntityMetadataWrapperException $e) {
                        // No value for field, ignore
                        //var_dump($e);
                    }
                }
                $row['data'][] = theme_item_list(array(
                    'items' => $list_items,
                    'title' => null,
                    'type' => 'ul',
                    'attributes' => array(),
                ));
        }
        
        /*
         $row['data'][] = sprintf(
         '<span class="entity-label-%s">%s</span>',
         $entity->bundle_type,
         edoweb_basic_bundle_name($entity->bundle_type)
         );
         $row['data'][] = $operation_elements;
         */
        $rows[] = $row;
    }
    
    $content['pager_above'] = array(
        '#theme' => 'pager',
        '#weight' => 8,
    );
    // Put our entities into a themed table. See theme_table() for details.
    $content['entity_table'] = array(
        '#theme' => 'table',
        '#rows' => $rows,
        '#header' => $header,
        '#weight' => 9,
    );
    $content['pager_below'] = array(
        '#theme' => 'pager',
        '#weight' => 10,
    );
    return $content;
}

/**
 * Returns a render array with all edoweb_basic entities.
 *
 */
function edoweb_basic_list_entities(EntityFieldQuery $efq, $operations = array(), $search_count = 0, $active_facets = null, $sortable = TRUE, $view_mode = 'default') {
    global $user;
    // Prepare query: header for table sort
    // TODO: decide which table header to use if multiple target bundles
    // are set.
    $target_bundle = isset($efq->entityConditions['bundle']['value'])
    ? current($efq->entityConditions['bundle']['value'])
    : null;
    $header = edoweb_basic_table_header($target_bundle, !isset($efq->metaData['term']));
    if ($sortable) {
        $efq->tableSort($header);
    }
    
    $content = array();
    
    // Prepare query: apply active facets
    $content['filters'] = array(
        '#type' => 'fieldset',
        '#title' => t('Facets'),
        '#collapsed' => FALSE,
        '#collapsible' => TRUE,
        '#attributes' => array(
            'class' => array('edoweb-facets')
        ),
    );
    
    if ($active_facets) {
        foreach (array_keys($active_facets) as $jsonld_property) {
            $content['filters'][$jsonld_property] = array(
                '#theme' => 'item_list',
                '#attributes' => array(
                    'class' => array('edoweb-facets-active'),
                ),
                '#type' => 'ul',
                '#weight' => -200,
            );
        }
        foreach ($active_facets as $jsonld_property => $facet) {
            $is_ref = in_array($jsonld_property, array('creator', 'subject'));
            $is_user = ('createdBy' == $jsonld_property
                || 'lastModifiedBy' == $jsonld_property);
            foreach ($facet as $key => $value) {
                // Add facet to query
                if ('contentType' == $jsonld_property) {
                    $efq->entityCondition('bundle', array($value));
                } else if ('lastModifiedBy' == $jsonld_property) {
                    $efq->propertyCondition('mid', $value);
                } else if ('createdBy' == $jsonld_property) {
                    $efq->propertyCondition('uid', $value);
                } else {
                    $field_name = _jsonld_key_to_field_name($jsonld_property);
                    $efq->fieldCondition(
                        $field_name, 'value', $value
                        );
                }
            }
        }
    }
    
    // Execute query
    $result = $efq->execute();
    
    if (!$result or !array_key_exists('edoweb_basic', $result)) {
        $entities = array();
    } else {
        $entities = $result['edoweb_basic'];
    }
    
    // Available facets
    if (isset($result['facets'])) {
        // Add list of available facets
        //drupal_set_message(var_dump($jsonld_property));
        foreach ($result['facets'] as $jsonld_property => $facet) {
            $content['filters']['facets'][$jsonld_property] = array(
                '#theme' => 'item_list',
                '#type' => 'ul',
                '#attributes' => array(
                    'class' => array('edoweb-facets-available'),
                ),
                '#title' => l(_edoweb_map_string($jsonld_property), 'browse/' . $jsonld_property),
            );
            $is_ref = in_array($jsonld_property, array('creator', 'subject', 'institution', 'medium', 'rdftype'));
            $is_user = ('createdBy' == $jsonld_property
                || 'lastModifiedBy' == $jsonld_property);
            foreach ($facet->terms as $term) {
                $facet_entity_uri = $term->term;
                $facet_entity_count = $term->count;
                if ($is_ref) {
                    //$facet_object = es_facet_object($facet_entity_uri);
                    $title = $facet_entity_uri;//$facet_object['prefLabel'];
                    $facet_entity_uri = $facet_entity_uri;//$facet_object['@id'];
                } else if ($is_user) {
                    $title = _edoweb_user_name($facet_entity_uri);
                } else {
                    $title = _edoweb_map_string("$facet_entity_uri");
                }
                // Check if facet is active
                if ($active_facets && array_key_exists($jsonld_property, $active_facets)
                    && in_array($facet_entity_uri, $active_facets[$jsonld_property])) {
                        // Link to remove facet
                        $params = drupal_get_query_parameters();
                        unset($params['query'][$search_count]['facets'][$jsonld_property][$key]);
                        if (isset($params['page'])) {
                            unset($params['page']);
                        }
                        $remove_facet_link = $title . l(t(' [x]'), current_path(), array('query' => $params));
                        $content['filters'][$jsonld_property]['#items'][] = $remove_facet_link;
                    } else if (!$is_user || ($is_user && $user->uid == $facet_entity_uri)
                        || in_array('edoweb_backend_admin', $user->roles)) {
                            $params = drupal_get_query_parameters();
                            if (isset($params['page'])) {
                                unset($params['page']);
                            }
                            $params['query'][$search_count]['facets'][$jsonld_property][] = $facet_entity_uri;
                            $apply_facet_link = l(
                                $title, current_path(),
                                array(
                                    'query' => $params,
                                    'attributes' => array(),
                                )
                                ) . " <span>($facet_entity_count)</span>";
                                $content['filters']['facets'][$jsonld_property]['#items'][] = $apply_facet_link;
                        }
            }
        }
    }
    
    if (!$active_facets && !isset($result['facets'])) {
        unset($content['filters']);
    }
    
    
    if (!empty($entities)) {
        $content['entity_list'] = edoweb_basic_entity_table($header, $entities, $operations, $result['hits'], $view_mode);
    } else {
        // There were no entities. Tell the user.
        $content[] = array(
            '#type' => 'item',
            '#markup' => t('No results.'),
        );
    }
    return $content;
}


function _edoweb_search($bundle_name = null, $field_name = null) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'edoweb_basic');
    $endpoint = null;
    $parameter = null;
    
    if ($bundle_name && $field_name) {
        $instance_definition = field_info_instance(
            EDOWEB_ENTITY_TYPE, $field_name, $bundle_name
            );
        $field_definition = field_info_field($field_name);
        
        $endpoint = isset($field_definition['settings']['endpoint'])
        ? $field_definition['settings']['endpoint']
        : false;
        $parameter = isset($field_definition['settings']['parameter'])
        ? $field_definition['settings']['parameter']
        : false;
        
        $target_bundles = isset($instance_definition['settings']['handler_settings']['target_bundles'])
        ? array_values($instance_definition['settings']['handler_settings']['target_bundles'])
        : array_values($field_definition['settings']['handler_settings']['target_bundles']);
        
        $query->entityCondition('bundle', $target_bundles);
    } else if ($bundle_name) {
        $endpoint = 'resource';
        $parameter = 'name';
        $query->entityCondition('bundle', array($bundle_name));
    } else if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];
        $parameter = 'name';
    }
    
    if ($endpoint && $parameter) {
        $query->addTag('lobid');
        $query->addMetaData('endpoint', $endpoint);
        $query->addMetaData('parameter', $parameter);
        $sortable = FALSE;
    } else {
        $query->addTag('elasticsearch');
        $sortable = TRUE;
    }
    
    $content = edoweb_basic_search_entities($query, FALSE, array(), FALSE, TRUE, $sortable);
    
    die(drupal_render($content));
    
}


function _edoweb_lookup($bundle_name, $field_name, $term, $page = null) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'edoweb_basic');
    $query->addMetaData('term', $term);
    
    // Overwrite page query parameter
    if (!is_null($page)) {
        $_GET['page'] = $page;
    }
    
    $instance_definition = field_info_instance(
        EDOWEB_ENTITY_TYPE, $field_name, $bundle_name
        );
    $field_definition = field_info_field($field_name);
    
    $endpoint = isset($field_definition['settings']['endpoint'])
    ? $field_definition['settings']['endpoint']
    : false;
    $parameter = isset($field_definition['settings']['parameter'])
    ? $field_definition['settings']['parameter']
    : false;
    
    if ($endpoint && $parameter) {
        $query->addTag('lobid');
        $query->addMetaData('endpoint', $endpoint);
        $query->addMetaData('parameter', $parameter);
    } else {
        $query->addTag('elasticsearch');
    }
    
    $target_bundles = isset($instance_definition['settings']['handler_settings']['target_bundles'])
    ? array_values($instance_definition['settings']['handler_settings']['target_bundles'])
    : array_values($field_definition['settings']['handler_settings']['target_bundles']);
    
    $query->entityCondition('bundle', $target_bundles);
    
    $result = $query->execute();
    if (!$result or !array_key_exists('edoweb_basic', $result)) {
        $entities = array();
    } else {
        $entities = $result['edoweb_basic'];
    }
    return $entities;
}



   
