<style>
    .container {
  display: flex;
  justify-content: center;
}
</style>
<?php
    session_start();

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

    $sql = "UPDATE `users` SET `losses`= `losses` + 1 WHERE username = '$username'";
    $pdo_statement = $pdo->prepare($sql);
    $pdo_statement->execute();
?>

<!DOCTYPE html>
<html lang="en">
    
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    
<head>
    <title>Lose screen</title>
    <div align="center" class="w3-bottombar"><h1 style="font-size: 400%;" class="w3-text-green"><b>musicle</b></h1></div>
</head>
    
<body style="background-color:#233436">
    <?php 
        $fopen_playlist_name_txt = fopen("playlist_name.txt", "r");
        $playlist_name = fgets($fopen_playlist_name_txt);
        echo "<p><div align=\"center\" style=\"color: white;\"><strong>Playlist:</strong> $playlist_name</div></p>";
        fclose($fopen_playlist_name_txt);

        $fopen_answers_txt = fopen("answers.txt", "r");

        fgets($fopen_answers_txt);
        $answer = ["", ""];
        $answer[0] = trim(fgets($fopen_answers_txt));
        $answer[1] = trim(fgets($fopen_answers_txt));
        fclose($fopen_answers_txt);
        $answer_string = $answer[0] . " -- " . $answer[1];
        

        for ($j = 0; $j < 6; $j++) { 
            $guess = $_SESSION['guesses'][$j];
            if ($guess == ""){
                $guess = "[   ]";
            }

            if (explode(",", (trim(explode(" -- ", $guess)[1])))[0] == explode(",", trim($answer[1]))[0]) {
                echo "<p><div class=\"w3-panel w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-yellow w3-round-large w3-padding-16 w3-center\" style=\"width:500px;margin:0 auto; color: white; background-color: #172224\" class=\"panel panel-default\"><b>$guess</b></div><p>";
            }
            elseif ($guess == "[   ]"){
                echo "<p><div class=\"w3-panel w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-white w3-round-large w3-padding-16 w3-center\" style=\"width:500px;margin:0 auto; color: white;\" class=\"panel panel-default\"><b>$guess</b></div><p>";
            }
            else {
                echo "<p><div class=\"w3-panel w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-gray w3-round-large w3-padding-16 w3-center\" style=\"width:500px;margin:0 auto; color: white; background-color: #172224\" class=\"panel panel-default\"><b>$guess</b></div><p>";                   
            }
        }


    ?>

    <br>
    <div align="center">
        <h2 class="w3-text-red"><b>
            <?php echo ($answer[0] . " by " . $answer[1]); ?>
        </b></h2>
    </div>
    <div id="song_preview_uri" style="display: none"><?php echo $_SESSION['song_preview_uri']; ?></div>

    <script src="https://open.spotify.com/embed/iframe-api/v1" async></script>

    <div style="margin:0 auto;" class="container w3-center panel panel-default">
        <div id="embed-iframe"></div>
    </div>
    
    <script type="text/javascript">
        let song_preview_uri = (document.getElementById("song_preview_uri").innerHTML + "").trim();

        window.onSpotifyIframeApiReady = (IFrameAPI) => {

            let element = document.getElementById('embed-iframe');
            let options = {
                uri: "",
                height: 180,
                width: "40%",
                };
            let callback = (EmbedController) => {
                EmbedController.loadUri(song_preview_uri);
            };
            IFrameAPI.createController(element, options, callback);
        };

        
        
        
    </script>

    <form action="musicle_game.php" method="post">
        <b><input type="submit" name="next" value="Next" class="w3-round-large w3-button w3-red w3-padding" style="width:500px;margin:0 auto;display:flex;justify-content:center;"></b></input>
    </form>
    
    
    <a href = "musicle_home.php"><div class="w3-round-large w3-button w3-red w3-padding" style="position: absolute; top: 4%; left: 80%"><b>Return</b></div></a>
</body>
    
    
    
</html>