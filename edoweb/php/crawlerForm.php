<?php

/**
 * Provides a form to administrate webpage crawls.
 */
function edoweb_basic_crawler_form($form, &$form_state, $entity) {
    
    $api = new EdowebAPIClient();
    
    if (!$conf = $api->getCrawlerConfiguration($entity)) {
        $conf = array();
    }
    // console_log('conf: '.$conf); // logs to apache error_log
    
    $form['basic_entity'] = array(
        '#type' => 'value',
        '#value' => $entity,
    );
    
    $form['url'] = array(
        '#type' => 'textfield',
        '#default_value' => @$conf['url'],
        '#weight' => 00,
    );
    if( @$conf['invalidUrl'] == true ) {
        $form['url']['#title'] = t('URL <span class="octicon octicon-alert" title="Ungültige URL oder Website ist umgezogen !"></span>');
        $form['url']['#attributes'] = array('readonly' => 'readonly');
        $form['url']['#required'] = FALSE;
        $form['urlNew'] = array(
            '#type' => 'textfield',
            '#title' => t('neue URL <span class="octicon red octicon-alert" title="Bitte neue URL bestätigen, ggfs. editieren !"></span>'),
            '#default_value' => @$conf['urlNew'] == null ? '' : @$conf['urlNew'],
            '#required' => TRUE,
            '#weight' => 02,
        );
        $form['confirmNewUrl'] = array(
            '#type' => 'submit',
            '#name' => 'confirmNewUrl',
            '#value' => t('Neue URL bestätigen'),
            '#weight' => 04,
        );
    } else {
        $form['url']['#title'] = t('URL');
        $form['url']['#required'] = TRUE;
    }
    
    $form['httpResponseCode'] = array(
        '#type' => 'hidden',
        '#value' => @$conf['httpResponseCode'],
    );
    
    $form['invalidUrl'] = array(
        '#type' => 'hidden',
        '#value' => @$conf['invalidUrl'] == null ? false : @$conf['invalidUrl'],
    );
    
    $form['urlHist'] = array(
        '#type' => 'hidden',
        '#value' => @$conf['urlHist'],
    );
    
    $form['domains'] = array(
        '#type' => 'fieldset',
        '#title' => t('Zusätzliche Domänen, die ebenfalls gecrawlt werden sollen'),
        '#weight' => 10,
    );
    
    $form['domains']['domain00'] = array(
        '#type' => 'textfield',
        '#title' => t('1. zusätzliche Domäne'),
        '#default_value' => @$conf['domains'][0],
        '#required' => FALSE,
    );
    for($i = 1; $i < sizeof(@$conf['domains']); $i++) {
        $form['domains'][sprintf('domain%02d', $i)] = array(
            '#type' => 'textfield',
            '#title' => t(sprintf('%d. zusätzliche Domäne', $i+1)),
            '#default_value' => @$conf['domains'][$i],
            '#required' => FALSE,
        );
    }
    if( sizeof(@$conf['domains']) > 0 ) {
        $form['domains'][sprintf('domain%02d', sizeof(@$conf['domains']))] = array(
            '#type' => 'textfield',
            '#title' => t('Weitere Domäne angeben'),
            '#default_value' => '',
            '#required' => FALSE,
        );
    }
    
    console_log('active='.@$conf['active']);
    $form['active'] = array(
        '#type' => 'checkbox',
        '#title' => t('Aktiv'),
        '#default_value' => @$conf['active'] == null ? true : @$conf['active'],
        '#weight' => 20,
    );
    
    $form['startDate'] = array(
        '#type' => 'date',
        '#title' => t('Datum des 1. Crawls'),
        '#default_value' => isset($conf['startDate'])
        ?  date_parse($conf['startDate'])
        : array(),
        '#required' => TRUE,
        '#weight' => 30,
    );
    
    $form['interval'] = array(
        '#type' => 'select',
        '#title' => t('Interval'),
        '#options' => array(
            'annually' => t('Annually'),
            'halfYearly' => t('Half yearly'),
            'quarterly' => t('Quarterly'),
            'monthly' => t('Monthly'),
            'weekly' => t('Weekly'),
            'daily' => t('Daily'),
            'once' => t('Once'),
        ),
        '#default_value' => @$conf['interval'],
        '#required' => TRUE,
        '#weight' => 40,
    );
    
    $form['robotsPolicy'] = array(
        '#type' => 'radios',
        '#title' => t('Robots-Regeln'),
        '#options' => array(
            'ignore' => t('Ignorieren'),
            'obey' => t('Befolgen'),
        ),
        '#default_value' => @$conf['robotsPolicy'] == null ? 'ignore' : @$conf['robotsPolicy'] == 'classic' ? 'ignore' : @$conf['robotsPolicy'],
        '#required' => TRUE,
        '#weight' => 50,
    );
    console_log('issetRobotsPolicy='.(@$conf['robotsPolicy']!==null));
    console_log('robotsPolicy='.@$conf['robotsPolicy']);
    
    $form['crawlerSelection'] = array(
        '#type' => 'select',
        '#title' => t('Crawler-Auswahl'),
        '#options' => array(
            'heritrix' => t('heritrix'),
            'wpull' => t('wpull'),
        ),
        '#default_value' => @$conf['crawlerSelection'] == null ? 'wpull' : @$conf['crawlerSelection'],
        '#required' => FALSE,
        '#weight' => 60,
    );
    console_log('issetCrawlerSelection='.(@$conf['crawlerSelection']!==null));
    console_log('crawlerSelection='.@$conf['crawlerSelection']);
    console_log('issetDeepness='.(@$conf['deepness']!==null));
    console_log('deepness='.@$conf['deepness']);
    
    if( @$conf['crawlerSelection'] == 'wpull' ) {
        
        $form['urlsExcluded'] = array(
            '#type' => 'fieldset',
            '#title' => t('URL-Bereiche, die ausgeschlossen werden sollen'),
            '#weight' => 70,
        );
        
        $form['urlsExcluded']['urlExcluded00'] = array(
            '#type' => 'textfield',
            '#title' => t('1. auszuschließender Bereich'),
            '#default_value' => @$conf['urlsExcluded'][0],
            '#required' => FALSE,
        );
        for($i = 1; $i < sizeof(@$conf['urlsExcluded']); $i++) {
            $form['urlsExcluded'][sprintf('urlExcluded%02d', $i)] = array(
                '#type' => 'textfield',
                '#title' => t(sprintf('%d. auszuschließender Bereich', $i+1)),
                '#default_value' => @$conf['urlsExcluded'][$i],
                '#required' => FALSE,
            );
        }
        if( sizeof(@$conf['urlsExcluded']) > 0 ) {
            $form['urlsExcluded'][sprintf('urlExcluded%02d', sizeof(@$conf['urlsExcluded']))] = array(
                '#type' => 'textfield',
                '#title' => t('Weitere Bereiche ausschließen'),
                '#default_value' => '',
                '#required' => FALSE,
            );
        }
        
        $form['agentIdSelection'] = array(
            '#type' => 'select',
            '#title' => t('Auswahl der Browser-ID'),
            '#options' => array(
                'Undefined' => t('Standard - Nicht spezifiziert'),
                'Chrome' => t('Google Chrome'),
                'Edge' => t('Microsoft Edge und IE'),
                'IE' => t('Microsoft IE 11'),
                'Firefox' => t('Mozilla Firefox'),
                'Safari' => t('Apple Safari'),
            ),
            '#default_value' => @$conf['agentIdSelection'] == null ? 'Chrome' : @$conf['agentIdSelection'],
            '#required' => FALSE,
            '#weight' => 65,
        );
        
        $form['deepness'] = array(
            '#type' => 'select',
            '#title' => t('Max. Verzeichnistiefe'),
            '#options' => array(
                '0' => t('keine'),
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
            ),
            '#default_value' => "keine",
            '#default_value' => @$conf['deepness'] == null ? '0' : @$conf['deepness'] == '0' ? '0' : @$conf['deepness'],
            '#weight' => 80,
            '#required' => FALSE,
        );
        
        // create an value array with non sequential integers
        # the values for the dropdown box
        $form['time_options'] = array(
            '#type' => 'value',
            '#value' => array('0' => t('keine'),
                '1' => t('1'),
                '2' => t('2'),
                '5' => t('5'),
                '10' => t('10'),
                '20' => t('20'),
                '60' => t('60'),
                '180' => t('180'),
                '600' => t('600'),
            ),
        );
        
        $form['waitSecBtRequests'] = array(
            '#type' => 'select',
            '#title' => t('Sekunden zwischen zwei Anfragen an Server'),
            '#options' => $form['time_options']['#value'],
            '#default_value' => "keine",
            '#default_value' => @$conf['waitSecBtRequests'] == null ? '0' : @$conf['waitSecBtRequests'] == '0' ? '0' : @$conf['waitSecBtRequests'],
            '#weight' => 87,
            '#required' => FALSE,
        );
        
        $form['tries'] = array(
            '#type' => 'select',
            '#title' => t('Anzahl der Versuche bei Serverfehlern'),
            '#options' => array(
                '0' => t('keine'),
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
            ),
            '#default_value' => "keine",
            '#default_value' => @$conf['tries'] == null ? '0' : @$conf['tries'] == '0' ? '0' : @$conf['tries'],
            '#weight' => 88,
            '#required' => FALSE,
        );
        
        $form['waitRetry'] = array(
            '#type' => 'select',
            '#title' => t('Pause zwischen neuen Versuchen (Sekunden)'),
            '#options' => $form['time_options']['#value'],
            '#default_value' => "keine",
            '#default_value' => @$conf['waitRetry'] == null ? '0' : @$conf['waitRetry'] == '0' ? '0' : @$conf['waitRetry'],
            '#weight' => 89,
            '#required' => FALSE,
        );
        
        $form['limitCrawlSize'] = array(
            '#type' => 'fieldset',
            '#title' => t('Maximale Größe eines Crawls'),
            '#attributes' => array('class' => array('container-inline')),
            '#weight' => 95,
        );
        $form['limitCrawlSize']['maxCrawlSize'] = array(
            '#type' => 'textfield',
            '#title' => t('Wert'),
            '#default_value' => @$conf['maxCrawlSize'],
            '#required' => FALSE,
            '#size' => 4,
        );
        $form['limitCrawlSize']['quotaUnitSelection'] = array(
            '#type' => 'select',
            '#options' => array(
                'KB' => t('Kilobyte'),
                'MB' => t('Megabyte'),
                'GB' => t('Gigabyte'),
            ),
            '#default_value' => @$conf['quotaUnitSelection'] == null ? 'Megabyte' : @$conf['quotaUnitSelection'],
            '#required' => FALSE,
        );
        
        
    }
    
    $form['save'] = array(
        '#type' => 'submit',
        '#name' => 'save',
        '#value' => t('Save'),
        '#weight' => 100,
    );
    
    $form['crawl'] = array(
        '#type' => 'submit',
        '#name' => 'crawl',
        '#value' => t('Save & Crawl'),
        '#weight' => 100,
    );
    
    _edoweb_build_breadcrumb($entity);
    return $form;
}

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

