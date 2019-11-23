        <div class="col user-nav mobile">
            <ul class="white">
                <li <?php if (isset($sousPage) && ($sousPage == "accueil" || $sousPage == "grid" || $sousPage == "sort-by-name" || $sousPage == "sort-by-size" || $sousPage == "sort-by-dl" || $sousPage == "sort-by-date")) { echo(" class='active'"); } ?>><a href="/espace-utilisateur/accueil"><i class="fas fa-table"></i> &nbsp;&nbsp;&nbsp; Tableau de bord</a></li>
                <li <?php if (isset($sousPage) && $sousPage == "compte") { echo(" class='active'"); } ?>><a href="/espace-utilisateur/compte"><i class="far fa-user-circle"></i> &nbsp;&nbsp;&nbsp; Mon compte</a></li>
                <li <?php if (isset($sousPage) && $sousPage == "assistance") { echo(" class='active'"); } ?>><a href="/espace-utilisateur/assistance"><i class="far fa-life-ring"></i> &nbsp;&nbsp;&nbsp; Aide et support</a></li>
                <?php if ($_SESSION['Data']['access_level'] == "ADMINISTRATOR") { ?>
                <li <?php if (isset($sousPage) && $sousPage == "administration") { echo(" class='active'"); } ?>><a href="/espace-utilisateur/administration"><i class="fas fa-user-shield"></i> &nbsp;&nbsp;&nbsp; Administration</a></li>
                <?php } ?>
            </ul>
        </div>