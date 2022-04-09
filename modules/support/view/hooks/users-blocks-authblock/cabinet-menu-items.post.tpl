{modulegetvars name="\Support\Controller\Block\NewMessages" var="data"}
<li>
    <a href="{$router->getUrl('support-front-support')}" class="aside-menu__link">
        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.6543 5H3.3457C2.32925 5 1.5 5.72498 1.5 6.61538V17.3846C1.5 18.272 2.32556 19 3.3457 19H20.6543C21.6682 19 22.5 18.2775 22.5 17.3846V6.61538C22.5 5.728 21.6744 5 20.6543 5ZM20.3994 6.07692L12.0391 13.394L3.60652 6.07692H20.3994ZM2.73047 17.1616V6.83325L8.65637 11.9752L2.73047 17.1616ZM3.60053 17.9231L9.53016 12.7334L11.5898 14.441C11.7744 14.5667 12.2871 14.5667 12.4922 14.441L14.502 12.7615L20.3995 17.9231H3.60053ZM21.2695 17.1616L15.372 12L21.2695 6.83838V17.1616Z"/>
        </svg>
        <span class="aside-menu__label">{t}Сообщения{/t} ({$data.new_count})</span>
    </a>
</li>