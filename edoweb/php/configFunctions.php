<?php

function _edoweb_available_languages() {
    $languages = include drupal_realpath(file_default_scheme() . '://') .  '/available_languages.inc';
    return $languages;
}

function _edoweb_available_medium() {
    return array(
        'http://rdvocab.info/termList/RDAproductionMethod/1010' =>t("Print"),
        'http://rdvocab.info/termList/RDACarrierType/1018' =>t("Online"),
        'http://purl.org/ontology/bibo/AudioDocument' =>t("Audio"),
        'http://rdvocab.info/termList/RDACarrierType/1050' =>t("Video"),
        'http://purl.org/ontology/bibo/Image' =>t("Bild"),
        'http://pbcore.org/vocabularies/instantiationMediaType#text ' =>t("Text"),
        'http://pbcore.org/vocabularies/instantiationMediaType#software' =>t("Software"),
        'http://purl.org/lobid/lv%23Miscellaneous' =>t("Andere"),
    );
    
}

function _edoweb_available_license() {
    return array(
        'https://creativecommons.org/licenses/by/4.0' =>t("CC BY 4.0"),
        'https://creativecommons.org/licenses/by-nd/4.0' =>t("CC BY-ND 4.0"),
        'https://creativecommons.org/licenses/by-nc-sa/4.0' =>t("CC BY-NC-SA  4.0"),
        'https://creativecommons.org/licenses/by-sa/4.0' =>t("CC BY-SA  4.0"),
        'https://creativecommons.org/licenses/by-nc/4.0' =>t("CC BY-NC  4.0"),
        'https://creativecommons.org/licenses/by-nc-nd/4.0' =>t("CC BY-NC-ND  4.0"),
    );
}

function _edoweb_available_professionalGroup() {
    return array(
        'http://d-nb.info/gnd/4038243-6' =>t("Medizin"),
        'http://d-nb.info/gnd/4020775-4' =>t("Gesundheitswesen"),
        'http://d-nb.info/gnd/4152829-3' =>t("Ernährungswissenschaften"),
        'http://d-nb.info/gnd/4068473-8' =>t("Agrarwissenschaften"),
        'http://d-nb.info/gnd/4137364-9' =>t("Umweltwissenschaften"),
        'http://d-nb.info/gnd/4006851-1' =>t("Biologie"),
    );
    
}
function _edoweb_available_data_origin(){
    return array(
        'http://hbz-nrw.de/regal#Interview' =>t("Interview"),
        'http://hbz-nrw.de/regal#Umfrage' =>t("Umfrage"),
        'http://hbz-nrw.de/regal#Anamnese' =>t("Anamnese"),
        'http://hbz-nrw.de/regal#Exploration' =>t("Exploration"),
        'http://hbz-nrw.de/regal#Probe' =>t("Probe"),
        'http://hbz-nrw.de/regal#Gewebeprobe' =>t("Gewebeprobe"),
        'http://hbz-nrw.de/regal#Flaechenmischprobe' =>t("Flächenmischprobe"),
        'http://hbz-nrw.de/regal#Bodenbohrung' =>t("Bodenbohrung"),
        'http://hbz-nrw.de/regal#apparativeUntersuchung' =>t("apparative Untersuchung"),
        'http://hbz-nrw.de/regal#koerperlicheUntersuchung' =>t("körperliche Untersuchung"),
        'http://hbz-nrw.de/regal#Feldbeobachtung' =>t("Feldbeobachtung"),
        'http://hbz-nrw.de/regal#Laborbeobachtung' =>t("Laborbeobachtung"),
        'http://hbz-nrw.de/regal#Analyse' =>t("Analyse"),
        'http://hbz-nrw.de/regal#Genomsequenzierung' =>t("Genomsequenzierung"),
        'http://hbz-nrw.de/regal#Messung' =>t("Messung"),
        'http://hbz-nrw.de/regal#Berechnung' =>t("Berechnung"),
        'http://hbz-nrw.de/regal#Evaluation' =>t("Evaluation"),
        'http://hbz-nrw.de/regal#Querschnittstudie' =>t("Querschnittstudie"),
        'http://hbz-nrw.de/regal#Langzeitstudie' =>t("Langzeitstudie"),
        'http://hbz-nrw.de/regal#Interventionsstudie' =>t("Interventionsstudie"),
        'http://hbz-nrw.de/regal#Kohortenstudie' =>t("Kohortenstudie"),
        'http://hbz-nrw.de/regal#Simulation' =>t("Simulation"),
        'http://hbz-nrw.de/regal#Andere' =>t("Andere"),
    );
}



function _edoweb_entity_table_headers_defaults() {
    return array(
        '_edoweb_compact_view',
        'field_edoweb_issued',
        'objectTimestamp',
        '_edoweb_format_access_icons',
        '_edoweb_link_lastmodified',
        'bundle_type',
    );
}

function _edoweb_entity_table_headers($init_sort = null) {
    return array (
        'field_edoweb_title' => array (
            'data' => 'Titel',
            'type' => 'field',
            'specifier' => array (
                'field' => 'field_edoweb_title',
                'column' => 'value'
            ),
            'format' => '_full_title'
        ),
        'field_edoweb_identifier_ht' => array (
            'data' => t ( 'ID' ),
            'type' => 'field',
            'specifier' => array (
                'field' => 'field_edoweb_identifier_ht',
                'column' => 'value'
            ),
            'format' => '_get_link'
        ),
        '_edoweb_compact_view' => array (
            'data' => t ( 'Kurzansicht' ),
            'type' => 'generated',
            'format' => '_edoweb_compact_view'
        ),
        'field_edoweb_issued' => array (
            'data' => t ( 'Issued' ),
            'type' => 'field',
            'specifier' => array (
                'field' => 'field_edoweb_issued',
                'column' => 'value'
            )
        ),
        'objectTimestamp' => array (
            'data' => t ( 'Updated' ),
            'type' => 'property',
            'specifier' => 'objectTimestamp',
            'format' => '_edoweb_format_date',
            'sort' => $init_sort ? 'desc' : null
        ),
        '_edoweb_format_access_icons' => array (
            'data' => t ( 'Zugriff' ),
            'type' => 'generated',
            'format' => '_edoweb_format_access_icons'
        ),
        '_edoweb_link_lastmodified' => array (
            'data' => t ( 'Zuletzt hinzugefügtes Label' ),
            'type' => 'generated',
            'format' => '_edoweb_link_lastmodified'
        ),
        'bundle_type' => array (
            'data' => t ( 'Objektart' ),
            'type' => 'property',
            'specifier' => 'bundle_type',
            'format' => '_edoweb_format_bundle_name'
        ),
        'type' => array (
            'data' => t ( 'Typ' ),
            'type' => 'field',
            'specifier' => array (
                'field' => 'field_edoweb_type',
                'column' => 'value'
            )
        )
    );
}

function _edoweb_authority_table_headers_defaults() {
    return array(
        'field_gnd_identifier',
        'field_gnd_name',
        '_edoweb_format_subject',
        'field_gnd_date_of_establishment',
        'field_gnd_date_of_termination',
        'field_gnd_profession',
        'field_gnd_date_of_birth',
        'field_gnd_date_of_death'
    );
}

function _edoweb_authority_table_headers() {
    return array(
        'field_gnd_name' => array(
            'data' => 'Name / Titel',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_name',
                'column' => 'value',
            ),
            'format' => '_get_edoweb_url',
        ),
        'field_gnd_identifier' => array(
            'data' => 'ID / Notation',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_identifier',
                'column' => 'value',
            ),
            'format' => '_get_external_url',
        ),
        '_edoweb_compact_view' => array(
            'data' => t('Kurzansicht'),
            'type' => 'generated',
            'format' => '_edoweb_compact_view',
        ),
        '_edoweb_format_subject' => array(
            'data' => 'Quelle',
            'type' => 'property',
            'specifier' => 'remote_id',
            'format' => '_edoweb_format_subject',
        ),
        'field_gnd_date_of_establishment' => array(
            'data' => 'Gründungsdatum',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_date_of_establishment',
                'column' => 'value',
            ),
        ),
        'field_gnd_date_of_termination' => array(
            'data' => 'Auflösungsdatum',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_date_of_termination',
                'column' => 'value',
            ),
        ),
        'field_gnd_profession' => array(
            'data' => 'Beruf',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_profession',
                'column' => 'value',
            ),
        ),
        'field_gnd_date_of_birth' => array(
            'data' => 'Geburtsdatum',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_date_of_birth',
                'column' => 'value',
            ),
        ),
        'field_gnd_date_of_death' => array(
            'data' => 'Todesdatum',
            'type' => 'field',
            'specifier' => array(
                'field' => 'field_gnd_date_of_death',
                'column' => 'value',
            ),
        ),
    );
}


/**
 * Due to the way Drupals t() function works, variable strings must be
 * mapped to literals.
 */
function _edoweb_map_string($string) {
    switch ($string) {
        case 'creator':
            return t('creator');
        case 'subject':
            return t('subject');
        case 'rpbSubject':
            return t('rpbSubject');
        case 'issued':
            return t('issued');
        case 'contentType':
            return t('contentType');
        case 'file':
            return t('file');
        case 'monograph':
            return t('monograph');
        case 'journal':
            return t('journal');
        case 'volume':
            return t('volume');
        case 'issue':
            return t('issue');
        case 'article':
            return t('article');
        case 'webpage':
            return t('webpage');
        case 'researchData':
            return t('researchData');
        case 'version':
            return t('version');
        case 'part':
            return t('part');
        case 'Add monograph':
            return t('Add monograph');
        case 'Add article':
            return t('Add article');
        case 'Add journal':
            return t('Add journal');
        case 'Add volume':
            return t('Add volume');
        case 'Add issue':
            return t('Add issue');
        case 'Add Publication':
            return t('Add Publication');
        case 'Add file':
            return t('Add file');
        case 'Add part':
            return t('Add part');
        case 'Add webpage':
            return t('Add webpage');
        case 'Add researchData':
            return t('Add researchData');
        case 'Add version':
            return t('Add version');
        case 'createdBy':
            return t('createdBy');
        case 'lastModifiedBy':
            return t('lastModifiedBy');
        case 'private':
            return t('private');
        case 'public':
            return t('public');
        case 'subscriber':
            return t('subscriber');
        case 'restricted':
            return t('restricted');
        case 'remote':
            return t('remote');
        case 'institution':
            return t('institution');
        case 'rdftype':
            return t('type');
        case 'medium':
            return t('medium');
        default:
            return $string;
    }
}


/*
 * Implements hook_rdf_namespaces().
 *
 * This hook should be used to define any prefixes used by this module that are
 * not already defined in core by entity_rdf_namespaces.
 *
 * http://api.drupal.org/api/drupal/modules--rdf--rdf.api.php/function/hook_rdf_namespaces/7
 */
function edoweb_rdf_namespaces() {
    $edoweb_api_host = variable_get('edoweb_api_host');
    $edoweb_api_namespace = variable_get('edoweb_api_namespace');
    $edoweb_lobid_host = variable_get('edoweb_lobid_host');
    return array(
        $edoweb_api_namespace => "$edoweb_api_host/resource/$edoweb_api_namespace:",
        'rd_authors' => "$edoweb_api_host/authors/",
        'rd_local' => "$edoweb_api_host/resource/rd_local:",
        'rd_template' => "$edoweb_api_host/resource/rd_template:",
        'rd_isbd' => 'http://iflastandards.info/ns/isbd/elements/',
        'rd_frbr' => 'http://purl.org/vocab/frbr/core#',
        'rd_bibo' => 'http://purl.org/ontology/bibo/',
        'rd_dce'  => 'http://purl.org/dc/elements/1.1/',
        'rd_ore'  => 'http://www.openarchives.org/ore/terms/',
        'rd_foaf'  => 'http://xmlns.com/foaf/0.1/',
        'rd_umbel'  => 'http://umbel.org/umbel#',
        'rd_lv'  => 'http://purl.org/lobid/lv#',
        'rd_rdfs'  => 'http://www.w3.org/2000/01/rdf-schema#',
        'rd_orca'  => 'http://geni-orca.renci.org/owl/topology.owl#',
        'rd_dnb' => 'http://d-nb.info/gnd/',
        'rd_gnd' => 'http://d-nb.info/standards/elementset/gnd#',
        'rd_lr' => "$edoweb_lobid_host/resource/",
        'rd_skos' => 'http://www.w3.org/2004/02/skos/core#',
        'rd_ddc' => 'http://dewey.info/class/',
        'rd_rdvocab' => 'http://rdvocab.info/Elements/',
        'rd_file' => 'http://downlode.org/Code/RDF/File_Properties/schema#',
        'rd_marcrel' => 'http://id.loc.gov/vocabulary/relators/',
        'rd_rpb' => 'http://purl.org/lobid/rpb#',
        'rd_nwbib' => 'http://purl.org/lobid/nwbib#',
        'rd_nwbib-spatial' => 'http://purl.org/lobid/nwbib-spatial#',
        'rd_radion' => 'http://www.w3.org/ns/radion#',
        'rd_holding' => 'http://purl.org/ontology/holding#',
        'rd_dbpedia' => 'http://dbpedia.org/ontology/',
        'rd_rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rd_rda' => 'http://rdaregistry.info/Elements/u/',
        'rd_rda-media' => 'http://rdaregistry.info/termList/RDAMediaType/',
        'rd_bibframe' => 'http://id.loc.gov/ontologies/bibframe/',
        'rd_zettel' =>'info:regal/zettel/',
	'rd_regal' => 'http://hbz-nrw.de/regal#',
    );
}


function edoweb_rdf_types($bundle = null) {
    $rdf_types = array(
        'person' => array('rd_gnd:DifferentiatedPerson', 'rd_gnd:UndifferentiatedPerson'),
        'corporate_body' => array('rd_gnd:CorporateBody'),
        'event' => array('rd_gnd:ConferenceOrEvent'),
        'family' => array('rd_gnd:Family'),
        'work' => array('rd_gnd:Work'),
        'place' => array('rd_gnd:PlaceOrGeographicName'),
        'subject' => array(
            'rd_skos:Concept',
            'rd_gnd:SubjectHeading'
        ),
        'monograph' => array('rd_bibo:Book'),
        'journal' => array('rd_bibo:Journal'),
        'volume' => array('rd_bibo:Volume'),
        'issue' => array('rd_bibo:Issue'),
        'article' => array('rd_bibo:Article'),
        'file' => array('rd_bibo:DocumentPart'),
        'webpage' => array('rd_lv:ArchivedWebPage'),
        //FIXME: dummy RDF type for version
        'version' => array('rd_lv:ArchivedWebPageVersion'),
        'generic' => array('dc:BibliographicResource'),
        'researchData' => array('rd_regal:ResearchData'),
        //FIXME: do we need an RDF type mapping for the 'part' bundle?
        'part' => array('dc:BibliographicResource'),
        'authority_resource' => array('rd_gnd:AuthorityResource'),
        'collection' => array(
            'rd_bibo:Collection',
            'rd_radion:Repository',
            'rd_bibo:Series',
        ),
    );
    return $bundle ? $rdf_types[$bundle] : $rdf_types;
}

function _edoweb_format_access_icons($entity) {
    
    if (!_is_edoweb_entity($entity)) {
        return '';
    }
    
    $icons = '';
    switch ($entity->access_data) {
        case 'private':
            $icons .= '<i class="fa fa-lock" title="' . t($entity->access_data) . '" style="color:red;"></i>';
            break;
        case 'subscriber':
        case 'restricted':
            $icons .= '<i class="fa fa-lock" title="' . t($entity->access_data) . '"style="color:orange;"></i>';
            break;
        case 'remote':
            $icons .= '<i class="fa fa-lock" title="' . t($entity->access_data) . '"style="color:yellow;"></i>';
            break;
        case 'public':
            $icons .= '<i class="fa fa-unlock" title="' . t($entity->access_data) . '"style="color:green;"></i>';
            break;
    }
    
    switch ($entity->access_md) {
        case 'private':
            $icons .= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle-o" title="' . t($entity->access_md) . '"style="color:red;"></i>';
            break;
        case 'public':
            $icons .= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle-o" title="' . t($entity->access_md) . '"style="color:green;"></i>';
            break;
    }
    
    return $icons;
    
}
