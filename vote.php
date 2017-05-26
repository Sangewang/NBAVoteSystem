<?php
  /*************************************************************************
    1.Database query to get vote info
  **************************************************************************/
  $team   = $_REQUEST['team'];
  $player = $_POST['player'];
/*  echo "You choose champion team is $team.</br>";
  echo "You choose best play is $player.</br>";*/
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
    //echo "Insert Record Succeed!</br>";
  }
  
  $query_team = 'select * from NBAChampionTeam';
  $query_play = 'select * from NBABestPlayer';

  $result_team = @$db_conn_team->query($query_team);
  $result_play = @$db_conn_play->query($query_play);

  if(!$result_team || !$result_play)
  {
    echo 'Could not connect to db</br >';
    exit;
  }
  $total_teams = $result_team->num_rows;
  $total_plays = $result_play->num_rows;
  $total_team_votes = 0;
  $total_player_votes = 0;

  while($row_team = $result_team->fetch_object())
  {
    $total_team_votes += $row_team->teamVotes;
  }

  while($row_play = $result_play->fetch_object())
  {
    $total_play_votes += $row_play->playerVotes;
  }
  /*
  echo "Total Teams is $total_teams<br/>";
  echo "Total Plays is $total_plays<br/>";
  echo "Total Team Votes is $total_team_votes<br/>";
  echo "Total Player Votes is $total_play_votes<br/>";*/
  $result_team->data_seek(0);
  $result_play->data_seek(0); 

  
  /*************************************************************************
    2.Initial calculations for graph
  **************************************************************************/
  //set up contents
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  //echo $DOCUMENT_ROOT."<br>";
  $width = 500;
  $left_margin = 100;
  $right_margin = 50;
  $bar_height = 40;
  $bar_spacing = $bar_height/2;
  $font = $DOCUMENT_ROOT.'/vote/ARIAL.TTF';
  $title_size = 16;
  $main_size = 12;
  $small_size = 12;
  $text_indet = 10;
  $x = $left_margin + 60;
  $y = 50;
  $bar_unit = ($width-$x-$right_margin)/100;
  $height = ($bar_height + $bar_spacing) * ($total_teams + $total_plays) + 50; 

  /*************************************************************************
    3.Set Up Base Image
  **************************************************************************/
  //create a blank canvas
  $im = imagecreatetruecolor($width,$height);

  $white = imagecolorallocate($im,255,255,255);
  $blue  = imagecolorallocate($im,0,64,128);
  $black = imagecolorallocate($im,0,0,0);
  $pink  = imagecolorallocate($im,255,78,243);
  $red   = imagecolorallocate($im,255,0,0);

  $text_color = $black;
  $percent_color = $black;
  $bg_color = $white;
  $line_color = $black;
  $team_color = $blue;
  $number_color = $pink;
  $play_color = $red;
  imagefilledrectangle($im,0,0,$width,$height,$bg_color);
  imagerectangle($im,0,0,$width-1,$height-1,$line_color);

  $title = "NBA Chamption & MVP Vote Results";
  $title_dimensions = imagettfbbox($title_size,0,$font,$title);
  /*
  for($i=0;$i<=7;$i++)
  {
    echo "$title_dimensions[$i] ";
  }*/

  $title_length = $title_dimensions[2] - $title_dimensions[0];
  $title_height = abs($title_dimensions[7] - $title_dimensions[1]);
  $title_above_line = abs($title_dimensions[7]);
 
  $title_x = ($width - $title_length)/2;
  $title_y = ($y - $title_height)/2 + $title_above_line;
  /*echo "title_above_line = $title_above_line<br>";
  echo "title_x = $title_x<br>";
  echo "title_y = $title_y<br>";*/
  imagettftext($im,$title_size,0,$title_x,$title_y,$text_color,$font,$title);
  imageline($im,$x,$y-5,$x,$height-15,$line_color);
 
  /*************************************************************************
    4.Draw data into graph
  **************************************************************************/
  //Get each line of db data and draw corresponding bars
  
  while($row_team = $result_team->fetch_object())
  {
    if($total_team_votes > 0)
    {
      $percent = intval(($row_team->teamVotes/$total_team_votes)*100);
    }
    else
    {
      $percent = 0;
    }

    $percent_dimensions = imagettfbbox($main_size,0,$font,$percent.'%');
    $percent_length = $percent_dimensions[2] - $percent_dimensions[0];
    imagettftext($im,$main_size,0,$width-$percent_length-$text_indent,$y+($bar_height/2),$percent_color,$font,$percent.'%');

    $bar_length = $x + ($percent * $bar_unit);

    imagefilledrectangle($im,$x,$y-2,$bar_length,$y+$bar_height,$team_color);

    imagettftext($im,$small_size,0,$text_indent,$y+($bar_height/2),$text_color,$font,"$row_team->teamName");

    imagerectangle($im,$bar_length+1,$y-2,($x+(100*$bar_unit)),$y+$bar_height,$line_color);

    imagettftext($im,$small_size,0,$x+(100*$bar_unit)-50,$y+($bar_height/2),$number_color,$font,$row_team->teamVotes.'/'.$total_team_votes);

    $y += $bar_height + $bar_spacing;
    
  }

  while($row_play = $result_play->fetch_object())
  {
    if($total_play_votes > 0)
    {
      $percent = intval(($row_play->playerVotes/$total_play_votes)*100);
    }
    else
    {
      $percent = 0;
    }

    $percent_dimensions = imagettfbbox($main_size,0,$font,$percent.'%');
    $percent_length = $percent_dimensions[2] - $percent_dimensions[0];
    imagettftext($im,$main_size,0,$width-$percent_length-$text_indent,$y+($bar_height/2),$percent_color,$font,$percent.'%');

    $bar_length = $x + ($percent * $bar_unit);

    imagefilledrectangle($im,$x,$y-2,$bar_length,$y+$bar_height,$play_color);

    imagettftext($im,$small_size,0,$text_indent+10,$y+($bar_height/2),$text_color,$font,"$row_play->playerName");

    imagerectangle($im,$bar_length+1,$y-2,($x+(100*$bar_unit)),$y+$bar_height,$line_color);

    imagettftext($im,$small_size,0,$x+(100*$bar_unit)-50,$y+($bar_height/2),$number_color,$font,$row_play->playerVotes.'/'.$total_play_votes);

    $y += $bar_height + $bar_spacing;
  }
  Header('Content-type:image/png');
  imagepng($im);

  imagedestroy($im);
?>
