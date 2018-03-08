<?php

function _edoweb_build_tree($tree) {
    
    // Add current entity to tree
    $title = isset($tree['title'])
    ? implode(', ', $tree['title']) : $tree['@id'];
    $entity_bundle = $tree['contentType'];
    $options = array('attributes' => array('data-bundle' => $entity_bundle));
    $tree_item = array(
        'data' => l($title, 'resource/' . $tree['@id'], $options),
        'data-curie' => $tree['@id'],
        'class' => array('collapsed'),
    );
    
    // Append download links when applicable
    if ('file' == $entity_bundle && isset($tree['hasData'])) {
        $tree_item['data'] .= _edoweb_download_link($tree['hasData']['format'], $tree['hasData']['@id']);
    }
    
    // Add child enities to tree
    if (isset($tree['hasPart'])) {
        $children = $tree['hasPart'];
        foreach ($children as $child) {
            foreach ($child as $id => $subtree) {
                $tree_item['children'][] = _edoweb_build_tree($subtree);
            }
        }
    }
    
    return $tree_item;
    
}
