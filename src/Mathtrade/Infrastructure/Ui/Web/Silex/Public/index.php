<?php



const RETURN_CODE_OK = 200;
const USER_NOT_FOUND = 520;
const SALT = '9ywmLatNHWuJJMH7k7LX';

use Edysanchez\Mathtrade\Application\Service\BoardGameGeekImport\BoardGameGeekImportRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../../../../../../../vendor/autoload.php';

define('CONTROLLERS', __DIR__ . '/');

$app =  \Edysanchez\Mathtrade\Infrastructure\Ui\Web\Silex\Application::bootstrap();

function getUser($hash)
{
    global $app;
    //Get the user
    $sql = "SELECT * FROM users WHERE hash = ?";
    $user = $app['db']->fetchAll($sql, array($hash));
    $user = $user[0];
    return $user;
}

function getWantUser($user)
{
    global $app;
    $sql = "SELECT * FROM items where username = ?";
    $post = $app['db']->fetchAll($sql, array($user));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
        $ids[] = $i['id'];
    }
    $sql = "SELECT w.*,i.name".
        " FROM wantlist w INNER JOIN items i ON type =1 AND w.target_id = i.item_id".
        " WHERE w.item_id IN (?) ORDER BY pos ASC";

    $want = $app['db']->fetchAll(
        $sql,
        array($ids),
        array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
    );

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
        return $app->redirect('/signin');
    }
    return $user;
}

// ... definitions


$app->get('/landing', function (Silex\Application $app) {
    $items = file_get_contents('mtitems.data');
    $its = json_decode(str_replace("\\\"", '"', $items));

    return $app['twig']->render('index.twig', array(
        'items' => $items,
        'useritems' => array(),
        'wants' => array(),
        'wildcards' => array(),
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
    if (count($r) > 0) {
        $sql = "SELECT * FROM accounts WHERE username = ?";
        $user = $app['db']->fetchAll($sql, array($r['user']));
        if (count($user) > 0) {
            $user = $user[0];

            //User logged!!
            if ($user['password'] === md5($r['pwd'] . SALT)) {
                unset($user['password']);
                $app['session']->set('user', $user);
                return $app->redirect('/home');
            }
        }
        return $app->redirect('/signin?error=1');
    }


    return $app['twig']->render('signin.twig');
})->method('GET|POST');


//The user registers in the MTH
$app->post('/register', function (Silex\Application $app) {
    //Get post params..
    $r = $app['request']->request->all();
    if (count($r) > 0) {
        $sql = "SELECT * FROM accounts WHERE username = ? OR email = ?";
        $user = $app['db']->fetchAll($sql, array($r['user'], $r['email']));
        if (count($user) > 0) {
            return $app->redirect('/signin?error=2');
        }

        //Not used go ahead
        $app['db']->insert('accounts', array(
            'username' => $r['user'],
            'password' => md5($r['pwd'] . SALT),
            'email' => $r['email'],
            'created' => date('Y-m-d H:i:s')
        ));
        return $app->redirect('/signin?success=1');
    }
    return $app->redirect('/signin?error=3');
});
$app->get('/logout', function (Silex\Application $app) {
    $app['session']->clear();
    return $app->redirect('/');
});

/**
 * Home of the app
 */
$app->get('/home', function (Silex\Application $app) {
    $user = logged();
    if (!is_array($user)) {
        return $user;
    }

    $games = $app['db']->fetchAll(
        'SELECT i.*, !isnull(im.id) as inMT '.
        'FROM newitems i LEFT JOIN items_mt im ON i.id = im.item_id WHERE account_id = ?',
        array($user['id'])
    );
    return $app['twig']->render('home.twig', array(
        'user' => $user,
        'games' => str_replace('"', '\\"', json_encode($games, JSON_HEX_APOS)),
    ));
});

/**
 * Home of the app
 */
$app->get('/bggimport', function (Silex\Application $app) {
    $user = logged();
    if (!is_array($user)) {
        return $user;
    }

    return $app['twig']->render('bggimport.twig', array(
        'user' => $user,
    ));
});

$app->get('/bggimport/get', function (Silex\Application $app) {

     $user = logged();

    if (!is_array($user)) {
        return $user;
    }

    $boardGameGeekImportRequest = new BoardGameGeekImportRequest($user['bgg_user']);
    $useCase = $app['board_game_geek_import'];
    $response = $useCase->execute($boardGameGeekImportRequest);


    return new Response(json_encode($response->games()), 200, array('Content-Type' => 'application/json'));
});


//Allows adding games 
$app->post('/bggimport/add', function (Silex\Application $app) {
    $user = logged();
    if (!is_array($user)) {
        return $user;
    }

    $games = $app['request']->request->get('data');

    $games = json_decode($games, true);

    foreach ($games as $g) {
        $already = $app['db']->fetchAll(
            'SELECT id FROM newitems WHERE account_id = ? AND collid = ?',
            array($user['id'], $g['collid'])
        );

        if (count($already) > 0) {
            continue;
        }

        $app['db']->insert('newitems', array(
            'account_id' => $user['id'],
            'name' => $g['name'],
            'description' => $g['description'],
            'bgg_id' => $g['bgg_id'],
            'bgg_img' => $g['bgg_img'],
            'collid' => $g['collid'],

        ));
    }


    return new Response(json_encode($games), 200, array('Content-Type' => 'application/json'));
});

/**
 * Home of the app
 */
$app->get('/mathtrade', function (Silex\Application $app) {
    $user = logged();
    if (!is_array($user)) {
        return $user;
    }

    $sql = "SELECT i.*,a.username FROM items_mt mt
			LEFT JOIN newitems i ON mt.item_id = i.id 
			LEFT JOIN accounts a ON i.account_id= a.id";
    $games = $app['db']->fetchAll($sql);

    return $app['twig']->render('mathtrade.twig', array(
        'user' => $user,
        'games' => $games
    ));
});


/**
 * This is the base url of a user identified by it's hash
 */
$app->get('/{hash}', function ($hash) use ($app) {

    $wants = array();

    //Get the user
    $user = getUser($hash);

    //Items selected by user either
    $sql = "SELECT i.*,ui.type FROM user_items ui
			INNER JOIN items i ON ui.item_id = i.item_id
			WHERE ui.user_id = ?";
    $items = $app['db']->fetchAll($sql, array($user['id']));

    //Items on the pending list
    $sql = "SELECT i.* FROM items i
			LEFT JOIN user_items ui ON i.item_id = ui.item_id AND ui.user_id = ?
			WHERE ui.id IS NULL";
    $pending = $app['db']->fetchAll($sql, array($user['id']));

    $wants = getWantUser($user['id']);


    $sql = "SELECT w.id as wid,w.name as wildname,i.* FROM wildcard w
			LEFT JOIN wildcarditems wi ON w.id = wi.wildcard_id
			LEFT JOIN items i ON wi.item_id = i.item_id
			WHERE w.user_id = ? ORDER by pos ASC";
    $dirty = $app['db']->fetchAll($sql, array($user['id']));

    $wildcards = array();
    foreach ($dirty as $w) {
        if (!isset($wildcards[$w['wid']])) {
            $wildcards[$w['wid']] = array(
                'id' => $w['wid'],
                'name' => $w['wildname'],
                'wantid' => '%' . $w['wildname'],
                'items' => array()
            );
        }
        if (isset($w['item_id'])) {
            $wildcards[$w['wid']]['items'][] = array(
                'id' => $w['item_id'],
                'item_id' => $w['item_id'],
                'name' => $w['name'],
            );
        }
    }
    $wildcards = array_values($wildcards);

    //Last Call
    //Items that users wanted to trade for our games that didn't switch
    $sql = "SELECT item_id FROM results;";
    //$traded


    return $app['twig']->render('index.twig', array(
        'items' => str_replace('"', '\\"', json_encode($pending, JSON_HEX_APOS)),
        'useritems' => str_replace('"', '\\"', json_encode($items, JSON_HEX_APOS)),
        'wants' => str_replace('"', '\\"', json_encode($wants, JSON_HEX_APOS)),
        'wildcards' => str_replace('"', '\\"', json_encode($wildcards, JSON_HEX_APOS)),
        'hash' => $hash
    ));
});

/**
 * Import the results from the file
 */
$app->get('/import/results', function (Silex\Application $app) {
    $results = file_get_contents('mathresults.txt');

    $lines = explode("\n", $results);

    $app['db']->executeQuery('TRUNCATE table results');

    $start = false;
    foreach ($lines as $i => $l) {
        if (!$start) {
            if (!preg_match('/TRADE LOOPS/', $l)) {
                continue;
            } else {
                $start = true;
                continue;
            }
        }
        //Has started
        if ($start) {
            preg_match("/\s([0-9]+).*\s([0-9]+)/", $l, $matches);
            if (count($matches) == 3) {
                $app['db']->insert('results', array(
                    'item_id' => $matches[1],
                    'item_rcvd' => $matches[2],
                ));
            }

            if (preg_match('/ITEM SUMMARY/', $l)) {
                break;
            }
        }
    }

    //Get to the point

    echo $results;
});

//Returns all the items
$app->get('/rest/items', function (Silex\Application $app) {
    $sql = "SELECT * FROM items";
    $post = $app['db']->fetchAll($sql);
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


//Returns all the items
$app->post('/rest/addtomt', function (Request $request) use ($app) {

    $d = array(
        'item_id' => $request->get('id'),
        'mt_id' => 1,
    );

    $sql = "SELECT * FROM items_mt where item_id = ? AND mt_id = ?";
    $post = $app['db']->fetchAll($sql, array_values($d));

    if ($post) {
        $app['db']->delete('items_mt', $d);
    } else {
        $post = $app['db']->insert('items_mt', $d);
    }
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


$app->get('/rest/itemstype/{type}/{hash}', function ($type, $hash) use ($app) {
    $user = getUser($hash);
    $sql = "SELECT * FROM user_items ui
            INNER JOIN items i ON ui.item_id = i.id AND user_id = ?
            WHERE type = ?";

    $post = $app['db']->fetchAll($sql, array($user['id'], $type == 'interested' ? 1 : 2));
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});

//Delegate the rest urls to the rest controller
$app->mount('/rest', include CONTROLLERS . 'rest.php');


$app->get('/rest/items/{id}/{hash}', function ($id, $hash) use ($app) {
    $sql = "SELECT * FROM items WHERE id = ?";
    $post = $app['db']->fetchAll($sql, array($id));

    $user = getUser($hash);

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
        $ids[] = $i['id'];
    }
    $sql = "SELECT w.*,i.name,wl.name as wlname
            FROM wantlist w
            LEFT JOIN items i ON type =1 AND w.target_id = i.item_id
            LEFT JOIN wildcard wl ON type=2 AND w.target_id = wl.id
            WHERE w.item_id IN (?) AND w.user_id = ? ORDER BY pos ASC";
        $want = $app['db']->fetchAll(
            $sql,
            array($ids, $user['id']),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        foreach ($post as $key => &$p) {
            foreach ($want as $j => $w) {
                if ($w['item_id'] == $p['item_id']) {
                    $w['id'] = $w['type'] == 2 ? 'w' . $w['target_id'] : $w['target_id'];
                    $w['wantid'] = $w['id'];
                    if ($w['type'] == 2) {
                        $w['name'] = $w['wlname'];
                        $w['wantid'] = "%" . $w['wlname'];
                    }
                    $p['wantlist'][] = $w;
                    unset($want[$j]);
                }
            }
        }

        return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


$app->get('/rest/results/{hash}', function ($hash) use ($app) {
    $user = getUser($hash);

    $sql = "SELECT * FROM items where username = ?";
    $post = $app['db']->fetchAll($sql, array($user['name']));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
        $ids[] = $i['id'];
    }


    $sql = "SELECT i2.item_id,i2.name,i2.bgg_img,i2.username,r.item_id as myitem
    		FROM results r
			LEFT JOIN items i2  ON  r.item_rcvd = i2.id
    		WHERE r.item_id IN (?)";
    $results = $app['db']->fetchAll(
        $sql,
        array($ids),
        array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
    );

    foreach ($post as $key => &$p) {
        foreach ($results as $j => $w) {
            if ($w['myitem'] == $p['item_id']) {
                $p['received'] = $w;
                unset($results[$j]);
            }
        }
    }


    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});



/**
 * Implements the REST to add an item to our collection
 */
$app->post('/rest/items/', function (Request $request) use ($app) {
    $r = $request->request->all();

    $r['bgg_img'] = $r['full_img'];
    unset($r['full_img']);
    unset($r['img']);
    unset($r['id']);
    unset($r['wantname']);

    $user = $app['session']->get('user');
    //print_r($user);
    $r['account_id'] = $user['id'];
    //print_r($r);

    $post = $app['db']->insert('newitems', $r);

    $sql = "SELECT * FROM newitems WHERE id = ? ";
    $post = $app['db']->fetchAll($sql, array($app['db']->lastInsertId()));

    //print_r($request->all());
    return new Response(json_encode($post[0]), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


/**
 * Allows the user to exclude or want an item from the MT
 */
$app->post('/rest/useritems/', function (Request $request) use ($app) {
    $user = $app['session']->get('user');

    if ($request->get('bulk')) {
        $bulk = json_decode($request->get('bulk'));

        $app['db']->executeQuery(
            'DELETE FROM user_items WHERE item_id IN (?) AND user_id = ' . $user['id'] . ' ',
            array($bulk),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );


        $sql = "INSERT INTO user_items (user_id,item_id,type) VALUES ";
        $first = true;
        foreach ($bulk as $id) {
            if ($first) {
                $first = false;
            } else {
                $sql .= ',';
            }
            $sql .= '(' . $user['id'] . ',' . $id . ',2)';
        }

        $app['db']->executeQuery($sql);
    } else {
        //Delete item if its in a list
        $app['db']->delete('user_items', array(
            'user_id' => $user['id'],
            'item_id' => $request->get('id'),
            'type' => $request->get('type') == 1 ? 2 : 1
        ));

        $post = $app['db']->insert('user_items', array(
            'user_id' => $user['id'],
            'item_id' => $request->get('id'),
            'type' => $request->get('type')

        ));
    }
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


$app->post('/rest/wildcards/{hash}', function ($hash, Request $request) use ($app) {
    $user = getUser($hash);
    $post = $app['db']->insert('wildcard', array(
        'user_id' => $user['id'],
        'name' => $request->get('name')
    ));
    $w = $app['db']->lastInsertId();
    return new Response(
        json_encode(
            array(
                'id' => $w,
                'wantid' => '%' . $request->get('name'))
        ),
        200,
        array('Content-Type' => 'application/json')
    );
});

$app->delete('/rest/wildcards/{hash}', function ($hash, Request $request) use ($app) {
    $user = getUser($hash);
    $post = $app['db']->delete('wildcard', array(
        'user_id' => $user['id'],
        'id' => $request->get('id')
    ));
    return new Response(
        json_encode($post),
        200,
        array('Content-Type' => 'application/json')
    );
});


$app->get('/rest/userwantlist/{user}', function ($user) use ($app) {
    $sql = "SELECT * FROM wantlist WHERE user_id = ?";
    $post = $app['db']->fetchAll($sql, array($user));
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


$app->post('/rest/wantlist/{hash}', function ($hash, Request $request) use ($app) {
    $user = getUser($hash);

    $d = json_decode($request->get('d'));
    $wantid = $request->get('wid');
    if (is_numeric($wantid)) {
        $app['db']->delete('wantlist', array('item_id' => $wantid));
    }

    //Now prepare to insert the items / wildcards
    foreach ($d as $pos => $i) {
        $app['db']->insert('wantlist', array(
            'item_id' => $wantid,
            'user_id' => $user['id'],
            'target_id' => str_replace('w', '', $i->id),
            'type' => $i->t,
            'pos' => $pos
        ));
    }

    print_r($d);
    return new Response(
        json_encode($d),
        RETURN_CODE_OK,
        array('Content-Type' => 'application/json')
    );
});

$app->post('/rest/wildcarditems/{hash}', function ($hash, Request $request) use ($app) {
    $user = getUser($hash);
    $d = json_decode($request->get('d'));
    $wildid = $request->get('wid');

    //Remove old wild
    if (is_numeric($wildid)) {
        $app['db']->delete('wildcarditems', array('wildcard_id' => $wildid));
    }
    //Now prepare to insert the items
    foreach ($d as $pos => $i) {
        $app['db']->insert('wildcarditems', array(
            'item_id' => $i->item_id,
            'wildcard_id' => $wildid,
            'pos' => $i->pos
        ));
    }

    return new Response(
        json_encode($d),
        200,
        array('Content-Type' => 'application/json')
    );
});


/**
 * @param $userName
 * @return string
 */
function generateHash($userName)
{
    return md5(time() . $userName . time());
}


$app->post('/gethash/{userName}', function ($userName, Request $request) use ($app) {


    $sql = "SELECT distinct username FROM items WHERE username = ?";
    $user = $app['db']->fetchAll($sql, array($userName));
    $returnCode = RETURN_CODE_OK;
    if (!(0 === count($user))) {
        $hash = generateHash($userName);
        $app['db']->insert('users', array(
            'name' => $userName,
            'hash' => $hash

        ));
    } else {
        $hash = 'fail';
        $errorCode = USER_NOT_FOUND;
    }

    return new Response(json_encode($hash), 200, array('Content-Type' => 'application/json'));
});


$app->get('/mt/get', function (Silex\Application $app) {
    $items = array();
    do {
        $url = isset($pages[1]) ? $pages[1] : 'http://labsk.net/index.php?topic=151319.0';
        file_put_contents('test.html', file_get_contents($url));
        $html = file_get_contents('test.html');
        $string = preg_replace('/\n/', '', $html);

        preg_match('/"forumposts">(.*)<a id="lastPost/', $string, $match);


        //ini_set('display_errors',0);
        // die();
        preg_match_all('/post_wrapper">(.*?)class="botslice"/', $match[1], $posts);

        if (!isset($pages)) {
            unset($posts[1][0]);
        }

        //Get pagination
        preg_match(
            '/<a class="navPages" href="([^"]*?)">>><\/a>/',
            $string,
            $pages
        );

        foreach (array_slice($posts[1], 0) as $post) {
            $dom = new DOMDocument();
            ini_set('display_errors', 0);
            $dom->loadHTML('<div>' . $post . '></span>');
            ini_set('display_errors', 1);
            $xpath = new DomXpath($dom);
            $innerpost = $xpath->query('//*[@class="inner"]');


            foreach ($innerpost as $el) {
                $nodes = $el->childNodes;
                foreach ($nodes as $node) {
                    if ($node->nodeName != 'table') {
                        continue;
                    }

                    //Skip tablebody
                    $gamelist = $node->childNodes;


                    //Check if it's a group
                    if ($gamelist->item(0)->childNodes->item(0)->childNodes->item(0)->nodeName == 'strong') {
                        foreach ($gamelist as $id => $game) {
                            if ($id % 2 == 0) {
                                $Group = array();
                                foreach ($game->childNodes->item(0)->childNodes as $i => $grgame) {
                                    if ($i < 2) {
                                        continue;
                                    }
                                    if ($grgame->nodeName == 'a') {
                                        if (strpos($grgame->nodeValue, '[') === false) {
                                            $GI = new stdClass();
                                            $GI->name = $grgame->nodeValue;
                                            $Group[] = $GI;
                                        }
                                    } else {
                                        $Group[count($Group) - 1]->description = $grgame->nodeValue;
                                    }
                                }
                            }
                            if ($id % 2 == 1) {
                                foreach ($game->childNodes->item(0)->childNodes as $i => $grgame) {
                                    if ($grgame->nodeName == 'a') {
                                        $Group[$i]->bgg_url = $grgame->getAttribute('href');
                                        $Group[$i]->bgg_img = $grgame->childNodes->item(0)->getAttribute('src');
                                    }
                                }
                                $items[] = $Group;
                            }
                        }
                    } else {
                        foreach ($gamelist as $game) {
                            $G = new stdClass();

                            if (!$game->childNodes->item(0)->childNodes->item(0)) {
                                continue;
                            }
                            if ($game->childNodes->item(0)->childNodes->item(0) instanceof DOMText) {
                                continue;
                            }
                            $G->bgg_url = $game->childNodes->item(0)->childNodes->item(0)->getAttribute('href');
                            if ($game->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)
                                instanceof DOMText
                            ) {
                                continue;
                            }
                            if ($game->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)) {
                                $G->bgg_img =
                                    $game->childNodes->item(0)
                                        ->childNodes->item(0)
                                        ->childNodes->item(0)
                                        ->getAttribute('src');
                            }
                            //	else continue;

                            //Second Columm table

                            //if ( count($game->childNodes) <2) continue;
                            //if (!$game->childNodes->item(1))continue;
                            if (count($game->childNodes->item(1)->childNodes) > 0) {
                                $col2 = $game->childNodes->item(1)->childNodes->item(0);
                                $G->name = $col2->childNodes->item(0)->nodeValue;
                                $G->description = '';
                                foreach ($col2->childNodes as $i => $row) {
                                    if ($i == 0) {
                                        continue;
                                    }
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
    } while (!empty($pages[1]));

    echo "<pre>";
    print_r($items);
    echo "</pre>";
    file_put_contents(
        'mtitems.data',
        str_replace('"', '\\"', json_encode($items, JSON_HEX_APOS))
    );

    return;


});


$app->post('/', function (Silex\Application $app) {

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // 5 minutes execution time
    @set_time_limit(5 * 60);
    // Uncomment this one to fake upload time
    // usleep(5000);
    // Settings
    //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
    $targetDir = __DIR__ . '/uploads';
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
            die(
                '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}'
            );
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
    return ('{"jsonrpc" : "2.0", "file" :"' . $fileName . '", "id" : "id"}');
});

$app->run();
