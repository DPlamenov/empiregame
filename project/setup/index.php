<?php
if (file_exists('database.php')) {
    header("Location: ../index.php");
}

if (isset($_POST['database_host']) && isset($_POST['username']) && isset($_POST['password'])
    && isset($_POST['database_name'])) {

    $database_host = trim($_POST['database_host']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $database_name = trim($_POST['database_name']);

    if ($database_host == 'localhost') {
        $database_host = '127.0.0.1';
    }

    $dbc = mysqli_connect($database_host, $username, $password, $database_name);
    $data = '<?php' . ' ' . "mb_internal_encoding('UTF-8');" . '$dbc = mysqli_connect("' . $database_host . '","' . $username . '","' . $password . '","' . $database_name . '");';
    $data .= '$dsn = "mysql:host=' . $database_host . ';dbname=' . $database_name . '";' . '$db_username = "' . $username . '"; $db_password = "' . $password . '";';
    file_put_contents("database.php", $data);

    if ($dbc) {
        $create_sql = file_get_contents('sql.sql');
        $create_sql = explode(';', $create_sql);
        foreach ($create_sql as $sql) {
            mysqli_query($dbc, $sql);
        }
        header("Location: ../index.php");
    } else {
        echo 'error';
    }
}

?>
<!doctype html>
<html lang="bg">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Setup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Setup Database</h1>

    <form action="index.php" method="post">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Host</span>
            </div>
            <input type="text" name="database_host" class="form-control" placeholder="Database host" aria-label="Database host" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Username</span>
            </div>
            <input type="text" name="username"   class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Password</span>
            </div>
            <input type="password" name="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Database name</span>
            </div>
            <input type="text" name="database_name" class="form-control" placeholder="Database name" aria-label="Database name" aria-describedby="basic-addon1">
        </div>
        <button type="submit" class="btn btn-primary">Next</button>
    </form>
</div>
</body>
</html>
