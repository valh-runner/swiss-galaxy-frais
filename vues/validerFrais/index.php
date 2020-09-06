<div id="my-liste-visiteurs">
    <h2>Validation fiches de frais</h2>
    <h3>Visiteur à sélectionner : </h3>
    <form action="/validerFrais/index" method="post">
        <div class="corpsForm">
            <p>
                <label for="lstVisiteur" accesskey="n">Visiteur : </label>
                <select id="lstVisiteur" name="lstVisiteur">
                    <?php while ($visiteur = $lesVisiteurs->fetch(PDO::FETCH_ASSOC)) :
                        $spacer = strlen($visiteur['id']) > 2 ?
                            (strlen($visiteur['id']) > 3 ? '&ensp;-&ensp;' : '&ensp;&ensp;-&ensp;')
                            : '&ensp;&ensp;&ensp;-&ensp;';
                    ?>
                        <?php if ($visiteur['id'] == $idVisiteur) : ?>
                            <option selected value="<?php echo $visiteur['id'] ?>"><?php echo  $visiteur['id'] . $spacer . $visiteur['nom'] . $visiteur['prenom'] ?> </option>
                        <?php else : ?>
                            <option value="<?php echo $visiteur['id'] ?>"><?php echo  $visiteur['id'] . $spacer . $visiteur['nom'] . $visiteur['prenom'] ?> </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois">
                    <?php foreach ($douzesDerniersMois as $mois) :
                        $selected = '';
                        if (!empty($moisFiche)) {
                            if ($mois[0] . $mois[1] == $moisFiche) {
                                $selected = 'selected';
                            }
                        }
                    ?>
                        <option <?php echo $selected ?> value="<?php echo  $mois[0] . $mois[1] ?>">
                            <?php echo  $mois[0] . " - " . $mois[1] ?>
                        </option>
                    <?php endforeach; ?>
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

<?php if ($afficherFiche) : ?>
    <div id="my-liste-frais-forfait">
        <h2>Renseigner ma fiche de frais du mois <?php echo $numMois . "-" . $numAnnee ?></h2>
        <form method="POST" action="/validerFrais/validerMajFraisForfait">
            <div class="corpsForm">
                <fieldset>
                    <legend>Eléments forfaitisés
                    </legend>
                    <?php foreach ($lesFraisForfait as $unFrais) : ?>
                        <p>
                            <label for="idFrais"><?php echo $unFrais['libelle'] ?></label>
                            <input type="text" id="idFrais" name="lesFrais[<?php echo $unFrais['idfrais'] ?>]" size="10" maxlength="5" value="<?php echo $unFrais['quantite'] ?>">
                        </p>
                    <?php endforeach; ?>
                    <input id="hiddenIdVisiteur" name="hiddenIdVisiteur" type="hidden" value="<?php echo $idVisiteur ?>">
                    <input id="hiddenMoisFiche" name="hiddenMoisFiche" type="hidden" value="<?php echo $moisFiche ?>">
                </fieldset>
            </div>
            <div class="piedForm">
                <p>
                    <input id="ok" type="submit" value="Valider" size="20" />
                    <input id="annuler" type="reset" value="Effacer" size="20" />
                </p>
            </div>
        </form>
    </div>

    <table class="listeLegere">
        <caption>Descriptif des éléments hors forfait
        </caption>
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class="montant">Montant</th>
            <th class="action">&nbsp;</th>
            <th class="action">&nbsp;</th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) :
            substr($unFraisHorsForfait['libelle'], 0, 7) == 'REFUSE:'
                ? $classLienRefuser = 'not-active' : $classLienRefuser = ''; ?>
            <tr>
                <td><?php echo $unFraisHorsForfait['date'] ?></td>
                <td><?php echo $unFraisHorsForfait['libelle'] ?></td>
                <td><?php echo $unFraisHorsForfait['montant'] ?></td>
                <td><a href="/validerFrais/refuserFrais/<?php echo $unFraisHorsForfait['id'] . '/' . $idVisiteur . '/' . $moisFiche ?>" onclick="return confirm('Voulez-vous vraiment refuser ce frais?');" class="<?php echo $classLienRefuser ?>">
                        Refuser ce frais
                    </a></td>
                <td><a href="/validerFrais/reporterFrais/<?php echo $unFraisHorsForfait['id'] . '/' . $idVisiteur . '/' . $moisFiche ?>" onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">
                        Reporter ce frais
                    </a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="end-actions">
        <form method="POST" action="/validerFrais/validerFiche/<?php echo $idVisiteur . '/' . $moisFiche ?>" class="align-right">
            <input type="submit" value="Valider la fiche de frais" onclick="return confirm('Voulez-vous vraiment valider la fiche de frais?');">
        </form>
    </p>
<?php endif;
