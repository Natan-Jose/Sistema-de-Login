create database login;

use login;

CREATE TABLE credenciais (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  users varchar(255),
 password varchar(100),
  email varchar(255)   UNIQUE KEY
 
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE credenciais
ADD COLUMN blocked TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN block_expiration DATETIME NULL,
ADD COLUMN login_attempts INT NOT NULL DEFAULT 0;

select * from credenciais;

truncate credenciais;


describe credenciais;