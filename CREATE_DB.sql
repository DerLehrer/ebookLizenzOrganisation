create database ebooks;
use ebooks;
Create table benutzer (Email varchar(100) COLLATE utf8mb4_general_ci primary key, Name varchar(255) COLLATE utf8mb4_general_ci, Klasse varchar(50) COLLATE utf8mb4_general_ci, SchuelerVname varchar(255) COLLATE utf8mb4_general_ci, SchuelerNname varchar(255) COLLATE utf8mb4_general_ci , RenewPW  varchar(255) COLLATE utf8mb4_general_ci, Eingeladen tinyint default 0, Gesetzt tinyint default 0, Hashcode varchar(255) COLLATE utf8mb4_general_ci, Renew datetime default NULL)  ENGINE = INNODB;;
Create table buch (Buch varchar(100) COLLATE utf8mb4_general_ci primary key, Stufe int, Fach varchar(255) COLLATE utf8mb4_general_ci, Verlag  varchar(255) COLLATE utf8mb4_general_ci, Preis Decimal(6,2))  ENGINE = INNODB;;
Create table codes (BuchId varchar(100) COLLATE utf8mb4_general_ci, Codes varchar(100) COLLATE utf8mb4_general_ci, Anzahl_max int not null default 1, primary key (Codes, BuchId))  ENGINE = INNODB;;
Create table bestellung (BestellerId varchar(100) COLLATE utf8mb4_general_ci , BuchId varchar(100) COLLATE utf8mb4_general_ci, Datum datetime,  Code varchar(100) COLLATE utf8mb4_general_ci,primary key (BestellerId, BuchId))  ENGINE = INNODB;;
Alter table codes add constraint fkBuchIDcode foreign key (BuchId) references buch(Buch) on delete restrict on update cascade;
Alter table bestellung add constraint fkCode foreign key (Code) references codes(Codes) on delete restrict on update cascade;
Alter table bestellung add constraint fkBenutzerID foreign key (BestellerID) references benutzer(Email) on delete restrict on update cascade;
Alter table bestellung add constraint fkBuchIDbest foreign key (BuchID) references buch(Buch) on delete restrict on update cascade;
Create table schuldaten (Sperre date, Schulname varchar(100) COLLATE utf8mb4_general_ci primary key , Direktor varchar(100) COLLATE utf8mb4_general_ci, Admin varchar(100) COLLATE utf8mb4_general_ci, Strasse varchar(255) COLLATE utf8mb4_general_ci, PLZ int, Ort varchar(100) COLLATE utf8mb4_general_ci, Email varchar(100) COLLATE utf8mb4_general_ci, Einladung Text(65535) COLLATE utf8mb4_general_ci )  ENGINE = INNODB;;
Insert into benutzer VALUES ('administrator@mailadresse.de', default,default,default,default,default, default, default, default, default);