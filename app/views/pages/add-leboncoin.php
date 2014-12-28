<?php $this->layout('pages/add-webpage', ['cssSelector' => $cssSelector]) ?>

<?php $this->start('form-intro') ?>
<p>Renseignez l'URL d'une page de recherche leboncoin pour surveiller l'arrivée de nouvelles annonces.<br>
Quand le surveillant voit des petits nouveaux dans le coin, il vous enverra un mail.</p>
<?php $this->stop('form-intro') ?>

<?php $this->start('form-url') ?>
<div class="form-group">
	<label for="addCheckerFormURL">URL complète de la recherche <span class="halp" title="Copiez bien l'url complète avec http://www.leboncoin.fr dedans">?</span></label>
	<input type="url" name="url" required class="form-control" id="addCheckerFormURL">
</div>
<?php $this->stop('form-url') ?>
