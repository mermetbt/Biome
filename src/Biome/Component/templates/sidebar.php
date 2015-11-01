<ul class="nav navbar-nav side-nav navbar-ex1-collapse"><?php

function prepareMenu(array &$menu_list)
{
	$is_active = FALSE;
	$rights = \Biome\Biome::getService('rights');

	foreach($menu_list AS $index => &$menu)
	{
		$url				= isset($menu['url']) ? $menu['url'] : '';
		$menu['class']		= isset($menu['class']) ? $menu['class'] : '';
		$menu['subclass']	= isset($menu['subclass']) ? $menu['subclass'] : '';

		/**
		 * Check if the menu is allowed.
		 */
		if(!empty($url) && !$rights->isUrlAllowed('GET', $url))
		{
			unset($menu_list[$index]);
			continue;
		}

		if(URL::matchRequest($url))
		{
			$menu['class'] .= 'active';
			$is_active = TRUE;
		}

		if(!empty($menu['submenu']))
		{
			if(prepareMenu($menu['submenu']))
			{
				$menu['subclass'] .= 'in';
				$is_active = TRUE;
			}

			/* Remove the menu if no other menu is inside. */
			if(empty($menu['submenu']))
			{
				unset($menu_list[$index]);
			}
		}
	}
	unset($menu);
	return $is_active;
}

function generateMenu(array $menu_list)
{
	foreach($menu_list AS $menu)
	{
		$url		= isset($menu['url']) ? $menu['url'] : '';
		$icon		= isset($menu['icon']) ? $menu['icon'] : NULL;
		$title		= isset($menu['title']) ? $menu['title'] : '';
		$class		= isset($menu['class']) ? $menu['class'] : '';
		$subclass	= isset($menu['subclass']) ? $menu['subclass'] : '';
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
			$target = uniqid();
			echo '<a href="javascript:;" data-toggle="collapse" data-target="#', $target, '">';
			if(!empty($icon))
			{
				echo '<i class="', $icon, '"></i>';
			}

			echo ' ', $title, '<i class="fa fa-fw fa-caret-down"></i></a>';
			echo '</a>';
			echo '<ul id="', $target, '" class="navbar-nav collapse', (!empty($subclass) ? ' ' . $subclass : '') ,'">';
			generateMenu($submenu);
			echo '</ul>';
		}

		echo '</li>';
	}
}

$menu_list = $this->getValue();

prepareMenu($menu_list);

generateMenu($menu_list);

?></ul>
