<?php
// Redirige l'utilisateur s'il est déjà identifié
if(!isset($_COOKIE["ID_UTILISATEUR"])){	
      header("Location: ../index.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
     // Une fois le formulaire envoyé     
     if(isset($_POST["valid"])){                               
        // Connexion à la base de données
        connectSQL();          	
		include_once("../include/protect_var.php");                          
		$reqContrat="SELECT a.*,a.id as id_s,b.*,b.id as id_c FROM contrat a, salaire b WHERE a.id='$_POST[id_contrat]' AND b.id='$_POST[id]'";
		$resContrat=mysql_fetch_array(mysql_query($reqContrat));		
		/***Salaire mensualisé***/
		$nb_heure_mois=($resContrat['nb_sem_travail']/12)*$resContrat['heures_gardes'];
		$salaireBase=$nb_heure_mois*$resContrat['salaire_horaire_net'];
		$salaire=$salaireBase;				
		$salaireComp=$nbHeuresComp*$resContrat['salaire_heure_comp'];//heures complémentaires
		$salaire+=$salaireComp;
		$salaireSupp=$nbHeuresSupp*$resContrat['salaire_heure_supp'];//heures supplémentaires		
		$salaire+=$salaireSupp;
		/********************/
		/* Année incomplète */
		/********************/
		
		/***Congés payés***/
		//$resContrat['nb_sem_travail']=37;
		$nbConges=ceil(($resContrat['nb_sem_travail']/4)*2.5);
		$nbMois=ceil($resContrat['nb_sem_travail']/4);				
		/*4 = nombre de semaines par mois
		2.5 = 2, 5 jours ouvrables de congés par mois travaillé
		arrondi au supérieur*/
		if ($resContrat['conges_payes']==1){//10% mensuel
			$cas1=round((($salaire*$nbConges)/26)/12,2);//26 = nombre de jours ouvrables en moyenne dans un mois
			$cas2=round($salaire*0.1,2);
			if($cas1>$cas2){
				$congesPayes=$cas1;
			}else{$congesPayes=$cas2;
			}
		}elseif ($resContrat['conges_payes']==2){//En une seule fois
			$mois=explode('-',$resContrat['date']);			
			if ($mois[1]==6){
				$cas1=round((($salaire*12)/26),2);//26 = nombre de jours ouvrables en moyenne dans un mois
				$cas2=round(($salaire*0.1*12),2);
				if($cas1>$cas2){
					$congesPayes=$cas1;
				}else{$congesPayes=$cas2;
				}
			}
		}elseif ($resContrat['conges_payes']==3){//Lors de la prise des congés			
		    $cas1=round(((($salaire)*12)/26)/$nbConges,2);//26 = nombre de jours ouvrables en moyenne dans un mois
			$cas2=round(($salaire*0.1*12)/$nbConges,2);
			if($cas1>$cas2){
				$congesPayes=$cas1*$resContrat['conge_mois'];				
			}else{$congesPayes=$cas2*$resContrat['conge_mois'];
			}			
		}	
		$salaire+=$congesPayes;
		/***Régularisation***/
		$salaire+=$resContrat['salaire_regul'];
		/***Indemnitées***/
		$salaireEntretient=$resContrat['jour_mois']*$resContrat['indemnite_entretien'];//indemnités entretien
		$salaire+=$salaireEntretient;
		$salaireRepas=$resContrat['repas_mois']*$resContrat['indemnite_repas'];//indemnités repas				
		$salaire+=$salaireRepas;
		$salaireKilometre=$resContrat['jour_mois']*$resContrat['indemnite_kilometrique'];
		$salaire+=$salaireKilometre;
		/***Loi TEPA***/
		$salaireTEPA=0;
		/***Fin***/
		//$salaire=round($salaire,2);
		$salaire=number_format($salaire,2,'.',' ');
		$reqUpdate="UPDATE salaire SET salaire_net='$salaire',salaire_base='$salaireBase',salaire_comp='$salaireComp',
		salaire_supp='$salaireSupp',salaire_conges_payes='$congesPayes',salaire_entretient='$salaireEntretient',salaire_repas='$salaireRepas',
		salaire_kilometre='$salaireKilometre',salaire_reduc_tepa='$salaireTEPA',nb_heures_mois='$nb_heure_mois'
		 WHERE id='$_POST[id]';";			
		$result=@mysql_query($reqUpdate);			
		// Si une erreur survient
        if(!$result){
        	echo "Erreur d'accès à la base de données";
        }else{                                                                                        
			//Message de confirmation			
			echo "!salaire_net#$salaire
			@@heures_mois#".number_format($heure_mois,2,'.',' ')."
			@@jour_mois#".number_format($jour_mois,2,'.',' ')."
			@@semaines_travaillees#".number_format($semaines_travaillees,2,'.',' ')."
			@@heure_comp_mois#".number_format($nbHeuresComp,2,'.',' ')."
			@@heure_supp_mois#".number_format($nbHeuresSupp,2,'.',' ')."
			@@repas_mois#".number_format($repas_mois,2,'.',' ')."
			@@conge_mois#".number_format($conge_mois,2,'.',' ')."
			@@absence_enfant#".number_format($abs_enfant_mois,2,'.',' ')."
			@@jour_formation_asm#".number_format($abs_asm_mois,2,'.',' ')."";                                                                                                             
        }                                                                                           
        //Fermeture de la connexion à la base de données
        mysql_close();                            
     }     
}
?>