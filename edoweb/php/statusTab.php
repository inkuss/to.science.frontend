<?php

/**
 * Provides API status information for a single entity.
 */
function edoweb_basic_status($entity) {
    
    $api = new EdowebAPIClient();
    $status = $api->getStatus($entity);
    $content = array();
    
    $content['interfaces'] = array(
        '#type' => 'fieldset',
        '#title' => t('Interfaces'),
    );
    
    $urnStatus = @$status['urnStatus'] == 200 ? t('registriert') : t('nicht registriert');
    
    $content['interfaces']['_urnStatus'] = array(
        '#prefix' => '<div class="field field-label-above"><div class="field-label">'
        . t('URN')
        . ':</div><div class="field-items"><div class="field-item even">',
        '#suffix' => '</div></div></div>',
        '#markup' => '<a href="' . @$status['links']['urn'] . '"target="_blank">' . $urnStatus . '</a>',
        '#weight' => 999,
    );
    
    $oaiStatus = @$status['oaiStatus'] == 200 ? t('gemeldet') : t('nicht gemeldet');
    
    $content['interfaces']['_oaiStatus'] = array(
        '#prefix' => '<div class="field field-label-above"><div class="field-label">'
        . t('Katalog')
        . ':</div><div class="field-items"><div class="field-item even">',
        '#suffix' => '</div></div></div>',
        '#markup' => '<a href="' . @$status['links']['oai'] . '"target="_blank">' . $oaiStatus . '</a>',
        '#weight' => 999,
    );
    
    if ('webpage' == $entity->bundle()) {
        $content['webgatherer'] = array(
            '#type' => 'fieldset',
            '#title' => t('Web Gatherer'),
        );
        $content['webgatherer']['_crawlControllerState'] = array(
            '#prefix' => '<div class="field field-label-above"><div class="field-label">'
            . t('Crawler state')
            . ':</div><div class="field-items"><div class="field-item even">',
            '#suffix' => '</div></div></div>',
            '#markup' => @$status['webgatherer']['crawlControllerState'],
            '#weight' => 999,
        );
        $content['webgatherer']['_crawlExitStatus'] = array(
            '#prefix' => '<div class="field field-label-above"><div class="field-label">'
            . t('Exit status')
            . ':</div><div class="field-items"><div class="field-item even">',
            '#suffix' => '</div></div></div>',
            '#markup' => @$status['webgatherer']['crawlExitStatus'],
            '#weight' => 999,
        );
        $content['webgatherer']['_launchCount'] = array(
            '#prefix' => '<div class="field field-label-above"><div class="field-label">'
            . t('Launch Count')
            . ':</div><div class="field-items"><div class="field-item even">',
            '#suffix' => '</div></div></div>',
            '#markup' => @$status['webgatherer']['launchCount'],
            '#weight' => 999,
        );
        
        $content['webgatherer']['_lastLaunch'] = array(
            '#prefix' => '<div class="field field-label-above"><div class="field-label">'
            . t('Last launch')
            . ':</div><div class="field-items"><div class="field-item even">',
            '#suffix' => '</div></div></div>',
            '#markup' => isset($status['webgatherer']['lastLaunch'])
            ? _edoweb_format_date(strtotime($status['webgatherer']['lastLaunch'])) : '',
            '#weight' => 999,
        );
        $content['webgatherer']['_nextLaunch'] = array(
            '#prefix' => '<div class="field field-label-above"><div class="field-label">'
            . t('Next launch')
            . ':</div><div class="field-items"><div class="field-item even">',
            '#suffix' => '</div></div></div>',
            '#markup' => isset($status['webgatherer']['nextLaunch'])
            ? _edoweb_format_date(strtotime($status['webgatherer']['nextLaunch'])) : '',
            '#weight' => 999,
        );
    }
    
    return $content;
    
}