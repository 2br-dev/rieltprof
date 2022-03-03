<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;
use \Catalog\Model\Inventory\DocumentApi;
use \Catalog\Model\Orm\Inventory\LinkedDocument;
use \Catalog\Model\Orm\Inventory\Document;

class DocumentLinkManager
{
    protected
        $source_id,
        $source_type;

    function __construct($source_id = null, $source_type = null)
    {
        $this->source_id = $source_id;
        $this->source_type = $source_type;
    }

    /**
     *  Создать связанное с заказом резервирование
     *
     * @param $document
     * @param $order
     */
    function createLinkOrder($document, $order)
    {
        $linked_document = new LinkedDocument();
        $linked_document['document_id'] = $order['id'];
        $linked_document['order_num'] = $order['order_num'];
        $linked_document['document_type'] = \Shop\Model\Orm\Order::DOCUMENT_TYPE_ORDER;
        $linked_document['source_type'] = $document['type'];
        $linked_document['source_id'] = $document['id'];

        $linked_document->insert();
    }

    /**
     *  Получить связи документов
     *
     * @param null $document
     * @return array|bool
     */
    function getLinks($document = null)
    {
        if($document){
            $this->source_id = $document['id'];
            $this->source_type = $document['type'];
        }

        if(!$this->source_id){
            return false;
        }
        $docs = \RS\Orm\Request::make()
            ->select()
            ->from(new LinkedDocument())
            ->where("(source_id = $this->source_id and source_type = '$this->source_type') or (document_id = $this->source_id and document_type = '$this->source_type')")
            ->objects();

        $new_docs = [];
        foreach ($docs as $doc){
            if($doc['source_id'] == $this->source_id && $doc['source_type'] == $this->source_type){
                $new_docs[] = $doc;
            }else{
                $new_docs[] = $this->reverseLink($doc);
            }
        }
        return $new_docs;
    }

    /**
     *  Удалить связанные документы
     *
     * @return void
     */
    function deleteLinkedDocuments()
    {
        $links = $this->getLinks();
        foreach ($links as $link){
            $this->deleteDocumentByLink($link);
        }
    }

    /**
     *  Удалить связи документа
     *
     * @param $document
     * @return void
     */
    function deleteLinksByDocument($document)
    {
        if($document['id']) {
            \RS\Orm\Request::make()
                ->delete()
                ->from(new LinkedDocument())
                ->where("(source_id = $document->id and source_type = '$document->type') or (document_id = $document->id and document_type = '$document->type')")
                ->exec();
        }
    }

    /**
     *  Переместить местами источник и документ в связи
     *
     * @param $link
     * @return Orm\Inventory\LinkedDocument
     */
    function reverseLink($link)
    {
        $new_doc = new LinkedDocument();
        $new_doc['source_id'] = $link['document_id'];
        $new_doc['source_type'] = $link['document_type'];
        $new_doc['document_type'] = $link['source_type'];
        $new_doc['document_id'] = $link['source_id'];
        $new_doc['reversed'] = true;
        return $new_doc;
    }

    /**
     *  Удалить документ по связи
     *
     * @param $link
     * @return void
     */
    function deleteDocumentByLink($link)
    {
        switch ($link['document_type']){
            case Document::DOCUMENT_TYPE_RESERVE  ||
                 Document::DOCUMENT_TYPE_WAITING  ||
                 Document::DOCUMENT_TYPE_ARRIVAL  ||
                 Document::DOCUMENT_TYPE_WRITE_OFF:
                $doc = new \Catalog\Model\Orm\Inventory\Document($link['document_id']);
                $doc->delete();
                break;
            case \Catalog\Model\Orm\Inventory\Movement::DOCUMENT_TYPE_MOVEMENT:
                $doc = new \Catalog\Model\Orm\Inventory\Movement($link['document_id']);
                $doc->delete();
                break;
            case \Catalog\Model\Orm\Inventory\Inventorization::DOCUMENT_TYPE_INVENTORY:
                $doc = new \Catalog\Model\Orm\Inventory\Inventorization($link['document_id']);
                $doc->delete();
                break;
        }
    }

    /**
     *  Изенить тип документа в связи
     *
     * @param $link
     * @param $from
     * @param $to
     * @return void
     */
    function changeLinkType($link, $from, $to)
    {
        if($link['reversed']){
            $link = $this->reverseLink($link);
        }
        $req = \RS\Orm\Request::make()
            ->update( new LinkedDocument())
            ->where([
                'source_type' => $link['source_type'],
                'source_id' => $link['source_id'],
                'document_id' => $link['document_id'],
                'document_type' => $link['document_type'],
            ]);
        if($link['source_type'] == $from){
            $req ->set(['source_type' => $to]);
        }else{
            $req ->set(['document_type' => $to]);
        }
        $req->exec();
    }
}