jQuery.event.special.touchstart={setup:function(e,t,a){this.addEventListener("touchstart",a,{passive:!t.includes("noPreventDefault")})}};
;(function($) {

    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        a256 = '',
        r64 = [256],
        r256 = [256],
        i = 0;

    var UTF8 = {

        /**
         * Encode multi-byte Unicode string into utf-8 multiple single-byte characters
         * (BMP / basic multilingual plane only)
         *
         * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
         *
         * @param {String} strUni Unicode string to be encoded as UTF-8
         * @returns {String} encoded string
         */
        encode: function(strUni) {
            // use regular expressions & String.replace callback function for better efficiency
            // than procedural approaches
            var strUtf = strUni.replace(/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
            })
            .replace(/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
            });
            return strUtf;
        },

        /**
         * Decode utf-8 encoded string back into multi-byte Unicode characters
         *
         * @param {String} strUtf UTF-8 string to be decoded back to Unicode
         * @returns {String} decoded string
         */
        decode: function(strUtf) {
            // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
            var strUni = strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
            function(c) { // (note parentheses for precence)
                var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
                return String.fromCharCode(cc);
            })
            .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
            function(c) { // (note parentheses for precence)
                var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
                return String.fromCharCode(cc);
            });
            return strUni;
        }
    };

    while(i < 256) {
      var c = String.fromCharCode(i);
      a256 += c;
      r256[i] = i;
      r64[i] = b64.indexOf(c);
      ++i;
    }

    function code(s, discard, alpha, beta, w1, w2) {
        s = String(s);
        var buffer = 0,
            i = 0,
            length = s.length,
            result = '',
            bitsInBuffer = 0;

        while(i < length) {
            var c = s.charCodeAt(i);
            c = c < 256 ? alpha[c] : -1;

            buffer = (buffer << w1) + c;
            bitsInBuffer += w1;

            while(bitsInBuffer >= w2) {
                bitsInBuffer -= w2;
                var tmp = buffer >> bitsInBuffer;
                result += beta.charAt(tmp);
                buffer ^= tmp << bitsInBuffer;
            }
            ++i;
        }
        if(!discard && bitsInBuffer > 0) result += beta.charAt(buffer << (w2 - bitsInBuffer));
        return result;
    }

    var Plugin = $.base64 = function(dir, input, encode) {
            return input ? Plugin[dir](input, encode) : dir ? null : this;
        };

    Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
        plain = Plugin.raw === false || Plugin.utf8encode || utf8encode ? UTF8.encode(plain) : plain;
        plain = code(plain, false, r256, b64, 8, 6);
        return plain + '===='.slice((plain.length % 4) || 4);
    };

    Plugin.atob = Plugin.decode = function(coded, utf8decode) {
        coded = coded.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        coded = String(coded).split('=');
        var i = coded.length;
        do {--i;
            coded[i] = code(coded[i], true, r64, a256, 6, 8);
        } while (i > 0);
        coded = coded.join('');
        return Plugin.raw === false || Plugin.utf8decode || utf8decode ? UTF8.decode(coded) : coded;
    };
}(jQuery));
$.base64.utf8encode = true;

// # Cookies Integration
  (function (factory) {
      if (typeof define === 'function' && define.amd) {
          // AMD (Register as an anonymous module)
          define(['jquery'], factory);
      } else if (typeof exports === 'object') {
          // Node/CommonJS
          module.exports = factory(require('jquery'));
      } else {
          // Browser globals
          factory(jQuery);
      }
    }(function ($) {
        var pluses = /\+/g;
        function encode(s) {
            return config.raw ? s : encodeURIComponent(s);
        }
        function decode(s) {
            return config.raw ? s : decodeURIComponent(s);
        }
        function stringifyCookieValue(value) {
            return encode(config.json ? JSON.stringify(value) : String(value));
        }
        function parseCookieValue(s) {
            if (s.indexOf('"') === 0) {
                // This is a quoted cookie as according to RFC2068, unescape...
                s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
            }
            try {
                // Replace server-side written pluses with spaces.
                // If we can't decode the cookie, ignore it, it's unusable.
                // If we can't parse the cookie, ignore it, it's unusable.
                s = decodeURIComponent(s.replace(pluses, ' '));
                return config.json ? JSON.parse(s) : s;
            } catch(e) {}
        }
        function read(s, converter) {
            var value = config.raw ? s : parseCookieValue(s);
            return $.isFunction(converter) ? converter(value) : value;
        }
        var config = $.cookie = function (key, value, options) {
            // Write
            if (arguments.length > 1 && !$.isFunction(value)) {
                options = $.extend({}, config.defaults, options);
                if (typeof options.expires === 'number') {
                    var days = options.expires, t = options.expires = new Date();
                    t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
                }
                return (document.cookie = [
                    encode(key), '=', stringifyCookieValue(value),
                    options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    options.path    ? '; path=' + options.path : '',
                    options.domain  ? '; domain=' + options.domain : '',
                    options.secure  ? '; secure' : ''
                ].join(''));
            }
            // Read
            var result = key ? undefined : {},
                // To prevent the for loop in the first place assign an empty array
                // in case there are no cookies at all. Also prevents odd result when
                // calling $.cookie().
                cookies = document.cookie ? document.cookie.split('; ') : [],
                i = 0,
                l = cookies.length;
            for (; i < l; i++) {
                var parts = cookies[i].split('='),
                    name = decode(parts.shift()),
                    cookie = parts.join('=');
                if (key === name) {
                    // If second argument (value) is a function it's a converter...
                    result = read(cookie, value);
                    break;
                }
                // Prevent storing a cookie that we couldn't decode.
                if (!key && (cookie = read(cookie)) !== undefined) {
                    result[name] = cookie;
                }
            }
            return result;
        };
        config.defaults = {};
        $.removeCookie = function (key, options) {
            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return !$.cookie(key);
        };
    }));

// # SHARING EDITS.
  // https://ellisonleao.github.io/sharer.js
  // # data-title - sharer text
  // # data-url - url to be shared
  // # data-width - popup width
  // # data-height - popup height
  // # data-link - share element will work as a link
  // # data-blank (requires data-link combined) - share element will work as a link in a new tab
  (function(m, r) {
    "use strict";
    var s = function(t) {
      this.elem = t
    };
    s.init = function() {
      var t = r.querySelectorAll("[data-sharer]"),
        e, a = t.length;
      for (e = 0; e < a; e++) {
        t[e].addEventListener("click", s.add)
      }
    };
    s.add = function(t) {
      var e = t.currentTarget || t.srcElement;
      var a = new s(e);
      a.share()
    };
    s.prototype = {
      constructor: s,
      getValue: function(t) {
        var e = this.elem.getAttribute("data-" + t);
        if (e && t === "hashtag") {
          if (!e.startsWith("#")) {
            e = "#" + e
          }
        }
        return e === null ? "" : e
      },
      share: function() {
        var t = this.getValue("sharer").toLowerCase(),
          e = {
            facebook: {
              shareUrl: "https://www.facebook.com/sharer/sharer.php",
              params: {
                u: this.getValue("url"),
                hashtag: this.getValue("hashtag"),
                quote: this.getValue("quote")
              }
            },
            linkedin: {
              shareUrl: "https://www.linkedin.com/shareArticle",
              params: {
                url: this.getValue("url"),
                mini: true
              }
            },
            twitter: {
              shareUrl: "https://twitter.com/intent/tweet/",
              params: {
                text: this.getValue("title"),
                url: this.getValue("url"),
                hashtags: this.getValue("hashtags"),
                via: this.getValue("via")
              }
            },
            email: {
              shareUrl: "mailto:" + this.getValue("to"),
              params: {
                subject: this.getValue("subject"),
                body: this.getValue("title") + "\n" + this.getValue("url")
              }
            },
            whatsapp: {
              shareUrl: this.getValue("web") === "true" ? "https://web.whatsapp.com/send" : "https://wa.me/",
              params: {
                phone: this.getValue("to"),
                text: this.getValue("title") + " " + this.getValue("url")
              }
            },
            telegram: {
              shareUrl: "https://t.me/share",
              params: {
                text: this.getValue("title"),
                url: this.getValue("url")
              }
            },
            viber: {
              shareUrl: "viber://forward",
              params: {
                text: this.getValue("title") + " " + this.getValue("url")
              }
            },
            line: {
              shareUrl: "http://line.me/R/msg/text/?" + encodeURIComponent(this.getValue("title") + " " + this.getValue("url"))
            },
            pinterest: {
              shareUrl: "https://www.pinterest.com/pin/create/button/",
              params: {
                url: this.getValue("url"),
                media: this.getValue("image"),
                description: this.getValue("description")
              }
            },
            tumblr: {
              shareUrl: "http://tumblr.com/widgets/share/tool",
              params: {
                canonicalUrl: this.getValue("url"),
                content: this.getValue("url"),
                posttype: "link",
                title: this.getValue("title"),
                caption: this.getValue("caption"),
                tags: this.getValue("tags")
              }
            },
            hackernews: {
              shareUrl: "https://news.ycombinator.com/submitlink",
              params: {
                u: this.getValue("url"),
                t: this.getValue("title")
              }
            },
            reddit: {
              shareUrl: "https://www.reddit.com/submit",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            vk: {
              shareUrl: "http://vk.com/share.php",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                description: this.getValue("caption"),
                image: this.getValue("image")
              }
            },
            xing: {
              shareUrl: "https://www.xing.com/social/share/spi",
              params: {
                url: this.getValue("url")
              }
            },
            buffer: {
              shareUrl: "https://buffer.com/add",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                via: this.getValue("via"),
                picture: this.getValue("picture")
              }
            },
            instapaper: {
              shareUrl: "http://www.instapaper.com/edit",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                description: this.getValue("description")
              }
            },
            pocket: {
              shareUrl: "https://getpocket.com/save",
              params: {
                url: this.getValue("url")
              }
            },
            mashable: {
              shareUrl: "https://mashable.com/submit",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            mix: {
              shareUrl: "https://mix.com/add",
              params: {
                url: this.getValue("url")
              }
            },
            flipboard: {
              shareUrl: "https://share.flipboard.com/bookmarklet/popout",
              params: {
                v: 2,
                title: this.getValue("title"),
                url: this.getValue("url"),
                t: Date.now()
              }
            },
            weibo: {
              shareUrl: "http://service.weibo.com/share/share.php",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                pic: this.getValue("image"),
                appkey: this.getValue("appkey"),
                ralateUid: this.getValue("ralateuid"),
                language: "zh_cn"
              }
            },
            blogger: {
              shareUrl: "https://www.blogger.com/blog-this.g",
              params: {
                u: this.getValue("url"),
                n: this.getValue("title"),
                t: this.getValue("description")
              }
            },
            baidu: {
              shareUrl: "http://cang.baidu.com/do/add",
              params: {
                it: this.getValue("title"),
                iu: this.getValue("url")
              }
            },
            douban: {
              shareUrl: "https://www.douban.com/share/service",
              params: {
                name: this.getValue("name"),
                href: this.getValue("url"),
                image: this.getValue("image"),
                comment: this.getValue("description")
              }
            },
            okru: {
              shareUrl: "https://connect.ok.ru/dk",
              params: {
                "st.cmd": "WidgetSharePreview",
                "st.shareUrl": this.getValue("url"),
                title: this.getValue("title")
              }
            },
            mailru: {
              shareUrl: "http://connect.mail.ru/share",
              params: {
                share_url: this.getValue("url"),
                linkname: this.getValue("title"),
                linknote: this.getValue("description"),
                type: "page"
              }
            },
            evernote: {
              shareUrl: "https://www.evernote.com/clip.action",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            skype: {
              shareUrl: "https://web.skype.com/share",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            delicious: {
              shareUrl: "https://del.icio.us/post",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            sms: {
              shareUrl: "sms://",
              params: {
                body: this.getValue("body")
              }
            },
            trello: {
              shareUrl: "https://trello.com/add-card",
              params: {
                url: this.getValue("url"),
                name: this.getValue("title"),
                desc: this.getValue("description"),
                mode: "popup"
              }
            },
            messenger: {
              shareUrl: "fb-messenger://share",
              params: {
                link: this.getValue("url")
              }
            },
            odnoklassniki: {
              shareUrl: "https://connect.ok.ru/dk",
              params: {
                st: {
                  cmd: "WidgetSharePreview",
                  deprecated: 1,
                  shareUrl: this.getValue("url")
                }
              }
            },
            meneame: {
              shareUrl: "https://www.meneame.net/submit",
              params: {
                url: this.getValue("url")
              }
            },
            diaspora: {
              shareUrl: "https://share.diasporafoundation.org",
              params: {
                title: this.getValue("title"),
                url: this.getValue("url")
              }
            },
            googlebookmarks: {
              shareUrl: "https://www.google.com/bookmarks/mark",
              params: {
                op: "edit",
                bkmk: this.getValue("url"),
                title: this.getValue("title")
              }
            },
            qzone: {
              shareUrl: "https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey",
              params: {
                url: this.getValue("url")
              }
            },
            refind: {
              shareUrl: "https://refind.com",
              params: {
                url: this.getValue("url")
              }
            },
            surfingbird: {
              shareUrl: "https://surfingbird.ru/share",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                description: this.getValue("description")
              }
            },
            yahoomail: {
              shareUrl: "http://compose.mail.yahoo.com",
              params: {
                to: this.getValue("to"),
                subject: this.getValue("subject"),
                body: this.getValue("body")
              }
            },
            wordpress: {
              shareUrl: "https://wordpress.com/wp-admin/press-this.php",
              params: {
                u: this.getValue("url"),
                t: this.getValue("title"),
                s: this.getValue("title")
              }
            },
            amazon: {
              shareUrl: "https://www.amazon.com/gp/wishlist/static-add",
              params: {
                u: this.getValue("url"),
                t: this.getValue("title")
              }
            },
            pinboard: {
              shareUrl: "https://pinboard.in/add",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                description: this.getValue("description")
              }
            },
            threema: {
              shareUrl: "threema://compose",
              params: {
                text: this.getValue("text"),
                id: this.getValue("id")
              }
            },
            kakaostory: {
              shareUrl: "https://story.kakao.com/share",
              params: {
                url: this.getValue("url")
              }
            },
            yummly: {
              shareUrl: "http://www.yummly.com/urb/verify",
              params: {
                url: this.getValue("url"),
                title: this.getValue("title"),
                yumtype: "button"
              }
            }
          },
          a = e[t];
        if (a) {
          a.width = this.getValue("width");
          a.height = this.getValue("height")
        }
        return a !== undefined ? this.urlSharer(a) : false
      },
      urlSharer: function(t) {
        var e = t.params || {},
        a = Object.keys(e),
        r, 
        s = a.length > 0 ? "?" : "";
        
        for (r = 0; r < a.length; r++) {
          if (s !== "?") {
            s += "&"
          }
          if (e[a[r]]) {
            s += a[r] + "=" + encodeURIComponent(e[a[r]])
          }
        }
        t.shareUrl += s;
        var l = this.getValue("link") === "true";
        var i = this.getValue("blank") === "true";
        if (l) {
          if (i) {
            m.open(t.shareUrl, "_blank")
          } else {
            m.location.href = t.shareUrl
          }
        } else {
          console.log(t.shareUrl);
          var h = t.width || 600,
            u = t.height || 480,
            o = m.innerWidth / 2 - h / 2 + m.screenX,
            p = m.innerHeight / 2 - u / 2 + m.screenY,
            g = "scrollbars=no, width=" + h + ", height=" + u + ", top=" + p + ", left=" + o,
            n = m.open(t.shareUrl, "", g);
          if (m.focus) {
            n.focus()
          }
        }
      }
    };
    if (r.readyState === "complete" || r.readyState !== "loading") {
      s.init()
    } else {
      r.addEventListener("DOMContentLoaded", s.init)
    }
    m.Sharer = s
  })(window, document);
  $('body').on("click",'[data-sharer] a',function(e) {e.preventDefault();});

// ## UniqID ## //
  var charstoformid = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
  var UniqID = function() {
    var idlength = 10;
      var uniqid = '';
      for (var i = 0; i < idlength; i++) {
        uniqid += charstoformid[Math.floor(Math.random() * charstoformid.length)];
      }
      return uniqid;
  }

// # THEME CONTEXT .  


// # THEME CONSTRACT FUNCTIONS
  function isEmpty(value){
    return !$.trim(value);
  }

  function stripslashes (str) {
    return (str + '').replace(/\\(.?)/g, function (s,n1) {
      switch (n1) {
        case '\\':
          return '\\';
        case '0':
          return '\u0000';
        case '':
          return '';
        default:
          return n1;
      }
    });
  }

  function strip_tags(str, allow){
    allow = (((allow || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return str.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
      return allow.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 :'';
    });
  }

// # Ajax Handler
    var AjaxHandlerXHR = false;
    var AjaxHandlerLastData = false;
    var RetryInterval;
    function AjaxRequest(data) {
        if( AjaxHandlerXHR != false && AjaxHandlerLastData.url == data.url ) {
            AjaxHandlerXHR.abort();
        }
        data.error = function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status == 404) {
                msg = 'لم يتم العثور على الصفحة.';
                AjaxHandlerXHR = false;
            } else if (jqXHR.status == 500) {
                msg = 'حدث خطأ اثناء معاينة الملف.';
                AjaxHandlerXHR = false;
            } else if (exception === 'timeout') {
                msg = 'إنتهت مهلة الإتصال.';
                AjaxHandlerXHR = false;
            }
            if( msg != '' && msg != undefined ) {
                RetryInterval = setInterval(AjaxRequest(data), 5000);
            }
        }
        AjaxHandlerLastData = data;
        AjaxHandlerXHR = $.ajax(data).done(function(){
            clearInterval(RetryInterval);
            AjaxHandlerXHR = false;
            CustomInitialize();
        });
        return true;
    }

// # ChangeURL && ChangeTitle
  function ChangeURL(url) {
    url = url.replace('?ajax', '');
    if(url!=window.location){
      window.history.pushState({path:url},'',url);
    }
  }

  function ChangeTitle(title) {
    $(document).prop("title", title);
  }
  function __loc(e) {
    return e
  }

// # outerHTML
  function outerHTML(node){
    return node.outerHTML || new XMLSerializer().serializeToString(node);
  }

// # HideElementTimeOut
  var TimerOutValues;
  function HideElementTimeOut(elem,time=500) {
    clearTimeout(TimerOutValues);
    TimerOutValues = setTimeout(function(){
      $(elem).hide();
    },time);        
  }

// # copyToClipboard
  function copyToClipboard(text) {

     var textArea = document.createElement( "textarea" );
     textArea.value = text;
     document.body.appendChild( textArea );       
     textArea.select();

     try {
        var successful = document.execCommand( 'copy' );
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
     } catch (err) {
        console.log('Oops, unable to copy',err);
     }    
     document.body.removeChild( textArea );
  }

// # GO TO TOP 
  $('body').on('click', '.GotoTop,.GoPeTop', function(){
    $('body, html').animate({"scrollTop": 0}, 200);
  });  
  $(window).scroll(function(){
    if( $(window).scrollTop() > 50 ) {
      $('.GotoTop').addClass('visible');
    }
    if( $(window).scrollTop() < 50 ) {
      $('.GotoTop').removeClass('visible');
    }
  });

// # TOOLTIP .
  var UserOnpage;
  $(document).mouseleave(function () {
    UserOnpage = false;
    $('body > title--tooltip').remove();
  });

  $(document).mouseenter(function () {
    UserOnpage = true;
  });

  var TooltipsTimeout;
  if( ISMobile == false ) {

    $('body').on("mouseover", function(e){
      var dropdown = $("[data-tooltip]");
      if (!dropdown.is(e.target) && dropdown.has(e.target).length === 0 || dropdown.hasClass('open')) {
        $('title--tooltip').remove();
      }
    });

    $('body').on("mouseover",'[data-tooltip]', function(e){
      var event = e;
      if($(this).data('off-tooltip') == undefined){
        var string = $(this).data('tooltip');
        if( $(this).data('tooltip-base') != undefined) string = $.base64.atob(string,true);

        var Tip__Uniq = UniqID();
        if( $(this).data('uniq-id') != undefined ){
          var Tip__Uniq = $(this).data('uniq-id');
        }

        var TooltipElement = $('body > title--tooltip');

        // # POSTIONS CALC
        var leftorig = $(this).offset().left;
        var calcul = $(this).width() / 2;

        // # CUSTOM CLASS .
        if($(this).data('custom-class') != undefined){
          var ScatsClass = '';
        }else{
          var ScatsClass = '';
        }

        // # Execute
        if( TooltipElement.length == 0 ) {
          $('body').append('<title--tooltip data-tip-uniq="'+Tip__Uniq+'" '+( ( $(this).data('custom-class') != undefined ) ? ' class="'+$(this).data('custom-class')+'"' : '' )+'></title--tooltip>');
          TooltipElement = $('body > title--tooltip');
        }

        TooltipElement.html(string);
        // # Position
        LeftPosition = (leftorig + calcul - ((TooltipElement.width() + 32) / 2));
        if( LeftPosition < 0 ) {
          LeftPosition = 16;
        }
        TopPosition = ($(this).offset().top + $(this).height() + 4) - $(window).scrollTop();
        if( $(this).data('position') == 'top' ) {
          TopPosition = ($(this).offset().top - TooltipElement.height() - 4) - $(window).scrollTop() - 10;
        }
        TooltipElement.css("left", LeftPosition+'px');
        TooltipElement.css("top", TopPosition);

        clearTimeout(TooltipsTimeout);
        TooltipsTimeout = setTimeout(function(){
          if( UserOnpage == true ) {
            $('body > title--tooltip').fadeIn(150);
          }
        },300);
      }
    });
  }


 // # COUNT LINES
  function countLines(el,lineHeight=30) {
    var divHeight = ( ( $.isNumeric( el ) ) ) ? el : el.outerHeight();
    var lineHeight = parseInt(lineHeight);
    var lines = divHeight / lineHeight;
    return Math.round(lines);
  }

// # INITIALIZING STAFF. 
var CaroseL__Events = [];

function InitializeOwlCarousel() {
     if( $('.YourColor-Intro--sliderArea').length > 0){
      $('.YourColor-Intro--sliderArea').each(function(m,slidelem){
        var S__Uniq = $(slidelem).data('uniq');
        CaroseL__Events[ S__Uniq ] = $(slidelem).owlCarousel({              
            loop: false,
            rtl: true,
            center: false,
            nav: true,
            dots: true,
            items: 1,
            margin:0,
            smartSpeed: 800,
            animateOut: 'fadeOut',
            autoplay:false,
            responsiveClass:true,
            autoplayTimeout:6000,
            onInitialized:NavsCarouselEdits,
        }).css("opacity", '1');

        CaroseL__Events[ S__Uniq ].on('changed.owl.carousel', function(property) {
          NavsCarouselEdits(property);          
        });

      });
    }
    $(".-YC-Category-carusel-setup").length > 0 && $(".-YC-Category-carusel-setup").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !0,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 25,
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onChanged: function e(t) {
                var a = t.item.index,
                    i = t.item.count;
                $(".owl-item").removeClass("prev-active"), $(".owl-item").eq(a).addClass("active"), $(".owl-item").eq(0 === a ? i - 1 : a - 1).addClass("prev-active")
            },
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".featured-setup-owl").length > 0 && $(".featured-setup-owl").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !ISMobile,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 25,
            navText: ['<i class="fa-solid fa-arrow-left"></i>', '<i class="fa-solid fa-arrow-right"></i>'],
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".-YC-city-Grid-Area-v2").length > 0 && $(".-YC-city-Grid-Area-v2").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !ISMobile,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 30,
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".-owl-PriceLists-Center-v1").length > 0 && $(".-owl-PriceLists-Center-v1").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !ISMobile,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 30,
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".--setup-works--slider--show").length > 0 && $(".--setup-works--slider--show").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !ISMobile,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 30,
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".city_owl").length > 0 && $(".city_owl").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            loop: !ISMobile,
            rtl: !0,
            center: !1,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 30,
            smartSpeed: 800,
            autoplay: !1,
            responsiveClass: !0,
            autoplayTimeout: 6e3,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    }), $(".-itemslist-share-icons-list").length > 0 && 0 == $(".-itemslist-share-icons-list.owl-loaded").length && $(".-itemslist-share-icons-list").each(function(e, t) {
        var a = $(t).data("uniq");
        CaroseL__Events[a] = $(t).owlCarousel({
            margin: 15,
            rtl: !ISMobile,
            loop: !1,
            center: !1,
            nav: !0,
            dots: !0,
            autoplayTimeout: 8e3,
            autoplayHoverPause: !0,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !1,
            responsiveClass: !0,
            autoWidth: !0,
            onInitialized: NavsCarouselEdits
        }).css("opacity", "1"), CaroseL__Events[a].on("changed.owl.carousel", function(e) {
            NavsCarouselEdits(e)
        })
    })
}

function NavsCarouselEdits(e) {
    e.item.index;
    var t = $(e.target).data("uniq");
    $(e.target).find(".owl-prev").hasClass("disabled") ? $('.-custom-owl-Slides-prev[data-owlnavs-change="' + t + '"]').css({
        "pointer-events": "none",
        opacity: "0.6"
    }).addClass("disabled-btn") : $('.-custom-owl-Slides-prev[data-owlnavs-change="' + t + '"]').css({
        "pointer-events": "auto",
        opacity: "1"
    }).removeClass("disabled-btn"), $(e.target).find(".owl-next").hasClass("disabled") ? $('.-custom-owl-Slides-next[data-owlnavs-change="' + t + '"]').css({
        "pointer-events": "none",
        opacity: "0.6"
    }).addClass("disabled-btn") : $('.-custom-owl-Slides-next[data-owlnavs-change="' + t + '"]').css({
        "pointer-events": "auto",
        opacity: "1"
    }).removeClass("disabled-btn")
}

function ReferencesInitialize() {
    if ($(".-references-post").length > 0) {
        var e = $(".-references-post > h2").outerHeight(),
            t = $(".-references-post > ul"),
            a = .2;
        t.find("li").each(function(e, t) {
            null == $(t).data("loaded") && ($(t).attr("data-loaded", !1), a += .1, $(t).attr("style", "--trans-delay:" + a + "s"))
        }), setTimeout(function() {
            var a = t.outerHeight();
            $(".-references-post").attr("style", "--tableofcontentH:" + (e + a + 50) + "px;")
        }, 100)
    }
}
function RootVars() {
    $("[data-roots-loded]").each(function(e, t) {
        var a = $(t).data("roots-loded");
        a = atob(a), a = jQuery.parseJSON(a), $(t).closest(".-YourColor-SingleWidget-Section").attr("style", a)
    })
}
$("body").on("click", "[data-owlnavs-change]", function() {
    var e = $(this).data("owlnavs-change");
    null != e && (e = $(this).data("owlnavs-change")), "next" == $(this).data("type") ? CaroseL__Events[e].trigger("next.owl.carousel") : CaroseL__Events[e].trigger("prev.owl.carousel", [300])
});
var ProgressLoaded = function() {
    setTimeout(function() {
        $("[data-progressload]").each(function(e, t) {
            if (null == $(t).data("loded-item")) {
                var a = $(t).offset().top - $(window).height() - 100;
                $(window).scrollTop() >= a && ($(t).css({
                    width: $(t).data("progressload") + "%"
                }), $(t).addClass("progressload-shows-in"), $(t).data("loded-item", !0))
            }
        })
    }, 300)
};

// # INITIALIZE WIDGETS ROOT VARS,
    function RootVars() {
      $('[data-roots-loded]').each(function(e,elem) {
        var RootsColors = $(elem).data('roots-loded');
        RootsColors = atob( RootsColors );
        RootsColors = jQuery.parseJSON( RootsColors );
        $(elem).closest('.-YourColor-SingleWidget-Section').attr("style",RootsColors);
      });
    }

// # ProgressLoaded
    var ProgressLoaded = function(){
      setTimeout(function(){
        $('[data-progressload]').each(function(hmada, el){
          if( $(el).data('loded-item') == undefined ){
            var n = $(el).offset().top - $(window).height() - 100;
            if( $(window).scrollTop() >= n ){
              $(el).css({"width":$(el).data('progressload')+'%'});
              $(el).addClass('progressload-shows-in');
              $(el).data("loded-item",true);
            }
          }
        });   
      },300);
    }

//# Counter Event.
    function CounterUP() {
        $("[counterup]").each(function(index, el){
            if( $(el).data('loded-item') == undefined ){
              var n = $(el).offset().top - $(window).height() - 100;
              if( $(window).scrollTop() >= n ){
                  $({ Counter: 0 }).animate({
                    Counter: $(el).text()
                  },{
                    duration: 3000,
                    easing: 'swing',
                    step: function() {
                        if($(el).data('round') != undefined){
                          var number = parseInt('' + (this.Counter * 100)) / 100;
                          $(el).text(Math.ceil(this.Counter));
                        }else{
                          $(el).text(Math.ceil(this.Counter));
                        }
                    }
                  });
                  $(el).data("loded-item",true);
              }
          }
        });
      }    

// # LazyloaderHook
    function LazyloaderHook() {
        $("[data-loader-src]").each(function(els, el){
            $(el).attr("src", $(el).attr("data-loader-src")).removeAttr("data-loader-src");
        });
        $("[data-loader-srcset]").each(function(els, el){
            $(el).attr("srcset", $(el).attr("data-loader-srcset")).removeAttr("data-loader-srcset");
        });
        $("[data-loader-style]").each(function(els, el){
            $(el).attr("style", $(el).attr("data-loader-style")).removeAttr("data-loader-style");
        });
        $("[data-loader-href]").each(function(els, el){
            $(el).attr("href", $(el).attr("data-loader-href")).removeAttr("data-loader-href");
        });
        $("[data-loader-style], [data-loader-src], [data-loader-srcset], [data-loader-href]").fadeIn(0);
    }

    if( IsSpeed == false ){
        LazyloaderHook();
    }

// # SVG__Loader
    var LoadedSVG = false;
    function SVG__Loader() {   
        if( LoadedSVG == false ){
            $("[data-svg-loaders]").each(function (e, t) {
                var n = $(t).offset().top - $(window).height() - 400;
                //alert( 'n ::'+ n);
                //alert( 'SCROLL TOP ::' +$(window).scrollTop() );
                if( $(window).scrollTop() >= n ){LoadedSVG = true;
                    var SVG__Src = $(t).data( 'svg-loaders' );
                    // # 
                    //var SVG__Scrape = HomeURL+'/SvgCenter/'+SVG__Src;

                    if( SVG_List[ SVG__Src ] != undefined ){
                        setTimeout(function() {
                            $(t).append(SVG_List[ SVG__Src ]);
                        },1000);
                        $(t).removeAttr('data-svg-loaders');
                        LoadedSVG = false;
                    }else{
                        alert(SVG_List);
                    }
                }
            });
        }
    }

// # CSS LOADER .
    function CSS__Loader(cssli) {   
        Css_List = cssli;
        $.each( Css_List ,function(k,csfile) {
            if( $('[data-style-ajax="'+k+'"]').length === 0 ){
                $('body').append('<link rel="stylesheet" data-style-ajax="'+k+'" type="text/css" href="'+csfile+'" />');
            }
        });
    }

// # WOW LOADING.
    function WowAjaxify__Loaded(elem) {
        $(elem).find("[data-animation-id]").each(function (e, t) {
            if( $(t).data('loaded-animation') == undefined ){
                Wow__Item_show(t);
            }
        });
    }
    function Wow__Item_show(elem) {
        if( $(elem).data('loaded-animation') == undefined ){
            var StyleAttr = $(elem).attr('style');
            StyleAttr = ( ( StyleAttr == undefined ) ) ? '' : StyleAttr+';';
            var Animation_Name = $(elem).data('animation-id');

            var Animation_Duration = $(elem).data('animation-duration');
            if( Animation_Duration == undefined ) Animation_Duration = '1.2s';
            
            var Animation_Delay = $(elem).data('animation-delay');      

            $(elem).addClass('YC-Animation-Item');
            $(elem).data('loaded-animation','true');
            StyleAttr += '--animation-name:'+Animation_Name+';--animation-duration:'+Animation_Duration+';';
            if( Animation_Delay != undefined ){
                StyleAttr += '--animation-delay:'+Animation_Delay+';';
            }
            $(elem).attr("style",StyleAttr);
            ser = 800;
            if( Animation_Delay != undefined ){
                var ser = Animation_Delay.split('s')[0];
                ser = ser * 1000;
                ser = ser + 500;
            }
            setTimeout(function() {
                $(elem).removeClass("animation-hidden");
            },ser);

            Animation_Duration = Animation_Duration + 100;
            setTimeout(function() {

            },Animation_Duration);
        }
    }
    function YC_WowLoader() {
        if( $("[data-animation-id]").length > 0 ){
            $("[data-animation-id]").each(function (e, t) {
                if( $(t).data('loaded-animation') == undefined ){
                    var StyleAttr = $(t).attr('style');
                    StyleAttr = ( ( StyleAttr == undefined ) ) ? '' : StyleAttr+';';
                    var Animation_Name = $(t).data('animation-id');

                    var Animation_Duration = $(t).data('animation-duration');
                    if( Animation_Duration == undefined ) Animation_Duration = '1.2s';
                
                    var Animation_Delay = $(t).data('animation-delay');

                    //$(t).addClass('animation-hidden');
                    var n = $(t).offset().top - $(window).height() - 100;
                    if( $(window).scrollTop() >= n ){
                        Wow__Item_show(t);
                    }
                }
            });
        }
    }

// # MENU EDITS 
function SubMenusIcons() {
    $(".--Site--Menu > ul > li a").addClass("activable");
    $(".--Site--Menu > ul > li > a").addClass("hoverable");
    
    // إزالة الأيقونة السابقة قبل إضافة أيقونة جديدة
    $(".-Show-SubMenu-Icon").remove();
    
    $(".menu-item-has-children > a").append('<div class="-Show-SubMenu-Icon"><i class="fa-solid fa-chevron-down"></i></div>');
    $(".-YourColor_active > a").append('<div class="-Show-SubMenu-Icon"><i class="fa-solid fa-chevron-down"></i></div>');
    $(".-YourColor-Menu-DropDown").each(function(e, t) {
        $(t).closest("li").addClass("-YourColor_active");
    });
}


// # SINGLE POPOVER .
    var SingularPopOver = function() {
        $('[data-scroll-popover]').each(function(k,el) {
            if( $(el).data('is-loaded') == undefined && $(window).scrollTop() >= 800 ) {
                var Arguments = $(el).data('scroll-popover');
                var DecodeArgums = $.base64.atob( Arguments ,true);
                DecodeArgums = jQuery.parseJSON( DecodeArgums );
                var output = '';
                output += '<div class="-order-services--single--popoover">';
            
                    output += '<div class="order-services--overlay" data-button="closse--order-services"></div>';
                    output += '<div class="order-services--body">';

                        output += '<div class="order-services--closse" data-button="closse--order-services"><i class="fa-solid fa-xmark"></i></div>';
                        output += '<div class="order-services--icon">'+( ( DecodeArgums.popover_call_icon != undefined && !isEmpty( DecodeArgums.popover_call_icon ) ) ? DecodeArgums.popover_call_icon : '<i class="fa-solid fa-headset"></i>' )+'</div>';

                        output += '<div class="order-services--info-context">';
                            output += ( ( DecodeArgums.popover_call_title != undefined ) ) ? '<h2>'+DecodeArgums.popover_call_title+'</h2>' : '';
                            output += ( ( DecodeArgums.popover_call_content != undefined ) ) ? '<p>'+DecodeArgums.popover_call_content+'</p>' : '';

                            output += '<div class="popup-boxnumber">';

                                if( DecodeArgums.phonenumber != undefined && window.kayanShowCallButtons ){
                                    output += '<a class="order-services-button order-services-phonenumber -BTN--hoverable" href="tel:'+DecodeArgums.phonenumber+'" rel="nofollow">';
                                        output += '<i class="fa-solid fa-phone"></i>';
                                        output += '<span>اتصل بنا</span>';
                                    output += '</a>';
                                }

                                if( DecodeArgums.whatsapp_number != undefined ){
                                    var waHref = 'https://wa.me/'+DecodeArgums.whatsapp_number;
                                    if( DecodeArgums.whatsapp_message != undefined && DecodeArgums.whatsapp_message != '' ){
                                        waHref += '?text='+encodeURIComponent(DecodeArgums.whatsapp_message);
                                    }
                                    output += '<a target="_blank" rel="noopener" class="order-services-button order-services-whatsapp -BTN--hoverable" href="'+waHref+'">';
                                        output += '<i class="fa-brands fa-whatsapp"></i>';
                                        output += '<span>   الواتساب</span>';
                                    output += '</a>';
                                }
                            output += '</div>';

                        output += '</div>';
                    output += '</div>';

                output += '</div>';
                $('body').append(output);
                $(el).data("is-loaded",'true');
            }
        });
    }
    $(window).on('scroll', SingularPopOver);

    function Closse__OrderSevices( elem ) {
        elem.closest('.-order-services--single--popoover').remove();
    }


// # SCROLL EVENTS && MAIN MENU SCROLLER 
    //if(!ISMobile){
       var prevScrollpos = window.pageYOffset;
        window.onscroll = function() {
            var currentScrollPos = window.pageYOffset;
            var Header__elem = $("header");
            var Body__elem = $("body");
            var Single__post_content = $('.-single-post-content');

            if (prevScrollpos > currentScrollPos) {
                if( Single__post_content.length > 0 ){
                var single_scroll_content = Single__post_content.offset().top + Single__post_content.outerHeight();
                if( single_scroll_content >  currentScrollPos && currentScrollPos > Single__post_content.offset().top ){
                    Header__elem.addClass("hidemenu");
                    Body__elem.addClass('hidemenu');
                }else{
                    Header__elem.removeClass('hidemenu');
                    Body__elem.removeClass('hidemenu');
                }
            }else{
                Header__elem.removeClass('hidemenu');
                Body__elem.removeClass('hidemenu');
            }
            }else{
                Header__elem.addClass("hidemenu");
                Body__elem.addClass('hidemenu');
            }
            prevScrollpos = currentScrollPos;
            CounterUP();
            SVG__Loader();
            YC_WowLoader();
            ProgressLoaded();
        }
    //}

// # ScrollEventes // 
    var FixedStaff = function(){
      var Header__elem = $("header");
      var IntroBoxes = $(".YourColor-IntroBoxes");

      if( $(window).scrollTop() > 150 ){
        Header__elem.removeClass('fixedintro');
      }else{
        Header__elem.addClass('fixedintro');
      }
        
    };
    $(window).on('scroll', FixedStaff);
    $(window).on('load', FixedStaff);
    $(window).ajaxSuccess(FixedStaff);

// # TBLE OF CONTENT ..
    function TabeOfContentOptions() {
      if($('#ez-toc-container').length > 0 ){
        if($('#ez-toc-container > .ez-toc-title-container > toggle-toc').length == 0){
          $('#ez-toc-container > .ez-toc-title-container').append('<toggle-toc class="hoverable activable"><span>عرض العناوين </span><i class="fa-solid fa-arrow-down"></i></toggle-toc>');
        }
        //
        var ULTocList = $('#ez-toc-container nav');
        //
        var TransDelay = 0.2;
          ULTocList.find('li').each(function(t, n) {
          if($(n).data('loaded') == undefined ){
            $(n).attr("data-loaded",false);
            $(n).find('a').addClass('hoverable activable');
            // # TRANSTION UPDATE 
            TransDelay = TransDelay + 0.1;
            $(n).attr("style",'--trans-delay:'+TransDelay+'s');
            // #L#M TEXT NUMBER ..
            var NumberSpan = $(n).find('a > span').text();
            if(NumberSpan < 10) $(n).find('a > span').text('0'+NumberSpan);
          }
        });   
        //
        setTimeout(function(){
          var TitleHeight = $('#ez-toc-container .ez-toc-title-container').outerHeight();
          var MyUlHeight = $('#ez-toc-container nav').outerHeight();
          var FinalHeight = TitleHeight + MyUlHeight + 50;
          $('#ez-toc-container').attr("style",'--tableofcontentH:'+FinalHeight+'px;');
        },800);


      }
    }


// # FIELDS SERCHING CENTER .
    function SearchFields(el,empty=false) {
        searchVal = $(el).val();
        Uniq = $(el).data('input-search-center');
        FieldsCenter = $('.-ScrollerCenter[data-uniqid="'+Uniq+'"]');
        var argums = $(el).data('field-type');
        if( argums == 'Taxonomy-Select' || argums == 'Posts-Select' || argums == 'Users-Select' || argums == 'Select' || argums == 'Select-Icon' || argums == 'CuntrySelect' || argums == 'Phone-Number' ){
            if( empty == true ){
                FieldsCenter.find('li').show();
            }else{
                FieldsCenter.find('li').each(function(e,selFectelem) {
                  elemTitle = $(selectelem).data('title')+'';
                  if( elemTitle.indexOf( searchVal ) >= 0) {
                    $(selectelem).show();
                  }else{
                    $(selectelem).hide();
                  }
                });
            }
        }else if( argums == 'Taxonomy-CheckBox' || argums == 'Posts-CheckBox' || argums == 'Users-CheckBox' ){
            if( empty == true ){
                FieldsCenter.find('.-CheckBox-Box-Item').show();
            }else{
                FieldsCenter.find('.-CheckBox-Box-Item').each(function(e,checkboxitem) {
                    elemTitle = $(checkboxitem).find('em').text()+'';
                    if( elemTitle.indexOf( searchVal ) >= 0) {
                        $(checkboxitem).show();
                    }else{
                        $(checkboxitem).hide();
                    }
                });
            }
        }else if( argums == 'Taxonomy-Radio' || argums == 'Posts-Radio' || argums == 'Users-Radio' ){
            if( empty == true ){
                FieldsCenter.find('.-Radio-Box-Item').show();
            }else{
                FieldsCenter.find('.-Radio-Box-Item').each(function(e,checkboxitem) {
                    elemTitle = $(checkboxitem).find('em').text()+'';
                    if( elemTitle.indexOf( searchVal ) >= 0) {
                        $(checkboxitem).show();
                    }else{
                        $(checkboxitem).hide();
                    }
                });
            }
        }
    }

    $('body').on('keypress keyup keydown', '[data-input-search-center]', function(evt){
        fieldID = $(this).parent().data('type');
        var CurrElement = $(this);

        if(evt.keyCode == 13 ){
            evt.preventDefault();
            SearchFields(CurrElement,false);
            return false;   
        }else{
            setTimeout(function(){
                if(evt.keyCode === 27) {
                    SearchFields(CurrElement,true);
                }else if(CurrElement.val() == '') {
                    SearchFields(CurrElement,true);
                }else {
                    SearchFields(CurrElement,false);
                }
            },100);
        }
    });

// # SELECT EDITS.
    $('body').on("click",'[data-select-open]',function() {
        $(this).parent().toggleClass('active');
    });

    $('body').on("click",'[data-selected]',function() {
        $(this).toggleClass('active').siblings().removeClass('active');
        $(this).closest('.Select-Options-Items').removeClass('active');
        $(this).closest('.Select-Options-Items').find('.Selected-Value').val( $(this).data('selected') );

        $(this).closest('.Select-Options-Items').find('h2 > span').html( $(this).html() );

        if( $(this).closest('.-Next-Actions-Selected').length > 0 && $(this).data('selected') == 'mail'){
            $('.-fix-post-box[data-show-postbox="mail"]').show();
        }else if( $(this).closest('.-Next-Actions-Selected').length > 0 ){
            $('.-fix-post-box[data-show-postbox="mail"]').hide();
        }
    });

    $(document).mouseup(function(e){
        dropdown = $('.Select-Options-Items .-Select-DropDown');
        if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 ) {
            dropdown.closest('.Select-Options-Items').removeClass('active');
        }  
    });

// # YOURCOLOR CUSTOM FORM SUBMIT.
  $('body').on("submit", '[data-form-ajax]', function(){
      var FormElement = $(this);
      var data, Data_arr, submit, method, url, Validate, ActiveStep, CurrentStep, SubmitFields, FieldsArguments,UserActions;
      method = $(this).attr('method');
      var Action = $(this).attr('action');

      if( $(this).data('for-action') != undefined ){
        if( $(this).data('user-action') != undefined ){
          url = HomeURL+'/AjaxCenter/'+$(this).data('user-action')+'/'+$(this).attr('action')+'/';
          // #
          if( Currentuser_Logged == false ) return RedirectLoggin('sign_up');

        }else{
          url = HomeURL+'/AjaxCenter/'+$(this).attr('action')+'/';
        }
      }else{
        url = $(this).attr('action');
      }

      Validate = true;
      // # FORM ATTRBUTIS DATA.

        // # FORM DATA .
          data = $(this).serialize();
          Data_arr = $(this).serializeArray();

          var NewFormData = {};
          $.each(Data_arr,function(z,r) {
            if( r['value'] != '' ){
              NewFormData[ r['name'] ] = r['value'];
            }
          });
        // # ACTIVE STEP KEY .
          ActiveStep = $(this).data('active-step');
        // # DECODE FIELDS ARGUMENTS.
          SubmitFields = $(this).data('fields-arguments');

          FieldsArguments = atob( SubmitFields );
          FieldsArguments = jQuery.parseJSON( FieldsArguments );

        FormElement.find('.alert').remove();

        if( $(this).data('fetch-type') != undefined && $(this).data('fetch-type') == 'total' ) {
          
          $.each( FieldsArguments , function(k, steps){

            // # ACTIVE SATEP ACTIONS .
              if( k == ActiveStep ){

                // # SET CURRENT ACTION .
                CurrentStep = steps;

                if( steps.fields != undefined && !isEmpty( steps.fields ) ){
                  $.each( steps.fields ,function(s,field) {
                    var InnerValidate = true;
                    var AlertValidate = '';
                    if( field.type == 'tel' ){
                      // # EACH FIELDS FIND INPUTS AREA CODE .
                      FormElement.find('.-fix-inputs-area').each(function(k,re__elem){
                        if( $(re__elem).data('field-id') != undefined && $(re__elem).data('field-id') == field.id ){
                          if( $(re__elem).find('.iti__selected-dial-code').length > 0 ){
                            if( field.country_number == undefined ) field.country_number = 'country_number';
                            NewFormData[ field.country_number ] = $(re__elem).find('.iti__selected-dial-code').html();
                            data += '&'+field.country_number+'='+$(re__elem).find('.iti__selected-dial-code').html();
                          }
                          if( ( NewFormData[field.country_number] == undefined || NewFormData[field.country_number] == '' || isEmpty( NewFormData[field.country_number] ) ) && ( field.Require == true || field.Require == 'on' ) ){
                            Validate = false;
                            InnerValidate = false;
                          }
                          // # 
                          if( $(re__elem).find('.iti__active').length > 0 ){
                            if( field.country_code == undefined ) field.country_code = 'country_code';
                            NewFormData[ field.country_code ] = $(re__elem).find('.iti__active').attr('data-country-code');
                            data += '&'+field.country_code+'='+$(re__elem).find('.iti__active').attr('data-country-code');
                          } 
                          if( ( NewFormData[field.country_code] == undefined || NewFormData[field.country_code] == '' || isEmpty( NewFormData[field.country_code] ) ) && ( field.Require == true || field.Require == 'on' ) ){
                            Validate = false;
                            InnerValidate = false;
                          }

                          if( $(re__elem).find('input[type="tel"]').length > 0 ){
                            var iti = window.intlTelInputGlobals.getInstance( $(re__elem).find('input[type="tel"]')[0] );
                            if (!iti.isValidNumber()) {
                              Validate = false;
                              InnerValidate = false;
                              AlertValidate = 'تأكد من اضافة الرقم بشكل صحيح';
                            }
                          }
                        }                      
                      });
                    }

                    if( ( NewFormData[field.id] == undefined || NewFormData[field.id] == '' || isEmpty( NewFormData[field.id] ) ) && ( field.Require == true || field.Require == 'on' ) ){
                      Validate = false;
                      InnerValidate = false;
                    }

                    FormElement.find('.-fix-inputs-area').each(function(k,el){
                      if( $(el).data('field-id') != undefined && $(el).data('field-id') == field.id ){
                        if( $(el).find('new--necessay').length > 0 ) $(el).find('new--necessay').remove();

                        if( InnerValidate == false ){
                          $(el).addClass('-is-necessary');
                          $(el).append('<new--necessay>هذا الحقل مطلوب *</new--necessay>');

                          if( AlertValidate != '' ){
                            FormElement.prepend('<div class="alert element-alert-danger">'+AlertValidate+'</div>');
                          }
                                    

                          setTimeout(function() {
                              $(el).removeClass('-is-necessary');
                          },4000);
                          
                        }else{
                          $(el).removeClass('-is-necessary');
                        }
                      }                      
                    });
                  });
                }
              }        
          });
        }else{
          $.each( FieldsArguments , function(s, field){
            var InnerValidate = true;
            if( field.type == 'tel' ){
              // # EACH FIELDS FIND INPUTS AREA CODE .
              FormElement.find('.-fix-inputs-area').each(function(k,re__elem){
                if( $(re__elem).data('field-id') != undefined && $(re__elem).data('field-id') == field.id ){
                  if( $(re__elem).find('.iti__selected-dial-code').length > 0 ){
                    if( field.country_number == undefined ) field.country_number = 'country_number';
                    NewFormData[ field.country_number ] = $(re__elem).find('.iti__selected-dial-code').html();
                    data += '&'+field.country_number+'='+$(re__elem).find('.iti__selected-dial-code').html();
                  }
                  if( field.Require == 'on' && NewFormData[ field.country_number ] == undefined || field.Require == 'on' && NewFormData[field.country_number] != undefined && isEmpty( NewFormData[ field.country_number ] ) ){
                    Validate = false;
                    InnerValidate = false;
                  }
                  // # 
                  if( $(re__elem).find('.iti__active').length > 0 ){
                    if( field.country_code == undefined ) field.country_code = 'country_code';
                    NewFormData[ field.country_code ] = $(re__elem).find('.iti__active').attr('data-country-code');
                    data += '&'+field.country_code+'='+$(re__elem).find('.iti__active').attr('data-country-code');
                  } 
                  if( field.Require == 'on' && NewFormData[ field.country_code ] == undefined || field.Require == 'on' && NewFormData[field.country_code] != undefined && isEmpty( NewFormData[ field.country_code ] ) ){
                    Validate = false;
                    InnerValidate = false;
                  }

                  if( $(re__elem).find('input[type="tel"]').length > 0 ){
                    var iti = window.intlTelInputGlobals.getInstance( $(re__elem).find('input[type="tel"]')[0] );
                    if (!iti.isValidNumber()) {
                      Validate = false;
                      InnerValidate = false;
                      AlertValidate = 'تأكد من اضافة الرقم بشكل صحيح';
                      FormElement.prepend('<div class="alert element-alert-danger">'+AlertValidate+'</div>');
                    }
                  }                  
                }                      
              });
              // # FIELD CHECK LENGTH
                if( NewFormData[field.id] != undefined ){
                  var number__count = NewFormData[field.id].length;
                  if( number__count < 8 )  {
                    Validate = false;
                    InnerValidate = false;
                    AlertValidate = 'تأكد من اضافة الرقم بشكل صحيح';                  
                  }
                }

            }

            if( (NewFormData[field.id] == undefined || NewFormData[field.id] == '' || isEmpty( NewFormData[field.id] ) ) && ( field.Require == true || field.Require == 'on' ) ){
              Validate = false;
              InnerValidate = false;
            }

            FormElement.find('.-fix-inputs-area').each(function(k,el){
              if( $(el).data('field-id') != undefined && $(el).data('field-id') == field.id ){
                if( InnerValidate == false ){
                  $(el).addClass('-is-necessary');
                  setTimeout(function() {
                    $(el).removeClass('-is-necessary');
                  },1500);
                }else{
                  $(el).removeClass('-is-necessary');
                }
              }
            });
             
          });
        }
      // # VALIDATE .
        if( Validate != false ){
          if( method == 'GET' ){
            AjaxNavigate(url);
          }else{
            $.ajax({
              url: url,
              dataType: 'json',
              data: data,
              type: method,
              success: function(msg) {
                if( msg.error != undefined ){
                  FormElement.find('.alert').remove();
                  FormElement.prepend('<div class="alert element-alert-danger">'+msg.error+'</div>');
                  FormElement.animate({"scrollTop":0}, 500);

                }

                if( msg.ActiveStep != undefined ){  
                  FormElement.data("active-step",msg.ActiveStep);
                }

                if( msg.page__reloaded___time_out != undefined ){
                 setTimeout(function() {
                    location.reload();
                  },1000);
                }

                if( msg.page__AjaxNavigate != undefined ){
                  AjaxNavigate(msg.page__AjaxNavigate);
                }

                if( msg.page__loaction != undefined ) {
                  location.href = msg.page__loaction;
                }

                if( msg.FastAlerts != undefined ){
                  FastAlerts( msg.FastAlerts );

                }

                if( msg.comment__output != undefined ){
                  $(".CommentsListInner").prepend(msg.comment__output);
                }

                FormElement.find('.-fix-inputs-area').each(function(k,el){
                  $(el).find('input[type="text"]').val('');
                  $(el).find('textarea').val('');
                  $(el).find('.-image-preview-item').hide();
                  $(el).find('.-image-preview-item').html('');
                });

                if( msg.Next_output != undefined ){
                  // FormElement.html( msg.Next_output );
                  FormElement.replaceWith(msg.Next_output);
                }else if( msg.alert_output ){
                  $('.Context--overlays').remove();
                  $('body').append(msg.alert_output);
                }
                //TimerCountDown();
                //Initialize__intlTel();

              }
            });
          }
          
        }
      return false;
    });

  // # CHEVRONS STEPS .
    $('body').on('click','[data-form-navs]',function() {
      var BTN , CurrentStepID,FormElement;
      BTN = $(this);
      CurrentStepID = BTN.data('form-navs');
      FormElement = BTN.closest('form');

      method = FormElement.attr('method');
      var Action = FormElement.attr('action');

      if( FormElement.data('for-action') != undefined ){
        if( FormElement.data('user-action') != undefined ){
          url = HomeURL+'/AjaxCenter/'+FormElement.data('user-action')+'/'+FormElement.attr('action')+'/';
          // #
          if( Currentuser_Logged == false ) return RedirectLoggin('sign_up');

        }else{
          url = HomeURL+'/AjaxCenter/'+FormElement.attr('action')+'/';
        }
      }else{
        url = FormElement.attr('action');
      }

      Validate = true;
      // # FORM ATTRBUTIS DATA.

        // # FORM DATA .
          data = FormElement.serialize();
          data += '&current_step='+CurrentStepID;
          data += '&forms__navs=1';

          Data_arr = FormElement.serializeArray();

          var Print__R = '';
          var NewFormData = {};
          $.each(Data_arr,function(z,r) {
            NewFormData[ r['name'] ] = r['value'];
          });
        // # ACTIVE STEP KEY .
          ActiveStep = FormElement.data('active-step');
          if( ActiveStep == undefined ) Validate = false;
        // # DECODE FIELDS ARGUMENTS.
          SubmitFields = FormElement.data('fields-arguments');

          FieldsArguments = atob( SubmitFields );
          FieldsArguments = jQuery.parseJSON( FieldsArguments );

      // # VALIDATE .
        var FindCurrent = false;
        $.each( FieldsArguments , function(k, steps){

          if( k == CurrentStepID ) FindCurrent = steps;

          if( FindCurrent == false ) {

            if( steps.fields != undefined && !isEmpty( steps.fields ) ){

              $.each( FieldsArguments ,function(s,field) {
                if( k == ActiveStep ){

                  if( field.Require == 'on' && NewFormData[field.id] == undefined || field.Require == 'on' && NewFormData[field.id] != undefined && isEmpty( NewFormData[ field.id ] ) ){
                    Validate = false;
                  }

                }else{

                  if( field.Require == 'on' && NewFormData[ 'HistoryFields['+field.id+']' ] == undefined || field.Require == 'on' && NewFormData[ 'HistoryFields['+field.id+']' ] != undefined && isEmpty( NewFormData[ 'HistoryFields['+field.id+']' ] ) ){
                    Validate = false;
                  }
                }

              });
            }
          }
        });


        if( Validate == true ){

          AjaxRequest({
            url: url,
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function(msg) {
              if( msg.Next_output != undefined ){
                FormElement.replaceWith(msg.Next_output);
              }
            }
          });
        }
    });


// # RatingReview .
    $('body').on("mouseover", function(e){
        $(".RatingReview > i").each(function(r,dropdown) {
            if (!$(dropdown).is(e.target) && $(dropdown).has(e.target).length === 0 ) {
                if( !$(dropdown).nextAll().hasClass('active') || !$(dropdown).parent().is(e.target) && $(dropdown).parent().has(e.target).length === 0 ){
                $(dropdown).removeClass('active');
                }
            }
        });
    });

    $('body').on("mouseover",'.RatingReview > i', function(e){
        $(this).prevAll().addClass('active');
        $(this).addClass('active');
    });

    $('body').on("click",'.RatingReview:not(.justView) > i',function(){
        var BTN = $(this);

        // # CHANGE REVIEW.
        $('.RatingReview > i').removeClass('fixedactive active');
        BTN.prevAll().addClass('fixedactive');
        BTN.addClass('fixedactive');
    
        if( BTN.closest('.RatingReview').data('save-id') != undefined ){
            var ID = BTN.closest('.RatingReview').data('save-id');
            var Type = BTN.closest('.RatingReview').data('save-type');
            var RateValue = BTN.data('rate');
            var AppenderValue = $('.-rating-value[data-post-id="'+ID+'"]');
            var LastValueRate  = $.cookie( "RateV1-"+Type+"-"+ID );
            var AjaxURL = HomeURL+'/AjaxCenter/RateAjax/';

            AjaxRequest({
                url: AjaxURL,
                dataType: 'json',
                type: 'POST',
                data: {
                    "id":ID,
                    "Type":Type,
                    "LastValueRate":LastValueRate,
                    "RateValue":RateValue,
                },
                success: function(msg) {
                    AppenderValue.html(msg.TotalValue);
                    if( $('.-single-bottom-list-Rate').length > 0 ){
                        $('.-single-bottom-list-Rate').show();
                    }
                    if( $('.Rate-New-Mixers').length > 0 ){
                        $('.Rate-New-Mixers').show();
                    }
                    if( BTN.closest('.-FeedBack-Rating-MasterArea').length > 0 ){
                        var Centerrr = BTN.closest('.-FeedBack-Rating-MasterArea');
                    }
                    if( $('.-Js-Rate-AverageItems[data-post-id="'+ID+'"]').length > 0 && msg.output != undefined ){
                        $('.-Js-Rate-AverageItems[data-post-id="'+ID+'"]').html(msg.output);
                    }
                    if( $('.-rating-suptitle[data-post-id="'+ID+'"]').length > 0 && msg.RateUserCount_v1 != undefined ){
                        $('.-rating-suptitle[data-post-id="'+ID+'"] em').html(msg.RateUserCount_v1);
                    }
                    if( $('.-YC-Review-Change[data-review-change="'+ID+'"]').length > 0 ){
                        $('.-YC-Review-Change[data-review-change="'+ID+'"] > i').each(function(www,ee) {
                            if( $(ee).data('rate') <= msg.TotalValue ) {
                                $(ee).addClass('fixedactive');
                            }else{
                                $(ee).removeClass('fixedactive');
                            }
                        });
                    }
                }
            });
            $.cookie( "RateV1-"+Type+"-"+ID, RateValue );
        }else{
            $(this).closest('.RateComment').find('.product-item-info-stats-ratings > p > .-rating-value').text( $(this).data('rate')+'.0' );
            $(this).closest('.RateComment').find('input').val($(this).data('rate'));
        }
    });

// # data-click-video

    $('body').on("click",'[data-click-video]',function(){
        var BTN,Args;
        BTN = $(this);
        Args = BTN.data('click-video');
        var DecodeVideo = atob( Args );
        DecodeVideo = jQuery.parseJSON( DecodeVideo );

        BTN.parent().html(DecodeVideo);
    });

// # COMMENTS AREA
    function SubmitComment(elem,event=false) {
        var t = 0;
        if( event != false && event.keyCode == 13 || event == false ){

            $(elem).find(".alerts").html("");
            $(elem).find(".necessary").removeClass("necessary");

            var data = $(elem).serialize();
            var Data_arr = $(elem).serializeArray();

            var NewFormData = {};
            $.each(Data_arr,function(z,r) {
                NewFormData[ r['name'] ] = r['value'];
            });

            $(elem).find('.alerts').html('');
            var Validate = true;      
            if( NewFormData['user_name'] == undefined || NewFormData['user_name'] != undefined && NewFormData['user_name'] == '' ) { 
                if ( Currentuser_Logged == false ) {
                    Validate = false;
                    $(elem).find('.-comments-form-inputs-area[data-comment-field="user_name"]').addClass('necessary');
                }else{
                    data += '&user_name='+Currentuser_display_name;
                    NewFormData[ 'user_name' ] = Currentuser_display_name;
                }
            }else{
                $(elem).find('.-comments-form-inputs-area[data-comment-field="user_name"]').removeClass('necessary');
            }

            if( NewFormData['email'] == undefined || NewFormData['email'] != undefined && NewFormData['email'] == '' ) { 
                if( Currentuser_Logged == false ) {
                    Validate = false;
                    $(elem).find('.-comments-form-inputs-area[data-comment-field="email"]').addClass('necessary');
                }else{
                    data += '&email='+Currentuser_email;
                    NewFormData[ 'email' ] = Currentuser_email;
                }
            }else{
                $(elem).find('.-comments-form-inputs-area[data-comment-field="email"]').removeClass('necessary');
            }

            if( NewFormData['comment'] == undefined || NewFormData['comment'] != undefined && NewFormData['comment'] == '' ) { 
                Validate = false;
                $(elem).find('.-comments-form-inputs-area[data-comment-field="comment"]').addClass('necessary');
            }else{
                $(elem).find('.-comments-form-inputs-area[data-comment-field="comment"]').removeClass('necessary');
            }

            if( $(elem).data('id') == undefined || $(elem).data('id') != undefined && $(elem).data('id') == '' ) { 
                Validate = false;
            }

            if( $(elem).find('.RateValue').val() == '' && $(elem).attr('data-parent') == 0 ) {
                Validate = false;
                $(elem).find('.RateComment').addClass('necessary');
            }

            if( Validate == false ){
                $(elem).find(".alerts").append('<div class="alert alert-danger">' + __loc("يرجي ملأ الحقول المطلوبة (*)") + "</div>");
            }else{
                data += '&postID='+$(elem).data('id');
                if( $(elem).data('parent') != undefined && $(elem).data('parent') != '' ){
                    data += '&parent='+$(elem).data('parent');
                }

                $(".NoComments").remove();
                $(elem).addClass("loading");
                $(elem).append('<div class="loader"> <div class="line"></div> <div class="line"></div> <div class="line"></div> <div class="line"></div> </div>');
                AjaxRequest({
                    url: HomeURL + "/AjaxCenter/AddComment/",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (t) {
                        if( t.error == null ){
                            $(elem).removeClass("loading");
                            $(elem).find(".loader").remove();
                            $(elem).trigger("reset");
                            if( $(elem).attr("data-parent") == "0" ){
                                $(".CommentsListInner").prepend(t.output);
                            }else{
                                $("#comment-" + $(elem).attr("data-parent") ).after('<ul class="ChildComments">' + t.output + "</ul>");
                            }
                            var CommentScrollTimeOut = setTimeout(function(){
                                var comment_scroll_offset = $("#comment-"+t.comment_ID).offset().top;
                                $('body,html').animate({"scrollTop":comment_scroll_offset }, 500);
                            },100);
                        } 
                    }
                });

                if ( Currentuser_Logged == false ) {
                    $.cookie("user-data-email", NewFormData['email']);
                    $.cookie("user-data-name", NewFormData['name']);
                }
            }
            return false;
        }
    }
    function ReplyComment(e) {
        var ReplayArgums = $(e).data('replay-arguments');
        ReplayArgums = atob( ReplayArgums );
        ReplayArgums = jQuery.parseJSON( ReplayArgums );
        $("form.CommentsFormInner").attr("data-parent", $(e).data("comment"));
        $(".ReplyCommentPreview").remove();
        $("form.CommentsFormInner").prepend('<div class="ReplyCommentPreview"><h2><i class="fas fa-reply"></i><em>' + __loc("رد علي") + "</em> <span>"+ReplayArgums.UserName+"</span></h2><p>"+ReplayArgums.Comment_content+"</p></div>");
        $("form.CommentsFormInner > textarea").focus();
        var CommentScrollTimeOut = setTimeout(function(){
            var comment_scroll_offset = $(".ReplyCommentPreview").offset().top;
            $('body,html').animate({"scrollTop":comment_scroll_offset }, 500);
        },100);
    }
// # OVERLAY ACTOINS
  var aa = 0;
  function PopoverActions( Button ) {
    var Arguments = Button.data('popover-context');
    OvelayPopover(Arguments,Button);
  }

  function OvelayPopover(argument,button=false) {
    var Un__ID = UniqID();
    var DecodeArgums = $.base64.atob( argument ,true);
    DecodeArgums = jQuery.parseJSON( DecodeArgums );

    if( $('.Context--overlays').length === 0 ){
      var Overlay = '<div class="Context--overlays loading-popover-for--'+DecodeArgums.ActionBlade+'" data-hash="'+Un__ID+'">';

        Overlay += '<div class="Backdrop--Context---overlays"></div>';

        Overlay += '<div class="OverParent-Boxed--Context---overlays">';
          Overlay += '<div class="OverParent-Innet--Context">';

            Overlay += '<div class="Parent-Boxed--Context---overlays">';
              Overlay += '<div class="Boxed--Context---overlays">';

                  Overlay += '<div class="title--Context---overlays">';
                    Overlay += '<loader></loader>';
                    Overlay += '<span class="Close--title---Context----overlays activable" data-tooltip="إغلاق" data-button="popover--tool" data-position="top" data-tool-type="Closse--Popover"><i class="fas fa-times"></i></span>';
                  Overlay += '</div>';

                Overlay += '<div class="inner--Context---overlays">';
                  Overlay += '<div class="Loading--Context---overlays"><em></em><em></em></div>';
                Overlay += '</div>';

              Overlay += '</div>';
            Overlay += '</div>';
          Overlay += '</div>';

        Overlay += '</div>';
      Overlay += '</div>';
      $('body').append(Overlay);
    }
    
    AjaxRequest({
      url: HomeURL + "/AjaxCenter/PopoverActions/",
      dataType: 'json',
      type: 'POST',
      data: {
        "Arguments":argument,
      },
      success: function(msg) {
        if( msg.output != undefined ){
          $('.Context--overlays .OverParent-Innet--Context').html( msg.output );
          $('.Context--overlays').removeClass( 'loading-popover-for--'+DecodeArgums.ActionBlade );
        }

      }
    });
  }
  // # CLOSSE POPOVER .
    $(document).mouseup(function(e){
      dropdown = $('.Parent-Boxed--Context---overlays');
      if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 && $('iti.iti--container').length === 0 && ISMobile == false ) {
          dropdown.closest('.Context--overlays').remove();
        }
    });


  // # PopoverTools
    function PopoverTools( elem ) {
      var ActionTool = elem.data('tool-type');

      if( ActionTool == 'Closse--Popover' ){
        elem.closest('.Context--overlays').remove();
      }else if( ActionTool == 'Maximize--Popover' ){
        var W__Width = $('body').outerWidth() - 80;
        elem.closest('.Context--overlays').addClass('showis--fullwidh').css({"--sv-widht":W__Width+'px'});
        elem.css({"pointer-events":'none',"opacity":'0.7'});
        elem.next().css({"pointer-events":'auto',"opacity":'1'});
      }else if( ActionTool == 'Minimize--Popover' ){
        elem.closest('.Context--overlays').removeClass('showis--fullwidh');
        elem.css({"pointer-events":'none',"opacity":'0.7'});
        elem.prev().css({"pointer-events":'auto',"opacity":'1'});
      }

    }

// # DATA BUTTONS ACTIONS .

    $('body').on('click', '[data-button]', function(e){ e.preventDefault();
        var Button = $(this);
        //  # POPOVER ACTION .
          if( Button.data( 'button' ) == 'Popover--Action' ){
            PopoverActions( $(this) );
          }

        //  # POPOVER TOOL .
          if( Button.data( 'button' ) == 'popover--tool' ){
            PopoverTools( $(this) );
          }

        // # READ MORE COMMENT BUTTON ACTION.
          if( Button.data('button') == 'readmore-comment' ) {
            ReadMoreComment( $(this) );
          }

        // # READ MORE COMMENT BUTTON ACTION.
          if( Button.data('button') == 'readmore-objects' ) {
            ReadMoreObject( $(this) );
          }

        // # READ MORE PRODUCT CONTENTS .
            if( Button.data('button') == 'open-searching' ){
                open__searching( $(this) );
            }

        // # closse--searching
            if( Button.data('button') == 'closse--searching' ){
                Closse__searching( $(this) );
            }
        // # closse--searching
            if( Button.data('button') == 'closse--order-services' ){
                Closse__OrderSevices( $(this) );
            }
        // # closse--searching
            if( Button.data('button') == 'open-video' ){
                OpenVideoPopOver( $(this) );
            }
        // # closse--searching
            if( Button.data('button') == 'closse--video' ){
                Closse__VideoPopOver( $(this) );
            }

    });

// SEARCHING UI .   



// # VIDEO POPOVER

    function Closse__VideoPopOver( elem ) {
        elem.closest('.video--popover').remove();
    }

    // OpenVideoPopOver
      function OpenVideoPopOver(elem) {
            var Arguments = elem.data('video');
            var DecodeArgums = $.base64.atob( Arguments ,true);
            DecodeArgums = jQuery.parseJSON( DecodeArgums );
            VideoPopover = '';
            VideoPopover += '<div class="video--popover">';
                VideoPopover += '<div class="video--popover--overlay" data-button="closse--video"></div>';
                VideoPopover += '<div class="video--popover--body">';
                  VideoPopover += '<div class="video--popover--closse" data-button="closse--video"><i class="fa-solid fa-xmark"></i></div>';

                  VideoPopover += '<div class="video--popover-iframe">';
                    VideoPopover += DecodeArgums;
                  VideoPopover += '</div>';

                VideoPopover += '</div>';

            VideoPopover += '</div>';
            $('body').append( VideoPopover );
      }


// # READ MORE COMMENT .
    function ReadMoreComment(elem) {
        var Button = elem;
        Button.css({"pointer-events":'none'});
        Button.append('<div class="showbox"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <path fill="#005fa3" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform> </path> </svg></div>');
        AjaxSystem = $.ajax({
            url: HomeURL+'/AjaxCenter/CommentContent/'+Button.data('comment')+'/',
            dataType: 'json',
            success: function(msg) {
                Button.css({"pointer-events":'inherit'});
                Button.parent().html(msg.content);
                Button.find('.showbox').remove();
            }
        });
    }

// # ReadMoreObject
    function ReadMoreObject(elem) {
        var Button = elem;
        Button.css({"pointer-events":'none'});
        Button.append('<div class="showbox"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <path fill="#005fa3" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform> </path> </svg></div>');

        var Object__type = Button.data('object-type');
        var Object__name = Button.data('object-name');
        var Object__id = Button.data('object-id');

        var data = {
            'Object__type':Object__type,
            'Object__name':Object__name,
            'Object__id':Object__id,
        };

        AjaxRequest({
            url: HomeURL + "/AjaxCenter/ReadMoreObject/",
            type: "POST",
            data: data,
            dataType: "json",
            success: function(msg) {
                Button.css({"pointer-events":'inherit'});
                if( Button.closest('.--container--category--info').length > 0 ){
                    Button.closest('.--container--category--info').addClass('full--content--intro');
                }
                Button.parent().html(msg.content);
                Button.find('.showbox').remove();
            }
        });
    }

// # SINGLE TOGGLE TOC ITEM
    $('body').on("click",'toggle-toc',function(){
        $(this).parent().parent().toggleClass('showin');

        if( $(this).parent().parent().hasClass('showin') ){
            $(this).html('<span>إغلاق المراجع</span><i class="fa-solid fa-arrow-down"></i>');
        }else{
            $(this).html('<span>عرض الكل </span><i class="fa-solid fa-arrow-down"></i>');
        }
    });

// # SINGLE SHARE COPY CONTENT
    $("body").on("click", '.-share-popover-boxed-copy > button', function(){
        var This = $(this);
        clipboardText = This.parent().find('input').val(); 
        copyToClipboard( clipboardText );
        This.parent().addClass("active");

        setTimeout(function(){
            This.parent().removeClass('active');
        }, 1000);
    });   

// # TOGGLE FAQS .
    $('body').on("click",'[data-toggle-faqs]',function(e) {e.preventDefault();
        var BTN,MasterQuestion,TogggleContent,ToggleValue,ContnetHeight;
        BTN = $(this);
        MasterQuestion = BTN.parent();
        TogggleContent = MasterQuestion.find('.-Toggle-Content');
        ToggleValue = TogggleContent.find('.-ToggleContentValue');
        ContnetHeight = ToggleValue.outerHeight();
        TogggleContent.css({"--pin-height":38+ContnetHeight+'px'});
        if( BTN.data('toggle-off') != undefined  ){
          MasterQuestion.toggleClass("active");
        }else{
          MasterQuestion.toggleClass("active").siblings().removeClass("active");
        }
    });
;



$('body').on('click','.menu__icon',function(e){
    $(".--Site--Menu").toggleClass("open_menu");
    $(".menu__icon").toggleClass("trans_menu");
    $('body').addClass('is_open_menu');
});
if (ISMobile === true) {
    $('body').on("click", '.--Site--Menu > ul > li.menu-item-has-children > a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $subMenu = $(this).siblings("ul.sub-menu");
        var $parentItem = $(this).parent();
        
        $subMenu.toggleClass("active");
        $parentItem.find('> i').toggleClass('trans');
        $parentItem.toggleClass('hover');
        
        // إغلاق القوائم الفرعية الأخرى
        $(".--Site--Menu > ul > li.menu-item-has-children > a")
            .not(this)
            .parent()
            .removeClass("hover")
            .find("ul.sub-menu")
            .removeClass("active");
  });
}



 window.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('scroll', function() {
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        var serviceWidget = document.querySelector('.--YC-service-requset-widget--');

        if (scrollTop > 410 && serviceWidget) {
            serviceWidget.classList.add('fixed');
        } else if (serviceWidget) {
            serviceWidget.classList.remove('fixed');
        }
    });
});

// # TAPS FILTER ACTION .
  $('body').on("click",'[data-filter-tabs]',function(e) {e.preventDefault();
    var BTN = $(this);
    var FilterUniq = BTN.data('uniq');
    var FilterID = BTN.data('filter-tabs');
    var FilterCenter = $('.-Taps-AppendCenter[data-uniq="'+FilterUniq+'"]');
    var FilterArguments = FilterCenter.data('arguments');

    if( !BTN.hasClass('active') ){
      BTN.addClass('active').siblings().removeClass('active');

      FilterCenter.html('<div class="cet"><div class="loder"></div></div>');
      FilterCenter.addClass('Loader');
      if( BTN.closest('.-Yc-Products-Category').length > 0 ){
        BTN.closest('.-Yc-Products-Category').removeClass('active');
        BTN.closest('.-Yc-Products-Category').find('.-category-filter-title element-title').html(BTN.find('span').html());
        $('.-DropChevrons-UL[data-uniq="'+FilterUniq+'"]').addClass('Loader');
      }else if( BTN.closest('.-DropDown-Filters-Contain').length > 0 ){
        BTN.closest('.-DropDown-Filters-Contain').removeClass('active');
        BTN.closest('.-DropDown-Filters-Contain').find('.-DropDown-Filters-title element-title').html(BTN.find('span').html());
      }
      
      ActionURL = HomeURL+'/AjaxCenter/TabsActions';
      // #
      AjaxRequest({
        url: ActionURL,
        dataType: 'json',
        type: 'POST',
        data: {'filter_arguments':FilterArguments,'filter_id':FilterID,'UniqKey':FilterUniq},
        success: function(msg) {
          FilterCenter.html(msg.output);
          FilterCenter.data("arguments",msg.argums);
          FilterCenter.removeClass("Loader");
              if( msg.Childs_output != undefined ){
                $('.-DropChevrons-UL[data-uniq="'+FilterUniq+'"]').removeClass('Loader');
                $('.-DropChevrons-UL[data-uniq="'+FilterUniq+'"]').html(msg.Childs_output);
              }
            ProgressLoaded();
            }
        });
    }
});
$("body").on("click", "[data-more-click]", function(e) {
    e.preventDefault();
    var t = $('.-ScrollerCenter[data-uniqid="' + $(this).data("more-click") + '"]'),
        a = $(this).data("aciton-type");
    void 0 == a && (a = "fields"), !1 == LoadingMore && !1 == t.data("finish") && ($(this).addClass("isloader"), "fields" == a && MoreAjax(t), "posts" == a && More_Objects_Ajax(t))
});
function More_Objects_Ajax(e) {
    $('[data-more-click="' + e.data("uniqid") + '"]').prepend('<div class="-yc--mini-loader"><div class="-yc-inner-mini-loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>'), $('[data-more-click="' + e.data("uniqid") + '"]').addClass("isloader"), LoadingMore = !0, AjaxRequest({
        url: HomeURL + "/AjaxCenter/More-Ajax-objects",
        dataType: "json",
        type: "POST",
        data: {
            args: e.data("loadmore"),
            uniq: e.data("uniqid"),
            part: e.data("part"),
            user: Currentuser_ID
        },
        success: function(t) {
            LoadingMore = !1, e.find(".Loader__Circle__style__row").remove(), $(".-yc--mini-loader").remove(), $('[data-more-click="' + e.data("uniqid") + '"]').removeClass("isloader"), $('[data-more-click="' + e.data("uniqid") + '"]').find(".-yc--mini-loader").remove(), 0 === e.find(".NothingFoundFilter").length && e.append(t.output), e.data("loadmore", t.arguments), void 0 != t.ScrollLoader && !0 != t.ScrollLoader && (e.attr("data-finish", !0), $('[data-more-click="' + e.data("uniqid") + '"]').hide())
        }
    })
}
function Closse__searching(e) {
    $(".search_header").removeClass("open");
    $(".search_overlay").removeClass("open");
}


function open__searching(e) {
    var t = "";
    if (0 == $(".search_header").length) {
        var a = e.data("searching-argums"),
            i = $.base64.atob(a, !0);
            t += '<div class="search_header">', 
                t += '<div class="container">',
                    t += '<div class="search_closse" data-button="closse--searching"><i class="fa-solid fa-xmark"></i></div>', 
                    t += '<div class="search_body">', 
                        t += '<div class="search_content">', 
                            t += '<div class="search__close_x_">',
                                t += "<h2>" + (i = jQuery.parseJSON(i)).search_title + "</h2>",
                            t += '</div>',
                            t += '<form action="' + HomeURL + '" method="GET" >', 
                                t += '<input type="search" id="search-input" name="s" placeholder="' + i.search_placeholder + '">', 
                                t += '<div class="input_search_shadwo"></div>',
                                t += '<button aria-label="name" title="Search" type="submit">' + i.Text_search_button + "</button>", 
                            t += "</form>", 
                        t += '</div>',
                    t += "</div>", 
                t +='</div>',
            t += "</div>", 
            t += '<div class="search_overlay" data-button="closse--searching"></div>',
        $("body").append(t)


    }
    setTimeout(function() {
        $(".search_header").addClass("open");
        $(".search_overlay").addClass("open");
    }, 50);

}

$("body").on("click", '[data-trigger-action]', function(){
    var TriggerURL = $(this).data('trigger-action');
    if( $('a[data-trigger-url="'+TriggerURL+'"]').length > 0 ){
      window.location.href = $('a[data-trigger-url="'+TriggerURL+'"]').attr('href');
      }
  });

// # MENU EDITS 
  // # SETUP SUBEMENU ICONS CHEVRON DOWN.

  // # CREATE NEW MEGAMENU.
    var MenuIsAjax = false;
    function CreateMegaMenu(el) {
      if( MenuIsAjax == false ){
        MenuIsAjax = true;
        var li_element = $(el).closest('li');
        var element_argums = $(el).data('mega-menu');
        //
        $(el).addClass('-loading');

        if( li_element.find('ul').length == 0 ){
          li_element.append('<div class="-YourColor-Menu-DropDown"></div>');
        }

        var AjaxURL = HomeURL+'/AjaxCenter/MenusInitialize/';

        AjaxRequest({
          url: AjaxURL,
          dataType: 'json',
          type: 'POST',
          data: {"args":element_argums},
          success: function(msg) {
            $(el).addClass('-loaded');
            li_element.find('.-YourColor-Menu-DropDown').html(msg.output);
            MenuIsAjax = false;
            MenusInitialize();
          }
        });
      }
    }

  // # INITIALIZE SUBMENUS ACTION.
    function MenusInitialize() {

      if( $('[data-mega-menu]').length > 0 && navigator.userAgent.indexOf("Lighthouse") == -1 && MenuIsAjax == false){

        var i = 0;
        $('[data-mega-menu]').not('.-loading').each( function(k,el) {i++;
          if( i == 1 ) CreateMegaMenu(el);
        });
      }
    }

  function NavsCarouselEdits(event) {
    var current = event.item.index;
    var MasterUniq = $(event.target).data('uniq');
    if( $(event.target).find(".owl-prev").hasClass('disabled') ){
      $('.-custom-owl-Slides-prev[data-owlnavs-change="'+MasterUniq+'"]').css({"pointer-events":'none',"opacity":'0.6'}).addClass('disabled-btn');
    }else{
      $('.-custom-owl-Slides-prev[data-owlnavs-change="'+MasterUniq+'"]').css({"pointer-events":'auto',"opacity":'1'}).removeClass('disabled-btn');
    }

    if( $(event.target).find(".owl-next").hasClass('disabled') ){
      $('.-custom-owl-Slides-next[data-owlnavs-change="'+MasterUniq+'"]').css({"pointer-events":'none',"opacity":'0.6'}).addClass('disabled-btn');
    }else{
      $('.-custom-owl-Slides-next[data-owlnavs-change="'+MasterUniq+'"]').css({"pointer-events":'auto',"opacity":'1'}).removeClass('disabled-btn');
    }

  }

  $('body').on("click",'[data-owlnavs-change]',function() {
    var NavID = $(this).data('owlnavs-change');
    if( NavID != undefined ) NavID = $(this).data('owlnavs-change');

    var NavType = $(this).data('type');
    if( NavType == 'next' ){
      CaroseL__Events[ NavID ].trigger('next.owl.carousel');
    }else{
      CaroseL__Events[ NavID ].trigger('prev.owl.carousel', [300]);
    }
  });


  // # INITIALIZE Ref
    function ReferencesInitialize() {
      if( $('.-references-post').length > 0 ){
        var TitleHeight = $('.-references-post > h2').outerHeight();
        //
        var ULTocList = $('.-references-post > ul');
        //
        var TransDelay = 0.2;
          ULTocList.find('li').each(function(t, n) {
          if($(n).data('loaded') == undefined ){
            $(n).attr("data-loaded",false);
            // # TRANSTION UPDATE 
            TransDelay = TransDelay + 0.1;
            $(n).attr("style",'--trans-delay:'+TransDelay+'s');
          }
        });   
        //
        setTimeout(function(){
          var MyUlHeight = ULTocList.outerHeight();
          var FinalHeight = TitleHeight + MyUlHeight + 50;
          $('.-references-post').attr("style",'--tableofcontentH:'+FinalHeight+'px;');
        },100);
      }
    }

  // # COOKIES Post FeedBack 
    function PostFeedBacksInit() {
      $('[data-post-feedback]').each(function(k,elem){
        var PostID = $(elem).data('post');
        var Postfeedback = $(elem).data('post-feedback');
        var CookValue = $.cookie("user-PostFeedBack-"+PostID );
        if( CookValue == Postfeedback ){
          $(elem).addClass('active');
          if( $(elem).closest('.-post-reviews-area').length ) $(elem).closest('.-post-reviews-area').hide();
        }
      });
    }

  // # INITIALIZE COOKIES SCROLLER SINGLE
    function InitializeReadingMe(){
      $('[data-auto-reading-action]').each( function(e,elem__output) {
        if( $(elem__output).data('loaded-item') == undefined ){
          var Action__postID = $(elem__output).data('auto-reading-action');
          var Content__container = $('[data-content-start-scroll="'+Action__postID+'"]');
          var CookValue = $.cookie("post-reading-"+Action__postID );
          if( CookValue > 0 && CookValue < 100 ) {
            var final__offset = Content__container.outerHeight() * CookValue / 100;
            final__offset = final__offset + Content__container.offset().top;
            $(elem__output).addClass('show stop-reading');
            $('body,html').animate({"scrollTop":final__offset }, 500);
            $(elem__output).data("loaded-item",'loaded');
          }
          //$.cookie("post-reading-"+Action__postID, 100);
        }
      });
    }

  // # INITIALIZE WIDGETS ROOT VARS,
    function RootVars() {
      $('[data-roots-loded]').each(function(e,elem) {
        var RootsColors = $(elem).data('roots-loded');
        RootsColors = atob( RootsColors );
        RootsColors = jQuery.parseJSON( RootsColors );
        $(elem).closest('.-YourColor-SingleWidget-Section').attr("style",RootsColors);
      });
    }

  // # RANGE EDITS .
    function Range__Spans() {
      $('.-RangeFilter-range-input input[type=range]').each(function(a,nbx) {
        var RangeCenter = $(nbx).parent().find('input');
        var  minVal = parseInt(RangeCenter[0].value),
        maxVal = parseInt(RangeCenter[1].value);
        // #
        var style_left = (minVal / RangeCenter[0].max) * 100 + "%";
        var style_right = 100 - (maxVal / RangeCenter[1].max) * 100 + "%";

        if ( $(nbx).attr('class') == "-RangeFilter-range-min") {
          $('span[data-sp-uniq="'+$(nbx).data('sp-uniq')+'"]').attr("style",'right:'+style_left);
        } else {
          $('span[data-sp-uniq="'+$(nbx).data('sp-uniq')+'"]').attr("style",'left:'+style_right);
        }
      });
    }

  // # ProgressLoaded
    var ProgressLoaded = function(){
      setTimeout(function(){
        $('[data-progressload]').each(function(hmada, el){
          if( $(el).data('loded-item') == undefined ){
            var n = $(el).offset().top - $(window).height() - 100;
            if( $(window).scrollTop() >= n ){
              $(el).css({"width":$(el).data('progressload')+'%'});
              $(el).addClass('progressload-shows-in');
              $(el).data("loded-item",true);
            }
          }
        });   
      },300);
    }

  //# Counter Event.
    function CounterUP() {
        $("[counterup]").each(function(index, el){
          if( $(el).data('loded-item') == undefined ){
            var n = $(el).offset().top - $(window).height() - 100;
            if( $(window).scrollTop() >= n ){
              $({ Counter: 0 }).animate({
                Counter: $(el).text()
              },{
                duration: 3000,
                easing: 'swing',
                step: function() {
                  if($(el).data('round') != undefined){
                    var number = parseInt('' + (this.Counter * 100)) / 100;
                    $(el).text(Math.ceil(this.Counter));
                  }else{
                    $(el).text(Math.ceil(this.Counter));
                  }
                }
              });
              $(el).data("loded-item",true);
            }
          }
        });
      }

  

function loader_image(){
  $(".-YC-Loader-Cover").each(function(els, el){
      var n = $(el).offset().top - $(window).height() - 10;
      if( $(window).scrollTop() >= n ){
          $(el).parent().addClass('--img--is--loaded-lazyload');
      }

  });
}

    if( IsSpeed == false ) {
      LazyloaderHook();
    }

  // # SVG__Loader
    var LoadedSVG = false;
    function SVG__Loader() {   
      if( LoadedSVG == false ){
        $("[data-svg-loaders]").each(function (e, t) {
          var n = $(t).offset().top - $(window).height() - 400;
          //alert( 'n ::'+ n);
          //alert( 'SCROLL TOP ::' +$(window).scrollTop() );
          if( $(window).scrollTop() >= n ){LoadedSVG = true;
            var SVG__Src = $(t).data( 'svg-loaders' );
            // # 
            //var SVG__Scrape = HomeURL+'/SvgCenter/'+SVG__Src;

            if( SVG_List[ SVG__Src ] != undefined ){
              setTimeout(function() {
                $(t).append(SVG_List[ SVG__Src ]);
              },1000);
              $(t).removeAttr('data-svg-loaders');
              LoadedSVG = false;
            }else{
              alert(SVG_List);
            }
          }
        });
      }
    }

  // # CSS LOADER .
    function CSS__Loader(cssli) {   

      Css_List = cssli;
      $.each( Css_List ,function(k,csfile) {
        if( $('[data-style-ajax="'+k+'"]').length === 0 ){
          $('body').append('<link rel="stylesheet" data-style-ajax="'+k+'" type="text/css" href="'+csfile+'" />');
        }
      });

    }

  // # WOW LOADING.
    function WowAjaxify__Loaded(elem) {
      $(elem).find("[data-animation-id]").each(function (e, t) {
        if( $(t).data('loaded-animation') == undefined ){
          Wow__Item_show(t);
        }
      });
    }
    function Wow__Item_show(elem) {
      if( $(elem).data('loaded-animation') == undefined ){
        var StyleAttr = $(elem).attr('style');
        StyleAttr = ( ( StyleAttr == undefined ) ) ? '' : StyleAttr+';';
        var Animation_Name = $(elem).data('animation-id');

        var Animation_Duration = $(elem).data('animation-duration');
        if( Animation_Duration == undefined ) Animation_Duration = '1.2s';
        
        var Animation_Delay = $(elem).data('animation-delay');      

        $(elem).addClass('YC-Animation-Item');
        $(elem).data('loaded-animation','true');
        StyleAttr += '--animation-name:'+Animation_Name+';--animation-duration:'+Animation_Duration+';';
        if( Animation_Delay != undefined ){
          StyleAttr += '--animation-delay:'+Animation_Delay+';';
        }
        $(elem).attr("style",StyleAttr);
        ser = 800;
        if( Animation_Delay != undefined ){
          var ser = Animation_Delay.split('s')[0];
          ser = ser * 1000;
          ser = ser + 500;
        }
        setTimeout(function() {
          $(elem).removeClass("animation-hidden");
        },ser);

        Animation_Duration = Animation_Duration + 100;
        setTimeout(function() {
        },Animation_Duration);
      }
    }
    function YC_WowLoader() {
      if( $("[data-animation-id]").length > 0 ){
        $("[data-animation-id]").each(function (e, t) {
          if( $(t).data('loaded-animation') == undefined ){
            var StyleAttr = $(t).attr('style');
            StyleAttr = ( ( StyleAttr == undefined ) ) ? '' : StyleAttr+';';
            var Animation_Name = $(t).data('animation-id');

            var Animation_Duration = $(t).data('animation-duration');
            if( Animation_Duration == undefined ) Animation_Duration = '1.2s';
            
            var Animation_Delay = $(t).data('animation-delay');

            //$(t).addClass('animation-hidden');
            var n = $(t).offset().top - $(window).height() - 100;
            if( $(window).scrollTop() >= n ){
              Wow__Item_show(t);
            }
          }
        });
      }
    }

  // # Separator_Loader  
    function Separator_Loader() {
      if( $("[data-separator]").length > 0 ){
        $("[data-separator]").each(function (e, t) {
          var n = $(t).offset().top - $(t).outerHeight() - 600;
          if( $(window).scrollTop() >= n ){
            $(t).addClass('css-YourColor-separatorInView');
            $(t).removeAttr('data-separator');
          }
        });
      }
    }


// # MENU EDITS 

  // # CREATE NEW MEGAMENU.
    var MenuIsAjax = false;
    function CreateMegaMenu(el) {
      if( MenuIsAjax == false ){
        MenuIsAjax = true;
        var li_element = $(el).closest('li');
        var element_argums = $(el).data('mega-menu');
        //
        $(el).addClass('-loading');

        if( li_element.find('ul').length == 0 ){
          li_element.append('<div class="-YourColor-Menu-DropDown"></div>');
        }

        var AjaxURL = HomeURL+'/AjaxCenter/MenusInitialize/';

        AjaxRequest({
          url: AjaxURL,
          dataType: 'json',
          type: 'POST',
          data: {"args":element_argums},
          success: function(msg) {
            $(el).addClass('-loaded');
            li_element.find('.-YourColor-Menu-DropDown').html(msg.output);
            MenuIsAjax = false;
            MenusInitialize();
          }
        });
      }
    }

  // # INITIALIZE SUBMENUS ACTION.
    function MenusInitialize() {

      if( $('[data-mega-menu]').length > 0 && navigator.userAgent.indexOf("Lighthouse") == -1 && MenuIsAjax == false){

        var i = 0;
        $('[data-mega-menu]').not('.-loading').each( function(k,el) {i++;
          if( i == 1 ) CreateMegaMenu(el);
        });
      }
    }

// # PROGRESS CIRCLE ACTIONS .
  var ProgressSetup = [];
  function UpdateProgressCircle(id,data) {
    var ProgreesElement = $('[data-progressid="'+id+'"]');

    var Pewf = ProgressSetup[id];
    if( Pewf != undefined ){
      Pewf.value = data.value;
    }else{
      ProgreesElement.attr("data-progress",false);
      ProgreesCircleActions(id,data);
    }
  }

  function ProgreesCircleActions(id=false,data='') {
    $('[data-progressid]').each(function(k, progelem){
      var ProgressElement,ProgressData,LastAverage;
      ProgressElement = $(progelem);
      ProgressData = ProgressElement.data('progress');
      ProgressID = ProgressElement.data('progressid');
      
      if( id == false || id != false && ProgressID == id ){
        if( ProgressData != undefined && ProgressData != false || data != ''){
          if( data != '' ) ProgressData = data;
          if( id == false ){
            ProgressData = atob(ProgressData);
            ProgressData = $.parseJSON(ProgressData);
          }          
          ProgressSetup[ProgressID] = new CircleProgress(progelem,ProgressData);
          ProgressElement.addClass('LoadedProgress');
        }
      }
    });
  }
  ProgreesCircleActions();

// # SCROLL EVENTS && MAIN MENU SCROLLER 
  //if(!ISMobile){
    var prevScrollpos = window.pageYOffset;
    window.onscroll = function() {
      var currentScrollPos = window.pageYOffset;
      var Header__elem = $("header");
      var Body__elem = $("body");
      var Single__post_content = $('.-single-post-content');

      if (prevScrollpos > currentScrollPos) {
        if( Single__post_content.length > 0 ){
          var single_scroll_content = Single__post_content.offset().top + Single__post_content.outerHeight();
          if( single_scroll_content >  currentScrollPos && currentScrollPos > Single__post_content.offset().top ){
            Header__elem.addClass("hidemenu");
            Body__elem.addClass('hidemenu');
          }else{
            Header__elem.removeClass('hidemenu');
            Body__elem.removeClass('hidemenu');
          }
        }else{
          Header__elem.removeClass('hidemenu');
          Body__elem.removeClass('hidemenu');
        }
      }else{
        Header__elem.addClass("hidemenu");
        Body__elem.addClass('hidemenu');
      }
      prevScrollpos = currentScrollPos;
     
      // # TOC SCROLL EVENTS.
        if( $('[data-scroll-toc]').length > 0 ){
          var current__element = false;
          var current__obj = false;
          var num__it = 0;
          var Toc__counter = 0;
          var Toc_Current_numb = 0;
          $('[data-scroll-toc]').each(function(w,to_elem) {Toc__counter++;
            var r__offset_top = $(to_elem).offset().top - 80;

            if( currentScrollPos >= r__offset_top ){num__it++;
              $('[data-toc-links="'+$(to_elem).data('scroll-toc')+'"]').parent().addClass('active');
              $('[data-toc-links="'+$(to_elem).data('scroll-toc')+'"]').parent().removeClass('selected');
              current__obj = to_elem;
            }else{
              $('[data-toc-links="'+$(to_elem).data('scroll-toc')+'"]').parent().removeClass('active selected');
              if( current__element == false ) {
                current__element = current__obj;
                Toc_Current_numb = Toc__counter - 1;
              }
            }
          });

          if( current__element == false && num__it > 0 ) {
            current__element = current__obj;
            Toc_Current_numb = 1;
          }

          if( current__element != false ){
            $('[data-toc-links="'+$(current__element).data('scroll-toc')+'"]').parent().addClass('selected').siblings().removeClass('selected');
            /*var current_nav_item = $('[data-toc-links="'+$(current__element).data('scroll-toc')+'"]');
            var parent_toc_navs = current_nav_item.closest('.-third-single-post-bar');
            var countLines__tocs = countLines(parent_toc_navs,50);
            var nav_outerhieght = parent_toc_navs.outerHeight();
            var offset_nav_top = parent_toc_navs.offset().top;*/
          }
        }

      // # SCROLL EVENT SINGLE CONTENT PROGRESS .
        if( $('[data-content-start-scroll]').length > 0 ){

          $('[data-content-start-scroll]').each(function(r,s__elem){
            var Height__s__elem = $(s__elem).outerHeight();
            var offsetTop_elem = $(s__elem).offset().top;
            var Inner_Scroll__offset = currentScrollPos - offsetTop_elem;
            var Calc__percent = Inner_Scroll__offset * 100 / Height__s__elem;
            var Content__postID = $(s__elem).data('content-start-scroll');

            if( currentScrollPos > offsetTop_elem && ( offsetTop_elem + Height__s__elem ) > currentScrollPos ){
              $('.-progress-single-fixed[data-content-progress="'+Content__postID+'"]').show();
              $('.-progress-single-fixed[data-content-progress="'+Content__postID+'"] .--progress-bar-content-value').css({"--progress-width":Calc__percent+'%'});
              $('[data-auto-reading-action="'+Content__postID+'"]').show();
            }else{
              $('.-progress-single-fixed[data-content-progress="'+Content__postID+'"]').hide();
              $('[data-auto-reading-action="'+Content__postID+'"]').hide();
            }

            if( $('[data-auto-reading-action="'+Content__postID+'"]').length > 0 ){
              var S_argums = { 'max':100,'value':Calc__percent,'textFormat':'valueOnCircle' };
              UpdateProgressCircle(Content__postID,S_argums);
            }

          });
        }
        ProgreesCircleActions();

      // # 
         loader_image();
      SVG__Loader();
      Separator_Loader();
      YC_WowLoader();
      ProgressLoaded();
      CounterUP();
    }
  //}

  
  function TapsActivable__Action(){
      $('[data-activable-tab]').each(function(e,rw) {
        if( $(rw).hasClass('active') ){
          var elem_parent = $(rw).parent().parent();
          var parent__width = elem_parent.outerWidth();
          var offset__left = $(rw).offset().left;
          offset__left = offset__left - 3;
          // # 
          elem_parent.find('.activable-tab-overlay').css({"left":offset__left+'px',"width":$(rw).outerWidth()+'px',"height":$(rw).outerHeight()+'px'});
        }
      });
    }


// # LIKES EVENTS LODED 
  function InitializLikesAttr() {
    $("[data-like-item]").each(function(els, el){
      var EncodedLike = $(el).data('like-item');
      var LikeArguments = atob( EncodedLike );
      LikeArguments = jQuery.parseJSON( LikeArguments );
      // # ID
      var ID = LikeArguments.id;

      if( LikeArguments.multibuttons != undefined ){
        var LikeValue = LikeArguments.multibuttons;
        if( $.cookie("likes-"+LikeArguments.type+"-"+ID) == LikeValue ) {
          $(el).addClass('active');
        }
      }else{
        if( $.cookie("likes-"+LikeArguments.type+"-"+ID) == 'liked' ) {
          $(el).addClass('active');
        }
      }

    });
  }

  // # GET USERS COUNTRY .
    function GetUserCountry(){
      var CookValue = $.cookie("UserCounrty__v2" );
      if( CookValue == undefined || CookValue == '' ){
        $.getJSON('https://www.iplocate.io/api/lookup/', function(info) {
          if( info.country != undefined ){
            $.cookie("UserCounrty__v2", info.country_code);
          }else{
            $.cookie("UserCounrty__v2",'sa', { expires: 1 } );
          }
          GetUserCountry();
        });
      }
      return CookValue;
    }
    GetUserCountry();

  var Loaded__iniltes__arguments = [];
  function Initialize__intlTel() {
    if( $('[data-tel-initalize]').length > 0 ){
      $('[data-tel-initalize]').each(function(k,element) {
        if( $(element).data('loaded-initalize') == undefined ){
          $(element).data('loaded-initalize',true);
          var s__uni = $(element).data('uniq');
          var UserCountry = GetUserCountry();
          Loaded__iniltes__arguments[ s__uni ] = window.intlTelInput($(element)[0], {
            initialCountry: ( ( $(element).data('tel-initalize') != undefined && $(element).data('tel-initalize') != '' ) ) ? $(element).data('tel-initalize') : UserCountry,
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            preferredCountries: ["sa", "ae","kw","om","us"]
          });
        }
      });
    }
  }
  $(window).on('load', Initialize__intlTel());

// # THEME INITIALIZE.  
  function CustomInitialize(argument) {
    window.Sharer.init();
    RootVars();
    YC_WowLoader();

    // # MenusInitialize
      MenusInitialize();

    // # PostFeedBacksInit
      PostFeedBacksInit();

    // # InitializeReadingMe
      InitializeReadingMe();
    // # LazyloaderHook
    if( IsSpeed == false ) {
      LazyloaderHook();
      SVG__Loader();

    }
    SubMenusIcons();
    Separator_Loader(); 
    ProgressLoaded();
    CounterUP();
    Range__Spans();
    ReferencesInitialize();

    InitializeOwlCarousel();
    Initialize__intlTel();

    InitializLikesAttr();

    setTimeout(function(){
      TapsActivable__Action();
    },500);

  }
  CustomInitialize();

  function Activable__Navs(event) {
    var current = event.item.index;
    var DotsUniq = elemCurr.find('.-single-features-step-item').data("uniq");
    var MasterUniq = $(event.target).data('uniq');

    var TotalCount = 0;
    var ActionPostion = 1;
    $('.-owl-dots-steps-items[data-uniq="'+MasterUniq+'"] .-steps-dots-singleitem').each( function(e,dotelem) {TotalCount++;
      var SingleUniq = $(dotelem).data('dots-sliders');
      //alert(SingleUniq);
      if( SingleUniq == DotsUniq ){
        $(dotelem).addClass('active');
        ActionPostion = TotalCount;
      }else{
        $(dotelem).removeClass('active');
      }
    });

    if( ActionPostion <= 1 ){
      $('.-steps-Slides-prev[data-navs-change="'+MasterUniq+'"]').css({"pointer-events":'none',"opacity":'0.6'});
    }else{
      $('.-steps-Slides-prev[data-navs-change="'+MasterUniq+'"]').css({"pointer-events":'auto',"opacity":'1'});
    }

    if( ActionPostion == TotalCount ){
      $('.-steps-Slides-next[data-navs-change="'+MasterUniq+'"]').css({"pointer-events":'none',"opacity":'0.6'});
    }else{
      $('.-steps-Slides-next[data-navs-change="'+MasterUniq+'"]').css({"pointer-events":'auto',"opacity":'1'});
    }    
  }

  $('body').on('click','[data-navs-change]',function() {
    var NavID = $(this).data('navs-change');
    var NavType = $(this).data('type');
    if( NavType == 'next' ){
      CaroseL__Events[ NavID ].trigger('next.owl.carousel');
    }else{
      CaroseL__Events[ NavID ].trigger('prev.owl.carousel', [300]);
    }
  });

  $('body').on('click','[data-dots-sliders]',function() {
    var NavID = $(this).data('dots-sliders');
    var NavUniq = $(this).data('uniq');
    var GetPostion = $('.-single-features-step-item[data-uniq="'+NavID+'"]').parent().index();
    CaroseL__Events[ NavUniq ].trigger("to.owl.carousel", GetPostion, 500);
  });
document.addEventListener('DOMContentLoaded', function() {
    var searchForm = document.querySelector('#search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            var searchInput = document.querySelector('#search-input').value.trim();
            if (searchInput === '') {
                event.preventDefault();
                window.location.href = '<?php echo home_url(); ?>';
            }
        });
    }
});

if (ISMobile === true) {
    $('body').on("click", '.--Site--Menu > ul > li.menu-item-has-children > a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $subMenu = $(this).siblings("ul.sub-menu");
        var $parentItem = $(this).parent();
        
        $subMenu.toggleClass("active");
        $parentItem.find('> i').toggleClass('trans');
        $parentItem.toggleClass('hover');
        
        // إغلاق القوائم الفرعية الأخرى
        $(".--Site--Menu > ul > li.menu-item-has-children > a")
            .not(this)
            .parent()
            .removeClass("hover")
            .find("ul.sub-menu")
            .removeClass("active");
    });
}
