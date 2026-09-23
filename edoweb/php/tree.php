<?php

function _edoweb_build_tree($tree_json) {
    
	// drupal_set_message("_edoweb_build_tree: tree_json: " . json_encode($tree_json));
	// drupal_set_message("_edoweb_build_tree: isHtml: " . $tree_json['isHtml']);
    if ($tree_json['isHtml']) {
      return $tree_json;
    }
    /**
     * Wenn nicht schon HTML geliefert wurde, wurde von der API JSON geliefert.
     * Aus diesem wird jetzt eine verschachtelte Liste von "tree items" aufgebaut.
     * Diese wird später mit der Drupal-Funktion "theme_item_list" als HTML ausgegeben werden ("gerendered").
     */
    $tree = $tree_json['json'];
	// drupal_set_message("_edoweb_build_tree: Start building tree item array for curie ".$tree['@id']);
	// drupal_set_message("_edoweb_build_tree: tree: " . var_dump($tree));
    // Add current entity to tree
    $title = isset($tree['title'])
    ? implode(', ', $tree['title']) : $tree['@id'];
    $entity_bundle = $tree['contentType'];
    $options = array('attributes' => array('data-bundle' => $entity_bundle));
    $tree_item = array(
	'isHtml' => false,
        'data' => l($title, 'resource/' . $tree['@id'], $options),
        'data-curie' => $tree['@id'],
        'class' => array('collapsed'),
    );
    
    // Append download links when applicable
    // Rm: EDOZWO-1005 hide all pdf-icons in tree
    // if ('file' == $entity_bundle && isset($tree['hasData'])) {
    //    $tree_item['data'] .= _edoweb_download_link($tree['hasData']['format'], $tree['hasData']['@id']);
    // }
    
    // Add child enities to tree
    if (isset($tree['hasPart'])) {
        $children = $tree['hasPart'];
        foreach ($children as $child) {
            foreach ($child as $id => $subtree) {
                $tree_item['children'][] = _edoweb_build_tree( json_decode(sprintf('{"isHtml":false,"json":%s}', json_encode($subtree)), TRUE));
            }
        }
    }
    
    return $tree_item;
    
}
