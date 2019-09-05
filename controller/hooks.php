<?php 
class hooks_worker
{
	private $hooks = array();
	
	public function __construct()
	{
		$this->InitialiseHooks();
	}
	
	private function InitialiseHooks()
	{
		$this->hooks = array(
			array(
				"hook_name"=>"%display_course_name%",
				"hook_value"=>(function_exists("get_course_name")) ? get_course_name() : "",
				"hook_description"=>"В случае успеха отобразит имя курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
			array(
				"hook_name"=>"%display_course_total%",
				"hook_value"=>(function_exists("get_course_total")) ? get_course_total() : "",
				"hook_description"=>"В случае успеха отобразит кол-во занятий искомого курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
			array(
				"hook_name"=>"%display_course_price%",
				"hook_value"=>(function_exists("get_course_price")) ? get_course_price() : "",
				"hook_description"=>"В случае успеха отобразит стоимость одного занятия искомого курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
			array(
				"hook_name"=>"%display_course_full_price%",
				"hook_value"=>(function_exists("get_course_full_price")) ? get_course_full_price() : "",
				"hook_description"=>"В случае успеха отобразит стоимость полную стоимость курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
			array(
				"hook_name"=>"%display_course_quarter_price%",
				"hook_value"=>(function_exists("get_course_quater_price")) ? get_course_quater_price() : "",
				"hook_description"=>"В случае успеха отобразит стоимость стоимость 1/4 курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
			array(
				"hook_name"=>"%display_course_half_price%",
				"hook_value"=>(function_exists("get_course_half_price")) ? get_course_half_price() : "",
				"hook_description"=>"В случае успеха отобразит стоимость стоимость 1/2 курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
				"hook_status"=>"1"
			),
            array(
                "hook_name"=>"%display_course_lesson_price%",
                "hook_value"=>(function_exists("get_course_price_for_one_lesson")) ? get_course_price_for_one_lesson() : "",
                "hook_description"=>"В случае успеха отобразит стоимость стоимость 1 пары(при условии оплаты по одной паре), в случае неудачи будет выведена пустая строка, ошибки выводится не будут.",
                "hook_status"=>"1"
            ),
            array(
                "hook_name"=>"%display_course_oneeight_price%",
                "hook_value"=>(function_exists("get_course_oneeight_price")) ? get_course_oneeight_price() : "",
                "hook_description\"=>\"В случае успеха отобразит стоимость стоимость 1/8 курса, в случае неудачи будет выведена пустая строка, ошибки выводится не будут.\",
                \"hook_status"=>"1"
            )
		);
	}
	
	public function hook_controller($type)
	{
		if($type == "decode")
		{
			return $this->decode_hooks();
		}
		if($type == 'get')
        {
            return $this->get_hooks_view_parametrs_list_for_admin_panel();
        }
	}
	
	private function decode_hooks()
	{
		$string = get_output_db_table_for_decode_core();
		if(is_array($string))
		{
			return 0;
		}
		foreach($this->hooks as $key)
		{
			if(preg_match("/".$key["hook_name"]."/", $string, $out))
			{
				$string = str_ireplace($key["hook_name"], $key["hook_value"], $string);
			}
		}
		return $string;
	}
	
	private function get_hooks_view_parametrs_list_for_admin_panel()
	{
		return $this->hooks;
	}
}