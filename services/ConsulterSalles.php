<?php
// Projet Réservations M2L
// fichier : services/ConsulterReservations.php
// Dernière mise à jour : 21/2/2018 par Duncan

// Rôle : ce service permet à un utilisateur de consulter ses réservations à venir
// Le service web doit recevoir 3 paramètres : nom, mdp, lang
//     nom  : le nom (ou login) de connexion
//     mdp  : le mot de passe de connexion
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant la liste des réservations

// Les paramètres peuvent être passés par la méthode GET (pratique pour les tests, mais à éviter en exploitation) :
//     http://<hébergeur>/ConsulterReservations.php?nom=zenelsy&mdp=passe&lang=xml

// Les paramètres peuvent être passés par la méthode POST (à privilégier en exploitation pour la confidentialité des données) :
//     http://<hébergeur>/ConsulterReservations.php

// Récupération des données transmises
// la fonction $_GET récupère une donnée passée en paramètre dans l'URL par la méthode GET
// la fonction $_POST récupère une donnée envoyées par la méthode POST
// la fonction $_REQUEST récupère par défaut le contenu des variables $_GET, $_POST, $_COOKIE
if ( empty ($_REQUEST["nom"]) == true)  $nom = "";  else   $nom = $_REQUEST["nom"];
if ( empty ($_REQUEST["mdp"]) == true)  $mdp = "";  else   $mdp = $_REQUEST["mdp"];
if ( empty ($_REQUEST["lang"]) == true) $lang = "";  else $lang = strtolower($_REQUEST["lang"]);
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// inclusion de la classe Outils
include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

// initialisation du nombre de réservations
$nbReponses = 0;
$lesSalles = array();

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();

// Contrôle de la présence des paramètres
if ( $nom == "" || $mdp == "" )
{	$msg = "Erreur : données incomplètes.";
}
else
{	if ( $dao->getNiveauUtilisateur($nom, $mdp) == "inconnu" )
    $msg = "Erreur : authentification incorrecte.";
    else
    {	        
        // récupération de la liste des salles
        $lesSalles = $dao->getLesSalles($nom);
        $nbReponses = sizeof($lesSalles);
        
        if ($nbReponses == 0)
            $msg = "Erreur : il n'y a aucune salle";
        else
            $msg = $nbReponses . " salles disponibles en réservation.";
    }
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
        
        
        // création du flux XML en sortie
function creerFluxXML($msg, $lesSalles)
        {
            /* Exemple de code XML
            <?xml version="1.0" encoding="UTF-8"?>
            <!--Service web ConsulterSalles - BTS SIO - Lycee De La Salle - Rennes-->
            <data>
              <reponse>14 salles disponibles en réservation.</reponse>
              <donnees>
            		<salle>
            			<id>5</id>
            			<room_name>Multimédia</room_name>
            			<capacity>25</capacity>
            			<area_name>Informatique - multimédia</area_name>
            		</salle>
            		.......................................................................................
            		<salle>
            			<id>9</id>
            			<room_name>Majorelle</room_name>
            			<capacity>40</capacity>
            			<area_name>Salles de réunion</area_name>
            		</salle>
              </donnees>
            </data>

             */
            
            // crée une instance de DOMdocument (DOM : Document Object Model)
            $doc = new DOMDocument();
            
            // specifie la version et le type d'encodage
            $doc->version = '1.0';
            $doc->encoding = 'UTF-8';
            
            // crée un commentaire et l'encode en ISO
            $elt_commentaire = $doc->createComment('Service web ConsulterReservations - BTS SIO - Lycée De La Salle - Rennes');
            
            // place ce commentaire à la racine du document XML
            $doc->appendChild($elt_commentaire);
            
            // crée l'élément 'data' à la racine du document XML
            $elt_data = $doc->createElement('data');
            $doc->appendChild($elt_data);
            
            // place l'élément 'reponse' dans l'élément 'data'
            $elt_reponse = $doc->createElement('reponse', $msg);
            $elt_data->appendChild($elt_reponse);
            
            // place l'élément 'donnees' dans l'élément 'data'
            $elt_donnees = $doc->createElement('donnees');
            $elt_data->appendChild($elt_donnees);
            
            // traitement des réservations
            if (sizeof($lesSalles) > 0) {
                foreach ($lesSalles as $uneSalle)
                {
                    // crée un élément vide 'reservation'
                    $elt_salle = $doc->createElement('salle');
                    // place l'élément 'salle' dans l'élément 'donnees'
                    $elt_donnees->appendChild($elt_salle);
                    
                    // crée les éléments enfants de l'élément 'salle'
                    $elt_id         = $doc->createElement('id', $uneSalle->getId());
                    $elt_salle->appendChild($elt_id);
                    $elt_room_name  = $doc->createElement('room_name', $uneSalle->getRoom_name());
                    $elt_salle->appendChild($elt_room_name);
                    $elt_capacity = $doc->createElement('capacity',$uneSalle->getCapacity());
                    $elt_salle->appendChild($elt_capacity);
                    $elt_area_name   = $doc->createElement('area_name',$uneSalle->getArea_name());
                    $elt_salle->appendChild($elt_area_name);
                                        
                }
            }
            
            // Mise en forme finale
            $doc->formatOutput = true;
            
            // renvoie le contenu XML
            echo $doc->saveXML();
            return;
        }
        
        // création du flux JSON en sortie
        function creerFluxJSON($msg, $lesSalles)
        {
            /* Exemple de code JSON
            {
                "data": {
                    "reponse": "14 salles disponibles en r\u00e9servation.",
                    "donnees": {
                        "salle": [
                            {
                                "id": "5",
                                "room_name": "Multim\u00e9dia",
                                "capacity": "25",
                                "area_name": "Informatique - multim\u00e9dia"
                            },
            					.......................................................................................
                            {
                                "id": "9",
                                "room_name": "Majorelle",
                                "capacity": "40",
                                "area_name": "Salles de r\u00e9union"
                            }
                        ]
                    }
                }
            }
          
             */
            
            // construction d'un tableau contenant les salles
            $lesLignesDuTableau = array();
            if (sizeof($lesSalles) > 0) {
                foreach ($lesSalles as $uneSalle)
                {	// crée une ligne dans le tableau
                    $uneLigne = array();
                    $uneLigne["id"] = $uneSalle->getId();
                    $uneLigne["room_name"] = $uneSalle->getRoom_name();
                    $uneLigne["capacity"] = $uneSalle->getCapacity();
                    $uneLigne["area_name"] = $uneSalle->getArea_name();
                    
                    //ajout d'une ligne
                    $lesLignesDuTableau[] = $uneLigne;
                
                }
            }
            // construction de l'élément "reservation"
            $elt_Salle = ["salle" => $lesLignesDuTableau];
            
            // construction de l'élément "data"
            $elt_data = ["reponse" => $msg, "donnees" => $elt_Salle];
            
            // construction de la racine
            $elt_racine = ["data" => $elt_data];
            
            // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
            echo json_encode($elt_racine, JSON_PRETTY_PRINT);
            return;
        }
        ?>
