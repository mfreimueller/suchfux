<?php
require_once 'query.php';
require_once 'database.php';

$database = new Database();
$database->open();

$searchQueries = $database->getSearchQueries();
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Suchfux</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <table>
		<thead>
			<tr>
				<th>Suchabfrage</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($searchQueries as $query) { ?>
			<tr>
				<td><?= $query ?></td>
				<td>
					<a class="showSuggestions" data-target="<?= $query ?>" href="#">Vorschläge zeigen</a>
					<ol id="<?= $query ?>-suggestions" class="hidden">
					<?php foreach ($database->getSearchQuerySuggestions(($query))->suggestions as $suggestion) { ?>
						<li><?= $suggestion ?></li>
					<?php } ?>
					</ol>
				</td>
				<td>
					<a href="removeQuery.php?q=<?= $query ?>&r=manage.php">Löschen</a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td>
					<input type="text" id="query">
				</td>
				<td></td>
				<td>
					<a id="addQueryBtn" href="">Hinzufügen</a>
				</td>
			</tr>
		</tbody>
	</table>

	<script>
		document.getElementById("query").oninput = function() {
			document.getElementById("addQueryBtn").setAttribute("href", "addQuery.php?q=" + this.value + "&r=manage.php");
		};

		var buttons = document.getElementsByClassName("showSuggestions");
		
		for (var idx = 0; idx < buttons.length; idx++) {
			var button =buttons[idx];
			button.onclick = function() {
				var query = this.getAttribute("data-target");
				document.getElementById(query + "-suggestions").classList.remove("hidden");
				this.classList.add("hidden");
			};
		};
	</script>
</body>
</html>