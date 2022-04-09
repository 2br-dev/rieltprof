<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Templates\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект описывает одну секцию (col-*), строку (row), сброс (clearfix)
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $page_id Страница
 * @property integer $parent_id Родительская секция
 * @property integer $width_xs Ширина (XS)
 * @property integer $width_sm Ширина (SM)
 * @property integer $width Ширина
 * @property integer $width_lg Ширина
 * @property integer $width_xl Ширина
 * @property integer $width_xxl Ширина
 * @property string $inset_align_xs Горизонтальное выравнивание внутри элемента
 * @property string $inset_align_sm Горизонтальное выравнивание внутри элемента
 * @property string $inset_align Горизонтальное выравнивание внутри элемента
 * @property string $inset_align_lg Горизонтальное выравнивание внутри элемента
 * @property string $inset_align_xl Горизонтальное выравнивание внутри элемента
 * @property string $inset_align_xxl Горизонтальное выравнивание внутри элемента
 * @property string $align_items_xs Вертикальное выравнивание внутри элемента
 * @property string $align_items_sm Вертикальное выравнивание внутри элемента
 * @property string $align_items Вертикальное выравнивание внутри элемента
 * @property string $align_items_lg Вертикальное выравнивание внутри элемента
 * @property string $align_items_xl Вертикальное выравнивание внутри элемента
 * @property string $align_items_xxl Вертикальное выравнивание внутри элемента
 * @property string $align_self_xs Собственное вертикальное выравнивание
 * @property string $align_self_sm Собственное вертикальное выравнивание
 * @property string $align_self Собственное вертикальное выравнивание
 * @property string $align_self_lg Собственное вертикальное выравнивание
 * @property string $align_self_xl Собственное вертикальное выравнивание
 * @property string $align_self_xxl Собственное вертикальное выравнивание
 * @property integer $child_width_xs Ширина дочерних элементов
 * @property integer $child_width_sm Ширина дочерних элементов
 * @property integer $child_width Ширина дочерних элементов
 * @property integer $child_width_lg Ширина дочерних элементов
 * @property integer $child_width_xl Ширина дочерних элементов
 * @property integer $child_width_xxl Ширина дочерних элементов
 * @property integer $gutter_x_xs Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_x_sm Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_x Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_x_lg Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_x_xl Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_x_xxl Отступ вокруг дочерних элементов по горизонтали
 * @property integer $gutter_y_xs Отступ вокруг дочерних элементов по вертикали
 * @property integer $gutter_y_sm Отступ вокруг дочерних элементов по вертикали
 * @property integer $gutter_y Отступ вокруг дочерних элементов по вертикали
 * @property integer $gutter_y_lg Отступ вокруг дочерних элементов по вертикали
 * @property integer $gutter_y_xl Отступ вокруг дочерних элементов по вертикали
 * @property integer $gutter_y_xxl Отступ вокруг дочерних элементов по вертикали
 * @property integer $prefix_xs Отступ слева (XS)
 * @property integer $prefix_sm Отступ слева (SM)
 * @property integer $prefix Отступ слева (prefix)
 * @property integer $prefix_lg Остступ слева (offset)
 * @property integer $prefix_xl Остступ слева (offset)
 * @property integer $prefix_xxl Остступ слева (offset)
 * @property integer $suffix Отступ справа (suffix)
 * @property integer $pull_xs Сдвиг влево (xs)
 * @property integer $pull_sm Сдвиг влево (sm)
 * @property integer $pull Сдвиг влево (pull)
 * @property integer $pull_lg Сдвиг влево (pull)
 * @property integer $pull_xl Сдвиг влево (pull)
 * @property integer $push_xs Сдвиг вправо (xs)
 * @property integer $push_sm Сдвиг вправо (sm)
 * @property integer $push Сдвиг вправо (push)
 * @property integer $push_lg Сдвиг вправо (push)
 * @property integer $push_xl Сдвиг вправо (push)
 * @property integer $order_xs Порядок
 * @property integer $order_sm Порядок
 * @property integer $order Порядок
 * @property integer $order_lg Порядок
 * @property integer $order_xl Порядок
 * @property integer $order_xxl Порядок
 * @property string $css_class Пользовательский CSS класс
 * @property integer $is_clearfix_after Очистка после элемента(clearfix)
 * @property string $clearfix_after_css Пользовательский CSS класс для clearfix
 * @property string $inset_template Внутренний шаблон
 * @property string $outside_template Внешний шаблон
 * @property string $element_type Тип элемента
 * @property integer $invisible Невидимый элемент
 * @property integer $sortn 
 * --\--
 */
class Section extends \RS\Orm\OrmObject
{        
    const
        BOOTSTRAP_POSTFIX_AUTO = -1,
        BOOTSTRAP_POSTFIX_EMPTY = -2,
        BOOTSTRAP_POSTFIX_FIRST = -3,
        BOOTSTRAP_POSTFIX_LAST = -4,

        ELEMENT_TYPE_COL = 'col',
        ELEMENT_TYPE_ROW = 'row';
        
    protected static
        $table = 'sections';
    
    function _init()
    {
        parent::_init();

        $align_items_list = [
            ''    => t('Нет'),
            'start' => t('В начале'),
            'end' => t('В конце'),
            'center' => 'По центру',
            'baseline' => 'В линию',
            'stretch' => 'Растянуть',
        ];

        $this->getPropertyIterator()->append([
            'page_id' => new Type\Varchar([
                'maxLength' => '255',
                'no_export' => true,
                'description' => t('Страница'),
                'visible' => false
            ]),
            'parent_id' => new Type\Integer([
                'no_export' => true,
                'index' => true,
                'description' => t('Родительская секция'),
                'visible' => false
            ]),
            //Ширина секций
            'width_xs' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина (XS)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'width_sm' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина (SM)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'width' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина'),
                'visible' => true, //Отображается для GS960
                'bootstrapVisible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'width_lg' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'width_xl' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина'),
                'template' => '%templates%/form/section/width.tpl',
                'visible' => false,
                'bootstrapVisible' => true, //Отображается для Bootstrap
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'width_xxl' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Ширина'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),

            //Горизонтальное выравнивание
            'inset_align_xs' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'inset_align_sm' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'inset_align' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'Attr' => [['size' => 1]],
                'ListFromArray' => [[
                    'wide' => t('На всю ширину'),
                    'left' => t('Слева'),
                    'right' => t('Справа')
                ]],
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,

                'bootstrapVisible' => true,
                'rowBootstrapVisible' => false
            ]),
            'inset_align_lg' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'inset_align_xl' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'hint' => t('Задает класс justify-content-*'),
                'template' => '%templates%/form/section/inset_align.tpl',
                'visible' => false,
                'rowBootstrap4Visible' => true,
                'rowBootstrap5Visible' => true,
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'requestType' => TYPE_STRING
            ]),
            'inset_align_xxl' => new Type\Varchar([
                'description' => t('Горизонтальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),

            //Вертикальное выравнивание
            'align_items_xs' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_items_sm' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_items' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_items_lg' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_items_xl' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'hint' => t('Задает класс align-items-*'),
                'template' => '%templates%/form/section/align_items.tpl',
                'visible' => false,
                'rowBootstrap4Visible' => true,
                'rowBootstrap5Visible' => true,
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_items_xxl' => new Type\Varchar([
                'description' => t('Вертикальное выравнивание внутри элемента'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),

            //Вертикальное выравнивание
            'align_self_xs' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_self_sm' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_self' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_self_lg' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_self_xl' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'hint' => t('Задает класс align-self-*'),
                'template' => '%templates%/form/section/align_self.tpl',
                'visible' => false,
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),
            'align_self_xxl' => new Type\Varchar([
                'description' => t('Собственное вертикальное выравнивание'),
                'visible' => false,
                'requestType' => TYPE_STRING,
                'listFromArray' => [$align_items_list]
            ]),

            //Ширина дочерних элементов
            'child_width_xs' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'child_width_sm' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'child_width' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'child_width_lg' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'child_width_xl' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'hint' => t('Задает класс row-cols-*'),
                'visible' => false,
                'rowBootstrap5Visible' => true,
                'template' => '%templates%/form/section/child_width.tpl',
                'requestType' => TYPE_STRING
            ]),
            'child_width_xxl' => new Type\Integer([
                'description' => t('Ширина дочерних элементов'),
                'visible' => false,
                'requestType' => TYPE_STRING,
            ]),

            //Gutter x
            'gutter_x_xs' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_x_sm' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_x' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_x_lg' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_x_xl' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'requestType' => TYPE_STRING,
                'hint' => t('Задает класс gx-*, если будет равен отступу по вертикали, то g-*'),
                'visible' => false,
                'rowBootstrap5Visible' => true,
                'template' => '%templates%/form/section/gutter_x.tpl',
            ]),
            'gutter_x_xxl' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по горизонтали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),

            //Gutter y
            'gutter_y_xs' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_y_sm' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_y' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_y_lg' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'gutter_y_xl' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'requestType' => TYPE_STRING,
                'hint' => t('Задает класс gy-*, если будет равен отступу по горизонтали, то g-*'),
                'visible' => false,
                'rowBootstrap5Visible' => true,
                'template' => '%templates%/form/section/gutter_y.tpl',
            ]),
            'gutter_y_xxl' => new Type\Integer([
                'description' => t('Отступ вокруг дочерних элементов по вертикали'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),

            //Отступ слева
            'prefix_xs' => new Type\Integer([
                'description' => t('Отступ слева (XS)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'prefix_sm' => new Type\Integer([
                'description' => t('Отступ слева (SM)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'prefix' => new Type\Integer([
                'description' => t('Отступ слева (prefix)'),
                'bootstrapVisible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'prefix_lg' => new Type\Integer([
                'description' => t('Остступ слева (offset)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'prefix_xl' => new Type\Integer([
                'description' => t('Остступ слева (offset)'),
                'visible' => false,
                'bootstrapVisible' => true,
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'template' => '%templates%/form/section/prefix.tpl',
                'requestType' => TYPE_STRING
            ]),
            'prefix_xxl' => new Type\Integer([
                'description' => t('Остступ слева (offset)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
                    
            'suffix' => new Type\Integer([
                'description' => t('Отступ справа (suffix)'),
                'bootstrapVisible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false
            ]),
            
            //Сдвиг влево
            'pull_xs' => new Type\Integer([
                'description' => t('Сдвиг влево (xs)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'pull_sm' => new Type\Integer([
                'description' => t('Сдвиг влево (sm)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'pull' => new Type\Integer([
                'description' => t('Сдвиг влево (pull)'),
                'bootstrapVisible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'pull_lg' => new Type\Integer([
                'description' => t('Сдвиг влево (pull)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'pull_xl' => new Type\Integer([
                'description' => t('Сдвиг влево (pull)'),
                'visible' => false,
                'bootstrapVisible' => true,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'template' => '%templates%/form/section/pull.tpl',
                'requestType' => TYPE_STRING
            ]),
            
            //Сдвиг вправо
            'push_xs' => new Type\Integer([
                'description' => t('Сдвиг вправо (xs)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'push_sm' => new Type\Integer([
                'description' => t('Сдвиг вправо (sm)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'push' => new Type\Integer([
                'description' => t('Сдвиг вправо (push)'),
                'bootstrapVisible' => false,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'push_lg' => new Type\Integer([
                'description' => t('Сдвиг вправо (push)'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'push_xl' => new Type\Integer([
                'description' => t('Сдвиг вправо (push)'),
                'visible' => false,
                'bootstrapVisible' => true,
                'bootstrap4Visible' => false,
                'bootstrap5Visible' => false,
                'template' => '%templates%/form/section/push.tpl',
                'requestType' => TYPE_STRING
            ]),

            //Сортировка секций (bootstrap4)
            'order_xs' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'order_sm' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'order' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'visible' => false, //Отображается для GS960
                'requestType' => TYPE_STRING
            ]),
            'order_lg' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'order_xl' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'template' => '%templates%/form/section/order.tpl',
                'visible' => false,
                'bootstrapVisible' => true, //Отображается для Bootstrap
                'bootstrap4Visible' => true,
                'bootstrap5Visible' => true,
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false,
                'requestType' => TYPE_STRING
            ]),
            'order_xxl' => new Type\Integer([
                'maxLength' => '5',
                'description' => t('Порядок'),
                'visible' => false,
                'requestType' => TYPE_STRING
            ]),

            'css_class' => new Type\Varchar([
                'description' => t('Пользовательский CSS класс'),
            ]),
            'is_clearfix_after' => new Type\Integer([
                'description' => t('Очистка после элемента(clearfix)'),
                'maxLength' => 1,
                'checkboxView' => [1,0],
                'template' => '%templates%/form/section/clearfix_after.tpl',
                'rowBootstrapVisible' => false,
                'rowBootstrap4Visible' => false,
                'rowBootstrap5Visible' => false
            ]),
            'clearfix_after_css' => new Type\Varchar([
                'description' => t('Пользовательский CSS класс для clearfix'),
                'maxLength' => 150,
                'visible' => false
            ]),
            'inset_template' => new Type\Template([
                'maxLength' => '255',
                'description' => t('Внутренний шаблон'),
                'hint' => t('Вы можете создать прослойку из вашего HTML кода между текущим элементом и всем внутренним содержимым. Внутреннее содержимое будет доступно внутри шаблона в переменной {$wrapped_content}.')
            ]),
            'outside_template' => new Type\Template([
                'description' => t('Внешний шаблон'),
                'hint' => t('Вы можете обернуть текущий элемент вашим HTML кодом. Текущий элемент будет доступен внутри шаблона в переменной {$wrapped_content}.')
            ]),
            'element_type' => new Type\Enum(['col', 'row'], [
                'maxLength' => 1,
                'description' => t('Тип элемента'),
                'allowEmpty' => false,
                'visible' => false
            ]),
            'invisible' => new Type\Integer([
                'description' => t('Невидимый элемент'),
                'hint' => t('В этом случае для элемента не будет создаваться HTML-тэг.'),
                'checkboxView' => [1, 0],
                'visible' => false,
                'bootstrap5Visible' => true,
                'rowBootstrap5Visible' => true,
            ]),
            'sortn' => new Type\Integer([
                'visible' => false
            ]),
        ]);
    }
    
    function beforeWrite($flag)
    {
        $null_fields = ['width', 'prefix', 'pull', 'push', 'order'];
        $devices = ['', '_xs', '_sm', '_lg', '_xl'];
        foreach($null_fields as $field) {
            foreach($devices as $device) {
                if ($this[$field.$device] === '') $this[$field.$device] = null;
            }
        }
        
        //Получаем порядковый номер вставляемого блока
        if (!$this->isModified('sortn') && $flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn)+1 as max')
                ->from($this)
                ->where([
                    'parent_id' => $this['parent_id'], 
                    'page_id' => $this['page_id']])
                ->exec()->getOneField('max', 0);
        }        
    }

    /**
     * Удаление секции
     *
     * @return bool
     * @throws \RS\Orm\Exception
     */
    function delete()
    {
        //Удаляем все блоки, которые находятся внутри данного
        $sub_sections = \RS\Orm\Request::make()
            ->from($this)
            ->where(['parent_id' => $this['id']])
            ->objects();
            
        if (count($sub_sections)) {
            foreach($sub_sections as $section) {
                $section->delete();
            }
        } else {
            $sub_modules = \RS\Orm\Request::make()
                ->from(new SectionModule())
                ->where(['section_id' => $this['id']])
                ->objects();
            foreach($sub_modules as $module) {
                $module->delete();
            }
        }
        
        return parent::delete();
    }

    /**
     * Перемещает элемент на новую позицию. 0 - первый элемент
     *
     * @param integer $new_position - номер новой позиции
     * @param integer|null $new_parent_id - id нового родителя, если вы поменяли расположение
     * @return bool
     * @throws \RS\Db\Exception
     */
    public function moveToPosition($new_position, $new_parent_id = null)
    {
        if ($this->noWriteRights()){
            return false;
        }

        if ($new_parent_id) {
            $this->changeParent($new_parent_id);
        }

        //Определим максимальную позицию для этого родителя
        $downmove = \RS\Orm\Request::make()
            ->update($this)
            ->where([
                'page_id'   => $this['page_id'],
                'parent_id' => $this['parent_id']
            ]);
        $upmove = clone $downmove;

        //Раздвинем позиции
        //Вниз
        $downmove->set('sortn = sortn - 1')
            ->where("sortn < '#new_pos'", ['new_pos' => $new_position])->exec();

        //Вверх
        $upmove->set('sortn = sortn + 1')
            ->where("sortn >= '#new_pos'", ['new_pos' => $new_position])->exec();


        //И занусем наш блок между позиций
        \RS\Orm\Request::make()
            ->update($this)
            ->set([
                'sortn' => $new_position
            ])
            ->where([
                'id' => $this['id']
            ])
            ->exec();

        //Обновим сортировочные индексы у данной секции, чтобы было 0,1,2,3,4
        $items = \RS\Orm\Request::make()
            ->from($this)
            ->orderby('sortn')
            ->where([
                'page_id'   => $this['page_id'],
                'parent_id' => $this['parent_id']
            ])
            ->exec()->fetchAll();

        foreach ($items as $k=>$item) {
            \RS\Orm\Request::make()
                ->update()
                ->from($this)
                ->set([
                    'sortn' => $k
                ])
                ->where([
                    'id' => $item['id']
                ])->exec();
        }
            
        //Сбросим кэш при перемещении блоков
        \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
        return true;
    }


    /**
     * Перемещает секцию относительно другой
     *
     * @param integer $section_id - id секции относительно, которого будет перемещение
     * @param string $move_type - тип перемещения (before|after|first|last)
     *
     * @return boolean
     * @throws \RS\Db\Exception
     */
    function moveToPositionRelativeOfSection($section_id, $move_type = "after")
    {
        //Переместим на нужное место относительно модуля
        $relative_section = new \Templates\Model\Orm\Section($section_id);
        $parent_id = $relative_section['parent_id'];
        switch($move_type){
            case "first": //В начало
                return $this->moveToPosition(0, $parent_id);
                break;
            case "before": //Перед
                return $this->moveToPosition($relative_section['sortn'], $parent_id);
                break;
            case "after": //После
                return $this->moveToPosition($relative_section['sortn'] + 1, $parent_id);
                break;
            case "last": //Последним
            default:
                //Получим максимальную позицию и вставим
                $max = \RS\Orm\Request::make()
                            ->select('MAX(sortn) as num')
                            ->from($this)
                            ->where([
                                'section_id' => $this['section_id'],
                                'page_id' => $this['page_id'],
                            ])->exec()
                            ->getOneField('num', 0);
                return $this->moveToPosition($max, $parent_id);
                break;
        }
    }

    /**
     * Перемещяет элемент в последнюю позицию нового родителя.
     * Обновляет сортировочные индексы у предыдущего родителя
     *
     * @param integer $new_parent_id - id нового родителя
     * @return bool
     * @throws \RS\Db\Exception
     */
    function changeParent($new_parent_id)
    {
        if ($this['parent_id'] == $new_parent_id) {
            return false;
        }

        //Изменяем сортировочные индексы в старом контейнере
        \RS\Orm\Request::make()
            ->update($this)
            ->set('sortn = sortn - 1')
            ->where([
                'page_id' => $this['page_id'],
                'parent_id' => $this['parent_id']
            ])
            ->where("sortn > '#sortn'", ['sortn' => $this['sortn']])
            ->exec();


        //Получаем новый
        $max_new_sortn = \RS\Orm\Request::make()
            ->select('MAX(sortn)+1 as maxsortn')
            ->from($this)
            ->where([
                'page_id' => $this['page_id'],
                'parent_id' => $new_parent_id
            ])
            ->exec()->getOneField('maxsortn', 0);

        //Изменяем родителя секции
        \RS\Orm\Request::make()
            ->update($this)
            ->set([
                'sortn' => $max_new_sortn,
                'parent_id' => $new_parent_id
            ])
            ->where([
                'id' => $this['id'],
            ])
            ->exec();
        
        $this['parent_id'] = $new_parent_id;
        $this['sortn'] = $max_new_sortn;
        
        return true;
    }

    /**
     * Возвращает объект контейнера, в котором находится секция
     *
     * @return SectionContainer
     * @throws \RS\Db\Exception
     */
    public function getContainer()
    {
        $parent_id = $this['parent_id'];
        while($parent_id > 0) {
            $arr = \RS\Orm\Request::make()
                ->select('id, parent_id')
                ->from($this)
                ->where(['id' => $parent_id])
                ->exec()
                ->fetchRow();
            $parent_id = $arr['parent_id'];
        }
        return SectionContainer::loadByWhere(['page_id' => $this['page_id'], 'type' => $parent_id]);
    }

    /**
     * Возвращает блоки, которые находятся в секции
     *
     * @return array
     */
    public function getBlocks()
    {
        $blocks = SectionModule::getPageBlocks($this['page_id']);
        return isset( $blocks[$this['id']] ) ? $blocks[$this['id']] : [];
    }

    /**
     * Возвращает true, если в секцию можно добавить еще секцию
     *
     * @return bool
     */
    public function canInsertSection()
    {
        $mod_count = \RS\Orm\Request::make()
            ->from(new SectionModule())
            ->where(['section_id' => $this['id']])
            ->count();
        return $this['element_type'] == self::ELEMENT_TYPE_ROW || !$mod_count;
    }

    /**
     * Возвращает true, если в секцию можно добавить модуль
     *
     * @return bool
     */
    public function canInsertModule()
    {
        $subsection_count = \RS\Orm\Request::make()
            ->from($this)
            ->where(['parent_id' => $this['id']])
            ->count();
        return $this['element_type'] == self::ELEMENT_TYPE_COL && !$subsection_count;
    }

    /**
     * Возвращает visible-*, d-* классы, которые установлены для секции
     *
     * @return string
     */
    public function getAnyVisibleClass()
    {
        $result = [];
        $classes = explode(' ', $this['css_class']);
        foreach($classes as $class) {
            if (preg_match('/^visible-(xs|sm|md|lg)$/', trim($class), $match)) {
                $result[] = 'bvisible-'.$match[1];
            }
            elseif (preg_match('/^d(-(none|sm|md|lg|xl))(-.*)?$/', trim($class), $match)) {
                if (isset($match[3]) && $match[3] != '-none') {
                    $match[3] = '-block'; //любой видимый display трансформируем в -block, чтобы не ломать конструктор
                }
                $result[] = 'd'.$match[1].(isset($match[3]) ? $match[3] : '');
            }
        }

        return implode(' ', $result);
    }

    /**
     * Модифицирует структуру ORM Объекта в зависимости от сеточного фреймворка
     *
     * @param string $grid_system
     * @return void
     */
    public function prepareFieldsForGridSystem($grid_system)
    {
        if (in_array($grid_system, [SectionContext::GS_BOOTSTRAP4,  SectionContext::GS_BOOTSTRAP5])) {

            $no = [
                '' => t('нет'),
            ];

            $auto = [
                self::BOOTSTRAP_POSTFIX_AUTO => t('col-auto'),
            ];

            $col = [
                self::BOOTSTRAP_POSTFIX_EMPTY => t('col')
            ];

            $zero = ['0' => 0];

            $columns12 = array_combine(range(1,12), range(1,12));
            $columns11 = array_combine(range(1,11), range(1,11));
            $columns6 = array_combine(range(1,6), range(1,6));
            $columns0_6 = array_combine(range(0,6), range(0,6));

            $order_list = $no + [
                self::BOOTSTRAP_POSTFIX_FIRST => 'first',
                self::BOOTSTRAP_POSTFIX_LAST => 'last'
                ] + array_combine(range(0,12), range(0,12));

            $devices = $this->getDevicesForGridSystem($grid_system);


            foreach($devices as $device) {
                $type_list = $no + $auto + $col + $columns12;
                $prefix_list = ($device == '_xs') ? $no + $columns11 : $no + $zero + $columns11;
                $modifier_list = $no + $auto + $columns6;
                $gutter_list = $no + $columns0_6;

                $this['__width'.$device]->setListFromArray($type_list);
                $this['__child_width'.$device]->setListFromArray($modifier_list);
                $this['__gutter_x'.$device]->setListFromArray($gutter_list);
                $this['__gutter_y'.$device]->setListFromArray($gutter_list);
                $this['__prefix'.$device]->setListFromArray($prefix_list);
                $this['__order'.$device]->setListFromArray($order_list);
                $this['__inset_align'.$device]->setListFromArray([
                    ''    => t('Нет'),
                    'start' => t('В начале'),
                    'end' => t('В конце'),
                    'center' => 'По центру',
                    'between' => 'Равномерно',
                    'around' => 'Равномерно'
                ]);
            }
        }
    }

    /**
     * Трансформирует значение для сетки bootstrap4
     *
     * @return string
     */
    public function transformBootstrap4Width($field)
    {
        $col_value = $this[$field];
        switch($col_value) {
            case self::BOOTSTRAP_POSTFIX_AUTO : return '-auto';
            case self::BOOTSTRAP_POSTFIX_EMPTY: return '';
            case self::BOOTSTRAP_POSTFIX_FIRST: return '-first';
            case self::BOOTSTRAP_POSTFIX_LAST: return '-last';
            default: return '-'.$col_value;
        }
    }

    /**
     * Возвращает список устройств, для которых рассчитан сеточный фреймворк
     *
     * @param string $grid_system - сеточная система
     * @return array
     */
    public function getDevicesForGridSystem($grid_system)
    {
        switch($grid_system) {
            case SectionContext::GS_BOOTSTRAP:
                $devices = ['_xs', '_sm', '', '_lg'];
                break;

            case SectionContext::GS_BOOTSTRAP4:
                $devices = ['_xs', '_sm', '', '_lg', '_xl'];
                break;

            case SectionContext::GS_BOOTSTRAP5:
                $devices = ['_xs', '_sm', '', '_lg', '_xl', '_xxl'];
                break;

            default:
                $devices = [];
        }
        return $devices;
    }

    /**
     * Конвертирует постфикс названия устройства в infix Bootstrap
     *
     * @param string $device
     * @param bool $has_xs
     * @return string
     */
    public function getInfixFromDevice($device, $has_xs = false)
    {
        $infix = $device == '' ? '-md' : str_replace('_', '-', $device);
        return $has_xs ? $infix : ($infix == '-xs' ? '' : $infix);
    }

    /**
     * Возвращает bootstrap классы модификаторы gx-*, gy-*, g-* для текущей строки
     *
     * @return array
     */
    protected function getBootstrapGutterModifier($grid_system)
    {
        $result = [];
        $devices = $this->getDevicesForGridSystem($grid_system);
        foreach($devices as $device) {
            $infix = $this->getInfixFromDevice($device);
            $x_value = $this['gutter_x'.$device];
            $y_value = $this['gutter_y'.$device];

            if ($x_value && $x_value == $y_value) {
                $result[] = 'g'.$infix.$this->transformBootstrap4Width('gutter_x'.$device);
            } else {
                if ($x_value) {
                    $result[] = 'gx'.$infix.$this->transformBootstrap4Width('gutter_x'.$device);
                }
                if ($y_value) {
                    $result[] = 'gy'.$infix.$this->transformBootstrap4Width('gutter_y'.$device);
                }
            }
        }
        return $result;
    }

    /**
     * Генерирует CSS классы для заданого поля
     *
     * @param $field
     * @param $classname
     * @param $grid_system
     * @return array
     */
    protected function generateCssClasses($field, $classname, $grid_system)
    {
        $classes = [];
        $has_xs_infix = $grid_system == SectionContext::GS_BOOTSTRAP;
        $devices = $this->getDevicesForGridSystem($grid_system);
        foreach($devices as $device) {
            $infix = $this->getInfixFromDevice($device, $has_xs_infix);
            if ($this[$field.$device]) {
                $classes[] = $classname.$infix.$this->transformBootstrap4Width($field.$device);
            }
        }

        return $classes;
    }

    /**
     * Возвращает классы которые необходимо добавить в публичной части сайта данной строке или секции,
     * согласно настройкам административной панели
     *
     * @param $grid_system
     * @param $extra_class
     *
     * @return string
     */
    protected function getBootstrapClass($grid_system, $extra_class = '')
    {
        $classes = [];

        $align_items = $this->generateCssClasses('align_items', 'align-items', $grid_system);
        $justify_content = [];

        if ($grid_system != SectionContext::GS_BOOTSTRAP) {
            //Было добавлено, начиная с bootstrap4
            $justify_content = $this->generateCssClasses('inset_align', 'justify-content', $grid_system);
        }

        if ($this['element_type'] == 'row') {
            $classes = array_merge($classes,
                ['row'],
                $this->generateCssClasses('child_width', 'row-cols', $grid_system),
                $this->getBootstrapGutterModifier($grid_system)
            );
        } else {
            if ($align_items || $justify_content) {
                $classes[] = 'd-flex';
            }

            $classes = array_merge($classes,
                $this->generateCssClasses('width', 'col', $grid_system),
                $this->generateCssClasses('prefix', 'offset', $grid_system),
                $this->generateCssClasses('align_self', 'align-self', $grid_system),
                $this->generateCssClasses('order', 'order', $grid_system)
            );
        }

        $classes = array_merge($classes, $align_items, $justify_content);
        $classes[] = $this['css_class'];
        $classes[] = $extra_class;

        return trim(implode(' ', $classes));
    }

    /**
     * Возвращает классы которые необходимо добавить в публичной части сайта данной строке или секции,
     * согласно настройкам административной панели для фреймворка GS960
     *
     * @param $extra_class
     * @return string
     */
    protected function getGs960Class($extra_class)
    {
        $classes = [];
        $classes[] = 'grid_'.$this['width'];

        if ($this['prefix']) $classes[] = 'prefix_'.$this['prefix'];
        if ($this['suffix']) $classes[] = 'suffix_'.$this['prefix'];
        if ($this['pull']) $classes[] = 'pull_'.$this['pull'];
        if ($this['push']) $classes[] = 'push_'.$this['push'];

        $classes[] = $this['css_class'];
        return trim(implode(' ', $classes));
    }

    /**
     * Возвращает классы которые необходимо добавить в публичной части сайта данной строке или секции,
     * согласно настройкам административной панели, для необходимого типа сетки
     *
     * @param $grid_system
     * @param $extra_class
     * @return string
     */
    public function renderElementClass($grid_system, $extra_class = '')
    {
        switch($grid_system) {
            case SectionContext::GS_GS960:
                return $this->getGs960Class($extra_class);
            default:
                return $this->getBootstrapClass($grid_system, $extra_class);
        }
    }

    /**
     * Возвращает CSS-классы, которые необходимо добавить у элемента clearfix данной секции
     *
     * @param $grid_system
     * @return string
     */
    public function renderClearfixClass($grid_system)
    {
        $classes = [];
        switch($grid_system) {
            case SectionContext::GS_GS960:
                $classes[] = 'clearfix';
                break;
            default:
                $classes[] = 'w-100';
        }

        $classes[] = $this['clearfix_after_css'];

        return trim(implode(' ', $classes));
    }

    /**
     * Возвращает CSS-классы для элемента, оборачивающего Блоки. Актуально только для GS960
     *
     * @param $grid_system
     * @return string
     */
    public function renderGridBlockClass($grid_system)
    {
        $classes = ['gridblock'];

        if ($this['inset_align'] == 'left') $classes[] = 'alignleft';
        if ($this['inset_align'] == 'right') $classes[] = 'alignright';

        return implode(' ', $classes);
    }
}
