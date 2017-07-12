function showGraph(){
	if (d3.selectAll("#graph_container").style("visibility") == "hidden"){
		d3.selectAll("#graph_container").style("visibility", "visible");
		d3.selectAll("#showButton_container").style("visibility", "hidden")
	}
};

function closeGraph(){
	if (d3.selectAll("#graph_container").style("visibility") == "visible"){
		d3.selectAll("#graph_container").style("visibility", "hidden");
		d3.selectAll("#showButton_container").style("visibility", "visible")
	}
}