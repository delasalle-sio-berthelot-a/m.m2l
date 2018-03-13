
<?php
include_once ('modele/DAO.class.php');
$dao = new DAO();
if ( $_SESSION['niveauUtilisateur'] != 'utilisateur' && $_SESSION['niveauUtilisateur'] != 'administrateur') {
	// si le demandeur n'est pas authentifié, il s'agit d'une tentative d'accès frauduleux
	// dans ce cas, on provoque une redirection vers la page de connexion
	header ("Location: index.php?action=Deconnecter");
}
else {
	
	if ( ! isset ($_POST ["txtReservation"]) == true) {
	
		$idReservation = '';
		$message = '';
		$typeMessage = '';			// 2 valeurs possibles : 'information' ou 'avertissement'
		$themeFooter = $themeNormal;
		include_once ('vues/VueAnnulerReservation.php');
	}
	
	else
	{
	    // récupération des données postées
	    if ( empty ($_POST ["txtReservation"]) == true)  $idReservation = "";  else   $idReservation = $_POST ["txtReservation"];
		$nomUtilisateur = $_SESSION['nom'];
	
	
	   // mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
		if ( ! $dao->existeReservation($idReservation)){
			$message = "Numéro de réservation inexistant !";
			$typeMessage = 'avertissement';
			$themeFooter = $themeNormal;
			include_once ('vues/VueAnnulerReservation.php');
		}
		else {
			$laReservation = $dao->getReservation($idReservation);
			//echo $Reservation->getEnd_time();
			$laDateReservation = $laReservation->getEnd_time();
			
			if ($laDateReservation <= time()){
				$message = "Cette réservation est déjà passée !";
				$typeMessage = 'avertissement';
				$themeFooter = $themeNormal;
				include_once ('vues/VueAnnulerReservation.php');
			}
			else {
				// On teste si l'utilisateur est le créateur de la réservation
				if ( ! $dao->estLeCreateur($nomUtilisateur, $idReservation)){
					$message = "Vous n'êtes pas l'auteur de cette réservation !";
					$typeMessage = 'avertissement';
					$themeFooter = $themeNormal;
					include_once ('vues/VueAnnulerReservation.php');
				}
			
				else {
				  
				    $adrMail = $dao->getUtilisateur($nomUtilisateur)->getEmail();
					$sujet = "Annuler réservation";
					$contenuMail = "Vous avez bien annulé votre réservation";
					
		            $ok = $dao->annulerReservation($idReservation);
		            if( $ok == false ){
		              
		              $message = "Echec de l'annulation !";
		              $typeMessage = 'avertissement';
		              $themeFooter = $themeNormal;
		              include_once ('vues/VueAnnulerReservation.php');
		              
		            }
				    else
				    {
    					$ok = Outils::envoyerMail($adrMail, $sujet, $contenuMail, $ADR_MAIL_EMETTEUR);
    					if ( ! $ok ) {
    					    // si l'envoi de mail a échoué, réaffichage de la vue avec un message explicatif
    					    $message = "Enregistrement effectué.<br>L'envoi du mail à l'utilisateur a rencontré un problème !";
    					    $typeMessage = 'avertissement';
    					    $themeFooter = $themeProbleme;
    					    include_once ('vues/VueAnnulerReservation.php');
    					}
    					else {
    					    // tout a fonctionné
    					    $message = "Enregistrement effectué.<br>Un mail va être envoyé à l'utilisateur !";
    					    $typeMessage = 'information';
    					    $themeFooter = $themeNormal;
    					    include_once ('vues/VueAnnulerReservation.php');
    					   }
				    }
				}
			}
			
			}
			}
}
