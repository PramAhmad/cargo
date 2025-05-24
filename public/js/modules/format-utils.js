/**
 * Format Utilities Module
 * Handles number formatting and parsing
 */

export const FormatUtils = {
    formatNumber(number) {
        if (!number) return '0,00';
        return parseFloat(number).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },
    
    parseNumberFromFormatted(formattedNumber) {
        if (!formattedNumber) return 0;
        return parseFloat(formattedNumber.replace(/\./g, '').replace(',', '.'));
    },
    
    initMoneyInputs() {
        $('.money-mask').each(function() {
            new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.'
            });
        });
    }
};