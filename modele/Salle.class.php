<?php
// Projet Réservations M2L - version web mobile
// fichier : modele/Salle.class.php
// Rôle : la classe Salle représente les réservations de salles
// Création : 5/11/2015 par JM CARTRON
// Mise à jour : 24/5/2016 par JM CARTRON

class Salle
{
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Membres privés de la classe ---------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    // Rappel : le temps UNIX mesure le nombre de secondes écoulées depuis le 1/1/1970
    // les types des champs timestamp, start_time et end_time découlent des types choisis pour la BDD
    private $id;			
    private $room_name;		
    private $capacity;
    private $area_name;	

    
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function Salle($unId, $unRoom_name, $unCapacity, $unArea_name) {
        $this->id = $unId;
        $this->room_name = $unRoom_name;
        $this->capacity = $unCapacity;
        $this->area_name = $unArea_name;
      
    }
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function getId()	{return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getRoom_name()	{return $this->room_name;}
    public function setRoom_name($unRoom_name) {$this->room_name = $unRoom_name;}
    
    public function getCapacity()	{return $this->capacity;}
    public function setCapacity($unCapacity) {$this->capacity = $unCapacity;}
    
    public function getArea_name()	{return $this->area_name;}
    public function setArea_name($unArea_name) {$this->area_name = $unArea_name;}
    
 
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Méthodes d'instances ----------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function toString() {
        $msg = "salle : <br>";
        $msg .= "id : " . $this->id . "<br>";
        $msg .= "room_name : " . $this->room_name . "<br>";
        $msg .= "capacity : " . $this->capacity . "<br>";
        $msg .= "area_name : " . $this->area_name . "<br>";
        return $msg;
    }
    
} // fin de la classe Salle

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!