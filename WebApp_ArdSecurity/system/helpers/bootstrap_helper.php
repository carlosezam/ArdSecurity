<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
