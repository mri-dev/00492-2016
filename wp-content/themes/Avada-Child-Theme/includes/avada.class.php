<?php 
class AvadaTemplate 
{
	protected function row_start()
	{
		return '<div class="fusion-row">';
	}

	protected function row_end()
	{
		return '</div>';
	}

	protected function fullrow( $content = '' ) 
	{
		$out = '<div class="fusion-row">
					<div class="fusion-one-full fusion-layout-column fusion-column-last">
						<div class="fusion-column-wrapper">';
							$out .= $content;
		$out .= '</div></div></div>';

		return $out;
	}

	protected function column( $index = 1, $column = 1, $content='', $spacer = 'yes', $last = false )
	{
		
		$indexkeys = array(
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'four',
			5 => 'five',
			6 => 'six',
		);

		$columnkeys = array(
			1 => 'full',
			2 => 'half',
			3 => 'third',
			4 => 'fourth',
			5 => 'fifth',
			6 => 'sixth',
		);

		$out = '<div class="fusion-'.$indexkeys[$index].'-'.$columnkeys[$column].' fusion-layout-column '. ( ($last) ? 'fusion-column-last':'' ) .' fusion-spacing-'.$spacer.'"><div class="fusion-column-wrapper">';
			$out .= $content;
		$out .= '</div></div>';

		return $out;
	}
}