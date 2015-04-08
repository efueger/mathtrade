<?php
/* {{#each wildcards}}
			({{../user}}): {{name}} : {{#each items}}{{id}} {{/each}}
			{{/each}} */ 

// web/index.php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'core.mordamir.com',
        'dbname'    => 'mathtrade',
        'user'      => 'mathtrade',
        'password'  => 'getthejobdone',
        'charset'   => 'utf8',
    )
));


// ... definitions
$app->get('/', function (Silex\Application $app) {
	 return $app['twig']->render('index.twig', array(
        'items' => file_get_contents('mtitems.data')
    ));
});

//Returns all the items
$app->get('/rest/items', function (Silex\Application $app) {
	$sql = "SELECT * FROM items";
    $post = $app['db']->fetchAll($sql);
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});

$app->get('/rest/items/{id}', function ($id)  use($app){
	$sql = "SELECT * FROM items WHERE id = ?";
    $post = $app['db']->fetchAll($sql,array($id));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
    	$ids[] = $i['id'];
    }
    $sql = "SELECT w.*,i.name FROM wantlist w INNER JOIN items i ON type =1 AND w.target_id = i.item_id WHERE w.item_id IN (?) ORDER BY pos ASC";
    $want = $app['db']->fetchAll($sql,
    array($ids),
    array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
    //echo $sql;

    foreach ($post as $key => &$p) {
    	foreach ($want as $j => $w) {
    		if ($w['item_id'] == $p['item_id']) {
    			$w['id'] = $w['target_id'];
    			$p['wantlist'][] = $w;
    			unset($want[$j]);
    		}
    	}
    }
    
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});

$app->get('/rest/itemsbyuser/{user}', function ($user) use($app) {
	$sql = "SELECT * FROM items where username = ?";
    $post = $app['db']->fetchAll($sql,array($user));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
    	$ids[] = $i['id'];
    }
    $sql = "SELECT w.*,i.name FROM wantlist w INNER JOIN items i ON type =1 AND w.target_id = i.item_id WHERE w.item_id IN (?) ORDER BY pos ASC";
    $want = $app['db']->fetchAll($sql,
    array($ids),
    array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
    //echo $sql;

    foreach ($post as $key => &$p) {
    	foreach ($want as $j => $w) {
    		if ($w['item_id'] == $p['item_id']) {
    			$w['id'] = $w['target_id'];
    			$p['wantlist'][] = $w;
    			unset($want[$j]);
    		}
    	}
    }


    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});


$app->get('/rest/useritems/{user}', function ($user) use ($app) {
	$sql = "SELECT i.* FROM user_items ui INNER JOIN items i ON ui.item_id = i.id WHERE user_id = ?";
    $post = $app['db']->fetchAll($sql,array((int)$user));
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});


$app->post('/rest/useritems/{user}', function ($user,Request $request) use ($app) {
	$sql = "SELECT * FROM user_items WHERE user_id = ?";
    $post = $app['db']->insert('user_items',array(
    	'user_id'=>$user,
    	'item_id'=>$request->get('id'),
    	'type'=>$request->get('type')

    	));
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});

$app->get('/rest/userwantlist/{user}', function ($user)  use($app){
	$sql = "SELECT * FROM wantlist WHERE user_id = ?";
    $post = $app['db']->fetchAll($sql,array($user));
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});

$app->post('/rest/wantlist/{id}', function ($id,Request $request) use ($app) {
	
	print_r($request->request);
	$d = json_decode($request->get('d'));
	$wantid = $request->get('wid');
	echo "id $wantid";
	if (is_numeric($id)) {
		$app['db']->delete('wantlist',array('item_id'=>$wantid));
	}

	//Now prepare to insert the items / wildcards
	foreach ($d as $pos=>$i) {
		$app['db']->insert('wantlist',array(
			'item_id'=>$wantid,
			'user_id'=>1,
			'target_id'=>$i->id,
			'type'=>$i->t,
			'pos'=>$pos
		));
	}

	print_r($d);
    return new Response(json_encode($d),200,array('Content-Type'=>'application/json'));
});

$app->get('api/collection', function(Request $request) use ($app) {
	$post = array();
	$csv = new CsvIterator('mt.csv');
	foreach ($csv->parse() as $row) {
		$post[]=$row;
	}


	return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});

$app->get('/mt', function (Silex\Application $app) {
	$items = array();
	do {

		$url = isset($pages[1])?$pages[1]:'http://labsk.net/index.php?topic=151319.0';
		file_put_contents('test.html', file_get_contents($url));
		$html = file_get_contents('test.html');
		$string = preg_replace('/\n/', '', $html);

		preg_match('/"forumposts">(.*)<a id="lastPost/', $string,$match);


		//ini_set('display_errors',0);
		// die();
		preg_match_all('/post_wrapper">(.*?)class="botslice"/', $match[1], $posts);

		if (!isset($pages))
			unset($posts[1][0]);

		//Get pagination
		preg_match('/<a class="navPages" href="([^"]*?)">>><\/a>/', $string,$pages);

		foreach ( array_slice($posts[1],0)  as $post) {
			$dom = new DOMDocument();
			ini_set('display_errors',0);
			$dom->loadHTML('<div>'.$post.'></span>');
			ini_set('display_errors',1);
			$xpath = new DomXpath($dom);
			$innerpost = $xpath->query('//*[@class="inner"]');



			foreach ($innerpost as $el) {

				$nodes = $el->childNodes;
			    foreach ($nodes as $node) {
			    	if ($node->nodeName != 'table') continue;

			    	//Skip tablebody
			    	$gamelist = $node->childNodes;


			    	//Check if it's a group
			    	if ($gamelist->item(0)->childNodes->item(0)->childNodes->item(0)->nodeName == 'strong') {

			    		foreach ($gamelist as $id=>$game) {
			    			if($id % 2 == 0 ) {
			    				$Group = array();
				    			foreach ($game->childNodes->item(0)->childNodes as $i=>$grgame) {
									if ($i<2) continue;
				    				if ($grgame->nodeName == 'a' ) {
				    					if (strpos($grgame->nodeValue, '[')===false) {

					    					$GI = new stdClass();
					    					$GI->name =$grgame->nodeValue;
						    				$Group[] = $GI;
				    					}
				    				}
				    				else {
				    					$Group[count($Group)-1]->description = $grgame->nodeValue;
				    				}
				    			}
			    			}
			    			if($id % 2 ==1) {
				    			foreach ($game->childNodes->item(0)->childNodes as $i => $grgame) {
				    				if ($grgame->nodeName == 'a' ) {
				    					$Group[$i]->bgg_url =$grgame->getAttribute('href');
					    				$Group[$i]->bgg_img =$grgame->childNodes->item(0)->getAttribute('src');

				    				}
				    			}
				    			$items[] = $Group;
			    			}
				    	}

			    	}
			    	else
				    	foreach ($gamelist as $game) {
				    		$G = new stdClass();

				    		if (!$game->childNodes->item(0)->childNodes->item(0)) continue;
					    	if ($game->childNodes->item(0)->childNodes->item(0) instanceof DOMText)continue;
						$G->bgg_url = $game->childNodes->item(0)->childNodes->item(0)->getAttribute('href');
						  if ($game->childNodes->item(0)->childNodes->item(0)->childNodes->item(0) instanceof DOMText)continue;
						if ($game->childNodes->item(0)->childNodes->item(0)->childNodes->item(0))
								$G->bgg_img = $game->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->getAttribute('src');
						//	else continue;

				    		//Second Columm table

				    		//if ( count($game->childNodes) <2) continue;
				    		//if (!$game->childNodes->item(1))continue;
						if(count($game->childNodes->item(1)->childNodes)>0){

						$col2 = $game->childNodes->item(1)->childNodes->item(0);
				    		$G->name = $col2->childNodes->item(0)->nodeValue;
				    		$G->description = '';
				    		foreach ($col2->childNodes as $i => $row) {
				    			if ($i == 0) continue;
				    			$G->description .= $row->nodeValue;
				    		}
				    		if ($G->name != '') {
				    			$items[] = $G;
				    		}
								}
				    	}
			    }
			}
		}
		$max--;
	}
	while (!empty($pages[1]));
//	unset($items[55][2]->description);
	echo "<pre>";
	print_r($items);
	echo "</pre>";
	file_put_contents('mtitems.data', str_replace('"','\\"',json_encode($items,JSON_HEX_APOS)));

	return;
	//Let's get all the items offered
	preg_match_all('/<tr>(.*?)<\/tr>/', $posts[1][1], $games);
	print_r($games[1]);


    // return $app['twig']->render('index.twig', array(
    //     'name' => 'edgard',
    // ));
    //return "ed";
});


$app->post('/', function (Silex\Application $app) {
   // return $app['twig']->render('index.twig', array(
   //      'name' => 'edgard',
   //  ));
   	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	/*
	// Support CORS
	header("Access-Control-Allow-Origin: *");
	// other CORS headers if any...
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		exit; // finish preflight CORS requests here
	}
	*/
	// 5 minutes execution time
	@set_time_limit(5 * 60);
	// Uncomment this one to fake upload time
	// usleep(5000);
	// Settings
	//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
	$targetDir = __DIR__.'/uploads';
	$cleanupTargetDir = false; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds
	// Create target dir
	if (!file_exists($targetDir)) {
		@mkdir($targetDir);
	}
	// Get a file name
	if (isset($_REQUEST["name"])) {
		$fileName = $_REQUEST["name"];
	} elseif (!empty($_FILES)) {
		$fileName = $_FILES["file"]["name"];
	} else {
		$fileName = uniqid("file_");
	}
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

	// Chunking might be enabled
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

	// Open temp file
	if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}
	if (!empty($_FILES)) {
		if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}
		// Read binary input stream and append it to temp file
		if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	} else {
		if (!$in = @fopen("php://input", "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	}
	while ($buff = fread($in, 4096)) {
		fwrite($out, $buff);
	}
	@fclose($out);
	@fclose($in);
	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off
		rename("{$filePath}.part", $filePath);
	}
	// Return Success JSON-RPC response
	return('{"jsonrpc" : "2.0", "file" :"'.$fileName.'", "id" : "id"}');
});




class CsvIterator
{
    protected $file;

    public function __construct($file) {
        $this->file = fopen($file, 'r');
    }

    public function parse() {
        $headers = array_map('trim', fgetcsv($this->file, 4096));
        $rows = array();
        while (!feof($this->file)) {
            $row = array_map('trim', (array)fgetcsv($this->file, 4096));
            if (count($headers) !== count($row)) {
                continue;
            }
            $row = array_combine($headers, $row);
            array_push($rows, $row);
        }
        return $rows;
    }
}

$app->run();
