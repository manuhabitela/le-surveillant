<?php
//méthode rache, comme dhab

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
        $document->email,
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



/*
LISTE DES SITES
 */
$app->get('/', function() use ($app, $typesCSSMap) {
    $webpages = documentsList('webpage');
    foreach ($webpages as $webpage) {
        $type = array_search($webpage->css, $typesCSSMap);
        if (!empty($type))
            $webpage->type = $type;
    }
    $app->render('pages/webpages-list', ['webpages' => $webpages]);
})->name('webpages-list');



/*
AJOUT D'UN SITE
 */
$app->get('/add-webpage(/:type)', function($type = "default") use ($app, $typesCSSMap, $typesTemplatesMap) {
    $cssSelector = $typesCSSMap[$type];
    $tpl = $typesTemplatesMap[$type];
    $app->render($tpl, ['cssSelector' => $cssSelector]);
})->name('webpages-list');

$app->post('/add-webpage', function() use ($app) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $css = filter_input(INPUT_POST, 'css', FILTER_SANITIZE_STRING);
    $url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
    $mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);

    try {
        $text = getWebpageContent($url, $css);
    } catch (Exception $e) {
        $text = $e->getMessage();
        $app->flash('danger', 'Sélecteur CSS erronné : '.$text);
        $app->redirect('/add-webpage');
    }

    addDocument([
        'name' => $name,
        'currentContent' => $text,
        'lastCheck' => date("Y/m/d H:i"),
        'css' => $css,
        'url' => $url,
        'mail' => $mail
    ], 'webpage');

    $app->flash('success', 'Check ajouté !');
    $app->redirect('/');
});



/*
SUPPRESSION D'UN SITE
 */
$app->delete('/delete-webpage/:id', function($id) use ($app) {
    $id = filter_var($id, FILTER_SANITIZE_STRING);
    $deleted = deleteDocument($id, 'webpage');
    if ($deleted)
        $app->flash('success', 'Check supprimé !');
    if (!$deleted)
        $app->flash('danger', 'Erreur lors de la suppression');
    $app->redirect('/');
});



/*
AFFICHAGE DU TEXTE TROUVÉ POUR UN SITE
 */
$app->get('/get-content', function() use ($app) {
    $css = filter_input(INPUT_GET, 'css', FILTER_SANITIZE_STRING);
    $url = filter_input(INPUT_GET, 'url', FILTER_VALIDATE_URL);

    $status = "success";
    $text = "";
    try {
        $text = getWebpageContent($url, $css);
    } catch (Exception $e) {
        $status = "error";
        $text = $e->getMessage();
    }
    return toJSON($app, ["text" => $text, "status" => $status]);
});


/*
CHECK TOUS LES SITES
 */
$app->get('/check', function() use ($app) {
    $webpages = documentsList('webpage');
    foreach ($webpages as $website) {
        checkWebpage($website);
    }
    $app->flash('success', 'Sites vérifiés : si ça a bougé, des mails ont été envoyés');
    $app->redirect('/');
});
