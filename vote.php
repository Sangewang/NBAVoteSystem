<?php
  $team   = $_REQUEST['team'];
  $player = $_POST['player'];
  echo "You choose champion team is $team.</br>";
  echo "You choose best play is $player.</br>";
  @$db_conn_team = new mysqli('localhost','NBA','NBA','NBATeam');
  @$db_conn_play = new mysqli('localhost','NBA','NBA','NBAPlayer'); 
  if(!$db_conn_team)
  {
    echo "Could not connect to db_team <br />";
    exit;
  }

  if(!$db_conn_play)
  {
    echo "Could not connect to db_play <br />";
  }
  if(!empty($team) && !empty($player))
  {
    $team   = addslashes($team);
    $player = addslashes($player);
    $query_team = "update NBAChampionTeam 
                   set teamVotes = teamVotes + 1
                   where teamName = '$team'";

    $query_play = "update NBABestPlayer
                   set playerVotes = playerVotes + 1
                   where playerName = '$player'";

    $result_team = @$db_conn_team->query($query_team);
    $result_play = @$db_conn_play->query($query_play);
    if(!$result_team)
    {
      echo 'Could not connect to db_team<br />';
      exit;
    }

    if(!$result_play)
    {
      echo 'Could not connect to db_play<br />';
      exit;
    }
    echo "Insert Record Succeed!";
  }

?>
