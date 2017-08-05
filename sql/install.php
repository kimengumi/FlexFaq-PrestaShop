<?php
/**
 * FlexFaq - Flexible FAQ and product FAQ for PrestaShop
 *
 * Copyright 2017 Antonio Rossetti (https://www.kimengumi.fr)
 *
 * Licensed under the EUPL, Version 1.1 or – as soon they will be approved by
 * the European Commission - subsequent versions of the EUPL (the "Licence");
 * You may not use this work except in compliance with the Licence.
 * You may obtain a copy of the Licence at:
 *
 * https://joinup.ec.europa.eu/software/page/eupl
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the Licence is distributed on an "AS IS" basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions and
 * limitations under the Licence.
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexfaq` (
    `id_flexfaq` INT(11) NOT NULL AUTO_INCREMENT,
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `common` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	`date_add` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`date_upd` DATETIME ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id_flexfaq`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'flexfaq_shop (
	`id_flexfaq` INT(10) NOT NULL AUTO_INCREMENT,
	`id_shop` int(10) DEFAULT NULL,
	PRIMARY KEY(`id_flexfaq`, `id_shop`)
	)ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'flexfaq_lang (
	`id_flexfaq` INT(10) NOT NULL AUTO_INCREMENT,
	`id_lang` INT(10) NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`content` TEXT,
	PRIMARY KEY(`id_flexfaq`, `id_lang`)
	)ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'flexfaq_product (
	`id_flexfaq` INT(10) NOT NULL AUTO_INCREMENT,
	`id_product` INT(10) NOT NULL,
	PRIMARY KEY(`id_flexfaq`, `id_product`)
	)ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';


foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
