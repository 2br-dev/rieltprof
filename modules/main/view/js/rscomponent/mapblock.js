class MapBlockItem {
    constructor(element) {
        this.owner = element;

        RsJsCore.plugins.mapManager.initMap(this.owner, (map) => {
            this.map = map;
            this.map.setEnableScrollZoom(false);

            let points = JSON.parse(this.owner.dataset.points);
            let coords_x = [];
            let coords_y = [];

            points.forEach(item => {
                let point = this.map.createPoint(item.lat, item.lon);
                if (item.balloonContent) {
                    this.map.pointSetBalloon(point, '', item.balloonContent, '');
                }
                this.map.addPoint(point);

                coords_x.push(parseFloat(item.coord_x));
                coords_y.push(parseFloat(item.coord_y));
            });

            if (points.length > 1) {
                map.setBounds(Math.max.apply(Math, coords_y), Math.min.apply(Math, coords_y), Math.min.apply(Math, coords_x), Math.max.apply(Math, coords_x));
            } else {
                map.setCenter(points[0]['lat'], points[0]['lon'], this.owner.dataset.zoom);
            }
        });
    }

    static init(element) {
        if (!element.mapBlock) {
            element.mapBlock = new MapBlockItem(element);
        }
    }
};

new class MapBlock extends RsJsCore.classes.component {
    constructor() {
        super();
        this.selector = {
            map: '.rs-mapBlock',
        };
    }

    onContentReady() {
        document.querySelectorAll(this.selector.map).forEach((element) => {
            MapBlockItem.init(element);
        });
    }
};