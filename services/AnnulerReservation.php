<?php
// Projet Réservations M2L
// fichier : services/AnnulerReservation.php
// Dernière mise à jour : 21/2/2018 par theCommodor

//Rôle : ce service web permet à un utilisateur autorisé d'annuler une réservation (à condition d'être l'auteur de la réservation).

//Paramètres à fournir :
//•	le nom (ou login) de l'utilisateur
//•	le mot de passe de l'utilisateur
//•	le numéro de la réservation à annuler

//Description du traitement :
//•	Vérifier que les données transmises sont complètes et sous une forme correcte
//•	Vérifier l'authentification de l'utilisateur
//•	Vérifiera que le numéro de réservation existe
//•	Vérifier que l'utilisateur est bien l'auteur de la réservation
//•	Vérifier que la date de début de réservation est bien supérieure à la date du jour.
//•	Le service confirmera la suppression de la réservation par l'envoi d'un mail à l'utilisateur.


// Les paramètres peuvent être passés par la méthode GET (pratique pour les tests, mais à éviter en exploitation) :
//    http://localhost/reservations/AnnulerReservation.php?nom=zenelsy&mdp=passe&numreservation=2


// Les paramètres peuvent être passés par la méthode POST (à privilégier en exploitation pour la confidentialité des données) :
//    http://localhost/reservations/AnnulerReservation.php

// Récupération des données transmises
// la fonction $_GET récupère une donnée passée en paramètre dans l'URL par la méthode GET
// la fonction $_POST récupère une donnée envoyées par la méthode POST
// la fonction $_REQUEST récupère par défaut le contenu des variables $_GET, $_POST, $_COOKIE

if ( empty ($_REQUEST["nom"]) == true)  $nom = "";  else   $nom = $_REQUEST["nom"];
if ( empty ($_REQUEST["mdp"]) == true)  $mdp = "";  else   $mdp = $_REQUEST["mdp"];
if ( empty ($_REQUEST["numreservation"]) == true)  $numreservation = "";  else   $numreservation = $_REQUEST["numreservation"];
if ( empty ($_REQUEST["lang"]) == true) $lang = "";  else $lang = strtolower($_REQUEST["lang"]);
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";


// inclusion de la classe Outils
include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $nom == "" || $mdp == "" || $numreservation == ""  ) {
    $msg = "Erreur : données incomplètes ou incorrectes.";
}
else {
    if ( $dao->getNiveauUtilisateur($nom, $mdp) == "inconnu")  {
                $msg = "Erreur : authentification incorrecte.";
            }
            else {
                if ($dao->getReservation($numreservation) == "inconnu"){
                    $msg = "Erreur : numéro de réservation inexistant.";
                }
                else{
                    if( $dao->aPasseDesReservations($nom) == "false"){
                        $msg = "Erreur : vous n'êtes pas l'auteur de cette réservation.";
                    }
                    else {
                        if ( $dao->getReservation($idReservation)->getEnd_time() <= time() ) {
                            $msg = "Erreur : cette réservation est déjà passée.";
                        }
                        else {
                            $sujet = "Annulation de votre réservation M2L";
                            $contenuMail = "Votre réservation a bien été annulée";
                            $email = $dao->getUtilisateur($nom)->getEmail();
                            
                            $ok = Outils::envoyerMail($email, $sujet, $contenuMail, $ADR_MAIL_EMETTEUR);
                            if ( ! $ok ) {
                                // l'envoi de mail a échoué
                                $msg = "Enregistrement effectué ; l'envoi du mail à l'utilisateur a rencontré un problème.";
                            }
                            else {
                                // tout a bien fonctionné
                                $msg = "Enregistrement effectué ; un mail va être envoyé à l'utilisateur.";
                        }
                    }
                }
                }
            }
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
    $elt_commentaire = $doc->createComment('Service web CreerUtilisateur - BTS SIO - Lycée De La Salle - Rennes');
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