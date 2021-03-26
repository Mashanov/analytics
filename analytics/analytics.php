<?php

	// Система аналитики
	// Специально для разработчиков игр и приложений в VK
	// Разработчик в VK https://vk.com/mashanovdanil

	class analytics {

		public function new_object ($name = false, $type = false, $about = false)
		{
			
			if ($name == false || $type == false || $about == false) return 'Не заданы параметры';
			
			if ($type != 'счет' && $type != 'ср' && $type != 'график') return 'Неизвестный тип';
		
			$file_name = $_SERVER['DOCUMENT_ROOT'].'/analytics/data.txt';
			
			$file_open = fopen ($file_name, 'r');
			$code = fread ($file_open, filesize ($file_name));
			$code = json_decode($code, true);
			fclose ($file_open);

			if (count($code) > 0) foreach ($code as $i) if ($i['name'] == $name) return 'Имя уже занято';
			
			$array = array ('name' => $name, 'type' => $type, 'data' => 'undefined', 'about' => $about);
			array_push ($code, $array);
			$code = json_encode($code);
			
			$file = fopen($file_name, 'w');
			fwrite($file, $code);
			fclose ($file);
			
			return 'success';
		}
		
		public function delete_object ($name = false)
		{
			
			if ($name == false) return 'Не ввели имя';
		
			$file_name = $_SERVER['DOCUMENT_ROOT'].'/analytics/data.txt';
			
			$file_open = fopen ($file_name, 'r');
			$code = fread ($file_open, filesize ($file_name));
			$code = json_decode($code, true);
			fclose ($file_open);
			
			$data = 'undefined';
			
			foreach ($code as $i)
			{
				
				if ($i['name'] == $name)
				{
					
					$data = $i;
					break;
				}
			}
			
			if ($data ==  'undefined') return 'Нет объекта';
			
			unset ($code[array_keys($code, $data)[0]]);
			$code = json_encode($code);
			
			$file = fopen($file_name, 'w');
			fwrite($file, $code);
			fclose ($file);
			
			return 'success';
		}
		
		public function data_object ($name = false, $ob = false)
		{
			
			if ($name == false || $ob == false) return 'Не заданы параметры';
		
			$file_name = $_SERVER['DOCUMENT_ROOT'].'/analytics/data.txt';
			
			$file_open = fopen ($file_name, 'r');
			$code = fread ($file_open, filesize ($file_name));
			$code = json_decode($code, true);
			fclose ($file_open);
			
			$data = 'undefined';
			
			foreach ($code as $i)
			{
				
				if ($i['name'] == $name)
				{
					
					$data = $i;
					break;
				}
			}
			
			if ($data ==  'undefined') return 'Нет объекта';
			$key = array_keys($code, $data)[0];
			
			switch ($data['type'])
			{
				
				case 'счет':
					if (!is_array($ob)) return 'Не передали массив данных';
					
					if ($ob[0] != '+' && $ob[0] != '-') return 'Не передали идентификатор';
					if (!is_numeric($ob[1])) return 'Не передали число';
					
					if ($data['data'] == 'undefined') $data['data'] = 0;
					
					$ob[0] == '+' ? $data['data'] += $ob[1] : $data['data'] -= $ob[1];
					
					break;
					
				case 'ср':
					if (!is_numeric($ob)) return 'Не передали число';
					
					$data['data'] == 'undefined' ? $data['data'] = [$ob, 1] : $data['data'] = [$data['data'][0] + $ob, $data['data'][1] + 1];
					
					break;
					
				case 'график':
					if (!is_array($ob)) return 'Не передали массив данных';
					if (!is_numeric($ob[1]) || !is_numeric($ob[0])) return 'Не передали число';
					
					if ($data['data'] == 'undefined') $data['data'] = [];
					array_push ($data['data'], $ob);
					
					break;
			}
			
			$code[$key] = $data;
			$code = json_encode($code);
			
			$file = fopen($file_name, 'w');
			fwrite($file, $code);
			fclose ($file);
			
			return 'success';
		}
		
		public function get ()
		{
			
			$file_name = $_SERVER['DOCUMENT_ROOT'].'/analytics/data.txt';
			
			$file_open = fopen ($file_name, 'r');
			$code = fread ($file_open, filesize ($file_name));
			$code = json_decode($code, true);
			fclose ($file_open);
			
			$text = '<style>
				logo {
				position: relative;
				font-size: 300%;
				display: flex;
				margin: 50px 25px;
				}

				.block {
				position: relative;
				padding: 10px;
				border: 1px solid #d4dbe6;
				width: fit-content;
				height: fit-content;
				margin: 10px;
				font-family: arial;
				font-size: 100%;
				max-width: 170px;
				display: inline-block;
				float: left;
				}

				.name {
				font-size: 130%;
				margin-bottom: 10px;
				min-width: 100px;
				text-align: center;
				}

				.about {
				min-width: 100px;
				text-align: center;
				color: #888;
				}

				.number {
				font-size: 300%;
				width: 100%;
				text-align: center;
				box-sizing: border-box;
				position: relative;
				color: #0037ff;
				}
				</style>
				<script>var d = document;
				function create_analytics (

					array_left = false,
					array_bottom = false,
					array_data = false,
					array_id = false
				){

					var max_left = array_left,
					max_left_pr = max_left / 100,
					max_bottom = array_bottom,
					max_bottom_pr = max_bottom / 100;

					var can = d.querySelector(`canvas[id="` + array_id + `"]`),
					ctx = can.getContext("2d");

					can.width = max_left;
					can.height = max_bottom;

					ctx.strokeStyle = "#bfc6d6cc";

					var bi = max_left / 5;

					ctx.beginPath ();
					ctx.lineTo(bi, 0);
					ctx.lineTo(bi, max_bottom);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(bi * 2, 0);
					ctx.lineTo(bi * 2, max_bottom);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(bi * 3, 0);
					ctx.lineTo(bi * 3, max_bottom);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(bi * 4, 0);
					ctx.lineTo(bi * 4, max_bottom);
					ctx.stroke ();

					var bi = max_bottom / 5;

					ctx.beginPath ();
					ctx.lineTo(0, bi);
					ctx.lineTo(max_left, bi);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(0, bi * 2);
					ctx.lineTo(max_left, bi * 2);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(0, bi * 3);
					ctx.lineTo(max_left, bi * 3);
					ctx.stroke ();

					ctx.beginPath ();
					ctx.lineTo(0, bi * 4);
					ctx.lineTo(max_left, bi * 4);
					ctx.stroke ();

					ctx.strokeStyle = "#0037ff";

					ctx.beginPath ();

					for (var t of array_data)
					{

						var explode = t.split(" ");
						ctx.lineTo(explode[0], explode[1]);
					};

					ctx.stroke ();
				};
				</script>
				<logo>Analytics system</logo>';
				
				if (count($code) > 0)
				{
				
					foreach ($code as $i)
					{
						
						switch ($i['type'])
						{
							
							case 'счет':
								if ($i['data'] == 'undefined') $i['data'] = 0;
								
								$text = $text.'<div class="block">
									<div class="name">'.$i['name'].'</div>
									<div class="number">'.$i['data'].'</div>
									<div class="about">'.$i['about'].'</div>
								</div>';
								
								break;
								
							case 'ср':
								if ($i['data'] == 'undefined') $i['data'] = [0, 1];
								
								$text = $text.'<div class="block">
									<div class="name">'.$i['name'].'</div>
									<div class="number">'.($i['data'][0] / $i['data'][1]).'</div>
									<div class="about">'.$i['about'].'</div>
								</div>';
								
								break;
								
							case 'график':
								if ($i['data'] != 'undefined')
								{
									
									$x = '[';
								
									foreach ($i['data'] as $q)
									{
										
										$q[0] = $q[0] * 4.5;
										$q[1] = (100 - $q[1]) * 3;
									
										$x = $x.'"'.$q[0].' '.$q[1].'",';
									}
									
									$text = $text.'<div class="block" style="max-width: fit-content;">
										<div class="name">'.$i['name'].'</div>
										<canvas id="'.$i['name'].'"></canvas>
										<div class="about">'.$i['about'].'</div>
									</div>
									<script>create_analytics ("450", "300",'.$x.'],"'.$i['name'].'");</script>';
								}
								
								break;
						}
					}
				}
			
			return $text;
		}
	}

	$it = new analytics ();