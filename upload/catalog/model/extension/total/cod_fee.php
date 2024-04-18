<?php
class ModelExtensionTotalCodfee extends Model {
    public function getTotal($total) {
        $this->load->language('extension/total/cod_fee');

        if($this->cart->getSubTotal()) {
            $sub_total = $this->cart->getSubTotal();
        } elseif ($total) {
            $sub_total = $total;
        } else {
            $sub_total = 0;
        }

        if(isset($this->session->data['payment_method']['code'])) {
            $payment_method = $this->session->data['payment_method']['code'];
        } else {
            $payment_method = '';
        }



        if ((($payment_method && ($payment_method == 'cod')) && ($sub_total > $this->config->get('cod_total')) ) && ($sub_total > 0)) {
            $total['totals'][] = array(
                'code'       => 'cod_fee',
                'title'      => $this->language->get('text_cod_fee'),
                'value'      => $this->config->get('total_cod_fee_fee'),
                'sort_order' => $this->config->get('total_cod_fee_sort_order')
            );

            if ($this->config->get('total_cod_fee_tax_class_id')) {
                $tax_rates = $this->tax->getRates($this->config->get('total_cod_fee_fee'), $this->config->get('total_cod_fee_tax_class_id'));
                foreach ($tax_rates as $tax_rate) {
                    if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
                        $total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
                    } else {
                        $total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
                    }
                }
            }


            $total['total'] += $this->config->get('total_cod_fee_fee');

        }
    }
}