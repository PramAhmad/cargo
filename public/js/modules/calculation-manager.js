/**
 * Calculation Manager Module
 * Handles calculations for totals, fees, taxes, etc.
 */

import { FormatUtils } from './format-utils.js';

export const CalculationManager = {
    state: null,
    
    init(shippingState) {
        this.state = shippingState;
    },
    
    calculateTotals() {
        let totalCtns = 0;
        let totalQty = 0;
        let totalVolume = 0;
        let totalGw = 0;
        
        // Count from product rows
        $('.product-row').each(function() {
            const ctns = parseFloat($(this).find('.total-ctns').val()) || 0;
            const qty = parseFloat($(this).find('.total-qty').val()) || 0;
            const volume = parseFloat($(this).find('.volume').val()) || 0;
            const gw = parseFloat($(this).find('.total-gw').val()) || 0;
            
            totalCtns += ctns;
            totalQty += qty;
            totalVolume += volume;
            totalGw += gw;
        });
        
        // Update display fields
        $('#carton_display').val(FormatUtils.formatNumber(totalCtns));
        $('#gw_display').val(FormatUtils.formatNumber(totalGw));
        $('#volume_display').val(FormatUtils.formatNumber(totalVolume));
        $('#cbm_display').val(FormatUtils.formatNumber(totalVolume)); // CBM is same as volume in m³
        
        // Update kalkulasi ongkir fields
        $('#total_volume_display').text(FormatUtils.formatNumber(totalVolume));
        $('#total_weight_display').text(FormatUtils.formatNumber(totalGw));
        
        // Update kalkulasi ongkir
        this.updateShippingCostCalculation();
    },
    
    updateShippingCostCalculation() {
        const totalVolume = FormatUtils.parseNumberFromFormatted($('#volume_display').val()) || 0;
        const totalWeight = FormatUtils.parseNumberFromFormatted($('#gw_display').val()) || 0;
        
        // Get pricing from currently selected category (via input fields)
        const priceCbm = parseFloat($('#harga_ongkir_cbm').val()) || 0;
        const priceKg = parseFloat($('#harga_ongkir_wg').val()) || 0;
        
        // Calculate costs
        const volumeCost = totalVolume * priceCbm;
        const weightCost = totalWeight * priceKg;
        
        // Display the costs
        $('#volume_cost_display').text(`Rp ${FormatUtils.formatNumber(volumeCost)}`);
        $('#weight_cost_display').text(`Rp ${FormatUtils.formatNumber(weightCost)}`);
        
        // Get max weight from mitra
        const maxWeight = parseFloat($('#max_weight').val()) || 0;
        
        // Determine calculation method
        let selectedMethod = '';
        let selectedCost = 0;
        
        // Styling for selected method
        $('.bg-blue-50').removeClass('border-blue-500 border-2');
        $('.bg-green-50').removeClass('border-green-500 border-2');
        
        // Automatically determine based on regulation
        if (maxWeight === 0) {
            // If max_weight not set, use the higher cost calculation
            if (volumeCost >= weightCost) {
                selectedMethod = 'volume';
                selectedCost = volumeCost;
                $('.bg-blue-50').addClass('border-blue-500 border-2');
            } else {
                selectedMethod = 'weight';
                selectedCost = weightCost;
                $('.bg-green-50').addClass('border-green-500 border-2');
            }
        } else if (totalWeight <= maxWeight) {
            // If weight is under max, use volume calculation
            selectedMethod = 'volume';
            selectedCost = volumeCost;
            $('.bg-blue-50').addClass('border-blue-500 border-2');
        } else {
            // If weight exceeds max, use weight calculation
            selectedMethod = 'weight';
            selectedCost = weightCost;
            $('.bg-green-50').addClass('border-green-500 border-2');
        }
        
        // Update message and total cost
        let message = '';
        if (selectedMethod === 'volume') {
            message = `<span class="text-blue-700 font-medium">Menggunakan perhitungan Volume</span>: 
                       Total Biaya CBM = <span class="font-bold">Rp ${FormatUtils.formatNumber(volumeCost)}</span><br>`;
            
            if (maxWeight === 0) {
                message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir volume lebih tinggi</span>`;
            } else {
                message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${FormatUtils.formatNumber(totalWeight)} kg masih di bawah batas maksimum ${FormatUtils.formatNumber(maxWeight)} kg</span>`;
            }
        } else {
            message = `<span class="text-green-700 font-medium">Menggunakan perhitungan Berat</span>: 
                       Total Biaya KG = <span class="font-bold">Rp ${FormatUtils.formatNumber(weightCost)}</span><br>`;
            
            if (maxWeight === 0) {
                message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir berat lebih tinggi</span>`;
            } else {
                message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${FormatUtils.formatNumber(totalWeight)} kg melebihi batas maksimum ${FormatUtils.formatNumber(maxWeight)} kg</span>`;
            }
        }
        
        // Update UI elements
        $('#used_calculation_message').html(message);
        $('#selected_shipping_cost').val(FormatUtils.formatNumber(selectedCost));
        $('#calculation_method_used').val(selectedMethod);
        
        // Automatically update shipping cost (no need for apply button)
        $('#jkt_sda').val(FormatUtils.formatNumber(selectedCost));
        
        // Recalculate all fees
        this.calculateFees();
    },
    
    calculateFees() {
        let totalFees = 0;
        
        $('[data-fee="true"]').each(function() {
            totalFees += FormatUtils.parseNumberFromFormatted($(this).val()) || 0;
        });
        
        // Biaya is the total of all fees
        $('#biaya').val(FormatUtils.formatNumber(totalFees));
        
        // Calculate nilai_biaya
        const nilaiBarang = FormatUtils.parseNumberFromFormatted($('#nilai').val()) || 0;
        $('#nilai_biaya').val(FormatUtils.formatNumber(nilaiBarang + totalFees));
        
        // Update biaya kirim
        $('#biaya_kirim').val(FormatUtils.formatNumber(totalFees));
        
        // Update nilai display
        this.updateNilaiDisplay();
        
        // Update PPN and grand total
        this.calculatePPN();
        this.calculateGrandTotal();
    },
    
    calculatePPN() {
        const biayaKirim = FormatUtils.parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
        const ppnRate = parseFloat($('#ppn').val()) || 0;
        const ppnAmount = biayaKirim * (ppnRate / 100);
        
        $('#ppn_total').val(FormatUtils.formatNumber(ppnAmount));
    },
    
    calculateGrandTotal() {
        const biayaKirim = FormatUtils.parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
        const pph = FormatUtils.parseNumberFromFormatted($('#pph').val()) || 0;
        const ppnTotal = FormatUtils.parseNumberFromFormatted($('#ppn_total').val()) || 0;
        
        const grandTotal = biayaKirim + pph + ppnTotal;
        $('#grand_total').val(FormatUtils.formatNumber(grandTotal));
    },
    
    updateNilaiDisplay() {
        const nilaiValue = FormatUtils.parseNumberFromFormatted($('#nilai').val()) || 0;
        $('#nilai_simple').text('Rp ' + FormatUtils.formatNumber(nilaiValue));
        
        this.calculatePPH();
    },
    
    calculatePPH() {
        const pphType = $('#pph').data('tax-type');
        const pphValue = parseFloat($('#pph').data('tax-value')) || 0;
        const nilaiBarang = FormatUtils.parseNumberFromFormatted($('#nilai').val()) || 0;
        
        let pphAmount = 0;
        
        if (pphType === 'percentage') {
            // Calculate as percentage
            if (pphValue > 0 && nilaiBarang > 0) {
                pphAmount = nilaiBarang * (pphValue / 100);
            }
        } else if (pphType === 'fixed') {
            // Use the fixed amount directly
            pphAmount = pphValue;
        }
        
        // Update the PPH field
        $('#pph').val(FormatUtils.formatNumber(pphAmount));
        
        // Update grand total
        this.calculateGrandTotal();
    },
    
    loadTaxData() {
        // Fetch all active taxes
        $.get('/api/taxes', function(response) {
            if (response.success && response.data) {
                const taxes = response.data;
                
                // Look for PPN tax
                const ppnTax = taxes.find(tax => tax.name.toLowerCase().includes('ppn'));
                if (ppnTax) {
                    $('#ppn').val(ppnTax.type === 'percentage' ? ppnTax.value : 0);
                    $('#ppn').closest('div').attr('title', `PPN ${ppnTax.value}% (ID: ${ppnTax.id})`);
                }
                
                // Look for PPH tax
                const pphTax = taxes.find(tax => tax.name.toLowerCase().includes('pph'));
                if (pphTax) {
                    // Store the PPH tax information in data attributes
                    $('#pph').data('tax-type', pphTax.type);
                    $('#pph').data('tax-value', pphTax.value);
                    $('#pph').data('tax-id', pphTax.id);
                    
                    // Update tooltip with tax info
                    let tooltipText = '';
                    if (pphTax.type === 'percentage') {
                        tooltipText = `PPH ${pphTax.value}% (ID: ${pphTax.id})`;
                    } else {
                        tooltipText = `PPH Fixed Rp ${FormatUtils.formatNumber(pphTax.value)} (ID: ${pphTax.id})`;
                    }
                    $('#pph').closest('div').attr('title', tooltipText);
                    
                    // If it's a fixed amount, directly set the value
                    if (pphTax.type === 'fixed') {
                        $('#pph').val(FormatUtils.formatNumber(pphTax.value));
                    } else {
                        // For percentage, we'll calculate it based on nilai
                        CalculationManager.calculatePPH();
                    }
                }
                
                // Recalculate values
                CalculationManager.calculatePPN();
                CalculationManager.calculateGrandTotal();
            }
        }).fail(function(error) {
            console.error('Error loading tax data:', error);
        });
    }
};