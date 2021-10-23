# cs-teams
The project is a responsive WEB-APP built with docker and deployed with heroku: http://csteams.herokuapp.com

Web application designed to be used as site for finding other people willing to party up and play together in CS:GO. It provides rating system for evaluation of player behavior and skills. Furthermore users may text each other to get to know someone better and arrange a game.

## technologies
- html, css
- javaScript
- php
- postgreSQL

## security
Project provides authentication and authorization.
Instead of $_SESSION it uses $_COOKIES and database - hashed token is put into both cookies and 
database. With each request app checks in db if passed cookie is valid and not expired, and if it is necessary - checks user's role.

## features
- login, register
- profile edition, viewing other users profiles
- Rating other users by specified traits
- Each user has assigned elo points that are calculated based on their ratings
- Filtering users by their rank and elo points
- Chatting with other users

## erd diagram
<img src="https://user-images.githubusercontent.com/64193740/105896036-9b8a2100-6016-11eb-8f7f-5d8ead95a2a7.png" width="1000">


## preview
If for any reason http://csteams.herokuapp.com won't work here are example screens:

<img src="https://user-images.githubusercontent.com/64193740/105900840-e0b15180-601c-11eb-89ab-cde8ea4d788a.png" width="1000" display="inline">
<img src="https://user-images.githubusercontent.com/64193740/105901111-2d952800-601d-11eb-9ea0-ef00b36774f4.png" width="1000" display="inline">
<img src="https://user-images.githubusercontent.com/64193740/105900659-a647b480-601c-11eb-8201-a54813ef13ea.png" width="1000">

<p float="left">
  <img src="https://user-images.githubusercontent.com/64193740/105901742-1571d880-601e-11eb-9354-771e15ffc11d.png" width="300" />
  <img src="https://user-images.githubusercontent.com/64193740/105901746-160a6f00-601e-11eb-9205-f7c02055f8bb.png" width="300" /> 
  <img src="https://user-images.githubusercontent.com/64193740/105901748-160a6f00-601e-11eb-8a25-39384f3678e1.png" width="300" />
</p>
