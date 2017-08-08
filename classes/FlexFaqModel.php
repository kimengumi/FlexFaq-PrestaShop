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

	public static $definition = array(
		'table'     => 'flexfaq',
		'primary'   => 'id_flexfaq',
		'multilang' => true,
		'fields'    => array(
			'active'   => array(
				'type'     => self::TYPE_BOOL,
				'required' => true,
				'validate' => 'isBool'
			),
			'common'   => array(
				'type'     => self::TYPE_BOOL,
				'required' => true,
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
	public $id_flexfaq;
	public $active;
	public $common;
	public $title;
	public $content;
	public $categories;
	public $products;

	/**
	 * Builds the object
	 *
	 * @param int|null $id If specified, loads and existing object from DB (optional).
	 * @param int|null $id_lang Required if object is multilingual (optional).
	 * @param int|null $id_shop ID shop for objects with multishop tables.
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {

		Shop::addTableAssociation( 'flexfaq', array( 'type' => 'shop' ) );

		return parent::__construct( $id, $id_lang, $id_shop );
	}

	/**
	 * Get all Faqs related to a product ID
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function getCollectionByProductId( $id_product, $id_category_default, $id_lang = null ) {

		if ( ! $id_lang ) {
			$id_lang = Context::getContext()->language->id;
		}

		$sql = '
			SELECT f.id_flexfaq,fl.title,fl.content  
			FROM `' . _DB_PREFIX_ . 'flexfaq` f
			LEFT JOIN `' . _DB_PREFIX_ . 'flexfaq_product` fp ON fp.id_flexfaq=f.id_flexfaq
			LEFT JOIN `' . _DB_PREFIX_ . 'flexfaq_category` fc ON fc.id_flexfaq=f.id_flexfaq
			LEFT JOIN `' . _DB_PREFIX_ . 'flexfaq_lang` fl ON fl.id_flexfaq=f.id_flexfaq AND fl.id_lang= ' . (int) $id_lang . '
			INNER JOIN `' . _DB_PREFIX_ . 'flexfaq_shop` fs ON fs.id_flexfaq=f.id_flexfaq AND fs.id_shop= ' . (int) Shop::getContextShopID() . '
			WHERE f.active=1
			AND (fp.id_product = ' . (int) $id_product . ' OR fc.id_category = ' . (int) $id_category_default . ')
			ORDER BY f.position ASC';

		return Db::getInstance()->ExecuteS( $sql );

	}

	/**
	 * Get all Faqs related to the common page ID
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function getCommonCollection( $id_lang = null ) {

		if ( ! $id_lang ) {
			$id_lang = Context::getContext()->language->id;
		}

		$sql = '
			SELECT f.id_flexfaq,fl.title,fl.content  
			FROM `' . _DB_PREFIX_ . 'flexfaq` f
			LEFT JOIN `' . _DB_PREFIX_ . 'flexfaq_lang` fl ON fl.id_flexfaq=f.id_flexfaq AND fl.id_lang= ' . (int) $id_lang . '
			INNER JOIN `' . _DB_PREFIX_ . 'flexfaq_shop` fs ON fs.id_flexfaq=f.id_flexfaq AND fs.id_shop= ' . (int) Shop::getContextShopID() . '
			WHERE f.active=1
			AND f.common=1
			ORDER BY f.position ASC';

		return Db::getInstance()->ExecuteS( $sql );

	}


	/**
	 * Adds current object to the database
	 *
	 * @param bool $auto_date
	 * @param bool $null_values
	 *
	 * @return bool Insertion result
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function add( $auto_date = true, $null_values = false ) {
		$return = parent::add( $auto_date, $null_values );

		if ( $return !== false ) {
			$this->setAssociatedCategories( $this->categories );
			$this->setAssociatedProducts( $this->products );
		}

		return $return;
	}

	/**
	 * Save associated categories for current object
	 *
	 * @param $categories array
	 */
	public function setAssociatedCategories( $categories ) {

		$this->cleanAssociatedCategories();

		foreach ( $categories as $category ) {
			Db::getInstance()->insert( 'flexfaq_category', array(
				'id_flexfaq'  => (int) $this->id,
				'id_category' => (int) $category
			) );
		}


	}

	/**
	 * Clean associated categories for current object
	 *
	 * @return bool
	 */
	public function cleanAssociatedCategories() {
		return Db::getInstance()->delete( 'flexfaq_category', 'id_flexfaq = ' . (int) $this->id );
	}

	/**
	 * Save associated products for current object
	 *
	 * @param $products array
	 */
	public function setAssociatedProducts( $products ) {

		$this->cleanAssociatedProducts();

		foreach ( $products as $product ) {
			Db::getInstance()->insert( 'flexfaq_product', array(
				'id_flexfaq' => (int) $this->id,
				'id_product' => (int) $product
			) );
		}


	}

	/**
	 * Clean associated products for current object
	 *
	 * @return bool
	 */
	public function cleanAssociatedProducts() {
		return Db::getInstance()->delete( 'flexfaq_product', 'id_flexfaq = ' . (int) $this->id );
	}

	/**
	 * Updates the current object in the database
	 *
	 * @param bool $null_values
	 *
	 * @return bool
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function update( $null_values = false ) {
		$return = parent::update( $null_values );

		if ( $return !== false ) {
			$this->setAssociatedCategories( $this->categories );
			$this->setAssociatedProducts( $this->products );
		}

		return $return;
	}

	/**
	 * Deletes current object from database
	 *
	 * @return bool True if delete was successful
	 * @throws PrestaShopException
	 */
	public function delete() {
		$return = parent::delete();

		if ( $return !== false ) {
			$this->cleanAssociatedCategories();
			$this->cleanAssociatedProducts();
		}

		return $return;
	}

	/**
	 * Get Associated Products for current object
	 *
	 * @return array
	 */
	public function getAssociatedProducts() {

		$products = array();

		if ( ! $this->id ) {
			return $products;
		}

		$db_products = Db::getInstance()->ExecuteS( '
			SELECT id_product 
			FROM `' . _DB_PREFIX_ . 'flexfaq_product`
			WHERE id_flexfaq = ' . (int) $this->id );

		foreach ( $db_products as $product ) {
			$products[] = (int) $product['id_product'];
		}

		return $products;
	}

	/**
	 * Get Associated Products for current object
	 *
	 * @return array
	 */
	public function getAssociableProducts() {

		$associated_categories = $this->getAssociatedCategories();

		return Db::getInstance()->ExecuteS( '
			SELECT DISTINCT p.id_product, CONCAT (p.reference," - ",pl.name) AS name 
			FROM `' . _DB_PREFIX_ . 'product` p
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
			ON pl.id_product = p.id_product 
			AND pl.id_lang = ' . (int) Context::getContext()->language->id . '
			' . ( count( $associated_categories ) ?
				' WHERE p.id_category_default NOT IN (' . implode( ',', $associated_categories ) . ')' : ' ' ) . '
			ORDER BY name ASC' );

	}

	/**
	 * Get Associated Categories for current object
	 *
	 * @return array
	 */
	public function getAssociatedCategories() {

		$categories = array();

		if ( ! $this->id ) {
			return $categories;
		}

		$db_categories = Db::getInstance()->ExecuteS( '
			SELECT id_category 
			FROM `' . _DB_PREFIX_ . 'flexfaq_category`
			WHERE id_flexfaq = ' . (int) $this->id );

		foreach ( $db_categories as $category ) {
			$categories[] = (int) $category['id_category'];
		}

		return $categories;
	}
}