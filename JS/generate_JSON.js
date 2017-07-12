/**
 * Created by theli on 1/9/2017.
 */
d3sparql.graph = function(json, config) {
    config = config || {};

    var head = json.head.vars;
    var data = json.results.bindings;

    var opts = {
        "node1URI": config.node1URI || head[0] || "node1URI",
        "node2URI": config.node2URI || head[1] || "node2URI",
        "node1Label": config.node1Label || head[3] || false,
        "node2Label": config.node2Label || head[4] || false,
        "node1Link": config.node1Link || head[5] || false,
        "node2Link": config.node2Link || head[6] || false,
        "relationURI": config.relationURI || head[7] || false,
        "relationLabel": config.relationLabel || head[8] || false
    };
    var graph = {
        "nodes": [],
        "links": []
    };
    var check = d3.map();
    var index = 0;
    for (var i = 0; i < data.length; i++) {
        var node1URI = data[i][opts.node1URI].value;
        var node2URI = data[i][opts.node2URI].value;
        var node1Label = opts.node1Label ? data[i][opts.node1Label].value : false;
        var node2Label =  opts.node2Label ? data[i][opts.node2Label].value: false;
        var node1Link =  opts.node1Link ? data[i][opts.node1Link].value: false;
        var node2Link =  opts.node2Link ? data[i][opts.node2Link].value: false;
        var relationURI = opts.relationURI ? data[i][opts.relationURI].value : false;
        var relationLabel = opts.relationLabel ? data[i][opts.relationLabel].value : false;

        if (!check.has(node1URI)) {
            graph.nodes.push({"nodeName": node1URI, "label": node1Label, "link": node1Link});
            check.set(node1URI, index);
            index++
        }
        if (!check.has(node2URI)) {
            graph.nodes.push({"nodeName": node2URI, "label": node2Label, "link": node2Link});
            check.set(node2URI, index);
            index++
        }
        graph.links.push({"source": check.get(node1URI), "target": check.get(node2URI), "relation": check.get(relationURI), "relationName": check.get(relationLabel)})
    }
    if (d3sparql.debug) { console.log(JSON.stringify(graph)) }
    return graph
};