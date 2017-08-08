{*
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
 *}

<section class="page-product-box">
    <h3 class="page-product-heading">{l s='FAQ' mod='flexfaq'}</h3>
    {foreach $faqs as $faq}
        <div class="rte" onclick="$('#faq-content-{$faq.id_flexfaq}').toggle();">
            <h4 style="cursor:pointer">
                {$faq.title}
            </h4>
        </div>
        <div id="faq-content-{$faq.id_flexfaq}"
             class="rte" style="display:none;">
            <p>
                {$faq.content}
            </p>
        </div>
    {/foreach}
</section>