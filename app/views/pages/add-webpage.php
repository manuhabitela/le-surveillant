<?php $this->layout('layout/default') ?>

<form action="/add-webpage" method="post">
	<?php if ($this->section('form-intro')): echo $this->section('form-intro'); else: ?>
	<p>Renseignez une page web et un sélecteur CSS dont vous voulez surveiller le contenu.<br>
	Quand le contenu correspondant au sélecteur CSS donné change, le surveillant s'empresse de cafter la chose en vous envoyant un mail.</p>
	<?php endif ?>

	<div class="form-group">
		<label for="addCheckerFormName">Nom <span class="halp" title="Que surveillez-vous ?">?</span></label>
		<input type="text" name="name" required class="form-control" id="addCheckerFormName">
	</div>

	<?php if ($this->section('form-url')): echo $this->section('form-url'); else: ?>
	<div class="form-group">
		<label for="addCheckerFormURL">URL de la page</label>
		<input type="url" name="url" required class="form-control" id="addCheckerFormURL">
	</div>
	<?php endif ?>

	<?php if (!empty($cssSelector)): ?>
		<input type="hidden" name="css" value="<?php echo $cssSelector ?>" id="addCheckerFormCSS">
	<?php else: ?>
	<div class="form-group">
		<label for="addCheckerFormCSS">Sélecteur CSS du contenu à vérifier <span class="halp" title="Utiliser de préférence un sélecteur qui ne renvoie qu'un seul élément du DOM">?</span></label>
		<input type="text" name="css" required class="form-control" id="addCheckerFormCSS">
	</div>
	<?php endif ?>

	<div class="form-group">
		<label for="addCheckerFormMail">Destinataire du mail de notification</label>
		<input type="email" name="mail" required class="form-control" id="addCheckerFormMail">
	</div>

	<div class="form-group hidden addCheckerFormPreview">
		<p><i>Prévisualisation du texte à surveiller :</i></p>
		<p class="alert"></p>
	</div>

	<button type="submit" class="btn btn-default">Valider</button>
</form>
