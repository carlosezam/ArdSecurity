<?php
function bs_is_invalid($field = '')
{
    if( form_error($field) )
    {
        return 'is-invalid';
    }
    else
    {
        return '';
    }

}

function fa_ico( $ico )
{
    return '<i class="fa fa-'.$ico.'"></i>';
}



function bs_form_group( $config, $layaout = array() )
{

    $config['type'] = isset( $config['type'] ) ? $config['type'] : 'text';
    $config['hint'] = isset( $config['hint'] ) ? $config['hint'] : '';
    $layaout['label'] = isset( $layaout['label'] ) ? $layaout['label'] : 'col-sm-2';
    $layaout['input'] = isset( $layaout['input'] ) ? $layaout['input'] : 'col-sm-2';

    $html_input = form_input( array(
        'class' => 'form-control '.bs_is_invalid( $config['field'] ),
        'type' => $config['type'],
        'name' => $config['field'],
        'value' => set_value( $config['field'] ),
        'placeholder' => $config['hint']
    ));

    $html_addon = isset($config['addon']) ? '<div class="input-group-addon">'.$config['addon'].'</div>' : '';
    $html_small = isset($config['small']) ? '<small class="form-text text-muted">'.$config['small'].'</small>' : '';

    $html_error = form_error( $config['field'] );
    $html_input_group = '<div class="input-group">'. $html_input . $html_addon. '</div>';

    $html_col_label = '<label class="'.$layaout['label'].' col-form-label">'.$config['label'].'</label>';
    $html_col_input = '<div class="' . $layaout['input'] .'">'.
                                $html_input_group.$html_small.$html_error.
                            '</div>';


    return '<div class="form-group row">'. $html_col_label.$html_col_input . '</div>';
}