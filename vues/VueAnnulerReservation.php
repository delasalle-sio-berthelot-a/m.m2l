<?php
// Projet Réservations M2L - version web mobile
// fichier : vues/VueConsulterReservations.php
// Rôle : visualiser la liste des réservations à venir d'un utilisateur
// cette vue est appelée par le contôleur controleurs/CtrlConsulterReservations.php
// Création : 12/10/2015 par JM CARTRON
// Mise à jour : 31/5/2016 par JM CARTRON
?>
<!doctype html>
<html>
	<head>
		<?php include_once ('vues/head.php'); ?>
	</head>
	 
	<body>
		<div data-role="page">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4>M2L-GRR</h4>
				<a href="index.php?action=Menu" data-transition="<?php echo $transition; ?>">Retour menu</a>
			</div>
			<div data-role="content">
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Annuler une réservation</h4>
				
			
				<input type="text" name="txtNom" id="txtNom" data-mini="true" placeholder="Entrez le numéro de reservation">
		
				<input type="submit" name="btnConnecter" id="btnConnecter" data-mini="true" data-ajax="false" value="Annuler la réservation">
				
			</div>
			<div data-role="footer" data-position="fixed" data-theme="<?php echo $themeNormal;?>">
				<h4>Suivi des réservations de salles<br>Maison des ligues de Lorraine (M2L)</h4>
			</div>
		</div>
		
	</body>
</html>