/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 webware,Inc
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
(function(window) {
    "use strict";

    var self = null;

    function OpenppWebPush(params) {

        let defaults = {
            swPath: '/serviceworker.js',
            swScope: './',
            keyPath: '/api/push/key/publicKey',
            registerPath: '/api/push/device/web/register',
            unregisterPath: '/api/push/device/web/unregister',
            applicationId: null
        };

        params = params || {};

        for (let def in defaults) {
            if (typeof params[def] === 'undefined') {
                params[def] = defaults[def];
            }
        }

        this.swPath = params.swPath;
        this.swScope = params.swScope;
        this.keyPath = params.keyPath;
        this.registerPath = params.registerPath;
        this.unregisterPath = params.unregisterPath;
        this.applicationId = params.applicationId;

        this._serverPublicKey = null;
        this._subscription = null;
        this._registration = null;

        this._listeners = null;

        self = this;
    }

    // a simplified event system
    function Event(type) {
        this.type = type;
    }

    OpenppWebPush.Event = Event;

    var p = OpenppWebPush.prototype;

    // a simplified event system
    p.addEventListener = function(type, listener) {
        let listeners = this._listeners = this._listeners||{};
        let arr = listeners[type];
        if (arr) {
            this.removeEventListener(type, listener);
        }
        arr = listeners[type]; // remove may have deleted the array
        if (!arr) {
            listeners[type] = [listener];
        }
        else {
            arr.push(listener);
        }
        return listener;
    };

    // a simplified event system
    p.removeEventListener = function(type, listener) {
        let listeners = this._listeners;
        if (!listeners) { return; }
        let arr = listeners[type];
        if (!arr) { return; }
        for (let i = 0,l = arr.length; i < l; i++) {
            if (arr[i] == listener) {
                if (l == 1) {
                    delete(listeners[type]);
                } else {
                    arr.splice(i, 1);
                }
                break;
            }
        }
    };

    // a simplified event system
    p.dispatchEvent = function(eventObj) {
        if (typeof eventObj == "string") {
            let listeners = self._listeners;
            if ((!listeners || !listeners[eventObj])) {
                return true;
            }
            eventObj = new OpenppWebPush.Event(eventObj);
        }

        let listeners = self._listeners;
        if (eventObj && listeners) {
            let arr = listeners[eventObj.type];
            if (!arr || !(arr.length)) {
                return;
            }
            arr = arr.slice();
            for (let i = 0; i < arr.length; i++) {
                let o = arr[i];
                if (o.handleEvent) {
                    o.handleEvent(eventObj);
                }
                else {
                    o(eventObj);
                }
            }
        }
    };

    p._triggerUnsupportedEvent = function(message) {
        let event = new OpenppWebPush.Event("unsupported");
        event.message = message;
        self.dispatchEvent(event);
    };

    p._triggerErrorEvent = function(message) {
        let event = new OpenppWebPush.Event("error");
        event.message = message;
        self.dispatchEvent(event);
    };

    p._triggerStateChangeEvent = function(state) {
        let event = new OpenppWebPush.Event("statechange");
        event.state = state;
        self.dispatchEvent(event);
    };

    p.initialize = function() {
        if (self.isServiceWorkerSupported()) {
            navigator.serviceWorker.register(self.swPath, { scope: self.swScope })
                .then(self._checkSubscription)
                .catch(function(e) {
                   let message = 'Failed to register the service worker.';
                    console.log(message + e);
                    self._triggerErrorEvent(message);
                });
        } else {
            let message = 'Unsupported property "serviceWorker" in navigator.';
            console.log(message);
            self._triggerUnsupportedEvent(message);
        }
    };

    p.isServiceWorkerSupported = function() {
        if ('serviceWorker' in navigator) {
            return true;
        }
        return false;
    };

    p.setApplicationId = function(applicationId) {
        this.applicationId = applicationId;
    };

    p.togglePushRequest = function(on) {
        if (on) {
            self._requestPermission();
        } else {
            self._unsubscribe();
        }
    };

    p._requestPermission = function() {
        Notification.requestPermission(permission => {
            if (permission !== 'denied') {
                self._subscribe();
            }
        });
    };

    p._checkSubscription = function(registration) {
        if ('pushManager' in registration) {
            self._fetchServerPublicKey().then(key => {
                registration.pushManager.getSubscription().then(subscription => {
                    if (subscription) {
                        self._subscription = subscription;
                        self._triggerStateChangeEvent("subscribing");
                    } else {
                        self._triggerStateChangeEvent("unsubscribing");
                    }
                })
                .catch(error => {
                    self._triggerErrorEvent(error);
                });
            })
            .catch(error => {
                self._triggerErrorEvent(error);
            });
        } else {
            let message = 'Unsupported property "pushManager" in ServiceWorkerRegistration.';
            console.log(message);
            self._triggerUnsupportedEvent(message);
        }
    };

    p._subscribe = function() {
        navigator.serviceWorker.getRegistration().then(registration => {
            if (registration) {
                let opt = {
                    userVisibleOnly: true,
                    applicationServerKey: self._serverPublicKey
                };
                registration.pushManager.subscribe(opt).then(subscription => {
                    self._subscription = subscription;
                    self._register(subscription);
                })
                .catch(function(e) {
                    let message = 'Failed to subscribe.';
                    console.log(message + e);
                    self._triggerErrorEvent(message);
                });
            } else {
                let message = 'ServiceWorker is not registered.';
                self._triggerErrorEvent(message);
            }
        });
    };

    p._unsubscribe = function() {
        if (self._subscription) {
            self._unregister(self._subscription);
            self._subscription.unsubscribe();
            self._subscription = null;
            self._triggerStateChangeEvent("unsubscribing");
        }
    };

    p._encodeBase64URL = function(buffer) {
        return btoa(String.fromCharCode.apply(null, new Uint8Array(buffer))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    };

    p._decodeBase64URL = function(str) {
        let dec = atob(str.replace(/\-/g, '+').replace(/_/g, '/'));
        let buffer = new Uint8Array(dec.length);
        for (let i = 0; i < dec.length; i++) {
            buffer[i] = dec.charCodeAt(i);
        }
        return buffer;
    };

    p._fetchServerPublicKey = function() {
        return new Promise((resolve, reject) => {
            fetch(self.keyPath).then(resp => {
                return resp.text();
            }).then(text => {
                try {
                    self._serverPublicKey = self._decodeBase64URL(text);
                    resolve(self._serverPublicKey);
                } catch (e) {
                    let message = 'Failed to fetch the server public key.';
                    console.log(message + e);
                    reject(message);
                }
            });
        });
    };

    p._register = function(subscription) {
        if ('getKey' in subscription) {
            var key = self._encodeBase64URL(subscription.getKey('p256dh'));
            try {
                var auth = self._encodeBase64URL(subscription.getKey('auth'));
            } catch(e) {
                let message = 'Failed to get authorization token.';
                console.log(message + e);
                self._triggerUnsupportedEvent(message);
            }
        } else {
            let message = 'Undefined function "getKey" in PushSubscription.';
            console.log(message);
            self._triggerUnsupportedEvent(message);
            return;
        }

        let arg = {
            application_id: new URL(location.href).origin,
            endpoint: subscription.endpoint,
            key: key,
            auth: auth
        };

        if (self.applicationId) {
            arg.application_id = self.applicationId;
        }

        fetch(self.registerPath, {
            method: 'POST',
            body: JSON.stringify(arg),
            headers: { 'Content-Type': 'application/json' }
        }).then(resp => {
            return resp.json();
        }).then(json => {
            if ('code' in json) {
                console.log(json.message);
                self._triggerErrorEvent('Faild to register to the server. ' + json.message);
            }
            else {
                self._registration = json;
            }
        });
    };

    p._unregister = function(subscription) {
        let arg = {
            application_id: new URL(location.href).origin,
            endpoint: subscription.endpoint
        };

        if (self.applicationId) {
            arg.application_id = self.applicationId;
        }

        fetch(self.unregisterPath, {
            method: 'POST',
            body: JSON.stringify(arg),
            headers: { 'Content-Type': 'application/json' }
        }).then(resp => {
            return resp.json();
        }).then(json => {
            if ('code' in json) {
                console.log(json.message);
                self._triggerErrorEvent('Faild to unregister from the server. ' + json.message);
            }
            else {
                self._registration = null;
            }
        });
    };

    window.OpenppWebPush = OpenppWebPush;

})(window);