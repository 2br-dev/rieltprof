new class MapManager extends RsJsCore.classes.plugin {
    constructor() {
        super();
        if (global.mapParams.map_type == 'google') {
            this.map = RsJsCore.plugins.googleMap;
        } else {
            this.map = RsJsCore.plugins.yandexMap;
        }
    }

    initMap(element, callback) {
        this.map.init(element, callback);
    }
};
