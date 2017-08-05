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

class Flexfaq extends Module {
	protected $config_form = false;

	public function __construct() {
		$this->name          = 'flexfaq';
		$this->tab           = 'content_management';
		$this->version       = '0.0.1';
		$this->author        = 'Antonio Rossetti';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l( 'Flex FAQ' );
		$this->description = $this->l( 'Flexible FAQ & Products FAQ' );

		$this->confirmUninstall = $this->l( '' );

		$this->ps_versions_compliancy = array( 'min' => '1.6', 'max' => _PS_VERSION_ );
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install() {
		Configuration::updateValue( 'FLEXFAQ_LIVE_MODE', false );

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
		       $this->registerHook( 'header' ) &&
		       $this->registerHook( 'backOfficeHeader' ) &&
		       $this->registerHook( 'displayProductExtraContent' );
	}

	public function uninstall() {
		Configuration::deleteByName( 'FLEXFAQ_LIVE_MODE' );

		include( dirname( __FILE__ ) . '/sql/uninstall.php' );

		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent() {
		/**
		 * If values have been submitted in the form, process.
		 */
		if ( ( (bool) Tools::isSubmit( 'submitFlexfaqModule' ) ) == true ) {
			$this->postProcess();
		}

		$this->context->smarty->assign( 'module_dir', $this->_path );

		$output = $this->context->smarty->fetch( $this->local_path . 'views/templates/admin/configure.tpl' );

		return $output . $this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm() {
		$helper = new HelperForm();

		$helper->show_toolbar             = false;
		$helper->table                    = $this->table;
		$helper->module                   = $this;
		$helper->default_form_language    = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0 );

		$helper->identifier    = $this->identifier;
		$helper->submit_action = 'submitFlexfaqModule';
		$helper->currentIndex  = $this->context->link->getAdminLink( 'AdminModules', false )
		                         . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token         = Tools::getAdminTokenLite( 'AdminModules' );

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
		);

		return $helper->generateForm( array( $this->getConfigForm() ) );
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm() {
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l( 'Settings' ),
					'icon'  => 'icon-cogs',
				),
				'input'  => array(
					array(
						'type'    => 'switch',
						'label'   => $this->l( 'Live mode' ),
						'name'    => 'FLEXFAQ_LIVE_MODE',
						'is_bool' => true,
						'desc'    => $this->l( 'Use this module in live mode' ),
						'values'  => array(
							array(
								'id'    => 'active_on',
								'value' => true,
								'label' => $this->l( 'Enabled' )
							),
							array(
								'id'    => 'active_off',
								'value' => false,
								'label' => $this->l( 'Disabled' )
							)
						),
					),
					array(
						'col'    => 3,
						'type'   => 'text',
						'prefix' => '<i class="icon icon-envelope"></i>',
						'desc'   => $this->l( 'Enter a valid email address' ),
						'name'   => 'FLEXFAQ_ACCOUNT_EMAIL',
						'label'  => $this->l( 'Email' ),
					),
					array(
						'type'  => 'password',
						'name'  => 'FLEXFAQ_ACCOUNT_PASSWORD',
						'label' => $this->l( 'Password' ),
					),
				),
				'submit' => array(
					'title' => $this->l( 'Save' ),
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues() {
		return array(
			'FLEXFAQ_LIVE_MODE'        => Configuration::get( 'FLEXFAQ_LIVE_MODE', true ),
			'FLEXFAQ_ACCOUNT_EMAIL'    => Configuration::get( 'FLEXFAQ_ACCOUNT_EMAIL', 'contact@prestashop.com' ),
			'FLEXFAQ_ACCOUNT_PASSWORD' => Configuration::get( 'FLEXFAQ_ACCOUNT_PASSWORD', null ),
		);
	}

	/**
	 * Save form data.
	 */
	protected function postProcess() {
		$form_values = $this->getConfigFormValues();

		foreach ( array_keys( $form_values ) as $key ) {
			Configuration::updateValue( $key, Tools::getValue( $key ) );
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookBackOfficeHeader() {
		if ( Tools::getValue( 'module_name' ) == $this->name ) {
			$this->context->controller->addJS( $this->_path . 'views/js/back.js' );
			$this->context->controller->addCSS( $this->_path . 'views/css/back.css' );
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader() {
		$this->context->controller->addJS( $this->_path . '/views/js/front.js' );
		$this->context->controller->addCSS( $this->_path . '/views/css/front.css' );
	}

	public function hookDisplayProductExtraContent() {
		/* Place your code here. */
	}
}