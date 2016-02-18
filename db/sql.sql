ALTER TABLE  `Login` ADD  `last_login` DATE NULL DEFAULT NULL;
RENAME TABLE  `db9_aegee_pl`.`Login` TO  `db9_aegee_pl`.`users` ;
ALTER TABLE  `users` CHANGE  `login_id`  `id` INT( 10 ) NOT NULL AUTO_INCREMENT COMMENT  'id loginu';
ALTER TABLE  `users` ADD  `session_id` VARCHAR( 256 ) NULL DEFAULT NULL