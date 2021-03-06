<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php
            $userId = $_GET['user_id'];
           
            $mysqli = new mysqli("localhost:8889", "root", "root", "socialnetwork");
            ?>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <nav id="menu">
                <a href="news.php?user_id=<?php echo $_SESSION['connected_id']?>">Actualités</a>
                <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id']?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id']?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id']?>">Mes abonnements</a></li>
                    <?php
                 $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
                 $lesInformations = $mysqli->query($laQuestionEnSql);
                 $user = $lesInformations->fetch_assoc();
                if($user['id']==NULL){
                    ?>
                    <li><a href="login.php">Connexion</a></li>
                    <?php } else if ($_SESSION['connected_id']==$user['id']) { ?> 
                    <li><a href="logout.php">Déconnexion</a></li>
                    <?php
                    }
                    ?>
                </ul>

            </nav>
        </header>
            <div id="wrapper">
                <?php
                $userId = $_GET['user_id'];

                $mysqli = new mysqli("localhost:8889", "root", "root", "socialnetwork");
                ?>

            <aside>
                <?php
                    $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
                    $lesInformations = $mysqli->query($laQuestionEnSql);
                    $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias']?></p>
   
                <?php
                    if ($_SESSION['connected_id']!=$user['id']){
                        $followedRequest = "SELECT FROM `followers` "
                        . "WHERE `followed_user_id`=$userId AND `following_user_id`=$following_id ";
                        $followedReturn = $mysqli->query($followedRequest);
                        if ($followedReturn == NULL){
                            ?>
                            <form action="wall.php?user_id=<?php echo $userId?>" method="post">
                                <input type="submit" value="suivre" name="bouton">
                                <input type="hidden" value ="false" name="follow">
                            </form>

                            <?php 
                              $enCoursDeTraitement = isset($_POST['bouton']); 
                    
                              if ($enCoursDeTraitement){
                                  $following_id = $_SESSION['connected_id'];
                                  $followedId = $userId;
                                  $followedId = intval($mysqli->real_escape_string($followedId));
                                  $following_id = $mysqli->real_escape_string($following_id);
                              
                                  $lInstructionSql = "INSERT INTO `followers` "
                                      . "(`id`, `followed_user_id`, `following_user_id`) "
                                      . "VALUES (NULL, "
                                      . "" . $followedId . ", "
                                      . "" . $following_id . ")" ;
                          
                                  $ok = $mysqli->query($lInstructionSql);
                                  if ( ! $ok)
                                  {
                                      echo "Vous suivez déjà " . $user['alias']." !";
                                  } else
                                  {
                                      echo "Bravo ! Vous suivez ". $user['alias']." !";
                                  }
                                }        
                        }                            
                            else {
                                ?>
                                <form action="wall.php?user_id=<?php echo $userId?>" method="post">
                                <input type="submit" value="ne plus suivre" name="bouton">
                                <input type="hidden" value ="true" name="follow">
                                </form>
                                <?php 
                                    $enCoursDeTraitement = isset($_POST['bouton']); 
                    
                                    if ($enCoursDeTraitement){
                                        $following_id = $_SESSION['connected_id'];
                                        $followedId = $userId;
                                    
                                        $followedId = intval($mysqli->real_escape_string($followedId));
                                        $following_id = $mysqli->real_escape_string($following_id);
                                    
                                        $lInstructionSql = "DELETE FROM `followers` "
                                        . "WHERE `followed_user_id`=$userId AND `following_user_id`=$following_id ";
                                
                                        $ok = $mysqli->query($lInstructionSql);
                                        if ( ! $ok)
                                        {
                                            echo "Vous suivez toujours " . $user['alias']." !";
                                        } else
                                        {
                                            echo "Vous ne suivez plus ". $user['alias']." !";
                                        }
                                    }
                            }
                    }
            

                    $mysqli = new mysqli("localhost:8889", "root", "root", "socialnetwork");
                    $enCoursDeTraitement = isset($_POST['bouton_like']); 
                        if ($enCoursDeTraitement) {
                            $liker_id = $_POST['liker_id'];
                            $post_id = $_POST['post_id'];
            
                            $liker_id = intval($mysqli->real_escape_string($liker_id));
                            $post_id = $mysqli->real_escape_string($post_id);
                    
                            $lInstructionSql = "INSERT INTO `likes` "
                            . "(`id`, `user_id`, `post_id`) "
                            . "VALUES (NULL, "
                            . "" . $liker_id . ", "
                            . "" . $post_id . ")" ;
                    
                            $ok = $mysqli->query($lInstructionSql);
                                if ( ! $ok) {
                                    echo "Vous avez déjà liké ! ";
                                } else {
                                    echo "Bravo vous avez liké ! ";
                                }
                        } 
                         
                   
                    
                    ?>
                
                </section>
            </aside>

            <main>
                <?php 
                if ($_SESSION['connected_id']==$user['id']){
                ?>
                <article>
                    <h2>Poster un message</h2>
                    <?php
                    $mysqli = new mysqli("localhost:8889", "root", "root", "socialnetwork");
                    $enCoursDeTraitement = isset($_POST['message']); 
                   
                    if ($enCoursDeTraitement){
                        $authorId = $_SESSION['connected_id'];
                        $postContent = $_POST['message'];
                        
                        $authorId = intval($mysqli->real_escape_string($authorId));
                        $postContent = $mysqli->real_escape_string($postContent);
                        
                        $lInstructionSql = "INSERT INTO `posts` "
                                . "(`id`, `user_id`, `content`, `created`, `parent_id`) "
                                . "VALUES (NULL, "
                                . "" . $authorId . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                . "NULL);"
                                . "";
                        
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté";
                        }
                    }
                    ?>      
                    
                    <form action="wall.php?user_id=<?php echo $_SESSION['connected_id']?>" method="post">
                        <dt><label for='message'>Message</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit'>
                    </form>
                <?php 
                }
                ?>
                </article>    

                <?php
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`posts`.`id` as post_identifiant, " 
                        . "`users`.`alias` as author_name,  "
                        . "count(DISTINCT `likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts`"
                        . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `posts`.`user_id`='" . intval($userId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  ";

                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($post = $lesInformations->fetch_assoc()){
                ?>                
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                    </h3>
                        <address>par <?php echo $post['author_name']?></address>
                    <div>
                        <p><?php echo $post['content']?></p>
                    </div>                                            
                    <footer>
                        <form method="post">
                            <input type="hidden" name="post_id" value=<?php echo $post['post_identifiant']?>>
                            <input type="hidden" name="liker_id" value=<?php echo $_SESSION['connected_id']?>>
                            <input type="submit" value="♥" name="bouton_like">
                        </form>
                        <small>♥ <?php echo $post['like_number']?></small>
                        <a href="">#<?php echo $post['taglist']?></a>
                    </footer>
                    </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>