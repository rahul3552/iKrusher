define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({

        /**
         * @inheritdoc
         */
        getLabel: function (row) {
            var number = this._super(row);

            return String(Number(number * 1).toFixed(2)) + '%';
        }
    });
});
