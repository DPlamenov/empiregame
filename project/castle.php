<?php
session_start();
include 'config.php';
echo '<script src="js/javascript.js" type="text/javascript"></script>';
echo '<div>';
//echo '<p>Вашите пари: ' . userdata($_SESSION['user']['user_id'], 'money',$dbc) . 'лева</p>';
echo '<p style="margin-left: 40%">Castle</p>';
echo '<button type="button" class="btn btn-danger" style="margin-left: auto; margin-right: auto"><a href="?find_opponent=1">Find opponent</a></button>';
?>
<br>
<span style="display: block">Attack specific user</span>
<input type="text" name="user" id="specific_user"/>
<button type="button" class="btn btn-danger" id="attack_button">Attack</button>
</div>
<?php
$your_battle = mysqli_query($dbc, "SELECT * FROM battle WHERE attacker = ".$_SESSION['user']['user_id']." or defender = " . $_SESSION['user']['user_id']);
if(mysqli_num_rows($your_battle) > 0){
    echo '<p>Your last battle</p>';
    echo '<table>';
    echo '<tr><th>Attacker</th><th>Defender</th><th>Result</th><th>Info</th></tr>';
    while($battle = mysqli_fetch_assoc($your_battle)){

    }

    echo '</table>';
}
?>

