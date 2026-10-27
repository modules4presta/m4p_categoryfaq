{**
 * m4pcategoryfaq
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 *}

{assign var=faqSchema value=$m4pFaqStructuredData}
<section class="m4p-category-faq" aria-labelledby="m4p-category-faq-title"{if $faqSchema} itemscope itemtype="https://schema.org/FAQPage"{/if}>
  <h2 id="m4p-category-faq-title" class="m4p-category-faq__title">
    {l s='%category% – frequently asked questions' sprintf=['%category%' => $m4pFaqCategoryName] d='Modules.M4pcategoryfaq.Shop'}
  </h2>
  <div class="m4p-category-faq__list">
    {foreach from=$m4pFaqItems item=faq}
      <div class="m4p-category-faq__item"{if $faqSchema} itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"{/if}>
        <h3 class="m4p-category-faq__question"{if $faqSchema} itemprop="name"{/if}>{$faq.question}</h3>
        <div class="m4p-category-faq__answer"{if $faqSchema} itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"{/if}>
          <div{if $faqSchema} itemprop="text"{/if}>{$faq.answer nofilter}</div>
        </div>
      </div>
    {/foreach}
  </div>
</section>
