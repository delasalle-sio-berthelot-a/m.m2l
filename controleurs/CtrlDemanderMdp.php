<?php
if( ! isset($_POST["txtNom"])){
    
    $nom ='';
    $message='';
    $typeMessage ='';
    $themeFooter = $themeNormal;
    
    include_once('vues/VueDemanderMdp.php');
}
else {
    if (empty($_POST["txtNom"]) == true ) $nom ="";  else  $nom = $_POST["txtNom"];
    
    if ($nom == ""){
        //si les données sont incorrectes
        $message = 'Données incomplètes ou incorrectes !';
        $typeMessage = 'avertissement';
        $themeFooter = $themeProbleme;
        include_once ('vues/VueDemanderMdp.php');
    }
    else {
        //classe Outils 
        include_once ('modele/Outils.class.php');
        //connection vers database
        include_once ('modele/DAO.class.php');
        $dao = new DAO();
        
        $existeUser = $dao->existeUtilisateur($nom);
        
        if($existeUser == false) {
            
            $message = "Nom d'utilisateur inexistant !";
            $typeMessage = 'avertissement';
            $themeFooter = $themeProbleme;
            include_once ('vues/VueDemanderMdp.php');
        }
        else {
            $nouveauMdp = Outils::creerMdp();
            
            $dao->modifierMdpUser($nom, $nouveauMdp);
            
            $ok = $dao->envoyerMdp($nom, $nouveauMdp);
            
            if ( $ok ) {
                $message = "Enregistrement effectué.<br> Vous allez recevoir un mail de confirmation.";
                $typeMessage = 'information';
                $themeFooter = $themeNormal;
            }
            else {
                $message = "Enregistrement effectué.<br>L'envoi du mail de confirmation a rencontré un problème.";
                $typeMessage = 'avertissement';
                $themeFooter = $themeProbleme;
            }
            unset($dao);
            include_once ('vues/VueDemanderMdp.php');
        }
    }
}