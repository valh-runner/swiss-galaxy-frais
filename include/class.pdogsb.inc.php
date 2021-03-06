<?php

/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb
{
    // private static $serveur='mysql:host=db5000825502.hosting-data.io';
    // private static $bdd='dbname=dbs730246';
    // private static $user='dbu949493';
    // private static $mdp='5ilver-Stone';	
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        try {
            PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
            PdoGsb::$monPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // affichage des erreurs PDO
            PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function _destruct()
    {
        PdoGsb::$monPdo = null;
    }
    /**
     * Fonction statique qui crée l'unique instance de la classe
 
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
     * @return l'unique objet de la classe PdoGsb
     */
    public  static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }
    /**
     * Retourne les informations d'un visiteur
 
     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom from visiteur
		where visiteur.login='$login' and visiteur.mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }
    /**
     * Retourne les informations d'un comptable
 
     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
     */
    public function getInfosComptable($login, $mdp)
    {
        $req = "select comptable.id as id, comptable.nom as nom, comptable.prenom as prenom from comptable
		where comptable.login='$login' and comptable.mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments
 
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur' 
		and lignefraishorsforfait.mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }
    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs 
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }
    /**
     * Retourne tous les id de la table FraisForfait
 
     * @return un tableau associatif 
     */
    public function getLesIdFrais()
    {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }
    /**
     * Met à jour la table ligneFraisForfait
 
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif 
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }
    /**
     * met à jour le nombre de justificatifs de la table fichefrais
     * pour le mois et le visiteur concerné
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }
    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux 
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            return true;
        }
        return false;
    }
    /**
     * Retourne le dernier mois en cours d'un visiteur
 
     * @param $idVisiteur 
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
 
     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        //si dernière fiche saisie non cloturée
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL'); //cloture dernière fiche de frais
        }
        //création nouvelle fiche pour mois en cours
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        //création des lignes frais forfait
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }
    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant)
    {
        $dateFr = dateFrancaisVersAnglais($date);
        if(strlen($libelle) > 100){ //si libelle trop long
            $libelle = substr($libelle, 0, 100); //tronquage libelle
        }
        $req = "insert into lignefraishorsforfait values(null,'$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }
    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
 
     * @param $idFrais 
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }
    /**
     * Retourne le frais hors forfait dont l'id est passé en argument
 
     * @param $idFrais 
     */
    public function getFraisHorsForfait($idFrais)
    {
        $req = "select * from lignefraishorsforfait where id =$idFrais ";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
     * @param $idVisiteur 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' 
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee"  => "$numAnnee",
                "numMois"  => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
	 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais
	 
     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $req = "update fichefrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne le nombre de fiches de frais à cloturer
     * 
     *  @return int nombre de fiches à clôturer
     */
    public function getNbrFichesACloturer()
    {
        $mois = getMois(date("d/m/Y"));
        //comptage de chaque dernière fiche de frais de chaque visiteurs, antérieure au mois actuel et non cloturée
        $req = "
        SELECT count(*) as nbFichesACloturer
        FROM fichefrais
        JOIN ( 
          SELECT idVisiteur, MAX(mois) AS moisMax FROM fichefrais 
          WHERE fichefrais.mois < '$mois'
          GROUP BY idVisiteur
        ) tmax 
        ON fichefrais.idVisiteur = tmax.idVisiteur AND fichefrais.mois = tmax.moisMax AND fichefrais.idEtat = 'CR'
        ";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nbFichesACloturer'];
    }

    /**
     * Clôture toutes les fiches de frais à cloturer
     * 
     * Chaque fiche de frais à cloturer passe de l'état créée à l'état clôturée
     */
    public function cloturerFichesACloturer()
    {
        $mois = getMois(date("d/m/Y"));
        //Cloture de chaque dernière fiche de frais de chaque visiteurs, antérieure au mois actuel et non cloturée
        $req = "
        UPDATE fichefrais
        JOIN ( 
          SELECT idVisiteur, MAX(mois) AS moisMax FROM fichefrais 
          WHERE fichefrais.mois < '$mois'
          GROUP BY idVisiteur
        ) tmax 
        ON fichefrais.idVisiteur = tmax.idVisiteur AND fichefrais.mois = tmax.moisMax AND fichefrais.idEtat = 'CR'
        SET fichefrais.idEtat = 'CL'
        ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne la liste des visiteurs
     * 
     * Retourne l'id, le nom et le prenom de chaque visiteur
     * @return resultset des visiteurs
     */
    public function getLesVisiteurs()
    {
        $req = "select visiteur.id, visiteur.nom, visiteur.prenom from visiteur";
        $rs = PdoGsb::$monPdo->query($req);
        return $rs;
    }

    /**
     * Retourne la liste des fiches cloturées ou validées et le nom et le prénom du visiteur associé
     * 
     * Retourne tous les champs de la fiche et le nom et le prenom du visiteur
     * @return resultset de fiches de frais
     */
    public function getFichesClotureesEtValidees()
    {
        $req = "SELECT fichefrais.*, visiteur.nom, visiteur.prenom FROM fichefrais, visiteur WHERE fichefrais.idVisiteur = visiteur.id AND (idEtat ='CL' OR idEtat ='VA') ORDER BY mois DESC, nom ASC, prenom ASC";
        $res = PdoGsb::$monPdo->query($req);
        return $res;
    }
}
