<?php
//Returns all the items
$app->get('/rest/items', function (Silex\Application $app) {
    $sql = "SELECT * FROM items";
    $post = $app['db']->fetchAll($sql);
    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});


$app->get('/rest/itemstype/{type}/{hash}', function ($type,$hash)  use($app){
    $user = getUser($hash);
    $sql = "SELECT * FROM user_items ui
            INNER JOIN items i ON ui.item_id = i.id AND user_id = ? 
            WHERE type = ?";

    $post = $app['db']->fetchAll($sql,array($user['id'],$type=='interested'?1:2));
    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});




$app->get('/rest/items/{id}/{hash}', function ($id,$hash)  use($app){
    $sql = "SELECT * FROM items WHERE id = ?";
    $post = $app['db']->fetchAll($sql,array($id));

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
    $want = $app['db']->fetchAll($sql,
    array($ids,$user['id']),
    array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
    //echo $sql;

    foreach ($post as $key => &$p) {
        foreach ($want as $j => $w) {
            if ($w['item_id'] == $p['item_id']) {
                $w['id'] = $w['type']==2?'w'.$w['target_id']:$w['target_id'];
                $w['wantid'] = $w['id'];
                if ($w['type']==2) {
                    $w['name'] = $w['wlname'];
                    $w['wantid'] = "%".$w['wlname'];
                }
                $p['wantlist'][] = $w;
                unset($want[$j]);
            }
        }
    }
    
    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});

$app->get('/rest/itemsbyuser/{hash}', function ($hash) use($app) {
    $user = getUser($hash);

    $sql = "SELECT * FROM items where username = ?";
    $post = $app['db']->fetchAll($sql,array($user['name']));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
        $ids[] = $i['id'];
    }

    
    $sql = "SELECT w.*,i.name,wl.name as wlname 
            FROM wantlist w 
            LEFT JOIN items i ON type =1 AND w.target_id = i.item_id 
            LEFT JOIN wildcard wl ON type=2 AND w.target_id = wl.id
            WHERE w.item_id IN (?) and w.user_id = ? ORDER BY pos ASC";
    $want = $app['db']->fetchAll($sql,
    array($ids,$user['id']),
    array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
    //echo $sql;

     foreach ($post as $key => &$p) {
        foreach ($want as $j => $w) {
            if ($w['item_id'] == $p['item_id']) {
                $w['id'] = $w['type']==2?'w'.$w['target_id']:$w['target_id'];
                $w['wantid'] = $w['id'];
                if ($w['type']==2) {
                    $w['name'] = $w['wlname'];
                    $w['wantid'] = "%".$w['wlname'];
                }
                $p['wantlist'][] = $w;
                unset($want[$j]);
            }
        }
    }


    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});


$app->get('/rest/results/{hash}', function ($hash) use($app) {
	$user = getUser($hash);

	$sql = "SELECT * FROM items where username = ?";
    $post = $app['db']->fetchAll($sql,array($user['name']));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
    	$ids[] = $i['id'];
    }

    
    $sql = "SELECT i2.item_id,i2.name,i2.bgg_img,i2.username,r.item_id as myitem  
    		FROM results r 
			LEFT JOIN items i2  ON  r.item_rcvd = i2.id
    		WHERE r.item_id IN (?)";
    $results = $app['db']->fetchAll($sql,
    array($ids),
    array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
    //echo $sql;

     foreach ($post as $key => &$p) {
    	foreach ($results as $j => $w) {
    		if ($w['myitem'] == $p['item_id']) {
    			$p['received'] = $w;
    			unset($results[$j]);
    		}
    	}
    }


    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});


$app->get('/rest/useritems/{hash}', function ($hash) use ($app) {
    $user = getUser($hash);

    $sql = "SELECT i.* FROM user_items ui 
            INNER JOIN items i ON ui.item_id = i.id 
            WHERE user_id = ? and type =1";
    $post = $app['db']->fetchAll($sql,array((int)$user['id']));

    //Now exclude the items of the wilcards
    $sql = "SELECT wi.item_id FROM wildcarditems wi 
            INNER JOIN wildcard w on wi.wildcard_id = w.id 
            WHERE w.user_id = ?";
    $excl = $app['db']->fetchAll($sql,array((int)$user['id']));
    $exclude = array();
    foreach ($excl as $a) {
        $exclude[] = $a['item_id'];
    }

    foreach ($post as $k => $i) {
        if (in_array($i['id'],$exclude) ) {
            unset($post[$k]);   
        }
    }
    $post = array_values($post);



    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});





$app->post('/rest/useritems/{hash}', function ($hash,Request $request) use ($app) {
    $user = getUser($hash);

    if ($request->get('bulk')) {
        $bulk = json_decode($request->get('bulk'));

        //$app['db']->executeQuery('SELECT FROM articles WHERE item_id IN (?) user_id = ? ',
        $app['db']->executeQuery('DELETE FROM user_items WHERE item_id IN (?) AND user_id = '.$user['id'].' ',
            array($bulk),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );


        $sql = "INSERT INTO user_items (user_id,item_id,type) VALUES ";
        $first = true;
        foreach ($bulk as $id) {
            if($first)$first=false;
            else $sql.=',';
            $sql.= '('.$user['id'].','.$id.',2)';
        }

        $app['db']->executeQuery($sql);
        
    }
    else {

        //Delete item if its in a list
        $app['db']->delete('user_items',array(
            'user_id'=>$user['id'],
            'item_id'=>$request->get('id'),
            'type'=> $request->get('type') == 1 ? 2: 1
        ));

        $post = $app['db']->insert('user_items',array(
            'user_id'=>$user['id'],
            'item_id'=>$request->get('id'),
            'type'=>$request->get('type')

        ));
    }
    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});


$app->post('/rest/wildcards/{hash}', function ($hash,Request $request) use ($app) {
    $user = getUser($hash);
    $post = $app['db']->insert('wildcard',array(
        'user_id'=>$user['id'],
        'name'=>$request->get('name')
        ));
        $w = $app['db']->lastInsertId(); 
    return new Response(json_encode(array('id'=>$w,'wantid'=>'%'.$request->get('name'))),200,array('Content-Type'=>'application/json'));
});

$app->delete('/rest/wildcards/{hash}', function ($hash,Request $request) use ($app) {
    $user = getUser($hash);
    $post = $app['db']->delete('wildcard',array(
        'user_id'=>$user['id'],
        'id'=>$request->get('id')
        ));
    return new Response(json_encode($post),200,array('Content-Type'=>'application/json'));
});


$app->get('/rest/userwantlist/{user}', function ($user)  use($app){
    $sql = "SELECT * FROM wantlist WHERE user_id = ?";
    $post = $app['db']->fetchAll($sql,array($user));
    return new Response(json_encode($post), returnCodeOK,array('Content-Type'=>'application/json'));
});


$app->post('/rest/wantlist/{hash}', function ($hash,Request $request) use ($app) {
    $user = getUser($hash);

    $d = json_decode($request->get('d'));
    $wantid = $request->get('wid');
    if (is_numeric($wantid)) {
        $app['db']->delete('wantlist',array('item_id'=>$wantid));
    }

    //Now prepare to insert the items / wildcards
    foreach ($d as $pos=>$i) {
        $app['db']->insert('wantlist',array(
            'item_id'=>$wantid,
            'user_id'=>$user['id'],
            'target_id'=>str_replace('w', '', $i->id),
            'type'=>$i->t,
            'pos'=>$pos
        ));
    }

    print_r($d);
    return new Response(json_encode($d), returnCodeOK,array('Content-Type'=>'application/json'));
});

$app->post('/rest/wildcarditems/{hash}', function ($hash,Request $request) use ($app) {
    $user = getUser($hash);
    $d = json_decode($request->get('d'));
    $wildid = $request->get('wid');

    //Remove old wild
    if (is_numeric($wildid)) {
        $app['db']->delete('wildcarditems',array('wildcard_id'=>$wildid));
    }
    //Now prepare to insert the items
    foreach ($d as $pos=>$i) {
        $app['db']->insert('wildcarditems',array(
            'item_id'=>$i->item_id,
            'wildcard_id'=>$wildid,
            'pos'=>$i->pos
        ));
    }

    return new Response(json_encode($d),200,array('Content-Type'=>'application/json'));
});


?>