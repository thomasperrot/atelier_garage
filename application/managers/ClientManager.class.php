<?php
class ClientManager
{
	private $_db;

	const ACTION_REUSSIE = 1;
	const ACTION_ECHOUEE = 0;

	public function __construct($db){$this->setDb($db);}
	
	public function setDb($db){$this->_db = $db;}

	# prend une client en argument, retourne 1
	public function add(Client $client)
	{
		$q = $this->_db->prepare('INSERT INTO client SET numero = :numero, nom = :nom, prenom = :prenom, adresse= :adresse, referent = :referent');

		$q->bindValue(':numero',$client->numero(),PDO::PARAM_INT);
		$q->bindValue(':nom',$client->nom(),PDO::PARAM_STR);
		$q->bindValue(':prenom',$client->prenom(),PDO::PARAM_STR);
		$q->bindValue(':adresse',$client->adresse(),PDO::PARAM_STR);
		$q->bindValue(':referent',$client->referent(),PDO::PARAM_STR);

		$q->execute();

		return self::ACTION_REUSSIE;
	}
	
	# retourne le nombre de clients en bdd (int)
	public function count()
	{
		return $this->_db->query('SELECT COUNT(*) FROM client')->fetchColumn();
	}
	
	# prend une client en argument, retourne 1 si l'action est réussie
	public function delete(Client $client)
	{
		$q = $this->_db->prepare('DELETE FROM client WHERE numero = :numero');
		$q->execute([':numero' => $client->numero()]);
		return self::ACTION_REUSSIE;
	}

	# prend une client en argument, retourne un booléen
	public function exists(Client $client)
	{    
		$q = $this->_db->prepare('SELECT COUNT(*) FROM client WHERE numero = :numero');
		$q->execute([':numero' => $client->numero()]);
    
		return (bool) $q->fetchColumn();
	}

  	# prend une numero en argument (string), retourne une client si il existe
	public function get($numero)
	{
		$q = $this->_db->prepare('SELECT numero, nom, prenom, adresse, referent FROM client WHERE numero = :numero');	
		$q->execute([':numero' => $numero]);

		$client = $q->fetch(PDO::FETCH_ASSOC);
		
		return empty($client) ? null : new Client($client);
	}
  
	# retourne untableau de clients
	/*public function getList($numero, $nom, $prenom, $adresse, $referent)
	{
		$clients = [];
		
		$q = $this->_db->prepare('
			SELECT vo.numero, vo.nom, vo.prenom, vo.adresse, vo.referent, vo.date_arrivee, vo.proprietaire, IFNULL(re.technicien,\'done\')
			FROM client vo LEFT JOIN repare re ON vo.numero = re.client
			WHERE vo.numero LIKE :numero
			AND vo.nom LIKE :nom
			AND vo.prenom LIKE :prenom 
			AND vo.adresse LIKE :adresse 
			AND vo.referent LIKE :referent 
			AND vo.date_arrivee LIKE :date_arrivee  
			AND vo.proprietaire LIKE :proprietaire
			AND ((re.technicien LIKE :technicien) '.$bonus.')
			ORDER BY date_arrivee
');


    		$q->bindParam(':numero', $numero, PDO::PARAM_STR);
    		$q->bindParam(':nom', $nom, PDO::PARAM_STR);
		$q->bindParam(':prenom', $prenom, PDO::PARAM_STR);
		$q->bindParam(':adresse', $adresse, PDO::PARAM_INT);
		$q->bindParam(':referent', $referent, PDO::PARAM_STR); 
		$q->bindParam(':date_arrivee', $date_arrivee,PDO::PARAM_INT);
		$q->bindParam(':proprietaire', $proprietaire, PDO::PARAM_INT);  
		$q->bindParam(':technicien', $reparateur, PDO::PARAM_INT);   
		$q->execute();
	    
		while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
		{
			$clients[] = new Client($donnees); 
		}
		return $clients;
	}*/
	
  	# prend un client en argument, retourne 1 si l'action est réussie, 0 sinon
	public function update(Client $client)
	{
		if($this->exists($client))
		{
			$q = $this->_db->prepare('UPDATE client SET nom = :nom, prenom = :prenom, adresse= :adresse, referent = :referent WHERE numero = :numero');
		    
			$q->bindValue(':nom',$client->nom(),PDO::PARAM_INT);
			$q->bindValue(':prenom',$client->prenom(),PDO::PARAM_STR);
			$q->bindValue(':adresse',$client->adresse(),PDO::PARAM_STR);
			$q->bindValue(':referent',$client->referent(),PDO::PARAM_STR);
			$q->bindValue(':numero',$client->numero(),PDO::PARAM_INT);
		    	
			$q->execute();

			return self::ACTION_REUSSIE;
		}
		else
		{
			return self::ACTION_ECHOUEE;
		}
	}
}