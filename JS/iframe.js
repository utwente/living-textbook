function callIframe(d){
    var wiki_link = d.link;

    if (wiki_link != false){
        d3.selectAll("#iframe").attr("src", wiki_link);
    };
}