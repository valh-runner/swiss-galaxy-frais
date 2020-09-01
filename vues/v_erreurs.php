<div class="erreur">
	<ul>
		<?php
		if (!empty($_SESSION['erreurs'])) {
			foreach ($_SESSION['erreurs'] as $erreur) {
				echo "<li>$erreur</li>";
			}
			unset($_SESSION['erreurs']); //suppression erreurs affichées
		}
		?>
	</ul>
</div>