new class Warehouse extends RsJsCore.classes.component
{
    constructor() {
        super();

        document.addEventListener('DOMContentLoaded', (event) => {
            let element = document.querySelector('#warehouseMap');
            let dataElement = document.querySelector('#warehouseMapParams');
            let data = JSON.parse(dataElement.innerHTML);
            RsJsCore.plugins.mapManager.map.init(element, (map) => {
                map.setCenter(parseFloat(data.lat), parseFloat(data.lng), parseInt(data.zoom));
                let point = map.createPoint(parseFloat(data.lat), parseFloat(data.lng));
                map.pointSetBalloon(point, '', data.balloon, '')
                map.addPoint(point);
            });
        });
    }
};