<?php

function edoweb_basic_browse($jsonld_property)
{
    $api = new EdowebAPIClient();
    $htmlString = $api->getBrowsingPage($jsonld_property);
    return $htmlString;
}
