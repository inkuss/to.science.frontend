<?php

function edoweb_basic_edit($entity, $view_mode = 'edit') {
    
    drupal_add_js(
        drupal_get_path('module', 'edoweb') . '/js/edoweb_edit.js'
        );
    drupal_add_js(
        drupal_get_path('module', 'edoweb') . '/js/zettel.js'
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


/**
 * Provides a wrapper on the edit form to add a new child to an entity.
 */
function edoweb_basic_structure($entity) {
    $api = new EdowebAPIClient();
    if ('POST' == $_SERVER['REQUEST_METHOD'] && user_access('edit any edoweb_basic entity')) {
        $new_parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : FALSE;
        $parts = isset($_POST['parts']) ? $_POST['parts'] : FALSE;
        $root_id = isset($_POST['root_id']) ? $_POST['root_id'] : FALSE;
        $tree_html = isset($_POST['tree_html']) ? $_POST['tree_html'] : FALSE;
        
        if ($new_parent_id) {
	    // Umhängen der Entity an einen neuen Parent.
            $wrapper = entity_metadata_wrapper('edoweb_basic', $entity);
            $prev_parent = $wrapper->field_edoweb_struct_parent->value();
            $prev_parent_id = $prev_parent['value'];
            $wrapper->field_edoweb_struct_parent = array(
                'value' => $new_parent_id,
                'label' => '',
            );
            //TODO: How to add response text by AJAX?
            if ($api->saveResource($entity)) {
                echo "Moving {$entity->remote_id} from $prev_parent_id to $new_parent_id\n";
            } else if ($new_parent_id) {
                echo "Failed moving {$entity->remote_id} from $prev_parent_id to $new_parent_id\n";
            }
        }
        //TODO: How to add response text by AJAX?
        if ($parts && $api->setParts($entity, $parts)) {
            echo "Settings parts for {$entity->remote_id}\n";
        } else if ($parts) {
            echo "Failed settings parts for {$entity->remote_id}\n";
        }
	// Abspeichern des Baumes am Parent (root_id = Journal oder Monograph); neu für TOSDEV-11
	if ($root_id && $tree_html && $api->writeTree($entity, $root_id, $tree_html)) {
            echo "Writing tree_html for {$entity->remote_id}\n";
        } else if ($tree_html) {
            echo "Failed writing tree_html for {$entity->remote_id}\n";
        }
        die;
    } else if ('POST' == $_SERVER['REQUEST_METHOD']) {
        return MENU_ACCESS_DENIED;
    } else { // GET-Methode
        $subtree = _edoweb_build_tree($api->getTree($entity));
        if ( $subtree['isHtml'] ) {
          // drupal_set_message("editTab.php: Output is html: " . $subtree['html']);
          die('<ul class="edoweb-tree">' + $subtree['html'] + '</ul>');
          }
        // falls nicht HTML geliefert wurde, nimmt die Drupal-Funktiom theme_item_list ein Rendering nach HTML vor.
        // drupal_set_message("editTab.php Rendering tree item list array to HTML for curie: " . $subtree['data-curie']);
        die(theme_item_list(array(
            'items' => array($subtree),
            'title' => null,
            'type' => 'ul',
            'attributes' => array('class' => array('edoweb-tree')),
        )));
    }
}
