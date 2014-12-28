<?php
//méthode rache, comme dhab

/*
DB UTILS
 */
function addChecker($data) {
	global $dbConfig;
	$checker = new \JamesMoss\Flywheel\Document($data);
	$id = $dbConfig['checkers']->store($checker);
	return $id;
}

function updateChecker($doc) {
	global $dbConfig;
	$dbConfig['checkers']->update($doc);
}

function deleteChecker($doc) {
	global $dbConfig;
	return $dbConfig['checkers']->delete($doc);
}

function checkersList() {
	global $dbConfig;

	$checkers = $dbConfig['checkers']->query()
		->orderBy('website ASC')
		->execute();
	return $checkers;
}



/*
WEBSCRAPING UTILS
 */
$goutte = new Goutte\Client();

function getContent($url, $css) {
	global $goutte;
	$website = $goutte->request('GET', $url);
	return $website->filter($css)->text();
}

/*
vérifie si du nouveau contenu est présent pour le document donné
 */
function check($document) {
	$newContent = getContent($document->url, $document->css);
	if ($newContent !== $document->currentContent) {
		notifyChange($document);

		$document->currentContent = $newContent;
	}

	$document->lastCheck = date("Y/m/d H:i");
	updateChecker($document);
}

$typesCSSMap = [
    'default' => '',
    'leboncoin' => '.list-lbc .detail .title'
];

$typesTemplatesMap = [
    'default' => 'pages/add-checker',
    'leboncoin' => 'pages/add-checker-leboncoin'
];



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



/*
LISTE DES SITES
 */
$app->get('/', function() use ($app, $typesCSSMap) {
	$checkers = checkersList();
    foreach ($checkers as $checker) {
        $type = array_search($checker->css, $typesCSSMap);
        if (!empty($type))
            $checker->type = $type;
    }
	$app->render('pages/checkers-list', ['checkers' => $checkers]);
})->name('checkers-list');



/*
AJOUT D'UN SITE
 */
$app->get('/add-checker(/:type)', function($type = "default") use ($app, $typesCSSMap, $typesTemplatesMap) {
	$cssSelector = $typesCSSMap[$type];
	$tpl = $typesTemplatesMap[$type];
	$app->render($tpl, ['cssSelector' => $cssSelector]);
})->name('checkers-list');

$app->post('/add-checker', function() use ($app) {
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$css = filter_input(INPUT_POST, 'css', FILTER_SANITIZE_STRING);
	$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
	$mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);

	try {
		$text = getContent($url, $css);
	} catch (Exception $e) {
		$text = $e->getMessage();
		$app->flash('danger', 'Sélecteur CSS erronné : '.$text);
		$app->redirect('/add-checker');
	}

	addChecker([
		'name' => $name,
		'currentContent' => $text,
		'lastCheck' => date("Y/m/d H:i"),
		'css' => $css,
		'url' => $url,
		'mail' => $mail]);

	$app->flash('success', 'Check ajouté !');
	$app->redirect('/');
});



/*
SUPPRESSION D'UN SITE
 */
$app->delete('/delete-checker/:id', function($id) use ($app) {
	$id = filter_var($id, FILTER_SANITIZE_STRING);
	$deleted = deleteChecker($id);
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
		$text = getContent($url, $css);
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
	$checkers = checkersList();
	foreach ($checkers as $website) {
		check($website);
	}
	$app->flash('success', 'Sites vérifiés : si ça a bougé, des mails ont été envoyés');
	$app->redirect('/');
});
