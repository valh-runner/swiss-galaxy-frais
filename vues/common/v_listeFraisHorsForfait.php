﻿<table class="listeLegere">
  <caption>Descriptif des éléments hors forfait
  </caption>
  <tr>
    <th class="date">Date</th>
    <th class="libelle">Libellé</th>
    <th class="montant">Montant</th>
    <th class="action">&nbsp;</th>
  </tr>
  <?php foreach ($lesFraisHorsForfait as $unFraisHorsForfait): ?>
    <tr>
      <td><?php echo $unFraisHorsForfait['date'] ?></td>
      <td><?php echo $unFraisHorsForfait['libelle'] ?></td>
      <td><?php echo $unFraisHorsForfait['montant'] ?></td>
      <td><a href="/gererFrais/supprimerFrais/<?php echo $unFraisHorsForfait['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer ce frais</a></td>
    </tr>
  <?php endforeach; ?>
</table>

<form action="/gererFrais/validerCreationFrais" method="post">
  <div class="corpsForm">
    <fieldset>
      <legend>Nouvel élément hors forfait
      </legend>
      <p>
        <label for="txtDateHF">Date (jj/mm/aaaa): </label>
        <input type="text" id="txtDateHF" name="dateFrais" size="10" maxlength="10" value="" />
      </p>
      <p>
        <label for="txtLibelleHF">Libellé</label>
        <input type="text" id="txtLibelleHF" name="libelle" size="70" maxlength="100" value="" />
      </p>
      <p>
        <label for="txtMontantHF">Montant : </label>
        <input type="text" id="txtMontantHF" name="montant" size="10" maxlength="10" value="" />
      </p>
    </fieldset>
  </div>
  <div class="piedForm">
    <p>
      <input id="ajouter" type="submit" value="Ajouter" size="20" />
      <input id="effacer" type="reset" value="Effacer" size="20" />
    </p>
  </div>
</form>
