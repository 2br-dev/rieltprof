<div class="formbox">
    <div class="tabs">
        <form method="POST" action="" enctype="multipart/form-data" class="crud-form">
            <div class="frame" data-name="tab2">
                <table class="otable">
                    <tr>
                        <td class="otitle">{$elem.__name->getTitle()}</td>
                        <td>{include file=$elem.__name->getRenderTemplate() field=$elem.__name}</td>
                    </tr>

                    <tr>
                        <td class="otitle">{$form->__title->getDescription()}</td>
                        <td>
                            {$form->getPropertyView('title')}
                        </td>
                    </tr>

                    <tr>
                        <td class="otitle">{$form->__description->getDescription()}</td>
                        <td>
                            {$form->getPropertyView('description')}
                        </td>
                    </tr>

                    <tr>
                        <td class="otitle">{$form->__author->getDescription()}</td>
                        <td>
                            {$form->getPropertyView('author')}
                        </td>
                    </tr>
                    </table>
            </div>
        </form>


    </div>
</div>