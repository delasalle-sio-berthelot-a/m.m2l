<?php

// Récupération des données transmises
// la fonction $_GET récupère une donnée passée en paramètre dans l'URL par la méthode GET
if ( empty ($_GET ["nomAdmin"]) == true)  $nomAdmin = "";  else   $nomAdmin = $_GET ["nomAdmin"];
if ( empty ($_GET ["mdpAdmin"]) == true)  $mdpAdmin = "";  else   $mdpAdmin = $_GET ["mdpAdmin"];
if ( empty ($_GET ["nomUser"]) == true)   $nomUser = "";  else   $nomUser = $_GET ["nomUser"];

if ( empty ($_REQUEST["nomAdmin"]) == true)  $nomAdmin = "";  else   $nomAdmin = $_REQUEST["nomAdmin"];
if ( empty ($_REQUEST["mdpAdmin"]) == true)  $mdpAdmin = "";  else   $mdpAdmin = $_REQUEST["mdpAdmin"];
if ( empty ($_REQUEST["nomUser"]) == true)  $nomUser = "";  else   $nomUser = $_REQUEST["nomUser"];


include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $nomAdmin == "" || $mdpAdmin == "" || $nomUser = "") 
    $msg = "Erreur : données incomplètes ou incorrectes.";

else 
    if ( $dao->getNiveauUtilisateur($nomAdmin, $mdpAdmin) != "administrateur") 
        $msg = "Erreur : authentification incorrecte.";
              
            else 
                if(!$dao->existeUtilisateur($nomUser))
                    $msg = "Erreur : nom d'utilisateur inexistant.";
                   
                else
                    $dao->getUtilisateur($nom);
                     $suppr = $dao->supprimerUtilisateur($nomUser);
                         if (!$suppr)
                         {
                             $user=$dao->getUtilisateur($nomUser);
                             $email=$user->getEmail();
                             $sujet = "Suppression de votre compte dans le système de réservation de M2L";
                             $contenuMail = "L'administrateur du système de réservations de la M2L vient de vous créer un compte utilisateur.\n\n";
                             $contenuMail .= "Les données enregistrées sont :\n\n";
                             $contenuMail .= "Votre nom : " . $name . "\n";
                             $contenuMail .= "Votre mot de passe : " . $password . " (nous vous conseillons de le changer lors de la première connexion)\n";
                             $contenuMail .= "Votre niveau d'accès (0 : invité    1 : utilisateur    2 : administrateur) : " . $level . "\n";
                             $ok = Outils::envoyerMail($email, $sujet, $contenuMail, $ADR_MAIL_EMETTEUR);
                             if(!$ok)
                             $msg = "Suppression effectué ; l'envoi du mail à l'utilisateur a rencontré un problème";
                             else 
                             $msg = "Suppression effectué ; un mail va être envoyé à l'utilisateur."; 
                         }
                         else
                             $msg="Impossible de supprimer l'utilisateur";
          
  return $msg;
  // ferme la connexion à MySQL
  unset($dao);
  
  // création du flux en sortie
  if ($lang == "xml")
      creerFluxXML ($msg, $lesSalles);
      else
          creerFluxJSON ($msg, $lesSalles);
          
          // fin du programme (pour ne pas enchainer sur la fonction qui suit)
          exit;
          
          // création du flux XML en sortie
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
              $elt_commentaire = $doc->createComment('Service web supprimerUtilisateur - BTS SIO - Lycée De La Salle - Rennes');
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