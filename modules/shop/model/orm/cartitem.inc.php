<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model\Orm;

use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Orm\Type;

/**
 * Позиция в корзине
 * --/--
 * @property string $uniq ID в рамках одной корзины
 * @property string $type Тип записи товар, услуга, скидочный купон
 * @property string $entity_id ID объекта type
 * @property integer $offer Комплектация
 * @property string $multioffers Многомерные комплектации
 * @property float $amount Количество
 * @property string $title Название
 * @property string $extra Дополнительные сведения (сериализованные)
 * @property array $extra_arr Дополнительные сведения
 * @property integer $site_id ID сайта
 * @property string $session_id ID сессии
 * @property string $dateof Дата добавления
 * @property integer $user_id Пользователь
 * --\--
 */
class CartItem extends AbstractCartItem
{
    const EXTRA_KEY_PRICE = 'price'; // Принудительная цена одной единицы
    const EXTRA_KEY_CONCOMITANTS = 'concomitants'; // данные по выбранным сопутствующим товарам

    protected static $table = 'cart';

    /** @var ConcomitantCartItem[] */
    protected $concomitants = null;

    public function _init()
    {
        parent::_init();

        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'session_id' => new Type\Varchar([
                'description' => t('ID сессии'),
                'maxLength' => 32,
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата добавления'),
            ]),
            'user_id' => new Type\Bigint([
                'description' => t('Пользователь'),
            ]),
        ]);

        $this->addIndex(['site_id', 'session_id', 'uniq'], self::INDEX_PRIMARY);
        $this->addIndex(['site_id', 'user_id'], self::INDEX_KEY);
    }

    /**
     * Возвращает первичный ключ.
     *
     * @return string[]
     */
    public function getPrimaryKeyProperty(): array
    {
        return ['site_id', 'session_id', 'uniq'];
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - insert|update|replace
     * @return void
     */
    public function beforeWrite($save_flag)
    {
        $this->saveConcomitants();
        parent::beforeWrite($save_flag);
    }

    /**
     * Заполняет список прикреплённых сопутствующих товаров на основе списка id тоапров (обычно из POST)
     *
     * @param int[] $concomitant_ids - id товаров
     * @param float[] $concomitant_amount - [id товара => количество товара]
     * @return void
     * @throws RSException
     */
    public function fillConcomitantsFromPost(?array $concomitant_ids, ?array $concomitant_amount): void
    {
        if (empty($concomitant_ids)) {
            return;
        }
        if ($concomitant_amount === null) {
            $concomitant_amount = [];
        }
        $shop_config = ConfigLoader::byModule(__CLASS__);
        $product = new Product($this['entity_id']);

        $this->clearConcomitants();
        if (!empty($concomitant_ids)) {
            foreach ($concomitant_ids as $concomitant_id) {
                if ($shop_config['allow_concomitant_count_edit']) {
                    $concomitant_amount_value = !empty($concomitant_amount[$concomitant_id]) ? abs((int)$concomitant_amount[$concomitant_id]) : $product->getAmountStep();
                } else {
                    $concomitant_amount_value = empty($product['concomitant_arr']['onlyone'][$concomitant_id]) ? $this['amount'] : $product->getAmountStep();
                }
                $concomitants_item = ConcomitantCartItem::loadFromArray($this, [
                    ConcomitantCartItem::SAVE_KEY_PRODUCT_ID => $concomitant_id,
                    ConcomitantCartItem::SAVE_KEY_AMOUNT => $concomitant_amount_value,
                ]);

                $this->addConcomitant($concomitants_item);
            }
        }
    }

    /**
     * Прикрепляет к товарной позиции сопутствующий товар
     *
     * @param ConcomitantCartItem $concomitant
     * @return void
     * @throws RSException
     */
    public function addConcomitant(ConcomitantCartItem $concomitant): void
    {
        $this->initConcomitants();
        if ($this['type'] == self::TYPE_PRODUCT) {
            $product = new Product($this['entity_id']);
            $product_concomitants = $product->getConcomitant();
            if (isset($product_concomitants[$concomitant['entity_id']])) {
                if (isset($this->concomitants[$concomitant['uniq']])) {
                    $this->concomitants[$concomitant['uniq']]['amoount'] = $this->concomitants[$concomitant['uniq']]['amoount'] + $concomitant['amount'];
                } else {
                    $this->concomitants[$concomitant['uniq']] = $concomitant;
                }
            } else {
                throw new RSException(t('Попытка добавить не соответствующий товару сопутствующий товар'));
            }
        } else {
            throw new RSException(t('Попытка добавить сопутствующий товар к нетоварной позиции'));
        }
    }

    /**
     * Очищает список прикреплённых сопутствующих товаров
     *
     * @return void
     */
    public function clearConcomitants(): void
    {
        $this->concomitants = [];
    }

    /**
     * Возвращает сопутствующие товары прикреплённые к товарной позиции
     *
     * @return ConcomitantCartItem[]
     * @throws RSException
     */
    public function getConcomitants(): array
    {
        $this->initConcomitants();
        return $this->concomitants;
    }

    /**
     * Инициализирует сопутствующие товары из дополнительного параметра
     * Исключаем из списка удаленные сопутствующие товары
     *
     * @return void
     * @throws RSException
     */
    protected function initConcomitants(): void
    {
        if ($this->concomitants === null) {
            $this->concomitants = [];
            $concomitants_data = $this->getExtraParam(self::EXTRA_KEY_CONCOMITANTS, []);
            $product = $this->getEntity();

            if ($concomitants_data && $product instanceof Product) {
                $sub_products = array_keys($product->getConcomitant());

                foreach ($concomitants_data as $item) {
                    if (in_array($item['id'], $sub_products)) {
                        $concomitant = ConcomitantCartItem::loadFromArray($this, $item);
                        $this->concomitants[$concomitant['uniq']] = $concomitant;
                    }
                }
            }
        }
    }

    /**
     * Сохраняет прикреплённые сопутствующие товары в дополнительном параметре
     *
     * @return void
     */
    protected function saveConcomitants(): void
    {
        if ($this->concomitants !== null) {
            $concomitants_data = [];
            foreach ($this->concomitants as $concomitant) {
                $concomitants_data[] = $concomitant->saveInArray();
            }
            $this->setExtraParam(self::EXTRA_KEY_CONCOMITANTS, $concomitants_data);
        }
    }
}
