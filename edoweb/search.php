<?php
function edoweb_basic_search_entities_form($form, &$form_state, $advanced, $search_count, $query, $fulltext_option = false) {
    
    $form['#method'] = 'get';
    
    $form['query'] = array(
        '#tree' => TRUE,
    );
    
    $url_params = explode('&', $_SERVER['QUERY_STRING']);
    foreach ($url_params as $param) {
        if (empty($param)) continue;
        list($key, $value) = explode('=', $param);
        if ("query[$search_count][term]" == urldecode($key)
            || 'page' == $key
            || 'op' == $key
            || "query[$search_count][type]" == substr(urldecode($key), 0, strlen("query[$search_count][type]"))
            || "query[$search_count][user]" == urldecode($key)
            || "query[$search_count][fulltext]" == urldecode($key)
            || "query[$search_count][childlevel]" == urldecode($key)
            ) continue;
            $form['query']['url_params'][] = array(
                '#type' => 'hidden',
                '#value' => urldecode($value),
                '#name' => urldecode($key),
            );
    }
    
    $form['query'][$search_count]['term'] = array(
        '#type' => 'textfield',
        '#default_value' => isset($query['term']) ? urldecode($query['term']) : '',
        '#attributes' => array(
            'title' => t('Enter your search term here. You may use quotes to search for phrases as in "The quick brown fox", and also boolean operators as in This AND that OR those.'),
        ),
    );
    
    $form['query'][$search_count]['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Search'),
    );
    
    if (isset($query['target_bundles']) && isset($query['endpoint'])) {
        $options = array();
        $bundle_infos = field_info_bundles(EDOWEB_ENTITY_TYPE);
        foreach ($query['target_bundles'] as $target_bundle) {
            $bundle_info = $bundle_infos[$target_bundle];
            $options[$target_bundle] = $bundle_info['label'];
        }
        $form['query'][$search_count]['type'] = array(
            '#type' => 'radios',
            '#options' => $options,
            '#default_value' => isset($query['type']) && $query['type'] != ''
            ? $query['type']
            : current(array_keys($options)),
        );
    } else if (isset($query['endpoint']) && 'resource' == $query['endpoint']) {
        $options = array(
            'generic' => t('Any'),
            'researchData'=>t('Forschungsdaten'),
            'proceeding'=>t('Konferenzbeitrag'),
            'monograph' => t('Monograph'),
            'journal' => t('Journal'),
            'webpage' => t('Webpage'),
            'article'=>t('Artikel'),
        );
        $form['query'][$search_count]['type'] = array(
            '#type' => 'radios',
            '#options' => $options,
            '#default_value' => isset($query['type']) && $query['type'] != ''
            ? $query['type']
            : current(array_keys($options)),
        );
    }
    
    if ($fulltext_option) {
        $form['query'][$search_count]['fulltext'] = array(
            '#type' => 'checkbox',
            '#title' => t('Volltexte'),
            '#default_value' => isset($query['fulltext']),
        );
        if (!user_is_anonymous()) {
            $form['query'][$search_count]['childlevel'] = array(
                '#type' => 'checkbox',
                '#title' => t('Kindobjekte'),
                '#default_value' => isset($query['childlevel']),
            );
        }
    }
    
    
    return $form;
}

function edoweb_basic_search_entities(
    EntityFieldQuery $efq, $advanced = FALSE, $operations = array(),
    $list_noterm = TRUE, $add_links = FALSE, $sortable = TRUE,
    $view_mode = 'default', $fulltext_option = false
    ) {
        static $search_count = 0;
        if (array_key_exists('query', $_GET)
            && !empty($_GET['query'])
            && array_key_exists($search_count, $_GET['query'])
            ) {
                $query = $_GET['query'][$search_count];
            } else {
                $query = array();
            }
            
            if (isset($efq->metaData['endpoint'])) {
                $query['endpoint'] = $efq->metaData['endpoint'];
            }
            
            if (isset($efq->entityConditions['bundle'])) {
                $query['target_bundles'] = $efq->entityConditions['bundle']['value'];
            }
            
            $content = array();
            $content['search'] = drupal_get_form(
                'edoweb_basic_search_entities_form', $advanced, $search_count, $query, $fulltext_option
                );
            
            unset($content['search']['form_build_id']);
            unset($content['search']['form_id']);
            unset($content['search']['form_token']);
            
            $parent_entity = null;
            foreach ($efq->fieldConditions as $field_condition) {
                if ($field_condition['field']['field_name'] == 'field_edoweb_struct_parent') {
                    $parent_entity = $field_condition['value'];
                }
            }
            
            if (isset($efq->entityConditions['bundle']) && $add_links) {
                $target_bundles = $efq->entityConditions['bundle']['value'];
                $links = '';
                foreach($target_bundles as $target_bundle) {
                    $url = is_null($parent_entity)
                    ? "resource/add/{$target_bundle}"
                    : "resource/$parent_entity/children/add/$target_bundle";
                    $links .= l(
                        _edoweb_map_string("Add {$target_bundle}"), $url,
                        array('attributes' => array('data-bundle' => $target_bundle))
                        );
                }
                $content['add'] = array(
                    '#type' => 'item',
                    '#markup' => $links,
                );
            }
            
            if (@$users = $query['users']) {
                $target_users = array_keys($users);
                $efq->propertyCondition('uid', $target_users);
            }
            
            if (@$type = $query['type']) {
                if (!is_array($type)) {
                    $type = array($type);
                } else {
                    $type = array_keys($type);
                }
                $efq->addMetaData('type', $type);
            }
            
            if (@$term = $query['term']) {
                $efq->addMetaData('term', $term);
            }
            
            if (! (@$childlevel = $query['childlevel']) && ! (@$fulltext = $query['fulltext']) ) {
                //FIXME: this overwrites person etc bundles!!!
                $efq->entityCondition('bundle', array('monograph', 'journal', 'webpage','proceeding','researchData','article'));
            }
            
            if (@$fulltext = $query['fulltext']) {
                $efq->addTag('fulltext');
            }
            
            if ($term || $list_noterm) {
                $content['results'] = edoweb_basic_list_entities($efq, $operations, $search_count, @$query['facets'], $sortable, $view_mode);
            }
            
            $search_count++;
            return $content;
}


function edoweb_basic_browse($jsonld_property) {
    
    $api = new EdowebApiClient();
    $query = new EntityFieldQuery();
    $query->addTag('elasticsearch');
    $query->addMetaData('facet_size', 999);
    $result = $query->execute();
    
    if (!property_exists($result['facets'], $jsonld_property)) {
        return drupal_not_found();
    }
    
    $is_ref = in_array($jsonld_property, array('creator', 'subject', 'institution', 'rdftype', 'medium'));
    $is_user = ('createdBy' == $jsonld_property
        || 'lastModifiedBy' == $jsonld_property);
    $items = array();
    foreach ($result['facets']->$jsonld_property->terms as $term) {
        $facet_entity_uri = $term->term;
        $facet_entity_count = $term->count;
        if ($is_ref) {
            $facet_object = es_facet_object($facet_entity_uri);
            $title = $facet_entity_uri;//$facet_object['prefLabel'];
            $facet_entity_uri = $facet_entity_uri;//$facet_object['@id'];
        } else if ($is_user) {
            $title = _edoweb_user_name($facet_entity_uri);
        } else {
            $title = _edoweb_map_string("$facet_entity_uri");
        }
        $params = array();
        $params['query'][0]['facets'][$jsonld_property][] = $facet_entity_uri;
        $apply_facet_link = l(
            $title, '/resource/',
            array(
                'query' => $params,
                'attributes' => array(),
            )
            ) . " <span>($facet_entity_count)</span>";
            $items[] = array(
                'data' => $apply_facet_link,
            );
    }
    
    return theme_item_list(array(
        'items' => $items,
        'title' => _edoweb_map_string($jsonld_property),
        'type' => 'ul',
        'attributes' => array('class' => array('listnav')),
    ));
    
}