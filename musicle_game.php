<!DOCTYPE html>
<html lang="en" >
    
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    
<head>
    <title>musicle game page</title>
    <div align="center" class="w3-bottombar w3-border-white"><h1 style="font-size: 400%;" class="w3-text-green"><b>musicle</b></h1></div>
</head>



<?php
    session_start();
    function fillList() {
        $song_names_txt = "song_names.txt";
        $artist_names_txt = "artist_names.txt";
        $song_previews_txt = "song_previews.txt";
        
        $fopen_song_names = fopen($song_names_txt, "r");
        $fopen_artist_names = fopen($artist_names_txt, "r");
        $fopen_song_previews = fopen($song_previews_txt, "r");
        if (!$fopen_song_names) {
            die("unable to open $song_names_txt");
        }
        if (!$fopen_artist_names) {
            die("unable to open $artist_names_txt");
        }
        if (!$fopen_song_previews) {
            die("unable to open $song_previews_txt");
        }
        
        # makes song names, artist names, and song previews arrays to be put in $_SESSION['songs'] array
        $song_names = array();
        $artist_names = array();
        $song_previews = array();

        while (!feof($fopen_song_names)) {
            $song_name = fgets($fopen_song_names);
            $artist_name = fgets($fopen_artist_names);
            $song_preview = fgets($fopen_song_previews);
            
            $song_names.array_push($song_names, $song_name);
            $artist_names.array_push($artist_names, $artist_name);
            $song_previews.array_push($song_previews, $song_preview);
        }
        $song_names.array_pop($song_names);
        $artist_names.array_pop($artist_names);
        $song_previews.array_pop($song_previews);

        # index 0 is previews, 1 is titles, 2 is artists
        $_SESSION['songs'] = array(
            $song_previews,
            $song_names,
            $artist_names,
        );

        fclose($fopen_song_names);
        fclose($fopen_artist_names);
        fclose($fopen_song_previews);

        
    }
?>
    
<body style="background-color:#233436">
    <?php
        if (isset($_POST["playlist_link"])) {
            session_start();
            $_SESSION['fails'] = 0;

            
            
            $playlist_link = $_POST["playlist_link"];
            $regex = "/open.spotify.com\/playlist/";
            if (!preg_match($regex, $playlist_link)) {
                $_SESSION['error'] = "Invalid playlist link";
                header('Location:  musicle_home.php');
                exit();
            }

            $playlist_link = explode("?", $playlist_link)[0];
            $playlist_id = explode("playlist/", $playlist_link)[1];

            
            

            $filename = "playlist_id.txt";
            $fopen = fopen($filename, "w");
            if (!$fopen) {
                die("unable to open $filename");
            }
            fwrite($fopen, $playlist_id);
            fclose($fopen);
            
            
            $run_python = shell_exec("python musicle.py");

            $fopen_playlist_name_txt = fopen("playlist_name.txt", "r");
            $playlist_name = fgets($fopen_playlist_name_txt);
            echo "<p ><div align=\"center\" style=\"color: white;\"><strong>Playlist:</strong> $playlist_name</div></p>";
            fclose($fopen_playlist_name_txt);

            # fills arrays
            # in session songs 2d array index 0 is previews, 1 is titles, 2 is artists
            
            fillList();
            

            # gets random index and pulls from each array to store in answers.txt and clears guesses.txt
            srand(time());
            $rand_index = rand(0, count($_SESSION['songs'][1]) - 1);
            

            $fopen_answers_txt = fopen("answers.txt", "w");
            fwrite($fopen_answers_txt, "");
            fclose($fopen_answers_txt);
            $fopen_guesses_txt = fopen("guesses.txt", "w");
            fwrite($fopen_guesses_txt, "");
            fclose($fopen_guesses_txt);

            $fopen_answers_txt = fopen("answers.txt", "a");
            fwrite($fopen_answers_txt, $_SESSION['songs'][0][$rand_index]);
            fwrite($fopen_answers_txt, $_SESSION['songs'][1][$rand_index]);
            fwrite($fopen_answers_txt, $_SESSION['songs'][2][$rand_index]);
            fclose($fopen_answers_txt);

            for ($i = 0; $i < 6; $i++) {
                echo "<p><div class=\"w3-panel w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-white w3-round-large w3-padding-16 w3-center\" style=\"width:500px;margin:0 auto; color: white;\" class=\"panel panel-default\"><b>[\t\t\t]</b></div><p>";
            }

            
                
            
        } 
        elseif (isset($_POST["user_answer"]) || isset($_POST["give_up"])) {
            session_start();

            if (isset($_POST["give_up"])) {
                $_SESSION['fails'] = 5;
            }
            
            
            if(array_key_exists('skip', $_POST) || $_POST["user_answer"] == "") {
                $_POST["user_answer"] = "[Skipped]";
            }

            $user_answer = ["", ""];
            $user_answer[0] = trim(explode(" -- ", $_POST["user_answer"])[0]);
            $user_answer[1] = trim(explode(" -- ", $_POST["user_answer"])[1]);
            $fopen_answers_txt = fopen("answers.txt", "r");

            fgets($fopen_answers_txt);
            $answer = ["", ""];
            $answer[0] = trim(fgets($fopen_answers_txt));
            $answer[1] = trim(fgets($fopen_answers_txt));
            fclose($fopen_answers_txt);
            
            
            
        
            if ($answer[0] == $user_answer[0] && $answer[1] == $user_answer[1]) {
                header("Location: musicle_win.php");
            }
            elseif ($_SESSION['fails'] >= 5) {
                header("Location: musicle_lose.php");
            }
            else {
                $_SESSION['fails'] += 1;
            }



            $fopen_playlist_name_txt = fopen("playlist_name.txt", "r");
            $playlist_name = fgets($fopen_playlist_name_txt);
            echo "<p ><div align=\"center\" style=\"color: white;\"><strong>Playlist:</strong> $playlist_name</div></p>";
            fclose($fopen_playlist_name_txt);

            
            
                    
            
            $fopen_guesses_txt = fopen("guesses.txt", "a");
            fwrite($fopen_guesses_txt, $_POST["user_answer"]);
            fwrite($fopen_guesses_txt, "\n");
            fclose($fopen_guesses_txt);
            
            $guesses = ["[   ]", "[   ]", "[   ]", "[   ]", "[   ]", "[   ]"];
            $_SESSION['guesses'] = $guesses;
            $fopen_guesses_txt = fopen("guesses.txt", "r");
            $i = 0;
            while (!feof($fopen_guesses_txt)) {
                $guess = fgets($fopen_guesses_txt);
                $_SESSION['guesses'][$i] = $guess;
                $i += 1;
            }
            fclose($fopen_guesses_txt);

            


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

            
        }
        elseif (isset($_POST["next"])) {
            $_SESSION['fails'] = 0;

            $fopen_playlist_name_txt = fopen("playlist_name.txt", "r");
            $playlist_name = fgets($fopen_playlist_name_txt);
            echo "<p ><div align=\"center\" style=\"color: white;\"><strong>Playlist:</strong> $playlist_name</div></p>";
            fclose($fopen_playlist_name_txt);

            srand(time());
            $rand_index = rand(0, count($_SESSION['songs'][1]) - 1);
            

            $fopen_answers_txt = fopen("answers.txt", "w");
            fwrite($fopen_answers_txt, "");
            fclose($fopen_answers_txt);
            $fopen_guesses_txt = fopen("guesses.txt", "w");
            fwrite($fopen_guesses_txt, "");
            fclose($fopen_guesses_txt);

            $fopen_answers_txt = fopen("answers.txt", "a");
            fwrite($fopen_answers_txt, $_SESSION['songs'][0][$rand_index]);
            fwrite($fopen_answers_txt, $_SESSION['songs'][1][$rand_index]);
            fwrite($fopen_answers_txt, $_SESSION['songs'][2][$rand_index]);
            fclose($fopen_answers_txt);


            for ($i = 0; $i < 6; $i++) {
                echo "<p><div class=\"w3-panel w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-white w3-round-large w3-padding-16 w3-center\" style=\"width:500px;margin:0 auto; color: white;\" class=\"panel panel-default\"><b>[\t\t\t]</b></div><p>";
            }
        }
        else {
            echo "<br/><br/><br/><br/><br/><br/><a href = \"musicle_home.php\">Back</a>";
        }

        
    ?>
    <div id="song_preview_uri" style="display: none">
    <?php
        $fopen_answers_txt = fopen("answers.txt", "r");
        $song_preview_uri = fgets($fopen_answers_txt);
        $song_preview_uri = "spotify:track:" . explode("track/", $song_preview_uri)[1];
        echo $song_preview_uri;
        $_SESSION['song_preview_uri'] = $song_preview_uri;
        
    ?>
    </div>

    <div id="fails" style="display: none">
        <?php echo $_SESSION['fails']; ?>
    </div>

    <script src="https://open.spotify.com/embed/iframe-api/v1" async></script>

    

    <div>
    <button onclick="togglePlay()" class="play w3-bottombar w3-leftbar w3-rightbar w3-topbar w3-border-green w3-button w3-green w3-circle" style="height: 100px; width: 100px; margin: 0 auto; display: table; justify-content: center;" data-spotify-id= <?php echo $song_preview_uri; ?> >
        <div id="play" style="margin: 0 auto; display: flex; justify-content: center; font-size: 200%;"><b>
            Play
        </b></div>
    </button>
    </div>
    

    <p id="progressTimestamp"></p>

    <script type="text/javascript">
        let div = document.getElementById("fails");
        let fails = div.textContent;
    
        
        
        let controller;

        window.onSpotifyIframeApiReady = IFrameAPI => {
            const element = document.getElementById('embed-iframe');
            options = {
                height: 0,
                width: 0,
                uri: "",
            };
            
            const callback = EmbedController => {
                controller = EmbedController;
                controller.addListener('ready', () => {
                    console.log('ready');
                });
                document.querySelectorAll('.play').forEach(
                episode => {
                    controller.loadUri(episode.dataset.spotifyId)
                })
                // this is the api call for showing the LIVE playback time, printing in the p with id progressTimestamp declared above the js code
                controller.addListener('playback_update', e => {
                    // document.getElementById('progressTimestamp').innerText = `${parseInt(e.data.position / 1000, 10)} s`;

                    if ((`${parseInt(e.data.position / 1000, 10)}` == 1) && (fails == 0)) {
                        controller.pause();
                    } 
                    if ((`${parseInt(e.data.position / 1000, 10)}` == 3) && (fails == 1)) {
                        controller.pause();
                    } 
                    if ((`${parseInt(e.data.position / 1000, 10)}` == 6) && (fails == 2)) {
                        controller.pause();
                    } 
                    if ((`${parseInt(e.data.position / 1000, 10)}` == 10) && (fails == 3)) {
                        controller.pause();
                    } 
                    if ((`${parseInt(e.data.position / 1000, 10)}` == 15) && (fails == 4)) {
                        controller.pause();
                    } 
                    if ((`${parseInt(e.data.position / 1000, 10)}` == 30) && (fails == 5)) {
                        controller.pause();
                    } 
                    
                     
                });
                controller.addListener('playback_update', e => {
                    if (e.data.isPaused) {
                        document.querySelector('button.play').onclick = function () {
                            controller.restart();
                        };
                    }
                });
                controller.addListener('playback_update', e => {
                    if (!e.data.isPaused) {
                        document.querySelector('button.play').onclick = function () {
                            controller.pause();
                        };
                    }
                });
            };
            
            IFrameAPI.createController(element, options, callback);
        };

        
        document.querySelector('button.play').onclick = function () {
            controller.resume();
        };
        
    </script>

    <?php 
        if ($_SESSION['fails'] == 0) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:4.3333333%;">1s</div>';
            echo '</div><br>';
        }
        if ($_SESSION['fails'] == 1) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:10%">3s</div>';
            echo '</div><br>';
        }
        if ($_SESSION['fails'] == 2) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:20%">6s</div>';
            echo '</div><br>';
        }
        if ($_SESSION['fails'] == 3) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:33.333333%">10s</div>';
            echo '</div><br>';
        }   
        if ($_SESSION['fails'] == 4) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:50%">15s</div>';
            echo '</div><br>';
        }
        if ($_SESSION['fails'] == 5) {
            echo '<div class="w3-light-grey w3-bottombar w3-border-gray" style="width: 45%; position: relative; left: 27.4%;">';
            echo '<div class="w3-container w3-green" style="width:100%">30s</div>';
            echo '</div><br>';
        }
    ?>
    
    
    
    <br>
    <form action = "musicle_game.php" method = "post">
        <div class="w3-cell-row" style="width: 20%; margin: 0 auto; display: table; justify-content: center;">
            <div class="w3-cell w3-margin-right">
                <input class="w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-green w3-round-large w3-padding" list="user_answer" name="user_answer" placeholder="Enter guess here" autocomplete ="off">
                    <datalist id="user_answer" name="user_answer">
                        <?php
                            
                            
                            
                            for ($i=0; $i < count($_SESSION['songs'][1]); $i++) { 
                                $song_name = $_SESSION['songs'][1][$i];
                                $artist_name = $_SESSION['songs'][2][$i];
                                echo "<option value=\"$song_name -- $artist_name\"></option>";
                            }

                            
                        
                            
                        ?>
                        <!-- <option value="test1">Test 1</option>
                        <option value="test2">Test 2</option>
                        <option value="test3">Test 3</option> -->
                    </datalist>
                </input>
            </div>

            <div class="w3-cell">
                <b><input class="w3-round-large w3-button w3-green w3-padding w3-margin-left w3-margin-right" style="height: 50px" type = "submit" name = "submit"></input></b>
            </div>

            <div class="w3-cell">
                <b><input class="w3-round-large w3-button w3-yellow w3-padding w3-margin-right" type = "submit" name = "skip" value = "Skip"></input></b>
            </div>

            <div class="w3-cell">
                <b><input class="w3-round-large w3-button w3-red w3-padding" type = "submit" name = "give_up" value = "Give Up"></input></b>
            </div>
        </div>
        <br>
        
    </form>
    
    
    <a href = "musicle_home.php"><div class="w3-round-large w3-button w3-red w3-padding" style="position: absolute; top: 4%; left: 80%"><b>Return</b></div></a>
    
    <div id="embed-iframe"></div>
</body>
    
    
    
</html>