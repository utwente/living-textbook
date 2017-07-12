var watermark = "Search";
$("#search").val(watermark).addClass("watermark")
    .blur(function(){
        if ($(this).val().length == 0){
            $(this).val(watermark).addClass("watermark");
        }
    })
    .focus(function(){
        if ($(this).val() == watermark){
            $(this).val("").removeClass("watermark");
        }
    });

var graph = (function () {
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url': "data/lastFullGraph2.json",
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
})();


console.log(graph);

var optArray = [];
for (var i = 0; i < graph.nodes.length - 1; i++) {
    optArray.push(graph.nodes[i].label);
}
optArray = optArray.sort();
$(function () {
    $("#search").autocomplete(
        {
            source: optArray
        },
        {
            select: function(e,ui){
                $("#search").val(ui.item.label);
                searchNode();
            }
        }

    )
});

function searchNode() {
    //find the node
    var selectedVal = document.getElementById('search').value;
    var node = g.selectAll(".node");
    var link = svg.selectAll(".link");
    var linklabel = svg.selectAll(".linklabel");
    var text = svg.selectAll("text");
    if (selectedVal == "none") {
        node.style("stroke", "white").style("stroke-width", "1");
    } else {
        var selectedNode = node.filter(function (d, i) {
            return d.label != selectedVal;
        });
        var selectedLabel = text.filter(function (d, i) {
            return d.label != selectedVal;
        });
        var focusNode = node.filter(function (d, i) {
            if (d.label == selectedVal) {
                return d;
            }
        });

        console.log(focusNode);
        var focusNodeX = focusNode["0"]["0"].__data__.px;
        var focusNodeY = focusNode["0"]["0"].__data__.py;
        var cont_width = document.getElementById("graph_container").offsetWidth / 2;
        var cont_height = document.getElementById("graph_container").offsetHeight / 2;
        var tr_x = cont_width - focusNodeX;
        var tr_y = cont_height - focusNodeY;

        g.transition()
            .duration(3000)
            .attr("transform", "translate (" + tr_x + "," + tr_y + ")");

        selectedNode.style("opacity", "0.2");
        selectedLabel.style("opacity", "0.2");
        link.style("opacity", "0.2");
        linklabel.style("opacity", "0.2");
        d3.selectAll(".node, .link, .linklabel, text").transition()
            .duration(7000)
            .style("opacity", 1);

        zoom.translate([tr_x, tr_y]);
        zoom.scale(1);
    }
}
