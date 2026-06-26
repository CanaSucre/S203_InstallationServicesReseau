<?php
    session_start();
    
    $mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");
    
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }
    
    $estConnecte = false;
    $userId = null;
    
    $query = "SELECT user_id FROM sessions WHERE session_id = '".session_id()."' AND expiration_date > ".time();
    $result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_row($result);
    
    if ($row) {
        $estConnecte = true;
        $userId = $row[0];
    }
    
    mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/common.css">
    <title>Page d'accueil du serveur web</title>
</head>
<body>
    <header>
        <h1>LE JEU DE L'OIE </h1>
        <nav>
            <ul>
                <li><a href="#classement">Classement</a></li>
                <?php if (!$estConnecte): ?>
                    <li><a href="signup.php">S'inscrire</a></li>
                    <li><a href="login.php">Se connecter</a></li>
                <?php else: ?>
                    <li><a href="profil.php?user=<?php echo $userId; ?>">Mon profil</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <hr>
    <main>
        <section>
            <div id="header-presentation">
                <h2>Présentation du jeu</h2>
            </div>
            <div id="presentation">
                <div id="presentation-texte">
                    <p>lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <nav>
                        <ul>
                            <li><a href="https://fr.wikipedia.org/wiki/Jeu_de_l'oie_(jeu_de_soci%C3%A9t%C3%A9)">Wikipédia</a></li>
                        </ul>
                    </nav>
                </div>
                <figure id = "imagepresentation">
                    <img src="images/imgjeudeloie.webp" alt="Image du jeu de l'oie">
                    <figcaption>Le jeu de l'oie</figcaption>
                </figure>
            </div>
        </section>
        <hr style="width : 60% ;">
        <section id="regles">
            <div id = "header-regles">
                <h2>Règles du jeu</h2>

            </div>
            <p><h4>Les différentes cases :<br></h4>
                <br>
                La règle de base est intangible.<br> Le jeu se joue avec 2 dés. Un premier coup décide de celui qui va commencer. <br>L'oie signale les cases fastes disposées de 9 en 9. Nul ne peut s'arrêter sur ces cases bénéfiques et on double alors le jet.<br>
                <br>
                • Qui fait 9 au premier jet, ira au 26 s'il l'a fait par 6 et 3, ou au 53 s'il l'a fait par 4 et 5.<br>
                • Qui tombe à 6, où il y a un pont, ira à 12.<br>
                • Qui tombe à 19, où il y a un hôtel, se repose quand chacun joue 2 fois.<br>
                • Qui tombe à 31, où il y a un puits attend qu'on le relève.<br>
                • Qui tombe à 42, où il y a un labyrinthe retourne à 30.<br>
                • Qui tombe à 52, où il y a une prison attend qu'on le relève.<br>
                • Qui tombe à 58, où il y a la mort, recommence.<br>
                <br>
                Le premier arrivé à 63, dans le jardin de l'oie, gagne la partie. À condition de tomber juste, sinon il retourne en arrière, sur autant de cases qu'il lui reste à parcourir.<br>
                <br>
                Si un joueur tombe sur une case déjà occupée par un autre joueur il renvoie ce dernier à la case d'où il est parti. Il ne peut y avoir qu'un joueur par case.<br></p>
            <nav>
                <ul>
                    <li><a href="https://www.regles-de-jeux.com/regle-du-jeu-de-l-oie/">Règles de jeux</a></li>
                </ul>
            </nav>
        </section>
        <hr style="width : 60% ;">
        <section id="classement">
            <h2>Classement</h2>
            <p><h4>Les différents joueurs :<br></h4>
                <br>
                Les joueurs sont classés selon le nombre de parties gagnées.<br>
            </p>
        </section>
    </main>
</body>
</html>