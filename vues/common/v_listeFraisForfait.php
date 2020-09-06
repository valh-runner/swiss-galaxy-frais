<div id="my-liste-frais-forfait">
  <h2>Renseigner ma fiche de frais du mois <?php echo $numMois . "-" . $numAnnee ?></h2>
  <form method="POST" action="/gererFrais/validerMajFraisForfait">
    <div class="corpsForm">
      <fieldset>
        <legend>Eléments forfaitisés
        </legend>
        <?php foreach ($lesFraisForfait as $unFrais): ?>
          <p>
            <label for="idFrais"><?php echo $unFrais['libelle'] ?></label>
            <input type="text" id="idFrais" name="lesFrais[<?php echo $unFrais['idfrais'] ?>]" size="10" maxlength="5" value="<?php echo $unFrais['quantite'] ?>">
          </p>
        <?php endforeach; ?>
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