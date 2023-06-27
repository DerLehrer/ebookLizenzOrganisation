# ebookLizenzOrganisation
a website for the management of licenses to be distributed to registered users using html, datatables, javascript, php, phpmailer, sql<br>
language: German

features:
- user registration with bcrypted password and resetting option
- frontend-administration for datasets of users, books, licenses and assignments (ordered->licenses) incl. upload of datasets from files, in-line changing of attribute-values in datasets and of base data, assignment of licenses to orders,
- user-frontend: registration, login, ordering licenses for available books
- send mass mailing via phpmailer to registered users

setup for use:
- Use a webserver like Apache and a sql database
- create database using CREATE_DB.sql from the repository
- copy all files from the repository to the webserver
- create a file infodat.ini and place it in the folder "php":<br>
  mailversandadresse ="yourfrommailadress"<br>
  mailusername = "yourmailserverlogin"<br>
  mailpassword = "yourmailserverpassword"<br>
  mailhost = "yourmailserverdomain"<br>
  auth = "TRUE"<br>
  sec = "tls"<br>
  mailport = 25<br>
  dbhost = 127.0.0.1<br>
  db = "yourdatabasename"<br>
  dbusername = "yourdatabaseusername"<br>
  dbpassword = "yourdatabasepassword"<br>
  dbport =3306<br>
- in the database change the existing entry of table "benutzer": enter your email-adress (with phpMyAdmin for example)
- open the website to register as Admin using yourdomain/Registrierung.php?name=youremailadress (for example: localhost/Registrierung.php?name=mymail@gmx.de)<br>
  You have to use "Verwalter" as admin-login (!), choose your own password.<br>
- Now you are ready to go!<br>
- read Anleitung.docx to get started using the website (at the moment in German only)
