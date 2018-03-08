<?php

function _edoweb_build_parent_trail($entity, $trail = array()) {
    // Recursively add parents to trail
    $parents = field_get_items('edoweb_basic', $entity, 'field_edoweb_struct_parent');
    if (FALSE !== $parents) {
        $parent = edoweb_basic_load($parents[0]['value']);
        $trail = array_merge(
            _edoweb_build_parent_trail($parent, $trail),
            $trail
            );
    }
    $title = entity_label(EDOWEB_ENTITY_TYPE, $entity);
    $entity_url = entity_class_uri($entity);
    $attributes = array('attributes' => array(
        'class' => array("entity-label-{$entity->bundle_type}"),
    ));
    $trail[] = l($title, $entity_url['path'], $attributes);
    return $trail;
}

function _edoweb_build_breadcrumb($entity) {
    $trail = _edoweb_build_parent_trail($entity);
    array_unshift($trail, l(t('Home'), 'resource'));
    drupal_set_breadcrumb($trail);
}

