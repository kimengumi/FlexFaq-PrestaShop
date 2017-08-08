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

require_once _PS_MODULE_DIR_ . 'flexfaq/classes/FlexFaqModel.php';

class Flexfaq extends Module {

	public function __construct() {
		$this->name          = 'flexfaq';
		$this->tab           = 'content_management';
		$this->version       = '0.0.1';
		$this->author        = 'Antonio Rossetti';
		$this->need_instance = 0;
		$this->bootstrap     = true;

		parent::__construct();

		$this->displayName = $this->l( 'Flex FAQ' );
		$this->description = $this->l( 'Flexible FAQ & Products FAQ' );

		$this->confirmUninstall = $this->l( 'Are you sure yuou want to uninstall the module ?' );

		$this->ps_versions_compliancy = array( 'min' => '1.6', 'max' => _PS_VERSION_ );
	}

	/**
	 * Insert module into datable
	 */
	public function install() {

		include( dirname( __FILE__ ) . '/sql/install.php' );

		// Module Tab
		$tab             = new Tab();
		$tab->active     = 1;
		$tab->class_name = 'AdminFlexfaq';
		$tab->name       = array();
		foreach ( Language::getLanguages( true ) as $lang ) {
			$tab->name[ $lang['id_lang'] ] = 'Flex FAQ';
		}
		$tab->module    = $this->name;
		$tab->id_parent = (int) Tab::getIdFromClassName( 'AdminCatalog' );

		return parent::install() &&
		       $tab->add() &&
		       $this->registerHook( 'productFooter' );
	}

	/**
	 * Delete module from datable
	 *
	 * @return bool result
	 */
	public function uninstall() {

		include( dirname( __FILE__ ) . '/sql/uninstall.php' );


		// Module Tab
		if ( $id_tab = (int) Tab::getIdFromClassName( 'AdminFlexfaq' ) ) {
			$tab = new Tab( $id_tab );
			$tab->delete();
		}

		return parent::uninstall();
	}

	/**
	 * Display in product page
	 *
	 * @param $params
	 *
	 * @return string
	 */
	public function hookProductFooter( $params ) {

		$this->context->smarty->assign( 'faqs',
			FlexFaqModel::getCollectionByProductId(
				$params['product']->id,
				$params['product']->id_category_default,
				$params['cookie']->id_lang
			, true ));

		return $this->display(__FILE__, 'productfooter.tpl');

	}
}
