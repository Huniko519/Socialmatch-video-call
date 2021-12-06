/**
 * 
 * flatNotify.js v0.1
 * @screenshake
 *
 * Inspired by :
 * http://tympanus.net/codrops/2014/07/23/notification-styles-inspiration/
 * 
 * Animation courtesy :
 * bounce.js - http://bouncejs.com/
 * 
 * Class manipulation
 * classie.js https://github.com/desandro/classie
 * 
 */
;( function( window ) {

  var proto_methods = {
    options: {
      wrapper: document.body,
      dismissIn: 5000
    },
    init: function() {
      this.ntf = document.createElement('div');
      this.ntf.className = 'f-notification';
      var strinner = '<div class="f-notification-inner"></div><div class="f-close">x</div></div>';
      this.ntf.innerHTML = strinner;

      // append to body or the element specified in options.wrapper
      this.options.wrapper.insertBefore(this.ntf, this.options.wrapper.lastChild);

      // init events
      this.initEvents();
    },
    initEvents: function() {
      var self = this;
      // dismiss notification
      this.ntf.querySelector('.f-close').addEventListener('click', function() {
        self.dismiss();
      });
    },
    dismiss: function() {
      var self = this;
      clearTimeout(this.dismissttl);

      classie.remove(self.ntf, 'f-show');
      setTimeout(function() {
        classie.add(self.ntf, 'f-hide');
      }, 25);

      setTimeout(function() {
        self.options.wrapper.removeChild( self.ntf );
      }, 500);
      
    },
    setType: function(newType) {
      var self = this;

      classie.remove(self.ntf, 'f-notification-error');
      classie.remove(self.ntf, 'f-notification-alert');
      classie.remove(self.ntf, 'f-notification-success');

      classie.add(self.ntf, newType);

    },
    success: function(message, dismissIn) {
      var self = this;

      /**
       * Use supplied dismiss timeout if present, else uses default value.
       * If set to 0, doesnt automatically dismiss.
       */
      dismissIn = (typeof dismissIn === "undefined") ? this.options['dismissIn'] : dismissIn;

      /**
       * Set notification type styling
       */
      self.setType('f-notification-success');

      self.ntf.querySelector('.f-notification-inner').innerHTML = message;

      classie.remove(self.ntf, 'f-hide');
      classie.add(self.ntf, 'f-show');

      if (dismissIn > 0) {
        this.dismissttl = setTimeout(function() {
          self.dismiss();
        }, dismissIn);
      }
    },
    error: function(message, dismissIn) {
      var self = this;

      /**
       * Use supplied dismiss timeout if present, else uses default value.
       * If set to 0, doesnt automatically dismiss.
       */
      dismissIn = (typeof dismissIn === "undefined") ? this.options['dismissIn'] : dismissIn;

      /**
       * Set notification type styling
       */
      self.setType('f-notification-error');

      self.ntf.querySelector('.f-notification-inner').innerHTML = message;

      classie.remove(self.ntf, 'f-hide');
      classie.add(self.ntf, 'f-show');

      if (dismissIn > 0) {
        this.dismissttl = setTimeout(function() {
          self.dismiss();
        }, dismissIn);
      }
    },
    alert: function(message, dismissIn) {
      var self = this;

      /**
       * Use supplied dismiss timeout if present, else uses default value.
       * If set to 0, doesnt automatically dismiss.
       */
      dismissIn = (typeof dismissIn === "undefined") ? this.options['dismissIn'] : dismissIn;

      /**
       * Set notification type styling
       */
      self.setType('f-notification-alert');

      self.ntf.querySelector('.f-notification-inner').innerHTML = message;

      classie.remove(self.ntf, 'f-hide');
      classie.add(self.ntf, 'f-show');

      if (dismissIn > 0) {
        this.dismissttl = setTimeout(function() {
          self.dismiss();
        }, dismissIn);
      }
    }
  }, flatNotify, _flatNotifiy;

  _flatNotifiy = function() {
    this.init();
  };

  _flatNotifiy.prototype = proto_methods;

  flatNotify = function() {
    return new _flatNotifiy();
  };

  /**
   * add to global namespace
   */
  window.flatNotify = flatNotify;

} )( window );
