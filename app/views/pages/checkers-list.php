<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom</th>
			<th>URL</th>
			<th>Type / Sélecteur CSS</th>
			<th>E-mail à notifier</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($checkers as $value): ?>
		<tr>
			<td><?php echo $value->name ?></td>
			<td><a href="<?php echo $value->url ?>" title="<?php echo $value->url ?>" target="_blank"><?php echo substr($value->url, 0, 20)."..." ?></a></td>
			<td><?php echo (!empty($value->type) ? $value->type : $value->css) ?></td>
			<td><a href="mailto:<?php echo $value->mail ?>" title="<?php echo $value->mail ?>" target="_blank"><?php echo substr($value->mail, 0, 20)."..." ?></a></td>
			<td>
				<form action="/delete-checker/<?php echo $value->getId() ?>" method="post">
					<input type="hidden" name="_METHOD" value="DELETE"/>
					<button class="btn btn-xs" type="submit">Supprimer</button>
				</form>
			</td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
