<div id="my-liste-visiteurs">
    <h2>Suivi fiches de frais</h2>
    <h3>Visiteur à sélectionner : </h3>
    <form action="/suivreFrais/index" method="post">
        <div class="corpsForm">
            <p>
                <label for="lstFiche" accesskey="n">Fiche : </label>
                <select id="lstFiche" name="lstFiche" class="monospace-font">
                    <?php while ($ficheASuivre = $fichesClotureesEtValidees->fetch(PDO::FETCH_ASSOC)) : 
                            $spacer = strlen($ficheASuivre['idVisiteur']) > 2 ?
                            (strlen($ficheASuivre['idVisiteur']) > 3 ? '&nbsp;-&nbsp;' : '&nbsp;&nbsp;-&nbsp;')
                            : '&nbsp;&nbsp;&nbsp;-&nbsp;';
                        ?>
                        <?php if($ficheASuivre['idVisiteur'] . '-' . $ficheASuivre['mois'] == $_POST['lstFiche']): ?>
                            <option selected value="<?php echo $ficheASuivre['idVisiteur'] . '-' . $ficheASuivre['mois'] ?>"><?php echo  $ficheASuivre['idEtat']. ' | ' . $ficheASuivre['idVisiteur'] . $spacer . substr($ficheASuivre['mois'], 0, 4).'-'.substr($ficheASuivre['mois'], 4, 2) . ' | ' . $ficheASuivre['nom'] . ' ' . $ficheASuivre['prenom'] ?> </option>
                        <?php else: ?>
                            <option value="<?php echo $ficheASuivre['idVisiteur'] . '-' . $ficheASuivre['mois'] ?>"><?php echo  $ficheASuivre['idEtat']. ' | ' . $ficheASuivre['idVisiteur'] . $spacer . substr($ficheASuivre['mois'], 0, 4).'-'.substr($ficheASuivre['mois'], 4, 2) . ' | ' . $ficheASuivre['nom'] . ' ' . $ficheASuivre['prenom'] ?> </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </p>
        </div>
        <div class="piedForm">
            <p>
                <input id="ok" type="submit" value="Valider" size="20" />
                <input id="annuler" type="reset" value="Effacer" size="20" />
            </p>
        </div>
    </form>
</div>

<?php
if (!empty($_POST['lstFiche'])):
    if($ficheExiste):
        include("vues/common/v_etatFrais.php");
        $validerDisabled = '';
        $rembourseeDisabled = '';
        if($idEtat == 'CL'){
            $rembourseeDisabled = 'disabled';
        }elseif($idEtat == 'VA'){
            $validerDisabled = 'disabled';
        }
?>
        <p class="end-actions">
        <form method="POST" action="/suivreFrais/marquerCommeValidee/<?php echo $idVisiteur . '/' . $mois ?>" class="align-right">
            <input type="submit" value="Valider / Mettre en paiement" onclick="return confirm('Voulez-vous vraiment valider / mettre en paiement la fiche de frais?');" <?php echo $validerDisabled ?> >
        </form>
        <form method="POST" action="/suivreFrais/marquerCommeRemboursee/<?php echo $idVisiteur . '/' . $mois ?>" class="align-right">
            <input type="submit" value="Mettre à l'état remboursé" onclick="return confirm('Voulez-vous vraiment mettre la fiche de frais à l\'état remboursé?');" <?php echo $rembourseeDisabled ?> >
        </form>
        </p>
<?php
    endif;
endif;
?>
