        <div class="col user-nav">
            <ul class="white">
                <li><a href="/espace-utilisateur/accueil"><i class="fas fa-table"></i> &nbsp;&nbsp;&nbsp; Tableau de bord</a></li>
                <li><a href="/espace-utilisateur/compte"><i class="far fa-user-circle"></i> &nbsp;&nbsp;&nbsp; Mon compte</a></li>
                <li><a href="/espace-utilisateur/assistance"><i class="far fa-life-ring"></i> &nbsp;&nbsp;&nbsp; Aide et support</a></li>
                <?php if ($_SESSION['Data']['access_level'] == "ADMINISTRATOR") { ?>
                <li><a href="/espace-utilisateur/administration"><i class="fas fa-user-shield"></i> &nbsp;&nbsp;&nbsp; Administration</a></li>
                <?php } ?>
            </ul>
        </div>