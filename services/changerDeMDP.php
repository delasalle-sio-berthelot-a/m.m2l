<?php
if ( empty ($_REQUEST["nom"]) == true)  $nom = "";  else   $nom = $_REQUEST["nom"];
if ( empty ($_REQUEST["mdp"]) == true)  $mdp = "";  else   $mdp = $_REQUEST["mdp"];
if ( empty ($_REQUEST["n1mdp"]) == true)  $n1mdp = "";  else   $n1mdp = $_REQUEST["n1mdp"];
if ( empty ($_REQUEST["n2mdp"]) == true)  $n2mdp = "";  else   $n2mdp = $_REQUEST["n2mdp"];

include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $nom == "" || $mdp== "") {
    $msg = "Erreur : données incomplètes ou incorrectes.";
}
else {
    if ( $dao->getNiveauUtilisateur($nom, $mdp) != "administrateur" ) {
        $msg = "Erreur : authentification incorrecte.";
    }
    else {
        if(!$n1mdp == $n2mdp){
            $msg = "Erreur : le nouveau mot de passe et sa confirmation sont différents.";
        }
        else if ($n1mdp == $n2mdp){
            $msg = "Enregistrement effectué ; vous allez recevoir un mail de confirmation.";
            $dao->envoyerMdp($unUtilisateur,$newPassword);
        }
        else {
            $msg = "Enregistrement effectué ; il y a un problème...";
        }    }
        return $msg;
}