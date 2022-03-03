! function() {
    var _$detectWebpSupport_4 = {},
        __awaiter = this && this.__awaiter || function(e, r, t, n) {
            return new(t || (t = Promise))(function(i, o) {
                function a(e) {
                    try {
                        f(n.next(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function u(e) {
                    try {
                        f(n.throw(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function f(e) {
                    e.done ? i(e.value) : new t(function(r) {
                        r(e.value)
                    }).then(a, u)
                }
                f((n = n.apply(e, r || [])).next())
            })
        },
        __generator = this && this.__generator || function(e, r) {
            var t, n, i, o, a = {
                label: 0,
                sent: function() {
                    if (1 & i[0]) throw i[1];
                    return i[1]
                },
                trys: [],
                ops: []
            };
            return o = {
                next: u(0),
                throw: u(1),
                return: u(2)
            }, "function" == typeof Symbol && (o[Symbol.iterator] = function() {
                return this
            }), o;

            function u(o) {
                return function(u) {
                    return function(o) {
                        if (t) throw new TypeError("Generator is already executing.");
                        for (; a;) try {
                            if (t = 1, n && (i = 2 & o[0] ? n.return : o[0] ? n.throw || ((i = n.return) && i.call(n), 0) : n.next) && !(i = i.call(n, o[1])).done) return i;
                            switch (n = 0, i && (o = [2 & o[0], i.value]), o[0]) {
                                case 0:
                                case 1:
                                    i = o;
                                    break;
                                case 4:
                                    return a.label++, {
                                        value: o[1],
                                        done: !1
                                    };
                                case 5:
                                    a.label++, n = o[1], o = [0];
                                    continue;
                                case 7:
                                    o = a.ops.pop(), a.trys.pop();
                                    continue;
                                default:
                                    if (!(i = (i = a.trys).length > 0 && i[i.length - 1]) && (6 === o[0] || 2 === o[0])) {
                                        a = 0;
                                        continue
                                    }
                                    if (3 === o[0] && (!i || o[1] > i[0] && o[1] < i[3])) {
                                        a.label = o[1];
                                        break
                                    }
                                    if (6 === o[0] && a.label < i[1]) {
                                        a.label = i[1], i = o;
                                        break
                                    }
                                    if (i && a.label < i[2]) {
                                        a.label = i[2], a.ops.push(o);
                                        break
                                    }
                                    i[2] && a.ops.pop(), a.trys.pop();
                                    continue
                            }
                            o = r.call(e, a)
                        } catch (u) {
                            o = [6, u], n = 0
                        } finally {
                            t = i = 0
                        }
                        if (5 & o[0]) throw o[1];
                        return {
                            value: o[0] ? o[1] : void 0,
                            done: !0
                        }
                    }([o, u])
                }
            }
        };
    Object.defineProperty(_$detectWebpSupport_4, "__esModule", {
        value: !0
    }), _$detectWebpSupport_4.detectWebpSupport = function() {
        return __awaiter(this, void 0, void 0, function() {
            var e, r;
            return __generator(this, function(t) {
                switch (t.label) {
                    case 0:
                        return e = ["data:image/webp;base64,UklGRjIAAABXRUJQVlA4ICYAAACyAgCdASoCAAEALmk0mk0iIiIiIgBoSygABc6zbAAA/v56QAAAAA==", "data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAQAAAAfQ//73v/+BiOh/AAA="], r = function(e) {
                            return new Promise(function(r, t) {
                                var n = document.createElement("img");
                                n.onerror = function(e) {
                                    return r(!1)
                                }, n.onload = function() {
                                    return r(!0)
                                }, n.src = e
                            })
                        }, [4, Promise.all(e.map(r))];
                    case 1:
                        return [2, t.sent().every(function(e) {
                            return !!e
                        })]
                }
            })
        })
    };
    var _$loadBinaryData_6 = {},
        extendStatics, __extends = this && this.__extends || (extendStatics = function(e, r) {
            return (extendStatics = Object.setPrototypeOf || {
                    __proto__: []
                }
                instanceof Array && function(e, r) {
                    e.__proto__ = r
                } || function(e, r) {
                    for (var t in r) r.hasOwnProperty(t) && (e[t] = r[t])
                })(e, r)
        }, function(e, r) {
            function t() {
                this.constructor = e
            }
            extendStatics(e, r), e.prototype = null === r ? Object.create(r) : (t.prototype = r.prototype, new t)
        }),
        ____awaiter_6 = this && this.__awaiter || function(e, r, t, n) {
            return new(t || (t = Promise))(function(i, o) {
                function a(e) {
                    try {
                        f(n.next(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function u(e) {
                    try {
                        f(n.throw(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function f(e) {
                    e.done ? i(e.value) : new t(function(r) {
                        r(e.value)
                    }).then(a, u)
                }
                f((n = n.apply(e, r || [])).next())
            })
        },
        ____generator_6 = this && this.__generator || function(e, r) {
            var t, n, i, o, a = {
                label: 0,
                sent: function() {
                    if (1 & i[0]) throw i[1];
                    return i[1]
                },
                trys: [],
                ops: []
            };
            return o = {
                next: u(0),
                throw: u(1),
                return: u(2)
            }, "function" == typeof Symbol && (o[Symbol.iterator] = function() {
                return this
            }), o;

            function u(o) {
                return function(u) {
                    return function(o) {
                        if (t) throw new TypeError("Generator is already executing.");
                        for (; a;) try {
                            if (t = 1, n && (i = 2 & o[0] ? n.return : o[0] ? n.throw || ((i = n.return) && i.call(n), 0) : n.next) && !(i = i.call(n, o[1])).done) return i;
                            switch (n = 0, i && (o = [2 & o[0], i.value]), o[0]) {
                                case 0:
                                case 1:
                                    i = o;
                                    break;
                                case 4:
                                    return a.label++, {
                                        value: o[1],
                                        done: !1
                                    };
                                case 5:
                                    a.label++, n = o[1], o = [0];
                                    continue;
                                case 7:
                                    o = a.ops.pop(), a.trys.pop();
                                    continue;
                                default:
                                    if (!(i = (i = a.trys).length > 0 && i[i.length - 1]) && (6 === o[0] || 2 === o[0])) {
                                        a = 0;
                                        continue
                                    }
                                    if (3 === o[0] && (!i || o[1] > i[0] && o[1] < i[3])) {
                                        a.label = o[1];
                                        break
                                    }
                                    if (6 === o[0] && a.label < i[1]) {
                                        a.label = i[1], i = o;
                                        break
                                    }
                                    if (i && a.label < i[2]) {
                                        a.label = i[2], a.ops.push(o);
                                        break
                                    }
                                    i[2] && a.ops.pop(), a.trys.pop();
                                    continue
                            }
                            o = r.call(e, a)
                        } catch (u) {
                            o = [6, u], n = 0
                        } finally {
                            t = i = 0
                        }
                        if (5 & o[0]) throw o[1];
                        return {
                            value: o[0] ? o[1] : void 0,
                            done: !0
                        }
                    }([o, u])
                }
            }
        };
    Object.defineProperty(_$loadBinaryData_6, "__esModule", {
        value: !0
    });
    var LoadingError = function(e) {
        function r() {
            return null !== e && e.apply(this, arguments) || this
        }
        return __extends(r, e), r
    }(Error);
    _$loadBinaryData_6.LoadingError = LoadingError, _$loadBinaryData_6.loadBinaryData = function(e) {
        return ____awaiter_6(this, void 0, void 0, function() {
            return ____generator_6(this, function(r) {
                return [2, new Promise(function(r, t) {
                    var n = new XMLHttpRequest;
                    n.open("GET", e), n.responseType = "arraybuffer";
                    var i = function() {
                        t(new LoadingError('failed to load binary data, code "' + n.status + '" from "' + e + '"'))
                    };
                    n.onerror = i, n.onreadystatechange = function() {
                        4 == n.readyState && (200 == n.status ? r(new Uint8Array(n.response)) : i())
                    }, n.send()
                })]
            })
        })
    };
    var _$_empty_1 = {},
        _$browser_3 = {},
        cachedSetTimeout, cachedClearTimeout, process = _$browser_3 = {};

    function defaultSetTimout() {
        throw new Error("setTimeout has not been defined")
    }

    function defaultClearTimeout() {
        throw new Error("clearTimeout has not been defined")
    }

    function runTimeout(e) {
        if (cachedSetTimeout === setTimeout) return setTimeout(e, 0);
        if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) return cachedSetTimeout = setTimeout, setTimeout(e, 0);
        try {
            return cachedSetTimeout(e, 0)
        } catch (r) {
            try {
                return cachedSetTimeout.call(null, e, 0)
            } catch (r) {
                return cachedSetTimeout.call(this, e, 0)
            }
        }
    }! function() {
        try {
            cachedSetTimeout = "function" == typeof setTimeout ? setTimeout : defaultSetTimout
        } catch (e) {
            cachedSetTimeout = defaultSetTimout
        }
        try {
            cachedClearTimeout = "function" == typeof clearTimeout ? clearTimeout : defaultClearTimeout
        } catch (e) {
            cachedClearTimeout = defaultClearTimeout
        }
    }();
    var currentQueue, queue = [],
        draining = !1,
        queueIndex = -1;

    function cleanUpNextTick() {
        draining && currentQueue && (draining = !1, currentQueue.length ? queue = currentQueue.concat(queue) : queueIndex = -1, queue.length && drainQueue())
    }

    function drainQueue() {
        if (!draining) {
            var e = runTimeout(cleanUpNextTick);
            draining = !0;
            for (var r = queue.length; r;) {
                for (currentQueue = queue, queue = []; ++queueIndex < r;) currentQueue && currentQueue[queueIndex].run();
                queueIndex = -1, r = queue.length
            }
            currentQueue = null, draining = !1,
                function(e) {
                    if (cachedClearTimeout === clearTimeout) return clearTimeout(e);
                    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) return cachedClearTimeout = clearTimeout, clearTimeout(e);
                    try {
                        cachedClearTimeout(e)
                    } catch (r) {
                        try {
                            return cachedClearTimeout.call(null, e)
                        } catch (r) {
                            return cachedClearTimeout.call(this, e)
                        }
                    }
                }(e)
        }
    }

    function Item(e, r) {
        this.fun = e, this.array = r
    }

    function noop() {}
    process.nextTick = function(e) {
        var r = new Array(arguments.length - 1);
        if (arguments.length > 1)
            for (var t = 1; t < arguments.length; t++) r[t - 1] = arguments[t];
        queue.push(new Item(e, r)), 1 !== queue.length || draining || runTimeout(drainQueue)
    }, Item.prototype.run = function() {
        this.fun.apply(null, this.array)
    }, process.title = "browser", process.browser = !0, process.env = {}, process.argv = [], process.version = "", process.versions = {}, process.on = noop, process.addListener = noop, process.once = noop, process.off = noop, process.removeListener = noop, process.removeAllListeners = noop, process.emit = noop, process.prependListener = noop, process.prependOnceListener = noop, process.listeners = function(e) {
        return []
    }, process.binding = function(e) {
        throw new Error("process.binding is not supported")
    }, process.cwd = function() {
        return "/"
    }, process.chdir = function(e) {
        throw new Error("process.chdir is not supported")
    }, process.umask = function() {
        return 0
    };
    var _$pathBrowserify_2 = {};
    (function(e) {
        function r(e, r) {
            for (var t = 0, n = e.length - 1; n >= 0; n--) {
                var i = e[n];
                "." === i ? e.splice(n, 1) : ".." === i ? (e.splice(n, 1), t++) : t && (e.splice(n, 1), t--)
            }
            if (r)
                for (; t--; t) e.unshift("..");
            return e
        }

        function t(e, r) {
            if (e.filter) return e.filter(r);
            for (var t = [], n = 0; n < e.length; n++) r(e[n], n, e) && t.push(e[n]);
            return t
        }
        _$pathBrowserify_2.resolve = function() {
            for (var n = "", i = !1, o = arguments.length - 1; o >= -1 && !i; o--) {
                var a = o >= 0 ? arguments[o] : e.cwd();
                if ("string" != typeof a) throw new TypeError("Arguments to path.resolve must be strings");
                a && (n = a + "/" + n, i = "/" === a.charAt(0))
            }
            return (i ? "/" : "") + (n = r(t(n.split("/"), function(e) {
                return !!e
            }), !i).join("/")) || "."
        }, _$pathBrowserify_2.normalize = function(e) {
            var i = _$pathBrowserify_2.isAbsolute(e),
                o = "/" === n(e, -1);
            return (e = r(t(e.split("/"), function(e) {
                return !!e
            }), !i).join("/")) || i || (e = "."), e && o && (e += "/"), (i ? "/" : "") + e
        }, _$pathBrowserify_2.isAbsolute = function(e) {
            return "/" === e.charAt(0)
        }, _$pathBrowserify_2.join = function() {
            var e = Array.prototype.slice.call(arguments, 0);
            return _$pathBrowserify_2.normalize(t(e, function(e, r) {
                if ("string" != typeof e) throw new TypeError("Arguments to path.join must be strings");
                return e
            }).join("/"))
        }, _$pathBrowserify_2.relative = function(e, r) {
            function t(e) {
                for (var r = 0; r < e.length && "" === e[r]; r++);
                for (var t = e.length - 1; t >= 0 && "" === e[t]; t--);
                return r > t ? [] : e.slice(r, t - r + 1)
            }
            e = _$pathBrowserify_2.resolve(e).substr(1), r = _$pathBrowserify_2.resolve(r).substr(1);
            for (var n = t(e.split("/")), i = t(r.split("/")), o = Math.min(n.length, i.length), a = o, u = 0; u < o; u++)
                if (n[u] !== i[u]) {
                    a = u;
                    break
                }
            var f = [];
            for (u = a; u < n.length; u++) f.push("..");
            return (f = f.concat(i.slice(a))).join("/")
        }, _$pathBrowserify_2.sep = "/", _$pathBrowserify_2.delimiter = ":", _$pathBrowserify_2.dirname = function(e) {
            if ("string" != typeof e && (e += ""), 0 === e.length) return ".";
            for (var r = e.charCodeAt(0), t = 47 === r, n = -1, i = !0, o = e.length - 1; o >= 1; --o)
                if (47 === (r = e.charCodeAt(o))) {
                    if (!i) {
                        n = o;
                        break
                    }
                } else i = !1;
            return -1 === n ? t ? "/" : "." : t && 1 === n ? "/" : e.slice(0, n)
        }, _$pathBrowserify_2.basename = function(e, r) {
            var t = function(e) {
                "string" != typeof e && (e += "");
                var r, t = 0,
                    n = -1,
                    i = !0;
                for (r = e.length - 1; r >= 0; --r)
                    if (47 === e.charCodeAt(r)) {
                        if (!i) {
                            t = r + 1;
                            break
                        }
                    } else -1 === n && (i = !1, n = r + 1);
                return -1 === n ? "" : e.slice(t, n)
            }(e);
            return r && t.substr(-1 * r.length) === r && (t = t.substr(0, t.length - r.length)), t
        }, _$pathBrowserify_2.extname = function(e) {
            "string" != typeof e && (e += "");
            for (var r = -1, t = 0, n = -1, i = !0, o = 0, a = e.length - 1; a >= 0; --a) {
                var u = e.charCodeAt(a);
                if (47 !== u) - 1 === n && (i = !1, n = a + 1), 46 === u ? -1 === r ? r = a : 1 !== o && (o = 1) : -1 !== r && (o = -1);
                else if (!i) {
                    t = a + 1;
                    break
                }
            }
            return -1 === r || -1 === n || 0 === o || 1 === o && r === n - 1 && r === t + 1 ? "" : e.slice(r, n)
        };
        var n = "b" === "ab".substr(-1) ? function(e, r, t) {
            return e.substr(r, t)
        } : function(e, r, t) {
            return r < 0 && (r = e.length + r), e.substr(r, t)
        }
    }).call(this, _$browser_3);
    var _$webp_9 = {
        exports: {}
    };
    (function(process) {
        function Webp() {
            var Module;
            Module || (Module = (void 0 !== Module ? Module : null) || {});
            var moduleOverrides = {};
            for (var key in Module) Module.hasOwnProperty(key) && (moduleOverrides[key] = Module[key]);
            var ENVIRONMENT_IS_WEB = !1,
                ENVIRONMENT_IS_WORKER = !1,
                ENVIRONMENT_IS_NODE = !1,
                ENVIRONMENT_IS_SHELL = !1,
                nodeFS, nodePath;
            if (Module.ENVIRONMENT)
                if ("WEB" === Module.ENVIRONMENT) ENVIRONMENT_IS_WEB = !0;
                else if ("WORKER" === Module.ENVIRONMENT) ENVIRONMENT_IS_WORKER = !0;
                else if ("NODE" === Module.ENVIRONMENT) ENVIRONMENT_IS_NODE = !0;
                else {
                    if ("SHELL" !== Module.ENVIRONMENT) throw new Error("The provided Module['ENVIRONMENT'] value is not valid. It must be one of: WEB|WORKER|NODE|SHELL.");
                    ENVIRONMENT_IS_SHELL = !0
                } else ENVIRONMENT_IS_WEB = "object" == typeof window, ENVIRONMENT_IS_WORKER = "function" == typeof importScripts, ENVIRONMENT_IS_NODE = "object" == typeof process && "function" == typeof require && !ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_WORKER, ENVIRONMENT_IS_SHELL = !ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_NODE && !ENVIRONMENT_IS_WORKER;
            if (ENVIRONMENT_IS_NODE) Module.print || (Module.print = console.log), Module.printErr || (Module.printErr = console.warn), Module.read = function(e, r) {
                nodeFS || (nodeFS = _$_empty_1), nodePath || (nodePath = _$pathBrowserify_2), e = nodePath.normalize(e);
                var t = nodeFS.readFileSync(e);
                return r ? t : t.toString()
            }, Module.readBinary = function(e) {
                var r = Module.read(e, !0);
                return r.buffer || (r = new Uint8Array(r)), r
            }, Module.load = function(e) {
                globalEval(read(e))
            }, Module.thisProgram || (process.argv.length > 1 ? Module.thisProgram = process.argv[1].replace(/\\/g, "/") : Module.thisProgram = "unknown-program"), Module.arguments = process.argv.slice(2), _$webp_9.exports = Module, process.on("uncaughtException", function(e) {
                if (!(e instanceof ExitStatus)) throw e
            }), Module.inspect = function() {
                return "[Emscripten Module object]"
            };
            else if (ENVIRONMENT_IS_SHELL) Module.print || (Module.print = print), "undefined" != typeof printErr && (Module.printErr = printErr), "undefined" != typeof read ? Module.read = read : Module.read = function() {
                throw "no read() available"
            }, Module.readBinary = function(e) {
                return "function" == typeof readbuffer ? new Uint8Array(readbuffer(e)) : read(e, "binary")
            }, "undefined" != typeof scriptArgs ? Module.arguments = scriptArgs : void 0 !== arguments && (Module.arguments = arguments), "function" == typeof quit && (Module.quit = function(e, r) {
                quit(e)
            });
            else {
                if (!ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_WORKER) throw "Unknown runtime environment. Where are we?";
                if (Module.read = function(e) {
                    var r = new XMLHttpRequest;
                    return r.open("GET", e, !1), r.send(null), r.responseText
                }, ENVIRONMENT_IS_WORKER && (Module.readBinary = function(e) {
                    var r = new XMLHttpRequest;
                    return r.open("GET", e, !1), r.responseType = "arraybuffer", r.send(null), new Uint8Array(r.response)
                }), Module.readAsync = function(e, r, t) {
                    var n = new XMLHttpRequest;
                    n.open("GET", e, !0), n.responseType = "arraybuffer", n.onload = function() {
                        200 == n.status || 0 == n.status && n.response ? r(n.response) : t()
                    }, n.onerror = t, n.send(null)
                }, void 0 !== arguments && (Module.arguments = arguments), "undefined" != typeof console) Module.print || (Module.print = function(e) {
                    console.log(e)
                }), Module.printErr || (Module.printErr = function(e) {
                    console.warn(e)
                });
                else {
                    var TRY_USE_DUMP = !1;
                    Module.print || (Module.print = TRY_USE_DUMP && "undefined" != typeof dump ? function(e) {
                        dump(e)
                    } : function(e) {})
                }
                ENVIRONMENT_IS_WORKER && (Module.load = importScripts), void 0 === Module.setWindowTitle && (Module.setWindowTitle = function(e) {
                    document.title = e
                })
            }

            function globalEval(e) {
                eval.call(null, e)
            }
            for (var key in !Module.load && Module.read && (Module.load = function(e) {
                globalEval(Module.read(e))
            }), Module.print || (Module.print = function() {}), Module.printErr || (Module.printErr = Module.print), Module.arguments || (Module.arguments = []), Module.thisProgram || (Module.thisProgram = "./this.program"), Module.quit || (Module.quit = function(e, r) {
                throw r
            }), Module.print = Module.print, Module.printErr = Module.printErr, Module.preRun = [], Module.postRun = [], moduleOverrides) moduleOverrides.hasOwnProperty(key) && (Module[key] = moduleOverrides[key]);
            moduleOverrides = void 0;
            var Runtime = {
                setTempRet0: function(e) {
                    return tempRet0 = e, e
                },
                getTempRet0: function() {
                    return tempRet0
                },
                stackSave: function() {
                    return STACKTOP
                },
                stackRestore: function(e) {
                    STACKTOP = e
                },
                getNativeTypeSize: function(e) {
                    switch (e) {
                        case "i1":
                        case "i8":
                            return 1;
                        case "i16":
                            return 2;
                        case "i32":
                            return 4;
                        case "i64":
                            return 8;
                        case "float":
                            return 4;
                        case "double":
                            return 8;
                        default:
                            return "*" === e[e.length - 1] ? Runtime.QUANTUM_SIZE : "i" === e[0] ? parseInt(e.substr(1)) / 8 : 0
                    }
                },
                getNativeFieldSize: function(e) {
                    return Math.max(Runtime.getNativeTypeSize(e), Runtime.QUANTUM_SIZE)
                },
                STACK_ALIGN: 16,
                prepVararg: function(e, r) {
                    return "double" !== r && "i64" !== r || 7 & e && (e += 4), e
                },
                getAlignSize: function(e, r, t) {
                    return t || "i64" != e && "double" != e ? e ? Math.min(r || (e ? Runtime.getNativeFieldSize(e) : 0), Runtime.QUANTUM_SIZE) : Math.min(r, 8) : 8
                },
                dynCall: function(e, r, t) {
                    return t && t.length ? Module["dynCall_" + e].apply(null, [r].concat(t)) : Module["dynCall_" + e].call(null, r)
                },
                functionPointers: [],
                addFunction: function(e) {
                    for (var r = 0; r < Runtime.functionPointers.length; r++)
                        if (!Runtime.functionPointers[r]) return Runtime.functionPointers[r] = e, 2 * (1 + r);
                    throw "Finished up all reserved function pointers. Use a higher value for RESERVED_FUNCTION_POINTERS."
                },
                removeFunction: function(e) {
                    Runtime.functionPointers[(e - 2) / 2] = null
                },
                warnOnce: function(e) {
                    Runtime.warnOnce.shown || (Runtime.warnOnce.shown = {}), Runtime.warnOnce.shown[e] || (Runtime.warnOnce.shown[e] = 1, Module.printErr(e))
                },
                funcWrappers: {},
                getFuncWrapper: function(e, r) {
                    if (e) {
                        Runtime.funcWrappers[r] || (Runtime.funcWrappers[r] = {});
                        var t = Runtime.funcWrappers[r];
                        return t[e] || (1 === r.length ? t[e] = function() {
                            return Runtime.dynCall(r, e)
                        } : 2 === r.length ? t[e] = function(t) {
                            return Runtime.dynCall(r, e, [t])
                        } : t[e] = function() {
                            return Runtime.dynCall(r, e, Array.prototype.slice.call(arguments))
                        }), t[e]
                    }
                },
                getCompilerSetting: function(e) {
                    throw "You must build with -s RETAIN_COMPILER_SETTINGS=1 for Runtime.getCompilerSetting or emscripten_get_compiler_setting to work"
                },
                stackAlloc: function(e) {
                    var r = STACKTOP;
                    return STACKTOP = 15 + (STACKTOP = STACKTOP + e | 0) & -16, r
                },
                staticAlloc: function(e) {
                    var r = STATICTOP;
                    return STATICTOP = 15 + (STATICTOP = STATICTOP + e | 0) & -16, r
                },
                dynamicAlloc: function(e) {
                    var r = HEAP32[DYNAMICTOP_PTR >> 2],
                        t = -16 & (r + e + 15 | 0);
                    return HEAP32[DYNAMICTOP_PTR >> 2] = t, t >= TOTAL_MEMORY && !enlargeMemory() ? (HEAP32[DYNAMICTOP_PTR >> 2] = r, 0) : r
                },
                alignMemory: function(e, r) {
                    return Math.ceil(e / (r || 16)) * (r || 16)
                },
                makeBigInt: function(e, r, t) {
                    return t ? +(e >>> 0) + 4294967296 * +(r >>> 0) : +(e >>> 0) + 4294967296 * +(0 | r)
                },
                GLOBAL_BASE: 8,
                QUANTUM_SIZE: 4,
                __dummy__: 0
            };
            Module.Runtime = Runtime;
            var ABORT = 0,
                EXITSTATUS = 0,
                cwrap, ccall;

            function assert(e, r) {
                e || abort("Assertion failed: " + r)
            }

            function getCFunc(ident) {
                var func = Module["_" + ident];
                if (!func) try {
                    func = eval("_" + ident)
                } catch (e) {}
                return func
            }

            function setValue(e, r, t, n) {
                switch ("*" === (t = t || "i8").charAt(t.length - 1) && (t = "i32"), t) {
                    case "i1":
                    case "i8":
                        HEAP8[e >> 0] = r;
                        break;
                    case "i16":
                        HEAP16[e >> 1] = r;
                        break;
                    case "i32":
                        HEAP32[e >> 2] = r;
                        break;
                    case "i64":
                        tempI64 = [r >>> 0, (tempDouble = r, +Math_abs(tempDouble) >= 1 ? tempDouble > 0 ? (0 | Math_min(+Math_floor(tempDouble / 4294967296), 4294967295)) >>> 0 : ~~+Math_ceil((tempDouble - +(~~tempDouble >>> 0)) / 4294967296) >>> 0 : 0)], HEAP32[e >> 2] = tempI64[0], HEAP32[e + 4 >> 2] = tempI64[1];
                        break;
                    case "float":
                        HEAPF32[e >> 2] = r;
                        break;
                    case "double":
                        HEAPF64[e >> 3] = r;
                        break;
                    default:
                        abort("invalid type for setValue: " + t)
                }
            }

            function getValue(e, r, t) {
                switch ("*" === (r = r || "i8").charAt(r.length - 1) && (r = "i32"), r) {
                    case "i1":
                    case "i8":
                        return HEAP8[e >> 0];
                    case "i16":
                        return HEAP16[e >> 1];
                    case "i32":
                    case "i64":
                        return HEAP32[e >> 2];
                    case "float":
                        return HEAPF32[e >> 2];
                    case "double":
                        return HEAPF64[e >> 3];
                    default:
                        abort("invalid type for setValue: " + r)
                }
                return null
            }! function() {
                var JSfuncs = {
                        stackSave: function() {
                            Runtime.stackSave()
                        },
                        stackRestore: function() {
                            Runtime.stackRestore()
                        },
                        arrayToC: function(e) {
                            var r = Runtime.stackAlloc(e.length);
                            return writeArrayToMemory(e, r), r
                        },
                        stringToC: function(e) {
                            var r = 0;
                            if (null != e && 0 !== e) {
                                var t = 1 + (e.length << 2);
                                stringToUTF8(e, r = Runtime.stackAlloc(t), t)
                            }
                            return r
                        }
                    },
                    toC = {
                        string: JSfuncs.stringToC,
                        array: JSfuncs.arrayToC
                    };
                ccall = function(e, r, t, n, i) {
                    var o = getCFunc(e),
                        a = [],
                        u = 0;
                    if (n)
                        for (var f = 0; f < n.length; f++) {
                            var l = toC[t[f]];
                            l ? (0 === u && (u = Runtime.stackSave()), a[f] = l(n[f])) : a[f] = n[f]
                        }
                    var s = o.apply(null, a);
                    if ("string" === r && (s = Pointer_stringify(s)), 0 !== u) {
                        if (i && i.async) return void EmterpreterAsync.asyncFinalizers.push(function() {
                            Runtime.stackRestore(u)
                        });
                        Runtime.stackRestore(u)
                    }
                    return s
                };
                var sourceRegex = /^function\s*[a-zA-Z$_0-9]*\s*\(([^)]*)\)\s*{\s*([^*]*?)[\s;]*(?:return\s*(.*?)[;\s]*)?}$/;

                function parseJSFunc(e) {
                    var r = e.toString().match(sourceRegex).slice(1);
                    return {
                        arguments: r[0],
                        body: r[1],
                        returnValue: r[2]
                    }
                }
                var JSsource = null;

                function ensureJSsource() {
                    if (!JSsource)
                        for (var e in JSsource = {}, JSfuncs) JSfuncs.hasOwnProperty(e) && (JSsource[e] = parseJSFunc(JSfuncs[e]))
                }
                cwrap = function cwrap(ident, returnType, argTypes) {
                    argTypes = argTypes || [];
                    var cfunc = getCFunc(ident),
                        numericArgs = argTypes.every(function(e) {
                            return "number" === e
                        }),
                        numericRet = "string" !== returnType;
                    if (numericRet && numericArgs) return cfunc;
                    var argNames = argTypes.map(function(e, r) {
                            return "$" + r
                        }),
                        funcstr = "(function(" + argNames.join(",") + ") {",
                        nargs = argTypes.length;
                    if (!numericArgs) {
                        ensureJSsource(), funcstr += "var stack = " + JSsource.stackSave.body + ";";
                        for (var i = 0; i < nargs; i++) {
                            var arg = argNames[i],
                                type = argTypes[i];
                            if ("number" !== type) {
                                var convertCode = JSsource[type + "ToC"];
                                funcstr += "var " + convertCode.arguments + " = " + arg + ";", funcstr += convertCode.body + ";", funcstr += arg + "=(" + convertCode.returnValue + ");"
                            }
                        }
                    }
                    var cfuncname = parseJSFunc(function() {
                        return cfunc
                    }).returnValue;
                    if (funcstr += "var ret = " + cfuncname + "(" + argNames.join(",") + ");", !numericRet) {
                        var strgfy = parseJSFunc(function() {
                            return Pointer_stringify
                        }).returnValue;
                        funcstr += "ret = " + strgfy + "(ret);"
                    }
                    return numericArgs || (ensureJSsource(), funcstr += JSsource.stackRestore.body.replace("()", "(stack)") + ";"), funcstr += "return ret})", eval(funcstr)
                }
            }(), Module.ccall = ccall, Module.cwrap = cwrap, Module.setValue = setValue, Module.getValue = getValue;
            var ALLOC_NORMAL = 0,
                ALLOC_STACK = 1,
                ALLOC_STATIC = 2,
                ALLOC_DYNAMIC = 3,
                ALLOC_NONE = 4;

            function allocate(e, r, t, n) {
                var i, o;
                "number" == typeof e ? (i = !0, o = e) : (i = !1, o = e.length);
                var a, u = "string" == typeof r ? r : null;
                if (a = t == ALLOC_NONE ? n : ["function" == typeof _malloc ? _malloc : Runtime.staticAlloc, Runtime.stackAlloc, Runtime.staticAlloc, Runtime.dynamicAlloc][void 0 === t ? ALLOC_STATIC : t](Math.max(o, u ? 1 : r.length)), i) {
                    var f;
                    for (n = a, f = a + (-4 & o); n < f; n += 4) HEAP32[n >> 2] = 0;
                    for (f = a + o; n < f;) HEAP8[n++ >> 0] = 0;
                    return a
                }
                if ("i8" === u) return e.subarray || e.slice ? HEAPU8.set(e, a) : HEAPU8.set(new Uint8Array(e), a), a;
                for (var l, s, c, d = 0; d < o;) {
                    var _ = e[d];
                    "function" == typeof _ && (_ = Runtime.getFunctionIndex(_)), 0 !== (l = u || r[d]) ? ("i64" == l && (l = "i32"), setValue(a + d, _, l), c !== l && (s = Runtime.getNativeTypeSize(l), c = l), d += s) : d++
                }
                return a
            }

            function getMemory(e) {
                return staticSealed ? runtimeInitialized ? _malloc(e) : Runtime.dynamicAlloc(e) : Runtime.staticAlloc(e)
            }

            function Pointer_stringify(e, r) {
                if (0 === r || !e) return "";
                for (var t, n = 0, i = 0; n |= t = HEAPU8[e + i >> 0], (0 != t || r) && (i++, !r || i != r););
                r || (r = i);
                var o = "";
                if (n < 128) {
                    for (var a; r > 0;) a = String.fromCharCode.apply(String, HEAPU8.subarray(e, e + Math.min(r, 1024))), o = o ? o + a : a, e += 1024, r -= 1024;
                    return o
                }
                return Module.UTF8ToString(e)
            }

            function AsciiToString(e) {
                for (var r = "";;) {
                    var t = HEAP8[e++ >> 0];
                    if (!t) return r;
                    r += String.fromCharCode(t)
                }
            }

            function stringToAscii(e, r) {
                return writeAsciiToMemory(e, r, !1)
            }
            Module.ALLOC_NORMAL = ALLOC_NORMAL, Module.ALLOC_STACK = ALLOC_STACK, Module.ALLOC_STATIC = ALLOC_STATIC, Module.ALLOC_DYNAMIC = ALLOC_DYNAMIC, Module.ALLOC_NONE = ALLOC_NONE, Module.allocate = allocate, Module.getMemory = getMemory, Module.Pointer_stringify = Pointer_stringify, Module.AsciiToString = AsciiToString, Module.stringToAscii = stringToAscii;
            var UTF8Decoder = "undefined" != typeof TextDecoder ? new TextDecoder("utf8") : void 0;

            function UTF8ArrayToString(e, r) {
                for (var t = r; e[t];) ++t;
                if (t - r > 16 && e.subarray && UTF8Decoder) return UTF8Decoder.decode(e.subarray(r, t));
                for (var n, i, o, a, u, f = "";;) {
                    if (!(n = e[r++])) return f;
                    if (128 & n)
                        if (i = 63 & e[r++], 192 != (224 & n))
                            if (o = 63 & e[r++], 224 == (240 & n) ? n = (15 & n) << 12 | i << 6 | o : (a = 63 & e[r++], 240 == (248 & n) ? n = (7 & n) << 18 | i << 12 | o << 6 | a : (u = 63 & e[r++], n = 248 == (252 & n) ? (3 & n) << 24 | i << 18 | o << 12 | a << 6 | u : (1 & n) << 30 | i << 24 | o << 18 | a << 12 | u << 6 | 63 & e[r++])), n < 65536) f += String.fromCharCode(n);
                            else {
                                var l = n - 65536;
                                f += String.fromCharCode(55296 | l >> 10, 56320 | 1023 & l)
                            } else f += String.fromCharCode((31 & n) << 6 | i);
                    else f += String.fromCharCode(n)
                }
            }

            function UTF8ToString(e) {
                return UTF8ArrayToString(HEAPU8, e)
            }

            function stringToUTF8Array(e, r, t, n) {
                if (!(n > 0)) return 0;
                for (var i = t, o = t + n - 1, a = 0; a < e.length; ++a) {
                    var u = e.charCodeAt(a);
                    if (u >= 55296 && u <= 57343 && (u = 65536 + ((1023 & u) << 10) | 1023 & e.charCodeAt(++a)), u <= 127) {
                        if (t >= o) break;
                        r[t++] = u
                    } else if (u <= 2047) {
                        if (t + 1 >= o) break;
                        r[t++] = 192 | u >> 6, r[t++] = 128 | 63 & u
                    } else if (u <= 65535) {
                        if (t + 2 >= o) break;
                        r[t++] = 224 | u >> 12, r[t++] = 128 | u >> 6 & 63, r[t++] = 128 | 63 & u
                    } else if (u <= 2097151) {
                        if (t + 3 >= o) break;
                        r[t++] = 240 | u >> 18, r[t++] = 128 | u >> 12 & 63, r[t++] = 128 | u >> 6 & 63, r[t++] = 128 | 63 & u
                    } else if (u <= 67108863) {
                        if (t + 4 >= o) break;
                        r[t++] = 248 | u >> 24, r[t++] = 128 | u >> 18 & 63, r[t++] = 128 | u >> 12 & 63, r[t++] = 128 | u >> 6 & 63, r[t++] = 128 | 63 & u
                    } else {
                        if (t + 5 >= o) break;
                        r[t++] = 252 | u >> 30, r[t++] = 128 | u >> 24 & 63, r[t++] = 128 | u >> 18 & 63, r[t++] = 128 | u >> 12 & 63, r[t++] = 128 | u >> 6 & 63, r[t++] = 128 | 63 & u
                    }
                }
                return r[t] = 0, t - i
            }

            function stringToUTF8(e, r, t) {
                return stringToUTF8Array(e, HEAPU8, r, t)
            }

            function lengthBytesUTF8(e) {
                for (var r = 0, t = 0; t < e.length; ++t) {
                    var n = e.charCodeAt(t);
                    n >= 55296 && n <= 57343 && (n = 65536 + ((1023 & n) << 10) | 1023 & e.charCodeAt(++t)), n <= 127 ? ++r : r += n <= 2047 ? 2 : n <= 65535 ? 3 : n <= 2097151 ? 4 : n <= 67108863 ? 5 : 6
                }
                return r
            }
            Module.UTF8ArrayToString = UTF8ArrayToString, Module.UTF8ToString = UTF8ToString, Module.stringToUTF8Array = stringToUTF8Array, Module.stringToUTF8 = stringToUTF8, Module.lengthBytesUTF8 = lengthBytesUTF8;
            var UTF16Decoder = "undefined" != typeof TextDecoder ? new TextDecoder("utf-16le") : void 0;

            function demangle(e) {
                var r = Module.___cxa_demangle || Module.__cxa_demangle;
                if (r) {
                    try {
                        var t = e.substr(1),
                            n = lengthBytesUTF8(t) + 1,
                            i = _malloc(n);
                        stringToUTF8(t, i, n);
                        var o = _malloc(4),
                            a = r(i, 0, 0, o);
                        if (0 === getValue(o, "i32") && a) return Pointer_stringify(a)
                    } catch (u) {} finally {
                        i && _free(i), o && _free(o), a && _free(a)
                    }
                    return e
                }
                return Runtime.warnOnce("warning: build with  -s DEMANGLE_SUPPORT=1  to link in libcxxabi demangling"), e
            }

            function demangleAll(e) {
                return e.replace(/__Z[\w\d_]+/g, function(e) {
                    var r = demangle(e);
                    return e === r ? e : e + " [" + r + "]"
                })
            }

            function jsStackTrace() {
                var e = new Error;
                if (!e.stack) {
                    try {
                        throw new Error(0)
                    } catch (r) {
                        e = r
                    }
                    if (!e.stack) return "(no stack trace available)"
                }
                return e.stack.toString()
            }

            function stackTrace() {
                var e = jsStackTrace();
                return Module.extraStackTrace && (e += "\n" + Module.extraStackTrace()), demangleAll(e)
            }
            Module.stackTrace = stackTrace;
            var WASM_PAGE_SIZE = 65536,
                ASMJS_PAGE_SIZE = 16777216,
                MIN_TOTAL_MEMORY = 16777216,
                HEAP, buffer, HEAP8, HEAPU8, HEAP16, HEAPU16, HEAP32, HEAPU32, HEAPF32, HEAPF64, STATIC_BASE, STATICTOP, staticSealed, STACK_BASE, STACKTOP, STACK_MAX, DYNAMIC_BASE, DYNAMICTOP_PTR, byteLength;

            function alignUp(e, r) {
                return e % r > 0 && (e += r - e % r), e
            }

            function updateGlobalBuffer(e) {
                Module.buffer = buffer = e
            }

            function updateGlobalBufferViews() {
                Module.HEAP8 = HEAP8 = new Int8Array(buffer), Module.HEAP16 = HEAP16 = new Int16Array(buffer), Module.HEAP32 = HEAP32 = new Int32Array(buffer), Module.HEAPU8 = HEAPU8 = new Uint8Array(buffer), Module.HEAPU16 = HEAPU16 = new Uint16Array(buffer), Module.HEAPU32 = HEAPU32 = new Uint32Array(buffer), Module.HEAPF32 = HEAPF32 = new Float32Array(buffer), Module.HEAPF64 = HEAPF64 = new Float64Array(buffer)
            }

            function abortOnCannotGrowMemory() {
                abort("Cannot enlarge memory arrays. Either (1) compile with  -s TOTAL_MEMORY=X  with X higher than the current value " + TOTAL_MEMORY + ", (2) compile with  -s ALLOW_MEMORY_GROWTH=1  which allows increasing the size at runtime but prevents some optimizations, (3) set Module.TOTAL_MEMORY to a higher value before the program runs, or (4) if you want malloc to return NULL (0) instead of this abort, compile with  -s ABORTING_MALLOC=0 ")
            }

            function enlargeMemory() {
                var e = Module.usingWasm ? WASM_PAGE_SIZE : ASMJS_PAGE_SIZE,
                    r = 2147483648 - e;
                if (HEAP32[DYNAMICTOP_PTR >> 2] > r) return !1;
                var t = TOTAL_MEMORY;
                for (TOTAL_MEMORY = Math.max(TOTAL_MEMORY, MIN_TOTAL_MEMORY); TOTAL_MEMORY < HEAP32[DYNAMICTOP_PTR >> 2];) TOTAL_MEMORY = TOTAL_MEMORY <= 536870912 ? alignUp(2 * TOTAL_MEMORY, e) : Math.min(alignUp((3 * TOTAL_MEMORY + 2147483648) / 4, e), r);
                var n = Module.reallocBuffer(TOTAL_MEMORY);
                return n && n.byteLength == TOTAL_MEMORY ? (updateGlobalBuffer(n), updateGlobalBufferViews(), !0) : (TOTAL_MEMORY = t, !1)
            }
            STATIC_BASE = STATICTOP = STACK_BASE = STACKTOP = STACK_MAX = DYNAMIC_BASE = DYNAMICTOP_PTR = 0, staticSealed = !1, Module.reallocBuffer || (Module.reallocBuffer = function(e) {
                var r;
                try {
                    if (ArrayBuffer.transfer) r = ArrayBuffer.transfer(buffer, e);
                    else {
                        var t = HEAP8;
                        r = new ArrayBuffer(e), new Int8Array(r).set(t)
                    }
                } catch (n) {
                    return !1
                }
                return !!_emscripten_replace_memory(r) && r
            });
            try {
                byteLength = Function.prototype.call.bind(Object.getOwnPropertyDescriptor(ArrayBuffer.prototype, "byteLength").get), byteLength(new ArrayBuffer(4))
            } catch (e) {
                byteLength = function(e) {
                    return e.byteLength
                }
            }
            var TOTAL_STACK = Module.TOTAL_STACK || 5242880,
                TOTAL_MEMORY = Module.TOTAL_MEMORY || 67108864;

            function getTotalMemory() {
                return TOTAL_MEMORY
            }
            if (TOTAL_MEMORY < TOTAL_STACK && Module.printErr("TOTAL_MEMORY should be larger than TOTAL_STACK, was " + TOTAL_MEMORY + "! (TOTAL_STACK=" + TOTAL_STACK + ")"), buffer = Module.buffer ? Module.buffer : new ArrayBuffer(TOTAL_MEMORY), updateGlobalBufferViews(), HEAP32[0] = 1668509029, HEAP16[1] = 25459, 115 !== HEAPU8[2] || 99 !== HEAPU8[3]) throw "Runtime error: expected the system to be little-endian!";

            function callRuntimeCallbacks(e) {
                for (; e.length > 0;) {
                    var r = e.shift();
                    if ("function" != typeof r) {
                        var t = r.func;
                        "number" == typeof t ? void 0 === r.arg ? Module.dynCall_v(t) : Module.dynCall_vi(t, r.arg) : t(void 0 === r.arg ? null : r.arg)
                    } else r()
                }
            }
            Module.HEAP = HEAP, Module.buffer = buffer, Module.HEAP8 = HEAP8, Module.HEAP16 = HEAP16, Module.HEAP32 = HEAP32, Module.HEAPU8 = HEAPU8, Module.HEAPU16 = HEAPU16, Module.HEAPU32 = HEAPU32, Module.HEAPF32 = HEAPF32, Module.HEAPF64 = HEAPF64;
            var __ATPRERUN__ = [],
                __ATINIT__ = [],
                __ATMAIN__ = [],
                __ATEXIT__ = [],
                __ATPOSTRUN__ = [],
                runtimeInitialized = !1,
                runtimeExited = !1;

            function preRun() {
                if (Module.preRun)
                    for ("function" == typeof Module.preRun && (Module.preRun = [Module.preRun]); Module.preRun.length;) addOnPreRun(Module.preRun.shift());
                callRuntimeCallbacks(__ATPRERUN__)
            }

            function ensureInitRuntime() {
                runtimeInitialized || (runtimeInitialized = !0, callRuntimeCallbacks(__ATINIT__))
            }

            function preMain() {
                callRuntimeCallbacks(__ATMAIN__)
            }

            function exitRuntime() {
                callRuntimeCallbacks(__ATEXIT__), runtimeExited = !0
            }

            function postRun() {
                if (Module.postRun)
                    for ("function" == typeof Module.postRun && (Module.postRun = [Module.postRun]); Module.postRun.length;) addOnPostRun(Module.postRun.shift());
                callRuntimeCallbacks(__ATPOSTRUN__)
            }

            function addOnPreRun(e) {
                __ATPRERUN__.unshift(e)
            }

            function addOnInit(e) {
                __ATINIT__.unshift(e)
            }

            function addOnPreMain(e) {
                __ATMAIN__.unshift(e)
            }

            function addOnExit(e) {
                __ATEXIT__.unshift(e)
            }

            function addOnPostRun(e) {
                __ATPOSTRUN__.unshift(e)
            }

            function intArrayFromString(e, r, t) {
                var n = t > 0 ? t : lengthBytesUTF8(e) + 1,
                    i = new Array(n),
                    o = stringToUTF8Array(e, i, 0, i.length);
                return r && (i.length = o), i
            }

            function intArrayToString(e) {
                for (var r = [], t = 0; t < e.length; t++) {
                    var n = e[t];
                    n > 255 && (n &= 255), r.push(String.fromCharCode(n))
                }
                return r.join("")
            }

            function writeStringToMemory(e, r, t) {
                var n, i;
                Runtime.warnOnce("writeStringToMemory is deprecated and should not be called! Use stringToUTF8() instead!"), t && (i = r + lengthBytesUTF8(e), n = HEAP8[i]), stringToUTF8(e, r, 1 / 0), t && (HEAP8[i] = n)
            }

            function writeArrayToMemory(e, r) {
                HEAP8.set(e, r)
            }

            function writeAsciiToMemory(e, r, t) {
                for (var n = 0; n < e.length; ++n) HEAP8[r++ >> 0] = e.charCodeAt(n);
                t || (HEAP8[r >> 0] = 0)
            }
            Module.addOnPreRun = addOnPreRun, Module.addOnInit = addOnInit, Module.addOnPreMain = addOnPreMain, Module.addOnExit = addOnExit, Module.addOnPostRun = addOnPostRun, Module.intArrayFromString = intArrayFromString, Module.intArrayToString = intArrayToString, Module.writeStringToMemory = writeStringToMemory, Module.writeArrayToMemory = writeArrayToMemory, Module.writeAsciiToMemory = writeAsciiToMemory, Math.imul && -5 === Math.imul(4294967295, 5) || (Math.imul = function(e, r) {
                var t = 65535 & e,
                    n = 65535 & r;
                return t * n + ((e >>> 16) * n + t * (r >>> 16) << 16) | 0
            }), Math.imul = Math.imul, Math.clz32 || (Math.clz32 = function(e) {
                e >>>= 0;
                for (var r = 0; r < 32; r++)
                    if (e & 1 << 31 - r) return r;
                return 32
            }), Math.clz32 = Math.clz32, Math.trunc || (Math.trunc = function(e) {
                return e < 0 ? Math.ceil(e) : Math.floor(e)
            }), Math.trunc = Math.trunc;
            var Math_abs = Math.abs,
                Math_cos = Math.cos,
                Math_sin = Math.sin,
                Math_tan = Math.tan,
                Math_acos = Math.acos,
                Math_asin = Math.asin,
                Math_atan = Math.atan,
                Math_atan2 = Math.atan2,
                Math_exp = Math.exp,
                Math_log = Math.log,
                Math_sqrt = Math.sqrt,
                Math_ceil = Math.ceil,
                Math_floor = Math.floor,
                Math_pow = Math.pow,
                Math_imul = Math.imul,
                Math_fround = Math.fround,
                Math_round = Math.round,
                Math_min = Math.min,
                Math_clz32 = Math.clz32,
                Math_trunc = Math.trunc,
                runDependencies = 0,
                runDependencyWatcher = null,
                dependenciesFulfilled = null;

            function getUniqueRunDependency(e) {
                return e
            }

            function addRunDependency(e) {
                runDependencies++, Module.monitorRunDependencies && Module.monitorRunDependencies(runDependencies)
            }

            function removeRunDependency(e) {
                if (runDependencies--, Module.monitorRunDependencies && Module.monitorRunDependencies(runDependencies), 0 == runDependencies && (null !== runDependencyWatcher && (clearInterval(runDependencyWatcher), runDependencyWatcher = null), dependenciesFulfilled)) {
                    var r = dependenciesFulfilled;
                    dependenciesFulfilled = null, r()
                }
            }
            Module.addRunDependency = addRunDependency, Module.removeRunDependency = removeRunDependency, Module.preloadedImages = {}, Module.preloadedAudios = {};
            var ASM_CONSTS = [];
            STATIC_BASE = Runtime.GLOBAL_BASE, STATICTOP = STATIC_BASE + 14144, __ATINIT__.push(), allocate([91, 9, 0, 0, 200, 13, 0, 0, 56, 15, 0, 0, 55, 18, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 3, 0, 0, 0, 7, 0, 0, 0, 15, 0, 0, 0, 31, 0, 0, 0, 63, 0, 0, 0, 127, 0, 0, 0, 255, 0, 0, 0, 255, 1, 0, 0, 255, 3, 0, 0, 255, 7, 0, 0, 255, 15, 0, 0, 255, 31, 0, 0, 255, 63, 0, 0, 255, 127, 0, 0, 255, 255, 0, 0, 255, 255, 1, 0, 255, 255, 3, 0, 255, 255, 7, 0, 255, 255, 15, 0, 255, 255, 31, 0, 255, 255, 63, 0, 255, 255, 127, 0, 255, 255, 255, 0, 124, 0, 0, 0, 128, 0, 0, 0, 132, 0, 0, 0, 136, 0, 0, 0, 140, 0, 0, 0, 144, 0, 0, 0, 148, 0, 0, 0, 48, 82, 225, 13, 134, 24, 179, 3, 203, 172, 95, 119, 106, 98, 136, 28, 85, 92, 56, 104, 40, 184, 179, 20, 248, 254, 133, 74, 75, 184, 221, 73, 151, 243, 252, 100, 137, 2, 85, 92, 0, 0, 41, 74, 218, 193, 126, 13, 171, 183, 64, 89, 125, 87, 146, 84, 114, 202, 25, 78, 105, 140, 211, 56, 101, 238, 1, 12, 95, 117, 161, 50, 82, 246, 55, 84, 50, 44, 187, 90, 177, 87, 170, 15, 231, 51, 245, 115, 218, 238, 95, 104, 226, 204, 99, 117, 131, 14, 153, 110, 237, 167, 48, 71, 198, 217, 192, 79, 60, 21, 107, 73, 250, 3, 20, 79, 12, 251, 26, 84, 50, 11, 153, 115, 28, 203, 215, 38, 6, 55, 204, 111, 216, 119, 187, 44, 42, 47, 118, 117, 221, 204, 37, 100, 97, 84, 179, 36, 21, 135, 125, 10, 168, 20, 4, 34, 103, 191, 30, 20, 131, 21, 180, 86, 227, 2, 229, 115, 111, 177, 202, 68, 66, 77, 38, 40, 251, 174, 186, 115, 237, 235, 80, 10, 251, 182, 106, 29, 11, 212, 58, 13, 104, 59, 219, 53, 131, 30, 8, 43, 149, 107, 206, 119, 240, 229, 129, 81, 188, 59, 133, 120, 148, 148, 159, 0, 60, 237, 229, 39, 1, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0, 3, 0, 0, 0, 4, 0, 0, 0, 140, 1, 0, 0, 144, 1, 0, 0, 236, 34, 0, 0, 240, 34, 0, 0, 245, 34, 0, 0, 251, 34, 0, 0, 168, 1, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 56, 51, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 51, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 2, 0, 0, 0, 64, 51, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 255, 255, 255, 255, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 24, 3, 0, 0, 138, 11, 140, 11, 142, 11, 146, 11, 154, 11, 170, 11, 202, 11, 10, 12, 140, 12, 140, 13, 140, 15, 140, 19, 24, 1, 0, 1, 0, 1, 0, 1, 40, 0, 0, 0, 4, 0, 8, 0, 12, 0, 128, 0, 132, 0, 136, 0, 140, 0, 0, 1, 4, 1, 8, 1, 12, 1, 128, 1, 132, 1, 136, 1, 140, 1, 4, 0, 5, 0, 6, 0, 7, 0, 8, 0, 9, 0, 10, 0, 11, 0, 12, 0, 13, 0, 14, 0, 15, 0, 16, 0, 17, 0, 18, 0, 19, 0, 20, 0, 21, 0, 22, 0, 23, 0, 24, 0, 25, 0, 26, 0, 27, 0, 28, 0, 29, 0, 30, 0, 31, 0, 32, 0, 33, 0, 34, 0, 35, 0, 36, 0, 37, 0, 38, 0, 39, 0, 40, 0, 41, 0, 42, 0, 43, 0, 44, 0, 45, 0, 46, 0, 47, 0, 48, 0, 49, 0, 50, 0, 51, 0, 52, 0, 53, 0, 54, 0, 55, 0, 56, 0, 57, 0, 58, 0, 60, 0, 62, 0, 64, 0, 66, 0, 68, 0, 70, 0, 72, 0, 74, 0, 76, 0, 78, 0, 80, 0, 82, 0, 84, 0, 86, 0, 88, 0, 90, 0, 92, 0, 94, 0, 96, 0, 98, 0, 100, 0, 102, 0, 104, 0, 106, 0, 108, 0, 110, 0, 112, 0, 114, 0, 116, 0, 119, 0, 122, 0, 125, 0, 128, 0, 131, 0, 134, 0, 137, 0, 140, 0, 143, 0, 146, 0, 149, 0, 152, 0, 155, 0, 158, 0, 161, 0, 164, 0, 167, 0, 170, 0, 173, 0, 177, 0, 181, 0, 185, 0, 189, 0, 193, 0, 197, 0, 201, 0, 205, 0, 209, 0, 213, 0, 217, 0, 221, 0, 225, 0, 229, 0, 234, 0, 239, 0, 245, 0, 249, 0, 254, 0, 3, 1, 8, 1, 13, 1, 18, 1, 23, 1, 28, 1, 76, 105, 98, 114, 97, 114, 121, 32, 118, 101, 114, 115, 105, 111, 110, 32, 109, 105, 115, 109, 97, 116, 99, 104, 33, 10, 0, 85, 110, 97, 98, 108, 101, 32, 116, 111, 32, 115, 101, 116, 32, 118, 105, 100, 101, 111, 32, 109, 111, 100, 101, 32, 40, 51, 50, 98, 112, 112, 32, 37, 100, 120, 37, 100, 41, 33, 10, 0, 85, 110, 97, 98, 108, 101, 32, 116, 111, 32, 99, 114, 101, 97, 116, 101, 32, 37, 100, 120, 37, 100, 32, 82, 71, 66, 65, 32, 115, 117, 114, 102, 97, 99, 101, 33, 10, 0, 69, 114, 114, 111, 114, 32, 100, 101, 99, 111, 100, 105, 110, 103, 32, 105, 109, 97, 103, 101, 32, 40, 37, 100, 41, 10, 0, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 127, 0, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 0, 255, 254, 253, 252, 251, 250, 249, 248, 247, 246, 245, 244, 243, 242, 241, 240, 239, 238, 237, 236, 235, 234, 233, 232, 231, 230, 229, 228, 227, 226, 225, 224, 223, 222, 221, 220, 219, 218, 217, 216, 215, 214, 213, 212, 211, 210, 209, 208, 207, 206, 205, 204, 203, 202, 201, 200, 199, 198, 197, 196, 195, 194, 193, 192, 191, 190, 189, 188, 187, 186, 185, 184, 183, 182, 181, 180, 179, 178, 177, 176, 175, 174, 173, 172, 171, 170, 169, 168, 167, 166, 165, 164, 163, 162, 161, 160, 159, 158, 157, 156, 155, 154, 153, 152, 151, 150, 149, 148, 147, 146, 145, 144, 143, 142, 141, 140, 139, 138, 137, 136, 135, 134, 133, 132, 131, 130, 129, 128, 127, 126, 125, 124, 123, 122, 121, 120, 119, 118, 117, 116, 115, 114, 113, 112, 111, 110, 109, 108, 107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 87, 86, 85, 84, 83, 82, 81, 80, 79, 78, 77, 76, 75, 74, 73, 72, 71, 70, 69, 68, 67, 66, 65, 64, 63, 62, 61, 60, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255, 7, 6, 6, 5, 5, 5, 5, 4, 4, 4, 4, 4, 4, 4, 4, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 127, 127, 191, 127, 159, 191, 223, 127, 143, 159, 175, 191, 207, 223, 239, 127, 135, 143, 151, 159, 167, 175, 183, 191, 199, 207, 215, 223, 231, 239, 247, 127, 131, 135, 139, 143, 147, 151, 155, 159, 163, 167, 171, 175, 179, 183, 187, 191, 195, 199, 203, 207, 211, 215, 219, 223, 227, 231, 235, 239, 243, 247, 251, 127, 129, 131, 133, 135, 137, 139, 141, 143, 145, 147, 149, 151, 153, 155, 157, 159, 161, 163, 165, 167, 169, 171, 173, 175, 177, 179, 181, 183, 185, 187, 189, 191, 193, 195, 197, 199, 201, 203, 205, 207, 209, 211, 213, 215, 217, 219, 221, 223, 225, 227, 229, 231, 233, 235, 237, 239, 241, 243, 245, 247, 249, 251, 253, 127, 24, 7, 23, 25, 40, 6, 39, 41, 22, 26, 38, 42, 56, 5, 55, 57, 21, 27, 54, 58, 37, 43, 72, 4, 71, 73, 20, 28, 53, 59, 70, 74, 36, 44, 88, 69, 75, 52, 60, 3, 87, 89, 19, 29, 86, 90, 35, 45, 68, 76, 85, 91, 51, 61, 104, 2, 103, 105, 18, 30, 102, 106, 34, 46, 84, 92, 67, 77, 101, 107, 50, 62, 120, 1, 119, 121, 83, 93, 17, 31, 100, 108, 66, 78, 118, 122, 33, 47, 117, 123, 49, 63, 99, 109, 82, 94, 0, 116, 124, 65, 79, 16, 32, 98, 110, 48, 115, 125, 81, 95, 64, 114, 126, 97, 111, 80, 113, 127, 96, 112, 17, 18, 0, 1, 2, 3, 4, 5, 16, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 2, 3, 7, 3, 3, 11, 3, 4, 3, 4, 4, 2, 2, 4, 4, 4, 2, 1, 1, 8, 7, 6, 4, 4, 2, 2, 2, 1, 1, 1, 1, 0, 2, 8, 67, 111, 117, 108, 100, 32, 110, 111, 116, 32, 100, 101, 99, 111, 100, 101, 32, 97, 108, 112, 104, 97, 32, 100, 97, 116, 97, 46, 0, 70, 114, 97, 109, 101, 32, 115, 101, 116, 117, 112, 32, 102, 97, 105, 108, 101, 100, 0, 116, 104, 114, 101, 97, 100, 32, 105, 110, 105, 116, 105, 97, 108, 105, 122, 97, 116, 105, 111, 110, 32, 102, 97, 105, 108, 101, 100, 46, 0, 110, 111, 32, 109, 101, 109, 111, 114, 121, 32, 100, 117, 114, 105, 110, 103, 32, 102, 114, 97, 109, 101, 32, 105, 110, 105, 116, 105, 97, 108, 105, 122, 97, 116, 105, 111, 110, 46, 0, 82, 73, 70, 70, 0, 87, 69, 66, 80, 0, 86, 80, 56, 88, 0, 65, 76, 80, 72, 0, 86, 80, 56, 32, 0, 86, 80, 56, 76, 0, 231, 120, 48, 89, 115, 113, 120, 152, 112, 152, 179, 64, 126, 170, 118, 46, 70, 95, 175, 69, 143, 80, 85, 82, 72, 155, 103, 56, 58, 10, 171, 218, 189, 17, 13, 152, 114, 26, 17, 163, 44, 195, 21, 10, 173, 121, 24, 80, 195, 26, 62, 44, 64, 85, 144, 71, 10, 38, 171, 213, 144, 34, 26, 170, 46, 55, 19, 136, 160, 33, 206, 71, 63, 20, 8, 114, 114, 208, 12, 9, 226, 81, 40, 11, 96, 182, 84, 29, 16, 36, 134, 183, 89, 137, 98, 101, 106, 165, 148, 72, 187, 100, 130, 157, 111, 32, 75, 80, 66, 102, 167, 99, 74, 62, 40, 234, 128, 41, 53, 9, 178, 241, 141, 26, 8, 107, 74, 43, 26, 146, 73, 166, 49, 23, 157, 65, 38, 105, 160, 51, 52, 31, 115, 128, 104, 79, 12, 27, 217, 255, 87, 17, 7, 87, 68, 71, 44, 114, 51, 15, 186, 23, 47, 41, 14, 110, 182, 183, 21, 17, 194, 66, 45, 25, 102, 197, 189, 23, 18, 22, 88, 88, 147, 150, 42, 46, 45, 196, 205, 43, 97, 183, 117, 85, 38, 35, 179, 61, 39, 53, 200, 87, 26, 21, 43, 232, 171, 56, 34, 51, 104, 114, 102, 29, 93, 77, 39, 28, 85, 171, 58, 165, 90, 98, 64, 34, 22, 116, 206, 23, 34, 43, 166, 73, 107, 54, 32, 26, 51, 1, 81, 43, 31, 68, 25, 106, 22, 64, 171, 36, 225, 114, 34, 19, 21, 102, 132, 188, 16, 76, 124, 62, 18, 78, 95, 85, 57, 50, 48, 51, 193, 101, 35, 159, 215, 111, 89, 46, 111, 60, 148, 31, 172, 219, 228, 21, 18, 111, 112, 113, 77, 85, 179, 255, 38, 120, 114, 40, 42, 1, 196, 245, 209, 10, 25, 109, 88, 43, 29, 140, 166, 213, 37, 43, 154, 61, 63, 30, 155, 67, 45, 68, 1, 209, 100, 80, 8, 43, 154, 1, 51, 26, 71, 142, 78, 78, 16, 255, 128, 34, 197, 171, 41, 40, 5, 102, 211, 183, 4, 1, 221, 51, 50, 17, 168, 209, 192, 23, 25, 82, 138, 31, 36, 171, 27, 166, 38, 44, 229, 67, 87, 58, 169, 82, 115, 26, 59, 179, 63, 59, 90, 180, 59, 166, 93, 73, 154, 40, 40, 21, 116, 143, 209, 34, 39, 175, 47, 15, 16, 183, 34, 223, 49, 45, 183, 46, 17, 33, 183, 6, 98, 15, 32, 183, 57, 46, 22, 24, 128, 1, 54, 17, 37, 65, 32, 73, 115, 28, 128, 23, 128, 205, 40, 3, 9, 115, 51, 192, 18, 6, 223, 87, 37, 9, 115, 59, 77, 64, 21, 47, 104, 55, 44, 218, 9, 54, 53, 130, 226, 64, 90, 70, 205, 40, 41, 23, 26, 57, 54, 57, 112, 184, 5, 41, 38, 166, 213, 30, 34, 26, 133, 152, 116, 10, 32, 134, 39, 19, 53, 221, 26, 114, 32, 73, 255, 31, 9, 65, 234, 2, 15, 1, 118, 73, 75, 32, 12, 51, 192, 255, 160, 43, 51, 88, 31, 35, 67, 102, 85, 55, 186, 85, 56, 21, 23, 111, 59, 205, 45, 37, 192, 55, 38, 70, 124, 73, 102, 1, 34, 98, 125, 98, 42, 88, 104, 85, 117, 175, 82, 95, 84, 53, 89, 128, 100, 113, 101, 45, 75, 79, 123, 47, 51, 128, 81, 171, 1, 57, 17, 5, 71, 102, 57, 53, 41, 49, 38, 33, 13, 121, 57, 73, 26, 1, 85, 41, 10, 67, 138, 77, 110, 90, 47, 114, 115, 21, 2, 10, 102, 255, 166, 23, 6, 101, 29, 16, 10, 85, 128, 101, 196, 26, 57, 18, 10, 102, 102, 213, 34, 20, 43, 117, 20, 15, 36, 163, 128, 68, 1, 26, 102, 61, 71, 37, 34, 53, 31, 243, 192, 69, 60, 71, 38, 73, 119, 28, 222, 37, 68, 45, 128, 34, 1, 47, 11, 245, 171, 62, 17, 19, 70, 146, 85, 55, 62, 70, 37, 43, 37, 154, 100, 163, 85, 160, 1, 63, 9, 92, 136, 28, 64, 32, 201, 85, 75, 15, 9, 9, 64, 255, 184, 119, 16, 86, 6, 28, 5, 64, 255, 25, 248, 1, 56, 8, 17, 132, 137, 255, 55, 116, 128, 58, 15, 20, 82, 135, 57, 26, 121, 40, 164, 50, 31, 137, 154, 133, 25, 35, 218, 51, 103, 44, 131, 131, 123, 31, 6, 158, 86, 40, 64, 135, 148, 224, 45, 183, 128, 22, 26, 17, 131, 240, 154, 14, 1, 209, 45, 16, 21, 91, 64, 222, 7, 1, 197, 56, 21, 39, 155, 60, 138, 23, 102, 213, 83, 12, 13, 54, 192, 255, 68, 47, 28, 85, 26, 85, 85, 128, 128, 32, 146, 171, 18, 11, 7, 63, 144, 171, 4, 4, 246, 35, 27, 10, 146, 174, 171, 12, 26, 128, 190, 80, 35, 99, 180, 80, 126, 54, 45, 85, 126, 47, 87, 176, 51, 41, 20, 32, 101, 75, 128, 139, 118, 146, 116, 128, 85, 56, 41, 15, 176, 236, 85, 37, 9, 62, 71, 30, 17, 119, 118, 255, 17, 18, 138, 101, 38, 60, 138, 55, 70, 43, 26, 142, 146, 36, 19, 30, 171, 255, 97, 27, 20, 138, 45, 61, 62, 219, 1, 81, 188, 64, 32, 41, 20, 117, 151, 142, 20, 21, 163, 112, 19, 12, 61, 195, 128, 48, 4, 24, 0, 1, 255, 2, 254, 3, 4, 6, 253, 5, 252, 251, 250, 7, 249, 8, 248, 247, 79, 75, 0, 110, 117, 108, 108, 32, 86, 80, 56, 73, 111, 32, 112, 97, 115, 115, 101, 100, 32, 116, 111, 32, 86, 80, 56, 71, 101, 116, 72, 101, 97, 100, 101, 114, 115, 40, 41, 0, 84, 114, 117, 110, 99, 97, 116, 101, 100, 32, 104, 101, 97, 100, 101, 114, 46, 0, 73, 110, 99, 111, 114, 114, 101, 99, 116, 32, 107, 101, 121, 102, 114, 97, 109, 101, 32, 112, 97, 114, 97, 109, 101, 116, 101, 114, 115, 46, 0, 70, 114, 97, 109, 101, 32, 110, 111, 116, 32, 100, 105, 115, 112, 108, 97, 121, 97, 98, 108, 101, 46, 0, 99, 97, 110, 110, 111, 116, 32, 112, 97, 114, 115, 101, 32, 112, 105, 99, 116, 117, 114, 101, 32, 104, 101, 97, 100, 101, 114, 0, 66, 97, 100, 32, 99, 111, 100, 101, 32, 119, 111, 114, 100, 0, 98, 97, 100, 32, 112, 97, 114, 116, 105, 116, 105, 111, 110, 32, 108, 101, 110, 103, 116, 104, 0, 99, 97, 110, 110, 111, 116, 32, 112, 97, 114, 115, 101, 32, 115, 101, 103, 109, 101, 110, 116, 32, 104, 101, 97, 100, 101, 114, 0, 99, 97, 110, 110, 111, 116, 32, 112, 97, 114, 115, 101, 32, 102, 105, 108, 116, 101, 114, 32, 104, 101, 97, 100, 101, 114, 0, 99, 97, 110, 110, 111, 116, 32, 112, 97, 114, 115, 101, 32, 112, 97, 114, 116, 105, 116, 105, 111, 110, 115, 0, 78, 111, 116, 32, 97, 32, 107, 101, 121, 32, 102, 114, 97, 109, 101, 46, 0, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 176, 246, 255, 255, 255, 255, 255, 255, 255, 255, 255, 223, 241, 252, 255, 255, 255, 255, 255, 255, 255, 255, 249, 253, 253, 255, 255, 255, 255, 255, 255, 255, 255, 255, 244, 252, 255, 255, 255, 255, 255, 255, 255, 255, 234, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 253, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 246, 254, 255, 255, 255, 255, 255, 255, 255, 255, 239, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 248, 254, 255, 255, 255, 255, 255, 255, 255, 255, 251, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 251, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 253, 255, 254, 255, 255, 255, 255, 255, 255, 250, 255, 254, 255, 254, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 217, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 225, 252, 241, 253, 255, 255, 254, 255, 255, 255, 255, 234, 250, 241, 250, 253, 255, 253, 254, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 223, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 238, 253, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 248, 254, 255, 255, 255, 255, 255, 255, 255, 255, 249, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 253, 255, 255, 255, 255, 255, 255, 255, 255, 255, 247, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 252, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 253, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 253, 255, 255, 255, 255, 255, 255, 255, 255, 250, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 186, 251, 250, 255, 255, 255, 255, 255, 255, 255, 255, 234, 251, 244, 254, 255, 255, 255, 255, 255, 255, 255, 251, 251, 243, 253, 254, 255, 254, 255, 255, 255, 255, 255, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 236, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 251, 253, 253, 254, 254, 255, 255, 255, 255, 255, 255, 255, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 254, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 248, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 250, 254, 252, 254, 255, 255, 255, 255, 255, 255, 255, 248, 254, 249, 253, 255, 255, 255, 255, 255, 255, 255, 255, 253, 253, 255, 255, 255, 255, 255, 255, 255, 255, 246, 253, 253, 255, 255, 255, 255, 255, 255, 255, 255, 252, 254, 251, 254, 254, 255, 255, 255, 255, 255, 255, 255, 254, 252, 255, 255, 255, 255, 255, 255, 255, 255, 248, 254, 253, 255, 255, 255, 255, 255, 255, 255, 255, 253, 255, 254, 254, 255, 255, 255, 255, 255, 255, 255, 255, 251, 254, 255, 255, 255, 255, 255, 255, 255, 255, 245, 251, 254, 255, 255, 255, 255, 255, 255, 255, 255, 253, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 251, 253, 255, 255, 255, 255, 255, 255, 255, 255, 252, 253, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 252, 255, 255, 255, 255, 255, 255, 255, 255, 255, 249, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 253, 255, 255, 255, 255, 255, 255, 255, 255, 250, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 254, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 253, 136, 254, 255, 228, 219, 128, 128, 128, 128, 128, 189, 129, 242, 255, 227, 213, 255, 219, 128, 128, 128, 106, 126, 227, 252, 214, 209, 255, 255, 128, 128, 128, 1, 98, 248, 255, 236, 226, 255, 255, 128, 128, 128, 181, 133, 238, 254, 221, 234, 255, 154, 128, 128, 128, 78, 134, 202, 247, 198, 180, 255, 219, 128, 128, 128, 1, 185, 249, 255, 243, 255, 128, 128, 128, 128, 128, 184, 150, 247, 255, 236, 224, 128, 128, 128, 128, 128, 77, 110, 216, 255, 236, 230, 128, 128, 128, 128, 128, 1, 101, 251, 255, 241, 255, 128, 128, 128, 128, 128, 170, 139, 241, 252, 236, 209, 255, 255, 128, 128, 128, 37, 116, 196, 243, 228, 255, 255, 255, 128, 128, 128, 1, 204, 254, 255, 245, 255, 128, 128, 128, 128, 128, 207, 160, 250, 255, 238, 128, 128, 128, 128, 128, 128, 102, 103, 231, 255, 211, 171, 128, 128, 128, 128, 128, 1, 152, 252, 255, 240, 255, 128, 128, 128, 128, 128, 177, 135, 243, 255, 234, 225, 128, 128, 128, 128, 128, 80, 129, 211, 255, 194, 224, 128, 128, 128, 128, 128, 1, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 246, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 255, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 198, 35, 237, 223, 193, 187, 162, 160, 145, 155, 62, 131, 45, 198, 221, 172, 176, 220, 157, 252, 221, 1, 68, 47, 146, 208, 149, 167, 221, 162, 255, 223, 128, 1, 149, 241, 255, 221, 224, 255, 255, 128, 128, 128, 184, 141, 234, 253, 222, 220, 255, 199, 128, 128, 128, 81, 99, 181, 242, 176, 190, 249, 202, 255, 255, 128, 1, 129, 232, 253, 214, 197, 242, 196, 255, 255, 128, 99, 121, 210, 250, 201, 198, 255, 202, 128, 128, 128, 23, 91, 163, 242, 170, 187, 247, 210, 255, 255, 128, 1, 200, 246, 255, 234, 255, 128, 128, 128, 128, 128, 109, 178, 241, 255, 231, 245, 255, 255, 128, 128, 128, 44, 130, 201, 253, 205, 192, 255, 255, 128, 128, 128, 1, 132, 239, 251, 219, 209, 255, 165, 128, 128, 128, 94, 136, 225, 251, 218, 190, 255, 255, 128, 128, 128, 22, 100, 174, 245, 186, 161, 255, 199, 128, 128, 128, 1, 182, 249, 255, 232, 235, 128, 128, 128, 128, 128, 124, 143, 241, 255, 227, 234, 128, 128, 128, 128, 128, 35, 77, 181, 251, 193, 211, 255, 205, 128, 128, 128, 1, 157, 247, 255, 236, 231, 255, 255, 128, 128, 128, 121, 141, 235, 255, 225, 227, 255, 255, 128, 128, 128, 45, 99, 188, 251, 195, 217, 255, 224, 128, 128, 128, 1, 1, 251, 255, 213, 255, 128, 128, 128, 128, 128, 203, 1, 248, 255, 255, 128, 128, 128, 128, 128, 128, 137, 1, 177, 255, 224, 255, 128, 128, 128, 128, 128, 253, 9, 248, 251, 207, 208, 255, 192, 128, 128, 128, 175, 13, 224, 243, 193, 185, 249, 198, 255, 255, 128, 73, 17, 171, 221, 161, 179, 236, 167, 255, 234, 128, 1, 95, 247, 253, 212, 183, 255, 255, 128, 128, 128, 239, 90, 244, 250, 211, 209, 255, 255, 128, 128, 128, 155, 77, 195, 248, 188, 195, 255, 255, 128, 128, 128, 1, 24, 239, 251, 218, 219, 255, 205, 128, 128, 128, 201, 51, 219, 255, 196, 186, 128, 128, 128, 128, 128, 69, 46, 190, 239, 201, 218, 255, 228, 128, 128, 128, 1, 191, 251, 255, 255, 128, 128, 128, 128, 128, 128, 223, 165, 249, 255, 213, 255, 128, 128, 128, 128, 128, 141, 124, 248, 255, 255, 128, 128, 128, 128, 128, 128, 1, 16, 248, 255, 255, 128, 128, 128, 128, 128, 128, 190, 36, 230, 255, 236, 255, 128, 128, 128, 128, 128, 149, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 1, 226, 255, 128, 128, 128, 128, 128, 128, 128, 128, 247, 192, 255, 128, 128, 128, 128, 128, 128, 128, 128, 240, 128, 255, 128, 128, 128, 128, 128, 128, 128, 128, 1, 134, 252, 255, 255, 128, 128, 128, 128, 128, 128, 213, 62, 250, 255, 255, 128, 128, 128, 128, 128, 128, 55, 93, 255, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 128, 202, 24, 213, 235, 186, 191, 220, 160, 240, 175, 255, 126, 38, 182, 232, 169, 184, 228, 174, 255, 187, 128, 61, 46, 138, 219, 151, 178, 240, 170, 255, 216, 128, 1, 112, 230, 250, 199, 191, 247, 159, 255, 255, 128, 166, 109, 228, 252, 211, 215, 255, 174, 128, 128, 128, 39, 77, 162, 232, 172, 180, 245, 178, 255, 255, 128, 1, 52, 220, 246, 198, 199, 249, 220, 255, 255, 128, 124, 74, 191, 243, 183, 193, 250, 221, 255, 255, 128, 24, 71, 130, 219, 154, 170, 243, 182, 255, 255, 128, 1, 182, 225, 249, 219, 240, 255, 224, 128, 128, 128, 149, 150, 226, 252, 216, 205, 255, 171, 128, 128, 128, 28, 108, 170, 242, 183, 194, 254, 223, 255, 255, 128, 1, 81, 230, 252, 204, 203, 255, 192, 128, 128, 128, 123, 102, 209, 247, 188, 196, 255, 233, 128, 128, 128, 20, 95, 153, 243, 164, 173, 255, 203, 128, 128, 128, 1, 222, 248, 255, 216, 213, 128, 128, 128, 128, 128, 168, 175, 246, 252, 235, 205, 255, 255, 128, 128, 128, 47, 116, 215, 255, 211, 212, 255, 255, 128, 128, 128, 1, 121, 236, 253, 212, 214, 255, 255, 128, 128, 128, 141, 84, 213, 252, 201, 202, 255, 219, 128, 128, 128, 42, 80, 160, 240, 162, 185, 255, 205, 128, 128, 128, 1, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 244, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 238, 1, 255, 128, 128, 128, 128, 128, 128, 128, 128, 4, 5, 6, 7, 8, 9, 10, 10, 11, 12, 13, 14, 15, 16, 17, 17, 18, 19, 20, 20, 21, 21, 22, 22, 23, 23, 24, 25, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 91, 93, 95, 96, 98, 100, 101, 102, 104, 106, 108, 110, 112, 114, 116, 118, 122, 124, 126, 128, 130, 132, 134, 136, 138, 140, 143, 145, 148, 151, 154, 157, 0, 1, 4, 8, 5, 2, 3, 6, 9, 12, 13, 10, 7, 11, 14, 15, 173, 148, 140, 0, 176, 155, 140, 135, 0, 180, 157, 141, 134, 130, 0, 254, 254, 243, 230, 196, 177, 153, 140, 133, 130, 129, 0, 78, 85, 76, 76, 32, 86, 80, 56, 73, 111, 32, 112, 97, 114, 97, 109, 101, 116, 101, 114, 32, 105, 110, 32, 86, 80, 56, 68, 101, 99, 111, 100, 101, 40, 41, 46, 0, 80, 114, 101, 109, 97, 116, 117, 114, 101, 32, 101, 110, 100, 45, 111, 102, 45, 112, 97, 114, 116, 105, 116, 105, 111, 110, 48, 32, 101, 110, 99, 111, 117, 110, 116, 101, 114, 101, 100, 46, 0, 80, 114, 101, 109, 97, 116, 117, 114, 101, 32, 101, 110, 100, 45, 111, 102, 45, 102, 105, 108, 101, 32, 101, 110, 99, 111, 117, 110, 116, 101, 114, 101, 100, 46, 0, 79, 117, 116, 112, 117, 116, 32, 97, 98, 111, 114, 116, 101, 100, 46, 0, 84, 33, 34, 25, 13, 1, 2, 3, 17, 75, 28, 12, 16, 4, 11, 29, 18, 30, 39, 104, 110, 111, 112, 113, 98, 32, 5, 6, 15, 19, 20, 21, 26, 8, 22, 7, 40, 36, 23, 24, 9, 10, 14, 27, 31, 37, 35, 131, 130, 125, 38, 42, 43, 60, 61, 62, 63, 67, 71, 74, 77, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 99, 100, 101, 102, 103, 105, 106, 107, 108, 114, 115, 116, 121, 122, 123, 124, 0, 73, 108, 108, 101, 103, 97, 108, 32, 98, 121, 116, 101, 32, 115, 101, 113, 117, 101, 110, 99, 101, 0, 68, 111, 109, 97, 105, 110, 32, 101, 114, 114, 111, 114, 0, 82, 101, 115, 117, 108, 116, 32, 110, 111, 116, 32, 114, 101, 112, 114, 101, 115, 101, 110, 116, 97, 98, 108, 101, 0, 78, 111, 116, 32, 97, 32, 116, 116, 121, 0, 80, 101, 114, 109, 105, 115, 115, 105, 111, 110, 32, 100, 101, 110, 105, 101, 100, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 110, 111, 116, 32, 112, 101, 114, 109, 105, 116, 116, 101, 100, 0, 78, 111, 32, 115, 117, 99, 104, 32, 102, 105, 108, 101, 32, 111, 114, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 78, 111, 32, 115, 117, 99, 104, 32, 112, 114, 111, 99, 101, 115, 115, 0, 70, 105, 108, 101, 32, 101, 120, 105, 115, 116, 115, 0, 86, 97, 108, 117, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 32, 102, 111, 114, 32, 100, 97, 116, 97, 32, 116, 121, 112, 101, 0, 78, 111, 32, 115, 112, 97, 99, 101, 32, 108, 101, 102, 116, 32, 111, 110, 32, 100, 101, 118, 105, 99, 101, 0, 79, 117, 116, 32, 111, 102, 32, 109, 101, 109, 111, 114, 121, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 98, 117, 115, 121, 0, 73, 110, 116, 101, 114, 114, 117, 112, 116, 101, 100, 32, 115, 121, 115, 116, 101, 109, 32, 99, 97, 108, 108, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 116, 101, 109, 112, 111, 114, 97, 114, 105, 108, 121, 32, 117, 110, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 73, 110, 118, 97, 108, 105, 100, 32, 115, 101, 101, 107, 0, 67, 114, 111, 115, 115, 45, 100, 101, 118, 105, 99, 101, 32, 108, 105, 110, 107, 0, 82, 101, 97, 100, 45, 111, 110, 108, 121, 32, 102, 105, 108, 101, 32, 115, 121, 115, 116, 101, 109, 0, 68, 105, 114, 101, 99, 116, 111, 114, 121, 32, 110, 111, 116, 32, 101, 109, 112, 116, 121, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 115, 101, 116, 32, 98, 121, 32, 112, 101, 101, 114, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 116, 105, 109, 101, 100, 32, 111, 117, 116, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 102, 117, 115, 101, 100, 0, 72, 111, 115, 116, 32, 105, 115, 32, 100, 111, 119, 110, 0, 72, 111, 115, 116, 32, 105, 115, 32, 117, 110, 114, 101, 97, 99, 104, 97, 98, 108, 101, 0, 65, 100, 100, 114, 101, 115, 115, 32, 105, 110, 32, 117, 115, 101, 0, 66, 114, 111, 107, 101, 110, 32, 112, 105, 112, 101, 0, 73, 47, 79, 32, 101, 114, 114, 111, 114, 0, 78, 111, 32, 115, 117, 99, 104, 32, 100, 101, 118, 105, 99, 101, 32, 111, 114, 32, 97, 100, 100, 114, 101, 115, 115, 0, 66, 108, 111, 99, 107, 32, 100, 101, 118, 105, 99, 101, 32, 114, 101, 113, 117, 105, 114, 101, 100, 0, 78, 111, 32, 115, 117, 99, 104, 32, 100, 101, 118, 105, 99, 101, 0, 78, 111, 116, 32, 97, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 73, 115, 32, 97, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 84, 101, 120, 116, 32, 102, 105, 108, 101, 32, 98, 117, 115, 121, 0, 69, 120, 101, 99, 32, 102, 111, 114, 109, 97, 116, 32, 101, 114, 114, 111, 114, 0, 73, 110, 118, 97, 108, 105, 100, 32, 97, 114, 103, 117, 109, 101, 110, 116, 0, 65, 114, 103, 117, 109, 101, 110, 116, 32, 108, 105, 115, 116, 32, 116, 111, 111, 32, 108, 111, 110, 103, 0, 83, 121, 109, 98, 111, 108, 105, 99, 32, 108, 105, 110, 107, 32, 108, 111, 111, 112, 0, 70, 105, 108, 101, 110, 97, 109, 101, 32, 116, 111, 111, 32, 108, 111, 110, 103, 0, 84, 111, 111, 32, 109, 97, 110, 121, 32, 111, 112, 101, 110, 32, 102, 105, 108, 101, 115, 32, 105, 110, 32, 115, 121, 115, 116, 101, 109, 0, 78, 111, 32, 102, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 115, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 66, 97, 100, 32, 102, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 0, 78, 111, 32, 99, 104, 105, 108, 100, 32, 112, 114, 111, 99, 101, 115, 115, 0, 66, 97, 100, 32, 97, 100, 100, 114, 101, 115, 115, 0, 70, 105, 108, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 0, 84, 111, 111, 32, 109, 97, 110, 121, 32, 108, 105, 110, 107, 115, 0, 78, 111, 32, 108, 111, 99, 107, 115, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 100, 101, 97, 100, 108, 111, 99, 107, 32, 119, 111, 117, 108, 100, 32, 111, 99, 99, 117, 114, 0, 83, 116, 97, 116, 101, 32, 110, 111, 116, 32, 114, 101, 99, 111, 118, 101, 114, 97, 98, 108, 101, 0, 80, 114, 101, 118, 105, 111, 117, 115, 32, 111, 119, 110, 101, 114, 32, 100, 105, 101, 100, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 99, 97, 110, 99, 101, 108, 101, 100, 0, 70, 117, 110, 99, 116, 105, 111, 110, 32, 110, 111, 116, 32, 105, 109, 112, 108, 101, 109, 101, 110, 116, 101, 100, 0, 78, 111, 32, 109, 101, 115, 115, 97, 103, 101, 32, 111, 102, 32, 100, 101, 115, 105, 114, 101, 100, 32, 116, 121, 112, 101, 0, 73, 100, 101, 110, 116, 105, 102, 105, 101, 114, 32, 114, 101, 109, 111, 118, 101, 100, 0, 68, 101, 118, 105, 99, 101, 32, 110, 111, 116, 32, 97, 32, 115, 116, 114, 101, 97, 109, 0, 78, 111, 32, 100, 97, 116, 97, 32], "i8", ALLOC_NONE, Runtime.GLOBAL_BASE), allocate([97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 68, 101, 118, 105, 99, 101, 32, 116, 105, 109, 101, 111, 117, 116, 0, 79, 117, 116, 32, 111, 102, 32, 115, 116, 114, 101, 97, 109, 115, 32, 114, 101, 115, 111, 117, 114, 99, 101, 115, 0, 76, 105, 110, 107, 32, 104, 97, 115, 32, 98, 101, 101, 110, 32, 115, 101, 118, 101, 114, 101, 100, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 101, 114, 114, 111, 114, 0, 66, 97, 100, 32, 109, 101, 115, 115, 97, 103, 101, 0, 70, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 32, 105, 110, 32, 98, 97, 100, 32, 115, 116, 97, 116, 101, 0, 78, 111, 116, 32, 97, 32, 115, 111, 99, 107, 101, 116, 0, 68, 101, 115, 116, 105, 110, 97, 116, 105, 111, 110, 32, 97, 100, 100, 114, 101, 115, 115, 32, 114, 101, 113, 117, 105, 114, 101, 100, 0, 77, 101, 115, 115, 97, 103, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 119, 114, 111, 110, 103, 32, 116, 121, 112, 101, 32, 102, 111, 114, 32, 115, 111, 99, 107, 101, 116, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 110, 111, 116, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 83, 111, 99, 107, 101, 116, 32, 116, 121, 112, 101, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 78, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 102, 97, 109, 105, 108, 121, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 65, 100, 100, 114, 101, 115, 115, 32, 102, 97, 109, 105, 108, 121, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 32, 98, 121, 32, 112, 114, 111, 116, 111, 99, 111, 108, 0, 65, 100, 100, 114, 101, 115, 115, 32, 110, 111, 116, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 78, 101, 116, 119, 111, 114, 107, 32, 105, 115, 32, 100, 111, 119, 110, 0, 78, 101, 116, 119, 111, 114, 107, 32, 117, 110, 114, 101, 97, 99, 104, 97, 98, 108, 101, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 115, 101, 116, 32, 98, 121, 32, 110, 101, 116, 119, 111, 114, 107, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 97, 98, 111, 114, 116, 101, 100, 0, 78, 111, 32, 98, 117, 102, 102, 101, 114, 32, 115, 112, 97, 99, 101, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 83, 111, 99, 107, 101, 116, 32, 105, 115, 32, 99, 111, 110, 110, 101, 99, 116, 101, 100, 0, 83, 111, 99, 107, 101, 116, 32, 110, 111, 116, 32, 99, 111, 110, 110, 101, 99, 116, 101, 100, 0, 67, 97, 110, 110, 111, 116, 32, 115, 101, 110, 100, 32, 97, 102, 116, 101, 114, 32, 115, 111, 99, 107, 101, 116, 32, 115, 104, 117, 116, 100, 111, 119, 110, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 97, 108, 114, 101, 97, 100, 121, 32, 105, 110, 32, 112, 114, 111, 103, 114, 101, 115, 115, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 105, 110, 32, 112, 114, 111, 103, 114, 101, 115, 115, 0, 83, 116, 97, 108, 101, 32, 102, 105, 108, 101, 32, 104, 97, 110, 100, 108, 101, 0, 82, 101, 109, 111, 116, 101, 32, 73, 47, 79, 32, 101, 114, 114, 111, 114, 0, 81, 117, 111, 116, 97, 32, 101, 120, 99, 101, 101, 100, 101, 100, 0, 78, 111, 32, 109, 101, 100, 105, 117, 109, 32, 102, 111, 117, 110, 100, 0, 87, 114, 111, 110, 103, 32, 109, 101, 100, 105, 117, 109, 32, 116, 121, 112, 101, 0, 78, 111, 32, 101, 114, 114, 111, 114, 32, 105, 110, 102, 111, 114, 109, 97, 116, 105, 111, 110, 0, 0, 17, 0, 10, 0, 17, 17, 17, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0, 9, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 17, 0, 15, 10, 17, 17, 17, 3, 10, 7, 0, 1, 19, 9, 11, 11, 0, 0, 9, 6, 11, 0, 0, 11, 0, 6, 17, 0, 0, 0, 17, 17, 17, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 17, 0, 10, 10, 17, 17, 17, 0, 10, 0, 0, 2, 0, 9, 11, 0, 0, 0, 9, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 12, 0, 0, 0, 0, 9, 12, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 13, 0, 0, 0, 4, 13, 0, 0, 0, 0, 9, 14, 0, 0, 0, 0, 0, 14, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 15, 0, 0, 0, 0, 9, 16, 0, 0, 0, 0, 0, 16, 0, 0, 16, 0, 0, 18, 0, 0, 0, 18, 18, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 18, 0, 0, 0, 18, 18, 18, 0, 0, 0, 0, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 10, 0, 0, 0, 0, 9, 11, 0, 0, 0, 0, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 12, 0, 0, 0, 0, 9, 12, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 45, 43, 32, 32, 32, 48, 88, 48, 120, 0, 40, 110, 117, 108, 108, 41, 0, 45, 48, 88, 43, 48, 88, 32, 48, 88, 45, 48, 120, 43, 48, 120, 32, 48, 120, 0, 105, 110, 102, 0, 73, 78, 70, 0, 110, 97, 110, 0, 78, 65, 78, 0, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 46, 0], "i8", ALLOC_NONE, Runtime.GLOBAL_BASE + 10240);
            var tempDoublePtr = STATICTOP;

            function _pthread_cond_signal() {
                return 0
            }

            function _pthread_cond_destroy() {
                return 0
            }

            function _pthread_mutex_destroy() {}

            function _pthread_create() {
                return 11
            }
            STATICTOP += 16;
            var GL = {
                    counter: 1,
                    lastError: 0,
                    buffers: [],
                    mappedBuffers: {},
                    programs: [],
                    framebuffers: [],
                    renderbuffers: [],
                    textures: [],
                    uniforms: [],
                    shaders: [],
                    vaos: [],
                    contexts: [],
                    currentContext: null,
                    offscreenCanvases: {},
                    timerQueriesEXT: [],
                    byteSizeByTypeRoot: 5120,
                    byteSizeByType: [1, 1, 2, 2, 4, 4, 4, 2, 3, 4, 8],
                    programInfos: {},
                    stringCache: {},
                    tempFixedLengthArray: [],
                    packAlignment: 4,
                    unpackAlignment: 4,
                    init: function() {
                        GL.miniTempBuffer = new Float32Array(GL.MINI_TEMP_BUFFER_SIZE);
                        for (var e = 0; e < GL.MINI_TEMP_BUFFER_SIZE; e++) GL.miniTempBufferViews[e] = GL.miniTempBuffer.subarray(0, e + 1);
                        for (e = 0; e < 32; e++) GL.tempFixedLengthArray.push(new Array(e))
                    },
                    recordError: function(e) {
                        GL.lastError || (GL.lastError = e)
                    },
                    getNewId: function(e) {
                        for (var r = GL.counter++, t = e.length; t < r; t++) e[t] = null;
                        return r
                    },
                    MINI_TEMP_BUFFER_SIZE: 256,
                    miniTempBuffer: null,
                    miniTempBufferViews: [0],
                    getSource: function(e, r, t, n) {
                        for (var i = "", o = 0; o < r; ++o) {
                            var a;
                            if (n) {
                                var u = HEAP32[n + 4 * o >> 2];
                                a = u < 0 ? Pointer_stringify(HEAP32[t + 4 * o >> 2]) : Pointer_stringify(HEAP32[t + 4 * o >> 2], u)
                            } else a = Pointer_stringify(HEAP32[t + 4 * o >> 2]);
                            i += a
                        }
                        return i
                    },
                    createContext: function(r, t) {
                        var n;
                        void 0 === t.majorVersion && void 0 === t.minorVersion && (t.majorVersion = 1, t.minorVersion = 0);
                        var i = "?";

                        function o(e) {
                            i = e.statusMessage || i
                        }
                        try {
                            r.addEventListener("webglcontextcreationerror", o, !1);
                            try {
                                if (1 == t.majorVersion && 0 == t.minorVersion) n = r.getContext("webgl", t) || r.getContext("experimental-webgl", t);
                                else {
                                    if (2 != t.majorVersion || 0 != t.minorVersion) throw "Unsupported WebGL context version " + majorVersion + "." + minorVersion + "!";
                                    n = r.getContext("webgl2", t)
                                }
                            } finally {
                                r.removeEventListener("webglcontextcreationerror", o, !1)
                            }
                            if (!n) throw ":("
                        } catch (e) {
                            return Module.print("Could not create canvas: " + [i, e, JSON.stringify(t)]), 0
                        }
                        return n ? GL.registerContext(n, t) : 0
                    },
                    registerContext: function(e, r) {
                        var t = GL.getNewId(GL.contexts),
                            n = {
                                handle: t,
                                attributes: r,
                                version: r.majorVersion,
                                GLctx: e
                            };
                        return e.canvas && (e.canvas.GLctxObject = n), GL.contexts[t] = n, (void 0 === r.enableExtensionsByDefault || r.enableExtensionsByDefault) && GL.initExtensions(n), t
                    },
                    makeContextCurrent: function(e) {
                        var r = GL.contexts[e];
                        return !!r && (GLctx = Module.ctx = r.GLctx, GL.currentContext = r, !0)
                    },
                    getContext: function(e) {
                        return GL.contexts[e]
                    },
                    deleteContext: function(e) {
                        GL.currentContext === GL.contexts[e] && (GL.currentContext = null), "object" == typeof JSEvents && JSEvents.removeAllHandlersOnTarget(GL.contexts[e].GLctx.canvas), GL.contexts[e] && GL.contexts[e].GLctx.canvas && (GL.contexts[e].GLctx.canvas.GLctxObject = void 0), GL.contexts[e] = null
                    },
                    initExtensions: function(e) {
                        if (e || (e = GL.currentContext), !e.initExtensionsDone) {
                            e.initExtensionsDone = !0;
                            var r = e.GLctx;
                            if (e.maxVertexAttribs = r.getParameter(r.MAX_VERTEX_ATTRIBS), e.version < 2) {
                                var t = r.getExtension("ANGLE_instanced_arrays");
                                t && (r.vertexAttribDivisor = function(e, r) {
                                    t.vertexAttribDivisorANGLE(e, r)
                                }, r.drawArraysInstanced = function(e, r, n, i) {
                                    t.drawArraysInstancedANGLE(e, r, n, i)
                                }, r.drawElementsInstanced = function(e, r, n, i, o) {
                                    t.drawElementsInstancedANGLE(e, r, n, i, o)
                                });
                                var n = r.getExtension("OES_vertex_array_object");
                                n && (r.createVertexArray = function() {
                                    return n.createVertexArrayOES()
                                }, r.deleteVertexArray = function(e) {
                                    n.deleteVertexArrayOES(e)
                                }, r.bindVertexArray = function(e) {
                                    n.bindVertexArrayOES(e)
                                }, r.isVertexArray = function(e) {
                                    return n.isVertexArrayOES(e)
                                });
                                var i = r.getExtension("WEBGL_draw_buffers");
                                i && (r.drawBuffers = function(e, r) {
                                    i.drawBuffersWEBGL(e, r)
                                })
                            }
                            r.disjointTimerQueryExt = r.getExtension("EXT_disjoint_timer_query");
                            var o = ["OES_texture_float", "OES_texture_half_float", "OES_standard_derivatives", "OES_vertex_array_object", "WEBGL_compressed_texture_s3tc", "WEBGL_depth_texture", "OES_element_index_uint", "EXT_texture_filter_anisotropic", "ANGLE_instanced_arrays", "OES_texture_float_linear", "OES_texture_half_float_linear", "WEBGL_compressed_texture_atc", "WEBGL_compressed_texture_pvrtc", "EXT_color_buffer_half_float", "WEBGL_color_buffer_float", "EXT_frag_depth", "EXT_sRGB", "WEBGL_draw_buffers", "WEBGL_shared_resources", "EXT_shader_texture_lod", "EXT_color_buffer_float"],
                                a = r.getSupportedExtensions();
                            a && a.length > 0 && r.getSupportedExtensions().forEach(function(e) {
                                -1 != o.indexOf(e) && r.getExtension(e)
                            })
                        }
                    },
                    populateUniformTable: function(e) {
                        var r = GL.programs[e];
                        GL.programInfos[e] = {
                            uniforms: {},
                            maxUniformLength: 0,
                            maxAttributeLength: -1,
                            maxUniformBlockNameLength: -1
                        };
                        for (var t = GL.programInfos[e], n = t.uniforms, i = GLctx.getProgramParameter(r, GLctx.ACTIVE_UNIFORMS), o = 0; o < i; ++o) {
                            var a = GLctx.getActiveUniform(r, o),
                                u = a.name;
                            if (t.maxUniformLength = Math.max(t.maxUniformLength, u.length + 1), -1 !== u.indexOf("]", u.length - 1)) {
                                var f = u.lastIndexOf("[");
                                u = u.slice(0, f)
                            }
                            var l = GLctx.getUniformLocation(r, u);
                            if (null != l) {
                                var s = GL.getNewId(GL.uniforms);
                                n[u] = [a.size, s], GL.uniforms[s] = l;
                                for (var c = 1; c < a.size; ++c) {
                                    var d = u + "[" + c + "]";
                                    l = GLctx.getUniformLocation(r, d), s = GL.getNewId(GL.uniforms), GL.uniforms[s] = l
                                }
                            }
                        }
                    }
                },
                PATH = {
                    splitPath: function(e) {
                        return /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/.exec(e).slice(1)
                    },
                    normalizeArray: function(e, r) {
                        for (var t = 0, n = e.length - 1; n >= 0; n--) {
                            var i = e[n];
                            "." === i ? e.splice(n, 1) : ".." === i ? (e.splice(n, 1), t++) : t && (e.splice(n, 1), t--)
                        }
                        if (r)
                            for (; t; t--) e.unshift("..");
                        return e
                    },
                    normalize: function(e) {
                        var r = "/" === e.charAt(0),
                            t = "/" === e.substr(-1);
                        return (e = PATH.normalizeArray(e.split("/").filter(function(e) {
                            return !!e
                        }), !r).join("/")) || r || (e = "."), e && t && (e += "/"), (r ? "/" : "") + e
                    },
                    dirname: function(e) {
                        var r = PATH.splitPath(e),
                            t = r[0],
                            n = r[1];
                        return t || n ? (n && (n = n.substr(0, n.length - 1)), t + n) : "."
                    },
                    basename: function(e) {
                        if ("/" === e) return "/";
                        var r = e.lastIndexOf("/");
                        return -1 === r ? e : e.substr(r + 1)
                    },
                    extname: function(e) {
                        return PATH.splitPath(e)[3]
                    },
                    join: function() {
                        var e = Array.prototype.slice.call(arguments, 0);
                        return PATH.normalize(e.join("/"))
                    },
                    join2: function(e, r) {
                        return PATH.normalize(e + "/" + r)
                    },
                    resolve: function() {
                        for (var e = "", r = !1, t = arguments.length - 1; t >= -1 && !r; t--) {
                            var n = t >= 0 ? arguments[t] : FS.cwd();
                            if ("string" != typeof n) throw new TypeError("Arguments to path.resolve must be strings");
                            if (!n) return "";
                            e = n + "/" + e, r = "/" === n.charAt(0)
                        }
                        return (r ? "/" : "") + (e = PATH.normalizeArray(e.split("/").filter(function(e) {
                            return !!e
                        }), !r).join("/")) || "."
                    },
                    relative: function(e, r) {
                        function t(e) {
                            for (var r = 0; r < e.length && "" === e[r]; r++);
                            for (var t = e.length - 1; t >= 0 && "" === e[t]; t--);
                            return r > t ? [] : e.slice(r, t - r + 1)
                        }
                        e = PATH.resolve(e).substr(1), r = PATH.resolve(r).substr(1);
                        for (var n = t(e.split("/")), i = t(r.split("/")), o = Math.min(n.length, i.length), a = o, u = 0; u < o; u++)
                            if (n[u] !== i[u]) {
                                a = u;
                                break
                            }
                        var f = [];
                        for (u = a; u < n.length; u++) f.push("..");
                        return (f = f.concat(i.slice(a))).join("/")
                    }
                };

            function _emscripten_set_main_loop_timing(e, r) {
                if (Browser.mainLoop.timingMode = e, Browser.mainLoop.timingValue = r, !Browser.mainLoop.func) return 1;
                if (0 == e) Browser.mainLoop.scheduler = function() {
                    var e = 0 | Math.max(0, Browser.mainLoop.tickStartTime + r - _emscripten_get_now());
                    setTimeout(Browser.mainLoop.runner, e)
                }, Browser.mainLoop.method = "timeout";
                else if (1 == e) Browser.mainLoop.scheduler = function() {
                    Browser.requestAnimationFrame(Browser.mainLoop.runner)
                }, Browser.mainLoop.method = "rAF";
                else if (2 == e) {
                    if (!window.setImmediate) {
                        var t = [];
                        window.addEventListener("message", function(e) {
                            e.source === window && "setimmediate" === e.data && (e.stopPropagation(), t.shift()())
                        }, !0), window.setImmediate = function(e) {
                            t.push(e), ENVIRONMENT_IS_WORKER ? (void 0 === Module.setImmediates && (Module.setImmediates = []), Module.setImmediates.push(e), window.postMessage({
                                target: "setimmediate"
                            })) : window.postMessage("setimmediate", "*")
                        }
                    }
                    Browser.mainLoop.scheduler = function() {
                        window.setImmediate(Browser.mainLoop.runner)
                    }, Browser.mainLoop.method = "immediate"
                }
                return 0
            }

            function _emscripten_get_now() {
                abort()
            }

            function _emscripten_set_main_loop(e, r, t, n, i) {
                var o;
                Module.noExitRuntime = !0, Browser.mainLoop.func = e, Browser.mainLoop.arg = n, o = void 0 !== n ? function() {
                    Module.dynCall_vi(e, n)
                } : function() {
                    Module.dynCall_v(e)
                };
                var a = Browser.mainLoop.currentlyRunningMainloop;
                if (Browser.mainLoop.runner = function() {
                    if (!ABORT)
                        if (Browser.mainLoop.queue.length > 0) {
                            var e = Date.now(),
                                r = Browser.mainLoop.queue.shift();
                            if (r.func(r.arg), Browser.mainLoop.remainingBlockers) {
                                var t = Browser.mainLoop.remainingBlockers,
                                    n = t % 1 == 0 ? t - 1 : Math.floor(t);
                                r.counted ? Browser.mainLoop.remainingBlockers = n : (n += .5, Browser.mainLoop.remainingBlockers = (8 * t + n) / 9)
                            }
                            if (console.log('main loop blocker "' + r.name + '" took ' + (Date.now() - e) + " ms"), Browser.mainLoop.updateStatus(), a < Browser.mainLoop.currentlyRunningMainloop) return;
                            setTimeout(Browser.mainLoop.runner, 0)
                        } else a < Browser.mainLoop.currentlyRunningMainloop || (Browser.mainLoop.currentFrameNumber = Browser.mainLoop.currentFrameNumber + 1 | 0, 1 == Browser.mainLoop.timingMode && Browser.mainLoop.timingValue > 1 && Browser.mainLoop.currentFrameNumber % Browser.mainLoop.timingValue != 0 ? Browser.mainLoop.scheduler() : (0 == Browser.mainLoop.timingMode && (Browser.mainLoop.tickStartTime = _emscripten_get_now()), "timeout" === Browser.mainLoop.method && Module.ctx && (Module.printErr("Looks like you are rendering without using requestAnimationFrame for the main loop. You should use 0 for the frame rate in emscripten_set_main_loop in order to use requestAnimationFrame, as that can greatly improve your frame rates!"), Browser.mainLoop.method = ""), Browser.mainLoop.runIter(o), a < Browser.mainLoop.currentlyRunningMainloop || ("object" == typeof SDL && SDL.audio && SDL.audio.queueNewAudioData && SDL.audio.queueNewAudioData(), Browser.mainLoop.scheduler())))
                }, i || (r && r > 0 ? _emscripten_set_main_loop_timing(0, 1e3 / r) : _emscripten_set_main_loop_timing(1, 1), Browser.mainLoop.scheduler()), t) throw "SimulateInfiniteLoop"
            }
            var Browser = {
                    mainLoop: {
                        scheduler: null,
                        method: "",
                        currentlyRunningMainloop: 0,
                        func: null,
                        arg: 0,
                        timingMode: 0,
                        timingValue: 0,
                        currentFrameNumber: 0,
                        queue: [],
                        pause: function() {
                            Browser.mainLoop.scheduler = null, Browser.mainLoop.currentlyRunningMainloop++
                        },
                        resume: function() {
                            Browser.mainLoop.currentlyRunningMainloop++;
                            var e = Browser.mainLoop.timingMode,
                                r = Browser.mainLoop.timingValue,
                                t = Browser.mainLoop.func;
                            Browser.mainLoop.func = null, _emscripten_set_main_loop(t, 0, !1, Browser.mainLoop.arg, !0), _emscripten_set_main_loop_timing(e, r), Browser.mainLoop.scheduler()
                        },
                        updateStatus: function() {
                            if (Module.setStatus) {
                                var e = Module.statusMessage || "Please wait...",
                                    r = Browser.mainLoop.remainingBlockers,
                                    t = Browser.mainLoop.expectedBlockers;
                                r ? r < t ? Module.setStatus(e + " (" + (t - r) + "/" + t + ")") : Module.setStatus(e) : Module.setStatus("")
                            }
                        },
                        runIter: function(r) {
                            if (!ABORT) {
                                if (Module.preMainLoop && !1 === Module.preMainLoop()) return;
                                try {
                                    r()
                                } catch (e) {
                                    if (e instanceof ExitStatus) return;
                                    throw e && "object" == typeof e && e.stack && Module.printErr("exception thrown: " + [e, e.stack]), e
                                }
                                Module.postMainLoop && Module.postMainLoop()
                            }
                        }
                    },
                    isFullscreen: !1,
                    pointerLock: !1,
                    moduleContextCreatedCallbacks: [],
                    workers: [],
                    init: function() {
                        if (Module.preloadPlugins || (Module.preloadPlugins = []), !Browser.initted) {
                            Browser.initted = !0;
                            try {
                                new Blob, Browser.hasBlobConstructor = !0
                            } catch (e) {
                                Browser.hasBlobConstructor = !1, console.log("warning: no blob constructor, cannot create blobs with mimetypes")
                            }
                            Browser.BlobBuilder = "undefined" != typeof MozBlobBuilder ? MozBlobBuilder : "undefined" != typeof WebKitBlobBuilder ? WebKitBlobBuilder : Browser.hasBlobConstructor ? null : console.log("warning: no BlobBuilder"), Browser.URLObject = "undefined" != typeof window ? window.URL ? window.URL : window.webkitURL : void 0, Module.noImageDecoding || void 0 !== Browser.URLObject || (console.log("warning: Browser does not support creating object URLs. Built-in browser image decoding will not be available."), Module.noImageDecoding = !0);
                            var r = {
                                canHandle: function(e) {
                                    return !Module.noImageDecoding && /\.(jpg|jpeg|png|bmp)$/i.test(e)
                                },
                                handle: function(r, t, n, i) {
                                    var o = null;
                                    if (Browser.hasBlobConstructor) try {
                                        (o = new Blob([r], {
                                            type: Browser.getMimetype(t)
                                        })).size !== r.length && (o = new Blob([new Uint8Array(r).buffer], {
                                            type: Browser.getMimetype(t)
                                        }))
                                    } catch (e) {
                                        Runtime.warnOnce("Blob constructor present but fails: " + e + "; falling back to blob builder")
                                    }
                                    if (!o) {
                                        var a = new Browser.BlobBuilder;
                                        a.append(new Uint8Array(r).buffer), o = a.getBlob()
                                    }
                                    var u = Browser.URLObject.createObjectURL(o),
                                        f = new Image;
                                    f.onload = function() {
                                        var e = document.createElement("canvas");
                                        e.width = f.width, e.height = f.height, e.getContext("2d").drawImage(f, 0, 0), Module.preloadedImages[t] = e, Browser.URLObject.revokeObjectURL(u), n && n(r)
                                    }, f.onerror = function(e) {
                                        console.log("Image " + u + " could not be decoded"), i && i()
                                    }, f.src = u
                                }
                            };
                            Module.preloadPlugins.push(r);
                            var t = {
                                canHandle: function(e) {
                                    return !Module.noAudioDecoding && e.substr(-4) in {
                                        ".ogg": 1,
                                        ".wav": 1,
                                        ".mp3": 1
                                    }
                                },
                                handle: function(r, t, n, i) {
                                    var o = !1;

                                    function a(e) {
                                        o || (o = !0, Module.preloadedAudios[t] = e, n && n(r))
                                    }

                                    function u() {
                                        o || (o = !0, Module.preloadedAudios[t] = new Audio, i && i())
                                    }
                                    if (!Browser.hasBlobConstructor) return u();
                                    try {
                                        var f = new Blob([r], {
                                            type: Browser.getMimetype(t)
                                        })
                                    } catch (e) {
                                        return u()
                                    }
                                    var l = Browser.URLObject.createObjectURL(f),
                                        s = new Audio;
                                    s.addEventListener("canplaythrough", function() {
                                        a(s)
                                    }, !1), s.onerror = function(e) {
                                        o || (console.log("warning: browser could not fully decode audio " + t + ", trying slower base64 approach"), s.src = "data:audio/x-" + t.substr(-3) + ";base64," + function(e) {
                                            for (var r = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", t = "", n = 0, i = 0, o = 0; o < e.length; o++)
                                                for (n = n << 8 | e[o], i += 8; i >= 6;) {
                                                    var a = n >> i - 6 & 63;
                                                    i -= 6, t += r[a]
                                                }
                                            return 2 == i ? (t += r[(3 & n) << 4], t += "==") : 4 == i && (t += r[(15 & n) << 2], t += "="), t
                                        }(r), a(s))
                                    }, s.src = l, Browser.safeSetTimeout(function() {
                                        a(s)
                                    }, 1e4)
                                }
                            };
                            Module.preloadPlugins.push(t);
                            var n = Module.canvas;
                            n && (n.requestPointerLock = n.requestPointerLock || n.mozRequestPointerLock || n.webkitRequestPointerLock || n.msRequestPointerLock || function() {}, n.exitPointerLock = document.exitPointerLock || document.mozExitPointerLock || document.webkitExitPointerLock || document.msExitPointerLock || function() {}, n.exitPointerLock = n.exitPointerLock.bind(document), document.addEventListener("pointerlockchange", i, !1), document.addEventListener("mozpointerlockchange", i, !1), document.addEventListener("webkitpointerlockchange", i, !1), document.addEventListener("mspointerlockchange", i, !1), Module.elementPointerLock && n.addEventListener("click", function(e) {
                                !Browser.pointerLock && Module.canvas.requestPointerLock && (Module.canvas.requestPointerLock(), e.preventDefault())
                            }, !1))
                        }

                        function i() {
                            Browser.pointerLock = document.pointerLockElement === Module.canvas || document.mozPointerLockElement === Module.canvas || document.webkitPointerLockElement === Module.canvas || document.msPointerLockElement === Module.canvas
                        }
                    },
                    createContext: function(e, r, t, n) {
                        if (r && Module.ctx && e == Module.canvas) return Module.ctx;
                        var i, o;
                        if (r) {
                            var a = {
                                antialias: !1,
                                alpha: !1
                            };
                            if (n)
                                for (var u in n) a[u] = n[u];
                            (o = GL.createContext(e, a)) && (i = GL.getContext(o).GLctx)
                        } else i = e.getContext("2d");
                        return i ? (t && (Module.ctx = i, r && GL.makeContextCurrent(o), Module.useWebGL = r, Browser.moduleContextCreatedCallbacks.forEach(function(e) {
                            e()
                        }), Browser.init()), i) : null
                    },
                    destroyContext: function(e, r, t) {},
                    fullscreenHandlersInstalled: !1,
                    lockPointer: void 0,
                    resizeCanvas: void 0,
                    requestFullscreen: function(e, r, t) {
                        Browser.lockPointer = e, Browser.resizeCanvas = r, Browser.vrDevice = t, void 0 === Browser.lockPointer && (Browser.lockPointer = !0), void 0 === Browser.resizeCanvas && (Browser.resizeCanvas = !1), void 0 === Browser.vrDevice && (Browser.vrDevice = null);
                        var n = Module.canvas;

                        function i() {
                            Browser.isFullscreen = !1;
                            var e = n.parentNode;
                            (document.fullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || document.webkitFullscreenElement || document.webkitCurrentFullScreenElement) === e ? (n.exitFullscreen = document.exitFullscreen || document.cancelFullScreen || document.mozCancelFullScreen || document.msExitFullscreen || document.webkitCancelFullScreen || function() {}, n.exitFullscreen = n.exitFullscreen.bind(document), Browser.lockPointer && n.requestPointerLock(), Browser.isFullscreen = !0, Browser.resizeCanvas && Browser.setFullscreenCanvasSize()) : (e.parentNode.insertBefore(n, e), e.parentNode.removeChild(e), Browser.resizeCanvas && Browser.setWindowedCanvasSize()), Module.onFullScreen && Module.onFullScreen(Browser.isFullscreen), Module.onFullscreen && Module.onFullscreen(Browser.isFullscreen), Browser.updateCanvasDimensions(n)
                        }
                        Browser.fullscreenHandlersInstalled || (Browser.fullscreenHandlersInstalled = !0, document.addEventListener("fullscreenchange", i, !1), document.addEventListener("mozfullscreenchange", i, !1), document.addEventListener("webkitfullscreenchange", i, !1), document.addEventListener("MSFullscreenChange", i, !1));
                        var o = document.createElement("div");
                        n.parentNode.insertBefore(o, n), o.appendChild(n), o.requestFullscreen = o.requestFullscreen || o.mozRequestFullScreen || o.msRequestFullscreen || (o.webkitRequestFullscreen ? function() {
                            o.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
                        } : null) || (o.webkitRequestFullScreen ? function() {
                            o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT)
                        } : null), t ? o.requestFullscreen({
                            vrDisplay: t
                        }) : o.requestFullscreen()
                    },
                    requestFullScreen: function(e, r, t) {
                        return Module.printErr("Browser.requestFullScreen() is deprecated. Please call Browser.requestFullscreen instead."), Browser.requestFullScreen = function(e, r, t) {
                            return Browser.requestFullscreen(e, r, t)
                        }, Browser.requestFullscreen(e, r, t)
                    },
                    nextRAF: 0,
                    fakeRequestAnimationFrame: function(e) {
                        var r = Date.now();
                        if (0 === Browser.nextRAF) Browser.nextRAF = r + 1e3 / 60;
                        else
                            for (; r + 2 >= Browser.nextRAF;) Browser.nextRAF += 1e3 / 60;
                        var t = Math.max(Browser.nextRAF - r, 0);
                        setTimeout(e, t)
                    },
                    requestAnimationFrame: function(e) {
                        "undefined" == typeof window ? Browser.fakeRequestAnimationFrame(e) : (window.requestAnimationFrame || (window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame || Browser.fakeRequestAnimationFrame), window.requestAnimationFrame(e))
                    },
                    safeCallback: function(e) {
                        return function() {
                            if (!ABORT) return e.apply(null, arguments)
                        }
                    },
                    allowAsyncCallbacks: !0,
                    queuedAsyncCallbacks: [],
                    pauseAsyncCallbacks: function() {
                        Browser.allowAsyncCallbacks = !1
                    },
                    resumeAsyncCallbacks: function() {
                        if (Browser.allowAsyncCallbacks = !0, Browser.queuedAsyncCallbacks.length > 0) {
                            var e = Browser.queuedAsyncCallbacks;
                            Browser.queuedAsyncCallbacks = [], e.forEach(function(e) {
                                e()
                            })
                        }
                    },
                    safeRequestAnimationFrame: function(e) {
                        return Browser.requestAnimationFrame(function() {
                            ABORT || (Browser.allowAsyncCallbacks ? e() : Browser.queuedAsyncCallbacks.push(e))
                        })
                    },
                    safeSetTimeout: function(e, r) {
                        return Module.noExitRuntime = !0, setTimeout(function() {
                            ABORT || (Browser.allowAsyncCallbacks ? e() : Browser.queuedAsyncCallbacks.push(e))
                        }, r)
                    },
                    safeSetInterval: function(e, r) {
                        return Module.noExitRuntime = !0, setInterval(function() {
                            ABORT || Browser.allowAsyncCallbacks && e()
                        }, r)
                    },
                    getMimetype: function(e) {
                        return {
                            jpg: "image/jpeg",
                            jpeg: "image/jpeg",
                            png: "image/png",
                            bmp: "image/bmp",
                            ogg: "audio/ogg",
                            wav: "audio/wav",
                            mp3: "audio/mpeg"
                        }[e.substr(e.lastIndexOf(".") + 1)]
                    },
                    getUserMedia: function(e) {
                        window.getUserMedia || (window.getUserMedia = navigator.getUserMedia || navigator.mozGetUserMedia), window.getUserMedia(e)
                    },
                    getMovementX: function(e) {
                        return e.movementX || e.mozMovementX || e.webkitMovementX || 0
                    },
                    getMovementY: function(e) {
                        return e.movementY || e.mozMovementY || e.webkitMovementY || 0
                    },
                    getMouseWheelDelta: function(e) {
                        var r = 0;
                        switch (e.type) {
                            case "DOMMouseScroll":
                                r = e.detail;
                                break;
                            case "mousewheel":
                                r = e.wheelDelta;
                                break;
                            case "wheel":
                                r = e.deltaY;
                                break;
                            default:
                                throw "unrecognized mouse wheel event: " + e.type
                        }
                        return r
                    },
                    mouseX: 0,
                    mouseY: 0,
                    mouseMovementX: 0,
                    mouseMovementY: 0,
                    touches: {},
                    lastTouches: {},
                    calculateMouseEvent: function(e) {
                        if (Browser.pointerLock) "mousemove" != e.type && "mozMovementX" in e ? Browser.mouseMovementX = Browser.mouseMovementY = 0 : (Browser.mouseMovementX = Browser.getMovementX(e), Browser.mouseMovementY = Browser.getMovementY(e)), void 0 !== SDL ? (Browser.mouseX = SDL.mouseX + Browser.mouseMovementX, Browser.mouseY = SDL.mouseY + Browser.mouseMovementY) : (Browser.mouseX += Browser.mouseMovementX, Browser.mouseY += Browser.mouseMovementY);
                        else {
                            var r = Module.canvas.getBoundingClientRect(),
                                t = Module.canvas.width,
                                n = Module.canvas.height,
                                i = void 0 !== window.scrollX ? window.scrollX : window.pageXOffset,
                                o = void 0 !== window.scrollY ? window.scrollY : window.pageYOffset;
                            if ("touchstart" === e.type || "touchend" === e.type || "touchmove" === e.type) {
                                var a = e.touch;
                                if (void 0 === a) return;
                                var u = a.pageX - (i + r.left),
                                    f = a.pageY - (o + r.top),
                                    l = {
                                        x: u *= t / r.width,
                                        y: f *= n / r.height
                                    };
                                if ("touchstart" === e.type) Browser.lastTouches[a.identifier] = l, Browser.touches[a.identifier] = l;
                                else if ("touchend" === e.type || "touchmove" === e.type) {
                                    var s = Browser.touches[a.identifier];
                                    s || (s = l), Browser.lastTouches[a.identifier] = s, Browser.touches[a.identifier] = l
                                }
                                return
                            }
                            var c = e.pageX - (i + r.left),
                                d = e.pageY - (o + r.top);
                            c *= t / r.width, d *= n / r.height, Browser.mouseMovementX = c - Browser.mouseX, Browser.mouseMovementY = d - Browser.mouseY, Browser.mouseX = c, Browser.mouseY = d
                        }
                    },
                    asyncLoad: function(e, r, t, n) {
                        var i = n ? "" : getUniqueRunDependency("al " + e);
                        Module.readAsync(e, function(e) {
                            r(new Uint8Array(e)), i && removeRunDependency(i)
                        }, function(r) {
                            if (!t) throw 'Loading data file "' + e + '" failed.';
                            t()
                        }), i && addRunDependency(i)
                    },
                    resizeListeners: [],
                    updateResizeListeners: function() {
                        var e = Module.canvas;
                        Browser.resizeListeners.forEach(function(r) {
                            r(e.width, e.height)
                        })
                    },
                    setCanvasSize: function(e, r, t) {
                        var n = Module.canvas;
                        Browser.updateCanvasDimensions(n, e, r), t || Browser.updateResizeListeners()
                    },
                    windowedWidth: 0,
                    windowedHeight: 0,
                    setFullscreenCanvasSize: function() {
                        if (void 0 !== SDL) {
                            var e = HEAPU32[SDL.screen + 0 * Runtime.QUANTUM_SIZE >> 2];
                            e |= 8388608, HEAP32[SDL.screen + 0 * Runtime.QUANTUM_SIZE >> 2] = e
                        }
                        Browser.updateResizeListeners()
                    },
                    setWindowedCanvasSize: function() {
                        if (void 0 !== SDL) {
                            var e = HEAPU32[SDL.screen + 0 * Runtime.QUANTUM_SIZE >> 2];
                            e &= -8388609, HEAP32[SDL.screen + 0 * Runtime.QUANTUM_SIZE >> 2] = e
                        }
                        Browser.updateResizeListeners()
                    },
                    updateCanvasDimensions: function(e, r, t) {
                        r && t ? (e.widthNative = r, e.heightNative = t) : (r = e.widthNative, t = e.heightNative);
                        var n = r,
                            i = t;
                        if (Module.forcedAspectRatio && Module.forcedAspectRatio > 0 && (n / i < Module.forcedAspectRatio ? n = Math.round(i * Module.forcedAspectRatio) : i = Math.round(n / Module.forcedAspectRatio)), (document.fullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || document.webkitFullscreenElement || document.webkitCurrentFullScreenElement) === e.parentNode && "undefined" != typeof screen) {
                            var o = Math.min(screen.width / n, screen.height / i);
                            n = Math.round(n * o), i = Math.round(i * o)
                        }
                        Browser.resizeCanvas ? (e.width != n && (e.width = n), e.height != i && (e.height = i), void 0 !== e.style && (e.style.removeProperty("width"), e.style.removeProperty("height"))) : (e.width != r && (e.width = r), e.height != t && (e.height = t), void 0 !== e.style && (n != r || i != t ? (e.style.setProperty("width", n + "px", "important"), e.style.setProperty("height", i + "px", "important")) : (e.style.removeProperty("width"), e.style.removeProperty("height"))))
                    },
                    wgetRequests: {},
                    nextWgetRequestHandle: 0,
                    getNextWgetRequestHandle: function() {
                        var e = Browser.nextWgetRequestHandle;
                        return Browser.nextWgetRequestHandle++, e
                    }
                },
                _environ = STATICTOP;

            function ___buildEnvironment(e) {
                var r, t;
                ___buildEnvironment.called ? (t = HEAP32[_environ >> 2], r = HEAP32[t >> 2]) : (___buildEnvironment.called = !0, ENV.USER = ENV.LOGNAME = "web_user", ENV.PATH = "/", ENV.PWD = "/", ENV.HOME = "/home/web_user", ENV.LANG = "C", ENV._ = Module.thisProgram, r = allocate(1024, "i8", ALLOC_STATIC), t = allocate(256, "i8*", ALLOC_STATIC), HEAP32[t >> 2] = r, HEAP32[_environ >> 2] = t);
                var n = [],
                    i = 0;
                for (var o in e)
                    if ("string" == typeof e[o]) {
                        var a = o + "=" + e[o];
                        n.push(a), i += a.length
                    }
                if (i > 1024) throw new Error("Environment size exceeded TOTAL_ENV_SIZE!");
                for (var u = 0; u < n.length; u++) writeAsciiToMemory(a = n[u], r), HEAP32[t + 4 * u >> 2] = r, r += a.length + 1;
                HEAP32[t + 4 * n.length >> 2] = 0
            }
            STATICTOP += 16;
            var ENV = {};

            function _getenv(e) {
                return 0 === e ? 0 : (e = Pointer_stringify(e), ENV.hasOwnProperty(e) ? (_getenv.ret && _free(_getenv.ret), _getenv.ret = allocate(intArrayFromString(ENV[e]), "i8", ALLOC_NORMAL), _getenv.ret) : 0)
            }
            var ERRNO_CODES = {
                EPERM: 1,
                ENOENT: 2,
                ESRCH: 3,
                EINTR: 4,
                EIO: 5,
                ENXIO: 6,
                E2BIG: 7,
                ENOEXEC: 8,
                EBADF: 9,
                ECHILD: 10,
                EAGAIN: 11,
                EWOULDBLOCK: 11,
                ENOMEM: 12,
                EACCES: 13,
                EFAULT: 14,
                ENOTBLK: 15,
                EBUSY: 16,
                EEXIST: 17,
                EXDEV: 18,
                ENODEV: 19,
                ENOTDIR: 20,
                EISDIR: 21,
                EINVAL: 22,
                ENFILE: 23,
                EMFILE: 24,
                ENOTTY: 25,
                ETXTBSY: 26,
                EFBIG: 27,
                ENOSPC: 28,
                ESPIPE: 29,
                EROFS: 30,
                EMLINK: 31,
                EPIPE: 32,
                EDOM: 33,
                ERANGE: 34,
                ENOMSG: 42,
                EIDRM: 43,
                ECHRNG: 44,
                EL2NSYNC: 45,
                EL3HLT: 46,
                EL3RST: 47,
                ELNRNG: 48,
                EUNATCH: 49,
                ENOCSI: 50,
                EL2HLT: 51,
                EDEADLK: 35,
                ENOLCK: 37,
                EBADE: 52,
                EBADR: 53,
                EXFULL: 54,
                ENOANO: 55,
                EBADRQC: 56,
                EBADSLT: 57,
                EDEADLOCK: 35,
                EBFONT: 59,
                ENOSTR: 60,
                ENODATA: 61,
                ETIME: 62,
                ENOSR: 63,
                ENONET: 64,
                ENOPKG: 65,
                EREMOTE: 66,
                ENOLINK: 67,
                EADV: 68,
                ESRMNT: 69,
                ECOMM: 70,
                EPROTO: 71,
                EMULTIHOP: 72,
                EDOTDOT: 73,
                EBADMSG: 74,
                ENOTUNIQ: 76,
                EBADFD: 77,
                EREMCHG: 78,
                ELIBACC: 79,
                ELIBBAD: 80,
                ELIBSCN: 81,
                ELIBMAX: 82,
                ELIBEXEC: 83,
                ENOSYS: 38,
                ENOTEMPTY: 39,
                ENAMETOOLONG: 36,
                ELOOP: 40,
                EOPNOTSUPP: 95,
                EPFNOSUPPORT: 96,
                ECONNRESET: 104,
                ENOBUFS: 105,
                EAFNOSUPPORT: 97,
                EPROTOTYPE: 91,
                ENOTSOCK: 88,
                ENOPROTOOPT: 92,
                ESHUTDOWN: 108,
                ECONNREFUSED: 111,
                EADDRINUSE: 98,
                ECONNABORTED: 103,
                ENETUNREACH: 101,
                ENETDOWN: 100,
                ETIMEDOUT: 110,
                EHOSTDOWN: 112,
                EHOSTUNREACH: 113,
                EINPROGRESS: 115,
                EALREADY: 114,
                EDESTADDRREQ: 89,
                EMSGSIZE: 90,
                EPROTONOSUPPORT: 93,
                ESOCKTNOSUPPORT: 94,
                EADDRNOTAVAIL: 99,
                ENETRESET: 102,
                EISCONN: 106,
                ENOTCONN: 107,
                ETOOMANYREFS: 109,
                EUSERS: 87,
                EDQUOT: 122,
                ESTALE: 116,
                ENOTSUP: 95,
                ENOMEDIUM: 123,
                EILSEQ: 84,
                EOVERFLOW: 75,
                ECANCELED: 125,
                ENOTRECOVERABLE: 131,
                EOWNERDEAD: 130,
                ESTRPIPE: 86
            };

            function ___setErrNo(e) {
                return Module.___errno_location && (HEAP32[Module.___errno_location() >> 2] = e), e
            }

            function _putenv(e) {
                if (0 === e) return ___setErrNo(ERRNO_CODES.EINVAL), -1;
                var r = (e = Pointer_stringify(e)).indexOf("=");
                if ("" === e || -1 === e.indexOf("=")) return ___setErrNo(ERRNO_CODES.EINVAL), -1;
                var t = e.slice(0, r),
                    n = e.slice(r + 1);
                return t in ENV && ENV[t] === n || (ENV[t] = n, ___buildEnvironment(ENV)), 0
            }

            function _SDL_RWFromConstMem(e, r) {
                var t = SDL.rwops.length;
                return SDL.rwops.push({
                    bytes: e,
                    count: r
                }), t
            }

            function _TTF_FontHeight(e) {
                return SDL.fonts[e].size
            }

            function _TTF_SizeText(e, r, t, n) {
                var i = SDL.fonts[e];
                return t && (HEAP32[t >> 2] = SDL.estimateTextWidth(i, Pointer_stringify(r))), n && (HEAP32[n >> 2] = i.size), 0
            }

            function _TTF_RenderText_Solid(e, r, t) {
                r = Pointer_stringify(r) || " ";
                var n = SDL.fonts[e],
                    i = SDL.estimateTextWidth(n, r),
                    o = n.size,
                    a = (t = SDL.loadColorToCSSRGB(t), SDL.makeFontString(o, n.name)),
                    u = SDL.makeSurface(i, o, 0, !1, "text:" + r),
                    f = SDL.surfaces[u];
                return f.ctx.save(), f.ctx.fillStyle = t, f.ctx.font = a, f.ctx.textBaseline = "bottom", f.ctx.fillText(r, 0, 0 | o), f.ctx.restore(), u
            }

            function _Mix_HaltMusic() {
                var e = SDL.music.audio;
                return e && (e.src = e.src, e.currentPosition = 0, e.pause()), SDL.music.audio = null, SDL.hookMusicFinished && Module.dynCall_v(SDL.hookMusicFinished), 0
            }

            function _Mix_PlayMusic(e, r) {
                SDL.music.audio && (SDL.music.audio.paused || Module.printErr("Music is already playing. " + SDL.music.source), SDL.music.audio.pause());
                var t, n = SDL.audios[e];
                return n.webAudio ? ((t = {}).resource = n, t.paused = !1, t.currentPosition = 0, t.play = function() {
                    SDL.playWebAudio(this)
                }, t.pause = function() {
                    SDL.pauseWebAudio(this)
                }) : n.audio && (t = n.audio), t.onended = function() {
                    SDL.music.audio == this && _Mix_HaltMusic()
                }, t.loop = 0 != r, t.volume = SDL.music.volume, SDL.music.audio = t, t.play(), 0
            }

            function _Mix_FreeChunk(e) {
                SDL.audios[e] = null
            }

            function _Mix_LoadWAV_RW(r, t) {
                var n = SDL.rwops[r];
                if (void 0 === n) return 0;
                var i, o, a, u = "";
                if (void 0 !== n.filename) {
                    u = PATH.resolve(n.filename);
                    var f = Module.preloadedAudios[u];
                    if (!f) {
                        null === f && Module.printErr("Trying to reuse preloaded audio, but freePreloadedMediaOnUse is set!"), Module.noAudioDecoding || Runtime.warnOnce("Cannot find preloaded audio " + u);
                        try {
                            a = FS.readFile(u)
                        } catch (e) {
                            return Module.printErr("Couldn't find file for: " + u), 0
                        }
                    }
                    Module.freePreloadedMediaOnUse && (Module.preloadedAudios[u] = null), i = f
                } else {
                    if (void 0 === n.bytes) return 0;
                    a = SDL.webAudioAvailable() ? HEAPU8.buffer.slice(n.bytes, n.bytes + n.count) : HEAPU8.subarray(n.bytes, n.bytes + n.count)
                }
                var l = a && a.buffer || a,
                    s = void 0 === Module.SDL_canPlayWithWebAudio || Module.SDL_canPlayWithWebAudio(u, l);
                if (void 0 !== a && SDL.webAudioAvailable() && s) i = void 0, (o = {}).onDecodeComplete = [], SDL.audioContext.decodeAudioData(l, function(e) {
                    o.decodedBuffer = e, o.onDecodeComplete.forEach(function(e) {
                        e()
                    }), o.onDecodeComplete = void 0
                });
                else if (void 0 === i && a) {
                    var c = new Blob([a], {
                            type: n.mimetype
                        }),
                        d = URL.createObjectURL(c);
                    (i = new Audio).src = d, i.mozAudioChannelType = "content"
                }
                var _ = SDL.audios.length;
                return SDL.audios.push({
                    source: u,
                    audio: i,
                    webAudio: o
                }), _
            }

            function _Mix_PlayChannel(e, r, t) {
                var n = SDL.audios[r];
                if (!n) return -1;
                if (!n.audio && !n.webAudio) return -1;
                if (-1 == e) {
                    for (var i = SDL.channelMinimumNumber; i < SDL.numChannels; i++)
                        if (!SDL.channels[i].audio) {
                            e = i;
                            break
                        }
                    if (-1 == e) return Module.printErr("All " + SDL.numChannels + " channels in use!"), -1
                }
                var o, a = SDL.channels[e];
                return n.webAudio ? ((o = {}).resource = n, o.paused = !1, o.currentPosition = 0, o.play = function() {
                    SDL.playWebAudio(this)
                }, o.pause = function() {
                    SDL.pauseWebAudio(this)
                }) : ((o = n.audio.cloneNode(!0)).numChannels = n.audio.numChannels, o.frequency = n.audio.frequency), o.onended = function() {
                    a.audio == this && (a.audio.paused = !0, a.audio = null), SDL.channelFinished && Runtime.getFuncWrapper(SDL.channelFinished, "vi")(e)
                }, a.audio = o, o.loop = 0 != t, o.volume = a.volume, o.play(), e
            }

            function _SDL_PauseAudio(e) {
                SDL.audio && (e ? void 0 !== SDL.audio.timer && (clearTimeout(SDL.audio.timer), SDL.audio.numAudioTimersPending = 0, SDL.audio.timer = void 0) : SDL.audio.timer || (SDL.audio.numAudioTimersPending = 1, SDL.audio.timer = Browser.safeSetTimeout(SDL.audio.caller, 1)), SDL.audio.paused = e)
            }

            function _SDL_CloseAudio() {
                SDL.audio && (_SDL_PauseAudio(1), _free(SDL.audio.buffer), SDL.audio = null, SDL.allocateChannels(0))
            }

            function _SDL_LockSurface(e) {
                var r = SDL.surfaces[e];
                if (r.locked++, r.locked > 1) return 0;
                if (r.buffer || (r.buffer = _malloc(r.width * r.height * 4), HEAP32[e + 20 >> 2] = r.buffer), HEAP32[e + 20 >> 2] = r.buffer, e == SDL.screen && Module.screenIsReadOnly && r.image) return 0;
                if (SDL.defaults.discardOnLock) {
                    if (r.image || (r.image = r.ctx.createImageData(r.width, r.height)), !SDL.defaults.opaqueFrontBuffer) return
                } else r.image = r.ctx.getImageData(0, 0, r.width, r.height);
                if (e == SDL.screen && SDL.defaults.opaqueFrontBuffer)
                    for (var t = r.image.data, n = t.length, i = 0; i < n / 4; i++) t[4 * i + 3] = 255;
                if (SDL.defaults.copyOnLock && !SDL.defaults.discardOnLock) {
                    if (r.isFlagSet(2097152)) throw "CopyOnLock is not supported for SDL_LockSurface with SDL_HWPALETTE flag set" + (new Error).stack;
                    HEAPU8.set(r.image.data, r.buffer)
                }
                return 0
            }

            function _SDL_FreeRW(e) {
                for (SDL.rwops[e] = null; SDL.rwops.length > 0 && null === SDL.rwops[SDL.rwops.length - 1];) SDL.rwops.pop()
            }

            function _IMG_Load_RW(e, r) {
                try {
                    var t = function() {
                            n && r && _SDL_FreeRW(e)
                        },
                        n = SDL.rwops[e];
                    if (void 0 === n) return 0;
                    var i = n.filename;
                    if (void 0 === i) return Runtime.warnOnce("Only file names that have been preloaded are supported for IMG_Load_RW. Consider using STB_IMAGE=1 if you want synchronous image decoding (see settings.js), or package files with --use-preload-plugins"), 0;
                    if (!o) {
                        i = PATH.resolve(i);
                        var o = Module.preloadedImages[i];
                        if (!o) return null === o && Module.printErr("Trying to reuse preloaded image, but freePreloadedMediaOnUse is set!"), Runtime.warnOnce("Cannot find preloaded image " + i), Runtime.warnOnce("Cannot find preloaded image " + i + ". Consider using STB_IMAGE=1 if you want synchronous image decoding (see settings.js), or package files with --use-preload-plugins"), 0;
                        Module.freePreloadedMediaOnUse && (Module.preloadedImages[i] = null)
                    }
                    var a = SDL.makeSurface(o.width, o.height, 0, !1, "load:" + i),
                        u = SDL.surfaces[a];
                    if (u.ctx.globalCompositeOperation = "copy", o.rawData) {
                        var f = u.ctx.getImageData(0, 0, u.width, u.height);
                        if (4 == o.bpp) f.data.set(HEAPU8.subarray(o.data, o.data + o.size));
                        else if (3 == o.bpp)
                            for (var l = o.size / 3, s = f.data, c = o.data, d = 0, _ = 0; _ < l; _++) s[d++] = HEAPU8[c++ >> 0], s[d++] = HEAPU8[c++ >> 0], s[d++] = HEAPU8[c++ >> 0], s[d++] = 255;
                        else if (2 == o.bpp)
                            for (l = o.size, s = f.data, c = o.data, d = 0, _ = 0; _ < l; _++) {
                                var h = HEAPU8[c++ >> 0],
                                    m = HEAPU8[c++ >> 0];
                                s[d++] = h, s[d++] = h, s[d++] = h, s[d++] = m
                            } else {
                            if (1 != o.bpp) return Module.printErr("cannot handle bpp " + o.bpp), 0;
                            for (l = o.size, s = f.data, c = o.data, d = 0, _ = 0; _ < l; _++) {
                                var p = HEAPU8[c++ >> 0];
                                s[d++] = p, s[d++] = p, s[d++] = p, s[d++] = 255
                            }
                        }
                        u.ctx.putImageData(f, 0, 0)
                    } else u.ctx.drawImage(o, 0, 0, o.width, o.height, 0, 0, o.width, o.height);
                    return u.ctx.globalCompositeOperation = "source-over", _SDL_LockSurface(a), u.locked--, SDL.GL && (u.canvas = u.ctx = null), a
                } finally {
                    t()
                }
            }

            function _SDL_RWFromFile(e, r) {
                var t = SDL.rwops.length,
                    n = Pointer_stringify(e);
                return SDL.rwops.push({
                    filename: n,
                    mimetype: Browser.getMimetype(n)
                }), t
            }

            function _IMG_Load(e) {
                return _IMG_Load_RW(_SDL_RWFromFile(e), 1)
            }

            function _SDL_UpperBlitScaled(e, r, t, n) {
                return SDL.blitSurface(e, r, t, n, !0)
            }

            function _SDL_UpperBlit(e, r, t, n) {
                return SDL.blitSurface(e, r, t, n, !1)
            }

            function _SDL_GetTicks() {
                return Date.now() - SDL.startTime | 0
            }
            var SDL = {
                defaults: {
                    width: 320,
                    height: 200,
                    copyOnLock: !0,
                    discardOnLock: !1,
                    opaqueFrontBuffer: !0
                },
                version: null,
                surfaces: {},
                canvasPool: [],
                events: [],
                fonts: [null],
                audios: [null],
                rwops: [null],
                music: {
                    audio: null,
                    volume: 1
                },
                mixerFrequency: 22050,
                mixerFormat: 32784,
                mixerNumChannels: 2,
                mixerChunkSize: 1024,
                channelMinimumNumber: 0,
                GL: !1,
                glAttributes: {
                    0: 3,
                    1: 3,
                    2: 2,
                    3: 0,
                    4: 0,
                    5: 1,
                    6: 16,
                    7: 0,
                    8: 0,
                    9: 0,
                    10: 0,
                    11: 0,
                    12: 0,
                    13: 0,
                    14: 0,
                    15: 1,
                    16: 0,
                    17: 0,
                    18: 0
                },
                keyboardState: null,
                keyboardMap: {},
                canRequestFullscreen: !1,
                isRequestingFullscreen: !1,
                textInput: !1,
                startTime: null,
                initFlags: 0,
                buttonState: 0,
                modState: 0,
                DOMButtons: [0, 0, 0],
                DOMEventToSDLEvent: {},
                TOUCH_DEFAULT_ID: 0,
                eventHandler: null,
                eventHandlerContext: null,
                eventHandlerTemp: 0,
                keyCodes: {
                    16: 1249,
                    17: 1248,
                    18: 1250,
                    20: 1081,
                    33: 1099,
                    34: 1102,
                    35: 1101,
                    36: 1098,
                    37: 1104,
                    38: 1106,
                    39: 1103,
                    40: 1105,
                    44: 316,
                    45: 1097,
                    46: 127,
                    91: 1251,
                    93: 1125,
                    96: 1122,
                    97: 1113,
                    98: 1114,
                    99: 1115,
                    100: 1116,
                    101: 1117,
                    102: 1118,
                    103: 1119,
                    104: 1120,
                    105: 1121,
                    106: 1109,
                    107: 1111,
                    109: 1110,
                    110: 1123,
                    111: 1108,
                    112: 1082,
                    113: 1083,
                    114: 1084,
                    115: 1085,
                    116: 1086,
                    117: 1087,
                    118: 1088,
                    119: 1089,
                    120: 1090,
                    121: 1091,
                    122: 1092,
                    123: 1093,
                    124: 1128,
                    125: 1129,
                    126: 1130,
                    127: 1131,
                    128: 1132,
                    129: 1133,
                    130: 1134,
                    131: 1135,
                    132: 1136,
                    133: 1137,
                    134: 1138,
                    135: 1139,
                    144: 1107,
                    160: 94,
                    161: 33,
                    162: 34,
                    163: 35,
                    164: 36,
                    165: 37,
                    166: 38,
                    167: 95,
                    168: 40,
                    169: 41,
                    170: 42,
                    171: 43,
                    172: 124,
                    173: 45,
                    174: 123,
                    175: 125,
                    176: 126,
                    181: 127,
                    182: 129,
                    183: 128,
                    188: 44,
                    190: 46,
                    191: 47,
                    192: 96,
                    219: 91,
                    220: 92,
                    221: 93,
                    222: 39,
                    224: 1251
                },
                scanCodes: {
                    8: 42,
                    9: 43,
                    13: 40,
                    27: 41,
                    32: 44,
                    35: 204,
                    39: 53,
                    44: 54,
                    46: 55,
                    47: 56,
                    48: 39,
                    49: 30,
                    50: 31,
                    51: 32,
                    52: 33,
                    53: 34,
                    54: 35,
                    55: 36,
                    56: 37,
                    57: 38,
                    58: 203,
                    59: 51,
                    61: 46,
                    91: 47,
                    92: 49,
                    93: 48,
                    96: 52,
                    97: 4,
                    98: 5,
                    99: 6,
                    100: 7,
                    101: 8,
                    102: 9,
                    103: 10,
                    104: 11,
                    105: 12,
                    106: 13,
                    107: 14,
                    108: 15,
                    109: 16,
                    110: 17,
                    111: 18,
                    112: 19,
                    113: 20,
                    114: 21,
                    115: 22,
                    116: 23,
                    117: 24,
                    118: 25,
                    119: 26,
                    120: 27,
                    121: 28,
                    122: 29,
                    127: 76,
                    305: 224,
                    308: 226,
                    316: 70
                },
                loadRect: function(e) {
                    return {
                        x: HEAP32[e + 0 >> 2],
                        y: HEAP32[e + 4 >> 2],
                        w: HEAP32[e + 8 >> 2],
                        h: HEAP32[e + 12 >> 2]
                    }
                },
                updateRect: function(e, r) {
                    HEAP32[e >> 2] = r.x, HEAP32[e + 4 >> 2] = r.y, HEAP32[e + 8 >> 2] = r.w, HEAP32[e + 12 >> 2] = r.h
                },
                intersectionOfRects: function(e, r) {
                    var t = Math.max(e.x, r.x),
                        n = Math.max(e.y, r.y),
                        i = Math.min(e.x + e.w, r.x + r.w),
                        o = Math.min(e.y + e.h, r.y + r.h);
                    return {
                        x: t,
                        y: n,
                        w: Math.max(t, i) - t,
                        h: Math.max(n, o) - n
                    }
                },
                checkPixelFormat: function(e) {},
                loadColorToCSSRGB: function(e) {
                    var r = HEAP32[e >> 2];
                    return "rgb(" + (255 & r) + "," + (r >> 8 & 255) + "," + (r >> 16 & 255) + ")"
                },
                loadColorToCSSRGBA: function(e) {
                    var r = HEAP32[e >> 2];
                    return "rgba(" + (255 & r) + "," + (r >> 8 & 255) + "," + (r >> 16 & 255) + "," + (r >> 24 & 255) / 255 + ")"
                },
                translateColorToCSSRGBA: function(e) {
                    return "rgba(" + (255 & e) + "," + (e >> 8 & 255) + "," + (e >> 16 & 255) + "," + (e >>> 24) / 255 + ")"
                },
                translateRGBAToCSSRGBA: function(e, r, t, n) {
                    return "rgba(" + (255 & e) + "," + (255 & r) + "," + (255 & t) + "," + (255 & n) / 255 + ")"
                },
                translateRGBAToColor: function(e, r, t, n) {
                    return e | r << 8 | t << 16 | n << 24
                },
                makeSurface: function(e, r, t, n, i, o, a, u, f) {
                    var l, s = 1 & (t = t || 0),
                        c = 2097152 & t,
                        d = 67108864 & t,
                        _ = _malloc(60),
                        h = _malloc(44),
                        m = c ? 1 : 4,
                        p = 0;
                    s || d || (p = _malloc(e * r * 4)), HEAP32[_ >> 2] = t, HEAP32[_ + 4 >> 2] = h, HEAP32[_ + 8 >> 2] = e, HEAP32[_ + 12 >> 2] = r, HEAP32[_ + 16 >> 2] = e * m, HEAP32[_ + 20 >> 2] = p, HEAP32[_ + 36 >> 2] = 0, HEAP32[_ + 40 >> 2] = 0, HEAP32[_ + 44 >> 2] = Module.canvas.width, HEAP32[_ + 48 >> 2] = Module.canvas.height, HEAP32[_ + 56 >> 2] = 1, HEAP32[h >> 2] = -2042224636, HEAP32[h + 4 >> 2] = 0, HEAP8[h + 8 >> 0] = 8 * m, HEAP8[h + 9 >> 0] = m, HEAP32[h + 12 >> 2] = o || 255, HEAP32[h + 16 >> 2] = a || 65280, HEAP32[h + 20 >> 2] = u || 16711680, HEAP32[h + 24 >> 2] = f || 4278190080, SDL.GL = SDL.GL || d, n ? l = Module.canvas : ((l = SDL.canvasPool.length > 0 ? SDL.canvasPool.pop() : document.createElement("canvas")).width = e, l.height = r);
                    var v = {
                            antialias: 0 != SDL.glAttributes[13] && SDL.glAttributes[14] > 1,
                            depth: SDL.glAttributes[6] > 0,
                            stencil: SDL.glAttributes[7] > 0,
                            alpha: SDL.glAttributes[3] > 0
                        },
                        b = Browser.createContext(l, d, n, v);
                    return SDL.surfaces[_] = {
                        width: e,
                        height: r,
                        canvas: l,
                        ctx: b,
                        surf: _,
                        buffer: p,
                        pixelFormat: h,
                        alpha: 255,
                        flags: t,
                        locked: 0,
                        usePageCanvas: n,
                        source: i,
                        isFlagSet: function(e) {
                            return t & e
                        }
                    }, _
                },
                copyIndexedColorData: function(e, r, t, n, i) {
                    if (e.colors) {
                        var o = Module.canvas.width,
                            a = Module.canvas.height,
                            u = r || 0,
                            f = t || 0,
                            l = (n || o - u) + u,
                            s = (i || a - f) + f,
                            c = e.buffer;
                        e.image.data32 || (e.image.data32 = new Uint32Array(e.image.data.buffer));
                        for (var d = e.image.data32, _ = e.colors32, h = f; h < s; ++h)
                            for (var m = h * o, p = u; p < l; ++p) d[m + p] = _[HEAPU8[c + m + p >> 0]]
                    }
                },
                freeSurface: function(e) {
                    var r = e + 56,
                        t = HEAP32[r >> 2];
                    if (t > 1) HEAP32[r >> 2] = t - 1;
                    else {
                        var n = SDL.surfaces[e];
                        !n.usePageCanvas && n.canvas && SDL.canvasPool.push(n.canvas), n.buffer && _free(n.buffer), _free(n.pixelFormat), _free(e), SDL.surfaces[e] = null, e === SDL.screen && (SDL.screen = null)
                    }
                },
                blitSurface__deps: ["SDL_LockSurface"],
                blitSurface: function(e, r, t, n, i) {
                    var o, a, u, f, l = SDL.surfaces[e],
                        s = SDL.surfaces[t];
                    if (o = r ? SDL.loadRect(r) : {
                        x: 0,
                        y: 0,
                        w: l.width,
                        h: l.height
                    }, a = n ? SDL.loadRect(n) : {
                        x: 0,
                        y: 0,
                        w: l.width,
                        h: l.height
                    }, s.clipRect) {
                        var c = i && 0 !== o.w ? o.w / a.w : 1,
                            d = i && 0 !== o.h ? o.h / a.h : 1;
                        a = SDL.intersectionOfRects(s.clipRect, a), o.w = a.w * c, o.h = a.h * d, n && SDL.updateRect(n, a)
                    }
                    if (i ? (u = a.w, f = a.h) : (u = o.w, f = o.h), 0 === o.w || 0 === o.h || 0 === u || 0 === f) return 0;
                    var _ = s.ctx.globalAlpha;
                    return s.ctx.globalAlpha = l.alpha / 255, s.ctx.drawImage(l.canvas, o.x, o.y, o.w, o.h, a.x, a.y, u, f), s.ctx.globalAlpha = _, t != SDL.screen && (Runtime.warnOnce("WARNING: copying canvas data to memory for compatibility"), _SDL_LockSurface(t), s.locked--), 0
                },
                downFingers: {},
                savedKeydown: null,
                // receiveEvent: function(e) {
                //     function r() {
                //         for (var e in SDL.keyboardMap) SDL.events.push({
                //             type: "keyup",
                //             keyCode: SDL.keyboardMap[e]
                //         })
                //     }
                //     switch (e.type) {
                //         case "touchstart":
                //         case "touchmove":
                //             e.preventDefault();
                //             var t = [];
                //             if ("touchstart" === e.type)
                //                 for (var n = 0; n < e.touches.length; n++) {
                //                     var i = e.touches[n];
                //                     1 != SDL.downFingers[i.identifier] && (SDL.downFingers[i.identifier] = !0, t.push(i))
                //                 } else t = e.touches;
                //             var o = t[0];
                //             if (o) {
                //                 var a;
                //                 switch ("touchstart" == e.type && (SDL.DOMButtons[0] = 1), e.type) {
                //                     case "touchstart":
                //                         a = "mousedown";
                //                         break;
                //                     case "touchmove":
                //                         a = "mousemove"
                //                 }
                //                 var u = {
                //                     type: a,
                //                     button: 0,
                //                     pageX: o.clientX,
                //                     pageY: o.clientY
                //                 };
                //                 SDL.events.push(u)
                //             }
                //             for (n = 0; n < t.length; n++) i = t[n], SDL.events.push({
                //                 type: e.type,
                //                 touch: i
                //             });
                //             break;
                //         case "touchend":
                //             for (e.preventDefault(), n = 0; n < e.changedTouches.length; n++) i = e.changedTouches[n], !0 === SDL.downFingers[i.identifier] && delete SDL.downFingers[i.identifier];
                //             for (u = {
                //                 type: "mouseup",
                //                 button: 0,
                //                 pageX: e.changedTouches[0].clientX,
                //                 pageY: e.changedTouches[0].clientY
                //             }, SDL.DOMButtons[0] = 0, SDL.events.push(u), n = 0; n < e.changedTouches.length; n++) i = e.changedTouches[n], SDL.events.push({
                //                 type: "touchend",
                //                 touch: i
                //             });
                //             break;
                //         case "DOMMouseScroll":
                //         case "mousewheel":
                //         case "wheel":
                //             var f = -Browser.getMouseWheelDelta(e),
                //                 l = (f = 0 == f ? 0 : f > 0 ? Math.max(f, 1) : Math.min(f, -1)) > 0 ? 3 : 4;
                //             SDL.events.push({
                //                 type: "mousedown",
                //                 button: l,
                //                 pageX: e.pageX,
                //                 pageY: e.pageY
                //             }), SDL.events.push({
                //                 type: "mouseup",
                //                 button: l,
                //                 pageX: e.pageX,
                //                 pageY: e.pageY
                //             }), SDL.events.push({
                //                 type: "wheel",
                //                 deltaX: 0,
                //                 deltaY: f
                //             }), e.preventDefault();
                //             break;
                //         case "mousemove":
                //             if (1 === SDL.DOMButtons[0] && SDL.events.push({
                //                 type: "touchmove",
                //                 touch: {
                //                     identifier: 0,
                //                     deviceID: -1,
                //                     pageX: e.pageX,
                //                     pageY: e.pageY
                //                 }
                //             }), Browser.pointerLock && ("mozMovementX" in e && (e.movementX = e.mozMovementX, e.movementY = e.mozMovementY), 0 == e.movementX && 0 == e.movementY)) return void e.preventDefault();
                //         case "keydown":
                //         case "keyup":
                //         case "keypress":
                //         case "mousedown":
                //         case "mouseup":
                //             if ("keydown" === e.type && (SDL.unicode || SDL.textInput) && 8 !== e.keyCode && 9 !== e.keyCode || e.preventDefault(), "mousedown" == e.type) SDL.DOMButtons[e.button] = 1, SDL.events.push({
                //                 type: "touchstart",
                //                 touch: {
                //                     identifier: 0,
                //                     deviceID: -1,
                //                     pageX: e.pageX,
                //                     pageY: e.pageY
                //                 }
                //             });
                //             else if ("mouseup" == e.type) {
                //                 if (!SDL.DOMButtons[e.button]) return;
                //                 SDL.events.push({
                //                     type: "touchend",
                //                     touch: {
                //                         identifier: 0,
                //                         deviceID: -1,
                //                         pageX: e.pageX,
                //                         pageY: e.pageY
                //                     }
                //                 }), SDL.DOMButtons[e.button] = 0
                //             }
                //             "keydown" === e.type || "mousedown" === e.type ? SDL.canRequestFullscreen = !0 : "keyup" !== e.type && "mouseup" !== e.type || (SDL.isRequestingFullscreen && (Module.requestFullscreen(!0, !0), SDL.isRequestingFullscreen = !1), SDL.canRequestFullscreen = !1), "keypress" === e.type && SDL.savedKeydown ? (SDL.savedKeydown.keypressCharCode = e.charCode, SDL.savedKeydown = null) : "keydown" === e.type && (SDL.savedKeydown = e), ("keypress" !== e.type || SDL.textInput) && SDL.events.push(e);
                //             break;
                //         case "mouseout":
                //             for (n = 0; n < 3; n++) SDL.DOMButtons[n] && (SDL.events.push({
                //                 type: "mouseup",
                //                 button: n,
                //                 pageX: e.pageX,
                //                 pageY: e.pageY
                //             }), SDL.DOMButtons[n] = 0);
                //             e.preventDefault();
                //             break;
                //         case "focus":
                //             SDL.events.push(e), e.preventDefault();
                //             break;
                //         case "blur":
                //             SDL.events.push(e), r(), e.preventDefault();
                //             break;
                //         case "visibilitychange":
                //             SDL.events.push({
                //                 type: "visibilitychange",
                //                 visible: !document.hidden
                //             }), r(), e.preventDefault();
                //             break;
                //         case "unload":
                //             return void(Browser.mainLoop.runner && (SDL.events.push(e), Browser.mainLoop.runner()));
                //         case "resize":
                //             SDL.events.push(e), e.preventDefault && e.preventDefault()
                //     }
                //     SDL.events.length >= 1e4 && (Module.printErr("SDL event queue full, dropping events"), SDL.events = SDL.events.slice(0, 1e4)), SDL.flushEventsToHandler()
                // },
                // lookupKeyCodeForEvent: function(e) {
                //     var r = e.keyCode;
                //     return r >= 65 && r <= 90 ? r += 32 : (r = SDL.keyCodes[e.keyCode] || e.keyCode, e.location === KeyboardEvent.DOM_KEY_LOCATION_RIGHT && r >= 1248 && r <= 1251 && (r += 4)), r
                // },
                // handleEvent: function(e) {
                //     if (!e.handled) switch (e.handled = !0, e.type) {
                //         case "touchstart":
                //         case "touchend":
                //         case "touchmove":
                //             Browser.calculateMouseEvent(e);
                //             break;
                //         case "keydown":
                //         case "keyup":
                //             var r = "keydown" === e.type,
                //                 t = SDL.lookupKeyCodeForEvent(e);
                //             HEAP8[SDL.keyboardState + t >> 0] = r, SDL.modState = (HEAP8[SDL.keyboardState + 1248 >> 0] ? 64 : 0) | (HEAP8[SDL.keyboardState + 1249 >> 0] ? 1 : 0) | (HEAP8[SDL.keyboardState + 1250 >> 0] ? 256 : 0) | (HEAP8[SDL.keyboardState + 1252 >> 0] ? 128 : 0) | (HEAP8[SDL.keyboardState + 1253 >> 0] ? 2 : 0) | (HEAP8[SDL.keyboardState + 1254 >> 0] ? 512 : 0), r ? SDL.keyboardMap[t] = e.keyCode : delete SDL.keyboardMap[t];
                //             break;
                //         case "mousedown":
                //         case "mouseup":
                //             "mousedown" == e.type ? SDL.buttonState |= 1 << e.button : "mouseup" == e.type && (SDL.buttonState &= ~(1 << e.button));
                //         case "mousemove":
                //             Browser.calculateMouseEvent(e)
                //     }
                // },
                // flushEventsToHandler: function() {
                //     if (SDL.eventHandler)
                //         for (; SDL.pollEvent(SDL.eventHandlerTemp);) Module.dynCall_iii(SDL.eventHandler, SDL.eventHandlerContext, SDL.eventHandlerTemp)
                // },
                // pollEvent: function(e) {
                //     if (512 & SDL.initFlags && SDL.joystickEventState && SDL.queryJoysticks(), e) {
                //         for (; SDL.events.length > 0;)
                //             if (!1 !== SDL.makeCEvent(SDL.events.shift(), e)) return 1;
                //         return 0
                //     }
                //     return SDL.events.length > 0
                // },
                makeCEvent: function(e, r) {
                    if ("number" == typeof e) return _memcpy(r, e, 28), void _free(e);
                    switch (SDL.handleEvent(e), e.type) {
                        case "keydown":
                        case "keyup":
                            var t, n = "keydown" === e.type,
                                i = SDL.lookupKeyCodeForEvent(e);
                            t = i >= 1024 ? i - 1024 : SDL.scanCodes[i] || i, HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP8[r + 8 >> 0] = n ? 1 : 0, HEAP8[r + 9 >> 0] = 0, HEAP32[r + 12 >> 2] = t, HEAP32[r + 16 >> 2] = i, HEAP16[r + 20 >> 1] = SDL.modState, HEAP32[r + 24 >> 2] = e.keypressCharCode || i;
                            break;
                        case "keypress":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type];
                            for (var o = intArrayFromString(String.fromCharCode(e.charCode)), a = 0; a < o.length; ++a) HEAP8[r + (8 + a) >> 0] = o[a];
                            break;
                        case "mousedown":
                        case "mouseup":
                        case "mousemove":
                            "mousemove" != e.type ? (n = "mousedown" === e.type, HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = 0, HEAP32[r + 8 >> 2] = 0, HEAP32[r + 12 >> 2] = 0, HEAP8[r + 16 >> 0] = e.button + 1, HEAP8[r + 17 >> 0] = n ? 1 : 0, HEAP32[r + 20 >> 2] = Browser.mouseX, HEAP32[r + 24 >> 2] = Browser.mouseY) : (HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = 0, HEAP32[r + 8 >> 2] = 0, HEAP32[r + 12 >> 2] = 0, HEAP32[r + 16 >> 2] = SDL.buttonState, HEAP32[r + 20 >> 2] = Browser.mouseX, HEAP32[r + 24 >> 2] = Browser.mouseY, HEAP32[r + 28 >> 2] = Browser.mouseMovementX, HEAP32[r + 32 >> 2] = Browser.mouseMovementY);
                            break;
                        case "wheel":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 16 >> 2] = e.deltaX, HEAP32[r + 20 >> 2] = e.deltaY;
                            break;
                        case "touchstart":
                        case "touchend":
                        case "touchmove":
                            var u = e.touch;
                            if (!Browser.touches[u.identifier]) break;
                            var f = Module.canvas.width,
                                l = Module.canvas.height,
                                s = Browser.touches[u.identifier].x / f,
                                c = Browser.touches[u.identifier].y / l,
                                d = s - Browser.lastTouches[u.identifier].x / f,
                                _ = c - Browser.lastTouches[u.identifier].y / l;
                            if (void 0 === u.deviceID && (u.deviceID = SDL.TOUCH_DEFAULT_ID), 0 === d && 0 === _ && "touchmove" === e.type) return !1;
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = _SDL_GetTicks(), tempI64 = [u.deviceID >>> 0, (tempDouble = u.deviceID, +Math_abs(tempDouble) >= 1 ? tempDouble > 0 ? (0 | Math_min(+Math_floor(tempDouble / 4294967296), 4294967295)) >>> 0 : ~~+Math_ceil((tempDouble - +(~~tempDouble >>> 0)) / 4294967296) >>> 0 : 0)], HEAP32[r + 8 >> 2] = tempI64[0], HEAP32[r + 12 >> 2] = tempI64[1], tempI64 = [u.identifier >>> 0, (tempDouble = u.identifier, +Math_abs(tempDouble) >= 1 ? tempDouble > 0 ? (0 | Math_min(+Math_floor(tempDouble / 4294967296), 4294967295)) >>> 0 : ~~+Math_ceil((tempDouble - +(~~tempDouble >>> 0)) / 4294967296) >>> 0 : 0)], HEAP32[r + 16 >> 2] = tempI64[0], HEAP32[r + 20 >> 2] = tempI64[1], HEAPF32[r + 24 >> 2] = s, HEAPF32[r + 28 >> 2] = c, HEAPF32[r + 32 >> 2] = d, HEAPF32[r + 36 >> 2] = _, void 0 !== u.force ? HEAPF32[r + 40 >> 2] = u.force : HEAPF32[r + 40 >> 2] = "touchend" == e.type ? 0 : 1;
                            break;
                        case "unload":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type];
                            break;
                        case "resize":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = e.w, HEAP32[r + 8 >> 2] = e.h;
                            break;
                        case "joystick_button_up":
                        case "joystick_button_down":
                            var h = "joystick_button_up" === e.type ? 0 : 1;
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP8[r + 4 >> 0] = e.index, HEAP8[r + 5 >> 0] = e.button, HEAP8[r + 6 >> 0] = h;
                            break;
                        case "joystick_axis_motion":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP8[r + 4 >> 0] = e.index, HEAP8[r + 5 >> 0] = e.axis, HEAP32[r + 8 >> 2] = SDL.joystickAxisValueConversion(e.value);
                            break;
                        case "focus":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = 0, HEAP8[r + 8 >> 0] = 12;
                            break;
                        case "blur":
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = 0, HEAP8[r + 8 >> 0] = 13;
                            break;
                        case "visibilitychange":
                            var m = e.visible ? 1 : 2;
                            HEAP32[r >> 2] = SDL.DOMEventToSDLEvent[e.type], HEAP32[r + 4 >> 2] = 0, HEAP8[r + 8 >> 0] = m;
                            break;
                        default:
                            throw "Unhandled SDL event: " + e.type
                    }
                },
                makeFontString: function(e, r) {
                    return "'" != r.charAt(0) && '"' != r.charAt(0) && (r = '"' + r + '"'), e + "px " + r + ", serif"
                },
                estimateTextWidth: function(e, r) {
                    var t = e.size,
                        n = SDL.makeFontString(t, e.name),
                        i = SDL.ttfContext;
                    i.save(), i.font = n;
                    var o = 0 | i.measureText(r).width;
                    return i.restore(), o
                },
                allocateChannels: function(e) {
                    if (!(SDL.numChannels && SDL.numChannels >= e && 0 != e)) {
                        SDL.numChannels = e, SDL.channels = [];
                        for (var r = 0; r < e; r++) SDL.channels[r] = {
                            audio: null,
                            volume: 1
                        }
                    }
                },
                setGetVolume: function(r, t) {
                    if (!r) return 0;
                    var n = 128 * r.volume;
                    if (-1 != t && (r.volume = Math.min(Math.max(t, 0), 128) / 128, r.audio)) try {
                        r.audio.volume = r.volume, r.audio.webAudioGainNode && (r.audio.webAudioGainNode.gain.value = r.volume)
                    } catch (e) {
                        Module.printErr("setGetVolume failed to set audio volume: " + e)
                    }
                    return n
                },
                setPannerPosition: function(e, r, t, n) {
                    e && e.audio && e.audio.webAudioPannerNode && e.audio.webAudioPannerNode.setPosition(r, t, n)
                },
                playWebAudio: function(r) {
                    if (r && !r.webAudioNode && SDL.webAudioAvailable()) try {
                        var t = r.resource.webAudio;
                        if (r.paused = !1, !t.decodedBuffer) return void 0 === t.onDecodeComplete && abort("Cannot play back audio object that was not loaded"), void t.onDecodeComplete.push(function() {
                            r.paused || SDL.playWebAudio(r)
                        });
                        r.webAudioNode = SDL.audioContext.createBufferSource(), r.webAudioNode.buffer = t.decodedBuffer, r.webAudioNode.loop = r.loop, r.webAudioNode.onended = function() {
                            r.onended()
                        }, r.webAudioPannerNode = SDL.audioContext.createPanner(), r.webAudioPannerNode.setPosition(0, 0, -.5), r.webAudioPannerNode.panningModel = "equalpower", r.webAudioGainNode = SDL.audioContext.createGain(), r.webAudioGainNode.gain.value = r.volume, r.webAudioNode.connect(r.webAudioPannerNode), r.webAudioPannerNode.connect(r.webAudioGainNode), r.webAudioGainNode.connect(SDL.audioContext.destination), r.webAudioNode.start(0, r.currentPosition), r.startTime = SDL.audioContext.currentTime - r.currentPosition
                    } catch (e) {
                        Module.printErr("playWebAudio failed: " + e)
                    }
                },
                pauseWebAudio: function(r) {
                    if (r) {
                        if (r.webAudioNode) try {
                            r.currentPosition = (SDL.audioContext.currentTime - r.startTime) % r.resource.webAudio.decodedBuffer.duration, r.webAudioNode.onended = void 0, r.webAudioNode.stop(0), r.webAudioNode = void 0
                        } catch (e) {
                            Module.printErr("pauseWebAudio failed: " + e)
                        }
                        r.paused = !0
                    }
                },
                openAudioContext: function() {
                    SDL.audioContext || ("undefined" != typeof AudioContext ? SDL.audioContext = new AudioContext : "undefined" != typeof webkitAudioContext && (SDL.audioContext = new webkitAudioContext))
                },
                webAudioAvailable: function() {
                    return !!SDL.audioContext
                },
                fillWebAudioBufferFromHeap: function(e, r, t) {
                    for (var n = SDL.audio.channels, i = 0; i < n; ++i) {
                        var o = t.getChannelData(i);
                        if (o.length != r) throw "Web Audio output buffer length mismatch! Destination size: " + o.length + " samples vs expected " + r + " samples!";
                        if (32784 == SDL.audio.format)
                            for (var a = 0; a < r; ++a) o[a] = HEAP16[e + 2 * (a * n + i) >> 1] / 32768;
                        else if (8 == SDL.audio.format)
                            for (a = 0; a < r; ++a) {
                                var u = HEAP8[e + (a * n + i) >> 0];
                                o[a] = (u >= 0 ? u - 128 : u + 128) / 128
                            }
                    }
                },
                debugSurface: function(e) {
                    console.log("dumping surface " + [e.surf, e.source, e.width, e.height]);
                    for (var r = e.ctx.getImageData(0, 0, e.width, e.height).data, t = Math.min(e.width, e.height), n = 0; n < t; n++) console.log("   diagonal " + n + ":" + [r[n * e.width * 4 + 4 * n + 0], r[n * e.width * 4 + 4 * n + 1], r[n * e.width * 4 + 4 * n + 2], r[n * e.width * 4 + 4 * n + 3]])
                },
                joystickEventState: 1,
                lastJoystickState: {},
                joystickNamePool: {},
                recordJoystickState: function(e, r) {
                    for (var t = new Array(r.buttons.length), n = 0; n < r.buttons.length; n++) t[n] = SDL.getJoystickButtonState(r.buttons[n]);
                    SDL.lastJoystickState[e] = {
                        buttons: t,
                        axes: r.axes.slice(0),
                        timestamp: r.timestamp,
                        index: r.index,
                        id: r.id
                    }
                },
                getJoystickButtonState: function(e) {
                    return "object" == typeof e ? e.pressed : e > 0
                },
                queryJoysticks: function() {
                    for (var e in SDL.lastJoystickState) {
                        var r = SDL.getGamepad(e - 1),
                            t = SDL.lastJoystickState[e];
                        if (void 0 === r) return;
                        if ("number" != typeof r.timestamp || r.timestamp !== t.timestamp) {
                            var n;
                            for (n = 0; n < r.buttons.length; n++) {
                                var i = SDL.getJoystickButtonState(r.buttons[n]);
                                i !== t.buttons[n] && SDL.events.push({
                                    type: i ? "joystick_button_down" : "joystick_button_up",
                                    joystick: e,
                                    index: e - 1,
                                    button: n
                                })
                            }
                            for (n = 0; n < r.axes.length; n++) r.axes[n] !== t.axes[n] && SDL.events.push({
                                type: "joystick_axis_motion",
                                joystick: e,
                                index: e - 1,
                                axis: n,
                                value: r.axes[n]
                            });
                            SDL.recordJoystickState(e, r)
                        }
                    }
                },
                joystickAxisValueConversion: function(e) {
                    return e = Math.min(1, Math.max(e, -1)), Math.ceil(32767.5 * (e + 1) - 32768)
                },
                getGamepads: function() {
                    var e = navigator.getGamepads || navigator.webkitGamepads || navigator.mozGamepads || navigator.gamepads || navigator.webkitGetGamepads;
                    return void 0 !== e ? e.apply(navigator) : []
                },
                getGamepad: function(e) {
                    var r = SDL.getGamepads();
                    return r.length > e && e >= 0 ? r[e] : null
                }
            };

            function _SDL_SetVideoMode(e, r, t, n) {
                ["touchstart", "touchend", "touchmove", "mousedown", "mouseup", "mousemove", "DOMMouseScroll", "mousewheel", "wheel", "mouseout"].forEach(function(e) {
                    Module.canvas.addEventListener(e, SDL.receiveEvent, !0)
                });
                var i = Module.canvas;
                return 0 == e && 0 == r && (e = i.width, r = i.height), SDL.addedResizeListener || (SDL.addedResizeListener = !0, Browser.resizeListeners.push(function(e, r) {
                    SDL.settingVideoMode || SDL.receiveEvent({
                        type: "resize",
                        w: e,
                        h: r
                    })
                })), e === i.width && r === i.height || (SDL.settingVideoMode = !0, Browser.setCanvasSize(e, r), SDL.settingVideoMode = !1), SDL.screen && SDL.freeSurface(SDL.screen), SDL.GL && (n |= 67108864), SDL.screen = SDL.makeSurface(e, r, n, !0, "screen"), SDL.screen
            }

            function ___lock() {}

            function ___unlock() {}

            function _SDL_UnlockSurface(e) {
                var r = SDL.surfaces[e];
                if (r.locked && !(--r.locked > 0)) {
                    if (r.isFlagSet(2097152)) SDL.copyIndexedColorData(r);
                    else if (r.colors)
                        for (var t = Module.canvas.width, n = Module.canvas.height, i = r.buffer, o = (c = r.image.data, r.colors), a = 0; a < n; a++) {
                            for (var u = a * t * 4, f = 0; f < t; f++) {
                                m = 4 * HEAPU8[i++ >> 0];
                                var l = u + 4 * f;
                                c[l] = o[m], c[l + 1] = o[m + 1], c[l + 2] = o[m + 2]
                            }
                            i += 3 * t
                        } else {
                        var s, c = r.image.data,
                            d = r.buffer >> 2,
                            _ = 0,
                            h = e == SDL.screen;
                        if ("undefined" != typeof CanvasPixelArray && c instanceof CanvasPixelArray)
                            for (s = c.length; _ < s;) {
                                var m = HEAP32[d];
                                c[_] = 255 & m, c[_ + 1] = m >> 8 & 255, c[_ + 2] = m >> 16 & 255, c[_ + 3] = h ? 255 : m >> 24 & 255, d++, _ += 4
                            } else {
                            var p = new Uint32Array(c.buffer);
                            if (h && SDL.defaults.opaqueFrontBuffer) {
                                s = p.length, p.set(HEAP32.subarray(d, d + s));
                                var v = new Uint8Array(c.buffer),
                                    b = 3,
                                    w = b + 4 * s;
                                if (s % 8 == 0)
                                    for (; b < w;) v[b] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, v[b = b + 4 | 0] = 255, b = b + 4 | 0;
                                else
                                    for (; b < w;) v[b] = 255, b = b + 4 | 0
                            } else p.set(HEAP32.subarray(d, d + p.length))
                        }
                    }
                    r.ctx.putImageData(r.image, 0, 0)
                }
            }

            function _SDL_Flip(e) {}

            function _pthread_mutex_init() {}

            function _SDL_CreateRGBSurface(e, r, t, n, i, o, a, u) {
                return SDL.makeSurface(r, t, e, !1, "CreateRGBSurface", i, o, a, u)
            }
            var SYSCALLS = {
                varargs: 0,
                get: function(e) {
                    return SYSCALLS.varargs += 4, HEAP32[SYSCALLS.varargs - 4 >> 2]
                },
                getStr: function() {
                    return Pointer_stringify(SYSCALLS.get())
                },
                get64: function() {
                    var e = SYSCALLS.get();
                    return SYSCALLS.get(), e
                },
                getZero: function() {}
            };

            function ___syscall54(r, t) {
                SYSCALLS.varargs = t;
                try {
                    return 0
                } catch (e) {
                    return "undefined" != typeof FS && e instanceof FS.ErrnoError || abort(e), -e.errno
                }
            }

            function _pthread_cond_init() {
                return 0
            }

            function _pthread_join() {}

            function _emscripten_memcpy_big(e, r, t) {
                return HEAPU8.set(HEAPU8.subarray(r, r + t), e), e
            }

            function ___syscall6(r, t) {
                SYSCALLS.varargs = t;
                try {
                    var n = SYSCALLS.getStreamFromFD();
                    return FS.close(n), 0
                } catch (e) {
                    return "undefined" != typeof FS && e instanceof FS.ErrnoError || abort(e), -e.errno
                }
            }
            var cttz_i8 = allocate([8, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 6, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 7, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 6, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0], "i8", ALLOC_STATIC),
                GLctx;

            function _SDL_Init(e) {
                if (SDL.startTime = Date.now(), SDL.initFlags = e, !Module.doNotCaptureKeyboard) {
                    var r = Module.keyboardListeningElement || document;
                    r.addEventListener("keydown", SDL.receiveEvent), r.addEventListener("keyup", SDL.receiveEvent), r.addEventListener("keypress", SDL.receiveEvent), window.addEventListener("focus", SDL.receiveEvent), window.addEventListener("blur", SDL.receiveEvent), document.addEventListener("visibilitychange", SDL.receiveEvent)
                }
                return 512 & e && addEventListener("gamepadconnected", function() {}), window.addEventListener("unload", SDL.receiveEvent), SDL.keyboardState = _malloc(65536), _memset(SDL.keyboardState, 0, 65536), SDL.DOMEventToSDLEvent.keydown = 768, SDL.DOMEventToSDLEvent.keyup = 769, SDL.DOMEventToSDLEvent.keypress = 771, SDL.DOMEventToSDLEvent.mousedown = 1025, SDL.DOMEventToSDLEvent.mouseup = 1026, SDL.DOMEventToSDLEvent.mousemove = 1024, SDL.DOMEventToSDLEvent.wheel = 1027, SDL.DOMEventToSDLEvent.touchstart = 1792, SDL.DOMEventToSDLEvent.touchend = 1793, SDL.DOMEventToSDLEvent.touchmove = 1794, SDL.DOMEventToSDLEvent.unload = 256, SDL.DOMEventToSDLEvent.resize = 28673, SDL.DOMEventToSDLEvent.visibilitychange = 512, SDL.DOMEventToSDLEvent.focus = 512, SDL.DOMEventToSDLEvent.blur = 512, SDL.DOMEventToSDLEvent.joystick_axis_motion = 1536, SDL.DOMEventToSDLEvent.joystick_button_down = 1539, SDL.DOMEventToSDLEvent.joystick_button_up = 1540, 0
            }

            function _pthread_cond_wait() {
                return 0
            }

            function _SDL_FreeSurface(e) {
                e && SDL.freeSurface(e)
            }

            function ___syscall140(r, t) {
                SYSCALLS.varargs = t;
                try {
                    var n = SYSCALLS.getStreamFromFD(),
                        i = (SYSCALLS.get(), SYSCALLS.get()),
                        o = SYSCALLS.get(),
                        a = SYSCALLS.get(),
                        u = i;
                    return FS.llseek(n, u, a), HEAP32[o >> 2] = n.position, n.getdents && 0 === u && 0 === a && (n.getdents = null), 0
                } catch (e) {
                    return "undefined" != typeof FS && e instanceof FS.ErrnoError || abort(e), -e.errno
                }
            }

            function ___syscall146(r, t) {
                SYSCALLS.varargs = t;
                try {
                    var n = SYSCALLS.get(),
                        i = SYSCALLS.get(),
                        o = SYSCALLS.get(),
                        a = 0;
                    ___syscall146.buffer || (___syscall146.buffers = [null, [],
                        []
                    ], ___syscall146.printChar = function(e, r) {
                        var t = ___syscall146.buffers[e];
                        0 === r || 10 === r ? ((1 === e ? Module.print : Module.printErr)(UTF8ArrayToString(t, 0)), t.length = 0) : t.push(r)
                    });
                    for (var u = 0; u < o; u++) {
                        for (var f = HEAP32[i + 8 * u >> 2], l = HEAP32[i + (8 * u + 4) >> 2], s = 0; s < l; s++) ___syscall146.printChar(n, HEAPU8[f + s]);
                        a += l
                    }
                    return a
                } catch (e) {
                    return "undefined" != typeof FS && e instanceof FS.ErrnoError || abort(e), -e.errno
                }
            }

            function invoke_iiii(r, t, n, i) {
                try {
                    return Module.dynCall_iiii(r, t, n, i)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_viiiii(r, t, n, i, o, a) {
                try {
                    Module.dynCall_viiiii(r, t, n, i, o, a)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_vi(r, t) {
                try {
                    Module.dynCall_vi(r, t)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_vii(r, t, n) {
                try {
                    Module.dynCall_vii(r, t, n)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_iiiiiii(r, t, n, i, o, a, u) {
                try {
                    return Module.dynCall_iiiiiii(r, t, n, i, o, a, u)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_ii(r, t) {
                try {
                    return Module.dynCall_ii(r, t)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_viii(r, t, n, i) {
                try {
                    Module.dynCall_viii(r, t, n, i)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_viiiiiiiii(r, t, n, i, o, a, u, f, l, s) {
                try {
                    Module.dynCall_viiiiiiiii(r, t, n, i, o, a, u, f, l, s)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_iiiii(r, t, n, i, o) {
                try {
                    return Module.dynCall_iiiii(r, t, n, i, o)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_viiiiii(r, t, n, i, o, a, u) {
                try {
                    Module.dynCall_viiiiii(r, t, n, i, o, a, u)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_iii(r, t, n) {
                try {
                    return Module.dynCall_iii(r, t, n)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }

            function invoke_viiii(r, t, n, i, o) {
                try {
                    Module.dynCall_viiii(r, t, n, i, o)
                } catch (e) {
                    if ("number" != typeof e && "longjmp" !== e) throw e;
                    Module.setThrew(1, 0)
                }
            }
            GL.init(), Module.requestFullScreen = function(e, r, t) {
                Module.printErr("Module.requestFullScreen is deprecated. Please call Module.requestFullscreen instead."), Module.requestFullScreen = Module.requestFullscreen, Browser.requestFullScreen(e, r, t)
            }, Module.requestFullscreen = function(e, r, t) {
                Browser.requestFullscreen(e, r, t)
            }, Module.requestAnimationFrame = function(e) {
                Browser.requestAnimationFrame(e)
            }, Module.setCanvasSize = function(e, r, t) {
                Browser.setCanvasSize(e, r, t)
            }, Module.pauseMainLoop = function() {
                Browser.mainLoop.pause()
            }, Module.resumeMainLoop = function() {
                Browser.mainLoop.resume()
            }, Module.getUserMedia = function() {
                Browser.getUserMedia()
            }, Module.createContext = function(e, r, t, n) {
                return Browser.createContext(e, r, t, n)
            }, _emscripten_get_now = ENVIRONMENT_IS_NODE ? function() {
                var e = process.hrtime();
                return 1e3 * e[0] + e[1] / 1e6
            } : "undefined" != typeof dateNow ? dateNow : "object" == typeof self && self.performance && "function" == typeof self.performance.now ? function() {
                return self.performance.now()
            } : "object" == typeof performance && "function" == typeof performance.now ? function() {
                return performance.now()
            } : Date.now, ___buildEnvironment(ENV), __ATEXIT__.push(function() {
                var e = Module._fflush;
                e && e(0);
                var r = ___syscall146.printChar;
                if (r) {
                    var t = ___syscall146.buffers;
                    t[1].length && r(1, 10), t[2].length && r(2, 10)
                }
            }), DYNAMICTOP_PTR = allocate(1, "i32", ALLOC_STATIC), STACK_BASE = STACKTOP = Runtime.alignMemory(STATICTOP), STACK_MAX = STACK_BASE + TOTAL_STACK, DYNAMIC_BASE = Runtime.alignMemory(STACK_MAX), HEAP32[DYNAMICTOP_PTR >> 2] = DYNAMIC_BASE, staticSealed = !0, Module.asmGlobalArg = {
                Math: Math,
                Int8Array: Int8Array,
                Int16Array: Int16Array,
                Int32Array: Int32Array,
                Uint8Array: Uint8Array,
                Uint16Array: Uint16Array,
                Uint32Array: Uint32Array,
                Float32Array: Float32Array,
                Float64Array: Float64Array,
                NaN: NaN,
                Infinity: 1 / 0,
                byteLength: byteLength
            }, Module.asmLibraryArg = {
                abort: abort,
                assert: assert,
                enlargeMemory: enlargeMemory,
                getTotalMemory: getTotalMemory,
                abortOnCannotGrowMemory: abortOnCannotGrowMemory,
                invoke_iiii: invoke_iiii,
                invoke_viiiii: invoke_viiiii,
                invoke_vi: invoke_vi,
                invoke_vii: invoke_vii,
                invoke_iiiiiii: invoke_iiiiiii,
                invoke_ii: invoke_ii,
                invoke_viii: invoke_viii,
                invoke_viiiiiiiii: invoke_viiiiiiiii,
                invoke_iiiii: invoke_iiiii,
                invoke_viiiiii: invoke_viiiiii,
                invoke_iii: invoke_iii,
                invoke_viiii: invoke_viiii,
                _pthread_cond_wait: _pthread_cond_wait,
                _putenv: _putenv,
                _pthread_join: _pthread_join,
                _SDL_SetVideoMode: _SDL_SetVideoMode,
                _IMG_Load: _IMG_Load,
                _TTF_FontHeight: _TTF_FontHeight,
                _SDL_CloseAudio: _SDL_CloseAudio,
                _emscripten_set_main_loop_timing: _emscripten_set_main_loop_timing,
                _SDL_GetTicks: _SDL_GetTicks,
                ___buildEnvironment: ___buildEnvironment,
                _pthread_cond_init: _pthread_cond_init,
                _SDL_LockSurface: _SDL_LockSurface,
                ___setErrNo: ___setErrNo,
                _pthread_cond_destroy: _pthread_cond_destroy,
                _SDL_PauseAudio: _SDL_PauseAudio,
                _SDL_Init: _SDL_Init,
                _SDL_FreeSurface: _SDL_FreeSurface,
                _Mix_PlayChannel: _Mix_PlayChannel,
                _TTF_RenderText_Solid: _TTF_RenderText_Solid,
                _Mix_LoadWAV_RW: _Mix_LoadWAV_RW,
                _IMG_Load_RW: _IMG_Load_RW,
                _Mix_PlayMusic: _Mix_PlayMusic,
                _emscripten_memcpy_big: _emscripten_memcpy_big,
                _pthread_cond_signal: _pthread_cond_signal,
                _pthread_mutex_destroy: _pthread_mutex_destroy,
                _SDL_UpperBlit: _SDL_UpperBlit,
                ___syscall54: ___syscall54,
                ___unlock: ___unlock,
                ___syscall140: ___syscall140,
                _pthread_create: _pthread_create,
                _emscripten_set_main_loop: _emscripten_set_main_loop,
                _emscripten_get_now: _emscripten_get_now,
                _SDL_CreateRGBSurface: _SDL_CreateRGBSurface,
                _TTF_SizeText: _TTF_SizeText,
                ___lock: ___lock,
                _SDL_UnlockSurface: _SDL_UnlockSurface,
                ___syscall6: ___syscall6,
                _Mix_FreeChunk: _Mix_FreeChunk,
                _Mix_HaltMusic: _Mix_HaltMusic,
                _getenv: _getenv,
                _SDL_Flip: _SDL_Flip,
                _SDL_FreeRW: _SDL_FreeRW,
                _SDL_UpperBlitScaled: _SDL_UpperBlitScaled,
                _pthread_mutex_init: _pthread_mutex_init,
                _SDL_RWFromConstMem: _SDL_RWFromConstMem,
                ___syscall146: ___syscall146,
                _SDL_RWFromFile: _SDL_RWFromFile,
                DYNAMICTOP_PTR: DYNAMICTOP_PTR,
                tempDoublePtr: tempDoublePtr,
                ABORT: ABORT,
                STACKTOP: STACKTOP,
                STACK_MAX: STACK_MAX,
                cttz_i8: cttz_i8
            };
            var asm = function(e, r, t) {
                    var n = e.Int8Array,
                        i = new n(t),
                        o = e.Int16Array,
                        a = new o(t),
                        u = e.Int32Array,
                        f = new u(t),
                        l = e.Uint8Array,
                        s = new l(t),
                        c = e.Uint16Array,
                        d = new c(t),
                        _ = e.Uint32Array,
                        h = (new _(t), e.Float32Array),
                        m = (new h(t), e.Float64Array),
                        p = new m(t),
                        v = e.byteLength,
                        b = 0 | r.DYNAMICTOP_PTR,
                        w = 0 | r.tempDoublePtr,
                        S = (r.ABORT, 0 | r.STACKTOP),
                        k = (r.STACK_MAX, 0 | r.cttz_i8),
                        E = 0,
                        M = (e.NaN, e.Infinity, 0),
                        A = (e.Math.floor, e.Math.abs, e.Math.sqrt, e.Math.pow, e.Math.cos, e.Math.sin, e.Math.tan, e.Math.acos, e.Math.asin, e.Math.atan, e.Math.atan2, e.Math.exp, e.Math.log, e.Math.ceil, e.Math.imul),
                        y = (e.Math.min, e.Math.max, e.Math.clz32),
                        L = r.abort,
                        g = (r.assert, r.enlargeMemory),
                        T = r.getTotalMemory,
                        D = r.abortOnCannotGrowMemory,
                        C = (r.invoke_iiii, r.invoke_viiiii, r.invoke_vi, r.invoke_vii, r.invoke_iiiiiii, r.invoke_ii, r.invoke_viii, r.invoke_viiiiiiiii, r.invoke_iiiii, r.invoke_viiiiii, r.invoke_iii, r.invoke_viiii, r._pthread_cond_wait),
                        P = (r._putenv, r._pthread_join),
                        R = r._SDL_SetVideoMode,
                        O = (r._IMG_Load, r._TTF_FontHeight, r._SDL_CloseAudio, r._emscripten_set_main_loop_timing, r._SDL_GetTicks, r.___buildEnvironment, r._pthread_cond_init),
                        B = r._SDL_LockSurface,
                        x = r.___setErrNo,
                        N = r._pthread_cond_destroy,
                        I = (r._SDL_PauseAudio, r._SDL_Init),
                        F = r._SDL_FreeSurface,
                        H = (r._Mix_PlayChannel, r._TTF_RenderText_Solid, r._Mix_LoadWAV_RW, r._IMG_Load_RW, r._Mix_PlayMusic, r._emscripten_memcpy_big),
                        U = r._pthread_cond_signal,
                        G = r._pthread_mutex_destroy,
                        W = r._SDL_UpperBlit,
                        Y = r.___syscall54,
                        V = r.___unlock,
                        q = r.___syscall140,
                        j = r._pthread_create,
                        z = (r._emscripten_set_main_loop, r._emscripten_get_now, r._SDL_CreateRGBSurface),
                        X = (r._TTF_SizeText, r.___lock),
                        K = r._SDL_UnlockSurface,
                        $ = r.___syscall6,
                        J = (r._Mix_FreeChunk, r._Mix_HaltMusic, r._getenv, r._SDL_Flip),
                        Q = (r._SDL_FreeRW, r._SDL_UpperBlitScaled, r._pthread_mutex_init),
                        Z = (r._SDL_RWFromConstMem, r.___syscall146);

                    function ee(e, r) {
                        e |= 0;
                        var t, n, o, a, u, l, c, _, h, m, p, v, b, w, S, k, E, M, y, L, g, T, D, C, P, R, O, B, x, N, I, F, H, U, G, W, Y, V, q, j, z, X, K, $, J, Q, Z, ee, re, te, ne, ie, oe, ae, ue, fe, le, se, ce, de, _e, he, me, pe, ve, be, we, Se, ke, Ee, Me, Ae, ye, Le, ge, Te, De, Ce, Pe, Re, Oe, Be, xe, Ne, Ie, Fe, He, Ue, Ge, We, Ye, Ve, qe, je, ze, Xe, Ke, $e, Je, Qe, Ze, er, rr, tr, nr, ir, or, ar, ur, fr, lr, sr, cr, dr, _r, hr, mr, pr, vr, br, wr, Sr, kr, Er = 0,
                            Mr = 0,
                            Ar = 0,
                            yr = 0,
                            Lr = 0,
                            gr = 0,
                            Tr = 0,
                            Dr = 0,
                            Cr = 0,
                            Pr = 0,
                            Rr = 0,
                            Or = 0,
                            Br = 0,
                            xr = 0,
                            Nr = 0,
                            Ir = 0;
                        if (t = 0 | f[4 + (r |= 0) >> 2], Er = 0 | f[r >> 2], n = 40 + (Mr = 0 | f[e + 2264 >> 2]) | 0, o = Mr + 584 | 0, i[Mr + 39 >> 0] = -127, i[Mr + 71 >> 0] = -127, i[Mr + 103 >> 0] = -127, i[Mr + 135 >> 0] = -127, i[Mr + 167 >> 0] = -127, i[Mr + 199 >> 0] = -127, i[Mr + 231 >> 0] = -127, i[Mr + 263 >> 0] = -127, i[Mr + 295 >> 0] = -127, i[Mr + 327 >> 0] = -127, i[Mr + 359 >> 0] = -127, i[Mr + 391 >> 0] = -127, i[Mr + 423 >> 0] = -127, i[Mr + 455 >> 0] = -127, i[Mr + 487 >> 0] = -127, i[Mr + 519 >> 0] = -127, a = Mr + 600 | 0, i[Mr + 583 >> 0] = -127, i[Mr + 599 >> 0] = -127, i[Mr + 615 >> 0] = -127, i[Mr + 631 >> 0] = -127, i[Mr + 647 >> 0] = -127, i[Mr + 663 >> 0] = -127, i[Mr + 679 >> 0] = -127, i[Mr + 695 >> 0] = -127, i[Mr + 711 >> 0] = -127, i[Mr + 727 >> 0] = -127, i[Mr + 743 >> 0] = -127, i[Mr + 759 >> 0] = -127, i[Mr + 775 >> 0] = -127, i[Mr + 791 >> 0] = -127, i[Mr + 807 >> 0] = -127, i[Mr + 823 >> 0] = -127, u = (0 | t) > 0) i[Mr + 567 >> 0] = -127, i[Mr + 551 >> 0] = -127, i[Mr + 7 >> 0] = -127;
                        else {
                            yr = 21 + (Ar = Mr + 7 | 0) | 0;
                            do {
                                i[Ar >> 0] = 127, Ar = Ar + 1 | 0
                            } while ((0 | Ar) < (0 | yr));
                            yr = 9 + (Ar = Mr + 551 | 0) | 0;
                            do {
                                i[Ar >> 0] = 127, Ar = Ar + 1 | 0
                            } while ((0 | Ar) < (0 | yr));
                            yr = 9 + (Ar = Mr + 567 | 0) | 0;
                            do {
                                i[Ar >> 0] = 127, Ar = Ar + 1 | 0
                            } while ((0 | Ar) < (0 | yr))
                        }
                        if (!((0 | f[(l = e + 288 | 0) >> 2]) <= 0)) {
                            c = r + 16 | 0, r = e + 2252 | 0, _ = Mr + 8 | 0, h = Mr + 552 | 0, m = Mr + 568 | 0, p = (Lr = 0 == (0 | t)) ? 6 : 5, v = Lr ? 4 : 0, Lr = e + 292 | 0, b = Mr + 520 | 0, w = Mr + 808 | 0, S = Mr + 824 | 0, k = Er << 4, E = e + 2280 | 0, M = Er << 3, Er = e + 2284 | 0, y = e + 2268 | 0, L = e + 2272 | 0, g = e + 2276 | 0, e = Mr + 24 | 0, T = Mr + 408 | 0, D = Mr + 280 | 0, C = Mr + 152 | 0, P = Mr + 4 | 0, R = Mr + 20 | 0, O = Mr + 36 | 0, B = Mr + 52 | 0, x = Mr + 68 | 0, N = Mr + 84 | 0, I = Mr + 100 | 0, F = Mr + 116 | 0, H = Mr + 132 | 0, U = Mr + 148 | 0, G = Mr + 164 | 0, W = Mr + 180 | 0, Y = Mr + 196 | 0, V = Mr + 212 | 0, q = Mr + 228 | 0, j = Mr + 244 | 0, z = Mr + 260 | 0, X = Mr + 276 | 0, K = Mr + 292 | 0, $ = Mr + 308 | 0, J = Mr + 324 | 0, Q = Mr + 340 | 0, Z = Mr + 356 | 0, ee = Mr + 372 | 0, re = Mr + 388 | 0, te = Mr + 404 | 0, ne = Mr + 420 | 0, ie = Mr + 436 | 0, oe = Mr + 452 | 0, ae = Mr + 468 | 0, ue = Mr + 484 | 0, fe = Mr + 500 | 0, le = Mr + 516 | 0, se = Mr + 532 | 0, ce = Mr + 548 | 0, de = Mr + 556 | 0, _e = Mr + 564 | 0, he = Mr + 572 | 0, me = Mr + 580 | 0, pe = Mr + 588 | 0, ve = Mr + 596 | 0, be = Mr + 604 | 0, we = Mr + 612 | 0, Se = Mr + 620 | 0, ke = Mr + 628 | 0, Ee = Mr + 636 | 0, Me = Mr + 644 | 0, Ae = Mr + 652 | 0, ye = Mr + 660 | 0, Le = Mr + 668 | 0, ge = Mr + 676 | 0, Te = Mr + 684 | 0, De = Mr + 692 | 0, Ce = Mr + 700 | 0, Pe = Mr + 708 | 0, Re = Mr + 716 | 0, Oe = Mr + 724 | 0, Be = Mr + 732 | 0, xe = Mr + 740 | 0, Ne = Mr + 748 | 0, Ie = Mr + 756 | 0, Fe = Mr + 764 | 0, He = Mr + 772 | 0, Ue = Mr + 780 | 0, Ge = Mr + 788 | 0, We = Mr + 796 | 0, Ye = Mr + 804 | 0, Ve = Mr + 812 | 0, qe = Mr + 820 | 0, je = Mr + 828 | 0, ze = Mr + 72 | 0, Xe = Mr + 104 | 0, Ke = Mr + 136 | 0, $e = Mr + 168 | 0, Je = Mr + 200 | 0, Qe = Mr + 232 | 0, Ze = Mr + 264 | 0, er = Mr + 296 | 0, rr = Mr + 328 | 0, tr = Mr + 360 | 0, nr = Mr + 392 | 0, ir = Mr + 424 | 0, or = Mr + 456 | 0, ar = Mr + 488 | 0, ur = Mr + 520 | 0, fr = Mr + 616 | 0, lr = Mr + 632 | 0, sr = Mr + 648 | 0, cr = Mr + 664 | 0, dr = Mr + 680 | 0, _r = Mr + 696 | 0, hr = Mr + 712 | 0, mr = Mr + 728 | 0, pr = Mr + 744 | 0, vr = Mr + 760 | 0, br = Mr + 776 | 0, wr = Mr + 792 | 0, Sr = Mr + 808 | 0, kr = Mr + 824 | 0, Mr = 0;
                            do {
                                if (gr = 0 | f[c >> 2], (0 | Mr) > 0 && (Tr = s[R >> 0] | s[R + 1 >> 0] << 8 | s[R + 2 >> 0] << 16 | s[R + 3 >> 0] << 24, i[P >> 0] = Tr, i[P + 1 >> 0] = Tr >> 8, i[P + 2 >> 0] = Tr >> 16, i[P + 3 >> 0] = Tr >> 24, Tr = s[B >> 0] | s[B + 1 >> 0] << 8 | s[B + 2 >> 0] << 16 | s[B + 3 >> 0] << 24, i[O >> 0] = Tr, i[O + 1 >> 0] = Tr >> 8, i[O + 2 >> 0] = Tr >> 16, i[O + 3 >> 0] = Tr >> 24, Tr = s[N >> 0] | s[N + 1 >> 0] << 8 | s[N + 2 >> 0] << 16 | s[N + 3 >> 0] << 24, i[x >> 0] = Tr, i[x + 1 >> 0] = Tr >> 8, i[x + 2 >> 0] = Tr >> 16, i[x + 3 >> 0] = Tr >> 24, Tr = s[F >> 0] | s[F + 1 >> 0] << 8 | s[F + 2 >> 0] << 16 | s[F + 3 >> 0] << 24, i[I >> 0] = Tr, i[I + 1 >> 0] = Tr >> 8, i[I + 2 >> 0] = Tr >> 16, i[I + 3 >> 0] = Tr >> 24, Tr = s[U >> 0] | s[U + 1 >> 0] << 8 | s[U + 2 >> 0] << 16 | s[U + 3 >> 0] << 24, i[H >> 0] = Tr, i[H + 1 >> 0] = Tr >> 8, i[H + 2 >> 0] = Tr >> 16, i[H + 3 >> 0] = Tr >> 24, Tr = s[W >> 0] | s[W + 1 >> 0] << 8 | s[W + 2 >> 0] << 16 | s[W + 3 >> 0] << 24, i[G >> 0] = Tr, i[G + 1 >> 0] = Tr >> 8, i[G + 2 >> 0] = Tr >> 16, i[G + 3 >> 0] = Tr >> 24, Tr = s[V >> 0] | s[V + 1 >> 0] << 8 | s[V + 2 >> 0] << 16 | s[V + 3 >> 0] << 24, i[Y >> 0] = Tr, i[Y + 1 >> 0] = Tr >> 8, i[Y + 2 >> 0] = Tr >> 16, i[Y + 3 >> 0] = Tr >> 24, Tr = s[j >> 0] | s[j + 1 >> 0] << 8 | s[j + 2 >> 0] << 16 | s[j + 3 >> 0] << 24, i[q >> 0] = Tr, i[q + 1 >> 0] = Tr >> 8, i[q + 2 >> 0] = Tr >> 16, i[q + 3 >> 0] = Tr >> 24, Tr = s[X >> 0] | s[X + 1 >> 0] << 8 | s[X + 2 >> 0] << 16 | s[X + 3 >> 0] << 24, i[z >> 0] = Tr, i[z + 1 >> 0] = Tr >> 8, i[z + 2 >> 0] = Tr >> 16, i[z + 3 >> 0] = Tr >> 24, Tr = s[$ >> 0] | s[$ + 1 >> 0] << 8 | s[$ + 2 >> 0] << 16 | s[$ + 3 >> 0] << 24, i[K >> 0] = Tr, i[K + 1 >> 0] = Tr >> 8, i[K + 2 >> 0] = Tr >> 16, i[K + 3 >> 0] = Tr >> 24, Tr = s[Q >> 0] | s[Q + 1 >> 0] << 8 | s[Q + 2 >> 0] << 16 | s[Q + 3 >> 0] << 24, i[J >> 0] = Tr, i[J + 1 >> 0] = Tr >> 8, i[J + 2 >> 0] = Tr >> 16, i[J + 3 >> 0] = Tr >> 24, Tr = s[ee >> 0] | s[ee + 1 >> 0] << 8 | s[ee + 2 >> 0] << 16 | s[ee + 3 >> 0] << 24, i[Z >> 0] = Tr, i[Z + 1 >> 0] = Tr >> 8, i[Z + 2 >> 0] = Tr >> 16, i[Z + 3 >> 0] = Tr >> 24, Tr = s[te >> 0] | s[te + 1 >> 0] << 8 | s[te + 2 >> 0] << 16 | s[te + 3 >> 0] << 24, i[re >> 0] = Tr, i[re + 1 >> 0] = Tr >> 8, i[re + 2 >> 0] = Tr >> 16, i[re + 3 >> 0] = Tr >> 24, Tr = s[ie >> 0] | s[ie + 1 >> 0] << 8 | s[ie + 2 >> 0] << 16 | s[ie + 3 >> 0] << 24, i[ne >> 0] = Tr, i[ne + 1 >> 0] = Tr >> 8, i[ne + 2 >> 0] = Tr >> 16, i[ne + 3 >> 0] = Tr >> 24, Tr = s[ae >> 0] | s[ae + 1 >> 0] << 8 | s[ae + 2 >> 0] << 16 | s[ae + 3 >> 0] << 24, i[oe >> 0] = Tr, i[oe + 1 >> 0] = Tr >> 8, i[oe + 2 >> 0] = Tr >> 16, i[oe + 3 >> 0] = Tr >> 24, Tr = s[fe >> 0] | s[fe + 1 >> 0] << 8 | s[fe + 2 >> 0] << 16 | s[fe + 3 >> 0] << 24, i[ue >> 0] = Tr, i[ue + 1 >> 0] = Tr >> 8, i[ue + 2 >> 0] = Tr >> 16, i[ue + 3 >> 0] = Tr >> 24, Tr = s[se >> 0] | s[se + 1 >> 0] << 8 | s[se + 2 >> 0] << 16 | s[se + 3 >> 0] << 24, i[le >> 0] = Tr, i[le + 1 >> 0] = Tr >> 8, i[le + 2 >> 0] = Tr >> 16, i[le + 3 >> 0] = Tr >> 24, Tr = s[de >> 0] | s[de + 1 >> 0] << 8 | s[de + 2 >> 0] << 16 | s[de + 3 >> 0] << 24, i[ce >> 0] = Tr, i[ce + 1 >> 0] = Tr >> 8, i[ce + 2 >> 0] = Tr >> 16, i[ce + 3 >> 0] = Tr >> 24, Tr = s[he >> 0] | s[he + 1 >> 0] << 8 | s[he + 2 >> 0] << 16 | s[he + 3 >> 0] << 24, i[_e >> 0] = Tr, i[_e + 1 >> 0] = Tr >> 8, i[_e + 2 >> 0] = Tr >> 16, i[_e + 3 >> 0] = Tr >> 24, Tr = s[pe >> 0] | s[pe + 1 >> 0] << 8 | s[pe + 2 >> 0] << 16 | s[pe + 3 >> 0] << 24, i[me >> 0] = Tr, i[me + 1 >> 0] = Tr >> 8, i[me + 2 >> 0] = Tr >> 16, i[me + 3 >> 0] = Tr >> 24, Tr = s[be >> 0] | s[be + 1 >> 0] << 8 | s[be + 2 >> 0] << 16 | s[be + 3 >> 0] << 24, i[ve >> 0] = Tr, i[ve + 1 >> 0] = Tr >> 8, i[ve + 2 >> 0] = Tr >> 16, i[ve + 3 >> 0] = Tr >> 24, Tr = s[Se >> 0] | s[Se + 1 >> 0] << 8 | s[Se + 2 >> 0] << 16 | s[Se + 3 >> 0] << 24, i[we >> 0] = Tr, i[we + 1 >> 0] = Tr >> 8, i[we + 2 >> 0] = Tr >> 16, i[we + 3 >> 0] = Tr >> 24, Tr = s[Ee >> 0] | s[Ee + 1 >> 0] << 8 | s[Ee + 2 >> 0] << 16 | s[Ee + 3 >> 0] << 24, i[ke >> 0] = Tr, i[ke + 1 >> 0] = Tr >> 8, i[ke + 2 >> 0] = Tr >> 16, i[ke + 3 >> 0] = Tr >> 24, Tr = s[Ae >> 0] | s[Ae + 1 >> 0] << 8 | s[Ae + 2 >> 0] << 16 | s[Ae + 3 >> 0] << 24, i[Me >> 0] = Tr, i[Me + 1 >> 0] = Tr >> 8, i[Me + 2 >> 0] = Tr >> 16, i[Me + 3 >> 0] = Tr >> 24, Tr = s[Le >> 0] | s[Le + 1 >> 0] << 8 | s[Le + 2 >> 0] << 16 | s[Le + 3 >> 0] << 24, i[ye >> 0] = Tr, i[ye + 1 >> 0] = Tr >> 8, i[ye + 2 >> 0] = Tr >> 16, i[ye + 3 >> 0] = Tr >> 24, Tr = s[Te >> 0] | s[Te + 1 >> 0] << 8 | s[Te + 2 >> 0] << 16 | s[Te + 3 >> 0] << 24, i[ge >> 0] = Tr, i[ge + 1 >> 0] = Tr >> 8, i[ge + 2 >> 0] = Tr >> 16, i[ge + 3 >> 0] = Tr >> 24, Tr = s[Ce >> 0] | s[Ce + 1 >> 0] << 8 | s[Ce + 2 >> 0] << 16 | s[Ce + 3 >> 0] << 24, i[De >> 0] = Tr, i[De + 1 >> 0] = Tr >> 8, i[De + 2 >> 0] = Tr >> 16, i[De + 3 >> 0] = Tr >> 24, Tr = s[Re >> 0] | s[Re + 1 >> 0] << 8 | s[Re + 2 >> 0] << 16 | s[Re + 3 >> 0] << 24, i[Pe >> 0] = Tr, i[Pe + 1 >> 0] = Tr >> 8, i[Pe + 2 >> 0] = Tr >> 16, i[Pe + 3 >> 0] = Tr >> 24, Tr = s[Be >> 0] | s[Be + 1 >> 0] << 8 | s[Be + 2 >> 0] << 16 | s[Be + 3 >> 0] << 24, i[Oe >> 0] = Tr, i[Oe + 1 >> 0] = Tr >> 8, i[Oe + 2 >> 0] = Tr >> 16, i[Oe + 3 >> 0] = Tr >> 24, Tr = s[Ne >> 0] | s[Ne + 1 >> 0] << 8 | s[Ne + 2 >> 0] << 16 | s[Ne + 3 >> 0] << 24, i[xe >> 0] = Tr, i[xe + 1 >> 0] = Tr >> 8, i[xe + 2 >> 0] = Tr >> 16, i[xe + 3 >> 0] = Tr >> 24, Tr = s[Fe >> 0] | s[Fe + 1 >> 0] << 8 | s[Fe + 2 >> 0] << 16 | s[Fe + 3 >> 0] << 24, i[Ie >> 0] = Tr, i[Ie + 1 >> 0] = Tr >> 8, i[Ie + 2 >> 0] = Tr >> 16, i[Ie + 3 >> 0] = Tr >> 24, Tr = s[Ue >> 0] | s[Ue + 1 >> 0] << 8 | s[Ue + 2 >> 0] << 16 | s[Ue + 3 >> 0] << 24, i[He >> 0] = Tr, i[He + 1 >> 0] = Tr >> 8, i[He + 2 >> 0] = Tr >> 16, i[He + 3 >> 0] = Tr >> 24, Tr = s[We >> 0] | s[We + 1 >> 0] << 8 | s[We + 2 >> 0] << 16 | s[We + 3 >> 0] << 24, i[Ge >> 0] = Tr, i[Ge + 1 >> 0] = Tr >> 8, i[Ge + 2 >> 0] = Tr >> 16, i[Ge + 3 >> 0] = Tr >> 24, Tr = s[Ve >> 0] | s[Ve + 1 >> 0] << 8 | s[Ve + 2 >> 0] << 16 | s[Ve + 3 >> 0] << 24, i[Ye >> 0] = Tr, i[Ye + 1 >> 0] = Tr >> 8, i[Ye + 2 >> 0] = Tr >> 16, i[Ye + 3 >> 0] = Tr >> 24, Tr = s[je >> 0] | s[je + 1 >> 0] << 8 | s[je + 2 >> 0] << 16 | s[je + 3 >> 0] << 24, i[qe >> 0] = Tr, i[qe + 1 >> 0] = Tr >> 8, i[qe + 2 >> 0] = Tr >> 16, i[qe + 3 >> 0] = Tr >> 24), Dr = (Tr = 0 | f[r >> 2]) + (Mr << 5) | 0, Cr = 0 | f[gr + (800 * Mr | 0) + 788 >> 2], u) {
                                    Pr = Dr, yr = (Ar = _) + 16 | 0;
                                    do {
                                        i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                    } while ((0 | Ar) < (0 | yr));
                                    Br = s[(Or = Rr = Tr + (Mr << 5) + 16 | 0) >> 0] | s[Or + 1 >> 0] << 8 | s[Or + 2 >> 0] << 16 | s[Or + 3 >> 0] << 24, Rr = s[(Or = Rr + 4 | 0) >> 0] | s[Or + 1 >> 0] << 8 | s[Or + 2 >> 0] << 16 | s[Or + 3 >> 0] << 24, i[(xr = Or = h) >> 0] = Br, i[xr + 1 >> 0] = Br >> 8, i[xr + 2 >> 0] = Br >> 16, i[xr + 3 >> 0] = Br >> 24, i[(Br = Or + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, Or = s[(Br = Rr = Tr + (Mr << 5) + 24 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, i[(xr = Br = m) >> 0] = Or, i[xr + 1 >> 0] = Or >> 8, i[xr + 2 >> 0] = Or >> 16, i[xr + 3 >> 0] = Or >> 24, i[(Or = Br + 4 | 0) >> 0] = Rr, i[Or + 1 >> 0] = Rr >> 8, i[Or + 2 >> 0] = Rr >> 16, i[Or + 3 >> 0] = Rr >> 24
                                }
                                e: do {
                                    if (0 | i[gr + (800 * Mr | 0) + 768 >> 0]) {
                                        do {
                                            if (u) {
                                                if ((0 | Mr) < ((0 | f[l >> 2]) - 1 | 0)) {
                                                    Or = s[(Rr = Dr + 32 | 0) >> 0] | s[Rr + 1 >> 0] << 8 | s[Rr + 2 >> 0] << 16 | s[Rr + 3 >> 0] << 24, i[e >> 0] = Or, i[e + 1 >> 0] = Or >> 8, i[e + 2 >> 0] = Or >> 16, i[e + 3 >> 0] = Or >> 24, Nr = Or;
                                                    break
                                                }
                                                at(0 | e, 0 | (Or = 0 | i[Tr + (Mr << 5) + 15 >> 0]), 4), Nr = (Or = (Rr = 255 & Or) | Rr << 8) | Or << 16;
                                                break
                                            }
                                            Nr = 0 | f[e >> 2]
                                        } while (0);
                                        for (f[T >> 2] = Nr, f[D >> 2] = Nr, f[C >> 2] = Nr, Or = Cr, Rr = 0;;) {
                                            switch (xr = n + (0 | d[954 + (Rr << 1) >> 1]) | 0, Pt[31 & f[11608 + ((255 & i[gr + (800 * Mr | 0) + 769 + Rr >> 0]) << 2) >> 2]](xr), Br = gr + (800 * Mr | 0) + (Rr << 4 << 1) | 0, Or >>> 30 & 3) {
                                                case 3:
                                                    xt[31 & f[2919]](Br, xr, 0);
                                                    break;
                                                case 2:
                                                    Rt[15 & f[2920]](Br, xr);
                                                    break;
                                                case 1:
                                                    Rt[15 & f[2922]](Br, xr)
                                            }
                                            if (16 == (0 | (Rr = Rr + 1 | 0))) break e;
                                            Or <<= 2
                                        }
                                    } else if (Rr = 0 | i[gr + (800 * Mr | 0) + 769 >> 0], Pt[31 & f[11580 + ((Rr << 24 >> 24 == 0 ? 0 == (0 | Mr) ? p : v : 255 & Rr) << 2) >> 2]](n), 0 | Cr)
                                        for (Rr = Cr, Or = 0;;) {
                                            switch (Br = gr + (800 * Mr | 0) + (Or << 4 << 1) | 0, xr = n + (0 | d[954 + (Or << 1) >> 1]) | 0, Rr >>> 30 & 3) {
                                                case 3:
                                                    xt[31 & f[2919]](Br, xr, 0);
                                                    break;
                                                case 2:
                                                    Rt[15 & f[2920]](Br, xr);
                                                    break;
                                                case 1:
                                                    Rt[15 & f[2922]](Br, xr)
                                            }
                                            if (16 == (0 | (Or = Or + 1 | 0))) break e;
                                            Rr <<= 2
                                        }
                                } while (0);
                                Cr = 0 | f[gr + (800 * Mr | 0) + 792 >> 2], Or = 0 | i[gr + (800 * Mr | 0) + 785 >> 0], Pt[31 & f[(Rr = 11648 + ((Or << 24 >> 24 == 0 ? 0 == (0 | Mr) ? p : v : 255 & Or) << 2) | 0) >> 2]](o), Pt[31 & f[Rr >> 2]](a), Rr = gr + (800 * Mr | 0) + 512 | 0;
                                do {
                                    if (255 & Cr | 0) {
                                        if (170 & Cr) {
                                            Rt[15 & f[2921]](Rr, o);
                                            break
                                        }
                                        Rt[15 & f[2923]](Rr, o);
                                        break
                                    }
                                } while (0);
                                Rr = Cr >>> 8, Or = gr + (800 * Mr | 0) + 640 | 0;
                                do {
                                    if (255 & Rr | 0) {
                                        if (170 & Rr) {
                                            Rt[15 & f[2921]](Or, a);
                                            break
                                        }
                                        Rt[15 & f[2923]](Or, a);
                                        break
                                    }
                                } while (0);
                                if ((0 | t) < ((0 | f[Lr >> 2]) - 1 | 0)) {
                                    Pr = b, yr = (Ar = Dr) + 16 | 0;
                                    do {
                                        i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                    } while ((0 | Ar) < (0 | yr));
                                    Rr = s[(Or = Dr = w) >> 0] | s[Or + 1 >> 0] << 8 | s[Or + 2 >> 0] << 16 | s[Or + 3 >> 0] << 24, Dr = s[(Or = Dr + 4 | 0) >> 0] | s[Or + 1 >> 0] << 8 | s[Or + 2 >> 0] << 16 | s[Or + 3 >> 0] << 24, i[(gr = Or = Tr + (Mr << 5) + 16 | 0) >> 0] = Rr, i[gr + 1 >> 0] = Rr >> 8, i[gr + 2 >> 0] = Rr >> 16, i[gr + 3 >> 0] = Rr >> 24, i[(Rr = Or + 4 | 0) >> 0] = Dr, i[Rr + 1 >> 0] = Dr >> 8, i[Rr + 2 >> 0] = Dr >> 16, i[Rr + 3 >> 0] = Dr >> 24, Or = s[(Rr = Dr = S) >> 0] | s[Rr + 1 >> 0] << 8 | s[Rr + 2 >> 0] << 16 | s[Rr + 3 >> 0] << 24, Dr = s[(Rr = Dr + 4 | 0) >> 0] | s[Rr + 1 >> 0] << 8 | s[Rr + 2 >> 0] << 16 | s[Rr + 3 >> 0] << 24, i[(gr = Rr = Tr + (Mr << 5) + 24 | 0) >> 0] = Or, i[gr + 1 >> 0] = Or >> 8, i[gr + 2 >> 0] = Or >> 16, i[gr + 3 >> 0] = Or >> 24, i[(Or = Rr + 4 | 0) >> 0] = Dr, i[Or + 1 >> 0] = Dr >> 8, i[Or + 2 >> 0] = Dr >> 16, i[Or + 3 >> 0] = Dr >> 24
                                }
                                Dr = 0 | A(k, 0 | f[E >> 2]), Or = 0 | f[Er >> 2], Rr = (0 | f[y >> 2]) + (Mr << 4) + Dr | 0, Dr = Mr << 3, gr = (0 | f[L >> 2]) + Dr | 0, Cr = (0 | f[g >> 2]) + Dr | 0, Pr = n, yr = (Ar = Rr) + 16 | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = ze, yr = 16 + (Ar = Rr + (0 | f[E >> 2]) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = Xe, yr = 16 + (Ar = Rr + (f[E >> 2] << 1) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = Ke, yr = 16 + (Ar = Rr + (3 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = $e, yr = 16 + (Ar = Rr + (f[E >> 2] << 2) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = Je, yr = 16 + (Ar = Rr + (5 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = Qe, yr = 16 + (Ar = Rr + (6 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = Ze, yr = 16 + (Ar = Rr + (7 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = er, yr = 16 + (Ar = Rr + (f[E >> 2] << 3) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = rr, yr = 16 + (Ar = Rr + (9 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = tr, yr = 16 + (Ar = Rr + (10 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = nr, yr = 16 + (Ar = Rr + (11 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = ir, yr = 16 + (Ar = Rr + (12 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = or, yr = 16 + (Ar = Rr + (13 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = ar, yr = 16 + (Ar = Rr + (14 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Pr = ur, yr = 16 + (Ar = Rr + (15 * (0 | f[E >> 2]) | 0) | 0) | 0;
                                do {
                                    i[Ar >> 0] = 0 | i[Pr >> 0], Ar = Ar + 1 | 0, Pr = Pr + 1 | 0
                                } while ((0 | Ar) < (0 | yr));
                                Tr = gr + (Rr = 0 | A(Or, M)) | 0, Dr = Cr + Rr | 0, Br = s[(xr = Rr = o) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, i[(Ir = xr = Tr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = a) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, i[(Ir = Br = Dr) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = fr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (0 | f[Er >> 2]) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = lr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (0 | f[Er >> 2]) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = sr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (f[Er >> 2] << 1) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = cr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (f[Er >> 2] << 1) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = dr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (3 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = _r) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (3 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = hr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (f[Er >> 2] << 2) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = mr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (f[Er >> 2] << 2) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = pr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (5 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = vr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (5 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = br) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (6 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = xr) >> 0] = Br, i[Ir + 1 >> 0] = Br >> 8, i[Ir + 2 >> 0] = Br >> 16, i[Ir + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = wr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (6 * (0 | f[Er >> 2]) | 0) | 0, i[(Ir = Br) >> 0] = xr, i[Ir + 1 >> 0] = xr >> 8, i[Ir + 2 >> 0] = xr >> 16, i[Ir + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Br = s[(xr = Rr = Sr) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, Rr = s[(xr = Rr + 4 | 0) >> 0] | s[xr + 1 >> 0] << 8 | s[xr + 2 >> 0] << 16 | s[xr + 3 >> 0] << 24, xr = Tr + (7 * (0 | f[Er >> 2]) | 0) | 0, i[(Tr = xr) >> 0] = Br, i[Tr + 1 >> 0] = Br >> 8, i[Tr + 2 >> 0] = Br >> 16, i[Tr + 3 >> 0] = Br >> 24, i[(Br = xr + 4 | 0) >> 0] = Rr, i[Br + 1 >> 0] = Rr >> 8, i[Br + 2 >> 0] = Rr >> 16, i[Br + 3 >> 0] = Rr >> 24, xr = s[(Br = Rr = kr) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Rr = s[(Br = Rr + 4 | 0) >> 0] | s[Br + 1 >> 0] << 8 | s[Br + 2 >> 0] << 16 | s[Br + 3 >> 0] << 24, Br = Dr + (7 * (0 | f[Er >> 2]) | 0) | 0, i[(Dr = Br) >> 0] = xr, i[Dr + 1 >> 0] = xr >> 8, i[Dr + 2 >> 0] = xr >> 16, i[Dr + 3 >> 0] = xr >> 24, i[(xr = Br + 4 | 0) >> 0] = Rr, i[xr + 1 >> 0] = Rr >> 8, i[xr + 2 >> 0] = Rr >> 16, i[xr + 3 >> 0] = Rr >> 24, Mr = Mr + 1 | 0
                            } while ((0 | Mr) < (0 | f[l >> 2]))
                        }
                    }

                    function re(e, r) {
                        r |= 0;
                        var t, n, o, a, u, l, c, d, _, h, m, p, v, b, w, k, E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            re = 0;
                        if (t = S, S = S + 64 | 0, n = t, a = 0 | f[(o = 160 + (e |= 0) | 0) >> 2], y = 255 & (M = 0 | i[5345 + (0 | f[(E = e + 2308 | 0) >> 2]) >> 0]), L = 0 | f[(u = e + 2280 | 0) >> 2], l = 0 | A(y, L), g = 0 | f[(c = e + 2284 | 0) >> 2], d = 0 | A((255 & M) >>> 1 & 255, g), M = 0 | A(L, a << 4), L = 0 | A(g, a << 3), _ = 0 - l | 0, h = (0 | f[(g = e + 2268 | 0) >> 2]) + _ + M | 0, p = 0 - d | 0, v = (0 | f[(m = e + 2272 | 0) >> 2]) + p + L | 0, w = (0 | f[(b = e + 2276 | 0) >> 2]) + p + L | 0, C = 0 == (0 | (D = 0 | f[(T = e + 164 | 0) >> 2])), k = (0 | D) >= ((0 | f[e + 308 >> 2]) - 1 | 0), 2 == (0 | f[e + 148 >> 2]) && ee(e, o), 0 | f[e + 168 >> 2] && (0 | (P = 0 | f[e + 296 >> 2])) < (0 | f[(R = e + 304 | 0) >> 2])) {
                            O = e + 172 | 0, B = (0 | f[T >> 2]) > 0, T = P;
                            do {
                                P = 0 | f[o >> 2], x = 0 | f[u >> 2], N = 0 | f[O >> 2], I = (0 | f[g >> 2]) + (0 | A(P << 4, x)) + (T << 4) | 0, F = 0 | s[N + (T << 2) + 1 >> 0], U = 255 & (H = 0 | i[N + (T << 2) >> 0]);
                                do {
                                    if (H << 24 >> 24) {
                                        if (G = (0 | T) > 0, 1 == (0 | f[E >> 2])) {
                                            if (G && xt[31 & f[2933]](I, x, U + 4 | 0), 0 | i[(W = N + (T << 2) + 2 | 0) >> 0] && xt[31 & f[2935]](I, x, U), B && xt[31 & f[2932]](I, x, U + 4 | 0), !(0 | i[W >> 0])) break;
                                            xt[31 & f[2934]](I, x, U);
                                            break
                                        }
                                        if (W = 0 | f[c >> 2], Y = 0 | A(W, P << 3), V = T << 3, q = (0 | f[m >> 2]) + Y + V | 0, j = (0 | f[b >> 2]) + Y + V | 0, V = 0 | s[N + (T << 2) + 3 >> 0], Y = U + 4 | 0, G && (Ct[31 & f[2925]](I, x, Y, F, V), Ft[15 & f[2927]](q, j, W, Y, F, V)), 0 | i[(G = N + (T << 2) + 2 | 0) >> 0] && (Ct[31 & f[2929]](I, x, U, F, V), Ft[15 & f[2931]](q, j, W, U, F, V)), B && (Ct[31 & f[2924]](I, x, Y, F, V), Ft[15 & f[2926]](q, j, W, Y, F, V)), !(0 | i[G >> 0])) break;
                                        Ct[31 & f[2928]](I, x, U, F, V), Ft[15 & f[2930]](q, j, W, U, F, V);
                                        break
                                    }
                                } while (0);
                                T = T + 1 | 0
                            } while ((0 | T) < (0 | f[R >> 2]))
                        }
                        if (0 | f[e + 540 >> 2] && (0 | (R = 0 | f[e + 296 >> 2])) < (0 | (B = 0 | f[(T = e + 304 | 0) >> 2])))
                            for (E = e + 176 | 0, O = e + 544 | 0, F = e + 548 | 0, U = R, R = B;;) {
                                if (B = 0 | f[c >> 2], x = (0 | f[E >> 2]) + (800 * U | 0) + 796 | 0, (255 & (I = 0 | i[x >> 0])) > 3) {
                                    N = 255 & I, I = 0 | f[m >> 2], P = 0 | A(f[o >> 2] << 3, B), H = 0 | f[b >> 2], V = 0, W = 0 | f[O >> 2], j = 0 | f[F >> 2];
                                    do {
                                        G = (0 | f[(q = e + 552 + (W << 2) | 0) >> 2]) - (0 | f[e + 552 + (j << 2) >> 2]) | 0, f[q >> 2] = 2147483647 & G, W = 55 == (0 | (q = 1 + (0 | f[O >> 2]) | 0)) ? 0 : q, f[O >> 2] = W, j = 55 == (0 | (q = 1 + (0 | f[F >> 2]) | 0)) ? 0 : q, f[F >> 2] = j, q = 128 + ((0 | A(G << 1 >> 24, N)) >>> 8) & 255, i[n + V >> 0] = q, V = V + 1 | 0
                                    } while (64 != (0 | V));
                                    V = U << 3, xt[31 & f[2936]](n, I + P + V | 0, B), N = 0 | s[x >> 0], j = 0, W = 0 | f[O >> 2], q = 0 | f[F >> 2];
                                    do {
                                        Y = (0 | f[(G = e + 552 + (W << 2) | 0) >> 2]) - (0 | f[e + 552 + (q << 2) >> 2]) | 0, f[G >> 2] = 2147483647 & Y, W = 55 == (0 | (G = 1 + (0 | f[O >> 2]) | 0)) ? 0 : G, f[O >> 2] = W, q = 55 == (0 | (G = 1 + (0 | f[F >> 2]) | 0)) ? 0 : G, f[F >> 2] = q, G = 128 + ((0 | A(Y << 1 >> 24, N)) >>> 8) & 255, i[n + j >> 0] = G, j = j + 1 | 0
                                    } while (64 != (0 | j));
                                    xt[31 & f[2936]](n, H + P + V | 0, B), z = 0 | f[T >> 2]
                                } else z = R;
                                if ((0 | (U = U + 1 | 0)) >= (0 | z)) break;
                                R = z
                            }
                        if (0 | f[(z = r + 44 | 0) >> 2]) {
                            if (R = D << 4, C ? (f[r + 20 >> 2] = (0 | f[g >> 2]) + M, f[r + 24 >> 2] = (0 | f[m >> 2]) + L, X = R, K = (0 | f[b >> 2]) + L | 0) : (f[r + 20 >> 2] = h, f[r + 24 >> 2] = v, X = R - y | 0, K = w), f[(L = r + 28 | 0) >> 2] = K, R = (0 | (K = R + 16 + (k ? 0 : 0 - y | 0) | 0)) > (0 | (y = 0 | f[r + 88 >> 2])) ? y : K, f[(K = r + 104 | 0) >> 2] = 0, 0 != (0 | f[e + 2348 >> 2]) & (0 | R) > (0 | X)) {
                                if (y = 0 | Ke(e, r, X, R - X | 0), f[K >> 2] = y, !y) return $ = 0 | te(e, 3, 5348), S = t, 0 | $;
                                J = y
                            } else J = 0;
                            (0 | (y = 0 | f[r + 84 >> 2])) > (0 | X) ? (M = y - X | 0, C = 0 | A(0 | f[u >> 2], M), f[(D = r + 20 | 0) >> 2] = (0 | f[D >> 2]) + C, C = 0 | A(0 | f[c >> 2], M >> 1), f[(D = r + 24 | 0) >> 2] = (0 | f[D >> 2]) + C, f[L >> 2] = (0 | f[L >> 2]) + C, J ? (C = J + (0 | A(0 | f[r >> 2], M)) | 0, f[K >> 2] = C, Q = y, Z = C) : (Q = y, Z = 0)) : (Q = X, Z = J), (0 | R) > (0 | Q) ? (J = 0 | f[r + 76 >> 2], f[(X = r + 20 | 0) >> 2] = (0 | f[X >> 2]) + J, X = J >> 1, f[(C = r + 24 | 0) >> 2] = (0 | f[C >> 2]) + X, f[L >> 2] = (0 | f[L >> 2]) + X, 0 | Z && (f[K >> 2] = Z + J), f[r + 8 >> 2] = Q - y, f[r + 12 >> 2] = (0 | f[r + 80 >> 2]) - J, f[r + 16 >> 2] = R - Q, re = 0 | Bt[7 & f[z >> 2]](r)) : re = 1
                        } else re = 1;
                        return k | (a + 1 | 0) != (0 | f[e + 156 >> 2]) ? (S = t, 0 | ($ = re)) : (lt((0 | f[g >> 2]) + _ | 0, h + (f[u >> 2] << 4) | 0, 0 | l), lt((0 | f[m >> 2]) + p | 0, v + (f[c >> 2] << 3) | 0, 0 | d), lt((0 | f[b >> 2]) + p | 0, w + (f[c >> 2] << 3) | 0, 0 | d), S = t, 0 | ($ = re))
                    }

                    function te(e, r, t) {
                        return r |= 0, t |= 0, 0 | f[(e |= 0) >> 2] ? 0 : (f[e >> 2] = r, f[e + 8 >> 2] = t, f[e + 4 >> 2] = 0, 0)
                    }

                    function ne(e, r) {
                        e |= 0;
                        var t = 0,
                            n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0;
                        if (0 | (t = 0 | f[48 + (r |= 0) >> 2]) && 0 == (0 | Bt[7 & t](r))) return te(e, 6, 5377), 0 | f[e >> 2];
                        if (t = e + 2308 | 0, 0 | f[r + 68 >> 2] ? (f[t >> 2] = 0, d = 0, _ = 0, c = 8) : (n = 0 | f[t >> 2], o = 0 | s[5345 + n >> 0], 2 == (0 | n) ? (f[e + 296 >> 2] = 0, a = e + 300 | 0, u = o, l = 2, c = 11) : (d = n, _ = o, c = 8)), 8 == (0 | c) && (t = (0 | f[r + 76 >> 2]) - _ >> 4, f[(o = e + 296 | 0) >> 2] = t, n = (0 | f[r + 84 >> 2]) - _ >> 4, f[(h = e + 300 | 0) >> 2] = n, (0 | t) < 0 && (f[o >> 2] = 0), (0 | n) < 0 ? (a = h, u = _, l = d, c = 11) : (m = _, p = d)), 11 == (0 | c) && (f[a >> 2] = 0, m = u, p = l), m = (l = m + 15 | 0) + (0 | f[r + 88 >> 2]) >> 4, f[(u = e + 308 | 0) >> 2] = m, a = l + (0 | f[r + 80 >> 2]) >> 4, r = 0 | f[e + 288 >> 2], f[e + 304 >> 2] = (0 | a) > (0 | r) ? r : a, (0 | m) > (0 | (a = 0 | f[e + 292 >> 2])) && (f[u >> 2] = a), (0 | p) <= 0) return 0;
                        p = e + 104 | 0, a = e + 60 | 0, u = e + 68 | 0, m = e + 64 | 0, r = e + 72 | 0, l = e + 88 | 0, c = e + 112 | 0, d = 0;
                        do {
                            0 | f[p >> 2] ? (_ = 0 | i[e + 120 + d >> 0], v = 0 | f[c >> 2] ? _ : (0 | f[a >> 2]) + _ | 0) : v = 0 | f[a >> 2], (0 | (h = (0 | (b = (_ = 0 == (0 | f[u >> 2])) ? v : (0 | f[r >> 2]) + v | 0)) < 0 ? 0 : (0 | b) < 63 ? b : 63)) > 0 ? (o = h >> ((0 | (n = 0 | f[m >> 2])) > 4 ? 2 : 1), t = 9 - n | 0, o = (0 | (w = (0 | n) > 0 ? (0 | o) > (0 | t) ? t : o : h)) > 1 ? w : 1, i[e + 2312 + (d << 3) + 1 >> 0] = o, i[e + 2312 + (d << 3) + 3 >> 0] = (0 | h) > 39 ? 2 : (0 | h) > 14 & 1, S = o + (h << 1) & 255) : S = 0, i[e + 2312 + (d << 3) >> 0] = S, i[e + 2312 + (d << 3) + 2 >> 0] = 0, (0 | (_ = (0 | (k = _ ? v : (0 | f[r >> 2]) + v + (0 | f[l >> 2]) | 0)) < 0 ? 0 : (0 | k) < 63 ? k : 63)) > 0 ? (o = _ >> ((0 | (h = 0 | f[m >> 2])) > 4 ? 2 : 1), w = 9 - h | 0, o = (0 | (t = (0 | h) > 0 ? (0 | o) > (0 | w) ? w : o : _)) > 1 ? t : 1, i[e + 2312 + (d << 3) + 5 >> 0] = o, i[e + 2312 + (d << 3) + 7 >> 0] = (0 | _) > 39 ? 2 : (0 | _) > 14 & 1, E = o + (_ << 1) & 255) : E = 0, i[e + 2312 + (d << 3) + 4 >> 0] = E, i[e + 2312 + (d << 3) + 6 >> 0] = 1, d = d + 1 | 0
                        } while (4 != (0 | d));
                        return 0
                    }

                    function ie(e, r) {
                        r |= 0;
                        var t, n, o, a, u, l = 0,
                            c = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0;
                        f[(t = 152 + (e |= 0) | 0) >> 2] = 0, l = e + 148 | 0;
                        do {
                            if ((0 | f[l >> 2]) > 0) {
                                if (0 | Bt[7 & f[(c = 376) >> 2]](e + 124 | 0)) {
                                    f[e + 136 >> 2] = e, f[e + 140 >> 2] = e + 180, f[e + 132 >> 2] = 22, _ = (0 | f[e + 2308 >> 2]) > 0 ? 3 : 2, h = 6;
                                    break
                                }
                                if (0 | te(e, 1, 5396)) {
                                    m = 0 | f[e + 156 >> 2];
                                    break
                                }
                                return 0
                            }
                            _ = 1, h = 6
                        } while (0);
                        if (6 == (0 | h) && (f[e + 156 >> 2] = _, m = _), c = (_ = 0 | f[e + 288 >> 2]) << 2, p = _ << 5, n = 2 + (_ << 1) | 0, b = 0 | f[(v = e + 2308 | 0) >> 2], w = 0 | f[l >> 2], S = 0 | A((0 | w) > 0 ? 2 : 1, c), k = (0 | b) > 0 ? S : 0, S = 0 | A(800 * _ | 0, 2 == (0 | w) ? 2 : 1), E = m << 4, o = 0 | A((3 * ((0 | s[5345 + b >> 0]) + E | 0) | 0) / 2 | 0, p), 0 | f[e + 2348 >> 2] ? (y = b = 0 | _t(0 | d[e + 50 >> 1], 0, 0 | d[e + 48 >> 1], 0), L = M) : (y = 0, L = 0), b = 0 | ot(0 | p, 0, 863, 0), g = 0 | ot(0 | b, 0 | M, 0 | c, 0), b = 0 | ot(0 | g, 0 | M, 0 | n, 0), g = 0 | ot(0 | b, 0 | M, 0 | S, 0), b = 0 | ot(0 | g, 0 | M, 0 | k, 0), g = 0 | ot(0 | b, 0 | M, 0 | o, 0), !((0 | (b = 0 | ot(0 | g, 0 | M, 0 | y, 0 | L))) == (0 | b) & 0 == (0 | (g = M)))) return 0;
                        a = e + 2292 | 0, T = 0 | f[(u = e + 2288 | 0) >> 2];
                        do {
                            if (g >>> 0 > 0 | (0 == (0 | g) ? b >>> 0 > (0 | f[a >> 2]) >>> 0 : 0)) {
                                if (Ve(T), f[a >> 2] = 0, D = 0 | Je(b, g, 1), f[u >> 2] = D, 0 | D) {
                                    f[a >> 2] = b, C = 0 | f[l >> 2], P = D, h = 13;
                                    break
                                }
                                if (0 | te(e, 1, 5426)) {
                                    R = e + 2276 | 0, O = e + 2280 | 0, B = e + 2284 | 0, x = e + 2268 | 0, N = e + 2272 | 0;
                                    break
                                }
                                return 0
                            }
                            C = w, P = T, h = 13
                        } while (0);
                        return 13 == (0 | h) && (f[(h = e + 2244 | 0) >> 2] = P, T = P + c | 0, f[e + 2252 >> 2] = T, p = 2 + (P = T + p | 0) | 0, f[e + 2256 >> 2] = p, T = P + n | 0, P = 0 | k ? T : 0, f[e + 2260 >> 2] = P, f[e + 160 >> 2] = 0, f[e + 172 >> 2] = (0 | C) > 0 ? P + (_ << 2) | 0 : P, P = T + k + 31 & -32, f[e + 2264 >> 2] = P, k = P + 832 | 0, f[e + 2304 >> 2] = k, f[(P = e + 176 | 0) >> 2] = k, 2 == (0 | C) && (f[P >> 2] = k + (800 * _ | 0)), P = k + S | 0, S = _ << 4, f[(k = e + 2280 | 0) >> 2] = S, C = _ << 3, f[(_ = e + 2284 | 0) >> 2] = C, T = 0 | i[5345 + (0 | f[v >> 2]) >> 0], v = 0 | A((255 & T) >>> 1 & 255, C), w = P + (0 | A(255 & T, S)) | 0, f[(T = e + 2268 | 0) >> 2] = w, l = w + (0 | A(S, E)) + v | 0, f[(E = e + 2272 | 0) >> 2] = l, S = l + (0 | A(m << 3, C)) + v | 0, f[(v = e + 2276 | 0) >> 2] = S, f[t >> 2] = 0, f[e + 2364 >> 2] = 0 != (0 | y) | 0 != (0 | L) ? P + o | 0 : 0, at(p + -2 | 0, 0, 0 | n), function(e) {
                            var r;
                            r = 0 | f[2256 + (e |= 0) >> 2], i[r + -2 >> 0] = 0, i[r + -1 >> 0] = 0, f[e + 2248 >> 2] = 0, f[e + 2296 >> 2] = 0
                        }(e), at(0 | f[h >> 2], 0, 0 | c), R = v, O = k, B = _, x = T, N = E), f[r + 8 >> 2] = 0, f[r + 20 >> 2] = f[x >> 2], f[r + 24 >> 2] = f[N >> 2], f[r + 28 >> 2] = f[R >> 2], f[r + 32 >> 2] = f[O >> 2], f[r + 36 >> 2] = f[B >> 2], f[r + 104 >> 2] = 0,
                            function() {
                                var e, r = 0;
                                0 | ut(12472) || (e = 0 | f[2893], (0 | f[99]) == (0 | e) ? r = e : (f[2894] = 4, f[2919] = 9, f[2922] = 5, f[2920] = 6, f[2921] = 7, f[2923] = 8, f[2924] = 19, f[2928] = 20, f[2925] = 21, f[2926] = 5, f[2930] = 6, f[2932] = 10, f[2933] = 11, f[2934] = 12, f[2935] = 13, f[2929] = 22, f[2927] = 7, f[2931] = 8, f[2902] = 8, f[2903] = 9, f[2904] = 10, f[2906] = 11, f[2908] = 12, f[2905] = 13, f[2907] = 14, f[2909] = 15, f[2910] = 16, f[2911] = 17, f[2895] = 18, f[2896] = 19, f[2897] = 20, f[2898] = 21, f[2899] = 22, f[2900] = 23, f[2901] = 24, f[2912] = 25, f[2913] = 26, f[2914] = 27, f[2915] = 28, f[2916] = 29, f[2917] = 30, f[2918] = 31, f[2936] = 14, r = 0 | f[2893]), f[99] = r, bt(12472))
                            }(), 1
                    }

                    function oe(e, r, t, n, o, a, u) {
                        e |= 0, r |= 0, t |= 0, a |= 0, u |= 0;
                        var l, c, d, _, h, m, p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0;
                        if (l = (o |= 0) << 1 | 1, !((0 | (n |= 0)) <= 0))
                            for (o = 0 | A(r, -2), c = 0 - r | 0, d = 0 | f[5], _ = 0 | f[2], h = 0 | f[3], m = 0 | f[4], p = e, e = n; n = e, e = e + -1 | 0, 0 | ae(p, r, l, a) && (b = 0 | s[(v = p + o | 0) >> 0], S = 0 | s[(w = p + c | 0) >> 0], (0 | s[d + (b - S) >> 0]) <= (0 | u) ? (M = 255 & (E = 0 | i[(k = p + r | 0) >> 0]), y = 0 | i[p >> 0], (0 | s[d + (M - (L = 255 & y)) >> 0]) > (0 | u) ? (g = y, T = E, D = 7) : (y = 0 | i[h + (4 + (E = 3 * (L - S | 0) | 0) >> 3) >> 0], C = 0 | i[h + (E + 3 >> 3) >> 0], E = y + 1 >> 1, i[v >> 0] = 0 | i[m + (E + b) >> 0], i[w >> 0] = 0 | i[m + (C + S) >> 0], i[p >> 0] = 0 | i[m + (L - y) >> 0], P = k, R = M - E | 0)) : (g = 0 | i[p >> 0], T = 0 | i[p + r >> 0], D = 7), 7 == (0 | D) && (D = 0, E = 255 & g, M = (0 | i[_ + (b - (255 & T)) >> 0]) + (3 * (E - S | 0) | 0) | 0, b = 0 | i[h + (M + 4 >> 3) >> 0], i[w >> 0] = 0 | i[m + ((0 | i[h + (M + 3 >> 3) >> 0]) + S) >> 0], P = p, R = E - b | 0), i[P >> 0] = 0 | i[m + R >> 0]), !((0 | n) <= 1);) p = p + t | 0
                    }

                    function ae(e, r, t, n) {
                        t |= 0, n |= 0;
                        var i, o, a, u, l, c, d, _ = 0;
                        return _ = (e |= 0) + (0 | A(r |= 0, -3)) | 0, i = 0 | s[_ >> 0], _ = e + (0 | A(r, -2)) | 0, o = 0 | s[_ >> 0], _ = 0 | s[e + (0 - r) >> 0], a = 0 | s[e >> 0], u = 0 | s[e + r >> 0], l = 0 | s[e + (r << 1) >> 0], c = 0 | s[e + (3 * r | 0) >> 0], d = 0 | f[5], (((0 | s[d + (_ - a) >> 0]) << 2) + (0 | s[d + (o - u) >> 0]) | 0) > (0 | t) ? 0 : (t = e + (0 | A(r, -4)) | 0, 0 | 1 & ((0 | s[d + ((0 | s[t >> 0]) - i) >> 0]) <= (0 | n) && (0 | s[d + (i - o) >> 0]) <= (0 | n) && (0 | s[d + (o - _) >> 0]) <= (0 | n) && (0 | s[d + (c - l) >> 0]) <= (0 | n) && (0 | s[d + (l - u) >> 0]) <= (0 | n) ? (0 | s[d + (u - a) >> 0]) <= (0 | n) : 0))
                    }

                    function ue(e, r, t, n, o, a, u) {
                        e |= 0, r |= 0, t |= 0, a |= 0, u |= 0;
                        var l, c, d, _, h, m, p, v, b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0;
                        if (l = (o |= 0) << 1 | 1, !((0 | (n |= 0)) <= 0))
                            for (o = 0 | A(r, -2), c = 0 - r | 0, d = 0 | f[5], _ = 0 | f[2], h = 0 | f[3], m = 0 | f[4], p = 0 | A(r, -3), v = r << 1, b = e, e = n; n = e, e = e + -1 | 0, 0 | ae(b, r, l, a) && (S = 0 | s[(w = b + o | 0) >> 0], E = 0 | s[(k = b + c | 0) >> 0], (0 | s[d + (S - E) >> 0]) <= (0 | u) ? (L = 255 & (y = 0 | i[(M = b + r | 0) >> 0]), g = 0 | i[b >> 0], (0 | s[d + (L - (T = 255 & g)) >> 0]) > (0 | u) ? (D = g, C = y, P = 7) : (y = b + p | 0, R = 0 | s[(g = b + v | 0) >> 0], B = 63 + (27 * (O = 0 | i[_ + ((0 | i[_ + (S - L) >> 0]) + (3 * (T - E | 0) | 0)) >> 0]) | 0) >> 7, x = 63 + (18 * O | 0) >> 7, N = 63 + (9 * O | 0) >> 7, i[y >> 0] = 0 | i[m + (N + (0 | s[y >> 0])) >> 0], i[w >> 0] = 0 | i[m + (x + S) >> 0], i[k >> 0] = 0 | i[m + (B + E) >> 0], i[b >> 0] = 0 | i[m + (T - B) >> 0], i[M >> 0] = 0 | i[m + (L - x) >> 0], I = g, F = R - N | 0)) : (D = 0 | i[b >> 0], C = 0 | i[b + r >> 0], P = 7), 7 == (0 | P) && (P = 0, N = 255 & D, R = (0 | i[_ + (S - (255 & C)) >> 0]) + (3 * (N - E | 0) | 0) | 0, S = 0 | i[h + (R + 4 >> 3) >> 0], i[k >> 0] = 0 | i[m + ((0 | i[h + (R + 3 >> 3) >> 0]) + E) >> 0], I = b, F = N - S | 0), i[I >> 0] = 0 | i[m + F >> 0]), !((0 | n) <= 1);) b = b + t | 0
                    }

                    function fe(e, r) {
                        var t, n, o, a, u, l = 0;
                        l = (e |= 0) + (0 | A(r |= 0, -2)) | 0, n = 0 | s[(t = e + (0 - r) | 0) >> 0], o = 0 | s[e >> 0], a = (0 | i[(0 | f[2]) + ((0 | s[l >> 0]) - (0 | s[e + r >> 0])) >> 0]) + (3 * (o - n | 0) | 0) | 0, r = 0 | f[3], l = 0 | i[r + (a + 4 >> 3) >> 0], u = 0 | f[4], i[t >> 0] = 0 | i[u + ((0 | i[r + (a + 3 >> 3) >> 0]) + n) >> 0], i[e >> 0] = 0 | i[u + (o - l) >> 0]
                    }

                    function le(e, r) {
                        r |= 0;
                        var t, n = 0,
                            o = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0;
                        for (t = S, S = S + 64 | 0, n = t, o = 0 | a[(e |= 0) >> 1], l = (u = 0 | a[e + 16 >> 1]) + o | 0, c = o - u | 0, d = (35468 * (u = 0 | a[e + 8 >> 1]) >> 16) - (o = 0 | a[e + 24 >> 1]) - (20091 * o >> 16) | 0, _ = (20091 * u >> 16) + u + (35468 * o >> 16) | 0, f[n >> 2] = _ + l, f[n + 4 >> 2] = d + c, f[n + 8 >> 2] = c - d, f[n + 12 >> 2] = l - _, _ = 0 | a[e + 2 >> 1], d = (l = 0 | a[e + 18 >> 1]) + _ | 0, c = _ - l | 0, o = (35468 * (l = 0 | a[e + 10 >> 1]) >> 16) - (_ = 0 | a[e + 26 >> 1]) - (20091 * _ >> 16) | 0, _ = (u = (20091 * l >> 16) + l + (35468 * _ >> 16) | 0) + d | 0, f[n + 16 >> 2] = _, f[n + 20 >> 2] = o + c, f[n + 24 >> 2] = c - o, f[n + 28 >> 2] = d - u, u = 0 | a[e + 4 >> 1], o = (d = 0 | a[e + 20 >> 1]) + u | 0, c = u - d | 0, l = (35468 * (d = 0 | a[e + 12 >> 1]) >> 16) - (u = 0 | a[e + 28 >> 1]) - (20091 * u >> 16) | 0, h = (20091 * d >> 16) + d + (35468 * u >> 16) | 0, f[n + 32 >> 2] = h + o, f[n + 36 >> 2] = l + c, f[n + 40 >> 2] = c - l, f[n + 44 >> 2] = o - h, h = 0 | a[e + 6 >> 1], l = (o = 0 | a[e + 22 >> 1]) + h | 0, c = h - o | 0, e = (35468 * (o = 0 | a[e + 14 >> 1]) >> 16) - (h = 0 | a[e + 30 >> 1]) - (20091 * h >> 16) | 0, u = (20091 * o >> 16) + o + (35468 * h >> 16) | 0, f[n + 48 >> 2] = u + l, f[n + 52 >> 2] = e + c, f[n + 56 >> 2] = c - e, f[n + 60 >> 2] = l - u, u = r, r = 0, l = n, n = _; c = (_ = 4 + (0 | f[l >> 2]) | 0) + (e = 0 | f[l + 32 >> 2]) | 0, h = _ - e | 0, _ = (35468 * n >> 16) - (e = 0 | f[l + 48 >> 2]) - (20091 * e >> 16) | 0, e = ((o = (20091 * n >> 16) + n + (35468 * e >> 16) | 0) + c >> 3) + (0 | s[u >> 0]) | 0, i[u >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, d = (_ + h >> 3) + (0 | s[(e = u + 1 | 0) >> 0]) | 0, i[e >> 0] = d >>> 0 > 255 ? 255 + (d >>> 31) | 0 : d, e = (0 | s[(d = u + 2 | 0) >> 0]) + (h - _ >> 3) | 0, i[d >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, d = (0 | s[(e = u + 3 | 0) >> 0]) + (c - o >> 3) | 0, i[e >> 0] = d >>> 0 > 255 ? 255 + (d >>> 31) | 0 : d, 4 != (0 | (d = r + 1 | 0));) e = 0 | f[l + 20 >> 2], u = u + 32 | 0, r = d, l = l + 4 | 0, n = e;
                        S = t
                    }

                    function se(e, r, t, n) {
                        var i;
                        if (r |= 0, 0 == (0 | (e |= 0)) | 0 == (0 | (t |= 0)) | 512 != (-256 & (n |= 0) | 0)) return 2;
                        i = (n = t) + 40 | 0;
                        do {
                            f[n >> 2] = 0, n = n + 4 | 0
                        } while ((0 | n) < (0 | i));
                        return 0 | ce(e, r, t, t + 4 | 0, t + 8 | 0, t + 12 | 0, t + 16 | 0, 0)
                    }

                    function ce(e, r, t, n, i, o, a, u) {
                        r |= 0, t |= 0, n |= 0, i |= 0, o |= 0, a |= 0, u |= 0;
                        var l, c, d, _, h, m, p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0;
                        if (l = S, S = S + 16 | 0, d = l, _ = e |= 0, f[(c = l + 4 | 0) >> 2] = 0, f[d >> 2] = 0, m = (h = 0 != (0 | u)) ? 0 | f[u + 8 >> 2] : 0, 0 == (0 | e) | r >>> 0 < 12) return S = l, 7;
                        if (r >>> 0 > 11)
                            if (0 | Ne(e, 5465, 4)) M = r, y = _, L = 0, g = 0;
                            else {
                                if (0 | Ne(e + 8 | 0, 5470, 4)) return S = l, 3;
                                if (((v = (0 | s[e + 5 >> 0]) << 8 | 0 | s[e + 4 >> 0] | ((0 | s[e + 7 >> 0]) << 8 | 0 | s[e + 6 >> 0]) << 16) - 12 | 0) >>> 0 > 4294967274) return S = l, 3;
                                if (0 != (0 | m) & v >>> 0 > (r + -8 | 0) >>> 0) return S = l, 7;
                                b = r + -12 | 0, w = e + 12 | 0, k = v, E = 9
                            } else b = r, w = _, k = 0, E = 9;
                        if (9 == (0 | E)) {
                            if (b >>> 0 < 8) return S = l, 7;
                            M = b, y = w, L = k, g = 0 != (0 | k)
                        }
                        if (0 | Ne(k = y, 5475, 4)) D = 0, C = 0, P = 0, R = 0, O = M, B = 0, x = y, N = 0, I = 0;
                        else {
                            if (10 != ((0 | s[k + 5 >> 0]) << 8 | 0 | s[k + 4 >> 0] | ((0 | s[k + 7 >> 0]) << 8 | 0 | s[k + 6 >> 0]) << 16 | 0)) return S = l, 3;
                            if (M >>> 0 < 18) return S = l, 7;
                            if (w = 0 | s[k + 8 >> 0], b = 1 + ((0 | s[k + 13 >> 0]) << 8 | 0 | s[k + 12 >> 0] | (0 | s[k + 14 >> 0]) << 16) | 0, v = 1 + ((0 | s[k + 16 >> 0]) << 8 | 0 | s[k + 15 >> 0] | (0 | s[k + 17 >> 0]) << 16) | 0, e = 0 | A(b, v), !(T = 0 == (0 | v)) && (0 | (e >>> 0) / ((T ? 1 : v) >>> 0)) != (0 | b)) return S = l, 3;
                            if (!g) return S = l, 3;
                            D = w, C = 1, P = v, R = b, O = M + -18 | 0, B = (T = 2 & w) >>> 1, x = k + 18 | 0, N = 0 != (0 | T), I = 1
                        }(y = 0 == (0 | i)) || (f[i >> 2] = D >>> 4 & 1), 0 | o && (f[o >> 2] = B), (o = 0 == (0 | a)) || (f[a >> 2] = 0), f[c >> 2] = R, f[d >> 2] = P, D = 0 == (0 | u);
                        e: do {
                            if (D & I & N) ie = 0;
                            else {
                                r: do {
                                    if (O >>> 0 >= 4) {
                                        g & 0 != (0 | C) ? (F = x, E = 27) : 0 == (C | L | 0) && 0 == (0 | Ne(M = x, 5480, 4)) ? (F = M, E = 27) : (H = O, U = x, G = 0, W = 0);
                                        t: do {
                                            if (27 == (0 | E)) {
                                                if (O >>> 0 < 8) {
                                                    Y = 0;
                                                    break r
                                                }
                                                if (!L) {
                                                    for (M = F, T = O, k = x, b = 0, v = 0;;) {
                                                        if ((w = (0 | s[M + 5 >> 0]) << 8 | 0 | s[M + 4 >> 0] | ((0 | s[M + 7 >> 0]) << 8 | 0 | s[M + 6 >> 0]) << 16) >>> 0 > 4294967286) {
                                                            p = 3;
                                                            break
                                                        }
                                                        if (e = w + 9 & -2, !(0 | Ne(M, 5485, 4))) {
                                                            H = T, U = k, G = b, W = v;
                                                            break t
                                                        }
                                                        if (!(0 | Ne(M, 5490, 4))) {
                                                            H = T, U = k, G = b, W = v;
                                                            break t
                                                        }
                                                        if (T >>> 0 < e >>> 0) {
                                                            Y = v;
                                                            break r
                                                        }
                                                        if (q = (V = 0 == (0 | Ne(M, 5480, 4))) ? M + 8 | 0 : v, j = M + e | 0, (z = T - e | 0) >>> 0 < 8) {
                                                            Y = q;
                                                            break r
                                                        }
                                                        M = j, T = z, k = j, b = V ? w : b, v = q
                                                    }
                                                    return S = l, 0 | p
                                                }
                                                for (X = F, K = 22, $ = O, J = x, Q = 0, Z = 0;;) {
                                                    if ((v = (0 | s[X + 5 >> 0]) << 8 | 0 | s[X + 4 >> 0] | ((0 | s[X + 7 >> 0]) << 8 | 0 | s[X + 6 >> 0]) << 16) >>> 0 > 4294967286) {
                                                        p = 3, E = 65;
                                                        break
                                                    }
                                                    if ((k = (b = v + 9 & -2) + K | 0) >>> 0 > L >>> 0) {
                                                        p = 3, E = 65;
                                                        break
                                                    }
                                                    if (!(0 | Ne(X, 5485, 4))) {
                                                        H = $, U = J, G = Q, W = Z;
                                                        break t
                                                    }
                                                    if (!(0 | Ne(X, 5490, 4))) {
                                                        H = $, U = J, G = Q, W = Z;
                                                        break t
                                                    }
                                                    if ($ >>> 0 < b >>> 0) {
                                                        Y = Z;
                                                        break r
                                                    }
                                                    if (M = (T = 0 == (0 | Ne(X, 5480, 4))) ? X + 8 | 0 : Z, q = X + b | 0, (w = $ - b | 0) >>> 0 < 8) {
                                                        Y = M;
                                                        break r
                                                    }
                                                    X = q, K = k, $ = w, J = q, Q = T ? v : Q, Z = M
                                                }
                                                if (65 == (0 | E)) return S = l, 0 | p
                                            }
                                        } while (0);
                                        if (T = 1 & (v = 0 == (0 | Ne(M = U, 5490, 4))), !(H >>> 0 < 8)) {
                                            if (v | 0 == (0 | Ne(M, 5485, 4))) {
                                                if (L >>> 0 > 11 & (v = (0 | s[M + 5 >> 0]) << 8 | 0 | s[M + 4 >> 0] | ((0 | s[M + 7 >> 0]) << 8 | 0 | s[M + 6 >> 0]) << 16) >>> 0 > (L + -12 | 0) >>> 0) return S = l, 3;
                                                if (0 != (0 | m) & v >>> 0 > (q = H + -8 | 0) >>> 0) {
                                                    Y = W;
                                                    break
                                                }
                                                ee = q, re = M + 8 | 0, te = v, ne = T
                                            } else ee = H, re = U, te = H, ne = 0 | de(M, H);
                                            if (te >>> 0 > 4294967286) return S = l, 3;
                                            if (M = 0 != (0 | ne), o | 0 != (0 | B) || (f[a >> 2] = M ? 2 : 1), M) {
                                                if (ee >>> 0 < 5) {
                                                    Y = W;
                                                    break
                                                }
                                                if (!(0 | he(re, ee, c, d, i))) return S = l, 3
                                            } else {
                                                if (ee >>> 0 < 10) {
                                                    Y = W;
                                                    break
                                                }
                                                if (!(0 | _e(re, ee, te, c, d))) return S = l, 3
                                            }
                                            if (0 | C && ((0 | R) != (0 | f[c >> 2]) || (0 | P) != (0 | f[d >> 2]))) return S = l, 3;
                                            if (!h) {
                                                ie = W;
                                                break e
                                            }
                                            f[u >> 2] = _, f[u + 4 >> 2] = r, f[(M = u + 8 | 0) >> 2] = 0, f[M + 4 >> 2] = 0, f[u + 16 >> 2] = W, f[u + 20 >> 2] = G, f[u + 24 >> 2] = te, f[u + 28 >> 2] = L, f[u + 32 >> 2] = ne, f[u + 12 >> 2] = re - _, ie = W;
                                            break e
                                        }
                                        Y = W
                                    } else Y = 0
                                } while (0);
                                if (!(D & 0 != (0 | C))) return S = l, 7;ie = Y
                            }
                        } while (0);
                        return y || (f[i >> 2] = f[i >> 2] | 0 != (0 | ie)), 0 | t && (f[t >> 2] = f[c >> 2]), n ? (f[n >> 2] = f[d >> 2], S = l, 0 | (p = 0)) : (S = l, 0 | (p = 0))
                    }

                    function de(e, r) {
                        return e |= 0, 1 & ((r |= 0) >>> 0 > 4 && 47 == (0 | i[e >> 0]) ? (0 | s[e + 4 >> 0]) < 32 : 0) | 0
                    }

                    function _e(e, r, t, n, o) {
                        var a, u;
                        return t |= 0, n |= 0, o |= 0, (r |= 0) >>> 0 > 9 & 0 != (0 | (e |= 0)) & (r + -3 | 0) >>> 0 > 2 ? -99 != (0 | i[e + 3 >> 0]) ? 0 : 1 != (0 | i[e + 4 >> 0]) ? 0 : 42 != (0 | i[e + 5 >> 0]) ? 0 : (r = 0 | s[e >> 0], a = s[e + 7 >> 0] << 8 & 16128 | s[e + 6 >> 0], u = s[e + 9 >> 0] << 8 & 16128 | s[e + 8 >> 0], (8 & r) >>> 0 < 7 & 16 == (17 & r | 0) & (s[e + 1 >> 0] << 8 | r | s[e + 2 >> 0] << 16) >>> 5 >>> 0 < t >>> 0 ? 0 == (0 | a) | 0 == (0 | u) ? 0 : (0 | n && (f[n >> 2] = a), o ? (f[o >> 2] = u, 1) : 1) : 0) : 0
                    }

                    function he(e, r, t, n, o) {
                        t |= 0, n |= 0, o |= 0;
                        var a, u, l = 0,
                            c = 0;
                        return a = S, S = S + 32 | 0, u = a, 0 != (0 | (e |= 0)) & (r |= 0) >>> 0 > 4 ? 47 != (0 | i[e >> 0]) ? (S = a, 0) : (0 | s[e + 4 >> 0]) >= 32 ? (S = a, 0) : (_r(u, e, r), 47 == (0 | ur(u, 8)) && (r = 1 + (0 | ur(u, 14)) | 0, e = 1 + (0 | ur(u, 14)) | 0, l = 0 | ur(u, 1), 0 == (0 | ur(u, 3))) && 0 == (0 | f[u + 24 >> 2]) ? (0 | t && (f[t >> 2] = r), 0 | n && (f[n >> 2] = e), o ? (f[o >> 2] = l, c = 1) : c = 1) : c = 0, S = a, 0 | c) : (S = a, 0)
                    }

                    function me(e, r) {
                        r |= 0;
                        var t, n, o, u, l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0;
                        if (!(e |= 0)) return 0;
                        if (f[e >> 2] = 0, f[(t = e + 8 | 0) >> 2] = 6413, !r) return f[e >> 2] = 2, f[t >> 2] = 6416, f[e + 4 >> 2] = 0, 0;
                        if (n = 0 | f[r + 64 >> 2], (l = 0 | f[r + 60 >> 2]) >>> 0 < 4) return f[e >> 2] = 7, f[t >> 2] = 6453, f[e + 4 >> 2] = 0, 0;
                        if (c = 0 | s[n >> 0], d = s[n + 1 >> 0] << 8 | c | s[n + 2 >> 0] << 16, o = 255 & (1 & c ^ 1), i[(u = e + 40 | 0) >> 0] = o, _ = c >>> 1 & 7, i[e + 41 >> 0] = _, h = c >>> 4 & 1, i[e + 42 >> 0] = h, c = d >>> 5, f[(d = e + 44 | 0) >> 2] = c, (255 & _) > 3) return f[e >> 2] = 3, f[t >> 2] = 6471, f[e + 4 >> 2] = 0, 0;
                        if (!(h << 24 >> 24)) return f[e >> 2] = 4, f[t >> 2] = 6502, f[e + 4 >> 2] = 0, 0;
                        h = n + 3 | 0, _ = l + -3 | 0;
                        do {
                            if (o << 24 >> 24) {
                                if (_ >>> 0 < 7) return f[e >> 2] = 7, f[t >> 2] = 6525, f[e + 4 >> 2] = 0, 0;
                                if (-99 == (0 | i[h >> 0]) && 1 == (0 | i[n + 4 >> 0]) && 42 == (0 | i[n + 5 >> 0])) {
                                    w = s[(b = n + 7 | 0) >> 0] << 8 & 16128 | s[n + 6 >> 0], a[e + 48 >> 1] = w, i[e + 52 >> 0] = (0 | s[b >> 0]) >>> 6, S = s[(b = n + 9 | 0) >> 0] << 8 & 16128 | s[n + 8 >> 0], a[e + 50 >> 1] = S, i[e + 53 >> 0] = (0 | s[b >> 0]) >>> 6, f[e + 288 >> 2] = (w + 15 | 0) >>> 4, f[e + 292 >> 2] = (S + 15 | 0) >>> 4, f[r >> 2] = w, f[r + 4 >> 2] = S, f[r + 72 >> 2] = 0, f[r + 84 >> 2] = 0, f[r + 76 >> 2] = 0, f[r + 80 >> 2] = w, f[r + 88 >> 2] = S, f[r + 92 >> 2] = 0, f[r + 96 >> 2] = w, f[r + 100 >> 2] = S, f[r + 12 >> 2] = w, f[r + 16 >> 2] = S, Ee(e + 904 | 0), f[e + 104 >> 2] = 0, f[e + 108 >> 2] = 0, f[e + 112 >> 2] = 1, f[e + 116 >> 2] = 0, f[e + 120 >> 2] = 0, m = n + 10 | 0, p = l + -10 | 0, v = 0 | f[d >> 2];
                                    break
                                }
                                return f[e >> 2] = 3, f[t >> 2] = 6553, f[e + 4 >> 2] = 0, 0
                            }
                            m = h, p = _, v = c
                        } while (0);
                        if (v >>> 0 > p >>> 0) return 0 | f[e >> 2] ? 0 : (f[e >> 2] = 7, f[t >> 2] = 6567, f[e + 4 >> 2] = 0, 0);
                        if (Me(l = e + 12 | 0, m, v), d = m + (v = 0 | f[d >> 2]) | 0, m = p - v | 0, 0 | i[u >> 0] && (v = 255 & (0 | Ae(l, 1)), i[e + 54 >> 0] = v, v = 255 & (0 | Ae(l, 1)), i[e + 55 >> 0] = v), v = 0 | Ae(l, 1), f[e + 104 >> 2] = v, v ? (v = 0 | Ae(l, 1), f[(p = e + 108 | 0) >> 2] = v, 0 | Ae(l, 1) && (v = 0 | Ae(l, 1), f[e + 112 >> 2] = v, k = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 7)) : 0, i[e + 116 >> 0] = k, E = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 7)) : 0, i[e + 117 >> 0] = E, M = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 7)) : 0, i[e + 118 >> 0] = M, L = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 7)) : 0, i[e + 119 >> 0] = L, g = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 6)) : 0, i[e + 120 >> 0] = g, T = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 6)) : 0, i[e + 121 >> 0] = T, D = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 6)) : 0, i[e + 122 >> 0] = D, C = 0 | Ae(l, 1) ? 255 & (0 | ye(l, 6)) : 0, i[e + 123 >> 0] = C), 0 | f[p >> 2] && (P = 0 | Ae(l, 1) ? 255 & (0 | Ae(l, 8)) : -1, i[e + 904 >> 0] = P, R = 0 | Ae(l, 1) ? 255 & (0 | Ae(l, 8)) : -1, i[e + 905 >> 0] = R, O = 0 | Ae(l, 1) ? 255 & (0 | Ae(l, 8)) : -1, i[e + 906 >> 0] = O)) : f[e + 108 >> 2] = 0, 0 | f[(O = e + 36 | 0) >> 2]) return 0 | f[e >> 2] ? 0 : (f[e >> 2] = 3, f[t >> 2] = 6588, f[e + 4 >> 2] = 0, 0);
                        R = 0 | Ae(l, 1), f[(P = e + 56 | 0) >> 2] = R, R = 0 | Ae(l, 6), f[(p = e + 60 | 0) >> 2] = R, R = 0 | Ae(l, 3), f[e + 64 >> 2] = R, R = 0 | Ae(l, 1), f[e + 68 >> 2] = R;
                        do {
                            if (0 | R && 0 | Ae(l, 1)) {
                                if (0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 72 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 76 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 80 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 84 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 88 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 92 >> 2] = C), 0 | Ae(l, 1) && (C = 0 | ye(l, 6), f[e + 96 >> 2] = C), !(0 | Ae(l, 1))) break;
                                C = 0 | ye(l, 6), f[e + 100 >> 2] = C
                            }
                        } while (0);
                        if (B = 0 | f[p >> 2] ? 0 | f[P >> 2] ? 1 : 2 : 0, f[e + 2308 >> 2] = B, 0 | f[O >> 2]) return 0 | f[e >> 2] ? 0 : (f[e >> 2] = 3, f[t >> 2] = 6616, f[e + 4 >> 2] = 0, 0);
                        if (O = d + m | 0, B = (1 << (0 | Ae(l, 2))) - 1 | 0, f[e + 312 >> 2] = B, m >>> 0 >= (P = 3 * B | 0) >>> 0) {
                            if (p = d + P | 0, R = m - P | 0, B)
                                for (P = 0, m = R, R = p, p = d;;) {
                                    if (Me(e + 316 + (28 * P | 0) | 0, R, C = (d = s[p + 1 >> 0] << 8 | s[p >> 0] | s[p + 2 >> 0] << 16) >>> 0 > m >>> 0 ? m : d), d = R + C | 0, D = m - C | 0, (0 | (P = P + 1 | 0)) == (0 | B)) {
                                        x = D, N = d;
                                        break
                                    }
                                    m = D, R = d, p = p + 3 | 0
                                } else x = R, N = p;
                            if (Me(e + 316 + (28 * B | 0) | 0, N, x), N >>> 0 < O >>> 0) return Le(e), 0 | i[u >> 0] ? (Ae(l, 1), function(e, r) {
                                r |= 0;
                                var t, n, o, a, u = 0,
                                    l = 0,
                                    c = 0,
                                    d = 0,
                                    _ = 0,
                                    h = 0,
                                    m = 0,
                                    p = 0,
                                    v = 0,
                                    b = 0,
                                    w = 0,
                                    S = 0;
                                t = 4 + (e |= 0) | 0, n = e + 8 | 0, o = e + 12 | 0, a = e + 20 | 0, u = 0;
                                do {
                                    l = 0;
                                    do {
                                        c = 0;
                                        do {
                                            d = 0;
                                            do {
                                                _ = 0 | s[6684 + (264 * u | 0) + (33 * l | 0) + (11 * c | 0) + d >> 0], h = 0 | f[t >> 2], m = 0 | f[n >> 2];
                                                do {
                                                    if ((0 | m) < 0) {
                                                        if ((p = 0 | f[o >> 2]) >>> 0 < (0 | f[a >> 2]) >>> 0) {
                                                            v = s[p >> 0] | s[p + 1 >> 0] << 8 | s[p + 2 >> 0] << 16 | s[p + 3 >> 0] << 24, f[o >> 2] = p + 3, p = (0 | wt(0 | v)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | p, p = m + 24 | 0, f[n >> 2] = p, b = p;
                                                            break
                                                        }
                                                        ke(e), b = 0 | f[n >> 2];
                                                        break
                                                    }
                                                    b = m
                                                } while (0);
                                                v = (m = (0 | A(h, _)) >>> 8) + 1 | 0, (p = 0 | f[e >> 2]) >>> b >>> 0 > m >>> 0 ? (w = h - m | 0, f[e >> 2] = p - (v << b), p = 24 ^ (0 | y(0 | w)), f[n >> 2] = b - p, f[t >> 2] = (w << p) - 1, S = 0 | Ae(e, 8)) : (p = 24 ^ (0 | y(0 | v)), f[n >> 2] = b - p, f[t >> 2] = (v << p) - 1, S = 0 | s[7740 + (264 * u | 0) + (33 * l | 0) + (11 * c | 0) + d >> 0]), i[r + 907 + (264 * u | 0) + (33 * l | 0) + (11 * c | 0) + d >> 0] = S, d = d + 1 | 0
                                            } while (11 != (0 | d));
                                            c = c + 1 | 0
                                        } while (3 != (0 | c));
                                        l = l + 1 | 0
                                    } while (8 != (0 | l));
                                    l = r + 907 + (264 * u | 0) | 0, f[r + 1964 + (68 * u | 0) >> 2] = l, f[r + 1964 + (68 * u | 0) + 4 >> 2] = r + 907 + (264 * u | 0) + 33, f[r + 1964 + (68 * u | 0) + 8 >> 2] = r + 907 + (264 * u | 0) + 66, f[r + 1964 + (68 * u | 0) + 12 >> 2] = r + 907 + (264 * u | 0) + 99, c = r + 907 + (264 * u | 0) + 198 | 0, f[r + 1964 + (68 * u | 0) + 16 >> 2] = c, f[r + 1964 + (68 * u | 0) + 20 >> 2] = r + 907 + (264 * u | 0) + 132, f[r + 1964 + (68 * u | 0) + 24 >> 2] = r + 907 + (264 * u | 0) + 165, f[r + 1964 + (68 * u | 0) + 28 >> 2] = c, f[r + 1964 + (68 * u | 0) + 32 >> 2] = c, f[r + 1964 + (68 * u | 0) + 36 >> 2] = c, f[r + 1964 + (68 * u | 0) + 40 >> 2] = c, f[r + 1964 + (68 * u | 0) + 44 >> 2] = c, f[r + 1964 + (68 * u | 0) + 48 >> 2] = c, f[r + 1964 + (68 * u | 0) + 52 >> 2] = c, f[r + 1964 + (68 * u | 0) + 56 >> 2] = c, f[r + 1964 + (68 * u | 0) + 60 >> 2] = r + 907 + (264 * u | 0) + 231, f[r + 1964 + (68 * u | 0) + 64 >> 2] = l, u = u + 1 | 0
                                } while (4 != (0 | u));
                                u = 0 | Ae(e, 1), f[r + 2236 >> 2] = u, u && (u = 255 & (0 | Ae(e, 8)), i[r + 2240 >> 0] = u)
                            }(l, e), f[e + 4 >> 2] = 1, 1) : 0 | f[e >> 2] ? 0 : (f[e >> 2] = 4, f[t >> 2] = 6667, f[e + 4 >> 2] = 0, 0);
                            I = 5
                        } else I = 7;
                        return 0 | f[e >> 2] ? 0 : (f[e >> 2] = I, f[t >> 2] = 6643, f[e + 4 >> 2] = 0, 0)
                    }

                    function pe(e, r) {
                        e |= 0;
                        var t, n, o, a, u, l, c, d, _, h, m, p, v, b, w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0,
                            Pe = 0,
                            Re = 0,
                            Oe = 0,
                            Be = 0,
                            xe = 0,
                            Ne = 0,
                            Ie = 0,
                            Fe = 0,
                            He = 0;
                        if ((0 | f[(t = 288 + (r |= 0) | 0) >> 2]) <= 0) return 0 | 1 & 0 == (0 | f[(r + 36 | 0) >> 2]);
                        n = r + 2244 | 0, o = r + 2248 | 0, a = r + 2304 | 0, u = r + 108 | 0, l = r + 2236 | 0, c = e + 4 | 0, d = e + 8 | 0, _ = e + 12 | 0, h = e + 20 | 0, m = r + 2240 | 0, p = r + 904 | 0, v = r + 906 | 0, b = r + 905 | 0, w = 0;
                        do {
                            if (S = (0 | f[n >> 2]) + (w << 2) | 0, k = 0 | f[a >> 2], 0 | f[u >> 2]) {
                                M = 0 | s[p >> 0], L = 0 | f[c >> 2], g = 0 | f[d >> 2];
                                do {
                                    if ((0 | g) < 0) {
                                        if ((T = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                            D = s[T >> 0] | s[T + 1 >> 0] << 8 | s[T + 2 >> 0] << 16 | s[T + 3 >> 0] << 24, f[_ >> 2] = T + 3, T = (0 | wt(0 | D)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | T, T = g + 24 | 0, f[d >> 2] = T, C = T;
                                            break
                                        }
                                        ke(e), C = 0 | f[d >> 2];
                                        break
                                    }
                                    C = g
                                } while (0);
                                if (g = (0 | A(L, M)) >>> 8, R = (T = 0 | f[e >> 2]) - ((P = g + 1 | 0) << C) | 0, (D = T >>> C >>> 0 > g >>> 0) ? (f[e >> 2] = R, O = L - g | 0, B = R) : (O = P, B = T), P = C - (T = 24 ^ (0 | y(0 | O))) | 0, f[d >> 2] = P, R = (O << T) - 1 | 0, f[c >> 2] = R, D) {
                                    D = 0 | s[v >> 0];
                                    do {
                                        if ((0 | P) < 0) {
                                            if ((T = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                g = s[T >> 0] | s[T + 1 >> 0] << 8 | s[T + 2 >> 0] << 16 | s[T + 3 >> 0] << 24, f[_ >> 2] = T + 3, T = (0 | wt(0 | g)) >>> 8 | B << 24, f[e >> 2] = T, g = P + 24 | 0, f[d >> 2] = g, x = T, N = g;
                                                break
                                            }
                                            ke(e), x = 0 | f[e >> 2], N = 0 | f[d >> 2];
                                            break
                                        }
                                        x = B, N = P
                                    } while (0);
                                    g = 1 + (L = (0 | A(D, R)) >>> 8) | 0, (M = x >>> N >>> 0 > L >>> 0) ? (f[e >> 2] = x - (g << N), I = R - L | 0) : I = g, g = 24 ^ (0 | y(0 | I)), f[d >> 2] = N - g, f[c >> 2] = (I << g) - 1, F = M ? 3 : 2
                                } else {
                                    M = 0 | s[b >> 0];
                                    do {
                                        if ((0 | P) < 0) {
                                            if ((g = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                L = s[g >> 0] | s[g + 1 >> 0] << 8 | s[g + 2 >> 0] << 16 | s[g + 3 >> 0] << 24, f[_ >> 2] = g + 3, g = (0 | wt(0 | L)) >>> 8 | B << 24, f[e >> 2] = g, L = P + 24 | 0, f[d >> 2] = L, H = g, U = L;
                                                break
                                            }
                                            ke(e), H = 0 | f[e >> 2], U = 0 | f[d >> 2];
                                            break
                                        }
                                        H = B, U = P
                                    } while (0);
                                    L = 1 + (P = (0 | A(M, R)) >>> 8) | 0, (D = H >>> U >>> 0 > P >>> 0) ? (f[e >> 2] = H - (L << U), G = R - P | 0) : G = L, L = 24 ^ (0 | y(0 | G)), f[d >> 2] = U - L, f[c >> 2] = (G << L) - 1, F = 1 & D
                                }
                                E = 255 & F
                            } else E = 0;
                            if (i[k + (800 * w | 0) + 798 >> 0] = E, 0 | f[l >> 2]) {
                                D = 0 | s[m >> 0], L = 0 | f[c >> 2], P = 0 | f[d >> 2];
                                do {
                                    if ((0 | P) < 0) {
                                        if ((g = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                            T = s[g >> 0] | s[g + 1 >> 0] << 8 | s[g + 2 >> 0] << 16 | s[g + 3 >> 0] << 24, f[_ >> 2] = g + 3, g = (0 | wt(0 | T)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | g, g = P + 24 | 0, f[d >> 2] = g, V = g;
                                            break
                                        }
                                        ke(e), V = 0 | f[d >> 2];
                                        break
                                    }
                                    V = P
                                } while (0);
                                g = 1 + (P = (0 | A(L, D)) >>> 8) | 0, (M = (R = 0 | f[e >> 2]) >>> V >>> 0 > P >>> 0) ? (f[e >> 2] = R - (g << V), q = L - P | 0) : q = g, P = V - (g = 24 ^ (0 | y(0 | q))) | 0, f[d >> 2] = P, R = (q << g) - 1 | 0, f[c >> 2] = R, i[k + (800 * w | 0) + 797 >> 0] = 1 & M, W = P, Y = R
                            } else W = 0 | f[d >> 2], Y = 0 | f[c >> 2];
                            do {
                                if ((0 | W) < 0) {
                                    if ((R = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                        P = s[R >> 0] | s[R + 1 >> 0] << 8 | s[R + 2 >> 0] << 16 | s[R + 3 >> 0] << 24, f[_ >> 2] = R + 3, R = (0 | wt(0 | P)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | R, R = W + 24 | 0, f[d >> 2] = R, j = R;
                                        break
                                    }
                                    ke(e), j = 0 | f[d >> 2];
                                    break
                                }
                                j = W
                            } while (0);
                            if (L = (145 * Y | 0) >>> 8, M = (D = 0 | f[e >> 2]) - ((P = L + 1 | 0) << j) | 0, (R = D >>> j >>> 0 > L >>> 0) ? (f[e >> 2] = M, z = Y - L | 0, X = M) : (z = P, X = D), P = j - (D = 24 ^ (0 | y(0 | z))) | 0, f[d >> 2] = P, M = (z << D) - 1 | 0, f[c >> 2] = M, i[k + (800 * w | 0) + 768 >> 0] = 1 & (1 ^ R), R) {
                                do {
                                    if ((0 | P) < 0) {
                                        if ((R = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                            D = s[R >> 0] | s[R + 1 >> 0] << 8 | s[R + 2 >> 0] << 16 | s[R + 3 >> 0] << 24, f[_ >> 2] = R + 3, R = (0 | wt(0 | D)) >>> 8 | X << 24, f[e >> 2] = R, D = P + 24 | 0, f[d >> 2] = D, K = R, $ = D;
                                            break
                                        }
                                        ke(e), K = 0 | f[e >> 2], $ = 0 | f[d >> 2];
                                        break
                                    }
                                    K = X, $ = P
                                } while (0);
                                if (L = K - ((R = 1 + (P = (156 * M | 0) >>> 8) | 0) << $) | 0, (D = K >>> $ >>> 0 > P >>> 0) ? (f[e >> 2] = L, J = M - P | 0, Q = L) : (J = R, Q = K), L = $ - (R = 24 ^ (0 | y(0 | J))) | 0, f[d >> 2] = L, P = (J << R) - 1 | 0, f[c >> 2] = P, R = (0 | L) < 0, D) {
                                    do {
                                        if (R) {
                                            if ((D = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                g = s[D >> 0] | s[D + 1 >> 0] << 8 | s[D + 2 >> 0] << 16 | s[D + 3 >> 0] << 24, f[_ >> 2] = D + 3, D = (0 | wt(0 | g)) >>> 8 | Q << 24, f[e >> 2] = D, g = L + 24 | 0, f[d >> 2] = g, Z = D, ee = g;
                                                break
                                            }
                                            ke(e), Z = 0 | f[e >> 2], ee = 0 | f[d >> 2];
                                            break
                                        }
                                        Z = Q, ee = L
                                    } while (0);
                                    D = 1 + (M = P >>> 1 & 16777215) | 0, (g = Z >>> ee >>> 0 > M >>> 0) ? (f[e >> 2] = Z - (D << ee), re = P - M | 0) : re = D, D = 24 ^ (0 | y(0 | re)), f[d >> 2] = ee - D, f[c >> 2] = (re << D) - 1, te = g ? 1 : 3
                                } else {
                                    do {
                                        if (R) {
                                            if ((g = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                D = s[g >> 0] | s[g + 1 >> 0] << 8 | s[g + 2 >> 0] << 16 | s[g + 3 >> 0] << 24, f[_ >> 2] = g + 3, g = (0 | wt(0 | D)) >>> 8 | Q << 24, f[e >> 2] = g, D = L + 24 | 0, f[d >> 2] = D, ne = g, ie = D;
                                                break
                                            }
                                            ke(e), ne = 0 | f[e >> 2], ie = 0 | f[d >> 2];
                                            break
                                        }
                                        ne = Q, ie = L
                                    } while (0);
                                    D = 1 + (L = (163 * P | 0) >>> 8) | 0, (R = ne >>> ie >>> 0 > L >>> 0) ? (f[e >> 2] = ne - (D << ie), oe = P - L | 0) : oe = D, D = 24 ^ (0 | y(0 | oe)), f[d >> 2] = ie - D, f[c >> 2] = (oe << D) - 1, te = R ? 2 : 0
                                }
                                R = 255 & te, i[k + (800 * w | 0) + 769 >> 0] = R, at(0 | S, 0 | R, 4), at(0 | o, 0 | R, 4)
                            } else
                                for (R = k + (800 * w | 0) + 769 | 0, D = 0;;) {
                                    g = 0, M = 0 | s[(L = r + 2248 + D | 0) >> 0];
                                    do {
                                        ae = 0 | s[(T = S + g | 0) >> 0], ue = 0 | s[5495 + (90 * ae | 0) + (9 * M | 0) >> 0], fe = 0 | f[c >> 2], le = 0 | f[d >> 2];
                                        do {
                                            if ((0 | le) < 0) {
                                                if ((se = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                    ce = s[se >> 0] | s[se + 1 >> 0] << 8 | s[se + 2 >> 0] << 16 | s[se + 3 >> 0] << 24, f[_ >> 2] = se + 3, se = (0 | wt(0 | ce)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | se, se = le + 24 | 0, f[d >> 2] = se, de = se;
                                                    break
                                                }
                                                ke(e), de = 0 | f[d >> 2];
                                                break
                                            }
                                            de = le
                                        } while (0);
                                        if (le = (0 | A(fe, ue)) >>> 8, _e = 1 & (ce = (se = 0 | f[e >> 2]) >>> de >>> 0 > le >>> 0), me = se - ((he = le + 1 | 0) << de) | 0, ce ? (f[e >> 2] = me, pe = fe - le | 0, ve = me) : (pe = he, ve = se), he = de - (se = 24 ^ (0 | y(0 | pe))) | 0, f[d >> 2] = he, me = (pe << se) - 1 | 0, f[c >> 2] = me, se = 0 | i[6395 + _e >> 0], 41706 >>> _e & 1)
                                            for (_e = se, se = he, he = ve, le = me;;) {
                                                me = _e << 1, ce = 0 | s[5495 + (90 * ae | 0) + (9 * M | 0) + _e >> 0];
                                                do {
                                                    if ((0 | se) < 0) {
                                                        if ((we = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                            Se = s[we >> 0] | s[we + 1 >> 0] << 8 | s[we + 2 >> 0] << 16 | s[we + 3 >> 0] << 24, f[_ >> 2] = we + 3, we = (0 | wt(0 | Se)) >>> 8 | he << 24, f[e >> 2] = we, Se = se + 24 | 0, f[d >> 2] = Se, Ee = we, Me = Se;
                                                            break
                                                        }
                                                        ke(e), Ee = 0 | f[e >> 2], Me = 0 | f[d >> 2];
                                                        break
                                                    }
                                                    Ee = he, Me = se
                                                } while (0);
                                                if (ye = Ee - ((Ae = 1 + (Se = (0 | A(ce, le)) >>> 8) | 0) << Me) | 0, (we = Ee >>> Me >>> 0 > Se >>> 0) ? (f[e >> 2] = ye, Le = le - Se | 0, ge = ye) : (Le = Ae, ge = Ee), se = Me - (Ae = 24 ^ (0 | y(0 | Le))) | 0, f[d >> 2] = se, le = (Le << Ae) - 1 | 0, f[c >> 2] = le, we = 0 | i[6395 + (Ae = 1 & we | me) >> 0], !(41706 >>> Ae & 1)) {
                                                    be = we;
                                                    break
                                                }
                                                _e = we, he = ge
                                            } else be = se;
                                        Te = 255 & (M = 0 - be | 0), i[T >> 0] = Te, g = g + 1 | 0
                                    } while (4 != (0 | g));
                                    if (g = s[S >> 0] | s[S + 1 >> 0] << 8 | s[S + 2 >> 0] << 16 | s[S + 3 >> 0] << 24, i[R >> 0] = g, i[R + 1 >> 0] = g >> 8, i[R + 2 >> 0] = g >> 16, i[R + 3 >> 0] = g >> 24, i[L >> 0] = Te, 4 == (0 | (D = D + 1 | 0))) break;
                                    R = R + 4 | 0
                                }
                            R = 0 | f[c >> 2], D = 0 | f[d >> 2];
                            do {
                                if ((0 | D) < 0) {
                                    if ((S = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                        P = s[S >> 0] | s[S + 1 >> 0] << 8 | s[S + 2 >> 0] << 16 | s[S + 3 >> 0] << 24, f[_ >> 2] = S + 3, S = (0 | wt(0 | P)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | S, S = D + 24 | 0, f[d >> 2] = S, De = S;
                                        break
                                    }
                                    ke(e), De = 0 | f[d >> 2];
                                    break
                                }
                                De = D
                            } while (0);
                            if (D = (142 * R | 0) >>> 8, M = (S = 0 | f[e >> 2]) - ((g = D + 1 | 0) << De) | 0, (P = S >>> De >>> 0 > D >>> 0) ? (f[e >> 2] = M, Ce = R - D | 0, Pe = M) : (Ce = g, Pe = S), g = De - (S = 24 ^ (0 | y(0 | Ce))) | 0, f[d >> 2] = g, M = (Ce << S) - 1 | 0, f[c >> 2] = M, P) {
                                do {
                                    if ((0 | g) < 0) {
                                        if ((P = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                            S = s[P >> 0] | s[P + 1 >> 0] << 8 | s[P + 2 >> 0] << 16 | s[P + 3 >> 0] << 24, f[_ >> 2] = P + 3, P = (0 | wt(0 | S)) >>> 8 | Pe << 24, f[e >> 2] = P, S = g + 24 | 0, f[d >> 2] = S, Re = P, Oe = S;
                                            break
                                        }
                                        ke(e), Re = 0 | f[e >> 2], Oe = 0 | f[d >> 2];
                                        break
                                    }
                                    Re = Pe, Oe = g
                                } while (0);
                                if (P = Re - ((S = 1 + (g = (114 * M | 0) >>> 8) | 0) << Oe) | 0, (R = Re >>> Oe >>> 0 > g >>> 0) ? (f[e >> 2] = P, Be = M - g | 0, xe = P) : (Be = S, xe = Re), P = Oe - (S = 24 ^ (0 | y(0 | Be))) | 0, f[d >> 2] = P, g = (Be << S) - 1 | 0, f[c >> 2] = g, R) {
                                    do {
                                        if ((0 | P) < 0) {
                                            if ((R = 0 | f[_ >> 2]) >>> 0 < (0 | f[h >> 2]) >>> 0) {
                                                S = s[R >> 0] | s[R + 1 >> 0] << 8 | s[R + 2 >> 0] << 16 | s[R + 3 >> 0] << 24, f[_ >> 2] = R + 3, R = (0 | wt(0 | S)) >>> 8 | xe << 24, f[e >> 2] = R, S = P + 24 | 0, f[d >> 2] = S, Ne = R, Ie = S;
                                                break
                                            }
                                            ke(e), Ne = 0 | f[e >> 2], Ie = 0 | f[d >> 2];
                                            break
                                        }
                                        Ne = xe, Ie = P
                                    } while (0);
                                    S = 1 + (P = (183 * g | 0) >>> 8) | 0, (M = Ne >>> Ie >>> 0 > P >>> 0) ? (f[e >> 2] = Ne - (S << Ie), Fe = g - P | 0) : Fe = S, S = 24 ^ (0 | y(0 | Fe)), f[d >> 2] = Ie - S, f[c >> 2] = (Fe << S) - 1, He = M ? 1 : 3
                                } else He = 2
                            } else He = 0;
                            i[k + (800 * w | 0) + 785 >> 0] = He, w = w + 1 | 0
                        } while ((0 | w) < (0 | f[t >> 2]));
                        return 0 | 1 & 0 == (0 | f[(r + 36 | 0) >> 2])
                    }

                    function ve(e, r) {
                        r |= 0;
                        var t, n, o, u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            A = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0;
                        if (t = S, S = S + 32 | 0, u = t, n = (l = 0 | f[2256 + (e |= 0) >> 2]) + ((d = 0 | f[(c = e + 2296 | 0) >> 2]) << 1) | 0, o = 0 | f[e + 2304 >> 2], 0 != (0 | f[e + 2236 >> 2]) && (h = 255 & (_ = 0 | i[o + (800 * d | 0) + 797 >> 0]), _ << 24 >> 24 != 0)) i[n >> 0] = 0, i[l + -2 >> 0] = 0, 0 | i[o + (800 * d | 0) + 768 >> 0] || (i[l + (d << 1) + 1 >> 0] = 0, i[l + -1 >> 0] = 0), f[o + (800 * d | 0) + 788 >> 2] = 0, f[o + (800 * d | 0) + 792 >> 2] = 0, i[o + (800 * d | 0) + 796 >> 0] = 0, m = h;
                        else {
                            if (h = o + (800 * d | 0) | 0, _ = 0 | s[o + (800 * d | 0) + 798 >> 0], at(0 | h, 0, 768), 0 | i[o + (800 * d | 0) + 768 >> 0]) k = 0, E = 3;
                            else {
                                v = (p = u) + 32 | 0;
                                do {
                                    a[p >> 1] = 0, p = p + 2 | 0
                                } while ((0 | p) < (0 | v));
                                p = l + (d << 1) + 1 | 0, v = l + -1 | 0, w = (0 | (b = 0 | Ot[7 & f[3132]](r, e + 2032 | 0, (0 | s[v >> 0]) + (0 | s[p >> 0]) | 0, e + 776 + (_ << 5) + 8 | 0, 0, u))) > 0 & 1, i[v >> 0] = w, i[p >> 0] = w, (0 | b) > 1 ? Rt[15 & f[2894]](u, h) : (b = (3 + (0 | a[u >> 1]) | 0) >>> 3 & 65535, a[o + (800 * d | 0) >> 1] = b, a[o + (800 * d | 0) + 32 >> 1] = b, a[o + (800 * d | 0) + 64 >> 1] = b, a[o + (800 * d | 0) + 96 >> 1] = b, a[o + (800 * d | 0) + 128 >> 1] = b, a[o + (800 * d | 0) + 160 >> 1] = b, a[o + (800 * d | 0) + 192 >> 1] = b, a[o + (800 * d | 0) + 224 >> 1] = b, a[o + (800 * d | 0) + 256 >> 1] = b, a[o + (800 * d | 0) + 288 >> 1] = b, a[o + (800 * d | 0) + 320 >> 1] = b, a[o + (800 * d | 0) + 352 >> 1] = b, a[o + (800 * d | 0) + 384 >> 1] = b, a[o + (800 * d | 0) + 416 >> 1] = b, a[o + (800 * d | 0) + 448 >> 1] = b, a[o + (800 * d | 0) + 480 >> 1] = b), k = 1, E = 0
                            }
                            for (b = e + 1964 + (68 * E | 0) | 0, E = l + -2 | 0, l = e + 776 + (_ << 5) | 0, u = h, h = 15 & i[n >> 0], w = 0, p = 0, v = 15 & i[E >> 0]; M = 255 & h, y = (0 | (A = 0 | Ot[7 & f[3132]](r, b, (1 & M) + (1 & v) | 0, l, k, u))) > (0 | k) & 1, L = M >>> 1, M = 0 != (0 | a[u >> 1]) & 1, g = u + 32 | 0, D = (0 | (T = 0 | Ot[7 & f[3132]](r, b, y + (1 & L) | 0, l, k, g))) > (0 | k) & 1, C = (y << 7 | L) >>> 1, L = ((0 | T) > 3 ? 3 : (0 | T) > 1 ? 2 : 0 != (0 | a[g >> 1]) & 1) | ((0 | A) > 3 ? 12 : (0 | A) > 1 ? 8 : M << 2), M = u + 64 | 0, g = (0 | (A = 0 | Ot[7 & f[3132]](r, b, (1 & C) + D | 0, l, k, M))) > (0 | k) & 1, T = (D << 7 | C) >>> 1, C = ((0 | A) > 3 ? 3 : (0 | A) > 1 ? 2 : 0 != (0 | a[M >> 1]) & 1) | L << 2, L = u + 96 | 0, h = (255 & ((A = ((0 | (M = 0 | Ot[7 & f[3132]](r, b, (1 & T) + g | 0, l, k, L))) > (0 | k) & 1) << 7) | (g << 7 | T) >>> 1)) >>> 4, v = A | v >>> 1, w = C << 2 | w << 8 | ((0 | M) > 3 ? 3 : (0 | M) > 1 ? 2 : 0 != (0 | a[L >> 1]) & 1), 4 != (0 | (p = p + 1 | 0));) u = u + 128 | 0;
                            for (u = e + 2100 | 0, p = e + 776 + (_ << 5) + 16 | 0, k = 0, l = v >>> 4, v = 255 & h, h = 0, b = o + (800 * d | 0) + 512 | 0; L = h + 4 | 0, M = (0 | s[n >> 0]) >>> L, C = (0 | s[E >> 0]) >>> L, A = (0 | (L = 0 | Ot[7 & f[3132]](r, u, (1 & C) + (1 & M) | 0, p, 0, b))) > 0 & 1, T = M >>> 1, M = 0 != (0 | a[b >> 1]) & 1, g = b + 32 | 0, y = (0 | (D = 0 | Ot[7 & f[3132]](r, u, A + (1 & T) | 0, p, 0, g))) > 0 & 1, P = (A << 3 | T) >>> 3, T = ((0 | D) > 3 ? 3 : (0 | D) > 1 ? 2 : 0 != (0 | a[g >> 1]) & 1) | ((0 | L) > 3 ? 12 : (0 | L) > 1 ? 8 : M << 2), M = b + 64 | 0, L = C >>> 1, g = (0 | (C = 0 | Ot[7 & f[3132]](r, u, (1 & P) + (1 & L) | 0, p, 0, M))) > 0 & 1, D = (y << 1 | P) >>> 1, P = ((0 | C) > 3 ? 3 : (0 | C) > 1 ? 2 : 0 != (0 | a[M >> 1]) & 1) | T << 2, T = b + 96 | 0, C = (0 | (M = 0 | Ot[7 & f[3132]](r, u, (1 & D) + g | 0, p, 0, T))) > 0 & 1, k = (((0 | M) > 3 ? 3 : (0 | M) > 1 ? 2 : 0 != (0 | a[T >> 1]) & 1) | P << 2) << (h << 2) | k, v |= ((C << 1 | (g << 3 | 120 & D) >>> 3) << 4 & 4080) << h, l |= (C << 5 | (y << 5 | L) >>> 1 & 112) << h, !((0 | (h = h + 2 | 0)) >= 4);) b = b + 128 | 0;
                            i[n >> 0] = v, i[E >> 0] = l, f[o + (800 * d | 0) + 788 >> 2] = w, f[o + (800 * d | 0) + 792 >> 2] = k, R = 43690 & k ? 0 : 255 & f[e + 776 + (_ << 5) + 28 >> 2], i[o + (800 * d | 0) + 796 >> 0] = R, m = 0 == (k | w | 0) & 1
                        }
                        return (0 | f[e + 2308 >> 2]) <= 0 ? (O = 0 | f[(r + 24 | 0) >> 2], S = t, 0 | 1 & 0 == (0 | O)) : (c = (w = 0 | f[e + 2260 >> 2]) + ((k = 0 | f[c >> 2]) << 2) | 0, R = e + 2312 + (s[o + (800 * d | 0) + 798 >> 0] << 3) + (s[o + (800 * d | 0) + 768 >> 0] << 2) | 0, d = s[R >> 0] | s[R + 1 >> 0] << 8 | s[R + 2 >> 0] << 16 | s[R + 3 >> 0] << 24, i[c >> 0] = d, i[c + 1 >> 0] = d >> 8, i[c + 2 >> 0] = d >> 16, i[c + 3 >> 0] = d >> 24, i[(d = w + (k << 2) + 2 | 0) >> 0] = s[d >> 0] | 0 == (0 | m), O = 0 | f[(r + 24 | 0) >> 2], S = t, 0 | 1 & 0 == (0 | O))
                    }

                    function be() {
                        var e;
                        0 | ut(12500) || (e = 0 | f[2893], (0 | f[100]) != (0 | e) && (f[3064] = 15, f[3065] = 23, f[3061] = 16, f[3062] = 17, f[3063] = 21, f[3066] = 1, f[3067] = 22, f[3068] = 24), f[100] = e, bt(12500))
                    }

                    function we(e, r, t, n, i) {
                        r |= 0, t |= 0, i |= 0;
                        var o = 0,
                            a = 0;
                        if ((0 | (n |= 0)) > 0)
                            for (o = 0, a = e |= 0; xt[31 & f[2882]](a, t, i), (0 | (o = o + 1 | 0)) != (0 | n);) a = a + r | 0
                    }

                    function Se(e, r, t, n) {
                        e |= 0, r |= 0, n |= 0;
                        var o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0;
                        switch (0 | (t |= 0)) {
                            case 0:
                                return void xt[31 & f[2947]](e, r, n);
                            case 1:
                                return void xt[31 & f[2948]](e, r, n);
                            case 7:
                                return xt[31 & f[2948]](e, r, n), void Ct[31 & f[2884]](n, 0, r, 1, 0);
                            case 2:
                                return void xt[31 & f[2949]](e, r, n);
                            case 3:
                                return void lt(0 | n, 0 | e, r << 2 | 0);
                            case 8:
                                return lt(0 | n, 0 | e, r << 2 | 0), void Ct[31 & f[2884]](n, 0, r, 1, 0);
                            case 4:
                                if (o = e + (r << 2) | 0, !((0 | r) > 0)) return;
                                for (a = n, u = e; l = 0 | wt(0 | f[u >> 2]), u = u + 4 | 0, i[a >> 0] = l, i[a + 1 >> 0] = l >> 8, i[a + 2 >> 0] = l >> 16, i[a + 3 >> 0] = l >> 24, !(u >>> 0 >= o >>> 0);) a = a + 4 | 0;
                                return;
                            case 9:
                                if (o = e + (r << 2) | 0, (0 | r) > 0)
                                    for (l = n, s = e; c = 0 | wt(0 | f[s >> 2]), s = s + 4 | 0, i[l >> 0] = c, i[l + 1 >> 0] = c >> 8, i[l + 2 >> 0] = c >> 16, i[l + 3 >> 0] = c >> 24, !(s >>> 0 >= o >>> 0);) l = l + 4 | 0;
                                return void Ct[31 & f[2884]](n, 1, r, 1, 0);
                            case 5:
                                return void xt[31 & f[2950]](e, r, n);
                            case 10:
                                return xt[31 & f[2950]](e, r, n), void Ut[31 & f[2885]](n, r, 1, 0);
                            case 6:
                                return void xt[31 & f[2951]](e, r, n);
                            default:
                                return
                        }
                    }

                    function ke(e) {
                        var r = 0,
                            t = 0,
                            n = 0;
                        return (t = 0 | f[(r = 12 + (e |= 0) | 0) >> 2]) >>> 0 < (0 | f[e + 16 >> 2]) >>> 0 ? (f[(n = e + 8 | 0) >> 2] = 8 + (0 | f[n >> 2]), f[r >> 2] = t + 1, void(f[e >> 2] = f[e >> 2] << 8 | 0 | s[t >> 0])) : 0 | f[(t = e + 24 | 0) >> 2] ? void(f[e + 8 >> 2] = 0) : (f[e >> 2] = f[e >> 2] << 8, f[(r = e + 8 | 0) >> 2] = 8 + (0 | f[r >> 2]), void(f[t >> 2] = 1))
                    }

                    function Ee(e) {
                        a[(e |= 0) >> 1] = 65535, i[e + 2 >> 0] = 255
                    }

                    function Me(e, r, t) {
                        r |= 0, t |= 0;
                        var n, i, o, a = 0,
                            u = 0;
                        return f[4 + (e |= 0) >> 2] = 254, f[e >> 2] = 0, f[(n = e + 8 | 0) >> 2] = -8, f[(i = e + 24 | 0) >> 2] = 0, f[(o = e + 12 | 0) >> 2] = r, a = r + t | 0, f[e + 16 >> 2] = a, u = t >>> 0 > 3 ? a + -4 + 1 | 0 : r, f[e + 20 >> 2] = u, u >>> 0 > r >>> 0 ? (u = s[r >> 0] | s[r + 1 >> 0] << 8 | s[r + 2 >> 0] << 16 | s[r + 3 >> 0] << 24, f[o >> 2] = r + 3, a = (0 | wt(0 | u)) >>> 8, f[e >> 2] = a, void(f[n >> 2] = 16)) : (0 | t) > 0 ? (f[n >> 2] = 0, f[o >> 2] = r + 1, void(f[e >> 2] = s[r >> 0])) : (f[e >> 2] = 0, f[n >> 2] = 0, void(f[i >> 2] = 1))
                    }

                    function Ae(e, r) {
                        var t, n, i, o, a, u, l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0;
                        if ((0 | (r |= 0)) <= 0) return 0;
                        for (t = 4 + (e |= 0) | 0, i = e + 12 | 0, o = e + 20 | 0, a = e + 16 | 0, u = e + 24 | 0, c = 0, d = r, r = 0 | f[(n = e + 8 | 0) >> 2], _ = 0 | f[t >> 2];;) {
                            h = d, d = d + -1 | 0;
                            do {
                                if ((0 | r) < 0) {
                                    if ((m = 0 | f[i >> 2]) >>> 0 < (0 | f[o >> 2]) >>> 0) {
                                        p = s[m >> 0] | s[m + 1 >> 0] << 8 | s[m + 2 >> 0] << 16 | s[m + 3 >> 0] << 24, f[i >> 2] = m + 3, v = (0 | wt(0 | p)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | v, v = r + 24 | 0, f[n >> 2] = v, b = v;
                                        break
                                    }
                                    if (m >>> 0 < (0 | f[a >> 2]) >>> 0) {
                                        v = r + 8 | 0, f[n >> 2] = v, f[i >> 2] = m + 1, f[e >> 2] = f[e >> 2] << 8 | 0 | s[m >> 0], b = v;
                                        break
                                    }
                                    if (0 | f[u >> 2]) {
                                        f[n >> 2] = 0, b = 0;
                                        break
                                    }
                                    f[e >> 2] = f[e >> 2] << 8, v = r + 8 | 0, f[n >> 2] = v, f[u >> 2] = 1, b = v;
                                    break
                                }
                                b = r
                            } while (0);
                            if (w = 1 + (v = _ >>> 1 & 16777215) | 0, (p = (m = 0 | f[e >> 2]) >>> b >>> 0 > v >>> 0) ? (f[e >> 2] = m - (w << b), S = _ - v | 0) : S = w, r = b - (w = 24 ^ (0 | y(0 | S))) | 0, f[n >> 2] = r, _ = (S << w) - 1 | 0, f[t >> 2] = _, w = (1 & p) << d | c, (0 | h) <= 1) {
                                l = w;
                                break
                            }
                            c = w
                        }
                        return 0 | l
                    }

                    function ye(e, r) {
                        var t;
                        return t = 0 | Ae(e |= 0, r |= 0), 0 | ((r = 0 != (0 | Ae(e, 1))) ? 0 - t | 0 : t)
                    }

                    function Le(e) {
                        var r, t, n, o, a, u, l, c, _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0;
                        r = 0 | Ae(_ = 12 + (e |= 0) | 0, 7), o = 0 | Ae(_, 1) ? 0 | ye(_, 4) : 0, a = 0 | Ae(_, 1) ? 0 | ye(_, 4) : 0, u = 0 | Ae(_, 1) ? 0 | ye(_, 4) : 0, l = 0 | Ae(_, 1) ? 0 | ye(_, 4) : 0, c = 0 | Ae(_, 1) ? 0 | ye(_, 4) : 0, _ = e + 104 | 0, t = e + 776 | 0, n = e + 112 | 0, h = 0;
                        do {
                            0 | f[_ >> 2] ? (p = (0 == (0 | f[n >> 2]) ? r : 0) + (0 | i[e + 116 + h >> 0]) | 0, v = 16) : (0 | h) > 0 ? (f[(m = e + 776 + (h << 5) | 0) >> 2] = f[t >> 2], f[m + 4 >> 2] = f[t + 4 >> 2], f[m + 8 >> 2] = f[t + 8 >> 2], f[m + 12 >> 2] = f[t + 12 >> 2], f[m + 16 >> 2] = f[t + 16 >> 2], f[m + 20 >> 2] = f[t + 20 >> 2], f[m + 24 >> 2] = f[t + 24 >> 2], f[m + 28 >> 2] = f[t + 28 >> 2]) : (p = r, v = 16), 16 == (0 | v) && (v = 0, m = p + o | 0, f[e + 776 + (h << 5) >> 2] = s[8796 + ((0 | m) < 0 ? 0 : (0 | m) < 127 ? m : 127) >> 0], f[e + 776 + (h << 5) + 4 >> 2] = d[986 + (((0 | p) < 0 ? 0 : (0 | p) < 127 ? p : 127) << 1) >> 1], m = p + a | 0, f[e + 776 + (h << 5) + 8 >> 2] = s[8796 + ((0 | m) < 0 ? 0 : (0 | m) < 127 ? m : 127) >> 0] << 1, b = 101581 * (0 | d[986 + (((0 | (m = p + u | 0)) < 0 ? 0 : (0 | m) < 127 ? m : 127) << 1) >> 1]) | 0, f[e + 776 + (h << 5) + 12 >> 2] = b >>> 0 < 524288 ? 8 : b >>> 16, b = p + l | 0, f[e + 776 + (h << 5) + 16 >> 2] = s[8796 + ((0 | b) < 0 ? 0 : (0 | b) < 117 ? b : 117) >> 0], b = p + c | 0, f[e + 776 + (h << 5) + 20 >> 2] = d[986 + (((0 | b) < 0 ? 0 : (0 | b) < 127 ? b : 127) << 1) >> 1], f[e + 776 + (h << 5) + 24 >> 2] = b), h = h + 1 | 0
                        } while (4 != (0 | h))
                    }

                    function ge(e, r) {
                        e |= 0;
                        var t, n, o, a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0;
                        a = 0 | s[3 + (r |= 0) >> 0], n = 0 | f[(t = e + 4 | 0) >> 2], u = 0 | f[(o = e + 8 | 0) >> 2];
                        do {
                            if ((0 | u) < 0) {
                                if ((c = 0 | f[(l = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                    d = s[c >> 0] | s[c + 1 >> 0] << 8 | s[c + 2 >> 0] << 16 | s[c + 3 >> 0] << 24, f[l >> 2] = c + 3, c = (0 | wt(0 | d)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | c, c = u + 24 | 0, f[o >> 2] = c, _ = c;
                                    break
                                }
                                ke(e), _ = 0 | f[o >> 2];
                                break
                            }
                            _ = u
                        } while (0);
                        if (d = 1 + (u = (0 | A(n, a)) >>> 8) | 0, (c = (a = 0 | f[e >> 2]) >>> _ >>> 0 > u >>> 0) ? (l = a - (d << _) | 0, f[e >> 2] = l, h = n - u | 0, m = l) : (h = d, m = a), d = _ - (a = 24 ^ (0 | y(0 | h))) | 0, f[o >> 2] = d, _ = (h << a) - 1 | 0, f[t >> 2] = _, !c) {
                            c = 0 | s[r + 4 >> 0];
                            do {
                                if ((0 | d) < 0) {
                                    if ((h = 0 | f[(a = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                        l = s[h >> 0] | s[h + 1 >> 0] << 8 | s[h + 2 >> 0] << 16 | s[h + 3 >> 0] << 24, f[a >> 2] = h + 3, h = m << 24 | (0 | wt(0 | l)) >>> 8, f[e >> 2] = h, l = d + 24 | 0, f[o >> 2] = l, p = h, v = l;
                                        break
                                    }
                                    ke(e), p = 0 | f[e >> 2], v = 0 | f[o >> 2];
                                    break
                                }
                                p = m, v = d
                            } while (0);
                            if (h = 1 + (l = (0 | A(c, _)) >>> 8) | 0, (c = p >>> v >>> 0 > l >>> 0) ? (a = p - (h << v) | 0, f[e >> 2] = a, b = _ - l | 0, w = a) : (b = h, w = p), h = v - (p = 24 ^ (0 | y(0 | b))) | 0, f[o >> 2] = h, v = (b << p) - 1 | 0, f[t >> 2] = v, !c) return 2;
                            c = 0 | s[r + 5 >> 0];
                            do {
                                if ((0 | h) < 0) {
                                    if ((b = 0 | f[(p = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                        a = s[b >> 0] | s[b + 1 >> 0] << 8 | s[b + 2 >> 0] << 16 | s[b + 3 >> 0] << 24, f[p >> 2] = b + 3, b = w << 24 | (0 | wt(0 | a)) >>> 8, f[e >> 2] = b, a = h + 24 | 0, f[o >> 2] = a, S = b, k = a;
                                        break
                                    }
                                    ke(e), S = 0 | f[e >> 2], k = 0 | f[o >> 2];
                                    break
                                }
                                S = w, k = h
                            } while (0);
                            return w = 1 + (h = (0 | A(c, v)) >>> 8) | 0, (c = S >>> k >>> 0 > h >>> 0) ? (f[e >> 2] = S - (w << k), E = v - h | 0) : E = w, w = 24 ^ (0 | y(0 | E)), f[o >> 2] = k - w, f[t >> 2] = (E << w) - 1, 0 | (c ? 4 : 3)
                        }
                        c = 0 | s[r + 6 >> 0];
                        do {
                            if ((0 | d) < 0) {
                                if ((E = 0 | f[(w = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                    k = s[E >> 0] | s[E + 1 >> 0] << 8 | s[E + 2 >> 0] << 16 | s[E + 3 >> 0] << 24, f[w >> 2] = E + 3, E = m << 24 | (0 | wt(0 | k)) >>> 8, f[e >> 2] = E, k = d + 24 | 0, f[o >> 2] = k, M = E, L = k;
                                    break
                                }
                                ke(e), M = 0 | f[e >> 2], L = 0 | f[o >> 2];
                                break
                            }
                            M = m, L = d
                        } while (0);
                        if (m = 1 + (d = (0 | A(c, _)) >>> 8) | 0, (c = M >>> L >>> 0 > d >>> 0) ? (k = M - (m << L) | 0, f[e >> 2] = k, g = _ - d | 0, T = k) : (g = m, T = M), m = L - (M = 24 ^ (0 | y(0 | g))) | 0, f[o >> 2] = m, L = (g << M) - 1 | 0, f[t >> 2] = L, c) {
                            c = 0 | s[r + 8 >> 0];
                            do {
                                if ((0 | m) < 0) {
                                    if ((g = 0 | f[(M = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                        k = s[g >> 0] | s[g + 1 >> 0] << 8 | s[g + 2 >> 0] << 16 | s[g + 3 >> 0] << 24, f[M >> 2] = g + 3, g = T << 24 | (0 | wt(0 | k)) >>> 8, f[e >> 2] = g, k = m + 24 | 0, f[o >> 2] = k, D = g, C = k;
                                        break
                                    }
                                    ke(e), D = 0 | f[e >> 2], C = 0 | f[o >> 2];
                                    break
                                }
                                D = T, C = m
                            } while (0);
                            g = 1 & (c = D >>> C >>> 0 > (k = (0 | A(c, L)) >>> 8) >>> 0), M = k + 1 | 0, c ? (d = D - (M << C) | 0, f[e >> 2] = d, P = L - k | 0, R = d) : (P = M, R = D), M = C - (D = 24 ^ (0 | y(0 | P))) | 0, f[o >> 2] = M, C = (P << D) - 1 | 0, f[t >> 2] = C, D = 0 | s[r + (c ? 10 : 9) >> 0];
                            do {
                                if ((0 | M) < 0) {
                                    if ((P = 0 | f[(c = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                        d = s[P >> 0] | s[P + 1 >> 0] << 8 | s[P + 2 >> 0] << 16 | s[P + 3 >> 0] << 24, f[c >> 2] = P + 3, P = R << 24 | (0 | wt(0 | d)) >>> 8, f[e >> 2] = P, d = M + 24 | 0, f[o >> 2] = d, O = P, B = d;
                                        break
                                    }
                                    ke(e), O = 0 | f[e >> 2], B = 0 | f[o >> 2];
                                    break
                                }
                                O = R, B = M
                            } while (0);
                            if (R = 1 + (M = (0 | A(C, D)) >>> 8) | 0, (D = O >>> B >>> 0 > M >>> 0) ? (d = O - (R << B) | 0, f[e >> 2] = d, x = C - M | 0, N = d) : (x = R, N = O), R = B - (O = 24 ^ (0 | y(0 | x))) | 0, f[o >> 2] = R, B = (x << O) - 1 | 0, f[t >> 2] = B, g = 0 | f[404 + ((O = 1 & D | g << 1) << 2) >> 2], (D = 0 | i[g >> 0]) << 24 >> 24)
                                for (x = e + 12 | 0, d = e + 20 | 0, M = g, g = 0, C = D, D = R, R = N, N = B;;) {
                                    B = 255 & C;
                                    do {
                                        if ((0 | D) < 0) {
                                            if ((P = 0 | f[x >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                                c = s[P >> 0] | s[P + 1 >> 0] << 8 | s[P + 2 >> 0] << 16 | s[P + 3 >> 0] << 24, f[x >> 2] = P + 3, P = R << 24 | (0 | wt(0 | c)) >>> 8, f[e >> 2] = P, c = D + 24 | 0, f[o >> 2] = c, F = P, H = c;
                                                break
                                            }
                                            ke(e), F = 0 | f[e >> 2], H = 0 | f[o >> 2];
                                            break
                                        }
                                        F = R, H = D
                                    } while (0);
                                    if (k = 1 + (c = (0 | A(N, B)) >>> 8) | 0, (P = F >>> H >>> 0 > c >>> 0) ? (_ = F - (k << H) | 0, f[e >> 2] = _, U = N - c | 0, G = _) : (U = k, G = F), D = H - (k = 24 ^ (0 | y(0 | U))) | 0, f[o >> 2] = D, N = (U << k) - 1 | 0, f[t >> 2] = N, k = 1 & P | g << 1, !((C = 0 | i[(M = M + 1 | 0) >> 0]) << 24 >> 24)) {
                                        I = k;
                                        break
                                    }
                                    g = k, R = G
                                } else I = 0;
                            return 0 | I + (8 << O | 3)
                        }
                        O = 0 | s[r + 7 >> 0];
                        do {
                            if ((0 | m) < 0) {
                                if ((I = 0 | f[(r = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                    G = s[I >> 0] | s[I + 1 >> 0] << 8 | s[I + 2 >> 0] << 16 | s[I + 3 >> 0] << 24, f[r >> 2] = I + 3, I = T << 24 | (0 | wt(0 | G)) >>> 8, f[e >> 2] = I, G = m + 24 | 0, f[o >> 2] = G, W = I, Y = G;
                                    break
                                }
                                ke(e), W = 0 | f[e >> 2], Y = 0 | f[o >> 2];
                                break
                            }
                            W = T, Y = m
                        } while (0);
                        if (T = 1 + (m = (0 | A(O, L)) >>> 8) | 0, (O = W >>> Y >>> 0 > m >>> 0) ? (G = W - (T << Y) | 0, f[e >> 2] = G, V = L - m | 0, q = G) : (V = T, q = W), T = Y - (W = 24 ^ (0 | y(0 | V))) | 0, f[o >> 2] = T, Y = (V << W) - 1 | 0, f[t >> 2] = Y, W = (0 | T) < 0, !O) {
                            do {
                                if (W) {
                                    if ((V = 0 | f[(O = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                        G = s[V >> 0] | s[V + 1 >> 0] << 8 | s[V + 2 >> 0] << 16 | s[V + 3 >> 0] << 24, f[O >> 2] = V + 3, V = q << 24 | (0 | wt(0 | G)) >>> 8, f[e >> 2] = V, G = T + 24 | 0, f[o >> 2] = G, j = V, z = G;
                                        break
                                    }
                                    ke(e), j = 0 | f[e >> 2], z = 0 | f[o >> 2];
                                    break
                                }
                                j = q, z = T
                            } while (0);
                            return O = 1 + (G = (159 * Y | 0) >>> 8) | 0, (V = j >>> z >>> 0 > G >>> 0) ? (f[e >> 2] = j - (O << z), X = Y - G | 0) : X = O, O = 24 ^ (0 | y(0 | X)), f[o >> 2] = z - O, f[t >> 2] = (X << O) - 1, 0 | (V ? 6 : 5)
                        }
                        do {
                            if (W) {
                                if ((O = 0 | f[(V = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                    X = s[O >> 0] | s[O + 1 >> 0] << 8 | s[O + 2 >> 0] << 16 | s[O + 3 >> 0] << 24, f[V >> 2] = O + 3, O = q << 24 | (0 | wt(0 | X)) >>> 8, f[e >> 2] = O, X = T + 24 | 0, f[o >> 2] = X, K = O, $ = X;
                                    break
                                }
                                ke(e), K = 0 | f[e >> 2], $ = 0 | f[o >> 2];
                                break
                            }
                            K = q, $ = T
                        } while (0);
                        W = 1 + (T = (165 * Y | 0) >>> 8) | 0, (q = K >>> $ >>> 0 > T >>> 0) ? (X = K - (W << $) | 0, f[e >> 2] = X, J = Y - T | 0, Q = X) : (J = W, Q = K), W = $ - (K = 24 ^ (0 | y(0 | J))) | 0, f[o >> 2] = W, $ = (J << K) - 1 | 0, f[t >> 2] = $, K = 7 + ((1 & q) << 1) | 0;
                        do {
                            if ((0 | W) < 0) {
                                if ((J = 0 | f[(q = e + 12 | 0) >> 2]) >>> 0 < (0 | f[e + 20 >> 2]) >>> 0) {
                                    X = s[J >> 0] | s[J + 1 >> 0] << 8 | s[J + 2 >> 0] << 16 | s[J + 3 >> 0] << 24, f[q >> 2] = J + 3, J = Q << 24 | (0 | wt(0 | X)) >>> 8, f[e >> 2] = J, X = W + 24 | 0, f[o >> 2] = X, Z = J, ee = X;
                                    break
                                }
                                ke(e), Z = 0 | f[e >> 2], ee = 0 | f[o >> 2];
                                break
                            }
                            Z = Q, ee = W
                        } while (0);
                        return X = 1 + (W = (145 * $ | 0) >>> 8) | 0, (Q = Z >>> ee >>> 0 > W >>> 0) ? (f[e >> 2] = Z - (X << ee), re = $ - W | 0) : re = X, X = 24 ^ (0 | y(0 | re)), f[o >> 2] = ee - X, f[t >> 2] = (re << X) - 1, 0 | K + (1 & Q)
                    }

                    function Te(e, r) {
                        var t, n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0;
                        if (!(e |= 0)) return 0;
                        if (!(r |= 0)) return 0 | f[e >> 2] ? 0 : (f[e >> 2] = 2, f[e + 8 >> 2] = 8967, f[e + 4 >> 2] = 0, 0);
                        if (0 == (0 | f[(t = e + 4 | 0) >> 2]) && 0 == (0 | me(e, r))) return 0;
                        if (o = 1 & (n = 0 == (0 | ne(e, r))), n) {
                            e: do {
                                if (0 | ie(e, r)) {
                                    f[(n = e + 2300 | 0) >> 2] = 0, u = e + 308 | 0;
                                    r: do {
                                        if ((0 | f[u >> 2]) > 0) {
                                            l = e + 312 | 0, s = e + 12 | 0, c = e + 2296 | 0, d = e + 288 | 0, _ = e + 2256 | 0, h = e + 2248 | 0, m = 0;
                                            t: for (;;) {
                                                if (p = e + 316 + (28 * (f[l >> 2] & m) | 0) | 0, !(0 | pe(s, e))) {
                                                    v = 13;
                                                    break
                                                }
                                                if ((0 | f[c >> 2]) < (0 | f[d >> 2]))
                                                    do {
                                                        if (!(0 | ve(e, p))) {
                                                            v = 16;
                                                            break t
                                                        }
                                                        b = 1 + (0 | f[c >> 2]) | 0, f[c >> 2] = b
                                                    } while ((0 | b) < (0 | f[d >> 2]));
                                                if (p = 0 | f[_ >> 2], i[p + -2 >> 0] = 0, i[p + -1 >> 0] = 0, f[h >> 2] = 0, f[c >> 2] = 0, !(0 | Hr(e, r))) {
                                                    v = 20;
                                                    break
                                                }
                                                if (m = 1 + (0 | f[n >> 2]) | 0, f[n >> 2] = m, (0 | m) >= (0 | f[u >> 2])) break r
                                            }
                                            if (13 == (0 | v)) {
                                                if (0 | f[e >> 2]) {
                                                    a = 0;
                                                    break e
                                                }
                                                f[e >> 2] = 7, f[e + 8 >> 2] = 9004, f[t >> 2] = 0, a = 0;
                                                break e
                                            }
                                            if (16 == (0 | v)) {
                                                if (0 | f[e >> 2]) {
                                                    a = 0;
                                                    break e
                                                }
                                                f[e >> 2] = 7, f[e + 8 >> 2] = 9045, f[t >> 2] = 0, a = 0;
                                                break e
                                            }
                                            if (20 == (0 | v)) {
                                                if (0 | f[e >> 2]) {
                                                    a = 0;
                                                    break e
                                                }
                                                f[e >> 2] = 6, f[e + 8 >> 2] = 9080, f[t >> 2] = 0, a = 0;
                                                break e
                                            }
                                        }
                                    } while (0);
                                    if ((0 | f[e + 148 >> 2]) > 0 && 0 == (0 | Bt[7 & f[(u = 380) >> 2]](e + 124 | 0))) {
                                        a = 0;
                                        break
                                    }
                                    a = 1
                                } else a = 0
                            } while (0);w = (0 | function(e, r) {
                                r |= 0;
                                var t;
                                return t = (0 | f[148 + (e |= 0) >> 2]) > 0 ? 0 | Bt[7 & f[95]](e + 124 | 0) : 1, (e = 0 | f[r + 52 >> 2]) ? (Pt[31 & e](r), 0 | t) : 0 | t
                            }(e, r)) & a
                        } else w = o;
                        return w ? (f[t >> 2] = 0, 0 | w) : (Pt[31 & f[(o = 392) >> 2]](e + 124 | 0), Ye(e), Ve(0 | f[(o = e + 2288 | 0) >> 2]), f[o >> 2] = 0, f[e + 2292 >> 2] = 0, f[(o = e + 12 | 0) >> 2] = 0, f[o + 4 >> 2] = 0, f[o + 8 >> 2] = 0, f[o + 12 >> 2] = 0, f[o + 16 >> 2] = 0, f[o + 20 >> 2] = 0, f[o + 24 >> 2] = 0, f[t >> 2] = 0, 0)
                    }

                    function De(e, r, t) {
                        e |= 0, r |= 0, t |= 0;
                        var n, i, o, a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0;
                        if (n = S, S = S + 160 | 0, a = n + 148 | 0, u = n + 144 | 0, i = n + 36 | 0, f[(o = n) >> 2] = e, f[(l = o + 4 | 0) >> 2] = r, f[o + 8 >> 2] = 1, f[u >> 2] = 0, s = 0 | ce(e, r, 0, 0, 0, u, 0, o), f[a >> 2] = s, s = 0 != (0 | f[u >> 2]), 0 | f[a >> 2] ? s & 7 == (0 | f[a >> 2]) && (c = 4) : s && (c = 4), 4 == (0 | c) && (f[a >> 2] = 4), 0 | (c = 0 | f[a >> 2])) return S = n, 0 | c;
                        if (Ze(i, 520), c = 0 | f[o + 12 >> 2], f[i + 64 >> 2] = (0 | f[o >> 2]) + c, f[i + 60 >> 2] = (0 | f[l >> 2]) - c, er(t, i), 0 | f[o + 32 >> 2]) {
                            if (!(_ = 0 | function() {
                                var e;
                                return (e = 0 | $e(1, 0, 272)) ? (f[e >> 2] = 0, f[e + 4 >> 2] = 2, dr(), 0 | e) : 0
                            }())) return S = n, 1;
                            m = 0 | function(e, r) {
                                var t, n = 0,
                                    i = 0;
                                if (!(e |= 0)) return 0;
                                if (!(r |= 0)) return f[e >> 2] = 2, 0;
                                if (f[e + 8 >> 2] = r, f[e >> 2] = 0, _r(t = e + 24 | 0, 0 | f[r + 64 >> 2], 0 | f[r + 60 >> 2]), 47 == (0 | ur(t, 8)) && (n = 1 + (0 | ur(t, 14)) | 0, i = 1 + (0 | ur(t, 14)) | 0, ur(t, 1), 0 == (0 | ur(t, 3))) && 0 == (0 | f[e + 48 >> 2])) {
                                    if (f[e + 4 >> 2] = 2, f[r >> 2] = n, f[r + 4 >> 2] = i, 0 | hr(n, i, 1, e, 0)) return 1
                                } else f[e >> 2] = 3;
                                return je(e), 0
                            }(_, i) ? (c = 0 | Cr(0 | f[i >> 2], 0 | f[i + 4 >> 2], 0 | f[t + 20 >> 2], 0 | f[t >> 2])) || (0 | function(e) {
                                var r, t, n, i = 0,
                                    o = 0,
                                    a = 0,
                                    u = 0,
                                    l = 0,
                                    s = 0,
                                    c = 0,
                                    d = 0,
                                    _ = 0,
                                    h = 0,
                                    m = 0,
                                    p = 0,
                                    v = 0,
                                    b = 0,
                                    w = 0,
                                    S = 0,
                                    k = 0,
                                    E = 0;
                                if (!(e |= 0)) return 0;
                                r = 0 | f[e + 8 >> 2], t = 0 | f[r + 40 >> 2], n = e + 4 | 0;
                                e: do {
                                    if (0 | f[n >> 2]) {
                                        if (f[(l = e + 12 | 0) >> 2] = f[t >> 2], !(0 | wr(0 | f[t + 20 >> 2], r, 3))) {
                                            f[e >> 2] = 2;
                                            break
                                        }
                                        if (s = 0 | f[r >> 2], d = 0 | f[(c = e + 100 | 0) >> 2], m = 0 | _t(0 | (h = 0 | f[(_ = e + 104 | 0) >> 2]), ((0 | h) < 0) << 31 >> 31 | 0, 0 | d, ((0 | d) < 0) << 31 >> 31 | 0), d = M, h = 65535 & s, s = 0 | ot(0 | (p = 0 | mt(0 | s, ((0 | s) < 0) << 31 >> 31 | 0, 4)), 0 | M, 0 | h, 0), d = 0 | Je(p = 0 | ot(0 | s, 0 | M, 0 | m, 0 | d), M, 4), f[(p = e + 16 | 0) >> 2] = d, !d) {
                                            f[e >> 2] = 1, f[e + 20 >> 2] = 0;
                                            break
                                        }
                                        f[e + 20 >> 2] = d + (m << 2) + (h << 2), h = r + 92 | 0;
                                        do {
                                            if (0 | f[h >> 2]) {
                                                if (m = 0 | f[r + 12 >> 2], d = 0 | f[r + 96 >> 2], s = 0 | f[r + 16 >> 2], v = 0 | f[r + 100 >> 2], S = 0 | ot(0 | (w = 0 | mt(0 | d, 0 | (b = ((0 | d) < 0) << 31 >> 31), 5)), 0 | M, 84, 0), k = 0 | Je(b = 0 | ot(0 | S, 0 | M, 0 | (k = 0 | mt(0 | d, 0 | b, 2)), 0 | M), M, 1)) {
                                                    if (f[e + 264 >> 2] = k, f[e + 268 >> 2] = k, kr(k, m, s, (b = k + 84 | 0) + w | 0, d, v, 0, 4, b), 0 | f[h >> 2]) {
                                                        u = 13;
                                                        break
                                                    }
                                                    u = 12;
                                                    break
                                                }
                                                f[e >> 2] = 1;
                                                break e
                                            }
                                            u = 12
                                        } while (0);
                                        if (12 == (0 | u) && (((h = 0 | f[f[l >> 2] >> 2]) - 7 | 0) >>> 0 < 4 ? u = 13 : E = h), 13 == (0 | u) && (ir(), E = 0 | f[f[l >> 2] >> 2]), E >>> 0 >= 11 && (be(), 0 | f[28 + (0 | f[l >> 2]) >> 2]) && ir(), 0 | f[e + 56 >> 2] && (0 | f[e + 120 >> 2]) > 0 && 0 == (0 | f[(h = e + 136 | 0) >> 2]) && 0 == (0 | vr(h, 0 | f[e + 132 >> 2]))) {
                                            f[e >> 2] = 1;
                                            break
                                        }
                                        f[n >> 2] = 0, i = c, o = _, a = p, u = 23
                                    } else i = e + 100 | 0, o = e + 104 | 0, a = e + 16 | 0, u = 23
                                } while (0);
                                return 23 == (0 | u) && 0 | fr(e, 0 | f[a >> 2], 0 | f[i >> 2], 0 | f[o >> 2], 0 | f[r + 88 >> 2], 9) ? (f[t + 16 >> 2] = f[e + 116 >> 2], 1) : (je(e), 0)
                            }(_) ? 0 : 0 | f[_ >> 2]) : 0 | f[_ >> 2], qe(_), h = m
                        } else {
                            if (!(c = 0 | function() {
                                var e, r = 0,
                                    t = 0;
                                return (e = 0 | $e(1, 0, 2376)) ? (f[e >> 2] = 0, f[e + 8 >> 2] = 6413, Pt[31 & f[(r = 372) >> 2]](e + 124 | 0), f[e + 4 >> 2] = 0, f[e + 312 >> 2] = 0, 0 | f[3132] ? 0 | e : (t = (r = 0 | f[2893]) && 0 != (0 | Bt[7 & r](2)) ? 4 : 3, f[3132] = t, 0 | e)) : 0 | e
                            }())) return S = n, 1;
                            f[c + 2348 >> 2] = f[o + 16 >> 2], f[c + 2352 >> 2] = f[o + 20 >> 2], 0 | me(c, i) ? (l = i + 4 | 0, a = t + 20 | 0, (s = 0 | Cr(0 | f[i >> 2], 0 | f[l >> 2], 0 | f[a >> 2], 0 | f[t >> 2])) ? _ = s : (u = 0 | function(e, r, t, n) {
                                return t |= 0, (e |= 0) ? 0 | (0 == (0 | f[e + 40 >> 2]) ? 0 : (0 | t) > 511 ? 2 : 0) : 0
                            }(0 | f[a >> 2], 0, 0 | f[i >> 2], f[l >> 2]), f[c + 148 >> 2] = u, Ir(0 | f[a >> 2], c), _ = 0 | Te(c, i) ? 0 : 0 | f[c >> 2])) : _ = 0 | f[c >> 2],
                                function(e) {
                                    var r = 0;
                                    (e |= 0) && (Pt[31 & f[(r = 392) >> 2]](e + 124 | 0), Ye(e), Ve(0 | f[(r = e + 2288 | 0) >> 2]), f[r >> 2] = 0, f[e + 2292 >> 2] = 0, f[(r = e + 12 | 0) >> 2] = 0, f[r + 4 >> 2] = 0, f[r + 8 >> 2] = 0, f[r + 12 >> 2] = 0, f[r + 16 >> 2] = 0, f[r + 20 >> 2] = 0, f[r + 24 >> 2] = 0, f[e + 4 >> 2] = 0, Ve(e))
                                }(c), h = _
                        }
                        return 0 | h ? (Or(0 | f[t >> 2]), S = n, 0 | (d = h)) : (h = 0 | f[t + 20 >> 2]) && 0 | f[h + 48 >> 2] ? (d = 0 | function(e) {
                            var r, t, n = 0,
                                i = 0,
                                o = 0,
                                a = 0;
                            return (e |= 0) ? (r = (0 | f[e + 8 >> 2]) - 1 | 0, (0 | f[e >> 2]) >>> 0 < 11 ? (i = 0 | f[(n = e + 20 | 0) >> 2], o = 0 | A(i, r), f[(a = e + 16 | 0) >> 2] = (0 | f[a >> 2]) + o, f[n >> 2] = 0 - i, 0) : (n = 0 | f[(i = e + 32 | 0) >> 2], o = 0 | A(n, r), f[(a = e + 16 | 0) >> 2] = (0 | f[a >> 2]) + o, f[i >> 2] = 0 - n, n = r >> 1, o = 0 | f[(i = e + 36 | 0) >> 2], a = 0 | A(o, n), f[(t = e + 20 | 0) >> 2] = (0 | f[t >> 2]) + a, f[i >> 2] = 0 - o, i = 0 | f[(o = e + 40 | 0) >> 2], a = 0 | A(i, n), f[(n = e + 24 | 0) >> 2] = (0 | f[n >> 2]) + a, f[o >> 2] = 0 - i, (o = 0 | f[(i = e + 28 | 0) >> 2]) ? (e = 0 | f[(a = e + 44 | 0) >> 2], n = o + (0 | A(e, r)) | 0, f[i >> 2] = n, f[a >> 2] = 0 - e, 0) : 0)) : 2
                        }(0 | f[t >> 2]), S = n, 0 | d) : (S = n, 0 | (d = 0))
                    }

                    function Ce(e, r, t) {
                        e |= 0, r |= 0;
                        var n, i, o, a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0;
                        if (n = S, S = S + 144 | 0, i = n + 88 | 0, o = n, !(t |= 0)) return S = n, 2;
                        e: do {
                            if (e) {
                                s = (l = t) + 40 | 0;
                                do {
                                    f[l >> 2] = 0, l = l + 4 | 0
                                } while ((0 | l) < (0 | s));
                                switch (0 | (d = 0 | ce(e, r, t, c = t + 4 | 0, t + 8 | 0, t + 12 | 0, t + 16 | 0, 0))) {
                                    case 0:
                                        break;
                                    case 7:
                                        return S = n, 3;
                                    default:
                                        u = d;
                                        break e
                                }
                                s = 52 + (l = i + 4 | 0) | 0;
                                do {
                                    f[l >> 2] = 0, l = l + 4 | 0
                                } while ((0 | l) < (0 | s));
                                return f[i + 20 >> 2] = t + 124, l = t + 40 | 0, f[i >> 2] = l, 0 | Nr(l, t) ? (Rr(o, 520), f[o >> 2] = f[l >> 2], f[o + 4 >> 2] = f[t >> 2], f[o + 8 >> 2] = f[c >> 2], f[i >> 2] = o, _ = (s = 0 | De(e, r, i)) || 0 | Br(o, l), Or(o), S = n, 0 | (a = _)) : (a = 0 | De(e, r, i), S = n, 0 | a)
                            }
                            u = 2
                        } while (0);
                        return S = n, 0 | u
                    }

                    function Pe(e) {
                        e |= 0;
                        var r, t = 0,
                            n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            A = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0;
                        r = S, S = S + 16 | 0, t = r;
                        do {
                            if (e >>> 0 < 245) {
                                if (i = (n = e >>> 0 < 11 ? 16 : e + 11 & -8) >>> 3, 3 & (a = (o = 0 | f[3133]) >>> i) | 0) return c = 0 | f[(s = 8 + (l = 12572 + ((u = (1 & a ^ 1) + i | 0) << 1 << 2) | 0) | 0) >> 2], (0 | l) == (0 | (_ = 0 | f[(d = c + 8 | 0) >> 2])) ? f[3133] = o & ~(1 << u) : (f[_ + 12 >> 2] = l, f[s >> 2] = _), _ = u << 3, f[c + 4 >> 2] = 3 | _, f[(u = c + _ + 4 | 0) >> 2] = 1 | f[u >> 2], S = r, 0 | d;
                                if (n >>> 0 > (d = 0 | f[3135]) >>> 0) {
                                    if (0 | a) return a = 0 | f[(s = 8 + (c = 12572 + ((l = ((u = (i = (u = ((_ = a << i & ((u = 2 << i) | 0 - u)) & 0 - _) - 1 | 0) >>> (_ = u >>> 12 & 16)) >>> 5 & 8) | _ | (i = (a = i >>> u) >>> 2 & 4) | (a = (c = a >>> i) >>> 1 & 2) | (c = (s = c >>> a) >>> 1 & 1)) + (s >>> c) | 0) << 1 << 2) | 0) | 0) >> 2], (0 | c) == (0 | (_ = 0 | f[(i = a + 8 | 0) >> 2])) ? (u = o & ~(1 << l), f[3133] = u, h = u) : (f[_ + 12 >> 2] = c, f[s >> 2] = _, h = o), _ = (l << 3) - n | 0, f[a + 4 >> 2] = 3 | n, f[4 + (l = a + n | 0) >> 2] = 1 | _, f[l + _ >> 2] = _, 0 | d && (a = 0 | f[3138], c = 12572 + ((s = d >>> 3) << 1 << 2) | 0, h & (u = 1 << s) ? (m = 0 | f[(u = c + 8 | 0) >> 2], p = u) : (f[3133] = h | u, m = c, p = c + 8 | 0), f[p >> 2] = a, f[m + 12 >> 2] = a, f[a + 8 >> 2] = m, f[a + 12 >> 2] = c), f[3135] = _, f[3138] = l, S = r, 0 | i;
                                    if (i = 0 | f[3134]) {
                                        if (v = 0 | f[12836 + (((l = (c = (l = (i & 0 - i) - 1 | 0) >>> (_ = l >>> 12 & 16)) >>> 5 & 8) | _ | (c = (a = c >>> l) >>> 2 & 4) | (a = (u = a >>> c) >>> 1 & 2) | (u = (s = u >>> a) >>> 1 & 1)) + (s >>> u) << 2) >> 2], u = (-8 & f[v + 4 >> 2]) - n | 0, s = 0 | f[v + 16 + ((0 == (0 | f[v + 16 >> 2]) & 1) << 2) >> 2])
                                            for (a = v, v = u, u = s;;) {
                                                if (_ = (c = (s = (-8 & f[u + 4 >> 2]) - n | 0) >>> 0 < v >>> 0) ? s : v, s = c ? u : a, !(u = 0 | f[u + 16 + ((0 == (0 | f[u + 16 >> 2]) & 1) << 2) >> 2])) {
                                                    b = s, w = _;
                                                    break
                                                }
                                                a = s, v = _
                                            } else b = v, w = u;
                                        if (b >>> 0 < (v = b + n | 0) >>> 0) {
                                            a = 0 | f[b + 24 >> 2], u = 0 | f[b + 12 >> 2];
                                            do {
                                                if ((0 | u) == (0 | b)) {
                                                    if (s = 0 | f[(_ = b + 20 | 0) >> 2]) E = s, M = _;
                                                    else {
                                                        if (!(l = 0 | f[(c = b + 16 | 0) >> 2])) {
                                                            k = 0;
                                                            break
                                                        }
                                                        E = l, M = c
                                                    }
                                                    for (;;)
                                                        if (0 | (s = 0 | f[(_ = E + 20 | 0) >> 2])) E = s, M = _;
                                                        else {
                                                            if (!(s = 0 | f[(_ = E + 16 | 0) >> 2])) break;
                                                            E = s, M = _
                                                        }
                                                    f[M >> 2] = 0, k = E
                                                } else _ = 0 | f[b + 8 >> 2], f[_ + 12 >> 2] = u, f[u + 8 >> 2] = _, k = u
                                            } while (0);
                                            do {
                                                if (0 | a) {
                                                    if (u = 0 | f[b + 28 >> 2], (0 | b) == (0 | f[(_ = 12836 + (u << 2) | 0) >> 2])) {
                                                        if (f[_ >> 2] = k, !k) {
                                                            f[3134] = i & ~(1 << u);
                                                            break
                                                        }
                                                    } else if (f[a + 16 + (((0 | f[a + 16 >> 2]) != (0 | b) & 1) << 2) >> 2] = k, !k) break;
                                                    f[k + 24 >> 2] = a, 0 | (u = 0 | f[b + 16 >> 2]) && (f[k + 16 >> 2] = u, f[u + 24 >> 2] = k), 0 | (u = 0 | f[b + 20 >> 2]) && (f[k + 20 >> 2] = u, f[u + 24 >> 2] = k)
                                                }
                                            } while (0);
                                            return w >>> 0 < 16 ? (a = w + n | 0, f[b + 4 >> 2] = 3 | a, f[(i = b + a + 4 | 0) >> 2] = 1 | f[i >> 2]) : (f[b + 4 >> 2] = 3 | n, f[v + 4 >> 2] = 1 | w, f[v + w >> 2] = w, 0 | d && (i = 0 | f[3138], u = 12572 + ((a = d >>> 3) << 1 << 2) | 0, o & (_ = 1 << a) ? (A = 0 | f[(_ = u + 8 | 0) >> 2], y = _) : (f[3133] = o | _, A = u, y = u + 8 | 0), f[y >> 2] = i, f[A + 12 >> 2] = i, f[i + 8 >> 2] = A, f[i + 12 >> 2] = u), f[3135] = w, f[3138] = v), S = r, 0 | b + 8
                                        }
                                        L = n
                                    } else L = n
                                } else L = n
                            } else if (e >>> 0 <= 4294967231)
                                if (i = -8 & (u = e + 11 | 0), _ = 0 | f[3134]) {
                                    a = 0 - i | 0, g = (s = u >>> 8) ? i >>> 0 > 16777215 ? 31 : i >>> (7 + (T = 14 - ((s = (520192 + (c = s << (u = (s + 1048320 | 0) >>> 16 & 8)) | 0) >>> 16 & 4) | u | (c = (245760 + (l = c << s) | 0) >>> 16 & 2)) + (l << c >>> 15) | 0) | 0) & 1 | T << 1 : 0, T = 0 | f[12836 + (g << 2) >> 2];
                                    e: do {
                                        if (T)
                                            for (c = 0, l = a, u = T, s = i << (31 == (0 | g) ? 0 : 25 - (g >>> 1) | 0), O = 0;;) {
                                                if ((B = (-8 & f[u + 4 >> 2]) - i | 0) >>> 0 < l >>> 0) {
                                                    if (!B) {
                                                        x = u, N = 0, I = u, R = 61;
                                                        break e
                                                    }
                                                    F = u, H = B
                                                } else F = c, H = l;
                                                if (U = 0 == (0 | (B = 0 | f[u + 20 >> 2])) | (0 | B) == (0 | (u = 0 | f[u + 16 + (s >>> 31 << 2) >> 2])) ? O : B, B = 0 == (0 | u)) {
                                                    D = U, C = F, P = H, R = 57;
                                                    break
                                                }
                                                c = F, l = H, s <<= 1 & (1 ^ B), O = U
                                            } else D = 0, C = 0, P = a, R = 57
                                    } while (0);
                                    if (57 == (0 | R)) {
                                        if (0 == (0 | D) & 0 == (0 | C)) {
                                            if (!(a = _ & ((T = 2 << g) | 0 - T))) {
                                                L = i;
                                                break
                                            }
                                            G = 0, W = 0 | f[12836 + (((T = (n = (T = (a & 0 - a) - 1 | 0) >>> (a = T >>> 12 & 16)) >>> 5 & 8) | a | (n = (v = n >>> T) >>> 2 & 4) | (v = (o = v >>> n) >>> 1 & 2) | (o = (d = o >>> v) >>> 1 & 1)) + (d >>> o) << 2) >> 2]
                                        } else G = C, W = D;
                                        W ? (x = G, N = P, I = W, R = 61) : (Y = G, V = P)
                                    }
                                    if (61 == (0 | R))
                                        for (;;) {
                                            if (R = 0, v = (d = (o = (-8 & f[I + 4 >> 2]) - i | 0) >>> 0 < N >>> 0) ? o : N, o = d ? I : x, !(I = 0 | f[I + 16 + ((0 == (0 | f[I + 16 >> 2]) & 1) << 2) >> 2])) {
                                                Y = o, V = v;
                                                break
                                            }
                                            x = o, N = v, R = 61
                                        }
                                    if (0 != (0 | Y) && V >>> 0 < ((0 | f[3135]) - i | 0) >>> 0) {
                                        if (Y >>> 0 >= (v = Y + i | 0) >>> 0) return S = r, 0;
                                        o = 0 | f[Y + 24 >> 2], d = 0 | f[Y + 12 >> 2];
                                        do {
                                            if ((0 | d) == (0 | Y)) {
                                                if (a = 0 | f[(n = Y + 20 | 0) >> 2]) j = a, z = n;
                                                else {
                                                    if (!(O = 0 | f[(T = Y + 16 | 0) >> 2])) {
                                                        q = 0;
                                                        break
                                                    }
                                                    j = O, z = T
                                                }
                                                for (;;)
                                                    if (0 | (a = 0 | f[(n = j + 20 | 0) >> 2])) j = a, z = n;
                                                    else {
                                                        if (!(a = 0 | f[(n = j + 16 | 0) >> 2])) break;
                                                        j = a, z = n
                                                    }
                                                f[z >> 2] = 0, q = j
                                            } else n = 0 | f[Y + 8 >> 2], f[n + 12 >> 2] = d, f[d + 8 >> 2] = n, q = d
                                        } while (0);
                                        do {
                                            if (o) {
                                                if (d = 0 | f[Y + 28 >> 2], (0 | Y) == (0 | f[(n = 12836 + (d << 2) | 0) >> 2])) {
                                                    if (f[n >> 2] = q, !q) {
                                                        n = _ & ~(1 << d), f[3134] = n, X = n;
                                                        break
                                                    }
                                                } else if (f[o + 16 + (((0 | f[o + 16 >> 2]) != (0 | Y) & 1) << 2) >> 2] = q, !q) {
                                                    X = _;
                                                    break
                                                }
                                                f[q + 24 >> 2] = o, 0 | (n = 0 | f[Y + 16 >> 2]) && (f[q + 16 >> 2] = n, f[n + 24 >> 2] = q), (n = 0 | f[Y + 20 >> 2]) ? (f[q + 20 >> 2] = n, f[n + 24 >> 2] = q, X = _) : X = _
                                            } else X = _
                                        } while (0);
                                        do {
                                            if (V >>> 0 >= 16) {
                                                if (f[Y + 4 >> 2] = 3 | i, f[v + 4 >> 2] = 1 | V, f[v + V >> 2] = V, _ = V >>> 3, V >>> 0 < 256) {
                                                    o = 12572 + (_ << 1 << 2) | 0, (n = 0 | f[3133]) & (d = 1 << _) ? (K = 0 | f[(d = o + 8 | 0) >> 2], $ = d) : (f[3133] = n | d, K = o, $ = o + 8 | 0), f[$ >> 2] = v, f[K + 12 >> 2] = v, f[v + 8 >> 2] = K, f[v + 12 >> 2] = o;
                                                    break
                                                }
                                                if (J = (o = V >>> 8) ? V >>> 0 > 16777215 ? 31 : V >>> (7 + (a = 14 - ((o = (520192 + (n = o << (d = (o + 1048320 | 0) >>> 16 & 8)) | 0) >>> 16 & 4) | d | (n = (245760 + (_ = n << o) | 0) >>> 16 & 2)) + (_ << n >>> 15) | 0) | 0) & 1 | a << 1 : 0, a = 12836 + (J << 2) | 0, f[v + 28 >> 2] = J, f[4 + (n = v + 16 | 0) >> 2] = 0, f[n >> 2] = 0, !(X & (n = 1 << J))) {
                                                    f[3134] = X | n, f[a >> 2] = v, f[v + 24 >> 2] = a, f[v + 12 >> 2] = v, f[v + 8 >> 2] = v;
                                                    break
                                                }
                                                for (n = V << (31 == (0 | J) ? 0 : 25 - (J >>> 1) | 0), _ = 0 | f[a >> 2];;) {
                                                    if ((-8 & f[_ + 4 >> 2] | 0) == (0 | V)) {
                                                        R = 97;
                                                        break
                                                    }
                                                    if (!(a = 0 | f[(Q = _ + 16 + (n >>> 31 << 2) | 0) >> 2])) {
                                                        R = 96;
                                                        break
                                                    }
                                                    n <<= 1, _ = a
                                                }
                                                if (96 == (0 | R)) {
                                                    f[Q >> 2] = v, f[v + 24 >> 2] = _, f[v + 12 >> 2] = v, f[v + 8 >> 2] = v;
                                                    break
                                                }
                                                if (97 == (0 | R)) {
                                                    a = 0 | f[(n = _ + 8 | 0) >> 2], f[a + 12 >> 2] = v, f[n >> 2] = v, f[v + 8 >> 2] = a, f[v + 12 >> 2] = _, f[v + 24 >> 2] = 0;
                                                    break
                                                }
                                            } else a = V + i | 0, f[Y + 4 >> 2] = 3 | a, f[(n = Y + a + 4 | 0) >> 2] = 1 | f[n >> 2]
                                        } while (0);
                                        return S = r, 0 | Y + 8
                                    }
                                    L = i
                                } else L = i;
                            else L = -1
                        } while (0);
                        if ((Y = 0 | f[3135]) >>> 0 >= L >>> 0) return V = Y - L | 0, Q = 0 | f[3138], V >>> 0 > 15 ? (J = Q + L | 0, f[3138] = J, f[3135] = V, f[J + 4 >> 2] = 1 | V, f[J + V >> 2] = V, f[Q + 4 >> 2] = 3 | L) : (f[3135] = 0, f[3138] = 0, f[Q + 4 >> 2] = 3 | Y, f[(V = Q + Y + 4 | 0) >> 2] = 1 | f[V >> 2]), S = r, 0 | Q + 8;
                        if ((Q = 0 | f[3136]) >>> 0 > L >>> 0) return V = Q - L | 0, f[3136] = V, J = (Y = 0 | f[3139]) + L | 0, f[3139] = J, f[J + 4 >> 2] = 1 | V, f[Y + 4 >> 2] = 3 | L, S = r, 0 | Y + 8;
                        if (0 | f[3251] ? Z = 0 | f[3253] : (f[3253] = 4096, f[3252] = 4096, f[3254] = -1, f[3255] = -1, f[3256] = 0, f[3244] = 0, Y = -16 & t ^ 1431655768, f[t >> 2] = Y, f[3251] = Y, Z = 4096), Y = L + 48 | 0, (Z = (V = Z + (t = L + 47 | 0) | 0) & (J = 0 - Z | 0)) >>> 0 <= L >>> 0) return S = r, 0;
                        if (0 | (X = 0 | f[3243]) && ($ = (K = 0 | f[3241]) + Z | 0) >>> 0 <= K >>> 0 | $ >>> 0 > X >>> 0) return S = r, 0;
                        e: do {
                            if (4 & f[3244]) ae = 0, R = 133;
                            else {
                                X = 0 | f[3139];
                                r: do {
                                    if (X) {
                                        for ($ = 12980; !((K = 0 | f[$ >> 2]) >>> 0 <= X >>> 0 && (K + (0 | f[(ee = $ + 4 | 0) >> 2]) | 0) >>> 0 > X >>> 0);) {
                                            if (!(K = 0 | f[$ + 8 >> 2])) {
                                                R = 118;
                                                break r
                                            }
                                            $ = K
                                        }
                                        if ((_ = V - Q & J) >>> 0 < 2147483647)
                                            if ((0 | (K = 0 | ht(0 | _))) == ((0 | f[$ >> 2]) + (0 | f[ee >> 2]) | 0)) {
                                                if (-1 != (0 | K)) {
                                                    te = _, ne = K, R = 135;
                                                    break e
                                                }
                                                re = _
                                            } else ie = K, oe = _, R = 126;
                                        else re = 0
                                    } else R = 118
                                } while (0);
                                do {
                                    if (118 == (0 | R))
                                        if (-1 != (0 | (X = 0 | ht(0))) && (i = X, _ = (q = (0 == ((K = (_ = 0 | f[3252]) - 1 | 0) & i | 0) ? 0 : (K + i & 0 - _) - i | 0) + Z | 0) + (i = 0 | f[3241]) | 0, q >>> 0 > L >>> 0 & q >>> 0 < 2147483647)) {
                                            if (0 | (K = 0 | f[3243]) && _ >>> 0 <= i >>> 0 | _ >>> 0 > K >>> 0) {
                                                re = 0;
                                                break
                                            }
                                            if ((0 | (K = 0 | ht(0 | q))) == (0 | X)) {
                                                te = q, ne = X, R = 135;
                                                break e
                                            }
                                            ie = K, oe = q, R = 126
                                        } else re = 0
                                } while (0);
                                do {
                                    if (126 == (0 | R)) {
                                        if (q = 0 - oe | 0, !(Y >>> 0 > oe >>> 0 & oe >>> 0 < 2147483647 & -1 != (0 | ie))) {
                                            if (-1 == (0 | ie)) {
                                                re = 0;
                                                break
                                            }
                                            te = oe, ne = ie, R = 135;
                                            break e
                                        }
                                        if ((X = t - oe + (K = 0 | f[3253]) & 0 - K) >>> 0 >= 2147483647) {
                                            te = oe, ne = ie, R = 135;
                                            break e
                                        }
                                        if (-1 == (0 | ht(0 | X))) {
                                            ht(0 | q), re = 0;
                                            break
                                        }
                                        te = X + oe | 0, ne = ie, R = 135;
                                        break e
                                    }
                                } while (0);
                                f[3244] = 4 | f[3244], ae = re, R = 133
                            }
                        } while (0);
                        if (133 == (0 | R) && Z >>> 0 < 2147483647 && !(-1 == (0 | (re = 0 | ht(0 | Z))) | 1 ^ (oe = (ie = (Z = 0 | ht(0)) - re | 0) >>> 0 > (L + 40 | 0) >>> 0) | re >>> 0 < Z >>> 0 & -1 != (0 | re) & -1 != (0 | Z) ^ 1) && (te = oe ? ie : ae, ne = re, R = 135), 135 == (0 | R)) {
                            re = (0 | f[3241]) + te | 0, f[3241] = re, re >>> 0 > (0 | f[3242]) >>> 0 && (f[3242] = re), re = 0 | f[3139];
                            do {
                                if (re) {
                                    for (ae = 12980;;) {
                                        if ((0 | ne) == ((ue = 0 | f[ae >> 2]) + (le = 0 | f[(fe = ae + 4 | 0) >> 2]) | 0)) {
                                            R = 145;
                                            break
                                        }
                                        if (!(ie = 0 | f[ae + 8 >> 2])) break;
                                        ae = ie
                                    }
                                    if (145 == (0 | R) && 0 == (8 & f[ae + 12 >> 2] | 0) && re >>> 0 < ne >>> 0 & re >>> 0 >= ue >>> 0) {
                                        f[fe >> 2] = le + te, ie = re + (oe = 0 == (7 & (ie = re + 8 | 0) | 0) ? 0 : 0 - ie & 7) | 0, Z = (0 | f[3136]) + (te - oe) | 0, f[3139] = ie, f[3136] = Z, f[ie + 4 >> 2] = 1 | Z, f[ie + Z + 4 >> 2] = 40, f[3140] = f[3255];
                                        break
                                    }
                                    for (ne >>> 0 < (0 | f[3137]) >>> 0 && (f[3137] = ne), Z = ne + te | 0, ie = 12980;;) {
                                        if ((0 | f[ie >> 2]) == (0 | Z)) {
                                            R = 153;
                                            break
                                        }
                                        if (!(oe = 0 | f[ie + 8 >> 2])) break;
                                        ie = oe
                                    }
                                    if (153 == (0 | R) && 0 == (8 & f[ie + 12 >> 2] | 0)) {
                                        f[ie >> 2] = ne, f[(ae = ie + 4 | 0) >> 2] = (0 | f[ae >> 2]) + te, oe = ne + (0 == (7 & (ae = ne + 8 | 0) | 0) ? 0 : 0 - ae & 7) | 0, t = Z + (0 == (7 & (ae = Z + 8 | 0) | 0) ? 0 : 0 - ae & 7) | 0, ae = oe + L | 0, Y = t - oe - L | 0, f[oe + 4 >> 2] = 3 | L;
                                        do {
                                            if ((0 | t) != (0 | re)) {
                                                if ((0 | t) == (0 | f[3138])) {
                                                    ee = (0 | f[3135]) + Y | 0, f[3135] = ee, f[3138] = ae, f[ae + 4 >> 2] = 1 | ee, f[ae + ee >> 2] = ee;
                                                    break
                                                }
                                                if (1 == (3 & (ee = 0 | f[t + 4 >> 2]) | 0)) {
                                                    J = -8 & ee, Q = ee >>> 3;
                                                    e: do {
                                                        if (ee >>> 0 < 256) {
                                                            if (V = 0 | f[t + 8 >> 2], (0 | (X = 0 | f[t + 12 >> 2])) == (0 | V)) {
                                                                f[3133] = f[3133] & ~(1 << Q);
                                                                break
                                                            }
                                                            f[V + 12 >> 2] = X, f[X + 8 >> 2] = V;
                                                            break
                                                        }
                                                        V = 0 | f[t + 24 >> 2], X = 0 | f[t + 12 >> 2];
                                                        do {
                                                            if ((0 | X) == (0 | t)) {
                                                                if (_ = 0 | f[(K = 4 + (q = t + 16 | 0) | 0) >> 2]) ce = _, de = K;
                                                                else {
                                                                    if (!(i = 0 | f[q >> 2])) {
                                                                        se = 0;
                                                                        break
                                                                    }
                                                                    ce = i, de = q
                                                                }
                                                                for (;;)
                                                                    if (0 | (_ = 0 | f[(K = ce + 20 | 0) >> 2])) ce = _, de = K;
                                                                    else {
                                                                        if (!(_ = 0 | f[(K = ce + 16 | 0) >> 2])) break;
                                                                        ce = _, de = K
                                                                    }
                                                                f[de >> 2] = 0, se = ce
                                                            } else K = 0 | f[t + 8 >> 2], f[K + 12 >> 2] = X, f[X + 8 >> 2] = K, se = X
                                                        } while (0);
                                                        if (!V) break;
                                                        K = 12836 + ((X = 0 | f[t + 28 >> 2]) << 2) | 0;
                                                        do {
                                                            if ((0 | t) == (0 | f[K >> 2])) {
                                                                if (f[K >> 2] = se, 0 | se) break;
                                                                f[3134] = f[3134] & ~(1 << X);
                                                                break e
                                                            }
                                                            if (f[V + 16 + (((0 | f[V + 16 >> 2]) != (0 | t) & 1) << 2) >> 2] = se, !se) break e
                                                        } while (0);
                                                        if (f[se + 24 >> 2] = V, 0 | (K = 0 | f[(X = t + 16 | 0) >> 2]) && (f[se + 16 >> 2] = K, f[K + 24 >> 2] = se), !(K = 0 | f[X + 4 >> 2])) break;
                                                        f[se + 20 >> 2] = K, f[K + 24 >> 2] = se
                                                    } while (0);
                                                    _e = t + J | 0, he = J + Y | 0
                                                } else _e = t, he = Y;
                                                if (f[(Q = _e + 4 | 0) >> 2] = -2 & f[Q >> 2], f[ae + 4 >> 2] = 1 | he, f[ae + he >> 2] = he, Q = he >>> 3, he >>> 0 < 256) {
                                                    ee = 12572 + (Q << 1 << 2) | 0, ($ = 0 | f[3133]) & (K = 1 << Q) ? (me = 0 | f[(K = ee + 8 | 0) >> 2], pe = K) : (f[3133] = $ | K, me = ee, pe = ee + 8 | 0), f[pe >> 2] = ae, f[me + 12 >> 2] = ae, f[ae + 8 >> 2] = me, f[ae + 12 >> 2] = ee;
                                                    break
                                                }
                                                ee = he >>> 8;
                                                do {
                                                    if (ee) {
                                                        if (he >>> 0 > 16777215) {
                                                            ve = 31;
                                                            break
                                                        }
                                                        ve = he >>> (7 + (_ = 14 - ((Q = (520192 + ($ = ee << (K = (ee + 1048320 | 0) >>> 16 & 8)) | 0) >>> 16 & 4) | K | ($ = (245760 + (X = $ << Q) | 0) >>> 16 & 2)) + (X << $ >>> 15) | 0) | 0) & 1 | _ << 1
                                                    } else ve = 0
                                                } while (0);
                                                if (ee = 12836 + (ve << 2) | 0, f[ae + 28 >> 2] = ve, f[4 + (J = ae + 16 | 0) >> 2] = 0, f[J >> 2] = 0, !((J = 0 | f[3134]) & (_ = 1 << ve))) {
                                                    f[3134] = J | _, f[ee >> 2] = ae, f[ae + 24 >> 2] = ee, f[ae + 12 >> 2] = ae, f[ae + 8 >> 2] = ae;
                                                    break
                                                }
                                                for (_ = he << (31 == (0 | ve) ? 0 : 25 - (ve >>> 1) | 0), J = 0 | f[ee >> 2];;) {
                                                    if ((-8 & f[J + 4 >> 2] | 0) == (0 | he)) {
                                                        R = 194;
                                                        break
                                                    }
                                                    if (!(ee = 0 | f[(be = J + 16 + (_ >>> 31 << 2) | 0) >> 2])) {
                                                        R = 193;
                                                        break
                                                    }
                                                    _ <<= 1, J = ee
                                                }
                                                if (193 == (0 | R)) {
                                                    f[be >> 2] = ae, f[ae + 24 >> 2] = J, f[ae + 12 >> 2] = ae, f[ae + 8 >> 2] = ae;
                                                    break
                                                }
                                                if (194 == (0 | R)) {
                                                    ee = 0 | f[(_ = J + 8 | 0) >> 2], f[ee + 12 >> 2] = ae, f[_ >> 2] = ae, f[ae + 8 >> 2] = ee, f[ae + 12 >> 2] = J, f[ae + 24 >> 2] = 0;
                                                    break
                                                }
                                            } else ee = (0 | f[3136]) + Y | 0, f[3136] = ee, f[3139] = ae, f[ae + 4 >> 2] = 1 | ee
                                        } while (0);
                                        return S = r, 0 | oe + 8
                                    }
                                    for (ae = 12980; !((Y = 0 | f[ae >> 2]) >>> 0 <= re >>> 0 && (we = Y + (0 | f[ae + 4 >> 2]) | 0) >>> 0 > re >>> 0);) ae = 0 | f[ae + 8 >> 2];
                                    Y = (ae = (Y = (ae = we + -47 | 0) + (0 == (7 & (oe = ae + 8 | 0) | 0) ? 0 : 0 - oe & 7) | 0) >>> 0 < (oe = re + 16 | 0) >>> 0 ? re : Y) + 8 | 0, t = ne + (Z = 0 == (7 & (t = ne + 8 | 0) | 0) ? 0 : 0 - t & 7) | 0, ie = te + -40 - Z | 0, f[3139] = t, f[3136] = ie, f[t + 4 >> 2] = 1 | ie, f[t + ie + 4 >> 2] = 40, f[3140] = f[3255], f[(ie = ae + 4 | 0) >> 2] = 27, f[Y >> 2] = f[3245], f[Y + 4 >> 2] = f[3246], f[Y + 8 >> 2] = f[3247], f[Y + 12 >> 2] = f[3248], f[3245] = ne, f[3246] = te, f[3248] = 0, f[3247] = Y, Y = ae + 24 | 0;
                                    do {
                                        t = Y, f[(Y = Y + 4 | 0) >> 2] = 7
                                    } while ((t + 8 | 0) >>> 0 < we >>> 0);
                                    if ((0 | ae) != (0 | re)) {
                                        if (Y = ae - re | 0, f[ie >> 2] = -2 & f[ie >> 2], f[re + 4 >> 2] = 1 | Y, f[ae >> 2] = Y, t = Y >>> 3, Y >>> 0 < 256) {
                                            Z = 12572 + (t << 1 << 2) | 0, (ee = 0 | f[3133]) & (_ = 1 << t) ? (Se = 0 | f[(_ = Z + 8 | 0) >> 2], ke = _) : (f[3133] = ee | _, Se = Z, ke = Z + 8 | 0), f[ke >> 2] = re, f[Se + 12 >> 2] = re, f[re + 8 >> 2] = Se, f[re + 12 >> 2] = Z;
                                            break
                                        }
                                        if (Ee = (Z = Y >>> 8) ? Y >>> 0 > 16777215 ? 31 : Y >>> (7 + ($ = 14 - ((Z = (520192 + (ee = Z << (_ = (Z + 1048320 | 0) >>> 16 & 8)) | 0) >>> 16 & 4) | _ | (ee = (245760 + (t = ee << Z) | 0) >>> 16 & 2)) + (t << ee >>> 15) | 0) | 0) & 1 | $ << 1 : 0, $ = 12836 + (Ee << 2) | 0, f[re + 28 >> 2] = Ee, f[re + 20 >> 2] = 0, f[oe >> 2] = 0, !((ee = 0 | f[3134]) & (t = 1 << Ee))) {
                                            f[3134] = ee | t, f[$ >> 2] = re, f[re + 24 >> 2] = $, f[re + 12 >> 2] = re, f[re + 8 >> 2] = re;
                                            break
                                        }
                                        for (t = Y << (31 == (0 | Ee) ? 0 : 25 - (Ee >>> 1) | 0), ee = 0 | f[$ >> 2];;) {
                                            if ((-8 & f[ee + 4 >> 2] | 0) == (0 | Y)) {
                                                R = 216;
                                                break
                                            }
                                            if (!($ = 0 | f[(Me = ee + 16 + (t >>> 31 << 2) | 0) >> 2])) {
                                                R = 215;
                                                break
                                            }
                                            t <<= 1, ee = $
                                        }
                                        if (215 == (0 | R)) {
                                            f[Me >> 2] = re, f[re + 24 >> 2] = ee, f[re + 12 >> 2] = re, f[re + 8 >> 2] = re;
                                            break
                                        }
                                        if (216 == (0 | R)) {
                                            Y = 0 | f[(t = ee + 8 | 0) >> 2], f[Y + 12 >> 2] = re, f[t >> 2] = re, f[re + 8 >> 2] = Y, f[re + 12 >> 2] = ee, f[re + 24 >> 2] = 0;
                                            break
                                        }
                                    }
                                } else {
                                    0 == (0 | (Y = 0 | f[3137])) | ne >>> 0 < Y >>> 0 && (f[3137] = ne), f[3245] = ne, f[3246] = te, f[3248] = 0, f[3142] = f[3251], f[3141] = -1, Y = 0;
                                    do {
                                        f[12 + (t = 12572 + (Y << 1 << 2) | 0) >> 2] = t, f[t + 8 >> 2] = t, Y = Y + 1 | 0
                                    } while (32 != (0 | Y));
                                    Y = ne + (ee = 0 == (7 & (Y = ne + 8 | 0) | 0) ? 0 : 0 - Y & 7) | 0, t = te + -40 - ee | 0, f[3139] = Y, f[3136] = t, f[Y + 4 >> 2] = 1 | t, f[Y + t + 4 >> 2] = 40, f[3140] = f[3255]
                                }
                            } while (0);
                            if ((te = 0 | f[3136]) >>> 0 > L >>> 0) return ne = te - L | 0, f[3136] = ne, re = (te = 0 | f[3139]) + L | 0, f[3139] = re, f[re + 4 >> 2] = 1 | ne, f[te + 4 >> 2] = 3 | L, S = r, 0 | te + 8
                        }
                        return f[(te = 612) >> 2] = 12, S = r, 0
                    }

                    function Re(e) {
                        var r, t = 0,
                            n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            A = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0;
                        if (e |= 0) {
                            t = e + -8 | 0, n = 0 | f[3137], r = t + (e = -8 & (i = 0 | f[e + -4 >> 2])) | 0;
                            do {
                                if (1 & i) c = t, d = e, _ = t;
                                else {
                                    if (o = 0 | f[t >> 2], !(3 & i)) return;
                                    if (u = o + e | 0, (a = t + (0 - o) | 0) >>> 0 < n >>> 0) return;
                                    if ((0 | a) == (0 | f[3138])) {
                                        if (3 != (3 & (s = 0 | f[(l = r + 4 | 0) >> 2]) | 0)) {
                                            c = a, d = u, _ = a;
                                            break
                                        }
                                        return f[3135] = u, f[l >> 2] = -2 & s, f[a + 4 >> 2] = 1 | u, void(f[a + u >> 2] = u)
                                    }
                                    if (s = o >>> 3, o >>> 0 < 256) {
                                        if (o = 0 | f[a + 8 >> 2], (0 | (l = 0 | f[a + 12 >> 2])) == (0 | o)) {
                                            f[3133] = f[3133] & ~(1 << s), c = a, d = u, _ = a;
                                            break
                                        }
                                        f[o + 12 >> 2] = l, f[l + 8 >> 2] = o, c = a, d = u, _ = a;
                                        break
                                    }
                                    o = 0 | f[a + 24 >> 2], l = 0 | f[a + 12 >> 2];
                                    do {
                                        if ((0 | l) == (0 | a)) {
                                            if (m = 0 | f[(h = 4 + (s = a + 16 | 0) | 0) >> 2]) b = m, w = h;
                                            else {
                                                if (!(p = 0 | f[s >> 2])) {
                                                    v = 0;
                                                    break
                                                }
                                                b = p, w = s
                                            }
                                            for (;;)
                                                if (0 | (m = 0 | f[(h = b + 20 | 0) >> 2])) b = m, w = h;
                                                else {
                                                    if (!(m = 0 | f[(h = b + 16 | 0) >> 2])) break;
                                                    b = m, w = h
                                                }
                                            f[w >> 2] = 0, v = b
                                        } else h = 0 | f[a + 8 >> 2], f[h + 12 >> 2] = l, f[l + 8 >> 2] = h, v = l
                                    } while (0);
                                    if (o) {
                                        if (l = 0 | f[a + 28 >> 2], (0 | a) == (0 | f[(h = 12836 + (l << 2) | 0) >> 2])) {
                                            if (f[h >> 2] = v, !v) {
                                                f[3134] = f[3134] & ~(1 << l), c = a, d = u, _ = a;
                                                break
                                            }
                                        } else if (f[o + 16 + (((0 | f[o + 16 >> 2]) != (0 | a) & 1) << 2) >> 2] = v, !v) {
                                            c = a, d = u, _ = a;
                                            break
                                        }
                                        f[v + 24 >> 2] = o, 0 | (h = 0 | f[(l = a + 16 | 0) >> 2]) && (f[v + 16 >> 2] = h, f[h + 24 >> 2] = v), (h = 0 | f[l + 4 >> 2]) ? (f[v + 20 >> 2] = h, f[h + 24 >> 2] = v, c = a, d = u, _ = a) : (c = a, d = u, _ = a)
                                    } else c = a, d = u, _ = a
                                }
                            } while (0);
                            if (!(_ >>> 0 >= r >>> 0) && 1 & (e = 0 | f[(t = r + 4 | 0) >> 2])) {
                                if (2 & e) f[t >> 2] = -2 & e, f[c + 4 >> 2] = 1 | d, f[_ + d >> 2] = d, M = d;
                                else {
                                    if (v = 0 | f[3138], (0 | r) == (0 | f[3139])) {
                                        if (b = (0 | f[3136]) + d | 0, f[3136] = b, f[3139] = c, f[c + 4 >> 2] = 1 | b, (0 | c) != (0 | v)) return;
                                        return f[3138] = 0, void(f[3135] = 0)
                                    }
                                    if ((0 | r) == (0 | v)) return v = (0 | f[3135]) + d | 0, f[3135] = v, f[3138] = _, f[c + 4 >> 2] = 1 | v, void(f[_ + v >> 2] = v);
                                    v = (-8 & e) + d | 0, b = e >>> 3;
                                    do {
                                        if (e >>> 0 < 256) {
                                            if (w = 0 | f[r + 8 >> 2], (0 | (n = 0 | f[r + 12 >> 2])) == (0 | w)) {
                                                f[3133] = f[3133] & ~(1 << b);
                                                break
                                            }
                                            f[w + 12 >> 2] = n, f[n + 8 >> 2] = w;
                                            break
                                        }
                                        w = 0 | f[r + 24 >> 2], n = 0 | f[r + 12 >> 2];
                                        do {
                                            if ((0 | n) == (0 | r)) {
                                                if (l = 0 | f[(h = 4 + (i = r + 16 | 0) | 0) >> 2]) k = l, E = h;
                                                else {
                                                    if (!(m = 0 | f[i >> 2])) {
                                                        S = 0;
                                                        break
                                                    }
                                                    k = m, E = i
                                                }
                                                for (;;)
                                                    if (0 | (l = 0 | f[(h = k + 20 | 0) >> 2])) k = l, E = h;
                                                    else {
                                                        if (!(l = 0 | f[(h = k + 16 | 0) >> 2])) break;
                                                        k = l, E = h
                                                    }
                                                f[E >> 2] = 0, S = k
                                            } else h = 0 | f[r + 8 >> 2], f[h + 12 >> 2] = n, f[n + 8 >> 2] = h, S = n
                                        } while (0);
                                        if (0 | w) {
                                            if (n = 0 | f[r + 28 >> 2], (0 | r) == (0 | f[(a = 12836 + (n << 2) | 0) >> 2])) {
                                                if (f[a >> 2] = S, !S) {
                                                    f[3134] = f[3134] & ~(1 << n);
                                                    break
                                                }
                                            } else if (f[w + 16 + (((0 | f[w + 16 >> 2]) != (0 | r) & 1) << 2) >> 2] = S, !S) break;
                                            f[S + 24 >> 2] = w, 0 | (a = 0 | f[(n = r + 16 | 0) >> 2]) && (f[S + 16 >> 2] = a, f[a + 24 >> 2] = S), 0 | (a = 0 | f[n + 4 >> 2]) && (f[S + 20 >> 2] = a, f[a + 24 >> 2] = S)
                                        }
                                    } while (0);
                                    if (f[c + 4 >> 2] = 1 | v, f[_ + v >> 2] = v, (0 | c) == (0 | f[3138])) return void(f[3135] = v);
                                    M = v
                                }
                                if (d = M >>> 3, M >>> 0 < 256) return _ = 12572 + (d << 1 << 2) | 0, (e = 0 | f[3133]) & (t = 1 << d) ? (A = 0 | f[(t = _ + 8 | 0) >> 2], y = t) : (f[3133] = e | t, A = _, y = _ + 8 | 0), f[y >> 2] = c, f[A + 12 >> 2] = c, f[c + 8 >> 2] = A, void(f[c + 12 >> 2] = _);
                                L = (_ = M >>> 8) ? M >>> 0 > 16777215 ? 31 : M >>> (7 + (e = 14 - ((_ = (520192 + (y = _ << (A = (_ + 1048320 | 0) >>> 16 & 8)) | 0) >>> 16 & 4) | A | (y = (245760 + (t = y << _) | 0) >>> 16 & 2)) + (t << y >>> 15) | 0) | 0) & 1 | e << 1 : 0, e = 12836 + (L << 2) | 0, f[c + 28 >> 2] = L, f[c + 20 >> 2] = 0, f[c + 16 >> 2] = 0, y = 0 | f[3134], t = 1 << L;
                                do {
                                    if (y & t) {
                                        for (A = M << (31 == (0 | L) ? 0 : 25 - (L >>> 1) | 0), _ = 0 | f[e >> 2];;) {
                                            if ((-8 & f[_ + 4 >> 2] | 0) == (0 | M)) {
                                                g = 73;
                                                break
                                            }
                                            if (!(d = 0 | f[(T = _ + 16 + (A >>> 31 << 2) | 0) >> 2])) {
                                                g = 72;
                                                break
                                            }
                                            A <<= 1, _ = d
                                        }
                                        if (72 == (0 | g)) {
                                            f[T >> 2] = c, f[c + 24 >> 2] = _, f[c + 12 >> 2] = c, f[c + 8 >> 2] = c;
                                            break
                                        }
                                        if (73 == (0 | g)) {
                                            w = 0 | f[(A = _ + 8 | 0) >> 2], f[w + 12 >> 2] = c, f[A >> 2] = c, f[c + 8 >> 2] = w, f[c + 12 >> 2] = _, f[c + 24 >> 2] = 0;
                                            break
                                        }
                                    } else f[3134] = y | t, f[e >> 2] = c, f[c + 24 >> 2] = e, f[c + 12 >> 2] = c, f[c + 8 >> 2] = c
                                } while (0);
                                if (c = (0 | f[3141]) - 1 | 0, f[3141] = c, !c) {
                                    for (D = 12988; c = 0 | f[D >> 2];) D = c + 8 | 0;
                                    f[3141] = -1
                                }
                            }
                        }
                    }

                    function Oe(e, r, t) {
                        r |= 0, t |= 0;
                        var n, i, o, a, u, l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0;
                        n = S, S = S + 48 | 0, i = n + 16 | 0, l = n, o = n + 32 | 0, s = 0 | f[(a = 28 + (e |= 0) | 0) >> 2], f[o >> 2] = s, c = (0 | f[(u = e + 20 | 0) >> 2]) - s | 0, f[o + 4 >> 2] = c, f[o + 8 >> 2] = r, f[o + 12 >> 2] = t, r = c + t | 0, c = e + 60 | 0, f[l >> 2] = f[c >> 2], f[l + 4 >> 2] = o, f[l + 8 >> 2] = 2, s = 0 | Be(0 | Z(146, 0 | l));
                        e: do {
                            if ((0 | r) != (0 | s)) {
                                for (l = 2, d = r, _ = o, h = s; !((0 | h) < 0);) {
                                    if (d = d - h | 0, b = ((p = h >>> 0 > (m = 0 | f[_ + 4 >> 2]) >>> 0) << 31 >> 31) + l | 0, w = h - (p ? m : 0) | 0, f[(v = p ? _ + 8 | 0 : _) >> 2] = (0 | f[v >> 2]) + w, f[(m = v + 4 | 0) >> 2] = (0 | f[m >> 2]) - w, f[i >> 2] = f[c >> 2], f[i + 4 >> 2] = v, f[i + 8 >> 2] = b, (0 | d) == (0 | (h = 0 | Be(0 | Z(146, 0 | i))))) {
                                        k = 3;
                                        break e
                                    }
                                    l = b, _ = v
                                }
                                f[e + 16 >> 2] = 0, f[a >> 2] = 0, f[u >> 2] = 0, f[e >> 2] = 32 | f[e >> 2], E = 2 == (0 | l) ? 0 : t - (0 | f[_ + 4 >> 2]) | 0
                            } else k = 3
                        } while (0);
                        return 3 == (0 | k) && (k = 0 | f[e + 44 >> 2], f[e + 16 >> 2] = k + (0 | f[e + 48 >> 2]), f[a >> 2] = k, f[u >> 2] = k, E = t), S = n, 0 | E
                    }

                    function Be(e) {
                        var r = 0;
                        return (e |= 0) >>> 0 > 4294963200 ? (f[612 >> 2] = 0 - e, r = -1) : r = e, 0 | r
                    }

                    function xe() {
                        return 612
                    }

                    function Ne(e, r, t) {
                        e |= 0, r |= 0, t |= 0;
                        var n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            f = 0,
                            l = 0;
                        e: do {
                            if (t) {
                                for (o = e, a = t, u = r;
                                     (f = 0 | i[o >> 0]) << 24 >> 24 == (l = 0 | i[u >> 0]) << 24 >> 24;) {
                                    if (!(a = a + -1 | 0)) {
                                        n = 0;
                                        break e
                                    }
                                    o = o + 1 | 0, u = u + 1 | 0
                                }
                                n = (255 & f) - (255 & l) | 0
                            } else n = 0
                        } while (0);
                        return 0 | n
                    }

                    function Ie(e, r, t) {
                        e |= 0;
                        var n, o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0;
                        n = 255 & (r |= 0), o = 0 != (0 | (t |= 0));
                        e: do {
                            if (o & 0 != (3 & e | 0))
                                for (a = 255 & r, u = e, l = t;;) {
                                    if ((0 | i[u >> 0]) == a << 24 >> 24) {
                                        s = u, c = l, d = 6;
                                        break e
                                    }
                                    if (!((m = 0 != (0 | (h = l + -1 | 0))) & 0 != (3 & (_ = u + 1 | 0) | 0))) {
                                        p = _, v = h, b = m, d = 5;
                                        break
                                    }
                                    u = _, l = h
                                } else p = e, v = t, b = o, d = 5
                        } while (0);
                        5 == (0 | d) && (b ? (s = p, c = v, d = 6) : (w = p, S = 0));
                        e: do {
                            if (6 == (0 | d))
                                if (p = 255 & r, (0 | i[s >> 0]) == p << 24 >> 24) w = s, S = c;
                                else {
                                    v = 0 | A(n, 16843009);
                                    r: do {
                                        if (c >>> 0 > 3) {
                                            for (b = s, o = c; !((-2139062144 & (t = f[b >> 2] ^ v) ^ -2139062144) & t + -16843009 | 0);) {
                                                if (t = b + 4 | 0, !((e = o + -4 | 0) >>> 0 > 3)) {
                                                    k = t, E = e, d = 11;
                                                    break r
                                                }
                                                b = t, o = e
                                            }
                                            M = b, y = o
                                        } else k = s, E = c, d = 11
                                    } while (0);
                                    if (11 == (0 | d)) {
                                        if (!E) {
                                            w = k, S = 0;
                                            break
                                        }
                                        M = k, y = E
                                    }
                                    for (;;) {
                                        if ((0 | i[M >> 0]) == p << 24 >> 24) {
                                            w = M, S = y;
                                            break e
                                        }
                                        if (v = M + 1 | 0, !(y = y + -1 | 0)) {
                                            w = v, S = 0;
                                            break
                                        }
                                        M = v
                                    }
                                }
                        } while (0);
                        return 0 | (0 | S ? w : 0)
                    }

                    function Fe(e, r, t) {
                        r |= 0, t |= 0;
                        var n, o, a, u, l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0;
                        n = 1794895138 + (0 | f[(e |= 0) >> 2]) | 0, o = 0 | He(0 | f[e + 8 >> 2], n), a = 0 | He(0 | f[e + 12 >> 2], n), u = 0 | He(0 | f[e + 16 >> 2], n);
                        e: do {
                            if (o >>> 0 < r >>> 2 >>> 0 && a >>> 0 < (l = r - (o << 2) | 0) >>> 0 & u >>> 0 < l >>> 0 && 0 == (3 & (u | a) | 0)) {
                                for (l = a >>> 2, s = u >>> 2, c = 0, d = o;;) {
                                    if (v = 0 | He(0 | f[e + ((p = (m = (h = c + (_ = d >>> 1) | 0) << 1) + l | 0) << 2) >> 2], n), !((b = 0 | He(0 | f[e + (p + 1 << 2) >> 2], n)) >>> 0 < r >>> 0 & v >>> 0 < (r - b | 0) >>> 0)) {
                                        w = 0;
                                        break e
                                    }
                                    if (0 | i[e + (b + v) >> 0]) {
                                        w = 0;
                                        break e
                                    }
                                    if (!(v = 0 | Ue(t, e + b | 0))) break;
                                    if (b = (0 | v) < 0, 1 == (0 | d)) {
                                        w = 0;
                                        break e
                                    }
                                    c = b ? c : h, d = b ? _ : d - _ | 0
                                }
                                c = 0 | He(0 | f[e + ((d = m + s | 0) << 2) >> 2], n), w = (l = 0 | He(0 | f[e + (d + 1 << 2) >> 2], n)) >>> 0 < r >>> 0 & c >>> 0 < (r - l | 0) >>> 0 && 0 == (0 | i[e + (l + c) >> 0]) ? e + l | 0 : 0
                            } else w = 0
                        } while (0);
                        return 0 | w
                    }

                    function He(e, r) {
                        var t;
                        return r |= 0, t = 0 | wt(0 | (e |= 0)), 0 | (0 == (0 | r) ? e : t)
                    }

                    function Ue(e, r) {
                        r |= 0;
                        var t = 0,
                            n = 0,
                            o = 0,
                            a = 0;
                        if (t = 0 | i[(e |= 0) >> 0], n = 0 | i[r >> 0], t << 24 >> 24 == 0 || t << 24 >> 24 != n << 24 >> 24) o = n, a = t;
                        else {
                            t = r, r = e;
                            do {
                                t = t + 1 | 0, e = 0 | i[(r = r + 1 | 0) >> 0], n = 0 | i[t >> 0]
                            } while (e << 24 >> 24 != 0 && e << 24 >> 24 == n << 24 >> 24);
                            o = n, a = e
                        }
                        return (255 & a) - (255 & o) | 0
                    }

                    function Ge(e) {
                        return 0 | function(e, r) {
                            e |= 0, r |= 0;
                            var t = 0,
                                n = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0;
                            for (t = 0;;) {
                                if ((0 | s[9096 + t >> 0]) == (0 | e)) {
                                    n = 2;
                                    break
                                }
                                if (87 == (0 | (o = t + 1 | 0))) {
                                    a = 9184, u = 87, n = 5;
                                    break
                                }
                                t = o
                            }
                            if (2 == (0 | n) && (t ? (a = 9184, u = t, n = 5) : l = 9184), 5 == (0 | n))
                                for (;;) {
                                    n = 0, t = a;
                                    do {
                                        e = t, t = t + 1 | 0
                                    } while (0 != (0 | i[e >> 0]));
                                    if (!(u = u + -1 | 0)) {
                                        l = t;
                                        break
                                    }
                                    a = t, n = 5
                                }
                            return 0 | function(e, r) {
                                return 0 | function(e, r) {
                                    e |= 0;
                                    var t = 0;
                                    return 0 | (0 | (t = (r |= 0) ? 0 | Fe(0 | f[r >> 2], 0 | f[r + 4 >> 2], e) : 0) ? t : e)
                                }(e |= 0, r |= 0)
                            }(l, 0 | f[r + 20 >> 2])
                        }(e |= 0, 0 | f[184])
                    }

                    function We(e, r, t) {
                        e |= 0, r |= 0, t |= 0;
                        var n, o, a, u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0;
                        n = S, S = S + 224 | 0, o = n + 120 | 0, a = n, l = n + 136 | 0, c = 40 + (s = u = n + 80 | 0) | 0;
                        do {
                            f[s >> 2] = 0, s = s + 4 | 0
                        } while ((0 | s) < (0 | c));
                        return f[o >> 2] = f[t >> 2], (0 | Ur(0, r, o, a, u)) < 0 ? d = -1 : (_ = (0 | f[e + 76 >> 2]) > -1 ? 0 | Gr(e) : 0, s = 32 & (t = 0 | f[e >> 2]), (0 | i[e + 74 >> 0]) < 1 && (f[e >> 2] = -33 & t), 0 | f[(t = e + 48 | 0) >> 2] ? b = 0 | Ur(e, r, o, a, u) : (h = 0 | f[(c = e + 44 | 0) >> 2], f[c >> 2] = l, f[(m = e + 28 | 0) >> 2] = l, f[(p = e + 20 | 0) >> 2] = l, f[t >> 2] = 80, f[(v = e + 16 | 0) >> 2] = l + 80, l = 0 | Ur(e, r, o, a, u), h ? (Dt[15 & f[e + 36 >> 2]](e, 0, 0), w = 0 == (0 | f[p >> 2]) ? -1 : l, f[c >> 2] = h, f[t >> 2] = 0, f[v >> 2] = 0, f[m >> 2] = 0, f[p >> 2] = 0, b = w) : b = l), u = 0 | f[e >> 2], f[e >> 2] = u | s, 0 | _ && Wr(e), d = 0 == (32 & u | 0) ? b : -1), S = n, 0 | d
                    }

                    function Ye(e) {
                        var r, t = 0;
                        Ve(0 | f[(t = 2360 + (e |= 0) | 0) >> 2]), f[t >> 2] = 0, f[e + 2364 >> 2] = 0, (e = 0 | f[(t = e + 2344 | 0) >> 2]) ? (qe(0 | f[(r = e + 20 | 0) >> 2]), f[r >> 2] = 0, Ve(e), f[t >> 2] = 0) : f[t >> 2] = 0
                    }

                    function Ve(e) {
                        Re(e |= 0)
                    }

                    function qe(e) {
                        (e |= 0) && (je(e), Ve(e))
                    }

                    function je(e) {
                        var r = 0,
                            t = 0,
                            n = 0;
                        if (e |= 0) {
                            Ve(0 | f[e + 160 >> 2]), Ve(0 | f[e + 172 >> 2]), ze(0 | f[e + 168 >> 2]), Xe(e + 124 | 0), Xe(e + 136 | 0), t = 56 + (r = e + 120 | 0) | 0;
                            do {
                                f[r >> 2] = 0, r = r + 4 | 0
                            } while ((0 | r) < (0 | t));
                            if (Ve(0 | f[(r = e + 16 | 0) >> 2]), f[r >> 2] = 0, (0 | f[(r = e + 176 | 0) >> 2]) > 0) {
                                t = 0;
                                do {
                                    Ve(0 | f[(n = e + 180 + (20 * t | 0) + 16 | 0) >> 2]), f[n >> 2] = 0, t = t + 1 | 0
                                } while ((0 | t) < (0 | f[r >> 2]))
                            }
                            f[r >> 2] = 0, f[e + 260 >> 2] = 0, Ve(0 | f[(r = e + 264 | 0) >> 2]), f[r >> 2] = 0, f[e + 12 >> 2] = 0
                        }
                    }

                    function ze(e) {
                        (e |= 0) && Ve(e)
                    }

                    function Xe(e) {
                        (e |= 0) && (Ve(0 | f[e >> 2]), f[e >> 2] = 0)
                    }

                    function Ke(e, r, t, n) {
                        e |= 0, t |= 0, n |= 0;
                        var o, a, u, l, c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0;
                        if (o = 0 | f[(r |= 0) >> 2], (0 | t) < 0 | (0 | n) < 1 | (n + t | 0) > (0 | (u = 0 | f[(a = r + 88 | 0) >> 2]))) return 0;
                        l = e + 2356 | 0;
                        e: do {
                            if (!(0 | f[l >> 2])) {
                                d = 0 | f[(c = e + 2344 | 0) >> 2];
                                do {
                                    if (d) y = n, L = d, g = 16;
                                    else {
                                        if (_ = 0 | $e(1, 0, 144), f[c >> 2] = _, !_) return 0;
                                        if (_ = 0 | f[r >> 2], _ = 0 | Je(m = 0 | _t(0 | (h = 0 | f[a >> 2]), ((0 | h) < 0) << 31 >> 31 | 0, 0 | _, ((0 | _) < 0) << 31 >> 31 | 0), M, 1), f[e + 2360 >> 2] = _, 0 | _ && (f[e + 2364 >> 2] = _, f[e + 2368 >> 2] = 0, m = 0 | f[c >> 2], v = 1 + (h = 0 | f[e + 2348 >> 2]) | 0, b = (p = 0 | f[e + 2352 >> 2]) - 1 | 0, w = m + 24 | 0, Qe(), f[m + 136 >> 2] = _, f[m >> 2] = f[r >> 2], _ = r + 4 | 0, f[(S = m + 4 | 0) >> 2] = f[_ >> 2], p >>> 0 >= 2) && (p = 3 & i[h >> 0], f[(k = m + 8 | 0) >> 2] = 255 & p, f[m + 12 >> 2] = (0 | s[h >> 0]) >>> 2 & 3, E = (0 | s[h >> 0]) >>> 4 & 3, f[m + 16 >> 2] = E, (255 & p) <= 1) && !(E >>> 0 > 1 | (0 | s[h >> 0]) > 63) && (Ze(w, 520), er(0, w), f[m + 64 >> 2] = m, f[w >> 2] = f[r >> 2], f[m + 28 >> 2] = f[_ >> 2], f[m + 96 >> 2] = f[r + 72 >> 2], f[m + 100 >> 2] = f[r + 76 >> 2], f[m + 104 >> 2] = f[r + 80 >> 2], f[m + 108 >> 2] = f[r + 84 >> 2], f[m + 112 >> 2] = f[a >> 2], 0 | (0 | f[k >> 2] ? 0 | rr(m, v, b) : b >>> 0 >= (0 | A(0 | f[S >> 2], 0 | f[m >> 2])) >>> 0 & 1))) {
                                            if (b = 0 | f[c >> 2], 1 == (0 | f[b + 16 >> 2])) {
                                                y = u - t | 0, L = b, g = 16;
                                                break
                                            }
                                            f[e + 2372 >> 2] = 0, y = n, L = b, g = 16;
                                            break
                                        }
                                    }
                                } while (0);
                                do {
                                    if (16 == (0 | g)) {
                                        if (d = 0 | f[L >> 2], b = 0 | f[L + 112 >> 2], 0 | f[L + 8 >> 2]) {
                                            if (!(0 | tr(L, v = y + t | 0))) break;
                                            T = v
                                        } else {
                                            if (m = 0 | f[(v = e + 2368 | 0) >> 2], S = 0 | A(d, t), k = 1 + (0 | f[e + 2348 >> 2]) + S | 0, w = (_ = 0 | f[e + 2364 >> 2]) + S | 0, E = (0 | y) > 0, h = 0 | f[(S = L + 12 | 0) >> 2])
                                                if (E) {
                                                    if (P = 0 | A(d, t + -1 + y | 0), Ut[31 & f[11764 + (h << 2) >> 2]](m, k, w, d), 1 != (0 | y)) {
                                                        D = k, C = w, O = 1;
                                                        do {
                                                            D = D + d | 0, B = C, C = C + d | 0, Ut[31 & f[11764 + (f[S >> 2] << 2) >> 2]](B, D, C, d), O = O + 1 | 0
                                                        } while ((0 | O) != (0 | y))
                                                    }
                                                    R = _ + P | 0
                                                } else R = m;
                                            else if (E) {
                                                for (p = t + -1 + y | 0, D = k, C = 0, P = w; lt(0 | P, 0 | D, 0 | d), (0 | (C = C + 1 | 0)) != (0 | y);) D = D + d | 0, P = P + d | 0;
                                                R = _ + (0 | A(d, p)) | 0
                                            } else R = m;
                                            f[v >> 2] = R, T = y + t | 0
                                        }
                                        if ((0 | T) < (0 | b)) {
                                            if (!(0 | f[l >> 2])) break e
                                        } else f[l >> 2] = 1;
                                        if (0 | (O = 0 | f[c >> 2]) && (qe(0 | f[(d = O + 20 | 0) >> 2]), f[d >> 2] = 0, Ve(O)), f[c >> 2] = 0, (0 | (O = 0 | f[e + 2372 >> 2])) <= 0) break e;
                                        if (d = 0 | f[r + 84 >> 2], 0 | nr((C = (0 | f[e + 2364 >> 2]) + (0 | A(d, o)) | 0) + (D = 0 | f[r + 76 >> 2]) | 0, (0 | f[r + 80 >> 2]) - D | 0, (0 | f[a >> 2]) - d | 0, o, O)) break e
                                    }
                                } while (0);
                                return Ve(0 | f[(O = e + 2360 | 0) >> 2]), f[O >> 2] = 0, f[e + 2364 >> 2] = 0, 0 | (O = 0 | f[c >> 2]) && (qe(0 | f[(d = O + 20 | 0) >> 2]), f[d >> 2] = 0, Ve(O)), f[c >> 2] = 0, 0
                            }
                        } while (0);
                        return 0 | (0 | f[e + 2364 >> 2]) + (0 | A(o, t))
                    }

                    function $e(e, r, t) {
                        var n = 0,
                            i = 0;
                        return n = 0 | _t(0 | (t |= 0), 0, 0 | (e |= 0), 0 | (r |= 0)), 0 == (0 | e) & 0 == (0 | r) || (i = (0 | n) == (0 | n) & 0 == (0 | M), n = 0 | dt(2147418112, 0, 0 | e, 0 | r), i & (0 < (r = M) >>> 0 | 0 == (0 | r) & t >>> 0 <= n >>> 0)) ? 0 | function(e, r) {
                            r |= 0;
                            var t = 0,
                                n = 0;
                            return (e |= 0) ? (t = 0 | A(r, e), n = (r | e) >>> 0 > 65535 ? (0 | (t >>> 0) / (e >>> 0)) == (0 | r) ? t : -1 : t) : n = 0, (t = 0 | Pe(n)) && 3 & f[t + -4 >> 2] ? (at(0 | t, 0, 0 | n), 0 | t) : 0 | t
                        }(e, t) : 0
                    }

                    function Je(e, r, t) {
                        var n, i = 0,
                            o = 0;
                        return n = 0 | _t(0 | (t |= 0), 0, 0 | (e |= 0), 0 | (r |= 0)), 0 == (0 | e) & 0 == (0 | r) || (i = (0 | n) == (0 | n) & 0 == (0 | M), o = 0 | dt(2147418112, 0, 0 | e, 0 | r), i & (0 < (r = M) >>> 0 | 0 == (0 | r) & t >>> 0 <= o >>> 0)) ? 0 | Pe(n) : 0
                    }

                    function Qe() {
                        var e;
                        0 | ut(12444) || (e = 0 | f[2893], (0 | f[37]) != (0 | e) && (f[2941] = 0, f[2942] = 1, f[2943] = 2, f[2944] = 3, f[2937] = 0, f[2938] = 1, f[2939] = 2, f[2940] = 3), f[37] = e, bt(12444))
                    }

                    function Ze(e, r) {
                        if (512 != (-256 & (r |= 0) | 0)) return 0;
                        if (!(e |= 0)) return 1;
                        e = (r = e) + 108 | 0;
                        do {
                            f[r >> 2] = 0, r = r + 4 | 0
                        } while ((0 | r) < (0 | e));
                        return 1
                    }

                    function er(e, r) {
                        e |= 0, f[44 + (r |= 0) >> 2] = 4, f[r + 48 >> 2] = 5, f[r + 52 >> 2] = 5, f[r + 40 >> 2] = e
                    }

                    function rr(e, r, t) {
                        e |= 0, r |= 0, t |= 0;
                        var n, o, a, u, l = 0,
                            s = 0,
                            c = 0,
                            d = 0;
                        if (!(n = 0 | $e(1, 0, 272))) return 0;
                        f[n >> 2] = 0, f[n + 4 >> 2] = 2, dr(), l = 0 | f[e >> 2], f[(o = n + 100 | 0) >> 2] = l, s = 0 | f[(a = e + 4 | 0) >> 2], f[(u = n + 104 | 0) >> 2] = s, c = e + 24 | 0, f[n + 8 >> 2] = c, f[e + 64 >> 2] = e, f[c >> 2] = l, f[e + 28 >> 2] = s, f[n >> 2] = 0, _r(n + 24 | 0, r, t);
                        e: do {
                            if (0 | hr(0 | f[e >> 2], 0 | f[a >> 2], 1, n, 0)) {
                                r: do {
                                    if (1 == (0 | f[n + 176 >> 2]) && 3 == (0 | f[n + 180 >> 2]) && (0 | f[n + 120 >> 2]) <= 0) {
                                        if ((0 | (t = 0 | f[n + 164 >> 2])) > 0) {
                                            r = 0 | f[n + 168 >> 2], s = 0;
                                            do {
                                                if (0 | i[f[r + (548 * s | 0) + 4 >> 2] >> 0]) {
                                                    d = 14;
                                                    break r
                                                }
                                                if (0 | i[f[r + (548 * s | 0) + 8 >> 2] >> 0]) {
                                                    d = 14;
                                                    break r
                                                }
                                                if (0 | i[f[r + (548 * s | 0) + 12 >> 2] >> 0]) {
                                                    d = 14;
                                                    break r
                                                }
                                                s = s + 1 | 0
                                            } while ((0 | s) < (0 | t))
                                        }
                                        if (f[e + 132 >> 2] = 1, t = 0 | f[o >> 2], r = 0 | _t(0 | (s = 0 | f[u >> 2]), ((0 | s) < 0) << 31 >> 31 | 0, 0 | t, ((0 | t) < 0) << 31 >> 31 | 0), f[n + 20 >> 2] = 0, t = 0 | Je(r, M, 1), f[n + 16 >> 2] = t, !t) {
                                            f[n >> 2] = 1;
                                            break e
                                        }
                                    } else d = 14
                                } while (0);do {
                                    if (14 == (0 | d)) {
                                        if (f[e + 132 >> 2] = 0, t = 0 | f[e >> 2], r = 0 | f[o >> 2], l = 0 | _t(0 | (s = 0 | f[u >> 2]), ((0 | s) < 0) << 31 >> 31 | 0, 0 | r, ((0 | r) < 0) << 31 >> 31 | 0), r = M, s = 65535 & t, t = 0 | ot(0 | (c = 0 | mt(0 | t, ((0 | t) < 0) << 31 >> 31 | 0, 4)), 0 | M, 0 | s, 0), r = 0 | Je(c = 0 | ot(0 | t, 0 | M, 0 | l, 0 | r), M, 4), f[n + 16 >> 2] = r, r) {
                                            f[n + 20 >> 2] = r + (l << 2) + (s << 2);
                                            break
                                        }
                                        f[n >> 2] = 1, f[n + 20 >> 2] = 0;
                                        break e
                                    }
                                } while (0);
                                return f[e + 20 >> 2] = n,
                                    1
                            }
                        } while (0);
                        return je(n), Ve(n), 0
                    }

                    function tr(e, r) {
                        r |= 0;
                        var t, n, o, u, l, c, _, h, m, p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0,
                            Pe = 0,
                            Re = 0,
                            Oe = 0;
                        if (t = 0 | f[20 + (e |= 0) >> 2], (0 | f[t + 108 >> 2]) >= (0 | r)) return 1;
                        if ((e = 0 | f[(p = e + 132 | 0) >> 2]) ? v = e : (ir(), v = 0 | f[p >> 2]), e = 0 | f[t + 16 >> 2], p = 0 | f[t + 104 >> 2], n = 0 | f[t + 100 >> 2], !v) return 0 | fr(t, e, n, p, r, 1);
                        u = (0 | (o = 0 | f[(v = t + 112 | 0) >> 2])) / (0 | n) | 0, l = (0 | o) % (0 | n) | 0, c = t + 24 | 0, _ = 0 | A(n, p), p = 0 | A(n, r), h = 0 | f[t + 148 >> 2];
                        e: do {
                            if ((0 | o) < (0 | p))
                                if ((w = 0 | f[(b = t + 152 | 0) >> 2]) ? (k = (0 | f[t + 160 >> 2]) + ((0 | A(0 | f[t + 156 >> 2], u >> w)) + (l >> w) << 2) | 0, S = 0 | f[k >> 2]) : S = 0, 0 | f[(k = t + 48 | 0) >> 2]) Pe = u, Re = o, Oe = k, me = 67;
                                else {
                                    for (E = t + 160 | 0, M = t + 156 | 0, y = t + 44 | 0, L = t + 40 | 0, g = t + 36 | 0, T = u, D = l, C = o, P = (0 | f[(w = t + 168 | 0) >> 2]) + (548 * S | 0) | 0;;) {
                                        D & h ? x = P : ((R = 0 | f[b >> 2]) ? (B = (0 | f[E >> 2]) + ((0 | A(0 | f[M >> 2], T >> R)) + (D >> R) << 2) | 0, O = 0 | f[B >> 2]) : O = 0, x = (0 | f[w >> 2]) + (548 * O | 0) | 0), (0 | (B = 0 | f[y >> 2])) > 31 ? (or(c), N = 0 | f[y >> 2]) : N = B, B = 0 | f[x >> 2], R = 0 | ft(0 | (I = 0 | f[(R = c) >> 2]), 0 | (F = 0 | f[R + 4 >> 2]), 63 & N | 0), (255 & (U = 0 | i[(R = B + ((H = 255 & R) << 2) | 0) >> 0])) > 8 ? (G = N + 8 | 0, f[y >> 2] = G, W = 0 | ft(0 | I, 0 | F, 63 & G | 0), V = Y = R + ((0 | d[B + (H << 2) + 2 >> 1]) << 2) + (((1 << (255 & U) - 8) - 1 & W) << 2) | 0, q = G, j = 0 | i[Y >> 0]) : (V = R, q = N, j = U), U = (255 & j) + q | 0, f[y >> 2] = U, Y = 65535 & (R = 0 | a[V + 2 >> 1]);
                                        do {
                                            if ((65535 & R) < 256) i[e + C >> 0] = R, G = C + 1 | 0, (0 | (W = D + 1 | 0)) >= (0 | n) ? (0 | T) < (0 | r) & 0 == (15 & (H = T + 1 | 0) | 0) ? (ar(t, H), z = G, X = H, K = 0, $ = x) : (z = G, X = H, K = 0, $ = x) : (z = G, X = T, K = W, $ = x);
                                            else {
                                                if ((65535 & R) >= 280) {
                                                    J = C, Q = 0, Z = k;
                                                    break e
                                                }
                                                if (G = Y + -258 >> 1, (0 | (W = Y + -256 | 0)) < 4 ? (ee = W, re = U, te = I, ne = F) : (H = (0 | ur(c, G)) + ((1 & W | 2) << G) | 0, G = c, ee = H, re = 0 | f[y >> 2], te = 0 | f[G >> 2], ne = 0 | f[G + 4 >> 2]), G = ee + 1 | 0, H = 0 | f[x + 16 >> 2], W = 0 | ft(0 | te, 0 | ne, 63 & re | 0), (255 & (ie = 0 | i[(W = H + ((B = 255 & W) << 2) | 0) >> 0])) > 8 ? (oe = re + 8 | 0, f[y >> 2] = oe, ae = 0 | ft(0 | te, 0 | ne, 63 & oe | 0), fe = ue = W + ((0 | d[H + (B << 2) + 2 >> 1]) << 2) + (((1 << (255 & ie) - 8) - 1 & ae) << 2) | 0, le = 0 | i[ue >> 0], se = oe) : (fe = W, le = ie, se = re), ie = (255 & le) + se | 0, f[y >> 2] = ie, oe = 65535 & (W = 0 | a[fe + 2 >> 1]), (0 | ie) > 31 && or(c), ie = oe + -2 >> 1, ((ce = (65535 & W) < 4 ? oe : (0 | ur(c, ie)) + ((1 & oe | 2) << ie) | 0) + 1 | 0) > 120 ? de = ce + -119 | 0 : (ie = 0 | s[5175 + ce >> 0], de = (0 | (oe = (0 | A(ie >>> 4, n)) + (8 - (15 & ie)) | 0)) > 1 ? oe : 1), (_ - C | 0) < (0 | G) | (0 | C) < (0 | de)) {
                                                    J = C, Q = 0, Z = k;
                                                    break e
                                                }
                                                ie = (oe = e + C | 0) + (0 - de) | 0;
                                                r: do {
                                                    if ((0 | G) > 7) {
                                                        switch (0 | de) {
                                                            case 1:
                                                                W = 0 | i[ie >> 0], _e = 0 | A(255 & W, 16843009), he = W;
                                                                break;
                                                            case 2:
                                                                _e = 65537 * (65535 & (W = s[ie >> 0] | s[ie + 1 >> 0] << 8)) | 0, he = 255 & W;
                                                                break;
                                                            case 4:
                                                                _e = W = s[ie >> 0] | s[ie + 1 >> 0] << 8 | s[ie + 2 >> 0] << 16 | s[ie + 3 >> 0] << 24, he = 255 & W;
                                                                break;
                                                            default:
                                                                me = 50;
                                                                break r
                                                        }
                                                        do {
                                                            if (3 & oe) {
                                                                if (W = ie + 1 | 0, ue = oe + 1 | 0, i[oe >> 0] = he, ae = _e << 24 | _e >>> 8, !(3 & ue)) {
                                                                    pe = ae, ve = ee, be = ue, we = W;
                                                                    break
                                                                }
                                                                for (Se = W, ke = ue, Ee = ae, Me = ee;;) {
                                                                    if (ae = Se + 1 | 0, ue = ke + 1 | 0, i[ke >> 0] = 0 | i[Se >> 0], W = Ee << 24 | Ee >>> 8, B = Me + -1 | 0, !(3 & ue)) {
                                                                        pe = W, ve = B, be = ue, we = ae;
                                                                        break
                                                                    }
                                                                    Se = ae, ke = ue, Ee = W, Me = B
                                                                }
                                                            } else pe = _e, ve = G, be = oe, we = ie
                                                        } while (0);
                                                        if ((0 | (B = ve >> 2)) > 0) {
                                                            W = 0;
                                                            do {
                                                                f[be + (W << 2) >> 2] = pe, W = W + 1 | 0
                                                            } while ((0 | W) != (0 | B));
                                                            Ae = B << 2
                                                        } else Ae = 0;
                                                        if (!((0 | Ae) < (0 | ve))) break;
                                                        ye = Ae;
                                                        do {
                                                            i[be + ye >> 0] = 0 | i[we + ye >> 0], ye = ye + 1 | 0
                                                        } while ((0 | ye) != (0 | ve))
                                                    } else me = 50
                                                } while (0);
                                                do {
                                                    if (50 == (0 | me)) {
                                                        if (me = 0, (0 | de) >= (0 | G)) {
                                                            lt(0 | oe, 0 | ie, 0 | G);
                                                            break
                                                        }
                                                        if (!((0 | G) > 0)) break;
                                                        Le = 0;
                                                        do {
                                                            i[oe + Le >> 0] = 0 | i[ie + Le >> 0], Le = Le + 1 | 0
                                                        } while ((0 | Le) != (0 | G))
                                                    }
                                                } while (0);
                                                if (ie = G + C | 0, (0 | (oe = G + D | 0)) < (0 | n)) ge = T, Te = oe;
                                                else
                                                    for (B = T, W = oe;;) {
                                                        if (oe = W - n | 0, (0 | B) < (0 | r) & 0 == (15 & (ue = B + 1 | 0) | 0) && ar(t, ue), (0 | oe) < (0 | n)) {
                                                            ge = ue, Te = oe;
                                                            break
                                                        }
                                                        B = ue, W = oe
                                                    }
                                                if ((0 | ie) >= (0 | p) | 0 == (Te & h | 0)) {
                                                    z = ie, X = ge, K = Te, $ = x;
                                                    break
                                                }(W = 0 | f[b >> 2]) ? (B = (0 | f[E >> 2]) + ((0 | A(0 | f[M >> 2], ge >> W)) + (Te >> W) << 2) | 0, De = 0 | f[B >> 2]) : De = 0, z = ie, X = ge, K = Te, $ = (0 | f[w >> 2]) + (548 * De | 0) | 0
                                            }
                                        } while (0);
                                        if (0 | f[k >> 2]) break;
                                        if (Ce = (0 | f[L >> 2]) == (0 | f[g >> 2]) ? (0 | f[y >> 2]) > 64 : 0, f[k >> 2] = 1 & Ce, !((0 | z) < (0 | p) & (1 ^ Ce))) {
                                            Pe = X, Re = z, Oe = k, me = 67;
                                            break e
                                        }
                                        T = X, D = K, C = z, P = $
                                    }
                                    f[k >> 2] = 1, Pe = X, Re = z, Oe = k, me = 67
                                } else Pe = u, Re = o, Oe = t + 48 | 0, me = 67
                        } while (0);
                        return 67 == (0 | me) && (ar(t, (0 | Pe) > (0 | r) ? r : Pe), J = Re, Q = 1, Z = Oe), m = 0 | f[Z >> 2] ? 1 : (0 | f[t + 40 >> 2]) == (0 | f[t + 36 >> 2]) ? (0 | f[t + 44 >> 2]) > 64 : 0, f[Z >> 2] = 1 & m, 0 == (0 | Q) | (0 | _) > (0 | J) & m ? (f[t >> 2] = m ? 5 : 3, 0) : (f[v >> 2] = J, 0 | Q)
                    }

                    function nr(e, r, t, n, o) {
                        n |= 0;
                        var u, f, l, c, _, h, m, p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0;
                        if (u = S, S = S + 256 | 0, p = u, v = (0 | (o |= 0)) / 25 | 0, o >>> 0 > 100) return S = u, 0;
                        if (0 == (0 | (e |= 0)) | (0 | (r |= 0)) < 1 | (0 | (t |= 0)) < 1) return S = u, 0;
                        if (o = r + -1 | 0, b = (1 | v << 1) > (0 | r) ? o >> 1 : v, v = t + -1 | 0, b = 0 - (f = (1 | b << 1) > (0 | t) ? v >> 1 : b) | 0, (0 | f) <= 0) return S = u, 1;
                        if (!(l = 0 | Je(1, 0, 4094 + (k = r << 1) + (E = 0 | A(1 + (w = f << 1 | 1) | 0, k)) | 0))) return S = u, 0;
                        if (c = l, at(0 | (M = (_ = l + ((0 | A(w, r)) << 1) | 0) + (0 - r << 1) | 0), 0, 0 | k), E = (h = l + E | 0) + k | 0, m = 262144 / ((0 | A(w, w)) >>> 0) | 0, at(0 | p, 0, 256), (w = (0 | r) > 0) & (0 | t) > 0)
                            for (y = 0, L = e, g = 255, T = 0, D = 0, C = 255;;) {
                                P = 0, R = g, O = T, B = C, x = D;
                                do {
                                    R = (I = (0 | (N = 0 | s[L + P >> 0])) < (0 | B)) ? N : R, B = I ? N : B, O = (I = (0 | N) > (0 | x)) ? N : O, x = I ? N : x, i[p + N >> 0] = 1, P = P + 1 | 0
                                } while ((0 | P) != (0 | r));
                                if ((0 | (y = y + 1 | 0)) == (0 | t)) {
                                    F = R, H = O, U = B, G = x;
                                    break
                                }
                                L = L + n | 0, g = R, T = O, D = x, C = B
                            } else F = 255, H = 0, U = 255, G = 0;
                        for (C = -1, D = 0, T = 0, g = G - U | 0; 0 | i[p + D >> 0] ? (U = T + 1 | 0, (0 | C) > -1 ? (W = D, Y = U, V = (0 | (G = D - C | 0)) < (0 | g) ? G : g) : (W = D, Y = U, V = g)) : (W = C, Y = T, V = g), 256 != (0 | (D = D + 1 | 0));) C = W, T = Y, g = V;
                        g = E + 2046 | 0, V = (E = V << 2) - (T = 12 * V >> 2) | 0, W = 1;
                        do {
                            C = (q = (0 | W) > (0 | T) ? (0 | E) > (0 | W) ? (0 | A(E - W | 0, T)) / (0 | V) | 0 : 0 : W) >> 2, a[g + (W << 1) >> 1] = C, a[g + (0 - W << 1) >> 1] = 0 - C, W = W + 1 | 0
                        } while (1024 != (0 | W));
                        if (a[g >> 1] = 0, (0 | Y) > 2 & (0 | b) < (0 | t))
                            for (Y = (0 | f) < 0, W = r - f | 0, q = ~f, V = _ + (o << 1) | 0, o = k + -2 - f | 0, k = f + -1 | 0, T = f + 1 | 0, E = b, b = e, C = e, e = c, D = M;;) {
                                if (M = D, D = e, w)
                                    for (p = 0, U = 0; L = (G = 0 | s[b + p >> 0]) + U + (0 | d[M + (p << 1) >> 1]) | 0, y = D + (p << 1) | 0, a[_ + (p << 1) >> 1] = L - (0 | d[y >> 1]), a[y >> 1] = L, (0 | (p = p + 1 | 0)) != (0 | r);) U = G + (65535 & U) | 0;
                                if (e = (0 | (U = D + (r << 1) | 0)) == (0 | _) ? c : U, b = (0 | E) > -1 & (0 | E) < (0 | v) ? b + n | 0 : b, (0 | E) < (0 | f)) j = C;
                                else {
                                    if (Y) z = 0;
                                    else {
                                        U = 0;
                                        do {
                                            p = (0 | A((0 | d[_ + (f - U << 1) >> 1]) + (0 | d[_ + (k + U << 1) >> 1]) & 65535, m)) >>> 16 & 65535, a[h + (U << 1) >> 1] = p, U = U + 1 | 0
                                        } while ((0 | U) != (0 | T));
                                        z = T
                                    }
                                    if ((0 | z) < (0 | W)) {
                                        U = z;
                                        do {
                                            p = (0 | A((0 | d[_ + (U + f << 1) >> 1]) - (0 | d[_ + (U + q << 1) >> 1]) & 65535, m)) >>> 16 & 65535, a[h + (U << 1) >> 1] = p, U = U + 1 | 0
                                        } while ((0 | U) != (0 | W));
                                        X = W
                                    } else X = z;
                                    if ((0 | X) < (0 | r)) {
                                        U = X;
                                        do {
                                            p = (0 | A((d[V >> 1] << 1) - (0 | d[_ + (o - U << 1) >> 1]) - (0 | d[_ + (U + q << 1) >> 1]) & 65535, m)) >>> 16 & 65535, a[h + (U << 1) >> 1] = p, U = U + 1 | 0
                                        } while ((0 | U) != (0 | r))
                                    }
                                    if (w) {
                                        U = 0;
                                        do {
                                            (0 | (M = 0 | s[(p = C + U | 0) >> 0])) < (0 | H) & (0 | M) > (0 | F) && (K = (B = (0 | a[g + ((0 | d[h + (U << 1) >> 1]) - (M << 2) << 1) >> 1]) + M | 0) >>> 0 > 255 ? 255 + (B >> 31 & -255) | 0 : 255 & B, i[p >> 0] = K), U = U + 1 | 0
                                        } while ((0 | U) != (0 | r))
                                    }
                                    j = C + n | 0
                                }
                                if ((0 | (E = E + 1 | 0)) == (0 | t)) break;
                                C = j
                            }
                        return Ve(l), S = u, 1
                    }

                    function ir() {
                        var e;
                        0 | ut(12276) || (e = 0 | f[2893], (0 | f[31]) != (0 | e) && (f[2882] = 1, f[2883] = 4, f[2885] = 5, f[2890] = 1, f[2884] = 4, f[2886] = 1, f[2887] = 2, f[2888] = 2, f[2889] = 2, f[2891] = 1, f[2892] = 2), f[31] = e, bt(12276))
                    }

                    function or(e) {
                        var r, t, n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0;
                        r = 16 + (e |= 0) | 0, n = 0 | f[(t = e + 20 | 0) >> 2];
                        e: do {
                            if ((0 | n) > 7)
                                for (i = e + 8 | 0, o = 0 | f[e + 12 >> 2], a = n, u = 0 | f[r >> 2];;) {
                                    if (u >>> 0 >= o >>> 0) {
                                        l = a;
                                        break e
                                    }
                                    if (d = 0 | ft(0 | f[(c = e) >> 2], 0 | f[c + 4 >> 2], 8), c = M, f[(_ = e) >> 2] = d, f[_ + 4 >> 2] = c, _ = 0 | mt(0 | s[(0 | f[i >> 2]) + u >> 0], 0, 56), f[(h = e) >> 2] = _ | d, f[h + 4 >> 2] = M | c, u = u + 1 | 0, f[r >> 2] = u, c = a + -8 | 0, f[t >> 2] = c, (0 | c) <= 7) {
                                        l = c;
                                        break
                                    }
                                    a = c
                                } else l = n
                        } while (0);
                        (0 != (0 | f[(n = e + 24 | 0) >> 2]) || (0 | l) > 64 && (0 | f[r >> 2]) == (0 | f[e + 12 >> 2])) && (f[n >> 2] = 1, f[t >> 2] = 0)
                    }

                    function ar(e, r) {
                        r |= 0;
                        var t, n, i, o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0;
                        if (o = 0 | f[8 + (e |= 0) >> 2], a = 0 | f[o + 40 >> 2], n = e + 108 | 0, u = 0 | f[((0 | f[(t = a + 12 | 0) >> 2]) >>> 0 < 2 ? o + 84 | 0 : n) >> 2], (0 | (s = (0 | (l = 0 | f[n >> 2])) < (0 | u) ? u : l)) >= (0 | r)) return f[(e + 116 | 0) >> 2] = r, void(f[n >> 2] = r);
                        if (l = 0 | f[o >> 2], o = (0 | f[a + 136 >> 2]) + (0 | A(l, s)) | 0, cr(e + 180 | 0, s, r, (0 | f[e + 16 >> 2]) + (0 | A(0 | f[e + 100 >> 2], s)) | 0, o), !(u = 0 | f[t >> 2])) return f[(e + 116 | 0) >> 2] = r, void(f[n >> 2] = r);
                        if (i = a + 140 | 0, a = 0 | A(l, r + -1 - s | 0), Ut[31 & f[11764 + (u << 2) >> 2]](0 | f[i >> 2], o, o, l), (0 | (u = s + 1 | 0)) != (0 | r)) {
                            s = o, c = u;
                            do {
                                u = s, s = s + l | 0, Ut[31 & f[11764 + (f[t >> 2] << 2) >> 2]](u, s, s, l), c = c + 1 | 0
                            } while ((0 | c) != (0 | r))
                        }
                        f[i >> 2] = o + a, f[(e + 116 | 0) >> 2] = r, f[n >> 2] = r
                    }

                    function ur(e, r) {
                        r |= 0;
                        var t, n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0;
                        t = 24 + (e |= 0) | 0;
                        do {
                            if ((0 | r) < 25 & 0 == (0 | f[t >> 2])) {
                                if (l = (u = 0 | ft(0 | (i = 0 | f[(n = e) >> 2]), 0 | (o = 0 | f[n + 4 >> 2]), 63 & (a = 0 | f[(n = e + 20 | 0) >> 2]) | 0)) & f[24 + (r << 2) >> 2], u = a + r | 0, f[n >> 2] = u, a = e + 16 | 0, (0 | u) <= 7) return 0 | l;
                                for (d = e + 8 | 0, _ = 0 | f[e + 12 >> 2], h = 0 | f[a >> 2], m = i, i = o, o = u; !(h >>> 0 >= _ >>> 0);) {
                                    if (u = 0 | ft(0 | m, 0 | i, 8), p = M, f[(v = e) >> 2] = u, f[v + 4 >> 2] = p, m = (v = 0 | mt(0 | s[(0 | f[d >> 2]) + h >> 0], 0, 56)) | u, i = M | p, f[(p = e) >> 2] = m, f[p + 4 >> 2] = i, h = h + 1 | 0, f[a >> 2] = h, p = o + -8 | 0, f[n >> 2] = p, (0 | p) <= 7) {
                                        c = l, b = 10;
                                        break
                                    }
                                    o = p
                                }
                                if (10 == (0 | b)) return 0 | c;
                                if ((0 | o) > 64 && (0 | f[a >> 2]) == (0 | f[e + 12 >> 2])) {
                                    f[t >> 2] = 1, w = l, S = n;
                                    break
                                }
                                return 0 | l
                            }
                            f[t >> 2] = 1, w = 0, S = e + 20 | 0
                        } while (0);
                        return f[S >> 2] = 0, 0 | w
                    }

                    function fr(e, r, t, n, o, u) {
                        r |= 0, t |= 0, n |= 0, o |= 0, u |= 0;
                        var l, c, _, h, m, p, v, b, w, S, k, E, M, y, L, g, T, D, C, P, R, O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0,
                            Pe = 0,
                            Re = 0,
                            Oe = 0,
                            Be = 0,
                            xe = 0,
                            Ne = 0,
                            Ie = 0,
                            Fe = 0,
                            He = 0,
                            Ue = 0,
                            Ge = 0,
                            We = 0,
                            Ye = 0,
                            Ve = 0,
                            qe = 0,
                            je = 0,
                            ze = 0,
                            Xe = 0,
                            Ke = 0,
                            $e = 0,
                            Je = 0,
                            Qe = 0,
                            Ze = 0,
                            er = 0,
                            rr = 0,
                            tr = 0,
                            nr = 0,
                            ir = 0,
                            ar = 0,
                            fr = 0,
                            sr = 0,
                            cr = 0;
                        B = (0 | (O = 0 | f[(l = 112 + (e |= 0) | 0) >> 2])) / (0 | t) | 0, x = (0 | O) % (0 | t) | 0, c = e + 24 | 0, N = r + (O << 2) | 0, _ = r + ((0 | A(n, t)) << 2) | 0, h = r + ((n = 0 | A(o, t)) << 2) | 0, p = 280 + (I = 0 | f[(m = e + 120 | 0) >> 2]) | 0, F = 0 | f[(v = e + 56 | 0) >> 2] ? B : 16777216, b = (0 | I) > 0, I = e + 124 | 0, w = b ? I : 0, S = 0 | f[e + 148 >> 2], k = e + 152 | 0, (0 | O) < (0 | n) ? ((n = 0 | f[k >> 2]) ? (O = (0 | f[e + 160 >> 2]) + ((0 | A(0 | f[e + 156 >> 2], B >> n)) + (x >> n) << 2) | 0, H = 0 | f[O >> 2]) : H = 0, U = (0 | f[(O = e + 168 | 0) >> 2]) + (548 * H | 0) | 0, G = O) : (U = 0, G = e + 168 | 0), O = e + 160 | 0, H = e + 156 | 0, n = e + 44 | 0, E = e + 48 | 0, M = e + 40 | 0, y = e + 36 | 0, L = r, r = _, g = 0 == (0 | u), T = e + 64 | 0, D = e + 96 | 0, C = e + 136 | 0, P = w + 4 | 0, R = 0 != (0 | u), W = B, B = x, x = N, Y = N, N = F, F = U;
                        e: for (;;) {
                            U = Y, V = W, q = B, j = x, z = N, X = F;
                            r: for (;;) {
                                for (K = V, $ = q, J = j, Q = z, Z = X;;) {
                                    if (J >>> 0 >= h >>> 0) {
                                        ee = 110;
                                        break e
                                    }
                                    if ((0 | K) < (0 | Q) ? re = Q : (f[T >> 2] = f[c >> 2], f[T + 4 >> 2] = f[c + 4 >> 2], f[T + 8 >> 2] = f[c + 8 >> 2], f[T + 12 >> 2] = f[c + 12 >> 2], f[T + 16 >> 2] = f[c + 16 >> 2], f[T + 20 >> 2] = f[c + 20 >> 2], f[T + 24 >> 2] = f[c + 24 >> 2], f[T + 28 >> 2] = f[c + 28 >> 2], f[D >> 2] = J - L >> 2, (0 | f[m >> 2]) > 0 && lr(I, C), re = K + 8 | 0), $ & S ? oe = Z : ((te = 0 | f[k >> 2]) ? (ie = (0 | f[O >> 2]) + ((0 | A(0 | f[H >> 2], K >> te)) + ($ >> te) << 2) | 0, ne = 0 | f[ie >> 2]) : ne = 0, oe = (0 | f[G >> 2]) + (548 * ne | 0) | 0), 0 | f[oe + 28 >> 2]) {
                                        ee = 21;
                                        break r
                                    }
                                    if ((0 | f[n >> 2]) > 31 && or(c), 0 | f[oe + 32 >> 2]) {
                                        if (se = 0 | ft(0 | (ae = 0 | f[(ue = c) >> 2]), 0 | (te = 0 | f[ue + 4 >> 2]), 63 & (ue = 0 | f[n >> 2]) | 0), se = 0 | f[oe + 36 + ((fe = 63 & se) << 3) >> 2], ce = 0 | f[oe + 36 + (fe << 3) + 4 >> 2], fe = se + ue | 0, (0 | se) < 256 ? (f[n >> 2] = fe, f[J >> 2] = ce, ke = 0) : (f[n >> 2] = fe + -256, ke = ce), 0 | f[E >> 2]) {
                                            Se = 1, ee = 113;
                                            break e
                                        }
                                        if ((0 | f[M >> 2]) == (0 | f[y >> 2]) && (0 | f[n >> 2]) > 64) {
                                            ee = 111;
                                            break e
                                        }
                                        if (!ke) {
                                            Ee = Y;
                                            break r
                                        }
                                        ve = ke, be = ae, we = te
                                    } else {
                                        if (ie = 0 | f[oe >> 2], fe = 0 | ft(0 | (ae = 0 | f[(te = c) >> 2]), 0 | (ue = 0 | f[te + 4 >> 2]), 63 & (te = 0 | f[n >> 2]) | 0), (255 & (se = 0 | i[(fe = ie + ((le = 255 & fe) << 2) | 0) >> 0])) > 8 ? (ce = te + 8 | 0, f[n >> 2] = ce, de = 0 | ft(0 | ae, 0 | ue, 63 & ce | 0), he = _e = fe + ((0 | d[ie + (le << 2) + 2 >> 1]) << 2) + ((de & (1 << (255 & se) - 8) - 1) << 2) | 0, me = 0 | i[_e >> 0], pe = ce) : (he = fe, me = se, pe = te), f[n >> 2] = (255 & me) + pe, 0 | f[E >> 2]) {
                                            Se = 1, ee = 113;
                                            break e
                                        }
                                        ve = 0 | d[he + 2 >> 1], be = ae, we = ue
                                    }
                                    if ((0 | f[M >> 2]) == (0 | f[y >> 2]) && (0 | f[n >> 2]) > 64) {
                                        ee = 111;
                                        break e
                                    }
                                    if ((0 | ve) < 256) {
                                        ee = 38;
                                        break r
                                    }
                                    if ((0 | ve) >= 280) {
                                        ee = 104;
                                        break r
                                    }
                                    if (ae = ve + -258 >> 1, (0 | (te = ve + -256 | 0)) < 4 ? (Me = te, Ae = be, ye = we) : (Me = ce = (0 | ur(c, ae)) + ((1 & te | 2) << ae) | 0, Ae = 0 | f[(ae = c) >> 2], ye = 0 | f[ae + 4 >> 2]), ae = Me + 1 | 0, ce = 0 | f[oe + 16 >> 2], fe = 0 | ft(0 | Ae, 0 | ye, 63 & (te = 0 | f[n >> 2]) | 0), (255 & (ue = 0 | i[(fe = ce + ((se = 255 & fe) << 2) | 0) >> 0])) > 8 ? (_e = te + 8 | 0, f[n >> 2] = _e, de = 0 | ft(0 | Ae, 0 | ye, 63 & _e | 0), Le = le = fe + ((0 | d[ce + (se << 2) + 2 >> 1]) << 2) + ((de & (1 << (255 & ue) - 8) - 1) << 2) | 0, ge = 0 | i[le >> 0], Te = _e) : (Le = fe, ge = ue, Te = te), te = (255 & ge) + Te | 0, f[n >> 2] = te, fe = 65535 & (ue = 0 | a[Le + 2 >> 1]), (0 | te) > 31 && or(c), te = fe + -2 >> 1, ((De = (65535 & ue) < 4 ? fe : (0 | ur(c, te)) + ((1 & fe | 2) << te) | 0) + 1 | 0) > 120 ? Ce = De + -119 | 0 : (te = 0 | s[5175 + De >> 0], Ce = (0 | (fe = (0 | A(te >>> 4, t)) + (8 - (15 & te)) | 0)) > 1 ? fe : 1), 0 | f[E >> 2]) {
                                        Se = 1, ee = 113;
                                        break e
                                    }
                                    if ((0 | f[M >> 2]) == (0 | f[y >> 2]) && (0 | f[n >> 2]) > 64) {
                                        ee = 111;
                                        break e
                                    }
                                    if (((fe = J) - L >> 2 | 0) < (0 | Ce)) break e;
                                    if ((r - fe >> 2 | 0) < (0 | ae)) break e;
                                    te = J + (0 - Ce << 2) | 0;
                                    do {
                                        if (0 == (3 & fe | 0) & (0 | ae) > 3 & (0 | Ce) < 3) {
                                            if (1 == (0 | Ce) ? (Pe = ue = 0 | f[te >> 2], Re = ue, Oe = ue) : (Pe = _e = 0 | f[(ue = te) >> 2], Re = _e, Oe = 0 | f[ue + 4 >> 2]), 4 & fe ? (f[J >> 2] = Pe, Be = Me, xe = J + 4 | 0, Ne = te + 4 | 0, Ie = Oe, Fe = Re) : (Be = ae, xe = J, Ne = te, Ie = Re, Fe = Oe), (0 | (ue = Be >> 1)) > 0) {
                                                _e = 0;
                                                do {
                                                    f[(le = xe + (_e << 3) | 0) >> 2] = Ie, f[le + 4 >> 2] = Fe, _e = _e + 1 | 0
                                                } while ((0 | _e) != (0 | ue));
                                                He = ue << 1
                                            } else He = 0;
                                            if (!(1 & Be)) break;
                                            f[xe + (He << 2) >> 2] = f[Ne + (He << 2) >> 2]
                                        } else {
                                            if ((0 | Ce) >= (0 | ae)) {
                                                lt(0 | J, 0 | te, ae << 2 | 0);
                                                break
                                            }
                                            if (!((0 | ae) > 0)) break;
                                            Ue = 0;
                                            do {
                                                f[J + (Ue << 2) >> 2] = f[te + (Ue << 2) >> 2], Ue = Ue + 1 | 0
                                            } while ((0 | Ue) != (0 | ae))
                                        }
                                    } while (0);
                                    Ge = J + (ae << 2) | 0, te = ae + $ | 0;
                                    t: do {
                                        if ((0 | te) < (0 | t)) We = K, Ye = te;
                                        else {
                                            if (g)
                                                for (fe = te, ue = K;;) {
                                                    if (le = ue + 1 | 0, (0 | (_e = fe - t | 0)) < (0 | t)) {
                                                        We = le, Ye = _e;
                                                        break t
                                                    }
                                                    fe = _e, ue = le
                                                } else Ve = te, qe = K;
                                            for (;;) {
                                                if (ue = Ve - t | 0, (0 | qe) < (0 | o) & 0 == (15 & (fe = qe + 1 | 0) | 0) && Rt[15 & u](e, fe), (0 | ue) < (0 | t)) {
                                                    We = fe, Ye = ue;
                                                    break
                                                }
                                                Ve = ue, qe = fe
                                            }
                                        }
                                    } while (0);
                                    if (Ye & S ? ((te = 0 | f[k >> 2]) ? (ae = (0 | f[O >> 2]) + ((0 | A(0 | f[H >> 2], We >> te)) + (Ye >> te) << 2) | 0, ze = 0 | f[ae >> 2]) : ze = 0, je = (0 | f[G >> 2]) + (548 * ze | 0) | 0) : je = oe, b) break;
                                    K = We, $ = Ye, J = Ge, Q = re, Z = je
                                }
                                if (Y >>> 0 < Ge >>> 0) {
                                    ee = 102;
                                    break
                                }
                                V = We, q = Ye, j = Ge, z = re, X = je
                            }
                            do {
                                if (21 == (0 | ee)) ee = 0, Xe = Y, Ke = 0 | f[oe + 24 >> 2], ee = 50;
                                else if (38 == (0 | ee)) {
                                    if (ee = 0, 0 | f[oe + 20 >> 2]) {
                                        Xe = Y, Ke = f[oe + 24 >> 2] | ve << 8, ee = 50;
                                        break
                                    }
                                    if (X = 0 | f[oe + 4 >> 2], j = 0 | ft(0 | be, 0 | we, 63 & (z = 0 | f[n >> 2]) | 0), (255 & (V = 0 | i[(j = X + ((q = 255 & j) << 2) | 0) >> 0])) > 8 ? (Z = z + 8 | 0, f[n >> 2] = Z, Q = 0 | ft(0 | be, 0 | we, 63 & Z | 0), $e = ae = j + ((0 | d[X + (q << 2) + 2 >> 1]) << 2) + ((Q & (1 << (255 & V) - 8) - 1) << 2) | 0, Je = 0 | i[ae >> 0], Qe = Z) : ($e = j, Je = V, Qe = z), z = (255 & Je) + Qe | 0, f[n >> 2] = z, V = 0 | d[$e + 2 >> 1], (0 | z) > 31 ? (or(c), j = c, Ze = 0 | f[n >> 2], er = 0 | f[j >> 2], rr = 0 | f[j + 4 >> 2]) : (Ze = z, er = be, rr = we), z = 0 | f[oe + 8 >> 2], j = 0 | ft(0 | er, 0 | rr, 63 & Ze | 0), (255 & (ae = 0 | i[(j = z + ((Z = 255 & j) << 2) | 0) >> 0])) > 8 ? (Q = Ze + 8 | 0, f[n >> 2] = Q, q = 0 | ft(0 | er, 0 | rr, 63 & Q | 0), tr = X = j + ((0 | d[z + (Z << 2) + 2 >> 1]) << 2) + ((q & (1 << (255 & ae) - 8) - 1) << 2) | 0, nr = 0 | i[X >> 0], ir = Q) : (tr = j, nr = ae, ir = Ze), ae = (255 & nr) + ir | 0, f[n >> 2] = ae, j = 0 | d[tr + 2 >> 1], Q = 0 | f[oe + 12 >> 2], X = 0 | ft(0 | er, 0 | rr, 63 & ae | 0), (255 & (Z = 0 | i[(X = Q + ((q = 255 & X) << 2) | 0) >> 0])) > 8 ? (z = ae + 8 | 0, f[n >> 2] = z, te = 0 | ft(0 | er, 0 | rr, 63 & z | 0), ar = fe = X + ((0 | d[Q + (q << 2) + 2 >> 1]) << 2) + (((1 << (255 & Z) - 8) - 1 & te) << 2) | 0, fr = 0 | i[fe >> 0], sr = z) : (ar = X, fr = Z, sr = ae), ae = (255 & fr) + sr | 0, f[n >> 2] = ae, 0 | f[E >> 2]) {
                                        Se = 1, ee = 113;
                                        break e
                                    }
                                    if ((0 | ae) > 64 && (0 | f[M >> 2]) == (0 | f[y >> 2])) {
                                        ee = 111;
                                        break e
                                    }
                                    Xe = Y, Ke = V << 16 | ve << 8 | j | (0 | d[ar + 2 >> 1]) << 24, ee = 50
                                } else {
                                    if (102 == (0 | ee)) {
                                        ee = 0, j = 0 | f[w >> 2], ae = ((Ge >>> 0 > (V = Y + 4 | 0) >>> 0 ? Ge : V) + -1 + (0 - U) | 0) >>> 2, Z = Y;
                                        do {
                                            X = 0 | f[Z >> 2], Z = Z + 4 | 0, z = j + ((0 | A(X, 506832829)) >>> (0 | f[P >> 2]) << 2) | 0, f[z >> 2] = X
                                        } while (Z >>> 0 < Ge >>> 0);
                                        W = We, B = Ye, x = Ge, Y = V + (ae << 2) | 0, N = re, F = je;
                                        continue e
                                    }
                                    if (104 == (0 | ee)) {
                                        if (ee = 0, (0 | ve) >= (0 | p)) break e;
                                        if (Z = ve + -280 | 0, j = 0 | f[w >> 2], Y >>> 0 < J >>> 0) {
                                            X = (J + -1 + (0 - U) | 0) >>> 2, z = Y;
                                            do {
                                                fe = 0 | f[z >> 2], z = z + 4 | 0, te = j + ((0 | A(fe, 506832829)) >>> (0 | f[P >> 2]) << 2) | 0, f[te >> 2] = fe
                                            } while (z >>> 0 < J >>> 0);
                                            cr = Y + 4 + (X << 2) | 0
                                        } else cr = Y;
                                        Xe = cr, Ke = 0 | f[j + (Z << 2) >> 2], ee = 50
                                    }
                                }
                            } while (0);
                            if (50 == (0 | ee) && (ee = 0, f[J >> 2] = Ke, Ee = Xe), U = J + 4 | 0, (0 | (z = $ + 1 | 0)) < (0 | t)) W = K, B = z, x = U, Y = Ee, N = re, F = oe;
                            else if (R & (0 | K) < (0 | o) & 0 == (15 & (z = K + 1 | 0) | 0) && Rt[15 & u](e, z), b & Ee >>> 0 < U >>> 0) {
                                for (ae = 0 | f[w >> 2], V = ((Ee >>> 0 > J >>> 0 ? Ee : J) + 3 + (0 - Ee) | 0) >>> 2, fe = Ee; te = 0 | f[fe >> 2], q = ae + ((0 | A(te, 506832829)) >>> (0 | f[P >> 2]) << 2) | 0, f[q >> 2] = te, fe >>> 0 < J >>> 0;) fe = fe + 4 | 0;
                                W = z, B = 0, x = U, Y = Ee + 4 + (V << 2) | 0, N = re, F = oe
                            } else W = z, B = 0, x = U, Y = Ee, N = re, F = oe
                        }
                        if (110 == (0 | ee) && (0 | f[E >> 2] ? (Se = 1, ee = 113) : ee = 111), 111 == (0 | ee) && ((0 | f[M >> 2]) == (0 | f[y >> 2]) ? (Se = (0 | f[n >> 2]) > 64, ee = 113) : (Se = 0, ee = 113)), 113 == (0 | ee)) {
                            if (f[E >> 2] = 1 & Se, J >>> 0 < _ >>> 0 & Se & 0 != (0 | f[v >> 2])) return f[e >> 2] = 5, f[c >> 2] = f[T >> 2], f[c + 4 >> 2] = f[T + 4 >> 2], f[c + 8 >> 2] = f[T + 8 >> 2], f[c + 12 >> 2] = f[T + 12 >> 2], f[c + 16 >> 2] = f[T + 16 >> 2], f[c + 20 >> 2] = f[T + 20 >> 2], f[c + 24 >> 2] = f[T + 24 >> 2], f[c + 28 >> 2] = f[T + 28 >> 2], f[l >> 2] = f[D >> 2], (0 | f[m >> 2]) <= 0 ? 1 : (lr(C, I), 1);
                            if (!Se) return g || Rt[15 & u](e, (0 | K) > (0 | o) ? o : K), f[e >> 2] = 0, f[l >> 2] = J - L >> 2, 1
                        }
                        return f[e >> 2] = 3, 0
                    }

                    function lr(e, r) {
                        e |= 0, lt(0 | f[(r |= 0) >> 2], 0 | f[e >> 2], 4 << f[r + 8 >> 2] | 0)
                    }

                    function sr(e, r, t, n, o) {
                        r |= 0, t |= 0, n |= 0, o |= 0;
                        var a, u, l, s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0;
                        switch (a = S, S = S + 16 | 0, u = a, l = 0 | f[(s = 8 + (e |= 0) | 0) >> 2], 0 | f[e >> 2]) {
                            case 2:
                                return c = 0 | A(l, t - r | 0), xt[31 & f[2946]](n, c, o), void(S = a);
                            case 0:
                                if (r) v = r, b = n, w = o, k = l + -1 | 0;
                                else {
                                    if (d = (c = 0 | f[n >> 2]) - 16777216 & -16711936 | 16711935 & c, f[o >> 2] = d, c = n + 4 | 0, _ = l + -1 | 0, h = o + 4 | 0, (0 | l) > 1) {
                                        m = 0, p = d;
                                        do {
                                            p = (-16711936 & (d = 0 | f[c + (m << 2) >> 2])) + (-16711936 & p) & -16711936 | (16711935 & d) + (16711935 & p) & 16711935, f[h + (m << 2) >> 2] = p, m = m + 1 | 0
                                        } while ((0 | m) != (0 | _))
                                    }
                                    v = 1, b = n + (l << 2) | 0, w = o + (l << 2) | 0, k = _
                                }
                                p = (m = 1 << (_ = 0 | f[e + 4 >> 2])) - 1 | 0, h = (m + k | 0) >>> _;
                                e: do {
                                    if ((0 | v) < (0 | t)) {
                                        if (k = 0 - l | 0, c = 0 - m | 0, (0 | l) <= 1)
                                            for (d = v, E = b, M = w;;) {
                                                if (y = 0 | f[M + (k << 2) >> 2], L = 0 | f[E >> 2], f[M >> 2] = (-16711936 & L) + (-16711936 & y) & -16711936 | (16711935 & L) + (16711935 & y) & 16711935, (0 | (d = d + 1 | 0)) == (0 | t)) break e;
                                                E = E + (l << 2) | 0, M = M + (l << 2) | 0
                                            }
                                        for (M = (0 | f[e + 16 >> 2]) + ((0 | A(h, v >> _)) << 2) | 0, E = v, d = b, y = w;;) {
                                            for (L = 0 | f[y + (k << 2) >> 2], g = 0 | f[d >> 2], f[y >> 2] = (-16711936 & g) + (-16711936 & L) & -16711936 | (16711935 & g) + (16711935 & L) & 16711935, L = 1, g = M; D = L, L = (0 | (T = (L & c) + m | 0)) > (0 | l) ? l : T, C = y + (D << 2) | 0, Ut[31 & f[11808 + (((0 | f[g >> 2]) >>> 8 & 15) << 2) >> 2]](d + (D << 2) | 0, C + (k << 2) | 0, L - D | 0, C), !((0 | l) <= (0 | T));) g = g + 4 | 0;
                                            if ((0 | (g = E + 1 | 0)) == (0 | t)) break;
                                            M = 0 == (g & p | 0) ? M + (h << 2) | 0 : M, E = g, d = d + (l << 2) | 0, y = y + (l << 2) | 0
                                        }
                                    }
                                } while (0);
                                return (0 | f[e + 12 >> 2]) == (0 | t) ? void(S = a) : (lt(o + (0 - l << 2) | 0, o + ((0 | A(l, ~r + t | 0)) << 2) | 0, l << 2 | 0), void(S = a));
                            case 1:
                                if (m = (p = 1 << (h = 0 | f[e + 4 >> 2])) - 1 | 0, b = l - (w = l & 0 - p) | 0, v = (l + -1 + p | 0) >>> h, (0 | t) <= (0 | r)) return void(S = a);
                                if (_ = (0 | f[e + 16 >> 2]) + ((0 | A(v, r >> h)) << 2) | 0, h = u + 1 | 0, y = u + 2 | 0, !((0 | w) > 0)) {
                                    for (d = (0 | l) > 0, E = o, M = _, _ = r, k = n; i[u >> 0] = 0, i[u + 1 >> 0] = 0, i[u + 2 >> 0] = 0, d ? (c = 0 | f[M >> 2], i[u >> 0] = c, i[h >> 0] = c >>> 8, i[y >> 0] = c >>> 16, Ut[31 & f[3016]](u, k, b, E), x = k + (b << 2) | 0, N = E + (b << 2) | 0) : (x = k, N = E), (0 | (c = _ + 1 | 0)) != (0 | t);) E = N, M = 0 == (c & m | 0) ? M + (v << 2) | 0 : M, _ = c, k = x;
                                    return void(S = a)
                                }
                                for (P = o, R = _, O = r, B = n;;) {
                                    i[u >> 0] = 0, i[u + 1 >> 0] = 0, i[u + 2 >> 0] = 0, x = B + (w << 2) | 0, k = R, _ = B, M = P;
                                    do {
                                        N = k, k = k + 4 | 0, E = 0 | f[N >> 2], i[u >> 0] = E, i[h >> 0] = E >>> 8, i[y >> 0] = E >>> 16, Ut[31 & f[3016]](u, _, p, M), _ = _ + (p << 2) | 0, M = M + (p << 2) | 0
                                    } while (_ >>> 0 < x >>> 0);
                                    if (_ >>> 0 < (B + (l << 2) | 0) >>> 0 ? (x = 0 | f[k >> 2], i[u >> 0] = x, i[h >> 0] = x >>> 8, i[y >> 0] = x >>> 16, Ut[31 & f[3016]](u, _, b, M), I = _ + (b << 2) | 0, F = M + (b << 2) | 0) : (I = _, F = M), (0 | (x = O + 1 | 0)) == (0 | t)) break;
                                    P = F, R = 0 == (x & m | 0) ? R + (v << 2) | 0 : R, O = x, B = I
                                }
                                return void(S = a);
                            case 3:
                                if ((0 | n) == (0 | o) & (0 | (B = 0 | f[(I = e + 4 | 0) >> 2])) > 0) {
                                    if (R = 0 | A((l + -1 + (1 << B) | 0) >>> B, O = t - r | 0), pt(0 | (v = n + ((0 | A(l, O)) << 2) + (0 - R << 2) | 0), 0 | n, R << 2 | 0), R = 0 | f[I >> 2], I = 0 | f[s >> 2], s = 0 | f[e + 16 >> 2], (0 | (O = 8 >>> R)) >= 8) return Ft[15 & f[3017]](v, s, n, r, t, I), void(S = a);
                                    if (m = (1 << R) - 1 | 0, R = (1 << O) - 1 | 0, !((0 | t) > (0 | r) & (0 | I) > 0)) return void(S = a);
                                    for (H = v, U = n, G = r;;) {
                                        for (v = 0, F = 0, P = H, b = U; v & m ? (W = F, Y = P) : (W = (0 | f[P >> 2]) >>> 8 & 255, Y = P + 4 | 0), f[b >> 2] = f[s + ((W & R) << 2) >> 2], (0 | (v = v + 1 | 0)) != (0 | I);) F = W >>> O, P = Y, b = b + 4 | 0;
                                        if ((0 | (G = G + 1 | 0)) == (0 | t)) break;
                                        H = Y, U = U + (I << 2) | 0
                                    }
                                    return void(S = a)
                                }
                                if (I = 0 | f[e + 16 >> 2], (0 | (e = 8 >>> B)) >= 8) return Ft[15 & f[3017]](n, I, o, r, t, l), void(S = a);
                                if (U = (1 << B) - 1 | 0, B = (1 << e) - 1 | 0, !((0 | t) > (0 | r) & (0 | l) > 0)) return void(S = a);
                                for (V = n, q = o, j = r;;) {
                                    for (r = 0, o = 0, n = V, Y = q; r & U ? (z = o, X = n) : (z = (0 | f[n >> 2]) >>> 8 & 255, X = n + 4 | 0), f[Y >> 2] = f[I + ((z & B) << 2) >> 2], (0 | (r = r + 1 | 0)) != (0 | l);) o = z >>> e, n = X, Y = Y + 4 | 0;
                                    if ((0 | (j = j + 1 | 0)) == (0 | t)) break;
                                    V = X, q = q + (l << 2) | 0
                                }
                                return void(S = a);
                            default:
                                return void(S = a)
                        }
                    }

                    function cr(e, r, t, n, o) {
                        r |= 0, t |= 0, n |= 0, o |= 0;
                        var a, u, l, c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0;
                        if (a = 8 >>> (c = 0 | f[4 + (e |= 0) >> 2]), u = 0 | f[e + 8 >> 2], l = 0 | f[e + 16 >> 2], (0 | a) >= 8) Ft[15 & f[2945]](n, l, o, r, t, u);
                        else if (e = (1 << c) - 1 | 0, c = (1 << a) - 1 | 0, (0 | r) < (0 | t) & (0 | u) > 0)
                            for (d = n, _ = o, h = r;;) {
                                for (r = 0, o = 0, n = d, m = _; o & e ? (p = r, v = n) : (p = 0 | s[n >> 0], v = n + 1 | 0), i[m >> 0] = (0 | f[l + ((p & c) << 2) >> 2]) >>> 8, (0 | (o = o + 1 | 0)) != (0 | u);) r = p >>> a, n = v, m = m + 1 | 0;
                                if ((0 | (h = h + 1 | 0)) == (0 | t)) break;
                                d = v, _ = _ + u | 0
                            }
                    }

                    function dr() {
                        var e;
                        0 | ut(12304) || (e = 0 | f[2893], (0 | f[32]) != (0 | e) && (f[2968] = 3, f[2969] = 4, f[2970] = 5, f[2971] = 6, f[2972] = 7, f[2973] = 8, f[2974] = 9, f[2975] = 10, f[2976] = 11, f[2977] = 12, f[2978] = 13, f[2979] = 14, f[2980] = 15, f[2981] = 16, f[2982] = 3, f[2983] = 3, f[3e3] = 3, f[3001] = 4, f[3002] = 5, f[3003] = 6, f[3004] = 7, f[3005] = 8, f[3006] = 9, f[3007] = 10, f[3008] = 11, f[3009] = 12, f[3010] = 13, f[3011] = 14, f[3012] = 15, f[3013] = 16, f[3014] = 3, f[3015] = 3, f[2952] = 6, f[2953] = 7, f[2954] = 8, f[2955] = 9, f[2956] = 10, f[2957] = 11, f[2958] = 12, f[2959] = 13, f[2960] = 14, f[2961] = 15, f[2962] = 16, f[2963] = 17, f[2964] = 18, f[2965] = 19, f[2966] = 6, f[2967] = 6, f[2984] = 6, f[2985] = 7, f[2986] = 8, f[2987] = 9, f[2988] = 10, f[2989] = 11, f[2990] = 12, f[2991] = 13, f[2992] = 14, f[2993] = 15, f[2994] = 16, f[2995] = 17, f[2996] = 18, f[2997] = 19, f[2998] = 6, f[2999] = 6, f[2946] = 3, f[3016] = 20, f[2948] = 4, f[2947] = 5, f[2949] = 6, f[2950] = 7, f[2951] = 8, f[3017] = 3, f[2945] = 4), f[32] = e, bt(12304))
                    }

                    function _r(e, r, t) {
                        r |= 0, t |= 0;
                        var n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            c = 0;
                        if (f[12 + (e |= 0) >> 2] = t, f[(n = e) >> 2] = 0, f[n + 4 >> 2] = 0, f[e + 20 >> 2] = 0, f[e + 24 >> 2] = 0, n = t >>> 0 < 8 ? t : 8)
                            for (t = 0, a = 0, u = 0;;) {
                                if (c = (l = 0 | mt(0 | s[r + t >> 0], 0, t << 3 | 0)) | a, l = M | u, (t = t + 1 | 0) >>> 0 >= n >>> 0) {
                                    i = c, o = l;
                                    break
                                }
                                a = c, u = l
                            } else i = 0, o = 0;
                        f[(u = e) >> 2] = i, f[u + 4 >> 2] = o, f[e + 16 >> 2] = n, f[e + 8 >> 2] = r
                    }

                    function hr(e, r, t, n, o) {
                        e |= 0, r |= 0, o |= 0;
                        var u, l, c, _, h, m, p, v, b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0,
                            Pe = 0,
                            Re = 0;
                        u = S, S = S + 1152 | 0, l = u + 632 | 0, c = u + 552 | 0, _ = u + 548 | 0, h = u, m = 24 + (n |= 0) | 0, p = n + 120 | 0, v = 0 != (0 | (t |= 0));
                        e: do {
                            if (v)
                                for (b = n + 176 | 0, w = n + 260 | 0, k = e;;) {
                                    r: for (;;) {
                                        t: for (;;) {
                                            if (!(0 | ur(m, 1))) {
                                                E = k, y = 18;
                                                break e
                                            }
                                            if (L = 0 | f[b >> 2], g = 0 | ur(m, 2), (T = 0 | f[w >> 2]) & (D = 1 << g) | 0) {
                                                C = 3, y = 105;
                                                break e
                                            }
                                            switch (f[w >> 2] = T | D, f[n + 180 + (20 * L | 0) >> 2] = g, f[(P = n + 180 + (20 * L | 0) + 8 | 0) >> 2] = k, f[(R = n + 180 + (20 * L | 0) + 12 | 0) >> 2] = r, f[(O = n + 180 + (20 * L | 0) + 16 | 0) >> 2] = 0, f[b >> 2] = 1 + (0 | f[b >> 2]), 0 | g) {
                                                case 3:
                                                    break r;
                                                case 1:
                                                case 0:
                                                    break t
                                            }
                                        }
                                        if (g = 2 + (0 | ur(m, 3)) | 0, f[n + 180 + (20 * L | 0) + 4 >> 2] = g, D = 1 << g, !(0 | hr(((0 | f[P >> 2]) - 1 + D | 0) >>> g, (D + -1 + (0 | f[R >> 2]) | 0) >>> g, 0, n, O))) {
                                            C = 3, y = 105;
                                            break e
                                        }
                                    }
                                    if (B = (0 | (g = 1 + (0 | ur(m, 8)) | 0)) > 16 ? 0 : (0 | g) > 4 ? 1 : (0 | g) > 2 ? 2 : 3, D = ((0 | f[P >> 2]) - 1 + (1 << B) | 0) >>> B, f[(T = n + 180 + (20 * L | 0) + 4 | 0) >> 2] = B, !(0 | hr(g, 1, 0, n, O))) {
                                        C = 3, y = 105;
                                        break e
                                    }
                                    if (!(T = 0 | Je(x = 1 << (8 >>> (0 | f[T >> 2])), ((0 | x) < 0) << 31 >> 31, 4))) {
                                        C = 3, y = 105;
                                        break e
                                    }
                                    if (N = 0 | f[O >> 2], f[T >> 2] = f[N >> 2], (0 | (I = g << 2)) > 4) {
                                        g = 4;
                                        do {
                                            i[T + g >> 0] = (0 | s[T + (g + -4) >> 0]) + (0 | s[N + g >> 0]), g = g + 1 | 0
                                        } while ((0 | g) != (0 | I));
                                        F = I
                                    } else F = 4;
                                    (0 | (I = x << 2)) > (0 | F) && at(T + F | 0, 0, I - F | 0),
                                        Ve(0 | f[O >> 2]),
                                        f[O >> 2] = T,
                                        k = D
                                } else E = e, y = 18
                        } while (0);
                        do {
                            if (18 == (0 | y)) {
                                if (0 | ur(m, 1)) {
                                    if (!(((e = 0 | ur(m, 4)) - 1 | 0) >>> 0 < 11)) {
                                        f[n >> 2] = 3, U = 0;
                                        break
                                    }
                                    H = e
                                } else H = 0;
                                f[_ >> 2] = 0, O = 65535 & (e = 0 | a[920 + (H << 1) >> 1]);
                                do {
                                    if (0 != (0 | t) && 0 != (0 | ur(m, 1)))
                                        if (F = 2 + (0 | ur(m, 3)) | 0, B = 0 | A(L = (E + -1 + (B = 1 << F) | 0) >>> F, P = (r + -1 + B | 0) >>> F), 0 | hr(L, P, 0, n, _)) {
                                            if (f[n + 152 >> 2] = F, F = (0 | B) > 0)
                                                for (P = 0 | f[_ >> 2], L = 0, R = 1;;) {
                                                    if (b = (0 | f[(k = P + (L << 2) | 0) >> 2]) >>> 8 & 65535, f[k >> 2] = b, k = (0 | b) < (0 | R) ? R : b + 1 | 0, (0 | (L = L + 1 | 0)) == (0 | B)) {
                                                        G = k;
                                                        break
                                                    }
                                                    R = k
                                                } else G = 1;
                                            if ((0 | G) > 1e3 | (0 | G) > (0 | A(E, r))) {
                                                if (!(R = 0 | Je(G, ((0 | G) < 0) << 31 >> 31, 4))) {
                                                    f[n >> 2] = 1, W = 0, Y = 0, V = 1, q = 0, j = 0, z = 0;
                                                    break
                                                }
                                                if (at(0 | R, -1, G << 2 | 0), F)
                                                    for (L = 0 | f[_ >> 2], P = 0, D = 0;;) {
                                                        if (x = R + (f[(T = L + (D << 2) | 0) >> 2] << 2) | 0, -1 == (0 | (k = 0 | f[x >> 2])) ? (f[x >> 2] = P, X = P + 1 | 0, K = P) : (X = P, K = k), f[T >> 2] = K, (0 | (D = D + 1 | 0)) == (0 | B)) {
                                                            $ = X;
                                                            break
                                                        }
                                                        P = X
                                                    } else $ = 0;
                                                (P = 0 | Je(65535 & e, 0, 4)) ? (J = P, Q = R, Z = G, ee = $, re = R, y = 37) : (f[n >> 2] = 1, W = 0, Y = 0, V = 1, q = P, j = 0, z = R)
                                            } else J = 0, Q = 0, Z = G, ee = G, re = 0, y = 37
                                        } else W = 0, Y = 0, V = 1, q = 0, j = 0, z = 0;
                                    else J = 0, Q = 0, Z = 1, ee = 1, re = 0, y = 37
                                } while (0);
                                e: do {
                                    if (37 == (0 | y))
                                        if (0 | f[(e = n + 48 | 0) >> 2]) W = 0, Y = 0, V = 1, q = J, j = 0, z = re;
                                        else {
                                            if (B = 1 << H, (P = (0 | H) > 0) ? (te = (L = (0 | B) > -280 & (0 | (D = B + 280 | 0)) > 256) ? D : 256, ne = 0) : (te = 280, ne = 0), L = 0 | $e(te, ne, 4), 0 == (0 | (F = 0 | Je(D = 0 | A(ee, O), ((0 | D) < 0) << 31 >> 31, 4))) | 0 == (0 | L) | 0 == (0 | (D = 0 | mr(ee)))) {
                                                f[n >> 2] = 1, W = D, Y = F, V = 1, q = J, j = L, z = re;
                                                break
                                            }
                                            r: do {
                                                if ((0 | Z) > 0) {
                                                    T = 0 == (0 | Q), k = n + 44 | 0, x = F, b = 0;
                                                    t: for (;;) {
                                                        for (T ? (ae = b, y = 46) : -1 == (0 | (w = 0 | f[Q + (b << 2) >> 2])) ? (ie = 1, oe = h) : (ae = w, y = 46), 46 == (0 | y) && (y = 0, ie = 0, oe = D + (548 * ae | 0) | 0), w = 0, I = 1, g = 0, N = ie ? J : x, ue = 0;;) {
                                                            if (fe = 0 | d[944 + (ue << 1) >> 1], f[oe + (ue << 2) >> 2] = N, le = fe + (P & 0 == (0 | ue) ? B : 0) | 0, fe = 0 | ur(m, 1), at(0 | L, 0, le << 2 | 0), fe) fe = 0 | ur(m, 1), se = 0 == (0 | ur(m, 1)), ce = L + ((0 | ur(m, se ? 1 : 8)) << 2) | 0, f[ce >> 2] = 1, 1 == (0 | fe) && (fe = L + ((0 | ur(m, 8)) << 2) | 0, f[fe >> 2] = 1);
                                                            else {
                                                                _e = (de = c) + 76 | 0;
                                                                do {
                                                                    f[de >> 2] = 0, de = de + 4 | 0
                                                                } while ((0 | de) < (0 | _e));
                                                                if ((0 | (fe = 4 + (0 | ur(m, 4)) | 0)) > 19) {
                                                                    y = 68;
                                                                    break t
                                                                }
                                                                if ((0 | fe) > 0) {
                                                                    ce = 0;
                                                                    do {
                                                                        se = 0 | ur(m, 3), f[c + (s[5295 + ce >> 0] << 2) >> 2] = se, ce = ce + 1 | 0
                                                                    } while ((0 | ce) != (0 | fe))
                                                                }
                                                                if (!(0 | pr(l, 7, c, 19))) {
                                                                    y = 70;
                                                                    break t
                                                                }
                                                                if (0 | ur(m, 1)) {
                                                                    if ((0 | (fe = 2 + (0 | ur(m, 2 + ((0 | ur(m, 3)) << 1) | 0)) | 0)) > (0 | le)) {
                                                                        y = 70;
                                                                        break t
                                                                    }
                                                                    he = fe
                                                                } else he = le;
                                                                n: do {
                                                                    if ((0 | le) > 0)
                                                                        for (fe = 0, ce = 8, se = he;;) {
                                                                            for (me = fe, pe = se;;) {
                                                                                if (ve = pe, pe = pe + -1 | 0, !ve) break n;
                                                                                if ((0 | (ve = 0 | f[k >> 2])) > 31 ? (or(m), be = 0 | f[k >> 2]) : be = ve, ve = 127 & (we = 0 | ft(0 | f[(ve = m) >> 2], 0 | f[ve + 4 >> 2], 63 & be | 0)), f[k >> 2] = (0 | s[l + (ve << 2) >> 0]) + be, ke = 65535 & (Se = 0 | a[l + (ve << 2) + 2 >> 1]), (65535 & Se) < 16) break;
                                                                                if (we = 0 | s[5317 + (ve = ke + -16 | 0) >> 0], ((Ee = (0 | ur(m, 0 | s[5314 + ve >> 0])) + we | 0) + me | 0) > (0 | le)) {
                                                                                    y = 70;
                                                                                    break t
                                                                                }
                                                                                if (we = Se << 16 >> 16 == 16 ? ce : 0, (0 | Ee) > 0)
                                                                                    for (ve = Ee, Ee = me;;) {
                                                                                        if (Me = Ee + 1 | 0, f[L + (Ee << 2) >> 2] = we, !((0 | ve) > 1)) {
                                                                                            Ae = Me;
                                                                                            break
                                                                                        }
                                                                                        ve = ve + -1 | 0, Ee = Me
                                                                                    } else Ae = me;
                                                                                if ((0 | Ae) >= (0 | le)) break n;
                                                                                me = Ae
                                                                            }
                                                                            if (fe = me + 1 | 0, f[L + (me << 2) >> 2] = ke, (0 | fe) >= (0 | le)) break;
                                                                            ce = Se << 16 >> 16 == 0 ? ce : ke, se = pe
                                                                        }
                                                                } while (0)
                                                            }
                                                            if (0 | f[e >> 2]) {
                                                                y = 73;
                                                                break t
                                                            }
                                                            if (!(se = 0 | pr(N, 8, L, le))) {
                                                                y = 73;
                                                                break t
                                                            }
                                                            n: do {
                                                                if (I) {
                                                                    switch (0 | ue) {
                                                                        case 0:
                                                                        case 4:
                                                                            ye = I;
                                                                            break n
                                                                    }
                                                                    ye = 0 == (0 | i[N >> 0]) & 1
                                                                } else ye = 0
                                                            } while (0);
                                                            if (g = (0 | s[N >> 0]) + g | 0, N = N + (se << 2) | 0, (0 | ue) < 4) {
                                                                if (ce = 0 | f[L >> 2], (0 | le) > 1)
                                                                    for (fe = 1, Ee = ce;;) {
                                                                        if (we = (0 | (ve = 0 | f[L + (fe << 2) >> 2])) > (0 | Ee) ? ve : Ee, (0 | (fe = fe + 1 | 0)) == (0 | le)) {
                                                                            Le = we;
                                                                            break
                                                                        }
                                                                        Ee = we
                                                                    } else Le = ce;
                                                                ge = Le + w | 0
                                                            } else ge = w;
                                                            if ((0 | (ue = ue + 1 | 0)) >= 5) break;
                                                            w = ge, I = ye
                                                        }
                                                        if (x = ie ? x : N, f[oe + 20 >> 2] = ye, f[(I = oe + 28 | 0) >> 2] = 0, 0 != (0 | ye) && (w = d[2 + (0 | f[oe + 8 >> 2]) >> 1] | d[2 + (0 | f[oe + 4 >> 2]) >> 1] << 16 | d[2 + (0 | f[oe + 12 >> 2]) >> 1] << 24, f[(ue = oe + 24 | 0) >> 2] = w, 0 == (0 | g)) && (65535 & (Ee = 0 | a[2 + (0 | f[oe >> 2]) >> 1])) < 256 ? (f[I >> 2] = 1, f[ue >> 2] = w | (65535 & Ee) << 8, f[oe + 32 >> 2] = 0) : y = 87, 87 == (0 | y) && (y = 0, Ee = (0 | ge) < 6, f[oe + 32 >> 2] = 1 & Ee, Ee)) {
                                                            Ee = oe + 4 | 0, w = oe + 8 | 0, ue = oe + 12 | 0, I = 0;
                                                            do {
                                                                le = oe + 36 + (I << 3) | 0, fe = (0 | f[oe >> 2]) + (I << 2) | 0, (65535 & (fe = (se = d[fe >> 1] | d[fe + 2 >> 1] << 16) >>> 16)) > 255 ? (f[le >> 2] = 255 & se | 256, f[oe + 36 + (I << 3) + 4 >> 2] = fe) : (we = oe + 36 + (I << 3) + 4 | 0, ve = 255 & se, f[le >> 2] = ve, se = fe << 8, f[we >> 2] = se, fe = I >>> ve, Me = 0 | f[Ee >> 2], Te = 0 | a[Me + (fe << 2) + 2 >> 1], Me = (De = 0 | s[Me + (fe << 2) >> 0]) + ve | 0, f[le >> 2] = Me, ve = (65535 & Te) << 16 | se, f[we >> 2] = ve, se = fe >>> De, De = 0 | f[w >> 2], fe = 0 | a[De + (se << 2) + 2 >> 1], De = (Te = 0 | s[De + (se << 2) >> 0]) + Me | 0, f[le >> 2] = De, Me = 65535 & fe | ve, f[we >> 2] = Me, ve = se >>> Te, Te = 0 | f[ue >> 2], se = 0 | a[Te + (ve << 2) + 2 >> 1], f[le >> 2] = (0 | s[Te + (ve << 2) >> 0]) + De, f[we >> 2] = (65535 & se) << 24 | Me), I = I + 1 | 0
                                                            } while (64 != (0 | I))
                                                        }
                                                        if ((0 | (b = b + 1 | 0)) >= (0 | Z)) break r
                                                    }
                                                    if (68 == (0 | y)) {
                                                        f[n >> 2] = 3, W = D, Y = F, V = 1, q = J, j = L, z = re;
                                                        break e
                                                    }
                                                    70 == (0 | y) ? (f[n >> 2] = 3, Ce = n) : 73 == (0 | y) && (Ce = n), f[Ce >> 2] = 3, W = D, Y = F, V = 1, q = J, j = L, z = re;
                                                    break e
                                                }
                                            } while (0);
                                            f[n + 160 >> 2] = f[_ >> 2], f[n + 164 >> 2] = ee, f[n + 168 >> 2] = D, f[n + 172 >> 2] = F, W = D, Y = F, V = 0, q = J, j = L, z = re
                                        }
                                } while (0);
                                if (Ve(j), Ve(q), Ve(z), V) {
                                    Ve(0 | f[_ >> 2]), Ve(Y), ze(W), C = 3, y = 105;
                                    break
                                }
                                if ((0 | H) > 0) {
                                    if (f[p >> 2] = 1 << H, !(0 | vr(n + 124 | 0, H))) {
                                        C = 1, y = 105;
                                        break
                                    }
                                } else f[p >> 2] = 0;
                                if (O = 0 | f[n + 152 >> 2], f[n + 100 >> 2] = E, f[n + 104 >> 2] = r, e = 1 << O, f[n + 156 >> 2] = (E + -1 + e | 0) >>> O, f[n + 148 >> 2] = 0 == (0 | O) ? -1 : e + -1 | 0, v) f[n + 4 >> 2] = 1, Pe = 1, Re = 0;
                                else {
                                    if (!(O = 0 | Je(e = 0 | _t(0 | E, ((0 | E) < 0) << 31 >> 31 | 0, 0 | r, ((0 | r) < 0) << 31 >> 31 | 0), M, 4))) {
                                        C = 1, y = 105;
                                        break
                                    }
                                    if (!(0 | fr(n, O, E, r, r, 0))) {
                                        U = O;
                                        break
                                    }
                                    if (!(e = 0 == (0 | f[n + 48 >> 2]))) {
                                        U = O;
                                        break
                                    }
                                    Pe = 1 & e, Re = O
                                }
                                if (0 | o && (f[o >> 2] = Re), f[n + 112 >> 2] = 0, v) return S = u, 0 | Pe;
                                Ve(0 | f[n + 160 >> 2]), Ve(0 | f[n + 172 >> 2]), ze(0 | f[n + 168 >> 2]), Xe(n + 124 | 0), Xe(n + 136 | 0), _e = (de = p) + 56 | 0;
                                do {
                                    f[de >> 2] = 0, de = de + 4 | 0
                                } while ((0 | de) < (0 | _e));
                                return S = u, 0 | Pe
                            }
                        } while (0);
                        105 == (0 | y) && (f[n >> 2] = C, U = 0), Ve(U), Ve(0 | f[n + 160 >> 2]), Ve(0 | f[n + 172 >> 2]), ze(0 | f[n + 168 >> 2]), Xe(n + 124 | 0), Xe(n + 136 | 0), _e = (de = p) + 56 | 0;
                        do {
                            f[de >> 2] = 0, de = de + 4 | 0
                        } while ((0 | de) < (0 | _e));
                        return S = u, 0
                    }

                    function mr(e) {
                        return 0 | Je(e |= 0, ((0 | e) < 0) << 31 >> 31, 548)
                    }

                    function pr(e, r, t, n) {
                        e |= 0, r |= 0, t |= 0;
                        var i, o = 0,
                            a = 0,
                            u = 0;
                        return i = S, S = S + 1024 | 0, (0 | (n |= 0)) >= 513 ? (o = 0 | Je(n, ((0 | n) < 0) << 31 >> 31, 2)) ? (u = 0 | br(e, r, t, n, o), Ve(o), a = u) : a = 0 : a = 0 | br(e, r, t, n, i), S = i, 0 | a
                    }

                    function vr(e, r) {
                        var t, n;
                        return e |= 0, n = 0 | $e(t = 1 << (r |= 0), ((0 | t) < 0) << 31 >> 31, 4), f[e >> 2] = n, n ? (f[e + 4 >> 2] = 32 - r, f[e + 8 >> 2] = r, 1) : 0
                    }

                    function br(e, r, t, n, o) {
                        e |= 0, t |= 0, n |= 0, o |= 0;
                        var u, l, s, c = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0,
                            A = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0;
                        u = S, S = S + 128 | 0, c = u, s = 1 << (r |= 0), h = 64 + (_ = l = u + 64 | 0) | 0;
                        do {
                            f[_ >> 2] = 0, _ = _ + 4 | 0
                        } while ((0 | _) < (0 | h));
                        _ = (0 | n) > 0;
                        do {
                            if (_) {
                                for (h = 0;;) {
                                    if ((0 | (m = 0 | f[t + (h << 2) >> 2])) > 15) {
                                        p = 0, v = 51;
                                        break
                                    }
                                    if (f[(b = l + (m << 2) | 0) >> 2] = 1 + (0 | f[b >> 2]), (0 | (h = h + 1 | 0)) >= (0 | n)) {
                                        v = 4;
                                        break
                                    }
                                }
                                if (4 == (0 | v)) {
                                    w = 0 | f[l >> 2];
                                    break
                                }
                                if (51 == (0 | v)) return S = u, 0 | p
                            } else w = 0
                        } while (0);
                        if ((0 | w) == (0 | n)) return S = u, 0;
                        if (f[c + 4 >> 2] = 0, (0 | (w = 0 | f[l + 4 >> 2])) > 2) return S = u, 0;
                        if (f[c + 8 >> 2] = w, (0 | (h = 0 | f[l + 8 >> 2])) > 4) return S = u, 0;
                        if (b = h + w | 0, f[c + 12 >> 2] = b, (0 | (h = 0 | f[l + 12 >> 2])) > 8) return S = u, 0;
                        if (m = h + b | 0, f[c + 16 >> 2] = m, (0 | (b = 0 | f[l + 16 >> 2])) > 16) return S = u, 0;
                        if (h = b + m | 0, f[c + 20 >> 2] = h, (0 | (m = 0 | f[l + 20 >> 2])) > 32) return S = u, 0;
                        if (b = m + h | 0, f[c + 24 >> 2] = b, (0 | (h = 0 | f[l + 24 >> 2])) > 64) return S = u, 0;
                        if (m = h + b | 0, f[c + 28 >> 2] = m, (0 | (b = 0 | f[l + 28 >> 2])) > 128) return S = u, 0;
                        if (h = b + m | 0, f[c + 32 >> 2] = h, (0 | (m = 0 | f[l + 32 >> 2])) > 256) return S = u, 0;
                        if (b = m + h | 0, f[c + 36 >> 2] = b, (0 | (h = 0 | f[l + 36 >> 2])) > 512) return S = u, 0;
                        if (m = h + b | 0, f[c + 40 >> 2] = m, (0 | (b = 0 | f[l + 40 >> 2])) > 1024) return S = u, 0;
                        if (h = b + m | 0, f[c + 44 >> 2] = h, (0 | (m = 0 | f[l + 44 >> 2])) > 2048) return S = u, 0;
                        if (b = m + h | 0, f[c + 48 >> 2] = b, (0 | (h = 0 | f[l + 48 >> 2])) > 4096) return S = u, 0;
                        if (m = h + b | 0, f[c + 52 >> 2] = m, (0 | (b = 0 | f[l + 52 >> 2])) > 8192) return S = u, 0;
                        if (h = b + m | 0, f[c + 56 >> 2] = h, (0 | (m = 0 | f[l + 56 >> 2])) > 16384) return S = u, 0;
                        if (b = m + h | 0, f[(h = c + 60 | 0) >> 2] = b, _) {
                            _ = 0;
                            do {
                                (0 | (m = 0 | f[t + (_ << 2) >> 2])) > 0 && (m = 0 | f[(k = c + (m << 2) | 0) >> 2], f[k >> 2] = m + 1, a[o + (m << 1) >> 1] = _), _ = _ + 1 | 0
                            } while ((0 | _) != (0 | n));
                            E = 0 | f[h >> 2]
                        } else E = b;
                        if (1 == (0 | E)) {
                            b = (0 | d[o >> 1]) << 16, n = s;
                            do {
                                _ = n, a[(c = e + ((n = n + -1 | 0) << 2) | 0) >> 1] = b, a[c + 2 >> 1] = b >>> 16
                            } while ((0 | _) > 1);
                            return S = u, 0 | s
                        }
                        b = s + -1 | 0;
                        e: do {
                            if (!((0 | r) < 1)) {
                                if ((0 | (n = 2 - w | 0)) < 0) return S = u, 0;
                                for (_ = 0, c = 2, t = 1, m = 0, k = w, g = l + 4 | 0, T = n, n = 3;;) {
                                    if ((0 | k) > 0) {
                                        D = 255 & t, C = 1 << t + -1, P = _, R = m, O = k;
                                        do {
                                            B = e + (P << 2) | 0, x = (0 | d[o + (R << 1) >> 1]) << 16 | D, N = s;
                                            do {
                                                a[(I = B + ((N = N - c | 0) << 2) | 0) >> 1] = x, a[I + 2 >> 1] = x >>> 16
                                            } while ((0 | N) > 0);
                                            for (F = C; F & P;) F >>>= 1;
                                            R = R + 1 | 0, P = 0 == (0 | F) ? P : (F + -1 & P) + F | 0, N = O, O = O + -1 | 0
                                        } while ((0 | N) > 1);
                                        f[g >> 2] = O, H = P, U = R
                                    } else H = _, U = m;
                                    if (C = t + 1 | 0, (0 | t) >= (0 | r)) {
                                        M = T, A = n, y = H, L = U;
                                        break e
                                    }
                                    if ((0 | (T = (D = T << 1) - (k = 0 | f[l + (C << 2) >> 2]) | 0)) < 0) {
                                        p = 0;
                                        break
                                    }
                                    _ = H, c <<= 1, t = C, m = U, g = l + (C << 2) | 0, n = D + n | 0
                                }
                                return S = u, 0 | p
                            }
                            M = 1, A = 1, y = 0, L = 0
                        } while (0);
                        U = r + 1 | 0;
                        do {
                            if ((0 | U) < 16) {
                                for (H = e, F = -1, w = e, n = s, g = s, m = M, t = A, c = 2, _ = y, T = r, k = U, D = L;;) {
                                    if (t = (C = m << 1) + t | 0, (0 | (m = C - (x = 0 | f[(N = l + (k << 2) | 0) >> 2]) | 0)) < 0) {
                                        p = 0, v = 51;
                                        break
                                    }
                                    e: do {
                                        if ((0 | x) > 0) {
                                            if (B = 255 & (C = k - r | 0), I = 1 << T, G = 1 << C, (0 | k) < 15) W = F, Y = w, V = n, q = g, j = _, z = D, X = x;
                                            else
                                                for (C = 255 & k, K = F, $ = w, J = n, Q = g, Z = _, ee = D;;) {
                                                    (0 | (re = Z & b)) == (0 | K) ? (te = Q, ne = K, ie = $, oe = J) : (ae = $ + (Q << 2) | 0, i[e + (re << 2) >> 0] = C, a[e + (re << 2) + 2 >> 1] = ((ae - H | 0) >>> 2) - re, te = G, ne = re, ie = ae, oe = G + J | 0), ae = ie + (Z >>> r << 2) | 0, re = (0 | d[o + (ee << 1) >> 1]) << 16 | B, ue = te;
                                                    do {
                                                        a[(fe = ae + ((ue = ue - c | 0) << 2) | 0) >> 1] = re, a[fe + 2 >> 1] = re >>> 16
                                                    } while ((0 | ue) > 0);
                                                    for (le = I; le & Z;) le >>>= 1;
                                                    if (ue = ee + 1 | 0, re = 0 == (0 | le) ? Z : (le + -1 & Z) + le | 0, ae = 0 | f[N >> 2], f[N >> 2] = ae + -1, !((0 | ae) > 1)) {
                                                        se = te, ce = ne, de = ie, _e = oe, he = re, me = ue;
                                                        break e
                                                    }
                                                    K = ne, $ = ie, J = oe, Q = te, Z = re, ee = ue
                                                }
                                            for (;;) {
                                                if ((0 | (ee = j & b)) == (0 | W)) pe = q, ve = W, be = Y, we = V;
                                                else {
                                                    Z = Y + (q << 2) | 0, Q = G - X | 0;
                                                    r: do {
                                                        if ((0 | Q) < 1) Se = k;
                                                        else
                                                            for (J = k, $ = Q;;) {
                                                                if ((0 | (K = J + 1 | 0)) >= 15) {
                                                                    Se = K;
                                                                    break r
                                                                }
                                                                if ((0 | ($ = ($ << 1) - (0 | f[l + (K << 2) >> 2]) | 0)) < 1) {
                                                                    Se = K;
                                                                    break
                                                                }
                                                                J = K
                                                            }
                                                    } while (0);
                                                    Q = 1 << Se - r, i[e + (ee << 2) >> 0] = Se, a[e + (ee << 2) + 2 >> 1] = ((Z - H | 0) >>> 2) - ee, pe = Q, ve = ee, be = Z, we = Q + V | 0
                                                }
                                                Q = be + (j >>> r << 2) | 0, J = (0 | d[o + (z << 1) >> 1]) << 16 | B, $ = pe;
                                                do {
                                                    a[(K = Q + (($ = $ - c | 0) << 2) | 0) >> 1] = J, a[K + 2 >> 1] = J >>> 16
                                                } while ((0 | $) > 0);
                                                for (ke = I; ke & j;) ke >>>= 1;
                                                if ($ = z + 1 | 0, J = 0 == (0 | ke) ? j : (ke + -1 & j) + ke | 0, X = (Q = 0 | f[N >> 2]) - 1 | 0, f[N >> 2] = X, (0 | Q) <= 1) {
                                                    se = pe, ce = ve, de = be, _e = we, he = J, me = $;
                                                    break
                                                }
                                                W = ve, Y = be, V = we, q = pe, j = J, z = $
                                            }
                                        } else se = g, ce = F, de = w, _e = n, he = _, me = D
                                    } while (0);
                                    if ((0 | (N = k + 1 | 0)) >= 16) {
                                        v = 49;
                                        break
                                    }
                                    x = k, F = ce, w = de, n = _e, g = se, c <<= 1, _ = he, k = N, D = me, T = x
                                }
                                if (49 == (0 | v)) {
                                    Ee = _e, Me = t, Ae = 0 | f[h >> 2];
                                    break
                                }
                                if (51 == (0 | v)) return S = u, 0 | p
                            } else Ee = s, Me = A, Ae = E
                        } while (0);
                        return S = u, 0 | ((0 | Me) == ((Ae << 1) - 1 | 0) ? Ee : 0)
                    }

                    function wr(e, r, t) {
                        e |= 0, t |= 0;
                        var n, i, o, a, u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            M = 0;
                        if (n = S, S = S + 16 | 0, i = n + 4 | 0, u = n, o = 0 | f[(r |= 0) >> 2], a = 0 | f[r + 4 >> 2], l = 0 != (0 | e))
                            if (s = (0 | f[e + 8 >> 2]) > 0, f[r + 72 >> 2] = 1 & s, s) {
                                if (s = 0 | f[e + 20 >> 2], c = 0 | f[e + 24 >> 2], d = 0 | f[e + 12 >> 2], _ = 0 | f[e + 16 >> 2], t = (h = t >>> 0 < 11) ? d : -2 & d, ((d = h ? _ : -2 & _) + c | 0) > (0 | a) | (t + s | 0) > (0 | o) | (0 | c) < 1 | (0 | s) < 1 | (d | t | 0) < 0) return S = n, 0;
                                m = c, p = s, v = d, b = t
                            } else m = a, p = o, v = 0, b = 0;
                        else f[r + 72 >> 2] = 0, m = a, p = o, v = 0, b = 0;
                        if (f[r + 76 >> 2] = b, f[r + 84 >> 2] = v, f[r + 80 >> 2] = p + b, f[r + 88 >> 2] = m + v, f[r + 12 >> 2] = p, f[r + 16 >> 2] = m, l) {
                            l = (0 | f[e + 28 >> 2]) > 0, f[(v = r + 92 | 0) >> 2] = 1 & l;
                            do {
                                if (l) {
                                    if (f[i >> 2] = f[e + 32 >> 2], f[u >> 2] = f[e + 36 >> 2], 0 | Dr(p, m, i, u)) {
                                        f[r + 96 >> 2] = f[i >> 2], f[r + 100 >> 2] = f[u >> 2];
                                        break
                                    }
                                    return S = n, 0
                                }
                            } while (0);
                            f[(u = r + 68 | 0) >> 2] = 0 != (0 | f[e >> 2]) & 1, w = 0 == (0 | f[e + 4 >> 2]), k = v, E = u
                        } else f[(u = r + 92 | 0) >> 2] = 0, f[(v = r + 68 | 0) >> 2] = 0, w = 1, k = u, E = v;
                        return f[(v = r + 56 | 0) >> 2] = 1 & w, 0 | f[k >> 2] ? (M = (0 | f[r + 96 >> 2]) < (0 | (3 * o | 0) / 4) ? (0 | f[r + 100 >> 2]) < (0 | (3 * a | 0) / 4) : 0, f[E >> 2] = 1 & M, f[v >> 2] = 0, S = n, 1) : (S = n, 1)
                    }

                    function Sr() {
                        var e;
                        0 | ut(12416) || (e = 0 | f[2893], (0 | f[36]) != (0 | e) && (f[3023] = 1, f[3025] = 2, f[3029] = 1, f[3030] = 2, f[3022] = 3, f[3024] = 4, f[3026] = 5, f[3027] = 6, f[3028] = 7, f[3031] = 5, f[3032] = 6), f[36] = e, bt(12416))
                    }

                    function kr(e, r, t, n, i, o, a, u, l) {
                        t |= 0, n |= 0, o |= 0, a |= 0, u |= 0, l |= 0;
                        var s = 0,
                            c = 0,
                            d = 0;
                        s = (0 | (r |= 0)) < (0 | (i |= 0)), f[(e |= 0) >> 2] = 1 & s, c = (0 | t) < (0 | o), f[e + 4 >> 2] = 1 & c, f[e + 44 >> 2] = r, f[e + 48 >> 2] = t, f[e + 52 >> 2] = i, f[e + 56 >> 2] = o, f[e + 60 >> 2] = 0, f[e + 64 >> 2] = 0, f[e + 68 >> 2] = n, f[e + 72 >> 2] = a, f[e + 8 >> 2] = u, a = s ? i + -1 | 0 : r, f[e + 36 >> 2] = a, n = s ? r + -1 | 0 : i, f[e + 40 >> 2] = n, s || (s = 0 | dt(0, 1, 0 | n, ((0 | n) < 0) << 31 >> 31 | 0), f[e + 12 >> 2] = s), n = (s = c << 31 >> 31) + t | 0, f[e + 28 >> 2] = n, t = s + o | 0, f[e + 32 >> 2] = t, f[e + 24 >> 2] = c ? t : n, c ? d = a : (a = 0 | dt(0, 0 | o, 0 | (c = 0 | A(n, a)), ((0 | c) < 0) << 31 >> 31 | 0), f[e + 20 >> 2] = (0 | a) == (0 | a) & 0 == (0 | M) ? a : 0, d = t), t = 0 | dt(0, 1, 0 | d, ((0 | d) < 0) << 31 >> 31 | 0), f[e + 16 >> 2] = t, f[e + 76 >> 2] = l, t = l + ((0 | A(u, i)) << 2) | 0, f[e + 80 >> 2] = t, at(0 | l, 0, 0 | A(i << 3, u)),
                            function() {
                                var e;
                                0 | ut(12388) || (e = 0 | f[2893], (0 | f[35]) != (0 | e) && (f[3020] = 6, f[3021] = 7, f[3019] = 2, f[3018] = 3), f[35] = e, bt(12388))
                            }()
                    }

                    function Er() {
                        var e;
                        0 | ut(12332) || (e = 0 | f[2893], (0 | f[33]) != (0 | e) && (f[3048] = 12, f[3049] = 13, f[3050] = 14, f[3051] = 15, f[3052] = 16, f[3053] = 17, f[3054] = 18, f[3055] = 13, f[3056] = 15, f[3057] = 16, f[3058] = 17), f[33] = e, bt(12332))
                    }

                    function Mr(e, r, t, n) {
                        n |= 0;
                        var i, o, a, u, l, s, c, d, _, h, m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0;
                        if (i = 24 + (e |= 0) | 0, (0 | (r |= 0)) <= 0) return 0;
                        for (o = e + 56 | 0, a = e + 64 | 0, u = e + 4 | 0, l = e + 60 | 0, s = e + 32 | 0, c = e + 8 | 0, d = e + 52 | 0, _ = e + 80 | 0, h = e + 76 | 0, p = t |= 0, t = 0;;) {
                            if ((0 | f[a >> 2]) < (0 | f[o >> 2]) && (0 | f[i >> 2]) < 1) {
                                m = t, v = 12;
                                break
                            }
                            if (0 | f[u >> 2] && (b = 0 | f[h >> 2], f[h >> 2] = f[_ >> 2], f[_ >> 2] = b), gr(e, p), 0 == (0 | f[u >> 2]) && (0 | A(0 | f[d >> 2], 0 | f[c >> 2])) > 0) {
                                b = 0 | f[_ >> 2], w = 0 | f[h >> 2], S = 0;
                                do {
                                    f[(k = w + (S << 2) | 0) >> 2] = (0 | f[k >> 2]) + (0 | f[b + (S << 2) >> 2]), S = S + 1 | 0
                                } while ((0 | S) < (0 | A(0 | f[d >> 2], 0 | f[c >> 2])))
                            }
                            if (f[l >> 2] = 1 + (0 | f[l >> 2]), S = t + 1 | 0, f[i >> 2] = (0 | f[i >> 2]) - (0 | f[s >> 2]), !((0 | S) < (0 | r))) {
                                m = S, v = 12;
                                break
                            }
                            p = p + n | 0, t = S
                        }
                        return 12 == (0 | v) ? 0 | m : 0
                    }

                    function Ar(e) {
                        var r, t, n, i = 0,
                            o = 0,
                            a = 0,
                            u = 0;
                        if (r = 24 + (e |= 0) | 0, t = e + 56 | 0, !((0 | f[(n = e + 64 | 0) >> 2]) < (0 | f[t >> 2]))) return 0;
                        for (i = 0;;) {
                            if ((0 | f[r >> 2]) >= 1) {
                                o = i, a = 4;
                                break
                            }
                            if (Lr(e), u = i + 1 | 0, !((0 | f[n >> 2]) < (0 | f[t >> 2]))) {
                                o = u, a = 4;
                                break
                            }
                            i = u
                        }
                        return 4 == (0 | a) ? 0 | o : 0
                    }

                    function yr(e, r, t, n, i, o, a) {
                        r |= 0, n |= 0, i |= 0, a |= 0;
                        var u = 0,
                            l = 0,
                            s = 0;
                        if ((0 | (o |= 0)) > 0)
                            for (u = e |= 0, l = t |= 0, s = 0; Ut[31 & f[2883]](u, l, i, a), (0 | (s = s + 1 | 0)) != (0 | o);) u = u + r | 0, l = l + n | 0
                    }

                    function Lr(e) {
                        var r = 0,
                            t = 0,
                            n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0;
                        if (!((0 | f[(r = 24 + (e |= 0) | 0) >> 2]) >= 1)) {
                            do {
                                if (0 | f[e + 4 >> 2]) Pt[31 & f[3020]](e);
                                else {
                                    if (0 | f[e + 20 >> 2]) {
                                        Pt[31 & f[3021]](e);
                                        break
                                    }
                                    if (t = e + 8 | 0, (0 | A(0 | f[(n = e + 52 | 0) >> 2], 0 | f[t >> 2])) > 0) {
                                        a = e + 68 | 0, u = 0, l = 0 | f[(o = e + 76 | 0) >> 2];
                                        do {
                                            i[(0 | f[a >> 2]) + u >> 0] = f[l + (u << 2) >> 2], l = 0 | f[o >> 2], f[l + (u << 2) >> 2] = 0, u = u + 1 | 0
                                        } while ((0 | u) < (0 | A(0 | f[n >> 2], 0 | f[t >> 2])))
                                    }
                                }
                            } while (0);
                            f[r >> 2] = (0 | f[r >> 2]) + (0 | f[e + 28 >> 2]), f[(r = e + 68 | 0) >> 2] = (0 | f[r >> 2]) + (0 | f[e + 72 >> 2]), f[(r = e + 64 | 0) >> 2] = 1 + (0 | f[r >> 2])
                        }
                    }

                    function gr(e, r) {
                        return r |= 0, 0 | f[(e |= 0) >> 2] ? void Rt[15 & f[3019]](e, r) : void Rt[15 & f[3018]](e, r)
                    }

                    function Tr(e, r) {
                        var t, n;
                        return r |= 0, t = 0 | f[32 + (e |= 0) >> 2], 0 | ((0 | (n = ((0 | f[e + 24 >> 2]) - 1 + t | 0) / (0 | t) | 0)) > (0 | r) ? r : n)
                    }

                    function Dr(e, r, t, n) {
                        e |= 0, r |= 0, n |= 0;
                        var i, o, a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0;
                        return a = 0 | f[(t |= 0) >> 2], i = 0 | f[n >> 2], u = ((0 | r) < 0) << 31 >> 31, o = ((0 | e) < 0) << 31 >> 31, (0 | r) > 0 & 0 == (0 | a) ? (l = 0 | _t(0 | i, ((0 | i) < 0) << 31 >> 31 | 0, 0 | e, 0 | o), s = M, c = 0 | ot(0 | r, 0 | u, -1, -1), d = s = 0 | dt(0 | ot(0 | c, 0 | M, 0 | l, 0 | s), 0 | M, 0 | r, 0 | u)) : d = a, (0 | e) > 0 & 0 == (0 | i) ? (a = 0 | _t(0 | d, ((0 | d) < 0) << 31 >> 31 | 0, 0 | r, 0 | u), u = M, r = 0 | ot(0 | e, 0 | o, -1, -1), _ = u = 0 | dt(0 | (s = 0 | ot(0 | r, 0 | M, 0 | a, 0 | u)), 0 | M, 0 | e, 0 | o)) : _ = i, (0 | d) < 1 | (0 | _) < 1 ? 0 : (f[t >> 2] = d, f[n >> 2] = _, 1)
                    }

                    function Cr(e, r, t, n) {
                        var o, a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0;
                        if (o = S, S = S + 16 | 0, a = o + 4 | 0, u = o, (0 | (r |= 0)) < 1 | (0 | (e |= 0)) < 1 | 0 == (0 | (n |= 0))) return S = o, 2;
                        if (l = 0 != (0 | (t |= 0))) {
                            if (0 | f[t + 8 >> 2]) {
                                if (s = 0 | f[t + 20 >> 2], c = 0 | f[t + 24 >> 2], d = 0 | f[t + 12 >> 2], (0 | c) < 1 | (0 | s) < 1 | ((_ = 0 | f[t + 16 >> 2]) | d | 0) < 0) return S = o, 2;
                                if (((-2 & d) + s | 0) > (0 | e) | ((-2 & _) + c | 0) > (0 | r)) return S = o, 2;
                                h = s, m = c
                            } else h = e, m = r;
                            if (0 | f[t + 28 >> 2]) {
                                if (f[a >> 2] = f[t + 32 >> 2], f[u >> 2] = f[t + 36 >> 2], c = 0 == (0 | Dr(h, m, a, u))) return S = o, 2;
                                p = 0 | f[a >> 2], v = 0 | f[u >> 2]
                            } else p = h, v = m
                        } else p = e, v = r;
                        if (f[n + 4 >> 2] = p, f[(r = n + 8 | 0) >> 2] = v, !((0 | v) > 0 & (0 | p) > 0 & (e = 0 | f[n >> 2]) >>> 0 < 13)) return S = o, 2;
                        do {
                            if ((0 | f[n + 12 >> 2]) < 1 && 0 == (0 | f[(m = n + 80 | 0) >> 2])) {
                                if (h = ((0 | p) < 0) << 31 >> 31, a = 0 | _t(255 & (u = 0 | i[5320 + e >> 0]) | 0, 0, 0 | p, 0 | h), (c = M) >>> 0 > 0 | 0 == (0 | c) & a >>> 0 > 4294967295) return S = o, 2;
                                if (c = 0 | _t(0 | (a = 0 | A(255 & u, p)), ((0 | a) < 0) << 31 >> 31 | 0, 0 | v, 0 | (u = ((0 | v) < 0) << 31 >> 31)), s = M, (_ = e >>> 0 < 11) ? (b = 0, w = 0, k = 0, E = 0, y = 0, L = 0) : (T = 0 | _t(0 | (d = (p + 1 | 0) / 2 | 0), ((0 | d) < 0) << 31 >> 31 | 0, 0 | (g = (v + 1 | 0) / 2 | 0), ((0 | g) < 0) << 31 >> 31 | 0), g = M, D = 12 == (0 | e), C = 0 | _t(0 | p, 0 | h, 0 | v, 0 | u), b = d, w = D ? p : 0, k = T, E = g, y = D ? C : 0, L = D ? M : 0), D = 0 | mt(0 | k, 0 | E, 1), C = M, g = 0 | ot(0 | y, 0 | L, 0 | c, 0 | s), !(C = 0 | Je(s = 0 | ot(0 | g, 0 | M, 0 | D, 0 | C), M, 1))) return S = o, 1;
                                if (f[m >> 2] = C, f[n + 16 >> 2] = C, _) {
                                    f[n + 20 >> 2] = a, f[n + 24 >> 2] = c;
                                    break
                                }
                                f[n + 32 >> 2] = a, f[n + 48 >> 2] = c, a = C + c | 0, f[n + 20 >> 2] = a, f[n + 36 >> 2] = b, f[n + 52 >> 2] = k, f[n + 24 >> 2] = a + k, f[n + 40 >> 2] = b, f[n + 56 >> 2] = k, 12 == (0 | e) && (f[n + 28 >> 2] = a + D), f[n + 60 >> 2] = y, f[n + 44 >> 2] = w
                            }
                        } while (0);
                        return 0 != (0 | (w = 0 | Pr(n))) | 1 ^ l ? (S = o, 0 | w) : 0 | f[t + 48 >> 2] ? (t = (0 | f[r >> 2]) - 1 | 0, (0 | f[n >> 2]) >>> 0 < 11 ? (w = 0 | f[(r = n + 20 | 0) >> 2], l = 0 | A(w, t), f[(y = n + 16 | 0) >> 2] = (0 | f[y >> 2]) + l, f[r >> 2] = 0 - w, S = o, 0) : (r = 0 | f[(w = n + 32 | 0) >> 2], l = 0 | A(r, t), f[(y = n + 16 | 0) >> 2] = (0 | f[y >> 2]) + l, f[w >> 2] = 0 - r, r = t >> 1, l = 0 | f[(w = n + 36 | 0) >> 2], y = 0 | A(l, r), f[(e = n + 20 | 0) >> 2] = (0 | f[e >> 2]) + y, f[w >> 2] = 0 - l, w = 0 | f[(l = n + 40 | 0) >> 2], y = 0 | A(w, r), f[(r = n + 24 | 0) >> 2] = (0 | f[r >> 2]) + y, f[l >> 2] = 0 - w, (l = 0 | f[(w = n + 28 | 0) >> 2]) ? (n = 0 | f[(y = n + 44 | 0) >> 2], r = l + (0 | A(n, t)) | 0, f[w >> 2] = r, f[y >> 2] = 0 - n, S = o, 0) : (S = o, 0))) : (S = o, 0)
                    }

                    function Pr(e) {
                        var r, t, n, i, o, a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0;
                        if (r = 0 | f[(e |= 0) >> 2], t = 0 | f[e + 4 >> 2], n = 0 | f[e + 8 >> 2], r >>> 0 >= 13) return 2;
                        o = ((0 | (i = n + -1 | 0)) < 0) << 31 >> 31;
                        do {
                            if (!(r >>> 0 < 11)) {
                                if (c = (t + 1 | 0) / 2 | 0, l = (0 | (a = 0 | f[e + 32 >> 2])) > -1 ? a : 0 - a | 0, u = (0 | (a = 0 | f[e + 36 >> 2])) > -1 ? a : 0 - a | 0, d = (0 | (a = 0 | f[e + 40 >> 2])) > -1 ? a : 0 - a | 0, _ = (0 | (a = 0 | f[e + 44 >> 2])) > -1 ? a : 0 - a | 0, m = 0 | ot(0 | (a = 0 | _t(0 | l, ((0 | l) < 0) << 31 >> 31 | 0, 0 | i, 0 | o)), 0 | M, 0 | t, 0 | (h = ((0 | t) < 0) << 31 >> 31)), a = M, S = 0 | ot(0 | (b = 0 | _t(0 | u, ((0 | u) < 0) << 31 >> 31 | 0, 0 | (p = ((n + 1 | 0) / 2 | 0) - 1 | 0), 0 | (v = ((0 | p) < 0) << 31 >> 31))), 0 | M, 0 | c, 0 | (w = ((0 | c) < 0) << 31 >> 31)), b = M, v = 0 | ot(0 | (k = 0 | _t(0 | d, ((0 | d) < 0) << 31 >> 31 | 0, 0 | p, 0 | v)), 0 | M, 0 | c, 0 | w), w = M, k = 0 != (0 | f[e + 24 >> 2]) & 0 != (0 | f[e + 20 >> 2]) & 0 != (0 | f[e + 16 >> 2]) & (0 | d) >= (0 | c) & (0 | u) >= (0 | c) & (0 | l) >= (0 | t) & (a >>> 0 < 0 | (0 == (0 | a) ? m >>> 0 <= (0 | f[e + 48 >> 2]) >>> 0 : 0)) & (b >>> 0 < 0 | (0 == (0 | b) ? S >>> 0 <= (0 | f[e + 52 >> 2]) >>> 0 : 0)) & (w >>> 0 < 0 | (0 == (0 | w) ? v >>> 0 <= (0 | f[e + 56 >> 2]) >>> 0 : 0)), 12 == (0 | r)) {
                                    if (w = 0 | ot(0 | (v = 0 | _t(0 | _, ((0 | _) < 0) << 31 >> 31 | 0, 0 | i, 0 | o)), 0 | M, 0 | t, 0 | h), h = M, 0 != (0 | f[e + 28 >> 2]) & (0 | _) >= (0 | t) & k & (h >>> 0 < 0 | (0 == (0 | h) ? w >>> 0 <= (0 | f[e + 60 >> 2]) >>> 0 : 0))) break;
                                    return 2
                                }
                                if (k) break;
                                return 2
                            }
                            if (a = 0 | _t(0 | (u = (0 | (a = 0 | f[e + 20 >> 2])) > -1 ? a : 0 - a | 0), ((0 | u) < 0) << 31 >> 31 | 0, 0 | i, 0 | o), l = 0 | A(0 | s[5320 + r >> 0], t), c = 0 | ot(0 | a, 0 | M, 0 | l, ((0 | l) < 0) << 31 >> 31 | 0), a = M, !(0 != (0 | f[e + 16 >> 2]) & (0 | u) >= (0 | l) & (a >>> 0 < 0 | (0 == (0 | a) ? c >>> 0 <= (0 | f[e + 24 >> 2]) >>> 0 : 0)))) return 2
                        } while (0);
                        return 0
                    }

                    function Rr(e, r) {
                        if (0 == (0 | (e |= 0)) | 512 != (-256 & (r |= 0) | 0)) return 0;
                        e = (r = e) + 84 | 0;
                        do {
                            f[r >> 2] = 0, r = r + 4 | 0
                        } while ((0 | r) < (0 | e));
                        return 1
                    }

                    function Or(e) {
                        var r;
                        (e |= 0) && (r = e + 80 | 0, (0 | f[e + 12 >> 2]) < 1 && Ve(0 | f[r >> 2]), f[r >> 2] = 0)
                    }

                    function Br(e, r) {
                        var t, n, i, o, a = 0,
                            u = 0;
                        if (t = 4 + (e |= 0) | 0, f[4 + (r |= 0) >> 2] = f[t >> 2], n = e + 8 | 0, f[r + 8 >> 2] = f[n >> 2], 0 | Pr(r)) return 2;
                        if (i = 0 | f[e >> 2], a = 0 | f[n >> 2], u = 0 | f[t >> 2], o = 0 | f[e + 16 >> 2], i >>> 0 < 11) return xr(o, 0 | f[e + 20 >> 2], 0 | f[r + 16 >> 2], 0 | f[r + 20 >> 2], 0 | A(0 | s[5320 + i >> 0], u), a), 0;
                        if (xr(o, 0 | f[e + 32 >> 2], 0 | f[r + 16 >> 2], 0 | f[r + 32 >> 2], u, a), xr(0 | f[e + 20 >> 2], 0 | f[e + 36 >> 2], 0 | f[r + 20 >> 2], 0 | f[r + 36 >> 2], (1 + (0 | f[t >> 2]) | 0) / 2 | 0, (1 + (0 | f[n >> 2]) | 0) / 2 | 0), xr(0 | f[e + 24 >> 2], 0 | f[e + 40 >> 2], 0 | f[r + 24 >> 2], 0 | f[r + 40 >> 2], (1 + (0 | f[t >> 2]) | 0) / 2 | 0, (1 + (0 | f[n >> 2]) | 0) / 2 | 0), (u = (a = 0 | f[e >> 2]) - 1 | 0) >>> 0 < 12) {
                            if (!((a + -7 | 0) >>> 0 < 4 | 0 != (2077 >>> (65535 & u) & 1))) return 0
                        } else if ((a + -7 | 0) >>> 0 >= 4) return 0;
                        return xr(0 | f[e + 28 >> 2], 0 | f[e + 44 >> 2], 0 | f[r + 28 >> 2], 0 | f[r + 44 >> 2], 0 | f[t >> 2], 0 | f[n >> 2]), 0
                    }

                    function xr(e, r, t, n, i, o) {
                        r |= 0, n |= 0, i |= 0;
                        var a = 0,
                            u = 0,
                            f = 0;
                        if ((0 | (o |= 0)) > 0)
                            for (a = o, u = e |= 0, f = t |= 0; lt(0 | f, 0 | u, 0 | i), (0 | a) > 1;) a = a + -1 | 0, u = u + r | 0, f = f + n | 0
                    }

                    function Nr(e, r) {
                        return r |= 0, 1 & ((0 | f[12 + (e |= 0) >> 2]) > 1 && 0 | r && ((0 | f[e >> 2]) - 7 | 0) >>> 0 < 4 ? 0 != (0 | f[r + 8 >> 2]) : 0) | 0
                    }

                    function Ir(e, r) {
                        r |= 0;
                        var t, n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            c = 0,
                            d = 0;
                        if (e |= 0) {
                            t = 0 | f[e + 44 >> 2];
                            do {
                                if ((0 | t) >= 0) {
                                    if ((0 | t) <= 100) {
                                        if (!((0 | (n = 255 * t | 0)) > 99)) break;
                                        i = (0 | n) / 100 | 0
                                    } else i = 255;
                                    (0 | (n = 0 | f[r + 800 >> 2])) < 12 ? (o = (0 | A(0 | s[5333 + ((0 | n) > 0 ? n : 0) >> 0], i)) >> 3, f[r + 804 >> 2] = o, a = o) : a = 0 | f[r + 804 >> 2], (0 | (o = 0 | f[r + 832 >> 2])) < 12 ? (n = (0 | A(0 | s[5333 + ((0 | o) > 0 ? o : 0) >> 0], i)) >> 3, f[r + 836 >> 2] = n, u = n) : u = 0 | f[r + 836 >> 2], (0 | (n = 0 | f[r + 864 >> 2])) < 12 ? (o = (0 | A(0 | s[5333 + ((0 | n) > 0 ? n : 0) >> 0], i)) >> 3, f[r + 868 >> 2] = o, l = o) : l = 0 | f[r + 868 >> 2], (0 | (o = 0 | f[r + 896 >> 2])) < 12 ? (n = (0 | A(0 | s[5333 + ((0 | o) > 0 ? o : 0) >> 0], i)) >> 3, f[r + 900 >> 2] = n, c = n) : c = 0 | f[r + 900 >> 2], c | l | u | a | 0 && (Fr(r + 544 | 0, 1), f[r + 540 >> 2] = 1)
                                }
                            } while (0);
                            if (a = 0 | f[e + 52 >> 2], f[(e = r + 2372 | 0) >> 2] = a, (0 | a) <= 100) {
                                if (!((0 | a) < 0)) return;
                                d = 0
                            } else d = 100;
                            f[e >> 2] = d
                        }
                    }

                    function Fr(e, r) {
                        r = +r;
                        var t;
                        lt(8 + (e |= 0) | 0, 152, 220), f[e >> 2] = 0, f[e + 4 >> 2] = 31, t = r < 0 ? 0 : r > 1 ? 256 : ~~(256 * r) >>> 0, f[e + 228 >> 2] = t
                    }

                    function Hr(e, r) {
                        r |= 0;
                        var t, n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0;
                        if (n = 160 + (e |= 0) | 0, o = (0 | f[e + 2308 >> 2]) > 0 && (0 | (i = 0 | f[e + 2300 >> 2])) >= (0 | f[e + 300 >> 2]) ? (0 | i) <= (0 | f[e + 308 >> 2]) : 0, i = 1 & o, !(0 | f[(a = e + 148 | 0) >> 2])) return f[e + 164 >> 2] = f[e + 2300 >> 2], f[e + 168 >> 2] = i, ee(e, n), 0 | re(e, r);
                        if (t = e + 124 | 0, !(1 & (0 | Bt[7 & f[(l = 380) >> 2]](t)))) return 0;
                        l = r, r = 108 + (u = e + 180 | 0) | 0;
                        do {
                            f[u >> 2] = f[l >> 2], u = u + 4 | 0, l = l + 4 | 0
                        } while ((0 | u) < (0 | r));
                        return l = e + 152 | 0, f[n >> 2] = f[l >> 2], f[e + 164 >> 2] = f[e + 2300 >> 2], f[e + 168 >> 2] = i, 2 == (0 | f[a >> 2]) ? (i = 0 | f[(a = e + 176 | 0) >> 2], u = e + 2304 | 0, f[a >> 2] = f[u >> 2], f[u >> 2] = i) : ee(e, n), o ? (n = 0 | f[(o = e + 172 | 0) >> 2], i = e + 2260 | 0, f[o >> 2] = f[i >> 2], f[i >> 2] = n, s = 0 | f[96], Pt[31 & s](t), d = (0 | (c = 1 + (0 | f[l >> 2]) | 0)) == (0 | f[(e + 156 | 0) >> 2]) ? 0 : c, f[l >> 2] = d, 1) : (s = 0 | f[96], Pt[31 & s](t), d = (0 | (c = 1 + (0 | f[l >> 2]) | 0)) == (0 | f[(e + 156 | 0) >> 2]) ? 0 : c, f[l >> 2] = d, 1)
                    }

                    function Ur(e, r, t, n, o) {
                        e |= 0, r |= 0, t |= 0, n |= 0, o |= 0;
                        var u, l, s, c, d, _, h, m, v, b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            A = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0,
                            Pe = 0,
                            Re = 0,
                            Oe = 0,
                            Be = 0;
                        u = S, S = S + 64 | 0, s = u, b = u + 24 | 0, c = u + 8 | 0, d = u + 20 | 0, f[(l = u + 16 | 0) >> 2] = r, _ = 0 != (0 | e), m = h = b + 40 | 0, v = b + 39 | 0, b = c + 4 | 0, w = 0, k = 0, E = 0, A = r;
                        e: for (;;) {
                            do {
                                if ((0 | k) > -1) {
                                    if ((0 | w) > (2147483647 - k | 0)) {
                                        f[(r = 612) >> 2] = 75, y = -1;
                                        break
                                    }
                                    y = w + k | 0;
                                    break
                                }
                                y = k
                            } while (0);
                            if (!((r = 0 | i[A >> 0]) << 24 >> 24)) {
                                L = 87;
                                break
                            }
                            g = r, T = A;
                            r: for (;;) {
                                switch (g << 24 >> 24) {
                                    case 37:
                                        D = T, C = T, L = 9;
                                        break r;
                                    case 0:
                                        P = T, R = T;
                                        break r
                                }
                                r = T + 1 | 0, f[l >> 2] = r, g = 0 | i[r >> 0], T = r
                            }
                            r: do {
                                if (9 == (0 | L))
                                    for (;;) {
                                        if (L = 0, 37 != (0 | i[C + 1 >> 0])) {
                                            P = D, R = C;
                                            break r
                                        }
                                        if (r = D + 1 | 0, O = C + 2 | 0, f[l >> 2] = O, 37 != (0 | i[O >> 0])) {
                                            P = r, R = O;
                                            break
                                        }
                                        D = r, C = O, L = 9
                                    }
                            } while (0);
                            if (O = P - A | 0, _ && Yr(e, A, O), 0 | O) w = O, k = y, A = R;
                            else {
                                (r = (0 | i[(O = R + 1 | 0) >> 0]) - 48 | 0) >>> 0 < 10 ? (x = (B = 36 == (0 | i[R + 2 >> 0])) ? r : -1, N = B ? 1 : E, I = B ? R + 3 | 0 : O) : (x = -1, N = E, I = O), f[l >> 2] = I, B = ((O = 0 | i[I >> 0]) << 24 >> 24) - 32 | 0;
                                r: do {
                                    if (B >>> 0 < 32)
                                        for (r = 0, F = O, H = B, U = I;;) {
                                            if (!(75913 & (G = 1 << H))) {
                                                W = r, Y = F, V = U;
                                                break r
                                            }
                                            if (q = G | r, G = U + 1 | 0, f[l >> 2] = G, (H = ((j = 0 | i[G >> 0]) << 24 >> 24) - 32 | 0) >>> 0 >= 32) {
                                                W = q, Y = j, V = G;
                                                break
                                            }
                                            r = q, F = j, U = G
                                        } else W = 0, Y = O, V = I
                                } while (0);
                                if (Y << 24 >> 24 == 42) {
                                    if ((B = (0 | i[(O = V + 1 | 0) >> 0]) - 48 | 0) >>> 0 < 10 && 36 == (0 | i[V + 2 >> 0])) f[o + (B << 2) >> 2] = 10, z = 0 | f[n + ((0 | i[O >> 0]) - 48 << 3) >> 2], X = 1, K = V + 3 | 0;
                                    else {
                                        if (0 | N) {
                                            $ = -1;
                                            break
                                        }
                                        _ ? (B = 3 + (0 | f[t >> 2]) & -4, U = 0 | f[B >> 2], f[t >> 2] = B + 4, z = U, X = 0, K = O) : (z = 0, X = 0, K = O)
                                    }
                                    f[l >> 2] = K, J = (O = (0 | z) < 0) ? 0 - z | 0 : z, Q = O ? 8192 | W : W, Z = X, ee = K
                                } else {
                                    if ((0 | (O = 0 | Vr(l))) < 0) {
                                        $ = -1;
                                        break
                                    }
                                    J = O, Q = W, Z = N, ee = 0 | f[l >> 2]
                                }
                                do {
                                    if (46 == (0 | i[ee >> 0])) {
                                        if (42 != (0 | i[ee + 1 >> 0])) {
                                            f[l >> 2] = ee + 1, re = O = 0 | Vr(l), te = 0 | f[l >> 2];
                                            break
                                        }
                                        if ((U = (0 | i[(O = ee + 2 | 0) >> 0]) - 48 | 0) >>> 0 < 10 && 36 == (0 | i[ee + 3 >> 0])) {
                                            f[o + (U << 2) >> 2] = 10, U = 0 | f[n + ((0 | i[O >> 0]) - 48 << 3) >> 2], B = ee + 4 | 0, f[l >> 2] = B, re = U, te = B;
                                            break
                                        }
                                        if (0 | Z) {
                                            $ = -1;
                                            break e
                                        }
                                        _ ? (B = 3 + (0 | f[t >> 2]) & -4, U = 0 | f[B >> 2], f[t >> 2] = B + 4, ne = U) : ne = 0, f[l >> 2] = O, re = ne, te = O
                                    } else re = -1, te = ee
                                } while (0);
                                for (O = 0, U = te;;) {
                                    if (((0 | i[U >> 0]) - 65 | 0) >>> 0 > 57) {
                                        $ = -1;
                                        break e
                                    }
                                    if (ie = U + 1 | 0, f[l >> 2] = ie, !(((ae = 255 & (oe = 0 | i[(0 | i[U >> 0]) - 65 + (10988 + (58 * O | 0)) >> 0])) - 1 | 0) >>> 0 < 8)) break;
                                    O = ae, U = ie
                                }
                                if (!(oe << 24 >> 24)) {
                                    $ = -1;
                                    break
                                }
                                B = (0 | x) > -1;
                                do {
                                    if (oe << 24 >> 24 == 19) {
                                        if (B) {
                                            $ = -1;
                                            break e
                                        }
                                        L = 49
                                    } else {
                                        if (B) {
                                            f[o + (x << 2) >> 2] = ae, r = 0 | f[4 + (F = n + (x << 3) | 0) >> 2], f[(H = s) >> 2] = f[F >> 2], f[H + 4 >> 2] = r, L = 49;
                                            break
                                        }
                                        if (!_) {
                                            $ = 0;
                                            break e
                                        }
                                        qr(s, ae, t)
                                    }
                                } while (0);
                                if (49 != (0 | L) || (L = 0, _)) {
                                    r = 0 != (0 | O) & 3 == (15 & (B = 0 | i[U >> 0]) | 0) ? -33 & B : B, B = -65537 & Q, H = 0 == (8192 & Q | 0) ? Q : B;
                                    r: do {
                                        switch (0 | r) {
                                            case 110:
                                                switch ((255 & O) << 24 >> 24) {
                                                    case 0:
                                                    case 1:
                                                        f[f[s >> 2] >> 2] = y, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    case 2:
                                                        F = 0 | f[s >> 2], f[F >> 2] = y, f[F + 4 >> 2] = ((0 | y) < 0) << 31 >> 31, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    case 3:
                                                        a[f[s >> 2] >> 1] = y, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    case 4:
                                                        i[f[s >> 2] >> 0] = y, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    case 6:
                                                        f[f[s >> 2] >> 2] = y, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    case 7:
                                                        F = 0 | f[s >> 2], f[F >> 2] = y, f[F + 4 >> 2] = ((0 | y) < 0) << 31 >> 31, w = 0, k = y, E = Z, A = ie;
                                                        continue e;
                                                    default:
                                                        w = 0, k = y, E = Z, A = ie;
                                                        continue e
                                                }
                                                break;
                                            case 112:
                                                ue = 120, fe = re >>> 0 > 8 ? re : 8, le = 8 | H, L = 61;
                                                break;
                                            case 88:
                                            case 120:
                                                ue = r, fe = re, le = H, L = 61;
                                                break;
                                            case 111:
                                                se = F = 0 | zr(G = 0 | f[(F = s) >> 2], j = 0 | f[F + 4 >> 2], h), ce = 0, de = 11452, _e = 0 == (8 & H | 0) | (0 | re) > (0 | (q = m - F | 0)) ? re : q + 1 | 0, he = H, me = G, pe = j, L = 67;
                                                break;
                                            case 105:
                                            case 100:
                                                if (G = 0 | f[(j = s) >> 2], (0 | (q = 0 | f[j + 4 >> 2])) < 0) {
                                                    j = 0 | it(0, 0, 0 | G, 0 | q), F = M, f[(ve = s) >> 2] = j, f[ve + 4 >> 2] = F, be = 1, we = 11452, Se = j, ke = F, L = 66;
                                                    break r
                                                }
                                                be = 0 != (2049 & H | 0) & 1, we = 0 == (2048 & H | 0) ? 0 == (1 & H | 0) ? 11452 : 11454 : 11453, Se = G, ke = q, L = 66;
                                                break r;
                                            case 117:
                                                be = 0, we = 11452, Se = 0 | f[(q = s) >> 2], ke = 0 | f[q + 4 >> 2], L = 66;
                                                break;
                                            case 99:
                                                i[v >> 0] = f[s >> 2], Ee = v, Me = 0, Ae = 11452, ye = h, Le = 1, ge = B;
                                                break;
                                            case 109:
                                                Te = 0 | Ge(0 | f[(q = 612) >> 2]), L = 71;
                                                break;
                                            case 115:
                                                Te = 0 | (q = 0 | f[s >> 2]) ? q : 11462, L = 71;
                                                break;
                                            case 67:
                                                f[c >> 2] = f[s >> 2], f[b >> 2] = 0, f[s >> 2] = c, De = -1, Ce = c, L = 75;
                                                break;
                                            case 83:
                                                q = 0 | f[s >> 2], re ? (De = re, Ce = q, L = 75) : (Kr(e, 32, J, 0, H), Pe = 0, L = 84);
                                                break;
                                            case 65:
                                            case 71:
                                            case 70:
                                            case 69:
                                            case 97:
                                            case 103:
                                            case 102:
                                            case 101:
                                                w = 0 | Jr(e, +p[s >> 3], J, re, H, r), k = y, E = Z, A = ie;
                                                continue e;
                                            default:
                                                Ee = A, Me = 0, Ae = 11452, ye = h, Le = re, ge = H
                                        }
                                    } while (0);
                                    r: do {
                                        if (61 == (0 | L)) L = 0, se = r = 0 | jr(O = 0 | f[(r = s) >> 2], U = 0 | f[r + 4 >> 2], h, 32 & ue), ce = (q = 0 == (8 & le | 0) | 0 == (0 | O) & 0 == (0 | U)) ? 0 : 2, de = q ? 11452 : 11452 + (ue >> 4) | 0, _e = fe, he = le, me = O, pe = U, L = 67;
                                        else if (66 == (0 | L)) L = 0, se = 0 | Xr(Se, ke, h), ce = be, de = we, _e = re, he = H, me = Se, pe = ke, L = 67;
                                        else if (71 == (0 | L)) L = 0, Ee = Te, Me = 0, Ae = 11452, ye = (O = 0 == (0 | (U = 0 | Ie(Te, 0, re)))) ? Te + re | 0 : U, Le = O ? re : U - Te | 0, ge = B;
                                        else if (75 == (0 | L)) {
                                            for (L = 0, U = Ce, O = 0, q = 0;;) {
                                                if (!(r = 0 | f[U >> 2])) {
                                                    Re = O, Oe = q;
                                                    break
                                                }
                                                if ((0 | (G = 0 | $r(d, r))) < 0 | G >>> 0 > (De - O | 0) >>> 0) {
                                                    Re = O, Oe = G;
                                                    break
                                                }
                                                if (!(De >>> 0 > (r = G + O | 0) >>> 0)) {
                                                    Re = r, Oe = G;
                                                    break
                                                }
                                                U = U + 4 | 0, O = r, q = G
                                            }
                                            if ((0 | Oe) < 0) {
                                                $ = -1;
                                                break e
                                            }
                                            if (Kr(e, 32, J, Re, H), Re)
                                                for (q = Ce, O = 0;;) {
                                                    if (!(U = 0 | f[q >> 2])) {
                                                        Pe = Re, L = 84;
                                                        break r
                                                    }
                                                    if ((0 | (O = (G = 0 | $r(d, U)) + O | 0)) > (0 | Re)) {
                                                        Pe = Re, L = 84;
                                                        break r
                                                    }
                                                    if (Yr(e, d, G), O >>> 0 >= Re >>> 0) {
                                                        Pe = Re, L = 84;
                                                        break
                                                    }
                                                    q = q + 4 | 0
                                                } else Pe = 0, L = 84
                                        }
                                    } while (0);
                                    if (67 == (0 | L)) L = 0, O = m - se + (1 & (1 ^ (B = 0 != (0 | me) | 0 != (0 | pe)))) | 0, Ee = (q = 0 != (0 | _e) | B) ? se : h, Me = ce, Ae = de, ye = h, Le = q ? (0 | _e) > (0 | O) ? _e : O : _e, ge = (0 | _e) > -1 ? -65537 & he : he;
                                    else if (84 == (0 | L)) {
                                        L = 0, Kr(e, 32, J, Pe, 8192 ^ H), w = (0 | J) > (0 | Pe) ? J : Pe, k = y, E = Z, A = ie;
                                        continue
                                    }
                                    Kr(e, 32, G = (0 | J) < (0 | (B = (q = (0 | Le) < (0 | (O = ye - Ee | 0)) ? O : Le) + Me | 0)) ? B : J, B, ge), Yr(e, Ae, Me), Kr(e, 48, G, B, 65536 ^ ge), Kr(e, 48, q, O, 0), Yr(e, Ee, O), Kr(e, 32, G, B, 8192 ^ ge), w = G, k = y, E = Z, A = ie
                                } else w = 0, k = y, E = Z, A = ie
                            }
                        }
                        e: do {
                            if (87 == (0 | L))
                                if (e) $ = y;
                                else if (E) {
                                    for (ie = 1;;) {
                                        if (!(A = 0 | f[o + (ie << 2) >> 2])) {
                                            Be = ie;
                                            break
                                        }
                                        if (qr(n + (ie << 3) | 0, A, t), (0 | (ie = ie + 1 | 0)) >= 10) {
                                            $ = 1;
                                            break e
                                        }
                                    }
                                    for (;;) {
                                        if (0 | f[o + (Be << 2) >> 2]) {
                                            $ = -1;
                                            break e
                                        }
                                        if ((0 | (Be = Be + 1 | 0)) >= 10) {
                                            $ = 1;
                                            break
                                        }
                                    }
                                } else $ = 0
                        } while (0);
                        return S = u, 0 | $
                    }

                    function Gr(e) {
                        return 0
                    }

                    function Wr(e) {}

                    function Yr(e, r, t) {
                        r |= 0, t |= 0, 32 & f[(e |= 0) >> 2] || rt(r, t, e)
                    }

                    function Vr(e) {
                        var r = 0,
                            t = 0,
                            n = 0,
                            o = 0,
                            a = 0;
                        if (r = 0 | f[(e |= 0) >> 2], (t = (0 | i[r >> 0]) - 48 | 0) >>> 0 < 10)
                            for (n = 0, o = r, r = t;;) {
                                if (t = r + (10 * n | 0) | 0, o = o + 1 | 0, f[e >> 2] = o, (r = (0 | i[o >> 0]) - 48 | 0) >>> 0 >= 10) {
                                    a = t;
                                    break
                                }
                                n = t
                            } else a = 0;
                        return 0 | a
                    }

                    function qr(e, r, t) {
                        e |= 0, r |= 0, t |= 0;
                        var n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0;
                        e: do {
                            if (r >>> 0 <= 20) switch (0 | r) {
                                case 9:
                                    n = 3 + (0 | f[t >> 2]) & -4, i = 0 | f[n >> 2], f[t >> 2] = n + 4, f[e >> 2] = i;
                                    break e;
                                case 10:
                                    i = 3 + (0 | f[t >> 2]) & -4, n = 0 | f[i >> 2], f[t >> 2] = i + 4, f[(i = e) >> 2] = n, f[i + 4 >> 2] = ((0 | n) < 0) << 31 >> 31;
                                    break e;
                                case 11:
                                    n = 3 + (0 | f[t >> 2]) & -4, i = 0 | f[n >> 2], f[t >> 2] = n + 4, f[(n = e) >> 2] = i, f[n + 4 >> 2] = 0;
                                    break e;
                                case 12:
                                    n = 7 + (0 | f[t >> 2]) & -8, o = 0 | f[(i = n) >> 2], a = 0 | f[i + 4 >> 2], f[t >> 2] = n + 8, f[(n = e) >> 2] = o, f[n + 4 >> 2] = a;
                                    break e;
                                case 13:
                                    a = 3 + (0 | f[t >> 2]) & -4, n = 0 | f[a >> 2], f[t >> 2] = a + 4, a = (65535 & n) << 16 >> 16, f[(n = e) >> 2] = a, f[n + 4 >> 2] = ((0 | a) < 0) << 31 >> 31;
                                    break e;
                                case 14:
                                    a = 3 + (0 | f[t >> 2]) & -4, n = 0 | f[a >> 2], f[t >> 2] = a + 4, f[(a = e) >> 2] = 65535 & n, f[a + 4 >> 2] = 0;
                                    break e;
                                case 15:
                                    a = 3 + (0 | f[t >> 2]) & -4, n = 0 | f[a >> 2], f[t >> 2] = a + 4, a = (255 & n) << 24 >> 24, f[(n = e) >> 2] = a, f[n + 4 >> 2] = ((0 | a) < 0) << 31 >> 31;
                                    break e;
                                case 16:
                                    a = 3 + (0 | f[t >> 2]) & -4, n = 0 | f[a >> 2], f[t >> 2] = a + 4, f[(a = e) >> 2] = 255 & n, f[a + 4 >> 2] = 0;
                                    break e;
                                case 17:
                                case 18:
                                    a = 7 + (0 | f[t >> 2]) & -8, u = +p[a >> 3], f[t >> 2] = a + 8, p[e >> 3] = u;
                                    break e;
                                default:
                                    break e
                            }
                        } while (0)
                    }

                    function jr(e, r, t, n) {
                        t |= 0, n |= 0;
                        var o = 0,
                            a = 0;
                        if (0 == (0 | (e |= 0)) & 0 == (0 | (r |= 0))) o = t;
                        else
                            for (a = t, t = r, r = e;;) {
                                if (i[(e = a + -1 | 0) >> 0] = 0 | s[11504 + (15 & r) >> 0] | n, 0 == (0 | (r = 0 | ft(0 | r, 0 | t, 4))) & 0 == (0 | (t = M))) {
                                    o = e;
                                    break
                                }
                                a = e
                            }
                        return 0 | o
                    }

                    function zr(e, r, t) {
                        t |= 0;
                        var n = 0,
                            o = 0;
                        if (0 == (0 | (e |= 0)) & 0 == (0 | (r |= 0))) n = t;
                        else
                            for (o = t, t = r, r = e;;) {
                                if (i[(e = o + -1 | 0) >> 0] = 7 & r | 48, 0 == (0 | (r = 0 | ft(0 | r, 0 | t, 3))) & 0 == (0 | (t = M))) {
                                    n = e;
                                    break
                                }
                                o = e
                            }
                        return 0 | n
                    }

                    function Xr(e, r, t) {
                        t |= 0;
                        var n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            f = 0,
                            l = 0;
                        if ((r |= 0) >>> 0 > 0 | 0 == (0 | r) & (e |= 0) >>> 0 > 4294967295) {
                            for (n = t, o = e, a = r; r = 0 | vt(0 | o, 0 | a, 10, 0), i[(n = n + -1 | 0) >> 0] = 255 & r | 48, r = o, o = 0 | dt(0 | o, 0 | a, 10, 0), a >>> 0 > 9 | 9 == (0 | a) & r >>> 0 > 4294967295;) a = M;
                            u = o, f = n
                        } else u = e, f = t;
                        if (u)
                            for (t = u, u = f;;) {
                                if (i[(f = u + -1 | 0) >> 0] = 48 | (t >>> 0) % 10, t >>> 0 < 10) {
                                    l = f;
                                    break
                                }
                                t = (t >>> 0) / 10 | 0, u = f
                            } else l = f;
                        return 0 | l
                    }

                    function Kr(e, r, t, n, i) {
                        e |= 0, r |= 0;
                        var o, a, u = 0;
                        if (o = S, S = S + 256 | 0, a = o, (0 | (t |= 0)) > (0 | (n |= 0)) & 0 == (73728 & (i |= 0) | 0)) {
                            if (at(0 | a, 0 | r, 0 | ((i = t - n | 0) >>> 0 < 256 ? i : 256)), i >>> 0 > 255) {
                                r = t - n | 0, n = i;
                                do {
                                    Yr(e, a, 256), n = n + -256 | 0
                                } while (n >>> 0 > 255);
                                u = 255 & r
                            } else u = i;
                            Yr(e, a, u)
                        }
                        S = o
                    }

                    function $r(e, r) {
                        return 0 | ((e |= 0) ? 0 | et(e, r |= 0, 0) : 0)
                    }

                    function Jr(e, r, t, n, o, a) {
                        e |= 0, r = +r, t |= 0, n |= 0, o |= 0, a |= 0;
                        var u, l, c, d, _, h, m, p, v = 0,
                            b = 0,
                            w = 0,
                            k = 0,
                            E = 0,
                            y = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0,
                            x = 0,
                            N = 0,
                            I = 0,
                            F = 0,
                            H = 0,
                            U = 0,
                            G = 0,
                            W = 0,
                            Y = 0,
                            V = 0,
                            q = 0,
                            j = 0,
                            z = 0,
                            X = 0,
                            K = 0,
                            $ = 0,
                            J = 0,
                            Q = 0,
                            Z = 0,
                            ee = 0,
                            re = 0,
                            te = 0,
                            ne = 0,
                            ie = 0,
                            oe = 0,
                            ae = 0,
                            ue = 0,
                            fe = 0,
                            le = 0,
                            se = 0,
                            ce = 0,
                            de = 0,
                            _e = 0,
                            he = 0,
                            me = 0,
                            pe = 0,
                            ve = 0,
                            be = 0,
                            we = 0,
                            Se = 0,
                            ke = 0,
                            Ee = 0,
                            Me = 0,
                            Ae = 0,
                            ye = 0,
                            Le = 0,
                            ge = 0,
                            Te = 0,
                            De = 0,
                            Ce = 0;
                        u = S, S = S + 560 | 0, l = u + 8 | 0, _ = d = u + 524 | 0, h = u + 512 | 0, f[(c = u) >> 2] = 0, m = h + 12 | 0, Qr(r), (0 | M) < 0 ? (v = -r, b = 1, w = 11469) : (v = r, b = 0 != (2049 & o | 0) & 1, w = 0 == (2048 & o | 0) ? 0 == (1 & o | 0) ? 11470 : 11475 : 11472), Qr(v), p = 2146435072 & M;
                        do {
                            if (p >>> 0 < 2146435072 | 2146435072 == (0 | p) & !1) {
                                if ((k = 0 != (r = 2 * +Zr(v, c))) && (f[c >> 2] = (0 | f[c >> 2]) - 1), 97 == (0 | (E = 32 | a))) {
                                    L = 0 == (0 | (y = 32 & a)) ? w : w + 9 | 0, g = 2 | b, T = 12 - n | 0;
                                    do {
                                        if (!(n >>> 0 > 11 | 0 == (0 | T))) {
                                            D = 8, C = T;
                                            do {
                                                C = C + -1 | 0, D *= 16
                                            } while (0 != (0 | C));
                                            if (45 == (0 | i[L >> 0])) {
                                                P = -(D + (-r - D));
                                                break
                                            }
                                            P = r + D - D;
                                            break
                                        }
                                        P = r
                                    } while (0);
                                    for ((0 | (R = 0 | Xr(C = (0 | (T = 0 | f[c >> 2])) < 0 ? 0 - T | 0 : T, ((0 | C) < 0) << 31 >> 31, m))) == (0 | m) ? (i[(C = h + 11 | 0) >> 0] = 48, O = C) : O = R, i[O + -1 >> 0] = 43 + (T >> 31 & 2), i[(T = O + -2 | 0) >> 0] = a + 15, R = (0 | n) < 1, C = 0 == (8 & o | 0), B = d, x = P; N = ~~x, I = B + 1 | 0, i[B >> 0] = s[11504 + N >> 0] | y, x = 16 * (x - +(0 | N)), 1 != (I - _ | 0) || C & R & 0 == x ? F = I : (i[I >> 0] = 46, F = B + 2 | 0), 0 != x;) B = F;
                                    Kr(e, 32, t, y = (R = m - T | 0) + g + (C = 0 != (0 | n) & ((B = F - _ | 0) - 2 | 0) < (0 | n) ? n + 2 | 0 : B) | 0, o), Yr(e, L, g), Kr(e, 48, t, y, 65536 ^ o), Yr(e, d, B), Kr(e, 48, C - B | 0, 0, 0), Yr(e, T, R), Kr(e, 32, t, y, 8192 ^ o), H = y;
                                    break
                                }
                                y = (0 | n) < 0 ? 6 : n, k ? (R = (0 | f[c >> 2]) - 28 | 0, f[c >> 2] = R, U = 268435456 * r, G = R) : (U = r, G = 0 | f[c >> 2]), B = R = (0 | G) < 0 ? l : l + 288 | 0, x = U;
                                do {
                                    C = ~~x >>> 0, f[B >> 2] = C, B = B + 4 | 0, x = 1e9 * (x - +(C >>> 0))
                                } while (0 != x);
                                if ((0 | G) > 0)
                                    for (k = R, T = B, g = G;;) {
                                        if (L = (0 | g) < 29 ? g : 29, (C = T + -4 | 0) >>> 0 >= k >>> 0) {
                                            I = C, C = 0;
                                            do {
                                                Y = 0 | vt(0 | (W = 0 | ot(0 | (N = 0 | mt(0 | f[I >> 2], 0, 0 | L)), 0 | M, 0 | C, 0)), 0 | (N = M), 1e9, 0), f[I >> 2] = Y, C = 0 | dt(0 | W, 0 | N, 1e9, 0), I = I + -4 | 0
                                            } while (I >>> 0 >= k >>> 0);
                                            C ? (f[(I = k + -4 | 0) >> 2] = C, V = I) : V = k
                                        } else V = k;
                                        for (I = T; !(I >>> 0 <= V >>> 0 || 0 | f[(N = I + -4 | 0) >> 2]);) I = N;
                                        if (C = (0 | f[c >> 2]) - L | 0, f[c >> 2] = C, !((0 | C) > 0)) {
                                            q = V, j = I, z = C;
                                            break
                                        }
                                        k = V, T = I, g = C
                                    } else q = R, j = B, z = G;
                                if ((0 | z) < 0)
                                    for (g = 1 + ((y + 25 | 0) / 9 | 0) | 0, T = 102 == (0 | E), k = q, C = j, N = z;;) {
                                        if (Y = (0 | (W = 0 - N | 0)) < 9 ? W : 9, k >>> 0 < C >>> 0) {
                                            W = (1 << Y) - 1 | 0, X = 1e9 >>> Y, K = 0, $ = k;
                                            do {
                                                J = 0 | f[$ >> 2], f[$ >> 2] = (J >>> Y) + K, K = 0 | A(J & W, X), $ = $ + 4 | 0
                                            } while ($ >>> 0 < C >>> 0);
                                            $ = 0 == (0 | f[k >> 2]) ? k + 4 | 0 : k, K ? (f[C >> 2] = K, Q = $, Z = C + 4 | 0) : (Q = $, Z = C)
                                        } else Q = 0 == (0 | f[k >> 2]) ? k + 4 | 0 : k, Z = C;
                                        if (X = (Z - ($ = T ? R : Q) >> 2 | 0) > (0 | g) ? $ + (g << 2) | 0 : Z, N = (0 | f[c >> 2]) + Y | 0, f[c >> 2] = N, (0 | N) >= 0) {
                                            ee = Q, re = X;
                                            break
                                        }
                                        k = Q, C = X
                                    } else ee = q, re = j;
                                if (C = R, ee >>> 0 < re >>> 0)
                                    if (k = 9 * (C - ee >> 2) | 0, (N = 0 | f[ee >> 2]) >>> 0 < 10) te = k;
                                    else
                                        for (g = k, k = 10;;) {
                                            if (T = g + 1 | 0, N >>> 0 < (k = 10 * k | 0) >>> 0) {
                                                te = T;
                                                break
                                            }
                                            g = T
                                        } else te = 0;
                                if ((0 | (N = y - (102 != (0 | E) ? te : 0) + (((k = 0 != (0 | y)) & (g = 103 == (0 | E))) << 31 >> 31) | 0)) < ((9 * (re - C >> 2) | 0) - 9 | 0)) {
                                    if (N = R + 4 + (((0 | (T = N + 9216 | 0)) / 9 | 0) - 1024 << 2) | 0, (0 | (B = 1 + ((0 | T) % 9 | 0) | 0)) < 9)
                                        for (T = B, B = 10;;) {
                                            if (X = 10 * B | 0, 9 == (0 | (T = T + 1 | 0))) {
                                                ne = X;
                                                break
                                            }
                                            B = X
                                        } else ne = 10;
                                    if ((E = (N + 4 | 0) == (0 | re)) & 0 == (0 | (T = ((B = 0 | f[N >> 2]) >>> 0) % (ne >>> 0) | 0))) le = N, se = te, ce = ee;
                                    else if (D = 0 == (1 & ((B >>> 0) / (ne >>> 0) | 0) | 0) ? 9007199254740992 : 9007199254740994, x = T >>> 0 < (X = (0 | ne) / 2 | 0) >>> 0 ? .5 : E & (0 | T) == (0 | X) ? 1 : 1.5, b ? (ie = (X = 45 == (0 | i[w >> 0])) ? -x : x, oe = X ? -D : D) : (ie = x, oe = D), X = B - T | 0, f[N >> 2] = X, oe + ie != oe) {
                                        if (T = X + ne | 0, f[N >> 2] = T, T >>> 0 > 999999999)
                                            for (T = ee, X = N;;) {
                                                if (B = X + -4 | 0, f[X >> 2] = 0, B >>> 0 < T >>> 0 ? (f[(E = T + -4 | 0) >> 2] = 0, ae = E) : ae = T, E = 1 + (0 | f[B >> 2]) | 0, f[B >> 2] = E, !(E >>> 0 > 999999999)) {
                                                    ue = ae, fe = B;
                                                    break
                                                }
                                                T = ae, X = B
                                            } else ue = ee, fe = N;
                                        if (X = 9 * (C - ue >> 2) | 0, (T = 0 | f[ue >> 2]) >>> 0 < 10) le = fe, se = X, ce = ue;
                                        else
                                            for (B = X, X = 10;;) {
                                                if (E = B + 1 | 0, T >>> 0 < (X = 10 * X | 0) >>> 0) {
                                                    le = fe, se = E, ce = ue;
                                                    break
                                                }
                                                B = E
                                            }
                                    } else le = N, se = te, ce = ee;
                                    de = se, _e = re >>> 0 > (B = le + 4 | 0) >>> 0 ? B : re, he = ce
                                } else de = te, _e = re, he = ee;
                                for (B = _e;;) {
                                    if (B >>> 0 <= he >>> 0) {
                                        me = 0;
                                        break
                                    }
                                    if (0 | f[(X = B + -4 | 0) >> 2]) {
                                        me = 1;
                                        break
                                    }
                                    B = X
                                }
                                N = 0 - de | 0;
                                do {
                                    if (g) {
                                        if ((0 | (X = (1 & (1 ^ k)) + y | 0)) > (0 | de) & (0 | de) > -5 ? (pe = a + -1 | 0, ve = X + -1 - de | 0) : (pe = a + -2 | 0, ve = X + -1 | 0), !(X = 8 & o)) {
                                            if (me && 0 != (0 | (T = 0 | f[B + -4 >> 2])))
                                                if ((T >>> 0) % 10 | 0) be = 0;
                                                else
                                                    for (E = 0, $ = 10;;) {
                                                        if (W = E + 1 | 0, 0 | (T >>> 0) % (($ = 10 * $ | 0) >>> 0)) {
                                                            be = W;
                                                            break
                                                        }
                                                        E = W
                                                    } else be = 9;
                                            if (E = (9 * (B - C >> 2) | 0) - 9 | 0, 102 == (32 | pe)) {
                                                we = pe, Se = (0 | ve) < (0 | (T = (0 | ($ = E - be | 0)) > 0 ? $ : 0)) ? ve : T, ke = 0;
                                                break
                                            }
                                            we = pe, Se = (0 | ve) < (0 | (E = (0 | (T = E + de - be | 0)) > 0 ? T : 0)) ? ve : E, ke = 0;
                                            break
                                        }
                                        we = pe, Se = ve, ke = X
                                    } else we = a, Se = y, ke = 8 & o
                                } while (0);
                                if (C = 0 != (0 | (y = Se | ke)) & 1, k = 102 == (32 | we)) Ee = 0, Me = (0 | de) > 0 ? de : 0;
                                else {
                                    if (E = 0 | Xr(g = (0 | de) < 0 ? N : de, ((0 | g) < 0) << 31 >> 31, m), ((g = m) - E | 0) < 2)
                                        for (T = E;;) {
                                            if (i[($ = T + -1 | 0) >> 0] = 48, !((g - $ | 0) < 2)) {
                                                Ae = $;
                                                break
                                            }
                                            T = $
                                        } else Ae = E;
                                    i[Ae + -1 >> 0] = 43 + (de >> 31 & 2), i[(T = Ae + -2 | 0) >> 0] = we, Ee = T, Me = g - T | 0
                                }
                                if (Kr(e, 32, t, T = b + 1 + Se + C + Me | 0, o), Yr(e, w, b), Kr(e, 48, t, T, 65536 ^ o), k) {
                                    Y = $ = d + 9 | 0, K = d + 8 | 0, W = N = he >>> 0 > R >>> 0 ? R : he;
                                    do {
                                        if (I = 0 | Xr(0 | f[W >> 2], 0, $), (0 | W) == (0 | N))(0 | I) == (0 | $) ? (i[K >> 0] = 48, ye = K) : ye = I;
                                        else if (I >>> 0 > d >>> 0)
                                            for (at(0 | d, 48, I - _ | 0), L = I;;) {
                                                if (!((J = L + -1 | 0) >>> 0 > d >>> 0)) {
                                                    ye = J;
                                                    break
                                                }
                                                L = J
                                            } else ye = I;
                                        Yr(e, ye, Y - ye | 0), W = W + 4 | 0
                                    } while (W >>> 0 <= R >>> 0);
                                    if (0 | y && Yr(e, 11520, 1), W >>> 0 < B >>> 0 & (0 | Se) > 0)
                                        for (R = Se, Y = W;;) {
                                            if ((K = 0 | Xr(0 | f[Y >> 2], 0, $)) >>> 0 > d >>> 0)
                                                for (at(0 | d, 48, K - _ | 0), N = K;;) {
                                                    if (!((k = N + -1 | 0) >>> 0 > d >>> 0)) {
                                                        Le = k;
                                                        break
                                                    }
                                                    N = k
                                                } else Le = K;
                                            if (Yr(e, Le, (0 | R) < 9 ? R : 9), N = R + -9 | 0, !((Y = Y + 4 | 0) >>> 0 < B >>> 0 & (0 | R) > 9)) {
                                                ge = N;
                                                break
                                            }
                                            R = N
                                        } else ge = Se;
                                    Kr(e, 48, ge + 9 | 0, 9, 0)
                                } else {
                                    if (R = me ? B : he + 4 | 0, (0 | Se) > -1)
                                        for ($ = 0 == (0 | ke), W = Y = d + 9 | 0, y = 0 - _ | 0, N = d + 8 | 0, I = Se, k = he;;) {
                                            (0 | (C = 0 | Xr(0 | f[k >> 2], 0, Y))) == (0 | Y) ? (i[N >> 0] = 48, Te = N) : Te = C;
                                            do {
                                                if ((0 | k) == (0 | he)) {
                                                    if (C = Te + 1 | 0, Yr(e, Te, 1), $ & (0 | I) < 1) {
                                                        De = C;
                                                        break
                                                    }
                                                    Yr(e, 11520, 1), De = C
                                                } else {
                                                    if (Te >>> 0 <= d >>> 0) {
                                                        De = Te;
                                                        break
                                                    }
                                                    for (at(0 | d, 48, Te + y | 0), C = Te;;) {
                                                        if (!((g = C + -1 | 0) >>> 0 > d >>> 0)) {
                                                            De = g;
                                                            break
                                                        }
                                                        C = g
                                                    }
                                                }
                                            } while (0);
                                            if (Yr(e, De, (0 | I) > (0 | (K = W - De | 0)) ? K : I), !((k = k + 4 | 0) >>> 0 < R >>> 0 & (0 | (C = I - K | 0)) > -1)) {
                                                Ce = C;
                                                break
                                            }
                                            I = C
                                        } else Ce = Se;
                                    Kr(e, 48, Ce + 18 | 0, 18, 0), Yr(e, Ee, m - Ee | 0)
                                }
                                Kr(e, 32, t, T, 8192 ^ o), H = T
                            } else I = 0 != (32 & a | 0), Kr(e, 32, t, R = b + 3 | 0, -65537 & o), Yr(e, w, b), Yr(e, v != v | !1 ? I ? 11496 : 11500 : I ? 11488 : 11492, 3), Kr(e, 32, t, R, 8192 ^ o), H = R
                        } while (0);
                        return S = u, 0 | ((0 | H) < (0 | t) ? t : H)
                    }

                    function Qr(e) {
                        var r;
                        return e = +e, p[w >> 3] = e, r = 0 | f[w >> 2], M = 0 | f[w + 4 >> 2], 0 | r
                    }

                    function Zr(e, r) {
                        return + + function e(r, t) {
                            r = +r, t |= 0;
                            var n, i, o, a = 0,
                                u = 0,
                                l = 0;
                            switch (p[w >> 3] = r, 2047 & (o = 0 | ft(0 | (n = 0 | f[w >> 2]), 0 | (i = 0 | f[w + 4 >> 2]), 52))) {
                                case 0:
                                    0 != r ? (a = +e(0x10000000000000000 * r, t), u = (0 | f[t >> 2]) - 64 | 0) : (a = r, u = 0), f[t >> 2] = u, l = a;
                                    break;
                                case 2047:
                                    l = r;
                                    break;
                                default:
                                    f[t >> 2] = (2047 & o) - 1022, f[w >> 2] = n, f[w + 4 >> 2] = -2146435073 & i | 1071644672, l = +p[w >> 3]
                            }
                            return +l
                        }(e = +e, r |= 0)
                    }

                    function et(e, r, t) {
                        e |= 0, r |= 0;
                        var n = 0;
                        do {
                            if (e) {
                                if (r >>> 0 < 128) {
                                    i[e >> 0] = r, n = 1;
                                    break
                                }
                                if (!(0 | f[f[184] >> 2])) {
                                    if (57216 == (-128 & r | 0)) {
                                        i[e >> 0] = r, n = 1;
                                        break
                                    }
                                    f[153] = 84, n = -1;
                                    break
                                }
                                if (r >>> 0 < 2048) {
                                    i[e >> 0] = r >>> 6 | 192, i[e + 1 >> 0] = 63 & r | 128, n = 2;
                                    break
                                }
                                if (r >>> 0 < 55296 | 57344 == (-8192 & r | 0)) {
                                    i[e >> 0] = r >>> 12 | 224, i[e + 1 >> 0] = r >>> 6 & 63 | 128, i[e + 2 >> 0] = 63 & r | 128, n = 3;
                                    break
                                }
                                if ((r + -65536 | 0) >>> 0 < 1048576) {
                                    i[e >> 0] = r >>> 18 | 240, i[e + 1 >> 0] = r >>> 12 & 63 | 128, i[e + 2 >> 0] = r >>> 6 & 63 | 128, i[e + 3 >> 0] = 63 & r | 128, n = 4;
                                    break
                                }
                                f[153] = 84, n = -1;
                                break
                            }
                            n = 1
                        } while (0);
                        return 0 | n
                    }

                    function rt(e, r, t) {
                        e |= 0, r |= 0;
                        var n = 0,
                            o = 0,
                            a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0;
                        (o = 0 | f[(n = 16 + (t |= 0) | 0) >> 2]) ? (a = o, u = 5) : 0 | function(e) {
                            var r = 0,
                                t = 0,
                                n = 0;
                            return t = 0 | i[(r = 74 + (e |= 0) | 0) >> 0], i[r >> 0] = t + 255 | t, 8 & (t = 0 | f[e >> 2]) ? (f[e >> 2] = 32 | t, n = -1) : (f[e + 8 >> 2] = 0, f[e + 4 >> 2] = 0, r = 0 | f[e + 44 >> 2], f[e + 28 >> 2] = r, f[e + 20 >> 2] = r, f[e + 16 >> 2] = r + (0 | f[e + 48 >> 2]), n = 0), 0 | n
                        }(t) ? l = 0 : (a = 0 | f[n >> 2], u = 5);
                        e: do {
                            if (5 == (0 | u)) {
                                if (s = n = 0 | f[(o = t + 20 | 0) >> 2], (a - n | 0) >>> 0 < r >>> 0) {
                                    l = 0 | Dt[15 & f[t + 36 >> 2]](t, e, r);
                                    break
                                }
                                r: do {
                                    if ((0 | i[t + 75 >> 0]) > -1) {
                                        for (n = r;;) {
                                            if (!n) {
                                                c = 0, d = e, _ = r, h = s;
                                                break r
                                            }
                                            if (10 == (0 | i[e + (m = n + -1 | 0) >> 0])) break;
                                            n = m
                                        }
                                        if ((m = 0 | Dt[15 & f[t + 36 >> 2]](t, e, n)) >>> 0 < n >>> 0) {
                                            l = m;
                                            break e
                                        }
                                        c = n, d = e + n | 0, _ = r - n | 0, h = 0 | f[o >> 2]
                                    } else c = 0, d = e, _ = r, h = s
                                } while (0);
                                lt(0 | h, 0 | d, 0 | _), f[o >> 2] = (0 | f[o >> 2]) + _, l = c + _ | 0
                            }
                        } while (0);
                        return 0 | l
                    }

                    function tt(e) {
                        var r, t, n = 0,
                            i = 0,
                            o = 0,
                            a = 0,
                            u = 0;
                        return t = 28 + (e |= 0) | 0, (0 | f[(r = e + 20 | 0) >> 2]) >>> 0 > (0 | f[t >> 2]) >>> 0 && (Dt[15 & f[e + 36 >> 2]](e, 0, 0), 0 == (0 | f[r >> 2])) ? n = -1 : ((o = 0 | f[(i = e + 4 | 0) >> 2]) >>> 0 < (u = 0 | f[(a = e + 8 | 0) >> 2]) >>> 0 && Dt[15 & f[e + 40 >> 2]](e, o - u | 0, 1), f[e + 16 >> 2] = 0, f[t >> 2] = 0, f[r >> 2] = 0, f[a >> 2] = 0, f[i >> 2] = 0, n = 0), 0 | n
                    }

                    function nt(e, r, t) {
                        var n, i;
                        return e |= 0, r |= 0, t |= 0, n = S, S = S + 16 | 0, f[(i = n) >> 2] = t, t = 0 | We(e, r, i), S = n, 0 | t
                    }

                    function it(e, r, t, n) {
                        return 0 | (M = (r |= 0) - (n |= 0) - ((t |= 0) >>> 0 > (e |= 0) >>> 0 | 0) >>> 0, e - t >>> 0 | 0)
                    }

                    function ot(e, r, t, n) {
                        var i;
                        return 0 | (M = (r |= 0) + (n |= 0) + ((i = (e |= 0) + (t |= 0) >>> 0) >>> 0 < e >>> 0 | 0) >>> 0, 0 | i)
                    }

                    function at(e, r, t) {
                        r |= 0;
                        var n, o = 0,
                            a = 0,
                            u = 0;
                        if (n = (e |= 0) + (t |= 0) | 0, r &= 255, (0 | t) >= 67) {
                            for (; 3 & e;) i[e >> 0] = r, e = e + 1 | 0;
                            for (a = (o = -4 & n | 0) - 64 | 0, u = r | r << 8 | r << 16 | r << 24;
                                 (0 | e) <= (0 | a);) f[e >> 2] = u, f[e + 4 >> 2] = u, f[e + 8 >> 2] = u, f[e + 12 >> 2] = u, f[e + 16 >> 2] = u, f[e + 20 >> 2] = u, f[e + 24 >> 2] = u, f[e + 28 >> 2] = u, f[e + 32 >> 2] = u, f[e + 36 >> 2] = u, f[e + 40 >> 2] = u, f[e + 44 >> 2] = u, f[e + 48 >> 2] = u, f[e + 52 >> 2] = u, f[e + 56 >> 2] = u, f[e + 60 >> 2] = u, e = e + 64 | 0;
                            for (;
                                (0 | e) < (0 | o);) f[e >> 2] = u, e = e + 4 | 0
                        }
                        for (;
                            (0 | e) < (0 | n);) i[e >> 0] = r, e = e + 1 | 0;
                        return n - t | 0
                    }

                    function ut(e) {
                        return 0
                    }

                    function ft(e, r, t) {
                        return r |= 0, (0 | (t |= 0)) < 32 ? (M = r >>> t, (e |= 0) >>> t | (r & (1 << t) - 1) << 32 - t) : (M = 0, r >>> t - 32 | 0)
                    }

                    function lt(e, r, t) {
                        e |= 0, r |= 0;
                        var n, o, a = 0;
                        if ((0 | (t |= 0)) >= 8192) return 0 | H(0 | e, 0 | r, 0 | t);
                        if (n = 0 | e, o = e + t | 0, (3 & e) == (3 & r)) {
                            for (; 3 & e;) {
                                if (!t) return 0 | n;
                                i[e >> 0] = 0 | i[r >> 0], e = e + 1 | 0, r = r + 1 | 0, t = t - 1 | 0
                            }
                            for (t = (a = -4 & o | 0) - 64 | 0;
                                 (0 | e) <= (0 | t);) f[e >> 2] = f[r >> 2], f[e + 4 >> 2] = f[r + 4 >> 2], f[e + 8 >> 2] = f[r + 8 >> 2], f[e + 12 >> 2] = f[r + 12 >> 2], f[e + 16 >> 2] = f[r + 16 >> 2], f[e + 20 >> 2] = f[r + 20 >> 2], f[e + 24 >> 2] = f[r + 24 >> 2], f[e + 28 >> 2] = f[r + 28 >> 2], f[e + 32 >> 2] = f[r + 32 >> 2], f[e + 36 >> 2] = f[r + 36 >> 2], f[e + 40 >> 2] = f[r + 40 >> 2], f[e + 44 >> 2] = f[r + 44 >> 2], f[e + 48 >> 2] = f[r + 48 >> 2], f[e + 52 >> 2] = f[r + 52 >> 2], f[e + 56 >> 2] = f[r + 56 >> 2], f[e + 60 >> 2] = f[r + 60 >> 2], e = e + 64 | 0, r = r + 64 | 0;
                            for (;
                                (0 | e) < (0 | a);) f[e >> 2] = f[r >> 2], e = e + 4 | 0, r = r + 4 | 0
                        } else
                            for (a = o - 4 | 0;
                                 (0 | e) < (0 | a);) i[e >> 0] = 0 | i[r >> 0], i[e + 1 >> 0] = 0 | i[r + 1 >> 0], i[e + 2 >> 0] = 0 | i[r + 2 >> 0], i[e + 3 >> 0] = 0 | i[r + 3 >> 0], e = e + 4 | 0, r = r + 4 | 0;
                        for (;
                            (0 | e) < (0 | o);) i[e >> 0] = 0 | i[r >> 0], e = e + 1 | 0, r = r + 1 | 0;
                        return 0 | n
                    }

                    function st(e) {
                        var r = 0;
                        return (0 | (r = 0 | i[k + (255 & (e |= 0)) >> 0])) < 8 ? 0 | r : (0 | (r = 0 | i[k + (e >> 8 & 255) >> 0])) < 8 ? r + 8 | 0 : (0 | (r = 0 | i[k + (e >> 16 & 255) >> 0])) < 8 ? r + 16 | 0 : 24 + (0 | i[k + (e >>> 24) >> 0]) | 0
                    }

                    function ct(e, r, t, n, i) {
                        i |= 0;
                        var o, a = 0,
                            u = 0,
                            l = 0,
                            s = 0,
                            c = 0,
                            d = 0,
                            _ = 0,
                            h = 0,
                            m = 0,
                            p = 0,
                            v = 0,
                            b = 0,
                            w = 0,
                            S = 0,
                            k = 0,
                            E = 0,
                            A = 0,
                            L = 0,
                            g = 0,
                            T = 0,
                            D = 0,
                            C = 0,
                            P = 0,
                            R = 0,
                            O = 0,
                            B = 0;
                        if (a = e |= 0, o = t |= 0, c = s = n |= 0, !(l = u = r |= 0)) return d = 0 != (0 | i), c ? d ? (f[i >> 2] = 0 | e, f[i + 4 >> 2] = 0 & r, 0 | (M = _ = 0, h = 0)) : 0 | (M = _ = 0, h = 0) : (d && (f[i >> 2] = (a >>> 0) % (o >>> 0), f[i + 4 >> 2] = 0), 0 | (M = _ = 0, h = (a >>> 0) / (o >>> 0) >>> 0));
                        d = 0 == (0 | c);
                        do {
                            if (o) {
                                if (!d) {
                                    if ((m = (0 | y(0 | c)) - (0 | y(0 | l)) | 0) >>> 0 <= 31) {
                                        w = p = m + 1 | 0, S = a >>> (p >>> 0) & (b = m - 31 >> 31) | l << (v = 31 - m | 0), k = l >>> (p >>> 0) & b, E = 0, A = a << v;
                                        break
                                    }
                                    return i ? (f[i >> 2] = 0 | e, f[i + 4 >> 2] = u | 0 & r, 0 | (M = _ = 0, h = 0)) : 0 | (M = _ = 0, h = 0)
                                }
                                if ((v = o - 1 | 0) & o | 0) {
                                    w = b = 33 + (0 | y(0 | o)) - (0 | y(0 | l)) | 0, S = (m = 32 - b | 0) - 1 >> 31 & l >>> ((g = b - 32 | 0) >>> 0) | (l << m | a >>> (b >>> 0)) & (T = g >> 31), k = T & l >>> (b >>> 0), E = a << (p = 64 - b | 0) & (L = m >> 31), A = (l << p | a >>> (g >>> 0)) & L | a << m & b - 33 >> 31;
                                    break
                                }
                                return 0 | i && (f[i >> 2] = v & a, f[i + 4 >> 2] = 0), 1 == (0 | o) ? 0 | (M = _ = u | 0 & r, h = 0 | e) : (v = 0 | st(0 | o), 0 | (M = _ = l >>> (v >>> 0) | 0, h = l << 32 - v | a >>> (v >>> 0) | 0))
                            }
                            if (d) return 0 | i && (f[i >> 2] = (l >>> 0) % (o >>> 0), f[i + 4 >> 2] = 0), 0 | (M = _ = 0, (l >>> 0) / (o >>> 0) >>> 0);
                            if (!a) return 0 | i && (f[i >> 2] = 0, f[i + 4 >> 2] = (l >>> 0) % (c >>> 0)), 0 | (M = _ = 0, (l >>> 0) / (c >>> 0) >>> 0);
                            if (!((v = c - 1 | 0) & c)) return 0 | i && (f[i >> 2] = 0 | e, f[i + 4 >> 2] = v & l | 0 & r), _ = 0, h = l >>> ((0 | st(0 | c)) >>> 0), 0 | (M = _, h);
                            if ((v = (0 | y(0 | c)) - (0 | y(0 | l)) | 0) >>> 0 <= 30) {
                                w = b = v + 1 | 0, S = l << (m = 31 - v | 0) | a >>> (b >>> 0), k = l >>> (b >>> 0), E = 0, A = a << m;
                                break
                            }
                            return i ? (f[i >> 2] = 0 | e, f[i + 4 >> 2] = u | 0 & r, 0 | (M = _ = 0, h = 0)) : 0 | (M = _ = 0, h = 0)
                        } while (0);
                        if (w) {
                            n = 0 | ot(0 | (r = 0 | t), 0 | (t = s | 0 & n), -1, -1), s = M, u = A, A = E, E = k, k = S, S = w, w = 0;
                            do {
                                e = u, u = A >>> 31 | u << 1, A = w | A << 1, it(0 | n, 0 | s, 0 | (a = k << 1 | e >>> 31 | 0), 0 | (e = k >>> 31 | E << 1 | 0)), w = 1 & (c = (l = M) >> 31 | ((0 | l) < 0 ? -1 : 0) << 1), k = 0 | it(0 | a, 0 | e, c & r | 0, (((0 | l) < 0 ? -1 : 0) >> 31 | ((0 | l) < 0 ? -1 : 0) << 1) & t | 0), E = M, S = S - 1 | 0
                            } while (0 != (0 | S));
                            D = u, C = A, P = E, R = k, O = 0, B = w
                        } else D = A, C = E, P = k, R = S, O = 0, B = 0;
                        return w = C, C = 0, 0 | i && (f[i >> 2] = R, f[i + 4 >> 2] = P), 0 | (M = _ = (0 | w) >>> 31 | (D | C) << 1 | 0 & (C << 1 | w >>> 31) | O, -2 & (w << 1 | 0) | B)
                    }

                    function dt(e, r, t, n) {
                        return 0 | ct(e |= 0, r |= 0, t |= 0, n |= 0, 0)
                    }

                    function _t(e, r, t, n) {
                        var i, o;
                        return r |= 0, n |= 0, t = 0 | function(e, r) {
                            var t, n, i, o = 0;
                            return e = ((n = 0 | A(o = 65535 & (r |= 0), t = 65535 & (e |= 0))) >>> 16) + (0 | A(o, i = e >>> 16)) | 0, r = 0 | A(o = r >>> 16, t), 0 | (M = (e >>> 16) + (0 | A(o, i)) + (((65535 & e) + r | 0) >>> 16) | 0, e + r << 16 | 65535 & n | 0)
                        }(i = e |= 0, e = t |= 0), o = M, 0 | (M = (0 | A(r, e)) + (0 | A(n, i)) + o | 0 & o, 0 | t)
                    }

                    function ht(e) {
                        var r, t;
                        return (0 | (e = 15 + (e |= 0) & -16 | 0)) > 0 & (0 | (t = (r = 0 | f[b >> 2]) + e | 0)) < (0 | r) | (0 | t) < 0 ? (D(), x(12), -1) : (f[b >> 2] = t, (0 | t) > (0 | T()) && 0 == (0 | g()) ? (f[b >> 2] = r, x(12), -1) : 0 | r)
                    }

                    function mt(e, r, t) {
                        return e |= 0, (0 | (t |= 0)) < 32 ? (M = (r |= 0) << t | (e & (1 << t) - 1 << 32 - t) >>> 32 - t, e << t) : (M = e << t - 32, 0)
                    }

                    function pt(e, r, t) {
                        var n = 0;
                        if ((0 | (r |= 0)) < (0 | (e |= 0)) & (0 | e) < (r + (t |= 0) | 0)) {
                            for (n = e, r = r + t | 0, e = e + t | 0;
                                 (0 | t) > 0;) r = r - 1 | 0, t = t - 1 | 0, i[(e = e - 1 | 0) >> 0] = 0 | i[r >> 0];
                            e = n
                        } else lt(e, r, t);
                        return 0 | e
                    }

                    function vt(e, r, t, n) {
                        var i, o;
                        return i = S, S = S + 16 | 0, ct(e |= 0, r |= 0, t |= 0, n |= 0, o = 0 | i), S = i, 0 | (M = 0 | f[o + 4 >> 2], 0 | f[o >> 2])
                    }

                    function bt(e) {
                        return 0
                    }

                    function wt(e) {
                        return (255 & (e |= 0)) << 24 | (e >> 8 & 255) << 16 | (e >> 16 & 255) << 8 | e >>> 24 | 0
                    }

                    function St(e, r, t) {
                        return L(0), 0
                    }

                    function kt(e, r, t, n, i) {
                        L(1)
                    }

                    function Et(e, r) {
                        L(3)
                    }

                    function Mt(e, r, t, n, i, o) {
                        return L(4), 0
                    }

                    function At(e) {
                        return L(5), 0
                    }

                    function yt(e, r, t) {
                        L(6)
                    }

                    function Lt(e, r, t, n, i, o) {
                        L(9)
                    }

                    function gt(e, r) {
                        return L(10), 0
                    }

                    function Tt(e, r, t, n) {
                        L(11)
                    }
                    r._SDL_RWFromFile;
                    var Dt = [St, Oe, function(e, r, t) {
                            e |= 0, r |= 0, t |= 0;
                            var n, i, o, a = 0;
                            return n = S, S = S + 32 | 0, o = n + 20 | 0, f[(i = n) >> 2] = f[e + 60 >> 2], f[i + 4 >> 2] = 0, f[i + 8 >> 2] = r, f[i + 12 >> 2] = o, f[i + 16 >> 2] = t, (0 | Be(0 | q(140, 0 | i))) < 0 ? (f[o >> 2] = -1, a = -1) : a = 0 | f[o >> 2], S = n, 0 | a
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, o = 0;
                            return n = S, S = S + 32 | 0, o = n, f[36 + (e |= 0) >> 2] = 1, 0 == (64 & f[e >> 2] | 0) && (f[o >> 2] = f[e + 60 >> 2], f[o + 4 >> 2] = 21523, f[o + 8 >> 2] = n + 16, 0 | Y(54, 0 | o)) && (i[e + 75 >> 0] = -1), o = 0 | Oe(e, r, t), S = n, 0 | o
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, i, o, a, u, l, s, c = 0,
                                d = 0,
                                _ = 0,
                                h = 0;
                            if (!(c = 0 | f[(n = 104 + (e |= 0) | 0) >> 2])) return 0;
                            if (i = 0 | f[r + 36 >> 2], o = (0 | f[r + 16 >> 2]) + t | 0, (0 | t) <= 0) return 0;
                            for (a = i + 60 | 0, u = e + 8 | 0, l = e + 16 | 0, s = r + 52 | 0, d = t, t = c; c = 0 | f[a >> 2], _ = 0 | f[u >> 2], h = 0 | f[e >> 2], Mr(i, _ - c + (0 | f[l >> 2]) | 0, t + (0 | A(h, c - _ | 0)) | 0, h), !((0 | (h = d - (0 | Dt[15 & f[s >> 2]](r, o - d | 0, d)) | 0)) <= 0);) d = h, t = 0 | f[n >> 2];
                            return 0
                        }, function(e, r, t) {
                            e |= 0, t |= 0;
                            var n, i, o, a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0;
                            if (a = 0 | f[(r |= 0) >> 2], u = 0 | f[a + 28 >> 2], n = 0 | f[r + 16 >> 2], l = 0 | f[(i = a + 44 | 0) >> 2], o = u + (0 | A(l, n)) | 0, !(s = 0 | f[e + 104 >> 2])) {
                                if (!u) return 0;
                                if (u = 0 | f[e + 96 >> 2], !((0 | t) > 0)) return 0;
                                for (c = o, d = 0; at(0 | c, -1, 0 | u), (0 | (d = d + 1 | 0)) != (0 | t);) c = c + l | 0;
                                return 0
                            }
                            if (l = 0 | f[a + 16 >> 2], a = 0 | f[(c = a + 32 | 0) >> 2], t = 0 | f[e >> 2], d = 0 | f[e + 16 >> 2], r = 0 | f[(e = r + 36 | 0) >> 2], !((0 | d) > 0)) return 0;
                            _ = 0, h = d, m = s;
                            do {
                                s = 0 | Mr(r, h, m, t), m = m + (0 | A(s, t)) | 0, h = h - s | 0, _ = (0 | Ar(r)) + _ | 0
                            } while ((0 | h) > 0);
                            return (0 | _) <= 0 ? 0 : (yr(h = l + (0 | A(a, n)) | 0, 0 | f[c >> 2], o, 0 | f[i >> 2], 0 | f[52 + (0 | f[e >> 2]) >> 2], _, 1), 0)
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, i, o, a = 0,
                                u = 0,
                                l = 0,
                                s = 0;
                            if (t = 0 | f[104 + (e |= 0) >> 2], a = 0 | f[r >> 2], r = 0 | f[e + 12 >> 2], n = 0 | f[e + 16 >> 2], i = 0 | f[a + 28 >> 2], a = 0 | f[(o = a + 44 | 0) >> 2], u = i + (0 | A(a, 0 | f[e + 8 >> 2])) | 0, !t) {
                                if (!((0 | n) > 0 & 0 != (0 | i))) return 0;
                                for (l = u, s = 0; at(0 | l, -1, 0 | r), (0 | (s = s + 1 | 0)) != (0 | n);) l = l + a | 0;
                                return 0
                            }
                            if ((0 | n) <= 0) return 0;
                            for (a = u, u = t, t = 0; lt(0 | a, 0 | u, 0 | r), (0 | (t = t + 1 | 0)) != (0 | n);) a = a + (0 | f[o >> 2]) | 0, u = u + (0 | f[e >> 2]) | 0;
                            return 0
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, i, o, a, u, l, s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0;
                            return (t = 0 | f[104 + (e |= 0) >> 2]) ? (n = 0 | f[e + 12 >> 2], i = 0 | f[r >> 2], a = 1 & (o = 4 == (0 | (r = 0 | f[i >> 2])) | 9 == (0 | r)), u = 0 | f[e + 8 >> 2], l = 0 | f[e + 16 >> 2], 0 | f[e + 56 >> 2] ? (u ? (_ = l, h = u + -1 | 0, m = t + (0 - (0 | f[e >> 2])) | 0) : (_ = l + -1 | 0, h = 0, m = t), s = (0 | (p = u + (t = 0 | f[e + 84 >> 2]) + l | 0)) == (0 | f[e + 88 >> 2]) ? p - (t + h) | 0 : _, c = h, d = m) : (s = l, c = u, d = t), h = 0 | f[(m = i + 20 | 0) >> 2], _ = (0 | f[i + 16 >> 2]) + (0 | A(h, c)) | 0, (r + -7 | 0) >>> 0 < 4 & 0 != (0 | Ot[7 & f[2886]](d, 0 | f[e >> 2], n, s, _ + (o ? 0 : 3) | 0, h)) ? (Ct[31 & f[2884]](_, a, n, s, 0 | f[m >> 2]), 0) : 0) : 0
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, o, a, u, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0;
                            if (n = 20 + (l = 0 | f[(e |= 0) >> 2]) | 0, o = (0 | f[l + 16 >> 2]) + (0 | A(0 | f[n >> 2], r)) | 0, e = 0 | f[(r = e + 36 | 0) >> 2], a = 0 | f[e + 52 >> 2], u = ((0 | f[l >> 2]) - 7 | 0) >>> 0 < 4, (0 | f[e + 64 >> 2]) >= (0 | f[e + 56 >> 2])) return 0;
                            if ((0 | a) <= 0) {
                                for (l = 0, d = e;;) {
                                    if (!((0 | l) < (0 | t) && (0 | f[d + 24 >> 2]) < 1)) {
                                        c = l, _ = 12;
                                        break
                                    }
                                    if (Lr(d), h = l + 1 | 0, d = 0 | f[r >> 2], (0 | f[d + 64 >> 2]) >= (0 | f[d + 56 >> 2])) {
                                        c = h, _ = 12;
                                        break
                                    }
                                    l = h
                                }
                                if (12 == (0 | _)) return 0 | c
                            }
                            for (_ = 15, l = 0, d = o + 1 | 0, h = e;;) {
                                if (!((0 | l) < (0 | t) && (0 | f[h + 24 >> 2]) < 1)) {
                                    m = _, p = l;
                                    break
                                }
                                Lr(h), e = 0, v = _;
                                do {
                                    b = (0 | s[(0 | f[68 + (0 | f[r >> 2]) >> 2]) + e >> 0]) >>> 4, i[(w = d + (e << 1) | 0) >> 0] = -16 & i[w >> 0] & 255 | b, v &= b, e = e + 1 | 0
                                } while ((0 | e) != (0 | a));
                                if (e = l + 1 | 0, h = 0 | f[r >> 2], (0 | f[h + 64 >> 2]) >= (0 | f[h + 56 >> 2])) {
                                    m = v, p = e;
                                    break
                                }
                                _ = v, l = e, d = d + (0 | f[n >> 2]) | 0
                            }
                            return u & 15 != (0 | m) ? (Ut[31 & f[2885]](o, a, p, 0 | f[n >> 2]), 0 | (c = p)) : 0 | (c = p)
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, i, o, a, u, l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0;
                            if (l = 0 | f[(e |= 0) >> 2], s = 0 | f[(n = l + 20 | 0) >> 2], i = (0 | f[l + 16 >> 2]) + (0 | A(s, r)) | 0, o = 1 & (l = 4 == (0 | (r = 0 | f[l >> 2])) | 9 == (0 | r)), a = (r + -7 | 0) >>> 0 < 4, e = 0 | f[(r = e + 36 | 0) >> 2], u = 0 | f[e + 52 >> 2], (0 | f[e + 64 >> 2]) >= (0 | f[e + 56 >> 2])) return 0;
                            for (c = 0, d = 0, _ = i + (l ? 0 : 3) | 0, l = e, e = s;;) {
                                if (!((0 | d) < (0 | t) && (0 | f[l + 24 >> 2]) < 1)) {
                                    h = c, m = d, p = e;
                                    break
                                }
                                if (Lr(l), s = 0 | Ot[7 & f[2886]](0 | f[68 + (0 | f[r >> 2]) >> 2], 0, u, 1, _, 0) | c, v = 0 | f[n >> 2], b = d + 1 | 0, l = 0 | f[r >> 2], (0 | f[l + 64 >> 2]) >= (0 | f[l + 56 >> 2])) {
                                    h = s, m = b, p = v;
                                    break
                                }
                                c = s, d = b, _ = _ + v | 0, e = v
                            }
                            return a & 0 != (0 | h) ? (Ct[31 & f[2884]](i, o, u, m, p), 0 | m) : 0 | m
                        }, function(e, r, t) {
                            r |= 0, t |= 0;
                            var n, o, a = 0,
                                u = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0;
                            if (!(t = 0 | f[104 + (e |= 0) >> 2])) return 0;
                            if (n = 0 | f[e + 12 >> 2], a = 0 | f[r >> 2], r = 0 | f[a >> 2], o = 0 | f[e + 8 >> 2], u = 0 | f[e + 16 >> 2], 0 | f[e + 56 >> 2] ? (o ? (_ = o + -1 | 0, h = u, m = t + (0 - (0 | f[e >> 2])) | 0) : (_ = 0, h = u + -1 | 0, m = t), l = _, c = (0 | (p = o + (t = 0 | f[e + 84 >> 2]) + u | 0)) == (0 | f[e + 88 >> 2]) ? p - (t + _) | 0 : h, d = m) : (l = o, c = u, d = t), m = a + 20 | 0, h = (0 | f[a + 16 >> 2]) + (0 | A(0 | f[m >> 2], l)) | 0, (0 | c) <= 0) return 0;
                            if ((0 | n) <= 0) return 0;
                            for (l = 0, a = 15, _ = h + 1 | 0, t = d;;) {
                                d = 0, v = a;
                                do {
                                    p = (0 | s[t + d >> 0]) >>> 4, i[(u = _ + (d << 1) | 0) >> 0] = -16 & i[u >> 0] & 255 | p, v &= p, d = d + 1 | 0
                                } while ((0 | d) != (0 | n));
                                if (b = 0 | f[m >> 2], (0 | (l = l + 1 | 0)) == (0 | c)) break;
                                a = v, _ = _ + b | 0, t = t + (0 | f[e >> 2]) | 0
                            }
                            return (r + -7 | 0) >>> 0 < 4 & 15 != (0 | v) ? (Ut[31 & f[2885]](h, n, c, b), 0) : 0
                        }, St, St, St, St, St],
                        Ct = [kt, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0;
                            if (i[(o |= 0) >> 0] = 0 | i[e >> 0], f = e + 1 | 0, a = o + 1 | 0, u = r + -1 | 0, l = (0 | r) > 1) {
                                r = 0;
                                do {
                                    i[a + r >> 0] = (0 | s[f + r >> 0]) - (0 | s[e + r >> 0]), r = r + 1 | 0
                                } while ((0 | r) != (0 | u))
                            }
                            if (r = e + n | 0, e = o + n | 0, !((0 | t) <= 1))
                                if (o = 0 - n | 0, l)
                                    for (c = 1, d = r, _ = e;;) {
                                        i[_ >> 0] = (0 | s[d >> 0]) - (0 | s[d + o >> 0]), r = d + 1 | 0, f = _ + 1 | 0, l = 0;
                                        do {
                                            i[f + l >> 0] = (0 | s[r + l >> 0]) - (0 | s[d + l >> 0]), l = l + 1 | 0
                                        } while ((0 | l) != (0 | u));
                                        if ((0 | (c = c + 1 | 0)) == (0 | t)) break;
                                        d = d + n | 0, _ = _ + n | 0
                                    } else
                                    for (l = 1, f = r, r = e; i[r >> 0] = (0 | s[f >> 0]) - (0 | s[f + o >> 0]), (0 | (l = l + 1 | 0)) != (0 | t);) f = f + n | 0, r = r + n | 0
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a, u, f, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0;
                            if (i[(o |= 0) >> 0] = 0 | i[e >> 0], a = e + 1 | 0, u = o + 1 | 0, f = r + -1 | 0, (0 | r) > 1) {
                                l = 0;
                                do {
                                    i[u + l >> 0] = (0 | s[a + l >> 0]) - (0 | s[e + l >> 0]), l = l + 1 | 0
                                } while ((0 | l) != (0 | f))
                            }
                            if ((0 | r) > 0 & (0 | t) > 1) {
                                c = e, d = 1, _ = o;
                                do {
                                    o = c, c = c + n | 0, _ = _ + n | 0, e = 0;
                                    do {
                                        i[_ + e >> 0] = (0 | s[c + e >> 0]) - (0 | s[o + e >> 0]), e = e + 1 | 0
                                    } while ((0 | e) != (0 | r));
                                    d = d + 1 | 0
                                } while ((0 | d) != (0 | t))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0;
                            if (i[(o |= 0) >> 0] = 0 | i[e >> 0], a = e + 1 | 0, u = o + 1 | 0, f = r + -1 | 0, l = (0 | r) > 1) {
                                c = 0;
                                do {
                                    i[u + c >> 0] = (0 | s[a + c >> 0]) - (0 | s[e + c >> 0]), c = c + 1 | 0
                                } while ((0 | c) != (0 | f))
                            }
                            if (f = e + n | 0, e = o + n | 0, !((0 | t) <= 1))
                                if (o = 0 - n | 0, l)
                                    for (d = 1, _ = f, h = e;;) {
                                        i[h >> 0] = (0 | s[_ >> 0]) - (0 | s[_ + o >> 0]), f = 1;
                                        do {
                                            l = (0 | s[_ + (c = f - n | 0) >> 0]) + (0 | s[_ + (f + -1) >> 0]) - (0 | s[_ + (c + -1) >> 0]) | 0, i[h + f >> 0] = (0 | s[_ + f >> 0]) - (l >>> 0 < 256 ? l : 255 + (l >>> 31) | 0), f = f + 1 | 0
                                        } while ((0 | f) != (0 | r));
                                        if ((0 | (d = d + 1 | 0)) == (0 | t)) break;
                                        _ = _ + n | 0, h = h + n | 0
                                    } else
                                    for (l = 1, c = f, f = e; i[f >> 0] = (0 | s[c >> 0]) - (0 | s[c + o >> 0]), (0 | (l = l + 1 | 0)) != (0 | t);) c = c + n | 0, f = f + n | 0
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, o |= 0;
                            var a, u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0;
                            if (!((0 | (n |= 0)) <= 0) && (r = 1 & (u = 0 != (0 | r)), a = u ? 0 : 3, (0 | t) > 0))
                                for (f = e, l = n;;) {
                                    n = l, l = l + -1 | 0, e = f + r | 0, u = f + a | 0, c = 0;
                                    do {
                                        (_ = 0 | i[u + (d = c << 2) >> 0]) << 24 >> 24 != -1 && (h = 32897 * (255 & _) | 0, m = (0 | A(0 | s[(_ = e + d | 0) >> 0], h)) >>> 23 & 255, i[_ >> 0] = m, _ = (0 | A(0 | s[(m = e + (1 | d) | 0) >> 0], h)) >>> 23 & 255, i[m >> 0] = _, d = (0 | A(0 | s[(_ = e + (2 | d) | 0) >> 0], h)) >>> 23 & 255, i[_ >> 0] = d), c = c + 1 | 0
                                    } while ((0 | c) != (0 | t));
                                    if ((0 | n) <= 1) break;
                                    f = f + o | 0
                                }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = n + (a << 2) | 0, f = 0 | s[r + a >> 0], l = 0 | s[t + a >> 0], d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 14234 + ((26149 * l | 0) >>> 8) | 0, i[u >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + 8708 - ((6419 * f | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[u + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + -17685 + ((33050 * f | 0) >>> 8) | 0, i[u + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, i[u + 3 >> 0] = -1, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = n + (a << 2) | 0, f = 0 | s[r + a >> 0], l = 0 | s[t + a >> 0], d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 17685 + ((33050 * f | 0) >>> 8) | 0, i[u >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + 8708 - ((6419 * f | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[u + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + -14234 + ((26149 * l | 0) >>> 8) | 0, i[u + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, i[u + 3 >> 0] = -1, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = 0 | s[r + a >> 0], f = 0 | s[t + a >> 0], l = n + (3 * a | 0) | 0, d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 14234 + ((26149 * f | 0) >>> 8) | 0, i[l >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + 8708 - ((6419 * u | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0, i[l + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + -17685 + ((33050 * u | 0) >>> 8) | 0, i[l + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = 0 | s[r + a >> 0], f = 0 | s[t + a >> 0], l = n + (3 * a | 0) | 0, d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 17685 + ((33050 * u | 0) >>> 8) | 0, i[l >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + 8708 - ((6419 * u | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0, i[l + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = c + -14234 + ((26149 * f | 0) >>> 8) | 0, i[l + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                s = 0,
                                c = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = 0 | i[e + a >> 0], f = 0 | i[r + a >> 0], l = 0 | i[t + a >> 0], i[(s = n + (a << 2) | 0) >> 0] = -1, c = 255 & f, f = 255 & l, u = (l = (19077 * (255 & u) | 0) >>> 8) - 14234 + ((26149 * f | 0) >>> 8) | 0, i[s + 1 >> 0] = u >>> 0 < 16384 ? u >>> 6 : 255 + (u >>> 31) | 0, u = l + 8708 - ((6419 * c | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0, i[s + 2 >> 0] = u >>> 0 < 16384 ? u >>> 6 : 255 + (u >>> 31) | 0, u = l + -17685 + ((33050 * c | 0) >>> 8) | 0, i[s + 3 >> 0] = u >>> 0 < 16384 ? u >>> 6 : 255 + (u >>> 31) | 0, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = 0 | s[r + a >> 0], f = 0 | s[t + a >> 0], l = n + (a << 1) | 0, d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 14234 + ((26149 * f | 0) >>> 8) | 0, _ = c + 8708 - ((6419 * u | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0, f = c + -17685 + ((33050 * u | 0) >>> 8) | 0, i[l >> 0] = (_ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >> 31 & 3841) | 0) >>> 4 | 240 & (d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0), i[l + 1 >> 0] = 15 | (f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0), a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, n |= 0;
                            var a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0;
                            if ((0 | (o |= 0)) > 0) {
                                a = 0;
                                do {
                                    u = 0 | s[r + a >> 0], f = 0 | s[t + a >> 0], l = n + (a << 1) | 0, d = (c = (19077 * (0 | s[e + a >> 0]) | 0) >>> 8) - 14234 + ((26149 * f | 0) >>> 8) | 0, f = (_ = c + 8708 - ((6419 * u | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0) >>> 0 < 16384 ? _ >> 6 : 255 + (_ >> 31 & -255) | 0, _ = c + -17685 + ((33050 * u | 0) >>> 8) | 0, i[l >> 0] = f >>> 5 | 248 & (d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0), i[l + 1 >> 0] = f << 3 & 224 | (_ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >> 31 & 1793) | 0) >>> 3, a = a + 1 | 0
                                } while ((0 | a) != (0 | o))
                            }
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0;
                            if (a = (n |= 0) + (3 * (u = -2 & (o |= 0)) | 0) | 0, u)
                                for (u = r, r = n, n = t, t = e;;) {
                                    if (e = 0 | s[u >> 0], _ = 0 | s[n >> 0], m = (h = (19077 * (0 | s[t >> 0]) | 0) >>> 8) - 14234 + ((26149 * _ | 0) >>> 8) | 0, i[r >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = h + 8708 - ((6419 * e | 0) >>> 8) - ((13320 * _ | 0) >>> 8) | 0, i[r + 1 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = h + -17685 + ((33050 * e | 0) >>> 8) | 0, i[r + 2 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = 0 | s[u >> 0], e = 0 | s[n >> 0], _ = (h = (19077 * (0 | s[t + 1 >> 0]) | 0) >>> 8) - 14234 + ((26149 * e | 0) >>> 8) | 0, i[r + 3 >> 0] = _ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >>> 31) | 0, _ = h + 8708 - ((6419 * m | 0) >>> 8) - ((13320 * e | 0) >>> 8) | 0, i[r + 4 >> 0] = _ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >>> 31) | 0, _ = h + -17685 + ((33050 * m | 0) >>> 8) | 0, i[r + 5 >> 0] = _ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >>> 31) | 0, _ = t + 2 | 0, m = u + 1 | 0, h = n + 1 | 0, (0 | (r = r + 6 | 0)) == (0 | a)) {
                                        f = _, l = m, c = a, d = h;
                                        break
                                    }
                                    u = m, n = h, t = _
                                } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 14234 + ((26149 * l | 0) >>> 8) | 0, i[c >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[c + 1 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + -17685 + ((33050 * o | 0) >>> 8) | 0, i[c + 2 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0;
                            if (u = (n |= 0) + (a = (o |= 0) << 2 & -8) | 0, a) {
                                for (h = 2 + ((_ = a + -8 | 0) >>> 2) | 0, _ = r + (m = 1 + (_ >>> 3) | 0) | 0, p = r, r = n, v = t, b = e; w = 0 | s[p >> 0], S = 0 | s[v >> 0], E = (k = (19077 * (0 | s[b >> 0]) | 0) >>> 8) - 14234 + ((26149 * S | 0) >>> 8) | 0, i[r >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = k + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0, i[r + 1 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = k + -17685 + ((33050 * w | 0) >>> 8) | 0, i[r + 2 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, i[r + 3 >> 0] = -1, E = 0 | s[p >> 0], w = 0 | s[v >> 0], S = (k = (19077 * (0 | s[b + 1 >> 0]) | 0) >>> 8) - 14234 + ((26149 * w | 0) >>> 8) | 0, i[r + 4 >> 0] = S >>> 0 < 16384 ? S >>> 6 : 255 + (S >>> 31) | 0, S = k + 8708 - ((6419 * E | 0) >>> 8) - ((13320 * w | 0) >>> 8) | 0, i[r + 5 >> 0] = S >>> 0 < 16384 ? S >>> 6 : 255 + (S >>> 31) | 0, S = k + -17685 + ((33050 * E | 0) >>> 8) | 0, i[r + 6 >> 0] = S >>> 0 < 16384 ? S >>> 6 : 255 + (S >>> 31) | 0, i[r + 7 >> 0] = -1, (0 | (r = r + 8 | 0)) != (0 | u);) p = p + 1 | 0, v = v + 1 | 0, b = b + 2 | 0;
                                f = e + h | 0, l = _, c = n + a | 0, d = t + m | 0
                            } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 14234 + ((26149 * l | 0) >>> 8) | 0, i[c >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[c + 1 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + -17685 + ((33050 * o | 0) >>> 8) | 0, i[c + 2 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, i[c + 3 >> 0] = -1)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0;
                            if (a = (n |= 0) + (3 * (u = -2 & (o |= 0)) | 0) | 0, u)
                                for (u = r, r = n, n = t, t = e;;) {
                                    if (e = 0 | s[u >> 0], _ = 0 | s[n >> 0], m = (h = (19077 * (0 | s[t >> 0]) | 0) >>> 8) - 17685 + ((33050 * e | 0) >>> 8) | 0, i[r >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = h + 8708 - ((6419 * e | 0) >>> 8) - ((13320 * _ | 0) >>> 8) | 0, i[r + 1 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = h + -14234 + ((26149 * _ | 0) >>> 8) | 0, i[r + 2 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = 0 | s[u >> 0], _ = 0 | s[n >> 0], e = (h = (19077 * (0 | s[t + 1 >> 0]) | 0) >>> 8) - 17685 + ((33050 * m | 0) >>> 8) | 0, i[r + 3 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = h + 8708 - ((6419 * m | 0) >>> 8) - ((13320 * _ | 0) >>> 8) | 0, i[r + 4 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = h + -14234 + ((26149 * _ | 0) >>> 8) | 0, i[r + 5 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = t + 2 | 0, _ = u + 1 | 0, h = n + 1 | 0, (0 | (r = r + 6 | 0)) == (0 | a)) {
                                        f = e, l = _, c = a, d = h;
                                        break
                                    }
                                    u = _, n = h, t = e
                                } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 17685 + ((33050 * o | 0) >>> 8) | 0, i[c >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[c + 1 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + -14234 + ((26149 * l | 0) >>> 8) | 0, i[c + 2 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0;
                            if (u = (n |= 0) + (a = (o |= 0) << 2 & -8) | 0, a) {
                                for (h = 2 + ((_ = a + -8 | 0) >>> 2) | 0, _ = r + (m = 1 + (_ >>> 3) | 0) | 0, p = r, r = n, v = t, b = e; w = 0 | s[p >> 0], S = 0 | s[v >> 0], E = (k = (19077 * (0 | s[b >> 0]) | 0) >>> 8) - 17685 + ((33050 * w | 0) >>> 8) | 0, i[r >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = k + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0, i[r + 1 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = k + -14234 + ((26149 * S | 0) >>> 8) | 0, i[r + 2 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, i[r + 3 >> 0] = -1, E = 0 | s[p >> 0], S = 0 | s[v >> 0], w = (k = (19077 * (0 | s[b + 1 >> 0]) | 0) >>> 8) - 17685 + ((33050 * E | 0) >>> 8) | 0, i[r + 4 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = k + 8708 - ((6419 * E | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0, i[r + 5 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = k + -14234 + ((26149 * S | 0) >>> 8) | 0, i[r + 6 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, i[r + 7 >> 0] = -1, (0 | (r = r + 8 | 0)) != (0 | u);) p = p + 1 | 0, v = v + 1 | 0, b = b + 2 | 0;
                                f = e + h | 0, l = _, c = n + a | 0, d = t + m | 0
                            } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 17685 + ((33050 * o | 0) >>> 8) | 0, i[c >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, i[c + 1 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, f = d + -14234 + ((26149 * l | 0) >>> 8) | 0, i[c + 2 >> 0] = f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0, i[c + 3 >> 0] = -1)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u, f = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0;
                            if (u = (n |= 0) + (a = (o |= 0) << 2 & -8) | 0, a) {
                                for (_ = 2 + ((d = a + -8 | 0) >>> 2) | 0, d = r + (h = 1 + (d >>> 3) | 0) | 0, m = r, r = n, p = t, v = e; b = 0 | i[v >> 0], w = 0 | i[m >> 0], S = 0 | i[p >> 0], i[r >> 0] = -1, k = 255 & w, w = 255 & S, b = (S = (19077 * (255 & b) | 0) >>> 8) - 14234 + ((26149 * w | 0) >>> 8) | 0, i[r + 1 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = S + 8708 - ((6419 * k | 0) >>> 8) - ((13320 * w | 0) >>> 8) | 0, i[r + 2 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = S + -17685 + ((33050 * k | 0) >>> 8) | 0, i[r + 3 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = 0 | i[v + 1 >> 0], k = 0 | i[m >> 0], S = 0 | i[p >> 0], i[r + 4 >> 0] = -1, w = 255 & k, k = 255 & S, b = (S = (19077 * (255 & b) | 0) >>> 8) - 14234 + ((26149 * k | 0) >>> 8) | 0, i[r + 5 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = S + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * k | 0) >>> 8) | 0, i[r + 6 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = S + -17685 + ((33050 * w | 0) >>> 8) | 0, i[r + 7 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, (0 | (r = r + 8 | 0)) != (0 | u);) m = m + 1 | 0, p = p + 1 | 0, v = v + 2 | 0;
                                f = e + _ | 0, l = d, s = n + a | 0, c = t + h | 0
                            } else f = e, l = r, s = n, c = t;
                            1 & o && (o = 0 | i[f >> 0], f = 0 | i[l >> 0], l = 0 | i[c >> 0], i[s >> 0] = -1, c = 255 & f, f = 255 & l, o = (l = (19077 * (255 & o) | 0) >>> 8) - 14234 + ((26149 * f | 0) >>> 8) | 0, i[s + 1 >> 0] = o >>> 0 < 16384 ? o >>> 6 : 255 + (o >>> 31) | 0, o = l + 8708 - ((6419 * c | 0) >>> 8) - ((13320 * f | 0) >>> 8) | 0, i[s + 2 >> 0] = o >>> 0 < 16384 ? o >>> 6 : 255 + (o >>> 31) | 0, o = l + -17685 + ((33050 * c | 0) >>> 8) | 0, i[s + 3 >> 0] = o >>> 0 < 16384 ? o >>> 6 : 255 + (o >>> 31) | 0)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0;
                            if (u = (n |= 0) + (a = (o |= 0) << 1 & -4) | 0, a) {
                                for (h = 2 + ((_ = a + -4 | 0) >>> 1) | 0, _ = r + (m = 1 + (_ >>> 2) | 0) | 0, p = r, r = n, v = t, b = e; w = 0 | s[p >> 0], S = 0 | s[v >> 0], E = (k = (19077 * (0 | s[b >> 0]) | 0) >>> 8) - 14234 + ((26149 * S | 0) >>> 8) | 0, M = k + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0, S = k + -17685 + ((33050 * w | 0) >>> 8) | 0, i[r >> 0] = (M >>> 0 < 16384 ? M >>> 6 : 255 + (M >> 31 & 3841) | 0) >>> 4 | 240 & (E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0), i[r + 1 >> 0] = 15 | (S >>> 0 < 16384 ? S >>> 6 : 255 + (S >>> 31) | 0), S = 0 | s[p >> 0], E = 0 | s[v >> 0], w = (M = (19077 * (0 | s[b + 1 >> 0]) | 0) >>> 8) - 14234 + ((26149 * E | 0) >>> 8) | 0, k = M + 8708 - ((6419 * S | 0) >>> 8) - ((13320 * E | 0) >>> 8) | 0, E = M + -17685 + ((33050 * S | 0) >>> 8) | 0, i[r + 2 >> 0] = (k >>> 0 < 16384 ? k >>> 6 : 255 + (k >> 31 & 3841) | 0) >>> 4 | 240 & (w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0), i[r + 3 >> 0] = 15 | (E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0), (0 | (r = r + 4 | 0)) != (0 | u);) p = p + 1 | 0, v = v + 1 | 0, b = b + 2 | 0;
                                f = e + h | 0, l = _, c = n + a | 0, d = t + m | 0
                            } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 14234 + ((26149 * l | 0) >>> 8) | 0, m = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0, l = d + -17685 + ((33050 * o | 0) >>> 8) | 0, i[c >> 0] = (m >>> 0 < 16384 ? m >>> 6 : 255 + (m >> 31 & 3841) | 0) >>> 4 | 240 & (f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0), i[c + 1 >> 0] = 15 | (l >>> 0 < 16384 ? l >>> 6 : 255 + (l >>> 31) | 0))
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a, u, f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0;
                            if (u = (n |= 0) + (a = (o |= 0) << 1 & -4) | 0, a) {
                                for (h = 2 + ((_ = a + -4 | 0) >>> 1) | 0, _ = r + (m = 1 + (_ >>> 2) | 0) | 0, p = r, r = n, v = t, b = e; w = 0 | s[p >> 0], S = 0 | s[v >> 0], E = (k = (19077 * (0 | s[b >> 0]) | 0) >>> 8) - 14234 + ((26149 * S | 0) >>> 8) | 0, S = (M = k + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0) >>> 0 < 16384 ? M >> 6 : 255 + (M >> 31 & -255) | 0, M = k + -17685 + ((33050 * w | 0) >>> 8) | 0, i[r >> 0] = S >>> 5 | 248 & (E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0), i[r + 1 >> 0] = S << 3 & 224 | (M >>> 0 < 16384 ? M >>> 6 : 255 + (M >> 31 & 1793) | 0) >>> 3, M = 0 | s[p >> 0], S = 0 | s[v >> 0], w = (E = (19077 * (0 | s[b + 1 >> 0]) | 0) >>> 8) - 14234 + ((26149 * S | 0) >>> 8) | 0, S = (k = E + 8708 - ((6419 * M | 0) >>> 8) - ((13320 * S | 0) >>> 8) | 0) >>> 0 < 16384 ? k >> 6 : 255 + (k >> 31 & -255) | 0, k = E + -17685 + ((33050 * M | 0) >>> 8) | 0, i[r + 2 >> 0] = S >>> 5 | 248 & (w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0), i[r + 3 >> 0] = S << 3 & 224 | (k >>> 0 < 16384 ? k >>> 6 : 255 + (k >> 31 & 1793) | 0) >>> 3, (0 | (r = r + 4 | 0)) != (0 | u);) p = p + 1 | 0, v = v + 1 | 0, b = b + 2 | 0;
                                f = e + h | 0, l = _, c = n + a | 0, d = t + m | 0
                            } else f = e, l = r, c = n, d = t;
                            1 & o && (o = 0 | s[l >> 0], l = 0 | s[d >> 0], f = (d = (19077 * (0 | s[f >> 0]) | 0) >>> 8) - 14234 + ((26149 * l | 0) >>> 8) | 0, l = (m = d + 8708 - ((6419 * o | 0) >>> 8) - ((13320 * l | 0) >>> 8) | 0) >>> 0 < 16384 ? m >> 6 : 255 + (m >> 31 & -255) | 0, m = d + -17685 + ((33050 * o | 0) >>> 8) | 0, i[c >> 0] = l >>> 5 | 248 & (f >>> 0 < 16384 ? f >>> 6 : 255 + (f >>> 31) | 0), i[c + 1 >> 0] = l << 3 & 224 | (m >>> 0 < 16384 ? m >>> 6 : 255 + (m >> 31 & 1793) | 0) >>> 3)
                        }, function(e, r, t, n, i) {
                            ue(e |= 0, r |= 0, 1, 16, t |= 0, n |= 0, i |= 0)
                        }, function(e, r, t, n, i) {
                            var o, a;
                            oe(a = (e |= 0) + (o = (r |= 0) << 2) | 0, r, 1, 16, t |= 0, n |= 0, i |= 0), oe(e = a + o | 0, r, 1, 16, t, n, i), oe(e + o | 0, r, 1, 16, t, n, i)
                        }, function(e, r, t, n, i) {
                            ue(e |= 0, 1, r |= 0, 16, t |= 0, n |= 0, i |= 0)
                        }, function(e, r, t, n, i) {
                            oe(4 + (e |= 0) | 0, 1, r |= 0, 16, t |= 0, n |= 0, i |= 0), oe(e + 8 | 0, 1, r, 16, t, n, i), oe(e + 12 | 0, 1, r, 16, t, n, i)
                        }, function(e, r, t, n, o) {
                            e |= 0, r |= 0, t |= 0, o |= 0;
                            var a = 0,
                                u = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0;
                            if ((0 | (a = (n |= 0) >> 1)) > 0)
                                if (o) {
                                    u = 0;
                                    do {
                                        m = 0 | f[e + ((_ = u << 1) << 2) >> 2], c = ((h = 0 | f[e + ((1 | _) << 2) >> 2]) >>> 7 & 510) + (m >>> 7 & 510) | 0, d = (h << 1 & 510) + (m << 1 & 510) | 0, h = (m = 33685504 + (0 | A(_ = (h >>> 15 & 510) + (m >>> 15 & 510) | 0, -9719)) + (0 | A(c, -19081)) + (28800 * d | 0) | 0) >> 18, d = (l = 33685504 + (28800 * _ | 0) + (0 | A(c, -24116)) + (0 | A(d, -4684)) | 0) >> 18, i[r + u >> 0] = h >>> 0 < 256 ? h : 255 + (m >>> 31) | 0, i[t + u >> 0] = d >>> 0 < 256 ? d : 255 + (l >>> 31) | 0, u = u + 1 | 0
                                    } while ((0 | u) != (0 | a));
                                    p = a
                                } else {
                                    u = 0;
                                    do {
                                        c = 0 | f[e + ((l = u << 1) << 2) >> 2], _ = ((d = 0 | f[e + ((1 | l) << 2) >> 2]) >>> 7 & 510) + (c >>> 7 & 510) | 0, h = (d << 1 & 510) + (c << 1 & 510) | 0, d = (c = 33685504 + (0 | A(l = (d >>> 15 & 510) + (c >>> 15 & 510) | 0, -9719)) + (0 | A(_, -19081)) + (28800 * h | 0) | 0) >> 18, h = (m = 33685504 + (28800 * l | 0) + (0 | A(_, -24116)) + (0 | A(h, -4684)) | 0) >> 18, i[(_ = r + u | 0) >> 0] = ((d >>> 0 < 256 ? d : 255 + (c >> 31 & -255) | 0) + 1 + (0 | s[_ >> 0]) | 0) >>> 1, i[(_ = t + u | 0) >> 0] = ((h >>> 0 < 256 ? h : 255 + (m >> 31 & -255) | 0) + 1 + (0 | s[_ >> 0]) | 0) >>> 1, u = u + 1 | 0
                                    } while ((0 | u) != (0 | a));
                                    p = a
                                } else p = 0;
                            1 & n && (a = (n = 0 | f[e + (p << 1 << 2) >> 2]) >>> 6 & 1020, u = n << 2 & 1020, d = (l = (n = 33685504 + (0 | A(e = n >>> 14 & 1020, -9719)) + (0 | A(a, -19081)) + (28800 * u | 0) | 0) >> 18) >>> 0 < 256 ? l : 255 + (n >> 31 & -255) | 0, a = (u = (n = 33685504 + (28800 * e | 0) + (0 | A(a, -24116)) + (0 | A(u, -4684)) | 0) >> 18) >>> 0 < 256 ? u : 255 + (n >> 31 & -255) | 0, o ? (i[r + p >> 0] = d, v = a, b = t + p | 0) : (i[(o = r + p | 0) >> 0] = (d + 1 + (0 | s[o >> 0]) | 0) >>> 1, v = (a + 1 + (0 | s[(o = t + p | 0) >> 0]) | 0) >>> 1, b = o), i[b >> 0] = v)
                        }, function(e, r, t, n, i) {
                            e |= 0, r |= 0, n |= 0, i |= 0;
                            var o = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                _ = 0;
                            if ((0 | (t |= 0)) > 0) {
                                o = r, u = 0, f = e;
                                do {
                                    e = 0 | a[f >> 1], r = 0 | a[(f = f + 2 | 0) >> 1], l = 0 | a[o >> 1], _ = (8 + (9 * e | 0) + (s = 0 | a[(o = o + 2 | 0) >> 1]) + (3 * (l + r | 0) | 0) >> 4) + (0 | d[n + ((c = u << 1) << 1) >> 1]) | 0, a[i + (c << 1) >> 1] = (0 | _) < 0 ? 0 : 65535 & ((0 | _) < 1023 ? _ : 1023), c = (8 + (9 * r | 0) + l + (3 * (s + e | 0) | 0) >> 4) + (0 | d[n + ((_ = 1 | c) << 1) >> 1]) | 0, a[i + (_ << 1) >> 1] = (0 | c) < 0 ? 0 : 65535 & ((0 | c) < 1023 ? c : 1023), u = u + 1 | 0
                                } while ((0 | u) != (0 | t))
                            }
                        }, kt, kt, kt, kt, kt, kt, kt],
                        Pt = [function(e) {
                            L(2)
                        }, function(e) {
                            f[(e |= 0) >> 2] = 0, f[e + 4 >> 2] = 0, f[e + 8 >> 2] = 0, f[e + 12 >> 2] = 0, f[e + 16 >> 2] = 0, f[e + 20 >> 2] = 0
                        }, function(e) {
                            var r, t, n = 0,
                                i = 0;
                            if (r = 0 | f[(e |= 0) >> 2]) {
                                switch (0 | f[(t = e + 4 | 0) >> 2]) {
                                    case 0:
                                        break;
                                    case 1:
                                        n = r + 28 | 0, i = 6;
                                        break;
                                    default:
                                        e = r + 28 | 0;
                                        do {
                                            C(0 | e, 0 | r)
                                        } while (1 != (0 | f[t >> 2]));
                                        n = e, i = 6
                                }
                                6 == (0 | i) && (f[t >> 2] = 2, U(0 | n))
                            }
                        }, function(e) {
                            var r, t = 0;
                            (t = 0 | f[8 + (e |= 0) >> 2]) && (r = 0 == (0 | Ht[31 & t](0 | f[e + 12 >> 2], 0 | f[e + 16 >> 2])) & 1, f[(t = e + 20 | 0) >> 2] = r | f[t >> 2])
                        }, function(e) {
                            var r, t, n = 0,
                                i = 0,
                                o = 0;
                            if (r = 0 | f[(e |= 0) >> 2]) {
                                switch (0 | f[(t = e + 4 | 0) >> 2]) {
                                    case 0:
                                        break;
                                    case 1:
                                        n = r + 28 | 0, i = 6;
                                        break;
                                    default:
                                        o = r + 28 | 0;
                                        do {
                                            C(0 | o, 0 | r)
                                        } while (1 != (0 | f[t >> 2]));
                                        n = o, i = 6
                                }
                                6 == (0 | i) && (f[t >> 2] = 0, U(0 | n)), P(0 | f[r + 76 >> 2], 0), G(0 | r), N(r + 28 | 0), Ve(r), f[e >> 2] = 0
                            }
                        }, function(e) {
                            var r;
                            r = 40 + (0 | f[40 + (e |= 0) >> 2]) | 0, Ve(0 | f[r >> 2]), f[r >> 2] = 0
                        }, function(e) {
                            var r, t, n, o, a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0;
                            if (r = 0 | f[68 + (e |= 0) >> 2], t = 0 | f[e + 76 >> 2], n = 0 | A(0 | f[e + 8 >> 2], 0 | f[e + 52 >> 2]), o = 0 | f[e + 80 >> 2], a = 0 | f[e + 24 >> 2]) {
                                if (u = 0 | dt(0, 0 - a | 0, 0 | (l = 0 | f[e + 32 >> 2]), ((0 | l) < 0) << 31 >> 31 | 0), (0 | n) <= 0) return;
                                l = 0 | it(0, 0, 0 | u, 0 | M), a = e + 16 | 0, e = 0;
                                do {
                                    s = 0 | _t(0 | f[o + (e << 2) >> 2], 0, 0 | l, 0), c = M, d = 0 | _t(0 | f[t + (e << 2) >> 2], 0, 0 | u, 0), _ = M, h = 0 | ot(0 | s, 0 | c, -2147483648, 0), ot(0 | h, 0 | M, 0 | d, 0 | _), ot(0 | (_ = 0 | _t(0 | M, 0, 0 | f[a >> 2], 0)), 0 | M, -2147483648, 0), i[r + e >> 0] = M, e = e + 1 | 0
                                } while ((0 | e) != (0 | n))
                            } else if (!((0 | n) <= 0)) {
                                u = e + 16 | 0, l = 0;
                                do {
                                    ot(0 | (s = 0 | _t(0 | f[u >> 2], 0, 0 | f[o + (l << 2) >> 2], 0)), 0 | M, -2147483648, 0), i[r + l >> 0] = M, l = l + 1 | 0
                                } while ((0 | l) != (0 | n))
                            }
                        }, function(e) {
                            var r, t, n, o, a, u = 0,
                                l = 0,
                                s = 0,
                                c = 0;
                            if (r = 0 | f[68 + (e |= 0) >> 2], t = 0 | f[e + 76 >> 2], n = 0 | A(0 | f[e + 8 >> 2], 0 | f[e + 52 >> 2]), o = 0 | f[e + 80 >> 2], u = (0 | n) > 0, a = 0 | A(0 | f[e + 24 >> 2], 0 - (0 | f[e + 16 >> 2]) | 0)) {
                                if (!u) return;
                                u = e + 20 | 0, e = 0;
                                do {
                                    ot(0 | (s = 0 | _t(0 | f[o + (e << 2) >> 2], 0, 0 | a, 0)), 0 | M, -2147483648, 0), s = M, _t((0 | f[(l = t + (e << 2) | 0) >> 2]) - s | 0, 0, 0 | f[u >> 2], 0), i[r + e >> 0] = M, f[l >> 2] = s, e = e + 1 | 0
                                } while ((0 | e) != (0 | n))
                            } else if (u) {
                                l = e + 20 | 0, s = 0;
                                do {
                                    c = t + (s << 2) | 0, ot(0 | _t(0 | f[l >> 2], 0, 0 | f[c >> 2], 0), 0 | M, -2147483648, 0), i[r + s >> 0] = M, f[c >> 2] = 0, s = s + 1 | 0
                                } while ((0 | s) != (0 | n))
                            }
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + -29 >> 0]) + ((0 | s[e + -30 >> 0]) + ((0 | s[e + -31 >> 0]) + (4 + (0 | s[e + -32 >> 0]) + (0 | s[e + -1 >> 0])) + (0 | s[e + 31 >> 0])) + (0 | s[e + 63 >> 0])) + (0 | s[e + 95 >> 0]) | 0) >>> 3 & 255), 4), at(e + 32 | 0, 0 | r, 4), at(e + 64 | 0, 0 | r, 4), at(e + 96 | 0, 0 | r, 4)
                        }, function(e) {
                            e |= 0;
                            var r, t, n, o, a, u = 0;
                            u = (r = (0 | f[4]) + (0 - (0 | s[e + -33 >> 0])) | 0) + (0 | s[e + -1 >> 0]) | 0, t = 0 | s[e + -32 >> 0], i[e >> 0] = 0 | i[u + t >> 0], n = 0 | s[e + -31 >> 0], i[e + 1 >> 0] = 0 | i[u + n >> 0], o = 0 | s[e + -30 >> 0], i[e + 2 >> 0] = 0 | i[u + o >> 0], a = 0 | s[e + -29 >> 0], i[e + 3 >> 0] = 0 | i[u + a >> 0], u = r + (0 | s[e + 31 >> 0]) | 0, i[e + 32 >> 0] = 0 | i[u + t >> 0], i[e + 33 >> 0] = 0 | i[u + n >> 0], i[e + 34 >> 0] = 0 | i[u + o >> 0], i[e + 35 >> 0] = 0 | i[u + a >> 0], u = r + (0 | s[e + 63 >> 0]) | 0, i[e + 64 >> 0] = 0 | i[u + t >> 0], i[e + 65 >> 0] = 0 | i[u + n >> 0], i[e + 66 >> 0] = 0 | i[u + o >> 0], i[e + 67 >> 0] = 0 | i[u + a >> 0], u = r + (0 | s[e + 95 >> 0]) | 0, i[e + 96 >> 0] = 0 | i[u + t >> 0], i[e + 97 >> 0] = 0 | i[u + n >> 0], i[e + 98 >> 0] = 0 | i[u + o >> 0], i[e + 99 >> 0] = 0 | i[u + a >> 0]
                        }, function(e) {
                            var r, t, n, o, a = 0,
                                u = 0;
                            a = 0 | s[(e |= 0) - 32 >> 0], u = 0 | s[e + -31 >> 0], r = (2 + (0 | s[e + -33 >> 0]) + (a << 1) + u | 0) >>> 2 & 255, n = (a + 2 + (u << 1) + (t = 0 | s[e + -30 >> 0]) | 0) >>> 2 & 255, o = (u + 2 + (t << 1) + (a = 0 | s[e + -29 >> 0]) | 0) >>> 2 & 255, u = (t + 2 + (a << 1) + (0 | s[e + -28 >> 0]) | 0) >>> 2 & 255, i[e >> 0] = r, i[e + 1 >> 0] = n, i[e + 2 >> 0] = o, i[e + 3 >> 0] = u, i[e + 32 >> 0] = r, i[e + 33 >> 0] = n, i[e + 34 >> 0] = o, i[e + 35 >> 0] = u, i[e + 64 >> 0] = r, i[e + 65 >> 0] = n, i[e + 66 >> 0] = o, i[e + 67 >> 0] = u, i[e + 96 >> 0] = r, i[e + 97 >> 0] = n, i[e + 98 >> 0] = o, i[e + 99 >> 0] = u
                        }, function(e) {
                            var r, t, n, o, a, u, f, l, c, d = 0;
                            r = 0 | s[(e |= 0) - 1 >> 0], t = 0 | s[e + 31 >> 0], n = 0 | s[e + 63 >> 0], o = 0 | s[e + -33 >> 0], a = 0 | s[e + -32 >> 0], u = 0 | s[e + -31 >> 0], f = 0 | s[e + -30 >> 0], l = 0 | s[e + -29 >> 0], i[e + 96 >> 0] = (t + 2 + (n << 1) + (0 | s[e + 95 >> 0]) | 0) >>> 2, d = ((c = r + 2 | 0) + (t << 1) + n | 0) >>> 2 & 255, i[e + 64 >> 0] = d, i[e + 97 >> 0] = d, d = (2 + (r << 1) + t + o | 0) >>> 2 & 255, i[e + 32 >> 0] = d, i[e + 65 >> 0] = d, i[e + 98 >> 0] = d, d = (c + (o << 1) + a | 0) >>> 2 & 255, i[e >> 0] = d, i[e + 33 >> 0] = d, i[e + 66 >> 0] = d, i[e + 99 >> 0] = d, d = (o + 2 + (a << 1) + u | 0) >>> 2 & 255, i[e + 1 >> 0] = d, i[e + 34 >> 0] = d, i[e + 67 >> 0] = d, d = (a + 2 + (u << 1) + f | 0) >>> 2 & 255, i[e + 2 >> 0] = d, i[e + 35 >> 0] = d, i[e + 3 >> 0] = (u + 2 + (f << 1) + l | 0) >>> 2
                        }, function(e) {
                            var r, t, n, o, a, u, f, l = 0;
                            r = 0 | s[(e |= 0) - 31 >> 0], t = 0 | s[e + -30 >> 0], n = 0 | s[e + -29 >> 0], o = 0 | s[e + -28 >> 0], a = 0 | s[e + -27 >> 0], u = 0 | s[e + -26 >> 0], f = 0 | s[e + -25 >> 0], i[e >> 0] = (2 + (0 | s[e + -32 >> 0]) + (r << 1) + t | 0) >>> 2, l = (r + 2 + (t << 1) + n | 0) >>> 2 & 255, i[e + 32 >> 0] = l, i[e + 1 >> 0] = l, l = (t + 2 + (n << 1) + o | 0) >>> 2 & 255, i[e + 64 >> 0] = l, i[e + 33 >> 0] = l, i[e + 2 >> 0] = l, l = (n + 2 + (o << 1) + a | 0) >>> 2 & 255, i[e + 96 >> 0] = l, i[e + 65 >> 0] = l, i[e + 34 >> 0] = l, i[e + 3 >> 0] = l, l = (o + 2 + (a << 1) + u | 0) >>> 2 & 255, i[e + 97 >> 0] = l, i[e + 66 >> 0] = l, i[e + 35 >> 0] = l, l = (a + 2 + (u << 1) + f | 0) >>> 2 & 255, i[e + 98 >> 0] = l, i[e + 67 >> 0] = l, i[e + 99 >> 0] = (u + 2 + f + (f << 1) | 0) >>> 2
                        }, function(e) {
                            var r, t, n, o, a = 0,
                                u = 0;
                            r = 0 | s[(e |= 0) - 1 >> 0], t = 0 | s[e + 31 >> 0], n = 0 | s[e + 63 >> 0], o = 0 | s[e + 95 >> 0], a = 0 | A((2 + (0 | s[e + -33 >> 0]) + (r << 1) + t | 0) >>> 2 & 255, 16843009), i[e >> 0] = a, i[e + 1 >> 0] = a >> 8, i[e + 2 >> 0] = a >> 16, i[e + 3 >> 0] = a >> 24, a = e + 32 | 0, u = 0 | A((r + 2 + (t << 1) + n | 0) >>> 2 & 255, 16843009), i[a >> 0] = u, i[a + 1 >> 0] = u >> 8, i[a + 2 >> 0] = u >> 16, i[a + 3 >> 0] = u >> 24, u = e + 64 | 0, a = 0 | A((t + 2 + (n << 1) + o | 0) >>> 2 & 255, 16843009), i[u >> 0] = a, i[u + 1 >> 0] = a >> 8, i[u + 2 >> 0] = a >> 16, i[u + 3 >> 0] = a >> 24, a = e + 96 | 0, e = 0 | A((n + 2 + o + (o << 1) | 0) >>> 2 & 255, 16843009), i[a >> 0] = e, i[a + 1 >> 0] = e >> 8, i[a + 2 >> 0] = e >> 16, i[a + 3 >> 0] = e >> 24
                        }, function(e) {
                            var r, t, n, o, a, u, f, l = 0,
                                c = 0;
                            r = 0 | s[(e |= 0) - 1 >> 0], l = 0 | s[e + 31 >> 0], t = 0 | s[e + 63 >> 0], n = 0 | s[e + -33 >> 0], o = 0 | s[e + -32 >> 0], a = 0 | s[e + -31 >> 0], u = 0 | s[e + -30 >> 0], f = 0 | s[e + -29 >> 0], c = (n + 1 + o | 0) >>> 1 & 255, i[e + 65 >> 0] = c, i[e >> 0] = c, c = (o + 1 + a | 0) >>> 1 & 255, i[e + 66 >> 0] = c, i[e + 1 >> 0] = c, c = (a + 1 + u | 0) >>> 1 & 255, i[e + 67 >> 0] = c, i[e + 2 >> 0] = c, i[e + 3 >> 0] = (u + 1 + f | 0) >>> 1, c = r + 2 | 0, i[e + 96 >> 0] = (c + (l << 1) + t | 0) >>> 2, i[e + 64 >> 0] = (2 + (r << 1) + l + n | 0) >>> 2, l = (c + (n << 1) + o | 0) >>> 2 & 255, i[e + 97 >> 0] = l, i[e + 32 >> 0] = l, l = (n + 2 + (o << 1) + a | 0) >>> 2 & 255, i[e + 98 >> 0] = l, i[e + 33 >> 0] = l, l = (o + 2 + (a << 1) + u | 0) >>> 2 & 255, i[e + 99 >> 0] = l, i[e + 34 >> 0] = l, i[e + 35 >> 0] = (a + 2 + (u << 1) + f | 0) >>> 2
                        }, function(e) {
                            var r, t, n, o, a, u, f, l = 0,
                                c = 0;
                            l = 0 | s[(e |= 0) - 32 >> 0], r = 0 | s[e + -31 >> 0], t = 0 | s[e + -30 >> 0], n = 0 | s[e + -29 >> 0], o = 0 | s[e + -28 >> 0], a = 0 | s[e + -27 >> 0], u = 0 | s[e + -26 >> 0], f = 0 | s[e + -25 >> 0], i[e >> 0] = (l + 1 + r | 0) >>> 1, c = (r + 1 + t | 0) >>> 1 & 255, i[e + 64 >> 0] = c, i[e + 1 >> 0] = c, c = (t + 1 + n | 0) >>> 1 & 255, i[e + 65 >> 0] = c, i[e + 2 >> 0] = c, c = (n + 1 + o | 0) >>> 1 & 255, i[e + 66 >> 0] = c, i[e + 3 >> 0] = c, i[e + 32 >> 0] = (l + 2 + (r << 1) + t | 0) >>> 2, l = (r + 2 + (t << 1) + n | 0) >>> 2 & 255, i[e + 96 >> 0] = l, i[e + 33 >> 0] = l, l = (t + 2 + (n << 1) + o | 0) >>> 2 & 255, i[e + 97 >> 0] = l, i[e + 34 >> 0] = l, l = (n + 2 + (o << 1) + a | 0) >>> 2 & 255, i[e + 98 >> 0] = l, i[e + 35 >> 0] = l, i[e + 67 >> 0] = (o + 2 + (a << 1) + u | 0) >>> 2, i[e + 99 >> 0] = (a + 2 + (u << 1) + f | 0) >>> 2
                        }, function(e) {
                            var r, t, n, o, a, u, f, l = 0,
                                c = 0,
                                d = 0;
                            r = 0 | s[(e |= 0) - 1 >> 0], t = 0 | s[e + 31 >> 0], n = 0 | s[e + 63 >> 0], o = 0 | s[e + 95 >> 0], a = 0 | s[e + -33 >> 0], u = 0 | s[e + -32 >> 0], l = 0 | s[e + -31 >> 0], c = 0 | s[e + -30 >> 0], d = ((f = r + 1 | 0) + a | 0) >>> 1 & 255, i[e + 34 >> 0] = d, i[e >> 0] = d, d = (f + t | 0) >>> 1 & 255, i[e + 66 >> 0] = d, i[e + 32 >> 0] = d, d = (t + 1 + n | 0) >>> 1 & 255, i[e + 98 >> 0] = d, i[e + 64 >> 0] = d, i[e + 96 >> 0] = (n + 1 + o | 0) >>> 1, i[e + 3 >> 0] = (u + 2 + (l << 1) + c | 0) >>> 2, i[e + 2 >> 0] = (a + 2 + (u << 1) + l | 0) >>> 2, c = ((l = r + 2 | 0) + (a << 1) + u | 0) >>> 2 & 255, i[e + 35 >> 0] = c, i[e + 1 >> 0] = c, c = (2 + (r << 1) + t + a | 0) >>> 2 & 255, i[e + 67 >> 0] = c, i[e + 33 >> 0] = c, c = (l + (t << 1) + n | 0) >>> 2 & 255, i[e + 99 >> 0] = c, i[e + 65 >> 0] = c, i[e + 97 >> 0] = (t + 2 + (n << 1) + o | 0) >>> 2
                        }, function(e) {
                            var r, t, n, o, a = 0,
                                u = 0;
                            a = 0 | s[(e |= 0) - 1 >> 0], r = 0 | s[e + 31 >> 0], t = 0 | s[e + 63 >> 0], o = 255 & (n = 0 | i[e + 95 >> 0]), i[e >> 0] = (a + 1 + r | 0) >>> 1, u = (r + 1 + t | 0) >>> 1 & 255, i[e + 32 >> 0] = u, i[e + 2 >> 0] = u, u = (t + 1 + o | 0) >>> 1 & 255, i[e + 64 >> 0] = u, i[e + 34 >> 0] = u, i[e + 1 >> 0] = (a + 2 + (r << 1) + t | 0) >>> 2, a = (r + 2 + (t << 1) + o | 0) >>> 2 & 255, i[e + 33 >> 0] = a, i[e + 3 >> 0] = a, a = (t + 2 + o + (o << 1) | 0) >>> 2 & 255, i[e + 65 >> 0] = a, i[e + 35 >> 0] = a, i[e + 66 >> 0] = n, i[e + 67 >> 0] = n, at(e + 96 | 0, 0 | n, 4)
                        }, function(e) {
                            e |= 0;
                            var r = 0,
                                t = 0;
                            r = 16, t = 0;
                            do {
                                r = (0 | s[e + ((t << 5) - 1) >> 0]) + r + (0 | s[e + (t + -32) >> 0]) | 0, t = t + 1 | 0
                            } while (16 != (0 | t));
                            at(0 | e, 0 | (t = r >>> 5 & 255), 16), at(e + 32 | 0, 0 | t, 16), at(e + 64 | 0, 0 | t, 16), at(e + 96 | 0, 0 | t, 16), at(e + 128 | 0, 0 | t, 16), at(e + 160 | 0, 0 | t, 16), at(e + 192 | 0, 0 | t, 16), at(e + 224 | 0, 0 | t, 16), at(e + 256 | 0, 0 | t, 16), at(e + 288 | 0, 0 | t, 16), at(e + 320 | 0, 0 | t, 16), at(e + 352 | 0, 0 | t, 16), at(e + 384 | 0, 0 | t, 16), at(e + 416 | 0, 0 | t, 16), at(e + 448 | 0, 0 | t, 16), at(e + 480 | 0, 0 | t, 16)
                        }, function(e) {
                            var r, t, n, o, a, u, l, c, d, _, h, m, p, v, b, w, S, k = 0,
                                E = 0;
                            for (r = (e |= 0) - 32 | 0, t = (0 | f[4]) + (0 - (0 | s[e + -33 >> 0])) | 0, n = e + -31 | 0, o = e + -30 | 0, a = e + -29 | 0, u = e + -28 | 0, l = e + -27 | 0, c = e + -26 | 0, d = e + -25 | 0, _ = e + -24 | 0, h = e + -23 | 0, m = e + -22 | 0, p = e + -21 | 0, v = e + -20 | 0, b = e + -19 | 0, w = e + -18 | 0, S = e + -17 | 0, k = e, e = 0; E = t + (0 | s[k + -1 >> 0]) | 0, i[k >> 0] = 0 | i[E + (0 | s[r >> 0]) >> 0], i[k + 1 >> 0] = 0 | i[E + (0 | s[n >> 0]) >> 0], i[k + 2 >> 0] = 0 | i[E + (0 | s[o >> 0]) >> 0], i[k + 3 >> 0] = 0 | i[E + (0 | s[a >> 0]) >> 0], i[k + 4 >> 0] = 0 | i[E + (0 | s[u >> 0]) >> 0], i[k + 5 >> 0] = 0 | i[E + (0 | s[l >> 0]) >> 0], i[k + 6 >> 0] = 0 | i[E + (0 | s[c >> 0]) >> 0], i[k + 7 >> 0] = 0 | i[E + (0 | s[d >> 0]) >> 0], i[k + 8 >> 0] = 0 | i[E + (0 | s[_ >> 0]) >> 0], i[k + 9 >> 0] = 0 | i[E + (0 | s[h >> 0]) >> 0], i[k + 10 >> 0] = 0 | i[E + (0 | s[m >> 0]) >> 0], i[k + 11 >> 0] = 0 | i[E + (0 | s[p >> 0]) >> 0], i[k + 12 >> 0] = 0 | i[E + (0 | s[v >> 0]) >> 0], i[k + 13 >> 0] = 0 | i[E + (0 | s[b >> 0]) >> 0], i[k + 14 >> 0] = 0 | i[E + (0 | s[w >> 0]) >> 0], i[k + 15 >> 0] = 0 | i[E + (0 | s[S >> 0]) >> 0], 16 != (0 | (e = e + 1 | 0));) k = k + 32 | 0
                        }, function(e) {
                            var r, t = 0,
                                n = 0,
                                o = 0;
                            n = r = (e |= 0) - 32 | 0, o = (t = e) + 16 | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 32 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 64 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 96 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 128 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 160 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 192 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 224 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 256 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 288 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 320 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 352 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 384 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 416 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 448 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o));
                            n = r, o = 16 + (t = e + 480 | 0) | 0;
                            do {
                                i[t >> 0] = 0 | i[n >> 0], t = t + 1 | 0, n = n + 1 | 0
                            } while ((0 | t) < (0 | o))
                        }, function(e) {
                            at(0 | (e |= 0), 0 | i[e + -1 >> 0], 16), at(e + 32 | 0, 0 | i[e + 31 >> 0], 16), at(e + 64 | 0, 0 | i[e + 63 >> 0], 16), at(e + 96 | 0, 0 | i[e + 95 >> 0], 16), at(e + 128 | 0, 0 | i[e + 127 >> 0], 16), at(e + 160 | 0, 0 | i[e + 159 >> 0], 16), at(e + 192 | 0, 0 | i[e + 191 >> 0], 16), at(e + 224 | 0, 0 | i[e + 223 >> 0], 16), at(e + 256 | 0, 0 | i[e + 255 >> 0], 16), at(e + 288 | 0, 0 | i[e + 287 >> 0], 16), at(e + 320 | 0, 0 | i[e + 319 >> 0], 16), at(e + 352 | 0, 0 | i[e + 351 >> 0], 16), at(e + 384 | 0, 0 | i[e + 383 >> 0], 16), at(e + 416 | 0, 0 | i[e + 415 >> 0], 16), at(e + 448 | 0, 0 | i[e + 447 >> 0], 16), at(e + 480 | 0, 0 | i[e + 479 >> 0], 16)
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + 479 >> 0]) + ((0 | s[e + 447 >> 0]) + ((0 | s[e + 415 >> 0]) + ((0 | s[e + 383 >> 0]) + ((0 | s[e + 351 >> 0]) + ((0 | s[e + 319 >> 0]) + ((0 | s[e + 287 >> 0]) + ((0 | s[e + 255 >> 0]) + ((0 | s[e + 223 >> 0]) + ((0 | s[e + 191 >> 0]) + ((0 | s[e + 159 >> 0]) + ((0 | s[e + 127 >> 0]) + ((0 | s[e + 95 >> 0]) + ((0 | s[e + 63 >> 0]) + ((0 | s[e + 31 >> 0]) + (8 + (0 | s[e + -1 >> 0])))))))))))))))) | 0) >>> 4 & 255), 16), at(e + 32 | 0, 0 | r, 16), at(e + 64 | 0, 0 | r, 16), at(e + 96 | 0, 0 | r, 16), at(e + 128 | 0, 0 | r, 16), at(e + 160 | 0, 0 | r, 16), at(e + 192 | 0, 0 | r, 16), at(e + 224 | 0, 0 | r, 16), at(e + 256 | 0, 0 | r, 16), at(e + 288 | 0, 0 | r, 16), at(e + 320 | 0, 0 | r, 16), at(e + 352 | 0, 0 | r, 16), at(e + 384 | 0, 0 | r, 16), at(e + 416 | 0, 0 | r, 16), at(e + 448 | 0, 0 | r, 16), at(e + 480 | 0, 0 | r, 16)
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + -17 >> 0]) + ((0 | s[e + -18 >> 0]) + ((0 | s[e + -19 >> 0]) + ((0 | s[e + -20 >> 0]) + ((0 | s[e + -21 >> 0]) + ((0 | s[e + -22 >> 0]) + ((0 | s[e + -23 >> 0]) + ((0 | s[e + -24 >> 0]) + ((0 | s[e + -25 >> 0]) + ((0 | s[e + -26 >> 0]) + ((0 | s[e + -27 >> 0]) + ((0 | s[e + -28 >> 0]) + ((0 | s[e + -29 >> 0]) + ((0 | s[e + -30 >> 0]) + ((0 | s[e + -31 >> 0]) + (8 + (0 | s[e + -32 >> 0])))))))))))))))) | 0) >>> 4 & 255), 16), at(e + 32 | 0, 0 | r, 16), at(e + 64 | 0, 0 | r, 16), at(e + 96 | 0, 0 | r, 16), at(e + 128 | 0, 0 | r, 16), at(e + 160 | 0, 0 | r, 16), at(e + 192 | 0, 0 | r, 16), at(e + 224 | 0, 0 | r, 16), at(e + 256 | 0, 0 | r, 16), at(e + 288 | 0, 0 | r, 16), at(e + 320 | 0, 0 | r, 16), at(e + 352 | 0, 0 | r, 16), at(e + 384 | 0, 0 | r, 16), at(e + 416 | 0, 0 | r, 16), at(e + 448 | 0, 0 | r, 16), at(e + 480 | 0, 0 | r, 16)
                        }, function(e) {
                            var r = 0,
                                t = 0;
                            t = 16 + (r = e |= 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 32 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 64 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 96 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 128 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 160 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 192 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 224 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 256 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 288 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 320 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 352 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 384 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 416 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 448 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t));
                            t = 16 + (r = e + 480 | 0) | 0;
                            do {
                                i[r >> 0] = 128, r = r + 1 | 0
                            } while ((0 | r) < (0 | t))
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + -25 >> 0]) + ((0 | s[e + -26 >> 0]) + ((0 | s[e + -27 >> 0]) + ((0 | s[e + -28 >> 0]) + ((0 | s[e + -29 >> 0]) + ((0 | s[e + -30 >> 0]) + ((0 | s[e + -31 >> 0]) + (8 + (0 | s[e + -32 >> 0]) + (0 | s[e + -1 >> 0])) + (0 | s[e + 31 >> 0])) + (0 | s[e + 63 >> 0])) + (0 | s[e + 95 >> 0])) + (0 | s[e + 127 >> 0])) + (0 | s[e + 159 >> 0])) + (0 | s[e + 191 >> 0])) + (0 | s[e + 223 >> 0]) | 0) >>> 4 & 255), 8), at(e + 32 | 0, 0 | r, 8), at(e + 64 | 0, 0 | r, 8), at(e + 96 | 0, 0 | r, 8), at(e + 128 | 0, 0 | r, 8), at(e + 160 | 0, 0 | r, 8), at(e + 192 | 0, 0 | r, 8), at(e + 224 | 0, 0 | r, 8)
                        }, function(e) {
                            var r, t, n, o, a, u, l, c, d, _ = 0,
                                h = 0;
                            for (r = (e |= 0) - 32 | 0, t = (0 | f[4]) + (0 - (0 | s[e + -33 >> 0])) | 0, n = e + -31 | 0, o = e + -30 | 0, a = e + -29 | 0, u = e + -28 | 0, l = e + -27 | 0, c = e + -26 | 0, d = e + -25 | 0, _ = e, e = 0; h = t + (0 | s[_ + -1 >> 0]) | 0, i[_ >> 0] = 0 | i[h + (0 | s[r >> 0]) >> 0], i[_ + 1 >> 0] = 0 | i[h + (0 | s[n >> 0]) >> 0], i[_ + 2 >> 0] = 0 | i[h + (0 | s[o >> 0]) >> 0], i[_ + 3 >> 0] = 0 | i[h + (0 | s[a >> 0]) >> 0], i[_ + 4 >> 0] = 0 | i[h + (0 | s[u >> 0]) >> 0], i[_ + 5 >> 0] = 0 | i[h + (0 | s[l >> 0]) >> 0], i[_ + 6 >> 0] = 0 | i[h + (0 | s[c >> 0]) >> 0], i[_ + 7 >> 0] = 0 | i[h + (0 | s[d >> 0]) >> 0], 8 != (0 | (e = e + 1 | 0));) _ = _ + 32 | 0
                        }, function(e) {
                            var r = 0,
                                t = 0,
                                n = 0,
                                o = 0;
                            n = s[(t = r = (e |= 0) - 32 | 0) >> 0] | s[t + 1 >> 0] << 8 | s[t + 2 >> 0] << 16 | s[t + 3 >> 0] << 24, r = s[(t = r + 4 | 0) >> 0] | s[t + 1 >> 0] << 8 | s[t + 2 >> 0] << 16 | s[t + 3 >> 0] << 24, i[(o = t = e) >> 0] = n, i[o + 1 >> 0] = n >> 8, i[o + 2 >> 0] = n >> 16, i[o + 3 >> 0] = n >> 24, i[(o = t + 4 | 0) >> 0] = r, i[o + 1 >> 0] = r >> 8, i[o + 2 >> 0] = r >> 16, i[o + 3 >> 0] = r >> 24, i[(t = o = e + 32 | 0) >> 0] = n, i[t + 1 >> 0] = n >> 8, i[t + 2 >> 0] = n >> 16, i[t + 3 >> 0] = n >> 24, i[(t = o + 4 | 0) >> 0] = r, i[t + 1 >> 0] = r >> 8, i[t + 2 >> 0] = r >> 16, i[t + 3 >> 0] = r >> 24, i[(o = t = e + 64 | 0) >> 0] = n, i[o + 1 >> 0] = n >> 8, i[o + 2 >> 0] = n >> 16, i[o + 3 >> 0] = n >> 24, i[(o = t + 4 | 0) >> 0] = r, i[o + 1 >> 0] = r >> 8, i[o + 2 >> 0] = r >> 16, i[o + 3 >> 0] = r >> 24, i[(t = o = e + 96 | 0) >> 0] = n, i[t + 1 >> 0] = n >> 8, i[t + 2 >> 0] = n >> 16, i[t + 3 >> 0] = n >> 24, i[(t = o + 4 | 0) >> 0] = r, i[t + 1 >> 0] = r >> 8, i[t + 2 >> 0] = r >> 16, i[t + 3 >> 0] = r >> 24, i[(o = t = e + 128 | 0) >> 0] = n, i[o + 1 >> 0] = n >> 8, i[o + 2 >> 0] = n >> 16, i[o + 3 >> 0] = n >> 24, i[(o = t + 4 | 0) >> 0] = r, i[o + 1 >> 0] = r >> 8, i[o + 2 >> 0] = r >> 16, i[o + 3 >> 0] = r >> 24, i[(t = o = e + 160 | 0) >> 0] = n, i[t + 1 >> 0] = n >> 8, i[t + 2 >> 0] = n >> 16, i[t + 3 >> 0] = n >> 24, i[(t = o + 4 | 0) >> 0] = r, i[t + 1 >> 0] = r >> 8, i[t + 2 >> 0] = r >> 16, i[t + 3 >> 0] = r >> 24, i[(o = t = e + 192 | 0) >> 0] = n, i[o + 1 >> 0] = n >> 8, i[o + 2 >> 0] = n >> 16, i[o + 3 >> 0] = n >> 24, i[(o = t + 4 | 0) >> 0] = r, i[o + 1 >> 0] = r >> 8, i[o + 2 >> 0] = r >> 16, i[o + 3 >> 0] = r >> 24, i[(e = o = e + 224 | 0) >> 0] = n, i[e + 1 >> 0] = n >> 8, i[e + 2 >> 0] = n >> 16, i[e + 3 >> 0] = n >> 24, i[(n = o + 4 | 0) >> 0] = r, i[n + 1 >> 0] = r >> 8, i[n + 2 >> 0] = r >> 16, i[n + 3 >> 0] = r >> 24
                        }, function(e) {
                            at(0 | (e |= 0), 0 | i[e + -1 >> 0], 8), at(e + 32 | 0, 0 | i[e + 31 >> 0], 8), at(e + 64 | 0, 0 | i[e + 63 >> 0], 8), at(e + 96 | 0, 0 | i[e + 95 >> 0], 8), at(e + 128 | 0, 0 | i[e + 127 >> 0], 8), at(e + 160 | 0, 0 | i[e + 159 >> 0], 8), at(e + 192 | 0, 0 | i[e + 191 >> 0], 8), at(e + 224 | 0, 0 | i[e + 223 >> 0], 8)
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + 223 >> 0]) + ((0 | s[e + 191 >> 0]) + ((0 | s[e + 159 >> 0]) + ((0 | s[e + 127 >> 0]) + ((0 | s[e + 95 >> 0]) + ((0 | s[e + 63 >> 0]) + ((0 | s[e + 31 >> 0]) + (4 + (0 | s[e + -1 >> 0])))))))) | 0) >>> 3 & 255), 8), at(e + 32 | 0, 0 | r, 8), at(e + 64 | 0, 0 | r, 8), at(e + 96 | 0, 0 | r, 8), at(e + 128 | 0, 0 | r, 8), at(e + 160 | 0, 0 | r, 8), at(e + 192 | 0, 0 | r, 8), at(e + 224 | 0, 0 | r, 8)
                        }, function(e) {
                            var r;
                            at(0 | (e |= 0), 0 | (r = ((0 | s[e + -25 >> 0]) + ((0 | s[e + -26 >> 0]) + ((0 | s[e + -27 >> 0]) + ((0 | s[e + -28 >> 0]) + ((0 | s[e + -29 >> 0]) + ((0 | s[e + -30 >> 0]) + ((0 | s[e + -31 >> 0]) + (4 + (0 | s[e + -32 >> 0])))))))) | 0) >>> 3 & 255), 8), at(e + 32 | 0, 0 | r, 8), at(e + 64 | 0, 0 | r, 8), at(e + 96 | 0, 0 | r, 8), at(e + 128 | 0, 0 | r, 8), at(e + 160 | 0, 0 | r, 8), at(e + 192 | 0, 0 | r, 8), at(e + 224 | 0, 0 | r, 8)
                        }, function(e) {
                            var r = 0,
                                t = 0;
                            i[(t = r = e |= 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(t = r + 4 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(r = t = e + 32 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(r = t + 4 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(t = r = e + 64 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(t = r + 4 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(r = t = e + 96 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(r = t + 4 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(t = r = e + 128 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(t = r + 4 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(r = t = e + 160 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(r = t + 4 | 0) >> 0] = -2139062144, i[r + 1 >> 0] = -8355712, i[r + 2 >> 0] = -32640, i[r + 3 >> 0] = -128, i[(t = r = e + 192 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(t = r + 4 | 0) >> 0] = -2139062144, i[t + 1 >> 0] = -8355712, i[t + 2 >> 0] = -32640, i[t + 3 >> 0] = -128, i[(e = t = e + 224 | 0) >> 0] = -2139062144, i[e + 1 >> 0] = -8355712, i[e + 2 >> 0] = -32640, i[e + 3 >> 0] = -128, i[(e = t + 4 | 0) >> 0] = -2139062144, i[e + 1 >> 0] = -8355712, i[e + 2 >> 0] = -32640, i[e + 3 >> 0] = -128
                        }],
                        Rt = [Et, function(e, r) {
                            var t, n, i, o, a, u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0;
                            if (n = 100 + (e |= 0) | 0, (0 | (l = (r |= 0) - (u = 0 | f[(t = e + 108 | 0) >> 2]) | 0)) <= 0) return f[(e + 116 | 0) >> 2] = r, void(f[t >> 2] = r);
                            for (s = 0 | f[n >> 2], i = e + 8 | 0, o = e + 20 | 0, a = e + 176 | 0, c = l, l = u, d = u, _ = (0 | f[e + 16 >> 2]) + ((0 | A(s, u)) << 2) | 0, u = s;;) {
                                if (s = (0 | c) < 16 ? c : 16, h = 0 | f[i >> 2], m = 0 | f[h + 40 >> 2], p = 0 | f[h >> 2], h = 0 | A(p, s), v = (0 | f[m + 136 >> 2]) + (0 | A(p, l)) | 0, b = 0 | f[o >> 2], S = d + s | 0, (0 | (w = 0 | f[a >> 2])) <= 0)(0 | b) != (0 | _) && lt(0 | b, 0 | _, 0 | A(s << 2, u));
                                else
                                    for (k = _, E = w; sr(e + 180 + (20 * (w = E + -1 | 0) | 0) | 0, d, S, k, b), (0 | E) > 1;) k = b, E = w;
                                if (xt[31 & f[2889]](b, v, h), E = s + l | 0, 0 | (S = 0 | f[(k = m + 12 | 0) >> 2])) {
                                    if (M = 0 | f[(w = m + 140 | 0) >> 2], (0 | c) > 0) {
                                        if (y = 0 | A(p, s + -1 | 0), Ut[31 & f[11764 + (S << 2) >> 2]](M, v, v, p), (0 | (S = l + 1 | 0)) != (0 | E)) {
                                            L = v, g = S;
                                            do {
                                                S = L, L = L + p | 0, Ut[31 & f[11764 + (f[k >> 2] << 2) >> 2]](S, L, L, p), g = g + 1 | 0
                                            } while ((0 | g) != (0 | E))
                                        }
                                        T = v + y | 0
                                    } else T = M;
                                    f[w >> 2] = T
                                }
                                if (g = c - s | 0, p = 0 | f[n >> 2], (0 | g) <= 0) break;
                                L = _ + ((0 | A(s, p)) << 2) | 0, c = g, l = E, d = 0 | f[t >> 2], _ = L, u = p
                            }
                            f[(e + 116 | 0) >> 2] = r, f[t >> 2] = r
                        }, function(e, r) {
                            r |= 0;
                            var t, n, i, o, a, u, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0;
                            if (t = 0 | f[8 + (e |= 0) >> 2], n = 0 | A(0 | f[e + 52 >> 2], t), !((0 | t) <= 0)) {
                                i = e + 36 | 0, o = e + 44 | 0, a = e + 80 | 0, u = e + 40 | 0, e = 0;
                                do {
                                    l = 0 | f[i >> 2], c = 0 | s[r + e >> 0], d = e + t | 0, _ = (0 | f[o >> 2]) > 1 ? 0 | s[r + d >> 0] : c, h = 0 | f[a >> 2], m = 0 | A(l, c), f[h + (e << 2) >> 2] = m, m = e + t | 0;
                                    e: do {
                                        if ((0 | m) < (0 | n)) {
                                            p = _, v = l, b = d, w = c - _ | 0, S = m;
                                            do {
                                                for (k = v, E = S; !((0 | (k = k - (0 | f[u >> 2]) | 0)) < 0);) {
                                                    if (M = (0 | A(0 | f[i >> 2], p)) + (0 | A(w, k)) | 0, f[h + (E << 2) >> 2] = M, (0 | (M = E + t | 0)) >= (0 | n)) break e;
                                                    E = M
                                                }
                                                M = p, p = 0 | s[r + (b = b + t | 0) >> 0], v = (y = 0 | f[i >> 2]) + k | 0, w = M - p | 0, M = (0 | A(y, p)) + (0 | A(w, v)) | 0, f[h + (E << 2) >> 2] = M, S = E + t | 0
                                            } while ((0 | S) < (0 | n))
                                        }
                                    } while (0);
                                    e = e + 1 | 0
                                } while ((0 | e) != (0 | t))
                            }
                        }, function(e, r) {
                            r |= 0;
                            var t, n, i, o, a, u, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0;
                            if (t = 0 | f[8 + (e |= 0) >> 2], n = 0 | A(0 | f[e + 52 >> 2], t), !((0 | t) <= 0)) {
                                i = e + 36 | 0, o = e + 40 | 0, a = e + 80 | 0, u = e + 12 | 0, e = 0;
                                do {
                                    if ((0 | e) < (0 | n))
                                        for (l = 0, c = 0, d = e, _ = e;;) {
                                            if (h = (0 | f[i >> 2]) + l | 0, m = 0 | f[o >> 2], (0 | h) > 0)
                                                for (p = c, v = _, b = h;;) {
                                                    if (w = b - m | 0, k = (S = 0 | s[r + v >> 0]) + p | 0, E = v + t | 0, !((0 | w) > 0)) {
                                                        y = S, L = w, g = k, T = E;
                                                        break
                                                    }
                                                    p = k, v = E, b = w
                                                } else y = 0, L = h, g = c, T = _;
                                            if (b = 0 | A(y, 0 - L | 0), v = (0 | A(m, g)) - b | 0, f[(0 | f[a >> 2]) + (d << 2) >> 2] = v, ot(0 | (v = 0 | _t(0 | f[u >> 2], 0, 0 | b, 0)), 0 | M, -2147483648, 0), (0 | (d = d + t | 0)) >= (0 | n)) break;
                                            l = L, c = M, _ = T
                                        }
                                    e = e + 1 | 0
                                } while ((0 | e) != (0 | t))
                            }
                        }, function(e, r) {
                            r |= 0;
                            var t, n, i = 0,
                                o = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0;
                            t = S, S = S + 64 | 0, i = t, o = 0 | a[(e |= 0) >> 1], l = (u = 0 | a[e + 24 >> 1]) + o | 0, s = 0 | a[e + 8 >> 1], d = (c = 0 | a[e + 16 >> 1]) + s | 0, _ = s - c | 0, c = o - u | 0, u = d + l | 0, f[i >> 2] = u, f[(o = i + 32 | 0) >> 2] = l - d, f[(d = i + 16 | 0) >> 2] = _ + c, f[(l = i + 48 | 0) >> 2] = c - _, _ = 0 | a[e + 2 >> 1], s = (c = 0 | a[e + 26 >> 1]) + _ | 0, h = 0 | a[e + 10 >> 1], p = (m = 0 | a[e + 18 >> 1]) + h | 0, v = h - m | 0, m = _ - c | 0, c = p + s | 0, f[i + 4 >> 2] = c, f[(_ = i + 36 | 0) >> 2] = s - p, p = v + m | 0, f[i + 20 >> 2] = p, f[(s = i + 52 | 0) >> 2] = m - v, v = 0 | a[e + 4 >> 1], h = (m = 0 | a[e + 28 >> 1]) + v | 0, b = 0 | a[e + 12 >> 1], k = (w = 0 | a[e + 20 >> 1]) + b | 0, E = b - w | 0, w = v - m | 0, m = k + h | 0, f[i + 8 >> 2] = m, v = h - k | 0, f[i + 40 >> 2] = v, k = E + w | 0, f[i + 24 >> 2] = k, f[(h = i + 56 | 0) >> 2] = w - E, E = 0 | a[e + 6 >> 1], b = (w = 0 | a[e + 30 >> 1]) + E | 0, n = 0 | a[e + 14 >> 1], e = (M = 0 | a[e + 22 >> 1]) + n | 0, A = n - M | 0, M = E - w | 0, w = e + b | 0, f[i + 12 >> 2] = w, E = b - e | 0, f[i + 44 >> 2] = E, e = A + M | 0, f[i + 28 >> 2] = e, b = M - A | 0, f[i + 60 >> 2] = b, u = (i = u + 3 | 0) + w | 0, A = m + c | 0, M = c - m | 0, m = i - w | 0, a[r >> 1] = (A + u | 0) >>> 3, a[r + 32 >> 1] = (M + m | 0) >>> 3, a[r + 64 >> 1] = (u - A | 0) >>> 3, a[r + 96 >> 1] = (m - M | 0) >>> 3, d = (M = 3 + (0 | f[d >> 2]) | 0) + e | 0, m = k + p | 0, A = p - k | 0, k = M - e | 0, a[r + 128 >> 1] = (m + d | 0) >>> 3, a[r + 160 >> 1] = (A + k | 0) >>> 3, a[r + 192 >> 1] = (d - m | 0) >>> 3, a[r + 224 >> 1] = (k - A | 0) >>> 3, o = (A = 3 + (0 | f[o >> 2]) | 0) + E | 0, _ = v + (k = 0 | f[_ >> 2]) | 0, m = k - v | 0, v = A - E | 0, a[r + 256 >> 1] = (_ + o | 0) >>> 3, a[r + 288 >> 1] = (m + v | 0) >>> 3, a[r + 320 >> 1] = (o - _ | 0) >>> 3, a[r + 352 >> 1] = (v - m | 0) >>> 3, l = (m = 3 + (0 | f[l >> 2]) | 0) + b | 0, v = 0 | f[s >> 2], h = (s = 0 | f[h >> 2]) + v | 0, _ = v - s | 0, s = m - b | 0, a[r + 384 >> 1] = (h + l | 0) >>> 3, a[r + 416 >> 1] = (_ + s | 0) >>> 3, a[r + 448 >> 1] = (l - h | 0) >>> 3, a[r + 480 >> 1] = (s - _ | 0) >>> 3, S = t
                        }, function(e, r) {
                            r |= 0;
                            var t, n = 0;
                            t = 4 + (0 | a[(e |= 0) >> 1]) >> 3, e = (0 | s[r >> 0]) + t | 0, i[r >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 1 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 2 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 3 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 32 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 33 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 34 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 35 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 64 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 65 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 66 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 67 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 96 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, n = (0 | s[(e = r + 97 | 0) >> 0]) + t | 0, i[e >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, e = (0 | s[(n = r + 98 | 0) >> 0]) + t | 0, i[n >> 0] = e >>> 0 > 255 ? 255 + (e >>> 31) | 0 : e, r = (0 | s[(e = r + 99 | 0) >> 0]) + t | 0, i[e >> 0] = r >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r
                        }, function(e, r) {
                            r |= 0;
                            var t, n = 0,
                                o = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0;
                            n = 4 + (0 | a[(e |= 0) >> 1]) | 0, u = 35468 * (o = 0 | a[e + 8 >> 1]) >> 16, f = (20091 * o >> 16) + o | 0, e = 35468 * (o = 0 | a[e + 2 >> 1]) >> 16, t = (20091 * o >> 16) + o | 0, l = ((o = f + n | 0) + t >> 3) + (0 | s[r >> 0]) | 0, i[r >> 0] = l >>> 0 > 255 ? 255 + (l >>> 31) | 0 : l, c = (o + e >> 3) + (0 | s[(l = r + 1 | 0) >> 0]) | 0, i[l >> 0] = c >>> 0 > 255 ? 255 + (c >>> 31) | 0 : c, l = (0 | s[(c = r + 2 | 0) >> 0]) + (o - e >> 3) | 0, i[c >> 0] = l >>> 0 > 255 ? 255 + (l >>> 31) | 0 : l, c = (0 | s[(l = r + 3 | 0) >> 0]) + (o - t >> 3) | 0, i[l >> 0] = c >>> 0 > 255 ? 255 + (c >>> 31) | 0 : c, c = u + n | 0, o = (0 | s[(l = r + 32 | 0) >> 0]) + (t + c >> 3) | 0, i[l >> 0] = o >>> 0 > 255 ? 255 + (o >>> 31) | 0 : o, l = (0 | s[(o = r + 33 | 0) >> 0]) + (c + e >> 3) | 0, i[o >> 0] = l >>> 0 > 255 ? 255 + (l >>> 31) | 0 : l, o = (0 | s[(l = r + 34 | 0) >> 0]) + (c - e >> 3) | 0, i[l >> 0] = o >>> 0 > 255 ? 255 + (o >>> 31) | 0 : o, l = (0 | s[(o = r + 35 | 0) >> 0]) + (c - t >> 3) | 0, i[o >> 0] = l >>> 0 > 255 ? 255 + (l >>> 31) | 0 : l, l = n - u | 0, o = (0 | s[(u = r + 64 | 0) >> 0]) + (t + l >> 3) | 0, i[u >> 0] = o >>> 0 > 255 ? 255 + (o >>> 31) | 0 : o, u = (0 | s[(o = r + 65 | 0) >> 0]) + (l + e >> 3) | 0, i[o >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, o = (0 | s[(u = r + 66 | 0) >> 0]) + (l - e >> 3) | 0, i[u >> 0] = o >>> 0 > 255 ? 255 + (o >>> 31) | 0 : o, u = (0 | s[(o = r + 67 | 0) >> 0]) + (l - t >> 3) | 0, i[o >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, u = n - f | 0, n = (0 | s[(f = r + 96 | 0) >> 0]) + (u + t >> 3) | 0, i[f >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, f = (0 | s[(n = r + 97 | 0) >> 0]) + (u + e >> 3) | 0, i[n >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, n = (0 | s[(f = r + 98 | 0) >> 0]) + (u - e >> 3) | 0, i[f >> 0] = n >>> 0 > 255 ? 255 + (n >>> 31) | 0 : n, r = (0 | s[(n = r + 99 | 0) >> 0]) + (u - t >> 3) | 0, i[n >> 0] = r >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r
                        }, function(e, r) {
                            e |= 0, r |= 0, xt[31 & f[2919]](e, r, 1), xt[31 & f[2919]](e + 64 | 0, r + 128 | 0, 1)
                        }, function(e, r) {
                            r |= 0;
                            var t = 0;
                            0 | a[(e |= 0) >> 1] && Rt[15 & f[2922]](e, r), 0 | a[(t = e + 32 | 0) >> 1] && Rt[15 & f[2922]](t, r + 4 | 0), 0 | a[(t = e + 64 | 0) >> 1] && Rt[15 & f[2922]](t, r + 128 | 0), 0 | a[(t = e + 96 | 0) >> 1] && Rt[15 & f[2922]](t, r + 132 | 0)
                        }, function(e, r) {
                            var t, n, i, o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0,
                                O = 0;
                            if ((0 | (a = (r |= 0) - (o = 0 | f[(t = 108 + (e |= 0) | 0) >> 2]) | 0)) <= 0) f[t >> 2] = r;
                            else {
                                if (u = 0 | f[e + 100 >> 2], l = (0 | f[e + 16 >> 2]) + ((0 | A(u, o)) << 2) | 0, s = 0 | f[e + 8 >> 2], c = 0 | f[e + 20 >> 2], n = f[s >> 2] << 2, _ = c, (0 | (d = 0 | f[e + 176 >> 2])) <= 0)(0 | _) != (0 | l) && lt(0 | c, 0 | l, 0 | A(a << 2, u));
                                else
                                    for (u = l, l = d; sr(e + 180 + (20 * (d = l + -1 | 0) | 0) | 0, o, r, u, _), (0 | l) > 1;) u = _, l = d;
                                if (l = 0 | f[t >> 2], (0 | (u = (0 | (_ = 0 | f[s + 88 >> 2])) < (0 | r) ? _ : r)) <= (0 | (d = (o = (0 | (_ = 0 | f[s + 84 >> 2])) > (0 | l)) ? _ : l))) f[t >> 2] = r;
                                else {
                                    if (a = c + (0 | A(_ - l | 0, n)) | 0, i = (o ? a : c) + ((l = 0 | f[s + 76 >> 2]) << 2) | 0, f[s + 8 >> 2] = d - _, _ = (0 | f[s + 80 >> 2]) - l | 0, f[s + 12 >> 2] = _, l = u - d | 0, f[s + 16 >> 2] = l, u = 0 | f[(d = e + 12 | 0) >> 2], c = 0 | f[u >> 2], a = 0 != (0 | f[s + 92 >> 2]), c >>> 0 < 11) {
                                        if (s = e + 116 | 0, o = 0 | f[u + 20 >> 2], h = (0 | f[u + 16 >> 2]) + (0 | A(o, 0 | f[s >> 2])) | 0, m = (0 | l) > 0, a)
                                            if (m)
                                                for (p = e + 268 | 0, v = 0, b = 0;;) {
                                                    w = i + (0 | A(b, n)) | 0, S = h + (0 | A(v, o)) | 0, k = l - b | 0, E = 0 | Tr(0 | f[p >> 2], k), we(w, n, 0 | f[44 + (0 | f[p >> 2]) >> 2], E, 0), b = (0 | Mr(0 | f[p >> 2], k, w, n)) + b | 0, w = 0 | f[p >> 2], k = 0 | f[w + 68 >> 2], E = 0 | f[w + 52 >> 2], M = w + 24 | 0, y = w + 56 | 0, L = w + 64 | 0;
                                                    e: do {
                                                        if ((0 | f[L >> 2]) < (0 | f[y >> 2]))
                                                            for (g = 0;;) {
                                                                if ((0 | f[M >> 2]) >= 1) {
                                                                    T = g;
                                                                    break e
                                                                }
                                                                if (D = S + (0 | A(g, o)) | 0, Lr(w), xt[31 & f[2882]](k, E, 1), Se(k, E, c, D), D = g + 1 | 0, !((0 | f[L >> 2]) < (0 | f[y >> 2]))) {
                                                                    T = D;
                                                                    break
                                                                }
                                                                g = D
                                                            } else T = 0
                                                    } while (0);
                                                    if (y = T + v | 0, (0 | b) >= (0 | l)) {
                                                        C = y;
                                                        break
                                                    }
                                                    v = y
                                                } else C = 0;
                                        else if (m)
                                            for (m = l, v = i, b = h;;) {
                                                if (Se(v, _, c, b), !((0 | m) > 1)) {
                                                    C = l;
                                                    break
                                                }
                                                m = m + -1 | 0, v = v + n | 0, b = b + o | 0
                                            } else C = l;
                                        return f[s >> 2] = (0 | f[s >> 2]) + C, void(f[t >> 2] = r)
                                    }
                                    s = 0 | f[(C = e + 116 | 0) >> 2], o = (0 | l) > 0;
                                    e: do {
                                        if (a)
                                            if (o)
                                                for (b = e + 268 | 0, v = 0, m = s, c = i;;) {
                                                    h = l - v | 0, T = 0 | Tr(0 | f[b >> 2], h), we(c, n, 0 | f[44 + (0 | f[b >> 2]) >> 2], T, 0), v = (0 | Mr(0 | f[b >> 2], h, c, n)) + v | 0, c = c + (0 | A(T, n)) | 0, T = 0 | f[b >> 2], h = 0 | f[T + 68 >> 2], p = 0 | f[T + 52 >> 2], y = T + 24 | 0, L = T + 56 | 0, E = T + 64 | 0;
                                                    r: do {
                                                        if ((0 | f[E >> 2]) < (0 | f[L >> 2]))
                                                            for (k = h + 3 | 0, w = m, S = 0;;) {
                                                                if ((0 | f[y >> 2]) >= 1) {
                                                                    P = S;
                                                                    break r
                                                                }
                                                                if (Lr(T), xt[31 & f[2882]](h, p, 1), M = 0 | f[d >> 2], g = (0 | f[M + 16 >> 2]) + (0 | A(0 | f[M + 32 >> 2], w)) | 0, xt[31 & f[3064]](h, g, p), g = w >> 1, D = (0 | f[M + 20 >> 2]) + (0 | A(0 | f[M + 36 >> 2], g)) | 0, R = (0 | f[M + 24 >> 2]) + (0 | A(0 | f[M + 40 >> 2], g)) | 0, Ct[31 & f[3065]](h, D, R, p, 1 & w ^ 1), 0 | (R = 0 | f[M + 28 >> 2]) && (D = R + (0 | A(0 | f[M + 44 >> 2], w)) | 0, Ot[7 & f[2888]](k, 0, p, 1, D, 0)), D = S + 1 | 0, !((0 | f[E >> 2]) < (0 | f[L >> 2]))) {
                                                                    P = D;
                                                                    break
                                                                }
                                                                w = w + 1 | 0, S = D
                                                            } else P = 0
                                                    } while (0);
                                                    if (L = P + m | 0, (0 | v) >= (0 | l)) {
                                                        O = L;
                                                        break
                                                    }
                                                    m = L
                                                } else O = s;
                                        else if (o)
                                            for (m = l, v = i, b = s, c = u;;) {
                                                if (L = (0 | f[c + 16 >> 2]) + (0 | A(0 | f[c + 32 >> 2], b)) | 0, xt[31 & f[3064]](v, L, _), L = b >> 1, E = (0 | f[c + 20 >> 2]) + (0 | A(0 | f[c + 36 >> 2], L)) | 0, p = (0 | f[c + 24 >> 2]) + (0 | A(0 | f[c + 40 >> 2], L)) | 0, Ct[31 & f[3065]](v, E, p, _, 1 & b ^ 1), 0 | (p = 0 | f[c + 28 >> 2]) && (E = p + (0 | A(0 | f[c + 44 >> 2], b)) | 0, Ot[7 & f[2888]](v + 3 | 0, 0, _, 1, E, 0)), E = b + 1 | 0, (0 | m) <= 1) {
                                                    O = E;
                                                    break e
                                                }
                                                m = m + -1 | 0, v = v + n | 0, b = E, c = 0 | f[d >> 2]
                                            } else O = s
                                    } while (0);
                                    f[C >> 2] = O, f[t >> 2] = r
                                }
                            }
                        }, Et, Et, Et, Et, Et, Et],
                        Ot = [Mt, function(e, r, t, n, o, a) {
                            r |= 0, a |= 0;
                            var u = 0,
                                f = 0,
                                l = 0,
                                s = 0,
                                c = 0;
                            if (!((0 | (n |= 0)) > 0 & (0 | (t |= 0)) > 0)) return 0;
                            for (u = 0, f = 255, l = o |= 0, s = e |= 0;;) {
                                e = 0, c = f;
                                do {
                                    o = 0 | i[s + e >> 0], i[l + (e << 2) >> 0] = o, c &= 255 & o, e = e + 1 | 0
                                } while ((0 | e) != (0 | t));
                                if ((0 | (u = u + 1 | 0)) == (0 | n)) break;
                                f = c, l = l + a | 0, s = s + r | 0
                            }
                            return 0 | 1 & 255 != (0 | c)
                        }, function(e, r, t, n, o, a) {
                            r |= 0, a |= 0;
                            var u = 0,
                                f = 0,
                                l = 0,
                                s = 0,
                                c = 0;
                            if (!((0 | (n |= 0)) > 0 & (0 | (t |= 0)) > 0)) return 1;
                            for (u = 0, f = -1, l = o |= 0, s = e |= 0;;) {
                                e = 0, c = f;
                                do {
                                    o = 0 | i[s + (e << 2) >> 0], i[l + e >> 0] = o, c &= o, e = e + 1 | 0
                                } while ((0 | e) != (0 | t));
                                if ((0 | (u = u + 1 | 0)) == (0 | n)) break;
                                f = c, l = l + a | 0, s = s + r | 0
                            }
                            return 0 | 1 & c << 24 >> 24 == -1
                        }, function(e, r, t, n, i, o) {
                            t |= 0, n |= 0, o |= 0;
                            var u, l, c, d, _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0,
                                O = 0,
                                B = 0,
                                x = 0,
                                N = 0,
                                I = 0,
                                F = 0,
                                H = 0,
                                U = 0,
                                G = 0,
                                W = 0,
                                Y = 0;
                            if ((0 | (i |= 0)) >= 16) return 16;
                            u = 4 + (e |= 0) | 0, l = e + 8 | 0, c = e + 12 | 0, d = e + 20 | 0, h = (0 | f[(r |= 0) + (i << 2) >> 2]) + (11 * t | 0) | 0, t = i, i = 0 | f[l >> 2], m = 0 | f[u >> 2];
                            e: for (;;) {
                                p = 0 | s[h >> 0];
                                do {
                                    if ((0 | i) < 0) {
                                        if ((v = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            b = s[v >> 0] | s[v + 1 >> 0] << 8 | s[v + 2 >> 0] << 16 | s[v + 3 >> 0] << 24, f[c >> 2] = v + 3, v = (0 | wt(0 | b)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | v, v = i + 24 | 0, f[l >> 2] = v, w = v;
                                            break
                                        }
                                        ke(e), w = 0 | f[l >> 2];
                                        break
                                    }
                                    w = i
                                } while (0);
                                if (S = 1 + (v = (0 | A(m, p)) >>> 8) | 0, (b = 0 | f[e >> 2]) >>> w >>> 0 <= v >>> 0) {
                                    k = 29;
                                    break
                                }
                                for (E = m - v | 0, v = b - (S << w) | 0, f[e >> 2] = v, M = w - (b = 24 ^ (0 | y(0 | E))) | 0, f[l >> 2] = M, L = (E << b) - 1 | 0, f[u >> 2] = L, b = h, E = t, g = M, M = v, v = L;;) {
                                    L = 0 | s[b + 1 >> 0];
                                    do {
                                        if ((0 | g) < 0) {
                                            if ((T = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                                D = s[T >> 0] | s[T + 1 >> 0] << 8 | s[T + 2 >> 0] << 16 | s[T + 3 >> 0] << 24, f[c >> 2] = T + 3, T = M << 24 | (0 | wt(0 | D)) >>> 8, f[e >> 2] = T, D = g + 24 | 0, f[l >> 2] = D, C = T, P = D;
                                                break
                                            }
                                            ke(e), C = 0 | f[e >> 2], P = 0 | f[l >> 2];
                                            break
                                        }
                                        C = M, P = g
                                    } while (0);
                                    if (R = 1 + (D = (0 | A(v, L)) >>> 8) | 0, (T = C >>> P >>> 0 > D >>> 0) ? (O = C - (R << P) | 0, f[e >> 2] = O, B = v - D | 0, x = O) : (B = R, x = C), g = P - (R = 24 ^ (0 | y(0 | B))) | 0, f[l >> 2] = g, v = (B << R) - 1 | 0, f[u >> 2] = v, I = 0 | f[r + ((N = E + 1 | 0) << 2) >> 2], T) break;
                                    if (16 == (0 | N)) {
                                        _ = 16, k = 30;
                                        break e
                                    }
                                    b = I, E = N, M = x
                                }
                                M = 0 | s[b + 2 >> 0];
                                do {
                                    if ((0 | g) < 0) {
                                        if ((p = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            T = s[p >> 0] | s[p + 1 >> 0] << 8 | s[p + 2 >> 0] << 16 | s[p + 3 >> 0] << 24, f[c >> 2] = p + 3, p = x << 24 | (0 | wt(0 | T)) >>> 8, f[e >> 2] = p, T = g + 24 | 0, f[l >> 2] = T, F = p, H = T;
                                            break
                                        }
                                        ke(e), F = 0 | f[e >> 2], H = 0 | f[l >> 2];
                                        break
                                    }
                                    F = x, H = g
                                } while (0);
                                T = 1 + (g = (0 | A(M, v)) >>> 8) | 0, F >>> H >>> 0 > g >>> 0 ? (p = v - g | 0, f[e >> 2] = F - (T << H), g = 24 ^ (0 | y(0 | p)), f[l >> 2] = H - g, f[u >> 2] = (p << g) - 1, U = g = 0 | ge(e, b), G = 2, W = 0 | f[l >> 2]) : (p = H - (g = 24 ^ (0 | y(0 | T))) | 0, f[l >> 2] = p, f[u >> 2] = (T << g) - 1, U = 1, G = 1, W = p), h = I + (11 * G | 0) | 0;
                                do {
                                    if ((0 | W) < 0) {
                                        if ((p = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            g = s[p >> 0] | s[p + 1 >> 0] << 8 | s[p + 2 >> 0] << 16 | s[p + 3 >> 0] << 24, f[c >> 2] = p + 3, p = (0 | wt(0 | g)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | p, p = W + 24 | 0, f[l >> 2] = p, Y = p;
                                            break
                                        }
                                        ke(e), Y = 0 | f[l >> 2];
                                        break
                                    }
                                    Y = W
                                } while (0);
                                if (p = (v = (b = 0 | f[u >> 2]) >>> 1) - ((M = 0 | f[e >> 2]) >>> Y) >> 31, i = Y + -1 | 0, f[l >> 2] = i, m = p + b | 1, f[u >> 2] = m, f[e >> 2] = M - ((p & v + 1) << Y), v = 65535 & (0 | A((p ^ U) - p | 0, 0 | f[n + (((0 | E) > 0 & 1) << 2) >> 2])), a[o + ((0 | s[8924 + E >> 0]) << 1) >> 1] = v, (0 | N) >= 16) {
                                    _ = 16, k = 30;
                                    break
                                }
                                t = N
                            }
                            return 29 == (0 | k) ? (N = 24 ^ (0 | y(0 | S)), f[l >> 2] = w - N, f[u >> 2] = (S << N) - 1, 0 | (_ = t)) : 30 == (0 | k) ? 0 | _ : 0
                        }, function(e, r, t, n, i, o) {
                            t |= 0, n |= 0, o |= 0;
                            var u, l, c, d, _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0,
                                O = 0,
                                B = 0,
                                x = 0,
                                N = 0,
                                I = 0,
                                F = 0,
                                H = 0,
                                U = 0,
                                G = 0,
                                W = 0,
                                Y = 0,
                                V = 0,
                                q = 0,
                                j = 0,
                                z = 0,
                                X = 0,
                                K = 0,
                                $ = 0,
                                J = 0,
                                Q = 0,
                                Z = 0,
                                ee = 0,
                                re = 0;
                            if ((0 | (i |= 0)) >= 16) return 16;
                            u = 4 + (e |= 0) | 0, l = e + 8 | 0, c = e + 12 | 0, d = e + 20 | 0, h = (0 | f[(r |= 0) + (i << 2) >> 2]) + (11 * t | 0) | 0, t = i, i = 0 | f[l >> 2], m = 0 | f[u >> 2];
                            e: for (;;) {
                                p = 0 | s[h >> 0];
                                do {
                                    if ((0 | i) < 0) {
                                        if ((v = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            b = s[v >> 0] | s[v + 1 >> 0] << 8 | s[v + 2 >> 0] << 16 | s[v + 3 >> 0] << 24, f[c >> 2] = v + 3, v = (0 | wt(0 | b)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | v, v = i + 24 | 0, f[l >> 2] = v, w = v;
                                            break
                                        }
                                        ke(e), w = 0 | f[l >> 2];
                                        break
                                    }
                                    w = i
                                } while (0);
                                if (S = 1 + (v = (0 | A(m, p)) >>> 8) | 0, (b = 0 | f[e >> 2]) >>> w >>> 0 > v >>> 0 ? (k = b - (S << w) | 0, f[e >> 2] = k, E = m - S | 0, M = 1, y = k) : (E = v, M = 0, y = b), E >>> 0 < 127 ? (b = 0 | s[5047 + E >> 0], v = w - (0 | s[4919 + E >> 0]) | 0, f[l >> 2] = v, L = b, g = v) : (L = E, g = w), f[u >> 2] = L, !M) {
                                    _ = t, T = 37;
                                    break
                                }
                                for (D = h, C = t, P = g, R = y, O = L;;) {
                                    v = 0 | s[D + 1 >> 0];
                                    do {
                                        if ((0 | P) < 0) {
                                            if ((b = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                                k = s[b >> 0] | s[b + 1 >> 0] << 8 | s[b + 2 >> 0] << 16 | s[b + 3 >> 0] << 24, f[c >> 2] = b + 3, b = R << 24 | (0 | wt(0 | k)) >>> 8, f[e >> 2] = b, k = P + 24 | 0, f[l >> 2] = k, B = b, x = k;
                                                break
                                            }
                                            ke(e), B = 0 | f[e >> 2], x = 0 | f[l >> 2];
                                            break
                                        }
                                        B = R, x = P
                                    } while (0);
                                    if (b = 1 + (k = (0 | A(O, v)) >>> 8) | 0, B >>> x >>> 0 > k >>> 0 ? (S = B - (b << x) | 0, f[e >> 2] = S, N = O - b | 0, I = 1, F = S) : (N = k, I = 0, F = B), N >>> 0 < 127 ? (k = 0 | s[5047 + N >> 0], S = x - (0 | s[4919 + N >> 0]) | 0, f[l >> 2] = S, H = k, U = S) : (H = N, U = x), f[u >> 2] = H, W = 0 | f[r + ((G = C + 1 | 0) << 2) >> 2], 0 | I) break;
                                    if (16 == (0 | G)) {
                                        _ = 16, T = 37;
                                        break e
                                    }
                                    D = W, C = G, P = U, R = F, O = H
                                }
                                p = 0 | s[D + 2 >> 0];
                                do {
                                    if ((0 | U) < 0) {
                                        if ((S = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            k = s[S >> 0] | s[S + 1 >> 0] << 8 | s[S + 2 >> 0] << 16 | s[S + 3 >> 0] << 24, f[c >> 2] = S + 3, S = F << 24 | (0 | wt(0 | k)) >>> 8, f[e >> 2] = S, k = U + 24 | 0, f[l >> 2] = k, Y = S, V = k;
                                            break
                                        }
                                        ke(e), Y = 0 | f[e >> 2], V = 0 | f[l >> 2];
                                        break
                                    }
                                    Y = F, V = U
                                } while (0);
                                S = 1 + (k = (0 | A(p, H)) >>> 8) | 0, Y >>> V >>> 0 > k >>> 0 ? (f[e >> 2] = Y - (S << V), q = H - S | 0, j = 1) : (q = k, j = 0), q >>> 0 < 127 ? (k = 0 | s[5047 + q >> 0], S = V - (0 | s[4919 + q >> 0]) | 0, f[l >> 2] = S, z = k, X = S) : (z = q, X = V), f[u >> 2] = z, j ? (K = S = 0 | ge(e, D), $ = 2, J = 0 | f[l >> 2]) : (K = 1, $ = 1, J = X), S = W + (11 * $ | 0) | 0;
                                do {
                                    if ((0 | J) < 0) {
                                        if ((k = 0 | f[c >> 2]) >>> 0 < (0 | f[d >> 2]) >>> 0) {
                                            b = s[k >> 0] | s[k + 1 >> 0] << 8 | s[k + 2 >> 0] << 16 | s[k + 3 >> 0] << 24, f[c >> 2] = k + 3, k = (0 | wt(0 | b)) >>> 8, f[e >> 2] = f[e >> 2] << 24 | k, k = J + 24 | 0, f[l >> 2] = k, Q = k;
                                            break
                                        }
                                        ke(e), Q = 0 | f[l >> 2];
                                        break
                                    }
                                    Q = J
                                } while (0);
                                if (Z = (k = (p = 0 | f[u >> 2]) >>> 1) - ((b = 0 | f[e >> 2]) >>> Q) >> 31, ee = Q + -1 | 0, f[l >> 2] = ee, re = Z + p | 1, f[u >> 2] = re, f[e >> 2] = b - ((Z & k + 1) << Q), k = 65535 & (0 | A((Z ^ K) - Z | 0, 0 | f[n + (((0 | C) > 0 & 1) << 2) >> 2])), a[o + ((0 | s[8924 + C >> 0]) << 1) >> 1] = k, !((0 | G) < 16)) {
                                    _ = 16, T = 37;
                                    break
                                }
                                h = S, t = G, i = ee, m = re
                            }
                            return 37 == (0 | T) ? 0 | _ : 0
                        }, Mt, Mt, Mt],
                        Bt = [At, function(e) {
                            var r, t, n = 0,
                                i = 0;
                            switch (f[(r = 20 + (e |= 0) | 0) >> 2] = 0, 0 | f[(t = e + 4 | 0) >> 2]) {
                                case 0:
                                    if (n = 0 | $e(1, 0, 80), f[e >> 2] = n, !n) return 0;
                                    do {
                                        if (!(0 | Q(0 | n, 0))) {
                                            if (0 | O(0 | (i = n + 28 | 0), 0)) {
                                                G(0 | n);
                                                break
                                            }
                                            if (0 | j(n + 76 | 0, 0, 6, 0 | e)) {
                                                G(0 | n), N(0 | i);
                                                break
                                            }
                                            return f[t >> 2] = 1, 1
                                        }
                                    } while (0);
                                    return Ve(n), f[e >> 2] = 0, 0;
                                case 1:
                                    return 1;
                                default:
                                    if (!(n = 0 | f[e >> 2])) return 1;
                                    if ((0 | f[t >> 2]) >>> 0 >= 2) {
                                        e = n + 28 | 0;
                                        do {
                                            C(0 | e, 0 | n)
                                        } while (1 != (0 | f[t >> 2]))
                                    }
                                    return 0 | 1 & 0 == (0 | f[r >> 2])
                            }
                            return 0
                        }, function(e) {
                            var r, t = 0,
                                n = 0;
                            if (0 | (r = 0 | f[(e |= 0) >> 2]) && (0 | f[(t = e + 4 | 0) >> 2]) >>> 0 >= 2) {
                                n = r + 28 | 0;
                                do {
                                    C(0 | n, 0 | r)
                                } while (1 != (0 | f[t >> 2]))
                            }
                            return 0 == (0 | f[e + 20 >> 2]) | 0
                        }, function(e) {
                            var r, t, n = 0;
                            return r = S, S = S + 16 | 0, t = r, n = 0 | function(e) {
                                return 0 | e
                            }(0 | f[60 + (e |= 0) >> 2]), f[t >> 2] = n, n = 0 | Be(0 | $(6, 0 | t)), S = r, 0 | n
                        }, function(e) {
                            var r, t, n;
                            return r = 0 | f[40 + (e |= 0) >> 2], (0 | f[e + 12 >> 2]) < 1 || (0 | f[e + 16 >> 2]) < 1 ? 0 : (t = 0 | Ht[31 & f[r + 44 >> 2]](e, r), 0 | (n = 0 | f[r + 48 >> 2]) && Dt[15 & n](e, r, t), f[(e = r + 16 | 0) >> 2] = (0 | f[e >> 2]) + t, 1)
                        }, function(e) {
                            var r, t, n, i, o, a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0;
                            if (r = 0 | f[40 + (e |= 0) >> 2], u = (a = 0 | f[f[r >> 2] >> 2]) >>> 0 < 11, s = (l = a + -1 | 0) >>> 0 < 12 && 0 != (2077 >>> (65535 & l) & 1) ? 1 : (a + -7 | 0) >>> 0 < 4, t = r + 44 | 0, n = r + 48 | 0, i = r + 52 | 0, f[(l = r + 40 | 0) >> 2] = 0, f[l + 4 >> 2] = 0, f[l + 8 >> 2] = 0, f[l + 12 >> 2] = 0, !(0 | wr(0 | f[r + 20 >> 2], e, s ? 11 : 12))) return 0;
                            if ((a + -7 | 0) >>> 0 < 4 & s && Sr(), !(0 | f[e + 92 >> 2])) {
                                do {
                                    if (u) {
                                        if (Er(), f[t >> 2] = 19, 0 | f[e + 56 >> 2]) {
                                            if (h = 0 | Je(1, 0, ((_ = 1 + (d = 0 | f[(c = e + 12 | 0) >> 2]) >> 1) << 1) + d | 0), f[l >> 2] = h, h) {
                                                f[r + 4 >> 2] = h, d = h + (0 | f[c >> 2]) | 0, f[r + 8 >> 2] = d, f[r + 12 >> 2] = d + _, f[t >> 2] = 20, Sr();
                                                break
                                            }
                                            return 0
                                        }
                                    } else f[t >> 2] = 21
                                } while (0);
                                if (!s) return 1;
                                switch (0 | a) {
                                    case 5:
                                    case 10:
                                        m = 10;
                                        break;
                                    default:
                                        m = u ? 7 : 6
                                }
                                return f[n >> 2] = m, u ? (ir(), 1) : 1
                            }
                            if (m = 0 | f[r >> 2], _ = (s = (a = 0 | f[m >> 2]) - 1 | 0) >>> 0 < 12, !u) return p = _ && 0 != (2077 >>> (65535 & s) & 1) ? 1 : (a + -7 | 0) >>> 0 < 4, c = 1 + (u = 0 | f[e + 96 >> 2]) >> 1, h = 1 + (d = 0 | f[e + 100 >> 2]) >> 1, b = 1 + (0 | f[(v = e + 12 | 0) >> 2]) >> 1, S = 1 + (0 | f[(w = e + 16 | 0) >> 2]) >> 1, A = 0 | Je(1, 0, (M = ((E = c << 2) + (k = u << 1) << 2) + (p ? u << 3 : 0) | 0) + (p ? 367 : 283) | 0), f[l >> 2] = A, A ? (y = A + M + 31 & -32, f[r + 24 >> 2] = y, f[(M = r + 28 | 0) >> 2] = y + 84, f[(L = r + 32 | 0) >> 2] = y + 168, f[(g = r + 36 | 0) >> 2] = p ? y + 252 | 0 : 0, kr(y, 0 | f[v >> 2], 0 | f[w >> 2], 0 | f[m + 16 >> 2], u, d, 0 | f[m + 32 >> 2], 1, A), y = A + (k << 2) | 0, kr(0 | f[M >> 2], b, S, 0 | f[m + 20 >> 2], c, h, 0 | f[m + 36 >> 2], 1, y), kr(0 | f[L >> 2], b, S, 0 | f[m + 24 >> 2], c, h, 0 | f[m + 40 >> 2], 1, y + (c << 1 << 2) | 0), f[t >> 2] = 18, p ? (kr(0 | f[g >> 2], 0 | f[v >> 2], 0 | f[w >> 2], 0 | f[m + 28 >> 2], u, d, 0 | f[m + 44 >> 2], 1, y + (E << 2) | 0), f[n >> 2] = 5, ir(), 1) : 1) : 0;
                            if (o = _ && 0 != (2077 >>> (65535 & s) & 1) ? 1 : (a + -7 | 0) >>> 0 < 4, a = 0 | f[e + 96 >> 2], s = 0 | f[e + 100 >> 2], E = 1 + (0 | f[(_ = e + 12 | 0) >> 2]) >> 1, e = 1 + (0 | f[(y = e + 16 | 0) >> 2]) >> 1, m = a << 1, d = 6 * a | 0, u = 3 * a | 0, w = a << 2, p = 0 | Je(1, 0, (g = ((v = o ? a << 3 : d) << 2) + (o ? w : u) | 0) + (o ? 367 : 283) | 0), f[l >> 2] = p, !p) return 0;
                            if (l = p + (v << 2) | 0, v = p + g + 31 & -32, f[r + 24 >> 2] = v, f[(g = r + 28 | 0) >> 2] = v + 84, f[(c = r + 32 | 0) >> 2] = v + 168, f[(h = r + 36 | 0) >> 2] = o ? v + 252 | 0 : 0, kr(v, 0 | f[_ >> 2], 0 | f[y >> 2], l, a, s, 0, 1, p), kr(0 | f[g >> 2], E, e, l + a | 0, a, s, 0, 1, p + (m << 2) | 0), kr(0 | f[c >> 2], E, e, l + m | 0, a, s, 0, 1, p + (w << 2) | 0), f[t >> 2] = 17, function() {
                                var e = 0;
                                e = 0 | f[2893], (0 | f[34]) != (0 | e) && (f[3036] = 5, f[3038] = 6, f[3035] = 7, f[3037] = 8, f[3039] = 9, f[3040] = 10, f[3041] = 11, f[3042] = 5, f[3043] = 6, f[3044] = 9, f[3045] = 10), f[34] = e
                            }(), !o) return 1;
                            switch (kr(0 | f[h >> 2], 0 | f[_ >> 2], 0 | f[y >> 2], l + u | 0, a, s, 0, 1, p + (d << 2) | 0), f[n >> 2] = 4, 0 | f[f[r >> 2] >> 2]) {
                                case 10:
                                case 5:
                                    T = 8;
                                    break;
                                default:
                                    T = 9
                            }
                            return f[i >> 2] = T, ir(), 1
                        }, function(e) {
                            var r, t, n, i = 0;
                            t = 4 + (e |= 0) | 0, n = 28 + (r = 0 | f[e >> 2]) | 0;
                            e: for (;;) {
                                r: for (;;) {
                                    switch (0 | f[t >> 2]) {
                                        case 0:
                                            break e;
                                        case 2:
                                            i = 5;
                                            break r;
                                        case 1:
                                            break;
                                        default:
                                            i = 6;
                                            break r
                                    }
                                    C(0 | n, 0 | r)
                                }
                                5 != (0 | i) ? 6 != (0 | i) || (i = 0, U(0 | n)) : (i = 0, Pt[31 & f[97]](e), f[t >> 2] = 1, U(0 | n))
                            }
                            return U(0 | n), 0
                        }, At],
                        xt = [yt, function(e, r, t) {
                            e |= 0, t |= 0;
                            var n = 0,
                                i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            if (!((0 | (r |= 0)) <= 0))
                                if (t) {
                                    t = 0;
                                    do {
                                        (n = 0 | f[(o = e + (t << 2) | 0) >> 2]) >>> 0 < 4278190080 && (u = n >>> 0 < 16777216 ? 0 : (8388608 + (0 | A(i = 4278190080 / (n >>> 24 >>> 0) | 0, 255 & n)) | 0) >>> 24 | -16777216 & n | (8388608 + (0 | A(i, n >>> 8 & 255)) | 0) >>> 24 << 8 | (8388608 + (0 | A(i, n >>> 16 & 255)) | 0) >>> 24 << 16, f[o >> 2] = u), t = t + 1 | 0
                                    } while ((0 | t) != (0 | r))
                                } else {
                                    t = 0;
                                    do {
                                        (i = 0 | f[(n = e + (t << 2) | 0) >> 2]) >>> 0 < 4278190080 && (o = i >>> 0 < 16777216 ? 0 : (8388608 + (0 | A(a = 65793 * (i >>> 24) | 0, 255 & i)) | 0) >>> 24 | -16777216 & i | (8388608 + (0 | A(a, i >>> 8 & 255)) | 0) >>> 24 << 8 | (8388608 + (0 | A(a, i >>> 16 & 255)) | 0) >>> 24 << 16, f[n >> 2] = o), t = t + 1 | 0
                                    } while ((0 | t) != (0 | r))
                                }
                        }, function(e, r, t) {
                            e |= 0, r |= 0;
                            var n = 0;
                            if ((0 | (t |= 0)) > 0) {
                                n = 0;
                                do {
                                    i[r + n >> 0] = (0 | f[e + (n << 2) >> 2]) >>> 8, n = n + 1 | 0
                                } while ((0 | n) != (0 | t))
                            }
                        }, function(e, r, t) {
                            e |= 0, t |= 0;
                            var n = 0,
                                i = 0,
                                o = 0;
                            if ((0 | (r |= 0)) > 0) {
                                n = 0;
                                do {
                                    o = (i = 0 | f[e + (n << 2) >> 2]) >>> 8 & 255, f[t + (n << 2) >> 2] = (o << 16 | o) + (16711935 & i) & 16711935 | -16711936 & i, n = n + 1 | 0
                                } while ((0 | n) != (0 | r))
                            }
                        }, function(e, r, t) {
                            var n, o = 0,
                                a = 0;
                            if (n = (e |= 0) + ((r |= 0) << 2) | 0, (0 | r) > 0)
                                for (o = t |= 0, a = e; e = 0 | f[a >> 2], a = a + 4 | 0, i[o >> 0] = e >>> 16, i[o + 1 >> 0] = e >>> 8, i[o + 2 >> 0] = e, i[o + 3 >> 0] = e >>> 24, !(a >>> 0 >= n >>> 0);) o = o + 4 | 0
                        }, function(e, r, t) {
                            var n, o = 0,
                                a = 0;
                            if (n = (e |= 0) + ((r |= 0) << 2) | 0, (0 | r) > 0)
                                for (o = t |= 0, a = e; e = 0 | f[a >> 2], a = a + 4 | 0, i[o >> 0] = e >>> 16, i[o + 1 >> 0] = e >>> 8, i[o + 2 >> 0] = e, !(a >>> 0 >= n >>> 0);) o = o + 3 | 0
                        }, function(e, r, t) {
                            var n, o = 0,
                                a = 0;
                            if (n = (e |= 0) + ((r |= 0) << 2) | 0, (0 | r) > 0)
                                for (o = t |= 0, a = e; e = 0 | f[a >> 2], a = a + 4 | 0, i[o >> 0] = e, i[o + 1 >> 0] = e >>> 8, i[o + 2 >> 0] = e >>> 16, !(a >>> 0 >= n >>> 0);) o = o + 3 | 0
                        }, function(e, r, t) {
                            var n, o = 0,
                                a = 0;
                            if (n = (e |= 0) + ((r |= 0) << 2) | 0, (0 | r) > 0)
                                for (o = t |= 0, a = e; e = 0 | f[a >> 2], a = a + 4 | 0, i[o >> 0] = e >>> 16 & 240 | e >>> 12 & 15, i[o + 1 >> 0] = 240 & e | e >>> 28, !(a >>> 0 >= n >>> 0);) o = o + 2 | 0
                        }, function(e, r, t) {
                            var n, o = 0,
                                a = 0;
                            if (n = (e |= 0) + ((r |= 0) << 2) | 0, (0 | r) > 0)
                                for (o = t |= 0, a = e; e = 0 | f[a >> 2], a = a + 4 | 0, i[o >> 0] = e >>> 16 & 248 | e >>> 13 & 7, i[o + 1 >> 0] = e >>> 5 & 224 | e >>> 3 & 31, !(a >>> 0 >= n >>> 0);) o = o + 2 | 0
                        }, function(e, r, t) {
                            t |= 0, le(e |= 0, r |= 0), t && le(e + 32 | 0, r + 4 | 0)
                        }, function(e, r, t) {
                            e |= 0;
                            var n, i, o, a = 0,
                                u = 0;
                            n = (t |= 0) << 1 | 1, t = 0 | A(r |= 0, -2), i = 0 - r | 0, o = 0 | f[5], a = 0;
                            do {
                                (((0 | s[o + ((0 | s[(u = e + a | 0) + i >> 0]) - (0 | s[u >> 0])) >> 0]) << 2) + (0 | s[o + ((0 | s[u + t >> 0]) - (0 | s[u + r >> 0])) >> 0]) | 0) <= (0 | n) && fe(u, r), a = a + 1 | 0
                            } while (16 != (0 | a))
                        }, function(e, r, t) {
                            e |= 0, r |= 0;
                            var n, o, a, u, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0;
                            n = (t |= 0) << 1 | 1, t = 0 | f[5], o = 0 | f[2], a = 0 | f[3], u = 0 | f[4], l = 0;
                            do {
                                c = e + (0 | A(l, r)) | 0, _ = 0 | s[(d = c + -1 | 0) >> 0], h = 0 | s[c >> 0], m = (0 | s[c + -2 >> 0]) - (0 | s[c + 1 >> 0]) | 0, ((s[t + (_ - h) >> 0] << 2) + (0 | s[t + m >> 0]) | 0) <= (0 | n) && (p = (0 | i[o + m >> 0]) + (3 * (h - _ | 0) | 0) | 0, m = 0 | i[a + (p + 4 >> 3) >> 0], i[d >> 0] = 0 | i[u + ((0 | i[a + (p + 3 >> 3) >> 0]) + _) >> 0], i[c >> 0] = 0 | i[u + (h - m) >> 0]), l = l + 1 | 0
                            } while (16 != (0 | l))
                        }, function(e, r, t) {
                            e |= 0;
                            var n, o, a, u, l, c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0;
                            d = (r |= 0) << 2, n = (t |= 0) << 1 | 1, t = 0 | A(r, -2), o = 0 - r | 0, a = 0 | f[5], u = 0 | f[2], l = 0 | f[3], c = 0 | f[4], _ = e + d | 0, e = 0;
                            do {
                                p = 0 | s[(m = (h = _ + e | 0) + o | 0) >> 0], v = 0 | s[h >> 0], b = (0 | s[h + t >> 0]) - (0 | s[h + r >> 0]) | 0, ((s[a + (p - v) >> 0] << 2) + (0 | s[a + b >> 0]) | 0) <= (0 | n) && (w = (0 | i[u + b >> 0]) + (3 * (v - p | 0) | 0) | 0, b = 0 | i[l + (w + 4 >> 3) >> 0], i[m >> 0] = 0 | i[c + ((0 | i[l + (w + 3 >> 3) >> 0]) + p) >> 0], i[h >> 0] = 0 | i[c + (v - b) >> 0]), e = e + 1 | 0
                            } while (16 != (0 | e));
                            e = _ + d | 0, _ = 0;
                            do {
                                h = 0 | s[(v = (b = e + _ | 0) + o | 0) >> 0], p = 0 | s[b >> 0], w = (0 | s[b + t >> 0]) - (0 | s[b + r >> 0]) | 0, ((s[a + (h - p) >> 0] << 2) + (0 | s[a + w >> 0]) | 0) <= (0 | n) && (m = (0 | i[u + w >> 0]) + (3 * (p - h | 0) | 0) | 0, w = 0 | i[l + (m + 4 >> 3) >> 0], i[v >> 0] = 0 | i[c + ((0 | i[l + (m + 3 >> 3) >> 0]) + h) >> 0], i[b >> 0] = 0 | i[c + (p - w) >> 0]), _ = _ + 1 | 0
                            } while (16 != (0 | _));
                            _ = e + d | 0, d = 0;
                            do {
                                p = 0 | s[(w = (e = _ + d | 0) + o | 0) >> 0], b = 0 | s[e >> 0], h = (0 | s[e + t >> 0]) - (0 | s[e + r >> 0]) | 0, ((s[a + (p - b) >> 0] << 2) + (0 | s[a + h >> 0]) | 0) <= (0 | n) && (m = (0 | i[u + h >> 0]) + (3 * (b - p | 0) | 0) | 0, h = 0 | i[l + (m + 4 >> 3) >> 0], i[w >> 0] = 0 | i[c + ((0 | i[l + (m + 3 >> 3) >> 0]) + p) >> 0], i[e >> 0] = 0 | i[c + (b - h) >> 0]), d = d + 1 | 0
                            } while (16 != (0 | d))
                        }, function(e, r, t) {
                            e |= 0, r |= 0;
                            var n, o, a, u, l = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0;
                            n = (t |= 0) << 1 | 1, t = 0 | f[5], o = 0 | f[2], a = 0 | f[3], u = 0 | f[4], l = e + 4 | 0, c = 0;
                            do {
                                d = l + (0 | A(c, r)) | 0, h = 0 | s[(_ = d + -1 | 0) >> 0], m = 0 | s[d >> 0], p = (0 | s[d + -2 >> 0]) - (0 | s[d + 1 >> 0]) | 0, ((s[t + (h - m) >> 0] << 2) + (0 | s[t + p >> 0]) | 0) <= (0 | n) && (v = (0 | i[o + p >> 0]) + (3 * (m - h | 0) | 0) | 0, p = 0 | i[a + (v + 4 >> 3) >> 0], i[_ >> 0] = 0 | i[u + ((0 | i[a + (v + 3 >> 3) >> 0]) + h) >> 0], i[d >> 0] = 0 | i[u + (m - p) >> 0]), c = c + 1 | 0
                            } while (16 != (0 | c));
                            c = e + 8 | 0, l = 0;
                            do {
                                p = c + (0 | A(l, r)) | 0, d = 0 | s[(m = p + -1 | 0) >> 0], h = 0 | s[p >> 0], v = (0 | s[p + -2 >> 0]) - (0 | s[p + 1 >> 0]) | 0, ((s[t + (d - h) >> 0] << 2) + (0 | s[t + v >> 0]) | 0) <= (0 | n) && (_ = (0 | i[o + v >> 0]) + (3 * (h - d | 0) | 0) | 0, v = 0 | i[a + (_ + 4 >> 3) >> 0], i[m >> 0] = 0 | i[u + ((0 | i[a + (_ + 3 >> 3) >> 0]) + d) >> 0], i[p >> 0] = 0 | i[u + (h - v) >> 0]), l = l + 1 | 0
                            } while (16 != (0 | l));
                            l = e + 12 | 0, e = 0;
                            do {
                                c = l + (0 | A(e, r)) | 0, h = 0 | s[(v = c + -1 | 0) >> 0], p = 0 | s[c >> 0], d = (0 | s[c + -2 >> 0]) - (0 | s[c + 1 >> 0]) | 0, ((s[t + (h - p) >> 0] << 2) + (0 | s[t + d >> 0]) | 0) <= (0 | n) && (_ = (0 | i[o + d >> 0]) + (3 * (p - h | 0) | 0) | 0, d = 0 | i[a + (_ + 4 >> 3) >> 0], i[v >> 0] = 0 | i[u + ((0 | i[a + (_ + 3 >> 3) >> 0]) + h) >> 0], i[c >> 0] = 0 | i[u + (p - d) >> 0]), e = e + 1 | 0
                            } while (16 != (0 | e))
                        }, function(e, r, t) {
                            e |= 0, r |= 0;
                            var n = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                f = 0;
                            if (1 == (0 | (t |= 0)))
                                for (a = r, n = 0, o = e, e = 0 | i[r + 6 >> 0]; r = ((0 | s[o >> 0]) - 120 >> 4) + (0 | s[a >> 0]) | 0, i[a >> 0] = r >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r, r = a + 1 | 0, f = ((0 | s[o + 1 >> 0]) - 120 >> 4) + (0 | s[r >> 0]) | 0, i[r >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = a + 2 | 0, r = ((0 | s[o + 2 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0, i[f >> 0] = r >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r, r = a + 3 | 0, f = ((0 | s[o + 3 >> 0]) - 120 >> 4) + (0 | s[r >> 0]) | 0, i[r >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = a + 4 | 0, r = ((0 | s[o + 4 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0, i[f >> 0] = r >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r, r = a + 5 | 0, f = ((0 | s[o + 5 >> 0]) - 120 >> 4) + (0 | s[r >> 0]) | 0, i[r >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = ((0 | s[o + 6 >> 0]) - 120 >> 4) + (255 & e) | 0, i[a + 6 >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = a + 7 | 0, e = 255 & ((r = ((0 | s[o + 7 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0) >>> 0 > 255 ? 255 + (r >>> 31) | 0 : r), i[f >> 0] = e, 8 != (0 | (n = n + 1 | 0));) a = a + t | 0, o = o + 8 | 0;
                            else
                                for (n = r, o = 0, a = e; u = ((0 | s[a >> 0]) - 120 >> 4) + (0 | s[n >> 0]) | 0, i[n >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, u = n + 1 | 0, f = ((0 | s[a + 1 >> 0]) - 120 >> 4) + (0 | s[u >> 0]) | 0, i[u >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = n + 2 | 0, u = ((0 | s[a + 2 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0, i[f >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, u = n + 3 | 0, f = ((0 | s[a + 3 >> 0]) - 120 >> 4) + (0 | s[u >> 0]) | 0, i[u >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = n + 4 | 0, u = ((0 | s[a + 4 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0, i[f >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, u = n + 5 | 0, f = ((0 | s[a + 5 >> 0]) - 120 >> 4) + (0 | s[u >> 0]) | 0, i[u >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, f = n + 6 | 0, u = ((0 | s[a + 6 >> 0]) - 120 >> 4) + (0 | s[f >> 0]) | 0, i[f >> 0] = u >>> 0 > 255 ? 255 + (u >>> 31) | 0 : u, u = n + 7 | 0, f = ((0 | s[a + 7 >> 0]) - 120 >> 4) + (0 | s[u >> 0]) | 0, i[u >> 0] = f >>> 0 > 255 ? 255 + (f >>> 31) | 0 : f, 8 != (0 | (o = o + 1 | 0));) n = n + t | 0, a = a + 8 | 0
                        }, function(e, r, t) {
                            e |= 0, r |= 0;
                            var n = 0,
                                o = 0;
                            if ((0 | (t |= 0)) > 0) {
                                n = 0;
                                do {
                                    o = 0 | f[e + (n << 2) >> 2], i[r + n >> 0] = (1081344 + (6420 * (255 & o) | 0) + (16839 * (o >>> 16 & 255) | 0) + (33059 * (o >>> 8 & 255) | 0) | 0) >>> 16, n = n + 1 | 0
                                } while ((0 | n) != (0 | t))
                            }
                        }, function(e, r, t) {
                            r |= 0;
                            var n = 0,
                                o = 0;
                            if ((0 | (t |= 0)) > 0)
                                for (n = 0, o = e |= 0; i[r + n >> 0] = (1081344 + (16839 * (0 | s[o >> 0]) | 0) + (33059 * (0 | s[o + 1 >> 0]) | 0) + (6420 * (0 | s[o + 2 >> 0]) | 0) | 0) >>> 16, (0 | (n = n + 1 | 0)) != (0 | t);) o = o + 3 | 0
                        }, function(e, r, t) {
                            r |= 0;
                            var n = 0,
                                o = 0;
                            if ((0 | (t |= 0)) > 0)
                                for (n = 0, o = e |= 0; i[r + n >> 0] = (1081344 + (16839 * (0 | s[o + 2 >> 0]) | 0) + (33059 * (0 | s[o + 1 >> 0]) | 0) + (6420 * (0 | s[o >> 0]) | 0) | 0) >>> 16, (0 | (n = n + 1 | 0)) != (0 | t);) o = o + 3 | 0
                        }, yt, yt, yt, yt, yt, yt, yt, yt, yt, yt, yt, yt, yt, yt],
                        Nt = [function(e, r, t, n, i, o, a, u, f) {
                            L(7)
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0;
                            if (c = (d = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18 & 255, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 14234 + ((26149 * v | 0) >>> 8) | 0, i[u >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + 8708 - ((6419 * p | 0) >>> 8) - ((13320 * v | 0) >>> 8) | 0, i[u + 1 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + -17685 + ((33050 * p | 0) >>> 8) | 0, i[u + 2 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, i[u + 3 >> 0] = -1, (b = 0 != (0 | r)) && (m = (p = _ + 131074 + (3 * h | 0) | 0) >>> 2 & 255, w = ((26149 * (v = p >>> 18 & 255) | 0) >>> 8) - 14234 + (p = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, i[f >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = 8708 - ((6419 * m | 0) >>> 8) - ((13320 * v | 0) >>> 8) + p | 0, i[f + 1 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = ((33050 * m | 0) >>> 8) - 17685 + p | 0, i[f + 2 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, i[f + 3 >> 0] = -1), (0 | c) < 1) S = _, k = h;
                            else
                                for (w = _, _ = h, h = 1;;) {
                                    if (A = (M = ((v = _ + 524296 + w + (p = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (m = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (m + w << 1) | 0) >>> 3) + p | 0, T = u + (g = (L = (y = h << 1) - 1 | 0) << 2) | 0, D = (v = (E = (v + (p + _ << 1) | 0) >>> 3) + w | 0) >>> 1 & 255, C = v >>> 17 & 255, P = (v = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 14234 + ((26149 * C | 0) >>> 8) | 0, i[T >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = v + 8708 - ((6419 * D | 0) >>> 8) - ((13320 * C | 0) >>> 8) | 0, i[T + 1 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = v + -17685 + ((33050 * D | 0) >>> 8) | 0, i[T + 2 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, i[T + 3 >> 0] = -1, P = u + (T = h << 3) | 0, D = A >>> 1 & 255, v = A >>> 17 & 255, C = (A = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 14234 + ((26149 * v | 0) >>> 8) | 0, i[P >> 0] = C >>> 0 < 16384 ? C >>> 6 : 255 + (C >>> 31) | 0, C = A + 8708 - ((6419 * D | 0) >>> 8) - ((13320 * v | 0) >>> 8) | 0, i[P + 1 >> 0] = C >>> 0 < 16384 ? C >>> 6 : 255 + (C >>> 31) | 0, C = A + -17685 + ((33050 * D | 0) >>> 8) | 0, i[P + 2 >> 0] = C >>> 0 < 16384 ? C >>> 6 : 255 + (C >>> 31) | 0, i[P + 3 >> 0] = -1, b && (P = M + _ | 0, M = E + m | 0, E = f + g | 0, g = P >>> 1 & 255, L = ((26149 * (C = P >>> 17 & 255) | 0) >>> 8) - 14234 + (P = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, i[E >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = 8708 - ((6419 * g | 0) >>> 8) - ((13320 * C | 0) >>> 8) + P | 0, i[E + 1 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = ((33050 * g | 0) >>> 8) - 17685 + P | 0, i[E + 2 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, i[E + 3 >> 0] = -1, E = f + T | 0, T = M >>> 1 & 255, y = ((26149 * (L = M >>> 17 & 255) | 0) >>> 8) - 14234 + (M = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, i[E >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = 8708 - ((6419 * T | 0) >>> 8) - ((13320 * L | 0) >>> 8) + M | 0, i[E + 1 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = ((33050 * T | 0) >>> 8) - 17685 + M | 0, i[E + 2 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, i[E + 3 >> 0] = -1), (0 | h) == (0 | c)) {
                                        S = p, k = m;
                                        break
                                    }
                                    w = p, _ = m, h = h + 1 | 0
                                }
                            1 & l | 0 || (_ = u + (h = d << 2) | 0, u = (l = k + 131074 + (3 * S | 0) | 0) >>> 2 & 255, e = ((26149 * (w = l >>> 18 & 255) | 0) >>> 8) - 14234 + (l = (19077 * (0 | s[e + d >> 0]) | 0) >>> 8) | 0, i[_ >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = 8708 - ((6419 * u | 0) >>> 8) - ((13320 * w | 0) >>> 8) + l | 0, i[_ + 1 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = ((33050 * u | 0) >>> 8) - 17685 + l | 0, i[_ + 2 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, i[_ + 3 >> 0] = -1, b && (b = S + 131074 + (3 * k | 0) | 0, k = f + h | 0, h = b >>> 2 & 255, d = ((26149 * (f = b >>> 18 & 255) | 0) >>> 8) - 14234 + (b = (19077 * (0 | s[r + d >> 0]) | 0) >>> 8) | 0, i[k >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = 8708 - ((6419 * h | 0) >>> 8) - ((13320 * f | 0) >>> 8) + b | 0, i[k + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = ((33050 * h | 0) >>> 8) - 17685 + b | 0, i[k + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, i[k + 3 >> 0] = -1))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0;
                            if (c = (d = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18 & 255, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 17685 + ((33050 * p | 0) >>> 8) | 0, i[u >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + 8708 - ((6419 * p | 0) >>> 8) - ((13320 * v | 0) >>> 8) | 0, i[u + 1 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + -14234 + ((26149 * v | 0) >>> 8) | 0, i[u + 2 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, i[u + 3 >> 0] = -1, (b = 0 != (0 | r)) && (p = (v = _ + 131074 + (3 * h | 0) | 0) >>> 18 & 255, w = ((33050 * (m = v >>> 2 & 255) | 0) >>> 8) - 17685 + (v = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, i[f >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = 8708 - ((6419 * m | 0) >>> 8) - ((13320 * p | 0) >>> 8) + v | 0, i[f + 1 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = ((26149 * p | 0) >>> 8) - 14234 + v | 0, i[f + 2 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, i[f + 3 >> 0] = -1), (0 | c) < 1) S = _, k = h;
                            else
                                for (w = _, _ = h, h = 1;;) {
                                    if (A = (M = ((m = _ + 524296 + w + (v = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (p = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (p + w << 1) | 0) >>> 3) + v | 0, T = u + (g = (L = (y = h << 1) - 1 | 0) << 2) | 0, D = (m = (E = (m + (v + _ << 1) | 0) >>> 3) + w | 0) >>> 1 & 255, C = m >>> 17 & 255, P = (m = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 17685 + ((33050 * D | 0) >>> 8) | 0, i[T >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = m + 8708 - ((6419 * D | 0) >>> 8) - ((13320 * C | 0) >>> 8) | 0, i[T + 1 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = m + -14234 + ((26149 * C | 0) >>> 8) | 0, i[T + 2 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, i[T + 3 >> 0] = -1, P = u + (T = h << 3) | 0, C = A >>> 1 & 255, m = A >>> 17 & 255, D = (A = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 17685 + ((33050 * C | 0) >>> 8) | 0, i[P >> 0] = D >>> 0 < 16384 ? D >>> 6 : 255 + (D >>> 31) | 0, D = A + 8708 - ((6419 * C | 0) >>> 8) - ((13320 * m | 0) >>> 8) | 0, i[P + 1 >> 0] = D >>> 0 < 16384 ? D >>> 6 : 255 + (D >>> 31) | 0, D = A + -14234 + ((26149 * m | 0) >>> 8) | 0, i[P + 2 >> 0] = D >>> 0 < 16384 ? D >>> 6 : 255 + (D >>> 31) | 0, i[P + 3 >> 0] = -1, b && (P = M + _ | 0, M = E + p | 0, E = f + g | 0, D = P >>> 17 & 255, L = ((33050 * (g = P >>> 1 & 255) | 0) >>> 8) - 17685 + (P = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, i[E >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = 8708 - ((6419 * g | 0) >>> 8) - ((13320 * D | 0) >>> 8) + P | 0, i[E + 1 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = ((26149 * D | 0) >>> 8) - 14234 + P | 0, i[E + 2 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, i[E + 3 >> 0] = -1, E = f + T | 0, L = M >>> 17 & 255, y = ((33050 * (T = M >>> 1 & 255) | 0) >>> 8) - 17685 + (M = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, i[E >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = 8708 - ((6419 * T | 0) >>> 8) - ((13320 * L | 0) >>> 8) + M | 0, i[E + 1 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = ((26149 * L | 0) >>> 8) - 14234 + M | 0, i[E + 2 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, i[E + 3 >> 0] = -1), (0 | h) == (0 | c)) {
                                        S = v, k = p;
                                        break
                                    }
                                    w = v, _ = p, h = h + 1 | 0
                                }
                            1 & l | 0 || (_ = u + (h = d << 2) | 0, w = (l = k + 131074 + (3 * S | 0) | 0) >>> 18 & 255, e = ((33050 * (u = l >>> 2 & 255) | 0) >>> 8) - 17685 + (l = (19077 * (0 | s[e + d >> 0]) | 0) >>> 8) | 0, i[_ >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = 8708 - ((6419 * u | 0) >>> 8) - ((13320 * w | 0) >>> 8) + l | 0, i[_ + 1 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = ((26149 * w | 0) >>> 8) - 14234 + l | 0, i[_ + 2 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, i[_ + 3 >> 0] = -1, b && (b = S + 131074 + (3 * k | 0) | 0, k = f + h | 0, f = b >>> 18 & 255, d = ((33050 * (h = b >>> 2 & 255) | 0) >>> 8) - 17685 + (b = (19077 * (0 | s[r + d >> 0]) | 0) >>> 8) | 0, i[k >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = 8708 - ((6419 * h | 0) >>> 8) - ((13320 * f | 0) >>> 8) + b | 0, i[k + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = ((26149 * f | 0) >>> 8) - 14234 + b | 0, i[k + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, i[k + 3 >> 0] = -1))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0;
                            if (c = (d = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 14234 + ((26149 * v | 0) >>> 8) | 0, i[u >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + 8708 - ((13320 * v | 0) >>> 8) - ((6419 * p | 0) >>> 8) | 0, i[u + 1 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + -17685 + ((33050 * p | 0) >>> 8) | 0, i[u + 2 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, (b = 0 != (0 | r)) && (m = (p = _ + 131074 + (3 * h | 0) | 0) >>> 2 & 255, w = ((26149 * (v = p >>> 18) | 0) >>> 8) - 14234 + (p = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, i[f >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = 8708 - ((13320 * v | 0) >>> 8) - ((6419 * m | 0) >>> 8) + p | 0, i[f + 1 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = ((33050 * m | 0) >>> 8) - 17685 + p | 0, i[f + 2 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0), (0 | c) < 1) S = _, k = h;
                            else
                                for (w = _, _ = h, h = 1;;) {
                                    if (A = (M = ((v = _ + 524296 + w + (p = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (m = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (m + w << 1) | 0) >>> 3) + p | 0, g = (v = (E = (v + (p + _ << 1) | 0) >>> 3) + w | 0) >>> 1 & 255, T = v >>> 17, D = u + (v = 3 * (L = (y = h << 1) - 1 | 0) | 0) | 0, P = (C = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 14234 + ((26149 * T | 0) >>> 8) | 0, i[D >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = C + 8708 - ((13320 * T | 0) >>> 8) - ((6419 * g | 0) >>> 8) | 0, i[D + 1 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = C + -17685 + ((33050 * g | 0) >>> 8) | 0, i[D + 2 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = A >>> 1 & 255, D = A >>> 17, g = u + (A = 6 * h | 0) | 0, T = (C = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 14234 + ((26149 * D | 0) >>> 8) | 0, i[g >> 0] = T >>> 0 < 16384 ? T >>> 6 : 255 + (T >>> 31) | 0, T = C + 8708 - ((13320 * D | 0) >>> 8) - ((6419 * P | 0) >>> 8) | 0, i[g + 1 >> 0] = T >>> 0 < 16384 ? T >>> 6 : 255 + (T >>> 31) | 0, T = C + -17685 + ((33050 * P | 0) >>> 8) | 0, i[g + 2 >> 0] = T >>> 0 < 16384 ? T >>> 6 : 255 + (T >>> 31) | 0, b && (T = M + _ | 0, M = E + m | 0, E = T >>> 1 & 255, g = T >>> 17, T = f + v | 0, L = ((26149 * g | 0) >>> 8) - 14234 + (v = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, i[T >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = 8708 - ((13320 * g | 0) >>> 8) - ((6419 * E | 0) >>> 8) + v | 0, i[T + 1 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = ((33050 * E | 0) >>> 8) - 17685 + v | 0, i[T + 2 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = M >>> 1 & 255, T = M >>> 17, M = f + A | 0, y = ((26149 * T | 0) >>> 8) - 14234 + (A = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, i[M >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = 8708 - ((13320 * T | 0) >>> 8) - ((6419 * L | 0) >>> 8) + A | 0, i[M + 1 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = ((33050 * L | 0) >>> 8) - 17685 + A | 0, i[M + 2 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0), (0 | h) == (0 | c)) {
                                        S = p, k = m;
                                        break
                                    }
                                    w = p, _ = m, h = h + 1 | 0
                                }
                            1 & l | 0 || (h = (l = k + 131074 + (3 * S | 0) | 0) >>> 2 & 255, _ = l >>> 18, w = u + (l = 3 * d | 0) | 0, e = ((26149 * _ | 0) >>> 8) - 14234 + (u = (19077 * (0 | s[e + d >> 0]) | 0) >>> 8) | 0, i[w >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = 8708 - ((13320 * _ | 0) >>> 8) - ((6419 * h | 0) >>> 8) + u | 0, i[w + 1 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = ((33050 * h | 0) >>> 8) - 17685 + u | 0, i[w + 2 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, b && (k = (b = S + 131074 + (3 * k | 0) | 0) >>> 2 & 255, S = b >>> 18, b = f + l | 0, d = ((26149 * S | 0) >>> 8) - 14234 + (l = (19077 * (0 | s[r + d >> 0]) | 0) >>> 8) | 0, i[b >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = 8708 - ((13320 * S | 0) >>> 8) - ((6419 * k | 0) >>> 8) + l | 0, i[b + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = ((33050 * k | 0) >>> 8) - 17685 + l | 0, i[b + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0;
                            if (c = (d = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 17685 + ((33050 * p | 0) >>> 8) | 0, i[u >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + 8708 - ((13320 * v | 0) >>> 8) - ((6419 * p | 0) >>> 8) | 0, i[u + 1 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, b = m + -14234 + ((26149 * v | 0) >>> 8) | 0, i[u + 2 >> 0] = b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0, (b = 0 != (0 | r)) && (p = (v = _ + 131074 + (3 * h | 0) | 0) >>> 18, w = ((33050 * (m = v >>> 2 & 255) | 0) >>> 8) - 17685 + (v = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, i[f >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = 8708 - ((13320 * p | 0) >>> 8) - ((6419 * m | 0) >>> 8) + v | 0, i[f + 1 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0, w = ((26149 * p | 0) >>> 8) - 14234 + v | 0, i[f + 2 >> 0] = w >>> 0 < 16384 ? w >>> 6 : 255 + (w >>> 31) | 0), (0 | c) < 1) S = _, k = h;
                            else
                                for (w = _, _ = h, h = 1;;) {
                                    if (A = (M = ((m = _ + 524296 + w + (v = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (p = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (p + w << 1) | 0) >>> 3) + v | 0, g = (m = (E = (m + (v + _ << 1) | 0) >>> 3) + w | 0) >>> 1 & 255, T = m >>> 17, D = u + (m = 3 * (L = (y = h << 1) - 1 | 0) | 0) | 0, P = (C = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 17685 + ((33050 * g | 0) >>> 8) | 0, i[D >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = C + 8708 - ((13320 * T | 0) >>> 8) - ((6419 * g | 0) >>> 8) | 0, i[D + 1 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = C + -14234 + ((26149 * T | 0) >>> 8) | 0, i[D + 2 >> 0] = P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0, P = A >>> 1 & 255, D = A >>> 17, T = u + (A = 6 * h | 0) | 0, g = (C = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 17685 + ((33050 * P | 0) >>> 8) | 0, i[T >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = C + 8708 - ((13320 * D | 0) >>> 8) - ((6419 * P | 0) >>> 8) | 0, i[T + 1 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = C + -14234 + ((26149 * D | 0) >>> 8) | 0, i[T + 2 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, b && (g = M + _ | 0, M = E + p | 0, E = g >>> 1 & 255, T = g >>> 17, g = f + m | 0, L = ((33050 * E | 0) >>> 8) - 17685 + (m = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, i[g >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = 8708 - ((13320 * T | 0) >>> 8) - ((6419 * E | 0) >>> 8) + m | 0, i[g + 1 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = ((26149 * T | 0) >>> 8) - 14234 + m | 0, i[g + 2 >> 0] = L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0, L = M >>> 1 & 255, g = M >>> 17, M = f + A | 0, y = ((33050 * L | 0) >>> 8) - 17685 + (A = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, i[M >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = 8708 - ((13320 * g | 0) >>> 8) - ((6419 * L | 0) >>> 8) + A | 0, i[M + 1 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0, y = ((26149 * g | 0) >>> 8) - 14234 + A | 0, i[M + 2 >> 0] = y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0), (0 | h) == (0 | c)) {
                                        S = v, k = p;
                                        break
                                    }
                                    w = v, _ = p, h = h + 1 | 0
                                }
                            1 & l | 0 || (h = (l = k + 131074 + (3 * S | 0) | 0) >>> 2 & 255, _ = l >>> 18, w = u + (l = 3 * d | 0) | 0, e = ((33050 * h | 0) >>> 8) - 17685 + (u = (19077 * (0 | s[e + d >> 0]) | 0) >>> 8) | 0, i[w >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = 8708 - ((13320 * _ | 0) >>> 8) - ((6419 * h | 0) >>> 8) + u | 0, i[w + 1 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, e = ((26149 * _ | 0) >>> 8) - 14234 + u | 0, i[w + 2 >> 0] = e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0, b && (k = (b = S + 131074 + (3 * k | 0) | 0) >>> 2 & 255, S = b >>> 18, b = f + l | 0, d = ((33050 * k | 0) >>> 8) - 17685 + (l = (19077 * (0 | s[r + d >> 0]) | 0) >>> 8) | 0, i[b >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = 8708 - ((13320 * S | 0) >>> 8) - ((6419 * k | 0) >>> 8) + l | 0, i[b + 1 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0, d = ((26149 * S | 0) >>> 8) - 14234 + l | 0, i[b + 2 >> 0] = d >>> 0 < 16384 ? d >>> 6 : 255 + (d >>> 31) | 0))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0;
                            if (c = (d = (l |= 0) - 1 | 0) >> 1, m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0, p = 0 | i[e >> 0], i[u >> 0] = -1, v = m >>> 2 & 255, b = m >>> 18 & 255, p = (m = (19077 * (255 & p) | 0) >>> 8) - 14234 + ((26149 * b | 0) >>> 8) | 0, i[u + 1 >> 0] = p >>> 0 < 16384 ? p >>> 6 : 255 + (p >>> 31) | 0, p = m + 8708 - ((6419 * v | 0) >>> 8) - ((13320 * b | 0) >>> 8) | 0, i[u + 2 >> 0] = p >>> 0 < 16384 ? p >>> 6 : 255 + (p >>> 31) | 0, p = m + -17685 + ((33050 * v | 0) >>> 8) | 0, i[u + 3 >> 0] = p >>> 0 < 16384 ? p >>> 6 : 255 + (p >>> 31) | 0, (p = 0 != (0 | r)) && (v = _ + 131074 + (3 * h | 0) | 0, m = 0 | i[r >> 0], i[f >> 0] = -1, b = v >>> 2 & 255, m = ((26149 * (w = v >>> 18 & 255) | 0) >>> 8) - 14234 + (v = (19077 * (255 & m) | 0) >>> 8) | 0, i[f + 1 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = 8708 - ((6419 * b | 0) >>> 8) - ((13320 * w | 0) >>> 8) + v | 0, i[f + 2 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0, m = ((33050 * b | 0) >>> 8) - 17685 + v | 0, i[f + 3 >> 0] = m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0), (0 | c) < 1) S = _, k = h;
                            else
                                for (m = _, _ = h, h = 1;;) {
                                    if (M = ((w = _ + 524296 + m + (v = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (b = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (b + m << 1) | 0) >>> 3, w = (E = (w + (v + _ << 1) | 0) >>> 3) + m | 0, A = M + v | 0, g = 0 | i[e + (L = (y = h << 1) - 1 | 0) >> 0], i[(D = u + (T = L << 2) | 0) >> 0] = -1, C = w >>> 1 & 255, P = w >>> 17 & 255, g = (w = (19077 * (255 & g) | 0) >>> 8) - 14234 + ((26149 * P | 0) >>> 8) | 0, i[D + 1 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = w + 8708 - ((6419 * C | 0) >>> 8) - ((13320 * P | 0) >>> 8) | 0, i[D + 2 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = w + -17685 + ((33050 * C | 0) >>> 8) | 0, i[D + 3 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = 0 | i[e + y >> 0], i[(C = u + (D = h << 3) | 0) >> 0] = -1, w = A >>> 1 & 255, P = A >>> 17 & 255, g = (A = (19077 * (255 & g) | 0) >>> 8) - 14234 + ((26149 * P | 0) >>> 8) | 0, i[C + 1 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = A + 8708 - ((6419 * w | 0) >>> 8) - ((13320 * P | 0) >>> 8) | 0, i[C + 2 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, g = A + -17685 + ((33050 * w | 0) >>> 8) | 0, i[C + 3 >> 0] = g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0, p && (g = M + _ | 0, M = E + b | 0, E = 0 | i[r + L >> 0], i[(L = f + T | 0) >> 0] = -1, T = g >>> 1 & 255, E = ((26149 * (C = g >>> 17 & 255) | 0) >>> 8) - 14234 + (g = (19077 * (255 & E) | 0) >>> 8) | 0, i[L + 1 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = 8708 - ((6419 * T | 0) >>> 8) - ((13320 * C | 0) >>> 8) + g | 0, i[L + 2 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = ((33050 * T | 0) >>> 8) - 17685 + g | 0, i[L + 3 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = 0 | i[r + y >> 0], i[(y = f + D | 0) >> 0] = -1, D = M >>> 1 & 255, E = ((26149 * (L = M >>> 17 & 255) | 0) >>> 8) - 14234 + (M = (19077 * (255 & E) | 0) >>> 8) | 0, i[y + 1 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = 8708 - ((6419 * D | 0) >>> 8) - ((13320 * L | 0) >>> 8) + M | 0, i[y + 2 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0, E = ((33050 * D | 0) >>> 8) - 17685 + M | 0, i[y + 3 >> 0] = E >>> 0 < 16384 ? E >>> 6 : 255 + (E >>> 31) | 0), (0 | h) == (0 | c)) {
                                        S = v, k = b;
                                        break
                                    }
                                    m = v, _ = b, h = h + 1 | 0
                                }
                            1 & l | 0 || (l = k + 131074 + (3 * S | 0) | 0, h = 0 | i[e + d >> 0], i[(_ = u + (e = d << 2) | 0) >> 0] = -1, u = l >>> 2 & 255, h = ((26149 * (m = l >>> 18 & 255) | 0) >>> 8) - 14234 + (l = (19077 * (255 & h) | 0) >>> 8) | 0, i[_ + 1 >> 0] = h >>> 0 < 16384 ? h >>> 6 : 255 + (h >>> 31) | 0, h = 8708 - ((6419 * u | 0) >>> 8) - ((13320 * m | 0) >>> 8) + l | 0, i[_ + 2 >> 0] = h >>> 0 < 16384 ? h >>> 6 : 255 + (h >>> 31) | 0, h = ((33050 * u | 0) >>> 8) - 17685 + l | 0, i[_ + 3 >> 0] = h >>> 0 < 16384 ? h >>> 6 : 255 + (h >>> 31) | 0, p && (p = S + 131074 + (3 * k | 0) | 0, k = 0 | i[r + d >> 0], i[(d = f + e | 0) >> 0] = -1, e = p >>> 2 & 255, k = ((26149 * (f = p >>> 18 & 255) | 0) >>> 8) - 14234 + (p = (19077 * (255 & k) | 0) >>> 8) | 0, i[d + 1 >> 0] = k >>> 0 < 16384 ? k >>> 6 : 255 + (k >>> 31) | 0, k = 8708 - ((6419 * e | 0) >>> 8) - ((13320 * f | 0) >>> 8) + p | 0, i[d + 2 >> 0] = k >>> 0 < 16384 ? k >>> 6 : 255 + (k >>> 31) | 0, k = ((33050 * e | 0) >>> 8) - 17685 + p | 0, i[d + 3 >> 0] = k >>> 0 < 16384 ? k >>> 6 : 255 + (k >>> 31) | 0))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0;
                            if (d = (c = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 14234 + ((26149 * v | 0) >>> 8) | 0, w = m + 8708 - ((13320 * v | 0) >>> 8) - ((6419 * p | 0) >>> 8) | 0, v = m + -17685 + ((33050 * p | 0) >>> 8) | 0, i[u >> 0] = (w >>> 0 < 16384 ? w >>> 6 : 255 + (w >> 31 & 3841) | 0) >>> 4 | 240 & (b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0), i[u + 1 >> 0] = 15 | (v >>> 0 < 16384 ? v >>> 6 : 255 + (v >>> 31) | 0), (v = 0 != (0 | r)) && (w = (b = _ + 131074 + (3 * h | 0) | 0) >>> 2 & 255, m = ((26149 * (p = b >>> 18) | 0) >>> 8) - 14234 + (b = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, S = 8708 - ((13320 * p | 0) >>> 8) - ((6419 * w | 0) >>> 8) + b | 0, p = ((33050 * w | 0) >>> 8) - 17685 + b | 0, i[f >> 0] = (S >>> 0 < 16384 ? S >>> 6 : 255 + (S >> 31 & 3841) | 0) >>> 4 | 240 & (m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0), i[f + 1 >> 0] = 15 | (p >>> 0 < 16384 ? p >>> 6 : 255 + (p >>> 31) | 0)), (0 | d) < 1) k = _, E = h;
                            else
                                for (p = _, _ = h, h = 1;;) {
                                    if (A = (M = ((b = _ + 524296 + p + (m = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (S = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (S + p << 1) | 0) >>> 3) + m | 0, g = (b = (w = (b + (m + _ << 1) | 0) >>> 3) + p | 0) >>> 1 & 255, T = b >>> 17, D = u + (b = (L = (y = h << 1) - 1 | 0) << 1) | 0, P = (C = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 14234 + ((26149 * T | 0) >>> 8) | 0, R = C + 8708 - ((13320 * T | 0) >>> 8) - ((6419 * g | 0) >>> 8) | 0, T = C + -17685 + ((33050 * g | 0) >>> 8) | 0, i[D >> 0] = (R >>> 0 < 16384 ? R >>> 6 : 255 + (R >> 31 & 3841) | 0) >>> 4 | 240 & (P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0), i[D + 1 >> 0] = 15 | (T >>> 0 < 16384 ? T >>> 6 : 255 + (T >>> 31) | 0), T = A >>> 1 & 255, D = A >>> 17, P = u + (A = h << 2) | 0, g = (R = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 14234 + ((26149 * D | 0) >>> 8) | 0, C = R + 8708 - ((13320 * D | 0) >>> 8) - ((6419 * T | 0) >>> 8) | 0, D = R + -17685 + ((33050 * T | 0) >>> 8) | 0, i[P >> 0] = (C >>> 0 < 16384 ? C >>> 6 : 255 + (C >> 31 & 3841) | 0) >>> 4 | 240 & (g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0), i[P + 1 >> 0] = 15 | (D >>> 0 < 16384 ? D >>> 6 : 255 + (D >>> 31) | 0), v && (D = M + _ | 0, M = w + S | 0, w = D >>> 1 & 255, P = D >>> 17, D = f + b | 0, L = ((26149 * P | 0) >>> 8) - 14234 + (b = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, g = 8708 - ((13320 * P | 0) >>> 8) - ((6419 * w | 0) >>> 8) + b | 0, P = ((33050 * w | 0) >>> 8) - 17685 + b | 0, i[D >> 0] = (g >>> 0 < 16384 ? g >>> 6 : 255 + (g >> 31 & 3841) | 0) >>> 4 | 240 & (L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0), i[D + 1 >> 0] = 15 | (P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0), P = M >>> 1 & 255, D = M >>> 17, M = f + A | 0, y = ((26149 * D | 0) >>> 8) - 14234 + (A = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, L = 8708 - ((13320 * D | 0) >>> 8) - ((6419 * P | 0) >>> 8) + A | 0, D = ((33050 * P | 0) >>> 8) - 17685 + A | 0, i[M >> 0] = (L >>> 0 < 16384 ? L >>> 6 : 255 + (L >> 31 & 3841) | 0) >>> 4 | 240 & (y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0), i[M + 1 >> 0] = 15 | (D >>> 0 < 16384 ? D >>> 6 : 255 + (D >>> 31) | 0)), (0 | h) == (0 | d)) {
                                        k = m, E = S;
                                        break
                                    }
                                    p = m, _ = S, h = h + 1 | 0
                                }
                            1 & l | 0 || (h = (l = E + 131074 + (3 * k | 0) | 0) >>> 2 & 255, _ = l >>> 18, p = u + (l = c << 1) | 0, e = ((26149 * _ | 0) >>> 8) - 14234 + (u = (19077 * (0 | s[e + c >> 0]) | 0) >>> 8) | 0, d = 8708 - ((13320 * _ | 0) >>> 8) - ((6419 * h | 0) >>> 8) + u | 0, _ = ((33050 * h | 0) >>> 8) - 17685 + u | 0, i[p >> 0] = (d >>> 0 < 16384 ? d >>> 6 : 255 + (d >> 31 & 3841) | 0) >>> 4 | 240 & (e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0), i[p + 1 >> 0] = 15 | (_ >>> 0 < 16384 ? _ >>> 6 : 255 + (_ >>> 31) | 0), v && (E = (v = k + 131074 + (3 * E | 0) | 0) >>> 2 & 255, k = v >>> 18, v = f + l | 0, c = ((26149 * k | 0) >>> 8) - 14234 + (l = (19077 * (0 | s[r + c >> 0]) | 0) >>> 8) | 0, r = 8708 - ((13320 * k | 0) >>> 8) - ((6419 * E | 0) >>> 8) + l | 0, k = ((33050 * E | 0) >>> 8) - 17685 + l | 0, i[v >> 0] = (r >>> 0 < 16384 ? r >>> 6 : 255 + (r >> 31 & 3841) | 0) >>> 4 | 240 & (c >>> 0 < 16384 ? c >>> 6 : 255 + (c >>> 31) | 0), i[v + 1 >> 0] = 15 | (k >>> 0 < 16384 ? k >>> 6 : 255 + (k >>> 31) | 0)))
                        }, function(e, r, t, n, o, a, u, f, l) {
                            e |= 0, r |= 0, t |= 0, o |= 0, a |= 0, u |= 0, f |= 0;
                            var c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                A = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0;
                            if (d = (c = (l |= 0) - 1 | 0) >> 1, p = (m = 131074 + (3 * (_ = (0 | s[(n |= 0) >> 0]) << 16 | 0 | s[t >> 0]) | 0) + (h = (0 | s[a >> 0]) << 16 | 0 | s[o >> 0]) | 0) >>> 2 & 255, v = m >>> 18, b = (m = (19077 * (0 | s[e >> 0]) | 0) >>> 8) - 14234 + ((26149 * v | 0) >>> 8) | 0, v = (w = m + 8708 - ((13320 * v | 0) >>> 8) - ((6419 * p | 0) >>> 8) | 0) >>> 0 < 16384 ? w >> 6 : 255 + (w >> 31 & -255) | 0, w = m + -17685 + ((33050 * p | 0) >>> 8) | 0, i[u >> 0] = v >>> 5 | 248 & (b >>> 0 < 16384 ? b >>> 6 : 255 + (b >>> 31) | 0), i[u + 1 >> 0] = v << 3 & 224 | (w >>> 0 < 16384 ? w >>> 6 : 255 + (w >> 31 & 1793) | 0) >>> 3, (w = 0 != (0 | r)) && (b = (v = _ + 131074 + (3 * h | 0) | 0) >>> 2 & 255, m = ((26149 * (p = v >>> 18) | 0) >>> 8) - 14234 + (v = (19077 * (0 | s[r >> 0]) | 0) >>> 8) | 0, p = (S = 8708 - ((13320 * p | 0) >>> 8) - ((6419 * b | 0) >>> 8) + v | 0) >>> 0 < 16384 ? S >> 6 : 255 + (S >> 31 & -255) | 0, S = ((33050 * b | 0) >>> 8) - 17685 + v | 0, i[f >> 0] = p >>> 5 | 248 & (m >>> 0 < 16384 ? m >>> 6 : 255 + (m >>> 31) | 0), i[f + 1 >> 0] = p << 3 & 224 | (S >>> 0 < 16384 ? S >>> 6 : 255 + (S >> 31 & 1793) | 0) >>> 3), (0 | d) < 1) k = _, E = h;
                            else
                                for (S = _, _ = h, h = 1;;) {
                                    if (A = (M = ((v = _ + 524296 + S + (p = (0 | s[n + h >> 0]) << 16 | 0 | s[t + h >> 0]) + (m = (0 | s[a + h >> 0]) << 16 | 0 | s[o + h >> 0]) | 0) + (m + S << 1) | 0) >>> 3) + p | 0, g = (v = (b = (v + (p + _ << 1) | 0) >>> 3) + S | 0) >>> 1 & 255, T = v >>> 17, D = u + (v = (L = (y = h << 1) - 1 | 0) << 1) | 0, P = (C = (19077 * (0 | s[e + L >> 0]) | 0) >>> 8) - 14234 + ((26149 * T | 0) >>> 8) | 0, T = (R = C + 8708 - ((13320 * T | 0) >>> 8) - ((6419 * g | 0) >>> 8) | 0) >>> 0 < 16384 ? R >> 6 : 255 + (R >> 31 & -255) | 0, R = C + -17685 + ((33050 * g | 0) >>> 8) | 0, i[D >> 0] = T >>> 5 | 248 & (P >>> 0 < 16384 ? P >>> 6 : 255 + (P >>> 31) | 0), i[D + 1 >> 0] = T << 3 & 224 | (R >>> 0 < 16384 ? R >>> 6 : 255 + (R >> 31 & 1793) | 0) >>> 3, R = A >>> 1 & 255, T = A >>> 17, D = u + (A = h << 2) | 0, g = (P = (19077 * (0 | s[e + y >> 0]) | 0) >>> 8) - 14234 + ((26149 * T | 0) >>> 8) | 0, T = (C = P + 8708 - ((13320 * T | 0) >>> 8) - ((6419 * R | 0) >>> 8) | 0) >>> 0 < 16384 ? C >> 6 : 255 + (C >> 31 & -255) | 0, C = P + -17685 + ((33050 * R | 0) >>> 8) | 0, i[D >> 0] = T >>> 5 | 248 & (g >>> 0 < 16384 ? g >>> 6 : 255 + (g >>> 31) | 0), i[D + 1 >> 0] = T << 3 & 224 | (C >>> 0 < 16384 ? C >>> 6 : 255 + (C >> 31 & 1793) | 0) >>> 3, w && (C = M + _ | 0, M = b + m | 0, b = C >>> 1 & 255, T = C >>> 17, C = f + v | 0, L = ((26149 * T | 0) >>> 8) - 14234 + (v = (19077 * (0 | s[r + L >> 0]) | 0) >>> 8) | 0, T = (D = 8708 - ((13320 * T | 0) >>> 8) - ((6419 * b | 0) >>> 8) + v | 0) >>> 0 < 16384 ? D >> 6 : 255 + (D >> 31 & -255) | 0, D = ((33050 * b | 0) >>> 8) - 17685 + v | 0, i[C >> 0] = T >>> 5 | 248 & (L >>> 0 < 16384 ? L >>> 6 : 255 + (L >>> 31) | 0), i[C + 1 >> 0] = T << 3 & 224 | (D >>> 0 < 16384 ? D >>> 6 : 255 + (D >> 31 & 1793) | 0) >>> 3, D = M >>> 1 & 255, T = M >>> 17, M = f + A | 0, y = ((26149 * T | 0) >>> 8) - 14234 + (A = (19077 * (0 | s[r + y >> 0]) | 0) >>> 8) | 0, T = (C = 8708 - ((13320 * T | 0) >>> 8) - ((6419 * D | 0) >>> 8) + A | 0) >>> 0 < 16384 ? C >> 6 : 255 + (C >> 31 & -255) | 0, C = ((33050 * D | 0) >>> 8) - 17685 + A | 0, i[M >> 0] = T >>> 5 | 248 & (y >>> 0 < 16384 ? y >>> 6 : 255 + (y >>> 31) | 0), i[M + 1 >> 0] = T << 3 & 224 | (C >>> 0 < 16384 ? C >>> 6 : 255 + (C >> 31 & 1793) | 0) >>> 3), (0 | h) == (0 | d)) {
                                        k = p, E = m;
                                        break
                                    }
                                    S = p, _ = m, h = h + 1 | 0
                                }
                            1 & l | 0 || (h = (l = E + 131074 + (3 * k | 0) | 0) >>> 2 & 255, _ = l >>> 18, S = u + (l = c << 1) | 0, e = ((26149 * _ | 0) >>> 8) - 14234 + (u = (19077 * (0 | s[e + c >> 0]) | 0) >>> 8) | 0, _ = (d = 8708 - ((13320 * _ | 0) >>> 8) - ((6419 * h | 0) >>> 8) + u | 0) >>> 0 < 16384 ? d >> 6 : 255 + (d >> 31 & -255) | 0, d = ((33050 * h | 0) >>> 8) - 17685 + u | 0, i[S >> 0] = _ >>> 5 | 248 & (e >>> 0 < 16384 ? e >>> 6 : 255 + (e >>> 31) | 0), i[S + 1 >> 0] = _ << 3 & 224 | (d >>> 0 < 16384 ? d >>> 6 : 255 + (d >> 31 & 1793) | 0) >>> 3, w && (E = (w = k + 131074 + (3 * E | 0) | 0) >>> 2 & 255, k = w >>> 18, w = f + l | 0, c = ((26149 * k | 0) >>> 8) - 14234 + (l = (19077 * (0 | s[r + c >> 0]) | 0) >>> 8) | 0, k = (r = 8708 - ((13320 * k | 0) >>> 8) - ((6419 * E | 0) >>> 8) + l | 0) >>> 0 < 16384 ? r >> 6 : 255 + (r >> 31 & -255) | 0, r = ((33050 * E | 0) >>> 8) - 17685 + l | 0, i[w >> 0] = k >>> 5 | 248 & (c >>> 0 < 16384 ? c >>> 6 : 255 + (c >>> 31) | 0), i[w + 1 >> 0] = k << 3 & 224 | (r >>> 0 < 16384 ? r >>> 6 : 255 + (r >> 31 & 1793) | 0) >>> 3))
                        }],
                        It = [function(e, r, t, n) {
                            return L(8), 0
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, t |= 0;
                            var i = 0,
                                o = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                _ = 0;
                            if (!((0 | (n |= 0)) > 0)) return M = f = 0, 0;
                            for (i = 0, o = 0, u = 0;;) {
                                if (s = (0 | d[e + (i << 1) >> 1]) - (0 | d[r + (i << 1) >> 1]) | 0, _ = (0 | d[(c = t + (i << 1) | 0) >> 1]) + s | 0, a[c >> 1] = (0 | _) < 0 ? 0 : 65535 & ((0 | _) < 1023 ? _ : 1023), s = 0 | ot(0 | (_ = (0 | s) > -1 ? s : 0 - s | 0), ((0 | _) < 0) << 31 >> 31 | 0, 0 | o, 0 | u), _ = M, (0 | (i = i + 1 | 0)) == (0 | n)) {
                                    f = _, l = s;
                                    break
                                }
                                o = s, u = _
                            }
                            return M = f, 0 | l
                        }],
                        Ft = [Lt, function(e, r, t, n, i, o) {
                            e |= 0, r |= 0, t |= 0, i |= 0, o |= 0;
                            var a = 0,
                                u = 0;
                            if ((0 | (n |= 0)) > 0)
                                for (a = 0, u = 0; f[o + (a << 2) >> 2] = (0 | s[e + u >> 0]) << 16 | (0 | s[r + u >> 0]) << 8 | 0 | s[t + u >> 0] | -16777216, (0 | (a = a + 1 | 0)) != (0 | n);) u = u + i | 0
                        }, function(e, r, t, n, i, o) {
                            r |= 0, i |= 0, o |= 0;
                            var a = 0,
                                u = 0,
                                l = 0;
                            if ((0 | (n |= 0)) > 0 & (0 | (t |= 0)) > 0)
                                for (a = e |= 0, u = i, l = 0;;) {
                                    i = 0;
                                    do {
                                        f[u + (i << 2) >> 2] = (0 | s[a + i >> 0]) << 8, i = i + 1 | 0
                                    } while ((0 | i) != (0 | t));
                                    if ((0 | (l = l + 1 | 0)) == (0 | n)) break;
                                    a = a + r | 0, u = u + (o << 2) | 0
                                }
                        }, function(e, r, t, n, i, o) {
                            e |= 0, r |= 0, t |= 0;
                            var a = 0,
                                u = 0,
                                l = 0;
                            if ((0 | (n |= 0)) < (0 | (i |= 0)) & (0 | (o |= 0)) > 0)
                                for (a = n, u = e, l = t;;) {
                                    for (t = 0, e = l, n = u, u = u + (o << 2) | 0; f[e >> 2] = f[r + (((0 | f[n >> 2]) >>> 8 & 255) << 2) >> 2], (0 | (t = t + 1 | 0)) != (0 | o);) e = e + 4 | 0, n = n + 4 | 0;
                                    if ((0 | (a = a + 1 | 0)) == (0 | i)) break;
                                    l = l + (o << 2) | 0
                                }
                        }, function(e, r, t, n, o, a) {
                            e |= 0, r |= 0, t |= 0;
                            var u = 0,
                                l = 0,
                                c = 0;
                            if ((0 | (n |= 0)) < (0 | (o |= 0)) & (0 | (a |= 0)) > 0)
                                for (u = n, l = e, c = t;;) {
                                    for (t = 0, e = c, n = l, l = l + a | 0; i[e >> 0] = (0 | f[r + ((0 | s[n >> 0]) << 2) >> 2]) >>> 8, (0 | (t = t + 1 | 0)) != (0 | a);) e = e + 1 | 0, n = n + 1 | 0;
                                    if ((0 | (u = u + 1 | 0)) == (0 | o)) break;
                                    c = c + a | 0
                                }
                        }, function(e, r, t, n, i, o) {
                            r |= 0, ue(e |= 0, t |= 0, 1, 8, n |= 0, i |= 0, o |= 0), ue(r, t, 1, 8, n, i, o)
                        }, function(e, r, t, n, i, o) {
                            var a;
                            r |= 0, oe((e |= 0) + (a = (t |= 0) << 2) | 0, t, 1, 8, n |= 0, i |= 0, o |= 0), oe(r + a | 0, t, 1, 8, n, i, o)
                        }, function(e, r, t, n, i, o) {
                            r |= 0, ue(e |= 0, 1, t |= 0, 8, n |= 0, i |= 0, o |= 0), ue(r, 1, t, 8, n, i, o)
                        }, function(e, r, t, n, i, o) {
                            r |= 0, oe(4 + (e |= 0) | 0, 1, t |= 0, 8, n |= 0, i |= 0, o |= 0), oe(r + 4 | 0, 1, t, 8, n, i, o)
                        }, Lt, Lt, Lt, Lt, Lt, Lt, Lt],
                        Ht = [gt, function(e, r) {
                            var t = 0,
                                n = 0,
                                o = 0;
                            for (t = r |= 0, r = e |= 0;;) {
                                if ((0 | t) <= 0) {
                                    n = 0, o = 4;
                                    break
                                }
                                if (-1 != (0 | i[r >> 0])) {
                                    n = 1, o = 4;
                                    break
                                }
                                t = t + -1 | 0, r = r + 1 | 0
                            }
                            return 4 == (0 | o) ? 0 | n : 0
                        }, function(e, r) {
                            e |= 0, r |= 0;
                            var t = 0,
                                n = 0,
                                o = 0;
                            e: do {
                                if ((0 | r) > 0)
                                    for (t = 0, n = r;;) {
                                        if (-1 != (0 | i[e + t >> 0])) {
                                            o = 1;
                                            break e
                                        }
                                        if (!((0 | n) > 1)) {
                                            o = 0;
                                            break
                                        }
                                        t = t + 4 | 0, n = n + -1 | 0
                                    } else o = 0
                            } while (0);
                            return 0 | o
                        }, function(e, r) {
                            return -16777216
                        }, function(e, r) {
                            return 0 | e
                        }, function(e, r) {
                            return 0 | f[(r |= 0) >> 2]
                        }, function(e, r) {
                            return 0 | f[4 + (r |= 0) >> 2]
                        }, function(e, r) {
                            return 0 | f[(r |= 0) - 4 >> 2]
                        }, function(e, r) {
                            var t, n;
                            return e |= 0, t = 0 | f[(r |= 0) >> 2], (((r = (((n = 0 | f[r + 4 >> 2]) ^ e) >>> 1 & 2139062143) + (n & e) | 0) ^ t) >>> 1 & 2139062143) + (r & t) | 0
                        }, function(e, r) {
                            var t;
                            return e |= 0, (((t = 0 | f[(r |= 0) - 4 >> 2]) ^ e) >>> 1 & 2139062143) + (t & e) | 0
                        }, function(e, r) {
                            var t;
                            return e |= 0, (((t = 0 | f[(r |= 0) >> 2]) ^ e) >>> 1 & 2139062143) + (t & e) | 0
                        }, function(e, r) {
                            var t;
                            return e |= 0, e = 0 | f[(r |= 0) - 4 >> 2], (((t = 0 | f[r >> 2]) ^ e) >>> 1 & 2139062143) + (t & e) | 0
                        }, function(e, r) {
                            var t;
                            return e |= 0, e = 0 | f[(r |= 0) >> 2], (((t = 0 | f[r + 4 >> 2]) ^ e) >>> 1 & 2139062143) + (t & e) | 0
                        }, function(e, r) {
                            var t, n, i;
                            return e |= 0, t = 0 | f[(r |= 0) - 4 >> 2], n = 0 | f[r >> 2], i = 0 | f[r + 4 >> 2], r = ((t ^ e) >>> 1 & 2139062143) + (t & e) | 0, (((e = ((i ^ n) >>> 1 & 2139062143) + (i & n) | 0) ^ r) >>> 1 & 2139062143) + (e & r) | 0
                        }, function(e, r) {
                            e |= 0;
                            var t, n, i, o, a, u, l, s, c = 0;
                            return t = 0 | f[(r |= 0) >> 2], n = (e >>> 24) - (r = (c = 0 | f[r + -4 >> 2]) >>> 24) | 0, i = (t >>> 24) - r | 0, o = (e >>> 16 & 255) - (r = c >>> 16 & 255) | 0, a = (t >>> 16 & 255) - r | 0, u = (e >>> 8 & 255) - (r = c >>> 8 & 255) | 0, l = (t >>> 8 & 255) - r | 0, 0 | ((((0 | (c = (255 & e) - (r = 255 & c) | 0)) > -1 ? c : 0 - c | 0) - ((0 | (s = (255 & t) - r | 0)) > -1 ? s : 0 - s | 0) - ((0 | i) > -1 ? i : 0 - i | 0) + ((0 | n) > -1 ? n : 0 - n | 0) - ((0 | l) > -1 ? l : 0 - l | 0) + ((0 | u) > -1 ? u : 0 - u | 0) - ((0 | a) > -1 ? a : 0 - a | 0) + ((0 | o) > -1 ? o : 0 - o | 0) | 0) < 1 ? t : e)
                        }, function(e, r) {
                            var t, n, i, o, a;
                            return e |= 0, ((r = ((t = 0 | f[(r |= 0) >> 2]) >>> 24) + (e >>> 24) - ((n = 0 | f[r + -4 >> 2]) >>> 24) | 0) >>> 0 < 256 ? r : r >>> 24 ^ 255) << 24 | ((a = (255 & t) + (255 & e) - (255 & n) | 0) >>> 0 < 256 ? a : a >>> 24 ^ 255) | ((i = (t >>> 16 & 255) + (e >>> 16 & 255) - (n >>> 16 & 255) | 0) >>> 0 < 256 ? i : i >>> 24 ^ 255) << 16 | ((o = (t >>> 8 & 255) + (e >>> 8 & 255) - (n >>> 8 & 255) | 0) >>> 0 < 256 ? o : o >>> 24 ^ 255) << 8 | 0
                        }, function(e, r) {
                            e |= 0;
                            var t, n, i, o = 0;
                            return o = 0 | f[(r |= 0) >> 2], t = 0 | f[r + -4 >> 2], o = (((e = (r = ((o ^ e) >>> 1 & 2139062143) + (o & e) | 0) >>> 24) - (t >>> 24) | 0) / 2 | 0) + e | 0, n = (((e = r >>> 16 & 255) - (t >>> 16 & 255) | 0) / 2 | 0) + e | 0, i = (((e = r >>> 8 & 255) - (t >>> 8 & 255) | 0) / 2 | 0) + e | 0, (o >>> 0 < 256 ? o : o >>> 24 ^ 255) << 24 | ((r = (((e = 255 & r) - (255 & t) | 0) / 2 | 0) + e | 0) >>> 0 < 256 ? r : r >>> 24 ^ 255) | (n >>> 0 < 256 ? n : n >>> 24 ^ 255) << 16 | (i >>> 0 < 256 ? i : i >>> 24 ^ 255) << 8 | 0
                        }, function(e, r) {
                            r |= 0;
                            var t, n, i, o, a, u, l, s, c, d, _, h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0;
                            if (n = 1 + (t = 0 | f[16 + (e |= 0) >> 2]) >> 1, (0 | t) <= 0) return 0;
                            for (o = e + 20 | 0, a = e + 32 | 0, u = r + 28 | 0, l = r + 16 | 0, s = r + 32 | 0, c = e + 24 | 0, d = e + 36 | 0, _ = e + 28 | 0, e = 0, m = 0, p = 0, v = 0 | f[(i = r + 24 | 0) >> 2];;) {
                                b = 0 | f[a >> 2], p = (0 | Mr(v, t - p | 0, (0 | f[o >> 2]) + (0 | A(b, p)) | 0, b)) + p | 0, b = n - e | 0, 0 | Tr(0 | f[u >> 2], b) ? (S = 0 | f[d >> 2], k = 0 | Mr(0 | f[u >> 2], b, (0 | f[c >> 2]) + (0 | A(S, e)) | 0, S), S = 0 | f[d >> 2], Mr(0 | f[s >> 2], b, (0 | f[_ >> 2]) + (0 | A(S, e)) | 0, S), w = k + e | 0) : w = e, k = 0 | f[r >> 2], S = 0 | f[12140 + (f[k >> 2] << 2) >> 2], b = k + 20 | 0, E = 0 | f[i >> 2];
                                e: do {
                                    if ((0 | f[E + 64 >> 2]) < (0 | f[E + 56 >> 2]))
                                        for (M = (0 | f[k + 16 >> 2]) + (0 | A(0 | f[b >> 2], (0 | f[l >> 2]) + m | 0)) | 0, y = 0, L = E;;) {
                                            if ((0 | f[L + 24 >> 2]) >= 1) {
                                                g = y, T = L;
                                                break e
                                            }
                                            if (D = 0 | f[u >> 2], (0 | f[D + 64 >> 2]) >= (0 | f[D + 56 >> 2])) {
                                                g = y, T = L;
                                                break e
                                            }
                                            if ((0 | f[D + 24 >> 2]) >= 1) {
                                                g = y, T = L;
                                                break e
                                            }
                                            if (Lr(L), Lr(0 | f[u >> 2]), Lr(0 | f[s >> 2]), D = 0 | f[i >> 2], Ct[31 & S](0 | f[D + 68 >> 2], 0 | f[68 + (0 | f[u >> 2]) >> 2], 0 | f[68 + (0 | f[s >> 2]) >> 2], M, 0 | f[D + 52 >> 2]), D = y + 1 | 0, C = 0 | f[i >> 2], !((0 | f[C + 64 >> 2]) < (0 | f[C + 56 >> 2]))) {
                                                g = D, T = C;
                                                break
                                            }
                                            M = M + (0 | f[b >> 2]) | 0, y = D, L = C
                                        } else g = 0, T = E
                                } while (0);
                                if (E = g + m | 0, (0 | t) <= (0 | p)) {
                                    h = E;
                                    break
                                }
                                e = w, m = E, v = T
                            }
                            return 0 | h
                        }, function(e, r) {
                            r |= 0;
                            var t = 0,
                                n = 0,
                                i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0;
                            if (n = 1 + (t = 0 | f[16 + (e |= 0) >> 2]) >> 1, i = 0 | f[r + 24 >> 2], (a = (o = 0 | f[f[r >> 2] >> 2]) - 1 | 0) >>> 0 < 12 ? (o + -7 | 0) >>> 0 < 4 | 0 != (2077 >>> (65535 & a) & 1) && (u = 4) : (o + -7 | 0) >>> 0 < 4 && (u = 4), 4 == (0 | u) && 0 | (u = 0 | f[e + 104 >> 2]) && yr(0 | f[e + 20 >> 2], 0 | f[e + 32 >> 2], u, 0 | f[e >> 2], 0 | f[e + 12 >> 2], t, 0), u = 0 | f[e + 32 >> 2], (0 | t) > 0)
                                for (o = 0, a = t, t = 0 | f[e + 20 >> 2];;) {
                                    if (l = 0 | Mr(i, a, t, u), t = t + (0 | A(l, u)) | 0, a = a - l | 0, l = (0 | Ar(i)) + o | 0, (0 | a) <= 0) {
                                        s = l;
                                        break
                                    }
                                    o = l
                                } else s = 0;
                            if (a = 0 | f[(o = e + 36 | 0) >> 2], i = 0 | f[r + 28 >> 2], (0 | n) <= 0) return 0 | s;
                            u = n, t = 0 | f[e + 24 >> 2];
                            do {
                                l = 0 | Mr(i, u, t, a), t = t + (0 | A(l, a)) | 0, u = u - l | 0, Ar(i)
                            } while ((0 | u) > 0);
                            u = 0 | f[o >> 2], o = 0 | f[r + 32 >> 2], r = n, n = 0 | f[e + 28 >> 2];
                            do {
                                e = 0 | Mr(o, r, n, u), n = n + (0 | A(e, u)) | 0, r = r - e | 0, Ar(o)
                            } while ((0 | r) > 0);
                            return 0 | s
                        }, function(e, r) {
                            var t, n, i;
                            return e |= 0, t = 0 | f[(r |= 0) >> 2], r = 0 | f[t + 20 >> 2], n = (0 | f[t + 16 >> 2]) + (0 | A(r, 0 | f[e + 8 >> 2])) | 0, i = e + 16 | 0,
                                function(e, r, t, n, i, o, a, u, f, l) {
                                    r |= 0, i |= 0, o |= 0, a |= 0, u |= 0, l |= 0;
                                    var s = 0,
                                        c = 0,
                                        d = 0,
                                        _ = 0,
                                        h = 0;
                                    if ((0 | (f |= 0)) > 0)
                                        for (s = e |= 0, c = t |= 0, d = n |= 0, _ = o, h = 0; Ct[31 & l](s, c, d, _, u), o = 0 == (1 & h | 0), (0 | (h = h + 1 | 0)) != (0 | f);) s = s + r | 0, c = o ? c : c + i | 0, d = o ? d : d + i | 0, _ = _ + a | 0
                                }(0 | f[e + 20 >> 2], 0 | f[e + 32 >> 2], 0 | f[e + 24 >> 2], 0 | f[e + 28 >> 2], 0 | f[e + 36 >> 2], n, r, 0 | f[e + 12 >> 2], 0 | f[i >> 2], 0 | f[12192 + (f[t >> 2] << 2) >> 2]), 0 | f[i >> 2]
                        }, function(e, r) {
                            r |= 0;
                            var t, n, i, o, a, u, l, s, c, d, _ = 0,
                                h = 0,
                                m = 0,
                                p = 0,
                                v = 0,
                                b = 0,
                                w = 0,
                                S = 0,
                                k = 0,
                                E = 0,
                                M = 0,
                                y = 0,
                                L = 0,
                                g = 0,
                                T = 0,
                                D = 0,
                                C = 0,
                                P = 0,
                                R = 0,
                                O = 0,
                                B = 0;
                            if (_ = 0 | f[16 + (e |= 0) >> 2], h = 0 | f[r >> 2], m = 0 | f[e + 8 >> 2], p = 0 | f[(t = h + 20 | 0) >> 2], n = (0 | f[h + 16 >> 2]) + (0 | A(p, m)) | 0, i = 0 | f[12088 + (f[h >> 2] << 2) >> 2], h = 0 | f[e + 20 >> 2], o = 0 | f[e + 24 >> 2], a = 0 | f[e + 28 >> 2], u = r + 8 | 0, l = r + 12 | 0, s = m + _ | 0, d = (1 + (c = 0 | f[e + 12 >> 2]) | 0) / 2 | 0, m ? (Nt[7 & i](0 | f[r + 4 >> 2], h, 0 | f[u >> 2], 0 | f[l >> 2], o, a, n + (0 - p) | 0, n, c), v = _ + 1 | 0, b = m + 2 | 0) : (Nt[7 & i](h, 0, o, a, o, a, n, 0, c), v = _, b = 2), _ = 0 | f[(m = e + 32 | 0) >> 2], (0 | b) < (0 | s))
                                for (p = e + 36 | 0, w = n, S = h, k = a, E = o, M = _, y = b;;) {
                                    if (L = E + (b = 0 | f[p >> 2]) | 0, g = k + b | 0, T = w + ((b = 0 | f[t >> 2]) << 1) | 0, D = S + (M << 1) | 0, Nt[7 & i](D + (0 - M) | 0, D, E, k, L, g, T + (0 - b) | 0, T, c), y = y + 2 | 0, b = 0 | f[m >> 2], (0 | y) >= (0 | s)) {
                                        C = T, P = D, R = g, O = L, B = b;
                                        break
                                    }
                                    w = T, S = D, k = g, E = L, M = b
                                } else C = n, P = h, R = a, O = o, B = _;
                            return _ = P + B | 0, ((0 | f[e + 84 >> 2]) + s | 0) < (0 | f[e + 88 >> 2]) ? (lt(0 | f[r + 4 >> 2], 0 | _, 0 | c), lt(0 | f[u >> 2], 0 | O, 0 | d), lt(0 | f[l >> 2], 0 | R, 0 | d), 0 | v + -1) : 1 & s | 0 ? 0 | v : (Nt[7 & i](_, 0, O, R, O, R, C + (0 | f[t >> 2]) | 0, 0, c), 0 | v)
                        }, function(e, r) {
                            e |= 0;
                            var t, n, i, o, a, u, l, s, c, d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0;
                            if (d = 0 | f[(r |= 0) >> 2], r = 0 | f[e + 8 >> 2], t = d + 32 | 0, n = (0 | f[d + 16 >> 2]) + (0 | A(0 | f[t >> 2], r)) | 0, _ = r >> 1, r = d + 36 | 0, i = (0 | f[d + 20 >> 2]) + (0 | A(0 | f[r >> 2], _)) | 0, o = d + 40 | 0, a = (0 | f[d + 24 >> 2]) + (0 | A(0 | f[o >> 2], _)) | 0, u = (1 + (_ = 0 | f[e + 12 >> 2]) | 0) / 2 | 0, l = (1 + (h = 0 | f[(d = e + 16 | 0) >> 2]) | 0) / 2 | 0, !(m = (0 | h) > 0)) return 0 | f[d >> 2];
                            if (c = e + 32 | 0, lt(0 | n, 0 | f[(s = e + 20 | 0) >> 2], 0 | _), 1 != (0 | h)) {
                                p = 1;
                                do {
                                    lt(0 | n + (0 | A(0 | f[t >> 2], p)), (0 | f[s >> 2]) + (0 | A(0 | f[c >> 2], p)) | 0, 0 | _), p = p + 1 | 0
                                } while ((0 | p) != (0 | h))
                            }
                            if (!m) return 0 | f[d >> 2];
                            m = e + 24 | 0, h = e + 36 | 0, p = e + 28 | 0, e = 0;
                            do {
                                lt(0 | (_ = i + (0 | A(0 | f[r >> 2], e)) | 0), (0 | f[m >> 2]) + (0 | A(0 | f[h >> 2], e)) | 0, 0 | u), lt(0 | (_ = a + (0 | A(0 | f[o >> 2], e)) | 0), (0 | f[p >> 2]) + (0 | A(0 | f[h >> 2], e)) | 0, 0 | u), e = e + 1 | 0
                            } while ((0 | e) < (0 | l));
                            return 0 | f[d >> 2]
                        }, re, gt, gt, gt, gt, gt, gt, gt, gt, gt],
                        Ut = [Tt, function(e, r, t, n) {
                            r |= 0, t |= 0, n |= 0;
                            var o, a = 0,
                                u = 0;
                            if (o = (e |= 0) ? 0 | i[e >> 0] : 0, (0 | n) > 0) {
                                a = o, u = 0;
                                do {
                                    a = (0 | s[r + u >> 0]) + (255 & a) & 255, i[t + u >> 0] = a, u = u + 1 | 0
                                } while ((0 | u) != (0 | n))
                            }
                        }, function(e, r, t, n) {
                            r |= 0, t |= 0;
                            var o, a = 0,
                                u = 0,
                                f = 0;
                            if (o = (0 | (n |= 0)) > 0, e |= 0) {
                                if (!o) return;
                                f = 0;
                                do {
                                    i[t + f >> 0] = (0 | s[r + f >> 0]) + (0 | s[e + f >> 0]), f = f + 1 | 0
                                } while ((0 | f) != (0 | n))
                            } else if (o) {
                                a = 0, u = 0;
                                do {
                                    a = (0 | s[r + u >> 0]) + (255 & a) | 0, i[t + u >> 0] = a, u = u + 1 | 0
                                } while ((0 | u) != (0 | n))
                            }
                        }, function(e, r, t, n) {
                            r |= 0, t |= 0, n |= 0;
                            var o = 0,
                                a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0;
                            if (e |= 0) {
                                if (a = 0 | i[e >> 0], (0 | n) > 0)
                                    for (u = a, f = a, l = 0, c = a; o = ((a = (255 & u) - (255 & f) + (255 & c) | 0) >>> 0 < 256 ? a : 255 + (a >>> 31) | 0) + (0 | s[r + l >> 0]) & 255, i[t + l >> 0] = o, (0 | (a = l + 1 | 0)) != (0 | n);) d = c, u = o, l = a, c = 0 | i[e + a >> 0], f = d
                            } else {
                                if (!((0 | n) > 0)) return;
                                o = 0, a = 0;
                                do {
                                    o = (0 | s[r + a >> 0]) + (255 & o) | 0, i[t + a >> 0] = o, a = a + 1 | 0
                                } while ((0 | a) != (0 | n))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var o = 0,
                                a = 0,
                                u = 0;
                            if (!((0 | (t |= 0)) <= 0))
                                if (n) {
                                    n = 0;
                                    do {
                                        switch ((u = 0 | i[r + n >> 0]) << 24 >> 24) {
                                            case -1:
                                                break;
                                            case 0:
                                                i[e + n >> 0] = 0;
                                                break;
                                            default:
                                                o = (8388608 + (0 | A(0 | s[(a = e + n | 0) >> 0], 4278190080 / ((255 & u) >>> 0) | 0)) | 0) >>> 24 & 255, i[a >> 0] = o
                                        }
                                        n = n + 1 | 0
                                    } while ((0 | n) != (0 | t))
                                } else {
                                    n = 0;
                                    do {
                                        switch ((o = 0 | i[r + n >> 0]) << 24 >> 24) {
                                            case -1:
                                                break;
                                            case 0:
                                                i[e + n >> 0] = 0;
                                                break;
                                            default:
                                                u = (8388608 + (0 | A(0 | s[(a = e + n | 0) >> 0], 65793 * (255 & o) | 0)) | 0) >>> 24 & 255, i[a >> 0] = u
                                        }
                                        n = n + 1 | 0
                                    } while ((0 | n) != (0 | t))
                                }
                        }, function(e, r, t, n) {
                            e |= 0, n |= 0;
                            var o = 0,
                                a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                c = 0,
                                d = 0,
                                _ = 0;
                            if ((0 | (r |= 0)) > 0 & (0 | (t |= 0)) > 0)
                                for (o = e, a = t;;) {
                                    t = 0;
                                    do {
                                        u = o + (e = t << 1) | 0, c = 4369 * (255 & (l = 15 & (e = 0 | i[(f = o + (1 | e) | 0) >> 0]))) | 0, d = 0 | s[u >> 0], e = (0 | A(240 & (_ = 255 & e) | _ >>> 4, c)) >>> 16 & 255, _ = 255 & ((0 | A(c, 240 & d | d >>> 4)) >>> 16 & 240 | (0 | A(d << 4 & 240 | 15 & d, c)) >>> 20 & 15), i[u >> 0] = _, i[f >> 0] = -16 & e | l, t = t + 1 | 0
                                    } while ((0 | t) != (0 | r));
                                    if (!((0 | a) > 1)) break;
                                    o = o + n | 0, a = a + -1 | 0
                                }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    r = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = r + -16777216 & -16711936 | 16711935 & r, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                r = 0, i = 0 | f[n + -4 >> 2];
                                do {
                                    i = (-16711936 & (o = 0 | f[e + (r << 2) >> 2])) + (-16711936 & i) & -16711936 | (16711935 & o) + (16711935 & i) & 16711935, f[n + (r << 2) >> 2] = i, r = r + 1 | 0
                                } while ((0 | r) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    o = 0 | f[r + (i << 2) >> 2], a = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = (-16711936 & a) + (-16711936 & o) & -16711936 | (16711935 & a) + (16711935 & o) & 16711935, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    o = 0 | f[r + (i << 2) + 4 >> 2], a = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = (-16711936 & a) + (-16711936 & o) & -16711936 | (16711935 & a) + (16711935 & o) & 16711935, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    o = 0 | f[r + (i << 2) + -4 >> 2], a = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = (-16711936 & a) + (-16711936 & o) & -16711936 | (16711935 & a) + (16711935 & o) & 16711935, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    u = 0 | f[(a = r + (i << 2) | 0) >> 2], o = (-16711936 & (l = (((a = (((l = 0 | f[a + 4 >> 2]) ^ o) >>> 1 & 2139062143) + (l & o) | 0) ^ u) >>> 1 & 2139062143) + (a & u) | 0)) + (-16711936 & (u = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & l) + (16711935 & u) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    o = (-16711936 & (u = (((a = 0 | f[r + (i << 2) + -4 >> 2]) ^ o) >>> 1 & 2139062143) + (a & o) | 0)) + (-16711936 & (a = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & u) + (16711935 & a) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    o = (-16711936 & (u = (((a = 0 | f[r + (i << 2) >> 2]) ^ o) >>> 1 & 2139062143) + (a & o) | 0)) + (-16711936 & (a = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & u) + (16711935 & a) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    a = 0 | f[(o = r + (i << 2) | 0) - 4 >> 2], o = (((u = 0 | f[o >> 2]) ^ a) >>> 1 & 2139062143) + (u & a) | 0, a = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = (-16711936 & o) + (-16711936 & a) & -16711936 | (16711935 & o) + (16711935 & a) & 16711935, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            if ((0 | (t |= 0)) > 0) {
                                i = 0;
                                do {
                                    a = 0 | f[(o = r + (i << 2) | 0) >> 2], o = (((u = 0 | f[o + 4 >> 2]) ^ a) >>> 1 & 2139062143) + (u & a) | 0, a = 0 | f[e + (i << 2) >> 2], f[n + (i << 2) >> 2] = (-16711936 & o) + (-16711936 & a) & -16711936 | (16711935 & o) + (16711935 & a) & 16711935, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    u = 0 | f[(a = r + (i << 2) | 0) - 4 >> 2], l = 0 | f[a >> 2], s = 0 | f[a + 4 >> 2], a = ((u ^ o) >>> 1 & 2139062143) + (u & o) | 0, o = (-16711936 & (l = (((u = ((s ^ l) >>> 1 & 2139062143) + (s & l) | 0) ^ a) >>> 1 & 2139062143) + (u & a) | 0)) + (-16711936 & (a = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & l) + (16711935 & a) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    u = 0 | f[(a = r + (i << 2) | 0) >> 2], s = (o >>> 24) - (a = (l = 0 | f[a + -4 >> 2]) >>> 24) | 0, c = (u >>> 24) - a | 0, d = (o >>> 16 & 255) - (a = l >>> 16 & 255) | 0, _ = (u >>> 16 & 255) - a | 0, h = (o >>> 8 & 255) - (a = l >>> 8 & 255) | 0, m = (u >>> 8 & 255) - a | 0, o = (-16711936 & (a = (((0 | (l = (255 & o) - (a = 255 & l) | 0)) > -1 ? l : 0 - l | 0) - ((0 | (p = (255 & u) - a | 0)) > -1 ? p : 0 - p | 0) - ((0 | c) > -1 ? c : 0 - c | 0) + ((0 | s) > -1 ? s : 0 - s | 0) - ((0 | m) > -1 ? m : 0 - m | 0) + ((0 | h) > -1 ? h : 0 - h | 0) - ((0 | _) > -1 ? _ : 0 - _ | 0) + ((0 | d) > -1 ? d : 0 - d | 0) | 0) < 1 ? u : o)) + (-16711936 & (u = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & a) + (16711935 & u) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    o = (-16711936 & (l = ((a = ((u = 0 | f[(a = r + (i << 2) | 0) >> 2]) >>> 24) + (o >>> 24) - ((l = 0 | f[a + -4 >> 2]) >>> 24) | 0) >>> 0 < 256 ? a : a >>> 24 ^ 255) << 24 | ((d = (255 & u) + (255 & o) - (255 & l) | 0) >>> 0 < 256 ? d : d >>> 24 ^ 255) | ((s = (u >>> 16 & 255) + (o >>> 16 & 255) - (l >>> 16 & 255) | 0) >>> 0 < 256 ? s : s >>> 24 ^ 255) << 16 | ((c = (u >>> 8 & 255) + (o >>> 8 & 255) - (l >>> 8 & 255) | 0) >>> 0 < 256 ? c : c >>> 24 ^ 255) << 8)) + (-16711936 & (c = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & l) + (16711935 & c) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var i = 0,
                                o = 0,
                                a = 0,
                                u = 0,
                                l = 0,
                                s = 0,
                                c = 0,
                                d = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                i = 0, o = 0 | f[n + -4 >> 2];
                                do {
                                    u = 0 | f[(a = r + (i << 2) | 0) >> 2], l = 0 | f[a + -4 >> 2], s = (((u = (a = ((u ^ o) >>> 1 & 2139062143) + (u & o) | 0) >>> 24) - (l >>> 24) | 0) / 2 | 0) + u | 0, c = (((u = a >>> 16 & 255) - (l >>> 16 & 255) | 0) / 2 | 0) + u | 0, d = (((u = a >>> 8 & 255) - (l >>> 8 & 255) | 0) / 2 | 0) + u | 0, o = (-16711936 & (u = (s >>> 0 < 256 ? s : s >>> 24 ^ 255) << 24 | ((a = (((u = 255 & a) - (255 & l) | 0) / 2 | 0) + u | 0) >>> 0 < 256 ? a : a >>> 24 ^ 255) | (c >>> 0 < 256 ? c : c >>> 24 ^ 255) << 16 | (d >>> 0 < 256 ? d : d >>> 24 ^ 255) << 8)) + (-16711936 & (d = 0 | f[e + (i << 2) >> 2])) & -16711936 | (16711935 & u) + (16711935 & d) & 16711935, f[n + (i << 2) >> 2] = o, i = i + 1 | 0
                                } while ((0 | i) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, n |= 0;
                            var o, a, u, l = 0,
                                s = 0,
                                c = 0,
                                d = 0;
                            if (!((0 | (t |= 0)) <= 0)) {
                                o = 0 | i[e >> 0], a = 0 | i[e + 1 >> 0], u = 0 | i[e + 2 >> 0], e = 0;
                                do {
                                    l = 0 | f[r + (e << 2) >> 2], d = (c = ((0 | A(s = l << 16 >> 24, o)) >> 5) + (l >>> 16) | 0) << 16 & 16711680 | -16711936 & l | ((0 | A(a, s)) >>> 5) + l + ((0 | A(c << 24 >> 24, u)) >>> 5) & 255, f[n + (e << 2) >> 2] = d, e = e + 1 | 0
                                } while ((0 | e) != (0 | t))
                            }
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, t |= 0;
                            var o = 0,
                                a = 0,
                                u = 0,
                                f = 0,
                                l = 0,
                                s = 0;
                            if ((0 | (n |= 0)) > 0)
                                for (o = 0, a = e; e = 0 | d[a >> 1], u = 0 | d[a + 2 >> 1], f = 0 | d[a + 4 >> 1], s = (l = 33685504 + (0 | A(e, -9719)) + (0 | A(u, -19081)) + (28800 * f | 0) | 0) >> 18, i[r + o >> 0] = s >>> 0 < 256 ? s : 255 + (l >>> 31) | 0, f = (l = 33685504 + (28800 * e | 0) + (0 | A(u, -24116)) + (0 | A(f, -4684)) | 0) >> 18, i[t + o >> 0] = f >>> 0 < 256 ? f : 255 + (l >>> 31) | 0, (0 | (o = o + 1 | 0)) != (0 | n);) a = a + 8 | 0
                        }, function(e, r, t, n) {
                            e |= 0, r |= 0, t |= 0;
                            var i = 0,
                                o = 0;
                            if ((0 | (n |= 0)) > 0) {
                                i = 0;
                                do {
                                    a[(o = t + (i << 1) | 0) >> 1] = (0 | d[e + (i << 1) >> 1]) - (0 | d[r + (i << 1) >> 1]) + (0 | d[o >> 1]), i = i + 1 | 0
                                } while ((0 | i) != (0 | n))
                            }
                        }, Tt, Tt, Tt, Tt, Tt, Tt, Tt, Tt, Tt];
                    return {
                        _llvm_bswap_i32: wt,
                        stackSave: function() {
                            return 0 | S
                        },
                        _i64Subtract: it,
                        ___udivdi3: dt,
                        dynCall_iiiiiii: function(e, r, t, n, i, o, a) {
                            return r |= 0, t |= 0, n |= 0, i |= 0, o |= 0, a |= 0, 0 | Ot[7 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i, 0 | o, 0 | a)
                        },
                        setThrew: function(e, r) {
                            0, E || (E = e |= 0)
                        },
                        dynCall_viii: function(e, r, t, n) {
                            r |= 0, t |= 0, n |= 0, xt[31 & (e |= 0)](0 | r, 0 | t, 0 | n)
                        },
                        _bitshift64Lshr: ft,
                        _bitshift64Shl: mt,
                        _fflush: function e(r) {
                            r |= 0;
                            var t = 0,
                                n = 0,
                                i = 0,
                                o = 0,
                                a = 0,
                                u = 0;
                            do {
                                if (r) {
                                    if ((0 | f[r + 76 >> 2]) <= -1) {
                                        t = 0 | tt(r);
                                        break
                                    }
                                    n = !0, t = i = 0 | tt(r)
                                } else {
                                    if (o = 0 | f[229] ? 0 | e(0 | f[229]) : 0, i = 0 | (X(13092), 13100), n = 0 | f[i >> 2])
                                        for (i = n, n = o;;) {
                                            if (f[i + 76 >> 2], 0, u = (0 | f[i + 20 >> 2]) >>> 0 > (0 | f[i + 28 >> 2]) >>> 0 ? 0 | tt(i) | n : n, !(i = 0 | f[i + 56 >> 2])) {
                                                a = u;
                                                break
                                            }
                                            n = u
                                        } else a = o;
                                    V(13092), t = a
                                }
                            } while (0);
                            return 0 | t
                        },
                        dynCall_iii: function(e, r, t) {
                            return r |= 0, t |= 0, 0 | Ht[31 & (e |= 0)](0 | r, 0 | t)
                        },
                        _memset: at,
                        _sbrk: ht,
                        _memcpy: lt,
                        stackAlloc: function(e) {
                            var r;
                            return r = S, S = 15 + (S = S + (e |= 0) | 0) & -16, 0 | r
                        },
                        ___muldi3: _t,
                        _WebpToSDL: function(e, r) {
                            e |= 0, r |= 0;
                            var t, n, i, o, a, u, l = 0,
                                s = 0,
                                c = 0,
                                d = 0,
                                _ = 0,
                                h = 0,
                                m = 0,
                                p = 0;
                            if (t = S, S = S + 224 | 0, n = t + 16 | 0, i = t + 8 | 0, o = t, u = 40 + (a = t + 24 | 0) | 0, !(0 | function(e, r) {
                                return 0 == (0 | (e |= 0)) | 512 != (-256 & (r |= 0) | 0) ? 0 : (at(0 | e, 0, 200), Rr(e + 40 | 0, 520), 1)
                            }(a, 520))) return function(e, r, t, n) {
                                n |= 0;
                                var i;
                                i = 0 | A(t |= 0, r |= 0), (0 | f[n + 76 >> 2]) > -1 ? (t = !0, rt(1242, i, n)) : rt(1242, i, n)
                            }(0, 26, 1, 0 | f[105]), S = t, 0;
                            0 | f[2881] || (I(32), f[2881] = 1);
                            do {
                                if (0 | se(e, r, a, 520)) h = 0, m = 0, p = 0;
                                else {
                                    if (l = a + 4 | 0, s = 0 | R(0 | f[a >> 2], 0 | f[l >> 2], 32, 0), c = 0 | f[l >> 2], d = 0 | f[a >> 2], !s) {
                                        _ = 0 | f[105], f[o >> 2] = d, f[o + 4 >> 2] = c, nt(_, 1269, o), h = 0, m = 0, p = 0;
                                        break
                                    }
                                    if (!(_ = 0 | z(0, 0 | d, 0 | c, 32, 255, 65280, 16711680, -16777216))) {
                                        c = 0 | f[105], d = 0 | f[l >> 2], f[i >> 2] = f[a >> 2], f[i + 4 >> 2] = d, nt(c, 1310, i), h = 0, m = s, p = 0;
                                        break
                                    }
                                    if (B(0 | _), f[u >> 2] = 1, f[a + 44 >> 2] = f[_ + 8 >> 2], c = 0 | f[_ + 12 >> 2], f[a + 48 >> 2] = c, f[a + 56 >> 2] = f[_ + 20 >> 2], d = 0 | f[_ + 16 >> 2], f[a + 60 >> 2] = d, l = 0 | A(c, d), f[a + 64 >> 2] = l, f[a + 52 >> 2] = 1, 0 | (l = 0 | Ce(e, r, a))) {
                                        d = 0 | f[105], f[n >> 2] = l, nt(d, 1348, n), h = _, m = s, p = 0;
                                        break
                                    }
                                    K(0 | _), 0 | W(0 | _, 0, 0 | s, 0) ? (h = _, m = s, p = 0) : (h = _, m = s, p = 0 == (0 | J(0 | s)) & 1)
                                }
                            } while (0);
                            return F(0 | h), F(0 | m), Or(u), S = t, 0 | p
                        },
                        dynCall_vii: function(e, r, t) {
                            r |= 0, t |= 0, Rt[15 & (e |= 0)](0 | r, 0 | t)
                        },
                        ___uremdi3: vt,
                        dynCall_vi: function(e, r) {
                            r |= 0, Pt[31 & (e |= 0)](0 | r)
                        },
                        getTempRet0: function() {
                            return 0 | M
                        },
                        setTempRet0: function(e) {
                            M = e |= 0
                        },
                        _i64Add: ot,
                        dynCall_iiii: function(e, r, t, n) {
                            return r |= 0, t |= 0, n |= 0, 0 | Dt[15 & (e |= 0)](0 | r, 0 | t, 0 | n)
                        },
                        _pthread_mutex_unlock: bt,
                        _emscripten_get_global_libc: function() {
                            return 13028
                        },
                        dynCall_iiiii: function(e, r, t, n, i) {
                            return r |= 0, t |= 0, n |= 0, i |= 0, 0 | It[1 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i)
                        },
                        dynCall_ii: function(e, r) {
                            return r |= 0, 0 | Bt[7 & (e |= 0)](0 | r)
                        },
                        dynCall_viiii: function(e, r, t, n, i) {
                            r |= 0, t |= 0, n |= 0, i |= 0, Ut[31 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i)
                        },
                        ___errno_location: xe,
                        dynCall_viiiii: function(e, r, t, n, i, o) {
                            r |= 0, t |= 0, n |= 0, i |= 0, o |= 0, Ct[31 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i, 0 | o)
                        },
                        _free: Re,
                        runPostSets: function() {},
                        dynCall_viiiiii: function(e, r, t, n, i, o, a) {
                            r |= 0, t |= 0, n |= 0, i |= 0, o |= 0, a |= 0, Ft[15 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i, 0 | o, 0 | a)
                        },
                        establishStackSpace: function(e, r) {
                            S = e |= 0, 0
                        },
                        _memmove: pt,
                        stackRestore: function(e) {
                            S = e |= 0
                        },
                        _malloc: Pe,
                        _pthread_mutex_lock: ut,
                        _emscripten_replace_memory: function(e) {
                            return !(16777215 & v(e) || v(e) <= 16777215 || v(e) > 2147483648 || (i = new n(e), a = new o(e), f = new u(e), s = new l(e), d = new c(e), new _(e), new h(e), p = new m(e), t = e, 0))
                        },
                        dynCall_viiiiiiiii: function(e, r, t, n, i, o, a, u, f, l) {
                            r |= 0, t |= 0, n |= 0, i |= 0, o |= 0, a |= 0, u |= 0, f |= 0, l |= 0, Nt[7 & (e |= 0)](0 | r, 0 | t, 0 | n, 0 | i, 0 | o, 0 | a, 0 | u, 0 | f, 0 | l)
                        }
                    }
                }(Module.asmGlobalArg, Module.asmLibraryArg, buffer),
                stackSave = Module.stackSave = asm.stackSave,
                getTempRet0 = Module.getTempRet0 = asm.getTempRet0,
                ___udivdi3 = Module.___udivdi3 = asm.___udivdi3,
                setThrew = Module.setThrew = asm.setThrew,
                _bitshift64Lshr = Module._bitshift64Lshr = asm._bitshift64Lshr,
                _bitshift64Shl = Module._bitshift64Shl = asm._bitshift64Shl,
                _fflush = Module._fflush = asm._fflush,
                _memset = Module._memset = asm._memset,
                _sbrk = Module._sbrk = asm._sbrk,
                _memcpy = Module._memcpy = asm._memcpy,
                _llvm_bswap_i32 = Module._llvm_bswap_i32 = asm._llvm_bswap_i32,
                ___muldi3 = Module.___muldi3 = asm.___muldi3,
                _WebpToSDL = Module._WebpToSDL = asm._WebpToSDL,
                ___uremdi3 = Module.___uremdi3 = asm.___uremdi3,
                stackAlloc = Module.stackAlloc = asm.stackAlloc,
                _i64Subtract = Module._i64Subtract = asm._i64Subtract,
                setTempRet0 = Module.setTempRet0 = asm.setTempRet0,
                _i64Add = Module._i64Add = asm._i64Add,
                _pthread_mutex_unlock = Module._pthread_mutex_unlock = asm._pthread_mutex_unlock,
                _emscripten_get_global_libc = Module._emscripten_get_global_libc = asm._emscripten_get_global_libc,
                ___errno_location = Module.___errno_location = asm.___errno_location,
                _free = Module._free = asm._free,
                runPostSets = Module.runPostSets = asm.runPostSets,
                establishStackSpace = Module.establishStackSpace = asm.establishStackSpace,
                _memmove = Module._memmove = asm._memmove,
                stackRestore = Module.stackRestore = asm.stackRestore,
                _malloc = Module._malloc = asm._malloc,
                _pthread_mutex_lock = Module._pthread_mutex_lock = asm._pthread_mutex_lock,
                _emscripten_replace_memory = Module._emscripten_replace_memory = asm._emscripten_replace_memory,
                dynCall_iiii = Module.dynCall_iiii = asm.dynCall_iiii,
                dynCall_viiiii = Module.dynCall_viiiii = asm.dynCall_viiiii,
                dynCall_vi = Module.dynCall_vi = asm.dynCall_vi,
                dynCall_vii = Module.dynCall_vii = asm.dynCall_vii,
                dynCall_iiiiiii = Module.dynCall_iiiiiii = asm.dynCall_iiiiiii,
                dynCall_ii = Module.dynCall_ii = asm.dynCall_ii,
                dynCall_viii = Module.dynCall_viii = asm.dynCall_viii,
                dynCall_viiiiiiiii = Module.dynCall_viiiiiiiii = asm.dynCall_viiiiiiiii,
                dynCall_iiiii = Module.dynCall_iiiii = asm.dynCall_iiiii,
                dynCall_viiiiii = Module.dynCall_viiiiii = asm.dynCall_viiiiii,
                dynCall_iii = Module.dynCall_iii = asm.dynCall_iii,
                dynCall_viiii = Module.dynCall_viiii = asm.dynCall_viiii,
                initialStackTop;

            function ExitStatus(e) {
                this.name = "ExitStatus", this.message = "Program terminated with exit(" + e + ")", this.status = e
            }
            Runtime.stackAlloc = Module.stackAlloc, Runtime.stackSave = Module.stackSave, Runtime.stackRestore = Module.stackRestore, Runtime.establishStackSpace = Module.establishStackSpace, Runtime.setTempRet0 = Module.setTempRet0, Runtime.getTempRet0 = Module.getTempRet0, Module.asm = asm, ExitStatus.prototype = new Error, ExitStatus.prototype.constructor = ExitStatus;
            var preloadStartTime = null,
                calledMain = !1;

            function run(e) {
                function r() {
                    Module.calledRun || (Module.calledRun = !0, ABORT || (ensureInitRuntime(), preMain(), Module.onRuntimeInitialized && Module.onRuntimeInitialized(), Module._main && shouldRunNow && Module.callMain(e), postRun()))
                }
                e = e || Module.arguments, null === preloadStartTime && (preloadStartTime = Date.now()), runDependencies > 0 || (preRun(), runDependencies > 0 || Module.calledRun || (Module.setStatus ? (Module.setStatus("Running..."), setTimeout(function() {
                    setTimeout(function() {
                        Module.setStatus("")
                    }, 1), r()
                }, 1)) : r()))
            }

            function exit(e, r) {
                r && Module.noExitRuntime || (Module.noExitRuntime || (ABORT = !0, EXITSTATUS = e, STACKTOP = initialStackTop, exitRuntime(), Module.onExit && Module.onExit(e)), ENVIRONMENT_IS_NODE && process.exit(e), Module.quit(e, new ExitStatus(e)))
            }
            dependenciesFulfilled = function e() {
                Module.calledRun || run(), Module.calledRun || (dependenciesFulfilled = e)
            }, Module.callMain = Module.callMain = function(r) {
                r = r || [], ensureInitRuntime();
                var t = r.length + 1;

                function n() {
                    for (var e = 0; e < 3; e++) i.push(0)
                }
                var i = [allocate(intArrayFromString(Module.thisProgram), "i8", ALLOC_NORMAL)];
                n();
                for (var o = 0; o < t - 1; o += 1) i.push(allocate(intArrayFromString(r[o]), "i8", ALLOC_NORMAL)), n();
                i.push(0), i = allocate(i, "i32", ALLOC_NORMAL);
                try {
                    exit(Module._main(t, i, 0), !0)
                } catch (e) {
                    if (e instanceof ExitStatus) return;
                    if ("SimulateInfiniteLoop" == e) return void(Module.noExitRuntime = !0);
                    var a = e;
                    e && "object" == typeof e && e.stack && (a = [e, e.stack]), Module.printErr("exception thrown: " + a), Module.quit(1, e)
                } finally {
                    calledMain = !0
                }
            }, Module.run = Module.run = run, Module.exit = Module.exit = exit;
            var abortDecorators = [];

            function abort(e) {
                Module.onAbort && Module.onAbort(e), void 0 !== e ? (Module.print(e), Module.printErr(e), e = JSON.stringify(e)) : e = "", ABORT = !0, EXITSTATUS = 1;
                var r = "abort(" + e + ") at " + stackTrace() + "\nIf this abort() is unexpected, build with -s ASSERTIONS=1 which can give more information.";
                throw abortDecorators && abortDecorators.forEach(function(t) {
                    r = t(r, e)
                }), r
            }
            if (Module.abort = Module.abort = abort, Module.preInit)
                for ("function" == typeof Module.preInit && (Module.preInit = [Module.preInit]); Module.preInit.length > 0;) Module.preInit.pop()();
            var shouldRunNow = !1;
            Module.noInitialRun && (shouldRunNow = !1), run(), this.Module = Module, this.webpToSdl = Module.cwrap("WebpToSDL", "number", ["array", "number"]), this.setCanvas = function(e) {
                Module.canvas = e
            }
        }
        _$webp_9.exports = {
            Webp: Webp
        }
    }).call(this, _$browser_3), _$webp_9 = _$webp_9.exports;
    var _$webpMachine_8 = {},
        __extendStatics_8, ____extends_8 = this && this.__extends || (__extendStatics_8 = function(e, r) {
            return (__extendStatics_8 = Object.setPrototypeOf || {
                    __proto__: []
                }
                instanceof Array && function(e, r) {
                    e.__proto__ = r
                } || function(e, r) {
                    for (var t in r) r.hasOwnProperty(t) && (e[t] = r[t])
                })(e, r)
        }, function(e, r) {
            function t() {
                this.constructor = e
            }
            __extendStatics_8(e, r), e.prototype = null === r ? Object.create(r) : (t.prototype = r.prototype, new t)
        }),
        ____awaiter_8 = this && this.__awaiter || function(e, r, t, n) {
            return new(t || (t = Promise))(function(i, o) {
                function a(e) {
                    try {
                        f(n.next(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function u(e) {
                    try {
                        f(n.throw(e))
                    } catch (r) {
                        o(r)
                    }
                }

                function f(e) {
                    e.done ? i(e.value) : new t(function(r) {
                        r(e.value)
                    }).then(a, u)
                }
                f((n = n.apply(e, r || [])).next())
            })
        },
        ____generator_8 = this && this.__generator || function(e, r) {
            var t, n, i, o, a = {
                label: 0,
                sent: function() {
                    if (1 & i[0]) throw i[1];
                    return i[1]
                },
                trys: [],
                ops: []
            };
            return o = {
                next: u(0),
                throw: u(1),
                return: u(2)
            }, "function" == typeof Symbol && (o[Symbol.iterator] = function() {
                return this
            }), o;

            function u(o) {
                return function(u) {
                    return function(o) {
                        if (t) throw new TypeError("Generator is already executing.");
                        for (; a;) try {
                            if (t = 1, n && (i = 2 & o[0] ? n.return : o[0] ? n.throw || ((i = n.return) && i.call(n), 0) : n.next) && !(i = i.call(n, o[1])).done) return i;
                            switch (n = 0, i && (o = [2 & o[0], i.value]), o[0]) {
                                case 0:
                                case 1:
                                    i = o;
                                    break;
                                case 4:
                                    return a.label++, {
                                        value: o[1],
                                        done: !1
                                    };
                                case 5:
                                    a.label++, n = o[1], o = [0];
                                    continue;
                                case 7:
                                    o = a.ops.pop(), a.trys.pop();
                                    continue;
                                default:
                                    if (!(i = (i = a.trys).length > 0 && i[i.length - 1]) && (6 === o[0] || 2 === o[0])) {
                                        a = 0;
                                        continue
                                    }
                                    if (3 === o[0] && (!i || o[1] > i[0] && o[1] < i[3])) {
                                        a.label = o[1];
                                        break
                                    }
                                    if (6 === o[0] && a.label < i[1]) {
                                        a.label = i[1], i = o;
                                        break
                                    }
                                    if (i && a.label < i[2]) {
                                        a.label = i[2], a.ops.push(o);
                                        break
                                    }
                                    i[2] && a.ops.pop(), a.trys.pop();
                                    continue
                            }
                            o = r.call(e, a)
                        } catch (u) {
                            o = [6, u], n = 0
                        } finally {
                            t = i = 0
                        }
                        if (5 & o[0]) throw o[1];
                        return {
                            value: o[0] ? o[1] : void 0,
                            done: !0
                        }
                    }([o, u])
                }
            }
        };
    Object.defineProperty(_$webpMachine_8, "__esModule", {
        value: !0
    });
    var WebpMachineError = function(e) {
        function r() {
            return null !== e && e.apply(this, arguments) || this
        }
        return ____extends_8(r, e), r
    }(Error);
    _$webpMachine_8.WebpMachineError = WebpMachineError;
    var WebpMachine = function() {
        function e(e) {
            var r = void 0 === e ? {} : e,
                t = r.webp,
                n = void 0 === t ? new _$webp_9.Webp : t,
                i = r.webpSupport,
                o = void 0 === i ? _$detectWebpSupport_4.detectWebpSupport() : i;
            this.busy = !1, this.cache = {}, this.webp = n, this.webpSupport = o
        }
        return e.prototype.decode = function(e) {
            return ____awaiter_8(this, void 0, void 0, function() {
                var r, t;
                return ____generator_8(this, function(n) {
                    switch (n.label) {
                        case 0:
                            if (this.busy) throw new WebpMachineError("cannot decode when already busy");
                            this.busy = !0, n.label = 1;
                        case 1:
                            return n.trys.push([1, 3, , 4]), [4, new Promise(function(e) {
                                return requestAnimationFrame(e)
                            })];
                        case 2:
                            return n.sent(), r = document.createElement("canvas"), this.webp.setCanvas(r), this.webp.webpToSdl(e, e.length), this.busy = !1, [2, r.toDataURL()];
                        case 3:
                            throw t = n.sent(), this.busy = !1, t.name = WebpMachineError.name, t.message = "failed to decode webp image: " + t.message, t;
                        case 4:
                            return [2]
                    }
                })
            })
        }, e.prototype.polyfillImage = function(e, r) {
            return ____awaiter_8(this, void 0, void 0, function() {
                var t, n, i, o, a;
                return ____generator_8(this, function(u) {
                    switch (u.label) {
                        case 0:
                            return [4, this.webpSupport];
                        case 1:
                            if (u.sent()) return [2, null];
                            t = 0, u.label = 2;
                        case 2:
                            if (!(t < r.length)) return [3, 8];
                            if (n = e.getAttribute(r[t]), !/\.webp$/i.test(n)) return [3, 7];
                            if (this.cache[n]) return e.setAttribute(r[t], this.cache[n]), [2, null];
                            u.label = 3;
                        case 3:
                            return u.trys.push([3, 6, , 7]), [4, _$loadBinaryData_6.loadBinaryData(n)];
                        case 4:
                            return i = u.sent(), [4, this.decode(i)];
                        case 5:
                            return o = u.sent(), this.cache[n] = o, e.setAttribute(r[t], o), [3, 7];
                        case 6:
                            throw (a = u.sent()).name = WebpMachineError.name, a.message = 'failed to polyfill image "' + n + '": ' + a.message, a;
                        case 7:
                            return t++, [3, 2];
                        case 8:
                            return [2]
                    }
                })
            })
        }, e.prototype.polyfillDocument = function(e, r, t) {
            var n = (void 0 === e ? {} : e).document,
                i = void 0 === n ? window.document : n;
            return ____awaiter_8(this, void 0, void 0, function() {
                var e, n, o, a, u;
                return ____generator_8(this, function(f) {
                    switch (f.label) {
                        case 0:
                            return [4, this.webpSupport];
                        case 1:
                            if (f.sent()) return [2, null];
                            e = 0, f.label = 2;
                        case 2:
                            if (!(e < r.length)) return [3, 9];
                            n = 0, o = Array.from(i.querySelectorAll(r[e])), f.label = 3;
                        case 3:
                            if (!(n < o.length)) return [3, 8];
                            a = o[n], f.label = 4;
                        case 4:
                            return f.trys.push([4, 6, , 7]), [4, this.polyfillImage(a, t)];
                        case 5:
                            return f.sent(), [3, 7];
                        case 6:
                            throw (u = f.sent()).name = WebpMachineError.name, u.message = 'webp image polyfill failed for image "' + a + '": ' + u, u;
                        case 7:
                            return n++, [3, 3];
                        case 8:
                            return e++, [3, 2];
                        case 9:
                            return [2]
                    }
                })
            })
        }, e
    }();
    _$webpMachine_8.WebpMachine = WebpMachine;
    var _$distCjs_5 = {};

    function __export(e) {
        for (var r in e) _$distCjs_5.hasOwnProperty(r) || (_$distCjs_5[r] = e[r])
    }
    Object.defineProperty(_$distCjs_5, "__esModule", {
        value: !0
    }), __export(_$detectWebpSupport_4), __export(_$loadBinaryData_6), __export(_$webpMachine_8);
    var _$webpHeroBundle_7 = {};
    Object.defineProperty(_$webpHeroBundle_7, "__esModule", {
        value: !0
    }), window.webpHero = _$distCjs_5
}();