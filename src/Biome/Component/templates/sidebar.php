<ul class="nav navbar-nav side-nav navbar-ex1-collapse"><?php

function generateMenu(array $menu_list)
{
	foreach($menu_list AS $menu)
	{
		$url		= isset($menu['url']) ? $menu['url'] : '#';
		$icon		= isset($menu['icon']) ? $menu['icon'] : NULL;
		$title		= isset($menu['title']) ? $menu['title'] : '';
		$class		= isset($menu['class']) ? $menu['class'] : '';
		$submenu 	= isset($menu['submenu']) ? $menu['submenu'] : NULL;

		echo '<li', (empty($class) ? '' : ' class="' . $class . '"') . '>';

		if(empty($submenu))
		{
			echo '<a href="', $url, '">';
			if(!empty($icon))
			{
				echo '<i class="', $icon, '"></i>';
			}
			echo ' ', $title, '</a>';
		}
		else
		{
			echo '<a href="javascript:;" data-toggle="collapse" data-target="#demo">';
			if(!empty($icon))
			{
				echo '<i class="', $icon, '"></i>';
			}

			echo ' ', $title, '<i class="fa fa-fw fa-caret-down"></i></a>';
			echo '</a>';
			echo '<ul id="demo" class="collapse">';
			generateMenu($submenu);
			echo '</ul>';
		}

		echo '</li>';
	}
}


$menu_list = $this->getValue();

generateMenu($menu_list);

?></ul>