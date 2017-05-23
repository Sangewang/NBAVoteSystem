drop database NBATeam;
drop database NBAPlayer;

create database NBATeam;

use NBATeam;

create table NBAChampionTeam(
  teamId int unsigned not null primary key,
  teamName char(30),
  teamVotes int unsigned);

insert into NBAChampionTeam values
  (1,"Cleveland Cavaliers",0),
  (2,"Golden State Warriors",0);

create database NBAPlayer;

use NBAPlayer;

create table NBABestPlayer(
  teamId int unsigned not null,
  playerName varchar(30),
  playerVotes int);

insert into NBABestPlayer values
  (1,"LeBron James",0),
  (1,"Kyrie Irving",0),
  (1,"Kevin Love",0),
  (2,"Kevin Durant",0),
  (2,"Stephen Curry",0),
  (2,"Klay Thompson",0);


grant all privileges
on NBATeam.* 
to NBA@localhost
identified by 'NBA';

grant all privileges
on  NBAPlayer.*
to NBA@localhost
identified by 'NBA';
