<?php 

    $mysqli = mysqli_connect("localhost","iut_s203","mdp","s203");

	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
	}



    if ($_POST['deleteUser']) {
        $userIdToDelete = $_POST['deleteUser'];
        $queryDeleteUser = "UPDATE user SET is_deleted = 1 WHERE id = '$userIdToDelete'";
        
        mysqli_query($mysqli, $queryDeleteUser);
    }

    $queryJoueurs = "SELECT * FROM statsAdminPage ORDER BY login ASC";

    $resultJoueurs = mysqli_query($mysqli, $queryJoueurs);


    $nbPartiesJoues = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS nbParties FROM game"))['nbParties'];
    $nbUtilisateurs = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS nbUtilisateurs FROM user WHERE is_deleted = 0"))['nbUtilisateurs'];


    $queryParties = "
        SELECT 
            game.id AS gameId,
            status,
            dateStart,
            dateEnd,
            joueurs,
            v.login AS winnerLogin
        FROM game
        LEFT JOIN (
            SELECT
                game_id,
                GROUP_CONCAT(login SEPARATOR \"|\") AS joueurs
            FROM player_game
            INNER JOIN user
                ON player_game.player_id = user.id 
            GROUP BY game_id
        ) AS joueurs 
            ON game.id = joueurs.game_id
        LEFT JOIN user v
            ON game.winner = v.id
        ORDER BY dateStart DESC
    ";
    $resultParties = mysqli_query($mysqli, $queryParties);


    
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/admin.css">

    <title>Page admin</title>
</head>

<body>
    <header>
        <h1 class="txt-center" id="titleProfil">Page admin</h1>
    </header>

    <main class="m-t-2 d-flex gap-2">



        <section id="section1" class="d-flex gap-1 flex-col">

            <!-- Statistiques du joueur -->
            <article id="statistiques" class="fond p-1 brad-0_5">
                <h2 class="m-0 m-b-1">Statistiques</h2>

                <div class="d-flex gap-0_5 flex-jc-around">
                    <div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
                        <p class="m-0 statValue" id="partiesJouees"><?php echo $nbPartiesJoues; ?></p>
                        <p class="m-0 statTitle">Parties jouées</p>
                    </div>
                    <div class="fond statItem txt-center brad-0_5 p-0_5 d-flex flex-col flex-jc-around">
                        <p class="m-0 statValue" id="utilisateurs"><?php echo $nbUtilisateurs; ?></p>
                        <p class="m-0 statTitle">Utilisateurs</p>
                    </div>
                </div>
            </article>


            <article id="historiqueParties" class="fond p-1 brad-0_5">
                <h2 class="m-0">Historique des parties</h2>


                <table class="m-t-2">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Joueurs</th>
                            <th>Vainqueur</th>
                            <th>Durée</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row = mysqli_fetch_assoc($resultParties);
                            while ($row) {

                                if ($row['status'] == 'IN_PROGRES') {
                                    $statut = "En cours";
                                } else if ($row['status'] == 'COMPLETED') {
                                    $statut = "Terminée";
                                } else {
                                    $statut = "Annulée";
                                }

                                $duree = (strtotime($row['dateEnd']) - strtotime($row['dateStart'])) / 60;
                                $duree = $duree <= 0 || $row['dateEnd'] == null ? "N/A" : round($duree);
                                

                                $joueurs = explode("|", $row['joueurs']);
                                $strJoueurs = "";
                                $nbJoueurs = 0;
                                foreach ($joueurs as $joueur) {
                                    $strJoueurs .= $joueur;
                                    $nbJoueurs++;

                                    if ($nbJoueurs % 2 == 0) {
                                        $strJoueurs .= "<br>";
                                    } else {
                                        $strJoueurs .= " | ";
                                    }
                                }

                                echo '<tr>
                                        <td>' . date("d/m/Y", strtotime($row['dateStart'])) . '</td>
                                        <td>' . $strJoueurs . '</td>
                                        <td>' . $row['winnerLogin'] . '</td>
                                        <td>' . $duree . ' min</td>
                                        <td>' . $statut . '</td>
                                    </tr>';

                                $row = mysqli_fetch_assoc($resultParties);    
                            }

                        ?>
                    </tbody>
                </table>
            </article>



        </section>


        <section id="section2" class="d-flex gap-1 flex-jc-between">


            <!-- Identification du joueur -->
            <article id="identification" class="fond p-1 brad-0_5">
                <h2 class="m-0 m-b-0_5">Joueurs</h2>



                <?php 
                    
                    $row = mysqli_fetch_assoc($resultJoueurs);
                    while ($row) {

                        $age = floor((time() - strtotime($row['dateNaissance'])) / (365.25 * 24 * 60 * 60));
                        

                        echo '   
                            <div class="joueur d-flex gap-2 flex-jc-between">
                        
                                <div class="d-flex gap-1">
                                    <div class="photoProfil"></div>

                                    <div class="d-flex flex-col">
                                        <p class="m-0">' . $row['login'] . ' - ' . $age . ' ans</p>
                                        <p class="m-0">' . $row['nbParties'] . ' parties (' . $row['nbVictoires'] . 'V/' . $row['nbDefaites'] . 'D)</p>
                                        <p class="italic m-0">#' . $row['id'] . '</p>
                                    </div>
                                </div>
                                
                            
                                <div class="d-flex flex-col gap-1">
                                    <a class="txt-center btn" target="_blank" href="../profil.php?user=' . $row['id'] . '">Afficher le profil</a>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="deleteUser" value="' . $row['id'] . '">
                                        <button type="submit" class="txt-center btn">Supprimer le compte</button>
                                    </form>
                                </div>
                            </div>
                        ';

                        $row = mysqli_fetch_assoc($resultJoueurs);
                    }
                ?>


            </article>
        </section>




    </main>

</body>

</html>