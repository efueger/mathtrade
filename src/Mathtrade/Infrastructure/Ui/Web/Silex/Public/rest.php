<?php

use Symfony\Component\HttpFoundation\Response;

/**
 * Controller that handles all the restfull interface
 * @var [type]
 */
$rest = $app['controllers_factory'];


/**
 * Rest to obtain items of a user of a certain type
 * Gets the interested items and the excluded
 */
$rest->get('/itemstype/{type}/', function ($type) use ($app) {
    $user = $app['session']->get('user');

    $sql = "SELECT * FROM user_items ui
            INNER JOIN newitems i ON ui.item_id = i.id AND user_id = ?
            WHERE type = ?";

    $post = $app['db']->fetchAll($sql, array($user['id'], $type == 'interested' ? 1 : 2));
    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


/**
 * Rest to obtain pending items of the current mathtrade
 * Gets all the items that the current user has not tagged as interested or excluded
 */
$rest->get('/pendingitems/', function () use ($app) {
    $user = $app['session']->get('user');

    $sql = "SELECT i.* FROM items_mt imt
			LEFT JOIN newitems i ON imt.item_id = i.id
			LEFT JOIN user_items ui ON i.id = ui.item_id AND ui.user_id = ?
			WHERE ui.id IS NULL";
    $result = $app['db']->fetchAll($sql, array($user['id']));

    return new Response(json_encode($result), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


/**
 * Gets the items that the user added to the MT
 */
$rest->get('/itemsbyuser/', function () use ($app) {
    $user = $app['session']->get('user');

    $sql = "SELECT i.* FROM items_mt imt
			LEFT JOIN newitems i ON imt.item_id = i.id where i.account_id = ?";
    $post = $app['db']->fetchAll($sql, array($user['id']));

    //Fetch want lists
    $ids = array();
    foreach ($post as $i) {
        $ids[] = $i['id'];
    }


    $sql = "SELECT w.*,i.name,wl.name as wlname
            FROM wantlist w
            LEFT JOIN newitems i ON type =1 AND w.target_id = i.id
            LEFT JOIN wildcard wl ON type=2 AND w.target_id = wl.id
            WHERE w.item_id IN (?) and w.user_id = ? ORDER BY pos ASC";
    $want = $app['db']->fetchAll(
        $sql,
        array($ids, $user['id']),
        array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
    );
    //echo $sql;

	foreach ($post as $key => &$p) {
		$p['username'] = $user['username'];
		foreach ($want as $j => $w) {
			if ($w['item_id'] == $p['id']) {
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


    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


$rest->get('/useritems/', function () use ($app) {
    $user = $app['session']->get('user');

    $sql = "SELECT i.* FROM user_items ui
            INNER JOIN newitems i ON ui.item_id = i.id
            WHERE user_id = ? and type =1";
    $post = $app['db']->fetchAll($sql, array((int)$user['id']));

    //Now exclude the items of the wilcards
    $sql = "SELECT wi.item_id FROM wildcarditems wi
            INNER JOIN wildcard w on wi.wildcard_id = w.id
            WHERE w.user_id = ?";
    $excl = $app['db']->fetchAll($sql, array((int)$user['id']));
    $exclude = array();
    foreach ($excl as $a) {
        $exclude[] = $a['item_id'];
    }

    foreach ($post as $k => $i) {
        if (in_array($i['id'], $exclude)) {
            unset($post[$k]);
        }
    }
    $post = array_values($post);


    return new Response(json_encode($post), RETURN_CODE_OK, array('Content-Type' => 'application/json'));
});


return $rest;
