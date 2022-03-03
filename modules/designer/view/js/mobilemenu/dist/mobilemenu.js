/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "../../../../../../../../denwerready/-readyscript/modules/designer/view/js/mobilemenu/src/mobilemenu_source.js":
/*!************************************************************************************************!*\
  !*** D:/denwerready/-readyscript/modules/designer/view/js/mobilemenu/src/mobilemenu_source.js ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var mmenu_event_click = 'ontouchstart' in document.documentElement && navigator.userAgent.match(/Mobi/) ? 'touchstart' : 'click'; //Какое событие использовать

var mobileMMenu =
/*#__PURE__*/
function () {
  function mobileMMenu() {
    _classCallCheck(this, mobileMMenu);
  }

  _createClass(mobileMMenu, [{
    key: "openMenu",

    /**
     * Открытие самого меню
     * @param {MouseEvent} event - событие нажатия
     */
    value: function openMenu(event) {
      var body = document.querySelector('body');
      body.insertAdjacentHTML("beforeend", "\n            <div id=\"d-mobile-mmenu\" class=\"d-mobile-mmenu\">\n                <div id=\"d-mobile-mmenu-header\" class=\"d-mobile-mmenu-header\"></div>\n                <a  id=\"d-mobile-mmenu-close\" class=\"d-mobile-mmenu-close\"></a>\n                <div class=\"d-mobile-mmenu-content\"></div>\n            </div>\n            <div id=\"d-mobile-mmenu-fog\" class=\"d-mobile-mmenu-fog\"></div>\n        ");
      body.classList.add('mmenu-hidden');
      var left_menu = document.querySelector("#d-mobile-mmenu");
      var menu_id = event.target.closest('a').dataset['id'];
      var menu_wrapper = document.querySelector("[data-mmenu-id=\"".concat(menu_id, "\"]"));
      left_menu.querySelector('.d-mobile-mmenu-content').insertAdjacentHTML("beforeend", menu_wrapper.innerHTML);
      left_menu.querySelector('.d-mobile-mmenu-header').innerHTML = menu_wrapper.dataset.title;
      mobileMMenu.addLevelEvents();
      setTimeout(function () {
        left_menu.classList.add('d-open');
      }, 200);
    }
    /**
     * Закрытие меню
     *
     * @param {MouseEvent} event - событие нажатия
     */

  }, {
    key: "closeMenu",
    value: function closeMenu(event) {
      var _this = this;

      var id = event.target.getAttribute('id');
      var body = document.querySelector('body');

      if (id == 'd-mobile-mmenu-close' || id == 'd-mobile-mmenu-fog') {
        //Если это кнопка закрытия или подложка
        mobileMMenu.removeLevelEvents();
        var left_menu = document.querySelector("#d-mobile-mmenu");
        left_menu.classList.remove('d-open');
        body.classList.remove('mmenu-hidden');
        var event_click = new Event('click');
        var designer_close = document.querySelector('.design-menu-overflow .d-close');

        if (designer_close) {
          designer_close.dispatchEvent(event_click);
        }

        setTimeout(function () {
          //Удалим всё из dom
          body.removeEventListener(mmenu_event_click, _this.closeMenu);
          left_menu.remove();
          document.querySelector('#d-mobile-mmenu-fog').remove();
        }, 300);
      }

      var is_in_debug = body.classList.contains('debug-mode-blocks');
      var href = event.target.getAttribute('href');

      if (!is_in_debug) {
        if (href && !href.length) {
          event.stopPropagation();
          event.preventDefault();
        }
      } else {
        event.stopPropagation();
        event.preventDefault();
      }
    }
    /**
     * Открытие открытие следующего уровня меню
     *
     * @param {MouseEvent} event - событие нажатия
     */

  }, {
    key: "init",

    /**
     * Инициализация открытия мобильного меню
     */
    value: function init() {
      var _this2 = this;

      var items = document.querySelectorAll('.designer-mmenu');

      if (items) {
        items.forEach(function (menuLink) {
          menuLink.removeEventListener(mmenu_event_click, _this2.openMenu);
          menuLink.addEventListener(mmenu_event_click, _this2.openMenu);
        });
      }

      var body = document.querySelector('body');
      body.removeEventListener(mmenu_event_click, this.closeMenu);
      body.addEventListener(mmenu_event_click, this.closeMenu);
    }
  }], [{
    key: "openLevel",
    value: function openLevel(event) {
      if (!event.target.closest('li').classList.contains('d-mobile-mmenu-close-level')) {
        var left_menu = document.querySelector("#d-mobile-mmenu");
        var level_title = event.target.dataset['title'];
        var wrapper = event.target.closest('li');
        var ul = wrapper.querySelector("ul");

        if (ul) {
          wrapper.querySelector("ul").classList.add('d-open');
        }

        left_menu.querySelector('#d-mobile-mmenu-header').innerHTML = level_title;
      }
    }
    /**
     * Закрытие текущего уровня меню
     *
     * @param {MouseEvent} event - событие нажатия
     */

  }, {
    key: "closeLevel",
    value: function closeLevel(event) {
      var left_menu = document.querySelector("#d-mobile-mmenu");
      var level_title = event.target.closest('li').dataset['title'];
      event.target.closest('.d-mobile-mmenu-level').classList.remove('d-open');
      left_menu.querySelector('#d-mobile-mmenu-header').innerHTML = level_title;
      event.stopPropagation();
      event.preventDefault();
    }
    /**
     * Добавляет все события из меню
     */

  }, {
    key: "addLevelEvents",
    value: function addLevelEvents() {
      var left_menu = document.querySelector("#d-mobile-mmenu");
      var items = left_menu.querySelectorAll('.d-mobile-mmenu-close-level');
      items.forEach(function (item) {
        item.addEventListener(mmenu_event_click, mobileMMenu.closeLevel);
      });
      items = left_menu.querySelectorAll('.d-mobile-mmenu-open-level');

      if (items) {
        //Назначим событие, которое откроет следующий уровень
        items.forEach(function (item) {
          item.addEventListener(mmenu_event_click, mobileMMenu.openLevel);
        });
      }
    }
    /**
     * Удаляет все события из меню
     */

  }, {
    key: "removeLevelEvents",
    value: function removeLevelEvents() {
      var left_menu = document.querySelector("#d-mobile-mmenu");
      var items = left_menu.querySelectorAll('.d-mobile-mmenu-close-level');
      items.forEach(function (item) {
        item.removeEventListener(mmenu_event_click, mobileMMenu.closeLevel);
      });
      items = left_menu.querySelectorAll('.d-mobile-mmenu-open-level');

      if (items) {
        //Назначим событие, которое откроет следующий уровень
        items.forEach(function (item) {
          item.removeEventListener(mmenu_event_click, mobileMMenu.openLevel);
        });
      }
    }
  }]);

  return mobileMMenu;
}();

var mobileMMenuClass; //Инициализиция открытия мобильного меню

document.addEventListener("DOMContentLoaded", function (event) {
  mobileMMenuClass = new mobileMMenu();
  mobileMMenuClass.init();
}); //Делаем событие для обновления

var reinit_mmenu;
document.addEventListener("designer.init-mmenu", function (event) {
  clearTimeout(reinit_mmenu);

  if (!mobileMMenuClass) {
    //Если сущности ещё нет, т.к. она не отработала
    mobileMMenuClass = new mobileMMenu();
  }

  mobileMMenuClass.init();
});

/***/ }),

/***/ 0:
/*!****************************************!*\
  !*** multi ./src/mobilemenu_source.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/mobilemenu_source.js */"../../../../../../../../denwerready/-readyscript/modules/designer/view/js/mobilemenu/src/mobilemenu_source.js");


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vL0Q6L2RlbndlcnJlYWR5Ly1yZWFkeXNjcmlwdC9tb2R1bGVzL2Rlc2lnbmVyL3ZpZXcvanMvbW9iaWxlbWVudS9zcmMvbW9iaWxlbWVudV9zb3VyY2UuanMiXSwibmFtZXMiOlsibW1lbnVfZXZlbnRfY2xpY2siLCJkb2N1bWVudCIsImRvY3VtZW50RWxlbWVudCIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsIm1hdGNoIiwibW9iaWxlTU1lbnUiLCJldmVudCIsImJvZHkiLCJxdWVyeVNlbGVjdG9yIiwiaW5zZXJ0QWRqYWNlbnRIVE1MIiwiY2xhc3NMaXN0IiwiYWRkIiwibGVmdF9tZW51IiwibWVudV9pZCIsInRhcmdldCIsImNsb3Nlc3QiLCJkYXRhc2V0IiwibWVudV93cmFwcGVyIiwiaW5uZXJIVE1MIiwidGl0bGUiLCJhZGRMZXZlbEV2ZW50cyIsInNldFRpbWVvdXQiLCJpZCIsImdldEF0dHJpYnV0ZSIsInJlbW92ZUxldmVsRXZlbnRzIiwicmVtb3ZlIiwiZXZlbnRfY2xpY2siLCJFdmVudCIsImRlc2lnbmVyX2Nsb3NlIiwiZGlzcGF0Y2hFdmVudCIsInJlbW92ZUV2ZW50TGlzdGVuZXIiLCJjbG9zZU1lbnUiLCJpc19pbl9kZWJ1ZyIsImNvbnRhaW5zIiwiaHJlZiIsImxlbmd0aCIsInN0b3BQcm9wYWdhdGlvbiIsInByZXZlbnREZWZhdWx0IiwiaXRlbXMiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZm9yRWFjaCIsIm1lbnVMaW5rIiwib3Blbk1lbnUiLCJhZGRFdmVudExpc3RlbmVyIiwibGV2ZWxfdGl0bGUiLCJ3cmFwcGVyIiwidWwiLCJpdGVtIiwiY2xvc2VMZXZlbCIsIm9wZW5MZXZlbCIsIm1vYmlsZU1NZW51Q2xhc3MiLCJpbml0IiwicmVpbml0X21tZW51IiwiY2xlYXJUaW1lb3V0Il0sIm1hcHBpbmdzIjoiO1FBQUE7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FB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QUFTLENBQUMyQixnQkFBVixDQUEyQiw0QkFBM0IsQ0FBUjs7QUFDQSxVQUFJRCxLQUFKLEVBQVU7QUFBRTtBQUNSQSxhQUFLLENBQUNFLE9BQU4sQ0FBYyxVQUFDTyxJQUFELEVBQVU7QUFDcEJBLGNBQUksQ0FBQ2pCLG1CQUFMLENBQXlCL0IsaUJBQXpCLEVBQTRDTSxXQUFXLENBQUM0QyxTQUF4RDtBQUNILFNBRkQ7QUFHSDtBQUNKOzs7Ozs7QUFvQkwsSUFBSUMsZ0JBQUosQyxDQUNBOztBQUNBbEQsUUFBUSxDQUFDMkMsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDLFVBQVNyQyxLQUFULEVBQWdCO0FBQzFENEMsa0JBQWdCLEdBQUcsSUFBSTdDLFdBQUosRUFBbkI7QUFDQTZDLGtCQUFnQixDQUFDQyxJQUFqQjtBQUNILENBSEQsRSxDQUtBOztBQUNBLElBQUlDLFlBQUo7QUFDQXBELFFBQVEsQ0FBQzJDLGdCQUFULENBQTBCLHFCQUExQixFQUFpRCxVQUFTckMsS0FBVCxFQUFnQjtBQUM3RCtDLGNBQVksQ0FBQ0QsWUFBRCxDQUFaOztBQUNBLE1BQUksQ0FBQ0YsZ0JBQUwsRUFBc0I7QUFBRTtBQUNwQkEsb0JBQWdCLEdBQUcsSUFBSTdDLFdBQUosRUFBbkI7QUFDSDs7QUFDRDZDLGtCQUFnQixDQUFDQyxJQUFqQjtBQUNILENBTkQsRSIsImZpbGUiOiJtb2JpbGVtZW51LmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDApO1xuIiwibGV0IG1tZW51X2V2ZW50X2NsaWNrID0gKCdvbnRvdWNoc3RhcnQnIGluIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCAmJiBuYXZpZ2F0b3IudXNlckFnZW50Lm1hdGNoKC9Nb2JpLykpID8gJ3RvdWNoc3RhcnQnIDogJ2NsaWNrJzsgLy/QmtCw0LrQvtC1INGB0L7QsdGL0YLQuNC1INC40YHQv9C+0LvRjNC30L7QstCw0YLRjFxyXG5jbGFzcyBtb2JpbGVNTWVudXtcclxuICAgIC8qKlxyXG4gICAgICog0J7RgtC60YDRi9GC0LjQtSDRgdCw0LzQvtCz0L4g0LzQtdC90Y5cclxuICAgICAqIEBwYXJhbSB7TW91c2VFdmVudH0gZXZlbnQgLSDRgdC+0LHRi9GC0LjQtSDQvdCw0LbQsNGC0LjRj1xyXG4gICAgICovXHJcbiAgICBvcGVuTWVudShldmVudClcclxuICAgIHtcclxuICAgICAgICBsZXQgYm9keSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2JvZHknKTtcclxuICAgICAgICBib2R5Lmluc2VydEFkamFjZW50SFRNTChcImJlZm9yZWVuZFwiLCBgXHJcbiAgICAgICAgICAgIDxkaXYgaWQ9XCJkLW1vYmlsZS1tbWVudVwiIGNsYXNzPVwiZC1tb2JpbGUtbW1lbnVcIj5cclxuICAgICAgICAgICAgICAgIDxkaXYgaWQ9XCJkLW1vYmlsZS1tbWVudS1oZWFkZXJcIiBjbGFzcz1cImQtbW9iaWxlLW1tZW51LWhlYWRlclwiPjwvZGl2PlxyXG4gICAgICAgICAgICAgICAgPGEgIGlkPVwiZC1tb2JpbGUtbW1lbnUtY2xvc2VcIiBjbGFzcz1cImQtbW9iaWxlLW1tZW51LWNsb3NlXCI+PC9hPlxyXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cImQtbW9iaWxlLW1tZW51LWNvbnRlbnRcIj48L2Rpdj5cclxuICAgICAgICAgICAgPC9kaXY+XHJcbiAgICAgICAgICAgIDxkaXYgaWQ9XCJkLW1vYmlsZS1tbWVudS1mb2dcIiBjbGFzcz1cImQtbW9iaWxlLW1tZW51LWZvZ1wiPjwvZGl2PlxyXG4gICAgICAgIGApO1xyXG4gICAgICAgIGJvZHkuY2xhc3NMaXN0LmFkZCgnbW1lbnUtaGlkZGVuJyk7XHJcbiAgICAgICAgbGV0IGxlZnRfbWVudSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjZC1tb2JpbGUtbW1lbnVcIik7XHJcbiAgICAgICAgbGV0IG1lbnVfaWQgPSBldmVudC50YXJnZXQuY2xvc2VzdCgnYScpLmRhdGFzZXRbJ2lkJ107XHJcbiAgICAgICAgbGV0IG1lbnVfd3JhcHBlciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoYFtkYXRhLW1tZW51LWlkPVwiJHttZW51X2lkfVwiXWApO1xyXG5cclxuICAgICAgICBsZWZ0X21lbnUucXVlcnlTZWxlY3RvcignLmQtbW9iaWxlLW1tZW51LWNvbnRlbnQnKS5pbnNlcnRBZGphY2VudEhUTUwoXCJiZWZvcmVlbmRcIiwgbWVudV93cmFwcGVyLmlubmVySFRNTCk7XHJcbiAgICAgICAgbGVmdF9tZW51LnF1ZXJ5U2VsZWN0b3IoJy5kLW1vYmlsZS1tbWVudS1oZWFkZXInKS5pbm5lckhUTUwgPSBtZW51X3dyYXBwZXIuZGF0YXNldC50aXRsZTtcclxuICAgICAgICBtb2JpbGVNTWVudS5hZGRMZXZlbEV2ZW50cygpO1xyXG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xyXG4gICAgICAgICAgICBsZWZ0X21lbnUuY2xhc3NMaXN0LmFkZCgnZC1vcGVuJyk7XHJcbiAgICAgICAgfSwgMjAwKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqINCX0LDQutGA0YvRgtC40LUg0LzQtdC90Y5cclxuICAgICAqXHJcbiAgICAgKiBAcGFyYW0ge01vdXNlRXZlbnR9IGV2ZW50IC0g0YHQvtCx0YvRgtC40LUg0L3QsNC20LDRgtC40Y9cclxuICAgICAqL1xyXG4gICAgY2xvc2VNZW51KGV2ZW50KVxyXG4gICAge1xyXG4gICAgICAgIGxldCBpZCA9IGV2ZW50LnRhcmdldC5nZXRBdHRyaWJ1dGUoJ2lkJyk7XHJcbiAgICAgICAgbGV0IGJvZHkgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdib2R5Jyk7XHJcbiAgICAgICAgaWYgKGlkID09ICdkLW1vYmlsZS1tbWVudS1jbG9zZScgfHwgaWQgPT0gJ2QtbW9iaWxlLW1tZW51LWZvZycpIHsgLy/QldGB0LvQuCDRjdGC0L4g0LrQvdC+0L/QutCwINC30LDQutGA0YvRgtC40Y8g0LjQu9C4INC/0L7QtNC70L7QttC60LBcclxuICAgICAgICAgICAgbW9iaWxlTU1lbnUucmVtb3ZlTGV2ZWxFdmVudHMoKTtcclxuICAgICAgICAgICAgbGV0IGxlZnRfbWVudSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjZC1tb2JpbGUtbW1lbnVcIik7XHJcbiAgICAgICAgICAgIGxlZnRfbWVudS5jbGFzc0xpc3QucmVtb3ZlKCdkLW9wZW4nKTtcclxuXHJcbiAgICAgICAgICAgIGJvZHkuY2xhc3NMaXN0LnJlbW92ZSgnbW1lbnUtaGlkZGVuJyk7XHJcbiAgICAgICAgICAgIGxldCBldmVudF9jbGljayA9IG5ldyBFdmVudCgnY2xpY2snKVxyXG4gICAgICAgICAgICBsZXQgZGVzaWduZXJfY2xvc2UgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuZGVzaWduLW1lbnUtb3ZlcmZsb3cgLmQtY2xvc2UnKTtcclxuICAgICAgICAgICAgaWYgKGRlc2lnbmVyX2Nsb3NlKXtcclxuICAgICAgICAgICAgICAgIGRlc2lnbmVyX2Nsb3NlLmRpc3BhdGNoRXZlbnQoZXZlbnRfY2xpY2spO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4geyAvL9Cj0LTQsNC70LjQvCDQstGB0ZEg0LjQtyBkb21cclxuICAgICAgICAgICAgICAgIGJvZHkucmVtb3ZlRXZlbnRMaXN0ZW5lcihtbWVudV9ldmVudF9jbGljaywgdGhpcy5jbG9zZU1lbnUpO1xyXG4gICAgICAgICAgICAgICAgbGVmdF9tZW51LnJlbW92ZSgpO1xyXG4gICAgICAgICAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2QtbW9iaWxlLW1tZW51LWZvZycpLnJlbW92ZSgpO1xyXG4gICAgICAgICAgICB9LCAzMDApO1xyXG4gICAgICAgIH1cclxuICAgICAgICBsZXQgaXNfaW5fZGVidWcgPSBib2R5LmNsYXNzTGlzdC5jb250YWlucygnZGVidWctbW9kZS1ibG9ja3MnKTtcclxuICAgICAgICBsZXQgaHJlZiA9IGV2ZW50LnRhcmdldC5nZXRBdHRyaWJ1dGUoJ2hyZWYnKTtcclxuICAgICAgICBpZiAoIWlzX2luX2RlYnVnKXtcclxuICAgICAgICAgICAgaWYgKGhyZWYgJiYgIWhyZWYubGVuZ3RoKXtcclxuICAgICAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiDQntGC0LrRgNGL0YLQuNC1INC+0YLQutGA0YvRgtC40LUg0YHQu9C10LTRg9GO0YnQtdCz0L4g0YPRgNC+0LLQvdGPINC80LXQvdGOXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtNb3VzZUV2ZW50fSBldmVudCAtINGB0L7QsdGL0YLQuNC1INC90LDQttCw0YLQuNGPXHJcbiAgICAgKi9cclxuICAgIHN0YXRpYyBvcGVuTGV2ZWwoZXZlbnQpXHJcbiAgICB7XHJcbiAgICAgICAgaWYgKCFldmVudC50YXJnZXQuY2xvc2VzdCgnbGknKS5jbGFzc0xpc3QuY29udGFpbnMoJ2QtbW9iaWxlLW1tZW51LWNsb3NlLWxldmVsJykpe1xyXG4gICAgICAgICAgICBsZXQgbGVmdF9tZW51ICAgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiI2QtbW9iaWxlLW1tZW51XCIpO1xyXG4gICAgICAgICAgICBsZXQgbGV2ZWxfdGl0bGUgPSBldmVudC50YXJnZXQuZGF0YXNldFsndGl0bGUnXTtcclxuICAgICAgICAgICAgbGV0IHdyYXBwZXIgPSBldmVudC50YXJnZXQuY2xvc2VzdCgnbGknKTtcclxuICAgICAgICAgICAgbGV0IHVsID0gd3JhcHBlci5xdWVyeVNlbGVjdG9yKGB1bGApO1xyXG4gICAgICAgICAgICBpZiAodWwpe1xyXG4gICAgICAgICAgICAgICAgd3JhcHBlci5xdWVyeVNlbGVjdG9yKGB1bGApLmNsYXNzTGlzdC5hZGQoJ2Qtb3BlbicpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGxlZnRfbWVudS5xdWVyeVNlbGVjdG9yKCcjZC1tb2JpbGUtbW1lbnUtaGVhZGVyJykuaW5uZXJIVE1MID0gbGV2ZWxfdGl0bGU7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICog0JfQsNC60YDRi9GC0LjQtSDRgtC10LrRg9GJ0LXQs9C+INGD0YDQvtCy0L3RjyDQvNC10L3RjlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7TW91c2VFdmVudH0gZXZlbnQgLSDRgdC+0LHRi9GC0LjQtSDQvdCw0LbQsNGC0LjRj1xyXG4gICAgICovXHJcbiAgICBzdGF0aWMgY2xvc2VMZXZlbChldmVudClcclxuICAgIHtcclxuICAgICAgICBsZXQgbGVmdF9tZW51ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcIiNkLW1vYmlsZS1tbWVudVwiKTtcclxuICAgICAgICBsZXQgbGV2ZWxfdGl0bGUgPSBldmVudC50YXJnZXQuY2xvc2VzdCgnbGknKS5kYXRhc2V0Wyd0aXRsZSddO1xyXG4gICAgICAgIGV2ZW50LnRhcmdldC5jbG9zZXN0KCcuZC1tb2JpbGUtbW1lbnUtbGV2ZWwnKS5jbGFzc0xpc3QucmVtb3ZlKCdkLW9wZW4nKTtcclxuICAgICAgICBsZWZ0X21lbnUucXVlcnlTZWxlY3RvcignI2QtbW9iaWxlLW1tZW51LWhlYWRlcicpLmlubmVySFRNTCA9IGxldmVsX3RpdGxlO1xyXG4gICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiDQlNC+0LHQsNCy0LvRj9C10YIg0LLRgdC1INGB0L7QsdGL0YLQuNGPINC40Lcg0LzQtdC90Y5cclxuICAgICAqL1xyXG4gICAgc3RhdGljIGFkZExldmVsRXZlbnRzKClcclxuICAgIHtcclxuICAgICAgICBsZXQgbGVmdF9tZW51ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcIiNkLW1vYmlsZS1tbWVudVwiKTtcclxuICAgICAgICBsZXQgaXRlbXMgPSBsZWZ0X21lbnUucXVlcnlTZWxlY3RvckFsbCgnLmQtbW9iaWxlLW1tZW51LWNsb3NlLWxldmVsJyk7XHJcbiAgICAgICAgaXRlbXMuZm9yRWFjaCgoaXRlbSkgPT4ge1xyXG4gICAgICAgICAgICBpdGVtLmFkZEV2ZW50TGlzdGVuZXIobW1lbnVfZXZlbnRfY2xpY2ssIG1vYmlsZU1NZW51LmNsb3NlTGV2ZWwpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGl0ZW1zID0gbGVmdF9tZW51LnF1ZXJ5U2VsZWN0b3JBbGwoJy5kLW1vYmlsZS1tbWVudS1vcGVuLWxldmVsJyk7XHJcbiAgICAgICAgaWYgKGl0ZW1zKXsgLy/QndCw0LfQvdCw0YfQuNC8INGB0L7QsdGL0YLQuNC1LCDQutC+0YLQvtGA0L7QtSDQvtGC0LrRgNC+0LXRgiDRgdC70LXQtNGD0Y7RidC40Lkg0YPRgNC+0LLQtdC90YxcclxuICAgICAgICAgICAgaXRlbXMuZm9yRWFjaCgoaXRlbSkgPT4ge1xyXG4gICAgICAgICAgICAgICAgaXRlbS5hZGRFdmVudExpc3RlbmVyKG1tZW51X2V2ZW50X2NsaWNrLCBtb2JpbGVNTWVudS5vcGVuTGV2ZWwpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiDQo9C00LDQu9GP0LXRgiDQstGB0LUg0YHQvtCx0YvRgtC40Y8g0LjQtyDQvNC10L3RjlxyXG4gICAgICovXHJcbiAgICBzdGF0aWMgcmVtb3ZlTGV2ZWxFdmVudHMoKVxyXG4gICAge1xyXG4gICAgICAgIGxldCBsZWZ0X21lbnUgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiI2QtbW9iaWxlLW1tZW51XCIpO1xyXG4gICAgICAgIGxldCBpdGVtcyA9IGxlZnRfbWVudS5xdWVyeVNlbGVjdG9yQWxsKCcuZC1tb2JpbGUtbW1lbnUtY2xvc2UtbGV2ZWwnKTtcclxuICAgICAgICBpdGVtcy5mb3JFYWNoKChpdGVtKSA9PiB7XHJcbiAgICAgICAgICAgIGl0ZW0ucmVtb3ZlRXZlbnRMaXN0ZW5lcihtbWVudV9ldmVudF9jbGljaywgbW9iaWxlTU1lbnUuY2xvc2VMZXZlbCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbXMgPSBsZWZ0X21lbnUucXVlcnlTZWxlY3RvckFsbCgnLmQtbW9iaWxlLW1tZW51LW9wZW4tbGV2ZWwnKTtcclxuICAgICAgICBpZiAoaXRlbXMpeyAvL9Cd0LDQt9C90LDRh9C40Lwg0YHQvtCx0YvRgtC40LUsINC60L7RgtC+0YDQvtC1INC+0YLQutGA0L7QtdGCINGB0LvQtdC00YPRjtGJ0LjQuSDRg9GA0L7QstC10L3RjFxyXG4gICAgICAgICAgICBpdGVtcy5mb3JFYWNoKChpdGVtKSA9PiB7XHJcbiAgICAgICAgICAgICAgICBpdGVtLnJlbW92ZUV2ZW50TGlzdGVuZXIobW1lbnVfZXZlbnRfY2xpY2ssIG1vYmlsZU1NZW51Lm9wZW5MZXZlbCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqINCY0L3QuNGG0LjQsNC70LjQt9Cw0YbQuNGPINC+0YLQutGA0YvRgtC40Y8g0LzQvtCx0LjQu9GM0L3QvtCz0L4g0LzQtdC90Y5cclxuICAgICAqL1xyXG4gICAgaW5pdCgpXHJcbiAgICB7XHJcbiAgICAgICAgbGV0IGl0ZW1zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmRlc2lnbmVyLW1tZW51Jyk7XHJcbiAgICAgICAgaWYgKGl0ZW1zKXtcclxuICAgICAgICAgICAgaXRlbXMuZm9yRWFjaCgobWVudUxpbmspID0+IHtcclxuICAgICAgICAgICAgICAgIG1lbnVMaW5rLnJlbW92ZUV2ZW50TGlzdGVuZXIobW1lbnVfZXZlbnRfY2xpY2ssIHRoaXMub3Blbk1lbnUpO1xyXG4gICAgICAgICAgICAgICAgbWVudUxpbmsuYWRkRXZlbnRMaXN0ZW5lcihtbWVudV9ldmVudF9jbGljaywgdGhpcy5vcGVuTWVudSk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBsZXQgYm9keSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2JvZHknKTtcclxuICAgICAgICBib2R5LnJlbW92ZUV2ZW50TGlzdGVuZXIobW1lbnVfZXZlbnRfY2xpY2ssIHRoaXMuY2xvc2VNZW51KTtcclxuICAgICAgICBib2R5LmFkZEV2ZW50TGlzdGVuZXIobW1lbnVfZXZlbnRfY2xpY2ssIHRoaXMuY2xvc2VNZW51KTtcclxuICAgIH1cclxufVxyXG5cclxubGV0IG1vYmlsZU1NZW51Q2xhc3M7XHJcbi8v0JjQvdC40YbQuNCw0LvQuNC30LjRhtC40Y8g0L7RgtC60YDRi9GC0LjRjyDQvNC+0LHQuNC70YzQvdC+0LPQviDQvNC10L3RjlxyXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwiRE9NQ29udGVudExvYWRlZFwiLCBmdW5jdGlvbihldmVudCkge1xyXG4gICAgbW9iaWxlTU1lbnVDbGFzcyA9IG5ldyBtb2JpbGVNTWVudSgpO1xyXG4gICAgbW9iaWxlTU1lbnVDbGFzcy5pbml0KCk7XHJcbn0pO1xyXG5cclxuLy/QlNC10LvQsNC10Lwg0YHQvtCx0YvRgtC40LUg0LTQu9GPINC+0LHQvdC+0LLQu9C10L3QuNGPXHJcbmxldCByZWluaXRfbW1lbnU7XHJcbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJkZXNpZ25lci5pbml0LW1tZW51XCIsIGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICBjbGVhclRpbWVvdXQocmVpbml0X21tZW51KTtcclxuICAgIGlmICghbW9iaWxlTU1lbnVDbGFzcyl7IC8v0JXRgdC70Lgg0YHRg9GJ0L3QvtGB0YLQuCDQtdGJ0ZEg0L3QtdGCLCDRgi7Qui4g0L7QvdCwINC90LUg0L7RgtGA0LDQsdC+0YLQsNC70LBcclxuICAgICAgICBtb2JpbGVNTWVudUNsYXNzID0gbmV3IG1vYmlsZU1NZW51KCk7XHJcbiAgICB9XHJcbiAgICBtb2JpbGVNTWVudUNsYXNzLmluaXQoKTtcclxufSk7Il0sInNvdXJjZVJvb3QiOiIifQ==