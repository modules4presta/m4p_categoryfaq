<?php

declare(strict_types=1);

/**
 * m4p_categoryfaq
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminM4pCategoryFaqController extends ModuleAdminController
{
    /** @var M4p_CategoryFaq */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'm4p_categoryfaq_item';
        $this->className = 'M4pCategoryFaqItem';
        $this->identifier = 'id_m4p_categoryfaq_item';
        $this->lang = true;
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';

        parent::__construct();

        $this->_select = 'GROUP_CONCAT(DISTINCT cl.`name` ORDER BY cl.`name` SEPARATOR ", ") AS categories';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'm4p_categoryfaq_item_category` ic
                ON ic.`id_m4p_categoryfaq_item` = a.`id_m4p_categoryfaq_item`
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON cl.`id_category` = ic.`id_category`
                AND cl.`id_lang` = ' . (int) $this->context->language->id . '
                AND cl.`id_shop` = ' . (int) $this->context->shop->id;
        $this->_group = 'GROUP BY a.`id_m4p_categoryfaq_item`';

        $this->fields_list = [
            'id_m4p_categoryfaq_item' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'question' => [
                'title' => $this->trans('Question', [], 'Modules.M4pcategoryfaq.Admin'),
                'filter_key' => 'b!question',
            ],
            'categories' => [
                'title' => $this->trans('Categories', [], 'Admin.Global'),
                'havingFilter' => true,
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ],
            'active' => [
                'title' => $this->trans('Displayed', [], 'Admin.Global'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function renderForm()
    {
        $selected = [];
        $object = $this->loadObject(true);
        if (Validate::isLoadedObject($object)) {
            $selected = $object->getCategories();
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Question', [], 'Modules.M4pcategoryfaq.Admin'),
                'icon' => 'icon-question',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Question', [], 'Modules.M4pcategoryfaq.Admin'),
                    'name' => 'question',
                    'lang' => true,
                    'required' => true,
                    'maxlength' => 255,
                    'hint' => $this->trans('Phrase it the way customers ask, e.g. "Which pyrometer for measuring metal?".', [], 'Modules.M4pcategoryfaq.Admin'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Answer', [], 'Modules.M4pcategoryfaq.Admin'),
                    'name' => 'answer',
                    'lang' => true,
                    'required' => true,
                    'autoload_rte' => true,
                    'desc' => $this->trans('Answer in the first sentence, then add details. Search engines and AI assistants quote short, self-contained answers.', [], 'Modules.M4pcategoryfaq.Admin'),
                ],
                [
                    'type' => 'categories',
                    'label' => $this->trans('Categories', [], 'Admin.Global'),
                    'name' => 'categoryBox',
                    'required' => true,
                    'desc' => $this->trans('The question shows below the product list of each selected category.', [], 'Modules.M4pcategoryfaq.Admin'),
                    'tree' => [
                        'id' => 'm4p_categoryfaq-categories',
                        'use_search' => true,
                        'use_checkbox' => true,
                        'selected_categories' => $selected,
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Position', [], 'Admin.Global'),
                    'name' => 'position',
                    'class' => 'fixed-width-sm',
                    'desc' => $this->trans('Lower numbers come first. Leave empty to add the question at the end.', [], 'Modules.M4pcategoryfaq.Admin'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Displayed', [], 'Admin.Global'),
                    'name' => 'active',
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                    ],
                ],
            ],
            'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
        ];

        return parent::renderForm();
    }

    public function processSave()
    {
        if (!array_filter(array_map('intval', (array) Tools::getValue('categoryBox', [])))) {
            $this->errors[] = $this->trans('Select at least one category.', [], 'Modules.M4pcategoryfaq.Admin');

            return false;
        }

        return parent::processSave();
    }

    public function postProcess()
    {
        if (!in_array($this->action, ['save', 'status', 'delete', 'bulkdelete'], true)) {
            return parent::postProcess();
        }

        $result = parent::postProcess();

        if ('save' === $this->action && !$this->errors && Validate::isLoadedObject($this->object)) {
            $this->object->setCategories((array) Tools::getValue('categoryBox', []));
        }

        if (!$this->errors) {
            Tools::clearSmartyCache();
        }

        return $result;
    }
}
