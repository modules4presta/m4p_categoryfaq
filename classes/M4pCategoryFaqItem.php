<?php

declare(strict_types=1);

/**
 * m4pcategoryfaq
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class M4pCategoryFaqItem extends ObjectModel
{
    /** @var bool */
    public $active = true;

    /** @var int */
    public $position = 0;

    /** @var string|array<int, string> */
    public $question;

    /** @var string|array<int, string> */
    public $answer;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    public static $definition = [
        'table' => 'm4pcategoryfaq_item',
        'primary' => 'id_m4pcategoryfaq_item',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'question' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'answer' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true],
        ],
    ];

    public function add($autoDate = true, $nullValues = false)
    {
        if (!$this->position) {
            $this->position = 1 + (int) Db::getInstance()->getValue(
                'SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'm4pcategoryfaq_item`'
            );
        }

        return parent::add($autoDate, $nullValues);
    }

    public function delete()
    {
        Db::getInstance()->delete('m4pcategoryfaq_item_category', 'id_m4pcategoryfaq_item = ' . (int) $this->id);

        return parent::delete();
    }

    /**
     * @return int[]
     */
    public function getCategories(): array
    {
        return self::getCategoriesOfItems([(int) $this->id]);
    }

    /**
     * @param int[] $idCategories
     */
    public function setCategories(array $idCategories): void
    {
        $db = Db::getInstance();
        $db->delete('m4pcategoryfaq_item_category', 'id_m4pcategoryfaq_item = ' . (int) $this->id);
        foreach (array_unique(array_filter(array_map('intval', $idCategories))) as $idCategory) {
            $db->insert('m4pcategoryfaq_item_category', [
                'id_m4pcategoryfaq_item' => (int) $this->id,
                'id_category' => $idCategory,
            ]);
        }
    }

    /**
     * @param int[] $idItems
     *
     * @return int[]
     */
    public static function getCategoriesOfItems(array $idItems): array
    {
        $idItems = array_filter(array_map('intval', $idItems));
        if (!$idItems) {
            return [];
        }

        $rows = Db::getInstance()->executeS(
            'SELECT DISTINCT `id_category` FROM `' . _DB_PREFIX_ . 'm4pcategoryfaq_item_category`
             WHERE `id_m4pcategoryfaq_item` IN (' . implode(',', $idItems) . ')'
        ) ?: [];

        return array_map(static fn (array $row): int => (int) $row['id_category'], $rows);
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    public static function getForCategory(int $idCategory, int $idLang): array
    {
        return Db::getInstance()->executeS(
            'SELECT il.`question`, il.`answer`
             FROM `' . _DB_PREFIX_ . 'm4pcategoryfaq_item` i
             INNER JOIN `' . _DB_PREFIX_ . 'm4pcategoryfaq_item_category` ic
                ON ic.`id_m4pcategoryfaq_item` = i.`id_m4pcategoryfaq_item` AND ic.`id_category` = ' . $idCategory . '
             INNER JOIN `' . _DB_PREFIX_ . 'm4pcategoryfaq_item_lang` il
                ON il.`id_m4pcategoryfaq_item` = i.`id_m4pcategoryfaq_item` AND il.`id_lang` = ' . $idLang . '
             WHERE i.`active` = 1 AND il.`question` <> \'\'
             ORDER BY i.`position` ASC, i.`id_m4pcategoryfaq_item` ASC'
        ) ?: [];
    }
}
