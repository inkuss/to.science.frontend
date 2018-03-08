<?php

/**
 * Processes a form to administrate webpage crawls.
 */
function edoweb_basic_crawler_form_submit($form, &$form_state) {
    
    $api = new EdowebAPIClient();
    $entity = $form_state['values']['basic_entity'];
    
    
    $conf = array();
    $conf['url'] = $form_state['values']['url'];
    $conf['httpResponseCode'] = $form_state['values']['httpResponseCode'];
    $conf['invalidUrl'] = $form_state['values']['invalidUrl'];
    if( $conf['invalidUrl'] == true ) {
        $conf['urlNew'] = $form_state['values']['urlNew'];
    }
    $conf['urlHist'] = $form_state['values']['urlHist'];
    if( isset($form_state['values']['domain00']) && $form_state['values']['domain00'] != '') {
        $conf['domains'] = array($form_state['values']['domain00']);
    }
    $i = 1;
    while( isset($form_state['values'][sprintf('domain%02d',$i)]) && $form_state['values'][sprintf('domain%02d',$i)] != '') {
        array_push($conf['domains'], $form_state['values'][sprintf('domain%02d',$i)]);
        $i++;
    }
    $conf['active'] = $form_state['values']['active'];
    $conf['startDate'] = date("Y-m-d", strtotime($form_state['values']['startDate']['year']
        . '-' . $form_state['values']['startDate']['month']
        . '-' . $form_state['values']['startDate']['day']));
    $conf['interval'] = $form_state['values']['interval'];
    $conf['robotsPolicy'] = $form_state['values']['robotsPolicy'];
    $conf['crawlerSelection'] = $form_state['values']['crawlerSelection'];
    if( $conf['crawlerSelection'] == 'wpull' ) {
        if( isset($form_state['values']['urlExcluded00']) && $form_state['values']['urlExcluded00'] != '') {
            $conf['urlsExcluded'] = array($form_state['values']['urlExcluded00']);
        }
        $i = 1;
        while( isset($form_state['values'][sprintf('urlExcluded%02d',$i)]) && $form_state['values'][sprintf('urlExcluded%02d',$i)] != '') {
            array_push($conf['urlsExcluded'], $form_state['values'][sprintf('urlExcluded%02d',$i)]);
            $i++;
        }
        if( isset($form_state['values']['deepness']) && $form_state['values']['deepness'] != '' )
        { $conf['deepness'] = $form_state['values']['deepness']; }
        if( isset($form_state['values']['maxCrawlSize']) && $form_state['values']['maxCrawlSize'] != '' )
        { $conf['maxCrawlSize'] = $form_state['values']['maxCrawlSize']; }
        if( isset($form_state['values']['quotaUnitSelection']) && $form_state['values']['quotaUnitSelection'] != '' )
        { $conf['quotaUnitSelection'] = $form_state['values']['quotaUnitSelection']; }
        if( isset($form_state['values']['agentIdSelection']) && $form_state['values']['agentIdSelection'] != '' )
        { $conf['agentIdSelection'] = $form_state['values']['agentIdSelection']; }
        if( isset($form_state['values']['waitSecBtRequests']) && $form_state['values']['waitSecBtRequests'] != '' )
        { $conf['waitSecBtRequests'] = $form_state['values']['waitSecBtRequests']; }
        if( isset($form_state['values']['tries']) && $form_state['values']['tries'] != '' )
        { $conf['tries'] = $form_state['values']['tries']; }
        else $conf['tries'] = '5';
        if( isset($form_state['values']['waitRetry']) && $form_state['values']['waitRetry'] != '' )
        { $conf['waitRetry'] = $form_state['values']['waitRetry']; }
        else $conf['waitRetry'] = '20';
    }
    
    $api->setCrawlerConfiguration($entity, $conf);
    
    if ('crawl' == $form_state['triggering_element']['#name']) {
        $api->triggerCrawl($entity);
        $uri = $entity->uri();
        $form_state['redirect'] = $uri['path'] . '/status';
    }
    
    if ('confirmNewUrl' == $form_state['triggering_element']['#name']) {
        $api->confirmNewUrl($entity);
    }
}

