<?php
// Projet Réservations M2L - version web mobile
// fichier : vues/VueDemanderMdp.php
// Rôle : Demander un nouveau mot de passe
// cette vue est appelée par le contôleur controleurs/CtrlDemanderMdp.php
// Création : 7/11/2017 par TheCommodor
?>
<!doctype html>
<html>
	<head>
		<?php include_once ('vues/head.php'); ?>
		
		<script>
			// associe une fonction à l'événement pageinit
			$(document).bind('pageinit', function() {
				<?php if ($typeMessage != '') { ?>
					// affiche la boîte de dialogue 'affichage_message'
					$.mobile.changePage('#affichage_message', {transition: "<?php echo $transition; ?>"});
				<?php } ?>
			} );
		</script>
	</head>
	
	<body>
		<div data-role="page" id="page_principale">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4>M2L-GRR</h4>
				<a href="index.php?action=Connecter" data-transition="<?php echo $transition; ?>">Reconnexion</a>
			</div>
			
			<div data-role="content">
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Demander un nouveau mot de passe</h4>
				<form action="index.php?action=DemanderMdp" method="post" data-ajax="false">
					<div data-role="fieldcontain" class="ui-hide-label">
						<label for="txtNom">Nom d'utilisateur :</label>
						<input type="text" name="txtNom" id="txtNom" required placeholder="Entrez votre nom" value="<?php echo $nom; ?>">
					</div>
					<div data-role="fieldcontain">
						<input type="submit" name="btnDemanderMdp" id="btnDemanderMdp" value="M'envoyer un nouveau Mot de passe" data-mini="true">
					</div>
				</form>

				<?php if($debug == true) {
					// en mise au point, on peut afficher certaines variables dans la page
					echo "<p>nom = " . $nom . "</p>";
					} ?>
				
			</div>
			
			<div data-role="footer" data-position="fixed" data-theme="<?php echo $themeNormal; ?>">
				<h4>Suivi des réservations de salles<br>Maison des ligues de Lorraine (M2L)</h4>
			</div>
		</div>
		
		<?php include_once ('vues/dialog_message.php'); ?>
		
	</body>
</html>