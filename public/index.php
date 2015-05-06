<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

const returnCodeOK = 200;
const USER_NOT_FOUND = 520;
const SALT = '9ywmLatNHWuJJMH7k7LX';
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
$app['debug'] = true;

//Register TWig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

//Register Sessions
$app->register(new Silex\Provider\SessionServiceProvider());


if ($_SERVER['SERVER_NAME'] == 'mt.dev') {
	$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	    'db.options' => array(
	        'driver'    => 'pdo_mysql',
	        'host'      => 'localhost',
	        'dbname'    => 'mathtrade',
	        'user'      => 'root',
	        'password'  => '',
	        'charset'   => 'utf8',
	    )
	));
}
else {
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
}


function getUser($hash)
{
	global $app;
	//Get the user
	$sql = "SELECT * FROM users WHERE hash = ?";
	$user = $app['db']->fetchAll($sql,array($hash));
	$user = $user[0];
	return $user;	
}

function getWantUser($user)
{
	global $app;
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

    foreach ($post as $key => &$p) {
    	foreach ($want as $j => $w) {
    		if ($w['item_id'] == $p['item_id']) {
    			$w['id'] = $w['target_id'];
    			$p['wantlist'][] = $w;
    			unset($want[$j]);
    		}
    	}
    }
    return $post;
}


function logged()
{
	global $app;
	if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/public/signin');
    }
    return $user;
}

// ... definitions


$app->get('/landing', function (Silex\Application $app) {
	$items =file_get_contents('mtitems.data');
	$its = json_decode(str_replace("\\\"", '"', $items));

	//$its = array_slice($its, 9,1);

	/*foreach ($its as $i) {	
		//print_r($i);
		
		if (is_array($i)) {
	// 		$sql = "SELECT * FROM items WHERE  name LIKE '%".addslashes($i[0]->name)."%' AND name LIKE '%".addslashes($i[1]->name)."%'";
	// 		echo $sql;
	// 		$user = $app['db']->fetchAll($sql);
	// 		echo "<pre>";
	// //print_r($user);
	// echo "</pre>";
			continue;
			//die();

		}
		$sql = "SELECT * FROM items WHERE  name LIKE '".addslashes($i->name)."%' ";
		$user = $app['db']->fetchAll($sql);
		if($user) {

			$app['db']->update('items',array('bgg_img'=>$i->bgg_img),array('id'=>$user[0]['id']));
		}

	}

	echo "<pre>";
	print_r($user);
	echo "</pre>";

	die();*/

	 return $app['twig']->render('index.twig', array(
        'items' => $items,
		'useritems' => array(),
        'wants' => array(),
        'wildcards' => array(),
        //'hash' => $hash
    ));
});


/**
 * Display the Mathtrade Landing
 */
$app->get('/', function (Silex\Application $app) {
	return $app['twig']->render('landing.twig');
});


/**
 * Display the signin page
 */
$app->match('/signin', function (Silex\Application $app) {
	//Get post params..
	$r = $app['request']->request->all();
	if (count($r)>0) {

		$sql = "SELECT * FROM accounts WHERE username = ?";
		$user = $app['db']->fetchAll($sql,array($r['user']));
		if (count($user) > 0) {
			$user = $user[0];

			//User logged!!
			if ($user['password'] === md5($r['pwd'].SALT)) {
				unset($user['password']);
				$app['session']->set('user', $user);
        			return $app->redirect('/public/home');

			}
		}
		return $app->redirect('/public/signin?error=1');
	}


	return $app['twig']->render('signin.twig');
})->method('GET|POST');


//The user registers in the MTH
$app->post('/register', function (Silex\Application $app) {
	//Get post params..
	$r = $app['request']->request->all();
	if (count($r)>0) {

		$sql = "SELECT * FROM accounts WHERE username = ? OR email = ?";
		$user = $app['db']->fetchAll($sql,array($r['user'],$r['email']));
		if (count($user) > 0) {
			return $app->redirect('/public/signin?error=2');
		}
		
		//Not used go ahead
		$app['db']->insert('accounts',array(
			'username'		=>$r['user'],
	    	'password'		=>md5($r['pwd'].SALT),
	    	'email'			=>$r['email'],
	    	'created'		=>date('Y-m-d H:i:s')
		));
		return $app->redirect('/public/signin?success=1');
	}
	return $app->redirect('/public/signin?error=3');
});

$app->get('/logout',function (Silex\Application $app) {
	$app['session']->clear();
	return $app->redirect('/public/');
});


/**
 * Home of the app
 */
$app->get('/home',function (Silex\Application $app) {
	$user = logged();
	if (!is_array($user)) return $user;


	$games = $app['db']->fetchAll('SELECT * FROM newitems WHERE account_id = ?',array($user['id']));


	return $app['twig']->render('home.twig',array(
		'user' => $user,
		'games'=> str_replace('"','\\"',json_encode($games,JSON_HEX_APOS)),
	));
});

/**
 * Home of the app
 */
$app->get('/bggimport',function (Silex\Application $app) {
	$user = logged();
	if (!is_array($user)) return $user;

	return $app['twig']->render('bggimport.twig',array(
		'user' => $user,
	));
});

$app->get('/bggimport/get',function (Silex\Application $app) {
	$user = logged();
	if (!is_array($user)) return $user;
	$xml = simplexml_load_file('http://boardgamegeek.com/xmlapi2/collection?username='.$user['bgg_user'].'&trade=1');
	$json = json_encode($xml);
	$array = json_decode($json,true);
	if (!$array['item']) {
		$xml = simplexml_load_file('http://boardgamegeek.com/xmlapi2/collection?username='.$user['bgg_user'].'&trade=1');
		$json = json_encode($xml);
		$array = json_decode($json,true);
	}
	// file_put_contents('bgg.txt', $json);

	//$array = json_decode(file_get_contents('bgg.txt'),TRUE);
	$games = array();

	foreach ($array['item'] as $g) {
		//print_r($g);
		$ng = array();
		$ng['name'] = $g['name'];
		$ng['bgg_img'] = $g['thumbnail'];
		$ng['description'] = $g['conditiontext'];
		$ng['bgg_id'] = $g['@attributes']['objectid'];
		$ng['collid'] = $g['@attributes']['collid'];
		$games[] = $ng;
	}


	// echo "<pre>";
	// print_r($games);
	// print_r($array);
	// echo "</pre>";

	return new Response(json_encode($games),200,array('Content-Type'=>'application/json'));
});


//Allows adding games 
$app->post('/bggimport/add',function (Silex\Application $app) {
	$user = logged();
	if (!is_array($user)) return $user;

	$games = $app['request']->request->get('data');

	$games = json_decode($games,true);

	foreach ($games as $g) {
		$already = $app['db']->fetchAll('SELECT id FROM newitems WHERE account_id = ? AND collid = ?',array($user['id'],$g['collid']));

		if ( count($already) >0) continue;

		$app['db']->insert('newitems',array(
	    	'account_id'=>$user['id'],
	    	'name'=>$g['name'],
	    	'description'=>$g['description'],
	    	'bgg_id'=>$g['bgg_id'],
	    	'bgg_img'=>$g['bgg_img'],
	    	'collid'=>$g['collid'],

	    ));
	}
	


	return new Response(json_encode($games),200,array('Content-Type'=>'application/json'));
});


/**
 * This is the base url of a user identified by it's hash
 */
$app->get('/{hash}', function ($hash)  use($app){

	$wants = array();
	
	//Get the user
	$user = getUser($hash);

	//Items selected by user either 
	$sql = "SELECT i.*,ui.type FROM user_items ui 
			INNER JOIN items i ON ui.item_id = i.item_id
			WHERE ui.user_id = ?";
	$items = $app['db']->fetchAll($sql,array($user['id']));

	//Items on the pending list
	$sql = "SELECT i.* FROM items i
			LEFT JOIN user_items ui ON i.item_id = ui.item_id AND ui.user_id = ?
			WHERE ui.id IS NULL";
	$pending = $app['db']->fetchAll($sql,array($user['id']));

	$wants = getWantUser($user['id']);


	$sql = "SELECT w.id as wid,w.name as wildname,i.* FROM wildcard w
			LEFT JOIN wildcarditems wi ON w.id = wi.wildcard_id
			LEFT JOIN items i ON wi.item_id = i.item_id
			WHERE w.user_id = ? ORDER by pos ASC";
	$dirty = $app['db']->fetchAll($sql,array($user['id']));

	$wildcards = array();
	foreach ($dirty as $w) {
		if (!isset($wildcards[$w['wid']])){
			$wildcards[$w['wid']] = array('id'=>$w['wid'],'name'=>$w['wildname'],'wantid'=>'%'.$w['wildname'],'items'=>array());
		}
		if (isset($w['item_id']))
			$wildcards[$w['wid']]['items'][] = array(
				'id'=>$w['item_id'],
				'item_id'=>$w['item_id'],
				'name'=>$w['name'],
				
			);
	}
	$wildcards = array_values($wildcards);
	//print_r($wildcards);


	return $app['twig']->render('index.twig', array(
        'items' => str_replace('"','\\"',json_encode($pending,JSON_HEX_APOS)),
        'useritems' => str_replace('"','\\"',json_encode($items,JSON_HEX_APOS)),
        'wants' => str_replace('"','\\"',json_encode($wants,JSON_HEX_APOS)),
        'wildcards' => str_replace('"','\\"',json_encode($wildcards,JSON_HEX_APOS)),
        'hash' => $hash
    ));
});

/**
 * Import the results from the file
 */
$app->get('/import/results',function (Silex\Application $app) {
	$results = file_get_contents('mathresults.txt');

	$lines = explode("\n", $results);

	$app['db']->executeQuery('TRUNCATE table results');

	$start = false;
	foreach ($lines as $i=>$l) {
		
		if (!$start) {

			if (!preg_match('/TRADE LOOPS/', $l)) {
				continue;
			}
			else {
				$start = true;
				continue;
			}
		}
		//Has started
		if ($start) {

			preg_match("/\s([0-9]+).*\s([0-9]+)/",$l,$matches);			
			if (count($matches)==3) {
				$app['db']->insert('results',array(
			    	'item_id'=>$matches[1],
			    	'item_rcvd'=>$matches[2],
			    ));
				
			}

			if (preg_match('/ITEM SUMMARY/',$l)) {
				break;
			}
		}
	}

	//Get to the point
	
	echo $results;
});


include('../controllers/rest.php');




/**
 * @param $userName
 * @return string
 */
function generateHash($userName)
{
	return md5(time() . $userName . time());
}


$app->post('/gethash/{userName}', function ($userName,Request $request) use ($app) {


	$sql = "SELECT distinct username FROM items WHERE username = ?";
	$user = $app['db']->fetchAll($sql,array($userName));
	$returnCode = returnCodeOK;
	if(!(0 === count($user))) {
		$hash= generateHash($userName);
		$app['db']->insert('users',array(
			'name'=>$userName,
			'hash'=>$hash

		));
	} else {
		$hash='fail';
		$errorCode = USER_NOT_FOUND;
	}

	return new Response(json_encode($hash),200,array('Content-Type'=>'application/json'));
});


//DEPRECATED
// $app->get('api/collection', function(Request $request) use ($app) {
// 	$post = array();
// 	$csv = new CsvIterator('mt.csv');
// 	foreach ($csv->parse() as $row) {
// 		$post[]=$row;
// 	}


// 	return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
// });

$app->get('/mt/get', function (Silex\Application $app) {
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
