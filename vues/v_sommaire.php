﻿    <!-- Division pour le sommaire -->
    <div id="menuGauche">
       <div id="infosUtil">
          <h2>
          </h2>
       </div>
       <ul id="menuList">
          <li>
             Visiteur :<br>
             <?php echo $_SESSION['prenom'] . "  " . $_SESSION['nom']  ?>
          </li>
          <li class="smenu">
             <a href="/gererFrais/index" title="Saisie fiche de frais ">Saisie fiche de frais</a>
          </li>
          <li class="smenu">
             <a href="/etatFrais/index" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
          </li>
          <li class="smenu">
             <a href="/connexion/deconnexion" title="Se déconnecter">Déconnexion</a>
          </li>
       </ul>
    </div>