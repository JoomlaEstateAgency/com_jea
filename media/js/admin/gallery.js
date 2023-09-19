/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate
 * agency
 *
 * @copyright Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license GNU/GPL, see LICENSE.txt
 */

+function ($) {

  $(document).ready(function() {
      $('a.delete-img').click(function(e) {
          e.preventDefault();
          $(this).closest('li').fadeOut(300, function() { $(this).remove(); });
      });

      $('a.img-move-up').click(function(e) {
          e.preventDefault();
          var activeLi = $(this).closest('li');
          activeLi.fadeOut(300, function() {
              if (activeLi.prev().length) { activeLi.insertBefore(activeLi.prev()); }
              else if (activeLi.parent().children().length > 1) { activeLi.appendTo(activeLi.parent()); }
              activeLi.fadeIn(300);
          });
      });

      $('a.img-move-down').click(function(e) {
          e.preventDefault();
          var activeLi = $(this).closest('li');
          activeLi.fadeOut(300, function() {
              if (activeLi.next().length) { activeLi.insertAfter(activeLi.next()); }
              else if (activeLi.parent().children().length > 1) { activeLi.prependTo(activeLi.parent()); }
              activeLi.fadeIn(300);
          });
      });
  });

}(jQuery);

