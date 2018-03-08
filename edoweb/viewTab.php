<?php


/**
 * Menu callback to display an entity.
 *
 * As we load the entity for display, we're responsible for invoking a number
 * of hooks in their proper order.
 *
 * @see hook_entity_prepare_view()
 * @see hook_entity_view()
 * @see hook_entity_view_alter()
 */
function edoweb_basic_view($entity, $view_mode = 'default') {
    global $base_url;
    $page_url = $base_url . '/' . current_path();
    $entity_type = 'edoweb_basic';
    //edoweb_update_7181();
    $wrapper = entity_metadata_wrapper('edoweb_basic', $entity);
    
    $attr_bundle = 'data-entity-bundle="' .  $entity->bundle_type . '"';
    $attr_rdf_typeof = 'typeof="' . implode(' ', edoweb_rdf_types($entity->bundle_type)) . '"';
    $attr_rdf_resource = isset($entity->remote_id)
    ? 'resource="' . $entity->remote_id . '"'
        : 'resource="[_:foo]"';
        
        // Start setting up the content.
        if (!('compact' == $view_mode)) {
            $entity->content = array(
                '#view_mode' => $view_mode,
                '#prefix' => "<div class=\"edoweb entity $view_mode\" $attr_bundle $attr_rdf_resource $attr_rdf_typeof>",
                '#suffix' => "<span about=\"$page_url\" rel=\"foaf:primaryTopic\" $attr_rdf_resource /></div>",
            );
            
            
            if (isset($entity->remote_id)) {
                
                $api = new EdowebAPIClient();
                $htmlString=$api->getView($entity,"frl");
                $userInfo="";$viewSource="";$mabSource="";
                if (user_access('edit any edoweb_basic entity')) {
                    
                    $userInfo = '<small>Letzte Änderung ';
                    $userInfo .= isset($entity->updated) ? _edoweb_format_date($entity->updated) : '';
                    $userInfo .= '. Erstellt: ';
                    $userInfo .= isset($entity->created) ? _edoweb_format_date($entity->created) : '';
                    $userInfo .= ' durch ' .  _edoweb_user_name($entity->uid) . '</small>';
                    
                }
                
                $viewSource = '<p/><small>' . l(
                    'View source',
                    _edoweb_expand_curie($entity->remote_id),
                    array('attributes' => array(
                        'target'=>'_blank',
                        'class' => array('entity-id'),
                    ))
                    ) . '</small>';
                    $mabSource="";
                    if (! empty($origin)) {
                        $mabSource	='<p/><small>' . l(
                            'View MAB source',
                            $origin[0]['value'] . '?format=source',
                            array('attributes' => array(
                                'target'=>'_blank',
                                'class' => array('entity-id'),
                            ))
                            ) . '</small>';
                    }
                    $entity->content = array(
                        '#view_mode' => $view_mode,
                        '#prefix' => "<div class=\"edoweb entity $view_mode\" $attr_bundle $attr_rdf_resource $attr_rdf_typeof>",
                        '#markup' =>  $htmlString . $userInfo . $viewSource . $mabSource,
                        '#suffix' => "<span about=\"$page_url\" rel=\"foaf:primaryTopic\" $attr_rdf_resource /></div>",
                    );
            }
            
            
        }
        else {
            $entity->content = array(
                '#prefix' => "<div class=\"edoweb entity $view_mode\" $attr_bundle $attr_rdf_resource $attr_rdf_typeof>". '<div class="compact-header">' . _full_title($entity->label(), $entity) . '</div>',
                '#suffix' => "</div>",
            );
        }
        if (isset($entity->remote_id)) {
            // OK, Field API done, now we can set up some of our own data.
            if ('default' == $view_mode) {
                
                if ('file' == $entity->bundle() && ($thumby = variable_get('edoweb_thumby_url'))) {
                    $url = $entity->url() . '/data';
                    $entity->content['thumbnail'] = array(
                        '#markup' => "<div class=\"thumb\"><a target=\"_blank\" href=\"$url\"><img src=\"$thumby?url=$url&size=250\" /></a></div>",
                        '#weight' => -1000
                    );
                }
                
                // Find fields linking to this entity
                $inverse_query = new EntityFieldQuery();
                $inverse_query->addTag('elasticsearch');
                $inverse_query->entityCondition(
                    'entity_type', 'edoweb_basic'
                    );
                $fields_info = field_info_fields();
                $has_inverse = FALSE;
                foreach ($fields_info as $field_name => $field_info) {
                    if (isset($field_info['settings']['inverse'])
                        && in_array($entity->bundle_type, $field_info['settings']['inverse']['bundles'])) {
                            $has_inverse = TRUE;
                            $inverse_query->fieldCondition(
                                $field_name, 'value', $entity->identifier(), null, 'or'
                                );
                        }
                }
                if ($has_inverse) {
                    $inverse_query->entityCondition('bundle', array(
                        'monograph', 'journal', 'volume', 'issue', 'article', 'file', 'part','proceeding','researchData'
                    ));
                    $entity->content['related'] = edoweb_basic_search_entities(
                        $inverse_query
                        );
                    $entity->content['related'] += array(
                        '#weight' => 900,
                        '#type' => 'fieldset',
                        '#title' => 'Related resources',
                    );
                }
                
                if (user_access('edit any edoweb_basic entity')) {
                    $markup = '<small>Letzte Änderung ';
                    $markup .= isset($entity->updated) ? _edoweb_format_date($entity->updated) : '';
                    $markup .= '. Erstellt: ';
                    $markup .= isset($entity->created) ? _edoweb_format_date($entity->created) : '';
                    $markup .= ' durch ' .  _edoweb_user_name($entity->uid) . '</small>';
                    $entity->content['_updated'] = array(
                        '#type' => 'item',
                        '#markup' => $markup,
                        '#weight' => 1000,
                    );
                }
                
                $entity->content['remote_id'] = array(
                    '#type' => 'item',
                    '#markup' => '<small>' . l(
                        'View source',
                        _edoweb_expand_curie($entity->remote_id),
                        array('attributes' => array(
                            'target'=>'_blank',
                            'class' => array('entity-id'),
                        ))
                        ) . '</small>',
                    '#weight' => 1001,
                );
                
                try {
                    $archived_url = $wrapper->field_edoweb_webpage_archived->value();
                    if (! empty($archived_url)) {
                        $entity->content['archived_url'] = array(
                            '#type' => 'item',
                            '#markup' => '<small>' . l(
                                'View in Internet Archive',
                                "http://web.archive.org/web/*/{$archived_url}",
                                array('attributes' => array(
                                    'target'=>'_blank',
                                ))
                                ) . '</small>',
                                '#weight' => 1003,
                                );
                    }
                } catch (EntityMetadataWrapperException $e) {
                    // Field not present, ignore
                }
                
                try {
                    $origin = $wrapper->field_edoweb_parallel->value();
                    if (! empty($origin)) {
                        $entity->content['mab_source'] = array(
                            '#type' => 'item',
                            '#markup' => '<small>' . l(
                                'View MAB source',
                                $origin[0]['value'] . '?format=source',
                                array('attributes' => array(
                                    'target'=>'_blank',
                                    'class' => array('entity-id'),
                                ))
                                ) . '</small>',
                            '#weight' => 1004,
                        );
                    }
                } catch (EntityMetadataWrapperException $e) {
                    // Field not present, ignore
                }
                
            }
        }
        
        if (1 == variable_get('edoweb_api_debug')) {
            $entity->content['turtle'] = array(
                '#type' => 'item',
                '#title' => t('Turtle view'),
                '#markup' => sprintf('<pre>%s</pre>', htmlentities(_edoweb_storage_entity_serialize_turtle($entity))),
                '#weight' => 100,
            );
            
            $entity->content['json'] = array(
                '#type' => 'item',
                '#title' => t('JSON-LD view'),
                '#markup' => sprintf('<pre>%s</pre>', htmlentities(_edoweb_storage_entity_serialize_jsonld($entity))),
                '#weight' => 100,
            );
        }
        
        // Now to invoke some hooks. We need the language code for
        // hook_entity_view(), so let's get that.
        global $language ;
        $langcode = $language->language ;
        // And now invoke hook_entity_view().
        module_invoke_all('entity_view', $entity, $entity_type, $view_mode, $langcode);
        // Now invoke hook_entity_view_alter().
        drupal_alter(array('edoweb_basic_view', 'entity_view'), $entity->content, $entity_type);
        
        if (!('compact' == $view_mode)) {
            _edoweb_build_breadcrumb($entity);
        }
        
        // Add custom HTTP header for AJAX requests
        if (isset($entity->remote_id)) {
            drupal_add_http_header('X-Edoweb-Entity', $entity->remote_id);
        }
        
        // And finally return the content.
        return $entity->content;
}