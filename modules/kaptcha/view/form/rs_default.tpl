<span {$attributes}>
    <img src="{$router->getUrl('kaptcha', ['do' => image, 'context' => $context, 'rand' => rand()])}" width="100" height="42" alt="">
    <input type="text" name="{$name}">
</span>
