// (function ($, Drupal) {
//
//   'use strict';
//
//   /**
//    * Behavior description.
//    */
//   Drupal.behaviors.logitworks = {
//     attach: function (context, settings) {
//
//       console.log('It really works!');
//
//     }
//   };
//
// } (jQuery, Drupal));

// (function (Drupal, $) {
//   "use strict";
//   // Our code here.
//   console.log('Yep - more stuff working.')
// }) (Drupal, jQuery);


// (function (Drupal, $) {
//   Drupal.behaviors.logitworks = {
//     attach: function (context, settings) {
//       //console.log('It really works!!!');
//       $('#noah').append(" and test this") ;
//
//     }
//   };
// }) (Drupal, jQuery);

// (function (Drupal, $) {
//   Drupal.behaviors.logitworks = {
//     attach: function (context, settings) {
//       // $('#noah').append(" and test this") ;
//
//       /* loop through all the missions and add test to the end */
//
//       // var noah_divs = $('#noah') ;
//       //
//       // noah_divs.each( function () {
//       //   $(this).append('test');
//       // })
//
//       const noah_elements = document.querySelectorAll('#noah');
//       noah_elements.forEach(element => {
//         // Do something with each element
//         element.append('test');
//       });
//
//       const elements = document.querySelectorAll('.yomama');
//       elements.forEach(element => {
//         element.style.backgroundColor = 'red';
//       });
//
//
//     }
//   };
// }) (Drupal, jQuery);



// runs once.
// (function($) {
//   $(document).ready(function() {
//     // Your code here.
//     console.log('Yep - more stuff working.');
//   });
// })(jQuery);

// runs multiple times.
// (function($) {
//
//   // Define your namespace.
//   Drupal.myModule = {};
//
//   // Attach your behavior to the document ready event.
//   Drupal.behaviors.myModule = {
//     attach: function (context, settings) {
//       // Do your stuff here.
//       console.log('Yep - stuff working.');
//     }
//   };
//
// })(jQuery);



