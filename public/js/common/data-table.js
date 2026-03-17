'use strict';

(function (global) {
    function getJQuery() {
        return global.jQuery || null;
    }

    function hasDataTableV2() {
        return typeof global.DataTable === 'function';
    }

    function hasJQueryDataTable() {
        var jq = getJQuery();
        return !!(jq && jq.fn && typeof jq.fn.DataTable === 'function');
    }

    function isInitialized(tableElement) {
        var jq = getJQuery();

        if (hasDataTableV2() && typeof global.DataTable.isDataTable === 'function' && global.DataTable.isDataTable(tableElement)) {
            return true;
        }

        if (hasJQueryDataTable() && jq.fn.dataTable && typeof jq.fn.dataTable.isDataTable === 'function' && jq.fn.dataTable.isDataTable(tableElement)) {
            return true;
        }

        return false;
    }

    function registerMomentFormats(formats) {
        var jq = getJQuery();
        if (!hasJQueryDataTable() || !jq.fn.dataTable || typeof jq.fn.dataTable.moment !== 'function') {
            return;
        }

        (formats || []).forEach(function (format) {
            jq.fn.dataTable.moment(format);
        });
    }

    function init(tableElement, options) {
        var jq = getJQuery();

        if (!tableElement || (!hasDataTableV2() && !hasJQueryDataTable()) || isInitialized(tableElement)) {
            return null;
        }

        return hasDataTableV2()
            ? new global.DataTable(tableElement, options)
            : jq(tableElement).DataTable(options);
    }

    global.intranetDataTable = {
        init: init,
        isInitialized: isInitialized,
        registerMomentFormats: registerMomentFormats,
        hasDataTableV2: hasDataTableV2,
        hasJQueryDataTable: hasJQueryDataTable
    };
})(window);
