<?php
if ( empty ($_REQUEST["name"]) == true)  $name = "";  else   $name = $_REQUEST["name"];

// inclusion de la classe Outils
include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $name == "") {
    $msg = "Erreur : données incomplètes ou incorrectes.";
}
    else 
      if ( !$dao->existeUtilisateur($name) ) {
        $msg = "Erreur : nom d'utilisateur inexistant.";
        }
         else {
            // nouveau mdp
             $password = Outils::creerMdp();
             $dao->modifierMdpUser($name, $password);
             $dao->envoyerMdp($name, $password);
         }

// ferme la connexion à MySQL
unset($dao);

// création du flux en sortie
if ($lang == "xml")
    creerFluxXML ($msg);
    else
        creerFluxJSON ($msg);
        
        // fin du programme (pour ne pas enchainer sur la fonction qui suit)
        exit;
        
        
        // création du flux XML en sortie
        function creerFluxXML($msg)
        {
            /* Exemple de code XML
             <?xml version="1.0" encoding="UTF-8"?>
             <!--Service web CreerUtilisateur - BTS SIO - Lycée De La Salle - Rennes-->
             <data>
             <reponse>Erreur : données incomplètes ou incorrectes.</reponse>
             </data>
             */
            
            // crée une instance de DOMdocument (DOM : Document Object Model)
            $doc = new DOMDocument();
            
            // specifie la version et le type d'encodage
            $doc->version = '1.0';
            $doc->encoding = 'UTF-8';
            
            // crée un commentaire et l'encode en ISO
            $elt_commentaire = $doc->createComment('Service web DemanderMdp - BTS SIO - Lycée De La Salle - Rennes');
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
             "reponse":"Erreur : donn\u00e9es incompl\u00e8tes ou incorrectes."
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