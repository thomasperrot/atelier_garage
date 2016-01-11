<?php
class VoitureManager
{
	private $_db;

	const ACTION_REUSSIE = 1;
	const ACTION_ECHOUEE = 0;

	public function __construct($db){$this->setDb($db);}
	
	public function setDb($db){$this->_db = $db;}

	public function add(Voiture $voiture)
	{
		$q = $this->_db->prepare('INSERT INTO voiture SET immatriculation = :immatriculation, marque = :marque, type = :type, annee= :annee, kilometrage = :kilometrage, date_arrivee = :date_arrivee, proprietaire = :proprietaire');

		$q->bindValue(':immatriculation',$voiture->immatriculation(),PDO::PARAM_STR);
		$q->bindValue(':marque',$voiture->marque(),PDO::PARAM_STR);
		$q->bindValue(':type',$voiture->type(),PDO::PARAM_STR);
		$q->bindValue(':annee',$voiture->annee(),PDO::PARAM_INT);
		$q->bindValue(':kilometrage',$voiture->kilometrage(),PDO::PARAM_INT);
		$q->bindValue(':date_arrivee',$voiture->date_arrivee(),PDO::PARAM_INT);
		$q->bindValue(':proprietaire',$voiture->proprietaire(),PDO::PARAM_INT);
		$q->execute();
		return self::ACTION_REUSSIE;
	}

	public function count()
	{
		return $this->_db->query('SELECT COUNT(*) FROM voiture')->fetchColumn();
	}

	public function delete(Voiture $voiture)
	{
		$q = $this->_db->prepare('DELETE FROM voiture WHERE immatriculation = :immatriculation');
		$q->execute([':immatriculation' => $voiture->immatriculation()]);
		return self::ACTION_REUSSIE;
	}

	public function exists(Voiture $voiture)
	{    
		$q = $this->_db->prepare('SELECT COUNT(*) FROM voiture WHERE immatriculation = :immatriculation');
		$q->execute([':immatriculation' => $voiture->immatriculation()]);
    
		return (bool) $q->fetchColumn();
	}

  
	public function get($immatriculation)
	{
		$q = $this->_db->prepare('SELECT immatriculation, marque, type, annee, kilometrage, date_arrivee, proprietaire FROM voiture WHERE immatriculation = :immatriculation');	
		$q->execute([':immatriculation' => $immatriculation]);

		$voiture = $q->fetch(PDO::FETCH_ASSOC);
		
		return new Voiture($voiture);
	}
  
	public function getList($immatriculation, $marque, $type, $annee, $kilometrage, $date_arrivee, $proprietaire, $reparateur)
	{
		$voitures = [];
		
		$bonus = ($reparateur == '%') ? 'OR (re.technicien IS NULL)' : '';
		
		$q = $this->_db->prepare('
			SELECT vo.immatriculation, vo.marque, vo.type, vo.annee, vo.kilometrage, vo.date_arrivee, vo.proprietaire, IFNULL(re.technicien,\'done\')
			FROM voiture vo LEFT JOIN repare re ON vo.immatriculation = re.voiture
			WHERE vo.immatriculation LIKE :immatriculation
			AND vo.marque LIKE :marque
			AND vo.type LIKE :type 
			AND vo.annee LIKE :annee 
			AND vo.kilometrage LIKE :kilometrage 
			AND vo.date_arrivee LIKE :date_arrivee  
			AND vo.proprietaire LIKE :proprietaire
			AND ((re.technicien LIKE :technicien) '.$bonus.')
			ORDER BY date_arrivee
');


    		$q->bindParam(':immatriculation', $immatriculation, PDO::PARAM_STR);
    		$q->bindParam(':marque', $marque, PDO::PARAM_STR);
		$q->bindParam(':type', $type, PDO::PARAM_STR);
		$q->bindParam(':annee', $annee, PDO::PARAM_INT);
		$q->bindParam(':kilometrage', $kilometrage, PDO::PARAM_STR); 
		$q->bindParam(':date_arrivee', $date_arrivee,PDO::PARAM_INT);
		$q->bindParam(':proprietaire', $proprietaire, PDO::PARAM_INT);  
		$q->bindParam(':technicien', $reparateur, PDO::PARAM_INT);   
		$q->execute();
	    
		while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
		{
			$voitures[] = new Voiture($donnees); 
		}
		return $voitures;
	}
	
  
	public function update(Voiture $voiture)
	{
		if($this->exists($voiture))
		{
			$q = $this->_db->prepare('UPDATE voiture SET marque = :marque, type = :type, annee= :annee, kilometrage = :kilometrage, date_arrivee = :date_arrivee, proprietaire = :proprietaire WHERE immatriculation = :immatriculation');
		    
			$q->bindValue(':marque',$voiture->marque(),PDO::PARAM_STR);
			$q->bindValue(':type',$voiture->type(),PDO::PARAM_STR);
			$q->bindValue(':annee',$voiture->annee(),PDO::PARAM_INT);
			$q->bindValue(':kilometrage',$voiture->kilometrage(),PDO::PARAM_STR);
			$q->bindValue(':date_arrivee',$voiture->date_arrivee(),PDO::PARAM_INT);
			$q->bindValue(':proprietaire',$voiture->proprietaire(),PDO::PARAM_INT);
			$q->bindValue(':immatriculation',$voiture->immatriculation(),PDO::PARAM_STR);
		    
			$q->execute();
			return self::ACTION_REUSSIE;
		}
		else
		{
			return self::ACTION_ECHOUEE;
		}
	}
}