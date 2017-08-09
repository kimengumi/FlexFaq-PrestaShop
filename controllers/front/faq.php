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

class FlexfaqFaqModuleFrontController extends ModuleFrontController {

	/**
	 * Init content of FAQ page
	 */
	public function initContent() {

		$this->context->smarty->assign( array(
			'meta_title'       => $this->module->l( 'FAQ' ),
			'meta_description' => $this->module->l( 'FAQ' ),
			'path'             => $this->module->l( 'FAQ' ),
			'faqs'             => FlexFaqModel::getCommonCollection()
		) );
		parent::initContent();
		$this->setTemplate( 'faq.tpl' );
	}

}