<?php
	$page = empty($_REQUEST['page']) ? 'home' : $_REQUEST['page'];

	require 'env.php';
	require 'api.php';	
	require 'content/Page.php';

	if( is_file('pages/'.$page.'.php')  ){		
		require 'pages/'.$page.'.php';

		$object = new $page($_REQUEST);
		$object->show();
	}
	else{
		$object = new Page($_REQUEST);
		$object->set404();
		$object->show();		
	}

	
