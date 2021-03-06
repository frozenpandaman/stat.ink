(function (window) {
  "use strict";
  var $ = window.jQuery;
  $(window.document).ready(function () {
    $(document.body).append(
      $('<div>').addClass('github-fork-ribbon-wrapper right').append(
        $('<div>').addClass('github-fork-ribbon').css('background-color', '#f80').append(
          $('<a>')
            .attr(
                'href', 'https://github.com/fetus-hina/stat.ink'
            ).append(
              'Fork me on '
            ).append(
              $('<span>').addClass('fa fa-github-alt')
            ).append(
              'GitHub'
            )
        )
      )
    );
  });
})(window);
