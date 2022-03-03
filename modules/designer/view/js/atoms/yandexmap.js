/**
 * Возвращает подготовленный массив точек для карты
 * @param {Object} yamap_set - настройки атома Яндекс Карты
 * @return Array
 */
function getDYaPoints(points)
{
  points = JSON.parse(points);
  let data_points = {
    type: "FeatureCollection",
    features: []
  };
  points.forEach((item, i) => {
    let data_point = {
      type: "Feature",
      id: i,
      geometry: {
        type: "Point",
        coordinates: [item.lat, item.long]
      },
      properties: {
        balloonContentBody: item.balloon,
        hintContent: item.title
      }
    };
    data_points.features.push(data_point);
  });
  return data_points;
}

/**
 * Возвращает конролы карты
 * @param {Object} yamap_set - настройки атома Яндекс Карты
 *
 * @return Array
 */
function getDYaMapControls(yamap_set)
{
  let controls = [];
  if (yamap_set.showSearch){
    controls.push('searchControl');
  }
  if (yamap_set.showTypeSelector){
    controls.push('typeSelector');
  }
  if (yamap_set.showFullscreen){
    controls.push('fullscreenControl');
  }
  if (yamap_set.showRouteButton){
    controls.push('routeButtonControl');
  }
  if (yamap_set.showZoom){
    controls.push('zoomControl');
  }
  return controls;
}

/**
 * Действия при старте карты
 *
 * @param {Object} yamap_set - настройки атома Яндекс Карты
 * @param {HTMLElement} atom_wrapper - обёртка атома
 */
function dYaMapReady(yamap_set, atom_wrapper) {
  //Создаём карту
  let mapInstance = new ymaps.Map(atom_wrapper.id, {
    center: [+yamap_set.mapLat, +yamap_set.mapLong],
    zoom: +yamap_set.zoom,
    controls: getDYaMapControls(yamap_set)
  });

  //Создадим массив точек
  let data_points;
  if (yamap_set.points){
    data_points = getDYaPoints(yamap_set.points);
  }

  if (!yamap_set.enableScrollZoom){
    mapInstance.behaviors.disable('scrollZoom');
  }else{
    mapInstance.behaviors.enable('scrollZoom');
  }

  //Объединяем в кластер
  let objectManager = new ymaps.ObjectManager({
    clusterize: true,
    gridSize: 32,
    clusterDisableClickZoom: false
  });
  objectManager.objects.options.set("preset", "islands#blueDotIcon");
  objectManager.clusters.options.set(
      "preset",
      "islands#blueClusterIcons"
  );
  mapInstance.geoObjects.add(objectManager);
  if (yamap_set.points) {
    objectManager.add(data_points);
  }
}

/**
 * Инициализация настрек карты
 *
 * @param {Element} wrapper - обёртка карты
 */
function initYandexMap(wrapper) {
  let atom_wrapper = wrapper.querySelector(".d-atom-item");
  let yamap_set = atom_wrapper.dataset;

  let int = setInterval(() => {
    if (ymaps){
      clearInterval(int);
      ymaps.ready(function(){
        dYaMapReady(yamap_set, atom_wrapper);
      });
    }
  }, 100);
}
/**
 * Инициализация карты
 */
document.addEventListener("DOMContentLoaded", e => {
  document
    .querySelectorAll(".d-atom-yandexmap")
    .forEach(yandexmap_wrapper => initYandexMap(yandexmap_wrapper));
});
