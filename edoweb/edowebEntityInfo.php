<?php

/**
 * Implements hook_entity_info().
 *
 * This is the fundamental description of the entity.
 *
 * It provides a single entity with multiple bundles
 * and without revision support.
 */
function edoweb_entity_info() {
    $info['edoweb_basic'] = array(
        
        // A human readable label to identify our entity.
        'label' => t('Edoweb Basic Entity'),
        
        // Callback to label entities
        'label callback' => 'entity_class_label',
        
        // The class for our Entity
        'entity class' => 'EdowebBasicEntity',
        
        // The controller for our Entity, extending the Drupal core controller.
        'controller class' => 'EdowebBasicController',
        
        // No base table as we are storing only remote
        'base table' => null,
        
        // Returns the uri elements of an entity
        'uri callback' => 'entity_class_uri',
        
        // IF fieldable == FALSE, we can't attach fields.
        'fieldable' => TRUE,
        
        // entity_keys tells the controller what database fields are used
        // for key functions
        'entity keys' => array(
            // The 'id' (remote_id here) is the unique id.
            'id' => 'remote_id',
            // Bundle will be determined by the 'bundle_type' field
            'bundle' => 'bundle_type'
        ),
        'bundle keys' => array(
            'bundle' => 'bundle_type',
        ),
        
        // Bundles are alternative groups of fields or configuration
        // associated with a base entity type.
        'bundles' => array(
            'monograph' => array(
                'label' => t('Monograph'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/monograph/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'journal' => array(
                'label' => t('Journal'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/journal/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'volume' => array(
                'label' => t('Volume'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/volume/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'issue' => array(
                'label' => t('Issue'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/issue/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'article' => array(
                'label' => t('Article'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/article/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'file' => array(
                'label' => t('File'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/file/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'webpage' => array(
                'label' => t('Webseite'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/webpage/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'version' => array(
                'label' => t('Version'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/version/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'generic' => array(
                'label' => t('Bibliographic Resource'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/generic/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'part' => array(
                'label' => t('Part'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/part/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'person' => array(
                'label' => t('Person'),
                'namespace' => 'local',
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/person/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'rpbSubject' => array(
                'label' => t('RPB Erschließung'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/subject/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'subject' => array(
                'label' => t('Sachschlagwort'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/subject/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'corporate_body' => array(
                'label' => t('Körperschaft'),
                'namespace' => 'local',
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/corporate_body/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'event' => array(
                'label' => t('Veranstaltung'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/event/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'family' => array(
                'label' => t('Familie'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/family/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'work' => array(
                'label' => t('Werk'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/work/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'place' => array(
                'label' => t('Geographikum'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/place/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'collection' => array(
                'label' => t('Sammlung'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/collection/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'authority_resource' => array(
                'label' => t('Authority Resource'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/authority_resource/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'researchData' => array(
                'label' => t('Forschungsdaten'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/researchData/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
            'proceeding' => array(
                'label' => t('Konferenzbeitrag'),
                'admin' => array(
                    'path' => 'admin/structure/edoweb_basic/proceeding/manage',
                    'access arguments' => array('administer edoweb_basic entities'),
                ),
            ),
        ),
        'view modes' => array(
            'compact' => array(
                'label' => t('Compact'),
                'custom settings' => TRUE,
            ),
        ),
    );
    
    return $info;
}