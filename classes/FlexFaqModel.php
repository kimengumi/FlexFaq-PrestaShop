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
if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class FlexFaqModel extends ObjectModelCore {

	public $id_flexfaq;
	public $active;
	public $common;
	public $title;
	public $content;

	public static $definition = array(
		'table'          => 'flexfaq',
		'primary'        => 'id_flexfaq',
		'multilang'      => true,
		'fields'         => array(
			'active'   => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool'
			),
			'common'   => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool'
			),
			'position' => array(
				'type' => self::TYPE_INT
			),
			'title'    => array(
				'type'     => self::TYPE_STRING,
				'required' => true,
				'lang'     => true,
				'validate' => 'isGenericName',
				'size'     => 255
			),
			'content'  => array(
				'type'     => self::TYPE_HTML,
				'required' => true,
				'lang'     => true,
				'validate' => 'isCleanHtml',
				'size'     => 3999999999999
			)
		)
	);

	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {

		Shop::addTableAssociation('flexfaq',array('type' => 'shop'));

		return parent::__construct( $id, $id_lang, $id_shop );
	}

}