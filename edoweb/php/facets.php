<?php

function edoweb_restricted_browse($inst_name, $jsonld_property = 'institution') {
    
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
        
        if( $term->term == $inst_name){
            
            //drupal_set_message(var_dump($result['facets']->$jsonld_property));
            
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
                
        };
    }
    
    return theme_item_list(array(
        'items' => $items,
        'title' => _edoweb_map_string($jsonld_property),
        'type' => 'ul',
        'attributes' => array('class' => array('listnav')),
    ));
    
}


function es_facet_object($string) {
    $string = substr($string, 1, -1);
    $regex = '/(?<=\s|\A)([^\s=]+)=(.*?)(?=(?:\s[^\s=]+=|$))/';
    // $regex = '/(.*)=(.*)/';
    $matches = array();
    if (preg_match_all($regex, $string, $matches)) {
        $result = array();
        foreach ($matches[0] as $i => $key_value) {
            list($key, $value) = explode('=', $key_value, 2);
            $result[$key] = ($i + 1 == count($matches[0])) ? $value : substr($value, 0, -1);
        }
        return $result;
    } else {
        return $string;
    }
}
