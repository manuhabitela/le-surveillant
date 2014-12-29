<?php

/*
DB UTILS
 */
function addDocument($data, $type) {
    global $dbConfig;
    $doc = new \JamesMoss\Flywheel\Document($data);
    $doc->created = date("Y-m-d H:i");
    $id = $dbConfig[$type]->store($doc);
    return $id;
}

function updateDocument($doc, $type) {
    global $dbConfig;
    $doc->modified = date("Y-m-d H:i");
    $dbConfig[$type]->update($doc);
}

function deleteDocument($doc, $type) {
    global $dbConfig;
    return $dbConfig[$type]->delete($doc);
}

function documentsList($type) {
    global $dbConfig;
    $docs = $dbConfig[$type]->query()
        ->orderBy('name ASC')
        ->execute();
    return $docs;
}



/*
WEBSCRAPING UTILS
 */
$goutte = new Goutte\Client();
function getWebpageContent($url, $css) {
    global $goutte;
    $website = $goutte->request('GET', $url);
    return $website->filter($css)->text();
}

/*
vérifie si du nouveau contenu est présent pour le document donné
 */
function checkWebpage($document) {
    $newContent = getWebpageContent($document->url, $document->css);
    if ($newContent !== $document->currentContent) {
        notifyChange($document);

        $document->currentContent = $newContent;
    }

    updateDocument($document, 'webpage');
}



/*
OTHER UTILS
 */
function notifyChange($document) {
    return mail(
        $document->mail,
        "Nouveau contenu pour la recherche *".$document->name."*",
        "Aller voir le nouveau contenu : ".$document->url
    );
}

function toJSON($app, $content) {
    $response = $app->response;
    $response['Content-Type'] = 'application/json';
    $response->body( json_encode($content) );
}

$typesCSSMap = [
    'default' => '',
    'leboncoin' => '.list-lbc .detail .title'
];

$typesTemplatesMap = [
    'default' => 'pages/add-webpage',
    'leboncoin' => 'pages/add-leboncoin'
];
