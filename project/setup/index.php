<?php
if(isset($_POST['database_host']) && isset($_POST['username']) && isset($_POST['password'])
&& isset($_POST['database_name'])){

    $database_host = trim($_POST['database_host']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $database_name = trim($_POST['database_name']);

}
?>
<!doctype html>
<html>
    <head>
        <title>Setup</title>
    </head>
    <body>
        <div id="container">
            <h1>Setup</h1>
            <h2>Database:</h2>
            <form action="index.php" method="post">
                <label>Database host:</label>
                <input name="database_host" placeholder="127.0.0.1"/>
                <br />
                <label>Username:</label>
                <input name="username" placeholder="root"/>
                <br />
                <label>Password:</label>
                <input name="password" placeholder="password"/>
                <br />
                <label>Database name:</label>
                <input name="database_name" placeholder="game"/>

                <button type="submit">Next</button>
            </form>
        </div>
    </body>
</html>
