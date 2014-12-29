<?php
//méthode rache, comme dhab

require_once LIB_PATH.'/common.php';

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
