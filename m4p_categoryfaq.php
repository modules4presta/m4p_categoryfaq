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

require_once __DIR__ . '/classes/M4pCategoryFaqItem.php';

class M4p_CategoryFaq extends Module
{
    public const HOOK = 'displayM4pCategoryFaq';

    private const ADMIN_TAB = 'AdminM4pCategoryFaq';

    public function __construct()
    {
        $this->name = 'm4p_categoryfaq';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->trans('Category FAQ', [], 'Modules.M4pcategoryfaq.Admin');
        $this->description = $this->trans('Questions and answers below the product list of a category, with FAQPage structured data.', [], 'Modules.M4pcategoryfaq.Admin');
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install(): bool
    {
        return parent::install()
            && $this->installTables()
            && $this->installHook()
            && $this->registerHook(self::HOOK)
            && $this->installTab();
    }

    public function uninstall(): bool
    {
        return $this->uninstallTab()
            && $this->uninstallTables()
            && parent::uninstall();
    }

    public function getContent(): void
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(self::ADMIN_TAB));
    }

    /**
     * Renders the questions assigned to a category.
     *
     * @param array{id_category?: int|string} $params
     */
    public function hookDisplayM4pCategoryFaq(array $params): string
    {
        $idCategory = (int) ($params['id_category'] ?? 0);
        if (!$idCategory) {
            return '';
        }

        $items = M4pCategoryFaqItem::getForCategory($idCategory, (int) $this->context->language->id);
        if (!$items) {
            return '';
        }

        $category = new Category($idCategory, (int) $this->context->language->id);

        $this->context->smarty->assign([
            'm4pFaqItems' => $items,
            'm4pFaqCategoryName' => (string) $category->name,
            'm4pFaqStructuredData' => $this->isCanonicalListing(),
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/category-faq.tpl');
    }

    // FAQPage markup is emitted once per question set: on the first, unfiltered page of the category.
    // Paginated and filtered URLs repeat the same questions and would duplicate it.
    private function isCanonicalListing(): bool
    {
        return (int) Tools::getValue('page', 1) <= 1
            && '' === (string) Tools::getValue('q', '')
            && '' === (string) Tools::getValue('order', '');
    }

    private function installTables(): bool
    {
        $engine = _MYSQL_ENGINE_;
        $prefix = _DB_PREFIX_;
        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$prefix}m4p_categoryfaq_item` (
                `id_m4p_categoryfaq_item` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                `position` INT UNSIGNED NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_m4p_categoryfaq_item`)
            ) ENGINE={$engine} DEFAULT CHARSET=utf8mb4;",
            "CREATE TABLE IF NOT EXISTS `{$prefix}m4p_categoryfaq_item_lang` (
                `id_m4p_categoryfaq_item` INT UNSIGNED NOT NULL,
                `id_lang` INT UNSIGNED NOT NULL,
                `question` VARCHAR(255) NOT NULL,
                `answer` TEXT,
                PRIMARY KEY (`id_m4p_categoryfaq_item`, `id_lang`)
            ) ENGINE={$engine} DEFAULT CHARSET=utf8mb4;",
            "CREATE TABLE IF NOT EXISTS `{$prefix}m4p_categoryfaq_item_category` (
                `id_m4p_categoryfaq_item` INT UNSIGNED NOT NULL,
                `id_category` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id_m4p_categoryfaq_item`, `id_category`),
                KEY `id_category` (`id_category`)
            ) ENGINE={$engine} DEFAULT CHARSET=utf8mb4;",
        ];

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function uninstallTables(): bool
    {
        foreach (['m4p_categoryfaq_item_category', 'm4p_categoryfaq_item_lang', 'm4p_categoryfaq_item'] as $table) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $table . '`');
        }

        return true;
    }

    private function installHook(): bool
    {
        if (Hook::getIdByName(self::HOOK)) {
            return true;
        }

        $hook = new Hook();
        $hook->name = self::HOOK;
        $hook->title = 'Category FAQ';
        $hook->description = 'Questions and answers below the category product list (m4p_categoryfaq).';

        return $hook->add();
    }

    private function installTab(): bool
    {
        $tab = new Tab();
        $tab->class_name = self::ADMIN_TAB;
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int) $lang['id_lang']] = 'pl' === $lang['iso_code'] ? 'FAQ kategorii' : 'Category FAQ';
        }

        return $tab->add();
    }

    private function uninstallTab(): bool
    {
        $idTab = (int) Tab::getIdFromClassName(self::ADMIN_TAB);

        return !$idTab || (new Tab($idTab))->delete();
    }
}
