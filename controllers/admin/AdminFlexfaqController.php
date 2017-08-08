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

require_once _PS_MODULE_DIR_ . 'flexfaq/classes/FlexFaqModel.php';

class AdminFlexfaqController extends ModuleAdminController {

	public function __construct() {

		$this->context          = Context::getContext();
		$this->bootstrap        = true;
		$this->table            = 'flexfaq';
		$this->className        = 'FlexFaqModel';
		$this->identifier       = 'id_flexfaq';
		$this->lang             = true;
		$this->requiredDatabase = true;
		$this->addRowAction( 'edit' );
		$this->addRowAction( 'delete' );

		parent:: __construct();

		$this->bulk_actions = array(
			'delete' => array(
				'text'    => $this->l( 'Delete selected' ),
				'confirm' => $this->l( 'Delete selected items?' ),
				'icon'    => 'icon-trash'
			)
		);
		$this->fields_list  = array(
			$this->identifier => array(
				'title' => '#',
			),
			'title'           => array(
				'title' => $this->l( 'Title' ),
			),
			'common'          => array(
				'title'  => $this->l( 'Common' ),
				'active' => 'status',
				'search' => false
			),
			'active'          => array(
				'title'  => $this->l( 'Enabled' ),
				'active' => 'status',
				'search' => false
			),
		);


	}

	public function renderForm() {

		$this->fields_value['products[]']=$this->object->getAssociatedProducts();
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l( 'Flex FAQ' ),
			),
			'input'  => array(
				array(
					'name'      => 'title',
					'type'      => 'text',
					'lang'      => $this->object::$definition['fields']['title']['lang'],
					'required'  => $this->object::$definition['fields']['title']['required'],
					'maxlength' => $this->object::$definition['fields']['title']['size'],
					'label'     => $this->l( 'Title' ),
					'title'     => $this->l( 'Title' ),
					'desc'      => $this->l( 'Title / Question of the item' ),
				),
				array(
					'name'          => 'content',
					'type'          => 'textarea',
					'lang'          => $this->object::$definition['fields']['title']['lang'],
					'required'      => $this->object::$definition['fields']['content']['required'],
					'maxlength'     => $this->object::$definition['fields']['content']['size'],
					'label'         => $this->l( 'Item content' ),
					'title'         => $this->l( 'Item content' ),
					'desc'          => $this->l( 'Main content for the item' ),
				),
				array(
					'name'     => 'common',
					'type'     => 'switch',
					'required' => $this->object::$definition['fields']['common']['required'],
					'label'    => $this->l( 'Common' ),
					'title'    => $this->l( 'Common' ),
					'desc'     => $this->l( 'Display item in the common FAQ page' ),
					'is_bool'  => true,
					'values'   => array(
						array(
							'id'    => 'common_on',
							'value' => 1,
							'label' => $this->l( 'Yes' )
						),
						array(
							'id'    => 'common_off',
							'value' => 0,
							'label' => $this->l( 'No' )
						)
					)
				),
				array(
					'name'     => 'active',
					'type'     => 'switch',
					'required' => $this->object::$definition['fields']['active']['required'],
					'label'    => $this->l( 'Enabled' ),
					'title'    => $this->l( 'Enabled' ),
					'desc'     => $this->l( 'Enable or Disable the item' ),
					'is_bool'  => true,
					'values'   => array(
						array(
							'id'    => 'active_on',
							'value' => 1,
							'label' => $this->l( 'Enabled' )
						),
						array(
							'id'    => 'active_off',
							'value' => 0,
							'label' => $this->l( 'Disabled' )
						)
					)
				),
				array(
					'name' => 'products[]',
					'type' => 'select',
					'multiple' => true,
					'class' => 'chosen',
					'label' => $this->l( 'Product(s)' ),
					'title'    => $this->l( 'Product(s)' ),
					'desc'     => $this->l( 'Select one or more associated products' ),
					'options' => array(
						'query' => $this->object->getAssociableProducts(),
						'id' => 'id_product',
						'name' => 'name'
					)
				),
				array(
					'name'  => 'categories',
					'type'  => 'categories',
					'label' => $this->l( 'Associated categories' ),
					'tree'  => array(
						'root_category'       => 1,
						'id'                  => 'id_category',
						'name'                => 'name_category',
						'use_checkbox'        => true,
						'selected_categories' => $this->object->getAssociatedCategories(),
					)
				),
			),
			'submit' => array(
				'title' => $this->l( 'Save' ),
			)
		);
		if ( Shop::isFeatureActive() ) {
			$this->fields_form['input'][] = array(
				'name'  => 'shops',
				'type'  => 'shop',
				'label' => $this->l( 'Associated shops' ),
				'title' => $this->l( 'Associated shops' )
			);
		}

		return parent::renderForm();
	}
}