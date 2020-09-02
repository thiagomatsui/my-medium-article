(function($) {
  $(function() {
    MyMediumArticles.loadVideos(my_medium_article_ajax.url).then((value) => {
      MyMediumArticles.listCallbacks.forEach((item) => {
        item.callback(value, item.container, item.limit, item.lang);
      })
    });
  });
})(jQuery);