<p>Le surveillant est à l'affût. Si une page web bouge, il le voit et fait immédiatement son rapport à qui le veut. Ouais, c'est une balance et puis quoi y'a un problème tu veux t'la met' ?</p>

<?php if (count($webpages)): ?>
<p>Voici la liste des pages qu'il surveille :</p>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom</th>
			<th>URL</th>
			<th class="hidden-xs">Type / Sélecteur CSS</th>
			<th class="hidden-xs">E-mail à notifier</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($webpages as $value): ?>
		<tr>
			<td><?php echo $value->name ?></td>
			<td><a href="<?php echo $value->url ?>" title="<?php echo $value->url ?>" target="_blank"><?php echo substr($value->url, 0, 20)."..." ?></a></td>
			<td class="hidden-xs"><?php echo (!empty($value->type) ? $value->type : $value->css) ?></td>
			<td class="hidden-xs"><a href="mailto:<?php echo $value->mail ?>" title="<?php echo $value->mail ?>" target="_blank"><?php echo substr($value->mail, 0, 20)."..." ?></a></td>
			<td>
				<form action="/delete-webpage/<?php echo $value->getId() ?>" method="post">
					<input type="hidden" name="_METHOD" value="DELETE"/>
					<button class="btn btn-xs" type="submit">Supprimer</button>
				</form>
			</td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>
