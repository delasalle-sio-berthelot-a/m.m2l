<?php
if ( empty ($_REQUEST["nom"]) == true)  $nom = "";  else   $nom = $_REQUEST["nom"];
if ( empty ($_REQUEST["mdp"]) == true)  $mdp = "";  else   $mdp = $_REQUEST["mdp"];
if ( empty ($_REQUEST["n1mdp"]) == true)  $n1mdp = "";  else   $n1mdp = $_REQUEST["n1mdp"];
if ( empty ($_REQUEST["n2mdp"]) == true)  $n2mdp = "";  else   $n2mdp = $_REQUEST["n2mdp"];
if ( empty ($_REQUEST["lang"]) == true) $lang = "";  else $lang = strtolower($_REQUEST["lang"]);
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $nom == "" || $mdp== "" || $n1mdp = "" || $n2mdp = "") {
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
             $msg = "Suppression  effectuée ; l'envoi du mail à l'utilisateur a rencontré un problème.";
        }    
    }
    
   return $msg;
}

// ferme la connexion à MySQL
unset($dao);

// création du flux en sortie
if ($lang == "xml")
    creerFluxXML ($msg, $lesSalles);
    else
        creerFluxJSON ($msg, $lesSalles);
        
        // fin du programme (pour ne pas enchainer sur la fonction qui suit)
        exit;
        
        function creerFluxXML($msg)
        {
            /* Exemple de code XML
             <?xml version="1.0" encoding="UTF-8"?>
             <!--Service web Connecter - BTS SIO - Lycée De La Salle - Rennes-->
             <data>
             <reponse>Utilisateur authentifié.</reponse>
             </data>
             */
            
            // crée une instance de DOMdocument (DOM : Document Object Model)
            $doc = new DOMDocument();
            
            // specifie la version et le type d'encodage
            $doc->version = '1.0';
            $doc->encoding = 'UTF-8';
            
            // crée un commentaire et l'encode en ISO
            $elt_commentaire = $doc->createComment('Service web changerDeMdp - BTS SIO - Lycée De La Salle - Rennes');
            // place ce commentaire à la racine du document XML
            $doc->appendChild($elt_commentaire);
            
            // crée l'élément 'data' à la racine du document XML
            $elt_data = $doc->createElement('data');
            $doc->appendChild($elt_data);
            
            // place l'élément 'reponse' juste après l'élément 'data'
            $elt_reponse = $doc->createElement('reponse', $msg);
            $elt_data->appendChild($elt_reponse);
            
            // Mise en forme finale
            $doc->formatOutput = true;
            
            // renvoie le contenu XML
            echo $doc->saveXML();
            return;
        }
        
        // création du flux JSON en sortie
        function creerFluxJSON($msg)
        {
            /* Exemple de code JSON
             {
             "data":{
             "reponse":"Utilisateur authentifi\u00e9."
             }
             }
             */
            
            // construction de l'élément "data"
            $elt_data = ["reponse" => $msg];
            
            // construction de la racine
            $elt_racine = ["data" => $elt_data];
            
            // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
            echo json_encode($elt_racine, JSON_PRETTY_PRINT);
            return;
        }
?>