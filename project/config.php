<?php
declare(strict_types=1);
session_start();
spl_autoload_register(function (string $file) {
    $extension = ".php";
    if (strpos($file, 'Database\\') !== false) {
        include 'system/' . $file . $extension;
    } else {
        include $file . $extension;
    }
});
define('ob', 'no', false);
define('debug_mode', 'no', false); //IF == YES DEBUG MOD WORKING
define('timezone', 'Europe/Sofia');


date_default_timezone_set(timezone);
if (strpos($_SERVER['REQUEST_URI'], 'admin') !== false) {
    include '../../vendor/autoload.php';
} else {
    include '../vendor/autoload.php';
}

use Database\PDODatabase;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('Empiregame');
$logger->pushHandler(new StreamHandler('logfile.log', Logger::WARNING));

mb_internal_encoding('UTF-8');
require_once 'setup/database.php';
/**
 * @var $database PDODatabase
 */
$database = new PDODatabase(new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]));
$database->exec('set names utf8');
mysqli_set_charset($dbc, 'utf8');
function deletebuilding($dbc)
{
    $time = time();
    $sql = "SELECT * FROM `building_now` WHERE `end_time` <'" . $time . "'";
    $finish_building = mysqli_query($dbc, $sql);
    $building_count = mysqli_num_rows($finish_building);
    if ($building_count >= 1) {
        while ($build = mysqli_fetch_assoc($finish_building)) {
            $get_build_id = "SELECT * FROM building_now WHERE user_id='" . $_SESSION['user']['user_id'] . "'";

            $get_build_id = mysqli_query($dbc, $get_build_id);
            $build_ = mysqli_fetch_assoc($get_build_id);
            $building_id = $build['building_name'];
            $userid = $build['user_id'];
            $select_build_sql = "SELECT * FROM building WHERE building_id ='" . $building_id . "'";
            $select_build_q = mysqli_query($dbc, $select_build_sql);
            $select_build_a = mysqli_fetch_assoc($select_build_q);
            $givexp = $select_build_a['give_xp'];
            mysqli_query($dbc, "UPDATE users SET xp  = xp + '" . $givexp . "' WHERE user_id='" . $userid . "'");
            $countub = mysqli_query($dbc, "SELECT * FROM users_building WHERE user_id='" . $_SESSION['user']['user_id'] . "' AND  building_id='" . $build_['building_name'] . "'");
            if (mysqli_num_rows($countub) == 0) {
                $select_build_lv = "INSERT INTO users_building (user_id,building_id,build_lv) VALUES ('" . $_SESSION['user']['user_id'] . "','" . $build_['building_name'] . "','1')";

                mysqli_query($dbc, $select_build_lv);
            } else {
                $lv = mysqli_query($dbc, "SELECT build_lv FROM users_building WHERE user_id='" . $_SESSION['user']['user_id'] . "' AND  building_id='" . $build_['building_name'] . "'");
                $lva = mysqli_fetch_assoc($lv);
                $lva['build_lv'];
                $sql_update_u_l = "UPDATE users_building SET build_lv  = build_lv +'1' WHERE user_id='" . $userid . "'";

                mysqli_query($dbc, $sql_update_u_l);
            }
        }
        $sql_delete = "DELETE FROM building_now WHERE `end_time` < '" . $time . "' and `user_id` = " . $_SESSION['user']['user_id'];

        mysqli_query($dbc, $sql_delete);
    }

}

function deletearmy($dbc)
{

    $now_unix = time();
    $sql = "SELECT * FROM army_now WHERE end_time <'" . $now_unix . "' AND user_id = '" . $_SESSION['user']['user_id'] . "'";
    $finished_army = mysqli_query($dbc, $sql);
    $finishedArmyCount = mysqli_num_rows($finished_army);
    if ($finishedArmyCount >= 1) {
        while ($army_ = mysqli_fetch_assoc($finished_army)) {
            $get_army_id = "SELECT * FROM army_now WHERE user_id='" . $_SESSION['user']['user_id'] . "' AND end_time < '" . $now_unix . "'";

            $getarmy = mysqli_query($dbc, $get_army_id);
            while ($_army = mysqli_fetch_assoc($getarmy)) {
                $army_name = $_army['army_name'];
                $count = $_army['army_num'];
                $userid = $_army['user_id'];

                $select_army_sql = "SELECT * FROM army WHERE army_id ='" . $army_name . "'";
                $select_army_q = mysqli_query($dbc, $select_army_sql);
                $select_army_a = mysqli_fetch_assoc($select_army_q);

                //give xp
                $givexp = $select_army_a['give_xp'];
                $give_xp_to_user = "UPDATE users SET xp  = xp +'" . $givexp . "' WHERE user_id=" . $userid;
                mysqli_query($dbc, $give_xp_to_user);

                //Check if user has already that army
                $sql_army_of_user = "SELECT * FROM `user_army` WHERE `user_id` = '" . $_SESSION['user']['user_id'] . "' AND `army_name` = '" . $_army['army_name'] . "'";
                $army_of_user = mysqli_query($dbc, $sql_army_of_user);
                if (mysqli_num_rows($army_of_user) == 0) {
                    $insert_army = "INSERT INTO user_army (user_id,army_name,count,lvl) VALUES ('" . $_SESSION['user']['user_id'] . "','" . $_army['army_name'] . "','" . $count . "', 1)";
                    mysqli_query($dbc, $insert_army);
                } else {
                    $insert_army = "UPDATE `user_army` SET `count` = `count` + " . $count . " WHERE `army_name` = " . $_army['army_name'] . " AND `user_id` = " . $_SESSION['user']['user_id'];
                    echo $insert_army;
                    mysqli_query($dbc, $insert_army);
                }
            }
            $sql_delete = "DELETE FROM army_now WHERE `end_time` < " . $now_unix . " and `user_id` = " . $_SESSION['user']['user_id'];
            mysqli_query($dbc, $sql_delete);
        }
    }
}

function upgrade_army($dbc)
{
    $now = time();
    $upgrade_army = mysqli_query($dbc, "SELECT * FROM upgrade_army WHERE  `end` < " . $now);
    if (mysqli_num_rows($upgrade_army) >= 1) {
        while ($army = mysqli_fetch_assoc($upgrade_army)) {

            $army_name = $army['army_id'];
            $user_army = mysqli_query($dbc, 'SELECT * FROM user_army WHERE army_name = ' . $army_name . ' and user_id = ' . $_SESSION['user']['user_id']);
            while ($army_ = mysqli_fetch_assoc($user_army)) {
                $level = $army['level'];
                $army_id = $army['army_id'];
                mysqli_query($dbc, "UPDATE `user_army` SET lvl = $level WHERE army_id = $army_id AND user_id = " . $_SESSION['user']['user_id']);
                mysqli_query($dbc, "DELETE FROM upgrade_army WHERE `army_id` = " . $army_id);
            }
        }
    }
}

function userdata(int $id, string $param, $dbc)
{
    $query = mysqli_query($dbc, "SELECT * FROM users WHERE user_id='$id'");
    return mysqli_fetch_assoc($query)[$param];

}

/**
 * @param int $time
 * @param string $url
 */
function refresh($time, $url = "")
{
    echo "<meta http-equiv=\"refresh\" content=\"" . $time . ";url=" . $url . "\">";
}

function addMoney(int $id, int $money, $dbc)
{
    mysqli_query($dbc, "UPDATE `users` SET `money`= `money` +  " . $money . " WHERE  `user_id`=" . $id);
}

function setMoney(int $id, int $money, $dbc)
{
    mysqli_query($dbc, "UPDATE `users` SET `money` = " . $money . " WHERE  `user_id`=" . $id);
}
