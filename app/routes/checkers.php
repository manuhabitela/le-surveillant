<?php
function checkersList() {
	global $dbConfig;

	$checkers = $dbConfig['checkers']->query()
		->orderBy('website ASC')
		->execute();
	return $checkers;
}

$app->get('/', function() use ($app) {
	$checkers = checkersList();
	$app->render('pages/home', ['checkers' => $checkers]);
})->name('home');