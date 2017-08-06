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

require_once _PS_MODULE_DIR_.'flexfaq/classes/FlexFaqModel.php';

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
		$this->fields_list = array(
			$this->identifier => array(
				'title' => '#',
			),
			'title' => array(
				'title' => $this->l('Title'),
			),
			'common' => array(
				'title' => $this->l('Common'),
				'active' => 'status',
				'search' => false
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'active' => 'status',
				'search' => false
			),
		);


	}

	public function renderForm() {

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Flex FAQ'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title'),
					'name' => 'title',
					'size' => 255,
					'maxlength' => 255,
					'required' => true,
					'lang' => true,
					'desc' => $this->l('Title / Question of the item')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Item content'),
					'name' => 'content',
					'rows' => 5,
					'cols' => 60,
					'lang' => true,
					'desc' => $this->l('Main content for the item')
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Common'),
					'title' => $this->l('Common'),
					'name' => 'common',
					'required' => true,
					'desc' => $this->l('Display item in the common FAQ page'),
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'common_on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'common_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Enabled'),
					'title' => $this->l('Enabled'),
					'name' => 'active',
					'required' => true,
					'desc' => $this->l('Enable or Disable the item'),
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'categories',
					'label' => $this->l('Associated categories'),
					'name' => 'categories',
					'tree' => array(
						'root_category' => 1,
						'id' => 'id_category',
						'name' => 'name_category',
						'use_checkbox' => true,
						'selected_categories' => $this->object->getAssociatedCategories(),
					)
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		if (Shop::isFeatureActive()) {
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'name' => 'shops',
				'label' => $this->l('Associated shops'),
				'title' => $this->l('Associated shops')
			);
		}
		return parent::renderForm();
	}
}