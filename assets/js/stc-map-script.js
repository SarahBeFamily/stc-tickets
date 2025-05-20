$ = jQuery;
jQuery(document).ready(function ($) {
    var UrlVars = getUrlVars();
    if(UrlVars.selectionMode && UrlVars.selectionMode == '1') {
        
    window.onload = function () {
        let map_wrapper = jQuery(document).find('.spettacolo-prices-img');
        let map_wrapper_zoom = jQuery(document).find('.spettacolo-prices-img').attr('map-zoom');
        var svgElement = document.getElementById('svgSeatSvg');
        var panZoom = svgPanZoom(svgElement, {
            zoomEnabled: true,
            controlIconsEnabled: false,
            zoomEnabled: true,
            fit: true,
            center: true,
            minZoom: 0.5,
            maxZoom: 7,
            zoomScaleSensitivity: 0.3,
            mouseWheelZoomEnabled: true,
            preventMouseEventsDefault: false,
            onPan: handlePan
        });
        let initialPanX = panZoom.getPan().x;
        let initialPanY = panZoom.getPan().y;
        console.log("load",panZoom);
        map_wrapper.attr('map-zoom', panZoom.getZoom());
        map_wrapper.attr('map-panX', panZoom.getPan().x);
        map_wrapper.attr('map-panY', panZoom.getPan().y);
        function handlePan(newPan) {
            var afterZoomPan = panZoom.getPan();
            var afterZoom = panZoom.getZoom();
            map_wrapper.attr('map-zoom', afterZoom);
            map_wrapper.attr('map-panX', afterZoomPan.x);
            map_wrapper.attr('map-panY', afterZoomPan.y);
        }
        document.getElementById('zoom-in').addEventListener('click', function (ev) {
            ev.preventDefault();
            panZoom.zoomIn();
            var afterZoomPan = panZoom.getPan();
            var afterZoom = panZoom.getZoom();
            map_wrapper.attr('map-zoom', afterZoom);
            map_wrapper.attr('map-panX', afterZoomPan.x);
            map_wrapper.attr('map-panY', afterZoomPan.y);
        });
        document.getElementById('zoom-out').addEventListener('click', function (ev) {
            ev.preventDefault();
            panZoom.zoomOut();
            var afterZoomPan = panZoom.getPan();
            var afterZoom = panZoom.getZoom();
            map_wrapper.attr('map-zoom', afterZoom);
            map_wrapper.attr('map-panX', afterZoomPan.x);
            map_wrapper.attr('map-panY', afterZoomPan.y);
        });
        document.getElementById('reset').addEventListener('click', function (ev) {
            ev.preventDefault();
            panZoom.resetZoom(1);
            panZoom.pan({x:initialPanX,y:initialPanY});

            var afterZoomPan = panZoom.getPan();
            var afterZoom = panZoom.getZoom();
            map_wrapper.attr('map-zoom', afterZoom);
            map_wrapper.attr('map-panX', afterZoomPan.x);
            map_wrapper.attr('map-panY', afterZoomPan.y);
        });
    };
    }

});