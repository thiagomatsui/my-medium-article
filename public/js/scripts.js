const MyMediumArticles = {
  listCallbacks: [],

  async loadVideos(url) {
    // console.log(
    //   `%cMy Medium Aticle: Loading data from JSON'`,
    //   "background:green;color:white"
    // );

    const postData = {
      action: "my_medium_article_posts",
    };

    let request = jQuery.ajax({
      method: "GET",
      url: url,
      data: postData,
      dataType: "json",
    });

    return await request.done();
  },

  buildList(jsonData, containerId, limit = 15) {
    const myData = jsonData;
    let theList = document.createElement("div");

    theList.className = "my-medium-article";

    let posts = {};
    if (limit != null) posts = myData.posts.slice(0, limit);
    for (let i = 0; i < posts.length; i++) {
      theList.appendChild(
        MyMediumArticles.buildListItem(posts[i])
      );
    }

    let container = document.querySelector(`#${containerId}`);
    container.innerHTML = "";
    container.appendChild(theList);
  },

  buildListItem(item) {
    const theItem = document.createElement("div");
    theItem.className = "my-medium-article-item";

    theItem.innerHTML = `
            <div class="mediumArticle">
                <a href="${item.link}" target="_blank" title="${item.title}">
                    <div class="mediumArticleImg">
                        <img src="${item.image}">
                    </div>
                    <div class="mediumArticleCnt">
                        <h3 class="mediumArticleTitle">${item.title}</h3>
                        <p class="mediumArticleDate">${item.date}</p>
                    </div>
                </a>
            </div>`;

    return theItem;
  },
};