<?php $this->layout('pages/add-checker', ['cssSelector' => $cssSelector]) ?>

<?php $this->start('form-intro') ?>
<p>Renseignez l'URL d'une page de recherche leboncoin pour surveiller les annonces.<br>
Quand le système détecte une nouvelle annonce, vous recevez un mail pour vous notifier.</p>
<?php $this->stop('form-intro') ?>

<?php $this->start('form-url') ?>
<div class="form-group">
	<label for="addCheckerFormURL">URL complète de la recherche <span class="halp" title="Copiez bien l'url complète avec http://www.leboncoin.fr dedans">?</span></label>
	<input type="url" name="url" required class="form-control" id="addCheckerFormURL">
</div>
<?php $this->stop('form-url') ?>