<?php
/*
	===============================SESSION VARIABLES=============================================================
	
	$_POST['type']					   = Current Chosen Track
	$_SESSION['playlist'] 	   		   = Array of Titles ( Of Local Playlist )
	$_SESSION['playlist-path'] 		   = Array of Paths ( Of Local Playlist  )
	$_SESSION['title'] 				   = Title Of The Current Track chosen by User [When he hits Add To Playlist]
	$_SESSION['sort_by'] 			   = The Sort By Criteria (artist , album , play-time)
	$_SESSION['submittype']   	 	   = To ensure The Sort By Button is always Enabled
	$_SESSION['track'] 				   = Title Of The Current Track chosen by User [When he hits Add To Playlist]
	$_SESSION['path'] 				   = Path Of Track which is Chosen by User [When he hits Add To Playlist]
	$_SESSION['current_playlist']	   = Name of Loaded Playlist 
	$_SESSION['current_playlist_path'] = Paths of Tracks In The Loaded Playlist 

	=============================================================================================================
*/
require_once "config.php";
include 'getPathNames.php';
$title = "";
session_start(); 
//Check if User Choose To Add a Song To Playlist
if(isset($_POST['type'])) {

	if(isset($_SESSION['current_playlist'])) {
		$lines = $_SESSION['current_playlist_path'];
		$linepath = file($lines);
		foreach ($linepath as $name) {
			$line = explode("|", $name);
			$songname = $line[0];
			if($songname ===$_POST['type']){
				$path = $line[1];
				break;
			}
		}
	}
	else{
		//Split into Array , Based on - as delimiter
		$title_extract = explode(" - ", $_POST['type']);

		//Check if playlist array & playlist path array is initialized    
	    if(!isset($_SESSION['playlist']) && !isset($_SESSION['playlist-path'])) {
	    	//playlist array & playlist path array initialization
	    	$_SESSION['playlist'] = array();
	    	$_SESSION['playlist-path'] = array();
	    } 
	    //Add current track to session playlist array
	    array_push($_SESSION['playlist'],$title_extract[1]);
	    //Make playlist array unique  (Removing Duplicate Entries)   
		$playlist_array = array_unique($_SESSION['playlist']);
		$_SESSION['playlist'] = array_unique($playlist_array);

		//Set Other Parameters
	    $_SESSION['title'] = $_POST['type'];  
	    $title = $_SESSION['title'];               		
	    $selected_val = $_SESSION['sort_by']; 
		$_POST['submittype'] = $_SESSION['submittype'];
		$_POST['sort_by'] = $selected_val;
		$path = "";

		//Find path of current track & add it to a local variable "path"
		if ($selected_val === 'artist') {
			$myfile = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\artist_index_sort.txt');
			$i=0;
			foreach ($myfile as $line) { 
				$artist = explode("|", $line);
				if($i === 0) {
					$i++;
					continue;
				}
				$line = $artist[1];
				if($line===$title_extract[1]) {
					$path = $artist[2];
					break;
				}    
			}
		}
		elseif ($selected_val === 'album') {
			$myfile = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\album_index_sort.txt');
			$i=0;
			foreach ($myfile as $line) { 
				$artist = explode("|", $line);
				if($i === 0) {
					$i++;
					continue;
				}
				$line = $artist[1];
				if($line===$title_extract[1]) {
					$path = $artist[2];
					break;
				}  
			}
		}
		if ($selected_val === 'play-time') {
			$myfile = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\play_time_index_sort.txt');
			$i=0;
			foreach ($myfile as $line) { 
				$artist = explode("|", $line);
				if($i === 0) {
					$i++;
					continue;
				}
				$line = $artist[1];
				if($line===$title_extract[1]) {
					$path = $artist[2];
					break;
				}  
			}
		}	
	}				
	//Copy name and path to session variables 			
	$_SESSION['track'] = $_POST['type'];
	$_SESSION['path'] = $path;	
	array_push($_SESSION['playlist-path'],$path);
	//Make the playlist path array unique     
	$path_array = array_unique($_SESSION['playlist-path']);
	$_SESSION['playlist-path'] = array_unique($path_array);	
	$_SESSION['track'] = $_POST['type'];
}	
//Check's if Clear Playlist Button is enabled
if(isset($_POST['clear-playlist'])) {
		//Checks if any Playlist has been Loaded
		if(isset($_SESSION['current_playlist'])) {
			//Uninitialize the chosen playlist
			unset($_SESSION['current_playlist']);
			unset($_SESSION['current_playlist_path']);
		}

		//Clear the local chosen playlist
		unset($_SESSION['playlist']);
		unset($_SESSION['playlist-path']);

		//Set Other Parameters
		$selected_val = $_SESSION['sort_by']; 
		$_POST['submittype'] = $_SESSION['submittype'];
		$_POST['sort_by'] = $selected_val;
}	
?>

<!DOCTYPE html>
<html lang="en" ng-app>
<head>
	<meta charset="utf-8">

	<script type="text/javascript">
		var playlistsongs =  [];
	</script>

	<link rel="stylesheet" href="./public/css/app.css">
	<link rel="stylesheet" href="./public/css/bootstrap.css">
	<script type="text/javascript" src="./public/app.php?view=jsobject"></script>
	<script type="text/javascript" src="./public/app/js/appPlayer.js"></script>
	<script type="text/javascript" src="./public/player/audio.min.js"></script>
	<script type="text/javascript" src="./public/js/angular.min.js"></script>
	<script type="text/javascript" src="./public/app/js/app.js"></script>
	<script type="text/javascript" src="./public/app/js/controllers.js"></script>
	<script type="text/javascript" src="./public/app/js/filters.js"></script>
</script>
</head>
<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<table class="table table-bordered">
					<tr>
                        <td >
                            <div id="musicplayer">
                                    <?php if($music_player === 'flash') { ?>
                                        <audio id="player"></audio>
                                    <?php } else if( $music_player === 'native' ) {
	                                    		$songpath = $_SESSION['path'];
	                                    		$songpath1 = explode("/",$songpath);
	                                    		$songtitle = $songpath1[7];
	                                    		$songtitle = str_replace(" ","%20",$songtitle);
	                                    		$arg = "proxy.php?name=".$songtitle;
                                    	?>
                                        <audio controls id="player" class="native-player">
                                            <source src=<?=$arg?> type="audio/mpeg">
                                        </audio>
                                    <?php } ?>
                            </div>
                        </td>
                    </tr>
					<tr bgcolor="#A584ED">
						<th><h3>Current Playlist : <?php if(isset($_SESSION['current_playlist'])) echo $_SESSION['current_playlist']; 
													else echo "NONE"; ?></h3></th>
					</tr>
					<tr ng-repeat="song in directorysongs">
						<td ng-click="addSong(song)" class="link">
							{{song.name}}
						</td>
					</tr>
					<tr>
						<td>
							<?php 
									//Check's if any Playlist has been loaded 
									//If Loaded then print it's contents
									if(isset($_SESSION['current_playlist'])) {
										$handle = file($_SESSION['current_playlist_path']) or die("Unable to open file!");
										foreach ($handle as $name) { 
											$track_title = explode("|", $name);
											echo $track_title[0].'<br>';
										}
									}
									// Else Display the local Playlist
									else {

											if(isset($_SESSION['playlist'])) {
												for($i = 0; $i < count($_SESSION['playlist']); $i++) {
												    echo $_SESSION['playlist'][$i].'<br>';
												}
											}
									}
							?>
						</td>
					</tr>
				</table>

				<div class = "row-fluid">
					<div class="span4" id="albums">
						<table class="table table-bordered">
							<thead>
								<tr bgcolor="#A584ED	">
									<th><h3>My Music</h3></th>

								</tr>
							</thead>
									<form action="index.php" method="post">
										<tr>
										<td>
										<select name="type" id="type">
											<?php
											//Check the Sort By Criteria chosen by user
											if(isset($_POST['submittype'])) {												
												//Display The Songs based on SORT criteria
												if(isset($_POST['sort_by'])) {
											    	$selected_val = $_POST['sort_by'];
											    	//session_start();
											    	$_SESSION['sort_by'] = $selected_val;
											    	$_SESSION['submittype'] = $_POST['submittype'];
											    }  // Storing Selected Value In Variable
											    if($selected_val === 'artist') {
											    	$names = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\artist_index_sort.txt');
											    	foreach ($names as $name) { 
											    		$artist = explode("|", $name);
											    		$name = $artist[0]." - ".$artist[1];
											    		echo $name;
											    		?>
											    		<option value="<?= $name ?>"> <?= $name ?> </option>
											    		<?php 
											    	}
											    }
											    elseif ($selected_val === 'album') {
											    	$names = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\album_index_sort.txt');
											    	foreach ($names as $name) { 
											    		$album = explode("|", $name);
											    		$name = $album[0]." - ".$album[1];
											    		?>
											    		<option value="<?= $name ?>"> <?= $name ?> </option>
											    		<?php 
											    	}
											    }
											    elseif($selected_val === 'play-time') {
											    	$names = file('C:\xampp\htdocs\FS Project\fs\sort_by\index_files\play_time_index_sort.txt');
											    	foreach ($names as $name) { 
											    		$play = explode("|", $name);
											    		$name = $play[0]." - ".$play[1];
											    		?>
											    		<option value="<?= $name ?>"> <?= $name ?> </option>
											    		<?php 
											    	}
												}
											}
											else {
												$pathnames = array();
												$pathnames = $_SESSION['current_playlist_path'];
												$path = file($pathnames);
												foreach ($path as $name) { 
											    	$play = explode("|", $name);
											    	$name = $play[0];
												    ?>
													<option value="<?= $name ?>"> <?= $name ?></option>
													<?php
												}
											}
										?>
										    </td>
											</tr>
											<tr>
												<td>
													<div class="wrapper">
														<input type="submit" name="submit" class="btn" value="Play Song"/>
													</div>
												</td>
											</tr>

									</form>
						</table>
					</div>

					<div class="span4" >
						<table class="table table-bordered">
							<thead>
								<tr bgcolor="#A584ED">
									<th><h3>Playlist</h3></th>
								</tr>
							</thead>
							<tr>
								<td >
									<div class="wrapper">
										<form action="load_playlist.php" method="post">
											<button type="submit" name="load-playlist" class="btn" formaction="load_playlist.php">Load Playlist</button>
										</form>								
									</div>
									<div class="wrapper">
										<form action="save_playlist.php" method="post">
											<button type="submit" name="save-playlist" class="btn" formaction="save_playlist.php">Save Playlist</button>
										</form>								
									</div>
								</td>
							</tr>
							<tr>
								<td >
									<div class="wrapper">
										<form action="index.php" method="post">
											<button type="submit" name="clear-playlist" class="btn" formaction="index.php">Clear Playlist</button>
										</form>								
									</div>
								</td>
							</tr>
							<tr ng-repeat="song in playlistsongs">
								<td class="link">
									<div class="song current" ng-show="$index==currentSongIndex">{{song.name}}</div>
									<div class="song" ng-show="$index!=currentSongIndex">
										<span ng-click="playSong($index)">{{song.name}}</span>
										<span ng-click="deleteSong($index)" class="delete icon-trash"></span>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="span4" >
						<table class="table table-bordered">
							<thead>
								<tr bgcolor="#A584ED">
									<th><h3>Sort By</h3></th>
								</tr>
							</thead>
							<tbody>
								<form action="index.php" method="post">
									<tr>
										<td>
											<div class="radio">
												<label><input type="radio" id='regular' name="sort_by" <?php if (isset($_POST['sort_by']) && $_POST['sort_by']=="artist") echo "checked";?> value="artist">Artist</label>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="radio">
												<label><input type="radio" id='express' name="sort_by" <?php if (isset($_POST['sort_by']) && $_POST['sort_by']=="album") echo "checked";?> value="album">Album</label>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="radio">
												<label><input type="radio" id='express' name="sort_by" <?php if (isset($_POST['sort_by']) && $_POST['sort_by']=="play-time") echo "checked";?> value="play-time">Play-Time</label>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="wrapper">
												<input type="submit" name="submittype" class="btn" value="SORT"/>
											</div>
										</td>
									</tr>
								</form>
								<tr >

								</tr>
							</tbody>


						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var currentSongIndex = 0;
		if(config.musicPlayer == 'flash') {
			appPlayer.player = audiojs.newInstance(document.getElementById("player"));
		} else if( config.musicPlayer == 'native'){
			appPlayer.player = appPlayer.nativePlayer();
		}
		function changeVolume(n) {
			var player = document.getElementById("player");
			player.volume = n;
		}
	</script>

</body>
</html>