/**
* Script para crear una base de datos y sus tablas 
* para ser usada durante el ejemplo de CRUD.
* 
*/

DROP DATABASE IF EXISTS hivedb;
CREATE DATABASE hivedb;

/*Tabla de héroes*/
CREATE TABLE IF NOT EXISTS heroe (
	heroe_id int(5) NOT NULL AUTO_INCREMENT,
	nombre varchar(100) DEFAULT NULL, -- Su nombre de héroe
	identidad varchar(100) DEFAULT NULL, -- Su nombre real
	lugar_origen varchar(150) DEFAULT NULL, -- La ciudad que protege
	PRIMARY KEY (heroe_id)
);
