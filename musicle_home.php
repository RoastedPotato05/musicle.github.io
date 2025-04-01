<?php
    session_unset();
    session_destroy();
    session_start();
    header('Content-Type: text/html; charset=ISO-8859-1');

    if (!isset($_SESSION['user_id'])) {
        header('Location: views/login_view.php'); // Redirect to login if not authenticated
        exit();
    }
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']); //Clear error after displaying
    }
?>
<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    

<head>
    <title>musicle home page</title>
    
</head>

    
<body style="background-color:#233436;">
    <div class="w3-cell-row">
        <div class="w3-container w3-cell w3-bottombar w3-border-white" style="width: 33%"></div>
        <div align="center" class="w3-bottombar w3-border-white w3-container w3-cell" style="width: 34%;">
            <h1 style= "font-size: 400%;" class="w3-text-green"><b>musicle</b></h1>
        </div>
        

        <div class="w3-container w3-cell w3-padding-8 w3-bottombar w3-border-white" style="width: 33%;">
            <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-blue w3-round-large" style="position: absolute; top: 4%; left: 80%"><b>Profile</b></button>
            <div id="id01" class="w3-modal">
                <div class="w3-modal-content w3-card-4">
                    <header class="w3-container w3-green"> 
                        <span onclick="document.getElementById('id01').style.display='none'" 
                        class="w3-button w3-display-topright">&times;</span>
                        <b><h2>
                            <?php echo $_SESSION['username'] . "'s Profile"?>
                        </b></h2>
                    </header>
                    <div class="w3-container">
                        <?php 
                        // Database connection
                            $dsn='mysql:host=localhost:8889;dbname=musicle';
                            $SQL_username='root';
                            $SQL_password='root';

                            try{
                                $pdo= new PDO($dsn,$SQL_username,$SQL_password);
                            }
                            catch(PDOException $e){
                                die('Connection error'.$e->getMessage());
                            }

                            $username = $_SESSION['username'];

                            $sql = "SELECT wins FROM `users` WHERE username = '$username'";
                            $stmt=$pdo->query($sql);
                            $wins=$stmt->fetch();

                            $sql = "SELECT losses FROM `users` WHERE username = '$username'";
                            $stmt=$pdo->query($sql);
                            $losses=$stmt->fetch();
                        ?>
                        <b><p>Wins: 
                            <?php echo $wins[0] ?>
                        </p>
                        <p>Losses: 
                            <?php echo $losses[0] ?>
                        </p>
                        </b>
                    </div>
                    <footer class="w3-container w3-green w3-padding-16">
                        <div align="center">
                            <a href="logout.php">
                                <button class="w3-button w3-red w3-round-large"><b>Logout</b></button>
                            </a>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
    
    <div align="center">
        <br>
    <form action = "musicle_game.php" method = "post">

        
            <b><input  
                    type = "text" 
                    id = "playlist_link" 
                    name = "playlist_link" 
                    placeholder = "Spotify Playlist Link" 
                    style="width: 500px; " 
                    class= "w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-green w3-round-large w3-padding-16"
                    required>
            </b>
            
        
        <br><br>

        <!--Submit Button-->
        <b><input 
            type = "submit" 
            value = "Submit"
            class= "w3-button w3-green w3-round-large">
        </b>

    </form>
    </div>     

</body>
    

    
</html>