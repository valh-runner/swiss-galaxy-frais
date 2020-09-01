<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
    <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="/styles/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div id="page">
        <div id="entete">
            <img src="/images/logo.jpg" id="logoGSB" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin" />
            <h1>Suivi du remboursement des frais</h1>
        </div>

        <?php if (!empty($_SESSION['idVisiteur'])) : ?>
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
        <?php endif; ?>

        <div class="erreur">
            <ul>
                <?php
                if (!empty($_SESSION['erreurs'])) {
                    foreach ($_SESSION['erreurs'] as $erreur) {
                        echo "<li>$erreur</li>";
                    }
                    unset($_SESSION['erreurs']); //suppression erreurs affichées
                }
                ?>
            </ul>
        </div>

        <?= $content_for_layout ?>
    </div>
</body>

</html>