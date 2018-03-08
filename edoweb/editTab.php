<?php

function edoweb_basic_edit($entity, $view_mode = 'edit') {
    
    drupal_add_js(
        drupal_get_path('module', 'edoweb') . '/edoweb_edit.js'
        );
    drupal_add_js(
        drupal_get_path('module', 'edoweb') . '/zettel.js'
        );
    
    global $base_url;
    $page_url = $base_url . '/' . current_path();
    $api = new EdowebAPIClient();
    drupal_add_js(array('baseUrl' => $base_url), 'setting');
    drupal_add_js(array('rdf' => ""), 'setting');
    $rdf= $api->getMetadata($entity);
    drupal_add_js(array('rdf' => $rdf), 'setting');
    drupal_add_js(array('actionPath' =>current_path()), 'setting');
    
    
    $postdata = file_get_contents("php://input");
    if ($postdata != '') {
        $rdf_model = new LibRDF_Model(new LibRDF_Storage());
        $rdf_parser = new LibRDF_Parser('rdfxml');
        
        $rdf_model->loadStatementsFromString(
            $rdf_parser, $postdata
            );
        $entity_uri = $rdf_model->getTarget(
            new LibRDF_URINode($page_url),
            new LibRDF_URINode('http://xmlns.com/foaf/0.1/primaryTopic')
            );
        $entity->remote_id = $entity_uri->getValue();
        _edoweb_storage_entity_deserialize_rdf($entity, $rdf_model);
        
        $entity->namespace = isset($_GET['namespace'])
        ? $_GET['namespace'] : null;
        $entity->name = isset($_GET['name'])
        ? $_GET['name'] : null;
        
        if ($entity_uri instanceof LibRDF_BlankNode) {
            $entity->remote_id = null;
        }
        $entity->postData = $postdata;
        edoweb_basic_save($entity);
        drupal_add_http_header('X-Edoweb-Entity', $entity->remote_id);
        die($entity->remote_id);
    }
    
    return edoweb_basic_view($entity, $view_mode);
    
}