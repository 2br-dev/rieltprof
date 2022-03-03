<div class="registered-block">
    {$config_rieltprof = \RS\Config\Loader::ByModule('rieltprof')}
    <div class="header">В системе зарегистрировано</div>
    <hr>
    <div class="registered-values">
        <div class="left">
            <div class="value">{$config_rieltprof->getCountAllUsers()}</div>
            <div class="key">{$config_rieltprof->num_word($config_rieltprof->getCountAllUsers(),['риэлтор', 'риэлтора', 'риэлторов'], false)}</div>
        </div>
        <div class="right">
            <div class="value">{$config_rieltprof->getCountAllAds()}</div>
            <div class="key">{$config_rieltprof->num_word($config_rieltprof->getCountAllAds(),['объект', 'объекта', 'объектов'], false)}</div>
        </div>
    </div>
</div>
