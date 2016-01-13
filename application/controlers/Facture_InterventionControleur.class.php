﻿<?php
class Facture_InterventionControleur{
	
	private $_facture_interventionManager;
		
	public function __construct(Facture_InterventionManager $facture_interventionManager){
		$this->_facture_interventionManager=$facture_interventionManager;
	}
	
	public function get($idFacture, $idIntervention){
		return $this->_facture_interventionManager->get($idFacture, $idIntervention);
	}
	
	public function getList(){
		$idFacture = '%';
		if (!empty($_POST['idFacture'])) {$idFacture.=$_POST['idFacture'].'%';}
		
		$idIntervention = '%';
		if (!empty($_POST['idIntervention'])) {$idIntervention.=$_POST['idIntervention'].'%';}
					
		$liste_factures_detail = $this->_facture_interventionManager->getList($idFacture);
		return $liste_factures_detail;
	}
	
	public function addFacture_Intervention(){
		$out='';
		if (!empty($_POST['idFacture']) AND !empty($_POST['idIntervention']) ) {
			$facture_intervention = new Facture_Intervention($_POST);
			
			if (!$this->_facture_interventionManager->exists($facture_intervention)) {
				if($this->_facture_interventionManager->add($facture_intervention)){
					$out='La facture_intervention entre '.$_POST['idFacture'].' et '.$_POST['idIntervention'].' a bien été ajoutée.';
				}else{
					$out='OUPS ! Il y a eu un problème.'; 
				}
			} else {
				$out='Erreur : ce couple est déjà pris ! ';
			}
		}else{
			$out='Erreur : vous ne devriez pas être ici !';
		}
		return $out;
	}
		
	public function deleteFacture_Intervention($facture_intervention){
		return ($this->_facture_interventionManager->delete($facture_intervention))?'Le facture_intervention immatriculée '.$facture_intervention->numero().' a bien été supprimé.':'OUPS ! Il y a eu un problème.'; 
	}
	
}
?>