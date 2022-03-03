/**
 * Устанавливает стили ширины заднего фона для блоков дизайнера
 */
function setDesignerBackgroundWidths()
{
    let styleBlock = document.querySelector('#designer-block-width-style');
    if (styleBlock){
        styleBlock.remove();
    }
    let styles = "";
    let windowWidth = window.innerWidth;
    document.querySelectorAll('.d-row-wrapper.d-full-width').forEach((designerBlock) => {
        let parent = designerBlock.closest('.designer-block');
        let parentWidth = parent.getBoundingClientRect().width;
        if (windowWidth > parentWidth){
            let margin = (windowWidth - parentWidth) / 2;
            let designerId = '#d-row-' + designerBlock.dataset['id'];
            styles += `${designerId}{
                margin-left: -${margin}px;
                margin-right: -${margin}px;
            }`;
        }
    });

    if (styles.length){
        document.querySelector('body').insertAdjacentHTML('beforeend', `<style id="designer-block-width-style">${styles}</style>`);
    }
}

/**
 * Смотрит все блоки дизайнера, которые заблокированы и показывает сообщение
 */
function checkDesignerBlockIsLocked()
{
    document.querySelectorAll('.d-atom-license-blocked').forEach((designerBlock) => {
        let error = designerBlock.dataset['licenseError'];
        designerBlock.insertAdjacentHTML("beforeend", '<span class="d-atom-license-error">' + error + '</span>')
    });
}

//Изменим ширину наших блоков дизайнера
document.addEventListener("DOMContentLoaded", function(event) {
    setDesignerBackgroundWidths();
    checkDesignerBlockIsLocked();
});
window.addEventListener("resize", function(event) {
    setDesignerBackgroundWidths();
});


var ie = 0; //Посмотрим версию браузера, если это ie
try {
    var mIE = navigator.userAgent.match( /(MSIE |Trident.*rv[ :])([0-9]+)/ );
    if (mIE){
        ie = mIE[ 2 ];
    }

    if (ie > 0 && ie < 14){ //Если это старый Internet Explorer
        //Подгрузим полифил.
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.src  = '/resource/old-ie-polyfill.js';
        document.body.appendChild(s);
    }
} catch(e){
    console.error(e);
}
