if (!socproof) {
    var socproof = {
        initDone: false, //защита от двойной инициализации
        countOfShow: 0,
        localStorageJS: {},
        initiate: function () {
            if (this.initDone) {
                return;
            }
            this.initDone = true;
            this.localStorageAvailable = this.localStorageTest();
            if (this.localStorageAvailable) {
                var arr = JSON.parse(localStorage.getItem('statsQueue')) || {};
                var arrSize = 0;
                for (var t in arr) {
                    arrSize++;
                }
                if (arrSize > 0) {
                    // send batch
                    this.tr(false, false);
                }
            }
            //Инициализация начальных стилей
            this.applyDefaultStyling();
            // определяем текущий язык по локации пользователя(пока не надо)
            var language = this.language;
            if (this.translate_messages) {
                var userLanguage = window.navigator.languages[0]
                    || window.navigator.language
                    || window.navigator.browserLanguage
                    || window.navigator.systemLanguage
                    || window.navigator.userLanguage;
                userLanguage = userLanguage.split('-')[0].toLowerCase(); //текущий язык

                if (this.availableLanguages.indexOf(userLanguage) > -1) {
                    language = userLanguage; //пригодится при доработке
                }
            }
            if (!this.allowClose && this.localStorageAvailable && localStorage.getItem('disable-fomo-' + this.clientId)) {
                localStorage.removeItem('disable-fomo-' + this.clientId);
            }
            if (this.isSocProofEnabled()) {
                this.initSecondScript();
            } else {
                this.consoleLog("SocProof is currently turned off on this domain.");
            }
        },
        initSecondScript: function () {
            if (this.randomDisplay) {
                this.events = this.shuffle(this.events);
            }
            setTimeout(function () {
                socproof.runNotifications();
            }, this.initialDelay + 1);
        },
        ua: navigator.userAgent.toLowerCase(), //браузер лоуеркейсом
        applyDefaultStyling: function () { //подключение стартовой css-ки
            if (this.themeCss) {
                var cssElement = document.createElement("link");
                cssElement.setAttribute("id", "dev_project_x_layout_css");
                cssElement.setAttribute("rel", "stylesheet");
                cssElement.setAttribute("type", "text/css");
                cssElement.setAttribute("href", this.themeCss);
                document.getElementsByTagName("head")[0].appendChild(cssElement);
            }
        },
        isSocProofEnabled: function () {                //доступность
            var enabled = true;
            // скрыть для мобильных устройств?
            if (this.hideInMobileVersion && this.isMobileDevice()) {
                enabled = false;
            }
            // удаляем поддержку <IE9
            if (this.isIE() && (this.isIE() === 7 || this.isIE() === 8)) {
                enabled = false;
            }

            return enabled;
        },
        isMobileDevice: function () {
            // "мобильные устройства" - любые устройства с размером экрана <767px;
            return (window && window.innerWidth && window.innerWidth < 767) ? 1 : 0;
        },
        isSafariEight: function () {
            var self = this;
            var check = function (r) {
                return r.test(self.ua);
            }
            var isChrome = check(/\bchrome\b/);
            var isSafari = !isChrome && check(/safari/);
            if (isSafari && check(/version\/8/)) {
                return true;
            } else if (isSafari && check(/version\/7/)) {
                return true;
            } else {
                return false;
            }
        },
        isIE: function () {
            return (this.ua.indexOf('msie') != -1) ? parseInt(this.ua.split('msie')[1]) : false;
        },
        localStorageTest: function () {//просто можно ли создать??
            var test = 'test';
            try {
                localStorage.setItem(test, test);
                localStorage.removeItem(test);
                return true;
            } catch (e) {
                return false;
            }
        },
        shuffle: function (array) {//для рандомного отображения нотификаций
            var currentIndex = array.length, temporaryValue, randomIndex;
            while (0 !== currentIndex) {
                randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex -= 1;
                temporaryValue = array[currentIndex];
                array[currentIndex] = array[randomIndex];
                array[randomIndex] = temporaryValue;
            }
            return array;
        },
        clearLocalStorageInProgress: false,
        clearLocalStorage: function () {
            if (this.clearLocalStorageInProgress) {
                return;
            }
            this.clearLocalStorageInProgress = true;

            if (this.localStorageAvailable) {
                for (var i = localStorage.length - 1; i >= 0; i--) {
                    var localStorageKey = localStorage.key(i);
                    if (localStorageKey && localStorageKey.split('-')[0] === 'snv') {
                        try {
                            localStorage.removeItem(localStorageKey);
                        } catch (ign) {
                        }
                    }
                }
            } else {
                for (var key in this.localStorageJS) {
                    if (key && key.split('-')[0] === 'snv') {
                        delete this.localStorageJS[key];
                    }
                }
            }
            this.clearLocalStorageInProgress = false;
        },
        ifCanShow: function () {
            if(localStorage.getItem("closedTime")) {
                var closedTime = parseInt(localStorage.getItem("closedTime")),
                    now = new Date().getTime();

                    var resTime = parseInt((now - closedTime) / 1000);

                    return resTime >= 3600;
            }

            return true;
        },
        runNotifications: function () {
            //очищаем блок после проигрыша всех событий
            if(!this.ifCanShow()) {
                return false;
            }
            var el = document.getElementById('project_x__block');
            if (el) {
                el.parentNode.removeChild(el);
            }
            var notificationBlockDiv = document.createElement('div');
            notificationBlockDiv.setAttribute("id", "project_x__block");
            this.setHolderClasses(notificationBlockDiv, 'fadeInUp');
            notificationBlockDiv.innerHTML = this.html;
            document.body.appendChild(notificationBlockDiv);

            this.post(this.actionId, 'viewed');

            this.notificationBlockDiv = document.getElementById('project_x__block');
            var self = this;

            if(this.time) {
                setTimeout(function() {
                    self.animateOut(self.notificationBlockDiv);
                }, self.time);
            }

            if(this.allowClose) {
                document.getElementById('x-popup-close').addEventListener('click', function (evt) {
                    evt.preventDefault();
                    localStorage.setItem("closedTime", new Date().getTime().toString());
                    self.post(self.actionId, 'closed');

                    console.log('close');
                    self.animateOut(self.notificationBlockDiv);
                }, false);
            }

            document.getElementById('x-popup-url').addEventListener('click', function (evt) {
                evt.preventDefault();
                localStorage.setItem("closedTime", new Date().getTime().toString());
                self.post(self.actionId, 'clicked');

                self.animateOut(self.notificationBlockDiv);

                self.visitDestination(self.url, '');
            }, false);

            document.getElementById('x-popup-img-url').addEventListener('click', function (evt) {
                evt.preventDefault();
                localStorage.setItem("closedTime", new Date().getTime().toString());
                self.post(self.actionId, 'clicked');

                self.animateOut(self.notificationBlockDiv);

                self.visitDestination(self.url, '');
            }, false);

            // var eventList = this.events;
            // if (eventList && eventList.length) {
            //     var that = this;
            //     var baseDelay = that.delayBetweenNotification * 1;
            //     //цикл с задержкой между нотификациями
            //     (function theLoop(index) {
            //         if (that.countOfShow >= that.maxNotificationCount) {
            //             return false;
            //         }
            //         var event = eventList[index];
            //         that.displayNotification(event);
            //         that.countOfShow++;
            //         //логируем показ
            //         that.post(event.eventId, "show");
            //         // подготовим следующий показ
            //         index++;
            //         var loopDelay = baseDelay;
            //         if (that.randomDelayBetween) {
            //             var min = Math.floor(baseDelay / 2);
            //             var max = baseDelay * 2;
            //             loopDelay = Math.floor(Math.random() * (max - min)) + min;
            //         }
            //         if (index < eventList.length) {
            //             setTimeout(function () {
            //                 theLoop(index);
            //             }, loopDelay);
            //         } else if (that.repeat) {
            //             index = 0;
            //             setTimeout(function () {
            //                 theLoop(index);
            //             }, loopDelay);
            //         }
            //     })(0); // Сразу вызовем для первого события
            // }
        },
        displayNotification: function (event) {
            function getRandom(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }
            var self = this;
            //var now = Math.round(new Date().getTime() / 1000);
            this.notificationBlockDiv = document.getElementById('socproof__block');
            var message = event.messageText;
            var arr = message.match(/<name>.+;+.+<\/name>/g);
            if (arr) {
                for (i = 0; i < arr.length; i++) {
                    var values = arr[i].substring(6, arr[i].length - 7);
                    var mas = values.split(';');
                    var randomIndex = getRandom(0, mas.length - 1);
                    message = message.replace(arr[i], mas[randomIndex]);
                }
            }
            arr = message.match(/<time>.+;+.+<\/time>/g);
            if (arr) {
                for (i = 0; i < arr.length; i++) {
                    var values = arr[i].substring(6, arr[i].length - 7);
                    var mas = values.split(';');
                    var randomIndex = getRandom(0, mas.length - 1);
                    message = message.replace(arr[i], mas[randomIndex]);
                }
            }

            arr = message.match(/<quantity>.+;+.+<\/quantity>/g);
            if (arr) {
                for (i = 0; i < arr.length; i++) {
                    var values = arr[i].substring(10, arr[i].length - 11);
                    var mas = values.split(';');
                    var randomIndex = getRandom(0, mas.length - 1);
                    message = message.replace(arr[i], mas[randomIndex]);
                }
            }
            // проверяем, стоит ли вообще отсылать геозапрос
            if (message.indexOf("{{city") > -1 || message.indexOf("{{country}}") > -1) {
                var city = geoResponse.city;
                var country = geoResponse.country_name;
                if (city == '') {
                    var messageVariableParts = message.match(/\{\{city.*?\}\}/g);
                    for (ind = 0; ind < messageVariableParts.length; ind++) {
                        var begin = messageVariableParts[ind].indexOf('{{city ');
                        if (begin != -1) {
                            var end = messageVariableParts[ind].indexOf('}}', begin);
                            var arr = messageVariableParts[ind].substring(begin, end).split(' ');
                            city = arr[1];
                        }
                        message = message.replace(messageVariableParts[ind], city);
                    }
                    message = message.replace(/\{\{\s?country\s?\}\}(?!\})/g, country);
                } else {
                    message = message.replace(/\{\{\s?city.*?\}\}(?!\})/g, city).replace(/\{\{\s?country\s?\}\}(?!\})/g, country);
                }
            }
            //создаем дивку нотификации
            var notificationDiv = document.createElement('div');
            notificationDiv.setAttribute("class", "socproof__notification");
            notificationDiv.addEventListener('click', function (evt) {
                self.visitDestination(event, evt.target);
            }, false);

            //document.body.appendChild(notificationDiv);

            // Добавляем закрытие окошка
            if (this.allowClose) {
                var closeSpan = document.createElement('span');
                closeSpan.setAttribute("class", "socproof__close");
                closeSpan.setAttribute("id", "socproof__close_id");
                notificationDiv.appendChild(closeSpan);
            }
            var has_image = event.imageUrl;
            // Добавляем изображение, если доступно
            if (has_image) {
                var imgDiv = document.createElement("div");
                imgDiv.setAttribute("class", "socproof__img");

                var imgObj = document.createElement("img");
                imgObj.setAttribute("src", has_image);

                imgDiv.appendChild(imgObj);
                notificationDiv.appendChild(imgDiv);
            }

            var infoDiv = document.createElement('div');
            infoDiv.setAttribute("class", "socproof__info");

            var textDiv = document.createElement('div');
            textDiv.setAttribute("class", "socproof__text");
            infoDiv.appendChild(textDiv);
            textDiv.innerHTML = message;

            if (this.poweredByEnabled) {
                var linkPowered = document.createElement("a");
                linkPowered.setAttribute("class", "socproof__powered");
                linkPowered.setAttribute("id", "socproof__powered_id");
                linkPowered.setAttribute("href", 'https://socproofy.ru/');
                linkPowered.setAttribute("target", "_blank");
                linkPowered.innerHTML = "Powered By SocProofy";
                infoDiv.appendChild(linkPowered);
            }
            // обновляем текст
            notificationDiv.appendChild(infoDiv);
            this.notificationBlockDiv.appendChild(notificationDiv);
            this.animateIn(notificationDiv);
            if (this.allowClose) {
                closeSpan.addEventListener('click', function (evt) {
                    evt.preventDefault();
                    self.animateOut(notificationDiv);
                }, false);
            }

            var fomoImg = notificationDiv.getElementsByTagName('img')[0];
            if (fomoImg) { //клик по ссылке
                fomoImg.addEventListener('click', function (evt) {
                    self.visitDestination(event, evt.target);
                }, false);
            }

            self.style();
            setTimeout(function () {
                self.animateOut(notificationDiv);
            }, this.notificationDisplayTime + 1);

        },
        setHolderClasses: function (el, classes) {
            var self = this;

            var roundClass = '';
            if(self.round) {
                roundClass = 'rounded';
            }
            if(self.isMobileDevice()) {
                el.setAttribute("class", classes + " mobile " + "position_" + self.mobilePlacement + " " + roundClass);
            } else {
                el.setAttribute("class", classes + " desktop " + "position_" + self.positionOnPageClass + " " + roundClass );
            }
        },
        animateIn: function (el) {
            var self = this;
            this.setHolderClasses(el, 'fadeInUp');

            //console.log('show');

            this.post(this.actionId, 'viewed');

            //el.setAttribute("class", "desktop position_6 rounded fadeInUp");
            if(self.time > 0 && self.repeat && this.ifCanShow()) {
                setTimeout(function () {
                    self.animateOut(el);
                    // el.parentNode.removeChild(el);
                }, self.time);
            }
        },
        animateOut: function (el) {
            var self = this;
            this.setHolderClasses(el, 'fadeOut');

            // el.setAttribute("class", "desktop position_6 rounded fadeOut");
            // setTimeout(function () {
            //     // el.parentNode.removeChild(el);
            //     //el.style.display = 'none';
            // }, 1000);

            if(self.time > 0 && self.repeat && this.ifCanShow()) {
                setTimeout(function () {
                    self.animateIn(el);
                    // el.parentNode.removeChild(el);
                }, self.time);
            }

        },
        style: function () {
            //смена стилей, если что
        },
        visitDestination: function (url, target) {//переход по ссылочке
            if (url) {
                if (this.newTab) {
                    window.open(url);
                } else {
                    window.location = url;
                }
            }
        },
        pause: function () {
            clearInterval(this.animationTimer);
        },
        ready: true,
        storage: function (key, value) { //запись данных в localStorage
            if (typeof (value) === "undefined") {
                // read
                if (this.localStorageAvailable) {
                    return localStorage.getItem(key);
                } else {
                    return this.localStorageJS[key];
                }
            } else {
                // запишем
                if (this.localStorageAvailable) {
                    localStorage.setItem(key, value);
                } else {
                    this.localStorageJS[key] = value;
                }
            }
        },
        post: function (eventId, type) {
            var http = new XMLHttpRequest();
            var url = this.socproofDomain + "/api/popups/" + type;
            var params = "event_id=" + eventId;
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function () {}
            http.send(params);
        }
    };
}

//дефолтные настройки

socproof.availableLanguages = ["en", "ru"];
var clientHash = document.getElementById('project_x_script').getAttribute('hash');
if (!clientHash) {
    clientHash = document.querySelectorAll('meta[name=hash]')[0].getAttribute('content');
}

var scriptSrc = document.getElementById('project_x_script').getAttribute('src');
var socproofDomain = scriptSrc.substring(0, scriptSrc.indexOf('/js/'));
socproof.socproofDomain = socproofDomain;

var socbean;
var xhr = new XMLHttpRequest();
var param = "?hash=" + clientHash;
var geoResponse = {};
xhr.open("GET", socproofDomain + "/api/popups/get" + param, true);
xhr.onload = function () {



    if (xhr.response != '') {
        var answer = JSON.parse(xhr.response);

        if (!answer.error) {
            socbean = answer.event;
            //socproof.language = socbean.language;
            //socproof.themeCss = socproofDomain + '/' + socbean.themeCss;
            // TODO: Just for testing
            //socproof.themeCss = socproofDomain + '/css/layout_1.css';
            socproof.themeCss = socbean.template.css_path;
            socproof.backgroundColor = socbean.background_color;
            socproof.allowClose = socbean.close;
            socproof.delayBetweenNotification = socbean.delay * 1000;
            socproof.positionOnPageClass = socbean.desktop_placement;
            socproof.clientHash = socbean.hash;
            socproof.image = socbean.image;
            socproof.message = socbean.message;
            socproof.messageColor = socbean.message_color;
            socproof.mobilePlacement = socbean.mobile_placement;
            socproof.newTab = socbean.new_tab;
            socproof.repeat = socbean.repeat;
            socproof.round = socbean.round;
            socproof.time = socbean.time * 1000;
            socproof.title = socbean.title;
            socproof.titleColor = socbean.title_color;
            socproof.url = socbean.url;
            socproof.clientId = socbean.user_id;
            socproof.html = answer.html;
            socproof.actionId = socbean.id;


            // socproof.repeat = socbean.repeat;
            // socproof.hideInMobileVersion = socbean.hideInMobileVersion;
            //
            // socproof.randomDelayBetween = socbean.randomDelayBetween;
            // socproof.openInNewTab = socbean.openInNewTab;
            // socproof.initialDelay = socbean.initialDelay * 1000;
            // socproof.maxNotificationCount = socbean.maxNotificationCount;
            // socproof.notificationDisplayTime = socbean.notificationDisplayTime * 1000;
            // socproof.delayBetweenNotification = socbean.delayBetweenNotification * 1000;
            //
            // socproof.poweredByEnabled = socbean.poweredByEnabled;
            //
            // socproof.clientId = socbean.clientId;
            // socproof.events = socbean.events;

            setTimeout(function () {
                socproof.initiate();
            }, socproof.delayBetweenNotification);
        } else {
            console.error('Error getting Popup Info. Please check your hash.');
        }
    }
}
xhr.send(null);